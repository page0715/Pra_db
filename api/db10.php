<?php
date_default_timezone_set("Asia/Taipei");
session_start();
class DB{
    protected $dsn = "mysql:host=localhost;charset=utf8;dbname=db15";
    protected $pdo;
    protected $table;

    public function __construct($table){
        $this->table=$table;
        $this->pdo=new PDO($this->dsn,'root','');
    }

    function q($sql){
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function a2s($array){
        foreach ($array as $col=>$value){
            $tmp[] = "`$col`='$value'";
        }
        return $tmp;
    }

    private function sql_all($sql,$array,$other){
        if(isset($this->table) && !empty($this->table)){
            if(is_array($array)){
                if(!empty($array)){
                    $tmp = $this->a2s($array);
                    $sql .= " where " . join(" && ",$tmp);
                }
            } else {
                $sql .= " $array";
            }
            $sql .= $other;
            return $sql;
        }
    }

    function all( $where = '',$other = ''){
        $sql = "select * from `$this->table` ";
        $sql = $this->sql_all($sql,$where,$other);
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    function count( $where = '',$other = ''){
        $sql = "select count(*) from `$this->table` ";
        $sql = $this->sql_all($sql,$where,$other);
        return $this->pdo->query($sql)->fetchColumn();
    }    

    private function math($math,$col,$array='',$other=''){
        $sql = "select $math(`$col`) from `$this->table` ";
        $sql = $this->sql_all($sql,$array,$other);
        return $this->pdo->query($sql)->fetchColumn();
    }

    function sum($col,$where='',$other=''){
        return $this->math('sum',$col,$where,$other);
    }

    function max($col,$where='',$other=''){
        return $this->math('max',$col,$where,$other);
    }

    function min($col,$where='',$other=''){
        return $this->math('min',$col,$where,$other);
    }

    function find($id){
        $sql = "select * from `$this->table` ";
        if(is_array($id)){
            $tmp = $this->a2s($id);
            $sql .= " where " . join(" && ",$tmp);
        } else if(is_numeric($id)){
            $sql .= " where `id`='$id'";
        }
        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    function del($id){
        $sql = "delete from `$this->table` where ";
        if(is_array($id)){
            $tmp = $this->a2s($id);
            $sql .= join(" && ",$tmp);
        } else if(is_numeric($id)){
            $sql .= " `id`='$id'";
        }
        return $this->pdo->exec($sql);
    }
    
}

function dd($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

function to($url){
    header("location:$url");
}

$Total =new DB('total');
?>