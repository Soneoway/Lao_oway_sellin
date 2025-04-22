<?php

$id = $this->getRequest()->getParam('id');

$flashMessenger = $this->_helper->flashMessenger;

try {
    if (!$id) throw new Exception("Invalid ID.");

    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    $QPo = new Application_Model_Po();
    $po = $QPo->find($id);
    $po = $po->current();

    if (!$po) throw new Exception("Invalid ID.");

    $imeis = false;

    if (PHONE_CAT_ID == $po['cat_id']) {
        $QImei = new Application_Model_Imei();
        $where = $QImei->getAdapter()->quoteInto('po_sn = ?', $po['sn']);
        $imeis = $QImei->fetchRow($where);
        
    } elseif (DIGITAL_CAT_ID == $po['cat_id']) {
        $QImei = new Application_Model_DigitalSn();
        $where = $QImei->getAdapter()->quoteInto('po_sn = ?', $po['sn']);
        $imeis = $QImei->fetchRow($where);
    }

    if ($imeis) throw new Exception("This Order is processing... Cannot rollback!");

    $where = $QPo->getAdapter()->quoteInto('id = ?', $id);
    $data = array(
        'pay_user' => null,
        'flow' => null,
        'flow_time' => null,
    );

    $QPo->update($data, $where);

    //todo log
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $ip = $this->getRequest()->getServer('REMOTE_ADDR');

    $info = 'Rollback: Purchase order number: '.$po['sn'];

    $QLog = new Application_Model_Log();

    $QLog->insert( array (
        'info' => $info,
        'user_id' => $userStorage->id,
        'ip_address' => $ip,
        'time' => date('Y-m-d H:i:s'),
    ) );

    $flashMessenger->setNamespace('success')->addMessage('Done!');
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
}

$this->_redirect(HOST.'warehouse/in');