<?php

class Application_Model_ImeiLot extends Zend_Db_Table_Abstract
{
    protected $_name = 'imei_lot';

    function checkImeiLot($list_imei){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->where('p.imei_sn in (?)',$list_imei);
        $select->where('p.status_imei = ?',1);

        $data = $db->fetchAll($select);

        return $data;
    }

    public function getImei_lot($page, $limit, &$total, $params)
    {
		$db = Zend_Registry::get('db');

		//print_r($params);
		$select = $db->select()
			->from(array('p' => $this->_name), 
				array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.lot_sn,p.lot_name,count(p.imei_sn)as imei_count'), 'created_date' => 'p.created_date'));
		$select->joinLeft(array('pp'=>'staff'), 'pp.id = p.created_by',
				array('pp.username'));
				   
		if(isset($params['lot_name']) && $params['lot_name']){
			$select->where("p.lot_name like '%".$params['lot_name']."%' ",null);
		}	

		if(isset($params['imei']) && $params['imei']){
			$imeiArray = explode("\r\n", $params['imei']);
			$imei = array_filter($imeiArray);
			//print_r($imei);//die;
			$select->where('p.imei_sn IN (?)',$imei);
		}
			$select->where('p.status_imei =?',1);

		$select->group('p.lot_sn');

		if($limit){
		   $select->limitPage($page, $limit);
		}
		$select->order('p.created_date desc');
		//echo $select;//die;
		$result = $db->fetchAll($select);

		if($limit)
		   $total = $db->fetchOne("select FOUND_ROWS()");
		return $result;
	}

	function ImeiLotDetail($lot_sn){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));
        $select->joinLeft(array('i'=>'imei'), 'i.imei_sn = p.imei_sn',array(null));
        $select->joinLeft(array('g'=>'good'), 'g.id = i.good_id',array('good_name' =>'g.name'));
        $select->joinLeft(array('gc'=>'good_color'), 'gc.id = i.good_color',array('good_color_name' =>'gc.name'));
        $select->joinLeft(array('w'=>'warehouse'), 'w.id = i.warehouse_id',array('warehouse_name' =>'w.name'));
        $select->where('p.lot_sn =?',$lot_sn);
        $select->where('p.status_imei = ?',1);

        $data = $db->fetchAll($select);

        return $data;
    }

}

