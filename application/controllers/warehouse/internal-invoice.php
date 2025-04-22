<?php

$flashMessenger = $this->_helper->flashMessenger;

$order_name             = $this->getRequest()->getParam('order_name');
$transport_date         = $this->getRequest()->getParam('transport_date');
$contract_name          = $this->getRequest()->getParam('contract_name');
$contract_for_name      = $this->getRequest()->getParam('contract_for_name');
$contract_for_work      = $this->getRequest()->getParam('contract_for_work');
$transport_name         = $this->getRequest()->getParam('transport_name');
$transport_type         = $this->getRequest()->getParam('transport_type');
$from_warehouse         = $this->getRequest()->getParam('from_warehouse');
$to_warehouse           = $this->getRequest()->getParam('to_warehouse');
$invoice_prefix         = $this->getRequest()->getParam('invoice_prefix');
$user_id                = $this->getRequest()->getParam('user_id');
$ids                    = $this->getRequest()->getParam('ids');
$good_name              = $this->getRequest()->getParam('good_name');
$good_type              = $this->getRequest()->getParam('good_type');
$good_desc              = $this->getRequest()->getParam('good_desc');
$product_id             = $this->getRequest()->getParam('product_id');
$price                  = $this->getRequest()->getParam('price');
$quantity               = $this->getRequest()->getParam('quantity');
$code                   = $this->getRequest()->getParam('code');
$price                  = $this->getRequest()->getParam('price');
$color                  = $this->getRequest()->getParam('good_color');
$imei                   = $this->getRequest()->getParam('imei');
$change_order           = $this->getRequest()->getParam('change-order');
$product_status         = $this->getRequest()->getParam('product_status');
$good                   = $this->getRequest()->getParam('good');
$system                 = $this->getRequest()->getParam('system');
$created_by             = $this->getRequest()->getParam('created_by');



$QInvoicePrefix       = new Application_Model_InvoicePrefix();
$QGood                = new Application_Model_Good();
$whereInvoicePrefix   = array();
$whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('type = ? ' , INVOICE_PREFIX_TYPE_TRANSPORT);

$QInternalNumber      = new Application_Model_InternalNumber();
$QMobilizationNumber  = new Application_Model_MobilizationNumber();
$QInternalOrder       = new Application_Model_InternalOrder();

$invoice_prefix_data_transport = $QInvoicePrefix->fetchAll($whereInvoicePrefix);

if(isset($invoice_prefix_data_transport))
    $invoice_prefix_data_transport = $invoice_prefix_data_transport->toArray();

/*
if(empty($invoice_prefix_data_transport))
    throw new exception('invalid invoice prefix');
/*
if(empty($from_warehouse) || empty($to_warehouse))
    throw new exception('Please warehouse input from service');
*/

$from_warehouse    = trim($from_warehouse);
$from_warehouse    = unserialize($from_warehouse);
$to_warehouse      = trim($to_warehouse);
$to_warehouse      = unserialize($to_warehouse);



$inset = array();
$product = array();
if(isset($ids) and $ids)
{
    foreach($ids as $k => $item)
    {
       
        if(!in_array($good_type[$k] .'_'. $product_id[$k]. '_' . $color[$k] . '_' . $product_status[$k], $inset))
        {
            $good_data  = trim($good[$k] ? $good[$k] : '');
            $good_data  = @unserialize($good_data); //decode first
           // $good_data  = array_map('utf8_encode', $good_data ); // encode the array again

            $good_name  = $good_data['name'];
            $code       = isset($good_data['code']) ? $good_data['code'] : $good_data['name'] ;
            if($good_type[$k] == 'PARTS')
                   $price      = $good_data['price'];
                else
                   $price      = $good_data['price_3'];
            

            $product[$product_id[$k]]['good_name']  = $good_desc[$k] ? $good_desc[$k] : '';
            $product[$product_id[$k]]['code']       = $code ? $code : '';
            $product[$product_id[$k]]['color']      = $color[$k] ? $color[$k] : '';
            $product[$product_id[$k]]['in']         = ($quantity[$k] ?  $quantity[$k] : 0) + (intval(isset($product[$product_id[$k]]['in']) ? $product[$product_id[$k]]['in'] : 0)) ;
            $product[$product_id[$k]]['good_type']  = $good_type[$k] ? $good_type[$k] : '';
            $product[$product_id[$k]]['price']      = $price ? $price : '';
            $product[$product_id[$k]]['total']      = intval($price  * $product[$product_id[$k]]['in']);
            $product[$product_id[$k]]['product_id'] = intval($product_id[$k] ? $product_id[$k] : 0);
            $product[$product_id[$k]]['imei']       = trim($imei[$k] ? $imei[$k] : '');
            $product[$product_id[$k]]['good_desc']  = $good_desc[$k] ? $good_desc[$k] : '';
            $inset[] = $good_type[$k] .'_'. $product_id[$k]. '_' . $color[$k] . '_' . $product_status[$k];
        }

      /*  if(isset($product[$k]['product_id']))
        {
            $good_rowset = $QGood->find($product[$k]['product_id']);
        }
      */

    }
}
$paramsInternalInvoice = array_filter(array(
    'from_warehouse' => $from_warehouse,
    'service'        => 1
));



$order_name              = $QMobilizationNumber->getLastId($from_warehouse);
$internal_invoice_number = $QInternalNumber->getLastId($paramsInternalInvoice);


$data = array(
    'order_name'               => $order_name ? $order_name : '',
    'transport_date'           => $transport_date ? $transport_date : '',
    'contract_name'            => $contract_name ? $contract_name : '',
    'contract_for_name'        => $contract_for_name ? $contract_for_name : '',
    'contract_for_work'        => $contract_for_work ? $contract_for_work : '',
    'transport_name'           => $transport_name ? $transport_name : '',
    '$transport_type'          => $transport_type ? $transport_type : '',
    'from_warehouse'           => $from_warehouse ? $from_warehouse : '',
    'to_warehouse'             => $to_warehouse ? $to_warehouse : '',
    'user_id'                  => $user_id ? $user_id : '',
    'product'                  => $ids ? $ids : '',
    'quantity'                 => $quantity ? $quantity : '',
    'code'                     => $code ? $code : '',
    'price'                    => $price ? $price : '',
    'product'                  => $product ? $product : '',
    'invoice_number'           => $internal_invoice_number ? $internal_invoice_number : '',
    'change_order'             => $change_order ? $change_order : '',
    'created_by'               => $created_by ? $created_by : ''
);

$where   = array();
$where[] = $QInternalOrder->getAdapter()->quoteInto('sn = ?', $change_order);
$where[] = $QInternalOrder->getAdapter()->quoteInto('invoice_number is not null' , null);
$where[] = $QInternalOrder->getAdapter()->quoteInto('order_name is not null' , null);
$where[] = $QInternalOrder->getAdapter()->quoteInto('is_back = 1' , null);
$is_back_order  = $QInternalOrder->fetchRow($where);


$this->view->is_back_order = $is_back_order ? $is_back_order : null;
$this->view->data = $data;
$this->view->invoice_prefix_data_transport = $invoice_prefix_data_transport;
$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();
$this->_helper->viewRenderer('warehouse/partials/invoice/customer-transport', null, true);

