<?php

namespace Cadencio\Services\Security;

use Cadencio\Application;

class SecurityProvider {

    private $providers = [];

    public function init() {
        foreach($this->providers as &$provider) {
            $providersResults = $provider->test();
            if($providersResults) {
                Application::$instance->setCurrentUserId($providersResults);
                return;
            }
        }
    }

    public function addProvider(ProviderInterface $provider) {
        $this->providers[] = $provider;
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