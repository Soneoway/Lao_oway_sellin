<?php

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $flashMessenger = $this->_helper->flashMessenger;

    if ($this->getRequest()->getMethod() == 'POST') {

        $imei = $this->getRequest()->getParam('temp_imei');

        if(!$imei){
            $flashMessenger->setNamespace('error')->addMessage('Error! Invalid Data');
            $this->_redirect(HOST."warehouse/manage-lotus");
        }

        $imei_list = trim($imei);
        $imei_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $imei_list);
        $imei_list = explode("\n", $imei_list);
        $imei_list = array_filter($imei_list);

        $check_imei_duplicate = array_unique($imei_list);

        if(count($imei_list) != count($check_imei_duplicate)){

            $array_imei_duplicate = array();

            foreach ($imei_list as $key => $value) {
                if(!isset($check_imei_duplicate[$key])){
                    array_push($array_imei_duplicate, ['imei' => $value,'good_model' => '-', 'good_name' => '-', 'good_color' => '-']);
                }
            }

            $flashMessenger->setNamespace('error')->addMessage('Error! Duplicate IMEI');
            $this->_redirect(HOST."warehouse/manage-lotus");

            echo json_encode(['status' => 400,'message' => 'Duplicate IMEI','valid' => [], 'invalid' => $array_imei_duplicate]);
                exit();
        }

        $QI = new Application_Model_Imei();

        $check_imei_list = $QI->checkImeiSoldAndNotiming($imei_list);

        $array_invalid = array();
        $array_valid = array();

        foreach ($imei_list as $key => $value) {
            $count_valid = 0;
            foreach ($check_imei_list as $key_sub => $value_sub) {

                if($value == $value_sub['imei_sn']){
                    array_push($array_valid, ['imei' => $value_sub['imei_sn'],'good_model' => $value_sub['good_model'],'good_name' => $value_sub['good_name'],'good_color' => $value_sub['good_color']]);

                    $count_valid++;
                }

            }
            if(!$count_valid){
                array_push($array_invalid, ['imei' => $value,'good_model' => '-', 'good_name' => '-', 'good_color' => '-']);
            }
        }

        if(count($imei_list) != count($check_imei_list)){
            echo json_encode(['status' => 400,'message' => 'Invalid Data','valid' => $array_valid, 'invalid' => $array_invalid]);
            exit();
        }

        echo json_encode(['status' => 200,'message' => 'Done','valid' => $array_valid]);
                exit();


        die;

        $old_imei = $this->getRequest()->getParam('old_imei');

        if(!$old_imei){
            $flashMessenger->setNamespace('error')->addMessage('Please input old imei');
            $this->_redirect(HOST."warehouse/factory-claim-request");
        }

        try{

            $db = Zend_Registry::get('db');
            $db->beginTransaction();

                $data = array(
                    'job_number' => $job_number,
                    'old_imei' => $old_imei,
                    'new_imei' => $new_imei,
                    'warehouse' => $warehouse,
                    'remark' => $remark,
                    'img_id_card' => $img_id_card,
                    'img_broken' => $img_broken,
                    'status' => 1,
                    'created_date' => date('Y-m-d H:i:s'),
                    'created_by' => $userStorage->id
                );

                $QFC->insert($data);

            $db->commit();

            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $this->_redirect(HOST."warehouse/factory-claim-list");

        } catch (Exception $e){
            $db->rollback();

            $flashMessenger->setNamespace('error')->addMessage('Failed - '.$e->getMessage());
            $this->_redirect(HOST."warehouse/factory-claim-request");
        }

    }


    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;