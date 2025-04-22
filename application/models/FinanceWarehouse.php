<?php
class Application_Model_FinanceWarehouse extends Zend_Db_Table_Abstract
{
	protected $_name = 'finance_warehouse';

	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->joinleft(array('aorg' => 'accounting_organization'),'p.account_org = aorg.id',array('account_org_name' => 'aorg.name'));
		$select->joinleft(array('fwg' => 'finance_warehouse_group'),'p.finance_wh_group = fwg.id',array('finance_wh_group_name'  => 'fwg.group_name'));
		$select->joinleft(array('w' => 'warehouse'),'p.warehouse_id = w.id',array('warehouse_name' => 'w.name'));

		$select->where('p.del_by IS NULL');

		if (isset($params['dis_id']) && $params['dis_id']) {
			$select->where('p.d_id =?',$params['dis_id']);
		}

		if (isset($params['warehouse_id']) && $params['warehouse_id']) {
			$select->where('p.warehouse_id =?',$params['warehouse_id']);
		}

		if (isset($params['finance_warehouse']) && $params['finance_warehouse']) {
			$select->where('p.name LIKE ?', '%'.$params['finance_warehouse'].'%');
		}

		if (isset($params['mnemonic_code']) && $params['mnemonic_code']) {
			$select->where('p.mnemonic_code LIKE ?', '%'.$params['mnemonic_code'].'%');
		}

		if (isset($params['fn']) && $params['fn']) {
			$select->where('p.d_id IN (?)',$params['fn']);
		}

		if ($limit)
			$select->limitPage($page, $limit);

		$result = $db->fetchAll($select);
		$total = $db->fetchOne("select FOUND_ROWS()");
		return $result;
	}

	function get_cache(){
		$cache      = Zend_Registry::get('cache');
		$result     = $cache->load($this->_name.'_cache');

		if ($result === false) {

			$db = Zend_Registry::get('db');

			$select = $db->select()
			->from(array('p' => $this->_name),
				array('p.*'));

			$select->where('p.status =?',1);
			$select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

			$data = $db->fetchAll($select);

			$result = array();
			if ($data){
				foreach ($data as $item){
					$result[$item['id']] = $item['name'];
				}
			}
			$cache->save($result, $this->_name.'_cache', array(), null);
		}
		return $result;
	}

}
?>