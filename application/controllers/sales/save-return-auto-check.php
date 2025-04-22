<?php
$flashMessenger = $this->_helper->flashMessenger;

//print_r($_POST);die;

if ($this->getRequest()->getMethod() == 'POST'){
    
    $row_d_id      = $this->getRequest()->getParam('d_id');
    $create_cn      = $this->getRequest()->getParam('create_cn');
    $active_cn      = $this->getRequest()->getParam('active_cn');
    $warehouse      = $this->getRequest()->getParam('warehouse_id');
    $imei_shape      = $this->getRequest()->getParam('imei_shape');
    $remark_text      = $this->getRequest()->getParam('remark_text');

    $data_phone_return      = $this->getRequest()->getParam('data_phone_return');
/*    print_r($row_d_id);
    print_r($remark_text);

    foreach ($row_d_id as $d_index=>$item_d){
        if($item_d==38403){
            echo $d_index;
            echo $remark_text[$d_index];
        }
    }

    die;*/
    $obj = json_decode($data_phone_return, true);
    //print_r($obj);die;

    $array_phone = $obj['result_return_by_group'];
    $array_imei = $obj['result_return_by_imei'];
    $array_distributor_id[] = array();
    foreach ($obj['result_return_by_group'] as $k=>$item){

        $array[$k] = $item['distributor_id'];
    }

    $array_distributor = array_unique($array);

    $isbatch        = 1;
    $isbacks        = 1;

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $QLog = new Application_Model_Log();

        try{

            $db = Zend_Registry::get('db');

            $db->beginTransaction();

            $QMarket = new Application_Model_Market();

            //$db = Zend_Registry::get('db');

            $QImeiReturn = new Application_Model_ImeiReturn();
            $QDistributor = new Application_Model_Distributor();
            $QImei = new Application_Model_Imei();

            //1=เครื่องเสีย 2=Adjustment 3=Demo 4=RTN 5=EOL
            $return_type=1;

            $count_create_cn_before=0;$count_create_cn_after=0;
            foreach ($array_distributor as $j=>$distributor_id){
                //echo $distributor_id;
                if ($distributor_id !='') {

                    $sn = date ( 'YmdHis' ) . substr ( microtime (), 2, 4 );
                    $sn_ref=$QImeiReturn->getReturnOrderNo_Ref($sn);

                    $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
                    $distributor = $QDistributor->fetchRow($where);
                    $rank = $distributor->rank;

                    foreach ($create_cn as $kk=>$item_kk){
                        if($item_kk==$distributor_id){
                            $status_create_cn=1;
                            break;
                        }else{
                            $status_create_cn=0;
                        }
                    }

                    foreach ($active_cn as $hh=>$item_hh){
                        if($item_hh==$distributor_id){
                            $status_active_cn=1;
                            break;
                        }else{
                            $status_active_cn=0;
                        }
                    }

                    foreach ($warehouse as $ww=>$item_ww){
                        $val = explode("-", $item_ww);
                        $d_id=$val[0];
                        if($d_id==$distributor_id)
                        {
                            $warehouse_id=$val[1];
                            break;
                        }
                    }

                    foreach ($imei_shape as $ss=>$item_ss){
                        $val = explode("-", $item_ss);
                        $d_id=$val[0];
                        if($d_id==$distributor_id)
                        {
                            $imei_shape_status=$val[1];
                            break;
                        }else{
                            $imei_shape_status=0;
                        }
                    }

                    foreach ($row_d_id as $d_index=>$item_d){
                        if($item_d==$distributor_id){
                            //echo $d_index;
                            $remark_text_val = $remark_text[$d_index];
                            break;
                        }else{
                            $remark_text_val="";
                        }
                    }

                    foreach ($array_phone as $k=>$item){

                        if ($distributor_id == $item['distributor_id']) 
                        {

                            $date = date('Y-m-d H:i:s');
                            
                            if(isset($item['invoice_number'])){
                                $invoice_number = $item['invoice_number'];
                            }else{$invoice_number='';}
                            $data = array(
                                'cat_id'        => $item['cat_id'],
                                'good_id'       => $item['good_id'],
                                'good_color'    => $item['good_color'],
                                'num'           => $item['num'],
                                'price'         => $item['unit_price'],
                                'total'         => $item['total_price'],
                                'text'          => $remark_text_val,
                                'price_clas'    => $rank,
                                'd_id'          => $distributor_id,
                                'isbatch'       => $isbatch,
                                'isbacks'       => $isbacks,
                                'backs_d_id'    => $warehouse_id,
                                'warehouse_id'  => $warehouse_id,
                                'invoice_number'    => $invoice_number,
                                'create_cn'     => $status_create_cn,
                                'active_cn'     => $status_active_cn,
                                'return_type'     => $return_type,
                                'pay_text'      => 'Approve',
                                'pay_time'      => $date,
                                'pay_user'      => $userStorage->id,
                                'shipping_yes_time'     => $date,
                                'shipping_yes_id'     => $userStorage->id,
                                'finance_confirm_date'     => $date,
                                'finance_confirm_id'     => $userStorage->id,

                                'add_time'      => $date,
                                'user_id'       => $userStorage->id,
                                'sn'            => $sn,
                                'sn_ref'        => $sn_ref
                            );
                        
                            //print_r($data);die;
                            //insert
                            $QMarket->insert($data);
                         
                        }  
                    }

                    foreach ($array_imei as $t=>$item_imei){

                        if ($distributor_id == $item_imei['distributor_id']) 
                        {
                            $imei = trim($item_imei['imei_sn']);
                            $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                            $imei_check = $QImei->fetchRow($where);
                            $sales_sn = $imei_check['sales_sn'];   
            
                            $dataImeiReturn = array(
                                'imei_sn' => trim($imei),
                                'warehouse_id' => $warehouse_id,
                                'return_sn' => $sn,
                                'sales_order_sn' => $sales_sn,
                                'created_at' => $date,
                                'created_by' => $userStorage->id,
                                'confirmed_at' => $date,
                                'confirmed_by' => $userStorage->id
                            );

                            $dataImei = array(
                                'return_sn'     => $sn,
                                'distributor_id'=> null,
                                'out_date'      => null,
                                'out_user'      => null,
                                'out_price'     => null,
                                'price_date'    => null,
                                'sales_sn'      => null,
                                'sales_id'      => null,
                                'shape'         => $imei_shape_status,
                                'warehouse_id'  => $warehouse_id,
                            );

                            switch ($imei_shape_status) {
                                case 1: //Goodset
                                    $dataImeiReturn['status']= 1;
                                    $dataImeiReturn['back_sale']= 1;
                                    break;
                                case 2: //Broken-seal
                                    $dataImeiReturn['status']= 2;
                                    $dataImei['status']= 1;
                                    $dataImei['shape']= 2;
                                    break;
                                case 3: //Box-damage
                                    $dataImeiReturn['status']= 3;
                                    $dataImei['status']= 1;
                                    $dataImei['shape']= 3;
                                    break;
                                case 4: //Unit-damage
                                    $dataImeiReturn['status']= 4;
                                    $dataImei['status']= 1;
                                    $dataImei['shape']= 4;
                                    break;
                            }

                            //print_r($data);die;
                            $QImeiReturn->insert($dataImeiReturn);

                            // Update Imei
                            $whereImei = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                            $QImei->update($dataImei, $whereImei);
                            
                        }
                    } 

                    if($status_create_cn == 1)
                    {
                        $count_create_cn_before+=1;

                       $creditnote_sn = $QImeiReturn->get_credit_note_sn($db,$distributor_id,$userStorage->id,$sn,$status_active_cn,$return_type);
                       if($creditnote_sn==''){
                            for($i=0;$i<3;$i++){ 
                                if($creditnote_sn==''){
                                    $creditnote_sn = $QImeiReturn->get_credit_note_sn($db,$distributor_id,$userStorage->id,$sn,$status_active_cn,$return_type);
                                }
                            }
                       }

                       if($creditnote_sn !='')
                       {
                          $count_create_cn_after+=1;
                       }
                    }

                }
            }

            $info = 'Return Product Auto Check : '. $sn;
            $ip = $this->getRequest()->getServer('REMOTE_ADDR');
            $info .= $sn;

            $QLog->insert( array (
                'info' => $info,
                'user_id' => $userStorage->id,
                'ip_address' => $ip,
                'time' => date('Y-m-d H:i:s'),
            ) );

                if($count_create_cn_after == $count_create_cn_before){
                    $flashMessenger->setNamespace('success')->addMessage('Done!');
                    $db->commit(); 
                }else{
                    $db->rollback();
                    $flashMessenger->setNamespace('error')->addMessage('Cannot Create CN For Return, Please try again!22');
                }


        }catch (Exception $e){

            $db->rollback();

            echo '<script>
                    parent.palert("Cannot Create CN For Return, Please try again!33");
                  </script>';
            exit;
        }   

}

echo '<script>parent.location.href="/sales/return-list"</script>';
exit;


