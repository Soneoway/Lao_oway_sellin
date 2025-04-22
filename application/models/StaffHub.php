<?php
class Application_Model_StaffHub extends Zend_Db_Table_Abstract{
    protected $_name = 'staff_hub';

    public function isAllow($staff_id, $hub_id)
    {
        $cache = $this->get_cache();
        return isset($cache[ $staff_id ]) && is_array($cache[ $staff_id ]) && in_array($hub_id, $cache[ $staff_id ]) ? true : false;
    }

    public function get_cache()
    {
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

            $data = $db->fetchAll($select);

            $result = array();
            if ($data)
                foreach ($data as $item)
                    $result[$item['staff_id']][] = $item['hub_id'];

            $cache->save($result, $this->_name.'_cache', array(), null);
        }
        return $result;
    }
}