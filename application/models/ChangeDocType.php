<?php
class Application_Model_ChangeDocType extends Zend_Db_Table_Abstract
{
	protected $_name = 'change_document_type';

    function get_cache(){
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

           // $select->where('p.del IS NULL OR p.del = ?', 0);

            $select->order(new Zend_Db_Expr('p.`id`'));
           
            $data = $db->fetchAll($select);

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['type_name'];
                }
            }
            $cache->save($result, $this->_name.'_cache', array(), null);
        }
        return $result;
    }
}
