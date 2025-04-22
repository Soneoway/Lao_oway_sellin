<?php

set_time_limit(0);
ini_set('memory_limit', -1);
$this->_helper->layout->disableLayout();
$sn = $this->getRequest()->getParam('sn');
$type = $this->getRequest()->getParam('type');
$show_imei = $this->getRequest()->getParam('show_imei');

$type_inv = $this->getRequest()->getParam('type_inv',1);

if(!$type_inv){
    echo 'Invalid Date Please Check Type Invoice';
    exit();
}

$this->view->type_inv = $type_inv;


//echo 111;die;
$params = array(
    'sn' => $sn,
    'group_sn' => 1,
    'isbacks' => 0,
    //    'outmysql_time' => 1,
    'group_good' => 10,
    );

$page = 1;
$limit = 1;
$total = 0;

//don hang bao ve gia
if ($type == 'bvg') {
    unset($params['group_good']);
    $params['group_neo3'] = 1;
    $this->view->bvg = 1;
}
//don hang danh cho nguyen kim - tuong tu don bao ve giá , chi khac co them mau
if ($type == "nk") {
    unset($params['group_good']);
    $params['group_neo3'] = 1;
    $this->view->nk = 1;
}

$QGood = new Application_Model_Good();
$QMarket = new Application_Model_Market();
$QImei = new Application_Model_Imei();
$QMarketProduct = new Application_Model_MarketProduct();
$QOffice = new Application_Model_Office;

// $market = $QMarket->fetchPagination($page, $limit, $total, $params);
$market_main = $QMarket->getMarketOnly($params);

if (!(isset($market_main) && isset($market_main[0]) && $market_main[0])) {

} else {
    $market = $market_main[0];
}
$warehouse_id = $market['warehouse_id'];
$QDistributor = new Application_Model_Distributor();
$distributor = $QDistributor->find($market['d_id']);
$distributor = $distributor->current();

//print_r($distributor);

if(isset($market['good_id']) and in_array($market['good_id'], array(373,267)) )
    $this->view->khuyenmai = 1;

if ($market['d_id'] == 9196) 
        $this->view->invoice_note = 'ผู้ซื้อไม่ได้รับค่าใช้จ่าย';
if (isset($market['office']) and $market['office']) {
    $office = $market['office'];

    if ($market['d_id'] == OPPO_STAFF) {
        $QCustomer = new Application_Model_Customer();
        $QOffice = new Application_Model_Office();
        $where = array();
        $where = $QCustomer->getAdapter()->quoteInto('sn = ?', $sn);
        $invoice = $QCustomer->fetchAll($where);
        if ($invoice[0]['invoice_type'] == 1) {
            /////có in hóa đơn//////
            $distributor = array(
                'unames' => $invoice[0]['company'],
                'company' => $invoice[0]['company'],
                'mst_sn' => $invoice[0]['tax_code'],
                'add' => $invoice[0]['address'],
                );
            $this->view->invoice_type = $invoice[0]['invoice_type'];
        } else {
            /////không in hóa đơn//////
            $this->view->invoice_note =
                'ผู้ซื้อไม่ได้รับใบแจ้งหนี้ (พนักงานขายปลีก)';
            $this->view->invoice_type = $invoice[0]['invoice_type'];
        }
    }

    if ($market['d_id'] == OPPO_GIFT) { 
        $this->view->invoice_note = 'ผู้ซื้อไม่ได้รับค่าใช้จ่าย';
        $this->view->event = 1;
    }
    

    
    if ($market['d_id'] == OPPO_DEMO) {
        $this->view->invoice_note = 'ผู้ซื้อไม่ได้รับค่าใช้จ่าย (พื้นที่ขาย)';
    }

    if ($market['d_id'] == OPPO_EVENT) {

        $where = array();
        $where = $QOffice->getAdapter()->quoteInto('id = ?', $office);
        $invoice = $QOffice->fetchAll($where);
        $distributor = array(
            'unames' => $invoice[0]['customer'],
            'company' => $invoice[0]['title'],
            'mst_sn' => $invoice[0]['mst_sn'],
            'add' => $invoice[0]['name'],
            );
        $this->view->event = 1;
    }

}

    

