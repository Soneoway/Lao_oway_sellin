<?php
class Application_Model_ChangeImeiDistibutor extends Zend_Db_Table_Abstract
{
	protected $_name = 'change_imei_distibutor';

	  function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));


    if(isset($params['imei_sn']) && $params['imei_sn']){
    	
        $imei_sn = explode("\r\n", $params['imei_sn']);

        $select->where('p.imei_sn IN (?)',$imei_sn);
        $select->order(array('p.change_at DESC'));
    }

    if(isset($params['form_date']) && $params['form_date']){

        list( $day, $month, $year ) = explode('/', $params['form_date']);

        if (isset($day) and isset($month) and isset($year) )
            $select->where('p.change_at >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
    }

    if(isset($params['to_date']) && $params['to_date']){

        list( $day, $month, $year ) = explode('/', $params['to_date']);

        if (isset($day) and isset($month) and isset($year) )
            $select->where('p.change_at <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
    }

    if(isset($params['new_distributor']) && $params['new_distributor']){
        $select->where('p.new_distibutor = ?',$params['new_distributor']);
    }

    if(isset($params['do_number']) && $params['do_number']){
        $select->where('p.do_number =?',$params['do_number']);
    }

        if ($limit)
            $select->limitPage($page, $limit);

        // echo $select; die;

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function _exportchangeimeilist($page, $limit, $total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'),'p.*'));

    if(isset($params['imei_sn']) && $params['imei_sn']){
        
        $imei_sn = explode("\r\n", $params['imei_sn']);

        $select->where('p.imei_sn IN (?)',$imei_sn);
        $select->order(array('p.change_at DESC'));
    }

     if(isset($params['form_date']) && $params['form_date']){

        list( $day, $month, $year ) = explode('/', $params['form_date']);

        if (isset($day) and isset($month) and isset($year) )
            $select->where('p.change_at >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
    }

    if(isset($params['to_date']) && $params['to_date']){

        list( $day, $month, $year ) = explode('/', $params['to_date']);

        if (isset($day) and isset($month) and isset($year) )
            $select->where('p.change_at <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
    }

    if(isset($params['new_distributor']) && $params['new_distributor']){
        $select->where('p.new_distibutor = ?',$params['new_distributor']);
    }

    if(isset($params['do_number']) && $params['do_number']){
        $select->where('p.do_number =?',$params['do_number']);
    }

            $result = $db->fetchAll($select);
            return $result;
    }

     public function getchangeOrderNo_do($sn)
    {
        $do_number="";
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref('DO',".$sn.")");
            $stmt->execute();
            $result = $stmt->fetch();
            $do_number = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Return Order No, please try again!');
        }
        return $do_number;
    }
}
