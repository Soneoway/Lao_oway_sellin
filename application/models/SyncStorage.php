<?php
class Application_Model_SyncStorage extends Zend_Db_Table_Abstract
{
    protected $_name = 'sync_storage';
	public function getsync_storage($params){
  
 	    $db = Zend_Registry::get('db');
        $select = $db->select()
               ->from(array('p' => $this->_name), array('p.*'));

        if(isset($params['warehouse_id']) && $params['warehouse_id']){
            $select->where('p.warehouse_id =?', $params['warehouse_id']);
        }else{
            return [];
        }
        	
        if(isset($params['cat_id']) && $params['cat_id']){
            $select->where('p.cat_id =?', $params['cat_id']);
        }else{
        	return [];
        }

        if(isset($params['good_id']) && $params['good_id']){
            $select->where('p.good_id =?', $params['good_id']);
        }else{
        	return [];
        }
        
        if(isset($params['good_color_id']) && $params['good_color_id']){
            $select->where('p.good_color_id = ?', $params['good_color_id']);
        }else{
            return [];
        }

        $select->where('p.status =?',1);
        // echo $select; die;
        
		return $db->fetchRow($select);

    }

}