unset($params['group_sn']);
unset($params['sort']);

// Lấy danh sách các sản phẩm theo từng đơn hàng (đã lấy ở trên)
$params['sn'] = $market['sn'];
$distributors = $market['d_id'];
$customer_brandshop_id = $market['customer_id'];
$total2 = 0;

// //print_r($params);
// //Tanong
// $mk_goods = $QMarket->fetchPagination(1, null, $total2, $params); //////////////
// $new_mk_goods = array();

// //print_r($mk_goods);

// foreach ($mk_goods as $key => $value)
//     $new_mk_goods[] = $value;

// $mk_goods = $new_mk_goods;

// //print_r($mk_goods);

// unset($new_mk_goods);

$mk_goods = $market_main;

// lấy danh sách IMEI có sn như trên theo từng sản phẩm
$imeis = array();

foreach ($mk_goods as $k => $v) {
    $where = array();


    $where[] = $QImei->getAdapter()->quoteInto('good_id = ?', $v['good_id']);
    $where[] = $QImei->getAdapter()->quoteInto('good_color = ?', $v['good_color']);
    $where[] = $QImei->getAdapter()->quoteInto('sales_sn = ?', $v['sn']);

    if ($v['good_id'] == 152 && $warehouse_id == 8)
        $joy = 1;

    $imeis[$v['good_id']][$v['good_color']] = $QImei->fetchAll($where);
    //print_r($imeis);
}


 $order_fee = My_Sale_Order_Fee::getFee($sn);

 if(isset($order_fee) and $order_fee)
 {
     $fee = array();
     foreach ($order_fee as $key => $value) {
         $fee[] = array(
             'desc'        => My_Sale_Order_Fee::$name[ $key ],
             'total_price' => $value['value'],
         );
     }

     $this->view->fee = $fee;
 }

//print_r($params);
//Tanong
$mk_goods = $QMarket->fetchInvoice($params); //////////////
//print_r($mk_goods);
//Tanong
if($show_imei==1){
    $imei_list = $QMarket->fetchWithImei($sn);
    $this->view->imeis = $imei_list;
    //print_r($imei_list);
}else{
    $this->view->imeis = null;
}

//print_r($mk_goods);
unset($params['sn']);

$this->view->market = $market;
$this->view->mk_goods = $mk_goods;

//fetchWithImei($sn)



$tmp = $QGood->fetchAll();
$all_goods = array();
foreach ($tmp as $k => $v) {
    $all_goods[$v['id']] = $v;
}
$this->view->all_goods = $all_goods;
$this->view->goods_cached = $QGood->get_cache();

if (isset($joy) and $joy) {
    $this->view->joy = 1;
}


$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

$QGoodColor = new Application_Model_GoodColor();
$this->view->good_colors = $QGoodColor->get_cache();


$QStaff = new Application_Model_Staff();

if(isset($market['sales_catty_id']) && $market['sales_catty_id'] > 0 && isset($market['rank']) && $market['rank'] == 7){
    $getStaffByCatty = $QStaff->getCattyStaffByID($market['sales_catty_id']);
    if($getStaffByCatty){
        $staff = $getStaffByCatty[0];
    }else{
        $staff = $QStaff->find($market['salesman']);
        $staff = $staff->current();
    }
}else{
    $staff = $QStaff->find($market['salesman']);
    $staff = $staff->current();
}


if ($staff) {
    $this->view->staff = $staff;
}

$QGoodColor = new Application_Model_GoodColor();
$this->view->colors_list = $QGoodColor->get_cache();

//set invoice time
if (!$market['invoice_time']) {
    $data = array('invoice_time' => date('Y-m-d H:i:s'));
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $QMarket->update($data, $where);
}
//in don hang phu kien cho fpt
if ($type == 'fpt') {
    $this->view->fpt = 1;
    $this->_helper->viewRenderer('warehouse/fpt', null, true);
}

//in don hang phu kien cho custom
if ($type == 'custom') {
    $this->_helper->viewRenderer('warehouse/custom', null, true);
}

