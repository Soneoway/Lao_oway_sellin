<?php

$sort           = $this->getRequest()->getParam('sort', 'created_at');
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);
$export           = $this->getRequest()->getParam('export');

$warehouse_id   = $this->getRequest()->getParam('warehouse_id');
$date_to        = $this->getRequest()->getParam('date_to');
$date_from      = $this->getRequest()->getParam('date_from');
$warehouse_id               = is_array($warehouse_id) ? array_unique( array_filter( $warehouse_id ) ) : array();
$limit = LIMITATION;
$total = 0;

$params = array_filter( array(
    
    'warehouse_id'  => $warehouse_id,
    'date_from'     => $date_from,
    'date_to'       => $date_to,
    
));


$params['sort'] = $sort;
$params['desc'] = $desc;


$QWarehouse = new Application_Model_Warehouse();
if ($params['warehouse_id']) {
    $listPhone              = $QWarehouse->in_phone($params);
    $listAccessories        = $QWarehouse->in_accessories($params);
    $listOutPhone           = $QWarehouse->out_phone($params);
    $listOutAccessories     = $QWarehouse->out_accessories($params);
    $listCoInPhone          = $QWarehouse->co_in_phone($params);
    $listCoOutPhone         = $QWarehouse->co_out_phone($params);
    $listCoInAccessories    = $QWarehouse->co_in_accessories($params);
    $listCoOutAccessories   = $QWarehouse->co_out_accessories($params);

    $listInAccessories_gift     = $QWarehouse->in_accessories_gift_box($params);
    $listOutAccessories_gift    = $QWarehouse->out_accessories_gift_box($params);
    $listCoInAccessories_gift   = $QWarehouse->co_in_accessories_gift_box($params);
    $listCoOutAccessories_gift  = $QWarehouse->co_out_accessories_gift_box($params);

    $this->view->listCoInPhone          = $listCoInPhone;
    $this->view->listCoOutPhone         = $listCoOutPhone;
    $this->view->listCoInAccessories    = $listCoInAccessories;
    $this->view->listCoOutAccessories   = $listCoOutAccessories;
    $this->view->listOutPhone           = $listOutPhone;
    $this->view->listOutAccessories     = $listOutAccessories;
    $this->view->listPhone              = $listPhone;
    $this->view->listAccessories        = $listAccessories;
    $this->view->listInAccessories_gift        = $listInAccessories_gift;
    $this->view->listOutAccessories_gift       = $listOutAccessories_gift;
    $this->view->listCoInAccessories_gift      = $listCoInAccessories_gift;
    $this->view->listCoOutAccessories_gift     = $listCoOutAccessories_gift;
    $this->view->action                 = 1;
    $this->view->warehouse_id           = $warehouse_id;

    if ($export == 1) {
        $_data = array(
            'listCoInPhone'          => $listCoInPhone,
            'listCoOutPhone'         => $listCoOutPhone,
            'listCoInAccessories'    => $listCoInAccessories,
            'listCoOutAccessories'   => $listCoOutAccessories,
            'listOutPhone'           => $listOutPhone,
            'listOutAccessories'     => $listOutAccessories,
            'listPhone'              => $listPhone,
            'listAccessories'        => $listAccessories,
            'listInAccessories_gift'            => $listInAccessories_gift,
            'listOutAccessories_gift'           => $listOutAccessories_gift,
            'listCoInAccessories_gift'          => $listCoInAccessories_gift,
            'listCoOutAccessories_gift'         => $listCoOutAccessories_gift,
            'warehouse_id'  => $warehouse_id,
            'date_from'     => $date_from,
            'date_to'       => $date_to,    
            );
      
     $this->_exportExcelInOutReport($_data);
}
}

// lấy danh sách các sản phẩm theo từng po_sn ở trên



$params['sn'] = $sn;

$this->view->desc               = $desc;
$this->view->sort               = $sort;


$this->view->params             = $params;
$this->view->limit              = $limit;
$this->view->total              = $total;
$this->view->url                = HOST.'tool/materail-in-out-report/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset   = $limit*($page-1);


$QWarehouse = new Application_Model_Warehouse();
$this->view->warehouses = $QWarehouse->get_cache();


$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;


