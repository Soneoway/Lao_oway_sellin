<?php
$flashMessenger = $this->_helper->flashMessenger;

set_time_limit(0);
ini_set('memory_limit', '200M');
$sort          = $this->getRequest()->getParam('sort', '');
$desc          = $this->getRequest()->getParam('desc', 1);

$warehouse_id  = $this->getRequest()->getParam('warehouse_id');
$cat_id        = $this->getRequest()->getParam('cat_id');
$good_id       = $this->getRequest()->getParam('good_id');
$good_color_id = $this->getRequest()->getParam('good_color_id');
$search        = $this->getRequest()->getParam('search', 0);
$btn_sync      = $this->getRequest()->getParam('btn_sync');

$cat_id = PHONE_CAT_ID;

$finan         = $this->getRequest()->getParam('flag');
$order_type    = $this->getRequest()->getParam('order_type');


$page  = $this->getRequest()->getParam('page', 1);
$limit = 40;
$total = 0;

$params = array(
    'warehouse_id'  => $warehouse_id,
    'cat_id'        => $cat_id,
    'good_id'       => $good_id,
    'good_color_id' => $good_color_id,
    'sort'          => $sort,
    'desc'          => $desc,
    'search'        => $search,
    'flag'          => $flag,
    'order_type'    => $order_type,
    
);

//print_r($params);
$QGood          = new Application_Model_Good();
$goods_cached   = $QGood->get_cache();

$QWarehouse     = new Application_Model_Warehouse();
$warehouses_cached = $QWarehouse->getWarehouses('');
$warehouse_arr = array();

foreach ($warehouses_cached as $k => $warehouse_data){
    $warehouses[$warehouse_data['id']] = $warehouse_data['name']; 
}


$QGoodColor     = new Application_Model_GoodColor();
if ($cat_id){
    $where = $QGood->getAdapter()->quoteInto('cat_id = ?', $cat_id);
    $goods_acc = $QGood->fetchAll($where, 'name');
    $this->view->goods_acc = $goods_acc;
}
$goodColors     = $QGoodColor->get_cache();

$QGoodCategory  = new Application_Model_GoodCategory();
$goodCategories = $QGoodCategory->get_cache();

$QBrand         = new Application_Model_Brand();
$brands         = $QBrand->get_cache();

$QImei         = new Application_Model_Imei();
$QImeiDigital  = new Application_Model_DigitalSn();
// print_r($params); die;
// $params = [];
$QMarket 	     = new Application_Model_Market();
$QSyncstorage  = new Application_Model_SyncStorage();

$this->view->params 		    = $params;
$this->view->warehouses     = $warehouses;
$this->view->goods_cached   = $goods_cached;
$this->view->goodColors     = $goodColors;
$this->view->goodCategories = $goodCategories;
$this->view->brands         = $brands;


if(!$search && !$btn_sync) return;

 // $data = $QGood->fetchPaginationStorage($page, $limit, $total, $params);
	// // $data = [];
 
 // 		$total_storage = 0;
 // 		$total_available = 0;
	// 	foreach ($data as $good){

 //              $bad = $demo = $count = $available_normal = $available_demo = $available_apk = $total_normal = $total_demo = $total_changing = $total_apk = 0;

 //              if ($good['cat_id'] == PHONE_CAT_ID){
 //                  $bad = ($good['imei_bad_count'] ? $good['imei_bad_count'] : 0);
 //                  $bad_normal = ($good['imei_normal_bad_count'] ? $good['imei_normal_bad_count'] : 0);
 //                  $bad_demo = ($good['imei_demo_bad_count'] ? $good['imei_demo_bad_count'] : 0);
 //                  $bad_apk = ($good['imei_apk_bad_count'] ? $good['imei_apk_bad_count'] : 0);
 //                  $count = ($good['imei_count'] ? $good['imei_count'] : 0);
 //                  $demo = ($good['imei_demo_count'] ? $good['imei_demo_count'] : 0);
 //                  $apk = ($good['imei_apk_count'] ? $good['imei_apk_count'] : 0);
 //              }
 //              $current_order =  ($good['current_order'] ? $good['current_order'] : 0);
 //              $current_change_order =  ($good['current_change_order'] ? $good['current_change_order'] : 0);
 //              $total_normal = $count;
 //              $available_normal = $total_normal - $current_order - $current_change_order;

 //              $current_order_demo =  ($good['current_order_demo'] ? $good['current_order_demo'] : 0); 
 //              $current_change_order_demo =  ($good['current_change_order_demo'] ? $good['current_change_order_demo'] : 0); 
 //              $total_demo = $demo;
 //              $available_demo = $total_demo - $current_order_demo - $current_change_order_demo;
              
 //              $current_order_apk =  ($good['current_order_apk'] ? $good['current_order_apk'] : 0); 
 //              $current_change_order_apk =  ($good['current_change_order_apk'] ? $good['current_change_order_apk'] : 0); 
 //              $total_apk = $apk;
 //              $available_apk = $total_apk - $current_order_apk - $current_change_order_apk;

 //              $total_current_order = $current_order + $current_order_demo + $current_order_apk;
 //              $total_current_change_order = $current_change_order + $current_change_order_demo + $current_change_order_apk;

 //              $total_available = intval($total_available) + intval($available_normal) + intval($available_demo) + intval($available_apk);

 //              $total_storage = $total_storage + $total_normal + $total_demo + $total_apk;
              
 //   				    //type filter
 //              if($order_type == 1){
 //                $total_available = intval($available_normal);
 //                $total_storage = $total_normal;
 //              }
 //              if($order_type == 2){
 //                $total_available = intval($available_demo);
 //                $total_storage   = $total_demo;
 //              }
 //              if($order_type == 3){
 //                $total_available = intval($available_normal);
 //                $total_storage = $total_normal;
 //              }
 //              if($order_type == 4){
 //                $total_available = intval($available_normal);
 //                $total_storage = $total_normal;
 //              }
 //              if($order_type == 5){
 //                $total_available = intval($available_apk);
 //                $total_storage   = $total_apk;
 //              }

 // }
