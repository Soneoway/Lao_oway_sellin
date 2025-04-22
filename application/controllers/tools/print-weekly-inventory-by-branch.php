<?php

set_time_limit(0);
ini_set('memory_limit', -1);
$this->_helper->layout->disableLayout();
$start_date = $this->getRequest()->getParam('start_date');
$end_date = $this->getRequest()->getParam('end_date');
$warehouse_name = $this->getRequest()->getParam('warehouse_name');
$good_code = $this->getRequest()->getParam('good_code');

/*$QimeiLot  	= new Application_Model_ImeiLot();
$imeilot		= $QimeiLot->ImeiLotDetail($lot_sn);
//print_r($imeilot);
$this->view->imeilot = $imeilot;
$this->view->url    = HOST.'tool/imei-lot/'.( $params ? '?'.http_build_query($params).'&' : '?' );
*/

$QServiceWeeklyStockShopList = new Application_Model_ServiceWeeklyStockShopList();

$params = array_filter(array(
            'warehouse_name'         => $warehouse_name,
            'good_code'              => $good_code,
            'start_date'             => $start_date,
            'end_date'               => $end_date
            ));

$get_resule = $QServiceWeeklyStockShopList->getWeeklyInventoryByBranch($total, $params,"0,1000"); 
//print_r($get_resule);//die;
$limit_item = number_format($total/3,0);
$start01 = 0;
$start02 = $start01+$limit_item;
$start03 = $start02+$limit_item;

//echo $limit_item;
//echo $start01;
//echo $start02;
//echo $start03;
//die;

$get_resule1 = $QServiceWeeklyStockShopList->getWeeklyInventoryByBranch($total, $params,$start01.",".$limit_item); 
$get_resule2 = $QServiceWeeklyStockShopList->getWeeklyInventoryByBranch($total, $params,$start02.",".$limit_item); 
$get_resule3 = $QServiceWeeklyStockShopList->getWeeklyInventoryByBranch($total, $params,$start03.",".$limit_item); 



$this->view->start_date = $start_date;
$this->view->end_date = $end_date;
$this->view->get_resule1 = $get_resule1;
$this->view->get_resule2 = $get_resule2;
$this->view->get_resule3 = $get_resule3;
$this->view->total  = $total;

?>