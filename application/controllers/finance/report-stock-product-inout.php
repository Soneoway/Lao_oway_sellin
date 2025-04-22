<?php

$sort           = $this->getRequest()->getParam('sort', 1);
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$cat_id = $this->getRequest()->getParam('cat_id');
$good_id = $this->getRequest()->getParam('good_id');
$good_color = $this->getRequest()->getParam('good_color');

$doc_created_at_from = $this->getRequest()->getParam('doc_created_at_from');
$doc_created_at_to = $this->getRequest()->getParam('doc_created_at_to');

$export  = $this->getRequest()->getParam('export', 0);

$this->_helper->viewRenderer->setRender('report-stock-product-inout');

$limit = LIMITATION;
//$limit = 2;
$total = 0;

//print_r($_GET);
/*$params = array(
    'document_type'       => $document_type,
    'doc_created_at_from' => $doc_created_at_from,
    'doc_created_at_to'   => $doc_created_at_to,
);*/

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

$QDailyStockINOut = new Application_Model_DailyStockINOUT();

//print_r($params);die;
$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

$this->view->doc_created_at_from = $doc_created_at_from;
$this->view->doc_created_at_to = $doc_created_at_to;

if($cat_id)
{
    $QGood = new Application_Model_Good();
    $where = $QGood->getAdapter()->quoteInto('cat_id = ?', $cat_id);
    $goods = $QGood->fetchAll($where, 'name');

    $this->view->goods = $goods;

    if ($good_id) {
        //get goods color
        $where = $QGood->getAdapter()->quoteInto('id = ?', $good_id);
        $good = $QGood->fetchRow($where);

        $aColor = array_filter(explode(',', $good->color));
        if ($aColor) {
            $QGoodColor = new Application_Model_GoodColor();
            $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

            $colors = $QGoodColor->fetchAll($where);
            $this->view->colors = $colors;
        }
    }
}

$params['export'] = $export;

$params['sort'] = $sort;
$params['desc'] = $desc;

if(isset($export) && $export == 1) 
{
    $data = $QDailyStockINOut->fetchPagination($page, $limit, $total, $params);
    $this->_exportExcel_stock_product_inout($data, $params);
}else{  //show list
    $data = $QDailyStockINOut->fetchPagination($page, $limit, $total, $params);
}

//print_r($data);

$this->view->data  = $data;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/report-stock-product-inout/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;





?>
