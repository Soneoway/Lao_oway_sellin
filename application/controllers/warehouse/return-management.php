<?php
$sort           = $this->getRequest()->getParam('sort', 'created_at');
$desc           = $this->getRequest()->getParam('desc', 1);

$page           = $this->getRequest()->getParam('page', 1);

$imei_sn        = $this->getRequest()->getParam('imei_sn');
$return_sn      = $this->getRequest()->getParam('return_sn');

$limit = LIMITATION;
$total = 0;

$params = array_filter( array(
    'imei_sn' => $imei_sn,
    'return_sn' => $return_sn,
    'sort' => $sort,
    'desc' => $desc,
));

$QImeiReturn = new Application_Model_ImeiReturn();

$list = $QImeiReturn->fetchPagination($page, $limit, $total, $params);

$this->view->list = $list;

$this->view->desc     = $desc;
$this->view->sort     = $sort;
$this->view->params   = $params;
$this->view->limit    = $limit;
$this->view->total    = $total;
$this->view->url      = HOST.'warehouse/return-management/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$QWarehouse = new Application_Model_Warehouse();
$this->view->warehouses_cached = $QWarehouse->get_cache();

$QStaff = new Application_Model_Staff();
$this->view->staffs_cached = $QStaff->get_cache();

$this->view->offset   = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;