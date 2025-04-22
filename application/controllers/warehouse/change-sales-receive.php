<?php

set_time_limit(0);
ini_set('memory_limit', -1);

//get list distributor
$QDistributor = new Application_Model_Distributor();
$distributors = $QDistributor->get_cache();

//get list warehouse
$QWarehouse = new Application_Model_Warehouse();
$warehouses = $QWarehouse->get_cache();

$QGood = new Application_Model_Good();
$where = $QGood->getAdapter()->quoteInto('cat_id = ?', ACCESS_CAT_ID);
$accessories = $QGood->get_cache();
$this->view->accessories = $QGood->fetchAll($where, 'name');

$this->view->distributors = $distributors;
$this->view->warehouses = $warehouses;
$goods_cached = $QGood->get_cache();
$this->view->goods_cached = $goods_cached;

$QGoodColor = new Application_Model_GoodColor();
$good_colors_cached = $QGoodColor->get_cache();
$this->view->good_colors_cached = $good_colors_cached;

$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->fetchAll();

$flashMessenger = $this->_helper->flashMessenger;

$id = $this->getRequest()->getParam('id');
$QChangeSalesOrder = new Application_Model_ChangeSalesOrder();
$QChangeSalesProduct = new Application_Model_ChangeSalesProduct();
$QChangeSalesImei = new Application_Model_ChangeSalesImei();
$QImei = new Application_Model_Imei();
$QDigitalSn = new Application_Model_DigitalSn();
$QWarehouseProduct = new Application_Model_WarehouseProduct();

$changeSalesOrder = null;
if ($id)
{
    $whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
    $changeSalesOrder = $QChangeSalesOrder->fetchRow($whereChangeSalesOrder);
}

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

