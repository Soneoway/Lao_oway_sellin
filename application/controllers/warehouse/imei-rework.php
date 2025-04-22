<?php

    $flashMessenger = $this->_helper->flashMessenger;
    
    $messages           = $flashMessenger->setNamespace('error')->getMessages();
    $messages_success   = $flashMessenger->setNamespace('success')->getMessages();

    if ($this->getRequest()->getMethod() == 'POST') {

        $old_imei = $this->getRequest()->getParam('old_imei');
        $new_imei = $this->getRequest()->getParam('new_imei');
        $good_color_id = $this->getRequest()->getParam('good_color_id');

        if(!$old_imei){
            echo json_encode(['status' => 400,'message' => 'Invalid Data! : Please input old imei']);
            exit();
        }

        if(!$new_imei){
            echo json_encode(['status' => 400,'message' => 'Invalid Data! : Please input new imei']);
            exit();
        }

        if(!$good_color_id){
            echo json_encode(['status' => 400,'message' => 'Invalid Data! : Please select new imei color']);
            exit();
        }

        $old_imei = explode("\n", $old_imei);
        $new_imei = explode("\n", $new_imei);

        $old_imei = array_values(array_unique($old_imei));
        $new_imei = array_values(array_unique($new_imei));

        if(count($old_imei) != count($new_imei)){
            echo json_encode(['status' => 400,'message' => 'Invalid Data! : Qty old imei not match new imei']);
            exit();
        }

        $imei_list = array_merge($old_imei,$new_imei);
        // check lock imei
        $QImeiLock = new Application_Model_ImeiLock();
        $getImeiLock = $QImeiLock->checkImeiLock($imei_list);
        $listImeiLock = '';
        foreach ($getImeiLock as $key => $value) {
            if($key == 0){
                $listImeiLock = $value['imei_log'];
            }else{
                $listImeiLock .= '<br>'. $value['imei_log'];
            }
        }
        if($listImeiLock){
            // exit('<div class="alert alert-danger">IMEI Locked<br>' . $listImeiLock .
            //     '<br></div>');

            echo json_encode(['status' => 400,'message' => 'IMEI Locked<br>' . $listImeiLock . '<br>']);
            exit();
        }

        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        try{
            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $QWarehouse = new Application_Model_Warehouse();
            $QChangeSalesProduct = new Application_Model_ChangeSalesProduct();
            $QChangeSalesImei = new Application_Model_ChangeSalesImei();
            $QLog = new Application_Model_Log();
            $QI = new Application_Model_Imei();
            $QChangeSalesOrder = new Application_Model_ChangeSalesOrder();
            $QGood = new Application_Model_Good();

            // check make sure 1 model
            $check1model = $QI->checkImeiInSystem($old_imei,true);

            if(count($check1model) > 1){

                $text = '';
                foreach ($check1model as $key) {

                    switch ($key['type']) {
                        case '1':
                            $type = 'Normal';
                            break;
                        case '2':
                            $type = 'Demo';
                            break;
                        case '5':
                            $type = 'APK';
                            break;
                        
                        default:
                            $type = '-';
                            break;
                    }

                    if($text == ''){
                        $text = PHP_EOL . $key['good_model'] . '[' .$key['good_name']. '] / ' . $key['color_name'] . ' - ' . $type;
                    }else{
                        $text = $text . PHP_EOL . $key['good_model'] . '[' .$key['good_name']. '] / ' . $key['color_name'] . ' - ' . $type;
                    }
                }
                echo json_encode(['status' => 401,'message' => 'Many model' . $text]);
                exit();

            }else if(count($check1model) < 1){
                echo json_encode(['status' => 400,'message' => 'Not find old imei']);
                exit();
            }

            // check imei on changing
            $check_imei_on_changing = $QChangeSalesImei->getImeiChangeSalesImei($old_imei);

            if($check_imei_on_changing){
                $text = '';

                foreach ($check_imei_on_changing as $key) {
                    if($text == ''){
                        $text = PHP_EOL . $key['imei_sn'];
                    }else{
                        $text = $text . PHP_EOL . $key['imei_sn'];
                    }
                }

                echo json_encode(['status' => 401,'message' => 'Invalid imei on changing' . $text]);
                exit();
            }
            
            // check imei to not have on system
            $check_nothave_imei = $QI->checkImeiInSystem($new_imei);

            if($check_nothave_imei){

                $text = '';

                foreach ($check_nothave_imei as $key) {
                    if($text == ''){
                        $text = PHP_EOL . $key['imei_sn'];
                    }else{
                        $text = $text . PHP_EOL . $key['imei_sn'];
                    }
                }

                echo json_encode(['status' => 401,'message' => 'Invalid imei for create po have on system' . $text]);
                exit();
            }

            // check imei in warehouse WM04 - คลังโทรศัพท์เคลมโรงงาน
            // $check_imei_in_warehouse = $QI->checkImeiInWarehouse($old_imei,6);

            // check imei in warehouse China Factory Warehouse
            // $check_imei_in_warehouse = $QI->checkImeiInWarehouse($old_imei,140);

            // check imei in warehouse WMFT - China Warranty Factory
            $check_imei_in_warehouse = $QI->checkImeiInWarehouse($old_imei,130);

            if(!$check_imei_in_warehouse){

                $text = '';

                foreach ($old_imei as $key) {
                
                    if($text == ''){
                        $text = PHP_EOL . $key;
                    }else{
                        $text = $text . PHP_EOL . $key;
                    }
                }

                // echo json_encode(['status' => 401,'message' => 'Not find imei in warehouse [WM04 - คลังโทรศัพท์เคลมโรงงาน]' . $text]);
                // echo json_encode(['status' => 401,'message' => 'Not find imei in warehouse [China Factory Warehouse]' . $text]);
                echo json_encode(['status' => 401,'message' => 'Not find imei in warehouse [WMFT - China Warranty Factory]' . $text]);
                exit();
            }

            $arr_check_imei_in_warehouse = array();

            foreach ($check_imei_in_warehouse as $key) {
                array_push($arr_check_imei_in_warehouse, $key['imei_sn']);
            }

            $imei_error = array_values(array_diff($old_imei,$arr_check_imei_in_warehouse));

            if($imei_error){
  
                $text = '';

                foreach ($imei_error as $key) {
                
                    if($text == ''){
                        $text = PHP_EOL . $key;
                    }else{
                        $text = $text . PHP_EOL . $key;
                    }
                }

                // echo json_encode(['status' => 401,'message' => 'Invalid imei in warehouse [WM04 - คลังโทรศัพท์เคลมโรงงาน]' . $text]);
                echo json_encode(['status' => 401,'message' => 'Not find imei in warehouse [China Factory Warehouse]' . $text]);
                exit();

            }



            //WM04 - คลังโทรศัพท์เคลมโรงงาน
            // $warehouse_id_from = 6;

            //WMPD - คลังตัด IMEI Product Warranty
            // $warehouse_id_to = 98;

            //WMKR - Kerry
            // $warehouse_id_end = 36;

            // //China Factory Warehouse
            // $warehouse_id_from = 140;

            // //China Factory Warehouse
            // $warehouse_id_to = 140;

            // //WMKR - Kerry
            // $warehouse_id_end = 36;


            //China WMFT - China Warranty Factory
            $warehouse_id_from = 130;

            //China Factory Warehouse
            $warehouse_id_to = 140;

            //WMKR - Kerry
            $warehouse_id_end = 36;


            $current_date = date('Y-m-d H:i:s');

            $good_id = $check1model[0]['good_id'];
            // $good_color = $check1model[0]['good_color'];
            $good_color = $good_color_id;
            $good_type = $check1model[0]['type'];
            $count_imei = count($new_imei);
            $price = $QGood->get_price($count_imei,$good_id);

            $by = $userStorage->id;

            if(isset($price['price']) and $price['price'] > 0){
                $price = $price['price'];
            }else{
                echo json_encode(['status' => 400,'message' => 'Invalid Product Price!']);
                exit();
            }

            // start check imei

            $array_check_imei = array();
            $imei_list_data = array();

            foreach ($check_imei_in_warehouse as $key => $valuse) {

                array_push($imei_list_data, ['imei' => $old_imei[$key],'imei_type' => $valuse['type'],'good_id' => $valuse['good_id'],'good_color' => $valuse['good_color']]);
                        
            }

            // end check imei

            // print_r($imei_list_data);
            // die;

            // start order product detail

            $list_data = array();
            foreach ($imei_list_data as $key_main => $value_main) {
                $check = 0;
                foreach ($list_data as $key_sub => $value_sub) {
                    if($value_main['good_id'] == $value_sub['good_id'] and $value_main['good_color'] == $value_sub['good_color']){
                        $list_data[$key_sub]['count'] = $list_data[$key_sub]['count']+1;
                        $check++;
                    }
                }
                if($check == 0){
                    array_push($list_data, ['good_id' => $value_main['good_id'],'good_color' => $value_main['good_color'],'count' => 1]);
                }
            }

            // end order product detail

            // start create co

            if($imei_list_data){

                $co_sn = date('YmdHis') . substr ( microtime (), 2, 4 );

                $cso_data_data = array(
                    'changed_sn'        => $co_sn,
                    'new_id'            => $warehouse_id_to,
                    'old_id'            => $warehouse_id_from,
                    'type'              => $good_type,
                    'is_changed_wh'     => 1,
                    'created_at'        => $current_date,
                    'created_by'        => $by,
                    'scanned_out_at'    => $current_date,
                    'scanned_out_by'    => $by,
                    'confirmed_out_at'  => $current_date,
                    'confirmed_out_by'  => $by,
                    'scanned_in_at'     => $current_date,
                    'scanned_in_by'     => $by,
                    'completed_date'    => $current_date,
                    'completed_user'    => $by,
                    'status'            => CHANGE_ORDER_STATUS_COMPLETED,
                    'changed_order'     => 0,
                    'remark'            => 'change sale for product warranty old'
                );

                $cso_data_id = $QChangeSalesOrder->insert($cso_data_data);
                $co_data = $this->getChangeOrderNo($co_sn);

                if(!$co_data){
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Invalid Data!4']);
                    exit();
                }

                foreach ($list_data as $key) {

                    $data = array(
                        'changed_id'    => $cso_data_id,
                        'cat_id'        => PHONE_CAT_ID,
                        'good_id'       => $key['good_id'],
                        'good_color'    => $key['good_color'],
                        'num'           => $key['count'],
                        'receive'       => $key['count'],
                        'new_id'        => $warehouse_id_to,
                        'old_id'        => $warehouse_id_from,
                        'created_at'    => $current_date,
                        'created_by'    => $by,
                        'changed_sn'    => $co_sn,
                        'status'        => 2
                    );

                    if($csp_data_id = $QChangeSalesProduct->insert($data)){

                        foreach ($imei_list_data as $key_getImei) {

                            if($key_getImei['good_id'] == $key['good_id'] && $key_getImei['good_color'] == $key['good_color']){

                                $data = array(
                                    'changed_sales_product_id' => $csp_data_id,
                                    'changed_sn' => $co_sn,
                                    'imei' => $key_getImei['imei'],
                                    'cat_id' => PHONE_CAT_ID,
                                    'status' => 2
                                );
                                
                                $QChangeSalesImei->insert($data);
                            }

                            $update_imei = array(
                                'warehouse_id' => $warehouse_id_to,
                                'changed_sn' => $co_sn
                            );

                            $where_update_imei = $QI->getAdapter()->quoteInto('imei_sn = ?', $key_getImei['imei']);

                            $QI->update($update_imei,$where_update_imei);

                        }

                    }
                    
                }
            }

            // end create co

            // start create po

            $QPO = new Application_Model_Po();

            $po_sn = date('YmdHis').substr(microtime(),2,4);

            $text = 'rework imei by system';
            $pay_user = 'confirm by system';
            $type = 2; //rework

            $data = array(
                'cat_id'       => PHONE_CAT_ID,
                'good_id'      => $good_id,
                'good_color'   => $good_color,
                'warehouse_id' => $warehouse_id_to,
                'num'          => $count_imei,
                'price'        => $price,
                'text'         => $text,
                'type'         => $type,
                'sn'           => $po_sn,
                'created_at'   => $current_date,
                'created_by'   => $by,
                'pay_user'     => $pay_user,
                'flow'         => $by,
                'flow_time'    => $current_date,
                'mysql_user'   => $by,
                'mysql_time'   => $current_date
            );

            $QPO->insert($data);

            $info = 'System: Purchase order number: '.$data['sn'];

            if ($po_sn != ''){
                if(!getPoOrderNo_Ref($po_sn)){
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Invalid Data! Can not po']);
                    exit();
                }
            }

            $QLog = new Application_Model_Log();

            $QLog->insert( array (
                'info' => $info,
                'user_id' => $by,
                'ip_address' => $ip,
                'time' => $current_date
            ) );

            $QImei = new Application_Model_Imei();

            foreach ($new_imei as $key) {
                
                $data = array(
                    'imei_sn'       => $key,
                    'po_sn'         => $po_sn,
                    'warehouse_id'  => $warehouse_id_to,
                    'good_id'       => $good_id,
                    'good_color'    => $good_color,
                    'into_date'     => $current_date,
                );

                $QImei->insert($data);
            }

            if($po_sn != ''){
                $QWarehouse = new Application_Model_Warehouse();
                $QWarehouse->getGROrderNo_Ref($po_sn);
            }

            // end create po

            // start create co end process

            $co_end_sn = date('YmdHis') . substr ( microtime (), 2, 4 );

            $cso_data_data = array(
                'changed_sn'        => $co_end_sn,
                'new_id'            => $warehouse_id_end,
                'old_id'            => $warehouse_id_to,
                'type'              => 1, //type normal only
                'is_changed_wh'     => 1,
                'created_at'        => $current_date,
                'created_by'        => $by,
                'scanned_out_at'    => $current_date,
                'scanned_out_by'    => $by,
                'confirmed_out_at'  => $current_date,
                // 'confirmed_out_by'  => $by,
                // 'scanned_in_at'     => $current_date,
                // 'scanned_in_by'     => $by,
                // 'completed_date'    => $current_date,
                // 'completed_user'    => $by,
                // 'status'            => CHANGE_ORDER_STATUS_COMPLETED,
                'status'            => CHANGE_ORDER_STATUS_ON_CHANGE,
                'changed_order'     => 0,
                'remark'            => 'change sale for product warranty new'
            );

            $cso_data_id = $QChangeSalesOrder->insert($cso_data_data);
            $co_data = $this->getChangeOrderNo($co_end_sn);

            if(!$co_data){
                $db->rollback();
                echo json_encode(['status' => 400,'message' => 'Invalid Data!6']);
                exit();
            }

            $data = array(
                'changed_id'    => $cso_data_id,
                'cat_id'        => PHONE_CAT_ID,
                'good_id'       => $good_id,
                'good_color'    => $good_color,
                'num'           => $count_imei,
                'receive'       => $count_imei,
                'new_id'        => $warehouse_id_end,
                'old_id'        => $warehouse_id_to,
                'created_at'    => $current_date,
                'created_by'    => $by,
                'changed_sn'    => $co_end_sn,
                'status'        => 1
            );

            if($csp_data_id = $QChangeSalesProduct->insert($data)){

                foreach ($new_imei as $key_sup) {

                    $data = array(
                        'changed_sales_product_id' => $csp_data_id,
                        'changed_sn' => $co_end_sn,
                        'imei' => $key_sup,
                        'cat_id' => PHONE_CAT_ID,
                        'status' => 1
                    );
                    
                    $QChangeSalesImei->insert($data);

                    // $update_imei = array(
                    //     'warehouse_id' => $warehouse_id_end,
                    //     'changed_sn' => $co_end_sn
                    // );

                    // $where_update_imei = $QI->getAdapter()->quoteInto('imei_sn = ?', $key_sup);

                    // $QI->update($update_imei,$where_update_imei);

                }

            }

            // end create co end process

            // start process swap imei

            $update_good_id_old = array('good_id_old' => 1);

            $where_update_good_id_old = $QI->getAdapter()->quoteInto('imei_sn in (?)', $old_imei);
            $QI->update($update_good_id_old,$where_update_good_id_old);

            $QIRN = new Application_Model_ImeiRenew();

            // get co by co_sn
            $where = $QChangeSalesOrder->getAdapter()->quoteInto('changed_sn = ?', $co_end_sn);
            $get_co_data = $QChangeSalesOrder->fetchRow($where);

            if(!isset($get_co_data['sn_ref']) || !$get_co_data['sn_ref']){
                $db->rollback();
                echo json_encode(['status' => 400,'message' => 'Error! can not get co for swap imei']);
                exit();
            }

            $get_co = $get_co_data['sn_ref'];

            // get po by po_sn
            $where = $QPO->getAdapter()->quoteInto('sn = ?', $po_sn);
            $get_po_data = $QPO->fetchRow($where);

            if(!isset($get_po_data['sn_ref']) || !$get_po_data['sn_ref']){
                $db->rollback();
                echo json_encode(['status' => 400,'message' => 'Error! can not get po for swap imei']);
                exit();
            }

            $get_po = $get_po_data['sn_ref'];

            foreach ($new_imei as $key => $value) {
                
                $data = array(
                    'imei_old'  => $old_imei[$key],
                    'imei_new'  => $value,
                    'co_sn'     => $get_co,
                    'po_sn'     => $get_po
                );

                $QIRN->insert($data);
            }

            // end process swap imei

            // start save log for report

            $QLSIR = new Application_Model_LogSwapImeiRework();

            // get co end by co_end_sn
            $where = $QChangeSalesOrder->getAdapter()->quoteInto('changed_sn = ?', $co_end_sn);
            $get_end_co = $QChangeSalesOrder->fetchRow($where);

            if(!isset($get_end_co['sn_ref']) || !$get_end_co['sn_ref']){
                $db->rollback();
                echo json_encode(['status' => 400,'message' => 'Error! can not get co for swap imei']);
                exit();
            }

            $get_end_co = $get_end_co['sn_ref'];

            $data = array(
                'co_sn'         => $co_sn,
                'co_ref'        => $get_co,
                'po_sn'         => $po_sn,
                'po_ref'        => $get_po,
                'co_end_sn'     => $co_end_sn,
                'co_end_ref'    => $get_end_co,
                'good_id'       => $good_id,
                'good_color'    => $good_color,
                'good_type'     => $good_type,
                'good_num'      => $count_imei,
                'created_by'    => $by,
                'created_date'  => $current_date,
                'status'        => 1
            );

            $report_id = $QLSIR->insert($data);

            // end save log for report

            $db->commit();

            $flashMessenger->setNamespace('success')->addMessage('Done! <br/>' . 'Report ID : ' . $report_id . '<br/>CO Swap : ' . $get_co . '<br/>PO : ' . $get_po . '<br/>CO End : ' . $get_end_co);
            // $flashMessenger->setNamespace('success')->addMessage('Done! <br/>' . 'Report ID : ' . $report_id . '<br/>PO : ' . $get_po . '<br/>CO : ' . $get_end_co);

            echo json_encode(['status' => 200,'message' => 'Done!']);
            exit();

        } catch (Exception $e){
            
            $db->rollback();
            echo json_encode(['status' => 400,'message' => 'Failed - '.$e->getMessage()]);
            exit();
        }

    }

    // $QGoodColor     = new Application_Model_GoodColor();
    // $goodColors     = $QGoodColor->get_cache();

    // $this->view->goodColors     = $goodColors;

    $this->view->messages_success   = $messages_success;
    $this->view->messages           = $messages;

    function getPoOrderNo_Ref($sn){
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref('PO',".$sn.")");
            return $stmt->execute();
        }catch (exception $e) {
            return false;
        }
    }

