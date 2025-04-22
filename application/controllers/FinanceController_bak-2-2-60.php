<?php

class FinanceController extends My_Controller_Action
{
    //Tanong 
    public function cnRewardViewPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cn-reward-view-print.php';
    }
    public function cnRewardAllGreenPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cn-reward-all-green-print.php';
    }
    public function invoiceAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'invoice.php';
    }

    public function viewPaySlipAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'view-pay-slip.php';
    }

    public function viewImeiListAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'view-imei-list.php';
    }

    public function invoiceDestroyAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'invoice-destroy.php';
    }

    //Tanong List return order for print  2016-03-13 1452
    //return-list-credit-note
    public function returnListCreditNoteAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'return-list-cn.php';
    }

    //Tanong
    public function oppoclupRewardListCnAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'oppoclup-reward-list-cn.php';
    }

    //Tanong oppoclup-reward-upload-cn
    public function oppoclupRewardImportCnAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'oppoclup-reward-upload-cn.php';
    }

    //Tanong
    public function returnListCnAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'return-list-cn.php';
    }
    //Tanong
    public function cnViewPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cn-view-print.php';
    }

    public function cpViewImportPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cp-view-import-print.php';
    }

    //Tanong
    public function cpViewPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cp-view-print.php';
    }

    public function cpViewImeiPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cp-view-imei-print.php';
    }
    public function cpViewImeiImportPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cp-view-imei-import-print.php';
    }

    public function cnViewImeiRewardPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cn-view-imei-reward-print.php';
    }

    public function cnViewImeiReturnPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cn-view-imei-return-print.php';
    }

    public function oppoAllGreenAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'oppo-all-green.php';
    }

    public function oppoAllGreenUploadAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'oppo-all-green-upload.php';
    }

    public function oppoAllGreenSaveAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'oppo-all-green-save.php';
    }

    public function poAction()
    {
        //auto refresh
        $this->view->meta_refresh = 30;

        $sort = $this->getRequest()->getParam('sort', 'created_at');
        $desc = $this->getRequest()->getParam('desc', 1);

        $page = $this->getRequest()->getParam('page', 1);

        $sn = $this->getRequest()->getParam('sn');
        $created_by = $this->getRequest()->getParam('created_by');
        $cat_id = $this->getRequest()->getParam('cat_id');
        $good_id = $this->getRequest()->getParam('good_id');
        $good_color = $this->getRequest()->getParam('good_color');
        $warehouse_id = $this->getRequest()->getParam('warehouse_id');
        $created_at_to = $this->getRequest()->getParam('created_at_to');
        $created_at_from = $this->getRequest()->getParam('created_at_from');

        $limit = LIMITATION;
        $total = 0;

        $params = array_filter(array(
            'sn' => $sn,
            'created_by' => $created_by,
            'cat_id' => $cat_id,
            'good_id' => $good_id,
            'good_color' => $good_color,
            'warehouse_id' => $warehouse_id,
            'created_at_to' => $created_at_to,
            'created_at_from' => $created_at_from,
            ));

        $params['isbacks'] = 0;
        // $params['group_sn'] = 1;

        if ($cat_id) {
            $QGood = new Application_Model_Good();
            $where = $QGood->getAdapter()->quoteInto('cat_id = ?', $cat_id);
            $goods = $QGood->fetchAll($where, 'name');

            $this->view->goods = $goods;

            if ($good_id) {
                //get goods color
                $where = $QGood->getAdapter()->quoteInto('id = ?', $good_id);
                $good = $QGood->fetchRow($where);

                $aColor = array_filter(explode(',', $good->color));
                if ($aColor) {
                    $QGoodColor = new Application_Model_GoodColor();
                    $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

                    $colors = $QGoodColor->fetchAll($where);
                    $this->view->colors = $colors;
                }
            }
        }

        $params['sort'] = $sort;
        $params['desc'] = $desc;

        $params['no_payment'] = 1;

        $QPO = new Application_Model_Po();

        $POs = $QPO->fetchPagination($page, $limit, $total, $params);

        unset($params['no_payment']);


        $this->view->desc = $desc;
        $this->view->sort = $sort;
        $this->view->POs = $POs;
        $this->view->params = $params;
        $this->view->limit = $limit;
        $this->view->total = $total;
        $this->view->url = HOST . 'finance/po/' . ($params ? '?' . http_build_query($params) .
            '&' : '?');

        $this->view->offset = $limit * ($page - 1);

        $QStaff = new Application_Model_Staff();
        $this->view->staffs = $QStaff->get_cache();

        $QWarehouse = new Application_Model_Warehouse();
        $this->view->warehouses = $QWarehouse->get_cache();

        $QGoodCategory = new Application_Model_GoodCategory();
        $this->view->good_categories = $QGoodCategory->get_cache();

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

    public function poConfirmAction()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            $QPo = new Application_Model_Po();

            if ($this->getRequest()->getMethod() == 'POST') {

                $pay_user = $this->getRequest()->getParam('pay_user');

                $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                $where = $QPo->getAdapter()->quoteInto('id = ?', $id);
                $data = array(
                    'pay_user' => $pay_user,
                    'flow' => $userStorage->id,
                    'flow_time' => date('Y-m-d H:i:s'),
                    );

                $flashMessenger = $this->_helper->flashMessenger;
                try {
                    $PO = $QPo->fetchRow($where);

                    $QPo->update($data, $where);

                    //todo log
                    $ip = $this->getRequest()->getServer('REMOTE_ADDR');

                    $info = 'Verify: Purchase order number: ' . $PO->sn;

                    $QLog = new Application_Model_Log();

                    $QLog->insert(array(
                        'info' => $info,
                        'user_id' => $userStorage->id,
                        'ip_address' => $ip,
                        'time' => date('Y-m-d H:i:s'),
                        ));

                    $flashMessenger->setNamespace('success')->addMessage('Done!');
                }
                catch (exception $e) {
                    $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
                }

                $this->_redirect('/finance/po');
            }

            $rowset = $QPo->find($id);
            $PO = $rowset->current();

            $this->view->PO = $PO;

            $QGoodCategory = new Application_Model_GoodCategory();
            $categories = $QGoodCategory->get_cache();

            $this->view->category = isset($categories[$PO->cat_id]) ? $categories[$PO->
                cat_id] : '';

            //get goods
            $QGood = new Application_Model_Good();
            $goods = $QGood->get_cache();

            $this->view->good = isset($goods[$PO->good_id]) ? $goods[$PO->good_id] : '';

            //get goods color
            $QGoodColor = new Application_Model_GoodColor();
            $goodColors = $QGoodColor->get_cache();

            $this->view->good_color = isset($goodColors[$PO->good_color]) ? $goodColors[$PO->
                good_color] : '';

            //get goods color
            $QWarehouse = new Application_Model_Warehouse();
            $warehouse = $QWarehouse->get_cache();

            $this->view->warehouse = isset($warehouse[$PO->warehouse_id]) ? $warehouse[$PO->
                warehouse_id] : '';

            //get username
            $QStaff = new Application_Model_Staff();

            $staffs = $QStaff->get_cache();

            $this->view->created_by_name = isset($staffs[$PO->created_by]) ? $staffs[$PO->
                created_by] : '';

            $this->view->payer_name = isset($staffs[$PO->flow]) ? $staffs[$PO->flow] : '';

            $this->view->warehousing_name = isset($staffs[$PO->mysql_user]) ? $staffs[$PO->
                mysql_user] : '';


        }
    }

    public function poRemoveAction()
    {
        $id = $this->getRequest()->getParam('id');
        $flashMessenger = $this->_helper->flashMessenger;


        if ($id) {
            $QPo = new Application_Model_Po();

            $rowset = $QPo->find($id);
            $PO = $rowset->current();

            if ($PO) {
                $where = $QPo->getAdapter()->quoteInto('id = ?', $id);

                try {
                    $QPo->delete($where);

                    $flashMessenger->setNamespace('success')->addMessage('Done!');
                }
                catch (exception $e) {
                    $flashMessenger->setNamespace('error')->addMessage('Cannot delete, please try again!');
                }
                $this->_redirect('/finance/po');
            }
        }

        $flashMessenger->setNamespace('error')->addMessage('Wrong ID!');
        $this->_redirect('/finance/po');
    }

    public function salesConfirmAction()
    {
        // print_r($_GET);die;
        $sn = $this->getRequest()->getParam('sn');
        $sn_re = $this->getRequest()->getParam('sn_re');
        $act = $this->getRequest()->getParam('act');
        $flashMessenger = $this->_helper->flashMessenger;

        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        //---------------Reject pond -----------------//
            if ($act=='reject') {
                $QMarket = new Application_Model_Market();
                $QCheckmoney = new Application_Model_Checkmoney();

                $where_re = $QMarket->getAdapter()->quoteInto('sn = ?', $sn_re);
                $data_re = array(
                        'sales_confirm_date'    => NULL,
                        'sales_confirm_id'      => NULL,
                        'confirm_so'            => 0
                    ); 
                
                $QMarket->update($data_re,$where_re);
                $where_del = array();
                $where_del = $QCheckmoney->getAdapter()->quoteInto('sn = ?', $sn_re);
                $where_del = $QCheckmoney->getAdapter()->quoteInto('type = 1');
                $QCheckmoney->delete($where_del);
                 $this->_redirect('/finance/sales');
            }
            //--------------------------------------------//
        if ($sn) {

            $db = Zend_Registry::get('db');
            $QMarket = new Application_Model_Market();
            $where = array();
            $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

            $sales = $QMarket->fetchAll($where);

            //check
            if (!$sales) {
                $flashMessenger->setNamespace('error')->addMessage('Invalid SN!');
                $this->_redirect('/finance/sales');
            }

            if (!isset($sales[0]) || ($sales[0]['shipping_yes_time'] and $sales[0]['pay_time'])) {

                $flashMessenger->setNamespace('error')->addMessage('This order cannot be changed!');

                $this->_redirect('/finance/sales');

            }

            /*---------Get Data From Sales Confirm-------------*/
            $select = $db->select()
            ->from(array('ch'=>'checkmoney'),   array('ch.*','ch_id'=>'ch.id'))
            ->joinleft(array('b'=>'bank'),'ch.bank=b.id',array("bank_name"=>'b.name','b.id'))// trường hợp transaction trừ tiền
            ->where($db->quoteInto('ch.sn = ?',$sn))
            ->where($db->quoteInto('ch.type=?',1));
            //echo $select;

            $currentTransaction = $db->fetchAll($select);
            $this->view->transaction = $currentTransaction; //checkmoney

            /*---------End Get Data From Sales Confirm-------------*/

            $QMarketProduct = new Application_Model_MarketProduct();
            $QMarket = new Application_Model_Market();
            //Tiền đi đơn nếu có bảo vệ giá thì đã trừ tiền
            $sn_total = 0;
            $intRebate = intval($QMarketProduct->getPrice($sn)); // số tiền được giảm
            $sn_total = $QMarket->getPrice($sn) - $intRebate; // số tiền còn lại

            $strNoteRebate = '';
            if ($intRebate > 0) {
                $strNoteRebate = ', rebate: ' . $intRebate;
            }

            if($sales[0]['office']){
                $selectArea = $db->select()
                    ->from(array('a'=>'office'),array('a.*'))
                    ->where('a.id = ?',$sales[0]['office']);
                ;
                $office_area = $db->fetchRow($selectArea);
                $this->view->office_area = $office_area;
            }

            //Store account
            $QStoreaccount = new Application_Model_Storeaccount();
            $QDistributor  = new Application_Model_Distributor();
            $QCampaign     = new Application_Model_Campaign();

            //lấy dealer mẹ
            $main_retailer                   = $QDistributor->getRootDistributor($sales[0]['d_id']);
            $this->view->main_retailer       = $main_retailer;
            $total_balance_row               = $QStoreaccount->getBalanceByGroup($sales[0]['d_id']);
            $distributor_balance_row         = $QStoreaccount->getBalance($sales[0]['d_id']);

            $selectCompany = $db->select()
                ->from(array('p'=>'warehouse'),array('p.company_id'))
                ->where('id = ?',$sales[0]['warehouse_id']);
            $company_id = $db->fetchOne($selectCompany);

            $remain_balance                  = ($company_id == 1) ? $total_balance_row['balance'] : $total_balance_row['balance_smartmobile'];
            $distributor_balance             = ($company_id == 1) ? $distributor_balance_row['balance'] : $distributor_balance_row['balance_smartmobile'];
            $this->view->distributor_balance = $distributor_balance;

            $checkBalance = 0;
            $checkPaymentStatus = 0; //kiểm tra có thể cho phép payment không?
            if ($remain_balance) {
                if ($sn_total <= $remain_balance) {
                    $checkPaymentStatus = 1;
                } else{
                    $checkPaymentStatus = 0;
                }

                $checkBalance = $remain_balance - $sn_total;
            } else{
                $checkBalance = -$sn_total;
            }

            //xử lý check payment
            $this->view->need               = abs($checkBalance);
            $this->view->checkPaymentStatus = $checkPaymentStatus;
            $this->view->checkBalance       = $checkBalance;
            $this->view->remain_balance     = $remain_balance;
            $this->view->campaign           = $QCampaign->get_cache();

            

            // get tags
            $QTag = new Application_Model_Tag();
            $QTagObject = new Application_Model_TagObject();

            $where = array();
            $where[] = $QTagObject->getAdapter()->quoteInto('object_id = ?', $sn);
            $where[] = $QTagObject->getAdapter()->quoteInto('type = ?', TAG_ORDER);

            $a_tags = array();

            $tags_object = $QTagObject->fetchAll($where);
            if ($tags_object)
                foreach ($tags_object as $to) {
                    $where = $QTag->getAdapter()->quoteInto('id = ?', $to['tag_id']);
                    $tag = $QTag->fetchRow($where);
                    if ($tag)
                        $a_tags[] = $tag['name'];
                }

            $this->view->a_tags = $a_tags;
            //print_r($a_tags);
            if ($this->getRequest()->getMethod() == 'POST') {
                //print_r($_POST);die;
                // echo "<pre>";
                // print_r($_FILES);
                // $file_name_show = $_FILES['file_name']['name'];
                // print_r($file_name_show);

                // die;

                $db->beginTransaction();
                try {

                    $sn       = $this->getRequest()->getParam('sn');
                    $ch_id       = $this->getRequest()->getParam('ch_id');
                    $d_id       = $this->getRequest()->getParam('d_id');
                    $payment       = $this->getRequest()->getParam('payment');
                    $shipping      = $this->getRequest()->getParam('shipping');
                    $pay_text      = $this->getRequest()->getParam('pay_text');
                    $shipping_text = $this->getRequest()->getParam('shipping_text');
                    $payment_type = $this->getRequest()->getParam('payment_type',NULL);
                    $payment_order = $this->getRequest()->getParam('payment_order', 0);
                    $payment_bank_transfer = $this->getRequest()->getParam('payment_bank_transfer', 0);
                    $payment_service = $this->getRequest()->getParam('payment_service', 0);
                    $payment_servicecharge = $this->getRequest()->getParam('payment_servicecharge', 0);
                    $pay_banktransfer = $this->getRequest()->getParam('pay_banktransfer', 0);
                    $pay_servicecharge = $this->getRequest()->getParam('pay_servicecharge', 0);
                    $pay_service = $this->getRequest()->getParam('pay_service', 0);
                    $file_ = $this->getRequest()->getParam('file_');
                    
                    $pay_time      = $this->getRequest()->getParam('pay_time');
                    $bank          = $this->getRequest()->getParam('select_bank_id', NULL);
                    $type          = 1;
                    $company_id    = $this->getRequest()->getParam('company_id');
                    $retailer_rank = $this->getRequest()->getParam('retailer_rank', NULL);

                    $total_amount  = $this->getRequest()->getParam('total_amount', NULL);
                    if($total_amount==0)
                    {
                        $payment_order=0;
                        $payment_bank_transfer=0;
                        $payment_servicecharge=0;
                        $payment_service=0;
                    }                   

                    $where = array();
                    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                    $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

                    $date = date('Y-m-d H:i:s');

                    $checkUpdateCheckMoney = 0;
                    $QStoreaccount  = new Application_Model_Storeaccount();
                    $QCheckmoney = new Application_Model_Checkmoney();
                    $QCheckmoneyPaymentorder = new Application_Model_CheckmoneyPaymentorder();

                    /*--------------------------------------------*/
                   // $data = array();
                   // $where = array();
                        $where = $QCheckmoney->getAdapter()->quoteInto('sn = ?', $sn);
                        $data['finance_confirm_id'] = $userStorage->id;
                        $data['finance_confirm_date'] = $date;
                        $QCheckmoney->update($data,$where); 

                    if($payment_type=="CA")
                    {
                        $data['finance_confirm_id'] = $userStorage->id;
                        $data['finance_confirm_date'] = $date;
                        $where = $db->quoteInto('sn = ?',$sn);
                        $QCheckmoneyPaymentorder->update($data,$where);
                    }

                    
                    // Edie Finance confirm Order By Pungpond
                    $where_edit = array();
                    $edit = array();
                    
                       // echo "<pre>";

                       // print_r($_FILES['file']['name']);
                        
                       
                    for ($i=0; $i < count($bank); $i++) { 
                        
                        if (isset($_FILES['file_name']['name'][$i]) and $_FILES['file_name']['name'][$i]) {
                            $file_name_upload = '/pay_slips/'.$sn.'/'.$_FILES['file_name']['name'][$i];
                            $edit['file_pay_slip'] = $file_name_upload;   
                        }

                      
                        if (isset($ch_id[$i]) and $ch_id[$i]) {
                            
                            $edit['bank'] = $bank[$i];
                            $edit['pay_money'] = $payment_order[$i];
                            $edit['pay_banktransfer'] = $pay_banktransfer[$i];
                            $edit['pay_servicecharge'] = $pay_servicecharge[$i];
                            $edit['pay_service'] = $pay_service[$i];
                            $edit['pay_time'] = $pay_time[$i];
                        
                        $where_edit = $QCheckmoney->getAdapter()->quoteInto('id = ?', $ch_id[$i]);
                        $QCheckmoney->update($edit, $where_edit);
                        }else{
                            $note_new='PayMoney='.number_format($payment_order[$i],2) .' Fee transfer='.number_format($pay_banktransfer[$i],2).' Service Charge='.number_format($pay_servicecharge[$i],2).' ค่าอะไหล่และค่าบริการ='.number_format($pay_service[$i],2);
                             $data_f = array(
                                    'd_id'                  => $d_id,
                                    'bank'                  => $bank[$i],
                                    'pay_money'             => $payment_order[$i],
                                    'pay_servicecharge'     => $pay_servicecharge[$i],
                                    'pay_banktransfer'      => $pay_banktransfer[$i],
                                    'pay_service'           => $pay_service[$i],
                                    'type'                  => 1,
                                    'pay_time'              => $pay_time[$i],
                                    'bank_serial'           => null,
                                    'bank_transaction_code' => null,
                                    'note'                  => $note_new,
                                    'content'               => null,
                                    'company_id'            => $company_id,
                                    'sn'                    => $sn,
                                    'file_pay_slip'         => $file_name_upload,
                                    'user_id'               => $userStorage->id,
                                    'create_by'             => $userStorage->id,
                                    'create_at'             => date('Y-m-d H:i:s'),
                                    'sales_confirm_id'      => $userStorage->id,
                                    'sales_confirm_date'    => date('Y-m-d H:i:s'),
                                    'addition'              => 1
                            );
                         $QCheckmoney->insert($data_f);

                        }
                        

                        
                        
                        // print_r($data_f).'<br/>';
                        // print_r($edit);
                    }
                  // die;
                    /*-------------------File Pay Slip Upload--------------------------*/
                            $upload    = new Zend_File_Transfer_Adapter_Http();
                            $files  = $upload->getFileInfo();
                            
                            $count_file_upload=0;$r=0;
                            foreach($files as $file => $fileInfo)
                            {
                                if($upload->isUploaded($file))
                                {
                                        
                                    $uniqid = uniqid('', true);

                                    $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
                                        . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files'
                                        . DIRECTORY_SEPARATOR . 'finance'. DIRECTORY_SEPARATOR . 'pay_slips'
                                        . DIRECTORY_SEPARATOR . $sn;

                                    $file_pay_slip = DIRECTORY_SEPARATOR . 'pay_slips'
                                        . DIRECTORY_SEPARATOR . $sn . DIRECTORY_SEPARATOR;    

                                    if (!is_dir($uploaded_dir))
                                        @mkdir($uploaded_dir, 0777, true);

                                    
                                    $upload->setDestination($uploaded_dir);

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
                                            $db->rollback();
                                            $flashMessenger->setNamespace('error')->addMessage($sError);
                                            $this->_redirect('/sales/sales-confirm-order?sn=' . $sn);
                                        }
                                    }else{
                                       $upload->receive($file);
                                    }                                                     
                                }
                                $r+=1;
                            }

                        /*-------------------End File Pay Slip Upload--------------------------*/

                    
                    /* ------------------------ */
                  // update balance
                    $QStoreaccount->updateBalance($sales[0]['d_id']);
                    $data['pay_time'] = $date;
                    $data['pay_user'] = $userStorage->id;
                    $data['shipping_yes_time'] = $date;
                    $data['shipping_yes_id'] = $userStorage->id;

                    $data['pay_text'] = $pay_text;
                    $data['shipping_text'] = $shipping_text;
                    $data['finance_confirm_id'] = $userStorage->id;
                    $data['finance_confirm_date'] = $date;
                    $QMarket->update($data, $where);

                    /*--------------------------------------------*/

                    //todo log
                    $ip = $this->getRequest()->getServer('REMOTE_ADDR');

                    $info = 'Verify: Sale order number: ' . $sn;

                    $QLog = new Application_Model_Log();

                    $QLog->insert(array(
                        'info' => $info,
                        'user_id' => $userStorage->id,
                        'ip_address' => $ip,
                        'time' => $date,
                        ));

                    //check before commit
                    if ($payment) {
                        $whereCheckMoney       = array();
                        $whereCheckMoney[]     = $QCheckmoney->getAdapter()->quoteInto('sn = ?',$sales[0]['sn']);
                        $checkUpdateCheckMoney = $QCheckmoney->fetchRow($whereCheckMoney);
                        if (!$checkUpdateCheckMoney) {
                            $db->rollback();
                            $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
                            $this->_redirect('/finance/sales-confirm?sn=' . $sn);
                        }
                    }

                    $db->commit();
                    $flashMessenger->setNamespace('success')->addMessage('Done!');
                    $this->_redirect('/finance/sales');
                }
                catch (exception $e) {
                    $db->rollback();
                    $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!' .
                        $e->getMessage());
                    $this->_redirect('/finance/sales-confirm?sn=' . $sn);
                }
                $flashMessenger->setNamespace('success')->addMessage('Done!');
                $this->_redirect('/finance/sales');

            } //End if check post

            $where = array();
            $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

            $sales = $QMarket->fetchAll($where);

            // print_r($sales);

            $data = array();

            $QGoodCategory = new Application_Model_GoodCategory();
            $categories    = $QGoodCategory->get_cache();

            $QGood         = new Application_Model_Good();
            $goods         = $QGood->get_cache();

            $QGoodColor    = new Application_Model_GoodColor();
            $goodColors    = $QGoodColor->get_cache();

            $QStaff        = new Application_Model_Staff();
            $staffs        = $QStaff->get_cache();

            $QDistributor  = new Application_Model_Distributor();
            $distributors  = $QDistributor->get_cache();

            // get another info of distributor
            $where = $QDistributor->getAdapter()->quoteInto('id = ?', $sales[0]->d_id);
            $distributors_info = $QDistributor->fetchRow($where);

            // get credit from distributor's credit type
            $QCredit  = new Application_Model_Credit();
            $where = $QCredit->getAdapter()->quoteInto('id = ?', $distributors_info->credit_type);
            $credit = $QCredit->fetchRow($where);

            $QWarehouse    = new Application_Model_Warehouse();
            $warehouses    = $QWarehouse->get_cache();

            $Credit_Note = $QMarket->fetchCredit_Note($sn);

            $show_cash_menu=false;
            if (My_Staff_Group::inGroup($userStorage->group_id, OPPO_BRAND_SHOP_SERVICE) || $userStorage->group_id == ADMINISTRATOR_ID ) 
            {
                $show_cash_menu  = true;
            }
            
            foreach ($sales as $k => $sale) {
                //get warehouse
                $data[$k]['warehouse_name'] = isset($warehouses[$sale->warehouse_id]) ? $warehouses[$sale->
                    warehouse_id] : '';

                //get retailer
                //print_r($distributors);
                $data[$k]['retailer_name'] = isset($distributors[$sale->d_id]) ? $distributors[$sale->
                    d_id] : '';

                //get d_id
                $data[$k]['d_id'] = $sale->d_id;        

                //get retailer : rank
                $data[$k]['retailer_rank'] = isset($distributors_info->rank) ? $distributors_info->rank : '';

                //get retailer : rank
                $data[$k]['show_cash_menu'] = $show_cash_menu;

                //get retailer : credit amount
                $data[$k]['credit_amount'] = isset($distributors_info->credit_amount) ? $distributors_info->credit_amount : '';

                //get retailer : credit type
                $data[$k]['credit_type'] = isset($credit->name) ? $credit->name : '';

                //get created_by_name
                $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->
                    user_id] : '';

                //get created_by_name
                $data[$k]['salesman_name'] = isset($staffs[$sale->salesman]) ? $staffs[$sale->
                    salesman] : '';

                //get sales man Catty
                if($sale->sales_catty_id !=''){
                    $staffs_catty = $QStaff->getSalesCattyByStore($sale->d_id,$sale->sales_catty_id);
                }

                $data[$k]['salescatty_name'] = isset($staffs_catty) ? $staffs_catty[0]['fullname'] : '';

                //get category
                $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->
                    cat_id] : '';

                //get good
                $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';

                //get goods color
                $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->
                    good_color] : '';

                $data[$k]['sale'] = $sale;

               // $data[$k]['total_discount'] = $Credit_Note[0]['total_discount'];

               // $data[$k]['credit_note_list'] = $Credit_Note[0];
                //print_r($Credit_Note[0]);

                $data[$k]['total_discount'] = $Credit_Note[0]['total_discount'];

                $data[$k]['credit_note_list'] = $Credit_Note;
                $data[$k]['total_spc_discount'] = $sale->total_spc_discount;

            }

            //Get remain discount
            $QMarketDeduction = new Application_Model_MarketDeduction();
            $deduction = $QMarketDeduction->getPrice(array('d_id' => $sales[0]['d_id']));

            $discount_ck = (isset($deduction[DISCOUNT_CK]) ? $deduction[DISCOUNT_CK] : 0) + (isset($deduction[DISCOUNT_CK_II]) ? $deduction[DISCOUNT_CK_II] : 0) + (isset($deduction[DISCOUNT_CK_III]) ? $deduction[DISCOUNT_CK_III] : 0);
            $diamond_discount = (isset($deduction[DISCOUNT_DIAMOND_CLUB]) ? $deduction[DISCOUNT_DIAMOND_CLUB] : 0) + (isset($deduction[DISCOUNT_DIAMOND_CLUB_5]) ? $deduction[DISCOUNT_DIAMOND_CLUB_5] : 0) + (isset($deduction[DISCOUNT_DIAMOND_CLUB_6]) ? $deduction[DISCOUNT_DIAMOND_CLUB_6] : 0) + (isset($deduction[DISCOUNT_DIAMOND_CLUB_7]) ? $deduction[DISCOUNT_DIAMOND_CLUB_7] : 0)+ (isset($deduction[DISCOUNT_DIAMOND_CLUB_8]) ? $deduction[DISCOUNT_DIAMOND_CLUB_8] : 0);

            $this->view->discount = $discount_ck;
            $this->view->diamond_discount = $diamond_discount;

             //Get remain discount BVG
            $discount_bvg = $QMarketProduct->getPriceDiscount(array('d_id' => $sales[0]['d_id']));
            $this->view->discount_bvg = $discount_bvg;

            //Get detail discount
            $detailDiscount = $QMarketProduct->getDetailDiscount($sn);
            //get detail BVG
            $detailBVG = $QMarketProduct->getDetailBVG($sn);

             $QBank = new Application_Model_Bank();
            $banks = $QBank->fetchAll(null,'name asc');
            $this->view->banks = $banks;

            $this->view->detailBVG = $detailBVG;
            $this->view->detailDiscount = $detailDiscount;
            
            $this->view->sales = $data;

            //print_r($data);
        }
    }
    public function deleteFinanceConfirmAction()
    {

        $d_id = $this->getRequest()->getParam('d_id');
        $ch_id = $this->getRequest()->getParam('ch_id');
        $QStoreaccount  = new Application_Model_Storeaccount();
        $QCheckmoney = new Application_Model_Checkmoney();
        $where = $QCheckmoney->getAdapter()->quoteInto('id = ?', $ch_id);
        $QCheckmoney->delete($where);
        $QStoreaccount->updateBalance($d_id);

       
    }
    //Tanong
    public function returnConfirmAction()
    {
        $sn = $this->getRequest()->getParam('sn');
        
        //die;
        if ($sn){

            $QMarket = new Application_Model_Market();
            $db = Zend_Registry::get('db');

            if ($this->getRequest()->getMethod() == 'POST') {
                
               // print_r($_POST);die;

                $payment = $this->getRequest()->getParam('payment');
                $pay_text = $this->getRequest()->getParam('pay_text');
                $distributor_id = $this->getRequest()->getParam('distributer_id');
                $create_cn = $this->getRequest()->getParam('create_cn','');
                $active_cn = $this->getRequest()->getParam('active_cn','');

                if ($payment) {
                    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);

                    $data = array('pay_text' => $pay_text, );

                    $date = date('Y-m-d H:i:s');

                    $data['pay_time'] = $date;
                    $data['pay_user'] = $userStorage->id;

                    $data['shipping_yes_time'] = $date;
                    $data['shipping_yes_id'] = $userStorage->id;

                    $flashMessenger = $this->_helper->flashMessenger;
                    try {

                        $db->beginTransaction();

                        // Tanong Create CN And ReCalcurate total_money,use_money,balance_money
                        $QMarket->update($data, $where);

                        //todo update imei & accessories return table
                        $data = array(
                            'confirmed_at' => $date,
                            'confirmed_by' => $userStorage->id,
                            );

                        $QImeiReturn = new Application_Model_ImeiReturn();
                        $where = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
                        $QImeiReturn->update($data, $where);

                        
                        $QProductReturn = new Application_Model_ProductReturn();
                        $where = $QProductReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
                        $QProductReturn->update($data, $where);

                        if($create_cn=='checked'){
                        //Tanong
                            if($active_cn=='checked'){
                                $status='0';
                            }else{
                                $status='1';
                            }

                           $this->get_credit_note_sn($db,$distributor_id,$userStorage->id,$sn,$status); 
                        }

                        

                        //todo log
                        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

                        $info = 'Verify: Sale order number: ' . $sn;

                        $QLog = new Application_Model_Log();

                        $QLog->insert(array(
                            'info' => $info,
                            'user_id' => $userStorage->id,
                            'ip_address' => $ip,
                            'time' => $date,
                            ));

                        $flashMessenger->setNamespace('success')->addMessage('Done!');

                        $db->commit();

                    }
                    catch (exception $e) {
                        $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
                    }
                }

                $this->_redirect('/finance/return-list');
            }

            $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $sales = $QMarket->fetchAll($where);
            //print_r($sales);
            $data = array();

            $QGoodCategory = new Application_Model_GoodCategory();
            $categories = $QGoodCategory->get_cache();

            $QGood = new Application_Model_Good();
            $goods = $QGood->get_cache();

            $QGoodColor = new Application_Model_GoodColor();
            $goodColors = $QGoodColor->get_cache();

            $QStaff = new Application_Model_Staff();
            $staffs = $QStaff->get_cache();

            $QDistributor = new Application_Model_Distributor();
            $distributors = $QDistributor->get_cache();

            $QWarehouse = new Application_Model_Warehouse();
            $warehouses = $QWarehouse->get_cache();

            foreach ($sales as $k => $sale) {
                //get warehouse
                $data[$k]['warehouse_name'] = isset($warehouses[$sale->backs_d_id]) ? $warehouses[$sale->
                    backs_d_id] : '';

                //get retailer id
                $data[$k]['distributer_id'] = $sale->d_id;

                //get create cn
                $data[$k]['create_cn'] = $sale->create_cn;

                //get retailer
                $data[$k]['retailer_name'] = isset($distributors[$sale->d_id]) ? $distributors[$sale->
                    d_id] : '';

                //get created_by_name
                $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->
                    user_id] : '';

                //get category
                $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->
                    cat_id] : '';

                //get good
                $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';

                //get goods color
                $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->
                    good_color] : '';

                $data[$k]['sale'] = $sale;
            }

            $this->view->sales = $data;


        }
    }

    public function salesAction()
    {
        //auto refresh
       // print_r(111);
        $this->view->meta_refresh = 300;

        $sort               = $this->getRequest()->getParam('sort', 'p.id');
        $desc               = $this->getRequest()->getParam('desc', 0);
        $page               = $this->getRequest()->getParam('page', 1);

        $sn                 = $this->getRequest()->getParam('sn');
        $d_id               = $this->getRequest()->getParam('d_id');
        $good_id            = $this->getRequest()->getParam('good_id');
        $good_color         = $this->getRequest()->getParam('good_color');
        $num                = $this->getRequest()->getParam('num');
        $price              = $this->getRequest()->getParam('price');
        $created_at_to      = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));
        $created_at_from    = $this->getRequest()->getParam('created_at_from', date('d/m/Y', strtotime('-1 month')));
        $cat_id             = $this->getRequest()->getParam('cat_id');
        $warehouse_id       = $this->getRequest()->getParam('warehouse_id');
        $export             = $this->getRequest()->getParam('export', 0);
        $export_distributor = $this->getRequest()->getParam('export_distributor', 0);
        $export_warehouse   = $this->getRequest()->getParam('export_warehouse', 0);
        $tags               = $this->getRequest()->getParam('tags');

        $limit = LIMITATION;
        $total = 0;

        if ($tags and is_array($tags))
            $tags = $tags;
        else
            $tags = null;

        $params = array_filter(array(
            'sn'              => $sn,
            'd_id'            => $d_id,
            'good_id'         => $good_id,
            'good_color'      => $good_color,
            'num'             => $num,
            'price'           => $price,
            'total'           => $total,
            'created_at_to'   => $created_at_to,
            'created_at_from' => $created_at_from,
            'from'            => $created_at_from, // tương tự 2 biến trên nhưng dùng cho function khác
            'to'              => $created_at_to, // (làm biếng đổi tên)
            'cat_id'          => $cat_id,
            'warehouse_id'    => $warehouse_id,
            'tags'            => $tags,
            'confirm_so'      => 1                  // check confirm so before finance confirm
            
            ));

        if ($export_distributor == 1) {
            $this->_export_distributor($params);

            exit;
        }

        if ($export_warehouse == 1) {
            $this->_export_warehouse($params);

            exit;
        }

        $params['group_sn'] = true;
        $params['finance'] = true;
        $params['isbacks'] = false;
        $params['status'] = 1;

        $params['cancel'] = 0;

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
            $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
            $this->_exportExcel($markets_sn);
        }

        $params['get_fields'] = array(
            'sn',
            'd_id',
            'pay_time',
            'shipping_yes_time',
            'outmysql_time',
            'warehouse_id',
            'status',
            'add_time',
            'last_updated_at'
            );

        $markets_sn = $QMarket->fetchPagination($page, $limit, $total, $params);

        $markets_sn_array = array();

        foreach($markets_sn as $k => $v)
        {
            $markets_sn_array[$k] = $v;
            $markets_sn_array[$k]['discount'] = $QMarketProduct->getDiscount($v['sn']);
        }

        $markets = array();

        unset($params['finance']);
        unset($params['group_sn']);
        unset($params['get_fields']);

        $params['sn'] = $sn;

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
        $this->view->url    = HOST . 'finance/sales/' . ($params ? '?' . http_build_query($params) .
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
            'SALE ORDER NUMBER',
            'RETAILER NAME',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'SALES QUANTITY',
            'SALES PRICE',
            'TOTAL',
            'PAYMENT OR NOT',
            'SHIPPING',
            'ORDER TIME');

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
            $sheet->getCell($alpha++ . $index)->setValueExplicit($item['sn'],
                PHPExcel_Cell_DataType::TYPE_STRING);

            if (isset($distributors) && isset($distributors[$item['d_id']]))
                $distributor = $distributors[$item['d_id']];
            else
                $distributor = '';

            //check payment
            isset($item['pay_time']) ? $pay = 'v' : $pay = 'X';

            //check shipping
            if ($item['shipping_yes_time'])
                $shipping = 'v';
            else
                $shipping = 'X';
            //check out_warehouse
            isset($item['outmysql_time']) ? $out = 'v' : $out = 'X';

            if ($item['status'] == 1)
                $status = 'v';
            else
                $status = 'X';

            $sheet->setCellValue($alpha++ . $index, $distributor);
            $sheet->setCellValue($alpha++ . $index, $item['price_1']);
            $sheet->setCellValue($alpha++ . $index, $item['price_2']);
            $sheet->setCellValue($alpha++ . $index, $item['total_qty']);
            $sheet->setCellValue($alpha++ . $index, $item['price_4']);
            $sheet->setCellValue($alpha++ . $index, $item['total_price']);
            $sheet->setCellValue($alpha++ . $index, $pay);
            $sheet->setCellValue($alpha++ . $index, $shipping);

            $sheet->setCellValue($alpha++ . $index, $item['add_time']);
            $index++;

            foreach ($markets[$item['sn']] as $key => $value) {
                $alpha = 'A';
                $sheet->setCellValue($alpha++ . $index, $i++);

                if (isset($goods) && isset($goods[$value['good_id']]))
                    $good_name = $goods[$value['good_id']];
                if (isset($goodColors) && isset($goodColors[$value['good_color']]))
                    $good_color = $goodColors[$value['good_color']];

                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, $good_name);
                $sheet->setCellValue($alpha++ . $index, $good_color);
                $sheet->setCellValue($alpha++ . $index, $value['num']);
                $sheet->setCellValue($alpha++ . $index, $value['price']);
                $sheet->setCellValue($alpha++ . $index, $value['total']);
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $index++;
            }
        }

        $filename = 'List_Sales_Order_' . date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');

        exit;
    }

    public function returnListAction()
    {
        //auto refresh
        $this->view->meta_refresh = 30;

        $sort = $this->getRequest()->getParam('sort', 'p.id');
        $desc = $this->getRequest()->getParam('desc', 1);
        $page = $this->getRequest()->getParam('page', 1);

        $sn = $this->getRequest()->getParam('sn');
        $d_id = $this->getRequest()->getParam('d_id');
        $good_id = $this->getRequest()->getParam('good_id');
        $good_color = $this->getRequest()->getParam('good_color');
        $num = $this->getRequest()->getParam('num');
        $price = $this->getRequest()->getParam('price');
        $created_at_to = $this->getRequest()->getParam('created_at_to');
        $created_at_from = $this->getRequest()->getParam('created_at_from');
        $cat_id = $this->getRequest()->getParam('cat_id');

        $export = $this->getRequest()->getParam('export', 0);

        $limit = LIMITATION;
        $total = 0;

        $params = array_filter(array(
            'sn' => $sn,
            'd_id' => $d_id,
            'good_id' => $good_id,
            'good_color' => $good_color,
            'num' => $num,
            'price' => $price,
            'total' => $total,
            'created_at_to' => $created_at_to,
            'created_at_from' => $created_at_from,
            'cat_id' => $cat_id,
            ));

        $params['isbacks'] = true;
        $params['finance_return'] = true;
        $params['group_sn'] = 1;


        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QMarket = new Application_Model_Market();
        $QDistributor = new Application_Model_Distributor();
        $QGoodCategory = new Application_Model_GoodCategory();

        $goods = $QGood->get_cache();
        $goodColors = $QGoodColor->get_cache();
        $distributors = $QDistributor->get_cache();
        $good_categories = $QGoodCategory->get_cache();

        $params['sort'] = $sort;
        $params['desc'] = $desc;

        if (isset($export) && $export) {
            $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
            $this->_exportReturnSaleExcel($markets_sn);
        } else {

            $markets_sn = $QMarket->fetchPagination($page, $limit, $total, $params);
        }
        $markets = array();

        foreach ($markets_sn as $key => $m) {
            $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
            $markets[$m['sn']] = $QMarket->fetchAll($where);
        }

        $this->view->goods = $goods;
        $this->view->goodColors = $goodColors;
        $this->view->markets = $markets;
        $this->view->markets_sn = $markets_sn;
        $this->view->distributors = $distributors;
        $this->view->good_categories = $good_categories;

        unset($params['isbacks']);
        unset($params['group_sn']);
        unset($params['finance_return']);

        $this->view->desc = $desc;
        $this->view->sort = $sort;
        $this->view->params = $params;
        $this->view->limit = $limit;
        $this->view->total = $total;
        $this->view->url = HOST . 'finance/return-list/' . ($params ? '?' .
            http_build_query($params) . '&' : '?');

        $this->view->offset = $limit * ($page - 1);

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $this->_helper->viewRenderer->setRender('partials/return-list');
        }
    }

    private function _exportReturnSaleExcel($data)
    {
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'No.',
            'SALE ORDER NUMBER',
            'RETAILER NAME',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'SALES QUANTITY',
            'SALES PRICE',
            'TOTAL',
            'ORDER TIME');

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
            $sheet->getCell($alpha++ . $index)->setValueExplicit($item['sn'],
                PHPExcel_Cell_DataType::TYPE_STRING);

            if (isset($distributors) && isset($distributors[$item['d_id']]))
                $distributor = $distributors[$item['d_id']];
            else
                $distributor = '';
            //check payment
            isset($item['pay_time']) ? $pay = 'v' : $pay = 'X';

            //check shipping
            if ($item['shipping_yes_time'])
                $shipping = 'v';
            else
                $shipping = 'X';

            //check out_warehouse
            isset($item['outmysql_time']) ? $out = 'v' : $out = 'X';

            if ($item['status'] == 1)
                $status = 'v';
            else
                $status = 'X';

            $sheet->setCellValue($alpha++ . $index, $distributor);
            $sheet->setCellValue($alpha++ . $index, $item['price_1']);
            $sheet->setCellValue($alpha++ . $index, $item['price_2']);
            $sheet->setCellValue($alpha++ . $index, $item['total_qty']);
            $sheet->setCellValue($alpha++ . $index, $item['price_4']);
            $sheet->setCellValue($alpha++ . $index, $item['total_price']);
            $sheet->setCellValue($alpha++ . $index, $item['add_time']);

            $index++;


            foreach ($markets[$item['sn']] as $key => $value) {
                $alpha = 'A';
                $sheet->setCellValue($alpha++ . $index, $i++);

                if (isset($goods) && isset($goods[$value['good_id']]))
                    $good_name = $goods[$value['good_id']];
                if (isset($goodColors) && isset($goodColors[$value['good_color']]))
                    $good_color = $goodColors[$value['good_color']];

                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, $good_name);
                $sheet->setCellValue($alpha++ . $index, $good_color);
                $sheet->setCellValue($alpha++ . $index, $value['num']);
                $sheet->setCellValue($alpha++ . $index, $value['price']);
                $sheet->setCellValue($alpha++ . $index, $value['total']);
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $index++;

            }

        }

        $filename = 'List_Return_Order_' . date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');
        exit;
    }


    function product_price($priceFloat) 
    {
        $symbol = ' THB';
        $symbol_thousand = ',';
        $decimal_place = 2;
        $price = number_format($priceFloat, $decimal_place, '.', $symbol_thousand);
        return $price;
    }

     
    private function _exportExcelCreditNote($data)
    {
         
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Credit Note For Return Order'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'DISTRIBUTOR CODE',
            'RETAILER NAME',
            'TYPE DISCOUNT',
            'CREDIT NOTE NO',
            'DISCOUNT TOTAL',
            'BALANCE TOTAL',
            'CREATEDATE',
            
        );
                fputcsv($output, $heads);


     
        $i = 2;
        foreach($data as $item)

        {
           
            $row = array();
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = '';
            $row[] = '';
            $row[] = $item['total_amount'];
            $row[] = $item['balance_total'];
            $row[] = $item['create_date'];
            

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }
     private function _exportExcelCreditNoteList($data)
    {
         
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Credit Note For Return Order'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'CREATEDATE',
            'CREDIT NOTE NO',
            'DISTRIBUTOR TYPE',
            'DISTRIBUTOR CODE',
            'RETAILER NAME',
            'TAX NO',
            'AREA_NAME',
            'PROVINCE',
            'DISTRICT',
            'CHANEL',
            'TYPE DISCOUNT',
            'TOTAL',
            'VAT',
            'TOTAL PRICE',
            'SUB TOTAL',
            'BALANCE TOTAL',
            
            
        );
                fputcsv($output, $heads);


        
        $i = 2;
        $vat = 0;  
        //print_r($data);die;
        foreach($data as $item)
        {

            $total_amount = $item['total_amount'];
            $creditnote_type_sn = $item['creditnote_type'];
            $creditnote_chanel_sn = $item['chanel']; 
                    if($creditnote_chanel_sn=='reward'){
                        $creditnote_chanel='OPPOCLUB';
                        $creditnote_type='CN';

                        $total = number_format($total_amount,2);
                        $b = str_replace( ',', '', $total );  
                        $vat =   number_format($total_amount-$b,2);

                    }else if($creditnote_chanel_sn=='accessories'){
                        $creditnote_chanel='Price Protection';
                        $creditnote_type='CP';
                        $total = number_format($total_amount / 1.07,2);
                        $b = str_replace( ',', '', $total );  
                        $vat =   number_format($total_amount-$b,2);
                    }else if($creditnote_chanel_sn=='oppo_all_green'){
                        $creditnote_chanel='OPPO ALL GREEN';
                        $creditnote_type='CN';

                        $total = number_format($total_amount / 1.03,2);
                        $b = str_replace( ',', '', $total );  
                        $vat =   number_format($total_amount-$b,2);

                    
                    }else{
                        if($creditnote_type_sn=='CN')
                        {
                            $creditnote_chanel='Return';
                            $creditnote_type='CN';
                            $total = number_format($total_amount / 1.07,2);
                            $b = str_replace( ',', '', $total );  
                            $vat =   number_format($total_amount-$b,2);
                        }else{
                            $creditnote_chanel='Price Protection';
                            $creditnote_type='CP';

                            $total = number_format($total_amount / 1.07,2);
                            $b = str_replace( ',', '', $total );  
                            $vat =   number_format($total_amount-$b,2);
                            
                        }
                    }

            
            $parent_key = $item['parent'];

            $tax_no = '="'.$item['tax_no'].'"';
            $area_name = '="'.$item['area_name'].'"';
            $province = '="'.$item['province'].'"';
            $district = '="'.$item['district'].'"';

            if($parent_key=='0'){
                $distributor_type="สำนักงานใหญ่";
            }else{
                $distributor_type="สาขา";
            }

            $row = array();
            $row[] = $item['create_date'];
            $row[] = $item['creditnote_sn'];
            $row[] = $distributor_type;
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $tax_no;
            $row[] = $area_name;
            $row[] = $province;
            $row[] = $district;
            $row[] = $creditnote_chanel;
            $row[] = $creditnote_type;
            $row[] = '-'.$total;
            $row[] = '-'.$vat;
            $row[] = '-'.number_format($item['total_amount'],2);
            $row[] = $item['use_total']== 0 ?'0' : '-'.number_format($item['use_total'],2);
            $row[] = '-'.number_format($item['balance_total'],2);
            
            
            fputcsv($output, $row);
            unset($item);
            unset($row);
            
        }
            
        
        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    private function _exportExcelUseCN($data)
    {
         
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Use_CN'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'sn',
            'sn_ref',
            'invoice_number',
            'pay_time',
            'creditnote_sn',
            'use_discount',
            'area_name',
            'province',
            'district',
            'd_id',
            'store_code',
            'title',
            'tax_code',
            'tel',

            
            
        );
                fputcsv($output, $heads);


        
        $i = 2;
        $vat = 0;  
        //print_r($data);die;
        foreach($data as $item)
        {

        

            $row = array();
             $row[] = "'".$item['sn']."'";
             $row[] = $item['sn_ref'];  
             $row[] = $item['invoice_number'];
             $row[] = $item['pay_time'];
             $row[] = $item['creditnote_sn'];
             $row[] = $item['use_discount'];
             $row[] = $item['area_name'];
             $row[] = $item['province'];
             $row[] = $item['district'];
             $row[] = $item['D_id'];
             $row[] = $item['store_code'];
             $row[] = $item['title'];
             $row[] = $item['tax_code'];
             $row[] = $item['tel'];
            
            
            fputcsv($output, $row);
            unset($item);
            unset($row);
            
        }
            
        
        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    public function distributorPoAction(){
        $flashMessenger               = $this->_helper->flashMessenger;
        //distributor po
        $po_name = $this->getRequest()->getParam('po_name');
        $d_id    = $this->getRequest()->getParam('d_id');
        $status  = $this->getRequest()->getParam('status');
        $from    = $this->getRequest()->getParam('from');
        $to      = $this->getRequest()->getParam('to');
        $id      = $this->getRequest()->getParam('id');
        $page    = $this->getRequest()->getParam('page',1);
        $sort    = $this->getRequest()->getParam('sort','created_at');
        $desc    = $this->getRequest()->getParam('desc', 1);
        $total   = 0;
        $limit   = LIMITATION;
        $params = array(
            'id'      => $id,
            'po_name' => trim($po_name),
            'd_id'    => intval($d_id),
            'status'  => intval($status),
            'from'    => $from,
            'to'      => $to,
            'sort'    => $sort,
            'desc'    => $desc
        );

        $QDistributorPo     = new Application_Model_DistributorPo();
        $list               = $QDistributorPo->fetchPagination($page,$limit,$total,$params);
        $this->view->list   = $list;
        $this->view->sort   = $sort;
        $this->view->desc   = $desc;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'finance/distributor-po/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->offset = $limit*($page-1);

        $QDistributor = new Application_Model_Distributor();
        $QArea        = new Application_Model_Area();
        $distributors = $QDistributor->get_cache();
        $areas        = $QArea->get_cache();
        $this->view->areas        = $areas;
        $this->view->distributors = $distributors;


        $messages                     = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages         = $messages;
        $messages_success             = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;
    }

    public function orderByPoAction(){
        $flashMessenger    = $this->_helper->flashMessenger;
        $po_id             = $this->getRequest()->getParam('po_id');
        $sort              = $this->getRequest()->getParam('sort', 'p.id');
        $desc              = $this->getRequest()->getParam('desc', 1);
        $page              = $this->getRequest()->getParam('page', 1);
        $sn                = $this->getRequest()->getParam('sn');
        $d_id              = $this->getRequest()->getParam('d_id');
        $good_id           = $this->getRequest()->getParam('good_id');
        $good_color        = $this->getRequest()->getParam('good_color');
        $num               = $this->getRequest()->getParam('num');
        $price             = $this->getRequest()->getParam('price');
        $pay_time          = $this->getRequest()->getParam('payment', 0);
        $outmysql_time     = $this->getRequest()->getParam('outmysql_time', 0);
        $created_at_to     = $this->getRequest()->getParam('created_at_to');
        $created_at_from   = $this->getRequest()->getParam('created_at_from');
        $invoice_time_from = $this->getRequest()->getParam('invoice_time_from');
        $invoice_time_to   = $this->getRequest()->getParam('invoice_time_to');
        $cat_id            = $this->getRequest()->getParam('cat_id');
        $warehouse_id      = $this->getRequest()->getParam('warehouse_id');
        $type              = $this->getRequest()->getParam('type');
        $text              = $this->getRequest()->getParam('text');
        $warehouse_id      = $this->getRequest()->getParam('warehouse_id');
        $status            = $this->getRequest()->getParam('status', 1);
        $tags              = $this->getRequest()->getParam('tags');
        $invoice_number    = $this->getRequest()->getParam('invoice_number');
        $user_id           = $this->getRequest()->getParam('user_id');
        $area_id           = $this->getRequest()->getParam('area_id');
        $region_id         = $this->getRequest()->getParam('region_id');

        if ($tags and is_array($tags))
            $tags = $tags;
        else
            $tags = null;

        $limit = LIMITATION;
        $total = 0;

        $params = array_filter( array(
            'po_id'             => $po_id,
            'sn'                => $sn,
            'd_id'              => $d_id,
            'good_id'           => $good_id,
            'good_color'        => $good_color,
            'num'               => $num,
            'price'             => $price,
            'total'             => $total,
            'cat_id'            => $cat_id,
            'warehouse_id'      => $warehouse_id,
            'status'            => $status,
            'text'              => $text,
            'type'              => $type,
            'tags'              => $tags,
            'invoice_time_from' => $invoice_time_from,
            'invoice_time_to'   => $invoice_time_to,
            'invoice_number'    => $invoice_number,
            'user_id'           => $user_id,
            'area_id'           => $area_id,
            'region_id'         => $region_id,
        ));
        $params['created_at_from']  = $created_at_from;
        $params['created_at_to']    = $created_at_to;

        $params['isbacks'] = false;
        $params['group_sn'] = true;

        if ($pay_time)
            $params['payment'] = true;

        if ($outmysql_time)
            $params['outmysql_time'] = true;


        $QGood          = new Application_Model_Good();
        $QGoodColor     = new Application_Model_GoodColor();
        $QMarket        = new Application_Model_Market();
        $QDistributor   = new Application_Model_Distributor();
        $QGoodCategory  = new Application_Model_GoodCategory();
        $QWarehouse     = new Application_Model_Warehouse();
        $QStaff         = new Application_Model_Staff();
        $QArea          = new Application_Model_Area();
        $QRegion        = new Application_Model_Region();
        $QDistributorPo = new Application_Model_DistributorPo();


        $goods             = $QGood->get_cache();
        $goodColors        = $QGoodColor->get_cache();
        $distributors      = $QDistributor->get_cache();
        $distributors2     = $QDistributor->get_cache2();
        $good_categories   = $QGoodCategory->get_cache();
        $warehouses_cached = $QWarehouse->get_cache();
        $staffs_cached     = $QStaff->get_cache();

        $params['sort'] = $sort;
        $params['desc'] = $desc;
        $params['get_fields'] = array(
            'sn',
            'd_id',
            'pay_time',
            'shipping_yes_time',
            'outmysql_time',
            'warehouse_id',
            'status',
            'add_time',
            'canceled',
        );

        if(!$po_id){
            $flashMessenger->setNamespace('error')->addMessage('Please select PO name!');
            $this->_redirect('/finance/distributor-po');
        }

        $currentPo = $QDistributorPo->find($po_id)->current();

        if(!$currentPo){
            $flashMessenger->setNamespace('error')->addMessage('Please select PO name!');
            $this->_redirect('/finance/distributor-po');
        }
        $this->view->currentPo = $currentPo;
        $markets_sn = $QMarket->fetchPagination($page, NULL, $total, $params);


        unset($params['get_fields']);
        unset($params['isbacks']);
        unset($params['group_sn']);

        $this->view->areas = $QArea->get_cache();

        if ($area_id) {
            $where = $QRegion->getAdapter()->quoteInto('area_id = ?', $area_id);
            $regions = $QRegion->fetchAll($where, 'name');

            $regions_arr = array();

            foreach ($regions as $key => $value)
                $regions_arr[$value['id']] = $value['name'];

            $this->view->regions = $regions_arr;
        }

        $this->view->po_id           = $po_id;
        $this->view->goods           = $goods;
        $this->view->goodColors      = $goodColors;
        $this->view->markets_sn      = $markets_sn;
        $this->view->distributors    = $distributors;
        $this->view->distributors2   = $distributors2;
        $this->view->good_categories = $good_categories;
        $this->view->warehouses_cached = $warehouses_cached;
        $this->view->staffs_cached   = $staffs_cached;

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'finance/order-by-po/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->offset = $limit*($page-1);


        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

    }

    public function saveDistributorPoAction(){
        set_time_limit( 0 );
        ini_set('memory_limit', -1);
        if($this->getRequest()->isPost()){

            $flashMessenger = $this->_helper->flashMessenger;
            $db             = Zend_Registry::get('db');
            $QMarket        = new Application_Model_Market();
            $Qcheckmoney    = new Application_Model_Checkmoney();
            $QStoreAccount  = new Application_Model_Storeaccount();
            $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
            $id             = $this->getRequest()->getParam('po_id');//po_id
            $sns            = $this->getRequest()->getParam('sns');
            $pay_text       = $this->getRequest()->getParam('pay_text','');
            $shipping_text  = $this->getRequest()->getParam('shipping_text','');

            if(!$id){
                $flashMessenger->setNamespace('error')->addMessage('Please select Po to check payment!');
                $this->_redirect('/finance/distributor-po');
            }

            if(!is_array($sns)){
                $flashMessenger->setNamespace('error')->addMessage('Please select order number to check payment!');
                $this->_redirect('/finance/order-by-po?po_id='.$id);
            }

            if(!count($sns)){
                $flashMessenger->setNamespace('error')->addMessage('Please select order number to check payment!');
                $this->_redirect('/finance/order-by-po?po_id='.$id);
            }

            $arrUpdateBalance = array();
            $db->beginTransaction();
            try{

                foreach($sns as $sn){
                    $selectDis = $QMarket->select()
                        ->where('sn = ?',$sn)
                        ->where('status = ?',1);
                    $rowDistributor  = $QMarket->fetchRow($selectDis);
                    $d_id = $rowDistributor->d_id;

                    if(!in_array($d_id,$arrUpdateBalance) ){
                          $arrUpdateBalance[]  = $d_id;
                    }

                    $selectSn = $db->select()
                                ->from(array('m'=>'market'),array('m.sn','m.pay_time','m.shipping_yes_time','m.pay_user',
                                    'm.shipping_yes_id','m.po_id','m.outmysql_time','m.warehouse_id'))
                                ->join(array('w'=>'warehouse'),'m.warehouse_id = w.id',array('w.company_id'))
                                ->where('sn = ?',$sn)
                                ->where('status = ?',1);
                    $rows           = $db->fetchAll($selectSn);

                    $QMarketProduct = new Application_Model_MarketProduct();
                    $intRebate      = intval($QMarketProduct->getPrice($sn));
                    $sn_total       = $QMarket->getPrice($sn) - $intRebate;

                    if(!count($rows)){
                        $flashMessenger->setNamespace('error')->addMessage('Error: '.$sn .' is not exist!');
                        $this->_redirect('/finance/order-by-po?po_id=',$id);
                    }

                    if($rows[0]['pay_user'] AND $rows[0]['shipping_yes_id'] ){
                        continue;
                    }

                    foreach($rows as $row){

                        if($row['pay_user'] AND $row['shipping_yes_id']){

                            $flashMessenger->setNamespace('error')->addMessage('Error: '. $sn .' has checked  payment, please review!');
                            $this->_redirect('/finance/order-by-po?po_id='.$id);
                        }

                        if($row['po_id'] != $id){
                            $flashMessenger->setNamespace('error')->addMessage('Error: '.$sn.' do not belong this PO');
                            $this->_redirect('/finance/order-by-po?po_id='.$id);
                        }

                        if($row['outmysql_time']){
                            $flashMessenger->setNamespace('error')->addMessage('Error: '. $sn .' was  out warehouse!');
                            $this->_redirect('/finance/order-by-po?po_id='.$id);
                        }
                    }


                    $date = date('Y-m-d H:i:s');

                    //update market
                    $data = array(
                        'pay_text'          => 'via po',
                        'shipping_text'     => $shipping_text,
                        'pay_time'          => $date,
                        'pay_user'          => $userStorage->id,
                        'shipping_yes_time' => $date,
                        'shipping_yes_id'   => $userStorage->id,

                    );
                    $where = $QMarket->getAdapter()->quoteInto('sn = ?',$sn);
                    $QMarket->update($data, $where);
                    //Tanong
                    //update checkmoney transaction
                    $company_id = $rows[0]['company_id'];
                    $transaction = array(
                        'd_id'      => $d_id,
                        'payment'   => $date,
                        'pay_time'  => $date,
                        'output'    => $sn_total,
                        'pay_money' => -$sn_total,
                        'type'      => 2,
                        'sn'        => $sn,
                        'user_id'   => $userStorage->id,
                        'create_by' => $userStorage->id,
                        'create_at' => $date,
                        'note'      => 'via po',
                        'company_id' => $company_id
                    );

                    // kiem tra sn da co trong checkmoney?
                    $selectSnTransaction = $Qcheckmoney->select()->where('sn = ?',$sn);
                    $checkSnTransaction  = $Qcheckmoney->fetchRow($selectSnTransaction);
                    if($checkSnTransaction){
                        $flashMessenger->setNamespace('error')->addMessage('Error: transaction sn '.$sn .' is existed');
                        $this->_redirect('/finance/order-by-po?po_id='.$id);
                    }
                    $Qcheckmoney->insert($transaction);

                    //todo log
                    $ip = $this->getRequest()->getServer('REMOTE_ADDR');
                    $info = 'Verify: Sale order number: '.$sn;
                    $QLog = new Application_Model_Log();
                    $QLog->insert( array (
                        'info'              => $info,
                        'user_id'           => $userStorage->id,
                        'ip_address'        => $ip,
                        'time'              => $date,
                    ) );

                }//End foreach;

                //update Balance
                foreach ($arrUpdateBalance as $value) {
                    $QStoreAccount->updateBalance($value);
                }

                $db->commit();
            }catch (Exception $e){
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage('error: '.$e->getMessage());
                $this->_redirect('/finance/order-by-po?po_id=',$id);
            }

            $flashMessenger->setNamespace('success')->addMessage('success!');
            $this->_redirect('/finance/order-by-po?po_id='.$id);
        }//End if check post
    }

    public function updateStockTetAction(){
        /**
         * @for admin: update stock Tet transaction for checkmoney
         */
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        set_time_limit( 0 );
        ini_set('memory_limit', -1);
        $db = Zend_Registry::get('db');
        $QCheckMoney    = new Application_Model_Checkmoney();
        $QStoreAccount  = new Application_Model_Storeaccount();
        $selectSns = $db->select()
            ->from(array('t'=>'tag_object'),array('t.*'))
            ->join(array('m'=>'market'),'t.object_id = m.sn',array())
            ->where('t.tag_id = ?',3447)
            //->where('t.object_id = ?','201503181534463335')
            ->where('m.add_time >= ?','2015-02-14')
            ->where('m.add_time <= ?','2015-02-14 23:59:59')
            ->group('t.object_id')
        ;

        $tags           = $db->fetchAll($selectSns);
        $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
        $date           = date('Y-m-d H:i:s');
        $intCount = 0;
        $db->beginTransaction();
        try{
            if($tags){
                foreach($tags as $tag){
                    $sn     = $tag['object_id'];
                    $select = $db->select()
                        ->from(array('m'=>'market'),array('sn_total'=>'SUM(m.total)','d_id'))
                        ->where('m.sn = ?',$sn)
                        ->where('m.add_time >= ?','2015-02-14')
                        ->where('m.add_time <= ?','2015-02-14 23:59:59')
                        ->group('m.sn')
                    ;

                    $row = $db->fetchRow($select);
                    if(!$row){
                        if($sn = '201503090803183610'){
                            //truong hop nay khong co don hang
                            continue;
                        }
                        exit('Error: '.$sn.' không tồn tại');
                    }
                    $selectSnCheckMoney = $db->select()
                            ->from(array('c'=>'checkmoney'),array('c.sn'))
                            ->where('c.sn = ?',$sn);
                    $checkSnCheckMoney = $db->fetchRow($selectSnCheckMoney);
                    if($checkSnCheckMoney){
                        exit('Error: '.$sn.' tồn tại đã tồn tại trong checkmoney');
                    }

                    $data = array(
                        'd_id'      => $row['d_id'],
                        'payment'   => $date,
                        'pay_time'  => $date,
                        'output'    => $row['sn_total'],
                        'pay_money' => -$row['sn_total'],
                        'type'      => 2,
                        'sn'        => $sn,
                        'user_id'   => $userStorage->id,
                        'create_by' => $userStorage->id,
                        'create_at' => $date,
                        'note'      => 'Payment Order Auto-generate;stock Tet'
                    );

                    $QCheckMoney->insert($data);
                    $QStoreAccount->updateBalance($row['d_id']);
                    $intCount+=1;//dat sai cho :(
                }

                $db->commit();
            }
        }catch (Exception $e){
            $db->rollback();
            exit('Error: '.$e->getMessage());
        }
        echo "Update ". $intCount." sn Success!";
    }

    //Tanong Get Data Credit Note 20160310
    public function start_discount_credit_note_sn($distributor_id,$user_id,$creditnote_sn)
    {           
        try {
                $flashMessenger = $this->_helper->flashMessenger;
                $db = Zend_Registry::get('db');

                //$db->beginTransaction();

                $stmt = $db->prepare("CALL start_discount_credit_note_sn('".$distributor_id."','".$user_id."','".$creditnote_sn."')");

                $stmt->execute();
                $credit_data = $stmt->fetchAll();
                $total_amount= $credit_data[0]['total_amount'];

               // $db->commit();
            }catch (exception $e) {
                $flashMessenger->setNamespace('error')->addMessage('Cannot Create Credit Note, please try again!');
            }
            
            return $creditnote_sn;
    }

    //Tanong Get Data Credit Note 20160310
    public function update_credit_note_sn($distributor_id,$user_id,$creditnote_sn)
    {        
        $total_amount=0;
        try {
                $flashMessenger = $this->_helper->flashMessenger;
                $db = Zend_Registry::get('db');

                //$db->beginTransaction();
                $user_discount=0;$sales_order_sn='';
                $stmt = $db->prepare("CALL update_credit_note_sn('".$distributor_id."','".$_creditnote_sn."','".$user_discount."','".$sales_order_sn."','".$_user_id."')");

                $stmt->execute();
               // $credit_data = $stmt->fetchAll();
                //$total_amount= $credit_data[0]['total_amount'];

               // $db->commit();
            }catch (exception $e) {
                $flashMessenger->setNamespace('error')->addMessage('Cannot Create Credit Note, please try again!');
            }
            
            return $total_amount;
    }


    //Tanong Get SalesOrderNoRef 20160313 1155
    //`gen_credit_note_sn_new_return`(_distributor_id INT,_user_id INT,_sn VARCHAR(20))
    public function get_credit_note_sn($db,$distributor_id,$user_id,$sn,$status)
    {
    try {
            $flashMessenger = $this->_helper->flashMessenger;

            $db->query("CALL gen_credit_note_sn_new_return('".$distributor_id."','".$user_id."',".$sn.",'".$status."')");

        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Create Credit Note No, please try again!');
        }
    }

    public function getCreateNoteNo_Ref($db,$distributor_id,$user_id,$sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $creditnote_sn="";
        try {
            $stmt = $db->query("CALL gen_running_no_ref('CN',".$sn.")");
            $stmt->execute();
            $result = $stmt->fetch();
            $creditnote_sn = $result['running_no'];

        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage($e);
        }
        return $creditnote_sn;
    }

    public function getReward_CreateNoteNo_Ref($db,$distributor_id,$user_id,$sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $creditnote_sn="";
        try {
            $stmt = $db->prepare("CALL gen_running_no_ref_reward('CN',".$sn.")");
            $stmt->execute();
            $result = $stmt->fetch();
            $creditnote_sn = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage($e);
        }
        return $creditnote_sn;
    }
    
    function oppoclupRewardUploadCnAction()
    {
        $QJointCircular = new Application_Model_JointCircular();
        $this->view->jointCircular = $QJointCircular->fetchAll();

        $QJointType = new Application_Model_JointType();


        $this->view->joint_type = $QJointType->get_cache();

        $QGood          = new Application_Model_Good();
        $whereGood      = array();
        $whereGood[]    = $QGood->getAdapter()->quoteInto('cat_id = ?' , PHONE_CAT_ID);
        $good = $QGood->fetchAll($whereGood)->toArray();
        $this->view->good = $good;

    }

    function saveOppoClupRewardConfirmAction()
    {
        $this->_helper->layout->disableLayout();
        //print_r($_POST);die;
        if ($this->getRequest()->getMethod() == 'POST') {

            $quater_no_sel       = $this->getRequest()->getParam('quater_no');
            $quater_year_sel     = $this->getRequest()->getParam('quater_year');

            define('MASS_BVG_LIST_ROW_START', 2);
            define('MASS_BVG_LIST_COL_key_sn', 0);
            define('MASS_BVG_LIST_COL_quater_year', 1);
            define('MASS_BVG_LIST_COL_quater_no', 2);
            define('MASS_BVG_LIST_COL_distributor_id', 3);
            define('MASS_BVG_LIST_COL_store_code', 4);
            define('MASS_BVG_LIST_COL_title', 5);
            define('MASS_BVG_LIST_COL_level_name', 6);
            define('MASS_BVG_LIST_COL_total_imei', 7);
            define('MASS_BVG_LIST_COL_creditnote_price', 8);
            define('MASS_BVG_LIST_COL_decorate_status', 9);
            define('MASS_BVG_LIST_COL_creditnote_price_confirm', 10);

            set_time_limit(0);
            ini_set('memory_limit', -1);
            $db = Zend_Registry::get('db');
            

            $progress = new My_File_Progress('parent.set_progress');
            $progress->flush(0);

            $upload = new Zend_File_Transfer();

            $uniqid = uniqid('', true);
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files'
                . DIRECTORY_SEPARATOR . 'mou'
                . DIRECTORY_SEPARATOR . $userStorage->id
                . DIRECTORY_SEPARATOR . $uniqid;

            if (!is_dir($uploaded_dir))
                @mkdir($uploaded_dir, 0777, true);

            
            $upload->setDestination($uploaded_dir);

            $upload->setValidators(array(
                'Size' => array('min' => 50, 'max' => 10000000),
                'Count' => array('min' => 1, 'max' => 1),
                'Extension' => array('xlsx', 'xls'),
            ));

            if (!$upload->isValid()) { // validate IF
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
                            $sError = 'Please choose a file in XLS or XLSX format.';
                            break;
                        case 'fileCountTooFew':
                            $sError = 'Please choose a PO file (in XLS or XLSX format)';
                            break;
                        case 'fileUploadErrorNoFile':
                            $sError = 'Please choose a PO file (in XLS or XLSX format)';
                            break;
                        case 'fileSizeTooBig':
                            $sError = 'File size is too big';
                            break;
                    }

                $this->view->error = $sError;

            } else {
                try {
                    
                    $db->beginTransaction();
                    
                    $path_info = pathinfo($upload->getFileName());
                    $filename =  $path_info['filename'];
                    $extension = $path_info['extension'];

                    $old_name = $filename . '.' . $extension;
                    $new_name = 'UPLOAD-' . md5($filename . uniqid('', true)) . '.' . $extension;

                    if (is_file($uploaded_dir . DIRECTORY_SEPARATOR . $old_name)) {
                        rename($uploaded_dir . DIRECTORY_SEPARATOR . $old_name, $uploaded_dir . DIRECTORY_SEPARATOR . $new_name);
                    } else {
                        $new_name = $old_name;
                    }

                    $upload->addFilter('Rename',
                       array('target' => $uploaded_dir. DIRECTORY_SEPARATOR .$new_name,
                             'overwrite' => true));

                    $upload->receive();
                    chmod($uploaded_dir. DIRECTORY_SEPARATOR .$new_name, 777);

                    $QFileLog = new Application_Model_FileUploadLog();
                    $QOppoClubRewardCn = new Application_Model_OppoClubRewardCn();
                    $QOppoClubRewardCnImei = new Application_Model_OppoClubRewardCnImei();

                    $data = array(
                        'staff_id' => $userStorage->id,
                        'folder' => $uniqid,
                        'filename' => $new_name,
                        'type' => 'mass Reward Confirm upload',
                        'real_file_name' => $filename . '.' . $extension,
                        'uploaded_at' => time(),
                    );

                    $log_id = $QFileLog->insert($data);

                    $number_of_order = 0;
                    $error_list = array();
                    $success_list = array();
                    $listBvgByProduct = array();

                    $QImei    = new Application_Model_Imei();

                    require_once 'PHPExcel.php';
                    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                    $cacheSettings = array('memoryCacheSize' => '8MB');
                    PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                    switch ($extension) {
                        case 'xls':
                            $objReader = PHPExcel_IOFactory::createReader('Excel5');
                            break;
                        case 'xlsx':
                            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                            break;
                        default:
                            throw new Exception("Invalid file extension");
                            break;
                    }

                    $objReader->setReadDataOnly(true);

                    $objPHPExcel = $objReader->load($uploaded_dir . DIRECTORY_SEPARATOR . $new_name);
                    $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

                    $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
                    $total_order_row = $highestRow - MASS_BVG_LIST_ROW_START + 1;

                    $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
                    $status=1; $d_id='';   
                                       
                    $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
                    $date           = date('Y-m-d H:i:s');

                    for ($i = MASS_BVG_LIST_ROW_START; $i <= $highestRow; $i++) {

                        $key_sn = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_key_sn, $i)
                            ->getValue());

                        $quater_year = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_quater_year, $i)
                            ->getValue());

                        $quater_no = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_quater_no, $i)
                            ->getValue());

                        $distributor_id = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_distributor_id, $i)
                            ->getValue());

                        $store_code = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_store_code, $i)
                            ->getValue());

                        $dealer_name = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_title, $i)
                            ->getValue());
                        
                        $level_name = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_level_name, $i)
                            ->getValue());

                        $total_imei = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_total_imei, $i)
                            ->getValue());

                        $decorate_status = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_decorate_status, $i)
                            ->getValue());

                        $creditnote_price = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_creditnote_price, $i)
                            ->getValue());

                        $creditnote_price_confirm = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_creditnote_price_confirm, $i)
                            ->getValue());
                      
                        $chk_file_reward = $QOppoClubRewardCn->check_file_import_reward_cn_confirm($quater_year_sel,$quater_no_sel);
                        if($chk_file_reward <= 0){
                            $data_error['quater_year'] = $quater_year_sel;
                            $data_error['quater_no'] = $quater_no_sel;
                            $data_error['message'] = "Cannot Import. Quater Year or Quater No Not existed in System";
                            $error_list[] = $data_error;
                            //exit();
                        }else{

                        $chk_reward = $QOppoClubRewardCn->check_reward_cn_confirm($quater_year,$quater_no,$distributor_id,$level_name,$key_sn,$creditnote_price_confirm);
                        //print_r($chk_reward);die;
                        if($chk_reward>0){
                            //print_r($chk_reward);

                           $key_cn = date('YmdHis') . substr(microtime(), 2, 4);
                           $creditnote_sn = $this->getReward_CreateNoteNo_Ref($db,$distributor_id,$userStorage->id,$key_cn);
                            if($creditnote_sn!=''){

                                // --------------Update Confirm------------------
                                $data = array(
                                    'confirm_date' => $date,
                                    'confirm_by' => $userStorage->id,
                                    'status_cn' => 1,
                                    'creditnote_price_confirm' => $creditnote_price_confirm,
                                    'creditnote_sn' => $creditnote_sn,
                                    'key_cn' => $key_cn,
                                );
                            
                                $whereReward = array();
                                $whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('key_sn = ?', $key_sn);
                                $whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_year = ?', $quater_year);
                                $whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_no = ?', $quater_no);
                                $whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);
                                $whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('level_name = ?', $level_name);

                                $QOppoClubRewardCn->update($data, $whereReward);


                                $data = array(
                                    'confirm_date' => $date,
                                    'confirm_by' => $userStorage->id,
                                    'creditnote_sn' => $creditnote_sn,
                                );
                            
                                $whereRewardImei = array();
                                $whereRewardImei[] = $QOppoClubRewardCnImei->getAdapter()->quoteInto('quater_year = ?', $quater_year);
                                $whereRewardImei[] = $QOppoClubRewardCnImei->getAdapter()->quoteInto('quater_no = ?', $quater_no);
                                $whereRewardImei[] = $QOppoClubRewardCnImei->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);

                                $QOppoClubRewardCnImei->update($data, $whereRewardImei);

                                //--------------------------------------

                                $reward="quater_year=".$quater_year.";quater_no=".$quater_no.";level_name=".$level_name;

                                $create_date = date('Y-m-d H:i:s');
                                $data = array(
                                    'distributor_id' => $distributor_id,
                                    'create_by' => $userStorage->id,
                                    'create_date' => $create_date,
                                    'creditnote_type' => 'CN',
                                    'total_amount' => $creditnote_price_confirm,
                                    'use_total' => 0,
                                    'balance_total' => $creditnote_price_confirm,
                                    'status' => 1,
                                    'creditnote_sn' => $creditnote_sn,
                                    'chanel' => 'reward',
                                    'sn' => $key_cn,
                                    'remark' => $reward,
                                );

                                $QCreditNote = new Application_Model_CreditNote();
                                $QCreditNote->insert($data);

                                // ----------------------------

                            }else{
                                $data_error['quater_year'] = $quater_year;
                                $data_error['quater_no'] = $quater_no;
                                $data_error['distributor_id'] = $d_id;
                                $data_error['store_code'] = $store_code;
                                $data_error['dealer_name'] = $dealer_name;
                                $data_error['level_name'] = $level_name;
                                $data_error['creditnote_price'] = $creditnote_price;
                                $data_error['message'] = "Reward Cannot Create Credit Note No.";
                                $error_list[] = $data_error;
                            }
                        }else{
                            $data_error['quater_year'] = $quater_year;
                            $data_error['quater_no'] = $quater_no;
                            $data_error['distributor_id'] = $d_id;
                            $data_error['store_code'] = $store_code;
                            $data_error['dealer_name'] = $dealer_name;
                            $data_error['level_name'] = $level_name;
                            $data_error['creditnote_price'] = $creditnote_price;
                            $data_error['message'] = "Dealer Already Import Or Reward is not existed in System";
                            $error_list[] = $data_error;
                        }

                        //print_r($result);die;
                        //$data['dealer_name'] = $dealer_name;
                        $status = $result['code'];
                        if ($result['code'] == 0){
                            $success_list[] = $data;                           
                        } else {
                            $data['message'] = $result['message'];
                            $error_list[] = $data;
                        }
                    }
                        $number_of_order++;
                        $percent = round($number_of_order * 100 / $total_order_row, 1);
                        $progress->flush($percent);
                    }

                    $data = array(
                        'total' => $total_order_row,
                        'failed' => count($error_list),
                        'succeed' => $total_order_row - count($error_list),
                    );

                    // xuất file excel các order lỗi
                    if (is_array($error_list) && count($error_list) > 0) 
                    {
                        
                        $data['error_file_name'] = $d_id.'-'.'FAILED-' . md5(microtime(true) . uniqid('', true)) . '.' . $extension;

                        $objPHPExcel_out = new PHPExcel();
                        $objPHPExcel_out->createSheet();
                        $objWorksheet_out = $objPHPExcel_out->getActiveSheet();
                        //
                        // get product list
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_quater_year, 1, 'quater_year');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_quater_no, 1, 'quater_no');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_distributor_id, 1, 'distributor_id');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_store_code, 1, 'store_code');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_title, 1, 'store_name');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_level_name, 1, 'level_name');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_total_imei, 1, 'total_imei');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_creditnote_price, 1, 'creditnote_price');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_creditnote_price_confirm, 1, 'creditnote_price_confirm');
                        //
                        switch ($extension) {
                            case 'xls':
                                $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel_out);
                                break;
                            case 'xlsx':
                                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel_out);
                                break;
                            default:
                                throw new Exception("Invalid file extension");
                                break;
                        }

                        $new_file_dir = $uploaded_dir . DIRECTORY_SEPARATOR . $data['error_file_name'];

                        //Tanong
                        $objWriter->save($new_file_dir);
                    }
                    // END IF // xuất file excel các order lỗi

                    $where = $QFileLog->getAdapter()->quoteInto('id = ?', $log_id);
                    $QFileLog->update($data, $where);

                    $this->view->error_list = $error_list;
                    $this->view->objWorksheet = $objWorksheet;
                    $this->view->number_of_order = $number_of_order;

                    //commit
                    $db->commit();

                    $this->view->error_file = isset($data['error_file_name']) ? (HOST
                        . 'files'
                        . DIRECTORY_SEPARATOR . 'mou'
                        . DIRECTORY_SEPARATOR . $userStorage->id
                        . DIRECTORY_SEPARATOR . $uniqid
                        . DIRECTORY_SEPARATOR . $data['error_file_name']) : false;

                } // end of Try
                catch (Exception $e) {
                    $db->rollback();
                    $this->view->error = $e->getMessage();
                    $progress->flush(0);
                }
                $progress->flush(100);
            }// end of check file

           // unlink(APPLICATION_PATH . '/../public/files/mou/lock');

            
        } // end of check POST
    }

    private function _exportExcelRewardCreditNote_Wait_Confirm($data)
    {
         
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Credit Note For Reward To Wait Confirm'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        $heads = array(
            'key_sn',
            'Quater Year',
            'Quater No',
            'Distributor id',
            'Distributor Code',
            'Retailer Name',
            'Level Name',
            'Total Imei',
            'Decorate Status',
            'Creditnote Amount',
            'Creditnote Amount Confirm',
        );
                fputcsv($output, $heads);


     
        $i = 2;
        foreach($data as $item)
        {
           $decorate_status="-";    
            if($item['decorate_status']==1){
                $decorate_status="Approve";
            }else{
                $decorate_status="Wait To Check";
            }
            $row = array();
            $row[] = $item['key_sn'];
            $row[] = $item['quater_year'];
            $row[] = $item['quater_no'];
            $row[] = $item['distributor_id'];
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $item['level_name'];
            $row[] = $item['total_imei'];
            $row[] = $decorate_status;
            $row[] = $item['creditnote_price'];
            $row[] = $item['creditnote_price_confirm'];
            

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    private function _exportExcelRewardCreditNote_Confirm($data)
    {
         
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Credit Note For Reward Confirmed'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        $heads = array(
            'Quater Year',
            'Quater No',
            'Start Date',
            'End Date',
            'Distributor id',
            'Distributor Code',
            'Retailer Name',
            'Level Name',
            'Total Imei',
            'Decorate Status',

            'Credit Note No',
            'Creditnote Amount',
            'Creditnote Amount Confirm',
            'Confirm Date',
            'Confirm By',
            'Status',
            
        );
                fputcsv($output, $heads);


     
        $i = 2;
        foreach($data as $item)
        {
            $decorate_status="-";    
            if($item['decorate_status']==1){
                $decorate_status="Approve";
            }else{
                $decorate_status="Wait To Check";
            }
            $row = array();
            $row[] = $item['quater_year'];
            $row[] = $item['quater_no'];
            $row[] = $item['start_date'];
            $row[] = $item['end_date'];
            $row[] = $item['distributor_id'];
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $item['level_name'];
            $row[] = $item['total_imei'];
            $row[] = $decorate_status;
            $row[] = $item['creditnote_sn'];
            $row[] = $item['creditnote_price'];
            $row[] = $item['creditnote_price_confirm'];
            $row[] = $item['confirm_date'];
            $row[] = $item['confirm_by'];
            $row[] = $item['status_cn'];
            

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    public function getCreditNoteNo_Ref($db,$sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $sn_ref="";
        try {
            $stmt = $db->query("CALL gen_running_no_ref_reward('CN',".$sn.")");
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
        }
        return $sn_ref;
    }

    public function _exportExcelOppoAllGreen($data)
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        // this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'OPPO All Green Report -'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);

        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'ID',
            'ROUND NO',
            'ROUND YEAR',
            'AIR NUMBE',
            'TAX ID',
            'DISTRIBUTOR ID',
            'DISTRIBUTOR NAME',
            'STORE ID',
            'STORE CODE',
            'STORE NAME',
            'START DATE',
            'END DATE',
            'SHOP TYPE',
            'TOTAL REWARD_PRICE ',
            'TAX PRICE',
            'CREDITNOTE PRICE CONFIRM',
            'ASM CONFIRM BY',
            'ASM CONFIRM DATE',
            'CONFIRM BY' ,
            'CONFIRM DATE' ,
            'STATUS CN' ,
            'CREDITNOTE_SN',
            'CREATE_DATE',
            'REASON REMARK',
            'STORE CODE',
        );

        fputcsv($output, $heads);

        $QDistributor   = new Application_Model_Distributor();
        $distributor_cache = $QDistributor->get_cache2();

        $i = 2;

        foreach($data as $item) {
            $row = array();
            $row[] = $item['id']; 
            $row[] = $item['round_no']; 
            $row[] = $item['round_year']; 
            $row[] = $item['air_number']; 
            $row[] = '\''.$item['tax_id'].'\'';
            $row[] = $item['d_id']; 
            $row[] = $item['title']; 
            $row[] = $item['store_id'];
            $row[] = $item['store_code'];  
            $row[] = $item['store_name']; 
            $row[] = $item['start_date']; 
            $row[] = $item['end_date']; 
            $row[] = $item['shop_type']; 
            $row[] = $item['total_reward_price']; 
            $row[] = $item['tax_price']; 
            $row[] = $item['creditnote_price_confirm']; 
            $row[] = $item['asm_confirm_by']; 
            $row[] = $item['asm_confirm_date']; 
            $row[] = $item['confirm_by']; 
            $row[] = $item['confirm_date']; 
            $row[] = $item['status_cn']; 
            $row[] = $item['creditnote_sn']; 
            $row[] = $item['create_date']; 
            $row[] = $item['reason_remark']; 
            $row[] = $item['store_code']; 


            fputcsv($output, $row);
            unset($item);
            unset($row);
        
        }
        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    public function duePaymentOrderAction()
    {   
        $sort              = $this->getRequest()->getParam('sort', 'p.id');
        $desc              = $this->getRequest()->getParam('desc', 1);
        $page              = $this->getRequest()->getParam('page', 1);
        $d_id              = $this->getRequest()->getParam('d_id');
        $due_from          = $this->getRequest()->getParam('due_from',date('d/m/Y') );
        $due_to            = $this->getRequest()->getParam('due_to',date('d/m/Y') );
        
        if ($due_from == '') {
            $due_from = date('d/m/Y');
        }
        if ($due_to == '') {
            $due_to = date('d/m/Y');
        }


        $limit = 20;
        $total = 0;
        $params = array(
            'due_from'  =>$due_from,
            'due_to'    =>$due_to,
            'd_id'    =>$d_id,
            );
        $QMarket = new Application_Model_Market();
        $due = $QMarket->duePaymentOrder($page, $limit, $total, $params);
        $distributor = $QMarket->SelectDistributor();
        
        
        $this->view->due         = $due;
        $this->view->params      = $params;
        $this->view->distributor      = $distributor;
        $this->view->desc       = $desc;
        $this->view->sort       = $sort;
        $this->view->limit      = $limit;
        $this->view->total      = $total;
        $this->view->url        = HOST . 'due-payment-order/' . ($params ? '?' . http_build_query($params) .'&' : '?');

        $this->view->offset = $limit * ($page - 1);
        $this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER');

       
    }

}
