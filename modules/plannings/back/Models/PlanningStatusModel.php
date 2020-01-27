<?php

namespace Modules\Plannings\Models;

use Planner\Models\AbstractModel;

class PlanningStatusModel extends AbstractModel
{

    protected $modelName = 'planning_status';
    protected $resourceName = 'planning_status';

    public function getPublicFields()
    {
        return ['id','title','color','closed'];
    }

    public function getSearchField() {
        return ['id','title'];
    }

}