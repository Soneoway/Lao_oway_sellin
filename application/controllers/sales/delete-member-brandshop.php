<?php

$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');

$db = Zend_Registry::get('db');
$db->beginTransaction();

$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender(true);

try {

        $QMB = new Application_Model_MemberBrandshop();

        if($id){

            $where = $QMB->getAdapter()->quoteInto('id = ?', $id);

            $QMB->delete($where);

            $db->commit();

            $flashMessenger->setNamespace('success')->addMessage('Done.');

            if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']){
                $this->_redirect($_SERVER['HTTP_REFERER']);

            }else{
                $this->_redirect(HOST.'sales/member-brandshop');
            }

        }
    
} catch (Exception $e) {
    $db->rollback();
    $this->view->error = $e->getMessage();

    if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']){
        $this->_redirect($_SERVER['HTTP_REFERER']);

    }else{
        $this->_redirect(HOST.'sales/member-brandshop');
    }

}
