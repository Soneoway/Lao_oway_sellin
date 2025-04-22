<?php
class Application_Model_ImeiMissing extends Zend_Db_Table_Abstract{
	protected $_name = 'imei_missing';

	function getImeiMissingByRQID($id){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.imei_sn'));

        $select->where('p.rq_id = ?',$id);

        $data = $db->fetchAll($select);

        return $data;

    }
   
}