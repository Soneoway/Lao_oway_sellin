<?php
$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');

$QBankAccountMySide = new Application_Model_BankAccountMySide();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$db = Zend_Registry::get('db');

if($id) {

	$accountRowSet = $QBankAccountMySide->find($id);
	$account       = $accountRowSet->current();

	if (!$account) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/bank-account-my-side');
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

		$where = $QBankAccountMySide->getAdapter()->quoteInto('id = ?', $id);
		$sql = $QBankAccountMySide->update($data,$where);

		$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
		$db->commit();

	} catch (Exception $e) {
		
		$db->rollback();
		echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
		exit;
		
	}

	echo '<script>parent.location.href="/finance/bank-account-my-side"</script>';
	exit;


}


?>