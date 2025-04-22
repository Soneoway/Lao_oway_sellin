<?php


		$id 		= $this->getRequest()->getParam('id',null);
		$page 		= $this->getRequest()->getParam('page',1);
		// $limit 		= LIMITATION;
		$limit 		= 50;
		$sort 		= $this->getRequest()->getParam('sort','b.id');
		$desc    	= $this->getRequest()->getParam('desc', 1);
		$total 		= 0;
		
		$params 	= array(
				
				'id'	=>	$id,
				'sort'	=>	$sort,
				'desc'	=>	$desc,
		
		);
		$QDistributorFile 		= new Application_Model_DistributorFile();
    	$QDistributor = new Application_Model_Distributor();
		$list 		= $QDistributorFile->fetchPagination($page, $limit, $total, $params);
		$where = $QDistributor->getAdapter()->quoteInto('id = ?', $id);
		$distributor = $QDistributor->fetchRow($where);
		// print_r($distributor);die;

		// print_r($list);
		$this->view->distributor_data   = $distributor;
		$this->view->list 				= $list;
		$this->view->distributor 		= $distributor['title'];
		$this->view->limit 				= $limit;
		$this->view->total 				= $total;
		$this->view->page 				= $page;
		$this->view->offset 			= $limit*($page-1);
		$this->view->params 			= $params;
		$this->view->sort 				= $sort;
		$this->view->desc 				= $desc;
		$this->view->url 				= HOST.'sales/distributor-document/'.( $params ? '?'.http_build_query($params).'&' : '?' );