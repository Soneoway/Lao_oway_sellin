<?php
$id = $this->getRequest()->getParam('id');
$number = $this->getRequest()->getParam('number');
$flashMessenger = $this->_helper->flashMessenger;
$back_url = $this->getRequest()->getParam('back_url');
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
set_time_limit(0);
$QLog = new Application_Model_InvoiceLog();
$QInvoiceNumber = new Application_Model_InvoiceNumber();
$QInvoicePrefix = new Application_Model_InvoicePrefix();

$whereInvoicePrefix = array();
$whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('type is null' , null);
$whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('status = ?' , 1);

$invoice_prefixs = $QInvoicePrefix->fetchAll($whereInvoicePrefix);

if($invoice_prefixs)
    $invoice_prefixs = $invoice_prefixs->toArray();

$services       = My_Finance_Service::$name;

$this->view->invoice_prefixs = $invoice_prefixs;
$this->view->services        = $services;

$db = Zend_Registry::get('db');

$db->beginTransaction();

if ($this->getRequest()->getMethod() == 'POST'){

    try {





        $invoice_prefix = $this->getRequest()->getParam('invoice_prefix');
        $service        = $this->getRequest()->getParam('service');
        $prefix         = $this->getRequest()->getParam('prefix');
        $from           = $this->getRequest()->getParam('from');
        $to             = $this->getRequest()->getParam('to');

        if(empty($number) and empty($from) and empty($to))
        {
            throw new exception('Please input number');
        }

        $invoicePrefix = $QInvoicePrefix->find($prefix);
        $prefix_row    = $invoicePrefix->current();
        $warehouse_id  = $prefix_row['warehouse_id'];

        $invoice_number_first = $QInvoiceNumber->getLastId($warehouse_id);
        $invoice_result_set   = $QInvoiceNumber->find($invoice_number_first);
        $invoice_first        = $invoice_result_set->current();

        if(empty($prefix))
        {
            throw new exception('Please input prefix');
        }

        if(empty($invoice_first))
        {
            throw new exception('invalid invoice number');
        }

        $id = '';

        $loop_time = 0;

        if($from and $from)
            $loop_time = $from;

        if($to and $to)
            $number = intval($to) - intval($from);

        for ($loop_time; $loop_time <= $to; $loop_time++) {

            $data = array(
                'invoice_number' => $QInvoiceNumber->getLastId($warehouse_id),
                'invoice_sign'   => $prefix,
                'date' => date('Y-m-d h:i:s')
            );

            if(isset($from) and $from and isset($to) and $to)
                $data['invoice_number'] = My_Finance_InvoiceNumber::formatInvoice($loop_time);


            if(isset($service) and $service)
            {
                $data['service']    = 1;
                $data['service_id'] = $service;
            }

            $QInvoiceNumber->insert($data);

        }



        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        $invoice_number_last = $QInvoiceNumber->getLastId(1);
        $invoice_result_set = $QInvoiceNumber->find($invoice_number_last);
        $invoice_last = $invoice_result_set->current();

        $info = 'Add Number Invoice :' . $number . ' From : ' . $invoice_number_first . ' To :' . $invoice_number_last;



        $QLog->insert(array(
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => date('Y-m-d h:i:s'),
        ));


        $db->commit();
        $flashMessenger->setNamespace('success')->addMessage('Done!');
        $this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER');
        $this->_redirect(HOST."warehouse/product-out");


    }
    catch(exception $e)
    {
        $db->rollback();
        $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        $this->_redirect(HOST."warehouse/product-out");
    }


}