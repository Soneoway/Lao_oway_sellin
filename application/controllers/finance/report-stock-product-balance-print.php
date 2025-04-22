<?php

set_time_limit(0);
ini_set('memory_limit', -1);
$this->_helper->layout->disableLayout();


$sort           = $this->getRequest()->getParam('sort', 1);
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$cat_id = $this->getRequest()->getParam('cat_id');
$good_id = $this->getRequest()->getParam('good_id');
$good_color = $this->getRequest()->getParam('good_color');

$doc_created_at_from = $this->getRequest()->getParam('doc_created_at_from');
$doc_created_at_to = $this->getRequest()->getParam('doc_created_at_from');

$limit = LIMITATION;
//$limit = 2;
$total = 0;

$this->_helper->viewRenderer->setRender('report-stock-product-balance-print');

$params = array_filter(array(
    'sn' => $sn,
    'created_by' => $created_by,
    'cat_id' => $cat_id,
    'good_id' => $good_id,
    'good_color' => $good_color,
    'warehouse_id' => $warehouse_id,
	'doc_created_at_from' => $doc_created_at_from,
    'doc_created_at_to'   => $doc_created_at_to,
    ));

$QDailyStockBalance = new Application_Model_DailyStockBalance();

$data = $QDailyStockBalance->fetchPagination($page, $limit, $total, $params);

$this->view->data = $data;
$this->view->params = $params;

//print_r($_GET);
?>