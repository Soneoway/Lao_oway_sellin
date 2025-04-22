<?php
$flashMessenger = $this->_helper->flashMessenger;

$id         	= $this->getRequest()->getParam('id');

$QDistributor = new Application_Model_Distributor();
$QFinanceWarehouse = new Application_Model_FinanceWarehouse();
$QWarehouse = new Application_Model_Warehouse();
$QAccountOrg = new Application_Model_AccountingOrganization();
$QFinanceWarehouseGroup = new Application_Model_FinanceWarehouseGroup();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();

if($id) {

	$financeWarehouseRowSet = $QFinanceWarehouse->find($id);
	$financeWarehouse       = $financeWarehouseRowSet->current();

	if (!$financeWarehouse) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/finance-warehouse');
	}

	$where = $QDistributor->getAdapter()->quoteInto('id =?',$financeWarehouse->d_id);
    $distributor_row = $QDistributor->fetchRow($where);

    $where2 = $QWarehouse->getAdapter()->quoteInto('id =?',$distributor_row->agent_warehouse_id);
    $warehouse_row = $QWarehouse->fetchAll($where2);

    $where3 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$financeWarehouse->d_id);
    $accountorg_row = $QAccountOrg->fetchAll($where3);

    $where4 = $QFinanceWarehouseGroup->getAdapter()->quoteInto('d_id =?',$financeWarehouse->d_id);
    $warehouse_group = $QFinanceWarehouseGroup->fetchAll($where4);

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

$this->view->distributor 	= $distributor_arr;
$this->view->financeWarehouse 	= $financeWarehouse;
$this->view->warehouse = $warehouse_row;
$this->view->accountOrg = $accountorg_row;
$this->view->warehouseGroup = $warehouse_group;

$this->_helper->viewRenderer->setRender('finance-doc/add-finance-warehouse');

?>