<?php

    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $messages_success = $flashMessenger->setNamespace('success')->getMessages();

    $print_type = $this->getRequest()->getParam('print_type');
    $list = $this->getRequest()->getParam('list');
    $start_date = $this->getRequest()->getParam('start_date');
    $end_date = $this->getRequest()->getParam('end_date');

    $start_invoice1 = $this->getRequest()->getParam('start_invoice1');
    $start_invoice2 = $this->getRequest()->getParam('start_invoice2');
    $end_invoice1 = $this->getRequest()->getParam('end_invoice1');
    $end_invoice2 = $this->getRequest()->getParam('end_invoice2');

    $group_id = $this->getRequest()->getParam('group_id');

    $params = array(
        'print_type' => $print_type,
        'list' => $list,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'start_invoice1' => $start_invoice1,
        'start_invoice2' => $start_invoice2,
        'end_invoice1' => $end_invoice1,
        'end_invoice2' => $end_invoice2,
        'group_id' => $group_id
    );

    $this->view->params = $params;

    if ($this->getRequest()->getMethod() == 'POST') {

        set_time_limit(0);
        ini_set('memory_limit', -1);
        $this->_helper->layout->disableLayout();
        
        try {

            $show_imei = '';
            $type_inv = '8';

            if(!$type_inv){
                echo 'Invalid Date Please Check Type Invoice';
                exit();
            }


            if(!$print_type){
                $flashMessenger->setNamespace('error')->addMessage("Please input type!");
                $this->_redirect(HOST.'finance/print-multi-invoice/'.( $params ? '?'.http_build_query($params).'&' : '?' ));

            }

            $this->view->web_post = 'POST';

            $QMarket = new Application_Model_Market();

            $list_data = array();

            switch ($print_type) {
                case '1':

                    if(!$list){
                        $flashMessenger->setNamespace('error')->addMessage("Please input type!");
                        $this->_redirect(HOST.'finance/print-multi-invoice/'.( $params ? '?'.http_build_query($params).'&' : '?' ));
                    }
                    
                    $list_data = trim($list);
                    $list_data = str_replace(" ","",$list_data);
                    $list_data = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $list_data);
                    $list_data = explode("\n", $list_data);
                    $list_data = array_values(array_unique($list_data));
                    break;
                case '2':

                    $check_dete = true;
                    $check_invoice = true;

                    if(!$start_date || !$end_date){
                        $check_dete = false;
                    }
                    
                    // if(!$start_date){
                    //     $flashMessenger->setNamespace('error')->addMessage("Please input start date!");
                    //     $this->_redirect(HOST.'finance/print-multi-invoice/'.( $params ? '?'.http_build_query($params).'&' : '?' ));
                    // }

                    // if(!$end_date){
                    //     $flashMessenger->setNamespace('error')->addMessage("Please input end date!");
                    //     $this->_redirect(HOST.'finance/print-multi-invoice/'.( $params ? '?'.http_build_query($params).'&' : '?' ));
                    // }

                    $start_invoice = '';
                    $end_invoice = '';

                    if(!$start_invoice1 || !$start_invoice2 || !$end_invoice1 || !$end_invoice2){
                        $check_invoice = false;
                    }else{
                        $start_invoice = 'IN'.$start_invoice1.'-'.$start_invoice2;
                        $end_invoice = 'IN'.$end_invoice1.'-'.$end_invoice2;

                        $params['start_invoice'] = $start_invoice;
                        $params['end_invoice'] = $end_invoice;
                    }

                    if(!$check_invoice && !$check_dete){

                        if(!$check_invoice){
                            $flashMessenger->setNamespace('error')->addMessage("Please input invoice number or invoice date!");
                            $this->_redirect(HOST.'finance/print-multi-invoice/'.( $params ? '?'.http_build_query($params).'&' : '?' ));
                        }

                        if(!$check_dete){
                            $flashMessenger->setNamespace('error')->addMessage("Please input invoice date!");
                            $this->_redirect(HOST.'finance/print-multi-invoice/'.( $params ? '?'.http_build_query($params).'&' : '?' ));
                        }
                    }

                    $getDataMarket = $QMarket->getInvoice($params);

                    foreach ($getDataMarket as $key => $value) {
                        array_push($list_data, $value['invoice_number']);
                    }

                    break;
            }

            if(!$list_data){
                $flashMessenger->setNamespace('error')->addMessage("Not Find Invoice!");
                $this->_redirect(HOST.'finance/print-multi-invoice/'.( $params ? '?'.http_build_query($params).'&' : '?' ));
            }

            //check invoice

            $params['list_invoice'] = $list_data;

            $checkInvoice = $QMarket->getInvoice($params);

            $temp_invoice_list = $list_data;
            foreach ($checkInvoice as $key => $value) {
                foreach ($temp_invoice_list as $key_sub => $value_sub) {
                    if($value['invoice_number'] == $value_sub){
                        unset($temp_invoice_list[$key_sub]);
                    }
                }
            }

            $temp_invoice_list = array_values($temp_invoice_list);

            if($temp_invoice_list){

                $wrong_invoice = '';

                foreach ($temp_invoice_list as $key => $value) {
                    if($key == 0){
                        $wrong_invoice = $value;
                    }else{
                        $wrong_invoice .= ', '.$value;
                    }
                }
                $flashMessenger->setNamespace('error')->addMessage("Please check! Not Find Invoice : ".$wrong_invoice);
                $this->_redirect(HOST.'finance/print-multi-invoice/'.( $params ? '?'.http_build_query($params).'&' : '?' ));
            }

            //check print type A4 only (Brand Shop By Dealer (11),Dealer and Hub (1),KR-Dealer (2),Staff (6))
            $checkData = $QMarket->checkInvoiceNotAllowPrintA4($list_data);

            if($checkData){

                $wrong_invoice = '';

                foreach ($checkData as $key => $value) {
                    if($key == 0){
                        $wrong_invoice = $value['invoice_number'];
                    }else{
                        $wrong_invoice .= ', '.$value['invoice_number'];
                    }
                }
                $flashMessenger->setNamespace('error')->addMessage("Please check! Invoice wrong type : ".$wrong_invoice);
                $this->_redirect(HOST.'finance/print-multi-invoice/'.( $params ? '?'.http_build_query($params).'&' : '?' ));
            }

            $this->view->list_data = $list_data;

            $page = 1;
            $limit = 1;
            $total = 0;

            $QGood = new Application_Model_Good();
            $QMarket = new Application_Model_Market();
            $QImei = new Application_Model_Imei();
            $QMarketProduct = new Application_Model_MarketProduct();
            $QOffice = new Application_Model_Office;
            $QDistributor = new Application_Model_Distributor();

            $array_type_inv = [];
            $array_khuyenmai = [];
            $array_invoice_note = [];
            $array_invoice_type = [];
            $array_event = [];
            $array_fee = [];
            $array_imeis = [];
            $array_market = [];
            $array_mk_goods = [];
            $array_joy = [];
            $array_staff = [];
            $array_invoice_prefix = [];
            $array_warehouse_id = [];
            $array_sn = [];
            $array_market_product = [];
            $array_customer_brandshop_id = [];
            $array_customerbrandshop = [];
            $array_distributor = [];
            $array_credit_type = [];
            $array_Tag = [];
            $array_dis_group = [];

            $tmp = $QGood->fetchAll();
            $all_goods = array();
            foreach ($tmp as $k => $v) {
                $all_goods[$v['id']] = $v;
            }
            $this->view->all_goods = $all_goods;
            $this->view->goods_cached = $QGood->get_cache();


            $QGoodCategory = new Application_Model_GoodCategory();
            $this->view->good_categories = $QGoodCategory->get_cache();

            $QGoodColor = new Application_Model_GoodColor();
            $this->view->good_colors = $QGoodColor->get_cache();

            $QGoodColor = new Application_Model_GoodColor();
            $this->view->colors_list = $QGoodColor->get_cache();

            foreach ($list_data as $key => $value) {

                // $this->view->type_inv = $type_inv;
                $array_type_inv[$key] = $type_inv;

                $sn = $value;

                $params = array(
                    'inv' => $sn,
                    'group_sn' => 1,
                    'isbacks' => 0,
                    //    'outmysql_time' => 1,
                    'group_good' => 10,
                );

                // $market = $QMarket->fetchPagination($page, $limit, $total, $params);
                $market_main = $QMarket->getMarketOnly($params);
                // print_r($market_main);die;

                if (!(isset($market_main) && isset($market_main[0]) && $market_main[0])) {

                } else {
                    $market = $market_main[0];
                }
                $warehouse_id = $market['warehouse_id'];
                $distributor = $QDistributor->find($market['d_id']);
                $distributor = $distributor->current();

                if(isset($market['good_id']) and in_array($market['good_id'], array(373,267)) )
                    // $this->view->khuyenmai = 1;
                    $array_khuyenmai[$key] = 1;

                if ($market['d_id'] == 9196) 
                        // $this->view->invoice_note = 'ผู้ซื้อไม่ได้รับค่าใช้จ่าย';
                        $array_invoice_note[$key] = 'ผู้ซื้อไม่ได้รับค่าใช้จ่าย';
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
                            // $this->view->invoice_type = $invoice[0]['invoice_type'];
                            $array_invoice_type[$key] = $invoice[0]['invoice_type'];
                        } else {
                            /////không in hóa đơn//////
                            // $this->view->invoice_note =
                            //     'ผู้ซื้อไม่ได้รับใบแจ้งหนี้ (พนักงานขายปลีก)';
                            // $this->view->invoice_type = $invoice[0]['invoice_type'];

                            $array_invoice_note[$key] = 'ผู้ซื้อไม่ได้รับใบแจ้งหนี้ (พนักงานขายปลีก)';
                            $array_invoice_type[$key] = $invoice[0]['invoice_type'];

                        }
                    }

                    if ($market['d_id'] == OPPO_GIFT) { 
                        // $this->view->invoice_note = 'ผู้ซื้อไม่ได้รับค่าใช้จ่าย';
                        // $this->view->event = 1;
                        $array_invoice_note[$key] = 'ผู้ซื้อไม่ได้รับค่าใช้จ่าย';
                        $array_event[$key] = 1;
                    }
                    

                    
                    if ($market['d_id'] == OPPO_DEMO) {
                        // $this->view->invoice_note = 'ผู้ซื้อไม่ได้รับค่าใช้จ่าย (พื้นที่ขาย)';
                        $array_invoice_note[$key] = 'ผู้ซื้อไม่ได้รับค่าใช้จ่าย (พื้นที่ขาย)';
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
                        // $this->view->event = 1;
                        $array_event[$key] = 1;
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

                     // $this->view->fee = $fee;
                     $array_fee[$key] = $fee;
                 }

                //Tanong
                $mk_goods = $QMarket->fetchInvoice($params); //////////////
                //Tanong
                if($show_imei==1){
                    $imei_list = $QMarket->fetchWithImei($sn);
                    // $this->view->imeis = $imei_list;
                    $array_imeis[$key] = $imei_list;

                }else{
                    // $this->view->imeis = null;
                    $array_imeis[$key] = null;
                }

                //print_r($mk_goods);
                unset($params['sn']);

                // $this->view->market = $market;
                // $this->view->mk_goods = $mk_goods;
                $array_market[$key] = $market;
                $array_mk_goods[$key] = $mk_goods;

                //fetchWithImei($sn)

                if (isset($joy) and $joy) {
                    // $this->view->joy = 1;
                    $array_joy[$key] = $joy;
                }

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
                    // $this->view->staff = $staff;
                    $array_staff[$key] = $staff;
                }

                

                //set invoice time
                if (!$market['invoice_time']) {
                    $data = array('invoice_time' => date('Y-m-d H:i:s'));
                    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                    $QMarket->update($data, $where);
                }

                $QInvoicePrefix = new Application_Model_InvoicePrefix();
                $invoice_prefix = $QInvoicePrefix->fetchAll();

                // $this->view->invoice_prefix = $invoice_prefix;
                // $this->view->warehouse_id = $warehouse_id;
                // $this->view->sn = $sn;
                $array_invoice_prefix[$key] = $invoice_prefix;
                $array_warehouse_id[$key] = $warehouse_id;
                $array_sn[$key] = $sn;


                if(isset($mk_bvg_joint) and $mk_bvg_joint)
                // $this->view->market_product = $mk_bvg_joint;
                $array_market_product[$key] = $mk_bvg_joint;


                $QCredit        = new Application_Model_Credit();
                $where_c        = $QCredit->getAdapter()->quoteInto('id = ?', $distributor->credit_type);
                $credit_type    = $QCredit->fetchRow($where_c);

                $QTag        = new Application_Model_Tag();
                $Tag = $QTag->fetch_Tag($sn);

                if($customer_brandshop_id !='')
                {
                    $CustomerBrandShop = $QMarket->getCustomerBrandShop($customer_brandshop_id);
                    // $this->view->customer_brandshop_id =$customer_brandshop_id;
                    // $this->view->customerbrandshop = $CustomerBrandShop;
                    $array_customer_brandshop_id[$key] = $customer_brandshop_id;
                    $array_customerbrandshop[$key] = $CustomerBrandShop;

                }

                if ($distributor) {
                    // $this->view->distributor = $distributor;
                    // $this->view->credit_type = $credit_type;
                    // $this->view->Tag = $Tag;

                    $array_distributor[$key] = $distributor;
                    $array_credit_type[$key] = $credit_type;
                    $array_Tag[$key] = $Tag;

                    $QDG = new Application_Model_DistributorGroup();
                    $dis_group = $QDG->find($distributor['group_id']);
                    $dis_group = $dis_group->current();
                    // $this->view->dis_group = $dis_group;
                    $array_dis_group[$key] = $dis_group;
                }

            }

            // print_r($array_type_inv);
            // echo '<br>';
            // print_r($array_khuyenmai);
            // echo '<br>';
            // print_r($array_invoice_note);
            // echo '<br>';
            // print_r($array_invoice_type);
            // echo '<br>';
            // print_r($array_event);
            // echo '<br>';
            // print_r($array_fee);
            // echo '<br>';
            // print_r($array_imeis);
            // echo '<br>';
            // print_r($array_market);
            // echo '<br>';
            // print_r($array_mk_goods);
            // echo '<br>';
            // print_r($array_joy);
            // echo '<br>';
            // print_r($array_staff);
            // echo '<br>';
            // print_r($array_invoice_prefix);
            // echo '<br>';
            // print_r($array_warehouse_id);
            // echo '<br>';
            // print_r($array_sn);
            // echo '<br>';
            // print_r($array_market_product);
            // echo '<br>';
            // print_r($array_customer_brandshop_id);
            // echo '<br>';
            // print_r($array_customerbrandshop);
            // echo '<br>';
            // print_r($array_distributor);
            // echo '<br>';
            // print_r($array_credit_type);
            // echo '<br>';
            // print_r($array_Tag);
            // echo '<br>';
            // print_r($array_dis_group);
            // echo '<br>';
            // die;

            $this->view->type_inv = $array_type_inv;
            $this->view->khuyenmai = $array_khuyenmai;
            $this->view->invoice_note = $array_invoice_note;
            $this->view->invoice_type = $array_invoice_type;
            $this->view->event = $array_event;
            $this->view->fee = $array_fee;
            $this->view->imeis = $array_imeis;
            $this->view->market = $array_market;
            $this->view->mk_goods = $array_mk_goods;
            $this->view->joy = $array_joy;
            $this->view->staff = $array_staff;
            $this->view->invoice_prefix = $array_invoice_prefix;
            $this->view->warehouse_id = $array_warehouse_id;
            $this->view->sn = $array_sn;
            $this->view->market_product = $array_market_product;
            $this->view->customer_brandshop_id = $array_customer_brandshop_id;
            $this->view->customerbrandshop = $array_customerbrandshop;
            $this->view->distributor = $array_distributor;
            $this->view->credit_type = $array_credit_type;
            $this->view->Tag = $array_Tag;
            $this->view->dis_group = $array_dis_group;

        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
            $this->_redirect(HOST.'finance/print-multi-invoice/'.( $params ? '?'.http_build_query($params).'&' : '?' ));
        }

    }

    $this->view->messages_success = $messages_success;
    $this->view->messages = $messages;






    


    



