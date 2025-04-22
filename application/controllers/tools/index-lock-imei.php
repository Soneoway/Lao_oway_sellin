<?php

set_time_limit(0);
ini_set('memory_limit', '200M');

	$params 	   = $this->getRequest()->getParam('params');
	$sort          = $this->getRequest()->getParam('sort', '');
	$page          = $this->getRequest()->getParam('page', 1);
	$desc          = $this->getRequest()->getParam('desc', 1);
	$export 	   = $this->getRequest()->getParam('export');

	$imei	       = $this->getRequest()->getParam('imei');

	$limit = LIMITATION;
	$total = 0;
	$QLockimei  	= new Application_Model_LockImei();


	$params = array_filter(array(
		'imei'          => $imei

	));

	$imei = explode("\r\n", $imei);
	$lockimeis		= $QLockimei->getlock_imei($page, $limit, $total, $params);



	if(isset($export) && $export){
		$data		= $QLockimei->getlock_imei($page, null, $total, $params);
		$this->_exportimeilock($data);
	}

	$this->view->lockimeis = $lockimeis;
	$this->view->desc   = $desc;
	$this->view->sort   = $sort;
	$this->view->params = $params;
	$this->view->limit  = $limit;
	$this->view->total  = $total;
	$this->view->url    = HOST.'tool/lockimei/'.( $params ? '?'.http_build_query($params).'&' : '?' );
	$this->view->offset = $limit*($page-1);



