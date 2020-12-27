<?php
namespace Config;

use Cadencio\Services\HookHandler;

class Permissions {

    public static function getResources() {
        $resources = [
            '*' => [
              '*'
            ],
            'users' => [
                '*',
                'create',
                'read',
                'update',
                'update_self',
                'delete',
            ],
            'roles' => [
                '*',
                'create',
                'read',
                'update',
                'delete',
            ],
            'notifications' => [
                '*',
            ],
            'settings' => [
                '*'
            ],
            'logs' => [
                'read'
            ]
        ];

        $hookHandler = HookHandler::getInstance();

        $permissionsHooked = $hookHandler->getHook('register_permission');
        foreach($permissionsHooked as $permissionFunct) {
            $permission = $permissionFunct();
            $resources = array_merge($resources,$permission);
        }

        return $resources;
    }

}