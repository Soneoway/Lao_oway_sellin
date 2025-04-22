<?php
class Application_Model_ContactNote extends Zend_Db_Table_Abstract
{
	protected $_name = 'contact_note';

	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->joinleft(array('fc' => 'finance_client'),'p.finance_client_id = fc.id',array('finance_client_name' => 'fc.name','finance_client_code' => 'fc.mnemonic_code'));
		$select->joinleft(array('d' => 'distributor'),'p.dis_y = d.id',array('distributor_you' => 'd.title'));
		$select->joinleft(array('ci' => 'cost_item'),'p.adjust_type = ci.id',array('adjustment_type' => 'ci.cost_name'));

		if(isset($params['distributor_id']) && $params['distributor_id']) {
			$select->where('p.d_id =?',$params['distributor_id']);
		}

		if(isset($params['finance_client']) && $params['finance_client']) {
			$select->where('p.finance_client_id =?',$params['finance_client']);
		}

		if(isset($params['doc_no']) && $params['doc_no']) {
			$select->where('p.code LIKE ?', '%'.$params['doc_no'].'%');
		}

		if(isset($params['cost_id']) && $params['cost_id']) {
			$select->where('p.adjust_type =?',$params['cost_id']);
		}

		if(isset($params['review_date_form']) && $params['review_date_form']) {
			$select->where('p.finance_date >=?',$params['review_date_form']);
		}

		if(isset($params['review_date_to']) && $params['review_date_to']) {
			$select->where('p.finance_date <=?',$params['review_date_to']);
		}

		if(isset($params['status']) && $params['status']) {
			$select->where('p.status =?',$params['status']);
		}

		if(isset($params['reconcilia_detail']) && $params['reconcilia_detail']) {
			$select->where('p.reconcil_details LIKE ?', '%'.$params['reconcilia_detail'].'%');
		}

		if(isset($params['business_date_form']) && $params['business_date_form']) {
			$select->where('p.business_date >=?',$params['business_date_form']);
		}

		if(isset($params['business_date_to']) && $params['business_date_to']) {
			$select->where('p.business_date <=?',$params['business_date_to']);
		}

		if($limit)
			$select->limitPage($page, $limit);

		$result = $db->fetchAll($select);
		$total = $db->fetchOne("select FOUND_ROWS()");
		return $result;
	}

}

?>