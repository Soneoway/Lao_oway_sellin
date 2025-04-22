<?php

set_time_limit(0);
ini_set('memory_limit', -1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

//get list distributor
$QDistributor = new Application_Model_Distributor();
$distributors = $QDistributor->get_cache();

//get list warehouse
$QWarehouse = new Application_Model_Warehouse();
$warehouses = $QWarehouse->get_cache();

$QGood = new Application_Model_Good();
$where = $QGood->getAdapter()->quoteInto('cat_id = ?', ACCESS_CAT_ID);
$this->view->accessories = $QGood->fetchAll($where, 'name');

$this->view->distributors = $distributors;
$this->view->warehouses = $warehouses;
$goods_cached = $QGood->get_cache();
$this->view->goods_cached = $goods_cached;

$QGoodColor = new Application_Model_GoodColor();
$good_colors_cached = $QGoodColor->get_cache();
$this->view->good_colors_cached = $good_colors_cached;

$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->fetchAll();

$flashMessenger = $this->_helper->flashMessenger;

$id                 = $this->getRequest()->getParam('id');
$QChangeSalesOrder  = new Application_Model_ChangeSalesOrder();
$QChangeSalesProduct = new Application_Model_ChangeSalesProduct();
$QChangeSalesImei   = new Application_Model_ChangeSalesImei();
$QImei              = new Application_Model_Imei();
$QDigitalSn         = new Application_Model_DigitalSn();
$QWarehouseProduct  = new Application_Model_WarehouseProduct();

$changeSalesOrder   = null;
if ($id){
    $whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
    $changeSalesOrder = $QChangeSalesOrder->fetchRow($whereChangeSalesOrder);
}

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

if($changeSalesOrder){

    $QMAW = new Application_Model_MapAddressWarehouse();
    $QKCC = new Application_Model_KerryCompanyCondition();
    $QKHC = new Application_Model_KerryHolidayCondition();

    //new warehouse
    $getMapAddressByWarehouse = $QMAW->getMapAddressByWarehouse($changeSalesOrder['new_id']);

    //36 = WMKR - Kerry,92 = Brandshop Warehouse at Kerry
    if($getMapAddressByWarehouse and in_array($changeSalesOrder['old_id'], ['36','92'])){

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
        foreach ($getMapAddressByWarehouse as $key) {

            if($key['province_id'] == '' || $key['warehouse_id'] == ''){
                continue;
            }

          if(!$this->checkConditionSendKerry($arrCondition, $arrHolidayCondition, $key['warehouse_id'], $key['province_id'], $dateTimeNow, $dateNow, '', '')){
            continue;
          }

          $isKerry = 'KERRY';

        }

        // END : EDI KE Kerry

        // START : API J&T

        foreach ($getMapAddressByWarehouse as $key) {

            if($isKerry != ''){
                continue;
            }

            if($key['province_id'] == '' || $key['warehouse_id'] == ''){
                continue;
            }

          if(!$this->checkConditionSendJNT($arrCondition, $arrHolidayCondition, $key['warehouse_id'], $key['province_id'], $dateTimeNow, $dateNow, '', '')){
            continue;
          }

          $isKerry = 'J&T';

        }

        // END : API J&T
    }

    $this->view->is_kerry = $isKerry;

}

//save form
if($this->getRequest()->isPost()) {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();

    echo '<link href="/css/bootstrap.min.css" rel="stylesheet">';

    $userStorage        = Zend_Auth::getInstance()->getStorage()->read();

    $QLog               = new Application_Model_Log();
    $ip                 = $this->getRequest()->getServer('REMOTE_ADDR');

    // check id
    if ($id){
        if (!$changeSalesOrder){
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Change Order is invalid!</div>';
            exit;
        }

        if (!in_array($changeSalesOrder['status'], array(CHANGE_ORDER_STATUS_SCANNED_OUT))){
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Change Order Status is invalid!</div>';
            exit;
        }
    }

    try
    {
        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        $changed_sn = $changeSalesOrder['changed_sn'];

        if ($id){

            $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('changed_id = ?', $id);
            $changeSalesProducts = $QChangeSalesProduct->fetchAll($whereChangeSalesProduct);

            $scannedListArray = [];

            foreach ($changeSalesProducts as $item){
                // kiem tra so luong
                // change WH
                if ($changeSalesOrder['is_changed_wh']){

                    $storageParams = array(
                        'warehouse_id'      => $changeSalesOrder['old_id'],
                        'cat_id'            => $item['cat_id'],
                        'good_id'           => $item['good_id'],
                        'good_color_id'     => $item['good_color'],
                    );

                    $storageParams['current_change_order_id']   = $id;

                    $storageParams['not_get_ilike_bad_count']   =
                    $storageParams['not_get_digital_bad_count'] =
                    $storageParams['not_get_imei_bad_count']    =
                    $storageParams['not_get_damage_product_count'] =
                    $storageParams['not_get_total']             =
                    $storageParams['not_order']                 =
                        true;


                    $storageParams['not_get_ilike_count'] = true;


                    $storage            = $QGood->fetchPaginationStorage(1, null, $total2, $storageParams);

                    $current_order      = isset($storage[0]['current_order']) ? $storage[0]['current_order'] : 0;
                    $current_change_order      = isset($storage[0]['current_change_order']) ? $storage[0]['current_change_order'] : 0;
                    if ($item['cat_id']==PHONE_CAT_ID and $changeSalesOrder['type']==FOR_DEMO){
                        $current_order          = isset($storage[0]['current_order_demo']) ? $storage[0]['current_order_demo'] : 0;
                        $current_change_order   = isset($storage[0]['current_change_order_demo']) ? $storage[0]['current_change_order_demo'] : 0;
                    }else if ($item['cat_id']==PHONE_CAT_ID and $changeSalesOrder['type']==FOR_APK){
                        $current_order          = isset($storage[0]['current_order_apk']) ? $storage[0]['current_order_apk'] : 0;
                        $current_change_order   = isset($storage[0]['current_change_order_apk']) ? $storage[0]['current_change_order_apk'] : 0;
                    }

                    $current_storage    = 0;

                    if (isset($storage[0]) and $storage[0]) {
                        switch ($item['cat_id']){
                            case DIGITAL_CAT_ID:
                                $current_storage = (isset($storage[0]['digital_count']) and $storage[0]['digital_count']) ? $storage[0]['digital_count'] : 0;
                                break;
                            case PHONE_CAT_ID:
                                $current_storage = (isset($storage[0]['imei_count']) and $storage[0]['imei_count']) ? $storage[0]['imei_count'] : 0;
                                if ($changeSalesOrder['type']==FOR_DEMO){
                                    $current_storage = (isset($storage[0]['imei_demo_count']) and $storage[0]['imei_demo_count']) ? $storage[0]['imei_demo_count'] : 0;
                                }else if ($changeSalesOrder['type']==FOR_APK){
                                    $current_storage = (isset($storage[0]['imei_apk_count']) and $storage[0]['imei_apk_count']) ? $storage[0]['imei_apk_count'] : 0;
                                }
                                break;
                            case ILIKE_CAT_ID:
                                $current_storage = (isset($storage[0]['ilike_count']) and $storage[0]['ilike_count']) ? $storage[0]['ilike_count'] : 0;
                                break;
                            case ACCESS_CAT_ID:
                                $current_storage = (isset($storage[0]['product_count']) and $storage[0]['product_count']) ? $storage[0]['product_count'] : 0;
                                break;

                            case IOT_CAT_ID:
                                $current_storage = (isset($storage[0]['imei_count']) and $storage[0]['imei_count']) ? $storage[0]['imei_count'] : 0;
                                break;

                        }
                    }

                    if ( ($current_storage - $current_order - $current_change_order) < $item['num'] ) {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - the quantity '.$goods_cached[$item['good_id']].' / '.$good_colors_cached[$item['good_color']].' is not enough in this Warehouse! ('.($current_storage - $current_order - $current_change_order).'<'.$item['num'].')</div>';
                        exit;
                    }

                }
                // change Retailer
                else {

                    if ($item['cat_id'] == PHONE_CAT_ID){
                        $whereImei = $QImei->getAdapter()->quoteInto('distributor_id = ?', $changeSalesOrder['old_id']);
                        $checked_result = $QImei->fetchAll($whereImei);

                        if ( $checked_result->count() < $item['num'] ){
                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - the quantity '.$goods_cached[$item['good_id']].' / '.$good_colors_cached[$item['good_color']].' is not enough!</div>';
                            exit;
                        }
                    } elseif ($item['cat_id'] == DIGITAL_CAT_ID) {
                        $whereDigitalSn = $QDigitalSn->getAdapter()->quoteInto('distributor_id = ?', $changeSalesOrder['old_id']);
                        $checked_result = $QDigitalSn->fetchAll($whereDigitalSn);

                        if ( $checked_result->count() < $item['num'] ){
                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - the quantity '.$goods_cached[$item['good_id']].' / '.$good_colors_cached[$item['good_color']].' is not enough!</div>';
                            exit;
                        }
                    }elseif($item['cat_id'] == IOT_CAT_ID){
                        $whereIotImei = $QImei->getAdapter()->quoteInto('distributor_id = ?', $changeSalesOrder['old_id']);
                        $checked_result = $QImei->fetchAll($whereIotImei);

                        if ( $checked_result->count() < $item['num'] ){
                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - the quantity '.$goods_cached[$item['good_id']].' / '.$good_colors_cached[$item['good_color']].' is not enough!</div>';
                            exit;
                        }
                    }

                }


                if ($item['cat_id'] == PHONE_CAT_ID){
                    $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?', $item['id']);
                    $scannedList = $QChangeSalesImei->fetchAll($whereChangeSalesImei);

                    if ($scannedList->count() != $item['num']){
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Please Scan OUT enough</div>';

                        exit;
                    }

                    foreach ($scannedList as $scannedSN){

                        $whereImei = array();
                        $whereImei[] = $QImei->getAdapter()->quoteInto('imei_sn = ?', $scannedSN['imei']);
                        $whereImei[] = $QImei->getAdapter()->quoteInto('sales_sn is null',1);
                        $imeiInfo = $QImei->fetchRow($whereImei);
                        if (!$imeiInfo){
                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - Invalid SN: '.$scannedSN['imei'].'</div>';

                            exit;
                        }

                        // if ($imeiInfo['out_date']){
                        //     echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        //     echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        //     echo '<div class="alert alert-error">Failed - was OUT: '.$scannedSN['imei'].'</div>';

                        //     exit;
                        // }

                        //update to imei
                        $data = array();
                        $data['status'] = IMEI_STATUS_ON_CHANGE; //on changing
                        $data['changed_sn'] = $changed_sn;

                        $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $scannedSN['imei']);
                        $QImei->update($data, $where);

                        array_push($scannedListArray, $scannedSN);

                    }

                }
                elseif ($item['cat_id'] == DIGITAL_CAT_ID){

                    $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?', $item['id']);
                    $scannedList = $QChangeSalesImei->fetchAll($whereChangeSalesImei);

                    if ($scannedList->count() != $item['num']){
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Please Scan OUT enough</div>';

                        exit;
                    }

                    foreach ($scannedList as $scannedSN){

                        $whereDigitalSn = $QDigitalSn->getAdapter()->quoteInto('sn = ?', $scannedSN['imei']);
                        $Info = $QDigitalSn->fetchRow($whereDigitalSn);
                        if (!$Info){
                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - Invalid SN: '.$scannedSN['imei'].'</div>';

                            exit;
                        }

                        if ($Info['out_date']){
                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - was OUT: '.$scannedSN['imei'].'</div>';

                            exit;
                        }

                        //update to Digital SN
                        $data = array();
                        $data['status'] = IMEI_STATUS_ON_CHANGE; //on changing
                        $data['changed_sn'] = $changed_sn;

                        $where = $QDigitalSn->getAdapter()->quoteInto('sn = ?', $scannedSN['imei']);
                        $QDigitalSn->update($data, $where);

                        array_push($scannedListArray, $scannedSN);

                    }

                    //edit khuan
                }elseif($item['cat_id'] == IOT_CAT_ID){
                    $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?', $item['id']);
                    $scannedList = $QChangeSalesImei->fetchAll($whereChangeSalesImei);

                    if ($scannedList->count() != $item['num']){
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Please Scan OUT enough</div>';

                        exit;
                    }

                    foreach ($scannedList as $scannedSN){

                        $whereImei = array();
                        $whereImei[] = $QImei->getAdapter()->quoteInto('imei_sn = ?', $scannedSN['imei']);
                        $whereImei[] = $QImei->getAdapter()->quoteInto('sales_sn is null',1);
                        $Info = $QImei->fetchRow($whereImei);

                        if (!$Info){
                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - Invalid SN: '.$scannedSN['imei'].'</div>';

                            exit;
                        }

                        if ($Info['out_date']){
                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - was OUT: '.$scannedSN['imei'].'</div>';

                            exit;
                        }

                        $data = array();
                        $data['status'] = IMEI_STATUS_ON_CHANGE; //on changing
                        $data['changed_sn'] = $changed_sn;

                        $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $scannedSN['imei']);
                        $QImei->update($data, $where);

                        array_push($scannedListArray, $scannedSN);
                }
                //end
                //edn

                } else {

                    // insert Warehouse product
                    $where = array();
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('cat_id = ?',        $item['cat_id']);
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_id = ?',       $item['good_id']);
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_color = ?',    $item['good_color']);
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('warehouse_id = ?',  $changeSalesOrder['old_id']);

                    $result = $QWarehouseProduct->fetchRow($where);

                    if ($result) {

                        $where = $QWarehouseProduct->getAdapter()->quoteInto('id = ?', $result['id']);

                        //update quantity
                        $QWarehouseProduct->update(array(
                            'quantity' => ($result['quantity'] - $item['num'])
                        ), $where);

                    }
                }
            }

            $is_kerry = $this->getRequest()->getParam('is_kerry');

            if(isset($is_kerry) and $is_kerry){
                $array_update_co = array(
                    'status'                => CHANGE_ORDER_STATUS_ON_CHANGE,
                    'confirmed_out_at'      => date('Y-m-d H:i:s'),
                    'confirmed_out_by'      => $userStorage->id,
                    'is_co'                 => 1    
                );
            }else{
                $array_update_co = array(
                    'status'                => CHANGE_ORDER_STATUS_ON_CHANGE,
                    'confirmed_out_at'      => date('Y-m-d H:i:s'),
                    'confirmed_out_by'      => $userStorage->id
                );
            }

            // update
            $whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
            $QChangeSalesOrder->update($array_update_co, $whereChangeSalesOrder);

            // update borrowing status for app
            if(isset($changeSalesOrder['borrowing_id']) && $changeSalesOrder['borrowing_id']){

                $QBL = new Application_Model_BorrowingList();

                $getDetailsBorrowingByID = $QBL->getDetailsBorrowingByID($changeSalesOrder['borrowing_id']);

                if($getDetailsBorrowingByID['wms_return_date']){

                    //status 14 is return by wms
                    $update_borrowing = array(
                        'read_data' => 1,
                        'update_datetime' => date('Y-m-d H:i:s'),
                        'status' => 14
                    );

                    $where_update = $QBL->getAdapter()->quoteInto('id = ?', $changeSalesOrder['borrowing_id']);
                    $status_update_14 = $QBL->update($update_borrowing,$where_update);

                    $QBT = new Application_Model_BorrowingTran();

                    $dateNow = date('Y-m-d H:i:s');

                    $updateBorrowingTran = array(
                        'status' => 2,
                        'update_date' => $dateNow,
                        'update_by' => $userStorage->id,
                        'co_return' => $changeSalesOrder['sn_ref']
                    );

                    $where_update_bowrrowingTrn = $QBT->getAdapter()->quoteInto('bl_id = ?', $changeSalesOrder['borrowing_id']);

                    $QBT->update($updateBorrowingTran, $where_update_bowrrowingTrn);

                }else{

                    //status 11 is confirm by wms
                    $update_borrowing = array(
                        'read_data' => 1,
                        'update_datetime' => date('Y-m-d H:i:s'),
                        'status' => 11
                    );

                    $where_update = $QBL->getAdapter()->quoteInto('id = ?', $changeSalesOrder['borrowing_id']);
                    $status_update_11 = $QBL->update($update_borrowing,$where_update);

                    $QBT = new Application_Model_BorrowingTran();

                    $dateNow = date('Y-m-d H:i:s');

                    $whereBowrrowing = $QBL->getAdapter()->quoteInto('id = ?', $changeSalesOrder['borrowing_id']);
                    $getBowrrowing = $QBL->fetchRow($whereBowrrowing);

                    foreach ($scannedListArray as $key_addBT) {

                        $addBorrowingTran = array(
                            'bl_id' => $changeSalesOrder['borrowing_id'],
                            'bl_sn' => $getBowrrowing['sn'],
                            'rq' => $getBowrrowing['rq'],
                            'co' => $changeSalesOrder['sn_ref'],
                            // 'type' => $getBowrrowing['borrowing_type'],
                            'imei' => $key_addBT['imei'],
                            'created_date' => $dateNow,
                            'created_by' => $userStorage->id,
                            'update_date' => $dateNow,
                            'update_by' => $userStorage->id,
                            'status' => 1
                        );

                        $QBT->insert($addBorrowingTran);

                    }
                }

            }

            // START : Delivery CO

                $QMAW = new Application_Model_MapAddressWarehouse();
                $QKCC = new Application_Model_KerryCompanyCondition();
                $QKHC = new Application_Model_KerryHolidayCondition();

                //new warehouse
                $getMapAddressByWarehouse = $QMAW->getMapAddressByWarehouse($changeSalesOrder['new_id']);

                //36 = WMKR - Kerry,92 = Brandshop Warehouse at Kerry
                if($getMapAddressByWarehouse and in_array($changeSalesOrder['old_id'], ['36','92'])){

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
                    foreach ($getMapAddressByWarehouse as $key) {

                        if($key['province_id'] == '' || $key['warehouse_id'] == ''){
                            continue;
                        }

                      if(!$this->checkConditionSendKerry($arrCondition, $arrHolidayCondition, $key['warehouse_id'], $key['province_id'], $dateTimeNow, $dateNow, '', '')){
                        continue;
                      }

                      $isKerry = 'KERRY';

                    }

                    // END : EDI KE Kerry

                    // START : API J&T

                    foreach ($getMapAddressByWarehouse as $key) {

                        if($isKerry != ''){
                            continue;
                        }

                        if($key['province_id'] == '' || $key['warehouse_id'] == ''){
                            continue;
                        }

                      if(!$this->checkConditionSendJNT($arrCondition, $arrHolidayCondition, $key['warehouse_id'], $key['province_id'], $dateTimeNow, $dateNow, '', '')){
                        continue;
                      }

                      $isKerry = 'J&T';

                    }

                    // END : API J&T

                    $is_kerry = $isKerry;

                    $kerry_status = 0;

                    if(!is_null($is_kerry) && $is_kerry == 'KERRY'){
                        $kerry_status = 1;
                    }

                    if(!is_null($is_kerry) && $is_kerry == 'J&T'){
                        $kerry_status = 2;
                    }

                    if($kerry_status == 1){

                        $get_tracking = 'OP' . $this->textRemoveDash($changeSalesOrder['sn_ref']);
                        $get_number_of_package = $this->getRequest()->getParam('number_of_package');
                        $get_weight = $this->getRequest()->getParam('weight');

                        // START : Add Delivery When Send To Kerry

                        $distributor_list = array(
                            'address'  => htmlspecialchars($getMapAddressByWarehouse['address'], ENT_QUOTES, 'UTF-8'),
                            'district' => intval($getMapAddressByWarehouse['districts_id']),
                            'phone'    => htmlspecialchars(My_String::trim($getMapAddressByWarehouse['phone']), ENT_QUOTES, 'UTF-8')
                        );

                        $QDeliveryOrder = new Application_Model_DeliveryOrder();

                        $receiver = date('YmdHis') . substr(microtime(), 2, 4);
                        $delivery_sn = $QDeliveryOrder->getDeliveryNo_Ref($receiver);

                        $address = $distributor_list['address'];
                        $district = $distributor_list['district'];
                        $phone = $distributor_list['phone'];

                        $data = array(
                            'created_at'        => date('Y-m-d H:i:s'),
                            'created_by'        => intval($userStorage->id),
                            'out_time'          => date('Y-m-d H:i:s'),
                            'receiver'          => My_String::trim($receiver),
                            'address'           => My_String::trim($address),
                            'shipping_id'       => $getMapAddressByWarehouse['id'],
                            'district'          => intval($district),
                            'phone_number'      => My_String::trim($phone),
                            'distributor_id'    => $getMapAddressByWarehouse['map_d_id'],
                            'warehouse_id'      => intval($changeSalesOrder['new_id']),
                            'type'              => 2,//Outside
                            'sn'                => My_String::trim($delivery_sn),
                            'staff_id'          => 0,
                            'carrier_id'        => 1,//kerry
                            'number_of_package' => $get_number_of_package,
                            'weight'            => $get_weight,
                            'status'            => 2,
                            'tracking_no'       => $get_tracking,
                            'is_co'             => 1
                        );


                        $id = $QDeliveryOrder->insert($data);

                        if (!$id) throw new Exception("Cannot create delivery", 3);

                        $QDeliverySales = new Application_Model_DeliverySales();

                            $data = array('delivery_order_id' => $id, 'sales_sn' => $changed_sn, 'is_co' => '1');
                            $QDeliverySales->insert($data);

                        // END : Add Delivery When Send To Kerry


                        // START : Add Kerry Transacion

                        $QKT = new Application_Model_KerryTransaction();

                        $QKTData = [
                        'tracking_no'   => $get_tracking,
                        'sn'            => $changed_sn,
                        'type'          => 1,
                        'outmysql_time' => $dateTimeNow,
                        'delivery_type' => $kerry_status,
                        'is_co'         => 1
                        ];

                        $QKT->insert($QKTData);

                        // END : Add Kerry Transacion

                    }else if($kerry_status == 2){ //J&T
                        $get_tracking = 'OP' . $this->textRemoveDash($changeSalesOrder['sn_ref']);
                        $get_number_of_package = $this->getRequest()->getParam('number_of_package');
                        $get_weight = $this->getRequest()->getParam('weight');

                        // START : Add Delivery When Send To J&T

                        $distributor_list = array(
                            'address'  => htmlspecialchars($getMapAddressByWarehouse['address'], ENT_QUOTES, 'UTF-8'),
                            'district' => intval($getMapAddressByWarehouse['districts_id']),
                            'phone'    => htmlspecialchars(My_String::trim($getMapAddressByWarehouse['phone']), ENT_QUOTES, 'UTF-8')
                        );

                        $QDeliveryOrder = new Application_Model_DeliveryOrder();

                        $receiver = date('YmdHis') . substr(microtime(), 2, 4);
                        $delivery_sn = $QDeliveryOrder->getDeliveryNo_Ref($receiver);

                        $address = $distributor_list['address'];
                        $district = $distributor_list['district'];
                        $phone = $distributor_list['phone'];

                        $data = array(
                            'created_at'        => date('Y-m-d H:i:s'),
                            'created_by'        => intval($userStorage->id),
                            'out_time'          => date('Y-m-d H:i:s'),
                            'receiver'          => My_String::trim($receiver),
                            'address'           => My_String::trim($address),
                            'shipping_id'       => $getMapAddressByWarehouse['id'],
                            'district'          => intval($district),
                            'phone_number'      => My_String::trim($phone),
                            'distributor_id'    => $getMapAddressByWarehouse['map_d_id'],
                            'warehouse_id'      => intval($changeSalesOrder['new_id']),
                            'type'              => 2,//Outside
                            'sn'                => My_String::trim($delivery_sn),
                            'staff_id'          => 0,
                            'carrier_id'        => 9,//J&T
                            'number_of_package' => $get_number_of_package,
                            'weight'            => $get_weight,
                            'status'            => 2,
                            'tracking_no'       => $get_tracking,
                            'is_co'             => 1
                        );


                        $id = $QDeliveryOrder->insert($data);

                        if (!$id) throw new Exception("Cannot create delivery", 3);

                        $QDeliverySales = new Application_Model_DeliverySales();

                            $data = array('delivery_order_id' => $id, 'sales_sn' => $changed_sn, 'is_co' => '1');
                            $QDeliverySales->insert($data);

                        // END : Add Delivery When Send To J&T


                        // START : Add J&T Transacion

                        $QKT = new Application_Model_KerryTransaction();

                        $QKTData = [
                        'tracking_no'   => $get_tracking,
                        'sn'            => $changed_sn,
                        'type'          => 1,
                        'outmysql_time' => $dateTimeNow,
                        'delivery_type' => $kerry_status,
                        'is_co'         => 1
                        ];

                        $QKT->insert($QKTData);

                        // END : Add j&T Transacion

                    }

                }

            // END : Delivery CO

        }

        $info = 'CHANGE ORDER - CONFIRMED OUT: ' . $changeSalesOrder['changed_sn'];
        $QLog->insert( array (
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => date('Y-m-d H:i:s'),
        ) );

        $db->commit();

        if(isset($status_update_11) && $status_update_11){
            $data_curl_11 = array('code' => $getDetailsBorrowingByID['code'], 'status' => 11, 'rq' => $getDetailsBorrowingByID['rq']);

            $handle = curl_init(API_IOPPO_URL . 'warehouse-notification');
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $data_curl_11);
            curl_exec($handle);
            curl_close($handle);
        }

        if(isset($status_update_14) && $status_update_14){
            $data_curl_14 = array('code' => $getDetailsBorrowingByID['code'], 'status' => 14, 'rq' => $getDetailsBorrowingByID['rq']);

            $handle = curl_init(API_IOPPO_URL . 'warehouse-notification');
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $data_curl_14);
            curl_exec($handle);
            curl_close($handle);
        }

        $flashMessenger->setNamespace('success')->addMessage('Done!');

        echo '<script>parent.location.href="/warehouse/change-sales-list"</script>';
        exit;

    } catch (Exception $e){
        $db->rollback();

        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
        echo '<div class="alert alert-error">Failed - '.$e->getMessage().'</div>';
        exit;
    }
}

