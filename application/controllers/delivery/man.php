<?php 
$name   = $this->getRequest()->getParam('name');
$page   = $this->getRequest()->getParam('page', 1);
$export = $this->getRequest()->getParam('export', 0);
$limit  = LIMITATION;
$total  = 0;
$params = array(
    'name' => $name,
    'export' => $export,
);

if (isset($export) && $export) {

}

$QDeliveryMan = new Application_Model_DeliveryMan();
$this->view->men    = $QDeliveryMan->fetchPagination($page, $limit, $total, $params);
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->page   = $page;
$this->view->offset = $limit*($page-1);
$this->view->params = $params;
$this->view->url    = HOST.'delivery/man'.( $params ? '?'.http_build_query($params).'&' : '?' );

$flashMessenger = $this->_helper->flashMessenger;
$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();