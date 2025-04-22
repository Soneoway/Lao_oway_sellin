<?php
class Application_Model_NewsnManual extends Zend_Db_Table_Abstract{
	protected $_name = 'newsn_manual';

	public function getNewSOManual($d_id)
     {
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p'=> $this->_name),array('p.d_id','p.sn','p.sn_ref','p.type','p.total'))
            ->joinLeft(array('ch' => 'checkmoney'), 'ch.sn = p.sn', array(''))
            
            ->where('p.status=?',1)
            ->where('p.d_id=?',$d_id)
            ->where('p.type=?',1)
            ->where('ch.id is null');

        // echo $select; die;
        $result = $db->fetchAll($select);
        return $result;
    }

    public function getNewCNCPManual($d_id)
     {
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p'=> $this->_name),array('p.d_id','p.sn','p.sn_ref','p.type','p.total'))
            ->joinLeft(array('cdt' => 'credit_note_tran'), 'cdt.creditnote_sn = p.sn_ref', array(''))

            ->where('p.status=?',1)
            ->where('p.d_id=?',$d_id)
            ->where('p.type=?',2)
            ->where('cdt.id is null');

        // echo $select; die;
        $result = $db->fetchAll($select);
        return $result;
    }
   
}