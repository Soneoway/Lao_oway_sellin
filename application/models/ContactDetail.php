<?php
class Application_Model_ContactDetail extends Zend_Db_Table_Abstract
{
	protected $_name = 'contact_detail';

	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*','total_amount' => 'p.amount'));

		$select->joinleft(array('m' => 'market'),'p.doc_no = m.sn',array('m.sn_ref'));
		$select->joinleft(array('fc' => 'finance_client'),'p.finance_client_id = fc.id',array('finance_client_name' => 'fc.name'));
		$select->joinleft(array('dt' => 'document_type'),'p.type = dt.id',array('document_type_name' => 'dt.name_type'));
		$select->joinleft(array('d' => 'distributor'),'p.d_id = d.id',array());

		if(isset($params['distributor_ids']) && $params['distributor_ids']) {
			$select->where('fc.distributor_m_id =?',$params['distributor_ids']);
		}

		if(isset($params['doc_type']) && $params['doc_type']) {
			$select->where('p.type =?',$params['doc_type']);
		}

		if(isset($params['regional']) && $params['regional']) {
			$select->where('d.region =?',$params['regional']);
		}

		if(isset($params['finance_client']) && $params['finance_client']) {
			$select->where('p.finance_client_id =?',$params['finance_client']);
		}

		if(isset($params['date_from']) && $params['date_from']) {
			$select->where('p.bill_date >= ?',$params['date_from'].' 00:00:00');
		}

		if(isset($params['date_to']) && $params['date_to']) {
			$select->where('p.bill_date <=?',$params['date_to']. ' 23:59:59');
		}

		$select->where('p.status = 2');
		$select->group('p.doc_no');

		if($limit)
			$select->limitPage($page, $limit);

		$result = $db->fetchAll($select);
		$total = $db->fetchOne("select FOUND_ROWS()");
		return $result;
	}


	function getFianaceClientPaymentDetail($finance_client){
		
		$db = Zend_Registry::get('db');

		$sub_select_01 = $db->select()
			->from(array('sr' => 'sale_receipt'),array('SUM(sr.amount)'))
			->where('sr.status =?',2) // Approved
			->where('sr.finance_client = p.id');

		$sub_select_02 = $db->select()
			->from(array('srf' => 'sale_refund'),array('SUM(srf.amount)'))
			->where('srf.status =?',2) // Approved
			->where('srf.finance_client_id = p.id');

		$sub_select_03 = $db->select()
			->from(array('cl' => 'credit_limit'),array('SUM(cl.quota)'))
			->where('cl.status =?',2) // Approved
			->where('cl.finance_client_id = p.id');

		$sub_select_04 = $db->select()
			->from(array('m' => 'market'),array('SUM(m.total)'))
			->where('m.order_status =?',4) // Order Complete
			->where('m.isbacks = 0') // is Order
			->where('m.finance_client_id = p.id');

		$sub_select_05 = $db->select()
			->from(array('m2' => 'market' ),array('SUM(m2.total)'))
			->where('m2.order_status =?',4) // Return Complete
			->where('m2.isbacks = 1') // is Return
			->where('m2.finance_client_id = p.id');

		$sub_select_06 = $db->select()
			->from(array('sf' => 'support_fund'),array('SUM(sf.amount)'))
			->where('sf.status =?',2)
			->where('sf.finance_client_id = p.id');

		$sub_select_07 = $db->select()
			->from(array('cn' => 'contact_note'),array('SUM(cn.amount)'))
			->where('cn.status =?',2)
			->where('cn.finance_client_id = p.id');

		$get = array(
			'finance_client_id' 		=> 'p.id',
			'finance_client_name'		=> 'p.name',

			'sale_receipt' 				=> new Zend_Db_Expr("(".$sub_select_01.")"), // ຍອດເຕິມເຂົ້າລະບົບ
			'sale_refund'  				=> new Zend_Db_Expr("(".$sub_select_02.")"), // ຍອດສົ່ງຄືນ
			'credit_limit' 				=> new Zend_Db_Expr("(".$sub_select_03.")"), // ເງິນເຄດິດ
			'sale_order_amount'			=> new Zend_Db_Expr("(".$sub_select_04.")"), // ຍອດສັ່ງເຄື່ອງ
			'retrun_amount'				=> new Zend_Db_Expr("(".$sub_select_05.")"), // ຍອດເທິນເຄືອງ
			'support_payment'			=> new Zend_Db_Expr("(".$sub_select_06.")"), // ເງິນນະໂຍບາຍ
			'contact_note'				=> new Zend_Db_Expr("(".$sub_select_07.")"), // ຍອດຕັດເງິນ ເເລະ ລາຍຮັບອື່ນໆ

		);

		$select = $db->select()
			->from(array('p' => 'finance_client'),$get);
		$select->where('p.id = ?',$finance_client);

		// echo $select; die();

		$result = $db->fetchAll($select);

		return $result;

		
	}

	function getClientDetail($doc_no) {

		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->joinleft(array('fc' => 'finance_client'),'p.finance_client_id = fc.id',array('finance_client_name' => 'fc.name'));
		$select->joinleft(array('dt' => 'document_type'),'p.type = dt.id',array('document_type_name' => 'dt.name_type'));
		
		$select->where('p.doc_no =?',$doc_no);
		$select->where('p.status = 2');
		
		$result = $db->fetchAll($select);

		return $result;

	}

}
?>