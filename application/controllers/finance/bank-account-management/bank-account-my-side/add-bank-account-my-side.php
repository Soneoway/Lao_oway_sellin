<?php
$flashMessenger = $this->_helper->flashMessenger;

$id            = $this->getRequest()->getParam('id');

$QDistributor = new Application_Model_Distributor();
$QBank = new Application_Model_Bank();
$QAccountType = new Application_Model_AccountType();
$QBankAccountMySide = new Application_Model_BankAccountMySide();
$QAccountOrg = new Application_Model_AccountingOrganization();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();

if($id){
	$accountMyRowSet = $QBankAccountMySide->find($id);
	$accountMy       = $accountMyRowSet->current();

	if (!$accountMy) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/bank-account-my-side');
		
	}

	$where = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$accountMy->d_id);
	$this->view->accountOrg = $QAccountOrg->fetchAll($where);
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

$this->view->accountMy = $accountMy;
$this->view->distributor = $distributor_arr;
$this->view->bank = $QBank->get_cache();
$this->view->accountType = $QAccountType->get_cache();
$this->view->bankaccount = $QBankAccountMySide->get_cache();

$this->_helper->viewRenderer->setRender('bank-account-management/add-bank-account-my-side');

?>