<?php
$flashMessenger = $this->_helper->flashMessenger;

$page 		= $this->getRequest()->getParam('page',1);
$limit 		= LIMITATION;
$sort 		= $this->getRequest()->getParam('sort','p.create_date');
$desc    	= $this->getRequest()->getParam('desc', 1);
$total 		= 0;

//print_r($_GET);die;

$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QPreSalesOrder = new Application_Model_PreSalesOrder();
$db = Zend_Registry::get('db');
$date = date('Y-m-d H:i:s');

// if ($this->getRequest()->getMethod() == 'GET'){

	$act      = $this->getRequest()->getParam('act');
	$presales_sn      = $this->getRequest()->getParam('presales_sn');
	$presales_no      = $this->getRequest()->getParam('presales_no');
	$status      = $this->getRequest()->getParam('status');
	$start_date      = $this->getRequest()->getParam('start_date');
	$end_date      = $this->getRequest()->getParam('end_date');
	$sell_name      = $this->getRequest()->getParam('sell_name');
	$distributor_name      = $this->getRequest()->getParam('distributor_name');
	$export      = $this->getRequest()->getParam('export');
	$params = array_filter(array(
	            'presales_sn'       => $presales_sn,
	            'presales_no'       => $presales_no,
	            'status'       => $status,
	            'start_date'       => $start_date,
	            'end_date'       => $end_date,
	            'sell_name'       => $sell_name,
	            'distributor_name'       => $distributor_name,
	            'export'       => $export,
	            'action_frm' => 'list'
	            ));


if (isset($export) && $export) {
	$get_resule = $QPreSalesOrder->fetchPagination(null, null, $total, $params);
	//echo "<pre>";print_r($get_resule);
    $this->_exportSalesRequest($get_resule);
}else{
	$get_resule = $QPreSalesOrder->fetchPagination($page, $limit, $total, $params);
	$this->view->get_resule = $get_resule;
}

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'sales/pre-sales-order-list/' . ($params ? '?' . http_build_query($params) .
    '&' : '?');
$this->view->offset = $limit * ($page - 1);


/*	//$params['sort'] = $sort;
	//$params['desc'] = $desc;

	$get_resule = $QPreSalesOrder->fetchPagination($page, $limit, $total, $params);

	$this->view->get_resule = $get_resule;
	//$this->view->params = $params;
	//print_r($params);

	$this->view->desc   = $desc;
	$this->view->sort   = $sort;
	$this->view->params = $params;
	$this->view->limit  = $limit;
	$this->view->total  = $total;

	$this->view->url    = HOST.'sales/pre-sales-order-list/'.( $params ? '?'.http_build_query($params).'&' : '?' );

	$this->view->offset = $limit*($page-1);*/


//}


$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;