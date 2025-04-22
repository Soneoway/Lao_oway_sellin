<?php 
$id = $this->getRequest()->getParam('id');
$flashMessenger = $this->_helper->flashMessenger;

try {
    if (!$id) throw new Exception("Invalid ID", 1);
    
    $QHub = new Application_Model_Hub();
    $where = $QHub->getAdapter()->quoteInto('id = ?', $id);
    $hub = $QHub->fetchRow($where);

    if (!$hub) throw new Exception("Invalid Hub", 2);
    
    $QHub->delete($where);

    $cache = Zend_Registry::get('cache');
    $cache->remove('hub_cache');
    $cache->remove('hub_all_cache');
    
    $flashMessenger->setNamespace('success')->addMessage('Success');
} catch (Exception $e) {
    $flashMessenger->setNamespace('error')->addMessage(sprintf("[%d] %s", $e->getCode(), $e->getMessage()));
}

$this->_redirect(My_Url::refer('hub'));