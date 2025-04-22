<?php
set_time_limit(0);
//print_r($_POST);die;
$sn = $this->getRequest()->getParam('sn');
$url = HOST . 'warehouse/product-out-print?sn=' . $sn;
$this->_helper->layout()->disableLayout(true);
$this->_helper->viewRenderer->setNoRender(true);

date_default_timezone_set("Asia/Bangkok");

$back_url = HOST . 'warehouse/out';

try {

    $db = Zend_Registry::get('db');

    $db->beginTransaction();

    $date_now = date('Y-m-d H:i:s');

    if ($this->getRequest()->getMethod() == 'POST'){
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        echo '<link href="/css/bootstrap.min.css" rel="stylesheet">';

        if (!$sn)
            exit('<div class="alert alert-error">Missing Sales Number.</div>');

        // kiem tra dau vao cac imei & digital sn
        $a_submitted_imeis = $this->getRequest()->getParam('imei');
        $a_submitted_digital_sns = $this->getRequest()->getParam('digital_sn');
        $a_submitted_ilike_sns = $this->getRequest()->getParam('ilike_sn');

        $a_submitted_iot = $this->getRequest()->getParam('imei_iot');

        $QMarket = new Application_Model_Market();
        $QPO = new Application_Model_Po();
        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QImei = new Application_Model_Imei();
        $QDigitalSn = new Application_Model_DigitalSn();
        $QGoodSn = new Application_Model_GoodSn();
        $QWarehouse = new Application_Model_Warehouse();
        $QGoodCategory = new Application_Model_GoodCategory();
        $QWarehouseProduct = new Application_Model_WarehouseProduct();
        $QImeiReturn = new Application_Model_ImeiReturn();
        $QDistributor = new Application_Model_Distributor();

        $goods_cached = $QGood->get_cache();
        $good_colors_cached = $QGoodColor->get_cache();
        $warehouses_cached = $QWarehouse->get_cache();

        $where = array();
        $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
        $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

        $m = $QMarket->fetchRow($where);

        // print_r($m); die();

        if (!$m) {
            echo '<div class="alert alert-error">Order Number not found</div>';
            exit;
        }

        $sales_out = $QMarket->fetchAll($where);

        $total_imei_scanned_before = $total_digital_scanned_before = $total_ilike_scanned_before =
            $total_imei = $total_digital = $total_ilike = 0;
        $order_has_phone = $order_has_iot = $order_has_digital = $order_has_ilike = false;

        $a_ordered_phones = $a_ordered_digital = $a_ordered_ilike = $a_ordered_iot = $a_submitted_phones_group =
            $a_submitted_digital_group = $a_submitted_ilike_group = $a_submitted_iot_group = $a_order_phones_group = $a_order_iot_group =
            $a_order_digital_group = $a_order_ilike_group = $a_imei_success = $a_digital_sn_success =
            $a_ilike_sn_success = $a_imei_errors = $a_digital_sn_errors = $a_ilike_sn_errors =
            array();

        // check đơn hàng có được confirm out chưa
        foreach ($sales_out as $item) {
            if ($item['outmysql_time'])
                exit('<div class="alert alert-error">This Order was confirmed OUT already!</div>');

            //check đơn hàng có được payment chưa
            //echo $userStorage->warehouse_type;
            if($userStorage->warehouse_type !='3'){
                if (!$item['pay_time']){
                    exit('<div class="alert alert-error">This Order was not confirmed payment yet!</div>');
                }

                if (!$item['shipping_yes_time']){
                    exit('<div class="alert alert-error">This Order was not confirmed to Warehouse proccess yet!</div>');
                }
            }



            if ($item['cat_id'] == PHONE_CAT_ID) {

                // tinh tong so imei cua don hang
                $total_imei += $item['num'];

                $order_has_phone = true;
                // get list imei
                $where = array();
                $where[] = $QImei->getAdapter()->quoteInto('sales_id = ?', $item['id']);
                $where[] = $QImei->getAdapter()->quoteInto('out_date IS NULL', null);
                $d = $QImei->fetchAll($where);

                $scanned_before = array();
                if ($d->count()) {
                    $scanned_before = $d->toArray();
                    $total_imei_scanned_before += $d->count();
                }

                // tinh so phone cua order theo group
                if (isset($a_order_phones_group[$item['good_id']][$item['good_color']]) and $a_order_phones_group[$item['good_id']][$item['good_color']])
                    $a_order_phones_group[$item['good_id']][$item['good_color']] += $item['num'];
                else
                    $a_order_phones_group[$item['good_id']][$item['good_color']] = $item['num'];

                $a_ordered_phones[] = array(
                    'id' => $item['id'],
                    'price' => $item['price'],
                    'd_id' => $item['d_id'],
                    'good_id' => $item['good_id'],
                    'good_color' => $item['good_color'],
                    'store_id'  => $item['store_id'],
                    'num' => $item['num'],
                    'submitted' => array(),
                    'scanned_before' => $scanned_before,
                    );

            } elseif ($item['cat_id'] == DIGITAL_CAT_ID) {

                // tinh tong so digital sn cua don hang
                $total_digital += $item['num'];

                $order_has_digital = true;
                // get list digital sn
                $where = array();
                $where[] = $QDigitalSn->getAdapter()->quoteInto('sales_id = ?', $item['id']);
                $where[] = $QDigitalSn->getAdapter()->quoteInto('out_date IS NULL', null);
                $d = $QDigitalSn->fetchAll($where);
                $scanned_before = array();
                if ($d->count()) {
                    $scanned_before = $d->toArray();
                    $total_digital_scanned_before += $d->count();
                }

                // tinh so digital cua order theo group
                if (isset($a_order_digital_group[$item['good_id']][$item['good_color']]) and $a_order_digital_group[$item['good_id']][$item['good_color']])
                    $a_order_digital_group[$item['good_id']][$item['good_color']] += $item['num'];
                else
                    $a_order_digital_group[$item['good_id']][$item['good_color']] = $item['num'];

                $a_ordered_digital[] = array(
                    'id' => $item['id'],
                    'price' => $item['price'],
                    'd_id' => $item['d_id'],
                    'good_id' => $item['good_id'],
                    'good_color' => $item['good_color'],
                    'store_id'  => $item['store_id'],
                    'num' => $item['num'],
                    'submitted' => array(),
                    'scanned_before' => $scanned_before,
                    );
                
            } elseif ($item['cat_id'] == IOT_CAT_ID) {

                   // tinh tong so imei cua don hang
                $total_imei += $item['num'];

                $order_has_iot = true;
                // get list imei
                $where = array();
                $where[] = $QImei->getAdapter()->quoteInto('sales_id = ?', $item['id']);
                $where[] = $QImei->getAdapter()->quoteInto('out_date IS NULL', null);
                $d = $QImei->fetchAll($where);
                $scanned_before = array();
                if ($d->count()) {
                    $scanned_before = $d->toArray();
                    $total_imei_scanned_before += $d->count();
                }

                // tinh so phone cua order theo group
                if (isset($a_order_iot_group[$item['good_id']][$item['good_color']]) and $a_order_iot_group[$item['good_id']][$item['good_color']])
                    $a_order_iot_group[$item['good_id']][$item['good_color']] += $item['num'];
                else
                    $a_order_iot_group[$item['good_id']][$item['good_color']] = $item['num'];

                $a_ordered_iot[] = array(
                    'id' => $item['id'],
                    'price' => $item['price'],
                    'd_id' => $item['d_id'],
                    'good_id' => $item['good_id'],
                    'good_color' => $item['good_color'],
                    'store_id'  => $item['store_id'],
                    'num' => $item['num'],
                    'submitted' => array(),
                    'scanned_before' => $scanned_before,
                    );

            } elseif ($item['cat_id'] == ILIKE_CAT_ID) {

                // tinh tong so ilike sn cua don hang
                $total_ilike += $item['num'];

                $order_has_ilike = true;
                // get list ilike sn
                $where = array();
                $where[] = $QGoodSn->getAdapter()->quoteInto('sales_id = ?', $item['id']);
                $where[] = $QGoodSn->getAdapter()->quoteInto('out_date IS NULL', null);
                $d = $QGoodSn->fetchAll($where);
                $scanned_before = array();
                if ($d->count()) {
                    $scanned_before = $d->toArray();
                    $total_ilike_scanned_before += $d->count();
                }

                // tinh so ilike cua order theo group
                if (isset($a_order_ilike_group[$item['good_id']][$item['good_color']]) and $a_order_ilike_group[$item['good_id']][$item['good_color']])
                    $a_order_ilike_group[$item['good_id']][$item['good_color']] += $item['num'];
                else
                    $a_order_ilike_group[$item['good_id']][$item['good_color']] = $item['num'];

                $a_ordered_ilike[] = array(
                    'id' => $item['id'],
                    'price' => $item['price'],
                    'd_id' => $item['d_id'],
                    'good_id' => $item['good_id'],
                    'good_color' => $item['good_color'],
                    'num' => $item['num'],
                    'submitted' => array(),
                    'scanned_before' => $scanned_before,
                    );
            }

        }

        // bat dau check tung imei
        // kiem tra neu co submit imei
        if ($order_has_phone) {

            if (!$a_submitted_imeis)
                exit('<div class="alert alert-warning">No IMEI Input.</div>');

            $imei_list = trim($a_submitted_imeis);
            $imei_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $imei_list);
            $imei_list = explode("\n", $imei_list);
            $imei_list = array_filter($imei_list);

            if (!($imei_list and is_array($imei_list)))
                exit('<div class="alert alert-danger">No IMEI Input.</div>');

            if ($total_imei < count($imei_list))
                exit('<div class="alert alert-danger">Redundant (' . (count($imei_list) - $total_imei) .
                    ') IMEIs.</div>');

            if ($total_imei < (count($imei_list) + $total_imei_scanned_before))
                exit('<div class="alert alert-danger">Redundant (' . ((count($imei_list) + $total_imei_scanned_before) -
                    $total_imei) . ') IMEIs.</div>');

            if (count(array_unique($imei_list)) < count($imei_list)) {
                echo '<div class="alert alert-danger">Error</div>';
                echo '<p><strong>List of duplicated IMEIs</strong></p>';
                echo '<textarea rows="13">';

                $check_duplicate = array_count_values($imei_list);
                $printed = false;
                foreach ($check_duplicate as $i => $c) {
                    if ($c > 1) {
                        echo $i . "\n";
                    }
                }
                echo '</textarea>';

                echo '<ul>';
                foreach ($check_duplicate as $i => $c) {
                    if ($c > 1)
                        echo '<li><strong>' . $i . '</strong> | ' . $c . ' times</li>';
                }
                echo '</ul>';
                exit;
            }

            // check lock imei
            $QImeiLock = new Application_Model_ImeiLock();
            $getImeiLock = $QImeiLock->checkImeiLock($imei_list);
            $listImeiLock = '';
            foreach ($getImeiLock as $key => $value) {
                if($key == 0){
                    $listImeiLock = $value['imei_log'];
                }else{
                    $listImeiLock .= '<br>'. $value['imei_log'];
                }
            }
            if($listImeiLock){
                exit('<div class="alert alert-danger">IMEI Locked<br>' . $listImeiLock .
                    '<br></div>');
            }

            foreach ($imei_list as $imei) {

                // check format
                if (!preg_match('/^[0-9]{15}$/', $imei)) {
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Wrong Format',
                        );
                    continue;
                }

                // check co nam trong bang IMEI
                $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);

                $imei_info = $QImei->fetchRow($where);

                if (!$imei_info) {
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Not found',
                        );
                    continue;
                }

                // neu IMEI nay la cu
                if ($imei_info['sales_sn'] == $sn) {
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Scanned Already',
                        );
                    continue;
                }

                // check co nam trong kho nay
                if ($imei_info['warehouse_id'] != $m['warehouse_id']) {
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Not In WH',
                        );
                    continue;
                }

                // check co xuat kho chua
                if ($imei_info['out_date']) {
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Exported',
                        );
                    continue;
                }

                // check status
                if ($imei_info['status'] != 1) {
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Unable to export',
                        );
                    continue;
                }

                // check status
                if ($m['type'] == FOR_DEMO and $imei_info['type'] != 5){
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Mismatch IMEI type',
                    );
                    continue;
                }

                if ($m['type'] == FOR_RETAILER and $imei_info['type'] != 1){
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Mismatch IMEI type',
                    );
                    continue;
                }

                if ($m['type'] == FOR_APK and $imei_info['type'] != 2){
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Mismatch IMEI type',
                    );
                    continue;
                }

                if ($m['type'] == FOR_STAFFS and $imei_info['type'] != 3){
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Mismatch IMEI type',
                    );
                    continue;
                }

                // tinh tong so imei da submit theo group
                if (isset($a_submitted_phones_group[$imei_info['good_id']][$imei_info['good_color']]) and
                    $a_submitted_phones_group[$imei_info['good_id']][$imei_info['good_color']])
                    $a_submitted_phones_group[$imei_info['good_id']][$imei_info['good_color']]++;
                else
                    $a_submitted_phones_group[$imei_info['good_id']][$imei_info['good_color']] = 1;

                for ($i = 0; $i < count($a_ordered_phones); $i++) {

                    // boc bo vao submitted
                    if ($a_ordered_phones[$i]['good_id'] == $imei_info['good_id'] and $a_ordered_phones[$i]['good_color'] ==
                        $imei_info['good_color']) { // neu dung san pham do thi bat dau

                        // neu quota con thi con nhet duoc
                        $quota = $a_ordered_phones[$i]['num'] - (count($a_ordered_phones[$i]['submitted']) +
                            count($a_ordered_phones[$i]['scanned_before']));

                        if (!$quota)
                            continue;
                        else {
                            $a_ordered_phones[$i]['submitted'][] = $imei;
                            break;
                        }
                    }

                }
            }

            // kiem tra nhap co lech, VD: 4 R1 den / 6 R1 trang trong khi don hang la 5 / 5
            foreach ($a_order_phones_group as $good_id => $item) {
                foreach ($item as $good_color => $val) {
                    if (isset($a_submitted_phones_group[$good_id][$good_color])) {

                        $total_scanned_out_imei_before = $QMarket->count_out_imei($sn, $good_id, $good_color);

                        // neu so cu + so nhap ma lon hon so cua don hang thi dung ngay
                        if ($a_submitted_phones_group[$good_id][$good_color] + $total_scanned_out_imei_before >
                            $val) {
                            exit('<div class="alert alert-error">' . $goods_cached[$good_id] . ' / ' . $good_colors_cached[$good_color] .
                                ' Ordered: ' . $val . ' / Scanned + Old: ' . $a_submitted_phones_group[$good_id][$good_color] .
                                ' + ' . $total_scanned_out_imei_before . '</div>');
                        }

                    }
                }
            }

            // update
            foreach ($a_ordered_phones as $item) {

                foreach ($item['submitted'] as $d) {

                    $data = array(
                        'sales_sn' => $sn,
                        'sales_id' => $item['id'],
                        'out_user' => $userStorage->id,
                        'out_price' => $item['price'],
                        'distributor_id' => $item['d_id'],
                        'store_id' => $item['store_id'],
                        'out_date' => date('Y-m-d H:i:s'),
                        'scanning' => 1,
                        );

                    $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $d);

                    $r = $QImei->update($data, $where);

                    if ($r > 0)
                        $a_imei_success[] = $d;
                    else {
                        $a_imei_errors[$d] = array('sn' => $d, 'error' => 'Update failed');
                    }
                }
            }

            // nhung loi khong biet
            $unknown_error_list = array_diff($imei_list, $a_imei_success);

            if (is_array($unknown_error_list) and $unknown_error_list) {
                foreach ($unknown_error_list as $d) {
                    if (!isset($a_imei_errors[$d])) {
                        $a_imei_errors[$d] = array('sn' => $d, 'error' => 'Update failed');
                    }
                }
            }
        }

        if ($order_has_iot) {

            if (!$a_submitted_iot)
                exit('<div class="alert alert-warning">No IMEI Input.</div>');

            $imei_list = trim($a_submitted_iot);
            $imei_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $imei_list);
            $imei_list = explode("\n", $imei_list);
            $imei_list = array_filter($imei_list);

            if (!($imei_list and is_array($imei_list)))
                exit('<div class="alert alert-danger">No IMEI Input.</div>');

            if ($total_imei < count($imei_list))
                exit('<div class="alert alert-danger">Redundant (' . (count($imei_list) - $total_imei) .
                    ') IMEIs.</div>');

            if ($total_imei < (count($imei_list) + $total_imei_scanned_before))
                exit('<div class="alert alert-danger">Redundant (' . ((count($imei_list) + $total_imei_scanned_before) -
                    $total_imei) . ') IMEIs.</div>');

            if (count(array_unique($imei_list)) < count($imei_list)) {
                echo '<div class="alert alert-danger">Error</div>';
                echo '<p><strong>List of duplicated IMEIs</strong></p>';
                echo '<textarea rows="13">';

                $check_duplicate = array_count_values($imei_list);
                $printed = false;
                foreach ($check_duplicate as $i => $c) {
                    if ($c > 1) {
                        echo $i . "\n";
                    }
                }
                
                echo '</textarea>';

                echo '<ul>';
                foreach ($check_duplicate as $i => $c) {
                    if ($c > 1)
                        echo '<li><strong>' . $i . '</strong> | ' . $c . ' times</li>';
                }
                echo '</ul>';
                exit;
            }

            // check lock imei
            $QImeiLock = new Application_Model_ImeiLock();
            $getImeiLock = $QImeiLock->checkImeiLock($imei_list);
            $listImeiLock = '';
            foreach ($getImeiLock as $key => $value) {
                if($key == 0){
                    $listImeiLock = $value['imei_log'];
                }else{
                    $listImeiLock .= '<br>'. $value['imei_log'];
                }
            }
            if($listImeiLock){
                exit('<div class="alert alert-danger">IMEI Locked<br>' . $listImeiLock .
                    '<br></div>');
            }

            foreach ($imei_list as $imei) {

                // check format
                if (!preg_match('/^[0-9a-zA-Z]{13,30}$/', $imei)) {
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Wrong Format',
                        );
                    continue;
                }

                // check co nam trong bang IMEI
                $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);

                $imei_info = $QImei->fetchRow($where);

                if (!$imei_info) {
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Not found',
                        );
                    continue;
                }

                // neu IMEI nay la cu
                if ($imei_info['sales_sn'] == $sn) {
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Scanned Already',
                        );
                    continue;
                }

                // check co nam trong kho nay
                if ($imei_info['warehouse_id'] != $m['warehouse_id']) {
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Not In WH',
                        );
                    continue;
                }

                // check co xuat kho chua
                if ($imei_info['out_date']) {
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Exported',
                        );
                    continue;
                }

                // check status
                if ($imei_info['status'] != 1) {
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Unable to export',
                        );
                    continue;
                }

                // check status
                if ($m['type'] == FOR_DEMO and $imei_info['type'] != 5){
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Mismatch IMEI type',
                    );
                    continue;
                }

                if ($m['type'] == FOR_RETAILER and $imei_info['type'] != 1){
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Mismatch IMEI type',
                    );
                    continue;
                }

                if ($m['type'] == FOR_APK and $imei_info['type'] != 2){
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Mismatch IMEI type',
                    );
                    continue;
                }

                if ($m['type'] == FOR_STAFFS and $imei_info['type'] != 3){
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Mismatch IMEI type',
                    );
                    continue;
                }

                // tinh tong so imei da submit theo group
                if (isset($a_submitted_phones_group[$imei_info['good_id']][$imei_info['good_color']]) and
                    $a_submitted_phones_group[$imei_info['good_id']][$imei_info['good_color']])
                    $a_submitted_phones_group[$imei_info['good_id']][$imei_info['good_color']]++;
                else
                    $a_submitted_phones_group[$imei_info['good_id']][$imei_info['good_color']] = 1;

                for ($i = 0; $i < count($a_ordered_iot); $i++) {

                    // boc bo vao submitted
                    if ($a_ordered_iot[$i]['good_id'] == $imei_info['good_id'] and $a_ordered_iot[$i]['good_color'] ==
                        $imei_info['good_color']) { // neu dung san pham do thi bat dau

                        // neu quota con thi con nhet duoc
                        $quota = $a_ordered_iot[$i]['num'] - (count($a_ordered_iot[$i]['submitted']) +
                            count($a_ordered_iot[$i]['scanned_before']));

                        if (!$quota)
                            continue;
                        else {
                            $a_ordered_iot[$i]['submitted'][] = $imei;
                            break;
                        }
                    }

                }
            }

            // kiem tra nhap co lech, VD: 4 R1 den / 6 R1 trang trong khi don hang la 5 / 5
            foreach ($a_order_iot_group as $good_id => $item) {
                foreach ($item as $good_color => $val) {
                    if (isset($a_submitted_iot_group[$good_id][$good_color])) {

                        $total_scanned_out_imei_before = $QMarket->count_out_imei($sn, $good_id, $good_color);

                        // neu so cu + so nhap ma lon hon so cua don hang thi dung ngay
                        if ($a_submitted_iot_group[$good_id][$good_color] + $total_scanned_out_imei_before >
                            $val) {
                            exit('<div class="alert alert-error">' . $goods_cached[$good_id] . ' / ' . $good_colors_cached[$good_color] .
                                ' Ordered: ' . $val . ' / Scanned + Old: ' . $a_submitted_iot_group[$good_id][$good_color] .
                                ' + ' . $total_scanned_out_imei_before . '</div>');
                        }

                    }
                }
            }

            // update
            foreach ($a_ordered_iot as $item) {

                foreach ($item['submitted'] as $d) {

                    $data = array(
                        'sales_sn' => $sn,
                        'sales_id' => $item['id'],
                        'out_user' => $userStorage->id,
                        'out_price' => $item['price'],
                        'distributor_id' => $item['d_id'],
                        'store_id' => $item['store_id'],
                        'out_date' => date('Y-m-d H:i:s'),
                        'scanning' => 1,
                        );

                    $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $d);

                    $r = $QImei->update($data, $where);

                    if ($r > 0)
                        $a_imei_success[] = $d;
                    else {
                        $a_imei_errors[$d] = array('sn' => $d, 'error' => 'Update failed');
                    }
                }
            }

            // nhung loi khong biet
            $unknown_error_list = array_diff($imei_list, $a_imei_success);

            if (is_array($unknown_error_list) and $unknown_error_list) {
                foreach ($unknown_error_list as $d) {
                    if (!isset($a_imei_errors[$d])) {
                        $a_imei_errors[$d] = array('sn' => $d, 'error' => 'Update failed');
                    }
                }
            }
        }


        if ($order_has_digital) {

            if (!$a_submitted_digital_sns)
                exit('<div class="alert alert-warning">No Digital SN Input.</div>');

            $digi_list = trim($a_submitted_digital_sns);
            $digi_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $digi_list);
            $digi_list = explode("\n", $digi_list);
            $digi_list = array_filter($digi_list);

            if (!($digi_list and is_array($digi_list)))
                exit('<div class="alert alert-warning">No Digital SN Input.</div>');

            if ($total_digital < count($digi_list))
                exit('<div class="alert alert-danger">Redundant (' . (count($digi_list) - $total_digital) .
                    ') Digital SNs.</div>');

            if ($total_digital < (count($digi_list) + $total_digital_scanned_before))
                exit('<div class="alert alert-danger">Redundant (' . ((count($digi_list) + $total_digital_scanned_before) -
                    $total_digital) . ') Digital SNs.</div>');

            if (count(array_unique($digi_list)) < count($digi_list)) {
                echo '<div class="alert alert-danger">Error</div>';
                echo '<p><strong>List of duplicated Digital SNs</strong></p>';
                echo '<textarea rows="13">';

                $check_duplicate = array_count_values($digi_list);
                $printed = false;
                foreach ($check_duplicate as $i => $c) {
                    if ($c > 1) {
                        echo $i . "\n";
                    }
                }
                echo '</textarea>';

                echo '<ul>';
                foreach ($check_duplicate as $i => $c) {
                    if ($c > 1)
                        echo '<li><strong>' . $i . '</strong> | ' . $c . ' times</li>';
                }
                echo '</ul>';
                exit;
            }

            foreach ($digi_list as $digi) {

                // check format
                if (!preg_match('/^[0-9a-zA-Z]{16}$/', $digi)) {
                    $a_digital_sn_errors[$digi] = array(
                        'sn' => $digi,
                        'error' => 'Wrong Format',
                        );
                    continue;
                }

                // check co nam trong bang $digi
                $where = $QDigitalSn->getAdapter()->quoteInto('sn = ?', $digi);

                $digi_info = $QDigitalSn->fetchRow($where);

                if (!$digi_info) {
                    $a_digital_sn_errors[$digi] = array(
                        'sn' => $digi,
                        'error' => 'Not found',
                        );
                    continue;
                }

                // neu IMEI nay la cu
                if ($digi_info['sales_sn'] == $sn) {
                    $a_digital_sn_errors[$digi] = array(
                        'sn' => $digi,
                        'error' => 'Scanned Already',
                        );
                    continue;
                }

                // check co nam trong kho nay
                if ($digi_info['warehouse_id'] != $m['warehouse_id']) {
                    $a_digital_sn_errors[$digi] = array(
                        'sn' => $digi,
                        'error' => 'Not In WH',
                        );
                    continue;
                }

                // check co xuat kho chua
                if ($digi_info['out_date']) {
                    $a_digital_sn_errors[$digi] = array(
                        'sn' => $digi,
                        'error' => 'Exported',
                        );
                    continue;
                }

                // check status
                if ($digi_info['status'] != 1) {
                    $a_digital_sn_errors[$digi] = array(
                        'sn' => $digi,
                        'error' => 'Unable to export',
                        );
                    continue;
                }

                // tinh tong so digital sn da submit theo group
                if (isset($a_submitted_digital_group[$digi_info['good_id']][$digi_info['good_color']]) and
                    $a_submitted_digital_group[$digi_info['good_id']][$digi_info['good_color']])
                    $a_submitted_digital_group[$digi_info['good_id']][$digi_info['good_color']]++;
                else
                    $a_submitted_digital_group[$digi_info['good_id']][$digi_info['good_color']] = 1;

                for ($i = 0; $i < count($a_ordered_digital); $i++) {

                    // boc bo vao submitted
                    if ($a_ordered_digital[$i]['good_id'] == $digi_info['good_id'] and $a_ordered_digital[$i]['good_color'] ==
                        $digi_info['good_color']) { // neu dung san pham do thi bat dau

                        // neu quota con thi con nhet duoc
                        $quota = $a_ordered_digital[$i]['num'] - (count($a_ordered_digital[$i]['submitted']) +
                            count($a_ordered_digital[$i]['scanned_before']));

                        if (!$quota)
                            continue;

                        $a_ordered_digital[$i]['submitted'][] = $digi;

                    }

                }
            }

            // kiem tra nhap co lech, VD: 4 R1 den / 6 R1 trang trong khi don hang la 5 / 5
            foreach ($a_order_digital_group as $good_id => $item) {
                foreach ($item as $good_color => $val) {
                    if (isset($a_submitted_digital_group[$good_id][$good_color])) {

                        $total_scanned_out_digital_sn_before = $QMarket->count_out_digital($sn, $good_id,
                            $good_color);

                        // neu so cu + so nhap ma lon hon so cua don hang thi dung ngay
                        if ($a_submitted_digital_group[$good_id][$good_color] + $total_scanned_out_digital_sn_before >
                            $val) {
                            exit('<div class="alert alert-error">' . $goods_cached[$good_id] . ' / ' . $good_colors_cached[$good_color] .
                                ' Ordered: ' . $val . ' / Scanned + Old: ' . $a_submitted_digital_group[$good_id][$good_color] .
                                ' + ' . $total_scanned_out_digital_sn_before . '</div>');
                        }

                    }
                }
            }

            // update
            foreach ($a_ordered_digital as $item) {

                foreach ($item['submitted'] as $d) {

                    $data = array(
                        'sales_sn' => $sn,
                        'sales_id' => $item['id'],
                        'out_user' => $userStorage->id,
                        'out_price' => $item['price'],
                        'distributor_id' => $item['d_id'],
                        'store_id' => $item['store_id'],
                        'out_date' => date('Y-m-d H:i:s'),
                        );

                    $where = $QDigitalSn->getAdapter()->quoteInto('sn = ?', $d);

                    $r = $QDigitalSn->update($data, $where);

                    if ($r > 0)
                        $a_digital_sn_success[] = $d;
                    else {
                        $a_digital_sn_errors[$d] = array('sn' => $d, 'error' => 'Update failed');
                    }
                }
            }

            // nhung loi khong biet
            $unknown_error_list = array_diff($digi_list, $a_digital_sn_success);

            if (is_array($unknown_error_list) and $unknown_error_list) {
                foreach ($unknown_error_list as $d) {
                    if (!isset($a_digital_sn_errors[$d]))
                        $a_digital_sn_errors[$d] = array('sn' => $d, 'error' => 'Update failed');
                }
            }

        }

        if ($order_has_ilike) {

            if (!$a_submitted_ilike_sns)
                exit('<div class="alert alert-warning">No Good SN Input.</div>');

            $ilike_list = trim($a_submitted_ilike_sns);
            $ilike_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $ilike_list);
            $ilike_list = explode("\n", $ilike_list);
            $ilike_list = array_filter($ilike_list);

            if (!($ilike_list and is_array($ilike_list)))
                exit('<div class="alert alert-warning">No Good SN Input.</div>');

            if ($total_ilike < count($ilike_list))
                exit('<div class="alert alert-danger">Redundant (' . (count($ilike_list) - $total_ilike) .
                    ') Good SNs.</div>');

            if ($total_ilike < (count($ilike_list) + $total_ilike_scanned_before))
                exit('<div class="alert alert-danger">Redundant (' . ((count($ilike_list) + $total_ilike_scanned_before) -
                    $total_ilike) . ') Good SNs.</div>');

            if (count(array_unique($ilike_list)) < count($ilike_list)) {
                echo '<div class="alert alert-danger">Error</div>';
                echo '<p><strong>List of duplicated Good SNs</strong></p>';
                echo '<textarea rows="13">';

                $check_duplicate = array_count_values($ilike_list);
                $printed = false;
                foreach ($check_duplicate as $i => $c) {
                    if ($c > 1) {
                        echo $i . "\n";
                    }
                }
                echo '</textarea>';

                echo '<ul>';
                foreach ($check_duplicate as $i => $c) {
                    if ($c > 1)
                        echo '<li><strong>' . $i . '</strong> | ' . $c . ' times</li>';
                }
                echo '</ul>';
                exit;
            }

            foreach ($ilike_list as $ilike) {

                // check format
                if (!preg_match('/^[0-9a-zA-Z]{15}$/', $ilike)) {
                    $a_ilike_sn_errors[$ilike] = array(
                        'sn' => $ilike,
                        'error' => 'Wrong Format',
                        );
                    continue;
                }

                // check co nam trong bang $digi
                $where = $QGoodSn->getAdapter()->quoteInto('sn = ?', $ilike);

                $ilike_info = $QGoodSn->fetchRow($where);

                if (!$ilike_info) {
                    $a_ilike_sn_errors[$ilike] = array(
                        'sn' => $ilike,
                        'error' => 'Not found',
                        );
                    continue;
                }

                // neu IMEI nay la cu
                if ($ilike_info['sales_sn'] == $sn) {
                    $a_ilike_sn_errors[$ilike] = array(
                        'sn' => $ilike,
                        'error' => 'Scanned Already',
                        );
                    continue;
                }

                // check co nam trong kho nay
                if ($ilike_info['warehouse_id'] != $m['warehouse_id']) {
                    $a_ilike_sn_errors[$ilike] = array(
                        'sn' => $ilike,
                        'error' => 'Not In WH',
                        );
                    continue;
                }

                // check co xuat kho chua
                if ($ilike_info['out_date'] or $ilike_info['sales_sn']) {
                    $a_ilike_sn_errors[$ilike] = array(
                        'sn' => $ilike,
                        'error' => 'Exported',
                        );
                    continue;
                }

                // check status
                if ($ilike_info['status'] != 1) {
                    $a_ilike_sn_errors[$ilike] = array(
                        'sn' => $ilike,
                        'error' => 'Unable to export',
                        );
                    continue;
                }

                // tinh tong so digital sn da submit theo group
                if (isset($a_submitted_ilike_group[$ilike_info['good_id']][$ilike_info['good_color']]) and
                    $a_submitted_ilike_group[$ilike_info['good_id']][$ilike_info['good_color']])
                    $a_submitted_ilike_group[$ilike_info['good_id']][$ilike_info['good_color']]++;
                else
                    $a_submitted_ilike_group[$ilike_info['good_id']][$ilike_info['good_color']] = 1;

                for ($i = 0; $i < count($a_ordered_ilike); $i++) {

                    // boc bo vao submitted
                    if ($a_ordered_ilike[$i]['good_id'] == $ilike_info['good_id'] and $a_ordered_ilike[$i]['good_color'] ==
                        $ilike_info['good_color']) { // neu dung san pham do thi bat dau

                        // neu quota con thi con nhet duoc
                        $quota = $a_ordered_ilike[$i]['num'] - (count($a_ordered_ilike[$i]['submitted']) +
                            count($a_ordered_ilike[$i]['scanned_before']));

                        if (!$quota)
                            continue;

                        $a_ordered_ilike[$i]['submitted'][] = $ilike;

                    }

                }
            }

            // kiem tra nhap co lech, VD: 4 R1 den / 6 R1 trang trong khi don hang la 5 / 5
            foreach ($a_order_ilike_group as $good_id => $item) {
                foreach ($item as $good_color => $val) {
                    if (isset($a_submitted_ilike_group[$good_id][$good_color])) {

                        $total_scanned_out_ilike_sn_before = $QMarket->count_out_digital($sn, $good_id,
                            $good_color);

                        // neu so cu + so nhap ma lon hon so cua don hang thi dung ngay
                        if ($a_submitted_ilike_group[$good_id][$good_color] + $total_scanned_out_ilike_sn_before >
                            $val) {
                            exit('<div class="alert alert-error">' . $goods_cached[$good_id] . ' / ' . $good_colors_cached[$good_color] .
                                ' Ordered: ' . $val . ' / Scanned + Old: ' . $a_submitted_ilike_group[$good_id][$good_color] .
                                ' + ' . $total_scanned_out_ilike_sn_before . '</div>');
                        }

                    }
                }
            }

            // update
            foreach ($a_ordered_ilike as $item) {

                foreach ($item['submitted'] as $d) {

                    $data = array(
                        'sales_sn' => $sn,
                        'sales_id' => $item['id'],
                        'out_user' => $userStorage->id,
                        'out_price' => $item['price'],
                        'distributor_id' => $item['d_id'],
                        'out_date' => date('Y-m-d H:i:s'),
                        );

                    $where = $QGoodSn->getAdapter()->quoteInto('sn = ?', $d);

                    $r = $QGoodSn->update($data, $where);

                    if ($r > 0)
                        $a_ilike_sn_success[] = $d;
                    else {
                        $a_ilike_sn_errors[$d] = array('sn' => $d, 'error' => 'Update failed');
                    }
                }
            }

            // nhung loi khong biet
            $unknown_error_list = array_diff($ilike_list, $a_ilike_sn_success);

            if (is_array($unknown_error_list) and $unknown_error_list) {
                foreach ($unknown_error_list as $d) {
                    if (!isset($a_ilike_sn_errors[$d]))
                        $a_ilike_sn_errors[$d] = array('sn' => $d, 'error' => 'Update failed');
                }
            }

        }

        //Tanong
        //$QWarehouse = new Application_Model_Warehouse();
        //$QWarehouse->getInvoiceNo($sn);

        //UPdate imei cho staff order
        $QStaffOrder = new Application_Model_StaffOrder();
        $result_assign = $QStaffOrder->assignImei($sn);
        if($result_assign['status'] < 0){
            exit('<div class="alert alert-error">' . $result_assign['message']. '</div>');
        }

        //commit

        $dateNow = date("Y-m-d");

        $sql_update="UPDATE warehouse.running_inv_seq AS cm
