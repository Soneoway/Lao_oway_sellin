<?php
class Application_Model_CPAutoCheckImei extends Zend_Db_Table_Abstract{
    protected $_name = 'cp_auto_check_imei';

    public function check_imei_cp_auto($lot_sn=null)
    {
        $db = Zend_Registry::get('db');
            $select="SELECT cpi.lot_sn,IF(d.`finance_group`='','Dealer',IFNULL(d.finance_group,'Dealer')) AS finance_group,cpi.create_date,im.imei_sn
            ,im.distributor_id,im.type,im.good_id,im.out_date
            ,DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00')AS cp_date
            ,im.activated_date
            ,NULL AS creditnote_sn,0 AS invoice_price
            ,1 AS active_cn
            ,NOW() AS sales_confirm_date
            FROM warehouse.imei im
            INNER JOIN warehouse.distributor d ON d.id = im.distributor_id
            INNER JOIN cp_auto_check_imei cpi ON im.`good_id`=cpi.`good_id` AND cpi.finance_group= IF(d.`finance_group`='','Dealer',IFNULL(d.finance_group,'Dealer'))
            WHERE 1=1
            AND cpi.auto_imei=1 
            AND im.`type`=1
            AND cpi.auto_imei_done IS NULL 
            AND cpi.`start_import_auto_date` <= NOW() AND cpi.`start_import_auto_date`<= NOW()
            AND cpi.`status`=1";    
            //print_r($select);die; 
            $result_check = $db->fetchAll($select);
            //print_r($result_check_return);die;
            return $result_check;
    }

    public function view_imei_cp_auto($lot_sn=null)
    {
        $db = Zend_Registry::get('db');
            $select="SELECT cpi.`lot_sn`,cpi.`lot_number`
            ,cpi.`total_auto_imei`,cpi.`total_imei` as total_imei_done,(SELECT COUNT(lg.imei_sn) FROM `cp_auto_check_imei_list_log` lg WHERE lg.lot_sn=cpi.`lot_sn`) AS imei_error
            ,cpi.`finance_group`,cpi.`good_id`,cpi.`remark`,DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00')AS cp_date,cpi.`start_import_auto_date`,cpi.`import_auto_date`,cpi.auto_imei_done
            FROM cp_auto_check_imei cpi
            WHERE 1=1
            AND cpi.auto_imei=1 
            AND cpi.`start_import_auto_date` <= NOW() AND cpi.`start_import_auto_date`<= NOW()
            AND cpi.`status`=1";    
            if($lot_sn !="")
            {
                $select=" and cpi.`lot_sn`='".$lot_sn."'";
            }
            //print_r($select);die; 
            $result_check = $db->fetchAll($select);
            //print_r($result_check_return);die;
            return $result_check;
    }
    
    public function check_imei_timing_sale($imei_sn=null, $good_id=null, $cp_date=null)
    {
        $db = Zend_Registry::get('db');
            /*$select="select im.imei_sn,im.distributor_id,im.type,im.good_id,im.out_date,ts.time_add as timing_date,DATE_FORMAT('".$cp_date." 00:00:00','%Y-%m-%d 00:00:00')as cp_date
            ,im.activated_date,IF(IFNULL(ts.time_add,1)=1,1,IF(ts.time_add >= DATE_FORMAT('".$cp_date." 00:00:00','%Y-%m-%d 00:00:00'),1,0))AS check_timing_status
                FROM warehouse.imei im
                LEFT JOIN hr.timing_sale AS ts  ON ts.imei=im.imei_sn
                WHERE im.type =1
                AND ((im.`activated_date` >= DATE_FORMAT('".$cp_date." 00:00:00','%Y-%m-%d 00:00:00') or (im.`activated_date` is null)))
                AND im.out_date < DATE_FORMAT('".$cp_date." 00:00:00','%Y-%m-%d 00:00:00')
                AND im.good_id = '".$good_id."'
                AND im.imei_sn ='".$imei_sn."'";*/
            //   

            $select="SELECT im.imei_sn,im.distributor_id,d.finance_group,im.type,im.good_id,im.out_date,ts.time_add AS timing_date,DATE_FORMAT('".$cp_date." 00:00:00','%Y-%m-%d 00:00:00')AS cp_date
            ,im.activated_date
            ,IF(IFNULL(im.out_date,0)=1,1,IF(im.out_date < DATE_FORMAT('".$cp_date." 00:00:00','%Y-%m-%d 00:00:00'),1,0))AS check_out_date_status
            ,IF(IFNULL(ts.time_add,1)=1,1,IF(ts.time_add >= DATE_FORMAT('".$cp_date." 00:00:00','%Y-%m-%d 00:00:00'),1,0))AS check_timing_status
            ,IF(IFNULL(ts.time_add,0)=1,1,IF(ts.time_add >= DATE_FORMAT('".$cp_date." 00:00:00','%Y-%m-%d 00:00:00'),1,0))AS check_timing_sub_d_id_status
            ,IF(IFNULL(im.`activated_date`,1)=1,1,IF((im.`activated_date` >= DATE_FORMAT('".$cp_date." 00:00:00','%Y-%m-%d 00:00:00') OR (im.`activated_date` IS NULL)),1,0))AS check_activated_status
            FROM warehouse.imei im
            LEFT JOIN hr.timing_sale AS ts  ON ts.imei=im.imei_sn
            left join warehouse.distributor d on d.id = im.distributor_id
            WHERE 1=1
            AND im.imei_sn ='".$imei_sn."'";    
            //print_r($select);die; 
            $result_check = $db->fetchAll($select);
            //print_r($result_check_return);die;
            return $result_check;
    }

