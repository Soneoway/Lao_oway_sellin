
<?php
class Application_Model_CreditNoteCreditTr extends Zend_Db_Table_Abstract
{
    protected $_name = 'credit_note_credit_tr';

    public function getCheckMoneyCreditCNCP($distributor_id, $sales_order){

        $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('cnct' => $this->_name),
                    array('cnct.*'))
                ->joinleft(array('ch'=>'checkmoney'),'ch.credit_tr_id=cnct.sales_order', array('ch.pay_time'));

            $select->where('cnct.distributor_id =?',$distributor_id);
	        $select->where('cnct.sales_order IN(?)',$sales_order);
        	
        	$select->order('ch.pay_time desc');
        	$select->group('cnct.id');

            $data = $db->fetchAll($select);

        return $data;

    }

    public function getCheckMoneyCreditCNCPExcel($sales_order){

        if(count($sales_order) < 1){
            return [];
        }

        $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('cnct' => $this->_name),
                    array('cnct.*'))
                ->joinleft(array('ch'=>'checkmoney'),'ch.credit_tr_id=cnct.sales_order', array('ch.pay_time','ch.bank'))
                ->joinleft(array('d'=>'distributor'),'d.id=cnct.distributor_id', array('d_name'=>'d.title'))
                ->joinleft(array('b'=>'bank'),'b.id=ch.bank', array('b_name' => 'b.name'));

            $select->where('cnct.sales_order IN(?)',$sales_order);
            
            $select->order('ch.pay_time desc');
            $select->group('cnct.id');

            $data = $db->fetchAll($select);

        return $data;

    }

}
