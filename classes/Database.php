<?php

class Database
{
    private static $_instance = null;
    private $pdo, $query, $result, $count = 0, $error = null;

    private function __construct() {

        try {
            $this->pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db') .';charset=utf8', Config::get('mysql/username'), Config::get('mysql/password'));
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function getInstance() {

        if(!isset(self::$_instance)) {
            self::$_instance = new Database();
        }

        return self::$_instance;
    }

    public function query($sql, $params = array()) {

        $this->error = false;

        if($this->query = $this->pdo->prepare($sql)) {
            $x = 1;

            if(count($params)) {
                foreach($params as $param) {
                    $this->query->bindValue($x, $param);
                    $x++;
                }
            }

            if($this->query->execute()) {
                $this->result = $this->query->fetchAll(PDO::FETCH_OBJ);
                $this->count = $this->query->rowCount();
            } else {
                $this->error = true;
            }
        }

        return $this;
    }

    public function action($action, $table, $where = array()) {

        if(count($where) === 3) {
            $operators = array('=', '>', '<', '>=', '<=', 'IN');

            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];

            if(in_array($operator, $operators)) {
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";

                if(!$this->query($sql, array($value))->error()) {
                    return $this;
                }
            }
        }

        return false;
    }

    public function insert($table, $fields = array()) {

        $keys = array_keys($fields);
        $values = null;
        $x = 1;

        foreach($fields as $field) {
            $values .= '?';
            if($x < count($fields)) {
                $values .= ', ';
            }
            $x++;
        }

        $sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ({$values})";

        if(!$this->query($sql, $fields)->error()) {
            return true;
        }

        return false;
    }

    public function update($table, $id, $fields) {

        $set = '';
        $x = 1;

        foreach($fields as $name => $value) {
            $set .= "{$name} = ?";
            if($x < count($fields)) {
                $set .= ', ';
            }
            $x++;
        }

        $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";

        if(!$this->query($sql, $fields)->error()) {
            return true;
        }

        return false;
    }

    public function delete($table, $where) {
        return $this->action('DELETE ', $table, $where);
    }

    public function get($table, $where) {
        return $this->action('SELECT *', $table, $where);
    }

    public function getAll($table) {
        return $this->query("SELECT * FROM " . $table);
    }

    public function results() {
        return $this->result;
    }

    public function first() {
        $data = $this->results();
        return $data[0];
    }

    public function count() {
        return $this->count;
    }

    public function error() {
        return $this->error;
    }

    public function getPDO() {
        return $this->pdo;
    }

}