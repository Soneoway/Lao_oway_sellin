<?php
class Application_Model_FptCodeMapping extends Zend_Db_Table_Abstract
{
    protected $_name = 'fpt_code_mapping';

    function fetchPagination($page, $limit, &$total, $params) {
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
        
        $select->join( array('d' => 'distributor'), 'p.d_id = d.id', array('d.store_code', 'd.title') );

        if (isset($params['fpt_code']) && $params['fpt_code']) {
            $select->where('p.fpt_code = ?', $params['fpt_code']);
        }

        if (isset($params['store_code']) && $params['store_code']) {
            $select->where('d.store_code = ?', $params['store_code']);
        }

        if (isset($params['d_id']) && $params['d_id']) {
            $select->where('p.d_id = ?', $params['d_id']);
        }

        if (isset($params['sort']) and $params['sort']) {
            $collate = '';
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            if (in_array($params['sort'], array('distributor_name')))
                $collate = ' COLLATE utf8_unicode_ci ';

            if ($params['sort'] == 'distributor_name') {
                $order_str = 'd.`title` ' . $collate . $desc;
            } elseif ($params['sort'] == 'store_code') {
                $order_str = 'd.`store_code` ' . $collate . $desc;
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
                    if ( trim($item['fpt_code']) == "" ) continue;
                    
                    $result[$item['fpt_code']] = $item['d_id'];
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