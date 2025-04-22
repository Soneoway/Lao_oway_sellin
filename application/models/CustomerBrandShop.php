<?php
class Application_Model_CustomerBrandShop extends Zend_Db_Table_Abstract
{
	protected $_name = 'customer_brandshop';

	public function chkCustomerBrandshop($customer_name,$tax_number,$member_brandshop_code = null)
	{
        if($customer_name =='') return false;
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => $this->_name), array('i.customer_id'));
        $select->where('i.customer_name = ?',$customer_name);
        $select->where('i.tax_number = ?',$tax_number);

        if($member_brandshop_code){
            $select->where('i.member_brandshop_code = ?',$member_brandshop_code);
        }
        //echo $select;die;
        return $db->fetchRow($select);
    }

}
