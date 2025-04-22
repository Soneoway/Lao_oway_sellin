<?php
class Application_Model_LogQuotaTranDistributor extends Zend_Db_Table_Abstract{
	protected $_name = 'log_quota_tran_distributor';
   

   public function fetchPagination($value='')
   {
   	 $db = Zend_Registry::get('db');

   		$select = $db->select()
                // start : old
                // ->from(array('q' => $this->_name),
                // end : old
                ->from(array('q' => 'market'),
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
                // start : old
                // ->from(array('q' => $this->_name),
                // end : old
                ->from(array('q' => 'market'),
                    array('q.*'));
            $select->joinLeft(array('d'=>'distributor'),'q.d_id=d.id',array('d_name'=>'d.title')); 
             
            $select->where('q.d_id = ?',$d_id);

            $data = $db->fetchAll($select);
            return $data;
   }


   public function checkQuantity($id,$qty)
   {
   	 $db = Zend_Registry::get('db');

            $select = $db->select()
                // start : old
                // ->from(array('q' => $this->_name),
                // end : old
                ->from(array('q' => 'market'),
                    array('q.quantity'));
             
            $select->where('q.id = ?',$id);
            // echo $select;
            $quantity = $db->fetchOne($select);
            echo $quantity.'===='.$qty;
            if ($quantity) {
            	if ($qty < $quantity || $qty > $quantity) {
	            	$data = 1;
	            }else{
	            	$data = 0;
	            }
            }else{
	            $data = 3;
            }


            return $data;
   }
}