<?php

$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');

$QWarehouse = new Application_Model_Warehouse();
$QArea = new Application_Model_Area();
$QRegionalMarket = new Application_Model_RegionalMarket();
$QWarehouseType = new Application_Model_WarehouseType();
$QStaff = new Application_Model_Staff();

$db = Zend_Registry::get('db');
$userStorage = Zend_Auth::getInstance()->getStorage()->read();

if($id){

	$warehouseRowSet = $QWarehouse->find($id);
    $warehouse       = $warehouseRowSet->current();

    if (!$warehouse) {
        $flashMessenger->setNamespace('error')->addMessage('Invalid Client. Please Check And Try Agian !.');
        $this->_redirect(HOST.'warehouse/list');
    }

}

$this->view->warehouse   		= $warehouse;
$this->view->area_cache 		= $QArea->get_cache();
$this->view->regional 			= $QRegionalMarket->get_cache();
$this->view->warehousetype 		= $QWarehouseType->get_cache();
$this->view->staff 				= $QStaff->get_cache();

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

?>