<?php
class Application_Model_BvgAccessories extends Zend_Db_Table_Abstract{
	protected $_name = 'bvg_accessories';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select_fields = array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'), 'total_qty' => 'SUM(p.number)', 'total_price' => 'SUM(p.total)', 'good_id' , 'joint_id' );


        $select = $db->select()
            ->from(array('p' => $this->_name),
                $select_fields);

        if(isset($params['good_id']) and $params['good_id'])
        {
            $select->where('good_id = ?' , $params['good_id']);
        }

        if(isset($params['joint_id']) and $params['joint_id'])
        {
            $select->where('joint_id = ?' , $params['joint_id']);
        }

        if(isset($params['not_approve']) and $params['not_approve'])
        {
            $select->where('bvg_payment_confirmed_at is null' , null);
        }

        if(isset($params['bvg_market_product_id']) and $params['bvg_market_product_id'])
        {
            $select->where('bvg_market_product_id = ?' , $params['bvg_market_product_id']);
        }

        $select->group('good_id');
        $select->group('joint_id');

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchRow($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
}