<?php

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $flashMessenger = $this->_helper->flashMessenger;

    if ($this->getRequest()->getMethod() == 'POST') {

        $old_imei = $this->getRequest()->getParam('old_imei');
        $new_imei = $this->getRequest()->getParam('new_imei');
        $warehouse = $this->getRequest()->getParam('warehouse');
        $job_number = $this->getRequest()->getParam('job_number');
        $remark = $this->getRequest()->getParam('remark');

        if(!$old_imei){
            $flashMessenger->setNamespace('error')->addMessage('Please input old imei');
            $this->_redirect(HOST."warehouse/factory-claim-request");
        }

        if(!$new_imei){
            $flashMessenger->setNamespace('error')->addMessage('Please input new imei');
            $this->_redirect(HOST."warehouse/factory-claim-request");
        }

        if(!$warehouse){
            $flashMessenger->setNamespace('error')->addMessage('Please select warehouse');
            $this->_redirect(HOST."warehouse/factory-claim-request");
        }

        if(!$job_number){
            $flashMessenger->setNamespace('error')->addMessage('Please input job number');
            $this->_redirect(HOST."warehouse/factory-claim-request");
        }

        if(!$remark){
            $flashMessenger->setNamespace('error')->addMessage('Please input remark');
            $this->_redirect(HOST."warehouse/factory-claim-request");
        }

        $QFC = new Application_Model_FactoryClaim();
        $check_job_number = $QFC->getJobNumber($job_number);

        if($check_job_number){
            $flashMessenger->setNamespace('error')->addMessage('Duplicate Job Number');
            $this->_redirect(HOST."warehouse/factory-claim-request");
        }

        $checklist_old_imei = $QFC->checklistOldImei($old_imei);

        if($checklist_old_imei){
            $flashMessenger->setNamespace('error')->addMessage('Duplicate Old IMEI');
            $this->_redirect(HOST."warehouse/factory-claim-request");
        }

        $checklist_new_imei = $QFC->checklistNewImei($new_imei);

        if($checklist_new_imei){
            $flashMessenger->setNamespace('error')->addMessage('Duplicate New IMEI');
            $this->_redirect(HOST."warehouse/factory-claim-request");
        }

        $QI = new Application_Model_Imei();
        $check_old_imei = $QI->checkImeiSold($old_imei);
        $check_new_imei = $QI->checkImeiReady($new_imei);

        if(!$check_old_imei){
            $flashMessenger->setNamespace('error')->addMessage('Invalid old imei');
            $this->_redirect(HOST."warehouse/factory-claim-request");
        }

        if(!$check_new_imei){
            $flashMessenger->setNamespace('error')->addMessage('Invalid new imei');
            $this->_redirect(HOST."warehouse/factory-claim-request");
        }

        if($check_new_imei['warehouse_id'] != $warehouse){
            $flashMessenger->setNamespace('error')->addMessage('Invalid warehouse');
            $this->_redirect(HOST."warehouse/factory-claim-request");
        }

        /*-------------------File Pay Slip Upload--------------------------*/
        $upload    = new Zend_File_Transfer_Adapter_Http();
        $files  = $upload->getFileInfo();
        $count_img = 1;

        $part_uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
                    . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files'
                    . DIRECTORY_SEPARATOR . 'warehouse'. DIRECTORY_SEPARATOR . 'factory_claim' . DIRECTORY_SEPARATOR;

        $file_name = $userStorage->id . '_' . time() . DIRECTORY_SEPARATOR;

        $img_id_card = '';
        $img_broken = '';

        foreach($files as $file => $fileInfo)
        {

            switch ($count_img) {
                case 1:
                    $uploaded_dir = $part_uploaded_dir . 'id_card_' . $file_name;
                    $img_id_card = 'id_card_' . $file_name . $fileInfo['name'];
                    break;
                case 2:
                    $uploaded_dir = $part_uploaded_dir . 'broken_' . $file_name;
                    $img_broken = 'broken_' . $file_name . $fileInfo['name'];
                    break;
            }

            if($upload->isUploaded($file))
            {

                if (!is_dir($uploaded_dir))
                    @mkdir($uploaded_dir, 0777, true);

                
                $upload->setDestination($uploaded_dir);

                // Upload Max 5 MB
                $upload->setValidators(array(
                    'Size' => array('min' => 50, 'max' => 2000000),
                    'Count' => array('min' => 1, 'max' => 3),
                    'Extension' => array('jpg','jpeg','png','gif')
                ));

                if (!$upload->isValid($file)) { // validate IF
                $errors = $upload->getErrors();
                $sError = null;
                    if ($errors and isset($errors[0]))
                    switch ($errors[0]) {
                        case 'fileUploadErrorIniSize':
                            $sError = 'File size is too large';
                            break;
                        case 'fileMimeTypeFalse':
                            $sError = 'The file you selected weren\'t the type we were expecting';
                            break;
                        case 'fileExtensionFalse':
                            $sError = 'Please choose a file in JPG or PNG format.';
                            break;
                        case 'fileCountTooFew':
                            $sError = 'Please choose a PO file (in JPG or PNG format)';
                            break;
                        case 'fileUploadErrorNoFile':
                            $sError = 'Please choose a PO file (in JPG or PNG format)';
                            break;
                        case 'fileSizeTooBig':
                            $sError = 'File size is too big';
                            break;
                    }

                    if($sError!=''){
                        $flashMessenger->setNamespace('error')->addMessage($sError);
                        $this->_redirect(HOST."warehouse/factory-claim-request");
                    }
                }else{
                   $upload->receive($file);
                }                                                     
            }
            $count_img++;
        }
        /*-------------------End File Pay Slip Upload--------------------------*/

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

    $QWG = new Application_Model_WarehouseGroupUser();

    $QWarehouse = new Application_Model_Warehouse();
    $warehouses = $QWarehouse->get_cache();

    $get_warehouse = $QWG->currentWarehouseGroupUserList($userStorage->id);

    $this->view->get_warehouse = $get_warehouse;

    $this->view->warehouse_all = $warehouses;

    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;