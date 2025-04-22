<?php
$flashMessenger = $this->_helper->flashMessenger;

//print_r($_GET);die;

$presales_sn = $this->getRequest()->getParam('presales_sn');
$action_frm = $this->getRequest()->getParam('action_frm');

if ($presales_sn) {
    $get_resule=null;
    $QPreSalesOrder 	  = new Application_Model_PreSalesOrder();
    $QGood 				  = new Application_Model_Good();
    $QMarket              = new Application_Model_Market();
    $QCreditNote = new Application_Model_CreditNote();

    $params = array_filter(array(
        'presales_sn'       => $presales_sn,
        'action_frm' => 'view'
        ));
    $get_resule = $QPreSalesOrder->pre_sales_order_view($params);
    //print_r($get_resule);die;
    $get_resule_stock = [];$error_data=[];
    $total_price = '0';$quota_error_message = '';$stock_error_message = '';

    
    foreach($get_resule as $key => $val)
    {
    	//print_r($val);die;  	
    	$distributor_id=$val['distributor_id'];
    	$rank=$val['rank'];
    	$warehouse_id=$val['warehouse_id'];
    	$is_sales_price="1";
    	$type=$val['order_type'];
        $sales_sn=$val['sales_order_sn'];

        /*$credits_note=null;
        if($val['use_cn']=='1')
        {
            $credits_note = $QCreditNote->getCredit_Note($distributor_id,"");
            //print_r($credits_note);
            //die;
        }*/
        
        // Check Stock
    	foreach($val as $key1 => $val1)
	    {
	    	$get_resule_stock[$key][$key1] = $val1;

	    	if($key1=="cat_id"){
	    		$cat_id=$val1;
	    	}
	    	if($key1=="good_id"){
	    		$good_id=$val1;
	    	}
	    	if($key1=="good_color"){
	    		$good_color=$val1;
	    	}
	    	if($key1=="qty"){
	    		$num=$val1;
	    	}
    	
	    	//array_push($get_resule_stock[$key1], $val1);
	    }

	    /*$result = $QGood->get_price($num, $good_id, $good_color, $cat_id, $distributor_id,$warehouse_id, $is_sales_price, $is_return, $type, $id , 0 , $campaign_id);
	    $quota_error_message = '';$stock_error_message = '';
	    //print_r($result);die;
	    switch ($result['code'])
        {
            case 1:

            	$params = array(
	                'distributor_id' => $distributor_id,
	                'good_id'        => $good_id,
	                'good_color'     => $good_color,
	                'num'            => $num,   
	                'rank'           => $rank,   
	                'warehouse_id'   => $warehouse_id,   
	                'cat_id'         => $cat_id,   
	                'sales_sn'       => $sales_sn,   
	                'type'           => $type,   
                );
            	//print_r($params);die;
            	$quota      = $QMarket->checkQuotaOppo($params);
            	//print_r($quota);die;
            	if($quota =="0"){
            		$total_price =  $result['price'];
            	}else{
            		$quota_error_message = "Over Quota!";
            	}
                break;
            case 0:
                $total_price = $result['price'];
                $stock_error_message = "Stock = 0" ;
                break;
            case - 1:
                $total_price = $result['price'];
                $stock_error_message = "Stock = 0" ;
                break;
            case - 2:
                $total_price = $result['price'];
                $stock_error_message = "Stock = ".$result['quantity'];
                break;
            case - 3:
                $total_price = $result['price'];
                $stock_error_message = "Stock = ".$result['quantity'];
                break;
            default:
                $total_price = $result['price'];
                $stock_error_message = "Stock = 0";
                break;
        }*/

    	$get_resule_stock[$key]['total_price'] = $total_price;
    	$get_resule_stock[$key]['stock_error_message'] = $stock_error_message;
    	$get_resule_stock[$key]['quota_error_message'] = $quota_error_message;
    }

    //print_r($get_resule_stock);die;
    $url_link= HOST."sales/print-sale?sn=".$sales_sn;
    $this->view->credits_note = $credits_note;
    $this->view->get_resule = $get_resule_stock;
    $this->view->url_link = $url_link;
}

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;
$this->view->action_frm = $action_frm;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;