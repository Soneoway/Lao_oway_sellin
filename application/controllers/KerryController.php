<?php

class KerryController extends My_Controller_Action
{

  public function reportAction()
  {
    //auto refresh
    // print_r(111);
    $this->view->meta_refresh = 300;

    $sort               = $this->getRequest()->getParam('sort', 'p.outmysql_time');
    $desc               = $this->getRequest()->getParam('desc', 0);
    $page               = $this->getRequest()->getParam('page', 1);

    $sn                 = $this->getRequest()->getParam('sn');
    $tracking_no          = $this->getRequest()->getParam('tracking_no');
    $d_id               = $this->getRequest()->getParam('d_id');
    $good_id            = $this->getRequest()->getParam('good_id');
    $good_color         = $this->getRequest()->getParam('good_color');
    $num                = $this->getRequest()->getParam('num');
    $price              = $this->getRequest()->getParam('price');
    $created_at_to      = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));
    $created_at_from    = $this->getRequest()->getParam('created_at_from', date('d/m/Y'));
    $send_at_to         = $this->getRequest()->getParam('send_at_to');
    $send_at_from       = $this->getRequest()->getParam('send_at_from');
    $cat_id             = $this->getRequest()->getParam('cat_id');
    $warehouse_id       = $this->getRequest()->getParam('warehouse_id');
    $export             = $this->getRequest()->getParam('export', 0);
    $export_distributor = $this->getRequest()->getParam('export_distributor', 0);
    $export_warehouse   = $this->getRequest()->getParam('export_warehouse', 0);
    $tags               = $this->getRequest()->getParam('tags');
    $no_show_brandshop  = $this->getRequest()->getParam('no_show_brandshop', 0);
    $show_kerry  = $this->getRequest()->getParam('show_kerry', 0);
    $show_kerry_status  = $this->getRequest()->getParam('show_kerry_status', 3);


    $company_logistics  = $this->getRequest()->getParam('company_logistics');
    $logistics_status  = $this->getRequest()->getParam('logistics_status');

    $rank           = $this->getRequest()->getParam('rank');
    $this->view->rank = $rank;
    $this->view->d_id = $d_id;

    $this->view->part_image = HOST.'upload/img_delivery/';

    $limit = LIMITATION;
    $total = 0;

    if ($tags and is_array($tags))
      $tags = $tags;
    else
      $tags = null;

    $params = array_filter(array(
      'sn'                  => $sn,
      'tracking_no'         => $tracking_no,
      'd_id'                => $d_id,
      'good_id'             => $good_id,
      'good_color'          => $good_color,
      'num'                 => $num,
      'price'               => $price,
      'total'               => $total,
      'created_at_to'       => $created_at_to,
      'created_at_from'     => $created_at_from,
      'send_at_to'          => $send_at_to,
      'send_at_from'        => $send_at_from,
      'cat_id'              => $cat_id,
      'warehouse_id'        => $warehouse_id,
      'tags'                => $tags,
      'confirm_so'          => 1,                  // check confirm so before finance confirm
      'no_show_brandshop'   => $no_show_brandshop,
      'show_kerry'          => $show_kerry,
      'show_kerry_status'   => $show_kerry_status,
      'company_logistics'   => $company_logistics,
      'logistics_status'    => $logistics_status,
      'rank'                => $rank
      ));

    if ($export_distributor == 1) {
      $this->_export_distributor($params);

      exit;
    }

    if ($export_warehouse == 1) {
      $this->_export_warehouse($params);

      exit;
    }

    $QKR = new Application_Model_KerryReport();

    $QGood          = new Application_Model_Good();
    $QGoodColor     = new Application_Model_GoodColor();
    $QMarket        = new Application_Model_Market();
    $QDistributor   = new Application_Model_Distributor();
    $QGoodCategory  = new Application_Model_GoodCategory();
    $QWarehouse     = new Application_Model_Warehouse();
    $QMarketProduct = new Application_Model_MarketProduct();

    $goods             = $QGood->get_cache();
    $goodColors        = $QGoodColor->get_cache();
    $distributors      = $QDistributor->get_with_store_code_cache();
    $good_categories   = $QGoodCategory->get_cache();
    $warehouses_cached = $QWarehouse->get_cache();

    $params['sort'] = $sort;
    $params['desc'] = $desc;

    if (isset($export) && $export) {
      $markets_sn = $QKR->fetchPagination($page, null, $total, $params);

      $markets_sn_array = $this->markData($markets_sn);

      $this->_exportExcel($markets_sn_array);
    }

    $params['get_fields'] = array(
      // 'qnum',
      // 'title',
      // 'send_date',
      // 'status',
      // 'sales_confirm_date',
      // 'outmysql_time',
      // 'add_time',
      // 'finance_confirm_date',
      // 'number_of_package',
      // 'weight',
      // 'sn',
      // 'sn_ref',
      // 'contact_name',
      // 'address',
      // 'district_name',
      // 'amphure_name',
      // 'provice_id',
      // 'provice_name',
      // 'zipcode',
      // 'phone'
      );

    // $markets_sn = $QKR->getKerryReport();
    $markets_sn = $QKR->fetchPagination($page, $limit, $total, $params);

    $markets_sn_array = $this->markData($markets_sn);

    $markets = array();

    $params['sn'] = $sn;
    $params['tracking_no'] = $tracking_no;

    $this->view->goods             = $goods;
    $this->view->goodColors        = $goodColors;
    $this->view->markets           = $markets;
    $this->view->markets_sn        = $markets_sn_array;
    $this->view->distributors      = $distributors;
    $this->view->good_categories   = $good_categories;
    $this->view->warehouses_cached = $warehouses_cached;

    $this->view->desc   = $desc;
    $this->view->sort   = $sort;
    $this->view->params = $params;
    $this->view->limit  = $limit;
    $this->view->total  = $total;
    $this->view->no_show_brandshop = $no_show_brandshop;
    $this->view->show_kerry = $show_kerry;
    $this->view->show_kerry_status = $show_kerry_status;
    $this->view->url    = HOST . 'kerry/report/' . ($params ? '?' . http_build_query($params) .
      '&' : '?');

    $this->view->offset = $limit * ($page - 1);

    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;

    $messages_success = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages_success = $messages_success;

    if ($this->getRequest()->isXmlHttpRequest()) {
      $this->_helper->layout->disableLayout();

      $this->_helper->viewRenderer->setRender('partials/list');
    }
  }

  private function _exportExcel($data)
  {

    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();

    $heads = array(
      'No.',
      'Tracking No',
      'Sale Order Reference',
      'Number of Package',
      'Weight',
      'Product Name',
      'Product Color',
      'Sales Quantity',
      'Retailer Name',
      'Send Time',
      'Send Status',
      'Receive Date',
      'Receive Status',
      'Order Time',
      'Sales Confirm Date',
      'Finance Confirm Date',
      'Out of Warehouse Time',
      'Company Logistics',
      'Contact Name',
      'Phone',
      'Address',
      'District Name',
      'Amphure Name',
      'Provice Name',
      'Zip Code'
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


    $QGood = new Application_Model_Good();
    $QGoodColor = new Application_Model_GoodColor();
    $QMarket = new Application_Model_Market();
    $QDistributor = new Application_Model_Distributor();
    $QGoodCategory = new Application_Model_GoodCategory();
    $QWarehouse = new Application_Model_Warehouse();

    $goods = $QGood->get_cache();
    $goodColors = $QGoodColor->get_cache();
    $distributors = $QDistributor->get_cache();
        /*$good_categories = $QGoodCategory->get_cache();
        $warehouses_cached = $QWarehouse->get_cache();*/

        $i = 1;
        $markets = array();

        foreach ($data as $key => $m) {
          $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
          $markets[$m['sn']] = $QMarket->fetchAll($where);
        }

        foreach ($data as $item) {
          $alpha = 'A';
          $sheet->setCellValue($alpha++ . $index, $i++);
          $sheet->getCell($alpha++ . $index)->setValueExplicit($item['tracking_no'] ?: '-',
            PHPExcel_Cell_DataType::TYPE_STRING);
          $sheet->getCell($alpha++ . $index)->setValueExplicit($item['sn_ref'] ?: '-',
            PHPExcel_Cell_DataType::TYPE_STRING);
          $sheet->setCellValue($alpha++ . $index, $item['number_of_package'] ?: '-');
          $sheet->setCellValue($alpha++ . $index, $item['weight'] ?: '-');
          $sheet->setCellValue($alpha++ . $index, '');
          $sheet->setCellValue($alpha++ . $index, '');
          $sheet->setCellValue($alpha++ . $index, $item['qnum'] ?: '-');
          $sheet->setCellValue($alpha++ . $index, $item['title'] ?: '-');

          // if(in_array($item['company'], [My_Carrier::Genious,My_Carrier::NKC])){
          if(in_array($item['company'], [My_Carrier::NKC])){

            if(in_array($item['created_at'], ['','0000-00-00 00:00:00'])){
                $sheet->setCellValue($alpha++ . $index, '-');
            }else{
                $sheet->setCellValue($alpha++ . $index, $item['created_at']);
            }

            if ($item['created_at'] > 1){
              $sheet->setCellValue($alpha++ . $index, 'Done');
            }else{
              $sheet->setCellValue($alpha++ . $index, '-');
            }

          }else{

            if(in_array($item['send_date'], ['','0000-00-00 00:00:00'])){
              $sheet->setCellValue($alpha++ . $index, '-');
            }else{
              $sheet->setCellValue($alpha++ . $index, $item['send_date']);
            }

            $sheet->setCellValue($alpha++ . $index, $item['status'] ?: '-');
          }

          if(in_array($item['receive_created_date'], ['','0000-00-00 00:00:00'])){
            $sheet->setCellValue($alpha++ . $index, '-');
          }else{
            $sheet->setCellValue($alpha++ . $index, $item['receive_created_date']);
          }

          $sheet->setCellValue($alpha++ . $index, $item['kerry_status_code'] ?: '-');

          if(in_array($item['add_time'], ['','0000-00-00 00:00:00'])){
            $sheet->setCellValue($alpha++ . $index, '-');
          }else{
            $sheet->setCellValue($alpha++ . $index, $item['add_time'] ?: '-');
          }

          if(in_array($item['sales_confirm_date'], ['','0000-00-00 00:00:00'])){
            $sheet->setCellValue($alpha++ . $index, '-');
          }else{
            $sheet->setCellValue($alpha++ . $index, $item['sales_confirm_date'] ?: '-');
          }

          if(in_array($item['finance_confirm_date'], ['','0000-00-00 00:00:00'])){
            $sheet->setCellValue($alpha++ . $index, '-');
          }else{
            $sheet->setCellValue($alpha++ . $index, $item['finance_confirm_date'] ?: '-');
          }

          if(in_array($item['outmysql_time'], ['','0000-00-00 00:00:00'])){
            $sheet->setCellValue($alpha++ . $index, '-');
          }else{
            $sheet->setCellValue($alpha++ . $index, $item['outmysql_time'] ?: '-');
          }

          $sheet->setCellValue($alpha++ . $index, $item['company_logistics'] ?: '-');

          $sheet->setCellValue($alpha++ . $index, $item['contact_name'] ?: '-');
          $sheet->getCell($alpha++ . $index)->setValueExplicit($item['phone'] ?: '-',
            PHPExcel_Cell_DataType::TYPE_STRING);
          $sheet->setCellValue($alpha++ . $index, $item['address'] ?: '-');
          $sheet->setCellValue($alpha++ . $index, $item['district_name'] ?: '-');
          $sheet->setCellValue($alpha++ . $index, $item['amphure_name'] ?: '-');
          $sheet->setCellValue($alpha++ . $index, $item['provice_name'] ?: '-');
          $sheet->setCellValue($alpha++ . $index, $item['zipcode'] ?: '-');
          $index++;

          foreach ($markets[$item['sn']] as $key => $value) {
            $alpha = 'A';
            $sheet->setCellValue($alpha++ . $index, $i++);

            if (isset($goods) && isset($goods[$value['good_id']]))
              $good_name = $goods[$value['good_id']];
            if (isset($goodColors) && isset($goodColors[$value['good_color']]))
              $good_color = $goodColors[$value['good_color']];

            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, $good_name);
            $sheet->setCellValue($alpha++ . $index, $good_color);
            $sheet->setCellValue($alpha++ . $index, $value['num']);
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $sheet->setCellValue($alpha++ . $index, '');
            $index++;
          }
        }

        $filename = 'Warehouse_Shipment_Info_Report_' . date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');

        exit;
      }

      public function markData($markets_sn){

        $markets_sn_array = array();

        foreach($markets_sn as $k => $v)
        {
          $markets_sn_array[$k] = $v;
          $markets_sn_array[$k]['status'] = $this->convertStatusKerry($v['status'],$v['status_code']);
          $markets_sn_array[$k]['is_kerry'] = $this->convertStatusIsKerry($v['is_kerry']);
          $markets_sn_array[$k]['kerry_status_code'] = $this->convertStatusKerry($v['kerry_status_code']);
        }

        return $markets_sn_array;

      }

      public function holidayAction(){


      }

      public function getholidayAction(){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $QKHC = new Application_Model_KerryHolidayCondition();

        $getYear = $this->getRequest()->getParam('year', date('Y'));

        $getArrHoliday = $QKHC->getKerryHolidayByYear($getYear);

        $dataRetrun = [];

        foreach ($getArrHoliday as $key) {
          array_push($dataRetrun, [
          'startDate' => strtotime($key['day_holiday']) * 1000,
          'endDate' => strtotime($key['day_holiday']) * 1000,
          'color' => 'red'
          ]);
        }

        print_r(json_encode(['success' => 1, 'result' => $dataRetrun]));

      }

      public function addholidayAction(){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $QKHC = new Application_Model_KerryHolidayCondition();
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $getSaveDate = $this->getRequest()->getParam('saveDate');

        if(is_null($getSaveDate)){
          print_r(json_encode(['success' => 404]));
          exit();
        }

        $QKHCData = [
                'user_id'       => $userStorage->id,
                'day_holiday'   => $getSaveDate,
                'created_date'  => date("Y-m-d H:i:s"),
                'status'        => 0,
                ];

                $QKHC->insert($QKHCData);

        print_r(json_encode(['success' => 1]));

      }

      public function cancelholidayAction(){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $QKHC = new Application_Model_KerryHolidayCondition();
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $getCancelDate = $this->getRequest()->getParam('saveDate');

        if(is_null($getCancelDate)){
          print_r(json_encode(['success' => 404]));
          exit();
        }

        $QKHCData = [
                  'user_id' => $userStorage->id,
                  'status'  => 1  
                  ];


        $where = $QKHC->getAdapter()->quoteInto('day_holiday = ?', $getCancelDate);

        $QKHC->update($QKHCData, $where);
 
        print_r(json_encode(['success' => 1]));

      }

      public function convertStatusKerry($status, $message = ''){

        $returnStatus = '-';

        switch ($status) {
          case '0':
          $returnStatus = '-';
          break;
          case '1':
          $returnStatus = 'Retry 1 (' . $this->convert_status_error($message) . ')';
          break;
          case '2':
          $returnStatus = 'Retry 2 (' . $this->convert_status_error($message) . ')';
          break;
          case '3':
          $returnStatus = 'Retry 3 (' . $this->convert_status_error($message) . ')';
          break;
          case '4':
          $returnStatus = 'Hold';
          break;
          case '5':
          $returnStatus = 'Rollback';
          break;
          case '6':
          $returnStatus = 'Not Rollback';
          break;
          case '7':
          $returnStatus = 'Done';
          break;
          case '010':
          $returnStatus = 'Shipment picked up';
          break;
          case '010.1':
          $returnStatus = 'Shipment picked up';
          break;
          case '045':
          $returnStatus = 'Out for delivery';
          break;
          case '045.1':
          $returnStatus = 'Out for delivery';
          break;
          case '060.01':
          $returnStatus = 'Delivery unsuccessful due to Wrong Address';
          break;
          case '060.02':
          $returnStatus = 'Delivery unsuccessful due to Cannot contact via phone';
          break;
          case '060.03':
          $returnStatus = 'Delivery unsuccessful due to Consignee refused the package';
          break;
          case '060.04':
          $returnStatus = 'Delivery unsuccessful due to Customer not in/home, office closed';
          break;
          case '060.05':
          $returnStatus = 'Delivery unsuccessful due to Package damaged';
          break;
          case '060.06':
          $returnStatus = 'Delivery unsuccessful due to Consignee asked to postpone delivery';
          break;
          case '060.07':
          $returnStatus = 'Delivery unsuccessful due to Consignee refused to pay COD';
          break;
          case '060.08':
          $returnStatus = 'Delivery unsuccessful due to change address';
          break;
          case '060.99':
          $returnStatus = 'Delivery unsuccessful, pending for action';
          break;
          case '090':
          $returnStatus = 'On the way to new address';
          break;
          case '091':
          $returnStatus = 'On the way back to shipper';
          break;
          case '101':
          $returnStatus = 'Arrived at origin station';
          break;
          case '101.1':
          $returnStatus = 'Arrived at origin station';
          break;
          case '102':
          $returnStatus = 'Arrived at Hub/Transit station';
          break;
          case '102.1':
          $returnStatus = 'Arrived at Hub/Transit station';
          break;
          case '103':
          $returnStatus = 'Arrived at destination station';
          break;
          case '103.1':
          $returnStatus = 'Arrived at destination station';
          break;
          case '112':
          $returnStatus = 'Undelivered shipment returns to origin';
          break;
          case 'POD':
          $returnStatus = 'Delivery successfully';
          break;
        }

        return $returnStatus;
      }

      public function convert_status_error($status){

        $returnStatus = $status;

        switch ($status) {
          case '000':
            $returnStatus = 'Success Requisition';
            break;
          case '001':
            $returnStatus = 'Log in fail, invalid app_key or app_id';
            break;
          case '002':
            $returnStatus = 'Duplicate Consignment No, only case action is “A”';
            break;
          case '003':
            $returnStatus = 'Invalid Recipient Zipcode';
            break;
          case '004':
            $returnStatus = 'Invalid Sender Zipcode';
            break;
          case '005':
            $returnStatus = 'Invalid Service Code';
            break;
          case '006':
            $returnStatus = 'Shipment already picked-up, cannot update/delete';
            break;
          case '007':
            $returnStatus = 'Action Code Error';
            break;
          case '100':
            $returnStatus = 'Require Information Parameter';
            break;
          case '999':
            $returnStatus = 'Unsuccessful Requisition / Undefined error exception, return windows exception message';
            break;
        }

        return $returnStatus;
      }

      public function convertStatusIsKerry($status){

        $data = '';
        switch ($status) {
          case '1':
            $data = 'Kerry';
            break;
          case '2':
            $data = 'J&T';
            break;
        }
        return $data;
      }

    }