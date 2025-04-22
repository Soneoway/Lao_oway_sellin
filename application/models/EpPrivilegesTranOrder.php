<?php
class Application_Model_EpPrivilegesTranOrder extends Zend_Db_Table_Abstract{
    protected $_name = 'ep_privileges_tran_order';
    
    function _initConfig($company_id)
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        if($company_id=='1')//OPPO
        {
            $db = Zend_Db::factory($config->resources->db);
            $db->query("SET NAMES utf8;");
            Zend_Db_Table::setDefaultAdapter($db);
            Zend_Registry::set('db', $db);
        }else if($company_id=='2'){//ONEPLUS
            $db = Zend_Db::factory($config->resources->dboneplus);
            $db->query("SET NAMES utf8;");
            Zend_Db_Table::setDefaultAdapter($db);
            Zend_Registry::set('db', $db);
        }
        $setdb = Zend_Registry::get('db');
        return $setdb;
    }

    function getDiscountType()
    {
        $db = Zend_Registry::get('db');
        $select="SELECT d.cat_id,d.discount_id,d.discount_type,if(d.cat_id=11,if(d.discount_type=0,'(Phone) EOL',concat('(Phone)',' ส่วนลด',d.discount_type,'%')),(if(d.discount_type=0,'(Acc) EOL',concat('(Acc)',' ส่วนลด',d.discount_type,'%'))))as discount_type_name
            FROM warehouse.ep_privileges_discount d
            where d.active=1
            order by d.cat_id,d.discount_type";
        $result = $db->fetchAll($select);
        return $result;
    }

    function fetchPagination($page, $limit, &$total, $params)
    {
        $company_id=$params['company_id'];
        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //print_r($params);//die;

            $select = $db->select();
            $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS '.$company_id.' as company_id,p.privileges_sn,p.cat_id,p.discount_id,p.discount_type,p.privileges_no,sum(pl.qty)as sum_qty,sum(pl.total_price)as total_amount'),
                new Zend_Db_Expr("if(p.status <> 6,CASE
    WHEN (((p.payment_slip_image IS NULL)or(p.staff_card_image IS NULL)or(p.id_card_image IS NULL))and(p.hr_confirm_date IS NULL)AND(p.invoice_date IS NULL)AND(p.receive_date IS NULL)) THEN 1
    WHEN (((p.payment_slip_image IS not NULL)OR(p.staff_card_image IS not NULL)OR(p.id_card_image IS not NULL))AND(p.hr_confirm_date IS NULL)AND(p.invoice_date IS NULL)AND(p.receive_date IS NULL)) THEN 2
    WHEN (((p.payment_slip_image IS not NULL)OR(p.staff_card_image IS not NULL)OR(p.id_card_image IS not NULL))AND(p.hr_confirm_date IS not NULL)AND(p.invoice_date IS NULL)AND(p.receive_date IS NULL)) THEN 3
    WHEN (((p.payment_slip_image IS not NULL)OR(p.staff_card_image IS not NULL)OR(p.id_card_image IS not NULL))AND(p.hr_confirm_date IS not NULL)AND(p.invoice_date IS not NULL)AND(p.receive_date IS NULL)) THEN 4
    WHEN (((p.payment_slip_image IS not NULL)OR(p.staff_card_image IS not NULL)OR(p.id_card_image IS not NULL))AND(p.hr_confirm_date IS not NULL)AND(p.invoice_date IS not NULL)AND(p.receive_date IS not NULL)) THEN 5
    
    ELSE '-' 
END,'6') as status_order"),
                new Zend_Db_Expr("if(p.status <> 6,CASE
    WHEN (((p.payment_slip_image IS NULL)OR(p.staff_card_image IS NULL)OR(p.id_card_image IS NULL))AND(p.hr_confirm_date IS NULL)AND(p.invoice_date IS NULL)AND(p.receive_date IS NULL)) THEN 'รอแนบใบเปย์'
    WHEN (((p.payment_slip_image IS NOT NULL)OR(p.staff_card_image IS NOT NULL)OR(p.id_card_image IS NOT NULL))AND(p.hr_confirm_date IS NULL)AND(p.invoice_date IS NULL)AND(p.receive_date IS NULL)) THEN 'รออนุมัติ'
    WHEN (((p.payment_slip_image IS NOT NULL)OR(p.staff_card_image IS NOT NULL)OR(p.id_card_image IS NOT NULL))AND(p.hr_confirm_date IS NOT NULL)AND(p.invoice_date IS NULL)AND(p.receive_date IS NULL)) THEN 'เปิดบิลแล้ว รอจัดสินค้า'
    WHEN (((p.payment_slip_image IS NOT NULL)OR(p.staff_card_image IS NOT NULL)OR(p.id_card_image IS NOT NULL))AND(p.hr_confirm_date IS NOT NULL)AND(p.invoice_date IS NOT NULL)AND(p.receive_date IS NULL)) THEN 'สินค้ารอการจัดส่ง'
    WHEN (((p.payment_slip_image IS NOT NULL)OR(p.staff_card_image IS NOT NULL)OR(p.id_card_image IS NOT NULL))AND(p.hr_confirm_date IS NOT NULL)AND(p.invoice_date IS NOT NULL)AND(p.receive_date IS NOT NULL)) THEN 'ได้รับสินค้าแล้ว'
    
    ELSE '-' 
END,'ยกเลิก') as status_name
"),
                'p.create_date','p.hr_confirm_date','p.remark','p.hr_remark','p.status','p.sales_order_sn','(select m.sn_ref from market m where m.sn=p.sales_order_sn group by m.sn) as sales_order_no'));
            $select->joinLeft(array('pl' => 'ep_privileges_tran_order_item'), 'p.privileges_sn = pl.privileges_sn', array('pl.qty'));
            $select->joinLeft(array('d' => 'distributor'), 'd.id = p.distributor_id', array('d.name','d.title','d.mst_sn','d.unames','d.store_code','d.district'));
            $select->joinLeft(array('sp' => 'shipping_address'), 'sp.d_id = d.id and sp.id=p.shipping_id', array("delivery_address"=>"CONCAT(sp.address,IF(am.provice_id=1,'',' อ.'),am.amphure_name,' จ.',pv.provice_name,' ',zc.zipcode)"));
            $select->joinLeft(array('am' => 'shipping_amphures'), 'am.amphure_id=sp.amphures_id', array('am.amphure_id'));
            $select->joinLeft(array('pv' => 'shipping_provinces'), 'pv.provice_id=sp.province_id', array('pv.provice_id','pv.provice_name'));
            $select->joinLeft(array('zc' => 'shipping_zipcodes'), 'zc.zip_id=sp.zipcodes', array('zc.zip_id'));
            $select->joinLeft(array('g' => 'good'), 'g.id=pl.good_id', array('g.name as good_name'));
            $select->joinLeft(array('gc' => 'good_color'), 'gc.id = pl.good_color', array('gc.name as good_color_name'));
            $select->joinLeft(array('c' => 'company'), 'p.company_id = c.company_id', array('c.company_id','c.company_name'));
            $select->joinleft(array('s'=>'oppohr.oppo_staff'),'p.staff_code=s.staff_code',array("staff_name"=>"concat(s.firstname_th,' ',s.lastname_th)",'s.staff_code','s.email'));
            $select->joinleft(array('ss'=>'staff'),'p.hr_comfirm_by=ss.id',array("hr_name"=>"concat(ss.firstname,' ',ss.lastname)",'ss.email'));

        if (isset($params['privileges_no']) and $params['privileges_no'] and $params['privileges_no'] !=''){
            $select->where('p.privileges_no LIKE ?', '%'.$params['privileges_no'].'%');
        }

        if (isset($params['staff_name']) and $params['staff_name'] and $params['staff_name'] !='')
        {
            $select->where('s.firstname LIKE "%'.$params['staff_name'].'%" or s.lastname LIKE "%'.$params['staff_name'].'%" or s.email LIKE "%'.$params['staff_name'].'%"', null);
        }

        

        if (isset($params['distributor_name']) and $params['distributor_name'] and $params['distributor_name'] !=''){
            $select->where('d.title LIKE ?', '%'.$params['distributor_name'].'%');
        }


        if (isset($params['status']) and $params['status'] and $params['status'] !='0'){
            if($params['status'] =='1')
            {
                $select->where('p.status <> 6 and (((p.payment_slip_image IS NULL)OR (p.staff_card_image IS NULL)OR (p.id_card_image IS NULL))AND (p.hr_confirm_date IS NULL)AND (p.invoice_date IS NULL)AND (p.receive_date IS NULL))',null);
            }else if($params['status'] =='2')
            {
                $select->where('p.status <> 6 and (((p.payment_slip_image IS NOT NULL)OR (p.staff_card_image IS NOT NULL)OR (p.id_card_image IS NOT NULL))AND (p.hr_confirm_date IS NULL)AND (p.invoice_date IS NULL)AND (p.receive_date IS NULL))',null);
            }else if($params['status'] =='3')
            {
                $select->where('p.status <> 6 and (((p.payment_slip_image IS NOT NULL)OR (p.staff_card_image IS NOT NULL)OR (p.id_card_image IS NOT NULL))AND (p.hr_confirm_date IS NOT NULL)AND (p.invoice_date IS NULL)AND (p.receive_date IS NULL))',null);
            }else if($params['status'] =='4')
            {
                $select->where('p.status <> 6 and (((p.payment_slip_image IS NOT NULL)OR (p.staff_card_image IS NOT NULL)OR (p.id_card_image IS NOT NULL))AND (p.hr_confirm_date IS NOT NULL)AND (p.invoice_date IS NOT NULL)AND (p.receive_date IS NULL))',null);
            }else if($params['status'] =='5')
            {
                $select->where('p.status <> 6 and (((p.payment_slip_image IS NOT NULL)OR (p.staff_card_image IS NOT NULL)OR (p.id_card_image IS NOT NULL))AND (p.hr_confirm_date IS NOT NULL)AND (p.invoice_date IS NOT NULL)AND (p.receive_date IS NOT NULL))',null);
            }else if($params['status'] =='6')
            {
                $select->where('p.status = 6',null);
            }
           // $select->having(" status_order IN(".$params['status'].")");
        }else{
            $select->having(" status_order IN(2,3,4,5)");
        }

        if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm']=='confirm'){

            $select->where('p.hr_confirm_date is null ', null);
        }

        if (isset($params['start_date']) and $params['start_date']){
            $select->where('p.create_date >= ?', $params['start_date'].' 00:00:00');
        }

        if (isset($params['end_date']) and $params['end_date']){
            $select->where('p.create_date <= ?', $params['end_date'].' 23:59:59');
        }

        if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm']=='list')
        {
            $select->group(new Zend_Db_Expr('p.privileges_sn'));
        }


        if (isset($params['discount_id']) and $params['discount_id'] and $params['discount_id'] !='0'){
            $select->where('p.discount_id = ?', $params['discount_id']);
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

        $select->group('p.privileges_sn');
        $select->order(['p.update_date asc']);
        $select->order(['p.create_date asc']);
        //$select->limitPage($page, $limit);

        if (isset($params['sort']) and $params['sort']) {
            $order_str = $collate = '';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str .= $params['sort'] . $collate . $desc;
            

            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

        //echo $select;die;

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }


    function staff_sales_order_view($params)
    {
        $company_id=$params['company_id'];
        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');

        //print_r($params);die;

            $select = $db->select();
            $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.privileges_sn,p.privileges_no'),'p.create_date','p.hr_confirm_date','p.payment_date','p.remark','p.hr_remark','p.status','p.sales_order_sn','p.order_type','p.warehouse_id','p.distributor_id','d.rank','(select m.sn_ref from market m where m.sn=p.sales_order_sn group by m.sn) as sales_order_no','p.payment_slip_image','p.staff_card_image','p.id_card_image'));
            $select->joinLeft(array('pl' => 'ep_privileges_tran_order_item'), 'p.privileges_sn = pl.privileges_sn', array('p.discount_type','pl.qty','pl.cat_id','pl.master_unit_price','pl.unit_price','pl.total_price'));
            $select->joinLeft(array('d' => 'distributor'), 'd.id = p.distributor_id', array('d.name','d.title','d.mst_sn','d.unames','d.store_code','d.district'));
            /*$select->joinLeft(array('sp' => 'shipping_address'), 'sp.d_id = d.id and sp.id=p.shipping_id', array("delivery_address"=>"CONCAT(sp.address,IF(am.provice_id=1,'',' อ.'),am.amphure_name,' จ.',pv.provice_name,' ',zc.zipcode)"));*/

            $select->joinLeft(array('sp' => 'shipping_address'), 'sp.d_id = d.id and sp.id=p.shipping_id', array("delivery_address"=>"if(pv.provice_id=1,(concat(trim(sp.address),' แขวง',trim(sd.district_name),' ',trim(am.amphure_name),' จ.',trim(pv.provice_name))),(concat(trim(sp.address),' ต.',trim(sd.district_name),' อ.',trim(am.amphure_name),' จ.',trim(pv.provice_name))))"));
            $select->joinLeft(array('am' => 'shipping_amphures'), 'am.amphure_id=sp.amphures_id', array('am.amphure_id'));
            $select->joinLeft(array('pv' => 'shipping_provinces'), 'pv.provice_id=sp.province_id', array('pv.provice_id'));
            $select->joinLeft(array('zc' => 'shipping_zipcodes'), 'zc.zip_id=sp.zipcodes', array('zc.zip_id'));
            $select->joinLeft(array('sd' => 'shipping_districts'), 'sd.district_code=sp.districts_id', array('sd.district_code'));
            $select->joinLeft(array('g' => 'good'), 'g.id=pl.good_id', array('g.desc','g.name as good_name','pl.good_id'));
            $select->joinLeft(array('gc' => 'good_color'), 'gc.id = pl.good_color', array('gc.name as good_color_name','pl.good_color'));
            $select->joinLeft(array('c' => 'company'), 'p.company_id = c.company_id', array('c.company_id','c.company_name'));
            //$select->joinleft(array('s'=>'hr.staff'),'p.staff_code=s.id',array("staff_name"=>"concat(s.firstname,' ',s.lastname)",'s.email as sell_email','p.staff_code as sell_id'));
            $select->joinleft(array('s'=>'oppohr.oppo_staff'),'p.staff_code=s.staff_code',array("staff_name"=>"concat(s.firstname_th,' ',s.lastname_th)",'p.staff_code','s.email'));
            $select->joinleft(array('ss'=>'staff'),'p.hr_comfirm_by=ss.id',array("hr_name"=>"concat(ss.firstname,' ',ss.lastname)",'ss.email as admin_email','p.hr_comfirm_by'));

        if (isset($params['privileges_sn']) and $params['privileges_sn'] and $params['privileges_sn'] !='')
            $select->where('p.privileges_sn LIKE ?', '%'.$params['privileges_sn'].'%');

        $select->order(['p.create_date asc']);

        //echo $select;die;
        $result = $db->fetchAll($select);

        return $result;
    }


    function staff_sales_order($company_id,$privileges_sn)
    {
        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');

            $select = $db->select();
            $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.privileges_sn,p.privileges_no,p.company_id'),'p.create_date','p.hr_confirm_date','p.payment_date','p.remark','p.hr_remark','p.bank_id','p.status','p.shipping_id','p.sales_order_sn','p.order_type','p.discount_type','p.warehouse_id','p.distributor_id','d.rank','(select m.sn_ref from market m where m.sn=p.sales_order_sn group by m.sn) as sales_order_no','p.payment_slip_image','p.staff_card_image','p.id_card_image'));
            $select->joinLeft(array('pl' => 'ep_privileges_tran_order_item'), 'p.privileges_sn = pl.privileges_sn', array('pl.qty','pl.cat_id','pl.unit_price','pl.total_price','pl.master_unit_price'));
            $select->joinLeft(array('d' => 'distributor'), 'd.id = p.distributor_id', array('d.name','d.title','d.mst_sn','d.unames','d.store_code','d.district'));
            $select->joinLeft(array('sp' => 'shipping_address'), 'sp.d_id = d.id and sp.id=p.shipping_id', array("delivery_address"=>"CONCAT(sp.address,IF(am.provice_id=1,'',' อ.'),am.amphure_name,' จ.',pv.provice_name,' ',zc.zipcode)"));
            $select->joinLeft(array('am' => 'shipping_amphures'), 'am.amphure_id=sp.amphures_id', array('am.amphure_id'));
            $select->joinLeft(array('pv' => 'shipping_provinces'), 'pv.provice_id=sp.province_id', array('pv.provice_id'));
            $select->joinLeft(array('zc' => 'shipping_zipcodes'), 'zc.zip_id=sp.zipcodes', array('zc.zip_id'));
            $select->joinLeft(array('g' => 'good'), 'g.id=pl.good_id', array('g.desc','g.name as good_name','pl.good_id'));
            $select->joinLeft(array('gc' => 'good_color'), 'gc.id = pl.good_color', array('gc.name as good_color_name','pl.good_color'));
            $select->joinleft(array('s'=>'oppohr.oppo_staff'),'p.staff_code=s.staff_code',array("staff_name"=>"concat(s.staff_code,' ',s.firstname_th,' ',s.lastname_th,' ส่วนลด ',if(p.discount_type=0,'EOL',p.discount_type),'%',' (พนักงาน)')",'s.staff_code','s.email'));
            $select->joinleft(array('ss'=>'staff'),'p.hr_comfirm_by=ss.id',array("hr_name"=>"concat(ss.firstname,' ',ss.lastname)",'ss.email as admin_email','p.hr_comfirm_by'));

            $select->where('p.privileges_sn = ?', $privileges_sn);

            $select->order(['p.create_date asc']);
        //echo $select;die;
        $result = $db->fetchAll($select);

        return $result;
    }

    function privileges_discount($company_id,$discount_id)
    {
        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');

            $select = $db->select();
            $select->from(array('p' => "ep_privileges_discount"),array(new Zend_Db_Expr('p.discount_id,IF(p.cat_id=11,IF(p.discount_type = 0,CONCAT(ct.name," EOL"),CONCAT(ct.name, " ", p.discount_type, "% ",(IF(p.good_id is not null,(SELECT concat("(",g.name," : ",g.desc,")") FROM good g WHERE g.id=p.good_id),"")))),IF(p.discount_type = 0,CONCAT(ct.name," EOL"),CONCAT(ct.name, " ", p.discount_type, "%")))AS discount_type'),'p.period_limit_day','p.warehouse_id','p.bank_id','p.distributor_id','p.company_id'));
            $select->joinLeft(array('ct' => 'good_category'), 'ct.id=p.cat_id', array('p.cat_id'));
            $select->where('p.active = 1', null);
            if($discount_id!="")
            {
               // $select->where('p.discount_id = ?',$discount_id);
            }

/*            if (isset($params['discount_id']) and $params['discount_id'] and $params['discount_id'] !=''){
                $select->where('p.discount_id = ?', ''.$params['discount_id'].'');
            }*/
            $select->order(['ct.id']);
            $select->order(['p.discount_type asc']);
        //echo $select;die;
        $result = $db->fetchAll($select);
        //echo "<pre>";print_r($result);
        return $result;
    }

    /*function privileges_discount($discount_id)
    {
        $db = Zend_Registry::get('db');

            $select = $db->select();
            $select->from(array('p' => "warehouse.ep_privileges_discount"),array(new Zend_Db_Expr('p.discount_id,IF(p.cat_id=11,IF(p.discount_type = 0,CONCAT(ct.name," EOL"),CONCAT(ct.name, " ", p.discount_type, "% ",(IF(p.good_id is not null,(SELECT concat("(",g.name," : ",g.desc,")") FROM warehouse.good g WHERE g.id=p.good_id),"")))),IF(p.discount_type = 0,CONCAT(ct.name," EOL"),CONCAT(ct.name, " ", p.discount_type, "%")))AS discount_type'),'p.period_limit_day','p.warehouse_id','p.bank_id','p.distributor_id'));
            $select->joinLeft(array('ct' => 'warehouse.good_category'), 'ct.id=p.cat_id', array('p.cat_id'));
            $select->where('p.active = 1', null);

            if (isset($params['discount_id']) and $params['discount_id'] and $params['discount_id'] !=''){
                $select->where('p.discount_id = ?', ''.$params['discount_id'].'');
            }
            $select->order(['ct.id']);
            $select->order(['p.discount_type asc']);
        //echo $select;die;
        $result = $db->fetchAll($select);
        //echo "<pre>";print_r($result);
        return $result;
    }*/

    /*function privileges_discount($discount_id)
    {
        $db = Zend_Registry::get('db');

            $select = $db->select();
            $select->from(array('p' => "ep_privileges_discount"),array(new Zend_Db_Expr('p.discount_id,CONCAT("[",c.company_name,"] ",IF(p.cat_id=11,IF(p.discount_type = 0,CONCAT(ct.name," EOL"),CONCAT(ct.name, " ", p.discount_type, "% ",(IF(p.good_id is not null,(SELECT concat("(",g.name," : ",g.desc,")") FROM good g WHERE g.id=p.good_id),"")))),IF(p.discount_type = 0,CONCAT(ct.name," EOL"),CONCAT(ct.name, " ", p.discount_type, "%")))) AS discount_type'),'p.period_limit_day','p.warehouse_id','p.bank_id','p.distributor_id','p.good_id','p.company_id'));
            $select->joinLeft(array('ct' => 'good_category'), 'ct.id=p.cat_id', array('p.cat_id'));
            $select->joinLeft(array('c' => 'company'), 'c.company_id=p.company_id', array('p.company_id'));
            $select->where('p.active = 1', null);

            if (isset($params['discount_id']) and $params['discount_id'] and $params['discount_id'] !=''){
                $select->where('p.discount_id = ?', ''.$params['discount_id'].'');
            }
            $select->order(['ct.id']);
            $select->order(['p.discount_type asc']);
        echo $select;die;
        //$result = $db->fetchAll($select);

         $res = null;
         $discount = $db->fetchAll($select);
         $row=0;
         //print_r($discount);
         //die; distributor_id
         foreach ($discount as $v)
         {
            if($v['company_id']=='2')
            {
                $res[$row] = $v;
                if($v['good_id'] !="")
                {
                    $product = $this->get_product($v['company_id'],$v['good_id']);
                    $res[$row]['discount_type'] = $product['good_full_name'];
                }
                
            }else{
                $res[$row] = $v;
            }
            
            $row +=1;
         }
         //print_r($res);//die;
         return $res;
    }*/

    function get_product($company_id,$good_id)
    {
        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('g' => 'good'), array('g.id as good_id','g.name','g.desc','CONCAT(g.name," [",g.desc,"]") as good_full_name'));
        $select->where('g.id = ?',$good_id);
        $res = $db->fetchRow($select);
        return $res;
    }

    function department($department_id)
    {
        $this->_initConfig(1);
        $db = Zend_Registry::get('db');

            $select = $db->select();
            $select->from(array('p' => "oppohr.department"),array(new Zend_Db_Expr('p.id as department_id,p.name as department_name'),'p.status'));
            $select->where('p.status = 1', null);

            if ($department_id !=''){
                $select->where('p.id = ?', ''.$department_id.'');
            }

            $select->order(['p.id asc']);
        //echo $select;die;
        $result = $db->fetchAll($select);

        return $result;
    }

    function position($department_id)
    {
        $this->_initConfig(1);
        $db = Zend_Registry::get('db');

            $select = $db->select();
            $select->from(array('p' => "oppohr.department_position"),array(new Zend_Db_Expr('p.department_id,p.id as position_id,concat(p.code," ",p.position_name,"    [",ifnull(d.id,"")," ",ifnull(d.name,""),"]")as position_name'),'p.code as position_code'));
            $select->joinLeft(array('d' => 'oppohr.department'), 'd.id=p.department_id', array('p.department_id'));


            if ($department_id !=''){
                $select->where('p.department_id = ?', ''.$department_id.'');
            }

            $select->order(['p.id asc']);
        //echo $select;die;
        $result = $db->fetchAll($select);

        return $result;
    }

    function check_position_discount($department_id,$position_id,$discount_id,$warehouse_id,$bank_id,$distributor_id)
    {

        $db = Zend_Registry::get('db');

            $select = $db->select();
            $select->from(array('p' => "ep_privileges_position_discount"),array(new Zend_Db_Expr('p.department_id,p.position_id,p.discount_id'),'p.active'));
            $select->where('p.active = 1', null);

            if ($department_id !=''){
                $select->where('p.department_id = ?', ''.$department_id.'');
            }
            if ($position_id !=''){
                $select->where('p.position_id = ?', ''.$position_id.'');
            }
            if ($discount_id !=''){
                $select->where('p.discount_id = ?', ''.$discount_id.'');
            }
            if ($warehouse_id !=''){
                $select->where('p.warehouse_id = ?', ''.$warehouse_id.'');
            }
            if ($bank_id !=''){
                $select->where('p.bank_id = ?', ''.$bank_id.'');
            }
            if ($distributor_id !=''){
                $select->where('p.distributor_id = ?', ''.$distributor_id.'');
            }

        //echo $select;die;
        $result = $db->fetchAll($select);

        return $result;
    }


    function position_discount_setup($company_id,$discount_id)
    {
        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');

        $select = "SELECT ppd.discount_id,p.department_id,d.name AS department_name,ppd.position_id,ppd.position_code,CONCAT(ifnull(ppd.position_code,''),' ',p.position_name) AS position_name 
        FROM ep_privileges_position_discount ppd 
        LEFT JOIN ep_privileges_discount pd ON pd.discount_id=ppd.discount_id
        LEFT JOIN oppohr.department d ON d.id=ppd.department_id
        LEFT JOIN oppohr.department_position p ON p.id=ppd.position_id
        WHERE ppd.discount_id='".$discount_id."' order by p.department_id,p.id";

        //echo $select;die;
        $result = $db->fetchAll($select);
        return $result;

    }
  

    function CheckStaffPrivilegesPosition($page, $limit, &$total, $params)
    {
        $db = Zend_Registry::get('db');
        $select="
            select distinct 
            t1.privileges_no
            ,t1.cat_id,t1.discount_product_type,if(t1.discount_type=0,0,if(t1.next_buy_day>0,t1.next_buy_day,0)) as next_buy_days
            ,if(t1.discount_type=0,'ใช้สิทธิ์ได้',if(t1.next_buy_day>0,'ใช้สิทธิ์ไปแล้ว','ใช้สิทธิ์ได้')) as status_discount
            ,if(t1.discount_type=0,'ใช้สิทธิ์ได้',if(t1.next_buy_day>0,concat(t1.next_buy_day,'วัน'),'ใช้สิทธิ์ได้')) as next_buy_day_name
            ,if(t1.last_buy_date='','-',t1.last_buy_date)as last_buy_date_name
            ,CASE
            WHEN t1.discount_type = 0 THEN 'ใช้ได้ทันที'
            ELSE if(t1.next_buy_day<=0,'ใช้ได้ทันที',(DATE_ADD(DATE_FORMAT(t1.last_buy_date, '%Y-%m-%d'), INTERVAL (if(t1.next_buy_day>0,t1.next_buy_day,0)) DAY)))
            END as next_buy_date_name
            ,t1.* 
            from (SELECT  
            pd.cat_id,ct.name as discount_product_type  
            ,pd.discount_id,pd.discount_type
            ,CASE
            WHEN (pd.discount_type=0) THEN 'EOL'
            else concat(pd.discount_type,'%')
            end as discount_type_name
            ,IFNULL((select max(trr.create_date) from warehouse.ep_privileges_tran_order trr where trr.discount_id=pd.discount_id and trr.discount_type=pd.discount_type and trr.staff_code=os.staff_code and trr.status <> 6),'')as last_buy_date
            ,IFNULL((SELECT MAX(trr.privileges_no) FROM warehouse.ep_privileges_tran_order trr WHERE trr.discount_id=pd.discount_id and trr.discount_type=pd.discount_type AND trr.staff_code=os.staff_code AND trr.status <> 6),'-') AS privileges_no
            ,pd.payment_type
            ,os.id AS staff_id,os.staff_code, CONCAT(os.firstname_th, ' ', os.lastname_th) AS staff_name
            ,ss.department AS department_id,dpp.id AS position_id,ss.department_position_code AS position_code,dpp.position_name
            ,ss.start_date as start_work_date
            ,DATEDIFF(NOW(),ss.start_date) AS total_work_day
            ,pd.start_use_day
            ,IFNULL(DATEDIFF(NOW(),(select max(trr.create_date) from warehouse.ep_privileges_tran_order trr where trr.discount_id=pd.discount_id and trr.discount_type=pd.discount_type and trr.staff_code=os.staff_code and trr.status <> 6)),pd.period_limit_day)as last_buy_day
            ,(pd.period_limit_day-IFNULL(DATEDIFF(NOW(),(select max(trr.create_date) from warehouse.ep_privileges_tran_order trr where trr.discount_id=pd.discount_id and trr.discount_type=pd.discount_type and trr.staff_code=os.staff_code and trr.status <> 6)),pd.period_limit_day))as next_buy_day
            ,ppd.bank_id,ppd.distributor_id,ppd.warehouse_id,shp.province_id,shp.province_name,shp.shipping_province_id,shp.shipping_province_name,if(pd.discount_type=0,'ไม่จำกัด',pd.qty_per_year) as qty_per_year,if(pd.discount_type=0,'ไม่จำกัด',pd.period_limit_day)as period_limit_day
            FROM oppohr.oppo_staff os
            LEFT JOIN oppohr.salary ss ON ss.staff_id=os.id
            LEFT JOIN warehouse.ep_privileges_position_discount ppd ON ppd.position_code=ss.department_position_code COLLATE utf8_unicode_ci
            LEFT JOIN warehouse.ep_privileges_discount pd ON pd.discount_id=ppd.discount_id
            LEFT JOIN warehouse.ep_privileges_tran_order AS tr ON tr.discount_id=pd.discount_id and tr.discount_type=pd.discount_type and tr.staff_code=os.staff_code 
            LEFT JOIN oppohr.department_position dpp ON ss.department_position_code=dpp.code
            left join warehouse.good_category ct on ct.id=pd.cat_id
            
            left join (SELECT
            s.code,IFNULL(am.th_province_id,1)AS province_id, TRIM(tp.name_th) AS province_name,tpp.id as shipping_province_id,TRIM(tpp.name_th) as shipping_province_name
            FROM hr.staff AS s 
            JOIN hr.group AS g ON s.group_id = g.id 
            JOIN hr.regional_market AS rm ON s.regional_market = rm.id 
            JOIN hr.area AS a ON rm.area_id = a.id 
            LEFT JOIN hr.area_map AS am ON s.regional_market = am.regional_market 
            LEFT JOIN hr.th_province AS tp ON tp.id =IFNULL(am.th_province_id, 1)
            left join warehouse.province_shipping_common sm on sm.province_id= am.th_province_id
            LEFT JOIN hr.th_province AS tpp ON tpp.id = IFNULL(sm.shipping_province_id, 1)
            WHERE s.status=1) shp on shp.code=os.staff_code
            WHERE os.staff_code ='".$params['staff_code']."'
            AND os.staff_status=1
            
        )t1
            order by t1.discount_product_type,t1.discount_type
            ";

            //order by if(t1.discount_type=0,0,if(t1.next_buy_day>0,t1.next_buy_day,0)),t1.next_buy_day desc
            //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }


    function exportPrivilegesStaffOrder($params)
    {
        $company_id=$params['company_id'];
        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //print_r($params);//die;

            $select = $db->select();
            $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.privileges_sn,p.discount_type,if(p.discount_type=0,"EOL",concat(p.discount_type,"%")) as discount_type_name,p.privileges_no,sum(pl.qty)as sum_qty,sum(pl.total_price)as total_amount'),
                new Zend_Db_Expr("if(p.status <> 6,CASE
    WHEN (((p.payment_slip_image IS NULL)or(p.staff_card_image IS NULL)or(p.id_card_image IS NULL))and(p.hr_confirm_date IS NULL)AND(p.invoice_date IS NULL)AND(p.receive_date IS NULL)) THEN 1
    WHEN (((p.payment_slip_image IS not NULL)OR(p.staff_card_image IS not NULL)OR(p.id_card_image IS not NULL))AND(p.hr_confirm_date IS NULL)AND(p.invoice_date IS NULL)AND(p.receive_date IS NULL)) THEN 2
    WHEN (((p.payment_slip_image IS not NULL)OR(p.staff_card_image IS not NULL)OR(p.id_card_image IS not NULL))AND(p.hr_confirm_date IS not NULL)AND(p.invoice_date IS NULL)AND(p.receive_date IS NULL)) THEN 3
    WHEN (((p.payment_slip_image IS not NULL)OR(p.staff_card_image IS not NULL)OR(p.id_card_image IS not NULL))AND(p.hr_confirm_date IS not NULL)AND(p.invoice_date IS not NULL)AND(p.receive_date IS NULL)) THEN 4
    WHEN (((p.payment_slip_image IS not NULL)OR(p.staff_card_image IS not NULL)OR(p.id_card_image IS not NULL))AND(p.hr_confirm_date IS not NULL)AND(p.invoice_date IS not NULL)AND(p.receive_date IS not NULL)) THEN 5
    
    ELSE '-' 
END,'6') as status_order"),
                new Zend_Db_Expr("if(p.status <> 6,CASE
    WHEN (((p.payment_slip_image IS NULL)OR(p.staff_card_image IS NULL)OR(p.id_card_image IS NULL))AND(p.hr_confirm_date IS NULL)AND(p.invoice_date IS NULL)AND(p.receive_date IS NULL)) THEN 'รอแนบใบเปย์'
    WHEN (((p.payment_slip_image IS NOT NULL)OR(p.staff_card_image IS NOT NULL)OR(p.id_card_image IS NOT NULL))AND(p.hr_confirm_date IS NULL)AND(p.invoice_date IS NULL)AND(p.receive_date IS NULL)) THEN 'รออนุมัติ'
    WHEN (((p.payment_slip_image IS NOT NULL)OR(p.staff_card_image IS NOT NULL)OR(p.id_card_image IS NOT NULL))AND(p.hr_confirm_date IS NOT NULL)AND(p.invoice_date IS NULL)AND(p.receive_date IS NULL)) THEN 'เปิดบิลแล้ว รอจัดสินค้า'
    WHEN (((p.payment_slip_image IS NOT NULL)OR(p.staff_card_image IS NOT NULL)OR(p.id_card_image IS NOT NULL))AND(p.hr_confirm_date IS NOT NULL)AND(p.invoice_date IS NOT NULL)AND(p.receive_date IS NULL)) THEN 'สินค้ารอการจัดส่ง'
    WHEN (((p.payment_slip_image IS NOT NULL)OR(p.staff_card_image IS NOT NULL)OR(p.id_card_image IS NOT NULL))AND(p.hr_confirm_date IS NOT NULL)AND(p.invoice_date IS NOT NULL)AND(p.receive_date IS NOT NULL)) THEN 'ได้รับสินค้าแล้ว'
    
    ELSE '-' 
END,'ยกเลิก') as status_name,dpp.id AS position_id,sss.department_position_code AS position_code,dpp.position_name,sss.department AS department_id,dp.name as department_name

"),
                'p.create_date','p.hr_confirm_date','p.remark','p.hr_remark','p.status','p.sales_order_sn','(select m.sn_ref from market m where m.sn=p.sales_order_sn group by m.sn) as sales_order_no','pl.master_unit_price','pl.total_price'));
            $select->joinLeft(array('pl' => 'ep_privileges_tran_order_item'), 'p.privileges_sn = pl.privileges_sn', array('pl.qty'));
            $select->joinLeft(array('d' => 'distributor'), 'd.id = p.distributor_id', array('d.name','d.title','d.mst_sn','d.unames','d.store_code','d.district'));
            $select->joinLeft(array('sp' => 'shipping_address'), 'sp.d_id = d.id and sp.id=p.shipping_id', array("delivery_address"=>"CONCAT(sp.address,IF(am.provice_id=1,'',' อ.'),am.amphure_name,' จ.',pv.provice_name,' ',zc.zipcode)"));
            $select->joinLeft(array('am' => 'shipping_amphures'), 'am.amphure_id=sp.amphures_id', array('am.amphure_id'));
            $select->joinLeft(array('pv' => 'shipping_provinces'), 'pv.provice_id=sp.province_id', array('pv.provice_id','pv.provice_name'));
            $select->joinLeft(array('zc' => 'shipping_zipcodes'), 'zc.zip_id=sp.zipcodes', array('zc.zip_id'));
            $select->joinLeft(array('g' => 'good'), 'g.id=pl.good_id', array('concat(g.name," ",g.desc) as good_name'));
            $select->joinLeft(array('gc' => 'good_color'), 'gc.id = pl.good_color', array('gc.name as good_color_name'));

            $select->joinleft(array('s'=>'oppohr.oppo_staff'),'p.staff_code=s.staff_code',array("staff_name"=>"concat(s.firstname_th,' ',s.lastname_th)",'s.staff_code','s.email'));
            $select->joinleft(array('ss'=>'staff'),'p.hr_comfirm_by=ss.id',array("hr_name"=>"concat(ss.firstname,' ',ss.lastname)",'ss.email'));

            $select->joinleft(array('sss'=>'oppohr.salary'),'sss.staff_id=s.id',array(null));
            $select->joinLeft(array('dpp' => 'oppohr.department_position'), 'sss.department_position_code=dpp.code', array(null));
            $select->joinLeft(array('dp' => 'oppohr.department'), 'dp.id=sss.department', array(null));

        if (isset($params['privileges_no']) and $params['privileges_no'] and $params['privileges_no'] !='')
            $select->where('p.privileges_no LIKE ?', '%'.$params['privileges_no'].'%');

        if (isset($params['staff_name']) and $params['staff_name'] and $params['staff_name'] !='')
        {
            $select->where('s.firstname LIKE "%'.$params['staff_name'].'%" or s.lastname LIKE "%'.$params['staff_name'].'%" or s.email LIKE "%'.$params['staff_name'].'%"', null);
        }

        if (isset($params['discount_id']) and $params['discount_id'] and $params['discount_id'] !='0'){
            $select->where('p.discount_id = ?', $params['discount_id']);
        }  

        if (isset($params['distributor_name']) and $params['distributor_name'] and $params['distributor_name'] !='')
            $select->where('d.title LIKE ?', '%'.$params['distributor_name'].'%');


        if (isset($params['status']) and $params['status'] and $params['status'] !='0'){
            $select->having(" status_order IN(".$params['status'].")");
        }else{
            $select->having(" status_order IN(2,3,4,5)");
        }

        if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm']=='confirm'){

            $select->where('p.hr_confirm_date is null ', null);
        }

        if (isset($params['start_date']) and $params['start_date'])
            $select->where('p.hr_confirm_date >= ?', $params['start_date'].' 00:00:00');

        if (isset($params['end_date']) and $params['end_date'])
            $select->where('p.hr_confirm_date <= ?', $params['end_date'].' 23:59:59');

        if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm']=='list')
        {
            $select->group(new Zend_Db_Expr('p.privileges_sn'));
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

        $select->group('p.privileges_sn');
        $select->group('pl.good_id');
        $select->group('pl.good_color');

        //$select->order(['p.update_date asc']);
        $select->order(['p.create_date asc']);

        //echo $select;die;

        $result = $db->fetchAll($select);
        return $result;
    }

}