<?php
$this->_helper->layout->disableLayout();
$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$messages_success = $flashMessenger->setNamespace('success')->getMessages();

if ($this->getRequest()->getMethod() == 'POST'){

    //print_r($_POST);die;
    $company_id      = $this->getRequest()->getParam('company_id');
    $privileges_sn      = $this->getRequest()->getParam('privileges_sn');
    set_time_limit(0);
    ini_set('memory_limit', -1);
    
    $QQuotaProduct = new Application_Model_EpPrivilegesQuotaProduct();
    $QQuotaProduct->_initConfig($company_id);
    $db = Zend_Registry::get('db');

    $QDistributor = new Application_Model_Distributor();
    $QStaffSalesOrder = new Application_Model_EpPrivilegesTranOrder();

    $uniqid = uniqid('', true);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    try {

        $data_order = $QStaffSalesOrder->staff_sales_order($company_id,$privileges_sn);

        //print_r($data_order);die;
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
            $order_type=3;  //STAFF
            $d_id       = trim($value['distributor_id'],'"');
            $bank_id    = trim($value['bank_id'],'"');
            $payment_date    = trim($value['payment_date'],'"');
            $payment_slip_image = trim($value['payment_slip_image'],'"');

            $staff_code = $value['staff_code'];
            $staff_name = $value['staff_name'];
            $company_id = 1;
            $good_id    = trim($value['good_id'],'"');
            $good_color = trim($value['good_color'],'"');
            $cat_id         = trim($value['cat_id'],'"');
            $qty            = trim($value['qty'],'"');
            $sale_off_percent = trim($value['discount_type'],'"');
            $unit_price       = trim($value['master_unit_price'],'"');
            $total_price      = trim(($value['master_unit_price']*$value['qty']),'"');
            
            $total_spc_discount = 0;

            $total_qty +=$qty;

            $warehouse_id=$value['warehouse_id'];
            $salesman=$userStorage->id;
            $rank=$value['rank'];
            $sipping_add=$value['shipping_id'];

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

            $company_id = $value['company_id'];
            $QStaffSalesOrder->_initConfig($company_id);
        }

        /*-------ถุงแถม---------------*/
        $cat_ids[] = 12;
        $good_ids[] = 127;
        $good_colors[] = 1;
        $nums[] = $total_qty;
        $prices[] = 0;
        $sale_off_percents[] = 100;
        $totals[] = 0;

        //print_r($sale_off_percents);die;
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
            'staff_code'           => 0,

        );
        //print_r($params);die;

        $result = $this->saveAPI($params);
        $check_result="0";
        //print_r($result); die; 
        if ($result['code'] == 1) { //success

            $QMarket = new Application_Model_Market();
            
            /*------------Check Money--------------------*/

            $data['payment_type'] = 'CA';

            //check money
            $QCheckmoney = new Application_Model_Checkmoney();
            $QMarketProduct = new Application_Model_MarketProduct();

            $payment_bank_transfer=0;
            $payment_servicecharge=0;
            $payment_service=0;

            $select_sn = array();
            $select_sn[] = $QCheckmoney->getAdapter()->quoteInto('sn = ?', $result['sn']);
            $select_sn[] = $QCheckmoney->getAdapter()->quoteInto('type = ?', 2); 

            //print_r($select_sn);die;
            $check_sn_exist = $QCheckmoney->fetchRow($select_sn);

            //print_r($check_sn_exist);die;
            if (!$check_sn_exist) {
                $payment_service_val=0;


                $sn_total = 0;
                $intRebate = intval($QMarketProduct->getPrice($result['sn']));
                $sn_total = $QMarket->getPrice($result['sn']) - $intRebate;
                $payment_order=$sn_total;

                for($i=0;$i<count($payment_order);$i++){
                    $payment_service_val +=$payment_service[$i];
                }
                //print_r($payment_order);die;
                $note_new='Payment Order='.number_format($sn_total,2).' ค่าอะไหล่และค่าบริการ='.number_format($payment_service_val,2);

                //data for checkmoney transaction
                $data_ch = array(
                    'd_id'       => $d_id,
                    'payment'    => $payment_date,
                    'pay_time'   => $payment_date,
                    'pay_service'     => $payment_service_val,
                    'output'     => $sn_total,
                    'pay_money'  => -$sn_total,
                    'type'       => 2,
                    'sn'         => $result['sn'],
                    'user_id'    => $userStorage->id,
                    'create_by'  => $userStorage->id,
                    'create_at'  => $date,
                    'note'       => $note_new,
                    'company_id' => $company_id,
                    'sales_confirm_date'    => $date,
                    'sales_confirm_id'    => $userStorage->id,
                );
                //print_r($data_ch);die;

                $checkUpdateCheckMoney = $QCheckmoney->insert($data_ch);

                $payment_order_val=0;$payment_bank_transfer_val=0;$pay_service_val=0;$i=0;
                $payment_order_val =$payment_order;

                if($payment_type=="CA"){
                    if ($payment_order_val >=0) 
                    {
                        $status=1;
                        if($retailer_rank ==10){
                            $status=0;
                        }
                        // $pay_money = $payment_order+$payment_bank_transfer;
                        $QCheckmoneyPaymentorder = new Application_Model_CheckmoneyPaymentorder();
                        $QCheckmoneyPaymentorder->insert(array(
                            'd_id'          => $d_id,
                            'sn'            => $result['sn'],
                            'payment_order' => ($payment_order_val),
                            'pay_banktransfer' => $payment_bank_transfer_val,
                            'status'        => $status,
                            'created_at'    => $date,
                            'created_by'    => $userStorage->id,
                            'sales_confirm_date'    => $date,
                            'sales_confirm_id'    => $userStorage->id,
                        ));
                    } //End check payment order
                }

            }

            $data['confirm_so'] = 1;

            // $data['shipping_yes_time'] = $date;
            // $data['shipping_yes_id'] = $staff_code;
            // $data['pay_time'] = $payment_date;
            // $data['pay_user'] = $staff_code;
            $data['customer_name'] = $staff_name;

            $data['sales_confirm_date'] = $date;
            $data['sales_confirm_id'] = $userStorage->id;

            $sn = $result['sn'];
            //print_r($data);die;
            $where = array();
            $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
            $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);
            $QMarket->update($data, $where);

            /*------------------------------*/

            $payment_bank_transfer_val=0;$i=0;
            $payment_service_val=0;$payment_servicecharge_val=0;


            $payment_bank_transfer_val =$payment_bank_transfer[$i];
            $payment_service_val =$payment_service[$i];
            $payment_servicecharge_val =$payment_servicecharge[$i];
            $pay_time_val=$pay_time[$i];

            //pay_banktransfer

            $QCheckMoney    = new Application_Model_Checkmoney();
            $QStoreaccount  = new Application_Model_Storeaccount();
            //$userStorage    = Zend_Auth::getInstance()->getStorage()->read();

            //$pay_money = $payment_order+$payment_bank_transfer;

            $renew_file_name = ($i+1).'_'.$sn.'_'.$time.'_'.$userStorage->id.'.'.pathinfo($_FILES['file']['name'][$i],PATHINFO_EXTENSION);
            $file_name_upload = '/pay_slips/'.$sn.'/'.$renew_file_name;
            // $file_name_upload = '/pay_slips/'.$sn.'/'.$_FILES['file']['name'][$i];

            $note_new='PayMoney='.number_format($payment_order_val,2) .' Fee transfer='.number_format($payment_bank_transfer_val,2).' Service Charge='.number_format($payment_servicecharge_val,2).' ค่าอะไหล่และค่าบริการ='.number_format($payment_service_val,2);

            $credit_card = 0;
            if($use_credit_card_input[$i]){
                $credit_card = $use_credit_card_input[$i];
            }

            $data = array(
                'd_id'                  => $d_id,
                'bank'                  => $bank_id,
                'pay_money'             => $payment_order_val,
                'pay_servicecharge'     => $payment_servicecharge_val,
                'pay_banktransfer'      => $payment_bank_transfer_val,
                'pay_service'           => $payment_service_val,
                'type'                  => 1,
                'output'                => $sn_total,
                'pay_time'              => $payment_date,
                'bank_serial'           => null,
                'bank_transaction_code' => null,
                'note'                  => $note_new,
                'content'               => null,
                'company_id'            => $company_id,
                'sn'                    => $sn,
                'file_pay_slip'         => $payment_slip_image,
                'user_id'               => $userStorage->id,
                'create_by'             => $userStorage->id,
                'create_at'             => $date,
                'sales_confirm_id'      => $userStorage->id,
                'sales_confirm_date'    => $date,
                'addition'              => 1,
                'credit_card'           => $credit_card
            );

            if($lacksurplus > 0){
                // $data['payment_surplus'] = $lacksurplus;
                $data['payment_surplus'] = 0;
            }

            if($lacksurplus < 0){
                // $data['lack_of_money'] = $lacksurplus*-1;
                $data['lack_of_money'] = 0;
            }

            $checkUpdateCheckMoney2 = $QCheckmoney->getCheckDuplicate($sn,$file_name_upload,$payment_order_val);
            if (!$checkUpdateCheckMoney2) {
                if($ch_id){
                    // echo "1";
                    $old_checkmoney = $QCheckMoney->find($ch_id)->current();
                    $data['user_id'] = $userStorage->id;
                    $data['updated_at'] = $date;
                    $where = $db->quoteInto('id = ?',$ch_id);
                    $QCheckMoney->update($data,$where); 
                    
                }else{
                    // echo "2";
                    $data['create_by'] = $userStorage->id;
                    $data['create_at'] = $date;
                    //print_r($data);die;
                    $QCheckMoney->insert($data);
                }

            }


            /*------------End Check Money--------------------*/

            $data_order = array(
                'sales_order_sn'=>$result['sn']
                ,'status'=>3
                ,'hr_confirm_date'=>$date
                ,'hr_comfirm_by'=>$userStorage->id
            );
            $where = array();
            $where[] = $QStaffSalesOrder->getAdapter()->quoteInto('privileges_sn = ?', $privileges_sn);
            $update_sn = $QStaffSalesOrder->update($data_order, $where);
            //print_r($result);die;

            $QCS = new Application_Model_CronSo();
            $cron_data = array(
                'sn' => $result['sn'],
                'status' => 1,
                'created_date' => date('Y-m-d H:i:s')
            );
            $QCS->insert($cron_data);

            //add paygorup
            $QPG = new Application_Model_PayGroup();
            $QPGBal = new Application_Model_PayGroupBalance();

            $payment_id = date('YmdHis') . substr(microtime(), 2, 4);
            $sale_order = $sn;
            $payment_group = 0;
            $case_text = null;
            $money = $sn_total;
            $lacksurplus = 0.00;

            $pay_bank_transfer = 0.00;
            $pay_servicecharge = 0.00;

            $created_date = date('Y-m-d H:i:s');
            $modified_date = date('Y-m-d H:i:s');
            $status = 1;

            $QPG->insert(array(
                'payment_no' => $payment_id,
                'payment_id' => $payment_id,
                'sale_order' => $sale_order,
                'd_id' => $d_id,
                'payment_group' => $payment_group,
                'case_text' => $case_text,
                'money' => $money,
                'lacksurplus' => $lacksurplus,
                'pay_bank_transfer' => $pay_bank_transfer,
                'pay_servicecharge' => $pay_servicecharge,

                'created_at' => $userStorage->id,
                'created_date' => $created_date,
                'modified_at' => $userStorage->id,
                'modified_date' => $modified_date,
                'status' => $status
            ));

            $QPGBal->insert(array(
                'payment_id' => $payment_id,
                'distributor_id' => $d_id,
                'total_amount' => 0,
                'use_total' => 0,
                'balance_total' => 0,
                'status' => $status,
                'create_date' => $created_date,
                'create_by' => $userStorage->id,
                'update_date' => $modified_date,
                'update_by' => $userStorage->id,
                'use_status' => 0,
                'remark' => null
            ));


            $check_result=json_encode(['code'=>1, 'privileges_sn'=>$privileges_sn, 'sales_order_sn'=>$result['sn'], 'messege'=>"Done"]);
        }else{
            $check_result=json_encode(['code'=>0, 'messege'=>"Error"]);
        }
        /*------------------End------------------------*/
        $this->view->result = $check_result;

    }//end of try
    catch (Exception $e) {
        $db->rollback();
        $check_result=json_encode(['code'=>0, 'messege'=>$e->getMessage()]);
        $this->view->result = $check_result;
       // $this->view->error = $e->getMessage();
       // $progress->flush(0);
    }

    


}



