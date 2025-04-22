<?php
class Application_Model_CreditLimit extends Zend_Db_Table_Abstract
{
	protected $_name = 'credit_limit';

	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$now_date = date('Y-m-d');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->joinleft(array('fc' => 'finance_client'),'p.finance_client_id = fc.id',array('finance_client_name' => 'fc.name'));
		$select->joinleft(array('d' => 'distributor'),'fc.distributor_y_id = d.id',array());
		$select->joinleft(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());

		if(isset($params['distributor_id']) && $params['distributor_id']) {
			$select->where('p.d_id =?',$params['distributor_id']);
		}

		if(isset($params['finance_client']) && $params['finance_client']) {
			$select->where('p.finance_client_id =?',$params['finance_client']);
		}

		if(isset($params['quota_type']) && $params['quota_type']) {
			$select->where('p.quota_type =?',$params['quota_type']);
		}

		if(isset($params['area_id']) && $params['area_id']) {
			$select->where('rm.area_id =?',$params['area_id']);
		}

		if(isset($params['effective_status']) && $params['effective_status']){ // Valid In Use

		}

		if(isset($params['status']) && $params['status']){
			$select->where('p.status =?',$params['status']);
		}

		if(isset($params['effective_from']) && $params['effective_from']){
			$select->where('p.start_date >=?',$params['effective_from']);
		}

		if(isset($params['effective_to']) && $params['effective_to']){
			$select->where('p.end_date <=?',$params['effective_to']);
		}


		$select->limitPage($page, $limit);

		$result = $db->fetchAll($select);
		$total = $db->fetchOne("select FOUND_ROWS()");
		return $result;
	}


}

?>