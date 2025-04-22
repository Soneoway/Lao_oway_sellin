<?php
class Application_Model_logCreateSaleOrder extends Zend_Db_Table_Abstract{
	protected $_name = 'log_create_sale_order';

	public function insertLog($data)
	{
		
		$QlogCreateSaleOrder = new Application_Model_logCreateSaleOrder();
		$QlogCreateSaleOrder->insert($data);
		
	}
   
}