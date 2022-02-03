<?php

namespace Cadencio\Models;

use Cadencio\Application;

class UserOptionModel extends AbstractModel
{

    protected $modelName = 'users_options';
    protected $resourceName = 'users';
    protected $userModelName;

    public function __construct()
    {
        $this->userModelName = 'Cadencio\Models\UserModel';
        parent::__construct();
    }

    public function getPublicFields()
    {
        return ['id','key','value'];
    }

    public function getSearchField() {
        return ['id','key'];
    }

    public function getByUser($id_user) {
        return $this->getAdapter()->fetchPairs('SELECT `key`,`value` FROM '.$this->modelName.' WHERE id_user = ?', [$id_user]);
    }

    public function getByUserAndName($id_user,$name) {
        return $this->getAdapter()->fetchOne('SELECT `value` FROM '.$this->modelName.' WHERE id_user = ? AND `key` = ? ', [$id_user,$name]);
    }

    public function setOption($name,$value,$id_user = false) {
        if (!$id_user) {
            $id_user = Application::$instance->getCurrentUserId();
        }
        $userModel =  new $this->userModelName();
        if( !$userModel->idExists($id_user)) {
            throw new \Exception('Unknown user with ID:'.$id_user);
        }
        $this->createOrUpdate([
            'id_user' => $id_user,
            'key' => $name,
            'value' => $value,
        ]);
    }

}