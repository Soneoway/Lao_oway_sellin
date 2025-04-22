<?php
class Application_Model_JointCircular extends Zend_Db_Table_Abstract
{
    protected $_name = 'joint_circular';

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

        if ($result == false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()->from(array('p' => $this->_name), array('p.*'));
            $select->where('type = 1 ', '');

            $select->order(new Zend_Db_Expr('p.`id` ASC'));
           // echo($select);die;
            $data = $db->fetchAll($select, 'name');

            $result = array();
            if ($data) {
                foreach ($data as $item) {
                    $result[$item['id']] = $item['name'];
                }
            }
            $cache->save($result, $this->_name . '_cache', array(), null);
            
        }
       // print_r($result);
        return $result;
    }

    function get_cache2()
    {
        $cache = Zend_Registry::get('cache');
        $result = $cache->load($this->_name . '_cache_2');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()->from(array('p' => $this->_name), array('p.*'));
            $select->where('type in (?) ', array(DISCOUNT_TYPE_CK , DISCOUNT_TYPE_DIAMOND));

            $select->order(new Zend_Db_Expr('p.`id` ASC'));

            $data = $db->fetchAll($select, 'name');

            $result = array();
            if ($data) {
                foreach ($data as $item) {
                    $result[$item['id']] = $item['name'];
                }
            }
            $cache->save($result, $this->_name . '_cache_2', array(), null);
        }
        return $result;
    }
}
