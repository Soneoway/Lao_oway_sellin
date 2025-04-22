<?php
$id = $this->getRequest()->getParam('id');

try {
    if (!$id) throw new Exception("Invalid ID", 1);

    $QDeliveryOrder = new Application_Model_DeliveryOrder();
    $where = $QDeliveryOrder->getAdapter()->quoteInto('id = ?', $id);
    $order = $QDeliveryOrder->fetchRow($where);

    if (!$order) throw new Exception("Wrong ID", 2);

    if (!isset($order['hub']) || !$order['hub'])
        throw new Exception("Delivery order not in hub", 3);

    $this->view->order = $order;

    /////////////////////////////
    /// Lấy danh sách đơn hàng thuộc vận đơn
    $QDeliverySales = new Application_Model_DeliverySales();
    $where = $QDeliverySales->getAdapter()->quoteInto('delivery_order_id = ?', $id);
    $sales = $QDeliverySales->fetchAll($where);
    $sn = array();

    foreach ($sales as $key => $value)
        $sn[] = $value['sales_sn'];
    
    $limit = null;
    $total = 0;
    $page = 1;
    $params = array('sn' => $sn);

    $QGood = new Application_Model_Good();
    $params['group_sn']      = 1;
    $params['isbacks']       = 0;
    $params['outmysql_time'] = 1;
    $params['status']        = 1;
    // $params['product_out']    = 1;

    $QMarket = new Application_Model_Market();

    $params['get_fields'] = array(
        'sn',
        'd_id',
        'pay_time',
        'shipping_yes_time',
        'outmysql_time',
        'warehouse_id',
        'status',
    );

    $markets = $QMarket->fetchPagination($page, $limit, $total, $params);
    $this->view->markets  = $markets;
    $this->view->all_goods  = $QGood->get_cache();
    $QGoodCategory                = new Application_Model_GoodCategory();
    $this->view->good_categories  = $QGoodCategory->get_cache();

    $QDistributor                 = new Application_Model_Distributor();
    $this->view->distributorsList = $QDistributor->get_all_cache();

    $QGoodColor                   = new Application_Model_GoodColor();
    $this->view->colors_list      = $QGoodColor->get_cache();
    
    $QWarehouse                   = new Application_Model_Warehouse();
    $this->view->warehouses       = $QWarehouse->get_cache();
    //////////////////////////
    /// END Lấy danh sách đơn hàng thuộc vận đơn

    $this->view->distributor_cache = $QDistributor->get_cache();

    if ($order['type'] == My_Delivery_Type::Inhouse) {
        $QStaff = new Application_Model_DeliveryMan();
        $this->view->staffs = $QStaff->get_cache();
    }

    $QHubDistrict = new Application_Model_HubDistrict();
    $this->view->hub = $QHubDistrict->checkDistrictHub($order['district']);

    $flashMessenger = $this->_helper->flashMessenger;
    $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();

} catch (Exception $e) {
    $flashMessenger = $this->_helper->flashMessenger;
    $flashMessenger->setNamespace('error')->addMessage(sprintf("[%d] %s", $e->getCode(), $e->getMessage()));
    $this->_redirect(HOST.'hub/order-control');
}