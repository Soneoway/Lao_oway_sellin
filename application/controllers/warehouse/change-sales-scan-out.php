<?php

set_time_limit(0);
ini_set('memory_limit', -1);

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

$id = $this->getRequest()->getParam('id');
$QChangeSalesOrder = new Application_Model_ChangeSalesOrder();
$QChangeSalesProduct = new Application_Model_ChangeSalesProduct();
$QChangeSalesImei = new Application_Model_ChangeSalesImei();
$QImei = new Application_Model_Imei();
$QDigitalSn = new Application_Model_DigitalSn();

$changeSalesOrder = null;
if ($id) {
    $whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
    $changeSalesOrder = $QChangeSalesOrder->fetchRow($whereChangeSalesOrder);
}

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

//save form
if ($this->getRequest()->isPost()) {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    //print_r($_POST);die;

    echo '<link href="/css/bootstrap.min.css" rel="stylesheet">';

    $SNs = $this->getRequest()->getParam('sns');
    $SNsDigital = $this->getRequest()->getParam('sns_digital');
    $SNIot = $this->getRequest()->getParam('sn_iot');

    $ids = $this->getRequest()->getParam('ids');

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $QLog = new Application_Model_Log();
    $ip = $this->getRequest()->getServer('REMOTE_ADDR');

    // check id
    if ($id) {
        if (!$changeSalesOrder) {
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Change Order is invalid!</div>';
            exit;
        }

        if (!in_array($changeSalesOrder['status'], array(CHANGE_ORDER_STATUS_SCANNED_OUT,
            CHANGE_ORDER_STATUS_PENDING))) {
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Change Order Status is invalid!</div>';
            exit;
        }
    }

    try {
        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        if ($id) {

            $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('changed_id = ?',
                $changeSalesOrder['id']);
            $changeSalesProduct = $QChangeSalesProduct->fetchAll($whereChangeSalesProduct);
            // delete old
            $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sn = ?',
                $changeSalesOrder['changed_sn']);
            $QChangeSalesImei->delete($whereChangeSalesImei);

            $total_imei = 0;
            $total_digital = 0;
            $total_iot = 0;
            $offset = 0;
            $offset_digital = 0;
            $offset_iot = 0;
            $imei_scan = array();
            $iot_scan = array();
            $digital_scan = array();

            $fist_warehouse = $changeSalesOrder['old_id'];

            //  var_dump($changeSalesOrder['changed_sn']);exit;
            foreach ($changeSalesProduct as $k => $v) {

                if ($v['cat_id'] == PHONE_CAT_ID) {
                    $total_imei += $v['num'];
                    for ($i = 1; $i <= $v['num']; $i++) {
                        $imei_scan[$offset]['good_id'] = $v['good_id'];
                        $imei_scan[$offset]['good_color'] = $v['good_color'];
                        $imei_scan[$offset]['receive'] = 0;
                        $imei_scan[$offset]['id'] = $v['id'];
                        $offset++;
                    }
                }

                //edit khuan
                if ($v['cat_id'] == DIGITAL_CAT_ID) {
                    $total_digital += $v['num'];
                    for ($i = 1; $i <= $v['num']; $i++) {
                        $digital_scan[$offset_digital]['good_id'] = $v['good_id'];
                        $digital_scan[$offset_digital]['good_color'] = $v['good_color'];
                        $digital_scan[$offset_digital]['receive'] = 0;
                        $digital_scan[$offset_digital]['id'] = $v['id'];
                        $offset_digital++;
                    }
                }

                if($v['cat_id'] == IOT_CAT_ID){
                    $total_iot += $v['num'];
                    for($i=1; $i <= $v['num']; $i++){
                        $iot_scan[$offset_iot]['good_id'] = $v['good_id'];
                        $iot_scan[$offset_iot]['good_color'] = $v['good_color'];
                        $iot_scan[$offset_iot]['receive'] = 0;
                        $iot_scan[$offset_iot]['id'] = $v['id'];
                        $offset_iot++;
                    }
                }

            }

            if(isset($SNIot) and $SNIot){

                if (!isset($SNIot) or !$SNIot) {
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - Please scan SN!</div>';
                    exit;
                }

                $scannedList = $SNIot;
                $count = 0;
                $scannedList = $scannedList ? preg_replace('/\n+/', ',', $scannedList) : '';
                $scannedList = $scannedList ? array_filter(array_map('trim', explode(",", $scannedList))) :
                array();
                $scannedList = array_unique($scannedList);

                if (count($scannedList) != $total_iot) {
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - IMEI scan ( ' . (count($scannedList)) . ' ) - Imei scanned - ( ' .
                    $total_iot . ' ) - Imei required - Please scan enough!</div>';
                    exit;
                }

                 // check lock imei
                $QImeiLock = new Application_Model_ImeiLock();
                $getImeiLock = $QImeiLock->checkImeiLock($scannedList);
                // $listImeiLock = '';
                // foreach ($getImeiLock as $key => $value) {
                //     if($key == 0){
                //         $listImeiLock = $value['imei_log'];
                //     }else{
                //         $listImeiLock .= ','. $value['imei_log'];
                //     }
                // }
                // if($listImeiLock){
                //     // exit('<div class="alert alert-danger">IMEI Locked<br>' . $listImeiLock .
                //     //     '<br></div>');

                //     echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                //     echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                //     echo '<div class="alert alert-error">Failed - IMEI Locked: ' . $listImeiLock . '</div>';
                //     exit;
                // }

                $getCheckImei = $QChangeSalesImei->getImeiChangeSalesImei($scannedList);
                $check_error_sn = '';
                foreach ($getCheckImei as $key => $value) {

                    if($key == 0){
                        $check_error_sn = $value['imei'];
                    }else{
                        $check_error_sn .= ',' . $value['imei'];
                    }
                }
                if($check_error_sn){
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - List Imei On Changing: ' . $check_error_sn . '</div>';
                    exit;
                }

                foreach ($scannedList as $sn) {
                    if (!preg_match('/^[0-9a-zA-Z]{15,22}$/', $sn)) {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Invalid SN ddd: ' . $sn . '</div>';
                        exit;
                    }

                    $where = array();
                    $where[] = $QImei->getAdapter()->quoteInto('imei_sn = ?', $sn);
                    $where[] = $QImei->getAdapter()->quoteInto('sales_sn is null',1);
                    $imeiInfo = $QImei->fetchRow($where);

                    if (!$imeiInfo) {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Invalid SN fff: ' . $sn . '</div>';
                        exit;
                    }

                    // check warehouse
                    if($imeiInfo['warehouse_id'] != $fist_warehouse){
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Incorrect Warehouse IMEI: ' . $sn . '</div>';
                        exit;
                    }

                    $checked = 0;
                    foreach ($iot_scan as $k => $v) {

                        if ($imeiInfo['good_id'] == $v['good_id'] && $imeiInfo['good_color'] == $v['good_color'] &&
                            $v['receive'] == 0) {
                            $iot_scan[$k]['receive'] = 1;
                        $changeSalesProductID = $iot_scan[$k]['id'];
                        $count++;
                        $checked = 1;
                        break;
                    }

                }

                if ($checked == 0) 
                {
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - Product or Good is not correct : ' .
                    $sn . '</div>';
                    exit;
                }

                $data = array(
                    'changed_sales_product_id' => $changeSalesProductID,
                    'changed_sn' => $changeSalesProduct[0]['changed_sn'],
                    'imei' => $sn,
                    'cat_id' => IOT_CAT_ID);

                $QChangeSalesImei->insert($data);
            }

            if ($count != $total_iot) {
                echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                echo '<div class="alert alert-error">Failed - ' . $sn .
                ' is invalid product</div>';

                exit;
            }

        }

            //end
            //end

        if (isset($SNs) and $SNs) {
                // check scanned co du khong
            if (!isset($SNs) or !$SNs) {
                echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                echo '<div class="alert alert-error">Failed - Please scan SN!</div>';
                exit;
            }

            $scannedList = $SNs;
            $count = 0;
            $scannedList = $scannedList ? preg_replace('/\n+/', ',', $scannedList) : '';
            $scannedList = $scannedList ? array_filter(array_map('trim', explode(",", $scannedList))) :
            array();
            $scannedList = array_unique($scannedList);

            if (count($scannedList) != $total_imei) {
                echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                echo '<div class="alert alert-error">Failed - IMEI scan ( ' . (count($scannedList)) . ' ) - Imei scanned - ( ' .
                $total_imei . ' ) - Imei required - Please scan enough!</div>';
                exit;
            }

                // check lock imei
            $QImeiLock = new Application_Model_ImeiLock();
            $getImeiLock = $QImeiLock->checkImeiLock($scannedList);
                // $listImeiLock = '';
                // foreach ($getImeiLock as $key => $value) {
                //     if($key == 0){
                //         $listImeiLock = $value['imei_log'];
                //     }else{
                //         $listImeiLock .= ','. $value['imei_log'];
                //     }
                // }
                // if($listImeiLock){
                //     // exit('<div class="alert alert-danger">IMEI Locked<br>' . $listImeiLock .
                //     //     '<br></div>');

                //     echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                //     echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                //     echo '<div class="alert alert-error">Failed - IMEI Locked: ' . $listImeiLock . '</div>';
                //     exit;
                // }

            $getCheckImei = $QChangeSalesImei->getImeiChangeSalesImei($scannedList);
            $check_error_sn = '';

            foreach ($getCheckImei as $key => $value) {

                if($key == 0){
                    $check_error_sn = $value['imei'];
                }else{
                    $check_error_sn .= ',' . $value['imei'];
                }
            }

            if($check_error_sn){
                echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                echo '<div class="alert alert-error">Failed - List Imei On Changing: ' . $check_error_sn . '</div>';
                exit;
            }

            foreach ($scannedList as $sn) {

                if (!preg_match('/^[0-9]{15}$/', $sn)) {
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - Invalid SN: ' . $sn . '</div>';
                    exit;
                }

                $whereImei = array();
                $whereImei[] = $QImei->getAdapter()->quoteInto('imei_sn = ?', $sn);
                $whereImei[] = $QImei->getAdapter()->quoteInto('sales_sn is null',1);
                $imeiInfo = $QImei->fetchRow($whereImei);

                if (!$imeiInfo) {
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - Invalid SN: ' . $sn . '</div>';
                    exit;
                }

                    // check warehouse
                if($imeiInfo['warehouse_id'] != $fist_warehouse){
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - Incorrect Warehouse IMEI: ' . $sn . '</div>';
                    exit;
                }

                    // check status
                if ($changeSalesOrder['type'] == FOR_DEMO and $imeiInfo['type'] != 5){
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - Invalid Type: ' . $sn . '</div>';
                    exit;
                }

                if ($changeSalesOrder['type'] == FOR_RETAILER and $imeiInfo['type'] != 1){
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - Invalid Type: ' . $sn . '</div>';
                    exit;
                }

                if ($changeSalesOrder['type'] == FOR_APK and $imeiInfo['type'] != 5){
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - Invalid Type: ' . $sn . '</div>';
                    exit;
                }

                $checked = 0;
                foreach ($imei_scan as $k => $v) {

                    if ($imeiInfo['good_id'] == $v['good_id'] && $imeiInfo['good_color'] == $v['good_color'] &&
                        $v['receive'] == 0) {
                        $imei_scan[$k]['receive'] = 1;
                    $changeSalesProductID = $imei_scan[$k]['id'];
                    $count++;
                    $checked = 1;
                    break;
                }

            }

            if ($checked == 0) 
            {
                echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                echo '<div class="alert alert-error">Failed - Product or Good is not correct Phone : ' .
                $sn . '</div>';
                exit;
            }

            $QImeiReturn = new Application_Model_ImeiReturn();
            $imei_check_return = $QImeiReturn->CheckSelloutPrice($sn);

            $data = array(
                'changed_sales_product_id' => $changeSalesProductID,
                'changed_sn' => $changeSalesProduct[0]['changed_sn'],
                'imei' => $sn,
                'new_id' => $changeSalesProduct[0]['new_id'],
                'old_id' => $changeSalesProduct[0]['old_id'],
                'price' => $imei_check_return[0]['price'],
                'cat_id' => PHONE_CAT_ID
            );

                // print_r($data); exit;

            $QChangeSalesImei->insert($data);
        }

        if ($count != $total_imei) {
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - ' . $sn .
            ' is invalid product</div>';

            exit;
        }


    }


    if (isset($SNsDigital) and $SNsDigital) {

        if (!isset($SNsDigital) or !$SNsDigital) {
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Please scan SN!</div>';
            exit;
        }


        $scannedList = $SNsDigital;
        $count = 0;
        $scannedList = $scannedList ? preg_replace('/\n+/', ',', $scannedList) : '';
        $scannedList = $scannedList ? array_filter(array_map('trim', explode(",", $scannedList))) :
        array();
        $scannedList = array_unique($scannedList);

        if (count($scannedList) != $total_digital) {
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - IMEI scan ( ' . (count($scannedList)) . ' ) - Imei scanned - ( ' .
            $total_digital . ' ) - Imei required - Please scan enough!</div>';
            exit;
        }

        $getCheckImei = $QChangeSalesImei->getImeiChangeSalesImei($scannedList);
        $check_error_sn = '';
        foreach ($getCheckImei as $key => $value) {
            if($key == 0){
                $check_error_sn = $value['imei'];
            }else{
                $check_error_sn .= ',' . $value['imei'];
            }
        }
        if($check_error_sn){
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - List Imei On Changing: ' . $check_error_sn . '</div>';
            exit;
        }

        foreach ($scannedList as $sn) {
            if (!preg_match('/^[0-9a-zA-Z]{16}$/', $sn)) {
                echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                echo '<div class="alert alert-error">Failed - Invalid SN: ' . $sn . '</div>';

                exit;
            }

            $whereDigitalSn = $QDigitalSn->getAdapter()->quoteInto('sn = ?', $sn);
            $Info = $QDigitalSn->fetchRow($whereDigitalSn);
            if (!$Info) {
                echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                echo '<div class="alert alert-error">Failed - Invalid SN: ' . $sn . '</div>';

                exit;
            }

            $checked = 0;

                    //ki?m tra imei trong list c� kh?p v?i m�u v� id
            foreach ($digital_scan as $k => $v) {

                if ($Info['good_id'] == $v['good_id'] && $Info['good_color'] == $v['good_color'] &&
                    $v['receive'] == 0) {
                    $digital_scan[$k]['receive'] = 1;
                $changeSalesProductID = $digital_scan[$k]['id'];
                $count++;
                $checked = 1;
                break;
            }

        }

        if ($checked == 0) {
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Product or Good Color is not correct : ' .
            $sn . '</div>';
            exit;
        }


        if ($Info['out_date']) {
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - was OUT: ' . $sn . '</div>';

            exit;
        }

        $get_price = $QImeiReturn->CheckSelloutPrice($sn);

        $data = array(
            'changed_sales_product_id' => $changeSalesProductID,
            'changed_sn' => $changeSalesProduct[0]['changed_sn'],
            'imei' => $sn,
            'new_id' => $changeSalesProductID[0]['new_id'],
            'old_id' => $changeSalesProductID[0]['old_id'],
            'price' => $get_price[0]['price'],
            'cat_id' => DIGITAL_CAT_ID
        );

        // print_r($data); exit;


        $QChangeSalesImei->insert($data);
    }

    if ($count != $total_digital) {
        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
        echo '<div class="alert alert-error">Failed - ' . $sn .
        ' is invalid product</div>';

        exit;
    }

}

            // update
$whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
$QChangeSalesOrder->update(array(
    'status' => CHANGE_ORDER_STATUS_SCANNED_OUT,
    'scanned_out_at' => date('Y-m-d H:i:s'),
    'scanned_out_by' => $userStorage->id,
), $whereChangeSalesOrder);

}

$info = 'CHANGE ORDER - SCANNED OUT: ' . $changeSalesOrder['changed_sn'];
$QLog->insert(array(
    'info' => $info,
    'user_id' => $userStorage->id,
    'ip_address' => $ip,
    'time' => date('Y-m-d H:i:s'),
));

$db->commit();

$flashMessenger->setNamespace('success')->addMessage('Done!');

        // echo '<script>parent.location.href="/warehouse/change-sales-list"</script>';
echo '<script>parent.location.href="/warehouse/change-sales-confirm?id='.$id.'"</script>';
exit;

}
catch (exception $e) {
    $db->rollback();

    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
    echo '<div class="alert alert-error">Failed - ' . $e->getMessage() . '</div>';
    exit;
}
}

if ($id) {
    if (!$changeSalesOrder) {
        $flashMessenger->setNamespace('error')->addMessage('Change Order is invalid!');
        $this->redirect('/warehouse/change-sales-list');
    }

    if (!in_array($changeSalesOrder['status'], array(CHANGE_ORDER_STATUS_SCANNED_OUT,
        CHANGE_ORDER_STATUS_PENDING))) {
        $flashMessenger->setNamespace('error')->addMessage('Status is invalid!');
        $this->redirect('/warehouse/change-sales-list');
    }
}

if ($id) {
    $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('changed_id = ?',
        $id);
    $changeSalesProduct = $QChangeSalesProduct->fetchAll($whereChangeSalesProduct);

    $changeSalesImeisList = $changeSalesImeisReceivedList = $changeSalesIotImeisReceivedList= $changeSalesImeisLostList = $changeSalesIotImeisLostList = $changeSalesIotList = array();
    $changeSalesImeisDigitalList = $changeSalesImeisReceivedDigitalList = $changeSalesImeisLostDigitalList = array();


    if ($changeSalesProduct->count()) {

        foreach ($changeSalesProduct as $item) {
            $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?',
                $item['id']);
            $changeSalesImeis = $QChangeSalesImei->fetchAll($whereChangeSalesImei);

            if ($changeSalesImeis->count()) {
                foreach ($changeSalesImeis as $im) {
                    if ($im['cat_id'] == PHONE_CAT_ID) {
                        if ($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                            $changeSalesIotImeisReceivedList[] = $im['imei'];
                        else
                            $changeSalesImeisLostList[] = $im['imei'];

                        $changeSalesImeisList[] = $im['imei'];
                    }
                    //edit khuan

                    if($im['cat_id'] == IOT_CAT_ID){
                        if($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                            $changeSalesIotImeisReceivedList[] = $im['imei'];
                        else
                            $changeSalesIotImeisLostList = $im['imei'];

                        $changeSalesIotList[] = $im['imei'];
                    }

                    //end
                    //end

                    if ($im['cat_id'] == DIGITAL_CAT_ID) {
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
    'type',
    'sn_iot_receives',
);

$this->view->hideFields = array(
    'sns_receive',
    'sn_iot_receive',
    'sn_iot_lost',
    'sns_lost',
    'num_receive',
    'num_lost',
    'remove-sales',
    'sns_digital_receive',
    'sns_digital_lost');
