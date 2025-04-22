<?php
class Application_Model_MapAddressWarehouse extends Zend_Db_Table_Abstract
{
	protected $_name = 'map_address_warehouse';
    
    function getMapAddressByWarehouse($warehouse_id){
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'));
        $select->where('p.warehouse_id = ?',$warehouse_id);
        $select->where('p.status = ?',1);
        // echo $select;die;
        return $db->fetchAll($select);
    }
    
}
