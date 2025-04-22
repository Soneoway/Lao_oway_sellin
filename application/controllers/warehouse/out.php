<?php
// OUT CONDITION
// g.cat_id = 11
// m.isbacks = 0
// m.mark_outmysql_user = 0
// AND(
// m.mark_pay_time != 0
// AND mark_shipping_yes_time != 0
// )


$sort            = $this->getRequest()->getParam('sort', 'p.pay_time');
$desc            = $this->getRequest()->getParam('desc', 0);

$page            = $this->getRequest()->getParam('page', 1);

$sn              = $this->getRequest()->getParam('sn');
$user_id         = $this->getRequest()->getParam('user_id');
$cat_id          = $this->getRequest()->getParam('cat_id');
$good_id         = $this->getRequest()->getParam('good_id');
$good_color      = $this->getRequest()->getParam('good_color');
$d_id            = $this->getRequest()->getParam('d_id');
$warehouse_id    = $this->getRequest()->getParam('warehouse_id');

$area_id         = $this->getRequest()->getParam('area_id');
$region_id       = $this->getRequest()->getParam('region_id');
$district_id     = $this->getRequest()->getParam('district_id');
/*$payment       = $this->getRequest()->getParam('payment');
$ship_warehouse  = $this->getRequest()->getParam('ship_warehouse');*/
/*$created_at_to   = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));
$created_at_from = $this->getRequest()->getParam('created_at_from',date('d/m/Y', strtotime('-1 day')));*/

$created_at_from = $this->getRequest()->getParam('created_at_from',date('d/m/Y 00:00', strtotime('-7 day')));
$created_at_to   = $this->getRequest()->getParam('created_at_to', date('d/m/Y 23:59'));

$export          = $this->getRequest()->getParam('export', 0);

//print_r($created_at_from);
$finance_confirm_time_from  = $this->getRequest()->getParam('finance_confirm_time_from');
$finance_confirm_time_to    = $this->getRequest()->getParam('finance_confirm_time_to');

$rank           = $this->getRequest()->getParam('rank');
$show_all       = $this->getRequest()->getParam('show_all');

$near_far       = $this->getRequest()->getParam('near_far');

$this->view->rank = $rank;
$this->view->d_id = $d_id;

$limit = !$export ? LIMITATION : null;
$total = 0;

$params = array_filter( array(
    'sn'              => $sn,
    'user_id'         => $user_id,
    'cat_id'          => $cat_id,
    'good_id'         => $good_id,
    'good_color'      => $good_color,
    'd_id'            => $d_id,
    'created_at_to'   => $created_at_to,
    'created_at_from' => $created_at_from,
    'finance_confirm_time_from'  => $finance_confirm_time_from,
    'finance_confirm_time_to'    => $finance_confirm_time_to,
    'warehouse_id'    => $warehouse_id,
    'area_id'         => $area_id,
    'region_id'       => $region_id,
    'district_id'     => $district_id,
    'rank'            => $rank,
    'show_all'        => $show_all,
    'near_far'        => $near_far,
    // 'export'       => $export,
));

$QGood = new Application_Model_Good();

$QStore = new Application_Model_Store();
$this->view->store = $QStore->get_cache();

$QGoodColor = new Application_Model_GoodColor();
$this->view->colors_list = $QGoodColor->get_cache();

$params['sort']             = $sort;
$params['desc']             = $desc;

$params['list_warehouse']   = 1;
$params['group_sn']         = 1;
$params['isbacks']          = 0;
$params['no_outmysql_time'] = 1;
$params['warehouse_out']    = 1;
$params['status']           = 1;

$QMarket = new Application_Model_Market();

// Lấy danh sách các đơn hàng (nhóm theo sn)
$params['get_fields'] = array(
    'sn',
    'print_time',
    'd_id',
    'pay_time',
    'shipping_yes_time',
    'outmysql_time',
    'warehouse_id',
    'status',
    'add_time',
    'user_id',
    'total_imei',
    'phone_qty',
    'digital_qty',
    'total_digital',
    'ilike_qty',
    'total_ilike',
);
$markets = $QMarket->fetchPagination($page, $limit, $total, $params);
$available_distributor = array();

