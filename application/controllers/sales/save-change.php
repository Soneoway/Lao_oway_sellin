<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST') {

    $ids                        = $this->getRequest()->getParam('ids');
    $cat_ids                    = $this->getRequest()->getParam('cat_id');
    $good_ids                   = $this->getRequest()->getParam('good_id');
    $good_colors                = $this->getRequest()->getParam('good_color');
    $nums                       = $this->getRequest()->getParam('num');
    $prices                     = $this->getRequest()->getParam('price');
    $totals                     = $this->getRequest()->getParam('total');
    $texts                      = $this->getRequest()->getParam('text');
    $distributor_id             = $this->getRequest()->getParam('distributor_id');
    $distributor_name           = $this->getRequest()->getParam('d_id');
    $imeis                      = $this->getRequest()->getParam('imei');
    $digitals                   = $this->getRequest()->getParam('digital');
    $invoice_sn                 = $this->getRequest()->getParam('invoice_sn');
    $remark                     = $this->getRequest()->getParam('remark');
    $change_to                  = $this->getRequest()->getParam('change_to');
    $sn                         = $this->getRequest()->getParam('sn');
    $create_cn                  = $this->getRequest()->getParam('create_cn');
    $active_cn                  = $this->getRequest()->getParam('active_cn');
    $return_type                = $this->getRequest()->getParam('return_type');
    $dis_id                     = $this->getRequest()->getParam('dis_id');
    $wh_id                      = $this->getRequest()->getParam('wh_id');
    $store_id                   = $this->getRequest()->getParam('store_id');
    $data_phone_return          = $this->getRequest()->getParam('data_phone_return');

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $obj = json_decode($data_phone_return, true);
    $array_phone = $obj['result_market'];
    $array_imei = $obj['result_imei'];

    foreach ($obj['result_market'] as $k=>$item){
        $array[$k] = $item['distributor_id'];
    }


    $array_distributor = array_unique($array);

    $QGood                  = new Application_Model_Good();
    $QGoodColor             = new Application_Model_GoodColor();
    $cached_goods           = $QGood->get_cache();
    $cached_good_colors     = $QGoodColor->get_cache();
    $QProductReturn         = new Application_Model_ProductReturn();
    $QDistributor           = new Application_Model_Distributor();
    $QImei                  = new Application_Model_Imei();
    $QChangeImei            = new Application_Model_ChangeImeiDistibutor();

    $where = $QDistributor->getAdapter()->quoteInto('id = ?',$dis_id);
    $check_distibutor = $QDistributor->fetchRow($where);
    $new_warehouse_id = $check_distibutor['warehouse_id'];

    $isbatch        = 1;
    $isbacks        = 1;

    $QLog = new Application_Model_Log();
    $QGood = new Application_Model_Good();
    $goods_cache = $QGood->get_cache();
    $QGoodColor = new Application_Model_GoodColor();
    $good_colors_cache = $QGoodColor->get_cache();

    if (is_array($ids)) {

        try {

            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            $imei_list = explode("\n", trim($imeis));
            $imei_list = array_filter(array_unique(array_map('trim', $imei_list)));

            $imei_arr = array();
            foreach ($imei_list as $key => $value) {
                $imei_arr[$key] = $value;
            }

            
            for ($i=0; $i < count($imei_arr); $i++) { // Loop Imei Check
                $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei_arr[$i]);
                $check_imei = $QImei->fetchRow($where);

                 // Imei Not In System
                if(!$check_imei) {
                    echo '<script> parent.palert(" Imei ບໍຖືກຕ້ອງ ຫຼື ບໍ່ຢູ່ໃນລະບົບ , ກະລຸນາກວດຄືນເເລ້ວລອງໃໝ່ອີກຄັ້ງ ");</script>';exit;
                    return false;
                }

                // Admin & After Sale Service ID
                if(!in_array($userStorage->id, array(1,724))) {

                // Check duplicate Warehouse
                    if($check_imei['warehouse_id'] != $new_warehouse_id) {
                        echo '<script> parent.palert(" IMei ບໍ່ໄດ້ຢູ່ໃນຮ້ານຄ້າທີ່ຂື້ນກັບສາງດຽວກັນບໍ່ສາມາດຢ້າຍໄດ້. ");</script>'; exit;
                        return false;
                    }
                }

                // Check Imei In OPPO Brand Shop Service [ 10752,5419 ] : Service 1 , [ 10753,5420 ] : Service 2
                if(in_array($check_imei['store_id'],array(10752,10753)) || in_array($distributor_name,array(5419,5420))) {
                    echo '<script> parent.palert(" IMei ຢູ່ໃນຮ້ານຄ້າຂອງບໍລິສັດ OPPO Brand Shop Service, ບໍ່ສາມາດຢ້າຍໄດ້ ກະລຸນາຕິດຕໍ່ຫາ Admin. ");</script>'; exit;
                    return false;
                }

                // Check Imei In Catty Visual Store [ 10672, 5967 ]
		if(!in_array($userStorage->id, array(1))) {
                if(in_array($check_imei['store_id'],array(10672)) || in_array($distributor_name,array(5967))) {
                    echo '<script> parent.palert(" IMei ຢູ່ໃນຮ້ານຄ້າຂອງບໍລິສັດ OPPO Brand Shop Service, ບໍ່ສາມາດຢ້າຍໄດ້ ກະລຸນາຕິດຕໍ່ຫາ Admin. ");</script>'; exit;
                    return false;
                }
		}

                // Case Imei In Warehouse
                if($check_imei['out_date'] == '') {
                    echo '<script> parent.palert("IMei ຍັງບໍ່ຖືກຂາຍອອກຈາກສາງ , ບໍ່ສາມາດຢ້າຍເຄືອງໄດ້");</script>';exit;
                    return false;
                }

                // Check Change To OPPO Brand Shop Service [ 10752,5419 ] : Service 1 , [ 10753,5420 ] : Service 2
                if(in_array($store_id,array(10752,10753)) || in_array($store_id,array(5419,5420))) {
                    echo '<script> parent.palert(" ບໍສາມາດຢ້າຍ IMei ໄປຮ້ານຄ້າຂອງບໍລິສັດ OPPO Brand Shop ໄດ້. ");</script>'; exit;
                    return false;
                }

            }// end Loop Imei Check

                foreach ($array_imei as $t => $item_imei){

                    $imei           = trim($item_imei['imei_sn']);
                    $where          = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                    $imei_check     = $QImei->fetchRow($where);

                    $sn             = date ( 'YmdHis' ) . substr ( microtime (), 2, 4 );
                    $do_number      = $QChangeImei->getchangeOrderNo_do($sn);

                    $imei_d_id           = $imei_check['distributor_id'];
                    $imei_w_id           = $imei_check['warehouse_id'];
                    $imei_store_id       = $imei_check['store_id'];
                    $good                = $imei_check['good_id'];
                    $color               = $imei_check['good_color'];
                    $price               = $imei_check['out_price'];

                    $imei_invoice        = $item['invoice_number'];

                    if($change_to == 1) {

                        $where          = $QDistributor->getAdapter()->quoteInto('id =?',$dis_id);
                        $dis_data       = $QDistributor->fetchRow($where);

                        // Insert To Change imei distributor
                        $data = array(
                            'invoice_number'        => $imei_invoice,
                            'do_number'             => $do_number,
                            'imei_sn'               => trim($imei),

                            'old_distibutor'        => $imei_d_id, // Old Distributor ID
                            'warehouse_id'          => $imei_w_id, // Old Warehouse ID
                            'old_store'             => $imei_store_id, // Old Store ID

                            'new_distibutor'        => $dis_id,
                            'change_warehouse'      => $dis_data['warehouse_id'], // To Warehouse
                            'new_store'             => $store_id,

                            'good'                  => $good,
                            'color'                 => $color,
                            'price'                 => $price,

                            'remark'                => $remark,
                            'status'                => 1,
                            'change_by'             => $userStorage->id,
                            'change_at'             => date('y-m-d H:m:s'),
                        );

                        $QChangeImei->insert($data);

                        // Update Table Imei
                        $data2 = array(
                            'distributor_id'        => $dis_id,
                            'warehouse_id'          => $dis_data['warehouse_id'], // To Warehouse
                            'store_id'              => $store_id,
                            'out_date'              => date('y-m-d H:m:s'),
                        );

                        $where2 = $QImei->getAdapter()->quoteInto('imei_sn = ?', trim($imei));
                        $QImei->update($data2,$where2);

                    } elseif($change_to == 2) {

                        // Insert To Change imei Distributor
                        $data = array(
                            'invoice_number'    => $imei_invoice,
                            'do_number'         => $do_number,
                            'imei_sn'           => trim($imei),

                            'warehouse_id'      => $imei_w_id, // Old Warehouse ID
                            'old_store'         => $imei_store_id, // Old Store ID
                            'old_distibutor'    => $d_id,


                            'new_distibutor'    => null,
                            'new_store'         => null,
                            'change_warehouse'  => $wh_id, // On Selected Warehouse

                            'good'              => $good,
                            'color'             => $color,
                            'price'             => $price,

                            'remark'            => $remark,
                            'status'            => 1,
                            'change_by'         => $userStorage->id,
                            'change_at'         => date('y-m-d H:m:s'),
                        );

                        $QChangeImei->insert($data);

                        // Update Table Imei
                        $data2 = array(
                            'distributor_id' => null,
                            'warehouse_id'   => $wh_id, // On Selected Warehouse
                            'store_id'       => null,
                            'out_date'       => null,
                            'sales_sn'       => null,
                            'sales_id'       => null,
                            'out_price'      => null,
                            'out_user'       => null,
                        );

                        $where2 = $QImei->getAdapter()->quoteInto('imei_sn = ?', trim($imei));
                        $QImei->update($data2,$where2);

                    }


                }

            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $db->commit(); 

        }catch (Exception $e){
            $db->rollback();

            echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
            exit;

        }
    } else {
        echo '<script>
        parent.palert(" ມີຂໍ້ມູນບາງຢ່າງບໍ່ຖືກຕ້ອງ, ກະລຸນາກວດຄືນເເລ້ວລອງໃໝ່ອີກຄັ້ງ. ");
        </script>';
        exit;
    }
}

echo '<script>parent.location.href="/sales/change-list"</script>';
exit;
