<?php

ini_set('memory_limit', '-1');

if (file_exists(APPLICATION_PATH . '/../public/files/mou/lock') || (defined("NO_CREATE_NO_EDIT_ORDER") &&
    NO_CREATE_NO_EDIT_ORDER)) {
    $this->_helper->viewRenderer->setRender('lock');
    return;
}
// die('test');
$db = Zend_Registry::get('db');
$flashMessenger = $this->_helper->flashMessenger;

// PreSales
$presales_sn = $this->getRequest()->getParam('presales_sn');
$use_cn = $this->getRequest()->getParam('use_cn');

$this->view->pre_rank = ''; 
$this->view->pre_d_id = '';
if($presales_sn !=''){
    $pre_resule=null;
    $QPreSalesOrder = new Application_Model_PreSalesOrder();
    $params = array_filter(array(
        'presales_sn'       => $presales_sn,
        'action_frm' => 'view'
        ));
    $pre_resule = $QPreSalesOrder->pre_sales_order_view($params);
    //print_r($pre_resule[0]['distributor_id']);die;
    foreach ($pre_resule as $k => $pre)
    {
        //get goods
        $QGood = new Application_Model_Good();
        $where = $QGood->getAdapter()->quoteInto('cat_id = ? ', $pre['cat_id']);
        $goods = $QGood->fetchAll($where, 'name');

        $pre_resule[$k]['goods'] = $goods;

        //get goods color
        $where = $QGood->getAdapter()->quoteInto('id = ?', $pre['good_id']);
        $good = $QGood->fetchRow($where);

        $aColor = array_filter(explode(',', $good->color));
        if ($aColor) {
            $QGoodColor = new Application_Model_GoodColor();
            $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

            $colors = $QGoodColor->fetchAll($where);
            $pre_resule[$k]['colors'] = $colors;
        }
    }

    
    $this->view->presales_sn = $presales_sn;
    $this->view->use_cn = $use_cn;
    $this->view->pre_resule = $pre_resule; 
    $this->view->pre_rank = $pre_resule[0]['rank']; 
    $this->view->pre_d_id = $pre_resule[0]['distributor_id']; 
}

    $QBRC  = new Application_Model_BuyReturnCondition();
    $getConditionByReturn = $QBRC->getConditionByReturn();

    $this->view->getConditionByReturn = $getConditionByReturn;

//edit
$sn = $this->getRequest()->getParam('sn');
$sn_ref = $this->getRequest()->getParam('sn_ref');

$QStaff = new Application_Model_Staff();
$QDistributor = new Application_Model_Distributor();
$QPhoneNumber = new Application_Model_PhoneNumber();

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$this->view->dis_name = $QDistributor->get_cache();

$QExceptionCase = new Application_Model_ExceptionCase();
$where = $QExceptionCase->getAdapter()->quoteInto('name = ?',
    'LIFETIME_EXCEPTION');
$lifetime_exception = $QExceptionCase->fetchRow($where);

$exception_case = null;
if (isset($lifetime_exception) and $lifetime_exception['value']) {
    eval(json_decode($lifetime_exception['value']));
    $exception_case = isset($data_exception) ? $data_exception : null;
}

if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, SUPER_SALES_ADMIN)) or ($exception_case and in_array
    ($userStorage->id, $exception_case)))
    $this->view->life_time_editable = 1;
else
    $this->view->life_time_editable = 0;

// brand shop and service not on sale admin
if (My_Staff_Group::inGroup($userStorage->group_id, array(25, 28)) && !My_Staff_Group::inGroup($userStorage->group_id, array(3, 23)))
    $this->view->auto_select_brandshop_service = 1;
else
    $this->view->auto_select_brandshop_service = 0;

if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, SPLIT_GIFBOX)) or ($exception_case and in_array
    ($userStorage->id, $exception_case)))
    if ($sn ==''){
        $this->view->split_gifbox = 1;
    }else{
        $this->view->split_gifbox = 0;
    }
