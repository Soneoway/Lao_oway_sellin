<?php

class WarehouseController extends My_Controller_Action
{
    public function inAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'in.php';
    }

    public function stockCardByStoreAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'stock-card-by-store.php';
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

    // Add by khuan //

    public function warehouseDetailAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'warehouse-detail.php';
    }

    public function warehouseEventAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'warehouse-event.php';
    }

    // end //

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

    public function rollback2Action()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'rollback2.php';
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

    //close warehosue
    // public function warehouseCloseAction()
    // {
    //     $flashMessenger   = $this->_helper->flashMessenger;
    //     $id = $this->getRequest()->getParam('id');

    // if ($id) {
    //     $QWarehouse = new Application_Model_Warehouse();
    //     $warehouseRowSet = $QWarehouse->find($id);
    //     $warehouse = $warehouseRowSet->current();

    // if ($warehouse) {
    //     $data = array('status' => 1);
    //     $where = $QWarehouse->getAdapter()->quoteInto('id = ?', $id);
    //     $QWarehouse->update($data,$where);
    //     $messages_success = $flashMessenger->setNamespace('success')->addMessage('Success');
    //     }
    // }
    //     $this->_redirect(HOST.'warehouse/list');
    // }

    //open
    // public function warehouseOpenAction()
    // {
    //     $flashMessenger   = $this->_helper->flashMessenger;
    //     $id = $this->getRequest()->getParam('id');

    // if ($id) {
    //     $QWarehouse = new Application_Model_Warehouse();
    //     $warehouseRowSet = $QWarehouse->find($id);
    //     $warehouse = $warehouseRowSet->current();

    // if ($warehouse) {
    //     $data = array('status' => null);
    //     $where = $QWarehouse->getAdapter()->quoteInto('id = ?', $id);
    //     $QWarehouse->update($data,$where);
    //     $messages_success = $flashMessenger->setNamespace('success')->addMessage('Success');
    //     }
    // }
    //     $this->_redirect(HOST.'warehouse/list');
    // }

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

    public function printInvoiceNewAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'print-invoice-new.php';
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

    public function transactionStockAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'transaction-stock.php';
    }

    public function printProductListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'print-product-list.php';
    }

    public function printChangeSalesAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'print-change-sales.php';
    }

    public function printChangeSalesAddressBorrowingAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'print-change-sales-address-borrowing.php';
    }

    public function btnCheckImeiAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'btn-check-imei.php';
    }

    public function btnCheckImeiListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'btn-check-imei-list.php';
    }

    public function btnCheckImeiQrAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'btn-check-imei-qr.php';
    }

    public function btnCheckImeiWarehouseAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'btn-check-imei-warehouse.php';
    }

    public function btnSavelogCheckImeiWarehouseAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'btn-savelog-check-imei-warehouse.php';
    }

    public function productWarrantyAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'product-warranty.php';
    }

    public function productWarrantyShowImeiAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'product-warranty-show-imei.php';
    }

    public function checkWarehouseCreateLineAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'check-warehouse-create-line.php';
    }

    public function checkWarehouseLineAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'check-warehouse-line.php';
    }

    public function checkWarehouseLineMonitoringAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'check-warehouse-line-monitoring.php';
    }

    public function requestListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'request-list.php';
    }

    public function requestProcessAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'request-process.php';
    }

    public function borrowingReturnListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'borrowing-return-list.php';
    }

    public function borrowingReturnProcessAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'borrowing-return-process.php';
    }

    public function borrowingReturnImeiListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'borrowing-return-imei-list.php';
    }

    public function borrowingReturnImeiAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'borrowing-return-imei.php';
    }

    public function borrowingReportAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'borrowing-report.php';
    }

    public function borrowingReportDetailsAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'borrowing-report-details.php';
    }

    public function factoryClaimRequestAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'factory-claim-request.php';
    }

    public function factoryClaimListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'factory-claim-list.php';
    }

    public function factoryClaimApproveAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'factory-claim-approve.php';
    }

    public function factoryClaimApproveListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'factory-claim-approve-list.php';
    }

    public function factoryClaimInputMoneyListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'factory-claim-input-money-list.php';
    }

    public function factoryClaimInputMoneyAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'factory-claim-input-money.php';
    }

    public function factoryClaimGetMoneyListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'factory-claim-get-money-list.php';
    }

    public function factoryClaimGetMoneyAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'factory-claim-get-money.php';
    }

    public function manageLotusListAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'manage-lotus-list.php';
    }

    public function manageLotusAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'manage-lotus.php';
    }

    public function imeiReworkAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'imei-rework.php';
    }

    public function reportImeiReworkAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'report-imei-rework.php';
    }

    public function imeiFactoryReworkAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'imei-factory-rework.php';
    }

    public function reportImeiFactoryReworkAction()
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'report-imei-factory-rework.php';
    }

    public function changeSalesDeleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'change-sales-delete.php';
    }

    public function deleteco2Action()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'deleteco2.php';
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

    private function _exportStorage($data, $warehouse_id = null) ##################
    {
        // print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '400M');
        $filename = 'Export_Storage_' . date('YmdHis') .'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/warehouses/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        

        $heads = array(
            'Name',
            'Color',
            'Category',
            'Product Details',

            'SO Reserve Normal',
            'CO Current Change Order Normal',
            
            'Available Normal',
            'Total Normal',

            'SO Reserve Demo',
            'CO Current Change Order Demo',
            
            'Available Demo',
            'Total Demo',

            'SO Reserve APK',
            'CO Current Change Order APK',
            
            'Available APK',
            'Total APK',

            'Total Available',
            'Total Storage',
            'CO ON Changing',
            
        );

        fputcsv($output, $heads);

        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QGoodCategory = new Application_Model_GoodCategory();
        $QBrand = new Application_Model_Brand();

        $goods = $QGood->get_cache();
        $goodColors = $QGoodColor->get_cache();
        $good_categories = $QGoodCategory->get_cache();
        
        $i = 2;

        foreach($data as $good) {

            $bad = $demo = $count = $available_normal = $available_demo = $available_apk = $total_normal = $total_demo = $total_apk = 0;
            if ($good['cat_id'] == PHONE_CAT_ID){
                $bad = ($good['imei_bad_count'] ? $good['imei_bad_count'] : 0);
                $bad_normal = ($good['imei_normal_bad_count'] ? $good['imei_normal_bad_count'] : 0);
                $bad_demo = ($good['imei_demo_bad_count'] ? $good['imei_demo_bad_count'] : 0);
                $bad_apk = ($good['imei_apk_bad_count'] ? $good['imei_apk_bad_count'] : 0);
                $count = ($good['imei_count'] ? $good['imei_count'] : 0);
                $demo = ($good['imei_demo_count'] ? $good['imei_demo_count'] : 0);
                $apk = ($good['imei_apk_count'] ? $good['imei_apk_count'] : 0);
            } elseif ($good['cat_id'] == ILIKE_CAT_ID){
                $bad = ($good['ilike_bad_count'] ? $good['ilike_bad_count'] : 0);
                $count = ($good['ilike_count'] ? $good['ilike_count'] : 0);
            } elseif ($good['cat_id'] == DIGITAL_CAT_ID){
                $bad = ($good['digital_bad_count'] ? $good['digital_bad_count'] : 0);
                $count = ($good['digital_count'] ? $good['digital_count'] : 0);
            } elseif ($good['cat_id'] == IOT_CAT_ID) {
                $bad = ($good['iot_imei_bad_count'] ? $good['iot_imei_bad_count'] : 0);
                $bad_normal = 0;
                $bad_demo = 0;
                $bad_apk = 0;
                $count = ($good['iot_imei_count'] ? $good['iot_imei_count'] : 0);
                $demo = 0;
                $apk = 0;
            } else {
                $bad = ($good['damage_product_count'] ? $good['damage_product_count'] : 0);
                $count = ($good['product_count'] ? $good['product_count'] : 0);
            }

            $current_order =  ($good['current_order'] ? $good['current_order'] : 0);
            $current_change_order =  ($good['current_change_order'] ? $good['current_change_order'] : 0);
            $total_normal = $count;
            $available_normal = $total_normal - $current_order - $current_change_order;

            $current_order_demo =  ($good['current_order_demo'] ? $good['current_order_demo'] : 0); 
            $current_change_order_demo =  ($good['current_change_order_demo'] ? $good['current_change_order_demo'] : 0); 
            $total_demo = $demo;
            $available_demo = $total_demo - $current_order_demo - $current_change_order_demo;
            
            $current_order_apk =  ($good['current_order_apk'] ? $good['current_order_apk'] : 0); 
            $current_change_order_apk =  ($good['current_change_order_apk'] ? $good['current_change_order_apk'] : 0); 
            $total_apk = $apk;
            $available_apk = $total_apk - $current_order_apk - $current_change_order_apk;

            $total_current_order = $current_order + $current_order_demo + $current_order_apk;
            $total_current_change_order = $current_change_order + $current_change_order_demo + $current_change_order_apk;

            $total_available = intval($available_normal) + intval($available_demo) + intval($available_apk);

            $total_storage = $total_normal + $total_demo + $total_apk;
            $total_changing = ($good['current_changing_order'] ? $good['current_changing_order'] : 0); 
            
            $row = array();

            $brand_name = $QBrand->getBrand($good['id']);

            $row[] = $brand_name[0]['brand_name'].' '.$good['name'];
            $row[] = $goodColors[$good['good_color_id']];
            $row[] = $good_categories[$good['cat_id']];
            $row[] = $good['desc'];

            $row[] = $good['current_order'];
            $row[] = $good['current_change_order'];
           // $row[] = $bad_normal;
            $row[] = $available_normal;
            $row[] = $total_normal;

            $row[] = $good['current_order_demo'];
            $row[] = $good['current_change_order_demo'];
            //$row[] = $bad_demo;
            $row[] = $available_demo;
            $row[] = $total_demo;

            $row[] = $good['current_order_apk'];
            $row[] = $good['current_change_order_apk'];
            //$row[] = $bad_apk;
            $row[] = $available_apk;
            $row[] = $total_apk;

            $row[] = $total_available;
            $row[] = $total_storage;
            $row[] = $total_changing;

            /*
            $row[] = $goodColors[$item['good_color']];
            $row[] = $good_categories[$item['cat_id']];
            
            
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
            $row[] = $imei_sn;
            $row[] = $item['activated_date'];
            $row[] = $item['text'];
            */


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

    private function _exportStorage_old($data, $warehouse_id = null)
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
            'Storage ( For APK )',
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
            $demo = $bad = $apk = $count = 0;

            if ($item['cat_id'] == PHONE_CAT_ID) {
                $bad = ($item['imei_bad_count'] ? $item['imei_bad_count'] : 0);
                $demo = ($item['imei_demo_count'] ? $item['imei_demo_count'] : 0);
                $apk = ($item['imei_apk_count'] ? $item['imei_apk_count'] : 0);
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
            $sheet->setCellValue($alpha++ . $index, $apk);
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
        //ini_set('memory_limit', '200M');
        $filename = 'IMEI Stock '.date('d-m-Y H-i-s').'.csv';
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

        // $path = $file_path.'/'.$filename;
        // $output = fopen($path, 'w+');
        //echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'IMEI',
            'MODEL',
            'COLOR',
            'TYPE',
            'WAREHOUSE',
            'AREA',
            'PROVIENCE',
            'CAGE',
            'Activated Date',
            'lock Imei',
            'Timing Status'
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
            
            if (isset($item['type']) and $item['type'] == 1) { $type = 'Normal'; } 
            else if (isset($item['type']) and $item['type'] == 2) { $type = 'Demo'; } 
            else if (isset($item['type']) and $item['type'] == 5) { $type = 'APK'; } 
            else { $type = '-';}
            
            $row = array();
            $row[] = $item['imei_sn'];
                $brand_name = $QBrand->getBrand($item['good_id']);
            $row[] = $brand_name[0]['brand_name'].' '.$goodName[$item['good_id']];
            $row[] = (isset($goodColors[$item['good_color']]) ? $goodColors[$item['good_color']] : '');
            $row[] = $type;
            $row[] = (isset($warehouses[$item['warehouse_id']]) ? $warehouses[$item['warehouse_id']] : '');
            $row[] = $item['area_name'];
            $row[] = $item['provice_name'];
            $row[] = (isset($item['bucket_name']) ? $item['bucket_name'] : '');
            $row[] = $item['activated_date'];
            if($item['lock_status'] == ''){
                $row[] = "No";
            }else{
                $row[] = "Yes";
            }

            $row[] = $item['timing_status'];

            // if($$item['timing_status'] !=''){
            //     $row[] = "Yes";
            // }else{
            //     $row[] = "No";
            // }

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

    private function _exportExcel4Digital($data)
    {
       
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', '200M');
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
            'TYPE',
            'WAREHOUSE',
            'CAGE'
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
            
            if (isset($item['type']) and $item['type'] == 1) { $type = 'Normal'; } 
            else if (isset($item['type']) and $item['type'] == 2) { $type = 'Demo'; } 
            else if (isset($item['type']) and $item['type'] == 5) { $type = 'APK'; } 
            else { $type = '-';}
            
            $row = array();
            $row[] = $item['sn'];
            $row[] = $goodName[$item['good_id']];
            $row[] = (isset($goodColors[$item['good_color']]) ? $goodColors[$item['good_color']] : '');
            $row[] = $type;
            $row[] = (isset($warehouses[$item['warehouse_id']]) ? $warehouses[$item['warehouse_id']] : '');
            $row[] = (isset($item['bucket_name']) ? $item['bucket_name'] : '');

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
        ini_set('memory_limit', -1);
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
            'DISTRIBUTOR TYPE GROUP',
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

        foreach($data as $item) {

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
        
        if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
        else { $temp_sn = $item['sn_ref']; }

        $row = array();
            //$row[] = $item['sn'];
        $row[] = '="'.$temp_sn.'"';
        $row[] = $good_categories[$item['cat_id']];
        $row[] = $goods[$item['good_id']];
        $row[] = $goodColors[$item['good_color']];
        $row[] = $item['num'];
        $row[] = $item['total'];
        $row[] = $type;
        $row[] = isset($warehouses[$item['warehouse_id']]) ? $warehouses[$item['warehouse_id']] : '';
        $row[] = $distributors_list['store_code'];
        $row[] = $distributor;
        $row[] = $item['group_name'];
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

    private function _exportExcelProductOutByImei($sql)
    {
       
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'Export_product_out_by_imei_' . date('YmdHis') .'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/warehouses/export/'.$userStorage->id.'/'.uniqid();
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
            'DISTRIBUTOR TYPE GROUP',
            'INVOICE NUMBER',
            'AREA',
            'PROVINCE',
            'DISTRICT',
            'OUT TIME',
            'FINANCE CONFIRM',
            'IMEI SN',
            'ACTIVATED DATE',
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
        $data = $db->query($sql);
        
        $i = 2;

        foreach($data as $item) {

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
        
        if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
        else { $temp_sn = $item['sn_ref']; }

        $row = array();

        $imei_sn = '="'.$item['imei_sn'].'"';
        
            //$row[] = $item['sn'];
        $row[] = '="'.$temp_sn.'"';
        $row[] = $good_categories[$item['cat_id']];
        $row[] = $goods[$item['good_id']];
        $row[] = $goodColors[$item['good_color']];
        $row[] = $item['num'];
        $row[] = $item['total'];
        $row[] = $type;
        $row[] = isset($warehouses[$item['warehouse_id']]) ? $warehouses[$item['warehouse_id']] : '';
        $row[] = $distributors_list['store_code'];
        $row[] = $distributor;
        $row[] = $item['group_name'];
        $row[] = $item['invoice_number'];
        
        $row[] = (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::Area) : '');
        $row[] = (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::Province) : '');            
        $row[] = (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::District) : '');           
        $row[] = $item['outmysql_time'];
        $row[] = $item['pay_time'];
        $row[] = $imei_sn;
        $row[] = $item['activated_date'];
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
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'Warehouse_List_' . date('YmdHis') .'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/warehouses/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        $heads = array(
            'Agency',
            'Office',
            'Affiliated Distributor',
            'Warehouse name',
            'Leader',
            'Leader Tel.',
            'Warehouse Address',
            'Warehouse Type',
            'Marketable',
            'AD Warehouse',
            'Shipper Address',
            'Shipper Name',
            'Shipper Tel.',
            'Company(Ship-From)',
            'Shipment Remark',
            'Level',
            'Tax name',
            'Delegate depot name',
            'Participation in Statement Statistics',
            'Status',
            'Finance client name',
            'External Code',
            'store attribute',
            'Country',
            'Location'
        );

        fputcsv($output, $heads);

        $QWarehouseType = new Application_Model_WarehouseType();
        $QArea = new Application_Model_Area();
        $QRegionalMarket = new Application_Model_RegionalMarket();

        $warehouseType = $QWarehouseType->get_cache();
        $area = $QArea->get_cache();
        $regional = $QRegionalMarket->get_cache();

        $i = 2;

        foreach($data as $item) {

            if($item['market_table'] == 1) {
                $market_table = 'True';
            }else{
                $market_table = 'False';
            }

            if($item['level'] == 1){
                $level = 'First-tier';
            }else{
                $level = 'Second-tier';
            }

            if($item['status'] == 1) {
                $status = 'In Cooperation';
            }else if($item['status'] == 2) {
                $status = 'Suspend Cooperation';
            }else if($item['status'] == 3) {
                $status = 'Close';
            }

            if($item['affiliation'] == 1) {
                $store_attribute = 'Private Warehouse';
            }else{
                $store_attribute = 'Third-party Warehouse';
            }

            $row = array();
            $row[] = $area[$item['area_id']];
            $row[] = $regional[$item['province_id']];
            $row[] = '';
            $row[] = $item['name'];
            $row[] = $item['leader'];
            $row[] = $item['phone'];
            $row[] = $item['address'];
            $row[] = $warehouseType[$item['warehouse_type']];
            $row[] = $market_table;
            $row[] = '';
            $row[] = '';
            $row[] = '';
            $row[] = '';
            $row[] = '';
            $row[] = '';
            $row[] = $level;
            $row[] = '';
            $row[] = '';
            $row[] = '';
            $row[] = $status;
            $row[] = '';
            $row[] = $item['external_serial'];
            $row[] = $store_attribute;
            $row[] = 'LAOS';
            $row[] = $area[$item['area_id']].' / '.$regional[$item['province_id']];


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
            'AREA',
            'PROVINCE',
            'DISTRICT',
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
                // $sheet->getCell($alpha++ . $index)->setValueExplicit($mk['sn'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sn_ref = $mk['sn_ref'];
                if($sn_ref==''){
                    $sn_ref = $mk['sn'];
                }
                $sheet->getCell($alpha++ . $index)->setValueExplicit($sn_ref, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue($alpha++ . $index, isset($staffs[$mk['user_id']]) ? $staffs[$mk['user_id']] : '');
                $sheet->setCellValue($alpha++ . $index, isset($goodCategories[$value['cat_id']]) && $goodCategories[$value['cat_id']] ? $goodCategories[$value['cat_id']] : '');
                $sheet->setCellValue($alpha++ . $index, isset($goods[$value['good_id']]) && $goods[$value['good_id']] ? $goods[$value['good_id']] : '');
                $sheet->setCellValue($alpha++ . $index, isset($goodColors[$value['good_color']]) && $goodColors[$value['good_color']] ? $goodColors[$value['good_color']] : '');
                $sheet->setCellValue($alpha++ . $index, $value['total_qty']);
                $sheet->getCell($alpha++ . $index)->setValueExplicit($value['price'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue($alpha++ . $index, isset($distributors[$mk['d_id']]) ? $distributors[$mk['d_id']]['title'] : '');

                $sheet->setCellValue($alpha++ . $index, isset($distributors[$mk['d_id']]['district']) ? My_Region::getValue($distributors[$mk['d_id']]['district'], My_Region::Area) : '');
                $sheet->setCellValue($alpha++ . $index, isset($distributors[$mk['d_id']]['district']) ? My_Region::getValue($distributors[$mk['d_id']]['district'], My_Region::Province) : '');               
                $sheet->setCellValue($alpha++ . $index, isset($distributors[$mk['d_id']]['district']) ? My_Region::getValue($distributors[$mk['d_id']]['district'], My_Region::District) : '');
/*
$row[] = (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::Area) : '');
$row[] = (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::Province) : '');            
$row[] = (isset($item['district']) ? My_Region::getValue($item['district'], My_Region::District) : '');
*/


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

        $imei       = array_map('trim', explode("\r\n", $imei_str));
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

        $arr_unique = array_unique($imei);
        $arr_duplicates = array_diff_assoc($imei, $arr_unique);
        if($arr_duplicates){
            foreach ($arr_duplicates as $key) {
                $imei_status_transfer[$key] = array('message' => 'Duplicate', 'status' => 0);
            }
            $this->view->imei_status_transfer = $imei_status_transfer;
            $this->view->params = $params;
            return;
        }

        if (is_array($imei)) {
            $QImei = new Application_Model_Imei();

            $where  = $QImei->getAdapter()->quoteInto('imei_sn IN (?)', $imei);
            $result = $QImei->fetchAll($where);

            $data = array();
            $dataCheck = array();
            foreach ($result->toArray() as $k => $v){
                $data[$v['imei_sn']] = $v;
                
                if($k == 0){
                    array_push($dataCheck, ['good_id' => $v['good_id'], 'good_color' => $v['good_color'], 'type' => $v['type'], 'warehouse_id' => $v['warehouse_id'], 'counter' => 1]);
                        // print_r($dataCheck);
                        // print_r($v);die;
                }else{
                    $count = 0;
                    foreach ($dataCheck as $key => $value) {
                        if($value['good_id'] == $v['good_id'] && $value['good_color'] == $v['good_color'] && $value['type'] == $v['type'] && $value['warehouse_id'] == $v['warehouse_id']){
                            $dataCheck[$key]['counter'] = $dataCheck[$key]['counter']+1;
                            $count++;
                        }
                    }
                    if($count == 0){
                        array_push($dataCheck, ['good_id' => $v['good_id'], 'good_color' => $v['good_color'], 'type' => $v['type'], 'warehouse_id' => $v['warehouse_id'], 'counter' => 1]);
                    }
                }


            }

            $error_check = array();

            $db = Zend_Registry::get('db');

            foreach ($dataCheck as $key) {
                $stock = $db->query("CALL proc_get_storage('warehouse_id=".$key['warehouse_id']."|good_id=".$key['good_id']."|good_color_id=".$key['good_color']."|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchALL();
                $stock->closeCursor();

                if($result){

                    $stock_num = 0;
                    $num_count = 0;
                    $num_order = 0;
                    $num_change = 0;

                    switch ($key['type']) {

                        //normal
                        case '1':
                        $stock_num = $result[0]['imei_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                        $num_count = $result[0]['imei_count'];
                        $num_order = $result[0]['current_order'];
                        $num_change = $result[0]['current_change_order'];
                        break;

                        //apk
                        case '2':
                        $stock_num = $result[0]['imei_apk_count']-$result[0]['current_order_apk']-$result[0]['current_change_order_apk'];
                        $num_count = $result[0]['imei_apk_count'];
                        $num_order = $result[0]['current_order_apk'];
                        $num_change = $result[0]['current_change_order_apk'];
                        break;
                        
                        //demo
                        case '5':
                        $stock_num = $result[0]['imei_demo_count']-$result[0]['current_order_demo']-$result[0]['current_change_order_demo'];
                        $num_count = $result[0]['imei_demo_count'];
                        $num_order = $result[0]['current_order_demo'];
                        $num_change = $result[0]['current_change_order_demo'];
                        break;

                        case '3':
                        $stock_num = $result[0]['imei_staff_count']-$result[0]['current_order_staff']-$result[0]['current_change_order_staff'];
                        $num_count = $result[0]['imei_staff_count'];
                        $number_order = $result[0]['current_order_staff'];
                        $num_change = $result[0]['current_change_order_staff'];
                        break;
                    }

                    if($key['counter'] > $stock_num){
                        $key['over_stock'] = $stock_num - $key['counter'];
                        $key['num_count'] = $num_count;
                        $key['num_order'] = $num_order;
                        $key['num_change'] = $num_change;
                        array_push($error_check, $key);
                    }

                }else{
                    $imei_status_transfer['Stock Fail : please contact IT'] = array('message' => 'Error', 'status' => 0);
                    $this->view->imei_status_transfer = $imei_status_transfer;
                    $this->view->params = $params;
                    return;
                }
            }

            if($error_check){

                $QGood = new Application_Model_Good();
                $c_good = $QGood->get_cache();

                $QGoodColor = new Application_Model_GoodColor();
                $c_color = $QGoodColor->get_cache();

                $QWarehouse = new Application_Model_Warehouse();
                $c_warehouse = $QWarehouse->get_cache();

                foreach ($error_check as $key) {

                    $alert_good = '';
                    $alert_color = '';
                    $alert_warehouse = '';

                    $alert_type = '';

                    switch ($key['type']) {
                        case '1':
                        $alert_type = 'Normal';
                        break;
                        case '2':
                        $alert_type = 'Demo';
                        break;
                        case '5':
                        $alert_type = 'Apk';
                        break;
                        case '3';
                        $alert_type = 'Staff';
                        break;
                    }

                    if (isset($c_good) && isset($c_good[$key['good_id']]))
                        $alert_good = $c_good[$key['good_id']];
                    if (isset($c_color) && isset($c_color[$key['good_color']]))
                        $alert_color = $c_color[$key['good_color']];
                    if (isset($c_warehouse) && isset($c_warehouse[$key['warehouse_id']]))
                        $alert_warehouse = $c_warehouse[$key['warehouse_id']];

                    $alert = $key['good_id'] . ' : ' . $alert_good . ' | ' . $key['good_color'] . ' : ' . $alert_color . ' | ' . $key['warehouse_id'] . ' : ' . $alert_warehouse . ' | Type : ' . $alert_type . ' | Available : ' . ($key['num_count'] + $key['over_stock']) . ' | Imei Scan : ' . $key['counter'] . ' | Over Stock : ' . $key['over_stock'] . ' | On Order : ' . $key['num_order'] . ' | On Change : ' . $key['num_change'];
                    $imei_status_transfer[$alert] = array('message' => 'Over Stock', 'status' => 0);
                }
                $this->view->imei_status_transfer = $imei_status_transfer;
                $this->view->params = $params;
                return;
            }

            $error_notfind = array();

            foreach ($imei as $v) {
                $v_imei = trim($v);
                if (!is_array($data[$v_imei])) {
                    array_push($error_notfind, $v_imei);
                }
            }

            if($error_notfind){
                foreach ($error_notfind as $key) {
                    $imei_status_transfer[$key] = array('message' => 'Not Find', 'status' => 0);
                }
                $this->view->imei_status_transfer = $imei_status_transfer;
                $this->view->params = $params;
                return;
            }

            foreach ($imei as $v) {
                $v_imei = trim($v);
                if (is_array($data[$v_imei])) {
                    $where_update       = $QImei->getAdapter()->quoteInto('imei_sn = ?', $v_imei);

                    $getDetailImei      = $QImei->fetchRow($where_update);

                    $status_transfer    = $QImei->update(array('type' => $type), $where_update);

                    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                    $QLCTI = new Application_Model_LogChangeTypeImei();

                    $data_log = array(
                        'old_type'     => $getDetailImei['type'],
                        'new_type'     => $type,
                        'imei_sn'      => $v_imei,
                        'created_date' => date('Y-m-d H:i:s'),
                        'created_by'   => $userStorage->id
                    );

                    $status_log = $QLCTI->insert($data_log);

                    $imei_status_transfer[$v_imei] = array('message' => 'Done', 'status' => 1);


                } else {
                    $imei_status_transfer[$v_imei] = array('message' => 'Not Find', 'status' => 0);
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

public function getChangeOrderNo($sn)
{
    try {
        $db = Zend_Registry::get('db');
        $stmt = $db->prepare("CALL gen_running_no_ref('CO',".$sn.")");
        return $stmt->execute();
    }catch (exception $e) {
        $flashMessenger = $this->_helper->flashMessenger;
        $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
        return false;
    }
}

function cmp($a, $b){
    $ad = strtotime($a['created_date']);
    $bd = strtotime($b['created_date']);
    return ($ad-$bd);
}

    public function gettransactionStockListAction()   ################
    {
        
        $sort             = $this->getRequest()->getParam('sort', 'outmysql_time');
        $desc             = $this->getRequest()->getParam('desc', 1);
        $page             = $this->getRequest()->getParam('page', 1);

        $cat_id           = $this->getRequest()->getParam('cat_id');
        $good_id          = $this->getRequest()->getParam('good_id');
        $color_id         = $this->getRequest()->getParam('color_id');
        $warehouse_id     = $this->getRequest()->getParam('warehouse_id');

        $from             = $this->getRequest()->getParam('out_time_from', date('01/m/Y'));
        $to               = $this->getRequest()->getParam('out_time_to', date('d/m/Y'));
        $export           = $this->getRequest()->getParam('export', 0);
        $class       = $this->getRequest()->getParam('_class');

        $limit = LIMITATION;
        $total = 0;

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

       // print_r($_GET);
        $params = array(
            'cat_id'        => array_unique($cat_id),
            'good_id'       => array_unique($good_id),
            'color_id'      => array_unique($color_id),
            'warehouse_id'  => array_unique($warehouse_id),
            'warehouse_type'=> $userStorage->warehouse_type,
            'from'          => $from,
            'to'            => $to,
        );

        $db = Zend_Registry::get('db');
        $QWarehouse = new Application_Model_Warehouse();
        $result = $QWarehouse->getTransactionStockGroup($params);

       // print_r($result);
        $this->view->stocks = $result;
        $this->view->params = $params;
        $this->view->class = $class;
        $this->_helper->layout()->disableLayout(true);

    }
    
    /*-----------Check Stock Imei-------------*/
    function checkStockImeiAction()    #######
    {
        //print_r($_SESSION);
        //echo "$_SESSION>>".$_SESSION["warehouse_id"];
        $export_type            = $this->getRequest()->getParam('export');
        $warehouse_id           = $_SESSION["stock_warehouse_id"];
        $good_id                = $_SESSION["stock_good_id"];
       // $warehouse_id=36;
        
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $QWarehouse = new Application_Model_Warehouse();
        //$this->view->warehouse = $QWarehouse->fetchAll(null, 'name');

        $where_wh = array();
        //$warehouse_type = $userStorage->warehouse_type;
        //$where_wh[] = $QWarehouse->getAdapter()->quoteInto('warehouse_type IN ('.$warehouse_type.')', null);
        if (My_Staff_Group::inGroup($userStorage->group_id, array(KERRY_STAFF,KERRY_LEADER))){
            $where_wh[] = $QWarehouse->getAdapter()->quoteInto('show_kerry = ? ', 1);
        }
        $this->view->warehouse= $QWarehouse->fetchAll($where_wh, 'name');


        $cat_id=11;
        if ($cat_id) {
            $QGood = new Application_Model_Good();

            $where = $QGood->getAdapter()->quoteInto('cat_id = ?', $cat_id);

            $this->view->goods = $QGood->fetchAll($where, 'name');

        }
        //print_r($warehouse_id);
        if ( isset($export_type) && $export_type) 
        {
            if ($export_type == 1) { 
                $this->export_check_total_imei_no_exist($warehouse_id,$good_id);

            }else if ($export_type == 2) { 
                $this->export_check_imei_all_no_sales_out($warehouse_id,$good_id);
            }else if ($export_type == 3) { 
                $this->export_check_imei_no_sales_out($warehouse_id,$good_id);
            }else if ($export_type == 4) { 
                $this->export_check_imei_have_sales_out($warehouse_id,$good_id);
            }
        } 

    }

    function checkWarehouseAction()  ####
    {
        
        $QWarehouse = new Application_Model_Warehouse();
        $this->view->warehouse = $QWarehouse->fetchAll(null, 'name');

        $QGood = new Application_Model_Good();
        $where = $QGood->getAdapter()->quoteInto('cat_id = ?', 11);
        $this->view->goods = $QGood->fetchAll($where, 'name');

        $QColor = new Application_Model_GoodColor();
        $this->view->colors = $QColor->fetchAll(null, 'name');

    }

    function saveCheckStockImeiAction() #################
    {
        $this->_helper->layout->disableLayout();
        //print_r($_SESSION);
         //print_r($_POST);
        //print_r($_POST);die;
        if ($this->getRequest()->getMethod() == 'POST')
        {
            define('LIST_ROW_START', 2);
            define('COL_IMEI_SN', 0);

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

                } else {
                    try {
                        
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
                        $QFileLog = new Application_Model_FileUploadLog();

                        $data = array(
                            'staff_id' => $userStorage->id,
                            'folder' => $uniqid,
                            'filename' => $new_name,
                            'type' => 'Check Imei Stock Upload',
                            'real_file_name' => $filename . '.' . $extension,
                            'uploaded_at' => time(),
                        );

                        $log_id = $QFileLog->insert($data);

                        $number_of_order = 0;
                        $error_list = array();
                        $success_list = array();
                        $listBvgByProduct = array();

                        $QImei    = new Application_Model_Imei();
                        $QBvgImei = new Application_Model_BvgImei();
                        $QBvgProduct = new Application_Model_BvgProduct();

                        $whereBvgProduct = $QBvgProduct->getAdapter()->quoteInto('joint_id = ?', $joint_circular_id);
                        $BvgProduct = $QBvgProduct->fetchAll($whereBvgProduct);
                        if ($BvgProduct->count()) {
                            foreach ($BvgProduct as $item) {
                                $listBvgByProduct[$item['good_id']] = $item['price'];
                            }
                        }

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
                    $total_order_row = $highestRow - LIST_ROW_START + 1;

                    $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
                    $status=1; $d_id='';   
                    
                    $data_sn=null;

                    $QImeiCheckStock = new Application_Model_ImeiCheckStock();
                    $import_date = date('Y-m-d H:i:s');
                    $import_by = $userStorage->id;
                    
                    $QImeiCheckStock->delete();

                    for ($i = LIST_ROW_START; $i <= $highestRow; $i++) 
                    {

                        $imei_sn = trim($objWorksheet
                            ->getCellByColumnAndRow(COL_IMEI_SN, $i)
                            ->getValue());                      

                        if($imei_sn !=''){
                            
                            $data = array(
                                'imei_sn' => $imei_sn,
                                'import_by' => $import_by,
                                'import_date' => $import_date
                            );
                            
                            //print_r($data);
                            $QImeiCheckStock->insert($data);

                        }else{                          
                            $data_error['imei_sn'] = $imei_sn;
                            $data_error['message'] = "Imei is not existed in System";
                            $error_list[] = $data_error;
                        }

                        //print_r($result);die;
                        //$data['dealer_name'] = $dealer_name;
                        $status = $result['code'];
                        if ($result['code'] == 0) {
                            $success_list[] = $data;                           
                        } else {
                            $data['message'] = $result['message'];
                            $error_list[] = $data;
                        }

                        $number_of_order++;
                        $percent = round($number_of_order * 100 / $total_order_row, 1);
                        $progress->flush($percent);
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

            // get product list

                        $objWorksheet_out->setCellValueByColumnAndRow(COL_IMEI_SN, 1, 'IMEI_SN');
                        $objWorksheet_out->setCellValueByColumnAndRow($userStorage->id, 1, 'import_by');
                        $objWorksheet_out->setCellValueByColumnAndRow($create_date, 1, 'import_date');

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

                    
                    $warehouse_id           = $this->getRequest()->getParam('warehouse_id');
                    $good_id                = $this->getRequest()->getParam('good_id');

                    $_SESSION["stock_warehouse_id"] = $warehouse_id;
                    $_SESSION["stock_good_id"] = $good_id;

                    /*--------count imei import-------------*/    
                    $db = Zend_Registry::get('db');
                    $select = $db->select()
                    ->from(array('i'=> 'imei_check_stock'),array('COUNT( i.imei_sn)'));
                    $total_imei_import = $db->fetchOne($select);
                    
                    /*--------count imei exist compare import-------------*/
                    $select = $db->select()
                    ->from(array('imc'=> 'imei_check_stock'),array('COUNT(imc.imei_sn)'))
                    ->join(array('im' => 'imei'), 'im.`imei_sn` = imc.`imei_sn`', null);
                    $total_imei_exist = $db->fetchOne($select);

                    $this->view->total_imei_import = $total_imei_import;
                    $this->view->total_imei_exist = $total_imei_exist;
                    $this->view->total_imei_no_exist = count($this->check_total_imei_no_exist($warehouse_id,$good_id));

                    $this->view->total_imei_all_no_sales_out = count($this->check_imei_all_no_sales_out($warehouse_id,$good_id));
                    $this->view->total_imei_no_sales_out = count($this->check_imei_no_sales_out($warehouse_id,$good_id));
                    $this->view->total_imei_have_sales_out = count($this->check_imei_have_sales_out($warehouse_id,$good_id));
                    

                } // end of Try
                catch (Exception $e) {
                    $db->rollback();
                    $this->view->error = $e->getMessage();
                    $progress->flush(0);
                }
                
            }
        } 
    }

    function check_imei_stock($imei_sn=null) ##########3
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('i'=> 'imei'),array('COUNT( i.imei_sn)'))
        ->joinLeft(array('g' => 'good'), 'm.good_id=g.id', array('g.name'))
        ->joinLeft(array('gc' => 'good_color'), 'm.good_color=gc.id', array('gc.name'))
        ->where('i.imei_sn = ?', $imei_sn);
        $total = $db->fetchOne($select);
        return $total;
    }

    function check_imei_all_no_sales_out($warehouse_id,$good_id) ##########
    {
        
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('im'=> 'imei'),array('im.imei_sn','im.good_id','g.name AS good_name','im.good_color','gc.name as good_color_name','im.warehouse_id','w.name AS warehouse_name','im.distributor_id','d.store_code','d.title AS distributor_name
            ','im.out_date'))
        ->joinLeft(array('g' => 'good'), 'im.good_id=g.id', null)
        ->joinLeft(array('gc' => 'good_color'), 'im.good_color=gc.id', null)
        ->joinLeft(array('w' => 'warehouse'), 'im.warehouse_id = w.id ', null)
        ->joinLeft(array('d' => 'distributor'), 'im.distributor_id = d.id', null)
        ->where('im.distributor_id IS NULL', null);

        if (is_array($warehouse_id) && $warehouse_id)
        {
            $select->where('im.warehouse_id in(?)', $warehouse_id);
        }else{
            $select->where('im.warehouse_id =?', $warehouse_id);
        }

        if (is_array($good_id) && $good_id)
        {
            $select->where('im.good_id in(?)', $good_id);
        }else{
            $select->where('im.good_id =?', $good_id);
        }

            //echo $select;die;
        $total = $db->fetchAll($select);
        return $total;
        
    }

    function check_imei_no_sales_out($warehouse_id,$good_id) ############
    {
        
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('im'=> 'imei'),array('im.imei_sn','im.good_id','g.name AS good_name','im.good_color','gc.name as good_color_name','im.warehouse_id','w.name AS warehouse_name','im.distributor_id','d.store_code','d.title AS distributor_name
            ','im.out_date'))
        ->joinLeft(array('g' => 'good'), 'im.good_id=g.id', null)
        ->joinLeft(array('gc' => 'good_color'), 'im.good_color=gc.id', null)
        ->joinLeft(array('w' => 'warehouse'), 'im.warehouse_id = w.id ', null)
        ->joinLeft(array('d' => 'distributor'), 'im.distributor_id = d.id', null)
        ->where('im.distributor_id IS NULL', null)
        ->where('im.imei_sn not in(SELECT imei_sn FROM imei_check_stock)', null);

        if (is_array($warehouse_id) && $warehouse_id)
        {
            $select->where('im.warehouse_id in(?)', $warehouse_id);
        }else{
            $select->where('im.warehouse_id =?', $warehouse_id);
        }

        if (is_array($good_id) && $good_id)
        {
            $select->where('im.good_id in(?)', $good_id);
        }else{
            $select->where('im.good_id =?', $good_id);
        }

            //echo $select;die;
        $total = $db->fetchAll($select);
        return $total;
        
    }

    function check_imei_have_sales_out($warehouse_id,$good_id) ###########
    {
        
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('imc'=> 'imei_check_stock'),array('im.imei_sn','im.good_id','g.name AS good_name','im.good_color','gc.name as good_color_name','im.warehouse_id','w.name AS warehouse_name','im.distributor_id','d.store_code','d.title AS distributor_name
            ','im.out_date','m.sn_ref','m.invoice_number'))
        ->joinLeft(array('im' => 'imei'), 'im.imei_sn = imc.imei_sn', null)
        ->joinLeft(array('g' => 'good'), 'im.good_id=g.id', null)
        ->joinLeft(array('gc' => 'good_color'), 'im.good_color=gc.id', null)
        ->joinLeft(array('w' => 'warehouse'), 'im.warehouse_id = w.id ', null)
        ->joinLeft(array('d' => 'distributor'), 'im.distributor_id = d.id', null)
        ->joinLeft(array('m' => 'market'), 'm.sn=im.sales_sn and m.d_id = im.distributor_id and m.good_id=im.good_id and m.good_color=im.good_color', null);

        $select->where('im.distributor_id IS NOT NULL', null);
        
        if (is_array($warehouse_id) && $warehouse_id)
        {
            $select->where('im.warehouse_id in(?)', $warehouse_id);
        }else{
            $select->where('im.warehouse_id =?', $warehouse_id);
        }

        if (is_array($good_id) && $good_id)
        {
            $select->where('im.good_id in(?)', $good_id);
        }else{
            $select->where('im.good_id =?', $good_id);
        }

           // echo $select;die;
        $total = $db->fetchAll($select);
        return $total;
    }

    function check_total_imei_no_exist($warehouse_id,$good_id)########
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('imc'=> 'imei_check_stock'),array('imc.*'))
        ->joinLeft(array('im' => 'imei'), 'im.imei_sn = imc.imei_sn', null)
        ->where('im.imei_sn IS NULL', null);
            //echo $select;die;
        $total = $db->fetchAll($select);
        return $total;
    }

    private function export_check_total_imei_no_exist($warehouse_id,$good_id) ##################
    {
        $data = $this->check_total_imei_no_exist($warehouse_id,$good_id);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'export_check_total_imei_no_exist_' . date('YmdHis') .'.xls';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/xls; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/warehouse/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'IMEI SN',
            'IMPORT DATE'     
        );
        fputcsv($output, $heads);
        
        $i = 2;
        foreach($data as $item) 
        {
            $imei_sn = '="'.$item['imei_sn'].'"';
            $row = array();
            $row[] = $imei_sn;
            $row[] = $item['import_date'];

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

    private function export_check_imei_all_no_sales_out($warehouse_id,$good_id)  #############
    {
        $data = $this->check_imei_all_no_sales_out($warehouse_id,$good_id);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'export_check_imei_all_no_sales_out_' . date('YmdHis') .'.xls';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/xls; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/warehouse/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'IMEI SN',
            'PRODUCT ID',
            'PRODUCT NAME',
            'PRODUCT COLOR ID',
            'PRODUCT COLOR',
            'WAREHOUSE ID',
            'WAREHOUSE'          
        );
        fputcsv($output, $heads);
        
        $i = 2;
        foreach($data as $item) 
        {
            $imei_sn = '="'.$item['imei_sn'].'"';
            $row = array();
            $row[] = $imei_sn;
            $row[] = $item['good_id'];
            $row[] = $item['good_name'];
            $row[] = $item['good_color'];
            $row[] = $item['good_color_name'];
            $row[] = $item['warehouse_id'];
            $row[] = $item['warehouse_name'];
            
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

    private function export_check_imei_no_sales_out($warehouse_id,$good_id)
    {
        $data = $this->check_imei_no_sales_out($warehouse_id,$good_id);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'export_check_imei_no_sales_out_' . date('YmdHis') .'.xls';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/xls; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/warehouse/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'IMEI SN',
            'PRODUCT ID',
            'PRODUCT NAME',
            'PRODUCT COLOR ID',
            'PRODUCT COLOR',
            'WAREHOUSE ID',
            'WAREHOUSE'          
        );
        fputcsv($output, $heads);
        
        $i = 2;
        foreach($data as $item) 
        {
            $imei_sn = '="'.$item['imei_sn'].'"';
            $row = array();
            $row[] = $imei_sn;
            $row[] = $item['good_id'];
            $row[] = $item['good_name'];
            $row[] = $item['good_color'];
            $row[] = $item['good_color_name'];
            $row[] = $item['warehouse_id'];
            $row[] = $item['warehouse_name'];
            
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

    private function export_check_imei_have_sales_out($warehouse_id,$good_id) ###########
    {
        $data = $this->check_imei_have_sales_out($warehouse_id,$good_id);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'export_check_imei_have_sales_out_' . date('YmdHis') .'.xls';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/xls; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/warehouse/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'IMEI SN',
            'PRODUCT ID',
            'PRODUCT NAME',
            'PRODUCT COLOR ID',
            'PRODUCT COLOR',
            'WAREHOUSE ID',
            'WAREHOUSE',
            'RETAILER ID',
            'RETAILER CODE',
            'RETAILER NAME',
            'SALES ORDER NUMBER',
            'INVOICE NUMBER',
            'OUT TIME'            
        );
        fputcsv($output, $heads);
        
        $i = 2;
        foreach($data as $item) 
        {
            $imei_sn = '="'.$item['imei_sn'].'"';
            $row = array();
            $row[] = $imei_sn;
            $row[] = $item['good_id'];
            $row[] = $item['good_name'];
            $row[] = $item['good_color'];
            $row[] = $item['good_color_name'];
            $row[] = $item['warehouse_id'];
            $row[] = $item['warehouse_name'];
            $row[] = $item['distributor_id'];
            $row[] = $item['store_code'];
            $row[] = $item['distributor_name'];
            $row[] = $item['sn_ref'];
            $row[] = $item['invoice_number'];
            $row[] = $item['out_date'];
            
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

    public function stockCardKerryAction() ####
    {
        require_once 'warehouse' . DIRECTORY_SEPARATOR . 'stock-card-kerry.php';
    }

    private function _exportStockCardKerry($params) #############
    {
        //echo $st_date;die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');

        $db = Zend_Registry::get('db');
        $QWarehouse = new Application_Model_Warehouse();
        $data = $QWarehouse->getStockCardKerry($params);
        //print_r($data);die;

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'Stock Card Kerry'. ' - '.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $headss = array(
            'NO',
            'action_type',
            'transaction_type',
            'doc_type',
            'IN sn',
            'IN sn_ref',
            'OUT sn',
            'OUT sn_ref',
            'action_time',
            'warehouse_id',
            'warehouse_name',
            'cat_id',
            'cat_name',
            'good_id',
            'product_name',
            'good_color',
            'color_name',
            'num_qty',
            'num_qty_demo',
            
        );

        fputcsv($output, $headss);
        $QStaff         = new Application_Model_Staff();
        $staff          = $QStaff->get_cache();
        

        if (!$data){return;}
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $i =1;

        $total_qty =0;
        foreach($data as $item){

            $action_type = $item['action_type'];
            $sn = '="'.$item['sn'].'"';
            $total_qty += $item['total_qty'];
            $row = array();
            $row[] = $i++;
            $row[] = $item['action_type'];
            $row[] = $item['transaction_type'];
            $row[] = $item['doc_type'];

            if($action_type=='IN'){
                $row[] = $sn;
                $row[] = $item['sn_ref'];
                $row[] = '';
                $row[] = '';
            }else{
                $row[] = '';
                $row[] = '';
                $row[] = $sn;
                $row[] = $item['sn_ref'];
            }
            
            $row[] = $item['action_time'];
            $row[] = $item['warehouse_id'];
            $row[] = $item['warehouse_name'];
            $row[] = $item['cat_id'];
            $row[] = $item['cat_name'];
            $row[] = $item['good_id'];
            $row[] = $item['product_name'];
            $row[] = $item['good_color'];
            $row[] = $item['color_name'];
            $row[] = $item['num_qty'];
            $row[] = $item['num_qty_demo'];
            //$row[] = $total_qty;
            
            
            fputcsv($output, $row);
            unset($row);
            unset($item);

        }
        
        unset($data);
        // die;
    }

    private function _exportStockCardByStore($params) ##########3
    {
        //echo $st_date;die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        $QWarehouse = new Application_Model_Warehouse();

        //print_r($params['cat_id'][0]);die;
        if($params['cat_id'][0]==11){  //Phone
            $data = $QWarehouse->getStockCardFinance($params);
        }else if($params['cat_id'][0]==13){ // Digital
            $data = $QWarehouse->getStockCardDigitalFinance($params);
        }else if($params['cat_id'][0]==12){ // Acc
            $data = $QWarehouse->getStockCardAccFinance($params);
        }

        //print_r($data);die;

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'Stock Daily Report'. ' - '.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $headss = array(
            'NO',
            'Date',
            'ยอดยกมา',
            'IN PO',
            'IN Return/Renew',
            'IN โอนเข้า',
            'OUT โอนออก',
            'OUT Invoice',
            'ยอดคงเหลือ',
            'warehouse_id',
            'warehouse_name',
            'good_id',
            'good_name',
            'good_color',
            'color_name'
            
        );

        fputcsv($output, $headss);
        $QStaff         = new Application_Model_Staff();
        $staff          = $QStaff->get_cache();
        

        if (!$data){return;}
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $i =1;

        $total_qty =0;

        if (!isset($params['warehouse_id'])){
          $warehouse_all ="1";
      }else {
          $warehouse_all ="0";
      }


      if($warehouse_all =="1"){

        foreach($data as $item){

            $row = array();
            $row[] = $i++;
            $row[] = $item['stock_date'];
            $row[] = $item['std_qty'];
            $row[] = $item['po_qty'];
            $row[] = $item['ro_qty'];
            $row[] = $item['csi_qty'];
            $row[] = $item['cso_qty'];
            $row[] = $item['inv_qty'];
            $row[] = $item['total_qty'];
            $row[] = '-';
            $row[] = 'All Company';
            $row[] = $item['good_id'];
            $row[] = $item['good_name'];
            $row[] = $item['good_color'];
            $row[] = $item['color_name'];
            
            
            fputcsv($output, $row);
            unset($row);
            unset($item);

        }


    }else{

        foreach($data as $item){

            $row = array();
            $row[] = $i++;
            $row[] = $item['stock_date'];
            $row[] = $item['std_qty'];
            $row[] = $item['po_qty'];
            $row[] = $item['ro_qty'];
            $row[] = $item['csi_qty'];
            $row[] = $item['cso_qty'];
            $row[] = $item['inv_qty'];
            $row[] = $item['total_qty'];
            $row[] = $item['warehouse_id'];
            $row[] = $item['warehouse_name'];
            $row[] = $item['good_id'];
            $row[] = $item['good_name'];
            $row[] = $item['good_color'];
            $row[] = $item['color_name'];
            
            
            fputcsv($output, $row);
            unset($row);
            unset($item);

        }
    }
    
    unset($data);
        // die;
}

    private function _exportStockCardFinance($params) #########
    {
        //echo $st_date;die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        $QWarehouse = new Application_Model_Warehouse();

        //print_r($params['cat_id'][0]);die;
        if($params['cat_id'][0]==11){  //Phone
            $data = $QWarehouse->getStockCardFinance($params);
        }else if($params['cat_id'][0]==13){ // Digital
            $data = $QWarehouse->getStockCardDigitalFinance($params);
        }else if($params['cat_id'][0]==12){ // Acc
            $data = $QWarehouse->getStockCardAccFinance($params);
        }

        //print_r($data);die;

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'Stock Card Finance'. ' - '.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $headss = array(
            'NO',
            'Date',
            'ยอดยกมา',
            'IN PO',
            'IN Return/Renew',
            'IN โอนเข้า',
            'OUT โอนออก',
            'OUT Invoice',
            'ยอดคงเหลือ',
            'warehouse_id',
            'warehouse_name',
            'good_id',
            'good_name',
            'good_color',
            'color_name'
            
        );

        fputcsv($output, $headss);
        $QStaff         = new Application_Model_Staff();
        $staff          = $QStaff->get_cache();
        

        if (!$data){return;}
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $i =1;

        $total_qty =0;

        if (!isset($params['warehouse_id'])){
          $warehouse_all ="1";
      }else {
          $warehouse_all ="0";
      }


      if($warehouse_all =="1"){

        foreach($data as $item){

            $row = array();
            $row[] = $i++;
            $row[] = $item['stock_date'];
            $row[] = $item['std_qty'];
            $row[] = $item['po_qty'];
            $row[] = $item['ro_qty'];
            $row[] = $item['csi_qty'];
            $row[] = $item['cso_qty'];
            $row[] = $item['inv_qty'];
            $row[] = $item['total_qty'];
            $row[] = '-';
            $row[] = 'All Company';
            $row[] = $item['good_id'];
            $row[] = $item['good_name'];
            $row[] = $item['good_color'];
            $row[] = $item['color_name'];
            
            
            fputcsv($output, $row);
            unset($row);
            unset($item);

        }


    }else{

        foreach($data as $item){

            $row = array();
            $row[] = $i++;
            $row[] = $item['stock_date'];
            $row[] = $item['std_qty'];
            $row[] = $item['po_qty'];
            $row[] = $item['ro_qty'];
            $row[] = $item['csi_qty'];
            $row[] = $item['cso_qty'];
            $row[] = $item['inv_qty'];
            $row[] = $item['total_qty'];
            $row[] = $item['warehouse_id'];
            $row[] = $item['warehouse_name'];
            $row[] = $item['good_id'];
            $row[] = $item['good_name'];
            $row[] = $item['good_color'];
            $row[] = $item['color_name'];
            
            
            fputcsv($output, $row);
            unset($row);
            unset($item);

        }
    }
    
    unset($data);
        // die;
}

    public function btnExportCheckImeiWarehouseAction() #########3
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $params = array(
            'warehouse_id'  => $this->getRequest()->getParam('export_warehouse_id'),
            'good_id'       => $this->getRequest()->getParam('export_good_id'),
            'color_id'      => $this->getRequest()->getParam('export_color_id'),
            'at_from'       => $this->getRequest()->getParam('log_at_from'),
            'to_from'       => $this->getRequest()->getParam('log_at_to'),
            'account_id'    => $userStorage->id
        );

        $db = Zend_Registry::get('db');

        $QLCIW = new Application_Model_LogCheckImeiWarehouse();
        $data = $QLCIW->getHistoryLog($params);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'Log Check Imei Warehouse'. ' - '.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $heads = array(
            'No.',
            'Log Date',
            'Warehouse Name',
            'Product Name',
            'Color Name',
            'Total Storage',
            'Total Checked',
            'Total Correct',
            'Total Incorrect',
            'Wrong Format',
            'Wrong Duplicate',
            'Wrong Not Found',
            'Wrong Warehouse',
            'Wrong Product',
            'Wrong Color',
            'Wrong Exported',
            'Wrong Unable Export'
        );

        if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID))){
            $heads = array(
                'No.',
                'Log Date',
                'Warehouse Name',
                'Product Name',
                'Color Name',
                'Total Storage',
                'Total Checked',
                'Total Correct',
                'Total Incorrect',
                'Wrong Format',
                'Wrong Duplicate',
                'Wrong Not Found',
                'Wrong Warehouse',
                'Wrong Product',
                'Wrong Color',
                'Wrong Exported',
                'Wrong Unable Export',
                'Checked By'
            );
        }

        fputcsv($output, $heads);

        $i = 1;

        foreach($data as $item){

            $row = array();
            $row[] = $i++;
            $row[] = $item['at_date'];
            $row[] = $item['warehouse_name'];
            $row[] = $item['product_name'];
            $row[] = $item['color_name'];
            $row[] = $item['total_qty'];
            $row[] = $item['check_qty'];
            $row[] = $item['correct'];
            $row[] = $item['incorrect'];
            $row[] = $item['w_notformat'];
            $row[] = $item['w_duplicate'];
            $row[] = $item['w_notfound'];
            $row[] = $item['w_notwh'];
            $row[] = $item['w_notproduct'];
            $row[] = $item['w_notcolor'];
            $row[] = $item['w_exported'];
            $row[] = $item['w_unableexport'];

            if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID))){
                $row[] = $item['fullname'];
            }
            
            fputcsv($output, $row);
            unset($row);
            unset($item);

        }
        
        unset($data);
        // die;
    }

    public function getTotalStorageForCheckWarehouseAction(){ ########

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $warehouse_id = $this->getRequest()->getParam('warehouse_id');
        $good_id = $this->getRequest()->getParam('good_id');
        $color_id = $this->getRequest()->getParam('color_id');

        $QCWL = new Application_Model_CheckWarehouseLine();

        $total_storage = $QCWL->getTotalStorage($warehouse_id,$good_id,$color_id);

        echo json_encode($total_storage);

    }

    public function finishLineAction(){  #######
       
        $flashMessenger = $this->_helper->flashMessenger;

        if ($this->getRequest()->getMethod() == 'POST') {

            try {

                $db = Zend_Registry::get('db');

                $db->beginTransaction();
                
                $select_line = $this->getRequest()->getParam('select_line');

                if(!$select_line){
                    echo json_encode(['status' => 400, 'message' => 'Can not Finish line, Please select line.']);
                    exit();
                }

                $QCWL = new Application_Model_CheckWarehouseLine();

                $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                $create_by = $QCWL->getCreatedLineBy($select_line);

                if(!$create_by){
                    echo json_encode(['status' => 400, 'message' => 'Can not Finish line, Not find account created.']);
                    exit();
                }

                if($userStorage->id != $create_by['create_by']){
                    echo json_encode(['status' => 400, 'message' => 'Can not Finish line, This account not created.']);
                    exit();
                }

                $data = array(
                    'status' => 0,
                    'finish_line_by' => $userStorage->id,
                    'finish_line_date' => date('Y-m-d H:i:s')
                );

                $where  = $QCWL->getAdapter()->quoteInto('id = ?', $select_line);

                $QCWL->update($data, $where);

                $db->commit();

                $flashMessenger->setNamespace('success')->addMessage('Done!');

                echo json_encode(['status' => 200, 'message' => 'Done.']);
                exit();

            } catch (Exception $e) {

                $db->rollback();

                echo json_encode(['status' => 400, 'message' => 'Can not Finish line : ' . $e->getMessage()]);
                exit();
            }
        }

        echo json_encode(['status' => 400, 'message' => 'Not Allow.']);
        exit();
    }

    public function getColorByProductAction(){ #########3

        $good_id = $this->getRequest()->getParam('good_id');

        $QGood = new Application_Model_Good();

        if ($good_id){
            $where = $QGood->getAdapter()->quoteInto('id = ?', $good_id);
            $good = $QGood->fetchRow($where);

            if ($good){
                $getColor = array_filter(explode(',', $good->color));
                
                if ($getColor){
                    $QGoodColor = new Application_Model_GoodColor();
                    $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $getColor);

                    $colors = $QGoodColor->fetchAll($where);
                    echo json_encode($colors->toArray());
                    exit();
                }

                echo json_encode(array());
                exit();
            }

        } else {
            echo json_encode(array());
            exit();
        }
    }

    public function btnExportCheckImeiWarehouseLineAction() #######
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $line = $this->getRequest()->getParam('line');
        $warehouse_id = $this->getRequest()->getParam('warehouse_id');
        $good_id = $this->getRequest()->getParam('good_id');
        $color_id = $this->getRequest()->getParam('color_id');

        $QCWL = new Application_Model_CheckWarehouseLine();
        $data = $QCWL->getLineScannedDetailsForExcel($line,$warehouse_id,$good_id,$color_id);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'Check Imei line Warehouse'. ' - '.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $heads = array(
            'No.',
            'Imei',
            'Scanned Date',
            'Warehouse Name',
            'Product Name',
            'Color Name'
        );

        if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID))){
            $heads = array(
                'No.',
                'Imei',
                'Scanned Date',
                'Warehouse Na
                me',
                'Product Name',
                'Color Name',
                'Checked By'
            );
        }

        fputcsv($output, $heads);

        $i = 1;

        foreach($data as $item){

            $row = array();
            $row[] = $i++;
            $row[] = $item['imei'];
            $row[] = $item['imei_created_date'];
            $row[] = $item['warehouse_name'];
            $row[] = $item['good_name'];
            $row[] = $item['good_color_name'];

            if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID))){
                $row[] = $item['fullname'];
            }
            
            fputcsv($output, $row);
            unset($row);
            unset($item);

        }
        
        unset($data);
        // die;
    }

    public function exportReportImeiRework($data){ ##########

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'Report_Imei_rework'. ' - '.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $heads = array(
            'Report ID',
            'CO Swap',
            'PO',
            'CO END',
            'Product Model',
            'Product Name',
            'Product Color',
            'Product Type',
            'Created By',
            'Created Date'
        );

        fputcsv($output, $heads);

        $i = 1;

        foreach($data as $value){

            $row = array();

            $row[] = $value['id'];
            $row[] = $value['co_ref'];
            $row[] = $value['po_ref'];
            $row[] = $value['co_end_ref'];
            $row[] = $value['good_model'];
            $row[] = $value['good_name'];
            $row[] = $value['color_name'];

            switch ($value['good_type']) {
                case '1':
                $row[] = 'Normal';
                break;
                case '2':
                $row[] = 'Demo';
                break;
                case '5':
                $row[] = 'APK';
                break;
                default:
                $row[] = '';
                break;
            }

            $row[] = $value['fullname'];
            $row[] = $value['created_date'];
            
            fputcsv($output, $row);
            unset($row);
            unset($value);

        }
        
        unset($data);
    }

    public function exportReportImeiFactoryRework($data){  #################

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'Report_Imei_factory_rework'. ' - '.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $heads = array(
            'Imei',
            'Product Model',
            'Product Name',
            'Product Color',
            'Product Type',
            'Flag Date',
            'Flag By',
            'Status'
        );

        fputcsv($output, $heads);

        foreach($data as $value){

            $row = array();

            $row[] = $value['imei_sn'];
            $row[] = $value['good_model'];
            $row[] = $value['good_name'];
            $row[] = $value['color_name'];

            switch ($value['type']) {
                case '1':
                $row[] = 'Normal';
                break;
                case '2':
                $row[] = 'Demo';
                break;
                case '5':
                $row[] = 'APK';
                break;
                default:
                $row[] = '';
                break;
            }

            $row[] = $value['flag_rework_date'];
            $row[] = $value['fullname'];

            $status = '';

            if($value['flag_rework_status'] == '1'){
                $status = 'Flag';
            }
            
            $row[] = $status;
            
            fputcsv($output, $row);
            unset($row);
            unset($value);

        }
        
        unset($data);
    }

    public function exportBorrowingReport($data){ ##################3

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'Borrowing Report'. ' - '.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $heads = array(
            'No.',
            'Request Number',
            'Grade',
            'Type',
            'Code',
            'Request By',
            'Position',
            'Area',
            'Request Date',
            'Return Date',
            'CO Number',
            'Receive Date',
            'Status',
            'Lost',
            'Remark',
            'ADMIN Approved By',
            'ASM Approved By',
            'RM Approved By',
            'AREA Approved By',
            'OPERATOR Approved By'
        );

        fputcsv($output, $heads);

        $i = 1;

        foreach($data as $value){

            $row = array();

            $row[] = $i++;
            $row[] = $value['rq'];

            switch ($value['product_grade']) {
                case '1':
                $row[] = 'A';
                break;
                case '2':
                $row[] = 'B';
                break;
                case '3':
                $row[] = 'Demo';
                break;
                case '4':
                $row[] = 'APK';
                break;
                case '5':
                $row[] = 'Prototype';
                break;
                default:
                $row[] = '-';
                break;
            }

            // switch ($value['borrowing_type']) {
            //     case '1':
            //         // $row[] = 'Demo for Event';
            //         $row[] = 'เบิกยืมพนักงาน';
            //         break;
            //     case '2':
            //         // $row[] = 'Complimentary';
            //         $row[] = 'อภินันทนาการ';
            //         break;
            //     case '3':
            //         // $row[] = 'Replace Customer Service';
            //         $row[] = 'เบิกเปลี่ยนเครื่องลูกค้า';
            //         break;
            //     case '4':
            //         $row[] = 'Prototype';
            //         break;
            //     default:
            //         $row[] = '-';
            //         break;
            // }

            $row[] = $value['hrs_department_name'];

            $row[] = $value['code'];
            $row[] = $value['fullname'];
            $row[] = $value['position_name'];
            $row[] = $value['name'];
            $row[] = $value['created_date'];
            // $row[] = $value['return_date'];
            $row[] = $value['return_date'] ? $value['return_date'] : 'No Return';
            $row[] = $value['sn_ref'];

            if($value['status'] == '14'){
                $row[] = $value['update_datetime'];
            }else{
                $row[] = '-';
            }

            switch ($value['status']) {
                case '1':
                if($value['wms_status'] == 1){
                    $row[] = 'Prepare product';
                }else{
                    $row[] = 'Waiting approval from Finance';
                }
                break;
                case '2':
                $row[] = 'Waiting approval from Admin';
                break;
                case '3':
                $row[] = 'Waiting approval from ASM';
                break;
                case '4':
                $row[] = 'Waiting approval from RD';
                break;
                case '5':
                $row[] = 'Waiting approval from Area Director';
                break;
                case '6':
                $row[] = 'Waiting approval from Operation Director';
                break;
                case '7':
                $row[] = 'Waiting approval from Manager';
                break;
                case '8':
                $row[] = '';
                break;
                case '9':
                $row[] = 'No Approved';
                break;
                case '10':
                $row[] = '';
                break;
                case '11':
                $row[] = 'Ready to shipping';
                break;
                case '12':
                $row[] = 'WMS no appoved';
                break;
                case '13':
                $row[] = 'Received product';
                break;
                case '14':
                $row[] = 'Return product';
                break;
            }

            $row[] = $value['missing'];
            $row[] = $value['remark'];

            $row[] = $value['admin_fullname'];
            $row[] = $value['asm_fullname'];
            $row[] = $value['rm_fullname'];
            $row[] = $value['area_fullname'];
            $row[] = $value['op_fullname'];
            
            fputcsv($output, $row);
            unset($row);
            unset($value);

        }
        
        unset($data);
        // die;
    }

    public function exportBorrowingReportImei($data){  #############3

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'Borrowing Report IMEI'. ' - '.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $heads = array(
            'No.',
            'Request Number',
            'Grade',
            'Type',
            'Code',
            'Request By',
            'Position',
            'Area',
            'Request Date',
            'Return Date',
            'CO Number',
            'Receive Date',
            'Status',
            'Lost',
            'Remark',
            'ADMIN Approved By',
            'ASM Approved By',
            'RM Approved By',
            'AREA Approved By',
            'OPERATOR Approved By'
        );

        fputcsv($output, $heads);

        $i = 1;

        $QBL = new Application_Model_BorrowingList();
        $QBT = new Application_Model_BorrowingTran();

        foreach($data as $value){

            $getDetailsReturn = $QBL->getItemByReturn($value['id'], true, $value['wms_return_date']);

            $getImeiBorrowing = $QBT->getImeiBorrowing($value['id']);

            $arrImeiBorrowing = [];

            foreach ($getDetailsReturn as $key => $value_sub) {
                $buckket = [];
                foreach ($getImeiBorrowing as $key_sub) {
                    if($value_sub['good_id'] == $key_sub['good_id'] && $value_sub['good_color_id'] == $key_sub['good_color']){
                        array_push($buckket, $key_sub);
                    }
                }

                $arrImeiBorrowing[$key] = $buckket;
            }

            $QIM = new Application_Model_ImeiMissing();

            $getImeiMissing = $QIM->getImeiMissingByRQID($value['id']);
            $arrayImeiMissing = [];

            foreach ($getImeiMissing as $key) {
                array_push($arrayImeiMissing, $key['imei_sn']);
            }

            $row = array();

            $row[] = $i++;
            $row[] = $value['rq'];

            switch ($value['product_grade']) {
                case '1':
                $row[] = 'A';
                break;
                case '2':
                $row[] = 'B';
                break;
                case '3':
                $row[] = 'Demo';
                break;
                case '4':
                $row[] = 'APK';
                break;
                case '5':
                $row[] = 'Prototype';
                break;
                default:
                $row[] = '-';
                break;
            }

            // switch ($value['borrowing_type']) {
            //     case '1':
            //         // $row[] = 'Demo for Event';
            //         $row[] = 'เบิกยืมพนักงาน';
            //         break;
            //     case '2':
            //         // $row[] = 'Complimentary';
            //         $row[] = 'อภินันทนาการ';
            //         break;
            //     case '3':
            //         // $row[] = 'Replace Customer Service';
            //         $row[] = 'เบิกเปลี่ยนเครื่องลูกค้า';
            //         break;
            //     case '4':
            //         $row[] = 'Prototype';
            //         break;
            //     default:
            //         $row[] = '-';
            //         break;
            // }

            $row[] = $value['hrs_department_name'];

            $row[] = $value['code'];
            $row[] = $value['fullname'];
            $row[] = $value['position_name'];
            $row[] = $value['name'];
            $row[] = $value['created_date'];
            // $row[] = $value['return_date'];
            $row[] = $value['return_date'] ? $value['return_date'] : 'No Return';
            $row[] = $value['sn_ref'];

            if($value['status'] == '14'){
                $row[] = $value['update_datetime'];
            }else{
                $row[] = '-';
            }

            switch ($value['status']) {
                case '1':
                if($value['wms_status'] == 1){
                    $row[] = 'Prepare product';
                }else{
                    $row[] = 'Waiting approval from Finance';
                }
                break;
                case '2':
                $row[] = 'Waiting approval from Admin';
                break;
                case '3':
                $row[] = 'Waiting approval from ASM';
                break;
                case '4':
                $row[] = 'Waiting approval from RD';
                break;
                case '5':
                $row[] = 'Waiting approval from Area Director';
                break;
                case '6':
                $row[] = 'Waiting approval from Operation Director';
                break;
                case '7':
                $row[] = 'Waiting approval from Manager';
                break;
                case '8':
                $row[] = '';
                break;
                case '9':
                $row[] = 'No Approved';
                break;
                case '10':
                $row[] = '';
                break;
                case '11':
                $row[] = 'Ready to shipping';
                break;
                case '12':
                $row[] = 'WMS no appoved';
                break;
                case '13':
                $row[] = 'Received product';
                break;
                case '14':
                $row[] = 'Return product';
                break;
            }

            $row[] = $value['missing'];
            $row[] = $value['remark'];

            $row[] = $value['admin_fullname'];
            $row[] = $value['asm_fullname'];
            $row[] = $value['rm_fullname'];
            $row[] = $value['area_fullname'];
            $row[] = $value['op_fullname'];
            
            fputcsv($output, $row);
            unset($row);
            unset($value);

            foreach ($getDetailsReturn as $key_sub_2 => $value_sub_2) {

                $row = array();

                $row[] = '';
                $row[] = 'Product Code : ' . $value_sub_2['good_name'];
                $row[] = 'Product Name : ' . $value_sub_2['good_main_name'];
                $row[] = 'Product Color : ' . $value_sub_2['color_name'];
                $row[] = 'Total Quantity : ' . $value_sub_2['total_qty'];

                fputcsv($output, $row);
                unset($row);
                
                foreach ($arrImeiBorrowing[$key_sub_2] as $key_sub_3 => $value_sub_3) {

                    $textMissing = '';

                    if(in_array($value_sub_3['imei'], $arrayImeiMissing)){
                        $textMissing = ' (เครื่องศูนย์หาย)';
                    }

                    $row = array();

                    $row[] = '';
                    $row[] = 'IMEI No.' . $key_sub_3;
                    $row[] = $value_sub_3['imei'] . $textMissing;
                    
                    fputcsv($output, $row);
                    unset($row);
                }

            }

            unset($getDetailsReturn);
            unset($arrImeiBorrowing);

        }
        
        unset($data);
        // die;
    }

    // public function exportBorrowingReportImeiPAnn($data){

    //     $this->_helper->layout->disableLayout();
    //     $this->_helper->viewRenderer->setNoRender(true);

    //     set_time_limit(0);
    //     error_reporting(~E_ALL);
    //     ini_set('display_error', 0);
    //     //ini_set('memory_limit', -1);
    //     $filename = 'Borrowing Report IMEI'. ' - '.date('d-m-Y H-i-s');
    //     // output headers so that the file is downloaded rather than displayed
    //     header('Content-Type: text/csv; charset=utf-8');
    //     header('Content-Disposition: attachment; filename='.$filename.'.csv');
    //     // echo "\xEF\xBB\xBF"; // UTF-8 BOM
    //     echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
    //     $output = fopen('php://output', 'w');

    //     $heads = array(
    //         'No.',
    //         'Request Number',
    //         'Grade',
    //         'Type',
    //         'Code',
    //         'Request By',
    //         'Position',
    //         'Area',
    //         'Request Date',
    //         'Return Date',
    //         'CO Number',
    //         'Receive Date',
    //         'Status',
    //         'Lost',
    //         'Remark',

    //         'Product Code',
    //         'Product Name',
    //         'Product Color',
    //         'IMEI'
    //     );

    //     fputcsv($output, $heads);

    //     $i = 1;

    //     $QBL = new Application_Model_BorrowingList();
    //     $QBT = new Application_Model_BorrowingTran();

    //     foreach($data as $value){

    //         $getDetailsReturn = $QBL->getItemByReturn($value['id'], true, $value['wms_return_date']);

    //         $getImeiBorrowing = $QBT->getImeiBorrowing($value['id']);

    //         $arrImeiBorrowing = [];

    //         foreach ($getDetailsReturn as $key => $value_sub) {
    //             $buckket = [];
    //             foreach ($getImeiBorrowing as $key_sub) {
    //                 if($value_sub['good_id'] == $key_sub['good_id'] && $value_sub['good_color_id'] == $key_sub['good_color']){
    //                     array_push($buckket, $key_sub);
    //                 }
    //             }

    //             $arrImeiBorrowing[$key] = $buckket;
    //         }

    //         $QIM = new Application_Model_ImeiMissing();

    //         $getImeiMissing = $QIM->getImeiMissingByRQID($value['id']);
    //         $arrayImeiMissing = [];

    //         foreach ($getImeiMissing as $key) {
    //             array_push($arrayImeiMissing, $key['imei_sn']);
    //         }

    //         foreach ($getDetailsReturn as $key_sub_2 => $value_sub_2) {
    
    //             foreach ($arrImeiBorrowing[$key_sub_2] as $key_sub_3 => $value_sub_3) {

    //                 $row = array();

    //                 $row[] = $i++;
    //                 $row[] = $value['rq'];

    //                 switch ($value['product_grade']) {
    //                     case '1':
    //                         $row[] = 'A';
    //                         break;
    //                     case '2':
    //                         $row[] = 'B';
    //                         break;
    //                     default:
    //                         $row[] = '-';
    //                         break;
    //                 }

    //                 switch ($value['borrowing_type']) {
    //                     case '1':
    //                         // $row[] = 'Demo for Event';
    //                         $row[] = 'เบิกยืมพนักงาน';
    //                         break;
    //                     case '2':
    //                         // $row[] = 'Complimentary';
    //                         $row[] = 'อภินันทนาการ';
    //                         break;
    //                     case '3':
    //                         // $row[] = 'Replace Customer Service';
    //                         $row[] = 'เบิกเปลี่ยนเครื่องลูกค้า';
    //                         break;
    //                     case '4':
    //                         $row[] = 'Prototype';
    //                         break;
    //                     default:
    //                         $row[] = '-';
    //                         break;
    //                 }

    //                 $row[] = $value['code'];
    //                 $row[] = $value['fullname'];
    //                 $row[] = $value['position_name'];
    //                 $row[] = $value['name'];
    //                 $row[] = $value['created_date'];
    //                 $row[] = $value['return_date'];
    //                 $row[] = $value['sn_ref'];

    //                 if($value['status'] == '14'){
    //                     $row[] = $value['update_datetime'];
    //                 }else{
    //                     $row[] = '-';
    //                 }

    //                 switch ($value['status']) {
    //                     case '1':
    //                         if($value['wms_status'] == 1){
    //                             $row[] = 'Prepare product';
    //                         }else{
    //                             $row[] = 'Waiting approval from Finance';
    //                         }
    //                         break;
    //                     case '2':
    //                         $row[] = 'Waiting approval from Admin';
    //                         break;
    //                     case '3':
    //                         $row[] = 'Waiting approval from ASM';
    //                         break;
    //                     case '4':
    //                         $row[] = 'Waiting approval from RD';
    //                         break;
    //                     case '5':
    //                         $row[] = 'Waiting approval from Area Director';
    //                         break;
    //                     case '6':
    //                         $row[] = 'Waiting approval from Operation Director';
    //                         break;
    //                     case '7':
    //                         $row[] = 'Waiting approval from Manager';
    //                         break;
    //                     case '8':
    //                         $row[] = '';
    //                         break;
    //                     case '9':
    //                         $row[] = 'No Approved';
    //                         break;
    //                     case '10':
    //                         $row[] = '';
    //                         break;
    //                     case '11':
    //                         $row[] = 'Ready to shipping';
    //                         break;
    //                     case '12':
    //                         $row[] = 'WMS no appoved';
    //                         break;
    //                     case '13':
    //                         $row[] = 'Received product';
    //                         break;
    //                     case '14':
    //                         $row[] = 'Return product';
    //                         break;
    //                 }

    //                 $row[] = $value['missing'];
    //                 $row[] = $value['remark'];

    //                 $row[] = $value_sub_2['good_name'];
    //                 $row[] = $value_sub_2['good_main_name'];
    //                 $row[] = $value_sub_2['color_name'];

    //                 $textMissing = '';

    //                 if(in_array($value_sub_3['imei'], $arrayImeiMissing)){
    //                     $textMissing = ' (เครื่องศูนย์หาย)';
    //                 }

    //                 $row[] = $value_sub_3['imei'] . $textMissing;
    
    //                 fputcsv($output, $row);
    //                 unset($row);
    //             }

    //         }

    //         unset($getDetailsReturn);
    //         unset($arrImeiBorrowing);

    //     }
    
    //     unset($data);
    //     // die;
    // }

    public function exportBorrowingReportImeiPAnn($data){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'Borrowing Report IMEI'. ' - '.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $heads = array(
            'No.',

            'IMEI Number',
            'Product Code',
            'Product Name',
            'Product Color',

            'Request Number',
            'Grade',
            'Type',
            'Code',
            'Request By',
            'Position',
            'Area',
            'Request Date',
            'CO Issued Number',
            'Receive Date',

            'ORG Full Name',
            'Event / Program Name',
            'Event / Program Start Period',
            'Event / Program End Period',

            'Return Date',
            'CO Closed Number',
            'Status',

            'Monitoring Flag',

            'Remark',

            'ADMIN Approved By',
            'ASM Approved By',
            'RM Approved By',
            'AREA Approved By',
            'OPERATOR Approved By'
        );

        fputcsv($output, $heads);

        $i = 1;

        $QBL = new Application_Model_BorrowingList();
        $QBT = new Application_Model_BorrowingTran();

        foreach($data as $value){

            $getDetailsReturn = $QBL->getItemByReturn($value['id'], true, $value['wms_return_date']);

            $getImeiBorrowing = $QBT->getImeiBorrowing($value['id']);

            $arrImeiBorrowing = [];

            foreach ($getDetailsReturn as $key => $value_sub) {
                $buckket = [];
                foreach ($getImeiBorrowing as $key_sub) {
                    if($value_sub['good_id'] == $key_sub['good_id'] && $value_sub['good_color_id'] == $key_sub['good_color']){
                        array_push($buckket, $key_sub);
                    }
                }

                $arrImeiBorrowing[$key] = $buckket;
            }

            $QIM = new Application_Model_ImeiMissing();

            $getImeiMissing = $QIM->getImeiMissingByRQID($value['id']);
            $arrayImeiMissing = [];

            foreach ($getImeiMissing as $key) {
                array_push($arrayImeiMissing, $key['imei_sn']);
            }

            foreach ($getDetailsReturn as $key_sub_2 => $value_sub_2) {
                
                foreach ($arrImeiBorrowing[$key_sub_2] as $key_sub_3 => $value_sub_3) {

                    $row = array();

                    $row[] = $i++;

                    $textMissing = '';

                    if(in_array($value_sub_3['imei'], $arrayImeiMissing)){
                        $textMissing = ' (เครื่องศูนย์หาย)';
                    }

                    $row[] = $value_sub_3['imei'] . $textMissing;

                    $row[] = $value_sub_2['good_name'];
                    $row[] = $value_sub_2['good_main_name'];
                    $row[] = $value_sub_2['color_name'];

                    $row[] = $value['rq'];

                    switch ($value['product_grade']) {
                        case '1':
                        $row[] = 'A';
                        break;
                        case '2':
                        $row[] = 'B';
                        break;
                        case '3':
                        $row[] = 'Demo';
                        break;
                        case '4':
                        $row[] = 'APK';
                        break;
                        case '5':
                        $row[] = 'Prototype';
                        break;
                        default:
                        $row[] = '';
                        break;
                    }

                    // switch ($value['borrowing_type']) {
                    //     case '1':
                    //         // $row[] = 'Demo for Event';
                    //         $row[] = 'เบิกยืมพนักงาน';
                    //         break;
                    //     case '2':
                    //         // $row[] = 'Complimentary';
                    //         $row[] = 'อภินันทนาการ';
                    //         break;
                    //     case '3':
                    //         // $row[] = 'Replace Customer Service';
                    //         $row[] = 'เบิกเปลี่ยนเครื่องลูกค้า';
                    //         break;
                    //     case '4':
                    //         $row[] = 'Prototype';
                    //         break;
                    //     default:
                    //         $row[] = '';
                    //         break;
                    // }

                    $row[] = $value['hrs_department_name'];

                    $row[] = $value['code'];
                    $row[] = $value['fullname'];
                    $row[] = $value['position_name'];
                    $row[] = $value['name'];
                    $row[] = $value['created_date'];
                    $row[] = $value['first_sn_ref'];
                    $row[] = $value['completed_date'];

                    $row[] = $value['org_full_name'];
                    $row[] = $value['event_program_name'];
                    $row[] = $value['event_program_start_period_date'];
                    // $row[] = $value['return_date'];
                    $row[] = $value['return_date'] ? $value['return_date'] : 'No Return';

                    $bucket_status = 'Open';

                    if($value['status'] == '14'){
                        $row[] = $value['update_datetime'];
                        $bucket_status = 'Closed';
                    }else{
                        $row[] = '';
                    }

                    if($value['first_sn_ref'] != $value['sn_ref']){
                        if($textMissing != ''){
                            $row[] = 'Lose';
                        }else{
                            $row[] = $value['sn_ref'];
                        }
                    }else{
                        $row[] = '';
                    }

                    $row[] = $bucket_status;

                    $date1=date_create($value['return_date']);
                    $date2=date_create(date('Y-m-d'));
                    $diff=date_diff($date2,$date1);
                    $diffDate = $diff->format("%R%a");

                    if($diffDate < 0 && $bucket_status == 'Open'){
                        $row[] = '! (' . $diffDate . ' Day)';
                    }else{
                        $row[] = '';
                        // $row[] = substr($diffDate,1);
                    }

                    $row[] = $value['remark'];

                    $row[] = $value['admin_fullname'];
                    $row[] = $value['asm_fullname'];
                    $row[] = $value['rm_fullname'];
                    $row[] = $value['area_fullname'];
                    $row[] = $value['op_fullname'];
                    
                    fputcsv($output, $row);
                    unset($row);
                }

            }

            unset($getDetailsReturn);
            unset($arrImeiBorrowing);

        }
        
        unset($data);
        // die;
    }

    public function exportBorrowingReturnImei($data){ ############

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'Borrowing Return IMEI'. ' - '.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $heads = array(
            'No.',

            'IMEI Number',
            'Product Code',
            'Product Name',
            'Product Color',

            'Request Number',
            'Grade',
            'Type',
            'Code',
            'Request By',
            'Position',
            'Area',
            'Request Date',
            'CO Issued Number',
            'Receive Date',

            'ORG Full Name',
            'Event / Program Name',
            'Event / Program Start Period',
            'Event / Program End Period',

            'Return Date',
            'CO Closed Number',
            'Status',

            'Monitoring Flag',

            'Remark'
        );

        fputcsv($output, $heads);

        $i = 1;

        foreach($data as $value){

            $date1=date_create($value['return_date']);
            $date2=date_create(date('Y-m-d'));
            $diff=date_diff($date2,$date1);
            $diffDate = $diff->format("%R%a");

            $bucket_status = 'Open';
            $update_datetime = '';

            if($value['status'] == '13' || $value['status'] == '14'){
                $update_datetime = $value['imei_return_date'];
                if($update_datetime || !$value['return_date']){
                    $bucket_status = 'Closed';
                }
            }

            $text_diffDate = '';

            if($diffDate < 0 && $bucket_status == 'Open'){
                $text_diffDate = '! (' . $diffDate . ' Day)';
            }

            $row = array();

            $row[] = $i++;
            $row[] = $value['imei'];
            $row[] = $value['good_model'];
            $row[] = $value['good_name'];
            $row[] = $value['good_color'];
            $row[] = $value['rq'];

            switch ($value['product_grade']) {
                case '1':
                $row[] = 'A';
                break;
                case '2':
                $row[] = 'B';
                break;
                case '3':
                $row[] = 'Demo';
                break;
                case '4':
                $row[] = 'APK';
                break;
                case '5':
                $row[] = 'Prototype';
                break;
                default:
                $row[] = '';
                break;
            }

            $row[] = $value['hrs_department_name'];
            $row[] = $value['code'];
            $row[] = $value['fullname'];
            $row[] = $value['position_name'];
            $row[] = $value['name'];
            $row[] = $value['created_date'];
            $row[] = $value['first_sn_ref'];
            $row[] = $value['completed_date'];
            $row[] = $value['org_full_name'];
            $row[] = $value['event_program_name'];
            $row[] = $value['event_program_start_period_date'];
            $row[] = $value['return_date'] ? $value['return_date'] : 'No Return';
            $row[] = $update_datetime;
            $row[] = $value['co_return'];
            $row[] = $bucket_status;
            $row[] = $text_diffDate;
            $row[] = $value['remark'];
            
            fputcsv($output, $row);
            unset($row);
        }
        
        unset($data);
        // die;
    }

    public function requestCancelAction(){ ################

        $flashMessenger = $this->_helper->flashMessenger;

        $id = $this->getRequest()->getParam('id');
        $remark = $this->getRequest()->getParam('remark');

        if(!$id){
           echo json_encode(['status' => 400, 'message' => 'Can not cancel, Invalid Data.']);
           exit();
       }

       if ($this->getRequest()->getMethod() == 'POST') {

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $QBL = new Application_Model_BorrowingList();

        try{
            $db = Zend_Registry::get('db');
            $db->beginTransaction();

                //status 12 is cancel by wms
            $data = array(
                'read_data' => 1,
                'update_datetime' => date('Y-m-d H:i:s'),
                'status' => 12,
                'wms_status' => 2,
                'wms_datetime' => date('Y-m-d H:i:s'),
                'wms_by' => $userStorage->id,
                'wms_remark' => $remark
            );

            $where_update = $QBL->getAdapter()->quoteInto('id = ?', $id);
            $status_update_12 = $QBL->update($data,$where_update);

            $db->commit();

            if(isset($status_update_12) && $status_update_12){
                $data_curl_12 = array('code' => $getDetailsBorrowingByID['code'], 'status' => 12, 'rq' => $getDetailsBorrowingByID['rq']);

                $handle = curl_init(API_IOPPO_URL . 'warehouse-notification');
                curl_setopt($handle, CURLOPT_POST, true);
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data_curl_12);
                curl_exec($handle);
                curl_close($handle);
            }

            $flashMessenger->setNamespace('success')->addMessage('Done!');

            echo json_encode(['status' => 200, 'message' => 'Done.']);
            exit();

        } catch (Exception $e){
            $db->rollback();

            echo json_encode(['status' => 400, 'message' => 'Can not cancel : '. $e->getMessage()]);
            exit();
        }
    }

}

    public function borrowCheckingAction(){ ##############

        $flashMessenger = $this->_helper->flashMessenger;

        $id = $this->getRequest()->getParam('id');

        $checkbox_imei_missing = $this->getRequest()->getParam('checkbox_imei_missing');

        $imei = $this->getRequest()->getParam('imei');
        $imei_missing = $this->getRequest()->getParam('imei_missing');
        // $type = $this->getRequest()->getParam('type');

        // if(!$id || !$type){
        if(!$id){
           echo json_encode(['status' => 400, 'message' => 'Can not check, Invalid Data.']);
           exit();
       }

       if ($this->getRequest()->getMethod() == 'POST') {

        $QBL = new Application_Model_BorrowingList();

        if($checkbox_imei_missing){

            if(!$imei_missing){
                echo json_encode(['status' => 400, 'message' => 'Can not check, Please input imei missing.']);
                exit();
            }

            $arrImei = array_merge(explode(PHP_EOL, $imei),explode(PHP_EOL, $imei_missing));
        }else{

            if(!$imei){
                echo json_encode(['status' => 400, 'message' => 'Can not check, Please input imei missing.']);
                exit();
            }

            $arrImei = explode(PHP_EOL, $imei);
        }

        $arrImei = array_filter($arrImei);

        $duplicates = array_values(array_unique(array_diff_assoc($arrImei, array_unique($arrImei))));

        if($duplicates){
            echo json_encode(['status' => 401, 'diffSystem' => ['Duplicates IMEI'], 'diffImei' => $duplicates]);
            exit();
        }

        $getDetailsBorrwing = $QBL->getDetailsBorrwing($id);

        if(!$getDetailsBorrwing){
            echo json_encode(['status' => 401, 'diffSystem' => ['Not find borrowing'], 'diffImei' => []]);
            exit();
        }

        $getImei = $QBL->getImeiByID($id);

        if(!$getImei){
            echo json_encode(['status' => 401, 'diffSystem' => ['Not find IMEI'], 'diffImei' => []]);
            exit();
        }

        if($getImei[0]['status'] != '13'){
            echo json_encode(['status' => 401, 'diffSystem' => ['Wrong step'], 'diffImei' => []]);
            exit();
        }

        $getDetailsUser = $QBL->getDetailsUser($getDetailsBorrwing['code']);

        if(!$getDetailsUser){
            echo json_encode(['status' => 401, 'diffSystem' => ['Not find user request'], 'diffImei' => []]);
            exit();
        }

        $arrImeiSystem = [];

            //Service คืน service แบบ imei ไม่ใช่ตัวที่ยืมไป
            // if($getDetailsUser['hrs_department_id'] == 21){
        if(false){

            foreach ($getImei as $key) {
                array_push($arrImeiSystem, $key['imei_sn']);
            }

            $QImei = new Application_Model_Imei();
            $getImeiDB = $QImei->checkImeiForReturnBorrowing($arrImei);

            $diffSwap = array_diff($arrImei, $getImeiDB);
            $diffSystem = array_diff($arrImeiSystem, $arrImei);
            $diffImei = array_diff($arrImei, $arrImeiSystem);

                // print_r($diffSwap);//อันที่ผิดสลับไม่ได้
                // print_r($diffSystem);//อันที่ขาด
                // print_r($diffImei);//อันที่ปิด
                // print_r($getImeiDB);//อันที่สลับได้
                // die;

            if(!$diffSystem && !$diffImei){
                echo json_encode(['status' => 200, 'arrImeiSystem' => $arrImeiSystem]);
                exit();
            }else if(false){

            }else{

                $haveImei = $arrImei;
                $haveImeiSwap = $arrImei;

                $temp_diffSwap = $diffSwap;
                $temp_diffSystem = $diffSystem;
                $temp_diffImei = $diffImei;

                foreach ($temp_diffSystem as $key) {
                    if (($key = array_search($key, $haveImei)) !== false) {
                        unset($haveImei[$key]);
                    }
                }

                foreach ($temp_diffImei as $key) {
                    if (($key = array_search($key, $haveImei)) !== false) {
                        unset($haveImei[$key]);
                    }
                }

                $haveImei = array_values($haveImei);

                foreach ($temp_diffSwap as $key) {
                    if (($key = array_search($key, $haveImeiSwap)) !== false) {
                        unset($haveImeiSwap[$key]);
                    }
                }

                $haveImeiSwap = array_values($haveImeiSwap);

                print_r($haveImei);
                echo 'okok';
                print_r($haveImeiSwap);die;


                if(count($getImei) > count($arrImei)){

                    echo json_encode(['status' => 401, 'diffSystem' => ['Imei ยังไม่ครบ'], 'diffImei' => $diffSystem]);
                    exit();
                }

                if (count($getImei) < count($arrImei)){

                    echo json_encode(['status' => 401, 'diffSystem' => ['Imei เกินจำนวน'], 'diffImei' => $diffImei]);
                    exit();
                }


            }

            print_r($haveImei);
            echo 'okok';
            print_r($haveImeiSwap);die;

            print_r($getImei);
            echo 'okok';
            print_r($getImeiDB);die;

            if(count($getImei) > count($getImeiDB)){

                echo json_encode(['status' => 401, 'diffSystem' => ['Imei ยังไม่ครบ'], 'diffImei' => $diffSwap]);
                exit();
            }

            if (count($getImei) < count($getImeiDB)){

                echo json_encode(['status' => 401, 'diffSystem' => ['Imei เกินจำนวน'], 'diffImei' => $diffSwap]);
                exit();
            }

            if($diffSwap){
                echo json_encode(['status' => 401, 'diffSystem' => [], 'diffImei' => $diffSwap]);
                exit();
            }

            echo json_encode(['status' => 200, 'arrImeiSystem' => []]);
            exit();

        }else{
            foreach ($getImei as $key) {
                array_push($arrImeiSystem, $key['imei_sn']);
            }
            
            $diffSystem = array_diff($arrImeiSystem, $arrImei);
            $diffImei = array_diff($arrImei, $arrImeiSystem);

            if(!$diffSystem && !$diffImei){
                echo json_encode(['status' => 200, 'arrImeiSystem' => $arrImeiSystem]);
                exit();
            }

            echo json_encode(['status' => 401, 'diffSystem' => $diffSystem, 'diffImei' => $diffImei]);
            exit();
        }
    }

}

    public function syncCheckWarehouseLineAction(){ ############

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $type = $this->getRequest()->getParam('type');
        $warehouse_id = $this->getRequest()->getParam('warehouse_id');
        $good_id = $this->getRequest()->getParam('good_id');
        $good_color_id = $this->getRequest()->getParam('good_color_id');

        if ($this->getRequest()->getMethod() == 'POST') {

            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            try {

                if($type == 'ALL'){

                    if(!$warehouse_id){
                        echo json_encode(['status' => 400, 'message' => 'Invalid Data!,Please Refresh page.']);
                        exit();
                    }

                    $QCWL = new Application_Model_CheckWarehouseLine();

                    $getLineScannedDetails = $QCWL->getLineScannedDetails($warehouse_id);

                    $arraySync = array();

                    foreach ($getLineScannedDetails as $key) {
                        $arrayDataSync = [
                            'warehouse_id' => $key['warehouse_id'],
                            'good_id' => $key['good_id'],
                            'good_color_id' => $key['good_color_id']
                        ];

                        array_push($arraySync, $arrayDataSync);
                    }

                    $status = $this->syncStorage($arraySync);

                    if($status){

                        $db->commit();

                        echo json_encode(['status' => 200, 'message' => 'Sync Done.']);
                        exit();
                    }else{
                        echo json_encode(['status' => 400, 'message' => 'Can not sync,Please Refresh page.']);
                        exit();
                    }

                }else{

                    if(!$warehouse_id || !$good_id || !$good_color_id){
                        echo json_encode(['status' => 400, 'message' => 'Invalid Data!,Please Refresh page.']);
                        exit();
                    }

                    $status = $this->syncStorage([['warehouse_id' => $warehouse_id, 'good_id' => $good_id, 'good_color_id' => $good_color_id]]);

                    if($status){

                        $db->commit();

                        echo json_encode(['status' => 200, 'message' => 'Sync Done.']);
                        exit();
                    }else{
                        echo json_encode(['status' => 400, 'message' => 'Can not sync,Please Refresh page.']);
                        exit();
                    }

                }

            } catch (Exception $e) {
                $db->rollBack();
            }

        }

    }

    public function syncStorage($arrayData,$force = null){ #############3

        $QCWL = new Application_Model_CheckWarehouseLine();
        $QCSW = new Application_Model_CheckStockWarehouse();

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $timeNow = date('Y-m-d H:i:s');

        try {

            foreach ($arrayData as $key) {

                $where = [];
                $where[] = $QCSW->getAdapter()->quoteInto('warehouse_id = ?', $key['warehouse_id']);
                $where[] = $QCSW->getAdapter()->quoteInto('good_id = ?', $key['good_id']);
                $where[] = $QCSW->getAdapter()->quoteInto('good_color_id = ?', $key['good_color_id']);
                $where[] = $QCSW->getAdapter()->quoteInto('status = ?', 1);

                $checkData = $QCSW->fetchRow($where);

                if($checkData && $force){
                    echo 'okok';die;
                    continue;
                }

                $updateStatus = $QCSW->update(['status' => 0], $where);

                $getTotalStorageAndChanging = $QCWL->getTotalStorage($key['warehouse_id'],$key['good_id'],$key['good_color_id'],true);

                $total_storage = 0;
                $total_changing = 0;

                if(isset($getTotalStorageAndChanging['total_storage']) && $getTotalStorageAndChanging['total_storage']){
                    $total_storage = $getTotalStorageAndChanging['total_storage'];
                }

                if(isset($getTotalStorageAndChanging['total_changing']) && $getTotalStorageAndChanging['total_changing']){
                    $total_changing = $getTotalStorageAndChanging['total_changing'];
                }

                $arrayData = array(
                    'warehouse_id' => $key['warehouse_id'],
                    'good_id' => $key['good_id'],
                    'good_color_id' => $key['good_color_id'],
                    'storage' => $total_storage,
                    'on_changing' => $total_changing,
                    'created_date' => $timeNow,
                    'created_by' => $userStorage->id,
                    'status' => 1
                );

                if(!$QCSW->insert($arrayData)){
                    return false;
                }

            }

            return true;

        } catch (Exception $e) {
            return false;
        }

    }
    
    public function ajaxgetimgborrowingAction() ##############3
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $rq = $this->getRequest()->getParam('rq');

        $QBL = new Application_Model_BorrowingList();
        $get_img_borrowing = $QBL->getImgBorrowing($rq);

        $arr_part_img = [];
        $part_url_img = API_IOPPO_URL . 'fileupload/' . $rq . '/';

        foreach ($get_img_borrowing as $key) {
            $part_img = $part_url_img . $key['image_name'];
            array_push($arr_part_img, $part_img);
        }

        echo json_encode(['img_borrowing' => $arr_part_img]);
        exit();

    }

    public function delFactoryClaimAction(){ ##############

        $flashMessenger = $this->_helper->flashMessenger;

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->getMethod() == 'POST') {

            $id = $this->getRequest()->getParam('id');
            $remark = $this->getRequest()->getParam('remark');

            if(!$id || !$remark){
                echo json_encode(['status' => 400,'message' => 'invalid data']);
                exit();
            }

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            $QFC = new Application_Model_FactoryClaim();

            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            try {

                $update = array(
                    'status' => 0,
                    'del_remark' => $remark,
                    'del_by' => $userStorage->id,
                    'del_date' => date('Y-m-d H:i:s')
                );

                $where_update = $QFC->getAdapter()->quoteInto('factory_claim_id = ?', $id);
                $status_update = $QFC->update($update,$where_update);

                if(!$status_update){

                    $db->rollBack();

                    echo json_encode(['status' => 400,'message' => 'can not delete']);
                    exit();
                }

                $db->commit();

                $flashMessenger->setNamespace('success')->addMessage('Done!');

                echo json_encode(['status' => 200,'message' => 'Done']);
                exit();

            } catch (Exception $e) {
                $db->rollBack();
            }

        }else{
            echo json_encode(['status' => 400,'message' => 'invalid data']);
            exit();
        }

    }

    public function cancelFactoryClaimAction(){ ###############

        $flashMessenger = $this->_helper->flashMessenger;

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->getMethod() == 'POST') {

            $id = $this->getRequest()->getParam('id');
            $remark = $this->getRequest()->getParam('remark');

            if(!$id || !$remark){
                echo json_encode(['status' => 400,'message' => 'invalid data']);
                exit();
            }

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            $QFC = new Application_Model_FactoryClaim();

            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            try {

                $update = array(
                    'status' => 3,
                    'del_remark' => $remark,
                    'del_by' => $userStorage->id,
                    'del_date' => date('Y-m-d H:i:s')
                );

                $where_update = $QFC->getAdapter()->quoteInto('factory_claim_id = ?', $id);
                $status_update = $QFC->update($update,$where_update);

                if(!$status_update){

                    $db->rollBack();

                    echo json_encode(['status' => 400,'message' => 'can not delete']);
                    exit();
                }

                $db->commit();

                $flashMessenger->setNamespace('success')->addMessage('Done!');

                echo json_encode(['status' => 200,'message' => 'Done']);
                exit();

            } catch (Exception $e) {
                $db->rollBack();
            }

        }else{
            echo json_encode(['status' => 400,'message' => 'invalid data']);
            exit();
        }

    }

    function ajaxCheckImeiDetailsHaveSaleOutAction(){ ################

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $imei = $this->getRequest()->getParam('imei');

        if(!$imei){
            echo json_encode(['status' => 400,'message' => 'No IMEI','valid' => [], 'invalid' => []]);
            exit();
        }

        $imei_list = trim($imei);
        $imei_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $imei_list);
        $imei_list = explode("\n", $imei_list);
        $imei_list = array_filter($imei_list);

        $check_imei_duplicate = array_unique($imei_list);

        if(count($imei_list) != count($check_imei_duplicate)){

            $array_imei_duplicate = array();

            foreach ($imei_list as $key => $value) {
                if(!isset($check_imei_duplicate[$key])){
                    array_push($array_imei_duplicate, ['imei' => $value,'good_model' => '-', 'good_name' => '-', 'good_color' => '-']);
                }
            }

            echo json_encode(['status' => 400,'message' => 'Duplicate IMEI','valid' => [], 'invalid' => $array_imei_duplicate]);
            exit();
        }

        $QI = new Application_Model_Imei();

        $check_imei_list = $QI->checkImeiSoldAndNotiming($imei_list);

        $array_invalid = array();
        $array_valid = array();

        foreach ($imei_list as $key => $value) {
            $count_valid = 0;
            foreach ($check_imei_list as $key_sub => $value_sub) {

                if($value == $value_sub['imei_sn']){
                    array_push($array_valid, ['imei' => $value_sub['imei_sn'],'good_model' => $value_sub['good_model'],'good_name' => $value_sub['good_name'],'good_color' => $value_sub['good_color']]);

                    $count_valid++;
                }

            }
            if(!$count_valid){
                array_push($array_invalid, ['imei' => $value,'good_model' => '-', 'good_name' => '-', 'good_color' => '-']);
            }
        }

        if(count($imei_list) != count($check_imei_list)){
            echo json_encode(['status' => 400,'message' => 'Invalid Data','valid' => $array_valid, 'invalid' => $array_invalid]);
            exit();
        }

        echo json_encode(['status' => 200,'message' => 'Done','valid' => $array_valid]);
        exit();
    }

    public function getColorByImeiAction(){ #########3

        $old_imei = $this->getRequest()->getParam('old_imei');

        if(!$old_imei){
           echo json_encode(array());
           exit();
       }

       $old_imei = explode("\n", $old_imei);

       $old_imei = array_values(array_unique($old_imei));

       $QI = new Application_Model_Imei();
       $QGood = new Application_Model_Good();
       $QGoodColor = new Application_Model_GoodColor();

       $get_good_id = $QI->checkImeiInSystem($old_imei);

       $array_good_id = array();

       foreach ($get_good_id as $key) {
        array_push($array_good_id, $key['good_id']);
    }

    $array_good_id = array_values(array_unique($array_good_id));

    if ($array_good_id){
        $where = $QGood->getAdapter()->quoteInto('id IN (?)', $array_good_id);
        $good_data = $QGood->fetchAll($where);

        $array_good_color = array();

        foreach ($good_data as $key) {
            
            if (isset($key['color']) and $key['color']){
                $getColor = array_filter(explode(',', $key->color));
                
                if ($getColor){
                    $array_good_color = array_merge($array_good_color,$getColor);
                }
            }

        }

        if($array_good_color){

            $array_good_color = array_values(array_unique($array_good_color));

            $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $array_good_color);

            $colors = $QGoodColor->fetchAll($where);
            echo json_encode($colors->toArray());
            exit();
        }

        echo json_encode(array());
        exit();

    } else {
        echo json_encode(array());
        exit();
    }
}

