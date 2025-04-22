<?php

class WarehouseController extends My_Controller_Action
{
    public function inAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'in.php';
    }

    public function outAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'out.php';
    }

    public function printPickingListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'print-picking-list.php';
    }

    public function poConfirmAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'po-confirm.php';
    }

    public function poRollbackAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'po-rollback.php';
    }

    public function poRemoveAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'po-remove.php';
    }

    public function addImeiAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'add-imei.php';
    }

    public function addImeiInActAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'add-imei-in-act.php';
    }

    public function addImeiOutAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'add-imei-out.php';
    }

    public function addImeiOutActAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'add-imei-out-act.php';
    }

    public function salesConfirmAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'sales-confirm.php';
    }

    public function listGoodAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'list-good.php';
    }

    public function listInvoiceAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'list-invoice.php';
    }

    public function productOutAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'product-out.php';
    }

    public function productOutArchivedAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'product-out-archived.php';
    }

    public function productOutPrintAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'product-out-print.php';
    }

    public function returnConfirmAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'return-confirm.php';
    }

    public function bvgCreateAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'bvg-create.php';
    }

    public function returnListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'return-list.php';
    }

    public function changeSalesAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'change-sales.php';
    }

    public function changeSalesCompleteAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'change-sales-complete.php';
    }

    public function changeSalesReceiveAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'change-sales-receive.php';
    }

    public function changeSalesListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'change-sales-list.php';
    }

    public function changeSalesViewAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'change-sales-view.php';
    }

    public function rollbackAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'rollback.php';
    }

    public function returnBackSalesAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'return-back-sales.php';
    }

    public function badBackSalesAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'bad-back-sales.php';
    }

    public function listAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'list.php';
    }

    public function warehouseCreateAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'warehouse-create.php';
    }

    public function warehouseSaveAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'warehouse-save.php';
    }

    public function warehouseDeleteAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'warehouse-delete.php';
    }

    public function storageAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'storage.php';
    }

    public function storageAllAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'storage-all.php';
    }

    //in hoa don voi day du phu kien
    public function invoiceAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'invoice.php';
    }

    public function invoiceLaserAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'invoice-laser.php';
    }

    public function invoiceVtAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'invoice-vt.php';
    }

    public function invoiceDmclAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'invoice-dmcl.php';
    }

    public function shapeAction()
    {

    }

    // update shape shield
    public function shapeActAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'shape-act.php';
    }

    public function badImeiManagementAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'bad-imei-management.php';
    }

    public function badAccessoriesManagementAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'bad-accessories-management.php';
    }

    public function addBadAccessoriesAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'add-bad-accessories.php';
    }

    public function invoiceNoAccessoriesAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'invoice-no-accessories.php';
    }

    public function invoiceAddAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'invoice-add.php';
    }

    public function customerSaveAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'customer-save.php';
    }

    public function customerInvoiceAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'customer-invoice.php';
    }

    public function internalInvoiceAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'internal-invoice.php';
    }

    public function internalPrintAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'internal-print.php';
    }

    public function internalNumberAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'internal-number.php';
    }

    public function internalResetAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'internal-reset.php';
    }

    public function customerPrintAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'customer-print.php';
    }

    public function mobilizationOrderAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'mobilization-order.php';
    }

    public function mobilizationListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'mobilization-list.php';
    }

    // hóa đơn phiên bản test
    public function  invoiceFinalAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'invoice-final.php';
    }

    private function  _exportInternalExcel($data)
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'internal-export.php';
    }

    public function stockCardAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'stock-card.php';
    }

    public function stockCardPrintAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'stock-card-print.php';
    }

    private function _exportChangeSaleListExcel($data)
    {
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'No.',
            'CHANGED SN',
            'QUANTITY',
            'COMPLETED',
            'OLD',
            'NEW',
            'CREATED BY',
            'CREATED AT'
        );


        $PHPExcel->setActiveSheetIndex(0);
        $sheet = $PHPExcel->getActiveSheet();

        $alpha = 'A';
        $index = 1;
        foreach ($heads as $key) {
            $sheet->setCellValue($alpha . $index, $key);
            $alpha++;
        }
        $index = 2;


        $QDistributor = new Application_Model_Distributor();
        $QStaff = new Application_Model_Staff();

        $distributors = $QDistributor->get_cache();
        $staff = $QStaff->get_cache();


        $i = 1;
        $markets = array();

        foreach ($data as $item) {
            $alpha = 'A';
            $sheet->setCellValue($alpha++ . $index, $i++);
            $sheet->getCell($alpha++ . $index)->setValueExplicit($item['changed_sn'], PHPExcel_Cell_DataType::TYPE_STRING);

            if (isset($distributors) && isset($distributors[$item['old_id']]))
                $old_id = $distributors[$item['old_id']];

            if (isset($distributors) && isset($distributors[$item['new_id']]))
                $new_id = $distributors[$item['new_id']];

            if (isset($staff) && isset($staff[$item['created_by']]))
                $created_by = $staff[$item['created_by']];

            $sheet->setCellValue($alpha++ . $index, $item['total_qty']);

            $sheet->setCellValue($alpha++ . $index, $item['completed']);
            $sheet->setCellValue($alpha++ . $index, $old_id);
            $sheet->setCellValue($alpha++ . $index, $new_id);
            $sheet->setCellValue($alpha++ . $index, $created_by);
            $sheet->setCellValue($alpha++ . $index, $item['created_at']);
            $index++;
        }

        $filename = 'List_Change_Sales_In_' . date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');

        exit;
    }

    //end function

    private function _exportStorage($data, $warehouse_id = null)
    {   

        set_time_limit(0);

        error_reporting(0);

        ini_set('display_error', 0);

        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();

        $filename = 'STORAGE_' . date('YmdHis');

        $heads = array(
            'Name',
            'Color',
            'Category',
            'Brand',
            'Product Details',
            'Current Order',
            'Current Change Order',
            'Storage ( Available for sale )',
            'Storage ( For Demo )',
            'Storage ( Unavailable for sale )',
            'Total Storage',
            'Available Stotck'
        );

        $PHPExcel->setActiveSheetIndex(0);
        $sheet = $PHPExcel->getActiveSheet();

        $alpha = 'A';
        $index = 1;
        foreach ($heads as $key) {
            $sheet->setCellValue($alpha . $index, $key);
            $alpha++;
        }
        $index = 2;

        $QGoodColor = new Application_Model_GoodColor();
        $goodColors = $QGoodColor->get_cache();

        $QGoodCategory = new Application_Model_GoodCategory();
        $goodCategories = $QGoodCategory->get_cache();

        $QBrand = new Application_Model_Brand();
        $brands = $QBrand->get_cache();

        foreach ($data as $item) {
            $alpha = 'A';

            $current_order = $item['current_order'];
            $current_change_order = $item['current_change_order'];
            $demo = $bad = $count = 0;

            if ($item['cat_id'] == PHONE_CAT_ID) {
                $bad = ($item['imei_bad_count'] ? $item['imei_bad_count'] : 0);
                $demo = ($item['imei_demo_count'] ? $item['imei_demo_count'] : 0);
                $count = ($item['imei_count'] ? $item['imei_count'] : 0);
            } elseif ($item['cat_id'] == ILIKE_CAT_ID) {
                $bad = ($item['ilike_bad_count'] ? $item['ilike_bad_count'] : 0);
                $count = ($item['ilike_count'] ? $item['ilike_count'] : 0);
            } elseif ($item['cat_id'] == DIGITAL_CAT_ID) {
                $bad = ($item['digital_bad_count'] ? $item['digital_bad_count'] : 0);
                $count = ($item['digital_count'] ? $item['digital_count'] : 0);
            } else {
                $bad = ($item['damage_product_count'] ? $item['damage_product_count'] : 0);
                $count = ($item['product_count'] ? $item['product_count'] : 0);
            }

            $available = intval($count) - intval($current_order) - intval($current_change_order);

            $sheet->setCellValue($alpha++ . $index, $item['name']);
            $sheet->setCellValue($alpha++ . $index, (isset($goodColors[$item['good_color_id']]) ? $goodColors[$item['good_color_id']] : ''));
            $sheet->setCellValue($alpha++ . $index, (isset($goodCategories[$item['cat_id']]) ? $goodCategories[$item['cat_id']] : ''));
            $sheet->setCellValue($alpha++ . $index, (isset($brands[$item['brand_id']]) ? $brands[$item['brand_id']] : ''));
            $sheet->setCellValue($alpha++ . $index, $item['desc']);

            $sheet->setCellValue($alpha++ . $index, $item['current_order']);
            $sheet->setCellValue($alpha++ . $index, $item['current_change_order']);

            $sheet->setCellValue($alpha++ . $index, $count);
            $sheet->setCellValue($alpha++ . $index, $demo);
            $sheet->setCellValue($alpha++ . $index, $bad);
            $total_storage = $demo + $bad + $count;
            $sheet->setCellValue($alpha++ . $index, ($total_storage ? $total_storage : 0));
            $sheet->setCellValue($alpha++ . $index, ($available > 0 ? $available : 0));
            $index++;


        }

        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');

        exit;
    }
    //create by PundPond Export  stock IMEI 
    private function _exportExcel4($data)
    {
         
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'IMEI Stock'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'IMEI',
            'MODEL',
            'COLOR',
            'WAREHOUSE'
            
        );
                fputcsv($output, $heads);


        $QGoodColor = new Application_Model_GoodColor();
        $goodColors = $QGoodColor->get_cache();

        $QGoodCategory = new Application_Model_GoodCategory();
        $goodCategories = $QGoodCategory->get_cache();

        $QGood= new Application_Model_Good();
        $goodName = $QGood->get_cache();

        $QBrand = new Application_Model_Brand();
        $brands = $QBrand->get_cache();

        $QWarehouse = new Application_Model_Warehouse();
        $warehouses = $QWarehouse->get_cache();
       
     
        $i = 2;
        
        foreach($data as $item)

        {
             if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
            else { $temp_sn = $item['sn_ref']; }

           
            $row = array();
            $row[] = $item['imei_sn'];
            $row[] = $goodName[$item['good_id']];
            $row[] = (isset($goodColors[$item['good_color']]) ? $goodColors[$item['good_color']] : '');
            $row[] = (isset($warehouses[$item['warehouse_id']]) ? $warehouses[$item['warehouse_id']] : '');
            

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }
    private function _exportExcelProductOut($data)
    {
         
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Export_product_out_' . date('YmdHis') .'.xls';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/xls; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'SALE ORDER NUMBER',
            'PRODUCT CATEGORY',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'QUANTITY',
            'PRICE',
            'ORDER TYPE',
            'WAREHOUSE',
            'RETAILER CODE',
            'RETAILER NAME',
            'INVOICE NUMBER',
            'AREA',
            'PROVINCE',
            'DISTRICT',
            'OUT TIME',
            'FINANCE CONFIRM',
            'NOTE'
            
        );
                fputcsv($output, $heads);


        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QDistributor = new Application_Model_Distributor();
        $QGoodCategory = new Application_Model_GoodCategory();
        $QWarehouse = new Application_Model_Warehouse();

        $goods = $QGood->get_cache();
        $goodColors = $QGoodColor->get_cache();
        $distributors = $QDistributor->get_cache();
        $good_categories = $QGoodCategory->get_cache();
        $warehouses = $QWarehouse->get_cache();
        //$result = $db->query($data);
     
        $i = 2;

        foreach($data as $item)

        {
             if (isset($distributors) && isset($distributors[$item['d_id']]))
                $distributor = $distributors[$item['d_id']];
            else
                $distributor = '';

            $where = $QDistributor->getAdapter()->quoteInto('id = ?', $item['d_id']);
            $distributors_list = $QDistributor->fetchRow($where);

            $type = '';
            switch ($item['type']) {
                case '1':
                    $type = 'Retailer';
                    break;
                case '2':
                    $type = 'Demo';
                    break;
                case '3':
                    $type = 'Staff';
                    break;
                default:
                    # code...
                    break;
            }
     
            $row = array();
            $row[] = $item['sn'];
            $row[] = $good_categories[$item['cat_id']];
            $row[] = $goods[$item['good_id']];
            $row[] = $goodColors[$item['good_color']];
            $row[] = $item['num'];
            $row[] = $item['total'];
            $row[] = $type;
            $row[] = isset($warehouses[$item['warehouse_id']]) ? $warehouses[$item['warehouse_id']] : '';
            $row[] = $distributors_list['store_code'];
            $row[] = $distributor;
            $row[] = $item['invoice_number'];
            
            $row[] = (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::Area) : '');
            $row[] = (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::Province) : '');            
            $row[] = (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::District) : '');           
            $row[] = $item['outmysql_time'];
            $row[] = $item['pay_time'];
            $row[] = $item['text'];
            


            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }// end

    private function _exportExcel($data)
    {
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'No.',
            'Name',
        );

        $PHPExcel->setActiveSheetIndex(0);
        $sheet = $PHPExcel->getActiveSheet();

        $alpha = 'A';
        $index = 1;
        foreach ($heads as $key) {
            $sheet->setCellValue($alpha . $index, $key);
            $alpha++;
        }
        $index = 2;

        $QWarehouse = new Application_Model_Warehouse();
        $warehouses = $QWarehouse->get_cache();


        $i = 1;

        foreach ($data as $item) {
            $alpha = 'A';
            $sheet->setCellValue($alpha++ . $index, $i++);
            $sheet->setCellValue($alpha++ . $index, $item['name']);
            $index++;

        }

        $filename = 'Warehouse_list_' . date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');

        exit;
    }

    private function _exportOutXML($data)
    {
        require_once 'ExcelWriterXML.php';
        set_time_limit(0);
        ini_set('memory_limit', -1);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);

        $filename = 'Export_product_out_' . date('YmdHis') . '.xml';
        $xml = new ExcelWriterXML($filename);
        $xml->docAuthor('OPPO Vietnam');

        $xml->sendHeaders();

        $xml->stdOutStart();

        $sheet = $xml->addSheet('Product OUT');


        $heads = array(
            'SALE ORDER NUMBER',
            'PRODUCT CATEGORY',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'QUANTITY',
            'PRICE',
            'ORDER TYPE',
            'WAREHOUSE',
            'RETAILER CODE',
            'RETAILER NAME',
            'INVOICE NUMBER',
            'AREA',
            'PROVINCE',
            'DISTRICT',
            'OUT TIME',
            'FINANCE CONFIRM',
            'NOTE',
        );

        $sheet->stdOutSheetStart();


        $sheet->stdOutSheetRowStart(1);
        foreach ($heads as $k => $item) {
            $sheet->stdOutSheetColumn('String', 1, $k + 1, $item);
        }
        $sheet->stdOutSheetRowEnd();

        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QDistributor = new Application_Model_Distributor();
        $QGoodCategory = new Application_Model_GoodCategory();
        $QWarehouse = new Application_Model_Warehouse();

        $goods = $QGood->get_cache();
        $goodColors = $QGoodColor->get_cache();
        $distributors = $QDistributor->get_cache();
        $distributors2_cached = $QDistributor->get_cache2();
        $good_categories = $QGoodCategory->get_cache();
        $warehouses = $QWarehouse->get_cache();

        //load distributor


        //load config
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        $config = $config->toArray();
        $con = mysqli_connect($config['resources']['db']['params']['host'], $config['resources']['db']['params']['username'], $config['resources']['db']['params']['password'], $config['resources']['db']['params']['dbname']);
        mysqli_set_charset($con, "utf8");

        $result = mysqli_query($con, $data);

        $i = 2;

        while ($item = mysqli_fetch_assoc($result)) {
            $sheet->stdOutSheetRowStart($i);

            $j = 1;

            if (isset($distributors) && isset($distributors[$item['d_id']]))
                $distributor = $distributors[$item['d_id']];
            else
                $distributor = '';

            $where = $QDistributor->getAdapter()->quoteInto('id = ?', $item['d_id']);
            $distributors_list = $QDistributor->fetchRow($where);

            $type = '';
            switch ($item['type']) {
                case '1':
                    $type = 'Retailer';
                    break;
                case '2':
                    $type = 'Demo';
                    break;
                case '3':
                    $type = 'Staff';
                    break;
                default:
                    # code...
                    break;
            }

            $sheet->stdOutSheetColumn('String', $i, $j++, $item['sn']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $good_categories[$item['cat_id']]);
            $sheet->stdOutSheetColumn('String', $i, $j++, $goods[$item['good_id']]);
            $sheet->stdOutSheetColumn('String', $i, $j++, $goodColors[$item['good_color']]);
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['num']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['total']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $type);
            $sheet->stdOutSheetColumn('String', $i, $j++, (isset($warehouses[$item['warehouse_id']]) ? $warehouses[$item['warehouse_id']] : ''));
            $sheet->stdOutSheetColumn('String', $i, $j++, $distributors_list['store_code']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $distributor);
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['invoice_number']);
            $sheet->stdOutSheetColumn('String', $i, $j++, (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::Area) : ''));
            $sheet->stdOutSheetColumn('String', $i, $j++, (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::Province) : ''));
            $sheet->stdOutSheetColumn('String', $i, $j++, (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::District) : ''));
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['outmysql_time']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['text']);


            $sheet->stdOutSheetRowEnd();

            $i++;
        }

        $sheet->stdOutSheetEnd();

        $xml->stdOutEnd();

        exit;

    }

    private function _exportBadXML($data)
    {
        require_once 'ExcelWriterXML.php';
        set_time_limit(0);
        ini_set('memory_limit', -1);
        error_reporting(0);
        ini_set('display_error', 0);

        $filename = 'Export_Bad_IMEI_' . date('YmdHis');
        $xml = new ExcelWriterXML($filename);
        $xml->docAuthor('OPPO Vietnam');

        $xml->sendHeaders();

        $xml->stdOutStart();

        $sheet = $xml->addSheet('Bad IMEI');


        $heads = array(
            'IMEI',
            'PO SN',
            'RETURN SN',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'SHAPE',
            'STATUS',
            'WAREHOUSE',
        );

        $sheet->stdOutSheetStart();


        $sheet->stdOutSheetRowStart(1);
        foreach ($heads as $k => $item) {
            $sheet->stdOutSheetColumn('String', 1, $k + 1, $item);
        }
        $sheet->stdOutSheetRowEnd();

        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QWarehouse = new Application_Model_Warehouse();

        $goods = $QGood->get_cache();
        $goodColors = $QGoodColor->get_cache();
        $warehouses = $QWarehouse->get_cache();

        //load config
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        $config = $config->toArray();
        $con = mysqli_connect($config['resources']['db']['params']['host'], $config['resources']['db']['params']['username'], $config['resources']['db']['params']['password'], $config['resources']['db']['params']['dbname']);
        mysqli_set_charset($con, "utf8");

        $result = mysqli_query($con, $data);

        $i = 2;

        $shapes = array(
            1 => 'goodset',
            2 => 'broken-seal',
            3 => 'box-damage',
            4 => 'unit-damage',
        );

        $statuses = array(
            1 => 'OK',
            2 => 'processing',
            3 => 'lost',
            4 => 'on-changing',
        );

        while ($item = mysqli_fetch_assoc($result)) {
            $sheet->stdOutSheetRowStart($i);

            $j = 1;

            $sheet->stdOutSheetColumn('String', $i, $j++, $item['imei_sn']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['po_sn']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['return_sn']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $goods[$item['good_id']]);
            $sheet->stdOutSheetColumn('String', $i, $j++, $goodColors[$item['good_color']]);
            $sheet->stdOutSheetColumn('String', $i, $j++, $shapes[$item['shape']]);
            $sheet->stdOutSheetColumn('String', $i, $j++, $statuses[$item['status']]);
            $sheet->stdOutSheetColumn('String', $i, $j++, $warehouses[$item['warehouse_id']]);

            $sheet->stdOutSheetRowEnd();

            $i++;
        }

        $sheet->stdOutSheetEnd();

        $xml->stdOutEnd();

        exit;

    }

    private function _exportImeiOutXML($data)
    {
        require_once 'ExcelWriterXML.php';
        set_time_limit(0);
        ini_set('memory_limit', -1);
        error_reporting(0);
        ini_set('display_error', 0);

        $filename = 'Export_product_out_' . date('YmdHis') . '.xml';
        $xml = new ExcelWriterXML($filename);
        $xml->docAuthor('OPPO Vietnam');

        $xml->sendHeaders();

        $xml->stdOutStart();

        $sheet = $xml->addSheet('Product OUT');


        $heads = array(
            'SALE ORDER NUMBER',
            'PRODUCT CATEGORY',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'IMEI',
            'ORDER TYPE',
            'WAREHOUSE',
            'RETAILER',
            'RETAILER ID',
            'AREA',
            'PROVINCE',
            'DISTRICT',
            
            'NOTE',
        );

        $sheet->stdOutSheetStart();


        $sheet->stdOutSheetRowStart(1);
        foreach ($heads as $k => $item) {
            $sheet->stdOutSheetColumn('String', 1, $k + 1, $item);
        }
        $sheet->stdOutSheetRowEnd();

        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QDistributor = new Application_Model_Distributor();
        $QGoodCategory = new Application_Model_GoodCategory();
        $QWarehouse = new Application_Model_Warehouse();

        $goods = $QGood->get_cache();
        $goodColors = $QGoodColor->get_cache();
        $distributors = $QDistributor->get_cache();
        $distributors2_cached = $QDistributor->get_cache2();
        $good_categories = $QGoodCategory->get_cache();
        $warehouses = $QWarehouse->get_cache();

        //load config
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        $config = $config->toArray();
        $con = mysqli_connect($config['resources']['db']['params']['host'], $config['resources']['db']['params']['username'], $config['resources']['db']['params']['password'], $config['resources']['db']['params']['dbname']);
        mysqli_set_charset($con, "utf8");

        $result = mysqli_query($con, $data);

        $i = 2;

        while ($item = mysqli_fetch_assoc($result)) {
            $sheet->stdOutSheetRowStart($i);

            $j = 1;

            if (isset($distributors) && isset($distributors[$item['d_id']]))
                $distributor = $distributors[$item['d_id']];
            else
                $distributor = '';

            $type = '';
            switch ($item['type']) {
                case '1':
                    $type = 'Retailer';
                    break;
                case '2':
                    $type = 'Demo';
                    break;
                case '3':
                    $type = 'Staff';
                    break;
                default:
                    # code...
                    break;
            }

            $sheet->stdOutSheetColumn('String', $i, $j++, $item['sn']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $good_categories[$item['cat_id']]);
            $sheet->stdOutSheetColumn('String', $i, $j++, $goods[$item['good_id']]);
            $sheet->stdOutSheetColumn('String', $i, $j++, $goodColors[$item['good_color']]);
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['imei_sn']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $type);
            $sheet->stdOutSheetColumn('String', $i, $j++, (isset($warehouses[$item['warehouse_id']]) ? $warehouses[$item['warehouse_id']] : ''));
            $sheet->stdOutSheetColumn('String', $i, $j++, $distributor);
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['d_id']);


            $region_name = $area_name = '';
            $distributor = isset($distributors2_cached[$item['d_id']]) ? $distributors2_cached[$item['d_id']] : null;

            if ($distributor) {
                $region = isset($regions2_cached[$distributor['region']]) ? $regions2_cached[$distributor['region']] : null;

                if ($region) {
                    $region_name = $region['name'];
                    $area_name = isset($areas_cached[$region['area_id']]) ? $areas_cached[$region['area_id']] : null;
                }
            }

            $sheet->stdOutSheetColumn('String', $i, $j++, (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::Area) : ''));
            $sheet->stdOutSheetColumn('String', $i, $j++, (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::Province) : ''));
            $sheet->stdOutSheetColumn('String', $i, $j++, (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::District) : ''));
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['outmysql_time']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['text']);


            $sheet->stdOutSheetRowEnd();

            $i++;
        }

        $sheet->stdOutSheetEnd();

        $xml->stdOutEnd();

        exit;

    }

    public function _exportOutWarehouse($markets, $mk_goods)
    {
        set_time_limit(0);
        error_reporting(0);
        ini_set('memory_limit', -1);
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'SALES ORDER NUMBER',
            'SALES MAN',
            'CATEGORY',
            'PRODUCT',
            'COLOR',
            'QUANTITY',
            'PRICE',
            'RETAILER',
            'PROVINCE',
            'WAREHOUSE',
            'ORDER TYPE',
            'ORDER TIME',
        );

        $PHPExcel->setActiveSheetIndex(0);
        $sheet = $PHPExcel->getActiveSheet();

        $alpha = 'A';
        $index = 1;
        foreach ($heads as $key) {
            $sheet->setCellValue($alpha . $index, $key);
            $alpha++;
        }
        $index = 2;

        $QDistributor = new Application_Model_Distributor();
        $distributors = $QDistributor->get_cache2();

        $QStaff = new Application_Model_Staff();
        $staffs = $QStaff->get_cache();

        $QGood = new Application_Model_Good();
        $goods = $QGood->get_cache();

        $QGoodColor = new Application_Model_GoodColor();
        $goodColors = $QGoodColor->get_cache();

        $QGoodCategory = new Application_Model_GoodCategory();
        $goodCategories = $QGoodCategory->get_cache();

        $QWarehouse = new Application_Model_Warehouse();
        $warehouses = $QWarehouse->get_cache();

        foreach ($markets as $mk) {
            foreach ($mk_goods[$mk['sn']] as $key => $value) {
                $alpha = 'A';
                $sheet->getCell($alpha++ . $index)->setValueExplicit($mk['sn'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue($alpha++ . $index, isset($staffs[$mk['user_id']]) ? $staffs[$mk['user_id']] : '');
                $sheet->setCellValue($alpha++ . $index, isset($goodCategories[$value['cat_id']]) && $goodCategories[$value['cat_id']] ? $goodCategories[$value['cat_id']] : '');
                $sheet->setCellValue($alpha++ . $index, isset($goods[$value['good_id']]) && $goods[$value['good_id']] ? $goods[$value['good_id']] : '');
                $sheet->setCellValue($alpha++ . $index, isset($goodColors[$value['good_color']]) && $goodColors[$value['good_color']] ? $goodColors[$value['good_color']] : '');
                $sheet->setCellValue($alpha++ . $index, $value['total_qty']);
                $sheet->getCell($alpha++ . $index)->setValueExplicit($value['price'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue($alpha++ . $index, isset($distributors[$mk['d_id']]) ? $distributors[$mk['d_id']]['title'] : '');
                $sheet->setCellValue($alpha++ . $index, isset($distributors[$mk['d_id']]['region']) ? My_Region::getValue($distributors[$mk['d_id']]['region'], My_Region::PROVINCE) : '');
                $sheet->setCellValue($alpha++ . $index, isset($warehouses[$mk['warehouse_id']]) ? str_replace('OPPOWarehouse_', '', $warehouses[$mk['warehouse_id']]) : '');
                $sheet->setCellValue($alpha++ . $index, $mk['type'] == 1 ? 'Retailer' : ($mk['type'] == 2 ? 'Demo' : ($mk['type'] == 3 ? 'Staff' : 'Lending')));
                $sheet->setCellValue($alpha++ . $index, $mk['add_time']);
                $index++;
            }
        }

        $filename = 'OUT WH - ' . date('d-m-Y H-i-s');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');

        exit;
    }

    public function changeSalesScanOutAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'change-sales-scan-out.php';
    }

    public function changeSalesConfirmAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'change-sales-confirm.php';
    }

    public function digitalSignatureAction()
    {
        $market_ids = $this->getRequest()->getParam('market_id');

        if ($this->getRequest()->getMethod() == 'POST') {
            $invoiceData_Info = '<inv:sellerAppRecordId>0101012</inv:sellerAppRecordId>
                                 <inv:invoiceAppRecordId>123321</inv:invoiceAppRecordId>
                                 <inv:invoiceType>01GTKT</inv:invoiceType>
                                 <inv:templateCode>01GTKT0/111</inv:templateCode>
                                 <inv:invoiceSeries>TH/15E</inv:invoiceSeries>
                                 <inv:invoiceNumber>0000001</inv:invoiceNumber>
                                 <inv:invoiceName>Hóa đơn giá trị gia tăng</inv:invoiceName>
                                 <inv:invoiceIssuedDate>2015-10-15T08:58:00</inv:invoiceIssuedDate>
                                 <inv:signedDate>2015-10-15T08:58:00</inv:signedDate>
                                 <inv:submittedDate>2015-10-15T08:58:00</inv:submittedDate>
                                 <inv:currencyCode>VND</inv:currencyCode>
                                 <inv:adjustmentType>1</inv:adjustmentType>
                                 <inv:payments>
                                    <inv:payment>
                                        <inv:paymentMethodName>Tiền mặt</inv:paymentMethodName>
                                    </inv:payment>
                                 </inv:payments>
                                 <inv:delivery>
                                    <inv:deliveryOrderDate>2015-10-15</inv:deliveryOrderDate>
                                 </inv:delivery>';

            $invoiceData_Seller = '<inv:sellerLegalName>CÔNG TY TNHH DỊCH VỤ TIN HỌC FPT (Demo)</inv:sellerLegalName>
                                    <inv:sellerTaxCode>0313290610</inv:sellerTaxCode>
                                    <inv:sellerAddressLine>Tầng 6 Tòa nhà Thành Công, Dịch Vọng Hậu, Cầu Giấy, Hà Nội</inv:sellerAddressLine>
                                    <inv:sellerPhoneNumber>0812345678</inv:sellerPhoneNumber>
                                    <inv:sellerFaxNumber>0812345678</inv:sellerFaxNumber>
                                    <inv:sellerEmail>yyy@fpt.com.vn</inv:sellerEmail>
                                    <inv:sellerContactPersonName>Đỗ C</inv:sellerContactPersonName>
                                    <inv:sellerSignedPersonName>Phạm A</inv:sellerSignedPersonName>
                                    <inv:sellerSubmittedPersonName>Nguyễn B</inv:sellerSubmittedPersonName>';

            $invoiceData_Buyer = '<inv:buyerDisplayName>Nguyễn Tiến X</inv:buyerDisplayName>
                                    <inv:buyerLegalName>Công ty Thử Nghiệm</inv:buyerLegalName>
                                    <inv:buyerTaxCode>0313290610</inv:buyerTaxCode>
                                    <inv:buyerAddressLine>15 Nguyễn Du - Hai Bà Trưng - Hà Nội</inv:buyerAddressLine>
                                    <inv:buyerPhoneNumber>0812345678</inv:buyerPhoneNumber>
                                    <inv:buyerFaxNumber>0812345678</inv:buyerFaxNumber>
                                    <inv:buyerEmail>xxx@fpt.com.vn</inv:buyerEmail>';

            $invoiceData_item = '<inv:items>
                                    <inv:item>
                                        <inv:lineNumber>1</inv:lineNumber>
                                        <inv:itemCode>00001112</inv:itemCode>
                                        <inv:itemName>Nexus One</inv:itemName>
                                        <inv:unitCode>C</inv:unitCode>
                                        <inv:unitName>Cái</inv:unitName>
                                        <inv:quantity>4</inv:quantity>
                                        <inv:itemTotalAmountWithoutVat>42000000</inv:itemTotalAmountWithoutVat>
                                        <inv:vatPercentage>5</inv:vatPercentage>
                                        <inv:unitPrice>10500000</inv:unitPrice>
                                    </inv:item>
                                    <inv:item>
                                        <inv:lineNumber>2</inv:lineNumber>
                                        <inv:itemCode>00001113</inv:itemCode>
                                        <inv:itemName>iPhone 6</inv:itemName>
                                        <inv:unitCode>C</inv:unitCode>
                                        <inv:unitName>Cái</inv:unitName>
                                        <inv:quantity>3</inv:quantity>
                                        <inv:itemTotalAmountWithoutVat>48000000</inv:itemTotalAmountWithoutVat>
                                        <inv:vatPercentage>10</inv:vatPercentage>
                                        <inv:unitPrice>16000000</inv:unitPrice>
                                    </inv:item>
                                </inv:items>';

            $invoiceData_Footer = '<inv:invoiceTaxBreakdowns>
                                    <inv:invoiceTaxBreakdown>
                                        <inv:vatPercentage>5</inv:vatPercentage>
                                        <inv:vatTaxableAmount>42000000</inv:vatTaxableAmount>
                                        <inv:vatTaxAmount>2100000</inv:vatTaxAmount>
                                    </inv:invoiceTaxBreakdown>
                                    <inv:invoiceTaxBreakdown>
                                        <inv:vatPercentage>10</inv:vatPercentage>
                                        <inv:vatTaxableAmount>48000000</inv:vatTaxableAmount>
                                        <inv:vatTaxAmount>4800000</inv:vatTaxAmount>
                                    </inv:invoiceTaxBreakdown>
                                   </inv:invoiceTaxBreakdowns>
                                   <inv:totalAmountWithoutVAT>1.1</inv:totalAmountWithoutVAT>
                                   <inv:totalVATAmount>2.2</inv:totalVATAmount>
                                   <inv:totalAmountWithVAT>3.3</inv:totalAmountWithVAT>
                                   <inv:totalAmountWithVATInWords>Chín mươi sáu triệu chín trăm nghìn đồng</inv:totalAmountWithVATInWords>
                                   <inv:isTotalAmountPos>true</inv:isTotalAmountPos>';

            $invoiceData = $invoiceData_Info . $invoiceData_Seller . $invoiceData_Buyer . $invoiceData_item . $invoiceData_Footer;

            //$invoiceData = '<inv:sellerAppRecordId>0101012</inv:sellerAppRecordId><inv:invoiceAppRecordId>123321</inv:invoiceAppRecordId><inv:invoiceType>01GTKT</inv:invoiceType><inv:templateCode>01GTKT0/111</inv:templateCode><inv:invoiceSeries>TH/15E</inv:invoiceSeries><inv:invoiceNumber>0000001</inv:invoiceNumber><inv:invoiceName>Hóa ??n giá tr? gia t?ng</inv:invoiceName><inv:invoiceIssuedDate>2015-10-15T08:58:00</inv:invoiceIssuedDate><inv:signedDate>2015-10-15T08:58:00</inv:signedDate><inv:submittedDate>2015-10-15T08:58:00</inv:submittedDate><inv:currencyCode>VND</inv:currencyCode><inv:adjustmentType>1</inv:adjustmentType><inv:payments><inv:payment><inv:paymentMethodName>Ti?n m?t</inv:paymentMethodName></inv:payment></inv:payments><inv:delivery><inv:deliveryOrderDate>2015-10-15</inv:deliveryOrderDate></inv:delivery><inv:sellerLegalName>CÔNG TY TNHH D?CH V? TIN H?C FPT (Demo)</inv:sellerLegalName><inv:sellerTaxCode>0313290610</inv:sellerTaxCode><inv:sellerAddressLine>T?ng 6 Tòa nhà Thành Công, D?ch V?ng H?u, C?u Gi?y, Hà N?i</inv:sellerAddressLine><inv:sellerPhoneNumber>0812345678</inv:sellerPhoneNumber><inv:sellerFaxNumber>0812345678</inv:sellerFaxNumber><inv:sellerEmail>yyy@fpt.com.vn</inv:sellerEmail><inv:sellerContactPersonName>?? C</inv:sellerContactPersonName><inv:sellerSignedPersonName>Ph?m A</inv:sellerSignedPersonName><inv:sellerSubmittedPersonName>Nguy?n B</inv:sellerSubmittedPersonName><inv:buyerDisplayName>Nguy?n Ti?n X</inv:buyerDisplayName><inv:buyerLegalName>Công ty Th? Nghi?m</inv:buyerLegalName><inv:buyerTaxCode>0313290610</inv:buyerTaxCode><inv:buyerAddressLine>15 Nguy?n Du - Hai Bà Tr?ng - Hà N?i</inv:buyerAddressLine><inv:buyerPhoneNumber>0812345678</inv:buyerPhoneNumber><inv:buyerFaxNumber>0812345678</inv:buyerFaxNumber><inv:buyerEmail>xxx@fpt.com.vn</inv:buyerEmail><inv:items><inv:item><inv:lineNumber>1</inv:lineNumber><inv:itemCode>00001112</inv:itemCode><inv:itemName>Nexus One</inv:itemName><inv:unitCode>C</inv:unitCode><inv:unitName>Cái</inv:unitName><inv:quantity>4</inv:quantity><inv:itemTotalAmountWithoutVat>42000000</inv:itemTotalAmountWithoutVat><inv:vatPercentage>5</inv:vatPercentage><inv:unitPrice>10500000</inv:unitPrice></inv:item><inv:item><inv:lineNumber>2</inv:lineNumber><inv:itemCode>00001113</inv:itemCode><inv:itemName>iPhone 6</inv:itemName><inv:unitCode>C</inv:unitCode><inv:unitName>Cái</inv:unitName><inv:quantity>3</inv:quantity><inv:itemTotalAmountWithoutVat>48000000</inv:itemTotalAmountWithoutVat><inv:vatPercentage>10</inv:vatPercentage><inv:unitPrice>16000000</inv:unitPrice></inv:item></inv:items><inv:invoiceTaxBreakdowns><inv:invoiceTaxBreakdown><inv:vatPercentage>5</inv:vatPercentage><inv:vatTaxableAmount>42000000</inv:vatTaxableAmount><inv:vatTaxAmount>2100000</inv:vatTaxAmount></inv:invoiceTaxBreakdown><inv:invoiceTaxBreakdown><inv:vatPercentage>10</inv:vatPercentage><inv:vatTaxableAmount>48000000</inv:vatTaxableAmount><inv:vatTaxAmount>4800000</inv:vatTaxAmount></inv:invoiceTaxBreakdown></inv:invoiceTaxBreakdowns><inv:totalAmountWithoutVAT>1.1</inv:totalAmountWithoutVAT><inv:totalVATAmount>2.2</inv:totalVATAmount><inv:totalAmountWithVAT>3.3</inv:totalAmountWithVAT><inv:totalAmountWithVATInWords>Chín m??i sáu tri?u chín tr?m nghìn ??ng</inv:totalAmountWithVATInWords><inv:isTotalAmountPos>true</inv:isTotalAmountPos>';

            $signatureValue = 'au5Xgrlrzxd/PYhhwunx3t7lrxYCpjqU/Rf4faKPp5aTOqk02+G6Gn9Lx667h12kixqbqvyun2P0oUJKopYOfUFJEoimqwfolQEDDk+1qPNIPqHut500OAzbWXfVgUXF3xW47CMY9/nbRByq6AVqKcOORN5dtlo/aMNIB/Xbam4ICD7yxpzy2e4rqPOywM1gXvImzi8Vh/bp5fSNuFt/Y2NTY5VC4K8oQ8xgU5xwhy8LojAt+vB57xMcCtqoYLBRg7LV1/MsYYcJB93zUvKwfLmT4cykwfWlFp0RVC5kLE+GvDz6NIQ/fivduo1DoYBOLEJWGpDEEZrQRRJfaJn95w==';
            //$signatureValue = 'Wk270A04cq9X/B3fWY5Ll78a5kLvQBZANEhDCdkRmfuMQMRnAixAHIeTS7XiT5gP3yqgc4qEbWZhx/SX0S55qvqCHddB+CfQtd3taO/kdWqVeyax8hIyhf7R2R3SU3xaTZcUFQ2VRiYW92CO43u7MK2hSFlQQnlhyjdwdLNm6Dc=';

            $X509Certificate = 'MIIEQDCCAyigAwIBAgIKaFRU1wABAAAC7jANBgkqhkiG9w0BAQUFADAUMRIwEAYDVQQDDAlGVFMtU3ViQ0EwHhcNMTUwNjE3MDg1MTEzWhcNMTYwNjE3MDkwMTEzWjCBjDELMAkGA1UEBhMCVk4xGTAXBgNVBAcMEFRow6BuaCBwaOG7kSBIQ00xFzAVBgNVBAoTDk1TVDowMzEzMjkwNjEwMSgwJgYDVQQDDB9Dw7RuZyBUeSBUTkhIIFhpYW9taSBWaeG7h3QgTmFtMR8wHQYJKoZIhvcNAQkBFhBYaWFvbWlAZ21haWwuY29tMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyN7AqjfmeqxuRvyAl4CYDKlOV7qmjrUn7JU9k4uUP66CNhoIwsonVu4wvOXdkNjWLcpta8g64rjh1FAPTqoHF9WBEs7ezMEHX3RJX6O4GzXjh38EiYhjQi1gUj8S/y3J12IeKrOly00NC3BP0UgdtX5Fy64pGgPwTF0Hs/dZXH3g4Yh3D7xJq1Zo4mt5Phtr4fxgFZU7diYhaPT+wpPLufsRNlwWyyOLwVQS0+dhVtTqYKfuO/VpqF+hRGY4Qq+/rxpYwZWJT9pM1F56BsjfCkfIK9ZDxP5fLhU8m7/7kqkE+qS3Uq8HasqOtYPwNUk4w0Lp/4ACUqhLnaadyssavwIDAQABo4IBGTCCARUwDgYDVR0PAQH/BAQDAgTwMBMGA1UdJQQMMAoGCCsGAQUFBwMCMB0GA1UdDgQWBBQhnetNdn5TElgx/aMEcHdlzvM+aDAfBgNVHSMEGDAWgBTXTEEJKhQDjYUrripVK0TOXTJC8DBABgNVHR8EOTA3MDWgM6Axhi9odHRwOi8vMTcyLjI3LjY4Ljg0L0NlcnRFbnJvbGwvRlRTLVN1YkNBKDEpLmNybDBeBggrBgEFBQcBAQRSMFAwTgYIKwYBBQUHMAKGQmZpbGU6Ly9XSU4tMDJPRk9MMVVKRU0vQ2VydEVucm9sbC9XSU4tMDJPRk9MMVVKRU1fRlRTLVN1YkNBKDEpLmNydDAMBgNVHRMBAf8EAjAAMA0GCSqGSIb3DQEBBQUAA4IBAQC0TFDWGOlUwr+O5iHC13+raeom793CweO+Fn1BnO1+qiqdkGr+2fAeqN+HrS+th5SSUp1lXK3Gsxzya/0K8UcRR5GYUB6N8g0i0BOAM4hrDvTqO5OrZke0YotY1BSaNIz8d0xJNo5uI4GQyzCBKDzE0UcxpDJWoTEvUjFhtp7GJKzqUYAOsdVnaLcWevEYNHb3jM+C0aJyhgb4r0GkJZLCUz4lE9BRw45utNZvPuFqS/NkpCbecm2o1+yCr6M5TLNPfPak5IxJ6+q0NosG/Tban+7yKmWpavTnaim+7xbZrjPriwSb5ZMnXxi9bzOPGVns/RzQb+LNKiaVmhyWB7uU';
            //$X509Certificate = 'MIIEQDCCAyigAwIBAgIKaFRU1wABAAAC7jANBgkqhkiG9w0BAQUFADAUMRIwEAYDVQQDDAlGVFMtU3ViQ0EwHhcNMTUwNjE3MDg1MTEzWhcNMTYwNjE3MDkwMTEzWjCBjDELMAkGA1UEBhMCVk4xGTAXBgNVBAcMEFRow6BuaCBwaOG7kSBIQ00xFzAVBgNVBAoTDk1TVDowMzEzMjkwNjEwMSgwJgYDVQQDDB9Dw7RuZyBUeSBUTkhIIFhpYW9taSBWaeG7h3QgTmFtMR8wHQYJKoZIhvcNAQkBFhBYaWFvbWlAZ21haWwuY29tMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyN7AqjfmeqxuRvyAl4CYDKlOV7qmjrUn7JU9k4uUP66CNhoIwsonVu4wvOXdkNjWLcpta8g64rjh1FAPTqoHF9WBEs7ezMEHX3RJX6O4GzXjh38EiYhjQi1gUj8S/y3J12IeKrOly00NC3BP0UgdtX5Fy64pGgPwTF0Hs/dZXH3g4Yh3D7xJq1Zo4mt5Phtr4fxgFZU7diYhaPT+wpPLufsRNlwWyyOLwVQS0+dhVtTqYKfuO/VpqF+hRGY4Qq+/rxpYwZWJT9pM1F56BsjfCkfIK9ZDxP5fLhU8m7/7kqkE+qS3Uq8HasqOtYPwNUk4w0Lp/4ACUqhLnaadyssavwIDAQABo4IBGTCCARUwDgYDVR0PAQH/BAQDAgTwMBMGA1UdJQQMMAoGCCsGAQUFBwMCMB0GA1UdDgQWBBQhnetNdn5TElgx/aMEcHdlzvM+aDAfBgNVHSMEGDAWgBTXTEEJKhQDjYUrripVK0TOXTJC8DBABgNVHR8EOTA3MDWgM6Axhi9odHRwOi8vMTcyLjI3LjY4Ljg0L0NlcnRFbnJvbGwvRlRTLVN1YkNBKDEpLmNybDBeBggrBgEFBQcBAQRSMFAwTgYIKwYBBQUHMAKGQmZpbGU6Ly9XSU4tMDJPRk9MMVVKRU0vQ2VydEVucm9sbC9XSU4tMDJPRk9MMVVKRU1fRlRTLVN1YkNBKDEpLmNydDAMBgNVHRMBAf8EAjAAMA0GCSqGSIb3DQEBBQUAA4IBAQC0TFDWGOlUwr+O5iHC13+raeom793CweO+Fn1BnO1+qiqdkGr+2fAeqN+HrS+th5SSUp1lXK3Gsxzya/0K8UcRR5GYUB6N8g0i0BOAM4hrDvTqO5OrZke0YotY1BSaNIz8d0xJNo5uI4GQyzCBKDzE0UcxpDJWoTEvUjFhtp7GJKzqUYAOsdVnaLcWevEYNHb3jM+C0aJyhgb4r0GkJZLCUz4lE9BRw45utNZvPuFqS/NkpCbecm2o1+yCr6M5TLNPfPak5IxJ6+q0NosG/Tban+7yKmWpavTnaim+7xbZrjPriwSb5ZMnXxi9bzOPGVns/RzQb+LNKiaVmhyWB7uU';

            $digestValue = 'uYKAQEFk+cmZ2ixFWC2zrfe55Cs=';
            //$digestValue = 'qccsn6HttcrX9bCNKq4YFokvY5M=';

            $xml = '<?xml version="1.0" encoding="utf-8"?><inv:transaction xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:inv="http://laphoadon.gdt.gov.vn/2014/09/invoicexml/v1"><inv:resend>0</inv:resend><inv:invoices><inv:invoice xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:inv="http://laphoadon.gdt.gov.vn/2014/09/invoicexml/v1"><inv:invoiceData id="data">' . $invoiceData . '</inv:invoiceData><inv:controlData><inv:systemCode>LHD_TEST</inv:systemCode></inv:controlData><Signature Id="seller" xmlns="http://www.w3.org/2000/09/xmldsig#"><SignedInfo><CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /><SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1" /><Reference URI="#data"><Transforms><Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" /><Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /></Transforms><DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1" /><DigestValue>' . $digestValue . '</DigestValue></Reference></SignedInfo><SignatureValue>' . $signatureValue . '</SignatureValue><KeyInfo><X509Data><X509Certificate>' . $X509Certificate . '</X509Certificate></X509Data></KeyInfo></Signature></inv:invoice></inv:invoices></inv:transaction>';

            $url = 'https://testdichvuhoadon.gdt.gov.vn:444/van-service/services/invoice/certify/v1';

            $header = array(
                'Accept' => 'text/html;charset=UTF-8',
                'Content-Type' => 'text/html;charset=UTF-8',
                'Authorization' => 'Basic TEhEMDMxMjc5ODAxNzp4eCMjQExIRDAzMTI3OTgwMTdAMjAxNTEyMTUxMTI1MzM='
            );

            $config = array(
                'adapter' => 'Zend_Http_Client_Adapter_Socket',
                'ssltransport' => 'tls',
            );

            $client = new Zend_Http_Client();
            $client->setUri($url);
            $client->setConfig($config);
            $client->setMethod(Zend_Http_Client::POST);
            $client->setHeaders($header);
            $client->setRawData($xml, 'text/html');

            $request_time = date('Y-m-d H:i:s');
            $response = $client->request();
            $response_time = date('Y-m-d H:i:s');

            echo '<pre>';
            echo print_r($response);
            echo '</pre>';

            if ($response->isSuccessful()) {
                $data = array(
                    'created_at' => date('Y-m-d H:i:s'),
                    'type' => 'default',
                    'market_id' => 'market_id',
                    'http_code' => $response->getStatus(),
                    'status_code' => $response->getHeader('X-return-status'),
                    'status_msg' => $response->getHeader('X-return-status-msg'),
                    'response_body' => str_replace(' ', '', $response->getBody()),
                    'digital_sn' => 'digital_sn',
                    'request_time' => $request_time,
                    'response_time' => $response_time,
                );

                $QMarketDSLog = new Application_Model_MarketDigitalSignatureLog();
                $QMarketDSLog->insert($data);

                $flashMessenger = $this->_helper->flashMessenger;
                $messages = $flashMessenger->setNamespace('success')->addMessage('Thành công!');
            }

            //$this->_redirect('warehouse/digital-signature-result');
        } else {
            $QDistributor = new Application_Model_Distributor();
            $this->view->distributorsList = $QDistributor->get_cache();

            $QDistributor = new Application_Model_Distributor();
            $tmp = $QDistributor->fetchAll();

            $distributors = array();
            foreach ($tmp as $k => $v) {
                $distributors[$v['id']] = $v;
            }

            $QMarket = new Application_Model_Market();
            $Markets = $QMarket->get_market_by_sn($market_ids);

            $this->view->distributors = $distributors;
            $this->view->markets = $Markets;
        }
    }

    public function transferImeiAction()
    {
        if ($this->getRequest()->getMethod() == 'POST') {

            $type       = trim($this->getRequest()->getParam('type', ''));
            $imei_str   = trim($this->getRequest()->getParam('imei', ''));

            $imei       = explode("\n", $imei_str);
            $total_imei = count($imei);
            $imei_status_transfer = array();

            $params = array(
                'type'          => $type,
                'imei'          => $imei_str,
                'total_imei'    => $total_imei,
            );

            if ($total_imei == 1 && $imei[0] == '') {
                $flashMessenger = $this->_helper->flashMessenger;
                $flashMessenger->setNamespace('messages')->addMessage('No Input Data.');

                $this->_redirect(HOST . "warehouse/transfer-imei");
            }

            if (is_array($imei)) {
                $QImei = new Application_Model_Imei();

                $where  = $QImei->getAdapter()->quoteInto('imei_sn IN (?)', $imei);
                $result = $QImei->fetchAll($where);

                $data = array();
                foreach ($result->toArray() as $k => $v)
                    $data[$v['imei_sn']] = $v;


                foreach ($imei as $v) {
                    $v_imei = trim($v);
                    if (is_array($data[$v_imei])) {
                        $where_update       = $QImei->getAdapter()->quoteInto('imei_sn = ?', $v_imei);
                        $status_transfer    = $QImei->update(array('type' => $type), $where_update);

                        $imei_status_transfer[$v_imei] = array('message' => 'Thành công', 'status' => 1);
                    } else {
                        $imei_status_transfer[$v_imei] = array('message' => 'Không tìm thấy', 'status' => 0);
                    }
                }
            }

            $this->view->imei_status_transfer   = $imei_status_transfer;
            $this->view->params                 = $params;
        }
    }

    public function digitalSignatureResultAction()
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;
    }

    
}