//--------sync db-------------------//

if($btn_sync){
  $params['btn_sync'] = $btn_sync;
  //---- ดัก error select----//
  $messages = $flashMessenger->setNamespace('error')->getMessages();
   if(!isset($params['warehouse_id']) || $params['warehouse_id'] == ""){
     array_push($messages,'Can not sync:: Please select warehouse');
      $this->view->messages  = $messages;
      return;
  }
    if(!isset($params['good_id']) || $params['good_id'] == ""){
     array_push($messages,'Can not sync:: Please select product');
     $this->view->messages  = $messages;
      return;
  }
   if(!isset($params['good_color_id']) || $params['good_color_id'] == ""){
     array_push($messages,'Can not sync:: Please select color');
     $this->view->messages  = $messages;
      return;
  }
 //---- end ดัก error select----//
  
  $data = $QGood->fetchPaginationStorage($page, $limit, $total, $params);
  // // $data = [];
  $total_storage = 0;
  $total_available = 0;
  
  foreach ($data as $good){

     $bad = $demo = $count = $available_normal = $available_demo = $available_apk = $total_normal = $total_demo = $total_changing = $total_apk = 0;

              if ($good['cat_id'] == PHONE_CAT_ID){
                  $bad = ($good['imei_bad_count'] ? $good['imei_bad_count'] : 0);
                  $bad_normal = ($good['imei_normal_bad_count'] ? $good['imei_normal_bad_count'] : 0);
                  $bad_demo = ($good['imei_demo_bad_count'] ? $good['imei_demo_bad_count'] : 0);
                  $bad_apk = ($good['imei_apk_bad_count'] ? $good['imei_apk_bad_count'] : 0);
                  $count = ($good['imei_count'] ? $good['imei_count'] : 0);
                  $demo = ($good['imei_demo_count'] ? $good['imei_demo_count'] : 0);
                  $apk = ($good['imei_apk_count'] ? $good['imei_apk_count'] : 0);
              }
              $current_order =  ($good['current_order'] ? $good['current_order'] : 0);
              $current_change_order =  ($good['current_change_order'] ? $good['current_change_order'] : 0);
              $total_normal = $count;
              $available_normal = $total_normal - $current_order - $current_change_order;

              $current_order_demo =  ($good['current_order_demo'] ? $good['current_order_demo'] : 0); 
              $current_change_order_demo =  ($good['current_change_order_demo'] ? $good['current_change_order_demo'] : 0); 
              $total_demo = $demo;
              $available_demo = $total_demo - $current_order_demo - $current_change_order_demo;
              
              $current_order_apk =  ($good['current_order_apk'] ? $good['current_order_apk'] : 0); 
              $current_change_order_apk =  ($good['current_change_order_apk'] ? $good['current_change_order_apk'] : 0); 
              $total_apk = $apk;
              $available_apk = $total_apk - $current_order_apk - $current_change_order_apk;

              $total_current_order = $current_order + $current_order_demo + $current_order_apk;
              $total_current_change_order = $current_change_order + $current_change_order_demo + $current_change_order_apk;

              $total_available = intval($total_available) + intval($available_normal) + intval($available_demo) + intval($available_apk);

              $total_storage = $total_storage + $total_normal + $total_demo + $total_apk;
              
               //type filter
              if($order_type == 1){
                $total_available = intval($available_normal);
                $total_storage = $total_normal;
              }
              if($order_type == 2){
                $total_available = intval($available_demo);
                $total_storage   = $total_demo;
              }
              if($order_type == 3){
                $total_available = intval($available_normal);
                $total_storage = $total_normal;
              }
              if($order_type == 4){
                $total_available = intval($available_normal);
                $total_storage = $total_normal;
              }
              if($order_type == 5){
                $total_available = intval($available_apk);
                $total_storage   = $total_apk;
              }
   } 

          $QSyncstorage  = new Application_Model_SyncStorage();
        //--------------update status---------//    
           $where = array(); 
           $where[] = $QSyncstorage->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
           $where[] = $QSyncstorage->getAdapter()->quoteInto('cat_id = ?', $cat_id);
           $where[] = $QSyncstorage->getAdapter()->quoteInto('good_id = ?', $good_id);
           $where[] = $QSyncstorage->getAdapter()->quoteInto('good_color_id = ?', $good_color_id);

             $update = array(
                        'status' => 0
             );
            
             $QSyncstorage->update($update,$where);
          
            
    //------------ดัก เออเร่อ--------------------//
  if(!isset($available_normal) && $available_normal =0){
     echo $available_normal;
  }
  if(!isset($available_demo) && $available_demo =0){
     echo $available_demo;
  }
   if(!isset($available_apk) && $available_apk =0){
     echo $available_apk;
  }
   if(!isset($total_normal) && $total_normal =0){
     echo $total_normal;
  }
   if(!isset($total_demo) && $total_demo =0){
     echo $total_demo;
  }
   if(!isset($total_apk) && $total_apk =0){
     echo $total_apk;
  }
//------------end ดัก เออเร่อ--------------------//
  //--------------insert sync---------// 
             $QSyncstorage->insert(array(
                        'warehouse_id'  => $warehouse_id,
                        'cat_id'        => $cat_id,
                        'good_id'       => $good_id,
                        'good_color_id' => $good_color_id,
                        'create_date'   => date('Y-m-d H:i:s'),
                        'normal'        => $available_normal,
                        'demo'          => $available_demo,
                        'apk'           => $available_apk,
                        'total_normal'  => $total_normal,
                        'total_demo'    => $total_demo,
                        'total_apk'     => $total_apk,
                        'all_total'     => $total_storage,
                        'all_total_available' => $total_available,
                        'status'        =>1
                    ));



} 
//--------end sync db-------------------/

