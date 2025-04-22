<?php
/**
* @author buu.pham
*/
class My_Carrier extends My_Type_Enum
{
    const Kerry = 1;
    const Saigon_Post = 2;

    public static $name = array(
        self::Kerry => "Kerry",
        self::Saigon_Post => "Saigon Post",
    );
}