<?php
class Application_Model_Deposit extends Zend_Db_Table_Abstract
{
    protected $_name = 'deposit';

   
public function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.deposit_sn,p.distributor_id,
p.total_amount,p.use_total,p.balance_total,d.title,p.create_date,p.status,
p.update_date'), 'p.distributor_id','total_amount' => 'p.total_amount', 'use_total' => 'p.use_total', 'confirm_date' => 'p.confirm_date', 'balance_total' => 'p.balance_total', 'deposit_type' => 'p.deposit_type','file_pay_slip' => 'p.file_pay_slip'));
        $select->joinleft(array('d'=>'distributor'),'p.distributor_id=d.id',array("distributor_name"=>'d.title','d.id'));
        $select->joinleft(array('s'=>'staff'),'p.create_by=s.id',array("staff_name"=>"concat(s.firstname,' ',s.lastname)",'s.email'));
       // $select->joinLeft(array('d'=>'distributor'),'d.id=p.distributor_id',array('d.title'));
	     

	    if (isset($params['status']) && $params['status']) {
        	$select->where('p.status = ?', $params['status']);
        }

        $select->where('p.total_amount >= ?',1);

        if (isset($params['d_id']) && $params['d_id']) {
        	$select->where('p.distributor_id = ?', $params['d_id']);
        }

        if (isset($params['deposit_sn']) && $params['deposit_sn']) {
        	$select->where('p.deposit_sn = ?', $params['deposit_sn']);
        }

        if (isset($params['distributor_name']) && $params['distributor_name']) {
        	$select->where('d.title like ?', '%'.$params['distributor_name'].'%');
        }

        if (isset($params['view_status']) && $params['view_status']==1) {
            $select->where('p.confirm_date is null', null);
        }else if (isset($params['view_status']) && $params['view_status']==2) {
            $select->where('p.confirm_date is not null', null);
        }


        $select->order('p.update_date desc');
        $select->order('p.create_date desc');


        if (isset($params['sort']) and $params['sort']) {
            $order_str = $collate = '';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str .= $params['sort'] . $collate . $desc;
            

            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

        // echo $select;die;

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
    
  
  public function getDeposit_sn($distributor_id,$deposit_sn_list)
    {
        try{
             $db = Zend_Registry::get('db');
            // $creditnote_sn_list='CN59020008';
                if($deposit_sn_list ==''){
                    $select = $db->select()
                    ->from(array('c' => 'deposit'),array('total_amount' => 'total_amount','use_total' => 'use_total','balance_total' => 'balance_total','deposit_sn' => 'deposit_sn'))
                    ->where('c.distributor_id = ?', $distributor_id)
                    ->where('c.status = 1 ', null)
                    ->where('c.confirm_date is not null ', null); 
                }else{

                    $select = $db->select()
                    ->from(array('c' => 'deposit'),array('total_amount' => 'total_amount','use_total' => 'use_total','balance_total' => 'balance_total','deposit_sn' => 'deposit_sn'))
                    ->where('c.distributor_id = ?', $distributor_id)
                    ->where("c.deposit_sn not in(".$deposit_sn_list.")", null)
                    ->where('c.status = 1 and c.confirm_date is not null and c.balance_total >0', null); 
                }
                
                
            //echo $select;die;
             if($deposit_sn_list!=''){
                $result= $select;
             }

            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            // $result= $select;   
            //return $result;
            return array(
                    'code' => 1,
                    'data' => $result,
                );
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
        //return $result;

    }

    

}