JOIN warehouse.market AS mk ON mk.sn = cm.sn
SET mk.invoice_number = cm.invoice_number
WHERE cm.inv_add_time BETWEEN '".$dateNow." 00:00:00' AND '".$dateNow." 23:59:59' and mk.invoice_time is not null and mk.`invoice_number` is null";

        $db->query($sql_update);

          //todo log
        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        $info = 'Scanned OUT: ' . $sn;

        $QLog = new Application_Model_Log();

        $QLog->insert(array(
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => date('Y-m-d H:i:s'),
            ));



        $total_scanned_out_imei = $QMarket->count_out_imei($sn);
        $total_scanned_out_digital_sn = $QMarket->count_out_digital($sn);
        $total_scanned_out_ilike_sn = $QMarket->count_out_ilike($sn);

        if ($total_scanned_out_imei == $total_imei and $total_scanned_out_digital_sn ==
            $total_digital and $total_scanned_out_ilike_sn == $total_ilike) {
            //update status don hang
            $where = array();
            $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);
            $sales_list = $QMarket->fetchAll($where);

            $sales_list_out = 0;

            foreach ($sales_list as $v) {

                // check đơn hàng có được confirm out chưa
                if ($v['outmysql_time']) {
                    exit('<div class="alert alert-danger">This Order was confirmed OUT already!</div>');
                }

                if ($v['cat_id'] == PHONE_CAT_ID) {
                    $scanned_out = $QMarket->count_out_imei($sn, $v['good_id'], $v['good_color'], $v['id']);

                    if ($scanned_out < $v['num']) {
                        exit('<div class="alert alert-danger">Please Scan out all of IMEI!</div>');
                    }
                } elseif ($v['cat_id'] == DIGITAL_CAT_ID) {
                    $scanned_out = $QMarket->count_out_digital($sn, $v['good_id'], $v['good_color'],
                        $v['id']);

                    if ($scanned_out < $v['num']) {
                        exit('<div class="alert alert-danger">Please Scan out all of Digital SN!</div>');
                    }
                } elseif ($v['cat_id'] == IOT_CAT_ID) {
                     $scanned_out = $QMarket->count_out_imei($sn, $v['good_id'], $v['good_color'], $v['id']);

                    if ($scanned_out < $v['num']) {
                        exit('<div class="alert alert-danger">Please Scan out all of IMEI!</div>');
                    }

                } elseif ($v['cat_id'] == ILIKE_CAT_ID) {
                    $scanned_out = $QMarket->count_out_ilike($sn, $v['good_id'], $v['good_color'], $v['id']);

                    if ($scanned_out < $v['num']) {
                        exit('<div class="alert alert-danger">Please Scan out all of Good SN!</div>');
                    }
                } elseif ($v['cat_id'] == ACCESS_CAT_ID) {

                    // Start : BK
                    // $where = array();
                    // $where[] = $QWarehouseProduct->getAdapter()->quoteInto('cat_id = ?', $v['cat_id']);
                    // $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_id = ?', $v['good_id']);
                    // $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_color = ?', $v['good_color']);
                    // $where[] = $QWarehouseProduct->getAdapter()->quoteInto('warehouse_id = ?', $v['warehouse_id']);

                    // $result = $QWarehouseProduct->fetchRow($where);
                    // if ($result) {
                    //     $where = $QWarehouseProduct->getAdapter()->quoteInto('id = ?', $result['id']);
                    //     $quantity = $result['quantity'] - $v['num'];
                    //     $data = array('quantity' => ($quantity > 0 ? $quantity : 0));

                    //     $QWarehouseProduct->update($data, $where);
                    // }
                    //END : BK


                    $where = array();
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('cat_id = ?', $v['cat_id']);
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_id = ?', $v['good_id']);
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_color = ?', $v['good_color']);
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('warehouse_id = ?', $v['warehouse_id']);
                    $data = array('quantity' => new Zend_Db_Expr('quantity-'. $v['num']));

                    // print_r($data);die;

                    // $check_update = 0;
                    if($v['num'] > 0){
                        // $check_update++;
                        $QWarehouseProduct->update($data, $where);
                    }

                    // if($check_update > 0){

                        $where = array();
                        $where[] = $QWarehouseProduct->getAdapter()->quoteInto('cat_id = ?', $v['cat_id']);
                        $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_id = ?', $v['good_id']);
                        $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_color = ?', $v['good_color']);
                        $where[] = $QWarehouseProduct->getAdapter()->quoteInto('warehouse_id = ?', $v['warehouse_id']);

                        $result = $QWarehouseProduct->fetchRow($where);

                        if(isset($result['quantity']) && $result['quantity'] < 0){
                            $db->rollback();
                            exit('<div class="alert alert-danger">Scan Out Over Item!</div>');
                        }

                    // }


                }
            }

             //Tanong
            /*
            if ($sn){
                $QWarehouse = new Application_Model_Warehouse();
                $invoice_number= $QWarehouse->getInvoiceNo($sn);
            }
            */
            $date = date('Y-m-d H:i:s');

            $is_kerry = $this->getRequest()->getParam('is_kerry');

            $kerry_status = 0;

            if(!is_null($is_kerry) && $is_kerry == 'KERRY'){
                $kerry_status = 1;
            }

            if(!is_null($is_kerry) && $is_kerry == 'J&T'){
                $kerry_status = 2;
            }

            $data = array(
                'outmysql_time' => $date,
                'outmysql_user' => $userStorage->id,
               // 'invoice_number' => $invoice_number,
                'invoice_time'  => $date,
                'is_kerry'      => $kerry_status,
                'order_status'  => 4
                );

            $where = array();

            $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);
            $rmu = $QMarket->update($data, $where);

            if($rmu) {

                $QContactDetail = new Application_Model_ContactDetail();

                $contact_data = array(
                    'bill_date' => date('Y-m-d H:i:s'),
                    'status'    => 2
                );

                $where2 = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$sn);
                $QContactDetail->update($contact_data,$where2);
            }


            $data_imei = array(
                'scanning' => null,
                );

            $where_imei = $QImei->getAdapter()->quoteInto('sales_sn = ?', $sn);

            $rr = $QImei->update($data_imei, $where_imei);

            if ($rr<0){
                throw new Exception("Scan Out Error!", 3);
                $db->rollback();
            } 

            $dateNow = date("Y-m-d");

            $sql_update="UPDATE warehouse.running_inv_seq AS cm
