<?php
$flashMessenger = $this->_helper->flashMessenger;
$this->_helper->layout->disableLayout();
//print_r($_POST);die;

if ($this->getRequest()->getMethod() == 'POST')
{
    set_time_limit(0);
    ini_set('memory_limit', -1);

    //print_r($_POST);die;
    //------------------ Box No ----------------//

    $lot_sn      = $this->getRequest()->getParam('lot_sn');
    $lot_number      = $this->getRequest()->getParam('lot_number');
    $good_id      = $this->getRequest()->getParam('good_id');
    $price      = $this->getRequest()->getParam('price');
    $new_price      = $this->getRequest()->getParam('new_price');
    $cp_date      = $this->getRequest()->getParam('cp_date');
    $detail      = $this->getRequest()->getParam('detail');
    $remark      = $this->getRequest()->getParam('remark');
    $frm_action      = $this->getRequest()->getParam('frm_action');
    $imei_list      = $this->getRequest()->getParam('imei');
    $imei_confirm      = $this->getRequest()->getParam('imei_confirm');
    $active_cn      = $this->getRequest()->getParam('active_cn');
    $delete_cn      = $this->getRequest()->getParam('delete_cn');
    $check_sub_d_id      = $this->getRequest()->getParam('check_sub_d_id');
    $sub_d_id_list      = $this->getRequest()->getParam('sub_d_id_list');
    $finance_group      = $this->getRequest()->getParam('finance_group');
    $check_cost      = $this->getRequest()->getParam('check_cost');
    $price_limit      = $this->getRequest()->getParam('price_limit');
    $no_use_spc_discount      = $this->getRequest()->getParam('no_use_spc_discount');
    $auto_imei      = $this->getRequest()->getParam('auto_imei');
    $start_import_auto_date      = $this->getRequest()->getParam('start_import_auto_date');
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

    if($price_limit=='')
    {
        $price_limit=0;
    }

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

                        $whereLotImeilog = array();
                        $whereLotImeilog[] = $QCPAutoCheckImeiListLog->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                        $whereLotImeilog[] = $QCPAutoCheckImeiListLog->getAdapter()->quoteInto('imei_sn = ?', $imei);
                        $delete_lot_imei_log = $QCPAutoCheckImeiListLog->delete($whereLotImeilog);

                        //print_r($whereLotImeilog);die;
                        if($delete_lot_imei==true){
                            $delete_imei +=1;
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

                    if(count($array_imei_del)==$delete_imei){
                        $flashMessenger->setNamespace('success')->addMessage('Done!');
                        $db->commit();

                        echo '<script>parent.location.href="/sales/cp-auto-check-imei?act=edit&lot_sn='.$lot_sn.'&lot_number='.$lot_number.'&cp_price='.$price.'&cp_new_price='.$new_price.'"</script>';
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
            
            if($imei_confirm=='sales_confirm')
            { 
                //print_r($_POST);
                //die;
                set_time_limit(0);
                ini_set('memory_limit', -1);

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
                $imei_check = $QCPAutoCheckImei->checkPriceProtectionImeiAutoCheckListAction($lot_sn,"salse",$check_sub_d_id);
                //print_r($imei_check['result_by_imei']);die;
                $sn = date('YmdHis') . substr(microtime(), 2, 1);
                $create_date = date('Y-m-d H:i:s');
                foreach ($imei_check['result_by_imei'] as $imei)
                {
                    $key_sn = $imei['distributor_id'].$sn;
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
                                'rank_price_id' => $imei['rank_price'],
                                'spc_discount' => $imei['spc_discount'],
                                'margin' => $imei['margin'],
                                'unit_price' => $imei['unit_price'],
                                'invoice_price' => $imei['invoice_price'],
                                'sales_sn' => $imei['sales_sn'],
                                'margin' => $imei['margin'],
                                'sales_confirm_by' => $userStorage->id,
                                'sales_confirm_date' => $create_date,
                                'active_cn' => $status_cn,
                                /*'sub_d_id' => $sub_d_id,*/
                                'key_sn' => $key_sn
                            );

                            $whereLotImei = array();
                            $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                            $whereLotImei[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('imei_sn = ?', $imei['imei_sn']);
                            $update_lot_number = $QCPAutoCheckImeiList->update($data_imei_lot, $whereLotImei);

                            $imei_check_timing_sale = $QCPAutoCheckImei->check_imei_timing_sale($imei['imei_sn'],$good_id,$cp_date);

                            if($imei_check_timing_sale[0]['distributor_id'] !='' && $imei_check_timing_sale[0]['imei_sn'] !='' && $imei_check_timing_sale[0]['check_out_date_status'] =='1' && $imei_check_timing_sale[0]['check_timing_status'] =='1' && $imei_check_timing_sale[0]['check_activated_status'] =='1')
                            {
                                //$create_lot_imei=$QCPAutoCheckImeiList->insert($data_imei);

                            }else
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
                        }
                    }

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

                $select_imei_total="SELECT ifnull(COUNT(bi.imei_sn),0) AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.lot_sn='".$lot_sn."'";
                $result_chk_imei_total = $db->fetchAll($select_imei_total);
                //print_r($result_chk_imei_total[0]['count_imei']);die;
                $dataLotImei = array(
                    'total_imei' => $result_chk_imei_total[0]['count_imei']
                );
                $whereLotImei = array();
                $whereLotImei = $QCPAutoCheckImei->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                //print_r($dataLotImei);die;
                $update_lot_number = $QCPAutoCheckImei->update($dataLotImei, $whereLotImei);
                //print_r($update_lot_number);die;
                if(1==1){
                    $flashMessenger->setNamespace('success')->addMessage('Done!');
                    $db->commit();

                    echo '<script>parent.location.href="/sales/cp-auto-check-imei?act=edit&lot_sn='.$lot_sn.'&lot_number='.$lot_number.'&cp_price='.$price.'&cp_new_price='.$price.'"</script>';
                    exit;
                }else{
                    $db->rollback();
                    $flashMessenger->setNamespace('error')->addMessage('Cannot Confirm Data, Please try again!');
                    //exit;
                }

            }else{

                // Save Data Only
                
                set_time_limit(0);
                ini_set('memory_limit', -1);
                
                $array_imei_data = explode("\r\n", trim($imei_list));
                $array_imei = array_unique($array_imei_data);

                //print_r($array_imei);die;

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
                    // echo 'IMEI Locked<br>' . $listImeiLock . '<br>'
                    $flashMessenger->setNamespace('error')->addMessage('IMEI Locked<br>' . $listImeiLock . '<br>');
                    echo '<script>parent.location.href="/sales/cp-auto-check-imei?act=edit&lot_sn='.$lot_sn.'&lot_number='.$lot_number.'&cp_price='.$price.'&cp_new_price='.$new_price.'"</script>';
                        exit;
                exit();
                }

                // echo json_encode(array('error' => $e->getMessage()));
                // exit;

                if ($cp_date !='')
                {
                    list( $day, $month, $year ) = explode('/', $cp_date);
                    list( $year,$time ) = explode(' ', $year);

                    if (isset($day) and isset($month) and isset($year) ){
                        $cp_date = $year.'-'.$month.'-'.$day.' '.$time;
                    }
                }

                if ($start_import_auto_date !='')
                {
                    list( $day, $month, $year ) = explode('/', $start_import_auto_date);
                    list( $year,$time ) = explode(' ', $year);

                    if (isset($day) and isset($month) and isset($year) ){
                        $start_import_auto_date = $year.'-'.$month.'-'.$day.' '.$time;
                    }
                }
                //print_r($sub_d_id_list);die;
                //print_r($cp_date);die;

                $db = Zend_Registry::get('db');
                $db->beginTransaction();

                    $date = date('Y-m-d H:i:s');
                    $total_imei=0;

                    if($array_imei!=''){
                        $total_imei = count($array_imei);
                    }
                    //------------------ Lot No ----------------//
                    $dataLotImei = array(
                        'total_imei'            => $total_imei,
                        'good_id'               => $good_id,
                        'price'                 => $price,
                        'new_price'             => $new_price,
                        'cp_date'               => $cp_date,
                        'detail'                => $detail,
                        'remark'                => $remark,
                        'sub_d_id'              => $check_sub_d_id,
                        'finance_group'         => $finance_group,
                        'check_cost'            => $check_cost,
                        'price_limit'           => $price_limit,
                        'no_use_spc_discount'   => $no_use_spc_discount,
                        'auto_imei'             => $auto_imei,
                        'update_by'             => $userStorage->id,
                        'update_date'           => $date
                    );

                    if($auto_imei=="1")
                    {
                       $dataLotImei['start_import_auto_date']= $start_import_auto_date;
                    }else{
                       $dataLotImei['start_import_auto_date']= null;
                    }
                    
                    $whereLotImei = array();
                    $whereLotImei[] = $QCPAutoCheckImei->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                    $update_lot_number = $QCPAutoCheckImei->update($dataLotImei, $whereLotImei)
                    
                    ;


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

                    $count_imei=0;
                    //print_r($array_imei);die;

                    $imei_check = $QCPAutoCheckImei->checkPriceProtectionImeiAutoCheckListAction($lot_sn,"salse",$check_sub_d_id);
                    //print_r($imei_check);die;

                    $lot_imei_error = array();

                    if($array_imei!=''){
                        foreach ($array_imei as $t=>$imei_sn)
                        {
                            $imei = trim($imei_sn);
                            if($imei!=''){
                                $data_imei = array(
                                    'lot_sn'                => $lot_sn,
                                    'imei_sn'               => $imei,
                                    'status'                => 1,   //1=active 0= not active
                                    'cp_date'               => trim($cp_date),
                                    'create_by'             => $userStorage->id,
                                    'sub_d_id'              => $sub_d_id,
                                    'create_date'           => $date
                                );
                                //print_r($data_imei);die;

                                //$select_chk_imei="(SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.imei_sn='".$imei."' AND bi.lot_sn='".$lot_sn."')";

                                $select_chk_good="select good_id,type from imei where imei_sn='".$imei."';";
                                $result_chk_good = $db->fetchAll($select_chk_good);
                                if($result_chk_good[0]['good_id']==$good_id) //[Imei ไม่ตรงกับสินค้าที่ปรับราคา]
                                {
                                    if($result_chk_good[0]['type']=='1') //[Imei ผิดประเภท]
                                    {
                                        $select_chk_imei="(SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.imei_sn='".$imei."' AND bi.cp_date='".trim($cp_date)." 00:00:00')";
                                        $result_chk_imei = $db->fetchAll($select_chk_imei);

                                        if($result_chk_imei[0]['count_imei']==0) // [Imei duplicate]
                                        {
                                            $imei_check_order = $QCPAutoCheckImei->check_imei_order($imei);
                                            //print_r($imei_check_order);die;
                                            if($imei_check_order[0]['imei_sn'] !='' && $imei_check_order[0]['sales_sn'] !='')
                                            {
                                                $imei_check_timing_sale = $QCPAutoCheckImei->check_imei_timing_sale($imei,$good_id,$cp_date);

                                                //print_r($finance_group);die;
                                                if($finance_group=='Dealer'){
                                                    $finance_group="";
                                                }

                                                if($imei_check_timing_sale[0]['finance_group'] ==$finance_group && $imei_check_timing_sale[0]['distributor_id'] !='' && $imei_check_timing_sale[0]['imei_sn'] !='' && $imei_check_timing_sale[0]['check_out_date_status'] =='1' && $imei_check_timing_sale[0]['check_timing_status'] =='1' && $imei_check_timing_sale[0]['check_activated_status'] =='1' && $check_sub_d_id =="")
                                                {
                                                    //print_r($data_imei);die;
                                                    $create_lot_imei=$QCPAutoCheckImeiList->insert($data_imei);

                                                }else if($imei_check_timing_sale[0]['finance_group'] ==$finance_group && $imei_check_timing_sale[0]['distributor_id'] !='' && $imei_check_timing_sale[0]['imei_sn'] !='' && $imei_check_timing_sale[0]['check_out_date_status'] =='1' && $imei_check_timing_sale[0]['check_timing_sub_d_id_status'] =='1' && $imei_check_timing_sale[0]['check_activated_status'] =='1' && $check_sub_d_id !="")
                                                {   // บุญชัย
                                                    //print_r($data_imei);die;
                                                    $create_lot_imei=$QCPAutoCheckImeiList->insert($data_imei);    
                                                }else{

                                                    if($imei_check_timing_sale[0]['finance_group'] !=$finance_group)
                                                    {
                                                        $remark_finance_group=" [Imei ผิดประเภทร้านที่กำหนดใน LOT NO ".$imei_check_timing_sale[0]['finance_group']."]";
                                                    }else{
                                                        $remark_finance_group="";
                                                    }

                                                    if($check_sub_d_id !=""){
                                                        if($imei_check_timing_sale[0]['check_timing_sub_d_id_status'] =='0'){
                                                            $remark_timing=" [ยังไม่มีการขายหลังวันปรับราคา ".$imei_check_timing_sale[0]['timing_date']."]";
                                                        }else{
                                                            $remark_timing="";
                                                        }
                                                    }else{
                                                        if($imei_check_timing_sale[0]['check_timing_status'] =='0'){
                                                            $remark_timing=" [ขายออกก่อนวันปรับราคา ".$imei_check_timing_sale[0]['timing_date']."]";
                                                        }else{
                                                            $remark_timing="";
                                                        }
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
                                                        'imei_sn'                   => $imei,
                                                        'remark'                    => $remark_timing.$remark_activated.$remark_out_date.$remark_finance_group,
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
                                                }

                                            }else if($imei_check_order[0]['imei_sn'] !='' && $imei_check_order[0]['sales_sn'] =='')// [no_order]
                                            {
                                                $lot_imei_error[] = array(
                                                    'lot_sn'                => $lot_sn,
                                                    'imei_sn'               => $imei,
                                                    'remark'                => "[no_order]",
                                                    'cp_date'               => $cp_date
                                                ); 
                                            }else{
                                                $lot_imei_error[] = array(
                                                    'lot_sn'                => $lot_sn,
                                                    'imei_sn'               => $imei,
                                                    'remark'                => "imei_error",
                                                    'cp_date'               => $cp_date
                                                );  
                                            }

                                            if($create_lot_imei==true)
                                            {
                                                $count_imei+=1;
                                            }
                                        }else{
                                            $lot_imei_error[] = array(
                                                    'lot_sn'                => $lot_sn,
                                                    'imei_sn'               => $imei,
                                                    'remark'                => "[Imei duplicate]",
                                                    'cp_date'               => $cp_date
                                            ); 
                                        }
                                    }else{
                                        $lot_imei_error[] = array(
                                                'lot_sn'                => $lot_sn,
                                                'imei_sn'               => $imei,
                                                'remark'                => "[Imei ผิดประเภท]",
                                                'cp_date'               => $cp_date
                                        ); 
                                    }
                                }else{
                                    $lot_imei_error[] = array(
                                            'lot_sn'                => $lot_sn,
                                            'imei_sn'               => $imei,
                                            'remark'                => "[Imei ไม่ตรงกับสินค้าที่ปรับราคา]",
                                            'cp_date'               => $cp_date
                                    ); 
                                }
                            }
                        }

                        //print_r($lot_imei_duplicate);die;
                    }
                    //print_r($lot_imei_error);die;
                    for ($j = 0; $j < count($lot_imei_error); $j++)
                    {
                        $select_chk_imei_log="SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list_log bi WHERE bi.imei_sn='".$imei."' AND bi.lot_sn='".$lot_sn."'";

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

                        echo '<script>parent.location.href="/sales/cp-auto-check-imei?act=edit&lot_sn='.$lot_sn.'&lot_number='.$lot_number.'&cp_price='.$price.'"</script>';
                        exit;
                    //}else if($update_ImeiList==true && $update_BvgImei==true && $update_CreditNote==true && $update_lot_number==true){    
                    }else if($update_lot_number==true){
                        //echo "OK1";die;
                        $flashMessenger->setNamespace('success')->addMessage('Done!');
                        $db->commit();

                        echo '<script>parent.location.href="/sales/cp-auto-check-imei?act=edit&lot_sn='.$lot_sn.'&lot_number='.$lot_number.'&cp_price='.$price.'"</script>';
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



