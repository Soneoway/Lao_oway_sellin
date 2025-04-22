<?php
class Application_Model_DeliveryOrder extends Zend_Db_Table_Abstract{
    protected $_name = 'delivery_order';

    public function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $sum_string = 'SUM(CASE WHEN m.cat_id=%d THEN (m.num*g.weight) ELSE 0 END)';
        $quantity_string = 'SUM(CASE WHEN m.cat_id=%d THEN m.num ELSE 0 END)';

        if (isset($params['export']) && $params['export'])
            $get_market = array(
                'sales_sn' => 'm.sn',
                'sales_ref'=>'m.sn_ref',
                'm.d_id',
                'phone_quantity'       => new Zend_Db_Expr(sprintf($quantity_string, PHONE_CAT_ID)),
                'accessories_quantity' => new Zend_Db_Expr(sprintf($quantity_string, ACCESS_CAT_ID)),
                'digital_quantity'     => new Zend_Db_Expr(sprintf($quantity_string, DIGITAL_CAT_ID)),
            );
        elseif (isset($params['export_finance']) && $params['export_finance'])
            $get_market = array(
                'net_weight'         => new Zend_Db_Expr('SUM(m.num*g.weight)'),
                'phone_weight'       => new Zend_Db_Expr(sprintf($sum_string, PHONE_CAT_ID)),
                'accessories_weight' => new Zend_Db_Expr(sprintf($sum_string, ACCESS_CAT_ID)),
                'digital_weight'     => new Zend_Db_Expr(sprintf($sum_string, DIGITAL_CAT_ID)),
            );
        elseif (isset($params['export_kerry']) && $params['export_kerry'])
            $get_market = array(
                'sales_sn' => 'm.sn',
                'sales_ref'=>'m.sn_ref',
                'm.d_id',
                'phone_quantity'       => new Zend_Db_Expr(sprintf($quantity_string, PHONE_CAT_ID)),
                'accessories_quantity' => new Zend_Db_Expr(sprintf($quantity_string, ACCESS_CAT_ID)),
                'digital_quantity'     => new Zend_Db_Expr(sprintf($quantity_string, DIGITAL_CAT_ID)),
            );
        else
            $get_market = array(
                'sales_sn' => 'm.sn',
                'sales_ref'=>'m.sn_ref',
                'is_kerry' => 'm.is_kerry'

            );
            //$get_market = array();

        $select = $db->select()
            ->distinct()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'))
            ->join(array('ds' => 'delivery_sales'), 'ds.delivery_order_id=p.id', array())
            ->joinLeft(array('m' => 'market'), 'ds.sales_sn=m.sn', $get_market);

        if($userStorage->warehouse_type !=''){
            $warehouse_type = $userStorage->warehouse_type;
            $select->where('p.warehouse_id in (SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))', null);
        }else{
            $select->where('p.warehouse_id in (SELECT id FROM warehouse WHERE warehouse_type = 1)', null);
        }

        if (isset($params['export_finance']) && $params['export_finance'])
            $select->join(array('g' => 'good'), 'g.id=m.good_id', array());

        if (isset($params['delivery_sn']) and $params['delivery_sn'])
            $select->where('p.sn LIKE ?', $params['delivery_sn']);

        if (isset($params['receiver']) and $params['receiver'])
            $select->where('p.receiver LIKE ?', '%'.$params['receiver'].'%');

        if (isset($params['address']) and $params['address'])
            $select->where('p.address LIKE ?', '%'.$params['address'].'%');

        if (isset($params['phone']) and $params['phone'])
            $select->where('p.phone_number LIKE ?', $params['phone']);

        if (isset($params['d_id']) and $params['d_id'])
            $select->where('m.d_id = ?', $params['d_id']);

        if (isset($params['hub'])) {
            if (is_array($params['hub']) && count($params['hub']))
                $select->where('p.hub IN (?)', $params['hub']);
            elseif (is_numeric($params['hub']) && intval($params['hub']))
                $select->where('p.hub = ?', intval($params['hub']));
            else
                $select->where('1=0', 1);
        }

        if (isset($params['in_hub']) and $params['in_hub'])
            $select->where('p.hub IS NOT NULL AND p.hub <> 0', 1);

        if (isset($params['carrier']) and $params['carrier'])
            $select->where('p.carrier_id = ?', intval($params['carrier']));

        if (isset($params['type']) and $params['type'])
            $select->where('p.type = ?', intval($params['type']));

        if (isset($params['warehouse']) and $params['warehouse']) {
            if (is_array($params['warehouse']) && count($params['warehouse']))
                $select->where('p.warehouse_id IN (?)', $params['warehouse']);

            elseif (is_numeric($params['warehouse']))
                $select->where('p.warehouse_id = ?', intval($params['warehouse']));
        }

        if (isset($params['staff_id']) and $params['staff_id'])
            $select->where('p.staff_id = ?', intval($params['staff_id']));

        if (isset($params['status']) and $params['status']) {
            $select->where('p.status = ?', intval($params['status']));

        } elseif (isset($params['list']) and $params['list']) {
            $select->where('p.status <> ?', My_Delivery_Order_Status::Deleted);
        }

