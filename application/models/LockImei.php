<?php
class Application_Model_LockImei extends Zend_Db_Table_Abstract
{
	protected $_name = 'imei_lock';

	public function getlock_imei($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

	if($limit){

		$select = $db->select()
		->from(array('p' => $this->_name),
		 array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
	}else {

		$select = $db->select()
		->from(array('p' => $this->_name), array('p.*'));
	}
		$select->joinLeft(array('pp'=>'staff'), 'pp.id = p.user',
						  array('pp.username'));
			   
		
	if(isset($params['imei']) && $params['imei']){
		$imei = explode("\r\n", $params['imei']);
		$select->where('p.imei_log IN (?)',$imei);
	}	
		$select->where('p.status_imei =?',1);

		if($limit)
		   $select->limitPage($page, $limit);

		$result = $db->fetchAll($select);

		if($limit)
		   $total = $db->fetchOne("select FOUND_ROWS()");
		return $result;
	}

}