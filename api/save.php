<?php

function save($array){
    if(isset($array['id'])){
        $sql = "update `$this->table` set";

        if(!empty($array)){
            $tmp = $this->a2s($array);
        }

        $sql .= join(",", $tmp);
        $sql .= " where `id`='{$array['id']}'";
    } else {
        $sql = "insert into `$this->table` ";
        
    }
    return $this->pdo->exec($sql);
}

?>