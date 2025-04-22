<?php

$file_pay_slip = $this->getRequest()->getParam('file_pay_slip');
$file_pay_slip_paygroup = $this->getRequest()->getParam('file_pay_slip_paygroup');
$file_pay_slip_mobile = $this->getRequest()->getParam('file_pay_slip_mobile');
$file_pay_order_staff = $this->getRequest()->getParam('file_pay_order_staff');

$params = array(
    'file_pay_slip'        => $file_pay_slip,
    'file_pay_slip_paygroup'        => $file_pay_slip_paygroup
);

$uploaded_dir = HOST . 'public' . DIRECTORY_SEPARATOR . 'files'
            . DIRECTORY_SEPARATOR . 'finance';

if($file_pay_slip_mobile){
	$uploaded_dir = API_IOPPO_STAFF_URL . 'staff_slip_payment/' . $file_pay_slip_mobile . '/';
}

$this->view->file_pay_slip = $uploaded_dir.$file_pay_slip;

if($file_pay_slip_paygroup){
	$this->view->file_pay_slip = $file_pay_slip_paygroup;
}

if($file_pay_order_staff){
	$this->view->file_pay_slip = $file_pay_order_staff;
}

$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setRender('/partials/view-pay-slip');

?>