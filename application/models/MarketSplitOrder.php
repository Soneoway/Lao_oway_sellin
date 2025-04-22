<?php
class Application_Model_MarketSplitOrder extends Zend_Db_Table_Abstract
{
    protected $_name = 'market_split_order';

    public function getSplitOrder($sn)
    {
     $db = Zend_Registry::get('db');

            $select = $db->select()
            ->from(array('ms' => 'warehouse.'.$this->_name),array());
            $select->join(array('m'=>'market'),'ms.sales_order_split=m.sn',array('sales_order_split'=>'m.sn_ref'));   
            $select->where("ms.sales_order = '".$sn."'");
            $select->group('m.sn');
            $data = $db->fetchRow($select);
            return $data;
    }
}