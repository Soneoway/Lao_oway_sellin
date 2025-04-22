<?php
class Application_Model_Quota extends Zend_Db_Table_Abstract{
	protected $_name = 'quota_oppo';
   

   public function fetchPagination($value='')
   {
   	 $db = Zend_Registry::get('db');

   		// $select = $db->select()
     //            ->from(array('q' => $this->_name),
     //                array('q.id','q.sn','q.quota_date','q.dis_type','q.status','created_at','good_type','q.channel','q.quantity','q.quantity_all','current_quota' => '(select sum(lqt.num)
     //                    from log_quota_tran lqt
     //                    where date_format(lqt.add_time,"%Y-%m-%d") = q.quota_date and lqt.cat_id = q.cat_id and lqt.good_id = q.good_id and lqt.good_color = q.good_color and lqt.good_type = q.good_type and q.dis_type = 
     //                    (select dg.group_type_id from distributor d 
     //                     left join distributor_group dg on dg.group_id=d.group_id
     //                      where d.id = lqt.d_id))'));
     
     $select = $db->select()
                ->from(array('q' => $this->_name),
                    array('q.id','q.sn','q.quota_date','q.dis_type','q.status','created_at','good_type','q.channel','q.quantity','q.quantity_all','q.warehouse_id'));

            $select->joinLeft(array('g'=>'good'),'q.good_id=g.id',array('good'=>'g.name','g.desc')); 
            $select->joinLeft(array('c'=>'good_color'),'q.good_color=c.id',array('color'=>'c.name'));
            $select->joinLeft(array('w'=>'warehouse'),'q.warehouse_id=w.id',array('warehouse_name'=>'w.name'));
            // $select->where('q.d_id = ?',$d_id);
            $select->where('q.created_at > date_add(CURDATE(),interval -3 day)');
            $select->group('q.sn');
            $select->order(['q.quota_date desc','q.status desc']);
            // echo $select;die;
            $data = $db->fetchAll($select);

            return $data;
   	
   }

   public function getQuotalist($sn)
   {
   	 $db = Zend_Registry::get('db');

            $select = $db->select()
                   ->from(array('q' => 'warehouse.'.$this->_name),array('q.*'));
            $select->joinLeft(array('a'=>'hr.area'),'q.area_id=a.id',array('a_name'=>'a.name')); 
            $select->joinLeft(array('g'=>'warehouse.good'),'g.id=q.good_id',array('p_name'=>'g.desc','p_code'=>'g.name')); 
            $select->joinLeft(array('gt'=>'warehouse.good_category'),'gt.id=q.cat_id',array('gt_name'=>'gt.name')); 
            $select->joinLeft(array('gc'=>'warehouse.good_color'),'gc.id=q.good_color',array('gc_name'=>'gc.name')); 
            $select->joinLeft(array('w'=>'warehouse.warehouse'),'w.id=q.warehouse_id',array('warehouse_name'=>'w.name')); 
             
            $select->where('q.sn = ?',$sn);

            $data = $db->fetchAll($select);
            return $data;
   }

   public function getQuotalistAll($data)
   {
     $db = Zend_Registry::get('db');

            $select_q = $db->select()
            ->from(array('q' => 'quota_oppo'), array('q.area_id'));
            // $select_q->where('q.dis_type = ?', 1);
            // $select_q->where('q.status = ?',1);
            $select_q->where('q.quantity = ?', 0);
            $select_q->where('q.sn = ?', $data['sn']);
            $select_q->where('q.warehouse_id = ?', $data['warehouse_id']);
            $inArea = $db->fetchAll($select_q);


            // start : old
            //     $select_m = $db->select()
            //         ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
            //     $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
            //     $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            //     $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            //     $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
            //     $select_m->where('a.id in (?)', $inArea );
            //     $select_m->where('lq.good_id = ?', $data['good_id']);
            //     $select_m->where('lq.good_color = ?', $data['good_color']);

            //     $select_m->where('lq.warehouse_id = ?', $data['warehouse_id']);

            //     $select_m->where('lq.good_type = ?', $data['good_type']);
            //     $select_m->where('dg.group_type_id = ?', 1);

            //     // $select_m->where('d.quota_channel IS NULL');
            //     // $select_m->where('(d.rank = 7');
            //     // $select_m->Orwhere('d.rank = 8)');
            //     $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$data['quota_date']);
                
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
            $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
            $select_m->where('a.id in (?)', $inArea );
            $select_m->where('m.good_id = ?', $data['good_id']);
            $select_m->where('m.good_color = ?', $data['good_color']);

            $select_m->where('m.warehouse_id = ?', $data['warehouse_id']);

            $select_m->where('m.type = ?', $data['good_type']);
            $select_m->where('dg.group_type_id = ?', 1);

            // $select_m->where('d.quota_channel IS NULL');
            // $select_m->where('(d.rank = 7');
            // $select_m->Orwhere('d.rank = 8)');
            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$data['quota_date']);
              
