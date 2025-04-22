<?php
class Application_Model_ForceSaleWarehouse extends Zend_Db_Table_Abstract
{
    protected $_name = 'force_sale_warehouse';

     function get_cache($id){
            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('fsw' => $this->_name),
                    array('fsw.*'));

            $select->joinLeft(array('w' => 'warehouse'),'fsw.w_id = w.id',array('name' => 'w.name'));
            $select->where('fsw.force_sale_id =?',$id);

            // $select->order(new Zend_Db_Expr('fsw`name` COLLATE utf8_unicode_ci'));
            $data = $db->fetchAll($select);

        return $data;
    }
}
