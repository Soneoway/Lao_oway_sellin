<?php
class Application_Model_WarehouseGroupUser extends Zend_Db_Table_Abstract{
    protected $_name = 'warehouse_group_user';
    
    function currentWarehouseGroupUserList($id){
        $db = Zend_Registry::get('db');
        $select = $db->select()
                     ->from(array('p'=>$this->_name),
                        array('user_id', 'warehouse_id')
                     );

        $select->where('p.user_id = ?', $id);
        $select->where('p.status = ?', 1);

        $result = $db->fetchALL($select);
        return $result;
    }
    
    
}