<?php
$sn = $this->getRequest()->getParam('sn');
$inv_sn = $this->getRequest()->getParam('inv_sn');
$this->_helper->layout->disableLayout();
if ($sn) {


    $QMarket = new Application_Model_Market();
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $sales = $QMarket->fetchAll($where);



    foreach ($sales as $k => $v) {
        if (isset($v['service']))
            $service = $v['service'];
        if (isset($v['office']))
            $office = $v['office'];
        if (isset($v['warehouse_nvmm'])){
            $warehouse_nvmm = $v['warehouse_nvmm'];
        }
    }

    $data = array();

    $QGoodCategory = new Application_Model_GoodCategory();
    $categories = $QGoodCategory->get_cache();

    $QService = new Application_Model_Service();
    $QOffice = new Application_Model_Office();
    
    $QGood = new Application_Model_Good();
    $goods = $QGood->get_cache();
    $goods_desc = $QGood->get_name();

    $QGoodColor = new Application_Model_GoodColor();
    $goodColors = $QGoodColor->get_cache();

    $QStaff = new Application_Model_Staff();
    $staffs = $QStaff->get_cache();

    $QDistributor = new Application_Model_Distributor();
    $distributors = $QDistributor->get_cache();

    $QWarehouse = new Application_Model_Warehouse();
    $warehouses = $QWarehouse->get_cache();

    $BVG = $QWarehouse->fetchSummaryBVGByInv($sn);
    $this->view->BVG = $BVG;

    $Credit_Note = $QMarket->fetchCredit_Note($sn);

    $QTag        = new Application_Model_Tag();
    $Tag = $QTag->fetch_Tag($sn);   

    foreach ($sales as $k => $sale) {

        //get warehouse
        $data[$k]['warehouse_name'] = isset($warehouses[$sale->warehouse_id]) ? $warehouses[$sale->warehouse_id] : '';
        //$data[$k]['retailer_id'] = isset($warehouses[$sale->d_id]) ? $warehouses[$sale->d_id] : '';
        $add_time = isset($sale->add_time) ? $sale->add_time : '';

        //get retailer
        $data[$k]['retailer_name'] = isset($distributors[$sale->user_id]) ? $distributors[$sale->user_id] : '';
        //get retailer
        $data[$k]['distributor_name'] = isset($sale->d_id) ? $sale->d_id : '';

        //get created_by_name
        $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->user_id] : '';

        //get sales man
        $data[$k]['salesman_name'] = isset($staffs[$sale->salesman]) ? $staffs[$sale->salesman] : '';

        //get sales man confirm
        $data[$k]['salesman_confirm'] = isset($staffs[$sale->sales_confirm_id]) ? $staffs[$sale->sales_confirm_id] : '';  

        $data[$k]['sales_confirm_date']= isset($sale->sales_confirm_date) ? $sale->sales_confirm_date : '';  

        // Finance Confirm
        $data[$k]['finance_confirm'] = isset($staffs[$sale->finance_confirm_id]) ? $staffs[$sale->finance_confirm_id] : '';   

        $data[$k]['finance_confirm_date']= isset($sale->finance_confirm_date) ? $sale->finance_confirm_date : '';     

        //get category
        $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->cat_id] : '';

        //get good
        $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';
        //get cat id
        $data[$k]['desc'] = isset($goods_desc[$sale->good_id]) ? $goods_desc[$sale->good_id] : '';
        $data[$k]['ship_address'] = isset($sale->shipping_address) ? $sale->shipping_address : '';
        $data[$k]['customer_name'] = isset($sale->customer_name) ? $sale->customer_name : '';


        $data[$k]['user_id'] = isset($sale->user_id) ? $sale->user_id : '';
        $data[$k]['sale_id'] = isset($sale->salesman) ? $sale->salesman : '';
        // $data[$k]['add_time'] = isset($sale->add_time)?$sale->add_time : '';
        //get goods color
        $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->good_color] : '';

        $data[$k]['sale'] = $sale;

       // $data[$k]['credit_note_list'] = $Credit_Note[0];

        $data[$k]['total_discount'] = $Credit_Note[0]['total_discount'];

        $data[$k]['credit_note_list'] = $Credit_Note;
        $data[$k]['Tag'] = $Tag[0];
        $data[$k]['customer'] = isset($sale->customer_id) ? $sale->customer_id : '';
        $CustomerBrand= array();
        if (isset($sale->customer_id) and $sale->customer_id) {
           $QCustomerBrandShop = new Application_Model_CustomerBrandShop();
            $where = $QCustomerBrandShop->getAdapter()->quoteInto('customer_id = ?', $sale->customer_id);
            $CustomerBrand = $QCustomerBrandShop->fetchRow($where);
            $address_tax = $CustomerBrand['address_tax'];
        }
        $data[$k]['customer_adddress'] = isset($address_tax) ? $address_tax : '';
        //$data[$k]['total_discount'] = $Credit_Note[0]['total_discount'];
    }

    //print_r($data);
    $QDistributor1 = new Application_Model_Distributor();
    if ($data[0]['distributor_name']) {
        $distributor = $QDistributor1->find($data[0]['distributor_name']);
        $distributor = $distributor->current();
    }

    
        

    if ($distributor) {
        $this->view->distributor = $distributor;
    }
    // $this->view->addtime = $
    $this->view->sn = $sn;
    if (isset($data[0]['sale_id'])) {
        $staff = $QStaff->find($data[0]['sale_id']);
        $staff = $staff->current();
        if ($staff) {
            $this->view->staff = $staff;
        }

        //get sales man Catty
        if($sale->sales_catty_id !=''){
            $staffs_catty = $QStaff->getSalesCattyByStore($sale->d_id,$sale->sales_catty_id);
        }

        $salescatty_name ="";
        if (isset($staffs_catty)) {
            $salescatty_name =$staffs_catty[0]['fullname'];
        }
        
        if($sale->sales_catty_id !='' and $salescatty_name==''){
            $staffs_catty = $QStaff->getCattyStaffByID($sale->sales_catty_id);
            $salescatty_name =$staffs_catty[0]['fullname'];
        }
        //$this->view->salescatty_name = isset($staffs_catty) ? $staffs_catty[0]['fullname'] : ''; 

        $this->view->salescatty_name = $salescatty_name; 
        $this->view->salescatty_phone_number = isset($staffs_catty) ? $staffs_catty[0]['phone_number'] : ''; 



    }



    if (isset($data[0]['user_id'])) {
        $oStaff = $QStaff->find($data[0]['user_id']);
        $oStaff = $oStaff->current();
        if ($oStaff) {
            $this->view->oStaff = $oStaff;
        }
    }
    $this->view->add_time = $add_time;
    $this->view->sales = $data;
    $this->view->market = $sales;
    $distributor = $data[0]['sale']['d_id'];
    $sn_01 = explode('-', $data[0]['sale']['sn_ref']);
    $sn_02 = substr($sn_01[0].$sn_01[1], 2, 11);
    $sn_ref = str_pad($sn_02, 16, "0", STR_PAD_LEFT); 
    
    
    $this->view->barcode =  '|'.TAX_OPPO.'01%0D'.$sn_ref.'%0D'.$distributor;
    $this->view->barcode_show =  TAX_OPPO.' '.$sn_ref.' '.$distributor;
    $this->view->ref1 = $sn_ref;
    $this->view->ref2 = $distributor;
    // print_r($data);
    // print_r($sales);
    if (isset($service) and $service) {
        $QService = new Application_Model_Service();
        $services = $QService->get_cache_service();
        $this->view->services = $services[$service];
    }else{

        $customer_id = $sales[0]['customer_id'];
        if($customer_id !=''){
            $CustomerBrandShop = $QMarket->getCustomerBrandShop($customer_id);
            $this->view->CustomerBrandShop = $CustomerBrandShop[0];   
        }
        
    }
   
    if (isset($office) and $office) {
        $officeRowSet = $QOffice->find($office);
        $offices = $officeRowSet->current();
        $this->view->offices = $offices;
        $this->view->service = $office;       
    }

    if(isset($warehouse_nvmm) AND $warehouse_nvmm){
        $QWarehouse = new Application_Model_Warehouse();
        $warehouse_nvmm_row = $QWarehouse->find($warehouse_nvmm)->current();
        $this->view->warehouse_nvmm_row = $warehouse_nvmm_row;
    }
    //$invoice_no='12345';
    if($inv_sn ==''){
        $this->view->inv_sn = $invoice_no; 
    }else{
        $this->view->inv_sn = $inv_sn; 
    }

    $Credit_Note = $QMarket->fetchCredit_Note($sn);

    if(isset($Credit_Note[0]['total_discount'])){
        $this->view->total_discount = $Credit_Note[0]['total_discount']; 
    }
      
    My_Image_Barcode::renderNoCode($sn);




}
