<?php
class Application_Model_KerryTransactionRollback extends Zend_Db_Table_Abstract{

	protected $_name = 'kerry_transaction_rollback';

	function getRollBack(){

		$db = Zend_Registry::get('db');

		//0=null,1=error1,2=error2,3=error3,4=hold,5=rollback,6=notrollback,7=done
		$sql = "select id,sn,data,status,remark from kerry_transaction_rollback where status not in(3,7) limit 10;";

		$result = $db->fetchAll($sql);

		return $result;

	}

}