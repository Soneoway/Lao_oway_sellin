<?php
$flashMessenger = $this->_helper->flashMessenger;

$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$distributor_ids 	= $this->getRequest()->getParam('distributor_ids');
$doc_type			= $this->getRequest()->getParam('doc_type');
$regional 			= $this->getRequest()->getParam('regional');
$finance_client     = $this->getRequest()->getParam('finance_client');
$date_from			= $this->getRequest()->getParam('date_from',date('Y-m-01'));
$date_to 			= $this->getRequest()->getParam('date_to',date('Y-m-d'));
$export				= $this->getRequest()->getParam('export');

$limit = LIMITATION;
$total = 0;

$QContactDetail = new Application_Model_ContactDetail();
$QDistributor = new Application_Model_Distributor();
$QRegionalMarket = new Application_Model_RegionalMarket();
$QFinanceClient = new Application_Model_FinanceClient();
$QStore = new Application_Model_Store();
$QStaff = new Application_Model_Staff();

$params = array(
	'distributor_ids' 		=> $distributor_ids,
	'doc_type'				=> $doc_type,
	'regional'				=> $regional,
	'date_from'				=> $date_from,
	'date_to'				=> $date_to,
	'finance_client'		=> $finance_client
);

if($distributor_ids) {

	$where = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$distributor_ids);
	$finance_client_arr = $QFinanceClient->fetchAll($where);

	$this->view->finance_client_arr = $finance_client_arr;
}

$clientContact = $QContactDetail->fetchPagination($page, $limit, $total, $params);

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor = $QDistributor->fetchAll($where);

if(isset($export) && $export) {
	$data = $QContactDetail->fetchPagination($page, null, $total, $params);

	// print_r($data); exit();

	$this->_exportClientContactNote($data);

}


$this->view->distributor_cache = $QDistributor->get_cache4();
$this->view->store_cache = $QStore->get_cache();

$this->view->clientContact = $clientContact;
$this->view->distributor = $distributor;
$this->view->regional = $QRegionalMarket->get_cache();
$this->view->staff = $QStaff->get_cache();

$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/client-contact-note/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$this->_helper->viewRenderer->setRender('client-contact-note/client-contact-note');

?>