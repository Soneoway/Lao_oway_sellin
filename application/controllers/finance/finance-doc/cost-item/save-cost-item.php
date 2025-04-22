<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

	$id 							= $this->getRequest()->getParam('id');
	$dis_id 						= $this->getRequest()->getParam('dis_id');
	$category_id 					= $this->getRequest()->getParam('category_id');
	$cost_name 						= $this->getRequest()->getParam('cost_name');
	$deposit_project 				= $this->getRequest()->getParam('deposit_project');
	$remind_code 					= $this->getRequest()->getParam('remind_code');
	$subject_code 					= $this->getRequest()->getParam('subject_code');
	$status 						= $this->getRequest()->getParam('status');
	$remark							= $this->getRequest()->getParam('remark');

	$userStorage = Zend_Auth::getInstance()->getStorage()->read();
	$db = Zend_Registry::get('db');

	$QCostitem = new Application_Model_CostItem();

	if($id){

		try {
			
			$db->beginTransaction();

			$data = array(
				'remind_code'		=> $remind_code,
				'subject_code'		=> $subject_code,
				'deposit_project'	=> $deposit_project,
				'cost_name'			=> $cost_name,
				'status'			=> $status,
				'remark'			=> $remark,
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $userStorage->id
			);

			$where = $QCostitem->getAdapter()->quoteInto('id = ?', $id);
			$QCostitem->update($data,$where);

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
				'd_id'				=> $dis_id,
				'remind_code'		=> $remind_code,
				'subject_code'		=> $subject_code,
				'category_id'		=> $category_id,
				'deposit_project'	=> $deposit_project,
				'cost_name'			=> $cost_name,
				'status'			=> $status,
				'remark'			=> $remark,
				'created_at'		=> date('Y-m-d H:i:s'),
				'created_by'		=> $userStorage->id
			);

			$QCostitem->insert($data);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit();

		} catch (Exception $e) {

			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;

		}
	}

	echo '<script>parent.location.href="/finance/cost-item"</script>';
	exit;

}

?>