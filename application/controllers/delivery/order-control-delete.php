<?php
$id = $this->getRequest()->getParam('id');
$is_kerry = $this->getRequest()->getParam('is_kerry');

$userStorage = Zend_Auth::getInstance()->getStorage()->read();
if (!$userStorage) $this->_redirect(HOST);

try {
    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    if (!$id) throw new Exception("Invalid ID", 3);
    $id = intval($id);

    $QDeliveryOrder = new Application_Model_DeliveryOrder();
    $where = $QDeliveryOrder->getAdapter()->quoteInto('id = ?', $id);
    $order = $QDeliveryOrder->fetchRow($where);

    $flashMessenger = $this->_helper->flashMessenger;
    if (!$order)
        throw new Exception("Invalid Delivery order", 1);

    if (isset($order['status'])
        && !in_array($order['status'], array(
            My_Delivery_Order_Status::Waiting,
            My_Delivery_Order_Status::Warehouse_To_Hub,
            My_Delivery_Order_Status::Warehouse_To_Distributor,
        )))
        throw new Exception("Delivery order status is delivered or in hub, cannot delete.", 2);

    switch ($is_kerry) {
        case '1':
            $QKT = new Application_Model_KerryTransaction();

            $arrGetSN = $QDeliveryOrder->getSNByDNID($id);

            if(!$arrGetSN)
                throw new Exception("Invalid Delivery Order Kerry");

            $QKSSL = new Application_Model_KerryShipmentStatusLog();

            $sn = isset($arrGetSN['sales_sn']) ? $arrGetSN['sales_sn'] : '';

            $where = [];
            $where[] = $QKT->getAdapter()->quoteInto('type = ?', '1');
            $where[] = $QKT->getAdapter()->quoteInto('delivery_type = ?', $is_kerry);
            $where[] = $QKT->getAdapter()->quoteInto('tracking_no = ?', $order['tracking_no']);

            $QKT_check = $QKT->fetchALL($where);

            if(count($QKT_check) == 1){

                if($QKT_check[0]['is_co'] == 1){
                    $getDetailMarket = $QKSSL->getDetailCO($sn);
                }else{
                    $getDetailMarket = $QKSSL->getDetailMarket($sn);
                }

                $response = $this->sendShipmentInfoRollBack($getDetailMarket);

                $arrResponse = json_decode($response);

                $status_code = null;
                $status_desc = null;

              if(isset($arrResponse->res->shipment->status_code)){
                $status_code = $arrResponse->res->shipment->status_code;
              }

              if(is_null($status_code)){

                throw new Exception("API Remove Shipment Info Error.");

              }else{

                if($status_code != '000'){

                    if(isset($arrResponse->res->shipment->status_desc)){
                        $status_desc = $arrResponse->res->shipment->status_desc;
                        throw new Exception($status_desc);
                    }

                    throw new Exception("API Remove Shipment Info Error.");
                }else{

                    $QKT = new Application_Model_KerryTransaction();

                    $data = [
                    'type' => 2
                    ];

                    $where = [];
                    $where[] = $QKT->getAdapter()->quoteInto('sn = ?', $sn);

                    $QKT->update($data, $where);
                }

              }

            }else if(count($QKT_check) > 1){

                //get last id QKT
                $QKT_id = null;
                $arr_sn = [];
                foreach ($QKT_check as $key) {
                    if($sn == $key['sn']){
                        $QKT_id = $key['id'];
                        array_push($arr_sn, $key['sn']);
                    }
                }

                $arr_unique = array_unique($arr_sn);
                $arr_duplicates = array_diff_assoc($arr_sn, $arr_unique);

                $QKT = new Application_Model_KerryTransaction();

                    $data = [
                    'type' => 2
                    ];

                    $where = [];
                    if(count($arr_duplicates) > 0){
                        $where[] = $QKT->getAdapter()->quoteInto('id = ?', $QKT_id);
                        $where[] = $QKT->getAdapter()->quoteInto('sn = ?', $sn);
                    }else{
                        $where[] = $QKT->getAdapter()->quoteInto('sn = ?', $sn);
                    }

                    $QKT->update($data, $where);
            }

            break;
        case '2':
            $QKT = new Application_Model_KerryTransaction();

            $arrGetSN = $QDeliveryOrder->getSNByDNID($id);

            if(!$arrGetSN)
                throw new Exception("Invalid Delivery Order Kerry");

            $QKSSL = new Application_Model_KerryShipmentStatusLog();

            $sn = isset($arrGetSN['sales_sn']) ? $arrGetSN['sales_sn'] : '';

            $where = [];
            $where[] = $QKT->getAdapter()->quoteInto('type = ?', '1');
            $where[] = $QKT->getAdapter()->quoteInto('delivery_type = ?', $is_kerry);
            $where[] = $QKT->getAdapter()->quoteInto('tracking_no = ?', $order['tracking_no']);

            $QKT_check = $QKT->fetchALL($where);

            if(count($QKT_check) == 1){

                $response = $this->JNTCancelOrder($order['tracking_no']);

                $response_data = json_decode($response);

                $status = null;
                $status_code = null;
                $remark = null;

                if(isset($response_data->responseitems[0])){
                    $response_value = $response_data->responseitems[0];
                    if(!$response_value->success || strtolower($response_value->success) == 'false'){

                        if(isset($response_value->reason)){
                          $status_code = $response_value->reason;
                        }

                        if(is_null($status_code)){
                            throw new Exception("API Remove Shipment Info Error.");
                        }
                        throw new Exception("Error! Can not send API Status : " . $status_code);
                    }else{
                        $QKT = new Application_Model_KerryTransaction();
                        $data = ['type' => 2];
                        $where = [];
                        $where[] = $QKT->getAdapter()->quoteInto('sn = ?', $sn);
                        $QKT->update($data, $where);
                    }
                }else{
                    throw new Exception("API Remove Shipment Info Error.");
                }

            }else if(count($QKT_check) > 1){

                //get last id QKT
                $QKT_id = null;
                $arr_sn = [];
                foreach ($QKT_check as $key) {
                    if($sn == $key['sn']){
                        $QKT_id = $key['id'];
                        array_push($arr_sn, $key['sn']);
                    }
                }

                $arr_unique = array_unique($arr_sn);
                $arr_duplicates = array_diff_assoc($arr_sn, $arr_unique);

                $QKT = new Application_Model_KerryTransaction();

                $data = ['type' => 2];
                $where = [];
                if(count($arr_duplicates) > 0){
                    $where[] = $QKT->getAdapter()->quoteInto('id = ?', $QKT_id);
                    $where[] = $QKT->getAdapter()->quoteInto('sn = ?', $sn);
                }else{
                    $where[] = $QKT->getAdapter()->quoteInto('sn = ?', $sn);
                }

                $QKT->update($data, $where);
            }
            break;
    }

    $data = array(
        'status'     => My_Delivery_Order_Status::Deleted,
        'deleted_at' => date('Y-m-d H:i:s'),
        'deleted_by' => $userStorage->id,
    );

    $where = $QDeliveryOrder->getAdapter()->quoteInto('id = ?', $id);
    $QDeliveryOrder->update($data, $where);

    $QDeliverySales = new Application_Model_DeliverySales();
    $where = $QDeliverySales->getAdapter()->quoteInto('delivery_order_id = ?', $id);
    $QDeliverySales->delete($where);

    My_Delivery_Order_Status::setStatus($id, My_Delivery_Order_Status::Deleted, $userStorage->id);

    // cập nhật trạng thái IMEI, để trigger update tồn kho
    // $sql = "UPDATE imei, delivery_sales
    //     SET imei.place_id=imei.warehouse_id, imei.place_type=?, imei.imei_status=?
    //     WHERE imei.sales_sn=delivery_sales.sales_sn
    //     AND delivery_sales.delivery_order_id=?";

    // $db->query($sql, array(
    //     My_Place::WAREHOUSE,
    //     My_Imei_Status::SCANNED_OUT,
    //     $id
    // ));
    //

    $flashMessenger->setNamespace('success')->addMessage('Success');
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
}

$refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : (HOST.'delivery/order-control');
$this->_redirect($refer);