<?php
$flashMessenger = $this->_helper->flashMessenger;

//print_r($_GET);die;

$box_sn = $this->getRequest()->getParam('box_sn');

$db = Zend_Registry::get('db');
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$QDistributor = new Application_Model_Distributor();
//if ($box_sn) {

    $imei_return_check=null;
    $QReturnBoxNumberImei = new Application_Model_ReturnBoxNumberImei();
    //if($box_sn !=''){

            $params = array_filter(array(
            'box_sn'       => $box_sn,
            'action_frm' => 'confirm'
            ));

        $imei_return_check = $QReturnBoxNumberImei->getReturnBoxNumberImeiCheckAction($params);
        $where_sub = array();
        $where_sub[] = $QDistributor->getAdapter()->quoteInto('sub_d_id = ?', 1);
        $rs_sub_d_id = $QDistributor->fetchAll($where_sub, 'name');
    //}

    $where = array();
    $QWarehouse = new Application_Model_Warehouse();
    $select_group = $db->select()
        ->from(array('u' => 'warehouse_group_user'),array('u.warehouse_id'))
        ->where('u.user_id=?',$userStorage->id);
    $result_group = $db->fetchAll($select_group);
    $warehouse_id = "";
    if ($result_group){
        foreach ($result_group as $to) {
            $warehouse_id .= $to['warehouse_id'].',';
        }

        $where[] = $QWarehouse->getAdapter()->quoteInto('id in('.rtrim($warehouse_id, ',').')', null);
    }
    $this->view->warehouses = $QWarehouse->fetchAll($where, 'name');

    $this->view->box_imei = $imei_return_check;
//}


$this->view->box_sn = $box_sn;
$this->view->sub_d_id = $rs_sub_d_id;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;