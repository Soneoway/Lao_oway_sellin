<?php
$flashMessenger = $this->_helper->flashMessenger;

$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$distributor_ids 	= $this->getRequest()->getParam('distributor_ids');
$my_bank 			= $this->getRequest()->getParam('my_bank');
$amount 			= $this->getRequest()->getParam('amount');
$status 			= $this->getRequest()->getParam('status');
$area_id 			= $this->getRequest()->getParam('area_id');
$store_id 			= $this->getRequest()->getParam('store_id');
$warehouse_id 		= $this->getRequest()->getParam('warehouse_id');
$business_date_form = $this->getRequest()->getParam('business_date_form');
$business_date_to 	= $this->getRequest()->getParam('business_date_to');
$finance_date_form 	= $this->getRequest()->getParam('finance_date_form');
$finance_date_to 	= $this->getRequest()->getParam('finance_date_to');
$export             = $this->getRequest()->getParam('export');


$limit = LIMITATION;
$total = 0;

$QSaleRefund = new Application_Model_SaleRefund();
$QStore = new Application_Model_Store();
$QWarehouse = new Application_Model_Warehouse();
$QAccountType = new Application_Model_AccountType();
$QStaff = new Application_Model_Staff();
$QDistributor = new Application_Model_Distributor();
$QBankAccountMy = new Application_Model_BankAccountMySide();

$params = array(
	'distributor_ids'		=> $distributor_ids,
	'my_bank'				=> $my_bank,
	'amount'				=> $amount,
	'status'				=> $status,
	'store_id'				=> $store_id,
	'warehouse_id'			=> $warehouse_id,
	'business_date_form'	=> $business_date_form,
	'business_date_to'		=> $business_date_to,
	'finance_date_form'		=> $finance_date_form,
	'finance_date_to'		=> $finance_date_to
);

if($distributor_ids) {

    $where2 = $QBankAccountMy->getAdapter()->quoteInto('d_id =?',$distributor_ids);
    $bankaccount = $QBankAccountMy->fetchAll($where2);

    $where3 = $QDistributor->getAdapter()->quoteInto('id =?',$distributor_ids);
    $distributor_arr = $QDistributor->fetchRow($where3);

    $where4 = $QDistributor->getAdapter()->quoteInto('warehouse_id =?',$distributor_arr->agent_warehouse_id);
    $distributor_w = $QDistributor->fetchAll($where4);

    // Loop Array
    foreach($distributor_w as $value) {
        $dis_id[] = $value['id'];
        $w_id[] = $value['agent_warehouse_id'];
    }

    $where5 = $QStore->getAdapter()->quoteInto('d_id IN (?)',$dis_id);
    $store_arr = $QStore->fetchAll($where5);

    $where6 = $QWarehouse->getAdapter()->quoteInto('id IN (?)',$w_id);
    $warehouse_arr = $QWarehouse->fetchAll($where6);

    $this->view->bankaccount = $bankaccount;
    $this->view->store_arr = $store_arr;
    $this->view->warehouse_arr = $warehouse_arr;
}

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor = $QDistributor->fetchAll($where);

$sale_refund = $QSaleRefund->fetchPagination($page, $limit, $total, $params);

if(isset($export) && $export) {
    $data = $QSaleRefund->fetchPagination($page, $limit, $total, $params);

    $this->_exportSaleRefund($data);
}

$this->view->distributor = $distributor;

$this->view->sale_refund = $sale_refund;
$this->view->store = $QStore->get_cache();
$this->view->warehouse = $QWarehouse->get_cache();
$this->view->accounttype = $QAccountType->get_cache();
$this->view->staff = $QStaff->get_cache();

$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/sale-refund/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$this->_helper->viewRenderer->setRender('finance-clients-contact-management/sale-refund');
?>