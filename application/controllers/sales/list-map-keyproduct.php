<?php
$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$messages_success = $flashMessenger->setNamespace('success')->getMessages();

$sort          = $this->getRequest()->getParam('sort', '');
$page          = $this->getRequest()->getParam('page', 1);
$desc          = $this->getRequest()->getParam('desc', 1);

$limit = LIMITATION;
$total = 0;

$QCsvimport   = new Application_Model_CsvImport();
$list_key = $QCsvimport->get_list_keyproduct($page, $limit, $total, $params);
$this->view->list_key = $list_key;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->page   = $page;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->offset = $limit*($page-1);
$this->view->url    = ( $params ? '?'.http_build_query($params).'&' : '?' );