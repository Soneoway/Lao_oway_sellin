<?php
$QArea = new Application_Model_Area();
$this->view->areas = $QArea->get_cache();

$QDistributor = new Application_Model_Distributor();
$this->view->distributors = $QDistributor->get_cache();

$QStaff = new Application_Model_Staff();
$this->view->staffs = $QStaff->get_cache();

$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$page        = $this->getRequest()->getParam('page', 1);
$sort        = $this->getRequest()->getParam('sort', '');
$desc        = $this->getRequest()->getParam('desc', 0);
$area_id     = $this->getRequest()->getParam('area_id');
$name        = $this->getRequest()->getParam('name');
$export      = $this->getRequest()->getParam('export', 0);

$limit = LIMITATION;
$total = 0;
$params = array(
    'from_date' => TARGET_FROM,
    'to_date'   => TARGET_TO,
    'area_id'   => $area_id,
    'name'      => $name,
    'export'    => $export,
    );

if (!My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, SUPER_SALES_ADMIN)) && $userStorage->id != SUPERADMIN_ID) {
    $params['created_by'] = $userStorage->id;
}

$QTarget = new Application_Model_TargetDistributor();
$target = $QTarget->fetchPagination($page, $limit, $total, $params);

if (isset($export) && $export == 1) {
    $this->_export_target($target);
    exit;
}

$this->view->target = $target;
$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'sales/target-view'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

