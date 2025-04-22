<?php
class Application_Model_DigitalSnReturn extends Zend_Db_Table_Abstract
{
	protected $_name = 'digital_sn_return';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['imei_sn']) and $params['imei_sn'])
            $select->where('p.imei_sn LIKE ?', '%'.$params['imei_sn'].'%');

        if (isset($params['return_sn']) and $params['return_sn'])
            $select->where('p.return_sn LIKE ?', '%'.$params['return_sn'].'%');

        $select->where('p.back_sale = ?', 0);

        $select->where('p.back_warehouse_at is not null', null);

        $order_str = $collate = '';

        if (isset($params['sort']) and $params['sort']) {
            $collate = '';


            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str .= $params['sort'] . $collate . $desc;

            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
}
