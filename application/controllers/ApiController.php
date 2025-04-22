<?php
class ApiController extends My_Controller_Action{

	public function savePreList1Action()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-pre-sales-order-list.php';
    }

	public function savePreListAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-pre-sales-order-list.php';
    }

    public function checkImeiAction(){

    	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

    	$from_data =json_decode(file_get_contents("php://input"));

        $imei = '';

        if(isset($from_data->id) && $from_data->id){
			$imei = $from_data->id;
		}

		$db = Zend_Registry::get('db');

		$select = $db->select()
        ->from(array('i' => 'warehouse.imei'),array('i.distributor_id'))
        ->joinleft(array('t'=>'hr.timing_sale'),'t.imei = i.imei_sn',array('t.id'))
        ->joinleft(array('lfcr'=>'warehouse.log_free_case_reno'),'lfcr.imei = i.imei_sn and lfcr.status = 1',array('log_id' => 'lfcr.id','imei' => 'lfcr.imei'))
        ->joinleft(array('m'=>'warehouse.market'),'m.sn = lfcr.sn',array('so' => 'm.sn_ref'))
        ->joinleft(array('kt'=>'warehouse.kerry_transaction'),'kt.sn = lfcr.sn and kt.type = 1 and kt.status = 7',array('kt.tracking_no','delivery' => new Zend_Db_Expr('CASE WHEN delivery_type = 1 THEN "Kerry" WHEN delivery_type = 2 THEN "J&T" ELSE NULL END')))
        ->where('i.imei_sn=?',$imei)
	    ->where('i.distributor_id IS NOT NULL',true)
        ->where('i.good_id=?',403) // CPH1919 RENO 10X
        ->where('i.good_color=?',8) // ชมพู
        ->group('m.sn');
        ;

    	$result = $db->fetchRow($select);

    	if($result){
    		echo json_encode($result,JSON_UNESCAPED_UNICODE);
			exit();
    	}

    	echo json_encode([],JSON_UNESCAPED_UNICODE);
		exit();
    }

    public function getProvinceAction(){

    	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

		$QSP = new Application_Model_ShippingProvinces();
		$getProvince = $QSP->getProvinceAPI();

		if($getProvince){
			echo json_encode($getProvince,JSON_UNESCAPED_UNICODE);
			exit();
		}

		echo json_encode([],JSON_UNESCAPED_UNICODE);
		exit();
    }

    public function getDistrictAction(){

    	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

    	$from_data =json_decode(file_get_contents("php://input"));

		$province_id = '';

		if(isset($from_data->id) && $from_data->id){
			$province_id = $from_data->id;
		}

		$QSA = new Application_Model_ShippingAmphures();

		$getDistrict = $QSA->getAmphuresAPI($province_id);

		if($getDistrict){
			echo json_encode($getDistrict,JSON_UNESCAPED_UNICODE);
			exit();
		}

		echo json_encode([],JSON_UNESCAPED_UNICODE);
		exit();
    }

    public function getSubDistrictAction(){

    	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

    	$from_data =json_decode(file_get_contents("php://input"));

		$district_id = '';

		if(isset($from_data->id) && $from_data->id){
			$district_id = $from_data->id;
		}
		$QSD = new Application_Model_ShippingDistricts();

		$getSubDistrict = $QSD->getDistrictsAPI($district_id);

		if($getSubDistrict){
			echo json_encode($getSubDistrict,JSON_UNESCAPED_UNICODE);
			exit();
		}

		echo json_encode([],JSON_UNESCAPED_UNICODE);
		exit();
    }

    public function getZipcodeAction(){

    	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

    	$from_data =json_decode(file_get_contents("php://input"));

		$subDistrict_id = '';

		if(isset($from_data->id) && $from_data->id){
			$subDistrict_id = $from_data->id;
		}
		$QSZ = new Application_Model_ShippingZipcodes();

		$getSubDistrict = $QSZ->getZipCodeAPI($subDistrict_id);

		if($getSubDistrict){
			echo json_encode($getSubDistrict,JSON_UNESCAPED_UNICODE);
			exit();
		}

		echo json_encode([],JSON_UNESCAPED_UNICODE);
		exit();
    }

    public function createOrderFreeRenoAction(){

    	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

		try {

    		if(!isset(json_decode(file_get_contents("php://input"))->id) || !json_decode(file_get_contents("php://input"))->id){
    			http_response_code(400);
	    		echo json_encode(['status' => 400, 'msg' => 'Invalid form data!'],JSON_UNESCAPED_UNICODE);
		    	$db->rollback();
	    		exit();
    		}

	    	$from_data = json_decode(file_get_contents("php://input"))->id;

	    	// START : Security Check

			$lifetime = '';
			$salt = '';
			$token = '';

			if(isset($from_data->lifetime) && $from_data->lifetime){
				$lifetime = $from_data->lifetime;
			}

			if(isset($from_data->salt) && $from_data->salt){
				$salt = $from_data->salt;
			}

			if(isset($from_data->token) && $from_data->token){
				$token = $from_data->token;
			}

			unset($from_data->lifetime);
			unset($from_data->salt);
			unset($from_data->token);

	    	$check_data = json_encode($from_data);

			if(!$check_data||!$lifetime||!$salt||!$token){
			    http_response_code(400);
	    		echo json_encode(['status' => 400, 'msg' => 'bad parameter!'],JSON_UNESCAPED_UNICODE);
	    		exit();
			}

			if(!$this->checkToken($check_data,$lifetime,$salt,$token)){
			    http_response_code(400);
	    		echo json_encode(['status' => 400, 'msg' => 'Invalid token!'],JSON_UNESCAPED_UNICODE);
	    		exit();
			}

	    	// END : Security Check

	    	$db = Zend_Registry::get('db');

	    	$time_now = date('Y-m-d H:i:s');

	    	$system_id = 520;

	    	$warehouse_id = 36;

	    	// Category Accessories
	    	$good_cat = 12;

	    	//Product Name 06HC0019(Reno 10X Sunset Rose  Mobile Phone Case)
	    	$good_id = 449;

	    	// Product Color P/ชมพู
	    	$good_color = 8;

	    	$imei = '';
	    	$first_name = '';
	    	$last_name = '';
	    	$phone = '';
	    	$address = '';
	    	$province = '';
	    	$district = '';
	    	$sub_district = '';
	    	$zipcode = '';

	    	if(isset($from_data->imei) && $from_data->imei){
	    		$imei = trim($from_data->imei);
	    	}

	    	if(isset($from_data->first_name) && $from_data->first_name){
	    		$first_name = trim($from_data->first_name);
	    	}

	    	if(isset($from_data->last_name) && $from_data->last_name){
	    		$last_name = trim($from_data->last_name);
	    	}

	    	if(isset($from_data->phone) && $from_data->phone){
	    		$phone = trim($from_data->phone);
	    	}

	    	if(isset($from_data->address) && $from_data->address){
	    		$address = trim($from_data->address);
	    	}

	    	if(isset($from_data->province) && $from_data->province){
	    		$province = trim($from_data->province);
	    	}

	    	if(isset($from_data->district) && $from_data->district){
	    		$district = trim($from_data->district);
	    	}
	    	if(isset($from_data->sub_district) && $from_data->sub_district){
	    		$sub_district = trim($from_data->sub_district);
	    	}

	    	if(isset($from_data->zipcode) && $from_data->zipcode){
	    		$zipcode = trim($from_data->zipcode);
	    	}

	    	if($imei == '' || $first_name == '' || $last_name == '' || $phone == '' || $address == '' || $province == '' || $district == '' || $sub_district == '' || $zipcode == ''){
	    		http_response_code(400);
	    		echo json_encode(['status' => 400, 'msg' => 'Invalid form data!'],JSON_UNESCAPED_UNICODE);
		    	$db->rollback();
	    		exit();
	    	}

	    	// START : Re-Check Data

	    	$select = $db->select()
	        ->from(array('i' => 'warehouse.imei'),array('i.distributor_id'))
	        ->joinleft(array('t'=>'hr.timing_sale'),'t.imei = i.imei_sn',array('t.id'))
	        ->joinleft(array('lfcr'=>'warehouse.log_free_case_reno'),'lfcr.imei = i.imei_sn and lfcr.status = 1',array('log_id' => 'lfcr.id','imei' => 'lfcr.imei'))
	        ->joinleft(array('m'=>'warehouse.market'),'m.sn = lfcr.sn',array('so' => 'm.sn_ref'))
	        ->where('i.imei_sn=?',$imei)
	        ->where('i.distributor_id IS NOT NULL',true)
	        ->where('i.good_id=?',403) // CPH1919 RENO 10X
	        ->where('i.good_color=?',8) // ชมพู
	        ->group('m.sn');
	        ; 

    		$data_imei = $db->fetchRow($select);

	        if((!isset($data_imei['id']) || !$data_imei['id']) && $data_imei['distributor_id'] != '11293'){
				http_response_code(400);
	    		echo json_encode(['status' => 400, 'msg' => 'IMEI Not Find. (ไม่พบเลข IMEI ในระบบ)'],JSON_UNESCAPED_UNICODE);
				exit();
			}

			if(isset($data_imei['log_id']) && $data_imei['log_id']){
				http_response_code(400);
	    		echo json_encode(['status' => 400, 'msg' => 'IMEI Duplicate (มี IMEI นี้อยู่ในระบบแล้ว)'],JSON_UNESCAPED_UNICODE);
				exit();
			}

	    	// END : Re-Check Data

    		$db->beginTransaction();

	    	// START : Add distributor

	        $title = $first_name . ' ' . $last_name;

	        $QDistributor  = new Application_Model_Distributor();

	        $store_code = '';

	        $pefix = 'EM';

	        if($store_code==''){
	            for($i=0;$i<3;$i++){ 
	                if($store_code==''){
	                    $store_code = $QDistributor->getDistributorCode($db,$pefix);
	                }
	            }
	        }

	    	$data_distributor = array(
				'title' => $title,
				'rank' => 3,
				'region' => 5902,
				'district' => 5903,
				'unames' => $title,
				'name' => $title,
				'tel' => $phone,
				'add' => $address,
				'add_tax' => $address,
				'add_time' => $time_now,
				'mst_sn' => '0000000000000',
				'warehouse_id' => $warehouse_id,
				'store_code' => $store_code,
				'credit_amount' => 0.00,
				'credit_type' => 1,
				'retailer_type' => 1,
				'branch_no' => '00000',
				'create_by' => $system_id,
				'create_date' => $time_now,
				'update_by' => $system_id,
				'update_date' => $time_now,
				'sales_ch' => 'EM',
				'ka_type' => 1,
				'group_id' => 6,
				'activate' => 1
			);

	        $d_id = $QDistributor->insert($data_distributor);

	        // END : Add distributor

	        // START : Add Shipping Address

	    	$data_address = array(
	            'contact_name'  => $title,
	            'address'       => $address,
	            'province_id'   => $province,
	            'amphures_id'   => $district,
	            'districts_id'  => $sub_district,
	            'zipcodes'      => $zipcode,
	            'phone'         => $phone,
	            'd_id'          => $d_id,
	            'created_at'    => $time_now,
	            'created_by'    => $system_id
	            );

	        $ShippingAddress = new Application_Model_ShippingAddress();

	        $shipping_id = $ShippingAddress->insert($data_address);

	        // END : Add Shipping Address

	        // START : Update Shipping Address Default

            $where_update_distributor_shipping = $QDistributor->getAdapter()->quoteInto('id = ?', $d_id);
            $QDistributor->update(['shipping_add_default' => $shipping_id],$where_update_distributor_shipping);

	        // END : Update Shipping Address Default

	        // START : Create Order

        	$QMarket = new Application_Model_Market();

            $sn = date('YmdHis') . substr(microtime(), 2, 4);

            $data_market = array(
				'market_general_id' => 0,
				'sn' => $sn,
				'user_id' => $system_id,
				'd_id' => $d_id,
				'num' => 1,
				'add_time' => $time_now,
				'price' => 0.00,
				'total' => 0.00,
				'shipping_yes_time' => $time_now,
				'pay_time' => $time_now,
				'shipping_yes_id' => $system_id,
				'pay_user' => $system_id,
				'good_color' => $good_color,
				'good_id' => $good_id,
				'cat_id' => $good_cat,
				'difference' => 0.00,
				'price_clas' => '3',
				'isbatch' => 1,
				'isbacks' => 0,
				'warehouse_id' => $warehouse_id,
				'salesman' => $system_id,
				'status' => 1,
				'life_time' => 86400,
				'type' => 3,
				'sale_off_percent' => 100,
				'canceled' => 0,
				'rebate_price' => 0.00,
				'service' => 0,
				'campaign' => 0,
				'last_updated_at' => $time_now,
				'last_updated_by' => $system_id,
				'for_partner' => 2,
				'confirm_so' => 1,
				'delivery_fee' => 0.00,
				'payment_type' => 'CA',
				'sales_catty_id' => 0,
				'sales_confirm_date' => $time_now,
				'sales_confirm_id' => $system_id,
				'finance_confirm_date' => $time_now,
				'finance_confirm_id' => $system_id,
				'shipping_address' => $shipping_id,
				'spc_discount' => 0.00,
				'spc_discount_phone' => 0,
				'spc_discount_acc' => 0,
				'spc_discount_digital' => 0,
				'total_spc_discount' => 0.00,
				'price_ext' => 0.00,
				'total_ext' => 0.00,
				'is_kerry' => 0,
				'pay_group' => 0,
				'staff_code' => 0,
				'buy_return' => 0,
				'discount_buy_return' => 0.00
            );

        	$QMarket->insert($data_market);

	        // END : Create Order

	        //START : Corn Gen So

            $QCS = new Application_Model_CronSo();

            $cron_data = array(
                'sn' => $sn,
                'status' => 1,
                'created_date' => $time_now
            );

            $QCS->insert($cron_data);

	        //END : Corn Gen So

	        // START : Create Checkmoney

	        $QCheckmoney = new Application_Model_Checkmoney();

            $data_ch2 = array(
                'd_id'                  => $d_id,
                'payment'               => $time_now,
                'pay_time'              => $time_now,
                'pay_service'           => 0,
                'output'                => 0,
                'pay_money'             => 0,
                'type'                  => 2,
                'sn'                    => $sn,
                'user_id'               => $system_id,
                'create_by'             => $system_id,
                'create_at'             => $time_now,
                'note'                  => '',
                'company_id'            => 1,
                'sales_confirm_date'    => $time_now,
                'sales_confirm_id'      => $system_id,
                'finance_confirm_id'    => $system_id,
                'finance_confirm_date'  => $time_now,
                'payment_surplus'       => 0,
                'lack_of_money'         => 0
            );

            $QCheckmoney->insert($data_ch2);

            $data_ch1 = array(
                'd_id'                  => $d_id,
                'bank'                  => 10,//No Payment
                'pay_money'             => 0,
                'pay_servicecharge'     => 0,
                'pay_banktransfer'      => 0,
                'pay_service'           => 0,
                'type'                  => 1,
                'pay_time'              => $time_now,
                'bank_serial'           => null,
                'bank_transaction_code' => null,
                'note'                  => '',
                'content'               => null,
                'company_id'            => 1,
                'sn'                    => $sn,
                'file_pay_slip'         => '',
                'user_id'               => $system_id,
                'create_by'             => $system_id,
                'create_at'             => $time_now,
                'sales_confirm_id'      => $system_id,
                'sales_confirm_date'    => $time_now,
                'addition'              => 0,
                'payment_surplus'       => 0,
                'lack_of_money'         => 0
            );

            $QCheckmoney->insert($data_ch1);

	        // END : Create Checkmoney

            // START : Add log

            $QLFCR = new Application_Model_LogFreeCaseReno();

	    	$dataLog = array(
	    		'imei' => $imei,
	    		'first_name' => $first_name,
	    		'last_name' => $last_name,
	    		'phone' => $phone,
	    		'address' => $address,
	    		'province' => $province,
	    		'district' => $district,
	    		'sub_district' => $sub_district,
	    		'zipcode' => $zipcode,
	    		'd_id' => $d_id,
	    		'shipping_id' => $shipping_id,
	    		'sn' => $sn,
	    		'created_date' => $time_now,
	    		'updated_date' => $time_now,
	    		'status' => 1
	    	);

	        $QLFCR->insert($dataLog);

	        // END : Add log

	    	$db->commit();

	    	// START : Get So

            file_get_contents(HOST."cron/gen-sn-ref");

            $where_get_so = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $get_data_market =  $QMarket->fetchRow($where_get_so);

            $so = $sn;
            if(isset($get_data_market['sn_ref']) && $get_data_market['sn_ref']){
            	$so = $get_data_market['sn_ref'];
            }
	    	// END : Get So

	        http_response_code(200);
    		echo json_encode(['status' => 200, 'msg' => 'success', 'data' => ['imei' => $imei, 'so' => $so]],JSON_UNESCAPED_UNICODE);


	    } catch (Exception $e) {
		    http_response_code(400);
    		echo json_encode(['status' => 400, 'msg' => $e->getMessage()],JSON_UNESCAPED_UNICODE);
	    	$db->rollback();
		}

    }

    function encode($string,$key){
	    $key = sha1($key);
	    $strLen = strlen($string);
	    $keyLen = strlen($key);
	    for ($i = 0; $i < $strLen; $i++) {
	        $ordStr = ord(substr($string,$i,1));
	        if ($j == $keyLen) { $j = 0; }
	        $ordKey = ord(substr($key,$j,1));
	        $j++;
	        $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
	    }
	    return $hash;
	}

	function checkToken($check_data,$lifetime,$salt,$token){

	    if(!$check_data||!$lifetime||!$salt||!$token){
	        return false;
	    }

	    $key = ENCRYPT_KET;

	    // $limit_lifetime = ENCRYPT_TOKEN_LIFETIME;//min
	    $limit_lifetime = ENCRYPT_TOKEN_LIFETIME;//min
	    $time_check = time()-($limit_lifetime*60);

	    if($time_check > $lifetime){
	        return false;
	    }
	    
	    $secrets = $check_data.$lifetime.$salt;
	    $checkToken = $this->encode($secrets,$key);

	    if($token == $checkToken){
	        return true;
	    }
	    return false;
	}

}