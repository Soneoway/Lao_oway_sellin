<?php
$sn = $this->getRequest()->getParam('sn');

if ($sn) {
    $QMarket = new Application_Model_Market();

    $flashMessenger = $this->_helper->flashMessenger;

    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);

    $order = $QMarket->fetchRow($where);

    if (!$order){

        $flashMessenger->setNamespace('error')->addMessage('This sales number is invalid!');

        $this->_redirect('/warehouse/out');
    }

    if ($order['outmysql_time']){

        $flashMessenger->setNamespace('error')->addMessage('This sales order was confirmed out already!');

        $this->_redirect('/warehouse/out');
    }


    if ($this->getRequest()->getMethod() == 'POST'){

        $db = Zend_Registry::get('db');

        $db->beginTransaction();

        try {


            $back_url = $this->getRequest()->getParam('back_url');

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $where = array();

            $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);

            $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

            //check quantity scan out IMEI
            $total_out = $QMarket->count_out_imei($sn);

            $sales_list = $QMarket->fetchAll($where);

            $QGoodCategory = new Application_Model_GoodCategory();

            $sales_list_out = 0;

            $QWarehouseProduct = new Application_Model_WarehouseProduct();

            foreach ($sales_list as $v) {

                // check đơn hàng có được confirm out chưa
                if ($v['outmysql_time']){

                    $flashMessenger->setNamespace('error')->addMessage('This Order was confirmed OUT already!');
                    $this->_redirect('/warehouse/out');

                }

                if ( $v['cat_id'] == PHONE_CAT_ID ){
                    $scanned_out = $QMarket->count_out_imei($sn, $v['good_id'], $v['good_color'], $v['id']);

                    if ($scanned_out < $v['num']){
                        $flashMessenger->setNamespace('error')->addMessage('Please Scan out all of IMEI!');
                        $this->_redirect('/warehouse/sales-confirm?sn='.$sn);
                    }
                } elseif ($v['cat_id'] == DIGITAL_CAT_ID) {
                    $scanned_out = $QMarket->count_out_digital($sn, $v['good_id'], $v['good_color'], $v['id']);

                    if ($scanned_out < $v['num']){
                        $flashMessenger->setNamespace('error')->addMessage('Please Scan out all of Digital SN!');
                        $this->_redirect('/warehouse/sales-confirm?sn='.$sn);
                    }
                } elseif ($v['cat_id'] == ILIKE_CAT_ID) {
                    $scanned_out = $QMarket->count_out_ilike($sn, $v['good_id'], $v['good_color'], $v['id']);

                    if ($scanned_out < $v['num']){
                        $flashMessenger->setNamespace('error')->addMessage('Please Scan out all of Good SN!');
                        $this->_redirect('/warehouse/sales-confirm?sn='.$sn);
                    }
                } elseif ( $v['cat_id'] == ACCESS_CAT_ID ){

                    $where = array();
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('cat_id = ?', $v['cat_id']);
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_id = ?', $v['good_id']);
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_color = ?', $v['good_color']);
                    $where[] = $QWarehouseProduct->getAdapter()->quoteInto('warehouse_id = ?', $v['warehouse_id']);

                    $result = $QWarehouseProduct->fetchRow($where);
                    if ($result){
                        $where = $QWarehouseProduct->getAdapter()->quoteInto('id = ?', $result['id']);
                        $quantity = $result['quantity'] - $v['num'];
                        $data = array(
                            'quantity' => ($quantity > 0 ? $quantity : 0)
                        );

                        $QWarehouseProduct->update($data, $where);
                    }
                }

            }


            $date = date('Y-m-d H:i:s');

            $data = array(
                'outmysql_time' => $date,
                'outmysql_user' => $userStorage->id,
            );

            $where = array();

            $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);

            $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

            $QMarket->update($data, $where);

            //commit
            $db->commit();

            //todo log
            $ip = $this->getRequest()->getServer('REMOTE_ADDR');

            $info = 'Check out warehousing: Sale order number: '.$sn;

            $QLog = new Application_Model_Log();

            $QLog->insert( array (
                'info' => $info,
                'user_id' => $userStorage->id,
                'ip_address' => $ip,
                'time' => $date,
            ) );

            $flashMessenger->setNamespace('success')->addMessage('Done!');

        }catch (Exception $e){

            $db->rollback();

            $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');

        }

        $this->_redirect($back_url);
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

    $sales_list_out = $sales_list_digital_out = $sales_list_ilike_out = array();
    foreach ($sales as $k=>$v) {
        $sales_list_out[$k]         = $QMarket->count_out_imei($sn, $v->good_id, $v->good_color, $v->id);
        $sales_list_digital_out[$k] = $QMarket->count_out_digital($sn, $v->good_id, $v->good_color, $v->id);
        $sales_list_ilike_out[$k]   = $QMarket->count_out_ilike($sn, $v->good_id, $v->good_color, $v->id);
    }

    $this->view->sales_list_out         = $sales_list_out;
    $this->view->sales_list_digital_out = $sales_list_digital_out;
    $this->view->sales_list_ilike_out   = $sales_list_ilike_out;

    foreach ($sales as $k=>$sale){
        //get retailer
        $data[$k]['retailer_name'] = isset($distributors[$sale->d_id]) ? $distributors[$sale->d_id] : '';

        //get created_by_name
        $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->user_id] : '';

        //get salesman_name
        $data[$k]['salesman_name'] = isset($staffs[$sale->salesman]) ? $staffs[$sale->salesman] : '';

        //get pay_user
        $data[$k]['pay_user_name'] = isset($staffs[$sale->pay_user]) ? $staffs[$sale->pay_user] : '';

        //get shipping_yes_id
        $data[$k]['shipping_yes_id_name'] = isset($staffs[$sale->shipping_yes_id]) ? $staffs[$sale->shipping_yes_id] : '';

        //get category
        $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->cat_id] : '';

        //get good
        $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';

        //get goods color
        $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->good_color] : '';

        $data[$k]['sale'] = $sale;
    }

    $this->view->sales = $data;

    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;

    //back url
    $this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER');
}