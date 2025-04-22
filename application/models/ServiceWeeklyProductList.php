<?php
class Application_Model_ServiceWeeklyProductList extends Zend_Db_Table_Abstract{
	protected $_name = 'service_weekly_product_list';

	function ServiceWeeklyProductListfetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //print_r($params);die;

        $select = $db->select();
        $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'),'p.data_date','p.good_code','p.good_model','p.good_name_chinese','p.good_name_eng','p.import_price_usd','p.import_price_bath','p.retail_price_bath','p.created_date as import_date','p.status'));

        $select->joinleft(array('s'=>'staff'),'p.created_by=s.id',array("import_by"=>"concat(s.firstname,' ',s.lastname)",'s.email'));

        if (isset($params['data_type']) and $params['data_type']){
            //$select->where('p.data_type = ?', $params['data_type']);
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
            $select->where('p.data_date >= ?', $start_date);
        }

        if (isset($params['end_date']) and $params['end_date']){
            list( $day, $month, $year ) = explode('/', $params['end_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $end_date = $year.'-'.$month.'-'.$day.' 23:59:59';
            }
            //$end_date = $params['end_date'];
            $select->where('p.data_date <= ?', $end_date);
        }

        $select->where('p.status = ?', 1);
        //$select->group('p.good_code');

        $select->order(['p.data_date desc','p.good_code']);
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


    function getWeekOfYear($date)
    {
        $db = Zend_Registry::get('db');
        $select = "SELECT YEAR('".$date."')AS year_number,WEEK('".$date."') AS week_number,CONCAT(YEAR('".$date."'),WEEK('".$date."')) AS week_of_year;";

        $result = $db->fetchRow($select);
        return $result;
    }









}