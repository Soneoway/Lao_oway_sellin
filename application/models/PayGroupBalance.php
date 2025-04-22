<?php
class Application_Model_PayGroupBalance extends Zend_Db_Table_Abstract
{
    protected $_name = 'pay_group_balance';

    public function getUsePaygroup($d_id){

   	 	$db = Zend_Registry::get('db');

	    $select = $db->select()
	        ->from(array('p' => $this->_name),
	            array('p.*'));
	    $select->joinLeft(array('pg'=>'pay_group'),'pg.payment_id=p.payment_id and pg.status=1',array('pg.payment_no'));
	    $select->joinLeft(array('m'=>'market'),'m.payment_no=pg.payment_no',array());
	     
	    $select->where('m.finance_confirm_date is not null');
	    $select->where('m.finance_confirm_id is not null');
	    $select->where('p.confirmed_date is not null');
	    $select->where('p.confirmed_by is not null');

	    $select->where('p.distributor_id = ?',$d_id);
	    $select->where('p.status = ?',1);
	    $select->where('p.balance_total >= ?',1);

	    $select->group('p.payment_id');
	    
	    $data = $db->fetchAll($select);
	    return $data;
   }

   public function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.distributor_id','total_amount' => 'sum(p.total_amount)', 'use_total' => 'sum(p.use_total)', 'balance_total' => 'sum(p.balance_total)'));

        $select->joinLeft(array('d'=>'distributor'),'d.id=p.distributor_id',array('d.title'));
	     
	    $select->where('p.status = ?',1);
        $select->where('p.total_amount >= ?',1);

        if (isset($params['d_id']) && $params['d_id']) {
        	$select->where('p.distributor_id = ?', $params['d_id']);
        }

        if (isset($params['distributor_name']) && $params['distributor_name']) {
        	$select->where('d.title like ?', '%'.$params['distributor_name'].'%');
        }

	    $select->where('d.rank <> ?',10);
        $select->group('p.distributor_id');


        if (isset($params['sort']) and $params['sort']) {
            $order_str = $collate = '';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str .= $params['sort'] . $collate . $desc;
            

            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

        // echo $select;die;

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function getDataPayGroupBalance($d_id, $export = false){

    	$db = Zend_Registry::get('db');

	    $select = $db->select()
	        ->from(array('p' => $this->_name),
	            array('p.*'));
	    $select->joinLeft(array('pg'=>'pay_group'),'pg.payment_id=p.payment_id and pg.status=1',array('pg.payment_no'));

	    $select->joinLeft(array('m'=>'market'),'m.payment_no=pg.payment_no',array());
	     
	    $select->where('m.finance_confirm_date is not null');
	    $select->where('m.finance_confirm_id is not null');

	    $select->where('p.distributor_id = ?',$d_id);
	    $select->where('p.status = ?',1);

	    if(!$export){
	    	$select->where('p.balance_total >= ?',1);
	    }

	    $select->group(['pg.payment_id','p.payment_id']);

	    // echo $select;die;
	    
	    $data = $db->fetchAll($select);
	    return $data;
    }

    public function checkPaymentGroupBalance($d_id,$payment_id){

    	$db = Zend_Registry::get('db');

	    $select = $db->select()
	        ->from(array('p' => $this->_name),
	            array('p.*'));

	    $select->joinLeft(array('pg'=>'pay_group'),'pg.payment_id=p.payment_id and pg.status=1',array('pg.payment_no'));
	    $select->joinLeft(array('m'=>'market'),'m.payment_no=pg.payment_no',array());
	     
	    $select->where('m.finance_confirm_date is not null');
	    $select->where('m.finance_confirm_id is not null');
	    $select->where('p.confirmed_date is not null');
	    $select->where('p.confirmed_by is not null');

	    $select->group('p.payment_id');
	     
	    $select->where('p.status = ?',1);
	    $select->where('p.balance_total >= ?',1);
	    $select->where('p.distributor_id = ?',$d_id);
	    $select->where('p.payment_id = ?',$payment_id);

	    // echo $select;die;
	    
	    $data = $db->fetchRow($select);
	    return $data;
    }

}