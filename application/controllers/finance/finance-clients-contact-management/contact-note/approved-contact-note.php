<?php
$flashMessenger = $this->_helper->flashMessenger;

$id 				= $this->getRequest()->getParam('id');
$action_type 		= $this->getRequest()->getParam('action_type');
$finance_date 		= $this->getRequest()->getParam('finance_date');
$remark				= $this->getRequest()->getParam('remark');

$QContactNote = new Application_Model_ContactNote();
$QContactDetail = new Application_Model_ContactDetail();

$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$db = Zend_Registry::get('db');

if($id){

	$ContactRowSet = $QContactNote->find($id);
	$ContactNote       = $ContactRowSet->current();

	// Get Data of Contact Note Table
	$where_contact = $QContactNote->getAdapter()->quoteInto('id =?',$id);
	$contact_note_data = $QContactNote->fetchRow($where_contact);

	if (!$ContactNote) {
		$flashMessenger->setNamespace('error')->addMessage('ບັນຊີອົງກອນບໍ່ຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/contact-note');
	}

	if(isset($action_type) && $action_type == 3) { // SAVE Approve Case

		try {

			$db->beginTransaction();

			$data = array(
				'status'			=> 2,
				'finance_date'		=> $finance_date,
				'approved_remark'	=> $remark,
				'approved_at'		=> date('Y-m-d H:i:s'),
				'approved_by'		=> $userStorage->id
			);

			$where = $QContactNote->getAdapter()->quoteInto('id =?',$id);
			$QContactNote->update($data, $where);

			// Date Array[] For Update Contact note Detail Table
			$data_contact_note = array(
				'status'			=> 2,
				'bill_date'			=> $finance_date,
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $userStorage->id
			);

			// Updated Table Contact Note Detail By Contact Note Code
			$where_contact_note = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$contact_note_data->code);
			$QContactDetail->update($data_contact_note,$where_contact_note);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit(); 
			
		} catch (Exception $e) {

			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;
			
		}

		echo '<script>parent.location.href="/finance/contact-note"</script>';
		exit;

	}elseif(isset($action_type) && $action_type == 4) { // Re-review Case

		try {

			$db->beginTransaction();

			$data = array(
				'status' 			=> 3,
				'finance_date'		=> null,
				'approved_remark'	=> null,
				'approved_at'		=> null,
				'approved_by'		=> null,
				'review_date' 		=> date('Y-m-d H:i:s'),
				'review_by'			=> $userStorage->id,
			);

			$where = $QContactNote->getAdapter()->quoteInto('id =?',$id);
			$QContactNote->update($data, $where);

			// Date Array[] For Update Contact note Detail Table
			$data_contact_note = array(
				'status'			=> 1,
				'bill_date'			=> null,
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $userStorage->id
			);

			// Updated Table Contact Note Detail By Contact Note Code
			$where_contact_note = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$contact_note_data->code);
			$QContactDetail->update($data_contact_note,$where_contact_note);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit(); 
			
		} catch (Exception $e) {
			
			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;
			
		}

		echo '<script>parent.location.href="/finance/contact-note"</script>';
		exit;

	}elseif(isset($action_type) && $action_type == 5) { // Delete Case

		try {

			$db->beginTransaction();

			$data = array(
				'status' 		=> 4,
				'review_date'	=> null,
				'review_by'		=> null,
				'updated_at' 	=> date('Y-m-d H:i:s'),
				'updated_by'	=> $userStorage->id,
			);
			
			$where = $QContactNote->getAdapter()->quoteInto('id =?',$id);
			$QContactNote->update($data, $where);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit(); 

		} catch (Exception $e) {
			
			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;

		}

		echo '<script>parent.location.href="/finance/contact-note"</script>';
		exit;
	}

}

?>