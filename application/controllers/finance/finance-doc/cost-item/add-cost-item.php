<?php
$flashMessenger = $this->_helper->flashMessenger;

$id         = $this->getRequest()->getParam('id');

$QDistributor = new Application_Model_Distributor();
$QCostitem = new Application_Model_CostItem();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();

if($id) {

	$costItemRowSet = $QCostitem->find($id);
	$costItem       = $costItemRowSet->current();

	if (!$costItem) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/cost-item');
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
	$distributor = $QDistributor->fetchAll($where);

}else{

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor = $QDistributor->fetchAll($where);

}

$this->view->distributor 	= $distributor;
$this->view->costItem 		= $costItem;

$this->_helper->viewRenderer->setRender('finance-doc/add-cost-item');

?>