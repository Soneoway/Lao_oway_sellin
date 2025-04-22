<?php
class Application_Model_WithholdingTaxManual extends Zend_Db_Table_Abstract{
    protected $_name = 'withholding_tax_manual';

    function getWHT($runing){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),
                array('p.*'));
         
        $select->where('p.wht_running_no = ?',$runing);

        $data = $db->fetchRow($select);
        return $data;
   }
    

    function withholding_tax_view($params)
    {
        $db = Zend_Registry::get('db');

        //print_r($params);//die;
            $select = $db->select();
            $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.wht_sn,p.wht_type,p.payment_type_wht_vat_01,p.payment_type_wht_vat_02,p.payment_type_wht_vat_03,p.payment_type_wht_vat_04,p.payment_type_wht_vat_05,p.payment_type_wht_vat_06,p.payment_type_wht_vat_07,p.payment_type_wht_vat_08,p.payment_type_wht_vat_09,p.payment_type_wht_vat_10,(p.payment_price_01+p.payment_price_02+p.payment_price_03+p.payment_price_04+p.payment_price_05+p.payment_price_06+p.payment_price_07+p.payment_price_08+p.payment_price_09+p.payment_price_10) as payment_price_total,(p.payment_wht_vat_01+p.payment_wht_vat_02+p.payment_wht_vat_03+p.payment_wht_vat_04+p.payment_wht_vat_05+p.payment_wht_vat_06+p.payment_wht_vat_07+p.payment_wht_vat_08+p.payment_wht_vat_09+p.payment_wht_vat_10) as payment_wht_vat_total'),'p.wht_sn', 'p.wht_running_no','p.wht_running_no','p.distributor_tax_no','p.distributor_name','p.address_tax','DATE_FORMAT(p.payment_date_01, "%Y/%m/%d")as payment_date_01','DATE_FORMAT(p.payment_date_02, "%Y/%m/%d")as payment_date_02','DATE_FORMAT(p.payment_date_03, "%Y/%m/%d")as payment_date_03','DATE_FORMAT(p.payment_date_04, "%Y/%m/%d")as payment_date_04','DATE_FORMAT(p.payment_date_05, "%Y/%m/%d")as payment_date_05','DATE_FORMAT(p.payment_date_06, "%Y/%m/%d")as payment_date_06','DATE_FORMAT(p.payment_date_07, "%Y/%m/%d")as payment_date_07','DATE_FORMAT(p.payment_date_08, "%Y/%m/%d")as payment_date_08','DATE_FORMAT(p.payment_date_09, "%Y/%m/%d")as payment_date_09','DATE_FORMAT(p.payment_date_10, "%Y/%m/%d")as payment_date_10','p.payment_name_01','p.payment_price_01','p.payment_wht_vat_01','p.payment_name_02','p.payment_price_02','p.payment_wht_vat_02','p.payment_name_03','p.payment_price_03','p.payment_wht_vat_03','p.payment_name_04','p.payment_price_04','p.payment_wht_vat_04','p.payment_name_05','p.payment_price_05','p.payment_wht_vat_05','p.payment_name_06','p.payment_price_06','p.payment_wht_vat_06','p.payment_name_07','p.payment_price_07','p.payment_wht_vat_07','p.payment_name_08','p.payment_price_08','p.payment_wht_vat_08','p.payment_name_09','p.payment_price_09','p.payment_wht_vat_09','p.payment_name_10','p.payment_price_10','p.payment_wht_vat_10','DATE_FORMAT(p.create_date, "%Y/%m/%d")as create_date'));
            $select->joinleft(array('s'=>'staff'),'p.create_by=s.id',array("staff_name"=>"concat(s.firstname,' ',s.lastname)",'s.email'));
            //$select->where('p.wht_running_no =', $wht_running_no);

            if (isset($params['wht_sn']) and $params['wht_sn'] != ''){
                $select->where('p.wht_sn = ?', $params['wht_sn']);
            }

            if (isset($params['wht_running_no']) and $params['wht_running_no'][0] !=''){

                //print_r($params['wht_running_no']);die;
                foreach ($params['wht_running_no'] as $wht) {
                    $wht_running_no .= "'".trim($wht)."',";
                }
                    //echo $wht_running_no;die;
                    $select->where('p.wht_running_no in('.rtrim($wht_running_no, ',').')',null);
            }

            //$select->where('p.wht_sn = ?', $sn);
            $select->where('p.status = ?', 1);


        //echo $select;die;
        $result = $db->fetchAll($select);
        return $result;
    }

    function whtfetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //print_r($params);die;
        $select = $db->select();
            $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.wht_sn'),'p.wht_sn','p.province_name','p.topic','p.payment_type_wht_vat_01','p.payment_type_wht_vat_02','p.payment_type_wht_vat_03','p.payment_type_wht_vat_04','p.payment_type_wht_vat_05','p.payment_type_wht_vat_06','p.payment_type_wht_vat_07','p.payment_type_wht_vat_08','p.payment_type_wht_vat_09','p.payment_type_wht_vat_10','p.import_name','p.distributor_id' ,'p.wht_running_no','p.distributor_name','p.distributor_tax_no','p.address_tax','p.payment_name_01','p.payment_price_01','p.payment_wht_vat_01','p.payment_name_02','p.payment_price_02','p.payment_wht_vat_02','p.payment_name_03','p.payment_price_03','p.payment_wht_vat_03','p.payment_name_04','p.payment_price_04','p.payment_wht_vat_04','p.payment_name_05','p.payment_price_05','p.payment_wht_vat_05','p.payment_name_06','p.payment_price_06','p.payment_wht_vat_06','p.payment_name_07','p.payment_price_07','p.payment_wht_vat_07','p.payment_name_08','p.payment_price_08','p.payment_wht_vat_08','p.payment_name_09','p.payment_price_09','p.payment_wht_vat_09','p.payment_name_10','p.payment_price_10','p.payment_wht_vat_10','DATE_FORMAT(p.create_date, "%Y-%m-%d")as create_date','DATE_FORMAT(p.payment_date_01, "%Y-%m-%d")as payment_date_01','DATE_FORMAT(p.payment_date_02, "%Y-%m-%d")as payment_date_02','DATE_FORMAT(p.payment_date_03, "%Y-%m-%d")as payment_date_03','DATE_FORMAT(p.payment_date_04, "%Y-%m-%d")as payment_date_04','DATE_FORMAT(p.payment_date_05, "%Y-%m-%d")as payment_date_05','DATE_FORMAT(p.payment_date_06, "%Y-%m-%d")as payment_date_06','DATE_FORMAT(p.payment_date_07, "%Y-%m-%d")as payment_date_07','DATE_FORMAT(p.payment_date_08, "%Y-%m-%d")as payment_date_08','DATE_FORMAT(p.payment_date_09, "%Y-%m-%d")as payment_date_09','DATE_FORMAT(p.payment_date_10, "%Y-%m-%d")as payment_date_10'));

            if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, CHECK_MONEY)) or ($exception_case and in_array
            ($userStorage->id, $exception_case))){
                $select->joinLeft(array('d' => 'distributor'), 'd.id = p.distributor_id', array('d.name', 'd.title','d.rank', 'd.mst_sn', 'd.unames', 'd.store_code', 'd.district','d.add_tax','d.region'));
            }else{
                $select->join(array('d' => 'distributor'), 'd.id = p.distributor_id', array('d.name', 'd.title','d.rank', 'd.mst_sn', 'd.unames', 'd.store_code', 'd.district','d.add_tax','d.region'));
            }

            
            $select->joinleft(array('s'=>'staff'),'p.create_by=s.id',array("staff_name"=>"concat(s.firstname,' ',s.lastname)",'s.email'));

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

        if($userStorage->group_id !='7'){
            if($userStorage->catty_staff_id !=''){
                $catty_staff_id = $userStorage->catty_staff_id;
                $select->where('d.region in(SELECT rm.id AS region_id
                                FROM hr.`asm` asm
                                LEFT JOIN hr.`regional_market` rm ON asm.area_id=rm.area_id
                                WHERE asm.`staff_id`=?)',$catty_staff_id);
            }
        }

        if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, CHECK_MONEY)) or ($exception_case and in_array
        ($userStorage->id, $exception_case))){
            $view_by_user="";
        }else{
            $select->where('p.distributor_id <> 0', null);
        }
                
        if (isset($params['wht_running_no']) and $params['wht_running_no'] and $params['wht_running_no'] !='')
            $select->where('p.wht_running_no LIKE ?', '%'.$params['wht_running_no'].'%');

        if (isset($params['distributor_name']) and $params['distributor_name']){
            $select->where('p.distributor_name = ?', $params['distributor_name']);
        }

        if (isset($params['import_name']) and $params['import_name']){
            $select->where('p.import_name LIKE ?', '%'.$params['import_name'].'%');
        }

        if (isset($params['topic']) and $params['topic']){
            $select->where('p.topic LIKE ?', '%'.$params['topic'].'%');
        }

        if (isset($params['province_name']) and $params['province_name']){
            $select->where('p.province_name = ?', $params['province_name']);
        }

        if (isset($params['wht_type']) and $params['wht_type']){
            $select->where('p.wht_type = ?', $params['wht_type']);
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

        $select->where('p.status = ?', 1);
        $select->order(['p.create_date asc','p.wht_running_no']);
        //print_r($params['export']);die;

        if (isset($params['export']) and $params['export']=='1'){
            
        }else{
            $select->limitPage($page, $limit);
        }

        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
  
}