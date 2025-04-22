<?php

    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $messages_success = $flashMessenger->setNamespace('success')->getMessages();

    if ($this->getRequest()->getMethod() == 'POST'){

        define('LIST_ROW_START', 2);

        define('LIST_COL_DISTRIBUTOR', 0);
        define('LIST_COL_WAREHOUSE', 1);
        define('LIST_COL_TYPE', 2);
        define('LIST_COL_CATEGORY', 3);
        define('LIST_COL_GOOD', 4);
        define('LIST_COL_COLOR', 5);
        define('LIST_COL_QUOTA_DATE', 6);
        define('LIST_COL_QUOTA', 7);

	    set_time_limit(0);
        ini_set('memory_limit', -1);
        $db = Zend_Registry::get('db');
        
        $progress = new My_File_Progress('parent.set_progress');
        $progress->flush();

        $upload = new Zend_File_Transfer();

        $uniqid = uniqid('', true);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $part_main = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR;
        $part_sub = 'public' . DIRECTORY_SEPARATOR . 'files'
            . DIRECTORY_SEPARATOR . 'import_quota_distributor'
            . DIRECTORY_SEPARATOR . $userStorage->id . '_' . $uniqid;

        // $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
        //     . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files'
        //     . DIRECTORY_SEPARATOR . 'import_quota_distributor'
        //     . DIRECTORY_SEPARATOR . $userStorage->id . '_' . $uniqid;

        $uploaded_dir = $part_main . $part_sub;

        if (!is_dir($uploaded_dir))
            @mkdir($uploaded_dir, 0777, true);
        
        $upload->setDestination($uploaded_dir);

        $upload->setValidators(array(
            'Size' => array('min' => 50, 'max' => 10000000),
            'Count' => array('min' => 1, 'max' => 1),
            'Extension' => array('xlsx', 'xls'),
        ));

        if (!$upload->isValid()) { // validate IF
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
                        $sError = 'Please choose a file in XLS or XLSX format.';
                        break;
                    case 'fileCountTooFew':
                        $sError = 'Please choose a PO file (in XLS or XLSX format)';
                        break;
                    case 'fileUploadErrorNoFile':
                        $sError = 'Please choose a PO file (in XLS or XLSX format)';
                        break;
                    case 'fileSizeTooBig':
                        $sError = 'File size is too big';
                        break;
                }

            $this->view->error = $sError;

        } else {

            $QNIQD = new Application_Model_NewImportQuotaDistributor();
            $QNQD = new Application_Model_NewQuotaDistributor();
            $QNQDD = new Application_Model_NewQuotaDistributorDetails();

            $data = array(
                'file_name' => $part_sub,
                'file_date' => date('Y-m-d H:i:s'),
                'file_by' => $userStorage->id
            );

            $QNIQD->insert($data);

            $db->beginTransaction();

            try {
                $path_info = pathinfo($upload->getFileName());
                $filename =  $path_info['filename'];
                $extension = $path_info['extension'];

                $old_name = $filename . '.' . $extension;
                $new_name = 'UPLOAD-' . md5($filename . uniqid('', true)) . '.' . $extension;

                if (is_file($uploaded_dir . DIRECTORY_SEPARATOR . $old_name)) {
                    rename($uploaded_dir . DIRECTORY_SEPARATOR . $old_name, $uploaded_dir . DIRECTORY_SEPARATOR . $new_name);
                } else {
                    $new_name = $old_name;
                }

                $upload->addFilter('Rename',
                   array('target' => $uploaded_dir. DIRECTORY_SEPARATOR .$new_name,
                         'overwrite' => true));

                $upload->receive();
                chmod($uploaded_dir. DIRECTORY_SEPARATOR .$new_name, 777);

                require_once 'PHPExcel.php';
                // $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                // $cacheSettings = array('memoryCacheSize' => '8MB');
                // PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                switch ($extension) {
                    case 'xls':
                        $objReader = PHPExcel_IOFactory::createReader('Excel5');
                        break;
                    case 'xlsx':
                        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                        break;
                    default:
                        throw new Exception("Invalid file extension");
                        break;
                }

                $objReader->setReadDataOnly(true);

                $objPHPExcel = $objReader->load($uploaded_dir . DIRECTORY_SEPARATOR . $new_name);

                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

                $highestRow = $objWorksheet->getHighestRow();
                $total_order_row = $highestRow - LIST_ROW_START + 1;

                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

                $array_distributor = array();
                $array_distributor_detail = array();

                for ($i = LIST_ROW_START; $i <= $highestRow; $i++) 
                {

                    $distributor_id = trim($objWorksheet
                        ->getCellByColumnAndRow(LIST_COL_DISTRIBUTOR, $i)
                        ->getValue()); 
                           
                    $warehouse_id = trim($objWorksheet
                        ->getCellByColumnAndRow(LIST_COL_WAREHOUSE, $i)
                        ->getValue());

                    $type = trim($objWorksheet
                        ->getCellByColumnAndRow(LIST_COL_TYPE, $i)
                        ->getValue());

                    $cat_id = trim($objWorksheet
                        ->getCellByColumnAndRow(LIST_COL_CATEGORY, $i)
                        ->getValue());

                    $good_id = trim($objWorksheet
                        ->getCellByColumnAndRow(LIST_COL_GOOD, $i)
                        ->getValue());

                    $good_color_id = trim($objWorksheet
                        ->getCellByColumnAndRow(LIST_COL_COLOR, $i)
                        ->getValue());


                    $quota_date = trim($objWorksheet
                        ->getCellByColumnAndRow(LIST_COL_QUOTA_DATE, $i)
                        ->getValue());

                    $quota = trim($objWorksheet
                        ->getCellByColumnAndRow(LIST_COL_QUOTA, $i)
                        ->getValue());

                    // print_r($distributor_id);echo '|';
                    // print_r($warehouse_id);echo '|';
                    // print_r($type);echo '|';
                    // print_r($cat_id);echo '|';
                    // print_r($good_id);echo '|';
                    // print_r($good_color_id);echo '|';
                    // print_r($quota_date);echo '|';
                    // print_r($quota);echo '|';

                    if($distributor_id == '' && $warehouse_id == '' && $type == '' && $cat_id == '' && $good_id == '' && $good_color_id == '' && $quota_date == '' && $quota == ''){
                        continue;
                    }

                    $where = array();
                    $where[] = $QNQD->getAdapter()->quoteInto('d_id = ?', $distributor_id);
                    $where[] = $QNQD->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                    $where[] = $QNQD->getAdapter()->quoteInto('good_id = ?', $good_id);
                    $where[] = $QNQD->getAdapter()->quoteInto('good_type = ?', $type);
                    $where[] = $QNQD->getAdapter()->quoteInto('quota_date = ?', $quota_date);
                    $data_quota = $QNQD->fetchRow($where);

                    if($data_quota && !in_array($data_quota['id'], $array_distributor)){

                        throw new Exception('Can not upload! (Have Quota Product) => Distributor ID : ' . $distributor_id . ' | Warehouse ID : ' . $warehouse_id . ' | Type : ' . $type . ' | Category : ' . ' | Good ID : ' . $good_id . ' | Good Color ID : ' . $good_color_id . ' | Quota : ' . $quota_date . ' | Quota : ' . $quota);
                    }

                    if($data_quota){

                        $quota_id = $data_quota['id'];

                    }else{

                        $insert_data_quota = array(
                            'd_id' => $distributor_id,
                            'warehouse_id' => $warehouse_id,
                            'good_id' => $good_id,
                            'good_type' => $type,
                            'order_type' => $type,
                            'quota_date' => $quota_date,
                            'created_date' => date('Y-m-d H:i:s'),
                            'created_by' => $userStorage->id,
                            'status' => 1
                        );

                        $quota_id = $QNQD->insert($insert_data_quota);

                        array_push($array_distributor, $quota_id);

                    }

                    $where = array();
                    $where[] = $QNQDD->getAdapter()->quoteInto('nqd_id = ?', $quota_id);
                    $where[] = $QNQDD->getAdapter()->quoteInto('good_color = ?', $good_color_id);
                    $data_quota_detail = $QNQDD->fetchRow($where);

                    if($data_quota_detail){
                        throw new Exception('Can not upload! (Have Quota Product Color On System) => Distributor ID : ' . $distributor_id . ' | Warehouse ID : ' . $warehouse_id . ' | Type : ' . $type . ' | Category : ' . ' | Good ID : ' . $good_id . ' | Good Color ID : ' . $good_color_id . ' | Quota : ' . $quota_date . ' | Quota : ' . $quota);
                    }

                    if(in_array($data_quota_detail['id'], $array_distributor_detail)){
                        throw new Exception('Can not upload! (Have Quota Product Color On Excel) => Distributor ID : ' . $distributor_id . ' | Warehouse ID : ' . $warehouse_id . ' | Type : ' . $type . ' | Category : ' . ' | Good ID : ' . $good_id . ' | Good Color ID : ' . $good_color_id . ' | Quota : ' . $quota_date . ' | Quota : ' . $quota);
                    }

                    if($quota == '-'){
                        $status = 2;
                        $quota = 0;
                    }else{
                        $status = 1;
                    }

                    if($quota < 0){
                        $quota = 0;
                    }

                    $insert_data_quota_detail = array(
                        'nqd_id' => $quota_id,
                        'good_color' => $good_color_id,
                        'num' => $quota,
                        'status' => $status,
                        'created_date' => date('Y-m-d H:i:s'),
                        'created_by' => $userStorage->id
                    );

                    $quota_details_id = $QNQDD->insert($insert_data_quota_detail);

                    array_push($array_distributor_detail, $quota_details_id);
                }

                $progress->flush(100);

                //commit
                $db->commit();

                exit();

            } // end of Try
            catch (Exception $e) {
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
                $progress->flush(0);

                echo "<script type='text/javascript'>parent.location.reload();</script>";
                exit();
            }
        }
    }

    $this->view->messages_success = $messages_success;
    $this->view->messages = $messages;