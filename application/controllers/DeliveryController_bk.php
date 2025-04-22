<?php
class DeliveryController extends My_Controller_Action
{
    public function manAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'man.php';
    }

    public function manCreateAction()
    {

    }

    public function manSaveAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'man-save.php';
    }

    public function manDeleteAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'man-delete.php';
    }

    public function manUndeleteAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'man-undelete.php';
    }

    public function manEditAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'man-edit.php';
    }

    ////////////////////////////////////////

    public function indexAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'index.php';
    }

    public function saveAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'save.php';
    }

    public function getOrderAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'get-order.php';
    }

    public function groupOrderAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'group-order.php';
    }

    public function printAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'print.php';
    }

    public function orderControlAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'order-control.php';
    }

    public function orderControlViewAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'order-control-view.php';
    }

    public function orderControlSaveAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'order-control-save.php';
    }

    public function orderControlDeliveredAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'order-control-delivered.php';
    }

    public function orderControlDeleteAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'order-control-delete.php';
    }

    public function orderControlDetailAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'order-control-detail.php';
    }

    public function orderControlPrintAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'order-control-print.php';
    }

    public function feeAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'fee.php';
    }

    public function feeCreateAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'fee-create.php';
    }

    public function feeEditAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'fee-edit.php';
    }

    public function feeDeleteAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'fee-delete.php';
    }

    public function feeSaveAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'fee-save.php';
    }

    public function massUpdateStatusAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'mass-update-status.php';
    }

    public function massUpdateStatusSaveAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'mass-update-status-save.php';
    }

    public function staffAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'staff.php';
    }

    public function staffEditAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'staff-edit.php';
    }

    public function staffSaveAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'staff-save.php';
    }

    private function _exportDeliveryOrderForFinance($order_list)
    {
        require_once 'ExcelWriterXML.php';


        $filename = 'Delivery Order - Finance - '.date('Y-m-d H-i-s').'.xml';
        $xml = new ExcelWriterXML($filename);
        set_time_limit(0);
        ini_set('memory_limit', -1);
        error_reporting(E_ALL);
        ini_set('display_error', 1);
        $xml->docAuthor('OPPO Vietnam');

        $xml->sendHeaders();

        $xml->stdOutStart();

        $sheet = $xml->addSheet('Delivery Order List');

        $heads = array(
            'NO.',
            'DELIVERY CODE',
            'WEIGHT',
            'NET WEIGHT',
            'PHONE NET WEIGHT',
            'ACCESSORIES NET WEIGHT',
            'DIGITAL NET WEIGHT',
            'CREATED DATE',
            'DELIVERED DATE',
            'TYPE',
            'HUB',
            'INSIDE STAFF',
            'CARRIER',
            'WAREHOUSE',
            'STATUS',
        );

        $sheet->stdOutSheetStart();

        $sheet->stdOutSheetRowStart(1);
        foreach ($heads as $k=>$item){
            $sheet->stdOutSheetColumn('String', 1, $k+1, $item );
        }
        $sheet->stdOutSheetRowEnd();

        $QDistributor = new Application_Model_Distributor();
        $QWarehouse   = new Application_Model_Warehouse();
        $QDeliveryMan = new Application_Model_DeliveryMan();
        $QHub         = new Application_Model_Hub();

        $distributors = $QDistributor->get_cache();
        $warehouses   = $QWarehouse->get_cache();
        $staffs       = $QDeliveryMan->get_cache();
        $hubs         = $QHub->get_cache();

        $i = 2;

        $no = 1;
        $db = Zend_Registry::get('db');
        $order_list = $db->query($order_list);

        foreach ($order_list as $_key => $_order)
        {
            $sheet->stdOutSheetRowStart($i);

            $j = 1;
            $sheet->stdOutSheetColumn('String', $i, $j++, $no++);
            $sheet->stdOutSheetColumn('String', $i, $j++, $_order['sn']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $_order['weight']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $_order['net_weight']);
            $sheet->stdOutSheetColumn('String', $i, $j++, isset($_order['net_weight']) && $_order['net_weight'] != 0 ? $_order['phone_weight']*100/$_order['net_weight'] : 0);
            $sheet->stdOutSheetColumn('String', $i, $j++, isset($_order['net_weight']) && $_order['net_weight'] != 0 ? $_order['accessories_weight']*100/$_order['net_weight'] : 0);
            $sheet->stdOutSheetColumn('String', $i, $j++, isset($_order['net_weight']) && $_order['net_weight'] != 0 ? $_order['digital_weight']*100/$_order['net_weight'] : 0);

            if (isset($_order['created_at']) && strtotime($_order['created_at']))
                $sheet->stdOutSheetColumn('DateTime', $i, $j++,  date('Y-m-d\TH:i:s', strtotime($_order['created_at'])), 'db_datetime');
            else
                $sheet->stdOutSheetColumn('String', $i, $j++, '');

            if (isset($_order['delivered_at']) && strtotime($_order['delivered_at']))
                $sheet->stdOutSheetColumn('DateTime', $i, $j++,  date('Y-m-d\TH:i:s', strtotime($_order['delivered_at'])), 'db_datetime');
            else
                $sheet->stdOutSheetColumn('String', $i, $j++, '');

            $sheet->stdOutSheetColumn('String', $i, $j++, isset(My_Delivery_Type::$name[ $_order['type'] ]) ? My_Delivery_Type::$name[ $_order['type'] ] : '' );
            $sheet->stdOutSheetColumn('String', $i, $j++, isset($hubs[ $_order['hub'] ]) ? $hubs[ $_order['hub'] ] : '' );

            $carrier_ = $staff_ = null;

            switch ($_order['type']) {
                case My_Delivery_Type::Outside:
                    $carrier_ = isset(My_Carrier::$name[ $_order['carrier_id'] ]) ? My_Carrier::$name[ $_order['carrier_id'] ] : '';
                    break;
                case My_Delivery_Type::Inhouse:
                    $staff_ = isset($staffs[ $_order['staff_id'] ]) ? $staffs[ $_order['staff_id'] ] : '';
                    break;
                default:
                    $carrier = '';
                    break;
            }

            $sheet->stdOutSheetColumn('String', $i, $j++, !is_null($staff_) ? $staff_ : '' );
            $sheet->stdOutSheetColumn('String', $i, $j++, !is_null($carrier_) ? $carrier_ : '' );
            $sheet->stdOutSheetColumn('String', $i, $j++, isset($warehouses[ $_order['warehouse_id'] ]) ? $warehouses[ $_order['warehouse_id'] ] : "");
            $sheet->stdOutSheetColumn('String', $i, $j++, isset(My_Delivery_Order_Status::$name[ $_order['status'] ]) ? My_Delivery_Order_Status::$name[ $_order['status'] ] : "");

            $sheet->stdOutSheetRowEnd();

            $i++;

            unset($_order);
            unset($carrier_);
            unset($staff_);
            unset($_key);
        }

        $sheet->stdOutSheetEnd();

        $xml->stdOutEnd();

        exit;
    }
}
