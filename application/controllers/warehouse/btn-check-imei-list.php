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

    if ($this->getRequest()->getMethod() == 'POST'){
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        if (!$sn)
            exit('<div class="alert alert-error">Missing Sales Number.</div>');

        // kiem tra dau vao cac imei & digital sn
        $a_submitted_imeis = $this->getRequest()->getParam('imei');
        $a_submitted_digital_sns = $this->getRequest()->getParam('digital_sn');
        $a_submitted_ilike_sns = $this->getRequest()->getParam('ilike_sn');

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

        $goods_cached = $QGood->get_cache();
        $good_colors_cached = $QGoodColor->get_cache();
        $warehouses_cached = $QWarehouse->get_cache();

        $where = array();
        $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
        $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

        $m = $QMarket->fetchRow($where);

        if (!$m) {
            echo '<div class="alert alert-error">Order Number not found</div>';
            exit;
        }

        $sales_out = $QMarket->fetchAll($where);

        $total_imei_scanned_before = $total_digital_scanned_before = $total_ilike_scanned_before =
            $total_imei = $total_digital = $total_ilike = 0;
        $order_has_phone = $order_has_digital = $order_has_ilike = false;

        $a_ordered_phones = $a_ordered_digital = $a_ordered_ilike = $a_submitted_phones_group =
            $a_submitted_digital_group = $a_submitted_ilike_group = $a_order_phones_group =
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

            $where_new = array();
            $where_new[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $where_new[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);
            $m_new = $QMarket->fetchAll($where_new);

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
                if ($m['type'] == FOR_DEMO and $imei_info['type'] != 2){
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

                if ($m['type'] == FOR_APK and $imei_info['type'] != 5){
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Mismatch IMEI type',
                    );
                    continue;
                }

                $true_good = 0;
                $wrong_good = 0;
                $true_color = 0;
                $wrong_color = 0;

                $countData = count($m_new);

                foreach ($m_new as $key_new) {
                    if($imei_info['good_id'] == $key_new['good_id']){
                        $true_good++;
                        if($imei_info['good_color'] == $key_new['good_color']){
                            $true_color++;
                        }else{
                            $wrong_color++;
                        }
                    }else{
                        $wrong_good++;
                    }
                }

                if($wrong_good >= $countData){
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Incorrect Model',
                    );
                    continue;
                }

                if($true_color < 1){
                    $a_imei_errors[$imei] = array(
                        'sn' => $imei,
                        'error' => 'Incorrect Color',
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
                        'out_date' => date('Y-m-d H:i:s'),
                        'scanning' => 1,
                        );

                    $a_imei_success[] = $d;
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

            $where_new = array();
            $where_new[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $where_new[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);
            $m_new = $QMarket->fetchAll($where_new);

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

                $true_good = 0;
                $wrong_good = 0;
                $true_color = 0;
                $wrong_color = 0;

                $countData = count($m_new);

                foreach ($m_new as $key_new) {
                    if($digi_info['good_id'] == $key_new['good_id']){
                        $true_good++;
                        if($digi_info['good_color'] == $key_new['good_color']){
                            $true_color++;
                        }else{
                            $wrong_color++;
                        }
                    }else{
                        $wrong_good++;
                    }
                }

                if($wrong_good >= $countData){
                    $a_digital_sn_errors[$digi] = array(
                        'sn' => $digi,
                        'error' => 'Incorrect Model',
                    );
                    continue;
                }

                if($true_color < 1){
                    $a_digital_sn_errors[$digi] = array(
                        'sn' => $digi,
                        'error' => 'Incorrect Color',
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
                        'out_date' => date('Y-m-d H:i:s'),
                        );

                    $a_digital_sn_success[] = $d;
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

            $where_new = array();
            $where_new[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $where_new[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);
            $m_new = $QMarket->fetchAll($where_new);

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

                $true_good = 0;
                $wrong_good = 0;
                $true_color = 0;
                $wrong_color = 0;

                $countData = count($m_new);

                foreach ($m_new as $key_new) {
                    if($ilike_info['good_id'] == $key_new['good_id']){
                        $true_good++;
                        if($ilike_info['good_color'] == $key_new['good_color']){
                            $true_color++;
                        }else{
                            $wrong_color++;
                        }
                    }else{
                        $wrong_good++;
                    }
                }

                if($wrong_good >= $countData){
                    $a_ilike_sn_errors[$ilike] = array(
                        'sn' => $ilike,
                        'error' => 'Incorrect Model',
                    );
                    continue;
                }

                if($true_color < 1){
                    $a_ilike_sn_errors[$ilike] = array(
                        'sn' => $ilike,
                        'error' => 'Incorrect Color',
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

                        $a_ilike_sn_success[] = $d;
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

        $total_scanned_out_imei = $QMarket->count_out_imei($sn);
        $total_scanned_out_digital_sn = $QMarket->count_out_digital($sn);
        $total_scanned_out_ilike_sn = $QMarket->count_out_ilike($sn);

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
catch (exception $e) {

    $flashMessenger = $this->_helper->flashMessenger;

    $flashMessenger->setNamespace('error')->addMessage($e->getMessage());

    echo '<script>parent.location.href="' . $back_url . '";</script>';

}


