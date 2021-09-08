<?php
header("Access-Control-Allow-Origin: *");

require_once __DIR__.'/../src/vendor/autoload.php';
require_once __DIR__.'/../config.php';

$app = new Cadencio\Application();
$app->run();