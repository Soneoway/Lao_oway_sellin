<?php
	$sort           = $this->getRequest()->getParam('sort', 'date_canceled');
	$desc           = $this->getRequest()->getParam('desc', 1);
	$page           = $this->getRequest()->getParam('page', 1);
	$from           = $this->getRequest()->getParam('from');
	$to             = $this->getRequest()->getParam('to');

	$sn                			= $this->getRequest()->getParam('sn');
	$good_id           			= $this->getRequest()->getParam('good_id');
    $good_color        			= $this->getRequest()->getParam('good_color');
    $cat_id            			= $this->getRequest()->getParam('cat_id');
    $cancel_at_form    			= $this->getRequest()->getParam('cancel_at_form', date('d/m/Y'));
    $cancel_at_to      			= $this->getRequest()->getParam('cancel_at_to', date('d/m/Y', strtotime('-0 day')));
    $cancel_delivery_form     	= $this->getRequest()->getParam('cancel_delivery_form');
    $cancel_delivery_to         = $this->getRequest()->getParam('cancel_delivery_to');
    $cancel              		= $this->getRequest()->getParam('cancel');
    $d_id              			= $this->getRequest()->getParam('d_id');
    $export              		= $this->getRequest()->getParam('export');


	$created_at_from   = $this->getRequest()->getParam('created_at_from', date('d/m/Y', strtotime('0 day')));
	$created_at_to   = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));	

	$limit = LIMITATION;
    $total = 0;


    $params['sort'] 				= $sort;
    $params['desc'] 				= $desc;
    $params['sn'] 					= $sn;
    $params['good_id'] 				= $good_id;
    $params['good_color'] 			= $good_color;
    $params['cat_id'] 				= $cat_id;
    $params['cancel_at_form'] 		= $cancel_at_form;
    $params['cancel_at_to']			= $cancel_at_to;
    $params['cancel_delivery_form'] = $cancel_delivery_form;
    $params['cancel_delivery_to'] 	= $cancel_delivery_to;
    $params['cancel'] 				= $cancel;
    $params['d_id'] 				= $d_id;


$QGoodCategory  = new Application_Model_GoodCategory();
$QCancelOrder = new Application_Model_Market();

$good_categories   = $QGoodCategory->get_cache();

	if ( isset($export) && $export == 1 ) {
	    //My_Report::preventExport();
	    $order = $QCancelOrder->cancelOrder($page, 1000000, $total, $params);
	    // print_r($order);die;
	    $this->_exportExcelOrderCancel($order);
	}else {
		$order = $QCancelOrder->cancelOrder($page, $limit, $total, $params);
	}


	    $this->view->good_categories   = $good_categories;	
	    $this->view->markets_sn  = $order;
		$this->view->desc     = $desc;
		$this->view->sort     = $sort;
		$this->view->messages = $messages;
		$this->view->params   = $params;
		$this->view->limit    = $limit;
		$this->view->total    = $total;
		$this->view->url      = HOST.'delivery/order-cancel'.( $params ? '?'.http_build_query($params).'&' : '?' );
		$this->view->offset   = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();