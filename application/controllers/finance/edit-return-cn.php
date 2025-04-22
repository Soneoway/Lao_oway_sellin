<?php
$sn = $this->getRequest()->getParam('sn');

if ($sn) {

    $QMarket = new Application_Model_Market();
    $db = Zend_Registry::get('db');

    if ($this->getRequest()->getMethod() == 'POST') {

        $active_cn = $this->getRequest()->getParam('active_cn','0');

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);

        $data = array('pay_text' => $pay_text, );

        $date = date('Y-m-d H:i:s');

        $data['last_updated_at'] = $date;
        $data['last_updated_by'] = $userStorage->id;


        $data['active_cn'] = null;
        if(isset($active_cn) && $active_cn == 1){
            $data['active_cn'] = $active_cn;
        }

        $flashMessenger = $this->_helper->flashMessenger;
        try {

            $db->beginTransaction();

            $QMarket->update($data, $where);

            $data = array(
                'status' => $active_cn,
                'update_date' => $date,
                'update_by' => $userStorage->id
                );

            $QCN = new Application_Model_CreditNote();
            $where = $QCN->getAdapter()->quoteInto('sn = ?', $sn);
            $QCN->update($data, $where);


            $flashMessenger->setNamespace('success')->addMessage('Done!');

            $db->commit();

        }
        catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
        }

        $this->_redirect('/finance/return-list-cn');

    }

    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $sales = $QMarket->fetchAll($where);

    $data = array();

    $QGoodCategory = new Application_Model_GoodCategory();
    $categories = $QGoodCategory->get_cache();

    $QGood = new Application_Model_Good();
    $goods = $QGood->get_cache();

    $QGoodColor = new Application_Model_GoodColor();
    $goodColors = $QGoodColor->get_cache();

    $QStaff = new Application_Model_Staff();
    $staffs = $QStaff->get_cache();

    $QDistributor = new Application_Model_Distributor();
    $distributors = $QDistributor->get_cache();

    $QWarehouse = new Application_Model_Warehouse();
    $warehouses = $QWarehouse->get_cache();

    foreach ($sales as $k=>$sale){
        //get return to which warehouse
        $data[$k]['backs_d_name'] = isset($warehouses[$sale->backs_d_id]) ? $warehouses[$sale->backs_d_id] : '';

        //get retailer
        $data[$k]['retailer_name'] = isset($distributors[$sale->d_id]) ? $distributors[$sale->d_id] : '';

        //get created_by_name
        $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->user_id] : '';

        //get pay_user
        $data[$k]['pay_user_name'] = isset($staffs[$sale->pay_user]) ? $staffs[$sale->pay_user] : '';

        //get category
        $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->cat_id] : '';

        //get good
        $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';

        //get goods color
        $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->good_color] : '';

        $data[$k]['sale'] = $sale;
    }

    $this->view->sales = $data;

    $imei_return_list='';
    $QImeiReturn = new Application_Model_ImeiReturn();
    $QDigitalReturn = new Application_Model_DigitalSnReturn();

    $where = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
    $imei_return_list = $QImeiReturn->fetchAll($where);
    $this->view->imei_return_list = $imei_return_list;

    if(!empty($imei_return_list))
    {
        $where = $QDigitalReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
        $imei_return_list = $QDigitalReturn->fetchAll($where);
        $this->view->imei_return_digital_list = $imei_return_list;
    }

    

    
}