else
    $this->view->split_gifbox = 0;

if($presales_sn ==''){
    if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, OPPO_BRAND_SHOP)) or ($exception_case and in_array
        ($userStorage->id, $exception_case))){
        $this->view->bs_campaign = 1;
        //$this->view->phone_number = 0960567959;
    }else{
        $this->view->bs_campaign = 0;
        //$this->view->phone_number = 0960567959;
    }
}
    
    $phone = $QPhoneNumber->getPhone_number("",$sn);
    //print_r($phone);
    $this->view->phone_number = $phone;


if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, EDIT_PRICE_PRODUCT)) or ($exception_case and in_array
    ($userStorage->id, $exception_case)))
    $this->view->eidt_price_product = 1;
else
    $this->view->eidt_price_product = 0;

if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, OPPO_BRAND_SHOP)))
    $this->view->is_brandshop = 1;
else
    $this->view->is_brandshop = 0;

if ($sn) {
    // chặn xem/edit đơn của người khác
    $QMarket = new Application_Model_Market();
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $market_check = $QMarket->fetchRow($where);


    if ($market_check) {

        // if ($market_check['user_id'] != $userStorage->id && !My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, SUPER_SALES_ADMIN))) {
        //     $flashMessenger->setNamespace('error')->addMessage('You cannot edit this Order');
        //     $this->_redirect(HOST . 'sales');
        // }
    }

    $this->view->staffs_cached = $QStaff->get_cache();
    $where = array();
    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $where[] = $QMarket->getAdapter()->quoteInto('status = ?',1 );
    $sales = $QMarket->fetchAll($where);

    $data = array();
    //print_r($sales);
    //check status
    if ($sales=="") {

        $flashMessenger->setNamespace('error')->addMessage('Invalid SN!');
        $this->_redirect('/sales');

    }

    if ($sales[0]['shipping_yes_time'] or $sales[0]['pay_time'] or $sales[0]['outmysql_time']) {

        $flashMessenger->setNamespace('error')->addMessage('This order cannot be changed!');

        $this->_redirect('/sales');

    }

    $buy_return = 0;
    $buy_return_true = 0;
      
    foreach ($sales as $k => $sale) {

        if(isset($sale->buy_return) && $sale->buy_return){
            $buy_return = 1;
            $sale->price = $sale->price + $sale->discount_buy_return;
        }

        if(isset($sale->buy_return) && $sale->buy_return && isset($sale->bs_campaign) && $sale->bs_campaign){
            $buy_return_true = 1;
            $sale->price = $sale->price + $sale->discount_buy_return_true;
        }
      
        //get goods
        $QGood = new Application_Model_Good();
        $where = $QGood->getAdapter()->quoteInto('cat_id = ? ', $sale->cat_id);
        $goods = $QGood->fetchAll($where, 'name');

        $data[$k]['goods'] = $goods;

        //get goods color
        $where = $QGood->getAdapter()->quoteInto('id = ?', $sale->good_id);
        $good = $QGood->fetchRow($where);

        $aColor = array_filter(explode(',', $good->color));
        if ($aColor) {
            $QGoodColor = new Application_Model_GoodColor();
            $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

            $colors = $QGoodColor->fetchAll($where);
            $data[$k]['colors'] = $colors;
        }

        if (isset($sale['service']) and $sale['service']) {
            $service = $sale['service'];
        }

        if (isset($sale['office']) and $sale['office']) {
            $office = $sale['office'];
        }

        if (isset($sale['warehouse_nvmm']) and $sale['warehouse_nvmm']) {
            $warehouse_nvmm = $sale['warehouse_nvmm'];
        }


        $data[$k]['sale'] = $sale;
        //print_r($sale);
        $customer_brandshop_id = $sale['customer_id'];
    }

    $this->view->buy_return = $buy_return;
    $this->view->buy_return_true = $buy_return_true;

    $QD  = new Application_Model_Distributor();

    $getDistributorGroup = $QD->getDistributorGroup($sales[0]['d_id']);

    $data['group_type_id'] = $getDistributorGroup['group_type_id'];


    $where_payment = $QDistributor->getAdapter()->quoteInto('id = ?', $sales[0]['d_id']);
    $distributors_payment = $QDistributor->fetchRow($where_payment);
    $rank = $distributors_payment->rank;
    // echo $rank;
    
    $this->view->rank = $rank;
    $this->view->sales = $data;
    $for_staff = $sales[0]['for_staff'];
    $this->view->for_partner = $sales[0]['for_partner'];
    $this->view->for_staff = $for_staff;
    $this->view->distributor = $QD->get_cache();

    $this->view->text_note = $sales[0]['shipping_text'];
    //load bvg

    $QMarket_product = new Application_Model_MarketProduct();
    $QIMEI = new Application_Model_BvgImei();
    $QBVG = new Application_Model_BvgProduct();
    $QMarketDeduction = new Application_Model_MarketDeduction();

    $where = array();
    $data = array();
    $imeis = array();

    $where[] = $QMarket_product->getAdapter()->quoteInto('sn = ?', $sn);
    $sales = $QMarket_product->fetchAll($where);

    foreach ($sales as $k => $sale)
    {

        $where = $QBVG->getAdapter()->quoteInto('joint_id = ?', $sale->joint);
        $bvg = $QBVG->fetchAll($where, 'good_id');

        $QGood = new Application_Model_Good();
        $where = $QGood->getAdapter()->quoteInto('id in (?) ', $bvg->toArray());
        $goods = $QGood->fetchAll($where, 'name');

        $where = $QIMEI->getAdapter()->quoteInto('bvg_market_product_id = ? ', $sale->
            id);
        $imei = $QIMEI->fetchAll($where);

        $data[$k]['goods'] = $goods;
        $data[$k]['sale'] = $sale;
        $imeis[$k] = $imei;
    }

    $where = array();
    $where[] = $QMarketDeduction->getAdapter()->quoteInto('sn = ?', $sn);
    $sales = $QMarketDeduction->fetchAll($where);

    $this->view->sales_discount = $sales;
    $this->view->sales_product = $data;
    $this->view->imei = $imeis;


    //end load bvg
    //
    // lấy các loại phí (có phí vận chuyển)
    $order_fee = My_Sale_Order_Fee::getFee($sn, My_Sale_Order_Fee::Shipping, true);
    $this->view->order_fee = $order_fee;
    // END // lấy các loại phí (có phí vận chuyển)

    if (isset($service) and $service) {
        $this->view->service_id = $service;
        $this->view->service_nvmm = $service;
    }

    if(isset($warehouse_nvmm) AND $warehouse_nvmm){
        $this->view->warehouse_nvmm = $warehouse_nvmm;
    }

    if( isset($office) and $office){
        $this->view->office_id = $office;
    }

    if ( (isset($office) and $office)
        OR (isset($service) AND $service)
        OR (isset($warehouse_nvmm) AND $warehouse_nvmm))
    {


    }

    // get tags
    $QTag = new Application_Model_Tag();
    $QTagObject = new Application_Model_TagObject();

    $where = array();
    $where[] = $QTagObject->getAdapter()->quoteInto('object_id = ?', $sn);
    $where[] = $QTagObject->getAdapter()->quoteInto('type = ?', TAG_ORDER);

    $a_tags = array();

    $tags_object = $QTagObject->fetchAll($where);
    if ($tags_object)
        foreach ($tags_object as $to) {
            $where = $QTag->getAdapter()->quoteInto('id = ?', $to['tag_id']);
            $tag = $QTag->fetchRow($where);
            if ($tag)
                $a_tags[] = $tag['name'];
        }

    $this->view->a_tags = $a_tags;



} else {
    if (defined("NO_CREATE_ORDER") && NO_CREATE_ORDER) {
        $this->_helper->viewRenderer->setRender('partials/nocreate');
        return;
    }
}