JOIN warehouse.market AS mk ON mk.sn = cm.sn
SET mk.invoice_number = cm.invoice_number
WHERE cm.inv_add_time BETWEEN '".$dateNow." 00:00:00' AND '".$dateNow." 23:59:59' and mk.invoice_time is not null and mk.`invoice_number` is null";

            $db->query($sql_update);

            //Tanong
            //$QWarehouse->getInvoiceNo($sn);

            //commit           

            if($kerry_status == 1){

                // $get_tracking = $this->getRequest()->getParam('tracking');
                $get_tracking = $this->convertSoToOp($m['sn_ref']);
                $get_number_of_package = $this->getRequest()->getParam('number_of_package');
                $get_weight = $this->getRequest()->getParam('weight');

                // START : Add Delivery When Send To Kerry

                $QDistributor = new Application_Model_Distributor();

                $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                $market_check = $QMarket->fetchRow($where);

                if (!$market_check) throw new Exception("Order SN ".$_sn." not exists", 2);

                if (isset($market_check['office']) && $market_check['office'] && isset($office_cache[ $market_check['office'] ]))
                    $distributor_sn = array(
                        'contact'      => $office_cache[ $market_check['office'] ]['contact'],
                        'address'      => $office_cache[ $market_check['office'] ]['address'],
                        'name'         => $office_cache[ $market_check['office'] ]['name'],
                        'phone_number' => $office_cache[ $market_check['office'] ]['phone_number'],
                        'district'     => $office_cache[ $market_check['office'] ]['district'],
                    );

                if (isset($market_check['service']) && $market_check['service'] && isset($service_cache[ $market_check['service'] ]))
                    $distributor_sn = array(
                        'contact'      => $service_cache[ $market_check['service'] ]['contact'],
                        'address'      => $service_cache[ $market_check['service'] ]['address'],
                        'name'         => $service_cache[ $market_check['service'] ]['name'],
                        'phone_number' => $service_cache[ $market_check['service'] ]['phone_number'],
                        'district'     => $service_cache[ $market_check['service'] ]['district'],
                    );

                $distributors = $market_check['d_id'];

                    if ($market_check['add_time'] > '2017-01-29') {
                        if (isset($market_check['shipping_address']) && $market_check['shipping_address']){
                            $shipping_address = $market_check['shipping_address'];

                            $shipp_add = My_Address::genAddessDeliveryNote($shipping_address);
                            // print_r($shipp_add);

                        }

                        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributors);
                        $distributor_check = $QDistributor->fetchRow($where);

                        if (!$distributor_check) throw new Exception("Distributor not exists", 3);

                        $distributor_list = array(
                            'id'       => intval($distributor_check['id']),
                            'title'    => htmlspecialchars( $distributor_check['title'], ENT_QUOTES, 'UTF-8' ),
                            //'address'  => htmlspecialchars( !empty( $distributor_check['add_tax'] ) ? My_String::trim($distributor_check['add_tax']) : $distributor_check['add'], ENT_QUOTES, 'UTF-8' ),
                            'address'  => htmlspecialchars( !empty( $shipp_add['address'] ) ? My_String::trim($shipp_add['address']) : $shipp_add['address'], ENT_QUOTES, 'UTF-8' ),
                            'name'     => htmlspecialchars( My_String::trim($distributor_check['name']), ENT_QUOTES, 'UTF-8' ),
                            'district' => intval($distributor_check['district']),
                            'phone'    => htmlspecialchars( My_String::trim($shipp_add['phone']), ENT_QUOTES, 'UTF-8' ),
                            'address_id'    => $shipp_add['address_id'],
                            // 'address_id'    => $shipp_add['address_id'],
                        );

                        // if (isset($distributor_sn)) {
                        //     $distributor_list['title']    .= ' ' . $distributor_sn['name'];
                        //     $distributor_list['address']  = $distributor_sn['address'];
                        //     $distributor_list['phone']    = $distributor_sn['phone_number'];
                        //     $distributor_list['name']     = $distributor_sn['contact'];
                        //     $distributor_list['district'] = $distributor_sn['district'];
                        // }

                    }else{
                        if (isset($market_check['delivery_address']) && $market_check['delivery_address']){
                            $delivery_address = $market_check['delivery_address'];
                        }

                        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributors);
                        $distributor_check = $QDistributor->fetchRow($where);

                        if (!$distributor_check) throw new Exception("Distributor not exists", 3);

                        $distributor_list = array(
                            'id'       => intval($distributor_check['id']),
                            'title'    => htmlspecialchars( $distributor_check['title'], ENT_QUOTES, 'UTF-8' ),
                            //'address'  => htmlspecialchars( !empty( $distributor_check['add_tax'] ) ? My_String::trim($distributor_check['add_tax']) : $distributor_check['add'], ENT_QUOTES, 'UTF-8' ),
                            'address'  => htmlspecialchars( !empty( $delivery_address ) ? My_String::trim($delivery_address) : $distributor_check['add_tax'], ENT_QUOTES, 'UTF-8' ),
                            'name'     => htmlspecialchars( My_String::trim($distributor_check['name']), ENT_QUOTES, 'UTF-8' ),
                            'district' => intval($distributor_check['district']),
                            'phone'    => htmlspecialchars( My_String::trim($distributor_check['tel']), ENT_QUOTES, 'UTF-8' ),
                        );

                        if (isset($distributor_sn)) {
                            $distributor_list['title']    .= ' ' . $distributor_sn['name'];
                            $distributor_list['address']  = $distributor_sn['address'];
                            $distributor_list['phone']    = $distributor_sn['phone_number'];
                            $distributor_list['name']     = $distributor_sn['contact'];
                            $distributor_list['district'] = $distributor_sn['district'];
                        }

                    }

                    // print_r($distributor_list);exit();
                    // print_r($m);exit();


                $QDeliveryOrder = new Application_Model_DeliveryOrder();

                if ($sn){
                    $receiver = date('YmdHis') . substr(microtime(), 2, 4);
                    $delivery_sn = $QDeliveryOrder->getDeliveryNo_Ref($receiver);
                }

                if ($type == My_Delivery_Type::Outside) {
                    $where = array();
                    $where[] = $QDeliveryOrder->getAdapter()->quoteInto('sn IS NOT NULL AND sn = ?', $delivery_sn);
                    $where[] = $QDeliveryOrder->getAdapter()->quoteInto('status <> ?', My_Delivery_Order_Status::Deleted);
                    $order_check = $QDeliveryOrder->fetchRow($where);
                    if ($order_check) throw new Exception("Duplicate Delivery Order SN: " . $delivery_sn, 5);
                }

                $warehouse = null;

                $locaton_pair = array(
                    1 => 8,
                    2 => 11,
                    3 => 10,
                    8 => 1,
                    10 => 3,
                    11 => 2
                );

                    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                    $market_check = $QMarket->fetchRow($where);

                    if (!$market_check)
                        throw new Exception("Invalid SN: " . $sn, 7);

                    if ($warehouse && $warehouse != $market_check['warehouse_id'] && $locaton_pair[$market_check['warehouse_id']] != $warehouse)
                        throw new Exception("Orders in 02 warehouses: " . $sn, 18);

                    $warehouse = $market_check['warehouse_id'];

                $distributor_id = $distributors;

                if (!$distributor_id)
                    throw new Exception("Choose distributor", 8);

                $QWarehouse = new Application_Model_Warehouse();
                $warehouse_cache = $QWarehouse->get_cache();

                if (!isset($warehouse_cache[$warehouse])) throw new Exception("Invalid warehouse", 17);

                $QDistributor = new Application_Model_Distributor();
                $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
                $distributor_check = $QDistributor->fetchRow($where);

                if (!$distributor_check)
                    throw new Exception("Invalid distributor", 5);
            /*
                if (empty($receiver))
                    throw new Exception("Receiver cannot be blank", 10);*/

                $address = $distributor_list['address'];

                if (empty($address))
                    throw new Exception("Address cannot be blank", 11);

                $district = $distributor_list['district'];

                if (!intval($district))
                    throw new Exception("District cannot be blank", 12);

                $data = array(
                    'created_at'        => date('Y-m-d H:i:s'),
                    'created_by'        => intval($userStorage->id),
                    'out_time'          => date('Y-m-d H:i:s'),
                    'receiver'          => My_String::trim($receiver),
                    'address'           => My_String::trim($address),
                    'shipping_id'       => $distributor_list['address_id'],
                    'district'          => intval($district),
                    'phone_number'      => My_String::trim($distributor_list['phone']),
                    'distributor_id'    => intval($distributor_id),
                    'warehouse_id'      => intval($warehouse),
                    'type'              => 2,//Outside
                    'sn'                => My_String::trim($delivery_sn),
                    'staff_id'          => 0,
                    'carrier_id'        => 1,//kerry
                    'number_of_package' => $get_number_of_package,
                    'weight'            => $get_weight,
                    'status'            => 2,
                    'tracking_no'       => $get_tracking
                );


                $id = $QDeliveryOrder->insert($data);

                if (!$id) throw new Exception("Cannot create order", 3);

                $QDeliverySales = new Application_Model_DeliverySales();

                    $data = array('delivery_order_id' => $id, 'sales_sn' => $sn);
                    $QDeliverySales->insert($data);

                // END : Add Delivery When Send To Kerry


                // START : Add Kerry Transacion

                $QKT = new Application_Model_KerryTransaction();

                $QKTData = [
                'tracking_no'   => $get_tracking,
                'sn'            => $m['sn'],
                'type'          => 1,
                'outmysql_time' => $date,
                'delivery_type' => $kerry_status
                ];

                $QKT->insert($QKTData);

                // END : Add Kerry Transacion

                $dateNow = date("Y-m-d");

                $sql_update="UPDATE warehouse.running_inv_seq AS cm
