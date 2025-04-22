<?php
/**
*
*/
class Application_Model_DistributorMapping extends Zend_Db_Table_Abstract
{
    protected $_name = 'distributor_mapping';

    function fetchPagination($page, $limit, &$total, $params) {
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.distributor_id'), 'p.code'))
            ->join(array('d' => 'distributor'), 'd.id=p.distributor_id', array('d.title', 'd.add'));

        if (isset($params['distributor_id']) && $params['distributor_id'])
            $select->where('p.distributor_id = ?', intval($params['distributor_id']));

        if (isset($params['code']) && $params['code'])
            $select->where('p.code = ?', strval($params['code']));

        if (isset($params['sort']) and $params['sort']) {
            $collate = '';
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            if (in_array($params['sort'], array('distributor_name')))
                $collate = ' COLLATE utf8_unicode_ci ';

            if ($params['sort'] == 'distributor_name') {
                $select->join( array('d' => 'distributor'), 'p.distributor_id = d.id', array() );
                $order_str = 'd.`title` ' . $collate . $desc;
            } else {
                $order_str = 'p.`'.$params['sort'] . '` ' . $collate . $desc;
            }

            $select->order(new Zend_Db_Expr($order_str));
        } else {
            $select->order('p.distributor_id DESC');
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
                ->from(array('p' => $this->_name), array('p.distributor_id', 'p.code'))
                ->join(array('d' => 'distributor'), 'd.id=p.distributor_id', array('d.warehouse_id'));

            $data = $db->fetchAll($select);

            $result = array();

            if ($data)
                foreach ($data as $item)
                    $result[ $item['code'] ] = array(
                        'distributor_id' => $item['distributor_id'],    
                        'warehouse_id' => $item['warehouse_id'],    
                    );

            $cache->save($result, $this->_name.'_cache', array(), null);
        }

        return $result;
    }
}