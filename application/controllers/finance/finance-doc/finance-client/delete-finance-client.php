<?php
$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');

$QFinanceClient = new Application_Model_FinanceClient();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$db = Zend_Registry::get('db');

if($id) {

	$financeClientRowSet = $QFinanceClient->find($id);
	$financeclient       = $financeClientRowSet->current();

	if (!$financeclient) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍ່ຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/finance-client');
	}

	try {
		$db->beginTransaction();

		$data = array(
			'updated_at' 	=> date('Y-m-d H:i:s'),
			'updated_by'	=> $userStorage->id,
			'del_by'		=> $userStorage->id,
			'del_at'		=> date('Y-m-d H:i:s'),
			'status'		=> 3
		);

		$where = $QFinanceClient->getAdapter()->quoteInto('id = ?', $id);
		$sql = $QFinanceClient->update($data,$where);

		$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
		$db->commit(); 
		
	} catch (Exception $e) {
		
		$db->rollback();
		echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
		exit;

	}

	echo '<script>parent.location.href="/finance/finance-client"</script>';
	exit;

}

?>