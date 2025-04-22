<?php
$flashMessenger   = $this->_helper->flashMessenger;
$id = $this->getRequest()->getParam('id');
$flag = FALSE;

if ($id) {
    $QDistributor = new Application_Model_Distributor();
    $distributorRowSet = $QDistributor->find($id);
    $distributor = $distributorRowSet->current();

    if ($distributor) {
        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $id);

        //update field del
        $data = array(
            'del' => null
        );

        $QDistributor->update($data, $where);

        $messages_success = $flashMessenger->setNamespace('success')->addMessage('Success');
        $flag = TRUE;
    }
}

if (!$flag)
    $messages = $flashMessenger->setNamespace('error')->addMessage('Invalid ID');

//remove cache
$cache = Zend_Registry::get('cache');
$cache->remove('distributor_cache');
$cache->remove('distributor_2_cache');
$cache->remove('distributor_with_store_code_cache');
$cache->remove('distributor_all_cache');
$cache->remove('distributor_by_area_cache');

$this->_redirect(My_Url::refer('sales/distributor'));