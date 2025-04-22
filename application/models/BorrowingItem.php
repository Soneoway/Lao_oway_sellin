<?php

class Application_Model_BorrowingItem extends Zend_Db_Table_Abstract
{
    protected $_name = 'borrowing_item';

    function getBorrowingDetailsBySN($sn){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->where('p.sn = ?',$sn);

        $data = $db->fetchAll($select);

        return $data;
    }
}

