<?php
class Application_Model_LogCheckImeiWarehouse extends Zend_Db_Table_Abstract
{
    protected $_name = 'log_check_imei_warehouse';

    function getHistoryLog($params){

        $userStorage    = Zend_Auth::getInstance()->getStorage()->read();

        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.*'))
            ->joinLeft(array('w' => 'warehouse'),'p.warehouse_id = w.id',array('warehouse_name' => 'w.name'))
            ->joinLeft(array('g' => 'good'),'p.good_id = g.id'        ,array('product_name' => 'g.name'))
            ->joinLeft(array('c' => 'good_color'),'p.color_id = c.id'      ,array('color_name' => 'c.name'));

        if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID))){
            $select->joinLeft(array('s'=>'staff'),'s.id = p.at_by',array('fullname' => 'CONCAT(s.firstname, " ", s.lastname)'));
        }else{
            if (isset($params['account_id']) and $params['account_id'])
            $select->where('p.at_by = ?', $params['account_id']);
        }

        if (isset($params['warehouse_id']) and $params['warehouse_id'])
            $select->where('p.warehouse_id in (?)', $params['warehouse_id']);

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('p.good_id in (?)', $params['good_id']);

        if (isset($params['color_id']) and $params['color_id'])
            $select->where('p.color_id in (?)', $params['color_id']);

        if (isset($params['at_from']) and $params['at_from']){
            list( $day, $month, $year ) = explode('/', $params['at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.at_date >= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['to_from']) and $params['to_from']){
            list( $day, $month, $year ) = explode('/', $params['to_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.at_date <= ?', $year.'-'.$month.'-'.$day . ' 23:59:59');
        }

        // echo $select;die;
        $result = $db->fetchAll($select);
        return $result;
    }
}
