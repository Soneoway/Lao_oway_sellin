<?php
class Application_Model_ChangeSalesImei extends Zend_Db_Table_Abstract
{
	protected $_name = 'change_sales_imei';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'total_qty' => 'COUNT(p.id)', 'p.*'));

        if (isset($params['imei']) and $params['imei'])
            $select->where('p.imei LIKE ?', '%'.$params['imei'].'%');

        $select->group('p.changed_sn');

        if (isset($params['sort']) and $params['sort']) {
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';


            $order_str = 'p.`'.$params['sort'] . '` ' . $desc;

            $select->order(new Zend_Db_Expr($order_str));
        }

        $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function getImeiChangeSalesImei($arrImei){

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.imei'));

        $select->where('p.status = ?',1);
        $select->where('p.imei IN (?)',$arrImei);
        $select->group('p.imei');
        // echo $select;die;

        return $db->fetchAll($select);
    }

}
