<?php

namespace Modules\Plannings\Models;

use Planner\Models\AbstractModel;

class PlanningModel extends AbstractModel
{

    protected $modelName = 'plannings';
    protected $resourceName = 'plannings';

    public function getPublicFields()
    {
        return ['id','title'];
    }

    public function getSearchField() {
        return ['id','title'];
    }

    public function getOne($id, $field = 'id') {
        $planning = parent::getOne($id,$field);

        $planning['total_entries'] = $this->getAdapter()->fetchOne('
                  SELECT COUNT(planning_entry.id) 
                  FROM planning_entry 
                  WHERE id_planning = ?', [$planning['id']]);

        $planning['closed_entries'] = $this->getAdapter()->fetchOne('
                  SELECT COUNT(planning_entry.id) 
                  FROM planning_entry 
                  JOIN planning_status ON planning_status.id = planning_entry.id_status
                  WHERE id_planning = ? AND planning_status.closed = 1', [$planning['id']]);

        return $planning;

    }




}