// Lấy danh sách các sản phẩm theo từng đơn hàng (đã lấy ở trên)
$mk_goods = array();
$prints = array();

if ($export == 1) {
    My_Report::preventExport();

    if ($markets) {
        foreach ($markets as $key => $mk) {
            $params['sn'] = $mk['sn'];
            $total2 = 0;
            $mk_goods[$mk['sn']]    = $QMarket->fetchPagination(1, NULL, $total2, $params);
            $prints[$mk['sn']] = $QMarket->get_print_time($mk['sn']); // lấy các cờ đã in phiếu hay chưa

            $markets[$key]['total_imei']    = $QMarket->count_out_imei($mk['sn']);
            $markets[$key]['phone_qty']     = $QMarket->count_phone($mk['sn']);

            $markets[$key]['digital_qty']   = $QMarket->count_digital($mk['sn']);
            $markets[$key]['total_digital'] = $QMarket->count_out_digital($mk['sn']);

            $markets[$key]['ilike_qty']     = $QMarket->count_ilike($mk['sn']);
            $markets[$key]['total_ilike']   = $QMarket->count_out_ilike($mk['sn']);

            $available_distributor[] = $mk['d_id'];
        }
    }

    $this->_exportOutWarehouse($markets, $mk_goods);
    exit;
}


unset($params['group_sn']);
/*unset($params['sn']);*/
unset($params['list_warehouse']);
unset($params['isbacks']);
unset($params['no_outmysql_time']);
unset($params['out']);
unset($params['warehouse_out']);
unset($params['get_fields']);

$params['sn'] = $sn;

$this->view->desc     = $desc;
$this->view->sort     = $sort;
$this->view->prints   = $prints;
$this->view->markets  = $markets;
$this->view->mk_goods = $mk_goods;
$this->view->params   = $params;
$this->view->limit    = $limit;
$this->view->total    = $total;
$this->view->url      = HOST.'warehouse/out/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset   = $limit*($page-1);

$QGroup        = new Application_Model_Group();
$QStaff        = new Application_Model_Staff();
$QWarehouse    = new Application_Model_Warehouse();
$QGoodCategory = new Application_Model_GoodCategory();
$QDistributor  = new Application_Model_Distributor();
$QArea         = new Application_Model_Area();
$QRegion       = new Application_Model_RegionalMarket();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$this->view->warehouse_rollback  = 0;
if (My_Staff_Group::inGroup($userStorage->group_id,array(ADMINISTRATOR_ID,SUPER_SALES_ADMIN,CHECK_MONEY,ROLLBACK_ORDER))) 
{
    $this->view->warehouse_rollback  = 1;
}

$sales_man_group_id = $QGroup->get_salesman_id_cache();
/*$where = $QStaff->getAdapter()->quoteInto('`group_id` = ?', $sales_man_group_id);*/
$where = $QStaff->getAdapter()->quoteInto('1', null);
$this->view->salesmans       = $QStaff->fetchAll($where);
$this->view->staffs          = $QStaff->get_cache();

$where_wh = array();
//$warehouse_type = $userStorage->warehouse_type;
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
//$this->view->warehouses      = $QWarehouse->get_cache();


$this->view->all_goods       = $QGood->get_cache();
$this->view->good_categories = $QGoodCategory->get_cache();

if (is_array($available_distributor) && count($available_distributor) > 0)
    $where = $QDistributor->getAdapter()->quoteInto('id IN (?)', $available_distributor);
elseif($available_distributor)
    $where = $QDistributor->getAdapter()->quoteInto('id = ?', $available_distributor);

if($userStorage->warehouse_type !=''){
    $warehouse_type = $userStorage->warehouse_type;
    $where = $QDistributor->getAdapter()->quoteInto('warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))', null);

}else{
    $where = $QDistributor->getAdapter()->quoteInto('warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
}

//$distributors = $QDistributor->fetchAll($where);

$distributor_list = array();

foreach ($distributors as $value)
    $distributor_list[$value['id']] = array('title' => $value['title'], 'region' => $value['region']);

$this->view->distributors     = $distributor_list;
$this->view->distributors_all = $QDistributor->get_cache();
$this->view->areas            = $QArea->get_cache();

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

    $this->_helper->viewRenderer->setRender('partials/list');
}