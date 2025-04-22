<?php
class Application_Model_CreditNote extends Zend_Db_Table_Abstract
{
    protected $_name = 'credit_note';

    public function creditnoteManualfetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        //print_r($params);die;
        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.creditnote_sn,p.distributor_id,
p.total_amount,p.use_total,p.balance_total,d.title,d.unames as company_name,p.create_date,p.status,p.manual_active,p.chanel,p.vat,p.wht_vat,p.manual_remark,p.price_ext_vat,p.wht_price,p.confirm_by,p.manual_remark,p.finance_remark,
p.update_date'), 'p.distributor_id','total_amount' => 'p.total_amount', 'use_total' => 'p.use_total', 'confirm_date' => 'p.confirm_date', 'balance_total' => 'p.balance_total', 'creditnote_type' => 'p.creditnote_type'));
        $select->joinleft(array('d'=>'distributor'),'p.distributor_id=d.id', array(null));
        $select->joinLeft(array('dm' => 'distributor'), 'p.main_d_id=dm.id', array('IF(p.main_d_id IS NOT NULL,dm.id,d.id) AS distributor_id','IF(p.main_d_id IS NOT NULL,dm.title,d.title) AS title','IF(p.main_d_id IS NOT NULL,dm.store_code,d.store_code) AS store_code','IF(p.main_d_id IS NOT NULL,dm.mst_sn,d.mst_sn) AS tax_no','IF(p.main_d_id IS NOT NULL,dm.parent,d.parent) AS parent','IF(p.main_d_id IS NOT NULL,dm.finance_group,d.finance_group) AS finance_group','d.add AS add_tax'));
        $select->joinLeft(array('rm'=>'hr.regional_market'),'d.district = rm.id',array('district'=>'rm.name'));
        $select->joinLeft(array('rm2'=>'hr.regional_market'),'d.region = rm2.id',array('province'=>'rm2.name'));
        $select->joinLeft(array('a'=>'hr.area'),'rm2.area_id = a.id',array('area_name'=>'a.name'));


        $select->joinleft(array('s'=>'staff'),'p.create_by=s.id',array("staff_name"=>"concat(s.firstname,' ',s.lastname) as staff_name",'s.email'));
        $select->joinleft(array('ss'=>'staff'),'p.confirm_by=ss.id',array("staff_name"=>"concat(ss.firstname,' ',ss.lastname) as confirm_staff_name"));
        $select->where('p.manual = ?', 1);

        
      
        if ($params['action_frm']=='confirm') {
            if (isset($params['status']) && $params['status']==1) {
                $select->where('p.confirm_date is null', null);
            }else if (isset($params['status']) && $params['status']==2) {
                $select->where('p.confirm_date is not null', null);
            }
        }

        if ($params['action_frm']=='list') {
            if (isset($params['status']) && $params['status']) {
                $select->where('p.status = ?', $params['status']);
            }
        }

        $select->where('p.total_amount >= ?',1);

        if (isset($params['d_id']) && $params['d_id']) {
            $select->where('p.distributor_id = ?', $params['d_id']);
        }

        if (isset($params['creditnote_sn']) && $params['creditnote_sn']) {
            $select->where('p.creditnote_sn = ?', $params['creditnote_sn']);
        }

        if (isset($params['distributor_name']) && $params['distributor_name']) {
            $select->where('d.title like ?', '%'.$params['distributor_name'].'%');
        }

        if (isset($params['view_status']) && $params['view_status']==1) {
            $select->where('p.confirm_date is null', null);
        }else if (isset($params['view_status']) && $params['view_status']==2) {
            $select->where('p.confirm_date is not null', null);
        }


        $select->order('p.update_date desc');
        $select->order('p.create_date desc');