    public function check_imei_log($lot_sn=null)
    {
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        if(My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, FINANCE_CONFIRM_PRICE_PROTECTION,SALES_ADMIN_CONFIRM_PRICE_PROTECTION)) or ($exception_case and in_array
            ($userStorage->id, $exception_case))){
            $where_by_imei="";
        }else{
            $where_by_imei=" and create_by ='".$userStorage->id."'";
        }


            $select = "SELECT lot_sn,imei_sn,check_timing_status,cp_date,timing_date,remark FROM cp_auto_check_imei_list_log where lot_sn='".$lot_sn."' ".$where_by_imei." and timing_date is null";
            $result_check_imei = $db->fetchAll($select);

            $select_timing = "SELECT lot_sn,imei_sn,check_timing_status,cp_date,timing_date FROM cp_auto_check_imei_list_log where lot_sn='".$lot_sn."' ".$where_by_imei." and timing_date is not null";
            $result_check_timing = $db->fetchAll($select_timing);

            return array('check'=>1,'result_check_imei'=>$result_check_imei,'result_check_timing'=>$result_check_timing);
    }

    public function check_imei_order($imei_sn)
    {
        $db = Zend_Registry::get('db');
            $select = $db->select()
            ->from(array('i'=> 'imei'),array('i.sales_sn','i.imei_sn'))
            ->where('i.imei_sn = ?', $imei_sn);

            $result = $db->fetchAll($select);
        return $result;
    }

    public function check_imei($imei_sn=null, $distributor_id=null)
    {
        $db = Zend_Registry::get('db');
            $select = $db->select()
            ->from(array('i'=> 'imei'),array('COUNT( i.imei_sn)'))
            ->where('i.imei_sn = ?', $imei_sn)
            ->where('i.distributor_id = ?', $distributor_id);
            //->where('i.sales_sn is not null', null);
            //->where('i.out_price >0', null);

            //echo $select;die;
            $total = $db->fetchOne($select);
        return $total;
    }

    public function check_imei_lot($lot_sn=null,$imei_sn=null, $distributor_id=null)
    {
        $db = Zend_Registry::get('db');
            $select = $db->select()
            ->from(array('i'=> 'cp_auto_check_imei_list'),array('COUNT( i.imei_sn)'))
            ->where('i.imei_sn = ?', $imei_sn)
            //->where('i.distributor_id = ?', $distributor_id)
            ->where('i.creditnote_sn is null', null)
            ->where('i.lot_sn =?', $lot_sn);
            //echo $select;die;
            $total = $db->fetchOne($select);
        return $total;
    }

    public function getProtectionPriceNo_Ref($db,$sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $sn_ref="";
        try {
            $stmt = $db->query("CALL gen_running_no_ref_reward('CP',".$sn.")");
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
        }
        return $sn_ref;
    }

    public function getPCNo_Ref($db,$sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $sn_ref="";
        try {
            $stmt = $db->query("CALL gen_running_no_ref_reward('PC',".$sn.")");
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
        }
        return $sn_ref;
    }

    public function getLotNumberImei_No($sn)
    {
        $sn_ref="";
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref_reward('LOT',".$sn.")");
            $stmt->execute();
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Lot Number Imei No, please try again!');
        }
        return $sn_ref;
    }

    public function getCPLotNumberImeiCheckAction($params)
    {

        $db = Zend_Registry::get('db');
        //print_r($params);die;
        $result_check_return=null;
        try{
            $select_by_lot ="SELECT br.lot_sn,br.detail,br.lot_number,br.good_id,br.price,br.new_price,br.remark,br.sub_d_id,DATE_FORMAT(br.cp_date,'%d/%m/%Y') as cp_date,br.finance_group,br.check_cost,br.price_limit,br.no_use_spc_discount
            ,br.auto_imei,br.auto_imei_done,br.total_auto_imei,DATE_FORMAT(br.start_import_auto_date,'%d/%m/%Y') as start_import_auto_date,br.import_auto_date
            FROM cp_auto_check_imei br
            where 1=1 ";

            if (isset($params['lot_sn']) and $params['lot_sn'] and $params['lot_sn'] !=''){
                    $select_by_lot .=" and br.status=1 and br.lot_sn='".$params['lot_sn']."'";
            }
            
            $select_by_lot .=" ORDER BY br.create_date";

            //print_r($select_by_lot);die;
            $result_check_return = $db->fetchAll($select_by_lot);
            if ($result_check_return)
            {
                return $result_check_return;
            }

        }catch(exception $e){
            echo json_encode(array('error' => $e->getMessage()));
            exit;
        }
    }


    public function getImeiLotConfirm($lot_sn)
    {

        $db = Zend_Registry::get('db');
        //print_r($params);die;
        $result_check_return=null;
        try{
            $select_by_lot ="SELECT cp.total_imei,
            (SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.lot_sn=cp.`lot_sn` AND bi.sales_confirm_date IS NOT NULL)AS total_sales_confirm_imei,
            (SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.lot_sn=cp.`lot_sn` AND bi.finance_confirm_date IS NOT NULL)AS total_finance_confirm_imei
            FROM cp_auto_check_imei cp WHERE 1=1 ";

            $select_by_lot .=" and cp.status=1 and cp.lot_sn='".$lot_sn."'";

            //print_r($select_by_lot);die;
            $result_check_return = $db->fetchAll($select_by_lot);
            if ($result_check_return)
            {
                return $result_check_return;
            }

        }catch(exception $e){
            echo json_encode(array('error' => $e->getMessage()));
            exit;
        }


    }

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //print_r($params);//die;
        if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, FINANCE_CONFIRM_PRICE_PROTECTION,SALES_ADMIN_CONFIRM_PRICE_PROTECTION)) or ($exception_case and in_array
        ($userStorage->id, $exception_case))){
            $view_by_user="";
        }else{
            $view_by_user=" and create_by='".$userStorage->id."'";
            //$select->where('p.create_by = ?', $userStorage->id);
        }

        $select = $db->select();
            $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.lot_sn,(SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.lot_sn=p.`lot_sn` '.$view_by_user.')AS total_imei_by_user,(SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list_log bi WHERE bi.lot_sn=p.`lot_sn` '.$view_by_user.')AS total_error,(SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.lot_sn=p.`lot_sn` and bi.sales_confirm_date is not null '.$view_by_user.')AS total_sales_confirm_imei,(SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.lot_sn=p.`lot_sn` and bi.finance_confirm_date is not null '.$view_by_user.')AS total_finance_confirm_imei'), 'p.*','DATE_FORMAT(p.receive_date, "%Y-%m-%d")as receive_date'));
            $select->joinleft(array('s'=>'staff'),'p.create_by=s.id',array("staff_name"=>"concat(s.firstname,' ',s.lastname)",'s.email'));
            $select->joinleft(array('g'=>'good'),'p.good_id=g.id',array("good_name"=>"g.name"));

        if (isset($params['lot_number']) and $params['lot_number'] and $params['lot_number'] !='')
            $select->where('p.lot_number LIKE ?', '%'.$params['lot_number'].'%');

        if (isset($params['lot_sn']) and $params['lot_sn'] and $params['lot_sn'] !='')
            $select->where('p.lot_sn LIKE ?', '%'.$params['lot_sn'].'%');

