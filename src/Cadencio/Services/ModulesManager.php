<?php

namespace Cadencio\Services;

use Cadencio\Adapters\MysqlAwareTrait;
use Cadencio\Application;
use Cadencio\Models\ModuleModel;

class ModulesManager
{

    use MysqlAwareTrait;

    private static $instance = false;
    private $modules = [];


    public static function getInstance()
    {

        if (!self::$instance) {
            self::$instance = new ModulesManager();
        }

        return self::$instance;
    }


    public function registerModule($name)
    {

        $pathFile = BASE_DIR . '/../modules/' . $name;
        $pathFileMain = $pathFile . '/back/main.php';
        require_once($pathFileMain);

        Application::$instance->registerModules();

    }

    protected function scanElligiblesModules()
    {

        $modules = [];
        $files = scandir(BASE_DIR . '/../modules/');
        foreach ($files as $file) {
            $pathFile = BASE_DIR . '/../modules/' . $file;
            if ($file !== '.' && $file !== '..' && is_dir($pathFile)) {
                $pathFileMain = $pathFile . '/back/main.php';
                if (file_exists($pathFileMain)) {
                    $modules[] = $file;
                    $moduleModel = new ModuleModel();
                    $moduleModel->createOrUpdate(['name' => $file]);
                }
            }
        }
        return $modules;

    }

    public function activateModule($name)
    {
        $pathFile = BASE_DIR . '/../modules/' . $name;
        if (is_dir($pathFile)) {
            $pathFileMain = $pathFile . '/back/main.php';
            if (file_exists($pathFileMain)) {
            }

        }
    }


    public function getActivesModules()
    {

        if (!defined('SKIP_MODULES') || SKIP_MODULES != 1) {
            $elligiblesModules = $this->scanElligiblesModules();
            $modulesInDb = $this->getAdapter()->fetchAll('SELECT * FROM modules', []);
            $existingModules = [];
            $activeModules = [];
            foreach ($modulesInDb as $module) {
                if (in_array($module['name'], $elligiblesModules)) {
                    $existingModules[] = $module['name'];
                    if ($module['active']) {
                        $activeModules[] = $module['name'];
                    }
                } else {
                    $this->getAdapter()->query('UPDATE  modules SET active = 0 WHERE name = ?', [$module['name']]);
                }
            }
        }
        else {
            $activeModules = [];
        }
        return $activeModules;
    }

}