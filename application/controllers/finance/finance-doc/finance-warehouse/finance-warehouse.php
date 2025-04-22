<?php
$flashMessenger = $this->_helper->flashMessenger;

$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$dis_id         	= $this->getRequest()->getParam('dis_id');
$warehouse_id   	= $this->getRequest()->getParam('warehouse_id');
$finance_warehouse	= $this->getRequest()->getParam('finance_warehouse');
$mnemonic_code		= $this->getRequest()->getParam('mnemonic_code');
$export				= $this->getRequest()->getParam('export');

$limit = LIMITATION;
$total = 0;

$params = array(
	'dis_id' 				=> $dis_id,
	'warehouse_id'			=> $warehouse_id,
	'finance_warehouse'		=> $finance_warehouse,
	'mnemonic_code'			=> $mnemonic_code
);

$QDistributor = new Application_Model_Distributor();
$QFinanceWarehouse = new Application_Model_FinanceWarehouse();
$QWarehouse = new Application_Model_Warehouse();
$QAccountOrg = new Application_Model_AccountingOrganization();
$QStaff = new Application_Model_Staff();
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

$financeWarehouse = $QFinanceWarehouse->fetchPagination($page, $limit, $total, $params);

if(isset($export) && $export) {
	$data = $QFinanceWarehouse->fetchPagination($page, null, $total, $params);

	$this->_exportFinanceWarehouse($data);
}

$this->view->distributor = $distributor_arr;
$this->view->financeWarehouse = $financeWarehouse;
$this->view->warehouse = $QWarehouse->get_cache();
$this->view->accountorg = $QAccountOrg->get_cache();
$this->view->staff = $QStaff->get_cache();

$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/finance-warehouse/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;


$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$this->_helper->viewRenderer->setRender('finance-doc/finance-warehouse');

?>