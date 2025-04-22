<?php
$this->_helper->layout->disableLayout();

$sn = $this->getRequest()->getParam('sn');
$type = $this->getRequest()->getParam('type');
$total_price = $this->getRequest()->getParam('total_price');
$vat = $this->getRequest()->getParam('vat');
$price_after_vat = $this->getRequest()->getParam('price_after_vat');

$params = array(
    'sn' => $sn,
    'group_sn_bvg' => 1,
    'isbacks' => 0,
    'group_good' => 1,
    );
if(!empty($total_price) and !empty($vat) and !empty($price_after_vat))
{
    $total_price = array(
    'total_price' => $total_price,
    'vat' => $vat,
    'price_after_vat' => $price_after_vat,
    );

    $this->view->total_price = $total_price;
}


$page = 1;
$limit = 1;
$total = 0;


$QGood = new Application_Model_Good();
$QMarket = new Application_Model_Market();
$QMarketProduct = new Application_Model_DeductionSalesSn();
$QImei = new Application_Model_Imei();
$QJoint = new Application_Model_JointCircular();
$joint = $QJoint->get_cache2();
$market = $QMarket->fetchPagination($page, $limit, $total, $params);
$QMarketDeduction = new Application_Model_MarketDeduction();
    $where = array();
    $where[] = $QMarketDeduction->getAdapter()->quoteInto('sn = ?' , $sn);
    $market_deduction = $QMarketDeduction->fetchRow($where);
    $invoice = $market_deduction['invoice_number'];
    $invoice_time = $market_deduction['invoice_time'];

if (!(isset($market) && isset($market[0]) && $market[0])) {
    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->addMessage('Sales Number not exists');
    $this->_redirect(HOST . "warehouse/product-out");
} else {
    $market = $market[0];
}


unset($params['group_sn_bvg']);
$mk_goods = $QMarket->fetchPagination(1, null, $total2, $params);

$mk_bvg_joint = array();
$params['group_sn'] = 1;
$params['d_id'] = $market['d_id'];
$params['joint'] = $market_deduction['joint_circular_id'];
foreach ($joint as $k => $v) {
    $mk_bvg_joint = $QMarketProduct->fetchPagination(1, null, $total2, $params);
}

unset($params['group_sn']);
unset($params['sort']);

// L?y danh s�ch c�c s?n ph?m theo t?ng don h�ng (d� l?y ? tr�n)
$params['sn'] = $market['sn'];
$total2 = 0;


// l?y danh s�ch IMEI c� sn nhu tr�n theo t?ng s?n ph?m
$imeis = array();

foreach ($mk_goods as $k => $v) {
    $where = array();
    $where[] = $QImei->getAdapter()->quoteInto('good_id = ?', $v['good_id']);
    $where[] = $QImei->getAdapter()->quoteInto('good_color = ?', $v['good_color']);
    $where[] = $QImei->getAdapter()->quoteInto('sales_sn = ?', $v['sn']);
    $imeis[$v['good_id']][$v['good_color']] = $QImei->fetchAll($where);
}

unset($params['sn']);

$this->view->market = $market;
$this->view->mk_goods = $mk_goods;
$this->view->imeis = $imeis;

$tmp = $QGood->fetchAll();
$all_goods = array();
foreach ($tmp as $k => $v) {
    $all_goods[$v['id']] = $v;
}
$this->view->all_goods = $all_goods;
$this->view->goods_cached = $QGood->get_cache();


$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

$QGoodColor = new Application_Model_GoodColor();
$this->view->good_colors = $QGoodColor->get_cache();

$QDistributor = new Application_Model_Distributor();

$distributor = $QDistributor->find($market['d_id']);
$distributor = $distributor->current();

if ($distributor) {
    $this->view->distributor = $distributor;
}

$QStaff = new Application_Model_Staff();
$staff = $QStaff->find($market['user_id']);
$staff = $staff->current();

if ($staff) {
    $this->view->staff = $staff;
}

$QGoodColor = new Application_Model_GoodColor();
$this->view->colors_list = $QGoodColor->get_cache();
$warehouse_id = $market['warehouse_id'];
$add_time    = $market['add_time'];

$joint_list = $QJoint->fetchAll();
$joint = array();
foreach($joint_list as $k=>$v)
{
    $joint[$v['id']] = $v['name'];
}

$joint_circular = $joint[$market_deduction['joint_circular_id']];

//set invoice time
if (!$market['invoice_time']) {
    $data = array('invoice_time' => date('Y-m-d H:i:s'));
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $QMarket->update($data, $where);
}
$this->view->add_time = strtotime($add_time);
$this->view->warehouse = $warehouse_id;
$this->view->sn = $sn;
$this->view->invoice = $invoice;
$this->view->market_product = $mk_bvg_joint;
$joint = $QJoint->get_cache();
$this->view->joint = $joint;
$this->view->invoice_time = $invoice_time;
$this->view->joint_circular = $joint_circular;