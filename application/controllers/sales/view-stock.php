<?php
$sn = $this->getRequest()->getParam('sn');
$this->_helper->viewRenderer->setRender('view');
if ($sn) {
    $QMarket = new Application_Model_MarketStock();

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $where = array();
    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);
    $sales = $QMarket->fetchAll($where);
    $data = array();

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

    foreach ($sales as $k=>$sale){
        //get warehouse
        $data[$k]['warehouse_name'] = isset($warehouses[$sale->warehouse_id]) ? $warehouses[$sale->warehouse_id] : '';

        //get retailer
        $data[$k]['retailer_name'] = isset($distributors[$sale->d_id]) ? $distributors[$sale->d_id] : '';

        //get created_by_name
        $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->user_id] : '';

        //get sales man
        $data[$k]['salesman_name'] = isset($staffs[$sale->salesman]) ? $staffs[$sale->salesman] : '';

        //get pay user
        $data[$k]['pay_user'] = isset($staffs[$sale->pay_user]) ? $staffs[$sale->pay_user] : '';

        //get pay time
        $data[$k]['pay_time'] = isset($sale->pay_time) ? $sale->pay_time : '';

        //get out time
        $data[$k]['outmysql_time'] = isset($sale->outmysql_time) ? $sale->outmysql_time : '';

        //get out user
        $data[$k]['outmysql_user'] = isset($staffs[$sale->outmysql_user]) ? $staffs[$sale->outmysql_user] : '';

        //get out user
        $data[$k]['last_updated_at'] = isset($sale->last_updated_at) ?$sale->last_updated_at : '';

        //get category
        $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->cat_id] : '';

        //get good
        $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';

        //get goods color
        $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->good_color] : '';

        $data[$k]['sale'] = $sale;
    }

    $QMarketProduct = new Application_Model_MarketProduct();
     //Get detail discount
    $detailDiscount = $QMarketProduct->getDetailDiscount($sn);
    //get detail BVG
    $detailBVG = $QMarketProduct->getDetailBVG($sn);   

    $this->view->detailDiscount = $detailDiscount;
    $this->view->detailBVG   = $detailBVG;
    $this->view->sales          = $data;

    // lấy các loại phí (có phí vận chuyển)
    $order_fee = My_Sale_Order_Fee::getFee($sn, My_Sale_Order_Fee::Shipping);
    $this->view->order_fee = $order_fee;
    // END // lấy các loại phí (có phí vận chuyển)
}