<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

	$id 							= $this->getRequest()->getParam('id');
	$dis_id 						= $this->getRequest()->getParam('dis_id');
	$finance_warehouse_group 		= $this->getRequest()->getParam('finance_warehouse_group');
	$remark 						= $this->getRequest()->getParam('remark');


	$userStorage = Zend_Auth::getInstance()->getStorage()->read();
	$db = Zend_Registry::get('db');

	$QFinanceWarehouseGroup = new Application_Model_FinanceWarehouseGroup();

	if($id){

		try {

			$db->beginTransaction(); 

			$data = array(
				'group_name'		=> $finance_warehouse_group,
				'updated_at'		=> date('Y-m-d H:m:s'),
				'updated_by'		=> $userStorage->id,
				'remark'			=> $remark
			);

			$where = $QFinanceWarehouseGroup->getAdapter()->quoteInto('id = ?', $id);
			$QFinanceWarehouseGroup->update($data,$where);

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
				'd_id'			=> $dis_id,
				'group_name'	=> $finance_warehouse_group,
				'status'		=> 1,
				'remark'		=> $remark,
				'created_at'	=> date('Y-m-d H:m:s'),
				'created_by'	=> $userStorage->id
			);


			$QFinanceWarehouseGroup->insert($data);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit(); 

		} catch (Exception $e) {

			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;

		}
	}

	echo '<script>parent.location.href="/finance/finance-warehouse-group"</script>';
	exit;

}

?>