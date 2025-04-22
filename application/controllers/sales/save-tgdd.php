<?php
// config for template
define('START_ROW', 2);
define('PO_CODE_COL', 0);
define('STORE_CODE_COL', 3);
define('ADDRESS_COL', 5);
define('PRODUCT_ID_COL', 7);
define('QUANTITY_COL', 9);
define('FINANCE_TYPE', 11);
define('SALE_OFF_COL', 12);
define('WAREHOUSE_COL', 13);

$this->_helper->layout->disableLayout();

if ( $this->getRequest()->getMethod() == 'POST' ) { // Big IF
    set_time_limit(0);
    ini_set('memory_limit', -1);
    // file_put_contents(APPLICATION_PATH.'/../public/files/mou/lock', '1');
    $progress = new My_File_Progress('parent.set_progress');
    $progress->flush(0);

    $save_folder   = 'mou';
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
        // My_File_Progress::set('mou', 0);
    } catch (Exception $e) {
        $this->view->errors = $e->getMessage();
        return;
    }

    $distributor_po = $this->getRequest()->getParam('distributor_po');

    if (!$distributor_po) {
        $this->view->errors = "Choose PO";
        return;
    }

    //read file
    include 'PHPExcel/IOFactory.php';

    //  Read your Excel workbook
    try {
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $path_info = pathinfo($inputFileName);
        $extension = $path_info['extension'];
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);
    } catch (Exception $e) {
        $this->view->errors = $e->getMessage();
        return;
    }

    //  Get worksheet dimensions
    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    // action
    $tags = $this->getRequest()->getParam('tags');
    $type = $this->getRequest()->getParam('type');
    $payment_method = $this->getRequest()->getParam('payment_method');

    /**
     * Danh sách các đơn hàng lỗi
     * @var array
     */
    $error_list      = array();
    $success_list    = array();
    $store_code_list = array();
    $number_of_order = 0;
    $total_value     = 0;
    $total_order_row = 0;
    $order_list      = array();

    // mapping products
    $QGoodMapping = new Application_Model_GoodMapping();
    $product_mapping = $QGoodMapping->get_cache();

    $QGood               = new Application_Model_Good();
    $good_list           = $QGood->get_cache();

    $QGoodColor          = new Application_Model_GoodColor();
    $good_color_list     = $QGoodColor->get_cache();

    $QDistributorMapping = new Application_Model_DistributorMapping();
    $distributor_mapping = $QDistributorMapping->get_cache();

    $QDistributor        = new Application_Model_Distributor();
    $distributor_warehouse_cache = $QDistributor->get_warehouse_cache();

    $QMarketMassuploadLog = new Application_Model_MarketMassUploadLog();

    $QWarehouse = new Application_Model_Warehouse();
    $warehouse_cache = $QWarehouse->get_cache();

    $warehouse = $this->getRequest()->getParam('warehouse', 0);

    $order_rows = $highestRow - START_ROW;
    $_failed = false;

    for ($row = START_ROW; $row <= $highestRow; $row++) {
        $percent = round($row * 100 / $order_rows, 1);
        $progress->flush($percent);

        try {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL, TRUE, FALSE);

            // validate data
            $rowData = isset($rowData[0]) ? $rowData[0] : array();

            $rowData[STORE_CODE_COL] = trim($rowData[STORE_CODE_COL]);

            if (empty($rowData[STORE_CODE_COL])) throw new Exception("Empty store code, row: ".$row);

            if (!isset($distributor_mapping[$rowData[STORE_CODE_COL]]['distributor_id']) || !$distributor_mapping[$rowData[STORE_CODE_COL]]['distributor_id']) {
                throw new Exception("Invalid store code: " .$rowData[STORE_CODE_COL]);
            }

            $distributor_id = $distributor_mapping[$rowData[STORE_CODE_COL]]['distributor_id'];

            $use_warehouse_in_db = false;
            // get warehouse
            if (isset($warehouse) && $warehouse > 0) {
                $warehouse_id = $warehouse;
            } else {
                // get Finance type
                $rowData[WAREHOUSE_COL] = isset($rowData[WAREHOUSE_COL]) ? trim($rowData[WAREHOUSE_COL]) : "";

                if (empty($rowData[WAREHOUSE_COL])) {
                    if (!isset($distributor_warehouse_cache[ $distributor_id ]) || !$distributor_warehouse_cache[ $distributor_id ])
                        throw new Exception("No warehouse selected: ".$rowData[STORE_CODE_COL]);

                    $warehouse_id = $distributor_warehouse_cache[ $distributor_id ];
                    $use_warehouse_in_db = true;
                } elseif (intval($rowData[WAREHOUSE_COL])) {
                    $warehouse_id = intval($rowData[WAREHOUSE_COL]);
                } else {
                    throw new Exception("Invalid Warehouse ID: ", $rowData[WAREHOUSE_COL]);
                }
            }

            // tạo order mới (khởi tạo array mới)
            // khi một trong hai điều sau xảy ra
            $new_order = !isset($last_distributor_id) || $last_distributor_id != $distributor_id // distributor_id thay đổi
                || !isset($last_warehouse_id) || $last_warehouse_id != $warehouse_id; // warehouse thay đổi

            // cùng 1 đơn mà món hàng trước (dòng trước) failed thì next tới khi qua đơn mới
            if (!$new_order && $_failed)
                continue;

            if ($new_order) {
                $ids              = array();
                $cat_ids          = array();
                $good_ids         = array();
                $good_colors      = array();
                $nums             = array();
                $prices           = array();
                $totals           = array();
                $texts            = array();
                $sale_off_percent = array();
                $is_sales_price   = 1;
                $distributor_id   = intval($distributor_id);
                $life_time        = 2;
                $sn               = null;
                $is_return        = null;
                $_failed = false;
            }

            $last_distributor_id = $distributor_id;
            $last_warehouse_id = $warehouse_id;

            // get product id and color id
            $rowData[PRODUCT_ID_COL] = trim($rowData[PRODUCT_ID_COL]);

            if ( !isset( $product_mapping [ $rowData[PRODUCT_ID_COL] ]['good_id'] ) || !$product_mapping [ $rowData[PRODUCT_ID_COL] ]['good_id'] ) {
                throw new Exception("Invalid product code/name: ".$rowData[PRODUCT_ID_COL]);
            }

            $product_id = $product_mapping [ $rowData[PRODUCT_ID_COL] ]['good_id'];

            if ( !isset( $product_mapping [ $rowData[PRODUCT_ID_COL] ]['color_id'] ) || !$product_mapping [ $rowData[PRODUCT_ID_COL] ]['color_id'] )
                throw new Exception("Invalid product code/color: ".$rowData[PRODUCT_ID_COL]);

            $product_color = $product_mapping [ $rowData[PRODUCT_ID_COL] ]['color_id'];
            //

            if (empty($rowData[QUANTITY_COL]) || !$rowData[QUANTITY_COL]) throw new Exception("Empty quantity: ".$rowData[STORE_CODE_COL]);

            // get Finance type
            $rowData[FINANCE_TYPE] = isset($rowData[FINANCE_TYPE]) ? trim($rowData[FINANCE_TYPE]) : "";
            $rowData[FINANCE_TYPE] = !empty($rowData[FINANCE_TYPE]) ? $rowData[FINANCE_TYPE] : "DEBIT";

            if (! defined("My_Finance_Type::".$rowData[FINANCE_TYPE]) )
                throw new Exception("Invalid Finance Type: ".$rowData[FINANCE_TYPE]);

            $rank = constant("My_Finance_Type::".$rowData[FINANCE_TYPE]);

            $where = $QGood->getAdapter()->quoteInto('id = ?', $product_id);
            $good_check = $QGood->fetchRow($where);
            if (!$good_check) throw new Exception("Invalid product ID: ". $product_id);
            if (!isset($good_check['cat_id'])) throw new Exception("Invalid Category ID");
            $cat_id = $good_check['cat_id'];

            $price_tmp = $QGood->get_price(
                $rowData[QUANTITY_COL], $product_id,
                $product_color, $cat_id, $distributor_id,
                $warehouse_id, 1, null, $type, null, $rank);

            if (!isset($good_list[ $product_id ]))
                throw new Exception("Invalid product ID: " . $good_list[ $product_id ]);

            if (!isset($good_color_list[ $product_color ]))
                throw new Exception("Invalid product color: " . $good_color_list[ $product_color ]);

            $tmp_good_name = $good_list[ $product_id ];
            $tmp_good_color = $good_color_list[ $product_color ];

            if ( $price_tmp['code'] != 1 )
                throw new Exception($price_tmp['message']   . (
                                        isset($price_tmp['quantity'])
                                        ? (
                                            ' - Current quantity: '.$price_tmp['quantity'] . ' / ' . $tmp_good_name . ' - ' . $tmp_good_color
                                        ) : ''));
            // //
            $ids[]              = null;
            $cat_ids[]          = $cat_id;
            $good_ids[]         = $product_id;
            $good_colors[]      = $product_color;
            $nums[]             = $rowData[QUANTITY_COL];
            $prices[]           = $price_tmp['price'] / $rowData[QUANTITY_COL] ;
            $off                = isset($rowData[SALE_OFF_COL]) ? intval( trim($rowData[SALE_OFF_COL]) ) : 0;
            $sale_off_percent[] = $off;
            $totals[]           = $price_tmp['price'] * (100-$off) / 100;

            // check next distributor_id, if new id, then save order, else continue the loop
            $tmp_rowData = $sheet->rangeToArray('A' . ($row+1) . ':' . $highestColumn . ($row+1),
                NULL, TRUE, FALSE);

            // kiểm tra dữ liệu distributor và warehouse ở dòng kế tiếp xem có gộp đơn ko
            $tmp_rowData = isset($tmp_rowData[0]) ? $tmp_rowData[0] : array();
            $tmp_distributor_id = isset($distributor_mapping[$tmp_rowData[STORE_CODE_COL]]['distributor_id'])
                ? $distributor_mapping[$tmp_rowData[STORE_CODE_COL]]['distributor_id']
                : -1;

            $tmp_warehouse_id = -1;

            // get temporary warehouse
            $use_warehouse_in_file = (!isset($warehouse) || !$warehouse || $warehouse <= 0) && !$use_warehouse_in_db;
            if ($use_warehouse_in_file) {
                $tmp_rowData[WAREHOUSE_COL] = isset($tmp_rowData[WAREHOUSE_COL]) ? trim($tmp_rowData[WAREHOUSE_COL]) : "";

                if (!empty($rowData[WAREHOUSE_COL]) && intval($tmp_rowData[WAREHOUSE_COL]) > 0)
                    $tmp_warehouse_id = intval($tmp_rowData[WAREHOUSE_COL]);
            }

            $to_do_insert = false;

            if ($tmp_distributor_id != $distributor_id)
                $to_do_insert = true;

            if ($use_warehouse_in_file && $tmp_warehouse_id != $warehouse_id)
                $to_do_insert = true;

            if (!$to_do_insert) continue;

            $params = array(
                'ids'              => $ids,
                'cat_id'           => $cat_ids,
                'good_id'          => $good_ids,
                'good_color'       => $good_colors,
                'num'              => $nums,
                'price'            => $prices,
                'total'            => $totals,
                'text'             => '',
                'distributor_id'   => $distributor_id,
                'warehouse_id'     => $warehouse_id,
                'salesman'         => 0,
                'type'             => $type,
                'sale_off_percent' => $sale_off_percent,
                'sn'               => $sn,
                'life_time'        => $life_time,
                'isbatch'          => 1,
                'distributor_po'   => $distributor_po,
                'payment_method'   => $payment_method
            );

            $result = $this->saveAPI($params);

            if (isset($result['code']) && $result['code'] == 1) {
                $number_of_order++;

                try {
                    if (trim($rowData[PO_CODE_COL])) {
                        $QMarket = new Application_Model_Market();
                        $_data = array('invoice_note' => $rowData[PO_CODE_COL]);
                        $where = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                        $QMarket->update($_data, $where);
                    }
                } catch (Exception $e) {

                }

                // add tags
                $QTag = new Application_Model_Tag();
                $QTag->add($tags, $result['sn'], TAG_ORDER);

                $row_total_value = 0;
                $row_total_quantity = 0;

                foreach ($totals as $t_value) {
                    $row_total_value += intval($t_value);
                    $total_value += intval($t_value);
                }

                foreach ($nums as $t_value) {
                    $row_total_quantity += intval($t_value);
                }

                // ghi log đơn hàng nào thuộc file mass upload nào,
                // muốn xóa hết đơn tạo từ 1 file dễ hơn
                if (isset($result['id']) && $result['id']) {
                    $data = array(
                        'sales_id' => $result['id'],
                        'file_upload_log_id' => $file['log_id'],
                    );

                    @$QMarketMassuploadLog->insert($data);
                }

                $success_list[] = array(
                    'sn'       => $result['sn'],
                    'fpt_code' => $rowData[STORE_CODE_COL],
                    'fpt_name' => $rowData[ADDRESS_COL],
                    'quantity' => $row_total_quantity,
                    'value'    => $row_total_value,
                );

                $_failed = false;
            } else {
                throw new Exception(isset($result['message']) ? $result['message'] : 'Unknown error');
            }
        } catch (Exception $e) {
            $_failed = true;
        //    PC::debug($e, 'try-catch');
            $error_list[] = array_merge($rowData, array($e->getMessage()));
        }
        // nothing here
    } // END loop through order rows

    $QFileLog = new Application_Model_FileUploadLog();
    $data = array();

    // xuất file excel các order thành công
    if (is_array($success_list) && count($success_list) > 0) {
        $data['success_file_name'] = 'SUCCESS-'.md5(microtime(true) . uniqid('', true)) . '.'.$extension;
        // xuất excel @@
        //
        $objPHPExcel_out = new PHPExcel();
        $objPHPExcel_out->createSheet();
        $objWorksheet_out = $objPHPExcel_out->getActiveSheet();
        //
        $headers = array(
            'No.',
            'Order SN',
            "TGDD Code",
            'TGDD Store',
            'Quantity',
            'Total Value',
            );

        $sxx_list = array();
        $stt = 1;
        $objWorksheet_out->fromArray($headers, NULL, 'A1');
        $index = 2;
        foreach ($success_list as $key => $value) {
            $col = "A";
            $objWorksheet_out->setCellValue($col++.$index, $stt++);
            $objWorksheet_out->getCell($col++.$index)->setValueExplicit($value['sn'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objWorksheet_out->getCell($col++.$index)->setValueExplicit($value['fpt_code'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objWorksheet_out->setCellValue($col++.$index, $value['fpt_name']);
            $objWorksheet_out->getCell($col++.$index)->setValueExplicit($value['quantity'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objWorksheet_out->getCell($col++.$index)->setValueExplicit($value['value'], PHPExcel_Cell_DataType::TYPE_STRING);
            $index++;
        }

        $objWorksheet_out->fromArray($sxx_list, NULL, 'A2');

        try {
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

            $new_file_dir = $uploaded_dir . DIRECTORY_SEPARATOR . $data['success_file_name'];

            $objWriter->save($new_file_dir);

        } catch (Exception $e) {
            $this->view->errors = $e->getMessage();
            return;
        }
    }
    // END IF // xuất file excel các order thành công

    // xuất file excel các order lỗi
    if (is_array($error_list) && count($error_list) > 0) {
        $data['error_file_name'] = 'FAILED-'.md5(microtime(true) . uniqid('', true)) . '.'.$extension;
        // xuất excel @@
        //
        $objPHPExcel_out = new PHPExcel();
        $objPHPExcel_out->createSheet();
        $objWorksheet_out = $objPHPExcel_out->getActiveSheet();
        //
        // Title
        $index = '1';
        $row = array('DERID',
                    'ORDERDATE',
                    'INPUTTYPENAME',
                    'STORE ID',
                    'STORENAME',
                    'STOREADDRESS',
                    'PROVIDERPRODUCTCODE',
                    'PRODUCTID',
                    'PRODUCTNAME',
                    'QUANTITY',
                    'PRICE',
                    'REASON',
                    );
        $objWorksheet_out->fromArray($row, NULL, 'A'.$index++);

        // các dòng lỗi
        foreach ($error_list as $key => $row) {
            $objWorksheet_out->fromArray($row, NULL, 'A'.$index++);
        }
        //

        try {
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

            $objWriter->save($new_file_dir);
        } catch (Exception $e) {
            $this->view->errors = $e->getMessage();
            return;
        }
    }
    // END IF // xuất file excel các order lỗi

    if (count($data)) {
        try {
            $where = $QFileLog->getAdapter()->quoteInto('id = ?', $file['log_id']);
            $QFileLog->update($data, $where);

        } catch (Exception $e) {
            $this->view->errors = $e->getMessage();
            return;
        }
    }

    $this->view->error_list = $error_list;
    $this->view->number_of_order = $number_of_order;

    $this->view->success_file = isset($data['success_file_name']) ? (HOST
            . 'files'
            . DIRECTORY_SEPARATOR . 'mou'
            . DIRECTORY_SEPARATOR  . $userStorage->id
            . DIRECTORY_SEPARATOR . $uniqid
            . DIRECTORY_SEPARATOR . $data['success_file_name']):false;

    $this->view->error_file = isset($data['error_file_name']) ? (HOST
            . 'files'
        . DIRECTORY_SEPARATOR . 'mou'
            . DIRECTORY_SEPARATOR  . $userStorage->id
            . DIRECTORY_SEPARATOR . $uniqid
            . DIRECTORY_SEPARATOR . $data['error_file_name']):false;

    // unlink(APPLICATION_PATH.'/../public/files/mou/lock');
    $progress->flush(0);
} // END Big IF
