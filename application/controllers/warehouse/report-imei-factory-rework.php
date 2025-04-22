<?php

$sort           = $this->getRequest()->getParam('sort', 'p.flag_rework_date');
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$imei = $this->getRequest()->getParam('imei');
$imei_munti = $this->getRequest()->getParam('imei_munti');
$imei_munti = array_values(array_unique($imei_munti));

$flag_rework_date_to = $this->getRequest()->getParam('flag_rework_date_to');
$flag_rework_date_from = $this->getRequest()->getParam('flag_rework_date_from',date('d/m/Y', strtotime('-30 day')));

$export = $this->getRequest()->getParam('export',0);

$limit = LIMITATION;
$total = 0;

$params = array_filter( array(
    'imei'             => $imei,
    'imei_munti'       => $imei_munti,
    'flag_rework_to'   => $flag_rework_to,
    'flag_rework_from' => $flag_rework_from
));

$params['sort'] = $sort;
$params['desc'] = $desc;

$QImei = new Application_Model_Imei();

if (isset($export) and $export){
    
    $data = $QImei->fetchPaginationFactoryRework($page, null, $total, $params);

    if($export == '1'){
        $this->exportReportImeiFactoryRework($data);
    }

}else{
    $data = $QImei->fetchPaginationFactoryRework($page, $limit, $total, $params);
}

$this->view->data   = $data;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'warehouse/report-imei-factory-rework/'.( $params ? '?'.http_build_query($params).'&' : '?' );
$this->view->offset = $limit*($page-1);