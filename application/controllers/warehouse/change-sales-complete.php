<?php

set_time_limit(0);
ini_set('memory_limit', -1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

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

$id                 = $this->getRequest()->getParam('id');
$QChangeSalesOrder  = new Application_Model_ChangeSalesOrder();
$QChangeSalesProduct = new Application_Model_ChangeSalesProduct();
$QChangeSalesImei   = new Application_Model_ChangeSalesImei();
$QImei              = new Application_Model_Imei();
$QDigitalSn         = new Application_Model_DigitalSn();
$QWarehouseProduct  = new Application_Model_WarehouseProduct();

$changeSalesOrder   = null;
if ($id){
    $whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
    $changeSalesOrder = $QChangeSalesOrder->fetchRow($whereChangeSalesOrder);
}

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$date_now = date('Y-m-d H:i:s');

//save form
if($this->getRequest()->isPost()) {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();

    echo '<link href="/css/bootstrap.min.css" rel="stylesheet">';

    $userStorage        = Zend_Auth::getInstance()->getStorage()->read();

    $QLog               = new Application_Model_Log();
    $ip                 = $this->getRequest()->getServer('REMOTE_ADDR');

    // check id
    if ($id){
        if (!$changeSalesOrder){
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Change Order is invalid!</div>';
            exit;
        }

        if (!in_array($changeSalesOrder['status'], array(CHANGE_ORDER_STATUS_SCANNED_IN , CHANGE_ORDER_STATUS_FULL_RECEIVED , CHANGE_ORDER_STATUS_PARTIALLY_RECEIVED))){
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Change Order Status is invalid!</div>';
            exit;
        }
    }

    try
    {
        $db = Zend_Registry::get('db');
        $db->beginTransaction();


        if ($id){
            
            if($changeSalesOrder['status'] == CHANGE_ORDER_STATUS_FULL_RECEIVED)
            {
                // update
                $whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
                $QChangeSalesOrder->update(array(
                    'status'                => CHANGE_ORDER_STATUS_COMPLETED,
                    'completed_date'        => $date_now,
                    'completed_user'        => $userStorage->id,
                ), $whereChangeSalesOrder);

                // update borrowing status for app
                if(isset($changeSalesOrder['borrowing_id']) && $changeSalesOrder['borrowing_id']){

                    $QBL = new Application_Model_BorrowingList();

                    $getBorrowingList = $QBL->getDetailsBorrowingByID($changeSalesOrder['borrowing_id']);

                    if($getBorrowingList && !$getBorrowingList['wms_return_date']){

                        //status 13 is receive
                        $update_borrowing = array(
                            'read_data' => 1,
                            'update_datetime' => $date_now,
                            'status' => 13
                        );

                        $where_update = $QBL->getAdapter()->quoteInto('id = ?', $changeSalesOrder['borrowing_id']);
                        $status_update_13 = $QBL->update($update_borrowing,$where_update);

                    }

                }

                // START : stamp stock shop brand shop

                $QMSS  = new Application_Model_MapStockShop();

                $getMapStockShop = $QMSS->getDetailsStockShopByWarehouse($changeSalesOrder['old_id']);
                if(count($getMapStockShop) == 1){

                    $getImeiCo = $QChangeSalesOrder->getImeiCo($changeSalesOrder['id']);

                    if($getImeiCo){

                        $whereUpdateImeiStockShop = $QImei->getAdapter()->quoteInto('imei_sn in (?)', $getImeiCo);
                        $QImei->update(array(
                            'stock_shop_id'          => null,
                            'stock_shop_status'      => null,
                            'stock_shop_date'        => null,
                            'stock_shop_scan'        => null
                        ), $whereUpdateImeiStockShop);
                    }

                }

                $getMapStockShop = $QMSS->getDetailsStockShopByWarehouse($changeSalesOrder['new_id']);
                if(count($getMapStockShop) == 1){

                    $getImeiCo = $QChangeSalesOrder->getImeiCo($changeSalesOrder['id']);

                    if($getImeiCo){

                        $whereUpdateImeiStockShop = $QImei->getAdapter()->quoteInto('imei_sn in (?)', $getImeiCo);
                        $QImei->update(array(
                            'stock_shop_id'          => $getMapStockShop[0]['store_code'],
                            'stock_shop_status'      => 1,
                            'stock_shop_date'        => $date_now,
                            'stock_shop_scan'        => $date_now
                        ), $whereUpdateImeiStockShop);
                    }

                }

                // END : stamp stock shop brand shop

            }
            
            else
            {
                 // update
                    $whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
                    $QChangeSalesOrder->update(array(
                        'status'                => CHANGE_ORDER_STATUS_LOST_RECEIVED,
                        'completed_date'        => $date_now,
                        'completed_user'        => $userStorage->id,
                    ), $whereChangeSalesOrder);
            }
            

        }

        $info = 'CHANGE ORDER - COMPLETED: ' . $changeSalesOrder['changed_sn'];
        $QLog->insert( array (
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => $date_now,
        ) );

        $db->commit();

        if(isset($status_update_13) && $status_update_13){
            $data_curl_13 = array('code' => $getBorrowingList['code'], 'status' => 13, 'rq' => $getBorrowingList['rq']);

            $handle = curl_init(API_IOPPO_URL . 'warehouse-notification');
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $data_curl_13);
            curl_exec($handle);
            curl_close($handle);
        }

        $flashMessenger->setNamespace('success')->addMessage('Done!');

        echo '<script>parent.location.href="/warehouse/change-sales-list"</script>';
        exit;

    } catch (Exception $e){
        $db->rollback();

        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
        echo '<div class="alert alert-error">Failed - '.$e->getMessage().'</div>';
        exit;
    }
}

