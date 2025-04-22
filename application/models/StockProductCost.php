<?php
class Application_Model_StockProductCost extends Zend_Db_Table_Abstract{
	protected $_name = 'stock_product_cost';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['name']) and $params['name'])
            $select->where('p.name LIKE ?', '%'.$params['name'].'%');

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function checkData($good_code,$good_color,$cost_date){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name),'p.*');
        $select->where('p.good_code = ?', $good_code);
        $select->where('p.good_color = ?', $good_color);
        $select->where('p.cost_date = ?', $cost_date);
        $result = $db->fetchAll($select);
        return $result;
    }
   
}