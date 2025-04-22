<?php

$sn = $this->getRequest()->getParam('sn');

$back_url = $this->getRequest()->getServer('HTTP_REFERER') ? $this->getRequest()->
getServer('HTTP_REFERER') : '/warehouse/internal-number';

if ($sn)
{
    $QInternalOrder  = new Application_Model_InternalOrder();
    $QInternalOrderDetail = new Application_Model_InternalOrderDetail();
    $QInternalNumber = new Application_Model_InternalNumber();
    $QMobilization   = new Application_Model_MobilizationNumber();
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $flashMessenger = $this->_helper->flashMessenger;

    $where   = array();
    $where[] = $QInternalOrder->getAdapter()->quoteInto('sn = ?', $sn);
    $where[] = $QInternalOrder->getAdapter()->quoteInto('invoice_number is not null' , null);
    $where[] = $QInternalOrder->getAdapter()->quoteInto('order_name is not null' , null);
    $orders  = $QInternalOrder->fetchRow($where);

    //check

    if (!$orders)
    {

        $flashMessenger->setNamespace('error')->addMessage('Invalid SN!');

        $this->_redirect('/warehouse/internal-number');

    }


    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    try
    {
        $currentTime = date('Y-m-d H:i:s');

        $dataRollback = array(
            'is_back'      => 1,
            'is_back_time' => null
        );


        $QInternalOrder->update($dataRollback , $where);


        //todo log
        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        $info = 'INTERNAL ORDER rollback : ' . $sn;

        $QLog = new Application_Model_Log();

        $QLog->insert(array(
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => $currentTime,
        ));

        $db->commit();
        $flashMessenger->setNamespace('success')->addMessage('Done!');

    }
    catch (exception $e)
    {
        var_dump($e);exit;
        $db->rollback();
        $flashMessenger->setNamespace('error')->addMessage('Cannot rollback, please try again!');
    }
}

$this->_redirect($back_url);
