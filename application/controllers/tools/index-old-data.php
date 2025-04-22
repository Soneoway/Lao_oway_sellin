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

	$params = array_filter(array(
		'imei'          => $imei,
		'export'		=> $export

	));

	$imei = explode("\r\n", $imei);

	$QImei = new Application_Model_Imei();
	$olddata		= $QImei->getImeiOlddata($page, $limit, $total, $params);

	if($export == 1){
		$this-> _exportExcelOldData($olddata);
	}


	$this->view->olddata = $olddata;
	$this->view->desc   = $desc;
	$this->view->sort   = $sort;
	$this->view->params = $params;
	$this->view->limit  = $limit;
	$this->view->total  = $total;
	$this->view->url    = HOST.'tool/olddata/'.( $params ? '?'.http_build_query($params).'&' : '?' );
	$this->view->offset = $limit*($page-1);



