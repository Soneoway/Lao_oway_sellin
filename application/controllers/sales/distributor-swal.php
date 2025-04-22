<?php
$d_id = $this->getRequest()->getParam('id');
$wh_id = $this->getRequest()->getParam('wh_id');
$status_id = $this->getRequest()->getParam('status_id');
// print_r($d_id);
// exit;
if ($d_id) {
    $QDistributor = new Application_Model_Distributor();
    $distributor = $QDistributor->find($d_id);
    $distributors = $distributor->current();
    if (!$distributors) throw new Exception("Invalid ID");
}
$flashMessenger = $this->_helper->flashMessenger;
if ($status_id == 1) {
    foreach ($d_id as $id) {
        $data = array(
            'warehouse_id'   => $wh_id,
            );
        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $id);
        $QDistributor->update($data, $where);
    }
}else{
    $flashMessenger->setNamespace('error')->addMessage('Error');
}
$flashMessenger->setNamespace('success')->addMessage('Done');
$this->_redirect(HOST.'manage/distributors');
