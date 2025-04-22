<?php
$sort           = $this->getRequest()->getParam('sort', 'add_time');
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$sn              = $this->getRequest()->getParam('sn');
$d_id            = $this->getRequest()->getParam('d_id');
$good_id         = $this->getRequest()->getParam('good_id');
$good_color      = $this->getRequest()->getParam('good_color');
$num             = $this->getRequest()->getParam('num');
$price           = $this->getRequest()->getParam('price');
$total           = $this->getRequest()->getParam('total');
$pay_time        = $this->getRequest()->getParam('payment', 0);
$outmysql_time   = $this->getRequest()->getParam('outmysql_time', 0);
$created_at_to   = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));
$created_at_from = $this->getRequest()->getParam('created_at_from', date('01/m/Y'));
$receive_at_to   = $this->getRequest()->getParam('receive_at_to'); // Add Receive date 
$receive_at_from = $this->getRequest()->getParam('receive_at_from'); // Add Receive date 
$cat_id          = $this->getRequest()->getParam('cat_id');
$creditnote_sn   = $this->getRequest()->getParam('creditnote_sn');
$create_cn       = $this->getRequest()->getParam('create_cn');
$warehouse       = $this->getRequest()->getParam('warehouse');

$rank           = $this->getRequest()->getParam('rank');
$this->view->rank = $rank;
$this->view->d_id = $d_id;

//print_r($_GET);
///die;

$export  = $this->getRequest()->getParam('export', 0);

$limit = LIMITATION;
$total = 0;

$params = array(
    'sn'              => $sn,
    'd_id'            => $d_id,
    'good_id'         => $good_id,
    'good_color'      => $good_color,
    'num'             => $num,
    'price'           => $price,
    'total'           => $total,
    'created_at_to'   => $created_at_to,
    'created_at_from' => $created_at_from,
    'receive_at_to'   => $receive_at_to,  // Add Receive date 
    'receive_at_from' => $receive_at_from,  // Add Receive date 
    'cat_id'          => $cat_id,
    'creditnote_sn'   => $creditnote_sn,
    'create_cn'       => $create_cn,
    'rank'            => $rank,
    'warehouse'       => array_unique($warehouse)
);

$params['isbacks'] = true;
$params['group_sn'] = true;

if ($pay_time)
    $params['payment'] = true;
// else
//  $params['no_payment'] = true;

if ($outmysql_time)
    $params['outmysql_time'] = true;
// else
//  $params['no_outmysql_time'] = true;

$QGood           = new Application_Model_Good();
$QGoodColor      = new Application_Model_GoodColor();
$QMarket         = new Application_Model_Market();
$QDistributor    = new Application_Model_Distributor();
$QGoodCategory   = new Application_Model_GoodCategory();
$QWarehouse = new Application_Model_Warehouse();
$QImeiReturn = new Application_Model_ImeiReturn();
$QDigitalReturn = new Application_Model_DigitalSnReturn();

$warehouses = $QWarehouse->get_cache();
$goods           = $QGood->get_cache();
$goodColors      = $QGoodColor->get_cache();
$distributors    = $QDistributor->get_cache();
$good_categories = $QGoodCategory->get_cache();

$params['sort'] = $sort;
$params['desc'] = $desc;

// $markets_sn = $QMarket->fetchPagination($page, $limit, $total, $params);
$markets = array();

// if( isset($export) && $export == 1) {
//     My_Report::preventExport();
//     $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
//     $this->_exportReturnSaleExcel( $markets_sn);
// }else 
if ( isset($export) && $export == 2) {
    $params['export_cn'] = true;
    $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
    $this->_exportReturnSaleExcelCN( $markets_sn);
 }
 else if ( isset($export) && $export == 3) {
    // $params['export_no_cn'] = true;
    $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
    $this->_exportReturnSaleExcelNoCN( $markets_sn);
} 
else if(isset($export) && $export == 4) {

    $params['gd'] = true;
    $params['gc'] = true;
    $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
    $this->_exportReturnSaleExcelCNForFinance( $markets_sn);
}
    else {
    $markets_sn = $QMarket->fetchPagination($page, $limit, $total, $params);
}

foreach ($markets_sn as $key => $m) {
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
    $markets[$m['sn']] = $QMarket->fetchAll($where);
}

//print_r($params);

$this->view->warehouses       = $warehouses;
$this->view->goods           = $goods;

$this->view->goodColors      = $goodColors;
$this->view->markets         = $markets;
$this->view->markets_sn      = $markets_sn;
$this->view->distributors    = $distributors;
$this->view->good_categories = $good_categories;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'sales/return-list/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();

    $this->_helper->viewRenderer->setRender('partials/list');
}