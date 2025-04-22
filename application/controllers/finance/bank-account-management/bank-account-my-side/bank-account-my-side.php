<?php
$flashMessenger = $this->_helper->flashMessenger;

$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$dis_id            = $this->getRequest()->getParam('dis_id');
$bank_account	   = $this->getRequest()->getParam('bank_account');
$bank 			   = $this->getRequest()->getParam('bank');
$account_type	   = $this->getRequest()->getParam('account_type');
$mnemonic_code	   = $this->getRequest()->getParam('mnemonic_code');
$host			   = $this->getRequest()->getParam('host');
$status			   = $this->getRequest()->getParam('status');
$account_pp		   = $this->getRequest()->getParam('account_pp');
$user_id		   = $this->getRequest()->getParam('user_id');
$card_type		   = $this->getRequest()->getParam('card_type');
$export			   = $this->getRequest()->getParam('export');


$limit = LIMITATION;
$total = 0;

$params = array(
	'dis_id'				=> $dis_id,
	'bank_account'			=> $bank_account,
	'bank'					=> $bank,
	'account_type'			=> $account_type,
	'mnemonic_code'			=> $mnemonic_code,
	'host'					=> $host,
	'status'				=> $status,
	'account_pp'			=> $account_pp,
	'user_id'				=> $user_id,
	'card_type'				=> $card_type
);

$QDistributor = new Application_Model_Distributor();
$QBank = new Application_Model_Bank();
$QAccountType = new Application_Model_AccountType();
$QStaff = new Application_Model_Staff();
$QBankAccountMySide = new Application_Model_BankAccountMySide();
$QAccountOrg = new Application_Model_AccountingOrganization();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();


// Check Finance Account // Show OPPO LAOS Mobile Distributor Only // warehouse_group_user
if(in_array($userStorage->group_id, array(96,118))) {

	$warehouse_list = [];
	$userWarehouseList = $QWarehouseGroupUser->currentWarehouseGroupUserList($userStorage->id);

	foreach ($userWarehouseList as $key => $value) {
		$warehouse_list[$key] = $value['warehouse_id'];
	}

	$where = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id IN (?)',$warehouse_list);
	$distributor_arr = $QDistributor->fetchAll($where);

	$distributor_list = [];
	$distributorWarehouseList = $QDistributor->getDistributorByWarehouse($warehouse_list);

	foreach ($distributorWarehouseList as $key => $value) {
		$distributor_list[$key] = $value['id'];
	}


	$params['fn'] = $distributor_list;

}else{

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor_arr = $QDistributor->fetchAll($where);

}

$where2 = $QStaff->getAdapter()->quoteInto('status = ?',1);
$staffs = $QStaff->fetchAll($where2);

$bankAccountMy = $QBankAccountMySide->fetchPagination($page, $limit, $total, $params);

if(isset($export) && $export) {
	$data = $QBankAccountMySide->fetchPagination($page, null, $total, $params);

	$this->_exportBankAccountMy($data);
}

$this->view->distributor = $distributor_arr;
$this->view->bank = $QBank->get_cache();
$this->view->accountType = $QAccountType->get_cache();
$this->view->staffs = $staffs;
$this->view->bankAccountMy = $bankAccountMy;
$this->view->accountOrg = $QAccountOrg->get_cache();
$this->view->staff_cache = $QStaff->get_cache();

$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/bank-account-my-side/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;


$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$this->_helper->viewRenderer->setRender('bank-account-management/bank-account-my-side');

?>