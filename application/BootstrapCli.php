<?php




class BootstrapCli extends Zend_Application_Bootstrap_Bootstrap
{

    public function __construct($application)
    {
        parent::__construct($application);

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('My_');
    }

	protected function _initRouter ()
	{
		$this->bootstrap ('frontcontroller');

		$this->getResource ('frontcontroller')
			->setResponse(new Zend_Controller_Response_Cli())
			->setRouter (new My_Application_Router_Cli ())
			->setRequest (new Zend_Controller_Request_Simple ());
	}


	protected function _initError ()
	{
		$frontcontroller = $this->getResource ('frontcontroller');

		$error = new Zend_Controller_Plugin_ErrorHandler ();
		$error->setErrorHandlerController ('error');

		$frontcontroller->registerPlugin ($error, 100);

		return $error;
	}

    protected function _initDB() {

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');

        $db = Zend_Db::factory($config->resources->db);
        $db->query("SET NAMES utf8;");
        Zend_Db_Table::setDefaultAdapter($db);

        Zend_Registry::set('db', $db);
    }
}

