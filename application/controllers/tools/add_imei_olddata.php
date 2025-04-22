<?php
set_time_limit(0);
ini_set('memory_limit', '200M');

    $flashMessenger = $this->_helper->flashMessenger;
	$messages = $flashMessenger->setNamespace('error')->getMessages();
	$messages_success = $flashMessenger->setNamespace('success')->getMessages();

if ($this->getRequest()->getMethod() == 'POST'){
	$params			 = $this->getRequest()->getParam('params');
	$imei        	 = $this->getRequest()->getParam('imei');
	$btn_lock		 = $this->getRequest()->getParam('btn_lock');// name button

	$params = array_filter(array(
		'params' 	=> $params,
		'imei'  	=> $imei,
		'btn_lock'	=> $btn_lock,
			));

	if(!$btn_lock) return;
	if($btn_lock){
		$params['btn_lock'] = $btn_lock;
			////error imei ซ้ำ	ใน DB
		$arr_imei = $_POST['imei'];
		$arr_imei = explode("\r\n", $arr_imei);
		// print_r($arr_imei); die;
		foreach($arr_imei as $imei){
			if($imei){
				
		        $where = array();
				$Qimei  	= new Application_Model_Imei();

				$where[] = $Qimei->getAdapter()->quoteInto('imei_sn =?', $imei);
				$where[] = $Qimei->getAdapter()->quoteInto('old_data =?',1);
				$err_imei = $Qimei->fetchRow($where);
				if($err_imei){
					 array_push($messages,'Imei ນີ້ເປັນ Old Data ຢູ່ເເລ້ວ!');
					 $this->view->messages = $messages;
					 return;
				}
				
			}
		}
		/// end error imei ซ้ำ ใน DB
		
		$check= array(); //array สำหรับการตรวจสอบ
		for ($i=0; $i<count($arr_imei); $i++) {
		  $check[$arr_imei[$i]]++;
		  if ($check[$arr_imei[$i]]>1){
		     array_push($messages,"Can not lock! เนื่องจากมี 
		     	Imei $arr_imei[$i] ซ้ำกัน");
		  	 $this->view->messages = $messages;
		  	 return;
		  }
		  
		}
			$userStorage    = Zend_Auth::getInstance()->getStorage()->read();
			$QImei = new Application_Model_Imei();
			
			$arr_imei = $_POST['imei'];
			$arr_imei = explode("\r\n", $arr_imei);
			foreach ($arr_imei as $imei) {

			$data      = array(
				'old_data' => 1,
				'set_olddata_by' => $userStorage->id,
				'set_olddata_at' => date('Y-m-d h:i:sa'),
			);
			$where     = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
            $QImei->update($data, $where);

				// $data = array(
				// 	'warehouse_id' => 216,
				// );

				// $where  = $QImei->getAdapter()->quoteInto('imei_sn =?', $imei);
				// $QImei->update($data, $where);

			}

			$flashMessenger = $this->_helper->flashMessenger;
			 $flashMessenger->setNamespace('success')->addMessage('Done!');
			 $this->_redirect('/tool/olddata');
	}

}

