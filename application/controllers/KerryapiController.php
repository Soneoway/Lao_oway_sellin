<?php

class KerryapiController extends My_Controller_Action
{

  private $partBucket = HOST.'upload/img_delivery/';

	public function init()
	{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    date_default_timezone_set("Asia/Bangkok");
  }

  public function indexAction()
  {

    // $this->dashboarddeliveryAction();

    echo 'Welcome To Oppo / Kerry By.MosTest';
    exit();

    // $sn = '201703081445078074';

    // $QKSSL = new Application_Model_KerryShipmentStatusLog();

    // $getDetailMarket = $QKSSL->getDetailMarket($sn);

    // $test = $this->sendShipmentInfo($getDetailMarket);

    // print_r($test);

    // exit();

    // ------------- START : Test Function Condition Send Kerry ------------------
    // $QKCC = new Application_Model_KerryCompanyCondition();
    // $QKHC = new Application_Model_KerryHolidayCondition();

    // //getKerryCondition by intersect Geniuos
    // $getKerryCondition = $QKCC->getKerryCondition('1');
    // $arrCondition = $this->convertDataInArrToArrayData($getKerryCondition, 'provice_id');

    // //provice_id not allow kerry : 1,4,3,2,12,11
    // $provice_id = "99";

    // //warehouse_id allow kerry and Brandshop Warehouse at Kerry and OPEN MARKET : 36,92,123
    // $warehouse_id = "9";

    // $outmysql_time = "2017-04-03 07:07:07";

    // $day = $this->getDayByDateTime($outmysql_time);

    // $dateImeiOut = substr($outmysql_time, 0, 10);

    // //getKerryHolidayCondition
    // $getKerryHoliday = $QKHC->getKerryHoliday($dateImeiOut);
    // $arrHolidayCondition = $this->convertDataInArrToArrayData($getKerryHoliday, 'day_holiday');

    // $holiday = 'Not Holiday';
    // if (count($arrHolidayCondition) > 0) {
    //   $holiday = 'Holiday';
    // }

    // echo "warehouse_id not allow : 62, 73<br /><br />provice_id not allow : 1, 4, 3, 2, 12, 11<br /><br /> Day not allow : Sun, Sat <br /><br />This day is : $holiday<br /><br />";
    // echo "Warehouse ID : $warehouse_id | Provice ID : $provice_id | Day : $day | Out Mysql Time : $outmysql_time <br /><br />";

    // $ka_type = '';
    // $d_id = '';

    // if(!$this->checkConditionSendKerry($arrCondition, $arrHolidayCondition, $warehouse_id, $provice_id, $outmysql_time, $dateImeiOut, $ka_type, $d_id)){
    //   echo "<br/>---------------------<br/>";
    //   echo "NOT SEND!";
    //   echo "<br/>---------------------<br/>";
    // }else{
    //   echo "<br/>---------------------<br/>";
    //   echo "SEND NOW!";
    //   echo "<br/>---------------------<br/>";
    // }

    // exit();

   // ------------- START : Test Function Condition Send Kerry ------------------

    // $shipmentInfo = [
    // "con_no" => "KERY00000000001",
    // "s_name" => "KERRY EXPRESS(Thailand)CO., LTD.",
    // "s_address" => "900/888",
    // "s_road" => "ประชาชื่น",
    // "s_subdistrict" => "วงศ์สว่าง",
    // "s_district" => "บางซื่อ",
    // "s_province" => "กรุงเทพมหานคร",
    // "s_zipcode" => "10310",
    // "s_mobile1" => "0812345678",
    // "s_mobile2" => "",
    // "s_telephone" => "0-2935-6799 # 300",
    // "s_email" => "",
    // "s_contactperson" => "คุณปรีชา มากมี",
    // "r_name" => "PHUTTACHINNARAT PITSANULOK HOSPITAL",
    // "r_address" => "1",
    // "r_village" => "ฟ้าใส วิวล์",
    // "r_soi" => "",
    // "r_road" => "",
    // "r_subdistrict" => "ในเมือง",
    // "r_district" => "เมืองพิษณุโลก",
    // "r_province" => "พิษณุโลก",
    // "r_zipcode" => "65000",
    // "r_mobile1" => "0819990012",
    // "r_mobile2" => "",
    // "r_telephone" => "",
    // "r_email" => "",
    // "r_contactperson" => "คุณนนั ทิดา แกว้ ชุ่ม",
    // "special_note" => "เกบ็ เงินสดปลายทาง",
    // "service_code" => "ND",
    // "cod_amount" => 2500.00,
    // "cod_type" => "CASH",
    // "tot_pkg" => 2,
    // "declare_value" => 0.00,
    // "ref_no" => "REF-3359000187",
    // "action_code" => "A"
    // ];

    // print_r($this->curlSendShipmentInfo($shipmentInfo));


    // $packageDetail = [
    // 'consignment_no' => 'OPPOTEST99999',
    // 'pkg_no' => '1',
    // 'qty' => '2',
    // 'pkg_weight' => '35',
    // 'pkg_length' => '0',
    // 'pkg_breadth' => '0',
    // 'pkg_height' => '0'
    // ];


    // print_r($this->curlSendPackageDetail($packageDetail));


  }

  public function sendShipmentInfo($getDetailMarket){

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

    $QKCC = new Application_Model_KerryCompanyCondition();
    $QKHC = new Application_Model_KerryHolidayCondition();

    //getKerryCondition by intersect Geniuos
    $getKerryCondition = $QKCC->getKerryCondition('1');
    $arrCondition = $this->convertDataInArrToArrayData($getKerryCondition, 'provice_id');

    foreach ($getDetailMarket as $key) {

      // print_r($getDetailMarket);
      // print_r($key['provice_id']);
      // print_r($this->getDayByDateTime($key['outmysql_time']));
      // exit();

      $dateTimeImeiOut = $key['outmysql_time'];
      $dateImeiOut = substr($dateTimeImeiOut, 0, 10);

      //getKerryHolidayCondition
      $getKerryHoliday = $QKHC->getKerryHoliday($dateImeiOut);
      $arrHolidayCondition = $this->convertDataInArrToArrayData($getKerryHoliday, 'day_holiday');

      if(!$this->checkConditionSendKerry($arrCondition, $arrHolidayCondition, $key['warehouse_id'], $key['provice_id'], $dateTimeImeiOut, $dateImeiOut, $key['ka_type'], $key['d_id'])){
        continue;
      }

      $s_name = 'บริษัท โพสเซฟี่ กรุ๊ป จำกัด (OPPO)';
      $s_address = 'อาคารเอไอเอ แคปปิตอล เซ็นเตอร์ ชั้น 31 ห้อง 5-7 เลขที่ 89';
      $s_road = 'รัชดาภิเษก';
      $s_subdistrict = 'ดินแดง';
      $s_district = 'ดินแดง';
      $s_province = 'กรุงเทพมหานคร';
      $s_zipcode = '10400';
      
      // tel k' ศริญญา เกตุเขียว
      $s_mobile1 = '0657174931';
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
        // k.ศริญญา เกตุเขียว default tel
        $cTextTel = '0657174931';
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
      "action_code" => "A"
      ];

      // print_r($shipmentInfo);
      // print_r($this->curlSendShipmentInfo($shipmentInfo));
      return $this->curlSendShipmentInfo($shipmentInfo);

    }

