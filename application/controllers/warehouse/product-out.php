<?php

// AND m.isbacks = 0
// AND g.cat_id = 11
// AND m.mark_outmysql_time > 0
// GROUP BY
// m.mark_sn
// ORDER BY
// m.mark_id DESC;

$sort             = $this->getRequest()->getParam('sort', 'outmysql_time');
$desc             = $this->getRequest()->getParam('desc', 1);
$page             = $this->getRequest()->getParam('page', 1);

$sn               = $this->getRequest()->getParam('sn');
$cat_id           = $this->getRequest()->getParam('cat_id');
$good_id          = $this->getRequest()->getParam('good_id');
$good_color       = $this->getRequest()->getParam('good_color');
$distributor_id   = $this->getRequest()->getParam('distributor_id');
$distributor_name = $this->getRequest()->getParam('distributor_name');
$d_id             = $this->getRequest()->getParam('d_id');

//$out_time_from    = $this->getRequest()->getParam('out_time_from', date_sub(new DateTime(), new DateInterval('P10D'))->format('d/m/Y'));

$out_time_from   = $this->getRequest()->getParam('out_time_from', date('d/m/Y 00:00', strtotime('-0 day')));
$out_time_to      = $this->getRequest()->getParam('out_time_to', date('d/m/Y 23:59'));


$finance_confirm_time_from  = $this->getRequest()->getParam('finance_confirm_time_from');
$finance_confirm_time_to    = $this->getRequest()->getParam('finance_confirm_time_to');
$area_id          = $this->getRequest()->getParam('area_id');
$region_id        = $this->getRequest()->getParam('region_id');
$district_id      = $this->getRequest()->getParam('district_id');
$warehouse_id     = $this->getRequest()->getParam('warehouse_id');
$invoice_number   = $this->getRequest()->getParam('invoice_number');
$tags             = $this->getRequest()->getParam('tags');

$group_id           = $this->getRequest()->getParam('group_id');

$export           = $this->getRequest()->getParam('export', 0);

$rank           = $this->getRequest()->getParam('rank');
$this->view->rank = $rank;
$this->view->d_id = $d_id;

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$limit = LIMITATION;
$total = 0;

$params = array_filter(array(
    'sn'               => $sn,
    'cat_id'           => $cat_id,
    'good_id'          => $good_id,
    'good_color'       => $good_color,
    'distributor_id'   => $distributor_id,
    'distributor_name' => $distributor_name,
    'finance_confirm_time_from'  => $finance_confirm_time_from,
    'finance_confirm_time_to'    => $finance_confirm_time_to,
    'out_time_from'    => $out_time_from,
    'out_time_to'      => $out_time_to,
    'area_id'          => $area_id,
    'region_id'        => $region_id,
    'district_id'      => $district_id,
    'd_id'             => $d_id,
    'warehouse_id'     => $warehouse_id,
    'invoice_number'   => $invoice_number,
    'tags'             => $tags,
    'rank'             => $rank,
    'group_id'         => $group_id
    ));

$QGood = new Application_Model_Good();

$params['sort'] = $sort;
$params['desc'] = $desc;

$params['group_sn'] = 1;
$params['isbacks'] = 0;
$params['outmysql_time'] = 1;
$params['status'] = 1;

//get product OUT
$params['product_out'] = 1;

$QMarket = new Application_Model_Market();
$QMarketProduct = new Application_Model_MarketProduct();

if ($export == 1) {
    $limit = false;
    $params['group_sn'] = 0;
    $params['export'] = $export;
    $data = $QMarket->report($page, null, $total, $params);
    $this->_exportExcelProductOut($data);

}else if ($export == 2) {
    $limit = false;
    $params['group_sn'] = 0;
    $params['export'] = $export;
    $sql = $QMarket->report_product_out_by_imei($page, null, $total, $params);
    $this->_exportExcelProductOutByImei($sql);

}

$params['get_fields'] = array(
    'sn',
    'd_id',
    'pay_time',
    'shipping_yes_time',
    'outmysql_time',
    'warehouse_id',
    'status',
    'add_time',
    'invoice_time',
    'print_time',
    );

//print_r($params);
if((isset($_GET['sn']) ? true: false)==true)
{
    $markets = $QMarket->fetchPagination($page, $limit, $total, $params);
}else{
    $markets = null;
}

