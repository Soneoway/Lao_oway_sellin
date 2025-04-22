<?php
$id = $this->getRequest()->getParam('id');
$flashMessenger = $this->_helper->flashMessenger;

$QShippingAddress = new Application_Model_ShippingAddress();
$QDistributor      = new Application_Model_Distributor();

$d_id           = $this->getRequest()->getParam('id');
$ship_id        = $this->getRequest()->getParam('ship_id');
$contract_name  = $this->getRequest()->getParam('contract_name', null);
$data_address   = $this->getRequest()->getParam('data_address', null);
$ship_province  = $this->getRequest()->getParam('ship_province', null);
$amphures       = $this->getRequest()->getParam('amphures', null);
$districts_sipping = $this->getRequest()->getParam('districts_sipping', null);
$zip_id         = $this->getRequest()->getParam('zip_id', null);
$contract_phone = $this->getRequest()->getParam('contract_phone', null);
$back_url = $this->getRequest()->getParam('back_url', null);

if ($ship_id) {
    $ShippingAddressRowSet = $QShippingAddress->find($ship_id);
    $shipping_address       = $ShippingAddressRowSet->current();
    $this->view->shipping_address = $shipping_address;

}

$this->view->province           = $QShippingAddress->getProvince();

$area_name = $QShippingAddress->getAreaName();
//print_r($area_name);
$this->view->area_name           = $area_name;

$this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER') 
    ? $this->getRequest()->getServer('HTTP_REFERER') : 'sales/distributor';

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$this->view->update_payment  = 0;
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