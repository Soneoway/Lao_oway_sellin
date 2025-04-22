<?php
$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');

$QFinanceWarehouseGroup = new Application_Model_FinanceWarehouseGroup();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$db = Zend_Registry::get('db');

if($id) {

	$warehouseGroupRowSet = $QFinanceWarehouseGroup->find($id);
	$warehouseGroup       = $warehouseGroupRowSet->current();

	if (!$warehouseGroup) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດກຸ່ມການເງິນບໍ່ຖຶກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/finance-warehouse-group');
	}

	try {

		$db->beginTransaction();

		$data = array(
			'updated_at' 	=> date('Y-m-d H:i:s'),
			'updated_by'	=> $userStorage->id,
			'del_by'		=> $userStorage->id,
			'del_at'		=> date('Y-m-d H:i:s'),
			'status'		=> 2
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

	echo '<script>parent.location.href="/finance/finance-warehouse-group"</script>';
	exit;

}



?>