        if (isset($params['sort']) and $params['sort']) {
            $order_str = $collate = '';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str .= $params['sort'] . $collate . $desc;
            

            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

        // echo $select;die;

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function getReward_CreateNoteNo_Ref($db,$distributor_id,$user_id,$sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $creditnote_sn="";
        try {
            $stmt = $db->prepare("CALL gen_running_no_ref_reward('CN',".$sn.")");
            $stmt->execute();
            $result = $stmt->fetch();
            $creditnote_sn = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage($e);
        }
        return $creditnote_sn;
    }

    public function getCredit_Note_Return_List_imei($distributor_id,$creditnote_sn)
    {
        try{
             $db = Zend_Registry::get('db');

        if($creditnote_sn !=''){
            
           $select = $db->select()
            ->from(array('ir'=> 'imei_return'),array('ir.*'))
            ->joinLeft(array('im' => 'imei'), 'im.imei_sn=ir.imei_sn', array('im.imei_sn'))
            ->joinLeft(array('mk' => 'market'), 'mk.sn=ir.sales_order_sn AND im.good_id=mk.good_id AND im.good_color=mk.good_color', array('mk.invoice_number'))
            ->joinLeft(array('g' => 'good'), 'im.good_id=g.id', array('g.name AS product_code'))
            ->joinLeft(array('gc' => 'good_color'), 'im.good_color=gc.id', array("concat(g.desc,' ',gc.name) AS product_detail"))

            ->where('ir.creditnote_sn = ?', $creditnote_sn)
            ->order('mk.invoice_number');
            
        }
            //   echo $select;die; 
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    
    //CN Reward
    public function getCredit_Note_Reward_View($distributor_id,$creditnote_sn)
    {
        try{
             $db = Zend_Registry::get('db');

                if($creditnote_sn !=''){
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note'),array('cr.*','cr.creditnote_sn as creditnote_sn_view','cr.create_date as create_date_cn_view'))
                    ->joinLeft(array('d' => 'distributor'), 'cr.distributor_id=d.id', array('d.*'))
                    ->joinLeft(array('m' => 'oppoclub_reward_cn_imei'), 'm.creditnote_sn=cr.creditnote_sn COLLATE utf8_unicode_ci', array('m.*','COUNT(m.imei_sn)AS num','m.out_price'))
                    ->joinLeft(array('im' => 'imei'), 'im.imei_sn=m.imei_sn', array('im.imei_sn','SUM(im.out_price)AS invoice_total_price'))
                    ->joinLeft(array('mk' => 'market'), 'mk.sn=im.sales_sn AND im.good_id=mk.good_id AND im.good_color=mk.good_color', array('mk.invoice_number'))
                    ->joinLeft(array('g' => 'good'), 'im.good_id=g.id', array(new Zend_Db_Expr('g.name AS product_code,g.desc AS product_detail')))
                    ->where('cr.distributor_id = ?', $distributor_id)
                    ->where('cr.creditnote_sn = ?', $creditnote_sn)
                    ->group('cr.creditnote_sn')
                    ->group('im.good_id')
                    ->group('im.good_color')
                    ->order('mk.invoice_number','im.good_id');
                   
                }
           //  echo $select;die;   
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    public function getCredit_Note_Reward_List_imei($distributor_id,$creditnote_sn)
    {
        try{
             $db = Zend_Registry::get('db');

                if($creditnote_sn !=''){
                   $select = $db->select()
                    ->from(array('cr'=> 'oppoclub_reward_cn_imei'),array('cr.invoice_number','cr.creditnote_sn','cr.imei_sn'))
                    ->joinLeft(array('g' => 'good'), 'cr.good_id=g.id', array('g.name AS product_code'))
                    ->joinLeft(array('gc' => 'good_color'), 'cr.good_color=gc.id', array(new Zend_Db_Expr('g.name AS product_code,g.desc AS product_detail')))
                    ->where('cr.creditnote_sn = ?', $creditnote_sn)
                    //->group('cr.invoice_number')
                    ->order('cr.invoice_number');
                }
           //  echo $select;die;   
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    //---------------------------------------------------------

    //---------------------CP Import------------------------------------
    public function getCredit_Note_Protection_Price_Import_View($distributor_id,$creditnote_sn)
    {
        try{
             $db = Zend_Registry::get('db');

                if($creditnote_sn !=''){
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note'),array('cr.*','cr.creditnote_sn as creditnote_sn_view','cr.create_date as create_date_cn_view'))
                    ->joinLeft(array('d' => 'distributor'), 'cr.distributor_id=d.id', array('d.*'))
                    ->joinLeft(array('m' => 'bvg_imei'), 'm.creditnote_sn=cr.creditnote_sn COLLATE utf8_unicode_ci', array('m.*','COUNT(m.good_id)AS num','(m.total_price) AS before_total_price','m.invoice_number as invoice_number_bvg'))
                    ->joinLeft(array('im' => 'imei'), 'im.imei_sn=m.imei_sn', array('im.imei_sn','SUM(im.out_price)AS invoice_total_price'))
                    ->joinLeft(array('mk' => 'market'), 'mk.sn=m.sales_sn AND m.good_id=mk.good_id AND m.good_color=mk.good_color', array('mk.invoice_number'))
                    ->joinLeft(array('g' => 'good'), 'm.good_id=g.id', array(new Zend_Db_Expr('g.name AS product_code,g.desc AS product_detail')))
                    ->where('cr.distributor_id = ?', $distributor_id)
                    ->where('cr.creditnote_sn = ?', $creditnote_sn)
                    ->group('cr.creditnote_sn')
                    ->group('m.good_id')
                    ->group('m.good_color')
                    ->order('mk.invoice_number','m.good_id');
                   
                }
            // echo $select;die;   
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    public function getCredit_Note_Protection_Price_Import_View_Product_List($distributor_id,$creditnote_sn)
    {
        try{
             $db = Zend_Registry::get('db');
                /*
                if($creditnote_sn !=''){
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note_cp_import'),array('cr.creditnote_sn','cr.imei_sn','cr.invoice_number','cr.product_detail','SUM(cr.total_num)AS total_num','(cr.price_unit)AS price_unit','SUM(cr.total_price)AS total_price','SUM(cr.correct_bal_amount)AS correct_bal_amount','SUM(cr.difference_amount)as difference_amount','SUM(cr.total_VAT)as total_VAT','SUM(cr.total_amount)as total_amount','cr.cat_id','cr.remark'))
                    ->where('cr.creditnote_sn = ?', $creditnote_sn)
                    ->group('cr.invoice_number')
                    ->group('cr.product_detail')
                    ->order('cr.invoice_number');
                   
                }
                */

                if($creditnote_sn !=''){
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note_cp_import'),array('cr.creditnote_sn','cr.imei_sn','cr.invoice_number','cr.product_detail','SUM(cr.total_num)AS total_num','(cr.price_unit)AS price_unit','SUM(cr.total_price)AS total_price','SUM(cr.correct_bal_amount)AS correct_bal_amount','SUM(cr.difference_amount)as difference_amount','SUM(cr.total_VAT)as total_VAT','SUM(cr.total_VAT+cr.difference_amount)as total_amount','cr.cat_id','cr.remark'))
                    ->where('cr.creditnote_sn = ?', $creditnote_sn)
                    ->group('cr.invoice_number')
                    ->group('cr.product_detail')
                    ->order('cr.invoice_number');
                   
                }
            // echo $select;die;   
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    public function getCredit_Note_Protection_Price_Import_View_List_imei($distributor_id,$creditnote_sn)
    {
        try{
             $db = Zend_Registry::get('db');

                if($creditnote_sn !=''){
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note_cp_import'),array('cr.invoice_number','cr.creditnote_sn','cr.imei_sn','cr.product_detail'))
                    ->where('cr.creditnote_sn = ?', $creditnote_sn)
                    //->group('cr.invoice_number')
                    ->order('cr.invoice_number');
                }
           //  echo $select;die;   
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    public function getCredit_Note_Protection_Price_View($distributor_id,$creditnote_sn)
    {
        try{
             $db = Zend_Registry::get('db');

                if($creditnote_sn !=''){
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note'),array('cr.*','cr.creditnote_sn as creditnote_sn_view','cr.create_date as create_date_cn_view'))
                    ->joinLeft(array('d' => 'distributor'), 'cr.distributor_id=d.id', array('d.*'))
                    ->joinLeft(array('m' => 'bvg_imei'), 'm.creditnote_sn=cr.creditnote_sn COLLATE utf8_unicode_ci', array('m.*','COUNT(m.good_id)AS num','(m.total_price) AS before_total_price','m.invoice_number as invoice_number_bvg','m.cat_id'))
                    //->joinLeft(array('im' => 'imei'), 'im.imei_sn=m.imei_sn', array('im.imei_sn','im.out_price AS invoice_price','SUM(im.out_price)AS invoice_total_price'))
                    ->joinLeft(array('im' => 'imei'), 'im.imei_sn=m.imei_sn', array('im.imei_sn','SUM(im.out_price)AS invoice_total_price'))
                    ->joinLeft(array('mk' => 'market'), 'mk.sn=m.sales_sn AND m.good_id=mk.good_id AND m.good_color=mk.good_color', array('mk.invoice_number'))

                    //->joinLeft(array('mk' => 'market'), 'mk.sn=m.sales_sn AND m.good_id=mk.good_id AND m.good_color=mk.good_color', array('mk.invoice_number','mk.price AS invoice_price'))
                    //->joinLeft(array('mk' => 'market'), 'mk.sn=m.sales_sn AND m.good_id=mk.good_id AND m.good_color=mk.good_color', array('mk.invoice_number','(mk.price - m.price) AS invoice_price'))
                    ->joinLeft(array('g' => 'good'), 'm.good_id=g.id', array(new Zend_Db_Expr('g.name AS product_code,g.desc AS product_detail')))
                    ->joinLeft(array('gc' => 'good_color'), 'm.good_color=gc.id', array('gc.name AS color_name'))
                    ->joinLeft(array('s' => 'staff'), 'm.create_by=s.id ', array("CONCAT(s.firstname,' ',s.lastname)AS prepared_by"))

                    ->where('cr.distributor_id = ?', $distributor_id)
                    ->where('cr.creditnote_sn = ?', $creditnote_sn)
                    ->group('cr.creditnote_sn')
                    ->group('m.invoice_number')
                    ->group('m.good_id')
                    ->group('m.good_color')
                    ->order('m.invoice_number','m.good_id');
                   
                }
             //echo $select;die;   
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    public function getCredit_Note_Protection_Price_View_List_imei($distributor_id,$creditnote_sn)
    {
        try{
             $db = Zend_Registry::get('db');

                if($creditnote_sn !=''){
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note'),array('cr.*'))
                    ->joinLeft(array('d' => 'distributor'), 'cr.distributor_id=d.id', array('d.*'))
                    ->joinLeft(array('m' => 'bvg_imei'), 'm.creditnote_sn=cr.creditnote_sn COLLATE utf8_unicode_ci', array('m.*','COUNT(m.good_id)AS num','m.invoice_number as invoice_number_import'))
                    ->joinLeft(array('im' => 'imei'), 'im.imei_sn=m.imei_sn', array('im.imei_sn','SUM(im.out_price)AS invoice_price'))
                    ->joinLeft(array('mk' => 'market'), 'mk.sn=im.sales_sn AND m.good_id=mk.good_id AND m.good_color=mk.good_color', array('mk.invoice_number'))
                    ->joinLeft(array('g' => 'good'), 'm.good_id=g.id', array(new Zend_Db_Expr('g.name AS product_code,g.desc AS product_detail')))
                    ->joinLeft(array('gc' => 'good_color'), 'm.good_color=gc.id', array('gc.name AS color_name'))
                    ->where('cr.distributor_id = ?', $distributor_id)
                    ->where('cr.creditnote_sn = ?', $creditnote_sn)
                    ->group('m.imei_sn')
                    ->group('cr.creditnote_sn')
                    ->group('g.name')
                    ->group('mk.invoice_number')
                    ->order('mk.invoice_number','m.good_id');
                }
            // echo $select;die;   
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }


    public function getCredit_Note_list_distributor_page($page, $limit, &$total, $params)
    {
        //print_r($params);die;
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');
        try{
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
             $db = Zend_Registry::get('db');
             

                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note'),array('SUM(cr.total_amount)AS total_amount','SUM(cr.balance_total)AS balance_total','cr.create_date','cr.update_date','cr.status','cr.chanel'))
                    ->joinLeft(array('d' => 'distributor'), 'cr.distributor_id=d.id', array('d.id as distributor_id','d.title','d.store_code'));
                    //->where('cr.status = 1', null);

                    if (isset($params['cn_status']) and $params['cn_status'] ==0){
                       $select->where('cr.status = 0',null); //ปิดการใช้งาน
                    }else if (isset($params['cn_status']) and $params['cn_status'] ==2){
                       //แสดงทั้งหมด
                    }else{
                       $select->where('cr.status =1',null);//เปิดใช้งาน 
                    }
                    

                    if (isset($params['sn']) and $params['sn'])
                       $select->where('cr.creditnote_sn LIKE ? or cr.sn LIKE ?', '%'.$params['sn'].'%');
                    
                    if (isset($params['d_id']) and $params['d_id'])    
                       $select->where('cr.distributor_id =?',$params['d_id']);

                    if (isset($params['created_at_from']) and $params['created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_from']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                    }

                    if (isset($params['chanel']) and $params['chanel'])
                    {
                       // $select->where('cr.chanel LIKE ?',$params['chanel'].'%');
                        if($params['chanel'] == 'return'){
                            $select->where('cr.chanel is null or cr.chanel = ?', $params['chanel']);
                        }else{
                            $select->where('cr.chanel = ? ', $params['chanel']);
                        }
                    }

                    if (isset($params['created_at_to']) and $params['created_at_to']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_to']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                    }

                    if (isset($params['creditnote_type']) and $params['creditnote_type'] !=''){
                       $select->where('cr.creditnote_type =?',$params['creditnote_type']);
                    }

                    if( isset($params['finance_group']) && $params['finance_group'] != '' ){
                        $select->where('d.finance_group = ?',$params['finance_group']);
                    }

                    /*
                    if (isset($params['text']) and $params['text'])
                        ->where('d.title LIKE ?', '%'.$params['text'].'%')
                   */

                    $select->group('d.id');
                    //$select->order('cr.create_date DESC'); 

                    if (isset($params['sort']) and $params['sort']) {
                        $order_str = $collate = '';

                        $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

                        if ($params['sort'] == 'create_date') {
                            $params['sort'] = 'cr.create_date';
                        } elseif ($params['sort'] == 'd_id') {
                            $params['sort'] = 'd.title';
                        } 

                        $order_str .= $params['sort'] . $collate . $desc;

                        $select->order(new Zend_Db_Expr($order_str));
                    }

                    if($userStorage->warehouse_type !=''){
                        $warehouse_type = $userStorage->warehouse_type;
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
                    }else{
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
                    }

                    if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                        $select->where('d.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or p.warehouse_id in (71,92)',null);
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

                    if ($limit) { $select->limitPage($page, $limit); }
                    
                    //echo $select;die;
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            //$total = 300;
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }
    public function getCredit_Note_list_for_export($page, $limit, $total, $params)
    {
        //print_r($params);
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');
        try{
             $userStorage = Zend_Auth::getInstance()->getStorage()->read();
             $db = Zend_Registry::get('db');
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note'),array('SUM(cr.total_amount)AS total_amount','SUM(cr.balance_total)AS balance_total','cr.create_date','cr.status'))
                    ->joinLeft(array('d' => 'distributor'), 'cr.distributor_id=d.id', array('d.id as distributor_id','d.title','d.store_code','d.finance_group'));
                    //->where('cr.status = 1', null);

                    if (isset($params['cn_status']) and $params['cn_status'] ==0){
                       $select->where('cr.status = 0',null); //ปิดการใช้งาน
                    }else if (isset($params['cn_status']) and $params['cn_status'] ==2){
                       //แสดงทั้งหมด
                    }else{
                       $select->where('cr.status =1',null);//เปิดใช้งาน 
                    }

                    if (isset($params['sn']) and $params['sn'])    
                       $select->where('cr.creditnote_sn LIKE ? or cr.sn LIKE ?', '%'.$params['sn'].'%');
                    
                    if (isset($params['d_id']) and $params['d_id'])    
                       $select->where('cr.distributor_id =?',$params['d_id']);

                    if (isset($params['created_at_from']) and $params['created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_from']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                    }

                    if (isset($params['created_at_to']) and $params['created_at_to']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_to']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                    }

                    if( isset($params['finance_group']) && $params['finance_group'] != '' ){
                        $select->where('d.finance_group = ?',$params['finance_group']);
                    }

                    /*
                    if (isset($params['text']) and $params['text'])
                        ->where('d.title LIKE ?', '%'.$params['text'].'%')
                   */

                    if( isset($params['creditnote_type']) && $params['creditnote_type'] != '' ){
                        $select->where('cr.creditnote_type = ? ', $params['creditnote_type']);
                    }

                    if( isset($params['chanel']) && $params['chanel'] != '' ){
                        if($params['chanel'] == 'return'){
                            $select->where('cr.chanel is null or cr.chanel = ?', $params['chanel']);
                        }else{
                            $select->where('cr.chanel = ? ', $params['chanel']);
                        }
                    }

                    if($userStorage->warehouse_type !=''){
                        $warehouse_type = $userStorage->warehouse_type;
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
                    }else{
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
                    }

                    if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                        $select->where('d.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or p.warehouse_id in (71,92)',null);
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

                    $select->group('d.id');
                    //$select->order('cr.create_date DESC'); 

                    if (isset($params['sort']) and $params['sort']) {
                        $order_str = $collate = '';

                        $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

                        if ($params['sort'] == 'create_date') {
                            $params['sort'] = 'cr.create_date';
                        } elseif ($params['sort'] == 'd_id') {
                            $params['sort'] = 'd.title';
                        } 

                        $order_str .= $params['sort'] . $collate . $desc;

                        $select->order(new Zend_Db_Expr($order_str));
                    }
                    
                    //echo $select;die;
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            //$total = 300;
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }


    public function getCredit_Note_list_for_export_list1($page, $limit, &$total, $params)
    {
        
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');
        try{
             $userStorage = Zend_Auth::getInstance()->getStorage()->read();
             $db = Zend_Registry::get('db');
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note'),array('cr.total_amount AS total_amount','cr.balance_total AS balance_total','cr.create_date','cr.creditnote_sn','cr.creditnote_type','cr.use_total','cr.status','cr.chanel'))


                    ->joinLeft(array('d' => 'distributor'), 'cr.distributor_id=d.id', array(null))
                    ->joinLeft(array('dm' => 'distributor'), 'cr.main_d_id=dm.id', array('IF(cr.main_d_id IS NOT NULL,dm.id,d.id) AS distributor_id','IF(cr.main_d_id IS NOT NULL,dm.title,d.title) AS title','IF(cr.main_d_id IS NOT NULL,dm.store_code,d.store_code) AS store_code','IF(cr.main_d_id IS NOT NULL,dm.mst_sn,d.mst_sn) AS tax_no','IF(cr.main_d_id IS NOT NULL,dm.parent,d.parent) AS parent','IF(cr.main_d_id IS NOT NULL,dm.finance_group,d.finance_group) AS finance_group'))
                    ->joinLeft(array('rm'  => 'hr.regional_market'), 'd.district = rm.id', array('rm.name AS district'))
                    ->joinLeft(array('rm2' => 'hr.regional_market'), 'd.region = rm2.id', array('rm2.name AS province'))
                    ->joinLeft(array('a'   => 'hr.area'), 'rm2.area_id = a.id ', array('a.name AS area_name'));

                    //->where('cr.status = 1', null);
                    
                    if (isset($params['cn_status']) and $params['cn_status'] ==0){
                       $select->where('cr.status = 0',null); //ปิดการใช้งาน
                    }else if (isset($params['cn_status']) and $params['cn_status'] ==2){
                       //แสดงทั้งหมด
                    }else{
                       $select->where('cr.status =1',null);//เปิดใช้งาน 
                    }

                    if (isset($params['sn']) and $params['sn'])      
                       $select->where('cr.creditnote_sn LIKE ? or cr.sn LIKE ?', '%'.$params['sn'].'%');
                    
                    if (isset($params['d_id']) and $params['d_id'])      
                       $select->where('cr.distributor_id =?',$params['d_id']);

                    if (isset($params['created_at_from']) and $params['created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_from']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                    }

                    if (isset($params['created_at_to']) and $params['created_at_to']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_to']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                    }

                    if( isset($params['finance_group']) && $params['finance_group'] != '' ){
                        $select->where('d.finance_group = ?',$params['finance_group']);
                    }

                    if( isset($params['creditnote_type']) && $params['creditnote_type'] != '' ){
                        $select->where('cr.creditnote_type = ? ', $params['creditnote_type']);
                    }

                    if( isset($params['chanel']) && $params['chanel'] != '' ){
                        if($params['chanel'] == 'return'){
                            $select->where('cr.chanel is null or cr.chanel = ?', $params['chanel']);
                        }else{
                            $select->where('cr.chanel = ? ', $params['chanel']);
                        }
                    }

            $select->order('distributor_id');
            //echo $select;die;
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            //$total = 300;
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    public function getCredit_Note_list_for_export_list($page, $limit, &$total, $params)
    {
        
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');
        try{
             $userStorage = Zend_Auth::getInstance()->getStorage()->read();

             /*->from(array('cr'=> 'credit_note'),array('cr.total_amount AS total_amount','cr.balance_total AS balance_total','cr.create_date','cr.creditnote_sn','cr.creditnote_type','cr.use_total','cr.status','cr.chanel'))*/

             $db = Zend_Registry::get('db');
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note'),array(new Zend_Db_Expr('cr.creditnote_sn,cr.manual,cr.confirm_date,cr.confirm_by,cr.price_ext_vat,cr.wht_price,cr.wht_vat,cr.vat,
                          m.invoice_number,cr.creditnote_type,cr.status,cr.chanel,cr.create_date,cr.manual_remark,cr.finance_remark,
                          g.name AS product_name,
                          g.desc AS product_call_name,
                          c.name AS product_color,
                          m.price,
                          m.total AS total,
                          m.price_ext AS price_ext,
                          m.num AS Qty,
                          ROUND((m.price / 1.07), 2) AS Unit_Price,
                          ROUND(SUM(((m.price / 1.07) * m.num)), 2) AS Amount_according_to_previous_TAX_invoice,
                          ROUND(
                            SUM(
                              (
                                ((m.price / 1.07) * m.num) - (((((m.price / 1.07) * 1.07)) * m.num) / 1.07)
                              )
                            ),
                            2
                          ) AS The_correct_amount,
                          ROUND(
                            SUM(((((((m.price / 1.07) * 1.07)) * m.num) / 1.07))),
                            2
                          ) AS Difference_amount,
                          ROUND(
                            SUM(
                              (
                                (m.price * m.num) - ((m.price * m.num) / 1.07)
                              )
                            ),
                            2
                          ) AS VAT,
                          ROUND(SUM((m.price * m.num)), 2) AS total_amount ,
                          cr.total_amount as total_amount_old,
                          cr.use_total,
                          cr.balance_total,
                        ROUND(cr.total_amount,2) as sp_total_amount,     
                        ROUND(cr.use_total,2) AS sp_use_total,
                        ROUND(cr.balance_total,2) AS sp_balance_total')))
                    ->joinLeft(array('m'=>'warehouse.market'),'m.creditnote_sn = cr.creditnote_sn',array(null))
        ->joinLeft(array('g'=>'warehouse.good'),'m.good_id = g.id ',array(null))
        ->joinLeft(array('c'=>'warehouse.good_color'),'m.good_color = c.id',array(null))
                    ->joinLeft(array('d' => 'distributor'), 'cr.distributor_id=d.id', array(null))
                    ->joinLeft(array('dm' => 'distributor'), 'cr.main_d_id=dm.id', array('IF(cr.main_d_id IS NOT NULL,dm.id,d.id) AS distributor_id','IF(cr.main_d_id IS NOT NULL,dm.title,d.title) AS title','IF(cr.main_d_id IS NOT NULL,dm.store_code,d.store_code) AS store_code','IF(cr.main_d_id IS NOT NULL,dm.mst_sn,d.mst_sn) AS tax_no','IF(cr.main_d_id IS NOT NULL,dm.parent,d.parent) AS parent','IF(cr.main_d_id IS NOT NULL,dm.finance_group,d.finance_group) AS finance_group'))
                    ->joinLeft(array('rm'  => 'hr.regional_market'), 'd.district = rm.id', array('rm.name AS district'))
                    ->joinLeft(array('rm2' => 'hr.regional_market'), 'd.region = rm2.id', array('rm2.name AS province'))
                    ->joinLeft(array('a'   => 'hr.area'), 'rm2.area_id = a.id ', array('a.name AS area_name'));

                    //->where('cr.status = 1', null);
                    
                    if (isset($params['cn_status']) and $params['cn_status'] ==0){
                       $select->where('cr.status = 0',null); //ปิดการใช้งาน
                    }else if (isset($params['cn_status']) and $params['cn_status'] ==2){
                       //แสดงทั้งหมด
                    }else{
                       $select->where('cr.status =1',null);//เปิดใช้งาน 
                    }

                    if (isset($params['sn']) and $params['sn'])      
                       $select->where('cr.creditnote_sn LIKE ? or cr.sn LIKE ?', '%'.$params['sn'].'%');
                    
                    if (isset($params['d_id']) and $params['d_id'])      
                       $select->where('cr.distributor_id =?',$params['d_id']);

                    if (isset($params['created_at_from']) and $params['created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_from']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                    }

                    if (isset($params['created_at_to']) and $params['created_at_to']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_to']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                    }

                    if( isset($params['finance_group']) && $params['finance_group'] != '' ){
                        $select->where('d.finance_group = ?',$params['finance_group']);
                    }

                    if( isset($params['creditnote_type']) && $params['creditnote_type'] != '' ){
                        $select->where('cr.creditnote_type = ? ', $params['creditnote_type']);
                    }

                    if( isset($params['chanel']) && $params['chanel'] != '' ){
                        if($params['chanel'] == 'return'){
                            $select->where('cr.chanel is null or cr.chanel = ?', $params['chanel']);
                        }else{
                            $select->where('cr.chanel = ? ', $params['chanel']);
                        }
                    }

                    if($userStorage->warehouse_type !=''){
                        $warehouse_type = $userStorage->warehouse_type;
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
                    }else{
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
                    }

                    if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                        $select->where('d.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or p.warehouse_id in (71,92)',null);
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

            $select->group('cr.creditnote_sn');
            $select->order('cr.creditnote_sn');
            $select->order('m.invoice_number');
            $select->order('m.cat_id');
            $select->order('m.good_id');
            $select->order('m.good_color');
          //  echo $select;die;
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            //$total = 300;
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    public function cn_view_print($params) {

        $db          = Zend_Registry::get('db');
     
        try {
     
          $select = $db->select()
            ->from(array('op' => 'credit_note'), array('op.*', 'a.name AS area_name'
              , 'rm2.name AS province' 
              , 'rm.name AS district', 'dis.id AS d_id,dis.store_code', 'dis.title', 'dis.mst_sn'
              , 'dis.tel','dis.add AS add_tax'))
            ->joinLeft(array('dis' => 'distributor'), 'dis.id = op.distributor_id', array(''))
            ->joinLeft(array('rm'  => 'hr.regional_market'), 'dis.district = rm.id', array())
            ->joinLeft(array('rm2' => 'hr.regional_market'), 'dis.region = rm2.id', array())
            ->joinLeft(array('a'   => 'hr.area'), 'rm2.area_id = a.id ', array())  
            ->where('op.creditnote_sn = ?',$params['sn'])
            ->where('op.distributor_id = ?',$params['d_id']);
           //echo $select;    
          $result = $db->fetchRow($select);
     
          // print_r($result);die;
          return $result;
     
        } catch (exception $e) {
     
          $result = null;
     
        }
     
        return $result;
     
      }

    public function getCredit_Note_list_for_export_new($page, $limit, &$total, $params)
    {
        
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');
        try{

            $now_y = date('Y');
             $userStorage = Zend_Auth::getInstance()->getStorage()->read();
             $db = Zend_Registry::get('db');
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note'),array('cr.total_amount AS total_amount','cr.balance_total AS balance_total','cr.create_date','cr.creditnote_sn','cr.creditnote_type','cr.use_total','cr.status','cr.chanel','cr.cn_type','cr.remark',
                        '(select sum(use_discount) from credit_note_tran cnt where cnt.creditnote_sn = cr.creditnote_sn and cnt.update_date >= "'.$now_y.'-01-01 00:00:00" and cnt.update_date <= "'.$now_y.'-01-31 23:59:59") as "1",
                        (select sum(use_discount) from credit_note_tran cnt where cnt.creditnote_sn = cr.creditnote_sn and cnt.update_date >= "'.$now_y.'-02-01 00:00:00" and cnt.update_date <= "'.$now_y.'-02-31 23:59:59") as "2",
                        (select sum(use_discount) from credit_note_tran cnt where cnt.creditnote_sn = cr.creditnote_sn and cnt.update_date >= "'.$now_y.'-03-01 00:00:00" and cnt.update_date <= "'.$now_y.'-03-31 23:59:59") as "3",
                        (select sum(use_discount) from credit_note_tran cnt where cnt.creditnote_sn = cr.creditnote_sn and cnt.update_date >= "'.$now_y.'-04-01 00:00:00" and cnt.update_date <= "'.$now_y.'-04-31 23:59:59") as "4",
                        (select sum(use_discount) from credit_note_tran cnt where cnt.creditnote_sn = cr.creditnote_sn and cnt.update_date >= "'.$now_y.'-05-01 00:00:00" and cnt.update_date <= "'.$now_y.'-05-31 23:59:59") as "5",
                        (select sum(use_discount) from credit_note_tran cnt where cnt.creditnote_sn = cr.creditnote_sn and cnt.update_date >= "'.$now_y.'-06-01 00:00:00" and cnt.update_date <= "'.$now_y.'-06-31 23:59:59") as "6",
                        (select sum(use_discount) from credit_note_tran cnt where cnt.creditnote_sn = cr.creditnote_sn and cnt.update_date >= "'.$now_y.'-07-01 00:00:00" and cnt.update_date <= "'.$now_y.'-07-31 23:59:59") as "7",
                        (select sum(use_discount) from credit_note_tran cnt where cnt.creditnote_sn = cr.creditnote_sn and cnt.update_date >= "'.$now_y.'-08-01 00:00:00" and cnt.update_date <= "'.$now_y.'-08-31 23:59:59") as "8",
                        (select sum(use_discount) from credit_note_tran cnt where cnt.creditnote_sn = cr.creditnote_sn and cnt.update_date >= "'.$now_y.'-09-01 00:00:00" and cnt.update_date <= "'.$now_y.'-09-31 23:59:59") as "9",
                        (select sum(use_discount) from credit_note_tran cnt where cnt.creditnote_sn = cr.creditnote_sn and cnt.update_date >= "'.$now_y.'-10-01 00:00:00" and cnt.update_date <= "'.$now_y.'-10-31 23:59:59") as "10",
                        (select sum(use_discount) from credit_note_tran cnt where cnt.creditnote_sn = cr.creditnote_sn and cnt.update_date >= "'.$now_y.'-11-01 00:00:00" and cnt.update_date <= "'.$now_y.'-11-31 23:59:59") as "11",
                        (select sum(use_discount) from credit_note_tran cnt where cnt.creditnote_sn = cr.creditnote_sn and cnt.update_date >= "'.$now_y.'-12-01 00:00:00" and cnt.update_date <= "'.$now_y.'-12-31 23:59:59") as "12"'
                        ))
                    ->joinLeft(array('d' => 'distributor'), 'cr.distributor_id=d.id', array('d.id as distributor_id','d.title','d.store_code','d.finance_group'));

                    //->where('cr.status = 1', null);

                    if (isset($params['cn_status']) and $params['cn_status'] ==0){
                       $select->where('cr.status = 0',null); //ปิดการใช้งาน
                    }else if (isset($params['cn_status']) and $params['cn_status'] ==2){
                       //แสดงทั้งหมด
                    }else{
                       $select->where('cr.status =1',null);//เปิดใช้งาน 
                    }


                    if (isset($params['sn']) and $params['sn'])      
                       $select->where('cr.creditnote_sn LIKE ? or cr.sn LIKE ?', '%'.$params['sn'].'%');
                    
                    if (isset($params['d_id']) and $params['d_id'])      
                       $select->where('cr.distributor_id =?',$params['d_id']);

                    if (isset($params['created_at_from']) and $params['created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_from']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                    }

                    if (isset($params['created_at_to']) and $params['created_at_to']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_to']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                    }

                    if( isset($params['finance_group']) && $params['finance_group'] != '' ){
                        $select->where('d.finance_group = ?',$params['finance_group']);
                    }

                    if( isset($params['creditnote_type']) && $params['creditnote_type'] != '' ){
                        $select->where('cr.creditnote_type = ? ', $params['creditnote_type']);
                    }

                    if( isset($params['chanel']) && $params['chanel'] != '' ){
                        if($params['chanel'] == 'return'){
                            $select->where('cr.chanel is null or cr.chanel = ?', $params['chanel']);
                        }else{
                            $select->where('cr.chanel = ? ', $params['chanel']);
                        }
                    }

                    if($userStorage->warehouse_type !=''){
                        $warehouse_type = $userStorage->warehouse_type;
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
                    }else{
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
                    }

                    if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                        $select->where('d.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or p.warehouse_id in (71,92)',null);
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

            $select->order('distributor_id');
            $select->order('cr.creditnote_sn');
            // echo $select;die;
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            //$total = 300;
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }


    public function genCNByModel($params)
    {
        
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('cn'=> 'warehouse.credit_note'),array('cn.creditnote_type','cn.creditnote_sn','cn.chanel','cn.status','cn.total_amount','cn.use_total','cn.balance_total','cn.remark','cn.cn_type'))
        ->joinInner(array('mk'=>'warehouse.market'),'mk.creditnote_sn=cn.creditnote_sn AND mk.isbacks=1',array('mk.sn_ref','mk.invoice_number','mk.sn','mk.good_id','mk.good_color','mk.num'))
        ->joinLeft(array('gd'=>'warehouse.good'),'gd.id = mk.good_id',array('good_name' => 'gd.name'))
        ->joinLeft(array('gc'=>'warehouse.good_color'),'gc.id = mk.good_color',array('color' => 'gc.name'))
        ->joinLeft(array('dis'=>'warehouse.distributor'),'dis.id = mk.d_id',array('d_id'=>'dis.id','dis.store_code','dis.title','dis.finance_group'))
        ->joinLeft(array('rm'=>'hr.regional_market'),'dis.district = rm.id',array('district'=>'rm.name'))
        ->joinLeft(array('rm2'=>'hr.regional_market'),'dis.region = rm2.id',array('province'=>'rm2.name'))
        ->joinLeft(array('a'=>'hr.area'),'rm2.area_id = a.id',array('area_name'=>'a.name'));

        if (isset($params['cn_status']) and $params['cn_status'] ==0){
           $select->where('cn.status = 0',null); //ปิดการใช้งาน
        }else if (isset($params['cn_status']) and $params['cn_status'] ==2){
           //แสดงทั้งหมด
        }else{
           $select->where('cn.status =1',null);//เปิดใช้งาน 
        }

        if (isset($params['sn']) and $params['sn'])      
           $select->where('cn.creditnote_sn LIKE ? or cn.sn LIKE ?', '%'.$params['sn'].'%');
        
        if (isset($params['d_id']) and $params['d_id'])      
           $select->where('cn.distributor_id =?',$params['d_id']);

        if (isset($params['created_at_from']) and $params['created_at_from']){
            list( $day, $month, $year ) = explode('/', $params['created_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('cn.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['created_at_to']) and $params['created_at_to']){
            list( $day, $month, $year ) = explode('/', $params['created_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('cn.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if( isset($params['finance_group']) && $params['finance_group'] != '' ){
            $select->where('dis.finance_group = ?',$params['finance_group']);
        }

        if( isset($params['creditnote_type']) && $params['creditnote_type'] != '' ){
            $select->where('cn.creditnote_type = ? ', $params['creditnote_type']);
        }else{
            $select->where('cn.creditnote_type IN ("CN","CP")');
        }

        if( isset($params['chanel']) && $params['chanel'] != '' ){

            if($params['chanel'] == 'return'){
                $select->where('cn.chanel is null or cn.chanel = ?', $params['chanel']);
            }else{
                $select->where('cn.chanel = ? ', $params['chanel']);
            }
        }

        if($userStorage->warehouse_type !=''){
            $warehouse_type = $userStorage->warehouse_type;
            $select->where('dis.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
        }else{
            $select->where('dis.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
        }

        if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
            $select->where('dis.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or p.warehouse_id in (71,92)',null);
            // allow warehouse WMDGC - คลังเคลมDigital, Brandshop Warehouse at Kerry
        }

        if($userStorage->group_id !='7'){
            if($userStorage->catty_staff_id !=''){
                $catty_staff_id = $userStorage->catty_staff_id;
                $select->where('dis.region in(SELECT rm.id AS region_id
                                FROM hr.`asm` asm
                                LEFT JOIN hr.`regional_market` rm ON asm.area_id=rm.area_id
                                WHERE asm.`staff_id`=?)',$catty_staff_id);
            }
        }

        $select->order(array('cn.creditnote_sn','cn.creditnote_type','cn.create_date'));

        //echo $select;die;
        $result = $db->fetchAll($select);
        return $result;
        
    }
    public function genCNByModel12($params)
    {
        
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('cn'=> 'warehouse.credit_note'),array('cn.creditnote_type','cn.creditnote_sn','cn.chanel','cn.status','cn.total_amount as 11','cn.use_total','cn.balance_total','cn.remark','cn.cn_type'))
        ->joinInner(array('mk'=>'warehouse.market'),'mk.creditnote_sn=cn.creditnote_sn AND mk.isbacks=1',array('mk.sn_ref','mk.invoice_number','mk.sn','mk.good_id','mk.good_color','mk.num','ROUND(SUM((mk.price * mk.num)), 2) AS total_amount '))
        ->joinLeft(array('gd'=>'warehouse.good'),'gd.id = mk.good_id',array('good_name' => 'gd.name'))
        ->joinLeft(array('gc'=>'warehouse.good_color'),'gc.id = mk.good_color',array('color' => 'gc.name'))
        ->joinLeft(array('dis'=>'warehouse.distributor'),'dis.id = mk.d_id',array('d_id'=>'dis.id','dis.store_code','dis.title','dis.finance_group'))
        ->joinLeft(array('rm'=>'hr.regional_market'),'dis.district = rm.id',array('district'=>'rm.name'))
        ->joinLeft(array('rm2'=>'hr.regional_market'),'dis.region = rm2.id',array('province'=>'rm2.name'))
        ->joinLeft(array('a'=>'hr.area'),'rm2.area_id = a.id',array('area_name'=>'a.name'));

        if (isset($params['cn_status']) and $params['cn_status'] ==0){
           $select->where('cn.status = 0',null); //ปิดการใช้งาน
        }else if (isset($params['cn_status']) and $params['cn_status'] ==2){
           //แสดงทั้งหมด
        }else{
           $select->where('cn.status =1',null);//เปิดใช้งาน 
        }

        if (isset($params['sn']) and $params['sn'])      
           $select->where('cn.creditnote_sn LIKE ? or cn.sn LIKE ?', '%'.$params['sn'].'%');
        
        if (isset($params['d_id']) and $params['d_id'])      
           $select->where('cn.distributor_id =?',$params['d_id']);

        if (isset($params['created_at_from']) and $params['created_at_from']){
            list( $day, $month, $year ) = explode('/', $params['created_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('cn.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['created_at_to']) and $params['created_at_to']){
            list( $day, $month, $year ) = explode('/', $params['created_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('cn.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if( isset($params['finance_group']) && $params['finance_group'] != '' ){
            $select->where('dis.finance_group = ?',$params['finance_group']);
        }

        if( isset($params['creditnote_type']) && $params['creditnote_type'] != '' ){
            $select->where('cn.creditnote_type = ? ', $params['creditnote_type']);
        }else{
            $select->where('cn.creditnote_type IN ("CN","CP")');
        }

        if( isset($params['chanel']) && $params['chanel'] != '' ){

            if($params['chanel'] == 'return'){
                $select->where('cn.chanel is null or cn.chanel = ?', $params['chanel']);
            }else{
                $select->where('cn.chanel = ? ', $params['chanel']);
            }
        }

        $select->order(array('cn.creditnote_sn','cn.creditnote_type','cn.create_date'));

        // echo $select;die;
        $result = $db->fetchAll($select);
        return $result;
        
    }

    public function getCP_By_Imei_list($params)
    {
        
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');
        try{
             $db = Zend_Registry::get('db');
             $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                    $select = $db->select()
                    ->from(array('b'=> 'bvg_imei'),array(new Zend_Db_Expr("dis.id,dis.store_code,dis.title
                    ,dis.rank
                    ,CASE dis.rank 
                    WHEN 1 THEN '1. ORG-WDS(1)' 
                    WHEN 2 THEN '2. ORG(2)' 
                    WHEN 5 THEN '3. ORG-Dtac/Advice(5)' 
                    WHEN 6 THEN '4. ORG-Lotus/Power by(6)' 
                    WHEN 7 THEN '5. Dealer(7)' 
                    WHEN 8 THEN '6. HUB(8)' 
                    WHEN 9 THEN '7. Laos(9)' 
                    WHEN 3 THEN '8. Online and Staff(3)' 
                    WHEN 10 THEN '9. Brand Shop/Service(10)' 
                    WHEN 11 THEN '10. King Power(11)' 
                    WHEN 12 THEN '11. Jaymart(12)' 
                    WHEN 13 THEN '12. Brand Shop By Dealer(13)' 
                    WHEN 14 THEN '13. KR Dealer(14)' 
                    WHEN 15 THEN '14. TRUE(15)' 
                    ELSE '' END AS rank_name
                    ,b.imei_sn
                    ,g.desc AS good_name
                    ,gc.name AS color
                    ,b.price,b.invoice_number,b.creditnote_sn,cn.total_amount,cn.use_total,cn.balance_total,
                    cn.cn_type,
                    cn.creditnote_type,
                    cn.create_date,
                    cn.status
                    ,b.remark,j.name AS CP_type,b.joint_circular_id")))
                    ->joinLeft(array('cn'   => 'credit_note'), 'cn.creditnote_sn=b.creditnote_sn', array(null))
                    ->joinLeft(array('j'   => 'joint_circular'), 'j.id=b.joint_circular_id', array(null)) 
                    ->joinLeft(array('g'   => 'good'), 'g.id = b.good_id', array(null)) 
                    ->joinLeft(array('gc'   => 'good_color'), 'gc.id = b.good_color ', array(null)) 
                    ->joinLeft(array('dis'   => 'distributor'), 'dis.id = cn.distributor_id', array('dis.id AS d_id','dis.store_code','dis.title','dis.finance_group'))
                    ->joinLeft(array('rm'   => 'hr.regional_market'), 'dis.district = rm.id', array('rm.name AS district'))
                    ->joinLeft(array('rm2'   => 'hr.regional_market'), 'dis.region = rm2.id', array('rm2.name AS province'))
                    ->joinLeft(array('a'   => 'hr.area'), 'rm2.area_id = a.id', array('a.name AS area_name'));


                    if (isset($params['cn_status']) and $params['cn_status'] ==0){
                       $select->where('cn.status = 0',null); //ปิดการใช้งาน
                    }else if (isset($params['cn_status']) and $params['cn_status'] ==2){
                       //แสดงทั้งหมด
                    }else{
                       $select->where('cn.status =1',null);//เปิดใช้งาน 
                    }

                    if (isset($params['sn']) and $params['sn'])      
                       $select->where('cn.creditnote_sn LIKE ? or cn.sn LIKE ?', '%'.$params['sn'].'%');
                    
                    if (isset($params['d_id']) and $params['d_id'])      
                       $select->where('cn.distributor_id =?',$params['d_id']);

                    if (isset($params['created_at_from']) and $params['created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_from']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cn.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                    }

                    if (isset($params['created_at_to']) and $params['created_at_to']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_to']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cn.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                    }

                    if( isset($params['finance_group']) && $params['finance_group'] != '' ){
                        $select->where('dis.finance_group = ?',$params['finance_group']);
                    }

                    if( isset($params['creditnote_type']) && $params['creditnote_type'] != '' ){
                        $select->where('cn.creditnote_type = ? ', $params['creditnote_type']);
                    }

                    if( isset($params['chanel']) && $params['chanel'] != '' ){
                        if($params['chanel'] == 'return'){
                            $select->where('cn.chanel is null or cn.chanel = ?', $params['chanel']);
                        }else{
                            $select->where('cn.chanel = ? ', $params['chanel']);
                        }
                    }

                    if($userStorage->warehouse_type !=''){
                        $warehouse_type = $userStorage->warehouse_type;
                        $select->where('dis.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
                    }else{
                        $select->where('dis.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
                    }

                    if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                        $select->where('dis.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or p.warehouse_id in (71,92)',null);
                        // allow warehouse WMDGC - คลังเคลมDigital, Brandshop Warehouse at Kerry
                    }

                    if($userStorage->group_id !='7'){
                        if($userStorage->catty_staff_id !=''){
                            $catty_staff_id = $userStorage->catty_staff_id;
                            $select->where('dis.region in(SELECT rm.id AS region_id
                                            FROM hr.`asm` asm
                                            LEFT JOIN hr.`regional_market` rm ON asm.area_id=rm.area_id
                                            WHERE asm.`staff_id`=?)',$catty_staff_id);
                        }
                    }

                    $select->group('b.creditnote_sn');
                    $select->group('b.imei_sn');
                    $select->group('cn.distributor_id');
                    $select->group('b.sales_sn');
                    $select->group('b.good_id');
                    $select->group('b.good_color');

                    $select->order('cn.creditnote_sn');
                    

                  
                // echo $select;die;

            $result = $db->fetchAll($select);

            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    public function getCredit_Note_By_Imei_list($params)
    {
        
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');
        try{
             $db = Zend_Registry::get('db');
             $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                
                    $select = $db->select()
                    ->from(array('ir'=> 'imei_return'),array(null))
                    ->joinLeft(array('cn'   => 'credit_note'), 'cn.creditnote_sn = ir.creditnote_sn', array(new Zend_Db_Expr('ir.imei_sn,
                      cn.creditnote_sn,
                    dis.id AS d_id,
                    dis.store_code,
                    dis.title,
                    m.invoice_number,
                    i.imei_sn,
                    m.invoice_number,
                    ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100),2)AS unit_price1,
                    ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100)-IFNULL(
                          (SELECT 
                            SUM(IFNULL(price, 0)) 
                          FROM
                            bvg_imei 
                          WHERE imei_sn IN (i.imei_sn) 
                            AND d_id = m.d_id 
                            AND invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
                            AND good_id = m.good_id 
                            AND good_color = m.good_color)
                            ,
                          0
                        ),2)AS unit_price,
                        round(ifnull(
    (SELECT 
        count(imei_sn) 
      FROM
        bvg_imei 
      WHERE imei_sn IN (i.imei_sn) 
        AND `d_id` = m.`d_id` 
        AND invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
        AND good_id = m.`good_id` 
        AND good_color = m.`good_color`)
        
        ,0),2)as count_bvg_price,
                        round(ifnull(
    (SELECT 
        SUM(IFNULL(price, 0)) 
      FROM
        bvg_imei 
      WHERE imei_sn IN (i.imei_sn) 
        AND `d_id` = m.`d_id` 
        AND invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
        AND good_id = m.`good_id` 
        AND good_color = m.`good_color`)
        
        ,0),2)as sum_bvg_price,
                    g.id AS good_id,
                    c.id AS color_id,
                    g.name AS good_name,
                    g.desc AS product_detail_name,
                    c.name AS color,
                    cn.total_amount,
                      cn.use_total,
                      cn.balance_total,
                      cn.cn_type,
                      cn.remark,
                      dis.id AS d_id,
                      dis.store_code,
                      dis.title,
                      dis.finance_group,
                      rm.name AS district,
                      rm2.name AS province,
                      a.name AS area_name ,
                      cn.creditnote_type,
                      cn.create_date,
                      cn.status')))
                    ->joinLeft(array('i'   => 'imei'), 'i.imei_sn=ir.imei_sn', array(null)) 
                    ->joinLeft(array('m'   => 'market'), 'm.sn = ir.sales_order_sn AND m.good_id = i.good_id AND m.good_color = i.good_color', array(null)) 
                    ->joinLeft(array('g'   => 'good'), 'g.id = i.good_id', array(null)) 
                    ->joinLeft(array('c'   => 'good_color'), 'c.id = i.good_color ', array(null)) 
                    ->joinLeft(array('dis'   => 'distributor'), 'dis.id = cn.distributor_id', array('dis.id AS d_id','dis.store_code','dis.title','dis.finance_group'))
                    ->joinLeft(array('rm'   => 'hr.regional_market'), 'dis.district = rm.id', array('rm.name AS district'))
                    ->joinLeft(array('rm2'   => 'hr.regional_market'), 'dis.region = rm2.id', array('rm2.name AS province'))
                    ->joinLeft(array('a'   => 'hr.area'), 'rm2.area_id = a.id', array('a.name AS area_name'));


                    if (isset($params['cn_status']) and $params['cn_status'] ==0){
                       $select->where('cn.status = 0',null); //ปิดการใช้งาน
                    }else if (isset($params['cn_status']) and $params['cn_status'] ==2){
                       //แสดงทั้งหมด
                    }else{
                       $select->where('cn.status =1',null);//เปิดใช้งาน 
                    }

                    if (isset($params['sn']) and $params['sn'])      
                       $select->where('cn.creditnote_sn LIKE ? or cn.sn LIKE ?', '%'.$params['sn'].'%');
                    
                    if (isset($params['d_id']) and $params['d_id'])      
                       $select->where('cn.distributor_id =?',$params['d_id']);

                    if (isset($params['created_at_from']) and $params['created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_from']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cn.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                    }

                    if (isset($params['created_at_to']) and $params['created_at_to']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_to']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cn.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                    }

                    if( isset($params['finance_group']) && $params['finance_group'] != '' ){
                        $select->where('dis.finance_group = ?',$params['finance_group']);
                    }

                    if( isset($params['creditnote_type']) && $params['creditnote_type'] != '' ){
                        $select->where('cn.creditnote_type = ? ', $params['creditnote_type']);
                    }

                    if( isset($params['chanel']) && $params['chanel'] != '' ){
                        if($params['chanel'] == 'return'){
                            $select->where('cn.chanel is null or cn.chanel = ?', $params['chanel']);
                        }else{
                            $select->where('cn.chanel = ? ', $params['chanel']);
                        }
                    }

                    if($userStorage->warehouse_type !=''){
                        $warehouse_type = $userStorage->warehouse_type;
                        $select->where('dis.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
                    }else{
                        $select->where('dis.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
                    }

                    if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                        $select->where('dis.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or p.warehouse_id in (71,92)',null);
                        // allow warehouse WMDGC - คลังเคลมDigital, Brandshop Warehouse at Kerry
                    }

                    if($userStorage->group_id !='7'){
                        if($userStorage->catty_staff_id !=''){
                            $catty_staff_id = $userStorage->catty_staff_id;
                            $select->where('dis.region in(SELECT rm.id AS region_id
                                            FROM hr.`asm` asm
                                            LEFT JOIN hr.`regional_market` rm ON asm.area_id=rm.area_id
                                            WHERE asm.`staff_id`=?)',$catty_staff_id);
                        }
                    }

                    $select->group('ir.creditnote_sn');
                    $select->group('ir.imei_sn');
                    $select->group('i.distributor_id');
                    $select->group('i.sales_sn');
                    $select->group('i.good_id');
                    $select->group('i.good_color');

                    $select->order('cn.creditnote_sn');
                    

                  
                // echo $select;die;

            $result = $db->fetchAll($select);

            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    public function getCredit_Note_By_Imei_list1($params)
    {
        
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');
        try{
             $db = Zend_Registry::get('db');
                    $select = $db->select()
                    ->from(array('i'=> 'imei_return'),array('i.imei_sn','i.creditnote_sn'))
                    ->joinLeft(array('ir'   => 'imei'), 'ir.imei_sn=i.imei_sn', array(null))
                    ->joinLeft(array('gd'   => 'good'), 'gd.id = ir.good_id', array('gd.name AS good_name'))
                    ->joinLeft(array('gc'   => 'good_color'), 'gc.id = ir.good_color', array('gc.name AS color'))
                    ->join(array('cn'   => 'credit_note'), 'cn.creditnote_sn=i.creditnote_sn', array('cn.creditnote_type','cn.create_date','cn.status','cn.total_amount','cn.use_total','cn.balance_total','cn.cn_type','cn.remark'))
                   
                    ->joinLeft(array('dis'   => 'distributor'), 'dis.id = cn.distributor_id', array('dis.id AS d_id','dis.store_code','dis.title','dis.finance_group'))
                    ->joinLeft(array('rm'   => 'hr.regional_market'), 'dis.district = rm.id', array('rm.name AS district'))
                    ->joinLeft(array('rm2'   => 'hr.regional_market'), 'dis.region = rm2.id', array('rm2.name AS province'))
                    ->joinLeft(array('a'   => 'hr.area'), 'rm2.area_id = a.id', array('a.name AS area_name'));


                    if (isset($params['cn_status']) and $params['cn_status'] ==0){
                       $select->where('cn.status = 0',null); //ปิดการใช้งาน
                    }else if (isset($params['cn_status']) and $params['cn_status'] ==2){
                       //แสดงทั้งหมด
                    }else{
                       $select->where('cn.status =1',null);//เปิดใช้งาน 
                    }

                    if (isset($params['sn']) and $params['sn'])      
                       $select->where('cn.creditnote_sn LIKE ? or cn.sn LIKE ?', '%'.$params['sn'].'%');
                    
                    if (isset($params['d_id']) and $params['d_id'])      
                       $select->where('cn.distributor_id =?',$params['d_id']);

                    if (isset($params['created_at_from']) and $params['created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_from']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cn.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                    }

                    if (isset($params['created_at_to']) and $params['created_at_to']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_to']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cn.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                    }

                    if( isset($params['finance_group']) && $params['finance_group'] != '' ){
                        $select->where('d.finance_group = ?',$params['finance_group']);
                    }

                    if( isset($params['creditnote_type']) && $params['creditnote_type'] != '' ){
                        $select->where('cn.creditnote_type = ? ', $params['creditnote_type']);
                    }

                    if( isset($params['chanel']) && $params['chanel'] != '' ){
                        if($params['chanel'] == 'return'){
                            $select->where('cn.chanel is null or cn.chanel = ?', $params['chanel']);
                        }else{
                            $select->where('cn.chanel = ? ', $params['chanel']);
                        }
                    }

                 //echo $select;die;

            $result = $db->fetchAll($select);

            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    public function getCredit_Note_invoice_for_export_list($page, $limit, $total, $params)
    {
        
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');
        try{
             $db = Zend_Registry::get('db');
             $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note'),array('IFNULL((SELECT sm.invoice_number from market sm where sm.sn = ir.sales_order_sn group by sm.sn),(SELECT sm.invoice_number from market sm where sm.sn = bi.sales_sn group by sm.sn)) AS invoice_number','cr.total_amount AS total_amount','cr.balance_total AS balance_total','cr.create_date','cr.creditnote_sn','cr.creditnote_type','cr.use_total','cr.status','cr.chanel','cr.cn_type'))
                    ->joinLeft(array('d' => 'distributor'), 'cr.distributor_id=d.id', array('d.id as distributor_id','d.title','d.store_code','d.mst_sn as tax_no','d.parent','d.finance_group'))
                    ->joinLeft(array('rm'  => 'hr.regional_market'), 'd.district = rm.id', array('rm.name AS district'))
                    ->joinLeft(array('rm2' => 'hr.regional_market'), 'd.region = rm2.id', array('rm2.name AS province'))
                    ->joinLeft(array('a'   => 'hr.area'), 'rm2.area_id = a.id ', array('a.name AS area_name'))
                    ->joinLeft(array('ir'   => 'imei_return'), 'ir.creditnote_sn = cr.creditnote_sn', array('ir.sales_order_sn'))
                    ->joinLeft(array('bi'   => 'bvg_imei'), 'bi.creditnote_sn = cr.creditnote_sn', array('bi.sales_sn'));

                    if (isset($params['cn_status']) and $params['cn_status'] ==0){
                       $select->where('cr.status = 0',null); //ปิดการใช้งาน
                    }else if (isset($params['cn_status']) and $params['cn_status'] ==2){
                       //แสดงทั้งหมด
                    }else{
                       $select->where('cr.status =1',null);//เปิดใช้งาน 
                    }

                    if (isset($params['sn']) and $params['sn'])      
                       $select->where('cr.creditnote_sn LIKE ? or cr.sn LIKE ?', '%'.$params['sn'].'%');
                    
                    if (isset($params['d_id']) and $params['d_id'])      
                       $select->where('cr.distributor_id =?',$params['d_id']);

                    if (isset($params['created_at_from']) and $params['created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_from']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                    }

                    if (isset($params['created_at_to']) and $params['created_at_to']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_to']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                    }

                    if( isset($params['finance_group']) && $params['finance_group'] != '' ){
                        $select->where('d.finance_group = ?',$params['finance_group']);
                    }

                    if( isset($params['creditnote_type']) && $params['creditnote_type'] != '' ){
                        $select->where('cr.creditnote_type = ? ', $params['creditnote_type']);
                    }

                    if( isset($params['chanel']) && $params['chanel'] != '' ){
                        if($params['chanel'] == 'return'){
                            $select->where('cr.chanel is null or cr.chanel = ?', $params['chanel']);
                        }else{
                            $select->where('cr.chanel = ? ', $params['chanel']);
                        }
                    }

                    if($userStorage->warehouse_type !=''){
                        $warehouse_type = $userStorage->warehouse_type;
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
                    }else{
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
                    }

                    if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                        $select->where('d.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or p.warehouse_id in (71,92)',null);
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

                    $select->group(array('ir.sales_order_sn', 'bi.sales_sn'));

                    $select->order('cr.creditnote_sn');
                // echo $select;die;
            $result = $db->fetchAll($select);

            // print_r($result);die;
            $total = $db->fetchOne("select FOUND_ROWS()");
            //$total = 300;
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }


    public function getCredit_Note_list_for_export_use_cn($page, $limit, $total, $params)
    {
        
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');
        try{
             $db = Zend_Registry::get('db');
             $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                   $select = $db->select()
                    ->from(array('ct'=> 'warehouse.credit_note_tran'),array('ct.creditnote_sn',
                    'ct.use_discount','ct.distributor_id'))
                    ->joinInner(array('cr' => 'warehouse.credit_note'), 'cr.distributor_id=ct.distributor_id AND cr.creditnote_sn=ct.creditnote_sn')
                    ->joinInner(array('m'  => 'warehouse.market'), 'ct.distributor_id AND m.sn=ct.sales_order' , array('m.sn','m.sn_ref','m.invoice_number','m.pay_time'))
                    ->joinInner(array('d'  => 'warehouse.distributor'), 'd.id = cr.distributor_id', array('d.id AS D_id','d.store_code','d.title','d.mst_sn AS tax_code','d.tel','d.finance_group'))
                    ->joinLeft(array('rm'  => 'hr.regional_market'), 'd.district = rm.id', array('rm.name AS district'))
                    ->joinLeft(array('rm2' => 'hr.regional_market'), 'd.region = rm2.id', array('rm2.name AS province'))
                    ->joinLeft(array('a'   => 'hr.area'), 'rm2.area_id = a.id ', array('a.name AS area_name'))
                    ->where('1 = 1', null);

                    if (isset($params['sn']) and $params['sn'])         
                       $select->where('ct.creditnote_sn LIKE ? or cr.sn LIKE ?', '%'.$params['sn'].'%');

                    if (isset($params['cn_status']) and $params['cn_status'] ==0){
                       $select->where('cr.status = 0',null); //ปิดการใช้งาน
                    }else if (isset($params['cn_status']) and $params['cn_status'] ==2){
                       //แสดงทั้งหมด
                    }else{
                       $select->where('cr.status =1',null);//เปิดใช้งาน 
                    }
                    
                    if (isset($params['d_id']) and $params['d_id'])         
                       $select->where('ct.distributor_id =?',$params['d_id']);

                    if (isset($params['created_at_from']) and $params['created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_from']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('m.pay_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                    }

                    if (isset($params['created_at_to']) and $params['created_at_to']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_to']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('m.pay_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                    }

                  if( isset($params['finance_group']) && $params['finance_group'] != '' ){
                        $select->where('d.finance_group = ?',$params['finance_group']);
                    }

                    if( isset($params['creditnote_type']) && $params['creditnote_type'] != '' ){
                        $select->where('cr.creditnote_type = ? ', $params['creditnote_type']);
                    }

                    if( isset($params['chanel']) && $params['chanel'] != '' ){
                        if($params['chanel'] == 'return'){
                            $select->where('cr.chanel is null or cr.chanel = ?', $params['chanel']);
                        }else{
                            $select->where('cr.chanel = ? ', $params['chanel']);
                        }
                    }

                    if($userStorage->warehouse_type !=''){
                        $warehouse_type = $userStorage->warehouse_type;
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
                    }else{
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
                    }

                    if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                        $select->where('d.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or p.warehouse_id in (71,92)',null);
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

                    $select->group('m.sn');
                    $select->group('ct.creditnote_sn');
                    $select->order('pay_time');
                //echo $select;die;
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            //$total = 300;
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    public function getCredit_Note_list_distributor()
    {
        try{
             $db = Zend_Registry::get('db');
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note'),array('SUM(cr.total_amount)AS total_amount','SUM(cr.balance_total)AS balance_total','cr.create_date'))
                    ->joinLeft(array('d' => 'distributor'), 'cr.distributor_id=d.id', array('d.id as distributor_id','d.title','d.store_code','d.finance_group'))
                    ->where('cr.status = 1', null)
                    ->group('d.id')
                    ->order('cr.create_date DESC');
                    //echo $select;die;
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

   public function getCredit_Note_list($distributor_id, $params)
    {
        try{
             $db = Zend_Registry::get('db');
             $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note'),array('cr.*','creditnote_sn_show'=>'cr.creditnote_sn','cn_create_date'=>'cr.create_date','cn_update_date'=>'cr.update_date','cr.status as credit_status_chk','cr.manual'));
                    $select->joinLeft(array('d' => 'distributor'), 'cr.distributor_id=d.id', array('d.*'));
                    $select->joinLeft(array('m' => 'market'), 'm.creditnote_sn=cr.creditnote_sn COLLATE utf8_unicode_ci and m.isbacks = 1', array('m.*'));
                    
                    if (isset($params['cn_status']) and $params['cn_status'] ==0){
                       $select->where('cr.status = 0',null); //ปิดการใช้งาน
                    }else if (isset($params['cn_status']) and $params['cn_status'] ==2){
                       //แสดงทั้งหมด
                    }else{
                       $select->where('cr.status =1',null);//เปิดใช้งาน 
                    }

                    if( isset($params['creditnote_type']) && $params['creditnote_type'] != '' ){
                        $select->where('cr.creditnote_type = ? ', $params['creditnote_type']);
                    }

                    if (isset($params['sn']) and $params['sn'])         
                       $select->where('cr.creditnote_sn LIKE ? ', '%'.$params['sn'].'%');

                    $select->where('d.id = ?', $distributor_id);

                    if($userStorage->warehouse_type !=''){
                        $warehouse_type = $userStorage->warehouse_type;
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
                    }else{
                        $select->where('d.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
                    }

                    if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
                        $select->where('d.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or p.warehouse_id in (71,92)',null);
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
                    
                    $select->group('cr.creditnote_sn');
                    // echo $select;die;
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            return $result;
        } catch (Exception $e){

            return array(
                'code' => -1,
                'message' => $e->getMessage(),
            );
        }
    }

    public function getCredit_Note_View($distributor_id,$creditnote_sn,$sales_order)
    {
        try{
             $db = Zend_Registry::get('db');
                if($creditnote_sn !=''){
                   $select = $db->select()
                    ->from(array('cr'=> 'credit_note'),array('cr.*','cr.creditnote_sn as creditnote_sn_view','cr.create_date as create_date_cn_view'))
                    ->joinLeft(array('d' => 'distributor'), 'cr.distributor_id=d.id', array('d.*'))
                    ->joinLeft(array('m' => 'market'), 'm.creditnote_sn=cr.creditnote_sn COLLATE utf8_unicode_ci', array('m.*'))
                    ->joinLeft(array('g' => 'good'), 'm.good_id=g.id', array(new Zend_Db_Expr("g.name AS product_code,CONCAT(g.desc,' ',gc.name) AS product_detail")))
                    ->joinLeft(array('gc' => 'good_color'), 'm.good_color=gc.id ', array('gc.name AS color_name'))
                    ->joinLeft(array('s' => 'staff'), 'm.pay_user=s.id ', array("CONCAT(s.firstname,' ',s.lastname)AS prepared_by"))
                    ->where('cr.distributor_id = ?', $distributor_id)
                    ->where('cr.creditnote_sn = ?', $creditnote_sn)
                   // ->where('cr.status = 1', null)
                   // ->where('m.invoice_number IS NOT NULL', null)
                    ->group('cr.creditnote_sn')
                    ->group('m.invoice_number')
                    ->group('m.cat_id')
                    ->group('m.id')
                    ->group('m.good_id')
                    ->group('m.good_color')
                    ->order('m.invoice_number')
                    ->order('m.cat_id')
                    ->order('m.good_id')
                    ->order('m.good_color');
                    
                }
            //echo $select;die;   
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    

    public function getCredit_Note_list_Imei($distributor_id,$creditnote_sn)
    {
        try{
             $db = Zend_Registry::get('db');
                $select = $db->select()
                ->from(array('imr' => 'imei_return'),array('imr.*'))
                    ->joinLeft(array('m' => 'market'), 'm.sn= imr.sales_order_sn AND m.cat_id=11', array('m.invoice_number'))
                    ->joinLeft(array('im' => 'imei'), 'im.imei_sn=imr.imei_sn', array('im.imei_sn'))
                    ->joinLeft(array('g' => 'good'), 'im.good_id=g.id', array('g.name AS product_code',"CONCAT(g.desc,' ',gc.name) AS product_detail"))
                    ->joinLeft(array('gc' => 'good_color'), 'im.good_color=gc.id ', array('gc.name AS color_name'))
                    ->where('imr.creditnote_sn = ?', $creditnote_sn)
                    ->where('m.canceled != 1', null)
                    ->group('im.good_id')
                    ->group('im.good_color')
                    ->group('imr.imei_sn')
                    ->order('im.good_id');
               //echo $select;die;
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");

            // Digital Return
            if(count($result) <= 0){
                $select = $db->select()
                ->from(array('imr' => 'digital_sn_return'),array('imr.*','imr.sn as imei_sn'))
                    ->joinLeft(array('m' => 'market'), 'm.sn= imr.sales_order_sn AND m.cat_id=13', array('m.invoice_number'))
                    ->joinLeft(array('g' => 'good'), 'm.good_id=g.id', array('g.name AS product_code',"CONCAT(g.desc,' ',gc.name) AS product_detail"))
                    ->joinLeft(array('gc' => 'good_color'), 'm.good_color=gc.id ', array('gc.name AS color_name'))
                    ->where('imr.creditnote_sn = ?', $creditnote_sn)
                    ->where('m.canceled != 1', null)
                    ->group('m.good_id')
                    ->group('m.good_color')
                    ->group('imr.sn')
                    ->order('m.good_id');
               //echo $select;die;
                $result = $db->fetchAll($select);
                $total = $db->fetchOne("select FOUND_ROWS()");
            }
            
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    public function getCredit_Note($distributor_id,$creditnote_sn_list)
    {
        try{
             $db = Zend_Registry::get('db');
            // $creditnote_sn_list='CN59020008';
                if($creditnote_sn_list ==''){
                    $select = $db->select()
                    ->from(array('c' => 'credit_note'),array('total_amount' => 'total_amount','use_total' => 'use_total','balance_total' => 'balance_total','creditnote_sn' => 'creditnote_sn'))
                    ->where('c.distributor_id = ?', $distributor_id)
                    ->where('c.balance_total > ?', 0)
                    ->where('c.status = 1 ', null); 
                }else{

                    $select = $db->select()
                    ->from(array('c' => 'credit_note'),array('total_amount' => 'total_amount','use_total' => 'use_total','balance_total' => 'balance_total','creditnote_sn' => 'creditnote_sn'))
                    ->where('c.distributor_id = ?', $distributor_id)
                    ->where("c.creditnote_sn not in(".$creditnote_sn_list.")", null)
                    ->where('c.status = 1 and c.balance_total >0', null); 
                }
                
                
            //echo $select;die;
             if($creditnote_sn_list!=''){
                $result= $select;
             }

            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            // $result= $select;   
            //return $result;
            return array(
                    'code' => 1,
                    'data' => $result,
                );
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
        //return $result;

    }

    public function getCredit_Note_PaySlip($distributor_id,$creditnote_sn_list)
    {
        try{
             $db = Zend_Registry::get('db');
            // $creditnote_sn_list='CN59020008';
                if($creditnote_sn_list ==''){
                    $select = $db->select()
                    ->from(array('c' => 'credit_note'),array('total_amount' => 'total_amount','use_total' => 'use_total','balance_total' => 'balance_total','creditnote_sn' => 'creditnote_sn'))
                    ->where('c.distributor_id = ?', $distributor_id)
                    ->where('c.status = 1 and c.balance_total >0', null); 
                }else{

                    $select = $db->select()
                    ->from(array('c' => 'credit_note'),array('total_amount' => 'total_amount','use_total' => 'use_total','balance_total' => 'balance_total','creditnote_sn' => 'creditnote_sn'))
                    ->where('c.distributor_id = ?', $distributor_id)
                    ->where("c.creditnote_sn not in(".$creditnote_sn_list.")", null)
                    ->where('c.status = 1 and c.balance_total >0', null); 
                }
                
                
            //echo $select;die;
             if($creditnote_sn_list!=''){
                $result= $select;
             }

            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            // $result= $select;   
            //return $result;
            return array(
                    'code' => 1,
                    'data' => $result,
                );
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
        //return $result;

    }

    function get_cache(){
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

            $select->order(new Zend_Db_Expr('p.create_date ASC'));

            $data = $db->fetchAll($select);

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['creditnote_sn']] = $item['creditnote_sn'];
                }
            }
            $cache->save($result, $this->_name.'_cache', array(), null);
        }
        return $result;
    }


    public function getCredit_Note_By_SalesOrder($sales_order,$distributor_id)
    {
        try{
            $db = Zend_Registry::get('db');
             $select = $db->select()
                ->from(array('c' => 'credit_note_tran'),array('c.creditnote_sn','c.sales_order','c.use_discount'))
                ->joinLeft(array('cn'=>'credit_note'), 'cn.creditnote_sn = c.creditnote_sn', array('cn.balance_total','cn.total_amount'))
               // ->where('c.distributor_id = ?', $distributor_id)
                ->where('c.sales_order = ?', $sales_order);
                //echo $select;die; 
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");

            
        } catch (Exception $e){

            return array(
                'code' => -1,
                'message' => $e->getMessage(),
            );
        }
        return $result;
    }

    public function getCredit_Note_list_for_export_check_cn($page, $limit, $total, $params)
    {
        
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_error', 0);
        error_reporting('~E_ALL');
        try{
             $db = Zend_Registry::get('db');
                   $select = $db->select()
                    ->from(array('cr' => 'warehouse.credit_note'))
                    ->joinLeft(array('ct'=> 'warehouse.credit_note_tran'),'cr.distributor_id=ct.distributor_id AND cr.creditnote_sn=ct.creditnote_sn AND cr.status=1',array('ct.creditnote_sn',
                    'ct.use_discount','ct.distributor_id'))
                    ->joinLeft(array('m'  => 'warehouse.market'), 'ct.distributor_id AND m.sn=ct.sales_order' , array('m.sn','m.sn_ref','m.invoice_number','m.pay_time'))
                    ->joinLeft(array('d'  => 'warehouse.distributor'), 'd.id = cr.distributor_id', array('d.id AS D_id','d.store_code','d.title','d.mst_sn AS tax_code','d.tel','d.finance_group'))
                    ->joinLeft(array('rm'  => 'hr.regional_market'), 'd.district = rm.id', array('rm.name AS district'))
                    ->joinLeft(array('rm2' => 'hr.regional_market'), 'd.region = rm2.id', array('rm2.name AS province'))
                    ->joinLeft(array('a'   => 'hr.area'), 'rm2.area_id = a.id ', array('a.id AS area_id','a.name AS area_name'))
                    ->where('1 = 1', null);

                    if (isset($params['sn']) and $params['sn'])         
                       $select->where('ct.creditnote_sn LIKE ? or cr.sn LIKE ?', '%'.$params['sn'].'%');
                    
                    if (isset($params['d_id']) and $params['d_id'])         
                       $select->where('ct.distributor_id =?',$params['d_id']);

                    if (isset($params['created_at_from']) and $params['created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_from']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                    }

                    if (isset($params['created_at_to']) and $params['created_at_to']){
                        list( $day, $month, $year ) = explode('/', $params['created_at_to']);

                        if (isset($day) and isset($month) and isset($year) )
                            $select->where('cr.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                    }

                    $select->group('m.sn');
                    $select->group('cr.creditnote_sn');
                    $select->order(['cr.create_date','m.pay_time','cr.creditnote_sn']);
                // echo $select;die;
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            //$total = 300;
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

}