<?php
$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');
$action_type = $this->getRequest()->getParam('action_type');

$QDistributor = new Application_Model_Distributor();
$QAccountType = new Application_Model_AccountType();
$QSaleRefund = new Application_Model_SaleRefund();
$QFinanceClient = new Application_Model_FinanceClient();
$QStore = new Application_Model_Store();
$QWarehouse = new Application_Model_Warehouse();
$QBankAccountMy = new Application_Model_BankAccountMySide();
$QAccountOrg = new Application_Model_AccountingOrganization();


if($id){

	$saleRowSet = $QSaleRefund->find($id);
	$saleRefund       = $saleRowSet->current();

	if (!$saleRefund) {
		$flashMessenger->setNamespace('error')->addMessage('ບັນຊີອົງກອນບໍ່ຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/sale-refund');
	}

	$where = array();
	$where[] = $QFinanceClient->getAdapter()->quoteInto('status = ?',1);
	$where[] = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id = ?',$saleRefund->d_id);
	$finance_client_arr = $QFinanceClient->fetchAll($where);


	$RowSet = $QStore->find($saleRefund->refund_dealer);
	$check_store = $RowSet->current();

	if(!$check_store) {

		$where2 = $QWarehouse->getAdapter()->quoteInto('id = ?',$saleRefund->refund_dealer);
		$refund_dealer = $QWarehouse->fetchAll($where2);

		$where7 = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$refund_dealer[0]->id);
		$distributor_arr = $QDistributor->fetchRow($where7);

	}else{

		$where2 = $QStore->getAdapter()->quoteInto('id = ?',$saleRefund->refund_dealer);
		$refund_dealer = $QStore->fetchAll($where2);

		$where7 = $QDistributor->getAdapter()->quoteInto('id =?',$refund_dealer[0]->d_id);
		$distributor_arr = $QDistributor->fetchRow($where7);

	}

	$where3 = $QBankAccountMy->getAdapter()->quoteInto('d_id =?',$saleRefund->d_id);
    $bankaccount = $QBankAccountMy->fetchAll($where3);


    $where6 = $QFinanceClient->getAdapter()->quoteInto('id =?',$saleRefund->finance_client_id);
    $fclient = $QFinanceClient->fetchRow($where6);

	$where4 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$fclient->distributor_m_id);
	$account_my = $QAccountOrg->fetchRow($where4);

	$where5 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$fclient->distributor_y_id);
	$account_you = $QAccountOrg->fetchRow($where5);

	// print_r($account_my); die();

	$this->view->distributor_arr = $distributor_arr;
	$this->view->account_you  = $account_you;
	$this->view->account_my  = $account_my;
	$this->view->bankaccount = $bankaccount;
	$this->view->refund_dealer = $refund_dealer;
	$this->view->finance_client_arr = $finance_client_arr;

}

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor = $QDistributor->fetchAll($where);

$this->view->saleRefund = $saleRefund;

$this->view->action_type = $action_type;
$this->view->accountType = $QAccountType->get_cache();
$this->view->distributor = $distributor;
$this->_helper->viewRenderer->setRender('finance-clients-contact-management/add-sale-refund');

?>