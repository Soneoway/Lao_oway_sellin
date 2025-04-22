<?php
class Application_Model_KerryCompanyCondition extends Zend_Db_Table_Abstract{

	protected $_name = 'kerry_company_condition';

	function getKerryCondition($krc_id){

		$db = Zend_Registry::get('db');

		$sql = "select provice_id from kerry_company_condition where krc_id = ? and status = 1;";

		$result = $db->fetchAll($sql, [$krc_id]);

		return $result;

	}

}