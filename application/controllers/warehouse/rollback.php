<?php

$sn = $this->getRequest()->getParam('sn');
$return_sn = $this->getRequest()->getParam('return_sn');
$action_frm = $this->getRequest()->getParam('action_frm');
$remark_mobile = $this->getRequest()->getParam('remark_mobile');

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

//print_r($_GET);die;
if($action_frm=='finance')
{
    $back_url = '/finance/sales-payment';
    $back_url_refer = '/finance/sales-payment';
}else{

    $back_url = '/warehouse/out'; 
    $back_url_refer = $this->getRequest()->getServer('HTTP_REFERER') ? $this->getRequest()->
    getServer('HTTP_REFERER') : '/warehouse/out';   
}

if ($sn)
{
    $QMarket = new Application_Model_Market();

    $flashMessenger = $this->_helper->flashMessenger;

    $where = array();
    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

    $sales = $QMarket->fetchAll($where);
   
    //check

    if (!$sales)
    {

        $flashMessenger->setNamespace('error')->addMessage('Invalid SN!');

        //$this->_redirect('/warehouse/out');
        $this->_redirect($back_url);

    }

    $QMD = new Application_Model_Distributor();

    $whereDistributor = $QMD->getAdapter()->quoteInto('id = ?', $sales[0]['d_id']);
    $distributor = $QMD->fetchAll($whereDistributor);

    if (!$distributor)
    {

        $flashMessenger->setNamespace('error')->addMessage('Invalid Distributor!');
        $this->_redirect($back_url);

    }

    if ($sales[0]['outmysql_time'] && $distributor[0]['rank'] != 10)
    {

        $flashMessenger->setNamespace('error')->addMessage('This order cannot be changed!');

        //$this->_redirect('/warehouse/out');
        $this->_redirect($back_url);

    }

    if($action_frm == 'out_list' && $sales[0]['payment_no']){

        $QPG = new Application_Model_PayGroup();
        $getBalancePayGroup = $QPG->getPaymentBalance($sales[0]['payment_no']);

        if($getBalancePayGroup['use_total'] > 0){
            $flashMessenger->setNamespace('error')->addMessage('This order cannot be rollback, Please clear payment no!');

            //$this->_redirect('/warehouse/out');
            $this->_redirect($back_url);
        }
    }

    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    try
    {
        //reset imei neu da scanout
        $QImei = new Application_Model_Imei();
        
        $where_sn = $QImei->getAdapter()->quoteInto('sales_sn = ?',$sn);

        $QStaffSalesOrder = new Application_Model_EpPrivilegesTranOrder();

        $ep_data = array(
                    'status' => 6,
                    'hr_remark' => $remark_mobile
                   );
        $where_ep_sn = $QImei->getAdapter()->quoteInto('sales_order_sn = ?',$sn);
        $ep_status = $QStaffSalesOrder->update($ep_data, $where_ep_sn);

        if($distributor[0]['rank'] == 10){

            $data = array(
                'shipping_yes_time' => null,
                'pay_time' => null,
                'shipping_yes_id' => null,
                'pay_user' => null,
                'confirm_so' => 0,
                'pay_group' => 0,
                'sales_confirm_date' => null,
                'sales_confirm_id' => null,
                'pay_text' => null,
                'shipping_text' => null,
                'finance_confirm_date' => null,
                'finance_confirm_id' => null,
                'confirm_access_date' => null,
                'confirm_access_status' => null,
                'confirm_access_remark' => null,
                'confirm_access_by' => null
            );

            if($ep_status){
                $data['canceled'] = 1;
                $data['canceled_by'] = $userStorage->id;
                $data['canceled_remark'] = $remark_mobile;
                $data['date_canceled'] = date('Y-m-d H:i:s');
                $data['pay_time'] = date('Y-m-d H:i:s');
            }

            $QMarket->update($data, $where);

        }else{

            $data = array(
                'distributor_id' => null,
                'out_date' => null,
                'out_user' => null,
                'out_price' => null,
                'price_date' => null,
                'sales_sn' => null,
                'sales_id' => null,
            );
            
            $QImei->update($data, $where_sn);
      
            $data = array(
                'shipping_yes_time' => null,
                'pay_time' => null,
                'shipping_yes_id' => null,
                'pay_user' => null,
                'confirm_so' => 0,
                'pay_group' => 0,
                'sales_confirm_date' => null,
                'sales_confirm_id' => null,
                'pay_text' => null,
                'shipping_text' => null,
                'finance_confirm_date' => null,
                'finance_confirm_id' => null,
                'confirm_access_date' => null,
                'confirm_access_status' => null,
                'confirm_access_remark' => null,
                'confirm_access_by' => null,
                'outmysql_time' => null,
                'outmysql_user' => null
            );

            if($ep_status){
                $data['canceled'] = 1;
                $data['canceled_by'] = $userStorage->id;
                $data['canceled_remark'] = $remark_mobile;
                $data['date_canceled'] = date('Y-m-d H:i:s');
                $data['pay_time'] = date('Y-m-d H:i:s');
            }

            $QMarket->update($data, $where);

        }

        // delete record in check money
        $QCheckmoney = new Application_Model_Checkmoney();
        $where = array();
        $where[] = $QCheckmoney->getAdapter()->quoteInto('sn = ?', $sn);
        // $where[] = $QCheckmoney->getAdapter()->quoteInto('type = 1');
        $where[] = $QCheckmoney->getAdapter()->quoteInto('d_id = ?', $sales[0]['d_id']);
        $QCheckmoney->delete($where);

        // delete record in Checkmoney_Paymentorder edit by pungpond
        $QCheckmoneyPaymentorder = new Application_Model_CheckmoneyPaymentorder();
        $where = $QCheckmoneyPaymentorder->getAdapter()->quoteInto('sn = ?', $sn);
        $QCheckmoneyPaymentorder->delete($where);

        // Cancel Payment_no
        $QPayGroup = new Application_Model_PayGroup();
        $where = $QPayGroup->getAdapter()->quoteInto('sale_order = ?', $sn);

        $data = array(
            'status' => 0,
        );
        $QPayGroup->update($data, $where);

        $QJobNumber = new Application_Model_JobNumber();
        $where_job = $QJobNumber->getAdapter()->quoteInto('sales_order = ?', $sn);
        $QJobNumber->delete($where_job);
   
        // update balance
        $QStoreaccount = new Application_Model_Storeaccount();
        $QStoreaccount->updateBalance($sales[0]['d_id']);

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //todo log
        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        $info = 'Sales rollback : ' . $sn;

        //Tanong
        $QImeiReturn = new Application_Model_ImeiReturn();
        $where = array();
        $where[] = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);

         $data = array(
            'creditnote_sn' => null,
         );
        $QImeiReturn->update($data, $where);
        


        $QLog = new Application_Model_Log();

        $QLog->insert(array(
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => date('Y-m-d H:i:s'),
            ));

        $db->commit();
        $flashMessenger->setNamespace('success')->addMessage('Done!');

    }
    catch (exception $e)
    {
        $db->rollback();
        $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
    }
}

$this->_redirect($back_url_refer);
