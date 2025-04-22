<?php 
echo '<link href="/css/alert-error-success.css" rel="stylesheet">'; //alert message
// echo "string"; die;
$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$messages_success = $flashMessenger->setNamespace('success')->getMessages();

$staff_code     = $this->getRequest()->getParam('staff_code');
$r_name         = $this->getRequest()->getParam('r_name');
$cat_id         = $this->getRequest()->getParam('cat_id');
$good_id        = $this->getRequest()->getParam('good_id');
$good_color_id  = $this->getRequest()->getParam('good_color_id');
$sipping_add    = $this->getRequest()->getParam('shipping_address');
$qty 		    = $this->getRequest()->getParam('num');
$ids 			= $this->getRequest()->getParam('ids');
$for_partner    = $this->getRequest()->getParam('for_partner',2);
$sale_off_percents    = $this->getRequest()->getParam('sale_off_percent');
$sn             = $this->getRequest()->getParam('sn');

$cat_id = PHONE_CAT_ID;

$params = array(
	'staff_code' 	=> $staff_code,
    'r_name'        => $r_name,
    'cat_id'        => $cat_id,
    'good_id'       => $good_id,
    'good_color_id' => $good_color_id,
    'shipping_address' 	=> $ship_address,
    'num'			=> $qty,
    'sale_off_percent' => $sale_off_percents,
);

$QGood          = new Application_Model_Good();
$goods_cached   = $QGood->get_cache();
// print_r($goods_cached); die;
$QGoodColor     = new Application_Model_GoodColor();
$goodColors     = $QGoodColor->get_cache();
// print_r($goodColors); die;
$QGoodCategory  = new Application_Model_GoodCategory();
$goodCategories = $QGoodCategory->get_cache();
// print_r($goodCategories); die;
if ($cat_id){
    $where = $QGood->getAdapter()->quoteInto('cat_id = ?', $cat_id);
    $goods_acc = $QGood->fetchAll($where, 'name');
    $this->view->goods_acc = $goods_acc;
}


$this->view->params 		= $params;
$this->view->goods_cached   = $goods_cached;
$this->view->goodColors     = $goodColors;
$this->view->goodCategories = $goodCategories;

