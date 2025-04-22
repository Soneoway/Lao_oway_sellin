<?php

$this->_helper->layout->disableLayout();

$id                = $this->getRequest()->getParam('id');
$delivery_sn       = $this->getRequest()->getParam('delivery_sn');
$number_of_package = $this->getRequest()->getParam('number_of_package', 1);
$weight            = $this->getRequest()->getParam('weight', 0);
$volumetric_weight = $this->getRequest()->getParam('volumetric_weight', 0);
$size_1            = $this->getRequest()->getParam('size_1', 0);
$size_2            = $this->getRequest()->getParam('size_2', 0);
$size_3            = $this->getRequest()->getParam('size_3', 0);
$fee               = $this->getRequest()->getParam('fee', 0);
$refer             = $this->getRequest()->getParam('refer', HOST.'delivery/order-control');

$flashMessenger = $this->_helper->flashMessenger;

$userStorage = Zend_Auth::getInstance()->getStorage()->read();
if (!$userStorage) $this->_redirect(HOST);

try {
    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    if (!$id) throw new Exception("Invalid ID", 1);

    $QDeliveryOrder = new Application_Model_DeliveryOrder();
    $where = $QDeliveryOrder->getAdapter()->quoteInto('id = ?', $id);
    $order = $QDeliveryOrder->fetchRow($where);

    if (!$order) throw new Exception("Invalid Delivery order", 2);

    if (isset($order['status']) && $order['status'] != My_Delivery_Order_Status::Waiting)
        throw new Exception("Delivery order status is not waiting", 4);

    if ( isset($order['hub']) && !My_Delivery_Role::isAllow($userStorage->group_id, $userStorage->id, $order['hub'], My_Delivery_Role::Hub) )
        throw new Exception("You have no permission", 7);

    if (!isset($order['carrier_id']) || !$order['carrier_id'])
        throw new Exception("Delivery order not for carrier", 7);

    if ( isset($order['carrier_id']) && !My_Delivery_Role::isAllow($userStorage->group_id, $userStorage->id, $order['carrier_id'], My_Delivery_Role::Carrier) )
        throw new Exception("You have no permission", 8);

    if (!$delivery_sn)
        throw new Exception("Delivery code cannot be blank", 3);

    $where = $QDeliveryOrder->getAdapter()->quoteInto('sn = ?', $delivery_sn);
    $order_check = $QDeliveryOrder->fetchRow($where);
    if ($order_check) throw new Exception("Duplicate Delivery Order SN: " . $delivery_sn, 5);

    $data = array(
        'sn'                => My_String::trim($delivery_sn),
        'number_of_package' => intval($number_of_package),
        'weight'            => floatval($weight),
        'volumetric_weight' => floatval($volumetric_weight),
        'size_1'            => floatval($size_1),
        'size_2'            => floatval($size_2),
        'size_3'            => floatval($size_3),
        'fee'               => floatval($fee),
        'updated_at'        => date('Y-m-d H:i:s'),
        'updated_by'        => $userStorage->id,
    );

    if (isset($order['hub']) && $order['hub']) $status = My_Delivery_Order_Status::Warehouse_To_Hub;
    else $status = My_Delivery_Order_Status::Warehouse_To_Distributor;

    $data['status'] = $status;

    $where = $QDeliveryOrder->getAdapter()->quoteInto('id = ?', $id);
    $QDeliveryOrder->update($data, $where);

    My_Delivery_Order_Status::setStatus($id, $status, $userStorage->id);

    $this->_helper->layout->disableLayout();
    $this->view->refer = $refer;

    if (!isset($order['type']) || $order['type'] != My_Delivery_Type::Outside)
        $this->view->no_print = true;
    else
        $this->view->order_id = $id;

    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    $this->view->error = sprintf("[%d] %s", $e->getCode(), $e->getMessage());
}