$markets_sn_array = array();

foreach ($markets as $k => $v)
{
    $markets_sn_array[$k] = $v;
    $markets_sn_array[$k]['discount'] = $QMarketProduct->getDiscount($v['sn']);
}

unset($params['group_sn']);
unset($params['get_fields']);
unset($params['status']);
unset($params['payment']);
unset($params['no_ship_warehouse']);
unset($params['list_warehouse']);

$params['sn'] = $sn;

$this->view->desc = $desc;
$this->view->sort = $sort;
$this->view->markets = $markets_sn_array;

$this->view->params = $params;
$this->view->limit = $limit;
$this->view->total = $total;
$this->view->url = HOST . 'warehouse/product-out/' . ($params ? '?' .
    http_build_query($params) . '&' : '?');

$this->view->offset = $limit * ($page - 1);

$QStaff = new Application_Model_Staff();
$this->view->staffs = $QStaff->get_cache();

$QWarehouse = new Application_Model_Warehouse();

//$warehouse_type = $userStorage->warehouse_type;

$where_wh = array();
//$where_wh[] = $QWarehouse->getAdapter()->quoteInto('warehouse_type IN ('.$warehouse_type.')', null);

if (My_Staff_Group::inGroup($userStorage->group_id, array(KERRY_STAFF,KERRY_LEADER))){
    $where_wh[] = $QWarehouse->getAdapter()->quoteInto('show_kerry = ? ', 1);
}

$warehouses_cached = $QWarehouse->fetchAll($where_wh, 'name');
$warehouse_arr = array();
foreach ($warehouses_cached as $k => $warehouse_data){
    $warehouse_arr[$warehouse_data['id']] = $warehouse_data['name']; 
}

$this->view->warehouses = $warehouse_arr;

//$this->view->warehouses = $QWarehouse->get_cache();

$this->view->all_goods = $QGood->get_cache();

$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

$QDistributor = new Application_Model_Distributor();
$this->view->distributorsList = $QDistributor->get_cache();

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$QDistributor = new Application_Model_Distributor();
//$tmp = $QDistributor->fetchAll();

if($userStorage->warehouse_type !=''){
    $warehouse_type = $userStorage->warehouse_type;
    $where = $QDistributor->getAdapter()->quoteInto('warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))', null);

}else{
    $where = $QDistributor->getAdapter()->quoteInto('warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
}

$tmp = $QDistributor->fetchAll($where);
$distributors = array();

foreach ($tmp as $k => $v)
{
    $distributors[$v['id']] = $v;
}

$this->view->distributors = $distributors;

$QGoodColor = new Application_Model_GoodColor();
$this->view->colors_list = $QGoodColor->get_cache();

$QArea        = new Application_Model_Area();
$QRegion      = new Application_Model_RegionalMarket();
$this->view->areas = $QArea->get_cache();

if ($area_id) {
    if (is_array($area_id) && count($area_id))
        $where = $QRegion->getAdapter()->quoteInto('area_id IN (?)', $area_id);
    elseif (is_numeric($area_id) && $area_id)
        $where = $QRegion->getAdapter()->quoteInto('area_id = ?', intval($area_id));

    $regions = $QRegion->fetchAll($where, 'name');

    $regions_arr = array();

    foreach ($regions as $key => $value)
        $regions_arr[$value['id']] = $value['name'];

    $this->view->regions = $regions_arr;
}

if ($region_id) {
    if (is_array($region_id) && count($region_id))
        $where = $QRegion->getAdapter()->quoteInto('parent IN (?)', $region_id);
    elseif (is_numeric($region_id) && $region_id)
        $where = $QRegion->getAdapter()->quoteInto('parent = ?', intval($region_id));

    $region_search = $QRegion->fetchAll($where, 'name');

    $this->view->districts = array();

    foreach ($region_search as $_region) {
        $this->view->districts[$_region['id']] = $_region['name'];
    }
}

$this->view->goods = $QGood->fetchAll(null, 'name');

$this->view->colors = $QGoodColor->fetchAll(null, 'name');


$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if ($this->getRequest()->isXmlHttpRequest())
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setRender('partials/product_out_list');
}
