<?php

$this->_helper->layout->disableLayout();

$QFileLog        = new Application_Model_FileUploadLog();
$QOppoGreenAll   = new Application_Model_OppoAllGreenRewardCn();
$QCreditNote   = new Application_Model_CreditNote();

$generateCN         = $_POST["generateCN"];
$activeCN           = $_POST["activeCN"];
$key_sn = date('YmdHis') . substr(microtime(), 2, 1);

    if ($this->getRequest()->getMethod() == 'POST') {

        define('MASS_BVG_LIST_ROW_START', 2);
        define('MASS_BVG_LIST_COL_ROUND_NO', 0);
        define('MASS_BVG_LIST_COL_ROUND_YEAR', 1);
        define('MASS_BVG_LIST_COL_AIR_NUMBER', 2);
        define('MASS_BVG_LIST_COL_DISTRIBUTOR_ID', 3);
        define('MASS_BVG_LIST_COL_DISTRIBUTOR_NAME', 4);
        define('MASS_BVG_LIST_COL_STORE_ID', 5);
        define('MASS_BVG_LIST_COL_STORE_NAME', 6);
        define('MASS_BVG_LIST_COL_START_DATE', 7);
        define('MASS_BVG_LIST_COL_END_DATE', 8);
        define('MASS_BVG_LIST_COL_SHOP_TYPE', 9);
        define('MASS_BVG_LIST_COL_TOTAL_REWARD_PRICE', 10);
        define('MASS_BVG_LIST_COL_TAX_PRICE', 11);
        define('MASS_BVG_LIST_COL_CREDITNOTE_PRICE', 12);
        define('MASS_BVG_LIST_COL_ASM_CONFIRM_BY', 13);
        define('MASS_BVG_LIST_COL_ASM_CONFIRM_DATE', 14);
        define('MASS_BVG_LIST_COL_STATUS_CN', 15);
        define('MASS_BVG_LIST_COL_REASON_REMARK', 16);
        



        set_time_limit(0);
        ini_set('memory_limit', -1);
        $db = Zend_Registry::get('db');
        
        $progress = new My_File_Progress('parent.set_progress');
        $progress->flush(0);

        $upload = new Zend_File_Transfer();

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

        }else {
            try{
                    $db->beginTransaction();          
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


                    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                    $data = array(
                        'staff_id' => $userStorage->id,
                        'folder' => $uniqid,
                        'filename' => $new_name,
                        'type' => 'OPPO Green All',
                        'real_file_name' => $filename . '.' . $extension,
                        'uploaded_at' => time(),
                    );

                    $log_id = $QFileLog->insert($data);

                    $number_of_order = 0;
                    $error_list = array();
                    $success_list = array();
                    $listOppoGreenAll = array();

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
                    $total_order_row = $highestRow - MASS_BVG_LIST_ROW_START + 1;

                    $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
                    $status=1; $d_id='';   
                                       
                    $data_sn=null;
                    $sn = date('YmdHis') . substr(microtime(), 2, 1);
                    for ($i = MASS_BVG_LIST_ROW_START; $i <= $highestRow; $i++) {

                    	$round_no = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_ROUND_NO, $i)
                            ->getValue());

                        $round_year = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_ROUND_YEAR, $i)
                            ->getValue());

                        $air_number = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_AIR_NUMBER, $i)
                            ->getValue());

                        $distributor_id = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_DISTRIBUTOR_ID, $i)
                            ->getValue());                      

                        $distributor_name = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_DISTRIBUTOR_NAME, $i)
                            ->getValue());

                        $store_id = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_STORE_ID, $i)
                            ->getValue());

                        $store_name = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_STORE_NAME, $i)
                            ->getValue());

                        $start_date = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_START_DATE, $i)
                            ->getValue());
                        $start_date = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($start_date));

                        $end_date = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_END_DATE, $i)
                            ->getValue());
                        $end_date = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($end_date));

                        $shop_type = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_SHOP_TYPE, $i)
                            ->getValue());

                        $total_reward_price = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_TOTAL_REWARD_PRICE, $i)
                            ->getValue());

                        $tax_price = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_TAX_PRICE, $i)
                            ->getValue());
                        
                        $creditnote_price = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_CREDITNOTE_PRICE, $i)
                            ->getValue());
                        
                        $asm_confirm_by = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_ASM_CONFIRM_BY, $i)
                            ->getValue());
                        
                        $asm_confirm_date = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_ASM_CONFIRM_DATE, $i)
                            ->getValue());
                        $asm_confirm_date = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($asm_confirm_date));

                        $confirm_by = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_COnFIRM_BY, $i)
                            ->getValue());

                        $confirm_date = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_COnFIRM_DATE, $i)
                            ->getValue());
                        $confirm_date = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($confirm_date));

                        $status_cn = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_STATUS_CN, $i)
                            ->getValue());
                        
                        $reason_remark = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_REASON_REMARK, $i)
                            ->getValue());
                        
                        $checkAirNumber = substr($air_number,0,1);
                        if ($checkAirNumber != "'")  $air_number = "'".$air_number;
                        
                        $data_row=[];
                        $data_row["round_no"] 				  =   $round_no;
                        $data_row["round_year"] 			  =   $round_year;
                        $data_row["air_number"]				  =	  $air_number ;
                        $data_row["distributor_id"]			  =   $distributor_id;
                        $data_row["distributor_name"] 		  =   $distributor_name;
                        $data_row["store_id"] 				  =   $store_id;
                        $data_row["store_name"] 			  =   $store_name;
                        $data_row["key_sn"] 				  =   $key_sn;
                        $data_row["start_date"] 			  =   $start_date;
                        $data_row["end_date"] 				  =   $end_date;
                        $data_row["shop_type"] 				  =   $shop_type;
                        $data_row["total_reward_price"] 	  =   $total_reward_price;
                        $data_row["tax_price"] 				  =   $tax_price;
                        $data_row["creditnote_price"]         =   $creditnote_price;
                        $data_row["asm_confirm_by"] 		  =   $asm_confirm_by;
                        $data_row["asm_confirm_date"] 		  =   $asm_confirm_date;
                        $data_row["confirm_by"] 			  =   $userStorage->id;
                        $data_row["confirm_date"] 			  =   date('Y-m-d H:i:s');
                        $data_row["status_cn"] 				  =   $status_cn;
                        $data_row["reason_remark"] 			  =   $reason_remark;
                        $data_row["create_date"]              =   date('Y-m-d H:i:s');

                        $data_row_keys = (array_keys($data_row));
                        foreach ($data_row_keys as  $key) {
                        	// echo $key ."=".$data_row[$key].'<br>';
                        	if(!$data_row[$key])
                        	{
                        		$data_row[$key]=null;
                        	}
                        }

                        if($data_row)
                        {
                        	$findDuplicate = null;
                        	$findDuplicate = $QOppoGreenAll->Duplicate($data_row["air_number"],$data_row["distributor_id"],$data_row["store_id"] );

                        	if ($findDuplicate) {
                        		array_push($error_list,$data_row);
                        	}
                        	else {
                        		array_push($success_list,$data_row);
                        		$insertData = $QOppoGreenAll->insert($data_row);
                        	}
                        	$number_of_order++;
                        }
                    }

                    $data = array(
                        'total' => $total_order_row,
                        'failed' => count($error_list),
                        'succeed' => $total_order_row - count($error_list),
                    );

                    // xuất file excel các order lỗi
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
                        //
                        // get product list
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ROUND_NO, 1, 'Round_No');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ROUND_YEAR, 1, 'Round_Year');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_AIR_NUMBER, 1, 'Air_number');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DISTRIBUTOR_ID, 1, 'Distributor_id');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DISTRIBUTOR_NAME, 1, 'Distributor_Name');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_STORE_ID, 1, 'Store_id');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_STORE_NAME, 1, 'Store_Name');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_KEY_SN, 1, 'Key_sn');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_START_DATE, 1, 'Start_date');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_END_DATE, 1, 'End_date');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_SHOP_TYPE, 1, 'Shop_type');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_TOTAL_REWARD_PRICE, 1, 'Total_reward_price');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_TAX_PRICE, 1, 'Tax_price');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CREDITNOTE_PRICE, 1, 'Creditnote_price');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ASM_CONFIRM_BY, 1, 'Asm_confirm_by');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ASM_CONFIRM_DATE, 1, 'Asm_confirm_date');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CONFIRM_BY, 1, 'Confirm_by');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CONFIRM_DATE, 1, 'Confirm_date');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_STATUS_CN, 1, 'Status_cn');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_REASON_REMARK, 1, 'Reason_remark');
                        


                        // các dòng lỗi
                        $i = 2;

                        foreach ($error_list as $key => $row) {
                         //    $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DEALER, $i, $row['d_id']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ROUND_NO, $i, $row['round_no']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ROUND_YEAR, $i, $row['round_year']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_AIR_NUMBER, $i, $row['air_number']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DISTRIBUTOR_ID, $i, $row['distributor_id']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DISTRIBUTOR_NAME, $i, $row['distributor_name']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_STORE_ID, $i, $row['store_id']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_STORE_NAME, $i, $row['store_name']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_KEY_SN, $i, $row['key_sn']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_START_DATE, $i, $row['start_date']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_END_DATE, $i, $row['end_date']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_SHOP_TYPE, $i, $row['shop_type']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_TOTAL_REWARD_PRICE, $i, $row['total_reward_price']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_TAX_PRICE, $i, $row['tax_price']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CREDITNOTE_PRICE, $i, $row['creditnote_price']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ASM_CONFIRM_BY, $i, $row['asm_confirm_by']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ASM_CONFIRM_DATE, $i, $row['asm_confirm_date']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CONFIRM_BY, $i, $row['confirm_by']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CONFIRM_DATE, $i, $row['confirm_date']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_STATUS_CN, $i, $row['status_cn']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_REASON_REMARK, $i, $row['reason_remark']);
                            $i++;
                        }

                        //
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

                        if($generateCN)
                        {
                            $countSuccess =count($success_list);
                            for ($i = 0; $i <= $countSuccess; $i++) 
                            {
                                if($success_list[$i]['air_number'])
                                {
                                   $air_number = $success_list[$i]['air_number'];
                                   $air_number = trim($air_number,"'");
                                   $creditnote_sn = $this->getCreditNoteNo_Ref($db,$air_number);

                                   $data_cn= [];
                                   $data_cn['creditnote_sn']      = $creditnote_sn  ;
                                   $data_cn['distributor_id']     = $success_list[$i]['distributor_id'];
                                   $data_cn['total_amount']       = $success_list[$i]['creditnote_price'];
                                   $data_cn['use_total']          = 0;
                                   $data_cn['balance_total']      = $success_list[$i]['creditnote_price'];
                                   $data_cn['status']             = $activeCN;
                                   $data_cn['create_date']        = date('Y-m-d H:i:s');
                                   $data_cn['create_by']          = $userStorage->id;
                                   $data_cn['update_date']        = date('Y-m-d H:i:s');
                                   $data_cn['update_by']          = $userStorage->id;
                                   $data_cn['creditnote_type']    = 'CN';
                                   $data_cn['sn']                 = $air_number;
                                   $data_cn['remark']             = $success_list[$i]['remark'];
                                   $data_cn['chanel']             = 'oppo_all_green';

                                   $QCreditNote->insert($data_cn);
                                   $whereOppoGreenAll = $QOppoGreenAll->getAdapter()->quoteInto('air_number = ?', $success_list[$i]['air_number']);
                                   $data_green['creditnote_sn']  = $creditnote_sn;
                                   $data_green['creditnote_price_confirm']  = $success_list[$i]['creditnote_price'];
                                   $QOppoGreenAll->update($data_green, $whereOppoGreenAll);
                                }
                            }
                        }
                    // END IF // xuất file excel các order lỗi

                    $data['success_file_name'] = $key_sn;
                    $where = $QFileLog->getAdapter()->quoteInto('id = ?', $log_id);
                    $QFileLog->update($data, $where);

                    $this->view->success_list = $success_list;
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


            }
            catch (Exception $e) {
                    $db->rollback();
                    $this->view->error = $e->getMessage();
                    $progress->flush(0);
            }// end of check file
        }

        

    }// end of check POST

?>
