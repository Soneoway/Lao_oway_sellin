<?php
class Application_Model_ForceSale extends Zend_Db_Table_Abstract
{
    protected $_name = 'force_sale';

    function fetchPagination($page, $limit, &$total, $params){
  		$db = Zend_Registry::get('db');
    	$userStorage = Zend_Auth::getInstance()->getStorage()->read();


        $select = $db->select()
        	->from(array('fs' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS fs.id'),'fs.*'))
       		->group('fs.campaign_id');
        // $select->joinLeft(array('fw' => 'force_sale_warehouse'),'f.campaign_id = fw.force_sale_id',array('w_id'));
        // $select->joinLeft(array('fd' => 'force_sale_distributor'),'f.campaign_id = fd.force_sale_id',array('d_id'));
        // $select->joinLeft(array('fg' => 'force_sale_detail'),'f.campaign_id = fw.force_sale_id',array('gift_id'=>'g_id','gift_num'=>'g_id_num'));

        	// echo $select;
        if(isset($params['campaign_name'])       and     $params['campaign_name'])
            {
               $select->where('fs.name = ?', '%'.$params['campaign_name'].'%'); 
            }
        if(isset($params['campaign_id'])       and     $params['campaign_id'])
            {
               $select->where('fs.campaign_id = ?', $params['campaign_id']); 
            }
        if(isset($params['start_date'])       and     $params['start_date'])
            {
               $select->where('fs.start_date = ?', $params['start_date']); 
            }
        if(isset($params['end_date'])       and     $params['end_date'])
            {
               $select->where('fs.end_date = ?', $params['end_date']); 
            }
        
        $select->limitPage($page, $limit);
        // echo $select;die;
       	$data = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
       	return $data;
    }

    function get_cache($id){
            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('fs' => $this->_name),
                    array('fs.*'));

            $select->joinLeft(array('g' => 'good'),'fs.good_id = g.id',array('good_name'=>'g.desc'));
            $select->where('fs.campaign_id =?',$id);

            // $select->order(new Zend_Db_Expr('fsw`name` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select);

        return $data;
    }

}
