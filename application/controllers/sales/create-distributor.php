<?php
$id = $this->getRequest()->getParam('id');
$flashMessenger = $this->_helper->flashMessenger;
$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$QRegion                = new Application_Model_RegionalMarket();
$QDistributor           = new Application_Model_Distributor();
$QShippingAddress       = new Application_Model_ShippingAddress();
$QArea                  = new Application_Model_Area();
$QCredit                = new Application_Model_Credit();
$Warehouse              = new Application_Model_Warehouse();


$QClient = new Application_Model_Client();
$client_cache = $QClient->get_cache();

$db = Zend_Registry::get('db');
$select = $db->select()
    ->from(array('p' => 'hrme.org'), array('*'))
    ->where('p.store_type_id = 1 or p.org_id IN (1,27,32,38)')
    ->order('p.org_name');

$ka_type = $db->fetchAll($select);

if ($id) { // load for editing
    $distributorRowSet = $QDistributor->find($id);
    $distributor       = $distributorRowSet->current();

    if (!$distributor) {
        $flashMessenger->setNamespace('error')->addMessage('Invalid Distributor');
        $this->_redirect(HOST.'sales/distributor');
    }

    if (isset($distributor['region'])) {
        $regions = $QRegion->get_cache_all();

        if (isset($regions[$distributor['region']]['area_id'])) {
            $this->view->area_id = $regions[$distributor['region']]['area_id'];
            $this->view->districts = $QRegion->get_district_by_province_cache($distributor['region']);
        }
    }

    //PongPond
    
    $where = $QShippingAddress->getAdapter()->quoteInto('id = ?',$distributor['shipping_add_default']);
    $ship_add = $QShippingAddress->fetchRow($where);

    //OPPO Club Grade
    $select = $db->select();
    $select->from(array('p' => 'oppo_club_distributor'),array('p.oc_grade', 'p.oc_distributor_id'));
    $select->where('p.oc_distributor_id = ?', $distributor['id']);
    $select->order(['p.oc_year desc','p.oc_q desc']);
    //echo $select;
    $oppo_club_grade = $db->fetchRow($select);

    //print_r($oppo_club_grade);

    $QEDC = new Application_Model_ExternalDistributorCode();

    $where = $QEDC->getAdapter()->quoteInto('oppo_distributor_id = ?', $id);
    $get_true_code = $QEDC->fetchRow($where);

    if(count($get_true_code)>0){
        $this->view->true_code = $get_true_code['partner_code'];
    }

    $QStaff = new Application_Model_Staff();
    $staffs_cached = $QStaff->get_cache();

    $this->view->modified_by = $staffs_cached[$distributor['update_by']];

    $this->view->ship_add = $ship_add;
    $this->view->oppo_club_grade = $oppo_club_grade['oc_grade'];
    $this->view->distributor = $distributor;

    $getDistributorGroupType = [];

    if(isset($distributor['group_id']) && $distributor['group_id']){
        $QDG = new Application_Model_DistributorGroup();
        $getDistributorGroupType = $QDG->getDistributorGroupType($distributor['group_id']);
    }

    $this->view->getDistributorGroupType = $getDistributorGroupType;

}

if (My_Staff_Group::inGroup($userStorage->group_id, FINANCE_UPDATE_PAYMENT) || $userStorage->group_id == ADMINISTRATOR_ID || $userStorage->group_id == 38) { // 38=Admin Digital
    $this->view->allow_use  = true;
}else{
    $this->view->allow_use  = false;
}

if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, SUPER_SALES_ADMIN,65)) || (isset($distributor['activate']) and $distributor['activate'] != 1)){
    //Edit Rank Price
    $this->view->allow_rank = true;
}else{
    $this->view->allow_rank = false;
}

$this->view->update_distributor  = 0;

//61=Edit Distributor
if (My_Staff_Group::inGroup($userStorage->group_id,array(ADMINISTRATOR_ID,SUPER_SALES_ADMIN,CHECK_MONEY,61))) 
{
    $this->view->update_distributor  = 1;
}

$this->view->ka_type            = $ka_type;

$this->view->areas              = $QArea->get_cache();
$this->view->province           = $QShippingAddress->getProvince();
$this->view->regions            = $QRegion->get_cache_all();
$this->view->credits            = $QCredit->get_cache();
$this->view->client_cache       = $client_cache;
$this->view->distributor_cache  = $QDistributor->get_cache();
$this->view->warehouses         = $Warehouse->get_cache();
$this->view->back_url           = $this->getRequest()->getServer('HTTP_REFERER')? $this->getRequest()->getServer('HTTP_REFERER') : 'sales/distributor';
$this->view->update_payment     = 0;
if ($id) {
    if (My_Staff_Group::inGroup($userStorage->group_id, FINANCE_UPDATE_PAYMENT) || $userStorage->group_id == ADMINISTRATOR_ID ) 
    {
        $this->view->update_payment  = 1;
    }
}else{
    $this->view->update_payment  = 1;
}

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;