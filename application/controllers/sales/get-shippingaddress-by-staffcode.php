<?php 
// echo "TREE"; die;
$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$messages_success = $flashMessenger->setNamespace('success')->getMessages();

$staff_code     = $this->getRequest()->getParam('staff_code');
$r_name			= $this->getRequest()->getParam('r_name');

$QShippingAdd = new Application_Model_ShippingAddress();
$shipping_add  = $QShippingAdd->get_cache();
// $staff_code = 5802915;
$provinc = $QShippingAdd->get_shipping_address_staff($staff_code);
$staff   = $QShippingAdd->get_staff_id_name($staff_code);

echo json_encode(['status' => 200,'message' => 'Done.','data' => $provinc, 'staff'=> $staff]);
exit();