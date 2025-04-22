<?php
$flashMessenger = $this->_helper->flashMessenger;

//print_r($_POST);die;

if ($this->getRequest()->getMethod() == 'POST'){
    
    //------------------ Box No ----------------//

    $box_sn      = $this->getRequest()->getParam('box_sn');
    $box_number      = $this->getRequest()->getParam('box_number');
    $box_post_number      = $this->getRequest()->getParam('box_post_number');
    $sender_name      = $this->getRequest()->getParam('sender_name');
    $distributor_name      = $this->getRequest()->getParam('distributor_name');
    $box_remark      = $this->getRequest()->getParam('box_remark');
    $frm_action      = $this->getRequest()->getParam('frm_action');
    $imei_list      = $this->getRequest()->getParam('imei');
    $imei_confirm      = $this->getRequest()->getParam('imei_confirm');

    //------------------ Imei List ----------------//
    $array_action_imei      = $this->getRequest()->getParam('action_imei');
    $array_good_id      = $this->getRequest()->getParam('good_id');
    $array_good_color      = $this->getRequest()->getParam('good_color');
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
    $array_sales_sn      = $this->getRequest()->getParam('sales_sn');
    $array_sum_unit_price      = $this->getRequest()->getParam('sum_unit_price');
    $array_sub_d_id      = $this->getRequest()->getParam('sub_d_id');
    $imei_list_action="";

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $QMarket = new Application_Model_Market();
    $QReturnBoxNumber = new Application_Model_ReturnBoxNumber();
    $QReturnBoxNumberImei = new Application_Model_ReturnBoxNumberImei();
    $QImeiReturn = new Application_Model_ImeiReturn();
    $QDistributor = new Application_Model_Distributor();
    $QImei = new Application_Model_Imei();
    $QLog = new Application_Model_Log();

        try{

            $array_imei_data = explode("\r\n", trim($imei_list));
            //print_r($array_imei_data);
            //die;
            $array_imei = array_unique($array_imei_data);
            // var_dump($array_imei);die;

            // check lock imei
            $QImeiLock = new Application_Model_ImeiLock();
            $getImeiLock = $QImeiLock->checkImeiLock($array_imei);
            $listImeiLock = '';
            foreach ($getImeiLock as $key => $value) {
                if($key == 0){
                    $listImeiLock = $value['imei_log'];
                }else{
                    $listImeiLock .= '<br>'. $value['imei_log'];
                }
            }
            if($listImeiLock){
                // exit('<div class="alert alert-danger">IMEI Locked<br>' . $listImeiLock .
                //     '<br></div>');

                $flashMessenger->setNamespace('error')->addMessage('IMEI Locked<br>' . $listImeiLock . '<br>');

                echo '<script>parent.location.href="/sales/return-box-number-imei?act=edit&box_sn='.$box_sn.'"</script>';
                exit();
            }

            $db = Zend_Registry::get('db');
            if($frm_action=="create_box_no" && $box_sn !="")
            {

                $db->beginTransaction();

                $date = date('Y-m-d H:i:s');

                //------------------ Box No ----------------//

                $Boxdata = array(
                    'box_post_number'       => $box_post_number,
                    'sender_name'           => $sender_name,
                    'distributor_name'      => $distributor_name,
                    'receive_date'          => $date,
                    'box_status'            => 1,   //0=no process,1=process ,2=success
                    'active'                => 1,   //1=active 0= not active
                    'remark'                => $box_remark,
                    'update_by'             => $userStorage->id,
                    'update_date'           => $date
                );
            
                //insert
                $insert_imei=0;
                foreach ($array_imei as $t=>$imei_data)
                {
                    $create_box_imei==false;
                    if ($imei_data != "") 
                    {
                        $data_imei = array(
                            'box_sn' => $box_sn,
                            'imei_sn' => trim($imei_data),
                            'status' => 0,  //0=pending 1=success
                            'active' => 1,  //1=active 0= not active
                            'check_type' => 0,
                            'create_date' => $date,
                            'create_by' => $userStorage->id
                        );

                        $select_chk_imei="(SELECT COUNT(bi.imei_sn)AS count_imei FROM return_box_imei bi WHERE bi.imei_sn='".trim($imei_data)."' AND bi.box_sn='".$box_sn."' AND bi.finance_confirm_date is null)";
                        $result_chk_imei = $db->fetchAll($select_chk_imei);
                        //print_r($result_chk_imei);die;
                        if($result_chk_imei['count_imei']==0){
                            $create_box_imei=$QReturnBoxNumberImei->insert($data_imei);
                            if($create_box_imei==true){
                                $insert_imei +=1;
                            }
                        }
                    }
                }

                $select_total_imei="(SELECT COUNT(bi.imei_sn)AS count_imei FROM return_box_imei bi WHERE bi.box_sn='".$box_sn."' AND bi.active=1)";
                $result_total_imei = $db->fetchAll($select_total_imei);

                $Boxdata['total_imei']=$result_total_imei[0]['count_imei'];          
                $whereBoxNumber = array();
                $whereBoxNumber[] = $QReturnBoxNumber->getAdapter()->quoteInto('box_sn = ?', $box_sn);
                //print_r($array_action_imei);die;
                $create_box_number = $QReturnBoxNumber->update($Boxdata, $whereBoxNumber);


                //------------------ Imei List ----------------//
                $data=null;
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
                                foreach ($array_action_imei as $kk=>$item_t0){
                                    //$val = explode("-", $item_t0);
                                    $imei_action_imei=$item_t0;
                                    if($imei_action_imei==$imei_action){
                                        $status_action_imei=1;
                                        break;
                                    }else{
                                        $status_action_imei=0;
                                    }
                                }

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

                                foreach ($array_sales_sn as $kk=>$item_t5){
                                    $val = explode("-", $item_t5);
                                    $imei_sales_sn=$val[0];
                                    if($imei_sales_sn==$imei_action){
                                        $sales_sn=$val[1];
                                        break;
                                    }else{
                                        $sales_sn=0;
                                    }
                                }

                                foreach ($array_sum_unit_price as $kk=>$item_t6){
                                    $val = explode("-", $item_t6);
                                    $imei_sum_unit_price=$val[0];
                                    if($imei_sum_unit_price==$imei_action){
                                        $total_amount=$val[1];
                                        break;
                                    }else{
                                        $total_amount=0;
                                    }
                                }

                                foreach ($array_good_id as $kk=>$item_t6){
                                    $val = explode("-", $item_t6);
                                    $imei_good_id=$val[0];
                                    if($imei_good_id==$imei_action){
                                        $good_id=$val[1];
                                        break;
                                    }else{
                                        $good_id=0;
                                    }
                                }

                                foreach ($array_good_color as $kk=>$item_t6){
                                    $val = explode("-", $item_t6);
                                    $imei_good_color=$val[0];
                                    if($imei_good_color==$imei_action){
                                        $good_color=$val[1];
                                        break;
                                    }else{
                                        $good_color=0;
                                    }
                                }

                                foreach ($array_sub_d_id as $kk=>$item_t7){
                                    $val = explode("-", $item_t7);
                                    $imei_good_color=$val[0];
                                    if($imei_good_color==$imei_action){
                                        $cn_to_d_id=$val[2];
                                        break;
                                    }else{
                                        $cn_to_d_id=null;
                                    }
                                }

                                switch ($return_type){
                                    case "1":   //เครื่องเสีย
                                        $data[] = array(
                                            'box_sn'  => $box_sn,
                                            'action_imei'  => $status_action_imei,
                                            'return_type'  => $return_type,
                                            'imei_sn'  => $imei_return_type,
                                            'good_id'  => $good_id,
                                            'good_color'  => $good_color,
                                            'sales_sn'  => $sales_sn,
                                            'total_amount'  => $total_amount,
                                            'distributor_id'  => $distributor_id,
                                            'create_cn'  => $status_create_cn,
                                            'active_cn'  => $status_active_cn,
                                            'warehouse_id'  => $warehouse_id,
                                            'shape_status'  => 4,
                                            'damage_detail'  => $damage_detail,
                                            'remark'  => $remark_text_val,
                                            'rtn_number'  => $rtn_number,
                                            'cn_to_d_id'  => $cn_to_d_id
                                        );
                                        break;
                                    case "3":   //Demo
                                        $data[] = array(
                                            'box_sn'  => $box_sn,
                                            'action_imei'  => $status_action_imei,
                                            'return_type'  => $return_type,
                                            'imei_sn'  => $imei_return_type,
                                            'good_id'  => $good_id,
                                            'good_color'  => $good_color,
                                            'sales_sn'  => $sales_sn,
                                            'total_amount'  => $total_amount,
                                            'distributor_id'  => $distributor_id,
                                            'create_cn'  => $status_create_cn,
                                            'active_cn'  => $status_active_cn,
                                            'warehouse_id'  => $warehouse_id,
                                            'shape_status'  => 1,
                                            'damage_detail'  => $damage_detail,
                                            'remark'  => $remark_text_val,
                                            'rtn_number'  => $rtn_number,
                                            'cn_to_d_id'  => $cn_to_d_id
                                        );
                                        break;
                                    case "4":   //Other
                                        $data[] = array(
                                            'box_sn'  => $box_sn,
                                            'action_imei'  => $status_action_imei,
                                            'return_type'  => $return_type,
                                            'imei_sn'  => $imei_return_type,
                                            'good_id'  => $good_id,
                                            'good_color'  => $good_color,
                                            'sales_sn'  => $sales_sn,
                                            'total_amount'  => $total_amount,
                                            'distributor_id'  => $distributor_id,
                                            'create_cn'  => $status_create_cn,
                                            'active_cn'  => $status_active_cn,
                                            'warehouse_id'  => $warehouse_id,
                                            'shape_status'  => 1,
                                            'damage_detail'  => $damage_detail,
                                            'remark'  => $remark_text_val,
                                            'rtn_number'  => $rtn_number,
                                            'cn_to_d_id'  => $cn_to_d_id
                                        );
                                        break;
                                    case "5":   //EOL
                                        $data[] = array(
                                            'box_sn'  => $box_sn,
                                            'action_imei'  => $status_action_imei,
                                            'return_type'  => $return_type,
                                            'imei_sn'  => $imei_return_type,
                                            'good_id'  => $good_id,
                                            'good_color'  => $good_color,
                                            'sales_sn'  => $sales_sn,
                                            'total_amount'  => $total_amount,
                                            'distributor_id'  => $distributor_id,
                                            'create_cn'  => $status_create_cn,
                                            'active_cn'  => $status_active_cn,
                                            'warehouse_id'  => $warehouse_id,
                                            'shape_status'  => 1,
                                            'damage_detail'  => $damage_detail,
                                            'remark'  => $remark_text_val,
                                            'rtn_number'  => $rtn_number,
                                            'cn_to_d_id'  => $cn_to_d_id
                                        );
                                        break;
                                }
                                break;
                            }
                        }

                    }
                }

                    //print_r($data);die; 

                    // Check Product Group from Imei Select

                    $cn_data=null;
                    foreach ($data as $im=>$imei_val)
                    {
                       // print_r($imei_val);
                        
                        $chk_rtn_number=$imei_val['rtn_number'];
                        if($chk_rtn_number !=''){ //RTN
                            $return_type=$imei_val['return_type'].'-'.$imei_val['create_cn'].'-'.$imei_val['active_cn'].'-'.$imei_val['rtn_number'].'-'.$imei_val['distributor_id'];
                            $imei_list=$imei_val['imei_sn'].",";
                            $distributor_id=$imei_val['distributor_id'];
                            $status_action_imei=$imei_val['action_imei'];
                            $status_create_cn=$imei_val['create_cn'];
                            $status_active_cn=$imei_val['active_cn'];
                            $warehouse_id=$imei_val['warehouse_id'];
                            $shape_status=$imei_val['shape_status'];
                            $rtn_number=$imei_val['rtn_number'];
                            $return_type_id=$imei_val['return_type'];
                            $cn_to_d_id=$imei_val['cn_to_d_id'];
                            $cn_data[$return_type] = array(
                                'box_sn'  => $box_sn,
                                'use_imei'  => $status_use_imei,
                                'return_type'  => $return_type_id,
                                'distributor_id'  => $distributor_id,
                                'sales_sn'  => $sales_sn,
                                'total_amount'  => $total_amount,
                                'create_cn'  => $status_create_cn,
                                'active_cn'  => $status_active_cn,
                                'warehouse_id'  => $warehouse_id,
                                'shape_status'  => $shape_status,
                                'rtn_number'  => $rtn_number,
                                'cn_to_d_id'  => $cn_to_d_id,
                                'imei_sn'  => $cn_data[$return_type]['imei_sn'].=$imei_list
                            );
                        }else{ //Other
                            $return_type=$imei_val['return_type'].'-'.$imei_val['create_cn'].'-'.$imei_val['active_cn'].'-'.$imei_val['rtn_number'].'-'.$imei_val['distributor_id'];
                            
                            $imei_list=$imei_val['imei_sn'].",";
                            $distributor_id=$imei_val['distributor_id'];
                            $status_action_imei=$imei_val['action_imei'];
                            $status_create_cn=$imei_val['create_cn'];
                            $status_active_cn=$imei_val['active_cn'];
                            $warehouse_id=$imei_val['warehouse_id'];
                            $shape_status=$imei_val['shape_status'];
                            $rtn_number="";
                            $return_type_id=$imei_val['return_type'];
                            $cn_to_d_id=$imei_val['cn_to_d_id'];
                            $cn_data[$return_type] = array(
                                'box_sn'  => $box_sn,
                                'use_imei'  => $status_use_imei,
                                'return_type'  => $return_type_id,
                                'distributor_id'  => $distributor_id,
                                'sales_sn'  => $sales_sn,
                                'total_amount'  => $total_amount,
                                'create_cn'  => $status_create_cn,
                                'active_cn'  => $status_active_cn,
                                'warehouse_id'  => $warehouse_id,
                                'shape_status'  => $shape_status,
                                'rtn_number'  => $rtn_number,
                                $cn_to_d_id =>$cn_to_d_id,
                                'imei_sn'  => $cn_data[$return_type]['imei_sn'].=$imei_list
                            );
                        }
                    }

                    //print_r($cn_data);die;
                
                    foreach ($cn_data as $im_cn=>$cn_val)
                    {

                        $distributor_id=$cn_val['distributor_id'];
                        $box_sn=$cn_val['box_sn'];
                        $return_type=$cn_val['return_type'];
                        $status_action_imei=$cn_val['action_imei'];
                        $status_create_cn=$cn_val['create_cn'];
                        $status_active_cn=$cn_val['active_cn'];
                        $warehouse_id=$cn_val['warehouse_id'];
                        $imei_shape_status=$cn_val['shape_status'];
                        $rtn_number=$cn_val['rtn_number'];
                        $cn_to_d_id=$cn_val['cn_to_d_id'];
                        $imei_list=rtrim($cn_val['imei_sn'], ',');
                        $array_imei = explode(",", $imei_list);
                           //print_r($array_imei);die;
                           foreach ($array_imei as $t=>$imei_sn)
                           {
                                $imei = trim($imei_sn);
                                $imei_list_action .=$imei.",";
                                //print_r($imei);die;
                                foreach ($data as $key=>$key_imei_sn)
                                {
                                   // print_r($key_imei_sn);die;
                                    if($imei==$key_imei_sn['imei_sn'])
                                    {
                                        //$dataBoxImeiReturn['status']= 1;
                                        //print_r($key_imei_sn);die;
                                        $dataBoxImeiReturn = array();
                                        $dataBoxImeiReturn['action_imei']= $key_imei_sn['action_imei'];
                                        $dataBoxImeiReturn['imei_sn']= $key_imei_sn['imei_sn'];
                                        $dataBoxImeiReturn['good_id']= $key_imei_sn['good_id'];
                                        $dataBoxImeiReturn['good_color']= $key_imei_sn['good_color'];
                                        $dataBoxImeiReturn['create_cn']= $key_imei_sn['create_cn'];
                                        $dataBoxImeiReturn['active_cn']= $key_imei_sn['active_cn'];
                                        $dataBoxImeiReturn['distributor_id']= $key_imei_sn['distributor_id'];
                                        $dataBoxImeiReturn['return_type']= $key_imei_sn['return_type'];
                                        $dataBoxImeiReturn['warehouse_id']= $key_imei_sn['warehouse_id'];
                                        $dataBoxImeiReturn['rtn_number']= $key_imei_sn['rtn_number'];
                                        $dataBoxImeiReturn['shape_status']= $key_imei_sn['shape_status'];
                                        $dataBoxImeiReturn['remark']= $key_imei_sn['remark'];
                                        $dataBoxImeiReturn['damage_detail']= $key_imei_sn['damage_detail'];
                                        $dataBoxImeiReturn['sales_sn']= $key_imei_sn['sales_sn'];
                                        $dataBoxImeiReturn['total_amount']= $key_imei_sn['total_amount'];
                                        $dataBoxImeiReturn['cn_to_d_id']= $key_imei_sn['cn_to_d_id'];
                                        if($imei_confirm=='staff_confirm')
                                        {
                                            $dataBoxImeiReturn['staff_confirm_date']= $date;
                                            $dataBoxImeiReturn['staff_confirm_by']= $userStorage->id;
                                        }

                                        //print_r($dataBoxImeiReturn);die;
                                        $whereBoxImei = array();
                                        $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                                        $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('box_sn = ?', $box_sn);
                                        //print_r($whereBoxImei);die;
                                        $QReturnBoxNumberImei->update($dataBoxImeiReturn, $whereBoxImei);

                                    }
                                }
                            }

                    }

                //print_r($cn_data);die;

                $imei_list_action = rtrim($imei_list_action, ',');
                //print_r($imei_list_action);die;
                if($imei_list_action!=''){
                    $dataBoxImeiReturn = array();
                    $dataBoxImeiReturn['action_imei']= 0;

                    $whereBoxImei = array();
                    $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('imei_sn not in('.$imei_list_action.')', null);
                    $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('box_sn = ?', $box_sn);
                    //print_r($dataBoxImeiReturn);die;
                    $ress = $QReturnBoxNumberImei->update($dataBoxImeiReturn, $whereBoxImei);
                }
                //print_r($imei_confirm);die;
                if($imei_confirm=='staff_confirm')
                {
                    $array_imei_data_not_oppo = explode("\n", trim($array_imei_not_oppo));
                    $array_imei_data_not_oppo = array_unique($array_imei_data_not_oppo);
                    //print_r($array_imei_data_not_oppo);die;
                    foreach ($array_imei_data_not_oppo as $j=>$imei_sn)
                    {
                        $dataBoxImeiReturn = array();
                        $dataBoxImeiReturn['staff_confirm_date']= $date;
                        $dataBoxImeiReturn['staff_confirm_by']= $userStorage->id;
                        $dataBoxImeiReturn['check_type']= 3; //1=oppo,2=no-return,3=not_oppo
                        $whereBoxImei = array();
                        $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('imei_sn = ?', $imei_sn);
                        $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('box_sn = ?', $box_sn);
                        $QReturnBoxNumberImei->update($dataBoxImeiReturn, $whereBoxImei);

                    }

                    $array_imei_data_not_return = explode("\n", trim($array_imei_not_return));
                    $array_imei_data_not_return = array_unique($array_imei_data_not_return);
                    //print_r($array_imei_data_not_return);die;
                    foreach ($array_imei_data_not_return as $j=>$imei_sn)
                    {
                        $dataBoxImeiReturn = array();
                        $dataBoxImeiReturn['staff_confirm_date']= $date;
                        $dataBoxImeiReturn['staff_confirm_by']= $userStorage->id;
                        $dataBoxImeiReturn['check_type']= 2; //1=oppo,2=no-return,3=not_oppo
                        $whereBoxImei = array();
                        $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('imei_sn = ?', $imei_sn);
                        $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('box_sn = ?', $box_sn);
                        $QReturnBoxNumberImei->update($dataBoxImeiReturn, $whereBoxImei);

                    }
                    //print_r($imei_confirm);die;
                }

                //die;
                $info = 'Return Product Scan Imei : '. $box_number;
                $ip = $this->getRequest()->getServer('REMOTE_ADDR');
                $info .= $box_sn;

                $QLog->insert( array (
                    'info' => $info,
                    'user_id' => $userStorage->id,
                    'ip_address' => $ip,
                    'time' => date('Y-m-d H:i:s'),
                ) );
                    
                    /*if(($insert_imei !=$imei_scan)){
                        $db->rollback();
                        $flashMessenger->setNamespace('error')->addMessage('Imei Scan ('.$imei_scan.') > ('.$insert_imei.')Imei Check, Please try again!');
                    }else{*/

                        if($create_box_number==1)
                        {
                            try{
                                $db->commit(); 
                                $flashMessenger->setNamespace('success')->addMessage('Done!');
                            }catch (Exception $e){
                                $db->rollback();
                                $flashMessenger->setNamespace('error')->addMessage('Cannot Get Return Box Number No, Please try again! ['.$e->getMessage().']');
                            }
                        }else{
                            $db->rollback();
                            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Return Box Number No, Please try again!');
                        }
                    //}
           }
           

        }catch (Exception $e){
            //echo $e.message;
            $db->rollback();

            echo '<script>
                    parent.palert("Cannot Get Return Box Number Imei No, Please try again! ['.$e->getMessage().']");
                  </script>';
            exit;
        }   

}

//echo '<script>parent.location.href="/sales/return-box-number-imei-list"</script>';
if($imei_confirm=='staff_confirm')
{ 
    echo '<script>parent.location.href="/sales/return-box-number-imei-list"</script>';
}else{
    echo '<script>parent.location.href="/sales/return-box-number-imei?act=edit&box_sn='.$box_sn.'"</script>';
}
exit;


