<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

$id 					= $this->getRequest()->getParam('id');
$client_name 			= $this->getRequest()->getParam('client_name');
$custormer 				= $this->getRequest()->getParam('custormer');
$person 				= $this->getRequest()->getParam('person');
$phone_number 			= $this->getRequest()->getParam('phone_number');
$email					= $this->getRequest()->getParam('email');
$short_name 			= $this->getRequest()->getParam('short_name');
$cooperate_date 		= $this->getRequest()->getParam('cooperate_date');
$level 					= $this->getRequest()->getParam('level');
$tag 					= $this->getRequest()->getParam('tag');
$remark					= $this->getRequest()->getParam('remark');


$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$db = Zend_Registry::get('db');

$QClient = new Application_Model_Client();
$QClientCode = new Application_Model_ClientCode();

if($id) {

	$clientRowSet = $QClient->find($id);
    $client       = $clientRowSet->current();

    if (!$client) {
        $flashMessenger->setNamespace('error')->addMessage('Invalid Client. Please Check And Try Agian !.');
        $this->_redirect(HOST.'sales/client-management');
    }

	try {

		if($cooperate_date){
			$temp = explode('/' , $cooperate_date);
			$temp = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
			$date = trim($temp);
		}

		$data = array(
			'client_name' 		=> $client_name,
			'short_name'  		=> $short_name,
			'custormer'   		=> $custormer,
			'person'      		=> $person,
			'phone_number'  	=> $phone_number,
			'email'				=> $email,
			'tag'				=> $tag,
			'level'		    	=> $level,
			'cooperate_date'	=> $date,
			'remark'			=> $remark,
			'updated_at'		=> date('Y-m-d H:i:s'),
			'updated_by'		=> $userStorage->id,
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


}else{

	try{

		$cusCode 	  	= $QClientCode->find(1);
		$insertCode 	= $cusCode[0]['next_code'];
		$insertCodeID	= $cusCode[0]['id'];


		if($cooperate_date){
			$temp = explode('/' , $cooperate_date);
			$temp = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
			$date = trim($temp);
		}

		$data = array(
			'customer_code'		=> $insertCode,
			'client_name' 		=> $client_name,
			'short_name'  		=> $short_name,
			'custormer'   		=> $custormer,
			'person'      		=> $person,
			'phone_number'  	=> $phone_number,
			'email'				=> $email,
			'tag'				=> $tag,
			'level'		    	=> $level,
			'cooperate_date'	=> $date,
			'status'			=> 1,
			'remark'			=> $remark,
			'created_at'		=> date('Y-m-d H:i:s'),
			'created_by'		=> $userStorage->id,
		);


		$insertcase = $QClient->insert($data);

		if($insertcase){

			$next_code = $insertCode + 1; 

			$data_code = array(
				'last_code' 	=> $insertCode,
				'next_code' 	=> $next_code,
				'updated_at' 	=> date('Y-m-d H:i:s')
			);

			$where = $QClientCode->getAdapter()->quoteInto('id = ?', 1);
			$QClientCode->update($data_code,$where);

			$flashMessenger->setNamespace('success')->addMessage('Save Data Success!');
		}else{
			$flashMessenger->setNamespace('error')->addMessage('Something went wrong.! Please check and try again.!');
		}


	}catch (Exception $e){


		echo '<script>
		parent.palert("Something went wrong.! Please check and try again.!");
		</script>';
		exit;
	}
}

echo '<script>parent.location.href="/sales/client-management"</script>';
exit;

}

echo '<script>parent.location.href="/sales/client-management"</script>';
exit;

?>