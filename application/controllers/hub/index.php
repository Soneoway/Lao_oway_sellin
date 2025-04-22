<?php 
$name        = $this->getRequest()->getParam('name');
$district_id = $this->getRequest()->getParam('district_id');
$region_id   = $this->getRequest()->getParam('region_id');
$area_id     = $this->getRequest()->getParam('area_id');
$export     = $this->getRequest()->getParam('export');

$page   = $this->getRequest()->getParam('page', 1);
$limit  = LIMITATION;
$total  = 0;
$params = array(
    'name'        => $name,
    'district_id' => $district_id,
    'region_id'   => $region_id,
    'area_id'     => $area_id,
    'export'      => $export,
);

$QHub = new Application_Model_Hub();
$QArea = new Application_Model_Area();
$QRegion = new Application_Model_RegionalMarket();

if ($export) {
    $hubs = $QHub->fetchPagination(1, null, $total, $params);
    $this->_exportCsvHubs($hubs);
    exit;
}

$this->view->hubs   = $QHub->fetchPagination($page, $limit, $total, $params);
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->page   = $page;
$this->view->offset = $limit*($page-1);
$this->view->params = $params;
$this->view->url    = HOST.'hub'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->areas = $QArea->get_cache();

if ($area_id)
{
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

if ($region_id)
{
    if (is_array($region_id) && count($region_id))
        $where = $QRegion->getAdapter()->quoteInto('parent IN (?)', $region_id);
    elseif (is_numeric($region_id) && $region_id)
        $where = $QRegion->getAdapter()->quoteInto('parent = ?', intval($region_id));
    $region_search = $QRegion->fetchAll($where, 'name');

    $this->view->districts = array();

    foreach ($region_search as $_region)
        $this->view->districts[$_region['id']] = $_region['name'];
}

$flashMessenger = $this->_helper->flashMessenger;
$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();

