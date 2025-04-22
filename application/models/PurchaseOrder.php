<?php
class Application_Model_PurchaseOrder extends Zend_Db_Table_Abstract
{
    protected $_name = 'purchase_order';

    function fetchPaginationWarrantyPO($params, &$total){
        $db = Zend_Registry::get('db');
    
         $select = $db->select()
            ->from(array('po' => $this->_name),
            	array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS po.id'), 'po.cat_id','po.good_id','po.good_color','po.sn','po.price','po.mysql_time','po.type','po.num','po.sn_ref'));           
        
        
        $select->where('po.type  = ?', 2);
        $select->where('po.warehouse_id  = ?', 98);
        $select->where('po.mysql_time  > ?','2017-05-01 00 00 00');
        $select->where('po.mysql_time is not null');

        if (isset($params['po']) and $params['po'])
            $select->where('po.sn LIKE ? or po.sn_ref LIKE ?', '%'.$params['po'].'%');

         //date
        if (isset($params['created_at_from_po']) and $params['created_at_from_po']){
            list( $day, $month, $year ) = explode('/', $params['created_at_from_po']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(po.mysql_time) >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['created_at_to_po']) and $params['created_at_to_po']){
            list( $day, $month, $year ) = explode('/', $params['created_at_to_po']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(po.mysql_time) <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        $result = $db->fetchAll($select);

        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }    
}
