<?php
$id = $this->getRequest()->getParam('id');
$this->_helper->layout->disableLayout();

try {
    if (!$id) throw new Exception("Invalid ID", 1);



    // Delivery Order Data
    $QDeliveryOrder = new Application_Model_DeliveryOrder();
    $where = $QDeliveryOrder->getAdapter()->quoteInto('id = ?', $id);
    $order = $QDeliveryOrder->fetchRow($where);

    // Distributor Data
    $QDistributor   = new Application_Model_Distributor();
    $distributor_cache = $QDistributor->get_cache2();

    //$distributor[] = $distributor_cache[ $order['distributor_id'] ]['name'];
    //$distributor[] = $distributor_cache[ $order['distributor_id'] ]['tel'];

    // Delivery Sale Data
    $QDeliverySales = new Application_Model_DeliverySales();
    $where_ds = $QDeliverySales->getAdapter()->quoteInto('delivery_order_id = ?', $order['id']);
    $delivery_sale = $QDeliverySales->fetchAll($where_ds);

    $sn_list = array();
    for($i=0;$i<count($delivery_sale);$i++) {
        array_push($sn_list, $delivery_sale[$i]['sales_sn']);
    }

    $QMarket = new Application_Model_Market();
    $where_m = $QMarket->getAdapter()->quoteInto('sn IN (?)', $sn_list);
    $result_market = $QMarket->fetchRow($where_m);

//print_r($result_market); die;
    // print_r($sn_list);

    // count phone
    $where_market_phone = array();
    $where_market_phone[] = $QMarket->getAdapter()->quoteInto('sn IN (?)', $sn_list);
    $where_market_phone[] = $QMarket->getAdapter()->quoteInto('cat_id = ?',11);

    $result_phone = $QMarket->fetchAll($where_market_phone);

    $cnt_phone = 0;
    for($i=0;$i<count($result_phone);$i++) { $cnt_phone = $cnt_phone + $result_phone[$i]['num']; }

    // count bag
    $where_market_bag[] = $QMarket->getAdapter()->quoteInto('sn IN (?)', $sn_list);
    $where_market_bag[] = $QMarket->getAdapter()->quoteInto('cat_id = ?',12);
    $where_market_bag[] = $QMarket->getAdapter()->quoteInto('good_id = ?',127);
    $result_bag = $QMarket->fetchAll($where_market_bag);

    $cnt_bag = 0;
    for($i=0;$i<count($result_bag);$i++) { $cnt_bag = $cnt_bag + $result_bag[$i]['num']; }


    // count accessory
    $where_market_acc[] = $QMarket->getAdapter()->quoteInto('sn IN (?)', $sn_list);
    $where_market_acc[] = $QMarket->getAdapter()->quoteInto('cat_id IN (?)',array(12,13,14));
    $where_market_acc[] = $QMarket->getAdapter()->quoteInto('good_id <> ?',127);
    $result_acc = $QMarket->fetchAll($where_market_acc);

    $cnt_acc = 0;
    for($i=0;$i<count($result_acc);$i++) { $cnt_acc = $cnt_acc + $result_acc[$i]['num']; }


//print_r($result_market); 
//echo $result_market['sn_ref'];
//die;

    // prepare data array
    $info['address']    = $order['address'];
    $info['shipping_id']   = $order['shipping_id'];
    $info['created_at']    = $order['created_at'];
    $info['d_id']       = $order['distributor_id'];
    $info['title']      = $distributor_cache[ $order['distributor_id'] ]['title'];
    $info['d_contact']  = $distributor_cache[ $order['distributor_id'] ]['name'];
    $info['d_code']     = $distributor_cache[ $order['distributor_id'] ]['code'];
    $info['sales_sn']   = $delivery_sale['sales_sn'];
    $info['sales_sn_ref'] = $result_market['sn_ref'];
    $info['add_time'] = $result_market['add_time'];
    $info['add_time'] = $result_market['add_time'];
    $info['tel']        = $order['phone_number'];
    $info['cnt_phone']  = $cnt_phone;
    $info['cnt_bag']    = $cnt_bag;
    $info['cnt_acc']    = $cnt_acc;

    if (!$order) throw new Exception("Invalid Order", 2);
    if (!isset($order['type']) || My_Delivery_Type::Outside != $order['type']) throw new Exception("Only print delivery order for Outside type", 3);
    if (!isset($order['carrier_id']) || !isset( My_Carrier::$name[ $order['carrier_id'] ] )) throw new Exception("Invalid carrier", 4);
    
    switch ($order['carrier_id']) {
        case My_Carrier::Kerry:
            $this->_helper->viewRenderer->setRender('bill/kerry');
            break;
        case My_Carrier::RFE:
            $this->_helper->viewRenderer->setRender('bill/rfe');
            break;
        // case My_Carrier::Genious:
        //     $this->_helper->viewRenderer->setRender('bill/genious');
        //     break;
        case My_Carrier::Road:
            $this->_helper->viewRenderer->setRender('bill/road');
            break;
        case My_Carrier::NKC:
            $this->_helper->viewRenderer->setRender('bill/nkc');
            break;
        case My_Carrier::YAS:
            $this->_helper->viewRenderer->setRender('bill/yas');
            break;
        case My_Carrier::JNT:
            $this->_helper->viewRenderer->setRender('bill/jnt');
            break;

        default:
            throw new Exception("Invalid carrier");
            break;
    }

    $this->view->order = $info;
    $this->view->refer = My_Url::refer('delivery');
} catch (Exception $e) {
    $this->view->error = sprintf("Error [%d] %s", $e->getCode(), $e->getMessage());
}
