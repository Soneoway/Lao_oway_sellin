<?php 
$id = $this->getRequest()->getParam('id');
$flashMessenger = $this->_helper->flashMessenger;

try {
    if (!$id) throw new Exception("Invalid ID", 1);

    $QDeliveryMan = new Application_Model_DeliveryMan();
    $where = $QDeliveryMan->getAdapter()->quoteInto('id = ?', $id);
    $man_check = $QDeliveryMan->fetchRow($where);

    if (!$man_check) throw new Exception("Delivery man not exists", 2);

    $data = array('status' => 1);
    $QDeliveryMan->update($data, $where);

    $cache = Zend_Registry::get('cache');
    $cache->remove('delivery_man_cache');

    $flashMessenger->setNamespace('success')->addMessage('Success');
} catch (Exception $e) {
    $flashMessenger->setNamespace('error')->addMessage(sprintf("[%d] %s", $e->getCode(), $e->getMessage()));
}

$this->_redirect(My_Url::refer(HOST.'delivery/man'));
