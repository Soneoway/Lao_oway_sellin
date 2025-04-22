<?php 
$name        = $this->getRequest()->getParam('name');
$no_delivery = $this->getRequest()->getParam('no_delivery', 0);
$page        = $this->getRequest()->getParam('page', 1);
$desc        = $this->getRequest()->getParam('desc', 0);
$sort        = $this->getRequest()->getParam('sort', '');
$limit       = LIMITATION;
$total       = 0;

$params = array(
    'name'        => $name,
    'no_delivery' => $no_delivery,
);

$QDistrictFee       = new Application_Model_DistrictFee();
$this->view->fees   = $QDistrictFee->fetchPagination($page, $limit, $total, $params);
$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->offset = $limit*($page-1);
$this->view->url    = HOST.'warehouse/delivery-fee/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$QRegion = new Application_Model_RegionalMarket();
$this->view->provinces = $QRegion->get_cache();

$flashMessenger = $this->_helper->flashMessenger;
$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();