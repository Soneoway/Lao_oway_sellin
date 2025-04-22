<?php

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $flashMessenger = $this->_helper->flashMessenger;

    $id = $this->getRequest()->getParam('id');

    if(!$id){
        $flashMessenger->setNamespace('error')->addMessage('Not Find!');
        $this->_redirect(HOST."warehouse/factory-claim-approve-list");
    }

    if ($this->getRequest()->getMethod() == 'POST') {

        $old_imei = $this->getRequest()->getParam('old_imei');
        $new_imei = $this->getRequest()->getParam('new_imei');
        $warehouse = $this->getRequest()->getParam('warehouse');
        $job_number = $this->getRequest()->getParam('job_number');
        $remark = $this->getRequest()->getParam('remark');

        if(!$old_imei){
            $flashMessenger->setNamespace('error')->addMessage('Please input old imei');
            $this->_redirect(HOST."warehouse/factory-claim-approve");
        }

        if(!$new_imei){
            $flashMessenger->setNamespace('error')->addMessage('Please input new imei');
            $this->_redirect(HOST."warehouse/factory-claim-approve");
        }

        if(!$warehouse){
            $flashMessenger->setNamespace('error')->addMessage('Please select warehouse');
            $this->_redirect(HOST."warehouse/factory-claim-approve");
        }

        if(!$job_number){
            $flashMessenger->setNamespace('error')->addMessage('Please input job number');
            $this->_redirect(HOST."warehouse/factory-claim-approve");
        }

        if(!$remark){
            $flashMessenger->setNamespace('error')->addMessage('Please input remark');
            $this->_redirect(HOST."warehouse/factory-claim-approve");
        }

        $QFC = new Application_Model_FactoryClaim();

        $QI = new Application_Model_Imei();
        $check_old_imei = $QI->checkImeiSold($old_imei);
        $check_new_imei = $QI->checkImeiReady($new_imei);

        if(!$check_old_imei){
            $flashMessenger->setNamespace('error')->addMessage('Invalid old imei');
            $this->_redirect(HOST."warehouse/factory-claim-approve");
        }

        if(!$check_new_imei){
            $flashMessenger->setNamespace('error')->addMessage('Invalid new imei');
            $this->_redirect(HOST."warehouse/factory-claim-approve");
        }

        if($check_new_imei['warehouse_id'] != $warehouse){
            $flashMessenger->setNamespace('error')->addMessage('Invalid warehouse');
            $this->_redirect(HOST."warehouse/factory-claim-approve");
        }

        try{

            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            //------------------Return Product
            /* Check Imei Return Product (Group By Distributor) */
            $select_return_by_distributor ="SELECT t.distributor_id,
            t.store_code,
            t.title,
            COUNT(t.imei_sn) AS count_imei,
            sum(t.bgv_price) as sum_bvg_price,
            SUM(t.total_price)AS sum_price
            FROM(
            SELECT   
            i.imei_sn,
            dis.id AS distributor_id,
            dis.store_code,
            dis.title,
            COUNT(bi.imei_sn) AS bvg_num,
            m.sn AS sales_sn,
            m.invoice_number,
            SUM(IFNULL( bi.price ,0)) AS bgv_price,
            ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100)-SUM(IFNULL( bi.price ,0)),2)AS unit_price,
            ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100)-SUM(IFNULL( bi.price ,0)),2)AS total_price,
            m.cat_id,       
            g.id AS good_id,
            c.id AS good_color,
            g.name AS product_name,
            g.desc AS product_detail_name,
            c.name AS product_color
            FROM
            imei AS i 
            LEFT JOIN market AS m ON m.sn = i.sales_sn AND m.good_id = i.good_id AND m.good_color = i.good_color 
            LEFT JOIN bvg_imei bi ON bi.imei_sn=i.imei_sn AND bi.d_id = m.d_id 
            AND bi.invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
            AND bi.good_id = m.good_id
            AND bi.good_color = m.good_color
            LEFT JOIN good AS g ON g.id = i.good_id
            LEFT JOIN good_color AS c  ON c.id = i.good_color  
            LEFT JOIN distributor AS dis  ON dis.id = i.distributor_id  
            WHERE 1=1 
            AND i.imei_sn = ".$old_imei."
            GROUP BY i.distributor_id,i.sales_sn,i.good_id,i.good_color,i.imei_sn
            )t
            GROUP BY t.distributor_id
            ORDER BY t.distributor_id,t.invoice_number";

            /* Check Imei Return Product (Group By Product) */
            $select_return_by_group ="SELECT t.distributor_id,
            t.store_code,
            t.title,
            COUNT(t.imei_sn) AS num,
            t.bgv_price,
            t.unit_price,
            SUM(t.total_price)AS total_price,
            t.sales_sn AS sn,
            t.invoice_number,
            t.cat_id,       
            t.good_id,
            t.good_color,
            t.product_name,
            t.product_detail_name,
            t.product_color
            FROM(
            SELECT   
            i.imei_sn,
            dis.id AS distributor_id,
            dis.store_code,
            dis.title,
            COUNT(bi.imei_sn) AS bvg_num,
            m.sn AS sales_sn,
            m.invoice_number,
            SUM(IFNULL( bi.price ,0)) AS bgv_price,
            ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100)-SUM(IFNULL( bi.price ,0)),2)AS unit_price,
            ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100)-SUM(IFNULL( bi.price ,0)),2)AS total_price,
            m.cat_id,       
            g.id AS good_id,
            c.id AS good_color,
            g.name AS product_name,
            g.desc AS product_detail_name,
            c.name AS product_color
            FROM
            imei AS i 
            LEFT JOIN market AS m ON m.sn = i.sales_sn AND m.good_id = i.good_id AND m.good_color = i.good_color 
            LEFT JOIN bvg_imei bi ON bi.imei_sn=i.imei_sn AND bi.d_id = m.d_id 
            AND bi.invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
            AND bi.good_id = m.good_id 
            AND bi.good_color = m.good_color
            LEFT JOIN good AS g ON g.id = i.good_id
            LEFT JOIN good_color AS c  ON c.id = i.good_color  
            LEFT JOIN distributor AS dis  ON dis.id = i.distributor_id  
            WHERE 1=1 
            AND i.imei_sn = ".$old_imei."
            GROUP BY i.distributor_id,i.sales_sn,i.good_id,i.good_color,i.imei_sn
            )t
            GROUP BY t.distributor_id,t.sales_sn,t.good_id,t.good_color,t.bvg_num
            ORDER BY t.distributor_id,t.invoice_number";

            /* Check Imei Return Product (Group By Imei)*/
            $select_return_by_imei ="SELECT   
            i.imei_sn,
            dis.id AS distributor_id,
            dis.store_code,
            dis.title,
            COUNT(i.imei_sn) AS bvg_num,
            m.sn as sales_sn,
            m.invoice_number,
            SUM(IFNULL( bi.price ,0)) AS bgv_price,
            ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100)-SUM(IFNULL( bi.price ,0)),2)AS sum_unit_price, 
            m.cat_id,       
            g.id AS good_id,
            c.id AS good_color,
            g.name AS product_name,
            g.desc AS product_detail_name,
            c.name AS product_color
            FROM
            imei AS i 
            LEFT JOIN market AS m ON m.sn = i.sales_sn AND m.good_id = i.good_id AND m.good_color = i.good_color 
            LEFT JOIN bvg_imei bi ON bi.imei_sn=i.imei_sn AND bi.d_id = m.d_id 
            AND bi.invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
            AND bi.good_id = m.good_id 
            AND bi.good_color = m.good_color
            LEFT JOIN good AS g ON g.id = i.good_id
            LEFT JOIN good_color AS c  ON c.id = i.good_color  
            LEFT JOIN distributor AS dis  ON dis.id = i.distributor_id  
            WHERE 1=1 
            AND i.imei_sn = ".$old_imei."
            GROUP BY i.distributor_id,i.sales_sn,i.good_id,i.good_color,i.imei_sn
            ORDER BY m.d_id,m.invoice_number";

            $result_return_by_distributor = $db->fetchAll($select_return_by_distributor);

            if(!$result_return_by_distributor){
                $flashMessenger->setNamespace('error')->addMessage('Invalid imei');
                $this->_redirect(HOST."warehouse/factory-claim-approve");
            }

            $result_return_by_group = $db->fetchAll($select_return_by_group);

            if(!$result_return_by_group){
                $flashMessenger->setNamespace('error')->addMessage('Invalid imei');
                $this->_redirect(HOST."warehouse/factory-claim-approve");
            }

            $result_return_by_imei = $db->fetchAll($select_return_by_imei);

            if(!$result_return_by_imei){
                $flashMessenger->setNamespace('error')->addMessage('Invalid imei');
                $this->_redirect(HOST."warehouse/factory-claim-approve");
            }

            $array_phone = $result_return_by_group;
            $array_imei = $result_return_by_imei;
            $array_distributor_id[] = array();
            foreach ($result_return_by_group as $k=>$item){

                $array[$k] = $item['distributor_id'];
            }

            $array_distributor = array_unique($array);

            $isbatch        = 1;
            $isbacks        = 1;

            $QLog = new Application_Model_Log();

            $QMarket = new Application_Model_Market();

            //$db = Zend_Registry::get('db');

            $QImeiReturn = new Application_Model_ImeiReturn();
            $QDistributor = new Application_Model_Distributor();
            $QImei = new Application_Model_Imei();

            //1=เครื่องเสีย 2=Adjustment 3=Demo 4=RTN 5=EOL
            $return_type=1;

            $count_create_cn_before=0;$count_create_cn_after=0;
            foreach ($array_distributor as $j=>$distributor_id){
                //echo $distributor_id;
                if ($distributor_id !='') {

                    $sn = date ( 'YmdHis' ) . substr ( microtime (), 2, 4 );
                    $sn_ref=$QImeiReturn->getReturnOrderNo_Ref($sn);

                    $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
                    $distributor = $QDistributor->fetchRow($where);
                    $rank = $distributor->rank;

                    $status_create_cn=0;
                    $status_active_cn=0;
                    $warehouse_id=114;//WMSHQ - คลังยืม Service เครื่องเปลี่ยนลูกค้า
                    $imei_shape_status=4;
                    $remark_text_val=$remark;

                    foreach ($array_phone as $k=>$item){

                        if ($distributor_id == $item['distributor_id']) 
                        {

                            $date = date('Y-m-d H:i:s');
                            
                            if(isset($item['invoice_number'])){
                                $invoice_number = $item['invoice_number'];
                            }else{$invoice_number='';}
                            $data = array(
                                'cat_id'        => $item['cat_id'],
                                'good_id'       => $item['good_id'],
                                'good_color'    => $item['good_color'],
                                'num'           => $item['num'],
                                'price'         => $item['unit_price'],
                                'total'         => $item['total_price'],
                                'text'          => $remark_text_val,
                                'price_clas'    => $rank,
                                'd_id'          => $distributor_id,
                                'isbatch'       => $isbatch,
                                'isbacks'       => $isbacks,
                                'backs_d_id'    => $warehouse_id,
                                'warehouse_id'  => $warehouse_id,
                                'invoice_number'    => $invoice_number,
                                'create_cn'     => $status_create_cn,
                                'active_cn'     => $status_active_cn,
                                'return_type'     => $return_type,
                                'pay_text'      => 'Approve',
                                'pay_time'      => $date,
                                'pay_user'      => $userStorage->id,
                                'shipping_yes_time'     => $date,
                                'shipping_yes_id'     => $userStorage->id,
                                'finance_confirm_date'     => $date,
                                'finance_confirm_id'     => $userStorage->id,

                                'add_time'      => $date,
                                'user_id'       => $userStorage->id,
                                'sn'            => $sn,
                                'sn_ref'        => $sn_ref
                            );
                        
                            //print_r($data);die;
                            //insert
                            $QMarket->insert($data);
                         
                        }  
                    }

                    foreach ($array_imei as $t=>$item_imei){

                        if ($distributor_id == $item_imei['distributor_id']) 
                        {
                            $imei = trim($item_imei['imei_sn']);
                            $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                            $imei_check = $QImei->fetchRow($where);
                            $sales_sn = $imei_check['sales_sn'];   
            
                            $dataImeiReturn = array(
                                'imei_sn' => trim($imei),
                                'warehouse_id' => $warehouse_id,
                                'return_sn' => $sn,
                                'sales_order_sn' => $sales_sn,
                                'created_at' => $date,
                                'created_by' => $userStorage->id,
                                'confirmed_at' => $date,
                                'confirmed_by' => $userStorage->id
                            );

                            $dataImei = array(
                                'return_sn'     => $sn,
                                'distributor_id'=> null,
                                'out_date'      => null,
                                'out_user'      => null,
                                'out_price'     => null,
                                'price_date'    => null,
                                'sales_sn'      => null,
                                'sales_id'      => null,
                                'shape'         => $imei_shape_status,
                                'warehouse_id'  => $warehouse_id,
                            );

                            switch ($imei_shape_status) {
                                case 1: //Goodset
                                    $dataImeiReturn['status']= 1;
                                    $dataImeiReturn['back_sale']= 1;
                                    break;
                                case 2: //Broken-seal
                                    $dataImeiReturn['status']= 2;
                                    $dataImei['status']= 1;
                                    $dataImei['shape']= 2;
                                    break;
                                case 3: //Box-damage
                                    $dataImeiReturn['status']= 3;
                                    $dataImei['status']= 1;
                                    $dataImei['shape']= 3;
                                    break;
                                case 4: //Unit-damage
                                    $dataImeiReturn['status']= 4;
                                    $dataImei['status']= 1;
                                    $dataImei['shape']= 4;
                                    break;
                            }

                            //print_r($data);die;
                            $QImeiReturn->insert($dataImeiReturn);

                            // Update Imei
                            $whereImei = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                            $QImei->update($dataImei, $whereImei);
                            
                        }
                    }

                }
            }

            $info = 'Return Product Auto Check : '. $sn;
            $ip = $this->getRequest()->getServer('REMOTE_ADDR');
            $info .= $sn;

            $QLog->insert( array (
                'info' => $info,
                'user_id' => $userStorage->id,
                'ip_address' => $ip,
                'time' => date('Y-m-d H:i:s'),
            ) );

            if($count_create_cn_after != $count_create_cn_before){
                $flashMessenger->setNamespace('error')->addMessage('Cannot Create CN For Return, Please try again!');
                $this->_redirect(HOST."warehouse/factory-claim-approve");
            }

            $data = array(
                'status' => 2,
                'approved_date' => date('Y-m-d H:i:s'),
                'approved_by' => $userStorage->id
            );

            $where_update = $QFC->getAdapter()->quoteInto('factory_claim_id = ?', $id);
            $QFC->update($data, $where_update);

            $db->commit();

            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $this->_redirect(HOST."warehouse/factory-claim-approve-list");

        } catch (Exception $e){
            $db->rollback();

            $flashMessenger->setNamespace('error')->addMessage('Failed - '.$e->getMessage());
            $this->_redirect(HOST."warehouse/factory-claim-approve");
        }

    }

    $QFC = new Application_Model_FactoryClaim();
    $get_factory_claim = $QFC->getFactoryClaim($id,1);

    if(!$get_factory_claim){
        $flashMessenger->setNamespace('error')->addMessage('Not Find!');
        $this->_redirect(HOST."warehouse/factory-claim-approve-list");
    }

    $this->view->get_factory_claim = $get_factory_claim;

    $part_uploaded_dir = HOST . 'public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'warehouse'. DIRECTORY_SEPARATOR . 'factory_claim' . DIRECTORY_SEPARATOR;

    $this->view->img_id_card = $part_uploaded_dir . $get_factory_claim['img_id_card'];

    $this->view->img_broken = $part_uploaded_dir . $get_factory_claim['img_broken'];

    $this->view->id = $id;

    $QWG = new Application_Model_WarehouseGroupUser();

    $QWarehouse = new Application_Model_Warehouse();
    $warehouses = $QWarehouse->get_cache();

    $get_warehouse = $QWG->currentWarehouseGroupUserList($userStorage->id);

    $this->view->get_warehouse = $get_warehouse;

    $this->view->warehouse_all = $warehouses;

    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;