
<?php
class Application_Model_ShippingAmphures extends Zend_Db_Table_Abstract
{
    protected $_name = 'shipping_amphures';

    function getAmphures($province){
    	$db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array( 'p.*'));
        $select->where('provice_id = ?',$province);
       
        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
                foreach ($data as $item){
                   $result[] = array(
                'amphur_id' => trim($item['amphure_id']),
                'amphur_name' => trim($item['amphure_name'])
                );                        
                    
                }
            }
        return $result;
    }

    function getAmphuresAPI($province){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array( 'p.*'));
        $select->where('provice_id = ?',$province);
        $select->where('amphure_name NOT LIKE ?','%*%');
        $select->order('amphure_name ASC');
       
        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
                foreach ($data as $item){
                   $result[] = array(
                'amphur_id' => trim($item['amphure_id']),
                'amphur_name' => trim($item['amphure_name'])
                );                        
                    
                }
            }
        return $result;
    }

      function get_data($item){
     
            $db = Zend_Registry::get('db');
            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.amphure_name'));
            $select->where('amphure_id = ?',$item);
            $data = $db->fetchOne($select);

        return $data;
    }
}
