<?php namespace Modules\Plannings\Controllers;


use Modules\Plannings\Models\PlanningEntryModel;
use Cadencio\Application;
use Cadencio\Controllers\RestController;
use Cadencio\Exceptions\ApiUnprocessableException;

class Planning_entry extends RestController {

    public function init() {
        $this->setModel(new PlanningEntryModel());
    }


    public function postTogglefollow($query) {
        return $this->auth->secure(function () use ($query) {

            $body = $this->getRequest()->getJsonBody();
            if (!isset($body->id_entry)) { throw new ApiUnprocessableException('Missing id_entry'); }
            if (!isset($body->id_user)) { throw new ApiUnprocessableException('Missing id_user'); }

            if ($this->getModel()->userOwnEntry($body->id_entry, Application::$instance->getCurrentUserId())) {
                $this->abortIfNotAllowed('planning_entry','update_mine');
            }
            else {
                $this->abortIfNotAllowed('planning_entry','update');
            }

            if ($this->getModel()->userFollowEntry($body->id_entry,$body->id_user)) {
                $this->getModel()->removeFollower($body->id_entry,$body->id_user);
            }
            else {
                $this->getModel()->addFollower($body->id_entry, $body->id_user );
            }

            return ['response' => 'ok'];

        });
    }

    protected function postProcessEntity($candidate) {

        if(isset($candidate->date_start) && empty($candidate->date_start)) {
            $candidate->date_start = NULL;
        }
        if(isset($candidate->date_end) && empty($candidate->date_end)) {
            $candidate->date_end = NULL;
        }

        return $candidate;
    }


}