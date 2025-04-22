<?php
class Application_Model_CheckmoneyPaymentorder extends Zend_Db_Table_Abstract
{
	protected $_name = 'checkmoney_paymentorder';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['d_id']) and intval($params['d_id']) > 0)
            $select->where('p.d_id = ?', $params['d_id']);

        if (isset($params['status']) and $params['status'] != '')
            $select->where('p.status = ?', $params['status']);

        $select->where('p.canceled IS NULL OR p.canceled !=1', null);

        if (isset($params['sort']) and $params['sort']) {
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';
            $order_str = 'p.`'.$params['sort'] . '` ' . $desc;
            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
}                                                      
