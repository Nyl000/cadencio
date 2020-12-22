<?php
define('SKIP_MODULES',1);

require_once realpath(__DIR__).'/../src/vendor/autoload.php';
require_once realpath(__DIR__).'/../config.php';



$adapter = new \Cadencio\Adapters\MysqlAdapter();
$adapter->setPdoAttribute(\PDO::ATTR_EMULATE_PREPARES, 0);

$queries = file_get_contents(BASE_DIR . '/../db/mysql/create.sql');



$adapter->exec($queries);