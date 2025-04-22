<?php

set_time_limit(0);
ini_set('memory_limit', -1);
$this->_helper->layout->disableLayout();
$sn = $this->getRequest()->getParam('sn');

$QMarket = new Application_Model_Market();
$QDiscount = new Application_Model_Discount();

$where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
$market_data = $QMarket->fetchRow($where);

$params = array(
    'sn'        => $sn,
    'type'      => $market_data->type
);

// I will up to server 30.09.2019 

$page = 1;
$limit = 15;
$total = 0;



$distributor_details = $QMarket->getInvoiceDistributorDetails($params);
$getInvoiceDetails = $QMarket->getInvoiceDetails($params);
$getarea = $QMarket->getareastyle($params);

$this->view->distributor_details = $distributor_details;
$this->view->getInvoiceDetails = $getInvoiceDetails;
$this->view->getarea = $getarea;


$total = 30;
$show_item=10;
$totalpage= $total/$show_item;

