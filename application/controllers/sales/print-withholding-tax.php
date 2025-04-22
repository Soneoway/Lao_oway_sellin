<?php
$wht_sn = $this->getRequest()->getParam('wht_sn');
$wht_running_no = $this->getRequest()->getParam('wht_running_no');
$this->_helper->layout->disableLayout();

$QWithholdingTaxManual = new Application_Model_WithholdingTaxManual();

$array_running_no = explode("\n", trim($wht_running_no));

//print_r($array_running_no);
$params = array(
    'wht_sn' => $wht_sn,
    'wht_running_no' => $array_running_no,
);

$get_resule = $QWithholdingTaxManual->withholding_tax_view($params);
//print_r($get_resule);
$this->view->get_resule   = $get_resule;