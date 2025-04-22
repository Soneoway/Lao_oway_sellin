<?php
$flashMessenger = $this->_helper->flashMessenger;

$id =$this->getRequest()->getParam('id');
$is_dealer 	= $this->getRequest()->getParam('is_dealer');
$type = $this->getRequest()->getParam('type');

$QAccountType = new Application_Model_AccountType();
$QStore = new Application_Model_Store();
$QDistributor = new Application_Model_Distributor();
$QWarehouse = new Application_Model_Warehouse();
$QSaleReceipt = new Application_Model_SaleReceipt();
$QFinanceClient = new Application_Model_FinanceClient();
$QBankAccountMy = new Application_Model_BankAccountMySide();
$QPaySlip = new Application_Model_PaySlip();
$QAccountOrg = new Application_Model_AccountingOrganization();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();

if($id) {

	$saleRowSet = $QSaleReceipt->find($id);
	$saleReceipt       = $saleRowSet->current();

	if (!$saleReceipt) {
		$flashMessenger->setNamespace('error')->addMessage('ບັນຊີອົງກອນບໍ່ຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/sale-receipt');
	}

	if($saleReceipt->store_id == '') {
		$is_dealer = 1;

		$where = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$saleReceipt->warehouse_id);
		$distributor = $QDistributor->fetchRow($where);

		$where2 = $QFinanceClient->getAdapter()->quoteInto('distributor_y_id =?',$distributor->id);
		$financeClient = $QFinanceClient->fetchAll($where2);

		$where5 = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$distributor->warehouse_id);
		$suppy = $QDistributor->fetchRow($where5);

		$where4 = $QBankAccountMy->getAdapter()->quoteInto('d_id =?',$suppy->id);
		$bankaccount = $QBankAccountMy->fetchAll($where4);

		$where6 = $QPaySlip->getAdapter()->quoteInto('sale_receipt_no =?',$saleReceipt->document_no);
		$slip = $QPaySlip->fetchAll($where6);

		$where9 = $QFinanceClient->getAdapter()->quoteInto('id =?',$saleReceipt->finance_client);
		$financeClient_arr = $QFinanceClient->fetchRow($where9);

		$where7 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$financeClient_arr->distributor_y_id);
		$accountOrgYou = $QAccountOrg->fetchRow($where7);

		$where8 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$financeClient_arr->distributor_m_id);
		$accountOrgMy = $QAccountOrg->fetchRow($where8);


	}else{

		$is_dealer = '';

		$where = $QStore->getAdapter()->quoteInto('id =?',$saleReceipt->store_id);
		$store_arr = $QStore->fetchRow($where);

		$where2 = $QFinanceClient->getAdapter()->quoteInto('distributor_y_id =?',$store_arr->d_id);
		$financeClient = $QFinanceClient->fetchAll($where2);

		$where3 = $QDistributor->getAdapter()->quoteInto('id =?',$store_arr->d_id);
		$distributor = $QDistributor->fetchRow($where3);

		$where9 = $QFinanceClient->getAdapter()->quoteInto('id =?',$saleReceipt->finance_client);
		$financeClient_arr = $QFinanceClient->fetchRow($where9);

		$where5 = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$distributor->warehouse_id);
		$suppy = $QDistributor->fetchRow($where5);

		$where7 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$financeClient_arr->distributor_y_id);
		$accountOrgYou = $QAccountOrg->fetchRow($where7);

		$where8 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$financeClient_arr->distributor_m_id);
		$accountOrgMy = $QAccountOrg->fetchRow($where8);

		$where6 = $QPaySlip->getAdapter()->quoteInto('sale_receipt_no =?',$saleReceipt->document_no);
		$slip = $QPaySlip->fetchAll($where6);


		if(in_array($distributor->warehouse_id, array(36,246))){

			$whare = $QBankAccountMy->getAdapter()->quoteInto('d_id =?',5970);
			$bankaccount = $QBankAccountMy->fetchAll($whare);

		}else{

			$bankaccount = array();

		}

	}

	$this->view->accountOrgYou = $accountOrgYou;
	$this->view->accountOrgMy = $accountOrgMy;
	$this->view->suppy = $suppy;
	$this->view->distributor = $distributor;
	$this->view->financeClient = $financeClient;
	$this->view->bankaccount = $bankaccount;
	$this->view->slip = $slip;
}


if($is_dealer) {

	if(in_array($userStorage->group_id, array(106))) {

		$warehouse_list = [];
		$userWarehouseList = $QWarehouseGroupUser->currentWarehouseGroupUserList($userStorage->id);

		foreach ($userWarehouseList as $key => $value) {
			$warehouse_list[$key] = $value['warehouse_id'];
		}

		$where = $QWarehouse->getAdapter()->quoteInto('id IN (?)',$warehouse_list);
		$warehouse = $QWarehouse->fetchAll($where);

	}else{

		$where = $QWarehouse->getAdapter()->quoteInto('status =?',1);
		$warehouse = $QWarehouse->fetchAll($where);

	}


}else{

	if(in_array($userStorage->group_id, array(95,109,106))) {

		$warehouse_list = [];
		$distributor_list = [];
		$userWarehouseList = $QWarehouseGroupUser->currentWarehouseGroupUserList($userStorage->id);

		foreach ($userWarehouseList as $key => $value) {
			$warehouse_list[$key] = $value['warehouse_id'];
		}

		$where = $QDistributor->getAdapter()->quoteInto('warehouse_id IN (?)',$warehouse_list);
		$distributor_arr = $QDistributor->fetchAll($where);

		foreach($distributor_arr as $key => $value) {
			$distributor_list[$key] = $value['id'];
		}

		$where2 = array();
		$where2[] = $QStore->getAdapter()->quoteInto('d_id IN (?)',$distributor_list);
		$where2[] = $QStore->getAdapter()->quoteInto('status =?',1);
		$store = $QStore->fetchAll($where2);


	}else{

		$where = $QStore->getAdapter()->quoteInto('status =?',1);
		$store = $QStore->fetchAll($where);

	}

}

$this->view->saleReceipt = $saleReceipt;
$this->view->store = $store;
$this->view->warehouse = $warehouse;
$this->view->accountType = $QAccountType->get_cache();
$this->view->is_dealer = $is_dealer;
$this->view->type = $type;
$this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER');

$this->_helper->viewRenderer->setRender('finance-clients-contact-management/add-sale-receipt');

?>