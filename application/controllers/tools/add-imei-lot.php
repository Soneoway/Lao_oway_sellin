<?php
set_time_limit(0);
ini_set('memory_limit', '200M');

    $flashMessenger = $this->_helper->flashMessenger;
	$messages = $flashMessenger->setNamespace('error')->getMessages();
	$messages_success = $flashMessenger->setNamespace('success')->getMessages();

if ($this->getRequest()->getMethod() == 'POST'){

	$lot_sn        	 = $this->getRequest()->getParam('lot_sn');
	$lot_name        	 = $this->getRequest()->getParam('lot_name');
	$imei_list        	 = $this->getRequest()->getParam('imei');
	$btn_save		 = $this->getRequest()->getParam('btn_save');// name button

	$params = array_filter(array(
		'lot_name'          => $lot_name,
		'imei'          => $imei_list
	));

	$this->view->params = $params;
	$err_array_imei = null;

	$QimeiLot  	= new Application_Model_ImeiLot();
	$Qimei  	= new Application_Model_Imei();
	if(!$btn_save) return;
	if($btn_save){
			////error imei ซ้ำ	ใน DB
		$imeiArray = explode("\r\n", $imei_list);
		$arr_imei = array_filter($imeiArray);
		//print_r($arr_imei); die;

		$where = array();
		$where[] = $QimeiLot->getAdapter()->quoteInto('lot_name =?', $lot_name);
		$where[] = $QimeiLot->getAdapter()->quoteInto('status_imei =?',1);
		$err_lot_name = $QimeiLot->fetchRow($where);
		if($err_lot_name){
			array_push($messages,'LOT Name : "'.$lot_name.'" already exists!');
			$this->view->messages = $messages;
			return;
		}

		foreach($arr_imei as $imei){
			if($imei){
		        $where = array();
				$where[] = $Qimei->getAdapter()->quoteInto('imei_sn =?', $imei);

				$err_imei_ex = $Qimei->fetchRow($where);
				if(!$err_imei_ex){
					$err_array_imei_ex[] = array(
						'lot_name'  	=> $lot_name,
						'imei'  	=> $imei,
					);					 
				}
			}
		}

		if($err_array_imei_ex){
			foreach($err_array_imei_ex as $imei_error){
				array_push($messages,'Imei '.$imei_error['imei'].' Not in the system.!');
			}
			$this->view->messages = $messages;
			return;
		}

		foreach($arr_imei as $imei){
			if($imei){
		        $where = array();
				$where[] = $QimeiLot->getAdapter()->quoteInto('imei_sn =?', $imei);
				$where[] = $QimeiLot->getAdapter()->quoteInto('status_imei =?',1);
				$err_imei = $QimeiLot->fetchRow($where);
				if($err_imei){
					$err_array_imei[] = array(
						'lot_name'  	=> $lot_name,
						'imei'  	=> $imei,
					);					 
				}
			}
		}

		if($err_array_imei_){
			foreach($err_array_imei as $imei_error){
				array_push($messages,'Imei '.$imei_error['imei'].' This has already been recorded.!');
			}
			$this->view->messages = $messages;
			return;
		}

		/// end error imei ซ้ำ ใน DB
		$check= array(); //array สำหรับการตรวจสอบ
		for ($i=0; $i<count($arr_imei); $i++) {
		  $check[$arr_imei[$i]]++;
		  if ($check[$arr_imei[$i]]>1){
		     array_push($messages,"Can not Insert! Because Imei $arr_imei[$i] duplicate");
		  	 $this->view->messages = $messages;
		  	 return;
		  }
		}

		$userStorage    = Zend_Auth::getInstance()->getStorage()->read();
		$QimeiLot  	= new Application_Model_ImeiLot();

		if (!$lot_sn){
            $lot_sn = date('YmdHis') . substr(microtime(), 2, 4);
        }

		//$arr_imei = $_POST['imei'];
		//$arr_imei = explode("\r\n", $imei_list);
		//print_r($arr_imei); die;
		// $arr_imei = array_unique($arr_imei);
		foreach ($arr_imei as $imei){
			$QimeiLot->insert(array(
				'lot_sn' 		=> $lot_sn,
				'lot_name' 		=> $lot_name,
				'imei_sn' 		=> $imei,
				'created_by'	=> $userStorage->id,
				'created_date'	=> date('Y-m-d H:i:s'),
				'status_imei'   => 1,
			));
		}
		 array_push($messages_success,'Done');
		 $this->view->messages_success = $messages_success;

	}

}

