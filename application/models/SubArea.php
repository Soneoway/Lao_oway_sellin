<?php
class Application_Model_SubArea extends Zend_Db_Table_Abstract
{
	protected $_name = 'sub_area';
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


}