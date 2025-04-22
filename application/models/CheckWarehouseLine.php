<?php
class Application_Model_CheckWarehouseLine extends Zend_Db_Table_Abstract
{
	protected $_name = 'check_warehouse_line';

	public function getLineDetailsAll($warehouse_id = null){

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'))
            ->joinLeft(array('w' => 'warehouse'), 'w.id=p.warehouse_id', array('warehouse_name' => 'w.name'))
         	->joinLeft(array('g' => 'good'), 'g.id=p.good_id', array('good_name' => 'g.name'))
         	->joinLeft(array('gc' => 'good_color'), 'gc.id=p.good_color_id', array('good_color_name' => 'gc.name'));

        if($warehouse_id){
            $select->where('p.warehouse_id = ?',$warehouse_id);
        }

        $select->where('p.status = ?',1);
        // echo $select;die;

        return $db->fetchAll($select);
    }

    public function getLineDetailsByLineName($lineName){

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'))
            ->joinLeft(array('w' => 'warehouse'), 'w.id=p.warehouse_id', array('warehouse_name' => 'w.name'))
            ->joinLeft(array('g' => 'good'), 'g.id=p.good_id', array('good_name' => 'g.name'))
            ->joinLeft(array('gc' => 'good_color'), 'gc.id=p.good_color_id', array('good_color_name' => 'gc.name'));

        $select->where('p.status = ?',1);
        $select->where('p.line_name = ?',$lineName);
        // echo $select;die;

        return $db->fetchRow($select);
    }

    public function getLineDetailsByWarehouseProductColor($warehouse_id,$good_id,$good_color_id){

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.id','p.line_name'));

        $select->where('p.status = ?',1);
        $select->where('p.warehouse_id = ?',$warehouse_id);
        $select->where('p.good_id = ?',$good_id);
        $select->where('p.good_color_id = ?',$good_color_id);
        // echo $select;die;

        return $db->fetchAll($select);
    }

    public function getCreatedLineBy($id){

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.create_by'));

        $select->where('p.status = ?',1);
        $select->where('p.id = ?',$id);
        // echo $select;die;

        return $db->fetchRow($select);
    }

    public function getDetailsByLine($id){

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'))
            ->joinLeft(array('w' => 'warehouse'), 'w.id=p.warehouse_id', array('warehouse_name' => 'w.name'))
            ->joinLeft(array('g' => 'good'), 'g.id=p.good_id', array('good_name' => 'g.name'))
            ->joinLeft(array('gc' => 'good_color'), 'gc.id=p.good_color_id', array('good_color_name' => 'gc.name'));

        $select->where('p.status = ?',1);
        $select->where('p.id = ?',$id);
        // echo $select;die;

        return $db->fetchRow($select);
    }

    public function getLineScannedDetails($warehouse_id){

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'))
            ->joinLeft(array('w' => 'warehouse'), 'w.id=p.warehouse_id', array('warehouse_name' => 'w.name'))
            ->joinLeft(array('g' => 'good'), 'g.id=p.good_id', array('good_name' => 'g.name'))
            ->joinLeft(array('gc' => 'good_color'), 'gc.id=p.good_color_id', array('good_color_name' => 'gc.name'))
            ->joinLeft(array('cws' => 'check_warehouse_scan'), 'cws.line=p.id and cws.status = 1', array('scanned_count' => 'count(cws.id)'))
            ->joinLeft(array('csw' => 'check_stock_warehouse'), 'csw.warehouse_id=p.warehouse_id and csw.good_id = p.good_id and csw.good_color_id = p.good_color_id and csw.status = 1', array('total_storage' => 'csw.storage','total_on_changing' => 'csw.on_changing','sync_date' =>'csw.created_date'));

        if($warehouse_id){
            $select->where('p.warehouse_id = ?',$warehouse_id);
        }

        $select->where('p.status = ?',1);
        $select->group('p.id');
        // echo $select;die;

        return $db->fetchAll($select);
    }