        if (isset($params['sn']) and $params['sn']) {
            $select->where('m.sn_ref = ?', $params['sn']);
        }

        if (isset($params['for_hub']) and $params['for_hub']) {
            if ($params['for_hub'] > 0) {
                $select
                    ->join(array('sh' => 'staff_hub'), 'sh.hub_id=p.hub', array())
                    ->where('sh.staff_id = ?', $params['for_hub']);
            }
        }
/*
        if (isset($params['for_carrier']) and $params['for_carrier']) {
            if ($params['for_carrier'] > 0) {
                $select
                    ->join(array('sc' => 'staff_carrier'), 'sc.carrier_id=p.carrier_id', array())
                    ->where('sc.staff_id = ?', $params['for_carrier']);
            }
        }
*/
        if (isset($params['district_id'])) {
            if (is_array($params['district_id']) && count($params['district_id']))
                $select->where('p.district IN (?)', $params['district_id']);
            elseif (is_numeric($params['district_id']) && $params['district_id'])
                $select->where('p.district = ?', intval($params['district_id']));
            else
                $select->where('1=0', 1);

        } elseif (isset($params['region_id'])) {
            $district_arr = array();

            if (is_array($params['region_id']) && count($params['region_id'])) {
                foreach ($params['region_id'] as $_key => $_id)
                    $this->get_districts_by_province($_id, $district_arr);

            } elseif (is_numeric($params['region_id']) && $params['region_id']) {
                $this->get_districts_by_province($params['region_id'], $district_arr);
            }

            if (count($district_arr))
                $select->where('p.district IN (?)', $district_arr);
            else
                $select->where('1=0', 1);

        } elseif (isset($params['area_id'])) {
            $district_arr = array();

            if (is_array($params['area_id']) && count($params['area_id'])) {
                foreach ($params['area_id'] as $_key => $_area_id)
                    $this->get_districts_by_area($_area_id, $district_arr);

            } elseif (is_numeric($params['area_id']) && $params['area_id']) {
                $this->get_districts_by_area($params['area_id'], $district_arr);
            }

            if (count($district_arr))
                $select->where('p.district IN (?)', $district_arr);
            else
                $select->where('1=0', 1);
        }

        if (isset($params['from']) && $params['from'])
            $select->where('p.created_at >= ?', DateTime::createFromFormat('d/m/Y', $params['from'])->format('Y-m-d 00:00:00'));

        if (isset($params['to']) && $params['to'])
            $select->where('p.created_at <= ?', DateTime::createFromFormat('d/m/Y', $params['to'])->format('Y-m-d 23:59:59'));

        if (isset($params['export_finance']) && $params['export_finance']) {
            $select->group('p.id');
        } elseif (isset($params['export']) && $params['export']) {
            $select->group('m.sn');
        }elseif (isset($params['export_kerry']) && $params['export_kerry']) {
            $select->group('m.sn');
        }

        $select->order('p.created_at DESC');

        if ((isset($params['export']) && $params['export']) || (isset($params['export_finance']) && $params['export_finance'])|| (isset($params['export_kerry']) && $params['export_kerry']))
            //echo $select;die;

            return $select->__toString();

        if ($limit)
            $select->limitPage($page, $limit);

        //echo $select;die;
        $result = $db->fetchAll($select);

        if ($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");

        return $result;
    }

    private function get_districts_by_province($province_id, &$district_arr)
    {
        $QRegion = new Application_Model_RegionalMarket();
        $district_cache = $QRegion->get_district_by_province_cache($province_id);

        if ($district_cache)
            foreach ($district_cache as $key => $value)
                $district_arr[] = intval($key);
    }

    private function get_districts_by_area($area_id, &$district_arr)
    {
        $QRegion = new Application_Model_RegionalMarket();
        $district_cache = $QRegion->get_district_by_area_cache($area_id);

        if ($district_cache)
            foreach ($district_cache as $key => $value)
                $district_arr[] = intval($value);
    }

    public function getDeliveryNo_Ref($sn)
    {
        $sn_ref="";
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref('DN',".$sn.")");

            //$stmt = $db->prepare("CALL gen_running_no_ref('SO',201603121740314924)");
            $stmt->execute();
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
            //print_r( $sn_ref);
            //die;

        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Delivery No, please try again!');
        }
        return $sn_ref;
    }

    public function getSNByDNID($id){

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('ds.sales_sn'))
             ->joinLeft(array('ds' => 'delivery_sales'), 'ds.delivery_order_id=p.id', array());

        $select->where('p.id = ?',$id);

        return $db->fetchRow($select);
    }

    function getDetailMarketByDo($id){

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
        where mar.outmysql_time is not null and sad.contact_name is not null and sad.phone is not null and sad.address is not null and sdi.district_name is not null and sam.amphure_name is not null and spr.provice_name is not null and szi.zipcode is not null and dor.number_of_package is not null and dor.weight is not null and mar.good_id != 127 and dor.id = ?
        group by mar.sn
        order by mar.add_time asc;";

        $result = $db->fetchAll($sql, [$id]);

        return $result;

    }
}