<?php

namespace Cadencio\Controllers;

use Cadencio\Application;
use Cadencio\Exceptions\ApiForbiddenException;
use Cadencio\Exceptions\ApiNotFoundException;
use Cadencio\Exceptions\ApiRuntimeException;
use Cadencio\Exceptions\ApiUnprocessableException;
use Cadencio\Services\auth;
use Cadencio\Services\HookHandler;
use Cadencio\Services\Permissions;
use Cadencio\Services\Security\BasicAuth;
use Cadencio\Services\Security\DigestAuth;
use Cadencio\Services\Security\Jwt;
use Cadencio\Services\Security\JwtInUrl;
use Cadencio\Services\Security\ProviderInterface;
use Cadencio\Services\Security\SecurityProvider;
use Cadencio\Services\CsvUtils;


class RestController extends AbstractController
{

    protected $auth;
    protected $renderOverrideFunction;
    protected $readPermission = 'read';

    public function __construct()
    {

        header('Content-Type: text/json');
        $this->auth = new SecurityProvider();
        $this->auth->addProvider(new BasicAuth());
        $this->auth->addProvider(new Jwt());
        $this->auth->addProvider(new JwtInUrl());

        $securityProviderHook = HookHandler::getInstance()->getHook('register_security_provider');
        foreach ($securityProviderHook as $hook) {
            $provider = $hook();
            if (!$provider instanceof ProviderInterface) {
                throw new \Exception('security provider must implement the ProviderInterface');
            }
            $this->auth->addProvider($provider);
        }
        $this->auth->init();
        parent::__construct();

    }

    protected function setRenderOverrideFunction($func)
    {
        if (is_callable($func)) {
            $this->renderOverrideFunction = $func;
        }
    }

    protected function getRenderOverrideFunction() :? \Closure
    {
        return $this->renderOverrideFunction;
    }

    public function abortIfNotAllowed($resource, $action)
    {
        if (!$this->userHasPermission($resource, $action)) {
            throw new ApiForbiddenException('You are not allowed to do this action: '.$resource.'.'.$action);
        }
    }

    public function userHasPermission($resource, $action) : bool
    {
        $permission = new Permissions();
        return $permission->userHasPermission(Application::$instance->getCurrentUserId(), $resource, $action);
    }

    public function render($datas) : string
    {
        if (is_callable($this->getRenderOverrideFunction())) {
            return $this->getRenderOverrideFunction()($datas);
        }
        $datasJson = json_encode($datas);

        if (!$datasJson) {
            throw new ApiRuntimeException('Invalid json string in output (Maybe charset error ?)');
        }
        return $datasJson;
    }

    public function deleteIndex($query)
    {

        return $this->auth->secure(function () use ($query) {

            if (isset($query['subaction']) && method_exists($this, 'delete' . ucfirst($query['subaction']))) {
                $funct = 'delete' . ucfirst($query['subaction']);
                return $this->$funct($query);
            }

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

        return $this->auth->secure(function () use ($query) {

            if (isset($query['subaction']) && method_exists($this, 'get' . ucfirst($query['subaction']))) {
                $funct = 'get' . ucfirst($query['subaction']);
                return $this->$funct($query);
            }

            $this->abortIfNotAllowed($this->getModel()->getResourceName(), $this->readPermission);

            if ($query['action'] !== 'index') {
                return $this->requestOne($query['action']);
            } else {
                return $this->getModel()->buildPaginatedQuery($_GET);
            }

        });
    }

    protected function requestOne($identifier)
    {
        return $this->getModel()->getOne($identifier);
    }

    protected function validateNewEntity($candidate)
    {

    }

    protected function validateUpdatedEntity($candidate)
    {
    }

    protected function postProcessEntity($candidate)
    {

        return $candidate;
    }

    protected function doAfterCreate($entity)
    {
        return;
    }

    public function postMultiples($query)
    {
        return $this->auth->secure(function () use ($query) {

            $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'update');

            $body = $this->getRequest()->getJsonBody();
            if (!isset($body->ids) || !is_array($body->ids)) {
                throw new ApiUnprocessableException('Missing ids Array');
            }
            if (!isset($body->key)) {
                throw new ApiUnprocessableException('Missing key');
            }
            if (!isset($body->value)) {
                throw new ApiUnprocessableException('Missing value');
            }
            $this->getModel()->massEdit($body->ids, $body->key, $body->value);
            return ['result' => 'ok'];

        });
    }

    public function getExport($query)
    {
        return $this->auth->secure(function () use ($query) {
            $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'export');

            $this->setRenderOverrideFunction(function ($datas) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="export_' . date('Y-m-d_H-i-s') . '.csv"');
                return $datas;

            });

            $_GET['nbItems'] = 9999999;

            $this->getModel()->setPublicFields($this->getModel()->getExportFields());
            $datas = $this->getModel()->buildPaginatedQuery($_GET);
            $datas = $datas[$this->getModel()->getModelName()];

            $fh = fopen('php://temp', 'rw');

            fputcsv($fh, array_keys(current($datas)), ';');
            foreach ($datas as &$row) {
                fputcsv($fh, $row, ';');
            }

            rewind($fh);
            $csv = stream_get_contents($fh);
            fclose($fh);
            return $csv;
        });
    }

    public function postIndex($query)
    {

        return $this->auth->secure(function () use ($query) {

            if (isset($query['subaction']) && method_exists($this, 'post' . ucfirst($query['subaction']))) {
                $funct = 'post' . ucfirst($query['subaction']);
                return $this->$funct($query);
            }

            if ($query['action'] == 'index') {

                return $this->requestCreate();

            } else {

                return $this->requestUpdate($query['action']);

            }
        });
    }

    protected function requestCreate() {
        $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'create');

        $body = $this->getRequest()->getJsonBody();

        $this->validateNewEntity($body);
        $body = $this->postProcessEntity($body);
        $entity = $this->getModel()->getOne($this->getModel()->createOrUpdate($body));
        $this->doAfterCreate($entity);
        return $entity;
    }

    protected function requestUpdate($identifier)
    {
        $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'update');

        if (!$this->getModel()->idExists($identifier)) {
            throw new ApiNotFoundException();
        } else {
            $body = $this->getRequest()->getJsonBody();
            $this->validateUpdatedEntity($body);
            $body = $this->postProcessEntity($body);
            $this->getModel()->patch($identifier, $body);
            return $this->getModel()->getOne($identifier);
        }
    }

    public function postCsvinfos($query)
    {
        $body = $this->getRequest()->getJsonBody();
        $file = base64_decode($body->file);
        $file = CsvUtils::decode($file, 'auto');
        $separator = isset($body->separator) ? $body->separator : ';';
        $enclosure = isset($body->enclosure) ? $body->enclosure : '"';
        $md5 = md5($file);
        $rows = [];
        file_put_contents('/tmp/cadencio_import_' . $md5, $file);
        if (($handle = fopen('/tmp/cadencio_import_' . $md5, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 100000, $separator, $enclosure)) !== FALSE) {
                $rows[] = $data;
            }
        }
        fclose($handle);
        unlink('/tmp/cadencio_import_' . $md5);


        return $rows;
    }

    public function getEmpty()
    {
        return $this->auth->secure(function () {
            return $this->getModel()->getEmpty();
        });
    }


}