<?php 
/**
* 
*/
class My_Delivery_Order
{
    public static function deliverToHub($district_id = null)
    {
        if (!$district_id) throw new Exception("My_Delivery_Order/Invalid district", 1);
        
        $QHub = new Application_Model_Hub();
        $where = $QHub->getAdapter()->quoteInto('district_id = ?', $district_id);
        $hub = $QHub->fetchRow($where);

        if ($hub) return $hub;
        return false;
    }

    public static function deliverToOppo($distributor_id = null)
    {
        if (!$distributor_id) throw new Exception("My_Delivery_Order/Invalid distributor", 1);
        
        if (in_array($distributor_id, array(OPPO_SERVICE_CLUB, OPPO_BORROW, OPPO_STAFF, OPPO_GIFT, OPPO_EVENT, OPPO_DEMO, OPPO_KVL, OPPO_SUPER_DAY)))
            return true;

        return false;
    }
}
