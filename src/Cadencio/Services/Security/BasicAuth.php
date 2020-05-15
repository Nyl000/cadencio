<?php
namespace Cadencio\Services\Security;

use Cadencio\Application;
use Cadencio\Models\UserModel;

class BasicAuth {

    public function test() {
        if (!isset ($_SERVER['PHP_AUTH_USER'])) {
            return false;
        }
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $userModel = new UserModel();
        $userId = $userModel->login($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
        if($has_supplied_credentials && $userId) {
            return $userId;
        }
        return false;

    }

}