<?php
class Application_Model_FinanceWarehouseGroup extends Zend_Db_Table_Abstract
{
	protected $_name = 'finance_warehouse_group';

	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->joinleft(array('d' => 'distributor'),'p.d_id = d.id',array('distributor_id' => 'd.title'));
		$select->joinleft(array('w' => 'warehouse'),'d.agent_warehouse_id = w.id',array('warehouse_name' => 'w.name'));

		$select->where('p.del_by IS NULL');

		if(isset($params['dis_id']) && $params['dis_id']) {
			$select->where('p.d_id =?',$params['dis_id']);
		}

		if(isset($params['finance_warehouse_group']) && $params['finance_warehouse_group']) {
			$select->where('p.group_name LIKE ?', '%'.$params['finance_warehouse_group'].'%');
		}

		if(isset($params['fn']) && $params['fn']) {
			$select->where('p.d_id IN (?)',$params['fn']);
		}

		if($limit)
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