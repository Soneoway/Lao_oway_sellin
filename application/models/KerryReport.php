<?php
class Application_Model_KerryReport extends Zend_Db_Table_Abstract{

	protected $_name = 'market';

	function fetchPagination($page, $limit, &$total, $params){

		set_time_limit(0);
		ini_set('memory_limit', -1);
		ini_set('display_error', 0);
		error_reporting('~E_ALL');

		$db = Zend_Registry::get('db');
		$userStorage = Zend_Auth::getInstance()->getStorage()->read();

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id,dor.tracking_no,p.is_kerry,sum(p.num) as qnum,dis.title,ktr.send_date,ktr.status,ktr.status_code,p.sales_confirm_date,p.outmysql_time,p.add_time,p.finance_confirm_date,dor.number_of_package,dor.weight,p.sn,p.sn_ref,sad.contact_name,sad.address,sdi.district_name,sam.amphure_name,spr.provice_id,spr.provice_name,szi.zipcode,sad.phone,kssl.shipment_status_id as kerry_status_code,kssl.created_date as receive_created_date,p.is_kerry,dsa.company,case dsa.company when 3 then "Genious" when 5 then "NKC" when 6 then "YAS" when 9 then "J&T" else case ktr.delivery_type when 1 then "Kerry" when 2 then "J&T" else "-" end end as company_logistics,dor.created_at,kssl.image')))
		->joinLeft(array('sad' => 'shipping_address'), 'sad.id = p.shipping_address', array())
		->joinLeft(array('sam' => 'shipping_amphures'), 'sam.amphure_id = sad.amphures_id', array())
		->joinLeft(array('sdi' => 'shipping_districts'), 'sdi.district_code = sad.districts_id', array())
		->joinLeft(array('spr' => 'shipping_provinces'), 'spr.provice_id = sad.province_id', array())
		->joinLeft(array('szi' => 'shipping_zipcodes'), 'szi.zip_id = sad.zipcodes', array())
		->joinLeft(array('dsa' => 'delivery_sales'), 'dsa.sales_sn = p.sn', array())
		->joinLeft(array('dor' => 'delivery_order'), 'dor.id = dsa.delivery_order_id', array())
		->joinLeft(array('ktr' => 'kerry_transaction'), 'ktr.sn = p.sn and ktr.type = 1', array())
		->joinLeft(array('dis' => 'distributor'), 'dis.id = p.d_id', array())
		->joinLeft(array('kssl' => 'kerry_shipment_status_log'), 'kssl.id = (select kssl2.id from kerry_shipment_status_log kssl2 where kssl2.sn_ref = p.sn_ref COLLATE utf8_unicode_ci group by kssl2.sn_ref,kssl2.shipment_status_date order by kssl2.shipment_status_date DESC limit 1) and LENGTH(kssl.shipment_status_id) < 10', array());


		//Tanong Add New Find by sn_ref
		if (isset($params['sn'])) {
			if (is_array($params['sn']) && count($params['sn']))
				$select->where('p.sn_ref IN (?) or p.sn IN (?)', $params['sn']);
			elseif (!is_array($params['sn']) && $params['sn'])
				$select->where('p.sn_ref LIKE ? or p.sn LIKE ?', '%'.$params['sn'].'%');
		}

		if (isset($params['cat_id']) and $params['cat_id']) {
			if (is_array($params['cat_id']) && count($params['cat_id']) > 0) {
				$select->where('p.cat_id IN (?)', $params['cat_id']);
			} else {
				$select->where('p.cat_id = ?', $params['cat_id']);
			}
		}

        // CHECK filter Warehouse
		if ( isset($params['warehouse_id']) && $params['warehouse_id'] ) {

			if (is_array($params['warehouse_id']))
				$select->where("p.warehouse_id IN (".implode(",",$params['warehouse_id']).")", null);
			else
				$select->where('p.warehouse_id IN (?)', $params['warehouse_id']);
		}

		if (isset($params['user_id']) and $params['user_id'])
			$select->where('p.user_id = ?', $params['user_id']);

		if (isset($params['good_id']) and $params['good_id'])
			$select->where('p.good_id = ?', $params['good_id']);

		if (isset($params['good_color']) and $params['good_color'])
			$select->where('p.good_color = ?', $params['good_color']);

		if (isset($params['d_id']) and $params['d_id'])
			$select->where('p.d_id = ?', $params['d_id']);

		if($userStorage->id =='106' || $userStorage->id =='216' || $userStorage->id =='233' || $userStorage->id =='227')
		{
			// $select->where('p.warehouse_id =90');
		}

		if (isset($params['created_at_from']) and $params['created_at_from']){
			list( $day, $month, $year ) = explode('/', $params['created_at_from']);

			if (isset($day) and isset($month) and isset($year) )
				$select->where('p.add_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
		}

		if (isset($params['created_at_to']) and $params['created_at_to']){
			list( $day, $month, $year ) = explode('/', $params['created_at_to']);

			if (isset($day) and isset($month) and isset($year) )
				$select->where('p.add_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
		}

		if (isset($params['send_at_from']) and $params['send_at_from']){
			list( $day, $month, $year ) = explode('/', $params['send_at_from']);

			if (isset($day) and isset($month) and isset($year) )
				$select->where('dor.created_at >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
		}

		if (isset($params['send_at_to']) and $params['send_at_to']){
			list( $day, $month, $year ) = explode('/', $params['send_at_to']);

			if (isset($day) and isset($month) and isset($year) )
				$select->where('dor.created_at <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
		}

		if (isset($params['tags']) and $params['tags']){
            $select->join(array('ta_ob' => 'tag_object'),
                '
                    p.sn = ta_ob.object_id
                    AND ta_ob.type = '.TAG_ORDER.'
                ',
                array());
            $select->join(array('ta' => 'tag'),
                '
                    ta.id = ta_ob.tag_id
                ',
                array());

            $select->where('ta.name IN (?)', $params['tags']);
        }

		if (isset($params['sort']) and $params['sort']) {

			$order_str = $collate = '';

			$desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

			$order_str .= $params['sort'] . $collate . $desc;


			$select->order(new Zend_Db_Expr($order_str));
		}

		if (isset($params['confirm_so']) and $params['confirm_so'])
			$select->where('confirm_so = ?', $params['confirm_so']);

		$select->where('p.old_data is null',null);

		if($userStorage->warehouse_type !=''){
			$warehouse_type = $userStorage->warehouse_type;
			$select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
		}else{
			$select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
		}

		if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
			$select->where('p.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4)',null);
		}

		// if(isset($params['show_kerry']) AND $params['show_kerry']){
		// 	$select->where('p.is_kerry = 1',null);
		// }

		// if(isset($params['show_kerry_status']) AND $params['show_kerry_status']){

		// 	switch ($params['show_kerry_status']) {
		// 		case '1':
		// 			$select->where('ktr.status = 7',null);
		// 			break;
		// 		case '2':
		// 			$select->where('ktr.status = 0',null);
		// 			break;
		// 		case '3':
		// 			$select->where('ktr.status in (1,2,3,4,5,6)',null);
		// 			break;
		// 	}
		// }

		if(isset($params['company_logistics']) AND $params['company_logistics']){

			switch ($params['company_logistics']) {
				case '1':

					$select->where('dsa.company is null',null);
					$select->where('ktr.delivery_type = ?','1');

					if(isset($params['logistics_status']) AND $params['logistics_status']){

						switch ($params['logistics_status']) {
							case '1':
								$select->where('ktr.status = 7',null);
								$select->where('
									kssl.shipment_status_id is null or 
									kssl.shipment_status_id <> "POD"',null);
								break;
							case '2':
								$select->where('kssl.shipment_status_id = "POD"',null);
								break;
							case '3':
								$select->where('ktr.status in (1,2,3,4,5,6)',null);
								break;
						}

					}else{
						return [];
					}

					break;
				case '3':

					$select->where('dsa.company = 3',null);
					
					if(isset($params['logistics_status']) AND $params['logistics_status']){

						switch ($params['logistics_status']) {
							case '1':
								$select->where('kssl.shipment_status_id is null or kssl.shipment_status_id <> "POD"',null);
								break;
							case '2':
								$select->where('kssl.shipment_status_id = "POD"',null);
								break;
							case '3':
								return [];
								break;
						}

					}else{
						return [];
					}

					break;
				case '5':
					
					$select->where('dsa.company = 5',null);

					if(isset($params['logistics_status']) AND $params['logistics_status']){

						switch ($params['logistics_status']) {
							case '1':
								$select->where('kssl.shipment_status_id is null or kssl.shipment_status_id <> "POD"',null);
								break;
							case '2':
								$select->where('kssl.shipment_status_id = "POD"',null);
								break;
							case '3':
								return [];
								break;
						}

					}else{
						return [];
					}

					break;
				case '6':
					
					$select->where('dsa.company = 6',null);

					if(isset($params['logistics_status']) AND $params['logistics_status']){

						switch ($params['logistics_status']) {
							case '1':
								$select->where('kssl.shipment_status_id is null or kssl.shipment_status_id <> "POD"',null);
								break;
							case '2':
								$select->where('kssl.shipment_status_id = "POD"',null);
								break;
							case '3':
								return [];
								break;
						}

					}else{
						return [];
					}

					break;
				case '9':
					
					$select->where('dsa.company is null',null);
					$select->where('ktr.delivery_type = ?','2');

					if(isset($params['logistics_status']) AND $params['logistics_status']){

						switch ($params['logistics_status']) {
							case '1':
								$select->where('ktr.status = 7',null);
								$select->where('
									kssl.shipment_status_id is null or 
									kssl.shipment_status_id <> "POD"',null);
								break;
							case '2':
								$select->where('kssl.shipment_status_id = "POD"',null);
								break;
							case '3':
								$select->where('ktr.status in (1,2,3,4,5,6)',null);
								break;
						}

					}else{
						return [];
					}

					break;
				default:
					return [];
					break;
			}

		}else{
			return [];
		}

		if(isset($params['tracking_no']) AND $params['tracking_no']){

			if (is_array($params['tracking_no']) && count($params['tracking_no']))
				$select->where('dor.tracking_no IN (?)', $params['tracking_no']);
			elseif (!is_array($params['tracking_no']) && $params['tracking_no'])
				$select->where('dor.tracking_no LIKE ?', '%'.$params['tracking_no'].'%');
		}

		$select->where('p.outmysql_time is not null and sad.contact_name is not null and sad.phone is not null and sad.address is not null and sdi.district_name is not null and sam.amphure_name is not null and spr.provice_name is not null and szi.zipcode is not null and dor.number_of_package is not null and dor.weight is not null',1);
        	$select->group('p.sn');
           // echo $select;die;

		if($limit)
			$select->limitPage($page, $limit);


		if (isset($params['export']) and $params['export'])
		{
            //WhereExport

             //echo $select;die;
            return $select->__toString();
        }else{
        	$result = $db->fetchAll($select);
        }

        // echo $select;die;
        // $result = $db->fetchAll($sql);

        if (isset($params['not_get_total']) and $params['not_get_total'])
            return $result;

        $total = $db->fetchOne("select FOUND_ROWS()");

        return $result;

    }

}