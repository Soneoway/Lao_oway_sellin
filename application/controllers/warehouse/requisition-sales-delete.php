<?php 
$id          = $_POST['id'];
$reason         = $_POST['reason'];
 $userStorage = Zend_Auth::getInstance()->getStorage()->read();

$data['delete_status'] = 1 ;
$data['delete_by'] = $userStorage->id; ;
$data['delete_reason'] = $reason ;
$data['delete_date'] = date('Y-m-d H:i:s') ;
$QChangeSalesOrder = new Application_Model_ChangeSalesOrder();
$where = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
$QChangeSalesOrder->update($data, $where);


$this->redirect('/warehouse/change-sales-list?cancel=1');