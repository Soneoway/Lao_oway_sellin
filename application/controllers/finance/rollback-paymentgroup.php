<?php

$payment_no = $this->getRequest()->getParam('payment_no');

$back_url = '/finance/sales-payment';
$back_url_refer = '/finance/sales-payment';

if ($payment_no)
{
    $QMarket = new Application_Model_Market();

    $flashMessenger = $this->_helper->flashMessenger;

    $where = array();
    $where[] = $QMarket->getAdapter()->quoteInto('payment_no = ?', $payment_no);
    $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

    $sales = $QMarket->fetchAll($where);
   
    //check

    if (!$sales)
    {

        $flashMessenger->setNamespace('error')->addMessage('Invalid SN!');
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
        $this->_redirect($back_url);

    }

    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    try
    {
  
        $data = array(
            'shipping_yes_time' => null,
            'pay_time' => null,
            'shipping_yes_id' => null,
            'pay_user' => null,
            'confirm_so' => 0,
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
        );

        // if($distributor[0]['rank'] == 10){
        //     $data['outmysql_time'] = null;
        //     $data['outmysql_user'] = null;
        // }

        $QMarket->update($data, $where);

        $QPG = new Application_Model_PayGroup();
        $getPaymentID = $QPG->getPaymentIDByPaymentNo($payment_no);

        // delete record in check money
        $QCheckmoney = new Application_Model_Checkmoney();
        $where = array();
        $where[] = $QCheckmoney->getAdapter()->quoteInto('payment_id = ?', $getPaymentID['payment_id']);
        $where[] = $QCheckmoney->getAdapter()->quoteInto('d_id = ?', $sales[0]['d_id']);
        $QCheckmoney->delete($where);

        // delete record in Checkmoney_Paymentorder edit by pungpond
        $QCheckmoneyPaymentorder = new Application_Model_CheckmoneyPaymentorder();
        $where = $QCheckmoneyPaymentorder->getAdapter()->quoteInto('payment_id = ?', $getPaymentID['payment_id']);
        $QCheckmoneyPaymentorder->delete($where);

        // Cancel Payment_no
        $QPayGroup = new Application_Model_PayGroup();
        $where = $QPayGroup->getAdapter()->quoteInto('payment_no = ?', $payment_no);

        $data = array(
            'status' => 0,
        );
        $QPayGroup->update($data, $where);
    
        // update balance
        $QStoreaccount = new Application_Model_Storeaccount();
        $QStoreaccount->updateBalance($sales[0]['d_id']);

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //todo log
        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        $info = 'Sales payment group rollback : ' . $payment_no;

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
        // print_r($e->getMessage());die;
        $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!' . ' | ' . $e->getMessage());
    }
}

$this->_redirect($back_url_refer);
