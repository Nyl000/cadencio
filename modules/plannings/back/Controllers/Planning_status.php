<?php namespace Modules\Plannings\Controllers;

use Modules\Plannings\Models\PlanningStatusModel;
use Cadencio\Controllers\RestController;

class Planning_status extends RestController {

    public function init() {
        $this->setModel(new PlanningStatusModel());
    }


}