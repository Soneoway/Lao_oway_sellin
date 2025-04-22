<?php
/**
*/
class My_Sale_Order_Type extends My_Type_Enum
{
    const Retailer = 1;
    const Demo     = 2;
    const Staff    = 3;
    const Lending  = 4;
    const APK  = 5;

    public static $name = array(
        self::Retailer => 'Retailer',
        self::Demo => 'Demo',
        self::Staff => 'Staff',
        self::Lending => 'Lending',
        self::APK => 'APK',
    );

    public static $class = array(
        self::Retailer => 'default',
        self::Demo => 'important',
        self::Staff => 'success',
        self::Lending => 'info',
        self::APK => 'APK',
    );

    public static function getLabel($type)
    {
        return sprintf('<span class="label label-%s">%s</span>', 
            isset(self::$class[$type]) ? self::$class[$type] : "default",
            isset(self::$name[$type]) ? self::$name[$type] : "###"
        );
    }
}
