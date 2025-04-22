<?php
class Application_Model_ShippingProvinces extends Zend_Db_Table_Abstract
{
    protected $_name = 'shipping_provinces';

    function getProvince(){
        $db = Zend_Registry::get('db'); 

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array( 'p.*'));
        $data = $db->fetchAll($select);
        $result = array();
        if ($data){
                foreach ($data as $item){
                   $result[$item['provice_id']] = trim($item['provice_name']);                        
                    
                }
            }
        
        return $result;
    }

    function getProvinceAPI(){
        $db = Zend_Registry::get('db'); 

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array( 'p.*'));
        $select->where('provice_name NOT LIKE ?','%*%');
        $select->where('provice_id NOT IN (?)',['78','79','80']);
        $select->order('provice_name ASC');

        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
                foreach ($data as $item){
                   $result[$item['provice_id']] = trim($item['provice_name']);                        
                    
                }
            }
        
        return $result;
    }

    function get_data($item){
     
            $db = Zend_Registry::get('db');
            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.provice_name'));
            $select->where('provice_id = ?',$item);
            $data = $db->fetchOne($select);

        return $data;
    }
}                                                      
