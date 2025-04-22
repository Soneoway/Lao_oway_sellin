<?php

class CreditnoteApiController extends My_Controller_Action
{
    public function init()
    {
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);

      date_default_timezone_set("Asia/Bangkok");
    }

    //http://local.wms-new/creditnote-api/cron-cn
    //http://wms-training.oppo.in.th/creditnote-api/cron-cn
    public function cronCnAction()
    {

      set_time_limit(0);
      ini_set('memory_limit', -1);

      //echo "OK";die;
      $userStorage = Zend_Auth::getInstance()->getStorage()->read();
      $QMarket = new Application_Model_Market();
      $QReturnBoxNumber = new Application_Model_ReturnBoxNumber();
      $QReturnBoxNumberImei = new Application_Model_ReturnBoxNumberImei();
      $QImeiReturn = new Application_Model_ImeiReturn();
      $QDistributor = new Application_Model_Distributor();
      $QImei = new Application_Model_Imei();
      $QLog = new Application_Model_Log();
      $imei_return_check=null;
      $end_date = date('Y-m-d');
      /*$params = array_filter(array(
            'action_frm' => 'confirm_cn',
            'start_date' => '2018-01-31 00:00:00',
            'end_date' => '2018-03-21 23:59:59'
            ));*/
      $params = array_filter(array(
            'action_frm' => 'confirm_cn',
            'start_date' => '2018-01-31 00:00:00',
            'end_date' => $end_date.' 23:59:59'
            ));

      //print_r($params);die;
      $imei_return_check = $QReturnBoxNumberImei->getReturnBoxNumberImeiCheckAction($params);
      if(!$imei_return_check){
        echo "No Data";
        exit;
      }
      //print_r($imei_return_check);die;

      $data=null;

      // Check By Imei Select
      foreach ($imei_return_check as $j=>$imei_val)
      {
          if($imei_val['active_cn']=='')
          {
            $active_cn = 0;
          }else{
            $active_cn = $imei_val['active_cn'];
          }
            $data[] = array(
                'box_sn'  => $imei_val['box_sn'],
                'return_type'  => $imei_val['return_type'],
                'imei_sn'  => $imei_val['imei_oppo'],
                'distributor_id'  => $imei_val['distributor_id'],
                'create_cn'  => $imei_val['create_cn'],
                'active_cn'  => $active_cn,
                'warehouse_id'  => $imei_val['warehouse_id'],
                'shape_status'  => $imei_val['shape_status'],
                'damage_detail'  => $imei_val['damage_detail'],
                'remark'  => $imei_val['remark'],
                'rtn_number'  => $imei_val['rtn_number'],
                'cn_to_d_id'  => $imei_val['cn_to_d_id'],
                'finance_confirm_by'  => $imei_val['finance_confirm_by']
            );

      }

      //print_r($data);die; 
      // Check Product Group from Imei Select

      $cn_data=null;
      foreach ($data as $im=>$imei_val)
      {
         // print_r($imei_val);
              $box_sn=$imei_val['box_sn'];
              $chk_return_type=$imei_val['return_type'];
              usleep(1000);
              $rtn_number="";
              if($imei_val['rtn_number']!=""){
                $rtn_number=$imei_val['rtn_number'];
              }
              
              if($rtn_number !=''){ //RTN

              $return_type_rtn=6;  
              $return_type=$return_type_rtn.'-'.$imei_val['create_cn'].'-'.$imei_val['active_cn'].'-'.$rtn_number.'-'.$imei_val['distributor_id'].'-';
                //$return_type=$imei_val['return_type'].'-'.$imei_val['create_cn'].'-'.$imei_val['active_cn'].'-'.$rtn_number.'-'.$imei_val['distributor_id'].'-'.$imei_val['warehouse_id'];

                  $sn = date ( 'YmdHis' ) . substr ( microtime (), 2, 4 );
                  $imei_list=$imei_val['imei_sn'].",";
                  $distributor_id=$imei_val['distributor_id'];
                  $status_create_cn=$imei_val['create_cn'];
                  $status_active_cn=$imei_val['active_cn'];
                  $warehouse_id=$imei_val['warehouse_id'];
                  $shape_status=$imei_val['shape_status'];
                  $rtn_number=$imei_val['rtn_number'];
                  $finance_confirm_by=$imei_val['finance_confirm_by'];
                  $return_type_id=$return_type_rtn;
                  $cn_data[$return_type] = array(
                      'box_sn'  => $box_sn,
                      'sn'  => $sn,
                      'return_type'  => $return_type_id,
                      'distributor_id'  => $distributor_id,
                      'create_cn'  => $status_create_cn,
                      'active_cn'  => $status_active_cn,
                      'warehouse_id'  => $warehouse_id,
                      'shape_status'  => $shape_status,
                      'rtn_number'  => $rtn_number,
                      'finance_confirm_by'  => $finance_confirm_by,
                      'imei_sn'  => $cn_data[$return_type]['imei_sn'].=$imei_list
                  );
              }else{ //Other
                  $return_type=$imei_val['return_type'].'-'.$imei_val['create_cn'].'-'.$imei_val['active_cn'].'-'.$rtn_number.'-'.$imei_val['distributor_id'].'-'.$imei_val['warehouse_id'];
                  $sn = date ( 'YmdHis' ) . substr ( microtime (), 2, 4 );
                  $imei_list=$imei_val['imei_sn'].",";
                  $distributor_id=$imei_val['distributor_id'];
                  $status_create_cn=$imei_val['create_cn'];
                  $status_active_cn=$imei_val['active_cn'];
                  $warehouse_id=$imei_val['warehouse_id'];
                  $shape_status=$imei_val['shape_status'];
                  $rtn_number=$imei_val['rtn_number'];
                  $finance_confirm_by=$imei_val['finance_confirm_by'];
                  $return_type_id=$imei_val['return_type'];
                  $cn_data[$return_type] = array(
                      'box_sn'  => $box_sn,
                      'sn'  => $sn,
                      'return_type'  => $return_type_id,
                      'distributor_id'  => $distributor_id,
                      'create_cn'  => $status_create_cn,
                      'active_cn'  => $status_active_cn,
                      'warehouse_id'  => $warehouse_id,
                      'shape_status'  => $shape_status,
                      'rtn_number'  => $rtn_number,
                      'finance_confirm_by'  => $finance_confirm_by,
                      'imei_sn'  => $cn_data[$return_type]['imei_sn'].=$imei_list
                  );
              }
      }

      //print_r($cn_data);die;

      try{

          $db = Zend_Registry::get('db');

          $db->beginTransaction();

          $count_create_cn_before=0;$count_create_cn_after=0;
          foreach ($cn_data as $im_cn=>$cn_val)
          {
              //$sn = date ( 'YmdHis' ) . substr ( microtime (), 2, 4 );

              $sn=$cn_val['sn'];
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

              $finance_confirm_by=$cn_val['finance_confirm_by'];
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

                      if($rtn_number !=''){
                        $market_warehouse_id=26;   //WMCN - คลังรอเคลม
                      }else{
                        $market_warehouse_id = $warehouse_id;
                      }

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
                          'backs_d_id'    => $market_warehouse_id,
                          'warehouse_id'  => $market_warehouse_id,
                          'invoice_number'    => $invoice_number,
                          'create_cn'     => $status_create_cn,
                          'active_cn'     => $status_active_cn,
                          'return_type'     => $return_type,
                          'pay_text'      => 'Approve',
                          'pay_time'      => $date,
                          'pay_user'      => $finance_confirm_by,
                          'shipping_yes_time'     => $date,
                          'shipping_yes_id'     => $finance_confirm_by,
                          'finance_confirm_date'     => $date,
                          'finance_confirm_id'     => $finance_confirm_by,

                          'add_time'      => $date,
                          'user_id'       => $finance_confirm_by,
                          'sn'            => $sn,
                          'sn_ref'        => $sn_ref,
                          'box_sn'        => $box_sn,
                          'rtn_number'        => $rtn_number
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
                 $array_imei_damage_detail = array();
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
                                  $damage_detail = trim($key_imei_sn['damage_detail']);
                                  $damage_detail_val = "";
                                  

                                  if (in_array($damage_detail, $array_imei_damage_detail)) {
                                      $damage_detail_val="";
                                  }else{
                                      $damage_detail_val = $damage_detail.' ,'; 
                                  }

                                  

                                  $remark_detail = $key_imei_sn['remark'];
                                  $rtn_number = $key_imei_sn['rtn_number'];
                                  $array_imei_damage_text[$key].= $damage_detail_val; 
                                  $array_imei_remark_text[$key].= $remark_detail.' ,'; 
                                  $array_imei_rtn[$key]= $rtn_number.' ,'; 

                                  $array_imei_damage_detail = array(
                                    'damage_detail' => $damage_detail
                                  );
                              }
                              
                              //print_r($array_imei_damage_detail);die;
                              
              
                              $dataImeiReturn = array(
                                  'imei_sn' => trim($imei),
                                  'box_sn' => $box_sn,
                                  'warehouse_id' => $key_imei_sn['warehouse_id'],
                                  'return_sn' => $sn,
                                  'sales_order_sn' => $sales_sn,
                                  'created_at' => $date,
                                  'created_by' => $finance_confirm_by,
                                  'confirmed_at' => $date,
                                  'confirmed_by' => $finance_confirm_by,
                                 // 'damage_detail' => $key_imei_sn['damage_detail'],
                                  'remark' => $key_imei_sn['remark'],
                                  'rtn_number' => $key_imei_sn['rtn_number'],
                                  'return_type' => $key_imei_sn['return_type']
                                  
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
                                  'warehouse_id'  => $key_imei_sn['warehouse_id'],
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

                              //$dataBoxImeiReturn['creditnote_date']= $date;
                              //$dataBoxImeiReturn['creditnote_sn']= $creditnote_sn;
                              $dataBoxImeiReturn['status']= 1;
                              $dataBoxImeiReturn['return_sn']= $sn;

    
                              $whereBoxImei = array();
                              $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                              $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('box_sn = ?', $box_sn);
                              $QReturnBoxNumberImei->update($dataBoxImeiReturn, $whereBoxImei);

                              $whereCheckBoxNumberImei = array();
                              $whereCheckBoxNumberImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('box_sn = ?', $box_sn);
                              $whereCheckBoxNumberImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('active = ?', 1);
                              $whereCheckBoxNumberImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('status = ?', 1);
                              $whereCheckBoxNumberImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('creditnote_date is not null', null);

                              $imei_check = $QReturnBoxNumberImei->fetchAll($whereCheckBoxNumberImei);
                              //echo count($imei_check);die;
                              //print_r($imei_check);die;

                              $update_ReturnBoxNumber['box_status']=2;
                              $update_ReturnBoxNumber['update_date']=$date;
                              $update_ReturnBoxNumber['update_by']=$finance_confirm_by;
                              
                              $whereBoxNumber = array();
                              $whereBoxNumber[] = $QReturnBoxNumber->getAdapter()->quoteInto('box_sn = ?', $box_sn);
                              $whereBoxNumber[] = $QReturnBoxNumber->getAdapter()->quoteInto('total_imei = ?', count($imei_check));
                              $QReturnBoxNumber->update($update_ReturnBoxNumber, $whereBoxNumber);

                          }
                      }
                  }

                  //print_r($array_imei_damage_text);die;
                  foreach ($array_phone as $k=>$item)
                  {
                      //print_r($item);die;
                      $invoice_number = $item['invoice_number'];   
                      $good_id = $item['good_id'];  
                      $good_color = $item['good_color']; 
                      $key = $invoice_number.'-'.$good_id.'-'.$good_color;
                      $damage_text="";$remark_text="";$rtn="";
                      if($array_imei_damage_text[$key]!=''){
                        $damage_text=rtrim($array_imei_damage_text[$key], ',');
                      }
                      if($array_imei_remark_text[$key]!=''){
                        $remark_text=rtrim($array_imei_remark_text[$key], ',');
                      }
                      if($array_imei_rtn[$key]!=''){
                        $rtn=rtrim($array_imei_rtn[$key], ',');
                      }
                      //print_r($damage_text);die;
                      $update_Market = array();
                      /*if (strlen($damage_text) >= 250)
                      {
                        print_r($damage_text);die;
                      }*/
                      $update_Market['text']=$damage_text;
                      //$update_Market['remark']=$remark_text;
                      $update_Market['rtn_number']=$rtn;

                      $whereMarket = array();
                      $whereMarket[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                      $whereMarket[] = $QMarket->getAdapter()->quoteInto('box_sn = ?', $box_sn);
                      $whereMarket[] = $QMarket->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);
                      $whereMarket[] = $QMarket->getAdapter()->quoteInto('good_id = ?', $good_id);
                      $whereMarket[] = $QMarket->getAdapter()->quoteInto('good_color = ?', $good_color);
                      //print_r($whereMarket);
                      //print_r($update_Market);die;
                      $QMarket->update($update_Market, $whereMarket);

                  } 
                  //print_r($array_imei_damage_text);die;
                 // die;
                  if($status_create_cn == 1)
                  {
                     $count_create_cn_before+=1;

                     $creditnote_sn = $QImeiReturn->get_credit_note_sn($db,$distributor_id,$finance_confirm_by,$sn,$status_active_cn,$return_type);
                     if($creditnote_sn==''){
                          for($i=0;$i<3;$i++){ 
                              if($creditnote_sn==''){
                                  $creditnote_sn = $QImeiReturn->get_credit_note_sn($db,$distributor_id,$finance_confirm_by,$sn,$status_active_cn,$return_type);
                              }
                          }
                     }

                     if($creditnote_sn !='')
                     {
                        $count_create_cn_after+=1;

                        $dataBoxImeiReturn = array();
                        $dataBoxImeiReturn['creditnote_date']=$date;
                        $dataBoxImeiReturn['creditnote_sn']=$creditnote_sn;

                        $whereBoxImei = array();
                        $whereBoxImei[] = $QReturnBoxNumberImei->getAdapter()->quoteInto('return_sn = ?', $sn);
                        $QReturnBoxNumberImei->update($dataBoxImeiReturn, $whereBoxImei);
                     }
                  }
                  //die;
          }
          //die;
          if($count_create_cn_after == $count_create_cn_before){
              //$flashMessenger->setNamespace('success')->addMessage('Done!');
              $db->commit(); 
              echo "Done";
          }else{
              $db->rollback();
              echo "Cannot Create CN For Return, Please try again!";
              //$flashMessenger->setNamespace('error')->addMessage('Cannot Create CN For Return, Please try again!');
          }

         // die;
      }catch (Exception $e){

          $db->rollback();

          print_r($e.message);
          echo "Cannot Create CN For Return, Please try again!!";
          exit;
      }

    }


    //http://local.wms-new/creditnote-api/cron-cp-auto
    //http://wms-training.oppo.in.th/creditnote-api/cron-cp-auto
    public function cronCpAutoAction()
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        //echo "OK";die;
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
      //--------------- Start ---------------
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
        $params="";
        $total_auto_imei=0;
        try{
                // Save Data Only
                
                $array_imei = $QCPAutoCheckImei->check_imei_cp_auto($params);
                if(!$array_imei){
                  echo "No Data";
                  exit;
                }
                //print_r($array_imei);die;
                $total_auto_imei = count($array_imei);
              
                $db = Zend_Registry::get('db');
                $db->beginTransaction();

                    $date = date('Y-m-d H:i:s');
                    $total_imei=0;

                    //------------------ Lot No ----------------//

                    $count_imei=0;
                    //print_r($array_imei);die;
                    $lot_imei_error = array();

                    foreach ($array_imei as $t=>$imei_sn)
                    {
                        //print_r($imei_sn);die;
                        $lot_sn = $imei_sn['lot_sn'];
                        $good_id = $imei_sn['good_id'];
                        $imei = $imei_sn['imei_sn'];
                        $cp_date = $imei_sn['cp_date'];
                        $finance_group = $imei_sn['finance_group'];
                        //print_r($imei);die;
                        if($imei!=''){
                            $data_imei = array(
                                'lot_sn'                => $lot_sn,
                                'imei_sn'               => $imei,
                                'status'                => 1,   //1=active 0= not active
                                'cp_date'               => trim($cp_date),
                                'create_by'             => 10,
                                'sub_d_id'              => null,
                                'create_date'           => $date
                            );
                            //print_r($data_imei);die;

                            //$select_chk_imei="(SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.imei_sn='".$imei."' AND bi.lot_sn='".$lot_sn."')";

                            $select_chk_good="select good_id,type from imei where imei_sn='".$imei."';";
                            $result_chk_good = $db->fetchAll($select_chk_good);
                            if($result_chk_good[0]['good_id']==$good_id) //[Imei ไม่ตรงกับสินค้าที่ปรับราคา]
                            {
                                //print_r($result_chk_good);die;
                                if($result_chk_good[0]['type']=='1') //[Imei ผิดประเภท]
                                {
                                    $select_chk_imei="(SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.imei_sn='".$imei."' AND bi.cp_date='".trim($cp_date)." 00:00:00')";
                                    $result_chk_imei = $db->fetchAll($select_chk_imei);
                                    //print_r($result_chk_imei);die;
                                    if($result_chk_imei[0]['count_imei']==0) // [Imei duplicate]
                                    {
                                        $imei_check_order = $QCPAutoCheckImei->check_imei_order($imei);
                                        //print_r($imei_check_order);die;
                                        if($imei_check_order[0]['imei_sn'] !='' && $imei_check_order[0]['sales_sn'] !='')
                                        {
                                            $imei_check_timing_sale = $QCPAutoCheckImei->check_imei_timing_sale($imei,$good_id,$cp_date);
                                            
                                            if($finance_group=='Dealer'){
                                                $finance_group="";
                                            }
                                            //print_r($finance_group);
                                            //print_r($imei_check_timing_sale);die;
                                            if($imei_check_timing_sale[0]['finance_group'] ==$finance_group && $imei_check_timing_sale[0]['distributor_id'] !='' && $imei_check_timing_sale[0]['imei_sn'] !='' && $imei_check_timing_sale[0]['check_out_date_status'] =='1' && $imei_check_timing_sale[0]['check_timing_status'] =='1' && $imei_check_timing_sale[0]['check_activated_status'] =='1' && $check_sub_d_id =="")
                                            {
                                                //print_r($data_imei);die;
                                                $create_lot_imei=$QCPAutoCheckImeiList->insert($data_imei);

                                            }else if($imei_check_timing_sale[0]['finance_group'] ==$finance_group && $imei_check_timing_sale[0]['distributor_id'] !='' && $imei_check_timing_sale[0]['imei_sn'] !='' && $imei_check_timing_sale[0]['check_out_date_status'] =='1' && $imei_check_timing_sale[0]['check_timing_sub_d_id_status'] =='1' && $imei_check_timing_sale[0]['check_activated_status'] =='1' && $check_sub_d_id !="")
                                            {   // บุญชัย
                                                //print_r($data_imei);die;
                                                $create_lot_imei=$QCPAutoCheckImeiList->insert($data_imei);    
                                            }else{

                                                //print_r($imei_check_timing_sale);die;
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
                        'total_auto_imei' => $total_auto_imei,
                        'total_imei' => $result_chk_imei_total[0]['count_imei'],
                        'auto_imei_done' => 1,
                        'import_auto_date' => $date
                    );
                    $whereLotImei = array();
                    $whereLotImei = $QCPAutoCheckImei->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                    $update_lot_number = $QCPAutoCheckImei->update($dataLotImei, $whereLotImei);

                    //echo "OK";echo "imei_check>".count($imei_check);echo "count_imei>".$count_imei;die;
                    //if(count($imei_check)<=0 && $count_imei >0)
                    


                    if($count_imei >=0)
                    {
                        $db->commit();
                        echo "New Done!";//die;

                        $view_cp_auto = $QCPAutoCheckImei->view_imei_cp_auto($params);
                        echo "<pre>";
                        print_r($view_cp_auto);
                        exit;
                    //}else if($update_ImeiList==true && $update_BvgImei==true && $update_CreditNote==true && $update_lot_number==true){    
                    }else if($update_lot_number==true){
                        $db->commit();
                        echo "Update Done!";//die;

                        $view_cp_auto = $QCPAutoCheckImei->view_imei_cp_auto($params);
                        echo "<pre>";
                        print_r($view_cp_auto);
                        exit;
                    }else{
                        $db->rollback();

                        $view_cp_auto = $QCPAutoCheckImei->view_imei_cp_auto($params);
                        print_r($view_cp_auto);

                        echo "error";echo "imei_check>".count($imei_check);echo "count_imei>".$count_imei;print_r($lot_imei_error);
                        exit;
                    }

        }catch (Exception $e){
            echo $e.message;
            //$db->rollback();
            echo '<script>
                    parent.palert("Cannot Import Imei Auto CP, Please try again!! ['.$e.message.']");
                  </script>';
            exit;
        }  

      //--------------- End ---------------
    }


}