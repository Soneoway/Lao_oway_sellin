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

$out_time_from    = $this->getRequest()->getParam('out_time_from');
$out_time_to      = $this->getRequest()->getParam('out_time_to');
$area_id          = $this->getRequest()->getParam('area_id');
$region_id        = $this->getRequest()->getParam('region_id');
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

//get product OUT // set lại nếu dành riêng cho delivery
$params['product_out']    = 1;

$QMarket = new Application_Model_Market();
// Lấy danh sách các đơn hàng (nhóm theo sn)

if ( isset($export) && $export ) {

    $params['group_sn']      = 0;

    $markets_sn = $QMarket->report($page, null, $total, $params);

    $this->_exportOutXML( $markets_sn);

}

$params['get_fields'] = array(
    'sn',
    'd_id',
    'pay_time',
    'shipping_yes_time',
    'outmysql_time',
    'warehouse_id',
    'status',
);

$markets = $QMarket->fetchPagination($page, $limit, $total, $params);

unset($params['group_sn']);

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
$this->view->markets  = $markets;
$this->view->params   = $params;
$this->view->limit    = $limit;
$this->view->total    = $total;
$this->view->url      = HOST.'warehouse/delivery'.( $params ? '?'.http_build_query($params).'&' : '?' );
$this->view->offset   = $limit*($page-1);

$QStaff                 = new Application_Model_Staff();
$this->view->staffs     = $QStaff->get_cache();

$QWarehouse             = new Application_Model_Warehouse();
$this->view->warehouses = $QWarehouse->get_cache();

$this->view->all_goods  = $QGood->get_cache();

$QGoodCategory                = new Application_Model_GoodCategory();
$this->view->good_categories  = $QGoodCategory->get_cache();

$QDistributor                 = new Application_Model_Distributor();
$this->view->distributorsList = $QDistributor->get_all_cache();

$QGoodColor                   = new Application_Model_GoodColor();
$this->view->colors_list      = $QGoodColor->get_cache();

$QArea               = new Application_Model_Area();
$this->view->areas   = $QArea->get_cache();

$QRegion             = new Application_Model_Region();
$this->view->regions = $QRegion->get_all_cache();;

if ($area_id)
    $this->view->region_select = $QRegion->get_by_area_cache($area_id);
else
    $this->view->region_select = $QRegion->get_cache();


$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();

    $this->_helper->viewRenderer->setRender('partials/product_out_list');
}