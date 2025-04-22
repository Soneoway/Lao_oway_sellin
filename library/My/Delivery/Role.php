<?php
/**
*
*/
class My_Delivery_Role
{
    const Hub = 1;
    const Carrier = 2;

    public static function isAllow($group_id, $staff_id, $object_id, $type)
    {
        if (self::allowAll($group_id))
            return true;

        if ($type == self::Hub && self::isAllowHub($staff_id, $object_id))
            return true;


        if ($type == self::Carrier && self::isAllowCarrier($staff_id, $object_id))
            return true;

        return false;
    }

    public static function allowAll($group_id)
    {
        if (My_Staff_Group::inGroup($group_id, array(MANAGER, WAREHOUSE, WAREHOUSE_LEADER, ADMINISTRATOR_ID)))
            return true;

        return false;
    }

    public static function isAllowHub($staff_id, $hub_id)
    {
        $QStaffHub = new Application_Model_StaffHub();
        return $QStaffHub->isAllow($staff_id, $hub_id);
    }

    public static function isAllowCarrier($staff_id, $carrier_id)
    {
        $QStaffCarrier = new Application_Model_StaffCarrier();
        return $QStaffCarrier->isAllow($staff_id, $carrier_id);
    }
}