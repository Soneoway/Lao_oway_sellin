<?php

$this->_helper->layout->disableLayout();

//print_r($_POST);die;

$QFileLog        = new Application_Model_FileUploadLog();
$QQuota             = new Application_Model_Quota();
$cat_id           = $_POST["cat_id"];
$good_id          = $_POST["good_id"];
$good_color       = $_POST["good_color"];
$date_quota       = $_POST["date_quota"];

$key_sn = date('YmdHis') . substr(microtime(), 2, 1);

    if ($this->getRequest()->getMethod() == 'POST') {

        define('MASS_QUOTA_LIST_ROW_START', 2);
        define('MASS_QUOTA_LIST_COL_ID', 0);
        define('MASS_QUOTA_LIST_COL_AREA_NAME', 1);
        define('MASS_QUOTA_LIST_COL_QUOTA_DATE', 2);
        define('MASS_QUOTA_LIST_COL_QUOTA_QTY', 3);

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
                        'type' => 'OPPO Upload Quota Products',
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
                    $total_order_row = $highestRow - MASS_QUOTA_LIST_ROW_START + 1;

                    $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
                    $status=1; $d_id='';   
                                       
                    $data_sn=null;
                    $sn = date('YmdHis') . substr(microtime(), 2, 1);
                    for ($i = MASS_QUOTA_LIST_ROW_START; $i <= $highestRow; $i++) {

                    	$area_id = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_QUOTA_LIST_COL_ID, $i)
                            ->getValue());

                        $dis_type = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_QUOTA_LIST_COL_AREA_NAME, $i)
                            ->getValue());

                        $quota_date_v = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_QUOTA_LIST_COL_QUOTA_DATE, $i)
                            ->getValue());
                        $quota_date = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($quota_date_v));
                       
                        $quantity = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_QUOTA_LIST_COL_QUOTA_QTY, $i)
                            ->getValue());
                        
                        if ($quantity > 0);
                        
                        $data_row=[];
                        $data_row["area_id"] 				  =   $area_id;
                        $data_row["dis_type"] 			      =   $dis_type;
                        $data_row["cat_id"]				      =	  $cat_id ;
                        $data_row["good_id"]			      =   $good_id;
                        $data_row["good_color"] 		      =   $good_color;
                        $data_row["quantity"] 				  =   $quantity;
                        $data_row["quota_date"] 			  =   $quota_date;
                        //$data_row["key_sn"] 				  =   $key_sn;
                        
                        $data_row["import_by"] 			      =   $userStorage->id;
                        $data_row["import_date"] 			  =   date('Y-m-d H:i:s');

                        //print_r($data_row);die;
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
                        	$findDuplicate = $QQuota->Duplicate($data_row["area_id"],$data_row["cat_id"],$data_row["good_id"],$data_row["good_color"],$data_row["quota_date"] );

                        	if ($findDuplicate) {
                        		array_push($error_list,$data_row);
                        	}
                        	else {
                        		array_push($success_list,$data_row);
                        		$insertData = $QQuota->insert($data_row);
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
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_ID, 1, 'area_id');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_AREA_NAME, 1, 'dis_type');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_cat_id, 1, 'cat_id');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_good_id, 1, 'good_id');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_good_color, 1, 'good_color');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_quantity, 1, 'quantity');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_quota_date, 1, 'quota_date');
                                               


                        // các dòng lỗi
                        $i = 2;

                        foreach ($error_list as $key => $row) {
                         //    $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DEALER, $i, $row['d_id']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_ID, $i, $row['area_id']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_AREA_NAME, $i, $row['dis_type']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_cat_id, $i, $row['cat_id']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_good_id, $i, $row['good_id']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_good_color, $i, $row['good_color']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_quantity, $i, $row['quantity']);
	                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_QUOTA_LIST_COL_quota_date, $i, $row['quota_date']);
	                        
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
                       // $objWriter->save($new_file_dir);
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
