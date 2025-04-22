<?php
class Application_Model_FptGoodMapping extends Zend_Db_Table_Abstract
{
    protected $_name = 'fpt_good_mapping';

    function fetchPagination($page, $limit, &$total, $params) {
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['fpt_good_id']) && $params['fpt_good_id']) {
            $select->where('p.fpt_good_id = ?', $params['fpt_good_id']);
        }

        if (isset($params['good_id']) && $params['good_id']) {
            $select->where('p.good_id = ?', $params['good_id']);
        }

        if (isset($params['good_color']) && $params['good_color']) {
            $select->where('p.good_color = ?', $params['good_color']);
        }

        if (isset($params['sort']) and $params['sort']) {
            $collate = '';
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            if (in_array($params['sort'], array('good_name', 'good_color')))
                $collate = ' COLLATE utf8_unicode_ci ';

            if ($params['sort'] == 'good_name') {
                $select->join( array('g' => 'good'), 'p.good_id = g.id', array() );
                $order_str = 'g.`name` ' . $collate . $desc;
            } elseif ($params['sort'] == 'good_color') {
                $select->join( array('g' => 'good_color'), 'p.good_color = g.id', array() );
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
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function get_cache($fpt_code = null)
    {
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_cache');

        if ($result === false || ( $result && !is_null( $fpt_code ) && !isset( $result[$fpt_code] ) ) ) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

            $data = $db->fetchAll($select);

            $result = array();

            if ($data){
                foreach ($data as $item){
                    if ( trim($item['fpt_good_id']) == "" ) continue;
                    
                    $result[$item['fpt_good_id']] = array(
                                            'good_id'    => $item['good_id'],
                                            'good_color' => $item['good_color'],
                                            );
                }
            }

            $cache->save($result, $this->_name.'_cache', array(), null);
        }

        return is_null($fpt_code) 
            ? $result 
            : ( 
                isset( $result[ $fpt_code ] ) 
                    ? $result[ $fpt_code ] 
                    : false 
                );
    }
}