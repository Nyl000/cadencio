<?php

//Inject module files here
namespace {
    require_once 'Controllers/Planning.php';
    require_once 'Controllers/Planning_entry.php';
    require_once 'Controllers/Planning_status.php';
    require_once 'Models/PlanningEntryModel.php';
    require_once 'Models/PlanningModel.php';
    require_once 'Models/PlanningStatusModel.php';
}

namespace Modules\Plannings {

    use Cadencio\Services\HookHandler;

    class main {
        public function __construct() {

            HookHandler::getInstance()->setHook('register_rest_controller', function() {
               return [
                   'planning' => 'Modules\Plannings\Controllers\Planning',
                   'planning_entry' => 'Modules\Plannings\Controllers\Planning_entry',
                   'planning_status' => 'Modules\Plannings\Controllers\Planning_status',
               ];
            });

            HookHandler::getInstance()->setHook('register_permission', function () {
                return [
                    'plannings' => [
                        '*',
                        'create',
                        'read',
                        'update',
                        'delete',
                    ],
                    'planning_status' => [
                        '*',
                        'create',
                        'read',
                        'update',
                        'delete',
                    ],
                    'planning_entry' => [
                        '*',
                        'create',
                        'read',
                        'update',
                        'update_mine',
                        'delete',
                    ],
                ];
            });
        }
    }
    new main();
}



