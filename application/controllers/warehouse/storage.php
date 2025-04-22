<?php
set_time_limit(0);
ini_set('memory_limit', '200M');

$sort               = $this->getRequest()->getParam('sort', '');
$desc               = $this->getRequest()->getParam('desc', 1);

$warehouse_id       = $this->getRequest()->getParam('warehouse_id');
$cat_id             = $this->getRequest()->getParam('cat_id');
$good_id            = $this->getRequest()->getParam('good_id');
$good_color_id      = $this->getRequest()->getParam('good_color_id');
$export             = $this->getRequest()->getParam('export');
$search             = $this->getRequest()->getParam('search', 0);
$brand_id           = $this->getRequest()->getParam('brand_id');
$page               = $this->getRequest()->getParam('page', 1);

$limit = none;
$total = 0;

$params = array_filter(array(
    'warehouses'    => $warehouse_id,
    'warehouse_id'  => implode(",",$warehouse_id),
    'cat_id'        => $cat_id,
    'good_id'       => implode(",",$good_id),
    'good'          => $good_id,
    'good_color'    => $good_color_id,
    'good_color_id' => implode(",",$good_color_id),
    'sort'          => $sort,
    'desc'          => $desc,
    'search'        => $search,
    'brand_id'     => $brand_id
));

// print_r($params['good_id']);

$QGood          = new Application_Model_Good();
$goods_cached   = $QGood->getProduct2($params);

$QWarehouse     = new Application_Model_Warehouse();

$warehouse_type = $userStorage->warehouse_type;
$warehouses_cached = $QWarehouse->getWarehouses($warehouse_type);
$warehouse_arr = array();

foreach ($warehouses_cached as $k => $warehouse_data){
$warehouse_arr[$warehouse_data['id']] = $warehouse_data['name'];
}

$warehouses_cached = $warehouse_arr;

$QGoodColor     = new Application_Model_GoodColor();

$QGoodCategory  = new Application_Model_GoodCategory();
$goodCategories = $QGoodCategory->get_cache();

$QBrand         = new Application_Model_Brand();
$brands         = $QBrand->get_cache();

$QImei         = new Application_Model_Imei();
$QImeiDigital  = new Application_Model_DigitalSn();

if(isset($good_id) && $good_id) {
    $goodColors = $QGood->getColorWithProductSelected($good_id);
}else{
    $goodColors     = $QGoodColor->get_cache();
}


$this->view->warehouses     = $warehouses;
$this->view->goods_cached   = $goods_cached;
$this->view->goodColors     = $goodColors;
$this->view->goodColorsCache     = $QGoodColor->get_cache();
$this->view->goodCategories = $goodCategories;
$this->view->brands         = $brands;
$this->view->warehouses_cached = $warehouses_cached;

if (!$search && !$export) return;

if ($export == 1) {
    $limit = false;
    $params['export'] = $export;
    $data = $QGood->fetchPaginationStorage($page, $limit, $total, $params);
   
    $this->_exportStorage($data, $warehouse_id);
}

//update by Pungpond export stock IMEI
elseif ($export == 2) {
    $limit = false;
    $params['export'] = $export;
    if(isset($cat_id) && $cat_id == 13){
        $data = $QImeiDigital->fetchStorageImeiDigital($page, $limit, $total, $params);
        $this->_exportExcel4Digital($data);
    }else{
        $data = $QImei->fetchStorageImei($page, $limit, $total, $params);
        $this->_exportExcel4($data);
    }
}  else

    $data = $QGood->fetchPaginationStorage($page, $limit, $total, $params);
   
$this->view->goods  = $data;
$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'warehouse/storage/'.( $params ? '?'.http_build_query($params).'&' : '?' );
$this->view->offset = $limit*($page-1);