JOIN warehouse.market AS mk ON mk.sn = cm.sn
SET mk.invoice_number = cm.invoice_number
WHERE cm.inv_add_time BETWEEN '".$dateNow." 00:00:00' AND '".$dateNow." 23:59:59' and mk.invoice_time is not null and mk.`invoice_number` is null";

                $db->query($sql_update);

            }else if($kerry_status == 2){ //J&T
                $get_tracking = $this->convertSoToOp($m['sn_ref']);
                $get_number_of_package = $this->getRequest()->getParam('number_of_package');
                $get_weight = $this->getRequest()->getParam('weight');

                // START : Add Delivery When Send To J&T

                $QDistributor = new Application_Model_Distributor();

                $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                $market_check = $QMarket->fetchRow($where);

                if (!$market_check) throw new Exception("Order SN ".$_sn." not exists", 2);

                if (isset($market_check['office']) && $market_check['office'] && isset($office_cache[ $market_check['office'] ]))
                    $distributor_sn = array(
                        'contact'      => $office_cache[ $market_check['office'] ]['contact'],
                        'address'      => $office_cache[ $market_check['office'] ]['address'],
                        'name'         => $office_cache[ $market_check['office'] ]['name'],
                        'phone_number' => $office_cache[ $market_check['office'] ]['phone_number'],
                        'district'     => $office_cache[ $market_check['office'] ]['district'],
                    );

                if (isset($market_check['service']) && $market_check['service'] && isset($service_cache[ $market_check['service'] ]))
                    $distributor_sn = array(
                        'contact'      => $service_cache[ $market_check['service'] ]['contact'],
                        'address'      => $service_cache[ $market_check['service'] ]['address'],
                        'name'         => $service_cache[ $market_check['service'] ]['name'],
                        'phone_number' => $service_cache[ $market_check['service'] ]['phone_number'],
                        'district'     => $service_cache[ $market_check['service'] ]['district'],
                    );

                $distributors = $market_check['d_id'];

                if ($market_check['add_time'] > '2017-01-29') {
                        if (isset($market_check['shipping_address']) && $market_check['shipping_address']){
                            $shipping_address = $market_check['shipping_address'];

                            $shipp_add = My_Address::genAddessDeliveryNote($shipping_address);
                            // print_r($shipp_add);

                        }

                        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributors);
                        $distributor_check = $QDistributor->fetchRow($where);

                        if (!$distributor_check) throw new Exception("Distributor not exists", 3);

                        $distributor_list = array(
                            'id'       => intval($distributor_check['id']),
                            'title'    => htmlspecialchars( $distributor_check['title'], ENT_QUOTES, 'UTF-8' ),
                            //'address'  => htmlspecialchars( !empty( $distributor_check['add_tax'] ) ? My_String::trim($distributor_check['add_tax']) : $distributor_check['add'], ENT_QUOTES, 'UTF-8' ),
                            'address'  => htmlspecialchars( !empty( $shipp_add['address'] ) ? My_String::trim($shipp_add['address']) : $shipp_add['address'], ENT_QUOTES, 'UTF-8' ),
                            'name'     => htmlspecialchars( My_String::trim($distributor_check['name']), ENT_QUOTES, 'UTF-8' ),
                            'district' => intval($distributor_check['district']),
                            'phone'    => htmlspecialchars( My_String::trim($shipp_add['phone']), ENT_QUOTES, 'UTF-8' ),
                            'address_id'    => $shipp_add['address_id'],
                            // 'address_id'    => $shipp_add['address_id'],
                        );

                        // if (isset($distributor_sn)) {
                        //     $distributor_list['title']    .= ' ' . $distributor_sn['name'];
                        //     $distributor_list['address']  = $distributor_sn['address'];
                        //     $distributor_list['phone']    = $distributor_sn['phone_number'];
                        //     $distributor_list['name']     = $distributor_sn['contact'];
                        //     $distributor_list['district'] = $distributor_sn['district'];
                        // }

                    }else{
                        if (isset($market_check['delivery_address']) && $market_check['delivery_address']){
                            $delivery_address = $market_check['delivery_address'];
                        }

                        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributors);
                        $distributor_check = $QDistributor->fetchRow($where);

                        if (!$distributor_check) throw new Exception("Distributor not exists", 3);

                        $distributor_list = array(
                            'id'       => intval($distributor_check['id']),
                            'title'    => htmlspecialchars( $distributor_check['title'], ENT_QUOTES, 'UTF-8' ),
                            //'address'  => htmlspecialchars( !empty( $distributor_check['add_tax'] ) ? My_String::trim($distributor_check['add_tax']) : $distributor_check['add'], ENT_QUOTES, 'UTF-8' ),
                            'address'  => htmlspecialchars( !empty( $delivery_address ) ? My_String::trim($delivery_address) : $distributor_check['add_tax'], ENT_QUOTES, 'UTF-8' ),
                            'name'     => htmlspecialchars( My_String::trim($distributor_check['name']), ENT_QUOTES, 'UTF-8' ),
                            'district' => intval($distributor_check['district']),
                            'phone'    => htmlspecialchars( My_String::trim($distributor_check['tel']), ENT_QUOTES, 'UTF-8' ),
                        );

                        if (isset($distributor_sn)) {
                            $distributor_list['title']    .= ' ' . $distributor_sn['name'];
                            $distributor_list['address']  = $distributor_sn['address'];
                            $distributor_list['phone']    = $distributor_sn['phone_number'];
                            $distributor_list['name']     = $distributor_sn['contact'];
                            $distributor_list['district'] = $distributor_sn['district'];
                        }

                    }

                    // print_r($distributor_list);exit();
                    // print_r($m);exit();


                $QDeliveryOrder = new Application_Model_DeliveryOrder();

                if ($sn){
                    $receiver = date('YmdHis') . substr(microtime(), 2, 4);
                    $delivery_sn = $QDeliveryOrder->getDeliveryNo_Ref($receiver);
                }

                if ($type == My_Delivery_Type::Outside) {
                    $where = array();
                    $where[] = $QDeliveryOrder->getAdapter()->quoteInto('sn IS NOT NULL AND sn = ?', $delivery_sn);
                    $where[] = $QDeliveryOrder->getAdapter()->quoteInto('status <> ?', My_Delivery_Order_Status::Deleted);
                    $order_check = $QDeliveryOrder->fetchRow($where);
                    if ($order_check) throw new Exception("Duplicate Delivery Order SN: " . $delivery_sn, 5);
                }

                $warehouse = null;

                $locaton_pair = array(
                    1 => 8,
                    2 => 11,
                    3 => 10,
                    8 => 1,
                    10 => 3,
                    11 => 2
                );

                    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                    $market_check = $QMarket->fetchRow($where);

                    if (!$market_check)
                        throw new Exception("Invalid SN: " . $sn, 7);

                    if ($warehouse && $warehouse != $market_check['warehouse_id'] && $locaton_pair[$market_check['warehouse_id']] != $warehouse)
                        throw new Exception("Orders in 02 warehouses: " . $sn, 18);

                    $warehouse = $market_check['warehouse_id'];

                $distributor_id = $distributors;

                if (!$distributor_id)
                    throw new Exception("Choose distributor", 8);

                $QWarehouse = new Application_Model_Warehouse();
                $warehouse_cache = $QWarehouse->get_cache();

                if (!isset($warehouse_cache[$warehouse])) throw new Exception("Invalid warehouse", 17);

                $QDistributor = new Application_Model_Distributor();
                $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
                $distributor_check = $QDistributor->fetchRow($where);

                if (!$distributor_check)
                    throw new Exception("Invalid distributor", 5);
            /*
                if (empty($receiver))
                    throw new Exception("Receiver cannot be blank", 10);*/

                $address = $distributor_list['address'];

                if (empty($address))
                    throw new Exception("Address cannot be blank", 11);

                $district = $distributor_list['district'];

                if (!intval($district))
                    throw new Exception("District cannot be blank", 12);

                $data = array(
                    'created_at'        => date('Y-m-d H:i:s'),
                    'created_by'        => intval($userStorage->id),
                    'out_time'          => date('Y-m-d H:i:s'),
                    'receiver'          => My_String::trim($receiver),
                    'address'           => My_String::trim($address),
                    'shipping_id'       => $distributor_list['address_id'],
                    'district'          => intval($district),
                    'phone_number'      => My_String::trim($distributor_list['phone']),
                    'distributor_id'    => intval($distributor_id),
                    'warehouse_id'      => intval($warehouse),
                    'type'              => 2,//Outside
                    'sn'                => My_String::trim($delivery_sn),
                    'staff_id'          => 0,
                    'carrier_id'        => 9,//J&T
                    'number_of_package' => $get_number_of_package,
                    'weight'            => $get_weight,
                    'status'            => 2,
                    'tracking_no'       => $get_tracking
                );


                $id = $QDeliveryOrder->insert($data);

                if (!$id) throw new Exception("Cannot create order", 3);

                $QDeliverySales = new Application_Model_DeliverySales();

                    $data = array('delivery_order_id' => $id, 'sales_sn' => $sn);
                    $QDeliverySales->insert($data);

                // END : Add Delivery When Send To J&T


                // START : Add J&T Transacion

                $QKT = new Application_Model_KerryTransaction();

                $QKTData = [
                'tracking_no'   => $get_tracking,
                'sn'            => $m['sn'],
                'type'          => 1,
                'outmysql_time' => $date,
                'delivery_type' => $kerry_status
                ];

                $QKT->insert($QKTData);

                // END : Add J&T Transacion

                $dateNow = date("Y-m-d");

                $sql_update="UPDATE warehouse.running_inv_seq AS cm
