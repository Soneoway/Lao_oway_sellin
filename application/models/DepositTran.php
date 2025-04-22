<?php
class Application_Model_DepositTran extends Zend_Db_Table_Abstract
{
	protected $_name = 'deposit_tran';


	public function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $QPG = new Application_Model_PayGroup();

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.deposit_sn'),'*','from_deposit_no' => '(select deposit_sn from deposit temp_pg where temp_pg.deposit_sn = p.deposit_sn group by temp_pg.deposit_sn)','to_sales_no' => '(select deposit_sn from deposit temp_pg where temp_pg.deposit_sn = p.deposit_sn group by temp_pg.deposit_sn)'));

        
        if (isset($params['d_id']) && $params['d_id']) {
        	$select->where('p.distributor_id = ?', $params['d_id']);
        }else{
        	return [];
        }

        if (isset($params['deposit_sn']) && $params['deposit_sn']) {
        	$select->where('p.deposit_sn = ?', $params['deposit_sn']);
        }

        if (isset($params['to_sales_no']) && $params['to_sales_no']) {
        	$select->where('p.payment_tran_id = ?', $params['deposit_sn']);
        }


        if (isset($params['created_at_from']) and $params['created_at_from']){
                list( $day, $month, $year ) = explode('/', $params['created_at_from']);

                if (isset($day) and isset($month) and isset($year) )
                    $select->where('p.update_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
            }

            if (isset($params['created_at_to']) and $params['created_at_to']){
                list( $day, $month, $year ) = explode('/', $params['created_at_to']);

                if (isset($day) and isset($month) and isset($year) )
                    $select->where('p.update_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
            }


        if (isset($params['sort']) and $params['sort']) {
            $order_str = $collate = '';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str .= $params['sort'] . $collate . $desc;
            

            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

       //  echo $select;die;

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function getDeposit_By_SalesOrder($sales_order,$distributor_id)
    {
        try{
            $db = Zend_Registry::get('db');
             $select = $db->select()
                ->from(array('c' => 'deposit_tran'),array('c.deposit_sn','c.sales_order','c.use_discount'))
                ->joinLeft(array('cn'=>'deposit'), 'cn.deposit_sn = c.deposit_sn', array('cn.balance_total','cn.total_amount'))
               // ->where('c.distributor_id = ?', $distributor_id)
                ->where('c.sales_order = ?', $sales_order);
                //echo $select;die; 
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");

            
        } catch (Exception $e){

            return array(
                'code' => -1,
                'message' => $e->getMessage(),
            );
        }
        return $result;
    }  
    
}
