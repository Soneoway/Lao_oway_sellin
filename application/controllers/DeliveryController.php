<?php
class DeliveryController extends My_Controller_Action
{
    public function manAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'man.php';
    }

    public function orderCancelAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'order-cancel.php';
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

    public function orderControlDetailUpdateAction()
    {
        require_once 'delivery' . DIRECTORY_SEPARATOR . 'order-control-detail-update.php';
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

    private function _exportDeliveryOrderForKerry($sql) {

        // this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);

        $filename = 'Delivery Order - Kerry - '.date('Y-m-d H-i-s').'.csv';
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

        $heads = array(
            'CONSIGNMENT_NO',
            'REF_NO',
            'QTY',
            'RECIPENT_NAME',
            'ADDRESS',
            'SUB_DISTINCT',
            'DISTINCT',
            'PROVINCE',
            'POSTAL_CODE',
            'CONTANCT_PERSON',
            'MOBILE',
            'MOBILE1',
            'SERVICE_CODE',
            'COD_AMOUNT',
            'COD_TYPE',
            'DECLARE_VALUE'
        );



        fputcsv($output, $heads);

        $QDistributor   = new Application_Model_Distributor();
        $distributor_cache = $QDistributor->get_cache2();

        $result = $db->query($sql);
        //print_r($result);die;
        $i = 2;

        foreach($result as $item) {
            /*
            if (is_null($item['sn']) || $item['sales_ref'] == '') { $temp_sn = $item['sn']; }
            else { $temp_sn = $item['sn']; }
            */
            if (is_null($item['sales_ref']) || $item['sales_ref'] == '') { $temp_sn = $item['sales_sn']; }
            else { $temp_sn = $item['sales_ref']; }


            $row = array();
            $row[] = $item['tracking_no'];  //CONSIGNMENT_NO
            $row[] = '="'.$temp_sn.'"'; //REF_NO

            $row[] = number_format($item['number_of_package'], 2);   //QTY
            $row[] = $distributor_cache[$item['distributor_id']]['title'];  //RECIPENT_NAME
            $row[] = $item['address'];   //ADDRESS
            //SUB_DISTINCT
            $row[] = My_Region::getValue($distributor_cache[$item['distributor_id']]['district'], My_Region::Area);
            //DISTINCT
            $row[] = My_Region::getValue($distributor_cache[$item['distributor_id']]['district'], My_Region::District);
            //PROVINCE
            $row[] = My_Region::getValue($distributor_cache[$item['distributor_id']]['district'], My_Region::Province);

            $row[] = ''; //POSTAL_CODE
            $row[] = $distributor_cache[$item['distributor_id']]['name']; //CONTANCT_PERSON
            $row[] = $item['phone_number']; //MOBILE
            $row[] = ''; //SERVICE_CODE
            $row[] = ''; //COD_AMOUNT
            $row[] = ''; //COD_TYPE
            $row[] = ''; //DECLARE_VALUE

            fputcsv($output, $row);
            unset($item);
            unset($row);
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

     private function _exportExcelOrderCancel($order) {

        // this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);

        $filename = 'Cancel Delivery Order - Kerry - '.date('Y-m-d H-i-s').'.csv';
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

        $heads = array(
            'SALE ID',
            'SALE ORDER NUMBER',
            'RETAILER NAME',
            'SALES QUANTITY',
            'TOTAL AMOUNT',
            'WAREHOUSE',
            'CANCEL TIME',
            'CANCEL BY',
            'REMARK',
            'CANCEL CONFIRM BY',
            'CANCEL CONFIRM TIME',
            'STATUS'
        );



        fputcsv($output, $heads);

        //print_r($result);die;
        $i = 2;

        foreach($order as $item) {

            $item['cancel_delivery_status'] = ($item['cancel_delivery_status'] == 1) ? 'Comfirm': '' ;

            $row = array();
            $row[] = $item['id'];
            $row[] = $item['sn_ref'];
            $row[] = $item['title'];
            $row[] = $item['sum'];
            $row[] = $item['total_price'];
            $row[] = $item['name'];
            $row[] = $item['date_canceled'];
            $row[] = $item['name_cancel'];
            $row[] = $item['canceled_remark'];
            $row[] = $item['name_cancel_kerry'];
            $row[] = $item['cancel_delivery_date'];
            $row[] = $item['cancel_delivery_status'];


            fputcsv($output, $row);
            unset($item);
            unset($row);
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


    public function sendShipmentInfoRollBack($getDetailMarket){

      // $testReturn = '{
      //   "res": {
      //     "shipment": {
      //       "con_no": "KEX1701000001",
      //       "status_code": "000",
      //       "status_desc": "Success Requisition"
      //     } }
      //   }';

      //   return $testReturn;

      //   exit();

    foreach ($getDetailMarket as $key) {

      // print_r($getDetailMarket);
      // print_r($key['provice_id']);
      // print_r($this->getDayByDateTime($key['outmysql_time']));
      // exit();

      $s_name = 'บริษัท ไทย ออปโป้ จำกัด';
      $s_address = 'เลขที่ 89 ชั้น 31 ห้อง 5-7';
      $s_road = 'รัชดาภิเษก';
      $s_subdistrict = 'ดินแดง';
      $s_district = 'ดินแดง';
      $s_province = 'กรุงเทพมหานคร';
      $s_zipcode = '10400';

      // tel k' ice
      $s_mobile1 = '0655062270';
      // tel k' nok
      $s_mobile2 = '0846410204';

      $r_name = $key['contact_name'];
      $r_address = $key['address'];

      // $r_village = '';
      // $r_soi = '';
      // $r_road = '';

      $r_subdistrict = $key['district_name'];
      $r_district = $key['amphure_name'];
      $r_province = $key['provice_name'];
      $r_zipcode = $key['zipcode'];

      $textTel = $key['phone'];

      $cTextTel = $this->cleanInt($textTel);

      if(strlen($cTextTel) == 9){
        $cTextTel = '0' . $cTextTel;
      }

      if(strlen($cTextTel) < 9){
        // k.ice default tel
        $cTextTel = '0655062270';
      }

      $tel1 = substr($cTextTel, 0, 10);
      $tel2 = substr($cTextTel,10, 10);
      $tel3 = substr($cTextTel, 20);

      if(!$tel1){
        $tel1 = "";
      }

      if(!$tel2){
        $tel2 = "";
      }

      if(!$tel3){
        $tel3 = "";
      }

      $r_mobile1 = $tel1;
      $r_mobile2 = $tel2;
      $r_telephone = $tel3;

      // $r_contactperson = '';
      // $special_note = 'รับเอกสารที่พับมุมกลับ';
      $special_note = '';

      $service_code = 'ND';

      //address แม่วัง สำนักงานใหญ่
      // if(isset($key['d_id']) && in_array($key['d_id'], [1200,29829,29355,29354])){
      //   $service_code = 'FD';
      // }
      
      $tot_pkg = $key['number_of_package'];
      $ref_no = $key['sn_ref'];

      $con_no = $key['tracking_no'];

      $shipmentInfo = [
      "con_no" => $con_no,
      "s_name" => $s_name,
      "s_address" => $s_address,
      "s_road" => $s_road,
      "s_subdistrict" => $s_subdistrict,
      "s_district" => $s_district,
      "s_province" => $s_province,
      "s_zipcode" => $s_zipcode,
      "s_mobile1" => $s_mobile1,
      "s_mobile2" => $s_mobile2,
      "r_name" => $r_name,
      "r_address" => $r_address,
      // "r_village" => $r_village,
      // "r_soi" => $r_soi,
      // "r_road" => $r_road,
      "r_subdistrict" => $r_subdistrict,
      "r_district" => $r_district,
      "r_province" => $r_province,
      "r_zipcode" => $r_zipcode,
      "r_mobile1" => $r_mobile1,
      "r_mobile2" => $r_mobile2,
      "r_telephone" => $r_telephone,
      // "r_contactperson" => $r_contactperson,
      "special_note" => $special_note,
      "service_code" => $service_code,
      "tot_pkg" => (int)$tot_pkg,
      "ref_no" => $ref_no,
      "action_code" => "D"
      ];

      // print_r($shipmentInfo);exit();
      // print_r($this->curlSendShipmentInfo($shipmentInfo));
      return $this->curlSendShipmentInfo($shipmentInfo);

    }

    return false;

  }

  public function curlSendShipmentInfo($shipmentInfo){

    $url = KERRY_EDI_SHIPMENTINFO_URL;

    $post_date = [
    "req" => [
    "shipment" => $shipmentInfo
    ]
    ];

    $post_json_data = json_encode($post_date, JSON_UNESCAPED_UNICODE);

    // print_r($post_json_data);exit();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post_json_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [
    'app_id: OPPO',
    'app_key: ab89011e-a09c-4b',
    'Content-Type: application/json; charset=UTF-8'
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $server_output = curl_exec ($ch);

    curl_close ($ch);

    return $server_output;
  }

  public function curlSendAPIJNTCancel($json_data){

    $url = JNT_API_URL;

    $url_create = $url . 'oppo/order/cancelOrder.do';

    $data_digest = $this->genDataDigestJAndT($json_data);

    $post_date = [
    'logistics_interface' => $json_data,
    'data_digest' => $data_digest,
    'eccompanyid' => JNT_ECCOMPANYID,
    'msg_type' => 'ORDERCANCEL'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url_create);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post_date);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec ($ch);

    curl_close ($ch);

    return $server_output;
  }

  public function JNTCancelOrder($tracking_no){
    $json_data = ['eccompanyid' => JNT_ECCOMPANYID,
                  'customerid' => JNT_CUSTOMERID,
                  'txlogisticid' => $tracking_no,
                  'reason' => 'Cancel order.'
                 ];

    $json_data_encode = json_encode($json_data,JSON_UNESCAPED_UNICODE);
    // print_r($json_data_encode);die;
    return $this->curlSendAPIJNTCancel($json_data_encode);
  }

// ------------------------------- Function tools ------------------------

  public function genDataDigestJAndT($json_data){
    $key = JNT_KEY;
    $endcode_md5 = md5($json_data.$key);
    $endcode_base64 = base64_encode(strtolower($endcode_md5));
    return $endcode_base64;
  } 

// ------------------------------- Function tools ------------------------

}
