<?php
$flashMessenger = $this->_helper->flashMessenger;
$this->_helper->layout->disableLayout();
//print_r($_POST);die;

if ($this->getRequest()->getMethod() == 'POST')
{
    set_time_limit(0);
    ini_set('memory_limit', -1);
    //------------------ Box No ----------------//

    $lot_sn      = $this->getRequest()->getParam('lot_sn');
    $lot_number      = $this->getRequest()->getParam('lot_number');
    $good_id      = $this->getRequest()->getParam('good_id');
    $price      = $this->getRequest()->getParam('price');
    $cp_date      = $this->getRequest()->getParam('cp_date');
    $remark      = $this->getRequest()->getParam('remark');
    $frm_action      = $this->getRequest()->getParam('frm_action');
    $imei_list      = $this->getRequest()->getParam('imei');
    $imei_confirm      = $this->getRequest()->getParam('imei_confirm');
    $active_cn      = $this->getRequest()->getParam('active_cn');
    $delete_cn      = $this->getRequest()->getParam('delete_cn');

    $check_sub_d_id      = $this->getRequest()->getParam('check_sub_d_id');
    $sub_d_id_list      = $this->getRequest()->getParam('sub_d_id_list');

    //------------------ Imei List ----------------//
    $imei_list_action="";

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $QCPAutoCheckImei = new Application_Model_CPAutoCheckImei();
    $QCPAutoCheckImeiList = new Application_Model_CPAutoCheckImeiList();
    $QCPAutoCheckImeiListLog = new Application_Model_CPAutoCheckImeiListLog();
    $QBvgImei = new Application_Model_BvgImei();
    $QCreditNote = new Application_Model_CreditNote();
    $QDistributor = new Application_Model_Distributor();
    $QLog = new Application_Model_Log();

        try{

            // Delete Imei
            if (isset($delete_cn) and $delete_cn)
            {
                $db = Zend_Registry::get('db');
                $db->beginTransaction();
                try{
                    $array_imei_delete = explode(",", trim($delete_cn[0]));
                    $array_imei_del = array_unique($array_imei_delete);
                    $delete_imei=0;
                    foreach ($array_imei_del as $imei)
                    {
                        //print_r($imei);die;

                        $whereLotImei = array();
                        $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                        $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('imei_sn = ?', $imei);
                        $delete_lot_imei = $QCPAutoCheckImeiList->delete($whereLotImei);
                        //print_r($delete_lot_imei);die;
                        if($delete_lot_imei==true){
                            $delete_imei +=1;
                        }
                    }

                    /*$whereLotImeilog = array();
                    $whereLotImeilog[] = $QCPAutoCheckImeiListLog->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                    $delete_lot_imei_log = $QCPAutoCheckImeiListLog->delete($whereLotImeilog);*/

                    $select_imei_total="SELECT ifnull(COUNT(bi.imei_sn),0) AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.lot_sn='".$lot_sn."'";
                    $result_chk_imei_total = $db->fetchAll($select_imei_total);
                    //print_r($result_chk_imei_total[0]['count_imei']);die;
                    $dataLotImei = array(
                        'total_imei' => $result_chk_imei_total[0]['count_imei']
                    );
                    $whereLotImei = array();
                    $whereLotImei = $QCPAutoCheckImei->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                    $update_lot_number = $QCPAutoCheckImei->update($dataLotImei, $whereLotImei);

                    if(count($array_imei_del)==$delete_imei){
                        $flashMessenger->setNamespace('success')->addMessage('Done!');
                        $db->commit();

                        echo '<script>parent.location.href="/finance/cp-auto-check-imei?act=edit&lot_sn='.$lot_sn.'&lot_number='.$lot_number.'&cp_price='.$price.'"</script>';
                        exit;
                    }else{
                        $db->rollback();
                        $flashMessenger->setNamespace('error')->addMessage('Cannot Delete Imei Data, Please try again!');
                        //exit;
                    }

                }catch (Exception $e){
                        echo $e.message;
                        $db->rollback();

                        echo '<script>
                                parent.palert("Cannot Update Data Imei No, Please try again!! ['.$e.message.']");
                              </script>';
                        exit;
                } 
            }
            
            if($imei_confirm=='finance_confirm')
            { 
                //print_r($_POST);
                //die;
                if ($cp_date !='')
                {
                    list( $day, $month, $year ) = explode('/', $cp_date);
                    list( $year,$time ) = explode(' ', $year);

                    if (isset($day) and isset($month) and isset($year) ){
                        $cp_date = $year.'-'.$month.'-'.$day.' '.$time;
                    }
                }

                $db = Zend_Registry::get('db');
                $db->beginTransaction();

                $imei_check=null;
                $imei_check = $QCPAutoCheckImei->checkPriceProtectionImeiAutoCheckListAction($lot_sn,"finance",$check_sub_d_id);
                //print_r($imei_check);die;
                
                $create_date = date('Y-m-d H:i:s');
                $sn = date('YmdHis') . substr(microtime(), 2, 1);
                $data_sn = array();
                foreach ($imei_check['result_by_imei'] as $imei)
                {
                    if($imei['sub_d_id'] !=""){
                        $key_sn = $imei['distributor_id'].$imei['sub_d_id'].substr($sn,6);
                    }else{
                        $key_sn = $imei['distributor_id'].$sn;
                    }
                    
                    $status_cn = 0;

                    for ($j = 0; $j < count($active_cn); $j++)
                    {

                        $arr_chk_list=$active_cn[$j];
                        $array_imei_data_list = explode(";", trim($arr_chk_list));
                        $array_imei_data = explode(",", trim($array_imei_data_list[1]));
                        $array_imei = array_unique($array_imei_data);
                        //print_r($array_imei);die;
                        if (in_array($imei['imei_sn'], $array_imei)) {
                            $status_cn=1;
                        }
                    }
                    //echo $key_sn;
                    //print_r($imei);die;
                    if($QCPAutoCheckImei->check_imei($imei['imei_sn'],$imei['distributor_id'])>0)
                    {
                        //print_r($imei['imei_sn']);die;
                        if($QCPAutoCheckImei->check_imei_lot($lot_sn,$imei['imei_sn'],$imei['distributor_id'])>0)
                        {
                            //insert into bvg_imei
                            
                            $data_imei_lot = array(
                                'good_id' => $imei['good_id'],
                                'good_color' => $imei['good_color'],
                                'distributor_id' => $imei['distributor_id'],
                                'sub_d_id' => $imei['sub_d_id'],
                                'rank_price_id' => $imei['rank_price'],
                                'spc_discount' => $imei['spc_discount'],
                                'margin' => $imei['margin'],
                                'unit_price' => $imei['unit_price'],
                                'invoice_price' => $imei['invoice_price'],
                                'sales_sn' => $imei['sales_sn'],
                                'margin' => $imei['margin'],
                                'finance_confirm_by' => $userStorage->id,
                                'finance_confirm_date' => $create_date,
                                'active_cn' => $status_cn,
                                'key_sn' => $key_sn
                            );

                            $whereLotImei = array();
                            $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                            $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('imei_sn = ?', $imei['imei_sn']);
                            $update_lot_number = $QCPAutoCheckImeiList->update($data_imei_lot, $whereLotImei);
                            //echo $cp_date;die;
                            $imei_check_timing_sale = $QCPAutoCheckImei->check_imei_timing_sale($imei['imei_sn'],$good_id,$cp_date);
                            //print_r($imei_check_timing_sale);die;
                            if($imei_check_timing_sale[0]['distributor_id'] !='' && $imei_check_timing_sale[0]['imei_sn'] !='' && $imei_check_timing_sale[0]['check_out_date_status'] =='1' && $imei_check_timing_sale[0]['check_timing_status'] =='1' && $imei_check_timing_sale[0]['check_activated_status'] =='1')
                            {
                                //$create_lot_imei=$QCPAutoCheckImeiList->insert($data_imei);
                                $d_id = $imei['distributor_id'];
                                $sub_d_id = $imei['sub_d_id'];
                                $data_imei = array(
                                    'lot_sn' => $lot_sn,
                                    'imei_sn' => $imei['imei_sn'],
                                    'seq' => $imei['seq'],
                                    'good_id' => $imei['good_id'],
                                    'good_color' => $imei['good_color'],
                                    'd_id' => $d_id,
                                    'joint_circular_id' => 28,
                                    'check_imei_info' => true,
                                    'check_status' => true,
                                    'check_dealer' => false,
                                    'get_invoice' => false,
                                    'get_price' => false,
                                    'cp_date' => $cp_date,
                                    'status' => 1,
                                    'invoice_number' => $imei['invoice_number'],
                                    'invoice_date' => $imei['invoice_time'],
                                    'price' => $imei['unit_price'],
                                    'total_price' => $imei['unit_price'],
                                    'out_price' => $imei['invoice_price'],
                                    'sn' => $key_sn,
                                    'create_by' => $userStorage->id,
                                    'create_date' => $create_date,
                                    'finance_confirm_date' => $create_date,
                                    'remark' => $remark
                                );

                                if($imei['imei_sn'] !=''){
                                    $data_imei['cat_id']=11;
                                }

                                if($sub_d_id !="")
                                {
                                    $data_sn[$sub_d_id]['key_sn'] = $key_sn;
                                    $data_sn[$sub_d_id]['status_cn'] = $status_cn;
                                    $data_sn[$sub_d_id]['lot_sn'] = $lot_sn;
                                    $data_sn[$sub_d_id]['cp_price'] += $imei['unit_price'];
                                    $data_sn[$sub_d_id]['remark'] = $remark;
                                    $data_sn[$sub_d_id]['d_id'] = $d_id;
                                    $data_sn[$sub_d_id]['sub_d_id'] = $imei['sub_d_id'];
                                }else{
                                    $data_sn[$d_id]['key_sn'] = $key_sn;
                                    $data_sn[$d_id]['status_cn'] = $status_cn;
                                    $data_sn[$d_id]['lot_sn'] = $lot_sn;
                                    $data_sn[$d_id]['cp_price'] += $imei['unit_price'];
                                    $data_sn[$d_id]['remark'] = $remark;
                                    $data_sn[$d_id]['d_id'] = $d_id;
                                    $data_sn[$d_id]['sub_d_id'] = $imei['sub_d_id']; 
                                }
                                
                                //print_r($data_imei);die;
                                $result_imei = $QBvgImei->save($data_imei);
                                $status = $result_imei['code'];
                                //print_r($result_imei);die;
                                $status=0;

                            }else{
                                //print_r($imei_check_timing_sale[0]);die;
                                if($imei_check_timing_sale[0]['check_timing_status'] =='0'){
                                    $remark_timing=" [ขายออกก่อนวันปรับราคา ".$imei_check_timing_sale[0]['timing_date']."]";
                                }else{
                                    $remark_timing="";
                                }

                                if($imei_check_timing_sale[0]['check_activated_status'] =='0'){
                                    $remark_activated=" [วันที่ activate น้อยกว่าวันปรับราคา ".$imei_check_timing_sale[0]['activated_date']."]";
                                }else{
                                    $remark_activated="";
                                }

                                if($imei_check_timing_sale[0]['check_out_date_status'] =='0'){
                                    $remark_out_date=" [วันที่ Sell IN มากกว่าวันปรับราคา ".$imei_check_timing_sale[0]['out_date']."]";
                                }else{
                                    $remark_out_date="";
                                }
                                //echo $remark;die;
                                //print_r($imei);die;
                                $lot_imei_error[] = array(
                                    'lot_sn'                    => $lot_sn,
                                    'imei_sn'                   => $imei['imei_sn'],
                                    'remark'                    => $remark_timing.$remark_activated.$remark_out_date,
                                    'check_out_date_status'     => $imei_check_timing_sale[0]['check_out_date_status'],  //1=ok 0= no
                                    'check_timing_status'       => $imei_check_timing_sale[0]['check_timing_status'],  //1=ok 0= no
                                    'check_activated_status'    => $imei_check_timing_sale[0]['check_activated_status'],  //1=ok 0= no
                                    'out_date'                  => $imei_check_timing_sale[0]['out_date'],
                                    'timing_date'               => $imei_check_timing_sale[0]['timing_date'],
                                    'activated_date'            => $imei_check_timing_sale[0]['activated_date'],
                                    'cp_date'                   => $imei_check_timing_sale[0]['cp_date'],
                                    'distributor_id'            => $imei_check_timing_sale[0]['distributor_id']
                                ); 
                                //print_r($lot_imei_error);die;

                                $whereLotImei = array();
                                $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                                $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('imei_sn = ?', $imei['imei_sn']);
                                //print_r($whereLotImei);die;
                                $delete_lot_imei = $QCPAutoCheckImeiList->delete($whereLotImei);

                            }  
                        }
                    }

                }

                //print_r($lot_imei_error);die;
                //echo 123;die;
                for ($j = 0; $j < count($lot_imei_error); $j++)
                {
                    $chk_imei = trim($lot_imei_error[$j]['imei_sn']);
                    $select_chk_imei_log="SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list_log bi WHERE bi.imei_sn='".$chk_imei."' AND bi.lot_sn='".$lot_sn."'";

                    $result_chk_imei_log = $db->fetchAll($select_chk_imei_log);

                    //print_r($result_chk_imei_log);die;
                    if($result_chk_imei_log[0]['count_imei']==1){
                        $imei_error = trim($lot_imei_error[$j]['imei_sn']);
                        $whereLogImei = array();
                        $whereLogImei[] = $QCPAutoCheckImeiListLog->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                        $whereLogImei[] = $QCPAutoCheckImeiListLog->getAdapter()->quoteInto('imei_sn = ?', $imei_error);
                        $QCPAutoCheckImeiListLog->delete($whereLogImei);
                        //print_r($whereLogImei);die; 
                    }

                        $imei_error = trim($lot_imei_error[$j]['imei_sn']);
                        $check_timing_status = $lot_imei_error[$j]['check_timing_status'];
                        $timing_date = $lot_imei_error[$j]['timing_date'];
                        $out_date = $lot_imei_error[$j]['out_date'];
                        $remark = $lot_imei_error[$j]['remark'];
                        $distributor_id = $lot_imei_error[$j]['distributor_id'];
                        $data_imei_log = array(
                            'lot_sn'                => $lot_sn,
                            'distributor_id'        => $distributor_id,
                            'imei_sn'               => $imei_error,
                            'cp_date'               => $cp_date,
                            'out_date'              => $out_date,
                            'remark'                => $remark,
                            'create_by'             => $userStorage->id,
                            'create_date'           => $create_date
                        );

                        if($check_timing_status !=''){
                            $data_imei_log['check_timing_status']=$check_timing_status;
                        }
                        if($timing_date !=''){
                            $data_imei_log['timing_date']=$timing_date;
                        }
                        //print_r($data_imei_log);die;   

                        $create_lot_imei_log=$QCPAutoCheckImeiListLog->insert($data_imei_log);
                        if($create_lot_imei_log==true){
                            $count_imei+=1;
                        }
                }

                    /*-----------------------------*/

                    //print_r($data_sn);die;
                    foreach($data_sn as $key => $val)
                    {
                        //echo "$v";
                        //$d_id = $key;
                        $d_id = $val['d_id'];
                        $key_sn = $val['key_sn'];
                        $lot_sn = $val['lot_sn'];
                        $status_cn = $val['status_cn'];
                        $cp_price = $val['cp_price'];
                        $remark = $val['remark'];
                        $sub_d_id = $val['sub_d_id'];
                        $creditnote_sn='';

                        if($key_sn!='' && $status==0)
                        {
                            if($check_sub_d_id =="1")
                            {
                                $creditnote_sn = $QCPAutoCheckImei->getPCNo_Ref($db,$key_sn);
                            }else{
                                $creditnote_sn = $QCPAutoCheckImei->getProtectionPriceNo_Ref($db,$key_sn);
                            }
                            //$creditnote_sn = $QCPAutoCheckImei->getPCNo_Ref($db,$key_sn);
                            $data = array(
                                'distributor_id' => $d_id,
                                'sub_d_id' => $sub_d_id,
                                'create_by' => $userStorage->id,
                                'create_date' => $create_date,
                                'creditnote_type' => 'CP',
                                'total_amount' => $cp_price,
                                'use_total' => 0,
                                'cn_type' => 2,
                                'balance_total' => $cp_price,
                                'remark' => $remark,
                                'status' => $status_cn,
                                'chanel' => 'price_protection',
                                'creditnote_sn' => $creditnote_sn,
                                'sn' => $key_sn,
                                'lot_sn' => $lot_sn
                            );
                            //print_r($data);die;
                            $QCreditNote->insert($data);

                            if($creditnote_sn==''){
                                $sError='Cannot Import Credit Note.';
                                $this->view->error = $sError;
                                $db->rollback();
                                return;
                            }

                            $dataImei = array(
                                'creditnote_date' => $create_date,
                                'creditnote_sn' => $creditnote_sn
                            );
                            $whereImei = array();
                            $whereImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('key_sn = ?', $key_sn);
                            $whereImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                            if($check_sub_d_id =="1")
                            {
                                $whereImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('sub_d_id = ?', $sub_d_id);
                            }
                            $QCPAutoCheckImeiList->update($dataImei, $whereImei);  

                            $data = array(
                                'cp_date' => $cp_date,
                                'create_date' => $create_date,
                                'creditnote_sn' => $creditnote_sn
                            );
                            $whereBvgImei = array();
                            $whereBvgImei[] = $QBvgImei->getAdapter()->quoteInto('sn = ?', $key_sn);
                            $whereBvgImei[] = $QBvgImei->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                            $QBvgImei->update($data, $whereBvgImei);
  
                        }
                    }

                $select_imei_total="SELECT ifnull(COUNT(bi.imei_sn),0) AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.lot_sn='".$lot_sn."'";
                $result_chk_imei_total = $db->fetchAll($select_imei_total);
                //print_r($result_chk_imei_total[0]['count_imei']);die;
                $dataLotImei = array(
                    'total_imei' => $result_chk_imei_total[0]['count_imei']
                );
                $whereLotImei = array();
                $whereLotImei = $QCPAutoCheckImei->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                $update_lot_number = $QCPAutoCheckImei->update($dataLotImei, $whereLotImei);
                     
                //print_r($status);die;
                if(1==1){
                    $flashMessenger->setNamespace('success')->addMessage('Done!');
                    $db->commit();

                    echo '<script>parent.location.href="/finance/cp-auto-check-imei?act=edit&lot_sn='.$lot_sn.'&lot_number='.$lot_number.'&cp_price='.$price.'"</script>';
                    exit;
                }else{
                    $db->rollback();
                    $flashMessenger->setNamespace('error')->addMessage('Cannot Confirm Data, Please try again!');
                    //exit;
                }

            }else{

                // Save Data Only
                $array_imei_data = explode("\n", trim($imei_list));
                $array_imei = array_unique($array_imei_data);
                if ($cp_date !=''){
                    list( $day, $month, $year ) = explode('/', $cp_date);
                    list( $year,$time ) = explode(' ', $year);

                    if (isset($day) and isset($month) and isset($year) ){
                        $cp_date = $year.'-'.$month.'-'.$day.' '.$time;
                    }
                }

                //print_r($cp_date);die;

                if ($lot_sn)
                {
                    $imei_return_check=null;
                    $QCPAutoCheckImei= new Application_Model_CPAutoCheckImei();
                    if($lot_sn !=''){                        
                        $imei_check_confirm=null;
                        $imei_check_confirm = $QCPAutoCheckImei->getImeiLotConfirm($lot_sn);
                    }
                    //$this->view->imei_check_confirm = $imei_check_confirm;
                    //print_r($chk['total_finance_confirm_imei']);
                    //print_r($imei_check_confirm);die;
                }


                $db = Zend_Registry::get('db');
                $db->beginTransaction();

                    $date = date('Y-m-d H:i:s');
                    $total_imei=0;

                    if($imei_check_confirm[0]['total_finance_confirm_imei']<=0)
                    {

                        $imei_check=null;
                        $imei_check = $QCPAutoCheckImei->checkPriceProtectionImeiAutoCheckListAction($lot_sn,"finance",$check_sub_d_id);
                        //print_r($imei_check);die;
                        foreach ($imei_check['result_by_imei'] as $imei)
                        {
                            $imei_check_timing_sale = $QCPAutoCheckImei->check_imei_timing_sale($imei['imei_sn'],$good_id,$cp_date);
                            if($imei_check_timing_sale[0]['distributor_id'] !='' && $imei_check_timing_sale[0]['imei_sn'] !='' && $imei_check_timing_sale[0]['check_out_date_status'] =='1' && $imei_check_timing_sale[0]['check_timing_status'] =='1' && $imei_check_timing_sale[0]['check_activated_status'] =='1')
                                {
                                    //$create_lot_imei=$QCPAutoCheckImeiList->insert($data_imei);

                            }
                            else
                            {
                                //print_r($imei_check_timing_sale[0]);die;
                                if($imei_check_timing_sale[0]['check_timing_status'] =='0'){
                                    $remark_timing=" [ขายออกก่อนวันปรับราคา ".$imei_check_timing_sale[0]['timing_date']."]";
                                }else{
                                    $remark_timing="";
                                }

                                if($imei_check_timing_sale[0]['check_activated_status'] =='0'){
                                    $remark_activated=" [วันที่ activate น้อยกว่าวันปรับราคา ".$imei_check_timing_sale[0]['activated_date']."]";
                                }else{
                                    $remark_activated="";
                                }

                                if($imei_check_timing_sale[0]['check_out_date_status'] =='0'){
                                    $remark_out_date=" [วันที่ Sell IN มากกว่าวันปรับราคา ".$imei_check_timing_sale[0]['out_date']."]";
                                }else{
                                    $remark_out_date="";
                                }
                                //echo $remark;die;
                                $lot_imei_error[] = array(
                                    'lot_sn'                    => $lot_sn,
                                    'imei_sn'                   => $imei['imei_sn'],
                                    'remark'                    => $remark_timing.$remark_activated.$remark_out_date,
                                    'check_out_date_status'     => $imei_check_timing_sale[0]['check_out_date_status'],  //1=ok 0= no
                                    'check_timing_status'       => $imei_check_timing_sale[0]['check_timing_status'],  //1=ok 0= no
                                    'check_activated_status'    => $imei_check_timing_sale[0]['check_activated_status'],  //1=ok 0= no
                                    'out_date'                  => $imei_check_timing_sale[0]['out_date'],
                                    'timing_date'               => $imei_check_timing_sale[0]['timing_date'],
                                    'activated_date'            => $imei_check_timing_sale[0]['activated_date'],
                                    'cp_date'                   => $imei_check_timing_sale[0]['cp_date'],
                                    'distributor_id'            => $imei_check_timing_sale[0]['distributor_id']
                                ); 
                                //print_r($lot_imei_error);die;

                                $whereLotImei = array();
                                $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                                $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('imei_sn = ?', $imei['imei_sn']);
                                //print_r($whereLotImei);die;
                                $delete_lot_imei = $QCPAutoCheckImeiList->delete($whereLotImei);

                            }

                            for ($j = 0; $j < count($lot_imei_error); $j++)
                            {
                                $chk_imei = trim($lot_imei_error[$j]['imei_sn']);
                                $select_chk_imei_log="SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list_log bi WHERE bi.imei_sn='".$chk_imei."' AND bi.lot_sn='".$lot_sn."'";

                                $result_chk_imei_log = $db->fetchAll($select_chk_imei_log);

                                //print_r($result_chk_imei_log);die;
                                if($result_chk_imei_log[0]['count_imei']==1){
                                    $imei_error = trim($lot_imei_error[$j]['imei_sn']);
                                    $whereLogImei = array();
                                    $whereLogImei[] = $QCPAutoCheckImeiListLog->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                                    $whereLogImei[] = $QCPAutoCheckImeiListLog->getAdapter()->quoteInto('imei_sn = ?', $imei_error);
                                    $QCPAutoCheckImeiListLog->delete($whereLogImei);
                                    //print_r($whereLogImei);die; 
                                }

                                    $imei_error = trim($lot_imei_error[$j]['imei_sn']);
                                    $check_timing_status = $lot_imei_error[$j]['check_timing_status'];
                                    $timing_date = $lot_imei_error[$j]['timing_date'];
                                    $out_date = $lot_imei_error[$j]['out_date'];
                                    $remark = $lot_imei_error[$j]['remark'];
                                    $distributor_id = $lot_imei_error[$j]['distributor_id'];
                                    $data_imei_log = array(
                                        'lot_sn'                => $lot_sn,
                                        'distributor_id'        => $distributor_id,
                                        'imei_sn'               => $imei_error,
                                        'cp_date'               => $cp_date,
                                        'out_date'              => $out_date,
                                        'remark'                => $remark,
                                        'create_by'             => $userStorage->id,
                                        'create_date'           => $date
                                    );

                                    if($check_timing_status !=''){
                                        $data_imei_log['check_timing_status']=$check_timing_status;
                                    }
                                    if($timing_date !=''){
                                        $data_imei_log['timing_date']=$timing_date;
                                    }
                                    //print_r($data_imei_log);die;   

                                    $create_lot_imei_log=$QCPAutoCheckImeiListLog->insert($data_imei_log);
                                    if($create_lot_imei_log==true){
                                        $count_imei+=1;
                                    }
                            }

                        }
                    }


                    $count_imei=0;
                    
                    $imei_check = $QCPAutoCheckImei->checkPriceProtectionImeiAutoCheckListAction($lot_sn,"finance",$check_sub_d_id);

                    //-------------By Imei--------------------
                    $update_imei=0;
                    foreach ($sub_d_id_list as $imei_sub)
                    {
                        
                        $array_imei_sub = explode("-", trim($imei_sub));
                        $array_imei_chk = array_unique($array_imei_sub);

                        $dataSubImei = array(
                            'sub_d_id'              => $array_imei_chk[2]
                        );
                        
                        $whereLotImei = array();
                        $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                        $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('imei_sn = ?', $array_imei_chk[0]);
                        //print_r($whereLotImei);die;
                        $update_lot_imei = $QCPAutoCheckImeiList->update($dataSubImei,$whereLotImei);

                        //print_r($update_lot_imei);die;
                        if($update_lot_imei==true){
                            $update_imei +=1;
                        }
                    }

                    //---------------------------------
                    //print_r($imei_check);die;

                    $lot_imei_error = array();

                    //print_r($data_imei_log);die;
                    /*--------Imei Lot----------*/
                    $data_imei_lot = array(
                            'active_cn' => 0
                    );
                    $whereLotImei = array();
                    $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                    $update_lot_number = $QCPAutoCheckImeiList->update($data_imei_lot, $whereLotImei);

                    /*--------Imei BVG----------*/
                    $data = array(
                            'status' => 0
                        );
                        $whereBvgImei = array();
                        $whereBvgImei[] = $QBvgImei->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                        $update_BvgImei = $QBvgImei->update($data, $whereBvgImei);

                    /*--------CreditNote----------*/
                    $dataImei = array(
                            'status' => 0
                        );
                        $whereImei = array();
                        $whereImei[] = $QCreditNote->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                        $update_CreditNote = $QCreditNote->update($dataImei, $whereImei);

                        //print_r($active_cn);die;
                        for ($j = 0; $j < count($active_cn); $j++)
                        {

                            $arr_chk_list=$active_cn[$j];

                            $array_imei_active = explode(";", trim($arr_chk_list));
                            $array_active = array_unique($array_imei_active);
                            //print_r($array_active);die;

                            $cp = $array_active[0];
                            $imei_cp = $array_active[1];
                            
                            //--------Imei Lot----------
                            $data_imei_lot = array(
                                'active_cn' => 1
                            );

                            $whereLotImei = array();
                            $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);

                            if($cp !=''){
                                $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('creditnote_sn = ?', $cp);
                            }else{
                                $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('imei_sn in('.$imei_cp.')',null);
                            }
                            $update_ImeiList = $QCPAutoCheckImeiList->update($data_imei_lot, $whereLotImei);
                            //print_r($whereLotImei);die;

                            //--------Imei BVG----------
                            $data = array(
                                'status' => 1
                            );
                            $whereBvgImei = array();
                            $whereBvgImei[] = $QBvgImei->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                            if($cp !=''){
                                $whereBvgImei[] = $QBvgImei->getAdapter()->quoteInto('creditnote_sn = ?', $cp);
                            }else{
                                $whereBvgImei[] = $QBvgImei->getAdapter()->quoteInto('imei_sn in('.$imei_cp.')',null);
                            }
                            $update_BvgImei = $QBvgImei->update($data, $whereBvgImei);
                            //print_r($data);print_r($whereBvgImei);die;
                            //--------CreditNote----------
                            $dataImei = array(
                                'status' => 1
                            );
                            $whereImei = array();
                            $whereImei[] = $QCreditNote->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                            if($cp !=''){
                                $whereImei[] = $QCreditNote->getAdapter()->quoteInto('creditnote_sn = ?', $cp);
                                $update_CreditNote = $QCreditNote->update($dataImei, $whereImei);
                            }
                        }
                    

                    $select_imei_total="SELECT ifnull(COUNT(bi.imei_sn),0) AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.lot_sn='".$lot_sn."'";
                    $result_chk_imei_total = $db->fetchAll($select_imei_total);
                    //print_r($result_chk_imei_total[0]['count_imei']);die;
                    $dataLotImei = array(
                        'total_imei' => $result_chk_imei_total[0]['count_imei']
                    );
                    $whereLotImei = array();
                    $whereLotImei = $QCPAutoCheckImei->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                    $update_lot_number = $QCPAutoCheckImei->update($dataLotImei, $whereLotImei);

                    //echo "OK";echo "imei_check>".count($imei_check);echo "count_imei>".$count_imei;die;
                    //if(count($imei_check)<=0 && $count_imei >0)
                    if($count_imei >=0)
                    {
                        //echo "OK";die;
                        $flashMessenger->setNamespace('success')->addMessage('Done!');
                        $db->commit();

                        echo '<script>parent.location.href="/finance/cp-auto-check-imei?act=edit&lot_sn='.$lot_sn.'&lot_number='.$lot_number.'&cp_price='.$price.'"</script>';
                        exit;
                    //}else if($update_ImeiList==true && $update_BvgImei==true && $update_CreditNote==true && $update_lot_number==true){    
                    }else if($update_lot_number==true){
                        //echo "OK1";die;
                        $flashMessenger->setNamespace('success')->addMessage('Done!');
                        $db->commit();

                        echo '<script>parent.location.href="/finance/cp-auto-check-imei?act=edit&lot_sn='.$lot_sn.'&lot_number='.$lot_number.'&cp_price='.$price.'"</script>';
                        exit;
                    }else{

                        $db->rollback();
                        echo "error";echo "imei_check>".count($imei_check);echo "count_imei>".$count_imei;print_r($lot_imei_error);
                        $flashMessenger->setNamespace('error')->addMessage('Cannot Update Data, Please try again!');
                        exit;
                    }

           }

        }catch (Exception $e){
            echo $e.message;
            $db->rollback();

            echo '<script>
                    parent.palert("Cannot Update Data Imei No, Please try again!! ['.$e.message.']");
                  </script>';
            exit;
        }   

}



