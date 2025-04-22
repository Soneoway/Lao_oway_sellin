<?php
class Application_Model_WarehouseType extends Zend_Db_Table_Abstract{
	protected $_name = 'warehouse_type'; 
	
	function get_cache(){
      
            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));
            $select->order('p.name_type ASC');
            $data = $db->fetchAll($select);
           
            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['name_type'];
                }
            }
            
       
        return $result;
    }
}