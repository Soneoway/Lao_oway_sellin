<?php
/**
* @author buu.pham
* @create 2015-09-30T10:39:54+07:00
*/
class My_Report_Delivery
{
    // public static function exportDeliveryOrder($order_list)
    // {
    //     require_once 'ExcelWriterXML.php';

    //     $filename = 'Delivery Order - '.date('Y-m-d H-i-s').'.xml';
    //     $xml = new ExcelWriterXML($filename);
    //     set_time_limit(0);
    //     ini_set('memory_limit', -1);
    //     error_reporting(~E_ALL);
    //     ini_set('display_error', 0);
    //     $xml->docAuthor('OPPO Vietnam');

    //     $xml->sendHeaders();

    //     $xml->stdOutStart();

    //     $sheet = $xml->addSheet('Delivery Order List');

    //     $heads = array(
    //         'NO.',
    //         'DELIVERY CODE',
    //         'TRACKING CODE',
    //         'SALE SN',
    //         'DISTRIBUTOR',
    //         'PROVINCE',
    //         'DISTRICT',
    //         'WEIGHT',
    //         'PHONE QUANTITY',
    //         'ACCESSORIES QUANTITY',
    //         'DIGITAL QUANTITY',
    //         'CREATED DATE',
    //         'DELIVERED DATE',
    //         'TYPE',
    //         'HUB',
    //         'INSIDE STAFF',
    //         'CARRIER',
    //         'WAREHOUSE',
    //         'STATUS',
    //     );

    //     $sheet->stdOutSheetStart();

    //     $sheet->stdOutSheetRowStart(1);
    //     foreach ($heads as $k=>$item){
    //         $sheet->stdOutSheetColumn('String', 1, $k+1, $item );
    //     }
    //     $sheet->stdOutSheetRowEnd();

    //     $QDistributor = new Application_Model_Distributor();
    //     $QWarehouse   = new Application_Model_Warehouse();
    //     $QDeliveryMan = new Application_Model_DeliveryMan();
    //     $QHub         = new Application_Model_Hub();

    //     $distributors = $QDistributor->get_cache2();
    //     $warehouses   = $QWarehouse->get_cache();
    //     $staffs       = $QDeliveryMan->get_cache();
    //     $hubs         = $QHub->get_cache();

    //     $i = 2;

    //     $no = 1;
    //     $db = Zend_Registry::get('db');
    //     $order_list = $db->query($order_list);

    //     foreach ($order_list as $_key => $_order)
    //     {
    //         $sheet->stdOutSheetRowStart($i);

    //         $j = 1;
    //         $sheet->stdOutSheetColumn('String', $i, $j++, $no++);
    //         $sheet->stdOutSheetColumn('String', $i, $j++, $_order['sn']);
    //         $sheet->stdOutSheetColumn('String', $i, $j++, $_order['tracking_no']);
    //         $sheet->stdOutSheetColumn('String', $i, $j++, $_order['sales_ref']);
    //         $sheet->stdOutSheetColumn('String', $i, $j++, isset($distributors[ $_order['d_id'] ]['title']) ? @$distributors[ $_order['d_id'] ]['title'] : "");
    //         $sheet->stdOutSheetColumn('String', $i, $j++, isset($distributors[ $_order['d_id'] ]['district']) ? My_Region::getValue(@$distributors[ $_order['d_id'] ]['district'], My_Region::Province) : "");
    //         $sheet->stdOutSheetColumn('String', $i, $j++, isset($distributors[ $_order['d_id'] ]['district']) ? My_Region::getValue(@$distributors[ $_order['d_id'] ]['district'], My_Region::District) : "");
    //         $sheet->stdOutSheetColumn('String', $i, $j++, $_order['weight']);
    //         $sheet->stdOutSheetColumn('String', $i, $j++, $_order['phone_quantity']);
    //         $sheet->stdOutSheetColumn('String', $i, $j++, $_order['accessories_quantity']);
    //         $sheet->stdOutSheetColumn('String', $i, $j++, $_order['digital_quantity']);

    //         if (isset($_order['created_at']) && strtotime($_order['created_at']))
    //             $sheet->stdOutSheetColumn('DateTime', $i, $j++,  date('Y-m-d\TH:i:s', strtotime($_order['created_at'])), 'db_datetime');
    //         else
    //             $sheet->stdOutSheetColumn('String', $i, $j++, '');

    //         if (isset($_order['delivered_at']) && strtotime($_order['delivered_at']))
    //             $sheet->stdOutSheetColumn('DateTime', $i, $j++,  date('Y-m-d\TH:i:s', strtotime($_order['delivered_at'])), 'db_datetime');
    //         else
    //             $sheet->stdOutSheetColumn('String', $i, $j++, '');

    //         $sheet->stdOutSheetColumn('String', $i, $j++, isset(My_Delivery_Type::$name[ $_order['type'] ]) ? My_Delivery_Type::$name[ $_order['type'] ] : '' );
    //         $sheet->stdOutSheetColumn('String', $i, $j++, isset($hubs[ $_order['hub'] ]) ? $hubs[ $_order['hub'] ] : '' );

    //         $carrier_ = $staff_ = null;

    //         switch ($_order['type']) {
    //             case My_Delivery_Type::Outside:
    //                 $carrier_ = isset(My_Carrier::$name[ $_order['carrier_id'] ]) ? My_Carrier::$name[ $_order['carrier_id'] ] : '';
    //                 break;
    //             case My_Delivery_Type::Inhouse:
    //                 $staff_ = isset($staffs[ $_order['staff_id'] ]) ? $staffs[ $_order['staff_id'] ] : '';
    //                 break;
    //             default:
    //                 $carrier = '';
    //                 break;
    //         }

