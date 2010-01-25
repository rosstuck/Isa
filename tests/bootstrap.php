<?php
//Init autoloader
require('Zend/Loader/Autoloader.php');
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);

//Set include paths
$paths = array(
    __DIR__,
    __DIR__.'/library/',
    __DIR__.'/../vendor/'
);
set_include_path(
    implode(PATH_SEPARATOR, $paths).
    PATH_SEPARATOR.get_include_path()
);

//Grab the config
$config = require(__DIR__.'/data/config.php');
if(empty($config) || !is_array($config)) {
    throw new Exception(
        'No config array given. Copy config.example.php to config.php'
    );
}
$config = new Zend_Config($config);

//Setup the test db conn
$db = new Zend_Db_Adapter_Pdo_Mysql($config->database);
Zend_Db_Table::setDefaultAdapter($db);
