<?php
$flashMessenger = $this->_helper->flashMessenger;

$id 			= $this->getRequest()->getParam('id');
$event 			= $this->getRequest()->getParam('event');
$remark         = $this->getRequest()->getParam('remark');

$db = Zend_Registry::get('db');
$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$QDistributor = new Application_Model_Distributor();


if($id) {

	$distributorRowSet = $QDistributor->find($id);
    $distributor       = $distributorRowSet->current();

    if (!$distributor) {
        $flashMessenger->setNamespace('error')->addMessage('Invalid Distributor ID. Please Check And Try Agian !.');
        $this->_redirect(HOST.'sales/distributor');
    }

    try {

        if($event == "close") {
         $status_new = 3;
     } elseif ($event == "restart") {
         $status_new = 1;
     } elseif ($event == "puss") {
         $status_new = 2;
     }else{
         $flashMessenger->setNamespace('error')->addMessage('Invalid Event. Please Check And Try Agian !.');
         $this->_redirect(HOST.'sales/distributor');
     }


     $data = array(
        'status'        => $status_new,
        'remark'        => $remark,
        'updated_at'    => date('Y-m-d H:i:s'),
        'updated_by'    => $userStorage->id
    );


     $where = $QDistributor->getAdapter()->quoteInto('id = ?', $id);
     $updatedcase = $QDistributor->update($data, $where);

     if($updatedcase){
        $flashMessenger->setNamespace('success')->addMessage('Update Data Success!');
    }else{
        $flashMessenger->setNamespace('error')->addMessage('Something went wrong.! ( Update Faill ) Please check and try again.!');
    }


}catch (Exception $e){

    echo '<script>
    parent.palert("Something went wrong.! Please check and try again.!");
    </script>';
    exit;
}

echo '<script>window.history.go(-1);</script>';
exit;
}

?>