<?php
$this->_helper->layout->disableLayout();
$sn = $this->getRequest()->getParam('sn');

$params = array(
    'sn'            => $sn,
    'group_sn'      => 1,
    'isbacks'       => 0,
//    'outmysql_time' => 1,
    'group_good'    => 1,
);

$page  = 1;
$limit = 1;
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
} else {
    $market = $market[0];
}

unset($params['group_sn']);
unset($params['sort']);

// Lấy danh sách các sản phẩm theo từng đơn hàng (đã lấy ở trên)
$params['sn'] = $market['sn'];
$total2       = 0;
$mk_goods     = $QMarket->fetchPagination(1, NULL, $total2, $params);

// lấy danh sách IMEI có sn như trên theo từng sản phẩm
$imeis = array();

foreach ($mk_goods as $k => $v) {
    $where = array();
    $where[] = $QImei->getAdapter()->quoteInto('good_id = ?', $v['good_id']);
    $where[] = $QImei->getAdapter()->quoteInto('good_color = ?', $v['good_color']);
    $where[] = $QImei->getAdapter()->quoteInto('sales_sn = ?', $v['sn']);
    $imeis[$v['good_id']][$v['good_color']] = $QImei->fetchAll($where);
}

unset($params['sn']);

$this->view->market   = $market;
$this->view->mk_goods = $mk_goods;
$this->view->imeis    = $imeis;

$tmp = $QGood->fetchAll();
$all_goods = array();
foreach ($tmp as $k => $v) {
    $all_goods[$v['id']] = $v;
}
$this->view->all_goods = $all_goods;

$QGoodCategory               = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

$QGoodColor               = new Application_Model_GoodColor();
$this->view->good_colors = $QGoodColor->get_cache();

$QDistributor = new Application_Model_Distributor();

$distributor  = $QDistributor->find($market['d_id']);
$distributor  = $distributor->current();

if ($distributor) {
    $this->view->distributor = $distributor;
}

$QStaff = new Application_Model_Staff();
$staff  = $QStaff->find($market['user_id']);
$staff  = $staff->current();

if ($staff) {
    $this->view->staff = $staff;
}

$QGoodColor               = new Application_Model_GoodColor();
$this->view->colors_list  = $QGoodColor->get_cache();

//set invoice time
if (!$market['invoice_time']) {
    $data = array('invoice_time'=>date('Y-m-d H:i:s'));
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $QMarket->update($data, $where);
}
$QInvoicePrefix = new Application_Model_InvoicePrefix();
$invoice_prefix = $QInvoicePrefix->fetchAll();
$this->view->invoice_prefix = $invoice_prefix;