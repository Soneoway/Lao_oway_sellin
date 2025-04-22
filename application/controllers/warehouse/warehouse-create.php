<?php
$id = $this->getRequest()->getParam('id');

if ($id) { // load for editing
    $QWarehouse      = new Application_Model_Warehouse();
    $warehouseRowSet = $QWarehouse->find($id);
    $warehouse       = $warehouseRowSet->current();

    $this->view->warehouse = $warehouse;
}

$QArea = new Application_Model_Area();
$this->view->area = $QArea->get_cache();

$QRegionalmarket = new Application_Model_RegionalMarket();

$where[] = $QRegionalmarket->getAdapter()->quoteInto('area_id IS NOT NULL',0);
$provience = $QRegionalmarket->fetchAll($where);

$this->view->provience = $provience;

$QWarehouseType  = new Application_Model_WarehouseType();
$this->view->warehouse_type = $QWarehouseType->get_cache();

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$this->_helper->viewRenderer->setRender('warehouse-create');