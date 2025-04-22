<?php
$sort           = $this->getRequest()->getParam('sort', 'p.created_date');
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$rq = $this->getRequest()->getParam('rq');
$co = $this->getRequest()->getParam('co');
$code = $this->getRequest()->getParam('code');
$fullname = $this->getRequest()->getParam('fullname');
$position = $this->getRequest()->getParam('position');
$area = $this->getRequest()->getParam('area');

$created_at_to = $this->getRequest()->getParam('created_at_to');
// $created_at_from = $this->getRequest()->getParam('created_at_from',date('d/m/Y', strtotime('-30 day')));
$created_at_from = $this->getRequest()->getParam('created_at_from',date('d/m/Y'));

$return_at_to  = $this->getRequest()->getParam('return_at_to');
$return_at_from = $this->getRequest()->getParam('return_at_from');

$grade = $this->getRequest()->getParam('grade');
// $type = $this->getRequest()->getParam('type');

$hrs_department_id = $this->getRequest()->getParam('hrs_department_id');

$status = $this->getRequest()->getParam('status');
$missing = $this->getRequest()->getParam('missing');

$cat_id        = $this->getRequest()->getParam('cat_id');
$good_id       = $this->getRequest()->getParam('good_id');
$good_color_id = $this->getRequest()->getParam('good_color_id');

$export = $this->getRequest()->getParam('export',0);

$limit = LIMITATION;
$total = 0;

$params = array_filter( array(
    'rq'               => $rq,
    'co'               => $co,
    'code'             => $code,
    'fullname'         => $fullname,
    'position'         => $position,
    'area'             => $area,
    'created_at_to'    => $created_at_to,
    'created_at_from'  => $created_at_from,
    'return_at_to'     => $return_at_to,
    'return_at_from'   => $return_at_from,
    'grade'            => $grade,
    // 'type'             => $type,
    'hrs_department_id'=> $hrs_department_id,
    'status'           => $status,
    'missing'          => $missing,
    'cat_id'           => $cat_id,
    'good_id'          => $good_id,
    'good_color_id'    => $good_color_id,
    'export'           => $export
));

$params['sort'] = $sort;
$params['desc'] = $desc;

$params['report'] = true;

$QBL = new Application_Model_BorrowingList();

if (isset($export) and $export){
    
    $data = $QBL->fetchPagination($page, null, $total, $params);

    if($export == '1'){
        $this->exportBorrowingReport($data);
    }
    if($export == '2'){
        $this->exportBorrowingReportImei($data);
    }
    if($export == '3'){
        $this->exportBorrowingReportImeiPAnn($data);
    }

}else{
    $data = $QBL->fetchPagination($page, $limit, $total, $params);
}

$QGoodCategory  = new Application_Model_GoodCategory();
$goodCategories = $QGoodCategory->get_cache();

$QGood          = new Application_Model_Good();
$goods_cached   = $QGood->get_cache();

$QGoodColor     = new Application_Model_GoodColor();
$goodColors     = $QGoodColor->get_cache();

$this->view->goods_cached   = $goods_cached;
$this->view->goodColors     = $goodColors;
$this->view->goodCategories = $goodCategories;

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
$this->view->url    = HOST.'warehouse/borrowing-report/'.( $params ? '?'.http_build_query($params).'&' : '?' );
$this->view->warehouses = $warehouses;
$this->view->distributors = $distributors;
$this->view->offset = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;