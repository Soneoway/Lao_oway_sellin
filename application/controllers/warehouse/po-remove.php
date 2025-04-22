<?php
$sn = $this->getRequest()->getParam('sn');
$flashMessenger = $this->_helper->flashMessenger;
$back_url = $this->getRequest()->getParam('back_url');

if ($sn) {
    $QPo = new Application_Model_Po();

    $where = $QPo->getAdapter()->quoteInto('sn = ?', $sn);
    $PO = $QPo->fetchAll($where);

    if ($PO) {

        try{
            $QPo->delete($where);

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            //todo log
            $ip = $this->getRequest()->getServer('REMOTE_ADDR');

            $info = 'Remove: From warehouse - Order number: '.$PO->sn;

            $QLog = new Application_Model_Log();

            $QLog->insert( array (
                'info' => $info,
                'user_id' => $userStorage->id,
                'ip_address' => $ip,
                'time' => date('Y-m-d H:i:s'),
            ) );

            $flashMessenger->setNamespace('success')->addMessage('Done!');
        }catch (Exception $e){
            $flashMessenger->setNamespace('error')->addMessage('Cannot delete, please try again!');
        }
        $this->_redirect(HOST.'warehouse/in');
    }
}

$flashMessenger->setNamespace('error')->addMessage('Wrong ID!');
$this->_redirect(HOST.'warehouse/in');