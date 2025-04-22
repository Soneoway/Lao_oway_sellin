<?php
class Application_Model_StaffOrder extends Zend_Db_Table_Abstract
{
	protected $_name = 'staff_order';

    /**
     * [getIdNumber description]
     * @param  [type] $staff_id [description]
     * @return [type]           [description]
     * return first joined at
     * return id_number
     */
	public function getIdNumber($staff_id){

 		$db = Zend_Registry::get('db');
        $selectIdNumber = $db->select()
            ->from(array('a'=>HR_DB.'.staff'),array('a.*'))
            ->where('a.id = ?',$staff_id);
        $rowIdNumber = $db->fetchRow($selectIdNumber); 

        if(!$rowIdNumber OR intval($rowIdNumber['ID_number']) == 0){
            return false;
        }

        $select2  = $db->select()
        	->from(array('a'=>HR_DB.'.staff'),'a.*')
        	->where('a.ID_number = ?',$rowIdNumber['ID_number'])
        	->order('a.joined_at ASC')
        	->limit(1);
        $row = $db->fetchRow($select2);

        return array(
                'ID_number' => $row['ID_number'],
                'joined_at' => $row['joined_at'],
                'firstname' => $row['firstname'],
                'lastname'  => $row['lastname'],
            );
	}

	public function checkStaffBuyProduct($staff_id,$type = 1,$date = NULL,$current_sn = ''){
        //format date
        if($date == NULL){
            $date = date('Y-m-d');
        }else{
            $date = date('Y-m-d',strtotime($date));
        }

		$db             = Zend_Registry::get('db');

        $rowGetIdNumber = $this->getIdNumber($staff_id);
        if($rowGetIdNumber == false){
            return array('status'=>0,'message'=>'ID number not exist');
        }

		$id_number      = $rowGetIdNumber['ID_number'];
        $joined_at      = $rowGetIdNumber['joined_at'];

        //Khi mua 40% (type = 1)
        if($type == 1){

            $date_create      = date_create($date);
            $joined_at_create = date_create($joined_at);

            $diff             = date_diff($date_create,$joined_at_create);
            $balance          = intval($diff->format("%a")) + 1;
            $time             = intval($balance / STAFF_BUY_40);
            if($balance < 14){

                return array('status'=>0,'message'=>'Nhân viên phải đủ 14 ngày làm việc chính thức');
            }

            $days_from        = $time*STAFF_BUY_40;
            $days_to          = STAFF_BUY_40 + ($time*STAFF_BUY_40);

            $from_date = new DateTime($joined_at);
            $from_date->add(new DateInterval('P'.$days_from.'D'));
            $from_date_string = $from_date->format('Y-m-d');
            
            $to_date = new DateTime($joined_at);
            $to_date->add(new DateInterval('P'.$days_to.'D'));
            $to_date_string = $to_date->format('Y-m-d');
             
            //Kiểm tra trong thời gian đặt hàng có mua thêm nữa ko?
            $select3 = $db->select()
                ->from(array('a'=>'market'),array('*'))
                ->where('for_staff = ?',$staff_id)
                ->where('DATE(add_time) >= ?',$from_date_string)
                ->where('DATE(add_time) <= ?',$to_date_string)
                ->where('d_id = ?',OPPO_STAFF)
                ->where('type = ?',FOR_STAFFS)
                ->where('sale_off_percent = ?',40)
                ->where('canceled IS NULL OR canceled = 0')
                ->where('status = ?',1)
                ;

            if($current_sn){
                $select3->where('sn != ?',$current_sn);

            }    
           
            $row3 = $db->fetchRow($select3);
            if($row3){
                return array('status'=>0,'message'=>$rowGetIdNumber['firstname'].' '.$rowGetIdNumber['lastname'].' đã mua suất 40%');
            }

            // Nếu ko có trong list thi dc mua
            $select = $db->select()
                ->from(array('a'=>'staff_order'),array('a.*'))
                ->where('a.id_number = ?',$id_number)
                ->where('a.type = ?',1)
                ->where('a.date_buy >= ?',$from_date_string)
                ->where('a.date_buy <= ?',$to_date_string)
                ->where('a.status = ?',1)
                ->where('a.del = ?',0)
                ->where('for_partner = ?',2)
                ;

            if($current_sn){
                $select->where('sn != ? OR sn IS  NULL',$current_sn);
            }

            $row = $db->fetchRow($select);
            if($row){
                return array('status'=>0,'message'=>$rowGetIdNumber['firstname'].' '.$rowGetIdNumber['lastname'].' đã mua suất 40%');
            }
            
            return array('status'=>1,'message'=>'First time','time'=>1,'data'=>$rowGetIdNumber);
        }elseif(in_array($type,array(2,3,4))){
            return array('status'=>1,'message'=>'Done');
        }else{
            return array('status'=>0,'message'=>'false');
        }

    }
    
