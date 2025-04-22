<?php

    $warehouse_id  = $this->getRequest()->getParam('warehouse_id','36');

    $QCWL = new Application_Model_CheckWarehouseLine();

    $getLineScannedDetails = $QCWL->getLineScannedDetails($warehouse_id);

    $this->view->arrData = $getLineScannedDetails;

    $QWarehouse = new Application_Model_Warehouse();
    $this->view->warehouse = $QWarehouse->fetchAll(null, 'name');

    $this->view->warehouse_id = $warehouse_id;