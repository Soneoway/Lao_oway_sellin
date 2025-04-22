<?php

$this->view->back_url = HOST . 'sales';

$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

    $so_text    = $this->getRequest()->getParam('so_text');
    $sn_text    = $this->getRequest()->getParam('sn_text');
    $d_id    = $this->getRequest()->getParam('d_id');
    $money  = $this->getRequest()->getParam('money');
    $bank   = $this->getRequest()->getParam('bank');

    $pay_bank_transfer   = $this->getRequest()->getParam('payment_bank_transfer');
    $pay_servicecharge   = $this->getRequest()->getParam('payment_servicecharge');

    $pay_date   = $this->getRequest()->getParam('pay_date');
    $money_bank = $this->getRequest()->getParam('money_bank');
    $lacksurplus    = $this->getRequest()->getParam('lacksurplus');
    $select_cause   = $this->getRequest()->getParam('select_cause');
    $cause_so   = $this->getRequest()->getParam('cause_so');

    $paygroup_remark   = $this->getRequest()->getParam('paygroup_remark');

    $checkbox_use_paygroup   = $this->getRequest()->getParam('checkbox_use_paygroup');
    $use_paygroup   = $this->getRequest()->getParam('use_paygroup');
    $money_use_paygroup   = $this->getRequest()->getParam('money_use_paygroup');

    $use_credit_card_input   = $this->getRequest()->getParam('use_credit_card_input');

    $QPG = new Application_Model_PayGroup();
    $checkPaygroup = $QPG->checkPaymentGroup($sn_text);

    if(count($checkPaygroup) > 0){
        echo json_encode(['status' => '400', 'message' => 'ไม่สามารถสร้างกลุ่มใบเปอินได้ เนื่องจากมีรายการ SO ที่ถูกสร้างในกลุ่มใบเปอินอื่นเเล้ว']);
        exit();
    }

    $distinct_arr_distributor = array_unique($d_id);
    $distinct_arr_distributor = array_values($distinct_arr_distributor);

    $usePaygroup = [];
    $QPGBal = new Application_Model_PayGroupBalance();

    if(count($distinct_arr_distributor) <= 0 || count($distinct_arr_distributor) > 1){
        echo json_encode(['status' => '400', 'message' => 'ไม่สามารถสร้างกลุ่มใบเปอินได้ เนื่องจากมี Distributor มากว่า 1 รายการ']);
        exit();
    }

    $allow_surplus = false;

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    // brand shop & service
    if (My_Staff_Group::inGroup($userStorage->group_id, array(25,28))){
        $allow_surplus = true;
    }

    if($lacksurplus < -10 && !$allow_surplus){
        echo json_encode(['status' => '400', 'message' => 'ไม่สามารถสร้างกลุ่มใบเปอินได้ เนื่องจากเงินขาดมากเกินไป']);
        exit();
    }

    if($checkbox_use_paygroup){

        $temp_use_paygroup = $use_paygroup;

        $distinct_use_paygroup = array_unique($temp_use_paygroup);
        $distinct_use_paygroup = array_values($distinct_use_paygroup);

        if(count($distinct_use_paygroup) != count($use_paygroup)){
            echo json_encode(['status' => '400', 'message' => 'ไม่สามารถสร้างกลุ่มใบเปอินได้เนื่องจากใช้ยอดเงินเหลือจากใบเปอินเก่าซ้ำกัน']);
            exit();
        }

        $usePaygroup = $QPGBal->getUsePaygroup($distinct_arr_distributor[0]);

        $count_check_usepaygroup=0;
        $i=0;
        foreach ($use_paygroup as $key) {
            
            foreach ($usePaygroup as $key_sub) {
                if($key == $key_sub['payment_id']){

                    if($key_sub['balance_total'] >= $money_use_paygroup[$i]){
                        $count_check_usepaygroup++;
                    }else{
                        echo json_encode(['status' => '400', 'message' => 'ไม่สามารถใช้ยอดเงินเหลือจากใบเปอินเก่าได้ ' . $key_sub['payment_no'] . ' เนื่องจากเงินคงเหลือไม่พอ']);
                        exit();
                    }
                }
            }
            $i++;
        }

        if($count_check_usepaygroup < count($use_paygroup)){
            echo json_encode(['status' => '400', 'message' => 'ยอดเงินเหลือจากใบเปอินเก่าผิดพลาด']);
            exit();
        }

    }

    // if($lacksurplus >= -1 and $lacksurplus <= 1)
    if($lacksurplus >= -10 || $allow_surplus)
    {
        $select_cause = null;
    }

    if($select_cause <> 2){
        $cause_so = [];
    }

    $db = Zend_Registry::get('db');

    $db->beginTransaction();

    try {

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $QPG = new Application_Model_PayGroup();
        $QPGB = new Application_Model_PayGroupBank();
        $QPGC = new Application_Model_PayGroupCause();
        $QPGBal = new Application_Model_PayGroupBalance();
        $QMarket = new Application_Model_Market();

        $upload    = new Zend_File_Transfer_Adapter_Http();
        $files  = $upload->getFileInfo();

        $arrTempNameFile = [];

        foreach($files as $file => $fileInfo){
            array_push($arrTempNameFile, $fileInfo['name']);
        }

        $getSn = $QMarket->getSnByArraySoForPaymentGroup($so_text);
        $listSn = [];

        foreach ($getSn as $key) {
            array_push($listSn, $key['sn']);
        }

        $where = array();
        $where[] = $QPG->getAdapter()->quoteInto('sale_order in (?)', $listSn);
        $where[] = $QPG->getAdapter()->quoteInto('status = ?', 1);

        $checkInPayGroup = $QPG->fetchAll($where);

        if(count($checkInPayGroup) > 0){
            // throw new Exception("Can not create payment group, SO duplicate รายการเมื่อสักครู่นี้ไม่สำเร็จ เนื่องจากตรวจพบรายการ SO ถูกใช้ซ้ำมากกว่าหนึ่งครั้ง");

            echo json_encode(['status' => '400', 'message' => 'Can not create payment group, SO duplicate รายการเมื่อสักครู่นี้ไม่สำเร็จ เนื่องจากตรวจพบรายการ SO ถูกใช้ซ้ำมากกว่าหนึ่งครั้ง']);
            exit();
        }

        if(count($so_text) != count($listSn)){
            // throw new Exception("Can not create payment group, SO incorrect");

            echo json_encode(['status' => '400', 'message' => 'Can not create payment group, SO incorrect']);
            exit();
        }

        $where = $QMarket->getAdapter()->quoteInto('sn in (?)', $listSn);
        $QMarket->update(['pay_group' => 1], $where);

        $payment_id = date('YmdHis') . substr(microtime(), 2, 4);
        $payment_group = 1;
        $case_text = $select_cause;
        $created_date = date('Y-m-d H:i:s');
        $modified_date = date('Y-m-d H:i:s');
        $status = 1;
        
        $i=0;
        foreach ($getSn as $key) {

            $QPG->insert(array(
                            'payment_no' => $payment_id,
                            'payment_id' => $payment_id,
                            'sale_order' => $key['sn'],
                            'd_id' => $key['d_id'],
                            'payment_group' => $payment_group,
                            'case_text' => $case_text,
                            'money' => $money[$i],
                            'lacksurplus' => $lacksurplus,
                            'remark' => $paygroup_remark,

                            'pay_bank_transfer' => $pay_bank_transfer,
                            'pay_servicecharge' => $pay_servicecharge,

                            'created_at' => $userStorage->id,
                            'created_date' => $created_date,
                            'modified_at' => $userStorage->id,
                            'modified_date' => $modified_date,
                            'status' => $status
                        ));
            $i++;
        }

        if($lacksurplus > 0){
            $QPGBal->insert(array(
                                'payment_id' => $payment_id,
                                'distributor_id' => $distinct_arr_distributor[0],
                                'total_amount' => $lacksurplus,
                                'use_total' => 0,
                                'balance_total' => $lacksurplus,
                                'status' => $status,
                                'create_date' => $created_date,
                                'create_by' => $userStorage->id,
                                'update_date' => $modified_date,
                                'update_by' => $userStorage->id,
                                'use_status' => 0,
                                'remark' => null
                            ));
        }else{
            $QPGBal->insert(array(
                                'payment_id' => $payment_id,
                                'distributor_id' => $distinct_arr_distributor[0],
                                'total_amount' => 0,
                                'use_total' => 0,
                                'balance_total' => 0,
                                'status' => $status,
                                'create_date' => $created_date,
                                'create_by' => $userStorage->id,
                                'update_date' => $modified_date,
                                'update_by' => $userStorage->id,
                                'use_status' => 0,
                                'remark' => null
                            ));
        }

        if($checkbox_use_paygroup){

            $QPGT = new Application_Model_PayGroupTran();

            $i=0;
            foreach ($use_paygroup as $key) {
                
                if($money_use_paygroup[$i] > 0){

                    $QPGT->insert(array(
                                    'payment_id' => $key,
                                    'payment_tran_id' => $payment_id,
                                    'distributor_id' => $distinct_arr_distributor[0],
                                    'use_total' => $money_use_paygroup[$i],
                                    'create_date' => $created_date,
                                    'create_by' => $userStorage->id,
                                    'status' => $status
                                ));
                }

                $i++;
            }
        }

        $arrNameFile = [];
        $i = 0;
        foreach ($bank as $key) {

            $lastnameFile = pathinfo($arrTempNameFile[$i],PATHINFO_EXTENSION);

            $file_name_upload = '';

            if($lastnameFile != ''){
                $file_name_upload = $key.'_'.$payment_id.'_'.time() . '_' . ($i+1) . '.' . $lastnameFile;
            }

            $credit_card = 0;
            if($use_credit_card_input[$i]){
                $credit_card = $use_credit_card_input[$i];
            }

            $QPGB->insert(array(
                            'payment_id' => $payment_id,
                            'bank' => $key,
                            'balance' => $money_bank[$i],
                            'pay_date' => $pay_date[$i],
                            'file_pay_slip' => $file_name_upload,
                            'status' => $status,
                            'credit_card' => $credit_card
                        ));

            array_push($arrNameFile, $file_name_upload);

            $i++;
        }

        if($select_cause == 2){

            foreach ($cause_so as $key) {
                $QPGC->insert(array(
                            'payment_id' => $payment_id,
                            'sn_manual' => $key
                            ));
            }
        }

        $count_file_upload=0;$r=0;
        foreach($files as $file => $fileInfo)
        {
            if($upload->isUploaded($file))
            {
                    
                $uniqid = uniqid('', true);

                $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
                    . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files'
                    . DIRECTORY_SEPARATOR . 'payment_group'. DIRECTORY_SEPARATOR . 'pay_slips' . DIRECTORY_SEPARATOR;

                if (!is_dir($uploaded_dir))
                    @mkdir($uploaded_dir, 0777, true);

                
                $upload->addFilter('Rename', $uploaded_dir.$arrNameFile[$r]);

                // Upload Max 5 MB
                $upload->setValidators(array(
                    'Size' => array('min' => 50, 'max' => 2000000),
                    'Count' => array('min' => 1, 'max' => 3),
                    'Extension' => array('jpg','jpeg', 'PNG','GIF'),
                ));

                if (!$upload->isValid($file)) { // validate IF
                $errors = $upload->getErrors();
                $sError = null;
                    if ($errors and isset($errors[0]))
                    switch ($errors[0]) {
                        case 'fileUploadErrorIniSize':
                            $sError = 'File size is too large';
                            break;
                        case 'fileMimeTypeFalse':
                            $sError = 'The file you selected weren\'t the type we were expecting';
                            break;
                        case 'fileExtensionFalse':
                            $sError = 'Please choose a file in JPG or PNG format.';
                            break;
                        case 'fileCountTooFew':
                            $sError = 'Please choose a PO file (in JPG or PNG format)';
                            break;
                        case 'fileUploadErrorNoFile':
                            $sError = 'Please choose a PO file (in JPG or PNG format)';
                            break;
                        case 'fileSizeTooBig':
                            $sError = 'File size is too big';
                            break;
                    }

                    if($sError!=''){
                        // $db->rollback();
                        // $flashMessenger->setNamespace('error')->addMessage($sError);

                        echo json_encode(['status' => '400', 'message' => 'Can not create payment group, ' . $sError]);
                        exit();
                    }
                }else{
                   $upload->receive($file);
                }                                                     
            }
            $r+=1;
        }

        $db->commit();

        $messages = $flashMessenger->setNamespace('success')->addMessage('Done!');

        echo json_encode(['status' => '201', 'message' => 'Done.', 'url' => HOST.'sales']);

        // $this->_redirect('/sales');

        exit();

    }
    catch (exception $e) {

        $db->rollback();

        // if($e->getMessage()){
        //     $messages = $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        // }else{
        //     $messages = $flashMessenger->setNamespace('error')->addMessage('Cannot create payment group, please try again! : ' . $e->getMessage());
        // }

        echo json_encode(['status' => '400', 'message' => $e->getMessage()]);

        // $this->_redirect('/sales/create-payment-group');

        exit();
    }

}