    return false;

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

      $s_name = 'บริษัท โพสเซฟี่ กรุ๊ป จำกัด (OPPO)';
      $s_address = 'อาคารเอไอเอ แคปปิตอล เซ็นเตอร์ ชั้น 31 ห้อง 5-7 เลขที่ 89';
      $s_road = 'รัชดาภิเษก';
      $s_subdistrict = 'ดินแดง';
      $s_district = 'ดินแดง';
      $s_province = 'กรุงเทพมหานคร';
      $s_zipcode = '10400';

      // tel k' ศริญญา เกตุเขียว 
      $s_mobile1 = '0657174931';
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
        // k.ศริญญา เกตุเขียว default tel
        $cTextTel = '0657174931';
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

  public function curlSendPackageDetails($packageDetail){

    // $testReturn = '{
    //   "res": {
    //     "status": {
    //       "status_code": "000",
    //       "status_desc": "Success"
    //     } }
    //   }';

    //   return $testReturn;

    //   exit();

    $url = KERRY_EDI_PACKAGEDETAILS_URL;

    $post_date = [
    "req" => [
    "info" => $packageDetail
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

  public function shipmentupdateAction() {

    $headers = $this->getHeaders();
    if(!$this->validateHearders($headers)){
      http_response_code(500);
      $result = array(
        "Message" => "Error."
        );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);

    $this->validateRequest($data);

    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    $QKSSL = new Application_Model_KerryShipmentStatusLog();
    $QDO = new Application_Model_DeliveryOrder();

    $date = new DateTime();
    $created_date = $date->format('Y-m-d H:i:s');

    try {

      $flogfrom = 0;
      switch ($headers['Privatekey']) {
        case KERRY_API_UPDATESTATUS:
          $flogfrom = 2;
          break;
        case JNT_API_UPDATESTATUS:
          $flogfrom = 3;
          break;
      }

      // insert Log of Shipment Status
      $shipment_log = array(
        'tracking_no'           => $data['req']['status']['con_no'],
        'shipment_status_id'    => $data['req']['status']['status_code'],
        'shipment_status_date'  => $data['req']['status']['status_date'],
        'sn_ref'                => $data['req']['status']['ref_no'],
        'created_date'          => $created_date,
        'from'                  => $flogfrom
        );

      $QKSSL->insert($shipment_log);

      // update Status of Delivery Order
      $DO_update = array(
        'real_receiver'     => $data['req']['status']['status_code'],
        'real_receive_time' => $data['req']['status']['status_date']
        );

      $DO_where = $QDO->getAdapter()->quoteInto('tracking_no = ?', $data['req']['status']['con_no']);

      $QDO->update($DO_update, $DO_where);

      $result = array(
        'res' => array(
          'status' => array(
            'status_code'   => '000',
            'status_desc'   => 'Successful'
            )
          )
        );

      // insert Log of Imei Receive
      if ($data['req']['status']['status_code'] == "POD") {
        $QIDR = new Application_Model_ImeiDistributorReceive();

        $imei_list = $QIDR->getImeiBySN($data['req']['status']['ref_no']);

        for ($i=0;$i<count($imei_list);$i++) {

          $imei_log = array(
            'imei_sn'               => $imei_list[$i]['imei_sn'],
            'tracking_no'           => $data['req']['status']['con_no'],
            'shipment_status_id'    => $data['req']['status']['status_code'],
            'shipment_status_date'  => $data['req']['status']['status_date'],
            'sn_ref'                => $data['req']['status']['ref_no']
            );

          //print_r($imei_log); echo $i; echo "<br/>";
          $QIDR->insert($imei_log);

        }

      }

      $db->commit();

    } catch (Exception $e) {

      $db->rollBack();
      //echo "Fail! : ".$e;

      $result = array(
        'res' => array(
          'status' => array(
            'status_code'   => '999',
            'status_desc'   => 'Unsuccessful Requisition / Undefined error exception, return windows exception message'
            )
          )
        );

    }

    //print_r($result);
    print_r(json_encode($result));
  }

  public function cronAction(){

    $QKT = new Application_Model_KerryTransaction();
    $QKSSL = new Application_Model_KerryShipmentStatusLog();

    $getKerryTransaction = $QKT->getKerryTransaction();

    $date = new DateTime();
    $created_date = $date->format('Y-m-d H:i:s');

    foreach ($getKerryTransaction as $key) {

      $created_date_temp = $created_date;
      $created_date_temp2 = $created_date;

      $sn = $key['sn'];
      $delivery_type = $key['delivery_type'];

      // START : Send shipment info

      if($key['is_co'] == 1){
        $getDetailMarket = $QKSSL->getDetailCO($sn);
      }else{
        $getDetailMarket = $QKSSL->getDetailMarket($sn);
      }

      switch ($delivery_type) {
        case '1': //Kerry
          
          $response = $this->sendShipmentInfo($getDetailMarket);

          $arrResponse = json_decode($response);

          $status = null;
          $status_code = null;
          $remark = null;


          if(isset($arrResponse->res->shipment->status_code)){
            $status_code = $arrResponse->res->shipment->status_code;
          }

          if(is_null($status_code)){

            $created_date_temp = '0000-00-00 00:00:00';

            $textRemark = $created_date . ' | not find status code';

            if($key['remark'] == ''){
              $remark = $textRemark;
            }else{
              $remark = $key['remark'] . ',' . $textRemark;
            }

            $status = $this->changeStatusError($key['status']);

          }else{

            if($status_code != '000'){

              $created_date_temp = '0000-00-00 00:00:00';

              $textRemark = $created_date . ' | ' . $status_code;

              if($key['remark'] == ''){
                $remark = $textRemark;
              }else{
                $remark = $key['remark'] . ',' . $textRemark;
              }

              $status = $this->changeStatusError($key['status']);

            }else{
              //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
              $status = 7;

              $textRemark = $created_date . ' | ' . $status_code;

              if($key['remark'] == ''){
                $remark = $textRemark;
              }else{
                $remark = $key['remark'] . ',' . $textRemark;
              }
            }

          }

          // END : Send shipment info

          // START : Send package details

          //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
          if($status == 7){

            $con_no = $getDetailMarket[0]['tracking_no'];
            $package = $getDetailMarket[0]['number_of_package'];
            $weight = $getDetailMarket[0]['weight'];

            $packageDetail = [
            'consignment_no' => $con_no,
            'pkg_no' => 1,
            'qty' => (int)$package,
            'pkg_weight' => (double)$weight,
            'pkg_length' => 0,
            'pkg_breadth' => 0,
            'pkg_height' => 0
            ];

            $response2 = $this->curlSendPackageDetails([$packageDetail]);

            $arrResponse2 = json_decode($response2);

            $status2 = null;
            $status_code2 = null;
            $remark2 = null;

            if(isset($arrResponse2->res->shipment->status_code)){
              $status_code2 = $arrResponse2->res->shipment->status_code;
            }

            if(is_null($status_code2)){

              // START : Roll Back
              $responseRollBack = $this->sendShipmentInfoRollBack($getDetailMarket);
              $arrResponseRollBack = json_decode($responseRollBack);

              $status_codeRollBack = null;

              if(isset($arrResponseRollBack->res->shipment->status_code)){
                $status_codeRollBack = $arrResponseRollBack->res->shipment->status_code;
              }

              if(is_null($status_codeRollBack)){

                //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
                $status2 = 4;
                $status_codeRollBack = 'not find status code';

                $textRemark2 = $created_date . ' | ' . $status_codeRollBack;
                $this->addRollBack($getDetailMarket, $created_date);

              }else{

                if($status_codeRollBack != '000'){

                  //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
                  $status2 = 4;

                  $textRemark2 = $created_date . ' | ' . $status_codeRollBack;
                  $this->addRollBack($getDetailMarket, $created_date);
                }else{
                  //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
                  $status2 = 5;

                  $textRemark2 = $created_date . ' | not find status code';

                  if($key['rollback_remark'] == ''){
                    $remark2 = $textRemark2;
                  }else{
                    $remark2 = $key['rollback_remark'] . ',' . $textRemark2;
                  }

                }
              }

                  // END : Roll Back


            }else{

              if($status_code2 != '000'){

                // START : Roll Back
                $responseRollBack = $this->sendShipmentInfoRollBack($getDetailMarket);
                $arrResponseRollBack = json_decode($responseRollBack);

                $status_codeRollBack = null;

                if(isset($arrResponseRollBack->res->shipment->status_code)){
                  $status_codeRollBack = $arrResponseRollBack->res->shipment->status_code;
                }

                if(is_null($status_codeRollBack)){

                  //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
                  $status2 = 4;
                  $status_codeRollBack = 'not find status code';

                  $textRemark2 = $created_date . ' | ' . $status_codeRollBack;

                  if($key['rollback_remark'] == ''){
                    $remark2 = $textRemark2;
                  }else{
                    $remark2 = $key['rollback_remark'] . ',' . $status_codeRollBack;
                  }
                  $this->addRollBack($getDetailMarket, $created_date);

                }else{

                  if($status_codeRollBack != '000'){

                    //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
                    $status2 = 4;

                    $textRemark2 = $created_date . ' | ' . $status_codeRollBack;

                    if($key['rollback_remark'] == ''){
                      $remark2 = $textRemark2;
                    }else{
                      $remark2 = $key['rollback_remark'] . ',' . $status_codeRollBack;
                    }
                    $this->addRollBack($getDetailMarket, $created_date);
                  }else{
                    //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
                    $status2 = 5;

                    $textRemark2 = $created_date . ' | ' . $status_code2;

                    if($key['rollback_remark'] == ''){
                      $remark2 = $textRemark2;
                    }else{
                      $remark2 = $key['rollback_remark'] . ',' . $textRemark2;
                    }
                  }
                }

                  // END : Roll Back

              }else{

                //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
                $status2 = 7;

                $textRemark2 = $created_date . ' | ' . $status_code2;

                if($key['rollback_remark'] == ''){
                  $remark2 = $textRemark2;
                }else{
                  $remark2 = $key['rollback_remark'] . ',' . $textRemark2;
                }
              }

            }

          }

          // END : Send package details


          // update send date of Kerry Transaction
          // echo "$status | $status2";exit();

          $log_shipmentinfo = json_encode($getDetailMarket, JSON_UNESCAPED_UNICODE);
          $log_package = json_encode($packageDetail, JSON_UNESCAPED_UNICODE);
          $log_api = $log_shipmentinfo . ' | ' . $log_package;


          //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
          if($status2 == 7){
            if(is_null($remark)){
              $KT_update = array(
                'send_date' => $created_date_temp,
                'status' => $status,
                'status_code' => $status_code,
                'send_log' => $log_api
                );
            }else{
              $KT_update = array(
                'send_date' => $created_date_temp,
                'status' => $status,
                'status_code' => $status_code,
                'remark' => $remark,
                'send_log' => $log_api
                );
            }
          }else{
            //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
            if($status == 7){

              $status = $this->changeStatusError($key['status']);

              if(is_null($remark2)){
                $KT_update = array(
                  'send_date' => $created_date_temp,
                  'status' => $status,
                  'status_code' => $status_code,
                  'rollback_date' => $created_date_temp2,
                  'rollback_status' => $status2,
                  'rollback_code' => $status_code2,
                  'send_log' => $log_api
                  );
              }else{
                $KT_update = array(
                  'send_date' => $created_date_temp,
                  'status' => $status,
                  'status_code' => $status_code,
                  'rollback_date' => $created_date_temp2,
                  'rollback_status' => $status2,
                  'rollback_code' => $status_code2,
                  'rollback_remark' => $remark2,
                  'send_log' => $log_api
                  );
              }
            }else{
              if(is_null($remark)){
                $KT_update = array(
                  'send_date' => $created_date_temp,
                  'status' => $status,
                  'status_code' => $status_code,
                  'send_log' => $log_api
                  );
              }else{
                $KT_update = array(
                  'send_date' => $created_date_temp,
                  'status' => $status,
                  'status_code' => $status_code,
                  'remark' => $remark,
                  'send_log' => $log_api
                  );
              }
            }

          }

          // print_r($KT_update);exit();

          $KT_where = [];
          $KT_where[] = $QKT->getAdapter()->quoteInto('sn = ?', $sn);
          $KT_where[] = $QKT->getAdapter()->quoteInto('type = ?', '1');
          $KT_where[] = $QKT->getAdapter()->quoteInto('delivery_type = ?', $delivery_type);
          $QKT->update($KT_update, $KT_where);

          break;
        case '2': //J&T

          $QMK = new Application_Model_Market();

          $product_details = $QMK->getDetailsProduct($getDetailMarket[0]['sn_ref']);

          $totalquantity = 0;

          foreach ($product_details as $key_pd) {
            $totalquantity = $totalquantity+$key_pd['num'];
          }

          $json_data = ['txlogisticid' => $getDetailMarket[0]['tracking_no'],
                        'logisticproviderid' => 'JNT',
                        'eccompanyid' => JNT_ECCOMPANYID,
                        'customerid' => JNT_CUSTOMERID,
                        'actiontype' => 'add',
                        'paytype' => '1',
                        'servicetype' => '1',
                        'ordertype' => '1',
                        'createordertime' => $created_date,
                        'sendstarttime' => $created_date,
                        'sendendtime' => $created_date,
                        'sender' => ['name' => 'บริษัท โพสเซฟี่ กรุ๊ป จำกัด (OPPO)',
                                     'phone' => '0657174931',
                                     'mobile' => '0846410204',
                                     'city' => '1',//กรุงเทพมหานคร
                                     'area' => '26',//ดินแดง
                                     'postcode' => '10400',
                                     'address' => 'อาคาร AIA Capital Center ชั้น 31 ห้อง 5-7 เลขที่ 89 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพฯ'
                                    ],
                        'ref_no' => trim($getDetailMarket[0]['sn_ref']),
                        'receiver' => ['name' => trim($getDetailMarket[0]['contact_name']),
                                       // 'phone' => $getDetailMarket[0][''],
                                       // 'mobile' => $getDetailMarket[0][''],
                                       'city' => trim($getDetailMarket[0]['provice_id']),
                                       'area' => trim($getDetailMarket[0]['amphure_id']),
                                       'postcode' => trim($getDetailMarket[0]['zipcode']),
                                       'address' => trim($getDetailMarket[0]['address']) . ' ต.' . trim($getDetailMarket[0]['district_name']) . ' อ.' . trim($getDetailMarket[0]['amphure_name']) . ' จ.' . trim($getDetailMarket[0]['provice_name']) . ' ' . trim($getDetailMarket[0]['zipcode']) . ' | ' . trim($getDetailMarket[0]['sn_ref'])
                                      ],
                        'boxes' => $getDetailMarket[0]['number_of_package'],
                        'totalquantity' => $totalquantity,
                        'weight' => $getDetailMarket[0]['weight']
                        // 'items' => ['itemname' => '',
                        //             'itemvalue' => '',
                        //             'desc' => '',
                        //             'number' => ''
                        //            ]
                      ];

          $textTel = $getDetailMarket[0]['phone'];
          $cTextTel = $this->cleanInt($textTel);

          if(strlen($cTextTel) == 9){
            $cTextTel = '0' . $cTextTel;
          }

          if(strlen($cTextTel) < 9){
            // k.ศริญญา เกตุเขียว default tel
            $cTextTel = '0657174931';
          }

          $tel1 = substr($cTextTel, 0, 10);
          $tel2 = substr($cTextTel,10, 10);
          $tel3 = substr($cTextTel, 20);

          if($tel1){
            $json_data['receiver']['phone'] = $tel1;
            $json_data['receiver']['mobile'] = $tel1;
          }

          if($tel2){
            $json_data['receiver']['mobile'] = $tel2;
          }

          $temp_array_item = [];
          foreach ($product_details as $key_pd => $val_pd) {
            array_push($temp_array_item, ['itemname' => $val_pd['good_code'] . ' ' . $val_pd['good_color'], 'desc' => $val_pd['good_name'] . ' ' . $val_pd['good_color'], 'itemvalue' => 0, 'number' => ($key_pd+1)]);
          }

          $json_data['items'] = $temp_array_item;

          $json_data_encode = json_encode($json_data,JSON_UNESCAPED_UNICODE);

          $response = $this->curlSendAPIJNT($json_data_encode);

          $response_data = json_decode($response);

          $status = null;
          $status_code = null;
          $remark = null;

          if(isset($response_data->responseitems[0])){
            $response_value = $response_data->responseitems[0];

            // if(!$response_value->success || strtolower($response_value->success) == 'false'){

            //   if(isset($response_value->reason)){
            //     $status_code = $response_value->reason;
            //   }

            //   if(is_null($status_code)){

            //     $created_date_temp = '0000-00-00 00:00:00';

            //     $textRemark = $created_date . ' | not find status code';

            //     if($key['remark'] == ''){
            //       $remark = $textRemark;
            //     }else{
            //       $remark = $key['remark'] . ',' . $textRemark;
            //     }

            //   }else{

            //     $created_date_temp = '0000-00-00 00:00:00';

            //     $textRemark = $created_date . ' | ' . $status_code;

            //     if($key['remark'] == ''){
            //       $remark = $textRemark;
            //     }else{
            //       $remark = $key['remark'] . ',' . $textRemark;
            //     }

            //   }

            // }else{
            //   //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
            //   $status = 7;
            //   $status_code = '000';

            //   $textRemark = $created_date . ' | ' . $response;

            //   if($key['remark'] == ''){
            //     $remark = $textRemark;
            //   }else{
            //     $remark = $key['remark'] . ',' . $textRemark;
            //   }

            //   $KT_update = array(
            //     'send_date' => $created_date,
            //     'status' => $status,
            //     'status_code' => $status_code,
            //     'remark' => $remark,
            //     'send_log' => $json_data_encode
            //   );

            // }

            if(isset($response_value->success) && strtolower($response_value->success) == 'true'){

              //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
              $status = 7;
              $status_code = '000';

              $textRemark = $created_date . ' | ' . $response;

              if($key['remark'] == ''){
                $remark = $textRemark;
              }else{
                $remark = $key['remark'] . ',' . $textRemark;
              }

              $KT_update = array(
                'send_date' => $created_date,
                'status' => $status,
                'status_code' => $status_code,
                'remark' => $remark,
                'send_log' => $json_data_encode
              );

            }else{

              if(isset($response_value->reason)){
                $status_code = $response_value->reason;
              }

              if(is_null($status_code)){

                $created_date_temp = '0000-00-00 00:00:00';

                $textRemark = $created_date . ' | not find status code';

                if($key['remark'] == ''){
                  $remark = $textRemark;
                }else{
                  $remark = $key['remark'] . ',' . $textRemark;
                }

              }else{

                $created_date_temp = '0000-00-00 00:00:00';

                $textRemark = $created_date . ' | ' . $status_code;

                if($key['remark'] == ''){
                  $remark = $textRemark;
                }else{
                  $remark = $key['remark'] . ',' . $textRemark;
                }

              }

            }

          }

          if($status != 7){

            $status = $this->changeStatusError($key['status']);

            if(is_null($remark)){
              $KT_update = array(
                'send_date' => $created_date,
                'status' => $status,
                'status_code' => $status_code,
                'send_log' => $json_data_encode
                );
            }else{
              $KT_update = array(
                'send_date' => $created_date,
                'status' => $status,
                'status_code' => $status_code,
                'remark' => $remark,
                'send_log' => $json_data_encode
                );
            }

          }

          $KT_where = [];
          $KT_where[] = $QKT->getAdapter()->quoteInto('sn = ?', $sn);
          $KT_where[] = $QKT->getAdapter()->quoteInto('type = ?', '1');
          $KT_where[] = $QKT->getAdapter()->quoteInto('delivery_type = ?', $delivery_type);
          $QKT->update($KT_update, $KT_where);

          break;
      }

    }

  }

  public function addRollBack($getDetailMarket, $created_date){

    $QKTR = new Application_Model_KerryTransactionRollback();

    $ro_sn = $getDetailMarket[0]['sn'];
    $ro_post_json_data = json_encode($getDetailMarket, JSON_UNESCAPED_UNICODE);
    $ro_created_date = $created_date;
    $ro_modified_date = '0000-00-00 00:00:00';
    $ro_status = '';
    $ro_status_cdoe = null;
    $ro_remark = null;

    $kerryTransactionRollback = array(
      'sn'             => $ro_sn,
      'data'           => $ro_post_json_data,
      'created_date'   => $ro_created_date,
      'modified_date'  => $ro_modified_date,
      'status'         => $ro_status,
      'status_code'    => $ro_status_cdoe,
      'remark'         => $ro_remark
      );

    $QKTR->insert($kerryTransactionRollback);

  }

  public function cronrollbackAction(){

    $date = new DateTime();
    $created_date = $date->format('Y-m-d H:i:s');

    $QKTR = new Application_Model_KerryTransactionRollback();
    $QKT = new Application_Model_KerryTransaction();

    $getRollBack = $QKTR->getRollBack();

    foreach ($getRollBack as $key) {

      $db = Zend_Registry::get('db');
      $db->beginTransaction();

      try{

            // START : Roll Back
        $response = $this->sendShipmentInfoRollBack(json_decode($key['data'],true));
        $arrResponse = json_decode($response);

        $status = null;
        $status_code = null;
        $remark = null;

        if(isset($arrResponse->res->shipment->status_code)){
          $status_code = $arrResponse->res->shipment->status_code;
        }

        if(is_null($status_code)){

          $textRemark = $created_date . ' | not find status code';

          if($key['remark'] == ''){
            $remark = $textRemark;
          }else{
            $remark = $key['remark'] . ',' . $textRemark;
          }

          $status = $this->changeStatusError($key['status']);

        }else{

          if($status_code != '000'){

            $textRemark = $created_date . ' | ' . $status_code;

            if($key['remark'] == ''){
              $remark = $textRemark;
            }else{
              $remark = $key['remark'] . ',' . $textRemark;
            }

            $status = $this->changeStatusError($key['status']);

          }else{
            //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
            $status = 7;

            $textRemark = $created_date . ' | ' . $status_code;

            if($key['remark'] == ''){
              $remark = $textRemark;
            }else{
              $remark = $key['remark'] . ',' . $textRemark;
            }
          }

        }

        if(is_null($remark)){
          $KTRB_update = array(
            'modified_date' => $created_date,
            'status' => $status,
            'status_code' => $status_code
            );
        }else{
          $KTRB_update = array(
            'modified_date' => $created_date,
            'status' => $status,
            'status_code' => $status_code,
            'remark' => $remark
            );
        }

        $KTRB_where = $QKTR->getAdapter()->quoteInto('id = ?', $key['id']);
        $QKTR->update($KTRB_update, $KTRB_where);

        //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
        if($status == 7){

          //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
          $KT_update = [
          'rollback_status' => 5
          ];

          $KT_where = [];
          $KT_where[] = $QKT->getAdapter()->quoteInto('sn = ?', $key['sn']);
          //0=null,1=add
          $KT_where[] = $QKT->getAdapter()->quoteInto('type = ?', 1);
          $QKT->update($KT_update, $KT_where);
        }else{
          //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
          if($key['status'] == 2){

            //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
            $KT_update = [
            'rollback_status' => 6
            ];

            $KT_where = [];
            $KT_where[] = $QKT->getAdapter()->quoteInto('sn = ?', $key['sn']);
            //0=null,1=add
            $KT_where[] = $QKT->getAdapter()->quoteInto('type = ?', 1);
            $QKT->update($KT_update, $KT_where);
          }
        }

          // END : Roll Back

        $db->commit();

      } catch (Exception $e){
        $db->rollBack();
      }
    }
  }

  public function logindeliveryAction(){
    
    $headers = $this->getHeaders();
    if(!$this->validateHearders($headers)){
      http_response_code(500);
      $result = array(
        "status"   => 1,
        "message" => "Error."
        );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

    $data = $_POST;
    // $data = json_decode(file_get_contents('php://input'), true);

    if(!isset($data['username']) || !$data['username'] || !isset($data['password']) || !$data['password']){
      
      http_response_code(400);
          $result = array(
            "status" => 1,
            "message" => "Pleas check parameter request"
            );

        print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
        exit();
    }

    $QSD = new Application_Model_StaffDelivery();

    $getDataLogin = $QSD->loginStaffDelivery($data['username'], md5($data['password']));

    if($getDataLogin){

      if(isset($getDataLogin['company']) && $getDataLogin['company']){
        $company_name = My_Carrier::get($getDataLogin['company']);
        if(!$company_name){
          $company_name = $getDataLogin['company'];
        }
        $getDataLogin['company_name'] = $company_name;
      }

       http_response_code(200);
        $result = array(
              "status" => 3,
              "message" => "Success",
              "data" => $getDataLogin
              );
        print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
        exit();
    }else{
      http_response_code(200);
      $result = array(
            "status" => 2,
            "message" => "Username or password is invalid"
            );
      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

  }

  public function getdetailbysoAction(){

    $headers = $this->getHeaders();
    if(!$this->validateHearders($headers)){
      http_response_code(500);
      $result = array(
        "status"   => 1,
        "message" => "Error."
        );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

    $data = $_POST;
    // $data = json_decode(file_get_contents('php://input'), true);

    $QMK = new Application_Model_Market();
    $QKL = new Application_Model_KerryShipmentStatusLog();

    if(isset($data['so_ref']) && $data['so_ref'] && isset($data['company']) && $data['company']){

      $sn_ref = $data['so_ref'];
      $company = $data['company'];

      $getDetailBySo = $QMK->getDetailDeliveryBySo($sn_ref, $company);

      $send_date = '';

      if($getDetailBySo){
        $deliveryStatus = 0;
        $partImage = '';
        $checkDeliveryDone = $QKL->getDeliveryDone($sn_ref, $company);

        if($checkDeliveryDone){
          $deliveryStatus = 1;
          if(isset($checkDeliveryDone['image']) && $checkDeliveryDone['image']){
            $partImage = $this->partBucket . $checkDeliveryDone['image'];
          }
          if(isset($checkDeliveryDone['shipment_status_date']) && $checkDeliveryDone['shipment_status_date']){
            $send_date = $checkDeliveryDone['shipment_status_date'];
          }
        }

        $getDetailBySo['image'] = $partImage;
        $getDetailBySo['send_date'] = $send_date;
        $getDetailBySo['delivery_status'] = $deliveryStatus;

      }else{
        $getDetailBySo = [];
      }

      http_response_code(200);
      $result = array(
              "status" => 3,
              "message" => "Success",
              "data" => $getDetailBySo
              );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();

    }else{
      http_response_code(400);
        $result = array(
          "status"   => 1,
          "message" => "Pleas check parameter request"
          );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

  }

  public function getdetailbycoAction(){

    $headers = $this->getHeaders();
    if(!$this->validateHearders($headers)){
      http_response_code(500);
      $result = array(
        "status"   => 1,
        "message" => "Error."
        );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

    $data = $_POST;
    // $data = json_decode(file_get_contents('php://input'), true);

    $QCSO = new Application_Model_ChangeSalesOrder();
    $QKL = new Application_Model_KerryShipmentStatusLog();

    if(isset($data['co']) && $data['co'] && isset($data['company']) && $data['company']){

      $co = $data['co'];
      $company = $data['company'];

      $getDetailBySo = $QCSO->getDetailDeliveryByCo($co);

      $send_date = '';

      if($getDetailBySo){
        $deliveryStatus = 0;
        $partImage = '';
        $checkDeliveryDone = $QKL->getDeliveryDone($co, $company);

        if($checkDeliveryDone){
          $deliveryStatus = 1;
          if(isset($checkDeliveryDone['image']) && $checkDeliveryDone['image']){
            $partImage = $this->partBucket . $checkDeliveryDone['image'];
          }
          if(isset($checkDeliveryDone['shipment_status_date']) && $checkDeliveryDone['shipment_status_date']){
            $send_date = $checkDeliveryDone['shipment_status_date'];
          }
        }

        $getDetailBySo['image'] = $partImage;
        $getDetailBySo['send_date'] = $send_date;
        $getDetailBySo['delivery_status'] = $deliveryStatus;

      }else{
        $getDetailBySo = [];
      }

      http_response_code(200);
      $result = array(
              "status" => 3,
              "message" => "Success",
              "data" => $getDetailBySo
              );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();

    }else{
      http_response_code(400);
        $result = array(
          "status"   => 1,
          "message" => "Pleas check parameter request"
          );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

  }

  public function gethistoryAction(){

    $headers = $this->getHeaders();
    if(!$this->validateHearders($headers)){
      http_response_code(500);
      $result = array(
        "status"   => 1,
        "message" => "Error."
        );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

    $data = $_POST;
    // $data = json_decode(file_get_contents('php://input'), true);

    $QKL = new Application_Model_KerryShipmentStatusLog();

    $date = date("Y-m-d");
    if(isset($data['date']) && $data['date']){
      $date = $data['date'];
    }

    if(isset($data['delivery_add_by']) && $data['delivery_add_by'] && isset($data['company']) && $data['company']){

      $delivery_add_by = $data['delivery_add_by'];
      $company = $data['company'];
      $date = $data['date'];

      $getDeliveryHistory = $QKL->getDeliveryHistory($delivery_add_by, $company, $date, $this->partBucket);

      $getDeliveryHistoryCO = $QKL->getDeliveryHistoryCO($delivery_add_by, $company, $date, $this->partBucket);

      if(!$getDeliveryHistory and !$getDeliveryHistoryCO){
          $getDeliveryHistory = [];
      }

      $getDeliveryHistory = array_merge($getDeliveryHistory,$getDeliveryHistoryCO);

      http_response_code(200);
      $result = array(
              "status" => 3,
              "message" => "Success",
              "data" => $getDeliveryHistory
              );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();

    }else{
      http_response_code(400);
        $result = array(
          "status"   => 1,
          "message" => "Pleas check parameter request"
          );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

  }

  public function postdetailAction(){

    $headers = $this->getHeaders();
    if(!$this->validateHearders($headers)){
      http_response_code(500);
      $result = array(
        "status"   => 1,
        "message" => "Error."
        );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

    $data = $_POST;
    // $data = json_decode(file_get_contents('php://input'), true);

    $QMK = new Application_Model_Market();
    $QKL = new Application_Model_KerryShipmentStatusLog();

    if(isset($data['tracking_no']) && $data['tracking_no'] && isset($data['so_ref']) && $data['so_ref'] && isset($data['company']) && $data['company'] && isset($data['send_date']) && $data['send_date'] && isset($data['delivery_add_by']) && $data['delivery_add_by']){

      http_response_code(200);
      $con_no = $data['tracking_no'];
      $sn_ref = $data['so_ref'];
      $company = $data['company'];
      $status_date = $data['send_date'];
      $delivery_add_by = $data['delivery_add_by'];

      $fieldFileName = 'image';

      //check upload image
      if(!$this->isset_file($_FILES[$fieldFileName])) {
          $result = array(
            "status"   => 2,
            "message" => "Please choose image."
          );
          print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
          exit();
      }else{
        $partImage = 'upload/img_delivery/';
        $taille_maxi = 100000;
        $taille      = filesize($_FILES[$fieldFileName]['tmp_name']);
        $extensions  = array('.png', '.jpg', '.jpeg');
        $extension   = strrchr($_FILES[$fieldFileName]['name'], '.');

        if(!in_array($extension, $extensions)) {
            $errorMessage = 'ERROR you must upload the right type';
        }

        // if($taille>$taille_maxi) {
        //      $errorMessage = 'too heavy';
        // }

        if(!empty($errorMessage)) {
          $result = array(
            "status"   => 2,
            "message" => $errorMessage
          );
          print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
          exit();
        }
      }

      $checkDeliveryDone = $QKL->getDeliveryDone($sn_ref, $company);

      if($checkDeliveryDone){
        $result = array(
          "status"   => 1,
          "message" => "Error. This Delivery Completed"
        );
        print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
        exit();
      }

      $image = $sn_ref . '_' . $company . '_' . time() . $extension;
      $file = $partImage . $image;

      if (move_uploaded_file($_FILES[$fieldFileName]['tmp_name'], $file)) {

            if(!$this->updateDelivery($con_no, $sn_ref, $status_date, $company, $image, $delivery_add_by)){
              $result = array(
                "status"   => 1,
                "message" => "Error. Can not update data"
              );
              print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
              exit();
            }
            
            $getDetailBySo = $QMK->getDetailDeliveryBySo($sn_ref, $company);

            if($getDetailBySo){
              $deliveryStatus = 0;
              $partImage = '';
              $checkDeliveryDone = $QKL->getDeliveryDone($sn_ref, $company);

              if($checkDeliveryDone){
                $deliveryStatus = 1;
                if(isset($checkDeliveryDone['image']) && $checkDeliveryDone['image']){
                  $partImage = $this->partBucket . $checkDeliveryDone['image'];
                }
              }

              $getDetailBySo['delivery_status'] = $deliveryStatus;
              $getDetailBySo['image'] = $partImage;
            }else{
              $getDetailBySo = [];
            }

            $result = array(
              "status" => 3,
              "message" => "Success",
              "data" => $getDetailBySo
              );

            print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
            exit();

        } else {
            $result = array(
              "status"   => 1,
              "message" => "Error. Can not upload"
            );
            print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
            exit();
        }

    }else{
      http_response_code(400);
        $result = array(
          "status"   => 1,
          "message" => "Pleas check parameter request"
          );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

  }

  public function canceldeliveryAction(){

    $headers = $this->getHeaders();
    if(!$this->validateHearders($headers)){
      http_response_code(500);
      $result = array(
        "status"   => 1,
        "message" => "Error."
        );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

    $data = $_POST;
    // $data = json_decode(file_get_contents('php://input'), true);

    $QMK = new Application_Model_Market();
    $QKL = new Application_Model_KerryShipmentStatusLog();

    if(isset($data['tracking_no']) && $data['tracking_no'] && isset($data['so_ref']) && $data['so_ref'] && isset($data['company']) && $data['company'] && isset($data['delivery_add_by']) && $data['delivery_add_by']){

      http_response_code(200);
      $tracking_no = $data['tracking_no'];
      $sn_ref = $data['so_ref'];
      $company = $data['company'];
      $delivery_add_by = $data['delivery_add_by'];

      $checkDeliveryDone = $QKL->getDeliveryDone($sn_ref, $company);

      if(!$checkDeliveryDone){
        $result = array(
          "status"   => 1,
          "message" => "Error. This Delivery Not Complete"
        );
        print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
        exit();
      }

      $db = Zend_Registry::get('db');
      $db->beginTransaction();

      $QKSSL = new Application_Model_KerryShipmentStatusLog();
      $QDO = new Application_Model_DeliveryOrder();

      $date = new DateTime();
      $created_date = $date->format('Y-m-d H:i:s');
      $created_date_timestamp = time();

      $status_code = 'POD';

      try {

        // cancel Log of Shipment Status
        $shipment_log = array(
          'shipment_status_id'  => $status_code . '|' . $delivery_add_by . '|' . $created_date_timestamp
          );

        $where_shipment_log = [];

        $where_shipment_log[] = $QKSSL->getAdapter()->quoteInto('tracking_no = ?', $tracking_no);
        $where_shipment_log[] = $QKSSL->getAdapter()->quoteInto('sn_ref = ?', $sn_ref);
        $where_shipment_log[] = $QKSSL->getAdapter()->quoteInto('company = ?', $company);
        $where_shipment_log[] = $QKSSL->getAdapter()->quoteInto('delivery_add_by = ?', $delivery_add_by);

        $QKSSL->update($shipment_log, $where_shipment_log);

        // update Status of Delivery Order
        $DO_update = array(
          'real_receiver'     => null,
          'real_receive_time' => null
          );

        $DO_where = $QDO->getAdapter()->quoteInto('tracking_no = ?', $tracking_no);

        $QDO->update($DO_update, $DO_where);

        $QIDR = new Application_Model_ImeiDistributorReceive();

        $imei_update = array(
          'shipment_status_id' => $status_code . '|' . $delivery_add_by . '|' . $created_date_timestamp
              );

        $QIDR_where = [];

        $QIDR_where[] = $QDO->getAdapter()->quoteInto('sn_ref = ?', $sn_ref);
        $QIDR_where[] = $QDO->getAdapter()->quoteInto('tracking_no = ?', $tracking_no);
        $QIDR_where[] = $QDO->getAdapter()->quoteInto('shipment_status_id = ?', $status_code);

        $QIDR->update($imei_update, $QIDR_where);

        $db->commit();

        $result = array(
          "status"   => 3,
          "message" => "Success"
          );

        print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
        exit();

      } catch (Exception $e) {

        $db->rollBack();

        http_response_code(400);
        $result = array(
          "status"   => 1,
          "message" => "Can not cancel delivery order"
          );

        print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
        exit();

      }

    }else{
      http_response_code(400);
        $result = array(
          "status"   => 1,
          "message" => "Pleas check parameter request"
          );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

  }

  public function updateDelivery($con_no, $ref_no, $status_date, $company, $image, $delivery_add_by) {

    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    $QKSSL = new Application_Model_KerryShipmentStatusLog();
    $QDO = new Application_Model_DeliveryOrder();

    $date = new DateTime();
    $created_date = $date->format('Y-m-d H:i:s');

    $status_code = 'POD';

    try {

      $flogfrom = 1;//from app

      // insert Log of Shipment Status
      $shipment_log = array(
        'tracking_no'           => $con_no,
        'shipment_status_id'    => $status_code,
        'shipment_status_date'  => $status_date,
        'sn_ref'                => $ref_no,
        'created_date'          => $created_date,
        'company'               => $company,
        'image'                 => $image,
        'delivery_add_by'       => $delivery_add_by,
        'from'                  => $flogfrom
        );

      $QKSSL->insert($shipment_log);

      // update Status of Delivery Order
      $DO_update = array(
        'real_receiver'     => $status_code,
        'real_receive_time' => $status_date
        );

      $DO_where = $QDO->getAdapter()->quoteInto('tracking_no = ?', $con_no);

      $QDO->update($DO_update, $DO_where);

      // insert Log of Imei Receive
      $QIDR = new Application_Model_ImeiDistributorReceive();

      $imei_list = $QIDR->getImeiBySN($ref_no);

      for ($i=0;$i<count($imei_list);$i++) {

        $imei_log = array(
          'imei_sn'               => $imei_list[$i]['imei_sn'],
          'tracking_no'           => $con_no,
          'shipment_status_id'    => $status_code,
          'shipment_status_date'  => $status_date,
          'sn_ref'                => $ref_no
          );

        //print_r($imei_log); echo $i; echo "<br/>";
        $QIDR->insert($imei_log);

      }

      $db->commit();

    } catch (Exception $e) {

      $db->rollBack();
      //echo "Fail! : ".$e;

      return false;

    }

    return true;

  }

  public function dashboarddeliveryAction(){

    $headers = $this->getHeaders();
    if(!$this->validateHearders($headers)){
      http_response_code(500);
      $result = array(
        "status"   => 1,
        "message" => "Error."
        );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

    $data = $_POST;
    // $data = json_decode(file_get_contents('php://input'), true);

    // $data['id'] = '3';
    // $data['company'] = '5';

    $QDS = new Application_Model_DeliverySales();
    $QSD = new Application_Model_StaffDelivery();
    $QKSSL = new Application_Model_KerryShipmentStatusLog();

    if(isset($data['id']) && $data['id'] && isset($data['company']) && $data['company']){

      $id = $data['id'];
      $company = $data['company'];

      if(!$QSD->checkAdminDelivery($id, $company)){
        http_response_code(400);
        $result = array(
          "status"   => 1,
          "message" => "permission denial not allowed"
          );

        print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
        exit();
      }

      $getDashboardDelivery = $QDS->getDashboardDelivery($company);

      if(!$getDashboardDelivery){
          $getDashboardDelivery = [];
      }

      $getTotalPODDeliveryByAccount = $QDS->getTotalPODDeliveryByAccount($company);

      if(!$getTotalPODDeliveryByAccount){
          $getTotalPODDeliveryByAccount = [];
      }

      $getTotalCO = $QKSSL->getTotalCO($company);
      if($getTotalCO){
        if(!$getDashboardDelivery){
          $getDashboardDelivery = array(
                                    'total' => $getTotalCO['count_co'],
                                    'today' => $getTotalCO['count_co'],
                                    'pending' => 0
                                  );
        }else{
          $getDashboardDelivery['today'] += $getTotalCO['count_co'];
          $getDashboardDelivery['total'] += $getTotalCO['count_co'];
        }
      }

      $getTotalDetailCO = $QKSSL->getTotalDetailCO($company);
      foreach ($getTotalDetailCO as $key => $value) {
        $count_data = 0;
        foreach ($getTotalPODDeliveryByAccount as $key_sub => $value_sub) {
          if($value['id'] == $value_sub['id']){
            $count_data++;
            $getTotalPODDeliveryByAccount[$key_sub]['pod_total'] += $value['pod_total'];
          }
        }
        if($count_data == 0){
          array_push($getTotalPODDeliveryByAccount, $value);
        }
      }

      http_response_code(200);
      $result = array(
              "status" => 3,
              "message" => "Success",
              "data" => ['heads' => $getDashboardDelivery, 'body' => $getTotalPODDeliveryByAccount]
              );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();

    }else{
      http_response_code(400);
        $result = array(
          "status"   => 1,
          "message" => "Pleas check parameter request"
          );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

  }

  public function getsopendingtodayAction(){

    $headers = $this->getHeaders();
    if(!$this->validateHearders($headers)){
      http_response_code(500);
      $result = array(
        "status"   => 1,
        "message" => "Error."
        );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

    $data = $_POST;
    // $data = json_decode(file_get_contents('php://input'), true);

    $QDS = new Application_Model_DeliverySales();
    $QSD = new Application_Model_StaffDelivery();

    if(isset($data['id']) && $data['id'] && isset($data['company']) && $data['company']){

      $id = $data['id'];
      $company = $data['company'];

      if(!$QSD->checkAdminDelivery($id, $company)){
        http_response_code(400);
        $result = array(
          "status"   => 1,
          "message" => "permission denial not allowed"
          );

        print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
        exit();
      }

      $getSoPendingDelivery = $QDS->getSoPendingDelivery($company);

      if(!$getSoPendingDelivery){
          $getSoPendingDelivery = [];
      }

      http_response_code(200);
      $result = array(
              "status" => 3,
              "message" => "Success",
              "data" => $getSoPendingDelivery
              );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();

    }else{
      http_response_code(400);
        $result = array(
          "status"   => 1,
          "message" => "Pleas check parameter request"
          );

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }

  }


  public function curlSendAPIJNT($json_data){

    $url = JNT_API_URL;

    $url_create = $url . 'oppo/order/createOrder.do';

    $data_digest = $this->genDataDigestJAndT($json_data);

    $post_date = [
    'logistics_interface' => $json_data,
    'data_digest' => $data_digest,
    'eccompanyid' => JNT_ECCOMPANYID,
    'msg_type' => 'ORDERCREATE'
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

  // ------------------------------- Function tools ------------------------

  public function genDataDigestJAndT($json_data){
    $key = JNT_KEY;
    $endcode_md5 = md5($json_data.$key);
    $endcode_base64 = base64_encode(strtolower($endcode_md5));
    return $endcode_base64;
  } 

  public function validateRequest($data){

    if(!isset($data['req']['status']) || !is_array($data['req']['status']) || count($data['req']['status']) < 1){

      $result = array(
        "Message" => "The requested resource does not support http method 'GET'."
        );

      http_response_code(405);

      print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
      exit();
    }
  }

  public function getHeaders(){

    $headers = array();
    foreach ($_SERVER as $key => $value) {
      if (strpos($key, 'HTTP_') === 0) {
        $headers[str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
      }
    }

    return $headers;
  }

  public function validateHearders($headers){

    $fixKey_kerry = KERRY_API_UPDATESTATUS;
    $fixKey_JAndT = JNT_API_UPDATESTATUS;

    if(is_array($headers)){
      if(isset($headers['Privatekey'])){
        // if($headers['Privatekey'] == $fixKey_kerry){
        if(in_array($headers['Privatekey'], [$fixKey_kerry,$fixKey_JAndT])){
          return true;
        }else{
          http_response_code(401);
          $result = array(
            "Message" => "Error key,Please check."
            );
        }
      }else{
        http_response_code(401);
        $result = array(
          "Message" => "Error Headers,Please check."
          );
      }
    }else{
      http_response_code(401);
      $result = array(
        "Message" => "Error Headers,Please check."
        );
    }

    print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
    exit();
  }

  public function changeStatusError($error){

    //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done

    $status = 0;

    switch ($error) {
      case 0:
      $status = 1;
      break;
      case 1:
      $status = 2;
      break;
      case 2:
      $status = 3;
      break;
    }
    return $status;
  }

  public function isset_file($file) {
    return (isset($file) && $file['error'] != UPLOAD_ERR_NO_FILE);
  }

}