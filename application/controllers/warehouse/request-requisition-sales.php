<?php
//get list distributor
$QDistributor = new Application_Model_Distributor();
$distributors = $QDistributor->get_cache();

$QChangeDocType = new Application_Model_ChangeDocType();
$changedoctype = $QChangeDocType->get_cache();

//get list warehouse
$QWarehouse = new Application_Model_Warehouse();
$warehouses = $QWarehouse->get_cache();

$QGood = new Application_Model_Good();
$where = $QGood->getAdapter()->quoteInto('cat_id = ?', ACCESS_CAT_ID);
$this->view->accessories = $QGood->fetchAll($where, 'name');

$this->view->changedoctype = $changedoctype;
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

$id                 = $this->getRequest()->getParam('id');
$QChangeSalesOrder = new Application_Model_ChangeSalesOrder();
$QChangeSalesProduct = new Application_Model_ChangeSalesProduct();
$QImei = new Application_Model_Imei();
$QChangeSalesList     = new Application_Model_ChangeSalesList();
$changeSalesOrder = null;
if ($id){
    $whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
    $changeSalesOrder = $QChangeSalesOrder->fetchRow($whereChangeSalesOrder);

    $whereChangeSalesList = $QChangeSalesList->getAdapter()->quoteInto('changed_id = ?', $id);
    $changeSalesList = $QChangeSalesList->fetchRow($whereChangeSalesList);
   
}

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

