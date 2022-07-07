<?php
class Database extends SQLite3 {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function run($query) {
        $this->open($this->db);
        $this->query($query);
        $this->close();
    }

    public function select($query) {
        $this->open($this->db);
        $result = $this->query($query);

        $array = array();
        while($row = $result->fetchArray(SQLITE3_ASSOC) ) {
            $array[] = $row;
        }

        $this->close();

        return $array;
    }

    public function selectSingle($query) {
        $this->open($this->db);
        $result = $this->query($query);

        $array = array();
        while($row = $result->fetchArray(SQLITE3_ASSOC) ) {
            $array[] = $row;
        }

        $this->close();

        if(count($array) == 1) {
            return $array[0];
        } else {
            return null;
        }
    }

    public function update($table, $values, $query) {
        $val = '';
        foreach ($values as $k => $v) {
            $val .= $k . '=\'' . SQLite3::escapeString($v) . '\', ';
        }
        $val = rtrim($val, ', ');

        $query = 'UPDATE ' . $table . ' SET ' . $val . $query;

        $this->open($this->db);
        $this->exec($query);
        $affected_rows = $this->changes();
        $this->close();

        return $affected_rows;
    }

    public function delete($query) {
        $this->open($this->db);
        $this->exec($query);
        $affected_rows = $this->changes();
        $this->close();

        return $affected_rows;
    }

    public function insert($table, $values) {
        $var = '';
        $val = '';
        foreach ($values as $k => $v) {
            $var .= $k . ', ';
            $val .= '\'' . SQLite3::escapeString($v) . '\', ';
        }
        $var = rtrim($var, ', ');
        $val = rtrim($val, ', ');

        $this->open($this->db);
        $query = 'INSERT INTO ' . $table . ' (' . $var . ') VALUES (' . $val . ')';
        $this->exec($query);
        $insert_row_id = $this->lastInsertRowID();
        $this->close();

        return $insert_row_id;
    }

    public function escape($var) {
        return SQLite3::escapeString($var);
    }
}
?>