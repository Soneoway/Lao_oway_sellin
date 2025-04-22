<?php

    $flashMessenger = $this->_helper->flashMessenger;

    $line = $this->getRequest()->getParam('line');

    if(!$line){
    
        $flashMessenger->setNamespace('error')->addMessage('Please select line name.');

        $this->_redirect(HOST."warehouse/check-warehouse-create-line");
    }

    $QCWL = new Application_Model_CheckWarehouseLine();

    $getDetailsLine = $QCWL->getDetailsByLine($line);

    if(!$getDetailsLine){
        $flashMessenger->setNamespace('error')->addMessage('Not find line name.');

        $this->_redirect(HOST."warehouse/check-warehouse-create-line");
    }

    $this->view->getDetailsLine = $getDetailsLine;

    $this->view->totalStorage = $QCWL->getTotalStorage($getDetailsLine['warehouse_id'],$getDetailsLine['good_id'],$getDetailsLine['good_color_id']);

    $this->view->line = $line;