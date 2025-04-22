<?php
$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender(true);

if (!$this->getRequest()->isXmlHttpRequest()) exit;

try {
    $id = $this->getRequest()->getParam('id');
    $target = $this->getRequest()->getParam('target');
    $code = $this->getRequest()->getParam('checksum');



    $QTarget = new Application_Model_TargetDistributor();
    $target_check = $QTarget->find($id);
    $target_check = $target_check->current();

    if (!$target_check) exit(json_encode(array(
                                    'code' => -1,
                                    'message' => "Invalid ID", 
                                    )));

    $checksum = hash('sha256', md5( $id ) . $target_check['target']);

    if ($code != $checksum) exit(json_encode(array(
                                    'code' => -2,
                                    'message' => "Invalid ID", 
                                    )));

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    if (!$userStorage) exit(json_encode(array(
                                    'code' => -3,
                                    'message' => "Invalid User", 
                                    )));

    $data = array(
        'target'     => $target,
        'total'      => $target * PRICE,
        'updated_by' => $userStorage->id,
        'updated_at' => date('Y-m-d H:i:s'),
        );

    $where = $QTarget->getAdapter()->quoteInto('id = ?', $id);
    $QTarget->update($data, $where);

    exit(json_encode(array(
                    'code' => 0,
                    'message' => "Success", 
                    )));
    
} catch (Exception $e) {
    exit(json_encode(array(
                    'code' => -99,
                    'message' => $e->getMessage(), 
                    )));
}