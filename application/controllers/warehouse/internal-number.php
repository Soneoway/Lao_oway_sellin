<?php
$sort           = $this->getRequest()->getParam('sort', '');
$desc           = $this->getRequest()->getParam('desc', 1);
$order_name     = trim($this->getRequest()->getParam('order_name'));
$sn             = trim($this->getRequest()->getParam('sn'));
$transport_from = trim($this->getRequest()->getParam('transport_from' ));
$transport_to   = trim($this->getRequest()->getParam('transport_to'));
$invoice_number = $this->getRequest()->getParam('invoice_number');
$invoice_prefix = $this->getRequest()->getParam('invoice_prefix');
$service_from   = $this->getRequest()->getParam('service_from');
$service_to     = $this->getRequest()->getParam('service_to');
$export         = $this->getRequest()->getParam('export', 0);

$page  = $this->getRequest()->getParam('page', 1);
$limit = LIMITATION;
$total = 0;

$params = array(
    'order_name'          => $order_name,
    'sn'                  => $sn,
    'transport_date_from' => $transport_from,
    'transport_date_to'   => $transport_to,
    'invoice_number'      => $invoice_number,
    'invoice_prefix'      => $invoice_prefix,
    'sort'                => $sort,
    'desc'                => $desc,
    'service_from'        => $service_from,
    'service_to'          => $service_to
);

$QInternalOrder = new Application_Model_InternalOrder();
$QInvoicePrefix = new Application_Model_InvoicePrefix();
$QService       = new Application_Model_Service();
$services       = $QService->get_cache_service();


// Xuất excel
if ( isset($export) && $export ) {
    $orders = $QInternalOrder->fetchPagination($page, null, $total, $params);
    $this->_exportInternalExcel($orders);
} else {
    $orders = $QInternalOrder->fetchPagination($page, $limit, $total, $params);
}

$whereInvoicePrefixs = array();
$whereInvoicePrefixs[] = $QInvoicePrefix->getAdapter()->quoteInto('company = ?' , COMPANY_OPPO);
$whereInvoicePrefixs[] = $QInvoicePrefix->getAdapter()->quoteInto('type = ?' , 2);

$invoide_prefixs = $QInvoicePrefix->fetchAll($whereInvoicePrefixs)->toArray();
$invoice_prefix_data = array();
if($invoide_prefixs)
{
    foreach($invoide_prefixs as $k => $v)
    {
        $invoice_prefix_data[$v['id']] = $v;
    }
}

$this->view->orders          = $orders;
$this->view->invoice_prefixs = $invoice_prefix_data;
$this->view->services        = $services;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'warehouse/internal-number/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

if($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setRender('partials/internal_list');
} else {
    $flashMessenger               = $this->_helper->flashMessenger;
    $messages_success             = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages_success = $messages_success;
    $messages                     = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages         = $messages;

    $this->_helper->viewRenderer->setRender('internal-number');
}