//in don hang phu kien cho campuchia
if ($type == 'campuchia') {
    $this->_helper->viewRenderer('warehouse/campuchia', null, true);
}

//in don hang phu kien cho digital
if ($type == 'digital') {
    $this->_helper->viewRenderer('warehouse/digital', null, true);
}

//in don hang bao ve gia va bang ke hang hoa
if ($type == 'bkhh') {
    $total_price = $QMarket->getPrice($sn);
    $total_price_bvg = $QMarketProduct->getPrice($sn);
    $total = intval($total_price) - intval($total_price_bvg);
    $this->view->total = $total;
    $mk_bvg_joint = array();
    $params = array(
    'sn' => $sn,
    'isbacks' => 0,
    'group_good' => 1,
    );
    $params['group_sn'] = 1;
    $QJoint = new Application_Model_JointCircular();
    $joint = $QJoint->get_cache();
    foreach ($joint as $k => $v) {
        $params['joint'] = $k;
        $mk_bvg_joint[$k] = $QMarketProduct->fetchPagination(1, null, $total2, $params);
    }
    $this->view->joint = $joint;
    $this->_helper->viewRenderer('warehouse/partials/invoice/bvg', null, true);
}

if ($type == 'ck') {
    $QMarketDeduction = new Application_Model_MarketDeduction();
    $where = array();
    $where[] = $QMarketDeduction->getAdapter()->quoteInto('sn = ?', $sn);
    $market_deduction = $QMarketDeduction->fetchRow($where);
    $invoice_number = $market_deduction['invoice_number'];
    $total_price = $QMarketProduct->getPrice($sn);
    $QJoint = new Application_Model_JointCircular();
    $joint_list = $QJoint->fetchAll();
    $joint = array();
    foreach($joint_list as $k=>$v)
    {
        $joint[$v['id']] = $v['name'];
    }
    $this->view->invoice_number = $invoice_number;
    $this->view->total = $total_price;
    $this->view->ck = 1;
    $this->view->joint_circular = $joint[$market_deduction['joint_circular_id']];
    $this->_helper->viewRenderer('warehouse/bkhh', null, true);
}

//in don hang phu kien cho dien may cho lon
if ($type == 'dmcl') {
    $QGoodColor = new Application_Model_GoodColor();
    $this->view->good_colors = $QGoodColor->get_cache2();
    $this->_helper->viewRenderer('warehouse/invoice-vt', null, true);
}
//in don hang phu kien fpt
if ($type == 'accessories-fpt') {
    $this->_helper->viewRenderer('warehouse/accessories-fpt', null, true);
}

$QInvoicePrefix = new Application_Model_InvoicePrefix();
$invoice_prefix = $QInvoicePrefix->fetchAll();
$this->view->invoice_prefix = $invoice_prefix;
$this->view->warehouse_id = $warehouse_id;
$this->view->sn = $sn;
if(isset($mk_bvg_joint) and $mk_bvg_joint)
$this->view->market_product = $mk_bvg_joint;

$QCredit        = new Application_Model_Credit();
$where_c        = $QCredit->getAdapter()->quoteInto('id = ?', $distributor->credit_type);
$credit_type    = $QCredit->fetchRow($where_c);

$QTag        = new Application_Model_Tag();
$Tag = $QTag->fetch_Tag($sn);

if($customer_brandshop_id !='')
{
    $CustomerBrandShop = $QMarket->getCustomerBrandShop($customer_brandshop_id);
    $this->view->customer_brandshop_id =$customer_brandshop_id;
    $this->view->customerbrandshop = $CustomerBrandShop;
}

if ($distributor) {
    $this->view->distributor = $distributor;
    $this->view->credit_type = $credit_type;
    $this->view->Tag = $Tag;

    $QDG = new Application_Model_DistributorGroup();
    $dis_group = $QDG->find($distributor['group_id']);
    $dis_group = $dis_group->current();
    $this->view->dis_group = $dis_group;
    //print_r($market);
}

//$memory_limit = ini_get('memory_limit');

//print_r($memory_limit);die;