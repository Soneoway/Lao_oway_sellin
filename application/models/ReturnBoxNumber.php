<?php
class Application_Model_ReturnBoxNumber extends Zend_Db_Table_Abstract{
    protected $_name = 'return_box_number';
    
    public function getReturnBoxNumberImei_No($sn)
    {
        $sn_ref="";
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref_reward('RB',".$sn.")");
            $stmt->execute();
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Return Box Number Imei No, please try again!');
        }
        return $sn_ref;
    }

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');


        //print_r($params);die;

        $select = $db->select();
            $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.box_sn,(SELECT COUNT(bi.imei_sn)AS count_imei FROM return_box_imei bi WHERE bi.box_sn=p.`box_sn` and bi.staff_confirm_date is not null)AS total_staff_confirm_imei,(SELECT COUNT(bi.imei_sn)AS count_imei FROM return_box_imei bi WHERE bi.box_sn=p.`box_sn` and bi.finance_confirm_date is not null)AS total_finance_confirm_imei'), 'p.*','DATE_FORMAT(p.receive_date, "%Y-%m-%d")as receive_date'));
            $select->joinleft(array('s'=>'staff'),'p.create_by=s.id',array("staff_name"=>"concat(s.firstname,' ',s.lastname)",'s.email'));

        if (isset($params['box_number']) and $params['box_number'] and $params['box_number'] !='')
            $select->where('p.box_number LIKE ?', '%'.$params['box_number'].'%');

        if (isset($params['box_post_number']) and $params['box_post_number'] and $params['box_post_number'] !='')
            $select->where('p.box_post_number LIKE ?', '%'.$params['box_post_number'].'%');

        if (isset($params['sender_name']) and $params['sender_name'] and $params['sender_name'] !='')
            $select->where('p.sender_name LIKE ?', '%'.$params['sender_name'].'%');

        /*if (isset($params['view_status']) and $params['view_status'] and $params['view_status']==1){
            
            //$select->where('p.box_status = ?', $params['view_status']);
        }*/

        if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm']=='list')
        {
            $select->where('p.total_imei != (SELECT COUNT(bi.imei_sn)AS count_imei FROM return_box_imei bi WHERE bi.box_sn=p.`box_sn` and bi.staff_confirm_date is not null) or p.total_imei=0', null);

            //$select->where('p.box_status = ?', $params['view_status']);
        }else if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm']=='confirm'){

            $select->where('(SELECT COUNT(bi.imei_sn)AS count_imei FROM return_box_imei bi WHERE bi.box_sn=p.`box_sn` and bi.staff_confirm_date is not null) != (SELECT COUNT(bi.imei_sn)AS count_imei FROM return_box_imei bi WHERE bi.box_sn=p.`box_sn` and bi.finance_confirm_date is not null)', null);

            //$select->where('p.box_status = ?', $params['view_status']);
        }

        if (isset($params['distributor_name']) and $params['distributor_name'])
            $select->where('p.distributor_name = ?', $params['distributor_name']);

        if (isset($params['start_date']) and $params['start_date'])
            $select->where('p.create_date >= ?', $params['start_date']);

        if (isset($params['end_date']) and $params['end_date'])
            $select->where('p.create_date <= ?', $params['end_date']);


        $select->order(['p.create_date asc']);

        $select->limitPage($page, $limit);

        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
  
}