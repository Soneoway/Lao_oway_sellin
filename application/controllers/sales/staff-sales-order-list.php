<?php
$flashMessenger = $this->_helper->flashMessenger;


$sort 		= $this->getRequest()->getParam('sort','p.create_date');
$desc    	= $this->getRequest()->getParam('desc', 1);
$page       = $this->getRequest()->getParam('page',1);
$limit      = LIMITATION;
$total      = 0;

//print_r($_GET);
//die;

$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QStaffSalesOrder = new Application_Model_EpPrivilegesTranOrder();
$db = Zend_Registry::get('db');
$date = date('Y-m-d H:i:s');

$get_discount_type = $QStaffSalesOrder->getDiscountType();
$this->view->get_discount_type  = $get_discount_type;

$act      = $this->getRequest()->getParam('act');
$privileges_sn      = $this->getRequest()->getParam('privileges_sn');
$privileges_no      = $this->getRequest()->getParam('privileges_no');
$sales_order_no      = $this->getRequest()->getParam('sales_order_no');
$status      = $this->getRequest()->getParam('status','2');
$start_date      = $this->getRequest()->getParam('start_date');
$end_date      = $this->getRequest()->getParam('end_date');
$staff_name      = $this->getRequest()->getParam('staff_name');
$staff_code     = $this->getRequest()->getParam('staff_code');
$discount_id     = $this->getRequest()->getParam('discount_id');
$export      = $this->getRequest()->getParam('export');
$params = array_filter(array(
            'privileges_sn'       	=> $privileges_sn,
            'privileges_no'       	=> $privileges_no,
            'sales_order_no'       	=> $sales_order_no,
            'status'       			=> $status,
            'start_date'       		=> $start_date,
            'end_date'       		=> $end_date,
            'staff_code'       		=> $staff_code,
            'staff_name'       		=> $staff_name,
            'discount_id'      		=> $discount_id,
            'action_frm' 			=> 'list'
            ));




$res= null;
if (isset($export) && $export){
    //OPPO
    $params['company_id']=1;
	$get_resule = $QStaffSalesOrder->exportPrivilegesStaffOrder($params);

    //ONEPLUS
	$params['company_id']=2;
    $get_resule_oneplus = $QStaffSalesOrder->exportPrivilegesStaffOrder($params);

    $res = array_merge($get_resule,$get_resule_oneplus);
    //echo "<pre>";print_r($get_resule);
    $this->_exportExcelPrivilegesStaffOrder($res);
}else{
    //OPPO
    $params['company_id']=1;
	$get_resule = $QStaffSalesOrder->fetchPagination($page, $limit, $total, $params);
    //ONEPLUS
    $params_oneplus['company_id']=2;
    $get_resule_oneplus = $QStaffSalesOrder->fetchPagination($page_oneplus, $limit, $total_oneplus, $params_oneplus);
    //echo "<pre>";print_r($get_resule_oneplus);die;
    $res = array_merge($get_resule,$get_resule_oneplus);
    
	$this->view->get_resule = $res;
}

/*$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST . 'sales/staff-sales-order-list/' . ($params ? '?' . http_build_query($params) .'&' : '?');
$this->view->offset = $limit * ($page - 1);*/


/*$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'sales/return-list/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);*/



//print_r($get_discount_type);

/*$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();

    $this->_helper->viewRenderer->setRender('partials/staff-sales-order-list');
}*/
$params['sort'] = $sort;
$params['desc'] = $desc;


$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST . 'sales/staff-sales-order-list/' . ($params ? '?' . http_build_query($params) .
    '&' : '?');
$this->view->params = $params;
$this->view->offset = $limit * ($page - 1);

//print_r($params);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if ($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();

    $this->_helper->viewRenderer->setRender('partials/staff-sales-order-list');
}

