<?php
class Application_Model_EtaxDocumentTran extends Zend_Db_Table_Abstract
{
	protected $_name = 'etax_document_tran';

	function view_invoice_pdf_file($order_sn)
    { 
    	$url="";
    	try{
            $lifetime = time();
            $salt = uniqid();

            $secrets = $order_sn.$lifetime.$salt;
            $key = ENCRYPT_KET;

            $token = $this->encode($secrets,$key);

    		$url = HOST.'finance/print-invoice-etax?sn='.$order_sn.'&lifetime='.$lifetime.'&salt='.$salt.'&token='.$token;
    		return $url;
    	}catch (exception $e) {
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        }
    }

    function view_cn_pdf_file($order_sn)
    { 
    	$url="";
    	try{
            $lifetime = time();
            $salt = uniqid();

            $secrets = $order_sn.$lifetime.$salt;
            $key = ENCRYPT_KET;

            $token = $this->encode($secrets,$key);

    		$url = HOST.'finance/print-creditnote-etax?code='.$order_sn.'&lifetime='.$lifetime.'&salt='.$salt.'&token='.$token;
    		return $url;
    	}catch (exception $e) {
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        }
    }

//--------------------Creste eTax File---------------------------//

/*---------------Create eTax PDF File------------------------*/
    function create_etax_invoice_pdf_file($flashMessenger,$invoice_number,&$return_data)
    {   
        try
        {   
            // D:\xampp\htdocs\api_generate_pdf
            //node api_generate_pdf.js
            $document_type="IN";
            $QEDT = new Application_Model_EtaxDocumentTran();
            $QM = new Application_Model_Market();
            $QCN = new Application_Model_CreditNote();
            $date = date('Y-m-d H:i:s');

            $db = Zend_Registry::get('db');
            $db->beginTransaction();
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $where = $QM->getAdapter()->quoteInto('invoice_number = ? ', $invoice_number);
            $getMarket = $QM->fetchRow($where);

            if(!$getMarket){
                 $flashMessenger->setNamespace('error')->addMessage("Not find invoice : " . $invoice_number);
                 header(HOST.'finance/import-etax');
            }

            $where = array();
            $where[] = $QEDT->getAdapter()->quoteInto('document_number = ? ', $invoice_number);
            $where[] = $QEDT->getAdapter()->quoteInto('document_type = ?', $document_type);

            $check_list = $QEDT->fetchAll($where);

            $file_path_date_pdf = substr($getMarket['invoice_time'],0,7).'/'.substr($getMarket['invoice_time'],8,2).'/'.$document_type.'/pdf/';
            
            $file_path_pdf = APPLICATION_PATH.'/../public/files/finance/etax/'.$file_path_date_pdf;

            $current_time = time();

            // $file_name = $invoice_number . '_' . $current_time . '_' . random_number();
            $file_name = $invoice_number.uniqid();

            $file_name_pdf = $file_name;

            $type = 'INV';
            $ref = $getMarket['sn'];
            $lifetime = time();
            $salt = $this->random_number();
            $secrets = $ref.$lifetime.$salt;
            $key = ENCRYPT_KET;
            $token = $this->encode($secrets,$key);

            $url_curl_get =  HOST."finance/export-pdf-etax?type=$type&ref=$ref&lifetime=$lifetime&salt=$salt&token=$token&part=$file_path_date_pdf&pdf_name=$file_name_pdf";

            //echo $url_curl_get;die;
            if(count($check_list) > 0){

                if($check_list[0]['document_status']=="1"){
                    //$flashMessenger->setNamespace('error')->addMessage("มีการ Upload Invoice Number : " . $invoice_number. " ไปแล้ว");
                    //header(HOST.'finance/import-etax');
                    $db->rollback();
                    return;
                }else{

                    if(count($check_list) > 1){
                        $flashMessenger->setNamespace('error')->addMessage("Please Contact Team IT Invoice Number : " . $invoice_number);
                        header(HOST.'finance/import-etax');
                    }

                    $data = array(
                        'document_date' => $getMarket['invoice_time'],
                        'filename_pdf' => $file_name_pdf . '.pdf',
                        'document_active' => 1,
                        'update_by' => $userStorage->id,
                        'update_date' => $date
                    );

                    $where = array();
                    $where[] = $QEDT->getAdapter()->quoteInto('document_number = ?', $check_list[0]['document_number']);
                    $where[] = $QEDT->getAdapter()->quoteInto("((document_status <>'1') ||(document_status IS NULL))", null);
                    $update = $QEDT->update($data, $where);
                }

            }else{

                $data = array(
                    'document_number' => $invoice_number,
                    'document_type' => $document_type,
                    'distributor_id' => $getMarket['d_id'],
                    'document_date' => $getMarket['invoice_time'],
                    'filename_pdf' => $file_name_pdf . '.pdf',
                    'document_active' => 1,
                    'create_by' => $userStorage->id,
                    'create_date' => $date
                );

                 $QEDT->insert($data);

            }

            // echo $url_curl_get.'<br><br>';

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,$url_curl_get);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close ($ch);

            // echo '<br><br>_________________<br><br>';

            // print_r($server_output);
            // echo '<br><br>';die;

            $res = json_decode($server_output);

            if (!isset($res->status) || !in_array($res->status, [200,201])){
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage("Wrong PDF Invoice Number : " . $invoice_number);
                header(HOST.'finance/import-etax');
            }else{
                $db->commit();
                return $return_data = $invoice_number;
                //$flashMessenger->setNamespace('success')->addMessage('Create PDF Done! Invoice Number : '.$invoice_number);  
            }
            
        }catch (exception $e) {
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        }
    }

    function create_etax_cn_pdf_file($flashMessenger,$creditnote_no,$document_type,&$return_data)
    {
        try
        {
            //$document_type="CN";
            $date = date('Y-m-d H:i:s');
            $QEDT = new Application_Model_EtaxDocumentTran();
            $QM = new Application_Model_Market();
            $QCN = new Application_Model_CreditNote();

            $db = Zend_Registry::get('db');
            $db->beginTransaction();
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $where = $QCN->getAdapter()->quoteInto('creditnote_sn = ? ', $creditnote_no);
            $getCN = $QCN->fetchRow($where);

            if(!$getCN){
                 $flashMessenger->setNamespace('error')->addMessage("Not find CN : " . $creditnote_no);
                    header(HOST.'finance/import-etax');
            }

            $where = array();
            $where[] = $QEDT->getAdapter()->quoteInto('document_number = ? ', $creditnote_no);
            $where[] = $QEDT->getAdapter()->quoteInto('document_type = ?', $document_type);

            $check_list = $QEDT->fetchAll($where);

            $file_path_date_pdf = substr($getCN['create_date'],0,7).'/'.substr($getCN['create_date'],8,2).'/'.$document_type.'/pdf/';
            
            $file_path_pdf = APPLICATION_PATH.'/../public/files/finance/etax/'.$file_path_date_pdf;

            $current_time = time();

            // $file_name = $creditnote_no . '_' . $current_time . '_' . random_number();
            $file_name = $creditnote_no.uniqid();

            $file_name_pdf = $file_name;

            $type = 'CN';
            $ref = $getCN['creditnote_sn'];
            $lifetime = time();
            $salt = $this->random_number();
            $secrets = $ref.$lifetime.$salt;
            $key = ENCRYPT_KET;
            $token = $this->encode($secrets,$key);

            $url_curl_get =  HOST."finance/export-pdf-etax?type=$type&ref=$ref&lifetime=$lifetime&salt=$salt&token=$token&part=$file_path_date_pdf&pdf_name=$file_name_pdf";

            if(count($check_list) > 0){

                if(count($check_list) > 1){
                    $db->rollback();
                    return;
                }

                $data = array(
                    'document_date' => $getCN['create_date'],
                    'distributor_id'=>$getCN['distributor_id'],
                    'filename_pdf' => $file_name_pdf.'.pdf',
                    'document_active' => 1,
                    'update_by' => $userStorage->id,
                    'update_date' => $date
                );

                $where = array();
                $where[] = $QEDT->getAdapter()->quoteInto('document_number = ?', $check_list[0]['document_number']);
                $where[] = $QEDT->getAdapter()->quoteInto("((document_status <>'1') ||(document_status IS NULL))", null);
                $update = $QEDT->update($data, $where);

            }else{

                $data = array(
                    'document_number' => $creditnote_no,
                    'document_type' => $document_type,
                    'distributor_id'=>$getCN['distributor_id'],
                    'document_date' => $getCN['create_date'],
                    'filename_pdf' => $file_name_pdf.'.pdf',
                    'document_active' => 1,
                    'create_by' => $userStorage->id,
                    'create_date' => $date
                );

                $QEDT->insert($data);

            }

            // echo $url_curl_get.'<br><br>';

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,$url_curl_get);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close ($ch);

            // echo '<br><br>_________________<br><br>';

            // print_r($server_output);
            // echo '<br><br>';die;

            $res = json_decode($server_output);

            if (!isset($res->status) || !in_array($res->status, [200,201])){
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage("Wrong PDF CN Number : " . $creditnote_no);
                header(HOST.'finance/import-etax');
            }else{
                $db->commit();
                return $return_data = $creditnote_no;
                //$flashMessenger->setNamespace('success')->addMessage('Create PDF Done! CN : '.$creditnote_no);  
            }
            
        }catch (exception $e) {
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        }
    }

     /*---------------Create eTax CN CSV File------------------------*/

    function create_etax_cn_csv_file($flashMessenger,$data_number,$document_type,&$return_data) 
    {
        try
        {
        //set_time_limit(0);
        //error_reporting(~E_ALL);
        //ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $QEtax      = new Application_Model_EtaxDocumentTran();    
        //$document_type="CN";
        $date = date('Y-m-d H:i:s');

        $db = Zend_Registry::get('db');
        
        $result = $QEtax->get_credit_note_number($document_type,$data_number);
        //print_r($result);die;
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        if(!$result){return;}

        //$flashMessenger->setNamespace('success')->addMessage('Create CSV Done!'); 
        //return;
        //print_r($result);die;
        foreach($result as $res)
        {
        	//print_r($res);die;
            $db->beginTransaction();
            //$sales_sn = $res['sn'];
            $creditnote_sn = $res['cp_no'];
            $filename = $creditnote_sn.uniqid().'.csv';
            //$filename = $sales_sn.'.txt';

            
            $sqlCheck="SELECT document_number,document_status FROM etax_document_tran WHERE document_number='".$creditnote_sn."'";
            $resultCheck = $db->fetchAll($sqlCheck);
            //print_r($resultCheck);die;
            if(!$resultCheck)
            {
                //------------ Insert -----------------
                try {
                    $dataEtax   = array('document_number'=>$creditnote_sn
                        ,'document_date'=>$res['create_date']
                        ,'distributor_id'=>$res['distributor_id']
                        ,'filename_csv'=>$filename
                        ,'document_type'=>$document_type
                        ,'document_active'=>1
                        ,'create_by'=>$userStorage->id
                        ,'create_date'=>$date);

                    //print_r($dataEtax);die;
                    $rs=$QEtax->insert($dataEtax);
                } catch (Exception $e) {
                    $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
                    return;
                }

            }else{
                //------------ Update -----------------
                    try {
                        if($resultCheck[0]['document_status']=="1"){
                            $db->rollback();
                            return;
                        }else{
                            $dataEtax   = array('filename_csv'=>$filename
                            ,'document_active'=>1
                            ,'update_by'=>$userStorage->id
                            ,'update_date'=>$date);

                            $where = array();
                            $where[] = $QEtax->getAdapter()->quoteInto('document_number = ?', $invoice_number);
                            $where[] = $QEtax->getAdapter()->quoteInto("((document_status <>'1') ||(document_status IS NULL))", null);
                            $update = $QEtax->update($dataEtax, $where);
                        }

                    } catch (Exception $e) {
                        $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
                        return;
                    }
                
            }
            //die;
            // output headers so that the file is downloaded rather than displayed
            while (@ob_end_clean());
            ob_start();
            header('Content-Encoding: UTF-8');
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename='.$filename);
            

            $file_path_date = substr($res['create_date'],0,7).'/'.substr($res['create_date'],8,2).'/'.$document_type.'/csv/';
            $file_path = APPLICATION_PATH.'/../public/files/finance/etax/'.$file_path_date;

            if (!file_exists($file_path))
                mkdir($file_path, 0777, true);

            $path = $file_path.'/'.$filename;
            $output = fopen($path, 'w+');
            echo "\xEF\xBB\xBF"; // UTF-8 BOM

            //print_r($res);die;
            /*-------------------------------CN Order---------------------------------------*/
            //DATA_TYPE = C
                $SELLER_TAX_ID="0745552000866"; //เลขประจำตัวผู้เสียภาษีอากรของผู้ขาย
                $SELLER_BRANCH_ID="00000";  //เลขสาขาสถานประกอบการ
                $data_type_C = array(
                                    'DATA_TYPE' => 'C',//ประเภทรายการ
                                    'SELLER_TAX_ID' => $SELLER_TAX_ID,//เลขประจำตัวผู้เสียภาษีอากรของผู้ขาย
                                    'SELLER_BRANCH_ID'  => $SELLER_BRANCH_ID,//เลขสาขาสถานประกอบการ
                                    'FILE_NAME'    => $filename,//ชื่อไฟล์
                                    'DATA_TEMPLATE_ID'    => date('Y-m-d').'T'.date('H:i:s'),//รหัส template text file
                                  );

                //DATA_TYPE = H
                if($res['doc_type']=='CN'){
                    $DOCUMENT_TYPE_CODE="81"; //ประเภทเอกสาร ใบลดหนี้
                    $DOCUMENT_NAME="ใบลดหนี้";//ชื่อเอกสาร 
                    $BUYER_ORDER_REF_TYPE_CODE = $document_type; //ประเภทเอกสารอ้างอิงการสั่งซื้อ
                    $DOCUMENT_ID= $res['cp_no'];//เลขที่เอกสาร
                    $DOCUMENT_ISSUE_DTM = $res['create_date'];//เอกสารลงวันที่
                    $CREATE_PURPOSE_CODE ="";
                    $CREATE_PURPOSE="";
                    //print_r($res[0]['return_type']);die;
                    switch ($res['imei'][0]['return_type']) {
					    case "1":	//EOL
					        $CREATE_PURPOSE_CODE ="CDNG99";//สาเหตุการออกเอกสาร
					        $CREATE_PURPOSE="สินค้าตกรุ่น";//สาเหตุการออกเอกสาร
					        break;
					    case "2":	//เครื่องเสีย DOA/DAP
					        $CREATE_PURPOSE_CODE ="CDNG02";//สาเหตุการออกเอกสาร
					        $CREATE_PURPOSE=$res['imei'][0]['damage_detail'];//สาเหตุการออกเอกสาร
					        break;
					    case "3":	//Demo
					        $CREATE_PURPOSE_CODE ="CDNG99";//สาเหตุการออกเอกสาร
					        $CREATE_PURPOSE="รับคืนสินค้า Demo";//สาเหตุการออกเอกสาร
					        break;
					    case "4":	//กรณีพิเศษ/อื่นๆ
					        $CREATE_PURPOSE_CODE ="CDNG99";//สาเหตุการออกเอกสาร
					        $CREATE_PURPOSE=$res['imei'][0]['damage_detail'];//สาเหตุการออกเอกสาร
					        break;     
					}
                    
                }
                
                $data_type_H = array(
                                    'DATA_TYPE' => 'H',//ประเภทรายการ
                                    'DOCUMENT_TYPE_CODE' => $DOCUMENT_TYPE_CODE,//ประเภทเอกสาร 
                                    'DOCUMENT_NAME'  => $DOCUMENT_NAME,//ชื่อเอกสาร 
                                    'DOCUMENT_ID'    => $DOCUMENT_ID,//เลขที่เอกสาร
                                    'DOCUMENT_ISSUE_DTM'    => $DOCUMENT_ISSUE_DTM,//เอกสารลงวันที่
                                    'CREATE_PURPOSE_CODE'  => $CREATE_PURPOSE_CODE,//สาเหตุการออกเอกสาร
                                    'CREATE_PURPOSE'  => $CREATE_PURPOSE,//สาเหตุการออกเอกสาร
                                    'ADDITIONAL_REF_ASSIGN_ID'  => $ADDITIONAL_REF_ASSIGN_ID,//เลขที่เอกสารอ้างอิง
                                    'ADDITIONAL_REF_ISSUE_DTM'  => $ADDITIONAL_REF_ISSUE_DTM,//เอกสารอ้างอิงลงวันที่
                                    'ADDITIONAL_REF_TYPE_CODE'  => $ADDITIONAL_REF_TYPE_CODE,//ประเภทเอกสารอ้างอิง
                                    'ADDITIONAL_REF_DOCUMENT_NAME'  => $ADDITIONAL_REF_DOCUMENT_NAME,//ชื่อเอกสารอ้างอิง 
                                    'DELIVERY_TYPE_CODE'  => $DELIVERY_TYPE_CODE,//เงื่อนไขการส่งของ 
                                    'BUYER_ORDER_ASSIGN_ID'  => $res['cp_no'],//เลขที่ใบสั่งซื้อ
                                    'BUYER_ORDER_ISSUE_DTM'  => $res['create_date'],//วันเดือนปี ที่ออกใบสั่งซื้อ
                                    'BUYER_ORDER_REF_TYPE_CODE'  => $BUYER_ORDER_REF_TYPE_CODE,//ประเภทเอกสารอ้างอิงการสั่งซื้อ
                                    'DOCUMENT_REMARK'  => $DOCUMENT_REMARK,//หมายเหตุท้ายเอกสาร
                                  );
                //print_r($data_type_H);die;
                //DATA_TYPE = B
                $BUYER_TAX_ID_TYPE="TXID";//ประเภทผู้เสียภาษีอากร 
                if($res['distributor_branch_no']!=''){
                    $BUYER_BRANCH_ID=$res['distributor_branch_no'];//เลขที่สาขา
                }else{
                    $BUYER_BRANCH_ID="00000";//เลขที่สาขา
                }

                if($res['post_code']!=''){
                    $BUYER_POST_CODE=$res['post_code'];//รหัสไปรษณีย์
                }else{
                    $BUYER_POST_CODE="00000";//รหัสไปรษณีย์
                }
                //print_r($res);die;
                $data_type_B = array(
                                    'DATA_TYPE' => 'B',//ประเภทรายการ
                                    'BUYER_ID' => $res['store_code'],//รหัสผู้ซื้อ
                                    'BUYER_NAME'  => $res['distributor_name'],//ชื่อผู้ซื้อ
                                    'BUYER_TAX_ID_TYPE'    => $BUYER_TAX_ID_TYPE,//ประเภทผู้เสียภาษีอากร 
                                    'BUYER_TAX_ID'    => $res['distributor_tax_id'],//เลขประจำตัวผู้เสียภาษีอากร
                                    'BUYER_BRANCH_ID'    => $BUYER_BRANCH_ID,//เลขที่สาขา
                                    'BUYER_CONTACT_PERSON_NAME'    => $res['contact_name'],//ชื่อผู้ติดต่อ
                                    'BUYER_CONTACT_DEPARTMENT_NAME'    => $BUYER_CONTACT_DEPARTMENT_NAME,//ชื่อแผนก
                                    'BUYER_URIID'    => $res['email'],//อีเมล
                                    'BUYER_PHONE_NO'    => $res['contact_tel'],//เบอร์โทรศัพท์
                                    'BUYER_POST_CODE'    => $BUYER_POST_CODE,//รหัสไปรษณีย์
                                    'BUYER_BUILDING_NAME'    => $BUYER_BUILDING_NAME,//ชื่ออาคาร
                                    'BUYER_BUILDING_NO'    => $BUYER_BUILDING_NO,//บ้านเลขที่
                                    'BUYER_ADDRESS_LINE1'    => $res['distributor_address'],//ที่อยู่บรรทัดที่ 1
                                    'BUYER_ADDRESS_LINE2'    => $BUYER_ADDRESS_LINE2,//ที่อยู่บรรทัดที่ 2
                                    'BUYER_ADDRESS_LINE3'    => $BUYER_ADDRESS_LINE3,//ซอย
                                    'BUYER_ADDRESS_LINE4'    => $BUYER_ADDRESS_LINE4,//หมู่บ้าน
                                    'BUYER_ADDRESS_LINE5'    => $BUYER_ADDRESS_LINE5,//หมู่ที่
                                    'BUYER_STREET_NAME'    => $BUYER_STREET_NAME,//ถนน
                                    'BUYER_CITY_SUB_DIV_ID'    => $BUYER_CITY_SUB_DIV_ID,//รหัสตำบล
                                    'BUYER_CITY_SUB_DIV_NAME'    => $BUYER_CITY_SUB_DIV_NAME,//ชื่อตำบล
                                    'BUYER_CITY_ID'    => $BUYER_CITY_ID,//รหัสอำเภอ
                                    'BUYER_CITY_NAME'    => $BUYER_CITY_NAME,//ชื่ออำเภอ
                                    'BUYER_COUNTRY_SUB_DIV_ID'    => $BUYER_COUNTRY_SUB_DIV_ID,//รหัสจังหวัด
                                    'BUYER_COUNTRY_SUB_DIV_NAME'    => $BUYER_COUNTRY_SUB_DIV_NAME,//ชื่อจังหวัด
                                    'BUYER_COUNTRY_ID'    => "TH",//รหัสประเทศ
                                  );
                //print_r($data_type_B);die;
            $r=1;
            $TAX_CAL_RATE = 7;//อัตราภาษี
            $GRAND_LINE_TOTAL_COUNT=0;
            $GRAND_LINE_NET_TOTAL_AMOUNT=0;
            $BASIS_AMOUNT=0;//มูลค่าสินค้า/บริการ (ไม่รวมภาษีมูลค่าเพิ่ม)
            $GRAND_TAX_TOTAL_AMOUNT=0;
            
            //print_r($res['invoice']);die;
            foreach($res['invoice'] as $item)
            {
            	//print_r($item);die;
            	 $num = $item['num'];
                 $uprice_in = $item['price'];
                 $uprice = round(($item['price']/1.07),2);

                 $diff_vat_unit = round($uprice_in-($item['price']/1.07),2);
	             $sale_off_percent = $item['imei'][$i]['sale_off_percent']; //ext vatxcd
	             if ($sale_off_percent) {
	               $percent = ($uprice*$item['imei'][$i]['sale_off_percent'])/100;
	             }
	             $amount_tax  = ($uprice * $num);
	             $price_discount_num = round(($item['price'])/1.07,2) * $num;
	             $correct_amount = round(($price_discount_num),2);
	             $diff_amount = $amount_tax - $correct_amount;
	             $diff_vat    = round((($correct_amount)*1.07),2)- $price_discount_num;

	             $price_discount_num = $price_discount_num+$diff_vat;
	             $total_qty   += $num;
	             $total_num   += $price_discount_num;
	             $total_uprice += $uprice;
	             $total_amount_tax += $amount_tax;
	             $total_diff_amount += $diff_amount;
	             $total_correct_amount += $correct_amount;
	             $total_diff_vat += $diff_vat;


	            $PRODUCT_CHARGE_AMOUNT = number_format(($uprice_in), 2, '.', '');//ราคาต่อหน่วย (in VAT)
                $PRODUCT_QUANTITY_PER_UNIT="1";//ขนาดบรรจุต่อหน่วยขาย
                $LINE_TAX_TYPE_CODE="VAT";//รหัสประเภทภาษี ภาษีที่ใช้มีอยู่หลายรูปแบบ เช่น Value Added Tax (VAT) 0% Vat 7% และ Exemption Tax (FRE)
                $LINE_TAX_CAL_RATE="7.00";//อัตราภาษี
                $LINE_BASIS_AMOUNT=number_format($uprice, 2, '.', '');//มูลค่าสินค้า/บริการ (ไม่รวมภาษีมูลค่าเพิ่ม)
                $LINE_BASIS_CURRENCY_CODE="THB";//รหัสสกุลเงิน (มูลค่าสินค้า/บริการ)
                $LINE_TAX_CAL_AMOUNT=number_format($diff_vat_unit, 2, '.', '');//มูลค่าภาษีมูลค่าเพิ่ม
                $LINE_TAX_CAL_CURRENCY_CODE="THB";//รหัสสกุลเงิน (มูลค่าภาษีมูลค่าเพิ่ม)

                //---------LINE_TOTAL-------------//

                $LINE_NET_INCLUDE_TAX_TOTAL_AMOUNT=number_format($price_discount_num, 2, '.', '');//จำนวนเงินรวม
                $LINE_NET_TOTAL_AMOUNT = number_format($total_diff_vat, 2, '.', '');//จำนวนเงินรวมก่อนภาษี
                $LINE_TAX_TOTAL_AMOUNT = number_format(($LINE_NET_INCLUDE_TAX_TOTAL_AMOUNT-$LINE_NET_TOTAL_AMOUNT), 2, '.', '');//ภาษีมูลค่าเพิ่ม

                /*

				$PRODUCT_CHARGE_AMOUNT = number_format($item['price'], 2, '.', '');//ราคาต่อหน่วย (in VAT)
                $PRODUCT_QUANTITY_PER_UNIT="1";//ขนาดบรรจุต่อหน่วยขาย
                $LINE_TAX_TYPE_CODE="VAT";//รหัสประเภทภาษี ภาษีที่ใช้มีอยู่หลายรูปแบบ เช่น Value Added Tax (VAT) 0% Vat 7% และ Exemption Tax (FRE)
                $LINE_TAX_CAL_RATE="7.00";//อัตราภาษี
                $LINE_BASIS_AMOUNT=number_format(($item['price']*1.07)-$item['price'], 2, '.', '');//มูลค่าสินค้า/บริการ (ไม่รวมภาษีมูลค่าเพิ่ม)
                $LINE_BASIS_CURRENCY_CODE="THB";//รหัสสกุลเงิน (มูลค่าสินค้า/บริการ)
                $LINE_TAX_CAL_AMOUNT=number_format(($PRODUCT_CHARGE_AMOUNT-$LINE_BASIS_AMOUNT), 2, '.', '');//มูลค่าภาษีมูลค่าเพิ่ม
                $LINE_TAX_CAL_CURRENCY_CODE="THB";//รหัสสกุลเงิน (มูลค่าภาษีมูลค่าเพิ่ม)

                //---------LINE_TOTAL-------------//

                $LINE_NET_INCLUDE_TAX_TOTAL_AMOUNT=number_format($item['total_amount_in_vat'], 2, '.', '');//จำนวนเงินรวม
                $LINE_NET_TOTAL_AMOUNT = number_format($item['total_amount_ex_vat'], 2, '.', '');//จำนวนเงินรวมก่อนภาษี
                $LINE_TAX_TOTAL_AMOUNT = number_format(($LINE_NET_INCLUDE_TAX_TOTAL_AMOUNT-$LINE_NET_TOTAL_AMOUNT), 2, '.', '');//ภาษีมูลค่าเพิ่ม

                */


                //---------GRAND_TOTAL-------------//
                $LINE_TOTAL_COUNT += $num;//จำนวนรายการสินค้า
                $data_type_L[] = array(
                                    'DATA_TYPE' => 'L',//ประเภทรายการ
                                    'LINE_ID' => $r,//ลำดับรายการ
                                    'PRODUCT_ID' => $item['product_name'],//รหัสสินค้า
                                    'PRODUCT_NAME' => $item['product_call_name'],//ชื่อสินค้า
                                    'PRODUCT_DESC' => $PRODUCT_DESC,//รายละเอียดสินค้า
                                    'PRODUCT_BATCH_ID' => $PRODUCT_BATCH_ID,//ครั้งที่ผลิต
                                    'PRODUCT_EXPIRE_DTM' => $PRODUCT_EXPIRE_DTM,//วันหมดอายุ
                                    'PRODUCT_CLASS_CODE' => $PRODUCT_CLASS_CODE,//รหัสหมวดหมู่สินค้า
                                    'PRODUCT_CLASS_NAME' => $PRODUCT_CLASS_NAME,//ชื่อหมวดหมู่สินค้า
                                    'PRODUCT_ORIGIN_COUNTRY_ID' => "TH",//รหัสประเทศกำเนิด
                                    'PRODUCT_CHARGE_AMOUNT' => $PRODUCT_CHARGE_AMOUNT,//ราคาต่อหน่วย
                                    'PRODUCT_CHARGE_CURRENCY_CODE' => "THB",//รหัสสกุลเงิน (ราคาต่อหน่วย)
                                    'PRODUCT_ALLOWANCE_CHARGE_IND' => $PRODUCT_ALLOWANCE_CHARGE_IND,//ตัวบอกส่วนลดหรือค่าธรรมเนียม
                                    'PRODUCT_ALLOWANCE_ACTUAL_AMOUNT' => $PRODUCT_ALLOWANCE_ACTUAL_AMOUNT,//มูลค่าส่วนลดหรือค่าธรรมเนียม
                                    'PRODUCT_ALLOWANCE_ACTUAL_CURRENCY_CODE' => $PRODUCT_ALLOWANCE_ACTUAL_CURRENCY_CODE,//รหัสสกุลเงิน (มูลค่าส่วนลดหรือค่าธรรมเนียม)
                                    'PRODUCT_ALLOWANCE_REASON_CODE' => $PRODUCT_ALLOWANCE_REASON_CODE,//รหัสเหตุผลในการคิดส่วนลดหรือค่าธรรมเนียม
                                    'PRODUCT_ALLOWANCE_REASON' => $PRODUCT_ALLOWANCE_REASON,//เหตุผลในการคิดสวนลดหรือค่าธรรมเนียม
                                    'PRODUCT_QUANTITY' => $num,//จำนวนสินค้า
                                    'PRODUCT_UNIT_CODE' => $PRODUCT_UNIT_CODE,//รหัสหน่วยสินค้า
                                    'PRODUCT_QUANTITY_PER_UNIT' => $PRODUCT_QUANTITY_PER_UNIT,//ขนาดบรรจุต่อหน่วยขาย
                                    'LINE_TAX_TYPE_CODE' => $LINE_TAX_TYPE_CODE,//รหัสประเภทภาษี ภาษีที่ใช้มีอยู่หลายรูปแบบ เช่น Value Added Tax (VAT) 0% Vat 7% และ Exemption Tax (FRE)
                                    'LINE_TAX_CAL_RATE' => $LINE_TAX_CAL_RATE,//อัตราภาษี
                                    'LINE_BASIS_AMOUNT' => $LINE_BASIS_AMOUNT,//มูลค่าสินค้า/บริการ (ไม่รวมภาษีมูลค่าเพิ่ม)
                                    'LINE_BASIS_CURRENCY_CODE' => $LINE_BASIS_CURRENCY_CODE,//รหัสสกุลเงิน (มูลค่าสินค้า/บริการ)
                                    'LINE_TAX_CAL_AMOUNT' => $LINE_TAX_CAL_AMOUNT,//มูลค่าภาษีมูลค่าเพิ่ม
                                    'LINE_TAX_CAL_CURRENCY_CODE' => $LINE_TAX_CAL_CURRENCY_CODE,//รหัสสกุลเงิน (มูลค่าภาษีมูลค่าเพิ่ม)
                                    'LINE_ALLOWANCE_CHARGE_IND' => $LINE_ALLOWANCE_CHARGE_IND,//จำนวนรายการสินค้า
                                    'LINE_ALLOWANCE_ACTUAL_AMOUNT' => $LINE_ALLOWANCE_ACTUAL_AMOUNT,//มูลค่าส่วนลดหรือค่าธรรมเนียม
                                    'LINE_ALLOWANCE_ACTUAL_CURRENCY_CODE' => $LINE_ALLOWANCE_ACTUAL_CURRENCY_CODE,//มูลค่าส่วนลดหรือค่าธรรมเนียม
                                    'LINE_ALLOWANCE_REASON_CODE' => $LINE_ALLOWANCE_REASON_CODE,//รหัสเหตุผลในการคิดส่วนลดหรือค่าธรรมเนียม
                                    'LINE_ALLOWANCE_REASON' => $LINE_ALLOWANCE_REASON,//เหตุผลในการคิดสวนลดหรือค่าธรรมเนียม
                                    'LINE_TAX_TOTAL_AMOUNT' => $LINE_TAX_TOTAL_AMOUNT,//ภาษีมูลค่าเพิ่ม
                                    'LINE_TAX_TOTAL_CURRENCY_CODE' => "THB",//รหัสสกุลเงิน (ภาษีมูลค่าเพิ่ม)
                                    'LINE_NET_TOTAL_AMOUNT' => $LINE_NET_TOTAL_AMOUNT,//จำนวนเงินรวมก่อนภาษี
                                    'LINE_NET_TOTAL_CURRENCY_CODE' => "THB",//รหัสสกุลเงิน (จำนวนเงินรวมก่อนภาษี)
                                    'LINE_NET_INCLUDE_TAX_TOTAL_AMOUNT' => $LINE_NET_INCLUDE_TAX_TOTAL_AMOUNT,//จำนวนเงินรวม
                                    'LINE_NET_INCLUDE_TAX_TOTAL_CURRENCY_CODE' => "THB",//รหัสสกุลเงิน (จำนวนเงินรวม)
                                    'PRODUCT_REMARK' => $PRODUCT_REMARK,//หมายเหตุท้ายสินค้า
                                  );
                $r+=1;
            }

            //print_r($data_type_L);die;
            //DATA_TYPE = F 
            $DELIVERY_OCCUR_DTM=$res['invoice_date'];//วันเวลานัดส่งสินค้า
            $PAYMENT_DUE_DTM=$res['invoice_date'];//วันครบกำหนดชำระเงิน

            $ALLOWANCE_CHARGE_IND="";//ตัวบอกส่วนลดหรือค่าธรรมเนียม
            $ALLOWANCE_ACTUAL_AMOUNT=number_format(0, 2, '.', '');//มูลค่าส่วนลดหรือค่าธรรมเนียม
            $ALLOWANCE_TOTAL_AMOUNT=number_format(0, 2, '.', '');//ส่วนลดทั้งหมด

            $BASIS_AMOUNT = number_format($total_correct_amount, 2, '.', '');//มูลค่าสินค้า/บริการ (ไม่รวมภาษีมูลค่าเพิ่ม)

            $TAX_CAL_AMOUNT = number_format($total_num-$total_correct_amount, 2, '.', '');//มูลค่าภาษีมูลค่าเพิ่ม

            $TAX_BASIS_TOTAL_AMOUNT = number_format($total_correct_amount, 2, '.', '');//มูลค่าที่นำมาคิดภาษีมูลค่าเพิ่ม
            $GRAND_TOTAL_AMOUNT = number_format($total_num, 2, '.', '');//จำนวนเงินรวม (รวมภาษีมูลค่าเพิ่ม)
            $GRAND_LINE_TAX_TOTAL_AMOUNT = number_format(($GRAND_TOTAL_AMOUNT-$TAX_BASIS_TOTAL_AMOUNT), 2, '.', '');//จำนวนภาษีมูลค่าเพิ่ม

            $LINE_TOTAL_AMOUNT = number_format($total_num, 2, '.', '');//รวมมูลค่าตามรายการ/มูลค่าที่ถูกต้อง



             


            $data_type_F = array(
                                    'DATA_TYPE' => 'F', //ประเภทรายการ
                                    'LINE_TOTAL_COUNT' => $LINE_TOTAL_COUNT,//จำนวนรายการสินค้า
                                    'DELIVERY_OCCUR_DTM' => $DELIVERY_OCCUR_DTM,//วันเวลานัดส่งสินค้า
                                    'INVOICE_CURRENCY_CODE' => "THB",//รหัสสกุลเงินตรา
                                    'TAX_TYPE_CODE' => "VAT",//รหัสประเภทภาษี ภาษีที่ใช้มีอยู่หลายรูปแบบ เช่น Value Added Tax (VAT) 0% Vat 7% และ Exemption Tax (FRE)
                                    'TAX_CAL_RATE' => $TAX_CAL_RATE,//อัตราภาษี
                                    'BASIS_AMOUNT' => $BASIS_AMOUNT,//มูลค่าสินค้า/บริการ (ไม่รวมภาษีมูลค่าเพิ่ม)
                                    'BASIS_CURRENCY_CODE' => "THB",//มูลค่าสินค้า/บริการ (ไม่รวมภาษีมูลค่าเพิ่ม)
                                    'TAX_CAL_AMOUNT' => $TAX_CAL_AMOUNT,//มูลค่าภาษีมูลค่าเพิ่ม
                                    'TAX_CAL_CURRENCY_CODE' => "THB",//รหัสสกุลเงิน (ภาษีมูลค่าเพิ่ม)
                                    'ALLOWANCE_CHARGE_IND' => $ALLOWANCE_CHARGE_IND,//ตัวบอกส่วนลดหรือค่าธรรมเนียม
                                    'ALLOWANCE_ACTUAL_AMOUNT' => $ALLOWANCE_ACTUAL_AMOUNT,//มูลค่าส่วนลดหรือค่าธรรมเนียม
                                    'ALLOWANCE_ACTUAL_CURRENCY_CODE' => "THB",//รหัสสกุลเงิน (มูลค่าส่วนลดหรือค่าธรรมเนียม)
                                    'ALLOWANCE_REASON_CODE' => $ALLOWANCE_REASON_CODE,//รหัสเหตุผลในการคิดส่วนลดหรือค่าธรรมเนียม
                                    'ALLOWANCE_REASON' => $ALLOWANCE_REASON,//เหตุผลในการคิดสวนลดหรือค่าธรรมเนียม
                                    'PAYMENT_TYPE_CODE' => $PAYMENT_TYPE_CODE,//รหัสประเภทส่วนลดหรือค่าธรรมเนียม
                                    'PAYMENT_DESCRIPTION' => $PAYMENT_DESCRIPTION,//รายละเอียดเงื่อนไขการชำระเงิน
                                    'PAYMENT_DUE_DTM' => $PAYMENT_DUE_DTM,//วันครบกำหนดชำระเงิน
                                    'ORIGINAL_TOTAL_AMOUNT' => $ORIGINAL_TOTAL_AMOUNT,//รวมมูลค่าตามเอกสารเดิม
                                    'ORIGINAL_TOTAL_CURRENCY_CODE' => $ORIGINAL_TOTAL_CURRENCY_CODE,//รวมมูลค่าตามเอกสารเดิม
                                    'LINE_TOTAL_AMOUNT' => $LINE_TOTAL_AMOUNT,//รวมมูลค่าตามรายการ/มูลค่าที่ถูกต้อง
                                    'LINE_TOTAL_CURRENCY_CODE' => "THB",//รวมมูลค่าตามรายการ/มูลค่าที่ถูกต้อง
                                    'ADJUSTED_INFORMATION_AMOUNT' => $ADJUSTED_INFORMATION_AMOUNT,//มูลค่าผลต่าง
                                    'ADJUSTED_INFORMATION_CURRENCY_CODE' => $ADJUSTED_INFORMATION_CURRENCY_CODE,//มูลค่าผลต่าง
                                    'ALLOWANCE_TOTAL_AMOUNT' => $ALLOWANCE_TOTAL_AMOUNT,//ส่วนลดทั้งหมด
                                    'ALLOWANCE_TOTAL_CURRENCY_CODE' => "THB",//ส่วนลดทั้งหมด
                                    'CHARGE_TOTAL_AMOUNT' => $CHARGE_TOTAL_AMOUNT,//ค่าธรรมเนียมทั้งหมด
                                    'CHARGE_TOTAL_CURRENCY_CODE' => $CHARGE_TOTAL_CURRENCY_CODE,//ค่าธรรมเนียมทั้งหมด
                                    'TAX_BASIS_TOTAL_AMOUNT' => $TAX_BASIS_TOTAL_AMOUNT,//มูลค่าที่นำมาคิดภาษีมูลค่าเพิ่ม
                                    'TAX_BASIS_TOTAL_CURRENCY_CODE' => "THB",//มูลค่าที่นำมาคิดภาษีมูลค่าเพิ่ม
                                    'TAX_TOTAL_AMOUNT' => $GRAND_LINE_TAX_TOTAL_AMOUNT,//จำนวนภาษีมูลค่าเพิ่ม
                                    'TAX_TOTAL_CURRENCY_CODE' => "THB",//จำนวนภาษีมูลค่าเพิ่ม
                                    'GRAND_TOTAL_AMOUNT' => $GRAND_TOTAL_AMOUNT,//จำนวนเงินรวม (รวมภาษีมูลค่าเพิ่ม)
                                    'GRAND_TOTAL_CURRENCY_CODE' => "THB",//จำนวนเงินรวม (รวมภาษีมูลค่าเพิ่ม)
                                  );

            //print_r($data_type_F);die;
            $data_type_T = array(
                                    'DATA_TYPE' => 'T',//ประเภทรายการ
                                    'TOTAL_DOCUMENT_COUNT' => 1,//จำนวนเอกสารทั้งหมด
                                  );
                    
            /*-------------------------------End Invoice Order---------------------------------------*/

            $rows = "";
            for($i=0;$i<6;$i++)
            {
                
                if($i==0){//data_type_C
                    $row = "";
                    foreach($data_type_C as $item_c)
                    {
                        $row .= '"'.$item_c.'",';
                    }
                    $row_c = rtrim($row, ',');
                    $row_c .="\n";
                }else if($i==1){//data_type_H
                    $row = "";
                    //print_r($data_type_H);die;
                    foreach($data_type_H as $item_h)
                    {
                        $row .= '"'.$item_h.'",';
                    }
                    $row_h = rtrim($row, ',');
                    $row_h .="\n";
                }else if($i==2){//data_type_B
                    $row = "";
                    foreach($data_type_B as $item_b)
                    {
                        $row .= '"'.$item_b.'",';
                    }
                    $row_b = rtrim($row, ',');
                    $row_b .="\n";
                }else if($i==3){//data_type_L
                    $row = "";
                    foreach($data_type_L as $item_l)
                    {
                        $row .= '"'.$item_l['DATA_TYPE'].'",';
                        $row .= '"'.$item_l['LINE_ID'].'",';
                        $row .= '"'.$item_l['PRODUCT_ID'].'",';
                        $row .= '"'.$item_l['PRODUCT_NAME'].'",';
                        $row .= '"'.$item_l['PRODUCT_DESC'].'",';
                        $row .= '"'.$item_l['PRODUCT_BATCH_ID'].'",';
                        $row .= '"'.$item_l['PRODUCT_EXPIRE_DTM'].'",';
                        $row .= '"'.$item_l['PRODUCT_CLASS_CODE'].'",';
                        $row .= '"'.$item_l['PRODUCT_CLASS_NAME'].'",';
                        $row .= '"'.$item_l['PRODUCT_ORIGIN_COUNTRY_ID'].'",';
                        $row .= '"'.$item_l['PRODUCT_CHARGE_AMOUNT'].'",';
                        $row .= '"'.$item_l['PRODUCT_CHARGE_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['PRODUCT_ALLOWANCE_CHARGE_IND'].'",';
                        $row .= '"'.$item_l['PRODUCT_ALLOWANCE_ACTUAL_AMOUNT'].'",';
                        $row .= '"'.$item_l['PRODUCT_ALLOWANCE_ACTUAL_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['PRODUCT_ALLOWANCE_REASON_CODE'].'",';
                        $row .= '"'.$item_l['PRODUCT_ALLOWANCE_REASON'].'",';
                        $row .= '"'.$item_l['PRODUCT_QUANTITY'].'",';
                        $row .= '"'.$item_l['PRODUCT_UNIT_CODE'].'",';
                        $row .= '"'.$item_l['PRODUCT_QUANTITY_PER_UNIT'].'",';
                        $row .= '"'.$item_l['LINE_TAX_TYPE_CODE'].'",';
                        $row .= '"'.$item_l['LINE_TAX_CAL_RATE'].'",';
                        $row .= '"'.$item_l['LINE_BASIS_AMOUNT'].'",';
                        $row .= '"'.$item_l['LINE_BASIS_CURRENCY_CODE'].'",';

                        $row .= '"'.$item_l['LINE_TAX_CAL_AMOUNT'].'",';
                        $row .= '"'.$item_l['LINE_TAX_CAL_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['LINE_ALLOWANCE_CHARGE_IND'].'",';
                        $row .= '"'.$item_l['LINE_ALLOWANCE_ACTUAL_AMOUNT'].'",';
                        $row .= '"'.$item_l['LINE_ALLOWANCE_ACTUAL_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['LINE_ALLOWANCE_REASON_CODE'].'",';
                        $row .= '"'.$item_l['LINE_ALLOWANCE_REASON'].'",';
                        $row .= '"'.$item_l['LINE_TAX_TOTAL_AMOUNT'].'",';
                        $row .= '"'.$item_l['LINE_TAX_TOTAL_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['LINE_NET_TOTAL_AMOUNT'].'",';
                        $row .= '"'.$item_l['LINE_NET_TOTAL_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['LINE_NET_INCLUDE_TAX_TOTAL_AMOUNT'].'",';
                        $row .= '"'.$item_l['LINE_NET_INCLUDE_TAX_TOTAL_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['PRODUCT_REMARK'].'"';
                        $row .="\n";
                    }


                    $row_l = rtrim($row, ',');
                }else if($i==4){//data_type_F
                    $row = "";
                    foreach($data_type_F as $item_f)
                    {
                        $row .= '"'.$item_f.'",';
                    }
                    $row_f = rtrim($row, ',');
                    $row_f .="\n";
                }else if($i==5){//data_type_T
                    $row = "";
                    foreach($data_type_T as $item_t)
                    {
                        $row .= '"'.$item_t.'",';
                    }
                    $row_t = rtrim($row, ',');
                    $row_t .="\n";
                }
            }
                

                $rows = $row_c.$row_h.$row_b.$row_l.$row_f.$row_t;
                fwrite($output, $rows);
                fclose($output);
                unset($item);
                unset($row);
            
            fclose($output);

            //ob_flush();
            //$flashMessenger->setNamespace('success')->addMessage('Create CSV Done! Invoice Number : '.$creditnote_sn); 
            $db->commit();
            return $return_data = $creditnote_sn;

            //ob_start(); 
            while (@ob_end_flush());

            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            readfile($path);

            exit;

            $file = fopen($path, 'r');
            $content = fread($file, filesize($path));
            var_dump(filesize($path));
            var_dump($content);

            exit;

        }

        }catch (exception $e) {
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        }
    }

    /*---------------Create eTax CSV File------------------------*/

    function create_etax_invoice_csv_file($flashMessenger,$data_number,&$return_data) 
    {
        try
        {
        //set_time_limit(0);
        //error_reporting(~E_ALL);
        //ini_set('display_error', 0);
        //ini_set('memory_limit', -1);
        $QEtax      = new Application_Model_EtaxDocumentTran();    
        $document_type="IN";
        $date = date('Y-m-d H:i:s');

        $db = Zend_Registry::get('db');
        
        $result = $QEtax->get_invoice_number($data_number);
        //print_r($result);die;
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        if(!$result){return;}

        //$flashMessenger->setNamespace('success')->addMessage('Create CSV Done!'); 
        //return;
        foreach($result as $res)
        {
            $db->beginTransaction();
            $sales_sn = $res['sn'];
            $invoice_number = $res['invoice_number'];
            $filename = $invoice_number.uniqid().'.csv';
            //$filename = $sales_sn.'.txt';

            
            $sqlCheck="SELECT document_number,document_status FROM etax_document_tran WHERE document_number='".$invoice_number."'";
            $resultCheck = $db->fetchAll($sqlCheck);
            //print_r($resultCheck);die;
            if(!$resultCheck)
            {
                //------------ Insert -----------------
                try {
                    $dataEtax   = array('document_number'=>$invoice_number
                        ,'document_date'=>$res['invoice_date']
                        ,'distributor_id'=>$res['d_id']
                        ,'filename_csv'=>$filename
                        ,'document_type'=>$document_type
                        ,'document_active'=>1
                        ,'create_by'=>$userStorage->id
                        ,'create_date'=>$date);

                    //print_r($dataEtax);die;
                    $rs=$QEtax->insert($dataEtax);
                } catch (Exception $e) {
                    $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
                    return;
                }

            }else{
                //------------ Update -----------------
                    try {
                        if($resultCheck[0]['document_status']=="1"){
                            //$flashMessenger->setNamespace('error')->addMessage("มีการ Upload Invoice Number : " . $invoice_number. " ไปแล้ว");
                            //header(HOST.'finance/import-etax');
                            $db->rollback();
                            return;
                        }else{
                            $dataEtax   = array('filename_csv'=>$filename
                            ,'document_active'=>1
                            ,'update_by'=>$userStorage->id
                            ,'update_date'=>$date);

                            $where = array();
                            $where[] = $QEtax->getAdapter()->quoteInto('document_number = ?', $invoice_number);
                            $where[] = $QEtax->getAdapter()->quoteInto("((document_status <>'1') ||(document_status IS NULL))", null);
                            $update = $QEtax->update($dataEtax, $where);
                        }

                    } catch (Exception $e) {
                        $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
                        return;
                    }
                
            }

            // output headers so that the file is downloaded rather than displayed
            while (@ob_end_clean());
            ob_start();
            header('Content-Encoding: UTF-8');
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename='.$filename);
            

            $file_path_date = substr($res['invoice_date'],0,7).'/'.substr($res['invoice_date'],8,2).'/'.$document_type.'/csv/';
            $file_path = APPLICATION_PATH.'/../public/files/finance/etax/'.$file_path_date;

            if (!file_exists($file_path))
                mkdir($file_path, 0777, true);

            $path = $file_path.'/'.$filename;
            $output = fopen($path, 'w+');
            echo "\xEF\xBB\xBF"; // UTF-8 BOM

            /*-------------------------------Invoice Order---------------------------------------*/
            //DATA_TYPE = C
                $SELLER_TAX_ID="0745552000866"; //เลขประจำตัวผู้เสียภาษีอากรของผู้ขาย
                $SELLER_BRANCH_ID="00000";  //เลขสาขาสถานประกอบการ
                $data_type_C = array(
                                    'DATA_TYPE' => 'C',//ประเภทรายการ
                                    'SELLER_TAX_ID' => $SELLER_TAX_ID,//เลขประจำตัวผู้เสียภาษีอากรของผู้ขาย
                                    'SELLER_BRANCH_ID'  => $SELLER_BRANCH_ID,//เลขสาขาสถานประกอบการ
                                    'FILE_NAME'    => $filename,//ชื่อไฟล์
                                    'DATA_TEMPLATE_ID'    => date('Y-m-d').'T'.date('H:i:s'),//รหัส template text file
                                  );

                //DATA_TYPE = H
                if($res['doc_type']=='IV'){
                    $DOCUMENT_TYPE_CODE="T02"; //ประเภทเอกสาร 
                    $DOCUMENT_NAME="ใบแจ้งหนี้/ใบกำกับภาษี/ใบส่งของ";//ชื่อเอกสาร 
                    $BUYER_ORDER_REF_TYPE_CODE = "IV"; //ประเภทเอกสารอ้างอิงการสั่งซื้อ
                    $DOCUMENT_ID= $res['invoice_number'];//เลขที่เอกสาร
                    $DOCUMENT_ISSUE_DTM = $res['invoice_date'];//เอกสารลงวันที่
                }
                
                $data_type_H = array(
                                    'DATA_TYPE' => 'H',//ประเภทรายการ
                                    'DOCUMENT_TYPE_CODE' => $DOCUMENT_TYPE_CODE,//ประเภทเอกสาร 
                                    'DOCUMENT_NAME'  => $DOCUMENT_NAME,//ชื่อเอกสาร 
                                    'DOCUMENT_ID'    => $DOCUMENT_ID,//เลขที่เอกสาร
                                    'DOCUMENT_ISSUE_DTM'    => $DOCUMENT_ISSUE_DTM,//เอกสารลงวันที่
                                    'CREATE_PURPOSE_CODE'  => $CREATE_PURPOSE_CODE,//สาเหตุการออกเอกสาร
                                    'CREATE_PURPOSE'  => $CREATE_PURPOSE,//สาเหตุการออกเอกสาร
                                    'ADDITIONAL_REF_ASSIGN_ID'  => $ADDITIONAL_REF_ASSIGN_ID,//เลขที่เอกสารอ้างอิง
                                    'ADDITIONAL_REF_ISSUE_DTM'  => $ADDITIONAL_REF_ISSUE_DTM,//เอกสารอ้างอิงลงวันที่
                                    'ADDITIONAL_REF_TYPE_CODE'  => $ADDITIONAL_REF_TYPE_CODE,//ประเภทเอกสารอ้างอิง
                                    'ADDITIONAL_REF_DOCUMENT_NAME'  => $ADDITIONAL_REF_DOCUMENT_NAME,//ชื่อเอกสารอ้างอิง 
                                    'DELIVERY_TYPE_CODE'  => $DELIVERY_TYPE_CODE,//เงื่อนไขการส่งของ 
                                    'BUYER_ORDER_ASSIGN_ID'  => $res['sn_ref'],//เลขที่ใบสั่งซื้อ
                                    'BUYER_ORDER_ISSUE_DTM'  => $res['order_date'],//วันเดือนปี ที่ออกใบสั่งซื้อ
                                    'BUYER_ORDER_REF_TYPE_CODE'  => $BUYER_ORDER_REF_TYPE_CODE,//ประเภทเอกสารอ้างอิงการสั่งซื้อ
                                    'DOCUMENT_REMARK'  => $DOCUMENT_REMARK,//หมายเหตุท้ายเอกสาร
                                  );
                //print_r($data_type_H);die;
                //DATA_TYPE = B
                $BUYER_TAX_ID_TYPE="TXID";//ประเภทผู้เสียภาษีอากร 
                if($res['branch_no']!=''){
                    $BUYER_BRANCH_ID=$res['branch_no'];//เลขที่สาขา
                }else{
                    $BUYER_BRANCH_ID="00000";//เลขที่สาขา
                }

                if($res['post_code']!=''){
                    $BUYER_POST_CODE=$res['post_code'];//รหัสไปรษณีย์
                }else{
                    $BUYER_POST_CODE="00000";//รหัสไปรษณีย์
                }
                $data_type_B = array(
                                    'DATA_TYPE' => 'B',//ประเภทรายการ
                                    'BUYER_ID' => $res['store_code'],//รหัสผู้ซื้อ
                                    'BUYER_NAME'  => $res['title'],//ชื่อผู้ซื้อ
                                    'BUYER_TAX_ID_TYPE'    => $BUYER_TAX_ID_TYPE,//ประเภทผู้เสียภาษีอากร 
                                    'BUYER_TAX_ID'    => $res['mst_sn'],//เลขประจำตัวผู้เสียภาษีอากร
                                    'BUYER_BRANCH_ID'    => $BUYER_BRANCH_ID,//เลขที่สาขา
                                    'BUYER_CONTACT_PERSON_NAME'    => $res['contact_name'],//ชื่อผู้ติดต่อ
                                    'BUYER_CONTACT_DEPARTMENT_NAME'    => $BUYER_CONTACT_DEPARTMENT_NAME,//ชื่อแผนก
                                    'BUYER_URIID'    => $res['email'],//อีเมล
                                    'BUYER_PHONE_NO'    => $res['contact_tel'],//เบอร์โทรศัพท์
                                    'BUYER_POST_CODE'    => $BUYER_POST_CODE,//รหัสไปรษณีย์
                                    'BUYER_BUILDING_NAME'    => $BUYER_BUILDING_NAME,//ชื่ออาคาร
                                    'BUYER_BUILDING_NO'    => $BUYER_BUILDING_NO,//บ้านเลขที่
                                    'BUYER_ADDRESS_LINE1'    => $res['add_tax'],//ที่อยู่บรรทัดที่ 1
                                    'BUYER_ADDRESS_LINE2'    => $BUYER_ADDRESS_LINE2,//ที่อยู่บรรทัดที่ 2
                                    'BUYER_ADDRESS_LINE3'    => $BUYER_ADDRESS_LINE3,//ซอย
                                    'BUYER_ADDRESS_LINE4'    => $BUYER_ADDRESS_LINE4,//หมู่บ้าน
                                    'BUYER_ADDRESS_LINE5'    => $BUYER_ADDRESS_LINE5,//หมู่ที่
                                    'BUYER_STREET_NAME'    => $BUYER_STREET_NAME,//ถนน
                                    'BUYER_CITY_SUB_DIV_ID'    => $BUYER_CITY_SUB_DIV_ID,//รหัสตำบล
                                    'BUYER_CITY_SUB_DIV_NAME'    => $BUYER_CITY_SUB_DIV_NAME,//ชื่อตำบล
                                    'BUYER_CITY_ID'    => $BUYER_CITY_ID,//รหัสอำเภอ
                                    'BUYER_CITY_NAME'    => $BUYER_CITY_NAME,//ชื่ออำเภอ
                                    'BUYER_COUNTRY_SUB_DIV_ID'    => $BUYER_COUNTRY_SUB_DIV_ID,//รหัสจังหวัด
                                    'BUYER_COUNTRY_SUB_DIV_NAME'    => $BUYER_COUNTRY_SUB_DIV_NAME,//ชื่อจังหวัด
                                    'BUYER_COUNTRY_ID'    => "TH",//รหัสประเทศ
                                  );

            $r=1;
            $TAX_CAL_RATE = 7;//อัตราภาษี
            $GRAND_LINE_TOTAL_COUNT=0;
            $GRAND_LINE_NET_TOTAL_AMOUNT=0;
            $BASIS_AMOUNT=0;//มูลค่าสินค้า/บริการ (ไม่รวมภาษีมูลค่าเพิ่ม)
            $GRAND_TAX_TOTAL_AMOUNT=0;
            
            foreach($result as $item)
            {
                $PRODUCT_CHARGE_AMOUNT = number_format($item['price_in_vat'], 2, '.', '');//ราคาต่อหน่วย (in VAT)
                $PRODUCT_QUANTITY_PER_UNIT="1";//ขนาดบรรจุต่อหน่วยขาย
                $LINE_TAX_TYPE_CODE="VAT";//รหัสประเภทภาษี ภาษีที่ใช้มีอยู่หลายรูปแบบ เช่น Value Added Tax (VAT) 0% Vat 7% และ Exemption Tax (FRE)
                $LINE_TAX_CAL_RATE="7.00";//อัตราภาษี
                $LINE_BASIS_AMOUNT=number_format($item['price_ex_vat'], 2, '.', '');//มูลค่าสินค้า/บริการ (ไม่รวมภาษีมูลค่าเพิ่ม)
                $LINE_BASIS_CURRENCY_CODE="THB";//รหัสสกุลเงิน (มูลค่าสินค้า/บริการ)
                $LINE_TAX_CAL_AMOUNT=number_format(($PRODUCT_CHARGE_AMOUNT-$LINE_BASIS_AMOUNT), 2, '.', '');//มูลค่าภาษีมูลค่าเพิ่ม
                $LINE_TAX_CAL_CURRENCY_CODE="THB";//รหัสสกุลเงิน (มูลค่าภาษีมูลค่าเพิ่ม)

                //---------LINE_TOTAL-------------//

                $LINE_NET_INCLUDE_TAX_TOTAL_AMOUNT=number_format($item['total_amount_in_vat'], 2, '.', '');//จำนวนเงินรวม
                $LINE_NET_TOTAL_AMOUNT = number_format($item['total_amount_ex_vat'], 2, '.', '');//จำนวนเงินรวมก่อนภาษี
                $LINE_TAX_TOTAL_AMOUNT = number_format(($LINE_NET_INCLUDE_TAX_TOTAL_AMOUNT-$LINE_NET_TOTAL_AMOUNT), 2, '.', '');//ภาษีมูลค่าเพิ่ม

                //---------GRAND_TOTAL-------------//
                $LINE_TOTAL_COUNT += $item['num'];//จำนวนรายการสินค้า
                $data_type_L[] = array(
                                    'DATA_TYPE' => 'L',//ประเภทรายการ
                                    'LINE_ID' => $r,//ลำดับรายการ
                                    'PRODUCT_ID' => $item['good_code'],//รหัสสินค้า
                                    'PRODUCT_NAME' => $item['good_desc'],//ชื่อสินค้า
                                    'PRODUCT_DESC' => $PRODUCT_DESC,//รายละเอียดสินค้า
                                    'PRODUCT_BATCH_ID' => $PRODUCT_BATCH_ID,//ครั้งที่ผลิต
                                    'PRODUCT_EXPIRE_DTM' => $PRODUCT_EXPIRE_DTM,//วันหมดอายุ
                                    'PRODUCT_CLASS_CODE' => $PRODUCT_CLASS_CODE,//รหัสหมวดหมู่สินค้า
                                    'PRODUCT_CLASS_NAME' => $PRODUCT_CLASS_NAME,//ชื่อหมวดหมู่สินค้า
                                    'PRODUCT_ORIGIN_COUNTRY_ID' => "TH",//รหัสประเทศกำเนิด
                                    'PRODUCT_CHARGE_AMOUNT' => $PRODUCT_CHARGE_AMOUNT,//ราคาต่อหน่วย
                                    'PRODUCT_CHARGE_CURRENCY_CODE' => "THB",//รหัสสกุลเงิน (ราคาต่อหน่วย)
                                    'PRODUCT_ALLOWANCE_CHARGE_IND' => $PRODUCT_ALLOWANCE_CHARGE_IND,//ตัวบอกส่วนลดหรือค่าธรรมเนียม
                                    'PRODUCT_ALLOWANCE_ACTUAL_AMOUNT' => $PRODUCT_ALLOWANCE_ACTUAL_AMOUNT,//มูลค่าส่วนลดหรือค่าธรรมเนียม
                                    'PRODUCT_ALLOWANCE_ACTUAL_CURRENCY_CODE' => $PRODUCT_ALLOWANCE_ACTUAL_CURRENCY_CODE,//รหัสสกุลเงิน (มูลค่าส่วนลดหรือค่าธรรมเนียม)
                                    'PRODUCT_ALLOWANCE_REASON_CODE' => $PRODUCT_ALLOWANCE_REASON_CODE,//รหัสเหตุผลในการคิดส่วนลดหรือค่าธรรมเนียม
                                    'PRODUCT_ALLOWANCE_REASON' => $PRODUCT_ALLOWANCE_REASON,//เหตุผลในการคิดสวนลดหรือค่าธรรมเนียม
                                    'PRODUCT_QUANTITY' => $item['num'],//จำนวนสินค้า
                                    'PRODUCT_UNIT_CODE' => $PRODUCT_UNIT_CODE,//รหัสหน่วยสินค้า
                                    'PRODUCT_QUANTITY_PER_UNIT' => $PRODUCT_QUANTITY_PER_UNIT,//ขนาดบรรจุต่อหน่วยขาย
                                    'LINE_TAX_TYPE_CODE' => $LINE_TAX_TYPE_CODE,//รหัสประเภทภาษี ภาษีที่ใช้มีอยู่หลายรูปแบบ เช่น Value Added Tax (VAT) 0% Vat 7% และ Exemption Tax (FRE)
                                    'LINE_TAX_CAL_RATE' => $LINE_TAX_CAL_RATE,//อัตราภาษี
                                    'LINE_BASIS_AMOUNT' => $LINE_BASIS_AMOUNT,//มูลค่าสินค้า/บริการ (ไม่รวมภาษีมูลค่าเพิ่ม)
                                    'LINE_BASIS_CURRENCY_CODE' => $LINE_BASIS_CURRENCY_CODE,//รหัสสกุลเงิน (มูลค่าสินค้า/บริการ)
                                    'LINE_TAX_CAL_AMOUNT' => $LINE_TAX_CAL_AMOUNT,//มูลค่าภาษีมูลค่าเพิ่ม
                                    'LINE_TAX_CAL_CURRENCY_CODE' => $LINE_TAX_CAL_CURRENCY_CODE,//รหัสสกุลเงิน (มูลค่าภาษีมูลค่าเพิ่ม)
                                    'LINE_ALLOWANCE_CHARGE_IND' => $LINE_ALLOWANCE_CHARGE_IND,//จำนวนรายการสินค้า
                                    'LINE_ALLOWANCE_ACTUAL_AMOUNT' => $LINE_ALLOWANCE_ACTUAL_AMOUNT,//มูลค่าส่วนลดหรือค่าธรรมเนียม
                                    'LINE_ALLOWANCE_ACTUAL_CURRENCY_CODE' => $LINE_ALLOWANCE_ACTUAL_CURRENCY_CODE,//มูลค่าส่วนลดหรือค่าธรรมเนียม
                                    'LINE_ALLOWANCE_REASON_CODE' => $LINE_ALLOWANCE_REASON_CODE,//รหัสเหตุผลในการคิดส่วนลดหรือค่าธรรมเนียม
                                    'LINE_ALLOWANCE_REASON' => $LINE_ALLOWANCE_REASON,//เหตุผลในการคิดสวนลดหรือค่าธรรมเนียม
                                    'LINE_TAX_TOTAL_AMOUNT' => $LINE_TAX_TOTAL_AMOUNT,//ภาษีมูลค่าเพิ่ม
                                    'LINE_TAX_TOTAL_CURRENCY_CODE' => "THB",//รหัสสกุลเงิน (ภาษีมูลค่าเพิ่ม)
                                    'LINE_NET_TOTAL_AMOUNT' => $LINE_NET_TOTAL_AMOUNT,//จำนวนเงินรวมก่อนภาษี
                                    'LINE_NET_TOTAL_CURRENCY_CODE' => "THB",//รหัสสกุลเงิน (จำนวนเงินรวมก่อนภาษี)
                                    'LINE_NET_INCLUDE_TAX_TOTAL_AMOUNT' => $LINE_NET_INCLUDE_TAX_TOTAL_AMOUNT,//จำนวนเงินรวม
                                    'LINE_NET_INCLUDE_TAX_TOTAL_CURRENCY_CODE' => "THB",//รหัสสกุลเงิน (จำนวนเงินรวม)
                                    'PRODUCT_REMARK' => $PRODUCT_REMARK,//หมายเหตุท้ายสินค้า
                                  );
                $r+=1;
            }

            //DATA_TYPE = F 
            $DELIVERY_OCCUR_DTM=$res['invoice_date'];//วันเวลานัดส่งสินค้า
            $PAYMENT_DUE_DTM=$res['invoice_date'];//วันครบกำหนดชำระเงิน

            $ALLOWANCE_CHARGE_IND="";//ตัวบอกส่วนลดหรือค่าธรรมเนียม
            $ALLOWANCE_ACTUAL_AMOUNT=number_format($res['total_spc_discount'], 2, '.', '');//มูลค่าส่วนลดหรือค่าธรรมเนียม
            $ALLOWANCE_TOTAL_AMOUNT=number_format($res['total_spc_discount'], 2, '.', '');//ส่วนลดทั้งหมด

            $BASIS_AMOUNT = number_format($res['grand_total_ex_vat'], 2, '.', '');//มูลค่าสินค้า/บริการ (ไม่รวมภาษีมูลค่าเพิ่ม)

            $TAX_CAL_AMOUNT = number_format($res['grand_total_in_vat']-$res['grand_total_ex_vat'], 2, '.', '');//มูลค่าภาษีมูลค่าเพิ่ม

            $TAX_BASIS_TOTAL_AMOUNT = number_format($res['grand_total_amount_ex_vat'], 2, '.', '');//มูลค่าที่นำมาคิดภาษีมูลค่าเพิ่ม
            $GRAND_TOTAL_AMOUNT = number_format($res['grand_total_amount_in_vat'], 2, '.', '');//จำนวนเงินรวม (รวมภาษีมูลค่าเพิ่ม)
            $GRAND_LINE_TAX_TOTAL_AMOUNT = number_format(($GRAND_TOTAL_AMOUNT-$TAX_BASIS_TOTAL_AMOUNT), 2, '.', '');//จำนวนภาษีมูลค่าเพิ่ม

            $LINE_TOTAL_AMOUNT = number_format($res['grand_total_ex_vat'], 2, '.', '');//รวมมูลค่าตามรายการ/มูลค่าที่ถูกต้อง

            $data_type_F = array(
                                    'DATA_TYPE' => 'F', //ประเภทรายการ
                                    'LINE_TOTAL_COUNT' => $LINE_TOTAL_COUNT,//จำนวนรายการสินค้า
                                    'DELIVERY_OCCUR_DTM' => $DELIVERY_OCCUR_DTM,//วันเวลานัดส่งสินค้า
                                    'INVOICE_CURRENCY_CODE' => "THB",//รหัสสกุลเงินตรา
                                    'TAX_TYPE_CODE' => "VAT",//รหัสประเภทภาษี ภาษีที่ใช้มีอยู่หลายรูปแบบ เช่น Value Added Tax (VAT) 0% Vat 7% และ Exemption Tax (FRE)
                                    'TAX_CAL_RATE' => $TAX_CAL_RATE,//อัตราภาษี
                                    'BASIS_AMOUNT' => $BASIS_AMOUNT,//มูลค่าสินค้า/บริการ (ไม่รวมภาษีมูลค่าเพิ่ม)
                                    'BASIS_CURRENCY_CODE' => "THB",//มูลค่าสินค้า/บริการ (ไม่รวมภาษีมูลค่าเพิ่ม)
                                    'TAX_CAL_AMOUNT' => $TAX_CAL_AMOUNT,//มูลค่าภาษีมูลค่าเพิ่ม
                                    'TAX_CAL_CURRENCY_CODE' => "THB",//รหัสสกุลเงิน (ภาษีมูลค่าเพิ่ม)
                                    'ALLOWANCE_CHARGE_IND' => $ALLOWANCE_CHARGE_IND,//ตัวบอกส่วนลดหรือค่าธรรมเนียม
                                    'ALLOWANCE_ACTUAL_AMOUNT' => $ALLOWANCE_ACTUAL_AMOUNT,//มูลค่าส่วนลดหรือค่าธรรมเนียม
                                    'ALLOWANCE_ACTUAL_CURRENCY_CODE' => "THB",//รหัสสกุลเงิน (มูลค่าส่วนลดหรือค่าธรรมเนียม)
                                    'ALLOWANCE_REASON_CODE' => $ALLOWANCE_REASON_CODE,//รหัสเหตุผลในการคิดส่วนลดหรือค่าธรรมเนียม
                                    'ALLOWANCE_REASON' => $ALLOWANCE_REASON,//เหตุผลในการคิดสวนลดหรือค่าธรรมเนียม
                                    'PAYMENT_TYPE_CODE' => $PAYMENT_TYPE_CODE,//รหัสประเภทส่วนลดหรือค่าธรรมเนียม
                                    'PAYMENT_DESCRIPTION' => $PAYMENT_DESCRIPTION,//รายละเอียดเงื่อนไขการชำระเงิน
                                    'PAYMENT_DUE_DTM' => $PAYMENT_DUE_DTM,//วันครบกำหนดชำระเงิน
                                    'ORIGINAL_TOTAL_AMOUNT' => $ORIGINAL_TOTAL_AMOUNT,//รวมมูลค่าตามเอกสารเดิม
                                    'ORIGINAL_TOTAL_CURRENCY_CODE' => $ORIGINAL_TOTAL_CURRENCY_CODE,//รวมมูลค่าตามเอกสารเดิม
                                    'LINE_TOTAL_AMOUNT' => $LINE_TOTAL_AMOUNT,//รวมมูลค่าตามรายการ/มูลค่าที่ถูกต้อง
                                    'LINE_TOTAL_CURRENCY_CODE' => "THB",//รวมมูลค่าตามรายการ/มูลค่าที่ถูกต้อง
                                    'ADJUSTED_INFORMATION_AMOUNT' => $ADJUSTED_INFORMATION_AMOUNT,//มูลค่าผลต่าง
                                    'ADJUSTED_INFORMATION_CURRENCY_CODE' => $ADJUSTED_INFORMATION_CURRENCY_CODE,//มูลค่าผลต่าง
                                    'ALLOWANCE_TOTAL_AMOUNT' => $ALLOWANCE_TOTAL_AMOUNT,//ส่วนลดทั้งหมด
                                    'ALLOWANCE_TOTAL_CURRENCY_CODE' => "THB",//ส่วนลดทั้งหมด
                                    'CHARGE_TOTAL_AMOUNT' => $CHARGE_TOTAL_AMOUNT,//ค่าธรรมเนียมทั้งหมด
                                    'CHARGE_TOTAL_CURRENCY_CODE' => $CHARGE_TOTAL_CURRENCY_CODE,//ค่าธรรมเนียมทั้งหมด
                                    'TAX_BASIS_TOTAL_AMOUNT' => $TAX_BASIS_TOTAL_AMOUNT,//มูลค่าที่นำมาคิดภาษีมูลค่าเพิ่ม
                                    'TAX_BASIS_TOTAL_CURRENCY_CODE' => "THB",//มูลค่าที่นำมาคิดภาษีมูลค่าเพิ่ม
                                    'TAX_TOTAL_AMOUNT' => $GRAND_LINE_TAX_TOTAL_AMOUNT,//จำนวนภาษีมูลค่าเพิ่ม
                                    'TAX_TOTAL_CURRENCY_CODE' => "THB",//จำนวนภาษีมูลค่าเพิ่ม
                                    'GRAND_TOTAL_AMOUNT' => $GRAND_TOTAL_AMOUNT,//จำนวนเงินรวม (รวมภาษีมูลค่าเพิ่ม)
                                    'GRAND_TOTAL_CURRENCY_CODE' => "THB",//จำนวนเงินรวม (รวมภาษีมูลค่าเพิ่ม)
                                  );

            $data_type_T = array(
                                    'DATA_TYPE' => 'T',//ประเภทรายการ
                                    'TOTAL_DOCUMENT_COUNT' => 1,//จำนวนเอกสารทั้งหมด
                                  );
                    
            /*-------------------------------End Invoice Order---------------------------------------*/

            $rows = "";
            for($i=0;$i<6;$i++)
            {
                
                if($i==0){//data_type_C
                    $row = "";
                    foreach($data_type_C as $item_c)
                    {
                        $row .= '"'.$item_c.'",';
                    }
                    $row_c = rtrim($row, ',');
                    $row_c .="\n";
                }else if($i==1){//data_type_H
                    $row = "";
                    //print_r($data_type_H);die;
                    foreach($data_type_H as $item_h)
                    {
                        $row .= '"'.$item_h.'",';
                    }
                    $row_h = rtrim($row, ',');
                    $row_h .="\n";
                }else if($i==2){//data_type_B
                    $row = "";
                    foreach($data_type_B as $item_b)
                    {
                        $row .= '"'.$item_b.'",';
                    }
                    $row_b = rtrim($row, ',');
                    $row_b .="\n";
                }else if($i==3){//data_type_L
                    $row = "";
                    foreach($data_type_L as $item_l)
                    {
                        $row .= '"'.$item_l['DATA_TYPE'].'",';
                        $row .= '"'.$item_l['LINE_ID'].'",';
                        $row .= '"'.$item_l['PRODUCT_ID'].'",';
                        $row .= '"'.$item_l['PRODUCT_NAME'].'",';
                        $row .= '"'.$item_l['PRODUCT_DESC'].'",';
                        $row .= '"'.$item_l['PRODUCT_BATCH_ID'].'",';
                        $row .= '"'.$item_l['PRODUCT_EXPIRE_DTM'].'",';
                        $row .= '"'.$item_l['PRODUCT_CLASS_CODE'].'",';
                        $row .= '"'.$item_l['PRODUCT_CLASS_NAME'].'",';
                        $row .= '"'.$item_l['PRODUCT_ORIGIN_COUNTRY_ID'].'",';
                        $row .= '"'.$item_l['PRODUCT_CHARGE_AMOUNT'].'",';
                        $row .= '"'.$item_l['PRODUCT_CHARGE_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['PRODUCT_ALLOWANCE_CHARGE_IND'].'",';
                        $row .= '"'.$item_l['PRODUCT_ALLOWANCE_ACTUAL_AMOUNT'].'",';
                        $row .= '"'.$item_l['PRODUCT_ALLOWANCE_ACTUAL_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['PRODUCT_ALLOWANCE_REASON_CODE'].'",';
                        $row .= '"'.$item_l['PRODUCT_ALLOWANCE_REASON'].'",';
                        $row .= '"'.$item_l['PRODUCT_QUANTITY'].'",';
                        $row .= '"'.$item_l['PRODUCT_UNIT_CODE'].'",';
                        $row .= '"'.$item_l['PRODUCT_QUANTITY_PER_UNIT'].'",';
                        $row .= '"'.$item_l['LINE_TAX_TYPE_CODE'].'",';
                        $row .= '"'.$item_l['LINE_TAX_CAL_RATE'].'",';
                        $row .= '"'.$item_l['LINE_BASIS_AMOUNT'].'",';
                        $row .= '"'.$item_l['LINE_BASIS_CURRENCY_CODE'].'",';

                        $row .= '"'.$item_l['LINE_TAX_CAL_AMOUNT'].'",';
                        $row .= '"'.$item_l['LINE_TAX_CAL_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['LINE_ALLOWANCE_CHARGE_IND'].'",';
                        $row .= '"'.$item_l['LINE_ALLOWANCE_ACTUAL_AMOUNT'].'",';
                        $row .= '"'.$item_l['LINE_ALLOWANCE_ACTUAL_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['LINE_ALLOWANCE_REASON_CODE'].'",';
                        $row .= '"'.$item_l['LINE_ALLOWANCE_REASON'].'",';
                        $row .= '"'.$item_l['LINE_TAX_TOTAL_AMOUNT'].'",';
                        $row .= '"'.$item_l['LINE_TAX_TOTAL_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['LINE_NET_TOTAL_AMOUNT'].'",';
                        $row .= '"'.$item_l['LINE_NET_TOTAL_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['LINE_NET_INCLUDE_TAX_TOTAL_AMOUNT'].'",';
                        $row .= '"'.$item_l['LINE_NET_INCLUDE_TAX_TOTAL_CURRENCY_CODE'].'",';
                        $row .= '"'.$item_l['PRODUCT_REMARK'].'"';
                        $row .="\n";
                    }


                    $row_l = rtrim($row, ',');
                }else if($i==4){//data_type_F
                    $row = "";
                    foreach($data_type_F as $item_f)
                    {
                        $row .= '"'.$item_f.'",';
                    }
                    $row_f = rtrim($row, ',');
                    $row_f .="\n";
                }else if($i==5){//data_type_T
                    $row = "";
                    foreach($data_type_T as $item_t)
                    {
                        $row .= '"'.$item_t.'",';
                    }
                    $row_t = rtrim($row, ',');
                    $row_t .="\n";
                }
            }
                

                $rows = $row_c.$row_h.$row_b.$row_l.$row_f.$row_t;
                fwrite($output, $rows);
                fclose($output);
                unset($item);
                unset($row);
            
            fclose($output);

            //ob_flush();
            //$flashMessenger->setNamespace('success')->addMessage('Create CSV Done! Invoice Number : '.$invoice_number); 
            $db->commit();
            return $return_data = $invoice_number;

            //ob_start(); 
            while (@ob_end_flush());

            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            readfile($path);

            exit;

            $file = fopen($path, 'r');
            $content = fread($file, filesize($path));
            var_dump(filesize($path));
            var_dump($content);

            exit;

        }

        }catch (exception $e) {
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        }
    }

    function get_invoice_number($invoice_number)
    {
        $db = Zend_Registry::get('db');
        $sql="
            SELECT 
              DISTINCT p.id,
              p.*,
              p.sn_ref,  d.name,  d.title,  d.rank,  d.mst_sn,  d.unames,d.name as contact_name,d.tel as contact_tel,
              d.store_code,  d.district,  d.add_tax,  d.region,  p.confirm_access_status,'00000' as post_code,
              p.confirm_access_remark,  p.confirm_access_by,  p.order_accessories,  d.finance_group,
              d.quota_channel,  d.del,  dg.group_type_id,  s.id AS sales_catty_id,  s.code AS sales_catty_code,
              TRIM(CONCAT( s.firstname,' ',s.lastname,'[',s.email,']')) AS sales_catty_name,
              (SELECT t.name FROM tag_object tg LEFT JOIN tag t ON tg.tag_id = t.id 
              WHERE tg.object_id = p.sn LIMIT 1) AS tax_po,
              d.finance_group,  ds.delivery_order_id,
              do.tracking_no,  a.name AS sales_area,

              (SELECT ROUND( ROUND( ROUND( ( IF( mm.sale_off_percent > 0, ROUND( ( ( ( mm.price * mm.sale_off_percent / 100 ) * 100 ) / 100 ), 2 ), mm.price ) / 1.07 ), 2 ), 2 ) * 1.07, 2 ) 
              FROM market mm 
              WHERE mm.sn = p.sn 
                AND p.good_id = mm.good_id AND p.good_color = mm.good_color 
              GROUP BY mm.sn) AS price_in_vat,
              (SELECT ROUND( ROUND( ROUND( ( IF( mm.sale_off_percent > 0, ROUND( ( ( ( mm.price * mm.sale_off_percent / 100 ) * 100 ) / 100 ), 2 ), mm.price ) / 1.07 ), 2 ), 2 ) , 2 ) 
              FROM market mm 
              WHERE mm.sn = p.sn 
                AND p.good_id = mm.good_id AND p.good_color = mm.good_color 
              GROUP BY mm.sn) AS price_ex_vat,


              (SELECT ROUND( ROUND( ROUND( ( IF( mm.sale_off_percent > 0, ROUND( ( ( ( mm.price * mm.sale_off_percent / 100 ) * 100 ) / 100 ), 2 ), mm.price ) / 1.07 ), 2 ) * mm.num, 2 ) * 1.07, 2 ) 
              FROM market mm 
              WHERE mm.sn = p.sn 
                AND p.good_id = mm.good_id AND p.good_color = mm.good_color 
              GROUP BY mm.sn) AS total_amount_in_vat,
              (SELECT ROUND( ( if( mm.sale_off_percent > 0, ROUND( ( ( ( mm.price * mm.sale_off_percent / 100 ) * 100 ) / 100 ), 2 ), mm.price ) / 1.07 ), 2 ) * mm.num 
              FROM market mm 
              WHERE mm.sn = p.sn 
              and p.good_id = mm.good_id and p.good_color = mm.good_color 
              group by mm.sn) AS total_amount_ex_vat,
              

             (SELECT ROUND( SUM( ROUND( ( IF( mm.sale_off_percent > 0, ROUND( ( ( ( mm.price * mm.sale_off_percent / 100 ) * 100 ) / 100 ), 2 ), mm.price ) / 1.07 ), 2 ) * mm.num * 1.07 ), 2 ) 
              FROM    market mm 
              WHERE mm.sn = p.sn 
              GROUP BY mm.sn) AS grand_total_in_vat,

             (SELECT ROUND( SUM( ROUND( ( IF( mm.sale_off_percent > 0, ROUND( ( ( ( mm.price * mm.sale_off_percent / 100 ) * 100 ) / 100 ), 2 ), mm.price ) / 1.07 ), 2 ) * mm.num ), 2 ) 
              FROM    market mm 
              WHERE mm.sn = p.sn 
              GROUP BY mm.sn) AS grand_total_ex_vat,
              
              (SELECT ROUND( SUM( ROUND( ( IF( mm.sale_off_percent > 0, ROUND( ( ( ( mm.price * mm.sale_off_percent / 100 ) * 100 ) / 100 ), 2 ), mm.price ) / 1.07 ), 2 ) * mm.num  )-(mm.total_spc_discount), 2 ) 
              FROM    market mm 
              WHERE mm.sn = p.sn 
              GROUP BY mm.sn) AS grand_total_amount_ex_vat,

              (SELECT ROUND( SUM( ROUND( ( IF( mm.sale_off_percent > 0, ROUND( ( ( ( mm.price * mm.sale_off_percent / 100 ) * 100 ) / 100 ), 2 ), mm.price ) / 1.07 ), 2 ) * mm.num * 1.07 )-(mm.total_spc_discount*1.07), 2 ) 
              FROM    market mm 
              WHERE mm.sn = p.sn 
              GROUP BY mm.sn) AS grand_total_amount_in_vat,

              p.salesman as sales_admin_id,
              CONCAT(ss.firstname, ' ', ss.lastname) AS sales_admin,
              p.bs_campaign,
              (SELECT concat('''', ph.phone_number_sn) FROM phone_number ph where ph.sales_order = p.sn and ph.status = 1) as phone_number_sn 
              ,g.name as good_code,g.desc as good_name,concat(g.desc,' ',gc.name) as good_desc,gc.name as color_name
              ,CONCAT(DATE_FORMAT(p.add_time, '%Y-%m-%d'),'T',DATE_FORMAT(p.add_time, '%H:%i:%s'))AS order_date
              ,concat(DATE_FORMAT(p.invoice_time, '%Y-%m-%d'),'T',DATE_FORMAT(p.invoice_time, '%H:%i:%s'))as invoice_date
              ,'IV' as doc_type
            FROM
              market AS p 
              LEFT JOIN distributor AS d ON d.id = p.d_id 
              LEFT JOIN distributor_group AS dg ON dg.group_id = d.group_id 
              LEFT JOIN hr.staff AS s ON s.id = p.sales_catty_id 
              LEFT JOIN delivery_sales AS ds ON ds.sales_sn = p.sn 
              LEFT JOIN delivery_order AS do ON do.id = ds.delivery_order_id 
              LEFT JOIN hr.regional_market AS rm ON s.regional_market = rm.id 
              LEFT JOIN hr.area AS a ON rm.area_id = a.id 
              LEFT JOIN warehouse.staff AS ss ON p.salesman = ss.id 
              left join good g on g.id=p.good_id
              left join good_color gc on gc.id = p.good_color
            WHERE (p.status = '1') 
              AND (p.isbacks = 0) 
              AND ( p.sn not in (SELECT m.sn FROM market m WHERE m.order_accessories = 1 AND m.confirm_access_date IS NULL GROUP BY m.sn)  ) 
              AND ( p.invoice_number = '".$invoice_number."')
              AND (p.old_data is null)
            ORDER BY p.sn asc";
            //echo $sql;die;
        $result = $db->fetchAll($sql);
        //print_r($result);die;
        return $result;
    }



    function get_credit_note_number($type,$cp_no)
    {
        $db = Zend_Registry::get('db');
        $cp_info = array();
        $cp_no = trim($cp_no);
        $item = array();
        $item['cp_no'] = $cp_no;
        $item['doc_type'] = $type;
        //Get CP info 
        $sql   = "SELECT * FROM `credit_note` WHERE creditnote_sn='".$cp_no."'";
        $row = $db->fetchRow($sql);
        $item['distributor_id']   = $row['distributor_id'];
        $item['create_date']   = $row['create_date'];

        $sql   = "SELECT id,store_code,unames,`add`,mst_sn,branch_no,parent,email,name,tel,add_tax FROM `distributor` WHERE id='".$row['distributor_id']."'";
        $row = $db->fetchRow($sql);
        $item['store_code'] = $row['store_code'];
        $item['distributor_name'] = $row['unames'];
        $item['distributor_id'] = $row['id'];
        $item['distributor_branch_name']  = $row['parent'] ==0 ? 'สำนักงานใหญ่':'สาขา';
        $item['distributor_branch_no']  = $row['branch_no'];
        $item['distributor_address'] = $row['add'];
        $item['distributor_tax_id']  = $row['mst_sn'];
        $item['email']  = $row['email'];
        $item['post_code']  = "00000";
        $item['add_tax']  = $row['add_tax'];
        $item['contact_name']  = $row['name'];
        $item['contact_tel']  = $row['tel'];
        
        $item['act'] = 'none';
        // print_r($item);die;
        //find INV list1883
        if(!isset($item['invoice'])){
            $item['invoice'] = array();
        }

        if($type=='CP') {

            $sql   = "SELECT count(b.invoice_number) as num,m.total as total,
            (SELECT MAX(mm.spc_discount)AS spc_discount FROM warehouse.market AS mm WHERE mm.sn = b.sales_sn)as spc_discount
            ,m.spc_discount_phone,d.rank,b.invoice_number,g.name as product_name,g.`desc` as product_call_name
            ,c.name as product_color,b.price as price,b.out_price,b.remark
            FROM $dbname.`credit_note` cr 
            join $dbname.bvg_imei as b on b.creditnote_sn=cr.creditnote_sn
            join $dbname.distributor as d on d.id=cr.distributor_id
            join $dbname.good as g on g.id = b.good_id
            join $dbname.good_color as c on c.id = b.good_color
            left join $dbname.market as m on m.sn = b.sales_sn and b.good_id = m.good_id and b.good_color = m.good_color
            WHERE cr.creditnote_sn IN('".$cp_no."') and b.status = 1
            group by b.invoice_number,b.good_id,b.good_color,b.out_price;";
            //echo $sql;die;
            $result = $db->fetchAll($sql);
            foreach ($result as $value) {
                $item['invoice'][] = $value;
            }
            //Find IMEI list
            $sql = "SELECT b.`imei_sn`,b.invoice_number,g.name as product_name,g.`desc` as product_call_name,c.name as product_color 
            ,'' as damage_detail ,''as rtn_number
            FROM $dbname.`bvg_imei` b 
            join $dbname.good as g on g.id = b.good_id
            join $dbname.good_color as c on c.id = b.good_color
            WHERE b.creditnote_sn IN('".$cp_no."') and b.status = 1
            order by b.`invoice_number`;";

            //echo $sql;die;
            $result = $db->fetchAll($sql);
            foreach ($result as $value) {
                $item['imei'][] = $value;
            }

            // echo $sql;die;
            // print_r($item);die;
            if (!$item['invoice']) {
                $item['act'] = 'powerby';

                $sql   = "SELECT `cr`.`creditnote_sn`, `cr`.`invoice_number`, count(`cr`.`invoice_number`) as num ,m.total as total, g.name as product_name,g.`desc` as product_call_name,c.name as product_color,cr.price_unit as price,cr.total_price as out_price,cr.remark ,cr.correct_bal_amount,cr.difference_amount,cr.total_VAT,cr.total_amount
                FROM `credit_note_cp_import` AS `cr` 
                left join $dbname.credit_note as crn on crn.creditnote_sn = cr.creditnote_sn
                join $dbname.good as g on g.id = cr.good_id
                join $dbname.good_color as c on c.id = cr.good_color
                left join $dbname.market as m on m.sn = crn.sn and cr.good_id = m.good_id and cr.good_color = m.good_color 
                WHERE (cr.creditnote_sn IN('".$cp_no."')) GROUP BY `cr`.`invoice_number`,cr.good_id,cr.good_color";
                //echo $sql;die;

                $result = $db->fetchAll($sql);
                foreach ($result as $value) {
                    $item['invoice'][] = $value;
                }
                //Find IMEI list
                $sql = "SELECT cr.`imei_sn`,cr.invoice_number,g.name as product_name,g.`desc` as product_call_name,c.name as product_color FROM $dbname.`credit_note_cp_import` cr 
                join $dbname.good as g on g.id = cr.good_id
                join $dbname.good_color as c on c.id = cr.good_color
                WHERE cr.creditnote_sn IN('".$cp_no."') 
                order by cr.`invoice_number`;";
                // echo $sql;die;

                $result = $db->fetchAll($sql);
                foreach ($result as $value) {
                    $item['imei'][] = $value;
                }         

            }          
            $cp_info[] = $item;
        }

        //1= EOL 2=เครื่องเสีย DOA/DAP 3=Demo 4=กรณีพิเศษ/อื่นๆ
        if ($type=='CN') {

            $sql   = "SELECT m.num as num,m.total as total,m.invoice_number,g.name as product_name,g.`desc` as product_call_name,c.name as product_color,(m.total/m.num) as price1,m.price,m.price_ext as       price_ext,m.text as remark ,cr.create_date as time_line,m.box_sn
            FROM $dbname.`credit_note` cr 
            join $dbname.market as m on m.creditnote_sn = cr.creditnote_sn 
            join $dbname.good as g on m.good_id=g.id
            join $dbname.good_color as c on m.good_color=c.id

            WHERE cr.creditnote_sn IN('".$cp_no."')
            group by cr.creditnote_sn ,m.invoice_number,m.price,m.price,m.cat_id,m.good_id,m.good_color 
            order by m.invoice_number,m.cat_id,m.good_id,m.good_color;";
            //echo $sql;die;
            $result = $db->fetchAll($sql);
            foreach ($result as $value) {
                $item['invoice'][] = $value;
            }

            //Find IMEI list
            $sql = "SELECT imr.`imei_sn`,m.invoice_number,g.name as product_name,g.`desc` as product_call_name,c.name as product_color, m.price_ext as price ,m.text as remark,imb.return_type,imb.damage_detail ,m.sale_off_percent,imb.rtn_number,imb.box_sn
            FROM $dbname.`imei_return` imr
            left join $dbname.return_box_imei as imb on imb.imei_sn=imr.imei_sn and imb.return_sn=imr.return_sn
            join $dbname.imei as im on im.imei_sn=imr.imei_sn
            join $dbname.market as m on m.cat_id=11 AND im.`good_id`=m.`good_id` AND im.`good_color`=m.`good_color` AND imr.`sales_order_sn` = m.`sn`
            join $dbname.good as g on im.good_id=g.id
            join $dbname.good_color as c on im.good_color=c.id
            WHERE imr.creditnote_sn IN('".$cp_no."') and m.canceled != 1
            group by im.good_id,im.good_color,imr.imei_sn
            order by im.good_id,imb.return_type;";
            //echo $sql; die;
            $result = $db->fetchAll($sql);
            $row_num =0;
            foreach ($result as $value) {
                $item['imei'][] = $value;
                $row_num +=1;
            }
            $cp_info[] = $item;
            //print_r($cp_info);die;

            if(count($row_num) <= 0){
                $sql = "SELECT imr.sn as imei_sn,m.invoice_number,g.name as product_name,g.`desc` as product_call_name,c.name as product_color, m.price_ext as price ,m.text as remark ,m.sale_off_percent 
                FROM $dbname.`digital_sn_return` imr 
                join $dbname.market as m on m.cat_id=13 AND imr.`sales_order_sn` = m.`sn`
                join $dbname.good as g on m.good_id=g.id
                join $dbname.good_color as c on m.good_color=c.id
                WHERE imr.creditnote_sn IN('".$cp_no."') and m.canceled != 1
                group by m.good_id,m.good_color,imr.sn
                order by m.good_id;";

                $result_digital = $db->fetchAll($sql);
                foreach ($result_digital as $value) {
                    $item['imei'][] = $value;
                }
                $cp_info[] = $item;
            }

        }

        if ($type=='OPPO') {
            $sql =   "SELECT `op`.`quater_year`, `op`.`quater_no`, `a`.`name` AS `area_name`, `rm2`.`name` AS `province`, `rm`.`name` AS `district`, `dis`.`id` AS `d_id,dis.store_code`, `dis`.`title`, `dis`.`mst_sn`, `dis`.`tel`, `op`.`level_name`, `op`.`total_imei`, `op`.`total_price`, `op`.`creditnote_price`, `op`.`creditnote_price_confirm`, `op`.`confirm_date`, `op`.`start_date`, `op`.`end_date`, `op`.`creditnote_sn`, `op`.`decorate_status`, `dis`.`add` AS `add_tax` 
            FROM `oppoclub_reward_cn` AS `op`
            LEFT JOIN `distributor` AS `dis` ON dis.id = op.distributor_id
            LEFT JOIN `hr`.`regional_market` AS `rm` ON dis.district = rm.id
            LEFT JOIN `hr`.`regional_market` AS `rm2` ON dis.region = rm2.id
            LEFT JOIN `hr`.`area` AS `a` ON rm2.area_id = a.id  
            WHERE (level_name in ('Platinum', 'Gold', 'Silver')) AND (op.quater_year in(2016,2017,2018)) AND (op.quater_no in('Quater_01','Quater_02','Quater_03','Quater_04')) AND (op.creditnote_sn in ('".$cp_no."')) ;";
            $result = $db->fetchAll($sql);
            foreach ($result as $value) {
                $item['oppo'][] = $value;
            }

            $sql =   "SELECT  count(imei.good_id) FROM `oppoclub_reward_cn_imei` AS `imei` WHERE (imei.creditnote_sn in ('".$cp_no."')) GROUP BY `imei`.`good_id`" ;           
            // echo $sql;      
            $result = $db->fetchAll($sql);

            foreach ($result as $value) {
                $item['sum_good'][] = $value;
            }

            $cp_info[] = $item;
        }

        return $cp_info;
    }




    //--------------------Send To eTax API---------------------------//

	public function get_etax_document($document_type)
    {

        $db = Zend_Registry::get('db');
        /*$sqlCheck="SELECT document_number,distributor_id,document_type,document_status,document_date,filename_csv,filename_pdf 
        FROM etax_document_tran 
        where document_active=1
        and document_type='".$document_type."'
        and document_status is null";*/

        $sqlCheck="SELECT document_number,distributor_id,document_type,document_date,filename_csv,filename_pdf 
        FROM etax_document_tran 
        where document_active=1 
        -- and document_number='IN610709-01356'
         ";

        $resultCheck = $db->fetchAll($sqlCheck);

        return $resultCheck;
    }

    function getFile($file, $newFileName) 
    { 
        try{
            $err_msg = ''; 
            //echo "<br>Attempting message download for $file<br>"; 
            $out = fopen($newFileName, 'wb'); 
            if ($out == FALSE){ 
              return "File not opened"; 
              exit; 
            } 
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_FILE, $out); 
            curl_setopt($ch, CURLOPT_HEADER, 0); 
            curl_setopt($ch, CURLOPT_URL, $file); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $rs = curl_exec($ch); 
            //print_r($rs);
            //echo "<br>Error is : ".curl_error ( $ch); 
            curl_close($ch); 
            fclose($out); 

            return $rs;
        }catch (exception $e) {
            return $e->getMessage();
        }
    }

    //http://local.wms-new/etax-api/cron-etax
    public function SendEtax($document_type)
    {

      set_time_limit(0);
      ini_set('memory_limit', -1);
      //echo "OK";die;
      $date = date('Y-m-d H:i:s');
      $userStorage = Zend_Auth::getInstance()->getStorage()->read();
      $QEtax      = new Application_Model_EtaxDocumentTran();
      $QEtaxErrorLog      = new Application_Model_EtaxDocumentErrorLog();

      $SellerTaxId="0745552000866";
      $SellerBranchId="00000";
      $APIKey="AK2-3UY8R84Q6IO3ECGNHA1WWENX070AVSF0FC54WMEBNN7BUES6LOVZDYXZJ4JQ9EHKNYQ8PCZX7JBHMNTL2OOEIYIOB3W62S76EW9XSBYSGN0TNVWW6K2XJXACGI9BFEWZN1UZUQB9P67Y6Q2CP5AZ3FQITKUTG7YME161EHBPA4OQL8Y4D8UDHZBEXCC9PDFLXOSN7CGBD119IP71R8H7YVSYLPLJ0FORHAH7OVQ348GELQBXNIMLZYXLKKQHG5GA5";
      $UserCode="watcharagon";
      $AccessKey="P@ssw0rd";
      $ServiceCode="S06";
      $TextContent="";
      $PDFContent="";

      /*-----WEB------
    Web URL https://uatetaxsp.one.th/etaxsp
    Web User    watcharagon
    Web Password    watcharagon001
    key 0745552000866
      */
        $res_data = $this->get_etax_document($document_type);
        //print_r($res_data);die;
        if(!$res_data){
        	echo "No Data";
        	return;
        }

        //print_r($res_data);die;
        $db = Zend_Registry::get('db');
        foreach($res_data as $result)
            {

            $db->beginTransaction();
            try{
                
                $invoice_number=$result['document_number'];
                $document_type=$result['document_type'];
                $distributor_id = $result['distributor_id'];
                //echo $invoice_number.",";
                $filename_csv=$result['filename_csv'];
                $filename_pdf=$result['filename_pdf'];


                $file_path_date_csv = '../public/files/finance/etax/'.substr($result['document_date'],0,7).'/'.substr($result['document_date'],8,2);

                $file_path_date_pdf = '../public/files/finance/etax/'.substr($result['document_date'],0,7).'/'.substr($result['document_date'],8,2);
                
                $csv_full_path = $file_path_date_csv.'/'.$document_type.'/csv/'.$filename_csv;
                $pdf_full_path = $file_path_date_pdf.'/'.$document_type.'/pdf/'.$filename_pdf;

                $target_url="https://uatetaxsp.one.th/etaxdocumentws/etaxsigndocument";
                $post = array('SellerTaxId' => $SellerTaxId
                    ,'SellerBranchId'=> $SellerBranchId
                    ,'APIKey'=> $APIKey
                    ,'UserCode'=> $UserCode
                    ,'AccessKey'=> $AccessKey
                    ,'ServiceCode'=> $ServiceCode
                    ,'TextContent' => curl_file_create(realpath($csv_full_path))
                    ,'PDFContent' => curl_file_create(realpath($pdf_full_path))
                );
                //print_r($post);die;
                    $headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
                    $ch = curl_init();
                    $options = array(
                        CURLOPT_URL => $target_url,
                        CURLOPT_HEADER => false,
                        CURLOPT_POST => true,
                        CURLOPT_HTTPHEADER => $headers,
                        CURLOPT_POSTFIELDS => $post,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_SSL_VERIFYPEER => false
                    ); // cURL options
                    curl_setopt_array($ch, $options);
                    $postResult = curl_exec($ch);
                    $res = json_decode($postResult);
                    //print_r($res);die;

                    if($res->status=="OK")
                    {
                        //------------ Update -----------------
                        try {

                            $download_full_path = $file_path_date_csv.'/'.$document_type.'/download/';

                            if (!file_exists($download_full_path)){
                                mkdir($download_full_path, 0777, true);
                            }

                            $newFileName_CSV = $invoice_number.uniqid().".csv";
                            $path_csv = $download_full_path.$newFileName_CSV;
                            $res_csv = $this->getFile($res->xmlURL, $path_csv);

                            $newFileName_PDF = $invoice_number.uniqid().".pdf";
                            $path_pdf = $download_full_path.$newFileName_PDF;
                            $res_pdf = $this->getFile($res->pdfURL, $path_pdf);


                            $whereEtax      = $db->quoteInto('document_number = ?',$invoice_number);
                            $dataEtax   = array('transaction_code'=>$res->transactionCode
                                ,'service_code'=>$ServiceCode
                                ,'upload_csv_status_code'=>$res->status
                                ,'upload_csv_date'=>$date
                                ,'upload_csv_by'=>$userStorage->id
                                ,'upload_pdf_status_code'=>$res->status
                                ,'upload_pdf_date'=>$date
                                ,'upload_pdf_by'=>$userStorage->id
                            );

                            if($res_csv=="1"){
                               $dataEtax['download_filename_csv']=$newFileName_CSV; 
                            }

                            if($res_pdf=="1"){
                               $dataEtax['download_filename_pdf']=$newFileName_PDF; 
                            }

                            if($res_csv=="1" && $res_pdf=="1"){
                                $dataEtax['document_status']=1; 
                            }else{
                                $dataEtax['document_status']=2; 
                            }

                            $rs=$QEtax->update($dataEtax,$whereEtax);
                            if($rs=="1"){
                                echo " Done: ".$invoice_number;
                            }
                            //print_r($rs);
                        } catch (Exception $e) {
                            print_r($e->getMessage());
                            return;
                        }
                    }
                    else
                    {
                        //print_r($res);die;
                        //------------ Update Error-----------------
                        try {
                            $whereEtax      = $db->quoteInto('document_number = ?',$invoice_number);
                            $dataEtax   = array('transaction_code'=>$res->transactionCode
                                ,'service_code'=>$ServiceCode
                                ,'distributor_id'=>$distributor_id
                                ,'upload_csv_status_code'=>$res->status
                                ,'upload_csv_date'=>$date
                                ,'upload_csv_by'=>$userStorage->id
                                ,'upload_pdf_status_code'=>$res->status
                                ,'upload_pdf_date'=>$date
                                ,'upload_pdf_by'=>$userStorage->id
                                ,'document_status'=>2
                            );
                            $rs=$QEtax->update($dataEtax,$whereEtax);
                            if($rs=="1"){
                                echo " Error : ".$invoice_number;
                            }

                            $dataEtaxLog   = array('document_number'=>$invoice_number
                            ,'document_type'=>$document_type
                            ,'distributor_id'=>$distributor_id
                            ,'transaction_code'=>$res->transactionCode
                            ,'errorCode'=>$res->errorCode
                            ,'errorMessage'=>$res->errorMessage
                            ,'create_date'=>$date);

                            $rs_log=$QEtaxErrorLog->insert($dataEtaxLog);

                            //print_r($rs);
                        } catch (Exception $e) {
                            print_r($e->getMessage());
                            return;
                        }


                        //print_r($res);
                    }
                    curl_close($ch);
                $db->commit(); 
            }catch (exception $e) {
                $db->rollback();
                print_r($e->getMessage());
                echo "Error";
            }
        }
    }

    //--------------------End Send To eTax API---------------------------//

    function encode($string,$key)
    {
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

    function random_number()
    {
        $min=1000;
        $max=9999;
        return rand($min,$max);
    }

    // --------------- Query ---------------- //

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.document_number'), 'p.*'));

        $select->joinLeft(array('staff' => 'staff'), 'staff.id = p.create_by', array('created_name' => 'concat(staff.firstname," ",staff.lastname)'));

        if (isset($params['document_number']) and $params['document_number'])
            $select->where('p.document_number LIKE ?', '%'.$params['document_number'].'%');

        if (isset($params['document_type']) and $params['document_type'])
            $select->where('p.document_type = ?', $params['document_type']);

        if (isset($params['doc_created_at_from']) and $params['doc_created_at_from']){
                list( $day, $month, $year ) = explode('/', $params['doc_created_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.document_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['doc_created_at_to']) and $params['doc_created_at_to']){
            list( $day, $month, $year ) = explode('/', $params['doc_created_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.document_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if (isset($params['created_at_from']) and $params['created_at_from']){
                list( $day, $month, $year ) = explode('/', $params['created_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.create_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['created_at_to']) and $params['created_at_to']){
            list( $day, $month, $year ) = explode('/', $params['created_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.create_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);

        if ($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");

        return $result;
    }

}                                                      
