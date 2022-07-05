<?php
class Database {
    private $pdo;
    private $dsn;
    private $username;
    private $password;

    public function __construct($dsn=null, $username=null, $password=null) {
        if(!$dsn) trigger_error('Missing configuration', E_USER_ERROR);

        $this->dsn = $dsn;
    }

    public function __destruct() {
        $this->close();
    }

    private function connect() {
        if($this->pdo) {
            return $this->pdo;
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            return $this->pdo = new PDO($this->dsn, $this->username, $this->password, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function close() {
        if($this->pdo) {
            $this->pdo = null;
        }
    }

    public function count($table, $where, $data=array()) {
        if(!isset($table) || !isset($where)) trigger_error('Missing statement', E_USER_ERROR);

        $sql = sprintf('SELECT COUNT(*) AS total FROM %s %s', $table, $where);

        $pdo = $this->connect();

        try {
            $stmt = $pdo->prepare($sql);
            $row = $stmt->execute($data);
            $stmt->fetch();

            return $row['total'];
        } catch(\PDOException $e) {
            $pdo->rollback();
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function query($sql, $data=array()) {
        if(!isset($sql)) trigger_error('Missing statement', E_USER_ERROR);

        $pdo = $this->connect();

        try {
            $stmt = $pdo->prepare($sql);

            return $stmt->execute($data);
        } catch(PDOException $e) {
            $pdo->rollback();
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function selectSingle($sql, $data=array()) {
        if(!isset($sql)) trigger_error('Missing statement', E_USER_ERROR);

        $pdo = $this->connect();

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            //$stmt->debugDumpParams();

            return $stmt->fetch();
        } catch(PDOException $e) {
            $pdo->rollback();
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function select($sql, $data=array()) {
        if(!isset($sql)) trigger_error('Missing statement', E_USER_ERROR);

        $pdo = $this->connect();

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            //$stmt->debugDumpParams();

            return $stmt->fetchAll();
        } catch(PDOException $e) {
            $pdo->rollback();
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function update($table, $values, $where, $data=array()) {
        if(!isset($table) || !isset($values) || count($values) == 0) trigger_error('Missing statement', E_USER_ERROR);

        $val = '';
        foreach ($values as $key => $value) {
            $val .= $key . '=:' . $key . ', ';
        }
        $val = rtrim($val, ', ');
        $sql = sprintf('UPDATE %s SET %s %s', $table, $val, $where);

        $pdo = $this->connect();

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $affected = $stmt->execute(array_merge($values, $data));
            //$stmt->debugDumpParams();
            $pdo->commit();

            return $affected;

        } catch(PDOException $e) {
            $pdo->rollback();
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function delete($table, $where=null, $data=array()) {
        if(!isset($table)) trigger_error('Missing statement', E_USER_ERROR);

        $sql = sprintf('DELETE FROM %s %s', $table, $where);

        $pdo = $this->connect();

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $affected = $stmt->execute($data);
            //$stmt->debugDumpParams();
            $pdo->commit();

            return $affected;

        } catch(PDOException $e) {
            $pdo->rollback();
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function insert($table, $values) {
        if(!isset($table) || !isset($values) || count($values) == 0) trigger_error('Missing statement', E_USER_ERROR);

        $var = '';
        $val = '';
        foreach ($values as $key => $value) {
            $var .= $key . ', ';
            $val    .= ':' . $key . ', ';
        }
        $var = rtrim($var, ', ');
        $val = rtrim($val, ', ');
        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, $var, $val);

        $pdo = $this->connect();

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            //$stmt->debugDumpParams();
            $id = $pdo->lastInsertId();
            $pdo->commit();

            return $id;
        } catch(PDOException $e) {
            $pdo->rollback();
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function insertMultiple($table, $values) {
        if(!isset($table) || !isset($values) || count($values) == 0) trigger_error('Missing statement', E_USER_ERROR);

        $var = '';
        $val = '';
        foreach ($values[0] as $key => $value) {
            $var .= $key . ', ';
            $val    .= ':' . $key . ', ';
        }
        $var = rtrim($var, ', ');
        $val = rtrim($val, ', ');
        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, $var, $val);

        $pdo = $this->connect();

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            foreach($values as $row) {
                $stmt->execute($row);
            }
            //$stmt->debugDumpParams();
            $pdo->commit();

            return true;
        } catch(PDOException $e) {
            $pdo->rollback();
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}
