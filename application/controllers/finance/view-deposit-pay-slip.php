<?php

$file_pay_slip = $this->getRequest()->getParam('file_pay_slip');
$file_pay_slip_paygroup = $this->getRequest()->getParam('file_pay_slip_paygroup');

$params = array(
    'file_pay_slip'        => $file_pay_slip,
    'file_pay_slip_paygroup'        => $file_pay_slip_paygroup
);

$uploaded_dir = HOST . 'public' . DIRECTORY_SEPARATOR . 'files'
            . DIRECTORY_SEPARATOR . 'sales';

$this->view->file_pay_slip = $uploaded_dir.$file_pay_slip;

if($file_pay_slip_paygroup){
	$this->view->file_pay_slip = $file_pay_slip_paygroup;
}

$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setRender('/partials/view-pay-slip');

?>