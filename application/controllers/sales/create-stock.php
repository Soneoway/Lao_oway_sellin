<?php

$db = Zend_Registry::get('db');
$flashMessenger = $this->_helper->flashMessenger;

//edit
$sn = $this->getRequest()->getParam('sn');

$QStaff = new Application_Model_Staff();
$QDistributor = new Application_Model_Distributor();

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

if (My_Staff_Group::inGroup($userStorage->group_id, SALES_ADMIN)) {
    $QAsm = new Application_Model_Asm();
    $asm_cache = $QAsm->get_cache();

    if (isset($asm_cache[ $userStorage->hr_id ])
        && isset($asm_cache[ $userStorage->hr_id ]['area'])
        && count($asm_cache[ $userStorage->hr_id ]['area'])) {

        $this->view->salesmans = $QStaff->get_by_area_cache( $asm_cache[ $userStorage->hr_id ]['area'] );
        $this->view->distributors = $QDistributor->get_by_area_cache( $asm_cache[ $userStorage->hr_id ]['area'] );
    } else {
        exit('No area assigned.');
    }
} else {
    $this->view->salesmans = $QStaff->get_cache();
    $this->view->distributors = $QDistributor->get_all();
}

$QExceptionCase = new Application_Model_ExceptionCase();
$where = $QExceptionCase->getAdapter()->quoteInto('name = ?',
    'LIFETIME_EXCEPTION');
$lifetime_exception = $QExceptionCase->fetchRow($where);

$exception_case = null;
if (isset($lifetime_exception) and $lifetime_exception['value']) {
    eval(json_decode($lifetime_exception['value']));
    $exception_case = isset($data_exception) ? $data_exception : null;
}

if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, SUPER_SALES_ADMIN)) or ($exception_case and in_array
    ($userStorage->id, $exception_case)))
    $this->view->life_time_editable = 1;
else
    $this->view->life_time_editable = 0;

if ($sn) {
    // chặn xem/edit đơn của người khác
    $QMarket = new Application_Model_MarketStock();
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $market_check = $QMarket->fetchRow($where);

    //check status
    if (!$market_check || $market_check['status'] != 1) {
        $flashMessenger->setNamespace('error')->addMessage('Invalid SN!');
        $this->_redirect(HOST . 'sales/stock');
    }

    if ($market_check['user_id'] != $userStorage->id && !My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, SUPER_SALES_ADMIN, CHECK_MONEY))) {
        $flashMessenger->setNamespace('error')->addMessage('You cannot edit this Order');
        $this->_redirect(HOST . 'sales/stock');
    }

    if ($market_check['shipping_yes_time'] or $market_check['pay_time'] or $market_check['outmysql_time']) {
        $flashMessenger->setNamespace('error')->addMessage('This order cannot be changed!');
        $this->_redirect(HOST . 'sales/stock');
    }

    $this->view->staffs_cached = $QStaff->get_cache();
    $where   = array();
    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);
    $sales   = $QMarket->fetchAll($where);
    $data    = array();

    foreach ($sales as $k => $sale) {
        //get goods
        $QGood = new Application_Model_Good();
        $where = $QGood->getAdapter()->quoteInto('cat_id = ?', $sale->cat_id);
        $goods = $QGood->fetchAll($where, 'name');

        $data[$k]['goods'] = $goods;

        //get goods color
        $where = $QGood->getAdapter()->quoteInto('id = ?', $sale->good_id);
        $good = $QGood->fetchRow($where);

        $aColor = array_filter(explode(',', $good->color));
        if ($aColor) {
            $QGoodColor = new Application_Model_GoodColor();
            $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

            $colors = $QGoodColor->fetchAll($where);
            $data[$k]['colors'] = $colors;
        }

        $data[$k]['sale'] = $sale;
    }

    $this->view->sales = $data;

    $QImeiStock = new Application_Model_ImeiStock();
    $where = $QImeiStock->getAdapter()->quoteInto('market_stock_sn = ?', $sn);
    $imei_list = $QImeiStock->fetchAll($where);
    $imei_list_arr = array();
    foreach ($imei_list as $key => $value) {
        $imei_list_arr[] = $value['imei_sn'];
    }
    $this->view->imei_list = implode("\r\n", $imei_list_arr);

    // get tags
    $QTag = new Application_Model_Tag();
    $QTagObject = new Application_Model_TagObject();

    $where = array();
    $where[] = $QTagObject->getAdapter()->quoteInto('object_id = ?', $sn);
    $where[] = $QTagObject->getAdapter()->quoteInto('type = ?', TAG_ORDER);

    $a_tags = array();

    $tags_object = $QTagObject->fetchAll($where);
    if ($tags_object)
        foreach ($tags_object as $to) {
            $where = $QTag->getAdapter()->quoteInto('id = ?', $to['tag_id']);
            $tag = $QTag->fetchRow($where);
            if ($tag)
                $a_tags[] = $tag['name'];
        }

    $this->view->a_tags = $a_tags;

} else {
    if (defined("NO_CREATE_ORDER") && NO_CREATE_ORDER) {
        $this->_helper->viewRenderer->setRender('partials/nocreate');
        return;
    }
}

$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->fetchAll();

$QService = new Application_Model_Service();
$this->view->service = $QService->get_cache_service();

$QOffice = new Application_Model_Office();
$this->view->office = $QOffice->get_cache();

$QJoint = new Application_Model_JointCircular();
$this->view->joint = $QJoint->get_cache();
$this->view->joint_discount = $QJoint->get_cache2();

$QCampaign = new Application_Model_Campaign();
$this->view->campaign = $QCampaign->get_cache();

$QWarehouse = new Application_Model_Warehouse();
$this->view->warehouses = $QWarehouse->fetchAll(null, 'name');

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;
