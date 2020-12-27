<?php
namespace Cadencio\Controllers;

use Cadencio\Models\LogModel;

class Logs extends RestController {

    public function init() {
        $this->setModel(new LogModel());
    }

}