//save form
if ($this->getRequest()->isPost())
{

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();

    echo '<link href="/css/bootstrap.min.css" rel="stylesheet">';

    $num_receives = $this->getRequest()->getParam('num_receive');
    $sns_receives = $this->getRequest()->getParam('sns_receive');
    $sns_digital_receives = $this->getRequest()->getParam('sns_digital_receive');
    $sn_iot_receives = $this->getRequest()->getParam('sn_iot_receives');
    $ids = $this->getRequest()->getParam('ids');

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $QLog = new Application_Model_Log();
    $ip = $this->getRequest()->getServer('REMOTE_ADDR');

    // check id
    if ($id)
    {
        if (!$changeSalesOrder)
        {
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Change Order is invalid!</div>';
            exit;
        }

        if (!in_array($changeSalesOrder['status'], array(
            CHANGE_ORDER_STATUS_ON_CHANGE,
            CHANGE_ORDER_STATUS_SCANNED_IN,
            CHANGE_ORDER_STATUS_FULL_RECEIVED,
            CHANGE_ORDER_STATUS_PARTIALLY_RECEIVED)))
        {
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Change Order Status is invalid!</div>';
            exit;
        }
    }

    try
    {
        $db = Zend_Registry::get('db');
        $db->beginTransaction();


        if ($id)
        {

            $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('changed_id = ?',
                $id);
            $changeSalesProducts = $QChangeSalesProduct->fetchAll($whereChangeSalesProduct);
            $receive = array();
            $offset = 0;
            $total_imei = 0;
            $total_iot = 0;
            $total_digital = 0;
            $imei_scan = array();
            $changeSaleTempData = array();
            $changeIotTempData = array();
            $changeSaleDigitalTempData = array();
            $not_receive_array = array();
            $not_iot_receive = array();
            $snProducts = $changeSalesProducts['0']['changed_sn'];

            $fist_warehouse = $changeSalesOrder['old_id'];

            foreach ($changeSalesProducts as $k => $v)
            {

                if ($v['cat_id'] == PHONE_CAT_ID)
                {
                    $total_imei += $v['num'];
                    $changeSaleTempData[$k]['change_id'] = $v['id'];
                    $changeSaleTempData[$k]['good_id'] = $v['good_id'];
                    $changeSaleTempData[$k]['good_color'] = $v['good_color'];
                    $changeSaleTempData[$k]['receive'] = 0;
                }

                //edit khuan
                if ($v['cat_id'] == IOT_CAT_ID) {
                    $total_iot += $v['num'];
                    $changeIotTempData[$k]['change_id'] = $v['id'];
                    $changeIotTempData[$k]['good_id'] = $v['good_id'];
                    $changeIotTempData[$k]['good_color'] = $v['good_color'];
                    $changeIotTempData[$k]['receive'] = 0;

                }
                //end
                //end

                if ($v['cat_id'] == DIGITAL_CAT_ID)
                {
                    $total_digital += $v['num'];
                    $changeSaleDigitalTempData[$k]['change_id'] = $v['id'];
                    $changeSaleDigitalTempData[$k]['good_id'] = $v['good_id'];
                    $changeSaleDigitalTempData[$k]['good_color'] = $v['good_color'];
                    $changeSaleDigitalTempData[$k]['receive'] = 0;

                }

                if ($v['cat_id'] == ACCESS_CAT_ID)
                {
                    $access_id = 1;
                }
            }


            //edit khuan

            if(isset($sn_iot_receives) and $sn_iot_receives)
            {
                 if (!$sn_iot_receives )
                {
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - Please scan SN!</div>';
                    exit;
                }

                $scannedList = $sn_iot_receives;
                $count = 0;
                $scannedList = $scannedList ? preg_replace('/\n+/', ',', $scannedList) : '';
                $scannedList = $scannedList ? array_filter(array_map('trim', explode(",", $scannedList))) :
                    array();
                $scannedList = array_unique($scannedList);

                // check co nam trong list gui di
                $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sn = ?',
                    $snProducts);
                $sentListRecords = $QChangeSalesImei->fetchAll($whereChangeSalesImei);

                if(count($sentListRecords) != count($scannedList)){
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Quantity IMEI Invalid: not in Sent List</div>';

                        exit;
                }

                 $sentList = array();
                foreach ($sentListRecords as $sent)
                {
                    $sentList[] = $sent['imei'];
                }

                foreach ($scannedList as $sn)
                {
                    if (!preg_match('/^[0-9a-zA-Z]{15,22}$/', $sn))
                    {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Invalid SN: ' . $sn . '</div>';

                        exit;
                    }

                    if (!in_array($sn, $sentList))
                    {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Invalid SN: ' . $sn .
                            ' not in Sent List</div>';

                        exit;
                    }

                    $whereImei = array();
                    $whereImei[] = $QImei->getAdapter()->quoteInto('imei_sn = ?', $sn);
                    $whereImei[] = $QImei->getAdapter()->quoteInto('sales_sn is null',1);
                    $imeiInfo = $QImei->fetchRow($whereImei);

                    if (!$imeiInfo)
                    {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Invalid SN: ' . $sn . '</div>';
                        exit;
                    }

                    // check warehouse
                    if($imeiInfo['warehouse_id'] != $fist_warehouse){
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Incorrect Warehouse IMEI: ' . $sn . '</div>';
                        exit;
                    }

                    foreach ($changeIotTempData as $k => $v)
                    {


                        if ($imeiInfo['good_id'] == $v['good_id'] && $imeiInfo['good_color'] == $v['good_color'])
                        {
                            $changeIotTempData[$k]['receive'] = $changeIotTempData[$k]['receive'] + 1;
                            break;
                        }


                    }

                    $data = array('status' => CHANGE_ORDER_IMEI_STATUS_RECEIVED, // ok
                            );
                    $whereChangeSalesImei = array();
                    $whereChangeSalesImei[] = $QChangeSalesImei->getAdapter()->quoteInto('imei = ?',
                        $sn);
                    $whereChangeSalesImei[] = $QChangeSalesImei->getAdapter()->quoteInto('changed_sn = ?',
                        $snProducts);
                    $QChangeSalesImei->update($data, $whereChangeSalesImei);
                } // END of foreach scan list

                // so nhan duoc bang so scan
                $receive['IoT'] = count($scannedList);

                if ($receive['IoT'] < $total_iot)
                {
                    $not_iot_receive[] = 'Phone : Not Receive ' . (intval($total_iot - $receive['IoT']));
                }
            }

            //end
            //end

            // xu ly rieng tung truong hop
            if (isset($sns_receives) and $sns_receives)
            {
                // check scanned co du khong
                if (!isset($sns_receives) or !$sns_receives)
                {
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - Please scan SN!</div>';
                    exit;
                }


                $scannedList = $sns_receives;
                $count = 0;
                $scannedList = $scannedList ? preg_replace('/\n+/', ',', $scannedList) : '';
                $scannedList = $scannedList ? array_filter(array_map('trim', explode(",", $scannedList))) :
                    array();
                $scannedList = array_unique($scannedList);

                // check co nam trong list gui di
                $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sn = ?',
                    $snProducts);
                $sentListRecords = $QChangeSalesImei->fetchAll($whereChangeSalesImei);

                if(count($sentListRecords) != count($scannedList)){
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Quantity IMEI Invalid: not in Sent List</div>';

                        exit;
                }

                $sentList = array();
                foreach ($sentListRecords as $sent)
                {
                    $sentList[] = $sent['imei'];
                }

                foreach ($scannedList as $sn)
                {
                    if (!preg_match('/^[0-9]{15}$/', $sn))
                    {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Invalid SN: ' . $sn . '</div>';

                        exit;
                    }

                    if (!in_array($sn, $sentList))
                    {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Invalid SN: ' . $sn .
                            ' not in Sent List</div>';

                        exit;
                    }

                    $whereImei = array();
                    $whereImei[] = $QImei->getAdapter()->quoteInto('imei_sn = ?', $sn);
                    $whereImei[] = $QImei->getAdapter()->quoteInto('sales_sn is null',1);
                    $imeiInfo = $QImei->fetchRow($whereImei);
                    if (!$imeiInfo)
                    {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Invalid SN: ' . $sn . '</div>';
                        exit;
                    }

                    // check warehouse
                    if($imeiInfo['warehouse_id'] != $fist_warehouse){
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Incorrect Warehouse IMEI: ' . $sn . '</div>';
                        exit;
                    }

                    foreach ($changeSaleTempData as $k => $v)
                    {


                        if ($imeiInfo['good_id'] == $v['good_id'] && $imeiInfo['good_color'] == $v['good_color'])
                        {
                            $changeSaleTempData[$k]['receive'] = $changeSaleTempData[$k]['receive'] + 1;
                            break;
                        }


                    }

                    $data = array('status' => CHANGE_ORDER_IMEI_STATUS_RECEIVED, // ok
                            );
                    $whereChangeSalesImei = array();
                    $whereChangeSalesImei[] = $QChangeSalesImei->getAdapter()->quoteInto('imei = ?',
                        $sn);
                    $whereChangeSalesImei[] = $QChangeSalesImei->getAdapter()->quoteInto('changed_sn = ?',
                        $snProducts);
                    $QChangeSalesImei->update($data, $whereChangeSalesImei);
                } // END of foreach scan list

                // so nhan duoc bang so scan
                $receive['phone'] = count($scannedList);

                if ($receive['phone'] < $total_imei)
                {
                    $not_receive_array[] = 'Phone : Not Receive ' . (intval($total_imei - $receive['phone']));
                }
            }

            // digital
            if (isset($sns_digital_receives) and $sns_digital_receives)
            {
                if (!isset($sns_digital_receives) or !$sns_digital_receives)
                {
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - Please scan digital SN!</div>';
                    exit;
                }


                $scannedList = $sns_digital_receives;
                $scannedList = $scannedList ? preg_replace('/\n+/', ',', $scannedList) : '';
                $scannedList = $scannedList ? array_filter(array_map('trim', explode(",", $scannedList))) :
                    array();


                // check co nam trong list gui di
                $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sn = ?',
                    $snProducts);
                $sentListRecords = $QChangeSalesImei->fetchAll($whereChangeSalesImei);
                $sentList = array();
                foreach ($sentListRecords as $sent)
                {
                    $sentList[] = $sent['imei'];
                }

                foreach ($scannedList as $sn)
                {
                    if (!preg_match('/^[0-9a-zA-Z]{16}$/', $sn))
                    {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Invalid digital SN: ' . $sn .
                            '</div>';

                        exit;
                    }

                    if (!in_array($sn, $sentList))
                    {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Invalid digital SN: ' . $sn .
                            ' not in Sent List</div>';

                        exit;
                    }

                    $whereDigitalSn = $QDigitalSn->getAdapter()->quoteInto('sn = ?', $sn);
                    $Info = $QDigitalSn->fetchRow($whereDigitalSn);
                    if (!$Info)
                    {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - Invalid digital SN: ' . $sn .
                            '</div>';

                        exit;
                    }

                    foreach ($changeSaleDigitalTempData as $k => $v)
                    {
                        if ($Info['good_id'] == $v['good_id'] && $Info['good_color'] == $v['good_color'])
                        {
                            $changeSaleDigitalTempData[$k]['receive'] = $changeSaleDigitalTempData[$k]['receive'] +
                                1;

                            break;
                        }
                    }


                    if ($Info['out_date'])
                    {
                        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                        echo '<div class="alert alert-error">Failed - digital number was OUT: ' . $sn .
                            '</div>';

                        exit;
                    }


                    $data = array('status' => CHANGE_ORDER_IMEI_STATUS_RECEIVED, // ok
                            );
                    $whereChangeSalesImei = array();
                    $whereChangeSalesImei[] = $QChangeSalesImei->getAdapter()->quoteInto('imei = ?',
                        $sn);
                    $whereChangeSalesImei[] = $QChangeSalesImei->getAdapter()->quoteInto('changed_sn = ?',
                        $snProducts);
                    $QChangeSalesImei->update($data, $whereChangeSalesImei);
                } // END of foreach scan list

                // so nhan duoc bang so scan
                $receive['digital'] = count($scannedList);

                if ($receive['digital'] < $total_digital)
                {
                    $not_receive_array[] = 'Digital : Not Receive ' . (intval($total_digital - $receive['digital']));
                }
            }

            //cap nhat so imei dt nhan duoc
            if (isset($changeSaleTempData) and $changeSaleTempData)
            {
                foreach ($changeSaleTempData as $k => $v)
                {
                    if (isset($v['change_id']) and $v['change_id'])
                    {
                        $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('id = ?',
                            $v['change_id']);
                        $QChangeSalesProduct->update(array('receive' => $v['receive'], ), $whereChangeSalesProduct);
                    }
                }

            }

             //edit khuan

            if(isset($changeIotTempData) and $changeIotTempData)
            {
                foreach ($changeIotTempData as $k => $v) {
                   if(isset($v['change_id']) and $v['change_id'])
                   {
                    $whereChangeSalesProductiot = $QChangeSalesProduct->getAdapter()->quoteInto('id =?',$v['change_id']);
                    $QChangeSalesProduct->update(array('receive' => $v['receive'],), $whereChangeSalesProductiot);
                   }
                }
            }
            //end
            //end

            //cap nhat so imei digital nhan duoc
            if (isset($changeSaleDigitalTempData) and $changeSaleDigitalTempData)
            {
                foreach ($changeSaleDigitalTempData as $k => $v)
                {

                    if (isset($v['change_id']) and $v['change_id'])
                    {
                        $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('id = ?',
                            $v['change_id']);
                        $QChangeSalesProduct->update(array('receive' => $v['receive'], ), $whereChangeSalesProduct);
                    }

                }
            }

            //cap nhat so phu kien nhan duoc
            if (isset($access_id) and $access_id)
            {


                foreach ($changeSalesProducts as $item)
                {
                    if ($item['cat_id'] == ACCESS_CAT_ID)
                    {

                        if (!isset($num_receives[$item['id']]) or !is_numeric($num_receives[$item['id']]) or
                            $num_receives[$item['id']] < 0 or $num_receives[$item['id']] > $item['num'])
                        {

                            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                            echo '<div class="alert alert-error">Failed - Please choose correct value!</div>';
                            exit;
                        }

                        if (!isset($num_receives[$item['id']]) or !is_numeric($num_receives[$item['id']]) or
                            $num_receives[$item['id']] < 0 or $num_receives[$item['id']] < $item['num'])
                        {

                            $not_receive_array[] = 'Accessories : < ' . $accessories[$item['good_id']] .
                                ' > Not Receive ' . (intval($item['num'] - $num_receives[$item['id']]));
                        }

                        $receive = $num_receives[$item['id']];
                        $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('id = ?',
                            $item['id']);
                        $QChangeSalesProduct->update(array('receive' => $receive, ), $whereChangeSalesProduct);
                    }
                }


            }
            //    // thong bao
            //            $width = isset($not_receive_array) ? (40 * count($not_receive_array)).'px' : '40px';
            //            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            //            echo '<script>window.parent.document.getElementById("iframe").height = '.$width.';</script>';
            //            foreach ($not_receive_array as $k => $v) {
            //                echo '<div class="alert alert-error">- ' . $v . '</div>';
            //            }
            //            exit;

            // update status don hang
            $whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);

            $data = array(
                'status' => CHANGE_ORDER_STATUS_FULL_RECEIVED,
                'scanned_in_at' => date('Y-m-d H:i:s'),
                'scanned_in_by' => $userStorage->id,
                );

            if (isset($not_receive_array) and $not_receive_array)
            {
                $data['status'] = CHANGE_ORDER_STATUS_PARTIALLY_RECEIVED;
            }

            $QChangeSalesOrder->update($data, $whereChangeSalesOrder);


            // c?p nh?t s? lu?ng t?n kho

            foreach ($changeSalesProducts as $item)
            {

                // xu ly rieng tung truong hop
                if ($item['cat_id'] == PHONE_CAT_ID)
                {

                    $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?',
                        $item['id']);
                    $sentListRecords = $QChangeSalesImei->fetchAll($whereChangeSalesImei);
                    foreach ($sentListRecords as $sent)
                    {
                        if ($sent['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                        {
                            $data = array('status' => IMEI_STATUS_OK, );

                            if ($changeSalesOrder['is_changed_wh'])
                                $data['warehouse_id'] = $changeSalesOrder['new_id'];
                            else
                                $data['distributor_id'] = $changeSalesOrder['new_id'];

                            $whereImei = $QImei->getAdapter()->quoteInto('imei_sn = ?', $sent['imei']);

                            $QImei->update($data, $whereImei);
                        } else
                        {
                            $data = array('status' => IMEI_STATUS_LOST, );
                            $whereImei = $QImei->getAdapter()->quoteInto('imei_sn = ?', $sent['imei']);

                            $QImei->update($data, $whereImei);
                        }
                    }
                }

                //khuan Add IoT
                elseif ($item['cat_id'] == IOT_CAT_ID) {

                     $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?',
                        $item['id']);
                    $sentListRecords = $QChangeSalesImei->fetchAll($whereChangeSalesImei);
                    foreach ($sentListRecords as $sent)
                    {
                        if ($sent['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                        {
                            $data = array('status' => IMEI_STATUS_OK, );

                            if ($changeSalesOrder['is_changed_wh'])
                                $data['warehouse_id'] = $changeSalesOrder['new_id'];
                            else
                                $data['distributor_id'] = $changeSalesOrder['new_id'];

                            $whereImei = $QImei->getAdapter()->quoteInto('imei_sn = ?', $sent['imei']);

                            $QImei->update($data, $whereImei);
                        } else
                        {
                            $data = array('status' => IMEI_STATUS_LOST, );
                            $whereImei = $QImei->getAdapter()->quoteInto('imei_sn = ?', $sent['imei']);

                            $QImei->update($data, $whereImei);
                        }
                    }
                }
            //end
                //end


                // digital
                elseif ($item['cat_id'] == DIGITAL_CAT_ID)
                {

                    // check co nam trong list gui di
                    $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?',
                        $item['id']);
                    $sentListRecords = $QChangeSalesImei->fetchAll($whereChangeSalesImei);
                    foreach ($sentListRecords as $sent)
                    {
                        if ($sent['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                        {
                            $data = array('status' => IMEI_STATUS_OK, );

                            if ($changeSalesOrder['is_changed_wh'])
                                $data['warehouse_id'] = $changeSalesOrder['new_id'];
                            else
                                $data['distributor_id'] = $changeSalesOrder['new_id'];

                            $whereDigitalSn = $QDigitalSn->getAdapter()->quoteInto('sn = ?', $sent['imei']);

                            $QDigitalSn->update($data, $whereDigitalSn);
                        } else
                        {
                            $data = array('status' => IMEI_STATUS_LOST, );
                            $whereDigitalSn = $QDigitalSn->getAdapter()->quoteInto('sn = ?', $sent['imei']);

                            $QDigitalSn->update($data, $whereDigitalSn);
                        }
                    }

                }

                // accessories
                else
                {
                    // insert Warehouse product
                    // d?i v?i TH phu kien thi check them da nhan du hang hay chua

                    if ($item['status'] == 1 && $num_receives[$item['id']] != 0)
                    {
                        $where = array();
                        $where[] = $QWarehouseProduct->getAdapter()->quoteInto('cat_id = ?', $item['cat_id']);
                        $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_id = ?', $item['good_id']);
                        $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_color = ?', $item['good_color']);
                        $where[] = $QWarehouseProduct->getAdapter()->quoteInto('warehouse_id = ?', $changeSalesOrder['new_id']);

                        $result = $QWarehouseProduct->fetchRow($where);


                        if ($item['num'] == $num_receives[$item['id']])
                        {
                            $data = array('status' => '2');
                            $whereChangeSalesProduct = array();
                            $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('id = ? ',
                                $item['id']);
                            $QChangeSalesProduct->update($data, $whereChangeSalesProduct);
                        }


                        if ($result)
                        {

                            $where = $QWarehouseProduct->getAdapter()->quoteInto('id = ?', $result['id']);
                          
                           
                                $quantity = $result['quantity'] + $num_receives[$item['id']] - $item['receive'];
                            //update quantity
                            $QWarehouseProduct->update(array('quantity' => ($quantity)), $where);

                            //update lai status don hang


                        } else
                        {
                            //insert quantity
                            $QWarehouseProduct->insert(array(
                                'cat_id' => $item['cat_id'],
                                'good_id' => $item['good_id'],
                                'good_color' => $item['good_color'],
                                'warehouse_id' => $changeSalesOrder['new_id'],
                                'quantity' => $item['num'],
                                ));
                        }
                    }
                }

            }

        }

        $info = 'CHANGE ORDER - SCANNED IN: ' . $changeSalesOrder['changed_sn'];
        $QLog->insert(array(
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => date('Y-m-d H:i:s'),
            ));

        $db->commit();

        $flashMessenger->setNamespace('success')->addMessage('Done!');

        // echo '<script>parent.location.href="/warehouse/change-sales-list"</script>';
        sleep(3);
        echo '<script>parent.location.href="/warehouse/change-sales-complete?id='.$id.'"</script>';
        exit;

    }
    catch (exception $e)
    {
        $db->rollback();

        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
        echo '<div class="alert alert-error">Failed - ' . $e->getMessage() . '</div>';
        exit;
    }

    //update s? lu?ng t?n kho khi scan imei


}

