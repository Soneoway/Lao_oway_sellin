<?php

    $flashMessenger = $this->_helper->flashMessenger;
    
    $messages           = $flashMessenger->setNamespace('error')->getMessages();
    $messages_success   = $flashMessenger->setNamespace('success')->getMessages();

    if ($this->getRequest()->getMethod() == 'POST') {

        $imei = $this->getRequest()->getParam('imei');
        $imei = str_replace(' ', '', $imei);

        if(!$imei){
            echo json_encode(['status' => 400,'message' => 'Invalid Data! : Please input old imei']);
            exit();
        }

        $imei = explode("\n", $imei);

        $imei = array_values(array_unique($imei));

        try{
            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $QImei = new Application_Model_Imei();

            // check lock imei
            $QImeiLock = new Application_Model_ImeiLock();
            $getImeiLock = $QImeiLock->checkImeiLock($imei);

            $listImeiLock = '';
            foreach ($getImeiLock as $key => $value) {
                if($key == 0){
                    $listImeiLock = $value['imei_log'];
                }else{
                    $listImeiLock .= PHP_EOL . $value['imei_log'];
                }

            }
            if($listImeiLock){
                echo json_encode(['status' => 401,'message' => 'IMEI Locked' . PHP_EOL . $listImeiLock . PHP_EOL]);
                exit();
            }

            // check imei in warehouse WM04 - คลังโทรศัพท์เคลมโรงงาน
            // $check_imei_in_warehouse = $QImei->checkImeiInWarehouse($imei,6,'imei-factory-rework');

            // check imei in warehouse China Factory Warehouse
            $check_imei_in_warehouse = $QImei->checkImeiInWarehouse($imei,140,'imei-factory-rework');

            if(!$check_imei_in_warehouse){

                $text = '';

                foreach ($imei as $key) {
                
                    if($text == ''){
                        $text = PHP_EOL . $key;
                    }else{
                        $text = $text . PHP_EOL . $key;
                    }
                }

                // echo json_encode(['status' => 401,'message' => 'Not find imei in warehouse [WM04 - คลังโทรศัพท์เคลมโรงงาน]' . $text]);
                echo json_encode(['status' => 401,'message' => 'Not find imei in warehouse [China Factory Warehouse]' . $text]);
                exit();
            }

            $arr_check_imei_in_warehouse = array();

            foreach ($check_imei_in_warehouse as $key) {
                array_push($arr_check_imei_in_warehouse, $key['imei_sn']);
            }

            $imei_error = array_values(array_diff($imei,$arr_check_imei_in_warehouse));

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
                echo json_encode(['status' => 401,'message' => 'Invalid imei in warehouse [China Factory Warehouse]' . $text]);
                exit();

            }

            //WM04 - คลังโทรศัพท์เคลมโรงงาน
            // $warehouse_id_from = 6;

            //China Factory Warehouse
            $warehouse_id_from = 140;

            $current_date = date('Y-m-d H:i:s');
            $by = $userStorage->id;

            // update flag
            $data = array(
                'flag_rework_status' => '1',
                'flag_rework_by' => $by,
                'flag_rework_date' => $current_date
            );

            $whereImei = array();
            $whereImei[] = $QImei->getAdapter()->quoteInto('imei_sn in (?)' , $imei);
            $whereImei[] = $QImei->getAdapter()->quoteInto('warehouse_id = ?' , $warehouse_id_from);

            $QImei->update($data , $whereImei);

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

    $this->view->messages_success   = $messages_success;
    $this->view->messages           = $messages;
