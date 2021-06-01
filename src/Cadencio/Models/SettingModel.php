<?php

namespace Cadencio\Models;

use Cadencio\Application;
use Cadencio\Services\ModulesManager;

class SettingModel extends AbstractModel
{

    protected $modelName = 'settings';
    protected $resourceName = 'settings';
    protected $identifier = 'name';


    public function getSearchField()
    {
        return ['name'];
    }

    public function patch($id, $datas, $uniqueFieldname = 'id') {
        if (isset($datas->active) && $datas->active == 1) {

            $current = $this->getOne($id,$uniqueFieldname);
            if($current['active'] == 0) {
                ModulesManager::getInstance()->registerModule($current['name']);
                $instance = Application::$instance->getModuleInstance($current['name']);
                $instance->onActivate();
            }

        }
        return parent::patch($id,$datas,'name');
    }

    public function idExists($id, $field = 'id')
    {
        return parent::idExists($id,'name');
    }

    public function getOne($id, $field = 'id',$ignoreCase = false) {
        return parent::getOne($id, 'name',$ignoreCase = false) ;

    }

    public function get($name) {
        return $this->getAdapter()->fetchOne('SELECT  val FROM '.$this->modelName.' WHERE `name` = ? ',[$name]);
    }

    public function getSettings(...$names) {
        return $this->getAdapter()->fetchPairs('SELECT `name`, val FROM '.$this->modelName.' WHERE `name` IN ('.implode(',',array_fill(0,count($names),'?')).')  ',$names);
    }




}