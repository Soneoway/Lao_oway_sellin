<?php
/**
* @author buu.pham
*/
class My_Carrier extends My_Type_Enum
{
    const Kerry     = 1;
    const RFE       = 2;
    // const Genious   = 3;
    const Road      = 4;
    const NKC       = 5;
    const YAS       = 6;
    const Sixtytwo  = 7;
    const Sutus     = 8;
    const JNT       = 9;

    public static $name = array(
        self::Kerry     => "Kerry",
        self::RFE       => "RFE",
        // self::Genious   => "Genious",
        self::Road      => "Road",
        self::NKC       => "NKC",
        self::YAS       => "YAS",
        self::Sixtytwo  => "Sixtytwo",
        self::Sutus     => "Sutus",
        self::JNT       => "J&T"
    );

    /*
    const Kerry_HCM = 1;
    const Kerry_HN = 3;
    const Kerry_DN = 4;
    const Saigon_Post_HCM = 2;
    const Saigon_Post_HN = 5;
    const Saigon_Post_DN = 6;

    public static $name = array(   
        self::Kerry_HCM => "Kerry HCM",
        self::Kerry_HN => "Kerry HN",
        self::Kerry_DN => "Kerry ĐN",
        self::Saigon_Post_HCM => "Saigon Post HCM",
        self::Saigon_Post_HN => "Saigon Post HN",
        self::Saigon_Post_DN => "Saigon Post ĐN",
    );
    */ 
}