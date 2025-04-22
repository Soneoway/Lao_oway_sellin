<?php
/**
*
*/
class Application_Model_GoodMapping extends Zend_Db_Table_Abstract
{
    protected $_name = 'good_mapping';

    function fetchPagination($page, $limit, &$total, $params) {
        $db = Zend_Registry::get('db');

        if (isset($params['id'])) $get = array('c.code');
        else $get = array();

        $select = $db->select()
            ->distinct()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.good_id', 'p.color_id'))
            ->joinLeft(array('c' => 'good_mapping_code'), 'p.id=c.good_mapping_id', $get);

        if (isset($params['id'])) {
            if (is_numeric($params['id']))
                $select->where('p.id = ?', intval($params['id']));
            elseif (is_array($params['id']) && count($params['id']))
                $select->where('p.id IN (?)', $params['id']);
        }

        if (isset($params['good_id']) && $params['good_id'])
            $select->where('p.good_id = ?', intval($params['good_id']));

        if (isset($params['color_id']) && $params['color_id'])
            $select->where('p.color_id = ?', intval($params['color_id']));

        if (isset($params['code']) && $params['code'])
            $select->where('c.code = ?', strval($params['code']));

        if (isset($params['sort']) and $params['sort']) {
            $collate = '';
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            if (in_array($params['sort'], array('good_name', 'color_id')))
                $collate = ' COLLATE utf8_unicode_ci ';

            if ($params['sort'] == 'good_name') {
                $select->join( array('g' => 'good'), 'p.good_id = g.id', array() );
                $order_str = 'g.`name` ' . $collate . $desc;
            } elseif ($params['sort'] == 'color_id') {
                $select->join( array('g' => 'good_color'), 'p.color_id = g.id', array() );
                $order_str = 'g.`name` ' . $collate . $desc;
            } else {
                $order_str = 'p.`'.$params['sort'] . '` ' . $collate . $desc;
            }

            $select->order(new Zend_Db_Expr($order_str));
        } else {
            $select->order('p.id DESC');
        }

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);

        if ($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");

        return $result;
    }

    public function get_cache()
    {
        $cache  = Zend_Registry::get('cache');
        $result = $cache->load($this->_name.'_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name), array('p.id', 'p.good_id', 'p.color_id'))
                ->join(array('c' => 'good_mapping_code'), 'p.id=c.good_mapping_id', array('c.code'))
                ->join(array('g' => 'good'), 'g.id=p.good_id', array('g.cat_id'));

            $data = $db->fetchAll($select);

            $result = array();

            if ($data)
                foreach ($data as $item)
                    $result[ $item['code'] ] = array(
                        'good_id'  => $item['good_id'],
                        'color_id' => $item['color_id'],
                        'cat_id'   => $item['cat_id'],
                    );

            $cache->save($result, $this->_name.'_cache', array(), null);
        }

        return $result;
    }
}