    public function checkStaffIngameBuyProduct($staff_ingame_idnumber,$type = 1,$date = NULL,$current_sn = ''){

        if(!$staff_ingame_idnumber){
            return array('status'=>0,'message'=>'ID number INGAME not exist');
        }

        $db             = Zend_Registry::get('db');
		$id_number      = trim($staff_ingame_idnumber);
        $date           = date('Y-m-d');

        /*
        $selectStaffInGame = $db->select()
                                ->from(array('a'=>'staff_ingame_order'),array('a.*'))
                                ->where('id_number = ?',$staff_ingame_idnumber);
        $rowStaffInGame = $db->fetchRow($selectStaffInGame);

        if(!$rowStaffInGame){
            return array('status'=>1,'message'=>'First time');
            //return array('status'=>0,'message'=>"Nhân viên với chứng minh $id_number không tồn tại");
        }

        $joined_at = $rowStaffInGame['joined_at'];
        */

        $selectStaff = $db->select()
                ->from(array('a'=>'staff_order'),array('*'))
                ->where('id_number = ?',$id_number)
                ->where('company_id = ?',4)
                ->where('del = ?',0)
                ->where('type = ?',1)
                ->where('a.status = ?',1)
                ->order('date_buy DESC')
                ->limit(1)
        ;

        $staffInGame = $db->fetchRow($selectStaff);
        if(!$staffInGame){
            return array('status'=>1,'message'=>'First time');
        }

        $joined_at = $staffInGame['date_buy'];

        if($type == 1){

            $date_create      = date_create($date);
            $joined_at_create = date_create($joined_at);

            $diff             = date_diff($date_create,$joined_at_create);
            $balance          = intval($diff->format("%a")) + 1;
            $time             = intval($balance / STAFF_BUY_40);

            $mod              = $balance % STAFF_BUY_40 ;
            $days_from        = $time*STAFF_BUY_40;
            $days_to          = STAFF_BUY_40 + ($time*STAFF_BUY_40);

            $from_date = new DateTime($joined_at);
            $from_date->add(new DateInterval('P'.$days_from.'D'));
            $from_date_string = $from_date->format('Y-m-d');

            $to_date = new DateTime($joined_at);
            $to_date->add(new DateInterval('P'.$days_to.'D'));
            $to_date_string = $to_date->format('Y-m-d');

            //Kiểm tra trong thời gian đặt hàng có mua thêm nữa ko?
            $select3 = $db->select()
                ->from(array('a'=>'market'),array('*'))
                ->where('outsite_cmnd = ?',$id_number)
                ->where('d_id = ?',OPPO_INGAME)
                ->where('type = ?',FOR_STAFFS)
                ->where('sale_off_percent = ?',40)
                ->where('canceled IS NULL OR canceled = 0')
                ->where('isbacks IS NULL OR isbacks = 0')
                ->where('status = ?',1)
                ;
            if($current_sn){
                $select3->where('sn != ?',$current_sn);
            }

            $row3 = $db->fetchRow($select3);
            if($row3){
                return array('status'=>0,'message'=>'Nhân viên: '.$id_number.' đã đặt mua suất 40%');
            }

            // Nếu ko có trong list thi dc mua
            $select = $db->select()
                ->from(array('a'=>'staff_order'),array('a.*'))
                ->where('a.id_number = ?',$id_number)
                ->where('a.type = ?',1)
                ->where('a.date_buy >= ?',$from_date_string)
                ->where('a.date_buy <= ?',$to_date_string)
                ->where('a.status = ?',1)
            ;
            $row = $db->fetchRow($select);

            if($row){
                return array('status'=>0,'message'=>$id_number.' đã mua suất 40%');
            }
            
            return array('status'=>1,'message'=>'First time','time'=>1,'data'=>$id_number);

        }elseif(in_array($type,array(2,3,4))){
            return array('status'=>1,'message'=>'Done');
        }else{
            return array('status'=>0,'message'=>'false');
        }

    }

