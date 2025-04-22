<?php
class Application_Model_KerryShipmentStatusLog extends Zend_Db_Table_Abstract{

	protected $_name = 'kerry_shipment_status_log';

	function getDetailMarket($sn){

		$db = Zend_Registry::get('db');

		$sql = "select d.ka_type,dor.tracking_no,mar.cat_id,mar.outmysql_time,dor.number_of_package,dor.weight,mar.sn,mar.sn_ref,sad.contact_name,sad.address,sdi.district_name,sam.amphure_id,sam.amphure_name,spr.provice_id,spr.provice_name,szi.zipcode,sad.phone,sad.d_id,mar.warehouse_id,mar.d_id
		from market mar
		left join distributor d on d.id = mar.d_id
		left join shipping_address sad on sad.id = mar.shipping_address and sad.d_id = mar.d_id
		left join shipping_amphures sam on sam.amphure_id = sad.amphures_id
		left join shipping_districts sdi on sdi.district_code = sad.districts_id
		left join shipping_provinces spr on spr.provice_id = sad.province_id
		left join shipping_zipcodes szi on zip_id = sad.zipcodes
		left join delivery_sales dsa on dsa.sales_sn = mar.sn
		left join delivery_order dor on dor.id = dsa.delivery_order_id
		where mar.outmysql_time is not null and sad.contact_name is not null and sad.phone is not null and sad.address is not null and sdi.district_name is not null and sam.amphure_name is not null and spr.provice_name is not null and szi.zipcode is not null and dor.number_of_package is not null and dor.weight is not null and mar.good_id != 127 and mar.sn = ?
		group by mar.sn
		order by mar.add_time asc;";

		$result = $db->fetchAll($sql, [$sn]);

		return $result;

	}

    function getDetailCO($sn){

        $db = Zend_Registry::get('db');

        $sql = "select '' as d_id,'' as cat_id,'' as ka_type,dor.tracking_no,cso.confirmed_out_at as outmysql_time,dor.number_of_package,dor.weight,cso.changed_sn as sn,cso.sn_ref,maw.contact_name,maw.address,sdi.district_name,sam.amphure_id,sam.amphure_name,spr.provice_id,spr.provice_name,szi.zipcode,maw.phone,maw.warehouse_id
        from change_sales_order cso
        left join map_address_warehouse maw on maw.warehouse_id = cso.new_id
        left join shipping_amphures sam on sam.amphure_id = maw.amphures_id and sam.provice_id = maw.province_id
        left join shipping_districts sdi on sdi.district_id = maw.districts_id and sdi.amphur_id = maw.amphures_id and sdi.provice_id = maw.province_id
        left join shipping_provinces spr on spr.provice_id = maw.province_id
        left join shipping_zipcodes szi on szi.zipcode = maw.zipcodes and szi.district_id = maw.districts_id and szi.amphur_id = maw.amphures_id and szi.province_id = maw.province_id
        left join delivery_sales dsa on dsa.sales_sn = cso.changed_sn
        left join delivery_order dor on dor.id = dsa.delivery_order_id
        where cso.confirmed_out_at is not null and maw.contact_name is not null and maw.phone is not null and maw.address is not null and sdi.district_name is not null and sam.amphure_name is not null and spr.provice_name is not null and szi.zipcode is not null and dor.number_of_package is not null and dor.weight is not null and cso.is_co = 1 and cso.status = 2 and cso.scanned_in_at is null and cso.completed_date is null and cso.changed_sn = ?
        group by cso.changed_sn
        order by cso.created_at asc;";

        $result = $db->fetchAll($sql, [$sn]);

        return $result;

    }

	function getProviceBySN($sn){

		$db = Zend_Registry::get('db');

		$sql = "select d.ka_type, spr.provice_id, dis.rank, mar.cat_id, mar.warehouse_id, mar.d_id
		from market mar
		left join distributor d on d.id = mar.d_id
		left join shipping_address sad on sad.id = mar.shipping_address and sad.d_id = mar.d_id
		left join shipping_provinces spr on spr.provice_id = sad.province_id
		left join distributor dis on dis.id = mar.d_id
		where mar.good_id != 127 and mar.sn = ?
		group by mar.sn;";

		$result = $db->fetchAll($sql, [$sn]);

		return $result;

	}

