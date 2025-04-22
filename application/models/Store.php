<?php
class Application_Model_Store extends Zend_Db_Table_Abstract
{
	protected $_name = 'store';
	protected $_schema = HR_DB;


	function get_cache()
    {
        $cache = Zend_Registry::get('cache');
        $result = $cache->load($this->_schema.'_'.$this->_name . '_cache');

        if ($result === false) {
            $where = $this->getAdapter()->quoteInto('status IN (?)', array(1,2,3));
            $data = $this->fetchAll($where, 'name');

            $result = array();
            if ($data) {
                foreach ($data as $item) {
                    $result[$item->id] = $item->name;
                }
            }
            $cache->save($result, $this->_schema.'_'.$this->_name . '_cache', array(), null);
        }
        return $result;
    }

    function get_cache2(){
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_schema.'_'.$this->_name . '_2_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
            ->from(array('p' => $this->_schema.'.'.$this->_name), array('p.*'));
            $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select);

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = array(
                        'store_id'          => $item['id'],
                        'store_code'        => $item['store_code'],
                        'store_name'        => $item['name'],
                        'leader'            => $item['leader'],
                        'shipping_address'  => $item['shipping_address'],
                        'phone_number'      => $item['phone_number']
                    );
                }
            }
            $cache->save($result, $this->_schema.'_'.$this->_name . '_2_cache', array(), null);
        }
        return $result;
    }


}