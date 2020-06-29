<?php

define('DB_USER','%DBUSER%');
define('DB_PASSWORD','%DBPASSWORD%');
define('DB_HOST', '%DBHOST%');
define('DB_NAME', '%DBNAME%');

define('BASE_URL', '%BASEURL%');
define('BASE_DIR', __DIR__.'/src');

define ('SENGRID_KEY' , '');

define('DEFAULT_PAGINATION', 20);

define('JWT_PRIV_KEY','%JWT_KEY%');


function core_autoloader($class)
{
    $path = str_replace('\\','/',$class);
    if(file_exists(BASE_DIR.'/'.$path . '.php')) {
        include BASE_DIR . '/' . $path . '.php';
    }
}

spl_autoload_register('core_autoloader');

include BASE_DIR .'/../modules/__config.php';

$modules = \Cadencio\Services\ModulesManager::getInstance()->getActivesModules();

foreach ($modules as $module) {
    $pathFile = BASE_DIR . '/../modules/' . $module;
    $pathFileMain = $pathFile . '/back/main.php';
    if (file_exists($pathFileMain)) {
        require_once $pathFileMain;
    }
}
