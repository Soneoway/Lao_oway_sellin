<?php
class Application_Model_AutoGiftbox extends Zend_Db_Table_Abstract
{
	protected $_name = 'auto_gift_box';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
    if($limit){  
        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
    }else{
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'));
    }
        $select->joinLeft(array('g'=>'good'),'g.id = p.good_id',array('phone_name'=>'g.name'));
        $select->joinLeft(array('gg'=>'good'),'gg.id = p.gift_good_id',
            array('gift_name'=>'gg.name'));
        $select->joinLeft(array('s'=>'staff'),'s.id = p.created_by',array('s.username'));
        $select->joinLeft(array('ss'=>'staff'),'ss.id = p.update_by',array('user_update'=>'ss.username'));
        //---เงื่อนไข search
        if(isset($params['good_id']) and $params['good_id'])
           $select->where('p.good_id =?',$params['good_id']);

       if(isset($params['good_id_give']) and $params['good_id_give'])
           $select->where('p.gift_good_id =?',$params['good_id_give']);

        if(isset($params['auto_giftbox_start_date']) and $params['auto_giftbox_start_date'])
           $select->where('p.start_date =?',$params['auto_giftbox_start_date']);

        if(isset($params['auto_giftbox_end_date']) and $params['auto_giftbox_end_date']){
            
            $date = $params['auto_giftbox_end_date'];
            $time = '23:59:59';
            $tt = $date.' '.$time;

            $select->where('p.end_date =?',$tt);
         }

        if(isset($params['checkbox_allday']) and $params['checkbox_allday'])
           $select->where('p.all_date =?',$params['checkbox_allday']);
        //--end search
        $select->where('p.status =?',1);
        $select->order('p.id desc');

        // echo $select; die;
        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);

        if ($limit)
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function checkAutoGiftbox($good_ids){

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'));

        $select->where('p.good_id IN(?)',$good_ids);
        $select->where('p.status = ?',1);
        // echo $select;die;
        return $db->fetchRow($select);
    }

}                                                      
