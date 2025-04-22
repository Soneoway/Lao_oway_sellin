<?php
/**
* @author mot nguoi nao do
*/
class My_Discount extends My_Type_Enum
{
    const BVG = 1;
    const BVGPayment = 2;
    const BVGAccessories = 3;
    const DISCOUNT_TYPE_BVG = 1;
    const DISCOUNT_TYPE_BVG_KA = 3;
    const DISCOUNT_TYPE_BVG_ACCESSORIES = 5;

    public static $name = array(
        self::BVG => "Bảo vệ giá",
        self::BVGPayment => "Bảo vệ giá chuyển trả",
        self::BVGAccessories => "Bảo vệ giá phụ kiện",
    );
}