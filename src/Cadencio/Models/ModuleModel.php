<?php

namespace Cadencio\Models;

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