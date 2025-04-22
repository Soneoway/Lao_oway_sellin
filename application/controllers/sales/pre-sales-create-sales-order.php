<?php
$this->_helper->layout->disableLayout();
$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$messages_success = $flashMessenger->setNamespace('success')->getMessages();

//Sales Request 
if ($this->getRequest()->getMethod() == 'POST'){

    //print_r($_POST);die;
    $presales_sn      = $this->getRequest()->getParam('presales_sn');
    $unit_prices      = $this->getRequest()->getParam('unit_price');
    $total_prices      = $this->getRequest()->getParam('total_price');

    set_time_limit(0);
    ini_set('memory_limit', -1);
    $db = Zend_Registry::get('db');

    $QDistributor = new Application_Model_Distributor();
    $QPreSalesOrder = new Application_Model_PreSalesOrder();
    $QMarket = new Application_Model_Market();
    $QGood                = new Application_Model_Good();
    
    $uniqid = uniqid('', true);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    try {

        $get_resule_stock = [];$error_data=[];
        $total_price = '0';

        $data_order = $QPreSalesOrder->pre_sales_order($presales_sn);

        //--- Check Stock

        foreach($data_order as $key => $val)
        {
            $distributor_id=$val['distributor_id'];
            $rank=$val['rank'];
            $warehouse_id=$val['warehouse_id'];
            $is_sales_price="1";
            $type=$val['order_type'];

            foreach($val as $key1 => $val1)
            {
                //print_r($key1);
                //print_r($val1);
                //die;
                $get_resule_stock[$key][$key1] = $val1;

                if($key1=="cat_id"){
                    $cat_id=$val1;
                }
                if($key1=="good_id"){
                    $good_id=$val1;
                }
                if($key1=="good_color"){
                    $good_color=$val1;
                }
                if($key1=="qty"){
                    $num=$val1;
                }
            
                //array_push($get_resule_stock[$key1], $val1);
            }

            
            $result = $QGood->get_price($num, $good_id, $good_color, $cat_id, $distributor_id,$warehouse_id, $is_sales_price, $is_return, $type, $id , 0 , $campaign_id);
            $quota_error_message = '';$stock_error_message = '';
            //print_r($result);die;

            switch ($result['code'])
            {
                case 1:

                    $params = array(
                        'distributor_id' => $distributor_id,
                        'good_id'        => $good_id,
                        'good_color'     => $good_color,
                        'num'            => $num,   
                        'rank'           => $rank,   
                        'warehouse_id'   => $warehouse_id,   
                        'cat_id'         => $cat_id,   
                        'sales_sn'       => $sales_sn,   
                        'type'           => $type,   
                    );
                    //print_r($params);die;
                    $quota      = $QMarket->checkQuotaOppo($params);
                    //print_r($quota);die;
                    if($quota =="0"){
                        $total_price =  $result['price'];
                    }else{
                        $quota_error_message = "Over Quota!";
                    }
                    break;
                case 0:
                    $total_price = $result['price'];
                    $stock_error_message = "Stock = 0" ;
                    break;
                case - 1:
                    $total_price = $result['price'];
                    $stock_error_message = "Stock = 0" ;
                    break;
                case - 2:
                    $total_price = $result['price'];
                    $stock_error_message = "Stock = ".$result['quantity'];
                    break;
                case - 3:
                    $total_price = $result['price'];
                    $stock_error_message = "Stock = ".$result['quantity'];
                    break;
                default:
                    $total_price = $result['price'];
                    $stock_error_message = "Stock = 0";
                    break;
            }
            $get_resule_stock[$key]['total_price'] = $total_price;
            $get_resule_stock[$key]['stock_error_message'] = $stock_error_message;
            $get_resule_stock[$key]['quota_error_message'] = $quota_error_message;


        }

        //print_r($get_resule_stock);die;
        $error_message ="";
        foreach ($get_resule_stock as $key_chk) 
        {
            //print_r($key_chk['stock_error_message']);die;
            $data_stock_error_message       .= $key_chk['stock_error_message'];
            $data_quota_error_message       .= $key_chk['quota_error_message'];

            if($data_stock_error_message !="")
            {
                $error_message = $key_chk['good_name']. '  '.$key_chk['good_color_name']. ' [Error : '.$key_chk['stock_error_message']."]";
                $check_result=json_encode(['code'=>0, 'messege'=>$error_message]);
                $this->view->result = $check_result;
                return $check_result;
            }

            if($data_quota_error_message !="")
            {
                $error_message = $key_chk['good_name']. '  '.$key_chk['good_color_name']. ' [Error : '.$key_chk['quota_error_message']."]";
                $check_result=json_encode(['code'=>0, 'messege'=>$error_message]);
                $this->view->result = $check_result;
                return $check_result;
            }

        }


        //$db->beginTransaction();
        //print_r($get_resule_stock);die;
        $date = date('Y-m-d H:i:s');
        $cat_ids = array();
        $good_ids = array();
        $good_colors = array();
        $nums = array();
        $prices = array();
        $sale_off_percents = array();
        $totals = array();

        $row =0;$total_qty=0;
        foreach ($data_order as $key => $value) 
        {
            //print_r($value);die;
            //$order_type = 'STAFF';
            //$order_type=1;  //STAFF

            $d_id       = trim($value['distributor_id'],'"');
            $bank_id    = trim($value['bank_id'],'"');
            if($bank_id==''){
                $bank_id=null;
            }
            $payment_date    = trim($value['payment_date'],'"');
            if($payment_date==''){
                $payment_date=null;
            }
            $payment_slip_image = trim($value['payment_slip_image'],'"');

            $staff_code = $value['staff_code'];
            $staff_name = $value['staff_name'];
            $company_id = 1;
            $good_id    = trim($value['good_id'],'"');
            $good_color = trim($value['good_color'],'"');
            $cat_id         = trim($value['cat_id'],'"');
            $qty            = trim($value['qty'],'"');
            $sale_off_percent = trim($value['discount_type'],'"');
            $order_type=$value['order_type'];

            /*$unit_prices = str_replace('[', '', $unit_prices);
            $unit_prices = str_replace(']', '', $unit_prices);
            $unit_prices = str_replace('"', '', $unit_prices);

            $total_prices = str_replace('[', '', $total_prices);
            $total_prices = str_replace(']', '', $total_prices);
            $total_prices = str_replace('"', '', $total_prices);

            $unit_price_tmp = explode(',',$unit_prices);
            foreach ($unit_price_tmp as $value1) {
                $unit_price_tmps = explode('-',$value1);
                if($good_id==$unit_price_tmps[0] && $good_color==$unit_price_tmps[1]){
                    $unit_price       = $unit_price_tmps[2];
                }
            }

            $total_prices_tmp = explode(',',$total_prices);
            foreach ($total_prices_tmp as $value2) {
                $total_prices_tmps = explode('-',$value2);
                if($good_id==$total_prices_tmps[0] && $good_color==$total_prices_tmps[1]){
                    $total_price       = $total_prices_tmps[2];
                }
            }*/

            $unit_price = 0;$total_price = 0;
            foreach ($get_resule_stock as $value1){
                //print_r($value1);die;
                //$unit_price_tmps = explode('-',$value1);
                if($good_id==$value1['good_id'] && $good_color==$value1['good_color'])
                {
                    $unit_price = $value1['total_price']/$value1['qty'];
                    $total_price       = $value1['total_price'];
                }
            }

           /* foreach ($get_resule_stock as $value2) {
                $total_prices_tmps = explode('-',$value2);
                if($good_id==$total_prices_tmps[0] && $good_color==$total_prices_tmps[1]){
                    $total_price       = $total_prices_tmps[2];
                }
            }*/

            //$unit_price       = 1;
            //$total_price      = 1;
            
            $total_spc_discount = 0;

            $total_qty +=$qty;

            $warehouse_id=$value['warehouse_id'];
            $salesman=$userStorage->id;
            $rank=$value['rank'];
            $sipping_add=$value['shipping_id'];
            $sales_catty_id = $value['sell_id'];
            if($good_id == ''){
                continue;
            }

            if(!isset($d_id) || $d_id ==''){
                continue;
            }

            $cat_ids[] = $cat_id;
            $good_ids[] = $good_id;
            $good_colors[] = $good_color;
            $nums[] = $qty;
            $prices[] = $unit_price;
            $sale_off_percents[] = $sale_off_percent;
            $totals[] = $total_price;
            $texts[] = "";
            $ids[] = 0;
            $tags[] = $tax_po;
            $row +=1;


            /*------------------Start------------------------*/

            $market_general_id=0;               
            $SearchBox='';
            $distributor_id=$d_id;


            $life_time=1;
            $include_shipping_fee=1;
            $user_uncheck=0;

            $campaign = array('0'    => '');

            $market_general_data = array('shipment_id'    => 0);

            $save_service='sales';
            $creditnote_data='';
            $deposit_data='';
        }

        /*-------ถุงแถม---------------*/
        $cat_ids[] = 12;
        $good_ids[] = 127;
        $good_colors[] = 1;
        $nums[] = $total_qty;
        $prices[] = 0;
        $sale_off_percents[] = 100;
        $totals[] = 0;

        
        //print_r($payment_date);die;
        $params = array(
            'order_type'           => $order_type,
            'market_general_id'    => $market_general_id,
            'ids'                  => $ids,
            'save_service'         => $save_service,
            'cat_id'               => $cat_ids,
            'good_id'              => $good_ids,
            'good_color'           => $good_colors,
            'num'                  => $nums,
            'price'                => $prices,
            'total'                => $totals,
            'text'                 => $texts,
            'pay_time'             => $payment_date,
            // 'shipping_yes_time'    => null,
            // 'shipping_yes_id'      => null,
            'distributor_id'       => $distributor_id,
            'warehouse_id'         => $warehouse_id,
            'salesman'             => $salesman,
            'sales_catty_id'       => $sales_catty_id,
            'area_id'              => $area_id,
            'type'                 => $order_type,
            'sale_off_percent'     => $sale_off_percents,
            'sn'                   => $sn,
            'life_time'            => $life_time,
            'isbatch'              => 1,
            'rebate_price'         => $rebate_price,
            'service_id'           => $service_id,
            'ids_bvg'              => $ids_bvg,
            'joint'                => $joint,
            'good_id_bvg'          => $good_ids_bvg,
            'num_bvg'              => $nums_bvg,
            'price_bvg'            => $prices_bvg,
            'total_bvg'            => $totals_bvg,
            'joint_discount'       => $joint_discount,
            'ids_discount'         => $ids_discount,
            'prices_discount'      => $prices_discount,
            'bvg_imei'             => $bvg_imei,
            'distributor_po'       => $distributor_po,
            'gift_id'              => $gift_id,
            'include_shipping_fee' => $include_shipping_fee,
            'user_uncheck'         => $user_uncheck == 'true' ? 1 : 0,
            'campaign'             => $campaign,
            'payment_method'       => $payment_method,
            'id_staff'             => $id_staffs,
            'name_staff_ingame'    => $name_staff_ingames,
            'cmnd_staff_ingame'    => $cmnd_staff_ingames,
            'shipment_type'        => $shipment_types,
            'sophieuthu'           => $sophieuthus,
            'sotienthucte'         => $sotienthuctes,
            'payment_date'         => $payment_date,
            'shipment_id'          => $shipment_id,
            'product_color_key'    => $product_color_key,
            'staff_num'            => $staff_num,
            'for_partner'          => $for_partner,
            'credit_id'            => $credit_id,
            // 'delivery_address'     => $delivery_address, 
            'delivery_fee'         => $delivery_fee,
            'customer_id'          => $customer_id,
            'customer_name'        => $staff_name,
            'customer_tax_number'  => $customer_tax_number,
            'customer_branch_number'  => $customer_branch_number,
            'customer_tax_address' => $customer_tax_address,
            'rank'                 => $rank,
            'edit'                 => $edit,
            'sipping_add'          => $sipping_add,
            'customer_name_for_staff'        => $customer_name_for_staff,
            'total_spc_discount'   => 0,
            'digital_discount'     => 0,
            'market_general_data'  => $market_general_data,
            'creditnote_data'      => $creditnote_data,
            'deposit_data'         => $deposit_data,

        );
        //print_r($params);
        //die; 
        //saveSalesRequestAPI
        $result = $this->saveAPI($params);
        $check_result="0";
        //print_r($result); die; 
        if ($result['code'] == 1) 
        { //success
            $db->beginTransaction();
            $sales_sn = $result['sn'];
            //print_r($result);die;
            $QCS = new Application_Model_CronSo();
            $cron_data = array(
                'sn' => $sales_sn,
                'status' => 1,
                'created_date' => date('Y-m-d H:i:s')
            );
            $QCS->insert($cron_data);

            /*-----------------------------*/
            $data_order = array(
                'sales_order_sn'=>$sales_sn
                ,'status'=>2
                ,'admin_confirm_date'=>$date
                ,'admin_id'=>$userStorage->id
            );

            $where = array();
            $where[] = $QPreSalesOrder->getAdapter()->quoteInto('presales_sn = ?', $presales_sn);
            $update_sn = $QPreSalesOrder->update($data_order, $where);

            try{
              $db->commit();
              $check_result=json_encode(['code'=>1, 'presales_sn'=>$presales_sn, 'sales_order_sn'=>$sales_sn, 'messege'=>"Done"]);
            }catch (exception $e)
            {
              $db->rollback();  
              $check_result=json_encode(['code'=>0, 'messege'=>$e->getMessage()]);
            }      
        }else{
            $check_result=json_encode(['code'=>0, 'messege'=>'Error : Cannot Create Order!']);
        }
        /*------------------End------------------------*/
        $this->view->result = $check_result;
        //return $check_result;

    }//end of try
    catch (Exception $e)
    {
        $db->rollback();
        $check_result=json_encode(['code'=>0, 'messege'=>$e->getMessage()]);
        $this->view->result = $check_result;
       // $this->view->error = $e->getMessage();
       // $progress->flush(0);
    }
    return $check_result;
    
}



