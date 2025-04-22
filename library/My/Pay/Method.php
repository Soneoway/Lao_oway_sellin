<?php 
/**
* Payment Method
* Luc dau chi danh cho dtdd
*/
class My_Pay_Method
{
    const CREDIT_30     = 1;
    const CREDIT_32     = 2;
    const DEBIT         = 3;
    const CREDIT_7      = 4;

    public static $date = array(
        self::CREDIT_30 => 30,
        self::CREDIT_32 => 32,
        self::DEBIT     => 0,
        self::CREDIT_7  => 7
    );
    public static $name = array(
        self::CREDIT_30 => 'CREDIT 30 DAYS',
        self::CREDIT_32 => 'CREDIT 30 DAYS',
        self::DEBIT     => 'DEBIT 30 DAYS',
        self::CREDIT_7  => 'CREDIT 7 DAYS'
    );

    public static function getNote($method)
    {
        if(self::$date[$method] == 0)
            return null;
        $paytime = self::$date[$method] - 1; // tru ngay hoa don
        $date_payment = date('d-m-Y', strtotime("+$paytime days"));
        $note = $date_payment;
        return $note;
    }

}
