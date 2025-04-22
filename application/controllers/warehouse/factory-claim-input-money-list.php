<?php
$sort           = $this->getRequest()->getParam('sort', 'return_date');
$desc           = $this->getRequest()->getParam('desc', 0);
$page           = $this->getRequest()->getParam('page', 1);


$job_number  = $this->getRequest()->getParam('job_number');
$old_imei  = $this->getRequest()->getParam('old_imei');
$warehouse  = $this->getRequest()->getParam('warehouse');
$request_at_from  = $this->getRequest()->getParam('request_at_from');
$request_at_to  = $this->getRequest()->getParam('request_at_to');

$limit = LIMITATION;
$total = 0;

$params = array_filter( array(
    'job_number' => $job_number,
    'old_imei' => $old_imei,
    'warehouse' => $warehouse,
    'request_at_from' => $request_at_from,
    'request_at_to' => $request_at_to,
    ));

$params['sort'] = $sort;
$params['desc'] = $desc;

$params['action'] = 'input_money';

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$QFC = new Application_Model_FactoryClaim();

$QWarehouse = new Application_Model_Warehouse();
$warehouse_cache = $QWarehouse->get_cache();

$QWG = new Application_Model_WarehouseGroupUser();

$get_warehouse = $QWG->currentWarehouseGroupUserList($userStorage->id);

$data = $QFC->fetchPagination($page, $limit, $total, $params);

$this->view->warehouse_cache = $warehouse_cache;
$this->view->get_warehouse = $get_warehouse;
$this->view->data   = $data;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'warehouse/factory-claim-get-money-list/'.( $params ? '?'.http_build_query($params).'&' : '?' );
$this->view->warehouses = $warehouses;
$this->view->distributors = $distributors;
$this->view->offset = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;