<?php
class Application_Model_DistributorGroup extends Zend_Db_Table_Abstract{
    protected $_name = 'distributor_group';
    
    function getDistributorGroup(){
        $db = Zend_Registry::get('db');
        $select = $db->select()
                     ->from(array('p'=>$this->_name),
                        array('*')
                     );

        $select->where('p.status = ?', 1);

        $select->order('p.group_name asc');

        $result = $db->fetchAll($select);
        return $result;
    }

    function getDistributorGroupType($id){
        $db = Zend_Registry::get('db');
        $select = $db->select()
                     ->from(array('p'=>$this->_name),
                        array('group_type_id', 'group_name')
                     );

        $select->where('p.group_id = ?', $id);
        $select->where('p.status = ?', 1);

        $result = $db->fetchRow($select);
        return $result;
    }
    
    
}