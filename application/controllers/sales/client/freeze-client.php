<?php
$flashMessenger = $this->_helper->flashMessenger;

$id 			= $this->getRequest()->getParam('id');
$status 		= $this->getRequest()->getParam('status');

$QClient = new Application_Model_Client();


$db = Zend_Registry::get('db');
$userStorage = Zend_Auth::getInstance()->getStorage()->read();

if($id){
	$clientRowSet = $QClient->find($id);
	$client       = $clientRowSet->current();


	if (!$client) {
		$flashMessenger->setNamespace('error')->addMessage('Invalid Client. Please Check And Try Agian !.');
		$this->_redirect(HOST.'sales/client-management');
	}

	try {

		if($status == 1) {
			
			$status_new = 2;

		}elseif($status == 2){

			$status_new = 1;
		}

		$data = array(
			'status' 		=> $status_new,
			'updated_at'	=> date('Y-m-d H:i:s'),
			'updated_by'	=> $userStorage->id
		);


		$where = $QClient->getAdapter()->quoteInto('id = ?', $id);
		$updatedcase = $QClient->update($data, $where);

		if($updatedcase){
			$flashMessenger->setNamespace('success')->addMessage('Update Data Success!');
		}else{
			$flashMessenger->setNamespace('error')->addMessage('Something went wrong.! ( Update Faill ) Please check and try again.!');
		}

	}catch (Exception $e){

		echo '<script>
		parent.palert("Something went wrong.! Please check and try again.!");
		</script>';
		exit;
	}

	echo '<script>parent.location.href="/sales/client-management"</script>';
	exit;

}

$this->view->client = $client;

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;


?>