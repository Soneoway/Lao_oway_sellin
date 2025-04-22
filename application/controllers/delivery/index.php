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

$imei_list        = $this->getRequest()->getParam('sn');
$d_id             = $this->getRequest()->getParam('d_id');
//$out_time_from    = $this->getRequest()->getParam('out_time_from', date('d/m/Y'));
$out_time_from    = $this->getRequest()->getParam('out_time_from', date('d/m/Y'));
$out_time_to      = $this->getRequest()->getParam('out_time_to', date('d/m/Y'));
$area_id          = $this->getRequest()->getParam('area_id');
$region_id        = $this->getRequest()->getParam('region_id');
$district_id      = $this->getRequest()->getParam('district_id');
$warehouse_id     = $this->getRequest()->getParam('warehouse_id');
$ajax             = $this->getRequest()->getParam('ajax', 0);

$rank           = $this->getRequest()->getParam('rank');
$this->view->rank = $rank;
$this->view->d_id = $d_id;

$limit = LIMITATION;
$total = 0;

$imei_list = trim($imei_list);
$imei_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $imei_list);
$imei_list = explode("\n", $imei_list);
$imei_list = array_filter($imei_list);

$params = array(
    'sn'            => $imei_list,
    'out_time_from' => $out_time_from,
    'out_time_to'   => $out_time_to,
    'area_id'       => $area_id,
    'region_id'     => $region_id,
    'district_id'   => $district_id,
    'd_id'          => $d_id,
    'warehouse_id'  => $warehouse_id,
    'rank'          => $rank
);

$QGood = new Application_Model_Good();

$params['sort'] = $sort;
$params['desc'] = $desc;

$params['group_sn']      = 1;
$params['isbacks']       = 0;
$params['outmysql_time'] = 1;
$params['payment']       = 1;
$params['status']        = 1;
$params['delivery']      = 1;

//get product OUT // set lại nếu dành riêng cho delivery
// $params['product_out']    = 1;

$QMarket = new Application_Model_Market();
// Lấy danh sách các đơn hàng (nhóm theo sn)

$params['get_fields'] = array(
    'sn',
    'd_id',
    'pay_time',
    'shipping_yes_time',
    'outmysql_time',
    'warehouse_id',
    'invoice_number',
    'status',
    'text',
    'delivery',
);

$markets = $QMarket->fetchPagination($page, $limit, $total, $params);

unset($params['group_sn']);
unset($params['payment']);
unset($params['outmysql_time']);
unset($params['get_fields']);
unset($params['status']);
unset($params['payment']);
unset($params['no_ship_warehouse']);
unset($params['list_warehouse']);
unset($params['delivery']);

$params['sn'] = implode("\n", $imei_list);

/*$this->view->search_sn= $sn;*/
$this->view->desc     = $desc;
$this->view->sort     = $sort;
$this->view->markets  = $markets;
$this->view->params   = $params;
$this->view->limit    = $limit;
$this->view->total    = $total;
$this->view->url      = HOST.'delivery'.( $params ? '?'.http_build_query($params).'&' : '?' );
$this->view->offset   = $limit*($page-1);

$QStaff                 = new Application_Model_Staff();
$this->view->staffs     = $QStaff->get_cache();

$QWarehouse             = new Application_Model_Warehouse();
$this->view->warehouses = $QWarehouse->get_cache();

$QDistributor                 = new Application_Model_Distributor();
$this->view->distributorsList = $QDistributor->get_all_cache();
/*
$QArea = new Application_Model_Area();
$QRegion = new Application_Model_RegionalMarket();

$this->view->areas = $QArea->get_cache();

$QService = new Application_Model_Service();
$this->view->services = $QService->get_cache_service();

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
print_r($params);
*/
$flashMessenger = $this->_helper->flashMessenger;
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();

if($this->getRequest()->isXmlHttpRequest() || $ajax) {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setRender('partials/list');
}