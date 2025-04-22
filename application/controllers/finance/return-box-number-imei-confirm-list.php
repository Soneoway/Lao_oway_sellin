<?php
$flashMessenger = $this->_helper->flashMessenger;

//edit
$sn = $this->getRequest()->getParam('sn');
$db = Zend_Registry::get('db');
$userStorage = Zend_Auth::getInstance()->getStorage()->read();


$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;