<?php
class Application_Model_ClientCode extends Zend_Db_Table_Abstract
{
	protected $_name = 'client_code';

	function getcode(){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$result = $db->fetchAll($select);
		return $result;
	}


}