<?php
$flashMessenger = $this->_helper->flashMessenger;

$distributor_name 					= $this->getRequest()->getParam('distributor_name');
$superior_d 						= $this->getRequest()->getParam('superior_d');
$tag 								= $this->getRequest()->getParam('tag');
$status								= $this->getRequest()->getParam('status');
$distributor_type					= $this->getRequest()->getParam('distributor_type');
$ext_code							= $this->getRequest()->getParam('ext_code');
$distributor_code					= $this->getRequest()->getParam('distributor_code');
$office								= $this->getRequest()->getParam('office');
$export 							= $this->getRequest()->getParam('export');

$page   						= $this->getRequest()->getParam('page', 1);
$limit  = LIMITATION;
$total  = 0;

$QDistributorNew = new Application_Model_DistributorNew();
$QRegionalMarket = new Application_Model_RegionalMarket();
$QClient = new Application_Model_Client();

$params = array(
	'distributor_name'		=> $distributor_name,
	'superior_d'			=> $superior_d,
	'tag'					=> $tag,
	'status'				=> $status,
	'distributor_type'		=> $distributor_type,
	'ext_code'				=> $ext_code,
	'distributor_code'		=> $distributor_code,
	'office'				=> $office
);

$distriubtor = $QDistributorNew->fetchPagination($page, $limit, $total, $params);

if (isset($export) and $export) {
	$data = $QDistributorNew->fetchPagination($page, null, $total, $params);
	$this->_exportDistributortExcel($data);
}



$superiorDistributor = $QDistributorNew->getSuperiorDsitributor();
$regional = $QRegionalMarket->get_cache();
$distributor_cache = $QDistributorNew->get_cache();
$client_cache = $QClient->get_cache();

$this->view->superiorD    			= $superiorDistributor;
$this->view->regional     			= $regional;
$this->view->params 	  			= $params;
$this->view->distriubtor  			= $distriubtor;
$this->view->distributor_cache		= $distributor_cache;
$this->view->client_cache			= $client_cache;
$this->view->limit       			= $limit;
$this->view->total       			= $total;
$this->view->url         			= HOST.'sales/distributor-management'.( $params ? '?'.http_build_query($params).'&' : '?' );


$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;
?>