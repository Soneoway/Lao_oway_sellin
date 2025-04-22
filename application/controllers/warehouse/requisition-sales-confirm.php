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

        if (!in_array($changeSalesOrder['status'], array(CHANGE_ORDER_STATUS_SCANNED_OUT))){
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

        $changed_sn = $changeSalesOrder['changed_sn'];

        if ($id){

            $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('changed_id = ?', $id);
            $changeSalesProducts = $QChangeSalesProduct->fetchAll($whereChangeSalesProduct);

            foreach ($changeSalesProducts as $item){
                // kiem tra so luong
                // change WH
                if ($changeSalesOrder['is_changed_wh']){

                    $storageParams = array(
                        'warehouse_id'      => $changeSalesOrder['old_id'],
                        'cat_id'            => $item['cat_id'],
                        'good_id'           => $item['good_id'],
                        'good_color_id'     => $item['good_color'],
                    );

                    $storageParams['current_change_order_id']   = $id;

                    $storageParams['not_get_ilike_bad_count']   =
                    $storageParams['not_get_digital_bad_count'] =
                    $storageParams['not_get_imei_bad_count']    =
                    $storageParams['not_get_damage_product_count'] =
                    $storageParams['not_get_total']             =
                    $storageParams['not_order']                 =
                        true;


                    $storageParams['not_get_ilike_count'] = true;


                    $storage            = $QGood->fetchPaginationStorage(1, null, $total2, $storageParams);

                    $current_order      = isset($storage[0]['current_order']) ? $storage[0]['current_order'] : 0;
                    $current_change_order      = isset($storage[0]['current_change_order']) ? $storage[0]['current_change_order'] : 0;
                    if ($item['cat_id']==PHONE_CAT_ID and $changeSalesOrder['type']==FOR_DEMO){
                        $current_order          = isset($storage[0]['current_order_demo']) ? $storage[0]['current_order_demo'] : 0;
                        $current_change_order   = isset($storage[0]['current_change_order_demo']) ? $storage[0]['current_change_order_demo'] : 0;
                    }else if ($item['cat_id']==PHONE_CAT_ID and $changeSalesOrder['type']==FOR_APK){
                        $current_order          = isset($storage[0]['current_order_apk']) ? $storage[0]['current_order_apk'] : 0;
                        $current_change_order   = isset($storage[0]['current_change_order_apk']) ? $storage[0]['current_change_order_apk'] : 0;
                    }

                    $current_storage    = 0;

                    if (isset($storage[0]) and $storage[0]) {
                        switch ($item['cat_id']){
                            case DIGITAL_CAT_ID:
                                $current_storage = (isset($storage[0]['digital_count']) and $storage[0]['digital_count']) ? $storage[0]['digital_count'] : 0;
                                break;
                            case PHONE_CAT_ID:
                                $current_storage = (isset($storage[0]['imei_count']) and $storage[0]['imei_count']) ? $storage[0]['imei_count'] : 0;
                                if ($changeSalesOrder['type']==FOR_DEMO){
                                    $current_storage = (isset($storage[0]['imei_demo_count']) and $storage[0]['imei_demo_count']) ? $storage[0]['imei_demo_count'] : 0;
                                }else if ($changeSalesOrder['type']==FOR_APK){
                                    $current_storage = (isset($storage[0]['imei_apk_count']) and $storage[0]['imei_apk_count']) ? $storage[0]['imei_apk_count'] : 0;
                                }
                                break;
                            case ILIKE_CAT_ID:
                                $current_storage = (isset($storage[0]['ilike_count']) and $storage[0]['ilike_count']) ? $storage[0]['ilike_count'] : 0;
                                break;
                            case ACCESS_CAT_ID:
                                $current_storage = (isset($storage[0]['product_count']) and $storage[0]['product_count']) ? $storage[0]['product_count'] : 0;
                                break;

                        }
                    }

                    if ( ($current_storage - $current_order - $current_change_order) < $item['num'] ) {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - the quantity '.$goods_cached[$item['good_id']].' / '.$good_colors_cached[$item['good_color']].' is not enough in this Warehouse! ('.($current_storage - $current_order - $current_change_order).'<'.$item['num'].')</div>';
                        exit;
                    }

                }
                // change Retailer
                else {

                    if ($item['cat_id'] == PHONE_CAT_ID){
                        $whereImei = $QImei->getAdapter()->quoteInto('distributor_id = ?', $changeSalesOrder['old_id']);
                        $checked_result = $QImei->fetchAll($whereImei);

                        if ( $checked_result->count() < $item['num'] ){
                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - the quantity '.$goods_cached[$item['good_id']].' / '.$good_colors_cached[$item['good_color']].' is not enough!</div>';
                            exit;
                        }
                    } elseif ($item['cat_id'] == DIGITAL_CAT_ID) {
                        $whereDigitalSn = $QDigitalSn->getAdapter()->quoteInto('distributor_id = ?', $changeSalesOrder['old_id']);
                        $checked_result = $QDigitalSn->fetchAll($whereDigitalSn);

                        if ( $checked_result->count() < $item['num'] ){
                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - the quantity '.$goods_cached[$item['good_id']].' / '.$good_colors_cached[$item['good_color']].' is not enough!</div>';
                            exit;
                        }
                    }

                }


                if ($item['cat_id'] == PHONE_CAT_ID){
                    $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?', $item['id']);
                    $scannedList = $QChangeSalesImei->fetchAll($whereChangeSalesImei);

                    if ($scannedList->count() != $item['num']){
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Please Scan OUT enough</div>';

                        exit;
                    }

                    foreach ($scannedList as $scannedSN){

                        $whereImei = $QImei->getAdapter()->quoteInto('imei_sn = ?', $scannedSN['imei']);
                        $imeiInfo = $QImei->fetchRow($whereImei);
                        if (!$imeiInfo){
                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - Invalid SN: '.$scannedSN['imei'].'</div>';

                            exit;
                        }

                        // if ($imeiInfo['out_date']){
                        //     echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        //     echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        //     echo '<div class="alert alert-error">Failed - was OUT: '.$scannedSN['imei'].'</div>';

                        //     exit;
                        // }

                        //update to imei
                        $data = array();
                        $data['status'] = IMEI_STATUS_ON_CHANGE; //on changing
                        $data['changed_sn'] = $changed_sn;

                        $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $scannedSN['imei']);
                        $QImei->update($data, $where);

                    }

                }
                elseif ($item['cat_id'] == DIGITAL_CAT_ID){

                    $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?', $item['id']);
                    $scannedList = $QChangeSalesImei->fetchAll($whereChangeSalesImei);

                    if ($scannedList->count() != $item['num']){
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Please Scan OUT enough</div>';

                        exit;
                    }

                    foreach ($scannedList as $scannedSN){

                        $whereDigitalSn = $QDigitalSn->getAdapter()->quoteInto('sn = ?', $scannedSN['imei']);
                        $Info = $QDigitalSn->fetchRow($whereDigitalSn);
                        if (!$Info){
                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - Invalid SN: '.$scannedSN['imei'].'</div>';

                            exit;
                        }

                        if ($Info['out_date']){
                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - was OUT: '.$scannedSN['imei'].'</div>';

                            exit;
                        }

                        //update to Digital SN
                        $data = array();
                        $data['status'] = IMEI_STATUS_ON_CHANGE; //on changing
                        $data['changed_sn'] = $changed_sn;

                        $where = $QDigitalSn->getAdapter()->quoteInto('sn = ?', $scannedSN['imei']);
                        $QDigitalSn->update($data, $where);
                    }
                } else {

                    // insert Warehouse product
                    $where = array();
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('cat_id = ?',        $item['cat_id']);
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_id = ?',       $item['good_id']);
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_color = ?',    $item['good_color']);
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('warehouse_id = ?',  $changeSalesOrder['old_id']);

                    $result = $QWarehouseProduct->fetchRow($where);

                    if ($result) {

                        $where = $QWarehouseProduct->getAdapter()->quoteInto('id = ?', $result['id']);

                        //update quantity
                        $QWarehouseProduct->update(array(
                            'quantity' => ($result['quantity'] - $item['num'])
                        ), $where);

                    }
                }
            }

            // update
            $whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
            $QChangeSalesOrder->update(array(
                'status'                => CHANGE_ORDER_STATUS_ON_CHANGE,
                'confirmed_out_at'        => date('Y-m-d H:i:s'),
                'confirmed_out_by'        => $userStorage->id,
            ), $whereChangeSalesOrder);

        }


        $db->commit();

        $info = 'CHANGE ORDER - CONFIRMED OUT: ' . $changeSalesOrder['changed_sn'];
        $QLog->insert( array (
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => date('Y-m-d H:i:s'),
        ) );

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

    if (!in_array($changeSalesOrder['status'], array(CHANGE_ORDER_STATUS_SCANNED_OUT))){
        $flashMessenger->setNamespace('error')->addMessage('Status is invalid!');
        $this->redirect('/warehouse/change-sales-list');
    }
}

