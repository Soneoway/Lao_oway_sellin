<?php
$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');
$url = $this->getRequest()->getParam('url');

$QPaySlip = new Application_Model_PaySlip();
$db = Zend_Registry::get('db');

if($id) {

	$payslipRowSet = $QPaySlip->find($id);
	$slip       = $payslipRowSet->current();

	if (!$slip) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/sale-receipt');
	}

	$where = $QPaySlip->getAdapter()->quoteInto('id =?', $id);
	$deletionSuccessful = $QPaySlip->delete($where);

	if ($deletionSuccessful) {
  		echo "success";
	} else {
  		echo "Error deleting data";
	}

}
        
$db->closeConnection();
exit();


?>