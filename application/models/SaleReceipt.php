<?php
class Application_Model_SaleReceipt extends Zend_Db_Table_Abstract
{
	protected $_name = 'sale_receipt';

	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->joinleft(array('fc' => 'finance_client'),'p.finance_client = fc.id',array('finance_client_name' => 'fc.name','finance_client_code' => 'fc.mnemonic_code'));
		$select->joinleft(array('bam' => 'bank_account_my'),'p.bank_account = bam.id',array('bank_account_name' => 'bam.bank_account','card_no' => 'bam.card_no'));
		$select->joinleft(array('act' => 'account_type'),'p.stm_type = act.id',array('smt_name' => 'act.name'));

		if(isset($params['distributor_ids']) && $params['distributor_ids']) {
			$select->where('p.dis_my =?',$params['distributor_ids']);
		}

		if(isset($params['store_id']) && $params['store_id']) {
			$select->where('p.store_id =?',$params['store_id']);
		}

		if(isset($params['warehouse_id']) && $params['warehouse_id']) {
			$select->where('p.warehouse_id =?',$params['warehouse_id']);
		}

		if(isset($params['transfer_type']) && $params['transfer_type']) {
			$select->where('p.transfer_type =?',$params['transfer_type']);
		}

		if(isset($params['serial_number']) && $params['serial_number']) {
			$select->where('p.serial_number LIKE ?', '%'.$params['serial_number'].'%');
		}

		if(isset($params['amount']) && $params['amount']) {
			$select->where('p.amount LIKE ?', '%'.$params['amount'].'%');
		}

		if(isset($params['create_form']) && $params['create_form']) {
			$select->where('p.created_at >= ?',$params['create_form'].' 00:00:00');
		}

		if(isset($params['create_to']) && $params['create_to']) {
			$select->where('p.created_at <= ?',$params['create_to'].' 23:59:59');
		}

		if(isset($params['finance_client']) && $params['finance_client']) {
			$select->where('p.finance_client =?',$params['finance_client']);
		}

		if(isset($params['status']) && $params['status']) {
			$select->where('p.status =?',$params['status']);
		}

		if(isset($params['bank_my']) && $params['bank_my']) {
			$select->where('p.bank_account =?',$params['bank_my']);
		}

		if(isset($params['area_id']) && $params['area_id']) {
			$select->joinleft(array('d' => 'distributor'),'p.dis_you = d.id',array());
			$select->joinleft(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
			$select->where('rm.area_id =?',$params['area_id']);
		}

		if(isset($params['doc_no']) && $params['doc_no']) {
			$select->where('p.document_no LIKE ?','%'.$params['doc_no'].'%');
		}

		if(isset($params['finance_date_form']) && $params['finance_date_form']) {
			$select->where('p.finance_date >= ?',$params['finance_date_form'].' 00:00:00');
		}

		if(isset($params['finance_date_to']) && $params['finance_date_to']) {
			$select->where('p.finance_date <= ?',$params['finance_date_to'].' 23:59:59');
		}

		if(isset($params['fn']) && $params['fn']) {
			$select->where('p.dis_my IN (?)',$params['fn']);
		}

		if($limit)
			$select->limitPage($page, $limit);

		// echo $select; die;

		$result = $db->fetchAll($select);
		$total = $db->fetchOne("select FOUND_ROWS()");
		return $result;
	}

}
?>