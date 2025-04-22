<?php
$flashMessenger = $this->_helper->flashMessenger;

$id 				= $this->getRequest()->getParam('id');
$action_type 		= $this->getRequest()->getParam('action_type');
$remark				= $this->getRequest()->getParam('remark');

$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$db = Zend_Registry::get('db');

$QSupportFund = new Application_Model_SupportFund();
$QContactDetail = new Application_Model_ContactDetail();
$QDistributior = new Application_Model_Distributor();
$QStore = new Application_Model_Store();

if($id){

	$supportRowSet = $QSupportFund->find($id);
	$supportFund       = $supportRowSet->current();

	if (!$supportFund) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍ່ຖຶກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/support-fund-management');
	}


	if(isset($action_type) && $action_type == 3) {

		try {
			$db->beginTransaction();

			$data = array(
				'status'			=> 2,
				'approved_remark'	=> $remark,
				'approved_at'		=> date('Y-m-d H:i:s'),
				'approved_by'		=> $userStorage->id
			);

			$where = $QSupportFund->getAdapter()->quoteInto('id =?',$id);
			$QSupportFund->update($data, $where);

			$data_contact_detail = array(
				'status'			=> 2,
				'bill_date'			=> date('Y-m-d H:i:s'),
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $userStorage->id
			);

			$where_contact = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$supportFund->doc_no);
			$QContactDetail->update($data_contact_detail,$where_contact);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit();
			
		} catch (Exception $e) {
			
			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;
		}

		echo '<script>parent.location.href="/finance/support-fund-management"</script>';
		exit;
	}elseif(isset($action_type) && $action_type == 4) {

		try {

			$db->beginTransaction();

			$data = array(
				'status' 			=> 3,
				'approved_remark'	=> null,
				'approved_at'		=> null,
				'approved_by'		=> null,
				'review_at' 		=> date('Y-m-d H:i:s'),
				'review_by'			=> $userStorage->id,
			);

			$where = $QSupportFund->getAdapter()->quoteInto('id =?',$id);
			$QSupportFund->update($data, $where);

			$data_contact_detail = array(
				'status'			=> 1,
				'bill_date'			=> null,
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $userStorage->id
			);

			$where_contact = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$supportFund->doc_no);
			$QContactDetail->update($data_contact_detail,$where_contact);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit();
			
		} catch (Exception $e) {

			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;
			
		}

		echo '<script>parent.location.href="/finance/support-fund-management"</script>';
		exit;
		
	}elseif(isset($action_type) && $action_type == 5) {

		try {

			$db->beginTransaction();

			$data = array(
				'status' 		=> 4,
				'review_at'		=> null,
				'review_by'		=> null,
				'updated_at' 	=> date('Y-m-d H:i:s'),
				'updated_by'	=> $userStorage->id,
			);

			$where = $QSupportFund->getAdapter()->quoteInto('id =?',$id);
			$QSupportFund->update($data, $where);

			$where_contact = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$supportFund->doc_no);
			$QContactDetail->delete($where_contact);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit();
			
		} catch (Exception $e) {

			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;
			
		}

		echo '<script>parent.location.href="/finance/support-fund-management"</script>';
		exit;

	}

}

?>