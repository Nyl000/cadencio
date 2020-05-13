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

    public function setHook($name, $function)
    {
        if (is_callable($function)) {

            if (!isset($this->hooks[$name])) {
                $this->hooks[$name] = [];
            }
            $this->hooks[$name][] = $function;

        }
        else {
            trigger_error('Seems you attempt to hook a non-callable variable on hook ['.$name.']',E_USER_WARNING);
        }
    }

    public function getHook($name)
    {
        if (!isset($this->hooks[$name])) {
            return [];
        }
        return $this->hooks[$name];
    }


}