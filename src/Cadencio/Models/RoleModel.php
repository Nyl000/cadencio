<?php

namespace Cadencio\Models;

use Cadencio\Application;
use Cadencio\Services\HookHandler;

class RoleModel extends AbstractModel
{

    protected $modelName = 'roles';
    protected $resourceName = 'roles';


    public function getFromUser($idUser)
    {
        $userModel = Application::$instance->getCurrentUserModel();
        $role = $this->getAdapter()->fetchRow('SELECT ' . $this->modelName . '.* FROM ' . $this->modelName . ' WHERE id = ? ', [$userModel->getRoleId($idUser)]);
        $role['permissions'] = $this->getPermissionsFromRole($role['id']);
        return $role;
    }



    public function getPermissionsFromRole($idRole)
    {
        $hookHandler = HookHandler::getInstance();
        $permissionsOverrides = $hookHandler->getHook('rolepermissions_override');
        foreach ($permissionsOverrides as $overrideFnct) {
            if (is_callable($overrideFnct)) {
                $overrideAttempt = $overrideFnct($idRole);
                if (is_array($overrideAttempt)) {
                    return $overrideAttempt;
                }
            }
        }

        $permissions = $this->getAdapter()->fetchAll('SELECT * FROM roles_resources WHERE id_role = ?', [$idRole]);
        $output = [];
        foreach ($permissions as $permission) {
            $output[] = $permission['resource_name'] . '.' . $permission['resource_right'];
        }
        return $output;
    }

    public function getPermissionsFromUser($idUser)
    {
        $role = $this->getFromUser($idUser);
        return $this->getPermissionsFromRole($role['id']);
    }

    public function getOne($id, $field = 'id', $ignoreCase = true)
    {

        $role = parent::getOne($id, $field, $ignoreCase);

        $role['permissions'] = $this->getPermissionsFromRole($role['id']);

        return $role;
    }

    public function addPermission($idRole, $resource, $right)
    {
        $this->getAdapter()->query('INSERT INTO roles_resources(id_role,resource_name,resource_right) VALUES(?,?,?)', [$idRole, $resource, $right]);
    }

    public function removePermission($idRole, $resource, $right)
    {
        $this->getAdapter()->query('DELETE FROM roles_resources WHERE id_role = ? AND resource_name = ? AND resource_right = ?', [$idRole, $resource, $right]);
    }

}