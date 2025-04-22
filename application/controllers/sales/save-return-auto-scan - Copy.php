<?php
$flashMessenger = $this->_helper->flashMessenger;

//print_r($_POST);die;

if ($this->getRequest()->getMethod() == 'POST'){
    
    $ids            = $this->getRequest()->getParam('ids');
    $cat_ids        = $this->getRequest()->getParam('cat_id');
    $good_ids       = $this->getRequest()->getParam('good_id');
    $good_colors    = $this->getRequest()->getParam('good_color');
    $nums           = $this->getRequest()->getParam('num');
    $prices         = $this->getRequest()->getParam('price');
    $totals         = $this->getRequest()->getParam('total');
    $texts          = $this->getRequest()->getParam('text');
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    $warehouse_id   = $this->getRequest()->getParam('warehouse_id');
    $imeis          = $this->getRequest()->getParam('imei');
    $digitals       = $this->getRequest()->getParam('digital');
    $invoice_sn     = $this->getRequest()->getParam('invoice_sn');

    $sn             = $this->getRequest()->getParam('sn');
    $create_cn      = $this->getRequest()->getParam('create_cn');
    $active_cn      = $this->getRequest()->getParam('active_cn');
    $return_type      = $this->getRequest()->getParam('return_type');

    $data_phone_return      = $this->getRequest()->getParam('data_phone_return');
    $obj = json_decode($data_phone_return, true);
    print_r($obj['result_market']);die;
    foreach ($obj['result_market'] as $k=>$item){

        echo $item['distributor_id'].',';
    }

    die;
    if(!$create_cn){
        $return_type = null;
    }

    $isbatch        = 1;
    $isbacks        = 1;

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $QLog = new Application_Model_Log();
    $QGood = new Application_Model_Good();
    $goods_cache = $QGood->get_cache();
    $QGoodColor = new Application_Model_GoodColor();
    $good_colors_cache = $QGoodColor->get_cache();


        try{

            $db = Zend_Registry::get('db');

            $db->beginTransaction();

            $imei_list = explode("\n", trim($imeis));
            $imei_list = array_filter(array_unique(array_map('trim', $imei_list)));
            //check duplicate
            $array_good_color = array();
            $count_input = 0;
            $QGoodCategory = new Application_Model_GoodCategory();
            foreach ($nums as $k=>$num){
                //only get phone :) them digital
                if ($cat_ids[$k] == $QGoodCategory->get_phone_id() || $cat_ids[$k] == DIGITAL_CAT_ID )
                    $count_input += $num;
            }

                if (count($imei_list) != $count_input){
                    echo '<script>
                                parent.palert("Please input valid number of IMEI / Digital SN.");
                            </script>';
                    exit;
                }


            $QMarket = new Application_Model_Market();
            $QGood = new Application_Model_Good();
            $QGoodColor = new Application_Model_GoodColor();

            $cached_goods = $QGood->get_cache();
            $cached_good_colors = $QGoodColor->get_cache();

            $db = Zend_Registry::get('db');

            foreach ($cat_ids as $k=>$cat_id){
                //check number
                $select = $db->select()
                    ->from(array('p' => 'market'),
                        array('total_num'=>'SUM(p.num)'))
                    ->join(array('d' => 'distributor'), 'd.id=p.d_id', array());
                $select->where('good_id = ?', $good_ids[$k]);
                $select->where('good_color = ?', $good_colors[$k]);
                $select->where('d.id = ? OR d.parent = ?', $distributor_id);
                $select->where('isbacks = ?', 0);
                $select->where('outmysql_time IS NOT NULL', null);
                $select->where('outmysql_time <> \'\'', null);
                $select->where('outmysql_time <> 0', null);
                
                $total_num = $db->fetchOne($select);

                if (!$total_num){
                    echo '<script>parent.palert("This '.$cached_goods[$good_ids[$k]].' / '.$cached_good_colors[$good_colors[$k]].' is not exported to this distributor.");</script>';
                    exit;
                } else {
                    //check return
                    $select = $db->select()
                        ->from(array('p' => 'market'),
                            array('total_num'=>'IFNULL(SUM(p.num),0)'));
                    $select->where('good_id = ?', $good_ids[$k]);
                    $select->where('good_color = ?', $good_colors[$k]);
                    $select->where('d_id = ?', $distributor_id);
                    $select->where('isbacks = ?', 1);
                    $select->where('pay_time IS NOT NULL', null);
                    $select->where('pay_time <> \'\'', null);
                    $select->where('pay_time <> 0', null);

                    $total_returned = $db->fetchOne($select);

                    if ( ( $total_num - $total_returned ) < $nums[$k] ) {
                        echo '<script>parent.palert("This '.$cached_goods[$good_ids[$k]].' / '.$cached_good_colors[$good_colors[$k]].' quantity\'s is not enough.");</script>';
                        exit;
                    }
                }
            }

            $QImeiReturn = new Application_Model_ImeiReturn();
            $QProductReturn = new Application_Model_ProductReturn();
            $QDistributor = new Application_Model_Distributor();
            $QImei = new Application_Model_Imei();
            $QDigital = new Application_Model_DigitalSn();
            $QDigitalReturn = new Application_Model_DigitalSnReturn();
            //check imei existed in system

            //delete in IMEI return
            if (count($imei_list)==$count_input){
                $where = $QImeiReturn->getAdapter()->quoteInto('return_sn =?', $sn);
                $QImeiReturn->delete($where);
            }

            foreach ($imei_list as $imei){          

                $imei = trim($imei);
                $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                $imei_check = $QImei->fetchRow($where);  
                /*
                echo '<script>parent.palert(">>'.$imei_check['sales_sn'].'");</script>';
                exit;die;
                */
                if(!empty($imei_check))
                {
                    if (!isset($imei_check['distributor_id']) || !intval($imei_check['distributor_id'])){
                        echo '<script>parent.palert("This IMEI '.$imei.' is not existed in our system.");</script>';
                        exit;
                    }

                    $where = $QDistributor->getAdapter()->quoteInto('id = ?', intval($imei_check['distributor_id']));
                    $imei_distributor = $QDistributor->fetchRow($where);
                    if (!$imei_distributor) {
                        echo '<script>parent.palert("This distributor is not existed in our system.");</script>';
                        exit;
                    }

                    if ($imei_distributor['id'] != $distributor_id && $imei_distributor['parent'] != $distributor_id) {
                        echo '<script>parent.palert("This IMEI '.$imei.' does not belong to this distributor.");</script>';
                        exit;
                    }
                }
            }


            //get old ids
            $old_ids = $old_imeireturns_list = array();
            if ($sn) {
                $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                $old_sales = $QMarket->fetchAll($where);

                if ($old_sales){
                    foreach ($old_sales as $sale)
                        $old_ids[] = $sale->id;
                }

                if(!empty($imei_check))
                {
                    $where = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
                    $old_imeireturns = $QImeiReturn->fetchAll($where);

                    if ($old_imeireturns){
                        foreach ($old_imeireturns as $item)
                            $old_imeireturns_list[] = $item->imei_sn;
                    }
                }
            }

            $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);

            $distributor = $QDistributor->fetchRow($where);
            $rank = $distributor->rank;

            if (!$sn){
                $sn = date ( 'YmdHis' ) . substr ( microtime (), 2, 4 );
                $sn_ref=$QImeiReturn->getReturnOrderNo_Ref($sn);
                //$sn_ref=$this->getSalesOrderNo_Ref($sn);
            }

            foreach ($ids as $k=>$id){
                if (
                    isset($cat_ids[$k]) and $cat_ids[$k]
                    and isset($good_ids[$k]) and $good_ids[$k]
                    and isset($good_colors[$k]) and $good_colors[$k]
                    and isset($nums[$k]) and $nums[$k]
                    and isset($prices[$k]) and $prices[$k]
                    /*and isset($totals[$k]) and $totals[$k]*/
                ) {

                    if(in_array($good_ids[$k] . '_' . $good_colors[$k]. '_' . $invoice_sn[$k] , $array_good_color) and $cat_ids[$k] == PHONE_CAT_ID)
                    {
                        echo '<script>
                            parent.palert("Sorry, your input is duplicated , Model : ' . $goods_cache[$good_ids[$k]] . ' - ' . $good_colors_cache[$good_colors[$k]] . '");
                        </script>';
                        exit;
                     //   throw new Exception("Sorry, your input is dublicate, Model : " . $goods_cache[$good_ids[$k]] . " - " . $good_colors[$good_colors[$k]]);
                    }

                    $array_good_color[] = $good_ids[$k] . '_' . $good_colors[$k]. '_' . $invoice_sn[$k];

                    if(isset($invoice_sn[$k])){
                        $invoice_number = $invoice_sn[$k];
                    }else{$invoice_number='';}
                    $data = array(
                        'cat_id'        => $cat_ids[$k],
                        'good_id'       => $good_ids[$k],
                        'good_color'    => $good_colors[$k],
                        'num'           => $nums[$k],
                        'price'         => $prices[$k],
                        'total'         => $totals[$k],
                        'text'          => (isset($texts[$k]) ? $texts[$k] : null),
                        'price_clas'    => $rank,
                        'd_id'          => $distributor_id,
                        'isbatch'       => $isbatch,
                        'isbacks'       => $isbacks,
                        'backs_d_id'    => $warehouse_id,
                        'warehouse_id'  => $warehouse_id,
                        'invoice_number'    => $invoice_number,
                        'create_cn'     => $create_cn,
                        'active_cn'     => $active_cn,
                        'return_type'     => $return_type
                    );
                    //Tanong
                    if ($id){ //update
                        $where = $QMarket->getAdapter()->quoteInto('id = ?', $id);
                        $QMarket->update($data, $where);
                    } else { //insert
                        $data['add_time'] = date('Y-m-d H:i:s');
                        $data['user_id'] = $userStorage->id;
                        $data['sn'] = $sn;
                        $data['sn_ref'] = $sn_ref;
                        $QMarket->insert($data);
                    }

                                   
                }
            }


            $new_imeireturns_list = array_diff($imei_list, $old_imeireturns_list);

            if ($new_imeireturns_list){

                if(!empty($imei_check))
                {
                    foreach ($new_imeireturns_list as $imei) {
                        $imei = trim($imei);
                        $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                        $imei_check = $QImei->fetchRow($where);
                        $sales_sn = $imei_check['sales_sn'];        
     
                        $data = array(
                            'imei_sn' => trim($imei),
                            'warehouse_id' => $warehouse_id,
                            'return_sn' => $sn,
                            'sales_order_sn' => $sales_sn,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $userStorage->id,
                        );
                        $QImeiReturn->insert($data);
                    }
                }
            }

            //todo log
            $ip = $this->getRequest()->getServer('REMOTE_ADDR');
            $info .= $sn;

            $QLog->insert( array (
                'info' => $info,
                'user_id' => $userStorage->id,
                'ip_address' => $ip,
                'time' => date('Y-m-d H:i:s'),
            ) );

            $flashMessenger->setNamespace('success')->addMessage('Done!');

            //commit
            $db->commit();

        }catch (Exception $e){

            $db->rollback();

            echo '<script>
                    parent.palert("Cannot save, please try again!");
                  </script>';
            exit;
        }
    

}

echo '<script>parent.location.href="/sales/return-list"</script>';
exit;


