<?php
	$sort           = $this->getRequest()->getParam('sort', 'date_canceled');
	$desc           = $this->getRequest()->getParam('desc', 1);
	$page           = $this->getRequest()->getParam('page', 1);
	$from           = $this->getRequest()->getParam('from');
	$to             = $this->getRequest()->getParam('to');

	$campaign_id               	= $this->getRequest()->getParam('campaign_id');
	$campaign_name           	= $this->getRequest()->getParam('campaign_name');
    $start_date        			= $this->getRequest()->getParam('start_date');
    $end_start            		= $this->getRequest()->getParam('end_start');
    // $cancel_at_form    			= $this->getRequest()->getParam('cancel_at_form', date('d/m/Y'));
 //    $cancel_at_to      			= $this->getRequest()->getParam('cancel_at_to', date('d/m/Y', strtotime('-0 day')));
 //    $cancel_delivery_form     	= $this->getRequest()->getParam('cancel_delivery_form');
 //    $cancel_delivery_to         = $this->getRequest()->getParam('cancel_delivery_to');
 //    $cancel              		= $this->getRequest()->getParam('cancel');
 //    $d_id              			= $this->getRequest()->getParam('d_id');
 //    $export              		= $this->getRequest()->getParam('export');


	// $created_at_from   = $this->getRequest()->getParam('created_at_from', date('d/m/Y', strtotime('0 day')));
	// $created_at_to   = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));	

	$limit = LIMITATION;
    $total = 0;


    $params['campaign_id'] 		= $campaign_id ;
    $params['name'] 				= $campaign_name ;
    $params['start_date'] 			= $start_date ;
    $params['end_date'] 			= $end_date;
    // $params['good_color'] 			= $good_color;
    // $params['cat_id'] 				= $cat_id;
    // $params['cancel_at_form'] 		= $cancel_at_form;
    // $params['cancel_at_to']			= $cancel_at_to;
    // $params['cancel_delivery_form'] = $cancel_delivery_form;
    // $params['cancel_delivery_to'] 	= $cancel_delivery_to;
    // $params['cancel'] 				= $cancel;
    // $params['d_id'] 				= $d_id;


// $QGoodCategory  = new Application_Model_GoodCategory();
// $QCancelOrder = new Application_Model_Market();

// $good_categories   = $QGoodCategory->get_cache();

// 	if ( isset($export) && $export == 1 ) {
// 	    //My_Report::preventExport();
// 	    $order = $QCancelOrder->cancelOrder($page, 1000000, $total, $params);
// 	    // print_r($order);die;
// 	    $this->_exportExcelOrderCancel($order);
// 	}else {
// 		$order = $QCancelOrder->cancelOrder($page, $limit, $total, $params);
// 	}
	$QForceSale  			= new Application_Model_ForceSale();
	$forceSale =  $QForceSale->fetchPagination($page, $limit, $total, $params);
	// echo "<pre>";
	// print_r($forceSale);


	    $this->view->forceSale   = $forceSale;	
		$this->view->desc     = $desc;
		$this->view->sort     = $sort;
		$this->view->messages = $messages;
		$this->view->params   = $params;
		$this->view->limit    = $limit;
		$this->view->total    = $total;
		$this->view->url      = HOST.'force-sale'.( $params ? '?'.http_build_query($params).'&' : '?' );
		$this->view->offset   = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();