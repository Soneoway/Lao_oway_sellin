<?php

    $flashMessenger = $this->_helper->flashMessenger;

    $this->view->errorInput = null;
    $this->view->errorInputLineName = null;
    $this->view->errorInputWarehouse = null;
    $this->view->errorInputProduct = null;
    $this->view->errorInputColor = null;

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $QWarehouse = new Application_Model_Warehouse();
    //$this->view->warehouse = $QWarehouse->fetchAll(null, 'name');

    $where_wh = array();
//$warehouse_type = $userStorage->warehouse_type;
//$where_wh[] = $QWarehouse->getAdapter()->quoteInto('warehouse_type IN ('.$warehouse_type.')', null);
    if (My_Staff_Group::inGroup($userStorage->group_id, array(KERRY_STAFF,KERRY_LEADER))){
        $where_wh[] = $QWarehouse->getAdapter()->quoteInto('show_kerry = ? ', 1);
    }
    $this->view->warehouse = $QWarehouse->fetchAll($where_wh, 'name');


    $QGood = new Application_Model_Good();
    $where = $QGood->getAdapter()->quoteInto('cat_id = ?', 11);

    $this->view->goods = $QGood->fetchAll($where, 'name');

    // $QColor = new Application_Model_GoodColor();
    // $this->view->colors = $QColor->fetchAll(null, 'name');

    $QCWL = new Application_Model_CheckWarehouseLine();
    $this->view->line = $QCWL->getLineDetailsAll();

    $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();

    $this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();

    if ($this->getRequest()->getMethod() == 'POST'){

        try {

            $db = Zend_Registry::get('db');

            $db->beginTransaction();

            $new_line_name = $this->getRequest()->getParam('new_line_name');
            $warehouse_id = $this->getRequest()->getParam('warehouse_id');
            $good_id = $this->getRequest()->getParam('good_id');
            $color_id = $this->getRequest()->getParam('color_id');

            if($new_line_name == '' || $warehouse_id == '' || $good_id == '' || $color_id == ''){
                $this->view->errorInput = 'Wrong input,Please check input creare new line.';

                $this->view->errorInputLineName = $new_line_name;
                $this->view->errorInputWarehouse = $warehouse_id;
                $this->view->errorInputProduct = $good_id;
                $this->view->errorInputColor = $color_id;
                return;
            }

            $QCWL = new Application_Model_CheckWarehouseLine();

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $checkLine = $QCWL->getLineDetailsByLineName($new_line_name);

            if($checkLine){
                $this->view->errorInput = 'Can not save, Duplicate line name.';

                $this->view->errorInputLineName = $new_line_name;
                $this->view->errorInputWarehouse = $warehouse_id;
                $this->view->errorInputProduct = $good_id;
                $this->view->errorInputColor = $color_id;
                return;
            }

            $checkWarehouseProductColor = $QCWL->getLineDetailsByWarehouseProductColor($warehouse_id,$good_id,$color_id);

            if($checkWarehouseProductColor){

                $dulicateLineName = '';

                foreach ($checkWarehouseProductColor as $key => $value) {

                    if($key == 0){
                        $dulicateLineName = $value['line_name'];
                    }else{
                        $dulicateLineName = $dulicateLineName + ',' + $value['line_name'];
                    }
                   
                }

                $this->view->errorInput = 'Can not save, Duplicate Details checking in line name : ' . $dulicateLineName;

                $this->view->errorInputLineName = $new_line_name;
                $this->view->errorInputWarehouse = $warehouse_id;
                $this->view->errorInputProduct = $good_id;
                $this->view->errorInputColor = $color_id;
                return;
            }

            $data = array(
                'line_name' => $new_line_name,
                'warehouse_id' => $warehouse_id,
                'good_id' => $good_id,
                'good_color_id' => $color_id,
                'create_by' => $userStorage->id,
                'create_date' => date('Y-m-d H:i:s'),
                'status' => 1,
            );

            $QCWL->insert($data);

            $db->commit();

            $flashMessenger->setNamespace('success')->addMessage('Done!');

            $this->_redirect(HOST."warehouse/check-warehouse-create-line");

        } catch (Exception $e) {

            $db->rollback();

            $flashMessenger->setNamespace('error')->addMessage('Cannot create new line, please try again! | ' . $e->getMessage());

            $this->_redirect(HOST."warehouse/check-warehouse-create-line");
        }

    }