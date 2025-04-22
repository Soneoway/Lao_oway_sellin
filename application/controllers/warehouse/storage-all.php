<?php
set_time_limit(0);
ini_set('memory_limit', '200M');
$sort = $this->getRequest()->getParam('sort', '');
$desc = $this->getRequest()->getParam('desc', 1);

$warehouse_id = $this->getRequest()->getParam('warehouse_id');
$cat_id = $this->getRequest()->getParam('cat_id');
$good_id = $this->getRequest()->getParam('good_id');
$good_color_id = $this->getRequest()->getParam('good_color_id');
$export = $this->getRequest()->getParam('export');

$page = $this->getRequest()->getParam('page', 1);
$limit = 5;
$total = 0;

$params = array_filter(array(
    'warehouse_id' => $warehouse_id,
    'cat_id' => $cat_id,
    'good_id' => $good_id,
    'good_color_id' => $good_color_id,
    'sort' => $sort,
    'desc' => $desc,
    ));

$QGood = new Application_Model_Good();


$goods_cached = $QGood->get_cache();

$QWarehouse = new Application_Model_Warehouse();

$warehouses = $QWarehouse->get_cache();

$QGoodColor = new Application_Model_GoodColor();
$goodColors = $QGoodColor->get_cache();

$QGoodCategory = new Application_Model_GoodCategory();
$goodCategories = $QGoodCategory->get_cache();

$QBrand = new Application_Model_Brand();
$brands = $QBrand->get_cache();

if ($export) {

    $limit = false;

    $params['export'] = $export;

    $sql = $QGood->fetchPaginationStorage($page, $limit, $total, $params);

    $this->_exportStorage($sql, $warehouse_id);

} else
    $data = $QGood->fetchPaginationStorage($page, $limit, $total, $params);

$data_all = array();
$params['warehouse_id'] = 1;
$data_all[1] = $QGood->fetchPaginationStorage($page, $limit, $total, $params);
$params['warehouse_id'] = 2;
$data_all[2] = $QGood->fetchPaginationStorage($page, $limit, $total, $params);
$params['warehouse_id'] = 3;
$data_all[3] = $QGood->fetchPaginationStorage($page, $limit, $total, $params);


$this->view->goods = $data;
$this->view->goods_all = $data_all;
$this->view->warehouses = $warehouses;
$this->view->goods_cached = $goods_cached;
$this->view->goodColors = $goodColors;
$this->view->goodCategories = $goodCategories;
$this->view->brands = $brands;

$this->view->desc = $desc;
$this->view->sort = $sort;
$this->view->params = $params;
$this->view->limit = $limit;
$this->view->total = $total;
$this->view->url = HOST . 'warehouse/storage-all/' . ($params ? '?' .
    http_build_query($params) . '&' : '?');

$this->view->offset = $limit * ($page - 1);