JOIN warehouse.market AS mk ON mk.sn = cm.sn
SET mk.invoice_number = cm.invoice_number
WHERE cm.inv_add_time BETWEEN '".$dateNow." 00:00:00' AND '".$dateNow." 23:59:59' and mk.invoice_time is not null and mk.`invoice_number` is null";

                $db->query($sql_update);
            }

            // START : stamp stock shop brand shop

            $QMSS  = new Application_Model_MapStockShop();
            $getMapStockShop = $QMSS->getDetailsStockShopByDistributor($m['d_id']);

            if(count($getMapStockShop) == 1){

                $imei_list = trim($a_submitted_imeis);
                $imei_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $imei_list);
                $imei_list = explode("\n", $imei_list);
                $imei_list = array_filter($imei_list);

                if($imei_list){

                    $whereUpdateImeiStockShop = $QImei->getAdapter()->quoteInto('imei_sn in (?)', $imei_list);
                    $QImei->update(array(
                        'stock_shop_id'          => $getMapStockShop[0]['store_code'],
                        'stock_shop_status'      => 1,
                        'stock_shop_date'        => $date_now,
                        'stock_shop_scan'        => $date_now
                    ), $whereUpdateImeiStockShop);
                }

            }

            // END : stamp stock shop brand shop

            //--------------- Auto Return -------------------

           $QDistributor = new Application_Model_Distributor();
           $where = $QDistributor->getAdapter()->quoteInto('id = ?', $m['d_id']);
           $distributor_check = $QDistributor->fetchRow($where);

           $agent_status = $distributor_check['agent_status'];
           $agent_warehouse_id = $distributor_check['agent_warehouse_id'];

           //print_r($distributor_check['agent_warehouse_id']);die;

           // print_r($agent_status); die();

           if($agent_status == 1)
           {
               $return_sn = date ( 'YmdHis' ) . substr ( microtime (), 2, 4 );
                //print_r($a_submitted_imeis);
                //die;

                if($order_has_phone) {
                    $imei_list = trim($a_submitted_imeis);
                    $imei_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $imei_list);
                    $imei_list = explode("\n", $imei_list);
                    $imei_list = array_filter($imei_list);
                }else{
                    $imei_list = trim($a_submitted_iot);
                    $imei_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $imei_list);
                    $imei_list = explode("\n", $imei_list);
                    $imei_list = array_filter($imei_list);
                }

                //print_r($imei_list);
                //die;
                foreach ($imei_list as $imei_val)
                {
                  $dataImeiReturn = array(
                      'imei_sn' => trim($imei_val),
                      'box_sn' => $return_sn,
                      'warehouse_id' => intval($agent_warehouse_id),
                      'return_sn' => $return_sn,
                      'sales_order_sn' => $sn,
                      'created_at' => $date,
                      'created_by' => $userStorage->id,
                      'confirmed_at' => $date,
                      'confirmed_by' => $userStorage->id,
                     // 'damage_detail' => $key_imei_sn['damage_detail'],
                      'remark' => 'Return To Main Agent Warehouse',
                      'rtn_number' => '',
                      'return_type' => 1,

                      'status' => 1,
                      'back_sale' => 1,
                      
                  );

                  // print_r($dataImeiReturn);die;

                  $dataImei = array(
                      'return_sn'     => $return_sn,
                      'distributor_id'=> null,
                      'out_date'      => null,
                      'out_user'      => null,
                      'out_price'     => null,
                      'price_date'    => null,
                      'sales_sn'      => null,
                      'sales_id'      => null,
                      'store_id'      => null,
                      'shape'         => 1,
                      'warehouse_id'  => intval($agent_warehouse_id),
                  );

                  // print_r($dataImei);die;
                  $QImeiReturn->insert($dataImeiReturn);

                  // Update Imei
                  $whereImei = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei_val);
                  $QImei->update($dataImei, $whereImei);

                }
           }
          
            //-------------- End Auto Return -----------------


            $db->commit();

            // echo '<script>parent.printed("'.$url.'");</script>';

            echo '<div class="alert alert-success">Success</div>';
            echo '<p>Window will printed redirect after 3 seconds.</p>';
            echo '<script>setTimeout(function(){parent.location.href="' . $back_url .
                '"}, 3000)</script>';

        } else {

            if ($order_has_phone) {
                echo '<p>Remain <strong>' . ($total_imei - $total_scanned_out_imei) .
                    '</strong> IMEIs not scanned.</p>';

                if ($a_imei_success) {
                    echo '<div class="alert alert-success">Success</div>';
                    echo '<p><strong>List of success IMEIs</strong></p>';
                    echo '<textarea rows="13">';
                    foreach ($a_imei_success as $v) {
                        echo $v . "\n";
                    }
                    echo '</textarea>';
                }
                if ($a_imei_errors) {
                    echo '<div class="alert alert-danger">Error</div>';
                    echo '<p><strong>List of failed IMEIs</strong></p>';
                    echo '<textarea rows="13">';
                    foreach ($a_imei_errors as $v) {
                        echo $v['sn'] . "\n";
                    }
                    echo '</textarea>';

                    echo '<ul>';
                    foreach ($a_imei_errors as $v) {
                        echo '<li><strong>' . $v['sn'] . '</strong> | ' . $v['error'] . '</li>';
                    }
                    echo '</ul>';
                }
            }

            if ($order_has_iot) {
                echo '<p>Remain <strong>' . ($total_imei - $total_scanned_out_imei) .
                    '</strong> IMEIs not scanned.</p>';

                if ($a_imei_success) {
                    echo '<div class="alert alert-success">Success</div>';
                    echo '<p><strong>List of success IMEIs</strong></p>';
                    echo '<textarea rows="13">';
                    foreach ($a_imei_success as $v) {
                        echo $v . "\n";
                    }
                    echo '</textarea>';
                }
                if ($a_imei_errors) {
                    echo '<div class="alert alert-danger">Error</div>';
                    echo '<p><strong>List of failed IMEIs</strong></p>';
                    echo '<textarea rows="13">';
                    foreach ($a_imei_errors as $v) {
                        echo $v['sn'] . "\n";
                    }
                    echo '</textarea>';

                    echo '<ul>';
                    foreach ($a_imei_errors as $v) {
                        echo '<li><strong>' . $v['sn'] . '</strong> | ' . $v['error'] . '</li>';
                    }
                    echo '</ul>';
                }
            }

            if ($order_has_digital) {

                echo '<p>Remain <strong>' . ($total_digital - $total_scanned_out_digital_sn) .
                    '</strong> Digital SNs not scanned.</p>';

                if ($a_digital_sn_success) {
                    echo '<div class="alert alert-success">Success</div>';
                    echo '<p><strong>List of success Digital SNs</strong></p>';
                    echo '<textarea rows="13">';
                    foreach ($a_digital_sn_success as $v) {
                        echo $v . "\n";
                    }
                    echo '</textarea>';
                }

                if ($a_digital_sn_errors) {
                    echo '<div class="alert alert-danger">Error</div>';
                    echo '<p><strong>List of failed Digital SNs</strong></p>';
                    echo '<textarea rows="13">';
                    foreach ($a_digital_sn_errors as $v) {
                        echo $v['sn'] . "\n";
                    }
                    echo '</textarea>';

                    echo '<ul>';
                    foreach ($a_digital_sn_errors as $v) {
                        echo '<li><strong>' . $v['sn'] . '</strong> | ' . $v['error'] . '</li>';
                    }
                    echo '</ul>';
                }
            }

            if ($order_has_ilike) {

                echo '<p>Remain <strong>' . ($total_ilike - $total_scanned_out_ilike_sn) .
                    '</strong> Good SNs not scanned.</p>';

                if ($a_ilike_sn_success) {
                    echo '<div class="alert alert-success">Success</div>';
                    echo '<p><strong>List of success Good SNs</strong></p>';
                    echo '<textarea rows="13">';
                    foreach ($a_ilike_sn_success as $v) {
                        echo $v . "\n";
                    }
                    echo '</textarea>';
                }

                if ($a_ilike_sn_errors) {
                    echo '<div class="alert alert-danger">Error</div>';
                    echo '<p><strong>List of failed Good SNs</strong></p>';
                    echo '<textarea rows="13">';
                    foreach ($a_ilike_sn_errors as $v) {
                        echo $v['sn'] . "\n";
                    }
                    echo '</textarea>';

                    echo '<ul>';
                    foreach ($a_ilike_sn_errors as $v) {
                        echo '<li><strong>' . $v['sn'] . '</strong> | ' . $v['error'] . '</li>';
                    }
                    echo '</ul>';
                }
            }


        }



    }

}
catch (exception $e) {


    $db->rollback();

    $flashMessenger = $this->_helper->flashMessenger;

    $flashMessenger->setNamespace('error')->addMessage($e->getMessage());

    echo '<script>parent.location.href="' . $back_url . '";</script>';

}


