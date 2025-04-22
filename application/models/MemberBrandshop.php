<?php
class Application_Model_MemberBrandshop extends Zend_Db_Table_Abstract
{
	protected $_name = 'member_brandshop';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['code']) and $params['code'])
            $select->where('p.code LIKE ?', '%'.$params['code'].'%');

        if (isset($params['customer_name']) and $params['customer_name'])
            $select->where('p.customer_name LIKE ?', '%'.$params['customer_name'].'%');

        if (isset($params['phone_number']) and $params['phone_number'])
            $select->where('p.phone_number LIKE ?', '%'.$params['phone_number'].'%');

        if (isset($params['tax_number']) and $params['tax_number'])
            $select->where('p.tax_number LIKE ?', '%'.$params['tax_number'].'%');

        if (isset($params['tax_address']) and $params['tax_address'])
            $select->where('p.tax_address LIKE ?', '%'.$params['tax_address'].'%');

        if (isset($params['status']) and $params['status'])
            $select->where('p.status = ?', $params['status']);

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        
        if ($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");
        
        return $result;
    }

    function getMemberBrandshop($code){

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'));

        $select->where('p.status = ?',1);
        $select->where('p.code = ?',$code);
        // echo $select;die;

        return $db->fetchRow($select);
    }

}
