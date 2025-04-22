<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

	$id 							= $this->getRequest()->getParam('id');
	$dis_id 						= $this->getRequest()->getParam('dis_id');
	$store_id						= $this->getRequest()->getParam('store_id');
	$finance_client					= $this->getRequest()->getParam('finance_client');
	$bank_id						= $this->getRequest()->getParam('bank_id');
	$account_pp						= $this->getRequest()->getParam('account_pp');
	$bank_account_my				= $this->getRequest()->getParam('bank_account_my');
	$opposite_account				= $this->getRequest()->getParam('opposite_account');
	$card_no						= $this->getRequest()->getParam('card_no');
	$contact						= $this->getRequest()->getParam('contact');
	$remark							= $this->getRequest()->getParam('remark');

	$userStorage = Zend_Auth::getInstance()->getStorage()->read();
	$db = Zend_Registry::get('db');

	$QBankAccountYourSide = new Application_Model_BankAccountYourSide();

	if($id) {

	}else{

		try {
			$db->beginTransaction();

			$data = array(
				'd_id'				=> $dis_id,
				'store_id'			=> $store_id,
				'finance_client_id' => $finance_client,
				'bank_id'			=> $bank_id,
				'account_pp'		=> $account_pp,
				'b_account_my'		=> $bank_account_my,
				'account_name'		=> $opposite_account,
				'card_no'			=> $card_no,
				'contact'			=> $contact,
				'remark'			=> $remark,
				'created_at'		=> date('Y-m-d H:i:s'),
				'created_by'		=> $userStorage->id
			);

			$QBankAccountYourSide->insert($data);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit(); 
			
		} catch (Exception $e) {
			
			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;

		}
	}

	echo '<script>parent.location.href="/finance/bank-account-your-side"</script>';
	exit;


}

?>