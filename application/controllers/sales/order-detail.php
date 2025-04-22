<?php
$sn = $this->getRequest()->getParam('sn');
$export = $this->getRequest()->getParam('export');

if ($sn) {
    $QMarket = new Application_Model_Market();

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $where = array();

    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);

    $sales = $QMarket->fetchAll($where);
    // print_r($sales);
    $data = array();


    $QIR            = new Application_Model_ImeiReturn();
    $ImeiList = $QIR->getImeiOrder($sn);
    $QBrand = new Application_Model_Brand();
    $brands_c = $QBrand->get_cache();

    $QGoodCategory = new Application_Model_GoodCategory();
    $categories = $QGoodCategory->get_cache();

    $QGood = new Application_Model_Good();
    $goods = $QGood->get_cache();

    $QGoodColor = new Application_Model_GoodColor();
    $goodColors = $QGoodColor->get_cache();

    $QStaff = new Application_Model_Staff();
    $staffs = $QStaff->get_cache();

    $QDistributor = new Application_Model_Distributor();
    $distributors = $QDistributor->get_cache();

    $QWarehouse = new Application_Model_Warehouse();
    $warehouses = $QWarehouse->get_cache();

    $Credit_Note = $QMarket->fetchCredit_Note($sn);

    $deposit_list = $QMarket->fetchDeposit($sn);

    $QCheckMoney = new Application_Model_Checkmoney();
    $AllPaymentOrder = $QCheckMoney->getAllPaymentOrder($sn);

    foreach ($sales as $k => $sale){

        $data[$k]['warehouse_name'] = isset($warehouses[$sale->warehouse_id]) ? $warehouses[$sale->warehouse_id] : '';

        $data[$k]['retailer_name'] = isset($distributors[$sale->d_id]) ? $distributors[$sale->d_id] : '';

        $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->user_id] : '';

        $data[$k]['salesman_name'] = isset($staffs[$sale->salesman]) ? $staffs[$sale->salesman] : '';

        if($sale->sales_catty_id !=''){
            $staffs_catty = $QStaff->getSalesCattyByStore($sale->d_id,$sale->sales_catty_id);
        }

        $data[$k]['salescatty_name'] = isset($staffs_catty) ? $staffs_catty[0]['fullname'] : '';

        $data[$k]['pay_user'] = isset($staffs[$sale->pay_user]) ? $staffs[$sale->pay_user] : '';

        $data[$k]['pay_time'] = isset($sale->pay_time) ? $sale->pay_time : '';

        $data[$k]['outmysql_time'] = isset($sale->outmysql_time) ? $sale->outmysql_time : '';

        $data[$k]['outmysql_user'] = isset($staffs[$sale->outmysql_user]) ? $staffs[$sale->outmysql_user] : '';

        $data[$k]['last_updated_at'] = isset($sale->last_updated_at) ?$sale->last_updated_at : '';

        $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->cat_id] : '';

        $brands  = $QBrand->getBrand($sale->good_id);
        $data[$k]['brand_name'] = $brands[0]['brand_name'];

        $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';

        $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->good_color] : '';

        $data[$k]['sale'] = $sale;


        $data[$k]['total_discount'] = $Credit_Note[0]['total_discount'];

        $data[$k]['credit_note_list'] = $Credit_Note;

        $data[$k]['deposit_list'] = $deposit_list;

        $data[$k]['all_payment_order'] = $AllPaymentOrder;

        $data[$k]['total_spc_discount'] = $sale->total_spc_discount;

    }


    $QMarketProduct = new Application_Model_MarketProduct();
    $detailDiscount = $QMarketProduct->getDetailDiscount($sn);
    $detailBVG = $QMarketProduct->getDetailBVG($sn);   

    if (isset($export) && $export){
        $markets_sn = '';
        $this->_exportExcelCashCollection($markets_sn);
    }

}

    $this->view->detailDiscount = $detailDiscount;
    $this->view->detailBVG   = $detailBVG;
    $this->view->sales          = $data;
    $this->view->imeiList = $ImeiList;

    $this->view->goods = $goods;
    $this->view->goodColors = $goodColors;
    $this->view->brands_c = $brands_c;

    $order_fee = My_Sale_Order_Fee::getFee($sn, My_Sale_Order_Fee::Shipping);
    $this->view->order_fee = $order_fee;
    $this->_helper->viewRenderer->setRender('partials/order-detail');
// }