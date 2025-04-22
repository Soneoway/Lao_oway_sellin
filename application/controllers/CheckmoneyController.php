<?php
class CheckmoneyController extends My_Controller_Action{

    public function init(){

    }

    public function indexAction(){
        
    }

    function decimal_remove_comma($priceFloat)
    {
        $price = str_replace(",","",$priceFloat);
        return $price;
    }

    public function addAction(){

        $flashMessenger     = $this->_helper->flashMessenger;
        $messages           = array();
        $messages_success   = array();
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

        $messages           += $flashMessenger->setNamespace('error')->getMessages();
        $messages_success   += $flashMessenger->setNamespace('success')->getMessages();

        $this->view->messages_success   = $messages_success;
        $this->view->messages           = $messages;
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

        $db         = Zend_Registry::get('db');
        $select     = $db->select()
            ->from('distributor',array('id','title'))
            ->where('title like ?',"%".$title."%")
            ->where('del = ?',0)
            ->limit(30,0)
            ;
        $result     = $db->fetchAll($select);
        $this->_helper->json->sendJson($result);
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

    }

    public function getorderAction(){

        $distributor_id = $this->getRequest()->getParam('id');
        $db     = Zend_Registry::get('db');
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

        $messages           = array();
        $messages_success   = array();

        $db = Zend_Registry::get('db');
        $id = $this->getRequest()->getParam('id');
        $is_credit = $this->getRequest()->getParam('is_credit');
        $this->view->is_credit = $is_credit;
        
        $select = $db->select()
            ->from(array('ch'=>'checkmoney'),   array('ch.*','ch_id'=>'ch.id'))
            ->joinleft(array('s'=>'store_account'), 'ch.d_id = s.d_id',array('remain'=>'s.balance'))//trường hợp không nhận d_id
            ->joinleft(array('d'=>'distributor'),   'd.id = ch.d_id',array('d.unames','d.title'))//trường hợp không nhận d_id
            ->joinleft(array('b'=>'bank'),'ch.bank=b.id',array("bank_name"=>'b.name'))// trường hợp transaction trừ tiền
            ->where($db->quoteInto('ch.id = ?',$id));
           // echo $select;
        $currentTransaction = $db->fetchRow($select);
        if($currentTransaction){

            $this->view->transaction = $currentTransaction; //checkmoney
            $sn                      = $currentTransaction['sn'];
            if(trim($sn) != "" ){
                //get sn from market
                $arr_sn         = explode(',', $sn);
                $QMarket        = new Application_Model_Market();
                $select_market  = $db->select()
                    ->from(array('m'=>'market'),array('m.sn','m.sn_ref','total_all'=>'SUM(m.total)'))
                    ->where('m.sn IN (?)',$arr_sn)
                    ->group('m.sn');
                $markets                    = $db->fetchAll($select_market);
                $this->view->markets        = $markets;
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

        $this->view->messages_success   = $messages_success;
        $this->view->messages           = $messages;
    }

    public function historyAction(){

        $db         = Zend_Registry::get('db');
        $page       = $this->getRequest()->getParam('page',1);
        $d_id       = $this->getRequest()->getParam('d_id',null);//distributor ID
        $limit      = 20;
        $sort       = $this->getRequest()->getParam('sort','pay_time');
        $desc       = $this->getRequest()->getParam('desc', 1);
        $total      = 0;
        $type       = $this->getRequest()->getParam('type','');

        $params = array(
            'd_id'  =>  $d_id,
            'type'  =>  $type,
            'sort'  =>  $sort,
            'desc'  =>  $desc,
        );

        $QCheckMoney    = new Application_Model_Checkmoney();
        $transactions   = $QCheckMoney->fetchPaginationHistory($page, $limit, $total, $params);

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
        $this->view->dealer         = $dealer;
        $this->view->storeaccount   = $curStoreAccount;
        $this->view->transactions   = $transactions;
        $this->view->limit          = $limit;
        $this->view->total          = $total;
        $this->view->page           = $page;
        $this->view->offset         = $limit*($page-1);
        $this->view->params         = $params;
        $this->view->sort           = $sort;
        $this->view->desc           = $desc;
        $this->view->url            = HOST.'checkmoney/history/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->money_types = unserialize(MONEY_TYPE);
    }

    public function historyCreditAction(){

        $db         = Zend_Registry::get('db');
        $page       = $this->getRequest()->getParam('page',1);
        $d_id       = $this->getRequest()->getParam('d_id',null);//distributor ID
        $limit      = 20;
        $sort       = $this->getRequest()->getParam('sort','pay_time');
        $desc       = $this->getRequest()->getParam('desc', 1);
        $total      = 0;
        $type       = $this->getRequest()->getParam('type','');

        $params = array(
            'd_id'      =>  $d_id,
            'type'      =>  $type,
            'sort'      =>  $sort,
            'desc'      =>  $desc,
            'is_credit' =>  'CREDIT'
        );

        $QCheckMoney    = new Application_Model_Checkmoney();
        $transactions   = $QCheckMoney->fetchPaginationHistory($page, $limit, $total, $params);

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
        $this->view->dealer         = $dealer;
        $this->view->storeaccount   = $curStoreAccount;
        $this->view->transactions   = $transactions;
        $this->view->limit          = $limit;
        $this->view->total          = $total;
        $this->view->page           = $page;
        $this->view->offset         = $limit*($page-1);
        $this->view->params         = $params;
        $this->view->sort           = $sort;
        $this->view->desc           = $desc;
        $this->view->url            = HOST.'checkmoney/history-credit/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->money_types = unserialize(MONEY_TYPE);
    }

    public function historyMulticashAction(){

        $db         = Zend_Registry::get('db');
        $page       = $this->getRequest()->getParam('page',1);
        $d_id       = $this->getRequest()->getParam('d_id',null);//distributor ID
        $limit      = 20;
        $sort       = $this->getRequest()->getParam('sort','pay_time');
        $desc       = $this->getRequest()->getParam('desc', 1);
        $total      = 0;
        $type       = $this->getRequest()->getParam('type','');

        $params = array(
            'd_id'      =>  $d_id,
            'type'      =>  $type,
            'sort'      =>  $sort,
            'desc'      =>  $desc,
            'is_credit' =>  'MULTICASH'
        );

        $QCheckMoney    = new Application_Model_Checkmoney();
        $transactions   = $QCheckMoney->fetchPaginationHistory($page, $limit, $total, $params);

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
        $this->view->dealer         = $dealer;
        $this->view->storeaccount   = $curStoreAccount;
        $this->view->transactions   = $transactions;
        $this->view->limit          = $limit;
        $this->view->total          = $total;
        $this->view->page           = $page;
        $this->view->offset         = $limit*($page-1);
        $this->view->params         = $params;
        $this->view->sort           = $sort;
        $this->view->desc           = $desc;
        $this->view->url            = HOST.'checkmoney/history-multicash/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->money_types = unserialize(MONEY_TYPE);
    }

    public function delAction(){
        $is_credit = $this->getRequest()->getParam('is_credit');
        $ch_id          = $this->getRequest()->getParam('id',null);
        $QCheckMoney    = new Application_Model_Checkmoney();
        $checkmoney     = $QCheckMoney->find($ch_id)->current();
        $db             = Zend_Registry::get('db');
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
        if($is_credit == 'CREDIT'){
            $this->_redirect('/checkmoney/credit-list');
        }else{
            $this->_redirect('/checkmoney/list');
        }
    }
    
    public function getbalanceAction(){
        $type           = $this->getRequest()->getParam('type',null);
        $d_id           = $this->getRequest()->getParam('id',null);
        
        $QDistributor   = new Application_Model_Distributor();
        $where_d        = $QDistributor->getAdapter()->quoteInto('id = ?', $d_id);
        $distributor    = $QDistributor->fetchRow($where_d);
        
        
        $QStoreAccount  = new Application_Model_Storeaccount();
       // $where            = $QStoreAccount->getAdapter()->quoteInto('d_id = ?', $d_id);
       // $row            = $QStoreAccount->fetchRow($where);

        $total_balance  = $QStoreAccount->getBalanceByGroup($d_id);
        $black_list = $distributor->black_list_distributor;
        $main_distributor_id = $distributor->main_distributor_id;

        if(!$type || !$d_id){
            $data = array('alert'=>'Order Type Or Distributor not select.');
            $status = 9;
            $result   = $data;

            $this->_helper->json->sendJson(array('status'=>$status,'result'=>$data));
            $this->_helper->layout()->disableLayout(true);
            $this->_helper->viewRenderer->setNoRender(true);
            die;
        }

        $QDBL = new Application_Model_DistributorBlackList();
        $check_back_list_distributor = $QDBL->check_black_list($type,$d_id);

        if($check_back_list_distributor){
            $data = array('alert'=>'Black List');
            $status = 9;
            $result   = $data;
        }
        // if($black_list == 1){ 
        //    $data = array('alert'=>'Black List');
        //     $status = 9;
        //     $result   = $data;
        // }
        else{
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
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('ship' => 'shipping_address'),
                array( 'ship.*'));
        $select->joinLeft(array('sp'=>'shipping_provinces'),'sp.provice_id=ship.province_id',array('provice_name'));  
        $select->joinLeft(array('sa'=>'shipping_amphures'),'sa.amphure_id=ship.`amphures_id`',array('amphure_name'));  
        $select->joinLeft(array('sd'=>'shipping_districts'),'sd.district_code=ship.districts_id',array('district_name'));  
        $select->joinLeft(array('sz'=>'shipping_zipcodes'),'sz.zip_id=ship.zipcodes',array('zipcode')); 
        $select->where('ship.d_id = ?',$d_id);
        $select->where('ship.status is null',1);
       
        $ship = $db->fetchAll($select);
       
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
                'main_distributor_id'   =>  $distributor->main_distributor_id,
                'rank'   =>  $distributor->rank,
                'credit_status'   =>  number_format($distributor->credit_status,0),
                'spc_discount'   =>  number_format($distributor->spc_discount,0),
                'spc_discount_phone'   =>  number_format($distributor->spc_discount_phone,0),
                'spc_discount_acc'   =>  number_format($distributor->spc_discount_acc,0),
                'spc_discount_digital'   =>  number_format($distributor->spc_discount_digital,0),
            );

           
            if ($ship){
                foreach ($ship as $item){
                   $data['shipping'][] = array(
                    'id' => $item['id'],
                    'information' => $item['contact_name'].' '.$item['address'].' '.$item['district_name'].' '.$item['amphure_name'].' '.$item['provice_name'].' '.$item['zipcode'].' '.$item['phone'].' '.$item['remark'],
                );                        
                    
                }
            }
        

        } else {
            $data = array(
                'balance'       =>  number_format(0,2),
                'total_balance' =>  number_format(0,2),
                'retailer_name' =>  $distributor->title,
                'add'           =>  $distributor->add_tax,
                'credit_amount' =>  number_format($total_credit_all,2),
                'main_distributor_id'   =>  $distributor->main_distributor_id,
                'rank'   =>  $distributor->rank,
                'credit_status'   =>  number_format($distributor->credit_status,0),
                'spc_discount'   =>  number_format($distributor->spc_discount,0),
                'spc_discount_phone'   =>  number_format($distributor->spc_discount_phone,0),
                'spc_discount_acc'   =>  number_format($distributor->spc_discount_acc,0),
                'spc_discount_digital'   =>  number_format($distributor->spc_discount_digital,0),
            );

            if ($ship){
                foreach ($ship as $item){
                   $data['shipping'][] = array(
                    'id' => $item['id'],
                    'information' => $item['contact_name'].' '.$item['address'].' '.$item['district_name'].' '.$item['amphure_name'].' '.$item['provice_name'].' '.$item['zipcode'].' '.$item['phone'].' '.$item['remark'],
                );                        
                    
                }
            }
        }
        }

        $QD  = new Application_Model_Distributor();

        $getDistributorGroup = $QD->getDistributorGroup($d_id);

        $data['group_type_id'] = $getDistributorGroup['group_type_id'];
        $data['warehouse_id'] = $getDistributorGroup['warehouse_id'];

        // print_r($data);
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
        $from_collection_time             = $this->getRequest()->getParam('from_collection_time',null);
        $to_collection_time               = $this->getRequest()->getParam('to_collection_time',null);
       // $from_time   = $this->getRequest()->getParam('from_time', date('d/m/Y', strtotime('-0 day')));

       // $to_time               = $this->getRequest()->getParam('to_time');
        $from_invoice_time             = $this->getRequest()->getParam('from_invoice_time',date('Y-m-d',strtotime('-1 days')));
        $to_invoice_time               = $this->getRequest()->getParam('to_invoice_time',date('Y-m-d',strtotime('-1 days')));
        $type_money            = $this->getRequest()->getParam('type_money',null);
        $from_money            = $this->getRequest()->getParam('from_money',null);
        $to_money              = $this->getRequest()->getParam('to_money',null);
        $note                  = $this->getRequest()->getParam('note','');
        $content               = $this->getRequest()->getParam('content','');
        $sn                    = $this->getRequest()->getParam('sn'); 
        $invoice_number        = $this->getRequest()->getParam('invoice_number'); 
        $finance_group         = $this->getRequest()->getParam('finance_group'); 

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

        $rank                  = $this->getRequest()->getParam('rank');
        $cancel              = $this->getRequest()->getParam('cancel');

        $this->view->rank = $rank;
        $this->view->d_id = $d_id;

        $QDistributor = new Application_Model_Distributor();
        $this->view->finance_group = $QDistributor->getFinanceGroup();

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
        
        $params     = array(
            'd_id'                  =>  $d_id,
            'note'                  =>  trim($note),
            'content'               =>  trim($content),
            'bank_serial'           =>  trim($bank_serial),
            'bank_transaction_code' =>  trim($bank_transaction_code),
            'type_time'             =>  $type_time,
            'from_collection_time'  =>  trim($from_collection_time),
            'to_collection_time'    =>  trim($to_collection_time),
            'from_invoice_time'     =>  trim($from_invoice_time),
            'to_invoice_time'       =>  trim($to_invoice_time),
            'type_money'            =>  $type_money,
            'from_money'            =>  trim($from_money),
            'to_money'              =>  trim($to_money),
            'sort'                  =>  $sort,
            'desc'                  =>  $desc,
            'export'                =>  $export,
            'bank'                  =>  $bank,
            'sn'                    =>  trim($sn),
            'invoice_number'        =>  trim($invoice_number),
            'finance_group'         =>  trim($finance_group),
            'export_smartmobile'    =>  $export_smartmobile,
            'export_oppo'           => $export_oppo,
            'rank'                  => $rank,
            'cancel'                => $cancel,
            'action_from'           => 'checkmoney'
        );
       
        if($params['from_money'] === '' ){
            unset($params['from_money']);
        }

        if($params['to_money'] === '' ){
            unset($params['to_money']);
        }

        $QCheckmoney        = new Application_Model_Checkmoney();
            

        if($export=='1'){
            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            $data = $QCheckmoney->fetchPaginationByRetailer(NULL, NULL, $total, $params);
            $this->_getDataForExcel($data,$params);
        }else if($export=='2'){ //Export Cash Collection

            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            //print_r($params);die;
            $result_ch = $QCheckmoney->_getDataForExcelCashCollection($data,$params);  
            $this->_exportExcelExportCashCollectionExcel($result_ch);
        }else if($export=='3'){ //Daily Cash In View
            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            $result_ch = $QCheckmoney->_getDataForExcelCashCollection($data,$params);  
            $this->_exportExcelExportDailyCashInViewExcel($result_ch);
        }else if($export=='4'){ //Daily Cash In For Bank Check
            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            $result_ch = $QCheckmoney->_getDataForExcelCashCollection($data,$params);
            $this->_exportExcelExportDailyCashInForBankCheck($result_ch); 
        }else if($export=='5'){ //Export Cash Collection For Service
            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            $params['shop_type']='service';
            //print_r($params);die;
            $result_ch = $QCheckmoney->_getDataForExcelCashCollection($data,$params);
            $this->_exportExcelExportCashCollectionServiceExcel($result_ch);  
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
        
        $list   = $QCheckmoney->fetchPaginationByRetailer($page, $limit, $total, $params);
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

        $this->view->banks              = $banks;
        $this->view->list               = $list;
        $this->view->limit              = $limit;
        $this->view->total              = $total;
        $this->view->page               = $page;
        $this->view->offset             = $limit*($page-1);
        $this->view->params             = $params;
        $this->view->sort               = $sort;
        $this->view->desc               = $desc;
        $this->view->url                = HOST.'checkmoney/list/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->messages_success   = $messages_success;
        $this->view->messages           = $messages;

    }

