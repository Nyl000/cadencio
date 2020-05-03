<?php namespace Cadencio\Controllers;

use Cadencio\Application;
use Cadencio\Exceptions\ApiUnprocessableException;
use Cadencio\Models\NotificationModel;
use Cadencio\Models\UserModel;
use Cadencio\Models\UserOptionModel;

class User extends RestController {

    public function init() {
        $this->setModel(new UserModel());
    }

    public function postLogin() {

        $body = $this->getRequest()->getJsonBody();

        if (!isset($body->email) ) {
            throw new ApiUnprocessableException('Missing email');
        }

        if (!isset($body->password) ) {
            throw new ApiUnprocessableException('Missing password');
        }

        if($this->getModel()->login($body->email,$body->password)) {
            return ['status' => 'ok'];
        }
        else {
            return ['status' => 'nok'];
        }
    }

    public function getChecktoken() {
        return $this->basicAuth->secure(function ()  {
            $user = $this->getModel()->getOne(Application::$instance->getCurrentUserId());

            return ['status' => 'ok','user' => $user];
        });
    }

    public function getMynotifications() {
        return $this->basicAuth->secure(function ()  {

            $model = new NotificationModel();

            return ['status' => 'ok','notifications' => $model->getMyUnseen()];
        });
    }

    public function getSelf_options() {
        return $this->basicAuth->secure(function ()  {

            $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'update_self');

            $model = new UserOptionModel();
            return $model->getByUser(Application::$instance->getCurrentUserId());
        });
    }

    public function postProcessEntity($candidate)
    {
        if (isset($candidate->password) && !empty($candidate->password)) {
            $candidate->password = hash('SHA256',$candidate->password);
        }
        else {
            unset($candidate->password);
        }
        return $candidate;
    }

    public function postSelfoptions() {
        return $this->basicAuth->secure(function ()  {

            $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'update_self');

            $body = $this->getRequest()->getJsonBody();

            if (!isset($body->key)) {
                throw new ApiUnprocessableException('Missing key');
            }
            if (!isset($body->value)) {
                throw new ApiUnprocessableException('Missing value');
            }
            $model = new UserOptionModel();
            return $model->createOrUpdate([
                'key' => $body->key,
                'value' => $body->value,
                'id_user' => Application::$instance->getCurrentUserId(),
            ]);

        });
    }

}