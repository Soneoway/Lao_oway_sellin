<?php

$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

	$id 								= $this->getRequest()->getParam('id');
	$distributor_name 					= $this->getRequest()->getParam('distributor_name');
	$cilent_code 						= $this->getRequest()->getParam('cilent_code');
	$leader 							= $this->getRequest()->getParam('leader');
	$superior_d 						= $this->getRequest()->getParam('superior_d');
	$rank_price 						= $this->getRequest()->getParam('rank_price');
	$ex_serial 							= $this->getRequest()->getParam('ex_serial');
	$dis_type							= $this->getRequest()->getParam('dis_type');
	$provience_id						= $this->getRequest()->getParam('provience_id');
	$tag								= $this->getRequest()->getParam('tag');
	$remark								= $this->getRequest()->getParam('remark');

	$userStorage = Zend_Auth::getInstance()->getStorage()->read();
	$db = Zend_Registry::get('db');

	$QClient = new Application_Model_Client();
	$QClientCode = new Application_Model_ClientCode();
	$QDistributorNew = new Application_Model_DistributorNew();

	if($id) {

		$distributorRowSet = $QDistributorNew->find($id);
		$distributor       = $distributorRowSet->current();

		if (!$distributor) {
			$flashMessenger->setNamespace('error')->addMessage('Invalid Client. Please Check And Try Agian !.');
			$this->_redirect(HOST.'sales/distributor-management');
		}

		if($distributorRowSet[0]['distributor_code'] == $superior_d) {
			$flashMessenger->setNamespace('error')->addMessage('Distributor and Superior Distributor is duplicate. Please Check And Try Agian !.');
			$this->_redirect(HOST.'sales/distributor-management');
		}

		try {

			$data = array(
				'distributor_name'		=> $distributor_name,
				'superior_distributor'	=> $superior_d,
				'distributor_type'		=> $dis_type,
				'client_code'			=> $cilent_code,
				'provience_id'			=> $provience_id,
				'leader'				=> $leader,
				'external_serial'		=> $ex_serial,
				'remark'				=> $remark,
				'updated_at'			=> date('Y-m-d H:i:s'),
				'updated_by'			=> $userStorage->id
			);

			$where = $QDistributorNew->getAdapter()->quoteInto('id = ?', $id);
			$updatedcase = $QDistributorNew->update($data, $where);

			if($updatedcase){
				$flashMessenger->setNamespace('success')->addMessage('Update Data Success!');
			}else{
				$flashMessenger->setNamespace('error')->addMessage('Something went wrong.! ( Update Faill ) Please check and try again.!');
			}

		}catch (Exception $e){
			echo '<script>
			parent.palert("Something went wrong.! ( Update Faill ! ) Please check and try again.!");
			</script>';
			exit;
		}

		echo '<script>parent.location.href="/sales/distributor-management"</script>';
		exit;

	}else{

		try {

			$cusCode 	  	= $QClientCode->find(2);
			$insertCode 	= $cusCode[0]['next_code'];


			$data = array(
				'distributor_code'		=> $insertCode,
				'distributor_name'		=> $distributor_name,
				'superior_distributor'	=> $superior_d,
				'distributor_type'		=> $dis_type,
				'client_code'			=> $cilent_code,
				'rank_price'			=> null,
				'provience_id'			=> $provience_id,
				'leader'				=> $leader,
				'external_serial'		=> $ex_serial,
				'tag'					=> null,
				'status'				=> 1,
				'remark'				=> $remark,
				'created_at'			=> date('Y-m-d H:i:s'),
				'created_by'			=> $userStorage->id
			);

			$insertcase = $QDistributorNew->insert($data);

			if($insertcase){

				$next_code = $insertCode + 1; 

				$data_code = array(
					'last_code' 	=> $insertCode,
					'next_code' 	=> $next_code,
					'updated_at' 	=> date('Y-m-d H:i:s')
				);

				$where = $QClientCode->getAdapter()->quoteInto('id = ?', 2);
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


}

echo '<script>parent.location.href="/sales/distributor-management"</script>';
exit;



?>