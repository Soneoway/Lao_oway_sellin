<?php
$flashMessenger = $this->_helper->flashMessenger;

//print_r($_POST);die;
try {
    $db = Zend_Registry::get('db');
    $db->beginTransaction();
    $this->_helper->layout->disableLayout();

    if ($this->getRequest()->getMethod() != 'POST')
        throw new Exception("Wrong Action");
    
    $id    = $this->getRequest()->getParam('id');
    $title = $this->getRequest()->getParam('title', '');

    if ($title == '') throw new Exception('Invalid Retailer Name');

    $name                       = $this->getRequest()->getParam('owner_name');
    $finance_code               = $this->getRequest()->getParam('finance_code');
    $tel                        = $this->getRequest()->getParam('tel');
    $email                      = $this->getRequest()->getParam('email');
    $region                     = $this->getRequest()->getParam('region', 0);
    $district                   = $this->getRequest()->getParam('district', 0);
    $warehouse_id               = $this->getRequest()->getParam('warehouse_id');
    $add                        = $this->getRequest()->getParam('add_tax');
    $add_tax                    = $this->getRequest()->getParam('add_tax');
    $admin                      = $this->getRequest()->getParam('admin', 0);
    $rank                       = $this->getRequest()->getParam('rank', null);
    $unames                     = $this->getRequest()->getParam('unames');
    $mst_sn                     = '0000000000000';
    $store_code                 = $this->getRequest()->getParam('store_code');
    $true_code                  = $this->getRequest()->getParam('true_code');
    $credit_amount              = $this->getRequest()->getParam('credit_amount',0);
    $credit_type                = '1';
    $nots                       = $this->getRequest()->getParam('nots');
    $retailer_type              = $this->getRequest()->getParam('retailer_type');
    $partner_id                 = $this->getRequest()->getParam('partner_id');
    $is_ka                      = $this->getRequest()->getParam('is_ka', 0);
    $branch_amout               = $this->getRequest()->getParam('branch_amout',null);
    $is_internal                = $this->getRequest()->getParam('is_internal', 0);
    $parent                     = $this->getRequest()->getParam('parent', 0);
    $check_use_money_main       = $this->getRequest()->getParam('check_use_money_main', null);
    $branch_no                  = '00000';
    $sales_ch                   = $this->getRequest()->getParam('sales_ch', null);
    $credit_status              = $this->getRequest()->getParam('credit_status', null);
    $spc_discount               = $this->getRequest()->getParam('spc_discount', 0);
    $spc_discount_phone         = $this->getRequest()->getParam('spc_discount_phone', 0);
    $spc_discount_acc           = $this->getRequest()->getParam('spc_discount_acc', 0);
    $spc_discount_digital       = $this->getRequest()->getParam('spc_discount_digital', 0);
    $money                      = $this->getRequest()->getParam('money',null); //Distributor not money invoice
    $contract_name              = $this->getRequest()->getParam('contract_name', null);
    $data_address               = $this->getRequest()->getParam('add_tax', null);
    $ship_province              = $this->getRequest()->getParam('ship_province', null);
    $amphures                   = $this->getRequest()->getParam('amphures', null);
    $districts_sipping          = $this->getRequest()->getParam('districts_sipping', null);
    $zip_id                     = $this->getRequest()->getParam('zip_id', null);
    $contract_phone             = $this->getRequest()->getParam('contract_phone', null);
    $remark                     = $this->getRequest()->getParam('remark', null);
    $owner_name                 = $this->getRequest()->getParam('owner_name', null);
    $auto_confirm_finance       = $this->getRequest()->getParam('auto_confirm_finance', 0);
    $finance_group              = $this->getRequest()->getParam('finance_group', null);
    $ka_type                    = $this->getRequest()->getParam('ka_type', 0);
    $is_kr                      = $this->getRequest()->getParam('is_kr', 0);
    $quota_channel              = $this->getRequest()->getParam('quota_channel');
    $check_sub_d_id             = $this->getRequest()->getParam('check_sub_d_id', null);
    $agent_status               = $this->getRequest()->getParam('agent_status', 0);
    $agent_warehouse_id         = $this->getRequest()->getParam('agent_warehouse_id', null);
    $distributor_group          = $this->getRequest()->getParam('distributor_group', null);

    // Client Management //
    $client_code                = $this->getRequest()->getParam('client');
    $dis_type                   = $this->getRequest()->getParam('dis_type');

    if($quota_channel == ''){
        $quota_channel = null;
    }

    if($distributor_group == ''){
        $distributor_group = null;
    }

    if($rank==1 || $rank==2 || $rank==5 || $rank==9 || $rank==11)
    {
        $pefix="OP";
    }else if($rank==7 || $rank==8)

    {
    $pefix="OP";

    if($quota_channel==3){
        $pefix="JD";
    }
    }else if($rank==3)
    {
        $pefix=$sales_ch;
    }else if($rank==10)
    {
        $pefix=$sales_ch;
    }else{
        $pefix="OP";
    }

    $QDistributor = new Application_Model_Distributor();
    $QClientCode = new Application_Model_ClientCode();
    $userStorage  = Zend_Auth::getInstance()->getStorage()->read();

    if(!$id){

        if($rank == '10' && ($distributor_group == '9' || $distributor_group == '10')){

            $QDG = new Application_Model_DistributorGroup();

            $arrData = array(
                'group_type_id' => $distributor_group,
                'group_name' => $title,
                'status' => 1,
                'create_date' => date('Y-m-d H:i:s'),
                'create_by' => $userStorage->id,
                'update_date' => date('Y-m-d H:i:s'),
                'update_by' => $userStorage->id
            );

            $distributor_group = $QDG->insert($arrData);
        }

    }

    $cusCode        = $QClientCode->find(2);
    $insertCode     = $cusCode[0]['next_code'];


    $data = array(
        'title'                 => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $title)),
        'name'                  => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $name)),
        'finance_code'          => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $finance_code)),
        'tel'                   => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $tel)),
        'email'                 => trim($email),
        'warehouse_id'          => intval($warehouse_id),
        'region'                => intval($region),
        'district'              => intval($district),
        'add'                   => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $add)),
        'add_tax'               => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $add_tax)),
        'admin'                 => $admin,
        'rank'                  => intval($rank),
        'unames'                => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $unames)),
        'mst_sn'                => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $mst_sn)),
        'nots'                  => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $nots)),
        //'store_code'          => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $store_code)),
        'credit_amount'         => $credit_amount,
        'credit_type'           => intval($credit_type),
        'parent'                => intval($parent),
        'partner_id'            => $partner_id,
        'retailer_type'         => intval($retailer_type),
        'is_ka'                 => intval($is_ka),
        'branch_amout'          => intval($branch_amout),
        'is_internal'           => intval($is_internal),
        'credit_status'         => intval($credit_status),
        'spc_discount'          => intval($spc_discount),
        'spc_discount_phone'    => intval($spc_discount_phone),
        'spc_discount_acc'      => intval($spc_discount_acc),
        'spc_discount_digital'  => intval($spc_discount_digital),
        'auto_confirm_finance'  => intval($auto_confirm_finance),
        'finance_group'         => $finance_group,
        'ka_type'               => intval($ka_type),
        'is_kr'                 => intval($is_kr),
        'quota_channel'         => $quota_channel,
        'group_id'              => $distributor_group,
        'owner_name'            => $owner_name,
        'not_money'             => $money, //distributor no money
        'client_code'           => $client_code,
        'distributor_type'      =>$dis_type
    );

    if($check_use_money_main && $parent > 0){
        $data['main_distributor_id'] = intval($parent);
    }else{
        $data['main_distributor_id'] = null;
    }

    if($check_sub_d_id =='1'){
        $data['sub_d_id'] = '1';
    }else{
        $data['sub_d_id'] = null;
    }

    if($agent_status =='1'){
        $data['agent_status'] = '1';
        $data['agent_warehouse_id'] = $agent_warehouse_id;
        
    }else{
        $data['agent_status'] = '0';
        $data['agent_warehouse_id'] = null;
    }

    // ---------------check exists title or store_code-----------------------
    // check exists title or store_code
    extract($data);
    $where = array();
    if ($id)
        $where[] = $QDistributor->getAdapter()->quoteInto('id <> ?', $id);

    $where[] = $QDistributor->getAdapter()->quoteInto('title = ?', $title);
    $where[] = $QDistributor->getAdapter()->quoteInto('store_code = ?', $store_code);
    $where[] = $QDistributor->getAdapter()->quoteInto('del IS NULL OR del = 0', 1);
    $checkedTitle = $QDistributor->fetchRow($where);

    //print_r($data);die;
    if ($checkedTitle)
        throw new Exception('Retailer Name is existed. <a href="'.HOST.'sales/distributor?id='.$checkedTitle['id'].'" targer="_blank">[View...]</a>');

    // check store_code
    // $where = array();
    // if ($id)
    //     $where[] = $QDistributor->getAdapter()->quoteInto('id <> ?', $id);

    // if($store_code !=''){
    //     $where[] = $QDistributor->getAdapter()->quoteInto('store_code = ?', $store_code);
    //     $where[] = $QDistributor->getAdapter()->quoteInto('del <> ?', 1);
    //     $checkedStoreCode = $QDistributor->fetchRow($where);
    //     if ($checkedStoreCode)
    //         throw new Exception('Store Code is existed');

    // }

    // ---------------End of: check exists title or store_code-----------------------

    if ($partner_id) {
        $partner_id = trim($partner_id);
        $where = array();
        $where[] = $QDistributor->getAdapter()->quoteInto('partner_id = ?', $partner_id);

        if ($id)
            $where[] = $QDistributor->getAdapter()->quoteInto('id <> ?', intval($id));
        if ($parent)
            $where[] = $QDistributor->getAdapter()->quoteInto('parent = ?', intval($parent));

        $distributor_check = $QDistributor->fetchRow($where);

        if ($distributor_check) throw new Exception('Partner ID is existed');

        $QDistributorMapping = new Application_Model_DistributorMapping();

        $where = array();
        $where[] = $QDistributorMapping->getAdapter()->quoteInto('distributor_id <> ?', $id);
        $where[] = $QDistributorMapping->getAdapter()->quoteInto('code LIKE ?', $partner_id);

        $mapping = $QDistributorMapping->fetchRow($where);

        if ($mapping) throw new Exception("Code exists", 6);

        $data_map = array(
            'distributor_id' => $id,
            'code' => $partner_id,
        );

        try {
            $QDistributorMapping->insert($data_map);
        } catch (Exception $e) {
            $where = $QDistributorMapping->getAdapter()->quoteInto('distributor_id = ?', $id);
            $QDistributorMapping->update($data_map, $where);
        }
    }

    $select = "SELECT mask(".$branch_no.",'#####') as branch_no";
    $branch =$db->fetchAll($select);
    $data['branch_no'] = $branch[0]['branch_no'];
    
    if ($id) { // save

        $status_create = 0;

        $data_address =array(

            // 'location_name'  =>  trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $title)),
            'contact_name'  =>  trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $title)),
            'address'       =>  $data_address,
            'province_id'   =>  $ship_province,
            'amphures_id'   =>  $amphures,
            'districts_id'  =>  $districts_sipping,
            'zipcodes'      =>  $zip_id,
            'phone'         =>  $contract_phone,
            'd_id'          =>  $id,
            'remark'        =>  $remark

        );

        $ShippingAddress = new Application_Model_ShippingAddress();
        $where_dis = array();
        $where_dis[] = $QDistributor->getAdapter()->quoteInto('id = ?', $id);
        $where_dis[] = $QDistributor->getAdapter()->quoteInto('shipping_add_default IS NOT NULL',null);
        //print_r($id);die;
        $check_ship = $QDistributor->fetchRow($where_dis);
        // print_r($data);die;

        if (isset($check_ship) and $check_ship) {


            $data_address['updated_at'] = date('Y-m-d H:i:s');
            $data_address['updated_by'] = $userStorage->id;
            $where = $ShippingAddress->getAdapter()->quoteInto('id = ?', $check_ship['shipping_add_default']);
            $ship_id = $ShippingAddress->update($data_address,$where);
        }else{

            $data_address['created_at'] = date('Y-m-d H:i:s');
            $data_address['created_by'] = $userStorage->id;

            $in_id = $ShippingAddress->insert($data_address);


        }

        if($store_code==''){
            for($i=0;$i<3;$i++){ 
                if($store_code==''){
                    $store_code = $QDistributor->getDistributorCode($db,$pefix);
                }
            }
        }
        
       // die;
        $update_time = date('Y-m-d H:i:s');
        $data['update_by'] = $userStorage->id;
        $data['update_date'] = $update_time;
        if ($in_id) {
            $data['shipping_add_default'] = $in_id;    
        }

        
        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $id);
        $QDistributor->update($data, $where);

    } else { // create new

        // print_r($data);die;
        $status_create = 1;

        $data_address =array(

            // 'location_name'  =>  trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $title)),
            'contact_name'  =>  trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $title)),
            'address'       =>  $data_address,
            'province_id'   =>  $ship_province,
            'amphures_id'   =>  $amphures,
            'districts_id'  =>  $districts_sipping,
            'zipcodes'      =>  $zip_id,
            'phone'         =>  $contract_phone,
            'remark'        =>  $remark,
            'created_by'    =>  $userStorage->id,
            'created_at'    =>  date('Y-m-d H:i:s')
        );
        $ShippingAddress = new Application_Model_ShippingAddress();

        $ship_id = $ShippingAddress->insert($data_address);
        


        $add_time = date('Y-m-d H:i:s');
        $data['add_time'] = $add_time;
        $data['shipping_add_default'] = $ship_id;
        $data['create_by'] = $userStorage->id;
        $data['create_date'] = $add_time;
        

        $store_code = $QDistributor->getDistributorCode($db,$pefix);
        if($store_code==''){
            for($i=0;$i<3;$i++){ 
                if($store_code==''){
                    $store_code = $QDistributor->getDistributorCode($db,$pefix);
                }
            }
        }

        $data['store_code'] = $store_code;
        $data['sales_ch'] = $sales_ch;
        $update_time = date('Y-m-d H:i:s');
        $data['update_by'] = $userStorage->id;
        $data['update_date'] = $update_time;

        //Allow Activate Online (5),Staff (6),Service Shop (9),Brand Shop (10)
        if(in_array($distributor_group, [5,6,9,10,1])){
            $data['activate'] = 1;
        }

        if($store_code!=""){

            $data['distributor_code'] = $insertCode;

            $id = $QDistributor->insert($data);

            if($id) {
                $next_code = $insertCode + 1; 

                $data_code = array(
                    'last_code'     => $insertCode,
                    'next_code'     => $next_code,
                    'updated_at'    => date('Y-m-d H:i:s')
                );

                $where = $QClientCode->getAdapter()->quoteInto('id = ?', 2);
                $QClientCode->update($data_code,$where);
            }

            $shippind_update['d_id'] = $id;
            $where_ship = $ShippingAddress->getAdapter()->quoteInto('id = ?', $ship_id);

            $ShippingAddress->update($shippind_update,$where_ship);
        }else{
            $back_url = $this->getRequest()->getParam('back_url');
            $this->view->result = 'Error';
            $this->view->error = $e->getMessage();
            $db->rollback();
            $this->view->back_url = $back_url;
        }

    }

    if($true_code){

        $QEDC = new Application_Model_ExternalDistributorCode();
        $where_truemove = $QEDC->getAdapter()->quoteInto('partner_code = ?', $true_code);
        $get_true_code = $QEDC->fetchAll($where_truemove);

        if(count($get_true_code)==0){
            //create true code when create distributor
            if($status_create == 1){
                $true_date = array(
                    'oppo_distributor_id' => $id,
                    'partner_name' => 'truemove',
                    'partner_code' => $true_code,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                );
                $QEDC->insert($true_date);
            }
            //create true code when update distributor
            if($status_create == 0){
                $QEDC = new Application_Model_ExternalDistributorCode();
                $true_date = array(
                    'oppo_distributor_id' => $id,
                    'partner_name' => 'truemove',
                    'partner_code' => $true_code,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                );
                $QEDC->insert($true_date);
            }

        }
    }

    if ($id) {
        if($store_code !=''){
            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $db->commit(); 
        }else{
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage('Cannot Create Distributor Code, Please try again!');
        }
    }else{
        $flashMessenger->setNamespace('success')->addMessage('Done!');
        $db->commit(); 
    }

    //remove cache
    
    $cache = Zend_Registry::get('cache');
    $cache->remove('distributor_cache');
    $cache->remove('distributor_2_cache');
    $cache->remove('distributor_with_store_code_cache');
    $cache->remove('distributor_all_cache');
    $cache->remove('distributor_warehouse_cache');
    $cache->remove('distributor_by_area_cache');
    

    $back_url = $this->getRequest()->getParam('back_url');
    $this->view->result = 'Success';
    $this->view->back_url = $back_url;
    //$this->view->back_url = "/sales/distributor";
} catch (Exception $e) {
    $db->rollback();
    $this->view->error = $e->getMessage();
}
