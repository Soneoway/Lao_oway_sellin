<?php

class Application_Model_MapStockShop extends Zend_Db_Table_Abstract
{
    protected $_name = 'map_stock_shop';

    function getWarehouseByDistributor($warehouse_id){

    	$db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => 'distributor'),array('p.id'));

        $select->where('p.warehouse_id = ?',$warehouse_id);

        $data = $db->fetchAll($select);

        return $data;
    }

    function getDetailsStockShopByWarehouse($warehouse_id){

    	$list_distributor = $this->getWarehouseByDistributor($warehouse_id);

    	if(!$list_distributor){
    		return [];
    	}

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->where('p.d_id in (?)', $list_distributor);
        $select->where('p.status = ?', 1);

        // echo($select);die;

        $data = $db->fetchAll($select);

        return $data;
    }

    function getDetailsStockShopByDistributor($d_id){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->where('p.d_id = ?', $d_id);
        $select->where('p.status = ?', 1);

        // echo($select);die;

        $data = $db->fetchAll($select);

        return $data;
    }

}

