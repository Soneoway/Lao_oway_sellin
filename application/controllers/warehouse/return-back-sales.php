<?php
$id = $this->getRequest()->getParam('id');

$back_url = $this->getRequest()->getServer('HTTP_REFERER') ? $this->getRequest()->getServer('HTTP_REFERER') : '/warehouse/return-management';

if ($id) {
    $QImeiReturn = new Application_Model_ImeiReturn();

    $flashMessenger = $this->_helper->flashMessenger;

    $data = array(
        'back_sale' => 1,
    );

    $where = $QImeiReturn->getAdapter()->quoteInto('id = ?', $id);

    $item = $QImeiReturn->fetchRow($where);

    $QImeiReturn->update($data, $where);

    $imei_sn    = $item['imei_sn'];
    $sn         = $item['return_sn'];
    $warehouse_id = $item['warehouse_id'];

    $QImei = new Application_Model_Imei();

    $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei_sn);

    $data = array(
        'return_sn'     => null,
        'out_date'      => null,
        'distributor_id'=> null,
        'out_user'      => null,
        'out_price'     => null,
        'price_date'    => null,
        'sales_sn'      => null,
        'sales_id'      => null,
        'warehouse_id'  => $warehouse_id,
        'status'        => 1,
    );

    $QImei->update($data, $where);

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    //todo log
    $ip = $this->getRequest()->getServer('REMOTE_ADDR');

    $info = 'Back to sale : ' . $id;

    $QLog = new Application_Model_Log();

    $QLog->insert( array (
        'info' => $info,
        'user_id' => $userStorage->id,
        'ip_address' => $ip,
        'time' => date('Y-m-d H:i:s'),
    ) );

    $flashMessenger->setNamespace('success')->addMessage('Done!');
}

$this->_redirect($back_url);