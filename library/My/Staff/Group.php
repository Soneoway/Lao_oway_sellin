<?php
/**
* @author buu.pham
* @create 2015-10-14T10:48:03+07:00
*/
class My_Staff_Group
{
    /**
     * Kiểm tra staff group có chứa group nào thuộc check_groups hay không
     *
     * @param  string $staff_group  [description]
     * @param  mixed $check_groups [description]
     * @return [type]               [description]
     */
    public static function inGroup($staff_group, $check_groups)
    {
        $groups = explode(',', $staff_group);
        $groups = is_array($groups) ? $groups : array();

        if (is_numeric($check_groups))
            return in_array($check_groups, $groups);

        if (is_array($check_groups)) {
            foreach ($groups as $key => $value) {
                if (in_array($value, $check_groups))
                    return true;
            }
        }

        return false;
    }

    public static function create_for_partner($staff_id,$staff_group){
        $groups = explode(',', $staff_group);
        $groups = is_array($groups) ? $groups : array();

        //DANH SACH NHOM DUOC LEN DON PARTNER
        $check_groups = array(
            SUPER_SALES_ADMIN,
            CHECK_MONEY,
            ADMINISTRATOR_ID
        );

        $check_staffs = array(
            822, //yennhi.pham
            466, //thucanh.tran
            723, //huynhngoc.nguyen
            1
        );

        if(count($groups)){
            foreach($groups as $key => $value){
                if(in_array($value,$check_groups)){
                   return true;
               }
            }
        }

        if($staff_id){
            if(in_array($staff_id,$check_staffs)){
                return true;
            }
        }

        return false;
    }

    public static function access($controller, $action, $module = 'default')
    {
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        if ($userStorage && is_array($userStorage->accesses)) {

            if ( ! ( $userStorage->id == SUPERADMIN_ID || $userStorage->group_id == ADMINISTRATOR_ID ) ) {
                if ( in_array( ( $module . '::' . $controller . '::' . $action ) , $userStorage->accesses ) )
                    return true;
            } else {
                return true;
            }
        }

        return false;

    }
}
