<?php
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
if (!$userStorage) $this->_redirect(HOST);

$QDistributor = new Application_Model_Distributor();
$QDeliveryOrder = new Application_Model_DeliveryOrder();

$limit          = LIMITATION;
$total          = 0;
$page           = $this->getRequest()->getParam('page', 1);
$sort           = $this->getRequest()->getParam('sort', 'created_at');
$desc           = $this->getRequest()->getParam('desc', 1);
$delivery_sn    = $this->getRequest()->getParam('delivery_sn');
$sn             = $this->getRequest()->getParam('sn');
$receiver       = $this->getRequest()->getParam('receiver');
$address        = $this->getRequest()->getParam('address');
$phone          = $this->getRequest()->getParam('phone');
// $from           = $this->getRequest()->getParam('from', date('01/m/Y'));
$from           = $this->getRequest()->getParam('from', date('d/m/Y', strtotime('-3 day')));
$to             = $this->getRequest()->getParam('to', date('d/m/Y'));
$status         = $this->getRequest()->getParam('status', 0);
$area_id        = $this->getRequest()->getParam('area_id');
$region_id      = $this->getRequest()->getParam('region_id');
$district_id    = $this->getRequest()->getParam('district_id');
$d_id           = $this->getRequest()->getParam('d_id');
$type           = $this->getRequest()->getParam('type');
$staff_id       = $this->getRequest()->getParam('staff_id');
$carrier        = $this->getRequest()->getParam('carrier');
$warehouse      = $this->getRequest()->getParam('warehouse');
$export         = $this->getRequest()->getParam('export', 0);
$export_finance = $this->getRequest()->getParam('export_finance', 0);
$export_kerry   = $this->getRequest()->getParam('export_kerry', 0);

$rank           = $this->getRequest()->getParam('rank');
$this->view->rank = $rank;
$this->view->d_id = $d_id;

$params = array(
    'sort'           => $sort,
    'desc'           => $desc,
    'delivery_sn'    => trim($delivery_sn),
    'sn'             => trim($sn),
    'receiver'       => trim($receiver),
    'address'        => trim($address),
    'phone'          => trim($phone),
    'from'           => trim($from),
    'to'             => trim($to),
    'status'         => $status,
    'area_id'        => $area_id,
    'region_id'      => $region_id,
    'district_id'    => $district_id,
    'd_id'           => $d_id,
    'type'           => $type,
    'carrier'        => My_Delivery_Type::Outside == $type ? $carrier : null,
    'staff_id'       => My_Delivery_Type::Inhouse == $type ? $staff_id : null,
    'warehouse'      => $warehouse,
    'export'         => $export,
    'export_finance' => $export_finance,
    'export_kerry'   => $export_kerry,
    'list'           => true,
    'rank'           => $rank
);

$params['for_carrier'] = $userStorage->id;
/*
if (My_Staff_Group::inGroup($userStorage->group_id, array(MANAGER, WAREHOUSE, WAREHOUSE_LEADER, ADMINISTRATOR_ID, FINANCE)))
        $params['for_carrier'] = -1;
*/
if (isset($export) && $export) {
    $order_list = $QDeliveryOrder->fetchPagination(1, null, $total, $params);
    //print_r($order_list);
    My_Report_Delivery::exportDeliveryOrder($order_list);
    exit;
}

if (isset($export_finance) && $export_finance) {
    //My_Report::preventExport();
    $order_list = $QDeliveryOrder->fetchPagination(1, null, $total, $params);
    $this->_exportDeliveryOrderForFinance($order_list);
    exit;
}

if (isset($export_kerry) && $export_kerry) {
    //My_Report::preventExport();
    $order_list = $QDeliveryOrder->fetchPagination(1, null, $total, $params);
    //print_r($order_list);
    //die;
    $this->_exportDeliveryOrderForKerry($order_list);
    exit;
}

$this->view->distributor_cache = $QDistributor->get_cache();
$this->view->order_list = $QDeliveryOrder->fetchPagination($page, $limit, $total, $params);
$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'delivery/order-control/'.( $params ? '?'.http_build_query($params).'&' : '?' );
$this->view->offset = $limit*($page-1);

$QDistributor = new Application_Model_Distributor();
$QArea        = new Application_Model_Area();
$QRegion      = new Application_Model_RegionalMarket();
$QStaff       = new Application_Model_DeliveryMan();
$QWarehouse   = new Application_Model_Warehouse();
$this->view->areas = $QArea->get_cache();
$this->view->warehouses = $QWarehouse->get_cache();

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

$this->view->distributors = $QDistributor->get_cache();
$this->view->staffs = $QStaff->get_cache();

$flashMessenger = $this->_helper->flashMessenger;
$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();