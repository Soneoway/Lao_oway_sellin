<?php
$flashMessenger = $this->_helper->flashMessenger;

$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$dis_id         = $this->getRequest()->getParam('dis_id');
$cost_name		= $this->getRequest()->getParam('cost_name');
$remind_code	= $this->getRequest()->getParam('remind_code');
$status			= $this->getRequest()->getParam('status');
$category_id	= $this->getRequest()->getParam('category_id');
$subject_code	= $this->getRequest()->getParam('subject_code');
$export			= $this->getRequest()->getParam('export');


$limit = LIMITATION;
$total = 0;

$params = array(
	'dis_id'		=> $dis_id,
	'cost_name'		=> $cost_name,
	'remind_code'	=> $remind_code,
	'status'		=> $status,
	'category_id'	=> $category_id,
	'subject_code'	=> $subject_code
);


$QDistributor = new Application_Model_Distributor();
$QCostitem = new Application_Model_CostItem();
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
	$distributor = $QDistributor->fetchAll($where);

	$distributor_list = [];
	$distributorWarehouseList = $QDistributor->getDistributorByWarehouse($warehouse_list);

	foreach ($distributorWarehouseList as $key => $value) {
		$distributor_list[$key] = $value['id'];
	}


	$params['fn'] = $distributor_list;

}else{

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor = $QDistributor->fetchAll($where);

}

$cost_item = $QCostitem->fetchPagination($page, $limit, $total, $params);

if(isset($export) && $export) {
	$data = $QCostitem->fetchPagination($page, null, $total, $params);

	$this->_exportCostItem($data);
}

$this->view->distributor = $distributor;
$this->view->cost_item = $cost_item;
$this->view->staffs = $QStaff->get_cache();

$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/cost-item/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;


$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$this->_helper->viewRenderer->setRender('finance-doc/cost-item');

?>