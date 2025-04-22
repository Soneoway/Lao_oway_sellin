<?php
class Application_Model_ForceSaleDistributor extends Zend_Db_Table_Abstract
{
    protected $_name = 'force_sale_distributor';

    function get_cache($id){
            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('fsd' => $this->_name),
                    array('fsd.*'));

            $select->joinLeft(array('d' => 'distributor'),'fsd.d_id = d.id',array('d_name'=>'d.title'));
            $select->where('fsd.force_sale_id =?',$id);

            // $select->order(new Zend_Db_Expr('fsw`name` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select);

        return $data;
    }

    function get_id($key){
            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('fsd' => $this->_name),
                    array('fsd.d_id'));

            // $select->joinLeft(array('d' => 'distributor'),'fsd.d_id = d.id',array('d_name'=>'d.title'));
            $select->where('fsd.force_sale_id =?',$key);
// echo $select;die;
            // $select->order(new Zend_Db_Expr('fsw`name` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select);

        return $data;
    }

    function delete_d_id($key){
            $db = Zend_Registry::get('db');

            $db->delete($this->_name, array('force_sale_id = ?' => $key,'d_id = ?' => $id));

            // $data = $db->fetchAll($select);

        // return $data;
    }
}
