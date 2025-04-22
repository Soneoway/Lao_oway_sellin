<?php
class Application_Model_LockStock extends Zend_Db_Table_Abstract
{
	protected $_name = 'lock_stock';

	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->joinleft(array('w' => 'warehouse'),'p.warehouse_id = w.id',array('warehouse_name' => 'w.name'));
		$select->joinleft(array('g' => 'good'),'p.good_id = g.id',array());
		$select->joinleft(array('b' => 'brand'),'b.id = g.brand_id',array('product_name' => 'CONCAT(b.name," ",g.name)'));
		$select->joinleft(array('gc' => 'good_color'),'p.color_id = gc.id',array('color_name' => 'gc.name'));

		if(isset($params['warehouse_id']) && $params['warehouse_id']){
			$select->where('p.warehouse_id =?',$params['warehouse_id']);
		}

		if(isset($params['product_model']) && $params['product_model']){
			$select->where('p.good_id =?',$params['product_model']);
		}

		if(isset($params['lock_type']) && $params['lock_type']){
			$select->where('p.lock_type =?',$params['lock_type']);
		}

		if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");

        return $result;

	}

	function getLockStockByProduct($storageParams)
	{
		$db = Zend_Registry::get('db');

		$select = $db->select()
			->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		if(isset($storageParams['good_id']) && $storageParams['good_id']) {
			$select->where('p.good_id =?',$storageParams['good_id']);
		}

		if(isset($storageParams['warehouse_id']) && $storageParams['warehouse_id']) {
			$select->where('p.warehouse_id =?',$storageParams['warehouse_id']);
		}

		if(isset($storageParams['good_color_id']) && $storageParams['good_color_id']) {
			$select->where('p.color_id =?',$storageParams['good_color_id']);
		}

		$result = $db->fetchAll($select);
		return $result;
	}

	function getLockStock($good_id){

		$db = Zend_Registry::get('db');

		$select = $db->select()
			->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		if(isset($good_id) && $good_id) {
			$select->where('p.good_id =?',$good_id);
		}

		$result = $db->fetchAll($select);
		return $result;

	}
}

?>