<?php
class Application_Model_Client extends Zend_Db_Table_Abstract
{
	protected $_name = 'client';


	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		if(isset($params['client_name']) && $params['client_name']) {
			$select->where('p.client_name LIKE ?', '%'.$params['client_name'].'%');
		}

		if(isset($params['short_name']) && $params['short_name']) {
			$select->where('p.short_name LIKE ?', '%'.$params['short_name'].'%');
		}

		if(isset($params['level']) && $params['level']) {
			$select->where('p.level =?', $params['level']);
		}

		if(isset($params['status']) && $params['status']) {
			$select->where('p.status =?', $params['status']);
		}

		if(isset($params['date_form']) && $params['date_form']) {
			list( $day, $month, $year ) = explode('/', $params['date_form']);

			if (isset($day) and isset($month) and isset($year) )
				$select->where('p.cooperate_date >= ?', $year.'-'.$month.'-'.$day);
		}

		if(isset($params['date_to']) && $params['date_to']) {
			list( $day, $month, $year ) = explode('/', $params['date_to']);

			if (isset($day) and isset($month) and isset($year) )
				$select->where('p.cooperate_date <= ?', $year.'-'.$month.'-'.$day);
		}

		if ($limit)
			$select->limitPage($page, $limit);

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

                $select->order(new Zend_Db_Expr('p.`client_name` COLLATE utf8_unicode_ci'));

                $data = $db->fetchAll($select);

                $result = array();
                if ($data){
                    foreach ($data as $item){
                        $result[$item['customer_code']] = $item['client_name'];
                    }
                }
                $cache->save($result, $this->_name.'_cache', array(), null);
            }
            return $result;
        }

}
?>