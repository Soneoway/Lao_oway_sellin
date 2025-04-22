<?php
// AND m.isbacks = 0
// AND g.cat_id = 11
// AND m.mark_outmysql_time > 0
// GROUP BY
// m.mark_sn
// ORDER BY
// m.mark_id DESC;

$sort = $this->getRequest()->getParam('sort', 'outmysql_time');
$desc = $this->getRequest()->getParam('desc', 1);
$page = $this->getRequest()->getParam('page', 1);

$sn               = $this->getRequest()->getParam('sn');
$cat_id           = $this->getRequest()->getParam('cat_id');
$good_id          = $this->getRequest()->getParam('good_id');
$good_color       = $this->getRequest()->getParam('good_color');
$distributor_id   = $this->getRequest()->getParam('distributor_id');
$distributor_name = $this->getRequest()->getParam('distributor_name');
$d_id             = $this->getRequest()->getParam('d_id');

$out_time_from    = $this->getRequest()->getParam('out_time_from', date_sub(new DateTime(), new DateInterval('P10D'))->format('d/m/Y'));
$out_time_to      = $this->getRequest()->getParam('out_time_to', date('d/m/Y'));
$area_id          = $this->getRequest()->getParam('area_id');
$region_id        = $this->getRequest()->getParam('region_id');
$district_id      = $this->getRequest()->getParam('district_id');
$warehouse_id     = $this->getRequest()->getParam('warehouse_id');
$invoice_number   = $this->getRequest()->getParam('invoice_number');
$export           = $this->getRequest()->getParam('export',0);

$limit = LIMITATION;
$total = 0;

$params = array_filter( array(
    'sn'               => $sn,
    'cat_id'           => $cat_id,
    'good_id'          => $good_id,
    'good_color'       => $good_color,
    'distributor_id'   => $distributor_id,
    'distributor_name' => $distributor_name,
    'out_time_from'    => $out_time_from,
    'out_time_to'      => $out_time_to,
    'area_id'          => $area_id,
    'region_id'        => $region_id,
    'district_id'      => $district_id,
    'd_id'             => $d_id,
    'warehouse_id'     => $warehouse_id,
    'invoice_number'   => $invoice_number
));

$QGood = new Application_Model_Good();

$params['sort'] = $sort;
$params['desc'] = $desc;

$params['group_sn']      = 1;
$params['isbacks']       = 0;
$params['outmysql_time'] = 1;
$params['status']        = 1;

$params['product_out_archived'] = 1;

$QMarket = new Application_Model_Market();
$QMarketProduct = new Application_Model_MarketProduct();
// Lấy danh sách các đơn hàng (nhóm theo sn)

if ( isset($export) && $export ) {


    if ($export==1) {
        $params['group_sn']      = 0;

        $markets_sn = $QMarket->report($page, null, $total, $params);

        $this->_exportOutXML( $markets_sn);

    } else if ($export==2) {
        if (My_Report::preventExport()) exit;
        $params['group_sn']      = 0;
        $params['get_imei']      = 1;
        $markets_sn = $QMarket->report($page, null, $total, $params);

        $this->_exportImeiOutXML( $markets_sn);
    }

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
    'cat_id',
    'good_id',
    'good_color',
    'num',
    'price',
);

$markets = $QMarket->fetchPagination($page, $limit, $total, $params);

$markets_sn_array = array();

foreach ($markets as $k => $v)
{
    $markets_sn_array[$k] = $v;
    $markets_sn_array[$k]['discount'] = $QMarketProduct->getDiscount($v['sn']);
}

unset($params['group_sn']);
/*unset($params['sort']);*/

// Lấy danh sách các sản phẩm theo từng đơn hàng (đã lấy ở trên)
$mk_goods = array();
$prints = array();
foreach ($markets as $key => $mk) {
    $params['sn'] = $mk['sn'];
    $total2 = 0;
    $mk_goods[$mk['sn']] = $QMarket->fetchPagination(1, NULL, $total2, $params);
    $prints[$mk['sn']] = $QMarket->get_print_time($mk['sn']); // lấy các cờ đã in phiếu hay chưa
    $markets[$key]['total_imei'] = $QMarket->count_out_imei($mk['sn']);
}

/*unset($params['sn']);*/
unset($params['group_sn']);
unset($params['get_fields']);
unset($params['status']);
unset($params['payment']);
unset($params['no_ship_warehouse']);
unset($params['list_warehouse']);

$params['sn'] = $sn;

/*$this->view->search_sn= $sn;*/
$this->view->desc     = $desc;
$this->view->sort     = $sort;
$this->view->markets  = $markets_sn_array;
$this->view->mk_goods = $mk_goods;

$this->view->prints = $prints;

$this->view->params   = $params;
$this->view->limit    = $limit;
$this->view->total    = $total;
$this->view->url      = HOST.'warehouse/product-out-archived/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset   = $limit*($page-1);

$QStaff                 = new Application_Model_Staff();
$this->view->staffs     = $QStaff->get_cache();

$QWarehouse             = new Application_Model_Warehouse();
$this->view->warehouses = $QWarehouse->get_cache();
$this->view->all_goods      = $QGood->get_cache();

$QGoodColor               = new Application_Model_GoodColor();
$this->view->colors_list  = $QGoodColor->get_cache();

if (isset($good_id) && $good_id) {
    $this->view->colors  = $QGoodColor->get_cache();
}

$QGoodCategory               = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

$QDistributor = new Application_Model_Distributor();
$this->view->distributors = $QDistributor->get_cache2();

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

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();

    $this->_helper->viewRenderer->setRender('partials/product_out_list');
}