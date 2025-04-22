<?php
$flashMessenger = $this->_helper->flashMessenger;

$id                    = $this->getRequest()->getParam('id');
$action_type		   = $this->getRequest()->getParam('action_type');

$QDistributor = new Application_Model_Distributor();
$QCostItem = new Application_Model_CostItem();
$QContactNote = new Application_Model_ContactNote();
$QFinanceClient = new Application_Model_FinanceClient();

if($id) {

	$ContactRowSet = $QContactNote->find($id);
	$ContactNote       = $ContactRowSet->current();

	if (!$ContactNote) {
		$flashMessenger->setNamespace('error')->addMessage('ບັນຊີອົງກອນບໍ່ຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/contact-note');
	}

	$where = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$ContactNote->d_id);
    $financeClient = $QFinanceClient->fetchAll($where);

    $where2 = $QFinanceClient->getAdapter()->quoteInto('id =?',$ContactNote->finance_client_id);
    $financeClient_arr = $QFinanceClient->fetchRow($where2);

    $where3 = $QDistributor->getAdapter()->quoteInto('id =?',$financeClient_arr->distributor_y_id);
    $distributor_arr = $QDistributor->fetchRow($where3);


    $this->view->financeClient = $financeClient;
	$this->view->ContactNote = $ContactNote;
	$this->view->distributor_arr = $distributor_arr;

}

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor = $QDistributor->fetchAll($where);

$this->view->distributor = $distributor;
$this->view->cost_item = $QCostItem->get_cache();
$this->view->action_type = $action_type;

$this->_helper->viewRenderer->setRender('finance-clients-contact-management/add-contact-note');
?>