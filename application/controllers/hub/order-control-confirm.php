<?php
$id = $this->getRequest()->getParam('id');
$userStorage = Zend_Auth::getInstance()->getStorage()->read();

if (!$userStorage) $this->_redirect(HOST);

try {
    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    if (!$id) throw new Exception("Invalid ID", 8);
    $id = intval($id);

    $QDeliveryOrder = new Application_Model_DeliveryOrder();
    $where = $QDeliveryOrder->getAdapter()->quoteInto('id = ?', $id);
    $order = $QDeliveryOrder->fetchRow($where);

    $flashMessenger = $this->_helper->flashMessenger;

    if (!$order)
        throw new Exception("Invalid Delivery order", 1);

    if (isset($order['status']) && $order['status'] == My_Delivery_Order_Status::Hub_To_Distributor)
        throw new Exception("Delivery order is on the way to distributor", 4);

    if (isset($order['status']) && $order['status'] == My_Delivery_Order_Status::Deleted)
        throw new Exception("Delivery order was deleted", 2);

    if (isset($order['status']) && $order['status'] == My_Delivery_Order_Status::Delivered)
        throw new Exception("Delivery order was delivered", 3);

    if (!isset($order['hub']) || !$order['hub'])
        throw new Exception("Delivery order not in hub", 7);

    if (isset($order['hub']) || isset($order['carrier_id'])) {
        if ( ! (isset($order['hub']) && My_Delivery_Role::isAllow($userStorage->group_id, $userStorage->id, $order['hub'], My_Delivery_Role::Hub))
            && ! (isset($order['carrier_id']) && My_Delivery_Role::isAllow($userStorage->group_id, $userStorage->id, $order['carrier_id'], My_Delivery_Role::Carrier)) )
            throw new Exception("You have no permission", 6);
    }

    $data = array(
        'status' => My_Delivery_Order_Status::Hub_To_Distributor,
    );
    $QDeliveryOrder->update($data, $where);

    My_Delivery_Order_Status::setStatus($id, My_Delivery_Order_Status::Hub_To_Distributor, $userStorage->id);

    // // cập nhật trạng thái IMEI, để trigger update tồn kho
    // $sql = "UPDATE imei, delivery_sales
    //     SET imei.place_id=?, imei.place_type=?, imei.imei_status=?
    //     WHERE imei.sales_sn=delivery_sales.sales_sn
    //     AND delivery_sales.delivery_order_id=?";

    // $db->query($sql, array(
    //     $order['hub'],
    //     My_Place::HUB,
    //     My_Imei_Status::HUB_TO_DISTRIBUTOR,
    //     $id
    // ));
    // //

    $flashMessenger->setNamespace('success')->addMessage('Success');

    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
}

$refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : (HOST.'hub/order-control');
$this->_redirect($refer);