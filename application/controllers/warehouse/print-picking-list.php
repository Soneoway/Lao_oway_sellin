<?php
$this->_helper->layout->disableLayout();
$sns = $this->getRequest()->getParam('sn');
$sn_ref = $this->getRequest()->getParam('sn_ref');
//echo $sn_ref[0];
if (is_array($sns) && $sns) {
    $sns = array_unique($sns);

    $QMarket       = new Application_Model_Market();
    $QGood         = new Application_Model_Good();
    $QGoodColor    = new Application_Model_GoodColor();
    $QGoodCategory = new Application_Model_GoodCategory();
    $QDistributor  = new Application_Model_Distributor();
    $QStaff        = new Application_Model_Staff();
    $QService      = new Application_Model_Service();
    $QOffice       = new Application_Model_Office();
    $QWarehouse    = new Application_Model_Warehouse();
    $QPrintPickingLog    = new Application_Model_PrintPickingLog();
    
    $staffs = $QStaff->get_cache();
    $this->view->warehouse_cached = $QWarehouse->get_cache();
    $this->view->goods            = $QGood->get_cache();
    $this->view->pname            = $QGood->get_name();
    $this->view->goodColors       = $QGoodColor->get_cache();
    $this->view->good_categories  = $QGoodCategory->get_cache();
    $this->view->distributors     = $QDistributor->get_all_cache();
    $this->view->phone_id         = PHONE_CAT_ID;

    $orders = array();
    $info_data = array();

    $db = Zend_Registry::get('db');

    foreach ($sns as $key => $sn) {
        $tmp = array();

        $params = array(
            'status' => 1,
            'group_sn' => 1,
            'sn' => $sn,
            );

        $limit = LIMITATION;
        $total = 0;
        $page = 1;

        $select = $db->select()
            ->from(array('p' => 'market'),
            array(
                'total_qty' => 'SUM(p.num)',
                'total_price' => 'SUM(p.total)',
                'p.invoice_number',
                'p.type',
                'p.print_picking_list',
                'p.service',
                'p.office',
                'p.add_time',
                'p.sn',
                'p.sn_ref',
                'p.text',
                'p.warehouse_id',
                'p.delivery_address',
                'p.shipping_address',
                'p.customer_name',
            ))
            ->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order'))
            ->join(array('d' => 'distributor'), 'd.id=p.d_id', array('d_id' => 'd.id', 'd.name', 'd.title', 'd.mst_sn', 'd.unames', 'd.store_code', 'd.district','d.add_tax','d.rank'))
            ->where('p.status = ?', 1)
            ->where('p.sn = ?', $sn)
            ->group('p.sn')
            ->limitPage(1, 1);
        ;

        $sales_out = $db->fetchAll($select);
    // echo "<pre>";
    // print_r($sales_out);
        foreach ($sales_out as $k => $v) {


            if (isset($v['service']))
                $service = $v['service'];
            if (isset($v['office']))
                $office = $v['office'];
        }

        $tmp['sales_out'] = $sales_out[0];

        if (!$sales_out)
            exit;

        $this->view->warehouse_id = $sales_out[0]['warehouse_id'];
        $this->view->op_ref = $this->convertSoToOp($sales_out[0]['sn_ref']);
        // if ($sales_out[0]['outmysql_time'])
        //     exit;

        // tính tổng số đã xuất của hóa đơn
        $total_out = $QMarket->count_out_imei($sn);
        $tmp['total_out'] = $total_out;

        // lấy danh sách tất cả sản phẩm trong hóa đơn
        $select = $db->select()->from(array('m' => 'market'),
            array('user_id', 'salesman', 'pay_user', 'shipping_yes_id', 'cat_id', 'good_id', 'good_color', 'num', 'price', 'total', 'text','delivery_address'))
            ->where('sn = ?', $sn)
            ->where('status = ?', 1);

        $sales_list = $db->fetchAll($select);

        $info_data[$sn] = array();

        if (isset($sales_list[0]) && $sales_list[0])
            $sale = $sales_list[0];

        $info_data[$sn]['created_by_name'] = isset($staffs[$sale['user_id']]) ? $staffs[$sale['user_id']] : '';
        //get salesman_name
        $info_data[$sn]['salesman_name'] = isset($staffs[$sale['salesman']]) ? $staffs[$sale['salesman']] : '';
        //get pay_user
        $info_data[$sn]['pay_user_name'] = isset($staffs[$sale['pay_user']]) ? $staffs[$sale['pay_user']] : '';
        //get shipping_yes_id
        $info_data[$sn]['shipping_yes_id_name'] = isset($staffs[$sale['shipping_yes_id']]) ?
            $staffs[$sale['shipping_yes_id']] : '';

        $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
        $data = array('print_picking_list' => 1);
        $QMarket->update($data, $where);

        
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $data = array(
            'print_date' => date('Y-m-d H:i:s'),
            'sales_order' => $sn,
            'user_id' => $userStorage->id,
        );

        $QPrintPickingLog->insert($data);


        $QKSSL = new Application_Model_KerryShipmentStatusLog();
        $QKCC = new Application_Model_KerryCompanyCondition();
        $QKHC = new Application_Model_KerryHolidayCondition();

        $getDetailMarket = $QKSSL->getProviceBySN($sn);

        //getKerryCondition by intersect Geniuos
        $getKerryCondition = $QKCC->getKerryCondition('1');
        $arrCondition = $this->convertDataInArrToArrayData($getKerryCondition, 'provice_id');

        $isKerry = '';

        date_default_timezone_set("Asia/Bangkok");
        $dateTimeNow = date("Y-m-d H:i:s");
        $dateNow = substr($dateTimeNow, 0, 10);

        //getKerryHolidayCondition
        $getKerryHoliday = $QKHC->getKerryHoliday($dateNow);
        $arrHolidayCondition = $this->convertDataInArrToArrayData($getKerryHoliday, 'day_holiday');

        // START : EDI KE Kerry
        foreach ($getDetailMarket as $key) {

            if($key['provice_id'] == '' || $key['rank'] == '' || $key['cat_id'] == '' || $key['warehouse_id'] == ''){
                continue;
            }

          if(!$this->checkConditionSendKerry($arrCondition, $arrHolidayCondition, $key['warehouse_id'], $key['provice_id'], $dateTimeNow, $dateNow, $key['ka_type'], $key['d_id'])){
            continue;
          }

          //rank ORG-Lotus/Power by, Laos, Brand Shop/Service
          if(in_array($key['rank'], [6,9,10])){
            continue;
          }

          if($key['cat_id'] == 13){
            continue;
          }

          $isKerry = 'KERRY';

        }

        // END : EDI KE Kerry

        // START : API J&T

        foreach ($getDetailMarket as $key) {

            if($isKerry != ''){
                continue;
            }

            if($key['provice_id'] == '' || $key['rank'] == '' || $key['cat_id'] == '' || $key['warehouse_id'] == ''){
                continue;
            }

          if(!$this->checkConditionSendJNT($arrCondition, $arrHolidayCondition, $key['warehouse_id'], $key['provice_id'], $dateTimeNow, $dateNow, $key['ka_type'], $key['d_id'])){
            continue;
          }

          // Big C
          // 49465 = บริษัท บิ๊กซี แฟรี่ จำกัด (สาขาที่ 00001)
          // 49466 = บริษัท พิษณุโลก บิ๊กซี 2015 จำกัด (พิษณุโลก)
          // 30344 = บริษัท บิ๊กซี ซูเปอร์เซ็นเตอร์ จำกัด (มหาชน)
          //rank ORG-Lotus/Power by, Laos, Brand Shop/Service
          if(in_array($key['rank'], [6,9,10]) && !in_array($key['d_id'], [49465,49466,30344])){
            continue;
          }

          if($key['cat_id'] == 13){
            continue;
          }

          $isKerry = 'J&T';

        }

        // END : API J&T

        
        // My_Image_Barcode::render($sn_ref[0]);

        $tmp['sales_list'] = $sales_list;
        $tmp['sn'] = $sn;
        $tmp['sn_ref'] = $sn_ref;
        $tmp['delivery_company'] = $isKerry;

        $orders[] = $tmp;
    } // end foreach

    $this->view->info_data = $info_data;
    $this->view->orders = $orders;
    if (isset($service) and $service) {

      $services = $QService->get_cache_service();
        $this->view->services = $services[$service];
    }
    if (isset($office) and $office) {
        $officeRowSet = $QOffice->find($office);
        $offices = $officeRowSet->current();
        $this->view->services = $offices;
        $this->view->service = $office;
    }

} else {
    exit;
}
