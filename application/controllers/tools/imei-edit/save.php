<?php
$good_id = $this->getRequest()->getParam('good_id');
$good_color= $this->getRequest()->getParam('good_color');

$distributor_id1 = $this->getRequest()->getParam('distributor_id');
$out_date1 = $this->getRequest()->getParam('out_date');
$sales_sn1 = $this->getRequest()->getParam('sales_sn');
$sales_id1 = $this->getRequest()->getParam('sales_id');
$out_price1 = $this->getRequest()->getParam('out_price');
$out_user1= $this->getRequest()->getParam('out_user');

$id = $this->getRequest()->getParam('id');

if ($distributor_id1 == '') {
    $distributor_id = NULL;
}else{
    $distributor_id = $distributor_id1;
}
if ($out_date1 == '') {
    $out_date = NULL;
}else{
    $out_date = $out_date1;
}
if ($sales_sn1 == '') {
    $sales_sn = NULL;
}else{
    $sales_sn = $sales_sn1;
}
if ($sales_id1 == '') {
    $sales_id = NULL;
}else{
    $sales_id = $sales_id1;
}
if ($out_price1 == '') {
    $out_price = NULL;
}else{
    $out_price = $distributor_id1;
}
if ($out_user1 == '') {
    $out_user = NULL;
}else{
    $out_user = $out_user1;
}
    $QImei = new Application_Model_Imei();

    if ($id) {
        $data = array(
            'good_id'   => $good_id,
            'good_color'  => $good_color,
            'distributor_id'   => $distributor_id,
            'out_date' => $out_date,
            'sales_sn'   => $sales_sn,
            'sales_id' => $sales_id,
            'out_price'   => $out_price,
            'out_user' => $out_user,
            );
        $where = $QImei->getAdapter()->quoteInto('id = ?', $id);
        $QImei->update($data, $where);
    } 

$this->view->messages = "1";
$this->_redirect(HOST.'tool/imei-edit');