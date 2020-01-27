<?php namespace Planner\Controllers;

use Planner\Models\NotificationModel;

class Notifications extends RestController {

    public function init() {
        $this->setModel(new NotificationModel());
    }

}