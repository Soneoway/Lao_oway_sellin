<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

	$id 							= $this->getRequest()->getParam('id');
	$dis_id_m 						= $this->getRequest()->getParam('dis_id_m');
	$dis_id_y						= $this->getRequest()->getParam('dis_id_y');
	$mnemonic_code					= $this->getRequest()->getParam('mnemonic_code');
	$finance_client					= $this->getRequest()->getParam('finance_client');
	$finance_warehouse				= $this->getRequest()->getParam('finance_warehouse');
	$status							= $this->getRequest()->getParam('status');
	$account_org_m					= $this->getRequest()->getParam('account_org_m');
	$account_org_y					= $this->getRequest()->getParam('account_org_y');
	$cross_account					= $this->getRequest()->getParam('cross_account');
	$network						= $this->getRequest()->getParam('network');
	$support_money					= $this->getRequest()->getParam('support_money');
	$remark							= $this->getRequest()->getParam('remark');

	$userStorage = Zend_Auth::getInstance()->getStorage()->read();
	$db = Zend_Registry::get('db');

	$QFinanceClient = new Application_Model_FinanceClient();
	$QClientCode = new Application_Model_ClientCode();

	if($id){

		try {
			
			$db->beginTransaction();

			$data = array(
				'mnemonic_code'		=> $mnemonic_code,
				'distributor_y_id'	=> $dis_id_y,
				'name'				=> $finance_client,
				'fw_id'				=> $finance_warehouse,
				'account_m'			=> $account_org_m,
				'account_y'		    => $account_org_y,
				'network'			=> $network,
				'cross_account'		=> $cross_account,
				'support_money'		=> $support_money,
				'status'			=> $status,
				'remark'			=> $remark,
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $userStorage->id
			);

			$where = $QFinanceClient->getAdapter()->quoteInto('id = ?', $id);
			$QFinanceClient->update($data,$where);

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

			$cusCode 	  	= $QClientCode->find(7);
		$insertCode 	= $cusCode[0]['next_code']; // ລະຫັດປະຈຸບັນ ( ຕົວອັກສອນນຳໜ້າ + ຕົວເລກ )
		$running_next	= $cusCode[0]['running_next']; // ລະຫັດຕົວເລກປະຈຸບັນ
		$running_now	= $cusCode[0]['running_now']; // ລະຫັດຕົວເລກກ່ອນໜ້າ
		$prefix			= $cusCode[0]['prefix']; // ໂຕອັນສອນນຳໜ້າ


		$data = array(
			'code'				=> $insertCode,
			'mnemonic_code'		=> $mnemonic_code,
			'distributor_m_id'	=> $dis_id_m,
			'distributor_y_id'	=> $dis_id_y,
			'name'				=> $finance_client,
			'fw_id'				=> $finance_warehouse,
			'account_m'			=> $account_org_m,
			'account_y'		    => $account_org_y,
			'network'			=> $network,
			'cross_account'		=> $cross_account,
			'support_money'		=> $support_money,
			'status'			=> $status,
			'remark'			=> $remark,
			'created_at'		=> date('Y-m-d H:i:s'),
			'created_by'		=> $userStorage->id
		);

		$QFinanceClient->insert($data);

		$next_running_code = $running_next + 1; 

		$data_code = array(
			'last_code' 	=> $prefix.''.$running_next,
			'next_code' 	=> $prefix.''.$next_running_code,
			'running_now'	=> $running_next,
			'running_next'	=> $next_running_code,
			'digital'		=> $next_running_code,
			'updated_at' 	=> date('Y-m-d H:i:s')
		);

		$where = $QClientCode->getAdapter()->quoteInto('id = ?', 7);
		$QClientCode->update($data_code,$where);
		
		$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
		$db->commit(); 
	} catch (Exception $e) {
		$db->rollback();
		echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
		exit;
	}
}

echo '<script>parent.location.href="/finance/finance-client"</script>';
exit;

}

?>