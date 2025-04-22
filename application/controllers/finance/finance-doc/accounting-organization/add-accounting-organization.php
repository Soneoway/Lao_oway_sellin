<?php
$flashMessenger = $this->_helper->flashMessenger;

$id            = $this->getRequest()->getParam('id');
$proceed 	   = $this->getRequest()->getParam('proceed');


$QDistributor = new Application_Model_Distributor();
$QAccountOrg = new Application_Model_AccountingOrganization();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();


if($id){


	if($proceed == 1) {

		$accountRowSet = $QAccountOrg->find($id);
		$account       = $accountRowSet->current();

		if (!$account) {
			$flashMessenger->setNamespace('error')->addMessage('ບັນຊີອົງກອນບໍ່ຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
			$this->_redirect(HOST.'finance/accounting-organization');
		}

	}elseif($proceed == 2) {

		$accountRowSet = $QAccountOrg->find($id);
		$account       = $accountRowSet->current();

		if (!$account) {
			$flashMessenger->setNamespace('error')->addMessage('ບັນຊີອົງກອນບໍ່ຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
			$this->_redirect(HOST.'finance/accounting-organization');
		}

	}else{
		$flashMessenger->setNamespace('error')->addMessage('ການດຳເນີນການບໍ່ຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/accounting-organization');
	}

}

// Check Finance Account // Show OPPO LAOS Mobile Distributor Only // warehouse_group_user
if(in_array($userStorage->group_id, array(96,118))) {

	$warehouse_list = [];
	$userWarehouseList = $QWarehouseGroupUser->currentWarehouseGroupUserList($userStorage->id);

	foreach ($userWarehouseList as $key => $value) {
		$warehouse_list[$key] = $value['warehouse_id'];
	}

	$where = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id IN (?)',$warehouse_list);
	$distributor_arr = $QDistributor->fetchAll($where);


}else{

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor_arr = $QDistributor->fetchAll($where);

}

$this->view->distributor = $distributor_arr;
$this->view->account 	 = $account;
$this->view->proceed     = $proceed;


$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$this->_helper->viewRenderer->setRender('finance-doc/add-accounting-organization');

?>