<?php

namespace Cadencio\Services\Security;

use Cadencio\Application;

class SecurityProvider {

    private $layers = [];

    public function init() {
        foreach($this->layers as &$layer) {
            $layerResult = $layer->test();
            if($layerResult) {
                Application::$instance->setCurrentUserId($layerResult);
                return;
            }
        }
    }

    public function addLayer($layer) {
        $this->layers[] = $layer;
    }

    public function secure($func) {
        if (!is_callable($func)) { throw new \RuntimeException('This is not callable');}

        header('Cache-Control: no-cache, must-revalidate, max-age=0');

        if( Application::$instance->getCurrentUserId()) {
            return $func();
        }
        else {
            header("HTTP/1.0 401 Unauthorized");
        }
    }

}