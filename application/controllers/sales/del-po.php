<?php
try {
    $flashMessenger = $this->_helper->flashMessenger;

    $po_id = $this->getRequest()->getParam('po_id');
    if (!$po_id) throw new Exception("Invalid PO ID", 1);
    $po_id = intval($po_id);

    $QDistributorPo = new Application_Model_DistributorPo();
    $where = $QDistributorPo->getAdapter()->quoteInto('id = ?', $po_id);
    $po_check = $QDistributorPo->fetchRow($where);
    if (!$po_check) throw new Exception("PO not existed", 2);

    $QMarket = new Application_Model_Market();

    $where = array();
    $where[] = $QMarket->getAdapter()->quoteInto('po_id = ?', $po_id);
    $where[] = $QMarket->getAdapter()->quoteInto('pay_time <> 0 AND pay_time IS NOT NULL', 1);
    $market_check = $QMarket->fetchRow($where);
    if ($market_check) throw new Exception("Cannot delete orders checked by finance", 3);

    $where = $QMarket->getAdapter()->quoteInto('po_id = ?', $po_id);
    $market_check = $QMarket->fetchAll($where);
    $market_sn = array();

    foreach ($market_check as $key => $value)
        $market_sn[] = $value['sn'];

    $market_sn = array_unique($market_sn);

    $QMarket->delete($where);

    //todo log
    $ip = $this->getRequest()->getServer('REMOTE_ADDR');
    $str = implode(',', $market_sn);
    $info = 'PO - Delete all orders in PO (' . $po_check['po_name'] .") - PO ID (".$po_id.") - Market SN list (".(strlen($str) > 100 ? count($market_sn) : $str).")";
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $QLog = new Application_Model_Log();
    $QLog->insert( array (
        'info' => $info,
        'user_id' => $userStorage->id,
        'ip_address' => $ip,
        'time' => date('Y-m-d H:i:s'),
    ) );

    $flashMessenger->setNamespace('success')->addMessage('Success');
} catch (Exception $e) {
    $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
}

$this->_redirect(HOST.'finance/distributor-po');
