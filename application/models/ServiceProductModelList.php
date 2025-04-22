<?php
class Application_Model_ServiceProductModelList extends Zend_Db_Table_Abstract{
	protected $_name = 'service_product_model_list';

	function ServiceProductModelListfetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //print_r($params);die;
        $select = $db->select();
        $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id,sum(p.num) as total_num'),'p.data_date','p.good_code','p.good_model_name','p.detail','p.num','p.create_by','p.create_date','p.status'));

        $select->joinleft(array('s'=>'staff'),'p.create_by=s.id',array("staff_name"=>"concat(s.firstname,' ',s.lastname)",'s.email'));

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
        $select->group('p.good_code');

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
}