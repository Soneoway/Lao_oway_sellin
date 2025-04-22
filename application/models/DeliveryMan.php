<?php
class Application_Model_DeliveryMan extends Zend_Db_Table_Abstract
{
    protected $_name = 'delivery_man';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['name']) and $params['name'])
            $select->where('p.name LIKE ? OR p.note LIKE ?', '%'.$params['name'].'%');

        $select->order('p.created_at DESC');

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        
        if ($limit)
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
                    array('p.*'))
                ->where('p.status IS NOT NULL AND p.status = 1');

            $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select, 'name');

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
