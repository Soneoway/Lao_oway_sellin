<?php

class PoController extends My_Controller_Action
{
    public function indexAction()
    {
        $sort            = $this->getRequest()->getParam('sort', 'created_at');
        $desc            = $this->getRequest()->getParam('desc', 0);
        $page            = $this->getRequest()->getParam('page', 1);
        $sn              = $this->getRequest()->getParam('sn');
        $created_by      = $this->getRequest()->getParam('created_by');
        $cat_id          = $this->getRequest()->getParam('cat_id');
        $good_id         = $this->getRequest()->getParam('good_id');
        $good_color      = $this->getRequest()->getParam('good_color');
        $payment         = $this->getRequest()->getParam('payment');
        $ship_warehouse  = $this->getRequest()->getParam('ship_warehouse');
        $created_at_to   = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));
        $created_at_from = $this->getRequest()->getParam('created_at_from', date('01/m/Y'));

        $receive_at_to   = $this->getRequest()->getParam('receive_at_to');
        $receive_at_from = $this->getRequest()->getParam('receive_at_from');

        $warehouse_id    = $this->getRequest()->getParam('warehouse_id');
        $type            = $this->getRequest()->getParam('type');
        $export          = $this->getRequest()->getParam('export', 0);

        $limit = LIMITATION;
        $total = 0;

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        // 84 = PO Acc Only
        if (My_Staff_Group::inGroup($userStorage->group_id, array(84))){
            //12 = Accessories
            $cat_id = 12;
        }

        $params = array_filter( array(
            'sn'              => $sn,
            'created_by'      => $created_by,
            'cat_id'          => intval($cat_id),
            'good_id'         => intval($good_id),
            'good_color'      => intval($good_color),
            'warehouse_id'    => intval($warehouse_id),
            'payment'         => $payment,
            'ship_warehouse'  => $ship_warehouse,
            'created_at_to'   => $created_at_to,
            'created_at_from' => $created_at_from,
            'receive_at_to'   => $receive_at_to,
            'receive_at_from' => $receive_at_from,
            'export'          => $export,
            'type'            => intval($type),
        ));

        if ($cat_id){
            $QGood = new Application_Model_Good();
            // $where = $QGood->getAdapter()->quoteInto('cat_id = ?', intval($cat_id));
            // $goods = $QGood->fetchAll($where, 'name');

            $goods = $QGood->getProduct2($params);

            $this->view->goods = $goods;

            if ($good_id){
                //get goods color
                $where = $QGood->getAdapter()->quoteInto('id = ?', intval($good_id));
                $good = $QGood->fetchRow($where);

                $aColor = array_filter(explode(',', $good->color));
                if ($aColor){
                    $QGoodColor = new Application_Model_GoodColor();
                    $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

                    $colors = $QGoodColor->fetchAll($where);
                    $this->view->colors = $colors;
                }
            }
        }

        $params['sort'] = $sort;
        $params['desc'] = $desc;

        $QPO = new Application_Model_Po();

        if ($export){
            $POs = $QPO->fetchPagination($page, null, $total, $params);
            $this->_exportCsv($POs, $export);
        }

        $POs = $QPO->fetchPagination($page, $limit, $total, $params);

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->POs    = $POs;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'po/'.( $params ? '?'.http_build_query($params).'&' : '?' );

        $this->view->offset = $limit*($page-1);

        $QStaff = new Application_Model_Staff();
        $this->view->staffs = $QStaff->get_cache();

        $QWarehouse = new Application_Model_Warehouse();

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $warehouse_type = $userStorage->warehouse_type;

        $warehouses = $QWarehouse->getWarehouses($warehouse_type);
        $warehouse_arr = array();
        
        foreach ($warehouses as $k => $warehouse_data){
            $warehouse_arr[$warehouse_data['id']] = $warehouse_data['name']; 
        } 
        
        // $this->view->warehouses = $QWarehouse->get_cache();
        $this->view->warehouses = $warehouse_arr;

        $QGoodCategory = new Application_Model_GoodCategory();
        $this->view->good_categories = $QGoodCategory->get_cache();

        // 84 = PO Acc Only
        if (My_Staff_Group::inGroup($userStorage->group_id, array(84))){
            $this->view->good_categories = $this->view->good_categories = [12 => 'Accessories'];
        }

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        if($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $this->_helper->viewRenderer->setRender('partials/list');
        }
    }

    public function createAction(){
        $flashMessenger = $this->_helper->flashMessenger;
        if ($this->getRequest()->getMethod() == 'POST'){
            $QPO = new Application_Model_Po();

            $id           = $this->getRequest()->getParam('id');
            $cat_id       = $this->getRequest()->getParam('cat_id');
            $good_id      = $this->getRequest()->getParam('good_id');
            $good_color   = $this->getRequest()->getParam('good_color');
            $warehouse_id = $this->getRequest()->getParam('warehouse_id');
            $num          = $this->getRequest()->getParam('num');
            $price        = $this->getRequest()->getParam('price');
            $text         = $this->getRequest()->getParam('text');
            $sn           = $this->getRequest()->getParam('sn');
            $type         = $this->getRequest()->getParam('type');

            $data = array(
                'cat_id'       => intval($cat_id),
                'good_id'      => intval($good_id),
                'good_color'   => intval($good_color),
                'warehouse_id' => intval($warehouse_id),
                'num'          => intval($num),
                'price'        => My_Number::floatval($price),
                'text'         => strval($text),
                'type'         => intval($type),
            );

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $QLog = new Application_Model_Log();
            //Tanong
            try{
                $running_no ="";
                if ($id){
                    $id = intval($id);
                    $where = $QPO->getAdapter()->quoteInto('id = ?', $id);

                    $data['updated_at'] = date('Y-m-d H:i:s');
                    $data['updated_by'] = $userStorage->id;

                    $QPO->update($data, $where);

                    $info = 'Edit: Purchase order number: '.$sn;

                } else {
                    $sn = date('YmdHis').substr(microtime(),2,4);
                    $running_no = $this->getPoOrderNo_Ref($sn);
                    
                    $data['sn']  = $sn ;    
                    $data['sn_ref']  = $running_no ;  
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $data['created_by'] = $userStorage->id;

                    $QPO->insert($data);

                    $info = 'Add: Purchase order number: '.$data['sn'];

                }

                $ip = $this->getRequest()->getServer('REMOTE_ADDR');

                //todo log
                $QLog->insert( array (
                    'info' => $info,
                    'user_id' => $userStorage->id,
                    'ip_address' => $ip,
                    'time' => date('Y-m-d H:i:s'),
                ) );

                $flashMessenger->setNamespace('success')->addMessage('Done!');

            }catch (Exception $e){
                $flashMessenger->setNamespace('error')->addMessage('Cannot insert, please try again!');
            }


            $this->_redirect( HOST.'po' );
        }

        //edit
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            $id = intval($id);
            $QPo = new Application_Model_Po();
            $rowset = $QPo->find($id);
            $PO = $rowset->current();

            $this->view->PO = $PO;

            //get goods
            $QGood = new Application_Model_Good();
            $where = $QGood->getAdapter()->quoteInto('cat_id = ?', $PO->cat_id);
            $goods = $QGood->fetchAll($where, 'name');

            $this->view->goods = $goods;

            //get goods color
            $where = $QGood->getAdapter()->quoteInto('id = ?', $PO->good_id);
            $good = $QGood->fetchRow($where);

            $aColor = array_filter(explode(',', $good->color));
            if ($aColor){
                $QGoodColor = new Application_Model_GoodColor();
                $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

                $colors = $QGoodColor->fetchAll($where);
                $this->view->colors = $colors;
            }
        }

        $QGoodCategory = new Application_Model_GoodCategory();
        // $this->view->good_categories = $QGoodCategory->fetchAll();
        $this->view->good_categories = $QGoodCategory->get_cache();

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        // 84 = PO Acc Only
        if (My_Staff_Group::inGroup($userStorage->group_id, array(84))){
            $this->view->good_categories = $this->view->good_categories = [12 => 'Accessories'];
        }

        $QWarehouse = new Application_Model_Warehouse();
        $warehouse_type = $userStorage->warehouse_type;
        $warehouses = $QWarehouse->getWarehouses($warehouse_type);
        $warehouse_arr = array();
        
        foreach ($warehouses as $k => $warehouse_data){
            $warehouse_arr[$warehouse_data['id']] = $warehouse_data['name']; 
        } 

        // $this->view->warehouses = $QWarehouse->fetchAll();
        $this->view->warehouses = $warehouse_arr;

        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;


    }

    public function viewAction(){
        //edit staff
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            $QPo = new Application_Model_Po();
            $rowset = $QPo->find($id);
            $PO = $rowset->current();

            $this->view->PO = $PO;

            $QGoodCategory = new Application_Model_GoodCategory();
            $categories = $QGoodCategory->get_cache();

            $this->view->category = isset($categories[$PO->cat_id]) ? $categories[$PO->cat_id] : '';

            //get goods
            $QGood = new Application_Model_Good();
            $goods = $QGood->get_cache();

            $this->view->good = isset($goods[$PO->good_id]) ? $goods[$PO->good_id] : '';

            //get goods color
            $QGoodColor = new Application_Model_GoodColor();
            $goodColors = $QGoodColor->get_cache();

            $this->view->good_color = isset($goodColors[$PO->good_color]) ? $goodColors[$PO->good_color] : '';

            //get goods color
            $QWarehouse = new Application_Model_Warehouse();
            $warehouse = $QWarehouse->get_cache();

            $this->view->warehouse = isset($warehouse[$PO->warehouse_id]) ? $warehouse[$PO->warehouse_id] : '';

            //get username
            $QStaff = new Application_Model_Staff();

            $staffs = $QStaff->get_cache();

            $this->view->created_by_name = isset($staffs[$PO->created_by]) ? $staffs[$PO->created_by] : '';

            $this->view->payer_name = isset($staffs[$PO->flow]) ? $staffs[$PO->flow] : '';

            $this->view->warehousing_name = isset($staffs[$PO->mysql_user]) ? $staffs[$PO->mysql_user] : '';
        }
    }

    public function delAction(){
        $id = $this->getRequest()->getParam('id');

        $QPO = new Application_Model_Po();
        $where = $QPO->getAdapter()->quoteInto('id = ?', $id);
        $PO = $QPO->fetchRow($where);

        $flashMessenger = $this->_helper->flashMessenger;

        if (!$PO){
            $flashMessenger->setNamespace('error')->addMessage('Cannot delete, please try again!');
            $this->_redirect( HOST.'po' );
        }

        if ($PO['flow_time'] or $PO['mysql_time']){
            $flashMessenger->setNamespace('error')->addMessage('This PO is already paid or imported!');
            $this->_redirect( HOST.'po' );
        }

        $QPO->delete($where);

        //todo log
        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        $info = 'Remove: Purchase order number: '.$PO->sn;

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $QLog = new Application_Model_Log();

        $QLog->insert( array (
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => date('Y-m-d H:i:s'),
        ) );

        $this->_redirect( HOST.'po' );
    }

    public function uploadImeiAction()
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting(~E_ALL);
        // config for template
        define('START_ROW', 3);
        define('BOX_COL', 4);
        define('IMEI_1_COL', 5);
        define('IMEI_2_COL', 6);
        define('SHIPPING_TIME_COL', 7);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->getMethod() != 'POST') exit;
        $po_sn = $this->getRequest()->getParam('po_sn');

        if (!$po_sn || empty($po_sn))
            exit(htmlspecialchars(json_encode(array('error' => 'Invalid PO')), ENT_NOQUOTES));

        $po_sn = str_replace('_', '', trim($po_sn));

        $progress = new My_File_Progress('parent.set_progress');
        $progress->flush(0);

        $save_folder   = 'imei_po_list';
        $new_file_path = '';
        $requirement   = array(
            'Size'      => array('min' => 5, 'max' => 5000000),
            'Count'     => array('min' => 1, 'max' => 1),
            'Extension' => array('xls', 'xlsx'),
        );

        try {
            $file = My_File::get($save_folder, $requirement, true);

            if (!$file || !count($file)) throw new Exception("Upload failed");

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $uploaded_dir = My_File::getDefaultDir() .DIRECTORY_SEPARATOR.$save_folder
                . DIRECTORY_SEPARATOR. $userStorage->id
                . DIRECTORY_SEPARATOR. $file['folder'];

            $inputFileName = $uploaded_dir . DIRECTORY_SEPARATOR. $file['filename'];
            $uniqid = $file['folder'];

            $QFileLog = new Application_Model_FileUploadLog();
            $fo       = fopen($inputFileName, 'r');
            $fr       = fread($fo, filesize($inputFileName));
            $where    = $QFileLog->getAdapter()->quoteInto('id = ?', $file['log_id']);
            $hash_content = hash('sha512', md5($file['filename']).$file['real_file_name']);
            $data = array(
                'content'       => $fr,
                'hash'          => $hash_content,
                'download_time' => 0,
            );

            $QFileLog->update($data, $where);

            $result['uniqid'] = $uniqid;
            $result['link'] = HOST.'download?id='.$hash_content;
        } catch (Exception $e) {
            echo htmlspecialchars(json_encode(array('error' => $e->getMessage())), ENT_NOQUOTES);
            exit;
        }

        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        //read file
        include 'PHPExcel/IOFactory.php';

        //  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $path_info     = pathinfo($inputFileName);
            $extension     = $path_info['extension'];
            $objReader     = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel   = $objReader->load($inputFileName);
        } catch (Exception $e) {
            exit($e->getMessage);
            $this->view->errors = $e->getMessage();
            return;
        }

        //  Get worksheet dimensions
        $sheet         = $objPHPExcel->getSheet(0);
        $highestRow    = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $order_rows    = $highestRow - START_ROW;

        $po_file       = array();
        $QCartoonBox   = new Application_Model_CartoonBox();
        $QPoCartoonBox = new Application_Model_PurchaseOrderCartoonBox();
        $QCartoonBoxImei = new Application_Model_CartoonBoxImei();

        for ($row = START_ROW; $row <= $highestRow; $row++) {
            try {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL, TRUE, FALSE);

                // validate data
                $rowData = isset($rowData[0]) ? $rowData[0] : array();

                if (isset($rowData[BOX_COL]) && !empty($rowData[BOX_COL]))
                    $rowData[BOX_COL] = trim($rowData[BOX_COL]);

                $where = array();
                $where[] = $QPoCartoonBox->getAdapter()->quoteInto('cartoon_box_number = ?', $rowData[BOX_COL]);
                $where[] = $QPoCartoonBox->getAdapter()->quoteInto('po_sn <> ?', $po_sn);
                $box_check = $QPoCartoonBox->fetchRow($where);
                if ($box_check) throw new Exception(sprintf("Box exists: %s, in PO: %s", $box_check['cartoon_box_number'], $box_check['po_sn']), 1);

            } catch (Exception $e) {
                $db->rollback();
                echo htmlspecialchars(json_encode(array('error' => $e->getMessage())), ENT_NOQUOTES);
                exit;
            }
        }

        for ($row = START_ROW; $row <= $highestRow; $row++) {
            $percent = round($row * 100 / $order_rows, 1);
            $progress->flush($percent);

            try {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL, TRUE, FALSE);

                // validate data
                $rowData = isset($rowData[0]) ? $rowData[0] : array();

                if (isset($rowData[BOX_COL]) && !empty($rowData[BOX_COL]))
                    $rowData[BOX_COL] = trim($rowData[BOX_COL]);

                // nếu box mới
                if (!isset($po_file[ $rowData[BOX_COL] ])) {
                    $po_file[ $rowData[BOX_COL] ] = array();

                    // tạo cartoon box mới
                    try {
                        $data = array(
                            'cartoon_box_number'  => $rowData[BOX_COL],
                            'file_upload_log_id' => $file['log_id'],
                            'shipping_time'       => isset($rowData[SHIPPING_TIME_COL]) ? $rowData[SHIPPING_TIME_COL] : null,
                        );

                        $QCartoonBox->insert($data);
                    } catch (Exception $e) {}

                    // liên kết po với cartoon box
                    try {
                        $data = array(
                            'po_sn' => $po_sn,
                            'cartoon_box_number' => $rowData[BOX_COL],
                        );

                        $QPoCartoonBox->insert($data);
                    } catch (Exception $e) {}
                }

                if (isset($rowData[IMEI_1_COL]) && !empty($rowData[IMEI_1_COL])) {
                    $rowData[IMEI_1_COL] = trim($rowData[IMEI_1_COL]);

                    // kiểm tra IMEI đã đọc qua chưa
                    if (!in_array($rowData[IMEI_1_COL], $po_file[ $rowData[BOX_COL] ]))
                        $po_file[ $rowData[BOX_COL] ][] = $rowData[IMEI_1_COL];

                    $data = array(
                        'cartoon_box_number' => $rowData[BOX_COL],
                        'imei_sn' => $rowData[IMEI_1_COL],
                    );

                    try {
                        $QCartoonBoxImei->insert($data);
                    } catch (Exception $e) {}
                }
            } catch (Exception $e) {
                $db->rollback();
                echo htmlspecialchars(json_encode(array('error' => $e->getMessage())), ENT_NOQUOTES);
                exit;
            }
            // nothing here
        } // END loop through order rows

        $progress->flush(0);
        $result['success'] = true;
        $db->commit();
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }

    private function _exportExcel($data){
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'ID',
            'PO number',
            'Staff name',
            'Type',
            'Product Category',
            'Product Name',
            'Color',
            'Quantity',
            'Total Price',
            'Payment',
            'Whether to enter the warehouse',
            'Warehouse',
            'Remark',
            'Order time',
        );

        $PHPExcel->setActiveSheetIndex(0);
        $sheet    = $PHPExcel->getActiveSheet();

        $alpha    = 'A';
        $index    = 1;
        foreach($heads as $key)
        {
            $sheet->setCellValue($alpha.$index, $key);
            $alpha++;
        }
        $index    = 2;

        foreach($data as $item){
            $alpha    = 'A';
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['id'] , PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['sn_ref'] , PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue($alpha++.$index, $item['staff_username']);
            $sheet->setCellValue($alpha++.$index, isset(My_Po_Type::$name[$item['type']]) ? My_Po_Type::$name[$item['type']] : '');
            $sheet->setCellValue($alpha++.$index, $item['good_category_name']);
            $sheet->setCellValue($alpha++.$index, $item['good_name']);
            $sheet->setCellValue($alpha++.$index, $item['good_color_name']);
            $sheet->setCellValue($alpha++.$index, $item['num']);
            $sheet->getCell($alpha++.$index)->setValueExplicit(My_Number::f($item['price']) , PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue($alpha++.$index, ($item['flow'] ? 'Yes' : 'No') );
            $sheet->setCellValue($alpha++.$index, ($item['mysql_user'] ? 'Yes' : 'No') );
            $sheet->setCellValue($alpha++.$index, $item['warehouse_name'] );
            $sheet->setCellValue($alpha++.$index, $item['text'] );
            $sheet->setCellValue($alpha++.$index, $item['created_at'] );
            $index++;
        }

        $filename = 'Purchase Order_'.date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');

        exit;
    }

    private function _exportCsv($sql, $type = 'no_imei')
    {
        //echo $sql;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'PO List ' .('all_imei'==$type?' - IMEI':''). ' - '.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'ID',
            'PO number',
            'Staff name',
            'Type',
            'Product Category',
            'Product Name',
            // 'Color',
            'Quantity',
            'Total Price',
            'Payment',
            'Whether to enter the warehouse',
            'Warehouse',
            'Remark',
            'Order time',
            'Receive time',
        );

        if ('all_imei' == $type)
            $heads[] = 'IMEI';

        fputcsv($output, $heads);

        $data = $db->query($sql);

        if (!$data) return;
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        foreach($data as $item){
            $row = array();

            $row[] = '="'.$item['id'].'"';
            $row[] = '="'.$item['sn_ref'].'"';
            $row[] = $item['staff_username'];
            $row[] = isset(My_Po_Type::$name[$item['type']]) ? My_Po_Type::$name[$item['type']] : '';
            $row[] = $item['good_category_name'];
            $row[] = $item['brand_name'].' '.$item['good_name'].' '.$item['good_color_name'];
            // $row[] = 
            $row[] = $item['num'];

            
            if (My_Staff_Group::inGroup($userStorage->group_id, PO_SHOW_PRICE) || $userStorage->group_id == ADMINISTRATOR_ID || HUMAN_RH_ID ) 
            {
                $row[] = '="'.My_Number::f($item['price']).'"';
            }else{
                $row[] = 'xxxx';
            }

            $row[] = $item['flow'] ? 'Yes' : 'No';
            $row[] = $item['mysql_user'] ? 'Yes' : 'No';
            $row[] = $item['warehouse_name'];
            $row[] = $item['text'];
            $row[] = date('Y-m-d',strtotime($item['created_at']));
            $row[] = date('Y-m-d',strtotime($item['mysql_time']));
            

            if ('all_imei' ==  $type){
                $row[] = '="'.$item['imei_sn'].'"';
            }


            fputcsv($output, $row);
            unset($row);
            unset($item);
        }

        unset($data);
    }

    public function cartoonBoxAction()
    {
        $imei  = $this->getRequest()->getParam('imei');
        $po_sn = $this->getRequest()->getParam('po_sn');
        $cartoon_box_number = $this->getRequest()->getParam('cartoon_box_number');

        $po_from       = $this->getRequest()->getParam('po_from', date('01/m/Y'));
        $po_to         = $this->getRequest()->getParam('po_to', date('d/m/Y'));
        $shipping_from = $this->getRequest()->getParam('shipping_from');
        $shipping_to   = $this->getRequest()->getParam('shipping_to');

        $export = $this->getRequest()->getParam('export', 0);
        $page   = $this->getRequest()->getParam('page', 1);
        $limit  = LIMITATION;
        $total  = 0;

        $imei_list = My_String::stringlistToArray($imei);
        $box_list  = My_String::stringlistToArray($cartoon_box_number);
        $po_list   = My_String::stringlistToArray($po_sn);

        $params = array(
            'imei'               => $imei_list,
            'po_sn'              => $po_list,
            'cartoon_box_number' => $box_list,
            'po_from'            => $po_from,
            'po_to'              => $po_to,
            'shipping_from'      => $shipping_from,
            'shipping_to'        => $shipping_to,
            'export'             => $export,
        );

        $QCartoonBox = new Application_Model_CartoonBox();
        $this->view->boxes = $QCartoonBox->fetchPagination($page, $limit, $total, $params);

        $params['imei'] = implode("\r\n", $params['imei']);
        $params['po_sn'] = implode("\r\n", $params['po_sn']);
        $params['cartoon_box_number'] = implode("\r\n", $params['cartoon_box_number']);

        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->page   = $page;
        $this->view->offset = $limit*($page-1);
        $this->view->params = $params;
        $this->view->url    = HOST.'po/cartoon-box'.( $params ? '?'.http_build_query($params).'&' : '?' );
    }

    public function loadBoxAction()
    {
        $po_sn = $this->getRequest()->getParam('po_sn');

        try {
            if (!$po_sn) throw new Exception("Empty PO SN", 1);
            $po_sn = str_replace('_', '', $po_sn);

            $QPoCartoonBox = new Application_Model_PurchaseOrderCartoonBox();
            $where = $QPoCartoonBox->getAdapter()->quoteInto('po_sn = ?', $po_sn);
            $boxes = $QPoCartoonBox->fetchAll($where);

            $data = array();

            if ($boxes)
                foreach ($boxes as $box)
                    $data[] = $box['cartoon_box_number'];

            exit(json_encode(array('boxes' => $data)));
        } catch (Exception $e) {
            exit(json_encode(array('error' => $e->getMessage())));
        }
    }

    public function loadImeiAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $cartoon_box_number = $this->getRequest()->getParam('box_number');

        try {
            if (!$cartoon_box_number) throw new Exception("Invalid cartoon box number", 1);
            $cartoon_box_number = str_replace('_', '', $cartoon_box_number);

            $QCartoonBoxImei = new Application_Model_CartoonBoxImei();
            $where = $QCartoonBoxImei->getAdapter()->quoteInto('cartoon_box_number = ?', $cartoon_box_number);
            $imeis = $QCartoonBoxImei->fetchAll($where);

            $data = array();

            if ($imeis)
                foreach ($imeis as $key => $value)
                    $data[] = $value['imei_sn'];

            exit(json_encode(array('imeis' => $data)));
        } catch (Exception $e) {
            exit(json_encode(array('error' => $e->getMessage())));
        }
    }

    public function loadLinkAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        try {
            $po_list = $this->getRequest()->getParam('po_list');
            if (!$po_list || !is_array($po_list)) throw new Exception("Invalid PO SN", 1);

            $po_sn = array();
            foreach ($po_list as $key => $value) {
                $po_sn[] = str_replace('_', '', $value);
            }

            $QCartoonBox = new Application_Model_CartoonBox();
            $result = $QCartoonBox->getLinks($po_sn);

            exit(json_encode($result));
        } catch (Exception $e) {
            exit(json_encode(array('error' => $e->getMessage())));
        }


    }

    public function cartoonBoxImeiAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $cartoon_box_number = $this->getRequest()->getParam('cartoon_box_number');

        try {
            if (!$cartoon_box_number) throw new Exception("Invalid cartoon box number", 1);

            $QCartoonBoxImei = new Application_Model_CartoonBoxImei();
            $where = $QCartoonBoxImei->getAdapter()->quoteInto('cartoon_box_number = ?', $cartoon_box_number);
            $imeis = $QCartoonBoxImei->fetchAll($where);

            $data = array();

            if ($imeis)
                foreach ($imeis as $key => $value)
                    $data[] = $value['imei_sn'];

            exit(json_encode(array('imeis' => $data)));
        } catch (Exception $e) {
            exit(json_encode(array('error' => $e->getMessage())));
        }
    }

    //Tanong Get PONo Ref 20160313 1155
    public function getPoOrderNo_Ref($sn)
    {
        $running_no="";
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_new_running_no_ref('PO',".$sn.")");
            //$stmt = $db->prepare("CALL gen_running_no_ref('SO',201603121740314924)");
            $stmt->execute();
            $res = $stmt->fetchAll();
            $running_no= $res[0]['running_no'];
        }catch (exception $e) {
            $flashMessenger = $this->_helper->flashMessenger;
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get PO No, please try again!');
        }
        return $running_no;
    }
}
