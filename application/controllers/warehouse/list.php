<?php
/*
$sort    = $this->getRequest()->getParam('sort', '');
$desc    = $this->getRequest()->getParam('desc', 1);
$page  = $this->getRequest()->getParam('page', 1);
*/

$name    = $this->getRequest()->getParam('name');
$export  = $this->getRequest()->getParam('export', 0);

$sort             = $this->getRequest()->getParam('sort', 'name');
$desc             = $this->getRequest()->getParam('desc', 1);
$page             = $this->getRequest()->getParam('page', 1);
$status           = $this->getRequest()->getParam('status');
$distributor      = $this->getRequest()->getParam('distributor');
$type             = $this->getRequest()->getParam('type');
$office           = $this->getRequest()->getParam('office');
$external_serial  = $this->getRequest()->getParam('external_serial');


$limit = LIMITATION;
$total = 0;

$params = array(
    'name'              => $name,
    'sort'              => $sort,
    'desc'              => $desc,
    'status'            => $status,
    'distributor'       => $distributor,
    'type'              => $type,
    'office'            => $office,
    'external_serial'   => $external_serial
);

$QWarehouse = new Application_Model_Warehouse();
$QArea = new Application_Model_Area();
$QRegionalmarket = new Application_Model_RegionalMarket();
$QWarehouseType = new Application_Model_WarehouseType();
$QStaff = new Application_Model_Staff();
$QDistributor = new Application_Model_Distributor();


$where = $QDistributor->getAdapter()->quoteInto('agent_status = ?', 1);
$superie_distributor = $QDistributor->fetchAll($where);

// Xuất excel
if ( isset($export) && $export ) {
    
    $warehouses = $QWarehouse->fetchPagination($page, null, $total, $params);
    $this->_exportExcel($warehouses);

} else {

    $warehouses = $QWarehouse->fetchPagination($page, $limit, $total, $params);
}


$this->view->superie_distributor    = $superie_distributor;
$this->view->warehouses             = $warehouses;
$this->view->area                   = $QArea->get_cache();
$this->view->provience              = $QRegionalmarket->get_cache();
$this->view->warehouseType          = $QWarehouseType->get_cache();
$this->view->staff                  = $QStaff->get_cache();
$this->view->desc                   = $desc;
$this->view->sort                   = $sort;
$this->view->params                 = $params;
$this->view->limit                  = $limit;
$this->view->total                  = $total;
$this->view->url                    = HOST.'warehouse/list/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset                 = $limit*($page-1);

if($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setRender('warehouse/partials/list');
} else {
    $flashMessenger               = $this->_helper->flashMessenger;
    $messages_success             = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages_success = $messages_success;

    $messages                     = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages         = $messages;

    $this->_helper->viewRenderer->setRender('list');
}