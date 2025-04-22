<?php
/**
*
*/
class My_Finance_Service extends My_Type_Enum
{
    const ServiceHCM            = 1;
    const ServiceHaNoi2         = 2;
    const ServiceHaNoi1         = 3;
    const DaNang                = 4;

    public static $name = array(
        self::ServiceHCM               => 'ServiceHCM',
        self::ServiceHaNoi2            => 'Service Hà Nội 2',
        self::ServiceHaNoi1            => 'Service Hà nội 1',
        self::DaNang                   => 'Service Đà Nẵng'
    );
}