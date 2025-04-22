<?php
set_time_limit(0);
ini_set('memory_limit', '200M');
$sort           = $this->getRequest()->getParam('sort', '');
$desc           = $this->getRequest()->getParam('desc', 1);

$warehouse_id   = $this->getRequest()->getParam('warehouse_id');
$cat_id         = $this->getRequest()->getParam('cat_id');
$good_id        = $this->getRequest()->getParam('good_id');
$good_color_id  = $this->getRequest()->getParam('good_color_id');
$export         = $this->getRequest()->getParam('export');

$cat_id = ACCESS_CAT_ID;

$page  = $this->getRequest()->getParam('page', 1);
$limit = LIMITATION;
$total = 0;

$params = array_filter(array(
    'warehouse_id'  => $warehouse_id,
    'cat_id'        => $cat_id,
    'good_id'       => $good_id,
    'good_color_id' => $good_color_id,
    'sort'          => $sort,
    'desc'          => $desc,
));

$QGood          = new Application_Model_Good();
if ($cat_id){
    $where = $QGood->getAdapter()->quoteInto('cat_id = ?', $cat_id);
    $goods_acc = $QGood->fetchAll($where, 'name');
    $this->view->goods_acc = $goods_acc;
}

$goods_cached   = $QGood->get_cache();

$QWarehouse     = new Application_Model_Warehouse();

$warehouses     = $QWarehouse->get_cache();

$QGoodColor     = new Application_Model_GoodColor();
$goodColors     = $QGoodColor->get_cache();

$QGoodCategory  = new Application_Model_GoodCategory();
$goodCategories = $QGoodCategory->get_cache();

$QBrand         = new Application_Model_Brand();
$brands         = $QBrand->get_cache();

$data = $QGood->fetchPaginationStorage($page, $limit, $total, $params);

$this->view->goods          = $data;
$this->view->warehouses     = $warehouses;
$this->view->goods_cached   = $goods_cached;
$this->view->goodColors     = $goodColors;
$this->view->goodCategories = $goodCategories;
$this->view->brands         = $brands;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'warehouse/bad-accessories-management/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;