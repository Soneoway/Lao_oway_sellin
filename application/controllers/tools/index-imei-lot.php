<?php

set_time_limit(0);
ini_set('memory_limit', '200M');

	//echo "AA";die;
	//print_r($_GET);
	$lot_name 	   = $this->getRequest()->getParam('lot_name');
	$sort          = $this->getRequest()->getParam('sort', '');
	$page          = $this->getRequest()->getParam('page', 1);
	$desc          = $this->getRequest()->getParam('desc', 1);
	$imei	       = $this->getRequest()->getParam('imei');

	$limit = LIMITATION;
	$total = 0;

	$params = array_filter(array(
		'lot_name'          => $lot_name,
		'imei'          => $imei
	));

	//$imei = explode("\r\n", $imei);

	$QimeiLot  	= new Application_Model_ImeiLot();
	$imeilot		= $QimeiLot->getImei_lot($page, $limit, $total, $params);
	$this->view->imeilot = $imeilot;

	$this->view->desc   = $desc;
	$this->view->sort   = $sort;
	$this->view->params = $params;
	$this->view->limit  = $limit;
	$this->view->total  = $total;
	$this->view->url    = HOST.'tool/imei-lot/'.( $params ? '?'.http_build_query($params).'&' : '?' );
	$this->view->offset = $limit*($page-1);



