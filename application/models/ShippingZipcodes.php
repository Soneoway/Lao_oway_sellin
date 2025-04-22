
<?php
class Application_Model_ShippingZipcodes extends Zend_Db_Table_Abstract
{
    protected $_name = 'shipping_zipcodes';

    function getZipCode($districts){
    	$db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array( 'p.*'));
        $select->where('district_code = ?',$districts);
       
        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
                foreach ($data as $item){
                   $result[] = array(
                'zip_id' => $item['zip_id'],
                'zipcode' => $item['zipcode'],
                );                        
                    
                }
            }
        return $result;
    }

    function getZipCodeAPI($districts){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array( 'p.*'));
        $select->where('district_code = ?',$districts);
        $select->where('zipcode NOT LIKE ?','%*%');
        $select->order('zipcode ASC');
       
        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
                foreach ($data as $item){
                   $result[] = array(
                'zip_id' => $item['zip_id'],
                'zipcode' => $item['zipcode'],
                );                        
                    
                }
            }
        return $result;
    }

    function get_data($item){
     
            $db = Zend_Registry::get('db');
            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.zipcode'));
            $select->where('zip_id = ?',$item);
            $data = $db->fetchOne($select);

        return $data;
    }
}
