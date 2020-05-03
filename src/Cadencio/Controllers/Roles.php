<?php

namespace Cadencio\Controllers;

use Config\Permissions;
use Cadencio\Exceptions\ApiNotFoundException;
use Cadencio\Exceptions\ApiUnprocessableException;
use Cadencio\Models\RoleModel;

class Roles extends RestController
{

    public function init()
    {
        $this->setModel(new RoleModel());
    }

    public function postIndex($query)
    {
        if ($query['action'] !== 'index' && $query['subaction'] === 'permissions') {
            return $this->basicAuth->secure(function () use ($query) {
                $this->abortIfNotAllowed('roles','update');
                if (!$this->getModel()->idExists($query['action'])) {
                    throw new ApiNotFoundException();
                }
                $body = $this->getRequest()->getJsonBody();

                if (!isset($body->resource)) {
                    throw new ApiUnprocessableException('Missing resource in body');
                }
                if (!isset($body->right)) {
                    throw new ApiUnprocessableException('Missing right in body');
                }
                $this->getModel()->addPermission($query['action'],$body->resource,$body->right);
            });
        } else {
            return parent::postIndex($query);
        }
    }

    public function deleteIndex($query)
    {

        if ($query['action'] !== 'index' && $query['subaction'] === 'permissions' && isset($query['subid'])) {
            return $this->basicAuth->secure(function () use ($query) {
                $this->abortIfNotAllowed('roles','delete');
                if (!$this->getModel()->idExists($query['action'])) {
                    throw new ApiNotFoundException();
                }
                $permission = explode('.',$query['subid']);
                $this->getModel()->removePermission($query['action'],$permission[0],$permission[1]);
            });
        }
        else {
            return parent::deleteIndex($query);
        }


    }

    protected function validateNewEntity($candidate)
    {
        if (!isset($candidate->name)) {
            throw new ApiUnprocessableException('Missing name property in root object');
        }
        if ($candidate->name == '') {
            throw new ApiUnprocessableException('name is empty');
        }
        if ($this->getModel()->idExists($candidate->name, 'name')) {
            throw new ApiUnprocessableException('Name already exists');
        }
    }

    protected function validateUpdatedEntity($candidate)
    {

        if (isset($candidate->name) && empty($candidate->name)) {
            throw new ApiUnprocessableException('Name is empty');
        }
        if (isset($candidate->name) && $this->getModel()->idExists($candidate->name, 'name')) {
            throw new ApiUnprocessableException('Name already exists');
        }
    }

    public function getAllpermissions()
    {
        $permissions = new Permissions();
        return $permissions->getResources();
    }


}