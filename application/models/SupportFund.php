<?php
class Application_Model_SupportFund extends Zend_Db_Table_Abstract
{
	protected $_name = 'support_fund';

	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->joinleft(array('fc' => 'finance_client'),'p.finance_client_id = fc.id',array('finance_client_name' => 'fc.name'));
		$select->joinleft(array('ci' => 'cost_item'),'p.cost_id = ci.id',array('cost_item_name' => 'ci.cost_name'));


		if(isset($params['distributor_ids']) && $params['distributor_ids']) {
			$select->where('p.d_id =?',$params['distributor_ids']);
		}

		if(isset($params['status']) && $params['status']) {
			$select->where('p.status =?',$params['status']);
		}else{
			$select->where('p.status IN (?)',array(1,2,3));
		}

		if(isset($params['finance_client']) && $params['finance_client']) {
			$select->where('p.finance_client_id =?',$params['finance_client']);
		}

		if(isset($params['from']) && $params['from']) {
			$select->where('p.business_date >= ?',$params['from']);
		}

		if(isset($params['to']) && $params['to']) {
			$select->where('p.business_date <= ?',$params['to']);
		}

		$select->limitPage($page, $limit);

		$result = $db->fetchAll($select);
		$total = $db->fetchOne("select FOUND_ROWS()");
		return $result;
	}

}

?>