<!--<?php
    //echo "OK";
    $this->_helper->layout->disableLayout();
    
    $this->_helper->viewRenderer->setRender('cn-view-print');

    $QImei = new Application_Model_Imei();

    $QDistributor = new Application_Model_Distributor();
    $distributors = $QDistributor->get_cache();
    
 /*   if(isset($_SESSION["imei_list"]))
    {
        $imei_list = $_SESSION["imei_list"];    
        $invoice_list = $QImei->fetchSummaryImeiByInv($imei_list);

        //Get Retailer
        $distributor_id=$invoice_list[0]["distributor_id"];
        $invoice_list['retailer_name'] = isset($distributors[$distributor_id]) ? $distributors[$distributor_id] : '-';
    }
   */
    //print_r($distributors);
    //count($invoice_list); 
    $this->view->invoice_list= $invoice_list;
    $this->view->invoice_imei_list= $imei_list;

    //print_r($_SESSION['imei_list']);

$sn=$this->getRequest()->getParam('sn');
$creditnote_type=$this->getRequest()->getParam('creditnote_type');
$distributor_id=$this->getRequest()->getParam('d_id');
$chanel=$this->getRequest()->getParam('chanel');

/* if ($sn) {
    
    $QCreditNote = new Application_Model_CreditNote();
    if($creditnote_type=='CP'){
        $CreditNoteData = $QCreditNote->getCredit_Note_Protection_Price_View($distributor_id,$sn);
        $ImeiReturn = $QCreditNote->getCredit_Note_Protection_Price_View_List_imei($distributor_id,$sn);
    }else{
        if($chanel=='reward'){
            $CreditNoteData = $QCreditNote->getCredit_Note_Reward_View($distributor_id,$sn);
            $ImeiReturn = $QCreditNote->getCredit_Note_Reward_List_imei($distributor_id,$sn);
        }else{
            $CreditNoteData = $QCreditNote->getCredit_Note_View($distributor_id,$sn);
            $ImeiReturn = $QCreditNote->getCredit_Note_list_Imei($distributor_id,$sn);  
        }
    }
    */
    //print_r($CreditNoteData);
    //print_r($ImeiReturn);
    $this->view->sales = $CreditNoteData;
    $this->view->imei_return_list = $ImeiReturn;
    $this->view->creditnote_type = $creditnote_type;
    $this->view->distributor_id = $distributor_id;
    
    
    //$this->view->ImeiReturn = $ImeiReturn;
    
    //$QImeiReturn = new Application_Model_ImeiReturn();

    //$where = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
    //$this->view->imei_return_list = $QImeiReturn->fetchAll($where);

    //print_r($ImeiReturn_List);
//}

?>
-->
<?php

$sn = $this->getRequest()->getParam('sn');

if ($sn) {
    $QMarket = new Application_Model_Market();

    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $sales = $QMarket->fetchAll($where);

    $data = array();

    $QGoodCategory = new Application_Model_GoodCategory();
    $categories = $QGoodCategory->get_cache();

    $QGood = new Application_Model_Good();
    $goods = $QGood->get_cache();

    $QGoodColor = new Application_Model_GoodColor();
    $goodColors = $QGoodColor->get_cache();

    $QStaff = new Application_Model_Staff();
    $staffs = $QStaff->get_cache();

    $QDistributor = new Application_Model_Distributor();
    $distributors = $QDistributor->get_cache();

    $QWarehouse = new Application_Model_Warehouse();
    $warehouses = $QWarehouse->get_cache();

    $QBrand = new Application_Model_Brand();

    foreach ($sales as $k=>$sale){
        //get return to which warehouse
        $data[$k]['backs_d_name'] = isset($warehouses[$sale->backs_d_id]) ? $warehouses[$sale->backs_d_id] : '';

        //get retailer
        $data[$k]['retailer_name'] = isset($distributors[$sale->d_id]) ? $distributors[$sale->d_id] : '';

        //get created_by_name
        $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->user_id] : '';

        //get pay_user
        $data[$k]['pay_user_name'] = isset($staffs[$sale->pay_user]) ? $staffs[$sale->pay_user] : '';

        //get category
        $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->cat_id] : '';

        $brand = $QBrand->getBrand($sale->good_id);
        $data[$k]['brand_name'] = $brand[0]['brand_name'];

        //get good
        $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';

        //get goods color
        $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->good_color] : '';

        $data[$k]['sale'] = $sale;

           //get return shop
        $data[$k]['return_shop'] = isset($distributors[$sale->return_shop]) ? $distributors[$sale->return_shop] : '';

        // get ID return shop
        $data[$k]['return_id'] = isset($sale->return_shop) ? $sale->return_shop : '';

        //get retailer ID
        $data[$k]['retailer_id'] = isset($sale->d_id) ? $sale->d_id : '';
    }

    $this->view->sales = $data;

    $imei_return_list='';
    $QImeiReturn = new Application_Model_ImeiReturn();
    $QDigitalReturn = new Application_Model_DigitalSnReturn();

    $where = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
    $imei_return_list = $QImeiReturn->fetchAll($where);
    $this->view->imei_return_list = $imei_return_list;

    if(!empty($imei_return_list))
    {
        $where = $QDigitalReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
        $imei_return_list = $QDigitalReturn->fetchAll($where);
        $this->view->imei_return_digital_list = $imei_return_list;
    }

    

    
}