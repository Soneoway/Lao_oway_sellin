<?php
class Application_Model_PayGroupTran extends Zend_Db_Table_Abstract
{
    protected $_name = 'pay_group_tran';

    public function getUsePaymentGroupByPaymentID($payment_id){

   	 	$db = Zend_Registry::get('db');

	    $select = $db->select()
	        ->from(array('p' => $this->_name),
	            array('p.*'));
	    $select->joinLeft(array('pg'=>'pay_group'),'pg.payment_id=p.payment_id and pg.status=1',array('pg.payment_no'));
        $select->joinLeft(array('pgbal'=>'pay_group_balance'),'pgbal.payment_id=p.payment_id and pgbal.status=1',array('pg.payment_no'));
        $select->joinLeft(array('m'=>'market'),'m.payment_no=pg.payment_no',array());

        $select->where('m.finance_confirm_date is not null');
        $select->where('m.finance_confirm_id is not null');
        $select->where('pgbal.confirmed_date is not null');
        $select->where('pgbal.confirmed_by is not null');
	    
	    $select->where('p.status = ?',1);
	    $select->where('p.pay_type = ?',0);
	    $select->where('p.payment_tran_id = ?',$payment_id);

	    $select->group('p.payment_id');

	    // echo $select;die;

	    $data = $db->fetchAll($select);
	    return $data;
	}

	public function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $QPG = new Application_Model_PayGroup();

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'),'*','from_payment_no' => '(select payment_no from pay_group temp_pg where temp_pg.payment_id = p.payment_id group by temp_pg.payment_id)','to_payment_no' => '(select payment_no from pay_group temp_pg where temp_pg.payment_id = p.payment_tran_id group by temp_pg.payment_id)'));

        $select->where('p.status = ?',1);
        
        if (isset($params['d_id']) && $params['d_id']) {
        	$select->where('p.distributor_id = ?', $params['d_id']);
        }else{
        	return [];
        }

        if (isset($params['from_payment_no']) && $params['from_payment_no']) {
        	$getPaymentNo = $QPG->getPaymentGroup($params['from_payment_no']);
        	$select->where('p.payment_id = ?', $getPaymentNo[0]['payment_id']);
        }

        if (isset($params['to_payment_no']) && $params['to_payment_no']) {
        	$getPaymentNo = $QPG->getPaymentGroup($params['to_payment_no']);
        	$select->where('p.payment_tran_id = ?', $getPaymentNo[0]['payment_id']);
        }


        if (isset($params['created_at_from']) and $params['created_at_from']){
                list( $day, $month, $year ) = explode('/', $params['created_at_from']);

                if (isset($day) and isset($month) and isset($year) )
                    $select->where('p.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
            }

            if (isset($params['created_at_to']) and $params['created_at_to']){
                list( $day, $month, $year ) = explode('/', $params['created_at_to']);

                if (isset($day) and isset($month) and isset($year) )
                    $select->where('p.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
            }


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

}