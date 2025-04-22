<?php
class Application_Model_Quota extends Zend_Db_Table_Abstract{
	protected $_name = 'quota_oppo';
   

   public function fetchPagination($value='')
   {
   	 $db = Zend_Registry::get('db');

   		$select = $db->select()
                ->from(array('q' => $this->_name),
                    array('q.*'));
            $select->joinLeft(array('d'=>'distributor'),'q.d_id=d.id',array('d_name'=>'d.title')); 
            $select->joinLeft(array('g'=>'good'),'q.good_id=g.id',array('good'=>'g.name')); 
            $select->joinLeft(array('c'=>'good_color'),'q.good_color=c.id',array('color'=>'c.name'));
            // $select->where('q.d_id = ?',$d_id);
            $select->group('q.d_id');
            $data = $db->fetchAll($select);
            return $data;
   	
   }

   public function getQuotalist($d_id)
   {
   	 $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('q' => $this->_name),
                    array('q.*'));
            $select->joinLeft(array('d'=>'distributor'),'q.d_id=d.id',array('d_name'=>'d.title')); 
             
            $select->where('q.d_id = ?',$d_id);

            $data = $db->fetchAll($select);
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