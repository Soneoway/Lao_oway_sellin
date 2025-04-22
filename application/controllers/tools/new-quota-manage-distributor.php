<?php

$flashMessenger = $this->_helper->flashMessenger;

$sort = $this->getRequest()->getParam('sort', '');
$desc = $this->getRequest()->getParam('desc', 1);
$page = $this->getRequest()->getParam('page', 1);

$store_code = $this->getRequest()->getParam('store_code');
$distributor = $this->getRequest()->getParam('distributor');
$dis_type = $this->getRequest()->getParam('dis_type');
$order_type = $this->getRequest()->getParam('order_type');
$warehouse_id = $this->getRequest()->getParam('warehouse_id');
$cat_id = $this->getRequest()->getParam('cat_id');
$good_id = $this->getRequest()->getParam('good_id');
$good_color = $this->getRequest()->getParam('good_color');

$limit = LIMITATION;
$total = 0;

$params = array(
    'store_code' => $store_code,
    'distributor' => $distributor,
    'dis_type' => $dis_type,
    'order_type' => $order_type,
    'warehouse_id' => $warehouse_id,
    'cat_id' => $cat_id,
    'good_id' => $good_id,
    'good_color' => $good_color
);

$params['sort'] = $sort;
$params['desc'] = $desc;

$QNQD = new Application_Model_NewQuotaDistributor();
$QNQDD = new Application_Model_NewQuotaDistributorDetails();

$QWarehouse = new Application_Model_Warehouse();
$this->view->warehouses = $QWarehouse->get_cache();

$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

$QGood = new Application_Model_Good();
$this->view->goods = $QGood->get_cache();

$QGoodColor = new Application_Model_GoodColor();
$this->view->colors = $QGoodColor->get_cache();

$get_quota = $QNQD->fetchPagination($page, $limit, $total, $params);
$this->view->quota = $get_quota;

$array_quota_color = array();
$array_quota_current = array();
$good_product_color = array();

if($get_quota){

    $good_product_color = $QGood->getColorByGood($good_id);

    $array_quota_color_id = array();

    foreach ($get_quota as $key => $value) {
        array_push($array_quota_color_id, $value['id']);
    }

    $get_quota_details = $QNQDD->getQuotaDetails($array_quota_color_id);

    foreach ($get_quota_details as $key => $value) {
        $array_quota_color[$value['nqd_id']][$value['good_color']] = $value;
    }

    $get_quota_current = $QNQD->getQuotaCurrent($array_quota_color_id);

    foreach ($get_quota_current as $key => $value) {
        if(isset($value['good_color'])){
            $array_quota_current[$value['id']][$value['good_color']] = $value;
        }
    }

}

$this->view->good_product_color = $good_product_color;
$this->view->quota_color = $array_quota_color;
$this->view->quota_current = $array_quota_current;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'tool/new-quota-manage-distributor/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

?>