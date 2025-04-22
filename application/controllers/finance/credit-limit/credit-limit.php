<?php
$flashMessenger = $this->_helper->flashMessenger;

$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$distributor_ids 	= $this->getRequest()->getParam('dis_id');
$finance_client		= $this->getRequest()->getParam('finance_client');
$quota_type			= $this->getRequest()->getParam('quota_type');
$area_id			= $this->getRequest()->getParam('area_id');
$effective_status	= $this->getRequest()->getParam('effective_status');
$status 			= $this->getRequest()->getParam('status');
$effective_from		= $this->getRequest()->getParam('effective_from');
$effective_to		= $this->getRequest()->getParam('effective_to');

$limit = LIMITATION;
$total = 0;

$QCreditLimit = new Application_Model_CreditLimit();
$QDistributor = new Application_Model_Distributor();
$QArea = new Application_Model_Area();
$QFinanceClient = new Application_Model_FinanceClient();
$QStaff = new Application_Model_Staff();

$params = array(
	'distributor_id' 	=> $distributor_ids,
	'finance_client'	=> $finance_client,
	'quota_type'		=> $quota_type,
	'area_id'			=> $area_id,
	'effective_status'	=> $effective_status,
	'status'			=> $status,
	'effective_from'	=> $effective_from,
	'effective_to'		=> $effective_to
);

if($distributor_ids) {

	$where = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$distributor_ids);
	$financeclient_arr = $QFinanceClient->fetchAll($where);

	$this->view->financeclient_arr = $financeclient_arr;
}

$where = $QDistributor->getAdapter()->quoteInto('agent_status =?',1);
$distributor = $QDistributor->fetchAll($where);

$credit = $QCreditLimit->fetchPagination($page, $limit, $total, $params);

$this->view->credit = $credit;
$this->view->distributor = $distributor;
$this->view->area = $QArea->get_cache();
$this->view->staff = $QStaff->get_cache();

$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/credit-limit/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$this->_helper->viewRenderer->setRender('credit-limit/index');
?>