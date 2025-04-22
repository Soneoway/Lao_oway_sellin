<?php
$flashMessenger = $this->_helper->flashMessenger;

$id 				= $this->getRequest()->getParam('id');
$action_type 		= $this->getRequest()->getParam('action_type');
$finance_date 		= $this->getRequest()->getParam('finance_date');
$remark				= $this->getRequest()->getParam('remark');

$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$db = Zend_Registry::get('db');

$QSaleRefund = new Application_Model_SaleRefund();
$QContactDetail = new Application_Model_ContactDetail();

if($id){

	$saleRefundRowSet = $QSaleRefund->find($id);
	$sale_refund      = $saleRefundRowSet->current();

	if (!$sale_refund) {
		$flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍ່ຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
		$this->_redirect(HOST.'finance/sale-refund');
	}

	if(isset($action_type) && $action_type == 3) {

		try {
			$db->beginTransaction();

			$data = array(
				'status'			=> 2,
				'finance_date'		=> $finance_date,
				'approved_remark'	=> $remark,
				'approved_at'		=> date('Y-m-d H:i:s'),
				'approved_by'		=> $userStorage->id
			);

			$where = $QSaleRefund->getAdapter()->quoteInto('id =?',$id);
			$QSaleRefund->update($data, $where);

			$data_contact = array(
				'bill_date'			=> $finance_date.' '.date('H:i:s'),
				'status'			=> 2,
				'updated_at'		=> date('Y-m-s H:i:s'),
				'updated_by'		=> $userStorage->id
			);

			$where_contact = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$sale_refund->code);
			$QContactDetail->update($data_contact,$where_contact);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit();
			
		} catch (Exception $e) {
			
			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;
		}

		echo '<script>parent.location.href="/finance/sale-refund"</script>';
		exit;

	}elseif(isset($action_type) && $action_type == 4){

		try {

			$db->beginTransaction();

			$data = array(
				'status' 			=> 3,
				'finance_date'		=> null,
				'approved_remark'	=> null,
				'approved_at'		=> null,
				'approved_by'		=> null,
				'review_at' 		=> date('Y-m-d H:i:s'),
				'review_by'			=> $userStorage->id,
			);

			$where = $QSaleRefund->getAdapter()->quoteInto('id =?',$id);
			$QSaleRefund->update($data, $where);

			$data_contact = array(
				'bill_date'			=> null,
				'status'			=> 1,
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $userStorage->id
			);

			$where_contact = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$sale_refund->code);
			$QContactDetail->update($data_contact,$where_contact);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit();
			
		} catch (Exception $e) {

			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;
			
		}

		echo '<script>parent.location.href="/finance/sale-refund"</script>';
		exit;

	}elseif(isset($action_type) && $action_type == 5){

		try {

			$db->beginTransaction();

			$data = array(
				'status' 		=> 4,
				'review_at'		=> null,
				'review_by'		=> null,
				'updated_at' 	=> date('Y-m-d H:i:s'),
				'updated_by'	=> $userStorage->id,
			);

			$where = $QSaleRefund->getAdapter()->quoteInto('id =?',$id);
			$QSaleRefund->update($data, $where);

			$where_contact = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$sale_refund->code);
			$QContactDetail->delete($where_contact);

			$flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
			$db->commit();
			
		} catch (Exception $e) {

			$db->rollback();
			echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
			exit;
			
		}

		echo '<script>parent.location.href="/finance/sale-refund"</script>';
		exit;

	}

}

?>