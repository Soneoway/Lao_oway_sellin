<?php
$flashMessenger = $this->_helper->flashMessenger;

$id 	= $this->getRequest()->getParam('id');

$QDistributor = new Application_Model_Distributor();
$QFinanceClient = new Application_Model_FinanceClient();
$QStore = new Application_Model_Store();
$QBank = new Application_Model_Bank();
$QBankAccountYourSide = new Application_Model_BankAccountYourSide();
$QBankAccountMySide = new Application_Model_BankAccountMySide();

if($id) {
	$accountYourRowSet = $QBankAccountYourSide->find($id);
	$accountYour       = $accountYourRowSet->current();

	if (!$accountYour) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/bank-account-your-side');
		
	}

	//Get Warehouse ID By Superied Distributor 
	$where = $QDistributor->getAdapter()->quoteInto('id =?',$accountYour->d_id);
	$check = $QDistributor->fetchRow($where);

	// Get Distributor ID By Superied Warehouse
	$where2 = $QDistributor->getAdapter()->quoteInto('warehouse_id =?',$check->warehouse_id);
	$distributor_arr = $QDistributor->fetchAll($where2);

	// Loop Dsitributor ID To Array
	foreach ($distributor_arr as $value) {
		$distributor[] = $value['id'];
	}

	// Get Store Date By Distributor Array
	$where3 = $QStore->getAdapter()->quoteInto('d_id IN (?)',$distributor);
	$store = $QStore->fetchAll($where3);

	// Get Store Data By Selected Store
	$where4 = $QStore->getAdapter()->quoteInto('id =?',$accountYour->store_id);
	$store_arr = $QStore->fetchRow($where4);

	// Get Finanace Client
	$where5 = $QFinanceClient->getAdapter()->quoteInto('distributor_y_id =?',$store_arr->d_id);
	$financeClient = $QFinanceClient->fetchAll($where5);

	// Get Bank Account My Side
	$where6 = $QBankAccountMySide->getAdapter()->quoteInto('d_id =?',$accountYour->d_id);
	$bankAccountMy = $QBankAccountMySide->fetchAll($where6);

	// Get Store Disributor Name
	$where7 = $QDistributor->getAdapter()->quoteInto('id =?',$store_arr->d_id);
	$dis_arr = $QDistributor->fetchRow($where7);

	$this->view->accountYour = $accountYour;
	$this->view->store = $store;
	$this->view->financeClient = $financeClient;
	$this->view->bankAccountMy = $bankAccountMy;
	$this->view->dis_arr = $dis_arr;
}

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor_arr = $QDistributor->fetchAll($where);

$this->view->distributor = $distributor_arr;
$this->view->banks = $QBank->get_cache();
$this->_helper->viewRenderer->setRender('bank-account-management/add-bank-account-your-side');

?>