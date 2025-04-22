<?php

class Application_Model_DistributorBlackListLog extends Zend_Db_Table_Abstract
{
    protected $_name = 'distributor_black_list_log';

    function fetchBlackListLog($page, $limit, &$total, $params){
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
        $select->joinLeft(array('d'=>'distributor'),'d.id=p.d_id',array('d_id'=>'d.id','d.title'));

        $select->joinLeft(array('hrrm'=>'hr.regional_market'),'hrrm.id=d.region',array('hrrm_name' => 'hrrm.name','hrrm.area_id'));

        $select->joinLeft(array('hrarea'=>'hr.area'),'hrarea.id=hrrm.area_id',array('hrarea_name' => 'hrarea.name'));

        $select->joinLeft(array('s'=>'staff'),'s.id=p.black_by',array('black_by'=>'s.firstname'));
        $select->joinLeft(array('s2'=>'staff'),'s2.id=p.unblack_by',array('unblack_by'=>'s2.firstname'));
        
        if (isset($params['reason_id']) and $params['reason_id'])
                $select->where('p.remark = ?',$params['reason_id']);

        if (isset($params['distributor']) and $params['distributor'])
                $select->where('d.title like ?','%'.$params['distributor'].'%');

        if (isset($params['dis_id']) and $params['dis_id'])
                $select->where('d.id = ?',$params['dis_id']);

        if (isset($params['type']) and $params['type'])
                $select->where('p.type = ?',$params['type']);
        
        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);

        if ($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");

        return $result;
    }
}

