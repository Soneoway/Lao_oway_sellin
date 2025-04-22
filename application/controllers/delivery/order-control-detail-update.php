<?php

$flashMessenger = $this->_helper->flashMessenger;
$id = $this->getRequest()->getParam('id');

if ($this->getRequest()->getMethod() == 'POST'){

    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    try {

        $carrier_id           = $this->getRequest()->getParam('carrier_id');
        $number_of_package    = $this->getRequest()->getParam('number_of_package');
        $weight               = $this->getRequest()->getParam('weight');

        $date = new DateTime();
        $created_date = $date->format('Y-m-d H:i:s');

        if($carrier_id != 9){ //allow J&T online
            $flashMessenger->setNamespace('error')->addMessage('Carrier Not Allow Update.');
            $this->_redirect(HOST.'delivery/order-control-detail-update?id=' . $id);
        }

        $QDeliveryOrder = new Application_Model_DeliveryOrder();

        $data = array(
            'number_of_package' => $number_of_package,
            'weight' => $weight
        );

        $where = $QDeliveryOrder->getAdapter()->quoteInto('id = ?', $id);
        $QDeliveryOrder->update($data, $where);

        $getDetailMarket = $QDeliveryOrder->getDetailMarketByDo($id);

        if(!$getDetailMarket){
            $flashMessenger->setNamespace('error')->addMessage('invalid data.');
            $this->_redirect(HOST.'delivery/order-control-detail-update?id=' . $id);
        }

        $QMK = new Application_Model_Market();

          $product_details = $QMK->getDetailsProduct($getDetailMarket[0]['sn_ref']);

          $totalquantity = 0;

          foreach ($product_details as $key_pd) {
            $totalquantity = $totalquantity+$key_pd['num'];
          }

          $json_data = ['txlogisticid' => $getDetailMarket[0]['tracking_no'],
                        'logisticproviderid' => 'JNT',
                        'eccompanyid' => JNT_ECCOMPANYID,
                        'customerid' => JNT_CUSTOMERID,
                        'actiontype' => 'update',
                        'paytype' => '1',
                        'servicetype' => '1',
                        'ordertype' => '1',
                        'createordertime' => $created_date,
                        'sendstarttime' => $created_date,
                        'sendendtime' => $created_date,
                        'sender' => ['name' => 'บริษัท โพสเซฟี่ กรุ๊ป จำกัด (OPPO)',
                                     'phone' => '0655062270',
                                     'mobile' => '0846410204',
                                     'city' => '1',//กรุงเทพมหานคร
                                     'area' => '26',//ดินแดง
                                     'postcode' => '10400',
                                     'address' => 'อาคาร AIA Capital Center ชั้น 31 ห้อง 5-7 เลขที่ 89 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพฯ'
                                    ],
                        'ref_no' => trim($getDetailMarket[0]['sn_ref']),
                        'receiver' => ['name' => trim($getDetailMarket[0]['contact_name']),
                                       // 'phone' => $getDetailMarket[0][''],
                                       // 'mobile' => $getDetailMarket[0][''],
                                       'city' => trim($getDetailMarket[0]['provice_id']),
                                       'area' => trim($getDetailMarket[0]['amphure_id']),
                                       'postcode' => trim($getDetailMarket[0]['zipcode']),
                                       'address' => trim($getDetailMarket[0]['address']) . ' ต.' . trim($getDetailMarket[0]['district_name']) . ' อ.' . trim($getDetailMarket[0]['amphure_name']) . ' จ.' . trim($getDetailMarket[0]['provice_name']) . ' ' . trim($getDetailMarket[0]['zipcode']) . ' | ' . trim($getDetailMarket[0]['sn_ref'])
                                      ],
                        'boxes' => $number_of_package,
                        'totalquantity' => $totalquantity,
                        'weight' => $weight
                        // 'items' => ['itemname' => '',
                        //             'itemvalue' => '',
                        //             'desc' => '',
                        //             'number' => ''
                        //            ]
                      ];

          $textTel = $getDetailMarket[0]['phone'];
          $cTextTel = $this->cleanInt($textTel);

          if(strlen($cTextTel) == 9){
            $cTextTel = '0' . $cTextTel;
          }

          if(strlen($cTextTel) < 9){
            // k.ice default tel
            $cTextTel = '0655062270';
          }

          $tel1 = substr($cTextTel, 0, 10);
          $tel2 = substr($cTextTel,10, 10);
          $tel3 = substr($cTextTel, 20);

          if($tel1){
            $json_data['receiver']['phone'] = $tel1;
            $json_data['receiver']['mobile'] = $tel1;
          }

          if($tel2){
            $json_data['receiver']['mobile'] = $tel2;
          }

          $temp_array_item = [];
          foreach ($product_details as $key_pd => $val_pd) {
            array_push($temp_array_item, ['itemname' => $val_pd['good_code'] . ' ' . $val_pd['good_color'], 'desc' => $val_pd['good_name'] . ' ' . $val_pd['good_color'], 'itemvalue' => 0, 'number' => ($key_pd+1)]);
          }

          $json_data['items'] = $temp_array_item;

          $json_data_encode = json_encode($json_data,JSON_UNESCAPED_UNICODE);

          $response = curlSendAPIJNT($json_data_encode);

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

                $created_date_temp = '0000-00-00 00:00:00';

                $textRemark = $created_date . ' | not find status code';

                if($key['remark'] == ''){
                  $remark = $textRemark;
                }else{
                  $remark = $key['remark'] . ',' . $textRemark;
                }

              }else{

                $created_date_temp = '0000-00-00 00:00:00';

                $textRemark = $created_date . ' | ' . $status_code;

                if($key['remark'] == ''){
                  $remark = $textRemark;
                }else{
                  $remark = $key['remark'] . ',' . $textRemark;
                }

              }

            }else{
              //0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
              $status = 7;
              $status_code = '000';

              $textRemark = $created_date . ' | ' . $response;

              if($key['remark'] == ''){
                $remark = $textRemark;
              }else{
                $remark = $key['remark'] . ',' . $textRemark;
              }

            }

          }

          if($status != 7){

            if(is_null($remark)){
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage('Can not update data : ' . $remark);
                $this->_redirect(HOST.'delivery/order-control-detail-update?id=' . $id);
            }else{
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage('Can not update data : ' . $remark);
                $this->_redirect(HOST.'delivery/order-control-detail-update?id=' . $id);
            }

          }

        $db->commit();
        $flashMessenger->setNamespace('success')->addMessage('Success');
        $this->_redirect(HOST.'delivery/order-control');


    } catch (Exception $e) {
        $db->rollback();
        $flashMessenger->setNamespace('error')->addMessage(sprintf("[%d] %s", $e->getCode(), $e->getMessage()));
        $this->_redirect(HOST.'delivery/order-control');
    }


}

