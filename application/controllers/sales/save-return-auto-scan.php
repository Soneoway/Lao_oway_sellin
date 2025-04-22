<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

    $return_type            = $this->getRequest()->getParam('return_type');
    $store_id               = $this->getRequest()->getParam('store_id');
    $warehouse_id           = $this->getRequest()->getParam('warehouse_id');
    $finance_client         = $this->getRequest()->getParam('finance_client');
    $delivery               = $this->getRequest()->getParam('delivery');
    $remark                 = $this->getRequest()->getParam('remark');
    $imeis                  = $this->getRequest()->getParam('imei');
    $type                   = $this->getRequest()->getParam('type');

    $data_phone_return      = $this->getRequest()->getParam('data_phone_return');
    $obj = json_decode($data_phone_return, true);

    $array_phone = $obj['result_phone'];
    $array_imei = $obj['result_imei'];

    $isbatch        = 1;
    $isbacks        = 1;
    $create_cn      = 1;
    $active_cn      = 1;
    $type           = 1;

    if(!$create_cn){
        $type = null;
    }

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $QImeiReturn = new Application_Model_ImeiReturn();
    $QProductReturn = new Application_Model_ProductReturn();
    $QDistributor = new Application_Model_Distributor();
    $QImei = new Application_Model_Imei();

    $QLog = new Application_Model_Log();
    $QGood = new Application_Model_Good();
    $QGoodColor = new Application_Model_GoodColor();
    $QFinanceClient = new Application_Model_FinanceClient();
    $QFinanceWarehouse = new Application_Model_FinanceWarehouse();
    $QGoodCategory = new Application_Model_GoodCategory();
    $QMarket = new Application_Model_Market();
    $QStore = new Application_Model_Store();
    $QWarehouse = new Application_Model_Warehouse();
    $QContactDetail = new Application_Model_ContactDetail();

    $goods_cache = $QGood->get_cache();
    $good_colors_cache = $QGoodColor->get_cache();

    $db = Zend_Registry::get('db');

    try {

        $db->beginTransaction();

        $count_create_cn_before = 0;
        $count_create_cn_after = 0;

        $sn = date ( 'YmdHis' ) . substr ( microtime (), 2, 4 );
        $sn_ref = $QImeiReturn->getReturnOrderNo_Ref($sn);

        if($return_type == 1) {

            $where = $QStore->getAdapter()->quoteInto('id = ?', $store_id);
            $store = $QStore->fetchRow($where);
            $distributor_id = $store->d_id;

            $where2 = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
            $distributor = $QDistributor->fetchRow($where2);
            $rank = $distributor->rank;

        }else if($return_type == 2){

            $where = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$warehouse_id);
            $distributor = $QDistributor->fetchRow($where);
            $distributor_id = $distributor->id;
            $rank = $distributor->rank;

        }

        // get Distributor Agent
        $where_upper = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$distributor->warehouse_id);
        $distributor_upper = $QDistributor->fetchRow($where_upper);

        // get Finance Client
        $where_dis = array();
        $where_dis[] = $QFinanceClient->getAdapter()->quoteInto('distributor_y_id =?',$distributor->id);
        $where_dis[] = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$distributor_upper->id);
        $finance_client_arr = $QFinanceClient->fetchRow($where_dis);

        // get Finance Warehouse
        $where_fw = $QFinanceWarehouse->getAdapter()->quoteInto('d_id =?',$distributor_upper->id);
        $finance_warehouse = $QFinanceWarehouse->fetchRow($where_fw);


        if($return_type == 1) {

            $backs_d_id = $distributor->warehouse_id; // return distributor ( To )
            $warehouse_id = $distributor->warehouse_id; // return warehouse ( To )

        }else if($return_type == 2) {

            $backs_d_id = $distributor_upper->warehouse_id; // return distributor ( To )
            $warehouse_id = $distributor_upper->warehouse_id; // return warehouse ( To )

        }

            $finance_client_id = $finance_client_arr->id;
            $finance_warehouse_id = $finance_warehouse->id;


            foreach ($array_phone as $key => $item){

                $date = date('Y-m-d H:i:s');

                if(isset($item['invoice_number'])){
                    $invoice_number = $item['invoice_number'];
                }else{
                    $invoice_number='';
                }

                // Price is Null
                if($item['out_price'] == '') {

                    $where = $QGood->getAdapter()->quoteInto('id =?',$item['good_id']);
                    $good = $QGood->fetchRow($where);
                    $out_price = $good->price_9;
                    $total_out_price = $item['qty'] * $out_price;

                }else{

                    $out_price = $item['out_price'];
                    $total_out_price = $item['qty'] * $out_price;
                }

                $data = array(
                    'cat_id'                    => $item['cat_id'],
                    'good_id'                   => $item['good_id'],
                    'good_color'                => $item['good_color'],
                    'num'                       => $item['qty'],
                    'price'                     => $out_price,
                    'total'                     => $total_out_price,
                    'text'                      => $remark,
                    'price_clas'                => $rank,
                    'd_id'                      => $distributor_id,
                    'isbatch'                   => $isbatch,
                    'isbacks'                   => $isbacks,

                    'return_shop'               => '',

                    'backs_d_id'                => $backs_d_id,
                    'warehouse_id'              => $warehouse_id,  

                    'invoice_number'            => $invoice_number,
                    'create_cn'                 => $create_cn,
                    'active_cn'                 => $active_cn,
                    'return_type'               => $type,
                    'return_order_type'         => $return_type,

                    'pay_text'                  => '',
                    'pay_time'                  => $date,
                    'pay_user'                  => $userStorage->id,
                    'shipping_yes_time'         => $date,
                    'shipping_yes_id'           => $userStorage->id,
                    'finance_confirm_date'      => $date,
                    'finance_confirm_id'        => $userStorage->id,

                    'add_time'                  => $date,
                    'user_id'                   => $userStorage->id,
                    'sn'                        => $sn,
                    'sn_ref'                    => $sn_ref,

                    'finance_client_id'         => $finance_client_id,
                    'finance_warehouse'         => $finance_warehouse_id,
                    'store_id'                  => $item['store_id']
                );

                //insert
                $rr = $QMarket->insert($data);


                if($rr) {

                    $good_name = $goods_cache[$item['good_id']];
                    $color_name = $good_colors_cache[$item['good_color']];

                    $where = $QStore->getAdapter()->quoteInto('id =?',$store_id);
                    $store_arr = $QStore->fetchRow($where);


                    $contact_data = array(
                        'store_id'              => $store_id,
                        'd_id'                  => $store_arr->d_id,
                        'finance_client_id'     => $finance_client_id,
                        'doc_no'                => $sn,
                        'status'                => 1,
                        'created_at'            => date('Y-m-d H:i:s'),
                        'created_by'            => $userStorage->id,
                        'description'           => $good_name.' '.$color_name.'*'.$item['out_price'].'*'.$item['qty'],
                        'type'                  => 2,
                    );

                    $QContactDetail->insert($contact_data);

                }

            }

            foreach ($array_imei as $t => $item_imei){

                $imei = trim($item_imei['imei_sn']);
                $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                $imei_check = $QImei->fetchRow($where);
                $sales_sn = $imei_check['sales_sn']; 

                $data = array(
                    'imei_sn'           => trim($imei),
                    'warehouse_id'      => $warehouse_id,
                    'return_sn'         => $sn,
                    'sales_order_sn'    => $sales_sn,
                    'created_at'        => $date,
                    'created_by'        => $userStorage->id,
                    'confirmed_at'      => $date,
                    'confirmed_by'      => $userStorage->id
                );

                $QImeiReturn->insert($data);

            }

            if(isset($create_cn) && $create_cn == 1){
                $status = 0;
                $count_create_cn_before+=1;
                if(isset($active_cn) && $active_cn==1){
                    $status = $active_cn;
                }

                $creditnote_sn = $QImeiReturn->get_credit_note_sn($db,$distributor_id,$userStorage->id,$sn,$status,$type);
                if($creditnote_sn==''){
                    for($i=0;$i<3;$i++){ 
                        if($creditnote_sn==''){
                            $creditnote_sn = $QImeiReturn->get_credit_note_sn($db,$distributor_id,$userStorage->id,$sn,$status,$type);
                        }
                    }
                }

                if($creditnote_sn !='')
                {
                  $count_create_cn_after+=1;
              }
          }

          $info = 'Batch addSale order number: Sale order number： ';
          $ip = $this->getRequest()->getServer('REMOTE_ADDR');
          $info .= $sn;

          $QLog->insert( array (
            'info'          => $info,
            'user_id'       => $userStorage->id,
            'ip_address'    => $ip,
            'time'          => date('Y-m-d H:i:s'),
            ));

          if(isset($create_cn) && $create_cn == 1){
            if($count_create_cn_after == $count_create_cn_before){
                $flashMessenger->setNamespace('success')->addMessage('Done!');
                $db->commit(); 

            }else{
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage('Cannot Create CN For Return, Please try again! @1');
            }

        }else{

            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $db->commit();  
        }

    } catch (Exception $e) {

        $db->rollback();

        echo '<script>
        parent.palert("Cannot Create CN For Return, Please try again! @2");
        </script>';
        exit;
    }

}


echo '<script>parent.location.href="/sales/return-list"</script>';
exit;
