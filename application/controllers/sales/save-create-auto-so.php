<?php

    $this->_helper->layout->disableLayout();
    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        
if ($this->getRequest()->getMethod() == 'POST'){

    $warehouse_online = $this->getRequest()->getParam('warehouse_online');

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


                // $file = fopen($uploaded_dir. DIRECTORY_SEPARATOR .$new_name,"r");
                // $data_csv = [];
                // while(! feof($file)){
                      
                //       $line_text = fgets($file);
                      
                //       if($line_text != ''){
                //        $t = explode(';',$line_text);
                //        $data_csv[] = $t;
                //       }
                //        $line_no++;
                // }
            
                // print_r(count($data_csv,COUNT_RECURSIVE)); die;

                // if(count($data_csv,COUNT_RECURSIVE) <100){
                //     $error_from_csv = "file in CSV format (Please select file CSV format Semicolon (;) )";
                //     $this->view->error = $error_from_csv;
                //     return;
                // }
                
                // echo'<pre>';
                // print_r($data_csv); die;
                // echo'</pre>';
            
                // $highestRow = count($data_csv);
                // $total_order_row = $highestRow - MASS_BVG_LIST_ROW_START + 1;

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

            // echo'<pre>';
            // print_r($data_excel); die;
            // echo'</pre>';
            $cat_ids     = array();
            $good_ids    = array();
            $good_colors = array();
            $nums        = array();
            $prices      = array();
            $sale_off_percents = array();
            $totals      = array();
            $ids         = array();
            $texts       = array();
            $tags        = array();
            $campaign    = array();
//------------------------------------------------
            $temp_sn = array();
            foreach ($data_excel as $key => $value) {
                array_push($temp_sn, trim($value["Order Number"]));
            }
            
            $temp_sn_array = array_values(array_unique($temp_sn));

        foreach ($temp_sn_array as $key1 => $value1) { // ใช้ตรวจสอบ order ที่มีมากกว่า 1
            
             //----- ประกาศให้ตัวแปรเริ่มต้น
            $cat_ids     = array();
            $good_ids    = array();
            $good_colors = array();
            $nums        = array();
            $prices      = array();
            $sale_off_percents = array();
            $totals      = array();
            $ids         = array();
            $tags        = array();
            $texts       = array();
            $campaign    = array();

            foreach ($data_excel as $key => $value) 
            { 
               if($value1 != trim($value["Order Number"])){
                  continue;
               }
                // if($key == 0){ //ข้าม array หัวข้อ ไปแสดง array อีกตัว
                //     continue;
                // }
                // $order_type = 'RETAILER';
                // $d_id       = trim($value['6'],'"');
                // $good_id    = trim($value['2'],'"');
                // $good_color = trim($value['2'],'"');
                // $qty            = trim($value[''],'"');
                // $sale_off_percent = trim($value[''],'"');
                // $unit_price       = trim($value['39'],'"');
                // $total_price      = trim($value[''],'"');
                // $total_spc_discount = 0;
                // $sale_order           = trim($value['0'],'"');
                // // $order_number           = trim($value['6'],'"');
                // $key_product_csv  = trim($value['2'],'"');
                // $delivery_fee     = trim($value['40'],'"');
                // $tax_po           = trim($value['6'],'"');

                // if($good_id == ''){
                //     continue;
                // } 
                // if($test_count == 15){
                //     print_r($value); die;
                // } $test_count++;

                if($value1 == trim($value["Order Number"])){

                           $order_type = 'RETAILER';
                           $d_id       = trim($value["Order Number"]);
                           $good_id    = trim($value["Model"]);
                           $good_color = trim($value["Model"]);
                           $qty        = trim($value["Amount"]);
                           $sale_off_percent = trim($value[""]);
                           $unit_price       = trim($value["Price"]);
                           $total_price      = trim($value[""]);
                           $total_spc_discount = 0;
                           $number           = trim($value["No"]);
                           $key_product      = trim($value["Model"]);
                           $delivery_fee     = trim($value[""]);
                           $tax_po           = trim($value["Order Number"]);
                

                        $QCsvimport   = new Application_Model_CsvImport();
                        $QDistributorMap_online = new Application_Model_DistributorMapOnline();
                        
                        $where = $QCsvimport->getAdapter()->quoteInto('key_product =?',$good_id);
                        $Keyproduct = $QCsvimport->fetchRow($where);
                        //----error
                        if(!isset($Keyproduct)){

                            $Error_import1 = "ไม่พบข้อมูล Key Product :: $key_product";
                            $this->view->error = $Error_import1;
                            return;
                        }
                        //--- end error

                        $good_id    = $Keyproduct['good_id'];
                        $good_color = $Keyproduct['good_color'];
                        $cat_id     = $Keyproduct['cat_id'];
                        $sale_off_percent = 0;
                        $total_price = 100;
                        
                        $where = $QDistributorMap_online->getAdapter()->quoteInto('order_number =?',$d_id);
                       $distributor_online = $QDistributorMap_online->fetchRow($where);

                       //----------error
                            if(!isset($distributor_online)){

                                $Error_import2 = "ไม่พบชื่อลูกค้าในรายการ เลขที่ $number ที่ upload ข้อมูล";
                                $this->view->error = $Error_import2;
                                return;
                            }
                        //------end error

                        $d_id = $distributor_online['distributor_id_online'];

                        if(!isset($d_id) || $d_id ==''){
                            continue;
                        }
                       
                        //-----------------------------------
                        
                        if($order_type=="APK"){
                            $order_type=5;  
                            array_push($ids, '');
                            array_push($texts, '');
                            array_push($campaign, '');
                            // array_push($tags, $tax_po);
                            //---------------------------
                            array_push($cat_ids, $cat_id);
                            array_push($good_ids, $good_id);
                            array_push($good_colors, $good_color);
                            array_push($nums, $qty);
                            array_push($prices, $unit_price);
                            array_push($sale_off_percents, $sale_off_percent);
                            array_push($totals, $total_price);

                        }else if($order_type=="RETAILER"){

                            $order_type=1;
                           
                            //---มือถือ
                            array_push($texts, '');
                            array_push($ids, '');
                            array_push($campaign, '');
                            // array_push($tags, $tax_po);
                            //----------------------------
                            array_push($cat_ids, $cat_id);
                            array_push($good_ids, $good_id);
                            array_push($good_colors, $good_color);
                            array_push($nums, $qty);
                            array_push($prices, $unit_price);
                            array_push($sale_off_percents, $sale_off_percent);
                            array_push($totals, $total_price);
                            
                        }
                        //-----------Gift-----------//
                    if($value["Free Gift"] == ''){

                    }else{
                        $data1 = explode("+",$value["Free Gift"]);

                        foreach ($data1 as $key2 => $value2) {

                           $data2 = explode("*",$value2);
                           
                           // echo "Item:".$data2[0]."<br>";
                           // echo "Num:".$data2[1]."<br>";

                           $G_good_id    = $data2[0];
                           $G_good_color = $data2[0];
                           $G_qty        = $data2[1];

                            $QCsvimport   = new Application_Model_CsvImport();
                            $QDistributorMap_online = new Application_Model_DistributorMapOnline();
                            
                            $where=$QCsvimport->getAdapter()->quoteInto('key_product =?',$G_good_id);
                            $G_key_productGift = $QCsvimport->fetchRow($where);
                            //----error
                            
                            if(!isset($G_key_productGift)){

                             $Error_import1 = "ไม่พบข้อมูล Key Product Gift :: $G_good_id";
                             $this->view->error = $Error_import1;
                             return;
                            }
                            //--- end error
                            $G_good_id    = $G_key_productGift['good_id'];
                            $G_good_color = $G_key_productGift['good_color'];
                            $G_cat_id     = $G_key_productGift['cat_id'];

                            

                            array_push($texts, '');
                            array_push($ids, '');
                            array_push($campaign, '');
                            // array_push($tags, $tax_po);
                            //--**กรณี productหรือของแถม เหมือนเดียวกัน ให้รวมเป็นอันเดียวกัน----
                            $check_temp = 0;
                            foreach ($good_ids as $tempgood_key => $temp_good) {
                                $temp_total = 0;
                                if($temp_good == $G_good_id && $cat_ids[$tempgood_key] == $G_cat_id && $good_colors[$tempgood_key] == $G_good_color){

                                    $temp_total = $nums[$tempgood_key]+$G_qty;
                                    $nums[$tempgood_key] = $temp_total;
                                    $check_temp++;
                                }
                            } // end **
                            if($check_temp == 0){

                                array_push($cat_ids, $G_cat_id);
                                array_push($good_ids, $G_good_id);
                                array_push($good_colors, $G_good_color);
                                array_push($nums, $G_qty );
                                array_push($prices, 0);
                                array_push($sale_off_percents, 100);
                                array_push($totals, 0);
                            }

                        }
                    }
                        
                        //--- end gift

                        /*------------------Start------------------------*/
                        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $d_id);
                        $distributor = $QDistributor->fetchRow($where);
            
                }
            } // end foreachs
            
                        $market_general_id=0;
                        //$type=5;
                        $salesman=$userStorage->id;
                        $rank=3;
                        $SearchBox='';
                        $distributor_id=$d_id;

                        switch ($warehouse_online) {
                            case '1'://Lazada
                                //WMLA - LAZADA
                                $warehouse_id=62;
                                break;
                            case '2'://11Street
                                //WMST-11STREET
                                $warehouse_id=73;
                                break;
                            case '3'://Shopee
                                //WMSP - Shopee
                                $warehouse_id=115;
                                break;
                            case '4'://JD
                                //WMJD - JD
                                $warehouse_id=125;
                                break;
                            case '5'://OS
                                //WMNL - ONLINE STORE
                                $warehouse_id=164;
                                break;
                            default:
                                $Error_import2 = "กรุณาเลือก Online Type";
                                $this->view->error = $Error_import2;
                                return;
                                break;
                        }

                        $sipping_add=$distributor['shipping_add_default'];
                        $life_time=1;
                        $include_shipping_fee=1;
                        $user_uncheck=0;
                        $market_general_data = array('shipment_id'    => 0);


                        $total_spc_discounts=$total_spc_discount;
                        $spc_discount=$distributor['spc_discount'];
                        $spc_discount_phone=$distributor['spc_discount_phone'];
                        $spc_discount_acc=$distributor['spc_discount_acc'];
                        $spc_discount_digital=$distributor['spc_discount_digital'];
                        $save_service='sales';
                        $creditnote_data='';
                        $deposit_data='';

                        $params = array(
                                'order_type'           => $order_type,
                                'market_general_id'    => $market_general_id,
                                'ids'                  => $ids,
                                'save_service'         => $save_service,
                                'cat_id'               => $cat_ids,
                                'good_id'              => $good_ids,
                                'good_color'           => $good_colors,
                                'num'                  => $nums,
                                'price'                => $prices,
                                'total'                => $totals,
                                'text'                 => $texts,
                                'distributor_id'       => $distributor_id,
                                'warehouse_id'         => $warehouse_id,
                                'salesman'             => $salesman,
                                'sales_catty_id'       => $sales_catty_id,
                                'area_id'              => $area_id,
                                'type'                 => $order_type,
                                'sale_off_percent'     => $sale_off_percents,
                                'sn'                   => $sn,
                                'life_time'            => $life_time,
                                'isbatch'              => 1,
                                'rebate_price'         => $rebate_price,
                                'service_id'           => $service_id,
                                'ids_bvg'              => $ids_bvg,
                                'joint'                => $joint,
                                'good_id_bvg'          => $good_ids_bvg,
                                'num_bvg'              => $nums_bvg,
                                'price_bvg'            => $prices_bvg,
                                'total_bvg'            => $totals_bvg,
                                'joint_discount'       => $joint_discount,
                                'ids_discount'         => $ids_discount,
                                'prices_discount'      => $prices_discount,
                                'bvg_imei'             => $bvg_imei,
                                'distributor_po'       => $distributor_po,
                                'gift_id'              => $gift_id,
                                'include_shipping_fee' => $include_shipping_fee,
                                'user_uncheck'         => $user_uncheck == 'true' ? 1 : 0,
                                'campaign'             => $campaign,
                                'payment_method'       => $payment_method,
                                'id_staff'             => $id_staffs,
                                'name_staff_ingame'    => $name_staff_ingames,
                                'cmnd_staff_ingame'    => $cmnd_staff_ingames,
                                'shipment_type'        => $shipment_types,
                                'sophieuthu'           => $sophieuthus,
                                'sotienthucte'         => $sotienthuctes,
                                'payment_date'         => $payment_dates,
                                'shipment_id'          => $shipment_id,
                                'product_color_key'    => $product_color_key,
                                'staff_num'            => $staff_num,
                                'for_partner'          => $for_partner,
                                'credit_id'            => $credit_id,
                                // 'delivery_address'     => $delivery_address, 
                                'delivery_fee'         => $delivery_fee,
                                'customer_id'          => $customer_id,
                                'customer_name'        => $customer_name,
                                'customer_tax_number'  => $customer_tax_number,
                                'customer_branch_number'  => $customer_branch_number,
                                'customer_tax_address' => $customer_tax_address,
                                'rank'                 => $rank,
                                'edit'                 => $edit,
                                'sipping_add'          => $sipping_add,
                                'customer_name_for_staff'        => $customer_name_for_staff,
                                'total_spc_discount'   => $total_spc_discounts,
                                'digital_discount'     => $digital_discount,
                                'market_general_data'  => $market_general_data,
                                'creditnote_data'      => $creditnote_data,
                                'deposit_data'         => $deposit_data,
                                'delivery_fee'         => $delivery_fee,

                            ); 
                        // echo'<pre>';
                        // print_r($params);
                        // echo '</pre>';
                        $result = $this->saveAPI($params);
                        
                        array_push($tags, $tax_po);

                        if ($result['code'] == 1) { //success 
                            //print_r($result);
                            //update discount when created
                            if($edit != 1){ 
                                $QMarket = new Application_Model_Market();
                                $QDistributor = new Application_Model_Distributor();
                                
                                $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
                                $distributor = $QDistributor->fetchRow($where);

                                // Check Special Discount
                                if(isset($warehouse_id) and $warehouse_id == 71){
                                    $total_discount_digital = $digital_discount + $distributor['spc_discount'];
                                    $spc_discount = $total_discount_digital;
                                }else{
                                    $spc_discount = $distributor['spc_discount'];
                                }
                                
                                $spc_discount_phone = $distributor['spc_discount_phone'];
                                $spc_discount_acc = $distributor['spc_discount_acc'];
                                $spc_discount_digital = $distributor['spc_discount_digital'];

                                if(isset($total_discount_digital) and $total_discount_digital > 0){
                                    $spc_discount_digital = 1;
                                }
                                
                                $array_data = array('spc_discount' => $spc_discount,
                                                'spc_discount_phone' => $spc_discount_phone,
                                                'spc_discount_acc' => $spc_discount_acc,
                                                'spc_discount_digital' => $spc_discount_digital);

                                $where = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                                $QMarket->update($array_data, $where); 
                            }
              
                            // save tags
                            $QTag       = new Application_Model_Tag();
                            $QTagObject = new Application_Model_TagObject();
                    
                            // remove old record on tag_object
                            $where = array();
                            $where[] = $QTagObject->getAdapter()->quoteInto('object_id = ?', $result['sn']);
                            $where[] = $QTagObject->getAdapter()->quoteInto('type = ?', TAG_ORDER);
                    
                            $QTagObject->delete($where);
                    
                            if ($tags and isset($result['sn']) and $result['sn']){
                    
                                foreach ($tags as $t){
                                    
                                    $where = $QTag->getAdapter()->quoteInto('name = ?', $t);
                                    $existed_tag = $QTag->fetchRow($where);
                    
                                    if ($existed_tag){
                    
                                        $tag_id = $existed_tag['id'];
                    
                                    } else {
                    
                                        $tag_id = $QTag->insert(array('name'=>$t));
                    
                                    }
                    
                                    $QTagObject->insert(
                                        array(
                                            'tag_id'    => $tag_id,
                                            'object_id' => $result['sn'], //order sn
                                            'type'      => TAG_ORDER,
                                        )
                                    ); 
                        
                    
                                }
                                
                            }

                        $success_list[] = $params;  
                        $flashMessenger->setNamespace('success')->addMessage($result['message']);

                        }else if ($result['code'] == -5){
                            $error_list[] = $params;
                            $flashMessenger->setNamespace('error')->addMessage($result['message']);
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
             
        }// end foreach
                //run cron gen sn_ref
                file_get_contents(HOST."cron/gen-sn-ref");

                $QFileLog = new Application_Model_FileUploadLog();

                $where = $QFileLog->getAdapter()->quoteInto('id = ?', $log_id);
                $QFileLog->update($data, $where);


                // $this->view->error_list = $error_list;
                $this->view->number_of_order = $number_of_order;
                //commit
                //$db->commit();

                $this->view->error_file = isset($data['error_file_name']) ? (HOST
                    . 'files'
                    . DIRECTORY_SEPARATOR . 'mou'
                    . DIRECTORY_SEPARATOR . $userStorage->id
                    . DIRECTORY_SEPARATOR . $uniqid
                    . DIRECTORY_SEPARATOR . $data['error_file_name']) : false;

                $progress->flush(100);

        }//end of try
        catch (Exception $e) {
            $db->rollback();
            $this->view->error = $e->getMessage();
            $progress->flush(0);
        }

    }


}
    
    

