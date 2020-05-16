<?php namespace Cadencio\Controllers;

use Cadencio\Models\SettingModel;

class Settings extends RestController {

    public function init() {
        $this->setModel(new SettingModel());
    }

    public function getIndex($query) {
        return $this->auth->secure(function () use ($query) {

            $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'read');

            if ($query['action'] !== 'index') {
                return $this->getModel()->getOne($query['action'],'name');
            } else {
                return null;
            }

        });
    }


}