/*        if (isset($params['view_status']) and $params['view_status'] and $params['view_status']=='1')//รอดำเนินการ
        {
            $select->where('p.total_imei >= 0 and 1 = (SELECT if(COUNT(bi.imei_sn)>=0,1,2) AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.lot_sn = p.`lot_sn` AND bi.sales_confirm_date IS NULL AND bi.finance_confirm_date IS NULL) ', null);

        }else if (isset($params['view_status']) and $params['view_status'] and $params['view_status']=='2')
        {
            $select->where(' 1 = (SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.lot_sn=p.`lot_sn` and bi.finance_confirm_date is not null) or p.total_imei=0', null);
        }*/

        if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm']=='confirm')    // Wait Admin Confirm
        {
            if (isset($params['view_status']) and $params['view_status'] and $params['view_status']=='1')//รอดำเนินการ
            {
                $select->where('p.total_imei >= 0 and (p.`lot_sn` in((SELECT distinct bi.lot_sn FROM cp_auto_check_imei_list bi WHERE bi.lot_sn = p.`lot_sn` AND bi.sales_confirm_date IS NULL AND bi.finance_confirm_date IS NULL)) or p.`lot_sn` in(SELECT distinct c.lot_sn FROM cp_auto_check_imei c where c.total_imei <=0 and c.status=1)) ', null);

            }else if (isset($params['view_status']) and $params['view_status'] and $params['view_status']=='2')
            {
                $select->where('p.total_imei > 0 and (p.`lot_sn` in((SELECT distinct bi.lot_sn FROM cp_auto_check_imei_list bi WHERE bi.lot_sn = p.`lot_sn` AND bi.sales_confirm_date IS not NULL AND bi.finance_confirm_date IS NULL))) ', null);
            }else if (isset($params['view_status']) and $params['view_status'] and $params['view_status']=='3')
            {
                $select->where('p.total_imei > 0 and (p.`lot_sn` in((SELECT distinct bi.lot_sn FROM cp_auto_check_imei_list bi WHERE bi.lot_sn = p.`lot_sn` AND bi.sales_confirm_date IS not NULL AND bi.finance_confirm_date IS not NULL))) ', null);
            }
        }

        if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm']=='finance_confirm')    //Wait Finance Confirm
        {
            if (isset($params['view_status']) and $params['view_status'] and $params['view_status']=='2')
            {
                $select->where('p.total_imei > 0 and (p.`lot_sn` in((SELECT distinct bi.lot_sn FROM cp_auto_check_imei_list bi WHERE bi.lot_sn = p.`lot_sn` AND bi.sales_confirm_date IS not NULL AND bi.finance_confirm_date IS NULL))) ', null);
            }else if (isset($params['view_status']) and $params['view_status'] and $params['view_status']=='3')
            {
                $select->where('p.total_imei > 0 and (p.`lot_sn` in((SELECT distinct bi.lot_sn FROM cp_auto_check_imei_list bi WHERE bi.lot_sn = p.`lot_sn` AND bi.sales_confirm_date IS not NULL AND bi.finance_confirm_date IS not NULL))) ', null);
            }
        }

        


        if (isset($params['distributor_name']) and $params['distributor_name']){
            $select->where('p.distributor_name = ?', $params['distributor_name']);
        }

        if (isset($params['good_id']) and $params['good_id']){
            $select->where('p.good_id = ?', $params['good_id']);
        }


        if (isset($params['start_date']) and $params['start_date']){
            list( $day, $month, $year ) = explode('/', $params['start_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $start_date = $year.'-'.$month.'-'.$day.' 00:00:00';
            }
            //$start_date = $params['start_date'];
            $select->where('p.create_date >= ?', $start_date);
        }

        if (isset($params['end_date']) and $params['end_date']){
            list( $day, $month, $year ) = explode('/', $params['end_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $end_date = $year.'-'.$month.'-'.$day.' 23:59:59';
            }
            //$end_date = $params['end_date'];
            $select->where('p.create_date <= ?', $end_date);
        }

        $select->order(['p.create_date asc']);

        $select->limitPage($page, $limit);

        //echo $select;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function getFinanceGroup()
    {
        $db = Zend_Registry::get('db');
        $select = "SELECT CASE IFNULL(finance_group,'') WHEN '' THEN 'Dealer' ELSE finance_group END AS finance_group_name FROM distributor GROUP BY (CASE IFNULL(finance_group,'') WHEN '' THEN 'Dealer' ELSE finance_group END) ORDER BY finance_group";
        $result = $db->fetchAll($select);
        return $result;
    }

    function checkPriceProtectionImeiAutoCheckListAction($lot_sn,$confirm_step,$group_by_imei)
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $result_check_return=null;
        $result_return_by_distributor=null;$result_return_by_group=null;$result_return_by_imei=null;
   
        if($confirm_step=="finance"){
            $where_confirm=" and ci.sales_confirm_date is not null";
        }else{
            $where_confirm="";
        }

        if(My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, FINANCE_CONFIRM_PRICE_PROTECTION,SALES_ADMIN_CONFIRM_PRICE_PROTECTION)) or ($exception_case and in_array
            ($userStorage->id, $exception_case))){
            $where_by_imei="";
        }else{
            $where_by_imei=" and ci.create_by ='".$userStorage->id."'";
        }

        $sql_group_by_imei="";$sql_show_imei="";$sql_show_imei_list="";
        if($group_by_imei=="1")
        {
            $sql_group_by_imei=" t.imei_sn, ";
            $sql_show_imei=" t.imei_sn,t.sub_d_id,";
            $sql_show_imei_list=" ci.sub_d_id,";
        }

        $good_id="";
        $select_good_id ="SELECT good_id FROM cp_auto_check_imei WHERE lot_sn='".$lot_sn."'";
        $res_good_id = $db->fetchRow($select_good_id);
        $good_id = $res_good_id['good_id'];

        try{

            //------------------Return Product
            /* Check Imei Return Product (Group By Distributor) */
            $select_by_distributor ="SELECT t.lot_number,t.lot_sn,".$sql_show_imei."t.distributor_id,t.good_id,
            t.store_code,t.type,t.type_name,t.spc_discount,t.sale_off_percent,
            t.title,t.rank_price,t.rank_price_name,t.margin,t.price,t.creditnote_sn
            ,ROUND(t.unit_price,2) as unit_price
            ,if((t.new_price+340)=t.invoice_price,1,2) as unit_price1
            ,t.invoice_price,t.new_price,
            COUNT(t.imei_sn) AS count_imei,
            ROUND((t.unit_price*COUNT(t.imei_sn)),2)AS cn_price
            ,t.create_date,t.active_cn
            FROM(
                SELECT   
                cp.lot_number,i.imei_sn,".$sql_show_imei_list."ci.lot_sn,
                dis.id AS distributor_id,
                dis.store_code,
                dis.title,
                COUNT(i.imei_sn) AS bvg_num,ci.creditnote_sn
                ,ifnull(cn.status,ci.active_cn) as active_cn
                ,m.sn as sales_sn,m.type,
                CASE m.type
                WHEN 1 THEN 'Normal'
                WHEN 2 THEN 'Demo'
                WHEN 5 THEN 'Apk'
                ELSE ''
                END as type_name,
                m.invoice_number, 
                IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)as spc_discount,  
                if(m.sale_off_percent>0,m.sale_off_percent,0) as sale_off_percent,
                cp.price as discount_price,
                CASE m.price_clas
                WHEN 1 THEN 
                    CASE m.d_id
                    WHEN 11293 THEN 15
                    ELSE 18
                    END
                WHEN 2 THEN 15
                WHEN 3 THEN 15
                WHEN 5 THEN 
                    CASE m.d_id
                    WHEN 44927 THEN 15
                    ELSE 17
                    END
                WHEN 6 THEN 10
                WHEN 7 THEN 15
                WHEN 8 THEN 15
                WHEN 11 THEN 20
                WHEN 12 THEN 15
                WHEN 13 THEN 15
                WHEN 14 THEN 15
                WHEN 15 THEN 12
                ELSE 0
                END as margin,
                cp.price,
                
            CASE 
            WHEN cp.check_cost =1 THEN
                CASE m.price_clas
                WHEN 1 THEN 
                    CASE m.d_id
                    WHEN 11293 THEN
                        ROUND(IF(
                            ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                            -(SELECT IFNULL(SUM(bvg.price),0) 
                            FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                            -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                            ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                            -(SELECT IFNULL(SUM(bvg.price),0) 
                            FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                            -IFNULL(cp.new_price,0),2)
                            )-
                            IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                                (IF(
                                ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                                -(SELECT IFNULL(SUM(bvg.price),0) 
                                FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                                -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                                ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                                -(SELECT IFNULL(SUM(bvg.price),0) 
                                FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                                -IFNULL(cp.new_price,0),2)
                                )*MAX(mm.spc_discount)/100)
                            ),0)
                            ,2)

                    ELSE
                        CASE m.d_id
                        WHEN 11293 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                        ELSE ROUND((cp.price-(cp.price*18)/100)-(cp.price-(cp.price*18)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*18)/100)-(cp.price-(cp.price*18)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                            END
                    END
                WHEN 2 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 3 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)    
                WHEN 5 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 6 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 7 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 8 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)

                WHEN 11 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 12 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 13 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 14 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 15 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                ELSE 0
                END  
            ELSE
                CASE m.price_clas
                WHEN 1 THEN 
                        CASE m.d_id
                    WHEN 11293 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    ELSE ROUND((cp.price-(cp.price*18)/100)-(cp.price-(cp.price*18)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*18)/100)-(cp.price-(cp.price*18)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    END
                WHEN 2 THEN 
                    CASE cp.no_use_spc_discount
                    WHEN 1 THEN
                    ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,0,0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,0,0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    ELSE
                    ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    END
                WHEN 5 THEN 
                    CASE m.d_id
                    WHEN 44927 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    ELSE ROUND((cp.price-(cp.price*17)/100)-(cp.price-(cp.price*17)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*17)/100)-(cp.price-(cp.price*17)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    END
                WHEN 6 THEN ROUND((cp.price-(cp.price*10)/100)-(cp.price-(cp.price*10)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*10)/100)-(cp.price-(cp.price*10)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 7 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 8 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 11 THEN ROUND((cp.price-(cp.price*20)/100)-(cp.price-(cp.price*20)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*20)/100)-(cp.price-(cp.price*20)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 12 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 13 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 14 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 15 THEN ROUND((cp.price-(cp.price*12)/100)-(cp.price-(cp.price*12)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*12)/100)-(cp.price-(cp.price*12)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                ELSE 0
                END  
            END as unit_price
            ,    
                SUM(IFNULL( bi.price ,0)) AS bvg_price,
                if(ci.creditnote_sn is not null,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(sum(bvg.price),0) FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id and bvg.creditnote_sn is not null)
                    /((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-if(m.sale_off_percent>0,m.sale_off_percent,0))/100),2)) AS invoice_price
                ,cp.new_price,
                m.cat_id,       
                g.id AS good_id,
                c.id AS good_color,
                g.name AS product_name,
                g.desc AS product_detail_name,
                c.name AS product_color,
                m.price_clas as rank_price,
                CASE m.price_clas
                WHEN 1 THEN 'ORG-WDS(1)'
                WHEN 2 THEN 'ORG(2)'
                WHEN 3 THEN 'Online and Staff(3)'
                WHEN 5 THEN 'ORG-Dtac/Advice(5)'
                WHEN 6 THEN 'ORG-Lotus/Power by(6)'
                WHEN 7 THEN 'Dealer(7)'
                WHEN 8 THEN 'HUB(8)'
                WHEN 9 THEN 'Laos(9)'
                WHEN 10 THEN 'Brand Shop/Service(10)'
                WHEN 11 THEN 'King Power(11)'
                WHEN 12 THEN 'Jaymart(12)'
                WHEN 13 THEN 'Brand Shop By Dealer(13)'
                WHEN 14 THEN 'KR Dealer(14)'
                WHEN 15 THEN 'TRUE(15)'
                ELSE '-'
                END as rank_price_name,ci.create_date
                FROM imei AS i 
                LEFT JOIN market AS m ON m.sn = i.sales_sn AND m.good_id = i.good_id AND m.good_color = i.good_color 
                LEFT JOIN market AS mm ON mm.sn = i.sales_sn 
                LEFT JOIN bvg_imei bi ON bi.imei_sn=i.imei_sn AND bi.d_id = m.d_id 
                AND bi.invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
                AND bi.good_id = m.good_id 
                AND bi.good_color = m.good_color
                LEFT JOIN good AS g ON g.id = i.good_id
                LEFT JOIN good_color AS c  ON c.id = i.good_color  
                LEFT JOIN distributor AS dis  ON dis.id = i.distributor_id
                inner join cp_auto_check_imei_list ci on ci.imei_sn=i.imei_sn
                LEFT JOIN cp_auto_check_imei AS cp  ON cp.lot_sn = ci.lot_sn
                LEFT JOIN credit_note AS cn  ON cn.lot_sn = ci.lot_sn and cn.creditnote_sn=ci.creditnote_sn
                inner join (
                    SELECT im.imei_sn,im.distributor_id,im.type,im.good_id,im.out_date,ts.time_add AS timing_date,DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00')AS cp_date
                    ,im.activated_date
                    ,IF(IFNULL(im.out_date,0)=1,1,IF(im.out_date < DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00'),1,0))AS check_out_date_status
                    ,IF(IFNULL(ts.time_add,1)=1,1,IF(ts.time_add >= DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00'),1,0))AS check_timing_status
                    ,IF(IFNULL(im.`activated_date`,1)=1,1,IF((im.`activated_date` >= DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00') OR (im.`activated_date` IS NULL)),1,0))AS check_activated_status
                    FROM warehouse.imei im
                    LEFT JOIN hr.timing_sale AS ts  ON ts.imei=im.imei_sn
                    inner join cp_auto_check_imei_list cpi on cpi.imei_sn=im.imei_sn
                    WHERE 1=1
                    and cpi.lot_sn='".$lot_sn."'
                ) as tms on tms.imei_sn =i.imei_sn
                WHERE 1=1 and i.distributor_id is not null
                and tms.check_out_date_status=1
                and tms.check_timing_status=1
                and tms.check_activated_status=1
                and ci.lot_sn='".$lot_sn."' ".$where_confirm. " ".$where_by_distributor." 
                

                GROUP BY i.distributor_id,m.spc_discount,m.sale_off_percent,ci.creditnote_sn,m.type,m.price_clas,i.sales_sn,i.good_id,i.good_color,i.imei_sn
                ORDER BY m.d_id,m.invoice_number
            )t
            GROUP BY ".$sql_group_by_imei." t.distributor_id,t.spc_discount,t.sale_off_percent,t.unit_price,t.invoice_price,t.creditnote_sn,t.type,t.rank_price
            ORDER BY t.creditnote_sn,t.create_date desc,t.distributor_id,t.invoice_number,t.unit_price";
            
            //AND im.good_id = cpi.good_id
            //echo $select_by_distributor;die;

            /* Check Imei Return Product (Group By Imei)*/
            $select_by_imei ="SELECT   
            cp.lot_number,i.imei_sn,
            (SELECT IFNULL(COUNT(bvg.imei_sn),0)+1 FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id and bvg.creditnote_sn is not null) as seq,
            dis.id AS distributor_id,
            dis.store_code,
            dis.title,
            COUNT(i.imei_sn) AS bvg_num,ci.creditnote_sn,ci.active_cn,ci.sub_d_id,
            m.sn as sales_sn,m.type,
            CASE m.type
            WHEN 1 THEN 'Normal'
            WHEN 2 THEN 'Demo'
            WHEN 5 THEN 'Apk'
            ELSE ''
            END as type_name,
            m.invoice_number,m.invoice_time, 
            IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0) as spc_discount,  
            if(m.sale_off_percent>0,m.sale_off_percent,0) as sale_off_percent,
            cp.price as discount_price,
            CASE m.price_clas
            WHEN 1 THEN 
                CASE m.d_id
                WHEN 11293 THEN 15
                ELSE 18
                END
            WHEN 2 THEN 15
            WHEN 3 THEN 15
            WHEN 5 THEN 
                CASE m.d_id
                WHEN 44927 THEN 15
                ELSE 17
                END
            WHEN 6 THEN 10
            WHEN 7 THEN 15
            WHEN 8 THEN 15
            WHEN 11 THEN 20
            WHEN 12 THEN 15
            WHEN 13 THEN 15
            WHEN 14 THEN 15
            WHEN 15 THEN 12
            ELSE 0
            END as margin,
            cp.price,
            CASE 
            WHEN cp.check_cost =1 THEN
                CASE m.price_clas
                WHEN 1 THEN 
                    CASE m.d_id
                    WHEN 11293 THEN
                        ROUND(IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )-
                        IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                            (IF(
                            ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                            -(SELECT IFNULL(SUM(bvg.price),0) 
                            FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                            -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                            ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                            -(SELECT IFNULL(SUM(bvg.price),0) 
                            FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                            -IFNULL(cp.new_price,0),2)
                            )*MAX(mm.spc_discount)/100)
                        ),0)
                        ,2)
                    ELSE
                        CASE m.d_id
                        WHEN 11293 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                        ELSE ROUND((cp.price-(cp.price*18)/100)-(cp.price-(cp.price*18)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*18)/100)-(cp.price-(cp.price*18)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                        END
                    END
                WHEN 2 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 3 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)    
                WHEN 5 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 6 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 7 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 8 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 11 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 12 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 13 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 14 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 15 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                ELSE 0
                END
            ELSE
                CASE m.price_clas
                WHEN 1 THEN 
                    CASE m.d_id
                    WHEN 11293 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    ELSE ROUND((cp.price-(cp.price*18)/100)-(cp.price-(cp.price*18)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*18)/100)-(cp.price-(cp.price*18)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    END
                WHEN 2 THEN 
                    CASE cp.no_use_spc_discount
                    WHEN 1 THEN
                    ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,0,0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,0,0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    ELSE
                    ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    END
                WHEN 3 THEN 
                    CASE cp.no_use_spc_discount
                    WHEN 1 THEN
                    ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,0,0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,0,0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    ELSE
                    ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    END    
                WHEN 5 THEN 
                    CASE m.d_id
                    WHEN 44927 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    ELSE ROUND((cp.price-(cp.price*17)/100)-(cp.price-(cp.price*17)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*17)/100)-(cp.price-(cp.price*17)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    END
                WHEN 6 THEN ROUND((cp.price-(cp.price*10)/100)-(cp.price-(cp.price*10)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*10)/100)-(cp.price-(cp.price*10)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 7 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 8 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 11 THEN ROUND((cp.price-(cp.price*20)/100)-(cp.price-(cp.price*20)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*20)/100)-(cp.price-(cp.price*20)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 12 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 13 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 14 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 15 THEN ROUND((cp.price-(cp.price*12)/100)-(cp.price-(cp.price*12)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*12)/100)-(cp.price-(cp.price*12)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                ELSE 0
                END
            END
            as unit_price,
            SUM(IFNULL( bi.price ,0)) AS bvg_price,
            if(ci.creditnote_sn is not null,ci.invoice_price,ROUND((IFNULL(m.price,0))-(SELECT IFNULL(sum(bvg.price),0) FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id and bvg.creditnote_sn is not null)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-if(m.sale_off_percent>0,m.sale_off_percent,0))/100),2)) AS invoice_price,cp.new_price,
            m.cat_id,       
            g.id AS good_id,
            c.id AS good_color,
            g.name AS product_name,
            g.desc AS product_detail_name,
            c.name AS product_color,
            m.price_clas as rank_price,
            CASE m.price_clas
            WHEN 1 THEN 'ORG-WDS(1)'
            WHEN 2 THEN 'ORG(2)'
            WHEN 3 THEN 'Online and Staff(3)'
            WHEN 5 THEN 'ORG-Dtac/Advice(5)'
            WHEN 6 THEN 'ORG-Lotus/Power by(6)'
            WHEN 7 THEN 'Dealer(7)'
            WHEN 8 THEN 'HUB(8)'
            WHEN 9 THEN 'Laos(9)'
            WHEN 10 THEN 'Brand Shop/Service(10)'
            WHEN 11 THEN 'King Power(11)'
            WHEN 12 THEN 'Jaymart(12)'
            WHEN 13 THEN 'Brand Shop By Dealer(13)'
            WHEN 14 THEN 'KR Dealer(14)'
            WHEN 15 THEN 'TRUE(15)'
            ELSE '-'
            END as rank_price_name
            ,tms.check_out_date_status
            ,tms.check_timing_status
            ,tms.check_activated_status
            ,tms.out_date
            ,tms.timing_date
            ,tms.activated_date
            ,tms.cp_date

            FROM imei AS i 
            LEFT JOIN market AS m ON m.sn = i.sales_sn AND m.good_id = i.good_id AND m.good_color = i.good_color
            LEFT JOIN market AS mm ON mm.sn = i.sales_sn 
            LEFT JOIN bvg_imei bi ON bi.imei_sn=i.imei_sn AND bi.d_id = m.d_id 
            AND bi.invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
            AND bi.good_id = m.good_id 
            AND bi.good_color = m.good_color
            LEFT JOIN good AS g ON g.id = i.good_id
            LEFT JOIN good_color AS c  ON c.id = i.good_color  
            LEFT JOIN distributor AS dis  ON dis.id = i.distributor_id
            inner join cp_auto_check_imei_list ci on ci.imei_sn=i.imei_sn
            LEFT JOIN cp_auto_check_imei AS cp  ON cp.lot_sn = ci.lot_sn
            inner join (
                SELECT im.imei_sn,im.distributor_id,im.type,im.good_id,im.out_date,ts.time_add AS timing_date,DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00')AS cp_date
                ,im.activated_date
                ,IF(IFNULL(im.out_date,0)=1,1,IF(im.out_date < DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00'),1,0))AS check_out_date_status
                ,IF(IFNULL(ts.time_add,1)=1,1,IF(ts.time_add >= DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00'),1,0))AS check_timing_status
                ,IF(IFNULL(im.`activated_date`,1)=1,1,IF((im.`activated_date` >= DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00') OR (im.`activated_date` IS NULL)),1,0))AS check_activated_status
                FROM warehouse.imei im
                LEFT JOIN hr.timing_sale AS ts  ON ts.imei=im.imei_sn
                inner join cp_auto_check_imei_list cpi on cpi.imei_sn=im.imei_sn
                WHERE 1=1
                and cpi.lot_sn='".$lot_sn."'
            ) as tms on tms.imei_sn =i.imei_sn

            WHERE 1=1 
            and ci.lot_sn='".$lot_sn."'  ".$where_confirm." ".$where_by_imei."
            and cp.status=1 and i.distributor_id is not null

            GROUP BY i.distributor_id,m.spc_discount,m.sale_off_percent,ci.creditnote_sn,m.type,m.price_clas,i.sales_sn,i.good_id,i.good_color,i.imei_sn
            ORDER BY i.distributor_id,m.invoice_number";
            //AND im.good_id = cp.good_id
            //echo $select_by_distributor;die;
            //echo $select_by_imei;die;
            $result_by_distributor = $db->fetchAll($select_by_distributor);
            if ($result_by_distributor)
            {
                $result_by_imei = $db->fetchAll($select_by_imei);
            }

            if ($result_by_distributor)
            {
                return array('check'=>1,'result_by_distributor'=>$result_by_distributor,'result_by_imei'=>$result_by_imei);
                exit;
            }

        }catch(exception $e)
        {
            echo json_encode(array('error' => $e->getMessage()));
            exit;
        }
    }


    function ImeiAutoCheckList($params)
    {
        
        //print_r($params);die;
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $result_check_return=null;
        $result_return_by_distributor=null;$result_return_by_group=null;$result_return_by_imei=null;
   
        if(My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, FINANCE_CONFIRM_PRICE_PROTECTION,SALES_ADMIN_CONFIRM_PRICE_PROTECTION)) or ($exception_case and in_array
            ($userStorage->id, $exception_case))){
            $where_by_imei="";
        }else{
            $where_by_imei=" and ci.create_by ='".$userStorage->id."'";
        }
        $good_id="";
        if (isset($params['lot_number']) and $params['lot_number'] and $params['lot_number'] !=''){
            $select_good_id ="SELECT good_id FROM cp_auto_check_imei WHERE lot_number='".$params['lot_number']."'";
            $res_good_id = $db->fetchRow($select_good_id);
            $good_id = $res_good_id['good_id'];
        }else{
            $good_id="339";
        }
        
        //echo $where_by_imei;die;
        try{

            /* Check Imei Return Product (Group By Imei)*/
            $select_by_imei ="SELECT   
            cp.lot_number,i.imei_sn,cp.remark,cp.create_date
            ,DATE_FORMAT(cp.cp_date,'%d/%m/%Y') as cp_date,
            dis.id AS distributor_id,
            dis.store_code,dis.finance_group
            ,CASE dis.group_id
            WHEN 10 THEN 'Brand Shop (10)'
            WHEN 11 THEN 'Brand Shop By Dealer (11)'
            WHEN 12 THEN 'Brand Shop-ORG (12)'
            WHEN 13 THEN 'Brand Shop by KR Dealer (13)'
            WHEN 1 THEN 'Dealer and Hub (1)'
            WHEN 8 THEN 'Digital (8)'
            WHEN 7 THEN 'Export (7)'
            WHEN 3 THEN 'KA(ORG) (3)'
            WHEN 2 THEN 'KR-Dealer (2)'
            WHEN 5 THEN 'Online (5)'
            WHEN 4 THEN 'Operator (4)'
            WHEN 9 THEN 'Service Shop (9)'
            WHEN 6 THEN 'Staff (6)'
            ELSE ''
            END AS distributor_group_name
            ,dis.title
            ,a.name as area_name
            ,COUNT(i.imei_sn) AS bvg_num,ci.creditnote_sn,ci.creditnote_date
            ,ci.finance_confirm_date
            ,ci.create_by
            ,concat(sa.firstname,' ',sa.lastname) as admin_create_by_name
            ,ci.finance_confirm_by
            ,concat(sf.firstname,' ',sf.lastname) as finance_confirm_by_name
            ,ci.active_cn,ci.sub_d_id
            ,(SELECT sd.title FROM distributor sd WHERE  sd.id=ci.sub_d_id)as sub_distributor_name
            ,m.sn as sales_sn,m.type,
            CASE m.type
            WHEN 1 THEN 'Normal'
            WHEN 2 THEN 'Demo'
            WHEN 5 THEN 'Apk'
            ELSE ''
            END as type_name,
            CASE ci.active_cn
            WHEN 1 THEN 'Y'
            ELSE ''
            END as active_name,
            m.invoice_number,m.invoice_time, 
            m.sale_off_percent,
            IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0) as spc_discount, 
            if(m.sale_off_percent>0,m.sale_off_percent,0) as sale_off_percent, 
            cp.price as discount_price,
            CASE m.price_clas
            WHEN 1 THEN 
                CASE m.d_id
                WHEN 11293 THEN 15
                ELSE 18
                END
            WHEN 2 THEN 15
            WHEN 3 THEN 15
            WHEN 5 THEN 
                CASE m.d_id
                WHEN 44927 THEN 15
                ELSE 17
                END
            WHEN 6 THEN 10
            WHEN 7 THEN 15
            WHEN 8 THEN 15
            WHEN 11 THEN 20
            WHEN 12 THEN 15
            WHEN 13 THEN 15
            WHEN 14 THEN 15
            WHEN 15 THEN 12
            ELSE 0
            END as margin,
            cp.price,
            CASE 
            WHEN cp.check_cost =1 THEN
                CASE m.price_clas
                WHEN 1 THEN 
                    CASE m.d_id
                    WHEN 11293 THEN
                        ROUND(IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )-
                        IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                            (IF(
                            ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                            -(SELECT IFNULL(SUM(bvg.price),0) 
                            FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                            -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                            ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                            -(SELECT IFNULL(SUM(bvg.price),0) 
                            FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                            -IFNULL(cp.new_price,0),2)
                            )*MAX(mm.spc_discount)/100)
                        ),0)
                        ,2)
                    ELSE
                        CASE m.d_id
                        WHEN 11293 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                        ELSE ROUND((cp.price-(cp.price*18)/100)-(cp.price-(cp.price*18)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*18)/100)-(cp.price-(cp.price*18)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                        END
                    END
                WHEN 2 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 3 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)    
                WHEN 5 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 6 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 7 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 8 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 11 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 12 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 13 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 14 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                WHEN 15 THEN 
                    ROUND(IF(
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                    ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                    -(SELECT IFNULL(SUM(bvg.price),0) 
                    FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                    -IFNULL(cp.new_price,0),2)
                    )-
                    IFNULL(IF(MAX(mm.spc_discount)<=0,0,
                        (IF(
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)>cp.price_limit,cp.price_limit,
                        ROUND(IF(ci.creditnote_sn IS NOT NULL,ci.invoice_price,ROUND((IFNULL(m.price,0))
                        -(SELECT IFNULL(SUM(bvg.price),0) 
                        FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id AND bvg.creditnote_sn IS NOT NULL)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-IF(m.sale_off_percent>0,m.sale_off_percent,0))/100),2))
                        -IFNULL(cp.new_price,0),2)
                        )*MAX(mm.spc_discount)/100)
                    ),0)
                    ,2)
                ELSE 0
                END
            ELSE
                CASE m.price_clas
                WHEN 1 THEN 
                    CASE m.d_id
                    WHEN 11293 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    ELSE ROUND((cp.price-(cp.price*18)/100)-(cp.price-(cp.price*18)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*18)/100)-(cp.price-(cp.price*18)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    END
                WHEN 2 THEN 
                    CASE cp.no_use_spc_discount
                    WHEN 1 THEN
                    ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,0,0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,0,0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    ELSE
                    ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    END
                WHEN 3 THEN 
                    CASE cp.no_use_spc_discount
                    WHEN 1 THEN
                    ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,0,0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,0,0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    ELSE
                    ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    END    
                WHEN 5 THEN 
                    CASE m.d_id
                    WHEN 44927 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    ELSE ROUND((cp.price-(cp.price*17)/100)-(cp.price-(cp.price*17)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*17)/100)-(cp.price-(cp.price*17)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                    END
                WHEN 6 THEN ROUND((cp.price-(cp.price*10)/100)-(cp.price-(cp.price*10)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*10)/100)-(cp.price-(cp.price*10)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 7 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 8 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 11 THEN ROUND((cp.price-(cp.price*20)/100)-(cp.price-(cp.price*20)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*20)/100)-(cp.price-(cp.price*20)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 12 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 13 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 14 THEN ROUND((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*15)/100)-(cp.price-(cp.price*15)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                WHEN 15 THEN ROUND((cp.price-(cp.price*12)/100)-(cp.price-(cp.price*12)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100,2)-ROUND(((cp.price-(cp.price*12)/100)-(cp.price-(cp.price*12)/100)*IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0)/100)*IF(m.sale_off_percent>0,m.sale_off_percent,0)/100,2)
                ELSE 0
                END
            END
            as unit_price,
            
            SUM(IFNULL( bi.price ,0)) AS bvg_price,
            if(ci.creditnote_sn is not null,ci.invoice_price,ROUND((IFNULL(m.price,0))-(SELECT IFNULL(sum(bvg.price),0) FROM bvg_imei bvg WHERE 1=1 AND bvg.imei_sn=i.imei_sn AND bvg.d_id=i.distributor_id and bvg.creditnote_sn is not null)/((100-IF(mm.total_spc_discount>0,MAX(mm.spc_discount),0))/100)/((100-if(m.sale_off_percent>0,m.sale_off_percent,0))/100),2)) AS invoice_price,cp.new_price,
            m.cat_id,       
            g.id AS good_id,
            c.id AS good_color,
            g.name AS product_name,
            g.desc AS product_detail_name,
            c.name AS product_color,
            m.price_clas as rank_price,
            CASE m.price_clas
            WHEN 1 THEN 'ORG-WDS(1)'
            WHEN 2 THEN 'ORG(2)'
            WHEN 3 THEN 'Online and Staff(3)'
            WHEN 5 THEN 'ORG-Dtac/Advice(5)'
            WHEN 6 THEN 'ORG-Lotus/Power by(6)'
            WHEN 7 THEN 'Dealer(7)'
            WHEN 8 THEN 'HUB(8)'
            WHEN 9 THEN 'Laos(9)'
            WHEN 10 THEN 'Brand Shop/Service(10)'
            WHEN 11 THEN 'King Power(11)'
            WHEN 12 THEN 'Jaymart(12)'
            WHEN 13 THEN 'Brand Shop By Dealer(13)'
            WHEN 14 THEN 'KR Dealer(14)'
            WHEN 15 THEN 'TRUE(15)'
            ELSE '-'
            END as rank_price_name
            ,tms.check_out_date_status
            ,tms.check_timing_status
            ,tms.check_activated_status
            ,tms.out_date
            ,tms.timing_date
            ,tms.activated_date
            ,tms.cp_date
            FROM imei AS i 
            LEFT JOIN market AS m ON m.sn = i.sales_sn AND m.good_id = i.good_id AND m.good_color = i.good_color 
            LEFT JOIN market AS mm ON mm.sn = i.sales_sn
            LEFT JOIN bvg_imei bi ON bi.imei_sn=i.imei_sn AND bi.d_id = m.d_id 
            AND bi.invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
            AND bi.good_id = m.good_id 
            AND bi.good_color = m.good_color
            LEFT JOIN good AS g ON g.id = i.good_id
            LEFT JOIN good_color AS c  ON c.id = i.good_color  
            LEFT JOIN distributor AS dis  ON dis.id = i.distributor_id
            inner join cp_auto_check_imei_list ci on ci.imei_sn=i.imei_sn
            LEFT JOIN cp_auto_check_imei AS cp  ON cp.lot_sn = ci.lot_sn
            LEFT JOIN staff AS sf  ON sf.id = ci.finance_confirm_by  
            LEFT JOIN staff AS sa  ON sa.id = ci.create_by 
            LEFT JOIN hr.regional_market r ON dis.region = r.id
            LEFT JOIN hr.area a ON a.id = r.area_id
            inner join (
                SELECT cpi.lot_sn,im.imei_sn,im.distributor_id,im.type,im.good_id,im.out_date,ts.time_add AS timing_date,DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00')AS cp_date
                    ,im.activated_date
                    ,IF(IFNULL(im.out_date,0)=1,1,IF(im.out_date < DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00'),1,0))AS check_out_date_status
                    ,IF(IFNULL(ts.time_add,1)=1,1,IF(ts.time_add >= DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00'),1,0))AS check_timing_status
                    ,IF(IFNULL(im.`activated_date`,1)=1,1,IF((im.`activated_date` >= DATE_FORMAT(cpi.cp_date,'%Y-%m-%d 00:00:00') OR (im.`activated_date` IS NULL)),1,0))AS check_activated_status
                    FROM warehouse.imei im
                    LEFT JOIN hr.timing_sale AS ts  ON ts.imei=im.imei_sn
                    inner join cp_auto_check_imei_list cpi on cpi.imei_sn=im.imei_sn
                    WHERE 1=1
                    
                ) as tms on tms.imei_sn =i.imei_sn and tms.lot_sn=cp.lot_sn

            WHERE 1=1 
            and cp.status=1
            ";

            if (isset($params['lot_number']) and $params['lot_number'] and $params['lot_number'] !=''){
                $select_by_imei .=" and cp.lot_number LIKE '%".$params['lot_number']."%' ";
            }else if (isset($params['view_status']) and $params['view_status'] and $params['view_status']=='1')
            {
                $select_by_imei .=" and cp.total_imei <> ((SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.lot_sn=cp.`lot_sn` and bi.finance_confirm_date is not null) or cp.total_imei=0)";
            }else if (isset($params['view_status']) and $params['view_status'] and $params['view_status']=='2')
            {
                $select_by_imei .=" and cp.total_imei = ((SELECT COUNT(bi.imei_sn)AS count_imei FROM cp_auto_check_imei_list bi WHERE bi.lot_sn=cp.`lot_sn` and bi.finance_confirm_date is not null) or cp.total_imei=0)";
            }


            if (isset($params['good_id']) and $params['good_id']){
                $select_by_imei .=" and cp.good_id = '".$params['good_id']."' ";
            }


            if (isset($params['start_date']) and $params['start_date']){
                list( $day, $month, $year ) = explode('/', $params['start_date']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $start_date = $year.'-'.$month.'-'.$day.' 00:00:00';
                }
                
                $select_by_imei .=" and cp.create_date >= '".$start_date."' ";

            }

            if (isset($params['end_date']) and $params['end_date']){
                list( $day, $month, $year ) = explode('/', $params['end_date']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $end_date = $year.'-'.$month.'-'.$day.' 23:59:59';
                }
                $select_by_imei .=" and cp.create_date <= '".$end_date."' ";
            }

            $select_by_imei .= $where_by_imei;

            $select_by_imei .=" 
            

            GROUP BY i.distributor_id,ci.creditnote_sn,m.type,m.price_clas,i.sales_sn,i.good_id,i.good_color,i.imei_sn
            ORDER BY m.d_id,ci.creditnote_sn,m.invoice_number";
            //AND im.good_id = ci.good_id
            //echo $select_by_imei;die;

            $result_by_imei = $db->fetchAll($select_by_imei);

            /*----------------Log Error---------------------*/
            $select_by_imei_log ="SELECT   
            cp.lot_number,ci.imei_sn,
            dis.id AS distributor_id,
            dis.store_code,dis.finance_group,
            dis.title,
            CASE i.type
            WHEN 1 THEN 'Normal'
            WHEN 2 THEN 'Demo'
            WHEN 5 THEN 'Apk'
            ELSE ''
            END as type_name,
            cp.remark,cp.create_date,cp.sub_d_id
            ,DATE_FORMAT(cp.cp_date,'%d/%m/%Y') as cp_date
            ,DATE_FORMAT(ci.timing_date,'%d/%m/%Y') as timing_date
            ,concat(sa.firstname,' ',sa.lastname) as admin_create_by_name
            ,ci.remark
            ,g.id AS good_id,
            c.id AS good_color,
            g.name AS product_name,
            g.desc AS product_detail_name,
            c.name AS product_color

            from cp_auto_check_imei_list_log ci
            LEFT JOIN cp_auto_check_imei AS cp  ON cp.lot_sn = ci.lot_sn
            LEFT JOIN imei AS i ON ci.imei_sn = i.imei_sn
            LEFT JOIN good AS g ON g.id = i.good_id
            LEFT JOIN good_color AS c  ON c.id = i.good_color 
            LEFT JOIN distributor AS dis  ON dis.id = i.distributor_id
            LEFT JOIN staff AS sa  ON sa.id = ci.create_by 
            where 1=1
            ";

            if (isset($params['lot_number']) and $params['lot_number'] and $params['lot_number'] !=''){
                $select_by_imei_log .=" and cp.lot_number LIKE '%".$params['lot_number']."%' ";
            }

            if (isset($params['good_id']) and $params['good_id']){
                $select_by_imei_log .=" and cp.good_id = '".$params['good_id']."' ";
            }


            if (isset($params['start_date']) and $params['start_date']){
                list( $day, $month, $year ) = explode('/', $params['start_date']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $start_date = $year.'-'.$month.'-'.$day.' 00:00:00';
                }
                
                $select_by_imei_log .=" and cp.create_date >= '".$start_date."' ";

            }

            if (isset($params['end_date']) and $params['end_date']){
                list( $day, $month, $year ) = explode('/', $params['end_date']);
                list( $year,$time ) = explode(' ', $year);

                if (isset($day) and isset($month) and isset($year) ){
                    $end_date = $year.'-'.$month.'-'.$day.' 23:59:59';
                }
                $select_by_imei_log .=" and cp.create_date <= '".$end_date."' ";
            }

            $select_by_imei_log .= $where_by_imei;

            $select_by_imei_log .=" order by cp.lot_number,cp.create_date";
            //echo $select_by_imei_log;die;
            $result_by_imei_log = $db->fetchAll($select_by_imei_log);

            if ($result_by_imei || $result_by_imei_log)
            {
                return array('check'=>1,'result_by_imei'=>$result_by_imei,'result_by_imei_log'=>$result_by_imei_log);
                exit;
            }

        }catch(exception $e)
        {
            echo json_encode(array('error' => $e->getMessage()));
            exit;
        }
    }


    function ImeiAutoCheckRegion($imei_sn)
    {
        
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $result_by_imei=null;
   
        if($userStorage->warehouse_type !=''){
            $warehouse_type = $userStorage->warehouse_type;
            $where_by_imei =' and i.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))';
        }else{
            $where_by_imei =' and i.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)';
        }

        if($userStorage->group_id !='7'){
            if($userStorage->catty_staff_id !=''){
                $catty_staff_id = $userStorage->catty_staff_id;
                $where_by_imei .=' and dis.region in(SELECT rm.id AS region_id
                                FROM hr.`asm` asm
                                LEFT JOIN hr.`regional_market` rm ON asm.area_id=rm.area_id
                                WHERE asm.`staff_id`='.$catty_staff_id.')';

            }
        }

        try{

            $sql_by_imei ="select i.imei_sn FROM imei AS i 
                                    inner JOIN distributor AS dis ON dis.id = i.distributor_id 
                                    where 1=1 and i.imei_sn='".$imei_sn."'";   
            $sql_by_imei .= $where_by_imei;
            
            //echo $sql_by_imei;die;
            $result_by_imei = $db->fetchAll($sql_by_imei);

            if ($result_by_imei)
            {
                return array('check'=>1,'result_by_imei_region'=>$result_by_imei);
                exit;
            }

        }catch(exception $e)
        {
            echo json_encode(array('error' => $e->getMessage()));
            exit;
        }
    }
  
}