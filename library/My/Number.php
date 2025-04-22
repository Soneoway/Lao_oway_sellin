<?php 
/**
* 
*/
class My_Number
{
    const FIX = 0;

    public static function f($number, $fix = self::FIX)
    {
        if ( ! is_numeric($number) ) return false;

        //return number_format($number, $fix, ',', '.');
        return number_format($number,2);

    }

    public static function price_val($amount)
    {
        return $amount;
    }
    public static function floatval($num, $fix = self::FIX) {

        //return number_format($num,2);
        //return $num;
        return str_replace(',', '', $num);

        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos > 0 && $commaPos > 0) ? $dotPos : 
            ((($commaPos > $dotPos) && $dotPos > 0 && $commaPos > 0) ? $commaPos : false);
       
        if (!$sep) {
            return round(floatval(preg_replace("/[^0-9]/", "", $num)), $fix);
        } 

        return round(floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
        ), $fix);
    }

    public static function priceText($amount)
    {
        if($amount <=0)
        {
            return $textnumber="";
        }
        $Text=array("không", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín");
        $TextLuythua =array("","nghìn", "triệu", "tỷ", "ngàn tỷ", "triệu tỷ", "tỷ tỷ");
        $textnumber = "";
        $length = strlen($amount);

        for ($i = 0; $i < $length; $i++)
            $unread[$i] = 0;

        for ($i = 0; $i < $length; $i++)
        {
            $so = substr($amount, $length - $i -1 , 1);

            if ( ($so == 0) && ($i % 3 == 0) && ($unread[$i] == 0)){
                for ($j = $i+1 ; $j < $length ; $j ++)
                {
                    $so1 = substr($amount,$length - $j -1, 1);
                    if ($so1 != 0)
                        break;
                }

                if (intval(($j - $i )/3) > 0){
                    for ($k = $i ; $k <intval(($j-$i)/3)*3 + $i; $k++)
                        $unread[$k] =1;
                }
            }
        }

        for ($i = 0; $i < $length; $i++)
        {
            $so = substr($amount,$length - $i -1, 1);
            if ($unread[$i] ==1)
                continue;

            if ( ($i% 3 == 0) && ($i > 0))
                $textnumber = $TextLuythua[$i/3] ." ". $textnumber;

            if ($i % 3 == 2 )
                $textnumber = 'trăm ' . $textnumber;

            if ($i % 3 == 1)
                $textnumber = 'mươi ' . $textnumber;


            $textnumber = $Text[$so] ." ". $textnumber;
        }

        //Phai de cac ham replace theo dung thu tu nhu the nay
        $textnumber = str_replace("không mươi", "lẻ", $textnumber);
        $textnumber = str_replace("lẻ không", "", $textnumber);
        $textnumber = str_replace("mươi không", "mươi", $textnumber);
        $textnumber = str_replace("một mươi", "mười", $textnumber);
        $textnumber = str_replace("mươi năm", "mươi lăm", $textnumber);
        $textnumber = str_replace("mươi một", "mươi mốt", $textnumber);
        $textnumber = str_replace("mười năm", "mười lăm", $textnumber);

        return ucfirst($textnumber);
    }

    public static function product_price($priceFloat,$decimal_p=0)
    {
        $symbol = ' THB';
        $symbol_thousand = '.';
        $decimal_place = $decimal_p;
        $price = number_format($priceFloat, $decimal_place, ',', $symbol_thousand);
        return $price;
    }

}