if ($this->getRequest()->getMethod() == 'POST'){
	
	$staff_code     = $this->getRequest()->getParam('staff_code');
	$r_name         = $this->getRequest()->getParam('r_name');
	$cat_id         = $this->getRequest()->getParam('cat_id');
	$good_id        = $this->getRequest()->getParam('good_id');
	$good_color_id  = $this->getRequest()->getParam('good_color_id');
	$sipping_add    = $this->getRequest()->getParam('shipping_address');
	$qty 		    = $this->getRequest()->getParam('num');
	$ids 			= $this->getRequest()->getParam('ids');
	$for_partner    = $this->getRequest()->getParam('for_partner',2);
    $sale_off_percents  = $this->getRequest()->getParam('sale_off_percent');
    // $payment_type   = $this->getRequest()->getParam('payment_type',NULL);
    $payment_order  = $this->getRequest()->getParam('payment_order', 0);
    $lacksurplus    = $this->getRequest()->getParam('lacksurplus', 0);

	$userStorage = Zend_Auth::getInstance()->getStorage()->read();

	$QGood   = new Application_Model_Good();
	$QDistributor = new Application_Model_Distributor();

    $where = $QGood->getAdapter()->quoteInto('id =?',$good_id);
    $goods = $QGood->fetchRow($where);
    
        $d_id           = 34807;
    	$unit_price     = $goods['price_3'];
		$type = 3; //type staff
		$cat_id = 11;

		$cat_ids     = array('0'    => $cat_id,'1'      => 12,);
        $good_ids    = array('0'    => $good_id,'1'     => 127);
        $good_colors = array('0'    => $good_color_id,'1'  => 1);
        $nums        = array('0'    => $qty,'1'         => 1);
        $prices      = array('0'    => $unit_price,'1'  => 0);
        $sale_off_percent = array('0' => $sale_off_percents,'1'    => 100);

        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $d_id);
        $distributor = $QDistributor->fetchRow($where);

        $market_general_id=0;
        $ids = array('0'    => '');
        $tags = array('0'    => $tax_po);

        $warehouse_id   = 133;
        $life_time      = 1;
        $rank           = 3;
        $delivery_fee   = 0;//ค่าจัดส่ง
        $distributor_id = $d_id;
        $salesman       = $userStorage->id;
        $include_shipping_fee=1;
        $user_uncheck   = 0;

        $texts = array('0'    => '');
        $campaign = array('0'    => '');
        $market_general_data = array('shipment_id'    => 0);

        $total_spc_discounts=$total_spc_discount;
        $spc_discount=$distributor['spc_discount'];
        $spc_discount_phone=$distributor['spc_discount_phone'];
        $spc_discount_acc=$distributor['spc_discount_acc'];
        $spc_discount_digital=$distributor['spc_discount_digital'];
        $save_service='sales';
        $creditnote_data='';
        $deposit_data='';
        $customer_name_for_staff = $r_name."-ส่วนลดพนักงานหักเงินเดือน $sale_off_percents%";

         $check_stock = $QGood->get_stock($good_id, $good_color_id, $cat_id,$warehouse_id, $type);
        
        if($check_stock < 0){
            echo "<script>";
                echo "alert(\" The available stock is only 0\");"; 
                echo "window.history.back()";
            echo "</script>";
            return;  
        }
        //----end check_stock---
		$params = array(
	            'ids'                  => $ids,
                'save_service'         => $save_service,
                'cat_id'               => $cat_ids,
                'good_id'              => $good_ids,
                'good_color'           => $good_colors,
                'num'                  => $nums,
                'price'                => $prices,
                'total'                => $totals,
                'text'                 => $texts,
                'distributor_id'       => $distributor_id,
                'warehouse_id'         => $warehouse_id,
                'salesman'             => $salesman,
                'sales_catty_id'       => $sales_catty_id,
                'area_id'              => $area_id,
                'type'                 => $type,
                'sale_off_percent'     => $sale_off_percent,
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
                'payment_date'         => $payment_dates,
                'shipment_id'          => $shipment_id,
                'product_color_key'    => $product_color_key,
                'staff_num'            => $staff_num,
                'for_partner'          => $for_partner,
                'credit_id'            => $credit_id,
                // 'delivery_address'     => $delivery_address, 
                'delivery_fee'         => $delivery_fee,
                'customer_id'          => $customer_id,
                'customer_name'        => $customer_name,
                'customer_phone_number'  => $customer_phone_number,
                'customer_tax_number'  => $customer_tax_number,
                'customer_branch_number'  => $customer_branch_number,
                'customer_tax_address' => $customer_tax_address,
                'rank'                 => $rank,
                'edit'                 => $edit,
                'sipping_add'          => $sipping_add,
                'customer_name_for_staff'        => $customer_name_for_staff,
                'total_spc_discount'   => $total_spc_discount,
                'digital_discount'     => $digital_discount,
                'job_sn'               => $job_sn,
                'job_type'               => $job_type,
                'group_type_id_post'        => $group_type_id_post,
                'member_brandshop_code'        => $member_brandshop_code,
                'bs_campaign'        => $bs_campaign,
                'phone_number'        => $phone_number,
                'swap_imei'         => $swap_imei,
                'old_imei'          => $old_imei,
                'new_imei'          => $new_imei,
                'text_note'         => $text_note,
                'open_market_campaign'         => $open_market_campaign,
                'presales_sn'         => $presales_sn,
	            // 'shipping_address'    => $ship_address,
	            // 'payment_type'		  => $payment_type,
        );

    // print_r($params); die;
    $result = $this->saveAPI($params);
   
    if ($result['code'] == 1) { //success
	    //print_r($result);
	    file_get_contents(HOST."cron/gen-sn-ref");
	    //update discount when created
	    if($edit != 1){
	        $QMarket = new Application_Model_Market();
	        $QDistributor = new Application_Model_Distributor();
	        
	        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
	        $distributor = $QDistributor->fetchRow($where);

	        // Check Special Discount
	        if(isset($warehouse_id) and $warehouse_id == 71){
	            $total_discount_digital = $digital_discount + $distributor['spc_discount'];
	            $spc_discount = $total_discount_digital;
	        }else{
	            $spc_discount = $distributor['spc_discount'];
	        }
	        
	        $spc_discount_phone = $distributor['spc_discount_phone'];
	        $spc_discount_acc = $distributor['spc_discount_acc'];
	        $spc_discount_digital = $distributor['spc_discount_digital'];

	        if(isset($total_discount_digital) and $total_discount_digital > 0){
	            $spc_discount_digital = 1;
	        }
	        
	        $array_data = array('spc_discount' => $spc_discount,
	                        'spc_discount_phone' => $spc_discount_phone,
	                        'spc_discount_acc' => $spc_discount_acc,
	                        'spc_discount_digital' => $spc_discount_digital);

	        $where = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
	        $QMarket->update($array_data, $where);
	    }

	    // save tags
	    
	    $QTag       = new Application_Model_Tag();
	    $QTagObject = new Application_Model_TagObject();

	    // remove old record on tag_object
	    $where = array();
	    $where[] = $QTagObject->getAdapter()->quoteInto('object_id = ?', $result['sn']);
	    $where[] = $QTagObject->getAdapter()->quoteInto('type = ?', TAG_ORDER);

	    $QTagObject->delete($where);

	    if ($tags and isset($result['sn']) and $result['sn']){

	        foreach ($tags as $t){
	            $where = $QTag->getAdapter()->quoteInto('name = ?', $t);
	            $existed_tag = $QTag->fetchRow($where);

	            if ($existed_tag){

	                $tag_id = $existed_tag['id'];

	            } else {

	                $tag_id = $QTag->insert(array('name'=>$t));

	            }

	            $QTagObject->insert(
	                array(
	                    'tag_id'    => $tag_id,
	                    'object_id' => $result['sn'], //order sn
	                    'type'      => TAG_ORDER,
	                )
	            );


	        }
	    }
        //-----------------gen sn-----------
        $db = Zend_Registry::get('db');
        $QMarket = new Application_Model_Market();
        $where_sales = array();
        $where_sales[] = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
        $where_sales[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);
        $sales = $QMarket->fetchAll($where_sales);

        //-------------------------create pay_group------------------------
        $QPGBal = new Application_Model_PayGroupBalance();

            if(isset($sales[0]['pay_group']) and $sales[0]['pay_group'] != 1){
                            // payment no

                $QPG = new Application_Model_PayGroup();
                $where = array();
                $where[] = $QPG->getAdapter()->quoteInto('sale_order = ?', $result['sn']);
                $where[] = $QPG->getAdapter()->quoteInto('status = ?', 1);
                $payment_group_check = $QPG->fetchRow($where);
        
                if ($payment_group_check) {

                    $payment_id = $payment_group_check['payment_id'];

                    $array_data = array(
                        'created_at' => $userStorage->id,
                        'modified_date' => date('Y-m-d H:i:s')
                        );

                    $where = array();
                    $where[] = $QPG->getAdapter()->quoteInto('sale_order = ?',$result['sn']);
                    $where[] = $QPG->getAdapter()->quoteInto('status = ?', 1);

                    $QPG->update($array_data, $where);

                }else{
                    $payment_id = date('YmdHis') . substr(microtime(), 2, 4);
                    $sale_order = $result['sn'];
                    $payment_group = 0;
                    $case_text = null;
                    $money = $sales[0]['total'];
                    // $lacksurplus = 0.00;

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

                    if($lacksurplus > 0){
                        $QPGBal->insert(array(
                                            'payment_id' => $payment_id,
                                            'distributor_id' => $d_id,
                                            'total_amount' => $lacksurplus,
                                            'use_total' => 0,
                                            'balance_total' => $lacksurplus,
                                            'status' => $status,
                                            'create_date' => $created_date,
                                            'create_by' => $userStorage->id,
                                            'update_date' => $modified_date,
                                            'update_by' => $userStorage->id,
                                            'use_status' => 0,
                                            'remark' => null
                                        ));
                    }else{
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
                    }
                }

                if($checkbox_use_paygroup){

                    $QPGT = new Application_Model_PayGroupTran();

                    $i=0;
                    foreach ($use_paygroup as $key) {
                        
                        if($money_use_paygroup[$i] > 0){

                            $QPGT->insert(array(
                                            'payment_id' => $key,
                                            'payment_tran_id' => $payment_id,
                                            'distributor_id' => $d_id,
                                            'use_total' => $money_use_paygroup[$i],
                                            'create_date' => $created_date,
                                            'create_by' => $userStorage->id,
                                            'status' => 1
                                        ));
                        }

                        $i++;
                    }
                }

            }

        //-------------------confirm sales order-------------------------------
            $where = array();
            $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
            $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);
            
                $data = array(
                    'pay_text' => $pay_text,
                    'shipping_text' => $shipping_text,
                    );

                // get another info of distributor
                $where_payment = $QDistributor->getAdapter()->quoteInto('id = ?', $sales[0]['d_id']);
                $distributors_payment = $QDistributor->fetchRow($where_payment);

                $auto_confirm_finance = $distributors_payment->auto_confirm_finance;
                $auto_confirm_finance_warehouse = $distributors_payment->warehouse_id;
                if($auto_confirm_finance_warehouse =='73' || $auto_confirm_finance_warehouse =='62' || $auto_confirm_finance_warehouse =='115')
                {
                  $auto_confirm_finance =1;  
                }
                
                $date = date('Y-m-d H:i:s');
                $checkUpdateCheckMoney = 0;
                $data['payment_type'] = CR;

                    //check money
                    $QCheckmoney = new Application_Model_Checkmoney();
                    $QMarketProduct = new Application_Model_MarketProduct();

                    $select_sn = array();
                    $select_sn[] = $QCheckmoney->getAdapter()->quoteInto('sn = ?', $sales[0]['sn']);
                    $select_sn[] = $QCheckmoney->getAdapter()->quoteInto('type = ?', 2); // phân loại trừ tiền
                    $check_sn_exist = $QCheckmoney->fetchRow($select_sn);


                    if (!$check_sn_exist) {

                        $payment_service_val=0;
                        for($i=0;$i<count($payment_order);$i++){
                            $payment_service_val +=$payment_service[$i];
                        }

                        $sn_total = 0;
                        $intRebate = intval($QMarketProduct->getPrice($sales[0]['sn']));
                        $sn_total = $QMarket->getPrice($sales[0]['sn']) - $intRebate;

                        $note_new='Payment Order='.number_format($sn_total,2).' ค่าอะไหล่และค่าบริการ='.number_format($payment_service_val,2);

                        //data for checkmoney transaction
                        $data_ch = array(
                            'd_id'       => $sales[0]['d_id'],
                            'payment'    => $date,
                            'pay_time'   => $date,
                            'pay_service'     => $payment_service_val,
                            'output'     => $sn_total,
                            'pay_money'  => -$sn_total,
                            'type'       => 2,
                            'sn'         => $sales[0]['sn'],
                            'user_id'    => $userStorage->id,
                            'create_by'  => $userStorage->id,
                            'create_at'  => $date,
                            'note'       => $note_new,
                            'company_id' => 1,
                            'sales_confirm_date'    => $date,
                            'sales_confirm_id'    => $userStorage->id,
                            );

                        if($auto_confirm_finance =='1')
                        {
                            $data_ch['finance_confirm_id'] = $userStorage->id;
                            $data_ch['finance_confirm_date'] = $date;
                        }

                        $checkUpdateCheckMoney = $QCheckmoney->insert($data_ch);

                        // update balance
                        if ($checkUpdateCheckMoney) {
                           // $QStoreaccount->updateBalance($sales[0]['d_id']);
                        } else {
                            exit("Loi khong dong bo!");
                        }

                        // Neu co uy nhiem chi
                            $payment_order_val=0;$payment_bank_transfer_val=0;$pay_service_val=0;$i=0;
                            for($i=0;$i<count($payment_order);$i++){
                                $payment_order_val +=$payment_order[$i];
                                $pay_service_val +=$pay_service[$i];
                                $payment_order_val +=$payment_order[$i];
                            }
                            
                    } //End check sn existed


                if ($shipping) {
                   // $data['shipping_yes_time'] = $date;
                   // $data['shipping_yes_id'] = $userStorage->id;
                }

                $data['confirm_so'] = 1;

                $data['sales_confirm_date'] = $date;
                $data['sales_confirm_id'] = $userStorage->id;
                $data['pay_time'] = $date;


                if($auto_confirm_finance =='1'){
                    $data['pay_time'] = $date;
                    $data['pay_user'] = $userStorage->id;
                    $data['shipping_yes_time'] = $date;
                    $data['shipping_yes_id'] = $userStorage->id;

                    $data['pay_text'] = "Confirm Payment Auto by Finance";
                    $data['finance_confirm_id'] = $userStorage->id;
                    $data['finance_confirm_date'] = $date;

                }
                
                $QMarket->update($data, $where);
    
         //---------end confirm order------------------
        
		$flashMessenger->setNamespace('success')->addMessage($result['message']);
        $url= HOST.'sales'.$result['sn'];

        if($fixAjax == 'AJAX'){
            echo json_encode(['status' => '410', 'message' => $result['message'], 'url' => $url]);
            exit();        
        }
        $this->_redirect(HOST.'sales');

	}else if ($result['code'] == -5) {
            $flashMessenger->setNamespace('error')->addMessage($result['message']);
            $url = HOST.'sales';
            if($fixAjax == 'AJAX'){
                echo json_encode(['status' => '410', 'message' => $result['message'], 'url' => $url]);
                exit();
            }
               $this->_redirect(HOST.'sales'); 
        } else {
            $flashMessenger->setNamespace('error')->addMessage($result['message']);
            $url = HOST.'sales';
            if($fixAjax == 'AJAX'){
                echo json_encode(['status' => '404', 'message' => $result['message'], 'url' => $url]);
                exit();
            }
            $this->_redirect(HOST.'sales');
        }
                     
}// end post
        







