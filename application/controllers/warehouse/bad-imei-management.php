<?php
$sort           = $this->getRequest()->getParam('sort');
$desc           = $this->getRequest()->getParam('desc', 1);

$page           = $this->getRequest()->getParam('page', 1);

$imei_sn        = $this->getRequest()->getParam('imei_sn');
$sn             = $this->getRequest()->getParam('sn');
$good_id        = $this->getRequest()->getParam('good_id');
$good_color     = $this->getRequest()->getParam('good_color');
$export         = $this->getRequest()->getParam('export');

$limit = LIMITATION;
$total = 0;

$params = array_filter( array(
    'sn'        => $sn,
    'imei_sn'   => $imei_sn,
    'good_id'   => $good_id,
    'good_color' => $good_color,
    'export' => $export,
));

$QWarehouse = new Application_Model_Warehouse();
$this->view->warehouses = $QWarehouse->get_cache();

$QImei = new Application_Model_Imei();

if ($export){
    $sql = $QImei->fetchBadPagination($page, $limit, $total, $params);
    $this->_exportBadXML($sql);
}
$list = $QImei->fetchBadPagination($page, $limit, $total, $params);

$this->view->list = $list;

$QGood           = new Application_Model_Good();

$where = $QGood->getAdapter()->quoteInto('cat_id = ?', PHONE_CAT_ID);

$this->view->goods           = $QGood->fetchAll($where);
$this->view->goods_cached    = $QGood->get_cache();

$QGoodColor           = new Application_Model_GoodColor();
$this->view->good_colors_cached    = $QGoodColor->get_cache();

if ($good_id){
    $good_colors_by_good = $QGood->get_cache2();

    $this->view->good_colors_2_cached = isset($good_colors_by_good[$good_id]) ? $good_colors_by_good[$good_id] : null;
}

$this->view->desc     = $desc;
$this->view->sort     = $sort;
$this->view->params   = $params;
$this->view->limit    = $limit;
$this->view->total    = $total;
$this->view->url      = HOST.'warehouse/bad-imei-management/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset   = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;