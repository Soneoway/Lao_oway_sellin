<?php
class CheckmoneyController extends My_Controller_Action{

    public function init(){

    }

    public function indexAction(){
        
    }

    function decimal_remove_comma($priceFloat)
    {
        $price = str_replace(",","",$priceFloat);;
        return $price;
    }

    public function addAction(){

        $flashMessenger 	= $this->_helper->flashMessenger;
        $messages 			= array();
        $messages_success 	= array();
        $id                 = $this->getRequest()->getParam('id');
        $sn                 = $this->getRequest()->getParam('sn', null);
        $action             = $this->getRequest()->getParam('ac', null);
        $total_money        = $this->getRequest()->getParam('total_money', 0);
        $title = 'New Transaction';
        if($id){
            //$QCheckmoney = new Application_Model_Checkmoney();
            //$row = $QCheckmoney->find($id)->current();

            $db     = Zend_Registry::get('db');

            $select_checkmoney = $db->select()
                ->from(array('c'=>'checkmoney'),array('c.*','total_pay'=>'SUM(c.pay_money)'))
                ->joinleft(array('m'=>'market'), 'm.sn = c.sn',array('sn_ref'=>'m.sn_ref'))
                ->where('c.id = ?',$id)
            ;
            $row = $db->fetchAll($select_checkmoney);
            //print_r($row[0]);
            $this->view->row  = $row[0];
            $title = 'Update Transaction';
            $this->view->id_key = $id;
        }else{
            $this->view->id_key = "";
        }
        $this->view->title = $title;


        $QBank = new Application_Model_Bank();
        $banks = $QBank->fetchAll(null,'name asc');
        $this->view->banks              = $banks;

        //Get Distributors
        $QDistributor = new Application_Model_Distributor();
        $distributors = $QDistributor->get_all();
        array_unshift($distributors, array('id'=>'0','title'=>'UNKNOWN'));
        $this->view->distributors   = $distributors;

        $messages 			+= $flashMessenger->setNamespace('error')->getMessages();
        $messages_success 	+= $flashMessenger->setNamespace('success')->getMessages();

        $this->view->messages_success 	= $messages_success;
        $this->view->messages 			= $messages;
        $this->view->action             = $action;
        $this->view->total_money        = $total_money;
        $this->view->sn                 = $sn;
    }

    public function saveCheckmoneyAction(){
        //$this->_helper->layout->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);

        $flashMessenger  = $this->_helper->flashMessenger;
        $action                = $this->getRequest()->getParam('ac',NULL);
        if($action=='edit'){
           $ch_id              = $this->getRequest()->getParam('id',null); 
        }else{
            $ch_id=='';
        }
        //print_r($_POST);die;
        $d_id                  = $this->getRequest()->getParam('d_id',NULL);
        $pay_time              = $this->getRequest()->getParam('pay_time');
        $pay_money             = $this->getRequest()->getParam('pay_money', 0);
        $pay_banktransfer      = $this->getRequest()->getParam('pay_banktransfer', 0);
        $pay_servicecharge     = $this->getRequest()->getParam('pay_servicecharge', 0);
        $pay_service           = $this->getRequest()->getParam('pay_service', 0);
        $bank                  = $this->getRequest()->getParam('bank', NULL);
        $bank_transaction_code = $this->getRequest()->getParam('bank_transaction_code','');
        $bank_serial           = $this->getRequest()->getParam('bank_serial');
        $note                  = $this->getRequest()->getParam('note');
        $content               = $this->getRequest()->getParam('content','');
        $type                  = $this->getRequest()->getParam('type');
        $company_id            = $this->getRequest()->getParam('company_id');

        //filter
        $ch_id                 = intval($ch_id);
        $d_id                  = intval($d_id);
        $pay_money             = str_replace ( ',', '', trim($pay_money) );
        $pay_time              = trim($pay_time);
        $bank_transaction_code = trim($bank_transaction_code);
        $bank_serial           = trim($bank_serial);
        $content               = trim($content);
        $note                  = trim($note);
        $sn_ref                = $this->getRequest()->getParam('sn',0);//tạm thời thay cho $sn_string
        //$sn                  = $this->getRequest()->getParam('sn',0);
        //$sn                  = intval($sn);
        $checkValue            = true;

        //if(!is_float($pay_money) AND $pay_money){
        if(is_float($pay_money)){
            $flashMessenger->setNamespace('error')->addMessage("'Money' must a digit");
            $this->_redirect('/checkmoney/list');
        }

        if($d_id < 0){
            $flashMessenger->setNamespace('error')->addMessage("Please select a dealer");
            $this->_redirect('/checkmoney/list');  
        }

        if(!$type){
            $flashMessenger->setNamespace('error')->addMessage("Please select 'Type money imput'");
            $this->_redirect('/checkmoney/list');   
        }

        /*
        if(!$bank AND ( $type == 1 OR $type == 2) ){
            $flashMessenger->setNamespace('error')->addMessage("Please, choose a bank!");
            $this->_redirect('/checkmoney/list');

        }
        */
        if(!$bank){
            $bank = NULL;
        }

        if(!$pay_time){
            $flashMessenger->setNamespace('error')->addMessage("Please, insert date time!");
            $this->_redirect('/checkmoney/list');   
        }

        if(!$company_id){
            $flashMessenger->setNamespace('error')->addMessage("Please, select company!");
            $this->_redirect('/checkmoney/list');      
        }

        $flashMessenger   = $this->_helper->flashMessenger;
        $messages         = array();
        $messages_success = array();
        $db               = Zend_Registry::get('db');

        if($this->getRequest()->isPost()){
            $db->beginTransaction();
            try {
                // Dữ liệu cũ trước khi thay đổi
                $QCheckMoney    = new Application_Model_Checkmoney();
                $QStoreaccount  = new Application_Model_Storeaccount();
                $userStorage    = Zend_Auth::getInstance()->getStorage()->read();


                if($sn_ref!=""){
                    $select_sn = $db->select()
                        ->from(array('m'=>'market'),array('sn'=>'m.sn'))
                        ->where('m.sn_ref = ?',$sn_ref)
                        ->group('m.sn');
                       // echo $select_sn;die;
                    $row = $db->fetchAll($select_sn);
                    $sn_key = $row[0]['sn'];               
                }else{
                    $sn_key='0';
                }

                /*-------------------File Pay Slip Upload--------------------------*/
                $upload = new Zend_File_Transfer();
                $path_info = pathinfo($upload->getFileName());
                $filename =  $path_info['filename'];

                //echo "filename>".$filename;die;
                if($filename!=''){

                    $uniqid = uniqid('', true);

                    if($sn_key !=0){
                        $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
                            . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files'
                            . DIRECTORY_SEPARATOR . 'finance'. DIRECTORY_SEPARATOR . 'pay_slips'
                            . DIRECTORY_SEPARATOR . $sn_key;

                        $file_pay_slip = DIRECTORY_SEPARATOR . 'pay_slips'
                            . DIRECTORY_SEPARATOR . $sn_key. DIRECTORY_SEPARATOR;
                    }else{
                        $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
                            . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files'
                            . DIRECTORY_SEPARATOR . 'finance'. DIRECTORY_SEPARATOR . 'pay_slips'
                            . DIRECTORY_SEPARATOR . $userStorage->id
                            . DIRECTORY_SEPARATOR . $uniqid;

                        $file_pay_slip = DIRECTORY_SEPARATOR . 'pay_slips'
                            . DIRECTORY_SEPARATOR . $userStorage->id
                            . DIRECTORY_SEPARATOR . $uniqid. DIRECTORY_SEPARATOR;
                    }    

                    if (!is_dir($uploaded_dir))
                        @mkdir($uploaded_dir, 0777, true);

                    
                    $upload->setDestination($uploaded_dir);

                    // Upload Max 5 MB
                    $upload->setValidators(array(
                        'Size' => array('min' => 50, 'max' => 5000000),
                        'Count' => array('min' => 1, 'max' => 3),
                        'Extension' => array('jpg','jpeg', 'PNG','GIF'),
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
                            $this->_redirect('/checkmoney/list'); 
                        }

                    } else {

                        //$filename =  $path_info['filename'];
                        $extension = $path_info['extension'];

                        $old_name = $filename . '.' . $extension;
                        $new_name = 'UPLOAD-' . md5($filename . uniqid('', true)) . '.' . $extension;

                        if (is_file($uploaded_dir . DIRECTORY_SEPARATOR . $old_name)) {
                            rename($uploaded_dir . DIRECTORY_SEPARATOR . $old_name, $uploaded_dir . DIRECTORY_SEPARATOR . $new_name);
                        } else {
                            $new_name = $old_name;
                        }

                        $file_pay_slip .= $new_name;

                        $upload->addFilter('Rename',
                           array('target' => $uploaded_dir. DIRECTORY_SEPARATOR .$new_name,
                                 'overwrite' => true));

                        $upload->receive();
                        chmod($uploaded_dir. DIRECTORY_SEPARATOR .$new_name, 777);


                        /*--------ReSize-------------*/
                        /*
                        $im = new Imagick();
                        $datadir = $uploaded_dir."/resize/";
                        $im->newImage( 200, 100, "red", "png" );
                        $im->writeImage( $datadir.'test.png' );
                        */
                        /*--------End ReSize-------------*/
                    }
                }
            /*-------------------End File Pay Slip Upload--------------------------*/
                $date =date('Y-m-d H:i:s');
                $note_new='PayMoney='.number_format($pay_money,2) .' Fee transfer='.number_format($pay_banktransfer,2).' Service Charge='.number_format($pay_servicecharge,2).' ค่าอะไหล่และค่าบริการ='.number_format($pay_service,2);

                if($note_new !=$note){
                    $note_new = $note_new.' '.$note;
                }

                $data = array(
                        'd_id'                  => $d_id,
                        'bank'                  => $bank,
                        'pay_money'             => $pay_money,
                        'pay_banktransfer'      => $pay_banktransfer,
                        'pay_servicecharge'     => $pay_servicecharge,
                        'pay_service'           => $pay_service,
                        'type'                  => $type,
                        'pay_time'              => $pay_time,
                        'bank_serial'           => $bank_serial,
                        'bank_transaction_code' => $bank_transaction_code,
                        'note'                  => $note_new,
                        'content'               => $content,
                        'company_id'            => $company_id,
                        'addition'              => 1,
                        'user_id'               => $$userStorage->id,
                        'finance_confirm_id'    => $$userStorage->id,
                        'finance_confirm_date'  => $date,
                        'sn'                    => $sn_key,

                );

                if($file_pay_slip !=''){
                    $data['file_pay_slip'] = $file_pay_slip; 
                }

                if($ch_id){
                    $old_checkmoney = $QCheckMoney->find($ch_id)->current();
                    $data['updated_at'] = $date;
                    $where = $db->quoteInto('id = ?',$ch_id);
                    $QCheckMoney->update($data,$where);    
                }else{
                    $data['create_by'] = $userStorage->id;
                    $data['create_at'] = $date;
                    $QCheckMoney->insert($data);
                }

                $QCheckmoneyPaymentorder = new Application_Model_CheckmoneyPaymentorder();
                $where = $QCheckmoneyPaymentorder->getAdapter()->quoteInto('sn = ?', $sn);
                $QCheckmoneyPaymentorder->update(array('status' => 1), $where);

                if($data['d_id'] >= 0){  
                    if($ch_id){//Trường hợp update
                        if($data['d_id'] == $old_checkmoney['d_id']){
                            $QStoreaccount->updateBalance( $data['d_id'] );
                        }else{
                            $QStoreaccount->updateBalance( $data['d_id'] );
                            if( intval( $old_checkmoney['d_id'] ) >= 0 ){
                                $QStoreaccount->updateBalance($old_checkmoney['d_id']);
                            }
                        }
                    }else{//Trường hợp thêm mới.
                        $QStoreaccount->updateBalance( $data['d_id'] );
                    }
                    
                }//End check d_id

                $flashMessenger->setNamespace('success')->addMessage("Success");
                $db->commit();
                $this->_redirect('/checkmoney/list'); 
            } catch (Exception $e) {
                $db->rollBack();
                $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
                if($ch_id){
                    $this->_redirect('/checkmoney/add?id='.$ch_id);        
                }
            }//End try catch
            $this->_redirect('/checkmoney/list');
        }// End if check post

    }