if ($id)
{
    if (!$changeSalesOrder)
    {
        $flashMessenger->setNamespace('error')->addMessage('Change Order is invalid!');
        $this->redirect('/warehouse/change-sales-list');
    }

    if (!in_array($changeSalesOrder['status'], array(
        CHANGE_ORDER_STATUS_ON_CHANGE,
        CHANGE_ORDER_STATUS_SCANNED_IN,
        CHANGE_ORDER_STATUS_FULL_RECEIVED,
        CHANGE_ORDER_STATUS_PARTIALLY_RECEIVED)))
    {
        $flashMessenger->setNamespace('error')->addMessage('Status is invalid!');
        $this->redirect('/warehouse/change-sales-list');
    }
}

if ($id)
{
    $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('changed_id = ?',
        $id);
    $changeSalesProduct = $QChangeSalesProduct->fetchAll($whereChangeSalesProduct);

    $changeSalesImeisList = $changeSalesIotList = $changeSalesImeisReceivedList = $changeSalesImeisLostList = $changeSalesIotImeisReceivedList = $changeSalesIotImeisLostList =
        array();

    $changeSalesImeisDigitalList = $changeSalesImeisReceivedDigitalList = $changeSalesImeisLostDigitalList =
        array();

    if ($changeSalesProduct->count())
    {

        foreach ($changeSalesProduct as $item)
        {
            $whereChangeSalesImei = $QChangeSalesImei->getAdapter()->quoteInto('changed_sales_product_id = ?',
                $item['id']);
            $changeSalesImeis = $QChangeSalesImei->fetchAll($whereChangeSalesImei);


            if ($changeSalesImeis->count())
            {
                foreach ($changeSalesImeis as $im)
                {
                    if ($im['cat_id'] == PHONE_CAT_ID)
                    {
                        if ($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                            $changeSalesImeisReceivedList[] = $im['imei'];
                        else
                            $changeSalesImeisLostList[] = $im['imei'];

                        $changeSalesImeisList[] = $im['imei'];
                    }

                    //edit khuan

                    if($im['cat_id'] == IOT_CAT_ID){
                        if($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                            $changeSalesIotImeisReceivedList[] = $im['imei'];
                        else
                            $changeSalesIotImeisLostList = $im['imei'];

                            $changeSalesIotList[] = $im['imei'];
                    }

                    //end
                    //end

                    if ($im['cat_id'] == DIGITAL_CAT_ID)
                    {
                        if ($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                            $changeSalesImeisReceivedDigitalList[] = $im['imei'];
                        else
                            $changeSalesImeisLostDigitalList[] = $im['imei'];

                        $changeSalesImeisDigitalList[] = $im['imei'];
                    }

                    //d?i v?i tru?ng hop nhung imei cu
                    if (empty($im['cat_id']))
                    {
                        if ($im['status'] == CHANGE_ORDER_IMEI_STATUS_RECEIVED)
                            $changeSalesImeisReceivedList[] = $im['imei'];
                        else
                            $changeSalesImeisLostList[] = $im['imei'];

                        $changeSalesImeisList[] = $im['imei'];
                    }
                }
            }
        }

        $this->view->changeSalesImeisReceivedList = $changeSalesImeisReceivedList;
        $this->view->changeSalesImeisLostList = $changeSalesImeisLostList;
        $this->view->changeSalesImeisList = $changeSalesImeisList;
        $this->view->changeSalesImeisReceivedDigitalList = $changeSalesImeisReceivedDigitalList;
        $this->view->changeSalesImeisLostDigitalList = $changeSalesImeisLostDigitalList;
        $this->view->changeSalesImeisDigitalList = $changeSalesImeisDigitalList;

        $this->view->changeSalesIotList = $changeSalesIotList;
        $this->view->changeSalesIotImeisReceivedList = $changeSalesIotImeisReceivedList;
        $this->view->changeSalesIotImeisLostList = $changeSalesIotImeisLostList;
    }

    $this->view->changeSalesProduct = $changeSalesProduct;
}

$this->view->changeSalesOrder = $changeSalesOrder;

$this->view->disabledFields = array(
    'is_changed_wh',
    'cat_id',
    'good_id',
    'good_color',
    'num',
    'sn_iot',
    'sns',
    'type',
    'num_receive'
    );

$hideFields = array(
    'sns_lost',
    'sns_digital_lost',
    'num_lost',
    'remove-sales',
    );


$this->view->hideFields = $hideFields;
