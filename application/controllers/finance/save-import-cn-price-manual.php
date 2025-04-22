<?php

    $this->_helper->layout->disableLayout();
    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        
    if ($this->getRequest()->getMethod() == 'POST'){

            //print_r($_POST);die;

            define('ROW_START', 2);
            define('DISTRIBUTOR_ID', 0);
            define('STORE_CODE', 1);
            define('DISTRIBUTOR_NAME', 2);
            define('TOTAL_CN_EXVAT', 3);
            define('VAT_PER', 4);
            define('WHT_PER', 5);
            define('WHT_PRICE', 6);
            define('TOTAL_INCN', 7);
            define('ACTIVE', 8);
            define('FINANCE_CONFIRM', 9);
            define('CHANEL', 10);
            define('REMARK', 11);
            

            set_time_limit(0);
            ini_set('memory_limit', -1);
            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            $progress = new My_File_Progress('parent.set_progress');
            $progress->flush(0);

            $upload = new Zend_File_Transfer();

            $QDistributor = new Application_Model_Distributor();

            $uniqid = uniqid('', true);
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files'
                . DIRECTORY_SEPARATOR . 'mou'
                . DIRECTORY_SEPARATOR . $userStorage->id
                . DIRECTORY_SEPARATOR . $uniqid;

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
                try {
                        $QCreditNote = new Application_Model_CreditNote();

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

                        $number_of_order = 0;
                        $error_list = array();
                        $success_list = array();
                        $listBvgByProduct = array();

                        require_once 'PHPExcel.php';
                        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                        $cacheSettings = array('memoryCacheSize' => '8MB');
                        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

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

                        $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
                        $total_order_row = $highestRow - ROW_START + 1;

                        $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
                        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

                        
                        $create_date = date('Y-m-d H:i:s');
                        $CN_date = date('Y-m-d');
                        //$date = DateTime::createFromFormat('d/m/Y', $data_date);
                        //$data_date1 = $date->format('Y-m-d');
                        //$chk_create_date = date('Y-m-d');

                        $sn = date('YmdHis') . substr(microtime(), 2, 4);

                        //print_r($sn);die;
                        for ($i = ROW_START; $i <= $highestRow; $i++) 
                        {

                            $DISTRIBUTOR_ID = trim($objWorksheet
                                ->getCellByColumnAndRow(DISTRIBUTOR_ID, $i)
                                ->getValue()); 
                            
                            $TOTAL_CN_EXVAT = trim($objWorksheet
                                ->getCellByColumnAndRow(TOTAL_CN_EXVAT, $i)
                                ->getValue()); 

                            $VAT_PER = trim($objWorksheet
                                ->getCellByColumnAndRow(VAT_PER, $i)
                                ->getValue()); 

                            $WHT_PER = trim($objWorksheet
                                ->getCellByColumnAndRow(WHT_PER, $i)
                                ->getValue()); 

                            $WHT_PRICE = trim($objWorksheet
                                ->getCellByColumnAndRow(WHT_PRICE, $i)
                                ->getValue()); 

                            $TOTAL_INCN = trim($objWorksheet
                                ->getCellByColumnAndRow(TOTAL_INCN, $i)
                                ->getValue()); 

                            $ACTIVE = trim($objWorksheet
                                ->getCellByColumnAndRow(ACTIVE, $i)
                                ->getValue()); 

                            $FINANCE_CONFIRM = trim($objWorksheet
                                ->getCellByColumnAndRow(FINANCE_CONFIRM, $i)
                                ->getValue()); 

                            $CHANEL = trim($objWorksheet
                                ->getCellByColumnAndRow(CHANEL, $i)
                                ->getValue()); 

                            $REMARK = trim($objWorksheet
                                ->getCellByColumnAndRow(REMARK, $i)
                                ->getValue()); 
                          
                            
                            /*------------------Start------------------------*/
                            // print_r($data);die;     
                            if($sn !=""){
                                
                                switch($CHANEL) {
                                    case 'ส่งเสริมการขาย OPPO Club':
                                        $chanel_show = "reward";
                                        break;
                                    case 'ส่งเสริมการขาย':
                                        $chanel_show = "promotion";
                                    break;    
                                    case 'Incentive':
                                        $chanel_show = "incentive";
                                        break;
                                    case 'ค่าตกแต่งหน้าร้าน':
                                        $chanel_show = "decoration";
                                        break;
                                    case 'แก้ไขราคา':
                                        $chanel_show = "price";
                                        break;    
                                    case 'OPPO All Green':
                                        $chanel_show = "oppo_all_green";
                                        break;    
                                    case 'OPPO Top Green':
                                        $chanel_show = "top_green";
                                        break; 
                                    case 'Live Demo':
                                        $chanel_show = "live_demo";
                                        break; 
                                    case 'ค่าเช่า':
                                        $chanel_show = "rent";
                                        break;                          
                                    default:
                                        $chanel_show = "";
                                }

                                $creditnote_sn = $QCreditNote->getReward_CreateNoteNo_Ref($db,$DISTRIBUTOR_ID,$userStorage->id,$sn);
                                
                                $data = array(
                                    'distributor_id' => $DISTRIBUTOR_ID,
                                    'create_by' => $userStorage->id,
                                    'create_date' => $CN_date,
                                    'creditnote_type' => "CN",
                                    'chanel' => $chanel_show,
                                    'price_ext_vat' => $TOTAL_CN_EXVAT,
                                    'total_amount' => $TOTAL_INCN,
                                    'use_total' => 0,
                                    'balance_total' => $TOTAL_INCN,
                                    'status' => $ACTIVE,
                                    'creditnote_sn' => $creditnote_sn,
                                    'manual' => 1,
                                    'manual_active' => $ACTIVE,
                                    'vat' => $VAT_PER,
                                    'wht_vat' => $WHT_PER,
                                    'wht_price' => $WHT_PRICE,
                                    'manual_remark' => $REMARK,
                                    'sn' => $sn
                                );

                                if($FINANCE_CONFIRM=="1"){
                                   $data['confirm_by']=$userStorage->id;
                                   $data['confirm_date']=$create_date;
                                }


                                //print_r($data);die;
                                $result = $QCreditNote->insert($data); 
                                //
                                //print_r($result);die;
                                if ($result != '') { //success           
                                    $success_list[] = $params;  
                                    $flashMessenger->setNamespace('success')->addMessage($result['message']);
                                } else {
                                    $error_list[] = $params;
                                    $flashMessenger->setNamespace('error')->addMessage($result['message']);
                                }


                                /*------------------End------------------------*/

                                $number_of_order++;
                                $percent = round($number_of_order * 100 / $total_order_row, 1);

                                $data = array(
                                    'total' => $total_order_row,
                                    'failed' => count($error_list),
                                    'succeed' => $total_order_row - count($error_list),
                                );

                                $progress->flush($percent);
                            }
                        }

                        if (is_array($error_list) && count($error_list) > 0) 
                        {
                            
                            $data['error_file_name'] = $d_id.'-'.'FAILED-' . md5(microtime(true) . uniqid('', true)) . '.' . $extension;
                            // xuất excel @@
                            //
                            //$error_file_name = date('YmdHis') . substr(microtime(), 2, 4);
                            //$data['error_file_name'] = 'FAILED-' .$error_file_name.'.' . $extension;

                            $objPHPExcel_out = new PHPExcel();
                            $objPHPExcel_out->createSheet();
                            $objWorksheet_out = $objPHPExcel_out->getActiveSheet();

                            $objWorksheet_out->setCellValueByColumnAndRow(DISTRIBUTOR_ID, 1, 'DISTRIBUTOR_ID');
                            $objWorksheet_out->setCellValueByColumnAndRow(TOTAL_CN_EXVAT, 1, 'TOTAL_CN_EXVAT');
                            $objWorksheet_out->setCellValueByColumnAndRow(VAT_PER, 1, 'VAT_PER');
                            $objWorksheet_out->setCellValueByColumnAndRow(WHT_PER, 1, 'WHT_PER');
                            $objWorksheet_out->setCellValueByColumnAndRow(WHT_PRICE, 1, 'WHT_PRICE');
                            $objWorksheet_out->setCellValueByColumnAndRow(TOTAL_INCN, 1, 'TOTAL_INCN');
                            $objWorksheet_out->setCellValueByColumnAndRow(ACTIVE, 1, 'ACTIVE');
                            $objWorksheet_out->setCellValueByColumnAndRow(CHANEL, 1, 'CHANEL');
                            $objWorksheet_out->setCellValueByColumnAndRow(REMARK + 1, 1, 'REMARK');

                            $i = 2;
                            foreach ($error_list as $key => $row)
                            {
                                $objWorksheet_out->setCellValueByColumnAndRow(DISTRIBUTOR_ID, $i, $row['DISTRIBUTOR_ID']);
                                $objWorksheet_out->setCellValueByColumnAndRow(TOTAL_CN_EXVAT, $i, $row['TOTAL_CN_EXVAT']);
                                $objWorksheet_out->setCellValueByColumnAndRow(VAT_PER, $i, $row['VAT_PER']);
                                $objWorksheet_out->setCellValueByColumnAndRow(WHT_PER, $i, $row['WHT_PER']);
                                $objWorksheet_out->setCellValueByColumnAndRow(WHT_PRICE, $i, $row['WHT_PRICE']);
                                $objWorksheet_out->setCellValueByColumnAndRow(TOTAL_INCN, $i, $row['TOTAL_INCN']);
                                $objWorksheet_out->setCellValueByColumnAndRow(ACTIVE, $i, $row['ACTIVE']);
                                $objWorksheet_out->setCellValueByColumnAndRow(CHANEL, $i, $row['CHANEL']);
                                $objWorksheet_out->setCellValueByColumnAndRow(REMARK +1, $i, $row['REMARK']);
                                $i++;
                            }
                            
                            switch ($extension) {
                                case 'xls':
                                    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel_out);
                                    break;
                                case 'xlsx':
                                    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel_out);
                                    break;
                                default:
                                    throw new Exception("Invalid file extension");
                                    break;
                            }

                            $new_file_dir = $uploaded_dir . DIRECTORY_SEPARATOR . $data['error_file_name'];

                            //Tanong
                            $objWriter->save($new_file_dir);
                        }
                        // END IF // xuất file excel các order lỗi
                        $QFileLog = new Application_Model_FileUploadLog();

                        $where = $QFileLog->getAdapter()->quoteInto('id = ?', $log_id);
                        $QFileLog->update($data, $where);

                        $this->view->error_list = $error_list;
                        $this->view->objWorksheet = $objWorksheet;
                        $this->view->number_of_order = $number_of_order;

                        //commit
                        $db->commit();

                        $this->view->error_file = isset($data['error_file_name']) ? (HOST
                            . 'files'
                            . DIRECTORY_SEPARATOR . 'mou'
                            . DIRECTORY_SEPARATOR . $userStorage->id
                            . DIRECTORY_SEPARATOR . $uniqid
                            . DIRECTORY_SEPARATOR . $data['error_file_name']) : false;

                        $progress->flush(100);

                } // end of Try
                catch (Exception $e) {
                    $db->rollback();
                    $this->view->error = $e->getMessage();
                    $progress->flush(0);
                }
                // $this->_redirect( HOST.'sales/save-manual' );

        }
    
    }
    
    //$this->_redirect( HOST.'sales' );

    
    

