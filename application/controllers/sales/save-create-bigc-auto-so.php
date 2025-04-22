
<?php
echo '<link href="/css/alert-error-success.css" rel="stylesheet">'; //alert message

    $this->_helper->layout->disableLayout();
    $flashMessenger = $this->_helper->flashMessenger;

    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $messages_success = $flashMessenger->setNamespace('success')->getMessages();

        
    if ($this->getRequest()->getMethod() == 'POST'){

        // print_r($_POST);die;

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
                    
                echo '<div class="alert_error"><strong>ERROR! </strong>'.$sError."</div>";
                // $this->view->error = $sError;
               
            } else {
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
                        $bigc_data = array();
                        for($row = 2; $row <= $highestRow; ++$row){
                             $dataRow = $objWorksheet->rangeToArray('A1:'.$highestColumn.$row,null,true,true,true);
                            if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')){
                                ++$r;
                                foreach ($headingsArray as $columkey => $columHeading) {
                                    $bigc_data[$r][$columHeading] = $dataRow[$row][$columkey];
                                }
                            }

                        }
                        // echo'<pre>';
                        // print_r($bigc_data); die;
                        // echo'</pre>';
                        //-----------

                        foreach ($bigc_data as $key => $value) 
                        { 
                            $no  = trim($value["NO"]);
                            $order_type = trim($value["Doc Type"]);
                            $d_id       = trim($value["Dritibuter ID"]);
                            $good_id    = trim($value["Product Name"]);
                            $good_color = trim($value["Product Color"]);
                            $qty            = trim($value["Quality"]);
                            // $sale_off_percent = trim($value["Sale off"]);
                            // $unit_price       = trim($value["UnitPrice"]);
                            // $total_price      = trim($value["TotalPrice"]);
                            // $total_spc_discount = trim($value["Spc Discount"]);
                            $tax_po     = trim($value["Tax PO"]);
                            $ship_address   = trim($value["Shipping address"]);

                            if($good_id == ''){
                                echo '<div class="alert_error"><strong>ERROR! </strong>'
                                 ."กรุณาใส่ Key Product Name แถวลำดับที่: $no"."</div>"; 
                                return;
                            }else if($good_color==''){
                                echo '<div class="alert_error"><strong>ERROR! </strong>'
                                 ."กรุณาใส่ Key Product Color แถวลำดับที่: $no"."</div>"; 
                                return;
                            }else if($tax_po==''){
                                echo '<div class="alert_error"><strong>ERROR! </strong>'
                                 ."กรุณาใส่ Key Tax Po แถวลำดับที่: $no"."</div>"; 
                                return;
                            }else if($order_type==''){
                                echo '<div class="alert_error"><strong>ERROR! </strong>'
                                 ."กรุณาใส่ Key Doc Type แถวลำดับที่: $no"."</div>"; 
                                return;
                            }else if($ship_address==''){
                                echo '<div class="alert_error"><strong>ERROR! </strong>'
                                 ."กรุณาใส่ Key Shipping address แถวลำดับที่: $no"."</div>"; 
                                return;
                            }

                        //  if($good_id=="CPH1723"){
                        //     $good_id=299;    
                        // }else if($good_id=="CPH1725"){
                        //     $good_id=300;    
                        // }else if($good_id=="A37fw"){
                        //     $good_id=255;    
                        // }else if($good_id=="CPH1613"){
                        //     $good_id=267;    
                        // }else if($good_id=="CPH1717"){
                        //     $good_id=266;    
                        // }else if($good_id=="CPH1727"){
                        //     $good_id=301;    
                        // }else if($good_id=="CPH1607"){
                        //     $good_id=208;    
                        // }else if($good_id=="CPH1611"){
                        //     $good_id=210;    
                        // }else if($good_id=="CPH1715"){
                        //     $good_id=256;    
                        // }

                        $QGood = new Application_Model_Good();
                        $where = $QGood->getAdapter()->quoteInto('name =?',$good_id);
                        $name_good_id = $QGood->fetchRow($where);

                        if(!$name_good_id){
                             echo '<div class="alert_error"><strong>ERROR! </strong>'
                             ."ไม่พบ Product Name: $good_id นี้"."</div>"; 
                            return;
                        }

                        $good_id = $name_good_id['id'];
                        $unit_price = $name_good_id['price_3'];
                        // echo $unit_price; die;

                        if($good_color=="G"){
                            $good_color=12;    
                        }else if($good_color=="BK"){
                            $good_color=2;    
                        }else if($good_color=="GD"){
                            $good_color=12;    
                        }else if($good_color=="RG"){
                            $good_color=24;    
                        }else if($good_color=="R"){
                            $good_color=7;    
                        }else if($good_color=="SV"){
                            $good_color=3;
                        }else if($good_color=="GY"){
                            $good_color=5;
                        }else if($good_color=="CM"){
                            $good_color=6;
                        }else if($good_color=="P"){
                            $good_color=8;
                        }else if($good_color=="BL"){
                            $good_color=9;
                        }else if($good_color=="BL LIMITED"){
                            $good_color=10;
                        }else if($good_color=="PP"){
                            $good_color=11;
                        }else if($good_color=="GREY LIMI"){
                            $good_color=14;
                        }else if($good_color=="Y"){
                            $good_color=16;
                        }else if($good_color=="GR"){
                            $good_color=17;
                        }else if($good_color=="CLEAR"){
                            $good_color=18;
                        }else if($good_color=="GLASS"){
                            $good_color=19;
                        }else if($good_color=="ORIGINAL"){
                            $good_color=20;
                        }else if($good_color=="OR"){
                            $good_color=21;
                        }else if($good_color=="BWN"){
                            $good_color=22;
                        }else if($good_color=="MATTE"){
                            $good_color=23;
                        }else if($good_color=="M"){
                            $good_color=25;
                        }else if($good_color=="FE"){
                            $good_color=26;
                        }else if($good_color=="HENY"){
                            $good_color=27;
                        }else if($good_color=="PUPY"){
                            $good_color=28;
                        }else if($good_color=="SEA HORSE"){
                            $good_color=29;
                        }else if($good_color=="PIGY"){
                            $good_color=30;
                        }else if($good_color=="WOOD"){
                            $good_color=32;
                        }else if($good_color=="CREAM"){
                            $good_color=33;
                        }else if($good_color=="TRIGON"){
                            $good_color=34;
                        }else if($good_color=="RHOMB"){
                            $good_color=35;
                        }else if($good_color=="MIRROR BLACK"){
                            $good_color=36;
                        }else if($good_color=="MIDNIGHT BLACK"){
                            $good_color=37;
                        }else{
                            echo '<div class="alert_error"><strong>ERROR! </strong>'
                             ."ไม่พบ Product Color $good_color นี้"."</div>"; 
                            return;
                        }



                        if($order_type=="APK"){
                            $order_type=5;  
                            $cat_ids = array('0'    => 11);
                            $good_ids = array('0'    => $good_id);
                            $good_colors = array('0'    => $good_color);
                            $nums = array('0'    => $qty);
                            $prices = array('0'    => $unit_price);
                            $sale_off_percents = array('0'    => $sale_off_percent);
                            $totals = array('0'    => $total_price);  
                        }else if($order_type=="RETAILER"){
                            $order_type=1;
                            $cat_ids = array('0'    => 11,'1'    => 12);
                            $good_ids = array('0'    => $good_id,'1'    => 127);
                            $good_colors = array('0'    => $good_color,'1'    => 1);
                            $nums = array('0'    => $qty,'1'    => 1);
                            $prices = array('0'    => $unit_price,'1'    => 0);
                            $sale_off_percents = array('0'    => $sale_off_percent,'1'    => 100);
                            $totals = array('0'    => $total_price,'1'    => 0);
                        }

                        $QTag = new Application_Model_Tag();
                        $QTagObject = new Application_Model_TagObject();

                        $where = array();
                        $where[] = $QTag->getAdapter()->quoteInto('name =?',$tax_po);
                        $t_tax   = $QTag->fetchRow($where);
                        // print_r($t_tax); die;
                        $where = array();
                        $where[] = $QTagObject->getAdapter()->quoteInto('tag_id =?'
                            ,$t_tax['id']);
                        $where[] = $QTagObject->getAdapter()->quoteInto('type =?'
                            ,$order_type);
                        $t_type  = $QTagObject->fetchRow($where);
                        // print_r($t_type); die;

                        if(!$t_type){
                            $messages ='PO เลขนี้มี imei มากกว่าหนึ่งประเภท';
                             echo '<div class="alert_error"><strong>ERROR! </strong>'
                             .$messages."</div>"; 
                            return;
                        }

                    
                        /*------------------Start------------------------*/
                        // $where  = $QDistributor->getAdapter()->quoteInto('id =?', $tax_po);
                        // $id_dis = $QDistributor->fetchRow($where);
                        // $d_id   = $id_dis['id'];
                        // echo $d_id; die;
                        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $d_id);
                        $distributor = $QDistributor->fetchRow($where);

                        $market_general_id=0;
                        $ids = array('0'    => '');
                        $tags = array('0'    => $tax_po);

                        //$type=5;
                        $salesman=$userStorage->id;
                        $rank=6;
                        $SearchBox='';
                        $distributor_id=$d_id;
                        $warehouse_id=36;
                        $sipping_add=$ship_address;
                        $life_time=1;
                        $include_shipping_fee=1;
                        $user_uncheck=0;
                        /*$cat_ids = array('0'    => 11);
                        $good_ids = array('0'    => $good_id);
                        $good_colors = array('0'    => $good_color);
                        $nums = array('0'    => $qty);
                        $prices = array('0'    => $unit_price);
                        $sale_off_percents = array('0'    => $sale_off_percent);
                        $totals = array('0'    => $total_price);*/
                        $texts = array('0'    => '');

                        $campaign = array('0'    => '');

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
                                'market_general_data'    => $market_general_data,
                                'creditnote_data'    => $creditnote_data,
                                'deposit_data'    => $deposit_data,
                                'shipping_address'    => $ship_address,
                            );

                        // print_r($params);die;
                       // $QDistributor = new Application_Model_Distributor();
                       // $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
                       // $distributor = $QDistributor->fetchRow($where);

                        $result = $this->saveAPI($params);
                        // print_r($result); die;
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

                    //run cron gen sn_ref
                    file_get_contents(HOST."cron/gen-sn-ref");

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
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_TYPE, 1, 'Order Type');
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DEALER, 1, 'Dritibuter ID');
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_PRODUCT_CODE, 1, 'Product Name');
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_PRODUCT_COLOR, 1, 'Product Color');
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_PRODUCT_COLOR + 1, 1, 'Messages');


                            // các dòng lỗi
                            $i = 2;
                            foreach ($error_list as $key => $row) {
                                $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_TYPE, $i, $row['order_type']);
                                $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DEALER, $i, $row['d_id']);
                                $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_PRODUCT_CODE, $i, $row['good_id']);
                                $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_PRODUCT_COLOR, $i, $row['good_color']);
                                $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_PRODUCT_COLOR +1, $i, $row['message']);
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
                        //$db->commit();

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


    
    

