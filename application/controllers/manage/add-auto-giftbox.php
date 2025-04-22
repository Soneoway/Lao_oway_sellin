<?php

$flashMessenger = $this->_helper->flashMessenger;

$QAG = new Application_Model_AutoGiftbox();

$QGood = new Application_Model_Good();
$this->view->goods_cached = $QGood->get_cache();

$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

$QGoodColor  = new Application_Model_GoodColor();
$this->view->goodColors = $QGoodColor->get_cache();

$id 		= $this->getRequest()->getParam('id');
$cat_id 	= $this->getRequest()->getParam('cat_id');
$good_id 	= $this->getRequest()->getParam('good_id');
$checkbox_allday 	= $this->getRequest()->getParam('checkbox_allday');
$auto_giftbox_start_date 	= $this->getRequest()->getParam('auto_giftbox_start_date');
$auto_giftbox_end_date 	    = $this->getRequest()->getParam('auto_giftbox_end_date');
$cat_id_give 				= $this->getRequest()->getParam('cat_id_give');
$good_id_give 				= $this->getRequest()->getParam('good_id_give');
$good_color_give 			= $this->getRequest()->getParam('good_color_give');

$cat_id      = PHONE_CAT_ID;
$userStorage = Zend_Auth::getInstance()->getStorage()->read();

if($cat_id){
	$where = $QGood->getAdapter()->quoteInto('cat_id = ?', $cat_id);
    $goods_cat = $QGood->fetchAll($where, 'name');
    $this->view->goods_cat = $goods_cat;
}

	$params = array(
			'auto_giftbox_start_date' => $auto_giftbox_start_date,
			'auto_giftbox_end_date'   => $auto_giftbox_end_date,
			'cat_id'	=> $cat_id,
			'good_id'	=> $good_id,
			'good_id_give'	=> $good_id_give,
			'good_color_give' => $good_color_give,
		  );
	$this->view->params = $params;

if ($this->getRequest()->getMethod() == 'POST'){

	if($good_id == '' || $good_id_give == '' || $good_color_give == ''){
		
		$flashMessenger->setNamespace('error')->addMessage('Please check input!');
        $this->_redirect(HOST."manage/add-auto-giftbox");
	}

	if(isset($checkbox_allday)){
	   $checkbox_allday = 1;
	}
	if($checkbox_allday == ''){
	   $checkbox_allday = 0;
	}
	if($auto_giftbox_start_date == ''){
		$auto_giftbox_start_date = null;
	}
	if($auto_giftbox_end_date == ''){
		$auto_giftbox_end_date = null;
	}
	
	$data = array(
		'cat_id'  		  => $cat_id,
		'good_id'		  => $good_id,
		'gift_cat_id'  	  => $cat_id_give,
		'gift_good_id'	  => $good_id_give,
		'gift_good_color' => $good_color_give,
		'all_date'        => $checkbox_allday,
		'start_date'      => $auto_giftbox_start_date,
		'end_date'        => $auto_giftbox_end_date.' 23:59:59',
		'status'		  => 1,
		'created_date'    => date('Y-m-d H:i:s'),
		'created_by'      => $userStorage->id
	);

	if($data['end_date'] == ' 23:59:59'){
	   $data['end_date'] = null;
	}

	$where =array();
	$where[] = $QAG->getAdapter()->quoteInto('good_id =?',$good_id);
	$where[] = $QAG->getAdapter()->quoteInto('gift_good_id =?',$good_id_give);
	$where[] = $QAG->getAdapter()->quoteInto('all_date =?',$checkbox_allday);
	$where[] = $QAG->getAdapter()->quoteInto('start_date =?',$auto_giftbox_start_date);
	$where[] = $QAG->getAdapter()->quoteInto('end_date =?',$auto_giftbox_end_date);
	$where[] = $QAG->getAdapter()->quoteInto('status =?',1);
	$check_giftbox = $QAG->fetchRow($where);
	
	if($check_giftbox > 0){

		$flashMessenger->setNamespace('error')->addMessage('Error! มีการ add-auto-giftbox ซ้ำ');
		$this->_redirect(HOST."manage/add-auto-giftbox");
	}else{

		if($id){ // edit update

			$end_date = explode(" ", $auto_giftbox_end_date);
	
			$where = $QAG->getAdapter()->quoteInto('id = ?', $id);
			$data2 = array(
				'cat_id'  		  => $cat_id,
				'good_id'		  => $good_id,
				'gift_cat_id'  	  => $cat_id_give,
				'gift_good_id'	  => $good_id_give,
				'gift_good_color' => $good_color_give,
				'all_date'        => $checkbox_allday,
				'start_date'      => $auto_giftbox_start_date,
				'end_date'        => $end_date[0].' 23:59:59',
				'status'		  => 1,
				// 'created_date'    => date('Y-m-d H:i:s'),
				// 'created_by'      => $userStorage->id
				'update_date'     => date('Y-m-d H:i:s'),
				'update_by' 	  => $userStorage->id
			);

			if($data2['end_date'] == ' 23:59:59'){
			   $data2['end_date'] = null;
			}
			// print_r($data2); die;
			if(isset($data2['all_date']) && $data2['all_date'] == 1 && isset($data2['start_date']) && $data2['start_date'] != null && isset($data2['end_date']) && $data2['end_date'] != null)
			{

			$flashMessenger->setNamespace('error')->addMessage('กรุณาเลือกระยะเวลาอย่างใดอย่างหนึ่ง');
				$this->_redirect(HOST."manage/add-auto-giftbox?id=".$id);
			}

			$QAG->update($data2, $where);
			$this->_redirect(HOST."manage/auto-giftbox");
		}else{ //insert

			$QAG->insert($data);
			$flashMessenger->setNamespace('success')->addMessage('Done!');
			$this->_redirect(HOST."manage/add-auto-giftbox");
		}
	}
	
} //end post

//edit
if($id){
	$Rowset = $QAG->find($id);
	$auto_giftbox = $Rowset->current();
	$this->view->auto_giftbox = $auto_giftbox;
}

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;
