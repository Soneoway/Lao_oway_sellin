<?php
class Application_Model_Area extends Zend_Db_Table_Abstract
{
    protected $_name = 'area';
	protected $_schema = HR_DB;

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_schema.'.'.$this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['name']) and $params['name'])
            $select->where('p.name LIKE ?', '%'.$params['name'].'%');

        $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function get_cache(){
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_schema.'_'.$this->_name.'_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_schema.'.'.$this->_name),
                    array('p.*'));

            $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select, 'name');

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['name'];
                }
            }
            $cache->save($result, $this->_schema.'_'.$this->_name.'_cache', array(), null);
        }
        return $result;
    }

    function areaForQuota(){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_schema.'.'.$this->_name),
                array('p.id','p.name'));

       
        $select->where('p.id not in (?)', array('48','49','72'));

        $select->order('p.name');

        $result = $db->fetchAll($select);
        
        // $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
    
    function getArea(){
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=>$this->_schema.'.'.$this->_name),
            array('*')
        );

        $select->order('p.name asc');

        $result = $db->fetchAll($select);
        return $result;
    }
}                                                      
