<?php

$sort           = $this->getRequest()->getParam('sort', 1);
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$document_type = $this->getRequest()->getParam('document_type');

$doc_created_at_from = $this->getRequest()->getParam('doc_created_at_from');
$doc_created_at_to = $this->getRequest()->getParam('doc_created_at_to');

$export  = $this->getRequest()->getParam('export', 0);

$this->_helper->viewRenderer->setRender('report-stock-daily');

$limit = LIMITATION;
//$limit = 2;
$total = 0;

$params = array(
    'document_type'       => $document_type,
    'doc_created_at_from' => $doc_created_at_from,
    'doc_created_at_to'   => $doc_created_at_to,
);

$QDailyStockNew = new Application_Model_DailyStockNew();

$params['export'] = $export;

$params['sort'] = $sort;
$params['desc'] = $desc;

if ( isset($export) && $export == 1 ) {
    $data = $QDailyStockNew->fetchPagination($page, $limit, $total, $params);
    // $this->_exportExcelRewardCreditNote_Wait_Confirm($data);
}else {  //show list
    $data = $QDailyStockNew->fetchPagination($page, $limit, $total, $params);
}

//print_r($data);

$this->view->data  = $data;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/report-stock-daily/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

?>