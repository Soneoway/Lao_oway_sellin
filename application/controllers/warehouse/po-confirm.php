<?php
$id = $this->getRequest()->getParam('id');
$sure = $this->getRequest()->getParam('sure', 0);
$flashMessenger = $this->_helper->flashMessenger;
$back_url = $this->getRequest()->getParam('back_url');

$QPo = new Application_Model_Po();

if (!$id){

    $flashMessenger->setNamespace('error')->addMessage('Wrong ID!');

    $this->_redirect(HOST."warehouse/in");
}

$where = $QPo->getAdapter()->quoteInto('id = ?', $id);
$PO = $QPo->fetchRow($where);

if (!$PO){

    $flashMessenger->setNamespace('error')->addMessage('Wrong ID!');

    $this->_redirect(HOST."warehouse/in");
}

if ($PO['mysql_time']){

    $flashMessenger->setNamespace('error')->addMessage('This order was confirmed IN already!');

    $this->_redirect(HOST."warehouse/in");

}

try {

    $db = Zend_Registry::get('db');

    $db->beginTransaction();

    $QGoodCategory = new Application_Model_GoodCategory();
    $categories = $QGoodCategory->get_cache();

    $phone_id = PHONE_CAT_ID;
    $this->view->phone_id = $phone_id;

    $num_scanned = 0;
    if (PHONE_CAT_ID == $PO['cat_id']) {
        $num_scanned = $QPo->count_imported_imei($PO['sn']);
        $this->view->num_scanned = $num_scanned;

    } elseif (DIGITAL_CAT_ID == $PO['cat_id']) {
        $num_scanned = $QPo->count_imported_digitalsn($PO['sn']);
        $this->view->num_scanned = $num_scanned;

    } elseif (ILIKE_CAT_ID == $PO['cat_id']) {
        $num_scanned = $QPo->count_imported_sn($PO['sn']);
        $this->view->num_scanned = $num_scanned;

    }elseif (IOT_CAT_ID == $PO['cat_id']) {
        $num_scanned = $QPo->count_imported_iot($PO['sn']);
        $this->view->num_scanned = $num_scanned;
    }

    if ($this->getRequest()->getMethod() == 'POST'){

        //prevent complete
        if ( in_array($PO['cat_id'], array(PHONE_CAT_ID, DIGITAL_CAT_ID, ILIKE_CAT_ID, IOT_CAT_ID)) and $num_scanned < $PO['num'] ) {
            $flashMessenger->setNamespace('error')->addMessage('Number in storage is less than the number in purchase order. Please add Serial Number to complete.');

            $this->_redirect(HOST."warehouse/po-confirm?id=".$id);
        }


        $userStorage = Zend_Auth::getInstance()->getStorage()->read();


        $data = array(
            'mysql_user' => $userStorage->id,
            'mysql_time' => date('Y-m-d H:i:s'),
        );

        $QPo->update($data, $where);

        //update to warehouse_product
        if ( ACCESS_CAT_ID == $PO['cat_id'] ){
            $QWarehouseProduct = new Application_Model_WarehouseProduct();
            $where = array();
            $where[] = $QWarehouseProduct->getAdapter()->quoteInto('cat_id = ?', $PO['cat_id']);
            $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_id = ?', $PO['good_id']);
            $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_color = ?', $PO['good_color']);
            $where[] = $QWarehouseProduct->getAdapter()->quoteInto('warehouse_id = ?', $PO['warehouse_id']);

            $result = $QWarehouseProduct->fetchRow($where);
            if ($result){
                $where = $QWarehouseProduct->getAdapter()->quoteInto('id = ?', $result['id']);
                $data = array(
                    'quantity' => $result['quantity'] + $PO['num']
                );

                $QWarehouseProduct->update($data, $where);
            } else {
                $data = array(
                    'quantity' => $PO['num'],
                    'cat_id' => $PO['cat_id'],
                    'good_id' => $PO['good_id'],
                    'good_color' => $PO['good_color'],
                    'warehouse_id' => $PO['warehouse_id'],
                );
                $QWarehouseProduct->insert($data);
            }
        }

        //todo log
        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        $info = 'Verify: Enter warehouse order number: '.$PO->sn;

        $QLog = new Application_Model_Log();

        $QLog->insert( array (
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => date('Y-m-d H:i:s'),
        ) );

        $flashMessenger->setNamespace('success')->addMessage('Done!');


        //commit
        $db->commit();

        $this->_redirect($back_url);
    }

} catch (Exception $e) {

    $db->rollback();

    $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');

    $this->_redirect($back_url);
}

$rowset = $QPo->find($id);
$PO = $rowset->current();

$num_imei = $QPo->count_imported_imei($PO['sn']);
$this->view->num_imei = $num_imei;

$this->view->PO = $PO;

$this->view->category = isset($categories[$PO->cat_id]) ? $categories[$PO->cat_id] : '';

//get goods
$QGood = new Application_Model_Good();
$goods = $QGood->get_cache();

$this->view->good = isset($goods[$PO->good_id]) ? $goods[$PO->good_id] : '';

//get goods color
$QGoodColor = new Application_Model_GoodColor();
$goodColors = $QGoodColor->get_cache();

$this->view->good_color = isset($goodColors[$PO->good_color]) ? $goodColors[$PO->good_color] : '';

//get goods color
$QWarehouse = new Application_Model_Warehouse();
$warehouse = $QWarehouse->get_cache();

$this->view->warehouse = isset($warehouse[$PO->warehouse_id]) ? $warehouse[$PO->warehouse_id] : '';

//get username
$QStaff = new Application_Model_Staff();

$staffs = $QStaff->get_cache();

$this->view->created_by_name = isset($staffs[$PO->created_by]) ? $staffs[$PO->created_by] : '';

$this->view->payer_name = isset($staffs[$PO->flow]) ? $staffs[$PO->flow] : '';

$this->view->warehousing_name = isset($staffs[$PO->mysql_user]) ? $staffs[$PO->mysql_user] : '';


$this->view->warning = $flashMessenger->setNamespace('warning')->getMessages();

$this->view->error = $flashMessenger->setNamespace('error')->getMessages();

//back url
$this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER');