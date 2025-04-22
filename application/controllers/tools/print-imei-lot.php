<?php

set_time_limit(0);
ini_set('memory_limit', -1);
$this->_helper->layout->disableLayout();
$lot_sn = $this->getRequest()->getParam('id');


$QimeiLot  	= new Application_Model_ImeiLot();
$imeilot		= $QimeiLot->ImeiLotDetail($lot_sn);
//print_r($imeilot);
$this->view->imeilot = $imeilot;
$this->view->url    = HOST.'tool/imei-lot/'.( $params ? '?'.http_build_query($params).'&' : '?' );