try {
    if (!$id) throw new Exception("Invalid ID", 1);
    $flashMessenger = $this->_helper->flashMessenger;
    $QDeliveryOrder = new Application_Model_DeliveryOrder();
    $where = $QDeliveryOrder->getAdapter()->quoteInto('id = ?', $id);
    $order = $QDeliveryOrder->fetchRow($where);

    if($order['carrier_id'] != 9){ //allow J&T online
        $flashMessenger->setNamespace('error')->addMessage('Carrier Not Allow Update.');
        $this->_redirect(HOST.'delivery/order-control');
    }

    if (!$order) throw new Exception("Wrong ID", 2);

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
    $params['payment'] = 1;
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
    $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
    $this->_redirect(HOST.'delivery/order-control');
}

function curlSendAPIJNT($json_data){

    $url = JNT_API_URL;

    $url_create = $url . 'oppo/order/createOrder.do';

    $data_digest = genDataDigestJAndT($json_data);

    $post_date = [
    'logistics_interface' => $json_data,
    'data_digest' => $data_digest,
    'eccompanyid' => JNT_ECCOMPANYID,
    'msg_type' => 'ORDERCREATE'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url_create);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post_date);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec ($ch);

    curl_close ($ch);

    return $server_output;
}

function genDataDigestJAndT($json_data){
    $key = JNT_KEY;
    $endcode_md5 = md5($json_data.$key);
    $endcode_base64 = base64_encode(strtolower($endcode_md5));
    return $endcode_base64;
} 