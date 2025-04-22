<?php
class Application_Model_PayGroupBank extends Zend_Db_Table_Abstract
{
    protected $_name = 'pay_group_bank';

    public function getPaymentGroupBank($payment_id){

   	 	$db = Zend_Registry::get('db');

	    $select = $db->select()
	        ->from(array('p' => $this->_name),
	            array('p.*'));
	    $select->joinLeft(array('b'=>'bank'),'b.id=p.bank',array('b.name'));
	    
	    $select->where('p.status = ?',1);
	    $select->where('p.payment_id = ?',$payment_id);

	    $data = $db->fetchAll($select);
	    return $data;
	}

}