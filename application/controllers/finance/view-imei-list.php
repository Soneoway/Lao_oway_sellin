<?php

$sales_sn = $this->getRequest()->getParam('sales_sn');
$good_id = $this->getRequest()->getParam('good_id');
$good_color = $this->getRequest()->getParam('good_color');

$QImei         = new Application_Model_Imei();
$imei_list = $QImei->getImeiBySalesOrder(trim($sales_sn,','),$good_id,$good_color);

//print_r($imei_list);
$this->view->imei_list = $imei_list;

$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setRender('/partials/view-imei-list');

?>