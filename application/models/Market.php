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

    public function getInvoice($params){
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> 'market'),array('p.sn','p.sn_ref','p.invoice_number',"concat(p.invoice_number,'/',p.sn_ref) as invoice_number_show"))
        ->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array())
        ->joinLeft(array('dg' => 'distributor_group'), 'dg.group_id = d.group_id', array());
        $select->where('p.invoice_number IS NOT NULL', null);
        $select->where('p.isbacks !=1', null);
        $select->where('p.canceled !=1', null);
        $select->group(new Zend_Db_Expr('p.sn'));
        $select->order('p.invoice_number asc');

        $print_type = '';
        if (isset($params['print_type']) and $params['print_type']){
            $print_type = $params['print_type'];
        }

        switch ($print_type) {
            case '1':

                if (isset($params['list_invoice']) and $params['list_invoice'])
                    $select->where('p.invoice_number in (?)', $params['list_invoice']);
                
                break;
            case '2':
                
                if (isset($params['start_invoice']) and $params['start_invoice'])
                    $select->where('p.invoice_number >= ?', $params['start_invoice']);

                if (isset($params['end_invoice']) and $params['end_invoice'])
                    $select->where('p.invoice_number <= ?', $params['end_invoice']);

                if (isset($params['group_id']) and $params['group_id'])
                    $select->where('dg.group_type_id = ?', $params['group_id']);

                if (isset($params['start_date']) and $params['start_date'])
                    $select->where('p.invoice_time >= ?', $params['start_date']);

                if (isset($params['end_date']) and $params['end_date'])
                    $select->where('p.invoice_time <= ?', $params['end_date'] . ' 23:59:59');

                break;
            
            default:
                return [];
                break;
        }

        // echo $select;die;
        return $db->fetchAll($select);
    }

    public function checkInvoiceNotAllowPrintA4($list_invoice){
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> 'market'),array('p.sn','p.sn_ref','p.invoice_number',"concat(p.invoice_number,'/',p.sn_ref) as invoice_number_show"))
        ->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array())
        ->joinLeft(array('dg' => 'distributor_group'), 'dg.group_id = d.group_id', array());
        $select->where('p.invoice_number in (?)', $list_invoice);
        
        //check print type A4 only (Brand Shop By Dealer (11),Dealer and Hub (1),KR-Dealer (2),Staff (6),Brand Shop by KR Dealer (13))
        $select->where('dg.group_id not in (?)', ['11','1','2','6','13']);

        $select->where('p.isbacks !=1', null);
        $select->where('p.canceled !=1', null);
        $select->group(new Zend_Db_Expr('p.sn'));
        $select->order('p.invoice_number asc');

        // echo $select;die;
        return $db->fetchAll($select);
    }

    //Tanong
    public function fetchInvoice($params)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> 'market'),array(new Zend_Db_Expr('CASE  WHEN p.price_clas = 11 and p.cat_id=11 THEN gs.good_code WHEN p.d_id = 34119 
    THEN gs.good_code ELSE g.name END as product_code'),new Zend_Db_Expr('CASE WHEN p.price_clas = 11 AND p.cat_id = 11 THEN gs.good_name WHEN p.d_id = 34119 THEN gs.good_name ELSE g.desc END AS `good_name`'),'p.id','SUM(p.num) AS total_qty','SUM(p.total) AS total_price','p.invoice_number','p.type','p.print_picking_list','p.service','p.*'))
        ->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array('d.name','d.title','d.mst_sn','d.unames','d.store_code','d.district'))
        ->joinLeft(array('g' => 'good'), 'g.id=p.good_id', array('g.*'))
        ->joinLeft(array('gs' => 'good_name_sp'), 'gs.good_id = p.good_id and gs.good_color_id = p.good_color and  gs.good_type = p.type AND p.d_id = gs.d_id', array('gs.*'));

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
        //print_r($params);//die;
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');

        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $select = $db->select();

        $QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();
        $getwarehouseByuserID = $QWarehouseGroupUser->currentWarehouseGroupUserList($userStorage->id);

        $whrehouse_name = array();
        foreach ($getwarehouseByuserID as $item){
            $whrehouse_name[$item['warehouse_id']] =  $item['warehouse_id'];
        }

        if(isset($params['order_list']) and $params['order_list']){

            if (My_Staff_Group::inGroup($userStorage->group_id, array(SALES_VIENTIAN,SALES_DEALER))) {
                $select->where('p.salesman IN (?)',$userStorage->id);
            }
            if(My_Staff_Group::inGroup($userStorage->group_id, array(RD_DEALER))){
                $QStaff = new Application_Model_Staff();
                $where = $QStaff->getAdapter()->quoteInto('id =?',$userStorage->id);
                $rd_area = $QStaff->fetchRow($where);


                $get_sales = $QStaff->getSalesByRdArea($rd_area['area_id']);
                $sales_ID = array();
                foreach ($get_sales as $item){
                    $sales_ID[$item['id']] =  $item['id'];
                }


                $select->where('p.salesman IN (?)',array($sales_ID,$userStorage->id));
            }
        }
        if($whrehouse_name){
            $select->where("p.warehouse_id IN (".implode(",",$whrehouse_name).")", null);
        }
        // End
        
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

        $sub_select = $db->select()
        ->from(array('p' => 'market'),array('total_sellout' => 'SUM(p.num)'))
        ->joinLeft(array('d' => 'distributor'),'d.id = p.d_id',array())
        ->joinleft(array('rm' => HR_DB.'.regional_market'),'rm.id = d.region',array())
        ->where('p.salesman IS NOT NULL');

        $whrehouse_name = array();
        foreach ($getwarehouseByuserID as $item){
            $whrehouse_name[$item['warehouse_id']] =  $item['warehouse_id'];
        }

        if(isset($params['order_list']) and $params['order_list']){

            if (My_Staff_Group::inGroup($userStorage->group_id, array(SALES_VIENTIAN,SALES_DEALER))) {
                $sub_select->where('p.salesman IN (?)',$userStorage->id);
            }
             if(My_Staff_Group::inGroup($userStorage->group_id, array(RD_DEALER))){
            $QStaff = new Application_Model_Staff();
            $where = $QStaff->getAdapter()->quoteInto('id =?',$userStorage->id);
            $rd_area = $QStaff->fetchRow($where);


            $get_sales = $QStaff->getSalesByRdArea($rd_area['area_id']);
            $sales_ID = array();
            foreach ($get_sales as $item){
                $sales_ID[$item['id']] =  $item['id'];
            }

                $sub_select->where('p.salesman IN (?)',array($sales_ID,$userStorage->id));
        }
        }
        if($whrehouse_name){
            $sub_select->where("p.warehouse_id IN (".implode(",",$whrehouse_name).")", null);
        }



            

            if (isset($params['group_sn']) and $params['group_sn']){
            //Tanong Edit total_price - discount by credit note  20160313
            $select_fields = array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'),'p.payment_no','user_id', 'sn_ref' => 'sn_ref',  'total_qty' => 'SUM(p.num)', 'total_price_amount_invat' => 'SUM( ROUND( ( (p.total/p.num) ),2) * p.num )', 'total_price_amount' => 'SUM( ROUND( ( (p.total/p.num)/1 ),2) * p.num )','delivery_fee'
                , 'total_price' => 'SUM( ROUND( ( (p.total/p.num)/1 ),2) * p.num ) - IFNULL((CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END),0) + IFNULL(p.delivery_fee,0) AS total_price'
                , 'total_creditnote' => 'CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END AS total_creditnote'
                ,'invoice_number', 'type', 'print_picking_list', 'service','confirm_so','sale_off_percent','p.sales_confirm_date','shipping_address','customer_name' 
                ,'(CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END) AS total_discount','(CASE p.use_dp WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM deposit_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END) AS total_deposit','p.canceled_by','total_spc_discount','p.bs_campaign');


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
                
                if( isset($params['newsalespayment']) && $params['newsalespayment'] != '' ){
                    $select->from(array('p' => $this->_name),$select_fields);
                }else{
                    $select->from(array('p' => $this->_name),$select_fields)
                    ->group('p.sn');
                }

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

        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array('d.name', 'd.title','d.rank', 'd.mst_sn','d.finance_code', 'd.unames', 'd.store_code','p.salesman','p.payment_type','p.confirm_cash','p.confirm_cash_by', 'd.district','d.add_tax','d.region','p.confirm_access_status','p.confirm_access_remark','p.confirm_access_by','p.order_accessories','d.finance_group','d.quota_channel','d.del','p.store_id','d.distributor_code'));

        $select->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

        $select->joinLeft(array('lsa' => 'log_swap_acc'),'lsa.sn=p.sn',array('lsa.old_cat_id','lsa.old_good_id','lsa.old_good_color_id','old_num','lsa.new_cat_id','lsa.new_good_id','lsa.new_good_color_id','new_num'));
        $select->joinleft(array('re' => HR_DB.'.regional_market'),'re.id = d.region',array());
        $select->joinLeft(array('are' => HR_DB.'.area'),'are.id = re.area_id',array('areaname' => 'are.name'));
        $select->joinLeft(array('go' => 'good'),'go.id = p.good_id',array());
        $select->joinLeft(array('br' => 'brand'),'br.id = go.brand_id',array('brand_name' => 'br.name','brand_id' => 'br.id'));

        $select->joinLeft(array('sto' => HR_DB.'.store'),'sto.id = p.store_id',array('store_id' => 'sto.id','store_name' => 'sto.name','store_code' => 'sto.store_code','store_area'=> 'sto.area_id','store_province' => 'sto.province_id'));

        if (isset($params['have_quota']) && $params['have_quota']) {
            $select->joinLeft(array('lqt' => 'log_quota_tran'), 'lqt.market_id=p.id', array('quota_id' => 'lqt.id'));
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
	
	   if (isset($params['sale_off_percent']) and $params['sale_off_percent'] == "1")
            $select->where('p.sale_off_percent !=0');

        if (isset($params['type']) and $params['type'])
            $select->where('p.type = ?', $params['type']);

        if (isset($params['brand_id']) and $params['brand_id'])
            $select->where('br.id =?',$params['brand_id']);

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



        //Search by warehouse
        if(isset($params['warehouse']) and $params['warehouse'])
            $select->where('p.warehouse_id IN (?)', $params['warehouse']);

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

         if(isset($params['payment_type']) && $params['payment_type'])
            if($params['payment_type'] =='Not' ){
                $select->where('payment_type IS NULL',null);
            }
            else if($params['payment_type'] == 'CR'){
                $select->where('payment_type =?',CR);
            }else{
                $select->where('payment_type =?',CA);
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

        if (isset($params['group_id']) && $params['group_id'])
            $select->where('d.group_id = ?', $params['group_id']);

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
            $select->where('p.pay_time is not null',1);
            $select->where('p.pay_time <> \'\'');
            $select->where('p.pay_time <> 0');
        }

        if (isset($params['no_payment']) and $params['no_payment'])
            $select->where('p.pay_time IS NULL OR p.pay_time = \'\' OR p.pay_time = 0');

        if (isset($params['outmysql_time']) and $params['outmysql_time']){
            $select->where('p.outmysql_time is not null',1);
            $select->where('p.outmysql_time <> \'\'');
            $select->where('p.outmysql_time <> 0');
        }

        if (isset($params['no_outmysql_time']) and $params['no_outmysql_time'])
            $select->where('p.outmysql_time IS NULL OR p.outmysql_time = \'\' OR p.outmysql_time = 0');

        if (isset($params['no_shipping']) and $params['no_shipping'])
            $select->where('p.shipping_yes_time IS NULL OR p.shipping_yes_time = \'\' OR p.shipping_yes_time = 0');

        //finance
        // if (isset($params['finance']) and $params['finance']) {
        //     $select->where('p.pay_time IS NULL OR p.pay_time = \'\' OR p.pay_time = 0 OR p.shipping_yes_time IS NULL OR p.shipping_yes_time = \'\' OR p.shipping_yes_time = 0');
        //     $select->where('p.isbacks = ?', 0);
        //    // $select->where('p.warehouse_id <> 90', null);
        // }

        //edit confirm payment
        //finance
        if (isset($params['finance']) and $params['finance']) {
            $select->where('p.pay_time IS NOT NULL AND invoice_number IS NOT NULL AND confirm_cash IS NULL');
            $select->where('p.payment_type = ?',CR);
           //  $select->where('p.pay_time IS NULL OR p.pay_time = \'\' OR p.pay_time = 0 OR p.shipping_yes_time IS NULL OR p.shipping_yes_time = \'\' OR p.shipping_yes_time = 0');
           //  $select->where('p.isbacks = ?', 0);
           // // $select->where('p.warehouse_id <> 90', null);
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
              // $select->where('p.sales_confirm_date IS NOT NULL AND p.sales_confirm_id is not null',1);
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
              $select->where('p.invoice_time IS NULL OR p.print_time IS NULL OR p.invoice_number is null',1);
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
            $select->where('p.print_time is not null',1);
            $select->where('p.invoice_time is not null',1);
            $select->where('p.invoice_number is not null',1);
        }

        if ((isset($params['product_out']) and $params['product_out']) || (isset($params['warehouse_out']) and $params['warehouse_out'])) {
            // -------------------------
            if (isset($params['created_at_from']) and $params['created_at_from']){
                list( $day, $month, $year ) = explode('/', $params['created_at_from']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.add_time >= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            }

            if (isset($params['created_at_to']) and $params['created_at_to']){
                list( $day, $month, $year ) = explode('/', $params['created_at_to']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.add_time <= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            }


            // -------------------------
            if (isset($params['invoice_time_from']) and $params['invoice_time_from']){
                list( $day, $month, $year ) = explode('/', $params['invoice_time_from']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.invoice_time >= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            }

            if (isset($params['invoice_time_to']) and $params['invoice_time_to']){
                list( $day, $month, $year ) = explode('/', $params['invoice_time_to']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.invoice_time <= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            }

            // -------------------------
            if (isset($params['finance_confirm_time_from']) and $params['finance_confirm_time_from']){
                list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_from']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.pay_time >= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            }

            if (isset($params['finance_confirm_time_to']) and $params['finance_confirm_time_to']){
                list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_to']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.pay_time <= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            }

            // -------------------------
            if (isset($params['out_time_from']) and $params['out_time_from']){
                list( $day, $month, $year ) = explode('/', $params['out_time_from']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.outmysql_time >= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            }

            if (isset($params['out_time_to']) and $params['out_time_to']){
                list( $day, $month, $year ) = explode('/', $params['out_time_to']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.outmysql_time <= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            }


        }else{
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


    // Add Receive date 
            
            if (isset($params['receive_at_from']) and $params['receive_at_from']){
                list( $day, $month, $year ) = explode('/', $params['receive_at_from']);

                if (isset($day) and isset($month) and isset($year) )
                    $select->where('p.outmysql_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
            }

            if (isset($params['receive_at_to']) and $params['receive_at_to']){
                list( $day, $month, $year ) = explode('/', $params['receive_at_to']);

                if (isset($day) and isset($month) and isset($year) )
                    $select->where('p.outmysql_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
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
        }
        
        
        if(isset($params['cancel']) AND $params['cancel']){
            $select->where('p.canceled = ?',$params['cancel']);
        }

        if(isset($params['po_id']) AND $params['po_id']){
            $select->where('p.po_id = ?',$params['po_id']);
        }

        if( isset($params['finance_group']) && $params['finance_group'] != '' ){
            $select->where('d.finance_group = ?',$params['finance_group']);
        }

        if( isset($params['sn_munti']) && $params['sn_munti'] != '' ){
            $arraySN = $params['sn_munti'];
            if(count($arraySN) == 1){
                $select->where('p.sn_ref = ?',$params['sn_munti']);
            }else{
                $select->where('p.sn_ref IN (?)',$params['sn_munti']);
            }
        }

        if( isset($params['in_munti']) && $params['in_munti'] != '' ){
            $arraySN = $params['in_munti'];
            if(count($arraySN) == 1){
                $select->where('p.invoice_number = ?',$params['in_munti']);
            }else{
                $select->where('p.invoice_number IN (?)',$params['in_munti']);
            }
        }

        if( isset($params['payment_no']) && $params['payment_no'] != '' ){
            $select->where('p.payment_no = ?',$params['payment_no']);
        }

        if( isset($params['order_no_payment']) && $params['order_no_payment'] != '' ){
            $select->where('p.invoice_number is not null',1);
            $select->where('p.pay_time is null',1);
        }

        if( isset($params['near_far']) && $params['near_far'] != '' ){

            $select->joinLeft(array('sa' => 'shipping_address'), 'sa.id=p.shipping_address', array('sa.province_id'));

            $dateTimeNow = date("Y-m-d H:i:s");

            if(in_array(date('D', strtotime($dateTimeNow)), ['Sun','Sat'])){

                switch ($params['near_far']) {
                    case '1':
                        //ใกล้ (กลาง,ตะวันออก)

                        // กลาง
                        // กรุงเทพมหานคร 1
                        // กำแพงเพชร 49
                        // ชัยนาท 9
                        // นครนายก 17
                        // นครปฐม 58
                        // นครสวรรค์ 47
                        // นนทบุรี 3
                        // ปทุมธานี 4
                        // พระนครศรีอยุธยา 5
                        // พิจิตร 53
                        // พิษณุโลก 52
                        // เพชรบูรณ์ 54
                        // สมุทรปราการ 2
                        // สมุทรสงคราม 60
                        // สมุทรสาคร 59
                        // สุโขทัย 51
                        // สุพรรณบุรี 57
                        // สระบุรี 10
                        // อุทัยธานี 48
      
                        // ตะวันออก
                        // จันทบุรี 13
                        // ฉะเชิงเทรา 15
                        // ชลบุรี 11
                        // ตราด 14
                        // ปราจีนบุรี 16
                        // ระยอง 12
                        // สระแก้ว 18
      
                        // ตะวันตก
                        // กาญจนบุรี 56
                        // ประจวบคีรีขันธ์ 62
                        // เพชรบุรี 61
                        // ราชบุรี 55

                        $array_near_far = array('1','49','9','17','58','47','3','4','5','53','52','54','2','60','59','51','57','10','48','13','15','11','14','16','12','18','56','62','61','55');
                        $select->where('sa.province_id in (?)',$array_near_far);
                        break;
                    case '2':
                        //ไกล (อีสาน,เหนือ,ใต้)

                        // กลาง
                        // ลพบุรี 7
                        // สิงห์บุรี 8
                        // อ่างทอง 6

                        // ตะวันตก
                        // ตาก 50

                        // เหนื่อ
                        // เชียงราย 45
                        // เชียงใหม่ 38
                        // น่าน 43
                        // พะเยา 44
                        // แพร่ 42
                        // แม่ฮ่องสอน 46
                        // ลำปาง 40
                        // ลำพูน 39
                        // อุตรดิตถ์ 41

                        // อีสาน
                        // กาฬสินธุ์ 34
                        // ขอนแก่น 28
                        // ชัยภูมิ 25
                        // นครพนม 36
                        // นครราชสีมา 19
                        // บึงกาฬ 77
                        // บุรีรัมย์ 20
                        // มหาสารคาม 32
                        // มุกดาหาร 37
                        // ยโสธร 24
                        // ร้อยเอ็ด 33
                        // เลย 30
                        // สกลนคร 35
                        // สุรินทร์ 21
                        // ศรีสะเกษ 22
                        // หนองคาย 31
                        // หนองบัวลำภู 27
                        // อุดรธานี 29
                        // อุบลราชธานี 23
                        // อำนาจเจริญ 26

                        $array_near_far = array('7','8','6','50','45','38','43','44','42','46','40','39','41','34','28','25','36','19','77','20','32','37','24','33','30','35','21','22','31','27','29','23','26');
                        $select->where('sa.province_id in (?)',$array_near_far);
                        break;
                    case '3':
                        // ใต้
                        // กระบี่ 64
                        // ชุมพร 69
                        // ตรัง 72
                        // นครศรีธรรมราช 63
                        // นราธิวาส 76
                        // ปัตตานี 74
                        // พังงา 65
                        // พัทลุง 73
                        // ภูเก็ต 66
                        // ระนอง 68
                        // สตูล 71
                        // สงขลา 70
                        // สุราษฎร์ธานี 67
                        // ยะลา 75

                        $array_near_far = array('64','69','72','63','76','74','65','73','66','68','71','70','67','75');
                        $select->where('sa.province_id in (?)',$array_near_far);
                        break;
                }

            }else{

                switch ($params['near_far']) {
                    case '1':
                        //ใกล้ (กลาง,ตะวันออก)

                        // กลาง
                        // กรุงเทพมหานคร 1
                        // นครนายก 17
                        // นครปฐม 58
                        // นนทบุรี 3
                        // ปทุมธานี 4
                        // พระนครศรีอยุธยา 5
                        // ลพบุรี 7
                        // สมุทรปราการ 2
                        // สมุทรสงคราม 60
                        // สมุทรสาคร 59
                        // สิงห์บุรี 8
                        // สุพรรณบุรี 57
                        // สระบุรี 10
                        // อ่างทอง 6
      
                        // ตะวันออก
                        // จันทบุรี 13
                        // ฉะเชิงเทรา 15
                        // ชลบุรี 11
                        // ตราด 14
                        // ปราจีนบุรี 16
                        // ระยอง 12
                        // สระแก้ว 18
      
                        // ตะวันตก
                        // กาญจนบุรี 56
                        // เพชรบุรี 61
                        // ราชบุรี 55

                        $array_near_far = array('1','17','58','3','4','5','7','2','60','59','8','57','10','6','13','15','11','14','16','12','18','56','61','55');
                        $select->where('sa.province_id in (?)',$array_near_far);
                        break;
                    case '2':
                        //ไกล (กลาง,ตะวันตก,อีสาน,เหนือ,ใต้)

                        // กลาง
                        // กำแพงเพชร 49
                        // ชัยนาท 9
                        // นครสวรรค์ 47
                        // พิจิตร 53
                        // พิษณุโลก 52
                        // เพชรบูรณ์ 54
                        // สุโขทัย 51
                        // อุทัยธานี 48

                        // ตะวันตก
                        // ตาก 50
                        // ประจวบคีรีขันธ์ 62

                        // เหนื่อ
                        // เชียงราย 45
                        // เชียงใหม่ 38
                        // น่าน 43
                        // พะเยา 44
                        // แพร่ 42
                        // แม่ฮ่องสอน 46
                        // ลำปาง 40
                        // ลำพูน 39
                        // อุตรดิตถ์ 41

                        // อีสาน
                        // กาฬสินธุ์ 34
                        // ขอนแก่น 28
                        // ชัยภูมิ 25
                        // นครพนม 36
                        // นครราชสีมา 19
                        // บึงกาฬ 77
                        // บุรีรัมย์ 20
                        // มหาสารคาม 32
                        // มุกดาหาร 37
                        // ยโสธร 24
                        // ร้อยเอ็ด 33
                        // เลย 30
                        // สกลนคร 35
                        // สุรินทร์ 21
                        // ศรีสะเกษ 22
                        // หนองคาย 31
                        // หนองบัวลำภู 27
                        // อุดรธานี 29
                        // อุบลราชธานี 23
                        // อำนาจเจริญ 26

                        $array_near_far = array('49','9','47','53','52','54','51','48','50','62','45','38','43','44','42','46','40','39','41','34','28','25','36','19','77','20','32','37','24','33','30','35','21','22','31','27','29','23','26');
                        $select->where('sa.province_id in (?)',$array_near_far);
                        break;
                    case '3':
                        // ใต้
                        // กระบี่ 64
                        // ชุมพร 69
                        // ตรัง 72
                        // นครศรีธรรมราช 63
                        // นราธิวาส 76
                        // ปัตตานี 74
                        // พังงา 65
                        // พัทลุง 73
                        // ภูเก็ต 66
                        // ระนอง 68
                        // สตูล 71
                        // สงขลา 70
                        // สุราษฎร์ธานี 67
                        // ยะลา 75

                        $array_near_far = array('64','69','72','63','76','74','65','73','66','68','71','70','67','75');
                        $select->where('sa.province_id in (?)',$array_near_far);
                        break;
                }

            }

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
            $select->where('p.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or p.warehouse_id in (71,92)',null);
            // allow warehouse WMDGC - คลังเคลมDigital, Brandshop Warehouse at Kerry
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
  WHERE tg.`object_id`=p.`sn` LIMIT 1)AS tax_po,d.finance_group")));

        if (isset($params['allowpage']) and $params['allowpage'] == 'salelist')
        {
             // show tracking no
            $select->joinLeft(array('ds' => 'delivery_sales'), 'ds.sales_sn=p.sn', array('ds.delivery_order_id'));
            $select->joinLeft(array('do' => 'delivery_order'), 'do.id=ds.delivery_order_id', array('tracking_no'));
            
        }


        //Export imei ມີ imei

        if (isset($params['export_cn']) and $params['export_cn'])
        {
            $select->join(array('ir' => 'imei_return'), 'ir.return_sn = p.sn', array('ir.imei_sn','ir.creditnote_sn','ir_remark' => 'ir.remark','ir_return_type' => 'ir.return_type'));
            $select->join(array('i' => 'imei'), 'i.imei_sn = ir.imei_sn AND i.good_id=p.good_id AND i.good_color=p.good_color', array(null));
            $select->joinLeft(array('m2' => 'market'), 'm2.sn = ir.sales_order_sn AND m2.good_id=i.good_id AND m2.good_color=i.good_color', array('m2_sn_ref'=>'m2.sn_ref','m2_invoice_number'=>'m2.invoice_number','distributor_id'=>'m2.d_id'));
            $select->group('ir.imei_sn');
        }

        if (isset($params['gd']) && $params['gd'])
        {
            $select->group('p.good_id');
        }

        if (isset($params['gc']) && $params['gc'])
        {
            $select->group('p.good_color');
        }

        if (isset($params['export_no_cn']) and $params['export_no_cn'])
        {
            $select->join(array('ir' => 'imei_return'), 'ir.return_sn=p.sn', array('ir.imei_sn','ir.creditnote_sn','ir_remark' => 'ir.remark','ir_return_type' => 'ir.return_type'));
            $select->join(array('i' => 'imei'), 'ir.imei_sn = i.imei_sn AND p.good_id=i.good_id AND p.good_color=i.good_color', array(null));
            $select->joinLeft(array('m2' => 'market'), 'm2.sn = ir.sales_order_sn AND m2.good_id=i.good_id AND m2.good_color=i.good_color', array('m2_sn_ref'=>'m2.sn_ref','m2_invoice_number'=>'m2.invoice_number'));
            $select->where('p.isbacks = ?', 1);
            $select->where('ir.creditnote_sn is null',1);
            $select->group('ir.imei_sn');
        }

        if (isset($params['new_export']) and $params['new_export'])
        {
            $select->join(array('ir' => 'imei_return'), 'ir.return_sn=p.sn', array('ir.imei_sn','ir.creditnote_sn','ir_remark' => 'ir.remark','ir_return_type' => 'ir.return_type'));
            $select->join(array('i' => 'imei'), 'i.imei_sn = ir.imei_sn AND i.good_id=p.good_id AND i.good_color=p.good_color', array(null));
            $select->joinLeft(array('m2' => 'market'), 'm2.sn = ir.sales_order_sn AND m2.good_id=i.good_id AND m2.good_color=i.good_color', array('m2_sn_ref'=>'m2.sn_ref','m2_invoice_number'=>'m2.invoice_number','distributor_id'=>'m2.d_id'));
            $select->group('ir.imei_sn');
        }
        

        if (isset($params['order_packed_sim']) and $params['order_packed_sim']=="1")
        {

            $select->where('p.sn in( SELECT ii.sales_sn
            FROM packed_sim ps
            JOIN imei ii ON ps.distributor_id=ii.distributor_id AND ps.imei_sn=ii.imei_sn
            GROUP BY ii.sales_sn )');    
        }
        
        //echo $select;die;

        if (isset($params['export']) and $params['export'])
        {
            //WhereExport
            switch ($params['export']) {
                case 7: //Output VAT Report
                     $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time'));
                     $select->joinLeft(array('c' => 'customer_brandshop'), 'p.customer_id = c.customer_id'
                        , array(new Zend_Db_Expr("( 
                CASE 
                    WHEN p.d_id = 3691 OR NOW() >= '2016-04-18 00:00:00' 
                      THEN CASE
                        WHEN d.rank = 9 and p.sale_off_percent <=0 THEN SUM(ROUND((p.price * p.num), 2)) - IFNULL(p.total_spc_discount, 0) + ROUND((p.delivery_fee), 2) 
                        WHEN d.rank != 9 and p.sale_off_percent > 0 THEN SUM(ROUND(ROUND(cal_sale_off_percent(p.sale_off_percent,p.price,p.num,(p.price*p.num))/1,2)* p.num,2)) - IFNULL(p.total_spc_discount, 0) + ROUND((p.delivery_fee / 1), 2)
                        WHEN d.rank = 3 AND p.sale_off_percent <= 0 
                        THEN SUM(
                              ROUND(ROUND(cal_sale_off_percent (p.sale_off_percent,p.price,p.num,(p.price * p.num)) / 1,2) * p.num,2)
                            ) - IFNULL(p.total_spc_discount, 0) + ROUND((p.delivery_fee / 1), 2)
                        ELSE SUM(ROUND(((p.total / p.num) / 1), 2) * p.num) - IFNULL(p.total_spc_discount, 0) + ROUND((p.delivery_fee / 1), 2) 
                      END 
                    WHEN p.sn_ref = 'SO590407-00082' OR p.sn_ref = 'SO590408-00294' THEN
                        SUM( ROUND( ( (p.total/p.num)/1 ),2) * p.num )-ifnull(p.total_spc_discount,0) + ROUND((p.delivery_fee/1),2) 
                    WHEN p.sn_ref = 'SO590405-00222' THEN 
                        SUM( ROUND( ( p.total/1 ),2) )-ifnull(p.total_spc_discount,0) + ROUND((p.delivery_fee/1),2)
                    WHEN p.d_id = 21088 THEN 
                        SUM( ROUND( ( (p.total/p.num)/1 ),2) * p.num )-ifnull(p.total_spc_discount,0) + ROUND((p.delivery_fee/1),2)     
                    ELSE 
                        SUM( ROUND( ( p.total/1 ),2) ) -ifnull(p.total_spc_discount,0) + ROUND((p.delivery_fee/1),2)
                END ) as sum_total, c.customer_name,p.total_spc_discount,p.spc_discount,(SELECT concat('''',ph.phone_number_sn) FROM phone_number ph where ph.sales_order=p.sn and ph.status=1)as phone_number_sn")));

                    // $select->where('p.d_id not in(?)','21088');
                     $select->group('p.sn');
                     // echo $select;die;
                    break;
                case 20: //Output VAT Report New
                     $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time'));
                     $select->joinLeft(array('c' => 'customer_brandshop'), 'p.customer_id = c.customer_id'
                        , array(new Zend_Db_Expr("( 
                CASE 
                    WHEN p.d_id = 3691 OR NOW() >= '2016-04-18 00:00:00' 
                      THEN CASE
                        WHEN d.rank = 9 and p.sale_off_percent <=0 THEN SUM(ROUND((p.price * p.num), 2)) - IFNULL(p.total_spc_discount, 0) + ROUND((p.delivery_fee), 2) 
                        WHEN d.rank != 9 and p.sale_off_percent > 0 THEN SUM(ROUND(ROUND(cal_sale_off_percent(p.sale_off_percent,p.price,p.num,(p.price*p.num))/1,2)* p.num,2)) - IFNULL(p.total_spc_discount, 0) + ROUND((p.delivery_fee / 1), 2)
                        WHEN d.rank = 3 AND p.sale_off_percent <= 0 
                        THEN SUM(
                              ROUND(ROUND(cal_sale_off_percent (p.sale_off_percent,p.price,p.num,(p.price * p.num)) / 1,2) * p.num,2)
                            ) - IFNULL(p.total_spc_discount, 0) + ROUND((p.delivery_fee / 1), 2)
                        ELSE SUM(ROUND(((p.total / p.num) / 1), 2) * p.num) - IFNULL(p.total_spc_discount, 0) + ROUND((p.delivery_fee / 1), 2) 
                      END 
                    WHEN p.sn_ref = 'SO590407-00082' OR p.sn_ref = 'SO590408-00294' THEN
                        SUM( ROUND( ( (p.total/p.num)/1 ),2) * p.num )-ifnull(p.total_spc_discount,0) + ROUND((p.delivery_fee/1),2) 
                    WHEN p.sn_ref = 'SO590405-00222' THEN 
                        SUM( ROUND( ( p.total/1 ),2) )-ifnull(p.total_spc_discount,0) + ROUND((p.delivery_fee/1),2)
                    WHEN p.d_id = 21088 THEN 
                        SUM( ROUND( ( (p.total/p.num)/1 ),2) * p.num )-ifnull(p.total_spc_discount,0) + ROUND((p.delivery_fee/1),2)     
                    ELSE 
                        SUM( ROUND( ( p.total/1 ),2) ) -ifnull(p.total_spc_discount,0) + ROUND((p.delivery_fee/1),2)
                END ) as sum_total, c.tax_number as cus_tax_number,c.customer_name,p.total_spc_discount,p.spc_discount,(SELECT concat('''',ph.phone_number_sn) FROM phone_number ph where ph.sales_order=p.sn and ph.status=1)as phone_number_sn")));

                    // $select->where('p.d_id not in(?)','21088');
                     $select->group('p.sn');
                     // echo $select;die;
                    break;
                case 8: //Order Status
                     $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time','SUM(p.total) as sum_total'));

                     $select->group('p.sn');
                    break;
                case 10: //Export By Distributor
                     $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time','SUM(p.num) as sum_num','SUM(p.total) as sum_total'));

                     $select->where('p.cat_id in(11,12)');
                     $select->group('p.sn');
                    break;
                case 11: //Sale Master Data By Imei
                      $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time','p.canceled'));
                      $select->joinLeft(array('i' => 'imei'), 'p.sn = i.sales_sn 
                    AND p.good_id <> 127 
                    AND p.cat_id = 11 
                    AND p.good_id = i.good_id 
                    AND p.good_color = i.good_color'
                        , array(new Zend_Db_Expr("i.imei_sn"),"i.activated_date"));
                      $select->joinLeft(array('ps' => 'packed_sim'), 'ps.imei_sn=i.imei_sn and ps.distributor_id=p.d_id', array('ps.simcard','ps.tel_no','ps.sim_activated_updated_at','ps.confirm_rebate_date','ps.operator','ps.sellout_updated_at')); 

                      //echo $select;die;
                    break;
                case 13: //export
                      $select->joinLeft(array('spa' => 'shipping_address'), 'spa.id=p.shipping_address', array('spa.contact_name','spa.address','spa.phone'));
                      $select->joinLeft(array('rm' => 'hr.regional_market'), 's.regional_market = rm.id', array('a.name as sales_area','(SELECT 
    ROUND(SUM(ROUND((IF(mm.sale_off_percent >0,ROUND((((mm.price*mm.sale_off_percent/100)*100)/100),2),mm.price)/1),2)*mm.num*1),2)
  FROM
    market mm 
  WHERE mm.sn = p.`sn`  GROUP BY mm.sn) AS `grand_total`'
,'(SELECT 
    ROUND((if(mm.sale_off_percent >0,ROUND((((mm.price*mm.sale_off_percent/100)*100)/100),2),mm.price)/1),2)*mm.num
  FROM
    market mm 
  WHERE mm.sn = p.`sn` and p.`good_id`=mm.good_id and p.`good_color`=mm.good_color group by mm.sn) AS `total_amount_ex_vat`'
  ,'  (SELECT 
    ROUND(ROUND(ROUND((IF(mm.sale_off_percent >0,ROUND((((mm.price*mm.sale_off_percent/100)*100)/100),2),mm.price)/1),2)*mm.num,2)*1,2)
  FROM
    market mm 
  WHERE mm.sn = p.`sn` AND p.`good_id`=mm.good_id AND p.`good_color`=mm.good_color GROUP BY mm.sn) AS `total_amount_in_vat` ,p.`salesman` as sales_admin_id,CONCAT(ss.firstname," ",ss.lastname)AS sales_admin,p.bs_campaign',"(SELECT concat('''',ph.phone_number_sn) FROM phone_number ph where ph.sales_order=p.sn and ph.status=1)as phone_number_sn
  "));
             $select->joinLeft(array('a' => 'hr.area'), 'rm.area_id = a.id', null);
             $select->joinLeft(array('ss' => 'warehouse.staff'), 'p.`salesman`=ss.id', null);
                    break;
                case 19: //Sale Master Data Original By Imei
                      $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time','p.canceled'));
                      $select->joinLeft(array('i' => 'imei'), 'p.sn = i.sales_sn 
                    AND p.good_id <> 127  
                    AND p.good_id = i.good_id 
                    AND p.good_color = i.good_color'
                        , array(new Zend_Db_Expr("i.imei_sn"),"i.activated_date"));
                      $select->joinLeft(array('ps' => 'packed_sim'), 'ps.imei_sn=i.imei_sn and ps.distributor_id=p.d_id', array('ps.simcard','ps.tel_no','ps.sim_activated_updated_at','ps.confirm_rebate_date','ps.operator','ps.sellout_updated_at'));  
                    break; 
                case 21: //Sale Master Data By Imei 2
                      $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time','p.canceled'));
                      $select->joinLeft(array('i' => 'imei'), 'p.sn = i.sales_sn 
                      
                    AND p.good_id <> 127 
                    AND p.cat_id = 11 
                    AND p.good_id = i.good_id 
                    AND p.good_color = i.good_color'
                        , array(new Zend_Db_Expr("i.imei_sn"),"i.activated_date"));
                      $select->joinLeft(array('ps' => 'packed_sim'), 'ps.imei_sn=i.imei_sn and ps.distributor_id=p.d_id', array('ps.simcard','ps.tel_no','ps.sim_activated_updated_at','ps.confirm_rebate_date','ps.operator','ps.sellout_updated_at')); 
                      $select->joinLeft(array('po' => 'purchase_order'), 'po.sn = i.po_sn', 'po.sn_ref as po_no');
                      $select->joinLeft(array('im2' => 'import_imei2'), 'im2.imei1 = i.imei_sn', 'im2.imei2 as imei2_sn');
                      //echo $select;die;
                    break;    
                default:
                    $select->joinLeft(array('rm' => 'hr.regional_market'), 's.regional_market = rm.id', array('a.name as sales_area','(SELECT 
    ROUND(SUM(ROUND((IF(mm.sale_off_percent >0,ROUND((((mm.price*mm.sale_off_percent/100)*100)/100),2),mm.price)/1),2)*mm.num*1),2)
  FROM
    market mm 
  WHERE mm.sn = p.`sn`  GROUP BY mm.sn) AS `grand_total`'
,'(SELECT 
    ROUND((if(mm.sale_off_percent >0,ROUND((((mm.price*mm.sale_off_percent/100)*100)/100),2),mm.price)/1),2)*mm.num
  FROM
    market mm 
  WHERE mm.sn = p.`sn` and p.`good_id`=mm.good_id and p.`good_color`=mm.good_color group by mm.sn) AS `total_amount_ex_vat`'
  ,'  (SELECT 
    ROUND(ROUND(ROUND((IF(mm.sale_off_percent >0,ROUND((((mm.price*mm.sale_off_percent/100)*100)/100),2),mm.price)/1),2)*mm.num,2)*1,2)
  FROM
    market mm 
  WHERE mm.sn = p.`sn` AND p.`good_id`=mm.good_id AND p.`good_color`=mm.good_color GROUP BY mm.sn) AS `total_amount_in_vat` ,p.`salesman` as sales_admin_id,CONCAT(ss.firstname," ",ss.lastname)AS sales_admin,p.bs_campaign',"(SELECT concat('''',ph.phone_number_sn) FROM phone_number ph where ph.sales_order=p.sn and ph.status=1)as phone_number_sn
  "));
             $select->joinLeft(array('a' => 'hr.area'), 'rm.area_id = a.id', null);
             $select->joinLeft(array('ss' => 'warehouse.staff'), 'p.`salesman`=ss.id', null);
                    break;
            }


            $select_group = $db->select()
                ->from(array('u' => 'distributor_group_user'),array('u.group_id'))
                ->where('u.user_id=?',$userStorage->id);
            $result_group = $db->fetchAll($select_group);
            $group_id = "";
            if ($result_group){
                foreach ($result_group as $to) {
                    $group_id .= $to['group_id'].',';
                }

                $select->where('d.group_id in('.rtrim($group_id, ',').')',null);
            }

            $select->order('p.sn asc');

             //echo $select;die;
            return $select->__toString();
        }else{
             

             if( isset($params['leftjoin_checkmoney']) && $params['leftjoin_checkmoney'] != '' ){
                $select->joinLeft(array('ck' => 'checkmoney'), 'ck.sn=p.sn and ck.type = 1', array('payment_date' => 'pay_time','payment_balance' => 'pay_money', 'payment_note' => 'note'));
                $select->joinLeft(array('ba' => 'bank'), 'ba.id=ck.bank', array('bank_name' => 'ba.name'));

            }else{
                $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time'));
            }

            if( isset($params['newsalespayment']) && $params['newsalespayment'] != '' ){
                $select->where('p.payment_no is not null',1);
                $select->group('p.payment_no');

            }

            $select_group = $db->select()
                ->from(array('u' => 'distributor_group_user'),array('u.group_id'))
                ->where('u.user_id=?',$userStorage->id);
            $result_group = $db->fetchAll($select_group);
            $group_id = "";
            if ($result_group){
                foreach ($result_group as $to) {
                    $group_id .= $to['group_id'].',';
                }

                $select->where('d.group_id in('.rtrim($group_id, ',').')',null);
            }

            if (isset($params['warehouse_out']) and $params['warehouse_out']) {


                //$now=date_create('2017-11-11 00:00:00');
                //$now = date_format($now,"Y-m-d H:i:s");

                $now = date("Y-m-d H:i:s");
                $paymentDate = strtotime($now);
                $contractDateBegin = strtotime("2017-11-08 00:00:00");
                $contractDateEnd = strtotime("2017-11-08 23:59:59");
                $show_all = $params['show_all'];

                if($paymentDate >= $contractDateBegin && $paymentDate <= $contractDateEnd && $show_all !=1)
                {

                    $select->where('d.oppoclub_type is not null',null);
                    $select->where('p.good_id =?',299);
                } 

            }

            $select->where($userStorage->id . '=' . $userStorage->id);
        
            
            $select->order('p.cat_id asc');
            //echo $select;die;
            $result = $db->fetchAll($select);
        }

        $select->where($userStorage->id . '=' . $userStorage->id);

        // echo $select;die; 
        // $result = $db->fetchAll($sql);

        if (isset($params['not_get_total']) and $params['not_get_total'])
            return $result;

        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    // public function showtracking(){
    //     $db = Zend_Registry::get('db');
    //     $select = $db->select()
    //     ->from(array('p'=> $this->_name), array('p.sn'));

    //     $select->joinLeft(array('ds' => 'delivery_sales'), 'ds.sales_sn=p.sn', array('ds.delivery_order_id'));
    //     $select->joinLeft(array('do' => 'delivery_order'), 'do.id=ds.delivery_order_id', array('tracking_no'));
    //     // echo($select); die;
    //      return $db->fetchRow($select);
    // }

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

        $select->joinLeft(array('dg' => 'distributor_group'), 'dg.group_id = d.group_id', array('dg.group_name'));

        $select->join(array('i' => 'imei'), 'p.sn = i.sales_sn AND i.distributor_id = p.d_id AND i.good_id=p.good_id AND i.good_color=p.good_color' 
            , array('i.imei_sn','i.activated_date'));

        if (isset($params['product_out']) and $params['product_out']) {
            $select->where('p.invoice_time IS NULL OR p.print_time IS NULL OR p.invoice_number is null',1);
            $select->where('p.canceled !=1');
        }

        if (isset($params['product_out_archived']) and $params['product_out_archived']) {
            $select->where('p.print_time is not null',1);
            $select->where('p.invoice_time is not null',1);
            $select->where('p.invoice_number is not null',1);
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

        if (isset($params['group_id']) && $params['group_id'])
            $select->where('d.group_id = ?', $params['group_id']);

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
            $select->where('p.pay_time is not null',1);
            $select->where('p.pay_time <> \'\'');
            $select->where('p.pay_time <> 0');
        }

        if (isset($params['no_payment']) and $params['no_payment'])
            $select->where('p.pay_time IS NULL OR p.pay_time = \'\' OR p.pay_time = 0');

        if (isset($params['outmysql_time']) and $params['outmysql_time']){
            $select->where('p.outmysql_time is not null',1);
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
              $select->where('p.sales_confirm_date IS NOT NULL AND p.sales_confirm_id is not null',1);
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
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.add_time >= ?', $year.'-'.$month.'-'.$day.' '.$time);
        }

        if (isset($params['created_at_to']) and $params['created_at_to']){
            list( $day, $month, $year ) = explode('/', $params['created_at_to']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.add_time <= ?', $year.'-'.$month.'-'.$day.' '.$time);
        }

        if (isset($params['out_time_from']) and $params['out_time_from']){
            list( $day, $month, $year ) = explode('/', $params['out_time_from']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.outmysql_time >= ?', $year.'-'.$month.'-'.$day.' '.$time);
        }

        if (isset($params['out_time_to']) and $params['out_time_to']){
            list( $day, $month, $year ) = explode('/', $params['out_time_to']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.outmysql_time <= ?', $year.'-'.$month.'-'.$day.' '.$time);
        }

        if (isset($params['finance_confirm_time_from']) and $params['finance_confirm_time_from']){
            list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_from']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.pay_time >= ?', $year.'-'.$month.'-'.$day.' '.$time);
        }

        if (isset($params['finance_confirm_time_to']) and $params['finance_confirm_time_to']){
            list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_to']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.pay_time <= ?', $year.'-'.$month.'-'.$day.' '.$time);
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
        // echo $select;die;
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

        $select->joinLeft(array('dg' => 'distributor_group'), 'dg.group_id = d.group_id', array('dg.group_name'));


        if (isset($params['product_out']) and $params['product_out']) {
            // $select->where('p.invoice_time IS NULL OR p.print_time IS NULL OR p.invoice_number is null',1);
            $select->where('p.canceled !=1');
        }

        if (isset($params['product_out_archived']) and $params['product_out_archived']) {
            $select->where('p.print_time is not null',1);
            $select->where('p.invoice_time is not null',1);
            $select->where('p.invoice_number is not null',1);
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

        if (isset($params['group_id']) && $params['group_id'])
            $select->where('d.group_id = ?', $params['group_id']);

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
            $select->where('p.pay_time is not null',1);
            $select->where('p.pay_time <> \'\'');
            $select->where('p.pay_time <> 0');
        }

        if (isset($params['no_payment']) and $params['no_payment'])
            $select->where('p.pay_time IS NULL OR p.pay_time = \'\' OR p.pay_time = 0');

        if (isset($params['outmysql_time']) and $params['outmysql_time']){
            $select->where('p.outmysql_time is not null',1);
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
              $select->where('p.sales_confirm_date IS NOT NULL AND p.sales_confirm_id is not null',1);
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

        if ((isset($params['product_out']) and $params['product_out']) || (isset($params['warehouse_out']) and $params['warehouse_out'])) {

            if (isset($params['out_time_from']) and $params['out_time_from']){
                list( $day, $month, $year ) = explode('/', $params['out_time_from']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.outmysql_time >= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            }

            if (isset($params['out_time_to']) and $params['out_time_to']){
                list( $day, $month, $year ) = explode('/', $params['out_time_to']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.outmysql_time <= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            }

            // -------------------------
            if (isset($params['created_at_from']) and $params['created_at_from']){
                list( $day, $month, $year ) = explode('/', $params['created_at_from']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.add_time >= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            }

            if (isset($params['created_at_to']) and $params['created_at_to']){
                list( $day, $month, $year ) = explode('/', $params['created_at_to']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.add_time <= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            } 

            // -------------------------
            if (isset($params['finance_confirm_time_from']) and $params['finance_confirm_time_from']){
                list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_from']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.pay_time >= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            }

            if (isset($params['finance_confirm_time_to']) and $params['finance_confirm_time_to']){
                list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_to']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $select->where('p.pay_time <= ?', $year.'-'.$month.'-'.$day.' '.$time);
                }
            }          

        }else{
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
         // $db = Zend_Registry::get('db');
         // $select = $db->select()
         //    ->from(array('p' => 'market')
         //        ,array('total_price' => 'SUM(p.total) - IFNULL((CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END),0) + IFNULL(p.delivery_fee,0)'
         //        ,'delivery_fee'=>'ifnull(p.delivery_fee,0)','(CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END) AS total_discount'))
         //    ->where('p.sn = ?', $sn)
         //    ->where('p.status = 1', null);

        $db = Zend_Registry::get('db');
         $select = $db->select()
            ->from(array('p' => 'market'),array('total_price' => 'ROUND(ROUND(( TRUNCATE(( sum( (ROUND((( p.price - ((p.price*ifnull(p.sale_off_percent,0)/100)*100)/100 )/1),2)*p.num) ) - p.total_spc_discount + ( ifnull(p.delivery_fee/1,0) ) ) ,2)*1 ),2) - IFNULL((CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END),0),2)'
                ,'delivery_fee'=>'ifnull(p.delivery_fee,0)','ROUND((CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END),2) AS total_discount','ROUND((CASE p.use_dp WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM deposit_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END),2) AS total_deposit'))
            ->where('p.sn = ?', $sn)
            ->where('p.status = 1', null);

         //echo $select;die;
                    
         $result = $db->fetchOne($select);
        
         return $result;
    }

    public function getPriceAndDetails($arr_sn)
    {

        $db = Zend_Registry::get('db');
         $select = $db->select()
            ->from(array('p' => 'market')
                ,array('p.sn','p.sn_ref','p.d_id','total_price' => 'ROUND(ROUND(( TRUNCATE(( sum( (ROUND((( p.price - ((p.price*ifnull(p.sale_off_percent,0)/100)*100)/100 )/1),2)*p.num) ) - p.total_spc_discount + ( ifnull(p.delivery_fee/1,0) ) ) ,2)*1 ),2) - IFNULL((CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END),0),2)'
                ,'delivery_fee'=>'ifnull(p.delivery_fee,0)','ROUND((CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END),2) AS total_discount','ROUND((CASE p.use_dp WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM deposit_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END),2) AS total_deposit'))
            ->joinLeft(array('d' => 'distributor'),'d.id=p.d_id',array('d.title','d.rank'))
            ->where('p.sn in (?)', $arr_sn)
            ->where('p.status = 1', null)
            ->group('p.sn');

        // echo $select;die;
                    
         $result = $db->fetchAll($select);
        
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

            ->where('m.old_data is null',1)
            ->where('m.outmysql_time is not null',1)
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
            ->where('m.old_data is null',1)
            ->where('m.canceled <> 1')
            ->where('m.outmysql_time is not null',1)
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

    public function fetchCredit_Note_All($sn)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('cn' => 'credit_note_tran'), array('sales_order' => 'cn.sales_order','cn.creditnote_sn','sum(cn.use_discount) AS total_discount'));


        if (!$sn)
            return false;

        $select->where('cn.sales_order in('.$sn.')', null);
        $select->group('cn.creditnote_sn');
        //echo $select;die;
        return $db->fetchAll($select);
    }

    public function CheckCredit_Note($creditnote_sn)
    {
        $db = Zend_Registry::get('db');
        $select = "SELECT cn.creditnote_sn,IFNULL(SUM(m.total),0) AS cn_total_amount,cn.total_amount,cn.creditnote_type 
                FROM market m
                left join credit_note cn on m.creditnote_sn=cn.creditnote_sn and m.d_id=cn.distributor_id
                WHERE 1=1 AND m.creditnote_sn ='".$creditnote_sn."'
                AND m.isbacks=1
                AND m.shipping_yes_time IS NOT NULL
                group by m.creditnote_sn";
        //echo $select;die;
        return $db->fetchAll($select);
    }

     public function fetchDeposit($sn)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('cn' => 'deposit_tran'), array('sales_order' => 'cn.sales_order','cn.deposit_sn','(cn.use_discount) AS total_deposit'));


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
        $select->where('p.canceled <> 1',null);

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
        $select->where('p.canceled <> 1',null);
        
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

        // start : new get data
        $url_api_getQuota = API_IOPPO_URL . "oppo-check-quota";

        $send_data = http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url_api_getQuota);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$send_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);

        return $server_output;
        // end : new get data

        // old get quota
        $db = Zend_Registry::get('db');
        $num = $params['num'];

        //allow quota for warehouse kerry only
        if (in_array($params['type'], [FOR_RETAILER,FOR_DEMO,FOR_APK])) {
            
        $toDay = date('Y-m-d');
        $select_q = $db->select()
            ->from(array('d' => 'quota_oppo_by_distributor'), array('d.quantity'));
       $select_q->where('d.warehouse = ?', $params['warehouse_id']);
       $select_q->where('d.d_id = ?', $params['distributor_id']);
       $select_q->where('d.cat_id = ?', $params['cat_id']);
       $select_q->where('d.good_id = ?', $params['good_id']);
       $select_q->where('d.good_color = ?', $params['good_color']);
       $select_q->where('d.status = 1');
       $select_q->where('d.del is null',1);
       $select_q->where('date_format(d.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
       $quota_by_distributor = $db->fetchRow($select_q);

       $select_q_d = $db->select()
            ->from(array('nqd' => 'new_quota_distributor'), array());
       $select_q_d->joinLeft(array('nqdd' => 'new_quota_distributor_details'),'nqdd.nqd_id=nqd.id',array('nqdd.num','nqdd.status'));
       $select_q_d->where('nqd.warehouse_id = ?', $params['warehouse_id']);
       $select_q_d->where('nqd.d_id = ?', $params['distributor_id']);
       $select_q_d->where('nqd.order_type = ?', $params['type']);
       $select_q_d->where('nqd.good_id = ?', $params['good_id']);
       $select_q_d->where('nqdd.good_color = ?', $params['good_color']);
       $select_q_d->where('nqd.status = 1');
       $select_q_d->where('nqdd.status <> 0');
       $select_q_d->where('date_format(nqd.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
       $new_quota_by_distributor = $db->fetchRow($select_q_d);
      
        if (isset($quota_by_distributor) and $quota_by_distributor and $params['type'] == FOR_RETAILER) {
            $data = $quota_by_distributor['quantity'];

            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran_distributor'), array('sum(lq.num)'));
            // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);
            // $select_m->where('lq.d_id = ?', $params['distributor_id']);
            // $select_m->where('lq.cat_id = ?', $params['cat_id']);
            // $select_m->where('lq.good_id = ?', $params['good_id']);
            // $select_m->where('lq.good_color = ?', $params['good_color']);
            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);
            $select_m->where('m.d_id = ?', $params['distributor_id']);
            $select_m->where('m.cat_id = ?', $params['cat_id']);
            $select_m->where('m.good_id = ?', $params['good_id']);
            $select_m->where('m.good_color = ?', $params['good_color']);
            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
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

        }else if (isset($new_quota_by_distributor) and $new_quota_by_distributor) {

            if($new_quota_by_distributor['status'] == 2){
                return 1;
            }

            if($new_quota_by_distributor['num'] == 0){
                return 0;
            }

            $data = $new_quota_by_distributor['num'];


            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran_distributor'), array('sum(lq.num)'));
            // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);
            // $select_m->where('lq.d_id = ?', $params['distributor_id']);
            // $select_m->where('lq.cat_id = ?', $params['cat_id']);
            // $select_m->where('lq.good_id = ?', $params['good_id']);
            // $select_m->where('lq.good_color = ?', $params['good_color']);
            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);
            $select_m->where('m.d_id = ?', $params['distributor_id']);
            $select_m->where('m.cat_id = ?', $params['cat_id']);
            $select_m->where('m.good_id = ?', $params['good_id']);
            $select_m->where('m.good_color = ?', $params['good_color']);
            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
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

            // by pass num < current num
            if($market >= $num){
                return 0;
            }

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

        $good_type = $params['type'];

        $select_a = $db->select()
            ->from(array('d' => 'distributor'), array('d.rank','d.quota_channel','d.group_id'));

       $select_a->joinLeft(array('r' => HR_DB.'.regional_market'),'r.id=d.region',array());
       $select_a->joinLeft(array('a' => HR_DB.'.area'),'r.area_id=a.id',array('area_id'=>'a.id'));
       $select_a->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
       $select_a->where('d.id = ?', $params['distributor_id']);
       $distributor = $db->fetchRow($select_a);

       if(!isset($distributor['group_type_id']) || !$distributor['group_type_id']){
            return 2;
       }

       //  if ($distributor['quota_channel']) {
       //     if ($distributor['quota_channel'] == 1) {
       //        $rank = 1;
             
       //     }else if ($distributor['quota_channel'] == 10) {
       //        $rank = 10;
       //     }
       // }else{
       //      if (in_array($distributor['rank'], array(7,8))) {
       //         $rank = 7; 
       //      }else if (in_array($distributor['rank'], array(1,2,5,6))) {
       //        $rank = 1;
       //      }else{
       //        $rank = $distributor['rank'];
       //      }
       // }

       // Group brand Shop by Dealer to brand shop
       $old_group_id = '';
       if($distributor['group_type_id'] == 11){
        $old_group_id = $distributor['group_type_id'];
        $distributor['group_type_id'] = 10;
       }

        $select_w = $db->select()
            ->from(array('o' => 'quota_oppo'), array('o.id'));
        $select_w->where('o.dis_type = ?', $distributor['group_type_id']);
        $select_w->where('o.good_id = ?', $params['good_id']);
        $select_w->where('o.good_color = ?', $params['good_color']);
        $select_w->where('o.warehouse_id = ?', $params['warehouse_id']);
        $select_w->where('o.good_type = ?', $good_type);
        $select_w->where('o.quota_date = ?',$toDay);
        $select_w->where('o.status = ?',1);
        
        $quota_oppo = $db->fetchAll($select_w);
        
       if (isset($quota_oppo) and $quota_oppo) {
       
       // $dis_type = '';
       // if ($distributor['quota_channel']) {
       //     if ($distributor['quota_channel'] == 1) {
       //        $action_quota = "org";
       //        $dis_type     = "have";
       //     }else if ($distributor['quota_channel'] == 10) {
       //        $action_quota = "brandshop";
       //        $dis_type     = "have";
       //     }
       // }else{
       //      if (in_array($distributor['rank'], array(7,8))) {
       //         $action_quota = "dealer"; 
       //      }else if (in_array($distributor['rank'], array(1,2,5,6))) {
       //        $action_quota = "org";
       //      }else if ($distributor['rank'] == 10) {
       //        $action_quota = "brandshop";
       //      }
       // }

        switch ($distributor['group_type_id']) {
            case '3':
                $action_quota = "ka";
                break;
            case '2':
                $action_quota = "kr";
                break;
            case '1':
                $action_quota = "dealer";
               break;
            case '10':
                $action_quota = "brandshop";
               break;
            case '4':
                $action_quota = "operator";
               break;
           default:
               # code...
               break;
        }

        if ($action_quota == "dealer") { //dealer ,hub
            
            $select_q = $db->select()
                ->from(array('q' => 'quota_oppo'), array('q.quantity','q.not_allow_area'));
            $select_q->where('q.area_id = ?', $distributor['area_id']);
            $select_q->where('q.status = ?',1);
            $select_q->where('q.dis_type = ?', 1);
            $select_q->where('q.good_id = ?', $params['good_id']);
            $select_q->where('q.good_color = ?', $params['good_color']);
            $select_q->where('q.warehouse_id = ?', $params['warehouse_id']);
            $select_q->where('q.good_type = ?', $good_type);
            $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
            // echo $select_q;die;
            $data = $db->fetchRow($select_q);

            if ($data['quantity'] || $data['not_allow_area']) {

                if($data['not_allow_area']){
                    return 1;
                }else{

                    // start : old
                    // $select_m = $db->select()
                    //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
                    // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
                    // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
                    // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
                    // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
                    // $select_m->where('dg.group_type_id = ?', 1);
                    // $select_m->where('a.id = ?', $distributor['area_id'] );
                    // $select_m->where('lq.good_id = ?', $params['good_id']);
                    // $select_m->where('lq.good_color = ?', $params['good_color']);
                    // $select_m->where('lq.good_type = ?', $good_type);

                    // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);

                    // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));

                    // // echo $select_m;
                    // $isSum = $db->fetchOne($select_m);
                    // end : old


                    $select_m = $db->select()
                        ->from(array('m' => 'market'), array('sum(m.num)'));
                    $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
                    $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
                    $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
                    $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
                    $select_m->where('dg.group_type_id = ?', 1);
                    $select_m->where('a.id = ?', $distributor['area_id'] );
                    $select_m->where('m.good_id = ?', $params['good_id']);
                    $select_m->where('m.good_color = ?', $params['good_color']);
                    $select_m->where('m.type = ?', $good_type);

                    $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);

                    $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));

                    // echo $select_m;
                    $isSum = $db->fetchOne($select_m);


                    if ($params['sales_sn']) {
                        $select_k = $db->select()
                            ->from(array('m' => 'market'), array('m.num'));
                        $select_k->where('m.sn = ?', $params['sales_sn']);
                        $select_k->where('m.d_id = ?', $params['distributor_id']);
                        $select_k->where('m.cat_id = ?', $params['cat_id']);
                        $select_k->where('m.good_id = ?', $params['good_id']);
                        $select_k->where('m.good_color = ?', $params['good_color']);
                        $select_k->where('m.warehouse_id = ?', $params['warehouse_id']);


                        $market = $db->fetchOne($select_k);

                        // by pass num < current num
                        if($market >= $num){
                            return 0;
                        }

                        if($market > $isSum){ 
                            $isSum = $market - $isSum;
                        }else{
                            $isSum = $isSum - $market;
                       }
                    }
                }

                $data = $data['quantity'];
        
            }else{

                unset($data);
                $select_qt = $db->select()
                ->from(array('q' => 'quota_oppo'), array('q.quantity'));
                $select_qt->where('q.channel = ?', 99);
                $select_qt->where('q.dis_type = ?', 1);
                $select_qt->where('q.status = ?',1);
                $select_qt->where('q.good_id = ?', $params['good_id']);
                $select_qt->where('q.good_color = ?', $params['good_color']);
                $select_qt->where('q.warehouse_id = ?', $params['warehouse_id']);
                $select_qt->where('q.good_type = ?', $good_type);
                $select_qt->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
                $data = $db->fetchOne($select_qt);

                $select_q = $db->select()
                ->from(array('q' => 'quota_oppo'), array('q.area_id'));
                $select_q->where('q.dis_type = ?', 1);
                $select_q->where('q.status = ?',1);
                $select_q->where('q.quantity = ?', 0);
                $select_q->where('q.good_id = ?', $params['good_id']);
                $select_q->where('q.good_color = ?', $params['good_color']);
                $select_q->where('q.warehouse_id = ?', $params['warehouse_id']);
                $select_q->where('q.good_type = ?', $good_type);
                $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
                $inArea = $db->fetchAll($select_q);
                if ($inArea) {

                    // start : old
                    // $select_m = $db->select()
                    // ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
                    // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
                    // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
                    // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
                    // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
                    // $select_m->where('dg.group_type_id = ?', 1);
                    // $select_m->where('a.id in (?)', $inArea );
                    // $select_m->where('lq.good_id = ?', $params['good_id']);
                    // $select_m->where('lq.good_color = ?', $params['good_color']);
                    // $select_m->where('lq.good_type = ?', $good_type);
                    // $select_m->where('d.quota_channel is null',1);

                    // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);

                    // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
                    // // echo $select_m;
                    // $isSum = $db->fetchOne($select_m);
                    // end : old


                    $select_m = $db->select()
                    ->from(array('m' => 'market'), array('sum(m.num)'));
                    $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
                    $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
                    $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
                    $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
                    $select_m->where('dg.group_type_id = ?', 1);
                    $select_m->where('a.id in (?)', $inArea );
                    $select_m->where('m.good_id = ?', $params['good_id']);
                    $select_m->where('m.good_color = ?', $params['good_color']);
                    $select_m->where('m.type = ?', $good_type);
                    $select_m->where('d.quota_channel is null',1);

                    $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);

                    $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
                    // echo $select_m;
                    $isSum = $db->fetchOne($select_m);

                }

                if ($params['sales_sn']) {
                    $select_k = $db->select()
                        ->from(array('m' => 'market'), array('m.num'));
                    $select_k->where('m.sn = ?', $params['sales_sn']);
                    $select_k->where('m.d_id = ?', $params['distributor_id']);
                    $select_k->where('m.cat_id = ?', $params['cat_id']);
                    $select_k->where('m.good_id = ?', $params['good_id']);
                    $select_k->where('m.good_color = ?', $params['good_color']);
                    $select_k->where('m.warehouse_id = ?', $params['warehouse_id']);

                    $market = $db->fetchOne($select_k);
                    // echo '--'.$market.'---';
                    // echo '--'.$isSum.'---';

                    // by pass num < current num
                    if($market >= $num){
                        return 0;
                    }

                    if($market > $isSum){ 
                        $isSum = $market - $isSum;
                    }else{
                        $isSum = $isSum - $market;
                    }
                    // echo '+'.$isSum.'+';
                }
                
            }
        // echo $params['sales_sn'];
        //  exit;

        }
        // if ($action_quota == "org") { //ORG

        //    $select_c = $db->select()
        //         ->from(array('d' => 'distributor'), array('d.quota_channel'));
        //     $select_c->where('d.id = ?', $params['distributor_id']);
        //     $channel = $db->fetchOne($select_c); 
             
        //     $select_q = $db->select()
        //             ->from(array('q' => 'quota_oppo'), array('q.quantity'));
              
        //         $select_q->where('q.dis_type = ?',1 );
        //         $select_q->where('q.good_id = ?', $params['good_id']);
        //         $select_q->where('q.good_color = ?', $params['good_color']);
        //         $select_q->where('q.status = ?', 1);
        //         $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
               
        //         $data = $db->fetchOne($select_q);

        //         $select_m = $db->select()
        //             ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
        //         $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
        //         $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
        //         $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
        //         $select_m->where('(d.quota_channel = ?', 1 );
        //         $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
               
        //         $select_m->where('lq.good_id = ?', $params['good_id']);
        //         $select_m->where('lq.good_color = ?', $params['good_color']);
        //         $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
               
        //         $isSum = $db->fetchOne($select_m);
        //         if ($params['sales_sn']) {
        //     $select_k = $db->select()
        //         ->from(array('m' => 'market'), array('m.num'));
        //     $select_k->where('m.sn = ?', $params['sales_sn']);
        //     $select_k->where('m.d_id = ?', $params['distributor_id']);
        //     $select_k->where('m.cat_id = ?', $params['cat_id']);
        //     $select_k->where('m.good_id = ?', $params['good_id']);
        //     $select_k->where('m.good_color = ?', $params['good_color']);

        //     $market = $db->fetchOne($select_k);
        //        if($market > $isSum){ 
        //        $isSum = $market - $isSum;
        //        }else{
        //         $isSum = $isSum - $market;
        //        }
        //     }
               
        // }

        if ($action_quota == "ka") { //ka

           $select_c = $db->select()
                ->from(array('d' => 'distributor'), array('d.quota_channel'));
            $select_c->where('d.id = ?', $params['distributor_id']);
            $channel = $db->fetchOne($select_c); 
             
            $select_q = $db->select()
                    ->from(array('q' => 'quota_oppo'), array('q.quantity'));
              
            $select_q->where('q.dis_type = ?',3);
            $select_q->where('q.good_id = ?', $params['good_id']);
            $select_q->where('q.good_color = ?', $params['good_color']);
            $select_q->where('q.warehouse_id = ?', $params['warehouse_id']);
            $select_q->where('q.good_type = ?', $good_type);
            $select_q->where('q.status = ?', 1);
            $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $data = $db->fetchOne($select_q);


            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
            // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
            // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            // $select_m->where('dg.group_type_id IN (?)', [3]);
            // // $select_m->where('(d.quota_channel = ?', 1 );
            // // $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
           
            // $select_m->where('lq.good_id = ?', $params['good_id']);
            // $select_m->where('lq.good_color = ?', $params['good_color']);
            // $select_m->where('lq.good_type = ?', $good_type);

            // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);

            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
            $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            $select_m->where('dg.group_type_id IN (?)', [3]);
            // $select_m->where('(d.quota_channel = ?', 1 );
            // $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
           
            $select_m->where('m.good_id = ?', $params['good_id']);
            $select_m->where('m.good_color = ?', $params['good_color']);
            $select_m->where('m.type = ?', $good_type);

            $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);

            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $isSum = $db->fetchOne($select_m);


            if ($params['sales_sn']) {
                $select_k = $db->select()
                    ->from(array('m' => 'market'), array('m.num'));
                $select_k->where('m.sn = ?', $params['sales_sn']);
                $select_k->where('m.d_id = ?', $params['distributor_id']);
                $select_k->where('m.cat_id = ?', $params['cat_id']);
                $select_k->where('m.good_id = ?', $params['good_id']);
                $select_k->where('m.good_color = ?', $params['good_color']);
                $select_k->where('m.warehouse_id = ?', $params['warehouse_id']);

                $market = $db->fetchOne($select_k);

                // by pass num < current num
                if($market >= $num){
                    return 0;
                }
                
                if($market > $isSum){ 
                   $isSum = $market - $isSum;
                }else{
                    $isSum = $isSum - $market;
                }
            }
               
        }

        if ($action_quota == "operator") { //operator

           $select_c = $db->select()
                ->from(array('d' => 'distributor'), array('d.quota_channel'));
            $select_c->where('d.id = ?', $params['distributor_id']);
            $channel = $db->fetchOne($select_c); 
             
            $select_q = $db->select()
                    ->from(array('q' => 'quota_oppo'), array('q.quantity'));
              
            $select_q->where('q.dis_type = ?',4);
            $select_q->where('q.good_id = ?', $params['good_id']);
            $select_q->where('q.good_color = ?', $params['good_color']);
            $select_q->where('q.warehouse_id = ?', $params['warehouse_id']);
            $select_q->where('q.good_type = ?', $good_type);
            $select_q->where('q.status = ?', 1);
            $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $data = $db->fetchOne($select_q);

            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
            // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
            // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            // $select_m->where('dg.group_type_id IN (?)', [4]);
            // // $select_m->where('(d.quota_channel = ?', 1 );
            // // $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
           
            // $select_m->where('lq.good_id = ?', $params['good_id']);
            // $select_m->where('lq.good_color = ?', $params['good_color']);
            // $select_m->where('lq.good_type = ?', $good_type);

            // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);

            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
            $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            $select_m->where('dg.group_type_id IN (?)', [4]);
            // $select_m->where('(d.quota_channel = ?', 1 );
            // $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
           
            $select_m->where('m.good_id = ?', $params['good_id']);
            $select_m->where('m.good_color = ?', $params['good_color']);
            $select_m->where('m.type = ?', $good_type);

            $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);

            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $isSum = $db->fetchOne($select_m);


            if ($params['sales_sn']) {
                $select_k = $db->select()
                    ->from(array('m' => 'market'), array('m.num'));
                $select_k->where('m.sn = ?', $params['sales_sn']);
                $select_k->where('m.d_id = ?', $params['distributor_id']);
                $select_k->where('m.cat_id = ?', $params['cat_id']);
                $select_k->where('m.good_id = ?', $params['good_id']);
                $select_k->where('m.good_color = ?', $params['good_color']);
                $select_k->where('m.warehouse_id = ?', $params['warehouse_id']);

                $market = $db->fetchOne($select_k);

                // by pass num < current num
                if($market >= $num){
                    return 0;
                }

                if($market > $isSum){ 
                   $isSum = $market - $isSum;
                }else{
                    $isSum = $isSum - $market;
                }
            }
               
        }

        if ($action_quota == "kr") { //kr

           $select_c = $db->select()
                ->from(array('d' => 'distributor'), array('d.quota_channel'));
            $select_c->where('d.id = ?', $params['distributor_id']);
            $channel = $db->fetchOne($select_c); 
             
            $select_q = $db->select()
                    ->from(array('q' => 'quota_oppo'), array('q.quantity'));
              
            $select_q->where('q.dis_type = ?',2);
            $select_q->where('q.good_id = ?', $params['good_id']);
            $select_q->where('q.good_color = ?', $params['good_color']);
            $select_q->where('q.warehouse_id = ?', $params['warehouse_id']);
            $select_q->where('q.good_type = ?', $good_type);
            $select_q->where('q.status = ?', 1);
            $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $data = $db->fetchOne($select_q);

            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
            // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
            // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            // $select_m->where('dg.group_type_id = ?', 2);
            // // $select_m->where('(d.quota_channel = ?', 1 );
            // // $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
           
            // $select_m->where('lq.good_id = ?', $params['good_id']);
            // $select_m->where('lq.good_color = ?', $params['good_color']);
            // $select_m->where('lq.good_type = ?', $good_type);

            // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);

            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
            $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            $select_m->where('dg.group_type_id = ?', 2);
            // $select_m->where('(d.quota_channel = ?', 1 );
            // $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
           
            $select_m->where('m.good_id = ?', $params['good_id']);
            $select_m->where('m.good_color = ?', $params['good_color']);
            $select_m->where('m.type = ?', $good_type);

            $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);

            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $isSum = $db->fetchOne($select_m);


            if ($params['sales_sn']) {
                $select_k = $db->select()
                    ->from(array('m' => 'market'), array('m.num'));
                $select_k->where('m.sn = ?', $params['sales_sn']);
                $select_k->where('m.d_id = ?', $params['distributor_id']);
                $select_k->where('m.cat_id = ?', $params['cat_id']);
                $select_k->where('m.good_id = ?', $params['good_id']);
                $select_k->where('m.good_color = ?', $params['good_color']);
                $select_k->where('m.warehouse_id = ?', $params['warehouse_id']);

                $market = $db->fetchOne($select_k);

                // by pass num < current num
                if($market >= $num){
                    return 0;
                }

                if($market > $isSum){ 
                   $isSum = $market - $isSum;
                }else{
                    $isSum = $isSum - $market;
                }
            }
               
        }
        
        if ($action_quota == "brandshop") { //Brand Shop
           $select_c = $db->select()
                ->from(array('d' => 'distributor'), array('d.quota_channel'));
            // $select_c->where('q.area_id = ?', $area);
            $select_c->where('d.id = ?', $params['distributor_id']);
            $channel = $db->fetchOne($select_c); 
            
            $select_q = $db->select()
                ->from(array('q' => 'quota_oppo'), array('q.quantity'));
          
            $select_q->where('q.dis_type = ?',10);
            $select_q->where('q.good_id = ?', $params['good_id']);
            $select_q->where('q.good_color = ?', $params['good_color']);
            $select_q->where('q.warehouse_id = ?', $params['warehouse_id']);
            $select_q->where('q.good_type = ?', $good_type);
            $select_q->where('q.status = ?', 1);
            $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $data = $db->fetchOne($select_q);


            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
            // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
            // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
            
           
            // $select_m->where('lq.good_id = ?', $params['good_id']);
            // $select_m->where('lq.good_color = ?', $params['good_color']);
            // $select_m->where('lq.good_type = ?', $good_type);

            // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);

            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));

            // $select_m->where('dg.group_type_id IN (?)', [10,11]);
            // // $select_m->where('d.group_id = ?', 2);
            // // $select_m->where('(d.quota_channel = ?', 10 );
            // // $select_m->Orwhere('d.rank in (?))', array(10) );
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
            $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
            
           
            $select_m->where('m.good_id = ?', $params['good_id']);
            $select_m->where('m.good_color = ?', $params['good_color']);
            $select_m->where('m.type = ?', $good_type);

            $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);

            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));

            $select_m->where('dg.group_type_id IN (?)', [10,11]);
            // $select_m->where('d.group_id = ?', 2);
            // $select_m->where('(d.quota_channel = ?', 10 );
            // $select_m->Orwhere('d.rank in (?))', array(10) );
            $isSum = $db->fetchOne($select_m);


            if ($params['sales_sn']) {
                $select_k = $db->select()
                    ->from(array('m' => 'market'), array('m.num'));
                $select_k->where('m.sn = ?', $params['sales_sn']);
                $select_k->where('m.d_id = ?', $params['distributor_id']);
                $select_k->where('m.cat_id = ?', $params['cat_id']);
                $select_k->where('m.good_id = ?', $params['good_id']);
                $select_k->where('m.good_color = ?', $params['good_color']);
                $select_k->where('m.warehouse_id = ?', $params['warehouse_id']);

                $market = $db->fetchOne($select_k);

                // by pass num < current num
                if($market >= $num){
                    return 0;
                }

                if($market > $isSum){ 
                   $isSum = $market - $isSum;
                }else{
                    $isSum = $isSum - $market;
                }
            } 
       
        } 
        
        if ($isSum) {
            if ($num+$isSum > $data) {$q='1';}else{$q='0';}
        }else{
            if ($data) {
                   if ($num > $data) {$q = '1';}else{$q = '0';}
            }else{
                $q = '1';
            }
                
        }
    }else{
        $q = '0';
    }
    } 
    }else{ //  FOR_RETAILER
        $q= '0';
    }   
    return $q;
          
}

public function checkQuotaOppoReturnData($params)
    {
        $db = Zend_Registry::get('db');
        $num = $params['num'];

        //allow quota for warehouse kerry only
        if (in_array($params['type'], [FOR_RETAILER,FOR_DEMO,FOR_APK])) {
            
        $toDay = date('Y-m-d');
        $select_q = $db->select()
            ->from(array('d' => 'quota_oppo_by_distributor'), array('d.quantity'));
       $select_q->where('d.warehouse = ?', $params['warehouse_id']);
       $select_q->where('d.d_id = ?', $params['distributor_id']);
       $select_q->where('d.cat_id = ?', $params['cat_id']);
       $select_q->where('d.good_id = ?', $params['good_id']);
       $select_q->where('d.good_color = ?', $params['good_color']);
       $select_q->where('d.status = 1');
       $select_q->where('d.del is null',1);
       $select_q->where('date_format(d.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
       $quota_by_distributor = $db->fetchRow($select_q);

       $select_q_d = $db->select()
            ->from(array('nqd' => 'new_quota_distributor'), array());
       $select_q_d->joinLeft(array('nqdd' => 'new_quota_distributor_details'),'nqdd.nqd_id=nqd.id',array('nqdd.num','nqdd.status'));
       $select_q_d->where('nqd.warehouse_id = ?', $params['warehouse_id']);
       $select_q_d->where('nqd.d_id = ?', $params['distributor_id']);
       $select_q_d->where('nqd.order_type = ?', $params['type']);
       $select_q_d->where('nqd.good_id = ?', $params['good_id']);
       $select_q_d->where('nqdd.good_color = ?', $params['good_color']);
       $select_q_d->where('nqd.status = 1');
       $select_q_d->where('nqdd.status <> 0');
       $select_q_d->where('date_format(nqd.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
       $new_quota_by_distributor = $db->fetchRow($select_q_d);
      
        if (isset($quota_by_distributor) and $quota_by_distributor and $params['type'] == FOR_RETAILER) {
            $data = $quota_by_distributor['quantity'];

            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran_distributor'), array('sum(lq.num)'));
            // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);
            // $select_m->where('lq.d_id = ?', $params['distributor_id']);
            // $select_m->where('lq.cat_id = ?', $params['cat_id']);
            // $select_m->where('lq.good_id = ?', $params['good_id']);
            // $select_m->where('lq.good_color = ?', $params['good_color']);
            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);
            $select_m->where('m.d_id = ?', $params['distributor_id']);
            $select_m->where('m.cat_id = ?', $params['cat_id']);
            $select_m->where('m.good_id = ?', $params['good_id']);
            $select_m->where('m.good_color = ?', $params['good_color']);
            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
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

        }else if (isset($new_quota_by_distributor) and $new_quota_by_distributor) {

            if($new_quota_by_distributor['status'] == 2){
                return -1;
            }

            if($new_quota_by_distributor['num'] == 0){
                return 0;
            }

            $data = $new_quota_by_distributor['num'];

            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran_distributor'), array('sum(lq.num)'));
            // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);
            // $select_m->where('lq.d_id = ?', $params['distributor_id']);
            // $select_m->where('lq.cat_id = ?', $params['cat_id']);
            // $select_m->where('lq.good_id = ?', $params['good_id']);
            // $select_m->where('lq.good_color = ?', $params['good_color']);
            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);
            $select_m->where('m.d_id = ?', $params['distributor_id']);
            $select_m->where('m.cat_id = ?', $params['cat_id']);
            $select_m->where('m.good_id = ?', $params['good_id']);
            $select_m->where('m.good_color = ?', $params['good_color']);
            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
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

            // by pass num < current num
            if($market >= $num){
                return 0;
            }

            if($market > $isSum){ 
                $isSum = $market - $isSum;
            }else{
                $isSum = $isSum - $market;
            }
           
        }

        }else{

        $good_type = $params['type'];

        $select_a = $db->select()
            ->from(array('d' => 'distributor'), array('d.rank','d.quota_channel','d.group_id'));

       $select_a->joinLeft(array('r' => HR_DB.'.regional_market'),'r.id=d.region',array());
       $select_a->joinLeft(array('a' => HR_DB.'.area'),'r.area_id=a.id',array('area_id'=>'a.id'));
       $select_a->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
       $select_a->where('d.id = ?', $params['distributor_id']);
       $distributor = $db->fetchRow($select_a);

       if(!isset($distributor['group_type_id']) || !$distributor['group_type_id']){
            return -2;
       }

       //  if ($distributor['quota_channel']) {
       //     if ($distributor['quota_channel'] == 1) {
       //        $rank = 1;
             
       //     }else if ($distributor['quota_channel'] == 10) {
       //        $rank = 10;
       //     }
       // }else{
       //      if (in_array($distributor['rank'], array(7,8))) {
       //         $rank = 7; 
       //      }else if (in_array($distributor['rank'], array(1,2,5,6))) {
       //        $rank = 1;
       //      }else{
       //        $rank = $distributor['rank'];
       //      }
       // }

       // Group brand Shop by Dealer to brand shop
       $old_group_id = '';
       if($distributor['group_type_id'] == 11){
        $old_group_id = $distributor['group_type_id'];
        $distributor['group_type_id'] = 10;
       }

        $select_w = $db->select()
            ->from(array('o' => 'quota_oppo'), array('o.id'));
        $select_w->where('o.dis_type = ?', $distributor['group_type_id']);
        $select_w->where('o.good_id = ?', $params['good_id']);
        $select_w->where('o.good_color = ?', $params['good_color']);
        $select_w->where('o.warehouse_id = ?', $params['warehouse_id']);
        $select_w->where('o.good_type = ?', $good_type);
        $select_w->where('o.quota_date = ?',$toDay);
        $select_w->where('o.status = ?',1);
        
        $quota_oppo = $db->fetchAll($select_w);
        
       if (isset($quota_oppo) and $quota_oppo) {
       
       // $dis_type = '';
       // if ($distributor['quota_channel']) {
       //     if ($distributor['quota_channel'] == 1) {
       //        $action_quota = "org";
       //        $dis_type     = "have";
       //     }else if ($distributor['quota_channel'] == 10) {
       //        $action_quota = "brandshop";
       //        $dis_type     = "have";
       //     }
       // }else{
       //      if (in_array($distributor['rank'], array(7,8))) {
       //         $action_quota = "dealer"; 
       //      }else if (in_array($distributor['rank'], array(1,2,5,6))) {
       //        $action_quota = "org";
       //      }else if ($distributor['rank'] == 10) {
       //        $action_quota = "brandshop";
       //      }
       // }

        switch ($distributor['group_type_id']) {
            case '3':
                $action_quota = "ka";
                break;
            case '2':
                $action_quota = "kr";
                break;
            case '1':
                $action_quota = "dealer";
               break;
            case '10':
                $action_quota = "brandshop";
               break;
            case '4':
                $action_quota = "operator";
               break;
           default:
               # code...
               break;
        }

        if ($action_quota == "dealer") { //dealer ,hub
            
            $select_q = $db->select()
                ->from(array('q' => 'quota_oppo'), array('q.quantity','q.not_allow_area'));
            $select_q->where('q.area_id = ?', $distributor['area_id']);
            $select_q->where('q.status = ?',1);
            $select_q->where('q.dis_type = ?', 1);
            $select_q->where('q.good_id = ?', $params['good_id']);
            $select_q->where('q.good_color = ?', $params['good_color']);
            $select_q->where('q.warehouse_id = ?', $params['warehouse_id']);
            $select_q->where('q.good_type = ?', $good_type);
            $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
            // echo $select_q;die;
            $data = $db->fetchRow($select_q);

            if ($data['quantity'] || $data['not_allow_area']) {

                if($data['not_allow_area']){
                    return -1;
                }else{

                    // start : old
                    // $select_m = $db->select()
                    //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
                    // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
                    // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
                    // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
                    // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
                    // $select_m->where('dg.group_type_id = ?', 1);
                    // $select_m->where('a.id = ?', $distributor['area_id'] );
                    // $select_m->where('lq.good_id = ?', $params['good_id']);
                    // $select_m->where('lq.good_color = ?', $params['good_color']);
                    // $select_m->where('lq.good_type = ?', $good_type);

                    // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);

                    // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
                    // end : old


                    $select_m = $db->select()
                        ->from(array('m' => 'market'), array('sum(m.num)'));
                    $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
                    $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
                    $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
                    $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
                    $select_m->where('dg.group_type_id = ?', 1);
                    $select_m->where('a.id = ?', $distributor['area_id'] );
                    $select_m->where('m.good_id = ?', $params['good_id']);
                    $select_m->where('m.good_color = ?', $params['good_color']);
                    $select_m->where('m.type = ?', $good_type);

                    $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);

                    $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));

                    // echo $select_m;
                    $isSum = $db->fetchOne($select_m);


                    if ($params['sales_sn']) {
                        $select_k = $db->select()
                            ->from(array('m' => 'market'), array('m.num'));
                        $select_k->where('m.sn = ?', $params['sales_sn']);
                        $select_k->where('m.d_id = ?', $params['distributor_id']);
                        $select_k->where('m.cat_id = ?', $params['cat_id']);
                        $select_k->where('m.good_id = ?', $params['good_id']);
                        $select_k->where('m.good_color = ?', $params['good_color']);
                        $select_k->where('m.warehouse_id = ?', $params['warehouse_id']);


                        $market = $db->fetchOne($select_k);

                        // by pass num < current num
                        if($market >= $num){
                            return 0;
                        }

                        if($market > $isSum){ 
                            $isSum = $market - $isSum;
                        }else{
                            $isSum = $isSum - $market;
                       }
                    }
                }

                $data = $data['quantity'];
        
            }else{

                unset($data);
                $select_qt = $db->select()
                ->from(array('q' => 'quota_oppo'), array('q.quantity'));
                $select_qt->where('q.channel = ?', 99);
                $select_qt->where('q.dis_type = ?', 1);
                $select_qt->where('q.status = ?',1);
                $select_qt->where('q.good_id = ?', $params['good_id']);
                $select_qt->where('q.good_color = ?', $params['good_color']);
                $select_qt->where('q.warehouse_id = ?', $params['warehouse_id']);
                $select_qt->where('q.good_type = ?', $good_type);
                $select_qt->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
                $data = $db->fetchOne($select_qt);

                $select_q = $db->select()
                ->from(array('q' => 'quota_oppo'), array('q.area_id'));
                $select_q->where('q.dis_type = ?', 1);
                $select_q->where('q.status = ?',1);
                $select_q->where('q.quantity = ?', 0);
                $select_q->where('q.good_id = ?', $params['good_id']);
                $select_q->where('q.good_color = ?', $params['good_color']);
                $select_q->where('q.warehouse_id = ?', $params['warehouse_id']);
                $select_q->where('q.good_type = ?', $good_type);
                $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
                $inArea = $db->fetchAll($select_q);
                if ($inArea) {

                    // start : old
                    // $select_m = $db->select()
                    // ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
                    // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
                    // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
                    // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
                    // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
                    // $select_m->where('dg.group_type_id = ?', 1);
                    // $select_m->where('a.id in (?)', $inArea );
                    // $select_m->where('lq.good_id = ?', $params['good_id']);
                    // $select_m->where('lq.good_color = ?', $params['good_color']);
                    // $select_m->where('lq.good_type = ?', $good_type);
                    // $select_m->where('d.quota_channel is null',1);

                    // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);

                    // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
                    // // echo $select_m;
                    // $isSum = $db->fetchOne($select_m);
                    // end : old


                    $select_m = $db->select()
                    ->from(array('m' => 'market'), array('sum(m.num)'));
                    $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
                    $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
                    $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
                    $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
                    $select_m->where('dg.group_type_id = ?', 1);
                    $select_m->where('a.id in (?)', $inArea );
                    $select_m->where('m.good_id = ?', $params['good_id']);
                    $select_m->where('m.good_color = ?', $params['good_color']);
                    $select_m->where('m.type = ?', $good_type);
                    $select_m->where('d.quota_channel is null',1);

                    $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);

                    $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
                    // echo $select_m;
                    $isSum = $db->fetchOne($select_m);


                }

                if ($params['sales_sn']) {
                    $select_k = $db->select()
                        ->from(array('m' => 'market'), array('m.num'));
                    $select_k->where('m.sn = ?', $params['sales_sn']);
                    $select_k->where('m.d_id = ?', $params['distributor_id']);
                    $select_k->where('m.cat_id = ?', $params['cat_id']);
                    $select_k->where('m.good_id = ?', $params['good_id']);
                    $select_k->where('m.good_color = ?', $params['good_color']);
                    $select_k->where('m.warehouse_id = ?', $params['warehouse_id']);

                    $market = $db->fetchOne($select_k);
                    // echo '--'.$market.'---';
                    // echo '--'.$isSum.'---';

                    // by pass num < current num
                    if($market >= $num){
                        return 0;
                    }

                    if($market > $isSum){ 
                        $isSum = $market - $isSum;
                    }else{
                        $isSum = $isSum - $market;
                    }
                    // echo '+'.$isSum.'+';
                }
                
            }

        }

        if ($action_quota == "ka") { //ka

           $select_c = $db->select()
                ->from(array('d' => 'distributor'), array('d.quota_channel'));
            $select_c->where('d.id = ?', $params['distributor_id']);
            $channel = $db->fetchOne($select_c); 
             
            $select_q = $db->select()
                    ->from(array('q' => 'quota_oppo'), array('q.quantity'));
              
            $select_q->where('q.dis_type = ?',3);
            $select_q->where('q.good_id = ?', $params['good_id']);
            $select_q->where('q.good_color = ?', $params['good_color']);
            $select_q->where('q.warehouse_id = ?', $params['warehouse_id']);
            $select_q->where('q.good_type = ?', $good_type);
            $select_q->where('q.status = ?', 1);
            $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $data = $db->fetchOne($select_q);

            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
            // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
            // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            // $select_m->where('dg.group_type_id IN (?)', [3]);
            // // $select_m->where('(d.quota_channel = ?', 1 );
            // // $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
           
            // $select_m->where('lq.good_id = ?', $params['good_id']);
            // $select_m->where('lq.good_color = ?', $params['good_color']);
            // $select_m->where('lq.good_type = ?', $good_type);

            // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);

            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
            $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            $select_m->where('dg.group_type_id IN (?)', [3]);
            // $select_m->where('(d.quota_channel = ?', 1 );
            // $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
           
            $select_m->where('m.good_id = ?', $params['good_id']);
            $select_m->where('m.good_color = ?', $params['good_color']);
            $select_m->where('m.type = ?', $good_type);

            $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);

            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $isSum = $db->fetchOne($select_m);


            if ($params['sales_sn']) {
                $select_k = $db->select()
                    ->from(array('m' => 'market'), array('m.num'));
                $select_k->where('m.sn = ?', $params['sales_sn']);
                $select_k->where('m.d_id = ?', $params['distributor_id']);
                $select_k->where('m.cat_id = ?', $params['cat_id']);
                $select_k->where('m.good_id = ?', $params['good_id']);
                $select_k->where('m.good_color = ?', $params['good_color']);
                $select_k->where('m.warehouse_id = ?', $params['warehouse_id']);

                $market = $db->fetchOne($select_k);

                // by pass num < current num
                if($market >= $num){
                    return 0;
                }
                
                if($market > $isSum){ 
                   $isSum = $market - $isSum;
                }else{
                    $isSum = $isSum - $market;
                }
            }
               
        }

        if ($action_quota == "operator") { //operator

           $select_c = $db->select()
                ->from(array('d' => 'distributor'), array('d.quota_channel'));
            $select_c->where('d.id = ?', $params['distributor_id']);
            $channel = $db->fetchOne($select_c); 
             
            $select_q = $db->select()
                    ->from(array('q' => 'quota_oppo'), array('q.quantity'));
              
            $select_q->where('q.dis_type = ?',4);
            $select_q->where('q.good_id = ?', $params['good_id']);
            $select_q->where('q.good_color = ?', $params['good_color']);
            $select_q->where('q.warehouse_id = ?', $params['warehouse_id']);
            $select_q->where('q.good_type = ?', $good_type);
            $select_q->where('q.status = ?', 1);
            $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $data = $db->fetchOne($select_q);

            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
            // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
            // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            // $select_m->where('dg.group_type_id IN (?)', [4]);
            // // $select_m->where('(d.quota_channel = ?', 1 );
            // // $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
           
            // $select_m->where('lq.good_id = ?', $params['good_id']);
            // $select_m->where('lq.good_color = ?', $params['good_color']);
            // $select_m->where('lq.good_type = ?', $good_type);

            // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);

            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
            $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            $select_m->where('dg.group_type_id IN (?)', [4]);
            // $select_m->where('(d.quota_channel = ?', 1 );
            // $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
           
            $select_m->where('m.good_id = ?', $params['good_id']);
            $select_m->where('m.good_color = ?', $params['good_color']);
            $select_m->where('m.type = ?', $good_type);

            $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);

            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $isSum = $db->fetchOne($select_m);


            if ($params['sales_sn']) {
                $select_k = $db->select()
                    ->from(array('m' => 'market'), array('m.num'));
                $select_k->where('m.sn = ?', $params['sales_sn']);
                $select_k->where('m.d_id = ?', $params['distributor_id']);
                $select_k->where('m.cat_id = ?', $params['cat_id']);
                $select_k->where('m.good_id = ?', $params['good_id']);
                $select_k->where('m.good_color = ?', $params['good_color']);
                $select_k->where('m.warehouse_id = ?', $params['warehouse_id']);

                $market = $db->fetchOne($select_k);

                // by pass num < current num
                if($market >= $num){
                    return 0;
                }

                if($market > $isSum){ 
                   $isSum = $market - $isSum;
                }else{
                    $isSum = $isSum - $market;
                }
            }
               
        }

        if ($action_quota == "kr") { //kr

           $select_c = $db->select()
                ->from(array('d' => 'distributor'), array('d.quota_channel'));
            $select_c->where('d.id = ?', $params['distributor_id']);
            $channel = $db->fetchOne($select_c); 
             
            $select_q = $db->select()
                    ->from(array('q' => 'quota_oppo'), array('q.quantity'));
              
            $select_q->where('q.dis_type = ?',2);
            $select_q->where('q.good_id = ?', $params['good_id']);
            $select_q->where('q.good_color = ?', $params['good_color']);
            $select_q->where('q.warehouse_id = ?', $params['warehouse_id']);
            $select_q->where('q.good_type = ?', $good_type);
            $select_q->where('q.status = ?', 1);
            $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $data = $db->fetchOne($select_q);

            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
            // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
            // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            // $select_m->where('dg.group_type_id = ?', 2);
            // // $select_m->where('(d.quota_channel = ?', 1 );
            // // $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
           
            // $select_m->where('lq.good_id = ?', $params['good_id']);
            // $select_m->where('lq.good_color = ?', $params['good_color']);
            // $select_m->where('lq.good_type = ?', $good_type);

            // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);

            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
            $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            $select_m->where('dg.group_type_id = ?', 2);
            // $select_m->where('(d.quota_channel = ?', 1 );
            // $select_m->Orwhere('d.rank in (?))', array(1,2,5,6) );
           
            $select_m->where('m.good_id = ?', $params['good_id']);
            $select_m->where('m.good_color = ?', $params['good_color']);
            $select_m->where('m.type = ?', $good_type);

            $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);

            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $isSum = $db->fetchOne($select_m);


            if ($params['sales_sn']) {
                $select_k = $db->select()
                    ->from(array('m' => 'market'), array('m.num'));
                $select_k->where('m.sn = ?', $params['sales_sn']);
                $select_k->where('m.d_id = ?', $params['distributor_id']);
                $select_k->where('m.cat_id = ?', $params['cat_id']);
                $select_k->where('m.good_id = ?', $params['good_id']);
                $select_k->where('m.good_color = ?', $params['good_color']);
                $select_k->where('m.warehouse_id = ?', $params['warehouse_id']);

                $market = $db->fetchOne($select_k);

                // by pass num < current num
                if($market >= $num){
                    return 0;
                }

                if($market > $isSum){ 
                   $isSum = $market - $isSum;
                }else{
                    $isSum = $isSum - $market;
                }
            }
               
        }
        
        if ($action_quota == "brandshop") { //Brand Shop
           $select_c = $db->select()
                ->from(array('d' => 'distributor'), array('d.quota_channel'));
            // $select_c->where('q.area_id = ?', $area);
            $select_c->where('d.id = ?', $params['distributor_id']);
            $channel = $db->fetchOne($select_c); 
            
            $select_q = $db->select()
                ->from(array('q' => 'quota_oppo'), array('q.quantity'));
          
            $select_q->where('q.dis_type = ?',10);
            $select_q->where('q.good_id = ?', $params['good_id']);
            $select_q->where('q.good_color = ?', $params['good_color']);
            $select_q->where('q.warehouse_id = ?', $params['warehouse_id']);
            $select_q->where('q.good_type = ?', $good_type);
            $select_q->where('q.status = ?', 1);
            $select_q->where('date_format(q.quota_date,"%Y-%m-%d") = ?',date('Y-m-d'));
           
            $data = $db->fetchOne($select_q);


            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
            // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
            // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
            
           
            // $select_m->where('lq.good_id = ?', $params['good_id']);
            // $select_m->where('lq.good_color = ?', $params['good_color']);
            // $select_m->where('lq.good_type = ?', $good_type);

            // $select_m->where('lq.warehouse_id = ?', $params['warehouse_id']);

            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));

            // $select_m->where('dg.group_type_id IN (?)', [10,11]);
            // // $select_m->where('d.group_id = ?', 2);
            // // $select_m->where('(d.quota_channel = ?', 10 );
            // // $select_m->Orwhere('d.rank in (?))', array(10) );
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
            $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
            
           
            $select_m->where('m.good_id = ?', $params['good_id']);
            $select_m->where('m.good_color = ?', $params['good_color']);
            $select_m->where('m.type = ?', $good_type);

            $select_m->where('m.warehouse_id = ?', $params['warehouse_id']);

            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',date('Y-m-d'));

            $select_m->where('dg.group_type_id IN (?)', [10,11]);
            // $select_m->where('d.group_id = ?', 2);
            // $select_m->where('(d.quota_channel = ?', 10 );
            // $select_m->Orwhere('d.rank in (?))', array(10) );
            $isSum = $db->fetchOne($select_m);


            if ($params['sales_sn']) {
                $select_k = $db->select()
                    ->from(array('m' => 'market'), array('m.num'));
                $select_k->where('m.sn = ?', $params['sales_sn']);
                $select_k->where('m.d_id = ?', $params['distributor_id']);
                $select_k->where('m.cat_id = ?', $params['cat_id']);
                $select_k->where('m.good_id = ?', $params['good_id']);
                $select_k->where('m.good_color = ?', $params['good_color']);
                $select_k->where('m.warehouse_id = ?', $params['warehouse_id']);

                $market = $db->fetchOne($select_k);

                // by pass num < current num
                if($market >= $num){
                    return 0;
                }

                if($market > $isSum){ 
                   $isSum = $market - $isSum;
                }else{
                    $isSum = $isSum - $market;
                }
            } 
       
        }

        $count_quota = $data-$isSum;
 
        if ($isSum) {
            return $count_quota;
            // if ($num+$isSum > $data) {$q='1';}else{$q='0';}
        }else{
            if ($data) {
                return $count_quota;
                   // if ($num > $data) {$q = '1';}else{$q = '0';}
            }else{
                $q = -1;
            }
                
        }
    }else{
        $q = 0;
    }
    } 
    }else{ //  FOR_RETAILER
        $q = 0;
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
    
    $select = "SELECT m.sn,m.sn_ref,d.title,round(sum(m.total)-if(m.total_spc_discount,m.total_spc_discount*1,0),2) as total ,m.finance_confirm_date ,d.credit_type,c.credit 
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

   public function getDetailMarketAndDistributor($sn, $order_type){

        $db = Zend_Registry::get('db');

        
        $select = $db->select()
        ->from(array('p'=> $this->_name),array('p.*'))
        ->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array('d.rank'));

        $select->joinLeft(array('ds' => 'delivery_sales'), 'ds.sales_sn=p.sn', array('ds.delivery_order_id'));
        $select->joinLeft(array('do' => 'delivery_order'), 'do.id=ds.delivery_order_id', array('tracking_no'));
        $select->joinLeft(array('sp' => 'shipping_address'), 'sp.id=p.shipping_address', array('remark'));
        $select->joinleft(array('str' => HR_DB.'.store'), 'str.id = p.store_id', array('store_name' =>'str.name', 'store_code' => 'str.store_code', 'store_address' => 'str.shipping_address', 'store_owner' => 'str.leader', 'store_tel' => 'str.phone_number'));

        if ($order_type == 5){
            $select->joinLeft(array('gdc' => 'good_discount'), 'p.good_id = gdc.good_id AND gdc.name = "DEMO"', array('good_discount' => 'gdc.discount'));
            }

        if ($order_type == 3){
                $select->joinLeft(array('gdc' => 'good_discount'), 'p.good_id = gdc.good_id AND gdc.name = "Staff"', array('good_discount' => 'gdc.discount'));
            }
        
        $select->where('p.sn = ?', $sn);
        // echo $select;die;
        return $db->fetchAll($select);
    }

    public function getDetailDeliveryBySo($sn_ref, $company){

        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> $this->_name), array('do.tracking_no','p.sn','p.sn_ref','sad.contact_name','sad.address','sad.phone','sam.amphure_name','sdi.district_name', 'spr.provice_name', 'szi.zipcode'))
        ->joinLeft(array('ds' => 'delivery_sales'), 'ds.sales_sn = p.sn', array())
        ->joinLeft(array('do' => 'delivery_order'), 'do.id = ds.delivery_order_id', array())
        ->joinLeft(array('sad' => 'shipping_address'), 'sad.id = p.shipping_address', array())
        ->joinLeft(array('sam' => 'shipping_amphures'), 'sam.amphure_id = sad.amphures_id', array())
        ->joinLeft(array('sdi' => 'shipping_districts'), 'sdi.district_code = sad.districts_id', array())
        ->joinLeft(array('spr' => 'shipping_provinces'), 'spr.provice_id = sad.province_id', array())
        ->joinLeft(array('szi' => 'shipping_zipcodes'), 'szi.zip_id = sad.zipcodes', array());
        $select->where('ds.company = ?', $company);
        $select->where('p.sn_ref = ?', $sn_ref);
        $select->group('p.sn');
        //echo $select;die;
        return $db->fetchRow($select);
    }

    public function getSnByArraySoForPaymentGroup($array_so){

        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> $this->_name),array('p.sn','p.sn_ref','p.d_id'));
        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array());
        $select->where('p.sn_ref in(?)', $array_so);
        $select->where('p.isbacks <> 1 and p.canceled <> 1 and p.sales_confirm_id is null and p.finance_confirm_id is null and CASE WHEN d.rank <> 10 THEN p.outmysql_user is null ELSE 1=1 END');
        $select->group('p.sn');

        // echo $select;die;
        return $db->fetchAll($select);
    }

    public function getImgPayment($sn){

        $db = Zend_Registry::get('db');
        $select = $db->select()
        // ->from(array('p'=> $this->_name),array('p.sn','p.sn_ref','p.d_id'))
        ->from(array('p'=> $this->_name),array('p.sn','p.sn_ref','p.d_id','p.pay_group', 'p.payment_no'))
        ->joinLeft(array('ch' => 'checkmoney'), 'ch.sn = p.sn and ch.type = 1', array('img_pay_one' => 'ch.file_pay_slip'))
        ->joinleft(array('ep'=>'ep_privileges_tran_order'),'ep.sales_order_sn=ch.sn',array('ep_id'=>'ep.ids','ep_payslip'=>'ep.payment_slip_image','ep_staff_id' => 'ep.staff_code'))
        ->joinLeft(array('pg' => 'pay_group'), 'pg.sale_order = p.sn and pg.status = 1', array())
        ->joinLeft(array('pgb' => 'pay_group_bank'), 'CAST(pgb.payment_id AS CHAR) = CAST(pg.payment_id AS CHAR) and pgb.status = 1', array('img_pay_group' => 'pgb.file_pay_slip'));

        if(is_array($sn)){
            $select->where('p.sn in (?)', $sn);
        }else{
            $select->where('p.sn = ?', $sn);
        }
        $select->where('p.isbacks <> 1 and p.canceled <> 1');
        $select->group(['p.sn','ch.id','pgb.id']);

        $sql = $db->select()->from(array('main' => new Zend_Db_Expr('(' . $select . ')')
            ), array('*'));

        $sql->group(['img_pay_one','img_pay_group']);

        // echo $select;die;
        // echo $sql;die;
        $result = $db->fetchAll($sql);
        // $result = $db->fetchAll($select);

        // $sql = "SELECT *
        //         FROM (SELECT `p`.`sn`, `p`.`sn_ref`, `p`.`d_id`, `p`.`pay_group`, `p`.`payment_no`, `ch`.`file_pay_slip` AS `img_pay_one`, `pgb`.`file_pay_slip` AS `img_pay_group` FROM `market` AS `p`
        //          LEFT JOIN `checkmoney` AS `ch` ON ch.sn = p.sn and ch.type = 1
        //          LEFT JOIN `pay_group` AS `pg` ON pg.sale_order = p.sn and pg.status = 1
        //          LEFT JOIN `pay_group_bank` AS `pgb` ON 'CAST(pgb.payment_id AS CHAR) = CAST(pg.payment_id AS CHAR) and pgb.status = 1 WHERE (p.sn in (?)) AND (p.isbacks <> 1 and p.canceled <> 1) GROUP BY `p`.`sn`,`ch`.`id`,`pgb`.`id`) AS a
        //         GROUP BY img_pay_one,img_pay_group;";

        // $result = $db->fetchAll($sql,implode(",",$sn));

        // print_r($result);die;

        return $result;
    }

    public function getUsePaymentGroup($payment_no){

        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> $this->_name),array('p.sn','p.sn_ref','p.d_id','p.pay_group', 'p.payment_no'))
        ->joinLeft(array('pg' => 'pay_group'), 'pg.sale_order = p.sn and pg.status = 1', array('payment_id'))
        ->joinLeft(array('pgt' => 'pay_group_tran'), 'pgt.payment_tran_id = pg.payment_id and pgt.status = 1', array('usePaygroup_paymentID' => 'pgt.payment_id', 'payment_tran_id','use_total'));

        $select->where('p.payment_no = ?', $payment_no);
        $select->where('p.isbacks <> 1 and p.canceled <> 1');
        $select->group(['p.sn','pgt.payment_id']);

        // echo $select;die;
        return $db->fetchAll($select);
    }

    function fetchPaginationPayGroup($page, $limit, &$total, $params){
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');

        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $select = $db->select();

        // Add permission cash
        $QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();
        $getwarehouseByuserID = $QWarehouseGroupUser->currentWarehouseGroupUserList($userStorage->id);

        $whrehouse_name = array();
        foreach ($getwarehouseByuserID as $item){
            $whrehouse_name[$item['warehouse_id']] =  $item['warehouse_id'];
        }

        if(isset($params['order_list']) and $params['order_list']){

        if (My_Staff_Group::inGroup($userStorage->group_id, array(SALES_VIENTIAN,SALES_DEALER,RD_DEALER))) {
                $select->where('p.salesman IN (?)',$userStorage->id);
            }
        }
        if($whrehouse_name){
            $select->where("p.warehouse_id IN (".implode(",",$whrehouse_name).")", null);
        }
        // End

        $select->from(array('p' => $this->_name),
                array('*'));
        $select->joinLeft(array('d' => 'distributor'),'d.id=p.d_id',array('d.title'));
        $select->joinLeft(array('pg' => 'pay_group'),'pg.payment_no=p.payment_no and pg.status=1',array('pg.money'));

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

        if(isset($params['distributor_name']) AND $params['distributor_name']){
            $select->where('d.title like ?','%'.$params['distributor_name'].'%');
        }

        if(isset($params['d_id']) AND $params['d_id']){
            $select->where('d.id = ?',$params['d_id']);
        }

        if(isset($params['cancel']) AND $params['cancel']){
            $select->where('p.canceled = ?',$params['cancel']);
        }

        if( isset($params['payment_no']) && $params['payment_no'] != '' ){
            $select->where('p.payment_no = ?',$params['payment_no']);
        }

        if (isset($params['isbacks']) and $params['isbacks'])
            $select->where('p.isbacks = ?', 1);
        else
            $select->where('p.isbacks = ?', 0);

        if (isset($params['status']) and $params['status'])
            $select->where('p.status = ?', $params['status']);

        //finance
        if (isset($params['finance']) and $params['finance']) {
            $select->where('p.pay_time IS NULL OR p.pay_time = \'\' OR p.pay_time = 0 OR p.shipping_yes_time IS NULL OR p.shipping_yes_time = \'\' OR p.shipping_yes_time = 0');
            $select->where('p.isbacks = ?', 0);
           // $select->where('p.warehouse_id <> 90', null);
        }

        if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
            $select->where('p.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or p.warehouse_id in (71,92)',null);
            // allow warehouse WMDGC - คลังเคลมDigital, Brandshop Warehouse at Kerry
        }

        if (isset($params['sn'])) {
            if (is_array($params['sn']) && count($params['sn']))
                $select->where('p.sn_ref IN (?) or p.sn IN (?)', $params['sn']);
            elseif (!is_array($params['sn']) && $params['sn'])
                $select->where('p.sn_ref LIKE ? or p.sn LIKE ?', '%'.$params['sn'].'%');
        }

        $select->where('p.sales_confirm_date IS NOT NULL AND p.sales_confirm_id is not null',1);
        $select->where('p.canceled IS NULL OR p.canceled !=1');

        $select_group = $db->select()
            ->from(array('u' => 'distributor_group_user'),array('u.group_id'))
            ->where('u.user_id=?',$userStorage->id);
        $result_group = $db->fetchAll($select_group);
        $group_id = "";
        if ($result_group){
            foreach ($result_group as $to) {
                $group_id .= $to['group_id'].',';
            }

            $select->where('d.group_id in('.rtrim($group_id, ',').')',null);
        }

        $select->where('p.payment_no is not null',1);
        $select->group(['p.payment_no','pg.id']);

        // echo $select;die;

        // $select->limitPage($page, $limit);

        $sql = $db->select()->from(array('main' => new Zend_Db_Expr('(' . $select . ')')
            ), array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS main.id'), '*', 'total_price' => 'sum(main.money)'));

        $sql->group('payment_no');
        // $sql->order('sales_confirm_date asc');

        if (isset($params['sort']) and $params['sort']) {
            $order_str = $collate = '';

            if (in_array($params['sort'], array('staff_username', 'd_id')))
                $collate .= ' COLLATE utf8_unicode_ci ';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            // if ($params['sort'] == 'staff_username') {
            //     $select->join(array('s' => 'staff'), 's.id = p.user_id', array('s.username'));
            //     $params['sort'] = 's.username';
            // } elseif ($params['sort'] == 'd_id') {
            //     $params['sort'] = 'd.title';
            // } elseif ($params['sort'] == 'total_qty') {
            //     $params['sort'] = 'SUM(p.num)';
            // } elseif ($params['sort'] == 'total_price') {
            //     $params['sort'] = 'SUM(p.total)';
            // }

            $order_str .= $params['sort'] . $collate . $desc;
            

            $sql->order(new Zend_Db_Expr($order_str));
        }

        if($limit)
            $sql->limitPage($page, $limit);

        // echo $sql;die;

        $result = $db->fetchAll($sql);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function getSnByPaymentNo($payment_no){

        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> $this->_name),array('p.sn'));
        $select->where('p.payment_no = ?', $payment_no);
        $select->group('p.sn');
        //echo $select;die;

        $data_return = [];

        foreach ($db->fetchAll($select) as $key) {
            array_push($data_return, $key['sn']);
        }

        return $data_return;
    }

    public function getMarketOnly($params){

        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> $this->_name),array('p.payment_no','user_id', 'sn_ref' => 'sn_ref',  'total_qty' => 'SUM(p.num)', 'total_price_amount_invat' => 'SUM( ROUND( ( (p.total/p.num) ),2) * p.num )', 'total_price_amount' => 'SUM( ROUND( ( (p.total/p.num)/1 ),2) * p.num )','delivery_fee'
                , 'total_price' => 'SUM( ROUND( ( (p.total/p.num)/1 ),2) * p.num ) - IFNULL((CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END),0) + IFNULL(p.delivery_fee,0) AS total_price'
                ,'invoice_number', 'type', 'print_picking_list', 'service','confirm_so','sale_off_percent','p.sales_confirm_date','shipping_address','customer_name' 
                ,'(CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END) AS total_discount','(CASE p.use_dp WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM deposit_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END) AS total_deposit','p.canceled_by','total_spc_discount','*'));

        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array('d.name', 'd.title','d.rank', 'd.mst_sn', 'd.unames', 'd.store_code', 'd.district','d.add_tax','d.region','p.confirm_access_status','p.confirm_access_remark','p.confirm_access_by','p.order_accessories','d.finance_group','d.quota_channel','p.store_id'));

        // show sales catty
        $select->joinLeft(array('s' => HR_DB.'.staff'), 's.id=p.sales_catty_id', array('s.id as sales_catty_id','s.code as sales_catty_code', "TRIM(CONCAT(s.firstname,' ',s.lastname, '[',s.email,']'))AS sales_catty_name",new Zend_Db_Expr("(SELECT t.`name` 
              FROM tag_object tg
              LEFT JOIN tag t ON tg.`tag_id`=t.id
              WHERE tg.`object_id`=p.`sn` LIMIT 1)AS tax_po,d.finance_group")));

        $select->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn', array('ck.payment_order','create_at'=>'p.pay_time'));

        if (isset($params['isbacks']) and $params['isbacks'])
            $select->where('p.isbacks = ?', 1);
        else
            $select->where('p.isbacks = ?', 0);

        if (isset($params['status']) and $params['status'])
                    $select->where('p.status = ?', $params['status']);
        
        if (isset($params['sn']) and $params['sn'])
                    $select->where('p.sn = ?', $params['sn']);

        if (isset($params['inv']) and $params['inv'])
                    $select->where('p.invoice_number = ?', $params['inv']);
        
        // echo $select;die;

        $result = $db->fetchAll($select);
        return $result;
    }

     ##--------------Dashboard storge,available,bill,financ,scanned out----------------#

   public function marketBilled($params,$flag){
        
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    if(isset($flag) && ($flag == 'count_good' || $flag == 'color')){
        $select = $db->select()
        ->from(array('p' => $this->_name),
               array('SUM(p.num)AS count_good'));
    }else{
        $select = $db->select()
        ->from(array('p' => $this->_name),
               array('COUNT(distinct p.sn)AS count_so'));
    }
        $select->where('p.add_time >=?',date('Y-m-d 00:00:00'));
        $select->where('p.add_time <=?',date('Y-m-d 23:59:59'));
        // $select->where('p.add_time >=?','2018-05-08 00:00:00');
        // $select->where('p.add_time <=?','2018-05-08 23:59:59');
    
        $select->where('p.canceled <> 1');
        $select->where('p.isbacks <> 1');

        if(isset($flag) && $flag == 'bill_unsucc') {
           $select->where('p.sales_confirm_date is not null',1);
           $select->where('p.sales_confirm_id is not null',1);
        }
        
        if(isset($flag) && $flag == 'financed' ){
            $select->where('p.finance_confirm_date is not null',1);
            $select->where('p.finance_confirm_id is not null',1);
            

        }
        if(isset($flag) && $flag == 'scaned'){
            $select->where('p.finance_confirm_date is not null',1);
            $select->where('p.finance_confirm_id is not null',1);

            $select->where('p.outmysql_time is not null',1);
            // $select->where('p.outmysql_time >=?','2018-05-08 00:00:00');
            // $select->where('p.outmysql_time <=?','2018-05-08 23:59:59');
            $select->where('p.outmysql_user is not null',1);
           
        }
        //filter
        if(isset($params['warehouse_id']) && $params['warehouse_id'])
            $select->where('p.warehouse_id =?', $params['warehouse_id']);
        
         if(isset($params['order_type']) && $params['order_type'])
            $select->where('p.type = ?', $params['order_type']);

        if(isset($params['good_id']) && $params['good_id']){
            $select->where('p.good_id =?', $params['good_id']);

            $select->joinLeft(array('pp'=>'good_color'), 'pp.id =  p.good_color',array('pp.name'));

            if(isset($params['good_color_id']) && $params['good_color_id']){
                $select->where('p.good_color = ?', $params['good_color_id']);


            }
            else{
                
                if($flag == 'color'){
                    $select->group('p.good_color');
                }
            }

            if($flag == 'color'){
                     return $db->fetchAll($select);
                }

        }
       
        // echo ($params); die;
        return $db->fetchRow($select);
        }

    public function getDataResidue(){

        $day=date('Y-m-d');

        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p'=> $this->_name),array('p.add_time','p.pay_time','p.status','p.payment_no','user_id', 'sn_ref' => 'sn_ref',  'total_qty' => 'SUM(p.num)', 'total_price_amount_invat' => 'SUM( ROUND( ( (p.total/p.num) ),2) * p.num )', 'total_price_amount' => 'SUM( ROUND( ( (p.total/p.num)/1 ),2) * p.num )','delivery_fee'
                , 'total_price' => 'SUM( ROUND( ( (p.total/p.num)/1 ),2) * p.num ) - IFNULL((CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END),0) + IFNULL(p.delivery_fee,0) AS total_price'
                ,'invoice_number', 'type', 'print_picking_list', 'service','confirm_so','sale_off_percent','p.sales_confirm_date','shipping_address','customer_name' 
                ,'(CASE p.use_cn WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM credit_note_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END) AS total_discount','(CASE p.use_dp WHEN 1 THEN (SELECT SUM(cn.use_discount) FROM deposit_tran cn WHERE cn.sales_order = p.sn)ELSE 0 END) AS total_deposit','p.canceled_by','total_spc_discount','p.bs_campaign'));

        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array('d.name', 'd.title', 'd.store_code', 'd.district'));

        $select->joinLeft(array('rm2' => 'hr.regional_market'), 'rm2.id = d.region', array());

        $select->joinLeft(array('a2' => 'hr.area'), 'a2.id = rm2.area_id', array('area_id' => 'a2.id','area_name' => 'a2.name'));

        $select->joinLeft(array('w' => 'warehouse'), 'w.id = p.warehouse_id', array('warehouse_name' => 'w.name'));

        $select->joinLeft(array('s' => HR_DB.'.staff'), 's.id=p.sales_catty_id', array('s.id as sales_catty_id','s.code as sales_catty_code', "TRIM(CONCAT(s.firstname,' ',s.lastname, '[',s.email,']'))AS sales_catty_name",new Zend_Db_Expr("(SELECT t.`name` 
  FROM tag_object tg
  LEFT JOIN tag t ON tg.`tag_id`=t.id
  WHERE tg.`object_id`=p.`sn` LIMIT 1)AS tax_po,d.finance_group")));

        $select->joinLeft(array('rm' => 'hr.regional_market'), 's.regional_market = rm.id', array('a.name as sales_area','(SELECT 
    ROUND(SUM(ROUND((IF(mm.sale_off_percent >0,ROUND((((mm.price*mm.sale_off_percent/100)*100)/100),2),mm.price)/1),2)*mm.num*1),2)
  FROM
    market mm 
  WHERE mm.sn = p.`sn`  GROUP BY mm.sn) AS `grand_total`'
,'(SELECT 
    ROUND((if(mm.sale_off_percent >0,ROUND((((mm.price*mm.sale_off_percent/100)*100)/100),2),mm.price)/1),2)*mm.num
  FROM
    market mm 
  WHERE mm.sn = p.`sn` and p.`good_id`=mm.good_id and p.`good_color`=mm.good_color group by mm.sn) AS `total_amount_ex_vat`'
  ,'  (SELECT 
    ROUND(ROUND(ROUND((IF(mm.sale_off_percent >0,ROUND((((mm.price*mm.sale_off_percent/100)*100)/100),2),mm.price)/1),2)*mm.num,2)*1,2)
  FROM
    market mm 
  WHERE mm.sn = p.`sn` AND p.`good_id`=mm.good_id AND p.`good_color`=mm.good_color GROUP BY mm.sn) AS `total_amount_in_vat` ,p.`salesman` as sales_admin_id,CONCAT(ss.firstname," ",ss.lastname)AS sales_admin,p.bs_campaign',"(SELECT concat('''',ph.phone_number_sn) FROM phone_number ph where ph.sales_order=p.sn and ph.status=1)as phone_number_sn
  "));
        $select->joinLeft(array('a' => 'hr.area'), 'rm.area_id = a.id', null);
        $select->joinLeft(array('ss' => 'warehouse.staff'), 'p.`salesman`=ss.id', null);

        $select->where('p.isbacks = 0');
        $select->where('p.canceled = 0');
        $select->where('p.status = 1');
        $select->where('p.sales_confirm_date is null',1);
        $select->where('p.sales_confirm_id is null',1);
        // $select->where('p.finance_confirm_date is null',1);
        // $select->where('p.finance_confirm_id is null',1);

        $select->where('p.cat_id in (11,12)');
        $select->where('p.add_time >= "'.$day.' 00:00:00"');
        $select->where('p.add_time <= "'.$day.' 23:59:59"');
        $select->where('p.warehouse_id in (32,36,62,73,74,75,90,92,93,115,116,123)');

        $select->group(new Zend_Db_Expr('p.sn'));

        $select->order('p.sn desc');
        // echo $select;die;
        return $db->fetchAll($select);
    }

    public function getDetailsProduct($sn_ref){
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> 'market'),array('p.num'))
        ->joinLeft(array('g' => 'good'), 'g.id = p.good_id', array('good_code' => 'g.name', 'good_name' => 'g.desc'))
        ->joinLeft(array('gc' => 'good_color'), 'gc.id = p.good_color', array('good_color' => 'gc.name'));

        $select->where('p.good_id <> ?', 127);
        $select->where('p.sn_ref = ?', $sn_ref);

        // echo $select;die;
        return $db->fetchAll($select);
    }





        /*
SELECT m.sn,m.`sn_ref`,m.`invoice_number`,m.`invoice_time`,m.`d_id`
,d.`store_code`,d.`title`
,CONCAT(d.`store_code`," ",d.`title`) AS distributor_name
,m.`user_id`,ss.`firstname`
,m.`shipping_address`
,s.`address`,s.`province_id`,s.`amphures_id`,s.`districts_id`,s.`zipcodes`
,sp.`provice_name`,sa.`amphure_name`,sd.`district_name`,sz.`zipcode`

,CONCAT (m.`shipping_address`,' ',sd.`district_name`,' ',sa.`amphure_name`,' ',sp.`provice_name`,' ',sz.`zipcode`) AS shipping_address
,sp.`provice_name`,sa.`amphure_name`,sd.`district_name`,sz.`zipcode`;

FROM market m
LEFT JOIN distributor d ON m.`d_id`=d.`id`
LEFT JOIN shipping_address s ON m.`shipping_address`=s.`id`
LEFT JOIN shipping_provinces sp ON s.`province_id`= sp.`provice_id`
LEFT JOIN `shipping_amphures` sa ON s.`amphures_id`= sa.`amphure_id`
LEFT JOIN `shipping_districts` sd ON s.`amphures_id`= sd.`district_id`
LEFT JOIN `shipping_zipcodes` sz ON s.`zipcodes`= sz.`zip_id`
LEFT JOIN staff ss ON m.`user_id`=ss.`id`
,CONCAT(ss.`firstname`, " ",ss.`lastname`) AS Fullname
WHERE m.sn='201909181037351522'
GROUP BY m.sn

        */

  /* //                              \\ */
  /* // Create Lao Invoice 25.09.2019 \\ */
  /* //                                \\ */


   public function getInvoiceDistributorDetails($params){
        $db = Zend_Registry::get('db');
        $select = $db->select();
             /*$totalpriceall=0;
             $totalprice=$db->select('total');
             $totalpriceall= $totalprice+$totalpriceall;*/

        $select->from(array('m'=> 'market'),array('m.sn','m.sn_ref','m.invoice_number','m.invoice_time','m.d_id','m.num','m.price','m.total','m.sale_off_price','d.not_money','d.store_code','d.title','m.warehouse_id','m.add_time'));

        $select->joinLeft(array('d' => 'distributor'), 'm.`d_id`=d.`id`', array("CONCAT( d.`finance_code`,' - ',d.`title`) AS distributor_name","CONCAT(d.`tel`) AS distributor_phone","CONCAT (IFNULL(s.address,''),' ',IFNULL(sd.`district_name`,''),' ',IFNULL(sa.`amphure_name`,''),' ',IFNULL(sp.`provice_name`,'') ) AS shipping_address",'d.agent_status'));

        $select->joinLeft(array('w' => 'warehouse'),'w.id = m.warehouse_id',array('ship_from' => 'w.name'));

        $select->joinLeft(array('sto' => HR_DB.'.store'),'sto.id = m.store_id',array('store_name' => 'sto.name','store_address' => 'sto.shipping_address','store_tel' => 'sto.phone_number','store_leader' =>'sto.leader'));

        $select->joinLeft(array('w2' => 'warehouse'),'w2.id = d.agent_warehouse_id',array('warehouse_name' => 'w2.name','warehouse_address' => 'w2.address','warehouse_tel' => 'w2.phone','warehouse_leader' => 'w2.leader'));

        $select->joinLeft(array('cl' => 'client'),'cl.customer_code = d.client_code',array("CONCAT( d.`distributor_code`,' - ',d.`title`) AS client_fullname"));


        $select->joinLeft(array('s' => 'shipping_address'), 'm.`shipping_address`=s.`id`', array(null));
        $select->joinLeft(array('sp' => 'shipping_provinces'), 's.`province_id`= sp.`provice_id`', array(null));
        $select->joinLeft(array('sa' => 'shipping_amphures'), 's.`amphures_id`= sa.`amphure_id`', array(null));
        $select->joinLeft(array('sd' => 'shipping_districts'), 's.`amphures_id`= sd.`district_id`', array(null));
        $select->joinLeft(array('sz' => 'shipping_zipcodes'), 's.`zipcodes`= sz.`zip_id`', array(null));
        $select->joinLeft(array('ct' => 'credit_type'), 'ct.`id`= d.`credit_type`', array('ct.credit'));
        $select->joinLeft(array('po' => 'tag_object'), 'po.object_id = m.sn', array(null));
        $select->joinLeft(array('tg' => 'tag'), 'tg.id = po.tag_id', array('tg.name as po_name'));

        $select->joinLeft(array('ss' => 'staff'), 'm.`user_id`=ss.`id`', array('CONCAT(ss.`firstname`, " ",ss.`lastname`) AS Fullname'));
        $select->joinLeft(array('tf' => 'staff'), 'm.`user_id`=tf.`id`', array('CONCAT(tf.`phone_number`) AS phone_number'));
         $select->group('m.sn');
        $select->where('m.sn = ?', $params['sn']);

        //echo $select;die;

        return $db->fetchAll($select);
    }

    public function getInvoiceDetails($params){
        $db = Zend_Registry::get('db');
        $select = $db->select();
        $select->from(array('p'=> 'market'),array('p.num','p.add_time','p.price','p.total','p.sale_off_price','p.text','p.num','p.sale_off_percent','p.payment_type','p.other_discounts','p.discount_note','p.dis_type_pp','p.dis_type_policy','p.type'));
        $select->joinLeft(array('g' => 'good'), 'g.id = p.good_id', array('good_code' => 'g.name', 'good_name' => 'g.desc', 'good_cname' => 'g.cat_id'));
        $select->joinLeft(array('gc' => 'good_color'), 'gc.id = p.good_color', array('good_color' => 'gc.name'));
	    $select->joinLeft(array('b' => 'brand'),'b.id = g.brand_id',array('brand_name' => 'b.name'));

        $select->where('p.sn = ?', $params['sn']);

        if(isset($params['type']) && $params['type'] == 3) {
            $select->joinLeft(array('gdc' => 'good_discount'),'p.good_id = gdc.good_id',array('product_discount' => 'gdc.discount'));
            $select->where('gdc.name =?','Staff');
        }

        if(isset($params['type']) && $params['type'] == 5) {
            $select->joinLeft(array('gdc' => 'good_discount'),'p.good_id = gdc.good_id',array('product_discount' => 'gdc.discount'));
            $select->where('gdc.name =?','DEMO'); 
        }

        //echo $select;die;

        return $db->fetchAll($select);
    }


// Function Get area by khuan
public function getareastyle($params){
        $db = Zend_Registry::get('db');
        $select = $db->select();
        $select->from(array('a' => HR_DB.'.area'),array('a.name'));
        $select->joinInner(array('r' => HR_DB.'.regional_market'), 'r.`area_id` = a.`id`',array(null));
        $select->joinInner(array('d' => 'distributor'),'d.`region` = r.`id`',array(null));
        $select->joinInner(array('k' => 'market'),'k.`d_id` = d.`id`',array(null));

        $select->where('k.`sn` = ?', $params['sn']);

        return $db->fetchAll($select);
    }

//Funtion Get compare placing an order
function checkOrderDistributor($params){
    set_time_limit(0);
 ini_set('memory_limit', '-1');
 error_reporting(~E_ALL);
 ini_set("display_error", '0');
 $d = explode('/', $params['order_from']);
 $from = $d[2].'-'.$d[1].'-'.$d[0];
 $d = explode('/', $params['order_to']);
 $to = $d[2].'-'.$d[1].'-'.$d[0];
 $db = Zend_Registry::get('db');

 $sub_select = $db->select()
     ->from(array('m' => 'market'),array('SUM(m.num)'))
     ->where('m.invoice_time >= ?',$from." 00:00:00")
     ->where('m.invoice_time <= ?',$to." 23:59:59")
     ->where('m.d_id = d.id');

 if (isset($params['order_from']) and $params['order_from']){
             list( $day, $month, $year ) = explode('/', $params['order_from']);

             if (isset($day) and isset($month) and isset($year) )
                     $sub_select->where('m.invoice_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
     }

 if (isset($params['order_to']) and $params['order_to']){
             list( $day, $month, $year ) = explode('/', $params['order_to']);

             if (isset($day) and isset($month) and isset($year) )
                     $sub_select->where('m.invoice_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
     }


     if (isset($params['good_id']) and $params['good_id'])
         $sub_select->where('m.good_id = ?', $params['good_id']);

     if (isset($params['good_color']) and $params['good_color'])
         $sub_select->where('m.good_color = ?', $params['good_color']);

     


 $get = array(
     'distributors_id'   => 'd.id',
     'distributors_name' => 'd.title',
     'imei_sn' => 'i.imei_sn',
     'good_id' => 'i.good_id',
     'good_color' => 'i.good_color',
     // 'total_qulity'      => new Zend_Db_Expr("SUM(CASE WHEN d.id = m.d_id THEN m.num END)"),
     'total_qulity'      => new Zend_Db_Expr("(".$sub_select.")"),
     'warehouse_name'    => 'd.warehouse_id',
     'status'            => 'd.del',
     // 'total_query'       => 'COUNT(d.id)'
 );
 $select = $db->select()
 ->from(array('d' => 'distributor'), $get)
 ->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array())
 ->joinLeft(array('i'    => 'imei'), 'd.id = i.distributor_id'     , array())
// ->joinLeft(array('m2' => 'market'),'d.id = m2.d_id',array())
 ->group('d.id');
 if (isset($params['warehouse']) and $params['warehouse'])
         $select->where('d.warehouse_id = ?', $params['warehouse']);

     if (isset($params['d_id']) and $params['d_id']){
             $select->where('d.id = ?', $params['d_id']);
         }
     if (isset($params['area']) && $params['area']) {
           $select->where('rm.area_id = ?',$params['area']);
         }

if(isset($params['list_imei']) && $params['list_imei']) {
    $select->group('i.imei_sn');

}


 $result = $db->fetchAll($select);
 return $result;
 }

 function checkOrderDistributorModel($params){
   set_time_limit(0);
   ini_set('memory_limit', '-1');
   error_reporting(~E_ALL);
   ini_set("display_error", '0');
   $d = explode('/', $params['order_from']);
   $from = $d[2].'-'.$d[1].'-'.$d[0];
   $d = explode('/', $params['order_to']);
   $to = $d[2].'-'.$d[1].'-'.$d[0];
   $db = Zend_Registry::get('db');

    $sub_select = $db->select()
           ->from(array('m' => 'market'),array('SUM(m.num)'))
           ->where('m.invoice_time >= ?',$from." 00:00:00")
           ->where('m.invoice_time <= ?',$to." 23:59:59")
           ->where('m.good_id = g.id')
           ->where('m.d_id = d.id');

           if(isset($params['type']) && $params['type'] !=0)
            $sub_select->where('m.type =?',$params['type']);

        if (isset($params['order_from']) and $params['order_from']){
            list( $day, $month, $year ) = explode('/', $params['order_from']);

            if (isset($day) and isset($month) and isset($year) )
                $sub_select->where('m.invoice_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['order_to']) and $params['order_to']){
            list( $day, $month, $year ) = explode('/', $params['order_to']);

            if (isset($day) and isset($month) and isset($year) )
                $sub_select->where('m.invoice_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }


        if (isset($params['good_id']) and $params['good_id'])
            $sub_select->where('m.good_id = ?', $params['good_id']);

        if (isset($params['good_color']) and $params['good_color'])
            $sub_select->where('m.good_color = ?', $params['good_color']);



   $get = array(
    'distributors_id'   => 'd.id',
    'distributors_name' => 'd.title',
    'area_name'         => 'a.name',
    'province'          => 'rm.name',
    'category'          => 'ct.name',
    'model_name'        => 'g.name',
    'model_color'       => 'gc.name',
    'total_qulity'      => new Zend_Db_Expr("(".$sub_select.")"),
    'warehouse_name'    => 'd.warehouse_id',
    'status'            => 'd.del',
);
   $select = $db->select()
   ->from(array('g'  => 'good'), $get)
   ->joinLeft(array('m' => 'market'),'m.good_id = g.id',array())
   ->joinLeft(array('d' => 'distributor'),'d.id = m.d_id',array())
   ->joinLeft(array('gc' => 'good_color'),'gc.id = m.good_color',array())
   ->joinLeft(array('ct' =>'good_category'),'ct.id = m.cat_id')
   ->joinLeft(array('rm' => HR_DB.'.regional_market'),'rm.id = d.region',array())
   ->joinLeft(array('a' => HR_DB.'.area'),'a.id = rm.area_id',array())
   ->group('m.good_id');

    if (isset($params['order_from']) and $params['order_from']){
    list( $day, $month, $year ) = explode('/', $params['order_from']);

    if (isset($day) and isset($month) and isset($year) )
        $select->where('m.invoice_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
}

if (isset($params['order_to']) and $params['order_to']){
    list( $day, $month, $year ) = explode('/', $params['order_to']);

    if (isset($day) and isset($month) and isset($year) )
        $select->where('m.invoice_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
}

if (isset($params['warehouse']) and $params['warehouse'])
    $select->where('d.warehouse_id = ?', $params['warehouse']);

if (isset($params['d_id']) and $params['d_id']){
    $select->where('d.id = ?', $params['d_id']);
}
if (isset($params['area']) && $params['area']) {
  $select->where('rm.area_id = ?',$params['area']);
}

if (isset($params['good_id']) and $params['good_id'])
    $select->where('m.good_id = ?', $params['good_id']);

if (isset($params['good_color']) and $params['good_color'])
    $select->where('m.good_color = ?', $params['good_color']);

// echo $select; die;

$result = $db->fetchAll($select);
return $result;
}

function chartCheckOrderDealer($params){
      set_time_limit(0);
 ini_set('memory_limit', '-1');
 error_reporting(~E_ALL);
 ini_set("display_error", '0');
 $d = explode('/', $params['order_from']);
 $from = $d[2].'-'.$d[1].'-'.$d[0];
 $d = explode('/', $params['order_to']);
 $to = $d[2].'-'.$d[1].'-'.$d[0];
 $db = Zend_Registry::get('db');

 $sub_select = $db->select()
     ->from(array('m' => 'market'),array('SUM(m.num)'))
     ->where('m.invoice_time >= ?',$from." 00:00:00")
     ->where('m.invoice_time <= ?',$to." 23:59:59")
     ->where('m.d_id = d.id');

 if (isset($params['order_from']) and $params['order_from']){
             list( $day, $month, $year ) = explode('/', $params['order_from']);

             if (isset($day) and isset($month) and isset($year) )
                     $sub_select->where('m.invoice_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
     }

 if (isset($params['order_to']) and $params['order_to']){
             list( $day, $month, $year ) = explode('/', $params['order_to']);

             if (isset($day) and isset($month) and isset($year) )
                     $sub_select->where('m.invoice_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
     }

     if (isset($params['good_id']) and $params['good_id'])
         $sub_select->where('m.good_id = ?', $params['good_id']);

     if (isset($params['good_color']) and $params['good_color'])
         $sub_select->where('m.good_color = ?', $params['good_color']);

 $get = array(
     'distributors_id'   => 'd.id',
     'distributors_name' => 'd.title',
     'total_qulity'      => new Zend_Db_Expr("(".$sub_select.")"),
     'warehouse_name'    => 'd.warehouse_id',
     'status'            => 'd.del',
 );
 $select = $db->select()
 ->from(array('d' => 'distributor'), $get)
 ->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array())
// ->joinLeft(array('m2' => 'market'),'d.id = m2.d_id',array())
 ->where('d.agent_warehouse_id !="" ')
 ->where('d.id != 3147')
 ->where('d.id != 3176')
 ->order('rm.area_id')
 ->group('d.id');
 if (isset($params['warehouse']) and $params['warehouse'])
         $select->where('d.warehouse_id = ?', $params['warehouse']);

     if (isset($params['d_id']) and $params['d_id']){
             $select->where('d.id = ?', $params['d_id']);
         }
     if (isset($params['area']) && $params['area']) {
           $select->where('rm.area_id = ?',$params['area']);
         }

 $result = $db->fetchAll($select);
 return $result;
     }

function chartCheckOrderWH($params){
     set_time_limit(0);
     ini_set('memory_limit', '-1');
     error_reporting(~E_ALL);
     ini_set("display_error", '0');
     $d = explode('/', $params['order_from']);
     $from = $d[2].'-'.$d[1].'-'.$d[0];
     $d = explode('/', $params['order_to']);
     $to = $d[2].'-'.$d[1].'-'.$d[0];
     $db = Zend_Registry::get('db');

     $sub_select = $db->select()
         ->from(array('m' => 'market'),array('SUM(m.num)'))
        // ->joinLeft(array('g' => 'good'), 'm.good_id =g.id', array())
         ->where('m.invoice_time >= ?',$from." 00:00:00")
         ->where('m.invoice_time <= ?',$to." 23:59:59")
         ->where('m.warehouse_id = d.warehouse_id');

     if (isset($params['order_from']) and $params['order_from']){
                 list( $day, $month, $year ) = explode('/', $params['order_from']);

                 if (isset($day) and isset($month) and isset($year) )
                         $sub_select->where('m.invoice_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
         }

     if (isset($params['order_to']) and $params['order_to']){
                 list( $day, $month, $year ) = explode('/', $params['order_to']);

                 if (isset($day) and isset($month) and isset($year) )
                         $sub_select->where('m.invoice_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
         }

         if (isset($params['good_id']) and $params['good_id'])
             $sub_select->where('m.good_id = ?', $params['good_id']);

         if (isset($params['good_color']) and $params['good_color'])
             $sub_select->where('m.good_color = ?', $params['good_color']);
         
     $get = array(
         'area_id'           => 'rm.area_id',
         'total_qulity'      => new Zend_Db_Expr("(".$sub_select.")"),
         'warehouse_name'    => 'd.warehouse_id',
     );

     $select = $db->select()
     ->from(array('d' => 'distributor'), $get)
     ->join(array('w' => 'warehouse'), 'w.id = d.warehouse_id', array())
     ->joinleft(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array())
     ->where('d.del = 0')
     ->where('d.warehouse_id != 36')
     ->where('w.show_kerry != 2')
     ->order('rm.area_id')
     ->group('d.warehouse_id');
     if (isset($params['warehouse']) and $params['warehouse'])
             $select->where('d.warehouse_id = ?', $params['warehouse']);

         if (isset($params['d_id']) and $params['d_id']){
                 $select->where('d.id = ?', $params['d_id']);
             }
         if (isset($params['area']) && $params['area']) {
               $select->where('rm.area_id = ?',$params['area']);
             }

     $result = $db->fetchAll($select);
     return $result;
     }

     function chartCheckOrder($params2){
     set_time_limit(0);
     ini_set('memory_limit', '-1');
     error_reporting(~E_ALL);
     ini_set("display_error", '0');
     $group_by = $params2['group_by'];
     $d = explode('/', $params2['order_from']);
     $from = $d[2].'-'.$d[1].'-'.$d[0];
     $d = explode('/', $params2['order_to']);
     $to = $d[2].'-'.$d[1].'-'.$d[0];
     $db = Zend_Registry::get('db');

     if ($params2['group_by'] == 'm2.invoice_time'){
        $get = array(
         'sum_total'         => 'SUM(m2.num)',
         'warehouse_name'    => 'd.warehouse_id',
         'invoice_time'      => 'DATE_FORMAT(m2.invoice_time, "%h:%m:%s")',
        );
     }else{
         
     $get = array(
     'sum_total'                  => 'SUM(m2.num)',
     'warehouse_name'    => 'd.warehouse_id',
     'invoice_time'      => 'DATE_FORMAT(m2.invoice_time, "%Y-%m-%d")'
     );
     }

     $select = $db->select()
     ->from(array('d' => 'distributor'), array())
     ->joinLeft(array('m2' => 'market'),'d.id = m2.d_id', $get)
     ->where('d.del = 0')
     ->where('d.warehouse_id = 36')
     ->where('m2.invoice_time >= ?',$from." 00:00:00")
     ->where('m2.invoice_time <= ?',$to." 23:59:00")
     ->group($group_by)
     ->order('m2.invoice_time');
     $result = $db->fetchAll($select);
     return $result;

     }

     function getNumForReuturnByModelColor($params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => 'market'),array('qty' => 'SUM(p.num)'))

            // ->from(array('ir' => 'imei_return'),array('qty' => 'count(i.imei_sn)'))
            // ->join(array('i' => 'imei'), 'i.imei_sn = ir.imei_sn', array(null))

            ->where('p.good_id =?',$params['good_id'])
            ->where('p.good_color =?',$params['good_color'])
            ->where('p.sn =?',$params['sn']);


        $result = $db->fetchAll($select);
       return $result;
     }

}
