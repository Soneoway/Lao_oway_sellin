<?php
$sn = $this->getRequest()->getParam('sn');

if ($sn) {

    if ($this->getRequest()->getMethod() == 'POST'){

        $imei_good              = $this->getRequest()->getParam('imei_good');
        $imei_bad1              = $this->getRequest()->getParam('imei_bad1');
        $imei_bad2              = $this->getRequest()->getParam('imei_bad2');
        $imei_bad3              = $this->getRequest()->getParam('imei_bad3');
        $num_goods              = $this->getRequest()->getParam('num_good');
        $num_bads1              = $this->getRequest()->getParam('num_bad1');
        $num_bads2              = $this->getRequest()->getParam('num_bad2');
        $num_bads3              = $this->getRequest()->getParam('num_bad3');
        $return_order_type      = $this->getRequest()->getParam('return_order_type');

        //validate data

        $imei_good_list = array();
        if (trim($imei_good)) {
            $imei_good_list = explode("\n", trim($imei_good));

            if ($imei_good_list)

                $imei_good_list = array_unique(array_map('trim', $imei_good_list));
        }

        $imei_bad_list1 = $imei_bad_list2 = $imei_bad_list3 = array();
        if (trim($imei_bad1)) {
            $imei_bad_list1 = explode("\n", trim($imei_bad1));

            if ($imei_bad_list1)
                $imei_bad_list1 = array_unique(array_map('trim', $imei_bad_list1));

        }

        if (trim($imei_bad2)) {
            $imei_bad_list2 = explode("\n", trim($imei_bad2));

            if ($imei_bad_list2)
                $imei_bad_list2 = array_unique(array_map('trim', $imei_bad_list2));

        }

        if (trim($imei_bad3)) {
            $imei_bad_list3 = explode("\n", trim($imei_bad3));

            if ($imei_bad_list3)
                $imei_bad_list3 = array_unique(array_map('trim', $imei_bad_list3));

        }

        foreach ($imei_good_list as $key => $value) {
            if ( in_array($value, $imei_bad_list1) || in_array($value, $imei_bad_list2) || in_array($value, $imei_bad_list3) ) {
                echo '<script>
                parent.palert("IMEI '.$value.' is duplicated.");
                </script>';
                exit;
            }
        }

        foreach ($imei_bad_list1 as $key => $value) {
            if ( in_array($value, $imei_bad_list2) || in_array($value, $imei_bad_list3) ) {
                echo '<script>
                parent.palert("IMEI '.$value.' is duplicated.");
                </script>';
                exit;
            }
        }

        foreach ($imei_bad_list2 as $key => $value) {
            if ( in_array($value, $imei_bad_list3) ) {
                echo '<script>
                parent.palert("IMEI '.$value.' is duplicated.");
                </script>';
                exit;
            }
        }

        $imei_list = array_filter(array_unique(array_merge($imei_good_list, $imei_bad_list1, $imei_bad_list2, $imei_bad_list3)));

        $QMarket = new Application_Model_Market();

        $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
        $sales = $QMarket->fetchAll($where);

        $QGoodCategory = new Application_Model_GoodCategory();
        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();

        $cached_goods = $QGood->get_cache();
        $cached_good_colors = $QGoodColor->get_cache();
        $return_product="";
        $count_phone = 0;
        foreach ($sales as $sale){

            if($sale->cat_id==DIGITAL_CAT_ID){
                $return_product = "digital";
            }else if($sale->cat_id==PHONE_CAT_ID){
                $return_product = "phone";
            }else{
                $return_product = "accessories";
            }

            if ( in_array($sale->cat_id , array(PHONE_CAT_ID , DIGITAL_CAT_ID, IOT_CAT_ID)) )
                $count_phone += $sale->num;               
            else{
                //check quantity of accessories
                $num_good = isset($num_goods[$sale->id]) ? intval($num_goods[$sale->id]) : 0;
                $num_bad1 = isset($num_bads1[$sale->id]) ? intval($num_bads1[$sale->id]) : 0;
                $num_bad2 = isset($num_bads2[$sale->id]) ? intval($num_bads2[$sale->id]) : 0;
                $num_bad3 = isset($num_bads3[$sale->id]) ? intval($num_bads3[$sale->id]) : 0;

                if (($num_good + $num_bad1  + $num_bad2  + $num_bad3 ) != $sale->num){

                    echo '<script>
                    parent.palert("Please input valid number of '.$cached_goods[$sale->good_id].' / '.$cached_good_colors[$sale->good_color].'");
                    </script>';
                    exit;

                }

            }
        }

        if (count($imei_good_list) + count($imei_bad_list1) + count($imei_bad_list2) + count($imei_bad_list3) != $count_phone) {
            echo '<script>
            parent.palert("Please input valid number of IMEI.");
            </script>';
            exit;
        }

        if ( count($imei_list) != $count_phone){
            echo '<script>
            parent.palert("Please input valid number of IMEI.");
            </script>';
            exit;
        }

        $QImeiReturn = new Application_Model_ImeiReturn();
        $QDigitalReturn = new Application_Model_DigitalSnReturn();
        $QImei = new Application_Model_Imei(); // check imei already warehouse

        if($return_product=="phone"){
            //check imei existed in system
            foreach ($imei_list as $imei){
                $imei = trim($imei);
                $where = $QImeiReturn->getAdapter()->quoteInto('imei_sn = ?', $imei);
                $check = $QImeiReturn->fetchRow($where);
                if (!$check){
                    echo '<script>parent.palert("This IMEI '.$imei.' is not existed in our system.");</script>';
                    exit;
                }
            }
        }else if($return_product=="digital"){
            //check imei existed in system
            foreach ($imei_list as $imei){
                $imei = trim($imei);
                $where = $QDigitalReturn->getAdapter()->quoteInto('sn = ?', $imei);
                $check = $QDigitalReturn->fetchRow($where);
                if (!$check){
                    echo '<script>parent.palert("This Serial Number '.$imei.' is not existed in our system.");</script>';
                    exit;
                }
            }
        }

        // Store Return Case
        if($return_order_type == 1) {

       // Check imei already in warehouse
           if($imei_list){
            foreach ($imei_list as $imei) {
                $imei = trim($imei);
                $where = array();
                $where[] = $QImei->getAdapter()->quoteInto('imei_sn =?',$imei);
                // $where[] = $QImei->getAdapter()->quoteInto('distributor_id IS NOT NULL',null);
                $check = $QImei->fetchAll($where);

                if($check){
                    $where[] = $QImei->getAdapter()->quoteInto('distributor_id IS NOT NULL',null); 
                    $check1 = $QImei->fetchRow($where);

                    if(!$check1){
                        echo '<script>alert("imei is already in warehouse ອິ່ມມີ່ນີ້ຢູ່ໃນສາງແລ້ວ ກາລຸນາຕິດຕໍ່ຫາ Admin")</script>';
                        exit;
                    }
                }

            }
        }

    }

        //end - validate data

        //update data

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);

    $data = array(
        'outmysql_time' => date('Y-m-d H:i:s'),
        'outmysql_user' => $userStorage->id,
        'order_status'  => 4
    );

    $QMarket->update($data, $where);

        //update imei & product return
    $QProductReturn = new Application_Model_ProductReturn();

    $data = array(
        'back_warehouse_at' => date('Y-m-d H:i:s'),
        'back_warehouse_by' => $userStorage->id,
    );

    $where = $QProductReturn->getAdapter()->quoteInto('return_sn = ?', $sn);

    $QProductReturn->update($data, $where);

    if ($imei_list) {
        $where = array();
        $where[] = $QImeiReturn->getAdapter()->quoteInto('imei_sn IN (?)', $imei_list);
        $where[] = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
        $QImeiReturn->update($data, $where);
    }
        //end - update imei & product return


        /*$QProductReturnReceive = new Application_Model_ProductReturnReceive();
        $QImeiReturnReceive = new Application_Model_ImeiReturnReceive();*/

        $QWarehouseProduct = new Application_Model_WarehouseProduct();
        $market_digital = 0;

        foreach ($sales as $sale){

            $warehouse_id = $sale->backs_d_id;

            if ( $sale->cat_id == $QGoodCategory->get_accessories_id() ){
                //update accessories

                $num_good = isset($num_goods[$sale->id]) ? intval($num_goods[$sale->id]) : 0;
                $num_bad1 = isset($num_bads1[$sale->id]) ? intval($num_bads1[$sale->id]) : 0;
                $num_bad2 = isset($num_bads2[$sale->id]) ? intval($num_bads2[$sale->id]) : 0;
                $num_bad3 = isset($num_bads3[$sale->id]) ? intval($num_bads3[$sale->id]) : 0;

                $where = array();
                $where[] = $QWarehouseProduct->getAdapter()->quoteInto('cat_id = ?', $sale->cat_id);
                $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_id = ?', $sale->good_id);
                $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_color = ?', $sale->good_color);
                $where[] = $QWarehouseProduct->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);

                $product = $QWarehouseProduct->fetchRow($where);

                if ($product) { //update
                    $data = array(
                        'quantity' => $product['quantity'] + $num_good
                    );

                    $where = $QWarehouseProduct->getAdapter()->quoteInto('id = ?', $product['id']);

                    $QWarehouseProduct->update($data, $where);

                } else { //insert

                    $data = array(
                        'cat_id' => $sale->cat_id,
                        'good_id' => $sale->good_id,
                        'good_color' => $sale->good_color,
                        'warehouse_id' => $warehouse_id,
                        'quantity' => $num_good,
                    );

                    $QWarehouseProduct->insert($data);

                }

                //insert into product return
                $data = array(
                    'status'        => 1,
                    'quantity_1'      => $num_good,
                    'quantity_2'      => $num_bad1,
                    'quantity_3'      => $num_bad2,
                    'quantity_4'      => $num_bad3,
                );

                $where = array();
                $where[] = $QProductReturn->getAdapter()->quoteInto('cat_id = ?', $sale->cat_id);
                $where[] = $QProductReturn->getAdapter()->quoteInto('good_id = ?', $sale->good_id);
                $where[] = $QProductReturn->getAdapter()->quoteInto('good_color = ?', $sale->good_color);
                $where[] = $QProductReturn->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                $where[] = $QProductReturn->getAdapter()->quoteInto('return_sn = ?', $sale->sn);


                $QProductReturn->update($data, $where);
            }

            if($sale->cat_id == DIGITAL_CAT_ID)
            {
                $market_digital = 1;
            }

        }

        if ($imei_list) {
            if(isset($market_digital) and $market_digital)
            {
                $QDigital = new Application_Model_DigitalSn();
                $where = $QDigital->getAdapter()->quoteInto('sn IN (?)' , $imei_list);
                $data = array(
                    'return_sn'     => $sn,
                    'distributor_id'=> null,
                    'out_date'      => null,
                    'out_user'      => null,
                    'out_price'     => null,
                    'sales_sn'      => null,
                    'sales_id'      => null,
                    'warehouse_id'  => $warehouse_id,
                );
                $QDigital->update($data, $where);
            }
            else
            {
                //update table IMEI for all IMEI
                $QImei = new Application_Model_Imei();
                $where = $QImei->getAdapter()->quoteInto('imei_sn IN (?)', $imei_list);
                $data = array(
                    'return_sn'     => $sn,
                    'distributor_id'=> null,
                    'out_date'      => null,
                    'out_user'      => null,
                    'out_price'     => null,
                    'price_date'    => null,
                    'sales_sn'      => null,
                    'sales_id'      => null,
                    'store_id'      => null,
                    'warehouse_id'  => $warehouse_id,
                );

                $QImei->update($data, $where);

		$QContactDetail = new Application_Model_ContactDetail();
                $where2 = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$sn);

                $data2 = array(
                    'status'        => 2,
                    'bill_date'     => date('Y-m-d H:m:s')
                );

                $QContactDetail->update($data2, $where2);

            }
        }


        //update to IMEI return

        if ($imei_good_list)
            foreach ($imei_good_list as $item){

                $where = array();
                if($return_product=="phone"){
                    $where[] = $QImeiReturn->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                    $where[] = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
                    $where[] = $QImeiReturn->getAdapter()->quoteInto('imei_sn = ?', $item);

                    $data = array(
                        'status'        => 1,
                        'back_sale'     => 1,
                    );

                    $QImeiReturn->update($data, $where);
                }else if($return_product=="digital"){
                    $where[] = $QDigitalReturn->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                    $where[] = $QDigitalReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
                    $where[] = $QDigitalReturn->getAdapter()->quoteInto('sn = ?', $item);

                    $data = array(
                        'status'        => 1,
                        'back_sale'     => 1,
                    );

                    $QDigitalReturn->update($data, $where);
                }
            }

            if ($imei_bad_list1)
                foreach ($imei_bad_list1 as $item){

                    $where = array();
                    if($return_product=="phone"){
                        $where[] = $QImeiReturn->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                        $where[] = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);

                        $where[] = $QImeiReturn->getAdapter()->quoteInto('imei_sn = ?', $item);

                        $data = array(
                            'status'        => 2,
                        );

                        $QImeiReturn->update($data, $where);

                    //update imei shape
                        $data = array(
                            'status'        => 2,
                            'shape'         => 2,
                        );

                        $where = array();
                        $where[] = $QImei->getAdapter()->quoteInto('return_sn = ?', $sn);
                        $where[] = $QImei->getAdapter()->quoteInto('imei_sn = ?', $item);

                        $QImei->update($data, $where);
                    }else if($return_product=="digital"){
                        $where[] = $QDigitalReturn->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                        $where[] = $QDigitalReturn->getAdapter()->quoteInto('return_sn = ?', $sn);

                        $where[] = $QDigitalReturn->getAdapter()->quoteInto('sn = ?', $item);

                        $data = array(
                            'status'        => 2,
                        );

                        $QDigitalReturn->update($data, $where);

                    //update imei shape
                        $data = array(
                            'status'        => 2,
                            'shape'         => 2,
                        );

                        $where = array();
                        $where[] = $QDigital->getAdapter()->quoteInto('return_sn = ?', $sn);
                        $where[] = $QDigital->getAdapter()->quoteInto('sn = ?', $item);

                        $QDigital->update($data, $where);
                    }

                }

                if ($imei_bad_list2)
                    foreach ($imei_bad_list2 as $item){

                        $where = array();
                        if($return_product=="phone"){
                            $where[] = $QImeiReturn->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                            $where[] = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);

                            $where[] = $QImeiReturn->getAdapter()->quoteInto('imei_sn = ?', $item);

                            $data = array(
                                'status'        => 3,
                            );

                            $QImeiReturn->update($data, $where);

                    //update imei shape
                            $data = array(
                                'status'        => 2,
                                'shape'         => 3,
                            );

                            $where = array();
                            $where[] = $QImei->getAdapter()->quoteInto('return_sn = ?', $sn);
                            $where[] = $QImei->getAdapter()->quoteInto('imei_sn = ?', $item);

                            $QImei->update($data, $where);
                        }else if($return_product=="digital"){
                            $where[] = $QDigitalReturn->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                            $where[] = $QDigitalReturn->getAdapter()->quoteInto('return_sn = ?', $sn);

                            $where[] = $QDigitalReturn->getAdapter()->quoteInto('sn = ?', $item);

                            $data = array(
                                'status'        => 3,
                            );

                            $QDigitalReturn->update($data, $where);

                    //update imei shape
                            $data = array(
                                'status'        => 2,
                                'shape'         => 3,
                            );

                            $where = array();
                            $where[] = $QDigital->getAdapter()->quoteInto('return_sn = ?', $sn);
                            $where[] = $QDigital->getAdapter()->quoteInto('sn = ?', $item);

                            $QDigital->update($data, $where);
                        }
                    }

                    if ($imei_bad_list3)
                        foreach ($imei_bad_list3 as $item){

                            $where = array();
                            if($return_product=="phone"){
                                $where[] = $QImeiReturn->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                                $where[] = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);

                                $where[] = $QImeiReturn->getAdapter()->quoteInto('imei_sn = ?', $item);

                                $data = array(
                                    'status'        => 4,
                                );

                                $QImeiReturn->update($data, $where);

                    //update imei shape
                                $data = array(
                                    'status'        => 2,
                                    'shape'         => 4,
                                );

                                $where = array();
                                $where[] = $QImei->getAdapter()->quoteInto('return_sn = ?', $sn);
                                $where[] = $QImei->getAdapter()->quoteInto('imei_sn = ?', $item);

                                $QImei->update($data, $where);
                            }else if($return_product=="digital"){
                                $where[] = $QDigitalReturn->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                                $where[] = $QDigitalReturn->getAdapter()->quoteInto('return_sn = ?', $sn);

                                $where[] = $QDigitalReturn->getAdapter()->quoteInto('sn = ?', $item);

                                $data = array(
                                    'status'        => 4,
                                );

                                $QDigitalReturn->update($data, $where);

                    //update imei shape
                                $data = array(
                                    'status'        => 2,
                                    'shape'         => 4,
                                );

                                $where = array();
                                $where[] = $QDigital->getAdapter()->quoteInto('return_sn = ?', $sn);
                                $where[] = $QDigital->getAdapter()->quoteInto('sn = ?', $item);

                                $QDigital->update($data, $where);
                            }
                        }

        //end - update data

                        $ip = $this->getRequest()->getServer('REMOTE_ADDR');
                        $info = 'Return : '. $sn;

                        $QLog = new Application_Model_Log();
                        $QLog->insert( array (
                            'info' => $info,
                            'user_id' => $userStorage->id,
                            'ip_address' => $ip,
                            'time' => date('Y-m-d H:i:s'),
                        ) );

                        $flashMessenger = $this->_helper->flashMessenger;
                        $flashMessenger->setNamespace('success')->addMessage('Done!');

                        $back_url = $this->getRequest()->getParam('back_url');

                        echo '<script>parent.location.href="'.( $back_url ? $back_url : HOST.'warehouse/return-list' ).'"</script>';
                        exit;
                    }

                    $QMarket = new Application_Model_Market();

                    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                    $sales = $QMarket->fetchAll($where);

                    $data = array();

                    $QGoodCategory = new Application_Model_GoodCategory();

                    $this->view->phone_id = $QGoodCategory->get_phone_id();

                    $this->view->accessories_id = $QGoodCategory->get_accessories_id();

                    $categories = $QGoodCategory->get_cache();

                    $QGood = new Application_Model_Good();
                    $goods = $QGood->get_cache();

                    $QGoodColor = new Application_Model_GoodColor();
                    $goodColors = $QGoodColor->get_cache();

                    $QStaff = new Application_Model_Staff();
                    $staffs = $QStaff->get_cache();

                    $QDistributor = new Application_Model_Distributor();
                    $distributors = $QDistributor->get_cache();

                    $QWarehouse = new Application_Model_Warehouse();
                    $warehouses = $QWarehouse->get_cache();

                    $QBrand = new Application_Model_Brand();

                    $QImeiReturn = new Application_Model_ImeiReturn();
                    $where = array();
                    $where[] = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
                    $imei_returns = $QImeiReturn->fetchAll($where);

                    $QDigitalReturn = new Application_Model_DigitalSnReturn();
                    $where = array();
                    $where[] = $QDigitalReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
                    $digital_returns = $QDigitalReturn->fetchAll($where);

                    if(count($digital_returns) > 0){
                        $this->view->return_product = 'digital';
                        $this->view->imei_returns = $digital_returns;
                    }else{
                        $this->view->return_product = 'phone';
                        $this->view->imei_returns = $imei_returns;
                    }

    //print_r($imei_returns);

                    $where[] = $QImeiReturn->getAdapter()->quoteInto('status = ?', 1);
                    $this->view->imei_return_good_ins = $QImeiReturn->fetchAll($where);

                    $where[] = $QImeiReturn->getAdapter()->quoteInto('status = ?', 2);
                    $this->view->imei_return_bad_ins1 = $QImeiReturn->fetchAll($where);

                    $where[] = $QImeiReturn->getAdapter()->quoteInto('status = ?', 3);
                    $this->view->imei_return_bad_ins2 = $QImeiReturn->fetchAll($where);

                    $where[] = $QImeiReturn->getAdapter()->quoteInto('status = ?', 4);
                    $this->view->imei_return_bad_ins3 = $QImeiReturn->fetchAll($where);


                    foreach ($sales as $k=>$sale){
        //get return to which warehouse
                        $data[$k]['backs_d_name'] = isset($warehouses[$sale->backs_d_id]) ? $warehouses[$sale->backs_d_id] : '';

        //get retailer
                        $data[$k]['retailer_name'] = isset($distributors[$sale->d_id]) ? $distributors[$sale->d_id] : '';

        //get created_by_name
                        $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->user_id] : '';

        //get pay_user
                        $data[$k]['pay_user_name'] = isset($staffs[$sale->pay_user]) ? $staffs[$sale->pay_user] : '';

        //get category
                        $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->cat_id] : '';

                        $brand = $QBrand->getBrand($sale->good_id);
                        $data[$k]['brand_name'] = $brand[0]['brand_name'];

        //get good
                        $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';

        //get goods color
                        $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->good_color] : '';

                        $data[$k]['sale'] = $sale;
                    }

                    $this->view->sales = $data;

    //back url
                    $this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER');
                }