    public function getLineScannedDetailsForExcel($line,$warehouse_id,$good_id,$good_color_id){

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'))
            ->joinLeft(array('w' => 'warehouse'), 'w.id=p.warehouse_id', array('warehouse_name' => 'w.name'))
            ->joinLeft(array('g' => 'good'), 'g.id=p.good_id', array('good_name' => 'g.name'))
            ->joinLeft(array('gc' => 'good_color'), 'gc.id=p.good_color_id', array('good_color_name' => 'gc.name'))
            ->joinLeft(array('cws' => 'check_warehouse_scan'), 'cws.line=p.id and cws.status = 1', array('imei','imei_created_date' => 'cws.create_date'));

        if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID))){
            $select->joinLeft(array('s'=>'staff'),'s.id = cws.create_by',array('fullname' => 'CONCAT(s.firstname, " ", s.lastname)'));
        }

        $select->where('p.status = ?',1);
        $select->where('p.id = ?',$line);
        $select->where('p.warehouse_id = ?',$warehouse_id);
        $select->where('p.good_id = ?',$good_id);
        $select->where('p.good_color_id = ?',$good_color_id);
        // echo $select;die;

        return $db->fetchAll($select);
    }

    public function getTotalStorage($warehouse_id,$good_id,$color_id,$fullData = null){

        $QImei = new Application_Model_Imei();
        
        $getStorage = $QImei->get_imei_storage($warehouse_id,$good_id,$color_id);

        if(!$getStorage){
            return 0;
        }

        foreach ($getStorage as $good) {

            $bad = $demo = $count = $available_normal = $available_demo = $available_apk = $total_normal = $total_demo = $total_changing = $total_apk = 0;
            if ($good['cat_id'] == PHONE_CAT_ID){
                $bad = ($good['imei_bad_count'] ? $good['imei_bad_count'] : 0);
                $bad_normal = ($good['imei_normal_bad_count'] ? $good['imei_normal_bad_count'] : 0);
                $bad_demo = ($good['imei_demo_bad_count'] ? $good['imei_demo_bad_count'] : 0);
                $bad_apk = ($good['imei_apk_bad_count'] ? $good['imei_apk_bad_count'] : 0);
                $count = ($good['imei_count'] ? $good['imei_count'] : 0);
                $demo = ($good['imei_demo_count'] ? $good['imei_demo_count'] : 0);
                $apk = ($good['imei_apk_count'] ? $good['imei_apk_count'] : 0);
            } elseif ($good['cat_id'] == ILIKE_CAT_ID){
                $bad = ($good['ilike_bad_count'] ? $good['ilike_bad_count'] : 0);
                $count = ($good['ilike_count'] ? $good['ilike_count'] : 0);
            } elseif ($good['cat_id'] == DIGITAL_CAT_ID){
                $bad = ($good['digital_bad_count'] ? $good['digital_bad_count'] : 0);
                $count = ($good['digital_count'] ? $good['digital_count'] : 0);
            } else {
                $bad = ($good['damage_product_count'] ? $good['damage_product_count'] : 0);
                $count = ($good['product_count'] ? $good['product_count'] : 0);
            }

            $current_order =  ($good['current_order'] ? $good['current_order'] : 0);
            $current_change_order =  ($good['current_change_order'] ? $good['current_change_order'] : 0);
            $total_normal = $count;
            $available_normal = $total_normal - $current_order - $current_change_order;

            $current_order_demo =  ($good['current_order_demo'] ? $good['current_order_demo'] : 0); 
            $current_change_order_demo =  ($good['current_change_order_demo'] ? $good['current_change_order_demo'] : 0); 
            $total_demo = $demo;
            $available_demo = $total_demo - $current_order_demo - $current_change_order_demo;
            
            $current_order_apk =  ($good['current_order_apk'] ? $good['current_order_apk'] : 0); 
            $current_change_order_apk =  ($good['current_change_order_apk'] ? $good['current_change_order_apk'] : 0); 
            $total_apk = $apk;
            $available_apk = $total_apk - $current_order_apk - $current_change_order_apk;

            $total_current_order = $current_order + $current_order_demo + $current_order_apk;
            $total_current_change_order = $current_change_order + $current_change_order_demo + $current_change_order_apk;

            $total_available = intval($available_normal) + intval($available_demo) + intval($available_apk);

            $total_storage = $total_normal + $total_demo + $total_apk;
            $total_changing =  ($good['current_changing_order'] ? $good['current_changing_order'] : 0); 

            if($fullData){
                return ['total_storage' => $total_storage, 'total_changing' => $total_changing];
            }else{
                return $total_storage;
            }

        }
    }

}