$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->fetchAll();

$QBrand = new Application_Model_Brand();
$this->view->brands = $QBrand->fetchAll();


$QWarehouse = new Application_Model_Warehouse();
$warehouse_type = $userStorage->warehouse_type;
$where = array();
if($warehouse_type !=''){
    
    $warehouses_cached = $QWarehouse->getWarehouses($warehouse_type);
    $warehouse_arr = array();

    foreach ($warehouses_cached as $k => $warehouse_data){
        $warehouse_arr[$warehouse_data['id']] = $warehouse_data['name']; 
    }
    $this->view->warehouses = $warehouse_arr;
       
}else{

    $warehouse_type="1";
    $warehouses_cached = $QWarehouse->getWarehouses($warehouse_type);
    $warehouse_arr = array();

    foreach ($warehouses_cached as $k => $warehouse_data){
        $warehouse_arr[$warehouse_data['id']] = $warehouse_data['name']; 
    }
    $this->view->warehouses = $warehouse_arr;

}


$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;


$this->view->id_userStorage = $userStorage->id;
$this->view->userStorage    = $userStorage;


$arr_shipment_type = array(
    1 => 'A',
    2 => 'B',
    3 => 'C',
    4 => 'D'
);
$this->view->arr_shipment_type = $arr_shipment_type;

