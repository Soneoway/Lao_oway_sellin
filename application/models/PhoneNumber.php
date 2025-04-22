<?php
class Application_Model_PhoneNumber extends Zend_Db_Table_Abstract{
    protected $_name = 'phone_number';
    
    function getPhone_number($phone_number,$sales_order){
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => $this->_name), array('i.*'));

        if($phone_number !=''){       
            $select->where('i.phone_number_sn = ?',$phone_number);
        }
        if($sales_order !=''){
            $select->where('i.sales_order = ?',$sales_order);
        }
        return $db->fetchRow($select);
    }

    


}