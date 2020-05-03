<?php namespace Modules\Plannings\Controllers;

use Modules\Plannings\Models\PlanningEntryModel;
use Modules\Plannings\Models\PlanningModel;
use Cadencio\Controllers\RestController;

class Planning extends RestController {

    public function init() {
        $this->setModel(new PlanningModel());
    }

    public function getEntries($query) {
        return $this->basicAuth->secure(function () use ($query) {

            $model = new PlanningEntryModel();

            $this->abortIfNotAllowed($model->getResourceName(), 'read');

            $results = $model->buildPaginatedQuery($_GET+['id_planning' => $query['subaction']], $model->getResourceName(), 'getByPlanningQuery');
            return $results;
        });
    }

    public function getTimeline($query) {
        return $this->basicAuth->secure(function () use ($query) {

            $model = new PlanningEntryModel();

            $this->abortIfNotAllowed($model->getResourceName(), 'read');

            $results = $model->getAllByPlanning( $query['subaction'],$_GET);
            return [
                'entries' => $results,
                'first_date' => $model->getFirstDateInPlanning($query['subaction'],$_GET),
                'last_date' => $model->getLastDateInPlanning($query['subaction'],$_GET),
            ];
        });
    }

}