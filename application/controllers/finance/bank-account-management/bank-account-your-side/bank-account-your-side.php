<?php
$flashMessenger = $this->_helper->flashMessenger;

$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$dis_id            = $this->getRequest()->getParam('dis_id');
$account_pp		   = $this->getRequest()->getParam('account_pp');
$bank_id		   = $this->getRequest()->getParam('bank_id');
$account 		   = $this->getRequest()->getParam('account');
$finance_client    = $this->getRequest()->getParam('finance_client');
$my_bank		   = $this->getRequest()->getParam('my_bank');
$card_no		   = $this->getRequest()->getParam('card_no');

$limit = LIMITATION;
$total = 0;

$params = array(
	'dis_id'				=> $dis_id,
	'account_pp'			=> $account_pp,
	'bank_id'				=> $bank_id,
	'account'				=> $account,
	'finance_client'		=> $finance_client,
	'my_bank'				=> $my_bank,
	'card_no'				=> $card_no
);

$QDistributor = new Application_Model_Distributor();
$QBank = new Application_Model_Bank();
$QFinanceClient = new Application_Model_FinanceClient();
$QBankAccountYourSide = new Application_Model_BankAccountYourSide();
$QBankAccountMySide = new Application_Model_BankAccountMySide();
$QStaff = new Application_Model_Staff();

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor_arr = $QDistributor->fetchAll($where);

if($dis_id) {
	$where = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$dis_id);
	$FinanceClient = $QFinanceClient->fetchAll($where);

	$where2 = $QBankAccountMySide->getAdapter()->quoteInto('d_id =?',$dis_id);
	$bankAccountMy = $QBankAccountMySide->fetchAll($where2);
}

$bankAccountYour = $QBankAccountYourSide->fetchPagination($page, $limit, $total, $params);

$this->view->distributor = $distributor_arr;
$this->view->bank = $QBank->get_cache();
$this->view->financeClient = $FinanceClient;
$this->view->bankAccountYour = $bankAccountYour;
$this->view->bankAccountMy = $bankAccountMy;
$this->view->staffs = $QStaff->get_cache();

$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/bank-account-my-side/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;


$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$this->_helper->viewRenderer->setRender('bank-account-management/bank-account-your-side');

?>