<?php

    $flashMessenger = $this->_helper->flashMessenger;

    $id = $this->getRequest()->getParam('id');

    if(!$id){
    
        $flashMessenger->setNamespace('error')->addMessage('Invalid Data!');
        $this->_redirect(HOST."warehouse/request-list");
    }

    if ($this->getRequest()->getMethod() == 'POST') {

        $request_id = $id;
        
        $select_warehouse = $this->getRequest()->getParam('warehouse');
        // $type = $this->getRequest()->getParam('type');

        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        // if(!$type){
        //     $flashMessenger->setNamespace('error')->addMessage('Please Select Product Type!');
        //     $this->_redirect(HOST."warehouse/request-process?id=".$request_id);
        // }

        if(!$select_warehouse){
            $flashMessenger->setNamespace('error')->addMessage('Please Select Warehouse!');
            $this->_redirect(HOST."warehouse/request-process?id=".$request_id);
        }

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $QGood = new Application_Model_Good();
        $goods_cached = $QGood->get_cache();

        $QGoodColor = new Application_Model_GoodColor();
        $good_colors_cached = $QGoodColor->get_cache();

        $QWarehouse = new Application_Model_Warehouse();
        $QChangeSalesProduct = new Application_Model_ChangeSalesProduct();

        $QLog = new Application_Model_Log();

        $QBL = new Application_Model_BorrowingList();
        $getItem = $QBL->getItem($request_id);

        if(!$getItem){
            $flashMessenger->setNamespace('error')->addMessage('Not Find Item!');
            $this->_redirect(HOST."warehouse/request-process?id=".$request_id);
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

        if(!$type){
            $flashMessenger->setNamespace('error')->addMessage('Wrong Product Type!');
            $this->_redirect(HOST."warehouse/request-process?id=".$request_id);
        }

        try{
            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            $warehouse_id1 = $select_warehouse;

            //WM06 - คลังยืมให้พนักงานบริษัท
            $warehouse_id2 = 8;
            
            if(isset($warehouse_id1) and $warehouse_id2){
                $warehouse_from = $QWarehouse->find($warehouse_id1);
                $warehouse_from = $warehouse_from->current();

                $warehouse_to   = $QWarehouse->find($warehouse_id2);
                $warehouse_to   = $warehouse_to->current();

                if(empty($warehouse_from) || empty($warehouse_to)){
                    $flashMessenger->setNamespace('error')->addMessage('Failed - Invalid Warehouse!');
                    $this->_redirect(HOST."warehouse/request-process?id=".$request_id);
                }

                if($warehouse_from['company_id'] != $warehouse_to['company_id']){

                    $flashMessenger->setNamespace('error')->addMessage('Failed - Company is different!');
                    $this->_redirect(HOST."warehouse/request-process?id=".$request_id);
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
                    'borrowing_id'      => $request_id
                );

                $id = $QChangeSalesOrder->insert($data);

                if(!$id){
                    throw new Exception('Error Insert Change Sales Order : Please try again.');
                }

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
                $insertQCSL = $QChangeSalesList->insert($data);

                if(!$insertQCSL){
                    throw new Exception('Error Insert Change Sales List : Please try again.');
                }
            
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
                    $this->_redirect(HOST."warehouse/request-process?id=".$request_id);
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

                $insertQCSP = $QChangeSalesProduct->insert($data);
                if(!$insertQCSP){
                    throw new Exception('Error Insert Change Sales Product : Please try again.');
                }
            }

            //status 11 is confirm by wms
            $update = array(
                // 'read_data' => 1,
                // 'update_datetime' => date('Y-m-d H:i:s'),
                // 'status' => 11,
                'wms_status' => 1,
                'wms_datetime' => date('Y-m-d H:i:s'),
                'wms_by' => $userStorage->id
            );

            $where_update = $QBL->getAdapter()->quoteInto('id = ?', $request_id);
            $updateQBL = $QBL->update($update,$where_update);

            if(!$updateQBL){
                throw new Exception('Error Update Borrowing List : Please try again.');
            }

            $info = 'CHANGE ORDER: ' . $changed_sn;
            $QLog->insert( array (
                'info' => $info,
                'user_id' => $userStorage->id,
                'ip_address' => $ip,
                'time' => date('Y-m-d H:i:s'),
            ) );

            $check_sn_ref = $QChangeSalesOrder->getCoBySn($changed_sn);

            if(!$check_sn_ref || $check_sn_ref == '' || is_null($check_sn_ref)){
                throw new Exception('Error Can not create co : Please try again.');
            }

            $db->commit();

            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $this->_redirect(HOST."warehouse/request-list");

        } catch (Exception $e){
            $db->rollback();

            $flashMessenger->setNamespace('error')->addMessage('Failed - '.$e->getMessage());
            $this->_redirect(HOST."warehouse/request-process?id=".$request_id);
        }

    }

    $QBL = new Application_Model_BorrowingList();

    $getDetailsRequest = $QBL->getItemByRequert($id);

    if(!$getDetailsRequest){
        $flashMessenger->setNamespace('error')->addMessage('Not Find!');
        $this->_redirect(HOST."warehouse/request-list");
    }

    $QWarehouse = new Application_Model_Warehouse();
    $where = array();
        
    $warehouses_cached = $QWarehouse->getWarehouses();
    $warehouse_arr = array();

    foreach ($warehouses_cached as $k => $warehouse_data){
        $warehouse_arr[$warehouse_data['id']] = $warehouse_data['name']; 
    }
    $this->view->warehouses = $warehouse_arr;
           
    $this->view->getDetailsRequest = $getDetailsRequest;

    $this->view->id = $id;

    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;