<?php
$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender(true);

$distributor_id = $this->getRequest()->getParam('distributor_id');
$target_from = TARGET_FROM;
$target_to = TARGET_TO;

$QTarget = new Application_Model_TargetDistributor();
$where = array();
$where[] = $QTarget->getAdapter()->quoteInto('d_id = ?', $distributor_id);
$where[] = $QTarget->getAdapter()->quoteInto('from_date = ?', $target_from);
$where[] = $QTarget->getAdapter()->quoteInto('to_date = ?', $target_to);

$target = $QTarget->fetchRow($where);

if($target)
    exit (json_encode(array('result' => 0))); // có người đký cmnr
else
    exit (json_encode(array('result' => 1))); // ok, chưa ai đăng ký