<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

    $id                           = $this->getRequest()->getParam('id');
    $dis_id                       = $this->getRequest()->getParam('dis_id');
    $business_date                = $this->getRequest()->getParam('business_date');
    $finance_client               = $this->getRequest()->getParam('finance_client');
    $refund_dealer                = $this->getRequest()->getParam('refund_dealer');
    $refund_type                  = $this->getRequest()->getParam('refund_type');
    $my_bank                  	  = $this->getRequest()->getParam('my_bank');
    $amount                  	  = $this->getRequest()->getParam('amount');
    $remark                  	  = $this->getRequest()->getParam('remark');

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $db = Zend_Registry::get('db');

    $QSaleRefund = new Application_Model_SaleRefund();
    $QClientCode = new Application_Model_ClientCode();
    $QContactDetail = new Application_Model_ContactDetail();
    $QDistributior = new Application_Model_Distributor();
    $QStore = new Application_Model_Store();

    if($id){

        $saleRefundRowSet = $QSaleRefund->find($id);
        $sale_refund       = $saleRefundRowSet->current();

        if (!$sale_refund) {
            $flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍ່ຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
            $this->_redirect(HOST.'finance/sale-refund');
        }

        try {

            $db->beginTransaction();

            $data = array(
                'finance_client_id'       => $finance_client,
                'refund_dealer'           => $refund_dealer,
                'refund_type'             => $refund_type,
                'my_bank'                 => $my_bank,
                'amount'                  => $amount,
                'business_date'           => $business_date,
                'remark'                  => $remark,
                'updated_at'              => date('Y-m-d H:i:s'),
                'updated_by'              => $userStorage->id,
            );

            $where = $QSaleRefund->getAdapter()->quoteInto('id = ?', $id);
            $QSaleRefund->update($data,$where);

            if(isset($store) && $store) {

                $store_id_a = $refund_dealer;
                $distributor_id_a = $store->d_id;

            }else{

                $where = $QDistributior->getAdapter()->quoteInto('agent_warehouse_id =?',$refund_dealer);
                $distributor_up = $QDistributior->fetchRow($where);

                $store_id_a = null;
                $distributor_id_a = $distributor_up->id;
            }


            $data_contact_detail = array(
                'store_id'          => $store_id_a,
                'd_id'              => $distributor_id_a,
                'finance_client_id' => $finance_client,
                'status'            => 1,
                'description'       => 'Sale Refund Amount '.$amount.' LAK',
                'amount'            => $amount,
                'updated_at'        => date('Y-m-d H:i:s'),
                'updated_by'        => $userStorage->id
            );

            $where_contact = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$sale_refund->code);
            $QContactDetail->update($data_contact_detail,$where_contact);

            $flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
            $db->commit(); 

        } catch (Exception $e) {

            $db->rollback();
            echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
            exit;
            
        }

    }else{

        try {

           $db->beginTransaction();

           $cusCode        = $QClientCode->find(10);
        $insertCode     = $cusCode[0]['next_code']; // ລະຫັດປະຈຸບັນ ( ຕົວອັກສອນນຳໜ້າ + ຕົວເລກ )
        $running_next   = $cusCode[0]['running_next']; // ລະຫັດຕົວເລກປະຈຸບັນ
        $running_now    = $cusCode[0]['running_now']; // ລະຫັດຕົວເລກກ່ອນໜ້າ
        $prefix         = $cusCode[0]['prefix']; // ໂຕອັນສອນນຳໜ້າ

        $data = array(
          'code'					=> $insertCode,
          'd_id'					=> $dis_id,
          'finance_client_id'		=> $finance_client,
          'refund_dealer'			=> $refund_dealer,
          'refund_type'			=> $refund_type,
          'my_bank'				=> $my_bank,
          'amount'				=> $amount,
          'business_date'			=> $business_date,
          'remark'				=> $remark,
          'created_at'			=> date('Y-m-d H:i:s'),
          'created_by'			=> $userStorage->id,
          'status'				=> 1
      );

        $QSaleRefund->insert($data);

        $next_running_code = $running_next + 1; 

        $data_code = array(
            'last_code'     => $prefix.''.$running_next,
            'next_code'     => $prefix.''.$next_running_code,
            'running_now'   => $running_next,
            'running_next'  => $next_running_code,
            'digital'       => $next_running_code,
            'updated_at'    => date('Y-m-d H:i:s')
        );

        $where = $QClientCode->getAdapter()->quoteInto('id = ?', 10);
        $QClientCode->update($data_code,$where);

        // Check Is Store
        $storeRowSet = $QStore->find($refund_dealer);
        $store       = $storeRowSet->current();

        if(isset($store) && $store) {

            $store_id_a = $refund_dealer;
            $distributor_id_a = $store->d_id;

        }else{

            $where = $QDistributior->getAdapter()->quoteInto('agent_warehouse_id =?',$refund_dealer);
            $distributor_up = $QDistributior->fetchRow($where);

            $store_id_a = null;
            $distributor_id_a = $distributor_up->id;
        }


        $data_contact_detail = array(
            'type'              => 4,
            'doc_no'            => $insertCode,
            'store_id'          => $store_id_a,
            'd_id'              => $distributor_id_a,
            'finance_client_id' => $finance_client,
            'status'            => 1,
            'description'       => 'Sale Refund Amount '.$amount.' LAK',
            'amount'            => $amount,
            'created_at'        => date('Y-m-d H:i:s'),
            'created_by'        => $userStorage->id
        ); 

        $QContactDetail->insert($data_contact_detail);

        $flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
        $db->commit(); 

    } catch (Exception $e) {
    	
    	$db->rollback();
        echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
        exit;

    }
}

echo '<script>parent.location.href="/finance/sale-refund"</script>';
exit;

}

?>