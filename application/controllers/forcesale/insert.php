<?php

	$this->_helper->layout->disableLayout();
	$this->_helper->viewRenderer->setNoRender(TRUE);
$key_sn = date('YmdHis') . substr(microtime(), 2, 4);


$flashMessenger = $this->_helper->flashMessenger;

//         echo "<pre>";
// print_r($_POST);die;
	// $sort           = $this->getRequest()->getParam('sort', 'date_canceled');
	// $desc           = $this->getRequest()->getParam('desc', 1);
	// $page           = $this->getRequest()->getParam('page', 1);
	// $from           = $this->getRequest()->getParam('from');
	// $to             = $this->getRequest()->getParam('to');

	$name           			= $this->getRequest()->getParam('name');
	$key          				= $this->getRequest()->getParam('key');
	$create_date				= $this->getRequest()->getParam('create_date');
    $status        				= $this->getRequest()->getParam('status',0);
    $w_id            			= $this->getRequest()->getParam('w_id');
    $type      					= $this->getRequest()->getParam('type');
    $d_id    					= $this->getRequest()->getParam('d_id', date('d/m/Y'));
    $distributor_all           		= $this->getRequest()->getParam('distributor_all');
    $start_date      			= $this->getRequest()->getParam('start_date');
    $end_date      				= $this->getRequest()->getParam('end_date');
    $date      					= date('Y-m-d H:i:s');
    $g_id     					= $this->getRequest()->getParam('g_id');
    $g_id_num         			= $this->getRequest()->getParam('g_id_num');
    $g_gift_id     				= $this->getRequest()->getParam('g_gift_id');
    $g_gift_id_num         		= $this->getRequest()->getParam('g_gift_id_num');
    // $cancel              		= $this->getRequest()->getParam('cancel');
    // $d_id              			= $this->getRequest()->getParam('d_id');
    // $export              		= $this->getRequest()->getParam('export');

    $d_id   		= array_unique($d_id);
    $w_id			= array_unique($w_id);
    $g_id 			= array_unique($g_id);
    $g_gift_id 		= array_unique($g_gift_id);

	$userStorage = Zend_Auth::getInstance()->getStorage()->read();
 	
 	list( $day, $month, $year ) = explode('/', $start_date);
	// if (isset($day) and isset($month) and isset($year) )
 	 $params['start_date'] = $year.'-'.$month.'-'.$day.' 00:00:00';

	list( $day2, $month2, $year2 ) = explode('/', $end_date);
	// if (isset($day2) and isset($month2) and isset($year2) )	
 	 $params['end_date'] = $year2.'-'.$month2.'-'.$day2.' 23:59:59';


    $params['name'] 					= $name;
    $good['good_ids'] 					= $g_id;
    $good['num'] 						= $g_id_num;


    $params['status'] 					= $status;
    $params['order_type'] 					= $type;
    $params['create_date'] 				= $date;
    $params['create_by'] 				= $userStorage->id;
    $params['update_date'] 				= $date;
    $params['update_by'] 				= $userStorage->id;
    $params['campaign_id']  			= $key_sn;
    $params['distributor_all']			= $distributor_all;

    $details['g_id'] 					= $g_gift_id;
    $details['g_id_num'] 				= $g_gift_id_num;
    $detail['force_sale_id']  			= $key_sn;

    $warehouse['force_sale_id']  		= $key_sn;
    $warehouses['w_id'] 					= $w_id;

    $distributor['force_sale_id']  		= $key_sn;
    $distributors['d_id'] 				= $d_id;

    if(isset($key) && $key != ''){
    	$detail['force_sale_id']  			= $key;
    	$params['campaign_id']  			= $key;
    	$warehouse['force_sale_id']  		= $key;
    	$distributor['force_sale_id']  		= $key;
    }
    if(isset($create_date) && $create_date != ''){
    	$params['create_date']  			= $create_date;
    }


	$QForceSale  			= new Application_Model_ForceSale();
	$QForceSaleDetail  		= new Application_Model_ForceSaleDetail();
	$QForceSaleDistributor  = new Application_Model_ForceSaleDistributor();
	$QForceSaleWarehouse  	= new Application_Model_ForceSaleWarehouse();
	

	//insert

    if(isset($key) && $key != '')
    {
	$whereForceSale  			= $QForceSale->getAdapter()->quoteInto('campaign_id = ?', $key);
	$whereForceSaleDetail  		= $QForceSaleDetail->getAdapter()->quoteInto('force_sale_id = ?', $key);
	$whereForceSaleWarehouse  	= $QForceSaleWarehouse->getAdapter()->quoteInto('force_sale_id = ?', $key);
    $whereQForceSaleDistributor =[];
	$whereQForceSaleDistributor[] = $QForceSaleDistributor->getAdapter()->quoteInto('force_sale_id = ?', $key);

	$forceSaleDistributor 			= $QForceSaleDistributor->get_id($key);
		$QForceSale 			->delete($whereForceSale);
		$QForceSaleDetail		->delete($whereForceSaleDetail);
		$QForceSaleWarehouse	->delete($whereForceSaleWarehouse);

		$forceSaleDistributor 			= $QForceSaleDistributor->get_id($key);
		
		$d_id_old = [];
		foreach ($forceSaleDistributor as $key => $value) 
		{
			array_push($d_id_old,$value['d_id']);
		}

		//delect Distributor old 
		$deleteDistributor = array_diff($d_id_old,$d_id);
		if($deleteDistributor && $deleteDistributor != '')
		{
			foreach ($deleteDistributor as $id) {
				$whereQForceSaleDistributor[1] = $QForceSaleDistributor->getAdapter()->quoteInto('d_id = ?', $id);
				print_r($whereQForceSaleDistributor);
				$QForceSaleDistributor	->delete($whereQForceSaleDistributor);
				$whereQForceSaleDistributor[1] =	null;
			}
		}

		// insert Distributor New
		$insertDistributor = array_diff($d_id,$d_id_old);
		if($insertDistributor && $insertDistributor != '')
		{
			foreach ($insertDistributor as $id) {
				$distributor['d_id'] = $id;
				$QForceSaleDistributor  		 ->insert($distributor);
				$distributor['d_id'] = null;
			}
		}
		
	}

	else
	{
		foreach ($distributors['d_id']  as $key => $distributor['d_id'] ) {
			echo $distributor['d_id'].'<br>';
			$QForceSaleDistributor  		 ->insert($distributor);
		}
	}



		foreach ($good['good_ids'] as $key => $params['good_id']) {
			$params['num'] = $good['num'][$key];
			$QForceSale  			 ->insert($params);
		}

		foreach ($details['g_id']  as $key => $detail['g_id'] ) {
			$detail['g_id_num']  = $details['g_id_num'][$key];
			echo $detail['g_id_num'].'- -'.$detail['g_id'].'<br>';
			$QForceSaleDetail  		 ->insert($detail);
		}

		foreach ($warehouses['w_id']  as $key => $warehouse['w_id'] ) {
			echo $warehouse['w_id'].'<br>';
			$QForceSaleWarehouse  		 ->insert($warehouse);
		}
		


		// $flashMessenger->setNamespace('success')->addMessage('Done!');
		$flashMessenger->setNamespace('success')->addMessage("Success");
$this->_redirect('/force-sale');

