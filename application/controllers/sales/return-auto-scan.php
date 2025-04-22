<?php
$flashMessenger = $this->_helper->flashMessenger;

$QStore = new Application_Model_Store();
$QWarehouse = new Application_Model_Warehouse();

$this->view->store = $QStore->get_cache();
$this->view->warehouse = $QWarehouse->get_cache();

//edit
// $sn = $this->getRequest()->getParam('sn');
// $db = Zend_Registry::get('db');
// $userStorage = Zend_Auth::getInstance()->getStorage()->read();
// if ($sn) {

//     $QMarket = new Application_Model_Market();
//     $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
//     $sales = $QMarket->fetchRow($where);
//     if ($sales && $sales['user_id'] != $userStorage->id && !My_Staff_Group::inGroup($userStorage->group_id, ADMINISTRATOR_ID,CHECK_MONEY)) {
//         $flashMessenger->setNamespace('error')->addMessage('You cannot edit this Order');
//         $this->_redirect(HOST.'sales');
//     }

//     $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
//     $sales = $QMarket->fetchAll($where);

//     $data = array();

//     foreach ($sales as $k=>$sale){
//         //get goods
//         $QGood = new Application_Model_Good();
//         $where = $QGood->getAdapter()->quoteInto('cat_id = ?', $sale->cat_id);
//         $goods = $QGood->fetchAll($where, 'name');

//         $data[$k]['goods'] = $goods;

//         //get goods color
//         $where = $QGood->getAdapter()->quoteInto('id = ?', $sale->good_id);
//         $good = $QGood->fetchRow($where);

//         $aColor = array_filter(explode(',', $good->color));
//         if ($aColor){
//             $QGoodColor = new Application_Model_GoodColor();
//             $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

//             $colors = $QGoodColor->fetchAll($where);
//             $data[$k]['colors'] = $colors;
//         }
//         $data[$k]['sale'] = $sale;       

//         $invoice = $QMarket->getInvoiceByDistributor($sale->d_id);
//         $data[$k]['invoice'] = $invoice;

//     }

    

//     $QImeiReturn = new Application_Model_ImeiReturn();
//     $where = array();
//     $where[] = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?',$sn);
//     $imei_return_check = $QImeiReturn->fetchAll($where);
//     $this->view->imei_return = $imei_return_check;
    

//     $this->view->sales = $data;
// }

//     // if (My_Staff_Group::inGroup($userStorage->group_id, RETURN_NO_CN) || $userStorage->group_id == ADMINISTRATOR_ID ) 
//     // {
//     //     $this->view->show_create_cn_menu = true;
//     // }else{
//     //     $this->view->show_create_cn_menu = false;
//     // }

//     $this->view->show_create_cn_menu = true;


// $QGoodCategory = new Application_Model_GoodCategory();
// $this->view->good_categories = $QGoodCategory->fetchAll();

// $where = array();
// $QDistributor = new Application_Model_Distributor();
// $select_group = $db->select()
//             ->from(array('u' => 'distributor_group_user'),array('u.group_id'))
//             ->where('u.user_id=?',$userStorage->id);
//         $result_group = $db->fetchAll($select_group);
//         $group_id = "";
//         if ($result_group){
//             foreach ($result_group as $to) {
//                 $group_id .= $to['group_id'].',';
//             }

//             $where[] = $QDistributor->getAdapter()->quoteInto('id in('.rtrim($group_id, ',').')', null);
//         }
// $this->view->distributors = $QDistributor->fetchAll($where, 'title');

// $where = array();
// $QWarehouse = new Application_Model_Warehouse();
// $select_group = $db->select()
//         ->from(array('u' => 'warehouse_group_user'),array('u.warehouse_id'))
//         ->where('u.user_id=?',$userStorage->id);
//     $result_group = $db->fetchAll($select_group);
//     $warehouse_id = "";
//     if ($result_group){
//         foreach ($result_group as $to) {
//             $warehouse_id .= $to['warehouse_id'].',';
//         }

//         $where[] = $QWarehouse->getAdapter()->quoteInto('id in('.rtrim($warehouse_id, ',').')', null);
//     }

// $this->view->warehouses = $QWarehouse->fetchAll($where, 'name');

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;