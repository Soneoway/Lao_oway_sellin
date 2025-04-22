<?php
/**
* @author buu.pham
*/
class My_Report
{
    public static function preventExport()
    {
        
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        if ($userStorage && (
            ADMINISTRATOR_ID == $userStorage->group_id
            || SUPER_SALES_ADMIN == $userStorage->group_id
            || SUPERADMIN_ID == $userStorage->id
            || 117 == $userStorage->id // duy.tran
            || 115 == $userStorage->id // dinh.ly
            || WAREHOUSE == $userStorage->group_id
            || WAREHOUSE_LEADER == $userStorage->group_id
            || SALES_ADMIN_ACCESSORIES == $userStorage->group_id
            || CHECK_MONEY == $userStorage->group_id
            || KERRY_STAFF == $userStorage->group_id
            || FINANCE == $userStorage->group_id
            || KERRY_STAFF == $userStorage->group_id
            || CANCEL_ORDER == $userStorage->group_id
            || SALES_ADMIN_REGIONAL == $userStorage->group_id
        )) return false; // không chặn
        
        
        //return false;

        exit; // chặn
    }
}
    