if ($id){
    if (!$changeSalesOrder){
        $flashMessenger->setNamespace('error')->addMessage('Change Order is invalid!');
        $this->redirect('/warehouse/change-sales-list');
    }

    if (!in_array($changeSalesOrder['status'], array(CHANGE_ORDER_STATUS_SCANNED_OUT))){
        $flashMessenger->setNamespace('error')->addMessage('Status is invalid!');
        $this->redirect('/warehouse/change-sales-list');
    }
}

if ($id){
    $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('changed_id = ?', $id);
    $changeSalesProduct = $QChangeSalesProduct->fetchAll($whereChangeSalesProduct);

    $changeSalesImeisList = $changeSalesIotList = $changeSalesImeisReceivedList = $changeSalesIotImeisReceivedList = $changeSalesIotImeisLostList = $changeSalesImeisLostList = array();
    $changeSalesImeisDigitalList = $changeSalesImeisReceivedDigitalList = $changeSalesImeisLostDigitalList = array();
    
    if ($changeSalesProduct->count()){

        foreach ($changeSalesProduct as $item){
            $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?', $item['id']);
            $changeSalesImeis = $QChangeSalesImei->fetchAll($whereChangeSalesImei);

            if ($changeSalesImeis->count()){ 
                foreach ($changeSalesImeis as $im){
                 
                 if($im['cat_id'] == PHONE_CAT_ID)
                 {
                    if ($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                        $changeSalesImeisReceivedList[] = $im['imei'];
                    else
                        $changeSalesImeisLostList[] = $im['imei'];

                    $changeSalesImeisList[] = $im['imei'];
                 }

                 //edit khuan

                 if($im['cat_id'] == IOT_CAT_ID)
                 {
                    if($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                        $changeSalesIotImeisReceivedList[] = $im['imei'];
                    else
                        $changeSalesIotList[] = $im['imei'];

                    $changeSalesIotImeisLostList = $im['imei'];
                 }

                 //end
                 //end
                 
                 if($im['cat_id'] == DIGITAL_CAT_ID)
                 {
                    if ($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                        $changeSalesImeisReceivedDigitalList[] = $im['imei'];
                    else
                        $changeSalesImeisLostDigitalList[] = $im['imei'];

                    $changeSalesImeisDigitalList[] = $im['imei'];
                 }
                 
                  //d?i v?i tru?ng hop nhung imei cu
                    if(empty($im['cat_id']))
                    {
                        if ($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                            $changeSalesImeisReceivedList[] = $im['imei'];
                        else
                            $changeSalesImeisLostList[] = $im['imei'];

                        $changeSalesImeisList[] = $im['imei'];                        
                    }
                   
                }
            }
        }

        $this->view->changeSalesImeisReceivedList = $changeSalesImeisReceivedList;
        $this->view->changeSalesImeisLostList = $changeSalesImeisLostList;
        $this->view->changeSalesImeisList = $changeSalesImeisList;
        $this->view->changeSalesImeisReceivedDigitalList = $changeSalesImeisReceivedDigitalList;
        $this->view->changeSalesImeisLostDigitalList = $changeSalesImeisLostDigitalList;
        $this->view->changeSalesImeisDigitalList = $changeSalesImeisDigitalList;

        $this->view->changeSalesIotList = $changeSalesIotList;
        $this->view->changeSalesIotImeisReceivedList = $changeSalesIotImeisReceivedList;
        $this->view->changeSalesIotImeisLostList = $changeSalesIotImeisLostList;
    }

    $this->view->changeSalesProduct = $changeSalesProduct;
}

$this->view->changeSalesOrder = $changeSalesOrder;

$this->view->disabledFields = array(
    'is_changed_wh',
    'cat_id',
    'good_id',
    'good_color',
    'num',
    'sns',
    'sn_iot',
    'type',
);

$this->view->hideFields = array(
    'sns_receive',
    'sns_lost',
    'sn_iot_receives',
    'sn_iot_lost',
    'num_receive',
    'num_lost',
    'remove-sales', 
    'sns_digital_receive',
    'sns_digital_lost'
);