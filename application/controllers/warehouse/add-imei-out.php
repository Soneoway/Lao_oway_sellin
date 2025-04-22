<?php
$sn = $this->getRequest()->getParam('sn');
$flashMessenger = $this->_helper->flashMessenger;
$back_url = $this->getRequest()->getServer('HTTP_REFERER');
$back_url = $back_url ? $back_url : '/warehouse/out';

if ($sn) {

    $QMarket            = new Application_Model_Market();
    $QImei              = new Application_Model_Imei();
    $QDigitalSn         = new Application_Model_DigitalSn();
    $QGoodSn            = new Application_Model_GoodSn();

    $QKSSL              = new Application_Model_KerryShipmentStatusLog();
    $QKCC               = new Application_Model_KerryCompanyCondition();
    $QKHC               = new Application_Model_KerryHolidayCondition();

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

    $this->view->is_kerry = $isKerry;
    $QStore = new Application_Model_Store;
    $this->view->store = $QStore->get_cache();

    $QGood            = new Application_Model_Good();
    $this->view->goods = $QGood->get_cache();

    $QGoodColor            = new Application_Model_GoodColor();
    $this->view->goodColors = $QGoodColor->get_cache();

    $QGoodCategory               = new Application_Model_GoodCategory();
    $this->view->good_categories = $QGoodCategory->get_cache();

    $QDistributor               = new Application_Model_Distributor();
    $this->view->distributors = $QDistributor->get_cache();

    $Credit_Note = $QMarket->fetchCredit_Note($sn);

    $phone_id = $QGoodCategory->get_phone_id();
    $this->view->phone_id = $phone_id;

    $params = array(
        'status' => 1,
        'group_sn' => 1,
        'sn' => $sn,
    );

    $limit = LIMITATION;
    $total = 0;
    $page = 1;

    // $sales_out = $QMarket->fetchPagination($page, $limit, $total, $params);
    $sales_out = $QMarket->getMarketOnly($params);

    $this->view->sales_out = $sales_out[0];
    $total_discount=0;
    foreach ($Credit_Note as $k => $datas)
    {
        $total_discount += $datas['total_discount'];
    }
    $this->view->total_discount = $total_discount;

    if ( !$sales_out ){

        $flashMessenger->setNamespace('error')->addMessage('Invalid SN!');

        $this->_redirect($back_url);

    }

    if ( $sales_out[0]['outmysql_time'] ){

        $flashMessenger->setNamespace('error')->addMessage('This order cannot be changed!');

        $this->_redirect($back_url);

    }

    // lấy danh sách tất cả sản phẩm trong hóa đơn
    $where = array();
    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

    $sales_list = $QMarket->fetchAll($where);

    // tính số đã xuất trên từng sản phẩm
    $sales_list_out = $sales_list_imei_scanned_out = $sales_list_digital_sn_scanned_out = array();
    $total_out = 0;
    foreach ($sales_list as $k => $v) {
        $scanned_out =
            $v['cat_id'] == PHONE_CAT_ID ?
                $QMarket->count_out_imei($sn, $v['good_id'], $v['good_color'], $v['id']) :
                (
                    $v['cat_id'] == DIGITAL_CAT_ID ?
                        $QMarket->count_out_digital($sn, $v['good_id'], $v['good_color'], $v['id']) :
                            (
                                $v['cat_id'] == ILIKE_CAT_ID ?
                                    $QMarket->count_out_ilike($sn, $v['good_id'], $v['good_color'], $v['id']) : 0
                            )
                )
        ;
        $sales_list_out[$k] = $scanned_out;

    }


    // $where = $QImei->getAdapter()->quoteInto('sales_sn = ?', $sn);
    // $sales_list_imei_scanned_out = $QImei->fetchAll($where);
    $db = Zend_Registry::get('db');
    $select = $db->select()->from(array('i' => 'imei'), array('total' => 'COUNT(imei_sn)'))
        ->where('sales_sn = ?', $sn);
    $count_phone = $db->fetchOne($select);

    $where = $QDigitalSn->getAdapter()->quoteInto('sales_sn = ?', $sn);
    $sales_list_digital_sn_scanned_out = $QDigitalSn->fetchAll($where);

    $where = $QGoodSn->getAdapter()->quoteInto('sales_sn = ?', $sn);
    $sales_list_ilike_sn_scanned_out = $QGoodSn->fetchAll($where);

    $total_out = $count_phone + $sales_list_digital_sn_scanned_out->count() + $sales_list_ilike_sn_scanned_out->count();

    $this->view->total_out      = $total_out;
    $this->view->sales_list     = $sales_list;
    $this->view->sales_list_out = $sales_list_out;

    // $this->view->sales_list_imei_scanned_out = $sales_list_imei_scanned_out;
    $this->view->sales_list_digital_sn_scanned_out = $sales_list_digital_sn_scanned_out;
    $this->view->sales_list_ilike_sn_scanned_out = $sales_list_ilike_sn_scanned_out;
    $this->view->sn             = $sn;

    //back url
    $this->view->back_url = $back_url;

} else {
    $flashMessenger->setNamespace('error')->addMessage('Wrong Action!');
    $this->_redirect($back_url);
}