    public function getstoreAction(){

        $title = $this->getRequest()->getParam('title');

        $db		 	= Zend_Registry::get('db');
        $select 	= $db->select()
            ->from('distributor',array('id','title'))
            ->where('title like ?',"%".$title."%")
            ->where('del = ?',0)
            ->limit(30,0)
            ;
        $result 	= $db->fetchAll($select);
        $this->_helper->json->sendJson($result);
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

    }

    public function getorderAction(){

        $distributor_id = $this->getRequest()->getParam('id');
        $db 	= Zend_Registry::get('db');
        $select = $db->select()->from('market',array('sn','total_sn'=>'SUM(total)','add_time'))
            ->where('d_id = ?',$distributor_id)
            ->where('pay_time IS  NULL')
            ->where('status = ?',1)
            ->group('sn')
        ;
        $result = $db->fetchAll($select);
        $this->_helper->json->sendJson($result);
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

    }

    public function viewAction(){

        $messages 			= array();
        $messages_success 	= array();

        $db = Zend_Registry::get('db');
        $id = $this->getRequest()->getParam('id');
        $select = $db->select()
            ->from(array('ch'=>'checkmoney'),	array('ch.*','ch_id'=>'ch.id'))
            ->joinleft(array('s'=>'store_account'),	'ch.d_id = s.d_id',array('remain'=>'s.balance'))//trường hợp không nhận d_id
            ->joinleft(array('d'=>'distributor'),	'd.id = ch.d_id',array('d.unames','d.title'))//trường hợp không nhận d_id
            ->joinleft(array('b'=>'bank'),'ch.bank=b.id',array("bank_name"=>'b.name'))// trường hợp transaction trừ tiền
            ->where($db->quoteInto('ch.id = ?',$id));
           // echo $select;
        $currentTransaction = $db->fetchRow($select);
        if($currentTransaction){

            $this->view->transaction = $currentTransaction;	//checkmoney
            $sn 					 = $currentTransaction['sn'];
            if(trim($sn) != "" ){
                //get sn from market
                $arr_sn 		= explode(',', $sn);
                $QMarket 		= new Application_Model_Market();
                $select_market 	= $db->select()
                    ->from(array('m'=>'market'),array('m.sn','m.sn_ref','total_all'=>'SUM(m.total)'))
                    ->where('m.sn IN (?)',$arr_sn)
                    ->group('m.sn');
                $markets 					= $db->fetchAll($select_market);
                $this->view->markets 		= $markets;
            }// End sn


            $remain_balance = 0;

            if($currentTransaction['d_id'] != NULL){
                if($currentTransaction['pay_time'] == null){
                   $pay_time = $currentTransaction['create_at'];
                }else{
                    $pay_time = $currentTransaction['pay_time'];
                }

                $select_balance = $db->select()
                    ->from(array('c'=>'checkmoney'),array('total_pay'=>'SUM(c.pay_money)'))
                    ->where('c.d_id = ?',$currentTransaction['d_id'])
                    ->where('c.pay_time <= ?',$pay_time)
                    ->group('c.d_id')
                ;
                $remain_balance = $db->fetchOne($select_balance);
            }

            $this->view->remain_balance = $remain_balance;

        }else{
            $messages[] = 'Transaction is not exist';
        }

        $this->view->messages_success 	= $messages_success;
        $this->view->messages 			= $messages;
    }

    public function historyAction(){

        $db 		= Zend_Registry::get('db');
        $page 		= $this->getRequest()->getParam('page',1);
        $d_id 		= $this->getRequest()->getParam('d_id',null);//distributor ID
        $limit 		= 20;
        $sort 		= $this->getRequest()->getParam('sort','pay_time');
        $desc    	= $this->getRequest()->getParam('desc', 1);
        $total 		= 0;
        $type 		= $this->getRequest()->getParam('type','');

        $params = array(
            'd_id'	=>	$d_id,
            'type'	=> 	$type,
            'sort'	=>	$sort,
            'desc'	=>	$desc,
        );

        $QCheckMoney 	= new Application_Model_Checkmoney();
        $transactions 	= $QCheckMoney->fetchPaginationHistory($page, $limit, $total, $params);

            $QDistributor                    = new Application_Model_Distributor();
            $curDistributor                  = $QDistributor->find($d_id)->current();
            $this->view->current_distributor = $curDistributor;
            $dealer                          = $curDistributor['title'];
            if(!$dealer){
                $dealer = 'UNKNOWN';
            }

        //Remain balance
        $QStoreAccount             = new Application_Model_Storeaccount();
        $main_retailer             = $QDistributor->getRootDistributor($d_id);
        $this->view->main_retailer = $main_retailer;
        
        $curStoreAccount           = $QStoreAccount->getBalance($d_id);

        $total_balance              = $QStoreAccount->getBalanceByGroup($d_id);
        $this->view->total_balance  = $total_balance;
        $this->view->dealer 		= $dealer;
        $this->view->storeaccount 	= $curStoreAccount;
        $this->view->transactions 	= $transactions;
        $this->view->limit 			= $limit;
        $this->view->total 			= $total;
        $this->view->page 			= $page;
        $this->view->offset 		= $limit*($page-1);
        $this->view->params 		= $params;
        $this->view->sort 			= $sort;
        $this->view->desc 			= $desc;
        $this->view->url 			= HOST.'checkmoney/history/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->money_types = unserialize(MONEY_TYPE);
    }

    public function delAction(){
        $ch_id 			= $this->getRequest()->getParam('id',null);
        $QCheckMoney 	= new Application_Model_Checkmoney();
        $checkmoney 	= $QCheckMoney->find($ch_id)->current();
        $db 			= Zend_Registry::get('db');
        $flashMessenger = $this->_helper->flashMessenger;
        if($checkmoney){
            $db->beginTransaction();
            try {

                $d_id = $checkmoney['d_id'];
                $checkmoney->delete();

                if ($d_id) {
                    $QStoreAccount = new Application_Model_Storeaccount();
                    $QStoreAccount->updateBalance($d_id);
                }

                $flashMessenger->setNamespace('success')->addMessage("Deleted");
                $db->commit();
            }
            catch (Exception $e){
                $db->rollBack();
                $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
            }
        }else{

            $flashMessenger->setNamespace('error')->addMessage("Transaction is not exist");
        }
        $this->_redirect('/checkmoney/list');
    }

    public function getbalanceAction(){
        $d_id 			= $this->getRequest()->getParam('id',null);
        
        $QDistributor   = new Application_Model_Distributor();
        $where_d        = $QDistributor->getAdapter()->quoteInto('id = ?', $d_id);
        $distributor    = $QDistributor->fetchRow($where_d);

        $QStoreAccount 	= new Application_Model_Storeaccount();
       // $where 	        = $QStoreAccount->getAdapter()->quoteInto('d_id = ?', $d_id);
       // $row            = $QStoreAccount->fetchRow($where);

        

        $total_balance  = $QStoreAccount->getBalanceByGroup($d_id);

        $main_distributor_id = $distributor->main_distributor_id;
        if($main_distributor_id !='')
        {
          $q_total_credit_all  = $QStoreAccount->getMainDistributorBalance($d_id); 
          $q_store_balance  = $QStoreAccount->getMainDistributorBalance($d_id); 
          $q_total_balance  = $QStoreAccount->getMainDistributorBalance($d_id); 
          //$distributor_balance_row  = $QStoreAccount->getBalance($d_id);

          //$store_balance = $q_store_balance[0]['balance'];
          //$total_balance = $q_total_balance[0]['balance'];

          $total_credit_all = $q_total_credit_all['credit_amount'];
          $store_balance = $q_total_balance['balance'];
          $total_balance = $q_total_balance['balance'];

        }else{
          $total_credit_all  =  $distributor->credit_amount;
          $q_store_balance  = $QStoreAccount->getBalance($d_id);
          $q_total_balance  = $QStoreAccount->getBalance($d_id);

          $store_balance = $q_store_balance['balance'];
          $total_balance = $q_total_balance['balance'];
        }

        

        $QCredit        = new Application_Model_Credit();
        $where_c        = $QCredit->getAdapter()->quoteInto('id = ?', $distributor->credit_type);
        $credit_type    = $QCredit->fetchRow($where_c);

        $data = array();
        $status = 1;
        if($store_balance) {
            $data = array(
                'balance'       =>  number_format($store_balance,2),
                'total_balance' =>  number_format($total_balance,2),
                'retailer_name' =>  $distributor->title,
                'add'           =>  $distributor->add_tax,
                'credit_amount' =>  number_format($total_credit_all,2),
                'credit_type'   =>  $credit_type->name,
                'rank'   =>  $distributor->rank,
                'credit_status'   =>  number_format($distributor->credit_status,0)
            );
        } else {
            $data = array(
                'balance'       =>  number_format(0,2),
                'total_balance' =>  number_format(0,2),
                'retailer_name' =>  $distributor->title,
                'add'           =>  $distributor->add_tax,
                'credit_amount' =>  number_format($total_credit_all,2),
                'rank'   =>  $distributor->rank,
                'credit_status'   =>  number_format($distributor->credit_status,0)
            );
        }

        $this->_helper->json->sendJson(array('status'=>$status,'result'=>$data));
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        //return
        //status: 1
        //result: {'balance: 1000','add': '123 tran hung dao'}
        
    }

