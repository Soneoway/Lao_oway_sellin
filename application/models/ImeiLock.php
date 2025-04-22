<?php

class Application_Model_ImeiLock extends Zend_Db_Table_Abstract
{
    protected $_name = 'imei_lock';

    function checkImeiLock($list_imei){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->where('p.imei_log in (?)',$list_imei);
        $select->where('p.status_imei = ?',1);

        $data = $db->fetchAll($select);

        return $data;
    }

}

