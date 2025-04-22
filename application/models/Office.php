<?php
class Application_Model_Office extends Zend_Db_Table_Abstract
{
    protected $_name = 'office';

    function fetchPagination($page, $limit, &$total, $params)
    {
        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name), array(new Zend_Db_Expr
                ('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['name']) and $params['name'])
            $select->where('p.name LIKE ?', '%' . $params['name'] . '%');

        $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function get_cache()
    {
        $cache = Zend_Registry::get('cache');
        $result = $cache->load($this->_name . '_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()->from(array('p' => $this->_name), array('p.*'));

            $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select, 'title');

            $result = array();
            if ($data) {
                foreach ($data as $item) {
                    $result[$item['id']] = $item['title'];
                }
            }
            $cache->save($result, $this->_name . '_cache', array(), null);
        }
        return $result;
    }

    function get_all_cache()
    {
        $cache = Zend_Registry::get('cache');
        $result = $cache->load($this->_name . '_all_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()->from(array('p' => $this->_name), array('p.*'));

            $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select, 'title');

            $result = array();
            if ($data) {
                foreach ($data as $item) {
                    $result[$item['id']] = array(
                        'name'         => $item['title'],
                        'address'      => $item['name'],
                        'contact'      => $item['customer'],
                        'phone_number' => $item['tel'],
                        'district'     => $item['district_id'],
                    );
                }
            }
            $cache->save($result, $this->_name . '_all_cache', array(), null);
        }
        return $result;
    }

    function get_warehouse($name)
    {
        $db = Zend_Registry::get('db');
        $name = trim($name);
        $select = $db->select()->from(array('p' => 'office'), array('warehouse' =>
                'warehouse_id'))->where('p.title LIKE ?', '%'.$name.'%');
        $result = $db->fetchOne($select);
        return $result;
    }
}
