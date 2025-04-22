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
$created_at_to   = $this->getRequest()->getParam('created_at_to');
$created_at_from = $this->getRequest()->getParam('created_at_from');
$cat_id          = $this->getRequest()->getParam('cat_id');
$warehouses      = $this->getRequest()->getParam('warehouse');

$export  = $this->getRequest()->getParam('export', 0);


$limit = LIMITATION;
$total = 0;

$params = array_filter( array(
    'sn'              => $sn,
    'd_id'            => $d_id,
    'good_id'         => $good_id,
    'good_color'      => $good_color,
    'num'             => $num,
    'price'           => $price,
    'total'           => $total,
    'created_at_to'   => $created_at_to,
    'created_at_from' => $created_at_from,
    'cat_id'          => $cat_id,
    'warehouse'       => $warehouses,
));

$params['isbacks'] = true;
$params['warehouse_return'] = true;
$params['group_sn'] = 1;


$QGood           = new Application_Model_Good();
$QGoodColor      = new Application_Model_GoodColor();
$QMarket         = new Application_Model_Market();
$QDistributor    = new Application_Model_Distributor();
$QGoodCategory   = new Application_Model_GoodCategory();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QWarehouse     = new Application_Model_Warehouse();

$goods           = $QGood->get_cache();
$goodColors      = $QGoodColor->get_cache();
$distributors    = $QDistributor->get_cache();
$good_categories = $QGoodCategory->get_cache();
$warehouses = $QWarehouse->get_cache();

$params['sort'] = $sort;
$params['desc'] = $desc;

$markets_sn = $QMarket->fetchPagination($page, $limit, $total, $params);
$markets = array();

if( isset($export) && $export == 1) {
    $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
    $this->_exportReturnToSystem( $markets_sn);
}

if( isset($export) && $export == 2) {
    $params['new_export'] = true;
    $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
    $this->_exportReturnToSystemImei( $markets_sn);
}

foreach ($markets_sn as $key => $m) {
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
    $markets[$m['sn']] = $QMarket->fetchAll($where);
}

if (My_Staff_Group::inGroup($userStorage->group_id, ADMINISTRATOR_ID)) {
    $this->view->user_group  = 'admin';
}

$this->view->goods           = $goods;
$this->view->goodColors      = $goodColors;
$this->view->markets         = $markets;
$this->view->markets_sn      = $markets_sn;
$this->view->distributors    = $distributors;
$this->view->good_categories = $good_categories;
$this->view->warehouses     = $warehouses;

unset ($params['isbacks']);
unset ($params['group_sn']);
unset ($params['warehouse_return']);

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'warehouse/return-list/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();

    $this->_helper->viewRenderer->setRender('partials/return-list');
}