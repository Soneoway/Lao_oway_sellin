<?php 
/**
* 
*/
class My_Delivery_Order_Status extends My_Type_Enum
{
    const Waiting = 1;
    const On_The_Way = 2;
    const Delivered = 3;
    const Deleted = 4;

    public static $name = array(
        self::Waiting => 'Waiting',
        self::On_The_Way => 'On The Way',
        self::Delivered => 'Delivered',
        self::Deleted => 'Deleted',
    );
}