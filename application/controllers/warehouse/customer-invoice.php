<?php
$flashMessenger = $this->_helper->flashMessenger;

$customer_name          = $this->getRequest()->getParam('customer_name');
$tax_number             = $this->getRequest()->getParam('tax_number');
$address                = $this->getRequest()->getParam('address');
$delivery_address       = $this->getRequest()->getParam('delivery_address');
$invoice_number         = $this->getRequest()->getParam('invoice_number');
$invoice_prefix         = $this->getRequest()->getParam('invoice_prefix');
$product                = $this->getRequest()->getParam('product');
$service_id             = $this->getRequest()->getParam('service_id');
$user_id                = $this->getRequest()->getParam('user_id');

$QInvoicePrefix = new Application_Model_InvoicePrefix();
$invoice_prefix_data = $QInvoicePrefix->get_cache();

$service = My_Finance_Service::$name;

$whereInvoicePrefix = array();
$whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('type is null' , null);
//$whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('company in (?)' , array(COMPANY_OPPO));
$whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('status = ?' , 1);

$invoice_prefixs = $QInvoicePrefix->fetchAll($whereInvoicePrefix);




$data = array(
    'customer_name'    => $customer_name ? $customer_name : '',
    'tax_number'       => $tax_number ? $tax_number : '',
    'address'          => $address ? $address : '',
    'delivery_address' => $delivery_address ? $delivery_address : '',
    'invoice_number'   => $invoice_number ? $invoice_number : '',
    'invoice_prefix'   => $invoice_prefix ? $invoice_prefix : '',
    'product'          => $product ? unserialize($product) : '',
    'service_id'       => $service_id ? $service_id : '',
    'user_id'          => $user_id ? $user_id : ''
);

$this->view->invoice_prefixs = $invoice_prefixs;
$this->view->services  = $service;
$this->view->data = $data;
$this->view->invoice_prefix_data = $invoice_prefix_data;
$this->view->messages_success    = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();

