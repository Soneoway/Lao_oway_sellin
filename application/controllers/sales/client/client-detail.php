<?php
$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');

$QClient        = new Application_Model_Client();
$QStaff         = new Application_Model_Staff();
$QDistributor   = new Application_Model_Distributor();

$db = Zend_Registry::get('db');
$userStorage = Zend_Auth::getInstance()->getStorage()->read();

if($id){
    $clientRowSet = $QClient->find($id);
    $client       = $clientRowSet->current();

    $where = $QDistributor->getAdapter()->quoteInto('client_code =?',$client['customer_code']);
    $distributors = $QDistributor->fetchAll($where);



    if (!$client) {
        $flashMessenger->setNamespace('error')->addMessage('Invalid Client. Please Check And Try Agian !.');
        $this->_redirect(HOST.'sales/client-management');
    }

}

    // print_r($distributors); exit();


$this->view->client         = $client;
$this->view->staff          = $QStaff->get_cache();
$this->view->distributors   = $distributors;

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;


?>