<?php

    $this->_helper->layout->disableLayout();
    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        
    if ($this->getRequest()->getMethod() == 'POST'){

        //print_r($_POST);die;
            $import_name                = $this->getRequest()->getParam('import_name');
            $wht_type                   = $this->getRequest()->getParam('wht_type');

            define('ROW_START', 2);
            define('RUNNING_NO', 0);
            define('PROVINCE_NAME', 1);
            define('TOPIC', 2);
            define('DISTRIBUTOR_ID', 3);
            define('DISTRIBUTOR_NAME', 4);
            define('DISTRIBUTOR_TAX_NO', 5);
            define('PAYMENT_NAME_01', 6);
            define('PAYMENT_DATE_01', 7);
            define('PAYMENT_PRICE_01',8);
            define('PAYMENT_WHT_VAT_01',9);
            define('PAYMENT_TYPE_WHT_VAT_01',10);
            define('PAYMENT_NAME_02',11);
            define('PAYMENT_DATE_02',12);
            define('PAYMENT_PRICE_02',13);
            define('PAYMENT_WHT_VAT_02',14);
            define('PAYMENT_TYPE_WHT_VAT_02',15);
            // define('PAYMENT_NAME_03',16);
            // define('PAYMENT_DATE_03',17);
            // define('PAYMENT_PRICE_03',18);
            // define('PAYMENT_WHT_VAT_03',19);
            // define('PAYMENT_TYPE_WHT_VAT_03',20);
            // define('PAYMENT_NAME_04',21);
            // define('PAYMENT_DATE_04',22);
            // define('PAYMENT_PRICE_04',23);
            // define('PAYMENT_WHT_VAT_04',24);
            // define('PAYMENT_TYPE_WHT_VAT_04',25);
            // define('PAYMENT_NAME_05',26);
            // define('PAYMENT_DATE_05',27);
            // define('PAYMENT_PRICE_05',28);
            // define('PAYMENT_WHT_VAT_05',29);
            // define('PAYMENT_TYPE_WHT_VAT_05',30);
            // define('PAYMENT_NAME_06',31);
            // define('PAYMENT_DATE_06',32);
            // define('PAYMENT_PRICE_06',33);
            // define('PAYMENT_WHT_VAT_06',34);
            // define('PAYMENT_TYPE_WHT_VAT_06',35);
            define('ADDRESS_TAX',16);

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
                        $QWithholdingTaxManual = new Application_Model_WithholdingTaxManual();

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
                        $format = 'dd/mm/yyyy';
                        for ($i = ROW_START; $i <= $highestRow; $i++) 
                        {

                        $wht_running_no = trim($objWorksheet
                            ->getCellByColumnAndRow(RUNNING_NO, $i)
                            ->getValue()); 

                        if(!$wht_running_no){
                            continue;
                        }
                        
                        $province_name = trim($objWorksheet
                            ->getCellByColumnAndRow(PROVINCE_NAME, $i)
                            ->getValue()); 

                        $topic = trim($objWorksheet
                            ->getCellByColumnAndRow(TOPIC, $i)
                            ->getValue()); 

                        $distributor_id = trim($objWorksheet
                            ->getCellByColumnAndRow(DISTRIBUTOR_ID, $i)
                            ->getValue()); 

                        if($distributor_id==''){
                            $distributor_id=0;
                        }
                                
                        $distributor_name = trim($objWorksheet
                            ->getCellByColumnAndRow(DISTRIBUTOR_NAME, $i)
                            ->getValue()); 
                               
                        $distributor_tax_no = trim($objWorksheet
                            ->getCellByColumnAndRow(DISTRIBUTOR_TAX_NO, $i)
                            ->getValue());

                        $payment_name_01 = trim($objWorksheet
                            ->getCellByColumnAndRow(PAYMENT_NAME_01, $i)
                            ->getValue());

                        $t_01 = trim($objWorksheet
                            ->getCellByColumnAndRow(PAYMENT_DATE_01, $i)
                            ->getFormattedValue());
                            
                        $payment_date_01 = '';
                        if($t_01 !=''){
                            $payment_date_01 = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($objWorksheet
                                ->getCellByColumnAndRow(PAYMENT_DATE_01, $i)
                                ->getValue()));
                        }

                        if($payment_date_01==''){
                            $payment_date_01 = "0000-00-00 00:00:00";
                        }

                        $payment_price_01 = trim($objWorksheet
                            ->getCellByColumnAndRow(PAYMENT_PRICE_01, $i)
                            ->getValue());

                        if($payment_price_01==''){
                            $payment_price_01 =0;
                        }


                        $payment_wht_vat_01 = trim($objWorksheet
                            ->getCellByColumnAndRow(PAYMENT_WHT_VAT_01, $i)
                            ->getValue());

                        if($payment_wht_vat_01==''){
                            $payment_wht_vat_01 =0;
                        }

                        $payment_type_wht_vat_01 = trim($objWorksheet
                            ->getCellByColumnAndRow(PAYMENT_TYPE_WHT_VAT_01, $i)
                            ->getValue());

                        $payment_name_02 = trim($objWorksheet
                            ->getCellByColumnAndRow(PAYMENT_NAME_02, $i)
                            ->getValue());

/*                        $t_02 = trim($objWorksheet
                            ->getCellByColumnAndRow(PAYMENT_DATE_02, $i)
                            ->getFormattedValue());

                        if($t_02 !=''){
                            $pay_02 = explode("-",$t_02);
                            $payment_date_02 = ($pay_02[2]-543).'-'.$pay_02[1].'-'.$pay_02[0].' 00:00:00';
                        }

                        if($payment_date_02==''){
                            $payment_date_02 = "0000-00-00 00:00:00";
                        }*/

                        $t_02 = trim($objWorksheet
                            ->getCellByColumnAndRow(PAYMENT_DATE_02, $i)
                            ->getFormattedValue());
                            
                        $payment_date_02 = '';
                        if($t_02 !=''){
                            $payment_date_02 = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($objWorksheet
                                ->getCellByColumnAndRow(PAYMENT_DATE_02, $i)
                                ->getValue()));
                        }

                        if($payment_date_02==''){
                            $payment_date_02 = "0000-00-00 00:00:00";
                        }


                        $payment_price_02 = trim($objWorksheet
                            ->getCellByColumnAndRow(PAYMENT_PRICE_02, $i)
                            ->getValue());

                        if($payment_price_02==''){
                            $payment_price_02 =0;
                        }

                        $payment_wht_vat_02 = trim($objWorksheet
                            ->getCellByColumnAndRow(PAYMENT_WHT_VAT_02, $i)
                            ->getValue());

                        if($payment_wht_vat_02==''){
                            $payment_wht_vat_02 =0;
                        }

                        $payment_type_wht_vat_02 = trim($objWorksheet
                            ->getCellByColumnAndRow(PAYMENT_TYPE_WHT_VAT_02, $i)
                            ->getValue());

                        // $payment_name_03 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_NAME_03, $i)
                        //     ->getValue());

                        // $t_03 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_DATE_03, $i)
                        //     ->getFormattedValue());
                        // $payment_date_03 = '';
                        // if($t_03 !=''){
                        //     $payment_date_03 = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($objWorksheet
                        //         ->getCellByColumnAndRow(PAYMENT_DATE_03, $i)
                        //         ->getValue()));
                        // }

                        // if($payment_date_03==''){
                        //     $payment_date_03 = "0000-00-00 00:00:00";
                        // }


                        // $payment_price_03 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_PRICE_03, $i)
                        //     ->getValue());

                        // if($payment_price_03==''){
                        //     $payment_price_03 =0;
                        // }

                        // $payment_wht_vat_03 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_WHT_VAT_03, $i)
                        //     ->getValue());

                        // if($payment_wht_vat_03==''){
                        //     $payment_wht_vat_03 =0;
                        // }

                        // $payment_type_wht_vat_03 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_TYPE_WHT_VAT_03, $i)
                        //     ->getValue());

                        // $payment_name_04 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_NAME_04, $i)
                        //     ->getValue());

                        // $t_04 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_DATE_04, $i)
                        //     ->getFormattedValue());
                        // $payment_date_04 = '';
                        // if($t_04 !=''){
                        //     $payment_date_04 = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($objWorksheet
                        //         ->getCellByColumnAndRow(PAYMENT_DATE_04, $i)
                        //         ->getValue()));
                        // }

                        // if($payment_date_04==''){
                        //     $payment_date_04 = "0000-00-00 00:00:00";
                        // }


                        // $payment_price_04 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_PRICE_04, $i)
                        //     ->getValue());

                        // if($payment_price_04==''){
                        //     $payment_price_04 =0;
                        // }

                        // $payment_wht_vat_04 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_WHT_VAT_04, $i)
                        //     ->getValue());

                        // if($payment_wht_vat_04==''){
                        //     $payment_wht_vat_04 =0;
                        // }

                        // $payment_type_wht_vat_04 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_TYPE_WHT_VAT_04, $i)
                        //     ->getValue());

                        // $payment_name_05 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_NAME_05, $i)
                        //     ->getValue());

                        // $t_05 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_DATE_05, $i)
                        //     ->getFormattedValue());
                        // $payment_date_05 = '';
                        // if($t_05 !=''){
                        //     $payment_date_05 = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($objWorksheet
                        //         ->getCellByColumnAndRow(PAYMENT_DATE_05, $i)
                        //         ->getValue()));
                        // }

                        // if($payment_date_05==''){
                        //     $payment_date_05 = "0000-00-00 00:00:00";
                        // }


                        // $payment_price_05 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_PRICE_05, $i)
                        //     ->getValue());

                        // if($payment_price_05==''){
                        //     $payment_price_05 =0;
                        // }

                        // $payment_wht_vat_05 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_WHT_VAT_05, $i)
                        //     ->getValue());

                        // if($payment_wht_vat_05==''){
                        //     $payment_wht_vat_05 =0;
                        // }

                        // $payment_type_wht_vat_05 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_TYPE_WHT_VAT_05, $i)
                        //     ->getValue());

                        // $payment_name_06 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_NAME_06, $i)
                        //     ->getValue());

                        // $t_06 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_DATE_06, $i)
                        //     ->getFormattedValue());
                        // $payment_date_06 = '';
                        // if($t_06 !=''){
                        //     $payment_date_06 = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($objWorksheet
                        //         ->getCellByColumnAndRow(PAYMENT_DATE_06, $i)
                        //         ->getValue()));
                        // }

                        // if($payment_date_06==''){
                        //     $payment_date_06 = "0000-00-00 00:00:00";
                        // }


                        // $payment_price_06 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_PRICE_06, $i)
                        //     ->getValue());

                        // if($payment_price_06==''){
                        //     $payment_price_06 =0;
                        // }

                        // $payment_wht_vat_06 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_WHT_VAT_06, $i)
                        //     ->getValue());

                        // if($payment_wht_vat_06==''){
                        //     $payment_wht_vat_06 =0;
                        // }

                        // $payment_type_wht_vat_06 = trim($objWorksheet
                        //     ->getCellByColumnAndRow(PAYMENT_TYPE_WHT_VAT_06, $i)
                        //     ->getValue());

                        $address_tax = trim($objWorksheet
                            ->getCellByColumnAndRow(ADDRESS_TAX, $i)
                            ->getValue());
                       
                        
                        /*------------------Start------------------------*/
                            $wht_sn = date('YmdHis') . substr(microtime(), 2, 4);
                            //$wht_running_no = $this->getCreateMonthNo_Ref($db,'WT',$wht_sn);

                            $params = array(
                                'wht_sn'                    => $wht_sn,
                                'wht_type'                  => $wht_type,   //1=WP 2=WR 3=WO 4=WA 5=WS
                                'wht_running_no'            => $wht_running_no,
                                'province_name'             => $province_name,
                                'topic'                     => $topic,
                                'import_name'               => trim($import_name),
                                'distributor_id'            => trim($distributor_id),
                                'distributor_name'          => trim($distributor_name),
                                'distributor_tax_no'        => trim($distributor_tax_no),
                                'payment_name_01'           => trim($payment_name_01),
                                'payment_date_01'           => trim($payment_date_01),
                                'payment_price_01'          => trim($payment_price_01),
                                'payment_wht_vat_01'        => trim($payment_wht_vat_01),
                                'payment_type_wht_vat_01'   => trim($payment_type_wht_vat_01),
                                'payment_name_02'           => trim($payment_name_02),
                                'payment_date_02'           => trim($payment_date_02),
                                'payment_price_02'          => trim($payment_price_02),
                                'payment_wht_vat_02'        => trim($payment_wht_vat_02),
                                'payment_type_wht_vat_02'   => trim($payment_type_wht_vat_02),
                                // 'payment_name_03'           => trim($payment_name_03),
                                // 'payment_date_03'           => trim($payment_date_03),
                                // 'payment_price_03'          => trim($payment_price_03),
                                // 'payment_wht_vat_03'        => trim($payment_wht_vat_03),
                                // 'payment_type_wht_vat_03'   => trim($payment_type_wht_vat_03),
                                // 'payment_name_04'           => trim($payment_name_04),
                                // 'payment_date_04'           => trim($payment_date_04),
                                // 'payment_price_04'          => trim($payment_price_04),
                                // 'payment_wht_vat_04'        => trim($payment_wht_vat_04),
                                // 'payment_type_wht_vat_04'   => trim($payment_type_wht_vat_04),
                                // 'payment_name_05'           => trim($payment_name_05),
                                // 'payment_date_05'           => trim($payment_date_05),
                                // 'payment_price_05'          => trim($payment_price_05),
                                // 'payment_wht_vat_05'        => trim($payment_wht_vat_05),
                                // 'payment_type_wht_vat_05'   => trim($payment_type_wht_vat_05),
                                // 'payment_name_06'           => trim($payment_name_06),
                                // 'payment_date_06'           => trim($payment_date_06),
                                // 'payment_price_06'          => trim($payment_price_06),
                                // 'payment_wht_vat_06'        => trim($payment_wht_vat_06),
                                // 'payment_type_wht_vat_06'   => trim($payment_type_wht_vat_06),
                                'address_tax'               => trim($address_tax),
                                'create_by'                 => $userStorage->id,
                                'create_date'               => $create_date,
                                'status'                    => 1
                            );

                        $textFlag = $wht_running_no;
                        $textFlag = strtoupper($textFlag);
                        $wo = false;

                        if (strpos($textFlag, 'WO') !== false) {
                            $wo = true;
                        }

                        switch ($wht_type) {
                            case '1':
                                if (strpos($textFlag, 'WP') !== false) {

                                }else{
                                    throw new Exception('Wrong Type Format!. : ' . $wht_running_no);
                                }
                                break;
                            case '2':
                                if (strpos($textFlag, 'WR') !== false) {

                                }else{
                                    throw new Exception('Wrong Type Format!. : ' . $wht_running_no);
                                }
                                break;
                            case '3':
                                if (strpos($textFlag, 'WO') !== false) {

                                }else{
                                    throw new Exception('Wrong Type Format!. : ' . $wht_running_no);
                                }
                                break;
                            case '4':
                                if (strpos($textFlag, 'WA') !== false) {

                                }else{
                                    throw new Exception('Wrong Type Format!. : ' . $wht_running_no);
                                }
                                break;
                            case '5':
                                if (strpos($textFlag, 'WS') !== false) {

                                }else{
                                    throw new Exception('Wrong Type Format!. : ' . $wht_running_no);
                                }
                                break;
                            case '6':
                                if (strpos($textFlag, 'WT') !== false) {

                                }else{
                                    throw new Exception('Wrong Type Format!. : ' . $wht_running_no);
                                }
                                break;
                        }

                        if($wo){

                            $user_slot = 0;

                            if(isset($payment_name_01) and $payment_name_01 != ''){
                                $user_slot++;
                            }

                            if(isset($payment_name_02) and $payment_name_02 != ''){
                                $user_slot++;
                            }

                            $get_wht = $QWithholdingTaxManual->getWHT($wht_running_no);

                            $current_payment = '';

                            if($get_wht){

                                if($get_wht['payment_name_01']){

                                    $current_payment = '1';

                                    if($get_wht['payment_name_02']){

                                        $current_payment = '2';

                                        if($get_wht['payment_name_03']){

                                            $current_payment = '3';

                                            if($get_wht['payment_name_04']){

                                                $current_payment = '4';

                                                if($get_wht['payment_name_05']){

                                                    $current_payment = '5';

                                                    if($get_wht['payment_name_06']){

                                                        $current_payment = '6';

                                                        if($get_wht['payment_name_07']){

                                                            $current_payment = '7';

                                                            if($get_wht['payment_name_08']){

                                                                $current_payment = '8';

                                                                if($get_wht['payment_name_09']){

                                                                    $current_payment = '9';

                                                                    if($get_wht['payment_name_10']){
                                                                        throw new Exception('Wrong Format!. : ' . $wht_running_no);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                switch ($current_payment) {
                                    
                                    case '':

                                        $params['payment_name_01'] = trim($payment_name_01);
                                        $params['payment_date_01'] = trim($payment_date_01);
                                        $params['payment_price_01'] = trim($payment_price_01);
                                        $params['payment_wht_vat_01'] = trim($payment_wht_vat_01);
                                        $params['payment_type_wht_vat_01'] = trim($payment_type_wht_vat_01);
                                        $params['payment_name_02'] = trim($payment_name_02);
                                        $params['payment_date_02'] = trim($payment_date_02);
                                        $params['payment_price_02'] = trim($payment_price_02);
                                        $params['payment_wht_vat_02'] = trim($payment_wht_vat_02);
                                        $params['payment_type_wht_vat_02'] = trim($payment_type_wht_vat_02);

                                        $result=$QWithholdingTaxManual->insert($params);

                                        break;

                                    case '1':

                                        $params_new = [];
                                        $params_new['payment_name_02'] = trim($payment_name_01);
                                        $params_new['payment_date_02'] = trim($payment_date_01);
                                        $params_new['payment_price_02'] = trim($payment_price_01);
                                        $params_new['payment_wht_vat_02'] = trim($payment_wht_vat_01);
                                        $params_new['payment_type_wht_vat_02'] = trim($payment_type_wht_vat_01);
                                        $params_new['payment_name_03'] = trim($payment_name_02);
                                        $params_new['payment_date_03'] = trim($payment_date_02);
                                        $params_new['payment_price_03'] = trim($payment_price_02);
                                        $params_new['payment_wht_vat_03'] = trim($payment_wht_vat_02);
                                        $params_new['payment_type_wht_vat_03'] = trim($payment_type_wht_vat_02);

                                        $where_update_payment = $QWithholdingTaxManual->getAdapter()->quoteInto('wht_running_no = ?', $wht_running_no);
                                        $result = $QWithholdingTaxManual->update($params_new,$where_update_payment);
                                        break;

                                    case '2':

                                        $params_new = [];

                                        $params_new['payment_name_03'] = trim($payment_name_01);
                                        $params_new['payment_date_03'] = trim($payment_date_01);
                                        $params_new['payment_price_03'] = trim($payment_price_01);
                                        $params_new['payment_wht_vat_03'] = trim($payment_wht_vat_01);
                                        $params_new['payment_type_wht_vat_03'] = trim($payment_type_wht_vat_01);
                                        $params_new['payment_name_04'] = trim($payment_name_02);
                                        $params_new['payment_date_04'] = trim($payment_date_02);
                                        $params_new['payment_price_04'] = trim($payment_price_02);
                                        $params_new['payment_wht_vat_04'] = trim($payment_wht_vat_02);
                                        $params_new['payment_type_wht_vat_04'] = trim($payment_type_wht_vat_02);

                                        $where_update_payment = $QWithholdingTaxManual->getAdapter()->quoteInto('wht_running_no = ?', $wht_running_no);
                                        $result = $QWithholdingTaxManual->update($params_new,$where_update_payment);
                                        break;

                                    case '3':

                                        $params_new = [];

                                        $params_new['payment_name_04'] = trim($payment_name_01);
                                        $params_new['payment_date_04'] = trim($payment_date_01);
                                        $params_new['payment_price_04'] = trim($payment_price_01);
                                        $params_new['payment_wht_vat_04'] = trim($payment_wht_vat_01);
                                        $params_new['payment_type_wht_vat_04'] = trim($payment_type_wht_vat_01);
                                        $params_new['payment_name_05'] = trim($payment_name_02);
                                        $params_new['payment_date_05'] = trim($payment_date_02);
                                        $params_new['payment_price_05'] = trim($payment_price_02);
                                        $params_new['payment_wht_vat_05'] = trim($payment_wht_vat_02);
                                        $params_new['payment_type_wht_vat_05'] = trim($payment_type_wht_vat_02);

                                        $where_update_payment = $QWithholdingTaxManual->getAdapter()->quoteInto('wht_running_no = ?', $wht_running_no);
                                        $result = $QWithholdingTaxManual->update($params_new,$where_update_payment);
                                        break;

                                    case '4':

                                        $params_new = [];

                                        $params_new['payment_name_05'] = trim($payment_name_01);
                                        $params_new['payment_date_05'] = trim($payment_date_01);
                                        $params_new['payment_price_05'] = trim($payment_price_01);
                                        $params_new['payment_wht_vat_05'] = trim($payment_wht_vat_01);
                                        $params_new['payment_type_wht_vat_05'] = trim($payment_type_wht_vat_01);
                                        $params_new['payment_name_06'] = trim($payment_name_02);
                                        $params_new['payment_date_06'] = trim($payment_date_02);
                                        $params_new['payment_price_06'] = trim($payment_price_02);
                                        $params_new['payment_wht_vat_06'] = trim($payment_wht_vat_02);
                                        $params_new['payment_type_wht_vat_06'] = trim($payment_type_wht_vat_02);

                                        $where_update_payment = $QWithholdingTaxManual->getAdapter()->quoteInto('wht_running_no = ?', $wht_running_no);
                                        $result = $QWithholdingTaxManual->update($params_new,$where_update_payment);
                                        break;

                                    case '5':

                                        $params_new = [];

                                        $params_new['payment_name_06'] = trim($payment_name_01);
                                        $params_new['payment_date_06'] = trim($payment_date_01);
                                        $params_new['payment_price_06'] = trim($payment_price_01);
                                        $params_new['payment_wht_vat_06'] = trim($payment_wht_vat_01);
                                        $params_new['payment_type_wht_vat_06'] = trim($payment_type_wht_vat_01);
                                        $params_new['payment_name_07'] = trim($payment_name_02);
                                        $params_new['payment_date_07'] = trim($payment_date_02);
                                        $params_new['payment_price_07'] = trim($payment_price_02);
                                        $params_new['payment_wht_vat_07'] = trim($payment_wht_vat_02);
                                        $params_new['payment_type_wht_vat_07'] = trim($payment_type_wht_vat_02);

                                        $where_update_payment = $QWithholdingTaxManual->getAdapter()->quoteInto('wht_running_no = ?', $wht_running_no);
                                        $result = $QWithholdingTaxManual->update($params_new,$where_update_payment);
                                        break;

                                    case '6':

                                        $params_new = [];

                                        $params_new['payment_name_07'] = trim($payment_name_01);
                                        $params_new['payment_date_07'] = trim($payment_date_01);
                                        $params_new['payment_price_07'] = trim($payment_price_01);
                                        $params_new['payment_wht_vat_07'] = trim($payment_wht_vat_01);
                                        $params_new['payment_type_wht_vat_07'] = trim($payment_type_wht_vat_01);
                                        $params_new['payment_name_08'] = trim($payment_name_02);
                                        $params_new['payment_date_08'] = trim($payment_date_02);
                                        $params_new['payment_price_08'] = trim($payment_price_02);
                                        $params_new['payment_wht_vat_08'] = trim($payment_wht_vat_02);
                                        $params_new['payment_type_wht_vat_08'] = trim($payment_type_wht_vat_02);

                                        $where_update_payment = $QWithholdingTaxManual->getAdapter()->quoteInto('wht_running_no = ?', $wht_running_no);
                                        $result = $QWithholdingTaxManual->update($params_new,$where_update_payment);
                                        break;

                                    case '7':

                                        $params_new = [];

                                        $params_new['payment_name_08'] = trim($payment_name_01);
                                        $params_new['payment_date_08'] = trim($payment_date_01);
                                        $params_new['payment_price_08'] = trim($payment_price_01);
                                        $params_new['payment_wht_vat_08'] = trim($payment_wht_vat_01);
                                        $params_new['payment_type_wht_vat_08'] = trim($payment_type_wht_vat_01);
                                        $params_new['payment_name_09'] = trim($payment_name_02);
                                        $params_new['payment_date_09'] = trim($payment_date_02);
                                        $params_new['payment_price_09'] = trim($payment_price_02);
                                        $params_new['payment_wht_vat_09'] = trim($payment_wht_vat_02);
                                        $params_new['payment_type_wht_vat_09'] = trim($payment_type_wht_vat_02);

                                        $where_update_payment = $QWithholdingTaxManual->getAdapter()->quoteInto('wht_running_no = ?', $wht_running_no);
                                        $result = $QWithholdingTaxManual->update($params_new,$where_update_payment);
                                        break;

                                    case '8':

                                        $params_new = [];

                                        $params_new['payment_name_09'] = trim($payment_name_01);
                                        $params_new['payment_date_09'] = trim($payment_date_01);
                                        $params_new['payment_price_09'] = trim($payment_price_01);
                                        $params_new['payment_wht_vat_09'] = trim($payment_wht_vat_01);
                                        $params_new['payment_type_wht_vat_09'] = trim($payment_type_wht_vat_01);
                                        $params_new['payment_name_10'] = trim($payment_name_02);
                                        $params_new['payment_date_10'] = trim($payment_date_02);
                                        $params_new['payment_price_10'] = trim($payment_price_02);
                                        $params_new['payment_wht_vat_10'] = trim($payment_wht_vat_02);
                                        $params_new['payment_type_wht_vat_10'] = trim($payment_type_wht_vat_02);

                                        $where_update_payment = $QWithholdingTaxManual->getAdapter()->quoteInto('wht_running_no = ?', $wht_running_no);
                                        $result = $QWithholdingTaxManual->update($params_new,$where_update_payment);
                                        break;

                                    case '9':

                                        $params_new = [];

                                        if(isset($payment_name_02) && $payment_name_02 != ''){
                                            throw new Exception('Wrong Format! (Payment Full[]). : ' . $wht_running_no);
                                        }

                                        $params_new['payment_name_10'] = trim($payment_name_01);
                                        $params_new['payment_date_10'] = trim($payment_date_01);
                                        $params_new['payment_price_10'] = trim($payment_price_01);
                                        $params_new['payment_wht_vat_10'] = trim($payment_wht_vat_01);
                                        $params_new['payment_type_wht_vat_10'] = trim($payment_type_wht_vat_01);

                                        $where_update_payment = $QWithholdingTaxManual->getAdapter()->quoteInto('wht_running_no = ?', $wht_running_no);
                                        $result = $QWithholdingTaxManual->update($params_new,$where_update_payment);
                                        break;
                                }
                            }else{
                                $result=$QWithholdingTaxManual->insert($params);
                            }

                        }else{

                            $params['payment_name_01'] = trim($payment_name_01);
                            $params['payment_date_01'] = trim($payment_date_01);
                            $params['payment_price_01'] = trim($payment_price_01);
                            $params['payment_wht_vat_01'] = trim($payment_wht_vat_01);
                            $params['payment_type_wht_vat_01'] = trim($payment_type_wht_vat_01);
                            $params['payment_name_02'] = trim($payment_name_02);
                            $params['payment_date_02'] = trim($payment_date_02);
                            $params['payment_price_02'] = trim($payment_price_02);
                            $params['payment_wht_vat_02'] = trim($payment_wht_vat_02);
                            $params['payment_type_wht_vat_02'] = trim($payment_type_wht_vat_02);

                            $get_wht = $QWithholdingTaxManual->getWHT($wht_running_no);
                            if($get_wht){
                                throw new Exception('Duplicate Running!. : ' . $wht_running_no);
                            }

                            $result=$QWithholdingTaxManual->insert($params);

                        }

                        //print_r($params);die;
                        //print_r($result);die;
                        if ($result != '') { //success           
                            $success_list[] = $params;  
                            $flashMessenger->setNamespace('success')->addMessage($result['message']);
                        } else {
                            $error_list[] = $params;
                            $flashMessenger->setNamespace('error')->addMessage($result['message']);
                        }


                        /*------------------End------------------------*/

                        /*$status = $result['code'];
                        if ($result['code'] == 0) {
                            $success_list[] = $params;                           
                        } else {
                            $data['message'] = $result['message'];
                            $error_list[] = $data;
                        }*/

                        $number_of_order++;
                        $percent = round($number_of_order * 100 / $total_order_row, 1);

                        $data = array(
                            'total' => $total_order_row,
                            'failed' => count($error_list),
                            'succeed' => $total_order_row - count($error_list),
                        );

                        $progress->flush($percent);
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

                            $objWorksheet_out->setCellValueByColumnAndRow(DISTRIBUTOR_ID, 1, 'Distributor ID');
                            $objWorksheet_out->setCellValueByColumnAndRow(DISTRIBUTOR_NAME, 1, 'Distributor Name');
                            $objWorksheet_out->setCellValueByColumnAndRow(DISTRIBUTOR_TAX_NO, 1, 'Distributor TAX No');
                            $objWorksheet_out->setCellValueByColumnAndRow(DISTRIBUTOR_NAME + 1, 1, 'Messages');


                            // các dòng lỗi
                            $i = 2;
                            foreach ($error_list as $key => $row) {
                                $objWorksheet_out->setCellValueByColumnAndRow(DISTRIBUTOR_ID, $i, $row['distributor_id']);
                                $objWorksheet_out->setCellValueByColumnAndRow(DISTRIBUTOR_NAME, $i, $row['distributor_name']);
                                $objWorksheet_out->setCellValueByColumnAndRow(DISTRIBUTOR_TAX_NO, $i, $row['distributor_tax_no']);
                                $objWorksheet_out->setCellValueByColumnAndRow(DISTRIBUTOR_NAME +1, $i, $row['message']);
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

    
    

