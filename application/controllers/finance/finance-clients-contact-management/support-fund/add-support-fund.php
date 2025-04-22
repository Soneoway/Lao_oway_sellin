<?php
$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');
$action_type = $this->getRequest()->getParam('action_type');

$QDistributor = new Application_Model_Distributor();
$QCostItem = new Application_Model_CostItem();
$QSupportFund = new Application_Model_SupportFund();
$QFinanceClient = new Application_Model_FinanceClient();

if($id){

	$supportRowSet = $QSupportFund->find($id);
	$supportFund       = $supportRowSet->current();

	if (!$supportFund) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍ່ຖຶກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/support-fund-management');
	}


	$where = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$supportFund->d_id);
	$financeClient = $QFinanceClient->fetchAll($where);


	$this->view->supportFund = $supportFund;
	$this->view->financeClient = $financeClient;

}


$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor = $QDistributor->fetchAll($where);

$where2 = $QCostItem->getAdapter()->quoteInto('category_id IN (?)',array(4));
$costItem = $QCostItem->fetchAll($where2);

$this->view->action_type = $action_type;
$this->view->costItem = $costItem;
$this->view->distributor = $distributor;
$this->_helper->viewRenderer->setRender('finance-clients-contact-management/add-support-fund');

?>