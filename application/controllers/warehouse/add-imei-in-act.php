<?php
set_time_limit(0);

$id = $this->getRequest()->getParam('id');

try {
    $cut_imei_false = 0;
    $db = Zend_Registry::get('db');

    $db->beginTransaction();

    if ($this->getRequest()->getMethod() == 'POST'){

        echo '<link href="/css/bootstrap.min.css" rel="stylesheet">';

        if (!$id) exit('<div class="alert alert-error">Missing Purchase order Number</div>');

        $QPo = new Application_Model_Po();

        $rowset = $QPo->find($id);
        $PO = $rowset->current();

        if (!$PO) {
            exit('<div class="alert alert-error">Wrong Purchase order Number.</div>');
        }

        if ($PO['mysql_time']){
            exit('<div class="alert alert-error">This PO was confirmed IN already!</div>');
        }

        $num_scanned = 0;

        if (PHONE_CAT_ID == $PO['cat_id']) {
            $num_scanned = $QPo->count_imported_imei($PO['sn']);

        } elseif (DIGITAL_CAT_ID == $PO['cat_id']) {
            $num_scanned = $QPo->count_imported_digitalsn($PO['sn']);

        } elseif (ILIKE_CAT_ID == $PO['cat_id']) {
            $num_scanned = $QPo->count_imported_sn($PO['sn']);

        }elseif (IOT_CAT_ID == $PO['cat_id']) {
            $num_scanned = $QPo->count_imported_iot($PO['sn']);

        } else {
            exit('<div class="alert alert-error">Wrong goods type! Cannot add IMEI for Accessories.</div>');
        }

        $imei_list = trim($this->getRequest()->getParam('imei', ''));
        $imei_list = explode("\n", $imei_list);

        if (count($imei_list) == 1 && $imei_list[0] == '') {
            exit('<div class="alert alert-warning">No input.</div>');
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
        
        $count = 0;

        $imei_failed = array();
        $imei_reason = array();

        if ($PO['cat_id'] == PHONE_CAT_ID) {
            $QImei = new Application_Model_Imei();
            
            foreach ($imei_list as $imei) {
                $imei = trim($imei);
                // Kiểm tra IMEI có trong hệ thống chưa
                $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                $imei_in_wh = $QImei->fetchRow($where);

                if (!preg_match('/^[0-9]{15}$/', $imei)) {// kiểm tra định dạng IMEI
                    $imei_failed[] = $imei; // lưu ra list
                    $imei_reason[$imei] = 'Wrong format';
                    continue;
                }

                // IMEI có trong hệ thống
                if ($imei_in_wh) {
                    $imei_failed[] = $imei; // lưu ra list
                    $imei_reason[$imei] = 'SN Code was in warehouse';
                    continue;
                }

                if (($count + $num_scanned) >= $PO['num']) {// kiểm tra số lượng IMEI nhập đã đủ chưa
                    $imei_failed[] = $imei; // lưu ra list
                    $imei_reason[$imei] = 'Enough SN Codes in order';
                    continue;
                }

                //-------------------------- start stamp imei--------------------------------//
                // warehouse_id = 98 // WMPD- คลังตัด IMEI Product Warranty
                // type = 2 // rework
                // echo $PO['type'];exit();
                if($PO['warehouse_id'] == '98' && $PO['type'] == 2){
                    $totalImei = 0;
                    $warehouse_id = '98';
                    $good_id = $PO["good_id"];
                    $good_color=$PO["good_color"];
                    $result_imei = $QImei->getImeiWMPDForStamp($warehouse_id,$good_id,$good_color,$totalImei);
                    if($result_imei){


                        // echo var_dump($result_imei);exit();
                        $imei_sn = $result_imei["imei_sn"];
                        $co_old = $result_imei["sn_ref"];
                        $whereImei = array();
                        $whereImei[] = $QImei->getAdapter()->quoteInto('imei_sn =?' , $imei_sn);

                        $data_update_good_id_old = array(
                            'good_id_old'    => 1                       
                        );

                        

                        $QImei->update($data_update_good_id_old , $whereImei);

                        $imei_renew = new Application_Model_ImeiRenew();

                        $data_imei_renew = array(
                            'imei_old'    => $imei_sn,
                            'imei_new'      => $imei,
                            'co_sn'    =>  $co_old,
                            'po_sn'    => $PO['sn_ref']                       
                        );
                        
                         $checkk = $imei_renew->insert($data_imei_renew);  

                        $data = array(
                            'imei_sn'    => $imei,
                            'po_sn'      => $PO['sn'],
                            'warehouse_id'    => $PO['warehouse_id'],
                            'good_id'    => $PO['good_id'],
                            'good_color' => $PO['good_color'],
                            'into_date'  => date('Y-m-d H:i:s'),
                        );

                        $QImei->insert($data);
                        $count++;   
                    }else{
                        $imei_failed[] =  $imei;
                            $cut_imei_false = 1;
                            $count++;  
                             // $count++;
                    }
                         // echo $checkk;exit();     
                        // 911888026077991
                        // 911888026077992    
                                  
                }else{
                     $data = array(
                        'imei_sn'    => $imei,
                        'po_sn'      => $PO['sn'],
                        'store_id'   => null,
                        'warehouse_id'    => $PO['warehouse_id'],
                        'good_id'    => $PO['good_id'],
                        'good_color' => $PO['good_color'],
                        'into_date'  => date('Y-m-d H:i:s'),
                    );

                    $QImei->insert($data);
                    $count++;
                }
                //-------------------------- end stamp imei--------------------------------//
                // if($cut_imei_false == 0){
                    // $data = array(
                    //     'imei_sn'    => $imei,
                    //     'po_sn'      => $PO['sn'],
                    //     'warehouse_id'    => $PO['warehouse_id'],
                    //     'good_id'    => $PO['good_id'],
                    //     'good_color' => $PO['good_color'],
                    //     'into_date'  => date('Y-m-d H:i:s'),
                    // );

                    // $QImei->insert($data);
                    // $count++;
                // }
                
            }

        }elseif ($PO['cat_id'] == IOT_CAT_ID) {

           $QImei = new Application_Model_Imei();
            
            foreach ($imei_list as $imei) {
                $imei = trim($imei);
                // Ki?m tra IMEI c� trong h? th?ng chua
                $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                $imei_in_wh = $QImei->fetchRow($where);

                if (!preg_match('/^[a-zA-Z0-9]{13,30}$/', $imei)) {// ki?m tra d?nh d?ng IMEI
                    $imei_failed[] = $imei; // luu ra list
                    $imei_reason[$imei] = 'Wrong format';
                    continue;
                }

                // IMEI c� trong h? th?ng
                if ($imei_in_wh) {
                    $imei_failed[] = $imei; // luu ra list
                    $imei_reason[$imei] = 'SN Code was in warehouse';
                    continue;
                }

                if (($count + $num_scanned) >= $PO['num']) {// ki?m tra s? lu?ng IMEI nh?p d� d? chua
                    $imei_failed[] = $imei; // luu ra list
                    $imei_reason[$imei] = 'Enough SN Codes in order';
                    continue;
                }

                //-------------------------- start stamp imei--------------------------------//
                // warehouse_id = 98 // WMPD- ??????? IMEI Product Warranty
                // type = 2 // rework
                // echo $PO['type'];exit();
                if($PO['warehouse_id'] == '98' && $PO['type'] == 2){
                    $totalImei = 0;
                    $warehouse_id = '98';
                    $good_id = $PO["good_id"];
                    $good_color=$PO["good_color"];
                    $result_imei = $QImei->getImeiWMPDForStamp($warehouse_id,$good_id,$good_color,$totalImei);
                    if($result_imei){


                        // echo var_dump($result_imei);exit();
                        $imei_sn = $result_imei["imei_sn"];
                        $co_old = $result_imei["sn_ref"];
                        $whereImei = array();
                        $whereImei[] = $QImei->getAdapter()->quoteInto('imei_sn =?' , $imei_sn);

                        $data_update_good_id_old = array(
                            'good_id_old'    => 1                       
                        );

                        

                        $QImei->update($data_update_good_id_old , $whereImei);

                        $imei_renew = new Application_Model_ImeiRenew();

                        $data_imei_renew = array(
                            'imei_old'    => $imei_sn,
                            'imei_new'      => $imei,
                            'co_sn'    =>  $co_old,
                            'po_sn'    => $PO['sn_ref']                       
                        );
                        
                         $checkk = $imei_renew->insert($data_imei_renew);  

                        $data = array(
                            'imei_sn'    => $imei,
                            'po_sn'      => $PO['sn'],
                            'warehouse_id'    => $PO['warehouse_id'],
                            'good_id'    => $PO['good_id'],
                            'good_color' => $PO['good_color'],
                            'into_date'  => date('Y-m-d H:i:s'),
                        );

                        $QImei->insert($data);
                        $count++;   
                    }else{
                        $imei_failed[] =  $imei;
                            $cut_imei_false = 1;
                            $count++;  
                             // $count++;
                    }
                         // echo $checkk;exit();     
                        // 911888026077991
                        // 911888026077992    
                                  
                }else{
                     $data = array(
                        'imei_sn'    => $imei,
                        'po_sn'      => $PO['sn'],
                        'warehouse_id'    => $PO['warehouse_id'],
                        'good_id'    => $PO['good_id'],
                        'good_color' => $PO['good_color'],
                        'into_date'  => date('Y-m-d H:i:s'),
                    );

                    $QImei->insert($data);
                    $count++;
                }
            }

        //end
            
        } elseif ($PO['cat_id'] == DIGITAL_CAT_ID) {
            $QDigitalSn = new Application_Model_DigitalSn();

            foreach ($imei_list as $imei) {
                $imei = trim($imei);
                // Kiểm tra IMEI có trong hệ thống chưa
                $where = $QDigitalSn->getAdapter()->quoteInto('sn = ?', $imei);
                $imei_in_wh = $QDigitalSn->fetchRow($where);

                if (!preg_match('/^[a-zA-Z0-9]{16}$/', $imei)) {// kiểm tra định dạng IMEI
                    $imei_failed[] = $imei; // lưu ra list
                    $imei_reason[$imei] = 'Wrong format';
                    continue;
                }

                // IMEI có trong hệ thống
                if ($imei_in_wh) {
                    $imei_failed[] = $imei; // lưu ra list
                    $imei_reason[$imei] = 'SN Code was in warehouse';
                    continue;
                }

                if (($count + $num_scanned) >= $PO['num']) {// kiểm tra số lượng IMEI nhập đã đủ chưa
                    $imei_failed[] = $imei; // lưu ra list
                    $imei_reason[$imei] = 'Enough SN Codes in order';
                    continue;
                }

                $data = array(
                    'sn'           => $imei,
                    'po_sn'        => $PO['sn'],
                    'warehouse_id' => $PO['warehouse_id'],
                    'good_id'      => $PO['good_id'],
                    'good_color'   => $PO['good_color'],
                    'into_date'    => date('Y-m-d H:i:s'),
                );

                $QDigitalSn->insert($data);
                $count++;
            }

        } elseif ($PO['cat_id'] == ILIKE_CAT_ID) {

            $QGoodSn = new Application_Model_GoodSn();

            foreach ($imei_list as $imei) {
                $imei = trim($imei);
                // Kiểm tra IMEI có trong hệ thống chưa
                $where = $QGoodSn->getAdapter()->quoteInto('sn = ?', $imei);
                $imei_in_wh = $QGoodSn->fetchRow($where);

                if (!preg_match('/^[a-zA-Z0-9]{15}$/', $imei)) {// kiểm tra định dạng IMEI
                    $imei_failed[] = $imei; // lưu ra list
                    $imei_reason[$imei] = 'Wrong format';
                    continue;
                }

                // IMEI có trong hệ thống
                if ($imei_in_wh) {
                    $imei_failed[] = $imei; // lưu ra list
                    $imei_reason[$imei] = 'SN Code was in warehouse';
                    continue;
                }

                if (($count + $num_scanned) >= $PO['num']) {// kiểm tra số lượng IMEI nhập đã đủ chưa
                    $imei_failed[] = $imei; // lưu ra list
                    $imei_reason[$imei] = 'Enough SN Codes in order';
                    continue;
                }

                $data = array(
                    'sn'           => $imei,
                    'po_sn'        => $PO['sn'],
                    'warehouse_id' => $PO['warehouse_id'],
                    'good_id'      => $PO['good_id'],
                    'good_color'   => $PO['good_color'],
                    'into_date'    => date('Y-m-d H:i:s'),
                );

                $QGoodSn->insert($data);
                $count++;
            }

        }
        //Tanong Create GR Running No Ref 20160328 1123
        if($PO->sn !=''){
            $QWarehouse = new Application_Model_Warehouse();
            $QWarehouse->getGROrderNo_Ref($PO->sn);
        }
        //commit
        $db->commit();

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //todo log
        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        $info = 'Add IMEI: ('.$count.') to warehouse - order number: '.$PO->sn . ' ; failed: ('.sizeof($imei_failed).') IMEIs';

        $QLog = new Application_Model_Log();

        $QLog->insert( array (
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => date('Y-m-d H:i:s'),
        ) );


        if ( (count($imei_failed) == 0) && ( ( ($count + $num_scanned) == $PO['num']) || (count($imei_list) == $count) ) ) {
            echo '<div class="alert alert-success">Success All | '.$count.' SN Codes</div>';

            $left = $PO['num'] - ($count + $num_scanned);

            if ($left == 0) {
                echo '<p>All SN Codes are scanned.</p>';
                echo '<p>Window will redirect after 2 seconds.</p>';
                echo '<script>setTimeout(function(){parent.location.href="'.HOST.'warehouse/in"}, 2000)</script>';
            } else {
                echo '<p>Remain <strong>'.$left.'</strong> SN Codes not scanned.</p>';
            }
        }else if($cut_imei_false == 1){
             echo '<div class="alert alert-danger">Current IMEI amount</div>';
             echo '<p>Less then PO IMEI amount.</p>';
            $cut_imei_false = 0;
        } else {
            echo '<div class="alert alert-success">Success ('.$count.')</div>';
            echo '<div class="alert alert-error">Failed ('.count($imei_failed).')</div>';

            $left = $PO['num'] - ($count + $num_scanned);

            if ($left == 0 && count($imei_failed) == 0) {
                echo '<p>All SN Codes are scanned.</p>';
                echo '<p>Window will redirect after 2 seconds.</p>';
                echo '<script>setTimeout(function(){parent.location.href="'.HOST.'warehouse/in"}, 2000)</script>';
            } else {
                echo '<p>Remain <strong>'.$left.'</strong> SN Codes not scanned.</p>';
                echo '<strong>List of failed SN Codes</strong>';
                echo '<textarea rows="13">';
                foreach ($imei_failed as $k => $v) {
                    echo $v."\n";
                }
                echo '</textarea>';

                echo '<ul>';
                foreach ($imei_reason as $k => $v) {
                    echo '<li><strong>'.$k.'</strong> | '.$v.'</li>';
                }
                echo '</ul>';
            }
        }
    }

} catch (Exception $e){

    $db->rollback();

    $flashMessenger = $this->_helper->flashMessenger;

    $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again22!');

    echo '<script>parent.location.href="'.HOST.'warehouse/in"</script>';

}
exit;