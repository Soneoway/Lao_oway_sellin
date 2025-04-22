<?php 
$id             = $this->getRequest()->getParam('id');
$name           = $this->getRequest()->getParam('name');
$note           = $this->getRequest()->getParam('note');
$refer          = $this->getRequest()->getParam('refer', HOST.'delivery/man');
$flashMessenger = $this->_helper->flashMessenger;

try {
    if (!$name) throw new Exception("Name is required", 1);

    $name = strval(trim($name));
    if ($id) $id = intval($id);

    $QDeliveryMan = new Application_Model_DeliveryMan();
    $where = array();
    $where[] = $QDeliveryMan->getAdapter()->quoteInto('name COLLATE utf8_bin LIKE ?', $name);
    if ($id) $where[] = $QDeliveryMan->getAdapter()->quoteInto('id <> ?', $id);

    $man_check = $QDeliveryMan->fetchRow($where);

    if ($man_check) throw new Exception("Name exists. Please add suffix for this name.", 2);
    if ($note) $note = trim($note);

    $data = array(
        'name' => $name,
        'note' => $note,
    );

    if ($id) {

        $where = $QDeliveryMan->getAdapter()->quoteInto('id = ?', $id);
        $man_check = $QDeliveryMan->fetchRow($where);

        if (!$man_check) throw new Exception("Delivery man not exists", 3);

        $QDeliveryMan->update($data, $where);
    } else {
        $id = $QDeliveryMan->insert($data);
    }

    if (!$id) throw new Exception("Insert failed", 4);

    $cache = Zend_Registry::get('cache');
    $cache->remove('delivery_man_cache');

    $flashMessenger->setNamespace('success')->addMessage('Success');
    $this->_redirect($refer);
} catch (Exception $e) {
    $flashMessenger->setNamespace('error')->addMessage(sprintf("[%d] %s", $e->getCode(), $e->getMessage()));
    $flashMessenger->setNamespace('refer')->addMessage($refer);
    $this->_redirect(My_Url::refer(HOST.'delivery/man'));
}