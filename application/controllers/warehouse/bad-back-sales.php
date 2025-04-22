<?php
$imei_sn = $this->getRequest()->getParam('imei_sn');

$back_url = $this->getRequest()->getServer('HTTP_REFERER') ? $this->getRequest()->getServer('HTTP_REFERER') : '/warehouse/bad-imei-management';

if ($imei_sn) {
    $QImei = new Application_Model_Imei();

    $flashMessenger = $this->_helper->flashMessenger;

    $data = array(
        'status' => 1,//ok
    );

    $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei_sn);

    $QImei->update($data, $where);

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    //todo log
    $ip = $this->getRequest()->getServer('REMOTE_ADDR');

    $info = 'Back to sale BAD : ' . $imei_sn;

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