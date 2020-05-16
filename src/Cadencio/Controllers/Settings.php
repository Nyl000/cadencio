<?php namespace Cadencio\Controllers;

use Cadencio\Application;
use Cadencio\Models\SettingModel;
use Cadencio\Models\UserModel;
use Cadencio\Services\Utils;

class Settings extends RestController {

    public function init() {
        $this->setModel(new SettingModel());
    }

    public function getIndex($query) {
        return $this->auth->secure(function () use ($query) {

            $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'read');

            if ($query['action'] !== 'index') {
                return $this->getModel()->getOne($query['action'],'name');
            } else {
                return null;
            }

        });
    }

    public function getSendtestmail() {
        return $this->auth->secure(function ()  {

            $this->abortIfNotAllowed($this->getModel()->getResourceName(), '*');

            $userModel = new UserModel();
            $user = $userModel->getOne(Application::$instance->getCurrentUserId());

            try {
                Utils::sendMail($user['email'], 'Test email', 'This is a test <strong>email</strong>');
                return ['status' => 'ok','sent_to' => $user['email']];
            }
            catch(\Exception $e) {
                return ['status' => 'nok', 'message' => $e->getMessage()];
            }


        });
    }


}