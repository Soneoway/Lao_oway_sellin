<?php
class Application_Model_SaleRefund extends Zend_Db_Table_Abstract
{
	protected $_name = 'sale_refund';

	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->joinleft(array('fc' => 'finance_client'),'p.finance_client_id = fc.id',array('finance_client_name' => 'fc.name'));
		$select->joinleft(array('mb' => 'bank_account_my'),'p.my_bank = mb.id',array('bank_account' => 'mb.bank_account'));

		if(isset($params['distributor_ids']) && $params['distributor_ids']) {
			$select->where('p.d_id =?',$params['distributor_ids']);
		}

		if(isset($params['my_bank']) && $params['my_bank']) {
			$select->where('p.my_bank =?',$params['my_bank']);
		}

		if(isset($params['amount']) && $params['amount']) {
			$select->where('p.amount LIKE ?','%'.$params['amount'].'%');
		}

		if(isset($params['status']) && $params['status']) {
			$select->where('p.status =?',$params['status']);
		}

		if(isset($params['store_id']) && $params['store_id']) {
			$select->where('p.refund_dealer =?',$params['store_id']);
		}

		if(isset($params['warehouse_id']) && $params['warehouse_id']) {
			$select->where('p.refund_dealer =?',$params['warehouse_id']);
		}

		if(isset($params['business_date_form']) && $params['business_date_form']) {
			$select->where('p.business_date >= ?',$params['business_date_form']);
		}

		if(isset($params['business_date_to']) && $params['business_date_to']) {
			$select->where('p.business_date <= ?',$params['business_date_to']);
		}

		if(isset($params['finance_date_form']) && $params['finance_date_form']) {
			$select->where('p.finance_date >= ?',$params['finance_date_form']);
		}

		if(isset($params['finance_date_to']) && $params['finance_date_to']) {
			$select->where('p.finance_date <= ?',$params['finance_date_to']);
		}

		$select->where('p.status IN (?)',array(1,2,3));

		if($limit)
			$select->limitPage($page, $limit);

		$result = $db->fetchAll($select);
		$total = $db->fetchOne("select FOUND_ROWS()");
		return $result;
	}

}

?>