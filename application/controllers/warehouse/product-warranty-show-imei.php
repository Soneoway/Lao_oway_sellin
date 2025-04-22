<?php

	$sn = $this->getRequest()->getParam('sn');
	$type = $this->getRequest()->getParam('type');
	$warehouse_id = 98;

	$QImei = new Application_Model_Imei();
    
    $total = 0;

    $Imei = $QImei->getImeiByProductWarranty($sn,$warehouse_id,$type,$total);
    $d = new DateTime();
	$date = $d->format('Ymd');
	if($type == "co"){

		$this->view->total = $total;
		$this->view->imeiarr = $Imei;
		$this->view->title = "Change Order Show Imei";
		$this->view->headtable = "Change Order Imei";
		$this->view->filename_excel = "Imei_co_".$date;

	}else{
		$this->view->total = $total;
		$this->view->imeiarr = $Imei;
		$this->view->title = "PURCHASE ORDER Show Imei";
		$this->view->headtable = "PURCHASE ORDER Imei";
		$this->view->filename_excel = "Imei_po_".$date;
	}
	$this->view->type = $type;
	

?>