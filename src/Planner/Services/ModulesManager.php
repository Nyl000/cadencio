<?php

namespace Planner\Services;

use Planner\Adapters\MysqlAwareTrait;

class ModulesManager {

    use MysqlAwareTrait;

    private static $instance = false;
    private $modules = [];

    private function __construct() {
    }

    public static function getInstance() {

        if(!self::$instance) {
            self::$instance = new ModulesManager();
        }

        return self::$instance;
    }


    protected function scanElligiblesModules() {

        $modules = [];
        $files = scandir(BASE_DIR .'/../modules/');
        foreach($files as $file) {
            $pathFile = BASE_DIR .'/../modules/'.$file;
            if($file!== '.' && $file !== '..' && is_dir($pathFile)) {
                $pathFileMain = $pathFile.'/back/main.php';
                if (file_exists($pathFileMain)) {
                    $modules[] = $file;
                    $this->getAdapter()->query('INSERT IGNORE INTO modules(name,active) VALUES(?,?)', [$file, 0]);
                }
            }
        }
        return $modules;

    }


    public function getActivesModules() {

        $elligiblesModules =  $this->scanElligiblesModules();
        $modulesInDb = $this->getAdapter()->fetchAll('SELECT * FROM modules', []);
        $existingModules = [];
        $activeModules = [];
        foreach ($modulesInDb as $module) {
            error_log(print_r($elligiblesModules,true));
            if(in_array($module['name'], $elligiblesModules)) {
                $existingModules[] = $module['name'];
                if ($module['active']) {
                    $activeModules[] = $module['name'];
                }
            }
            else {
                $this->getAdapter()->query('DELETE FROM modules WHERE name = ?', [$module['name']]);
            }
        }
        return $activeModules;
    }

}