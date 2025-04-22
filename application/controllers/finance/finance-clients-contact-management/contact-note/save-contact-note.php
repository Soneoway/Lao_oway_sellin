<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

	$id                    				= $this->getRequest()->getParam('id');
	$distributor_ids                    = $this->getRequest()->getParam('distributor_ids');
	$finance_client_id                  = $this->getRequest()->getParam('finance_client_id');
	$adjust_type                       	= $this->getRequest()->getParam('adjust_type');
	$business_date                      = $this->getRequest()->getParam('business_date');
	$conatin_product                    = $this->getRequest()->getParam('conatin_product');
	$dis_y_id							= $this->getRequest()->getParam('dis_y_id');
	$amount							    = $this->getRequest()->getParam('amount');
	$reconcil_details					= $this->getRequest()->getParam('reconcil_details');

	$userStorage = Zend_Auth::getInstance()->getStorage()->read();
	$db = Zend_Registry::get('db');

	$QContactNote = new Application_Model_ContactNote();
	$QClientCode = new Application_Model_ClientCode();
	$QContactDetail = new Application_Model_ContactDetail();
	$QDistributior = new Application_Model_Distributor();
	$QStore = new Application_Model_Store();

	if($id) {

		try {

			$db->beginTransaction();

			$where = $QContactNote->getAdapter()->quoteInto('id =?',$id);
            $contact_note = $QContactNote->fetchRow($where);

			$data = array(
				'finance_client_id'		=> $finance_client_id,
				'adjust_type'			=> $adjust_type,
				'conatin_product'		=> $conatin_product,
				'dis_y'					=> $dis_y_id,
				'amount'				=> $amount,
				'reconcil_details'		=> $reconcil_details,
				'updated_at'			=> date('Y-m-d H:i:s'),
				'updated_by'			=> $userStorage->id,
				'business_date'			=> $business_date
			);

			$where = $QContactNote->getAdapter()->quoteInto('id =?',$id);
			$QContactNote->update($data,$where);

			$data_contact_detail = array(
				'store_id'				=> null,
				'd_id'					=> null,
				'finance_client_id'		=> $finance_client_id,
				'status'				=> 1,
				'description'			=> 'add Contact Note '.$amount.' LAK',
				'amount'				=> $amount,
				'updated_at'			=> date('Y-m-d H:i:s'),
				'updated_by'			=> $userStorage->id
			);

			$where_contact_note = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$contact_note->code);
            $QContactDetail->update($data_contact_detail,$where_contact_note);

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

			$cusCode        = $QClientCode->find(11);
        $insertCode     = $cusCode[0]['next_code']; // ລະຫັດປະຈຸບັນ ( ຕົວອັກສອນນຳໜ້າ + ຕົວເລກ )
        $running_next   = $cusCode[0]['running_next']; // ລະຫັດຕົວເລກປະຈຸບັນ
        $running_now    = $cusCode[0]['running_now']; // ລະຫັດຕົວເລກກ່ອນໜ້າ
        $prefix         = $cusCode[0]['prefix']; // ໂຕອັນສອນນຳໜ້າ

        $data = array(
        	'code'					=> $insertCode,
        	'd_id'					=> $distributor_ids,
        	'finance_client_id'		=> $finance_client_id,
        	'adjust_type'			=> $adjust_type,
        	'conatin_product'		=> $conatin_product,
        	'dis_y'					=> $dis_y_id,
        	'amount'				=> $amount,
        	'reconcil_details'		=> $reconcil_details,
        	'created_at'			=> date('Y-m-d H:i:s'),
        	'created_by'			=> $userStorage->id,
        	'status'				=> 1,
        	'business_date'			=> $business_date
        );

        $QContactNote->insert($data);

        $data_contact_detail = array(
        	'type'					=> 6,
        	'doc_no'				=> $insertCode,
        	'store_id'				=> null,
        	'd_id'					=> null,
        	'finance_client_id'		=> $finance_client_id,
        	'status'				=> 1,
        	'description'			=> 'add Contact Note '.$amount.' LAK',
        	'amount'				=> $amount,
        	'created_at'			=> date('Y-m-d H:i:s'),
        	'created_by'			=> $userStorage->id
        );

        $QContactDetail->insert($data_contact_detail);

        $next_running_code = $running_next + 1; 

        $data_code = array(
        	'last_code'     => $prefix.''.$running_next,
        	'next_code'     => $prefix.''.$next_running_code,
        	'running_now'   => $running_next,
        	'running_next'  => $next_running_code,
        	'digital'       => $next_running_code,
        	'updated_at'    => date('Y-m-d H:i:s')
        );

        $where = $QClientCode->getAdapter()->quoteInto('id = ?', 11);
        $QClientCode->update($data_code,$where);


        $flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
        $db->commit(); 

    } catch (Exception $e) {

    	$db->rollback();
    	echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
    	exit;

    }
}

echo '<script>parent.location.href="/finance/contact-note"</script>';
exit;

}

?>