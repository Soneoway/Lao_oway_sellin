<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

	$id 								= $this->getRequest()->getParam('id');
	$distributor_ids                    = $this->getRequest()->getParam('distributor_ids');
	$finance_client                     = $this->getRequest()->getParam('finance_client');
	$quota_type                     	= $this->getRequest()->getParam('quota_type');
	$quota                     			= $this->getRequest()->getParam('quota');
	$effective_from                     = $this->getRequest()->getParam('effective_from');
	$effective_to                     	= $this->getRequest()->getParam('effective_to');
	$remark                     		= $this->getRequest()->getParam('remark');

	$userStorage = Zend_Auth::getInstance()->getStorage()->read();
	$db = Zend_Registry::get('db');

	$QCreditLimit = new Application_Model_CreditLimit();

	if($id){

		try {

			$db->beginTransaction();

			$data = array(
				'finance_client_id'		=> $finance_client,
				'quota_type'			=> $quota_type,
				'quota'					=> $quota,
				'start_date'			=> $effective_from,
				'end_date'				=> $effective_to,
				'remark'				=> $remark,
				'updated_by'			=> $userStorage->id,
				'updated_at'			=> date('Y-m-d H:i:s')
			);

			$where = $QCreditLimit->getAdapter()->quoteInto('id =?',$id);
			$QCreditLimit->update($data,$where);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit();
			
		} catch (Exception $e) {
			
			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;

		}

	}else{

		try {

			$db->beginTransaction();

			$data = array(
				'd_id' 					=> $distributor_ids,
				'finance_client_id'		=> $finance_client,
				'quota_type'			=> $quota_type,
				'quota'					=> $quota,
				'start_date'			=> $effective_from,
				'end_date'				=> $effective_to,
				'status'				=> 1,
				'remark'				=> $remark,
				'created_by'			=> $userStorage->id,
				'created_at'			=> date('Y-m-d H:i:s')
			);

			$QCreditLimit->insert($data);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit();

		} catch (Exception $e) {

			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;

		}
	}

	echo '<script>parent.location.href="/finance/credit-limit"</script>';
	exit;

}

?>