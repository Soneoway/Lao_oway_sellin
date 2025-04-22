<?php
class Application_Model_ImportImei2 extends Zend_Db_Table_Abstract{
	protected $_name = 'import_imei2';

	function importImei2ListfetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //print_r($params);die;
        $select = $db->select();
        $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.imei1,p.imei2'),'p.good_code','p.color','p.created_by','p.created_date','p.status'));

        $select->joinleft(array('s'=>'staff'),'p.created_by=s.id',array("staff_name"=>"concat(s.firstname,' ',s.lastname)",'s.email'));

        if (isset($params['imei1']) and $params['imei1']){
            $select->where('p.imei1 = ?', $params['imei1']);
        }

        if (isset($params['imei2']) and $params['imei2']){
            $select->where('p.imei2 = ?', $params['imei2']);
        }

        if (isset($params['good_code']) and $params['good_code']){
            $select->where('p.good_code = ?', $params['good_code']);
        }

        if (isset($params['start_date']) and $params['start_date']){
            list( $day, $month, $year ) = explode('/', $params['start_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $start_date = $year.'-'.$month.'-'.$day.' 00:00:00';
            }
            //$start_date = $params['start_date'];
            $select->where('p.created_date >= ?', $start_date);
        }

        if (isset($params['end_date']) and $params['end_date']){
            list( $day, $month, $year ) = explode('/', $params['end_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $end_date = $year.'-'.$month.'-'.$day.' 23:59:59';
            }
            //$end_date = $params['end_date'];
            $select->where('p.created_date <= ?', $end_date);
        }

        $select->where('p.status = ?', 1);

        $select->order(['p.created_date desc','p.good_code']);
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