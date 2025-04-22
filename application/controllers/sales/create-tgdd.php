<?php
$QStaff = new Application_Model_Staff();
$this->view->salesmans = $QStaff->fetchAll(/*$where*/null, 'username');
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$this->view->uid = $userStorage->id;

$QDistributor = new Application_Model_Distributor();
$where = $QDistributor->getAdapter()->quoteInto('title LIKE ?', '%_KA');
$this->view->distributors = $QDistributor->fetchAll($where, 'title');

$QWarehouse = new Application_Model_Warehouse();
$this->view->warehouses = $QWarehouse->get_cache();
