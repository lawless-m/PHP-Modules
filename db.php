<?php

class PG {

    protected $conn;
    protected array $statements;
    
    public function __construct(string $host, string $port, string $db, string $un, string $pw) {
        $this->conn = pg_connect("host=$host port=$port dbname=$db user=$un password=$pw");
        $this->statements = [];
    }
    
    protected function query(string $sql) {
        $res = pg_query($this->conn, $sql);
        if($res) {
            return [true, pg_fetch_all($res)];
        }
        return [false, []];
    }

    protected function execute(string $stmt, array $params) {
        $conversions = [];
        $res = pg_execute($this->conn, $stmt, $params);
        if($res) {
            for($f = 0; $f < pg_num_fields($res); $f++) {
                if(pg_field_type($res, $f) == 'numeric') {
                    $conversions[] = function($v) { return floatval($v);};
                } else {
                    $conversions[] = function($v) { return $v;};
                }
            }
            $data = pg_fetch_all($res);
            if($data) {
                $keys = array_keys($data[0]);
                for($i = 0; $i < count($data); $i++) {
                    for($c = 0; $c < count($conversions); $c++) {
                        $data[$i][$keys[$c]] = $conversions[$c]($data[$i][$keys[$c]]);
                    }
                }
                return [true, $data];
            }
            return [true, []];
        }
        return [false, []];
    }

    protected function build_where(array $keys, string $join) {
        $wheres = [];
        for($i = 1; $i <= count($keys); $i++) {
            $wheres[] = pg_escape_identifier($keys[$i-1]) . ' = $' . "$i";
        }

        if(count($wheres) > 0) { 
            return ' WHERE (' . implode(") $join (", $wheres) . ')';
        }
        return '';
    }

    protected function prepare($sql) {
        $hash = md5($sql);
        if(array_key_exists($hash, $this->statements)) {
            return $hash;
        }
        if(pg_prepare($this->conn, $hash, $sql)) {
            $this->statements[$hash] = $sql;
            return $hash;
        }
    }

    protected function row_to_tree(&$tree, &$row, $levels) {
        
        if(!array_key_exists($row[$levels[0]], $tree)) {
            $tree[$row[$levels[0]]] = [];
        }
        $leaf = &$tree[$row[$levels[0]]];
        
        for($l = 1; $l < count($levels)-1; $l++) {
            if(!array_key_exists($row[$levels[$l]], $leaf)) {
                $leaf[$row[$levels[$l]]] = [];
            }
            $leaf = &$leaf[$row[$levels[$l]]];
        }
        
        $leaf[$row[$levels[count($levels)-1]]] = $row;

    }
    
    protected function execute_to_tree(string $stmt, array $params, array $levels) {
        $res = $this->execute($stmt, $params);
        if($res[0]) {
            $tree = [];
            foreach($res[1] as &$row) {
                $this->row_to_tree($tree, $row, $levels);
            }
            return [true, $tree];
        }
        return $res;
    }

}

?>