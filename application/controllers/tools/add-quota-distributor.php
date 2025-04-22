<?php
	// $sort           = $this->getRequest()->getParam('sort', 'date_canceled');
	// $desc           = $this->getRequest()->getParam('desc', 1);
	// $page           = $this->getRequest()->getParam('page', 1);
	// $from           = $this->getRequest()->getParam('from');
	$type             = $this->getRequest()->getParam('type');

    $id                     = $this->getRequest()->getParam('id');
	$sn              		= $this->getRequest()->getParam('sn');


	$limit = LIMITATION;
    $total = 0;


    // $params['sort'] 				= $sort;
    // $params['desc'] 				= $desc;
    // $params['sn'] 					= $sn;
    // $params['good_id'] 				= $good_id;
    // $params['good_color'] 			= $good_color;
    // $params['cat_id'] 				= $cat_id;
    // $params['cancel_at_form'] 		= $cancel_at_form;
    // $params['cancel_at_to']			= $cancel_at_to;
    // $params['cancel_delivery_form'] = $cancel_delivery_form;
    // $params['cancel_delivery_to'] 	= $cancel_delivery_to;
    // $params['cancel'] 				= $cancel;
    $params['type'] 				= $type;


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
    	$QWarehouses     = new Application_Model_Warehouse();
    	$QGoodCategory  = new Application_Model_GoodCategory();

        $warehouses 		= $QWarehouses->get_cache();
        $good_categories   	= $QGoodCategory->get_cache();

        $QForceSale  			= new Application_Model_ForceSale();
		$QForceSaleDetail  		= new Application_Model_ForceSaleDetail();
		$QForceSaleDistributor  = new Application_Model_ForceSaleDistributor();
		$QForceSaleWarehouse  	= new Application_Model_ForceSaleWarehouse();
        $QQuotaOppoByDistributor    = new Application_Model_QuotaOppoByDistributor();
		if(isset($sn)){
         
        
            
            $quotas = $QQuotaOppoByDistributor->getAddQuotalist($sn);
           
            
            
            $this->view->quota          = $quotas;

		}

		$this->view->warehouses      		= $warehouses;
        
	    $this->view->good_categories 		= $good_categories;	
	    $this->view->markets_sn  			= $order;
		$this->view->desc     				= $desc;
		$this->view->sort     				= $sort;
		$this->view->messages 				= $messages;
		$this->view->params   				= $params;
		$this->view->limit    				= $limit;
		$this->view->total    				= $total;
		$this->view->url      				= HOST.'delivery/order-cancel'.( $params ? '?'.http_build_query($params).'&' : '?' );
		$this->view->offset   				= $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();