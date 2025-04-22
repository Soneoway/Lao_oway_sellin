<?php

    $flashMessenger = $this->_helper->flashMessenger;

    $id = $this->getRequest()->getParam('id');

    if(!$id){
    
        $flashMessenger->setNamespace('error')->addMessage('Invalid Data!');
        $this->_redirect(HOST."warehouse/borrowing-return-list");
    }

    if ($this->getRequest()->getMethod() == 'POST') {

        $request_id = $id;
        
        $select_warehouse = $this->getRequest()->getParam('warehouse');
        // $type = $this->getRequest()->getParam('type');

        $submit_checkbox_imei_missing = $this->getRequest()->getParam('checkbox_imei_missing_submit');

        $submit_imei = $this->getRequest()->getParam('imei_submit');
        $submit_imei_missing = $this->getRequest()->getParam('imei_missing_submit');

        $have_imei = $this->getRequest()->getParam('have_imei');

        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        // if(!$type){
        //     $flashMessenger->setNamespace('error')->addMessage('Please Select Product Type!');
        //     $this->_redirect(HOST."warehouse/borrowing-return-process?id=".$request_id);
        // }

        // Start : Check imei

        if(!$select_warehouse){
            $flashMessenger->setNamespace('error')->addMessage('Please Select Warehouse!');
            $this->_redirect(HOST."warehouse/borrowing-return-process?id=".$request_id);
        }

        if($submit_checkbox_imei_missing && !$submit_imei_missing && $have_imei){
            $flashMessenger->setNamespace('error')->addMessage('Please Enter IMEI Missing!');
            $this->_redirect(HOST."warehouse/borrowing-return-process?id=".$request_id);
        }

        if(!$submit_checkbox_imei_missing && !$submit_imei && $have_imei){
            $flashMessenger->setNamespace('error')->addMessage('Please Enter IMEI!');
            $this->_redirect(HOST."warehouse/borrowing-return-process?id=".$request_id);
        }

        $QBL = new Application_Model_BorrowingList();

        $getImei = $QBL->getImeiByID($id);

        if(!$getImei){

            $flashMessenger->setNamespace('error')->addMessage('Not find borrowing!');
            $this->_redirect(HOST."warehouse/borrowing-return-process?id=".$request_id);
        }

        if($getImei[0]['status'] != '13'){

            $flashMessenger->setNamespace('error')->addMessage('Wrong step!');
            $this->_redirect(HOST."warehouse/borrowing-return-process?id=".$request_id);
        }

        $NEWLINE_RE = '/(\r\n)|\r|\n/';
        $arrImei = explode(PHP_EOL, $submit_imei);
        $arrImei = preg_replace($NEWLINE_RE,'', $arrImei);

        if($submit_checkbox_imei_missing){
            $arrImeiMissing = explode(PHP_EOL, $submit_imei_missing);
            $arrImeiMissing = preg_replace($NEWLINE_RE,'', $arrImeiMissing);

            $arrImei = array_merge($arrImei,$arrImeiMissing);
        }

        $arrImei = array_filter($arrImei);

        $duplicates = array_values(array_unique(array_diff_assoc($arrImei, array_unique($arrImei))));

        if($duplicates){
            $flashMessenger->setNamespace('error')->addMessage('Duplicates IMEI!');
            $this->_redirect(HOST."warehouse/borrowing-return-process?id=".$request_id);
        }

        $arrImeiSystem = [];

        foreach ($getImei as $key) {
            array_push($arrImeiSystem, $key['imei_sn']);
        }

        $diffSystem = array_diff($arrImeiSystem, $arrImei);
        $diffImei = array_diff($arrImei, $arrImeiSystem);

        $checkImei = false;

        if(!$have_imei){
            $checkImei = true;
        }

        if(!$diffSystem && !$diffImei){
            $checkImei = true;
        }

        if(!$checkImei){
            $flashMessenger->setNamespace('error')->addMessage('Invalid Checking!');
            $this->_redirect(HOST."warehouse/borrowing-return-process?id=".$request_id);
        }

        // End : Check imei


        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $QGood = new Application_Model_Good();
        $goods_cached = $QGood->get_cache();

        $QGoodColor = new Application_Model_GoodColor();
        $good_colors_cached = $QGoodColor->get_cache();

        $QWarehouse = new Application_Model_Warehouse();
        $QChangeSalesProduct = new Application_Model_ChangeSalesProduct();
        $QChangeSalesImei = new Application_Model_ChangeSalesImei();

        $QLog = new Application_Model_Log();

        $QBL = new Application_Model_BorrowingList();
        $getItem = $QBL->getItem($request_id);

        if(!$getItem){
            $flashMessenger->setNamespace('error')->addMessage('Not Find Item!');
            $this->_redirect(HOST."warehouse/borrowing-return-process?id=".$request_id);
        }

        $type = null;

        // 1=A,2=B,3=Demo,4=APK,5=Prototype
        switch ($getItem[0]['product_grade']) {
            case '1':
                $type = 1;
                break;
            case '2':
                $type = 1;
                break;
            case '3':
                $type = 2;
                break;
            case '4':
                $type = 5;
                break;
            case '5':
                $type = 1;
                break;
        }

        try{
            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            //WM06 - คลังยืมให้พนักงานบริษัท
            $warehouse_id1 = 8;

            $warehouse_id2 = $select_warehouse;
            
            if(isset($warehouse_id1) and $warehouse_id2){
                $warehouse_from = $QWarehouse->find($warehouse_id1);
                $warehouse_from = $warehouse_from->current();

                $warehouse_to   = $QWarehouse->find($warehouse_id2);
                $warehouse_to   = $warehouse_to->current();

                if(empty($warehouse_from) || empty($warehouse_to)){
                    $flashMessenger->setNamespace('error')->addMessage('Failed - Invalid Warehouse!');
                    $this->_redirect(HOST."warehouse/borrowing-return-process?id=".$request_id);
                }

                if($warehouse_from['company_id'] != $warehouse_to['company_id']){

                    $flashMessenger->setNamespace('error')->addMessage('Failed - Company is different!');
                    $this->_redirect(HOST."warehouse/borrowing-return-process?id=".$request_id);
                }

            }

            $changed_sn = date('YmdHis') . substr ( microtime (), 2, 4 );

            $is_changed_wh = 1;
            $is_changed_wh2 = 2;

            $QChangeSalesOrder = new Application_Model_ChangeSalesOrder();
            $data = array(
                'type'              => $type,
                'is_changed_wh'     => $is_changed_wh,
                'changed_order'     => $is_changed_wh2,
                'new_id'            => $warehouse_id2,
                'old_id'            => $warehouse_id1,
                'created_at'        => date('Y-m-d H:i:s'),
                'created_by'        => $userStorage->id,
                'changed_sn'        => $changed_sn,
                'borrowing_id'      => $request_id,
                'status'            => CHANGE_ORDER_STATUS_SCANNED_OUT,
                'scanned_out_at'    => date('Y-m-d H:i:s'),
                'scanned_out_by'    => $userStorage->id
            );

            $id = $QChangeSalesOrder->insert($data);
            $this->getChangeOrderNo($changed_sn);

            $QChangeSalesList = new Application_Model_ChangeSalesList();
            // $temp = explode('/' , $product_demand);
            // $date_demand = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
            $date_demand = date('Y-m-d 00:00:00');

            // 1=A,2=B,3=Demo,4=APK,5=Prototype
            switch ($getItem[0]['product_grade']) {
                case '1':
                    $grade = 'A';
                    break;
                case '2':
                    $grade = 'B';
                    break;
                case '3':
                    $grade = 'Demo';
                    break;
                case '4':
                    $grade = 'APK';
                    break;
                case '5':
                    $grade = 'Prototype';
                    break;
                default:
                    $grade = '';
                    break;
            }

            $doc_type = 1;
            $getSalesman_id = $QBL->getNameBorrowingByID($getItem[0]['created_by']);

            if($getSalesman_id){
                $salesman_id = $getSalesman_id['fullname'];
            }else{
                $salesman_id = $getItem[0]['created_by'];
            }

            $detail = 'ผู้ดูแล ' .$salesman_id. ' (เครื่อง Grade ' .$grade. ')';
            $note = $getItem[0]['remark'] . '(วันที่ส่งคืน '.$getItem[0]['return_date'].')';

            $data = array(
                    'changed_id'    => $id,
                    'changed_co'    => $changed_sn,
                    'doc_type'      => $doc_type,
                    'salesman_id'   => $salesman_id,
                    'product_demand'=> $date_demand,
                    'detail'        => $detail,
                    'note'          => $note,
                   
                );
            $QChangeSalesList->insert($data);
            
            // check quantity
            foreach ($getItem as $key){

                $storageParams = array(
                    'warehouse_id'  => $warehouse_id1,
                    'cat_id'        => $key['cat_id'],
                    'good_id'       => $key['good_id'],
                    'good_color_id' => $key['good_color_id']
                );

                // truong hop edit lai
                $storageParams['current_change_order_id']   = $id;

                $storageParams['not_get_ilike_bad_count']      =
                $storageParams['not_get_digital_bad_count']    =
                $storageParams['not_get_imei_bad_count']       =
                $storageParams['not_get_damage_product_count'] =
                $storageParams['not_get_total']                =
                $storageParams['not_order']                    =
                    true;


                $storageParams['not_get_ilike_count'] = true;


                $storage            = $QGood->fetchPaginationStorage(1, null, $total2, $storageParams);

                $current_order      = isset($storage[0]['current_order']) ? $storage[0]['current_order'] : 0;
                $current_change_order      = isset($storage[0]['current_change_order']) ? $storage[0]['current_change_order'] : 0;
                if ($key['cat_id']==PHONE_CAT_ID and $type==FOR_DEMO){
                    $current_order          = isset($storage[0]['current_order_demo']) ? $storage[0]['current_order_demo'] : 0;
                    $current_change_order   = isset($storage[0]['current_change_order_demo']) ? $storage[0]['current_change_order_demo'] : 0;
                }else if ($key['cat_id']==PHONE_CAT_ID and $type==FOR_APK){
                    $current_order          = isset($storage[0]['current_order_apk']) ? $storage[0]['current_order_apk'] : 0;
                    $current_change_order   = isset($storage[0]['current_change_order_apk']) ? $storage[0]['current_change_order_apk'] : 0;
                }

                $current_storage    = 0;

                if (isset($storage[0]) and $storage[0]) {
                    switch ($key['cat_id']){
                        case DIGITAL_CAT_ID:
                            $current_storage = (isset($storage[0]['digital_count']) and $storage[0]['digital_count']) ? $storage[0]['digital_count'] : 0;
                            break;
                        case PHONE_CAT_ID:
                            $current_storage = (isset($storage[0]['imei_count']) and $storage[0]['imei_count']) ? $storage[0]['imei_count'] : 0;
                            if ($type==FOR_DEMO){
                                $current_storage = (isset($storage[0]['imei_demo_count']) and $storage[0]['imei_demo_count']) ? $storage[0]['imei_demo_count'] : 0;
                            }else if ($type==FOR_APK){
                                $current_storage = (isset($storage[0]['imei_apk_count']) and $storage[0]['imei_apk_count']) ? $storage[0]['imei_apk_count'] : 0;
                            }
                            break;
                        case ILIKE_CAT_ID:
                            $current_storage = (isset($storage[0]['ilike_count']) and $storage[0]['ilike_count']) ? $storage[0]['ilike_count'] : 0;
                            break;
                        case ACCESS_CAT_ID:
                            $current_storage = (isset($storage[0]['product_count']) and $storage[0]['product_count']) ? $storage[0]['product_count'] : 0;
                            break;

                    }
                }

                $count = $current_storage - $current_order - $current_change_order;

                if (($current_storage - $current_order - $current_change_order) < $key['total_qty']) {

                    $flashMessenger->setNamespace('error')->addMessage('Failed - the quantity '.$goods_cached[$key['good_id']].' | '.$good_colors_cached[$key['good_color_id']].' is not enough in this Warehouse!');
                    $this->_redirect(HOST."warehouse/borrowing-return-process?id=".$request_id);
                }

                $data = array(
                    'changed_id'    => $id,
                    'cat_id'        => $key['cat_id'],
                    'good_id'       => $key['good_id'],
                    'good_color'    => $key['good_color_id'],
                    'num'           => $key['total_qty'],
                    'new_id'        => $warehouse_id2,
                    'old_id'        => $warehouse_id1,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'created_by'    => $userStorage->id,
                    'changed_sn'    => $changed_sn
                );

                if($cspid = $QChangeSalesProduct->insert($data)){

                    foreach ($getImei as $key_getImei) {

                        if($key_getImei['good_id'] == $key['good_id'] && $key_getImei['good_color'] == $key['good_color_id']){
                            $data = array(
                            'changed_sales_product_id' => $cspid,
                            'changed_sn' => $changed_sn,
                            'imei' => $key_getImei['imei_sn'],
                            'cat_id' => PHONE_CAT_ID);
                            
                            $QChangeSalesImei->insert($data);
                        }
                    }

                    $countMissing = 0;

                    if(isset($arrImeiMissing)){
                        $countMissing = count($arrImeiMissing);
                    }

                    if($submit_checkbox_imei_missing and $countMissing > 0){
                        $update = array(
                            'wms_return_date' => date('Y-m-d H:i:s'),
                            'wms_return_by' => $userStorage->id,
                            'missing'   => $countMissing
                        );
                    }else{
                        $update = array(
                            'wms_return_date' => date('Y-m-d H:i:s'),
                            'wms_return_by' => $userStorage->id
                        );
                    }

                    

                    $where_update = $QBL->getAdapter()->quoteInto('id = ?', $request_id);
                    $QBL->update($update,$where_update);
                }

            }

            if($submit_checkbox_imei_missing){

                $QIM = new Application_Model_ImeiMissing();

                foreach ($arrImeiMissing as $key) {

                    $DataImeiMissing = array(
                        'imei_sn' => $key,
                        'rq_id' => $getImei[0]['id'],
                        'rq_sn' => $getImei[0]['sn'],
                        'rq' => $getImei[0]['rq'],
                        'created_date' => date('Y-m-d H:i:s'),
                        'created_by' => $userStorage->id
                    );

                    $QIM->insert($DataImeiMissing);
                }

            }

            $db->commit();

            $info = 'RETURN: ' . $changed_sn;
            $QLog->insert( array (
                'info' => $info,
                'user_id' => $userStorage->id,
                'ip_address' => $ip,
                'time' => date('Y-m-d H:i:s'),
            ) );

            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $this->_redirect(HOST."warehouse/borrowing-return-list");

        } catch (Exception $e){
            $db->rollback();

            $flashMessenger->setNamespace('error')->addMessage('Failed - '.$e->getMessage());
            $this->_redirect(HOST."warehouse/borrowing-return-process?id=".$request_id);
        }

    }

    $QBL = new Application_Model_BorrowingList();

    $getDetailsBorrowing = $QBL->getDetailsBorrowingByID($id);

    if(!$getDetailsBorrowing){
        $flashMessenger->setNamespace('error')->addMessage('Not Find!');
        $this->_redirect(HOST."warehouse/borrowing-return-list");
    }

    $getDetailsReturn = $QBL->getItemByReturn($id, null, $getDetailsBorrowing['wms_return_date']);

    if(!$getDetailsReturn){
        $flashMessenger->setNamespace('error')->addMessage('Not Find!');
        $this->_redirect(HOST."warehouse/borrowing-return-list");
    }

    if($getDetailsReturn[0]['wms_return_date']){
        $flashMessenger->setNamespace('error')->addMessage('This RQ is returned!');
        $this->_redirect(HOST."warehouse/borrowing-return-list");
    }

    $have_imei = null;

    foreach ($getDetailsReturn as $key) {
        // allow cat phone,digital
        if(in_array($key['good_cat_id'], [11,13])){
            $have_imei++;
        }
    }

    $this->view->have_imei = $have_imei;

    $QWarehouse = new Application_Model_Warehouse();

    $this->view->warehouse_cache = $QWarehouse->get_cache();

    $this->view->getDetailsReturn = $getDetailsReturn;

    $this->view->id = $id;

    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;