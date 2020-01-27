<?php

namespace Planner\Controllers;

use Planner\Application;
use Planner\Exceptions\ApiForbiddenException;
use Planner\Exceptions\ApiNotFoundException;
use Planner\Exceptions\ApiUnprocessableException;
use Planner\Services\BasicAuthSecure;
use Planner\Services\Permissions;


class RestController extends AbstractController
{

    protected $basicAuth;

    public function __construct()
    {

        header('Content-Type: text/json');
        $this->basicAuth = new BasicAuthSecure();
        $this->basicAuth->init();
        parent::__construct();      

    }

    public function abortIfNotAllowed($resource, $action)
    {
        $permission = new Permissions();
        if (!$permission->userHasPermission(Application::$instance->getCurrentUserId(), $resource, $action)) {
            throw new ApiForbiddenException();
        }
    }

    public function render($datas)
    {
        return json_encode($datas);
    }

    public function optionsIndex()
    {
        header('Access-Control-Allow-Headers: Authorization');
        header('Access-Control-Allow-Methods: GET,POST,PUT,PATCH,DELETE,OPTIONS');
        return true;
    }

    public function deleteIndex($query)
    {

        return $this->basicAuth->secure(function () use ($query) {

            $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'delete');

            if ($query['action'] == 'index') {
                throw new ApiUnprocessableException('Missing id in url');
            }
            $this->getModel()->delete($query['action']);
            return ['result' => 'ok'];
        });

    }

    public function getIndex($query)
    {

        return $this->basicAuth->secure(function () use ($query) {

            $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'read');

            if ($query['action'] !== 'index') {
                return $this->getModel()->getOne($query['action']);
            } else {
                return $this->getModel()->buildPaginatedQuery($_GET);
            }

        });
    }

    protected function validateNewEntity($candidate)
    {
    }

    protected function validateUpdatedEntity($candidate)
    {
    }

    protected function postProcessEntity($candidate) {

        return $candidate;
    }

    protected function doAfterCreate($entity) {
        return;
    }

    public function postMultiples($query) {
        return $this->basicAuth->secure(function () use ($query) {

            $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'update');

            $body = $this->getRequest()->getJsonBody();
            if (!isset($body->ids) || !is_array($body->ids)) {
                throw new ApiUnprocessableException('Missing ids Array');
            }
            if (!isset($body->key) ) {
                throw new ApiUnprocessableException('Missing key');
            }
            if (!isset($body->value) ) {
                throw new ApiUnprocessableException('Missing value');
            }
            $this->getModel()->massEdit($body->ids, $body->key,$body->value);
            return ['result' => 'ok'];

        });
    }

    public function postIndex($query)
    {

        return $this->basicAuth->secure(function () use ($query) {

            if ($query['action'] == 'index') {

                $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'create');

                $body = $this->getRequest()->getJsonBody();

                $this->validateNewEntity($body);
                $body = $this->postProcessEntity($body);
                $entity = $this->getModel()->getOne($this->getModel()->createOrUpdate($body));
                $this->doAfterCreate($entity);
                return $entity;

            } else {

                $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'update');

                if (!$this->getModel()->idExists($query['action'])) {
                    throw new ApiNotFoundException();
                } else {
                    $body = $this->getRequest()->getJsonBody();
                    $this->validateUpdatedEntity($body);
                    $body = $this->postProcessEntity($body);
                    $this->getModel()->patch($query['action'], $body);
                    return $this->getModel()->getOne($query['action']);
                }
            }
        });
    }

}