    public function listAction(){

        $flashMessenger        = $this->_helper->flashMessenger;
        $messages              = $flashMessenger->setNamespace('error')->getMessages();
        $messages_success      = $flashMessenger->setNamespace('success')->getMessages();
        $d_id                  = $this->getRequest()->getParam('d_id','');
        $bank                  = $this->getRequest()->getParam('bank',null);//array
        $bank_serial           = $this->getRequest()->getParam('bank_serial',null);
        $bank_transaction_code = $this->getRequest()->getParam('bank_transaction_code',null);
        $type_time             = $this->getRequest()->getParam('type_time',null);
        $from_time             = $this->getRequest()->getParam('from_time',date('Y-m-d',strtotime('0 days')));
        $to_time               = $this->getRequest()->getParam('to_time',date('Y-m-d',strtotime('0 days')));
       // $from_time   = $this->getRequest()->getParam('from_time', date('d/m/Y', strtotime('-0 day')));

       // $to_time               = $this->getRequest()->getParam('to_time');
        $type_money            = $this->getRequest()->getParam('type_money',null);
        $from_money            = $this->getRequest()->getParam('from_money',null);
        $to_money              = $this->getRequest()->getParam('to_money',null);
        $note                  = $this->getRequest()->getParam('note','');
        $content               = $this->getRequest()->getParam('content','');
        $sn                    = $this->getRequest()->getParam('sn'); 
        $export                = $this->getRequest()->getParam('export',0);
        $export_retailer       = $this->getRequest()->getParam('export_retailer',0);
        $export_full           = $this->getRequest()->getParam('export_full',0);
        $export_smartmobile    = $this->getRequest()->getParam('smartmobile',0);
        $export_oppo           = $this->getRequest()->getParam('oppo',0);
        $export_by_day         = $this->getRequest()->getParam('export_by_day',0);
        $total                 = 0;
        $limit                 = LIMITATION;
        $sort                  = $this->getRequest()->getParam('sort','pay_time');
        $desc                  = $this->getRequest()->getParam('desc', 1);
        $page                  = $this->getRequest()->getParam('page',1);

        if($bank != NULL){
            $bank = array_unique($bank);
        }
        
        $from_money = trim($from_money);
        if(is_numeric($from_money)){
            $from_money = $from_money;
        }else{
            $from_money = '';
        }

        $to_money = trim($to_money);
        if(is_numeric($to_money)){
            $to_money = $to_money;
        }else{
            $to_money = '';
        }

        $params 	= array(
            'd_id'					=>	$d_id,
            'note'					=>	trim($note),
            'content'               => 	trim($content),
            'bank_serial'           =>	trim($bank_serial),
            'bank_transaction_code' =>  trim($bank_transaction_code),
            'type_time'             => 	$type_time,
            'from_time'             =>	trim($from_time),
            'to_time'               =>	trim($to_time),
            'type_money'            =>  $type_money,
            'from_money'            => 	trim($from_money),
            'to_money'              => 	trim($to_money),
            'sort'                  =>	$sort,
            'desc'                  =>	$desc,
            'export'                => 	$export,
            'bank'                  =>  $bank,
            'sn'                    =>  trim($sn),
            'export_smartmobile'    =>  $export_smartmobile,
            'export_oppo'           => $export_oppo
        );
       
        if($params['from_money'] === '' ){
        	unset($params['from_money']);
        }

        if($params['to_money'] === '' ){
        	unset($params['to_money']);
        }

        $QCheckmoney 		= new Application_Model_Checkmoney();

        if($export=='1'){

			set_time_limit( 0 );
			error_reporting( 0 );
			ini_set('display_error', 0);
			ini_set('memory_limit', -1);
            $data = $QCheckmoney->fetchPaginationByRetailer(NULL, NULL, $total, $params);
            $this->_getDataForExcel($data,$params);

        }else if($export=='2'){

            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            //$data = $QCheckmoney->fetchCashCollection(NULL, NULL, $total, $params);
            $this->_getDataForExcelCashCollection($data,$params);

        }

        if($export_retailer){
            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            $from = '';
            $to = '';
            $data = $QCheckmoney->_getMoneyIn($params,$from,$to);
            $this->_exportByRetailer($data,$from,$to);
        }

        if($export_by_day){
            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            $from = '';
            $to = '';
            $data = $QCheckmoney->fetchPaginationByDay($params,$from,$to);
            $this->_exportByDay($data,$from,$to);
        }

        if($export_full OR $export_smartmobile OR $export_oppo){
            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            $from   = '';
            $to     = '';
            $data   = $QCheckmoney->getReportFullByWarehouse($params,$from,$to);
            if($export_oppo){
                $name = 'oppo';
            }elseif($export_smartmobile){
                $name = 'smartmobile';
            }else{
                $name = 'full';
            }
            $this->_exportAuditing($data,$from,$to,$name);
        }
        
        $list 	= $QCheckmoney->fetchPaginationByRetailer($page, $limit, $total, $params);
        //lấy unknown d_id
        $unknown = NULL;

        $QStoreAccount = new Application_Model_Storeaccount();

        foreach($list as $key => $value):
            $total_balance = $QStoreAccount->getBalanceByGroup($value['d_id']);
            $list[$key]['total_balance'] = $total_balance['balance'];
            $list[$key]['total_balance_smartmobile'] = $total_balance['balance_smartmobile'];
            if($value['d_id']  == 0):
                $unknown = $value;
                unset($list[$key]);
                break;
            endif;
        endforeach;

        //moving the unknown to first position
        if($unknown != NULL){
            $unknown['d_id'] = 0;
            array_unshift($list, $unknown);
        }

        //Get Bank
        $QBank = new Application_Model_Bank();
        $banks = $QBank->fetchAll(null,'name asc');

        //Get Distributors
        $QDistributor = new Application_Model_Distributor();
        $distributors = $QDistributor->get_all_full();// from cache
        array_unshift($distributors, array('id'=>'0','title'=>'UNKNOWN'));
        $this->view->distributors = $distributors;

        $this->view->banks 				= $banks;
        $this->view->list 				= $list;
        $this->view->limit 				= $limit;
        $this->view->total 				= $total;
        $this->view->page 				= $page;
        $this->view->offset 			= $limit*($page-1);
        $this->view->params 			= $params;
        $this->view->sort 				= $sort;
        $this->view->desc 				= $desc;
        $this->view->url 				= HOST.'checkmoney/list/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->messages_success 	= $messages_success;
        $this->view->messages 			= $messages;

    }

    public function getcheckmoneyAction(){

        $d_id        = $this->getRequest()->getParam('d_id2','');
        $bank_id     = $this->getRequest()->getParam('bank_id',null);//array
        $type        = $this->getRequest()->getParam('type',null);
        $from_time   = $this->getRequest()->getParam('from_time',null);
        $to_time     = $this->getRequest()->getParam('to_time',null);
        $type_money  = $this->getRequest()->getParam('type_money',null);
        $from_money  = $this->getRequest()->getParam('from_money',null);
        $to_money    = $this->getRequest()->getParam('to_money',null);
        $bank_serial = $this->getRequest()->getParam('bank_serial',null);
        $note        = $this->getRequest()->getParam('note','');
        $content     = $this->getRequest()->getParam('content','');
        $class       = $this->getRequest()->getParam('_class');
        $sort        = $this->getRequest()->getParam('sort','pay_time');
        $desc        = $this->getRequest()->getParam('desc',1);

        $sn    = $this->getRequest()->getParam('sn');
        $page  = $this->getRequest()->getParam('page',1);
        $limit = 200;
        $total = 0;
        $params = array(
            'd_id'        => $d_id,            
            'bank_id'     => $bank_id,            
            'type'        => $type,       
            'from_time'   => $from_time,       
            'to_time'     => $to_time,         
            'type_money'  => $type_money,              
            'bank_serial' => $bank_serial,     
            'note'        => $note,            
            'content'     => $content,         
            'sn'          => $sn,  
            'sort'        => $sort,
            'desc' => $desc,
            );
        $QCheckmoney = new Application_Model_Checkmoney();
        $list   = $QCheckmoney->fetchPagination($page, $limit, $total, $params);
        $this->view->result = $list;
        $this->view->class = $class;
        $this->_helper->layout()->disableLayout(true);
        $this->view->page           = $page;
        $this->view->offset         = $limit*($page-1);
        $show_view_more  = 1;
        if( ($page * $limit) >= $total){
            $show_view_more = 0;
        }
        $this->view->show_view_more = $show_view_more;

    }

