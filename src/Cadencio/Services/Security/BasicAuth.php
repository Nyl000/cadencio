<?php
namespace Cadencio\Services\Security;

use Cadencio\Models\UserModel;

class BasicAuth implements ProviderInterface {

    private $model;

    public function __construct() {
        $this->model = new UserModel();
    }
    public function getModel() {
        return $this->model;
    }

    public function test() {
        if (!isset ($_SERVER['PHP_AUTH_USER'])) {
            return false;
        }
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $userModel = $this->getModel();
        $userId = $userModel->login($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
        if($has_supplied_credentials && $userId) {
            return ['id' => $userId, 'model' => $this->getModel()];

        }
        return false;

    }

}