public function _exportReturnToSystem($data){
   $this->_helper->layout->disableLayout();
   $this->_helper->viewRenderer->setNoRender();
   set_time_limit(0);
   error_reporting(~E_ALL);
   ini_set('display_error', 0);
   ini_set('memory_limit', -1);
   $filename = 'Return Order List - '.date('d-m-Y H-i-s').'.csv';

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

        $heads = array(
            'No',
            'SALE ORDER NUMBER',
            'INVOICE NUMBER',
            'DISTRIBUTOR ID',
            'DISTRIBUTOR NAME',
            'ORDER TYPE',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'QUANTITY',
            'SALES PRICE',
            'TOTAL',
            'CREATE ORDERTIME',
            'CREATE BY',
            'WAREHOUSE'
        );
        fputcsv($output, $heads);

        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QMarket = new Application_Model_Market();
        $QDistributor = new Application_Model_Distributor();
        $QGoodCategory = new Application_Model_GoodCategory();
        $QWarehouse = new Application_Model_Warehouse();
        $Qstaff = new Application_Model_Staff();

        $staff = $Qstaff->get_cache();
        $goods = $QGood->get_cache();
        $goodColors = $QGoodColor->get_cache();
        $distributors = $QDistributor->get_cache();
        $good_categories = $QGoodCategory->get_cache();
        $warehouses_cached = $QWarehouse->get_cache();

        $markets = array();
        foreach ($data as $key => $m)
        {
            $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
            $markets[$m['sn']] = $QMarket->fetchAll($where);
        }

        $i = 1;
        $old_sn = '';
        foreach($data as $item) {
            if($item['sn'] == $old_sn){
                $count_row++;
            }else{
                $count_row = 0;
            }

            $old_sn = $item['sn'];
            $d_id = $item['d_id'];

            if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; }
            else { $temp_sn = $item['sn_ref']; }

            if (is_null($item['invoice_number']) || $item['invoice_number'] == ''){
                $temp_invoice = $item['invoice_number'];}
                else{
                    $temp_invoice = $item['invoice_number'];
                }

                if (is_null($item['d_id']) || $item['d_id'] == ''){
                    $temp_did = $item['d_id'];}
                    else{
                        $temp_did = $item['d_id'];
                    }

                    if (is_null($item['title']) || $item['title'] == ''){
                        $temp_title = $item['title'];}
                        else{
                            $temp_title = $item['title'];
                        }

                        if (is_null($good_categories[$item['cat_id']]) || $good_categories[$item['cat_id']] == ''){
                            $temp_cg = $good_categories[$item['cat_id']];}
                            else{
                                $temp_cg = $good_categories[$item['cat_id']];
                            }

                            if (is_null($staff[$item['pay_user']]) || $staff[$item['pay_user']] == '') {
                                $temp_user = $staff[$item['pay_user']];
                            }else{
                                $temp_user = $staff[$item['pay_user']];
                            }

                            if(is_null($staff[$item['outmysql_user']]) || $staff[$item['outmysql_user']] == ''){
                                $confirm_user = $staff[$item['outmysql_user']];}
                                else{
                                    $confirm_user = $staff[$item['outmysql_user']];
                                }


                                if (is_null($item['outmysql_time']) || $item['outmysql_time'] == ''){
                                    $temp_pay = $item['outmysql_time'];}
                                    else{
                                        $temp_pay = $item['outmysql_time'];
                                    }

                                    if (is_null($item['add_time']) || $item['add_time'] == ''){
                                        $temp_addt = $item['add_time'];}
                                        else{
                                            $temp_addt = $item['pay_time'];
                                        }

                                        if (is_null($warehouses_cached[$item['warehouse_id']]) || $warehouses_cached[$item['warehouse_id']] == ''){
                                            $temp_house = $warehouses_cached[$item['warehouse_id']];}
                                            else{
                                                $temp_house = $warehouses_cached[$item['warehouse_id']];
                                            }


                                            if (isset($distributors) && isset($distributors[$item['d_id']]))
                                                $distributor = $distributors[$item['d_id']];
                                            else
                                                $distributor = '';



                                            foreach ($markets[$item['sn']] as $key => $value)
                                            {
                                                if (isset($goods) && isset($goods[$value['good_id']]))
                                                    $good_name = $goods[$value['good_id']];
                                                if (isset($goodColors) && isset($goodColors[$value['good_color']]))
                                                    $good_color = $goodColors[$value['good_color']];

                                                $row[] = $i;
                                                $row[] = '="'.$temp_sn.'"';
                                                $row[] = $temp_invoice;
                                                $row[] = $temp_did;
                                                $row[] = $temp_title;
                                                $row[] = $temp_cg;
                                                $row[] = $good_name;
                                                $row[] = $good_color;
                                                $row[] = $value['num'];
                                                $row[] = My_Number::f(@$value['price'], 0, ',','.');
                                                $row[] = My_Number::f($value['total'], 0, ',','.');
                                                $row[] = $temp_addt;
                                                $row[] = $temp_user;
                                                $row[] = $temp_house ;

                                                fputcsv($output,$row);
                                                unset($item);
                                                unset($value);
                                                unset($row);
                                                $i++;
                                            }
                                        }
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

                                    public function _exportReturnToSystemImei($data){

                                        $this->_helper->layout->disableLayout();
                                        $this->_helper->viewRenderer->setNoRender();
                                        set_time_limit(0);
                                        error_reporting(~E_ALL);
                                        ini_set('display_error', 0);
                                        ini_set('memory_limit', -1);
                                        $filename = 'Return Order List IMEI - '.date('d-m-Y H-i-s').'.csv';

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

        $heads = array(
            'No',
            'SALE ORDER NUMBER',
            'INVOICE NUMBER',
            'DISTRIBUTOR ID',
            'DISTRIBUTOR NAME',
            'ORDER TYPE',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'QUANTITY',
            'SALES PRICE',
            'CREATE ORDERTIME',
            'CREATE BY',
            'WAREHOUSE',
            'IMEI',
        );
        fputcsv($output, $heads);

        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QMarket = new Application_Model_Market();
        $QDistributor = new Application_Model_Distributor();
        $QGoodCategory = new Application_Model_GoodCategory();
        $QWarehouse = new Application_Model_Warehouse();
        $Qstaff = new Application_Model_Staff();

        $staff = $Qstaff->get_cache();
        $goods = $QGood->get_cache();
        $goodColors = $QGoodColor->get_cache();
        $distributors = $QDistributor->get_cache();
        $good_categories = $QGoodCategory->get_cache();
        $warehouses_cached = $QWarehouse->get_cache();

        $markets = array();
        foreach ($data as $key => $m)
        {
            $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
            $markets[$m['sn']] = $QMarket->fetchAll($where);
        }

        $i = 1;
        $old_sn = '';
        foreach($data as $item) {
            if (isset($distributors) && isset($distributors[$item['d_id']]))
                $distributor = $distributors[$item['d_id']];
            else
                $distributor = '';

            if ($item['return_type']==1){
                $order_type="Defective";
            }else if ($item['return_type']==2){
                $order_type="Adjustment";
            }else if ($item['return_type']==3){
                $order_type="Demo";
            }else{
                $order_type="-";
            }

            $row = array();

            $confirmstatus = $item['outmysql_user'];
            if($confirmstatus == NULL){
                $confirmstatus = 'NO';
            }else{
                $confirmstatus ='YES';
            }
            if (isset($goods) && isset($goods[$item['good_id']]))
                $good_name = $goods[$item['good_id']];
            if (isset($goodColors) && isset($goodColors[$item['good_color']]))
                $good_color = $goodColors[$item['good_color']];

            if (isset($good_categories) && $good_categories[$item['cat_id']])
               $temp_cg = $good_categories[$item['cat_id']];

           if (isset($staff) && $staff[$item['pay_user']])
            $temp_user = $staff[$item['pay_user']];

        if(isset($staff) && $staff[$item['outmysql_user']])
           $confirm_user = $staff[$item['outmysql_user']];


       if (isset($warehouses_cached) && $warehouses_cached[$item['warehouse_id']])
        $temp_house = $warehouses_cached[$item['warehouse_id']];



    $row[] = $i;
    $row[] = $item['sn_ref'];
    $row[] = $item['invoice_number'];
    $row[] = $item['d_id'];
    $row[] = $item['title'];
    $row[] = $temp_cg;
    $row[] = $good_name;
    $row[] = $good_color;
    $row[] = '1';
    $row[] = My_Number::f(@$item['price'], 0, ',','.');
    $row[] = $item['add_time'];
    $row[] = $temp_user;
    $row[] = $temp_house;
    $row[] = $item['imei_sn'];
                // $row[] = $item['add_time'];

    fputcsv($output,$row);
    unset($item);
    unset($value);
    unset($row);
    $i++;
}
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


}