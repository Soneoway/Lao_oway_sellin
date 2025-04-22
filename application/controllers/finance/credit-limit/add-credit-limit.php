<?php
$flashMessenger = $this->_helper->flashMessenger;

$id 			= $this->getRequest()->getParam('id');
$action_type 	= $this->getRequest()->getParam('action_type');

$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$db = Zend_Registry::get('db');

$QCreditLimit = new Application_Model_CreditLimit();
$QDistributor = new Application_Model_Distributor();
$QFinanceClient = new Application_Model_FinanceClient();

if($id) {

	$CreditRowSet = $QCreditLimit->find($id);
	$creditLimit       = $CreditRowSet->current();

	if (!$creditLimit) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/credit-limit');
	}

	$where = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$creditLimit->d_id);
	$financeclient = $QFinanceClient->fetchAll($where);

	$this->view->financeclient = $financeclient;
	$this->view->creditLimit = $creditLimit;
	$this->view->action_type = $action_type;

}

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor = $QDistributor->fetchAll($where);

$this->view->distributor = $distributor;

$this->_helper->viewRenderer->setRender('credit-limit/add-credit');
?>