    public function paymentOrderAction(){
        $flashMessenger 	= $this->_helper->flashMessenger;
        $messages 			= $flashMessenger->setNamespace('error')->getMessages();
        $messages_success 	= $flashMessenger->setNamespace('success')->getMessages();

        $limit 		= LIMITATION;
        $d_id       = $this->getRequest()->getParam('d_id');
        $status     = $this->getRequest()->getParam('status');
        $page 		= $this->getRequest()->getParam('page',1);
        $sort 		= $this->getRequest()->getParam('sort','id');
        $desc    	= $this->getRequest()->getParam('desc', 1);
        $total 		= 0;
        $params 	= array(
            'status'    =>	$status,
            'd_id'		=>	$d_id,
            'sort'		=>	$sort,
            'desc'		=>	$desc,
        );

        $params = array_filter($params);

        if ($status!='')
            $params['status'] = $status;

        $QCheckmoneyPaymentorder = new Application_Model_CheckmoneyPaymentorder();
        $list = $QCheckmoneyPaymentorder->fetchPagination($page, $limit, $total, $params);

        $QDistributor = new Application_Model_Distributor();
        $this->view->distributors_cached = $QDistributor->get_cache();

        $QStaff = new Application_Model_Staff();
        $this->view->staffs_cached = $QStaff->get_cache();

        $QBank = new Application_Model_Bank();
        $this->view->banks = $QBank->fetchAll();

        $this->view->list 				= $list;
        $this->view->limit 				= $limit;
        $this->view->total 				= $total;
        $this->view->page 				= $page;
        $this->view->offset 			= $limit*($page-1);
        $this->view->params 			= $params;
        $this->view->sort 				= $sort;
        $this->view->desc 				= $desc;
        $this->view->url 				= HOST.'checkmoney/payment-order/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->messages_success 	= $messages_success;
        $this->view->messages 			= $messages;
    }

    public function confirmPaymentOrderAction(){
        $flashMessenger 	= $this->_helper->flashMessenger;
        $id             = $this->getRequest()->getParam('payment_order_id');
        $select_bank_id = $this->getRequest()->getParam('select_bank_id');
        $receive_time   = $this->getRequest()->getParam('receive_time');
        $company_id     = $this->getRequest()->getParam('company_id');
        $QCheckmoneyPaymentorder = new Application_Model_CheckmoneyPaymentorder();
        $where = $QCheckmoneyPaymentorder->getAdapter()->quoteInto('id = ?', $id);

        $CheckmoneyPaymentorder = $QCheckmoneyPaymentorder->fetchRow($where);

        if (!$CheckmoneyPaymentorder){
            $flashMessenger->setNamespace('error')->addMessage("Error ID Confirm Payment");
            $this->_redirect('/checkmoney/payment-order');
        }

        if ($CheckmoneyPaymentorder['status'] == 1){
            $flashMessenger->setNamespace('error')->addMessage("This Confirm Payment was confirmed");
            $this->_redirect('/checkmoney/payment-order');
        }

        if (!$select_bank_id){
            $flashMessenger->setNamespace('error')->addMessage("Please select the Bank");
            $this->_redirect('/checkmoney/payment-order');
        }

        if(!$receive_time){
            $flashMessenger->setNamespace('error')->addMessage("Please insert date time");
            $this->_redirect('/checkmoney/payment-order');
        }

        // insert vao checkmoney
        try {
            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            $date = date('Y-m-d H:i:s');

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $data_ch = array(
                'd_id'		=> $CheckmoneyPaymentorder['d_id'],
                'bank'	    => $select_bank_id,
                'pay_time'	=> $receive_time,
                'pay_money'	=> $CheckmoneyPaymentorder['payment_order'],
                'type'		=> 1,
                'user_id'   => $userStorage->id,
                'create_by' => $userStorage->id,
                'create_at' => $date,
                'note'      => 'Payment Order Auto-generate',
                'addition'  => 1,
                'company_id' => $company_id
            );

            // insert vao checkmoney
            $QCheckmoney 		= new Application_Model_Checkmoney();
            $QCheckmoney->insert($data_ch);

            // update
            $where = $QCheckmoneyPaymentorder->getAdapter()->quoteInto('id = ?', $id);
            $QCheckmoneyPaymentorder->update(array('status' => 1), $where);

            // update balance
            $QStoreaccount 	= new Application_Model_Storeaccount();
            $QStoreaccount->updateBalance($CheckmoneyPaymentorder['d_id']);

            $db->commit();

            $flashMessenger->setNamespace('success')->addMessage("Confirmed Done!");
            $this->_redirect('/checkmoney/payment-order');

        } catch (Exception $e){
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage("Cannot update, please try again!");
            $this->_redirect('/checkmoney/payment-order');
            
        }

    }
    
