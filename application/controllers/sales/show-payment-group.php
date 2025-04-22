<?php

    $flashMessenger = $this->_helper->flashMessenger;

    $payment_no = $this->getRequest()->getParam('paygroup');

    $QPG = new Application_Model_PayGroup();
    $priceAndDetails = $QPG->getPaymentGroup($payment_no);

    if(count($priceAndDetails) < 1){
        array_push($messages, 'ไม่มีข้อมูล');
    }else{

        $part_pay_one = HOST . 'public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'finance';
        $part_pay_group = HOST . 'public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'payment_group'. DIRECTORY_SEPARATOR . 'pay_slips' . DIRECTORY_SEPARATOR;

        $partImg = '';

        switch ($priceAndDetails[0]['payment_group']) {
            case '0':
                $partImg = $part_pay_one;
                break;
            case '1':
                $partImg = $part_pay_group;
                break;
        }

        $this->view->partImg = $partImg;

        $arrDataDetail = [];



        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $db = Zend_Registry::get('db');
        $QMarket = new Application_Model_Market();

        $QGoodCategory = new Application_Model_GoodCategory();
        $categories    = $QGoodCategory->get_cache();

        $QGood         = new Application_Model_Good();
        $goods         = $QGood->get_cache();

        $QGoodColor    = new Application_Model_GoodColor();
        $goodColors    = $QGoodColor->get_cache();

        $QStaff        = new Application_Model_Staff();
        $staffs        = $QStaff->get_cache();

        $QDistributor  = new Application_Model_Distributor();
        $distributors  = $QDistributor->get_cache();

        $QWarehouse    = new Application_Model_Warehouse();
        //$warehouses    = $QWarehouse->get_cache();
        $warehouse_type = $userStorage->warehouse_type;
        $where_wh = $QWarehouse->getAdapter()->quoteInto('warehouse_type IN ('.$warehouse_type.')', null);
        $warehouses_cached = $QWarehouse->fetchAll($where_wh, 'name');
        $warehouse_arr = array();
        foreach ($warehouses_cached as $k => $warehouse_data){
            $warehouse_arr[$warehouse_data['id']] = $warehouse_data['name']; 
        }
        $warehouses = $warehouse_arr;
        $this->view->warehouses = $warehouses;

        $show_cash_menu=false;
        if (My_Staff_Group::inGroup($userStorage->group_id, OPPO_BRAND_SHOP_SERVICE) || $userStorage->group_id == ADMINISTRATOR_ID )
        {
            $show_cash_menu  = true;
        }


        $arr_distributor = [];
        $bucketData = [];
        foreach ($priceAndDetails as $key) {
            array_push($arr_distributor, $key['d_id']);

            $Credit_Note = $QMarket->fetchCredit_Note($key['sale_order']);

            $where = array();
            $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $key['sale_order']);
            $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

            $sales = $QMarket->fetchAll($where);

             // get another info of distributor
            $where = $QDistributor->getAdapter()->quoteInto('id = ?', $sales[0]->d_id);
            $distributors_info = $QDistributor->fetchRow($where);

            // get credit from distributor's credit type
            $QCredit  = new Application_Model_Credit();
            $where = $QCredit->getAdapter()->quoteInto('id = ?', $distributors_info->credit_type);
            $credit = $QCredit->fetchRow($where);

            $data = array();

            foreach ($sales as $k => $sale) {
                //get warehouse
                $data[$k]['warehouse_name'] = isset($warehouses[$sale->warehouse_id]) ? $warehouses[$sale->
                    warehouse_id] : '';

                //get retailer
                //print_r($distributors);
                $data[$k]['retailer_name'] = isset($distributors[$sale->d_id]) ? $distributors[$sale->
                    d_id] : '';

                //get d_id
                $data[$k]['d_id'] = $sale->d_id;

                //get retailer : rank
                $data[$k]['retailer_rank'] = isset($distributors_info->rank) ? $distributors_info->rank : '';

                //get retailer : rank
                $data[$k]['show_cash_menu'] = $show_cash_menu;

                //get retailer : credit amount
                $data[$k]['credit_amount'] = isset($distributors_info->credit_amount) ? $distributors_info->credit_amount : '';

                //get retailer : credit type
                $data[$k]['credit_type'] = isset($credit->name) ? $credit->name : '';

                //get created_by_name
                $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->
                    user_id] : '';

                //get created_by_name
                $data[$k]['salesman_name'] = isset($staffs[$sale->salesman]) ? $staffs[$sale->
                    salesman] : '';

                //get sales man Catty
                if($sale->sales_catty_id !=''){
                    $staffs_catty = $QStaff->getSalesCattyByStore($sale->d_id,$sale->sales_catty_id);
                }

                $data[$k]['salescatty_name'] = isset($staffs_catty) ? $staffs_catty[0]['fullname'] : '';

                //get category
                $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->
                    cat_id] : '';

                //get good
                $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';

                //get goods color
                $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->
                    good_color] : '';

                $data[$k]['sale'] = $sale;

                $data[$k]['total_discount'] = $Credit_Note[0]['total_discount'];

                $data[$k]['credit_note_list'] = $Credit_Note;
                $data[$k]['total_spc_discount'] = $sale->total_spc_discount;

            }

            array_push($bucketData, $data);

        }

        $this->view->bucketData = $bucketData;

        $distinct_arr_distributor = array_unique($arr_distributor);
        $distinct_arr_distributor = array_values($distinct_arr_distributor);

        $this->view->priceAndDetails = $priceAndDetails;

        $QPGB = new Application_Model_PayGroupBank();
        $getDetailPayBank = $QPGB->getPaymentGroupBank($priceAndDetails[0]['payment_id']);

        $this->view->detailPayBank = $getDetailPayBank;

        $QPGT = new Application_Model_PayGroupTran();
        $usePaygroup = $QPGT->getUsePaymentGroupByPaymentID($priceAndDetails[0]['payment_id']);

        $this->view->usePaygroup = $usePaygroup;

        $messages = $flashMessenger->setNamespace('error')->getMessages();

    }

    $this->view->messages = $messages;  

?>