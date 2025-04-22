<?php
$sort           = $this->getRequest()->getParam('sort', 'add_time');
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$d_id            = $this->getRequest()->getParam('d_id');
$good_id         = $this->getRequest()->getParam('good_id');
$good_color      = $this->getRequest()->getParam('good_color');
$order_to        = $this->getRequest()->getParam('order_to', date('30/m/Y'));
$order_from         = $this->getRequest()->getParam('order_from', date('01/m/Y'));
$cat_id          = $this->getRequest()->getParam('cat_id');
$warehouse       = $this->getRequest()->getParam('warehouse');
$export           = $this->getRequest()->getParam('export');
$area           = $this->getRequest()->getParam('area');
//$type           = $this->getRequest()->getParam('type');


$rank           = $this->getRequest()->getParam('rank');
$this->view->rank = $rank;
$this->view->d_id = $d_id;

$export  = $this->getRequest()->getParam('export', 0);

$limit = LIMITATION;
$total = 0;

$params = array(
    'd_id'            => $d_id,
    'area'            => $area,
    'good_id'         => $good_id,
    'good_color'      => $good_color,
    'order_to'        => $order_to,
    'order_from'      => $order_from,
    'cat_id'          => $cat_id,
    'warehouse'       => $warehouse,
);


$QGood           = new Application_Model_Good();
$QGoodColor      = new Application_Model_GoodColor();
$QMarket         = new Application_Model_Market();
$QDistributor    = new Application_Model_Distributor();
$QGoodCategory   = new Application_Model_GoodCategory();
$QWarehouse = new Application_Model_Warehouse();
$QImeiReturn = new Application_Model_ImeiReturn();
$QDigitalReturn = new Application_Model_DigitalSnReturn();
$QArea           = new Application_Model_Area();

$warehouses_cached = $QWarehouse->get_cache();
$area            = $QArea->get_cache();
$goods           = $QGood->get_cache();
$goodColors      = $QGoodColor->get_cache();
$distributors    = $QDistributor->get_cache();
$good_categories = $QGoodCategory->get_cache();

$params['sort'] = $sort;
$params['desc'] = $desc;

$markets = array();

    if ( isset($export) && $export == 1) {
    $params['get_total_sales'] = true;
    $markets_sn = $QMarket->checkOrderDistributor($params);
    $this->_exportCheckOrderExcel($markets_sn);
    }

    if ( isset($export) && $export == 2) {
    $params['list_imei'] = true;
    $markets_sn = $QMarket->checkOrderDistributorModel($params);
    $this->_exportModelCheckOrderExcel($markets_sn);
    }

    if ( isset($export) && $export == 3) {
    $params['list_imei'] = true;
    $markets_sn = $QMarket->checkOrderDistributor($params);
    $this->_exportImeiCheckOrderExcel($markets_sn);
    }

if (!empty($_GET)) { 
    $markets_sn = $QMarket->checkOrderDistributor($params);

    $total_query = $QMarket->checkOrderDistributor($params);
}

$this->view->area              = $area;
$this->view->total_query    = $total_query;
$this->view->warehouses_cached       = $warehouses_cached;
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
$this->view->url    = HOST.'sales/distributor-order/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();

    $this->_helper->viewRenderer->setRender('partials/list-check-order');
}