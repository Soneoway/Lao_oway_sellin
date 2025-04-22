<?php
$flashMessenger = $this->_helper->flashMessenger;


$id = $this->getRequest()->getParam('id');

$QClient = new Application_Model_Client();
$QRegionalMarket = new Application_Model_RegionalMarket();
$QDistributorNew = new Application_Model_DistributorNew();


$db = Zend_Registry::get('db');
$userStorage = Zend_Auth::getInstance()->getStorage()->read();


if($id){
    $distributorRowSet = $QDistributorNew->find($id);
    $distributor       = $distributorRowSet->current();


    if (!$distributor) {
        $flashMessenger->setNamespace('error')->addMessage('Invalid Client. Please Check And Try Agian !.');
        $this->_redirect(HOST.'sales/client-management');
    }

}


$client = $QClient->get_cache();
$regional = $QRegionalMarket->get_cache();
$superiorDistributor = $QDistributorNew->getSuperiorDsitributor();


$this->view->client 			= $client;
$this->view->regional 			= $regional;
$this->view->superiorD 			= $superiorDistributor;
$this->view->distributor 		= $distributor;


$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;
?>