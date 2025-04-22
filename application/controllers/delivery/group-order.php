<?php

$this->_helper->layout->disableLayout();
$sn = $this->getRequest()->getParam('sn', array());

$sn = is_array($sn) ? array_unique($sn) : array();

try {
    if (!count($sn)) throw new Exception("Please choose at lease 1 order", 1);

    $QMarket = new Application_Model_Market();
    $QDistributor = new Application_Model_Distributor();
        $QShippingAmphures          = new Application_Model_ShippingAmphures();
        $QShippingDistricts         = new Application_Model_ShippingDistricts();
        $QShippingProvinces         = new Application_Model_ShippingProvinces();
        $QShippingZipcodes          = new Application_Model_ShippingZipcodes();
        $QShippingAddress           = new Application_Model_ShippingAddress();
    $distributors = array();
    $distributor_list = array();
    $distributor_sn = array();
    // $QOffice = new Application_Model_Office();
    // $office_cache = $QOffice->get_all_cache();
    // $QService = new Application_Model_Service();
    // $service_cache = $QService->get_all_cache();
    $weight = 0;

    foreach ($sn as $_key => $_sn) {
        $where = $QMarket->getAdapter()->quoteInto('sn = ?', $_sn);
        $market_check = $QMarket->fetchRow($where);

        $weight += My_Sale_Order::getWeight($_sn);

        if (!$market_check) throw new Exception("Order SN ".$_sn." not exists", 2);

        if (isset($market_check['office']) && $market_check['office'] && isset($office_cache[ $market_check['office'] ]))
            $distributor_sn[ $_key ] = array(
                'contact'      => $office_cache[ $market_check['office'] ]['contact'],
                'address'      => $office_cache[ $market_check['office'] ]['address'],
                'name'         => $office_cache[ $market_check['office'] ]['name'],
                'phone_number' => $office_cache[ $market_check['office'] ]['phone_number'],
                'district'     => $office_cache[ $market_check['office'] ]['district'],
            );

        if (isset($market_check['service']) && $market_check['service'] && isset($service_cache[ $market_check['service'] ]))
            $distributor_sn[ $_key ] = array(
                'contact'      => $service_cache[ $market_check['service'] ]['contact'],
                'address'      => $service_cache[ $market_check['service'] ]['address'],
                'name'         => $service_cache[ $market_check['service'] ]['name'],
                'phone_number' => $service_cache[ $market_check['service'] ]['phone_number'],
                'district'     => $service_cache[ $market_check['service'] ]['district'],
            );

        $distributors[ $_key ] = $market_check['d_id'];
    }
    if ($market_check['add_time'] > '2017-03-15 00:00:00') {
        if (isset($market_check['shipping_address']) && $market_check['shipping_address']){
            $shipping_address = $market_check['shipping_address'];

            $shipp_add = My_Address::genAddessDeliveryNote($shipping_address);
            //print_r($shipp_add);

        }

    foreach ($distributors as $_key => $_distributor) {
        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $_distributor);
        $distributor_check = $QDistributor->fetchRow($where);

        if (!$distributor_check) throw new Exception("Distributor not exists", 3);

        $distributor_list[ $_key ] = array(
            'id'       => intval($distributor_check['id']),
            'title'    => htmlspecialchars( $distributor_check['title'], ENT_QUOTES, 'UTF-8' ),
            //'address'  => htmlspecialchars( !empty( $distributor_check['add_tax'] ) ? My_String::trim($distributor_check['add_tax']) : $distributor_check['add'], ENT_QUOTES, 'UTF-8' ),
            'address'  => htmlspecialchars( !empty( $shipp_add['address'] ) ? My_String::trim($shipp_add['address']) : $shipp_add['address'], ENT_QUOTES, 'UTF-8' ),
            'name'     => htmlspecialchars( My_String::trim($distributor_check['name']), ENT_QUOTES, 'UTF-8' ),
            'district' => intval($distributor_check['district']),
            'phone'    => htmlspecialchars( My_String::trim($shipp_add['phone']), ENT_QUOTES, 'UTF-8' ),
            'address_id'    => $shipp_add['address_id'],
            // 'address_id'    => $shipp_add['address_id'],
        );

        // if (isset($distributor_sn[ $_key ])) {
        //     $distributor_list[ $_key ]['title']    .= ' ' . $distributor_sn[ $_key ]['name'];
        //     $distributor_list[ $_key ]['address']  = $distributor_sn[ $_key ]['address'];
        //     $distributor_list[ $_key ]['phone']    = $distributor_sn[ $_key ]['phone_number'];
        //     $distributor_list[ $_key ]['name']     = $distributor_sn[ $_key ]['contact'];
        //     $distributor_list[ $_key ]['district'] = $distributor_sn[ $_key ]['district'];
        // }

    }
    }else{
    $distributors = is_array($distributors) ? array_unique($distributors) : array();
        if (isset($market_check['delivery_address']) && $market_check['delivery_address']){
            $delivery_address = $market_check['delivery_address'];
        }

    foreach ($distributors as $_key => $_distributor) {
        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $_distributor);
        $distributor_check = $QDistributor->fetchRow($where);

        if (!$distributor_check) throw new Exception("Distributor not exists", 3);

        $distributor_list[ $_key ] = array(
            'id'       => intval($distributor_check['id']),
            'title'    => htmlspecialchars( $distributor_check['title'], ENT_QUOTES, 'UTF-8' ),
            //'address'  => htmlspecialchars( !empty( $distributor_check['add_tax'] ) ? My_String::trim($distributor_check['add_tax']) : $distributor_check['add'], ENT_QUOTES, 'UTF-8' ),
            'address'  => htmlspecialchars( !empty( $delivery_address ) ? My_String::trim($delivery_address) : $distributor_check['add_tax'], ENT_QUOTES, 'UTF-8' ),
            'name'     => htmlspecialchars( My_String::trim($distributor_check['name']), ENT_QUOTES, 'UTF-8' ),
            'district' => intval($distributor_check['district']),
            'phone'    => htmlspecialchars( My_String::trim($distributor_check['tel']), ENT_QUOTES, 'UTF-8' ),
        );

        if (isset($distributor_sn[ $_key ])) {
            $distributor_list[ $_key ]['title']    .= ' ' . $distributor_sn[ $_key ]['name'];
            $distributor_list[ $_key ]['address']  = $distributor_sn[ $_key ]['address'];
            $distributor_list[ $_key ]['phone']    = $distributor_sn[ $_key ]['phone_number'];
            $distributor_list[ $_key ]['name']     = $distributor_sn[ $_key ]['contact'];
            $distributor_list[ $_key ]['district'] = $distributor_sn[ $_key ]['district'];
        }

    }
    }


    $this->view->distributor_list = $distributor_list;
    $this->view->weight = $weight;
    $this->view->dateNewAdd = $market_check['add_time'];

    $QRegion = new Application_Model_RegionalMarket();
    $this->view->provinces = $QRegion->nget_all_province_cache();

    $QStaff = new Application_Model_DeliveryMan();
    $this->view->staffs = $QStaff->get_cache();

    $QWarehouse = new Application_Model_Warehouse();
    $this->view->warehouses = $QWarehouse->get_cache();
} catch (Exception $e) {
    $this->view->error = sprintf("[%s] %s", $e->getCode(), $e->getMessage());
}

$this->_helper->viewRenderer->setRender('partials/group-order');
