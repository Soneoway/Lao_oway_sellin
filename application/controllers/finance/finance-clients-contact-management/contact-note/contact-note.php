<?php
$flashMessenger = $this->_helper->flashMessenger;

$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$distributor_ids 	= $this->getRequest()->getParam('distributor_ids');
$finance_client		= $this->getRequest()->getParam('finance_client');
$doc_no				= $this->getRequest()->getParam('doc_no');
$cost_id			= $this->getRequest()->getParam('cost_id');
$review_date_form	= $this->getRequest()->getParam('review_date_form');
$review_date_to		= $this->getRequest()->getParam('review_date_to');
$status				= $this->getRequest()->getParam('status');
$reconcilia_detail	= $this->getRequest()->getParam('reconcilia_detail');
$business_date_form	= $this->getRequest()->getParam('business_date_form');
$business_date_to	= $this->getRequest()->getParam('business_date_to');
$export 			= $this->getRequest()->getParam('export');

$limit = LIMITATION;
$total = 0;

$QConcatNote = new Application_Model_ContactNote();
$QStaff = new Application_Model_Staff();
$QDistributor = new Application_Model_Distributor();
$QCostItem = new Application_Model_CostItem();
$QFinanceClient = new Application_Model_FinanceClient();

$params = array(
	'distributor_id' 		=> $distributor_ids,
	'finance_client'		=> $finance_client,
	'doc_no'				=> $doc_no,
	'cost_id'				=> $cost_id,
	'review_date_form'		=> $review_date_form,
	'review_date_to'		=> $review_date_to,
	'status'				=> $status,
	'reconcilia_detail'		=> $reconcilia_detail,
	'business_date_form'	=> $business_date_form,
	'business_date_to'		=> $business_date_to
);

if($distributor_ids){

	$where = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id = ?',$distributor_ids);
	$financeclient = $QFinanceClient->fetchAll($where);

	$this->view->financeclient = $financeclient;
}

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor = $QDistributor->fetchAll($where);

$contactNote = $QConcatNote->fetchPagination($page, $limit, $total, $params);

if(isset($export) && $export) {
	$data = $QConcatNote->fetchPagination($page, $limit, $total, $params);

	$this->_exportContactNote($data);
}

$this->view->contactNote = $contactNote;
$this->view->staff = $QStaff->get_cache();
$this->view->distributor = $distributor;
$this->view->costItem = $QCostItem->get_cache();

$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/contact-note/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;


$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$this->_helper->viewRenderer->setRender('finance-clients-contact-management/contact-note');

?>