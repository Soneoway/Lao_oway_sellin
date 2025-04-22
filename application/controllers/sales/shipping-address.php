<?php
$id = $this->getRequest()->getParam('id');
$flashMessenger = $this->_helper->flashMessenger;
$sort   = $this->getRequest()->getParam('sort', '');
$desc   = $this->getRequest()->getParam('desc', 1);

$id     = $this->getRequest()->getParam('id');

$contact_name     = $this->getRequest()->getParam('contact_name');
$address     = $this->getRequest()->getParam('address');
$phone     = $this->getRequest()->getParam('phone');

$page   = $this->getRequest()->getParam('page', 1);
$limit  = LIMITATION;
$total  = 0;
$QShippingAddress = new Application_Model_ShippingAddress();

$params = array(
    'sort'          => $sort,
    'desc'          => $desc,
    'id'            => $id,
    'region'        => $region,
    'contact_name' => $contact_name,
    'address'       => $address,
    'phone'         => $phone
);

$shipping = $QShippingAddress->fetchPagination($page, $limit, $total, $params);
// print_r($shipping);
$this->view->ship_add  =$shipping ;


$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER') ? $this->getRequest()->getServer('HTTP_REFERER') : 'sales/shipping-address';
$this->view->url    = HOST . 'sales/shipping-address' . ($params ? '?' . http_build_query($params) .'&' : '?');
$this->view->offset = $limit * ($page - 1);

$this->view->update_shipping_address  = 0;

//62=Shipping Address Distributor
if (My_Staff_Group::inGroup($userStorage->group_id,array(ADMINISTRATOR_ID,SUPER_SALES_ADMIN,CHECK_MONEY,62))) 
{
    $this->view->update_shipping_address  = 1;
}

$messages                     = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages         = $messages;

$messages_success             = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;