    //         $sheet->stdOutSheetColumn('String', $i, $j++, !is_null($staff_) ? $staff_ : '' );
    //         $sheet->stdOutSheetColumn('String', $i, $j++, !is_null($carrier_) ? $carrier_ : '' );
    //         $sheet->stdOutSheetColumn('String', $i, $j++, isset($warehouses[ $_order['warehouse_id'] ]) ? $warehouses[ $_order['warehouse_id'] ] : "");
    //         $sheet->stdOutSheetColumn('String', $i, $j++, isset(My_Delivery_Order_Status::$name[ $_order['status'] ]) ? My_Delivery_Order_Status::$name[ $_order['status'] ] : "");

    //         $sheet->stdOutSheetRowEnd();

    //         $i++;

    //         unset($_order);
    //         unset($carrier_);
    //         unset($staff_);
    //         unset($_key);
    //     }

    //     $sheet->stdOutSheetEnd();

    //     $xml->stdOutEnd();

    //     exit;
    // }

    public static function exportDeliveryOrder($data)
    {
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        
        while(@ob_end_clean());
        ob_start();
        $filename = 'Delivery Order - '.date('Y-m-d H-i-s');
        header('Content-Description: File Transfer');
        header('Content-Encoding: UTF-8');
        header('Content-Disposition: attachment; filename=' . $filename . '.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        header('Content-Type: application/csv; charset=utf-8');
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $output = fopen('php://output', 'w');

        $heads = array(
            'NO.',
            'DELIVERY CODE',
            'TRACKING CODE',
            'SALE SN',
            'DISTRIBUTOR',
            'PROVINCE',
            'DISTRICT',
            'WEIGHT',
            'PHONE QUANTITY',
            'ACCESSORIES QUANTITY',
            'DIGITAL QUANTITY',
            'CREATED DATE',
            'DELIVERED DATE',
            'TYPE',
            'HUB',
            'INSIDE STAFF',
            'CARRIER',
            'WAREHOUSE',
            'STATUS',
            );

        fputcsv($output, $heads);

        $QDistributor = new Application_Model_Distributor();
        $QWarehouse   = new Application_Model_Warehouse();
        $QDeliveryMan = new Application_Model_DeliveryMan();
        $QHub         = new Application_Model_Hub();

        $distributors = $QDistributor->get_cache2();
        $warehouses   = $QWarehouse->get_cache();
        $staffs       = $QDeliveryMan->get_cache();
        $hubs         = $QHub->get_cache();

        $i = 1;

        $db = Zend_Registry::get('db');
        $data = $db->query($data);

        foreach ($data as $item)
        {
            $row = array();
            $row[] = $i++;
            $row[] = $item['sn'];
            $row[] = $item['tracking_no'];
            $row[] = $item['sales_ref'];
            $row[] = isset($distributors[ $item['d_id'] ]['title']) ? @$distributors[ $item['d_id'] ]['title'] : "";
            $row[] = isset($distributors[ $item['d_id'] ]['district']) ? My_Region::getValue(@$distributors[ $item['d_id'] ]['district'], My_Region::Province) : "";
            $row[] = isset($distributors[ $item['d_id'] ]['district']) ? My_Region::getValue(@$distributors[ $item['d_id'] ]['district'], My_Region::District) : "";
            $row[] = $item['weight'];
            $row[] = $item['phone_quantity'];
            $row[] = $item['accessories_quantity'];
            $row[] = $item['digital_quantity'];

            if (isset($item['created_at']) && strtotime($item['created_at']))
                $row[] = date('Y-m-d\TH:i:s', strtotime($item['created_at']));
            else
                $row[] = '';

            if (isset($item['delivered_at']) && strtotime($item['delivered_at']))
                $row[] = date('Y-m-d\TH:i:s', strtotime($item['delivered_at']));
            else
                $row[] = '';

            $row[] = isset(My_Delivery_Type::$name[ $item['type'] ]) ? My_Delivery_Type::$name[ $item['type'] ] : '';
            $row[] = isset($hubs[ $item['hub'] ]) ? $hubs[ $item['hub'] ] : '';

            $carrier_ = $staff_ = null;

            switch ($item['type']) {
                case My_Delivery_Type::Outside:
                    $carrier_ = isset(My_Carrier::$name[ $item['carrier_id'] ]) ? My_Carrier::$name[ $item['carrier_id'] ] : '';
                    break;
                case My_Delivery_Type::Inhouse:
                    $staff_ = isset($staffs[ $item['staff_id'] ]) ? $staffs[ $item['staff_id'] ] : '';
                    break;
                default:
                    $carrier = '';
                    break;
            }

            $row[] = !is_null($staff_) ? $staff_ : '';
            $row[] = !is_null($carrier_) ? $carrier_ : '';
            $row[] = isset($warehouses[ $item['warehouse_id'] ]) ? $warehouses[ $item['warehouse_id'] ] : "";
            $row[] = isset(My_Delivery_Order_Status::$name[ $item['status'] ]) ? My_Delivery_Order_Status::$name[ $item['status'] ] : "";

            fputcsv($output, $row);

            unset($item);
            unset($row);
        }

        unset($data);
        exit;
    }
}
