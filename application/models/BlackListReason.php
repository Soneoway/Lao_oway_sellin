<?php

class Application_Model_BlackListReason extends Zend_Db_Table_Abstract
{
    protected $_name = 'black_list_reason';


    function  get_cache()
    {
       
            $db = Zend_Registry::get('db');
            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

            $select->order(new Zend_Db_Expr('p.`id`'));

            $data = $db->fetchAll($select);

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['reason'];      
                }
            
            }
        return $result;
   }

}

