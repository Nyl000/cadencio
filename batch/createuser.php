<?php

require_once realpath(__DIR__).'/../src/vendor/autoload.php';
require_once realpath(__DIR__).'/../config.php';



$adapter = new \Cadencio\Adapters\MysqlAdapter();

$options = getopt('', ['email::','password::','idrole::']);

if (!isset($options['email']) || !$options['email']) {
    echo "No email given. Please use a name : php createuser.php --email=my@email.com --password=myPwd --idrole=1 \n";
    die();
}

if (!isset($options['password']) || !$options['password']) {
    echo "No email given. Please use a name : php createuser.php --email=my@email.com --password=myPwd --idrole=1 \n";
    die();
}

if (!isset($options['idrole']) || !$options['idrole']) {
    echo "No email given. Please use a name : php createuser.php --email=my@email.com --password=myPwd --idrole=1 \n";
    die();
}


$adapter->query(
    'INSERT INTO users(id,email,password,date_register,id_role) 
            VALUES (0,?, SHA2(?,256),NOW(),?);',
    [$options['email'],$options['password'],$options['idrole']]
);

