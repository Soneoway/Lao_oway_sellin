<?php
$flashMessenger = $this->_helper->flashMessenger;

$id 	= $this->getRequest()->getParam('id');

$QDistributor = new Application_Model_Distributor();
$QFinanceWarehouseGroup = new Application_Model_FinanceWarehouseGroup();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();

if($id) {

	$warehouseGroupRowSet = $QFinanceWarehouseGroup->find($id);
	$warehouseGroup       = $warehouseGroupRowSet->current();

	if (!$warehouseGroup) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດກຸ່ມການເງິນບໍ່ຖຶກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/finance-warehouse-group');
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
$this->view->fwarehouseGroup = $warehouseGroup;

$this->_helper->viewRenderer->setRender('finance-doc/add-finance-warehouse-group');

?>