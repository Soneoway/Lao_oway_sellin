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

$QServiceStockShopList = new Application_Model_ServiceStockShopList();

$params = array_filter(array(
            'warehouse_name'         => $warehouse_name,
            'good_code'              => $good_code,
            'start_date'             => $start_date,
            'end_date'               => $end_date
            ));

$get_resule = $QServiceStockShopList->getInventoryPurchaseConsumption($total, $params,"0,1000"); 
//echo $total;die;

$this->view->start_date = $start_date;
$this->view->end_date = $end_date;
$this->view->get_resule1 = $get_resule;
$this->view->total  = $total;

?>