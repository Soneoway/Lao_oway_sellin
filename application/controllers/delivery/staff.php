<?php 
$username   = $this->getRequest()->getParam('username');
$page   = $this->getRequest()->getParam('page', 1);
$export = $this->getRequest()->getParam('export', 0);
$limit  = LIMITATION;
$total  = 0;
$params = array(
    'username' => $username,
    'export' => $export,
);

if (isset($export) && $export) {

}

$QStaff = new Application_Model_Staff();
$this->view->men    = $QStaff->fetchDeliveryPagination($page, $limit, $total, $params);
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->page   = $page;
$this->view->offset = $limit*($page-1);
$this->view->params = $params;
$this->view->url    = HOST.'delivery/staff'.( $params ? '?'.http_build_query($params).'&' : '?' );

$QHub = new Application_Model_Hub();
$this->view->hubs = $QHub->get_cache();

$flashMessenger = $this->_helper->flashMessenger;
$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();