	public function getDeliveryDone($sn_ref){

        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> $this->_name));
        $select->where('p.sn_ref = ?', $sn_ref);
        $select->where('p.shipment_status_id = ?', 'POD');
        //echo $select;die;
        return $db->fetchRow($select);
    }

    public function getDeliveryHistory($delivery_add_by, $company, $date, $partBucket){

    	$db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> $this->_name), array('do.tracking_no','mar.sn','mar.sn_ref','sad.contact_name','sad.address','sad.phone','sam.amphure_name','sdi.district_name', 'spr.provice_name', 'szi.zipcode', 'image' => 'concat("'.$partBucket.'",p.image)', 'send_date' => 'p.shipment_status_date'))
        ->joinLeft(array('do' => 'delivery_order'), 'do.tracking_no = p.tracking_no and do.status <> 6', array())
        ->joinLeft(array('ds' => 'delivery_sales'), 'ds.delivery_order_id = do.id and ds.company = p.company', array())
        ->joinLeft(array('mar' => 'market'), 'mar.sn = ds.sales_sn', array())
        ->joinLeft(array('sad' => 'shipping_address'), 'sad.id = mar.shipping_address', array())
        ->joinLeft(array('sam' => 'shipping_amphures'), 'sam.amphure_id = sad.amphures_id', array())
        ->joinLeft(array('sdi' => 'shipping_districts'), 'sdi.district_code = sad.districts_id', array())
        ->joinLeft(array('spr' => 'shipping_provinces'), 'spr.provice_id = sad.province_id', array())
        ->joinLeft(array('szi' => 'shipping_zipcodes'), 'szi.zip_id = sad.zipcodes', array());
        $select->where('p.company = ?', $company);
        $select->where('p.delivery_add_by = ?', $delivery_add_by);
        $select->where('p.shipment_status_id = ?', 'POD');
        $select->where('p.sn_ref like "%SO%"', true);
        $select->where('p.shipment_status_date >= ?', $date . ' 00:00:00');
        $select->where('p.shipment_status_date <= ?', $date . ' 23:59:59');
        $select->group('mar.sn');
        // echo $select;die;
        return $db->fetchAll($select);
    }

    public function getDeliveryHistoryCO($delivery_add_by, $company, $date, $partBucket){

    	$db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> $this->_name), array('p.tracking_no','cso.changed_sn','p.sn_ref','image' => 'concat("'.$partBucket.'",p.image)', 'send_date' => 'p.shipment_status_date'))
        ->joinLeft(array('cso' => 'change_sales_order'), 'cso.sn_ref COLLATE utf8_unicode_ci = p.sn_ref', array());

        // $select->joinLeft(array('csp' => 'change_sales_product'), 'cso.id = csp.changed_id', array('total_qty_product'=>'csp.num', 'total_qty_product_receive'=>'csp.receive','cat_id', 'good_id','good_color'));
        // $select->joinLeft(array('gcat' => 'good_category'), 'gcat.id = csp.cat_id', array('cat_name' =>'name'));

        // $select->joinLeft(array('gd' => 'good'), 'gd.id = csp.good_id', array('good_name' =>'name'));

        // $select->joinLeft(array('gcol' => 'good_color'), 'gcol.id = csp.good_color', array('color_name' =>'name'));

        $select->joinLeft(array('s_created' => 'staff'), 'cso.created_by = s_created.id', array("TRIM(CONCAT(s_created.firstname,' ',s_created.lastname))AS fullname_created"));
        $select->joinLeft(array('s_scanned_out' => 'staff'), 'cso.scanned_out_by = s_scanned_out.id', array("TRIM(CONCAT(s_scanned_out.firstname,' ',s_scanned_out.lastname))AS fullname_scannedout"));
        $select->joinLeft(array('s_confirmed_out' => 'staff'), 'cso.confirmed_out_by = s_confirmed_out.id', array("TRIM(CONCAT(s_confirmed_out.firstname,' ',s_confirmed_out.lastname))AS fullname_confirmedout"));
        $select->joinLeft(array('s_scanned_in' => 'staff'), 'cso.scanned_in_by = s_scanned_in.id', array("TRIM(CONCAT(s_scanned_in.firstname,' ',s_scanned_in.lastname))AS fullname_scannedin"));
        $select->joinLeft(array('s_completed' => 'staff'), 'cso.completed_user = s_completed.id', array("TRIM(CONCAT(s_completed.firstname,' ',s_completed.lastname))AS fullname_completed"));

        $select->joinLeft(array('w_old' => 'warehouse'), 'w_old.id = cso.old_id', array('from_warehouse' =>'w_old.name'));
        $select->joinLeft(array('w_new' => 'warehouse'), 'w_new.id = cso.new_id', array('to_warehouse' =>'w_new.name'));

        $select->where('p.company = ?', $company);
        $select->where('p.delivery_add_by = ?', $delivery_add_by);
        $select->where('p.shipment_status_id = ?', 'POD');
        $select->where('p.sn_ref like "%CO%"', true);
        $select->where('p.shipment_status_date >= ?', $date . ' 00:00:00');
        $select->where('p.shipment_status_date <= ?', $date . ' 23:59:59');
        // echo $select;die;
        return $db->fetchAll($select);
    }

    public function getTotalCO($company){
        $dateNow = date("Y-m-d");
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p'=> $this->_name), array('count(p.id) as count_co'));
        $select->where('p.company = ?', $company);
        $select->where('p.shipment_status_id = ?', 'POD');
        $select->where('p.sn_ref like "%CO%"', true);
        $select->where('p.shipment_status_date >= ?', $dateNow . ' 00:00:00');
        $select->where('p.shipment_status_date <= ?', $dateNow . ' 23:59:59');
        // echo $select;die;
        return $db->fetchRow($select);
    }

    public function getTotalDetailCO($company){
        $dateNow = date("Y-m-d");
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> $this->_name), array('p.delivery_add_by, count(*) as pod_total, sd.id, sd.firstname, sd.lastname, TRIM(CONCAT(sd.firstname," ",sd.lastname))AS fullname, sd.company, case sd.company when 3 then "Genious" when 5 then "NKC" else "Kerry" end as company_logistics'))
        ->joinLeft(array('sd' => 'staff_delivery'), 'sd.id = p.delivery_add_by', array());
        $select->where('p.sn_ref like "%CO%"', true);
        $select->where('LENGTH(p.shipment_status_id) < 10', true);
        $select->where('p.company = ?', $company);
        $select->where('p.shipment_status_id = ?', 'POD');
        $select->where('p.shipment_status_date >= ?', $dateNow . ' 00:00:00');
        $select->where('p.shipment_status_date <= ?', $dateNow . ' 23:59:59');
        $select->group('p.delivery_add_by');
        $select->order('pod_total desc');
        // echo $select;die;
        return $db->fetchAll($select);
    }

}