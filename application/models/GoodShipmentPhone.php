<?php
class Application_Model_GoodShipmentPhone extends Zend_Db_Table_Abstract{
    protected $_name = 'good_shipment_phone';
    
    function getShipmentPhone($id){
        $db = Zend_Registry::get('db');
        $select = $db->select()
                     ->from(array('a'=>$this->_name),
                        array('a.id','b.name','a.price','id_good'=>'b.id','type'=>'a.type')
                     )
                     ->joinLeft(array('b'=>'good'),'a.good_id = b.id',array())
                     ->where('a.good_shipment_id = ?', $id)
                     ->order(array('b.name DESC'));
        //print_r($select);die;             
        $result = $db->fetchAll($select);
        return $result;
    }
}
