<?php
class Application_Model_GoodSalePhone extends Zend_Db_Table_Abstract{
    protected $_name = 'good_sale_phone';
    
    function getAllSalePhone(){
        $db = Zend_Registry::get('db');
        $select = $db->select()
                     ->from(array('a'=>$this->_name),
                        array('a.good_id','b.name','GROUP_CONCAT(a.good_sale_id) as good_sale_id')
                     )
                     ->joinLeft(array('b'=>'good'),'a.good_id = b.id',array())
                     ->group('b.id');
        $result = $db->fetchAll($select);
        return $result;
    }
    
    function checkSalePhone($good_id,$sale_off_percent){
        return 1;
        $db = Zend_Registry::get('db');
        $select = $db->select()
                     ->from(array('a'=>$this->_name),
                        array('a.id','b.sale')
                     )
                     ->join(array('b'=>'good_sale'),'a.good_sale_id = b.id',array())
                     ->where('b.sale = ?', $sale_off_percent)
                     ->where('a.good_id = ?', $good_id);
        $result = $db->fetchRow($select);
        if($result)
            return 1;
        else
            return 0;
    }
    
    
}