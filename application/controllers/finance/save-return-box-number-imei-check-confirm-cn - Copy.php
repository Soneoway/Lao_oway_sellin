<?php
$flashMessenger = $this->_helper->flashMessenger;

 //print_r($_POST);die;

if ($this->getRequest()->getMethod() == 'POST'){
    
    $box_sn      = $this->getRequest()->getParam('box_sn');
    $array_action_imei      = $this->getRequest()->getParam('action_imei');
    $array_return_type      = $this->getRequest()->getParam('return_type');
    $array_rtn_number      = $this->getRequest()->getParam('rtn_number');
    $array_imei_shape      = $this->getRequest()->getParam('imei_shape');
    $array_create_cn      = $this->getRequest()->getParam('create_cn');
    $array_active_cn      = $this->getRequest()->getParam('active_cn');
    $array_warehouse_id      = $this->getRequest()->getParam('warehouse_id');
    $array_damage_detail      = $this->getRequest()->getParam('damage_detail');
    $array_remark      = $this->getRequest()->getParam('remark');
    $array_imei_not_return      = $this->getRequest()->getParam('imei_not_return');
    $array_imei_not_oppo      = $this->getRequest()->getParam('imei_not_oppo');

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $QMarket = new Application_Model_Market();
    $QReturnBoxNumber = new Application_Model_ReturnBoxNumber();
    $QReturnBoxNumberImei = new Application_Model_ReturnBoxNumberImei();
    $QImeiReturn = new Application_Model_ImeiReturn();
    $QDistributor = new Application_Model_Distributor();
    $QImei = new Application_Model_Imei();

    $data=null;
    //print_r($array_action_imei);die;

    // Check By Imei Select
    foreach ($array_action_imei as $j=>$imei_action)
    {

        if ($imei_action !=''){
            foreach ($array_return_type as $action_index=>$item_t1)
            {
                //print_r($action_index);echo ",";
                $val = explode("-", $item_t1);
                $imei_return_type=$val[0];
                $return_type=$val[1];
                $distributor_id=$val[2];

                if($imei_action==$imei_return_type)
                {
                    foreach ($array_create_cn as $kk=>$item_t2){
                        $val = explode("-", $item_t2);
                        $imei_create_cn=$val[0];
                        if($imei_create_cn==$imei_action){
                            $status_create_cn=1;
                            break;
                        }else{
                            $status_create_cn=0;
                        }
                    }

                    foreach ($array_active_cn as $kk=>$item_t3){
                        $val = explode("-", $item_t3);
                        $imei_active_cn=$val[0];
                        if($imei_active_cn==$imei_action){
                            $status_active_cn=1;
                            break;
                        }else{
                            $status_active_cn=0;
                        }
                    }

                    foreach ($array_warehouse_id as $ww=>$item_t4){
                        $val = explode("-", $item_t4);
                        $imei_warehouse_id=$val[0];
                        if($imei_warehouse_id==$imei_action){
                            $warehouse_id=$val[2];
                            $damage_detail = $array_damage_detail[$ww];
                            $remark_text_val = $array_remark[$ww];
                            $rtn_number = $array_rtn_number[$ww];
                            break;
                        }else{
                            $damage_detail ="";
                            $remark_text_val ="";
                            $rtn_number ="";
                        }
                    }

                    switch ($return_type) {
                        case "1":   //เครื่องเสีย
                            $data[] = array(
                                'box_sn'  => $box_sn,
                                'return_type'  => $return_type,
                                'imei_sn'  => $imei_return_type,
                                'distributor_id'  => $distributor_id,
                                'create_cn'  => $status_create_cn,
                                'active_cn'  => $status_active_cn,
                                'warehouse_id'  => $warehouse_id,
                                'shape_status'  => 4,
                                'damage_detail'  => $damage_detail,
                                'remark'  => $remark_text_val,
                                'rtn_number'  => $rtn_number
                            );
                            break;
                        case "3":   //Demo
                            $data[] = array(
                                'box_sn'  => $box_sn,
                                'return_type'  => $return_type,
                                'imei_sn'  => $imei_return_type,
                                'distributor_id'  => $distributor_id,
                                'create_cn'  => $status_create_cn,
                                'active_cn'  => $status_active_cn,
                                'warehouse_id'  => $warehouse_id,
                                'shape_status'  => 1,
                                'damage_detail'  => $damage_detail,
                                'remark'  => $remark_text_val,
                                'rtn_number'  => $rtn_number
                            );
                            break;
                        case "4":   //RTN
                            $data[] = array(
                                'box_sn'  => $box_sn,
                                'return_type'  => $return_type,
                                'imei_sn'  => $imei_return_type,
                                'distributor_id'  => $distributor_id,
                                'create_cn'  => $status_create_cn,
                                'active_cn'  => $status_active_cn,
                                'warehouse_id'  => $warehouse_id,
                                'shape_status'  => 1,
                                'damage_detail'  => $damage_detail,
                                'remark'  => $remark_text_val,
                                'rtn_number'  => $rtn_number
                            );
                            break;
                        case "5":   //EOL
                            $data[] = array(
                                'box_sn'  => $box_sn,
                                'return_type'  => $return_type,
                                'imei_sn'  => $imei_return_type,
                                'distributor_id'  => $distributor_id,
                                'create_cn'  => $status_create_cn,
                                'active_cn'  => $status_active_cn,
                                'warehouse_id'  => $warehouse_id,
                                'shape_status'  => 1,
                                'damage_detail'  => $damage_detail,
                                'remark'  => $remark_text_val,
                                'rtn_number'  => $rtn_number
                            );
                            break;
                    }
                    break;
                }
            }

        }
    }

    //print_r($data);
    //die; 
    // Check Product Group from Imei Select

    $cn_data=null;
    foreach ($data as $im=>$imei_val)
    {
       // print_r($imei_val);
        
            $chk_return_type=$imei_val['return_type'];
            if($chk_return_type==4){ //RTN
                $return_type=$imei_val['return_type'].'-'.$imei_val['create_cn'].'-'.$imei_val['active_cn'].'-'.$imei_val['rtn_number'].'-'.$imei_val['distributor_id'];
                $imei_list=$imei_val['imei_sn'].",";
                $distributor_id=$imei_val['distributor_id'];
                $status_create_cn=$imei_val['create_cn'];
                $status_active_cn=$imei_val['active_cn'];
                $warehouse_id=$imei_val['warehouse_id'];
                $shape_status=$imei_val['shape_status'];
                $rtn_number=$imei_val['rtn_number'];
                $return_type_id=$imei_val['return_type'];
                $cn_data[$return_type] = array(
                    'box_sn'  => $box_sn,
                    'return_type'  => $return_type_id,
                    'distributor_id'  => $distributor_id,
                    'create_cn'  => $status_create_cn,
                    'active_cn'  => $status_active_cn,
                    'warehouse_id'  => $warehouse_id,
                    'shape_status'  => $shape_status,
                    'rtn_number'  => $rtn_number,
                    'imei_sn'  => $cn_data[$return_type]['imei_sn'].=$imei_list
                );
            }else{ //Other
                $return_type=$imei_val['return_type'].'-'.$imei_val['create_cn'].'-'.$imei_val['active_cn'].'-'.$imei_val['rtn_number'].'-'.$imei_val['distributor_id'];
                
                $imei_list=$imei_val['imei_sn'].",";
                $distributor_id=$imei_val['distributor_id'];
                $status_create_cn=$imei_val['create_cn'];
                $status_active_cn=$imei_val['active_cn'];
                $warehouse_id=$imei_val['warehouse_id'];
                $shape_status=$imei_val['shape_status'];
                $rtn_number="";
                $return_type_id=$imei_val['return_type'];
                $cn_data[$return_type] = array(
                    'box_sn'  => $box_sn,
                    'return_type'  => $return_type_id,
                    'distributor_id'  => $distributor_id,
                    'create_cn'  => $status_create_cn,
                    'active_cn'  => $status_active_cn,
                    'warehouse_id'  => $warehouse_id,
                    'shape_status'  => $shape_status,
                    'rtn_number'  => $rtn_number,
                    'imei_sn'  => $cn_data[$return_type]['imei_sn'].=$imei_list
                );
            }
    }

    //print_r($cn_data);
    //die;

    try{

        $db = Zend_Registry::get('db');

        $db->beginTransaction();

        foreach ($cn_data as $im_cn=>$cn_val)
        {
            $sn = date ( 'YmdHis' ) . substr ( microtime (), 2, 4 );
            $sn_ref=$QImeiReturn->getReturnOrderNo_Ref($sn);
            if($sn_ref==''){
                for($i=0;$i<3;$i++){ 
                    if($sn_ref==''){
                        $sn_ref =$QImeiReturn->getReturnOrderNo_Ref($sn);
                    }
                }
            }

            $distributor_id=$cn_val['distributor_id'];
            $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
            $distributor = $QDistributor->fetchRow($where);
            $rank = $distributor->rank;

            $box_sn=$cn_val['box_sn'];
            $return_type=$cn_val['return_type'];
            
            $status_create_cn=$cn_val['create_cn'];
            $status_active_cn=$cn_val['active_cn'];
            $warehouse_id=$cn_val['warehouse_id'];
            $imei_shape_status=$cn_val['shape_status'];
            $rtn_number=$cn_val['rtn_number'];
            $imei_list=rtrim($cn_val['imei_sn'], ',');
            $array_phone = $QReturnBoxNumberImei->getReturnBoxNumberByProductCheckAction($imei_list);
            //print_r($array_phone);die;

            $isbatch        = 1;
            $isbacks        = 1;
            foreach ($array_phone as $k=>$item)
            {

                    $date = date('Y-m-d H:i:s');
                    
                    if(isset($item['invoice_number'])){
                        $invoice_number = $item['invoice_number'];
                    }else{$invoice_number='';}
                    $data_market = array(
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
                        'sn_ref'        => $sn_ref,
                        'box_sn'        => $box_sn
                    );
                
                    //print_r($data_market);die;
                    //insert
                   $QMarket->insert($data_market);

            }

               $array_imei_damage_text = array();
               $array_imei_remark_text = array();
               $array_imei_rtn = array();
               $array_imei = explode(",", $imei_list);
               //print_r($data);die;
               foreach ($array_imei as $t=>$imei_sn)
               {
                    $imei = trim($imei_sn);
                    //print_r($imei);die;
                    foreach ($data as $key=>$key_imei_sn)
                    {
                       // print_r($key_imei_sn);die;
                        if($imei==$key_imei_sn['imei_sn'])
                        {
                            
                            $sql="select imi.sales_sn,m.invoice_number,imi.good_id,imi.good_color
                                from imei imi 
                                left join market m on imi.sales_sn=m.sn
                                where imi.imei_sn=".$imei."
                                group by m.sn";

                            $imei_check = $db->fetchAll($sql);
                            //print_r($imei_check);die;
                            $sales_sn = $imei_check[0]['sales_sn']; 
                            $invoice_number = $imei_check[0]['invoice_number'];   
                            $good_id = $imei_check[0]['good_id'];  
                            $good_color = $imei_check[0]['good_color']; 
                            $key = $invoice_number.'-'.$good_id.'-'.$good_color;
                            if($key_imei_sn['damage_detail']!=''){
                                $damage_detail = $key_imei_sn['damage_detail'];
                                $remark_detail = $key_imei_sn['remark'];
                                $rtn_number = $key_imei_sn['rtn_number'];
                                $array_imei_damage_text[$key].= $damage_detail.' ,'; 
                                $array_imei_remark_text[$key].= $remark_detail.' ,'; 
                                $array_imei_rtn[$key].= $rtn_number.' ,'; 
                            }
                            //print_r($array_imei_damage_text);
                            //die;
            
                            $dataImeiReturn = array(
                                'imei_sn' => trim($imei),
                                'box_sn' => $box_sn,
                                'warehouse_id' => $warehouse_id,
                                'return_sn' => $sn,
                                'sales_order_sn' => $sales_sn,
                                'created_at' => $date,
                                'created_by' => $userStorage->id,
                                'confirmed_at' => $date,
                                'confirmed_by' => $userStorage->id,
                                'damage_detail' => $key_imei_sn['damage_detail'],
                                'remark' => $key_imei_sn['remark'],
                                'rtn_number' => $key_imei_sn['rtn_number']
                                
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

                            //print_r($dataImeiReturn);
                            //die;
                            $QImeiReturn->insert($dataImeiReturn);

                            // Update Imei
                            $whereImei = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                            $QImei->update($dataImei, $whereImei);

                            $dataBoxImeiReturn = array();
                            $dataBoxImeiReturn['create_cn']= $key_imei_sn['create_cn'];
                            $dataBoxImeiReturn['active_cn']= $key_imei_sn['active_cn'];
                            $dataBoxImeiReturn['distributor_id']= $key_imei_sn['distributor_id'];
                            $dataBoxImeiReturn['return_type']= $key_imei_sn['return_type'];
                            $dataBoxImeiReturn['warehouse_id']= $key_imei_sn['warehouse_id'];
                            $dataBoxImeiReturn['rtn_number']= $key_imei_sn['rtn_number'];
                            $dataBoxImeiReturn['shape_status']= $key_imei_sn['shape_status'];
                            $dataBoxImeiReturn['remark']= $key_imei_sn['remark'];
                            $dataBoxImeiReturn['damage_detail']= $key_imei_sn['damage_detail'];

                            $dataBoxImeiReturn['finance_confirm_date']= $date;
                            $dataBoxImeiReturn['finance_confirm_by']= $userStorage->id;
                            $dataBoxImeiReturn['status']= 1;
  
                            $whereBoxImei = array();
                            $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                            $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('box_sn = ?', $box_sn);
                            $QReturnBoxNumberImei->update($dataBoxImeiReturn, $whereBoxImei);

                            $whereCheckBoxNumberImei = array();
                            $whereCheckBoxNumberImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('box_sn = ?', $box_sn);
                            $whereCheckBoxNumberImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('active = ?', 1);
                            $whereCheckBoxNumberImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('status = ?', 1);
                            $whereCheckBoxNumberImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('finance_confirm_date is not null', null);

                            $imei_check = $QReturnBoxNumberImei->fetchAll($whereCheckBoxNumberImei);
                            //echo count($imei_check);die;
                            //print_r($imei_check);die;

                            $update_ReturnBoxNumber['box_status']=2;
                            $update_ReturnBoxNumber['update_date']=$date;
                            $update_ReturnBoxNumber['update_by']=$userStorage->id;
                            
                            $whereBoxNumber = array();
                            $whereBoxNumber[] = $QReturnBoxNumber->getAdapter()->quoteInto('box_sn = ?', $box_sn);
                            $whereBoxNumber[] = $QReturnBoxNumber->getAdapter()->quoteInto('total_imei = ?', count($imei_check));
                            $QReturnBoxNumber->update($update_ReturnBoxNumber, $whereBoxNumber);

                        }
                    }
                }

                foreach ($array_phone as $k=>$item)
                {
                    //print_r($item);die;
                    $invoice_number = $item['invoice_number'];   
                    $good_id = $item['good_id'];  
                    $good_color = $item['good_color']; 
                    $key = $invoice_number.'-'.$good_id.'-'.$good_color;
                    
                    $damage_text=rtrim($array_imei_damage_text[$key], ',');
                    $remark_text=rtrim($array_imei_remark_text[$key], ',');
                    $rtn=rtrim($array_imei_rtn[$key], ',');
                    $update_Market['text']=$damage_text;
                    $update_Market['remark']=$remark_text;
                    $update_Market['rtn_number']=$rtn;

                    $whereMarket = array();
                    $whereMarket[] = $QMarket->getAdapter()->quoteInto('box_sn = ?', $box_sn);
                    $whereMarket[] = $QMarket->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);
                    $whereMarket[] = $QMarket->getAdapter()->quoteInto('good_id = ?', $good_id);
                    $whereMarket[] = $QMarket->getAdapter()->quoteInto('good_color = ?', $good_color);

                    //print_r($update_Market);die;
                    $QMarket->update($update_Market, $whereMarket);

                } 

                //$QMarket->update($update_Market, $whereMarket);

        }

        $array_imei_data_not_oppo = explode("\n", trim($array_imei_not_oppo));
        $array_imei_data_not_oppo = array_unique($array_imei_data_not_oppo);
        //print_r($array_imei_data_not_oppo);die;
        foreach ($array_imei_data_not_oppo as $j=>$imei_sn)
        {
            $dataBoxImeiReturn = array();
        
            $dataBoxImeiReturn['finance_confirm_date']= $date;
            $dataBoxImeiReturn['finance_confirm_by']= $userStorage->id;
            $dataBoxImeiReturn['status']= 1;
            $whereBoxImei = array();
            $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('imei_sn = ?', $imei_sn);
            $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('box_sn = ?', $box_sn);
            $QReturnBoxNumberImei->update($dataBoxImeiReturn, $whereBoxImei);

        }

        $array_imei_data_not_return = explode("\n", trim($array_imei_not_return));
        $array_imei_data_not_return = array_unique($array_imei_data_not_return);
        //print_r($array_imei_data_not_oppo);die;
        foreach ($array_imei_data_not_return as $j=>$imei_sn)
        {
            $dataBoxImeiReturn = array();
            $dataBoxImeiReturn['finance_confirm_date']= $date;
            $dataBoxImeiReturn['finance_confirm_by']= $userStorage->id;
            $dataBoxImeiReturn['status']= 1;
            $whereBoxImei = array();
            $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('imei_sn = ?', $imei_sn);
            $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('box_sn = ?', $box_sn);
            $QReturnBoxNumberImei->update($dataBoxImeiReturn, $whereBoxImei);

        }
            //die;
            $flashMessenger->setNamespace('success')->addMessage('Done!');
            //$db->rollback();
            $db->commit(); 

       // die;
    }catch (Exception $e){

        $db->rollback();

        echo '<script>
                parent.palert("Cannot Create CN For Return, Please try again!");
              </script>';
        exit;
    }



}

echo '<script>parent.location.href="/finance/return-box-number-imei-confirm-list"</script>';
exit;


