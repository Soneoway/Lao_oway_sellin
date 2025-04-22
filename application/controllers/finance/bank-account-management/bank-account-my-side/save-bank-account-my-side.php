<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

	$id 							= $this->getRequest()->getParam('id');
	$dis_id 						= $this->getRequest()->getParam('dis_id');
	$bank_account					= $this->getRequest()->getParam('bank_account');
	$mnemonic_code					= $this->getRequest()->getParam('mnemonic_code');
	$card_no						= $this->getRequest()->getParam('card_no');
	$user_id						= $this->getRequest()->getParam('user_id',null);
	$status							= $this->getRequest()->getParam('status');
	$card_type						= $this->getRequest()->getParam('card_type');
	$master_account					= $this->getRequest()->getParam('master_account',null);
	$bank_id						= $this->getRequest()->getParam('bank_id');
	$account_org					= $this->getRequest()->getParam('account_org');
	$hots							= $this->getRequest()->getParam('hots');
	$account_pp						= $this->getRequest()->getParam('account_pp');
	$account_type					= $this->getRequest()->getParam('account_type');
	$remark							= $this->getRequest()->getParam('remark');

	$userStorage = Zend_Auth::getInstance()->getStorage()->read();
	$db = Zend_Registry::get('db');

	$QClientCode = new Application_Model_ClientCode();
	$QBankAccountMySide = new Application_Model_BankAccountMySide();

	if($id){

		try {
			$db->beginTransaction();

			$data = array(
				'bank_account'		=> $bank_account,
				'mnemonic_code'		=> $mnemonic_code,
				'card_no'			=> $card_no,
				'user_id'			=> $user_id,
				'status'			=> $status,
				'card_type'			=> $card_type,
				'master_account_id' => $master_account,
				'bank'				=> $bank_id,
				'account_org_id'	=> $account_org,
				'host'				=> $hots,
				'account_pp'		=> $account_pp,
				'account_type'		=> $account_type,
				'remark'			=> $remark,
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $userStorage->id
			);

			$where = $QBankAccountMySide->getAdapter()->quoteInto('id =?',$id);
			$QBankAccountMySide->update($data,$where);

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

			$cusCode 	  	= $QClientCode->find(8);
		$insertCode 	= $cusCode[0]['next_code']; // ລະຫັດປະຈຸບັນ ( ຕົວອັກສອນນຳໜ້າ + ຕົວເລກ )
		$running_next	= $cusCode[0]['running_next']; // ລະຫັດຕົວເລກປະຈຸບັນ
		$running_now	= $cusCode[0]['running_now']; // ລະຫັດຕົວເລກກ່ອນໜ້າ
		$prefix			= $cusCode[0]['prefix']; // ໂຕອັນສອນນຳໜ້າ

		$data = array(
			'd_id'				=> $dis_id,
			'code'				=> $insertCode,
			'bank_account'		=> $bank_account,
			'mnemonic_code'		=> $mnemonic_code,
			'card_no'			=> $card_no,
			'user_id'			=> $user_id,
			'status'			=> $status,
			'card_type'			=> $card_type,
			'master_account_id' => $master_account,
			'bank'				=> $bank_id,
			'account_org_id'	=> $account_org,
			'host'				=> $hots,
			'account_pp'		=> $account_pp,
			'account_type'		=> $account_type,
			'remark'			=> $remark,
			'created_at'		=> date('Y-m-d H:i:s'),
			'created_by'		=> $userStorage->id
		);

		$QBankAccountMySide->insert($data);

		$next_running_code = $running_next + 1; 

		$data_code = array(
			'last_code' 	=> $prefix.''.$running_next,
			'next_code' 	=> $prefix.''.$next_running_code,
			'running_now'	=> $running_next,
			'running_next'	=> $next_running_code,
			'digital'		=> $next_running_code,
			'updated_at' 	=> date('Y-m-d H:i:s')
		);

		$where = $QClientCode->getAdapter()->quoteInto('id = ?', 8);
		$QClientCode->update($data_code,$where);
		
		$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
		$db->commit(); 
	} catch (Exception $e) {

		$db->rollback();
		echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
		exit;

	}
}

echo '<script>parent.location.href="/finance/bank-account-my-side"</script>';
exit;

}

?>