    public function save($params = array(),$sn){

        $db = Zend_Registry::get('db');
        $userStorage          = Zend_Auth::getInstance()->getStorage()->read();

        //Thong tin nhan vien mua may
        $id_staff             = isset($params['id_staff']) ? $params['id_staff'] : NULL;
        $staff_num            = isset($params['staff_num']) ? $params['staff_num'] : NULL;
        $payment_dates        = isset($params['payment_date']) ? $params['payment_date'] : null;
        $name_staff_ingames   = isset($params['name_staff_ingame']) ? $params['name_staff_ingame'] : null;
        $cmnd_staff_ingames   = isset($params['cmnd_staff_ingame']) ? $params['cmnd_staff_ingame'] : null;
        $shipment_types       = isset($params['shipment_type']) ? $params['shipment_type'] : null;
        $sophieuthus          = isset($params['sophieuthu']) ? $params['sophieuthu'] : null;
        $sotienthuctes        = isset($params['sotienthucte']) ? $params['sotienthucte'] : null;
        $product_color_key    = isset($params['product_color_key']) ? $params['product_color_key'] : null;
        $for_partner          = isset($params['for_partner']) ? intval($params['for_partner']) : 0;
        $shipment_id          = isset($params['shipment_id']) ? intval($params['shipment_id']) : 0;


        //data market
        $select_market = $db->select()
            ->from(array('a'=>'market'),array('a.*'))
            ->where('a.sn = ?',$sn)
            ->where('canceled IS NULL OR canceled = 0')
            ->where('isbacks IS NULL OR isbacks = 0');
        $markets = $db->fetchAll($select_market);

        if($sn){

            $this->deleteByMarketSn($sn);
        }

        if(count($markets) == 0){
            return array('status'=>1,'message'=>'Done');
        }

        //Neu ko phai la nhan vien mua may
        if (in_array($markets[0]['d_id'], array(OPPO_STAFF, OPPO_INGAME)) AND $markets[0]['type'] == FOR_STAFFS AND $markets[0]['for_partner'] == 2) {
            //chi luu khi la don hang danh cho nhan vien
            //xóa danh sách cũ

        } else {
            return array('status' => 1, 'message' => 'Done');
        }

        foreach($markets as $market) {
            $good_id        = $market['good_id'];
            $good_color     = $market['good_color'];
            $date_buy       = $market['add_time'];
            $d_id           = $market['d_id'];

            $type = 4;
            if ($market['sale_off_percent'] == 40) {
                $type = 1;
            }elseif ($market['sale_off_percent'] == 15) {
                $type = 2;
            }
            elseif ($market['shipment_id'] != 0 && $market['shipment_id'] != null) {
                $type = 3;
            }elseif ($market['sale_off_percent'] == 30) {
                $type = 5;
            }


            $_product_color_key = $market['good_id'] . '_' . $market['good_color'];
            $data_staff_order = array();
            foreach ($product_color_key as $k => $v) {
                if ($v == $_product_color_key) {
                    if ($d_id == OPPO_STAFF) {

                        $IdNumber = $this->getIdNumber($id_staff[$k]);
                        $data_staff_order = array(
                            'market_id'          => $market['id'],
                            'sn'                 => $sn,
                            'id_number'          => $IdNumber['ID_number'],
                            'staff_id'           => $id_staff[$k],
                            'name'               => $IdNumber['firstname'] . ' ' . $IdNumber['lastname'],
                            'good_id'            => $good_id,
                            'good_color'         => $good_color,
                            'num'                => $staff_num[$k],
                            'payment_date'       => (isset($payment_dates[$k]) AND $payment_dates[$k]) ? My_Date::normal_to_mysql($payment_dates[$k]) : NULL,
                            'shipment_type'      => $shipment_types[$k],
                            'actual_amount_paid'  => My_Number::floatval($sotienthuctes[$k]),
                            'number_sales_order' => $sophieuthus[$k],
                            'type'               => $type,
                            'date_buy'           => $date_buy,
                            'company_id'         => 1,
                            'num'                => $staff_num[$k],
                            'for_partner'        => $for_partner,
                            'shipment_id'        => $shipment_id
                        );
                    } elseif ($d_id == OPPO_INGAME) {
                        $data_staff_order = array(
                            'market_id'          => $market['id'],
                            'sn'                 => $sn,
                            'id_number'          => $cmnd_staff_ingames[$k],
                            'staff_id'           => $id_staff[$k],
                            'name'               => $name_staff_ingames[$k],
                            'good_id'            => $good_id,
                            'good_color'         => $good_color,
                            'num'                => $staff_num[$k],
                            'payment_date'       => (isset($payment_dates[$k]) AND $payment_dates[$k]) ? My_Date::normal_to_mysql($payment_dates[$k]) : NULL,
                            'shipment_type'      => $shipment_types[$k],
                            'actual_amount_paid'  => My_Number::floatval($sotienthuctes[$k]),
                            'number_sales_order' => $sophieuthus[$k],
                            'type'               => $type,
                            'date_buy'           => $date_buy,
                            'company_id'         => 4,
                            'num'                => $staff_num[$k],
                            'for_partner'        => $for_partner,
                            'shipment_id'        => $shipment_id
                        );
                    }

                    try {
                        $r = $this->insert($data_staff_order);
                    } catch (Exception $e) {
                        return array('status' => 0, 'message' => $e->getMessage());
                    }
                }
            }
        }
        
        return array('status'=>1,'message'=>'Done');
    		
    }

