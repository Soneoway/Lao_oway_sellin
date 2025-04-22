<?php

set_time_limit(0);

$this->_helper->layout()->disableLayout(true);
$this->_helper->viewRenderer->setNoRender(true);

date_default_timezone_set("Asia/Bangkok");

$back_url = HOST . 'warehouse/check-warehouse';


try {
    $db = Zend_Registry::get('db');
    if ($this->getRequest()->getMethod() == 'POST') {
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $getimei = $this->getRequest()->getParam('imei');
        $warehouse_id = $this->getRequest()->getParam('warehouse_id');
        $good_id = $this->getRequest()->getParam('good_id');
        $color_id = $this->getRequest()->getParam('color_id');

        $type = $this->getRequest()->getParam('type');

        $line = $this->getRequest()->getParam('line');

        if(!in_array($type, ['normal','qr','normal_new','qr_new'])){
            echo json_encode(['status' => 400, 'message' => 'Error Data']);
            exit();
        }


        if (!$getimei) {
            echo json_encode(['status' => 400, 'message' => 'No IMEI Input.']);
            exit();
        }
        $imei_list = trim($getimei);
        $imei_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $imei_list);
        $imei_list = explode("\n", $imei_list);
        $imei_list = array_filter($imei_list);
        if (!($imei_list and is_array($imei_list))) {
            echo json_encode(['status' => 400, 'message' => 'No IMEI Input.']);
            exit();
        }
        if (count(array_unique($imei_list)) < count($imei_list)) {
            echo json_encode(['status' => 400, 'message' => 'Duplicated IMEIs']);
            exit();
        }

        foreach ($imei_list as $imei) {
            // check format
            if (!preg_match('/^[0-9]{15}$/', $imei)) {
                $a_imei_errors[$imei] = array(
                    'sn' => $imei,
                    'error' => 'Wrong Format',
                );
                continue;
            }

            $QImei = new Application_Model_Imei();

            // check co nam trong bang IMEI
            $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
            $imei_info = $QImei->fetchRow($where);
            if (!$imei_info) {
                $a_imei_errors[$imei] = array(
                    'sn' => $imei,
                    'error' => 'Not found',
                );
                continue;
            }

            // check co nam trong kho nay
            if ($imei_info['warehouse_id'] != $warehouse_id) {
                $a_imei_errors[$imei] = array(
                    'sn' => $imei,
                    'error' => 'Not In WH',
                );
                continue;
            }

            if ($imei_info['good_id'] != $good_id) {
                $a_imei_errors[$imei] = array(
                    'sn' => $imei,
                    'error' => 'Not In Product',
                );
                continue;
            }

            if ($imei_info['good_color'] != $color_id) {
                $a_imei_errors[$imei] = array(
                    'sn' => $imei,
                    'error' => 'Not In Color',
                );
                continue;
            }

            // check co xuat kho chua
            if ($imei_info['out_date']) {
                $a_imei_errors[$imei] = array(
                    'sn' => $imei,
                    'error' => 'Exported',
                );
                continue;
            }
            // check status
            if ($imei_info['status'] != 1) {
                $a_imei_errors[$imei] = array(
                    'sn' => $imei,
                    'error' => 'Unable to export',
                );
                continue;
            }

            $a_imei_success[] = [$imei];
        }

        switch ($type) {
            case 'normal':
                if ($a_imei_errors) {
                    foreach ($a_imei_errors as $v) {
                        echo json_encode(['status' => 400, 'message' => ' | ' . $v['error']]);
                        exit();
                    }
                }else{
                    echo json_encode(['status' => 200, 'message' => 'Success']);
                    exit();
                }
                break;

            case 'qr':
                $returnData = [];
                $returnData['success'] = [];
                $returnData['error'] = [];

                if ($a_imei_success) {
                    $returnData['success'] = $a_imei_success;
                }
                if ($a_imei_errors) {
                    foreach ($a_imei_errors as $v) {
                        array_push($returnData['error'], $v['sn'] . ' | ' . $v['error']);
                    }
                }

                echo json_encode(['status' => 200, 'message' => 'Success', 'data' => $returnData]);
                break;

            case 'normal_new':

                if($line < 1){
                    echo json_encode(['status' => 400, 'message' => ' | Error line']);
                        exit();
                }

                if ($a_imei_errors) {
                    foreach ($a_imei_errors as $v) {
                        echo json_encode(['status' => 400, 'message' => ' | ' . $v['error']]);
                        exit();
                    }
                }else{

                    try {

                        $db = Zend_Registry::get('db');

                        $db->beginTransaction();

                        $QCWS = new Application_Model_CheckWarehouseScan();

                        $getImeiLineScan = $QCWS->getImeiLineScanBy($line, $imei_list[0]);

                        if($getImeiLineScan){
                            echo json_encode(['status' => 400, 'message' => ' | Scanned']);
                            exit();
                        }

                        $data = array(
                            'line' => $line,
                            'imei' => $imei_list[0],
                            'create_date' => date('Y-m-d H:i:s'),
                            'create_by' => $userStorage->id,
                            'status' => 1,
                        );

                        $QCWS->insert($data);

                        $db->commit();

                    } catch (Exception $e) {

                        $db->rollback();

                        echo json_encode(['status' => 400, 'message' => ' | This Imei Passed the check but can not add to DB please contact team IT']);
                        exit();

                    }

                    echo json_encode(['status' => 200, 'message' => 'Success']);
                    exit();
                }
                break;

            case 'qr_new':
                $returnData = [];
                $returnData['success'] = [];
                $returnData['error'] = [];

                if ($a_imei_success) {
                    $returnData['success'] = $a_imei_success;
                }
                if ($a_imei_errors) {
                    foreach ($a_imei_errors as $v) {
                        array_push($returnData['error'], $v['sn'] . ' | ' . $v['error']);
                    }
                }

                try {

                        $db = Zend_Registry::get('db');

                        $db->beginTransaction();

                        $QCWS = new Application_Model_CheckWarehouseScan();

                        $temp_success = $returnData['success'];

                        foreach ($returnData['success'] as $key => $value) {

                            $getImeiLineScan = $QCWS->getImeiLineScanBy($line, $value[0]);

                            if($getImeiLineScan){
                                unset($returnData['success'][$key]);
                                array_push($returnData['error'], $value[0] . ' | Scanned');

                            }else{

                                $data = array(
                                    'line' => $line,
                                    'imei' => $value[0],
                                    'create_date' => date('Y-m-d H:i:s'),
                                    'create_by' => $userStorage->id,
                                    'status' => 1,
                                );

                                $QCWS->insert($data);

                            }

                        }

                        $db->commit();

                    } catch (Exception $e) {

                        $db->rollback();

                        echo json_encode(['status' => 400, 'message' => ' | This Imei Passed the check but can not add to DB please contact team IT']);
                        exit();

                    }

                $returnData['success'] = array_values($returnData['success']);
                
                echo json_encode(['status' => 200, 'message' => 'Success', 'data' => $returnData]);
                break;
        }

        // if($type == 'normal'){
        //     if ($a_imei_errors) {
        //         foreach ($a_imei_errors as $v) {
        //             echo json_encode(['status' => 400, 'message' => ' | ' . $v['error']]);
        //             exit();
        //         }
        //     }else{
        //         echo json_encode(['status' => 200, 'message' => 'Success']);
        //         exit();
        //     }
        // }

        // if($type == 'qr'){

        //     $returnData = [];
        //     $returnData['success'] = [];
        //     $returnData['error'] = [];

        //     if ($a_imei_success) {
        //         $returnData['success'] = $a_imei_success;
        //     }
        //     if ($a_imei_errors) {
        //         foreach ($a_imei_errors as $v) {
        //             array_push($returnData['error'], $v['sn'] . ' | ' . $v['error']);
        //         }
        //     }

        //     echo json_encode(['status' => 200, 'message' => 'Success', 'data' => $returnData]);
        // }
                

    }

}catch(exception $e) {
    $flashMessenger = $this->_helper->flashMessenger;
    $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
    echo json_encode(['status' => 400, 'message' => $flashMessenge]);
    exit();
}

?>