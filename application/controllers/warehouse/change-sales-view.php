<?php
//get list distributor
$QDistributor = new Application_Model_Distributor();
$distributors = $QDistributor->get_cache();

//get list warehouse
$QWarehouse = new Application_Model_Warehouse();
$warehouses = $QWarehouse->get_cache();

$QGood = new Application_Model_Good();
$where = $QGood->getAdapter()->quoteInto('cat_id = ?', ACCESS_CAT_ID);
$this->view->accessories = $QGood->fetchAll($where, 'name');

$this->view->distributors = $distributors;
$this->view->warehouses = $warehouses;
$goods_cached = $QGood->get_cache();
$this->view->goods_cached = $goods_cached;

$QGoodColor = new Application_Model_GoodColor();
$good_colors_cached = $QGoodColor->get_cache();
$this->view->good_colors_cached = $good_colors_cached;

$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->fetchAll();

$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');
$QChangeSalesOrder = new Application_Model_ChangeSalesOrder();
$QChangeSalesProduct = new Application_Model_ChangeSalesProduct();
$QChangeSalesImei = new Application_Model_ChangeSalesImei();
$QImei = new Application_Model_Imei();
$QDigitalSn = new Application_Model_DigitalSn();
$QWarehouseProduct = new Application_Model_WarehouseProduct();

$changeSalesOrder = null;
if ($id) {
    $whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
    $changeSalesOrder = $QChangeSalesOrder->fetchRow($whereChangeSalesOrder);
}

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

if ($id) {
    if (!$changeSalesOrder) {
        $flashMessenger->setNamespace('error')->addMessage('Change Order is invalid!');
        $this->redirect('/warehouse/change-sales-list');
    }

}

if ($id) {
    $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('changed_id = ?',
        $id);
    $changeSalesProduct = $QChangeSalesProduct->fetchAll($whereChangeSalesProduct);

    if ($changeSalesProduct->count()) {
        $changeSalesImeisList = $changeSalesIotList = $changeSalesImeisReceivedList = $changeSalesImeisLostList = $changeSalesIotImeisLostList = $changeSalesIotImeisReceivedList =
            array();
        $changeSalesImeisDigitalList = $changeSalesImeisReceivedDigitalList = $changeSalesImeisLostDigitalList =
        array();
        
        foreach ($changeSalesProduct as $item) {
            $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?',
                $item['id']);
            $changeSalesImeis = $QChangeSalesImei->fetchAll($whereChangeSalesImei);
            
            if (isset($changeSalesImeis) and $changeSalesImeis) {
                foreach ($changeSalesImeis as $im) {
                    if ($im['cat_id'] == PHONE_CAT_ID) {
                        if ($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                            $changeSalesImeisReceivedList[] = $im['imei'];
                        else
                            $changeSalesImeisLostList[] = $im['imei'];

                        $changeSalesImeisList[] = $im['imei'];
                    }

                    //edit khuan

                    if($im['cat_id'] == IOT_CAT_ID){
                        if($imp['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                             $changeSalesIotImeisReceivedList[] = $im['imei'];
                        else
                            $changeSalesIotList[] = $im['imei'];

                            $changeSalesIotImeisLostList = $im['imei'];
                    }

                    if ($im['cat_id'] == DIGITAL_CAT_ID) {
                        if ($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                            $changeSalesImeisReceivedDigitalList[] = $im['imei'];
                        else
                            $changeSalesImeisLostDigitalList[] = $im['imei'];

                        $changeSalesImeisDigitalList[] = $im['imei'];
                    }
                    //d?i v?i tru?ng hop nhung imei cu
                    if(empty($im['cat_id']))
                    {
                        if ($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                            $changeSalesImeisReceivedList[] = $im['imei'];
                        else
                            $changeSalesImeisLostList[] = $im['imei'];

                        $changeSalesImeisList[] = $im['imei'];                        
                    }
                }
            }
        }

        $this->view->changeSalesImeisReceivedList = $changeSalesImeisReceivedList;
        $this->view->changeSalesImeisLostList = $changeSalesImeisLostList;
        $this->view->changeSalesImeisList = $changeSalesImeisList;
        $this->view->changeSalesImeisReceivedDigitalList = $changeSalesImeisReceivedDigitalList;
        $this->view->changeSalesImeisLostDigitalList = $changeSalesImeisLostDigitalList;
        $this->view->changeSalesImeisDigitalList = $changeSalesImeisDigitalList;

        $this->view->changeSalesIotList = $changeSalesIotList;
        $this->view->changeSalesIotImeisReceivedList = $changeSalesIotImeisReceivedList;
        $this->view->changeSalesIotImeisLostList = $changeSalesIotImeisLostList;
    }

    $this->view->changeSalesProduct = $changeSalesProduct;
}

$this->view->changeSalesOrder = $changeSalesOrder;

$this->view->disabledFields = array(
    'is_changed_wh',
    'cat_id',
    'good_id',
    'good_color',
    'num',
    'sn_iot',
    'type',
    'sns',
    'sns_receive',
    'sns_lost',
    'sn_iot_receives',
    'num_receive',
    'num_lost',
    );
