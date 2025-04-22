<?php
$flashMessenger = $this->_helper->flashMessenger;

$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$distributor_ids	= $this->getRequest()->getParam('distributor_ids');
$finance_client		= $this->getRequest()->getParam('finance_client');
$form_date			= $this->getRequest()->getParam('form_date');
$to_date			= $this->getRequest()->getParam('to_date',date('Y-m-d'));
$code 				= $this->getRequest()->getParam('code');
$export				= $this->getRequest()->getParam('export');

$limit = LIMITATION;
$total = 0;

$params = array(
	'distributor_ids'		=> $distributor_ids,
	'finance_client'		=> $finance_client,
	'form_date'				=> $form_date,
	'to_date'				=> $to_date,
	'code'					=> $code,
	'export'				=> $export
);

$QFinanceClient = new Application_Model_FinanceClient();
$QDistributor = new Application_Model_Distributor();

if($distributor_ids) {
	$where = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$distributor_ids);
	$financeclient = $QFinanceClient->fetchAll($where);

	$this->view->financeclient = $financeclient;
}

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor = $QDistributor->fetchAll($where);

$distributor_recon = $QFinanceClient->DistributorReconciliation($page, $limit, $total, $params);

if(isset($export) && $export){
	$data = $QFinanceClient->DistributorReconciliation($page, null, $total, $params);

	$this->_exportDistributorReconciliation($data);
}

$this->view->distributor_recon = $distributor_recon;
$this->view->distributor = $distributor;

$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/distributor-account-reconciliation/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;


$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$this->_helper->viewRenderer->setRender('finance-clients-contact-management/distributor-account-reconcilication');

?>