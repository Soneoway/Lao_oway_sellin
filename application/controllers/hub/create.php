<?php 
$QArea   = new Application_Model_Area();
$QRegion = new Application_Model_RegionalMarket();
$this->view->areas   = $QArea->get_cache();
$this->view->regions = $QRegion->nget_all_province_with_area_cache();
$this->view->refer   = My_Url::refer('hub');

$db = Zend_Registry::get('db');

$flashMessenger = $this->_helper->flashMessenger;
$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages         = $flashMessenger->setNamespace('error')->getMessages();