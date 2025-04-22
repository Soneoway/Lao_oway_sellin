<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function __construct($application)
    {
        parent::__construct($application);

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('My_');

        $frontendOptions = array
        (
            'lifetime' 					=> null,
            'automatic_serialization' 	=> true
        );

        $strRootDir 	= APPLICATION_PATH.'/../data/cache' ;
        $backendOptions = array
        (
            'cache_dir'	=> $strRootDir
        );
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        Zend_Registry::set('cache', $cache);
    }

    protected function _initDB() {

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');

        $db = Zend_Db::factory($config->resources->db);
        $db->query("SET NAMES utf8;");
        Zend_Db_Table::setDefaultAdapter($db);

        Zend_Registry::set('db', $db);
    }

    protected function _initPlugins()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin(new My_Controller_Plugin_ACL(), 1);
        $frontController->registerPlugin(new My_Controller_Plugin_Db(), 2);
        return $frontController;
    }
}
