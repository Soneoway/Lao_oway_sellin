<?php
class Application_Model_Brand extends Zend_Db_Table_Abstract
{
	protected $_name = 'brand';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        // if (isset($params['name']) and $params['name'])
        //     $select->where('p.name LIKE ?', '%'.$params['name'].'%');

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function getBrand($good_id) {
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('b' => 'brand'),array('brand_name' => 'b.name'))
            ->joinLeft(array('g' => 'good'),'g.brand_id = b.id',array())
            ->where('g.id =?',$good_id);

        $result = $db->fetchAll($select);
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

}                                                      