$arr_for_partner = array(
    2 => 'FOR STAFF',
    1 => 'FOR PARTNER'
);

$this->view->arr_for_partner = $arr_for_partner;
$arr_order_type             = unserialize(ORDER_TYPE);

//print_r($userStorage->group_id);
//print_r(My_Staff_Group::access('priviledge','nvmm'));
/*if( My_Staff_Group::access('priviledge','nvmm')){

    if(!($userStorage->id == SUPERADMIN_ID OR $userStorage->group_id == ADMINISTRATOR_ID) ){
        $arr_order_type = array(ORDER_FOR_STAFF => ORDER_FOR_STAFF_NAME);
        
    }
}*/



$this->view->arr_order_type = $arr_order_type;

// Tanong Add Function Credit 2016/02/25 09:47
$QCredit = new Application_Model_Credit();
$this->view->credits = $QCredit->get_cache();

// Tanong Add Function CreditNote 2016/03/08 16:12
$QCreditNote = new Application_Model_CreditNote();
$QDeposit = new Application_Model_Deposit();
$QDepositTran = new Application_Model_DepositTran();
$distributor_id=$market_check['d_id'];
$CreditNote = $QCreditNote->getCredit_Note($distributor_id,'');
$this->view->credits_note=$CreditNote;

$deposit_list = $QDeposit->getDeposit_sn($distributor_id,'');
$this->view->deposit_list=$deposit_list;

$total_amount_all=0;$delivery_fee_all=0;
if($sn!=''){
   $CreditNote_data = $QCreditNote->getCredit_Note_By_SalesOrder($sn,$distributor_id);

   $deposit_data = $QDepositTran->getDeposit_By_SalesOrder($sn,$distributor_id);

   
   $Total_Amount = $QMarket->LoadSalesOrderAmount($distributor_id);
   $total_amount_all = $Total_Amount[0]['total_acmount'];
   $delivery_fee_all = $Total_Amount[0]['delivery_fee'];

    if($customer_brandshop_id !='')
    {
        $CustomerBrandShop = $QMarket->getCustomerBrandShop('');
    }
}

$this->view->credits_note_sales_order=$CreditNote_data;
$this->view->deposit_sales_order=$deposit_data;

$this->view->total_amount_all = $total_amount_all;
$this->view->delivery_fee_all = $delivery_fee_all;
$this->view->sales_order = $sn;
$this->view->sn_ref = $sn_ref;

$this->view->customerbrandshop = $CustomerBrandShop;
$this->view->staff_id = $userStorage->id;
$this->view->group_id = $userStorage->group_id;

