<?php

class Application_Model_OppoTopGreenRewardCn extends Zend_Db_Table_Abstract
{
    protected $_name = 'credit_note';

    public function cn_top_green_reward_view_print($params) {

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



}

