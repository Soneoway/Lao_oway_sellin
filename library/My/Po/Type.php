<?php
/**
*/
class My_Po_Type extends My_Type_Enum
{
    const New_Shipment = 1;
    const Rework       = 2;

    public static $name = array(
        self::New_Shipment => 'New Shipment',
        self::Rework       => 'Rework',
        // self::Warehouse_Transfer => 'Warehouse Transfer',
    );

    public static $class = array(
        self::New_Shipment => 'default',
        self::Rework       => 'important',
        // self::Rework_Warehouse_Transfer => 'success',
    );

    public static function getLabel($type)
    {
        return sprintf('<span class="label label-%s">%s</span>',
            isset(self::$class[$type]) ? self::$class[$type] : "default",
            isset(self::$name[$type]) ? self::$name[$type] : "###"
        );
    }
}
