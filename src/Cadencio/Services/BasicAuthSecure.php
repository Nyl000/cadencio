<?php
namespace Cadencio\Services;

use Cadencio\Application;
use Cadencio\Models\UserModel;

class BasicAuthSecure {

    public function init() {
        if (!isset ($_SERVER['PHP_AUTH_USER'])) {
            return;
        }
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $userModel = new UserModel();
        $userId = $userModel->login($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
        if($has_supplied_credentials && $userId) {
            Application::$instance->setCurrentUserId($userId);
        }

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