<?php
class Application_Model_Region extends Zend_Db_Table_Abstract
{
	protected $_name = 'region';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        // if (isset($params['name']) and $params['name'])
        //     $select->where('p.name LIKE ?', '%'.$params['name'].'%');

        if (isset($params['area_id']) && $params['area_id']) {
        	$select->where('p.area_id = ?', $params['area_id']);
        }

        if ( $limit && ! ( isset( $params['area_id'] ) && $params['area_id'] ) )
            $select->limitPage($page, $limit);

        $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

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

    function get_all_cache(){
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_all_cache');

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
                    $result[$item['id']] = array(
                        'name' => $item['name'],
                        'area_id' => $item['area_id'],
                        );
                }
            }
            $cache->save($result, $this->_name.'_all_cache', array(), null);
        }
        return $result;
    }

    function get_cache2(){
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'2_cache');

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
                    $result[$item['id']] = $item;
                }
            }
            $cache->save($result, $this->_name.'2_cache', array(), null);
        }
        return $result;
    }

    function get_by_area_cache($area_id){
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_by_area_'.$area_id.'cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

            $select->where('p.area_id = ?', $area_id);

            $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select);

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['name'];
                }
            }
            $cache->save($result, $this->_name.'_by_area_'.$area_id.'cache', array(), null);
        }
        return $result;
    }
}                                                      
