<?php
class Application_Model_PayGroup extends Zend_Db_Table_Abstract
{
    protected $_name = 'pay_group';

    public function getPaymentGroup($payment_no){

   	 	$db = Zend_Registry::get('db');

	    $select = $db->select()
	        ->from(array('p' => $this->_name),
	            array('p.*'));
	    $select->joinLeft(array('m'=>'market'),'m.sn=p.sale_order',array('m.sn','m.sn_ref'));
	    $select->joinLeft(array('d'=>'distributor'),'d.id=p.d_id',array('d.title'));
	     
	    $select->where('p.status = ?',1);
	    $select->where('p.payment_no = ?',$payment_no);

	    $select->group('m.sn');

	    $data = $db->fetchAll($select);
	    return $data;
	}

   public function checkPaymentGroup($array_sn){

   	 	$db = Zend_Registry::get('db');

	    $select = $db->select()
	        ->from(array('p' => $this->_name),
	            array('p.*'));
	     
	    $select->where('p.status = ?',1);
	    $select->where('p.sale_order in (?)',$array_sn);

	    $data = $db->fetchAll($select);
	    return $data;
	}

   public function getPaymentNoByPaymentID($payment_id){

   	 	$db = Zend_Registry::get('db');

	    $select = $db->select()
	        ->from(array('p' => $this->_name),
	            array('p.*'));
	    $select->joinLeft(array('m'=>'market'),'m.sn=p.sale_order',array('m.sn','m.sn_ref','m.d_id','m.pay_group', 'm.payment_no'));
	     
	    $select->where('p.status = ?',1);
	    $select->where('p.payment_id = ?',$payment_id);

	    $select->group('m.sn');
	    // echo $select;die;

	    $data = $db->fetchRow($select);
	    return $data;
	}

   public function getPaymentBalance($payment_no){

   	 	$db = Zend_Registry::get('db');

	    $select = $db->select()
	        ->from(array('p' => $this->_name),
	            array('p.*'));
	    $select->joinLeft(array('pgb'=>'pay_group_balance'),'pgb.payment_id=p.payment_id and pgb.status = 1',array('pgb_id' => 'pgb.id', 'pgb.total_amount', 'pgb.balance_total', 'use_total'));

	    $select->where('p.status = ?',1);
	    $select->where('p.payment_no = ?',$payment_no);

	    $data = $db->fetchRow($select);
	    return $data;
	}

	public function getPaymentIDByPaymentNo($payment_no){

   	 	$db = Zend_Registry::get('db');

	    $select = $db->select()
	        ->from(array('p' => $this->_name),
	            array('p.*'));
	     
	    $select->where('p.status = ?',1);
	    $select->where('p.payment_no = ?',$payment_no);

	    $select->group('p.payment_no');
	    // echo $select;die;

	    $data = $db->fetchRow($select);
	    return $data;
	}

}