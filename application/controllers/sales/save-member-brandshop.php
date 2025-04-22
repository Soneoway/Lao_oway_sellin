<?php

$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('member_brandshop_id');

$code = $this->getRequest()->getParam('code');
$customer_name = $this->getRequest()->getParam('customer_name');
$phone_number = $this->getRequest()->getParam('phone_number');
$tax_number = $this->getRequest()->getParam('tax_number');
$tax_address = $this->getRequest()->getParam('tax_address');

$province_id = $this->getRequest()->getParam('province_id');
$amphures_id = $this->getRequest()->getParam('amphures_id');
$districts_id = $this->getRequest()->getParam('districts_id');
$zipcodes_id = $this->getRequest()->getParam('zipcodes_id');
$zipcodes = $this->getRequest()->getParam('zipcodes');

$status = $this->getRequest()->getParam('status',0);

$db = Zend_Registry::get('db');
$db->beginTransaction();

$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender(true);


try {

    if ($this->getRequest()->getMethod() == 'POST'){

        $userStorage  = Zend_Auth::getInstance()->getStorage()->read();

        $QMB = new Application_Model_MemberBrandshop();

        $QSA = new Application_Model_ShippingAmphures();
        $QSD = new Application_Model_ShippingDistricts();
        $QSP = new Application_Model_ShippingProvinces();
        $QSZ = new Application_Model_ShippingZipcodes();

        $getDistricts = trim($QSD->get_data($districts_id));
        $getAmphures = trim($QSA->get_data($amphures_id));
        $getProcinces = trim($QSP->get_data($province_id));
        $getZipcode = trim($QSZ->get_data($zipcodes_id));

        $address_full = ' ต.' . $getDistricts . ' อ.' . $getAmphures . ' จ.' . $getProcinces . ' ' . $getZipcode;

        if($id){

            $arrData = array(
                'code' => $code,
                'customer_name' => $customer_name,
                'phone_number' => $phone_number,
                'tax_number' => $tax_number,
                'tax_address' => $tax_address,
                'province_id' => $province_id,
                'amphures_id' => $amphures_id,
                'districts_id' => $districts_id,
                'zipcodes_id' => $zipcodes_id,
                'zipcodes' => $zipcodes,
                'show_address' => $address_full,
                'modified_date' => date('Y-m-d H:i:s'),
                'modified_by' => $userStorage->id,
                'status' => $status
            );

            $where = $QMB->getAdapter()->quoteInto('id = ?', $id);

            $procrss = $QMB->update($arrData, $where);


        }else{

            $where = $QMB->getAdapter()->quoteInto('code = ?', $code);
            $getMemberBrandshop = $QMB->fetchRow($where);

            if($getMemberBrandshop){
                echo json_encode(['status' => 400,'message' => 'Duplicate Code!']);
                exit();
            }

            $arrData = array(
                'code' => $code,
                'customer_name' => $customer_name,
                'phone_number' => $phone_number,
                'tax_number' => $tax_number,
                'tax_address' => $tax_address,
                'province_id' => $province_id,
                'amphures_id' => $amphures_id,
                'districts_id' => $districts_id,
                'zipcodes_id' => $zipcodes_id,
                'zipcodes' => $zipcodes,
                'show_address' => $address_full,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $userStorage->id,
                'status' => 1
            );

            $procrss = $QMB->insert($arrData);

        }

        if($procrss){

            $QCustomerBrandShop   = new Application_Model_CustomerBrandShop();

            $CustomerBrandShop = $QCustomerBrandShop->chkCustomerBrandshop($customer_name,$tax_number,$code);

            $customer_id = '';

            $tax_address = $tax_address . $address_full;

            if($CustomerBrandShop){
                $customer_id = $CustomerBrandShop['customer_id'];
            }

            $data_customer = array();
            $data_customer['customer_name'] = $customer_name;
            $data_customer['phone_number'] = $phone_number;
            $data_customer['tax_number'] = $tax_number;
            $data_customer['address_tax'] = $tax_address;
            $key_sn = date('YmdHis') . substr(microtime(), 2, 4);

            $data_customer['member_brandshop_code'] = $code;

            if($customer_id != ''){ //update
                $data_customer['update_date']   = date('Y-m-d H:i:s');
                $data_customer['update_by']  = $userStorage->id;
                $data_customer['key_sn']   = $key_sn;
                $where = $QCustomerBrandShop->getAdapter()->quoteInto('customer_id = ?', $customer_id);
                $QCustomerBrandShop->update($data_customer, $where);
            }else{ //insert
                
                $data_customer['status'] = 1;
                $data_customer['create_date']   = date('Y-m-d H:i:s');
                $data_customer['create_by']  = $userStorage->id;
                $data_customer['key_sn']   = $key_sn;
                // print_r($data_customer);die;
                $QCustomerBrandShop->insert($data_customer);
            }

        }

        $db->commit();

        $flashMessenger->setNamespace('success')->addMessage('Done.');

        echo json_encode(['status' => 200,'url' => HOST.'sales/member-brandshop']);
        exit();
    }

    $flashMessenger->setNamespace('error')->addMessage('Invalid Data!');
    echo json_encode(['status' => 500]);
    exit();
    
} catch (Exception $e) {
    $db->rollback();
    // $this->view->error = $e->getMessage();

    echo json_encode(['status' => 400,'message' => $e->getMessage()]);
    exit();

}
