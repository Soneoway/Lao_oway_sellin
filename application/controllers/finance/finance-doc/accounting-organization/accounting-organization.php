<?php
$flashMessenger = $this->_helper->flashMessenger;

$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$dis_id            = $this->getRequest()->getParam('dis_id');
$d_id_range 	   = $this->getRequest()->getParam('d_id_range');
$mnemonic_code 	   = $this->getRequest()->getParam('mnemonic_code');
$corporation_type  = $this->getRequest()->getParam('corporation_type');
$account_org	   = $this->getRequest()->getParam('account_org');
$export			   = $this->getRequest()->getParam('export');

$limit = LIMITATION;
$total = 0;


$params = array(
	'dis_id'				=> $dis_id,
	'd_id_range'			=> $d_id_range,
	'mnemonic_code' 		=> $mnemonic_code,
	'corporation_type'		=> $corporation_type,
	'account_org'			=> $account_org
);

$QAccountOrg = new Application_Model_AccountingOrganization();
$QDistributor = new Application_Model_Distributor();
$QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();

// Check Finance Account // Show OPPO LAOS Mobile Distributor Only // warehouse_group_user
if(in_array($userStorage->group_id, array(96,118))){

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

$accountOrg = $QAccountOrg->fetchPagination($page, $limit, $total, $params);

if(isset($export) && $export) {
	$data = $QAccountOrg->fetchPagination($page, null, $total, $params);

	$this->_exportAccountingOrganization($data);
}

$this->view->accountOrg = $accountOrg;
$this->view->distributor = $distributor_arr;

$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/accounting-organization/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;


$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$this->_helper->viewRenderer->setRender('finance-doc/accounting-organization');

?>