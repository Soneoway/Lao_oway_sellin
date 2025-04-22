<?php
	//echo "OK";
	$this->_helper->layout->disableLayout();
	
	$this->_helper->viewRenderer->setRender('cn-view-imei-return-print');

	$QImei = new Application_Model_Imei();

	$QDistributor = new Application_Model_Distributor();
    $distributors = $QDistributor->get_cache();
    
    if(isset($_SESSION["imei_list"]))
	{
		$imei_list = $_SESSION["imei_list"];	
		$invoice_list = $QImei->fetchSummaryImeiByInv($imei_list);

		//Get Retailer
	  	$distributor_id=$invoice_list[0]["distributor_id"];
        $invoice_list['retailer_name'] = isset($distributors[$distributor_id]) ? $distributors[$distributor_id] : '-';
	}
   
    //print_r($distributors);
	//count($invoice_list);	
    $this->view->invoice_list= $invoice_list;
    $this->view->invoice_imei_list= $imei_list;

	//print_r($_SESSION['imei_list']);

$sn=$this->getRequest()->getParam('sn');
$creditnote_type=$this->getRequest()->getParam('creditnote_type');
$distributor_id=$this->getRequest()->getParam('d_id');
$chanel=$this->getRequest()->getParam('chanel');

if ($sn) {
    
	$QCreditNote = new Application_Model_CreditNote();
	$CreditNoteData = $QCreditNote->getCredit_Note_View($distributor_id,$sn);
	$ImeiReturn = $QCreditNote->getCredit_Note_Return_List_imei($distributor_id,$sn);	
	//print_r($CreditNoteData);
	//echo "</br></br></br>";
	//print_r($ImeiReturn);
    $this->view->sales = $CreditNoteData;
    $this->view->product_list = $Product_List;
    $this->view->imei_return_list = $ImeiReturn;
    $this->view->creditnote_type = $creditnote_type;

}

?>