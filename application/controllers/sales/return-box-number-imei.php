<?php
$flashMessenger = $this->_helper->flashMessenger;

//print_r($_GET);
//die;


$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QReturnBoxNumber = new Application_Model_ReturnBoxNumber();
$QDistributor = new Application_Model_Distributor();

$db = Zend_Registry::get('db');
$date = date('Y-m-d H:i:s');
if ($this->getRequest()->getMethod() == 'GET'){

	$act      = $this->getRequest()->getParam('act');
	$box_sn      = $this->getRequest()->getParam('box_sn');
	if($act=='new'){
		$db->beginTransaction();

		$box_sn = date ( 'YmdHis' ) . substr ( microtime (), 2, 4 );
        $box_number=$QReturnBoxNumber->getReturnBoxNumberImei_No($box_sn);

		$data = array(
            'box_sn'                => $box_sn,
            'box_number'            => $box_number,
            'receive_date'          => $date,
            'total_imei'            => 0,
            'box_status'            => 1,   //0=no process,1=process ,2=success
            'active'                => 1,   //1=active 0= not active
            'remark'                => $remark,
            'create_by'             => $userStorage->id,
            'create_date'           => $date
        );
		$create_box_number=$QReturnBoxNumber->insert($data);
		if($create_box_number==true){
            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $db->commit();

            echo '<script>parent.location.href="/sales/return-box-number-imei?act=edit&box_sn='.$box_sn.'"</script>';
			exit;
        }else{
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Return Box Number No, Please try again!');
        }

        
	}else{
		if ($box_sn)
		{
		    $imei_return_check=null;
		    $QReturnBoxNumberImei = new Application_Model_ReturnBoxNumberImei();
		    if($box_sn !=''){
		        $params = array_filter(array(
		            'box_sn'       => $box_sn,
		            'action_frm' => 'staff_confirm'
		            ));
		        $imei_return_check = $QReturnBoxNumberImei->getReturnBoxNumberImeiCheckAction($params);

		        $where_sub = array();
    			$where_sub[] = $QDistributor->getAdapter()->quoteInto('sub_d_id = ?', 1);
		        $rs_sub_d_id = $QDistributor->fetchAll($where_sub, 'name');
		    }

		    $where = array();
		    $QWarehouse = new Application_Model_Warehouse();
		    $select_group = $db->select()
		        ->from(array('u' => 'warehouse_group_user'),array('u.warehouse_id'))
		        ->where('u.user_id=?',$userStorage->id);
		    $result_group = $db->fetchAll($select_group);
		    $warehouse_id = "";
		    if ($result_group){
		        foreach ($result_group as $to) {
		            $warehouse_id .= $to['warehouse_id'].',';
		        }

		        $where[] = $QWarehouse->getAdapter()->quoteInto('id in('.rtrim($warehouse_id, ',').')', null);
		    }

		    //print_r($imei_return_check);
		    $this->view->warehouses = $QWarehouse->fetchAll($where, 'name');
		    $this->view->box_imei = $imei_return_check;
		    $this->view->sub_d_id = $rs_sub_d_id;
		}
	}

}

$this->view->box_sn = $box_sn;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;