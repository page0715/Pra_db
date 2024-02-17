<?php
date_default_timezone_set("Asia/Taipei");
session_start();
class DB
{
    protected $dsn = "mysql:host=localhost;charset=utf8;dbname=db08";
    protected $pdo;
    protected $table;
    public function __construct($table)
    {
        $this->table = $table;
        $this->pdo = new PDO($this->dsn, 'root', '');
    }
    private function a2s($array)
    {
        foreach ($array as $col => $value) {
            $tmp[] = "`$col`='$value'";
        }
        return $tmp;
    }
    function save($array)
    {
        if (isset($array['id'])) {
            if (!empty($array)) {
                $sql = "update `$this->table` set ";
                $tmp = $this->a2s($array);
                $sql .= join(",", $tmp);
                $sql .= " where `id` = '{$array['id']}'";
            } else {
                echo "空的";
            }
        } else {
            $sql = "insert into `$this->table` ";
            $cols = "(`" . join("`,`", array_keys($array)) . "`)";
            $vals = "('" . join("','", $array) . "')";
            $sql .= $cols . "values" . $vals;
        }
        return $this->pdo->exec($sql);
    }
    function del($id)
    {
        $sql = "delete from `$this->table` where ";
        if (is_array($id)) {
            $tmp = $this->a2s($id);
            $sql .= join(" && ", $tmp);
        } elseif (is_numeric($id)) {
            $sql .= "`id`='$id'";
        } else {
            echo "錯誤:參數資料型態必須是數字或陣列";
        }
        return $this->pdo->exec($sql);
    }
    function find($id)
    {
        $sql = "select * from `$this->table` where ";
        if (is_array($id)) {
            $tmp = $this->a2s($id);
            $sql .= join(" && ", $tmp);
        } elseif (is_numeric($id)) {
            $sql .= "`id`='$id'";
        } else {
            echo "錯誤:參數的資料型態必須是數字或陣列";
        }
        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
    private function sql_all($sql, $array, $other)
    {
        if (isset($this->table) && !empty($this->table)) {
            if (is_array($array)) {
                if (!empty($array)) {
                    $tmp = $this->a2s($array);
                    $sql .= " where " . join(" && ", $tmp);
                }
            } else {
                $sql .= " $array ";
            }
            return $sql .= $other;
        } else {
            echo "錯誤:沒有指定的資料表名稱";
        }
    }
    function q($sql)
    {
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    function all($where = '', $other = '')
    {
        $sql = "select * from `$this->table` ";
        $sql = $this->sql_all($sql, $where, $other);
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    function count($where = '', $other = '')
    {
        $sql = "select count(*) from `$this->table` ";
        $sql = $this->sql_all($sql, $where, $other);
        return $this->pdo->query($sql)->fetchColumn();
    }
    private function math($math, $col, $array = '', $other = '')
    {
        $sql = "select $math(`$col`) from `$this->table` ";
        $sql = $this->sql_all($sql, $array, $other);
        return $this->pdo->query($sql)->fetchColumn();
    }
    function sum($col = '', $where = '', $other = '')
    {
        return $this->math('sum', $col, $where, $other);
    }
    function max($col = '', $where = '', $other = '')
    {
        return $this->math('max', $col, $where, $other);
    }
    function min($col = '', $where = '', $other = '')
    {
        return $this->math('min', $col, $where, $other);
    }
}
function dd($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}
function to($url)
{
    header("location:$url");
}
$Title = new DB('titles');
$Total = new DB('total');
$Bottom = new DB('bottom');
$Image = new DB('image');
$News = new DB('news');
$Mvim = new DB('mvim');
$Menu = new DB('menu');
$Ad = new DB('ad');
$Admin = new DB('admin');
//這段大寫要放後面放$Ttite前面會先找不到錯誤訊息
if (isset($_GET['do'])) {
    if (isset(${ucfirst($_GET['do'])})) {
        $DB = ${ucfirst($_GET['do'])};
    }
} else {
    $DB = $Title;
}
//total進站人數增加session
if (!isset($_SESSION['visited'])) {
    $Total->q("update `total` set `total` = `total`+1 where `id`=1");
    $_SESSION['visited'] = 1;
}
?>
<!-- 檔案 -->
<?php
$do = $_GET['do'] ?? 'main'; //title
$file = "./front/{$do}.php"; //back
if (file_exists($file)) {
    include $file;
} else {
    include "./front/main.php";
}
?>
<!-- 分頁 -->
<?php
$total = $DB->count();
$div = 3; //5
$pages = ceil($total / $div);
$now = $_GET['p'] ?? 1;
$start = ($now - 1) * $div;
$rows = $DB->all(" limit $start,$div");
foreach ($rows as $row) {
?>
<?php
}
?>
<?php
if ($now > 1) {
    $prev = $now - 1;
    echo "<a href='?do=$do&p=$prev'><</a>";
}
for ($i = 1; $i <= $pages; $i++) {
    $fontsize = ($now == $i) ? '24px' : '16px';
    echo "<a href='?do=$do&p=$i'style='font-size:$fontsize'>$i</a>";
}
if ($now < $pages) {
    $next = $now + 1;
    echo "<a href='?do=$do&p=$next'>></a>";
}
?>
<!-- 02 -->
<?php
$Total = new DB('total');
$News = new DB('news');
$User = new DB('user');
$Que = new DB('que');
$Log = new DB('log');
?>