<?php
$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');

$QClient = new Application_Model_Client();


$db = Zend_Registry::get('db');
$userStorage = Zend_Auth::getInstance()->getStorage()->read();


if($id){
    $clientRowSet = $QClient->find($id);
    $client       = $clientRowSet->current();


    if (!$client) {
        $flashMessenger->setNamespace('error')->addMessage('Invalid Client. Please Check And Try Agian !.');
        $this->_redirect(HOST.'sales/client-management');
    }

}


$this->view->client = $client;

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;


?>