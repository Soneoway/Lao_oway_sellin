<?php
class Application_Model_Market extends Zend_Db_Table_Abstract
{
    protected $_name = 'market';

    function getRecordBySN($sn){
        if($sn =='') return false;
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => $this->_name), array('i.*'));
        $select->where('i.sn = ?',$sn);
        return $db->fetchRow($select);
    }
    //Tanong
    public function fetchInvoice($params)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> 'market'),array('p.id','SUM(p.num) AS total_qty','SUM(p.total) AS total_price','p.invoice_number','p.type','p.print_picking_list','p.service','p.*'))
        ->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array('d.name','d.title','d.mst_sn','d.unames','d.store_code','d.district'))
        ->joinLeft(array('g' => 'good'), 'g.id=p.good_id', array('g.*'));
        $select->where('p.sn = ?', $params['sn'])
        ->group(new Zend_Db_Expr('p.id,p.sn,p.good_id,p.good_color'));
        $select->order('p.good_id');
        //echo $select;die;
        return $db->fetchAll($select);
    }


    //Tanong
    public function getInvoiceByDistributor($d_id)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> 'market'),array('p.sn','p.sn_ref','p.invoice_number',"concat(p.invoice_number,'/',p.sn_ref) as invoice_number_show"));
        $select->where('p.d_id = ?', $d_id);
        $select->where('p.invoice_number IS NOT NULL', null);
        $select->where('p.isbacks !=1', null);
        $select->group(new Zend_Db_Expr('p.sn'));
        $select->order('p.sn desc');
        //echo $select;die;
        return $db->fetchAll($select);
    }

    //Tanong
    public function getCustomerBrandShop($customer_id)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> 'customer_brandshop'),array('p.*'));
        $select->where('p.status =1', null);
        if($customer_id !=''){
            $select->where('p.customer_id =?', $customer_id);
        }
        $select->order('p.create_date desc');
        //echo $select;die;
        return $db->fetchAll($select);
    }

    //Tanong
    public function LoadSalesOrderAmount($d_id)
    {
        $db = Zend_Registry::get('db');
        //$d_id='10326';
        $select = $db->select()
        ->from(array('p'=> 'market'),array('COUNT(p.id)AS rec','p.id','IFNULL(SUM(p.total),0) AS total_acmount','IFNULL(SUM(p.delivery_fee),0) as delivery_fee'));
        $select->where('(1=1) AND (p.isbacks = 0)', null);
        $select->where("DATE_FORMAT(p.add_time,'%Y%m%d')=DATE_FORMAT(NOW(),'%Y%m%d')", null);
        $select->where('p.d_id = ?', $d_id);
        //echo($select);die;
        return $db->fetchAll($select);
        
    }

    //Tanong
    public function LoadProductSalesOrder($d_id,$sales_sn,$good_id,$order_date)
    {
        $db = Zend_Registry::get('db');
        //$d_id='10326';
        $select = $db->select()
        ->from(array('p'=> 'market'),array('ifnull(sum(p.num),0)AS qty'));
        $select->where('(1=1) AND (p.isbacks = 0)', null);
        
        $select->where('p.d_id = ?', $d_id);
        $select->where('p.good_id = ?', $good_id);
        if($sales_sn !=''){
            $select->where('p.sn != ?', $sales_sn);
            $select->where("DATE_FORMAT(p.add_time,'%Y%m%d')=DATE_FORMAT('".$order_date."','%Y%m%d')", null);
        }else{
            $select->where("DATE_FORMAT(p.add_time,'%Y%m%d')=DATE_FORMAT(NOW(),'%Y%m%d')", null);
        }
        //echo($select);die;
        return $db->fetchAll($select);
        
    }

    public function LoadDistributorNoCheckLimit($d_id,$good_id)
    {
        $db = Zend_Registry::get('db');
        //$d_id='10326';
        $select = $db->select()
        ->from(array('p'=> 'check_order_limit'),array('count(p.d_id)as chk'));
        $select->where('p.d_id = ?', $d_id);
        $select->where('p.good_id = ?', $good_id);
        $select->where('p.active = ?', 1);
        //echo($select);die;
        return $db->fetchAll($select);
        
    }

    
    function fetchPagination($page, $limit, &$total, $params){
       // print_r($params);die;
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');

        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

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
            //Tanong Edit total_price - discount by credit note  20160313
            $select_fields = array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'),'user_id', 'sn_ref' => 'sn_ref',  'total_qty' => 'SUM(p.num)', 'total_price_amount' => 'SUM( ROUND( ( (p.total/p.num)/1.07 ),2) * p.num )','delivery_fee'
                , 'total_price' => 'SUM( ROUND( ( (p.total/p.num)/1.07 ),2) * p.num ) - IFNULL((CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END),0) + IFNULL(p.delivery_fee,0) AS total_price'
                ,'invoice_number', 'type', 'print_picking_list', 'service','confirm_so','sale_off_percent','p.sales_confirm_date','shipping_address','customer_name' 
                ,'(CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END) AS total_discount','p.canceled_by','total_spc_discount');


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

            } else {
                array_push($select_fields, 'p.*');
            }
                

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
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'), 'total_price' => 'SUM(p.total)','num' => 'SUM(p.num)', 'good_id','cat_id','price','sn','good_color','sale_off_percent','type','good.desc', 'p.total' , 'good.desc_name', 'p.campaign'));
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
            $select->joinLeft('good','p.good_id = good.id');
            $select->group ( 'p.good_id' );
            $select->group ( 'p.good_color' );

            $select->group(new Zend_Db_Expr("case when p.campaign <> 0 then p.total else good.desc end"));
            $select->group(new Zend_Db_Expr("good.desc"));
            $select->order ( 'good.cat_id ASC' );



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
                array_push($select_fields, 'sn_ref');

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

        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array('d.name', 'd.title','d.rank', 'd.mst_sn', 'd.unames', 'd.store_code', 'd.district','d.add_tax','d.region','p.confirm_access_status','p.confirm_access_remark','p.confirm_access_by','p.order_accessories'));


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
            $select->join(array('i' => 'imei'), 'p.sn = i.sales_sn', array('i.imei_sn' , 'i.activated_date'));

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


        if (isset($params['isbacks']) and $params['isbacks'])
            $select->where('p.isbacks = ?', 1);
        else
            $select->where('p.isbacks = ?', 0);

        if (isset($params['creditnote_sn']) and $params['creditnote_sn'])
            $select->where('p.creditnote_sn = ?', $params['creditnote_sn']);

        if(isset($params['view_accessories']) and $params['view_accessories']=='all'){
            $select->where('p.order_accessories = ?', 1);
        }else if (isset($params['view_accessories']) and $params['view_accessories']=='wait'){
            $select->where('p.order_accessories = ?', 1);
            $select->where('p.confirm_access_date is null', null);
        }else{
            $select->where('p.sn not in(SELECT m.sn FROM market m WHERE m.order_accessories =1 AND m.confirm_access_date IS NULL GROUP BY m.sn)',null);
        }

        if (isset($params['create_cn']) and $params['create_cn']=='1'){
            $select->where('p.create_cn = ?', 1);
            $select->where('p.creditnote_sn is not null', null);
        }else if (isset($params['create_cn']) and $params['create_cn']=='2'){
            $select->where('p.create_cn is null', null);
            $select->where('p.creditnote_sn is null', null);
        }

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


        if (isset($params['cancel'])){
            if ($params['cancel']==1){
                $select->where('p.canceled = ?', 1);
            }
            else if ($params['cancel']==0){
                $select->where('p.canceled <> ?', 1);
            }
        }

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
           // $select->where('p.warehouse_id <> 90', null);
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

            if($userStorage->warehouse_type =='3'){
              $select->where('p.sales_confirm_date IS NOT NULL AND p.sales_confirm_id IS NOT NULL');
            }else{
              $select->where('p.pay_time IS NOT NULL AND p.pay_time <> \'\' AND p.pay_time <> 0 AND p.shipping_yes_time IS NOT NULL AND p.shipping_yes_time <> \'\' AND p.shipping_yes_time <> 0');  
            }
            
            $select->where('p.canceled IS NULL OR p.canceled !=1');

            if($userStorage->id =='106' || $userStorage->id =='216' || $userStorage->id =='233' || $userStorage->id =='227')
            {
                //$select->where('p.warehouse_id =90');
            }
        }

        if (isset($params['product_out']) and $params['product_out']) {


            if (isset($params['sn']) and $params['sn']) {

            }else{
              $select->where('p.invoice_time IS NULL OR p.print_time IS NULL OR p.invoice_number IS NULL');
            }
            $select->where('p.canceled IS NULL OR p.canceled !=1');  

            if($userStorage->id =='106' || $userStorage->id =='216' || $userStorage->id =='233' || $userStorage->id =='227')
            {
               // $select->where('p.warehouse_id =90');
            }

        }

        if($userStorage->id =='106' || $userStorage->id =='216' || $userStorage->id =='233' || $userStorage->id =='227')
            {
               // $select->where('p.warehouse_id =90');
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

        // [Sak] : add filter Finance Confirm Time 
        
        if (isset($params['finance_confirm_time_from']) and $params['finance_confirm_time_from']){
            list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.pay_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['finance_confirm_time_to']) and $params['finance_confirm_time_to']){
            list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.pay_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
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
//tanong
        if(isset($params['cancel']) AND $params['cancel']){
            $select->where('p.canceled = ?',$params['cancel']);
        }

        if(isset($params['cancel']) AND $params['cancel']){
            $select->where('p.canceled = ?',$params['cancel']);
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

        //$select->where('1=1');

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

        if($userStorage->group_id !='7'){
            if($userStorage->catty_staff_id !=''){
                $catty_staff_id = $userStorage->catty_staff_id;
                $select->where('d.region in(SELECT rm.id AS region_id
                                FROM hr.`asm` asm
                                LEFT JOIN hr.`regional_market` rm ON asm.area_id=rm.area_id
                                WHERE asm.`staff_id`=?)',$catty_staff_id);
            }
        }

        if($limit)
                $select->limitPage($page, $limit);

        // show sales catty
        $select->joinLeft(array('s' => HR_DB.'.staff'), 's.id=p.sales_catty_id', array('s.id as sales_catty_id','s.code as sales_catty_code', "TRIM(CONCAT(s.firstname,' ',s.lastname, '[',s.email,']'))AS sales_catty_name",new Zend_Db_Expr("(SELECT t.`name` 
  FROM tag_object tg
  LEFT JOIN tag t ON tg.`tag_id`=t.id
  WHERE tg.`object_id`=p.`sn` LIMIT 1)AS tax_po")));

         

        if (isset($params['export']) and $params['export'])
        {
            //WhereExport
            switch ($params['export']) {
                case 7: //Output VAT Report
                     $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time'));
                     $select->joinLeft(array('c' => 'customer_brandshop'), 'p.customer_id = c.customer_id'
                        , array(new Zend_Db_Expr("( 
                CASE 
                    WHEN p.d_id = 3691 OR NOW() >= '2016-04-18 00:00:00' THEN 
                        SUM( ROUND( ( (p.total/p.num)/1.07 ),2) * p.num )-ifnull(p.total_spc_discount,0) + ROUND((p.delivery_fee/1.07),2) 
                    WHEN p.sn_ref = 'SO590407-00082' OR p.sn_ref = 'SO590408-00294' THEN
                        SUM( ROUND( ( (p.total/p.num)/1.07 ),2) * p.num )-ifnull(p.total_spc_discount,0) + ROUND((p.delivery_fee/1.07),2) 
                    WHEN p.sn_ref = 'SO590405-00222' THEN 
                        SUM( ROUND( ( p.total/1.07 ),2) )-ifnull(p.total_spc_discount,0) + ROUND((p.delivery_fee/1.07),2)
                    WHEN p.d_id = 21088 THEN 
                        SUM( ROUND( ( (p.total/p.num)/1.07 ),2) * p.num )-ifnull(p.total_spc_discount,0) + ROUND((p.delivery_fee/1.07),2)     
                    ELSE 
                        SUM( ROUND( ( p.total/1.07 ),2) ) -ifnull(p.total_spc_discount,0) + ROUND((p.delivery_fee/1.07),2)
                END ) as sum_total, c.customer_name,p.total_spc_discount,p.spc_discount")));

                    // $select->where('p.d_id not in(?)','21088');
                     $select->group('p.sn');
                    break;
                case 8: //Order Status
                     $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time','SUM(p.total) as sum_total'));

                     $select->group('p.sn');
                    break;
                case 10: //Export By Distributor
                     $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time','SUM(p.num) as sum_num','SUM(p.total) as sum_total'));

                     $select->where('p.cat_id = 11');
                     $select->group('p.sn');
                    break;
                case 11: //Sale Master Data
                      $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time'));
                      $select->joinLeft(array('i' => 'imei'), 'p.sn = i.sales_sn 
                    AND p.good_id <> 127 
                    AND p.cat_id = 11 
                    AND p.good_id = i.good_id 
                    AND p.good_color = i.good_color'
                        , array(new Zend_Db_Expr("i.imei_sn")));
                    break;
                default:
                    $select->joinLeft(array('rm' => 'hr.regional_market'), 's.regional_market = rm.id', array('a.name as sales_area','(SELECT 
    ROUND(SUM(ROUND((IF(mm.sale_off_percent >0,ROUND((((mm.price*mm.sale_off_percent/100)*100)/100),2),mm.price)/1.07),2)*mm.num*1.07),2)
  FROM
    market mm 
  WHERE mm.sn = p.`sn`  GROUP BY mm.sn) AS `grand_total`'
,'(SELECT 
    ROUND((if(mm.sale_off_percent >0,ROUND((((mm.price*mm.sale_off_percent/100)*100)/100),2),mm.price)/1.07),2)*mm.num
  FROM
    market mm 
  WHERE mm.sn = p.`sn` and p.`good_id`=mm.good_id and p.`good_color`=mm.good_color group by mm.sn) AS `total_amount_ex_vat`'
  ,'  (SELECT 
    ROUND(ROUND(ROUND((IF(mm.sale_off_percent >0,ROUND((((mm.price*mm.sale_off_percent/100)*100)/100),2),mm.price)/1.07),2)*mm.num,2)*1.07,2)
  FROM
    market mm 
  WHERE mm.sn = p.`sn` AND p.`good_id`=mm.good_id AND p.`good_color`=mm.good_color GROUP BY mm.sn) AS `total_amount_in_vat` '
  ));
             $select->joinLeft(array('a' => 'hr.area'), 'rm.area_id = a.id', null);
                    break;
            }

             //echo $select;die;
            return $select->__toString();
        }else{
             $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time'));



            $select->order('p.cat_id asc');
          //  echo $select;die;
            $result = $db->fetchAll($select);
        }

        //echo $select;die; 
        // $result = $db->fetchAll($sql);

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

    function report_product_out_by_imei($page, $limit, &$total, $params)
    {
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

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
            $select->join(array('i' => 'imei'), 'p.sn = i.sales_sn', array('i.imei_sn','i.activated_date'));

        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array('d.name', 'd.title', 'd.store_code', 'd.district'));

        $select->join(array('i' => 'imei'), 'p.sn = i.sales_sn AND i.distributor_id = p.d_id AND i.good_id=p.good_id AND i.good_color=p.good_color' 
            , array('i.imei_sn','i.activated_date'));

        if (isset($params['product_out']) and $params['product_out']) {
            $select->where('p.invoice_time IS NULL OR p.print_time IS NULL OR p.invoice_number IS NULL');
            $select->where('p.canceled !=1');
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

        //Tanong
        if (isset($params['sn']) and $params['sn'])
            $select->where('p.sn LIKE ? or p.sn_ref LIKE ?', '%'.$params['sn'].'%');

        if (isset($params['cat_id']) and $params['cat_id'])
            $select->where('p.cat_id = ?', $params['cat_id']);

        /////////////////////////////////////
        // check filter Warehouse
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

            if($userStorage->warehouse_type =='3'){
              $select->where('p.sales_confirm_date IS NOT NULL AND p.sales_confirm_id IS NOT NULL');
            }else{
              $select->where('p.pay_time IS NOT NULL AND p.pay_time <> \'\' AND p.pay_time <> 0 AND p.shipping_yes_time IS NOT NULL AND p.shipping_yes_time <> \'\' AND p.shipping_yes_time <> 0');  
            }
            
            $select->where('p.canceled IS NULL OR p.canceled !=1');

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

        if (isset($params['finance_confirm_time_from']) and $params['finance_confirm_time_from']){
            list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.pay_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['finance_confirm_time_to']) and $params['finance_confirm_time_to']){
            list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.pay_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
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
        //echo $select;die;
        //$result = $db->fetchAll($select);
        // return $result;
        return $select->__toString();

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
            $select->join(array('i' => 'imei'), 'p.sn = i.sales_sn', array('i.imei_sn','i.activated_date'));

        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array('d.name', 'd.title', 'd.store_code', 'd.district'));

        if (isset($params['product_out']) and $params['product_out']) {
            $select->where('p.invoice_time IS NULL OR p.print_time IS NULL OR p.invoice_number IS NULL');
            $select->where('p.canceled !=1');
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

        //Tanong
        if (isset($params['sn']) and $params['sn'])
            $select->where('p.sn LIKE ? or p.sn_ref LIKE ?', '%'.$params['sn'].'%');

        if (isset($params['cat_id']) and $params['cat_id'])
            $select->where('p.cat_id = ?', $params['cat_id']);

        /////////////////////////////////////
        // check filter Warehouse
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

            if($userStorage->warehouse_type =='3'){
              $select->where('p.sales_confirm_date IS NOT NULL AND p.sales_confirm_id IS NOT NULL');
            }else{
              $select->where('p.pay_time IS NOT NULL AND p.pay_time <> \'\' AND p.pay_time <> 0 AND p.shipping_yes_time IS NOT NULL AND p.shipping_yes_time <> \'\' AND p.shipping_yes_time <> 0');  
            }
            
            $select->where('p.canceled IS NULL OR p.canceled !=1');

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

        if (isset($params['finance_confirm_time_from']) and $params['finance_confirm_time_from']){
            list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.pay_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['finance_confirm_time_to']) and $params['finance_confirm_time_to']){
            list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.pay_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
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
        //echo $select;die;
        $result = $db->fetchAll($select);
         return $result;
       // return $select->__toString();

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

    //Tanong
    /*
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
    */
    //Tanong
    public function getPrice_bk($sn)
    {
         $db = Zend_Registry::get('db');
         $select = $db->select()
            ->from(array('p' => 'market'),array('total_price' => 'SUM(p.total) - ifnull(cn.use_discount,0) + ifnull(p.delivery_fee,0) ','delivery_fee'=>'ifnull(p.delivery_fee,0)'))
            ->joinLeft(array('cn' => 'credit_note_tran'), 'cn.sales_order=p.sn', array('ifnull(cn.use_discount,0) AS total_discount'))
            ->where('p.sn = ?', $sn)
            ->where('p.status = 1', null);
            
         $result = $db->fetchOne($select);
        
         return $result;
    }

    public function getPrice($sn)
    {
         $db = Zend_Registry::get('db');
         $select = $db->select()
            ->from(array('p' => 'market')
                ,array('total_price' => 'SUM(p.total) - IFNULL((CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END),0) + IFNULL(p.delivery_fee,0)'
                ,'delivery_fee'=>'ifnull(p.delivery_fee,0)','(CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END) AS total_discount'))
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
            ->join(array('i' => 'imei'), 'm.sn=i.sales_sn AND m.good_id=i.good_id and m.good_color = i.good_color ', array('imei_id' => 'i.id', 'i.imei_sn'));
//->join(array('i' => 'imei'), 'm.id=i.sales_id', array('imei_id' => 'i.id', 'i.imei_sn'));
        if (!$sn)
            return false;

        $select->where('CAST(m.sn AS CHAR) = ?', $sn);
        //echo $select;die;
        return $db->fetchAll($select);
    }

    
    function so_stock_card($params) {
        // check filter Warehouse
        /*
        $add_filter = "";
        if ( isset($params['warehouse_id']) && $params['warehouse_id'] ) {

            if (is_array($params['warehouse_id']))
                $add_filter = " AND m.warehouse_id IN (".implode(",",$params['warehouse_id']).")";
            else
                $add_filter = " AND m.warehouse_id = ".$params['warehouse_id'];
        }
        */
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('m' => $this->_name),
                array(
                    'created_date'      => 'm.outmysql_time',
                    'document_number'   => 'm.sn',
                    'document_ref'      => 'm.sn_ref',
                    'invoice_number'    => 'm.invoice_number',
                    'invoice_time'      => 'm.invoice_time',
                    'document_type'     => new Zend_Db_Expr($db->quote('SO')),
                    'out_amount'        => 'm.num',
                    'out_total_cost'    => 'm.price'
                ))
            ->joinLeft(array('w'  => 'warehouse')       ,'m.warehouse_id = w.id',array('wh_from' => 'w.name','m.warehouse_id'))
            ->joinLeft(array('go' => 'good')            ,'m.good_id = go.id'        ,array('out_cost' => 'go.price_4','product_name' => 'go.name'))
            ->joinLeft(array('g'  => 'good_color')      ,'m.good_color = g.id'      ,array('product_color' => 'g.name'))
            ->joinLeft(array('gc' => 'good_category')   ,'m.cat_id = gc.id'         ,array('category' => 'gc.name','m.good_id','m.good_color','m.cat_id'))
            ->joinInner(array('d'  => 'distributor')     ,'m.d_id = d.id'            ,array('wh_to' => 'd.title'))
            ->where('m.isbacks = 0')

            ->where('m.old_data is null')
            ->where('m.outmysql_time IS NOT NULL')
            ->where("m.outmysql_time <> '' ")
            ->where('m.outmysql_time <> 0')
            ->group(array('m.sn','m.good_id','m.good_color'))
            ->order('m.outmysql_time ASC');

        if ( isset($params['warehouse_id']) && $params['warehouse_id'] ) {
            if (is_array($params['warehouse_id']))
                $select->where("m.warehouse_id IN (".implode(",",$params['warehouse_id']).")",null);
            else
                $select->where("m.warehouse_id = ?",$params['warehouse_id']);
        }    

        $from = explode('/', $params['from']);
        $to = explode('/', $params['to']);
        // Filter Data From - To
        if (isset($params['from']) && $params['from'] && !isset($params['to'])) {
            $select->where( 'm.outmysql_time >= ?', $from[2].'-'.$from[1].'-'.$from[0] . ' 00:00:00' );
        } elseif (isset($params['to']) && $params['to'] && !isset($params['from'])) {
            $select->where( 'm.outmysql_time <= ?', $to[2].'-'.$to[1].'-'.$to[0] . ' 23:59:59' );
        } elseif (isset($params['to']) && $params['to'] && isset($params['from']) && $params['from'] ) {
            $select->where( 'm.outmysql_time >= ?', $from[2].'-'.$from[1].'-'.$from[0] . ' 00:00:00' );
            $select->where( 'm.outmysql_time <= ?', $to[2].'-'.$to[1].'-'.$to[0] . ' 23:59:59' );
        }

        // Filter 
        if ( isset($params['cat_id']) && $params['cat_id'] ) {

            if (is_array($params['cat_id']))
                $select->where( 'm.cat_id IN (?)', $params['cat_id']);
            else
                $select->where( 'm.cat_id = ?', $params['cat_id']);
        }
        if ( isset($params['good_id']) && $params['good_id'] ) {

            if (is_array($params['good_id']))
                $select->where( 'm.good_id IN (?)', $params['good_id']);
            else
                $select->where( 'm.good_id = ?', $params['good_id']);
        }
        if ( isset($params['color_id']) && $params['color_id'] ) {

            if (is_array($params['color_id']))
                $select->where( 'm.good_color IN (?)', $params['color_id']);
            else
                $select->where( 'm.good_color = ?', $params['color_id']);
        }
        
        // check warehouse type
        if ( isset($params['warehouse_type']) && $params['warehouse_type'] ) {
            $select->where( 'w.id IN ( SELECT id FROM warehouse WHERE warehouse_type IN ('.$params['warehouse_type'].') )', null);
        }

/*
        if ( isset($params['warehouse_id']) && $params['warehouse_id'] ) {

            if (is_array($params['warehouse_id']))
                $select->where( 'm.warehouse_id IN (?)', $params['warehouse_id']);
            else
                $select->where( 'm.warehouse_id = ?', $params['warehouse_id']);
        }
*/
        //echo $select; 
        $result = $db->fetchAll($select);

        return $result;
    }

    function return_stock_card($params) {
        // check filter Warehouse
        /*
        $add_filter = "";
        if ( isset($params['warehouse_id']) && $params['warehouse_id'] ) {

            if (is_array($params['warehouse_id']))
                $add_filter = " AND m.warehouse_id IN (".implode(",",$params['warehouse_id']).")";
            else
                $add_filter = " AND m.warehouse_id = ".$params['warehouse_id'];
        }
        */
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('m' => $this->_name),
                array(
                    'created_date'      => 'm.outmysql_time',
                    'document_number'   => 'm.sn',
                    'document_ref'      => 'm.sn_ref',
                    'document_type'     => new Zend_Db_Expr($db->quote('Return')),
                    'in_amount'         => 'm.num',
                    'in_total_cost'     => 'm.price'
                ))
            ->joinLeft(array('w'  => 'warehouse')       ,'m.warehouse_id = w.id',array('wh_to' => 'w.name','m.warehouse_id'))
            ->joinLeft(array('go' => 'good')            ,'m.good_id = go.id'        ,array('in_cost' => 'go.price_4','product_name' => 'go.name'))
            ->joinLeft(array('g'  => 'good_color')      ,'m.good_color = g.id'      ,array('product_color' => 'g.name'))
            ->joinLeft(array('gc' => 'good_category')   ,'m.cat_id = gc.id'         ,array('category' => 'gc.name','m.good_id','m.good_color','m.cat_id'))
            ->joinLeft(array('d'  => 'distributor')     ,'m.d_id = d.id'            ,array('wh_from' => 'd.title'))
            ->where('m.isbacks = 1')
            ->where('m.old_data is null')
            ->where('m.canceled <> 1')
            ->where('m.outmysql_time IS NOT NULL')
            ->where("m.outmysql_time <> '' ")
            ->where('m.outmysql_time <> 0')
            ->group(array('go.name','g.name'))
            ->order('m.outmysql_time ASC');

        if ( isset($params['warehouse_id']) && $params['warehouse_id'] ) {
            if (is_array($params['warehouse_id']))
                $select->where("m.warehouse_id IN (".implode(",",$params['warehouse_id']).")",null);
            else
                $select->where("m.warehouse_id = ?",$params['warehouse_id']);
        } 

        $from = explode('/', $params['from']);
        $to = explode('/', $params['to']);
        // Filter Data From - To
        if (isset($params['from']) && $params['from'] && !isset($params['to'])) {
            $select->where( 'm.outmysql_time >= ?', $from[2].'-'.$from[1].'-'.$from[0] . ' 00:00:00' );
        } elseif (isset($params['to']) && $params['to'] && !isset($params['from'])) {
            $select->where( 'm.outmysql_time <= ?', $to[2].'-'.$to[1].'-'.$to[0] . ' 23:59:59' );
        } elseif (isset($params['to']) && $params['to'] && isset($params['from']) && $params['from'] ) {
            $select->where( 'm.outmysql_time >= ?', $from[2].'-'.$from[1].'-'.$from[0] . ' 00:00:00' );
            $select->where( 'm.outmysql_time <= ?', $to[2].'-'.$to[1].'-'.$to[0] . ' 23:59:59' );
        }

        // Filter 
        if ( isset($params['cat_id']) && $params['cat_id'] ) {

            if (is_array($params['cat_id']))
                $select->where( 'm.cat_id IN (?)', $params['cat_id']);
            else
                $select->where( 'm.cat_id = ?', $params['cat_id']);
        }
        if ( isset($params['good_id']) && $params['good_id'] ) {

            if (is_array($params['good_id']))
                $select->where( 'm.good_id IN (?)', $params['good_id']);
            else
                $select->where( 'm.good_id = ?', $params['good_id']);
        }
        if ( isset($params['color_id']) && $params['color_id'] ) {

            if (is_array($params['color_id']))
                $select->where( 'm.good_color IN (?)', $params['color_id']);
            else
                $select->where( 'm.good_color = ?', $params['color_id']);
        }
        // check warehouse type
        if ( isset($params['warehouse_type']) && $params['warehouse_type'] ) {
            $select->where( 'w.id IN ( SELECT id FROM warehouse WHERE warehouse_type IN ('.$params['warehouse_type'].') )', null);
        }
/*
        if ( isset($params['warehouse_id']) && $params['warehouse_id'] ) {

            if (is_array($params['warehouse_id']))
                $select->where( 'm.warehouse_id IN (?)', $params['warehouse_id']);
            else
                $select->where( 'm.warehouse_id = ?', $params['warehouse_id']);
        }
*/
        //echo $select; die;
        $result = $db->fetchAll($select);

        return $result;
    }

    public function fetchCredit_Note($sn)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('cn' => 'credit_note_tran'), array('sales_order' => 'cn.sales_order','cn.creditnote_sn','(cn.use_discount) AS total_discount'));


        if (!$sn)
            return false;

        $select->where('cn.sales_order = ?', $sn);
        //echo $select;die;
        return $db->fetchAll($select);
    }

     function fetchScanOut($page, $limit, &$total, $params){

        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');

        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $select = $db->select();

            $select_fields = array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'), 'p.*','create_at'=>'p.pay_time');

            $select
                ->from(array('p' => $this->_name),
                    $select_fields
                )
                ->group('p.sn');

        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array('d.name', 'd.title', 'd.mst_sn', 'd.unames', 'd.store_code', 'd.district','d.add_tax','d.region'));
       
        $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order'));
        //$select->joinLeft(array('cm' => 'checkmoney'), 'cm.sn=p.sn', array('checkmoney_create_at' => 'cm.create_at'));
        if (!isset($params['warehouse_id'])) {
            $select->where( 'p.warehouse_id = ?', 1);
        }
         if (isset($params['warehouse_id']) and $params['warehouse_id']) {
            if (is_array($params['warehouse_id']) && count($params['warehouse_id']))
                $select->where('p.warehouse_id IN (?)', $params['warehouse_id']);

            elseif (is_numeric($params['warehouse_id']))
                $select->where('p.warehouse_id = ?', intval($params['warehouse_id']));
        }
        if (!isset($params['invoice_time_from']) || !isset($params['invoice_time_from'])){
            
                $select->where('p.invoice_time >= ?', $params['date_now'].' 00:00:00');
                $select->where('p.invoice_time <= ?', $params['date_now'].' 23:59:59');
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
        $select->where('old_data is null',null);
       if($userStorage->warehouse_type !=''){
            $warehouse_type = $userStorage->warehouse_type;
            $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
        }else{
            $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
        }
     // echo  $select;
        $result = $db->fetchAll($select);

        if (isset($params['not_get_total']) and $params['not_get_total'])
            return $result;

        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function StaffScanOut($params){
       
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');

        
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> 'market'),array('user' => 'distinct(p.outmysql_user)'));

        if (!isset($params['warehouse_id'])) {
            $select->where( 'p.warehouse_id = ?', 1);
        }
         if (isset($params['warehouse_id']) and $params['warehouse_id']) {
            if (is_array($params['warehouse_id']) && count($params['warehouse_id']))
                $select->where('p.warehouse_id IN (?)', $params['warehouse_id']);

            elseif (is_numeric($params['warehouse_id']))
                $select->where('p.warehouse_id = ?', intval($params['warehouse_id']));
        }
        if (!isset($params['invoice_time_from']) || !isset($params['invoice_time_from'])){
            
                $select->where('p.invoice_time >= ?', $params['date_now'].' 00:00:00');
                $select->where('p.invoice_time <= ?', $params['date_now'].' 23:59:59');
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
        $select->where("p.outmysql_time is not null");
        $select->where('p.old_data is null',null);
       // if($userStorage->warehouse_type !=''){
       //      $warehouse_type = $userStorage->warehouse_type;
       //      $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
       //  }else{
       //      $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
       //  }
     
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function fetchScanOutProduct($page, $limit, &$total, $params){

        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');

        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $select = $db->select();

            $select_fields = array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'), 'p.*','sum'=>'SUM(p.num)','create_at'=>'p.pay_time');

            $select
                ->from(array('p' => $this->_name),
                    $select_fields
                )
                ->group('p.sn');

        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array('d.name', 'd.title', 'd.mst_sn', 'd.unames', 'd.store_code', 'd.district','d.add_tax','d.region'));
       
        $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order'));
       // $select->joinLeft(array('cm' => 'checkmoney'), 'cm.sn=p.sn', array('checkmoney_create_at' => 'cm.create_at'));
        if (!isset($params['warehouse_id'])) {
            $select->where( 'p.warehouse_id = ?', 1);
        }
         if (isset($params['warehouse_id']) and $params['warehouse_id']) {
            if (is_array($params['warehouse_id']) && count($params['warehouse_id']))
                $select->where('p.warehouse_id IN (?)', $params['warehouse_id']);

            elseif (is_numeric($params['warehouse_id']))
                $select->where('p.warehouse_id = ?', intval($params['warehouse_id']));
        }
        if (!isset($params['invoice_time_from']) || !isset($params['invoice_time_from'])){
            
                $select->where('p.invoice_time >= ?', $params['date_now'].' 00:00:00');
                $select->where('p.invoice_time <= ?', $params['date_now'].' 23:59:59');
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
        $select->where('old_data is null',null);
        $select->where('p.cat_id=11');
       if($userStorage->warehouse_type !=''){
            $warehouse_type = $userStorage->warehouse_type;
            $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
        }else{
            $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
        }
     // echo  $select;
        $result = $db->fetchAll($select);

        if (isset($params['not_get_total']) and $params['not_get_total'])
            return $result;

        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function cancelOrder($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $select = $db->select();
         $select_fields = array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'),'p.sn','sn_ref','sum'=>'SUM(p.num)','SUM(p.total) AS total_price','p.add_time','p.canceled_remark','p.canceled','p.date_canceled','p.cancel_delivery_date','p.cancel_delivery_status');

            $select
                ->from(array('p' => $this->_name),
                    $select_fields
                )
                ->group('p.sn')

         ->joinLeft(array('w'  => 'warehouse')       ,'p.warehouse_id = w.id',array('w.name','w.id'))
         ->joinLeft(array('s1'  => 'staff')       ,'p.canceled_by = s1.id',array('s1.firstname AS name_cancel'))
         ->joinLeft(array('s2'  => 'staff')       ,'p.cancel_delivery_by = s2.id',array('s2.firstname AS name_cancel_kerry'))
         ->joinLeft(array('d'  => 'distributor')       ,'p.d_id = d.id',array('d.title','d.id'));

          $select->where('p.canceled = ?', 1);
          $select->where('p.canceled_by != ?', '');

        if(isset($params['sn'])                     and         $params['sn'])
            {
               $select->where('p.sn_ref = ?', $params['sn']); 
            }

        if(isset($params['good_id'])                and        $params['good_id']) 
            {
               $select->where('p.good_id = ?', $params['good_id']); 
            }

        if(isset($params['good_color'])             and        $params['good_color']) 
            {
               $select->where('p.good_color = ?', $params['good_color']); 
            }

        if(isset($params['cat_id'])                 and        $params['cat_id'])    
            {
               $select->where('p.cat_id = ?', $params['cat_id']); 
            }

        if(isset($params['cancel_delivery_form'])   and        $params['cancel_delivery_form'])
            {
                list( $day, $month, $year ) = explode('/', $params['cancel_delivery_form']);
                if (isset($day) and isset($month) and isset($year) )
                $select->where('p.cancel_delivery_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
            }

        if(isset($params['cancel_delivery_to'])     and        $params['cancel_delivery_to'])
            {
                list( $day, $month, $year ) = explode('/', $params['cancel_delivery_to']);
                if (isset($day) and isset($month) and isset($year) )
                $select->where('p.cancel_delivery_date <= ?', $year.'-'.$month.'-'.$day.'  23:59:59');
            }

        if(isset($params['cancel_at_form'])         and        $params['cancel_at_form'])
            {
                list( $day, $month, $year ) = explode('/', $params['cancel_at_form']);
                if (isset($day) and isset($month) and isset($year) )
                $select->where('p.date_canceled >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
            }

        if(isset ($params['cancel_at_to'])           and        $params['cancel_at_to'])    
            {
                list( $day, $month, $year ) = explode('/', $params['cancel_at_to']);
                if (isset($day) and isset($month) and isset($year) )
                $select->where('p.date_canceled <= ?', $year.'-'.$month.'-'.$day.'  23:59:59');
            }


        if(($params['cancel']))
        {
            if ($params['cancel']==-1)
                $select->where('p.cancel_delivery_status = ? OR p.cancel_delivery_status IS NULL', 0);
            elseif ($params['cancel'])
                $select->where('p.cancel_delivery_status = ?', $params['cancel']);
        }
        // echo $select;die;
        if(isset($params['d_id'])                   and        $params['d_id'])
            {
               $select->where('p.d_id = ?', $params['d_id']); 
            }


        if (isset($params['sort']) and $params['sort']) {

            $collate = ' ';
            $desc = ($params['desc'] == 1) ? 'DESC' : 'ASC';

            $order_str .= $params['sort'] . $collate . $desc;
            // echo $order_str;die;
            $select->order($order_str);
        }
        else{
             $select->order('p.date_canceled DESC');
        }


        $select->limitPage($page, $limit);



        $result = $db->fetchAll($select);
         $total = $db->fetchOne("select FOUND_ROWS()");
         // $total = $db->fetchOne($select);
        // echo "<pre>";
        // print_r($result);die;
        return $result;
    }
     //pond
    public function checkQuotaOppo($params)
    {
        $db = Zend_Registry::get('db');
        $num = $params['num'];

        $toDay = date('Y-m-d');
        $select_q = $db->select()
            ->from(array('d' => 'quota_oppo_by_distributor'), array('d.quantity'));
       $select_q->where('d.warehouse = ?', $params['warehouse']);
       $select_q->where('d.d_id = ?', $params['distributor_id']);
       $select_q->where('d.cat_id = ?', $params['cat_id']);
       $select_q->where('d.good_id = ?', $params['good_id']);
       $select_q->where('d.good_color = ?', $params['good_color']);
       $select_q->where('d.status = 1');
       $select_q->where('d.del is null');
       $select_q->where('date_format(d.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
       $quota_by_distributor = $db->fetchRow($select_q);
       
      
    if (isset($quota_by_distributor) and $quota_by_distributor) {
            $data = $quota_by_distributor['quantity'];
            $select_m = $db->select()
                ->from(array('lq' => 'log_quota_tran_distributor'), array('sum(lq.num)'));
            $select_m->where('lq.warehouse_id = ?', $params['warehouse']);
            $select_m->where('lq.d_id = ?', $params['distributor_id']);
            $select_m->where('lq.cat_id = ?', $params['cat_id']);
            $select_m->where('lq.good_id = ?', $params['good_id']);
            $select_m->where('lq.good_color = ?', $params['good_color']);
            $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
            $isSum = $db->fetchOne($select_m);
            


        if ($params['sales_sn']) {
            $select_k = $db->select()
                ->from(array('m' => 'market'), array('m.num'));
            $select_k->where('m.sn = ?', $params['sales_sn']);
            $select_k->where('m.d_id = ?', $params['distributor_id']);
            $select_k->where('m.cat_id = ?', $params['cat_id']);
            $select_k->where('m.good_id = ?', $params['good_id']);
            $select_k->where('m.good_color = ?', $params['good_color']);

            $market = $db->fetchOne($select_k);
               if($market > $isSum){ 
               $isSum = $market - $isSum;
               }else{
                $isSum = $isSum - $market;
               }
           
        }
       
            if ($isSum) {
               
            if ($num+$isSum > $data) {
                $q='1';
            }else{
                $q='0';
            }
            }else{
                if ($data) {
                       if ($num > $data) {$q = '1';}else{$q = '0';}
                }else{
                    $q = '0';
                }
                
            }

       }else{   
        $select_a = $db->select()
            ->from(array('d' => 'distributor'), array('d.rank','d.quota_channel'));

       $select_a->joinLeft(array('r' => HR_DB.'.regional_market'),'r.id=d.region',array());
       $select_a->joinLeft(array('a' => HR_DB.'.area'),'r.area_id=a.id',array('area_id'=>'a.id'));
       $select_a->where('d.id = ?', $params['distributor_id']);
       $distributor = $db->fetchRow($select_a);

        if ($distributor['quota_channel']) {
           if ($distributor['quota_channel'] == 1) {
              $rank = 1;
             
           }else if ($distributor['quota_channel'] == 10) {
              $rank = 10;
           }
       }else{
            if (in_array($distributor['rank'], array(7,8))) {
               $rank = 7; 
            }else if (in_array($distributor['rank'], array(1,2,5,6))) {
              $rank = 1;
            }else if ($distributor['rank'] == 10) {
              $rank = 10;
            }
       }

        $select_w = $db->select()
            ->from(array('o' => 'quota_oppo'), array('o.id'));
        $select_w->where('o.dis_type = ?', $rank);
        $select_w->where('o.good_id = ?', $params['good_id']);
        $select_w->where('o.good_color = ?', $params['good_color']);
        $select_w->where('o.quota_date = ?',$toDay);
        $select_w->where('o.status = ?',1);
        
        $quota_oppo = $db->fetchAll($select_w);
        
       if (isset($quota_oppo) and $quota_oppo) {
        
        
       
       $dis_type = '';
       if ($distributor['quota_channel']) {
           if ($distributor['quota_channel'] == 1) {
              $action_quota = "org";
              $dis_type     = "have";
           }else if ($distributor['quota_channel'] == 10) {
              $action_quota = "brandshop";
              $dis_type     = "have";
           }
       }else{
            if (in_array($distributor['rank'], array(7,8))) {
               $action_quota = "dealer"; 
            }else if (in_array($distributor['rank'], array(1,2,5,6))) {
              $action_quota = "org";
            }else if ($distributor['rank'] == 10) {
              $action_quota = "brandshop";
            }
       }

        if ($action_quota == "dealer") { //dealer ,hub
            
            $select_q = $db->select()
                ->from(array('q' => 'quota_oppo'), array('q.quantity'));
            $select_q->where('q.area_id = ?', $distributor['area_id']);
            $select_q->where('q.status = ?',1);
            $select_q->where('q.dis_type = ?', 7);
            $select_q->where('q.good_id = ?', $params['good_id']);
            $select_q->where('q.good_color = ?', $params['good_color']);

            $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
            $select_q->where('q.status = ?', 1);
            $data = $db->fetchOne($select_q);

            if ($data) {
                $select_m = $db->select()
                    ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
                $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
                $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
                $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
                $select_m->where('a.id = ?', $distributor['area_id'] );
                $select_m->where('lq.good_id = ?', $params['good_id']);
                $select_m->where('lq.good_color = ?', $params['good_color']);
                 $select_m->where('(d.quota_channel = ?', 10 );
                $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));

            // echo $select_m;
                $isSum = $db->fetchOne($select_m);

            }else{

                unset($data);
                $select_qt = $db->select()
                ->from(array('q' => 'quota_oppo'), array('q.quantity'));
                $select_qt->where('q.channel = ?', 99);
                $select_qt->where('q.dis_type = ?', 7);
                $select_qt->where('q.status = ?',1);
                $select_qt->where('q.good_id = ?', $params['good_id']);
                $select_qt->where('q.good_color = ?', $params['good_color']);
                $select_qt->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
                $select_qt->where('q.status = ?', 1);
                $data = $db->fetchOne($select_qt);

                $select_q = $db->select()
                ->from(array('q' => 'quota_oppo'), array('q.area_id'));
                $select_q->where('q.dis_type = ?', 7);
                $select_q->where('q.status = ?',1);
                $select_q->where('q.quantity = ?', 0);
                $select_q->where('q.good_id = ?', $params['good_id']);
                $select_q->where('q.good_color = ?', $params['good_color']);
                $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
                $select_q->where('q.status = ?', 1);
                $inArea = $db->fetchAll($select_q);
                if ($inArea) {
                    $select_m = $db->select()
                    ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
                    $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
                    $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
                    $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
                    $select_m->where('a.id in (?)', $inArea );
                    $select_m->where('lq.good_id = ?', $params['good_id']);
                     $select_m->where('(d.quota_channel = ?', 10 );
                    $select_m->where('lq.good_color = ?', $params['good_color']);
                    $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
                    $isSum = $db->fetchOne($select_m);
                }
                
            }
       
         

        }
        if ($action_quota == "org") { //ORG

           $select_c = $db->select()
                ->from(array('d' => 'distributor'), array('d.quota_channel'));
            $select_c->where('d.id = ?', $params['distributor_id']);
            $channel = $db->fetchOne($select_c); 
             
            $select_q = $db->select()
                    ->from(array('q' => 'quota_oppo'), array('q.quantity'));
              
                $select_q->where('q.dis_type = ?',1 );
                $select_q->where('q.good_id = ?', $params['good_id']);
                $select_q->where('q.good_color = ?', $params['good_color']);
                $select_q->where('q.status = ?', 1);
                $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
               
                $data = $db->fetchOne($select_q);

                $select_m = $db->select()
                    ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
                $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
                $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
                $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
                $select_m->where('(d.quota_channel = ?', 1 );
                $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
               
                $select_m->where('lq.good_id = ?', $params['good_id']);
                $select_m->where('lq.good_color = ?', $params['good_color']);
                $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
               
                $isSum = $db->fetchOne($select_m);
               
        }
        
        if ($action_quota == "brandshop") { //Brand Shop
           $select_c = $db->select()
                ->from(array('d' => 'distributor'), array('d.quota_channel'));
            // $select_c->where('q.area_id = ?', $area);
            $select_c->where('d.id = ?', $params['distributor_id']);
            $channel = $db->fetchOne($select_c); 
            
           
         $select_q = $db->select()
                    ->from(array('q' => 'quota_oppo'), array('q.quantity'));
              
                $select_q->where('q.dis_type = ?',10 );
                $select_q->where('q.good_id = ?', $params['good_id']);
                $select_q->where('q.good_color = ?', $params['good_color']);
                $select_q->where('q.status = ?', 1);
                $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
               
                $data = $db->fetchOne($select_q);

                
                $select_m = $db->select()
                    ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
                $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
                $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
                $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
                
               
                $select_m->where('lq.good_id = ?', $params['good_id']);
                $select_m->where('lq.good_color = ?', $params['good_color']);
                $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
                $select_m->where('(d.quota_channel = ?', 10 );
                $select_m->Orwhere('d.rank in (?))', array(10) );
                $isSum = $db->fetchOne($select_m);

        } 
        
        if ($isSum) {
            if ($num+$isSum > $data) {
                $q='1';
            }else{
                $q='0';
            }
            }else{
                if ($data) {
                       if ($num > $data) {$q = '1';}else{$q = '0';}
                }else{
                    $q = '0';
                }
                
            }
    }else{
        $q = '0';
    }    
    }    
        return $q;
          
}       

    public function duePaymentOrder($page, $limit, &$total, $params)
    {
        $db = Zend_Registry::get('db');

        if (isset($params['due_from']) and $params['due_from']) {
            list($day, $month, $year) = explode('/', $params['due_from']);

            if (isset($day) and isset($month) and isset($year)) {
                $due_from = $year.'-'.$month.'-'.$day;
            }
        }
        if (isset($params['due_to']) and $params['due_to']) {
            list($day, $month, $year) = explode('/', $params['due_to']);

            if (isset($day) and isset($month) and isset($year)) {
                $due_to = $year.'-'.$month.'-'.$day;
            }
        }

    if (isset($params['d_id']) and $params['d_id']) {
        $d_id = '('.$params['d_id'].')';
    }else{
        $d_id = '(3025,11293,10016,30344,3169)';
    }
    
    $select = "SELECT m.sn,m.sn_ref,d.title,round(sum(m.total)-if(m.total_spc_discount,m.total_spc_discount*1.07,0),2) as total ,m.finance_confirm_date ,d.credit_type,c.credit 
        ,(SELECT DATE_ADD(DATE_FORMAT(m.finance_confirm_date,'%Y-%m-%d'), INTERVAL + (c.credit+2) DAY))AS pay_date
        FROM market m 
        JOIN distributor d ON m.d_id = d.id
        JOIN credit_type c ON c.id = d.credit_type
        WHERE
        m.d_id IN ".$d_id."

        GROUP BY m.sn

        HAVING pay_date >= '".$due_from."' AND pay_date <= '".$due_to."'
            ORDER BY pay_date ";

        // if ($limit) {
        //     $select .= "LIMIT ".$page.",".$limit   ; 
        //     }    
           
           
       
        //HAVING pay_date >= '2016-10-07' AND pay_date <= '2016-10-07'";
        //$params['pay_date'];
        // echo $select;  
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;

    }

    public function SelectDistributor($value='')
    {
        $db = Zend_Registry::get('db');
        $select_c = $db->select()
                ->from(array('d' => 'distributor'), array('d.id','d.title'));
            $select_c->where('d.id in (?)', array(3025,11293,10016,30344,3169));
             $data = $db->fetchAll($select_c);
            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['title'];
                }
            }
             return $result;
    }

}
