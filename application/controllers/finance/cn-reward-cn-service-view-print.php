<?php
//echo "OK";
$this->_helper->layout->disableLayout();

$this->_helper->viewRenderer->setRender('cn-reward-cn-service-view-print');

$QImei = new Application_Model_Imei();

$QDistributor = new Application_Model_Distributor();
$distributors = $QDistributor->get_cache();

if (isset($_SESSION["imei_list"])) {
	$imei_list    = $_SESSION["imei_list"];
	$invoice_list = $QImei->fetchSummaryImeiByInv($imei_list);

	//Get Retailer
	$distributor_id                = $invoice_list[0]["distributor_id"];
	$invoice_list['retailer_name'] = isset($distributors[$distributor_id])?$distributors[$distributor_id]:'-';
}

//print_r($distributors);
//count($invoice_list);
$this->view->invoice_list      = $invoice_list;
$this->view->invoice_imei_list = $imei_list;

//print_r($_SESSION['imei_list']);

$sn              = $this->getRequest()->getParam('sn');
$creditnote_type = $this->getRequest()->getParam('creditnote_type');
$distributor_id  = $this->getRequest()->getParam('d_id');

$params = array(
	'sn'   => $sn,
	'd_id' => $distributor_id,
	'cn_status' => 2,
	
);

$QCreditNote = new Application_Model_CreditNote();
if ($creditnote_type == 'cn') {
	$oppo = $QCreditNote->cn_view_print($params);
}
//print_r($oppo);
$this->view->imei             = $imei;
$this->view->oppo             = $oppo;
$this->view->imei_return_list = $ImeiReturn;
$this->view->creditnote_type  = $creditnote_type;

?>