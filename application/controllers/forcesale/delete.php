<?php

$flashMessenger = $this->_helper->flashMessenger;

$key          				= $this->getRequest()->getParam('id');


	$QForceSale  			= new Application_Model_ForceSale();
	$QForceSaleDetail  		= new Application_Model_ForceSaleDetail();
	$QForceSaleDistributor  = new Application_Model_ForceSaleDistributor();
	$QForceSaleWarehouse  	= new Application_Model_ForceSaleWarehouse();


	$whereForceSale  			= $QForceSale->getAdapter()->quoteInto('campaign_id = ?', $key);
	$whereForceSaleDetail  		= $QForceSaleDetail->getAdapter()->quoteInto('force_sale_id = ?', $key);
	$whereForceSaleWarehouse  	= $QForceSaleWarehouse->getAdapter()->quoteInto('force_sale_id = ?', $key);
	$whereQForceSaleDistributor = $QForceSaleDistributor->getAdapter()->quoteInto('force_sale_id = ?', $key);

		$QForceSale 			->delete($whereForceSale);
		$QForceSaleDetail		->delete($whereForceSaleDetail);
		$QForceSaleWarehouse	->delete($whereForceSaleWarehouse);
		$QForceSaleDistributor	->delete($whereQForceSaleDistributor);

	

$flashMessenger = $this->_helper->flashMessenger;
$this->view->messages_success = $flashMessenger->setNamespace('Delete Success')->getMessages();
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();

	$this->_redirect('/force-sale');
?>