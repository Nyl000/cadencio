<?php

namespace Cadencio\Models;

use Cadencio\Application;
use Cadencio\Exceptions\ApiUnprocessableException;

class UserModel extends AbstractModel
{

    protected $modelName = 'users';
    protected $resourceName = 'users';
    protected $optionModel;

    public function init()
    {
        $this->optionModel = new UserOptionModel();

    }


    public function writeUserLog($type,$message,$idUser= false)
    {
        $idUser = $idUser ?: Application::$instance->getCurrentUserId();
        $logModel = new LogModel();
        $date = date('Y-m-d H:i:s');
        try {
            $logModel->createOrUpdate([
                'date' => $date,
                'message' => $message,
                'type' => $type,
                'id_user' => $idUser
            ]);
        }
        catch (\Exception $e) {

        }
    }


    public function getPublicFields()
    {
        return ['id', 'email', 'id_role', 'name', 'firstname', 'nickname','active','users.phone'];
    }

    public function getSearchField()
    {
        return ['id', 'email'];
    }

    public function getFilters()
    {
        return [
            'id_role' => 'id_role'
        ];
    }

    public function login($email, $password)
    {
        $userId = $this->getAdapter()->fetchOne('SELECT id FROM ' . $this->modelName . ' WHERE active = 1 AND email = ? AND password = ?', [$email, hash('sha256', $password)]);        return $userId;
    }

    public function getHashedPassword($userId)
    {
        return $this->getAdapter()->fetchOne('SELECT password FROM ' . $this->modelName . ' WHERE id = ?', [$userId]);
    }

    public function isActive($userId) {
        return $this->getAdapter()->fetchOne('SELECT active FROM ' . $this->modelName . ' WHERE id = ? ',[$userId]);
    }

    public function createOrUpdate($datas)
    {
        if (is_object($datas)) {
            $datas = (array)$datas;
        }
        if(isset($datas['active'])) {
            $datas['active'] = $datas['active'] ? 1 : 0;
        }

        if (!isset($datas['id']) || empty($datas['id'])) {
            unset($datas['id']);
            $datas['date_register'] = date('Y-m-d H:i:s');
            $datas['hash'] = hash('SHA256', uniqid());
            $testUserExists = $this->getOne($datas['email'],'email');
            if (isset($testUserExists['id']) && !empty($testUserExists['id'])) {
                throw new ApiUnprocessableException('A user with the same email already exists.');
            }
        }

        $id = parent::createOrUpdate($datas);
        if (!isset($datas['id'])) {
            $settingsModel = new SettingModel();
            //create default user options
            $modelOptions = $this->optionModel;
            $modelOptions->createOrUpdate([
                'id_user' => $id,
                'key' => 'timezone',
                'value' => $settingsModel->get('default_timezone'),
            ]);
            $modelOptions->createOrUpdate([
                'id_user' => $id,
                'key' => 'lang',
                'value' => $settingsModel->get('default_language'),
            ]);
        }
        return $id;
    }

    public function patch($id, $datas, $uniqueFieldname = 'id')
    {
        if (is_object($datas)) {
            $datas = (array)$datas;
        }

        if(isset($datas['active'])) {
            $datas['active'] = $datas['active'] ? 1 : 0;
        }

        if (isset($datas['id_role'])) {
            $roleModel = new RoleModel();

        }
        if (isset($datas['lang'])) {
            $modelOptions = $this->optionModel;
            $modelOptions->createOrUpdate([
                'id_user' => $id,
                'key' => 'lang',
                'value' =>$datas['lang']
            ]);
        }
        parent::patch($id, $datas, $uniqueFieldname);
    }

    public function delete($id)
    {

        return parent::delete($id);
    }


    public function getOneWithoutAdminCheck($id,$field,$ignoreCase = true) {
        $roleModel = new RoleModel();
        $optionsModel = $this->optionModel;

        $user = parent::getOne($id, $field, $ignoreCase);

        $user['role'] = $roleModel->getOne($user['id_role']);
        $user['options'] = $optionsModel->getByUser(($user['id']));

        unset($user['id_role']);

        return $user;

    }

    public function getOne($id, $field = 'id', $ignoreCase = true)
    {

        $roleModel = new RoleModel();
        $optionsModel = $this->optionModel;

        $user = parent::getOne($id, $field, $ignoreCase);

        $user['role'] = $roleModel->getOne($this->getRoleId($user['id']));
        $user['options'] = $optionsModel->getByUser(($user['id']));



        unset($user['id_role']);

        return $user;
    }

    public function getRoleId($idUser) {
        return $this->getAdapter()->fetchOne('SELECT id_role FROM users WHERE users.id = ? ', [$idUser]);
    }


}