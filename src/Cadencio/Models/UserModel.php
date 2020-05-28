<?php

namespace Cadencio\Models;

use Cadencio\Application;

class UserModel extends AbstractModel
{

    protected $modelName = 'users';
    protected $resourceName = 'users';

    public function init()
    {
        if (!$this->isAdministrator(Application::$instance->getCurrentUserId())) {
            $this->where('id_role != 1', []);
        }
    }

    public function isAdministrator($idUser) {

        return
            $this->getAdapter()->fetchOne(
                'SELECT COUNT(*) 
                        FROM roles_resources 
                        JOIN users ON users.id_role = roles_resources.id_role
                        WHERE users.id = ? AND resource_name = ? AND resource_right = ?', [$idUser,'*','*']
            ) > 0;

    }

    public function getPublicFields()
    {
        return ['id','email','id_role','name','firstname','nickname'];
    }

    public function getSearchField() {
        return ['id','email'];
    }

    public function login($email,$password) {
        $userId = $this->getAdapter()->fetchOne('SELECT id FROM '.$this->modelName. ' WHERE email = ? AND password = ?', [$email, hash('sha256',$password)]);
        return $userId;
    }

    public function getHashedPassword($userId) {
        return $this->getAdapter()->fetchOne('SELECT password FROM '.$this->modelName.' WHERE id = ?', [$userId]);
    }

    public function createOrUpdate($datas)
    {
        if (is_object($datas)) {
            $datas = (array) $datas;
        }

        if (!isset($datas['id']) || empty($datas['id'])) {
            unset($datas['id']);
            $datas['date_register'] = date('Y-m-d H:i:s');
            $datas['hash'] = hash('SHA256', uniqid());
        }
        return parent::createOrUpdate($datas);
    }

    public function getOne($id, $field = 'id',$ignoreCase = true) {
        $roleModel = new RoleModel();
        $optionsModel = new UserOptionModel();

        $user = parent::getOne($id,$field,$ignoreCase);

        $user['role'] = $roleModel->getOne($user['id_role']);
        $user['options'] = $optionsModel->getByUser(($user['id']));

        unset($user['id_role']);

        return $user;
    }

}