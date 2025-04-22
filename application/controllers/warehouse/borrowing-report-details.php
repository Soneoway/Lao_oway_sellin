<?php

    $flashMessenger = $this->_helper->flashMessenger;

    $id = $this->getRequest()->getParam('id');

    if(!$id){
    
        $flashMessenger->setNamespace('error')->addMessage('Invalid Data!');
        $this->_redirect(HOST."warehouse/request-list");
    }

    $QBL = new Application_Model_BorrowingList();

    $getDetailsBorrowing = $QBL->getDetailsBorrowingByID($id);

    if(!$getDetailsBorrowing){
        $flashMessenger->setNamespace('error')->addMessage('Not Find!');
        $this->_redirect(HOST."warehouse/request-list");
    }

    $getDetailsReport = $QBL->getItemByReturn($id, true, $getDetailsBorrowing['wms_return_date']);

    if(!$getDetailsReport){
        $flashMessenger->setNamespace('error')->addMessage('Not Find!');
        $this->_redirect(HOST."warehouse/request-list");
    }

    $QBT = new Application_Model_BorrowingTran();

    $getImeiBorrowing = $QBT->getImeiBorrowing($id);

    $arrImeiBorrowing = [];

    foreach ($getDetailsReport as $key => $value) {
        $buckket = [];
        foreach ($getImeiBorrowing as $key_sub) {
            if($value['good_id'] == $key_sub['good_id'] && $value['good_color_id'] == $key_sub['good_color']){
                array_push($buckket, $key_sub);
            }
        }

        $arrImeiBorrowing[$key] = $buckket;
    }

    $QIM = new Application_Model_ImeiMissing();

    $getImeiMissing = $QIM->getImeiMissingByRQID($id);
    $arrayImeiMissing = [];

    foreach ($getImeiMissing as $key) {
        array_push($arrayImeiMissing, $key['imei_sn']);
    }

    $this->view->imeiMissing = $arrayImeiMissing;

    $this->view->imei = $arrImeiBorrowing;

    $QWarehouse = new Application_Model_Warehouse();

    $this->view->warehouse_cache = $QWarehouse->get_cache();

    $this->view->getDetailsReport = $getDetailsReport;

    $this->view->id = $id;

    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;