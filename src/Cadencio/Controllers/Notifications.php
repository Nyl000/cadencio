<?php namespace Cadencio\Controllers;

use Cadencio\Models\NotificationModel;

class Notifications extends RestController {

    public function init() {
        $this->setModel(new NotificationModel());
    }

}