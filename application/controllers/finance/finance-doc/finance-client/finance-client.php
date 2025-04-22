<?php
$flashMessenger = $this->_helper->flashMessenger;

$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$dis_id_m			= $this->getRequest()->getParam('dis_id_m');
$dis_id_y			= $this->getRequest()->getParam('dis_id_y');
$finance_client 	= $this->getRequest()->getParam('finance_client');
$account_org_m  	= $this->getRequest()->getParam('account_org_m');
$account_org_y  	= $this->getRequest()->getParam('account_org_y');
$network			= $this->getRequest()->getParam('network');
$mnemonic_code		= $this->getRequest()->getParam('mnemonic_code');
$finance_warehouse 	= $this->getRequest()->getParam('finance_warehouse');
$cross_account 		= $this->getRequest()->getParam('cross_account');
$status				= $this->getRequest()->getParam('status');
$export 			= $this->getRequest()->getParam('export');

$limit = LIMITATION;
$total = 0;

$QDistributor = new Application_Model_Distributor();
$QFinanceClient = new Application_Model_FinanceClient();
$QFinanceWarehouse = new Application_Model_FinanceWarehouse();
$QAccountOrg = new Application_Model_AccountingOrganization();
$QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();
$QStaff = new Application_Model_Staff();

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$params = array(
	'dis_id_m'			=> $dis_id_m,
	'dis_id_y'			=> $dis_id_y,
	'finance_client'	=> $finance_client,
	'account_org_m'		=> $account_org_m,
	'account_org_y'		=> $account_org_y,
	'network'			=> $network,
	'mnemonic_code'		=> $mnemonic_code,
	'finance_warehouse'	=> $finance_warehouse,
	'cross_account'		=> $cross_account,
	'status'			=> $status
);

if($dis_id_m) {

	$where = $QDistributor->getAdapter()->quoteInto('id =?',$dis_id_m);
	$distributor_row = $QDistributor->fetchRow($where);

	$where2 = $QDistributor->getAdapter()->quoteInto('warehouse_id =?',$distributor_row->agent_warehouse_id);
	$distributor = $QDistributor->fetchAll($where2);

	$where3 = $QFinanceWarehouse->getAdapter()->quoteInto('d_id =?',$dis_id_m);
	$finance_warehouse_arr = $QFinanceWarehouse->fetchAll($where3);

	$this->view->distributor_y = $distributor;
	$this->view->finance_warehouse_arr = $finance_warehouse_arr;

}else{

	$where = $QFinanceWarehouse->getAdapter()->quoteInto('status IN (?)',array(1,2));
	$finance_warehouse_arr = $QFinanceWarehouse->fetchAll($where);

	$this->view->finance_warehouse_arr = $finance_warehouse_arr;

}

// Check Finance Account // Show OPPO LAOS Mobile Distributor Only // warehouse_group_user
if(in_array($userStorage->group_id, array(96,118))) {

	$warehouse_list = [];
	$userWarehouseList = $QWarehouseGroupUser->currentWarehouseGroupUserList($userStorage->id);

	foreach ($userWarehouseList as $key => $value) {
		$warehouse_list[$key] = $value['warehouse_id'];
	}

	$where = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id IN (?)',$warehouse_list);
	$distributor_arr_m = $QDistributor->fetchAll($where);

	$distributor_list = [];
	$distributorWarehouseList = $QDistributor->getDistributorByWarehouse($warehouse_list);

	foreach ($distributorWarehouseList as $key => $value) {
		$distributor_list[$key] = $value['id'];
	}


	$params['fn'] = $distributor_list;

}else{

	$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
	$distributor_arr_m = $QDistributor->fetchAll($where);

}

$financeClient = $QFinanceClient->fetchPagination($page, $limit, $total, $params);


if(isset($export) && $export) {

	$data = $QFinanceClient->fetchPagination($page, null, $total, $params);

	$this->_exportFinanceClient($data);
}

$this->view->financeclient = $financeClient;
$this->view->distributor_m = $distributor_arr_m;
$this->view->distributor   = $QDistributor->get_cache4();
$this->view->financewarehouse = $QFinanceWarehouse->get_cache();
$this->view->acccountorg = $QAccountOrg->get_cache();
$this->view->staffs = $QStaff->get_cache();

$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/finance-client/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);


$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;


$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$this->_helper->viewRenderer->setRender('finance-doc/finance-client');


?>