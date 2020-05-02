<?php namespace Planner\Controllers;

use Planner\Exceptions\ApiException;
use Planner\Exceptions\ApiNotFoundException;
use Planner\Exceptions\ApiUnprocessableException;
use Planner\Models\ModuleModel;

class Modules extends RestController
{

    public function init()
    {
        $this->setModel(new ModuleModel());
    }


    public function postIndex($query)
    {
        //Creation is now allowed for modules !
        if ($query['action'] == 'index') {
            throw new ApiNotFoundException();
        }

        return parent::postIndex($query);
    }



}