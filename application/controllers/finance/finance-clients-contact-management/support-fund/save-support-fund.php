<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

	$id 				= $this->getRequest()->getParam('id');
	$dis_id       		= $this->getRequest()->getParam('dis_id');
	$finance_client		= $this->getRequest()->getParam('finance_client');
	$amount				= $this->getRequest()->getParam('amount');
	$cost_id			= $this->getRequest()->getParam('cost_id');
	$bussiness_date		= $this->getRequest()->getParam('bussiness_date');
	$remark				= $this->getRequest()->getParam('remark');


	$userStorage = Zend_Auth::getInstance()->getStorage()->read();
	$db = Zend_Registry::get('db');

	$QClientCode = new Application_Model_ClientCode();
	$QSupportFund = new Application_Model_SupportFund();
	$QContactDetail = new Application_Model_ContactDetail();
	$QDistributior = new Application_Model_Distributor();
	$QStore = new Application_Model_Store();

	if($id) {

		$supportFundRowSet = $QSupportFund->find($id);
		$support_fund       = $supportFundRowSet->current();

		if (!$support_fund) {
			$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍ່ຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
			$this->_redirect(HOST.'finance/support-fund-management');
		}

		try {

			$db->beginTransaction();

			$data = array(
				'finance_client_id'		=> $finance_client,
				'amount'				=> $amount,
				'cost_id'				=> $cost_id,
				'business_date'			=> $bussiness_date,
				'remark'				=> $remark,
				'updated_at'			=> date('Y-m-d H:i:s'),
				'updated_by'			=> $userStorage->id
			);

			$where = $QSupportFund->getAdapter()->quoteInto('id =?',$id);
			$QSupportFund->update($data,$where);

			$data_contact_detail = array(
				'type'              => 5,
				'store_id'          => null,
				'd_id'              => null,
				'finance_client_id' => $finance_client,
				'status'            => 1,
				'description'       => 'Add Support Payment Amount '.$amount.' LAK',
				'amount'            => $amount,
				'created_at'        => date('Y-m-d H:i:s'),
				'created_by'        => $userStorage->id
			);

			$where_contact = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$support_fund->doc_no);
			$QContactDetail->update($data_contact_detail,$where_contact);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit(); 
			
		} catch (Exception $e) {

			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;
			
		}

	}else{

		try {

			$db->beginTransaction();

			$cusCode        = $QClientCode->find(12);
        $insertCode     = $cusCode[0]['next_code']; // ລະຫັດປະຈຸບັນ ( ຕົວອັກສອນນຳໜ້າ + ຕົວເລກ )
        $running_next   = $cusCode[0]['running_next']; // ລະຫັດຕົວເລກປະຈຸບັນ
        $running_now    = $cusCode[0]['running_now']; // ລະຫັດຕົວເລກກ່ອນໜ້າ
        $prefix         = $cusCode[0]['prefix']; // ໂຕອັນສອນນຳໜ້າ

        $data = array(
        	'doc_no'				=> $insertCode,
        	'd_id'					=> $dis_id,
        	'finance_client_id'		=> $finance_client,
        	'amount'				=> $amount,
        	'cost_id'				=> $cost_id,
        	'status'				=> 1,
        	'business_date'			=> $bussiness_date,
        	'remark'				=> $remark,
        	'created_at'			=> date('Y-m-d H:i:s'),
        	'created_by'			=> $userStorage->id
        );

        $QSupportFund->insert($data);

        $next_running_code = $running_next + 1; 

        $data_code = array(
        	'last_code'     => $prefix.''.$running_next,
        	'next_code'     => $prefix.''.$next_running_code,
        	'running_now'   => $running_next,
        	'running_next'  => $next_running_code,
        	'digital'       => $next_running_code,
        	'updated_at'    => date('Y-m-d H:i:s')
        );

        $where = $QClientCode->getAdapter()->quoteInto('id = ?', 12);
        $QClientCode->update($data_code,$where);

        $data_contact_detail = array(
        	'type'              => 5,
        	'doc_no'            => $insertCode,
        	'store_id'          => null,
        	'd_id'              => null,
        	'finance_client_id' => $finance_client,
        	'status'            => 1,
        	'description'       => 'Add Support Payment Amount '.$amount.' LAK',
        	'amount'            => $amount,
        	'created_at'        => date('Y-m-d H:i:s'),
        	'created_by'        => $userStorage->id
        ); 

        $QContactDetail->insert($data_contact_detail);

        $flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
        $db->commit(); 

    } catch (Exception $e) {

    	$db->rollback();
    	echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
    	exit;

    }
}

echo '<script>parent.location.href="/finance/support-fund-management"</script>';
exit;

}

?>