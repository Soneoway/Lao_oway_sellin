<?php
$flashMessenger = $this->_helper->flashMessenger;

$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$distributor_ids 	= $this->getRequest()->getParam('distributor_ids');
$status				= $this->getRequest()->getParam('status');
$finance_client		= $this->getRequest()->getParam('finance_client');
$business_date_from = $this->getRequest()->getParam('business_date_from');
$business_date_to	= $this->getRequest()->getParam('business_date_to');

$limit = LIMITATION;
$total = 0;

$params = array(
	'distributor_ids'		=> $distributor_ids,
	'status'				=> $status,
	'finance_client'		=> $finance_client,
	'from'					=> $business_date_from,
	'to'					=> $business_date_to
);

$QSupportFund = new Application_Model_SupportFund();
$QStaff = new Application_Model_Staff();
$QDistributor = new Application_Model_Distributor();
$QFinanceClient = new Application_Model_FinanceClient();

if($distributor_ids) {

	$where = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$distributor_ids);
	$financeclient = $QFinanceClient->fetchAll($where);


	$this->view->financeclient = $financeclient;
}

$support_fund = $QSupportFund->fetchPagination($page, $limit, $total, $params);

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor = $QDistributor->fetchAll($where);

$this->view->support_fund = $support_fund;
$this->view->distributor = $distributor;
$this->view->staff = $QStaff->get_cache();

$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/sale-refund/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$this->_helper->viewRenderer->setRender('finance-clients-contact-management/support-fund');

?>