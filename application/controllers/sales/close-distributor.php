<?php

    $this->_helper->layout->disableLayout();
    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        
if ($this->getRequest()->getMethod() == 'POST'){

    define('MASS_BVG_LIST_ROW_START', 2);
    
    set_time_limit(0);
    ini_set('memory_limit', -1);
    $db = Zend_Registry::get('db');

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

    }else {
        try {
//-------------------
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
            $total_order_row = $highestRow - MASS_BVG_LIST_ROW_START + 1;

            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
            //---------- file excel array
            $headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null,true,true,true);
            $headingsArray = $headingsArray[1];

            $r = -1;
            $data_excel = array();
            for($row = 2; $row <= $highestRow; ++$row){
                 $dataRow = $objWorksheet->rangeToArray('A1:'.$highestColumn.$row,null,true,true,true); 
                if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')){
                    ++$r; 
                    foreach ($headingsArray as $columkey => $columHeading) {
                        $data_excel[$r][$columHeading] = $dataRow[$row][$columkey];
                    }
                }

            }
           
            foreach ($data_excel as $key => $value) {

            	$Number  	= trim($value["NO"]);
            	$d_id  		= (int) trim($value["ID Distributor"]);

            	$where = $QDistributor->getAdapter()->quoteInto('id =?',$d_id);
            	$distributor = $QDistributor->fetchRow($where);

            	if(!isset($distributor)){
            		$Error = "ไม่พบ Distributor นี้:: $d_id ";
            		$this->view->error = $Error;
            	}

            	$where = array();
            	$where[] = $QDistributor->getAdapter()->quoteInto('id =?',$d_id);
            	$where[] = $QDistributor->getAdapter()->quoteInto('del =?',1);
            	$check_id = $QDistributor->fetchRow($where);

            	if(isset($check_id)){
            		$data_error['id'] = $d_id;
                    $data_error['message'] = "ID Distributor นี้มีการ Close แล้ว";
                    $error_list[] = $data_error;
            	}else{
            	
	            	$where = $QDistributor->getAdapter()->quoteInto('id =?',$d_id);
	            	$update = $QDistributor->fetchRow($where);
	            	
	            	if(isset($update)){
	            		
	            		$data = array(
	            			'del' => 1,
	            			'update_by' => 280,
	            			'update_date' => date('Y-m-d H:i:s')
	            		);

	            		$QDistributor->update($data,$where);
	            	}
	            }
	            	
	            	$number_of_order++;
	           	    $percent = round($number_of_order * 100 / $total_order_row, 1);
	           	    $progress->flush($percent);
	           	    $progress->flush(100);
            }

            $this->view->number_of_order = $number_of_order;
            $this->view->error_list = $error_list;

        }//end of try
        catch (Exception $e) {
            $db->rollback();
            $this->view->error = $e->getMessage();
            $progress->flush(0);
        }

    }


}
    
    

