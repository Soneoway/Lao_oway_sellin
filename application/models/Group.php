<?php
class Application_Model_Group extends Zend_Db_Table_Abstract
{
	protected $_name = 'group';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['name']) and $params['name'])
            $select->where('p.name LIKE ?', '%'.$params['name'].'%');

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function get_cache(){
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

            $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select);

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['name'];
                }
            }
            $cache->save($result, $this->_name.'_cache', array(), null);
        }
        return $result;
    }

    function get_salesman_id_cache(){
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_salesman_id_cache');

        if ($result === false) {

            $where = $this->getAdapter()->quoteInto('`name` = ?', 'SALES');

            $data = $this->fetchRow($where);

            $result = null;
            if ($data){
                $result = $data['id'];
            }
            $cache->save($result, $this->_name.'_salesman_id_cache', array(), null);
        }
        return $result;
    }

    function getGroup(){
        $db = Zend_Registry::get('db');
        $select = $db->select()
                     ->from(array('p'=>$this->_name),
                        array('*')
                     );

        $select->order('p.name asc');

        $result = $db->fetchAll($select);
        return $result;
    }
}                                                      
