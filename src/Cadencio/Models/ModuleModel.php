<?php

namespace Cadencio\Models;

use Cadencio\Application;
use Cadencio\Services\ModulesManager;

class ModuleModel extends AbstractModel
{

    protected $modelName = 'modules';
    protected $resourceName = 'modules';
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
        else {
            //Necessary to avoid "false" returned to DB.. if direct "false" value, query fails, did't investigated why yet.
            $datas->active = '0';
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


}