<?php

try{


set_time_limit(0);
ini_set('memory_limit', '200M');
$this->_helper->layout->disableLayout();
$sn = $this->getRequest()->getParam('sn');
$type = $this->getRequest()->getParam('type');

$params = array(
    'sn' => $sn,
    'group_sn' => 1,
    'isbacks' => 0,
    //    'outmysql_time' => 1,
    'group_good' => 1,
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
$QMarketDeduction = new Application_Model_MarketDeduction();
$QOffice = new Application_Model_Office;
$QWarehouse = new Application_Model_Warehouse();
$QInvoiceNumber = new Application_Model_InvoiceNumber();
$QInvoicePrefix = new Application_Model_InvoicePrefix();
$market = $QMarket->fetchPagination($page, $limit, $total, $params);

/* if($type == 'bvg' )
{

unset($params['group_neo3']);
$params['group_neo3_1'] = 1;
$market1 = $QMarket->fetchPagination($page, $limit, $total, $params);

$market = $market + $market1;
} */


if (!(isset($market) && isset($market[0]) && $market[0])) {
    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->addMessage('Sales Number not exists');
    $this->_redirect(HOST . "warehouse/product-out");
} else {
    $market = $market[0];
}
$warehouse_id = $market['warehouse_id'];
$QDistributor = new Application_Model_Distributor();
$distributor = $QDistributor->find($market['d_id']);
$distributor = $distributor->current();



if(isset($market['good_id']) and in_array($market['good_id'], array(373,267,390)) )
    $this->view->khuyenmai = 1;

if(isset($market['payment_method']) and $market['payment_method'])
    $this->view->payment_method = My_Pay_Method::getNote($market['payment_method']);

//check don nguyen kim

if(isset($distributor) and $distributor)
{
     unset($params['group_good']);
     $params['group_good_color'] = 1;

   /* if(in_array($distributor['parent'] , array(KA_TGDD)) )
    {
        $this->view->date_tgdd = '2016-02-01';
        if(isset($market['payment_method']) and $market['payment_method'] == 1)
             $this->view->payment_method = '01-03-2016';

        if(isset($market['payment_method']) and $market['payment_method'] == 2)
             $this->view->payment_method = '03-03-2016';
    }
    */
}



if (isset($market['office']) and $market['office']) {
    $office = $market['office'];

    if (in_array($market['d_id'] , array(OPPO_KVL, OPPO_STAFF))) {
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
                'Người mua không lấy hóa đơn ( hàng bán lẻ cho nhân viên )';
            $this->view->invoice_type = $invoice[0]['invoice_type'];
        }
    }

    if ($market['d_id'] == OPPO_GIFT) {	
        $this->view->invoice_note = 'Người mua không lấy hóa đơn';
        $this->view->event = 1;
    }
	

    
    if ($market['d_id'] == OPPO_DEMO) {
        $this->view->invoice_note = 'Người mua không lấy hóa đơn ( hàng bán khu vực )';
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
$total2 = 0;
$mk_goods = $QMarket->fetchPagination(1, null, $total2, $params); //////////////
$new_mk_goods = array();

foreach ($mk_goods as $key => $value)
    $new_mk_goods[] = $value;

$mk_goods = $new_mk_goods;
unset($new_mk_goods);

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



unset($params['sn']);

$this->view->market = $market;
$this->view->mk_goods = $mk_goods;
$this->view->imeis = $imeis;


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
$staff = $QStaff->find($market['user_id']);
$staff = $staff->current();

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

if (!$market['invoice_number'])
{
    $invoice_number = $QInvoiceNumber->getLastId($warehouse_id);
}
else{
    $invoice_number = $market['invoice_number'];
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

    $mk_bvg_joint = array();
    $params = array(
    'sn' => $sn,
    'isbacks' => 0,
    'group_good' => 1,
    );
    $params['group_sn'] = 1;
    $QJoint = new Application_Model_JointCircular();
    $joint_list = $QJoint->fetchAll();
    $joint = array();
    foreach($joint_list as $k=>$v)
    {
        $joint[$v['id']] = $v['name'];
    }
    foreach ($joint as $k => $v) {
        $params['joint'] = $k;
        $mk_bvg_joint[$k] = $QMarketProduct->fetchPagination(1, null, $total2, $params);
    }

    $type_discount = $QMarketProduct->getDiscount($sn);
    $this->view->type_discount = $type_discount;

    $QGoodColor               = new Application_Model_GoodColor();
    $this->view->good_colors = $QGoodColor->get_cache2();

    //TH chiet khau
    if($type_discount == 2)
    {
        $whereMarketDeduction   = array();
        $whereMarketDeduction[] = $QMarketDeduction->getAdapter()->quoteInto('sn = ?' , $sn);
        $market_product         = $QMarketDeduction->fetchRow($whereMarketDeduction);
        if (!$market_product['invoice_number'])
        {
            $invoice_number = $QInvoiceNumber->getLastId($warehouse_id);
        }
        else{
            $invoice_number = $market_product['invoice_number'];
        }

        $this->view->bvg = 1;
        $this->view->joint = $joint;
        $this->view->joint_circular = $joint[$market_product['joint_circular_id']];
        $this->view->total = $QMarketProduct->getPrice($sn);
    }
    //TH BVG
    else
    {
        $whereMarketProduct   = array();
        $whereMarketProduct[] = $QMarketProduct->getAdapter()->quoteInto('sn = ?' , $sn);
        $market_product       = $QMarketProduct->fetchRow($whereMarketProduct);
        if (!$market_product['invoice_number'])
        {
            $invoice_number = $QInvoiceNumber->getLastId($warehouse_id);
        }
        else{
            $invoice_number = $market_product['invoice_number'];
        }
        $this->view->bvg = 1;
        $total_price_bvg = $QMarketProduct->getPrice($sn);
        $total = intval($total_price) - intval($total_price_bvg);
        $this->view->total = $total;

        if($market_product['type'] == My_Discount::BVGAccessories)
        {
            $this->view->accessories = 1;
        }

        $this->view->joint = $joint;
    }



}
else{
    /*if($market['invoice_number'] and $market['invoice_sign'])
        exit('Invoice can\'t not print again');*/
}

if ($type == 'ck') {
    $QMarketDeduction = new Application_Model_MarketDeduction();
    $where = array();
    $where[] = $QMarketDeduction->getAdapter()->quoteInto('sn = ?', $sn);
    $market_deduction = $QMarketDeduction->fetchRow($where);
    $invoice_number = $market_deduction['invoice_number'];
    $total_price = $QMarketProduct->getPrice($sn);
    $this->view->invoice_number = $invoice_number;
    $this->view->total = $total_price;
    $this->view->ck = 1;
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

$warehouseRowset = $QWarehouse->find($warehouse_id);
$warehouse       = $warehouseRowset->current();

// My_Image_Barcode::renderNoCode($sn);


$invoice_prefix = $QInvoicePrefix->getPrefix($warehouse_id);

$this->view->invoice_prefix = $invoice_prefix;
$this->view->warehouse = $warehouse;
$this->view->sn = $sn;

if(isset($mk_bvg_joint) and $mk_bvg_joint)
    $this->view->market_product = $mk_bvg_joint;

$this->view->invoice_number = $invoice_number;

if ($distributor) {
    $this->view->distributor = $distributor;
    $QGoodColor               = new Application_Model_GoodColor();
    $this->view->good_colors = $QGoodColor->get_cache2();
    $tmp = $QGood->fetchAll();
    $all_goods = array();
    foreach ($tmp as $k => $v) {
        $all_goods[$v['id']] = $v;
    }
    $this->view->all_goods = $all_goods;
}


}
catch (exception $e)
{
    echo ($e->getMessage());
    exit;
}
