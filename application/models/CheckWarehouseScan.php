<?php
class Application_Model_CheckWarehouseScan extends Zend_Db_Table_Abstract
{
    protected $_name = 'check_warehouse_scan';

    public function getImeiLineScanBy($line, $imei){

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'));

        $select->where('p.status = ?',1);
        $select->where('p.line = ?',$line);
        $select->where('p.imei = ?',$imei);
        // echo $select;die;

        return $db->fetchRow($select);
    }

}
