<?php
require_once realpath(__DIR__).'/../src/vendor/autoload.php';
require_once realpath(__DIR__).'/../config.php';

$scriptHook = \Cadencio\Services\HookHandler::getInstance()->getHook('register_batch_script');

$actions = [];

foreach ($scriptHook as $hook) {
    $res = $hook();
    $actions[$res['name']] = $res['callable'];
};

$options = getopt('', ['name::']);
if (!isset($options['name']) || !$options['name']) {
    echo "No name given. Please use a name : php batch.php --name=examplename\n";
    die();
}

if (!array_key_exists($options['name'],$actions)) {
    echo "{$options['name']} not registered\n";
    die();
}


if (!is_callable($actions[$options['name']])) {
    echo "No {$options['name']} is not callable\n";
    die();
}

$actions[$options['name']]();