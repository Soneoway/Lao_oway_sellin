<?php
$id = $this->getRequest()->getParam('id');
$flashMessenger = $this->_helper->flashMessenger;
$back_url = $this->getRequest()->getParam('back_url');

if ($id) {
    $QPo = new Application_Model_Po();

    $rowset = $QPo->find($id);
    $PO = $rowset->current();

    if ($PO) {
        $QGoodCategory = new Application_Model_GoodCategory();

        $where = $QPo->getAdapter()->quoteInto('id = ?', $id);
        $PO = $QPo->fetchRow($where);

        if ( in_array( $PO['cat_id'], array( DIGITAL_CAT_ID, PHONE_CAT_ID , ILIKE_CAT_ID, IOT_CAT_ID ) ) ) {

            $num_scanned = 0;

            if (PHONE_CAT_ID == $PO['cat_id']) {
                $num_scanned = $QPo->count_imported_imei($PO['sn']);
                
            } elseif (DIGITAL_CAT_ID == $PO['cat_id']) {
                $num_scanned = $QPo->count_imported_digitalsn($PO['sn']);

            } elseif (ILIKE_CAT_ID == $PO['cat_id']) {

                $num_scanned = $QPo->count_imported_sn($PO['sn']);

            } elseif (IOT_CAT_ID == $PO['cat_id']) {
                $num_scanned = $QPo->count_imported_iot($PO['sn']);
            }

            if ($PO['num'] == $num_scanned) {
                $flashMessenger->setNamespace('error')->addMessage('Enough product in storage.');
                $this->_redirect(HOST."warehouse/in");
            }

            $this->view->num_scanned = $num_scanned;
        } else {
            $flashMessenger->setNamespace('error')->addMessage('Wrong goods type! Cannot add IMEI for Accessories.');
            $this->_redirect(HOST."warehouse/in");
        }

            

        $this->view->PO = $PO;

        $QGoodCategory = new Application_Model_GoodCategory();
        $this->view->good_categories = $QGoodCategory->get_cache();

        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $this->view->goods_cache = $QGood->get_cache();
        $this->view->good_colors_cache = $QGoodColor->get_cache();

        //get username
        $QStaff = new Application_Model_Staff();

        $staffs = $QStaff->get_cache();

        $this->view->created_by_name = isset($staffs[$PO->created_by]) ? $staffs[$PO->created_by] : '';

        $this->view->payer_name = isset($staffs[$PO->flow]) ? $staffs[$PO->flow] : '';

        $this->view->warehousing_name = isset($staffs[$PO->mysql_user]) ? $staffs[$PO->mysql_user] : '';

        //back url
        $this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER');
    } else {
        $flashMessenger->setNamespace('error')->addMessage('Wrong Order ID!');
        $this->_redirect(HOST."warehouse/in");
    }
} else {
    $flashMessenger->setNamespace('error')->addMessage('Wrong Action!');
    $this->_redirect(HOST."warehouse/in");
}