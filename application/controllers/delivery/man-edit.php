<?php 
$id = $this->getRequest()->getParam('id');
$flashMessenger = $this->_helper->flashMessenger;

try {
    if (!$id) throw new Exception("Invalid ID", 1);

    $QDeliveryMan = new Application_Model_DeliveryMan();
    $where = $QDeliveryMan->getAdapter()->quoteInto('id = ?', $id);
    $man = $QDeliveryMan->fetchRow($where);

    if (!$man) throw new Exception("Wrong ID", 2);

    $refer = $flashMessenger->setNamespace('refer')->getMessages();

    $this->view->man = $man;
    $this->view->refer = isset($refer[0]) ? $refer[0] : My_Url::refer(HOST.'delivery/man');

    $this->_helper->viewRenderer->setRender('man-create');

    $this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();
} catch (Exception $e) {
    $flashMessenger->setNamespace('success')->addMessage(sprintf("[%d] %s", $e->getCode(), $e->getMessage()));
    $this->_redirect(My_Url::refer(HOST.'delivery/man'));
}