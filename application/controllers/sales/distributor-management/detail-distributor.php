<?php
$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');

// $QDistributorNew = new Application_Model_DistributorNew();
$QClient = new Application_Model_Client();
$QStaff = new Application_Model_Staff();
$QRegionalMarket = new Application_Model_RegionalMarket();

$QDistributor = new Application_Model_Distributor();

$db = Zend_Registry::get('db');
$userStorage = Zend_Auth::getInstance()->getStorage()->read();

// $distributor_cache = $QDistributorNew->get_cache();
$client_cache = $QClient->get_cache();
$staff_cache = $QStaff->get_cache();
$regional_cache = $QRegionalMarket->get_cache();


if($id){
    $distributorRowSet = $QDistributor->find($id);
    $distributor       = $distributorRowSet->current();


    if (!$distributor) {
        $flashMessenger->setNamespace('error')->addMessage('Invalid Distributor. Please Check And Try Agian !.');
        $this->_redirect(HOST.'sales/distributor-management');
    }

}

$this->view->distributor 			= $distributor;
// $this->view->distributor_cache		= $distributor_cache;
$this->view->client_cache			= $client_cache;
$this->view->staff_cache			= $staff_cache;
$this->view->regional_cache			= $regional_cache;


$flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if($this->getRequest()->isXmlHttpRequest()) {
	$this->_helper->layout->disableLayout();

	$this->_helper->viewRenderer->setRender('partials/distributornew/detail-distributor');
}

$this->_helper->viewRenderer->setRender('partials/distributornew/detail-distributor');

?>