    public function fetchPagination($page, $limit, &$total, $params){
        $db         = Zend_Registry::get('db');
        $from_date = (isset($params['from_date']) AND $params['from_date']) ? My_Date::normal_to_mysql($params['from_date']) : NULL;
        $to_date = (isset($params['to_date']) AND $params['to_date']) ? My_Date::normal_to_mysql($params['to_date']) : NULL;
        $sql_params = array(
            $params['sn'],
            $params['staff_name'],
            intval($params['staff_id']),
            $params['staff_code'],
            $params['id_number'],
            intval($params['type']),
            intval($page),
            $limit,
            $from_date,
            $to_date,
            ( isset($params['company_id']) AND $params['company_id'] ) ? $params['company_id'] : NULL,
            isset($params['for_partner']) ? intval($params['for_partner']) : NULL
        );

        $sql = 'CALL proc_staff_order(?,?,?,?,?,?,?,?,?,?,?,?,@total)';
        $stmt = $db->query($sql,$sql_params);
        $list = $stmt->fetchAll();
        $stmt->closeCursor();
        $total = $db->fetchOne('select @total');
        return $list;

    }

    /**
     * [gán imei theo sn]
     * @param  [type] $sn [description]
     * @return [type]     [description]
     */

    public function assignImei($sn){
        $db = Zend_Registry::get('db');

        $select_check = $db->select()
            ->from(array('a'=>'market'),array('a.*'))
            ->where('a.status = ?',1)
            ->where('a.sn = ?',$sn);
        $market = $db->fetchRow($select_check);
        if(!$market){
            return array('status'=>0,'message'=>'Done');
        }

        //kiem tra dieu kien co phai la nhan vien mua may hay ko?
        if(!in_array(intval($market['d_id']), array(OPPO_INGAME,OPPO_STAFF)) ){
            return array('status'=>0,'message'=>'Done');    
        }

        //Kiem tra lai co phai la partner hay ko
        if(intval($market['for_partner']) != 2){
            return array('status'=>0,'message'=>'Done');
        }

        // danh sach imei group theo good_id, good_color
        $select = $db->select()
                    ->from(array('a'=>'imei'),array('a.*'))
                    ->where('a.sales_sn = ?',$sn);
        $result = $db->fetchAll($select);
        $imeis = array();
        foreach($result as $item){
            $imeis[$item['good_id']][$item['good_color']][] = $item['imei_sn'];
        }

        // danh sach nhan vien mua may, moi may 1 dong
        $select_staff_order = $db->select()
                    ->from(array('a'=>'staff_order'),array('a.*','b.cat_id'))
                    ->join(array('b'=>'market'),'a.market_id = b.id',array())
                    ->where('a.sn = ?',$sn)
                    ->where('a.del = 0',0)
                    ->order(array('a.good_id','a.good_color'))
                    ;
        $result_staff_order = $db->fetchAll($select_staff_order);
        $staff_orders = array();
        $staff_order_id_not_phone = array(); // danh sách staff_id không phải điện thoại

        foreach($result_staff_order as $item){
            $staff_orders[$item['cat_id']][$item['good_id']][$item['good_color']][] = $item;
            if($item['cat_id'] != PHONE_CAT_ID){
                $staff_order_id_not_phone[] = $item['id'];
            }
        }

        try {

            // Update imei
            if( isset($staff_orders[PHONE_CAT_ID]) ){
                foreach($imeis as $key_good_id => $good_colors){
                    foreach($good_colors as $key_good_color => $arr_imei){
                        $tmp = $arr_imei;
                        foreach($staff_orders[PHONE_CAT_ID][$key_good_id][$key_good_color] as $key_staff_order => $value_staff_order){
                            $imei_s = array();
                            for($i=1; $i <= $value_staff_order['num'];$i++){
                                $imei_s[] = array_shift($tmp);
                            }

                            $where = $this->getAdapter()->quoteInto('id = ?',$value_staff_order['id']);
                            $data_update = array(
                                'imei' => implode(',',$imei_s),
                                'status' => 1
                            );
                            $this->update($data_update,$where);
                        }
                    }
                }
            }

            // Insert PK and ...
            if( count($staff_order_id_not_phone) > 0 ){
                $whereNotPhone = $this->getAdapter()->quoteInto('id IN (?)',$staff_order_id_not_phone);
                $this->update(array('status'=>1),$whereNotPhone);
            }

            return array('status'=>0,'message'=>'Done');
        } catch (Exception $e) {
            return array('status'=>-1,'message'=>$e->getMessage());
        }

        return array('status'=>0,'message'=>'Done');

    }

    public function deleteByMarketId($market_id){
        $where = $this->getAdapter()->quoteInto('market_id = ?',$market_id);
        $this->delete($where);
    }

    public function deleteByMarketSn($market_sn){
        $where = $this->getAdapter()->quoteInto('sn = ?',$market_sn);
        $this->delete($where);
    }



}