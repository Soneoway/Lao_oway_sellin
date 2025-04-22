<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST'){

    $id                       = $this->getRequest()->getParam('id');
    $warehouse_id             = $this->getRequest()->getParam('warehouse_id');
    $store_id                 = $this->getRequest()->getParam('store_id');
    $finance_client           = $this->getRequest()->getParam('finance_client');
    $stm_type                 = $this->getRequest()->getParam('stm_type');
    $transfer_type            = $this->getRequest()->getParam('transfer_type');
    $remitted_by              = $this->getRequest()->getParam('remitted_by');
    $amount                   = $this->getRequest()->getParam('amount');
    $serial_number            = $this->getRequest()->getParam('serial_number');
    $finance_client_code      = $this->getRequest()->getParam('finance_client_code');
    $business_date            = $this->getRequest()->getParam('business_date');
    $settlement               = $this->getRequest()->getParam('settlement');
    $pay_date                 = $this->getRequest()->getParam('pay_date');
    $remark                   = $this->getRequest()->getParam('remark');
    $bank_account             = $this->getRequest()->getParam('bank_account');
    $image                    = $this->getRequest()->getParam('images');

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $db = Zend_Registry::get('db');

    $QClientCode = new Application_Model_ClientCode();
    $QSaleReceipt = new Application_Model_SaleReceipt();
    $QDistributior = new Application_Model_Distributor();
    $QStore = new Application_Model_Store();
    $QFinanceClient = new Application_Model_FinanceClient();
    $QAccountOrg = new Application_Model_AccountingOrganization();
    $QPaySlip = new Application_Model_PaySlip();
    $QContactDetail = new Application_Model_ContactDetail();
    $QWarehouse = new Application_Model_Warehouse();

    $images = $_FILES['images'];

    if($warehouse_id) {
        $where = $QDistributior->getAdapter()->quoteInto('agent_warehouse_id =?',$warehouse_id);
        $warehouse_d_id = $QDistributior->fetchRow($where);

        $where2 = $QFinanceClient->getAdapter()->quoteInto('distributor_y_id =?',$warehouse_d_id->id);
        $financeClient = $QFinanceClient->fetchRow($where2);

        $distributor_id = $warehouse_d_id->id;
        $distributor_my = $financeClient->distributor_m_id;
    }

    if($store_id) {
        $where = $QStore->getAdapter()->quoteInto('id =?',$store_id);
        $store_d_id = $QStore->fetchRow($where);

        $where2 = $QFinanceClient->getAdapter()->quoteInto('distributor_y_id =?',$store_d_id->d_id);
        $financeClient = $QFinanceClient->fetchRow($where2);

        $distributor_id = $store_d_id->d_id;
        $distributor_my = $financeClient->distributor_m_id;
    }

    if($finance_client) {
        $where = $QFinanceClient->getAdapter()->quoteInto('id =?',$finance_client);
        $financeClient = $QFinanceClient->fetchRow($where);

        $where2 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$financeClient->distributor_y_id);
        $accountOrgMy = $QAccountOrg->fetchRow($where2);

        $where3 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$financeClient->distributor_m_id);
        $accountOrgYou = $QAccountOrg->fetchRow($where3);

        $account_org_my = $accountOrgMy->id;
        $account_org_you = $accountOrgYou->id;
        $finance_client_code = $financeClient->mnemonic_code;
    }

    if($id) {

        $saleReceiptRowSet = $QSaleReceipt->find($id);
        $sale_receipt       = $saleReceiptRowSet->current();

        if (!$sale_receipt) {
            $flashMessenger->setNamespace('error')->addMessage('ລະຫັດບໍ່ຖືກຕ້ອງ. ກະລຸນາກວດຄືນເເລ້ວລອງອີກຄັ້ງ !.');
            $this->_redirect(HOST.'finance/sale-receipt');
        }

        try {

            $db->beginTransaction();

            $where = $QSaleReceipt->getAdapter()->quoteInto('id =?',$id);
            $sale_receipt = $QSaleReceipt->fetchRow($where);

            $data = array(
                'd_id'                  => $distributor_id,
                'store_id'              => $store_id,
                'warehouse_id'          => $warehouse_id,
                'finance_client'        => $finance_client,
                'stm_type'              => $stm_type,
                'bank_account'          => $bank_account,
                'transfer_type'         => $transfer_type,
                'remitted_by'           => $remitted_by,
                'amount'                => $amount,
                'serial_number'         => $serial_number,
                'dis_my'                => $distributor_my,
                'dis_you'               => $distributor_id,
                'account_org_my'        => $account_org_my,
                'account_org_you'       => $account_org_you,
                'finance_client_code'   => $finance_client_code,
                'business_date'         => $business_date,
                'settlement'            => $settlement,
                'pay_date'              => $pay_date,
                'remark'                => $remark,
                'updated_at'            => date('Y-m-d H:i:s'),
                'updated_by'            => $userStorage->id,
            );


            $QSaleReceipt->update($data,$where);


            for ($i = 0; $i < count($images['name']); $i++) {
                $fileName = $images['name'][$i];
                $fileTmpName = $images['tmp_name'][$i];
                $fileSize = $images['size'][$i];
                $error = $images['error'][$i];

                $upload = new Zend_File_Transfer();

                $uploadPath = 'upload/pay_slip/'.$sale_receipt->document_no.'/';

                if (!file_exists('upload/pay_slip/'.$sale_receipt->document_no.'/')) {
                    mkdir('upload/pay_slip/'.$sale_receipt->document_no.'/', 0775, true);
                }
                $upload->setDestination($uploadPath);

                $upload->receive();
                chmod($uploadPath. DIRECTORY_SEPARATOR .$images['name'][$i], 777);


                $slip = array(
                    'sale_receipt_no'   => $sale_receipt->document_no,
                    'photo'             => $images['name'][$i],
                    'parth'             => $uploadPath,
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s')
                );

                $QPaySlip->insert($slip);

            }

            if(isset($store_id) && $store_id) {
                $store_id_a   = $store_id;
                $distributor_id_a  = null;
            }else{
                $store_id_a   = null;

                $where = $QDistributior->getAdapter()->quoteInto('agent_warehouse_id = ?',$warehouse_id);
                $distributor_up = $QDistributior->fetchRow($where);

                $distributor_id_a = $distributor_up->id;

            }


            $data_contact_detail = array(
                'store_id'              => $store_id_a,
                'd_id'                  => $distributor_id_a,
                'finance_client_id'     => $finance_client,
                'status'                => 1,
                'description'           => 'Sale Receipt Add '.$amount.' LAK ',
                'amount'                => $amount,
                'updated_at'            => date('Y-m-d H:i:s'),
                'updated_by'            => $userStorage->id
            );

            $where_contact = $QContactDetail->getAdapter()->quoteInto('doc_no =?',$sale_receipt->document_no);
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

            $cusCode        = $QClientCode->find(9);
        $insertCode     = $cusCode[0]['next_code']; // ລະຫັດປະຈຸບັນ ( ຕົວອັກສອນນຳໜ້າ + ຕົວເລກ )
        $running_next   = $cusCode[0]['running_next']; // ລະຫັດຕົວເລກປະຈຸບັນ
        $running_now    = $cusCode[0]['running_now']; // ລະຫັດຕົວເລກກ່ອນໜ້າ
        $prefix         = $cusCode[0]['prefix']; // ໂຕອັນສອນນຳໜ້າ

        $data = array(
            'document_no'           => $insertCode,
            'd_id'                  => $distributor_id,
            'store_id'              => $store_id,
            'warehouse_id'          => $warehouse_id,
            'finance_client'        => $finance_client,
            'stm_type'              => $stm_type,
            'bank_account'          => $bank_account,
            'transfer_type'         => $transfer_type,
            'remitted_by'           => $remitted_by,
            'amount'                => $amount,
            'serial_number'         => $serial_number,
            'dis_my'                => $distributor_my,
            'dis_you'               => $distributor_id,
            'account_org_my'        => $account_org_my,
            'account_org_you'       => $account_org_you,
            'finance_client_code'   => $finance_client_code,
            'business_date'         => $business_date,
            'settlement'            => $settlement,
            'pay_date'              => $pay_date,
            'remark'                => $remark,
            'created_at'            => date('Y-m-d H:i:s'),
            'created_by'            => $userStorage->id,
            'status'                => 1,
        );


        $QSaleReceipt->insert($data);

        for ($i = 0; $i < count($images['name']); $i++) {
            $fileName = $images['name'][$i];
            $fileTmpName = $images['tmp_name'][$i];
            $fileSize = $images['size'][$i];
            $error = $images['error'][$i];

            $upload = new Zend_File_Transfer();

            $uploadPath = 'upload/pay_slip/'.$insertCode.'/';

            if (!file_exists('upload/pay_slip/'.$insertCode.'/')) {
                mkdir('upload/pay_slip/'.$insertCode.'/', 0775, true);
            }
            $upload->setDestination($uploadPath);

            $upload->receive();
            chmod($uploadPath. DIRECTORY_SEPARATOR .$images['name'][$i], 777);


            $slip = array(
                'sale_receipt_no'   => $insertCode,
                'photo'             => $images['name'][$i],
                'parth'             => $uploadPath,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s')
            );

            $QPaySlip->insert($slip);

        }

        if(isset($store_id) && $store_id) {
            $store_id_a   = $store_id;
            $distributor_id_a  = null;
        }else{
            $store_id_a   = null;

            $where = $QDistributior->getAdapter()->quoteInto('agent_warehouse_id = ?',$warehouse_id);
            $distributor_up = $QDistributior->fetchRow($where);

            $distributor_id_a = $distributor_up->id;

        }

        $data_contact_detail = array(
            'type'                  => 3,
            'doc_no'                => $insertCode,
            'store_id'              => $store_id_a,
            'd_id'                  => $distributor_id_a,
            'finance_client_id'     => $finance_client,
            'status'                => 1,
            'description'           => 'Sale Receipt Add '.$amount.' LAK ',
            'amount'                => $amount,
            'created_at'            => date('Y-m-d H:i:s'),
            'created_by'            => $userStorage->id
        );

        $QContactDetail->insert($data_contact_detail);

        $next_running_code = $running_next + 1; 

        $data_code = array(
            'last_code'     => $prefix.''.$running_next,
            'next_code'     => $prefix.''.$next_running_code,
            'running_now'   => $running_next,
            'running_next'  => $next_running_code,
            'digital'       => $next_running_code,
            'updated_at'    => date('Y-m-d H:i:s')
        );

        $where = $QClientCode->getAdapter()->quoteInto('id = ?', 9);
        $QClientCode->update($data_code,$where);

        $flashMessenger->setNamespace('success')->addMessage('ບັນທຶກຂໍ້ມູນສຳເລັດ. !');
        $db->commit(); 


    } catch (Exception $e) {

        $db->rollback();
        echo '<script> parent.palert("ມີຂໍຜິດພາດບາງຢ່າງເກິດຂື້ນ , ກະລຸນາຕິດຕໍ່ຫາ Admin."); </script>';
        exit;

    }
}

echo '<script>parent.location.href="/finance/sale-receipt"</script>';
exit;

    // Zend_Debug::dump($_FILES['images']); exit;
}

?> 