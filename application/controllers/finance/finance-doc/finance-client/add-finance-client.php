<?php
$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');

$QFinanceClient = new Application_Model_FinanceClient();
$QDistributor = new Application_Model_Distributor();
$QFinanceWarehouse = new Application_Model_FinanceWarehouse();
$QAccountOrg = new Application_Model_AccountingOrganization();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();

if($id) {

	$financeClientRowSet = $QFinanceClient->find($id);
	$financeClient       = $financeClientRowSet->current();

	if (!$financeClient) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/finance-client');
	}

	$where = $QDistributor->getAdapter()->quoteInto('id =?',$financeClient->distributor_m_id);
	$distributor_row = $QDistributor->fetchRow($where);

	$where2 = $QDistributor->getAdapter()->quoteInto('warehouse_id =?', $distributor_row->agent_warehouse_id);
	$distributor_y = $QDistributor->fetchAll($where2);

	$where3 = $QFinanceWarehouse->getAdapter()->quoteInto('d_id =?',$financeClient->distributor_m_id);
	$financeWarehouse = $QFinanceWarehouse->fetchAll($where3);

	$where_org_m = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$financeClient->distributor_m_id);
	$account_org_m = $QAccountOrg->fetchAll($where_org_m);

	$where_org_y = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$financeClient->distributor_y_id);
	$account_org_y = $QAccountOrg->fetchAll($where_org_y);

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

$this->view->distributor 		= $distributor_arr;
$this->view->financeClient  	= $financeClient;
$this->view->distributor_y		= $distributor_y;
$this->view->financeWarehouse 	= $financeWarehouse;
$this->view->account_org_m		= $account_org_m;
$this->view->account_org_y		= $account_org_y;

$this->_helper->viewRenderer->setRender('finance-doc/add-finance-client');
?>