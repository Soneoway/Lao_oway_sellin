<?php
$QStaff = new Application_Model_Staff();
$this->view->salesmans = $QStaff->get_cache();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$this->view->uid = $userStorage->id;

$QWarehouse = new Application_Model_Warehouse();
$this->view->warehouses = $QWarehouse->get_cache();