<?php
class Application_Model_DistributorNew extends Zend_Db_Table_Abstract
{
	protected $_name = 'distributor_new';

	function getSuperiorDsitributor(){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array('p.*'));

		$select->where('p.distributor_type IN (?)',array(1,4));
		$select->where('p.status =?',1);

		return $db->fetchAll($select);

	}


	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->joinLeft(array('c' => 'client'),'p.client_code = c.customer_code',array('c.client_name','c.short_name','c.client_type'));

		if(isset($params['distributor_name']) && $params['distributor_name']) {
			$select->where('p.distributor_name  LIKE ?', '%'.$params['distributor_name'].'%');
		}

		if(isset($params['superior_d']) && $params['superior_d']) {
			$select->where('p.superior_distributor = ?',$params['superior_d']);
		}

		if(isset($params['status']) && $params['status']) {
			$select->where('p.status =?', $params['status']);
		}

		if(isset($params['distributor_type']) && $params['distributor_type']) {
			$select->where('p.distributor_type =?', $params['distributor_type']);
		}

		if(isset($params['ext_code']) && $params['ext_code']) {
			$select->where('p.external_serial =?', $params['external_serial']);
		}

		if(isset($params['distributor_code']) && $params['distributor_code']) {
			$select->where('p.distributor_code =?', $params['distributor_code']);
		}
		if(isset($params['office']) && $params['office']) {
			$select->where('p.provience_id =?', $params['office']);
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

			$select->order(new Zend_Db_Expr('p.`distributor_name` COLLATE utf8_unicode_ci'));

			$data = $db->fetchAll($select);

			$result = array();
			if ($data){
				foreach ($data as $item){
					$result[$item['distributor_code']] = $item['distributor_name'];
				}
			}
			$cache->save($result, $this->_name.'_cache', array(), null);
		}
		return $result;
	}
}