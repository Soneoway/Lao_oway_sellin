<?php

$sort = $this->getRequest()->getParam('sort','created_date');
$desc = $this->getRequest()->getParam('desc', 1);


$code           = $this->getRequest()->getParam('code');
$customer_name  = $this->getRequest()->getParam('customer_name');
$phone_number   = $this->getRequest()->getParam('phone_number');
$tax_number     = $this->getRequest()->getParam('tax_number');
$tax_address    = $this->getRequest()->getParam('tax_address');

$export = $this->getRequest()->getParam('export', 0);

$page   = $this->getRequest()->getParam('page', 1);
$limit  = LIMITATION;
$total  = 0;

$params = array(
    'id'           => $id,
    'sort'         => $sort,
    'desc'         => $desc,
    'code'         => $code,
    'customer_name'=> $customer_name,
    'phone_number' => $phone_number,
    'tax_number'   => $tax_number,
    'tax_address'  => $tax_address,
    'export'       => $export
);

$QMB = new Application_Model_MemberBrandshop();

$memberBrandshop = $QMB->fetchPagination($page, $limit, $total, $params);

$this->view->memberBrandshops = $memberBrandshop;

$this->view->desc        = $desc;
$this->view->current_col = $sort;
$this->view->params      = $params;
$this->view->limit       = $limit;
$this->view->total       = $total;
$this->view->url         = HOST.'sales/member-brandshop/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset       = $limit*($page-1);

$flashMessenger               = $this->_helper->flashMessenger;
$messages_success             = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

$messages                     = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages         = $messages;
