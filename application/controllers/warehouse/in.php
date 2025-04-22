<?php
$sort           = $this->getRequest()->getParam('sort', 'created_at');
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);
$sn             = $this->getRequest()->getParam('sn');
$created_by     = $this->getRequest()->getParam('created_by');
$cat_id         = $this->getRequest()->getParam('cat_id');
$good_id        = $this->getRequest()->getParam('good_id');
$good_color     = $this->getRequest()->getParam('good_color');
$warehouse_id   = $this->getRequest()->getParam('warehouse_id');
/*$payment        = $this->getRequest()->getParam('payment');
$ship_warehouse = $this->getRequest()->getParam('ship_warehouse');*/
$created_at_to  = $this->getRequest()->getParam('created_at_to');
$created_at_from = $this->getRequest()->getParam('created_at_from');

$limit = LIMITATION;
$total = 0;

$params = array_filter( array(
    'sn' => $sn,
    'created_by' => $created_by,
    'cat_id' => $cat_id,
    'good_id' => $good_id,
    'good_color' => $good_color,
    'warehouse_id' => $warehouse_id,
    /*'payment' => $payment,
    'ship_warehouse' => $ship_warehouse,*/
    'created_at_to' => $created_at_to,
    'created_at_from' => $created_at_from,
));

$QGood = new Application_Model_Good();

$params['sort'] = $sort;
$params['desc'] = $desc;

$params['payment'] = 1;
$params['no_ship_warehouse'] = 1;
$params['list_warehouse'] = 1;
$params['group_sn'] = 1;

$QPO = new Application_Model_Po();
// Lấy danh sách PO, nhóm theo po_sn
$POs = $QPO->fetchPagination($page, $limit, $total, $params);


unset($params['group_sn']);
unset($params['sort']);

// lấy danh sách các sản phẩm theo từng po_sn ở trên
$po_goods = array();
foreach ($POs as $key => $po) {
    $params['sn'] = $po['sn'];
    $total2 = 0;
    $po_goods[$po['sn']] = $QPO->fetchPagination(1, NULL, $total2, $params);

    // if (PHONE_CAT_ID == $po['cat_id']) {
    //     $POs[$key]['total_sn'] = $QPO->count_imported_imei($po['sn']);
        
    // } elseif (DIGITAL_CAT_ID == $po['cat_id']) {
    //     $POs[$key]['total_sn'] = $QPO->count_imported_digitalsn($po['sn']);

    // } elseif (ILIKE_CAT_ID == $po['cat_id']) {

    //     $POs[$key]['total_sn'] = $QPO->count_imported_sn($po['sn']);
    // }
}

/*unset($params['sn']);*/
unset($params['payment']);
unset($params['no_ship_warehouse']);
unset($params['list_warehouse']);

$params['sn'] = $sn;

$this->view->desc     = $desc;
$this->view->sort     = $sort;
$this->view->POs      = $POs;
$this->view->po_goods = $po_goods;
$this->view->params   = $params;
$this->view->limit    = $limit;
$this->view->total    = $total;
$this->view->url      = HOST.'warehouse/in/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset   = $limit*($page-1);

$QStaff = new Application_Model_Staff();
$this->view->staffs = $QStaff->get_cache();

$QWarehouse = new Application_Model_Warehouse();
$this->view->warehouses = $QWarehouse->get_cache();

$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$messages_imeis = $flashMessenger->setNamespace('imei')->getMessages();
$this->view->messages_imeis = $messages_imeis;

if($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();

    $this->_helper->viewRenderer->setRender('partials/list');
}