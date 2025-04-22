<?php
class Application_Model_NewQuotaDistributor extends Zend_Db_Table_Abstract{
  protected $_name = 'new_quota_distributor';
   
  function fetchPagination($page, $limit, &$total, $params){

    $db = Zend_Registry::get('db');

    $select = $db->select()
        ->from(array('p' => $this->_name),
            array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

    $select->joinLeft(array('d'=>'distributor'),'p.d_id=d.id',array('d_name'=>'d.title','d.store_code')); 

    $select->joinLeft(array('dg'=>'distributor_group'),'dg.group_id=d.group_id',array('group_type_id','group_name'));

    if (isset($params['good_id']) and $params['good_id']){
        $select->where('p.good_id = ?', $params['good_id']);
    }else{
        return array();
    }

    if (isset($params['store_code']) and $params['store_code'])    
        $select->where('d.store_code like ?','%'.$params['store_code'].'%');

    if (isset($params['distributor']) and $params['distributor'])    
        $select->where('d.title like ?','%'.$params['distributor'].'%');  

    if (isset($params['dis_type']) and $params['dis_type'])
        $select->where('dg.group_type_id = ?', $params['dis_type']);

    if (isset($params['order_type']) and $params['order_type'])
        $select->where('p.order_type = ?', $params['order_type']);

    if (isset($params['warehouse_id']) and $params['warehouse_id'])
        $select->where('p.warehouse_id = ?', $params['warehouse_id']);

    $select->where('p.quota_date >= ?',date("Y/m/d"));
    // $select->where('p.status = ?',1);

    $select->limitPage($page, $limit);

    $result = $db->fetchAll($select);
    $total = $db->fetchOne("select FOUND_ROWS()");
    
    return $result;
  }

  function getQuotaCurrent($array_id){

    $db = Zend_Registry::get('db');
    $select = $db->select()
        ->from(array('p' => $this->_name), array('p.*'));


    // start : old
    // $select->joinLeft(array('lqtd' => 'log_quota_tran_distributor'), 'lqtd.warehouse_id = p.warehouse_id and lqtd.d_id = p.d_id and lqtd.good_id = p.good_id and lqtd.good_type = p.good_type and lqtd.add_time >= p.quota_date', array('sum(num) as current_quota','lqtd.good_color'));
    // end : old

    $select->joinLeft(array('m' => 'market'), 'm.warehouse_id = p.warehouse_id and m.d_id = p.d_id and m.good_id = p.good_id and m.good_type = p.good_type and m.add_time >= p.quota_date', array('sum(num) as current_quota','m.good_color'));

    
    $select->where('p.id in (?)',$array_id);
    $select->where('p.quota_date >= ?',date("Y/m/d 00:00:00"));
    $select->where('p.quota_date <= ?',date("Y/m/d 23:59:59"));
    // $select->where('p.status = ?',1);

    // start : old
    // $select->group('lqtd.good_color');
    // end : old

    $select->group('m.good_color');

    // echo $select;die;

    return $db->fetchAll($select);
  }

  function getNewQuotaDistributor($id){

    $db = Zend_Registry::get('db');

    $select = $db->select()
        ->from(array('p' => $this->_name),
            array('p.*'));

    $select->joinLeft(array('d'=>'distributor'),'p.d_id=d.id',array('d_name'=>'d.title','d.store_code')); 

    $select->joinLeft(array('dg'=>'distributor_group'),'dg.group_id=d.group_id',array('group_type_id','group_name'));

    $select->where('p.id = ?', $id);

    $result = $db->fetchRow($select);

    return $result;
  }

  function checkDuplicateQuota($d_id,$good_id,$good_type,$quota){

  }

}