<?php

class Application_Model_BorrowingTran extends Zend_Db_Table_Abstract
{
    protected $_name = 'borrowing_tran';

    function getImeiBorrowing($id){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->joinLeft(array('i' => 'imei'), 'i.imei_sn=p.imei', array('good_id','good_color','type'));

        $select->where('p.bl_id = ?',$id);
        $select->where('p.status <> ?',0);

        $data = $db->fetchAll($select);

        return $data;
    }

    function CheckBorrowingImeiDetail($id,$imei,$current_warehouse){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->joinLeft(array('i' => 'imei'), 'i.imei_sn=p.imei', array('imei_type' => 'i.type','i.good_id','i.good_color'));

        $select->joinLeft(array('bl' => 'borrowing_list'), 'bl.id=p.bl_id', array(''));

        $select->where('p.bl_id = ?',$id);
        $select->where('p.imei = ?',$imei);
        $select->where('p.status = ?',1);
        $select->where('bl.status = ?',13);
        $select->where('bl.wms_status = ?',1);
        $select->where('bl.wms_return_by is null');
        $select->where('i.sales_sn is null');
        $select->where('i.warehouse_id = ?',$current_warehouse);

        $data = $db->fetchRow($select);

        return $data;
    }
}

