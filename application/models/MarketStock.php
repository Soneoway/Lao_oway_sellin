<?php
class Application_Model_MarketStock extends Zend_Db_Table_Abstract
{
    protected $_name = 'market_stock';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        $QDistributor = new Application_Model_Distributor();

        $select = $db->select();

        $subCountOutImei = $db->select()
            ->from(array('i' => 'imei'),
                array('total' => 'COUNT(imei_sn)'))
            ->where('i.sales_sn = p.sn')
            ->where('   i.out_date IS NOT NULL AND i.out_date <> 0 AND i.out_date <> \'\'
                        AND i.into_date IS NOT NULL AND i.into_date <> 0 AND i.into_date <> \'\'
                    ');

        $subCountOutDigital = $db->select()
            ->from(array('ds' => 'digital_sn'),
                array('total' => 'COUNT(*)'))
            ->where('ds.sales_sn = p.sn')
            ->where('   ( ds.out_date IS NOT NULL AND ds.out_date <> 0 AND ds.out_date <> \'\' )
                        AND ( ds.into_date IS NOT NULL AND ds.into_date <> 0 AND ds.into_date <> \'\' )
                    ');

        $subCountOutIlike = $db->select()
            ->from(array('gs' => 'good_sn'),
                array('total' => 'COUNT(*)'))
            ->where('gs.sales_sn = p.sn')
            ->where('   ( gs.out_date IS NOT NULL AND gs.out_date <> 0 AND gs.out_date <> \'\' )
                        AND ( gs.into_date IS NOT NULL AND gs.into_date <> 0 AND gs.into_date <> \'\' )
                    ');

        $subCountPhone = $db->select()
            ->from(array('i' => 'market'),
                array('count_phone' => 'SUM(i.num)'))
            ->where('i.sn = p.sn')
            ->where('i.status = ?', 1)
            ->where('i.cat_id = ?', PHONE_CAT_ID);

        $subCountDigital = $db->select()
            ->from(array('i' => 'market'),
                array('count_digital' => 'SUM(i.num)'))
            ->where('i.sn = p.sn')
            ->where('i.status = ?', 1)
            ->where('i.cat_id = ?', DIGITAL_CAT_ID);

        $subCountIlike = $db->select()
            ->from(array('i' => 'market'),
                array('count_ilike' => 'SUM(i.num)'))
            ->where('i.sn = p.sn')
            ->where('i.status = ?', 1)
            ->where('i.cat_id = ?', ILIKE_CAT_ID);

        if (isset($params['group_sn']) and $params['group_sn']){
            $select_fields = array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'), 'total_qty' => 'SUM(p.num)', 'total_price' => 'SUM(p.total)','invoice_number', 'type', 'print_picking_list', 'service' );

            if (isset($params['get_fields']) and is_array($params['get_fields'])){
                foreach ($params['get_fields'] as $get_field){
                    switch ($get_field){
                        case 'total_imei':
                            $select_fields['total_imei'] = new Zend_Db_Expr('(' . $subCountOutImei . ')');
                            break;
                        case 'phone_qty':
                            $select_fields['phone_qty'] = new Zend_Db_Expr('(' . $subCountPhone . ')');
                            break;
                        case 'digital_qty':
                            $select_fields['digital_qty'] = new Zend_Db_Expr('(' . $subCountDigital . ')');
                            break;
                        case 'total_digital':
                            $select_fields['total_digital'] = new Zend_Db_Expr('(' . $subCountOutDigital . ')');
                            break;
                        case 'ilike_qty':
                            $select_fields['ilike_qty'] = new Zend_Db_Expr('(' . $subCountOutIlike . ')');
                            break;
                        case 'total_ilike':
                            $select_fields['total_ilike'] = new Zend_Db_Expr('(' . $subCountIlike . ')');
                            break;
                        case 'delivery':
                            $select_fields['service'] = 'p.service';
                            break;

                        default:
                            array_push($select_fields, $get_field);
                            break;
                    }
                }

            }else
                array_push($select_fields, 'p.*');

            $select
                ->from(array('p' => $this->_name),
                    $select_fields
                )
                ->group('p.sn');

        }
        elseif ( isset($params['group_sn_bvg']) and $params['group_sn_bvg'] ){
            $select_fields = array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'), 'total_qty' => 'SUM(p.num)', 'total_price' => 'SUM(p.total)','invoice_number', 'type', 'print_picking_list', 'service' );

            if (isset($params['get_fields']) and is_array($params['get_fields']))
                foreach ($params['get_fields'] as $get_field)
                    array_push($select_fields, $get_field);
            else
                array_push($select_fields, 'p.*');

            $select
                ->from(array('p' => $this->_name),
                    $select_fields
                )
                ->group('p.id');
        }


        elseif ( isset($params['sales_in']) and $params['sales_in'] )
            $select->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('DISTINCT p.id'), new Zend_Db_Expr('SUM(p.num) AS total_qty_all'), new Zend_Db_Expr('SUM(p.total) AS total_price_all')));

        elseif ( isset($params['group_good']) and $params['group_good'] ){

            $select->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'), 'total_price' => 'SUM(p.total)','num' => 'SUM(p.num)', 'good_id','cat_id','price','sn','good_color','sale_off_percent','type','good.desc', 'p.total' , 'good.desc_name'));
             $select->joinLeft('good','p.good_id = good.id');

            $select->group(new Zend_Db_Expr("case when p.campaign <> 0 then p.total else good.desc end"));
            $select->group(new Zend_Db_Expr("good.desc"));
            $select->order ( 'good.cat_id ASC' );

        }
        elseif ( isset($params['group_neo3']) and $params['group_neo3'] ){

             $select->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'), 'total_price' => 'p.total','num' => 'p.num','id', 'good_id','cat_id','price','sn','good_color','sale_off_percent','type','good.desc','good.name'));
             $select->joinLeft('good','p.good_id = good.id');

             $select->group ('p.id');

        }

        elseif ( isset($params['group_good_color']) and $params['group_good_color'] ){

            $select->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'), 'total_price' => 'SUM(p.total)','num' => 'SUM(p.num)','good_id','cat_id','price','sn','good_color','sale_off_percent','p.d_id','p.invoice_number', 'p.sn', 'p.invoice_note','type','invoice_time'));
            $select->group ( 'p.good_id' );
            $select->group ( 'p.good_color' );

        }


        else {

            if (isset($params['not_get_total']) and $params['not_get_total'])
                $select_fields = array(new Zend_Db_Expr('DISTINCT p.id'));
            else
                $select_fields = array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'));

            if (isset($params['get_fields']) and is_array($params['get_fields']))
                foreach ($params['get_fields'] as $get_field)
                    array_push($select_fields, $get_field);
            else
                array_push($select_fields, 'p.*');

            $select->from(array('p' => $this->_name),
                $select_fields);
        }

        if (isset($params['delivery']) && $params['delivery']) {
            $select
                ->joinLeft(array('ds' => 'delivery_sales'), 'ds.sales_sn=p.sn', array('order_sales_sn' => 'ds.sales_sn'))
                ->joinLeft(array('do' => 'delivery_order'), 'do.id=ds.delivery_order_id', array())
                ->joinLeft(array('of' => 'office'), 'p.office=of.id', array('office_address' => 'of.name', 'office_title' => 'of.title', 'office_contact' => 'of.customer', 'office_mobile' => 'of.tel'))
                // ->joinLeft(array('se' => 'service'), 'p.service=se.id', array('service_name' => 'se.name'))
                ->where('ds.sales_sn IS NULL OR do.status = ?', My_Delivery_Order_Status::Deleted)
                ->where('campaign <> ?' , 99)
                ;

        }

        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array('d.name', 'd.title', 'd.mst_sn', 'd.unames', 'd.store_code', 'd.district'));

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

        if (isset($params['campaign_id']) and $params['campaign_id'])
        {
            if (is_array($params['campaign_id']) && count($params['campaign_id']))
                $select->where('p.campaign IN (?)', $params['campaign_id']);
            elseif (!is_array($params['campaign_id']) && $params['campaign_id'])
                $select->where('p.campaign = ?', $params['campaign_id']);
        }

        //for export get imei
        if (isset($params['get_imei']) and $params['get_imei'])
        {
            $select->join(array('i' => 'imei'), 'p.id = i.sales_id', array('i.imei_sn' , 'i.activated_date' , 'i.return_sn'));
        }


        if (isset($params['status']) and $params['status'])
            $select->where('p.status = ?', $params['status']);

        if (isset($params['text']) and $params['text'])
            $select->where('p.text LIKE ?', '%'.$params['text'].'%');

        if (isset($params['type']) and $params['type'])
            $select->where('p.type = ?', $params['type']);

        //get no accessories
        if (isset($params['no_accessories']) and $params['no_accessories'])
            $select->where('p.cat_id = ?', PHONE_CAT_ID);

        if (isset($params['sales_type']) and is_array($params['sales_type']))
            $select->where('p.type IN (?)', $params['sales_type']);

        if(isset($params['distributor_ka']) and  $params['distributor_ka'])
        {
            $distributorRowset = $QDistributor->find($params['distributor_ka']);
            $distributor       = $distributorRowset->current();

            $select->where('d.parent = ? ' , $distributor['id']);
        }


        if (isset($params['isbacks']) and $params['isbacks'])
            $select->where('p.isbacks = ?', 1);
        else
            $select->where('p.isbacks = ?', 0);

        if (isset($params['sn'])) {
            if (is_array($params['sn']) && count($params['sn']))
                $select->where('p.sn IN (?)', $params['sn']);
            elseif (!is_array($params['sn']) && $params['sn'])
                $select->where('p.sn LIKE ?', '%'.$params['sn'].'%');
        }

        if (isset($params['cat_id']) and $params['cat_id']) {
            if (is_array($params['cat_id']) && count($params['cat_id']) > 0) {
                $select->where('p.cat_id IN (?)', $params['cat_id']);
            } else {
                $select->where('p.cat_id = ?', $params['cat_id']);
            }
        }

        if (isset($params['warehouse_id']) and $params['warehouse_id']) {
            if (is_array($params['warehouse_id']) && count($params['warehouse_id']))
                $select->where('p.warehouse_id IN (?)', $params['warehouse_id']);

            elseif (is_numeric($params['warehouse_id']))
                $select->where('p.warehouse_id = ?', intval($params['warehouse_id']));
        }

        if (isset($params['user_id']) and $params['user_id'])
            $select->where('p.user_id = ?', $params['user_id']);

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('p.good_id = ?', $params['good_id']);

        if (isset($params['good_color']) and $params['good_color'])
            $select->where('p.good_color = ?', $params['good_color']);

        if (isset($params['d_id']) and $params['d_id'])
            $select->where('p.d_id = ?', $params['d_id']);

        if (isset($params['distributor_name']) and $params['distributor_name'])
            $select->where('d.title LIKE ?', '%'.$params['distributor_name'].'%');

        if (isset($params['invoice_number']) && $params['invoice_number']) {
            $select->where('p.invoice_number = ?', $params['invoice_number']);
        }

        if (isset($params['user_id']) && $params['user_id']) {
            $select->where('p.user_id = ?', $params['user_id']);
        }

        if (isset($params['distributor_po']) && $params['distributor_po'])
            $select->where('p.po_id = ?', $params['distributor_po']);

        // SALES_ADMIN thì chỉ xem đc đơn của khu vực mình
        if (isset($params['create_user_id']) && $params['create_user_id']) {
            $QAsm = new Application_Model_Asm();
            $asm_cache = $QAsm->get_cache();
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            if (isset($asm_cache[$params['create_user_id']]) && isset($asm_cache[$params['create_user_id']]['district'])) {
                $_district_arr = $asm_cache[$params['create_user_id']]['district'];

                if (is_array($_district_arr) && count($_district_arr))
                    $select->where('d.district IN (?) OR ( d.is_internal = 1 AND p.user_id = '.intval($userStorage->id).')', $_district_arr);
                elseif (is_numeric($_district_arr) && $_district_arr)
                    $select->where('d.district = ? OR ( d.is_internal = 1 AND p.user_id = '.intval($userStorage->id).')', intval($_district_arr));
            } else
                $select->where('1=0', 1);
        }

        if (isset($params['district_id'])) {
            if (is_array($params['district_id']) && count($params['district_id']))
                $select->where('d.district IN (?)', $params['district_id']);
            elseif (is_numeric($params['district_id']) && $params['district_id'])
                $select->where('d.district = ?', intval($params['district_id']));
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
                $select->where('d.district IN (?)', $district_arr);
            else
                $select->where('1=0', 1);

        } elseif (isset($params['area_id']) && $params['area_id']) {
            $district_arr = array();

            if (is_array($params['area_id']) && count($params['area_id'])) {
                foreach ($params['area_id'] as $_key => $_area_id)
                    $this->get_districts_by_area($_area_id, $district_arr);

            } elseif (is_numeric($params['area_id']) && $params['area_id']) {
                $this->get_districts_by_area($params['area_id'], $district_arr);
            }

            if (count($district_arr))
                $select->where('d.district IN (?)', $district_arr);
            else
                $select->where('1=0', 1);
        }

        if (isset($params['num']) and $params['num'])
            $select->where('p.num = ?', $params['num']);

        if (isset($params['canceled']))
            if ($params['canceled']==-1)
                $select->where('p.canceled = ? OR p.canceled IS NULL', 0);
            elseif ($params['canceled'])
                $select->where('p.canceled = ?', $params['canceled']);

        if (isset($params['price']) and $params['price'])
            $select->where('p.price = ?', $params['price']);

        if (isset($params['total']) and $params['total'])
            $select->where('p.total = ?', $params['total']);

        if (isset($params['add_time']) and $params['add_time'])
            $select->where('p.add_time = ?', $params['add_time']);

        if (isset($params['payment']) and $params['payment']){
            $select->where('p.pay_time IS NOT NULL');
            $select->where('p.pay_time <> \'\'');
            $select->where('p.pay_time <> 0');
        }

        if (isset($params['no_payment']) and $params['no_payment'])
            $select->where('p.pay_time IS NULL OR p.pay_time = \'\' OR p.pay_time = 0');

        if (isset($params['outmysql_time']) and $params['outmysql_time']){
            $select->where('p.outmysql_time IS NOT NULL');
            $select->where('p.outmysql_time <> \'\'');
            $select->where('p.outmysql_time <> 0');
        }

        if (isset($params['no_outmysql_time']) and $params['no_outmysql_time'])
            $select->where('p.outmysql_time IS NULL OR p.outmysql_time = \'\' OR p.outmysql_time = 0');

        if (isset($params['no_shipping']) and $params['no_shipping'])
            $select->where('p.shipping_yes_time IS NULL OR p.shipping_yes_time = \'\' OR p.shipping_yes_time = 0');

        //finance
        if (isset($params['finance']) and $params['finance']) {
            $select->where('p.pay_time IS NULL OR p.pay_time = \'\' OR p.pay_time = 0 OR p.shipping_yes_time IS NULL OR p.shipping_yes_time = \'\' OR p.shipping_yes_time = 0');
            $select->where('p.isbacks = ?', 0);
        }

        //finance return
        if (isset($params['finance_return']) and $params['finance_return']) {
            $select->where('p.pay_time IS NULL OR p.pay_time = \'\' OR p.pay_time = 0 OR p.shipping_yes_time IS NULL OR p.shipping_yes_time = \'\' OR p.shipping_yes_time = 0');
            $select->where('p.isbacks = ?', 1);
            $select->where('p.outmysql_time IS NULL OR p.outmysql_time = \'\' OR p.outmysql_time = 0');
        }

        //warehouse return
        if (isset($params['warehouse_return']) and $params['warehouse_return']) {
            $select->where('p.pay_time IS NOT NULL AND p.pay_time <> \'\' AND p.pay_time <> 0 AND p.shipping_yes_time IS NOT NULL AND p.shipping_yes_time <> \'\' AND p.shipping_yes_time <> 0');
            $select->where('p.isbacks = ?', 1);
            $select->where('p.outmysql_time IS NULL OR p.outmysql_time = \'\' OR p.outmysql_time = 0');
        }

        //warehouse out
        if (isset($params['warehouse_out']) and $params['warehouse_out']) {
            $select->where('p.pay_time IS NOT NULL AND p.pay_time <> \'\' AND p.pay_time <> 0 AND p.shipping_yes_time IS NOT NULL AND p.shipping_yes_time <> \'\' AND p.shipping_yes_time <> 0');
        }

        if (isset($params['product_out']) and $params['product_out']) {
            $select->where('p.invoice_time IS NULL OR p.print_time IS NULL OR p.invoice_number IS NULL');
        }

        if (isset($params['product_out_archived']) and $params['product_out_archived']) {
            $select->where('p.print_time IS NOT NULL');
            $select->where('p.invoice_time IS NOT NULL');
            $select->where('p.invoice_number IS NOT NULL');
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

        if (isset($params['invoice_time_from']) and $params['invoice_time_from']){
            list( $day, $month, $year ) = explode('/', $params['invoice_time_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.invoice_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['invoice_time_to']) and $params['invoice_time_to']){
            list( $day, $month, $year ) = explode('/', $params['invoice_time_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.invoice_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if (isset($params['out_time_from']) and $params['out_time_from']){
            list( $day, $month, $year ) = explode('/', $params['out_time_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.outmysql_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['out_time_to']) and $params['out_time_to']){
            list( $day, $month, $year ) = explode('/', $params['out_time_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.outmysql_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if(isset($params['po_id']) AND $params['po_id']){
            $select->where('p.po_id = ?',$params['po_id']);
        }

        if (isset($params['sort']) and $params['sort']) {
            $order_str = $collate = '';

            if (in_array($params['sort'], array('staff_username', 'd_id')))
                $collate .= ' COLLATE utf8_unicode_ci ';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            if ($params['sort'] == 'staff_username') {
                $select->join(array('s' => 'staff'), 's.id = p.user_id', array('s.username'));
                $params['sort'] = 's.username';
            } elseif ($params['sort'] == 'd_id') {
                $params['sort'] = 'd.title';
            } elseif ($params['sort'] == 'total_qty') {
                $params['sort'] = 'SUM(p.num)';
            } elseif ($params['sort'] == 'total_price') {
                $params['sort'] = 'SUM(p.total)';
            }

            $order_str .= $params['sort'] . $collate . $desc;

            $select->order(new Zend_Db_Expr($order_str));
        }



        if (isset($params['export']) and $params['export'])
            return $select->__toString();

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);

        if (isset($params['not_get_total']) and $params['not_get_total'])
            return $result;

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

    function count_out_imei($sn = '', $good_id = 0, $good_color = 0, $market_id = null ) {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => 'imei'),
                array('total' => 'COUNT(imei_sn)'))
            ->where('i.sales_sn = ?', $sn)
            ->where('   i.out_date IS NOT NULL AND i.out_date <> 0 AND i.out_date <> \'\'
                        AND i.into_date IS NOT NULL AND i.into_date <> 0 AND i.into_date <> \'\'
                    ');

        if($good_id != 0)
            $select->where('i.good_id = ?', $good_id);

        if($market_id)
            $select->where('i.sales_id = ?
                ', $market_id); //check null hok check nua

        if($good_color != 0)
            $select->where('i.good_color = ?', $good_color);

        $result = $db->fetchOne($select);

        return $result;
    }

    function count_out_digital($sn = '', $good_id = 0, $good_color = 0, $market_id = null ) {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('ds' => 'digital_sn'),
                array('total' => 'COUNT(*)'))
            ->where('ds.sales_sn = ?', $sn)
            ->where('   ( ds.out_date IS NOT NULL AND ds.out_date <> 0 AND ds.out_date <> \'\' )
                        AND ( ds.into_date IS NOT NULL AND ds.into_date <> 0 AND ds.into_date <> \'\' )
                    ');

        if($good_id != 0)
            $select->where('ds.good_id = ?', $good_id);

        if($market_id)
            $select->where('ds.sales_id = ?
                ', $market_id); //check null hok check nua

        if($good_color != 0)
            $select->where('ds.good_color = ?', $good_color);

        $result = $db->fetchOne($select);

        return $result;
    }

    function count_out_ilike($sn = '', $good_id = 0, $good_color = 0, $market_id = null ) {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('gs' => 'good_sn'),
                array('total' => 'COUNT(*)'))
            ->where('gs.sales_sn = ?', $sn)
            ->where('   ( gs.out_date IS NOT NULL AND gs.out_date <> 0 AND gs.out_date <> \'\' )
                        AND ( gs.into_date IS NOT NULL AND gs.into_date <> 0 AND gs.into_date <> \'\' )
                    ');

        if($good_id != 0)
            $select->where('gs.good_id = ?', $good_id);

        if($market_id)
            $select->where('gs.sales_id = ?
                ', $market_id); //check null hok check nua

        if($good_color != 0)
            $select->where('gs.good_color = ?', $good_color);

        $result = $db->fetchOne($select);

        return $result;
    }

    function count_out_imei2($from_date = '', $to_date = '', $good_id = 0, $good_color = 0, $market_id = null ) {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => 'imei'),
                array('total' => 'COUNT(imei_sn)'))
            /*->where('i.sales_sn = ?', $sn)*/
            ->where('   i.out_date IS NOT NULL AND i.out_date <> 0 AND i.out_date <> \'\'
                        AND i.into_date IS NOT NULL AND i.into_date <> 0 AND i.into_date <> \'\'
                    ');

        if($from_date)
            $select->where('i.out_date >= ?', $from_date.' 00:00:00');

        if($to_date)
            $select->where('i.out_date <= ?', $to_date.' 23:59:59');

        if($good_id != 0)
            $select->where('i.good_id = ?', $good_id);

        if($good_id != 0)
            $select->where('i.good_id = ?', $good_id);

        if($market_id)
            $select->where('i.sales_id = ?
                ', $market_id); //check null hok check nua

        if($good_color != 0)
            $select->where('i.good_color = ?', $good_color);



        $result = $db->fetchOne($select);


        return $result;
    }

    function count_phone($sn = '') {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => 'market'),
                array('count_phone' => 'SUM(i.num)'))
            ->where('i.sn = ?', $sn)
            ->where('i.status = ?', 1)
            ->where('i.cat_id = ?', PHONE_CAT_ID);

        $result = $db->fetchOne($select);

        return $result;
    }

    function count_digital($sn = '') {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => 'market'),
                array('count_digital' => 'SUM(i.num)'))
            ->where('i.sn = ?', $sn)
            ->where('i.status = ?', 1)
            ->where('i.cat_id = ?', DIGITAL_CAT_ID);

        $result = $db->fetchOne($select);

        return $result;
    }

    function count_ilike($sn = '') {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => 'market'),
                array('count_ilike' => 'SUM(i.num)'))
            ->where('i.sn = ?', $sn)
            ->where('i.status = ?', 1)
            ->where('i.cat_id = ?', ILIKE_CAT_ID);

        $result = $db->fetchOne($select);

        return $result;
    }

    function get_print_no($sn)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name),
                array('number'=>'p.print_no'))
            ->where('p.sn = ?', $sn);

        $result = $db->fetchOne($select);

        if (isset($result) && $result > 0) {
            return $result;
        }

        return 0;
    }

    function get_print_no_max($sn)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name),
                array('number'=>'MAX(p.print_no)'))
            ->where('p.add_time >= ?', date('Y-m-d'))
            ->where('p.add_time <= ?', date('Y-m-d 23:59:59'));

        $result = $db->fetchOne($select);

        if (isset($result) && $result > 0) {
            return $result;
        }

        return 0;
    }

    function get_print_time($sn)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.print_time'))
            ->where('p.sn = ?', $sn);

        $result = $db->fetchOne($select);

        return $result;
    }

    function report($page, $limit, &$total, $params)
    {
        $db = Zend_Registry::get('db');

        $select = $db->select();

        if (isset($params['group_sn']) and $params['group_sn'])
            $select
                ->from(array('p' => $this->_name),
                    array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'total_qty' => 'SUM(p.num)', 'total_price' => 'SUM(p.total)', 'p.*'))
                ->group('p.sn');

        elseif ( isset($params['sales_in']) and $params['sales_in'] )
            $select->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SUM(p.num) AS total_qty_all'), new Zend_Db_Expr('SUM(p.total) AS total_price_all')));


        else
            $select->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['get_imei']) and $params['get_imei'])
            $select->join(array('i' => 'imei'), 'p.id = i.sales_id', array('i.imei_sn','i.activated_date'));

        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array('d.name', 'd.title', 'd.store_code', 'd.district'));

        if (isset($params['product_out']) and $params['product_out']) {
            $select->where('p.invoice_time IS NULL OR p.print_time IS NULL OR p.invoice_number IS NULL');
        }

        if (isset($params['product_out_archived']) and $params['product_out_archived']) {
            $select->where('p.print_time IS NOT NULL');
            $select->where('p.invoice_time IS NOT NULL');
            $select->where('p.invoice_number IS NOT NULL');
        }

        if (isset($params['status']) and $params['status'])
            $select->where('p.status = ?', $params['status']);


        if (isset($params['isbacks']) and $params['isbacks'])
            $select->where('p.isbacks = ?', 1);
        else
            $select->where('p.isbacks = ?', 0);

        if (isset($params['sn']) and $params['sn'])
            $select->where('p.sn LIKE ?', '%'.$params['sn'].'%');

        if (isset($params['cat_id']) and $params['cat_id'])
            $select->where('p.cat_id = ?', $params['cat_id']);

        /////////////////////////////////////
        if (isset($params['warehouse_id']) and $params['warehouse_id']) {
            if (is_array($params['warehouse_id']) && count($params['warehouse_id']))
                $select->where('p.warehouse_id IN (?)', $params['warehouse_id']);

            elseif (is_numeric($params['warehouse_id']))
                $select->where('p.warehouse_id = ?', intval($params['warehouse_id']));
        }

        if (isset($params['user_id']) and $params['user_id'])
            $select->where('p.user_id = ?', $params['user_id']);

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('p.good_id = ?', $params['good_id']);

        if (isset($params['good_color']) and $params['good_color'])
            $select->where('p.good_color = ?', $params['good_color']);

        if (isset($params['d_id']) and $params['d_id'])
            $select->where('p.d_id = ?', $params['d_id']);

        if (isset($params['distributor_name']) and $params['distributor_name'])
            $select->where('d.name LIKE ?', '%'.$params['distributor_name'].'%');

        if (isset($params['num']) and $params['num'])
            $select->where('p.num = ?', $params['num']);

        if (isset($params['price']) and $params['price'])
            $select->where('p.price = ?', $params['price']);

        if (isset($params['total']) and $params['total'])
            $select->where('p.total = ?', $params['total']);

        if (isset($params['add_time']) and $params['add_time'])
            $select->where('p.add_time = ?', $params['add_time']);

        if (isset($params['payment']) and $params['payment']){
            $select->where('p.pay_time IS NOT NULL');
            $select->where('p.pay_time <> \'\'');
            $select->where('p.pay_time <> 0');
        }

        if (isset($params['no_payment']) and $params['no_payment'])
            $select->where('p.pay_time IS NULL OR p.pay_time = \'\' OR p.pay_time = 0');

        if (isset($params['outmysql_time']) and $params['outmysql_time']){
            $select->where('p.outmysql_time IS NOT NULL');
            $select->where('p.outmysql_time <> \'\'');
            $select->where('p.outmysql_time <> 0');
        }

        if (isset($params['no_outmysql_time']) and $params['no_outmysql_time'])
            $select->where('p.outmysql_time IS NULL OR p.outmysql_time = \'\' OR p.outmysql_time = 0');

        if (isset($params['no_shipping']) and $params['no_shipping'])
            $select->where('p.shipping_yes_time IS NULL OR p.shipping_yes_time = \'\' OR p.shipping_yes_time = 0');

        //finance
        if (isset($params['finance']) and $params['finance']) {
            $select->where('p.pay_time IS NULL OR p.pay_time = \'\' OR p.pay_time = 0 OR p.shipping_yes_time IS NULL OR p.shipping_yes_time = \'\' OR p.shipping_yes_time = 0');
            $select->where('p.isbacks = ?', 0);
        }

        //finance return
        if (isset($params['finance_return']) and $params['finance_return']) {
            $select->where('p.pay_time IS NULL OR p.pay_time = \'\' OR p.pay_time = 0 OR p.shipping_yes_time IS NULL OR p.shipping_yes_time = \'\' OR p.shipping_yes_time = 0');
            $select->where('p.isbacks = ?', 1);
            $select->where('p.outmysql_time IS NULL OR p.outmysql_time = \'\' OR p.outmysql_time = 0');
        }

        //warehouse return
        if (isset($params['warehouse_return']) and $params['warehouse_return']) {
            $select->where('p.pay_time IS NOT NULL AND p.pay_time <> \'\' AND p.pay_time <> 0 AND p.shipping_yes_time IS NOT NULL AND p.shipping_yes_time <> \'\' AND p.shipping_yes_time <> 0');
            $select->where('p.isbacks = ?', 1);
            $select->where('p.outmysql_time IS NULL OR p.outmysql_time = \'\' OR p.outmysql_time = 0');
        }

        //warehouse out
        if (isset($params['warehouse_out']) and $params['warehouse_out']) {
            $select->where('p.pay_time IS NOT NULL AND p.pay_time <> \'\' AND p.pay_time <> 0 AND p.shipping_yes_time IS NOT NULL AND p.shipping_yes_time <> \'\' AND p.shipping_yes_time <> 0');
        }

        if (isset($params['district_id']) && $params['district_id']) {
            $select->where('d.district = ?', $params['district_id']);

        } elseif (isset($params['region_id']) && $params['region_id']) {
            $QRegion = new Application_Model_RegionalMarket();
            $district_cache = $QRegion->get_district_by_province_cache($params['region_id']);
            $district_arr = array();

            if ($district_cache)
                foreach ($district_cache as $key => $value)
                    $district_arr[] = $key;

            if (count($district_arr))
                $select->where('d.district IN (?)', $district_arr);
            else
                $select->where('1=0', 1);

        } elseif (isset($params['area_id']) && $params['area_id']) {
            $QRegion = new Application_Model_RegionalMarket();
            $district_cache = $QRegion->get_district_by_area_cache($params['area_id']);

            if ($district_cache && count($district_cache))
                $select->where('d.district IN (?)', $district_cache);
            else
                $select->where('1=0', 1);
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

        if (isset($params['out_time_from']) and $params['out_time_from']){
            list( $day, $month, $year ) = explode('/', $params['out_time_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.outmysql_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['out_time_to']) and $params['out_time_to']){
            list( $day, $month, $year ) = explode('/', $params['out_time_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.outmysql_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if (isset($params['sort']) and $params['sort']) {
            $order_str = $collate = '';

            if (in_array($params['sort'], array('staff_username', 'd_id')))
                $collate .= ' COLLATE utf8_unicode_ci ';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            if ($params['sort'] == 'staff_username') {
                $select->join(array('s' => 'staff'), 's.id = p.user_id', array('s.username'));
                $params['sort'] = 's.username';
            } elseif ($params['sort'] == 'd_id') {
                $params['sort'] = 'd.title';
            } elseif ($params['sort'] == 'total_qty') {
                $params['sort'] = 'SUM(p.num)';
            } elseif ($params['sort'] == 'total_price') {
                $params['sort'] = 'SUM(p.total)';
            }

            $order_str .= $params['sort'] . $collate . $desc;

            $select->order(new Zend_Db_Expr($order_str));
        }

        return $select->__toString();

    }

    function get_current_order( $warehouse_id=null, $good_id=null, $good_color=null, $id = null ){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('m' => $this->_name),
                array(new Zend_Db_Expr('SUM( m.`num` ) ')));

        $select->where('m.isbacks = ?', 0);
        $select->where('m.status = ?', 1);
        $select->where('m.outmysql_time IS NULL OR m.outmysql_time = 0 OR m.outmysql_time = \'\'', null);

        if ($warehouse_id)
            $select->where('m.warehouse_id = ?', $warehouse_id);

        if ($good_id)
            $select->where('m.good_id = ?', $good_id);

        if ($good_color)
            $select->where('m.good_color = ?', $good_color);

        if ($id)
            $select->where('m.id = ?', $id);


        $total = $db->fetchOne($select);

        return $total;
    }

    public function getPrice($sn)
    {
         $db = Zend_Registry::get('db');
         $select = $db->select()
            ->from(array('p' => 'market'),
                array('total_price' => 'SUM(p.total)'))
            ->where('p.sn = ?', $sn)
            ->where('p.status = 1', null);

         $result = $db->fetchOne($select);
         return $result;
    }

    public function fetchWithImei($sn)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->distinct()
            ->from(array('m' => $this->_name), array('market_id' => 'm.id', 'm.sn', 'm.good_id', 'm.good_color', 'm.num', 'm.price', 'm.d_id', 'm.total'))
            ->join(array('i' => 'imei'), 'm.id=i.sales_id', array('imei_id' => 'i.id', 'i.imei_sn'));

        if (!$sn)
            return false;

        $select->where('CAST(m.sn AS CHAR) = ?', $sn);

        return $db->fetchAll($select);
    }
}
