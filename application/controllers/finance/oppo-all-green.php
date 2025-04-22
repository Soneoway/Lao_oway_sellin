<?php

		$sort           = $this->getRequest()->getParam('sort', 'uploaded_at');
		$desc           = $this->getRequest()->getParam('desc', 1);
		$page           = $this->getRequest()->getParam('page', 1);
		$real_file_name = $this->getRequest()->getParam('real_file_name');
		$from           = $this->getRequest()->getParam('from');
		$to             = $this->getRequest()->getParam('to');

		$year 			= $this->getRequest()->getParam('year');
        $air_number 	= $this->getRequest()->getParam('air_number');
        $d_id 			= $this->getRequest()->getParam('d_id');
        $cn 			= $this->getRequest()->getParam('cn');

		$created_at_from   = $this->getRequest()->getParam('created_at_from', date('d/m/Y', strtotime('0 day')));
		$created_at_to   = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));

		$export  = $this->getRequest()->getParam('export', 0);

		$limit = LIMITATION;

        $params = array(
            'year'        		=> $year,
            'air_number'        => $air_number,
            'd_id'      		=> $d_id,
            'cn'        		=> $cn,
            'created_at_to'   	=> $created_at_to,
    		'created_at_from' 	=> $created_at_from,
        );

			$QDistributor  = new Application_Model_Distributor();
            $distributors  = $QDistributor->get_cache();
            $this->view->distributors  = $distributors;

			$this->_helper->viewRenderer->setRender('oppo-all-green');

	$QOppoGreenAll   = new Application_Model_OppoAllGreenRewardCn();

	if ( isset($export) && $export == 1 ) {
	    //My_Report::preventExport();
	    $oppoAllGreen = $QOppoGreenAll->fetchPagination($page, 1000000, $total, $params);
	    // print_r($oppoAllExport);die;
	    $this->_exportExcelOppoAllGreen($oppoAllGreen);
	}else {
		$oppoAllGreen = $QOppoGreenAll->fetchPagination($page, $limit, $total, $params);
	}

	
		$this->view->oppoAllGreen =  $oppoAllGreen;
		$this->view->desc     = $desc;
		$this->view->sort     = $sort;
		$this->view->messages = $messages;
		$this->view->params   = $params;
		$this->view->limit    = $limit;
		$this->view->total    = $total;
		$this->view->url      = HOST.'finance/oppo-all-green/'.( $params ? '?'.http_build_query($params).'&' : '?' );
		$this->view->offset   = $limit*($page-1);



?>