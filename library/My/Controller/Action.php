<?php
class My_Controller_Action extends Zend_Controller_Action
{
    public function preDispatch() {
        // sanitize data
        foreach ($this->getRequest()->getParams() as $key => $value)
            if (is_string($value))
                $this->getRequest()->setParam($key, $this->sanitize($value));
    }

    private function sanitize($value)
    {
        return $this->UnicodeToHop2DungSan($value);
    }

    private function UnicodeToHop2DungSan($str)
    {
        if ( !is_string( $str) )
            return $str;

        $dungsan=array("à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă","ằ","ắ"
        ,"ặ","ẳ","ẵ","è","é","ẹ","ẻ","ẽ","ê","ề","ế","ệ","ể","ễ","ì","í","ị","ỉ","ĩ",
            "ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ"
        ,"ờ","ớ","ợ","ở","ỡ",
            "ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ",
            "ỳ","ý","ỵ","ỷ","ỹ",
            "đ",
            "À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă"
        ,"Ằ","Ắ","Ặ","Ẳ","Ẵ",
            "È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ",
            "Ì","Í","Ị","Ỉ","Ĩ",
            "Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ"
        ,"Ờ","Ớ","Ợ","Ở","Ỡ",
            "Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ",
            "Ỳ","Ý","Ỵ","Ỷ","Ỹ",
            "Đ","ê","ù","à");

        $tohop=array("à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă","ằ","ắ"
        ,"ặ","ẳ","ẵ","è","é","ẹ","ẻ","ẽ","ê","ề","ế","ệ","ể","ễ","ì","í","ị","ỉ","ĩ",
            "ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ"
        ,"ờ","ớ","ợ","ở","ỡ",
            "ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ",
            "ỳ","ý","ỵ","ỷ","ỹ",
            "đ",
            "À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă"
        ,"Ằ","Ắ","Ặ","Ẳ","Ẵ",
            "È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ",
            "Ì","Í","Ị","Ỉ","Ĩ",
            "Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ"
        ,"Ờ","Ớ","Ợ","Ở","Ỡ",
            "Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ",
            "Ỳ","Ý","Ỵ","ỷ","Ỹ",
            "Đ","ê","ù","à");

        return str_replace($tohop,$dungsan,$str);
    }


    public function convertSoToOp($so){

        $op = '';

        $op = str_replace('SO', 'OP', $so);
        $op = $this->textRemoveDash($op);

        return $op;
    }

    public function convertOpToSo($op){

        $so = '';

        if(preg_match('/SO/',$op) > 0){
            return 'not allow';
        }

        if(preg_match('/OP/',$op) > 0){

            if(strlen($op) > 8){
                $so = substr($op, 0, 8) . '-' . substr($op, 8);
            }else{
                $so = $op;
            }

        }else{

            if(strlen($op) > 6){
                $so = substr($op, 0, 6) . '-' . substr($op, 6);
            }else{
                $so = $op;
            }

        }

        $so = str_replace('OP', 'SO', $so);

        return $so;
    }

    public function textRemoveDash($text){

        return str_replace('-', '', $text);
    }

    public function cleanInt($string){
        $string = str_replace('', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^0-9]/', '', $string); // Removes special chars.
    }

