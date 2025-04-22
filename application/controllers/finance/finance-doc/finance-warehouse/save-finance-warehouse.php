<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

	$id 							= $this->getRequest()->getParam('id');
	$dis_id 						= $this->getRequest()->getParam('dis_id');
	$mnemonic_code 					= $this->getRequest()->getParam('mnemonic_code');
	$warehouse_id 					= $this->getRequest()->getParam('warehouse_id');
	$status 						= $this->getRequest()->getParam('status');
	$app_consignment 				= $this->getRequest()->getParam('app_consignment');
	$account_org 					= $this->getRequest()->getParam('account_org');
	$finance_warehouse_group 		= $this->getRequest()->getParam('finance_warehouse_group');
	$finance_warehouse_name 		= $this->getRequest()->getParam('finance_warehouse_name');
	$remark 						= $this->getRequest()->getParam('remark');

	$userStorage = Zend_Auth::getInstance()->getStorage()->read();
	$db = Zend_Registry::get('db');

	$QFinanceWarehouse = new Application_Model_FinanceWarehouse();
	$QClientCode = new Application_Model_ClientCode();

	if($id) {

		try {
			$db->beginTransaction();

			$data = array(
				'name'				=> $finance_warehouse_name,
				'mnemonic_code'		=> $mnemonic_code,
				'account_org'		=> $account_org,
				'finance_wh_group'	=> $finance_warehouse_group,
				'status'			=> $status,
				'app_consigment'	=> $app_consignment,
				'warehouse_id'		=> $warehouse_id,
				'remark'			=> $remark,
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $userStorage->id
			); 

			$where = $QFinanceWarehouse->getAdapter()->quoteInto('id = ?', $id);
			$QFinanceWarehouse->update($data,$where);

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

			$cusCode 	  	= $QClientCode->find(6);
		$insertCode 	= $cusCode[0]['next_code']; // ລະຫັດປະຈຸບັນ ( ຕົວອັກສອນນຳໜ້າ + ຕົວເລກ )
		$running_next	= $cusCode[0]['running_next']; // ລະຫັດຕົວເລກປະຈຸບັນ
		$running_now	= $cusCode[0]['running_now']; // ລະຫັດຕົວເລກກ່ອນໜ້າ
		$prefix			= $cusCode[0]['prefix']; // ໂຕອັນສອນນຳໜ້າ

		$data = array(
			'code'				=> $insertCode,
			'd_id'				=> $dis_id,
			'name'				=> $finance_warehouse_name,
			'mnemonic_code'		=> $mnemonic_code,
			'account_org'		=> $account_org,
			'finance_wh_group'	=> $finance_warehouse_group,
			'status'			=> $status,
			'app_consigment'	=> $app_consignment,
			'warehouse_id'		=> $warehouse_id,
			'remark'			=> $remark,
			'created_at'		=> date('Y-m-d H:i:s'),
			'created_by'		=> $userStorage->id
		);


		$QFinanceWarehouse->insert($data);

		$next_running_code = $running_next + 1; 

		$data_code = array(
			'last_code' 	=> $prefix.''.$running_next,
			'next_code' 	=> $prefix.''.$next_running_code,
			'running_now'	=> $running_next,
			'running_next'	=> $next_running_code,
			'digital'		=> $next_running_code,
			'updated_at' 	=> date('Y-m-d H:i:s')
		);

		$where = $QClientCode->getAdapter()->quoteInto('id = ?', 6);
		$QClientCode->update($data_code,$where);

		$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
		$db->commit(); 
		
	} catch (Exception $e) {

		$db->rollback();
		echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
		exit;
		
	}
}

echo '<script>parent.location.href="/finance/finance-warehouse"</script>';
exit;

}

?>