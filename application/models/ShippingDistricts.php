
<?php
class Application_Model_ShippingDistricts extends Zend_Db_Table_Abstract
{
    protected $_name = 'shipping_districts';

    function getDistricts($amphures){
    	$db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array( 'p.*'));
        $select->where('amphur_id = ?',$amphures);
       
        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
                foreach ($data as $item){
                   $result[] = array(
                'district_code' => trim($item['district_code']),
                'district_name' => trim($item['district_name'])
                );                        
                    
                }
            }
        return $result;
    }

    function getDistrictsAPI($amphures){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array( 'p.*'));
        $select->where('amphur_id = ?',$amphures);
        $select->where('district_name NOT LIKE ?','%*%');
        $select->order('district_name ASC');

        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
                foreach ($data as $item){
                   $result[] = array(
                'district_code' => trim($item['district_code']),
                'district_name' => trim($item['district_name'])
                );                        
                    
                }
            }
        return $result;
    }

     function get_data($item){
     
            $db = Zend_Registry::get('db');
            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.district_name'));
            $select->where('district_code = ?',$item);
            $data = $db->fetchOne($select);

        return $data;
    }
}