//save form
if($this->getRequest()->isPost()) {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();

    echo '<link href="/css/bootstrap.min.css" rel="stylesheet">';

    $is_changed_wh      = $this->getRequest()->getParam('is_changed_wh');
    $warehouse_id1      = $this->getRequest()->getParam('warehouse_id1');
    $warehouse_id2      = $this->getRequest()->getParam('warehouse_id2');
    $distributor_id1    = $this->getRequest()->getParam('distributor_id1');
    $distributor_id2    = $this->getRequest()->getParam('distributor_id2');
    $type               = $this->getRequest()->getParam('type');
    $salesman_id        = $this->getRequest()->getParam('salesman_id');
    $product_demand     = $this->getRequest()->getParam('product_demand');
    $detail             = $this->getRequest()->getParam('detail');
    $note               = $this->getRequest()->getParam('note');
    $is_changed_wh2     = $this->getRequest()->getParam('is_changed_wh2');
    $doc_type           = $this->getRequest()->getParam('doc_type');

    $is_changed_wh      = $is_changed_wh ? 1 : 0;

    $cat_ids            = $this->getRequest()->getParam('cat_id');
    $good_ids           = $this->getRequest()->getParam('good_id');
    $good_colors        = $this->getRequest()->getParam('good_color');
    $nums               = $this->getRequest()->getParam('num');

    $userStorage        = Zend_Auth::getInstance()->getStorage()->read();

    $QLog               = new Application_Model_Log();


    $ip                 = $this->getRequest()->getServer('REMOTE_ADDR');

    if ($is_changed_wh == 0){
        $type = 0;
        if (!$distributor_id1 or !$distributor_id2) {
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Please choose retailer!</div>';
            exit;
        }

        if ($distributor_id1 == $distributor_id2) {
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Please do not choose same retailer!</div>';
            exit;
        }

        if (is_array($cat_ids) and in_array(ACCESS_CAT_ID, $cat_ids)){
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - You can only change Warehouse for accessories!</div>';
            exit;
        }
    } else {

        if (!$warehouse_id1 or !$warehouse_id2) {
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Please choose warehouse!</div>';
            exit;
        }

        if ($warehouse_id1 == $warehouse_id2) {
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Please do not choose same warehouse!</div>';
            exit;
        }

        if (!$type){
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Please choose Type!</div>';
            exit;
        }
    }

    // check data
    if (
        !$cat_ids or !is_array($cat_ids)
        or !$good_ids or !is_array($good_ids)
        or !$good_colors or !is_array($good_colors)
        or !$nums or !is_array($nums)
    ){
        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
        echo '<div class="alert alert-error">Failed - Please select product!</div>';
        exit;
    }

    //check input
    if ( ! (    is_array($good_ids) and is_array($good_colors) and is_array($nums)
        and count($good_ids) and count($good_colors) and count($nums)
        and count($good_ids) == count($good_colors)
        and count($good_ids) == count($nums)
    ) ){
        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
        echo '<div class="alert alert-error">Failed - Please select product!</div>';
        exit;
    }

    // check id
    if ($id){
        if (!$changeSalesOrder){
            echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
            echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
            echo '<div class="alert alert-error">Failed - Change Order is invalid!</div>';
            exit;
        }

        if ($changeSalesOrder['status'] != CHANGE_ORDER_STATUS_PENDING){
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

        $old_detail_ids = $new_detail_ids = array();

        // kiểm tra chéo kho
        if(isset($warehouse_id1) and $warehouse_id2)
        {
            $warehouse_from = $QWarehouse->find($warehouse_id1);
            $warehouse_from = $warehouse_from->current();

            $warehouse_to   = $QWarehouse->find($warehouse_id2);
            $warehouse_to   = $warehouse_to->current();

            if(empty($warehouse_from) || empty($warehouse_to))
            {
                echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                echo '<div class="alert alert-error">Failed - Invalid Warehouse!</div>';
                exit;
            }

            if($warehouse_from['company_id'] != $warehouse_to['company_id'])
            {
                echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                echo '<div class="alert alert-error">Failed - Company is different!</div>';
                exit;
            }

        }

        $changed_sn = date('YmdHis') . substr ( microtime (), 2, 4 );

             
        if ($id){
            $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('changed_id = ?', $id);
            $changeSalesProduct = $QChangeSalesProduct->fetchAll($whereChangeSalesProduct);

            foreach ($changeSalesProduct as $item){
                $old_detail_ids[] = $item['id'];
            }


            // update
            $whereChangeSalesOrder = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
            $QChangeSalesOrder->update(array(
                'type'              => $type,
                'is_changed_wh'     => $is_changed_wh,
                'new_id'            => ($is_changed_wh==1 ? $warehouse_id2 : $distributor_id2),
                'old_id'            => ($is_changed_wh==1 ? $warehouse_id1 : $distributor_id1),
                'updated_at'        => date('Y-m-d H:i:s'),
                'updated_by'        => $userStorage->id,
            ), $whereChangeSalesOrder);

            if ($is_changed_wh2==2) {
            $whereChangeSalesList = $QChangeSalesList->getAdapter()->quoteInto('changed_id = ?', $id);
            $temp = explode('/' , $product_demand);
            $date_demand = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
            $data = array(
                    'doc_type'      => $doc_type,
                    'salesman_id'   => $salesman_id,
                    'product_demand'=> $date_demand,
                    'detail'        => $detail,
                    'note'          => $note,
                   
                );
            $QChangeSalesList->update($data,$whereChangeSalesList);
        }

        } else {
            //insert to change_sales_order
                if($is_changed_wh2==""){
                    $is_changed_wh2 = 0;
                }

            $QChangeSalesOrder = new Application_Model_ChangeSalesOrder();
            $data = array(
                'type'              => $type,
                'is_changed_wh'     => $is_changed_wh,
                'changed_order'     => $is_changed_wh2,
                'new_id'            => ($is_changed_wh==1 ? $warehouse_id2 : $distributor_id2),
                'old_id'            => ($is_changed_wh==1 ? $warehouse_id1 : $distributor_id1),
                'created_at'        => date('Y-m-d H:i:s'),
                'created_by'        => $userStorage->id,
                'changed_sn'        => $changed_sn,
            );

            $id = $QChangeSalesOrder->insert($data);
            $this->getChangeOrderNo($changed_sn);
        
        //Pungpond ใบชอเบิก 
       
            if ($is_changed_wh2==2) {
                
                
                $QChangeSalesList = new Application_Model_ChangeSalesList();
                $temp = explode('/' , $product_demand);
                $date_demand = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
                $data = array(
                        'changed_id'    => $id,
                        'changed_co'    => $changed_sn,
                        'doc_type'      => $doc_type,
                        'salesman_id'   => $salesman_id,
                        'product_demand'=> $date_demand,
                        'detail'        => $detail,
                        'note'          => $note,
                       
                    );
                $QChangeSalesList->insert($data);
                
            }
        }
        // check quantity
        foreach ($good_ids as $k=>$good_id){
            // change WH
            if ($is_changed_wh){

                $storageParams = array(
                    'warehouse_id'  => $warehouse_id1,
                    'cat_id'        => $cat_ids[$k],
                    'good_id'       => $good_id,
                    'good_color_id' => $good_colors[$k],);

                // truong hop edit lai
                $storageParams['current_change_order_id']   = $id;

                $storageParams['not_get_ilike_bad_count']      =
                $storageParams['not_get_digital_bad_count']    =
                $storageParams['not_get_imei_bad_count']       =
                $storageParams['not_get_damage_product_count'] =
                $storageParams['not_get_total']                =
                $storageParams['not_order']                    =
                    true;


                $storageParams['not_get_ilike_count'] = true;


                $storage            = $QGood->fetchPaginationStorage(1, null, $total2, $storageParams);

                $current_order      = isset($storage[0]['current_order']) ? $storage[0]['current_order'] : 0;
                $current_change_order      = isset($storage[0]['current_change_order']) ? $storage[0]['current_change_order'] : 0;
                if ($cat_ids[$k]==PHONE_CAT_ID and $type==FOR_DEMO){
                    $current_order          = isset($storage[0]['current_order_demo']) ? $storage[0]['current_order_demo'] : 0;
                    $current_change_order   = isset($storage[0]['current_change_order_demo']) ? $storage[0]['current_change_order_demo'] : 0;
                }else if ($cat_ids[$k]==PHONE_CAT_ID and $type==FOR_APK){
                    $current_order          = isset($storage[0]['current_order_apk']) ? $storage[0]['current_order_apk'] : 0;
                    $current_change_order   = isset($storage[0]['current_change_order_apk']) ? $storage[0]['current_change_order_apk'] : 0;
                }

                $current_storage    = 0;

                if (isset($storage[0]) and $storage[0]) {
                    switch ($cat_ids[$k]){
                        case DIGITAL_CAT_ID:
                            $current_storage = (isset($storage[0]['digital_count']) and $storage[0]['digital_count']) ? $storage[0]['digital_count'] : 0;
                            break;
                        case PHONE_CAT_ID:
                            $current_storage = (isset($storage[0]['imei_count']) and $storage[0]['imei_count']) ? $storage[0]['imei_count'] : 0;
                            if ($type==FOR_DEMO){
                                $current_storage = (isset($storage[0]['imei_demo_count']) and $storage[0]['imei_demo_count']) ? $storage[0]['imei_demo_count'] : 0;
                            }else if ($type==FOR_APK){
                                $current_storage = (isset($storage[0]['imei_apk_count']) and $storage[0]['imei_apk_count']) ? $storage[0]['imei_apk_count'] : 0;
                            }
                            break;
                        case ILIKE_CAT_ID:
                            $current_storage = (isset($storage[0]['ilike_count']) and $storage[0]['ilike_count']) ? $storage[0]['ilike_count'] : 0;
                            break;
                        case ACCESS_CAT_ID:
                            $current_storage = (isset($storage[0]['product_count']) and $storage[0]['product_count']) ? $storage[0]['product_count'] : 0;
                            break;

                    }
                }

                $count = $current_storage - $current_order - $current_change_order;
                /*
                echo "current_storage>".$current_storage;
                echo "current_order>".$current_order;
                echo "current_change_order>".$current_change_order;
                echo "nums>".$nums[$k];
                */
                if (($current_storage - $current_order - $current_change_order) < $nums[$k]) {
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - the quantity '.$goods_cached[$good_id].' / '.$good_colors_cached[$good_colors[$k]].' is not enough in this Warehouse!</div>';
                    exit;
                }

            }
            // change Retailer
            else {

                $whereImei = $QImei->getAdapter()->quoteInto('distributor_id = ?', $distributor_id1);
                $checked_result = $QImei->fetchAll($whereImei);

                if ( $checked_result->count() < $nums[$k] ){
                    echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
                    echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
                    echo '<div class="alert alert-error">Failed - the quantity '.$goods_cached[$good_id].' / '.$good_colors_cached[$good_colors[$k]].' is not enough!</div>';
                    exit;
                }

            }

            $data = array(
                'changed_id'    => $id,
                'cat_id'        => $cat_ids[$k],
                'good_id'       => $good_id,
                'good_color'    => $good_colors[$k],
                'num'           => $nums[$k],
                'new_id'        => ($is_changed_wh==1 ? $warehouse_id2 : $distributor_id2),
                'old_id'        => ($is_changed_wh==1 ? $warehouse_id1 : $distributor_id1),
                'created_at'    => date('Y-m-d H:i:s'),
                'created_by'    => $userStorage->id,
                'changed_sn'    => $changed_sn,
            );

            
            
        if (isset($ids[$k]) and $ids[$k]){
                $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('id = ?', $ids[$k]);
                $QChangeSalesProduct->update($data, $whereChangeSalesProduct);
                $new_detail_ids[] = $ids[$k];
            }
            else
                $new_detail_ids[] = $QChangeSalesProduct->insert($data);

        }       

        

        // xoa cac record cu
        if ($old_detail_ids){
            $diff = array_diff($old_detail_ids, $new_detail_ids);

            if ($diff){
                $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('id IN (?)', $diff);
                $QChangeSalesProduct->delete($whereChangeSalesProduct);
            }
        }

        $db->commit();

        $info = 'CHANGE ORDER: ' . $changed_sn;
        $QLog->insert( array (
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => date('Y-m-d H:i:s'),
        ) );

        $flashMessenger->setNamespace('success')->addMessage('Done!');

        echo '<script>parent.location.href="/warehouse/requisition-sales-list"</script>';
        exit;

    } catch (Exception $e){
        $db->rollback();

        echo '<script>window.parent.document.getElementById("iframe").style.display = \'block\';</script>';
        echo '<script>window.parent.document.getElementById("iframe").height = \'40px\';</script>';
        echo '<div class="alert alert-error">Failed - '.$e->getMessage().'</div>';
        exit;
    }
}

if ($id){
    if (!$changeSalesOrder){
        $flashMessenger->setNamespace('error')->addMessage('Change Order is invalid!');
        $this->redirect('/warehouse/requisition-sales-list');
    }

    if ($changeSalesOrder['status'] != CHANGE_ORDER_STATUS_PENDING){
        $flashMessenger->setNamespace('error')->addMessage('Status is invalid!');
        $this->redirect('/warehouse/requisition-sales-list');
    }
}

if ($id){
    $whereChangeSalesProduct = $QChangeSalesProduct->getAdapter()->quoteInto('changed_id = ?', $id);
    $changeSalesProduct = $QChangeSalesProduct->fetchAll($whereChangeSalesProduct);
    $this->view->changeSalesProduct = $changeSalesProduct;
}

$this->view->changeSalesOrder = $changeSalesOrder;
 $this->view->changeSalesList = $changeSalesList;
$this->view->hideFields = array(
    'sns',
    'sns_receive',
    'sns_lost',
    'num_receive',
    'num_lost',
    'sns_digital_receive',
    'sns_digital',
    'sns_digital_lost'
);

