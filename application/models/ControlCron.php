<?php
class Application_Model_ControlCron extends Zend_Db_Table_Abstract
{
	protected $_name = 'control_cron';

    public function getControlCronGenSnRef($id){

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'));

        $select->where('p.id = ?',$id);
        $select->where('p.status = ?',1);
        // echo $select;die;

        return $db->fetchRow($select);
    }
}                                                      
