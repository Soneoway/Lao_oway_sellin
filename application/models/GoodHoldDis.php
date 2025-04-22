<?php
class Application_Model_GoodHoldDis extends Zend_Db_Table_Abstract{
	protected $_name = 'good_hold_dis';

	function fetchGoodHoldDis($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
            $select->join(array('g'=>'good'),'p.good_id=g.id',array('p_name'=>'g.name'));
            $select->joinLeft(array('s'=>'staff'),'p.hold_by=s.id',array('name'=>'s.username'));
            $select->joinLeft(array('s2'=>'staff'),'p.unblock_by=s2.id',array('name_unblock'=>'s2.username'));
            $select->joinLeft(array('d'=>'distributor'),'p.d_id=d.id',array('d_name'=>'d.title'));

        // if (isset($params['name']) and $params['name'])
        //     $select->where('p.name LIKE ?', '%'.$params['name'].'%');
        	 $select->where('p.good_id = ?', $params['good_id']);
            $select->where('p.status is NULL');

        if ($limit)
            $select->limitPage($page, $limit);
        $select->order('p.status asc');
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function CheckHoldDis($good_id,$d_id,$warehouse_id){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.*'));
            $select->join(array('g'=>'good'),'p.good_id=g.id',array('p_name'=>'g.name'));
            $select->join(array('s'=>'staff'),'p.hold_by=s.id',array('name'=>'s.username'));
            $select->join(array('d'=>'distributor'),'p.d_id=d.id',array('d_name'=>'d.title'));

        // if (isset($params['name']) and $params['name'])
            $select->where('p.good_id = ?', $good_id);
            $select->where('p.warehouse_id = ?', $warehouse_id);
            $select->where('p.d_id = ?', $d_id);
            $select->where('p.status is NULL');

        // echo $select;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function GetGoodId(){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.good_id'));
            $select->where('p.status is NULL');
            $select->group('p.good_id');
        
        $result = $db->fetchAll($select);
        $data =  array();
  		if ($result) {
  			for ($i=0; $i < count($result); $i++) { 
  				$data[] = $result[$i]['good_id'];
  			}
  			
  		}
        return $data;
    }
   
}