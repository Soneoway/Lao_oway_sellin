<?php
/**
*
*/
class My_Delivery_Type extends My_Type_Enum
{
    const Inhouse               = 1;
    const Outside               = 2;
    const Customer_Pickup       = 3;
    const Hub_Pickup            = 4;
    const Returned_To_Warehouse = 5;

    public static $name = array(
        self::Inhouse               => 'Inhouse',
        self::Outside               => 'Outside',
        self::Customer_Pickup       => 'Customer Pickup',
        self::Hub_Pickup            => 'Hub Pickup',
        self::Returned_To_Warehouse => 'Returned to Warehouse',
    );
}