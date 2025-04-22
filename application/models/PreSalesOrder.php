<?php
class Application_Model_PreSalesOrder extends Zend_Db_Table_Abstract{
    protected $_name = 'pre_sales_order';
    
    public function getPreSales_No($sn)
    {
        $sn_ref="";
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref_reward('PS',".$sn.")");
            $stmt->execute();
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Pre Sales Order No, Please try again!');
        }
        return $sn_ref;
    }

    function fetchPagination($page, $limit, &$total, $params)
    {
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //print_r($params);//die;

            $select = $db->select();
            $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.presales_sn,p.presales_no,sum(pl.qty)as sum_qty'),'p.request_date','p.admin_confirm_date','p.sell_remark','p.admin_remark','p.status','p.sales_order_sn','p.use_cn','(select m.sn_ref from market m where m.sn=p.sales_order_sn group by m.sn) as sales_order_no'));
            $select->joinLeft(array('pl' => 'pre_sales_order_item'), 'p.presales_sn = pl.presales_sn', array('pl.qty'));
            $select->joinLeft(array('d' => 'distributor'), 'd.id = p.distributor_id', array('d.name','d.title','d.mst_sn','d.unames','d.store_code','d.district'));
            $select->joinLeft(array('sp' => 'shipping_address'), 'sp.d_id = d.id and sp.id=p.shipping_id', array("delivery_address"=>"CONCAT(sp.address,IF(am.provice_id=1,'',' อ.'),am.amphure_name,' จ.',pv.provice_name,' ',zc.zipcode)"));
            $select->joinLeft(array('am' => 'shipping_amphures'), 'am.amphure_id=sp.amphures_id', array('am.amphure_id'));
            $select->joinLeft(array('pv' => 'shipping_provinces'), 'pv.provice_id=sp.province_id', array('pv.provice_id'));
            $select->joinLeft(array('zc' => 'shipping_zipcodes'), 'zc.zip_id=sp.zipcodes', array('zc.zip_id'));
            $select->joinLeft(array('g' => 'good'), 'g.id=pl.good_id', array('g.name as good_name'));
            $select->joinLeft(array('gc' => 'good_color'), 'gc.id = pl.good_color', array('gc.name as good_color_name'));

            $select->joinleft(array('s'=>'hr.staff'),'p.request_by=s.id',array("sell_name"=>"concat(s.firstname,' ',s.lastname)",'s.email','s.code as request_staff_code'));
            $select->joinleft(array('ss'=>'staff'),'p.admin_id=ss.id',array("admin_name"=>"concat(ss.firstname,' ',ss.lastname)",'ss.email'));
            $select->where('p.delete_date is null', null);

        if (isset($params['presales_no']) and $params['presales_no'] and $params['presales_no'] !='')
            $select->where('p.presales_no LIKE ?', '%'.$params['presales_no'].'%');

        if (isset($params['sell_name']) and $params['sell_name'] and $params['sell_name'] !='')
        {
            $select->where('s.firstname LIKE "%'.$params['sell_name'].'%" or s.lastname LIKE "%'.$params['sell_name'].'%" or s.email LIKE "%'.$params['sell_name'].'%"', null);
        }

        

        if (isset($params['distributor_name']) and $params['distributor_name'] and $params['distributor_name'] !='')
            $select->where('d.title LIKE ?', '%'.$params['distributor_name'].'%');


        if (isset($params['status']) and $params['status'] and $params['status'] !='')
            $select->where('p.status = ?', $params['status']);

        if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm']=='confirm'){

            $select->where('p.admin_confirm_date is null ', null);
        }

        if (isset($params['start_date']) and $params['start_date'])
            $select->where('p.request_date >= ?', $params['start_date'].' 00:00:00');

        if (isset($params['end_date']) and $params['end_date'])
            $select->where('p.request_date <= ?', $params['end_date'].' 23:59:59');

        if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm']=='list')
        {
            $select->group(new Zend_Db_Expr('p.presales_sn'));
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

        $select->group('p.presales_sn');
        $select->order(['p.request_date asc']);

        if (isset($params['export']) and $params['export'])
        {
           $select->group('pl.good_id'); 
           $select->group('pl.good_color'); 
        }else{
           $select->limitPage($page, $limit);
        }

        //echo $select;//die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function pre_sales_order_view($params)
    {
        $db = Zend_Registry::get('db');

        //print_r($params);die;

            $select = $db->select();
            $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.presales_sn,p.presales_no,CASE
    WHEN p.order_type = 1 THEN "Normal"
    WHEN p.order_type = 2 THEN "Demo"
    WHEN p.order_type = 3 THEN "APK"
    ELSE "" END as order_type_name'),'p.request_date as sell_request_date','p.admin_confirm_date','p.sell_remark','p.admin_remark','p.status','p.sales_order_sn','p.use_cn','p.order_type','p.warehouse_id','p.distributor_id','d.rank','(select m.sn_ref from market m where m.sn=p.sales_order_sn group by m.sn) as sales_order_no'));
            $select->joinLeft(array('pl' => 'pre_sales_order_item'), 'p.presales_sn = pl.presales_sn', array('pl.qty','pl.cat_id'));
            $select->joinLeft(array('d' => 'distributor'), 'd.id = p.distributor_id', array('d.name','d.title','d.mst_sn','d.unames','d.store_code','d.district'));
            $select->joinLeft(array('sp' => 'shipping_address'), 'sp.d_id = d.id and sp.id=p.shipping_id', array("delivery_address"=>"CONCAT(sp.address,IF(am.provice_id=1,'',' อ.'),am.amphure_name,' จ.',pv.provice_name,' ',zc.zipcode)"));
            $select->joinLeft(array('am' => 'shipping_amphures'), 'am.amphure_id=sp.amphures_id', array('am.amphure_id'));
            $select->joinLeft(array('pv' => 'shipping_provinces'), 'pv.provice_id=sp.province_id', array('pv.provice_id'));
            $select->joinLeft(array('zc' => 'shipping_zipcodes'), 'zc.zip_id=sp.zipcodes', array('zc.zip_id'));
            $select->joinLeft(array('g' => 'good'), 'g.id=pl.good_id', array('g.desc','g.name as good_name','pl.good_id'));
            $select->joinLeft(array('gc' => 'good_color'), 'gc.id = pl.good_color', array('gc.name as good_color_name','pl.good_color'));

            $select->joinleft(array('s'=>'hr.staff'),'p.request_by=s.id',array("sell_name"=>"concat(s.firstname,' ',s.lastname)",'s.email as sell_email','p.request_by as sell_id','s.code as request_staff_code'));
            $select->joinleft(array('ss'=>'staff'),'p.admin_id=ss.id',array("admin_name"=>"concat(ss.firstname,' ',ss.lastname)",'ss.email as admin_email','p.admin_id'));

        if (isset($params['presales_sn']) and $params['presales_sn'] and $params['presales_sn'] !='')
            $select->where('p.presales_sn LIKE ?', '%'.$params['presales_sn'].'%');

        $select->order(['p.request_date asc']);

        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function pre_sales_order($presales_sn)
    {
        $db = Zend_Registry::get('db');

        //print_r($params);die;

            $select = $db->select();
            $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.presales_sn,p.presales_no'),'p.request_date as sell_request_date','p.admin_confirm_date','p.sell_remark','p.admin_remark','p.status','p.sales_order_sn','p.use_cn','p.order_type','p.warehouse_id','p.distributor_id','d.rank','p.shipping_id','(select m.sn_ref from market m where m.sn=p.sales_order_sn group by m.sn) as sales_order_no'));
            $select->joinLeft(array('pl' => 'pre_sales_order_item'), 'p.presales_sn = pl.presales_sn', array('pl.qty','pl.cat_id'));
            $select->joinLeft(array('d' => 'distributor'), 'd.id = p.distributor_id', array('d.name','d.title','d.mst_sn','d.unames','d.store_code','d.district'));
            $select->joinLeft(array('sp' => 'shipping_address'), 'sp.d_id = d.id and sp.id=p.shipping_id', array("delivery_address"=>"CONCAT(sp.address,IF(am.provice_id=1,'',' อ.'),am.amphure_name,' จ.',pv.provice_name,' ',zc.zipcode)"));
            $select->joinLeft(array('am' => 'shipping_amphures'), 'am.amphure_id=sp.amphures_id', array('am.amphure_id'));
            $select->joinLeft(array('pv' => 'shipping_provinces'), 'pv.provice_id=sp.province_id', array('pv.provice_id'));
            $select->joinLeft(array('zc' => 'shipping_zipcodes'), 'zc.zip_id=sp.zipcodes', array('zc.zip_id'));
            $select->joinLeft(array('g' => 'good'), 'g.id=pl.good_id', array('g.desc','g.name as good_name','pl.good_id'));
            $select->joinLeft(array('gc' => 'good_color'), 'gc.id = pl.good_color', array('gc.name as good_color_name','pl.good_color'));

            $select->joinleft(array('s'=>'hr.staff'),'p.request_by=s.id',array("sell_name"=>"concat(s.firstname,' ',s.lastname)",'s.email as sell_email','p.request_by as sell_id'));
            $select->joinleft(array('ss'=>'staff'),'p.admin_id=ss.id',array("admin_name"=>"concat(ss.firstname,' ',ss.lastname)",'ss.email as admin_email','p.admin_id'));

            $select->where('p.presales_sn = ?', $presales_sn);

        $select->order(['p.request_date asc']);

        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    
  
}