    public function clean($string){
        $string = str_replace('', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public function getDayByDateTime($datetime){

        return date('D', strtotime($datetime));
    }

    public function convertDataInArrToArrayData($arrayData, $parameter){

        $arrayReturn = [];

        foreach ($arrayData as $key) {
            array_push($arrayReturn, $key[$parameter]);
        }

        return $arrayReturn;
    }

    public function checkConditionSendKerry($arrKerryCondition, $arrHolidayCondition, $warehouse_id, $provice_id, $datetime, $date, $ka_type, $d_id){

        // Big C
        // 49465 = บริษัท บิ๊กซี แฟรี่ จำกัด (สาขาที่ 00001)
        // 49466 = บริษัท พิษณุโลก บิ๊กซี 2015 จำกัด (พิษณุโลก)
        // 30344 = บริษัท บิ๊กซี ซูเปอร์เซ็นเตอร์ จำกัด (มหาชน)

        // Com7
        // 34119 = Brand shop By Com7 
        $block_d_id = [49465,49466,30344,34119];

        if(in_array($d_id, $block_d_id)){
            return false;
        }

        // By Pass Distributor
        if(isset($d_id) and $d_id){

            // KR-Dealer
            // 649 = บริษัท วิซแมกซ์ ช็อป จำกัด
            // 57  = เอสโมบาย (สาขาพระราม 2)
            // 1023    = ร้านทาโร่โมบายโซน
            // 1290    = แสตมป์ สาขาพัทยาเหนือ
            // 370 = กรณ์ เซอร์วิส (บางใหญ่)
            // 20755   = STAMP PHONE นวนคร
            // 8453    = บริษัท เบทเทอร์ อัพ จำกัด
            // 361 = เลค คอมมูนิเคชั่น บจก.
            // 38451   = บริษัท ไอบิส พลัส เน็ทเวอร์ค จำกัด
            // 25054   = สยามภัทรชัย จำกัด
            // 3172    = บริษัท พี.ที.อี. อินเตอร์กรุ๊ป จำกัด (สำนักงานใหญ่)
            // 1076    = บริษัท ป๊อปเทล จำกัด
            // 34649   = บริษัท สยามชัย เซอร์วิส จำกัด
            // 606 = บริษัท แกรมมี่ อะโกร จำกัด
            // 992 = บริษัท เพาเวอร์ฟูล คอมมูนิเคชั่น จำกัด
            // 34650   = บริษัท บูรพวัฒน์ คอมมิวนิเคชั่น จำกัด
            // 580 = บริษัท สตูดิโอ โฟน พลัส จำกัด

            // New Brandshop By KR-Dealer
            // 40429 => OPPO Brand Shop Big C Extra Pattaya Klang By Stamp(Smart Phone)
            // 40534 => Brand Shop Big C Extra Chaeng Watthana By S Mobile
            // 44152 => Brand Shop Robinson Sriracha By Powerfull
            // 44145 => Brand Shop Big C Nakhon Pathom By Ibiz
            // 44153 => Brand Shop Robinson Srisaman By Wizmax
            // 44149 => Brand Shop The Mall Bangkapi By Studio Phone
            // 41101 => Brand Shop Lotus Bang Pakong By Better Up
            // 41196 => Brandshop Zeer Rangsit By GSM (Poptel)

            $fix_d_id = [649,57,1023,1290,370,20755,8453,361,38451,25054,3172,1076,34649,606,992,34650,580,40429,40534,44152,44145,44153,44149,41101,41196];

            if((in_array($warehouse_id, [36,92,123,131,132,133]) || ($ka_type == '' && $d_id == '')) && in_array($d_id, $fix_d_id) && !in_array($date, $arrHolidayCondition) && !in_array($this->getDayByDateTime($datetime), ['Sun','Sat']) && in_array($ka_type, ['',1,27,32,38])){

                return true;
            }

        }

        //Not Geniuos And allow warehouse Kerry and Brandshop Warehouse at Kerry and OPEN MARKET
        //Not allow ka type
        if((in_array($warehouse_id, [36,92,123,131,132,133]) || ($ka_type == '' && $d_id == '')) && !in_array($provice_id, $arrKerryCondition) && !in_array($date, $arrHolidayCondition) && !in_array($this->getDayByDateTime($datetime), ['Sun','Sat']) && in_array($ka_type, ['',1,27,32,38])){

            return true;
        }

        return false;
    }

    public function checkConditionSendJNT($arrKerryCondition, $arrHolidayCondition, $warehouse_id, $provice_id, $datetime, $date, $ka_type, $d_id){

        // By Pass Distributor
        if(isset($d_id) and $d_id){

            // Big C
            // 49465 = บริษัท บิ๊กซี แฟรี่ จำกัด (สาขาที่ 00001)
            // 49466 = บริษัท พิษณุโลก บิ๊กซี 2015 จำกัด (พิษณุโลก)
            // 30344 = บริษัท บิ๊กซี ซูเปอร์เซ็นเตอร์ จำกัด (มหาชน)

            // Com7
            // 34119 = Brand shop By Com7 
            $fix_d_id = [49465,49466,30344,34119];

            if(in_array($d_id, $fix_d_id)){

                return true;
            }

        }

        //ไกล (อีสาน,เหนือ,ใต้)

        // กลาง
        // ลพบุรี 7
        // สิงห์บุรี 8
        // อ่างทอง 6

        // ตะวันตก
        // ตาก 50

        // เหนื่อ
        // เชียงราย 45
        // เชียงใหม่ 38
        // น่าน 43
        // พะเยา 44
        // แพร่ 42
        // แม่ฮ่องสอน 46
        // ลำปาง 40
        // ลำพูน 39
        // อุตรดิตถ์ 41

        // อีสาน
        // กาฬสินธุ์ 34
        // ขอนแก่น 28
        // ชัยภูมิ 25
        // นครพนม 36
        // นครราชสีมา 19
        // บึงกาฬ 77
        // บุรีรัมย์ 20
        // มหาสารคาม 32
        // มุกดาหาร 37
        // ยโสธร 24
        // ร้อยเอ็ด 33
        // เลย 30
        // สกลนคร 35
        // สุรินทร์ 21
        // ศรีสะเกษ 22
        // หนองคาย 31
        // หนองบัวลำภู 27
        // อุดรธานี 29
        // อุบลราชธานี 23
        // อำนาจเจริญ 26

        // ใต้
        // กระบี่ 64
        // ชุมพร 69
        // ตรัง 72
        // นครศรีธรรมราช 63
        // นราธิวาส 76
        // ปัตตานี 74
        // พังงา 65
        // พัทลุง 73
        // ภูเก็ต 66
        // ระนอง 68
        // สตูล 71
        // สงขลา 70
        // สุราษฎร์ธานี 67
        // ยะลา 75

        // $array_near_far = array('7','8','6','50','45','38','43','44','42','46','40','39','41','34','28','25','36','19','77','20','32','37','24','33','30','35','21','22','31','27','29','23','26','64','69','72','63','76','74','65','73','66','68','71','70','67','75');

        //Not Geniuos And allow warehouse Kerry and Brandshop Warehouse at Kerry and OPEN MARKET
        //Not allow ka type
        // if(in_array($warehouse_id, [36,92,123,131,132,133]) && in_array($provice_id, $array_near_far) && (in_array($this->getDayByDateTime($datetime), ['Sun','Sat']) || in_array($date, $arrHolidayCondition)) && in_array($ka_type, ['',1,27,32,38])){
        if((in_array($warehouse_id, [36,92,123,131,132,133]) || ($ka_type == '' && $d_id == '')) && (!in_array($this->getDayByDateTime($datetime), []) || in_array($date, $arrHolidayCondition)) && in_array($ka_type, ['',1,27,32,38])){

            return true;
        }

        return false;
    }

}