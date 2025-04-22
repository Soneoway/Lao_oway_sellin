<?php
$flashMessenger = $this->_helper->flashMessenger;

$id 				= $this->getRequest()->getParam('id');
$action_type 		= $this->getRequest()->getParam('action_type');
$remark				= $this->getRequest()->getParam('remark');

$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$db = Zend_Registry::get('db');

$QCreditLimit = new Application_Model_CreditLimit();

if($id){

	$creditRowSet = $QCreditLimit->find($id);
	$creditLimit       = $creditRowSet->current();

	if (!$creditLimit) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍ່ຖຶກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/credit-limit');
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

			$where = $QCreditLimit->getAdapter()->quoteInto('id =?',$id);
			$QCreditLimit->update($data, $where);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit();
			
		} catch (Exception $e) {
			
			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;

		}

		echo '<script>parent.location.href="/finance/credit-limit"</script>';
		exit;

	}elseif(isset($action_type) && $action_type == 4) {

		try {

			$db->beginTransaction();

			$data = array(
				'status' 			=> 3,
				'approved_remark'	=> null,
				'approved_at'		=> null,
				'approved_by'		=> null,
				'review_date' 		=> date('Y-m-d H:i:s'),
				'review_by'			=> $userStorage->id,
			);

			$where = $QCreditLimit->getAdapter()->quoteInto('id =?',$id);
			$QCreditLimit->update($data, $where);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit();
			
		} catch (Exception $e) {

			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;
			
		}

		echo '<script>parent.location.href="/finance/credit-limit"</script>';
		exit;

	}elseif(isset($action_type) && $action_type == 5) {

		try {

			$db->beginTransaction();

			$data = array(
				'status' 		=> 4,
				'review_date'	=> null,
				'review_by'		=> null,
				'updated_at' 	=> date('Y-m-d H:i:s'),
				'updated_by'	=> $userStorage->id,
			);

			$where = $QCreditLimit->getAdapter()->quoteInto('id =?',$id);
			$QCreditLimit->update($data, $where);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit();
			
		} catch (Exception $e) {

			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;
			
		}

		echo '<script>parent.location.href="/finance/credit-limit"</script>';
		exit;

	}

}

?>