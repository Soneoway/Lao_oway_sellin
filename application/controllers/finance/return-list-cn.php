<?php
$sort           = $this->getRequest()->getParam('sort', 'add_time');
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$sn              = $this->getRequest()->getParam('sn');
$d_id            = $this->getRequest()->getParam('d_id');
$good_id         = $this->getRequest()->getParam('good_id');
$good_color      = $this->getRequest()->getParam('good_color');
$num             = $this->getRequest()->getParam('num');
$chanel          = $this->getRequest()->getParam('chanel');
$price           = $this->getRequest()->getParam('price');
$total           = $this->getRequest()->getParam('total');
$pay_time        = $this->getRequest()->getParam('payment', 0);
$outmysql_time   = $this->getRequest()->getParam('outmysql_time', 0);
$created_at_to   = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));
$creditnote_type = $this->getRequest()->getParam('creditnote_type', null);

$finance_group   = $this->getRequest()->getParam('finance_group');
$cn_status   = $this->getRequest()->getParam('cn_status');

$QDistributor = new Application_Model_Distributor();
$this->view->finance_group = $QDistributor->getFinanceGroup();

//print_r($_GET);

//$created_at_from = $this->getRequest()->getParam('created_at_from', date('01/m/Y'));

$created_at_from   = $this->getRequest()->getParam('created_at_from', date('d/m/Y', strtotime('0 day')));

$cat_id          = $this->getRequest()->getParam('cat_id');

$export  = $this->getRequest()->getParam('export', 0);

$rank           = $this->getRequest()->getParam('rank');
$this->view->rank = $rank;
$this->view->d_id = $d_id;

$this->_helper->viewRenderer->setRender('return-list-cn');

$limit = LIMITATION;
//$limit = 2;
$total = 0;

$params = array(
    'sn'              => $sn,
    'd_id'            => $d_id,
    'good_id'         => $good_id,
    'good_color'      => $good_color,
    'num'             => $num,
    'price'           => $price,
    'total'           => $total,
    'created_at_to'   => $created_at_to,
    'created_at_from' => $created_at_from,
    'cat_id'          => $cat_id,
    'creditnote_type' => $creditnote_type,
    'chanel'          => $chanel,
    'rank'            => $rank,
    'cn_status'       => $cn_status,
    'finance_group'   => $finance_group
);

$params['isbacks'] = true;
$params['group_sn'] = true;

if ($pay_time)
    $params['payment'] = true;
// else
// 	$params['no_payment'] = true;

if ($outmysql_time)
    $params['outmysql_time'] = true;
// else
// 	$params['no_outmysql_time'] = true;

$QGood           = new Application_Model_Good();
$QGoodColor      = new Application_Model_GoodColor();
$QMarket         = new Application_Model_Market();
$QDistributor    = new Application_Model_Distributor();
$QGoodCategory   = new Application_Model_GoodCategory();
$QCreditNote     = new Application_Model_CreditNote();

//$goods           = $QGood->get_cache();
//$goodColors      = $QGoodColor->get_cache();
$distributors    = $QDistributor->get_cache();
//$good_categories = $QGoodCategory->get_cache();

$params['sort'] = $sort;
$params['desc'] = $desc;

$markets = array();


if ( isset($export) && $export == 1 ) {
    //My_Report::preventExport();
    $credit_note_list = $QCreditNote->getCredit_Note_list_for_export($page, $limit, $total, $params);
    $this->_exportExcelCreditNote($credit_note_list);
}elseif ( isset($export) && $export == 2) {
   //Export List
    $credit_note_list = $QCreditNote->getCredit_Note_list_for_export_list($page, $limit, $total, $params);
    $this->_exportExcelCreditNoteList($credit_note_list);
}elseif ( isset($export) && $export == 3) {
   //My_Report::preventExport();
    $credit_note_list = $QCreditNote->getCredit_Note_list_for_export_use_cn($page, $limit, $total, $params);
    $this->_exportExcelUseCN($credit_note_list);
}elseif ( isset($export) && $export == 4) {
    //My_Report::preventExport();
    $credit_note_invoice = $QCreditNote->getCredit_Note_invoice_for_export_list($page, $limit, $total, $params);
    $this->_exportExcelCreditNoteInvoice($credit_note_invoice);
}elseif ( isset($export) && $export == 5) {
   //My_Report::preventExport();
    $credit_note_list = $QCreditNote->getCredit_Note_list_for_export_new($page, $limit, $total, $params);
    $this->_exportExcelCreditNoteNew($credit_note_list);
}elseif ( isset($export) && $export == 6) {
   //My_Report::preventExport();
    $credit_note_list = $QCreditNote->genCNByModel($params);
    $this->_exportExcelCNByModel($credit_note_list);
}elseif ( isset($export) && $export == 7) {
   //Export By Imei List
    $credit_note_list = $QCreditNote->getCredit_Note_By_Imei_list($params);
    $this->_exportExcelCNByImei($credit_note_list); 
}elseif ( isset($export) && $export == 8) {
   //Export CP By Imei List
    $credit_note_list = $QCreditNote->getCP_By_Imei_list($params);
    $this->_exportExcelCPByImei($credit_note_list);  
}elseif ( isset($export) && $export == 9 ) {
    //Export List
    $credit_note_list = $QCreditNote->getCredit_Note_list_for_export_list($page, $limit, $total, $params);
    $this->_exportExcelCreditNoteListNumberPlus($credit_note_list);      
} else {
  //  $markets_sn = $QMarket->fetchPagination($page, $limit, $total, $params);

    if((isset($sn) ? true: false)==true)
    {
        $credit_note_list = $QCreditNote->getCredit_Note_list_distributor_page($page, $limit, $total, $params);
    }else{
        $credit_note_list = null;
    }
}

//$cn_tmp = $QCreditNote->getCredit_Note_list_distributor_page(null, null, $total, $params);

foreach ($credit_note_list as $key => $m) {
    $credit_note_view = $QCreditNote->getCredit_Note_list($m['distributor_id'], $params);
    $markets[$m['distributor_id']] = $credit_note_view;
}

//print_r($markets);

$this->view->goods           = $goods;
$this->view->goodColors      = $goodColors;
$this->view->markets         = $markets;
$this->view->credit_note_list      = $credit_note_list;
$this->view->markets_sn      = $markets;
$this->view->distributors    = $distributors;
$this->view->good_categories = $good_categories;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/return-list-cn/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();

    $this->_helper->viewRenderer->setRender('partials/list');
}