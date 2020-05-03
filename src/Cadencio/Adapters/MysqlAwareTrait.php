<?php namespace Cadencio\Adapters;

trait MysqlAwareTrait {

    protected static $adapter = false;

    protected static function getAdapterStatic() {
        if (!self::$adapter) {
            self::$adapter = new MysqlAdapter();
        }
        return self::$adapter;
    }
    protected function getAdapter() {
        if (!self::$adapter) {
            self::$adapter = new MysqlAdapter();
        }
        return self::$adapter;
    }
}