<?php namespace Cadencio\Controllers;

use Cadencio\Exceptions\ApiException;
use Cadencio\Exceptions\ApiNotFoundException;
use Cadencio\Exceptions\ApiUnprocessableException;
use Cadencio\Models\ModuleModel;

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