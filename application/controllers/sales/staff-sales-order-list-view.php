<?php
$flashMessenger = $this->_helper->flashMessenger;

//print_r($_GET);die;

$company_id = $this->getRequest()->getParam('company_id');
$privileges_sn = $this->getRequest()->getParam('privileges_sn');
$action_frm = $this->getRequest()->getParam('action_frm');

if ($privileges_sn) {
    $get_resule=null;
    $QStaffSalesOrder = new Application_Model_EpPrivilegesTranOrder();
    $params = array_filter(array(
    	'company_id'       => $company_id,
        'privileges_sn'       => $privileges_sn,
        'action_frm' => 'view'
        ));

    $get_resule = $QStaffSalesOrder->staff_sales_order_view($params);

    //print_r($get_resule);
    $this->view->get_resule = $get_resule;
}

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;
$this->view->action_frm = $action_frm;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;