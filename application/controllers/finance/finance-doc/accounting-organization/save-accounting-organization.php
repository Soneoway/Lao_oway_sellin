<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

	$id 							= $this->getRequest()->getParam('id');
	$dis_id 						= $this->getRequest()->getParam('dis_id');
	$corporation_type 				= $this->getRequest()->getParam('corporation_type');
	$taxpayer_identification 		= $this->getRequest()->getParam('taxpayer_identification');
	$mnemonic_code 					= $this->getRequest()->getParam('mnemonic_code');
	$accounting_organization 		= $this->getRequest()->getParam('accounting_organization');
	$credit_code 					= $this->getRequest()->getParam('credit_code');
	$remark 						= $this->getRequest()->getParam('remark');

	$userStorage = Zend_Auth::getInstance()->getStorage()->read();
	$db = Zend_Registry::get('db');

	$QAccountingOrg = new Application_Model_AccountingOrganization();
	$QClientCode = new Application_Model_ClientCode();

	if($id) {

		try {
			$db->beginTransaction();

			$data = array(
				'name'						=> $accounting_organization,
				'corporation_type'			=> $corporation_type,
				'identification_no'			=> $taxpayer_identification,
				'credit_code'				=> $credit_code,
				'mnemonic_code'				=> $mnemonic_code,
				'remark'					=> $remark,
				'updated_at'				=> date('Y-m-d H:m:s'),
				'updated_by'				=> $userStorage->id
			);

			$where = $QAccountingOrg->getAdapter()->quoteInto('id = ?', $id);
			$QAccountingOrg->update($data,$where);

			
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

			$cusCode 	  	= $QClientCode->find(5);
		$insertCode 	= $cusCode[0]['next_code']; // ລະຫັດປະຈຸບັນ ( ຕົວອັກສອນນຳໜ້າ + ຕົວເລກ )
		$running_next	= $cusCode[0]['running_next']; // ລະຫັດຕົວເລກປະຈຸບັນ
		$running_now	= $cusCode[0]['running_now']; // ລະຫັດຕົວເລກກ່ອນໜ້າ
		$prefix			= $cusCode[0]['prefix']; // ໂຕອັນສອນນຳໜ້າ

		$data = array(
			'code'						=> $insertCode,
			'd_id'						=> $dis_id,
			'name'						=> $accounting_organization,
			'corporation_type'			=> $corporation_type,
			'identification_no'			=> $taxpayer_identification,
			'credit_code'				=> $credit_code,
			'mnemonic_code'				=> $mnemonic_code,
			'remark'					=> $remark,
			'created_at'				=> date('Y-m-d H:m:s'),
			'created_by'				=> $userStorage->id
		);

		$QAccountingOrg->insert($data);

		$next_running_code = $running_next + 1; 

		$data_code = array(
			'last_code' 	=> $prefix.''.$running_next,
			'next_code' 	=> $prefix.''.$next_running_code,
			'running_now'	=> $running_next,
			'running_next'	=> $next_running_code,
			'digital'		=> $next_running_code,
			'updated_at' 	=> date('Y-m-d H:i:s')
		);

		$where = $QClientCode->getAdapter()->quoteInto('id = ?', 5);
		$QClientCode->update($data_code,$where);

		$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
		$db->commit(); 
		
	} catch (Exception $e) {
		$db->rollback();
		echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
		exit;
		
	}

}

echo '<script>parent.location.href="/finance/accounting-organization"</script>';
exit;

}

?>