$str_sn = $this->getRequest()->getParam('sn');
$arraySn = explode(',', $str_sn);

$QMarket = new Application_Model_Market();
$priceAndDetails = $QMarket->getPriceAndDetails($arraySn);

$Credit_Note_All = $QMarket->fetchCredit_Note_All(rtrim($str_sn, ','));

$arr_distributor = [];
$bucketData = [];

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$show_cash_menu=false;
if (My_Staff_Group::inGroup($userStorage->group_id, OPPO_BRAND_SHOP_SERVICE) || $userStorage->group_id == ADMINISTRATOR_ID )
{
    $show_cash_menu  = true;
}

$this->view->retailer_rank = isset($priceAndDetails[0]['rank']) ? $priceAndDetails[0]['rank'] : '';

$this->view->show_cash_menu = $show_cash_menu;

foreach ($priceAndDetails as $key) {
    array_push($arr_distributor, $key['d_id']);
}

$distinct_arr_distributor = array_unique($arr_distributor);
$distinct_arr_distributor = array_values($distinct_arr_distributor);

$this->view->priceAndDetails = $priceAndDetails;
$this->view->Credit_Note_All = $Credit_Note_All;

$QBank = new Application_Model_Bank();

$where_bank = null;
// Service Show Bank
if (My_Staff_Group::inGroup($userStorage->group_id, array(28))){
    $where_bank = array();
    $where_bank[] = $QBank->getAdapter()->quoteInto('id IN (1,5,17)');
// Brand Shop Show Bank
}else if(My_Staff_Group::inGroup($userStorage->group_id, array(25))){
    $where_bank = array();
    $where_bank[] = $QBank->getAdapter()->quoteInto('id IN (1,3,5,7,8,9,10,16,17)');
}

$banks = $QBank->fetchAll($where_bank,'name asc');
$this->view->banks = $banks;

$messages = $flashMessenger->setNamespace('error')->getMessages();

$usePaygroup = [];
$QPGBal = new Application_Model_PayGroupBalance();

if(count($distinct_arr_distributor) <= 0 || count($distinct_arr_distributor) > 1){
    array_push($messages, 'ไม่สามารถสร้างกลุ่มใบเปอินได้ เนื่องจากมี Distributor มากว่า 1 รายการ');
}else{
    $usePaygroup = $QPGBal->getUsePaygroup($arr_distributor[0]);
}

$QPG = new Application_Model_PayGroup();
$checkPaygroup = $QPG->checkPaymentGroup($arraySn);

if(count($checkPaygroup) > 0){
    array_push($messages, 'ไม่สามารถสร้างกลุ่มใบเปอินได้ เนื่องจากมีรายการ SO ที่ถูกสร้างในกลุ่มใบเปอินอื่นเเล้ว');
}

$this->view->usePaygroup = $usePaygroup;

$this->view->messages = $messages;  

?>