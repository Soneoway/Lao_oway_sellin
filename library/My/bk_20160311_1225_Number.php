<?php 
/**
* 
*/
class My_Number
{
    public static function f($number)
    {
        if ( ! is_numeric($number) ) return false;

        return number_format($number, 2, '.', ',');
    }

    public static function floatval($num) {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos : 
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
       
        if (!$sep) {
            return round(floatval(preg_replace("/[^0-9]/", "", $num)), 2);
        } 

        return round(floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
        ), 2);
    }

    public static function dateThai($strDate, $type)
    {
        $strYear = date("Y",strtotime($strDate))+543;
        $strMonth= date("m",strtotime($strDate));
        $strDay= date("d",strtotime($strDate));
        if(isset($type))
        {
            $strHour= date("H",strtotime($strDate));
            $strMinute= date("i",strtotime($strDate));
            $strSeconds= date("s",strtotime($strDate));
            return "$strYear-$strMonth-$strDay  $strHour:$strMinute:$strSeconds";
        }
        else
        {
            return "$strYear-$strMonth-$strDay";
        }
    }

    public static function bahtEng($thb) {
           list($thb, $ths) = explode('.', $thb);
           $ths = substr($ths.'00', 0, 2);
           $thb = Currency::engFormat(intval($thb)).' Baht';
           if (intval($ths) > 0) {
            $thb .= ' and '.Currency::engFormat(intval($ths)).' Satang';
           }
           return $thb;
     }
 
  public static function bahtThai($thb) {
      $ths = '';
      
      if(isset($thb) and $thb)
      {
           list($thb, $ths) = explode('.', $thb);
           $ths = substr($ths.'00', 0, 2);
           $thaiNum = array('', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า');
           $unitBaht = array('บาท', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
           $unitSatang = array('สตางค์', 'สิบ');
           $THB = '';
           $j = 0;
           for ($i = strlen($thb) - 1; $i >= 0; $i--, $j++) {
            $num = $thb[$i];
            $tnum = $thaiNum[$num];
            $unit = $unitBaht[$j];
            switch (true) {
             case $j == 0 && $num == 1 && strlen($thb) > 1:
              $tnum = 'เอ็ด';
              break;
             case $j == 1 && $num == 1:
              $tnum = '';
              break;
             case $j == 1 && $num == 2:
              $tnum = 'ยี่';
              break;
             case $j == 6 && $num == 1 && strlen($thb) > 7:
              $tnum = 'เอ็ด';
              break;
             case $j == 7 && $num == 1:
              $tnum = '';
              break;
             case $j == 7 && $num == 2:
              $tnum = 'ยี่';
              break;
             case $j != 0 && $j != 6 && $num == 0:
              $unit = '';
              break;
            }
            $S = $tnum.$unit;
            $THB = $S.$THB;
           }
           if ($ths == '00') {
            $THS = 'ถ้วน';
           } else {
            $j = 0;
            $THS = '';
            for ($i = strlen($ths) - 1; $i >= 0; $i--, $j++) {
             $num = $ths[$i];
             $tnum = $thaiNum[$num];
             $unit = $unitSatang[$j];
             switch (true) {
              case $j == 0 && $num == 1 && strlen($ths) > 1:
               $tnum = 'เอ็ด';
               break;
              case $j == 1 && $num == 1:
               $tnum = '';
               break;
              case $j == 1 && $num == 2:
               $tnum = 'ยี่';
               break;
              case $j != 0 && $j != 6 && $num == 0:
               $unit = '';
               break;
             }
             $S = $tnum.$unit;
             $THS = $S.$THS;
            }
           }
           return $THB.$THS;
  }}
      public static function product_price($priceFloat,$decimal_p=0)
    {
        $symbol = ' VND';
        $symbol_thousand = '.';
        $decimal_place = $decimal_p;
        $price = number_format($priceFloat, $decimal_place, ',', $symbol_thousand);
        return $price;
    }

}
