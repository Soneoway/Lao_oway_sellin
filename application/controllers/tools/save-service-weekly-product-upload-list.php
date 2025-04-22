<?php

    $this->_helper->layout->disableLayout();
    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        
    if ($this->getRequest()->getMethod() == 'POST'){

            //print_r($_POST);die;
            $data_date                = $this->getRequest()->getParam('data_date');

            define('ROW_START', 3);
            define('good_code', 0);
            define('good_model', 2);
            define('good_name_chinese', 3);
            define('good_name_eng', 4);
            define('import_price_usd', 5);
            define('import_price_bath', 6);
            define('retail_price_bath', 7);

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
                        $QServiceProductList = new Application_Model_ServiceWeeklyProductList();

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
                        $date = DateTime::createFromFormat('d/m/Y', $data_date);
                        $data_date1 = $date->format('Y-m-d');

                        //$where = $QServiceProductList->getAdapter()->quoteInto('data_date = ?', $data_date1);
                        $where = $QServiceProductList->getAdapter()->quoteInto('1 = 1', $data_date1);
                        $code_check = $QServiceProductList->fetchRow($where);
                        if ($code_check) {
                            $result_del=$QServiceProductList->delete($where);
                        }

                        for ($i = ROW_START; $i <= $highestRow; $i++) 
                        {

                            $good_code = trim($objWorksheet
                                ->getCellByColumnAndRow(good_code, $i)
                                ->getValue()); 
                            
                            $good_model = trim($objWorksheet
                                ->getCellByColumnAndRow(good_model, $i)
                                ->getValue()); 

                            $good_name_chinese = trim($objWorksheet
                                ->getCellByColumnAndRow(good_name_chinese, $i)
                                ->getValue()); 

                            $good_name_eng = trim($objWorksheet
                                ->getCellByColumnAndRow(good_name_eng, $i)
                                ->getValue()); 

                            $import_price_usd = trim($objWorksheet
                                ->getCellByColumnAndRow(import_price_usd, $i)
                                ->getValue()); 

                            $import_price_bath = trim($objWorksheet
                                ->getCellByColumnAndRow(import_price_bath, $i)
                                ->getValue()); 

                            $retail_price_bath = trim($objWorksheet
                                ->getCellByColumnAndRow(retail_price_bath, $i)
                                ->getValue()); 
                          

                            if($import_price_usd=='' || $import_price_usd=='FREE'){$import_price_usd=0;}
                            if($import_price_bath=='' || $import_price_bath=='FREE'){$import_price_bath=0;}
                            if($retail_price_bath=='' || $retail_price_bath=='FREE' || $retail_price_bath=='Free'){$retail_price_bath=0;}
                           
                            /*------------------Start------------------------*/
                            if($good_code !=""){
                                
                                $params = array(
                                    'data_date'                 => trim($data_date1),
                                    'good_code'                 => trim($good_code),
                                    'good_model'                => trim($good_model),
                                    'good_name_chinese'         => trim($good_name_chinese),
                                    'good_name_eng'             => trim($good_name_eng),
                                    'import_price_usd'          => trim($import_price_usd),
                                    'import_price_bath'         => trim($import_price_bath),
                                    'retail_price_bath'         => trim($retail_price_bath),
                                    'created_by'                => $userStorage->id,
                                    'created_date'              => $create_date,
                                    'status'                    => 1
                                );

                                //print_r($params);//die;
                                //echo "<pre>";
                                $result=$QServiceProductList->insert($params); 
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
                                $percent = round($number_of_order * 1000 / $total_order_row, 1);

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


                            $objWorksheet_out->setCellValueByColumnAndRow(good_code, 1, 'Good Code');
                            $objWorksheet_out->setCellValueByColumnAndRow(good_model, 1, 'Good Replacement Code');
                            $objWorksheet_out->setCellValueByColumnAndRow(good_name_chinese, 1, 'good_name_chinese');
                            $objWorksheet_out->setCellValueByColumnAndRow(good_name_eng, 1, 'good_name_eng');
                            $objWorksheet_out->setCellValueByColumnAndRow(import_price_usd, 1, 'import_price_usd');
                            $objWorksheet_out->setCellValueByColumnAndRow(import_price_bath, 1, 'import_price_bath');
                            $objWorksheet_out->setCellValueByColumnAndRow(retail_price_bath + 1, 1, 'Retail Price');

                            $i = 2;
                            foreach ($error_list as $key => $row)
                            {
                                $objWorksheet_out->setCellValueByColumnAndRow(good_code, $i, $row['good_code']);
                                $objWorksheet_out->setCellValueByColumnAndRow(good_model, $i, $row['good_model']);
                                $objWorksheet_out->setCellValueByColumnAndRow(good_name_chinese, $i, $row['good_name_chinese']);
                                $objWorksheet_out->setCellValueByColumnAndRow(good_name_eng, $i, $row['good_name_eng']);
                                $objWorksheet_out->setCellValueByColumnAndRow(import_price_usd, $i, $row['import_price_usd']);
                                $objWorksheet_out->setCellValueByColumnAndRow(import_price_bath, $i, $row['import_price_bath']);
                                $objWorksheet_out->setCellValueByColumnAndRow(retail_price_bath +1, $i, $row['retail_price_bath']);
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

    
    

