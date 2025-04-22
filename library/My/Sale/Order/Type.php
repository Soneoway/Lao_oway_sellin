<?php
/**
*/
class My_Sale_Order_Type extends My_Type_Enum
{
    const Nomal = 1;
    const APK  = 2;
    const Staff    = 3;
    const Lending  = 4;
    const DEMO     = 5;

    public static $name = array(
        self::Nomal => 'Normal',
        self::APK => 'APK',
        self::Staff => 'Staff',
        self::Lending => 'Lending',
        self::DEMO => 'DEMO',
    );

    public static $class = array(
        self::Nomal => 'Normal',
        self::APK => 'APK',
        self::Staff => 'Staff',
        self::Lending => 'Lending',
        self::DEMO => 'DEMO',
    );

    public static function getLabel($type)
    {
        return sprintf('<span class="label label-%s">%s</span>', 
            isset(self::$class[$type]) ? self::$class[$type] : "default",
            isset(self::$name[$type]) ? self::$name[$type] : "###"
        );
    }

    public static function getHeader($type)
    {
        return sprintf('<span class="get_header">%s</span>', 
            isset(self::$class[$type]) ? self::$class[$type] : "default",
            isset(self::$name[$type]) ? self::$name[$type] : "###"
        );
    }
}
