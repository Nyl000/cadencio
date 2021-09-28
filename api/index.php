<?php
header("Access-Control-Allow-Origin: *");

require_once __DIR__.'/../src/vendor/autoload.php';
require_once __DIR__.'/../config.php';

if (defined('SENTRY_DNS')) {
    \Sentry\init(['dsn' => SENTRY_DNS ]);
    \Sentry\captureLastError();
}




$app = new Cadencio\Application();
$app->run();


