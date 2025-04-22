<?php
$sort = $this->getRequest()->getParam('sort', 'update_date');
$desc = $this->getRequest()->getParam('desc', 1);


$id             = $this->getRequest()->getParam('id');
$store_code     = $this->getRequest()->getParam('store_code');
$name           = $this->getRequest()->getParam('name');
$title          = $this->getRequest()->getParam('title');
$tel            = $this->getRequest()->getParam('tel');
$email          = $this->getRequest()->getParam('email');
$region         = $this->getRequest()->getParam('region');
$district       = $this->getRequest()->getParam('district');
$add            = $this->getRequest()->getParam('add');
$admin                  = $this->getRequest()->getParam('admin', 0);
$del                    = $this->getRequest()->getParam('del', 0);
$ka                     = $this->getRequest()->getParam('ka', 0);
$internal               = $this->getRequest()->getParam('internal', 0);
$inactive               = $this->getRequest()->getParam('inactive', 0);
$area                   = $this->getRequest()->getParam('area');
$from                   = $this->getRequest()->getParam('from');
$to                     = $this->getRequest()->getParam('to');
$has_exported           = $this->getRequest()->getParam('has_exported');
$warehouse_id           = $this->getRequest()->getParam('warehouse_id');

//add
$code                   = $this->getRequest()->getParam('code');
$dis_type               = $this->getRequest()->getParam('dis_type');
$status                 = $this->getRequest()->getParam('status',1);

$ka_type                = $this->getRequest()->getParam('ka_type');
$quota_channel = $this->getRequest()->getParam('quota_channel');
$group_id = $this->getRequest()->getParam('group_id');

$area_multi = $this->getRequest()->getParam('area_multi');
$area_multi = array_values(array_unique($area_multi));
$grand_area_multi = $this->getRequest()->getParam('grand_area_multi');
$grand_area_multi = array_values(array_unique($grand_area_multi));

$activate = $this->getRequest()->getParam('activate');

$export = $this->getRequest()->getParam('export', 0);

$page   = $this->getRequest()->getParam('page', 1);
$limit  = LIMITATION;
$total  = 0;

$params = array(
    'id'           => $id,
    'sort'         => $sort,
    'desc'         => $desc,
    'name'         => $name,
    'title'        => $title,
    'tel'          => $tel,
    'email'        => $email,
    'region'       => $region,
    'district'     => $district,
    'add'          => $add,
    'del'          => $del,
    'ka'           => $ka,
    'internal'     => $internal,
    'inactive'     => $inactive,
    'admin'        => $admin,
    'store_code'   => $store_code,
    'area'         => $area,
    'ka_type'      => $ka_type,
    'quota_channel'=> $quota_channel,
    'has_exported' => $has_exported,
    'from'         => $from,
    'to'           => $to,
    'export'       => $export,
    'group_id'     => $group_id,
    'area_multi'   => $area_multi,
    'grand_area_multi' => $grand_area_multi,
    'activate'     => $activate,
    'warehouse_id' => $warehouse_id,
    'code'         => $code,
    'dis_type'     => $dis_type,
    'status'       => $status
);

$QDistributor = new Application_Model_Distributor();

// Xuất excel
if ( isset($export) && $export ) {
    $params['dis_active'] = true;
    $this->_forward('distributor-list', 'sales-report', null, array('params' => $params));
    return;
} else {
    $distributors = $QDistributor->fetchPagination($page, $limit, $total, $params);
}

$QRegion = new Application_Model_RegionalMarket();
$regions = $QRegion->get_cache_all();
$districts = $QRegion->get_district_cache();

if ($area) {
    $where = $QRegion->getAdapter()->quoteInto('area_id = ?', $area);
    $region_search = $QRegion->fetchAll($where);

    $this->view->region_search = array();

    foreach ($region_search as $_region) {
        $this->view->region_search[$_region['id']] = $_region['name'];
    }
}

if ($region) {
    $where = $QRegion->getAdapter()->quoteInto('parent = ?', $region);
    $region_search = $QRegion->fetchAll($where);

    $this->view->district_search = array();

    foreach ($region_search as $_region) {
        $this->view->district_search[$_region['id']] = $_region['name'];
    }
}

$db = Zend_Registry::get('db');
$select = $db->select()
    ->from(array('p' => 'hrme.org'), array('*'))
    ->where('p.store_type_id = 1 or p.org_id IN (1,27,32,38)')
    ->order('p.org_name');

$ka_type = $db->fetchAll($select);

$this->view->ka_type = $ka_type;

$QArea = new Application_Model_Area();
$areas = $QArea->get_cache();

$QWarehouse = new Application_Model_Warehouse();
$warehouses = $QWarehouse->get_cache();

$QStaff = new Application_Model_Staff();
$staffs = $QStaff->get_cache();

$this->view->distributors = $distributors;
$this->view->areas        = $areas;
$this->view->regions      = $regions;
$this->view->districts    = $districts;
$this->view->staffs       = $staffs;
$this->view->warehouses   = $warehouses;
$this->view->desc        = $desc;
$this->view->current_col = $sort;
$this->view->params      = $params;
$this->view->limit       = $limit;
$this->view->total       = $total;
$this->view->url         = HOST.'sales/distributor/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset       = $limit*($page-1);

if($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setRender('partials/list-distributor');
} else {
    $flashMessenger               = $this->_helper->flashMessenger;
    $messages_success             = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages_success = $messages_success;

    $messages                     = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages         = $messages;
}