            $isSum = $db->fetchOne($select_m);


            if ($isSum) {
                $num = $isSum;
            }else{
                $num = 0;
            }
            return $num;
   }
   public function getQuotalistArea($data)
   {
     $db = Zend_Registry::get('db');

            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
            // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
            // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
            // $select_m->where('a.id = ?', $data['area_id'] );
            // $select_m->where('lq.good_id = ?', $data['good_id']);
            // $select_m->where('lq.good_color = ?', $data['good_color']);

            // $select_m->where('lq.warehouse_id = ?', $data['warehouse_id']);

            // $select_m->where('lq.good_type = ?', $data['good_type']);
            // $select_m->where('dg.group_type_id = ?', 1);

            // // $select_m->where('d.quota_channel IS NULL');
            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$data['quota_date']);
        
            // // echo $select_m;die;
            // $isSum = $db->fetchOne($select_m);
            // end : old


            $select_m = $db->select()
                ->from(array('m' => 'market'), array('sum(m.num)'));
            $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
            $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
            $select_m->where('a.id = ?', $data['area_id'] );
            $select_m->where('m.good_id = ?', $data['good_id']);
            $select_m->where('m.good_color = ?', $data['good_color']);

            $select_m->where('m.warehouse_id = ?', $data['warehouse_id']);

            $select_m->where('m.type = ?', $data['good_type']);
            $select_m->where('dg.group_type_id = ?', 1);

            // $select_m->where('d.quota_channel IS NULL');
            $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$data['quota_date']);
        
            // echo $select_m;die;
            $isSum = $db->fetchOne($select_m);

            if ($isSum) {
                $num = $isSum;
            }else{
                $num = 0;
            }
            return $num;
   }
    function Duplicate ($area_id,$cat_id,$good_id,$good_color,$quota_date,$warehouse_id)
    {
            $db = Zend_Registry::get('db');
            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

            $select->where('p.`area_id`=?',$area_id);
            $select->where('p.`cat_id`=?',$cat_id);
            $select->where('p.`good_id`=?',$good_id);
            $select->where('p.`good_color`=?',$good_color);
            $select->where('p.`quota_date`=?',$quota_date);
            $select->where('p.`warehouse_id`=?',$warehouse_id);

            $data = $db->fetchAll($select);
            //echo $select;
        return $data;
   }

   public function checkQuantity($id,$qty)
   {
   	 $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('q' => $this->_name),
                    array('q.*'));
             
            $select->where('q.id = ?',$id);

            // echo $select;
            $quantity = $db->fetchRow($select);
          
            if ($qty) {
            	if ($qty < $quantity['quantity'] || $qty > $quantity['quantity']) {
                $data['sta'] = 1;
	            	$data['quantity_old'] = $quantity['quantity'];
	            }else{
                $data['sta'] = 2;


	            }
            }else{
	            $data = array(
                    'd_id'        => $quantity['d_id'],
                    'cat_id'      => $quantity['cat_id'],
                    'good_id'     => $quantity['good_id'],
                    'good_color'  => $quantity['good_color'],
                    'quantity'    => $quantity['quantity'] 
                  );
                $data['sta'] = 3;
                
            }


            return $data;
   }

   public function getQuotaForClone($arrayQuotaID){

      $db = Zend_Registry::get('db');

      $select = $db->select()
                ->from(array('p1' => $this->_name),
                    array('p1.*'));

      $select->joinLeft(array('p2' => $this->_name),'p1.dis_type = p2.dis_type AND p1.good_type = p2.good_type AND p1.good_id = p2.good_id AND p1.good_color = p2.good_color AND p1.status = p2.status AND p1.created_at > p2.created_at',array());

      $select->where('p1.status = ?',1);
      $select->where('p1.id in (?)',$arrayQuotaID);
      $select->group(['p1.dis_type','p1.warehouse_id','p1.good_type','p1.good_id','p1.good_color']);

      // echo $select;die;

      return $db->fetchAll($select);

   }


}