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

    public function getIndex($query) : array
    {

        return $this->auth->secure(function () use ($query) {

            if ($query['action'] !== 'index') {
                return $this->getModel()->getOne($query['action']);
            } else {
                return $this->getModel()->buildPaginatedQuery($_GET);
            }

        });
    }

    public function postIndex($query) : array
    {
        //Creation is now allowed for modules !
        if ($query['action'] == 'index') {
            throw new ApiNotFoundException();
        }

        return parent::postIndex($query);
    }



}