if ($id){
    if (!$changeSalesOrder){
        $flashMessenger->setNamespace('error')->addMessage('Change Order is invalid!');
        $this->redirect('/warehouse/change-sales-list');
    }

    if (!in_array($changeSalesOrder['status'], array(CHANGE_ORDER_STATUS_SCANNED_IN ,CHANGE_ORDER_STATUS_FULL_RECEIVED, CHANGE_ORDER_STATUS_PARTIALLY_RECEIVED))){
        $flashMessenger->setNamespace('error')->addMessage('Status is invalid!');
        $this->redirect('/warehouse/change-sales-list');
    }
}

if ($id){
    $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('changed_id = ?', $id);
    $changeSalesProduct = $QChangeSalesProduct->fetchAll($whereChangeSalesProduct);

    if ($changeSalesProduct->count()){
        $changeSalesImeisList = $changeSalesImeisReceivedList = $changeSalesImeisLostList = $changeSalesIotImeisReceivedList = $changeSalesIotList = $changeSalesIotImeisLostList = array();
        $changeSalesImeisDigitalList = $changeSalesImeisReceivedDigitalList = $changeSalesImeisLostDigitalList =
        array();
        foreach ($changeSalesProduct as $item){
            $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?', $item['id']);
            $changeSalesImeis = $QChangeSalesImei->fetchAll($whereChangeSalesImei);

            if ($changeSalesImeis->count()){
                foreach ($changeSalesImeis as $im){
                    if ($im['cat_id'] == PHONE_CAT_ID) {
                        if ($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                            $changeSalesImeisReceivedList[] = $im['imei'];
                        else
                            $changeSalesImeisLostList[] = $im['imei'];

                        $changeSalesImeisList[] = $im['imei'];
                    }

                       //edit khuan

                    if($im['cat_id'] == IOT_CAT_ID){
                        if($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                            $changeSalesIotImeisReceivedList[] = $im['imei'];
                        else
                            $changeSalesIotImeisLostList = $im['imei'];

                            $changeSalesIotList[] = $im['imei'];
                    }

                    //end
                    //end

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
    'sn_iot_receives',
    'type',
    'sns',
    'sns_receive',
    'sns_lost',
    'num_receive',
    'num_lost',
);