<?php
$id = $this->getRequest()->getParam('id');
$flashMessenger = $this->_helper->flashMessenger;
$refer = $this->getRequest()->getParam('refer', HOST.'hub');

try {
    if (!$id) throw new Exception("Invalid ID", 1);
    $id = intval($id);
    $QHub = new Application_Model_Hub();
    $where = $QHub->getAdapter()->quoteInto('id = ?', $id);
    $hub = $QHub->fetchRow($where);

    if (!$hub) throw new Exception("Invalid hub", 2);

    $this->view->hub = $hub;

    $QArea = new Application_Model_Area();
    $this->view->areas = $QArea->get_cache();

    $QRegion = new Application_Model_RegionalMarket();
    $this->view->regions = $QRegion->nget_all_province_with_area_cache();

    $QHubDistrict = new Application_Model_HubDistrict();
    $where = $QHubDistrict->getAdapter()->quoteInto('hub_id = ?', $id);
    $regions = $QHubDistrict->fetchAll($where);
    $provinces = array();
    $districts = array();

    foreach ($regions as $key => $value) {
        if ($value['type'] == 1) $districts[] = $value['region_id'];
        if ($value['type'] == 2) $provinces[] = $value['region_id'];
    }

    $district_list = array();

    foreach ($districts as $key => $value) {
        $district_list[ $value ] = $QRegion->nget_district_by_district_cache($value);
    }

    $this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages         = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->refer            = $refer;
    $this->view->provinces        = $provinces;
    $this->view->districts        = $districts;
    $this->view->district_list    = $district_list;

    $this->_helper->viewRenderer->setRender('create');
} catch (Exception $e) {
    $flashMessenger->setNamespace('error')->addMessage(sprintf("[%d] %s", $e->getCode(), $e->getMessage()));
    $this->_redirect(My_Url::refer('hub'));
}
