<?php
$this->_helper->layout->disableLayout();
$id = $this->getRequest()->getParam('id');

if (isset($id) && $id) {
    
$QDistributor   = new Application_Model_Distributor();
$QWarehouse     = new Application_Model_Warehouse();
$QGood          = new Application_Model_Good();
$QGoodColor     = new Application_Model_GoodColor();
$QChangeSalesList     = new Application_Model_ChangeSalesList();
$QGoodCategory = new Application_Model_GoodCategory();
$QChangeSalesOrder = new Application_Model_ChangeSalesOrder();
$QChangeSalesProduct = new Application_Model_ChangeSalesProduct();
$QChangeDocType = new Application_Model_ChangeDocType();
$QStaff = new Application_Model_Staff();

$staff = $QStaff->get_cache();
$this->view->staff = $staff;
$distributors = $QDistributor->get_cache();
$warehouses = $QWarehouse->get_cache();
$changedoctype = $QChangeDocType->get_cache();
$where = $QGood->getAdapter()->quoteInto('cat_id = ?', ACCESS_CAT_ID);
$this->view->accessories = $QGood->fetchAll($where, 'name');

$this->view->changedoctype = $changedoctype;
$this->view->distributors = $distributors;
$this->view->warehouses = $warehouses;
$goods_cached = $QGood->get_cache();
$this->view->goods_cached = $goods_cached;

$desc_name_cache = $QGood->get_desc_name_cache();
$this->view->desc_name_cache = $desc_name_cache;

$good_colors_cached = $QGoodColor->get_cache();
$this->view->good_colors_cached = $good_colors_cached;
$this->view->good_categories = $QGoodCategory->fetchAll();



$whereChangeSalesList = $QChangeSalesList->getAdapter()->quoteInto('changed_id = ?', $id);
$changeSalesList = $QChangeSalesList->fetchRow($whereChangeSalesList);
$this->view->changeSalesList = $changeSalesList;

   
$whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
$changeSalesOrder = $QChangeSalesOrder->fetchRow($whereChangeSalesOrder);
$this->view->changeSalesOrder = $changeSalesOrder;

// $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('changed_id = ?',$id);
// $changeSalesProduct = $QChangeSalesProduct->fetchAll($whereChangeSalesProduct);
// $this->view->changeSalesProduct = $changeSalesProduct;

//$limit = 10 ;

 $params['changed_id'] = $id;
    $changeSalesProduct = $QChangeSalesProduct->fetchPagination($page, $limit, $total, $params);
    $this->view->changeSalesProduct = $changeSalesProduct;
    $this->view->total = $total;


 }
