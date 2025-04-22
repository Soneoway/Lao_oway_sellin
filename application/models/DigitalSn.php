<?php
class Application_Model_DigitalSn extends Zend_Db_Table_Abstract
{
    protected $_name = 'digital_sn';


    function fetchStorageImeiDigital($page, $limit, &$total, $params){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        error_reporting(~E_ALL);
        ini_set("display_error", '0');
        
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('i' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS i.id'), 'i.*'));
            
            $select->where('i.sales_sn is null'); 
        if (isset($params['warehouse_id']) and $params['warehouse_id'])
            $select->where('i.warehouse_id = ?',$params['warehouse_id']);
        if (isset($params['good_id']) and $params['good_id'])
            $select->where('i.good_id = ?', $params['good_id']);

        if (isset($params['good_color_id']) and $params['good_color_id'])
            $select->where('i.good_color = ?', $params['good_color_id']);

        $select->where('i.out_date is null', null);
        // $select->where('i.status <> ?', '4');

        if ($limit)
            $select->limitPage($page, $limit);
       
        $result = $db->fetchAll($select);
        
        
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
}