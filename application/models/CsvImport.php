<?php
class Application_Model_CsvImport extends Zend_Db_Table_Abstract
{
	protected $_name = 'map_import_product';

	public function importCsv($key){
		// echo 'test'; die;
		$db = Zend_Registry::get('db');

	        $select = $db->select()
			->from(array('c' => $this->_name),array('c.*'));
			$select->where('c.key_product =?', $key);

			// echo($select); die;
			$db->fetchAll($select);
			
	}

	public function get_list_keyproduct($page, $limit, &$total, $params){

		$db = Zend_Registry::get('db');

		if($limit){

			$select = $db->select()
			->from(array('p' => $this->_name),
			 array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
		}else {

			$select = $db->select()
			->from(array('p' => $this->_name), array('p.*'));
		}
			$select->joinLeft(array('pp'=>'staff'), 'pp.id = p.create_by',
							  array('pp.username'));
			$select->joinLeft(array('ss'=>'good'), 'ss.id = p.good_id',
							  array('good_name'=>'ss.name'));	
			$select->joinLeft(array('gg'=>'good_color'), 'gg.id = p.good_color',
							  array('color_name'=>'gg.name'));
			$select->order('p.id desc');

			if($limit)
			   $select->limitPage($page, $limit);

			$result = $db->fetchAll($select);
			// print_r($result); die;
			if($limit)
			   $total = $db->fetchOne("select FOUND_ROWS()");
			
			return $result;

	}

}