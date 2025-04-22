<?php
// config for template
define('PRODUCT_LIST_ROW', 2);
define('PRODUCT_LIST_COL_START', 2);
define('STORE_CODE_LIST_COL', 0);
define('STORE_CODE_LIST_ROW_START', 4);
define('SALE_OFF_ROW', 3);

$this->_helper->layout->disableLayout();

if ( $this->getRequest()->getMethod() != 'POST' ) { // Big IF

} else {
    set_time_limit(0);
    ini_set('memory_limit', -1);
    // file_put_contents(APPLICATION_PATH.'/../public/files/mou/lock', '1');
    $progress = new My_File_Progress('parent.set_progress');
    $progress->flush(0);

    $upload = new Zend_File_Transfer();

    $uniqid = uniqid('', true);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR .'public' . DIRECTORY_SEPARATOR . 'files'
            . DIRECTORY_SEPARATOR . 'mou'
            . DIRECTORY_SEPARATOR  . $userStorage->id
            . DIRECTORY_SEPARATOR . $uniqid;

    if (!is_dir($uploaded_dir))
        @mkdir($uploaded_dir, 0777, true);

    $upload->setDestination($uploaded_dir);

    $upload->setValidators(array(
        'Size'  => array('min' => 50, 'max' => 1000000),
        'Count' => array('min' => 1, 'max' => 1),
        'Extension' => array('xlsx', 'xls'),
    ));

    if (!$upload->isValid()){ // validate IF
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
            $upload->receive();

            $path_info = pathinfo($upload->getFileName());
            $filename = $path_info['filename'];
            $extension = $path_info['extension'];

            $old_name = $filename . '.'.$extension;
            $sub_str_file_name = md5($filename . uniqid('', true) . microtime(true));
            $new_name = 'UPLOAD-'. $sub_str_file_name . '.'.$extension;

            if (is_file($uploaded_dir . DIRECTORY_SEPARATOR . $old_name)){
                rename($uploaded_dir . DIRECTORY_SEPARATOR . $old_name, $uploaded_dir . DIRECTORY_SEPARATOR . $new_name);
            } else {
                $new_name = $old_name;
            }

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            $QFileLog = new Application_Model_FileUploadLog();
            $QMarketMassuploadLog = new Application_Model_MarketMassUploadLog();

            $data = array(
                'staff_id'       => $userStorage->id,
                'folder'         => $uniqid,
                'filename'       => $new_name,
                'type'           => 'mass order upload',
                'real_file_name' => $filename . '.'.$extension,
                'uploaded_at'    => time(),
            );

            $log_id = $QFileLog->insert($data);

            // action
            $tags = $this->getRequest()->getParam('tags');

            /**
             * Danh sách các đơn hàng lỗi
             * @var array
             */
            $error_list = array();
            $success_list = array();
            $product_list = array();
            $off_list = array();
            $store_code_list = array();
            $number_of_order = 0;
            $total_value = 0;
            $total_order_row = 0;

            // mapping products
            $QGoodMapping = new Application_Model_GoodMapping();
            $product_mapping = $QGoodMapping->get_cache();

            $QDistributorMapping = new Application_Model_DistributorMapping();
            $distributor_mapping = $QDistributorMapping->get_cache();

            $QGood = new Application_Model_Good();
            $good_list = $QGood->get_cache();
            $QGoodColor = new Application_Model_GoodColor();
            $good_color_list = $QGoodColor->get_cache();

            $QWarehouse = new Application_Model_Warehouse();
            $warehouse_cache = $QWarehouse->get_cache();

            $salesman       = $this->getRequest()->getParam('salesman');
            $type           = $this->getRequest()->getParam('type');
            $distributor_po = $this->getRequest()->getParam('distributor_po');
            $payment_method = $this->getRequest()->getParam('payment_method');

            if (!$distributor_po) {
                $this->view->error = "Choose PO";
                return;
            }
            // $sale_off        = $this->getRequest()->getParam('sale_off');

            require_once 'PHPExcel.php';
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array( 'memoryCacheSize' => '8MB');
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
            $total_order_row = $highestRow - STORE_CODE_LIST_ROW_START + 1;

            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

            // get product list
            for ($i = PRODUCT_LIST_COL_START;
                    ( $product_tmp = trim($objWorksheet
                        ->getCellByColumnAndRow($i, PRODUCT_LIST_ROW)
                        ->getValue())
                    ) != '';
                    $i++) {
                $product_list[] = $product_tmp;
            }

            // get sale off list
            for ($i = PRODUCT_LIST_COL_START;
                    ( $product_tmp = trim($objWorksheet
                        ->getCellByColumnAndRow($i, PRODUCT_LIST_ROW)
                        ->getValue())
                    ) != '';
                    $i++
                ) {
                $off_tmp  = trim($objWorksheet
                    ->getCellByColumnAndRow($i, SALE_OFF_ROW)
                    ->getValue()
                );

                $off_list[] = intval($off_tmp) > 0 ? intval($off_tmp) : 0;
            }

            $number_of_product = count($product_list);

            $warehouse = $this->getRequest()->getParam('warehouse');

            // get store code list
            // $i là dòng
            for ($i = STORE_CODE_LIST_ROW_START;
                    ( $store_code_tmp = trim($objWorksheet
                        ->getCellByColumnAndRow(STORE_CODE_LIST_COL, $i)
                        ->getValue())
                    ) != '';
                    $i++
                ) {
                $flag = true;
                $number_of_order++;
                $percent = round($number_of_order*100/$total_order_row, 1);
                $progress->flush($percent);

                $store_code_list[] = $store_code_tmp;

                if (!isset($distributor_mapping[$store_code_tmp]['distributor_id']) || !$distributor_mapping[$store_code_tmp]['distributor_id']) {
                    $error_list[] = array(
                        'reason' => 'Invalid Store code',
                        'row'    => $i,
                    );
                    $flag = false;
                    continue;
                }

                $distributor_id = $distributor_mapping[$store_code_tmp]['distributor_id'];

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
                $distributor_id   = $distributor_id;
                $life_time        = 2;
                $sn               = null;
                $is_return        = null;

                // get warehouse_id
                // nếu chọn warehouse từ combobox thì theo đó mà làm, không thì tự động
                //

                $warehouse_id = null;

                if ($warehouse && isset($warehouse_cache[ $warehouse ])) {
                    $warehouse_id = $warehouse;

                } else {
                    if (!isset($distributor_mapping[$store_code_tmp]['warehouse_id']) || !$distributor_mapping[$store_code_tmp]['warehouse_id']) {
                        $error_list[] = array(
                            'reason' => 'Invalid warehouse',
                            'row'    => $i,
                        );
                        $flag = false;
                        continue;
                    }

                    $warehouse_id = $distributor_mapping[$store_code_tmp]['warehouse_id'];
                }

                if (!$warehouse_id) {
                    $error_list[] = array(
                        'reason' => 'Invalid warehouse',
                        'row'    => $i,
                    );

                    $flag = false;
                    continue;
                }

                // loop through every product
                // $j là cột
                for ($j = PRODUCT_LIST_COL_START;
                        $j < PRODUCT_LIST_COL_START + $number_of_product;
                        $j++) {
                    $quantity = trim($objWorksheet
                        ->getCellByColumnAndRow($j, $i)
                        ->getValue());

                    if ($quantity == "" || $quantity == 0) {
                        // $flag = false; // do not mark false
                        continue; // check next product quantity
                    }

                    // product code:
                    // $product_list[$j - PRODUCT_LIST_COL_START]


                    if ( !isset( $product_mapping [ $product_list[$j - PRODUCT_LIST_COL_START] ]['good_id'] ) || !$product_mapping [ $product_list[$j - PRODUCT_LIST_COL_START] ]['good_id'] ) {
                        $error_list[] = array(
                            'reason' => ('Invalid Product code/product - ' . $product_list[$j - PRODUCT_LIST_COL_START]),
                            'row'    => $i,
                        );
                        $flag = false;
                        break;
                    }

                    $good_id_tmp    = $product_mapping [ $product_list[$j - PRODUCT_LIST_COL_START] ]['good_id'];

                    if ( !isset( $product_mapping [ $product_list[$j - PRODUCT_LIST_COL_START] ]['color_id'] ) || !$product_mapping [ $product_list[$j - PRODUCT_LIST_COL_START] ]['color_id'] ) {
                        $error_list[] = array(
                            'reason' => ('Invalid Product code/color - ' .$product_list[$j - PRODUCT_LIST_COL_START]),
                            'row'    => $i,
                        );
                        $flag = false;
                        break;
                    }

                    $good_color_tmp = $product_mapping [ $product_list[$j - PRODUCT_LIST_COL_START] ]['color_id'];

                    if ( !isset( $product_mapping [ $product_list[$j - PRODUCT_LIST_COL_START] ]['cat_id'] ) || !$product_mapping [ $product_list[$j - PRODUCT_LIST_COL_START] ]['cat_id'] ) {
                        $error_list[] = array(
                            'reason' => ('Invalid Product code/category - ' .$product_list[$j - PRODUCT_LIST_COL_START]),
                            'row'    => $i,
                        );
                        $flag = false;
                        break;
                    }

                    $cat_id = $product_mapping [ $product_list[$j - PRODUCT_LIST_COL_START] ]['cat_id'];

                    $price_tmp = $QGood->get_price($quantity, $good_id_tmp, $good_color_tmp, $cat_id, $distributor_id, $warehouse_id, $is_sales_price, $is_return, $type);

                    $tmp_good_name = $good_list[ $good_id_tmp ];
                    $tmp_good_color = $good_color_list[ $good_color_tmp ];

                    if ( $price_tmp['code'] != 1 ) {
                        $error_list[] = array(
                            'reason' => ($price_tmp['message']
                                . (isset($price_tmp['quantity'])
                                    ? (' - Current quantity: '.$price_tmp['quantity'] . ' / ' . $tmp_good_name . ' - ' . $tmp_good_color)
                                    : '')
                            ),
                            'row' => $i,
                        );
                        $flag = false;
                        break;
                    }

                    $ids[]              = null;
                    $cat_ids[]          = $cat_id;
                    $good_ids[]         = $good_id_tmp;
                    $good_colors[]      = $good_color_tmp;
                    $nums[]             = $quantity;
                    $prices[]           = $price_tmp['price'] / $quantity ;
                    $off                = isset($off_list[$j - PRODUCT_LIST_COL_START]) ? $off_list[$j - PRODUCT_LIST_COL_START] : 0;
                    $sale_off_percent[] = $off;
                    $totals[]           = $price_tmp['price'] * (100-$off) / 100;
                    $texts[]            = is_array($tags) ? implode(', ',  $tags) : '';
                } // END inner FOR loop

                if (!$flag) continue;

                $params = array(
                    'ids'              => $ids,
                    'cat_id'           => $cat_ids,
                    'good_id'          => $good_ids,
                    'good_color'       => $good_colors,
                    'num'              => $nums,
                    'price'            => $prices,
                    'total'            => $totals,
                    'text'             => $texts,
                    'distributor_id'   => $distributor_id,
                    'warehouse_id'     => $warehouse_id,
                    'salesman'         => $salesman,
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
                    // add tags
                    $QTag = new Application_Model_Tag();
                    $QTag->add($tags, $result['sn'], TAG_ORDER);

                    $row_total_value = 0;
                    $row_total_quantity = 0;

                    foreach ($totals as $t_value) {
                        $row_total_value += intval($t_value);
                        $total_value += intval($t_value);
                    }

                    foreach ($nums as $t_value)
                        $row_total_quantity += intval($t_value);

                    // ghi log đơn hàng nào thuộc file mass upload nào,
                    // muốn xóa hết đơn tạo từ 1 file dễ hơn
                    if (isset($result['id']) && $result['id']) {
                        $data = array(
                            'sales_id' => $result['id'],
                            'file_upload_log_id' => $log_id,
                        );

                        @$QMarketMassuploadLog->insert($data);
                    }

                    $success_list[] = array(
                        'sn' => $result['sn'],
                        'fpt_code' => $store_code_tmp,
                        'fpt_name' => trim($objWorksheet
                            ->getCellByColumnAndRow(STORE_CODE_LIST_COL+1, $i)
                            ->getValue()
                        ),
                        'quantity' => $row_total_quantity,
                        'value' => $row_total_value,
                    );
                } else {
                    $error_list[] = array(
                        'reason' => (isset($result['message']) ? $result['message'] : 'Unknown Error'),
                        'row'    => $i,
                    );
                }
            } // END outter FOR loop

            $data = array(
                'total'   => $number_of_order,
                'failed'  => count($error_list),
                'succeed' => $number_of_order - count($error_list),
                'value'   => $total_value,
            );

            // xuất file excel các order lỗi
            if (is_array($error_list) && count($error_list) > 0) {
                $data['error_file_name'] = 'FAILED-'.md5(microtime(true) . uniqid('', true)) . '.'.$extension;
                // xuất excel @@
                //
                $objPHPExcel_out = new PHPExcel();
                $objPHPExcel_out->createSheet();
                $objWorksheet_out = $objPHPExcel_out->getActiveSheet();
                //
                // get product list
                $objWorksheet = $objPHPExcel->getActiveSheet();
                $i = 0;
                $index = 'A';
                foreach ($objWorksheet->getRowIterator() as $row) {
                    if ($i++ > PRODUCT_LIST_ROW) break;

                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $j = 0;
                    foreach ($cellIterator as $cell) {
                        if ($i == 2)
                            $objWorksheet_out
                                ->getCellByColumnAndRow($j++, $i)
                                ->setValueExplicit($cell->getValue(),
                                            PHPExcel_Cell_DataType::TYPE_STRING);
                        else
                            $objWorksheet_out
                                ->setCellValueByColumnAndRow($j++, $i,
                                                                $cell->getValue());
                    }
                    $index++;
                }
                // $objWorksheet_out->getStyle('A1:'.$index.$j)
                //                 ->getNumberFormat()
                //                 ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                // $i++;
                // các dòng lỗi
                $objWorksheet = $objPHPExcel->getActiveSheet();
                foreach ($error_list as $key => $row) {
                    for ($j=0; $j <= $highestColumnIndex; $j++) {
                        $objWorksheet_out->setCellValueByColumnAndRow(
                            $j, $i, $objWorksheet->getCellByColumnAndRow($j, $row['row'])->getValue()
                            );
                    }

                    $objWorksheet_out->setCellValueByColumnAndRow($j, $i, $row['reason']);
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

                $objWriter->save($new_file_dir);
            }
            // END IF // xuất file excel các order lỗi

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
                    "FPT Code",
                    'FPT Store',
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
            }
            // END IF // xuất file excel các order thành công

            $where = $QFileLog->getAdapter()->quoteInto('id = ?', $log_id);
            $QFileLog->update($data, $where);

            $this->view->error_list = $error_list;
            $this->view->objWorksheet = $objWorksheet;
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

        } catch (Zend_File_Transfer_Exception $e) {
            $this->view->error =  $e->getMessage();
        } catch (Exception $e) {
            $this->view->error =  $e->getMessage();
        }
    } // END validate IF
    // unlink(APPLICATION_PATH.'/../public/files/mou/lock');

    $progress->flush(0);
} // END Big IF
