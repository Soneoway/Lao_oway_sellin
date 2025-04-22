<?php
class Application_Model_Bank extends Zend_Db_Table_Abstract{

	protected $_name = 'bank'; 
	
	function fetchPagination($page, $limit, &$total, $params){
		$db 	= Zend_Registry::get('db');
		$select = $db->select();
		$select->from(array('b'=>$this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT b.id'),'b.*'));
		
		if(isset($params['name']) and $params['name']){
			$select->where('name like ?','%'.$params['name'].'%');
		}
		//Sort
		if(isset($params['sort']) && $params['sort']){
			$order_str = $collate = '';
			if (in_array($params['sort'], array('name', 'id')))
			
			if($params['sort']=='b.id'){
					
			}else{
				$collate .= ' COLLATE utf8_unicode_ci ';
			}
		
				
			$desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';	
			$order_str .= $params['sort'] . $collate . $desc;
			$select->order(new Zend_Db_Expr($order_str));
		}// End sort
		
		
		if($limit)
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