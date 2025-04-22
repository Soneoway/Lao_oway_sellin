<?php

$flashMessenger = $this->_helper->flashMessenger;
$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$id = $this->getRequest()->getParam('id');

if($id){

    $QMB = new Application_Model_MemberBrandshop();

    $where = $QMB->getAdapter()->quoteInto('id = ?', $id);
    $getMemberBrandshop = $QMB->fetchRow($where); 

    $this->view->getMemberBrandshop = $getMemberBrandshop;
}

$QShippingAddress = new Application_Model_ShippingAddress();
$this->view->province = $QShippingAddress->getProvince();

$this->view->member_brandshop_id = $id;

$this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER')? $this->getRequest()->getServer('HTTP_REFERER') : 'sales/create-member-bandshop';

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;