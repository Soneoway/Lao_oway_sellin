<?php
$id = $this->getRequest()->getParam('id');
$flashMessenger = $this->_helper->flashMessenger;

try {
    if (!$id) throw new Exception("Invalid ID", 1);

    $QStaff = new Application_Model_Staff();
    $where = $QStaff->getAdapter()->quoteInto('id = ?', $id);
    $staff = $QStaff->fetchRow($where);

    if (!$staff) throw new Exception("Wrong ID", 2);

    $this->view->staff = $staff;
    $this->view->refer = My_Url::refer('delivery/staff');

    $QStaffHub = new Application_Model_StaffHub();
    $where = $QStaffHub->getAdapter()->quoteInto('staff_id = ?', $id);
    $this->view->hub = $QStaffHub->fetchAll($where);

    $QStaffCarrier = new Application_Model_StaffCarrier();
    $where = $QStaffCarrier->getAdapter()->quoteInto('staff_id = ?', $id);
    $this->view->carrier = $QStaffCarrier->fetchRow($where);

    $QHub = new Application_Model_Hub();
    $this->view->hubs = $QHub->get_cache();

    $this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();
} catch (Exception $e) {
    $flashMessenger->setNamespace('success')->addMessage(sprintf("[%d] %s", $e->getCode(), $e->getMessage()));
    $this->_redirect(My_Url::refer('delivery/staff'));
}
