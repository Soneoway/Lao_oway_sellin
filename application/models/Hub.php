<?php
class Application_Model_Hub extends Zend_Db_Table_Abstract{
    protected $_name = 'hub';

    public function fetchPagination($page, $limit, &$total, $params){
        $db     = Zend_Registry::get('db');
        $select = $db->select();
        $select->from(array('p' => $this->_name),
            array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.name', 'p.address', 'p.note', 'p.contact', 'p.phone_number', 'p.mobile_phone'));

        if(isset($params['name']) and $params['name'])
            $select->where('p.name like ?', '%'.$params['name'].'%');

        if(isset($params['district_id']) and $params['district_id']) {
            $select
                ->join(array('hd' => 'hub_district'), 'p.id=hd.hub_id', array())
                ->join(array('d' => HR_DB.'.regional_market'), 'd.id=hd.region_id', array())
                ->where('d.id = ? OR d.parent = ?', $params['district_id']);
        }

        if(isset($params['address']) and $params['address'])
            $select->where('p.address = ?', '%'.$params['address'].'%');

        $select->order('p.created_at DESC');

        if($limit)
            $select->limitPage($page, $limit);

        if (isset($params['export']) && $params['export'])
            return $db->query($select->__toString());

        $result = $db->fetchAll($select);

        if($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");

        return $result;
    }

    public function get_cache()
    {
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_cache');

        if ($result !== false) return $result;

        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.*'));

        $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

        $data = $db->fetchAll($select, 'name');

        $result = array();
        if ($data)
            foreach ($data as $item)
                $result[$item['id']] = $item['name'];

        $cache->save($result, $this->_name.'_cache', array(), null);

        return $result;
    }

    public function get_all_cache()
    {
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_all_cache');

        if ($result !== false) return $result;

        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.*'));

        $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

        $data = $db->fetchAll($select, 'name');

        $result = array();
        if ($data)
            foreach ($data as $item) {  
                $result[$item['id']] = array(
                    'id'           => $item['id'],
                    'name'         => trim($item['name']),
                    'contact'      => trim($item['contact']),
                    'mobile_phone' => trim($item['mobile_phone']),
                    'address'      => trim($item['address']),
                );
            }

        $cache->save($result, $this->_name.'_all_cache', array(), null);

        return $result;
    }
}