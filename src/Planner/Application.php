<?php
namespace Planner;

use Planner\Exception\ApiException;
use Planner\Services\HookHandler;

class Application {

    public static $instance;
    protected $rewriter;
    protected $currUserId;

    public function __construct() {
        if(!isset(self::$instance)) {
            self::$instance = $this;
        }
        $this->rewriter = new Rewriter();
    }

    public function route() {


        $this->rewriter->addRewrite('^/([a-z0-9_]+)/([a-z0-9]+)/([a-z0-9]+)/([a-z0-9\._\*]+)/?.*', 'index.php?resource=$1&action=$2&subaction=$3&subid=$4');
        $this->rewriter->addRewrite('^/([a-z0-9_]+)/([a-z0-9]+)/([a-z0-9]+)/?.*', 'index.php?resource=$1&action=$2&subaction=$3');
        $this->rewriter->addRewrite('^/([a-z0-9_]+)/([a-z0-9]+)/?.*', 'index.php?resource=$1&action=$2');
        $this->rewriter->addRewrite('^/([a-z0-9_]+)/?.*', 'index.php?resource=$1&action=index');
        $this->rewriter->addRewrite('^/', 'index.php?resource=index&action=index');

        $this->rewriter->rewrite();

        $parse = $this->rewriter->getParsedUrl();
        return $parse['query'];

    }

    private function resolveClass($className,$actionName,$query,$httpMethod) {
        $class = new $className();
        if (method_exists($class,$actionName)){
            try {
                $datas = $class->$actionName($query);
                return $class->render($datas);
            }
            catch (ApiException $e) {
                header($e->getResponseHeader());
                return json_encode(['message' => $e->getMessage()]);
            }
        }
        else {
            $actionName = strtolower($httpMethod). 'Index';
            if (method_exists($class, $actionName)) {
                try {
                    $datas = $class->$actionName($query);
                    return $class->render($datas);
                }
                catch (ApiException $e) {
                    header($e->getResponseHeader());
                    return json_encode(['message' => $e->getMessage()]);
                }
            }
            header("HTTP/1.0 404 Not found");
        }
    }


    public function handleRouteQuery(Array $query) {
        header("Access-Control-Allow-Origin: *");

        $className = 'Planner\\Controllers\\'.ucfirst($query['resource']);
        $httpMethod = $_SERVER['REQUEST_METHOD'];

        $actionName = strtolower($httpMethod).ucfirst($query['action']);

        $modulesRestClasses = [];
        $modulesRestClassesHook = HookHandler::getInstance()->getHook('register_rest_controller');
        foreach($modulesRestClassesHook as $hook) {
            $modulesRestClasses = array_merge($modulesRestClasses, $hook());
        }

        if (class_exists($className)) {
            return $this->resolveClass($className,$actionName,$query,$httpMethod);
        }
        elseif(array_key_exists($query['resource'], $modulesRestClasses)) {
            return $this->resolveClass($modulesRestClasses[$query['resource']], $actionName,$query,$httpMethod);
        }
        else{
            header("HTTP/1.0 404 Not found");
        }

        return;

    }

    public function run() {
        $query = $this->route();
        echo $this->handleRouteQuery($query);

    }

    public function getCurrentUserId() {
        return $this->currUserId;
    }

    public function setCurrentUserId($idUser) {
        $this->currUserId = $idUser;
    }
}


