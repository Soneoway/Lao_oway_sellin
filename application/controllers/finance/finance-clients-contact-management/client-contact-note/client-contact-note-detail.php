<?php
$flashMessenger = $this->_helper->flashMessenger;

$sn 	= $this->getRequest()->getParam('sn');

$QContactDetail = new Application_Model_ContactDetail();
$QMarket = new Application_Model_Market();
$QGood = new Application_Model_Good();
$QGoodColor = new Application_Model_GoodColor();
$QStaff = new Application_Model_Staff();
$QFinanceClient = new Application_Model_FinanceClient();
$QFinanceWarehouse = new Application_Model_FinanceWarehouse();
$QStore = new Application_Model_Store();
$QSaleReceipt = new Application_Model_SaleReceipt();
$QSaleRefund = new Application_Model_SaleRefund();
$QDistributor = new Application_Model_Distributor();
$QAccountType = new Application_Model_AccountType();
$QBankAccountMy = new Application_Model_BankAccountMySide();
$QWarehouse = new Application_Model_Warehouse();

$contactdetail = $QContactDetail->getClientDetail($sn);


if($contactdetail[0]['type'] == 1) {

	$where = $QMarket->getAdapter()->quoteInto('sn =?',$contactdetail[0]['doc_no']);
	$data = $QMarket->fetchAll($where);

}elseif($contactdetail[0]['type'] == 2) {

	$where = $QMarket->getAdapter()->quoteInto('sn =?',$contactdetail[0]['doc_no']);
	$data = $QMarket->fetchAll($where);

}elseif($contactdetail[0]['type'] == 3) {

	$where = $QSaleReceipt->getAdapter()->quoteInto('document_no =?',$contactdetail[0]['doc_no']);
	$data = $QSaleReceipt->fetchAll($where);

}elseif($contactdetail[0]['type'] == 4) {

	$where = $QSaleRefund->getAdapter()->quoteInto('code =?',$contactdetail[0]['doc_no']);
	$data = $QSaleRefund->fetchAll($where);

}elseif($contactdetail[0]['type'] == 5) {

}elseif($contactdetail[0]['type'] == 6) {

}


	$this->view->data = $data;

	$this->view->good = $QGood->get_cache();
	$this->view->good_color = $QGoodColor->get_cache();
	$this->view->staff = $QStaff->get_cache();
	$this->view->finance_client = $QFinanceClient->get_cache();
	$this->view->finance_warehouse = $QFinanceWarehouse->get_cache();
	$this->view->store = $QStore->get_cache2();
	$this->view->distributor = $QDistributor->get_cache();
	$this->view->accounttype = $QAccountType->get_cache();
	$this->view->bankaccount = $QBankAccountMy->get_cache();
	$this->view->warehouse = $QWarehouse->get_cache();

	$this->view->contactdetail = $contactdetail;

	$messages = $flashMessenger->setNamespace('error')->getMessages();
	$this->view->messages = $messages;

	$messages_success = $flashMessenger->setNamespace('success')->getMessages();
	$this->view->messages_success = $messages_success;

	$this->_helper->viewRenderer->setRender('client-contact-note/client-contact-note-detail');

?>