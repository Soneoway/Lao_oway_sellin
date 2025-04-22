<?php

class Application_Model_BuyReturnCondition extends Zend_Db_Table_Abstract
{
    protected $_name = 'buy_return_condition';

    function getConditionByReturn(){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->where('p.status = ?',1);

        $data = $db->fetchAll($select);

        return $data;
    }
}