if ($id){
    $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('changed_id = ?', $id);
    $changeSalesProduct = $QChangeSalesProduct->fetchAll($whereChangeSalesProduct);

    $changeSalesImeisList = $changeSalesImeisReceivedList = $changeSalesImeisLostList = array();
    $changeSalesImeisDigitalList = $changeSalesImeisReceivedDigitalList = $changeSalesImeisLostDigitalList = array();
    
    if ($changeSalesProduct->count()){

        foreach ($changeSalesProduct as $item){
            $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?', $item['id']);
            $changeSalesImeis = $QChangeSalesImei->fetchAll($whereChangeSalesImei);

            if ($changeSalesImeis->count()){ 
                foreach ($changeSalesImeis as $im){
                 
                 if($im['cat_id'] == PHONE_CAT_ID)
                 {
                    if ($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                        $changeSalesImeisReceivedList[] = $im['imei'];
                    else
                        $changeSalesImeisLostList[] = $im['imei'];

                    $changeSalesImeisList[] = $im['imei'];
                 }
                 
                 if($im['cat_id'] == DIGITAL_CAT_ID)
                 {
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
    'sns',
    'type',
);

$this->view->hideFields = array(
    'sns_receive',
    'sns_lost',
    'num_receive',
    'num_lost',
    'remove-sales',
    'sns_digital_receive',
    'sns_digital_lost'
);