<?php
$flashMessenger = $this->_helper->flashMessenger;

$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$distributor_ids 	= $this->getRequest()->getParam('distributor_ids');
$store_id			= $this->getRequest()->getParam('store_id');
$warehouse_id		= $this->getRequest()->getParam('warehouse_id');
$transfer_type		= $this->getRequest()->getParam('transfer_type');
$serial_number 		= $this->getRequest()->getParam('serial_number');
$amount				= $this->getRequest()->getParam('amount');
$create_form		= $this->getRequest()->getParam('create_form');
$create_to			= $this->getRequest()->getParam('create_to');
$finance_client		= $this->getRequest()->getParam('finance_client');
$status				= $this->getRequest()->getParam('status');
$bank_my			= $this->getRequest()->getParam('bank_my');
$area_id			= $this->getRequest()->getParam('area_id');
$doc_no				= $this->getRequest()->getParam('doc_no');
$finance_date_form  = $this->getRequest()->getParam('finance_date_form');
$finance_date_to	= $this->getRequest()->getParam('finance_date_to');
$export 			= $this->getRequest()->getParam('export');

$limit = LIMITATION;
$total = 0;

$params = array(
	'distributor_ids'		=> $distributor_ids,
	'store_id'				=> $store_id,
	'warehouse_id'			=> $warehouse_id,
	'transfer_type'			=> $transfer_type,
	'serial_number'			=> $serial_number,
	'amount'				=> $amount,
	'create_form'			=> $create_form,
	'create_to'				=> $create_to,
	'finance_client'		=> $finance_client,
	'status'				=> $status,
	'bank_my'				=> $bank_my,
	'area_id'				=> $area_id,
	'doc_no'				=> $doc_no,
	'finance_date_form'		=> $finance_date_form,
	'finance_date_to'		=> $finance_date_to
);

$QSaleReceipt = new Application_Model_SaleReceipt();
$QStore = new Application_Model_Store();
$QWarehouse = new Application_Model_Warehouse();
$QStaff = new Application_Model_Staff();
$QDistributor = new Application_Model_Distributor();
$QWarehouse = new Application_Model_Warehouse();
$QArea = new Application_Model_Area();
$QFinanceClient = new Application_Model_FinanceClient();
$QBankAccountMy = new Application_Model_BankAccountMySide();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();

if($distributor_ids) {

	$where = $QDistributor->getAdapter()->quoteInto('id =?',$distributor_ids);
    $distributor_arr = $QDistributor->fetchRow($where);

    $where2 = $QDistributor->getAdapter()->quoteInto('warehouse_id =?',$distributor_arr->agent_warehouse_id);
    $distributor_w = $QDistributor->fetchAll($where2);

    // Loop Array
    foreach($distributor_w as $value) {
        $dis_id[] = $value['id'];
        $w_id[] = $value['agent_warehouse_id'];
    }

    $where3 = $QStore->getAdapter()->quoteInto('d_id IN (?)',$dis_id);
    $store_arr = $QStore->fetchAll($where3);

    $where4 = $QWarehouse->getAdapter()->quoteInto('id IN (?)',$w_id);
    $warehouse_arr = $QWarehouse->fetchAll($where4);

    $where6 = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$distributor_ids);
    $financeClient_arr = $QFinanceClient->fetchAll($where6);

    $where7 = $QBankAccountMy->getAdapter()->quoteInto('d_id =?',$distributor_ids);
    $account_my_arr = $QBankAccountMy->fetchAll($where7);

	$this->view->store_arr = $store_arr;
	$this->view->warehouse_arr = $warehouse_arr;
	$this->view->financeClient_arr = $financeClient_arr;
	$this->view->account_my_arr = $account_my_arr;
}

$sale_receipt = $QSaleReceipt->fetchPagination($page, $limit, $total, $params);

// Check Finance Account // Show OPPO LAOS Mobile Distributor Only // warehouse_group_user
// 96 : Finance Vientaine  // 118 : Finance Admin
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

	// 106 : RGM  // 109 : Sale Dealer  // 95 : Sale Vientian
}else if (in_array($userStorage->group_id, array(106,109,95))){

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

	$params['sale'] = $distributor_list;

}else{


$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor = $QDistributor->fetchAll($where);

}

if(isset($export) && $export) {
	
	$data = $QSaleReceipt->fetchPagination($page, null, $total, $params);

	$this->_exportSaleReceipt($data);
}

$this->view->area = $QArea->get_cache();
$this->view->distributor = $distributor;
$this->view->sale_receipt = $sale_receipt;
$this->view->store = $QStore->get_cache();
$this->view->warehouse = $QWarehouse->get_cache();
$this->view->staff = $QStaff->get_cache();

$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/sale-receipt/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);


$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;


$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$this->_helper->viewRenderer->setRender('finance-clients-contact-management/sale-receipt');

?>