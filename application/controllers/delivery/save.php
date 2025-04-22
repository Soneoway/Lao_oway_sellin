<?php
$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender(true);
$db = Zend_Registry::get('db');
$QDeliveryOrder = new Application_Model_DeliveryOrder();
$db->beginTransaction();

try {
    if (!$this->getRequest()->isXmlHttpRequest())
        throw new Exception("Only accept AJAX request.", -100);

    $sn                = $this->getRequest()->getParam('sn', array());
    $distributor_id    = $this->getRequest()->getParam('distributor_id');

    //$receiver          = $this->getRequest()->getParam('receiver');
    //$receiver = date('YmdHis') . substr(microtime(), 2, 4);
     $shipping_id       = $this->getRequest()->getParam('shipping_id');
    $address           = $this->getRequest()->getParam('address');
    $district          = $this->getRequest()->getParam('district', 0);
    $phone             = $this->getRequest()->getParam('phone');
    $type              = $this->getRequest()->getParam('type');
    $staff_id          = $this->getRequest()->getParam('staff_id');
    $carrier           = $this->getRequest()->getParam('carrier');
    $delivery_sn       = $this->getRequest()->getParam('delivery_sn');
    $number_of_package = $this->getRequest()->getParam('number_of_package');
    $weight            = $this->getRequest()->getParam('weight');
    $choose_hub        = $this->getRequest()->getParam('choose_hub', 0);
    $hub_id            = $this->getRequest()->getParam('hub_id', 0);
    $tracking_no       = $this->getRequest()->getParam('tracking_no', null);

    $tracking_no       = trim($tracking_no);

    //print_r($_GET);die;
    //Tanong
    if ($sn){
        $receiver = date('YmdHis') . substr(microtime(), 2, 4);
        $delivery_sn=$QDeliveryOrder->getDeliveryNo_Ref($receiver);
    }

    if (!$type || !isset( My_Delivery_Type::$name[ $type ] ))
        throw new Exception("Invalid type", 13);

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    if (!$userStorage) throw new Exception("Invalid user", 2);

    if (!$sn || !is_array($sn) || !count($sn))
        throw new Exception("Cannot create delivery order without any order", 4);

    if ($choose_hub == 1) {
        $QHub = new Application_Model_Hub();
        $hub_cache = $QHub->get_all_cache();

        if (!isset( $hub_cache[ $hub_id ] ) ) throw new Exception("Invalid hub ID", 18);

        $hub = $hub_cache[ $hub_id ];

        if ($hub['contact'] != trim($receiver) || $hub['mobile_phone'] != trim($phone) || $hub['address'] != trim($address))
            throw new Exception("Invalid hub info", 19);

    }



    if ($type == My_Delivery_Type::Outside) {
        $where = array();
        $where[] = $QDeliveryOrder->getAdapter()->quoteInto('sn IS NOT NULL AND sn = ?', $delivery_sn);
        $where[] = $QDeliveryOrder->getAdapter()->quoteInto('status <> ?', My_Delivery_Order_Status::Deleted);
        $order_check = $QDeliveryOrder->fetchRow($where);
        if ($order_check) throw new Exception("Duplicate Delivery Order SN: " . $delivery_sn, 5);
    }

    $QMarket = new Application_Model_Market();

    $warehouse = null;

    $locaton_pair = array(
        1 => 8,
        2 => 11,
        3 => 10,
        8 => 1,
        10 => 3,
        11 => 2
    );

    $distributor_list = array();

    $date = date('Y-m-d H:i:s');

    foreach ($sn as $_key => $_sn) {
        $where = $QMarket->getAdapter()->quoteInto('sn = ?', $_sn);
        $market_check = $QMarket->fetchRow($where);

        // if($type == 2 && in_array($carrier, [My_Carrier::Genious,My_Carrier::NKC])){
        if($type == 2 && in_array($carrier, [My_Carrier::NKC])){
            if($tracking_no == ''){

                if(isset($market_check['sn_ref']) and $market_check['sn_ref']){
                    $tracking_no = str_replace('-', '', $market_check['sn_ref']);
                    $tracking_no = str_replace('SO', '', $tracking_no);
                    $tracking_no = 'OPPO' . $tracking_no;
                }else{
                    $tracking_no = 'OPPO' . time();
                }
            }
        }

        if($type == 2 && in_array($carrier, [My_Carrier::YAS])){

            if($tracking_no == ''){

                if(isset($market_check['sn_ref']) and $market_check['sn_ref']){
                    $tracking_no = str_replace('-', '', $market_check['sn_ref']);
                }else{
                    $tracking_no = 'YAS' . time();
                }

            }

        }

        if(isset($market_check['sn_ref']) && isset($market_check['is_kerry']) && $market_check['is_kerry'] == 1 && $type == My_Delivery_Type::Outside && $carrier == My_Carrier::Kerry){

            $QKT = new Application_Model_KerryTransaction();

            $tracking_no = $this->convertSoToOp($market_check['sn_ref']);

                $QKTData = [
                'tracking_no'   => $tracking_no,
                'sn'            => $_sn,
                'type'          => 1,
                'outmysql_time' => $date,
                'delivery_type' => $market_check['is_kerry']
                ];

                $QKT->insert($QKTData);
        }

        if(isset($market_check['sn_ref']) && isset($market_check['is_kerry']) && $market_check['is_kerry'] == 2 && $type == My_Delivery_Type::Outside && $carrier == My_Carrier::JNT){

            $QKT = new Application_Model_KerryTransaction();

            $tracking_no = $this->convertSoToOp($market_check['sn_ref']);

                $QKTData = [
                'tracking_no'   => $tracking_no,
                'sn'            => $_sn,
                'type'          => 1,
                'outmysql_time' => $date,
                'delivery_type' => $market_check['is_kerry']
                ];

                $QKT->insert($QKTData);
        }

        if ($type == My_Delivery_Type::Outside) {
            $where = array();
            $where[] = $QDeliveryOrder->getAdapter()->quoteInto('tracking_no = ?', $tracking_no);
            $where[] = $QDeliveryOrder->getAdapter()->quoteInto('status <> ?', My_Delivery_Order_Status::Deleted);
            $order_check = $QDeliveryOrder->fetchRow($where);
            if ($order_check) throw new Exception("Duplicate Delivery Tracking No.: " . $tracking_no, 5);
        }

        if (!$market_check)
            throw new Exception("Invalid SN: " . $_sn, 7);

        if ($warehouse && $warehouse != $market_check['warehouse_id'] && $locaton_pair[$market_check['warehouse_id']] != $warehouse)
            throw new Exception("Orders in 02 warehouses: " . $_sn, 18);

        $warehouse = $market_check['warehouse_id'];
        $distributor_list[$_sn] = $market_check['d_id'];
    }

    if (!$distributor_id)
        throw new Exception("Choose distributor", 8);

    $QWarehouse = new Application_Model_Warehouse();
    $warehouse_cache = $QWarehouse->get_cache();

    if (!isset($warehouse_cache[$warehouse])) throw new Exception("Invalid warehouse", 17);

    $QDistributor = new Application_Model_Distributor();
    $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
    $distributor_check = $QDistributor->fetchRow($where);

    if (!$distributor_check)
        throw new Exception("Invalid distributor", 5);
/*
    if (empty($receiver))
        throw new Exception("Receiver cannot be blank", 10);*/

    if (empty($address))
        throw new Exception("Address cannot be blank", 11);

    if (!intval($district))
        throw new Exception("District cannot be blank", 12);

    $data = array(
        'created_at'     => date('Y-m-d H:i:s'),
        'created_by'     => intval($userStorage->id),
        'out_time'       => date('Y-m-d H:i:s'),
        'receiver'       => My_String::trim($receiver),
        'address'        => My_String::trim($address),
        'district'       => intval($district),
        'phone_number'   => My_String::trim($phone),
        'shipping_id'    => $shipping_id,
        'distributor_id' => intval($distributor_id),
        'warehouse_id'   => intval($warehouse),
        'type'           => intval($type),
    );

    $data['sn']                = My_String::trim($delivery_sn);


    if ($type == My_Delivery_Type::Inhouse) {
        if (!$staff_id) throw new Exception("Invalid staff", 14);

        $QStaff = new Application_Model_DeliveryMan();
        $staffs_cached = $QStaff->get_cache();

        if (!isset($staffs_cached[$staff_id])) throw new Exception("Wrong staff", 15);

        $data['staff_id'] = $staff_id;
        $data['carrier_id'] = 0;

    } elseif ($type == My_Delivery_Type::Outside) {
        if (!$carrier || !My_Carrier::get($carrier)) throw new Exception("Invalid Carrier", 16);

        $data['staff_id']          = 0;
        $data['carrier_id']        = intval($carrier);

        $data['number_of_package'] = intval($number_of_package);
        $data['weight']            = floatval($weight);
    } elseif ($type == My_Delivery_Type::Hub_Pickup) {
        $data['staff_id'] = 0;
        $data['carrier_id'] = 0;
    }

    if ($choose_hub == 1 && isset($hub) && $hub_id) {
        $data['receiver']     = $hub['contact'];
        $data['phone_number'] = $hub['mobile_phone'];
        $data['address']      = $hub['address'];
        $data['hub'] = $hub_id;

    } else {
        $data['hub'] = 0;
    }

    // rối như canh hẹ, lúc khác viết lại cho gọn gọn tí @@
    if ($type == My_Delivery_Type::Customer_Pickup) {
        $status = My_Delivery_Order_Status::Delivered;
        // $imei_status = My_Imei_Status::DELIVERED;

    } elseif ($type == My_Delivery_Type::Hub_Pickup) {
        $status = My_Delivery_Order_Status::Warehouse_To_Hub;
        // $imei_status = My_Imei_Status::WAREHOUSE_TO_HUB;

    } elseif (isset($hub) && $type == My_Delivery_Type::Inhouse) {
        $status = My_Delivery_Order_Status::Warehouse_To_Hub;
        // $imei_status = My_Imei_Status::WAREHOUSE_TO_HUB;

    } elseif (!isset($hub) && $type == My_Delivery_Type::Inhouse) {
        $status = My_Delivery_Order_Status::Warehouse_To_Distributor;
        // $imei_status = My_Imei_Status::WAREHOUSE_TO_DISTRIBUTOR;

    } elseif ($type == My_Delivery_Type::Outside && $choose_hub == 1 && isset($hub) && $hub_id) {
        $status = My_Delivery_Order_Status::Warehouse_To_Hub;
        // $imei_status = My_Imei_Status::WAREHOUSE_TO_HUB;

    } elseif ($type == My_Delivery_Type::Outside && ($choose_hub <> 1 || !isset($hub) || !$hub_id)) {
        $status = My_Delivery_Order_Status::Warehouse_To_Distributor;
        // $imei_status = My_Imei_Status::WAREHOUSE_TO_DISTRIBUTOR;

    } elseif ($type == My_Delivery_Type::Returned_To_Warehouse) {
        $status = My_Delivery_Order_Status::Delivered;
        // $imei_status = My_Imei_Status::DELIVERED;

    } else {
        $status = My_Delivery_Order_Status::Waiting;
        // $imei_status = false;
    }

    $data['status'] = $status;
    $data['tracking_no'] = $tracking_no;
   // print_r($data);die;
    $id = $QDeliveryOrder->insert($data);

    if (!$id) throw new Exception("Cannot create order", 3);

    $QDeliverySales = new Application_Model_DeliverySales();
    $QImei = new Application_Model_Imei();

    foreach ($sn as $_key => $_sn) {
        $data = array('delivery_order_id' => $id, 'sales_sn' => $_sn);

        //Add fax new delivery
        // if($type == 2 && in_array($carrier, [My_Carrier::Genious,My_Carrier::NKC,My_Carrier::YAS])){
        if($type == 2 && in_array($carrier, [My_Carrier::NKC,My_Carrier::YAS])){
            $data = array('delivery_order_id' => $id, 'sales_sn' => $_sn, 'company' => $carrier);
        }

        $QDeliverySales->insert($data);
        unset($data);

        // if ($imei_status) {
        //     $where = $QImei->getAdapter()->quoteInto('sales_sn = ?', $_sn);
        //     $data = array('imei_status' => $imei_status);
        //     $data = array();

        //     switch ($imei_status) {
        //         case My_Imei_Status::DELIVERED:
        //             $data['place_type'] = My_Place::DISTRIBUTOR;
        //             $data['place_id'] = $distributor_list[$_sn];
        //             break;

        //         default:
        //             $data['place_type'] = My_Place::OUTSIDE;
        //             $data['place_id'] = 0;
        //             break;
        //     }

        //     $QImei->update($data, $where);
        //     unset($data);
        //     unset($where);
        // }
    }

    $result = array(
        'code'   => 1,
        'result' => 'Success',
        'id'     => $id,
        'print'  => 0,
    );

    // if ($type == My_Delivery_Type::Outside
    //     && isset($carrier)
    //     && in_array($carrier, array(My_Carrier::Kerry, My_Carrier::RFE, My_Carrier::Genious)) )
    if ($type == My_Delivery_Type::Outside
        && isset($carrier)
        && in_array($carrier, array(My_Carrier::Kerry, My_Carrier::RFE)) )
        $result['print'] = 1;

    My_Delivery_Order_Status::setStatus($id, $status, $userStorage->id);

    $db->commit();

    exit( json_encode( $result ) );

} catch (Exception $e) {
    $db->rollback();

    exit( json_encode( array(
         'code' => $e->getCode(),
         'result' => $e->getMessage()
    ) ) );
}