    public function creditListAction(){

        $flashMessenger        = $this->_helper->flashMessenger;
        $messages              = $flashMessenger->setNamespace('error')->getMessages();
        $messages_success      = $flashMessenger->setNamespace('success')->getMessages();
        $d_id                  = $this->getRequest()->getParam('d_id','');
        $bank                  = $this->getRequest()->getParam('bank',null);//array
        $bank_serial           = $this->getRequest()->getParam('bank_serial',null);
        $bank_transaction_code = $this->getRequest()->getParam('bank_transaction_code',null);
        $type_time             = $this->getRequest()->getParam('type_time',null);
        $from_collection_time             = $this->getRequest()->getParam('from_collection_time',null);
        $to_collection_time               = $this->getRequest()->getParam('to_collection_time',null);
       // $from_time   = $this->getRequest()->getParam('from_time', date('d/m/Y', strtotime('-0 day')));

       // $to_time               = $this->getRequest()->getParam('to_time');
        $from_invoice_time             = $this->getRequest()->getParam('from_invoice_time',date('Y-m-d',strtotime('-1 days')));
        $to_invoice_time               = $this->getRequest()->getParam('to_invoice_time',date('Y-m-d',strtotime('-1 days')));
        $type_money            = $this->getRequest()->getParam('type_money',null);
        $from_money            = $this->getRequest()->getParam('from_money',null);
        $to_money              = $this->getRequest()->getParam('to_money',null);
        $note                  = $this->getRequest()->getParam('note','');
        $content               = $this->getRequest()->getParam('content','');
        $sn                    = $this->getRequest()->getParam('sn');
        $invoice_number        = $this->getRequest()->getParam('invoice_number'); 
        $finance_group         = $this->getRequest()->getParam('finance_group'); 

        $export                = $this->getRequest()->getParam('export',0);
        $export_retailer       = $this->getRequest()->getParam('export_retailer',0);
        $export_full           = $this->getRequest()->getParam('export_full',0);
        $export_smartmobile    = $this->getRequest()->getParam('smartmobile',0);
        $export_oppo           = $this->getRequest()->getParam('oppo',0);
        $export_by_day         = $this->getRequest()->getParam('export_by_day',0);

        $no_show_brandshop      = $this->getRequest()->getParam('no_show_brandshop', 0);

        $total                 = 0;
        $limit                 = LIMITATION;
        $sort                  = $this->getRequest()->getParam('sort','pay_time');
        $desc                  = $this->getRequest()->getParam('desc', 1);
        $page                  = $this->getRequest()->getParam('page',1);

        $rank           = $this->getRequest()->getParam('rank');
        $this->view->rank = $rank;
        $this->view->d_id = $d_id;

        $QDistributor = new Application_Model_Distributor();
        $this->view->finance_group = $QDistributor->getFinanceGroup();

        $counter            = $this->getRequest()->getParam('counter', 0);
        $this->view->counter = $counter+1;
        
        if($counter < 1){
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            if (My_Staff_Group::inGroup($userStorage->group_id, array(CHECK_MONEY))){
                $no_show_brandshop = 1;
            }
        }

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

        $params     = array(
            'd_id'                  =>  $d_id,
            'note'                  =>  trim($note),
            'content'               =>  trim($content),
            'bank_serial'           =>  trim($bank_serial),
            'bank_transaction_code' =>  trim($bank_transaction_code),
            'type_time'             =>  $type_time,
            'from_collection_time'  =>  trim($from_collection_time),
            'to_collection_time'    =>  trim($to_collection_time),
            'from_invoice_time'     =>  trim($from_invoice_time),
            'to_invoice_time'       =>  trim($to_invoice_time),
            'type_money'            =>  $type_money,
            'from_money'            =>  trim($from_money),
            'to_money'              =>  trim($to_money),
            'sort'                  =>  $sort,
            'desc'                  =>  $desc,
            'export'                =>  $export,
            'bank'                  =>  $bank,
            'sn'                    =>  trim($sn),
            'invoice_number'        =>  trim($invoice_number),
            'finance_group'         =>  trim($finance_group),
            'export_smartmobile'    =>  $export_smartmobile,
            'export_oppo'           => $export_oppo,
            'rank'                  => $rank,
            'no_show_brandshop'     => $no_show_brandshop,
            'counter'               => $counter
        );
       
        if($params['from_money'] === '' ){
            unset($params['from_money']);
        }

        if($params['to_money'] === '' ){
            unset($params['to_money']);
        }

        $QCheckmoney        = new Application_Model_Checkmoney();

        if($export=='1'){

            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            $data = $QCheckmoney->fetchPaginationByRetailerCredit(NULL, NULL, $total, $params);
            $this->_getDataForExcel($data,$params);

        }else if($export=='2'){

            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            //$data = $QCheckmoney->fetchCashCollection(NULL, NULL, $total, $params);

            $this->_getDataForExcelCashCreditCollection($data,$params);

        }else if($export=='3'){

            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            //$data = $QCheckmoney->fetchCashCollection(NULL, NULL, $total, $params);

            $this->_getDataForExcelCashCreditCollectionOutStanding($data,$params);

        }else if($export=='4'){

            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            //$data = $QCheckmoney->fetchCashCollection(NULL, NULL, $total, $params);

            $this->_getDataForExcelCashCreditCollectionBankDetail($data,$params);

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
        
        $list   = $QCheckmoney->fetchPaginationByRetailerCredit($page, $limit, $total, $params);
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

        $this->view->banks              = $banks;
        $this->view->list               = $list;
        $this->view->limit              = $limit;
        $this->view->total              = $total;
        $this->view->page               = $page;
        $this->view->offset             = $limit*($page-1);
        $this->view->params             = $params;
        $this->view->sort               = $sort;
        $this->view->desc               = $desc;
        $this->view->url                = HOST.'checkmoney/credit-list/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->messages_success   = $messages_success;
        $this->view->messages           = $messages;

    }

    public function multiCashListAction(){

        $flashMessenger        = $this->_helper->flashMessenger;
        $messages              = $flashMessenger->setNamespace('error')->getMessages();
        $messages_success      = $flashMessenger->setNamespace('success')->getMessages();
        $d_id                  = $this->getRequest()->getParam('d_id','');
        $bank                  = $this->getRequest()->getParam('bank',null);//array
        $bank_serial           = $this->getRequest()->getParam('bank_serial',null);
        $bank_transaction_code = $this->getRequest()->getParam('bank_transaction_code',null);
        $type_time             = $this->getRequest()->getParam('type_time',null);
        $from_collection_time             = $this->getRequest()->getParam('from_collection_time',null);
        $to_collection_time               = $this->getRequest()->getParam('to_collection_time',null);
       // $from_time   = $this->getRequest()->getParam('from_time', date('d/m/Y', strtotime('-0 day')));

       // $to_time               = $this->getRequest()->getParam('to_time');
        $from_invoice_time             = $this->getRequest()->getParam('from_invoice_time',date('Y-m-d',strtotime('-1 days')));
        $to_invoice_time               = $this->getRequest()->getParam('to_invoice_time',date('Y-m-d',strtotime('-1 days')));
        $type_money            = $this->getRequest()->getParam('type_money',null);
        $from_money            = $this->getRequest()->getParam('from_money',null);
        $to_money              = $this->getRequest()->getParam('to_money',null);
        $note                  = $this->getRequest()->getParam('note','');
        $content               = $this->getRequest()->getParam('content','');
        $sn                    = $this->getRequest()->getParam('sn');
        $invoice_number        = $this->getRequest()->getParam('invoice_number'); 
        $finance_group         = $this->getRequest()->getParam('finance_group'); 

        $export                = $this->getRequest()->getParam('export',0);
        $export_retailer       = $this->getRequest()->getParam('export_retailer',0);
        $export_full           = $this->getRequest()->getParam('export_full',0);
        $export_smartmobile    = $this->getRequest()->getParam('smartmobile',0);
        $export_oppo           = $this->getRequest()->getParam('oppo',0);
        $export_by_day         = $this->getRequest()->getParam('export_by_day',0);

        $no_show_brandshop      = $this->getRequest()->getParam('no_show_brandshop', 0);

        $total                 = 0;
        $limit                 = LIMITATION;
        $sort                  = $this->getRequest()->getParam('sort','pay_time');
        $desc                  = $this->getRequest()->getParam('desc', 1);
        $page                  = $this->getRequest()->getParam('page',1);

        $rank           = $this->getRequest()->getParam('rank');
        $this->view->rank = $rank;
        $this->view->d_id = $d_id;

        $QDistributor = new Application_Model_Distributor();
        $this->view->finance_group = $QDistributor->getFinanceGroup();

        $counter            = $this->getRequest()->getParam('counter', 0);
        $this->view->counter = $counter+1;
        
        if($counter < 1){
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            if (My_Staff_Group::inGroup($userStorage->group_id, array(CHECK_MONEY))){
                $no_show_brandshop = 1;
            }
        }

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

        $params     = array(
            'd_id'                  =>  $d_id,
            'note'                  =>  trim($note),
            'content'               =>  trim($content),
            'bank_serial'           =>  trim($bank_serial),
            'bank_transaction_code' =>  trim($bank_transaction_code),
            'type_time'             =>  $type_time,
            'from_collection_time'  =>  trim($from_collection_time),
            'to_collection_time'    =>  trim($to_collection_time),
            'from_invoice_time'     =>  trim($from_invoice_time),
            'to_invoice_time'       =>  trim($to_invoice_time),
            'type_money'            =>  $type_money,
            'from_money'            =>  trim($from_money),
            'to_money'              =>  trim($to_money),
            'sort'                  =>  $sort,
            'desc'                  =>  $desc,
            'export'                =>  $export,
            'bank'                  =>  $bank,
            'sn'                    =>  trim($sn),
            'invoice_number'        =>  trim($invoice_number),
            'finance_group'         =>  trim($finance_group),
            'export_smartmobile'    =>  $export_smartmobile,
            'export_oppo'           => $export_oppo,
            'rank'                  => $rank,
            'no_show_brandshop'     => $no_show_brandshop,
            'counter'               => $counter
        );
       
        if($params['from_money'] === '' ){
            unset($params['from_money']);
        }

        if($params['to_money'] === '' ){
            unset($params['to_money']);
        }

        $QCheckmoney        = new Application_Model_Checkmoney();

        if($export=='1'){

            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            $data = $QCheckmoney->fetchPaginationByRetailerCredit(NULL, NULL, $total, $params);
            $this->_getDataForExcel($data,$params);

        }else if($export=='2'){

            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            //$data = $QCheckmoney->fetchCashCollection(NULL, NULL, $total, $params);

            $this->_getDataForExcelMultiCashCollection($data,$params);

        }else if($export=='3'){

            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            //$data = $QCheckmoney->fetchCashCollection(NULL, NULL, $total, $params);

            $this->_getDataForExcelMultiCashCollectionOutStanding($data,$params);

        }else if($export=='4'){

            set_time_limit( 0 );
            error_reporting( 0 );
            ini_set('display_error', 0);
            ini_set('memory_limit', -1);
            //$data = $QCheckmoney->fetchCashCollection(NULL, NULL, $total, $params);

            $this->_getDataForExcelMultiCashCollectionBankDetail($data,$params);

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
        
        $list   = $QCheckmoney->fetchPaginationByRetailer($page, $limit, $total, $params);
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

        $this->view->banks              = $banks;
        $this->view->list               = $list;
        $this->view->limit              = $limit;
        $this->view->total              = $total;
        $this->view->page               = $page;
        $this->view->offset             = $limit*($page-1);
        $this->view->params             = $params;
        $this->view->sort               = $sort;
        $this->view->desc               = $desc;
        $this->view->url                = HOST.'checkmoney/multi-cash-list/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->messages_success   = $messages_success;
        $this->view->messages           = $messages;

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
            'desc'        => $desc,
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

    public function getcheckmoneycreditAction(){

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
            'desc'        => $desc,
            );
        $QCheckmoney = new Application_Model_Checkmoney();
        $list   = $QCheckmoney->fetchPaginationCredit($page, $limit, $total, $params);

        // $QCNCT = new Application_Model_CreditNoteCreditTr();

        // $getCreditTrId = [];

        // foreach ($list as $key) {
        //     array_push($getCreditTrId, $key['credit_tr_id']);
        // }

        // $getUniqueCreditTrId = array_values(array_unique($getCreditTrId));

        // $getCNCP = $QCNCT->getCheckMoneyCreditCNCP($d_id, $getUniqueCreditTrId);

        // $this->view->resultCNCP = $getCNCP;
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

    public function getcheckmoneymulticashAction(){

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
            'desc'        => $desc,
            );
        $QCheckmoney = new Application_Model_Checkmoney();
        $list   = $QCheckmoney->fetchPagination($page, $limit, $total, $params);

        // $QCNCT = new Application_Model_CreditNoteCreditTr();

        // $getCreditTrId = [];

        // foreach ($list as $key) {
        //     array_push($getCreditTrId, $key['credit_tr_id']);
        // }

        // $getUniqueCreditTrId = array_values(array_unique($getCreditTrId));

        // $getCNCP = $QCNCT->getCheckMoneyCreditCNCP($d_id, $getUniqueCreditTrId);

        // $this->view->resultCNCP = $getCNCP;
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
        $params     = array(
            'status'    =>  $status,
            'd_id'      =>  $d_id,
            'sort'      =>  $sort,
            'desc'      =>  $desc,
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

        $this->view->list               = $list;
        $this->view->limit              = $limit;
        $this->view->total              = $total;
        $this->view->page               = $page;
        $this->view->offset             = $limit*($page-1);
        $this->view->params             = $params;
        $this->view->sort               = $sort;
        $this->view->desc               = $desc;
        $this->view->url                = HOST.'checkmoney/payment-order/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->messages_success   = $messages_success;
        $this->view->messages           = $messages;
    }

    public function confirmPaymentOrderAction(){
        $flashMessenger     = $this->_helper->flashMessenger;
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
                'd_id'      => $CheckmoneyPaymentorder['d_id'],
                'bank'      => $select_bank_id,
                'pay_time'  => $receive_time,
                'pay_money' => $CheckmoneyPaymentorder['payment_order'],
                'type'      => 1,
                'user_id'   => $userStorage->id,
                'create_by' => $userStorage->id,
                'create_at' => $date,
                'note'      => 'Payment Order Auto-generate',
                'addition'  => 1,
                'company_id' => $company_id
            );

            // insert vao checkmoney
            $QCheckmoney        = new Application_Model_Checkmoney();
            $QCheckmoney->insert($data_ch);

            // update
            $where = $QCheckmoneyPaymentorder->getAdapter()->quoteInto('id = ?', $id);
            $QCheckmoneyPaymentorder->update(array('status' => 1), $where);

            // update balance
            $QStoreaccount  = new Application_Model_Storeaccount();
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
    

    

    public function _exportExcelExportCashCollectionService($data){
        //print_r($data);die;
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'NO',
            'STORE ID',
            'RETAILER',
            'STORE CODE',
            'COMPANY',
            'BANK',
            'IN TIME',
            'PAYMENT NO',
            'TOTAL MONEY',
            'PAYMENT MONEY',
            'SERVICECHARGE'
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

        ////////////////////////////////////////////////

        $float = floatval($string);
        $QDistributor = new Application_Model_Distributor();
        $distributors_cached = $QDistributor->get_cache();
        $distributors_storecode_cached = $QDistributor->storecode_get_cache();

        $i = 2;
        $index    = 2;
        $payment_note='';
        //print_r($data);die;
        foreach($data as $t){
           // if($t['TYPE']==1){
                $float = floatval($string);

                if($t['output'] <= 0)
                {
                    $money = $t['output'] * -1;  
                }
                
                $money_check = $t['output'];
                
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

                $title = isset($distributors_cached[$t['d_id']]) ? $distributors_cached[$t['d_id']] : '';
                $store_code = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

                //$finance_group = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

                if($t['seq']=='2'){
                    $payment_note=$t['pay_text'];
                }

                if($t['canceled']=='1'){
                    $cancel_status='canceled';
                    $canceled_remark=$t['canceled_remark'];
                }else{
                    $cancel_status='';
                    $canceled_remark='';
                }


               if($t['note'] !='Discount 1 %'){

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $index - 1 , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['d_id'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $title , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $store_code , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( ( $t['company_id'] == 1 ) ? 'OPPO':'TM' , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_name'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_time'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_no'] , PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_amount'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_money'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_servicecharge'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    
                }


                if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %')){
                      
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $index - 1 , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['d_id'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $title , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $store_code , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( ($t['company_id'] == 1 ) ? 'OPPO':'TM' , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_name'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_time'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_no'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_amount'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_money'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_servicecharge'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                }


                $index++;
                
                fputcsv($output, $row);
                unset($t);
                unset($row);
            //}
        }
                   

        //////////////////////////////////////////////////
       
        $filename = 'MoneyChecks_'.date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $filename = 'MoneyChecksCashCollection_'.date('d-m-Y H-i-s').'.xlsx';

        header('Content-Disposition: attachment;filename="' . $filename);

        $objWriter->save('php://output');
        exit;
    }

   

    public function _exportExcelExportDailyCashInViewExcel($data){

        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'Inv mth',
            'NO',
            'STORE ID',
            'RETAILER',
            'STORE CODE',
            'BANK',
            'COMPANY',
            'TYPE',
            'PAYMENT TYPE',
            'ORDER NUMBER',            
            'CREDIT NOTE',
            'BANK TRANSACTION CODE',
            'CONTENT',
            'FINANCE GROUP',
            'CANCEL STATUS',
            'CANCEL REMARK',
            '',
            'PAY NOTE',
            'PAY NOTE FROM OUT',
            'BALANCE',
            'INVOICE NUMBER',
            'IN / OUT MONEY',
            'IN / OUT TIME',
            'NOTE',
            'Cash collection period (YMD)',
            'OPERATION CAMPAIGN'
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

////////////////////////////////////////////////

        $float = floatval($string);
        $QDistributor = new Application_Model_Distributor();
        $distributors_cached = $QDistributor->get_cache();
        $distributors_storecode_cached = $QDistributor->storecode_get_cache();

        $i = 2;
        $index    = 2;
        $payment_note='';
        foreach($data as $t){
                $float = floatval($string);

                if($t['output'] <= 0)
                {
                    $money = $t['output'] * -1;  
                }
                
                $money_check = $t['output'];
                
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

                $title = isset($distributors_cached[$t['d_id']]) ? $distributors_cached[$t['d_id']] : '';
                $store_code = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

                //$finance_group = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

                if($t['seq']=='2'){
                    $payment_note=$t['pay_text'];
                }

                if($t['canceled']=='1'){
                    $cancel_status='canceled';
                    $canceled_remark=$t['canceled_remark'];
                }else{
                    $cancel_status='';
                    $canceled_remark='';
                }

                $inv_mth = '';
                if(isset($t['invoice_number']) && $t['invoice_number']){
                    $inv_mth = substr($t['invoice_number'],2,6);
                }

               if($t['note'] !='Discount 1 %' && $type_title == 'IN'){

                $sheet->getCell($alpha++.$index)->setValueExplicit( $inv_mth , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $index - 1 , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['d_id'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $title , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $store_code , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_name'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( ( $t['company_id'] == 1 ) ? 'OPPO':'TM' , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $type_title , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_type'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $sn_ref , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['creditnote_sn'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_transaction_code'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['content'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['finance_group'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $cancel_status, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $canceled_remark, PHPExcel_Cell_DataType::TYPE_STRING);

                $sheet->getCell($alpha++.$index)->setValueExplicit( '', PHPExcel_Cell_DataType::TYPE_STRING);
                
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_text'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $payment_note, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['balance'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['invoice_number'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_money'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_time'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['note'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( date("ymd", strtotime($t['pay_time'])) , PHPExcel_Cell_DataType::TYPE_STRING);

                }


                if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %') && $type_title == 'IN'){

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $inv_mth , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $index - 1 , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                   $sheet->getCell($alpha++.$index)->setValueExplicit( $t['d_id'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                   $sheet->getCell($alpha++.$index)->setValueExplicit( $title , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $store_code , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_name'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( ($t['company_id'] == 1 ) ? 'OPPO':'TM' , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $type_title , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_type'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $sn_ref , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['creditnote_sn'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_transaction_code'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['content'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['finance_group'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $cancel_status, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $canceled_remark, PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( '', PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_text'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $payment_note, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['balance'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['invoice_number'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_money'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_time'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['note'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( date("ymd", strtotime($t['pay_time'])) , PHPExcel_Cell_DataType::TYPE_STRING);

                }
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bs_campaign'] , PHPExcel_Cell_DataType::TYPE_STRING);
                if($type_title == 'IN'){
                    $index++;
                }
            
                fputcsv($output, $row);
                unset($t);
                unset($row);
                
        }
                   

        //////////////////////////////////////////////////
       
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $filename = 'DailyCashInView_'.date('d-m-Y H-i-s').'.xlsx';

        header('Content-Disposition: attachment;filename="' . $filename);

        $objWriter->save('php://output');
        exit;
    }

    public function _exportExcelExportDailyCashInForBankCheck($data){

        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'BANK',
            'Cash collection period (YMD)',
            'INVOICE NUMBER',
            'IN / OUT MONEY',
            'IN / OUT TIME',
            'NOTE',
            'RETAILER',
            'ORDER NUMBER',            
            'PAY NOTE',
            'CANCEL OR NOT',
            'FINANCE GROUP',
            'OPERATION CAMPAIGN'
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

////////////////////////////////////////////////

        $float = floatval($string);
        $QDistributor = new Application_Model_Distributor();
        $distributors_cached = $QDistributor->get_cache();
        $distributors_storecode_cached = $QDistributor->storecode_get_cache();

        $i = 2;
        $index    = 2;
        $payment_note='';
        foreach($data as $t){
                $float = floatval($string);

                if($t['output'] <= 0)
                {
                    $money = $t['output'] * -1;  
                }
                
                $money_check = $t['output'];
                
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

                $title = isset($distributors_cached[$t['d_id']]) ? $distributors_cached[$t['d_id']] : '';
                $store_code = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

                //$finance_group = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

                if($t['seq']=='2'){
                    $payment_note=$t['pay_text'];
                }

                if($t['canceled']=='1'){
                    $cancel_status='canceled';
                    $canceled_remark=$t['canceled_remark'];
                }else{
                    $cancel_status='';
                    $canceled_remark='';
                }

                $inv_mth = '';
                if(isset($t['invoice_number']) && $t['invoice_number']){
                    $inv_mth = substr($t['invoice_number'],2,6);
                }

                $cancel = '';
                if ($t['canceled']== 1){$cancel = 'Canceled';}else{$cancel = 'Not Cancel';}

               if($t['note'] !='Discount 1 %' && $type_title == 'IN'){

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_name'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( date("ymd", strtotime($t['pay_time'])) , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['invoice_number'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_money'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_time'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['note'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $title , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $sn_ref , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_text'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $cancel , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['finance_group'] , PHPExcel_Cell_DataType::TYPE_STRING);

                }


                if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %') && $type_title == 'IN'){

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_name'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( date("ymd", strtotime($t['pay_time'])) , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['invoice_number'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_money'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_time'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['note'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $title , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $sn_ref , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_text'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $cancel , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['finance_group'] , PHPExcel_Cell_DataType::TYPE_STRING);

                }
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bs_campaign'] , PHPExcel_Cell_DataType::TYPE_STRING);
                if($type_title == 'IN'){
                    $index++;
                }
            
                fputcsv($output, $row);
                unset($t);
                unset($row);
                
        }
                   

        //////////////////////////////////////////////////
       
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $filename = 'DailyCashInForBankCheck_'.date('d-m-Y H-i-s').'.xlsx';

        header('Content-Disposition: attachment;filename="' . $filename);

        $objWriter->save('php://output');
        exit;
    }

    public function _getDataForExcelCashCreditCollection($data,$params)
    {

        if(isset($params['to_collection_time']) AND $params['to_collection_time']){
            $params['to_collection_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }

        if(isset($params['to_invoice_time']) AND $params['to_invoice_time']){
            $params['to_invoice_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }

        $db = Zend_Registry::get('db');
       // print_r($data);die;
        $arr_d_id=0;

        $where_paytime = '';
        $where_invoicetime = '';
        $where_d_id = '';
        $where_sn = '';
        $where_no_show_brandshop = '';
        $where_bank = '';
        $where_finance_group = '';

        $where_invoicetime_newsn = '';

        if($arr_d_id == 0){

            if(isset($params['from_collection_time']) AND $params['from_collection_time'])
            {
                $pay_time_st = $params['from_collection_time'];
                $where_paytime .= " AND (ck.pay_time >= '".$pay_time_st."') ";
            }

            if(isset($params['to_collection_time']) AND $params['to_collection_time'])
            {
                $pay_time_en = $params['to_collection_time'];
                $where_paytime .= " AND (ck.pay_time <= '".$pay_time_en."') ";
            }

            if(isset($params['from_invoice_time']) AND $params['from_invoice_time'])
            {
                $invoice_time_st = $params['from_invoice_time'];
                $where_invoicetime .= " AND (m.invoice_time >= '".$invoice_time_st."') ";

                $where_invoicetime_newsn .= " AND (ck.create_at >= '".$invoice_time_st."') "; 
            }

            if(isset($params['to_invoice_time']) AND $params['to_invoice_time'])
            {
                $invoice_time_en = $params['to_invoice_time'];
                $where_invoicetime .= " AND (m.invoice_time <= '".$invoice_time_en."') ";

                $where_invoicetime_newsn .= " AND (ck.create_at <= '".$invoice_time_en."') ";
            }

            // $params['d_id'] = 33925;//33925,1076

            if(isset($params['d_id']) AND $params['d_id'])
            {
                $d_id = $params['d_id'];
                $where_d_id=" AND (cnit.d_id = '".$d_id."') ";
            }
  
            if(isset($params['sn']) AND $params['sn'])
            {
                $sn = $params['sn'];
                $where_sn=" AND (ck.sn = '".$sn."') or (ck.sn =(SELECT sn FROM market WHERE sn_ref='".$sn."' GROUP BY sn)) ";
            }

            if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                $where_no_show_brandshop = " AND m.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or m.warehouse_id in (71,92)";
                // allow warehouse WMDGC - คลังเคลมDigital, Brandshop Warehouse at Kerry
            }

            if(isset($params['bank']) && $params['bank']){
                if(is_array($params['bank'])){
                    $where_bank = " AND ck.bank in (" . implode(",",$params['bank']) . ")";
                }else{
                    $where_bank = " AND ck.bank = " . $params['bank'];
                }
            }

            if(isset($params['finance_group']) && $params['finance_group']){
                if(is_array($params['finance_group'])){
                    $where_finance_group = " AND d.finance_group in (" . implode(",",$params['finance_group']) . ")";
                }else{
                    $where_finance_group = " AND d.finance_group = '" . $params['finance_group']."'";
                }
            }

            $sql_result = "select ck.*,m.invoice_number,m.sn_ref,d.title as d_name,b.name as b_name,
            (select sub.pay_money from checkmoney sub where sub.sn = ck.sn and sub.type = 2) as main_price
            from credit_note_invoice_tr cnit
            left join checkmoney ck on ck.credit_tr_id=cnit.credit_tr_id
            left join market m on m.sn=ck.sn
            left join distributor d on d.id=ck.d_id
            left join bank b on b.id=ck.bank
            where 1=1 and ck.type in(1,2) ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_no_show_brandshop.$where_bank.$where_finance_group."
            group by ck.id
            ";

            $sql_result_addnewsn = "select ck.*,nm.sn_ref as invoice_number,m.sn_ref,d.title as d_name,b.name as b_name,
            (select sub.pay_money from checkmoney sub where sub.sn = ck.sn and sub.type = 4) as main_price
            from credit_note_invoice_tr cnit
            left join checkmoney ck on ck.credit_tr_id=cnit.credit_tr_id
            left join market m on m.sn=ck.sn
            left join distributor d on d.id=ck.d_id
            left join bank b on b.id=ck.bank
            left join newsn_manual nm ON nm.sn = ck.sn
            where 1=1 and ck.type in(3,4) ".$where_paytime.$where_invoicetime_newsn.$where_d_id.$where_bank.$where_finance_group."
            group by ck.id
            ";

            try {
                $result_ch = $db->fetchAll($sql_result);

                $result_ch_addnewsn = $db->fetchAll($sql_result_addnewsn);

                $mergeData = array_merge($result_ch,$result_ch_addnewsn);

                // print_r($mergeData);exit(); 

                $tempRef = array();
                foreach ($mergeData as $key) {
                    array_push($tempRef, $key['credit_tr_id']);
                }

                $tempRefNoSame = array_values(array_unique($tempRef));

                sort($tempRefNoSame);

                $getArrayData = array();

                foreach ($tempRefNoSame as $key_main) {
                    
                    foreach ($mergeData as $key_sub) {
                        
                        if($key_main == $key_sub['credit_tr_id']){
                            array_push($getArrayData, $key_sub);
                        }
                    }
                }

                $QCNCT = new Application_Model_CreditNoteCreditTr();

                $getCreditTrId = [];

                foreach ($getArrayData as $key) {
                    array_push($getCreditTrId, $key['credit_tr_id']);
                }

                // print_r($getCreditTrId);die;

                $getUniqueCreditTrId = array_values(array_unique($getCreditTrId));

                // print_r($getUniqueCreditTrId);die;

                $getCNCP = $QCNCT->getCheckMoneyCreditCNCPExcel($getUniqueCreditTrId);


                // print_r($getCNCP);

                $arrCNCP = [];

                foreach ($getUniqueCreditTrId as $key) {
                    
                    $arrCNCP[$key] = [];

                    foreach ($getCNCP as $item) {
                        
                        if($key == $item['sales_order']){
                            array_push($arrCNCP[$key], $item);
                        }

                    }
                }
 
            } catch (Exception $e) {
                echo 'error';
                exit;
            }

        }

        //$this->_exportExcelExportCashCollectionCSV($result_ch);
        $this->_exportExcelExportCashCollectionCreditExcel($getArrayData, $arrCNCP);
    }

    public function _exportExcelExportCashCollectionCreditExcel($data, $arrCNCP = []){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'AR_Card_'.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $heads = array(
            'Retailer ID',
            'Retailer Name',
            'Period',
            'Original Flag',
            'Outstanding Flag',
            'INVOICE / CN NUMBER',
            'TOTAL PRICE',
            'Cash Receive Reference',
            'Bank Receive',
            'Receive Period',
            'Receive Date',
            'Receive Balance (เครื่องหมายตรงข้ามกับยอดในคอลัมน์ F)',
            // 'เงินขาด',
            // 'เงินเกิน',
            'ค่าธรรมเนียม',
            'ลูกหนี้อื่น',
            'หนัก ณ ที่จ่าย 3%',
            'Outstanding Final'
        );

        fputcsv($output, $heads);

        $count = 0;
        $currentRef = '';

        $arrDataPrice = array();

        foreach($data as $item){

            if(!array_key_exists($item['sn'], $arrDataPrice)){

                $arrDataPrice[$item['sn']] = [
                    'main_price' => $item['main_price']*-1,
                    'balance' => $item['main_price']*-1,
                    'first' => $item['id'],
                    'last' => $item['id']
                ];
            }

            $arrDataPrice[$item['sn']]['last'] = $item['id'];

        }
        
        foreach($data as $item){

            if($currentRef == $item['credit_tr_id']){
                $count++;
            }else{
                $count = 0;
            }

            $currentRef = $item['credit_tr_id'];

            $lackOfMoney = '';

            if($item['lack_of_money']){
                $lackOfMoney = $this->convert2Decimal($item['lack_of_money']*-1);
            }

            $paymentSurplus = '';

            if($item['payment_surplus']){
                $paymentSurplus = $this->convert2Decimal($item['payment_surplus']*-1);
            }

            $payBankTransfer = '';

            if($item['pay_banktransfer']){
                $payBankTransfer = $this->convert2Decimal($item['pay_banktransfer']*-1);
            }

            $paymentETC = '';

            if($item['payment_etc']){
                $paymentETC = $this->convert2Decimal($item['payment_etc'])*-1;
            }

            $paySerive = '';

            if($item['pay_service']){
                $paySerive = $this->convert2Decimal($item['pay_service']*-1);
            }

            $outStandingFinal = '-';

            if($count == 0){

                // $totalPay = $this->convert2Decimal($item['total_money_transfer'])+$this->convert2Decimal($item['lack_of_money'])+$this->convert2Decimal($item['payment_surplus'])+$this->convert2Decimal($item['pay_banktransfer'])+$this->convert2Decimal($item['payment_etc'])+$this->convert2Decimal($item['pay_service']);

                // $row = array();
                // $row[] = $item['d_id'];
                // $row[] = $item['d_name'];
                // $row[] = date("ymd", strtotime($item['pay_time']));
                // $row[] = '';
                // $row[] = '';
                // $row[] = '';
                // $row[] = $this->convert2Decimal($totalPay);
                // $row[] = "'".$currentRef;
                // $row[] = $item['b_name'];
                // $row[] = date("ymd", strtotime($item['pay_time']));
                // $row[] = date("j-M-y", strtotime($item['pay_time']));
                // $row[] = $this->convert2Decimal($item['total_money_transfer'])*-1;
                // $row[] = $lackOfMoney;
                // $row[] = $paymentSurplus;
                // $row[] = $payBankTransfer;
                // $row[] = $paymentETC;
                // $row[] = $paySerive;

                // $sumTotal = $totalPay + (($item['total_money_transfer']*-1) + $lackOfMoney + $paymentSurplus + $payBankTransfer + $paymentETC + $paySerive);

                // if((int)$sumTotal != 0){
                //     $outStandingFinal = $this->convert2Decimal($sumTotal);
                // }

                // $row[] = $outStandingFinal;
            
                // fputcsv($output, $row);
                // unset($row);

                $outStandingFinal = '-';

                foreach ($arrCNCP[$currentRef] as $key) {
                    $row = array();
                    $row[] = $item['d_id'];
                    $row[] = $item['d_name'];
                    $row[] = date("ymd", strtotime($item['pay_time']));
                    $row[] = '';
                    $row[] = '';
                    $row[] = $key['creditnote_sn'];
                    $row[] = $this->convert2Decimal($key['use_discount']*-1);
                    $row[] = "'".$currentRef;
                    $row[] = $item['b_name'];
                    $row[] = date("ymd", strtotime($item['pay_time']));
                    $row[] = date("j-M-y", strtotime($item['pay_time']));
                    $row[] = $this->convert2Decimal($key['use_discount']);
                    // $row[] = '';
                    // $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = $outStandingFinal;
                
                    fputcsv($output, $row);
                    unset($row);
                    unset($key);
                }
            }


            $row = array();
            $row[] = $item['d_id'];
            $row[] = $item['d_name'];
            $row[] = date("ymd", strtotime($item['pay_time']));

            if($arrDataPrice[$item['sn']]['first'] == $item['id']){
                $row[] = 'Original';
            }else{
                $row[] = 'NULL';
            }

            if($arrDataPrice[$item['sn']]['last'] == $item['id']){
                $row[] = 'Outstanding';
            }else{
                $row[] = 'NULL';
            }

            $row[] = $item['invoice_number'];

            $row[] = $this->convert2Decimal($arrDataPrice[$item['sn']]['balance']);

            $arrDataPrice[$item['sn']]['balance'] = $arrDataPrice[$item['sn']]['balance'] + ($item['pay_money']*-1);

            $row[] = "'".$currentRef;
            $row[] = $item['b_name'];
            $row[] = date("ymd", strtotime($item['pay_time']));
            $row[] = date("j-M-y", strtotime($item['pay_time']));

            if($count == 0){
                $row[] = $this->convert2Decimal(($item['pay_money']*-1)-$lackOfMoney-$paymentSurplus-$payBankTransfer-$paymentETC-$paySerive);

                // $row[] = $lackOfMoney;
                // $row[] = $paymentSurplus;
                $row[] = $payBankTransfer;
                $row[] = $paymentETC;
                $row[] = $paySerive;
            }else{
                $row[] = $this->convert2Decimal($item['pay_money'])*-1;

                // $row[] = '';
                // $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
            }

            if((int)$arrDataPrice[$item['sn']]['balance'] != 0){
                $outStandingFinal = $this->convert2Decimal($arrDataPrice[$item['sn']]['balance']);
            }

            $row[] = $outStandingFinal;
        
            fputcsv($output, $row);
            unset($row);
            unset($item);

        }

        unset($data);
        unset($arrCNCP);
    }

    public function _getDataForExcelMultiCashCollection($data,$params)
    {

        if(isset($params['to_collection_time']) AND $params['to_collection_time']){
            $params['to_collection_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }

        if(isset($params['to_invoice_time']) AND $params['to_invoice_time']){
            $params['to_invoice_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }

        $db = Zend_Registry::get('db');
       // print_r($data);die;
        $arr_d_id=0;

        $where_paytime = '';
        $where_invoicetime = '';
        $where_d_id = '';
        $where_sn = '';
        $where_no_show_brandshop = '';
        $where_bank = '';

        $where_invoicetime_newsn = '';

        if($arr_d_id == 0){

            if(isset($params['from_collection_time']) AND $params['from_collection_time'])
            {
                $pay_time_st = $params['from_collection_time'];
                $where_paytime .= " AND (ck.pay_time >= '".$pay_time_st."') ";
            }

            if(isset($params['to_collection_time']) AND $params['to_collection_time'])
            {
                $pay_time_en = $params['to_collection_time'];
                $where_paytime .= " AND (ck.pay_time <= '".$pay_time_en."') ";
            }

            if(isset($params['from_invoice_time']) AND $params['from_invoice_time'])
            {
                $invoice_time_st = $params['from_invoice_time'];
                $where_invoicetime .= " AND (m.invoice_time >= '".$invoice_time_st."') ";

                $where_invoicetime_newsn .= " AND (ck.create_at >= '".$invoice_time_st."') "; 
            }

            if(isset($params['to_invoice_time']) AND $params['to_invoice_time'])
            {
                $invoice_time_en = $params['to_invoice_time'];
                $where_invoicetime .= " AND (m.invoice_time <= '".$invoice_time_en."') ";

                $where_invoicetime_newsn .= " AND (ck.create_at <= '".$invoice_time_en."') ";
            }

            // $params['d_id'] = 33925;//33925,1076

            if(isset($params['d_id']) AND $params['d_id'])
            {
                $d_id = $params['d_id'];
                $where_d_id=" AND (cnit.d_id = '".$d_id."') ";
            }
  
            if(isset($params['sn']) AND $params['sn'])
            {
                $sn = $params['sn'];
                $where_sn=" AND (ck.sn = '".$sn."') or (ck.sn =(SELECT sn FROM market WHERE sn_ref='".$sn."' GROUP BY sn)) ";
            }

            if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                $where_no_show_brandshop = " AND m.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or m.warehouse_id in (71,92)";
                // allow warehouse WMDGC - คลังเคลมDigital, Brandshop Warehouse at Kerry
            }

            if(isset($params['bank']) && $params['bank']){
                if(is_array($params['bank'])){
                    $where_bank = " AND ck.bank in (" . implode(",",$params['bank']) . ")";
                }else{
                    $where_bank = " AND ck.bank = " . $params['bank'];
                }
            }

            $sql_result = "select ck.*,m.invoice_number,m.sn_ref,d.title as d_name,b.name as b_name,
            (select sub.pay_money from checkmoney sub where sub.sn = ck.sn and sub.type = 2) as main_price
            from credit_note_invoice_tr cnit
            left join checkmoney ck on ck.multicash_tr_id=cnit.multicash_tr_id
            left join market m on m.sn=ck.sn
            left join distributor d on d.id=ck.d_id
            left join bank b on b.id=ck.bank
            where 1=1 and ck.type in(1,2) ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_no_show_brandshop.$where_bank."
            group by ck.id
            ";

            // $sql_result_addnewsn = "select ck.*,nm.sn_ref as invoice_number,m.sn_ref,d.title as d_name,b.name as b_name,
            // (select sub.pay_money from checkmoney sub where sub.sn = ck.sn and sub.type = 4) as main_price
            // from credit_note_invoice_tr cnit
            // left join checkmoney ck on ck.multicash_tr_id=cnit.multicash_tr_id
            // left join market m on m.sn=ck.sn
            // left join distributor d on d.id=ck.d_id
            // left join bank b on b.id=ck.bank
            // left join newsn_manual nm ON nm.sn = ck.sn
            // where 1=1 and ck.type in(3,4) ".$where_paytime.$where_invoicetime_newsn.$where_d_id.$where_bank."
            // group by ck.id
            // ";

            try {
                $result_ch = $db->fetchAll($sql_result);

                // $result_ch_addnewsn = $db->fetchAll($sql_result_addnewsn);
                $result_ch_addnewsn = array();

                $mergeData = array_merge($result_ch,$result_ch_addnewsn);

                // print_r($mergeData);exit(); 

                $tempRef = array();
                foreach ($mergeData as $key) {
                    array_push($tempRef, $key['multicash_tr_id']);
                }

                $tempRefNoSame = array_values(array_unique($tempRef));

                sort($tempRefNoSame);

                $getArrayData = array();

                foreach ($tempRefNoSame as $key_main) {
                    
                    foreach ($mergeData as $key_sub) {
                        
                        if($key_main == $key_sub['multicash_tr_id']){
                            array_push($getArrayData, $key_sub);
                        }
                    }
                }

                $QCNCT = new Application_Model_CreditNoteCreditTr();

                $getCreditTrId = [];

                foreach ($getArrayData as $key) {
                    array_push($getCreditTrId, $key['multicash_tr_id']);
                }

                // print_r($getCreditTrId);die;

                $getUniqueCreditTrId = array_values(array_unique($getCreditTrId));

                // print_r($getUniqueCreditTrId);die;

                $getCNCP = $QCNCT->getCheckMoneyCreditCNCPExcel($getUniqueCreditTrId);


                // print_r($getCNCP);

                $arrCNCP = [];

                foreach ($getUniqueCreditTrId as $key) {
                    
                    $arrCNCP[$key] = [];

                    foreach ($getCNCP as $item) {
                        
                        if($key == $item['sales_order']){
                            array_push($arrCNCP[$key], $item);
                        }

                    }
                }
 
            } catch (Exception $e) {
                echo 'error';
                exit;
            }

        }

        //$this->_exportExcelExportCashCollectionCSV($result_ch);
        $this->_exportExcelExportCashCollectionMultiCashExcel($getArrayData, $arrCNCP);
    }

    public function _exportExcelExportCashCollectionMultiCashExcel($data, $arrCNCP = []){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'AR_Card_'.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $heads = array(
            'Retailer ID',
            'Retailer Name',
            'Period',
            'Original Flag',
            'Outstanding Flag',
            'INVOICE / CN NUMBER',
            'TOTAL PRICE',
            'MultiCash Receive Reference',
            'Bank Receive',
            'Receive Period',
            'Receive Date',
            'Receive Balance (เครื่องหมายตรงข้ามกับยอดในคอลัมน์ F)',
            // 'เงินขาด',
            // 'เงินเกิน',
            'ค่าธรรมเนียม',
            'ลูกหนี้อื่น',
            'หนัก ณ ที่จ่าย 3%',
            'Outstanding Final'
        );

        fputcsv($output, $heads);

        $count = 0;
        $currentRef = '';

        $arrDataPrice = array();

        foreach($data as $item){

            if(!array_key_exists($item['sn'], $arrDataPrice)){

                $arrDataPrice[$item['sn']] = [
                    'main_price' => $item['main_price']*-1,
                    'balance' => $item['main_price']*-1,
                    'first' => $item['id'],
                    'last' => $item['id']
                ];
            }

            $arrDataPrice[$item['sn']]['last'] = $item['id'];

        }
        
        foreach($data as $item){

            if($currentRef == $item['multicash_tr_id']){
                $count++;
            }else{
                $count = 0;
            }

            $currentRef = $item['multicash_tr_id'];

            $lackOfMoney = '';

            if($item['lack_of_money']){
                $lackOfMoney = $this->convert2Decimal($item['lack_of_money']*-1);
            }

            $paymentSurplus = '';

            if($item['payment_surplus']){
                $paymentSurplus = $this->convert2Decimal($item['payment_surplus']*-1);
            }

            $payBankTransfer = '';

            if($item['pay_banktransfer']){
                $payBankTransfer = $this->convert2Decimal($item['pay_banktransfer']*-1);
            }

            $paymentETC = '';

            if($item['payment_etc']){
                $paymentETC = $this->convert2Decimal($item['payment_etc'])*-1;
            }

            $paySerive = '';

            if($item['pay_service']){
                $paySerive = $this->convert2Decimal($item['pay_service']*-1);
            }

            $outStandingFinal = '-';

            if($count == 0){

                // $totalPay = $this->convert2Decimal($item['total_money_transfer'])+$this->convert2Decimal($item['lack_of_money'])+$this->convert2Decimal($item['payment_surplus'])+$this->convert2Decimal($item['pay_banktransfer'])+$this->convert2Decimal($item['payment_etc'])+$this->convert2Decimal($item['pay_service']);

                // $row = array();
                // $row[] = $item['d_id'];
                // $row[] = $item['d_name'];
                // $row[] = date("ymd", strtotime($item['pay_time']));
                // $row[] = '';
                // $row[] = '';
                // $row[] = '';
                // $row[] = $this->convert2Decimal($totalPay);
                // $row[] = "'".$currentRef;
                // $row[] = $item['b_name'];
                // $row[] = date("ymd", strtotime($item['pay_time']));
                // $row[] = date("j-M-y", strtotime($item['pay_time']));
                // $row[] = $this->convert2Decimal($item['total_money_transfer'])*-1;
                // $row[] = $lackOfMoney;
                // $row[] = $paymentSurplus;
                // $row[] = $payBankTransfer;
                // $row[] = $paymentETC;
                // $row[] = $paySerive;

                // $sumTotal = $totalPay + (($item['total_money_transfer']*-1) + $lackOfMoney + $paymentSurplus + $payBankTransfer + $paymentETC + $paySerive);

                // if((int)$sumTotal != 0){
                //     $outStandingFinal = $this->convert2Decimal($sumTotal);
                // }

                // $row[] = $outStandingFinal;
            
                // fputcsv($output, $row);
                // unset($row);

                $outStandingFinal = '-';

                foreach ($arrCNCP[$currentRef] as $key) {
                    $row = array();
                    $row[] = $item['d_id'];
                    $row[] = $item['d_name'];
                    $row[] = date("ymd", strtotime($item['pay_time']));
                    $row[] = '';
                    $row[] = '';
                    $row[] = $key['creditnote_sn'];
                    $row[] = $this->convert2Decimal($key['use_discount']*-1);
                    $row[] = "'".$currentRef;
                    $row[] = $item['b_name'];
                    $row[] = date("ymd", strtotime($item['pay_time']));
                    $row[] = date("j-M-y", strtotime($item['pay_time']));
                    $row[] = $this->convert2Decimal($key['use_discount']);
                    // $row[] = '';
                    // $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = $outStandingFinal;
                
                    fputcsv($output, $row);
                    unset($row);
                    unset($key);
                }
            }


            $row = array();
            $row[] = $item['d_id'];
            $row[] = $item['d_name'];
            $row[] = date("ymd", strtotime($item['pay_time']));

            if($arrDataPrice[$item['sn']]['first'] == $item['id']){
                $row[] = 'Original';
            }else{
                $row[] = 'NULL';
            }

            if($arrDataPrice[$item['sn']]['last'] == $item['id']){
                $row[] = 'Outstanding';
            }else{
                $row[] = 'NULL';
            }

            $row[] = $item['invoice_number'];

            $row[] = $this->convert2Decimal($arrDataPrice[$item['sn']]['balance']);

            $arrDataPrice[$item['sn']]['balance'] = $arrDataPrice[$item['sn']]['balance'] + ($item['pay_money']*-1);

            $row[] = "'".$currentRef;
            $row[] = $item['b_name'];
            $row[] = date("ymd", strtotime($item['pay_time']));
            $row[] = date("j-M-y", strtotime($item['pay_time']));

            if($count == 0){
                $row[] = $this->convert2Decimal(($item['pay_money']*-1)-$lackOfMoney-$paymentSurplus-$payBankTransfer-$paymentETC-$paySerive);

                // $row[] = $lackOfMoney;
                // $row[] = $paymentSurplus;
                $row[] = $payBankTransfer;
                $row[] = $paymentETC;
                $row[] = $paySerive;
            }else{
                $row[] = $this->convert2Decimal($item['pay_money'])*-1;

                // $row[] = '';
                // $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
            }

            if((int)$arrDataPrice[$item['sn']]['balance'] != 0){
                $outStandingFinal = $this->convert2Decimal($arrDataPrice[$item['sn']]['balance']);
            }

            $row[] = $outStandingFinal;
        
            fputcsv($output, $row);
            unset($row);
            unset($item);

        }

        unset($data);
        unset($arrCNCP);
    }

    public function _getDataForExcelCashCreditCollectionOutStanding($data,$params)
    {

        if(isset($params['to_collection_time']) AND $params['to_collection_time']){
            $params['to_collection_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }

        if(isset($params['to_invoice_time']) AND $params['to_invoice_time']){
            $params['to_invoice_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }

        $db = Zend_Registry::get('db');
       // print_r($data);die;
        $arr_d_id=0;

        $where_paytime = '';
        $where_invoicetime = '';
        $where_d_id = '';
        $where_sn = '';
        $where_no_show_brandshop = '';
        $where_bank = '';
        $where_finance_group = '';

        $where_invoicetime_newsn = '';

        if($arr_d_id == 0){

            if(isset($params['from_collection_time']) AND $params['from_collection_time'])
            {
                $pay_time_st = $params['from_collection_time'];
                $where_paytime .= " AND (ck.pay_time >= '".$pay_time_st."') ";
            }

            if(isset($params['to_collection_time']) AND $params['to_collection_time'])
            {
                $pay_time_en = $params['to_collection_time'];
                $where_paytime .= " AND (ck.pay_time <= '".$pay_time_en."') ";
            }

            if(isset($params['from_invoice_time']) AND $params['from_invoice_time'])
            {
                $invoice_time_st = $params['from_invoice_time'];
                $where_invoicetime .= " AND (m.invoice_time >= '".$invoice_time_st."') ";

                $where_invoicetime_newsn .= " AND (ck.create_at >= '".$invoice_time_st."') "; 
            }

            if(isset($params['to_invoice_time']) AND $params['to_invoice_time'])
            {
                $invoice_time_en = $params['to_invoice_time'];
                $where_invoicetime .= " AND (m.invoice_time <= '".$invoice_time_en."') ";

                $where_invoicetime_newsn .= " AND (ck.create_at <= '".$invoice_time_en."') ";
            }

            // $params['d_id'] = 33925;//33925,1076

            if(isset($params['d_id']) AND $params['d_id'])
            {
                $d_id = $params['d_id'];
                $where_d_id=" AND (cnit.d_id = '".$d_id."') ";
            }
  
            if(isset($params['sn']) AND $params['sn'])
            {
                $sn = $params['sn'];
                $where_sn=" AND (ck.sn = '".$sn."') or (ck.sn =(SELECT sn FROM market WHERE sn_ref='".$sn."' GROUP BY sn)) ";
            }

            if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                $where_no_show_brandshop = " AND m.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or m.warehouse_id in (71,92)";
                // allow warehouse WMDGC - คลังเคลมDigital, Brandshop Warehouse at Kerry
            }

            if(isset($params['bank']) && $params['bank']){
                if(is_array($params['bank'])){
                    $where_bank = " AND ck.bank in (" . implode(",",$params['bank']) . ")";
                }else{
                    $where_bank = " AND ck.bank = " . $params['bank'];
                }
            }

            if(isset($params['finance_group']) && $params['finance_group']){
                if(is_array($params['finance_group'])){
                    $where_finance_group = " AND d.finance_group in (" . implode(",",$params['finance_group']) . ")";
                }else{
                    $where_finance_group = " AND d.finance_group = '" . $params['finance_group']."'";
                }
            }

            $sql_result = "select ck.*,m.invoice_number,m.sn_ref,d.title as d_name,b.name as b_name,
            (select sub.pay_money from checkmoney sub where sub.sn = ck.sn and sub.type = 2) as main_price
            from credit_note_invoice_tr cnit
            left join checkmoney ck on ck.credit_tr_id=cnit.credit_tr_id
            left join market m on m.sn=ck.sn
            left join distributor d on d.id=ck.d_id
            left join bank b on b.id=ck.bank
            where 1=1 and ck.type in(1,2) ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_no_show_brandshop.$where_bank.$where_finance_group."
            group by ck.id
            ";

            $sql_result_addnewsn = "select ck.*,nm.sn_ref as invoice_number,m.sn_ref,d.title as d_name,b.name as b_name,
            (select sub.pay_money from checkmoney sub where sub.sn = ck.sn and sub.type = 4) as main_price
            from credit_note_invoice_tr cnit
            left join checkmoney ck on ck.credit_tr_id=cnit.credit_tr_id
            left join market m on m.sn=ck.sn
            left join distributor d on d.id=ck.d_id
            left join bank b on b.id=ck.bank
            left join newsn_manual nm ON nm.sn = ck.sn
            where 1=1 and ck.type in(3,4) ".$where_paytime.$where_invoicetime_newsn.$where_d_id.$where_bank.$where_finance_group."
            group by ck.id
            ";

            try {
                $result_ch = $db->fetchAll($sql_result);  
                
                $result_ch_addnewsn = $db->fetchAll($sql_result_addnewsn);

                $mergeData = array_merge($result_ch,$result_ch_addnewsn);

                $tempRef = array();
                foreach ($mergeData as $key) {
                    array_push($tempRef, $key['credit_tr_id']);
                }

                $tempRefNoSame = array_values(array_unique($tempRef));

                sort($tempRefNoSame);

                $getArrayData = array();

                foreach ($tempRefNoSame as $key_main) {
                    
                    foreach ($mergeData as $key_sub) {
                        
                        if($key_main == $key_sub['credit_tr_id']){
                            array_push($getArrayData, $key_sub);
                        }
                    }
                }

                $QCNCT = new Application_Model_CreditNoteCreditTr();

                $getCreditTrId = [];

                foreach ($getArrayData as $key) {
                    array_push($getCreditTrId, $key['credit_tr_id']);
                }

                // print_r($getCreditTrId);die;

                $getUniqueCreditTrId = array_values(array_unique($getCreditTrId));

                // print_r($getUniqueCreditTrId);die;

                $getCNCP = $QCNCT->getCheckMoneyCreditCNCPExcel($getUniqueCreditTrId);


                // print_r($getCNCP);

                $arrCNCP = [];

                foreach ($getUniqueCreditTrId as $key) {
                    
                    $arrCNCP[$key] = [];

                    foreach ($getCNCP as $item) {
                        
                        if($key == $item['sales_order']){
                            array_push($arrCNCP[$key], $item);
                        }

                    }
                }

                // print_r($arrCNCP);die;

                // print_r($result_ch);exit(); 
            } catch (Exception $e) {
                echo 'error';
                exit;
            }
            //print_r($result_ch);die;
        }

        //print_r($result_ch);die;
        //$this->_exportExcelExportCashCollectionCSV($result_ch);
        $this->_exportExcelExportCashCollectionCreditOutStandingExcel($getArrayData, $arrCNCP);
    }

    public function _exportExcelExportCashCollectionCreditOutStandingExcel($data, $arrCNCP = []){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'AR_Outstanding_'.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $heads = array(
            'Retailer ID',
            'Retailer Name',
            'Period',
            'Original Flag',
            'Outstanding Flag',
            'INVOICE / CN NUMBER',
            'TOTAL PRICE',
            'Cash Receive Reference',
            'Bank Receive',
            'Receive Period',
            'Receive Date',
            'Receive Balance (เครื่องหมายตรงข้ามกับยอดในคอลัมน์ F)',
            // 'เงินขาด',
            // 'เงินเกิน',
            'ค่าธรรมเนียม',
            'ลูกหนี้อื่น',
            'หนัก ณ ที่จ่าย 3%',
            'Outstanding Final'
        );

        fputcsv($output, $heads);

        $count = 0;
        $currentRef = '';

        $arrDataPrice = array();

        foreach($data as $item){

            if(!array_key_exists($item['sn'], $arrDataPrice)){

                $arrDataPrice[$item['sn']] = [
                    'main_price' => $item['main_price']*-1,
                    'balance' => $item['main_price']*-1,
                    'check_balance' => $item['main_price']*-1,
                    'first' => $item['id'],
                    'last' => $item['id']
                ];
            }

            $arrDataPrice[$item['sn']]['last'] = $item['id'];

            $arrDataPrice[$item['sn']]['check_balance'] = $arrDataPrice[$item['sn']]['check_balance'] + ($item['pay_money']*-1);

        }
        
        foreach($data as $item){

            if($currentRef == $item['credit_tr_id']){
                $count++;
            }else{
                $count = 0;
            }

            $currentRef = $item['credit_tr_id'];

            $lackOfMoney = '';

            if($item['lack_of_money']){
                $lackOfMoney = $this->convert2Decimal($item['lack_of_money']*-1);
            }

            $paymentSurplus = '';

            if($item['payment_surplus']){
                $paymentSurplus = $this->convert2Decimal($item['payment_surplus']*-1);
            }

            $payBankTransfer = '';

            if($item['pay_banktransfer']){
                $payBankTransfer = $this->convert2Decimal($item['pay_banktransfer']*-1);
            }

            $paymentETC = '';

            if($item['payment_etc']){
                $paymentETC = $this->convert2Decimal($item['payment_etc'])*-1;
            }

            $paySerive = '';

            if($item['pay_service']){
                $paySerive = $this->convert2Decimal($item['pay_service']*-1);
            }

            $outStandingFinal = '-';

            if($count == 0){

                // $totalPay = $this->convert2Decimal($item['total_money_transfer'])+$this->convert2Decimal($item['lack_of_money'])+$this->convert2Decimal($item['payment_surplus'])+$this->convert2Decimal($item['pay_banktransfer'])+$this->convert2Decimal($item['payment_etc'])+$this->convert2Decimal($item['pay_service']);

                // $row = array();
                // $row[] = $item['d_id'];
                // $row[] = $item['d_name'];
                // $row[] = date("ymd", strtotime($item['pay_time']));
                // $row[] = '';
                // $row[] = '';
                // $row[] = '';
                // $row[] = $this->convert2Decimal($totalPay);
                // $row[] = "'".$currentRef;
                // $row[] = $item['b_name'];
                // $row[] = date("ymd", strtotime($item['pay_time']));
                // $row[] = date("j-M-y", strtotime($item['pay_time']));
                // $row[] = $this->convert2Decimal($item['total_money_transfer'])*-1;
                // $row[] = $lackOfMoney;
                // $row[] = $paymentSurplus;
                // $row[] = $payBankTransfer;
                // $row[] = $paymentETC;
                // $row[] = $paySerive;

                // $sumTotal = $totalPay + (($item['total_money_transfer']*-1) + $lackOfMoney + $paymentSurplus + $payBankTransfer + $paymentETC + $paySerive);

                // if((int)$sumTotal != 0){
                //     $outStandingFinal = $this->convert2Decimal($sumTotal);
                // }

                // $row[] = $outStandingFinal;
            
                // fputcsv($output, $row);
                // unset($row);

                $outStandingFinal = '-';

                foreach ($arrCNCP[$currentRef] as $key) {
                    $row = array();
                    $row[] = $item['d_id'];
                    $row[] = $item['d_name'];
                    $row[] = date("ymd", strtotime($item['pay_time']));
                    $row[] = '';
                    $row[] = '';
                    $row[] = $key['creditnote_sn'];
                    $row[] = $this->convert2Decimal($key['use_discount']*-1);
                    $row[] = "'".$currentRef;
                    $row[] = $item['b_name'];
                    $row[] = date("ymd", strtotime($item['pay_time']));
                    $row[] = date("j-M-y", strtotime($item['pay_time']));
                    $row[] = $this->convert2Decimal($key['use_discount']);
                    // $row[] = '';
                    // $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = $outStandingFinal;
                
                    if($arrDataPrice[$item['sn']]['last'] == $item['id'] && (int)$arrDataPrice[$item['sn']]['check_balance'] <> 0){
                        fputcsv($output, $row);
                    }
                    unset($row);
                    unset($key);
                }
            }


            $row = array();
            $row[] = $item['d_id'];
            $row[] = $item['d_name'];
            $row[] = date("ymd", strtotime($item['pay_time']));

            if($arrDataPrice[$item['sn']]['first'] == $item['id']){
                $row[] = 'Original';
            }else{
                $row[] = 'NULL';
            }

            if($arrDataPrice[$item['sn']]['last'] == $item['id']){
                $row[] = 'Outstanding';
            }else{
                $row[] = 'NULL';
            }

            $row[] = $item['invoice_number'];

            $row[] = $this->convert2Decimal($arrDataPrice[$item['sn']]['balance']);

            $arrDataPrice[$item['sn']]['balance'] = $arrDataPrice[$item['sn']]['balance'] + ($item['pay_money']*-1);

            $row[] = "'".$currentRef;
            $row[] = $item['b_name'];
            $row[] = date("ymd", strtotime($item['pay_time']));
            $row[] = date("j-M-y", strtotime($item['pay_time']));

            if($count == 0){
                $row[] = $this->convert2Decimal(($item['pay_money']*-1)-$lackOfMoney-$paymentSurplus-$payBankTransfer-$paymentETC-$paySerive);

                // $row[] = $lackOfMoney;
                // $row[] = $paymentSurplus;
                $row[] = $payBankTransfer;
                $row[] = $paymentETC;
                $row[] = $paySerive;
            }else{
                $row[] = $this->convert2Decimal($item['pay_money'])*-1;

                // $row[] = '';
                // $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
            }

            if((int)$arrDataPrice[$item['sn']]['balance'] != 0){
                $outStandingFinal = $this->convert2Decimal($arrDataPrice[$item['sn']]['balance']);
            }

            $row[] = $outStandingFinal;
        
            if($arrDataPrice[$item['sn']]['last'] == $item['id'] && (int)$arrDataPrice[$item['sn']]['check_balance'] <> 0){
                fputcsv($output, $row);
            }
            unset($row);
            unset($item);

        }

        unset($data);
        unset($arrCNCP);
    }

    public function _getDataForExcelMultiCashCollectionOutStanding($data,$params)
    {

        if(isset($params['to_collection_time']) AND $params['to_collection_time']){
            $params['to_collection_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }

        if(isset($params['to_invoice_time']) AND $params['to_invoice_time']){
            $params['to_invoice_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }

        $db = Zend_Registry::get('db');
       // print_r($data);die;
        $arr_d_id=0;

        $where_paytime = '';
        $where_invoicetime = '';
        $where_d_id = '';
        $where_sn = '';
        $where_no_show_brandshop = '';
        $where_bank = '';

        $where_invoicetime_newsn = '';

        if($arr_d_id == 0){

            if(isset($params['from_collection_time']) AND $params['from_collection_time'])
            {
                $pay_time_st = $params['from_collection_time'];
                $where_paytime .= " AND (ck.pay_time >= '".$pay_time_st."') ";
            }

            if(isset($params['to_collection_time']) AND $params['to_collection_time'])
            {
                $pay_time_en = $params['to_collection_time'];
                $where_paytime .= " AND (ck.pay_time <= '".$pay_time_en."') ";
            }

            if(isset($params['from_invoice_time']) AND $params['from_invoice_time'])
            {
                $invoice_time_st = $params['from_invoice_time'];
                $where_invoicetime .= " AND (m.invoice_time >= '".$invoice_time_st."') ";

                $where_invoicetime_newsn .= " AND (ck.create_at >= '".$invoice_time_st."') "; 
            }

            if(isset($params['to_invoice_time']) AND $params['to_invoice_time'])
            {
                $invoice_time_en = $params['to_invoice_time'];
                $where_invoicetime .= " AND (m.invoice_time <= '".$invoice_time_en."') ";

                $where_invoicetime_newsn .= " AND (ck.create_at <= '".$invoice_time_en."') ";
            }

            // $params['d_id'] = 33925;//33925,1076

            if(isset($params['d_id']) AND $params['d_id'])
            {
                $d_id = $params['d_id'];
                $where_d_id=" AND (cnit.d_id = '".$d_id."') ";
            }
  
            if(isset($params['sn']) AND $params['sn'])
            {
                $sn = $params['sn'];
                $where_sn=" AND (ck.sn = '".$sn."') or (ck.sn =(SELECT sn FROM market WHERE sn_ref='".$sn."' GROUP BY sn)) ";
            }

            if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                $where_no_show_brandshop = " AND m.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or m.warehouse_id in (71,92)";
                // allow warehouse WMDGC - คลังเคลมDigital, Brandshop Warehouse at Kerry
            }

            if(isset($params['bank']) && $params['bank']){
                if(is_array($params['bank'])){
                    $where_bank = " AND ck.bank in (" . implode(",",$params['bank']) . ")";
                }else{
                    $where_bank = " AND ck.bank = " . $params['bank'];
                }
            }

            $sql_result = "select ck.*,m.invoice_number,m.sn_ref,d.title as d_name,b.name as b_name,
            (select sub.pay_money from checkmoney sub where sub.sn = ck.sn and sub.type = 2) as main_price
            from credit_note_invoice_tr cnit
            left join checkmoney ck on ck.multicash_tr_id=cnit.multicash_tr_id
            left join market m on m.sn=ck.sn
            left join distributor d on d.id=ck.d_id
            left join bank b on b.id=ck.bank
            where 1=1 and ck.type in(1,2) ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_no_show_brandshop.$where_bank."
            group by ck.id
            ";

            // $sql_result_addnewsn = "select ck.*,nm.sn_ref as invoice_number,m.sn_ref,d.title as d_name,b.name as b_name,
            // (select sub.pay_money from checkmoney sub where sub.sn = ck.sn and sub.type = 4) as main_price
            // from credit_note_invoice_tr cnit
            // left join checkmoney ck on ck.multicash_tr_id=cnit.multicash_tr_id
            // left join market m on m.sn=ck.sn
            // left join distributor d on d.id=ck.d_id
            // left join bank b on b.id=ck.bank
            // left join newsn_manual nm ON nm.sn = ck.sn
            // where 1=1 and ck.type in(3,4) ".$where_paytime.$where_invoicetime_newsn.$where_d_id.$where_bank."
            // group by ck.id
            // ";

            try {
                $result_ch = $db->fetchAll($sql_result);  
                
                // $result_ch_addnewsn = $db->fetchAll($sql_result_addnewsn);
                $result_ch_addnewsn = array();

                $mergeData = array_merge($result_ch,$result_ch_addnewsn);

                $tempRef = array();
                foreach ($mergeData as $key) {
                    array_push($tempRef, $key['multicash_tr_id']);
                }

                $tempRefNoSame = array_values(array_unique($tempRef));

                sort($tempRefNoSame);

                $getArrayData = array();

                foreach ($tempRefNoSame as $key_main) {
                    
                    foreach ($mergeData as $key_sub) {
                        
                        if($key_main == $key_sub['multicash_tr_id']){
                            array_push($getArrayData, $key_sub);
                        }
                    }
                }

                $QCNCT = new Application_Model_CreditNoteCreditTr();

                $getCreditTrId = [];

                foreach ($getArrayData as $key) {
                    array_push($getCreditTrId, $key['multicash_tr_id']);
                }

                // print_r($getCreditTrId);die;

                $getUniqueCreditTrId = array_values(array_unique($getCreditTrId));

                // print_r($getUniqueCreditTrId);die;

                $getCNCP = $QCNCT->getCheckMoneyCreditCNCPExcel($getUniqueCreditTrId);


                // print_r($getCNCP);

                $arrCNCP = [];

                foreach ($getUniqueCreditTrId as $key) {
                    
                    $arrCNCP[$key] = [];

                    foreach ($getCNCP as $item) {
                        
                        if($key == $item['sales_order']){
                            array_push($arrCNCP[$key], $item);
                        }

                    }
                }

                // print_r($arrCNCP);die;

                // print_r($result_ch);exit(); 
            } catch (Exception $e) {
                echo 'error';
                exit;
            }
            //print_r($result_ch);die;
        }

        //print_r($result_ch);die;
        //$this->_exportExcelExportCashCollectionCSV($result_ch);
        $this->_exportExcelExportMultiCashCollectionCreditOutStandingExcel($getArrayData, $arrCNCP);
    }

    public function _exportExcelExportMultiCashCollectionCreditOutStandingExcel($data, $arrCNCP = []){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'AR_Outstanding_'.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $heads = array(
            'Retailer ID',
            'Retailer Name',
            'Period',
            'Original Flag',
            'Outstanding Flag',
            'INVOICE / CN NUMBER',
            'TOTAL PRICE',
            'MultiCash Receive Reference',
            'Bank Receive',
            'Receive Period',
            'Receive Date',
            'Receive Balance (เครื่องหมายตรงข้ามกับยอดในคอลัมน์ F)',
            // 'เงินขาด',
            // 'เงินเกิน',
            'ค่าธรรมเนียม',
            'ลูกหนี้อื่น',
            'หนัก ณ ที่จ่าย 3%',
            'Outstanding Final'
        );

        fputcsv($output, $heads);

        $count = 0;
        $currentRef = '';

        $arrDataPrice = array();

        foreach($data as $item){

            if(!array_key_exists($item['sn'], $arrDataPrice)){

                $arrDataPrice[$item['sn']] = [
                    'main_price' => $item['main_price']*-1,
                    'balance' => $item['main_price']*-1,
                    'check_balance' => $item['main_price']*-1,
                    'first' => $item['id'],
                    'last' => $item['id']
                ];
            }

            $arrDataPrice[$item['sn']]['last'] = $item['id'];

            $arrDataPrice[$item['sn']]['check_balance'] = $arrDataPrice[$item['sn']]['check_balance'] + ($item['pay_money']*-1);

        }
        
        foreach($data as $item){

            if($currentRef == $item['multicash_tr_id']){
                $count++;
            }else{
                $count = 0;
            }

            $currentRef = $item['multicash_tr_id'];

            $lackOfMoney = '';

            if($item['lack_of_money']){
                $lackOfMoney = $this->convert2Decimal($item['lack_of_money']*-1);
            }

            $paymentSurplus = '';

            if($item['payment_surplus']){
                $paymentSurplus = $this->convert2Decimal($item['payment_surplus']*-1);
            }

            $payBankTransfer = '';

            if($item['pay_banktransfer']){
                $payBankTransfer = $this->convert2Decimal($item['pay_banktransfer']*-1);
            }

            $paymentETC = '';

            if($item['payment_etc']){
                $paymentETC = $this->convert2Decimal($item['payment_etc'])*-1;
            }

            $paySerive = '';

            if($item['pay_service']){
                $paySerive = $this->convert2Decimal($item['pay_service']*-1);
            }

            $outStandingFinal = '-';

            if($count == 0){

                // $totalPay = $this->convert2Decimal($item['total_money_transfer'])+$this->convert2Decimal($item['lack_of_money'])+$this->convert2Decimal($item['payment_surplus'])+$this->convert2Decimal($item['pay_banktransfer'])+$this->convert2Decimal($item['payment_etc'])+$this->convert2Decimal($item['pay_service']);

                // $row = array();
                // $row[] = $item['d_id'];
                // $row[] = $item['d_name'];
                // $row[] = date("ymd", strtotime($item['pay_time']));
                // $row[] = '';
                // $row[] = '';
                // $row[] = '';
                // $row[] = $this->convert2Decimal($totalPay);
                // $row[] = "'".$currentRef;
                // $row[] = $item['b_name'];
                // $row[] = date("ymd", strtotime($item['pay_time']));
                // $row[] = date("j-M-y", strtotime($item['pay_time']));
                // $row[] = $this->convert2Decimal($item['total_money_transfer'])*-1;
                // $row[] = $lackOfMoney;
                // $row[] = $paymentSurplus;
                // $row[] = $payBankTransfer;
                // $row[] = $paymentETC;
                // $row[] = $paySerive;

                // $sumTotal = $totalPay + (($item['total_money_transfer']*-1) + $lackOfMoney + $paymentSurplus + $payBankTransfer + $paymentETC + $paySerive);

                // if((int)$sumTotal != 0){
                //     $outStandingFinal = $this->convert2Decimal($sumTotal);
                // }

                // $row[] = $outStandingFinal;
            
                // fputcsv($output, $row);
                // unset($row);

                $outStandingFinal = '-';

                foreach ($arrCNCP[$currentRef] as $key) {
                    $row = array();
                    $row[] = $item['d_id'];
                    $row[] = $item['d_name'];
                    $row[] = date("ymd", strtotime($item['pay_time']));
                    $row[] = '';
                    $row[] = '';
                    $row[] = $key['creditnote_sn'];
                    $row[] = $this->convert2Decimal($key['use_discount']*-1);
                    $row[] = "'".$currentRef;
                    $row[] = $item['b_name'];
                    $row[] = date("ymd", strtotime($item['pay_time']));
                    $row[] = date("j-M-y", strtotime($item['pay_time']));
                    $row[] = $this->convert2Decimal($key['use_discount']);
                    // $row[] = '';
                    // $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = $outStandingFinal;
                
                    if($arrDataPrice[$item['sn']]['last'] == $item['id'] && (int)$arrDataPrice[$item['sn']]['check_balance'] <> 0){
                        fputcsv($output, $row);
                    }
                    unset($row);
                    unset($key);
                }
            }


            $row = array();
            $row[] = $item['d_id'];
            $row[] = $item['d_name'];
            $row[] = date("ymd", strtotime($item['pay_time']));

            if($arrDataPrice[$item['sn']]['first'] == $item['id']){
                $row[] = 'Original';
            }else{
                $row[] = 'NULL';
            }

            if($arrDataPrice[$item['sn']]['last'] == $item['id']){
                $row[] = 'Outstanding';
            }else{
                $row[] = 'NULL';
            }

            $row[] = $item['invoice_number'];

            $row[] = $this->convert2Decimal($arrDataPrice[$item['sn']]['balance']);

            $arrDataPrice[$item['sn']]['balance'] = $arrDataPrice[$item['sn']]['balance'] + ($item['pay_money']*-1);

            $row[] = "'".$currentRef;
            $row[] = $item['b_name'];
            $row[] = date("ymd", strtotime($item['pay_time']));
            $row[] = date("j-M-y", strtotime($item['pay_time']));

            if($count == 0){
                $row[] = $this->convert2Decimal(($item['pay_money']*-1)-$lackOfMoney-$paymentSurplus-$payBankTransfer-$paymentETC-$paySerive);

                // $row[] = $lackOfMoney;
                // $row[] = $paymentSurplus;
                $row[] = $payBankTransfer;
                $row[] = $paymentETC;
                $row[] = $paySerive;
            }else{
                $row[] = $this->convert2Decimal($item['pay_money'])*-1;

                // $row[] = '';
                // $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
            }

            if((int)$arrDataPrice[$item['sn']]['balance'] != 0){
                $outStandingFinal = $this->convert2Decimal($arrDataPrice[$item['sn']]['balance']);
            }

            $row[] = $outStandingFinal;
        
            if($arrDataPrice[$item['sn']]['last'] == $item['id'] && (int)$arrDataPrice[$item['sn']]['check_balance'] <> 0){
                fputcsv($output, $row);
            }
            unset($row);
            unset($item);

        }

        unset($data);
        unset($arrCNCP);
    }

    public function _getDataForExcelCashCreditCollectionBankDetail($data,$params)
    {

        if(isset($params['to_collection_time']) AND $params['to_collection_time']){
            $params['to_collection_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }

        if(isset($params['to_invoice_time']) AND $params['to_invoice_time']){
            $params['to_invoice_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }

        $db = Zend_Registry::get('db');
       // print_r($data);die;
        $arr_d_id=0;

        $where_paytime = '';
        $where_invoicetime = '';
        $where_d_id = '';
        $where_sn = '';
        $where_no_show_brandshop = '';
        $where_bank = '';
        $where_finance_group = '';

        $where_invoicetime_newsn = '';

        if($arr_d_id == 0){

            if(isset($params['from_collection_time']) AND $params['from_collection_time'])
            {
                $pay_time_st = $params['from_collection_time'];
                $where_paytime .= " AND (ck.pay_time >= '".$pay_time_st."') ";
            }

            if(isset($params['to_collection_time']) AND $params['to_collection_time'])
            {
                $pay_time_en = $params['to_collection_time'];
                $where_paytime .= " AND (ck.pay_time <= '".$pay_time_en."') ";
            }

            if(isset($params['from_invoice_time']) AND $params['from_invoice_time'])
            {
                $invoice_time_st = $params['from_invoice_time'];
                $where_invoicetime .= " AND (m.invoice_time >= '".$invoice_time_st."') ";

                $where_invoicetime_newsn .= " AND (ck.create_at >= '".$invoice_time_st."') "; 
            }

            if(isset($params['to_invoice_time']) AND $params['to_invoice_time'])
            {
                $invoice_time_en = $params['to_invoice_time'];
                $where_invoicetime .= " AND (m.invoice_time <= '".$invoice_time_en."') ";

                $where_invoicetime_newsn .= " AND (ck.create_at <= '".$invoice_time_en."') ";
            }

            // $params['d_id'] = 33925;//33925,1076

            if(isset($params['d_id']) AND $params['d_id'])
            {
                $d_id = $params['d_id'];
                $where_d_id=" AND (cnit.d_id = '".$d_id."') ";
            }
  
            if(isset($params['sn']) AND $params['sn'])
            {
                $sn = $params['sn'];
                $where_sn=" AND (ck.sn = '".$sn."') or (ck.sn =(SELECT sn FROM market WHERE sn_ref='".$sn."' GROUP BY sn)) ";
            }

            if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                $where_no_show_brandshop = " AND m.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or m.warehouse_id in (71,92)";
                // allow warehouse WMDGC - คลังเคลมDigital, Brandshop Warehouse at Kerry
            }

            if(isset($params['bank']) && $params['bank']){
                if(is_array($params['bank'])){
                    $where_bank = " AND ck.bank in (" . implode(",",$params['bank']) . ")";
                }else{
                    $where_bank = " AND ck.bank = " . $params['bank'];
                }
            }

            if(isset($params['finance_group']) && $params['finance_group']){
                if(is_array($params['finance_group'])){
                    $where_finance_group = " AND d.finance_group in (" . implode(",",$params['finance_group']) . ")";
                }else{
                    $where_finance_group = " AND d.finance_group = '" . $params['finance_group']."'";
                }
            }

            $sql_result = "select ck.*,m.invoice_number,m.sn_ref,d.title as d_name,b.name as b_name,
            (select sub.pay_money from checkmoney sub where sub.sn = ck.sn and sub.type = 2) as main_price
            from credit_note_invoice_tr cnit
            left join checkmoney ck on ck.credit_tr_id=cnit.credit_tr_id
            left join market m on m.sn=ck.sn
            left join distributor d on d.id=ck.d_id
            left join bank b on b.id=ck.bank
            where 1=1 and ck.type in(1,2) ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_no_show_brandshop.$where_bank.$where_finance_group."
            group by ck.id
            ";

            $sql_result_addnewsn = "select ck.*,nm.sn_ref as invoice_number,m.sn_ref,d.title as d_name,b.name as b_name,
            (select sub.pay_money from checkmoney sub where sub.sn = ck.sn and sub.type = 4) as main_price
            from credit_note_invoice_tr cnit
            left join checkmoney ck on ck.credit_tr_id=cnit.credit_tr_id
            left join market m on m.sn=ck.sn
            left join distributor d on d.id=ck.d_id
            left join bank b on b.id=ck.bank
            left join newsn_manual nm ON nm.sn = ck.sn
            where 1=1 and ck.type in(3,4) ".$where_paytime.$where_invoicetime_newsn.$where_d_id.$where_bank.$where_finance_group."
            group by ck.id
            ";

            try {
                $result_ch = $db->fetchAll($sql_result);  
                
                $result_ch_addnewsn = $db->fetchAll($sql_result_addnewsn);

                $mergeData = array_merge($result_ch,$result_ch_addnewsn);

                $tempRef = array();
                foreach ($mergeData as $key) {
                    array_push($tempRef, $key['credit_tr_id']);
                }

                $tempRefNoSame = array_values(array_unique($tempRef));

                sort($tempRefNoSame);

                $getArrayData = array();

                foreach ($tempRefNoSame as $key_main) {
                    
                    foreach ($mergeData as $key_sub) {
                        
                        if($key_main == $key_sub['credit_tr_id']){
                            array_push($getArrayData, $key_sub);
                        }
                    }
                }

                $QCNCT = new Application_Model_CreditNoteCreditTr();

                $getCreditTrId = [];

                foreach ($getArrayData as $key) {
                    array_push($getCreditTrId, $key['credit_tr_id']);
                }

                // print_r($getCreditTrId);die;

                $getUniqueCreditTrId = array_values(array_unique($getCreditTrId));

                // print_r($getUniqueCreditTrId);die;

                $getCNCP = $QCNCT->getCheckMoneyCreditCNCPExcel($getUniqueCreditTrId);


                // print_r($getCNCP);

                $arrCNCP = [];

                foreach ($getUniqueCreditTrId as $key) {
                    
                    $arrCNCP[$key] = [];

                    foreach ($getCNCP as $item) {
                        
                        if($key == $item['sales_order']){
                            array_push($arrCNCP[$key], $item);
                        }

                    }
                }

                // print_r($arrCNCP);die;

                // print_r($result_ch);exit(); 
            } catch (Exception $e) {
                print_r($e);
                echo 'error';
                exit;
            }
            //print_r($result_ch);die;
        }

        //print_r($result_ch);die;
        //$this->_exportExcelExportCashCollectionCSV($result_ch);
        $this->_exportExcelExportCashCollectionCreditBankDetailExcel($getArrayData, $arrCNCP);
    }

    public function _exportExcelExportCashCollectionCreditBankDetailExcel($data, $arrCNCP = []){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'AR_Bank_Detail_'.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $banks = array();
        $period = array();
        $blance = array();
        $heads = array('Receive Period');

        $count = 0;

        foreach ($data as $key) {

            if($currentRef == $key['credit_tr_id']){
                $count++;
            }else{
                $count = 0;
            }

            $currentRef = $key['credit_tr_id'];

            $lackOfMoney = '';

            if($key['lack_of_money']){
                $lackOfMoney = $this->convert2Decimal($key['lack_of_money']*-1);
            }

            $paymentSurplus = '';

            if($key['payment_surplus']){
                $paymentSurplus = $this->convert2Decimal($key['payment_surplus']*-1);
            }

            $payBankTransfer = '';

            if($key['pay_banktransfer']){
                $payBankTransfer = $this->convert2Decimal($key['pay_banktransfer']*-1);
            }

            $paymentETC = '';

            if($key['payment_etc']){
                $paymentETC = $this->convert2Decimal($key['payment_etc'])*-1;
            }

            $paySerive = '';

            if($key['pay_service']){
                $paySerive = $this->convert2Decimal($key['pay_service']*-1);
            }

            if(!array_key_exists($key['b_name'], $banks)){
                $banks[$key['b_name']] = ['name' => $key['b_name'],'total' => 0];
                array_push($heads, $key['b_name']);
            }

            if(!array_key_exists(date("ymd", strtotime($key['pay_time'])), $period)){
                $period[date("ymd", strtotime($key['pay_time']))] = ['name' => date("ymd", strtotime($key['pay_time']))];
            }

            if($count == 0){

                if(isset($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']])){

                    $blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']] = ($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']]) + (($key['pay_money']*-1)-$lackOfMoney-$paymentSurplus-$payBankTransfer-$paymentETC-$paySerive);

                }else{

                $blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']] = ($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']])+(($key['pay_money']*-1)-$lackOfMoney-$paymentSurplus-$payBankTransfer-$paymentETC-$paySerive);

                }

                foreach ($arrCNCP[$currentRef] as $key) {

                    $blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']] = ($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']])+ ($key['use_discount']);
                }

            }else{

                if(isset($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']])){

                    $blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']] = ($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']]) + (($key['pay_money'])*-1);

                }else{

                    $blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']] = ($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']]) + (($key['pay_money'])*-1);

                }
            }


        }

        array_push($heads, 'Grand Total');

        fputcsv($output, $heads);

        $sumTotalMain = 0;

        foreach ($period as $key) {
            $row[] = $key['name'];
            $sumTotalSub = 0;
            foreach ($banks as $key_sub) {
                if(isset($blance[$key['name']][$key_sub['name']])){
                    $row[] = $blance[$key['name']][$key_sub['name']];
                    $sumTotalSub = $sumTotalSub+$blance[$key['name']][$key_sub['name']];
                    $banks[$key_sub['name']]['total'] = $banks[$key_sub['name']]['total']+$blance[$key['name']][$key_sub['name']];
                }else{
                    $row[] = 0.00;
                }
            }
            $row[] = $sumTotalSub;

            $sumTotalMain = $sumTotalMain + $sumTotalSub;

            fputcsv($output, $row);
            unset($row);
            unset($key);
            unset($key_sub);
        }

        $row[] = 'Grand Total';
        foreach ($banks as $key) {
            $row[] = $key['total'];
        }
        $row[] = $sumTotalMain;

        fputcsv($output, $row);
        unset($row);
        unset($key);

        unset($banks);
        unset($period);
        unset($blance);
    }

    public function _getDataForExcelMultiCashCollectionBankDetail($data,$params)
    {

        if(isset($params['to_collection_time']) AND $params['to_collection_time']){
            $params['to_collection_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }

        if(isset($params['to_invoice_time']) AND $params['to_invoice_time']){
            $params['to_invoice_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
        }

        $db = Zend_Registry::get('db');
       // print_r($data);die;
        $arr_d_id=0;

        $where_paytime = '';
        $where_invoicetime = '';
        $where_d_id = '';
        $where_sn = '';
        $where_no_show_brandshop = '';
        $where_bank = '';

        $where_invoicetime_newsn = '';

        if($arr_d_id == 0){

            if(isset($params['from_collection_time']) AND $params['from_collection_time'])
            {
                $pay_time_st = $params['from_collection_time'];
                $where_paytime .= " AND (ck.pay_time >= '".$pay_time_st."') ";
            }

            if(isset($params['to_collection_time']) AND $params['to_collection_time'])
            {
                $pay_time_en = $params['to_collection_time'];
                $where_paytime .= " AND (ck.pay_time <= '".$pay_time_en."') ";
            }

            if(isset($params['from_invoice_time']) AND $params['from_invoice_time'])
            {
                $invoice_time_st = $params['from_invoice_time'];
                $where_invoicetime .= " AND (m.invoice_time >= '".$invoice_time_st."') ";

                $where_invoicetime_newsn .= " AND (ck.create_at >= '".$invoice_time_st."') "; 
            }

            if(isset($params['to_invoice_time']) AND $params['to_invoice_time'])
            {
                $invoice_time_en = $params['to_invoice_time'];
                $where_invoicetime .= " AND (m.invoice_time <= '".$invoice_time_en."') ";

                $where_invoicetime_newsn .= " AND (ck.create_at <= '".$invoice_time_en."') ";
            }

            // $params['d_id'] = 33925;//33925,1076

            if(isset($params['d_id']) AND $params['d_id'])
            {
                $d_id = $params['d_id'];
                $where_d_id=" AND (cnit.d_id = '".$d_id."') ";
            }
  
            if(isset($params['sn']) AND $params['sn'])
            {
                $sn = $params['sn'];
                $where_sn=" AND (ck.sn = '".$sn."') or (ck.sn =(SELECT sn FROM market WHERE sn_ref='".$sn."' GROUP BY sn)) ";
            }

            if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                $where_no_show_brandshop = " AND m.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or m.warehouse_id in (71,92)";
                // allow warehouse WMDGC - คลังเคลมDigital, Brandshop Warehouse at Kerry
            }

            if(isset($params['bank']) && $params['bank']){
                if(is_array($params['bank'])){
                    $where_bank = " AND ck.bank in (" . implode(",",$params['bank']) . ")";
                }else{
                    $where_bank = " AND ck.bank = " . $params['bank'];
                }
            }

            $sql_result = "select ck.*,m.invoice_number,m.sn_ref,d.title as d_name,b.name as b_name,
            (select sub.pay_money from checkmoney sub where sub.sn = ck.sn and sub.type = 2) as main_price
            from credit_note_invoice_tr cnit
            left join checkmoney ck on ck.multicash_tr_id=cnit.multicash_tr_id
            left join market m on m.sn=ck.sn
            left join distributor d on d.id=ck.d_id
            left join bank b on b.id=ck.bank
            where 1=1 and ck.type in(1,2) ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_no_show_brandshop.$where_bank."
            group by ck.id
            ";

            // $sql_result_addnewsn = "select ck.*,nm.sn_ref as invoice_number,m.sn_ref,d.title as d_name,b.name as b_name,
            // (select sub.pay_money from checkmoney sub where sub.sn = ck.sn and sub.type = 4) as main_price
            // from credit_note_invoice_tr cnit
            // left join checkmoney ck on ck.multicash_tr_id=cnit.multicash_tr_id
            // left join market m on m.sn=ck.sn
            // left join distributor d on d.id=ck.d_id
            // left join bank b on b.id=ck.bank
            // left join newsn_manual nm ON nm.sn = ck.sn
            // where 1=1 and ck.type in(3,4) ".$where_paytime.$where_invoicetime_newsn.$where_d_id.$where_bank."
            // group by ck.id
            // ";

            try {
                $result_ch = $db->fetchAll($sql_result);  
                
                // $result_ch_addnewsn = $db->fetchAll($sql_result_addnewsn);
                $result_ch_addnewsn = array();

                $mergeData = array_merge($result_ch,$result_ch_addnewsn);

                $tempRef = array();
                foreach ($mergeData as $key) {
                    array_push($tempRef, $key['multicash_tr_id']);
                }

                $tempRefNoSame = array_values(array_unique($tempRef));

                sort($tempRefNoSame);

                $getArrayData = array();

                foreach ($tempRefNoSame as $key_main) {
                    
                    foreach ($mergeData as $key_sub) {
                        
                        if($key_main == $key_sub['multicash_tr_id']){
                            array_push($getArrayData, $key_sub);
                        }
                    }
                }

                $QCNCT = new Application_Model_CreditNoteCreditTr();

                $getCreditTrId = [];

                foreach ($getArrayData as $key) {
                    array_push($getCreditTrId, $key['multicash_tr_id']);
                }

                // print_r($getCreditTrId);die;

                $getUniqueCreditTrId = array_values(array_unique($getCreditTrId));

                // print_r($getUniqueCreditTrId);die;

                $getCNCP = $QCNCT->getCheckMoneyCreditCNCPExcel($getUniqueCreditTrId);


                // print_r($getCNCP);

                $arrCNCP = [];

                foreach ($getUniqueCreditTrId as $key) {
                    
                    $arrCNCP[$key] = [];

                    foreach ($getCNCP as $item) {
                        
                        if($key == $item['sales_order']){
                            array_push($arrCNCP[$key], $item);
                        }

                    }
                }

                // print_r($arrCNCP);die;

                // print_r($result_ch);exit(); 
            } catch (Exception $e) {
                print_r($e);
                echo 'error';
                exit;
            }
            //print_r($result_ch);die;
        }

        //print_r($result_ch);die;
        //$this->_exportExcelExportCashCollectionCSV($result_ch);
        $this->_exportExcelExportMultiCashCollectionBankDetailExcel($getArrayData, $arrCNCP);
    }

    public function _exportExcelExportMultiCashCollectionBankDetailExcel($data, $arrCNCP = []){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $filename = 'AR_Bank_Detail_'.date('d-m-Y H-i-s');
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        $banks = array();
        $period = array();
        $blance = array();
        $heads = array('Receive Period');

        $count = 0;

        foreach ($data as $key) {

            if($currentRef == $key['multicash_tr_id']){
                $count++;
            }else{
                $count = 0;
            }

            $currentRef = $key['multicash_tr_id'];

            $lackOfMoney = '';

            if($key['lack_of_money']){
                $lackOfMoney = $this->convert2Decimal($key['lack_of_money']*-1);
            }

            $paymentSurplus = '';

            if($key['payment_surplus']){
                $paymentSurplus = $this->convert2Decimal($key['payment_surplus']*-1);
            }

            $payBankTransfer = '';

            if($key['pay_banktransfer']){
                $payBankTransfer = $this->convert2Decimal($key['pay_banktransfer']*-1);
            }

            $paymentETC = '';

            if($key['payment_etc']){
                $paymentETC = $this->convert2Decimal($key['payment_etc'])*-1;
            }

            $paySerive = '';

            if($key['pay_service']){
                $paySerive = $this->convert2Decimal($key['pay_service']*-1);
            }

            if(!array_key_exists($key['b_name'], $banks)){
                $banks[$key['b_name']] = ['name' => $key['b_name'],'total' => 0];
                array_push($heads, $key['b_name']);
            }

            if(!array_key_exists(date("ymd", strtotime($key['pay_time'])), $period)){
                $period[date("ymd", strtotime($key['pay_time']))] = ['name' => date("ymd", strtotime($key['pay_time']))];
            }

            if($count == 0){

                if(isset($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']])){

                    $blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']] = ($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']]) + (($key['pay_money']*-1)-$lackOfMoney-$paymentSurplus-$payBankTransfer-$paymentETC-$paySerive);


                }else{

                $blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']] = ($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']])+(($key['pay_money']*-1)-$lackOfMoney-$paymentSurplus-$payBankTransfer-$paymentETC-$paySerive);

                }

                foreach ($arrCNCP[$currentRef] as $key) {

                    $blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']] = ($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']])+ ($key['use_discount']);
                }

            }else{

                if(isset($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']])){

                    $blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']] = ($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']]) + (($key['pay_money'])*-1);

                }else{

                    $blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']] = ($blance[date("ymd", strtotime($key['pay_time']))][$key['b_name']]) + (($key['pay_money'])*-1);

                }
            }


        }

        array_push($heads, 'Grand Total');

        fputcsv($output, $heads);

        $sumTotalMain = 0;

        foreach ($period as $key) {
            $row[] = $key['name'];
            $sumTotalSub = 0;
            foreach ($banks as $key_sub) {
                if(isset($blance[$key['name']][$key_sub['name']])){
                    $row[] = $blance[$key['name']][$key_sub['name']];
                    $sumTotalSub = $sumTotalSub+$blance[$key['name']][$key_sub['name']];
                    $banks[$key_sub['name']]['total'] = $banks[$key_sub['name']]['total']+$blance[$key['name']][$key_sub['name']];
                }else{
                    $row[] = 0.00;
                }
            }
            $row[] = $sumTotalSub;

            $sumTotalMain = $sumTotalMain + $sumTotalSub;

            fputcsv($output, $row);
            unset($row);
            unset($key);
            unset($key_sub);
        }

        $row[] = 'Grand Total';
        foreach ($banks as $key) {
            $row[] = $key['total'];
        }
        $row[] = $sumTotalMain;

        fputcsv($output, $row);
        unset($row);
        unset($key);

        unset($banks);
        unset($period);
        unset($blance);
    }

      public function _exportExcelExportCashCollectionCSV($data)
      {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        //print_r($data);die;
        //$db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 1);
        ini_set('memory_limit', '400M');
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
            mkdir($file_path, 7777, true);
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
            'PAY NOTE',
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
                $float = floatval($string);

                if($t['output'] <= 0)
                {
                    $money = $t['output'] * -1;  
                }
                
                $money_check = $t['output'];
                
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

                $title = isset($distributors_cached[$t['d_id']]) ? $distributors_cached[$t['d_id']] : '';
                $store_code = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

               if($t['note'] !='Discount 1 %'){

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
                    
                    
                    $row[] = $money;
                    $row[] = $t['pay_time'];
                    

                    $row[] = $t['bank_transaction_code'];
                    
                    $row[] = $t['content'];
                    $row[] = $t['note'];
                    $row[] = $t['pay_text'];
                    $row[] = $t['balance'];
                }

                if(floatval($money_check) > 0 && ($t['note'] =='Discount 1 %')){
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
                    
                    
                    $row[] = $money;
                    $row[] = $t['pay_time'];
                    

                    $row[] = $t['bank_transaction_code'];
                    
                    $row[] = $t['content'];
                    $row[] = $t['note'];
                    $row[] = $t['pay_text'];
                    $row[] = $t['balance'];
                }
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
                ->joinleft(array('m'=>'market'),'m.sn = ch.sn',array('m.invoice_number','m.sn_ref','m.pay_text'))
                ->group(array('ch.id','m.sn'))
                ->order('ch.pay_time desc')
                ->where('ch.d_id IN (?)',$arr_d_id);
                ;

            if( isset($params['bank']) AND $params['bank']  ){
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
            'PAYMENT REMARK',
            'BALANCE',
            'OPERATION CAMPAIGN',
            'PHONE NUMBER'
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
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_text'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $item['balance'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bs_campaign'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['phone_number_sn'] , PHPExcel_Cell_DataType::TYPE_STRING);
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
             // echo "<pre>";
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
                    $pay_ment                        = $this->getRequest()->getParam('pay_ment',0);//-----
                    $lack_of_money                  = $this->getRequest()->getParam('lack_of_money',0);//-----
                    $pay_money                      = $this->decimal_remove_comma($this->getRequest()->getParam('pay_money',0));
                    $payment_order                  = $this->getRequest()->getParam('payment_order', 0);
                    $bank_transaction_code          = $this->getRequest()->getParam('bank_transaction_code', 0);
                    $payment_service                = $this->getRequest()->getParam('payment_service', 0);
                    $payment_servicecharge          = $this->getRequest()->getParam('payment_servicecharge', 0);
                    $pay_time                       = $this->getRequest()->getParam('pay_time');//-----
                    $bank                           = $this->getRequest()->getParam('select_bank_id', NULL);//-----
                    $type                           = 2;
                    $price_use_discount_creditnote  = $this->getRequest()->getParam('price_use_discount_creditnote',0);
                    $price_balance_discount_creditnote  = $this->getRequest()->getParam('price_balance_discount_creditnote',0);
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
                            
                            // echo "<br/> i = ".$i;$grade = str_replace(',', '.', $grade);
                            $payment_order_val          =str_replace(',', '', $payment_order[$i]);
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

    public function paymentSlipCreditAction(){
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
             // echo "<pre>";
                // print_r($_POST);die;
               
                // print_r($_FILES); die;
                //$file_name_show = $_FILES['file']['name'];
                //print_r($file_name_show);
                //die;
                $userStorage      = Zend_Auth::getInstance()->getStorage()->read();
                $db->beginTransaction();
                try {  

                    $payment                        = $this->getRequest()->getParam('payment');//-----
                    $pay_ment                       = $this->getRequest()->getParam('pay_ment',0);//-----
                    $lack_of_money                  = $this->getRequest()->getParam('lack_of_money',0);//-----
                    $pay_money                      = $this->decimal_remove_comma($this->getRequest()->getParam('pay_money',0));
                    $payment_order                  = $this->getRequest()->getParam('payment_order', 0);
                    $bank_transaction_code          = $this->getRequest()->getParam('bank_transaction_code', 0);
                    $payment_service                = $this->getRequest()->getParam('payment_service', 0);
                    $payment_banktransfer           = $this->getRequest()->getParam('payment_banktransfer', 0);
                    $payment_servicecharge          = $this->getRequest()->getParam('payment_servicecharge', 0);
                    $payment_parts_and_service      = $this->getRequest()->getParam('payment_parts_and_service', 0);
                    $pay_time                       = $this->getRequest()->getParam('pay_time');//-----
                    $bank                           = $this->getRequest()->getParam('select_bank_id', NULL);//-----
                    $type                           = 2;
                    $price_use_discount_creditnote  = $this->getRequest()->getParam('price_use_discount_creditnote',0);
                    $price_balance_discount_creditnote  = $this->getRequest()->getParam('price_balance_discount_creditnote',0);
                    $note_new                       = $this->getRequest()->getParam('remark', NULL);
                    $ids_discount_creditnote        = $this->getRequest()->getParam('ids_discount_creditnote', NULL);
                    $cn                             = $this->getRequest()->getParam('cn', NULL);
                    $pay_cn                         = $this->getRequest()->getParam('pay_cn', NULL);
                    $payment_etc                    = $this->getRequest()->getParam('payment_etc', 0);
                    $payment_surplus                = $this->getRequest()->getParam('payment_surplus', 0);
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
                    $QCheckMoney            = new Application_Model_Checkmoney();
                    $QStoreaccount          = new Application_Model_Storeaccount();
                    $QCreditNoteTran        = new Application_Model_CreditNoteTran();
                    $QCreditNoteCreditTr    = new Application_Model_CreditNoteCreditTr();
                    $QCreditNoteInvoiceTr   = new Application_Model_CreditNoteInvoiceTr();
                    $QNewSnManual           = new Application_Model_NewsnManual();
                    /* ---------Add Money Check--------------- */
                    // print_r($QCreditNoteCreditTr->fetchAll());
                        $payment_order_val=0;$payment_bank_transfer_val=0;$i=0;
                        $payment_service_val=0;$payment_servicecharge_val=0;
                        $credit_tr_id = date('YmdHis') . substr(microtime(), 2, 4);
                        for($i=0;$i<count($pay_ment);$i++){
                            
                            // echo "<br/> i = ".$i;
                           
                            // $note_new                   =$note_new;
                            $date                       = date('Y-m-d H:i:s');

                            if($payment_order[$i] !=''){
                                $payment_order_val          =str_replace(',', '', $payment_order[$i]);
                            }else{
                                $payment_order_val = 0;
                            }

                            if($payment_banktransfer !=''){
                                $payment_bank_transfer_val  =str_replace(',', '', $payment_banktransfer);
                            }else{
                                $payment_bank_transfer_val = 0;
                            }

                            if($payment_service !=''){
                                $payment_service_val        =str_replace(',', '', $payment_service);
                            }else{
                                $payment_service_val = 0;
                            }

                            if($payment_servicecharge !=''){
                                $payment_servicecharge_val  =str_replace(',', '', $payment_servicecharge);
                            }else{
                                $payment_servicecharge_val = 0;
                            }

                            if($lack_of_money !=''){
                                $lack_of_money_val          =str_replace(',', '', $lack_of_money);
                            }else{
                                $lack_of_money_val = 0;
                            }

                            if($pay_ment !=''){
                                $pay_ment_val          =str_replace(',', '', $pay_ment[$i]);
                            }else{
                                $pay_ment_val = 0;
                            }
                            
                            if($payment_etc !=''){
                                $payment_etc_val          =str_replace(',', '', $payment_etc);
                            }else{
                                $payment_etc_val = 0;
                            }

                            if($payment_surplus !=''){
                                $payment_surplus_val          =str_replace(',', '', $payment_surplus);
                            }else{
                                $payment_surplus_val = 0;
                            }

                            //$pay_money = $payment_order+$payment_bank_transfer;
                            $file_name_upload = '/pay_slips/'.$sn[0].'/'.$_FILES['file']['name'];
                            
                            $note_new='PayMoney='.number_format($pay_ment_val,2) .' Fee transfer='.number_format($payment_bank_transfer_val,2).' Service Charge='.number_format($payment_servicecharge,2).' ค่าอะไหล่และค่าบริการ='.number_format($payment_service,2).'ค่าบริการอื่นๆ='.number_format($payment_etc,2);

                            $data = array(
                                    'd_id'                  => $d_id,
                                    'bank'                  => $bank,
                                    'pay_money'             => $pay_ment_val,
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
                                    'pay_banktransfer'      => $payment_bank_transfer_val,
                                    'pay_service'           => $payment_service_val,
                                    // 'pay_servicecharge'     => $payment_servicecharge,
                                    'payment_etc'           => $payment_etc_val,
                                    'credit_tr_id'          => $credit_tr_id,
                                    'payment_surplus'       => $payment_surplus_val
                                    
                            );

                            $where = $QNewSnManual->getAdapter()->quoteInto('sn = ?', trim($sn[$i]));
                            $getNewSN = $QNewSnManual->fetchRow($where);

                            if ($getNewSN){
                                $data['type'] = 3;
                            }
                            // echo "<pre>";
                            // print_r($data);
                            $file_name_upload = $_FILES['file']['name'];
                            // die;
                            if($ch_id){
                                $old_checkmoney = $QCheckMoney->find($ch_id)->current();
                                $data['user_id'] = $userStorage->id;
                                $data['updated_at'] = $date;
                                $where = $db->quoteInto('id = ?',$ch_id);
                                $QCheckMoney->update($data,$where); 
                                
                            }else{
                           // print_r($data);
                               $QCheckMoney->insert($data);
                              
                            }
                           
                           
                         }
                          
                          if (isset($cn) and $cn ) {
                             
                            for ($i=0; $i < count($cn); $i++) { 
                                $cn_data = array(
                                            'creditnote_sn'     => $cn[$i],   
                                            'distributor_id'    => $d_id,
                                            'sales_order'       => $credit_tr_id,  
                                            'use_discount'      => $pay_cn[$i],
                                            
                                            'update_by'         => $userStorage->id,
                                            'update_date'       => date('Y-m-d H:i:s'),
                                            'creditnote_type'   => 'CC'
                                        );
                                      
                                       $QCreditNoteCreditTr->insert($cn_data);
                                       $QCreditNoteTran->insert($cn_data);
                               //  print_r($cn_data);
                                }
                            }

                            $credit_invoice = array(
                                            'credit_tr_id'      => $credit_tr_id,   
                                            'd_id'              => $d_id,
                                            
                                            'created_by'         => $userStorage->id,
                                            'created_date'       => date('Y-m-d H:i:s'),

                                        );
                                      
                                        $QCreditNoteInvoiceTr->insert($credit_invoice);
                           // print_r($credit_invoice)  ;

                                 
                               
                        // if ($price_use_discount_creditnote and $ids_discount_creditnote) {
                        //      $array_1 = array('price_use_discount_creditnote' => $price_use_discount_creditnote);
                        //      $array_2 = array('ids_discount_creditnote' => $ids_discount_creditnote);
                            
                           
                        //     $temp = $array_1 + $array_2;
                          
                        //      foreach($temp as $key => $item) { 
                        //         foreach($item as $k => $v) {
                        //             $arr[] = $k; 
                        //         }
                        //     }
                        //     $arr = array_unique($arr);
                            
                        //     foreach($temp as $key => $item) { $arr2[] = $key;  }

                        //     for ($i=0;$i<count($arr);$i++) {
                        //         for ($j=0;$j<count($arr2);$j++) {
                        //             $data2[ $arr[$i] ][$arr2[$j]] = $temp[ $arr2[$j] ][ $arr[$i] ];
                        //         }
                        //     }
                          
                        //     foreach($data2 as $key => $item) { 
                        //         foreach ($item as $key2 => $value2) {}
                        //         for ($j=0; $j < count($value2); $j++) { 
                                   
                        //             $cn_data = array(   
                        //                 'distributor_id'    => $d_id,
                        //                 'sales_order'       => $key,  
                        //                 'use_discount'      => $item['price_use_discount_creditnote'][$j],
                        //                 'creditnote_sn'     => $item['ids_discount_creditnote'][$j],
                        //                 'update_by'         => $userStorage->id,
                        //                 'update_date'       => date('Y-m-d H:i:s'),
                        //                 'creditnote_type'   => substr($item['ids_discount_creditnote'][$j], 0, 2)
                        //             );
                                  
                        //             $QCreditNoteTran->insert($cn_data);
                        //         }
                                    
                        //     }
                        // }    [0] => 201604011513294053
                             // [1] => 201604011520048272
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
// die; 
                    $db->commit();
                       
                    $flashMessenger->setNamespace('success')->addMessage('Done!');
                    $this->_redirect('/checkmoney/credit-list');
                }
                catch (exception $e) {
                    $db->rollback();
                    $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!' .$e->getMessage());die($e);
                    $this->_redirect('/checkmoney/payment-slip-credit?d_id=' . $d_id);
                }
                $flashMessenger->setNamespace('success')->addMessage('Done!');
                $this->_redirect('/checkmoney/credit-list');

            } 

        $QCreditNote = new Application_Model_CreditNote();
        $CreditNote = $QCreditNote->getCredit_Note_PaySlip($d_id,NULL);

        $QCheckmoney = new Application_Model_Checkmoney();
        $list = $QCheckmoney->getSoPaymentslip($d_id);

        $QNSM = new Application_Model_NewsnManual();
        $newSOManual = $QNSM->getNewSOManual($d_id);
        $newCNCPManual = $QNSM->getNewCNCPManual($d_id);

        $this->view->newSOManual = $newSOManual;
        $this->view->newCNCPManual = $newCNCPManual;
       
        $QDistributor = new Application_Model_Distributor();

        $distributors_cached = $QDistributor->get_cache();
        $this->view->distributors_cached = $distributors_cached;

        $this->view->distributor_name = $distributors_cached[$d_id];

        $QStaff = new Application_Model_Staff();
        $this->view->staffs_cached = $QStaff->get_cache();

        $QBank = new Application_Model_Bank();
        $this->view->banks = $QBank->fetchAll();

        $this->view->credits_note       = $CreditNote['data'];
        $this->view->list               = $list;
        $this->view->limit              = $limit;
        $this->view->total              = $total;
        $this->view->page               = $page;
        $this->view->offset             = $limit*($page-1);
        // $this->view->params             = $params;
        $this->view->sort               = $sort;
        $this->view->desc               = $desc;
        $this->view->url                = HOST.'checkmoney/credit-list/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->messages_success   = $messages_success;
        $this->view->messages           = $messages;
    }

    public function paymentSlipMulticashAction(){
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
             // echo "<pre>";
                // print_r($_POST);die;
               
                // print_r($_FILES); die;
                //$file_name_show = $_FILES['file']['name'];
                //print_r($file_name_show);
                //die;
                $userStorage      = Zend_Auth::getInstance()->getStorage()->read();
                $db->beginTransaction();
                try {  

                    $payment                        = $this->getRequest()->getParam('payment');//-----
                    $pay_ment                       = $this->getRequest()->getParam('pay_ment',0);//-----
                    $lack_of_money                  = $this->getRequest()->getParam('lack_of_money',0);//-----
                    $pay_money                      = $this->decimal_remove_comma($this->getRequest()->getParam('pay_money',0));
                    $payment_order                  = $this->getRequest()->getParam('payment_order', 0);
                    $bank_transaction_code          = $this->getRequest()->getParam('bank_transaction_code', 0);
                    $payment_service                = $this->getRequest()->getParam('payment_service', 0);
                    $payment_banktransfer           = $this->getRequest()->getParam('payment_banktransfer', 0);
                    $payment_servicecharge          = $this->getRequest()->getParam('payment_servicecharge', 0);
                    $payment_parts_and_service      = $this->getRequest()->getParam('payment_parts_and_service', 0);
                    $pay_time                       = $this->getRequest()->getParam('pay_time');//-----
                    $bank                           = $this->getRequest()->getParam('select_bank_id', NULL);//-----
                    $type                           = 2;
                    $price_use_discount_creditnote  = $this->getRequest()->getParam('price_use_discount_creditnote',0);
                    $price_balance_discount_creditnote  = $this->getRequest()->getParam('price_balance_discount_creditnote',0);
                    $note_new                       = $this->getRequest()->getParam('remark', NULL);
                    $ids_discount_creditnote        = $this->getRequest()->getParam('ids_discount_creditnote', NULL);
                    $cn                             = $this->getRequest()->getParam('cn', NULL);
                    $pay_cn                         = $this->getRequest()->getParam('pay_cn', NULL);
                    $payment_etc                    = $this->getRequest()->getParam('payment_etc', 0);
                    $payment_surplus                = $this->getRequest()->getParam('payment_surplus', 0);
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
                    $QCheckMoney            = new Application_Model_Checkmoney();
                    $QStoreaccount          = new Application_Model_Storeaccount();
                    $QCreditNoteTran        = new Application_Model_CreditNoteTran();
                    $QCreditNoteCreditTr    = new Application_Model_CreditNoteCreditTr();
                    $QCreditNoteInvoiceTr   = new Application_Model_CreditNoteInvoiceTr();
                    // $QNewSnManual           = new Application_Model_NewsnManual();
                    /* ---------Add Money Check--------------- */
                    // print_r($QCreditNoteCreditTr->fetchAll());
                        $payment_order_val=0;$payment_bank_transfer_val=0;$i=0;
                        $payment_service_val=0;$payment_servicecharge_val=0;
                        $multicash_tr_id = date('YmdHis') . substr(microtime(), 2, 4);
                        for($i=0;$i<count($pay_ment);$i++){
                            
                            // echo "<br/> i = ".$i;
                           
                            // $note_new                   =$note_new;
                            $date                       = date('Y-m-d H:i:s');

                            if($payment_order[$i] !=''){
                                $payment_order_val          =str_replace(',', '', $payment_order[$i]);
                            }else{
                                $payment_order_val = 0;
                            }

                            if($payment_banktransfer !=''){
                                $payment_bank_transfer_val  =str_replace(',', '', $payment_banktransfer);
                            }else{
                                $payment_bank_transfer_val = 0;
                            }

                            if($payment_service !=''){
                                $payment_service_val        =str_replace(',', '', $payment_service);
                            }else{
                                $payment_service_val = 0;
                            }

                            if($payment_servicecharge !=''){
                                $payment_servicecharge_val  =str_replace(',', '', $payment_servicecharge);
                            }else{
                                $payment_servicecharge_val = 0;
                            }

                            if($lack_of_money !=''){
                                $lack_of_money_val          =str_replace(',', '', $lack_of_money);
                            }else{
                                $lack_of_money_val = 0;
                            }

                            if($pay_ment !=''){
                                $pay_ment_val          =str_replace(',', '', $pay_ment[$i]);
                            }else{
                                $pay_ment_val = 0;
                            }
                            
                            if($payment_etc !=''){
                                $payment_etc_val          =str_replace(',', '', $payment_etc);
                            }else{
                                $payment_etc_val = 0;
                            }

                            if($payment_surplus !=''){
                                $payment_surplus_val          =str_replace(',', '', $payment_surplus);
                            }else{
                                $payment_surplus_val = 0;
                            }

                            //$pay_money = $payment_order+$payment_bank_transfer;
                            $file_name_upload = '/pay_slips/'.$sn[0].'/'.$_FILES['file']['name'];
                            
                            $note_new='PayMoney='.number_format($pay_ment_val,2) .' Fee transfer='.number_format($payment_bank_transfer_val,2).' Service Charge='.number_format($payment_servicecharge,2).' ค่าอะไหล่และค่าบริการ='.number_format($payment_service,2).'ค่าบริการอื่นๆ='.number_format($payment_etc,2);

                            $data = array(
                                    'd_id'                  => $d_id,
                                    'bank'                  => $bank,
                                    'pay_money'             => $pay_ment_val,
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
                                    'pay_banktransfer'      => $payment_bank_transfer_val,
                                    'pay_service'           => $payment_service_val,
                                    // 'pay_servicecharge'     => $payment_servicecharge,
                                    'payment_etc'           => $payment_etc_val,
                                    'multicash_tr_id'          => $multicash_tr_id,
                                    'payment_surplus'       => $payment_surplus_val
                                    
                            );

                            // $where = $QNewSnManual->getAdapter()->quoteInto('sn = ?', trim($sn[$i]));
                            // $getNewSN = $QNewSnManual->fetchRow($where);

                            // if ($getNewSN){
                            //     $data['type'] = 3;
                            // }
                            // echo "<pre>";
                            // print_r($data);
                            $file_name_upload = $_FILES['file']['name'];
                            // die;
                            if($ch_id){
                                $old_checkmoney = $QCheckMoney->find($ch_id)->current();
                                $data['user_id'] = $userStorage->id;
                                $data['updated_at'] = $date;
                                $where = $db->quoteInto('id = ?',$ch_id);
                                $QCheckMoney->update($data,$where); 
                                
                            }else{
                           // print_r($data);
                               $QCheckMoney->insert($data);
                              
                            }
                           
                           
                         }
                          
                          if (isset($cn) and $cn ) {
                             
                            for ($i=0; $i < count($cn); $i++) { 
                                $cn_data = array(
                                            'creditnote_sn'     => $cn[$i],   
                                            'distributor_id'    => $d_id,
                                            'sales_order'       => $credit_tr_id,  
                                            'use_discount'      => $pay_cn[$i],
                                            
                                            'update_by'         => $userStorage->id,
                                            'update_date'       => date('Y-m-d H:i:s'),
                                            'creditnote_type'   => 'CC'
                                        );
                                      
                                       $QCreditNoteCreditTr->insert($cn_data);
                                       $QCreditNoteTran->insert($cn_data);
                               //  print_r($cn_data);
                                }
                            }

                            $credit_invoice = array(
                                            'multicash_tr_id'      => $multicash_tr_id,   
                                            'd_id'              => $d_id,
                                            
                                            'created_by'         => $userStorage->id,
                                            'created_date'       => date('Y-m-d H:i:s'),

                                        );
                                      
                                        $QCreditNoteInvoiceTr->insert($credit_invoice);
                           // print_r($credit_invoice)  ;

                                 
                               
                        // if ($price_use_discount_creditnote and $ids_discount_creditnote) {
                        //      $array_1 = array('price_use_discount_creditnote' => $price_use_discount_creditnote);
                        //      $array_2 = array('ids_discount_creditnote' => $ids_discount_creditnote);
                            
                           
                        //     $temp = $array_1 + $array_2;
                          
                        //      foreach($temp as $key => $item) { 
                        //         foreach($item as $k => $v) {
                        //             $arr[] = $k; 
                        //         }
                        //     }
                        //     $arr = array_unique($arr);
                            
                        //     foreach($temp as $key => $item) { $arr2[] = $key;  }

                        //     for ($i=0;$i<count($arr);$i++) {
                        //         for ($j=0;$j<count($arr2);$j++) {
                        //             $data2[ $arr[$i] ][$arr2[$j]] = $temp[ $arr2[$j] ][ $arr[$i] ];
                        //         }
                        //     }
                          
                        //     foreach($data2 as $key => $item) { 
                        //         foreach ($item as $key2 => $value2) {}
                        //         for ($j=0; $j < count($value2); $j++) { 
                                   
                        //             $cn_data = array(   
                        //                 'distributor_id'    => $d_id,
                        //                 'sales_order'       => $key,  
                        //                 'use_discount'      => $item['price_use_discount_creditnote'][$j],
                        //                 'creditnote_sn'     => $item['ids_discount_creditnote'][$j],
                        //                 'update_by'         => $userStorage->id,
                        //                 'update_date'       => date('Y-m-d H:i:s'),
                        //                 'creditnote_type'   => substr($item['ids_discount_creditnote'][$j], 0, 2)
                        //             );
                                  
                        //             $QCreditNoteTran->insert($cn_data);
                        //         }
                                    
                        //     }
                        // }    [0] => 201604011513294053
                             // [1] => 201604011520048272
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
// die; 
                    $db->commit();
                       
                    $flashMessenger->setNamespace('success')->addMessage('Done!');
                    $this->_redirect('/checkmoney/multi-cash-list');
                }
                catch (exception $e) {
                    $db->rollback();
                    $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!' .$e->getMessage());die($e);
                    $this->_redirect('/checkmoney/payment-slip-multicash?d_id=' . $d_id);
                }
                $flashMessenger->setNamespace('success')->addMessage('Done!');
                $this->_redirect('/checkmoney/multi-cash-list');

            } 

        $QCreditNote = new Application_Model_CreditNote();
        $CreditNote = $QCreditNote->getCredit_Note_PaySlip($d_id,NULL);

        $QCheckmoney = new Application_Model_Checkmoney();
        $list = $QCheckmoney->getSoPaymentslip($d_id);

        $QNSM = new Application_Model_NewsnManual();
        $newSOManual = $QNSM->getNewSOManual($d_id);
        $newCNCPManual = $QNSM->getNewCNCPManual($d_id);

        $this->view->newSOManual = $newSOManual;
        $this->view->newCNCPManual = $newCNCPManual;
       
        $QDistributor = new Application_Model_Distributor();

        $distributors_cached = $QDistributor->get_cache();
        $this->view->distributors_cached = $distributors_cached;

        $this->view->distributor_name = $distributors_cached[$d_id];

        $QStaff = new Application_Model_Staff();
        $this->view->staffs_cached = $QStaff->get_cache();

        $QBank = new Application_Model_Bank();
        $this->view->banks = $QBank->fetchAll();

        $this->view->credits_note       = $CreditNote['data'];
        $this->view->list               = $list;
        $this->view->limit              = $limit;
        $this->view->total              = $total;
        $this->view->page               = $page;
        $this->view->offset             = $limit*($page-1);
        // $this->view->params             = $params;
        $this->view->sort               = $sort;
        $this->view->desc               = $desc;
        $this->view->url                = HOST.'checkmoney/multi-cash-list/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->messages_success   = $messages_success;
        $this->view->messages           = $messages;
    }

    public function excludeVat($value){
        return $value-($value-($value*7/107));
    }

    public function convert2Decimal($value){
        return number_format($value,2,'.','');
    }

    public function addNewSnAction(){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        try {

            $d_id = $this->getRequest()->getParam('d_id');

            $newsn = $this->getRequest()->getParam('newsn');
            $newsn_value = $this->getRequest()->getParam('newsn_value');
            $newsn_type = $this->getRequest()->getParam('newsn_type');

            if(!$d_id){
                echo json_encode(['status' => 400,'message' => 'Invalid Distributor.']);
                exit();
            }

            $QNSM = new Application_Model_NewsnManual();
            $QMarket = new Application_Model_Market();
            $QCDN = new Application_Model_CreditNote();
            $QCK = new Application_Model_Checkmoney();

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $array_data = array();

            $timenow = date('Y-m-d H:i:s');

            $count_make = 0;

            for($i=0;$i<count($newsn);$i++){

                if(!isset($newsn[$i]) || !trim($newsn[$i])){
                    echo json_encode(['status' => 400,'message' => 'Invalid SN, Please Check.']);
                    exit();
                }

                if(!isset($newsn_value[$i]) || !trim($newsn_value[$i])){
                    echo json_encode(['status' => 400,'message' => 'Invalid Value, Please Check.']);
                    exit();
                }

                if(!isset($newsn_type[$i]) || !trim($newsn_type[$i])){
                    echo json_encode(['status' => 400,'message' => 'Invalid Type, Please Check.']);
                    exit();
                }

                $where = $QNSM->getAdapter()->quoteInto('sn = ?', trim($newsn[$i]));
                $getNewSN = $QNSM->fetchRow($where);

                if ($getNewSN){
                    echo json_encode(['status' => 400,'message' => 'Dulipcate : ' . trim($newsn[$i])]);
                    exit();
                }

                switch (trim($newsn_type[$i])) {
                    case '1':
                        
                        $where = $QMarket->getAdapter()->quoteInto('sn_ref = ?', trim($newsn[$i]));
                        $getInvoice = $QMarket->fetchRow($where);

                        if ($getInvoice){
                            echo json_encode(['status' => 400,'message' => 'Dulipcate : ' . trim($newsn[$i])]);
                            exit();
                        }

                        break;
                    case '2':

                        $where = $QCDN->getAdapter()->quoteInto('creditnote_sn = ?', trim($newsn[$i]));
                        $getCNCP = $QCDN->fetchRow($where);

                        if ($getCNCP){
                            echo json_encode(['status' => 400,'message' => 'Dulipcate : ' . trim($newsn[$i])]);
                            exit();
                        }
                        
                        break;
                    
                    default:
                        echo json_encode(['status' => 400,'message' => 'ERROR : ' . trim($newsn[$i])]);
                        exit();
                        break;
                }

                $make_sn = $d_id . time() . $count_make;

                $array_data[] = array(
                    'd_id' => $d_id,
                    'sn' => $make_sn,
                    'sn_ref' => trim($newsn[$i]),
                    'type' => trim($newsn_type[$i]),
                    'total' => trim($newsn_value[$i]),
                    'created_date' => $timenow,
                    'created_at' => $userStorage->id,
                    'status' => 1
                );

                $count_make++;

            }

            foreach ($array_data as $key) {

                $QNSM->insert($key);

                if($key['type'] == '1'){

                    $data = array(
                        'd_id' => $key['d_id'],
                        'pay_time' => $key['created_date'],
                        'pay_money' => $key['total']*-1,
                        'sn' => $key['sn'],
                        'note' => 'Payment Order='.$key['total'].' ค่าอะไหล่และค่าบริการ=0.00',
                        'type' => 4,
                        'output' => $key['total'],
                        'payment' => $key['created_date'],
                        'user_id' => $key['created_at'],
                        'addition' => 0,
                        'create_at' => $key['created_date'],
                        'company_id' => 1,
                        'canceled' => 0,
                        'pay_banktransfer' => 0.00,
                        'pay_servicecharge' => 0.00,
                        'pay_service' => 0.00,
                        'sales_confirm_date' => $key['created_date'],
                        'sales_confirm_id' => $key['created_at'],
                        'finance_confirm_date' => $key['created_date'],
                        'finance_confirm_id' => $key['created_at']
                    );

                    $QCK->insert($data);
                }
            }

            $db->commit();

            echo json_encode(['status' => 200,'message' => 'Success.', 'data' => $array_data]);
                exit();

        } catch (Exception $e) {
            $db->rollback();

            echo json_encode(['status' => 400,'message' => 'Error.']);
                exit();
        }


    


    }

    public function _exportExcelExportCashCollectionExcel($data){
        //print_r($data);die;
        //$this->_helper->layout->disableLayout();
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'NO',
            'PAYMENT NO',
            'STORE ID',
            'RETAILER',
            'STORE CODE',
            'BANK',
            'Credit Card',
            'COMPANY',
            'TYPE',
            'PAYMENT TYPE',
            'ORDER NUMBER',
            'INVOICE NUMBER',
            'CREDIT NOTE',
            'DEPOSIT NO',
            'IN / OUT MONEY',
            'IN / OUT TIME',
            'BANK TRANSACTION CODE',
            'CONTENT',
            'NOTE',
            'PAY NOTE',
            'PAY NOTE FROM OUT',
            'BALANCE',
            'Cash collection period (YMD)',
            'FINANCE GROUP',
            'CANCEL STATUS',
            'CANCEL REMARK',
            'SALES ORDER REF',
            'PAYMENT NO REF',
            'OPERATION CAMPAIGN',
            'PHONE NUMBER'
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

////////////////////////////////////////////////

        $float = floatval($string);
        $QDistributor = new Application_Model_Distributor();
        $distributors_cached = $QDistributor->get_cache();
        $distributors_storecode_cached = $QDistributor->storecode_get_cache();

        $i = 2;
        $index    = 2;
        $payment_note='';$payment_no="";$pay_servicechargefee=0;$payment_servicechargefee='';
        foreach($data as $t){
                $float = floatval($string);

                if($t['payment_no'].'-'.$t['TYPE']!=$payment_servicechargefee && ($t['TYPE']==8)){
                    $pay_servicechargefee=1;
                    $payment_servicechargefee = $t['payment_no'].'-'.$t['TYPE'];
                }else{
                   // $pay_servicechargefee=0;
                }

                if($t['output'] <= 0)
                {
                    $money = $t['output'] * -1;  
                }
                
                $money_check = $t['output'];
                
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

                $title = isset($distributors_cached[$t['d_id']]) ? $distributors_cached[$t['d_id']] : '';
                $store_code = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

                //$finance_group = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

                if($t['seq']=='2'){
                    $payment_note=$t['pay_text'];
                }

                if($t['canceled']=='1'){
                    $cancel_status='canceled';
                    $canceled_remark=$t['canceled_remark'];
                }else{
                    $cancel_status='';
                    $canceled_remark='';
                }
                //print_r($t['TYPE']);die;
            if($pay_servicechargefee==1 && $t['TYPE']==8){ 
               $pay_servicechargefee=0;
               if($t['note'] !='Discount 1 %'){

                $sheet->getCell($alpha++.$index)->setValueExplicit( $index - 1 , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_no'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['d_id'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $title , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $store_code , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_name'] , PHPExcel_Cell_DataType::TYPE_STRING);

                $is_credit = '';
                if($t['credit_card']){
                    $is_credit = 'YES';
                }

                $sheet->getCell($alpha++.$index)->setValueExplicit( $is_credit , PHPExcel_Cell_DataType::TYPE_STRING);

                $sheet->getCell($alpha++.$index)->setValueExplicit( ( $t['company_id'] == 1 ) ? 'OPPO':'TM' , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $type_title , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_type'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $sn_ref , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['invoice_number'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['creditnote_sn'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['deposit_sn'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_money'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);

                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_time'] , PHPExcel_Cell_DataType::TYPE_STRING);

                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_transaction_code'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['content'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['note'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_text'] , PHPExcel_Cell_DataType::TYPE_STRING);

                $sheet->getCell($alpha++.$index)->setValueExplicit( $payment_note, PHPExcel_Cell_DataType::TYPE_STRING);

                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['balance'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);

                $sheet->getCell($alpha++.$index)->setValueExplicit( date("ymd", strtotime($t['pay_time'])) , PHPExcel_Cell_DataType::TYPE_STRING);

                }

                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['finance_group'], PHPExcel_Cell_DataType::TYPE_STRING);

                $sheet->getCell($alpha++.$index)->setValueExplicit( $cancel_status, PHPExcel_Cell_DataType::TYPE_STRING);

                $sheet->getCell($alpha++.$index)->setValueExplicit( $canceled_remark, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['sales_order_ref'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_no_ref'], PHPExcel_Cell_DataType::TYPE_STRING);

                if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %')){
                      
                   $sheet->getCell($alpha++.$index)->setValueExplicit( $index - 1 , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                   $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_no'] , PHPExcel_Cell_DataType::TYPE_STRING);
                   $sheet->getCell($alpha++.$index)->setValueExplicit( $t['d_id'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                   $sheet->getCell($alpha++.$index)->setValueExplicit( $title , PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $store_code , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_name'] , PHPExcel_Cell_DataType::TYPE_STRING);

                    $is_credit = '';
                    if($t['credit_card']){
                        $is_credit = 'YES';
                    }

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $is_credit , PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( ($t['company_id'] == 1 ) ? 'OPPO':'TM' , PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $type_title , PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_type'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $sn_ref , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['invoice_number'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['creditnote_sn'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_money'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_time'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_transaction_code'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['content'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['note'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_text'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['balance'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( date("ymd", strtotime($t['pay_time'])) , PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['finance_group'], PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $cancel_status, PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $canceled_remark, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['sales_order_ref'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_no_ref'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
            

                $index++;
            
                fputcsv($output, $row);
                unset($t);
                unset($row);
              }else if($t['TYPE'] !=8){
                //echo 11;die;
                if($t['note'] !='Discount 1 %'){

                $sheet->getCell($alpha++.$index)->setValueExplicit( $index - 1 , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_no'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['d_id'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $title , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $store_code , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_name'] , PHPExcel_Cell_DataType::TYPE_STRING);

                $is_credit = '';
                if($t['credit_card']){
                    $is_credit = 'YES';
                }

                $sheet->getCell($alpha++.$index)->setValueExplicit( $is_credit , PHPExcel_Cell_DataType::TYPE_STRING);

                $sheet->getCell($alpha++.$index)->setValueExplicit( ( $t['company_id'] == 1 ) ? 'OPPO':'TM' , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $type_title , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_type'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $sn_ref , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['invoice_number'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['creditnote_sn'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['deposit_sn'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_money'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);

                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_time'] , PHPExcel_Cell_DataType::TYPE_STRING);

                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_transaction_code'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['content'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['note'] , PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_text'] , PHPExcel_Cell_DataType::TYPE_STRING);

                $sheet->getCell($alpha++.$index)->setValueExplicit( $payment_note, PHPExcel_Cell_DataType::TYPE_STRING);

                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['balance'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);

                $sheet->getCell($alpha++.$index)->setValueExplicit( date("ymd", strtotime($t['pay_time'])) , PHPExcel_Cell_DataType::TYPE_STRING);

                }

                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['finance_group'], PHPExcel_Cell_DataType::TYPE_STRING);

                $sheet->getCell($alpha++.$index)->setValueExplicit( $cancel_status, PHPExcel_Cell_DataType::TYPE_STRING);

                $sheet->getCell($alpha++.$index)->setValueExplicit( $canceled_remark, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['sales_order_ref'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_no_ref'], PHPExcel_Cell_DataType::TYPE_STRING);

                if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %')){
                      
                   $sheet->getCell($alpha++.$index)->setValueExplicit( $index - 1 , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                   $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_no'] , PHPExcel_Cell_DataType::TYPE_STRING);
                   $sheet->getCell($alpha++.$index)->setValueExplicit( $t['d_id'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                   $sheet->getCell($alpha++.$index)->setValueExplicit( $title , PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $store_code , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_name'] , PHPExcel_Cell_DataType::TYPE_STRING);

                    $is_credit = '';
                    if($t['credit_card']){
                        $is_credit = 'YES';
                    }

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $is_credit , PHPExcel_Cell_DataType::TYPE_STRING);
                    
                    $sheet->getCell($alpha++.$index)->setValueExplicit( ($t['company_id'] == 1 ) ? 'OPPO':'TM' , PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $type_title , PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_type'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $sn_ref , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['invoice_number'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['creditnote_sn'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_money'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_time'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bank_transaction_code'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['content'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['note'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['pay_text'] , PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['balance'] , PHPExcel_Cell_DataType::TYPE_NUMERIC);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( date("ymd", strtotime($t['pay_time'])) , PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['finance_group'], PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $cancel_status, PHPExcel_Cell_DataType::TYPE_STRING);

                    $sheet->getCell($alpha++.$index)->setValueExplicit( $canceled_remark, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['sales_order_ref'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getCell($alpha++.$index)->setValueExplicit( $t['payment_no_ref'], PHPExcel_Cell_DataType::TYPE_STRING);
                }
            
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['bs_campaign'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getCell($alpha++.$index)->setValueExplicit( $t['phone_number_sn'], PHPExcel_Cell_DataType::TYPE_STRING);
                $index++;
            
                fputcsv($output, $row);
                unset($t);
                unset($row);
              }  
        }
                   

        //////////////////////////////////////////////////
       
        $filename = 'MoneyChecks_'.date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $filename = 'MoneyChecksCashCollection_'.date('d-m-Y H-i-s').'.xlsx';

        header('Content-Disposition: attachment;filename="' . $filename);

        $objWriter->save('php://output');
        exit;
    }

    public function _exportExcelExportCashCollectionServiceExcel($data)
    {
        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        //$filename = 'SERVICE-JOBNUMBER'. ' - '.date('d-m-Y H-i-s').'.csv';
        $filename = 'MoneyChecksCashCollectionService_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $file_path = APPLICATION_PATH.'/../public/files/sales/service/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);


        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'NO',
            'STORE ID',
            'RETAILER',
            'STORE CODE',
            'COMPANY',
            'PAYMENT NO',
            'BANK',    
            'IN TIME',
            'TYPE',
            'IN MONEY',
            'ORDER NUMBER',
            'INVOICE NUMBER',
            
            'FINANCE GROUP'
        );

        fputcsv($output, $heads);
        //--------------------------------------


        $float = floatval($string);
        $QDistributor = new Application_Model_Distributor();
        $distributors_cached = $QDistributor->get_cache();
        $distributors_storecode_cached = $QDistributor->storecode_get_cache();

        $i = 2;
        $index    = 2;
        $payment_note='';$payment_no="";$pay_servicechargefee=0;$payment_servicechargefee='';
        foreach($data as $t){
            $row = array();
            //$row[] = $t['job_running'];

            
            $float = floatval($string);

            if($t['payment_no'].'-'.$t['TYPE']!=$payment_servicechargefee && ($t['TYPE']==8)){
                $pay_servicechargefee=1;
                $payment_servicechargefee = $t['payment_no'].'-'.$t['TYPE'];
            }else{
               // $pay_servicechargefee=0;
            }

            if($t['output'] <= 0)
            {
                $money = $t['output'] * -1;  
            }
            
            $money_check = $t['output'];
            
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

            $title = isset($distributors_cached[$t['d_id']]) ? $distributors_cached[$t['d_id']] : '';
            $store_code = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

            //$finance_group = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

            if($t['seq']=='2'){
                $payment_note=$t['pay_text'];
            }

            if($t['canceled']=='1'){
                $cancel_status='canceled';
                $canceled_remark=$t['canceled_remark'];
            }else{
                $cancel_status='';
                $canceled_remark='';
            }

            if($pay_servicechargefee==1 && $t['TYPE']==8){ 
               $pay_servicechargefee=0;
               if($t['note'] !='Discount 1 %'){

                    $row[] = $index - 1;
                    $row[] = $t['d_id'];
                    $row[] = $title;
                    $row[] = $store_code;
                    $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
                    $row[] = $t['payment_no'];
                    $row[] = $t['bank_name'];
                    
                    $row[] = $t['pay_time'];
                    $row[] = $type_title;
                    $row[] = $t['pay_money'];
                    $row[] = $sn_ref;
                    $row[] = $t['invoice_number'];
                    

                }
                $row[] = $t['finance_group'];

                if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %')){
                    $row[] = $index - 1;
                    $row[] = $t['d_id'];
                    $row[] = $title;
                    $row[] = $store_code;
                    $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
                    $row[] = $t['payment_no'];
                    $row[] = $t['bank_name'];
                    $row[] = $t['pay_time'];
                    $row[] = $type_title;
                    $row[] = $t['pay_money'];
                    $row[] = $sn_ref;
                    $row[] = $t['invoice_number'];
                    
                    $row[] = $t['finance_group'];
                }
            

                $index++;
            
                fputcsv($output, $row);
                unset($t);
                unset($row);
              }else
              if($t['TYPE'] !=8 && $t['TYPE'] !=2){
                if($t['note'] !='Discount 1 %'){
                    $row[] = $index - 1;
                    $row[] = $t['d_id'];
                    $row[] = $title;
                    $row[] = $store_code;
                    $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
                    $row[] = $t['payment_no'];
                    $row[] = $t['bank_name'];
                    $row[] = $t['pay_time'];
                    $row[] = $type_title;
                    $row[] = $t['pay_money'];
                    $row[] = $sn_ref;
                    $row[] = $t['invoice_number'];
                    
                }
                $row[] = $t['finance_group'];

                if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %')){
                    $row[] = $index - 1;
                    $row[] = $t['d_id'];
                    $row[] = $title;
                    $row[] = $store_code;
                    $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
                    $row[] = $t['payment_no'];
                    $row[] = $t['bank_name'];
                    $row[] = $t['pay_time'];
                    $row[] = $type_title;
                    $row[] = $t['pay_money'];
                    $row[] = $sn_ref;
                    $row[] = $t['invoice_number'];
                    
                    $row[] = $t['finance_group'];
              }  
                $index++;
            
                fputcsv($output, $row);
                unset($t);
                unset($row);
            }

        }
        unset($data);
       
        //--------------------------------------
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

    public function _exportExcelExportCashCollectionBrandShopExcel($data)
    {
        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        //$filename = 'SERVICE-JOBNUMBER'. ' - '.date('d-m-Y H-i-s').'.csv';
        $filename = 'MoneyChecksCashCollectionBrandShop_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $file_path = APPLICATION_PATH.'/../public/files/sales/service/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);


        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'NO',
            'STORE ID',
            'RETAILER',
            'STORE CODE',
            'COMPANY',
            'PAYMENT NO',
            'BANK',    
            'IN TIME',
            'TYPE',
            'IN MONEY',
            'ORDER NUMBER',
            'INVOICE NUMBER',
            
            'FINANCE GROUP'
        );

        fputcsv($output, $heads);
        //--------------------------------------


        $float = floatval($string);
        $QDistributor = new Application_Model_Distributor();
        $distributors_cached = $QDistributor->get_cache();
        $distributors_storecode_cached = $QDistributor->storecode_get_cache();

        $i = 2;
        $index    = 2;
        $payment_note='';$payment_no="";$pay_servicechargefee=0;$payment_servicechargefee='';
        foreach($data as $t){
            $row = array();
            //$row[] = $t['job_running'];

            
            $float = floatval($string);

            if($t['payment_no'].'-'.$t['TYPE']!=$payment_servicechargefee && ($t['TYPE']==8)){
                $pay_servicechargefee=1;
                $payment_servicechargefee = $t['payment_no'].'-'.$t['TYPE'];
            }else{
               // $pay_servicechargefee=0;
            }

            if($t['output'] <= 0)
            {
                $money = $t['output'] * -1;  
            }
            
            $money_check = $t['output'];
            
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

            $title = isset($distributors_cached[$t['d_id']]) ? $distributors_cached[$t['d_id']] : '';
            $store_code = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

            //$finance_group = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

            if($t['seq']=='2'){
                $payment_note=$t['pay_text'];
            }

            if($t['canceled']=='1'){
                $cancel_status='canceled';
                $canceled_remark=$t['canceled_remark'];
            }else{
                $cancel_status='';
                $canceled_remark='';
            }

            if($pay_servicechargefee==1 && $t['TYPE']==8){ 
               $pay_servicechargefee=0;
               if($t['note'] !='Discount 1 %'){

                    $row[] = $index - 1;
                    $row[] = $t['d_id'];
                    $row[] = $title;
                    $row[] = $store_code;
                    $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
                    $row[] = $t['payment_no'];
                    $row[] = $t['bank_name'];
                    
                    $row[] = $t['pay_time'];
                    $row[] = $type_title;
                    $row[] = $t['pay_money'];
                    $row[] = $sn_ref;
                    $row[] = $t['invoice_number'];
                    

                }
                $row[] = $t['finance_group'];

                if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %')){
                    $row[] = $index - 1;
                    $row[] = $t['d_id'];
                    $row[] = $title;
                    $row[] = $store_code;
                    $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
                    $row[] = $t['payment_no'];
                    $row[] = $t['bank_name'];
                    $row[] = $t['pay_time'];
                    $row[] = $type_title;
                    $row[] = $t['pay_money'];
                    $row[] = $sn_ref;
                    $row[] = $t['invoice_number'];
                    
                    $row[] = $t['finance_group'];
                }
            

                $index++;
            
                fputcsv($output, $row);
                unset($t);
                unset($row);
              }else
              if($t['TYPE'] !=8 && $t['TYPE'] !=2){
                if($t['note'] !='Discount 1 %'){
                    $row[] = $index - 1;
                    $row[] = $t['d_id'];
                    $row[] = $title;
                    $row[] = $store_code;
                    $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
                    $row[] = $t['payment_no'];
                    $row[] = $t['bank_name'];
                    $row[] = $t['pay_time'];
                    $row[] = $type_title;
                    $row[] = $t['pay_money'];
                    $row[] = $sn_ref;
                    $row[] = $t['invoice_number'];
                    
                }
                $row[] = $t['finance_group'];

                if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %')){
                    $row[] = $index - 1;
                    $row[] = $t['d_id'];
                    $row[] = $title;
                    $row[] = $store_code;
                    $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
                    $row[] = $t['payment_no'];
                    $row[] = $t['bank_name'];
                    $row[] = $t['pay_time'];
                    $row[] = $type_title;
                    $row[] = $t['pay_money'];
                    $row[] = $sn_ref;
                    $row[] = $t['invoice_number'];
                    
                    $row[] = $t['finance_group'];
              }  
                $index++;
            
                fputcsv($output, $row);
                unset($t);
                unset($row);
            }

        }
        unset($data);
       
        //--------------------------------------
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
    
}          