$sync = $QSyncstorage->getsync_storage($params);
switch ($order_type) {
  case '1':
    $total_available = (isset($sync['normal']) ? $sync['normal'] :0);
    $total_storage   = (isset($sync['total_normal']) ? $sync['total_normal'] :0);
    $create_date     = $sync['create_date'];
    break;

  case '2':
    $total_available = (isset($sync['demo']) ? $sync['demo'] :0);
    $total_storage   = (isset($sync['total_demo']) ? $sync['total_demo'] :0);
    $create_date     = $sync['create_date'];
    break;

  case '3':
    $total_available = (isset($sync['normal']) ? $sync['normal'] :0);
    $total_storage   = (isset($sync['total_normal']) ? $sync['total_normal'] :0);
    $create_date     = $sync['create_date'];
    break;

  case '4':
    $total_available = (isset($sync['normal']) ? $sync['normal'] :0);
    $total_storage   = (isset($sync['total_normal']) ? $sync['total_normal'] :0);
    $create_date     = $sync['create_date'];
    break;

  case '5':
    $total_available = (isset($sync['apk']) ? $sync['apk'] :0);
    $total_storage   = (isset($sync['total_apk']) ? $sync['total_apk'] :0);
    $create_date     = $sync['create_date'];
    break;
  default:
    $total_available = (isset($sync['all_total_available']) ? 
                        $sync['all_total_available'] :0); 
    $total_storage   = (isset($sync['all_total']) ? $sync['all_total'] :0);
    $create_date     = $sync['create_date'];
    break;
  
}

 $total_bill        = $QMarket->marketBilled($params,'');
 // print_r('test'); die;
 $total_bill_unsucc = $QMarket->marketBilled($params,'bill_unsucc');
 $total_financ      = $QMarket->marketBilled($params,'financed');
 $total_scaned		  = $QMarket->marketBilled($params,'scaned');

 $total_count_good  = $QMarket->marketBilled($params,'count_good');

 $Market  = new Application_Model_Market();
 $bill_color = $Market->marketBilled($params,'color');
 $this->view->bill_color = $bill_color;
// print_r($bill_color); die;
$this->view->total_count_good  = $total_count_good;
$this->view->total_bill_unsucc = $total_bill_unsucc;
$this->view->total_scaned 	  = $total_scaned;
$this->view->total_financ 	  = $total_financ;
$this->view->total_bill  	    = $total_bill;
$this->view->total_storage    = $total_storage;
$this->view->total_available  = $total_available;
$this->view->create_date      = $create_date;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'tool/dashboard/'.( $params ? '?'.http_build_query($params).'&' : '?' );
$this->view->offset = $limit*($page-1);

