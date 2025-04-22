<?php
$sort           = $this->getRequest()->getParam('sort', 'return_date');
$desc           = $this->getRequest()->getParam('desc', 0);
$page           = $this->getRequest()->getParam('page', 1);

$rq = $this->getRequest()->getParam('rq');
$code = $this->getRequest()->getParam('code');
$fullname = $this->getRequest()->getParam('fullname');
$position = $this->getRequest()->getParam('position');

$rm_fullname = $this->getRequest()->getParam('rm_fullname');
$mg_fullname = $this->getRequest()->getParam('mg_fullname');

$created_at_to = $this->getRequest()->getParam('created_at_to');
$created_at_from = $this->getRequest()->getParam('created_at_from');

$return_at_to  = $this->getRequest()->getParam('return_at_to');
$return_at_from = $this->getRequest()->getParam('return_at_from');

$grade = $this->getRequest()->getParam('grade');
// $type = $this->getRequest()->getParam('type');

$hrs_department_id = $this->getRequest()->getParam('hrs_department_id');

$export = $this->getRequest()->getParam('export',0);

$limit = LIMITATION;
$total = 0;

$params = array_filter( array(
    'rq'               => $rq,
    'code'             => $code,
    'fullname'         => $fullname,
    'position'         => $position,
    'rm_fullname'      => $rm_fullname,
    'mg_fullname'      => $mg_fullname,
    'created_at_to'    => $created_at_to,
    'created_at_from'  => $created_at_from,
    'return_at_to'     => $return_at_to,
    'return_at_from'   => $return_at_from,
    'grade'            => $grade,
    // 'type'             => $type
    'hrs_department_id'=> $hrs_department_id
));

$params['sort'] = $sort;
$params['desc'] = $desc;

$params['return'] = true;

$QBL = new Application_Model_BorrowingList();

if (isset($export) and $export){

    $data = $QBL->fetchPagination($page, null, $total, $params);
    // $QBL->export_change_sales_order($data);
    
}else{
    $data = $QBL->fetchPagination($page, $limit, $total, $params);
}

$db = Zend_Registry::get('db');
$select = $db->select()->from(array('p' => 'oppohr.department'),array('p.id','p.name'))->order('p.name');
$department = $db->fetchAll($select);

$this->view->department   = $department;

$this->view->data   = $data;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'warehouse/borrowing-return-list/'.( $params ? '?'.http_build_query($params).'&' : '?' );
$this->view->warehouses = $warehouses;
$this->view->distributors = $distributors;
$this->view->offset = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;