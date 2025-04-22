<?php 
/**
* 
*/
class My_Delivery_Type extends My_Type_Enum
{
    const Inhouse = 1;
    const Outside = 2;

    public static $name = array(
        self::Inhouse => 'Inhouse',
        self::Outside => 'Outside',
    );
}