<?php
class Application_Model_CronSo extends Zend_Db_Table_Abstract
{
    protected $_name = 'cron_so';

    public function getCronSo(){

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'));

        $select->where('p.status = ?',1);
        $select->limit('100');
        // echo $select;die;

        return $db->fetchAll($select);
    }

    public function getRecheckLossSO(){
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'));

        $select->where('p.status = ?',0);
        $select->where('p.sn_ref = ?','');
        // echo $select;die;

        return $db->fetchRow($select);
    }

}
