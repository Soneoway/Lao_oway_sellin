<?php

$client_name           			= $this->getRequest()->getParam('client_name');
$short_name            			= $this->getRequest()->getParam('short_name');
$level            	   			= $this->getRequest()->getParam('level');
$date_form            			= $this->getRequest()->getParam('cooperate_date_form');
$date_to            			= $this->getRequest()->getParam('cooperate_date_to');
$status							= $this->getRequest()->getParam('status');
$export 						= $this->getRequest()->getParam('export', 0);

$page   						= $this->getRequest()->getParam('page', 1);
$limit  = LIMITATION;
$total  = 0;

$QClient = new Application_Model_Client();

$QStaff = new Application_Model_Staff();
$staff = $QStaff->get_cache();

$params = array(
	'client_name'	=> $client_name,
	'short_name'	=> $short_name,
	'level'			=> $level,
	'date_form'		=> $date_form,
	'date_to'		=> $date_to,
	'status'		=> $status
);

$client = $QClient->fetchPagination($page, $limit, $total, $params);

if (isset($export) and $export) {
	$data = $QClient->fetchPagination($page, null, $total, $params);
	$this->_exportClientExcel($data);
}


$this->view->staff       = $staff;
$this->view->client 	 = $client;
$this->view->params      = $params;
$this->view->limit       = $limit;
$this->view->total       = $total;
$this->view->url         = HOST.'sales/client-management/'.( $params ? '?'.http_build_query($params).'&' : '?' );


if($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setRender('partials/client-list');
} else {
    $flashMessenger               = $this->_helper->flashMessenger;
    $messages_success             = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages_success = $messages_success;

    $messages                     = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages         = $messages;
}


?>