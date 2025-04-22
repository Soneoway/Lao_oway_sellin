<?php
class Application_Model_InternalOrder extends Zend_Db_Table_Abstract{
    protected $_name = 'internal_order';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['sn']) and $params['sn'])
            $select->where('p.sn LIKE ?', '%'.$params['sn'].'%');

        if (isset($params['order_name']) and $params['order_name'])
            $select->where('p.order_name LIKE ?', '%'.$params['order_name'].'%');

        if (isset($params['transport_date_from']) and $params['transport_date_from'])
            $select->where('p.transport_date >= ?', date_create_from_format("d/m/Y", $params['transport_date_from'])->format('Y-m-d'));

        if (isset($params['transport_date_to']) and $params['transport_date_to'])
            $select->where('p.transport_date <= ?', date_create_from_format("d/m/Y", $params['transport_date_to'])->format('Y-m-d'));

        if(isset($params['invoice_prefix']) and $params['invoice_prefix'])
            $select->where('p.invoice_prefix = ?' , $params['invoice_prefix']);

        if(isset($params['service_from']) and $params['service_from'])
            $select->where('p.from_warehouse = ?' , $params['service_from']);

        if(isset($params['service_to']) and $params['service_to'])
            $select->where('p.to_warehouse = ?' , $params['service_to']);

        if(isset($params['invoice_number']) and $params['invoice_number'])
            $select->where('p.invoice_number LIKE ?', '%'.$params['invoice_number'].'%');

        $select->group('p.sn');

        $select->order('p.created_at DESC');

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

}
