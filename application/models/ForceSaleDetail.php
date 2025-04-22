<?php
class Application_Model_ForceSaleDetail extends Zend_Db_Table_Abstract
{
    protected $_name = 'force_sale_detail';

    function get_cache($id){
            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('fsd' => $this->_name),
                    array('fsd.*'));

            $select->joinLeft(array('g' => 'good'),'fsd.g_id = g.id',array('good_name'=>'g.desc'));
            $select->where('fsd.force_sale_id =?',$id);

            // $select->order(new Zend_Db_Expr('fsw`name` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select);

        return $data;
    }

    function forceSale($campaign_id){
         $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.*'));
        $select->join(array('g'=>'good'),'p.g_id=g.id',array('g.cat_id','g.color'));    
        if (isset($campaign_id) and $campaign_id)
            $select->where('p.force_sale_id = ?', $campaign_id);

       
        $result = $db->fetchAll($select);
       
        return $result;
    }
  //Add this file 
}



