<?php
// config for template
define('START_ROW', 2);
define('CODE_COL', 0);
define('RECEIVER_COL', 1);
define('TIME_COL', 2);

$this->_helper->layout->disableLayout();

if ( $this->getRequest()->getMethod() == 'POST' ) { // Big IF
    set_time_limit(0);
    ini_set('memory_limit', -1);
    $progress = new My_File_Progress('parent.set_progress');
    $progress->flush(0);

    $save_folder   = 'delivery_status';
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

    $error_list = array();
    $total      = 0;
    $success    = 0;
    $failed     = 0;

    $QDeliveryOrder = new Application_Model_DeliveryOrder();

    $order_rows = $highestRow - START_ROW;

    for ($row = START_ROW; $row <= $highestRow; $row++) {
        $percent = 0;// round($row * 100 / $order_rows, 1);
        $progress->flush($percent);

        try {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL, TRUE, FALSE);

            $total++;
            // validate data
            $rowData = isset($rowData[0]) ? $rowData[0] : array();

            $rowData[CODE_COL] = isset($rowData[CODE_COL]) ? trim($rowData[CODE_COL]) : '';

            if (empty($rowData[CODE_COL])) throw new Exception("Empty delivery Code, row: ".$row);

            $rowData[RECEIVER_COL] = isset($rowData[RECEIVER_COL]) ? trim($rowData[RECEIVER_COL]) : '';
            $rowData[TIME_COL] = isset($rowData[TIME_COL]) ? trim($rowData[TIME_COL]) : '';

            $where = array();
            $where[] = $QDeliveryOrder->getAdapter()->quoteInto('sn = ?', $rowData[CODE_COL]);
            $where[] = $QDeliveryOrder->getAdapter()->quoteInto('status IS NOT NULL AND status <> ?', My_Delivery_Order_Status::Deleted);
            $order_check = $QDeliveryOrder->fetchRow($where);

            if (!$order_check) throw new Exception("Invalid order, Code: " . $rowData[CODE_COL] . ", row: ".$row);


            // chọn status, place
            $status = $tmp_status = false;
            $sql = $place_type = $place_id = $imei_status = 0;
            $data = array();

            if (isset($order_check['hub']) && $order_check['hub']
                && isset($order_check['status']) && $order_check['status'] == My_Delivery_Order_Status::Warehouse_To_Hub) {
                $status = My_Delivery_Order_Status::Hub_To_Distributor;
                // $place_type  = My_Place::HUB;
                // $place_id    = $order_check['hub'];
                // $imei_status = My_Imei_Status::HUB_TO_DISTRIBUTOR;

            } elseif (!(isset($order_check['hub']) && $order_check['hub'])
                && isset($order_check['status']) && $order_check['status'] == My_Delivery_Order_Status::Warehouse_To_Distributor) {
                $status = My_Delivery_Order_Status::Delivered;
                // $place_type  = My_Place::DISTRIBUTOR;
                // $imei_status = My_Imei_Status::DELIVERED;
            }

            if (isset($order_check['hub']) && $order_check['hub']) {
                $tmp_status = My_Delivery_Order_Status::Hub_To_Distributor;
            } else {
                $tmp_status = My_Delivery_Order_Status::Delivered;
            }

            if (!empty($rowData[RECEIVER_COL])) {
                $data = array(
                    'real_receiver' => strval($rowData[RECEIVER_COL]),
                    'real_receive_time' => PHPExcel_Style_NumberFormat::toFormattedString($rowData[TIME_COL], 'YYYY-mm-dd HH:i:s'),
                );
            }

            // trạng thực hiện tại của vận đơn - nếu carrier update trước khi hub confirm thì lấy trạng thái này
            if ($status)
                $data['status'] = $status;

            // trạng thái carrier giao hàng - có thể carrier update sau khi hub nhận hàng và giao đại lý
            // vẫn log lại trạng thái này - nhưng không update vào vận đơn
            if ($tmp_status)
                My_Delivery_Order_Status::setStatus($order_check['id'], $tmp_status, $userStorage->id);

            if (My_Delivery_Order_Status::Delivered == $status) {
                $data['delivered_at'] = date('Y-m-d H:i:s');
                $data['delivered_by'] = $userStorage->id;
            }

            if ($data && count($data))
                $QDeliveryOrder->update($data, $where);

            // switch ($place_type) {
            //     case My_Place::DISTRIBUTOR:
            //         // cập nhật trạng thái IMEI, để trigger update tồn kho
            //         $sql = "UPDATE imei, delivery_sales
            //             SET imei.place_id=imei.distributor_id, imei.place_type=?, imei.imei_status=?
            //             WHERE imei.sales_sn=delivery_sales.sales_sn
            //             AND delivery_sales.delivery_order_id=?";

            //         $db->query($sql, array(
            //             $place_type,
            //             $imei_status,
            //             $order_check['id'],
            //         ));
            //         //
            //         break;
            //     case My_Place::HUB:
            //         // cập nhật trạng thái IMEI, để trigger update tồn kho
            //         $sql = "UPDATE imei, delivery_sales
            //             SET imei.place_id=?, imei.place_type=?, imei.imei_status=?
            //             WHERE imei.sales_sn=delivery_sales.sales_sn
            //             AND delivery_sales.delivery_order_id=?";

            //         $db->query($sql, array(
            //             $place_id,
            //             $place_type,
            //             $imei_status,
            //             $order_check['id'],
            //         ));
            //         //
            //         break;
            //     default:
            //         break;
            // }

            $success++;
        } catch (Exception $e) {
            $failed++;
            $error_list[] = $e->getMessage();
        }

    } // END loop through order rows

    $this->view->error_list = $error_list;
    $this->view->total      = $total;
    $this->view->success    = $success;
    $this->view->failed     = $failed;
    $progress->flush(0);
} // END Big IF