    public function _getDataForExcelCashCollection($data,$params){

        if($params['to_time']){
            $params['to_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }
        $db = Zend_Registry::get('db');
       // print_r($data);die;
        /*
        

        $arr_d_id      = array();
        $d_id_list =""; 
        foreach($data as $item){
            $arr_d_id[] = $item['d_id'];
            $d_id_list .= "'".$item['d_id']."',";
        }

        $d_id_list = trim($d_id_list,",");
    */
        $arr_d_id=0;

        if($arr_d_id == 0){

           // print_r($_GET);die;
            /*
            $select = $db->select()
                ->from(array('ch' => 'checkmoney'),array('ch.sn','ch.TYPE','ch.company_id','ch.pay_money','ch.output','ch.bank_transaction_code','ch.content' 
,'ch.note','ch.balance','ch.d_id','ch.pay_time','creditnote_sn'=> NEW Zend_Db_Expr("'' COLLATE utf8_unicode_ci")))
                ->joinleft(array('b' => 'bank'), 'b.id = ch.bank',array('bank_name' => 'b.name'))
                ->joinleft(array('m'=>'market'),'m.sn = ch.sn',array('m.invoice_number','m.sn_ref','m.payment_type'))
                ->group(array('ch.id','m.sn'))
                //->order('ch.pay_time desc')
                ->where('ch.d_id IN (?)',$arr_d_id);
                ;

            if( isset($params['bank']) AND $params['bank']  )
            {
                if( is_array($params['bank']) ){
                    $select->where('ch.bank IN (?)',$params['bank']);
                }else{
                    $select->where('ch.bank = ?',$params['bank']);
                }
            }

            if( isset($params['bank_serial']) AND $params['bank_serial'] ){
                $select->where('ch.bank_serial like ?','%'.$params['bank_serial'].'%');
            }

            if( isset($params['bank_transaction_code']) AND $params['bank_transaction_code']    ){
                $select->where('ch.bank_transaction_code like ?','%'.$params['bank_transaction_code'].'%');
            }

            if( isset($params['note']) AND $params['note'] != '' ){
                $select->where('ch.note like ?','%'.$params['note'].'%');
            }

            if( isset($params['content']) AND $params['content'] ){
                $select->where('ch.content like ?','%'.$params['content'].'%');
            }

            if(isset($params['type_time']) AND $params['type_time']){
                if($params['type_time'] == 3){
                    if( isset($params['from_time']) AND $params['from_time']    ){
                        $select->where('ch.create_at >= ?',$params['from_time']);
                    }
                    if( isset($params['to_time']) AND $params['to_time']    ){
                        $select->where('ch.create_at <= ?',$params['to_time']);
                    }
                    $select->where('ch.type = ?',$params['type_time']);
                }else{
                    if( isset($params['from_time']) AND $params['from_time']    ){
                        $select->where('ch.pay_time >= ?',$params['from_time']);
                    }
                    if( isset($params['to_time']) AND $params['to_time']    ){
                        $select->where('ch.pay_time <= ?',$params['to_time']);
                    }
                }
            }else{
                if( isset($params['from_time']) AND $params['from_time']    ){
                    $select->where('ch.pay_time >= ?',$params['from_time']);
                }
                if( isset($params['to_time']) AND $params['to_time']    ){
                    $select->where('ch.pay_time <= ?',$params['to_time']);
                }
            }

            if(isset($params['type_money']) AND $params['type_money']){
                $select->where('ch.type = ?',$params['type_money']);
            }

            if(isset($params['from_money']) AND $params['from_money'] !=''){
                $select->where('ch.pay_money >= ?',$params['from_money']);
            }

            if(isset($params['to_money']) AND $params['to_money'] !=''){
                $select->where('ch.pay_money <= ?',$params['to_money']);
            }
            */
            

            if( isset($params['from_time']) AND $params['from_time'])
            {
                $pay_time_st = $params['from_time'];
                $pay_time_en = $params['to_time'];
                $where_paytime="
                    AND (ck.pay_time >= '".$pay_time_st."') 
                    AND (ck.pay_time <= '".$pay_time_en."')
                ";
            }

            if( isset($params['d_id']) AND $params['d_id'])
            {
                $d_id = $params['d_id'];
                $where_d_id=" AND (ck.d_id = '".$d_id."') ";
            }

            if( isset($params['sn']) AND $params['sn'])
            {
                $sn = $params['sn'];
                $where_sn=" AND (ck.sn = '".$sn."') or (ck.sn =(SELECT sn FROM market WHERE sn_ref='".$sn."' GROUP BY sn)) ";
            }

            /*--------------Total Order Amount------------------*/
            $sql_total_pay_order="SELECT 
            `m`.`sn`,
            '2' as `TYPE`,
            '1' AS `company_id`,
             (ROUND(SUM(m.total),2)*-1) AS `pay_money`,
             (ROUND(SUM(m.total),2)*-1) AS `output`,
            '' AS `bank_transaction_code`,
            '' AS `content`,
            '' AS `note`,
            0 AS `balance`,
            `m`.`d_id`,
            `m`.`pay_time`,
            '' COLLATE utf8_unicode_ci AS `creditnote_sn`,
            '' AS `bank_name`,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type` 
            ,'1' as seq
          FROM
             `market` AS `m` 
          WHERE 1=1
            and m.canceled <>1
            AND m.sn IN (SELECT ck.sn
            FROM checkmoney ck 
            WHERE  
              1=1 ".$where_paytime.$where_d_id.$where_sn."
              AND ck.sn <>'0') 
            GROUP BY `m`.`sn`";

            /*--------------Tranfer Money Amount------------------*/
            $sql_checkmoney=" SELECT 
            `ch`.`sn`,
            `ch`.`TYPE`,
            `ch`.`company_id`,
            ROUND((`ch`.`pay_money`),2) as pay_money,
            ROUND((`ch`.`pay_money`),2) as output,
            `ch`.`bank_transaction_code`,
            `ch`.`content`,
            `ch`.`note`,
            `ch`.`balance`,
            `ch`.`d_id`,
            `ch`.`pay_time`,
            '' COLLATE utf8_unicode_ci AS `creditnote_sn`,
            `b`.`name` AS `bank_name`,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type` 
            ,'2' as seq
            FROM
              `checkmoney` AS `ch` 
              LEFT JOIN `bank` AS `b` 
                ON b.id = ch.bank 
              LEFT JOIN `market` AS `m` 
                ON m.sn = ch.sn 
            WHERE 1=1
            AND ch.sn <>'0' 
            AND ch.type ='1'
            and m.canceled <>1
            AND ch.sn IN (SELECT ck.sn
            FROM checkmoney ck 
            WHERE  
              1=1 ".$where_paytime.$where_d_id.$where_sn."
              AND ck.sn <>'0')
            GROUP BY `ch`.`id`,
              `m`.`sn` 
            ";

            /*--------------Total Use Credit Note Amount------------------*/
            $sql_credit_note_tran=" SELECT 
            ch.sales_order AS sn,
            '5' AS TYPE,
            '1' AS company_id,
            ROUND(ch.use_discount,2) AS pay_money,
            ROUND(ch.use_discount,2) AS output,
            '' AS bank_transaction_code,
            '' content,
            '' AS note,
            '0' AS balance,
            ch.distributor_id AS d_id,
            ch.update_date AS pay_time,
            ch.creditnote_sn COLLATE utf8_unicode_ci AS creditnote_sn,
            '' AS bank_name,
            m.invoice_number,
            m.sn_ref 
            ,m.payment_type
            ,'3' as seq
          FROM
            credit_note_tran AS ch 
            LEFT JOIN market AS m 
              ON m.sn = ch.sales_order 
          WHERE 1=1
            AND ch.sales_order <> '0' 
            and m.canceled <>1
            AND ch.sales_order IN 
            (SELECT 
              ck.sn 
            FROM
              checkmoney ck 
            WHERE 1=1 ".$where_paytime.$where_d_id.$where_sn."
              AND ck.sn <> '0')
          GROUP BY ch.id,
            m.sn  
            ";

            /*--------------Total Delivery Fee Amount------------------*/

            $sql_pay_delivery_fee="SELECT 
            `m`.`sn`,
            '6' as `TYPE`,
            '1' AS `company_id`,
             (ROUND(SUM(distinct m.delivery_fee),2)*-1) AS `pay_money`,
             (ROUND(SUM(distinct m.delivery_fee),2)*-1) AS `output`,
            '' AS `bank_transaction_code`,
            '' AS `content`,
            '' AS `note`,
            0 AS `balance`,
            `m`.`d_id`,
            `m`.`pay_time`,
            '' COLLATE utf8_unicode_ci AS `creditnote_sn`,
            '' AS `bank_name`,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type`
            ,'4' as seq 
          FROM
             `market` AS `m` 
          WHERE 1=1
            AND m.delivery_fee >0
            and m.canceled <>1
            AND m.sn IN (SELECT ck.sn
            FROM checkmoney ck 
            WHERE  
              1=1 ".$where_paytime.$where_d_id.$where_sn."
              AND ck.sn <>'0') 
            GROUP BY `m`.`sn`";

            /*--------------Total pay banktransfer Amount------------------*/

            $sql_checkmoney_pay_banktransfer=" SELECT 
            `ch`.`sn`,
            '7' as `TYPE`,
            `ch`.`company_id`,
            ROUND((`ch`.`pay_banktransfer`),2) as pay_money,
            ROUND((`ch`.`pay_banktransfer`),2) as output,
            `ch`.`bank_transaction_code`,
            `ch`.`content`,
            `ch`.`note`,
            `ch`.`balance`,
            `ch`.`d_id`,
            `ch`.`pay_time`,
            '' COLLATE utf8_unicode_ci AS `creditnote_sn`,
            `b`.`name` AS `bank_name`,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type`
            ,'5' as seq 
            FROM
              `checkmoney` AS `ch` 
              LEFT JOIN `bank` AS `b` 
                ON b.id = ch.bank 
              LEFT JOIN `market` AS `m` 
                ON m.sn = ch.sn 
            WHERE 1=1
            AND ch.sn <>'0' 
            AND ch.type ='1'
            and m.canceled <>1
            and ch.pay_banktransfer >0
            AND ch.sn IN (SELECT ck.sn
            FROM checkmoney ck 
            WHERE  
              1=1 ".$where_paytime.$where_d_id.$where_sn."
              AND ck.sn <>'0')
            GROUP BY `ch`.`id`,
              `m`.`sn` 
            ";

             /*--------------Total pay_servicecharge Amount------------------*/

            $sql_checkmoney_pay_servicecharge=" SELECT 
            `ch`.`sn`,
            '8' as `TYPE`,
            `ch`.`company_id`,
            ROUND((`ch`.`pay_servicecharge`),2) as pay_money,
            ROUND((`ch`.`pay_servicecharge`),2) as output,
            `ch`.`bank_transaction_code`,
            `ch`.`content`,
            `ch`.`note`,
            `ch`.`balance`,
            `ch`.`d_id`,
            `ch`.`pay_time`,
            '' COLLATE utf8_unicode_ci AS `creditnote_sn`,
            `b`.`name` AS `bank_name`,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type` 
            ,'6' as seq
            FROM
              `checkmoney` AS `ch` 
              LEFT JOIN `bank` AS `b` 
                ON b.id = ch.bank 
              LEFT JOIN `market` AS `m` 
                ON m.sn = ch.sn 
            WHERE 1=1
            AND ch.sn <>'0' 
            AND ch.type ='1'
            and m.canceled <>1
            and ch.pay_servicecharge >0
            AND ch.sn IN (SELECT ck.sn
            FROM checkmoney ck 
            WHERE  
              1=1 ".$where_paytime.$where_d_id.$where_sn."
              AND ck.sn <>'0')
            GROUP BY `ch`.`id`,
              `m`.`sn` 
            ";

            /*--------------Total pay service Amount------------------*/

            $sql_checkmoney_pay_service=" SELECT 
            `ch`.`sn`,
            '9' as `TYPE`,
            `ch`.`company_id`,
            ROUND((`ch`.`pay_service`),2) as pay_money,
            ROUND((`ch`.`pay_service`),2) as output,
            `ch`.`bank_transaction_code`,
            `ch`.`content`,
            `ch`.`note`,
            `ch`.`balance`,
            `ch`.`d_id`,
            `ch`.`pay_time`,
            '' COLLATE utf8_unicode_ci AS `creditnote_sn`,
            `b`.`name` AS `bank_name`,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type` 
            ,'7' as seq
            FROM
              `checkmoney` AS `ch` 
              LEFT JOIN `bank` AS `b` 
                ON b.id = ch.bank 
              LEFT JOIN `market` AS `m` 
                ON m.sn = ch.sn 
            WHERE 1=1
            AND ch.sn <>'0' 
            AND ch.type ='1'
            and m.canceled <>1
            and ch.pay_service >0
            AND ch.sn IN (SELECT ck.sn
            FROM checkmoney ck 
            WHERE  
              (1=1 ".$where_paytime.$where_d_id.$where_sn."
              AND ck.sn <>'0')
            GROUP BY `ch`.`id`,
              `m`.`sn`) 
            ";


            /*--------------Total Discount 1 %------------------*/

            $sql_checkmoney_discount_spc="SELECT 
            `m`.`sn`,
            '2' as `TYPE`,
            '1' AS `company_id`,
                CASE
                  WHEN m.d_id = 3691 
                THEN (ROUND(SUM(m.total)*1/100, 2) ) 
                  ELSE
                    '0'
                  END AS `pay_money`,
                CASE
                  WHEN m.d_id = 3691 
                THEN (ROUND(SUM(m.total)*1/100, 2) ) 
                  ELSE
                    '0'
                  END AS `output`,
            '' AS `bank_transaction_code`,
            '' AS `content`,
            '' AS `note`,
            0 AS `balance`,
            `m`.`d_id`,
            `m`.`pay_time`,
            '' COLLATE utf8_unicode_ci AS `creditnote_sn`,
            '' AS `bank_name`,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type` 
            ,'8' as seq
          FROM
             `market` AS `m` 
          WHERE 1=1
            and m.canceled <>1
            AND m.sn IN (SELECT ck.sn
            FROM checkmoney ck 
            WHERE  
              1=1 ".$where_paytime.$where_d_id.$where_sn."
              AND ck.sn <>'0') 
            GROUP BY `m`.`sn`";

            
            $sql_result = "select * from (".$sql_total_pay_order.' UNION '.$sql_checkmoney.' UNION '.$sql_credit_note_tran.' UNION '.$sql_pay_delivery_fee.' UNION '.$sql_checkmoney_pay_banktransfer.' UNION '.$sql_checkmoney_pay_servicecharge.' UNION '.$sql_checkmoney_pay_service.' UNION '.$sql_checkmoney_discount_spc.")t1 order by t1.sn,t1.seq,t1.type desc,t1.pay_time";
            

             //$sql_result = "select * from (".$sql_total_pay_order.")t1 order by t1.sn,t1.type desc,t1.pay_time"; 


            //echo $sql_result;die;

            try {
                $result_ch = $db->fetchAll($sql_result);    
            } catch (Exception $e) {
                echo 'error';
                exit;
            }
            //print_r($result_ch);die;
        }
        /*
        foreach($result_ch as $item){
            foreach($data as $i => $d){

                $data[$i]['transaction'][] = $item;
                
                if($item['d_id'] == $d['d_id'] ){
                    $data[$i]['transaction'][] = $item;
                    break;
                }

            }
        }
        */
        //print_r($result_ch);die;
        $this->_exportExcelExportCashCollectionCSV($result_ch);
    
    }

      public function _exportExcelExportCashCollectionCSV($data)
      {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        //print_r($data);die;
        //$db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
       // $filename = 'Credit Note For Reward Confirmed'.date('d-m-Y H-i-s').'.csv';
        $filename = 'MoneyChecksCashCollection_'.date('d-m-Y H-i-s').'.csv';
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
            'NO',
            'STORE ID',
            'RETAILER',
            'STORE CODE',
            'BANK',
            'COMPANY',
            'TYPE',
            'PAYMENT TYPE',
            'ORDER NUMBER',
            'INVOICE NUMBER',
            'CREDIT NOTE',
            'IN / OUT MONEY',
            'IN / OUT TIME',
            
            
            'BANK TRANSACTION CODE',
            
            'CONTENT',
            'NOTE',
            'BALANCE'
        );
        fputcsv($output, $heads);

        $QDistributor = new Application_Model_Distributor();
        $distributors_cached = $QDistributor->get_cache();
        $distributors_storecode_cached = $QDistributor->storecode_get_cache();

        $i = 2;
        $index    = 2;
        foreach($data as $t){
            //$title = trim($t['title']);
            //$store_code = trim($t['store_code']);
            //foreach($item['transaction'] as $t)
            //{

                
                $alpha = 'A';
                $arrMoneyType = unserialize(MONEY_TYPE);
                foreach($arrMoneyType as $key => $value):
                    if($t['TYPE'] == $key){
                        $type_title = $value;
                        break;
                    }
                endforeach;
                $sn_ref=$t['sn_ref'];
                if($sn_ref==''){
                  $sn_ref=$t['sn'];  
                }

                //$title = ( trim($item['title']) == '' ) ? '' : trim($item['title']);
                //$store_code = ( trim($item['store_code']) == '' ) ? '' : trim($item['store_code']);
                //$title = trim($item['title']);
                //$store_code = trim($item['store_code']);

                $title = isset($distributors_cached[$t['d_id']]) ? $distributors_cached[$t['d_id']] : '';
                $store_code = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

                $row = array();
                $row[] = $index - 1;
                $row[] = $t['d_id'];
                $row[] = $title;
                $row[] = $store_code;
                
                $row[] = $t['bank_name'];
                
                $row[] = ( $t['company_id'] == 1 ) ? 'OPPO':'TM';
                
                $row[] = $type_title;
                $row[] = $t['payment_type'];
                $row[] = $sn_ref;
                $row[] = $t['invoice_number'];
                $row[] = $t['creditnote_sn'];
                $money = $t['pay_money'];
                if($t['type'] == 2)
                {
                    $money = $t['output'] * -1;
                }
                
                $row[] = $money;
                $row[] = $t['pay_time'];
                

                $row[] = $t['bank_transaction_code'];
                
                $row[] = $t['content'];
                $row[] = $t['note'];
                $row[] = $t['balance'];
                
                $index++;
            
                fputcsv($output, $row);
                unset($t);
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


    //Tanong
    public function _exportExcelExportCashCollection($data){

        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'STT',
            'RETAILER',
            'STORE CODE',
            'BANK',
            'COMPANY',
            'IN / OUT MONEY',
            'IN / OUT TIME',
            'ORDER NUMBER',
            'INVOICE NUMBER',
            'TYPE',
            'BANK TRANSACTION CODE',
            'CREDIT NOTE',
            'CONTENT',
            'NOTE',
            'BALANCE'
        );

        $PHPExcel->setActiveSheetIndex(0);
        $sheet    = $PHPExcel->getActiveSheet();
        $alpha    = 'A';
        $index    = 1;

        foreach($heads as $key)
        {
            $sheet->setCellValue($alpha.$index, $key);
            $alpha++;
        }
        $index    = 2;

        foreach($data as $item){

            foreach($item['transaction'] as $t){
                $alpha = 'A';
                $arrMoneyType = unserialize(MONEY_TYPE);
                foreach($arrMoneyType as $key => $value):
                    if($t['type'] == $key){
                        $type_title = $value;
                        break;
                    }
                endforeach;
                $sn_ref=$t['sn_ref'];
                if($sn_ref==''){
                  $sn_ref=$t['sn'];  
                }

                $title = ( trim($item['title']) == '' ) ? 'UNKNOWN' : trim($item['title']);
                $sheet->setCellValue($alpha++.$index, $index - 1);
                $sheet->setCellValue($alpha++.$index, $title);
                $sheet->setCellValue($alpha++.$index, trim($item['store_code']));
                $sheet->setCellValue($alpha++.$index,$t['bank_name'] );
                $sheet->setCellValue($alpha++.$index, ( $t['company_id'] == 1 ) ? 'OPPO':'TM' );
                $money = $t['pay_money'];
                if($t['type'] == 2){
                    $money = $t['output'] * -1;
                }
                $sheet->getCell($alpha++.$index)->setValueExplicit( $money, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->setCellValue($alpha++.$index, $t['pay_time']);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $sn_ref , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['invoice_number'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $type_title , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_transaction_code'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['content'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['note'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $item['balance'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $index++;

            }

        }

        $filename = 'MoneyChecksCashCollection_'.date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        $objWriter->save('php://output');
        exit;
    }

    public function _getDataForExcel($data,$params){

        if($params['to_time']){
            $params['to_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }

    	$db = Zend_Registry::get('db');

        $arr_d_id      = array();
        foreach($data as $item){
            $arr_d_id[] = $item['d_id'];
        }

        if(count($arr_d_id) > 0){
    		$select = $db->select()
                ->from(array('ch' => 'checkmoney'),array('ch.*'))
                ->joinleft(array('b' => 'bank'), 'b.id = ch.bank',array('bank_name' => 'b.name'))
                ->joinleft(array('m'=>'market'),'m.sn = ch.sn',array('m.invoice_number','m.sn_ref'))
                ->group(array('ch.id','m.sn'))
                ->order('ch.pay_time desc')
                ->where('ch.d_id IN (?)',$arr_d_id);
                ;

            if(	isset($params['bank']) AND $params['bank']	){
                if( is_array($params['bank']) ){
                    $select->where('ch.bank IN (?)',$params['bank']);
                }else{
                    $select->where('ch.bank = ?',$params['bank']);
                }
            }

            if( isset($params['bank_serial']) AND $params['bank_serial'] ){
                $select->where('ch.bank_serial like ?','%'.$params['bank_serial'].'%');
            }

            if(	isset($params['bank_transaction_code']) AND $params['bank_transaction_code']	){
                $select->where('ch.bank_transaction_code like ?','%'.$params['bank_transaction_code'].'%');
            }

            if(	isset($params['note']) AND $params['note'] != '' ){
                $select->where('ch.note like ?','%'.$params['note'].'%');
            }

            if( isset($params['content']) AND $params['content'] ){
                $select->where('ch.content like ?','%'.$params['content'].'%');
            }

            if(isset($params['type_time']) AND $params['type_time']){
                if($params['type_time'] == 3){
                    if(	isset($params['from_time']) AND $params['from_time']	){
                        $select->where('ch.create_at >= ?',$params['from_time']);
                    }
                    if(	isset($params['to_time']) AND $params['to_time']	){
                        $select->where('ch.create_at <= ?',$params['to_time']);
                    }
                    $select->where('ch.type = ?',$params['type_time']);
                }else{
                    if(	isset($params['from_time']) AND $params['from_time']	){
                        $select->where('ch.pay_time >= ?',$params['from_time']);
                    }
                    if(	isset($params['to_time']) AND $params['to_time']	){
                        $select->where('ch.pay_time <= ?',$params['to_time']);
                    }
                }
            }else{
                if(	isset($params['from_time']) AND $params['from_time']	){
                    $select->where('ch.pay_time >= ?',$params['from_time']);
                }
                if(	isset($params['to_time']) AND $params['to_time']	){
                    $select->where('ch.pay_time <= ?',$params['to_time']);
                }
            }

            if(isset($params['type_money']) AND $params['type_money']){
                $select->where('ch.type = ?',$params['type_money']);
            }

            if(isset($params['from_money']) AND $params['from_money'] !=''){
                $select->where('ch.pay_money >= ?',$params['from_money']);
            }

            if(isset($params['to_money']) AND $params['to_money'] !=''){
                $select->where('ch.pay_money <= ?',$params['to_money']);
            }
       // echo $select;die;
            try {
                $result_ch = $db->fetchAll($select);    
            } catch (Exception $e) {
                echo 'error';
                exit;
            }
        
        }

        foreach($result_ch as $item){
            foreach($data as $i => $d){
                if($item['d_id'] == $d['d_id'] ){
                    $data[$i]['transaction'][] = $item;
                    break;
                }
            }
        }
        //print_r($data);die;
    	$this->_exportExcel($data);
    
    }

    //Tanong
    public function _exportExcel($data){

        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'STT',
            'RETAILER',
            'STORE CODE',
            'BANK',
            'COMPANY',
            'IN / OUT MONEY',
            'IN / OUT TIME',
            'ORDER NUMBER',
            'INVOICE NUMBER',
            'TYPE',
            'BANK TRANSACTION CODE',
            'CONTENT',
            'NOTE',
            'BALANCE'
        );

        $PHPExcel->setActiveSheetIndex(0);
        $sheet    = $PHPExcel->getActiveSheet();
        $alpha    = 'A';
        $index    = 1;

        foreach($heads as $key)
        {
            $sheet->setCellValue($alpha.$index, $key);
            $alpha++;
        }
        $index    = 2;

        foreach($data as $item){

            foreach($item['transaction'] as $t){
                $alpha = 'A';
                $arrMoneyType = unserialize(MONEY_TYPE);
                foreach($arrMoneyType as $key => $value):
                    if($t['type'] == $key){
                        $type_title = $value;
                        break;
                    }
                endforeach;
                $sn_ref=$t['sn_ref'];
                if($sn_ref==''){
                  $sn_ref=$t['sn'];  
                }

                $title = ( trim($item['title']) == '' ) ? 'UNKNOWN' : trim($item['title']);
                $sheet->setCellValue($alpha++.$index, $index - 1);
                $sheet->setCellValue($alpha++.$index, $title);
                $sheet->setCellValue($alpha++.$index, trim($item['store_code']));
                $sheet->setCellValue($alpha++.$index,$t['bank_name'] );
                $sheet->setCellValue($alpha++.$index, ( $t['company_id'] == 1 ) ? 'OPPO':'TM' );
                $money = $t['pay_money'];
                if($t['type'] == 2){
                    $money = $t['output'] * -1;
                }
                $sheet->getCell($alpha++.$index)->setValueExplicit( $money, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->setCellValue($alpha++.$index, $t['pay_time']);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $sn_ref , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['invoice_number'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $type_title , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_transaction_code'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['content'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['note'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $item['balance'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $index++;

            }

        }

        $filename = 'MoneyChecks_'.date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        $objWriter->save('php://output');
        exit;
    }

    private  function  _exportByRetailer($data,$from,$to){
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'No.',
            'Store Name',
            'Store Code',
            'First Period Balance',
            'Total Money Sellout in Period',
            'Total Money Receive in Period',
            'Ending Blance',
            'Blance Account',
        );

        $PHPExcel->setActiveSheetIndex(0);
        $sheet    = $PHPExcel->getActiveSheet();

        $index    = 1;
        $sheet->setCellValue('A'.$index,'Statistics From: '.$from.' To '.$to);
        $alpha    = 'A';
        $index    = 2;
        foreach($heads as $key)
        {
            $sheet->setCellValue($alpha.$index, $key);
            $alpha++;
        }
        $index    = 3;

        $i = 1;
        foreach($data as $item){
            $beforeBalance = 0;
            $beforeBalance = $item['money_in_before'] - $item['money_out_before'];
            $alpha = 'A';
            $title = ( trim($item['title']) == '' ) ? 'UNKNOWN' : trim($item['title']);
            $sheet->setCellValue($alpha++.$index, $i++);
            $sheet->setCellValue($alpha++.$index, $title);
            $sheet->setCellValue($alpha++.$index, trim($item['store_code']));
            $sheet->getCell($alpha++.$index)->setValueExplicit( $beforeBalance  , PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['money_out'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['money_in'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['money_in'] - $item['money_out'] + $beforeBalance , PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['balance'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $index++;

        }

        $filename = 'Money_Check_By_Retailer_'.date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Cập nhật balance = tay
     */
    public function updateBalanceAllAction(){
        $d_id = $this->getRequest()->getParam('d_id');
        $QStoreAccount = new Application_Model_Storeaccount();
        $d_id = intval($d_id);
        if($d_id){
            try{
                $QStoreAccount->updateBalance($d_id);
                echo 'Done';
            }catch (Exception $e){
                echo $e->getMessage();
                exit;
            }
        }
        exit;
    }   

    public function selectSnAction(){
        /**
         *      kiểm tra lệch số tiền bvg
         */
        $sn = $this->getRequest()->getParam('sn');
        $QMarketProduct = new Application_Model_MarketProduct();
        $QMarket        = new Application_Model_Market();
        //Tiền đi đơn nếu có bảo vệ giá thì đã trừ tiền
        $sn  = trim($sn);
        $sn_total = 0;
        $intRebate = intval($QMarketProduct->getPrice($sn));// số tiền được giảm
        $sn_total = $QMarket->getPrice($sn) - $intRebate;// số tiền còn lại

        echo "Tiền Bảo vệ giá: ".$intRebate;
        echo '<br/>';
        echo 'Tiền lên đơn: '.$QMarket->getPrice($sn);
        echo '<br/>';
        echo "Tiền sau khi trừ bvg: ".$sn_total;
        exit;
    }

    private function _exportAuditing($data,$from,$to,$name =''){
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'No.',
            'Store Name',
            'Store Code',
            'First Period Blance',
            'Total Money Sellout in Period',
            'Total Money Receive in Period',
            'Ending Blance',
            'Blance Account',
        );

        $PHPExcel->setActiveSheetIndex(0);
        $sheet    = $PHPExcel->getActiveSheet();
        $index    = 1;
        $sheet->setCellValue('A'.$index,'ข้อมูลระหว่างวันที่ : '.$from.' ถึง '.$to);
        $alpha    = 'A';
        $index    = 2;
        foreach($heads as $key)
        {
            $sheet->setCellValue($alpha.$index, $key);
            $alpha++;
        }
        $index    = 3;
        /*
        $db = Zend_Registry::get('db');

        $selectKA = $db->select()
            ->from(array('p'=>'checkmoney_ka'),array('d.id','d.title','d.store_code'))
            ->join(array('d'=>'distributor'),'p.d_id = d.id',array())
            ->group('p.d_id')
        ;
        $result = $db->fetchAll($selectKA);

        $resultKA = array();
        $arr_dis = array();
        foreach($result as $key => $value){
            $resultKA[$value['id']] = $value;
            $arr_dis[] = $value['id'];
        }
        */

        $i = 1;
        foreach($data as $item){

            /*
            if(in_array($item['d_id'],$arr_dis) OR in_array($item['parent'],$arr_dis)){
                if(in_array($item['d_id'],$arr_dis)){
                    $key = $item['d_id'];
                }else{
                    $key = $item['parent'];
                }

                if(isset($resultKA[$key]['money_in_before'])){
                    $resultKA[$key]['money_in_before']  += $item['money_in_before'];
                    $resultKA[$key]['money_out_before'] += $item['money_out_before'];
                    $resultKA[$key]['money_in']         += $item['money_in'];
                    $resultKA[$key]['money_out']        += $item['money_out'];
                    $resultKA[$key]['balance']          += $item['balance'];
                }else{
                    $resultKA[$key]['money_in_before']  = $item['money_in_before'];
                    $resultKA[$key]['money_out_before'] = $item['money_out_before'];
                    $resultKA[$key]['money_in']         = $item['money_in'];
                    $resultKA[$key]['money_out']        = $item['money_out'];
                    $resultKA[$key]['balance']          = $item['balance'];
                }
                continue;
            }
            */
            $beforeBalance = 0;
            $beforeBalance = $item['money_in_before'] - $item['money_out_before'];
            $endBalance = $item['money_in'] - $item['money_out'] + $beforeBalance;
            if( abs($endBalance) <= 10000 ){
                $endBalance = 0;
            }

            $balance = ( abs($item['balance']) <= 10000 ) ? 0 : $item['balance'];
            $alpha = 'A';
            $title = ( trim($item['title']) == '' ) ? 'UNKNOWN' : trim($item['title']);
            $sheet->setCellValue($alpha++.$index, $i++);
            $sheet->setCellValue($alpha++.$index, $title);
            $sheet->setCellValue($alpha++.$index, trim($item['store_code']));
            $sheet->getCell($alpha++.$index)->setValueExplicit( $beforeBalance  , PHPExcel_Cell_DataType::TYPE_NUMERIC);//số dư đầu kỳ
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['money_out'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);//tiền đi dươn trong kỳ
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['money_in'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);//tổng tiền vào của khách gửi + phạt
            $sheet->getCell($alpha++.$index)->setValueExplicit( $endBalance, PHPExcel_Cell_DataType::TYPE_NUMERIC);//số dư cuối kỳ
            $sheet->getCell($alpha++.$index)->setValueExplicit( $balance, PHPExcel_Cell_DataType::TYPE_NUMERIC);// số dư hiện tại
            $index++;
        }

        /*
        //Tính theo Retailer mẹ
        if(count($resultKA) > 0){
            foreach($resultKA as $item){
                if(!isset($item['money_in_before'])){
                    continue;
                }
                $beforeBalance = 0;
                $beforeBalance = $item['money_in_before'] - $item['money_out_before'];
                $alpha = 'A';
                $title = ( trim($item['title']) == '' ) ? 'UNKNOWN' : trim($item['title']);
                $sheet->setCellValue($alpha++.$index, $i++);
                $sheet->setCellValue($alpha++.$index, $title);
                $sheet->setCellValue($alpha++.$index, trim($item['store_code']));
                $sheet->getCell($alpha++.$index)->setValueExplicit( $beforeBalance  , PHPExcel_Cell_DataType::TYPE_NUMERIC);//số dư đầu kỳ
                $sheet->getCell($alpha++.$index)->setValueExplicit( $item['money_out'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);//tiền đi dươn trong kỳ
                $sheet->getCell($alpha++.$index)->setValueExplicit( $item['money_in'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);//tổng tiền vào của khách gửi + phạt
                $sheet->getCell($alpha++.$index)->setValueExplicit( $item['money_in'] - $item['money_out'] + $beforeBalance , PHPExcel_Cell_DataType::TYPE_NUMERIC);//số dư cuối kỳ
                $sheet->getCell($alpha++.$index)->setValueExplicit( $item['balance'], PHPExcel_Cell_DataType::TYPE_NUMERIC);// số dư hiện tại
                $index++;
            }
        }
        */
        $filename = 'Money_check_auditing_'.$name.'_'.date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        $objWriter->save('php://output');
        exit;
    }

    
    private  function  _exportByDay($data,$from,$to){
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'STT',
            'TÊN ĐẠI LÝ',
            'MÃ CỬA HÀNG',
            'SỐ DƯ ĐẦU KỲ',
            'TỔNG TIỀN ĐI ĐƠN TRONG KỲ',
            'TỔNG TIỀN KHÁCH GỬI TRONG KỲ',
            'Money by day',
            'bank',
            'SỐ DƯ CUỐI KỲ',
            'SỐ DƯ HIỆN TẠI',
        );

        $PHPExcel->setActiveSheetIndex(0);
        $sheet    = $PHPExcel->getActiveSheet();

        $index    = 1;
        $sheet->setCellValue('A'.$index,'Thống kê vào ngày: '.$from.' đến '.$to);
        $alpha    = 'A';
        $index    = 2;
        foreach($heads as $key)
        {
            $sheet->setCellValue($alpha.$index, $key);
            $alpha++;
        }
        $index    = 3;

        $i = 1;
        foreach($data as $item){
            $beforeBalance = 0;
            $beforeBalance = $item['money_in_before'] - $item['money_out_before'];
            $alpha = 'A';
            $title = ( trim($item['title']) == '' ) ? 'UNKNOWN' : trim($item['title']);
            $sheet->setCellValue($alpha++.$index, $i++);
            $sheet->setCellValue($alpha++.$index, $title);
            $sheet->setCellValue($alpha++.$index, trim($item['store_code']));
            $sheet->getCell($alpha++.$index)->setValueExplicit( $beforeBalance  , PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['money_out'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['money_in'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['day_money'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['day_bank'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['money_in'] - $item['money_out'] + $beforeBalance , PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['balance'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $index++;

        }

        $filename = 'Money_Check_By_Retailer_'.date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        $objWriter->save('php://output');
        exit;
    }

public function paymentSlipAction(){
        $db = Zend_Registry::get('db');
        $flashMessenger     = $this->_helper->flashMessenger;
        $messages           = $flashMessenger->setNamespace('error')->getMessages();
        $messages_success   = $flashMessenger->setNamespace('success')->getMessages();
        
        $limit      = LIMITATION;
        $d_id       = $this->getRequest()->getParam('d_id');
        $status     = $this->getRequest()->getParam('status');
        $page       = $this->getRequest()->getParam('page',1);
        $sort       = $this->getRequest()->getParam('sort','id');
        $desc       = $this->getRequest()->getParam('desc', 1);
        $total      = 0;
        $sn         = $this->getRequest()->getParam('sn');
        if ($this->getRequest()->getMethod() == 'POST') {
             echo "<pre>";
                // print_r($_POST);
             //    die;  print_r($_POST);

               
                // print_r($_FILES); die;
                //$file_name_show = $_FILES['file']['name'];
                //print_r($file_name_show);
                //die;
                $userStorage      = Zend_Auth::getInstance()->getStorage()->read();
                $db->beginTransaction();
                try {  

                    $payment                        = $this->getRequest()->getParam('payment');//-----
                    $pay_ment                        = $this->getRequest()->getParam('pay_ment');//-----
                    $lack_of_money                  = $this->getRequest()->getParam('lack_of_money');//-----
                    $pay_money                      = $this->decimal_remove_comma($this->getRequest()->getParam('pay_money',0));
                    $payment_order                  = $this->getRequest()->getParam('payment_order', 0);
                    $bank_transaction_code          = $this->getRequest()->getParam('bank_transaction_code', 0);
                    $payment_service                = $this->getRequest()->getParam('payment_service', 0);
                    $payment_servicecharge          = $this->getRequest()->getParam('payment_servicecharge', 0);
                    $pay_time                       = $this->getRequest()->getParam('pay_time');//-----
                    $bank                           = $this->getRequest()->getParam('select_bank_id', NULL);//-----
                    $type                           = 2;
                    $price_use_discount_creditnote  = $this->getRequest()->getParam('price_use_discount_creditnote');
                    $price_balance_discount_creditnote  = $this->getRequest()->getParam('price_balance_discount_creditnote');
                    $note_new                       = $this->getRequest()->getParam('remark', NULL);
                    $ids_discount_creditnote        = $this->getRequest()->getParam('ids_discount_creditnote', NULL);
                    // if($total_amount==0)
                    // {
                    //     $payment_order=0;
                    //     $payment_bank_transfer=0;
                    //     $payment_servicecharge=0;
                    //     $payment_service=0;
                    // }                   

                    // $where = array();
                    // $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                    // $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

                    
                    $date = date('Y-m-d H:i:s');

                    $data['confirm_so'] = 1;
                    $data['sales_confirm_date'] = $date;
                    $data['sales_confirm_id'] = $userStorage->id;

                    // $QMarket->update($data, $where);

                    /* ---------Add Money Check--------------- */
                
                        $payment_order_val=0;$payment_bank_transfer_val=0;$i=0;
                        $payment_service_val=0;$payment_servicecharge_val=0;

                        for($i=0;$i<count($pay_ment);$i++){
                            
                            // echo "<br/> i = ".$i;
                            $payment_order_val          =$payment_order[$i];
                            $payment_bank_transfer_val  =$payment_bank_transfer[$i];
                            $payment_service_val        =$payment_service[$i];
                            $payment_servicecharge_val  =$payment_servicecharge[$i];
                            $lack_of_money_val          =$lack_of_money[$i];
                            $note_new                   =$note_new[$i];
                            $date                       = date('Y-m-d H:i:s');

                            //pay_banktransfer

                            $QCheckMoney    = new Application_Model_Checkmoney();
                            $QStoreaccount  = new Application_Model_Storeaccount();
                            $QCreditNoteTran= new Application_Model_CreditNoteTran();
                            //$userStorage    = Zend_Auth::getInstance()->getStorage()->read();

                            //$pay_money = $payment_order+$payment_bank_transfer;
                            $file_name_upload = '/pay_slips/'.$sn[$i].'/'.$_FILES['file']['name'];
                            

                            // $note_new='PayMoney='.number_format($payment_order_val,2) .' Fee transfer='.number_format($payment_bank_transfer_val,2).' Service Charge='.number_format($payment_servicecharge_val,2).' ค่าอะไหล่และค่าบริการ='.number_format($payment_service_val,2);

                            $data = array(
                                    'd_id'                  => $d_id,
                                    'bank'                  => $bank,
                                    'pay_money'             => $payment_order_val,
                                    'type'                  => 1,
                                    'pay_time'              => $pay_time,
                                    'note'                  => $note_new,
                                    'company_id'            => 1,
                                    'sn'                    => $sn[$i],
                                    'file_pay_slip'         => $file_name_upload,
                                    'user_id'               => $userStorage->id,
                                    'create_by'             => $userStorage->id,
                                    'create_at'             => $date,
                                    'sales_confirm_id'      => $userStorage->id,
                                    'sales_confirm_date'    => $date,
                                    'addition'              => 1,
                                    'finance_confirm_date'  => $date,
                                    'finance_confirm_id'    => $userStorage->id,
                                    'total_money_transfer'  => $pay_money,
                                    'bank_transaction_code' => $bank_transaction_code,
                                    'lack_of_money'         => $lack_of_money_val,
                                    'pay_service'           => $payment_service_val,
                                    'pay_servicecharge'     => $payment_servicecharge_val
                                    
                            );
                        
                            $file_name_upload = $_FILES['file']['name'];
                            // die;
                            if($ch_id){
                                $old_checkmoney = $QCheckMoney->find($ch_id)->current();
                                $data['user_id'] = $userStorage->id;
                                $data['updated_at'] = $date;
                                $where = $db->quoteInto('id = ?',$ch_id);
                                $QCheckMoney->update($data,$where); 
                                
                            }else{

                               $QCheckMoney->insert($data);
                              
                            }
                           
                           
                         }

                        if ($price_use_discount_creditnote and $ids_discount_creditnote) {
                             $array_1 = array('price_use_discount_creditnote' => $price_use_discount_creditnote);
                             $array_2 = array('ids_discount_creditnote' => $ids_discount_creditnote);
                            
                           
                            $temp = $array_1 + $array_2;
                          
                             foreach($temp as $key => $item) { 
                                foreach($item as $k => $v) {
                                    $arr[] = $k; 
                                }
                            }
                            $arr = array_unique($arr);
                            
                            foreach($temp as $key => $item) { $arr2[] = $key;  }

                            for ($i=0;$i<count($arr);$i++) {
                                for ($j=0;$j<count($arr2);$j++) {
                                    $data2[ $arr[$i] ][$arr2[$j]] = $temp[ $arr2[$j] ][ $arr[$i] ];
                                }
                            }
                          
                            foreach($data2 as $key => $item) { 
                                foreach ($item as $key2 => $value2) {}
                                for ($j=0; $j < count($value2); $j++) { 
                                   
                                    $cn_data = array(   
                                        'distributor_id'    => $d_id,
                                        'sales_order'       => $key,  
                                        'use_discount'      => $item['price_use_discount_creditnote'][$j],
                                        'creditnote_sn'     => $item['ids_discount_creditnote'][$j],
                                        'update_by'         => $userStorage->id,
                                        'update_date'       => date('Y-m-d H:i:s'),
                                        'creditnote_type'   => substr($item['ids_discount_creditnote'][$j], 0, 2)
                                    );
                                  
                                    $QCreditNoteTran->insert($cn_data);
                                }
                                    
                            }
                        }    
                         $QStoreaccount->updateBalance( $data['d_id'] );
                         //print_r($data_money);die;
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
                                        . DIRECTORY_SEPARATOR . $sn[0];
                                        
                                    $file_pay_slip = DIRECTORY_SEPARATOR . 'pay_slips'
                                        . DIRECTORY_SEPARATOR . $sn[0] . DIRECTORY_SEPARATOR;    

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
                    // if ($payment) {
                    //     $whereCheckMoney       = array();
                    //     $whereCheckMoney[]     = $QCheckmoney->getAdapter()->quoteInto('sn = ?',$sales[0]['sn']);
                    //     $checkUpdateCheckMoney = $QCheckmoney->fetchRow($whereCheckMoney);
                    //     if (!$checkUpdateCheckMoney) {
                    //         $db->rollback();
                    //         $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
                    //         $this->_redirect('/sales/sales-confirm-order?sn=' . $sn);
                    //     }
                    // }

                    $db->commit();
                    
                    $flashMessenger->setNamespace('success')->addMessage('Done!');
                    $this->_redirect('/checkmoney/list');
                }
                catch (exception $e) {
                    $db->rollback();
                    $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!' .$e->getMessage());die($e);
                    $this->_redirect('/checkmoney/payment-slip?d_id=' . $d_id);
                }
                $flashMessenger->setNamespace('success')->addMessage('Done!');
                $this->_redirect('/checkmoney/list');

            } 

        $QCheckmoney = new Application_Model_Checkmoney();
        $list = $QCheckmoney->getSoPaymentslip($d_id);
       
        $QDistributor = new Application_Model_Distributor();
        $this->view->distributors_cached = $QDistributor->get_cache();

        $QStaff = new Application_Model_Staff();
        $this->view->staffs_cached = $QStaff->get_cache();

        $QBank = new Application_Model_Bank();
        $this->view->banks = $QBank->fetchAll();

        $this->view->list               = $list;
        $this->view->limit              = $limit;
        $this->view->total              = $total;
        $this->view->page               = $page;
        $this->view->offset             = $limit*($page-1);
        // $this->view->params             = $params;
        $this->view->sort               = $sort;
        $this->view->desc               = $desc;
        $this->view->url                = HOST.'checkmoney/payment-order/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->messages_success   = $messages_success;
        $this->view->messages           = $messages;
    }
}          

