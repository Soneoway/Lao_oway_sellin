<?php
class Application_Model_QuotaOppoByDistributor extends Zend_Db_Table_Abstract{
  protected $_name = 'quota_oppo_by_distributor';
   

   public function fetchPagination($value='')
   {
     $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('q' => $this->_name),
                    array('q.sn','q.quota_date','q.order_type','q.status'));
           $select->joinLeft(array('d'=>'distributor'),'q.d_id=d.id',array('d_name'=>'d.title')); 
            $select->joinLeft(array('g'=>'good'),'q.good_id=g.id',array('good'=>'g.name','g.desc')); 
            $select->joinLeft(array('c'=>'good_color'),'q.good_color=c.id',array('color'=>'c.name'));
            // $select->where('q.d_id = ?',$d_id);
            $select->group('q.sn');
            $select->order('q.quota_date desc');
            $data = $db->fetchAll($select);

            return $data;
    
   }

   public function getQuotalist($sn)
   {
     $db = Zend_Registry::get('db');

            // start : old
            // $select2 = $db->select()
            //        ->from(array('lq' => 'log_quota_tran_distributor'),array('total'=>'ifnull(sum(lq.num),0)','lq.d_id','lq.good_id' ,'lq.good_color','lq.warehouse_id','timeing'=>'date_format(lq.add_time,"%Y-%m-%d")' ));
            // $select2->group(array('lq.d_id','lq.good_id' ,'lq.good_color','lq.warehouse_id','date_format(lq.add_time,"%Y-%m-%d")'));
            // end : old

            $select2 = $db->select()
                   ->from(array('m' => 'market'),array('total'=>'ifnull(sum(m.num),0)','m.d_id','m.good_id' ,'m.good_color','m.warehouse_id','timeing'=>'date_format(m.add_time,"%Y-%m-%d")' ));
            $select2->group(array('m.d_id','m.good_id' ,'m.good_color','m.warehouse_id','date_format(m.add_time,"%Y-%m-%d")'));

            $select = $db->select()
                   ->from(array('q' => $this->_name),array('q.*'));
            $select->joinLeft(array('o'=>$select2),'(o.d_id =q.d_id) AND (o.good_id = q.good_id) AND (o.good_color = q.good_color) AND (o.warehouse_id = q.warehouse) AND (o.timeing = q.`quota_date`)',array('o.total'));
            $select->joinLeft(array('w'=>'warehouse'),'q.warehouse=w.id',array('w_name'=>'w.name'));        
            $select->joinLeft(array('d'=>'distributor'),'q.d_id=d.id',array('d_name'=>'d.title','d_code'=>'d.store_code')); 
            $select->joinLeft(array('g'=>'good'),'g.id=q.good_id',array('p_name'=>'g.desc','p_code'=>'g.name')); 
            $select->joinLeft(array('gt'=>'good_category'),'gt.id=q.cat_id',array('gt_name'=>'gt.name')); 
            $select->joinLeft(array('gc'=>'good_color'),'gc.id=q.good_color',array('gc_name'=>'gc.name')); 
             
            $select->where('q.sn = ?',$sn);
            $select->where('q.del is null');
           
            $data = $db->fetchAll($select);
            return $data;
   }

   public function getAddQuotalist($sn)
   {
     $db = Zend_Registry::get('db');

            $select = $db->select()
                   ->from(array('q' => $this->_name),array('q.*'));

            $select->joinLeft(array('w'=>'warehouse'),'q.warehouse=w.id',array('w_name'=>'w.name')); 
            $select->joinLeft(array('d'=>'distributor'),'q.d_id=d.id',array('d_name'=>'d.title','d_code'=>'d.store_code')); 
            $select->joinLeft(array('g'=>'good'),'g.id=q.good_id',array('p_name'=>'g.desc','p_code'=>'g.name')); 
            $select->joinLeft(array('gt'=>'good_category'),'gt.id=q.cat_id',array('gt_name'=>'gt.name')); 
            $select->joinLeft(array('gc'=>'good_color'),'gc.id=q.good_color',array('gc_name'=>'gc.name')); 
             
            $select->where('q.sn = ?',$sn);
            $select->group('q.sn');
            $data = $db->fetchAll($select);
            // print_r($data);
            return $data;
   }

    function Duplicate ($area_id,$cat_id,$good_id,$good_color,$quota_date)
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
}