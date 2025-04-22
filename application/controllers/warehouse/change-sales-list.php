<?php
$sort           = $this->getRequest()->getParam('sort', 'created_at');
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);
$is_changed_wh  = $this->getRequest()->getParam('is_changed_wh');
$status         = $this->getRequest()->getParam('status');
$cat_id         = $this->getRequest()->getParam('cat_id');
$good_id        = $this->getRequest()->getParam('good_id');
$good_color     = $this->getRequest()->getParam('good_color');

$rq             = $this->getRequest()->getParam('rq');

$sn             = $this->getRequest()->getParam('sn');
$co_type        = $this->getRequest()->getParam('co_type');

$warehouse_id1           = $this->getRequest()->getParam('warehouse_id1');
$warehouse_id2 = $this->getRequest()->getParam('warehouse_id2');

$distributor_id1= $this->getRequest()->getParam('distributor_id1');
$distributor_id2= $this->getRequest()->getParam('distributor_id2');

$created_at_to  = $this->getRequest()->getParam('created_at_to');
$created_at_from= $this->getRequest()->getParam('created_at_from',date('d/m/Y', strtotime('-3 day')));

$receive_at_to  = $this->getRequest()->getParam('receive_at_to');
$receive_at_from= $this->getRequest()->getParam('receive_at_from');

$confirmed_at_to  = $this->getRequest()->getParam('confirmed_at_to');
$confirmed_at_from= $this->getRequest()->getParam('confirmed_at_from');

$export         = $this->getRequest()->getParam('export',0);

$cancel        = $this->getRequest()->getParam('cancel',0);

//$created_at_to     = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));
//$created_at_from   = $this->getRequest()->getParam('created_at_from', date('d/m/Y'));


$export = $this->getRequest()->getParam('export');
if(isset($warehouse_id1) || isset($warehouse_id2))
    $is_changed_wh=0;



// if(isset( $distributor_id1)) die( $distributor_id1);
$QWarehouse = new Application_Model_Warehouse();
$warehouses = $QWarehouse->get_cache();

$QDistributor = new Application_Model_Distributor();
$distributors = $QDistributor->get_cache();

$limit = LIMITATION;
$total = 0;

$params = array_filter( array(
    'sn'                => $sn,
    'is_changed_wh'     =>$is_changed_wh,
    'warehouse_id1'     => $warehouse_id1,
    'warehouse_id2'     => $warehouse_id2,
    'distributor_id1'   => $distributor_id1,
    'distributor_id2'   => $distributor_id2,
    'created_at_to'     => $created_at_to,
    'created_at_from'   => $created_at_from,
    'receive_at_to'     => $receive_at_to,
    'receive_at_from'   => $receive_at_from,
    'confirmed_at_to'     => $confirmed_at_to,
    'confirmed_at_from'   => $confirmed_at_from,
    'export'            => $export,
    'cancel'            => $cancel,
    'cat_id'            => $cat_id,
    'good_id'           => $good_id,
    'good_color'        => $good_color,
    'co_type'           => $co_type,
    'rq'                => $rq
));

// print_r($params);die;

$params['status'] = $status;
$params['sort'] = $sort;
$params['desc'] = $desc;
$params['created_at_from'] = $created_at_from;

$QChangeSalesOrder = new Application_Model_ChangeSalesOrder();
$change_sales = array();

//list

if (isset($export) and $export)
{   //export for imei

    if($export == 1){
        $data = $QChangeSalesOrder->fetchPagination($page, null, $total, $params);
        $QChangeSalesOrder->export_change_sales_order($data);
    }else if($export == 2){
        $data = $QChangeSalesOrder->fetchPaginationForDetail($page, null, $total, $params);
        $QChangeSalesOrder->export_change_sales_order_for_detail($data);
        
    }else if($export == 3){

        $params['for_finance'] = true;
        $data = $QChangeSalesOrder->fetchPaginationForDetail($page, null, $total, $params);
        $QChangeSalesOrder->export_change_Sales_for_fianace($data);
    }
    
}else{
    $data = $QChangeSalesOrder->fetchPagination($page, $limit, $total, $params);
}

//print_r($data);
$QStaff = new Application_Model_Staff();
$this->view->staff_cached = $QStaff->get_cache();

$QDistributor = new Application_Model_Distributor();
$this->view->distributor_cached = $QDistributor->get_cache();

$QWarehouse = new Application_Model_Warehouse();
$this->view->warehouse_cached = $QWarehouse->get_cache();

$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

$QGood          = new Application_Model_Good();
$this->view->goods = $QGood->get_cache();

$QGoodColor     = new Application_Model_GoodColor();
$this->view->good_colors = $QGoodColor->get_cache();

$this->view->change_sales   = $data;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'warehouse/change-sales-list/'.( $params ? '?'.http_build_query($params).'&' : '?' );
$this->view->warehouses = $warehouses;
$this->view->distributors = $distributors;
$this->view->offset = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;