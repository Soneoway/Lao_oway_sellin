<?php
class Application_Model_DistributorGroupUser extends Zend_Db_Table_Abstract{
    protected $_name = 'distributor_group_user';
    
    function currentDistributorGroup($id){
        $db = Zend_Registry::get('db');
        $select = $db->select()
                     ->from(array('p'=>$this->_name),
                        array('*')
                     );

        $select->where('p.user_id = ?', $id);
        $select->where('p.status = ?', 1);

        $result = $db->fetchAll($select);
        return $result;
    }
    
    
}