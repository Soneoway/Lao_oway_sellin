<?php
$this->_helper->layout->disableLayout();

$sn = $this->getRequest()->getParam('sn');

$params = array(
    'sn'            => $sn,
//    'group_sn'      => 1,
    'isbacks'       => 0,
//    'outmysql_time' => 1,
    'group_good_color'    => 1,
);

$page  = 1;
$limit = null;
$total = 0;

$QGood = new Application_Model_Good();
$QMarket = new Application_Model_Market();
$QImei = new Application_Model_Imei();

// Lấy danh sách các đơn hàng (nhóm theo sn)
$market = $QMarket->fetchPagination($page, $limit, $total, $params);

if (! ( isset($market) && isset($market[0]) && $market[0]) ) {
    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->addMessage('Sales Number not exists');
    $this->_redirect(HOST."warehouse/product-out");
}

if ($this->getRequest()->getMethod() == 'POST'){
    $invoice_note_input = $this->getRequest()->getParam('invoice_note_input');

    $data = array(
        'invoice_note' => $invoice_note_input
    );

    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);

    $QMarket->update($data, $where);

    $this->_redirect(HOST."warehouse/invoice-vt?sn=".$sn);
}

//check invoice note
$invoice_note = $market[0]['invoice_note'];
if(isset($market[0]['good_id']) and in_array($market[0]['good_id'], array(373,267)) )
    $this->view->khuyenmai = 1;

//if not input invoice note
if (!$invoice_note){
    $this->_helper->viewRenderer->setRender('invoice-vt-2');
}

$this->view->market   = $market;

$tmp = $QGood->fetchAll();
$all_goods = array();
foreach ($tmp as $k => $v) {
    $all_goods[$v['id']] = $v;
}
$this->view->all_goods = $all_goods;

$QGoodCategory               = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

$QGoodColor               = new Application_Model_GoodColor();
$this->view->good_colors = $QGoodColor->get_cache2();

$QDistributor = new Application_Model_Distributor();

$distributor  = $QDistributor->find($market[0]['d_id']);
$distributor  = $distributor->current();

if ($distributor) {
    $this->view->distributor = $distributor;
}

//set invoice time
if (isset($market[0]) and !$market[0]['invoice_time']) {
    $data = array('invoice_time'=>date('Y-m-d H:i:s'));
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $QMarket->update($data, $where);
}

$QInvoicePrefix = new Application_Model_InvoicePrefix();
$invoice_prefix = $QInvoicePrefix->fetchAll();
$this->view->invoice_prefix = $invoice_prefix;