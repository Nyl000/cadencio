<?php
namespace Cadencio\Controllers;

use Cadencio\Adapters\MysqlAwareTrait;
use Cadencio\Models\AbstractModel;
use Cadencio\Request;


abstract class AbstractController {

    use MysqlAwareTrait;

    private $model;
    private $resource;

    private static $request = false;

    public function __construct() {

        if (!self::$request) {
            self::$request = new Request();
        }
        $this->init();
    }



    protected function getRequest() {
        return self::$request;
    }

    public function render($datas) {
        echo $datas;
    }

    /**
     * @return AbstractModel
     * @throws \Exception
     */
    protected function getModel() {
        if (!isset($this->model)) {
            throw new \Exception('No model defined');
        }
        return $this->model;
    }

    protected function getResource() {
        if (!isset($this->resource)) {
            throw new \Exception('No resource defined');
        }
        return $this->resource;
    }

    protected function setModel(AbstractModel $m) {
        $this->model = $m;
        return $this;
    }

    protected function setResource($r) {
        $this->resource = $r;
        return $this;
    }

    protected  function init() {}

}