<?php
$flashMessenger   = $this->_helper->flashMessenger;
$id = $this->getRequest()->getParam('id');
$flag = FALSE;

if ($id) {
    $QWarehouse = new Application_Model_Warehouse();
    $warehouseRowSet = $QWarehouse->find($id);
    $warehouse = $warehouseRowSet->current();

    if ($warehouse) {
        $where = $QWarehouse->getAdapter()->quoteInto('id = ?', $id);
        $QWarehouse->delete($where);
        $messages_success = $flashMessenger->setNamespace('success')->addMessage('Success');
        $flag = TRUE;
    }
}

//remove cache
$cache = Zend_Registry::get('cache');
$cache->remove('warehouse_cache');

if (!$flag)
    $messages = $flashMessenger->setNamespace('error')->addMessage('Invalid ID');

$this->_redirect(HOST.'warehouse/list');