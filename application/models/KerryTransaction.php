<?php
class Application_Model_KerryTransaction extends Zend_Db_Table_Abstract{

	protected $_name = 'kerry_transaction';

	function getKerryTransaction(){

		$db = Zend_Registry::get('db');

		//0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
		//0=null,1=add
		$sql = "select * from kerry_transaction where type = 1 and status not in (3,7) and rollback_status not in (4,6) limit 50;";

		$result = $db->fetchAll($sql, [$krc_id]);

		return $result;

	}

}