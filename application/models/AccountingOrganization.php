<?php
class Application_Model_AccountingOrganization extends Zend_Db_Table_Abstract
{
	protected $_name = 'accounting_organization';


	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->where('p.del_by IS NULL');

		if (isset($params['dis_id']) && $params['dis_id']){
			$select->where('p.d_id =?',$params['dis_id']);
		}

		if (isset($params['mnemonic_code']) && $params['mnemonic_code']){
			$select->where('p.mnemonic_code LIKE ?', '%'.$params['mnemonic_code'].'%');
		}

		if (isset($params['corporation_type']) && $params['corporation_type']){
			$select->where('p.corporation_type =?',$params['corporation_type']);
		}

		if (isset($params['account_org']) && $params['account_org']){
			$select->where('p.name LIKE ?', '%'.$params['account_org'].'%');
		}

		if (isset($params['fn']) && $params['fn']){
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

?>