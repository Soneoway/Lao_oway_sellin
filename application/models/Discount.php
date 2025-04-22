<?php
class Application_Model_Discount extends Zend_Db_Table_Abstract
{
    protected $_name = 'good_discount';

   function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.good_id', 'p.name', 'p.discount'));

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('p.good_id IN (?)', $params['good_id']);

        if (isset($params['sort']) && $params['sort']) {
            if ($params['sort'] == 'name') {
                $select->join(array('g' => 'good'), 'g.id=p.good_id', array());

                $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';
                $order_str = 'g.`name` ' . $desc;

                $select->order(new Zend_Db_Expr($order_str));
            }
        }

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
     function Checkdiscount($good_id){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.*'));
            $select->join(array('g'=>'good'),'p.good_id=g.id',array('p_name'=>'g.name'));
            $select->where('p.good_id = ?', $good_id);

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

	function get_discount(){
        $db = Zend_Registry::get('db');
        $select = $db->select()
                     ->from(array('p' => $this->_name),array('p.*'))
                     ->group('p.discount');


        $result = $db->fetchAll($select);
        return $result;
    }
}