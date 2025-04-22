<?php

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $flashMessenger = $this->_helper->flashMessenger;

    if ($this->getRequest()->getMethod() == 'POST') {

        $id_list = $this->getRequest()->getParam('id');
        $imei_list = $this->getRequest()->getParam('imei');

        $btn = $this->getRequest()->getParam('btn');

        if(!$id_list || !$imei_list){

            echo json_encode(['status' => 400,'message' => 'Invalid Data!1']);
            exit();
        }

        if(count($id_list) != count($imei_list)){
            echo json_encode(['status' => 400,'message' => 'Invalid Data!2']);
            exit();
        }

        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        try{
            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            $QBL = new Application_Model_BorrowingList();

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $QWarehouse = new Application_Model_Warehouse();
            $QChangeSalesProduct = new Application_Model_ChangeSalesProduct();
            $QChangeSalesImei = new Application_Model_ChangeSalesImei();
            $QLog = new Application_Model_Log();
            $QBT = new Application_Model_BorrowingTran();
            $QI = new Application_Model_Imei();
            $QChangeSalesOrder = new Application_Model_ChangeSalesOrder();

            //WM06 - คลังยืมให้พนักงานบริษัท
            $warehouse_id_from = 8;

            switch ($btn) {
                case 'change_warehouse':
                    //WM79 คลังรอเปิดบิลอภินันทนาการ
                    $warehouse_id_to = 117;
                    break;
                case 'return':
                    //WM03 - คลังโทรศัพท์เกรด B
                    // $warehouse_id_to = 5;

                    //Borrow Warehouse
                    $warehouse_id_to = 142;
                    break;
                
                default:
                    echo json_encode(['status' => 400,'message' => 'Invalid Data3!']);
                    exit();
                    break;
            }

            // start check imei

            $array_check_imei = array();
            $imei_list1_normal = array();
            $imei_list2_demo = array();
            $imei_list5_apk = array();

            foreach ($id_list as $key => $value) {
                
               $checkBorrowingImeiDetail = $QBT->CheckBorrowingImeiDetail($value,$imei_list[$key],$warehouse_id_from);

               if($checkBorrowingImeiDetail){
                    switch ($checkBorrowingImeiDetail['imei_type']) {
                        case '1':
                            array_push($imei_list1_normal, ['bl_id' => $value, 'imei' => $imei_list[$key],'imei_type' => $checkBorrowingImeiDetail['imei_type'],'good_id' => $checkBorrowingImeiDetail['good_id'],'good_color' => $checkBorrowingImeiDetail['good_color']]);
                            break;
                        case '2':
                            array_push($imei_list2_demo, ['bl_id' => $value, 'imei' => $imei_list[$key],'imei_type' => $checkBorrowingImeiDetail['imei_type'],'good_id' => $checkBorrowingImeiDetail['good_id'],'good_color' => $checkBorrowingImeiDetail['good_color']]);
                            break;
                        case '5':
                            array_push($imei_list5_apk, ['bl_id' => $value, 'imei' => $imei_list[$key],'imei_type' => $checkBorrowingImeiDetail['imei_type'],'good_id' => $checkBorrowingImeiDetail['good_id'],'good_color' => $checkBorrowingImeiDetail['good_color']]);
                            break;
                    }
               }else{
                    array_push($array_check_imei, ['bl_id' => $value,'imei' => $imei_list[$key]]);
               }

            }

            if($array_check_imei){

                $imei_error = '';

                foreach ($array_check_imei as $key => $value) {
                    if($key == 0){
                        $imei_error .= $value['imei'] . ',';
                    }else{
                        $imei_error .= $value['imei'];
                    }
                }

                $db->rollback();
                echo json_encode(['status' => 400,'message' => 'Invalid Imei ' . $imei_error]);
                exit();
            }

            // end check imei

            // print_r($imei_list1_normal);
            // print_r($imei_list2_demo);
            // print_r($imei_list5_apk);
            // die;

            // start order product detail

            $list1_normal = array();
            foreach ($imei_list1_normal as $key_main => $value_main) {
                $check = 0;
                foreach ($list1_normal as $key_sub => $value_sub) {
                    if($value_main['good_id'] == $value_sub['good_id'] and $value_main['good_color'] == $value_sub['good_color']){
                        $list1_normal[$key_sub]['count'] = $list1_normal[$key_sub]['count']+1;
                        $check++;
                    }
                }
                if($check == 0){
                    array_push($list1_normal, ['good_id' => $value_main['good_id'],'good_color' => $value_main['good_color'],'count' => 1]);
                }
            }

            $list2_demo = array();
            foreach ($imei_list2_demo as $key_main => $value_main) {
                $check = 0;
                foreach ($list2_demo as $key_sub => $value_sub) {
                    if($value_main['good_id'] == $value_sub['good_id'] and $value_main['good_color'] == $value_sub['good_color']){
                        $list2_demo[$key_sub]['count'] = $list2_demo[$key_sub]['count']+1;
                        $check++;
                    }
                }
                if($check == 0){
                    array_push($list2_demo, ['good_id' => $value_main['good_id'],'good_color' => $value_main['good_color'],'count' => 1]);
                }
            }

            $list5_apk = array();
            foreach ($imei_list5_apk as $key_main => $value_main) {
                $check = 0;
                foreach ($list5_apk as $key_sub => $value_sub) {
                    if($value_main['good_id'] == $value_sub['good_id'] and $value_main['good_color'] == $value_sub['good_color']){
                        $list5_apk[$key_sub]['count'] = $list5_apk[$key_sub]['count']+1;
                        $check++;
                    }
                }
                if($check == 0){
                    array_push($list5_apk, ['good_id' => $value_main['good_id'],'good_color' => $value_main['good_color'],'count' => 1]);
                }
            }

            // end order product detail

            // start create co

            if($imei_list1_normal){

                $changed1_normal_sn = date('YmdHis') . substr ( microtime (), 2, 4 );

                $cso1_normal_data = array(
                    'changed_sn'        => $changed1_normal_sn,
                    'new_id'            => $warehouse_id_to,
                    'old_id'            => $warehouse_id_from,
                    'type'              => 1,
                    'is_changed_wh'     => 1,
                    'created_at'        => date('Y-m-d H:i:s'),
                    'created_by'        => $userStorage->id,
                    'scanned_out_at'    => date('Y-m-d H:i:s'),
                    'scanned_out_by'    => $userStorage->id,
                    'confirmed_out_at'  => date('Y-m-d H:i:s'),
                    'confirmed_out_by'  => $userStorage->id,
                    'scanned_in_at'     => date('Y-m-d H:i:s'),
                    'scanned_in_by'     => $userStorage->id,
                    'completed_date'    => date('Y-m-d H:i:s'),
                    'completed_user'    => $userStorage->id,
                    'status'            => CHANGE_ORDER_STATUS_COMPLETED,
                    'changed_order'     => 0
                );

                $cso1_normal_id = $QChangeSalesOrder->insert($cso1_normal_data);
                $co1_normal = $this->getChangeOrderNo($changed1_normal_sn);

                if(!$co1_normal){
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Invalid Data!4']);
                    exit();
                }

                $db = Zend_Registry::get('db');
                $select = $db->select()->from(array('p' => 'change_sales_order'),array('p.id','p.sn_ref'))->where('changed_sn = ?', $changed1_normal_sn);
                $get_co = $db->fetchRow($select);

                if(!isset($get_co['sn_ref']) || !$get_co['sn_ref']){
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Invalid Data!5']);
                    exit();
                }

                $co1_normal = $get_co['sn_ref'];

                foreach ($list1_normal as $key) {

                    $data = array(
                        'changed_id'    => $cso1_normal_id,
                        'cat_id'        => PHONE_CAT_ID,
                        'good_id'       => $key['good_id'],
                        'good_color'    => $key['good_color'],
                        'num'           => $key['count'],
                        'receive'       => $key['count'],
                        'new_id'        => $warehouse_id_to,
                        'old_id'        => $warehouse_id_from,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'created_by'    => $userStorage->id,
                        'changed_sn'    => $changed1_normal_sn,
                        'status'        => 2
                    );

                    if($csp1_normal_id = $QChangeSalesProduct->insert($data)){

                        foreach ($imei_list1_normal as $key_getImei) {

                            if($key_getImei['good_id'] == $key['good_id'] && $key_getImei['good_color'] == $key['good_color']){

                                $data = array(
                                'changed_sales_product_id' => $csp1_normal_id,
                                'changed_sn' => $changed1_normal_sn,
                                'imei' => $key_getImei['imei'],
                                'cat_id' => PHONE_CAT_ID,
                                'status' => 2);
                                
                                $QChangeSalesImei->insert($data);
                            }

                            $update_bt = array(
                                'return_date' => date('Y-m-d H:i:s'),
                                'return_by' => $userStorage->id,
                                'update_date' => date('Y-m-d H:i:s'),
                                'update_by' => $userStorage->id,
                                'status' => 2,
                                'co_return' => $co1_normal
                            );

                            $where_update_bt = array();
                            $where_update_bt[] = $QBT->getAdapter()->quoteInto('bl_id = ?', $key_getImei['bl_id']);
                            $where_update_bt[] = $QBT->getAdapter()->quoteInto('imei = ?', $key_getImei['imei']);
                            $QBT->update($update_bt,$where_update_bt);

                            $update_imei = array(
                                'warehouse_id' => $warehouse_id_to,
                                'changed_sn' => $changed1_normal_sn
                            );

                            $where_update_imei = $QI->getAdapter()->quoteInto('imei_sn = ?', $key_getImei['imei']);

                            $QI->update($update_imei,$where_update_imei);

                        }

                    }
                    
                }
            }

            if($imei_list2_demo){

                $changed2_demo_sn = date('YmdHis') . substr ( microtime (), 2, 4 );

                $cso2_demo_data = array(
                    'changed_sn'        => $changed2_demo_sn,
                    'new_id'            => $warehouse_id_to,
                    'old_id'            => $warehouse_id_from,
                    'type'              => 2,
                    'is_changed_wh'     => 1,
                    'created_at'        => date('Y-m-d H:i:s'),
                    'created_by'        => $userStorage->id,
                    'scanned_out_at'    => date('Y-m-d H:i:s'),
                    'scanned_out_by'    => $userStorage->id,
                    'confirmed_out_at'  => date('Y-m-d H:i:s'),
                    'confirmed_out_by'  => $userStorage->id,
                    'scanned_in_at'     => date('Y-m-d H:i:s'),
                    'scanned_in_by'     => $userStorage->id,
                    'completed_date'    => date('Y-m-d H:i:s'),
                    'completed_user'    => $userStorage->id,
                    'status'            => CHANGE_ORDER_STATUS_COMPLETED,
                    'changed_order'     => 0
                );

                $cso2_demo_id = $QChangeSalesOrder->insert($cso2_demo_data);
                $co2_demo = $this->getChangeOrderNo($changed2_demo_sn);

                if(!$co2_demo){
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Invalid Data6!']);
                    exit();
                }

                $db = Zend_Registry::get('db');
                $select = $db->select()->from(array('p' => 'change_sales_order'),array('p.id','p.sn_ref'))->where('changed_sn = ?', $changed2_demo_sn);
                $get_co = $db->fetchRow($select);

                if(!isset($get_co['sn_ref']) || !$get_co['sn_ref']){
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Invalid Data7!']);
                    exit();
                }

                $co2_demo = $get_co['sn_ref'];

                foreach ($list2_demo as $key) {

                    $data = array(
                        'changed_id'    => $cso2_demo_id,
                        'cat_id'        => PHONE_CAT_ID,
                        'good_id'       => $key['good_id'],
                        'good_color'    => $key['good_color'],
                        'num'           => $key['count'],
                        'receive'       => $key['count'],
                        'new_id'        => $warehouse_id_to,
                        'old_id'        => $warehouse_id_from,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'created_by'    => $userStorage->id,
                        'changed_sn'    => $changed2_demo_sn,
                        'status'        => 2
                    );

                    if($csp2_demo_id = $QChangeSalesProduct->insert($data)){

                        foreach ($imei_list2_demo as $key_getImei) {

                            if($key_getImei['good_id'] == $key['good_id'] && $key_getImei['good_color'] == $key['good_color']){

                                $data = array(
                                'changed_sales_product_id' => $csp2_demo_id,
                                'changed_sn' => $changed2_demo_sn,
                                'imei' => $key_getImei['imei'],
                                'cat_id' => PHONE_CAT_ID,
                                'status' => 2);
                                
                                $QChangeSalesImei->insert($data);
                            }

                            $update_bt = array(
                                'return_date' => date('Y-m-d H:i:s'),
                                'return_by' => $userStorage->id,
                                'update_date' => date('Y-m-d H:i:s'),
                                'update_by' => $userStorage->id,
                                'status' => 2,
                                'co_return' => $co2_demo
                            );

                            $where_update_bt = array();
                            $where_update_bt[] = $QBT->getAdapter()->quoteInto('bl_id = ?', $key_getImei['bl_id']);
                            $where_update_bt[] = $QBT->getAdapter()->quoteInto('imei = ?', $key_getImei['imei']);
                            $QBT->update($update_bt,$where_update_bt);

                            $update_imei = array(
                                'warehouse_id' => $warehouse_id_to,
                                'changed_sn' => $changed2_demo_sn
                            );

                            $where_update_imei = $QI->getAdapter()->quoteInto('imei_sn = ?', $key_getImei['imei']);

                            $QI->update($update_imei,$where_update_imei);

                        }

                    }
                    
                }
            }

            if($imei_list5_apk){

                $changed5_apk_sn = date('YmdHis') . substr ( microtime (), 2, 4 );

                $cso5_apk_data = array(
                    'changed_sn'        => $changed5_apk_sn,
                    'new_id'            => $warehouse_id_to,
                    'old_id'            => $warehouse_id_from,
                    'type'              => 5,
                    'is_changed_wh'     => 1,
                    'created_at'        => date('Y-m-d H:i:s'),
                    'created_by'        => $userStorage->id,
                    'scanned_out_at'    => date('Y-m-d H:i:s'),
                    'scanned_out_by'    => $userStorage->id,
                    'confirmed_out_at'  => date('Y-m-d H:i:s'),
                    'confirmed_out_by'  => $userStorage->id,
                    'scanned_in_at'     => date('Y-m-d H:i:s'),
                    'scanned_in_by'     => $userStorage->id,
                    'completed_date'    => date('Y-m-d H:i:s'),
                    'completed_user'    => $userStorage->id,
                    'status'            => CHANGE_ORDER_STATUS_COMPLETED,
                    'changed_order'     => 0
                );

                $cso5_apk_id = $QChangeSalesOrder->insert($cso5_apk_data);
                $co5_apk = $this->getChangeOrderNo($changed5_apk_sn);

                if(!$co5_apk){
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Invalid Data8!']);
                    exit();
                }

                $db = Zend_Registry::get('db');
                $select = $db->select()->from(array('p' => 'change_sales_order'),array('p.id','p.sn_ref'))->where('changed_sn = ?', $changed5_apk_sn);
                $get_co = $db->fetchRow($select);

                if(!isset($get_co['sn_ref']) || !$get_co['sn_ref']){
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Invalid Data9!']);
                    exit();
                }

                $co5_apk = $get_co['sn_ref'];

                foreach ($list5_apk as $key) {

                    $data = array(
                        'changed_id'    => $cso5_apk_id,
                        'cat_id'        => PHONE_CAT_ID,
                        'good_id'       => $key['good_id'],
                        'good_color'    => $key['good_color'],
                        'num'           => $key['count'],
                        'receive'       => $key['count'],
                        'new_id'        => $warehouse_id_to,
                        'old_id'        => $warehouse_id_from,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'created_by'    => $userStorage->id,
                        'changed_sn'    => $changed5_apk_sn,
                        'status'        => 2
                    );

                    if($csp5_apk_id = $QChangeSalesProduct->insert($data)){

                        foreach ($imei_list5_apk as $key_getImei) {

                            if($key_getImei['good_id'] == $key['good_id'] && $key_getImei['good_color'] == $key['good_color']){

                                $data = array(
                                'changed_sales_product_id' => $csp5_apk_id,
                                'changed_sn' => $changed5_apk_sn,
                                'imei' => $key_getImei['imei'],
                                'cat_id' => PHONE_CAT_ID,
                                'status' => 2);
                                
                                $QChangeSalesImei->insert($data);
                            }

                            $update_bt = array(
                                'return_date' => date('Y-m-d H:i:s'),
                                'return_by' => $userStorage->id,
                                'update_date' => date('Y-m-d H:i:s'),
                                'update_by' => $userStorage->id,
                                'status' => 2,
                                'co_return' => $co5_apk
                            );

                            $where_update_bt = array();
                            $where_update_bt[] = $QBT->getAdapter()->quoteInto('bl_id = ?', $key_getImei['bl_id']);
                            $where_update_bt[] = $QBT->getAdapter()->quoteInto('imei = ?', $key_getImei['imei']);
                            $QBT->update($update_bt,$where_update_bt);

                            $update_imei = array(
                                'warehouse_id' => $warehouse_id_to,
                                'changed_sn' => $changed5_apk_sn
                            );

                            $where_update_imei = $QI->getAdapter()->quoteInto('imei_sn = ?', $key_getImei['imei']);

                            $QI->update($update_imei,$where_update_imei);

                        }

                    }
                    
                }
            }

            // end create co

            // start check end process return rq

            $array_id = array_values(array_unique($id_list));

            foreach ($array_id as $key) {

                if(!$QBL->checkEndProcessRQ($key)){

                    $update_bl = array(
                        'status' => '14',
                        'read_data' => '1',
                        'wms_return_date' => date('Y-m-d H:i:s'),
                        'wms_return_by' => $userStorage->id
                    );

                    $where_update_bl = $QBL->getAdapter()->quoteInto('id = ?', $key);
                    $QBL->update($update_bl,$where_update_bl);
                }
            }

            // end check end process return rq

            $db->commit();

            $flashMessenger->setNamespace('success')->addMessage('Done!');

            echo json_encode(['status' => 200,'message' => 'Done!']);
            exit();

        } catch (Exception $e){
            
            $db->rollback();
            echo json_encode(['status' => 400,'message' => 'Failed - '.$e->getMessage()]);
            exit();
        }

    }

    echo json_encode(['status' => 400,'message' => 'Invalid Data10!']);
    exit();