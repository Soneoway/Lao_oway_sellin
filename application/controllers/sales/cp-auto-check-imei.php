<?php
$flashMessenger = $this->_helper->flashMessenger;

//print_r($_GET);die;
set_time_limit(0);
ini_set('memory_limit', -1);

$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QCPAutoCheckImei = new Application_Model_CPAutoCheckImei();
$db = Zend_Registry::get('db');
$date = date('Y-m-d H:i:s');
if ($this->getRequest()->getMethod() == 'GET'){

	$act      = $this->getRequest()->getParam('act');
	$lot_sn      = $this->getRequest()->getParam('lot_sn');
	$lot_number      = $this->getRequest()->getParam('lot_number');
	
	$QDistributor = new Application_Model_Distributor();
	$QGood          = new Application_Model_Good();
	/*$whereGood      = array();
	$whereGood[]    = $QGood->getAdapter()->quoteInto('cat_id = ?' , PHONE_CAT_ID);*/

	$select_good = $QGood->select();
    $select_good->from(array('u' => 'good'),array('u.id','u.name'));
    $select_good->where('u.cat_id=?',11);
    $select_good->order('name');
    $good = $QGood->fetchAll($select_good);
    //print_r($good);
    $this->view->goods = $good;

	if($act=='new'){
		$db->beginTransaction();

		$lot_sn = date ( 'YmdHis' ) . substr ( microtime (), 2, 4 );
        $lot_number=$QCPAutoCheckImei->getLotNumberImei_No($lot_sn);

		$data = array(
            'lot_sn'                => $lot_sn,
            'lot_number'            => $lot_number,
            'receive_date'          => $date,
            'total_imei'            => 0,
            'lot_status'            => 1,   //0=no process,1=process ,2=success
            'active_cn'             => 1,   //1=active 0= not active
            'status'                => 1,   //1=active 0= not active
            'remark'                => $remark,
            'create_by'             => $userStorage->id,
            'create_date'           => $date
        );

		$create_lot_number=$QCPAutoCheckImei->insert($data);
		if($create_lot_number==true){
            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $db->commit();

            echo '<script>parent.location.href="/sales/cp-auto-check-imei?act=edit&lot_sn='.$lot_sn.'&lot_number='.$lot_number.'"</script>';
			exit;
        }else{
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get CP Lot Number No, Please try again!');
        }

        
	}else{

		if ($lot_sn)
		{
		    $imei_return_check=null;$group_by_imei="";
		    $QCPAutoCheckImei= new Application_Model_CPAutoCheckImei();
		    if($lot_sn !=''){
		        $params = array_filter(array(
		            'lot_sn'       => $lot_sn,
		            'action_frm' => 'confirm'
		            ));
		        $imei_return_check = $QCPAutoCheckImei->getCPLotNumberImeiCheckAction($params);

		        if(!empty($imei_return_check))
		        {
	        		if($imei_return_check[0]['sub_d_id']=='1'){
	        			$group_by_imei="1";

	        			$where_sub = array();
		    			$where_sub[] = $QDistributor->getAdapter()->quoteInto('sub_d_id = ?', 1);
				        //$rs_sub_d_id = $QDistributor->fetchAll($where_sub, 'name');


				        $rs_sub_d_id = $QDistributor->fetchAll($where_sub,new Zend_Db_Expr(" title ASC"));

	        		}
				}
				
		        $imei_check=null;
			    $imei_check = $QCPAutoCheckImei->checkPriceProtectionImeiAutoCheckListAction($lot_sn,"salse",$group_by_imei);
			    //print_r($imei_check);
			    
			    $imei_check_confirm=null;
			    $imei_check_confirm = $QCPAutoCheckImei->getImeiLotConfirm($lot_sn);

			    $imei_check_log=null;
			    $imei_check_log = $QCPAutoCheckImei->check_imei_log($lot_sn);
			    //print_r($imei_check_log);
		    }

		    $this->view->sub_d_id = $rs_sub_d_id;
		    $this->view->imei_check = $imei_check;
		    $this->view->cp_imei = $imei_return_check;
		    $this->view->group_by_imei = $group_by_imei;
		    $this->view->imei_check_confirm = $imei_check_confirm;
		    $this->view->imei_check_log = $imei_check_log;
		}
	}

}
//$aa = $QCPAutoCheckImei->getFinanceGroup();
//print_r($aa);
$this->view->finance_group = $QCPAutoCheckImei->getFinanceGroup();
$this->view->lot_sn = $lot_sn;
$this->view->lot_number = $lot_number;

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;