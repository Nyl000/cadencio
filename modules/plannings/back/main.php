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
    use Cadencio\AbstractModule;

    class main extends AbstractModule
    {

        public function __construct()
        {
            $this->setName('plannings');

            HookHandler::getInstance()->setHook('register_rest_controller', function () {
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

            parent::__construct();
        }

        public function onActivate()
        {
            $version = $this->getDbVersion();

            if ($version < 1) {

                $this->getAdapter()->query('
                CREATE TABLE plannings (
                      id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
                      title VARCHAR(256),
                      PRIMARY KEY(id)
                    ) ENGINE=InnoDB;
                ', []);



                $this->getAdapter()->query('
                  INSERT INTO plannings VALUES (0,\'My first planning\');
                ', []);

                $this->getAdapter()->query('
                    CREATE TABLE planning_status (
                        id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
                        title VARCHAR(256),
                        color VARCHAR(30) NOT NULL DEFAULT \'#999999\',
                        closed TINYINT NOT NULL DEFAULT 0,
                        PRIMARY KEY(id)
                    ) ENGINE=InnoDB;
                ', []);

                $this->getAdapter()->query("INSERT INTO planning_status(id,title,color,closed) VALUES
                    (0,'Awaiting','#F2C511',0),
                    (0,'Planned','#3D556E',0),
                    (0,'Ready','#F39C19',0),
                    (0,'Ongoing','#3398DB',0),
                    (0,'Done','#2ECC70',1);
                ", []);

                $this->getAdapter()->query('CREATE TABLE planning_entry (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    id_creator INTEGER UNSIGNED NOT NULL,
                    title VARCHAR(256),
                    description TEXT,
                    id_status INTEGER UNSIGNED NOT NULL,
                    id_planning INTEGER UNSIGNED NOT NULL,
                    date_start DATETIME DEFAULT NULL,
                    date_end DATETIME DEFAULT NULL,
                    id_assigned_to INTEGER UNSIGNED DEFAULT NULL,
                    PRIMARY KEY(id),
                    FOREIGN KEY (id_status) REFERENCES planning_status(id) ON DELETE RESTRICT,
                    FOREIGN KEY (id_planning) REFERENCES plannings(id) ON DELETE CASCADE,
                    FOREIGN KEY (id_creator) REFERENCES users(id) ON DELETE RESTRICT,
                    FOREIGN KEY (id_assigned_to) REFERENCES users(id) ON DELETE SET NULL,
                    INDEX(date_start),
                    INDEX(date_end)
                ) ENGINE=InnoDB;', []);

                $this->getAdapter()->query('CREATE TABLE planning_entry_followers (
                    id_planning_entry BIGINT UNSIGNED NOT NULL,
                    id_user INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
                    PRIMARY KEY(id_planning_entry,id_user),
                    FOREIGN KEY (id_planning_entry) REFERENCES planning_entry(id) ON DELETE CASCADE,
                    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB;', []);


                $this->incrementDbVersion();


            }
        }

    }

    new main();
}



