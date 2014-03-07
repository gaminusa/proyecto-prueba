<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'db' => array(
        'username' => 'root',
        'password' => '*sql$$cr1xus*',
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=db_cusquena2013;host=localhost',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'utf8\''
        ),
    )
);
