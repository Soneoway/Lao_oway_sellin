<?php

class Application_Model_OppoAllGreenRewardCn extends Zend_Db_Table_Abstract
{
    protected $_name = 'oppo_all_green_reward_cn';

    public function cn_reward_view_print($params) {

        $db          = Zend_Registry::get('db');
     
        try {
     
          $select = $db->select()
     
                       ->from(array('op' => $this->_name), array('op.*', 'a.name AS area_name'
     
              , 'rm2.name AS province'
     
              , 'rm.name AS district', 'dis.id AS d_id,dis.store_code', 'dis.title', 'dis.mst_sn'
     
              , 'dis.tel','dis.add AS add_tax'))
     
            ->joinLeft(array('dis' => 'distributor'), 'dis.id = op.distributor_id', array(''))
     
            ->joinLeft(array('rm'  => 'hr.regional_market'), 'dis.district = rm.id', array())
     
            ->joinLeft(array('rm2' => 'hr.regional_market'), 'dis.region = rm2.id', array())
     
            ->joinLeft(array('a'   => 'hr.area'), 'rm2.area_id = a.id ', array())  
     
            ->where('op.creditnote_sn = ?',$params['cn'])
     
            ->where('op.distributor_id = ?',$params['d_id']);
     

          // echo $select;
     
      
     
          $result = $db->fetchRow($select);
     
          // print_r($result);die;
     
          return $result;
     
        } catch (exception $e) {
     
          $result = null;
     
        }
     
        return $result;
     
      }
 
    function Duplicate ($air_number,$distributor_id,$store_id)
    {
            $db = Zend_Registry::get('db');
            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

            $select->where('p.`air_number`=?',$air_number);
            $select->where('p.`distributor_id`=?',$distributor_id);
            $select->where('p.`store_id`=?',$store_id);

            $data = $db->fetchAll($select);
            // echo $select;
        return $data;
   }

    function Export ($key_sn)
    {

            $db = Zend_Registry::get('db');
            $select = $db->select()
                ->from(array('cn' => $this->_name),
                    array('cn.*'));
            $select->join(array('d' => 'distributor'),'cn.distributor_id = d.id',array('tax_id' => 'd.mst_sn','d.store_code','d.title', 'd_id' => 'd.id'));
            $select->where('cn.key_sn = ?', $key_sn);
            $data = $db->fetchAll($select);

        return $data;
   }


    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
        $select->join(array('d' => 'distributor'),'p.distributor_id = d.id',array('tax_id' => 'd.mst_sn','d.store_code','d.title', 'd_id' => 'd.id'));

        if (isset($params['year']) and $params['year'])
            $select->where('p.round_year =?', $params['year']);

        if (isset($params['air_number']) and $params['air_number'])
            $select->where('p.air_number LIKE ?', '%'.$params['air_number'].'%');

        if (isset($params['d_id']) and $params['d_id'])
            $select->where('p.distributor_id =?', $params['d_id']);

        if (isset($params['cn']) and $params['cn'])
            $select->where('p.creditnote_sn LIKE ?', '%'.$params['cn'].'%');

        if (isset($params['created_at_from']) and $params['created_at_from']){
             list( $day, $month, $year ) = explode('/', $params['created_at_from']);
            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['created_at_to']) and $params['created_at_to']){
             list( $day, $month, $year ) = explode('/', $params['created_at_to']);
            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }


        $order_str = $collate = '';

        if (isset($params['sort']) and $params['sort']) {
            $collate = '';
            
            if (in_array($params['sort'], array('real_file_name')))
                $collate .= ' COLLATE utf8_unicode_ci ';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';
            
            $order_str .= $params['sort'] . $collate . $desc;

            $select->order(new Zend_Db_Expr($order_str));
        }

        $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

}

