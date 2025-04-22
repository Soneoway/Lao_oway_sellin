<?php
class Application_Model_DeliveryOrderStatus extends Zend_Db_Table_Abstract {
    protected $_name = 'delivery_order_status';

    public function fetchPagination($page, $limit, &$total, $params)
    {
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.id', 'p.updated_by', 'p.status', 'p.updated_at_unix'));

        if (isset($params['delivery_order_id']) and $params['delivery_order_id'])
            $select->where('p.delivery_order_id = ?', $params['delivery_order_id']);

        // if ($limit)
            // $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
    
        // if ($limit)
            // $total = $db->fetchOne("select FOUND_ROWS()");
        
        return $result;
    }

    public function getStatus($delivery_order_id, $date)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => $this->_name), array('i.*'));

        $select->where('i.updated_at_unix <= ?', strtotime($date));

        $where = $select->getPart(Zend_Db_Select::WHERE);

        $sub_select = $db->select()
            ->from(array('i' => $this->_name), 
                array('maxdate' =>  new Zend_Db_Expr('MAX(i.updated_at_unix)')))
            ->where(implode(' ', $where));
        
        $select->join(array('md' => $sub_select), 'i.updated_at_unix=md.maxdate', array());

        $result = $db->fetchRow($select);
        return $result;
    }
}