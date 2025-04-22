<?php
$flashMessenger = $this->_helper->flashMessenger;


if ($this->getRequest()->getMethod() == 'POST'){

        //mua máy nhân viên 
        $saleoff_price       = $this->getRequest()->getParam('sale_off_price'); // Add sale off price unit
        $payment_dates      = $this->getRequest()->getParam('payment_date');
        $shipment_id        = $this->getRequest()->getParam('shipment_id'); 
        $id_staffs          = $this->getRequest()->getParam('id_staff');
        $name_staff_ingames = $this->getRequest()->getParam('name_staff_ingame');
        $cmnd_staff_ingames = $this->getRequest()->getParam('cmnd_staff_ingame');
        $shipment_types     = $this->getRequest()->getParam('shipment_type');
        $sophieuthus        = $this->getRequest()->getParam('sophieuthu');
        $sotienthuctes      = $this->getRequest()->getParam('sotienthucte');
        $service_nvmm       = $this->getRequest()->getParam('service_nvmm');
        $warehouse_nvmm     = $this->getRequest()->getParam('warehouse_nvmm');
        $product_color_key  = $this->getRequest()->getParam('product_color_key');
        $staff_num          = $this->getRequest()->getParam('staff_num');
        $for_partner        = $this->getRequest()->getParam('for_partner',2);
        $save_service       = $this->getRequest()->getParam('save_service');
        $save_accessories   = $this->getRequest()->getParam('save_accessories');
        //end mua may nhan vien

        $market_general_id= $this->getRequest()->getParam('market_general_id');
        $ids              = $this->getRequest()->getParam('ids');
        $cat_ids          = $this->getRequest()->getParam('cat_id');
        $good_ids         = $this->getRequest()->getParam('good_id');
        $good_colors      = $this->getRequest()->getParam('good_color');
        $sipping_add      = $this->getRequest()->getParam('sipping_add');
        $digital_discount      = $this->getRequest()->getParam('digital_discount');
        $job_sn             = $this->getRequest()->getParam('job_sn');
        $job_type           = $this->getRequest()->getParam('job_type');
        $split_gifbox       = $this->getRequest()->getParam('split_gifbox');
        $bs_campaign       = $this->getRequest()->getParam('bs_campaign');
        $phone_number       = $this->getRequest()->getParam('phone_number');
        $staff_code       = $this->getRequest()->getParam('staff_code');
        $store_id           = $this->getRequest()->getParam('store_id');
	$finance_client     = $this->getRequest()->getParam('finance_client');

        if($staff_code=="")
        {
            $staff_code="0";
        }

        $presales_sn   = $this->getRequest()->getParam('presales_sn');

        $text_note       = $this->getRequest()->getParam('text_note');

        $open_market_campaign       = $this->getRequest()->getParam('open_market_campaign');

        //print_r($job_sn);die;

        $nums             = $this->getRequest()->getParam('num');

        $total_spc_discount  = $this->getRequest()->getParam('total_spc_discount', 0);

        $fixAjax            = $this->getRequest()->getParam('faxAjax');

        //$prices           = $this->getRequest()->getParam('price');
        //$$total_spc_discount=60000;
        //Sak remove comma and convert type from string to float

        if($save_service=='service'){
            if($good_ids[0]==156 && $nums[0] <=0){
             $nums[0]=1; 
         }
         $tmp_prices = $this->getRequest()->getParam('price');
         $tmp_totals = $this->getRequest()->getParam('total');
     }else{
        $tmp_prices = $this->getRequest()->getParam('price');
        $tmp_totals = $this->getRequest()->getParam('total');
    }

        //print_r($nums);die;
    foreach ($tmp_prices as $key => $value) {
        $prices[] = floatval(str_replace(",","", $value ));
    }

    foreach ($tmp_totals as $key => $value) {
        $totals[] = floatval(str_replace(",","", $value ));
    }

       // print_r($prices);die;

    $texts              = $this->getRequest()->getParam('text');
    $distributor_id     = $this->getRequest()->getParam('distributor_id');
    $warehouse_id       = $this->getRequest()->getParam('warehouse_id');
    $salesman           = $this->getRequest()->getParam('salesman');
    $sales_catty_id     = $this->getRequest()->getParam('salesman_catty');
    $area_id            = $this->getRequest()->getParam('area_id');
    $type               = $this->getRequest()->getParam('type');
    $sale_off_percent   = $this->getRequest()->getParam('sale_off_percent');
    $sn                 = $this->getRequest()->getParam('sn');
    $life_time          = $this->getRequest()->getParam('life_time');
    $rebate_price       = $this->getRequest()->getParam('rebate_price');
    $service_id         = $this->getRequest()->getParam('service_id');
        //bvg
    $ids_bvg            = $this->getRequest()->getParam('ids_bvg');
    $joint              = $this->getRequest()->getParam('joint');
    $good_ids_bvg       = $this->getRequest()->getParam('good_id_bvg');
    $prices_bvg         = $this->getRequest()->getParam('price_bvg');
        //$prices_bvg       = preg_replace('/[^0-9]/i', '',$prices_bvg);
    $nums_bvg           = $this->getRequest()->getParam('num_bvg');
    $totals_bvg         = $this->getRequest()->getParam('total_bvg');
    $bvg_imei           = $this->getRequest()->getParam('ids_bvg_imei');
    $ids_discount       = $this->getRequest()->getParam('ids_discount');
    $joint_discount     = $this->getRequest()->getParam('joint_discount');
    $prices_discount    = $this->getRequest()->getParam('price_discount');
    $campaign           = $this->getRequest()->getParam('campaign'); 
    $for_staff          = $this->getRequest()->getParam('for_staff');
    $edit               = $this->getRequest()->getParam('edit');
    $saleoff_price       = $this->getRequest()->getPost('sale_off_price');

        //////////Hàng bán cho nhân viên thì lưu thong tin lai/////////
    $office             = $this->getRequest()->getParam('oppo_office');
    $company            = $this->getRequest()->getParam('customer_company');
    $customer_name      = $this->getRequest()->getParam('customer_name');
    $customer_address   = $this->getRequest()->getParam('customer_address');
    $tax_code           = $this->getRequest()->getParam('tax_code');
    $invoice            = $this->getRequest()->getParam('invoice');
        ///////////////////////////////////////////////////////////////
    $gift_id            = $this->getRequest()->getParam('gift_id');
        $distributor_po     = $this->getRequest()->getParam('distributor_po'); // PO theo dealer
        // phương thức thanh toán
        $payment_method     = $this->getRequest()->getParam('payment_method');
        $customer_name_for_staff        = $this->getRequest()->getParam('custom_name');
        // = 1 thì tính phí, không thì thôi
        $include_shipping_fee = $this->getRequest()->getParam('include_shipping_fee', 0);
        $user_uncheck         = $this->getRequest()->getParam('user_uncheck', 0);

        $group_type_id_post = $this->getRequest()->getParam('group_type_id');
        $member_brandshop_code = $this->getRequest()->getParam('member_brandshop_code');


        // Tanong Add New --> credit_id
        $credit_id = $this->getRequest()->getParam('credit_id');
        $price_balance_discount_creditnote = $this->getRequest()->getParam('price_balance_discount_creditnote');
        $price_use_discount_creditnote = $this->getRequest()->getParam('price_use_discount_creditnote');
        $ids_discount_creditnote = $this->getRequest()->getParam('ids_discount_creditnote');

        // Tanong Add New --> deposit_id
        $deposit_id = $this->getRequest()->getParam('deposit_id');
        $price_balance_discount_deposit = $this->getRequest()->getParam('price_balance_discount_deposit');
        $price_use_discount_deposit = $this->getRequest()->getParam('price_use_discount_deposit');
        $ids_discount_deposit = $this->getRequest()->getParam('ids_discount_deposit');

        //Tanong Delivery Fee 20160323
        $delivery_fee = trim($this->getRequest()->getParam('delivery_fee'));

        $rank = trim($this->getRequest()->getParam('rank'));
        if($rank==10){
            $customer_id = trim($this->getRequest()->getParam('customer_id'));
            $customer_name = trim($this->getRequest()->getParam('customer_name'));
            $customer_phone_number = trim($this->getRequest()->getParam('customer_phone_number'));
            $customer_tax_number = trim($this->getRequest()->getParam('customer_tax_number'));
            $customer_branch_number = trim($this->getRequest()->getParam('customer_branch_number'));
            $customer_tax_address = trim($this->getRequest()->getParam('customer_tax_address'));
            $customer_zip_code = trim($this->getRequest()->getParam('customer_zip_code'));
        }else{
            $customer_id = '';
            $customer_name = '';
            $customer_phone_number = '';
            $customer_tax_number = '';
            $customer_branch_number = '';
            $customer_tax_address = '';
            $customer_zip_code = '';
        }


        $swap_imei = $this->getRequest()->getParam('swap_imei');
        $old_imei = $this->getRequest()->getParam('old_imei');
        $new_imei = $this->getRequest()->getParam('new_imei');

        $swap_acc = $this->getRequest()->getParam('swap_acc');
        $swap_acc_cat_id = $this->getRequest()->getParam('swap_acc_cat_id');
        $swap_acc_good_id = $this->getRequest()->getParam('swap_acc_good_id');
        $swap_acc_good_color = $this->getRequest()->getParam('swap_acc_good_color');
        $swap_acc_num = 1;

        $buy_return = $this->getRequest()->getParam('buy_return');
        $discount_buy_return = $this->getRequest()->getParam('discount_buy_return');
        $discount_buy_return_true = $this->getRequest()->getParam('discount_buy_return_true');

        $check_money_transfer = $this->getRequest()->getParam('check_money_transfer');

        // check invalid cn

        // $sumtotal_price_use_discount_creditnote = array_sum(str_replace(',','',$_POST['price_use_discount_creditnote']));

        // $sumtotal_price_use_discount_deposit = array_sum(str_replace(',','',$_POST['price_use_discount_deposit']));

        // $sumtotal = array_sum(str_replace(',','',$_POST['total']));

        // if(($sumtotal+($delivery_fee*1.07)) < ($sumtotal_price_use_discount_creditnote+$sumtotal_price_use_discount_deposit)){

        //     if($fixAjax == 'AJAX'){
        //         echo json_encode(['status' => '411', 'message' => 'CN Or Deposit Invalid Please check.', 'url' => '']);
        //         exit();
        //     }

        //     $flashMessenger->setNamespace('error')->addMessage('CN Or Deposit Invalid Please check.');
        //     $this->_redirect(HOST.'sales');
        // }

        // chặn edit đơn của người khác
        $QMarket = new Application_Model_Market();
        if (!empty($sn)) {
            $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $market_check = $QMarket->fetchRow($where);

            if ($market_check) {
                $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                // if ($market_check['user_id'] != $userStorage->id && !My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, SUPER_SALES_ADMIN))) {
                //     $flashMessenger->setNamespace('error')->addMessage('You cannot edit this Order');
                //     $this->_redirect(HOST.'sales');
                // }
            }

            // not allow edit order but have quota

            $QLQT = new Application_Model_LogQuotaTran();
            $whereCheckQuota = $QLQT->getAdapter()->quoteInto('sn = ?', $sn);
            $checkQuotaEdit = $QLQT->fetchRow($whereCheckQuota);

            if($checkQuotaEdit && $market_check['add_time'] < date('Y-m-d')){

                if($fixAjax == 'AJAX'){
                    echo json_encode(['status' => '410', 'message' => 'You cannot edit this order, This order have use quota please contact p\'pui.', 'url' => HOST.'sales']);
                    exit();
                }

                $flashMessenger->setNamespace('error')->addMessage('You cannot edit this order, This order have use quota please contact p\'pui.');
                $this->_redirect(HOST.'sales');

            }

        }

        if($save_service=='service'){
            $id_service = 156;
            $temp_good_array = $this->getRequest()->getParam('good_id');
            $index_temp = array_search($id_service, $temp_good_array);
            if(is_int($index_temp)){
                $prices[$index_temp] = $this->getRequest()->getParam('total')[$index_temp];
            }
        }

//$prices="2500";
       // print_r($prices);die;

        if(($swap_imei or $swap_acc) and $save_service == 'service'){
            unset($tmp_totals[0]);
            unset($cat_ids[0]);
            unset($good_ids[0]);
            unset($good_colors[0]);
            unset($nums[0]);
            unset($tmp_prices[0]);
            unset($texts[0]);
            unset($sale_off_percent[0]);
            unset($ids[0]);
            unset($prices[0]);
            unset($totals[0]);


            $tmp_totals = array_values($tmp_totals);
            $cat_ids = array_values($cat_ids);
            $good_ids = array_values($good_ids);
            $good_colors = array_values($good_colors);
            $nums = array_values($nums);
            $tmp_prices = array_values($tmp_prices);
            $texts = array_values($texts);
            $sale_off_percent = array_values($sale_off_percent);
            $ids = array_values($ids);
            $prices = array_values($prices);
            $totals = array_values($totals);
        }

        // echo '<br>good : ';
        // print_r($good_ids);
        // echo '<br>price : ';
        // print_r($prices);
        // echo '<br>num : ';
        // print_r($nums);
        // echo '<br>total : ';
        // print_r($totals);
        // die;

        if($store_id) {
            $QStore = new Application_Model_Store();
            $where = $QStore->getAdapter()->quoteInto('id =?',$store_id);
            $result = $QStore->fetchRow($where);

            // print_r($result);die;

            if($result){
                $insert_store_id = $store_id;
            }else{
                $insert_store_id = '0';
            }
        }

        $params = array(
            'market_general_id'    => $market_general_id,
            'ids'                  => $ids,
            'save_service'         => $save_service,
            'cat_id'               => $cat_ids,
            'good_id'              => $good_ids,
            'good_color'           => $good_colors,
            'num'                  => $nums,
            'price'                => $prices,
            'total'                => $totals,
            'sale_off_price'        => $saleoff_price,
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
            'customer_zip_code'    => $customer_zip_code,
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
            'swap_acc'          => $swap_acc,
            'text_note'         => $text_note,
            'open_market_campaign'         => $open_market_campaign,
            'presales_sn'         => $presales_sn,
            'staff_code'        => $staff_code,
            'buy_return'        => $buy_return,
            'discount_buy_return'        => $discount_buy_return,
            'discount_buy_return_true'        => $discount_buy_return_true,
            'check_money_transfer' => $check_money_transfer,
            'store_id'          => $insert_store_id,
	    'finance_client'    => $finance_client
        );
       // print_r($params);die;
        //fix remark big c
if($distributor_id == 30344){
    if($params['text_note'] != ''){
        $params['text_note'] = ' | **ประทับตราบิ๊กซีในเอกสารก่อนนำกลับทุกครั้ง**';
    }else{
        $params['text_note'] = '**ประทับตราบิ๊กซีในเอกสารก่อนนำกลับทุกครั้ง**';
    }
}

if($save_accessories=='1'){
    $params['order_accessories'] = 1;
}

if(isset($invoice) and $invoice)
{
   $invoice_data = array(
    'office' => $office,
    'company' => $company,
    'customer_name' => $customer_name,
    'customer_address' => $customer_address,
    'tax_code' => $tax_code,
    'invoice' => $invoice,
    'service_nvmm'  => $service_nvmm,
    'warehouse_nvmm'  => $warehouse_nvmm
);
   $params['invoice_data'] = $invoice_data;        
}

$market_general_data = array(
    'shipment_id'          => intval($shipment_id),
);
$params['market_general_data'] = $market_general_data;

$tmp = Zend_Auth::getInstance()->getIdentity();
$user_info = json_decode(json_encode($tmp), true);
$user_id = 10;

if(isset($ids_discount_creditnote))
{  
    $creditnote_data = array(
        'ids_discount_creditnote' => $ids_discount_creditnote,
        'price_balance_discount_creditnote' => $price_balance_discount_creditnote,
        'price_use_discount_creditnote' => $price_use_discount_creditnote,
        'distributor_id' => $distributor_id,
        'user_id' => $user_id
    );

}else{
 $creditnote_data=null;
}

$params['creditnote_data'] = $creditnote_data;

if(isset($ids_discount_deposit))
{  
    $deposit_data = array(
        'ids_discount_deposit' => $ids_discount_deposit,
        'price_balance_discount_deposit' => $price_balance_discount_deposit,
        'price_use_discount_deposit' => $price_use_discount_deposit,
        'distributor_id' => $distributor_id,
        'user_id' => $user_id
    );

}else{
 $deposit_data=null;
}

$params['deposit_data'] = $deposit_data;

        // Check Over Quota

$where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
$get_market = $QMarket->fetchAll($where);

$count_checkQuota = -1;
foreach ($params['good_id'] as $key) {

    $count_checkQuota++;

    $check_count = 0;
    $current_num_product = 0;
    foreach ($get_market as $key_market => $value_market) {
        if($value_market['good_id'] == $key && $value_market['good_color'] == $params['good_color'][$count_checkQuota]){
            $current_num_product = $value_market['num'];
            $check_count++;
        }
    }

    if($check_count > 0 && $current_num_product >= $params['num'][$count_checkQuota]){
        continue;
    }

    $params_checkQuota = array(
        'distributor_id' => $distributor_id,
        'good_id'        => $key,
        'good_color'     => $params['good_color'][$count_checkQuota],
        'num'            => $params['num'][$count_checkQuota],   
        'rank'           => $rank,   
        'warehouse_id'      => $warehouse_id,   
        'cat_id'         => $params['cat_id'][$count_checkQuota],   
        'sales_sn'       => $sn,   
        'type'           => $type
    );

    $checkQuota = $QMarket->checkQuotaOppo($params_checkQuota);
    if($checkQuota == 1){
            	echo json_encode(['status' => '411', 'message' => 'Over Quota !', 'url' => '']);  // Over Quota
            	exit();
            }
            if($checkQuota == 2){
                echo json_encode(['status' => '411', 'message' => 'ร้านนี้ยังไม่ได้กำหนดประเภทร้านค้า กรุณาอัพเดทข้อมูลร้านค้าด้วย!', 'url' => '']);
                exit();
            }
        }

        // start check block item

        $QGood          = new Application_Model_Good();
        $QGoodHoldAll   = new Application_Model_GoodHoldAll();
        $QGoodHoldDis   = new Application_Model_GoodHoldDis();

        foreach ($params['good_id'] as $key => $value) {

            $bColor = array();

            if ($value) {
                $where      = $QGood->getAdapter()->quoteInto('id = ?', $value);
                $good       = $QGood->fetchRow($where);
                $resultAll  = $QGoodHoldAll->CheckHoldAll($value,$warehouse_id);
                $resultHold = $QGoodHoldDis->CheckHoldDis($value,$distributor_id,$warehouse_id);

                if ($rank==10) {

                }else{
                    if ($resultAll) {


                     if ($resultAll[0]['type_all']==1) {

                        if($fixAjax == 'AJAX'){
                            echo json_encode(['status' => '411', 'message' => 'Product ' . $good['desc'] . ' Model ' . $good['name'] . ' Is Block', 'url' => '']);
                            exit();
                        }

                        $flashMessenger->setNamespace('error')->addMessage('Product ' . $good['desc'] . ' Model ' . $good['name'] . ' Is Block');
                        $this->_redirect(HOST.'sales');
                    }else{
                        for ($i=0; $i < count($resultAll); $i++) { 
                            $bColor[] =$resultAll[$i]['good_color'];
                        }

                    }

                }
                if ($resultHold) {
                    if ($resultHold[0]['type_all']==1) {
                     if($fixAjax == 'AJAX'){
                        echo json_encode(['status' => '411', 'message' => 'Product ' . $good['desc'] . ' Model ' . $good['name'] . ' Is Block', 'url' => '']);
                        exit();
                    }

                    $flashMessenger->setNamespace('error')->addMessage('Product ' . $good['desc'] . ' Model ' . $good['name'] . ' Is Block');
                    $this->_redirect(HOST.'sales');
                }else{
                    for ($i=0; $i < count($resultHold); $i++) { 
                        $bColor[] =$resultHold[$i]['good_color'];
                    }

                }
            }
        }
        if ($good)
        {
            $aColor = array_filter(explode(',', $good->color));
            if ($bColor) {
             $detail = array_delete($bColor, $aColor);

         }else{
            $detail = $aColor;
        }
        if (!$detail)
        {
           if($fixAjax == 'AJAX'){
            echo json_encode(['status' => '411', 'message' => 'Product ' . $good['desc'] . ' Model ' . $good['name'] . ' Is Block', 'url' => '']);
            exit();
        }

        $flashMessenger->setNamespace('error')->addMessage('Product ' . $good['desc'] . ' Model ' . $good['name'] . ' Is Block');
        $this->_redirect(HOST.'sales');
    }
}

}
}
        // end check block item

if($split_gifbox==1){
            //print_r($params);
    $data_chk = array();$data_phone = array();$data_acc = array();
    $index_phone = array();$index_acc = array();

    $data_phone= $params;
    unset($data_phone['ids']);
    unset($data_phone['cat_id']);
    unset($data_phone['good_id']);
    unset($data_phone['good_color']);
    unset($data_phone['num']);
    unset($data_phone['price']);
    unset($data_phone['total']);
    unset($data_phone['text']);
    unset($data_phone['sale_off_percent']);
    unset($data_phone['campaign']);

    $data_acc= $params;
    unset($data_acc['ids']);
    unset($data_acc['cat_id']);
    unset($data_acc['good_id']);
    unset($data_acc['good_color']);
    unset($data_acc['num']);
    unset($data_acc['price']);
    unset($data_acc['total']);
    unset($data_acc['text']);
    unset($data_acc['sale_off_percent']);
    unset($data_acc['campaign']);
    unset($data_acc['total_spc_discount']);


            // check cat_id
    foreach($params['cat_id'] as $index=>$val){ 
                if($val==11){ //phone
                  $index_phone[]= $index;
                }else if($val==12){ // acc
                  $index_acc[]= $index;
              }
          }

          foreach($params as $index=>$val){    
            foreach($index_phone as $index_item=>$vals_item){
                switch ($index) 
                {
                    case "ids":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_phone['ids'][] = $arr_item;
                    break;    
                    case "cat_id":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_phone['cat_id'][] = $arr_item;
                    break;
                    case "good_id":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_phone['good_id'][] = $arr_item;
                    break;
                    case "good_color":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_phone['good_color'][] = $arr_item;
                    break;
                    case "num":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_phone['num'][] = $arr_item;
                    break;
                    case "price":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_phone['price'][] = $arr_item;
                    break;
                    case "total":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_phone['total'][] = $arr_item;
                    break;
                    case "text":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_phone['text'][] = $arr_item;
                    break;
                    case "sale_off_percent":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_phone['sale_off_percent'][] = $arr_item;
                    break;                
                    case "campaign":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_phone['campaign'][] = $arr_item;
                    break;   
                }
            }

            foreach($index_acc as $index_item=>$vals_item){
                switch ($index) 
                {
                    case "ids":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_acc['ids'][] = $arr_item;
                    break;    
                    case "cat_id":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_acc['cat_id'][] = $arr_item;
                    break;
                    case "good_id":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_acc['good_id'][] = $arr_item;
                    break;
                    case "good_color":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_acc['good_color'][] = $arr_item;
                    break;
                    case "num":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_acc['num'][] = $arr_item;
                    break;
                    case "price":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_acc['price'][] = $arr_item;
                    break;
                    case "total":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_acc['total'][] = $arr_item;
                    break;
                    case "text":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_acc['text'][] = $arr_item;
                    break;
                    case "sale_off_percent":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_acc['sale_off_percent'][] = $arr_item;
                    break;                
                    case "campaign":
                    $arr_item = array();
                    $arr_item=$val[$vals_item];
                    $data_acc['campaign'][] = $arr_item;
                    break;   
                }
            }
        }

            // print_r($data_phone);die;
        $result = $this->saveAPI($data_phone); 
            // print_r($result);die;
        if ($result['code'] == 1) {

            $data_acc['total_spc_discount']=0;
                // print_r($data_acc);die;
            $result_acc = $this->saveAPI($data_acc); 
        }

            if ($result['code'] == 1) { //success

                $QLQT = new Application_Model_LogQuotaTran();
                $del_where_qt = $QLQT->getAdapter()->quoteInto('sn = ?', $result['sn']);
                $QLQT->update(['num' => 100],$del_where_qt);

                //recheck order
                if($fixAjax == 'AJAX'){
                    if($edit == 1){//edit

                    }else{//create
                        $QCS = new Application_Model_CronSo();
                        $QLQT = new Application_Model_LogQuotaTran();
                        $QMarket = new Application_Model_Market();
                        $QCNT = new Application_Model_CreditNoteCreditTr();

                        $QGood = new Application_Model_Good();
                        $goodsCached = $QGood->get_cache();
                        $QGoodColor = new Application_Model_GoodColor();
                        $goodColorsCached = $QGoodColor->get_cache();
                        $QWarehouse     = new Application_Model_Warehouse();
                        $warehouseCached = $QWarehouse->get_cache();

                        $where = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                        $get_market_for_check = $QMarket->fetchAll($where);

                        foreach ($get_market_for_check as $value) {
                            $params_checkQuota = array(
                                'distributor_id' => $distributor_id,
                                'good_id'        => $value['good_id'],
                                'good_color'     => $value['good_color'],
                                'num'            => $value['num'],   
                                'rank'           => $rank,   
                                'warehouse_id'   => $warehouse_id,   
                                'cat_id'         => $value['cat_id'],   
                                'sales_sn'       => $sn,   
                                'type'           => $type
                            );

                            $checkQuota = $QMarket->checkQuotaOppoReturnData($params_checkQuota);
                            if($checkQuota == -2){

                                $del_where_mk = $QMarket->getAdapter()->quoteInto('sn in (?)', [$result['sn'],$result_acc['sn']]);
                                $QMarket->delete($del_where_mk);

                                $del_where_qt = $QLQT->getAdapter()->quoteInto('sn in (?)', [$result['sn'],$result_acc['sn']]);
                                $QLQT->delete($del_where_qt);

                                $del_where_cnt = $QCNT->getAdapter()->quoteInto('sales_order in (?)', [$result['sn'],$result_acc['sn']]);
                                $QCNT->delete($del_where_cnt);

                                $update_where_cs = $QCS->getAdapter()->quoteInto('sn in (?)', [$result['sn'],$result_acc['sn']]);
                                $QCS->update(['status' => 2],$update_where_cs);

                                $array_url = array('phone' => ['status' => '411', 'message' => 'ร้านนี้ยังไม่ได้กำหนดประเภทร้านค้า กรุณาอัพเดทข้อมูลร้านค้าด้วย หลัง Submit!', 'url' => '']);
                                echo $data_array = json_encode($array_url);
                                exit();
                            }

                            if($checkQuota < 0){

                                $del_where_mk = $QMarket->getAdapter()->quoteInto('sn in (?)', [$result['sn'],$result_acc['sn']]);
                                $QMarket->delete($del_where_mk);

                                $del_where_qt = $QLQT->getAdapter()->quoteInto('sn in (?)', [$result['sn'],$result_acc['sn']]);
                                $QLQT->delete($del_where_qt);

                                $del_where_cnt = $QCNT->getAdapter()->quoteInto('sales_order in (?)', [$result['sn'],$result_acc['sn']]);
                                $QCNT->delete($del_where_cnt);

                                $update_where_cs = $QCS->getAdapter()->quoteInto('sn in (?)', [$result['sn'],$result_acc['sn']]);
                                $QCS->update(['status' => 2],$update_where_cs);

                                // Over Quota
                                $array_url = array('phone' => ['status' => '411', 'message' => 'Over Quota Submit!' . $goodsCached[$value['good_id']] . '['.$goodColorsCached[$value['good_color']].']' . '[' . $warehouseCached[$warehouse_id] . ']', 'url' => '']);
                                echo $data_array = json_encode($array_url);
                                exit();
                            }

                            //check_stock
                            $check_stock = $QGood->get_stock($value['good_id'], $value['good_color'], $value['cat_id'],
                                $warehouse_id, $type);
                            
                            if($check_stock < 0){
                                $del_where_mk = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QMarket->delete($del_where_mk);

                                $del_where_qt = $QLQT->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QLQT->delete($del_where_qt);

                                $del_where_cnt = $QCNT->getAdapter()->quoteInto('sales_order = ?', $result['sn']);
                                $QCNT->delete($del_where_cnt);

                                $update_where_cs = $QCS->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QCS->update(['status' => 2],$update_where_cs);

                                echo json_encode(['status' => '411', 'message' => 'Over Stock Submit! : ' . $goodsCached[$value['good_id']] . '['.$goodColorsCached[$value['good_color']].']' . '[' . $warehouseCached[$warehouse_id] . '] : ' . $check_stock, 'url' => '']);
                                exit();
                            }
                        }

                        if (isset($get_market_for_check[0]['shipping_address']) && $get_market_for_check[0]['shipping_address']){

                            $temp_check_address_d_id = '';
                            $temp_check_address_id = '';

                            if (isset($get_market_for_check[0]['d_id']) && $get_market_for_check[0]['d_id']) {
                                $temp_check_address_d_id = $get_market_for_check[0]['d_id'];
                            }

                            if (isset($get_market_for_check[0]['shipping_address']) && $get_market_for_check[0]['shipping_address']){
                                $temp_check_address_id = $get_market_for_check[0]['shipping_address'];
                            }

                            $QSA = new Application_Model_ShippingAddress();
                            $get_check_address = $QSA->checkAddressOnOrder($temp_check_address_d_id,$temp_check_address_id);
                            if(!$get_check_address){

                                $del_where_mk = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QMarket->delete($del_where_mk);

                                $del_where_qt = $QLQT->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QLQT->delete($del_where_qt);

                                $del_where_cnt = $QCNT->getAdapter()->quoteInto('sales_order = ?', $result['sn']);
                                $QCNT->delete($del_where_cnt);

                                $update_where_cs = $QCS->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QCS->update(['status' => 2],$update_where_cs);

                                echo json_encode(['status' => '411', 'message' => 'Wrong Address,Please check.', 'url' => '']);
                                exit();
                            }
                        }
                        
                    }

                }

                //run cron gen sn_ref
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
                $tags        = $this->getRequest()->getParam('tags');

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

                $flashMessenger->setNamespace('success')->addMessage($result['message']);

                $url= HOST."sales/print-sale?sn=".$result['sn'];

                if($fixAjax == 'AJAX'){
                    //echo json_encode(['status' => '410', 'message' => $result['message'], 'url' => $url]);
                    $array_phone = array('status' => '410', 'message' => $result['message'], 'url' => $url);
                    //exit();        
                }
                //print_r($array_phone);
                //$this->_redirect($url);
                //header('Location: /'.$url);
            }else if ($result['code'] == -5)
            {
                $flashMessenger->setNamespace('error')->addMessage($result['message']);
                $url = HOST.'sales';
                if($fixAjax == 'AJAX'){
                    //echo json_encode(['status' => '410', 'message' => $result['message'], 'url' => HOST.'sales']);
                    $array_phone = array('status' => '411', 'message' => $result['message'], 'url' => '');
                    //exit();
                }
               // $this->_redirect( HOST.'sales' );
                
            } else {
                $flashMessenger->setNamespace('error')->addMessage($result['message']);
                $url = HOST.'sales';
                if($fixAjax == 'AJAX'){
                    //echo json_encode(['status' => '404', 'message' => $result['message'], 'url' => HOST.'sales']);
                    $array_phone = array('status' => '404', 'message' => $result['message'], 'url' => '');
                    //exit();
                }

               // $this->_redirect( HOST.'sales' );
                
            }

            if ($result_acc['code'] == 1) { //success
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

                    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $result_acc['sn']);
                    $QMarket->update($array_data, $where);
                }

                // save tags
                $tags        = $this->getRequest()->getParam('tags');

                $QTag       = new Application_Model_Tag();
                $QTagObject = new Application_Model_TagObject();

                // remove old record on tag_object
                $where = array();
                $where[] = $QTagObject->getAdapter()->quoteInto('object_id = ?', $result_acc['sn']);
                $where[] = $QTagObject->getAdapter()->quoteInto('type = ?', TAG_ORDER);

                $QTagObject->delete($where);

                if ($tags and isset($result_acc['sn']) and $result_acc['sn']){

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
                                'object_id' => $result_acc['sn'], //order sn
                                'type'      => TAG_ORDER,
                            )
                        );
                    }
                }

                $flashMessenger->setNamespace('success')->addMessage($result_acc['message']);

                $url_acc= HOST."sales/print-sale?sn=".$result_acc['sn'];

                if($fixAjax == 'AJAX'){
                    //echo json_encode(['status' => '410', 'message' => $result_acc['message'], 'url' => $url]);
                    $array_acc = array('status' => '410', 'message' => $result_acc['message'], 'url_acc' => $url_acc);
                    //exit();        
                }
                //$this->_redirect($url);
            }else if ($result_acc['code'] == -5)
            {
                $flashMessenger->setNamespace('error')->addMessage($result_acc['message']);
                $url_acc = HOST.'sales';
                if($fixAjax == 'AJAX'){
                    //echo json_encode(['status' => '410', 'message' => $result_acc['message'], 'url' => HOST.'sales']);
                    $array_acc = array('status' => '411', 'message' => $result_acc['message'], 'url_acc' => '');
                    //exit();
                }
                //$this->_redirect( HOST.'sales' );
                
            } else {
                $flashMessenger->setNamespace('error')->addMessage($result_acc['message']);
                $url_acc = HOST.'sales';
                if($fixAjax == 'AJAX'){
                    //echo json_encode(['status' => '404', 'message' => $result_acc['message'], 'url' => HOST.'sales']);
                    $array_acc = array('status' => '404', 'message' => $result_acc['message'], 'url_acc' => '');
                    //exit();
                }

               // $this->_redirect( HOST.'sales' );
                
            }

            if($result['code']==1 && $result_acc['code']==1){
                $currentTime          = date('Y-m-d H:i:s');
                $QMarketSplitOrder = new Application_Model_MarketSplitOrder();

                $array_sn_phone = array('sales_order'=> $result['sn'],'sales_order_split' => $result_acc['sn'],'create_date'=> $currentTime);
                $array_sn_acc = array('sales_order'=> $result_acc['sn'],'sales_order_split' => $result['sn'],'create_date'=> $currentTime);

                //print_r($array_sn);die;
                $QMarketSplitOrder->insert($array_sn_phone);
                $QMarketSplitOrder->insert($array_sn_acc);
            }else{
                $array_phone = array('status' => '404', 'message' => 'Cannot Create Sales Order For Accessory', 'url' => $url);
            }

            $array_url = array('phone' => $array_phone,'acc' => $array_acc);
            echo $data_array = json_encode($array_url);
            exit();

        }else{

         // Save Data Normal Case
            // print_r($params);die;
            $result = $this->saveAPI($params);  
            //print_r($result);die; 
            if ($result['code'] == 1) { //success

                if($save_service=='service')
                {

                    $QMarket = new Application_Model_Market();

                    //$select_inv = "select m.invoice_number,m.invoice_time from market m where m.sn='".$result['sn']."' and m.invoice_number is not null group by m.invoice_number";
                    //print_r($select_inv);die; 
                    //$inv =  $QMarket->fetchAll($select_inv);

                    $where = array();
                    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                    $where[] = $QMarket->getAdapter()->quoteInto('invoice_number is not null', null);
                    $inv =  $QMarket->fetchRow($where);
                    $invoice_number_update=null;$invoice_time_update=null;

                    //print_r($inv['invoice_number']);die;
                    if($inv['invoice_number'] != '')
                    {
                        $invoice_number_update=$inv['invoice_number'];
                        $invoice_time_update=$inv['invoice_time'];
                        
                        $array_inv = array('invoice_number' => $invoice_number_update,
                            'invoice_time' => $invoice_time_update);
                        //print_r($array_inv);die;
                        $where = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                        $QMarket->update($array_inv, $where);
                    }


                    // swap acc
                    if(isset($swap_acc) and $swap_acc){

                        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                        $QLSA = new Application_Model_LogSwapAcc();

                        $new_cat_id = $params['cat_id'][0];
                        $new_good_id = $params['good_id'][0];
                        $new_good_color_id = $params['good_color'][0];
                        $new_num = $params['num'][0];

                        $arr_log_swap_acc = array(
                            'sn' => $result['sn'],
                            'old_cat_id' => $swap_acc_cat_id,
                            'old_good_id' => $swap_acc_good_id,
                            'old_good_color_id' => $swap_acc_good_color,
                            'old_num' => $swap_acc_num,
                            'new_cat_id' => $new_cat_id,
                            'new_good_id' => $new_good_id,
                            'new_good_color_id' => $new_good_color_id,
                            'new_num' => $new_num,
                            'created_date' => date('Y-m-d H:i:s'),
                            'created_by' => $userStorage->id
                        );
                        $status_log_swap_acc = $QLSA->insert($arr_log_swap_acc);

                        $isbatch = $params['isbatch'];

                        $sn_return = date('YmdHis') . substr(microtime(), 2, 4);

                        $sn_ref_return = $this->getReturnOrderNo_Ref($sn_return);

                        if($store_id) {
                            $QStore = new Application_Model_Store();
                            $result = $QStore->find($store_id);

                            if($result){
                                $insert_store_id = $store_id;
                            }else{
                                $insert_store_id = '0';
                            }
                        }


                        //insert ro
                        $data = array(
                            'cat_id'                => $swap_acc_cat_id,
                            'good_id'               => $swap_acc_good_id,
                            'good_color'            => $swap_acc_good_color,
                            'num'                   => $swap_acc_num,
                            'price'                 => 0,
                            'total'                 => 0,
                            'text'                  => 'System : service swap accessories',
                            'price_clas'            => $rank,
                            'd_id'                  => $distributor_id,
                            'isbatch'               => $isbatch,
                            'isbacks'               => 1,
                            'backs_d_id'            => $warehouse_id,
                            'warehouse_id'          => $warehouse_id,
                            'create_cn'             => 0,
                            'active_cn'             => 0,
                            'return_type'           => 1,
                            'add_time'              => date('Y-m-d H:i:s'),
                            'user_id'               => $userStorage->id,
                            'sn'                    => $sn_return,
                            'sn_ref'                => $sn_ref_return,
                            'shipping_yes_time'     => date('Y-m-d H:i:s'),
                            'pay_time'              => date('Y-m-d H:i:s'),
                            'shipping_yes_id'       => $userStorage->id,
                            'pay_user'              => $userStorage->id,
                            'pay_text'              => 'Approve',
                            'finance_confirm_date'  => date('Y-m-d H:i:s'),
                            'finance_confirm_id'    => $userStorage->id,
                            'store_id'              => $insert_store_id,
			    'finance_client'        => $finance_client
                        );

                        // print_r($data); die()

                        $QMarket->insert($data);

                        //insert into product return
                        $QProductReturn = new Application_Model_ProductReturn();
                        $data = array(
                            'return_sn'     => $sn_return,
                            'cat_id'        => $swap_acc_cat_id,
                            'good_id'       => $swap_acc_good_id,
                            'good_color'    => $swap_acc_good_color,
                            'quantity'      => $swap_acc_num,
                            'warehouse_id'  => $warehouse_id,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'created_by'    => $userStorage->id,
                        );
                        $QProductReturn->insert($data);

                        $QWP = new Application_Model_WarehouseProduct();

                        $where_wp = array();
                        $where_wp[] = $QWP->getAdapter()->quoteInto('cat_id = ?', $swap_acc_cat_id);
                        $where_wp[] = $QWP->getAdapter()->quoteInto('good_id = ?', $swap_acc_good_id);
                        $where_wp[] = $QWP->getAdapter()->quoteInto('good_color = ?', $swap_acc_good_color);
                        $where_wp[] = $QWP->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                        $get_wp = $QWP->fetchRow($where_wp);

                        if($get_wp){
                            $where_wp_update = array();
                            $where_wp_update[] = $QWP->getAdapter()->quoteInto('cat_id = ?', $swap_acc_cat_id);
                            $where_wp_update[] = $QWP->getAdapter()->quoteInto('good_id = ?', $swap_acc_good_id);
                            $where_wp_update[] = $QWP->getAdapter()->quoteInto('good_color = ?', $swap_acc_good_color);
                            $where_wp_update[] = $QWP->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);

                            $QWP->update(['quantity' => new Zend_Db_Expr('(quantity+'.$swap_acc_num.')')],$where_wp_update);

                        }else{
                            $data_wp = array(
                                'good_id' => $swap_acc_good_id,
                                'good_color' => $swap_acc_good_color,
                                'cat_id' => $swap_acc_cat_id,
                                'quantity' => $swap_acc_num,
                                'damage_quantity' => 0,
                                'warehouse_id' => $warehouse_id
                            );
                            $QWP->insert($data_wp);
                        }

                    }
                }
                
                // TEST OVERQUOTA
                // $QLQT = new Application_Model_LogQuotaTran();
                // $where_qt = $QLQT->getAdapter()->quoteInto('sn = ?', $result['sn']);
                // $QLQT->update(['num' => 5],$where_qt);

                // TEST OVER STOCK
                // $QMarket = new Application_Model_Market();
                // $where_mk = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                // $QMarket->update(['num' => 1000],$where_mk);

                //recheck order
                if($fixAjax == 'AJAX'){
                    if($edit == 1){//edit

                    }else{//create
                        $QGood = new Application_Model_Good();
                        $QCS = new Application_Model_CronSo();
                        $QLQT = new Application_Model_LogQuotaTran();
                        $QMarket = new Application_Model_Market();
                        $QCNT = new Application_Model_CreditNoteCreditTr();

                        $QGood = new Application_Model_Good();
                        $goodsCached = $QGood->get_cache();
                        $QGoodColor = new Application_Model_GoodColor();
                        $goodColorsCached = $QGoodColor->get_cache();
                        $QWarehouse     = new Application_Model_Warehouse();
                        $warehouseCached = $QWarehouse->get_cache();

                        $where = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                        $get_market_for_check = $QMarket->fetchAll($where);

                        foreach ($get_market_for_check as $value) {
                            $params_checkQuota = array(
                                'distributor_id' => $distributor_id,
                                'good_id'        => $value['good_id'],
                                'good_color'     => $value['good_color'],
                                'num'            => $value['num'],   
                                'rank'           => $rank,   
                                'warehouse_id'   => $warehouse_id,   
                                'cat_id'         => $value['cat_id'],   
                                'sales_sn'       => $sn,   
                                'type'           => $type
                            );

                            $checkQuota = $QMarket->checkQuotaOppoReturnData($params_checkQuota);
                            
                            if($checkQuota == -2){

                                $del_where_mk = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QMarket->delete($del_where_mk);

                                $del_where_qt = $QLQT->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QLQT->delete($del_where_qt);

                                $del_where_cnt = $QCNT->getAdapter()->quoteInto('sales_order = ?', $result['sn']);
                                $QCNT->delete($del_where_cnt);

                                $update_where_cs = $QCS->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QCS->update(['status' => 2],$update_where_cs);

                                echo json_encode(['status' => '411', 'message' => 'ร้านนี้ยังไม่ได้กำหนดประเภทร้านค้า กรุณาอัพเดทข้อมูลร้านค้าด้วย หลัง Submit!', 'url' => '']);
                                exit();
                            }

                            if($checkQuota < 0){

                                $del_where_mk = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QMarket->delete($del_where_mk);

                                $del_where_qt = $QLQT->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QLQT->delete($del_where_qt);

                                $del_where_cnt = $QCNT->getAdapter()->quoteInto('sales_order = ?', $result['sn']);
                                $QCNT->delete($del_where_cnt);

                                $update_where_cs = $QCS->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QCS->update(['status' => 2],$update_where_cs);

                                echo json_encode(['status' => '411', 'message' => 'Over Quota Submit! : ' . $goodsCached[$value['good_id']] . '['.$goodColorsCached[$value['good_color']].']' . '[' . $warehouseCached[$warehouse_id] . ']', 'url' => '']);  // Over Quota
                                exit();
                            }

                            //check_stock
                            $check_stock = $QGood->get_stock($value['good_id'], $value['good_color'], $value['cat_id'],
                                $warehouse_id, $type);
                            
                            if($check_stock < 0){
                                $del_where_mk = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QMarket->delete($del_where_mk);

                                $del_where_qt = $QLQT->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QLQT->delete($del_where_qt);

                                $del_where_cnt = $QCNT->getAdapter()->quoteInto('sales_order = ?', $result['sn']);
                                $QCNT->delete($del_where_cnt);

                                $update_where_cs = $QCS->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QCS->update(['status' => 2],$update_where_cs);

                                echo json_encode(['status' => '411', 'message' => 'Over Stock Submit! : ' . $goodsCached[$value['good_id']] . '['.$goodColorsCached[$value['good_color']].']' . '[' . $warehouseCached[$warehouse_id] . '] : ' . $check_stock, 'url' => '']);
                                exit();
                            }
                        }

                        if (isset($get_market_for_check[0]['shipping_address']) && $get_market_for_check[0]['shipping_address']){

                            $temp_check_address_d_id = '';
                            $temp_check_address_id = '';

                            if (isset($get_market_for_check[0]['d_id']) && $get_market_for_check[0]['d_id']) {
                                $temp_check_address_d_id = $get_market_for_check[0]['d_id'];
                            }

                            if (isset($get_market_for_check[0]['shipping_address']) && $get_market_for_check[0]['shipping_address']){
                                $temp_check_address_id = $get_market_for_check[0]['shipping_address'];
                            }

                            $QSA = new Application_Model_ShippingAddress();
                            $get_check_address = $QSA->checkAddressOnOrder($temp_check_address_d_id,$temp_check_address_id);
                            if(!$get_check_address){

                                $del_where_mk = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QMarket->delete($del_where_mk);

                                $del_where_qt = $QLQT->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QLQT->delete($del_where_qt);

                                $del_where_cnt = $QCNT->getAdapter()->quoteInto('sales_order = ?', $result['sn']);
                                $QCNT->delete($del_where_cnt);

                                $update_where_cs = $QCS->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QCS->update(['status' => 2],$update_where_cs);

                                echo json_encode(['status' => '411', 'message' => 'Wrong Address,Please check.', 'url' => '']);
                                exit();
                            }
                        }

                    }

                }

                //run cron gen sn_ref
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
                $tags        = $this->getRequest()->getParam('tags');

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

                $flashMessenger->setNamespace('success')->addMessage($result['message']);
                
                if($save_accessories=='1'){
                    if($fixAjax == 'AJAX'){
                        echo json_encode(['status' => '410', 'message' => $result['message'], 'url' => HOST.'sales/index-accessories?view_accessories=all']);
                        exit();
                    }
                    $this->_redirect( HOST.'sales/index-accessories?view_accessories=all' );
                }else{

                    $url= HOST."sales/print-sale?sn=".$result['sn'];

                    if($fixAjax == 'AJAX'){
                        echo json_encode(['status' => '410', 'message' => $result['message'], 'url' => $url]);
                        exit();        
                    }
                    $this->_redirect($url);

                }

                $this->_redirect( HOST.'sales' );

            }else if ($result['code'] == -5)
            {
                $flashMessenger->setNamespace('error')->addMessage($result['message']);
                if($save_accessories=='1'){
                    if($fixAjax == 'AJAX'){
                        echo json_encode(['status' => '411', 'message' => $result['message'], 'url' => '']);
                        exit();
                    }

                    $this->_redirect( HOST.'sales/index-accessories?view_accessories=all' );
                }else{
                    if($fixAjax == 'AJAX'){
                        echo json_encode(['status' => '411', 'message' => $result['message'], 'url' => '']);
                        exit();
                    }
                    $this->_redirect( HOST.'sales' );
                    $url = HOST.'sales';
                }
                
                //$this->_redirect( HOST.'sales/create' );
            } else {
                $flashMessenger->setNamespace('error')->addMessage($result['message']);
                if($save_accessories=='1'){

                    if($fixAjax == 'AJAX'){
                        echo json_encode(['status' => '404', 'message' => $result['message'], 'url' => '']);
                        exit();
                    }
                    $this->_redirect( HOST.'sales/index-accessories?view_accessories=all' );
                }else{

                    if($fixAjax == 'AJAX'){
                        echo json_encode(['status' => '404', 'message' => $result['message'], 'url' => '']);
                        exit();
                    }

                    $this->_redirect( HOST.'sales' );
                    $url = HOST.'sales';
                }

            }

            $array_url = array('url' => $url,'url_acc' => $url_acc);
            $this->_redirect($array_url);
        }

    }
    
    //$this->_redirect( HOST.'sales' );


    function array_delete($del_val, $array) {
        if(is_array($del_val)) {
           foreach ($del_val as $del_key => $del_value) {
            foreach ($array as $key => $value){
                if ($value == $del_value) {
                    unset($array[$key]);
                }
            }
        }
    } else {
        foreach ($array as $key => $value){
            if ($value == $del_val) {
                unset($array[$key]);
            }
        }
    }
    return array_values($array);
}



