<?php

namespace Cadencio;

use Cadencio\Exception\ApiException;
use Cadencio\Models\UserModel;
use Cadencio\Services\HookHandler;

class Application
{

    public static $instance;
    protected $rewriter;
    protected $currUserId;
    protected $currUserModel;

    protected $modules = [];

    public function __construct()
    {
        if (!isset(self::$instance)) {
            self::$instance = $this;
        }
        $this->rewriter = new Rewriter();
    }

    public function route()
    {


        $this->rewriter->addRewrite('^/([-a-z0-9_]+)/([-a-z0-9_]+)/([-a-z0-9]+)/([-a-z0-9\._\*]+)/?.*', 'index.php?resource=$1&action=$2&subaction=$3&subid=$4');
        $this->rewriter->addRewrite('^/([-a-z0-9_]+)/([-a-z0-9_]+)/([-a-z0-9]+)/?.*', 'index.php?resource=$1&action=$2&subaction=$3');
        $this->rewriter->addRewrite('^/([-a-z0-9_]+)/([-a-z0-9_]+)/?.*', 'index.php?resource=$1&action=$2');
        $this->rewriter->addRewrite('^/([-a-z0-9_]+)/?.*', 'index.php?resource=$1&action=index');
        $this->rewriter->addRewrite('^/', 'index.php?resource=index&action=index');

        $this->rewriter->rewrite();

        $parse = $this->rewriter->getParsedUrl();
        return $parse['query'];

    }

    public function registerModules()
    {
        $hookHandler = HookHandler::getInstance();
        $modulesHooked = $hookHandler->getHook('register_module');
        $modules = [];

        foreach ($modulesHooked as $moduleHook) {
            $modules = array_merge($modules, $moduleHook());
        }
        $this->modules = $modules;
    }

    public function getModuleInstance($name)
    {
        return $this->modules[$name];
    }

    private function resolveClass($className, $actionName, $query, $httpMethod)
    {
        $class = new $className();
        if (method_exists($class, $actionName)) {
            try {
                $datas = $class->$actionName($query);
                return $class->render($datas);
            } catch (ApiException $e) {
                header($e->getResponseHeader());
                return json_encode(['message' => $e->getMessage()]);
            }
        } else {
            $actionName = strtolower($httpMethod) . 'Index';
            if (method_exists($class, $actionName)) {
                try {
                    $datas = $class->$actionName($query);
                    return $class->render($datas);
                } catch (ApiException $e) {
                    header($e->getResponseHeader());
                    return json_encode(['message' => $e->getMessage()]);
                }
            }
            header("HTTP/1.0 404 Not found");
        }
    }


    public function handleRouteQuery(Array $query)
    {
        header("Access-Control-Allow-Origin: *");
        $httpMethod = $_SERVER['REQUEST_METHOD'];

        if($httpMethod == 'OPTIONS') {
            header('Access-Control-Allow-Headers: *');
            header('Access-Control-Allow-Methods: GET,POST,PUT,PATCH,DELETE,OPTIONS');
            die();
        }

        $className = 'Cadencio\\Controllers\\' . ucfirst($query['resource']);

        $actionName = strtolower($httpMethod) . ucfirst($query['action']);

        $modulesRestClasses = [];
        $modulesRestClassesHook = HookHandler::getInstance()->getHook('register_rest_controller');
        foreach ($modulesRestClassesHook as $hook) {
            $modulesRestClasses = array_merge($modulesRestClasses, $hook());
        }

        if (class_exists($className)) {
            $res = $this->resolveClass($className, $actionName, $query, $httpMethod);
            return $res;
        } elseif (array_key_exists($query['resource'], $modulesRestClasses)) {
            return $this->resolveClass($modulesRestClasses[$query['resource']], $actionName, $query, $httpMethod);
        } else {
            header("HTTP/1.0 404 Not found");
        }

        return;

    }

    public function run()
    {
        try {
            $query = $this->route();
            $this->registerModules();
            $res = $this->handleRouteQuery($query);

            echo $res;
            die();
        }
        catch (\Exception $e) {
            if ($e instanceof \Cadencio\Exceptions\ApiException) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                echo json_encode(['api_error' => $e->getMessage()]);
                die();
            }
            else {
                throw $e;
            }

        }


    }

    public function getCurrentUserId()
    {
        return $this->currUserId;
    }

    public function setCurrentUserId($idUser)
    {
        $this->currUserId = $idUser;
    }


    public function getCurrentUserModel()
    {
        return $this->currUserModel;
    }

    public function setCurrentUserModel($model)
    {
        if ( ! ($model instanceof UserModel)) throw new \Exception("$model does not inhertit from UserModel !");
        $this->currUserModel = new $model();
    }
}


