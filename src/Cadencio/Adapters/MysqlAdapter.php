<?php
namespace Cadencio\Adapters;

class MysqlAdapter {


    private static $instance = false;
    protected $pdo;

    public function __construct() {

        if (!self::$instance) {
            $this->pdo = new \PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWORD);
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$instance = $this;
        }
        return self::$instance;
    }

    public function setPdoAttribute($attr,$val) {
        $this->pdo->setAttribute($attr,$val);
    }

    public function beginTransaction() {
        $this->pdo->beginTransaction();
    }

    public function commit() {
        $this->pdo->commit();
    }

    public function rollBack() {
        $this->pdo->rollBack();
    }

    public function exec($query) {
        return self::$instance->pdo->exec($query);
    }
    public function query($query,$parameters) {
        $stm = self::$instance->pdo->prepare($query);
        return $stm->execute($parameters);
    }

    public function fetchRow($query,$params) {
        $stm=self::$instance->pdo->prepare($query);
        $stm->execute($params);
        return $stm->fetch();
    }

    public function fetchAll($query,$params) {
        $stm=self::$instance->pdo->prepare($query);
        $stm->execute($params);
        return $stm->fetchAll();
    }

    public function fetchColumn($query,$params) {
        $stm=self::$instance->pdo->prepare($query);
        $stm->execute($params);
        return $stm->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function fetchPairs($query,$params) {
        $rows = $this->fetchAll($query,$params);
        $results = array();
        foreach($rows  as $row) {
            $row = array_values($row);
            $results[$row[0]]= $row[1];
        }
        return $results;
    }

    public function fetchOne($query,$params) {
        $stm=self::$instance->pdo->prepare($query);
        $stm->execute($params);
        $r = $stm->fetch();
        if ($r) {
            $arraykeys = array_keys($r);
            return $r[$arraykeys[0]];
        }
        return null;
    }

    public function getLastId($name= null) {
        return self::$instance->pdo->lastInsertId($name);
    }


}