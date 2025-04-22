<?php

class Application_Model_DistributorBlackList extends Zend_Db_Table_Abstract
{
    protected $_name = 'distributor_black_list';

    function check_black_list($type,$d_id){
        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p'=>$this->_name),array('*'));

        $select->where('p.type = ? or p.type = 10', $type);
        $select->where('p.d_id = ?', $d_id);
		// echo $select;die;
        $result = $db->fetchRow($select);
        return $result;
    }
}
