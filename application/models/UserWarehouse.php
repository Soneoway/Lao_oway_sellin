<?php
class Application_Model_UserWarehouse extends Zend_Db_Table_Abstract
{
	protected $_name = 'user_warehouse';

	public function getProvideWarehouse($user_id){

    	$db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array( 'p.warehouse_id'));
        $select->where('p.user_id = ?',$user_id);
       
        return $db->fetchRow($select);
    }

}                                                      
