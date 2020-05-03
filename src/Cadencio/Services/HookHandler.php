<?php


namespace Cadencio\Services;

class HookHandler
{


    private static $instance;
    private $hooks = [];

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new HookHandler();
        }

        return self::$instance;
    }

    public function setHook($name,$function) {
        if (!isset($this->hooks[$name])) {
            $this->hooks[$name] = [];
        }
        $this->hooks[$name][] = $function;
    }

    public function getHook($name) {
        if (!isset($this->hooks[$name])) {
            return [];
        }
        return $this->hooks[$name];
    }


}