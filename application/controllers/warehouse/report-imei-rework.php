<?php

$sort           = $this->getRequest()->getParam('sort', 'p.created_date');
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$id = $this->getRequest()->getParam('id');
$co_sn = $this->getRequest()->getParam('co_sn');
$co_ref = $this->getRequest()->getParam('co_ref');
$co_end_sn = $this->getRequest()->getParam('co_end_sn');
$co_end_ref = $this->getRequest()->getParam('co_end_ref');
$po_sn = $this->getRequest()->getParam('po_sn');
$po_ref = $this->getRequest()->getParam('po_ref');

$created_at_to = $this->getRequest()->getParam('created_at_to');
$created_at_from = $this->getRequest()->getParam('created_at_from',date('d/m/Y', strtotime('-30 day')));
// $created_at_from = $this->getRequest()->getParam('created_at_from',date('d/m/Y'));

$cat_id       = $this->getRequest()->getParam('cat_id');
$good_id       = $this->getRequest()->getParam('good_id');
$good_color_id = $this->getRequest()->getParam('good_color_id');
$good_type = $this->getRequest()->getParam('good_type');

$export = $this->getRequest()->getParam('export',0);

$limit = LIMITATION;
$total = 0;

$params = array_filter( array(
    'id'               => $id,
    'co_sn'            => $co_sn,
    'co_ref'           => $co_ref,
    'po_sn'            => $po_sn,
    'po_ref'           => $po_ref,
    'created_at_to'    => $created_at_to,
    'created_at_from'  => $created_at_from,
    'cat_id'           => $cat_id,
    'good_id'          => $good_id,
    'good_color_id'    => $good_color_id,
    'good_type'        => $good_type
));

$params['sort'] = $sort;
$params['desc'] = $desc;

$QLSIR = new Application_Model_LogSwapImeiRework();

if (isset($export) and $export){
    
    $data = $QLSIR->fetchPagination($page, null, $total, $params);

    if($export == '1'){
        $this->exportReportImeiRework($data);
    }

}else{
    $data = $QLSIR->fetchPagination($page, $limit, $total, $params);
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

$this->view->data   = $data;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'warehouse/report-imei-rework/'.( $params ? '?'.http_build_query($params).'&' : '?' );
$this->view->offset = $limit*($page-1);