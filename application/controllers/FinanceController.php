<?php

class FinanceController extends My_Controller_Action
{
    //Tanong

    /*public function returnBoxNumberImeiConfirmListAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'return-box-number-imei-confirm-list.php';
    }*/

    public function viewCnPdfFileAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $data_sn = $this->getRequest()->getParam('data_sn');
        //$data_sn="CN610713-00003";
        $QEDT = new Application_Model_EtaxDocumentTran();
        $res = $QEDT->view_cn_pdf_file($data_sn);
        echo $res;
    }
    // Get Confirm Receive payment 
    public function confirmReceivePaymentAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'receiveconfirm.php';
    }

    public function reportStockProductBalanceAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'report-stock-product-balance.php';
    }

    public function reportStockProductBalancePrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'report-stock-product-balance-print.php';
    }

   // New Finance System

   public function financeDocAction()
   {
       require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'finance-doc.php';
   }

   public function accountingOrganizationAction()
   {
       require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'accounting-organization' . DIRECTORY_SEPARATOR . 'accounting-organization.php';
   }

   public function addAccountingOrganizationAction()
   {
       require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'accounting-organization' . DIRECTORY_SEPARATOR . 'add-accounting-organization.php';
   }

   public function saveAccountingOrganizationAction()
   {
       require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'accounting-organization' . DIRECTORY_SEPARATOR . 'save-accounting-organization.php';
   }

   public function deleteAccountingOrganizationAction()
   {
       require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'accounting-organization' . DIRECTORY_SEPARATOR . 'delete-accounting-organization.php';
   }

   public function financeWarehouseAction()
   {
       require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'finance-warehouse' . DIRECTORY_SEPARATOR . 'finance-warehouse.php';
   }

   public function addFinanceWarehouseAction()
   {
       require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'finance-warehouse' . DIRECTORY_SEPARATOR . 'add-finance-warehouse.php';
   }

   public function saveFinanceWarehouseAction()
   {
    require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'finance-warehouse' . DIRECTORY_SEPARATOR . 'save-finance-warehouse.php';
}

public function deleteFinanceWarehouseAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'finance-warehouse' .DIRECTORY_SEPARATOR . 'delete-finance-warehouse.php';
}

public function financeWarehouseGroupAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'finance-warehouse-group' . DIRECTORY_SEPARATOR . 'finance-warehouse-group.php';
}

public function addFinanceWarehouseGroupAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'finance-warehouse-group' . DIRECTORY_SEPARATOR . 'add-finance-warehouse-group.php';
}

public function saveFinanceWarehouseGroupAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'finance-warehouse-group' . DIRECTORY_SEPARATOR . 'save-finance-warehouse-group.php';
}

public function deleteFinanceWarehouseGroupAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'finance-warehouse-group' . DIRECTORY_SEPARATOR . 'delete-finance-warehouse-group.php';
}

public function financeClientAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'finance-client' . DIRECTORY_SEPARATOR . 'finance-client.php';
}

public function addFinanceClientAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'finance-client' . DIRECTORY_SEPARATOR . 'add-finance-client.php';
}

public function saveFinanceClientAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'finance-client' . DIRECTORY_SEPARATOR . 'save-finance-client.php';
}

public function deleteFinanceClientAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'finance-client' . DIRECTORY_SEPARATOR . 'delete-finance-client.php';
}

public function costItemAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'cost-item' . DIRECTORY_SEPARATOR . 'cost-item.php';
}

public function addCostItemAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'cost-item' . DIRECTORY_SEPARATOR . 'add-cost-item.php';
}

public function saveCostItemAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'cost-item' . DIRECTORY_SEPARATOR . 'save-cost-item.php';
}

public function deleteCostItemAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-doc' . DIRECTORY_SEPARATOR . 'cost-item' . DIRECTORY_SEPARATOR . 'delete-cost-item.php';
}

public function bankAccountManagementAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'bank-account-management' . DIRECTORY_SEPARATOR .'bank-account-management.php';
}

public function bankAccountMySideAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'bank-account-management' . DIRECTORY_SEPARATOR . 'bank-account-my-side' . DIRECTORY_SEPARATOR . 'bank-account-my-side.php';
}

public function addBankAccountMySideAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'bank-account-management' . DIRECTORY_SEPARATOR . 'bank-account-my-side' . DIRECTORY_SEPARATOR . 'add-bank-account-my-side.php';
}

public function saveBankAccountMySideAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'bank-account-management' . DIRECTORY_SEPARATOR . 'bank-account-my-side' . DIRECTORY_SEPARATOR . 'save-bank-account-my-side.php';
}

public function deleteBankAccountMySideAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'bank-account-management' . DIRECTORY_SEPARATOR . 'bank-account-my-side' . DIRECTORY_SEPARATOR . 'delete-bank-account-my-side.php';
}

public function bankAccountYourSideAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'bank-account-management' . DIRECTORY_SEPARATOR . 'bank-account-your-side' . DIRECTORY_SEPARATOR . 'bank-account-your-side.php';
}

public function addBankAccountYourSideAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'bank-account-management' . DIRECTORY_SEPARATOR . 'bank-account-your-side' . DIRECTORY_SEPARATOR . 'add-bank-account-your-side.php';
}

public function saveBankAccountYourSideAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'bank-account-management' . DIRECTORY_SEPARATOR . 'bank-account-your-side' . DIRECTORY_SEPARATOR . 'save-bank-account-your-side.php';
}

public function financeClientsContactManagementAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management.php';
}

public function saleReceiptAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'sale-receipt' . DIRECTORY_SEPARATOR . 'sale-receipt.php';
}

public function addSaleReceiptAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'sale-receipt' . DIRECTORY_SEPARATOR . 'add-sale-receipt.php';
}

public function saveSaleReceiptAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'sale-receipt' . DIRECTORY_SEPARATOR . 'save-sale-receipt.php';
}

public function deletePaySlipAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'sale-receipt' . DIRECTORY_SEPARATOR . 'delete-slip.php';
}

public function approvedSaleReceiptAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'sale-receipt' . DIRECTORY_SEPARATOR . 'approved-sale-receipt.php';
}

public function saleRefundAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'sale-refund' . DIRECTORY_SEPARATOR . 'sale-refund.php';
}

public function addSaleRefundAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'sale-refund' . DIRECTORY_SEPARATOR . 'add-sale-refund.php';
}

public function saveSaleRefundAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'sale-refund' . DIRECTORY_SEPARATOR . 'save-sale-refund.php';
}

public function approvedSaleRefundAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'sale-refund' . DIRECTORY_SEPARATOR . 'approved-sale-refund.php';
}

public function contactNoteAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'contact-note' . DIRECTORY_SEPARATOR . 'contact-note.php';
}

public function addContactNoteAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'contact-note' . DIRECTORY_SEPARATOR . 'add-contact-note.php';
}

public function saveContactNoteAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'contact-note' . DIRECTORY_SEPARATOR . 'save-contact-note.php';
}

public function approvedContactNoteAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'contact-note' . DIRECTORY_SEPARATOR . 'approved-contact-note.php';
}

public function supportFundManagementAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'support-fund' . DIRECTORY_SEPARATOR . 'support-fund.php';
}

public function addSupportFundAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'support-fund' . DIRECTORY_SEPARATOR . 'add-support-fund.php';
}

public function saveSupportFundAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'support-fund' . DIRECTORY_SEPARATOR . 'save-support-fund.php';
}

public function approvedSupportFundAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'support-fund' . DIRECTORY_SEPARATOR . 'approved-support-fund.php';
}

public function creditLimitAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'credit-limit' . DIRECTORY_SEPARATOR . 'credit-limit.php';
}

public function addCreditLimitAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'credit-limit' . DIRECTORY_SEPARATOR . 'add-credit-limit.php';
}

public function saveCreditLimitAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'credit-limit' . DIRECTORY_SEPARATOR . 'save-credit-limit.php';
}

public function approvedCreditLimitAction()
{
   require_once 'finance' . DIRECTORY_SEPARATOR . 'credit-limit' . DIRECTORY_SEPARATOR . 'approve-credit-limit.php';
}

public function clientContactNoteAction()
{
    require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'client-contact-note' . DIRECTORY_SEPARATOR . 'client-contact-note.php';
}

public function clientContactNoteDetailAction()
{
    require_once 'finance' . DIRECTORY_SEPARATOR . 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'client-contact-note' . DIRECTORY_SEPARATOR . 'client-contact-note-detail.php';
}

public function distributorAccountReconciliationAction()
{
    require_once 'finance' . DIRECTORY_SEPARATOR. 'finance-clients-contact-management' . DIRECTORY_SEPARATOR . 'distributor-account' . DIRECTORY_SEPARATOR .'distributor-account-reconciliation.php';
}


//End New Finance System
    
    public function reportStockDailyAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'report-stock-daily.php';
    }

    public function reportStockProductInoutAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'report-stock-product-inout.php';
    }

    public function cpAutoCheckImeiAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cp-auto-check-imei.php';
    }

    public function saveCpAutoCheckImeiAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'save-cp-auto-check-imei.php';
    }

    public function returnBoxNumberImeiCheckConfirmAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'return-box-number-imei-check-confirm.php';
    }

    public function saveReturnBoxNumberImeiCheckConfirmAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'save-return-box-number-imei-check-confirm.php';
    }

    public function cnRewardCnServiceViewPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cn-reward-cn-service-view-print.php';
    }
    
    public function cnRewardViewPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cn-reward-view-print.php';
    }
    public function cnRewardServiceViewPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cn-reward-service-view-print.php';
    }
    public function cnRewardAllGreenPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cn-reward-all-green-print.php';
    }
    public function cnRewardTopGreenPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cn-reward-top-green-print.php';
    }
    public function creditnoteManualPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'creditnote-manual-print.php';
    }
    public function invoiceAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'invoice.php';
    }

    public function viewPaySlipAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'view-pay-slip.php';
    }

    public function viewImeiListAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'view-imei-list.php';
    }

    public function invoiceDestroyAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'invoice-destroy.php';
    }

    //Tanong List return order for print  2016-03-13 1452
    //return-list-credit-note
    public function returnListCreditNoteAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'return-list-cn.php';
    }

    //Tanong
    public function oppoclupRewardListCnAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'oppoclup-reward-list-cn.php';
    }

    //Tanong oppoclup-reward-upload-cn
    public function oppoclupRewardImportCnAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'oppoclup-reward-upload-cn.php';
    }

    //Tanong
    public function returnListCnAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'return-list-cn.php';
    }
    //Tanong
    public function cnViewPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cn-view-print.php';
    }

    public function cpViewImportPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cp-view-import-print.php';
    }

    //Tanong
    public function cpViewPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cp-view-print.php';
    }

    public function cpViewImeiPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cp-view-imei-print.php';
    }
    public function cpViewImeiImportPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cp-view-imei-import-print.php';
    }

    public function cnViewImeiRewardPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cn-view-imei-reward-print.php';
    }

    public function cnViewImeiReturnPrintAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'cn-view-imei-return-print.php';
    }

    public function oppoAllGreenAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'oppo-all-green.php';
    }

    public function oppoAllGreenUploadAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'oppo-all-green-upload.php';
    }

    public function oppoAllGreenSaveAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'oppo-all-green-save.php';
    }

    public function editReturnCnAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'edit-return-cn.php';
    }

    public function rollbackPaymentgroupAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'rollback-paymentgroup.php';
    }

    public function exportPdfEtaxAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'export-pdf-etax.php';
    }

    public function printInvoiceEtaxAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'print-invoice-etax.php';
    }

    public function printCreditnoteEtaxAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'print-creditnote-etax.php';
    }

    public function importEtaxAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'import-etax.php';
    }

    public function reportEtaxAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'report-etax.php';
    }

    public function printMultiInvoiceAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'print-multi-invoice.php';
    }

    public function reportInOutStockAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'report-in-out-stock.php';
    }

    public function reportServiceStockAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'report-service-stock.php';
    }

    public function reportDetailStockAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'report-detail-stock.php';
    }

    public function importRevenueAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'import-revenue.php';
    }

    public function poAction()
    {
        //auto refresh
        $this->view->meta_refresh = 30;

        $sort = $this->getRequest()->getParam('sort', 'created_at');
        $desc = $this->getRequest()->getParam('desc', 1);

        $page = $this->getRequest()->getParam('page', 1);

        $sn = $this->getRequest()->getParam('sn');
        $created_by = $this->getRequest()->getParam('created_by');
        $cat_id = $this->getRequest()->getParam('cat_id');
        $good_id = $this->getRequest()->getParam('good_id');
        $good_color = $this->getRequest()->getParam('good_color');
        $warehouse_id = $this->getRequest()->getParam('warehouse_id');
        $created_at_to = $this->getRequest()->getParam('created_at_to');
        $created_at_from = $this->getRequest()->getParam('created_at_from');

        $limit = LIMITATION;
        $total = 0;

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        // 84 = PO Acc Only
        if (My_Staff_Group::inGroup($userStorage->group_id, array(84))){
            //12 = Accessories
            $cat_id = 12;
        }

        $params = array_filter(array(
            'sn' => $sn,
            'created_by' => $created_by,
            'cat_id' => $cat_id,
            'good_id' => $good_id,
            'good_color' => $good_color,
            'warehouse_id' => $warehouse_id,
            'created_at_to' => $created_at_to,
            'created_at_from' => $created_at_from,
        ));

        $params['isbacks'] = 0;
        // $params['group_sn'] = 1;

        if ($cat_id) {
            $QGood = new Application_Model_Good();
            // $where = $QGood->getAdapter()->quoteInto('cat_id = ?', $cat_id);
            $goods = $QGood->getGoodRecordByCategory($cat_id);

            $this->view->goods = $goods;

            if ($good_id) {
                //get goods color
                $where = $QGood->getAdapter()->quoteInto('id = ?', $good_id);
                $good = $QGood->fetchRow($where);

                $aColor = array_filter(explode(',', $good->color));
                if ($aColor) {
                    $QGoodColor = new Application_Model_GoodColor();
                    $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

                    $colors = $QGoodColor->fetchAll($where);
                    $this->view->colors = $colors;
                }
            }
        }

        $params['sort'] = $sort;
        $params['desc'] = $desc;

        $params['no_payment'] = 1;

        $QPO = new Application_Model_Po();

        $POs = $QPO->fetchPagination($page, $limit, $total, $params);

        unset($params['no_payment']);


        $this->view->desc = $desc;
        $this->view->sort = $sort;
        $this->view->POs = $POs;
        $this->view->params = $params;
        $this->view->limit = $limit;
        $this->view->total = $total;
        $this->view->url = HOST . 'finance/po/' . ($params ? '?' . http_build_query($params) .
            '&' : '?');

        $this->view->offset = $limit * ($page - 1);

        $QStaff = new Application_Model_Staff();
        $this->view->staffs = $QStaff->get_cache();

        $QWarehouse = new Application_Model_Warehouse();
        $this->view->warehouses = $QWarehouse->get_cache();

        $QGoodCategory = new Application_Model_GoodCategory();
        $this->view->good_categories = $QGoodCategory->get_cache();

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $this->_helper->viewRenderer->setRender('partials/list');
        }
    }

    public function poConfirmAction()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            $QPo = new Application_Model_Po();

            if ($this->getRequest()->getMethod() == 'POST') {

                $pay_user = $this->getRequest()->getParam('pay_user');

                $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                $where = $QPo->getAdapter()->quoteInto('id = ?', $id);
                $data = array(
                    'pay_user' => $pay_user,
                    'flow' => $userStorage->id,
                    'flow_time' => date('Y-m-d H:i:s'),
                );

                $flashMessenger = $this->_helper->flashMessenger;
                try {
                    $PO = $QPo->fetchRow($where);

                    $QPo->update($data, $where);

                    //todo log
                    $ip = $this->getRequest()->getServer('REMOTE_ADDR');

                    $info = 'Verify: Purchase order number: ' . $PO->sn;

                    $QLog = new Application_Model_Log();

                    $QLog->insert(array(
                        'info' => $info,
                        'user_id' => $userStorage->id,
                        'ip_address' => $ip,
                        'time' => date('Y-m-d H:i:s'),
                    ));

                    $flashMessenger->setNamespace('success')->addMessage('Done!');
                }
                catch (exception $e) {
                    $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
                }

                $this->_redirect('/finance/po');
            }

            $rowset = $QPo->find($id);
            $PO = $rowset->current();

            $this->view->PO = $PO;

            $QGoodCategory = new Application_Model_GoodCategory();
            $categories = $QGoodCategory->get_cache();

            $this->view->category = isset($categories[$PO->cat_id]) ? $categories[$PO->
                cat_id] : '';

            //get goods
                $QGood = new Application_Model_Good();
                $goods = $QGood->get_cache();

                $this->view->good = isset($goods[$PO->good_id]) ? $goods[$PO->good_id] : '';

            //get goods color
                $QGoodColor = new Application_Model_GoodColor();
                $goodColors = $QGoodColor->get_cache();

                $this->view->good_color = isset($goodColors[$PO->good_color]) ? $goodColors[$PO->
                    good_color] : '';

            //get goods color
                    $QWarehouse = new Application_Model_Warehouse();
                    $warehouse = $QWarehouse->get_cache();

                    $this->view->warehouse = isset($warehouse[$PO->warehouse_id]) ? $warehouse[$PO->
                        warehouse_id] : '';

            //get username
                        $QStaff = new Application_Model_Staff();

                        $staffs = $QStaff->get_cache();

                        $this->view->created_by_name = isset($staffs[$PO->created_by]) ? $staffs[$PO->
                            created_by] : '';

                            $this->view->payer_name = isset($staffs[$PO->flow]) ? $staffs[$PO->flow] : '';

                            $this->view->warehousing_name = isset($staffs[$PO->mysql_user]) ? $staffs[$PO->
                                mysql_user] : '';


                            }
                        }

                        public function poRemoveAction()
                        {
                            $id = $this->getRequest()->getParam('id');
                            $flashMessenger = $this->_helper->flashMessenger;


                            if ($id) {
                                $QPo = new Application_Model_Po();

                                $rowset = $QPo->find($id);
                                $PO = $rowset->current();

                                if ($PO) {
                                    $where = $QPo->getAdapter()->quoteInto('id = ?', $id);

                                    try {
                                        $QPo->delete($where);

                                        $flashMessenger->setNamespace('success')->addMessage('Done!');
                                    }
                                    catch (exception $e) {
                                        $flashMessenger->setNamespace('error')->addMessage('Cannot delete, please try again!');
                                    }
                                    $this->_redirect('/finance/po');
                                }
                            }

                            $flashMessenger->setNamespace('error')->addMessage('Wrong ID!');
                            $this->_redirect('/finance/po');
                        }

                        public function salesConfirmAction()
                        {
                            $this->view->back_url = HOST . 'finance/sales-payment';


                            $no_show_brandshop = $this->getRequest()->getParam('no_show_brandshop');
        //$no_show_brandshop =1;
                            $sn = $this->getRequest()->getParam('sn');
                            $sn_re = $this->getRequest()->getParam('sn_re');
                            $act = $this->getRequest()->getParam('act');
                            $flashMessenger = $this->_helper->flashMessenger;

                            $messages = $flashMessenger->setNamespace('error')->getMessages();
                            $this->view->messages = $messages;
                            $this->view->no_show_brandshop = $no_show_brandshop;

                            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        //---------------Reject pond -----------------//
                            if ($act=='reject') {
                                $QMarket = new Application_Model_Market();
                                $QCheckmoney = new Application_Model_Checkmoney();

                                $where_re = $QMarket->getAdapter()->quoteInto('sn = ?', $sn_re);
                                $data_re = array(
                                    'sales_confirm_date'    => NULL,
                                    'sales_confirm_id'      => NULL,
                                    'confirm_so'            => 0
                                );

                                $QMarket->update($data_re,$where_re);
                                $where_del = array();
                                $where_del = $QCheckmoney->getAdapter()->quoteInto('sn = ?', $sn_re);
                                $where_del = $QCheckmoney->getAdapter()->quoteInto('type = 1');
                                $QCheckmoney->delete($where_del);
                                if(isset($_SESSION["save_search"]) && $_SESSION["save_search"]){
                                    $this->_redirect($_SESSION["save_search"]);
                                }else{
                                    $this->_redirect('/finance/sales?no_show_brandshop='.$no_show_brandshop);
                                }
                            }
            //--------------------------------------------//
                            if ($sn) {

                                $db = Zend_Registry::get('db');
                                $QMarket = new Application_Model_Market();
                                $where = array();
                                $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                                $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

                                $sales = $QMarket->fetchAll($where);

            //check
                                if (!$sales) {
                                    $flashMessenger->setNamespace('error')->addMessage('Invalid SN!');
                                    if(isset($_SESSION["save_search"]) && $_SESSION["save_search"]){
                                        $this->_redirect($_SESSION["save_search"]);
                                    }else{
                                        $this->_redirect('/finance/sales?no_show_brandshop='.$no_show_brandshop);
                                    }
                                }

                                if (!isset($sales[0]) || ($sales[0]['shipping_yes_time'] and $sales[0]['pay_time'])) {

                                    $flashMessenger->setNamespace('error')->addMessage('This order cannot be changed!');

                                    if(isset($_SESSION["save_search"]) && $_SESSION["save_search"]){
                                        $this->_redirect($_SESSION["save_search"]);
                                    }else{
                                        $this->_redirect('/finance/sales?no_show_brandshop='.$no_show_brandshop);
                                    }

                                }

                                $pay_group_data = [];
                                $pay_group_bank_data = [];
                                $pay_group_cause_data = [];

                                if(isset($sales[0]['pay_group']) and $sales[0]['pay_group'] == 1){

                                    $payment_no = $sales[0]['payment_no'];

                                    $QPG = new Application_Model_PayGroup();
                                    $QPGB = new Application_Model_PayGroupBank();
                                    $QPGC = new Application_Model_PayGroupCause();

                // $where = $QPG->getAdapter()->quoteInto('payment_no = ?',$payment_no);
                // $pay_group_data = $QPG->fetchAll($where);

                                    $pay_group_data = $QPG->getPaymentGroup($payment_no);

                                    if(isset($pay_group_data[0]['payment_id'])){

                                        $payment_id = $pay_group_data[0]['payment_id'];

                    // $where = $QPGB->getAdapter()->quoteInto('payment_id = ?',$payment_id);
                    // $pay_group_bank_data = $QPGB->fetchAll($where);

                                        $pay_group_bank_data = $QPGB->getPaymentGroupBank($payment_id);

                                        $where = $QPGC->getAdapter()->quoteInto('payment_id = ?',$payment_id);
                                        $pay_group_cause_data = $QPGC->fetchAll($where);
                                    }

                                }

                                $this->view->pay_group_status = $sales[0]['pay_group'];
                                $this->view->pay_group_data = $pay_group_data;
                                $this->view->pay_group_bank_data = $pay_group_bank_data;
                                $this->view->pay_group_cause_data = $pay_group_cause_data;

                                $getUsetPaygroup = [];

                                if($sales[0]['payment_no']){
                                    $getArrUsetPaygroup = $QMarket->getUsePaymentGroup($sales[0]['payment_no']);

                                    $QPG = new Application_Model_PayGroup();

                                    foreach ($getArrUsetPaygroup as $key) {

                                        if($key['usePaygroup_paymentID']){
                                            $getUsePayment_no = $QPG->getPaymentNoByPaymentID($key['usePaygroup_paymentID']);

                                            $arrData = array(
                                                'payment_id' => $key['usePaygroup_paymentID'],
                                                'payment_no' => $getUsePayment_no['payment_no'],
                                                'total' => $key['use_total']
                                            );

                                            array_push($getUsetPaygroup, $arrData);
                                        }
                                    }

                                }

                                $this->view->usePaygroup = $getUsetPaygroup;

                                /*---------Get Data From Sales Confirm-------------*/
                                $select = $db->select()
                                ->from(array('ch'=>'checkmoney'),   array('ch.*','ch_id'=>'ch.id'))
            ->joinleft(array('b'=>'bank'),'ch.bank=b.id',array("bank_name"=>'b.name','b.id'))// trường hợp transaction trừ tiền
            ->joinleft(array('ep'=>'ep_privileges_tran_order'),'ep.sales_order_sn=ch.sn',array('ep_id'=>'ep.ids','ep_payslip'=>'ep.payment_slip_image','ep_staff_id' => 'ep.staff_code'))
            ->where($db->quoteInto('ch.sn = ?',$sn))
            ->where($db->quoteInto('ch.type=?',1));
            //echo $select;

            $currentTransaction = $db->fetchAll($select);
            $this->view->transaction = $currentTransaction; //checkmoney

            /*---------End Get Data From Sales Confirm-------------*/

            $QMarketProduct = new Application_Model_MarketProduct();
            $QMarket = new Application_Model_Market();
            //Tiền đi đơn nếu có bảo vệ giá thì đã trừ tiền
            $sn_total = 0;
            $intRebate = intval($QMarketProduct->getPrice($sn)); // số tiền được giảm
            $sn_total = $QMarket->getPrice($sn) - $intRebate; // số tiền còn lại

            $strNoteRebate = '';
            if ($intRebate > 0) {
                $strNoteRebate = ', rebate: ' . $intRebate;
            }

            if($sales[0]['office']){
                $selectArea = $db->select()
                ->from(array('a'=>'office'),array('a.*'))
                ->where('a.id = ?',$sales[0]['office']);
                ;
                $office_area = $db->fetchRow($selectArea);
                $this->view->office_area = $office_area;
            }

            //Store account
            $QStoreaccount = new Application_Model_Storeaccount();
            $QDistributor  = new Application_Model_Distributor();
            $QCampaign     = new Application_Model_Campaign();

            //lấy dealer mẹ
            $main_retailer                   = $QDistributor->getRootDistributor($sales[0]['d_id']);
            $this->view->main_retailer       = $main_retailer;
            $total_balance_row               = $QStoreaccount->getBalanceByGroup($sales[0]['d_id']);
            $distributor_balance_row         = $QStoreaccount->getBalance($sales[0]['d_id']);

            $selectCompany = $db->select()
            ->from(array('p'=>'warehouse'),array('p.company_id'))
            ->where('id = ?',$sales[0]['warehouse_id']);
            $company_id = $db->fetchOne($selectCompany);

            $remain_balance                  = ($company_id == 1) ? $total_balance_row['balance'] : $total_balance_row['balance_smartmobile'];
            $distributor_balance             = ($company_id == 1) ? $distributor_balance_row['balance'] : $distributor_balance_row['balance_smartmobile'];
            $this->view->distributor_balance = $distributor_balance;

            $checkBalance = 0;
            $checkPaymentStatus = 0; //kiểm tra có thể cho phép payment không?
            if ($remain_balance) {
                if ($sn_total <= $remain_balance) {
                    $checkPaymentStatus = 1;
                } else{
                    $checkPaymentStatus = 0;
                }

                $checkBalance = $remain_balance - $sn_total;
            } else{
                $checkBalance = -$sn_total;
            }

            //xử lý check payment
            $this->view->need               = abs($checkBalance);
            $this->view->checkPaymentStatus = $checkPaymentStatus;
            $this->view->checkBalance       = $checkBalance;
            $this->view->remain_balance     = $remain_balance;
            $this->view->campaign           = $QCampaign->get_cache();



            // get tags
            $QTag = new Application_Model_Tag();
            $QTagObject = new Application_Model_TagObject();

            $where = array();
            $where[] = $QTagObject->getAdapter()->quoteInto('object_id = ?', $sn);
            $where[] = $QTagObject->getAdapter()->quoteInto('type = ?', TAG_ORDER);

            $a_tags = array();

            $tags_object = $QTagObject->fetchAll($where);
            if ($tags_object)
                foreach ($tags_object as $to) {
                    $where = $QTag->getAdapter()->quoteInto('id = ?', $to['tag_id']);
                    $tag = $QTag->fetchRow($where);
                    if ($tag)
                        $a_tags[] = $tag['name'];
                }

                $this->view->a_tags = $a_tags;
            //print_r($a_tags);
                if ($this->getRequest()->getMethod() == 'POST') {
                // print_r($_POST);die;
                // echo "<pre>";
                // print_r($_FILES);
                // $file_name_show = $_FILES['file_name']['name'];
                // print_r($file_name_show);

                // die;

                    $db->beginTransaction();
                    try {

                        $sn       = $this->getRequest()->getParam('sn');
                        $ch_id       = $this->getRequest()->getParam('ch_id');
                        $d_id       = $this->getRequest()->getParam('d_id');
                        $payment       = $this->getRequest()->getParam('payment');
                        $shipping      = $this->getRequest()->getParam('shipping');
                        $pay_text      = $this->getRequest()->getParam('pay_text');
                        $shipping_text = $this->getRequest()->getParam('shipping_text');
                        $payment_type = $this->getRequest()->getParam('payment_type',NULL);
                        $payment_order = $this->getRequest()->getParam('payment_order', 0);
                        $payment_bank_transfer = $this->getRequest()->getParam('payment_bank_transfer', 0);
                        $payment_service = $this->getRequest()->getParam('payment_service', 0);
                        $payment_servicecharge = $this->getRequest()->getParam('payment_servicecharge', 0);
                        $pay_banktransfer = $this->getRequest()->getParam('pay_banktransfer', 0);
                        $pay_servicecharge = $this->getRequest()->getParam('pay_servicecharge', 0);
                        $pay_service = $this->getRequest()->getParam('pay_service', 0);
                        $file_ = $this->getRequest()->getParam('file_');

                        $o_dis = $this->getRequest()->getParam('o_dis',NULL);
                        $dis_type_01 = $this->getRequest()->getParam('dis_type_01');
                        $dis_type_02 = $this->getRequest()->getParam('dis_type_02');
                        $discount_note = $this->getRequest()->getParam('discount_note');

                        $pay_time      = $this->getRequest()->getParam('pay_time');
                        $bank          = $this->getRequest()->getParam('select_bank_id', NULL);
                        $type          = 1;
                        $company_id    = $this->getRequest()->getParam('company_id');
                        $retailer_rank = $this->getRequest()->getParam('retailer_rank', NULL);

                        $total_amount  = $this->getRequest()->getParam('total_amount', NULL);

                        $lacksurplus  = $this->getRequest()->getParam('lacksurplus', 0);

                        $payment_no  = $this->getRequest()->getParam('payment_no', 0);

                        $use_credit_card_input   = $this->getRequest()->getParam('use_credit_card_input');

                        if($total_amount==0)
                        {
                            $payment_order=0;
                            $payment_bank_transfer=0;
                            $payment_servicecharge=0;
                            $payment_service=0;
                        }

                        $allow_surplus = false;

                    // brand shop & service
                        if (My_Staff_Group::inGroup($userStorage->group_id, array(25,28))){
                            $allow_surplus = true;
                        }

                        if($lacksurplus < -10 && !$allow_surplus){
                            $db->rollback();
                            $flashMessenger->setNamespace('error')->addMessage('ไม่สามารถทำรายการได้ เนื่องจากเงินขาดมากเกินไป');
                            $this->_redirect('/finance/sales-confirm?sn=' . $sn.'&no_show_brandshop='.$no_show_brandshop);
                        }

                        $where = array();
                        $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                        $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

                        $date = date('Y-m-d H:i:s');

                        $checkUpdateCheckMoney = 0;
                        $QStoreaccount  = new Application_Model_Storeaccount();
                        $QCheckmoney = new Application_Model_Checkmoney();
                        $QCheckmoneyPaymentorder = new Application_Model_CheckmoneyPaymentorder();

                        /*--------------------------------------------*/
                   // $data = array();
                   // $where = array();
                        $where = $QCheckmoney->getAdapter()->quoteInto('sn = ?', $sn);
                        $data['finance_confirm_id'] = $userStorage->id;
                        $data['finance_confirm_date'] = $date;
                        $QCheckmoney->update($data,$where);

                        if($payment_type=="CA")
                        {
                            $data['finance_confirm_id'] = $userStorage->id;
                            $data['finance_confirm_date'] = $date;
                            $where = $db->quoteInto('sn = ?',$sn);
                            $QCheckmoneyPaymentorder->update($data,$where);

                            $QPG = new Application_Model_PayGroup();
                            $QPGB = new Application_Model_PayGroupBalance();

                            if(isset($payment_no) and $payment_no){

                                $data_update_paygroup = array(
                                    'lacksurplus' => $lacksurplus,
                                    'modified_at' => $userStorage->id,
                                    'modified_date' => date('Y-m-d H:i:s')
                                );

                                $where_update_paygroup = [];
                                $where_update_paygroup[] = $QPG->getAdapter()->quoteInto('payment_no = ?', $payment_no);
                                $where_update_paygroup[] = $QPG->getAdapter()->quoteInto('status = ?', 1);
                                $QPG->update($data_update_paygroup, $where_update_paygroup);

                            }

                            $getBalancePayGroup = $QPG->getPaymentBalance($payment_no);

                            if($getBalancePayGroup['pgb_id']){

                                $data_update_balancepaygroup = array(
                                    'total_amount' => $lacksurplus,
                                    'balance_total' => $lacksurplus,
                                    'update_date' => date('Y-m-d H:i:s'),
                                    'update_by' => $userStorage->id
                                );

                                $where_update_balancepaygroup = $QPGB->getAdapter()->quoteInto('id = ?', $getBalancePayGroup['pgb_id']);
                                $QPGB->update($data_update_balancepaygroup, $where_update_balancepaygroup);

                            }else{

                                $data_balancepaygroup = array(
                                    'payment_id' => $getBalancePayGroup['payment_id'],
                                    'distributor_id' => $getBalancePayGroup['d_id'],
                                    'total_amount' => $lacksurplus,
                                    'use_total' => 0,
                                    'balance_total' => $lacksurplus,
                                    'status' => 1,
                                    'create_date' => date('Y-m-d H:i:s'),
                                    'create_by' => $userStorage->id,
                                    'update_date' => date('Y-m-d H:i:s'),
                                    'update_by' => $userStorage->id
                                );

                                $QPGB->insert($data_balancepaygroup);

                            }

                        }

                    // Edie Finance confirm Order By Pungpond
                        $where_edit = array();
                        $edit = array();

                       // echo "<pre>";

                       // print_r($_FILES['file']['name']);

                        if($lacksurplus > 0){
                        // $edit['payment_surplus'] = $lacksurplus;
                            $edit['payment_surplus'] = 0;
                        }

                        if($lacksurplus < 0){
                        // $edit['lack_of_money'] = $lacksurplus*-1;
                            $edit['lack_of_money'] = 0;
                        }


                        for ($i=0; $i < count($bank); $i++) {

                            if (isset($_FILES['file_name']['name'][$i]) and $_FILES['file_name']['name'][$i]) {
                                $file_name_upload = '/pay_slips/'.$sn.'/'.$_FILES['file_name']['name'][$i];
                                $edit['file_pay_slip'] = $file_name_upload;
                            }


                            if (isset($ch_id[$i]) and $ch_id[$i]) {

                                $edit['bank'] = $bank[$i];
                                $edit['pay_money'] = $payment_order[$i];
                                $edit['pay_banktransfer'] = $pay_banktransfer[$i];
                                $edit['pay_servicecharge'] = $pay_servicecharge[$i];
                                $edit['pay_service'] = $pay_service[$i];
                                $edit['pay_time'] = $pay_time[$i];

                                $credit_card = 0;
                                if($use_credit_card_input[$i]){
                                    $credit_card = $use_credit_card_input[$i];
                                }

                                $edit['credit_card'] = $credit_card;

                                $where_edit = $QCheckmoney->getAdapter()->quoteInto('id = ?', $ch_id[$i]);
                                $QCheckmoney->update($edit, $where_edit);
                            }else{
                                $note_new='PayMoney='.number_format($payment_order[$i],2) .' Fee transfer='.number_format($pay_banktransfer[$i],2).' Service Charge='.number_format($pay_servicecharge[$i],2).' ค่าอะไหล่และค่าบริการ='.number_format($pay_service[$i],2);

                                $credit_card = 0;
                                if($use_credit_card_input[$i]){
                                    $credit_card = $use_credit_card_input[$i];
                                }

                                $data_f = array(
                                    'd_id'                  => $d_id,
                                    'bank'                  => $bank[$i],
                                    'pay_money'             => $payment_order[$i],
                                    'pay_servicecharge'     => $pay_servicecharge[$i],
                                    'pay_banktransfer'      => $pay_banktransfer[$i],
                                    'pay_service'           => $pay_service[$i],
                                    'type'                  => 1,
                                    'pay_time'              => $pay_time[$i],
                                    'bank_serial'           => null,
                                    'bank_transaction_code' => null,
                                    'note'                  => $note_new,
                                    'content'               => null,
                                    'company_id'            => $company_id,
                                    'sn'                    => $sn,
                                    'file_pay_slip'         => $file_name_upload,
                                    'user_id'               => $userStorage->id,
                                    'create_by'             => $userStorage->id,
                                    'create_at'             => date('Y-m-d H:i:s'),
                                    'sales_confirm_id'      => $userStorage->id,
                                    'sales_confirm_date'    => date('Y-m-d H:i:s'),
                                    'addition'              => 1,
                                    'credit_card'           => $credit_card
                                );

                                if($lacksurplus > 0){
                                // $data_f['payment_surplus'] = $lacksurplus;
                                    $data_f['payment_surplus'] = 0;
                                }

                                if($lacksurplus < 0){
                                // $data_f['lack_of_money'] = $lacksurplus*-1;
                                    $data_f['lack_of_money'] = 0;
                                }

                                $QCheckmoney->insert($data_f);

                            }
                        }

                  // die;
                        /*-------------------File Pay Slip Upload--------------------------*/
                        $upload    = new Zend_File_Transfer_Adapter_Http();
                        $files  = $upload->getFileInfo();

                        $count_file_upload=0;$r=0;
                        foreach($files as $file => $fileInfo)
                        {
                            if($upload->isUploaded($file))
                            {

                                $uniqid = uniqid('', true);

                                $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
                                . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files'
                                . DIRECTORY_SEPARATOR . 'finance'. DIRECTORY_SEPARATOR . 'pay_slips'
                                . DIRECTORY_SEPARATOR . $sn;

                                $file_pay_slip = DIRECTORY_SEPARATOR . 'pay_slips'
                                . DIRECTORY_SEPARATOR . $sn . DIRECTORY_SEPARATOR;

                                if (!is_dir($uploaded_dir))
                                    @mkdir($uploaded_dir, 0777, true);


                                $upload->setDestination($uploaded_dir);

                                    // Upload Max 5 MB
                                $upload->setValidators(array(
                                    'Size' => array('min' => 50, 'max' => 2000000),
                                    'Count' => array('min' => 1, 'max' => 3),
                                    'Extension' => array('jpg','jpeg', 'PNG','GIF'),
                                ));

                                    if (!$upload->isValid($file)) { // validate IF
                                        $errors = $upload->getErrors();
                                        $sError = null;
                                        if ($errors and isset($errors[0]))
                                            switch ($errors[0]) {
                                                case 'fileUploadErrorIniSize':
                                                $sError = 'File size is too large';
                                                break;
                                                case 'fileMimeTypeFalse':
                                                $sError = 'The file you selected weren\'t the type we were expecting';
                                                break;
                                                case 'fileExtensionFalse':
                                                $sError = 'Please choose a file in JPG or PNG format.';
                                                break;
                                                case 'fileCountTooFew':
                                                $sError = 'Please choose a PO file (in JPG or PNG format)';
                                                break;
                                                case 'fileUploadErrorNoFile':
                                                $sError = 'Please choose a PO file (in JPG or PNG format)';
                                                break;
                                                case 'fileSizeTooBig':
                                                $sError = 'File size is too big';
                                                break;
                                            }

                                            if($sError!=''){
                                                $db->rollback();
                                                $flashMessenger->setNamespace('error')->addMessage($sError);
                                                $this->_redirect('/sales/sales-confirm-order?sn=' . $sn.'&no_show_brandshop='.$no_show_brandshop);
                                            }
                                        }else{
                                           $upload->receive($file);
                                       }
                                   }
                                   $r+=1;
                               }

                               /*-------------------End File Pay Slip Upload--------------------------*/


                               /* ------------------------ */
                  // update balance
                               $QStoreaccount->updateBalance($sales[0]['d_id']);
                               $data['pay_time'] = $date;
                               $data['pay_user'] = $userStorage->id;
                               $data['shipping_yes_time'] = $date;
                               $data['shipping_yes_id'] = $userStorage->id;

                               $data['other_discounts'] = $o_dis;
                               $data['dis_type_pp'] = $dis_type_01;
                               $data['dis_type_policy'] = $dis_type_02;
                               $data['discount_note'] = $discount_note;

                               $data['pay_text'] = $pay_text;
                               $data['shipping_text'] = $shipping_text;
                               $data['finance_confirm_id'] = $userStorage->id;
                               $data['finance_confirm_date'] = $date;
                               $QMarket->update($data, $where);

                               /*--------------------------------------------*/

                    //todo log
                               $ip = $this->getRequest()->getServer('REMOTE_ADDR');

                               $info = 'Verify: Sale order number: ' . $sn;

                               $QLog = new Application_Model_Log();

                               $QLog->insert(array(
                                'info' => $info,
                                'user_id' => $userStorage->id,
                                'ip_address' => $ip,
                                'time' => $date,
                            ));

                    //check before commit
                               if ($payment) {
                                $whereCheckMoney       = array();
                                $whereCheckMoney[]     = $QCheckmoney->getAdapter()->quoteInto('sn = ?',$sales[0]['sn']);
                                $checkUpdateCheckMoney = $QCheckmoney->fetchRow($whereCheckMoney);
                                if (!$checkUpdateCheckMoney) {
                                    $db->rollback();
                                    $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
                                    $this->_redirect('/finance/sales-confirm?sn=' . $sn.'&no_show_brandshop='.$no_show_brandshop);
                                }
                            }

                            $db->commit();
                            $flashMessenger->setNamespace('success')->addMessage('Done!');
                            if(isset($_SESSION["save_search"]) && $_SESSION["save_search"]){
                                $this->_redirect($_SESSION["save_search"]);
                            }else{
                                $this->_redirect('/finance/sales?no_show_brandshop='.$no_show_brandshop);
                            }
                        }
                        catch (exception $e) {
                            $db->rollback();
                            $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!' .
                                $e->getMessage());
                            $this->_redirect('/finance/sales-confirm?sn=' . $sn.'?no_show_brandshop='.$no_show_brandshop);
                        }
                        $flashMessenger->setNamespace('success')->addMessage('Done!');
                        if(isset($_SESSION["save_search"]) && $_SESSION["save_search"]){
                            $this->_redirect($_SESSION["save_search"]);
                        }else{
                            $this->_redirect('/finance/sales?no_show_brandshop='.$no_show_brandshop);
                        }

            } //End if check post

            $where = array();
            $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

            $sales = $QMarket->fetchAll($where);

            // print_r($sales);

            $data = array();

            $QGoodCategory = new Application_Model_GoodCategory();
            $categories    = $QGoodCategory->get_cache();

            $QGood         = new Application_Model_Good();
            $goods         = $QGood->get_cache();

            $QGoodColor    = new Application_Model_GoodColor();
            $goodColors    = $QGoodColor->get_cache();

            $QStaff        = new Application_Model_Staff();
            $staffs        = $QStaff->get_cache();

            $QDistributor  = new Application_Model_Distributor();
            $distributors  = $QDistributor->get_cache();

            // get another info of distributor
            $where = $QDistributor->getAdapter()->quoteInto('id = ?', $sales[0]->d_id);
            $distributors_info = $QDistributor->fetchRow($where);

            // get credit from distributor's credit type
            $QCredit  = new Application_Model_Credit();
            $where = $QCredit->getAdapter()->quoteInto('id = ?', $distributors_info->credit_type);
            $credit = $QCredit->fetchRow($where);

            $QWarehouse    = new Application_Model_Warehouse();
            //$warehouses    = $QWarehouse->get_cache();
            $warehouse_type = $userStorage->warehouse_type;
            $where_wh = $QWarehouse->getAdapter()->quoteInto('warehouse_type IN ('.$warehouse_type.')', null);
            $warehouses_cached = $QWarehouse->fetchAll($where_wh, 'name');
            $warehouse_arr = array();
            foreach ($warehouses_cached as $k => $warehouse_data){
                $warehouse_arr[$warehouse_data['id']] = $warehouse_data['name']; 
            }
            $warehouses = $warehouse_arr;
            $this->view->warehouses = $warehouses;

            $Credit_Note = $QMarket->fetchCredit_Note($sn);

            $deposit = $QMarket->fetchDeposit($sn);

            $show_cash_menu=false;
            if (My_Staff_Group::inGroup($userStorage->group_id, OPPO_BRAND_SHOP_SERVICE) || $userStorage->group_id == ADMINISTRATOR_ID )
            {
                $show_cash_menu  = true;
            }

            foreach ($sales as $k => $sale) {
                //get warehouse
                $data[$k]['warehouse_name'] = isset($warehouses[$sale->warehouse_id]) ? $warehouses[$sale->
                    warehouse_id] : '';

                //get retailer
                //print_r($distributors);
                    $data[$k]['retailer_name'] = isset($distributors[$sale->d_id]) ? $distributors[$sale->
                        d_id] : '';

                //get d_id
                        $data[$k]['d_id'] = $sale->d_id;

                //get retailer : rank
                        $data[$k]['retailer_rank'] = isset($distributors_info->rank) ? $distributors_info->rank : '';

                //get retailer : rank
                        $data[$k]['show_cash_menu'] = $show_cash_menu;

                //get retailer : credit amount
                        $data[$k]['credit_amount'] = isset($distributors_info->credit_amount) ? $distributors_info->credit_amount : '';

                //get retailer : credit type
                        $data[$k]['credit_type'] = isset($credit->name) ? $credit->name : '';

                //get created_by_name
                        $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->
                            user_id] : '';

                //get created_by_name
                            $data[$k]['salesman_name'] = isset($staffs[$sale->salesman]) ? $staffs[$sale->
                                salesman] : '';

                //get sales man Catty
                                if($sale->sales_catty_id !=''){
                                    $staffs_catty = $QStaff->getSalesCattyByStore($sale->d_id,$sale->sales_catty_id);
                                }

                                $data[$k]['salescatty_name'] = isset($staffs_catty) ? $staffs_catty[0]['fullname'] : '';

                //get category
                                $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->
                                    cat_id] : '';

                //get good
                                    $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';

                //get goods color
                                    $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->
                                        good_color] : '';

                                        $data[$k]['sale'] = $sale;

               // $data[$k]['total_discount'] = $Credit_Note[0]['total_discount'];

               // $data[$k]['credit_note_list'] = $Credit_Note[0];
                //print_r($Credit_Note);

                                        $data[$k]['total_discount'] = $Credit_Note[0]['total_discount'];

                                        $data[$k]['credit_note_list'] = $Credit_Note;
                                        $data[$k]['deposit_list'] = $deposit;
                                        $data[$k]['total_spc_discount'] = $sale->total_spc_discount;

                                    }

            //Get remain discount
                                    $QMarketDeduction = new Application_Model_MarketDeduction();
                                    $deduction = $QMarketDeduction->getPrice(array('d_id' => $sales[0]['d_id']));

                                    $discount_ck = (isset($deduction[DISCOUNT_CK]) ? $deduction[DISCOUNT_CK] : 0) + (isset($deduction[DISCOUNT_CK_II]) ? $deduction[DISCOUNT_CK_II] : 0) + (isset($deduction[DISCOUNT_CK_III]) ? $deduction[DISCOUNT_CK_III] : 0);
                                    $diamond_discount = (isset($deduction[DISCOUNT_DIAMOND_CLUB]) ? $deduction[DISCOUNT_DIAMOND_CLUB] : 0) + (isset($deduction[DISCOUNT_DIAMOND_CLUB_5]) ? $deduction[DISCOUNT_DIAMOND_CLUB_5] : 0) + (isset($deduction[DISCOUNT_DIAMOND_CLUB_6]) ? $deduction[DISCOUNT_DIAMOND_CLUB_6] : 0) + (isset($deduction[DISCOUNT_DIAMOND_CLUB_7]) ? $deduction[DISCOUNT_DIAMOND_CLUB_7] : 0)+ (isset($deduction[DISCOUNT_DIAMOND_CLUB_8]) ? $deduction[DISCOUNT_DIAMOND_CLUB_8] : 0);

                                    $this->view->discount = $discount_ck;
                                    $this->view->diamond_discount = $diamond_discount;

             //Get remain discount BVG
                                    $discount_bvg = $QMarketProduct->getPriceDiscount(array('d_id' => $sales[0]['d_id']));
                                    $this->view->discount_bvg = $discount_bvg;

            //Get detail discount
                                    $detailDiscount = $QMarketProduct->getDetailDiscount($sn);
            //get detail BVG
                                    $detailBVG = $QMarketProduct->getDetailBVG($sn);

                                    $QBank = new Application_Model_Bank();
                                    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                                    $where_bank = null;
            // Service Show Bank
                                    if (My_Staff_Group::inGroup($userStorage->group_id, array(28))){
                                        $where_bank = array();
                                        $where_bank[] = $QBank->getAdapter()->quoteInto('id IN (1,5,17)');
            // Brand Shop Show Bank
                                    }else if(My_Staff_Group::inGroup($userStorage->group_id, array(25))){
                                        $where_bank = array();
                                        $where_bank[] = $QBank->getAdapter()->quoteInto('id IN (1,3,5,7,8,9,10,16,17)');
                                    }

                                    $banks = $QBank->fetchAll($where_bank,'name asc');
                                    $this->view->banks = $banks;

                                    $this->view->detailBVG = $detailBVG;
                                    $this->view->detailDiscount = $detailDiscount;

                                    $this->view->sales = $data;

            //print_r($data);
                                }
                            }

    //confirm payment received New Function 

                            public function salesConfirm1Action()
                            {
                                $this->view->back_url = HOST . 'finance/sales';

                                $no_show_brandshop = $this->getRequest()->getParam('no_show_brandshop');
        //$no_show_brandshop =1;
                                $sn = $this->getRequest()->getParam('sn');
                                $flashMessenger = $this->_helper->flashMessenger;

                                $messages = $flashMessenger->setNamespace('error')->getMessages();
                                $this->view->messages = $messages;
                                $this->view->no_show_brandshop = $no_show_brandshop;

                                $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                                if ($sn) {

                                    $db = Zend_Registry::get('db');
                                    $QMarket = new Application_Model_Market();
                                    $where = array();
                                    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                                    $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

                                    $sales = $QMarket->fetchAll($where);

            //check
                                    if (!$sales) {
                                        $flashMessenger->setNamespace('error')->addMessage('Invalid SN!');
                                        if(isset($_SESSION["save_search"]) && $_SESSION["save_search"]){
                                            $this->_redirect($_SESSION["save_search"]);
                                        }else{
                                            $this->_redirect('/finance/sales?no_show_brandshop='.$no_show_brandshop);
                                        }
                                    }



                                    $QPG = new Application_Model_PayGroup();
                                    $QPGB = new Application_Model_PayGroupBank();
                                    $QPGC = new Application_Model_PayGroupCause();


                                    /*---------End Get Data From Sales Confirm-------------*/

                                    $QMarketProduct = new Application_Model_MarketProduct();
                                    $QMarket = new Application_Model_Market();

                                    $sn_total = 0;
            $intRebate = intval($QMarketProduct->getPrice($sn)); // số tiền được giảm
            $sn_total = $QMarket->getPrice($sn) - $intRebate; // số tiền còn lại

            $strNoteRebate = '';
            if ($intRebate > 0) {
                $strNoteRebate = ', rebate: ' . $intRebate;
            }

            if($sales[0]['office']){
                $selectArea = $db->select()
                ->from(array('a'=>'office'),array('a.*'))
                ->where('a.id = ?',$sales[0]['office']);
                ;
                $office_area = $db->fetchRow($selectArea);
                $this->view->office_area = $office_area;
            }

            //Store account
            $QStoreaccount = new Application_Model_Storeaccount();
            $QDistributor  = new Application_Model_Distributor();
            $QCampaign     = new Application_Model_Campaign();


            if ($this->getRequest()->getMethod() == 'POST') {


                $db->beginTransaction();
                try {

                    $sn       = $this->getRequest()->getParam('sn');
                    $ch_id       = $this->getRequest()->getParam('ch_id');
                    $d_id       = $this->getRequest()->getParam('d_id');
                    $payment       = $this->getRequest()->getParam('payment');
                    $shipping      = $this->getRequest()->getParam('shipping');
                    $pay_text      = $this->getRequest()->getParam('pay_text');
                    $shipping_text = $this->getRequest()->getParam('shipping_text');
                    $payment_type = $this->getRequest()->getParam('payment_type',NULL);
                    $payment_order = $this->getRequest()->getParam('payment_order', 0);
                    $payment_bank_transfer = $this->getRequest()->getParam('payment_bank_transfer', 0);
                    $payment_service = $this->getRequest()->getParam('payment_service', 0);
                    $payment_servicecharge = $this->getRequest()->getParam('payment_servicecharge', 0);
                    $pay_banktransfer = $this->getRequest()->getParam('pay_banktransfer', 0);
                    $pay_servicecharge = $this->getRequest()->getParam('pay_servicecharge', 0);
                    $pay_service = $this->getRequest()->getParam('pay_service', 0);
                    $file_ = $this->getRequest()->getParam('file_');

                    $pay_time      = $this->getRequest()->getParam('pay_time');
                    $bank          = $this->getRequest()->getParam('select_bank_id', NULL);
                    $type          = 1;
                    $company_id    = $this->getRequest()->getParam('company_id');
                    $retailer_rank = $this->getRequest()->getParam('retailer_rank', NULL);

                    $total_amount  = $this->getRequest()->getParam('total_amount', NULL);

                    $lacksurplus  = $this->getRequest()->getParam('lacksurplus', 0);

                    $payment_no  = $this->getRequest()->getParam('payment_no', 0);

                    $use_credit_card_input   = $this->getRequest()->getParam('use_credit_card_input');


                    $where = array();
                    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                    $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

                    $date = date('Y-m-d H:i:s');

                    /* ------------------------ */
                  // update balance
                    $data['confirm_cash'] = $date;
                    $data['confirm_cash_by'] = $userStorage->id;
                    $QMarket->update($data, $where);


                    /*--------------------------------------------*/
                    //check before commit
                    if ($payment) {
                        $whereCheckMoney       = array();
                        $whereCheckMoney[]     = $QCheckmoney->getAdapter()->quoteInto('sn = ?',$sales[0]['sn']);
                        $checkUpdateCheckMoney = $QCheckmoney->fetchRow($whereCheckMoney);
                        if (!$checkUpdateCheckMoney) {
                            $db->rollback();
                            $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
                            $this->_redirect('/finance/sales-confirm1?sn=' . $sn.'&no_show_brandshop='.$no_show_brandshop);
                        }
                    }

                    $db->commit();
                    $flashMessenger->setNamespace('success')->addMessage('Confirm received Success !!!!');
                    if(isset($_SESSION["save_search"]) && $_SESSION["save_search"]){
                        $this->_redirect($_SESSION["save_search"]);
                    }else{
                        $this->_redirect('/finance/sales?no_show_brandshop='.$no_show_brandshop);
                    }
                }
                catch (exception $e) {
                    $db->rollback();
                    $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!' .
                        $e->getMessage());
                    $this->_redirect('/finance/sales-confirm1?sn=' . $sn.'?no_show_brandshop='.$no_show_brandshop);
                }
                $flashMessenger->setNamespace('success')->addMessage('Done!');
                if(isset($_SESSION["save_search"]) && $_SESSION["save_search"]){
                    $this->_redirect($_SESSION["save_search"]);
                }else{
                    $this->_redirect('/finance/sales?no_show_brandshop='.$no_show_brandshop);
                }

            } //End if check post

            $where = array();
            $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

            $sales = $QMarket->fetchAll($where);

            // print_r($sales);

            $data = array();

            $QGoodCategory = new Application_Model_GoodCategory();
            $categories    = $QGoodCategory->get_cache();

            $QGood         = new Application_Model_Good();
            $goods         = $QGood->get_cache();

            $QGoodColor    = new Application_Model_GoodColor();
            $goodColors    = $QGoodColor->get_cache();

            $QStaff        = new Application_Model_Staff();
            $staffs        = $QStaff->get_cache();

            $QDistributor  = new Application_Model_Distributor();
            $distributors  = $QDistributor->get_cache();

            // get another info of distributor
            $where = $QDistributor->getAdapter()->quoteInto('id = ?', $sales[0]->d_id);
            $distributors_info = $QDistributor->fetchRow($where);

            // get credit from distributor's credit type
            $QCredit  = new Application_Model_Credit();
            $where = $QCredit->getAdapter()->quoteInto('id = ?', $distributors_info->credit_type);
            $credit = $QCredit->fetchRow($where);

            $QWarehouse    = new Application_Model_Warehouse();
            //$warehouses    = $QWarehouse->get_cache();
            $warehouse_type = $userStorage->warehouse_type;
            $where_wh = $QWarehouse->getAdapter()->quoteInto('warehouse_type IN ('.$warehouse_type.')', null);
            $warehouses_cached = $QWarehouse->fetchAll($where_wh, 'name');
            $warehouse_arr = array();
            foreach ($warehouses_cached as $k => $warehouse_data){
                $warehouse_arr[$warehouse_data['id']] = $warehouse_data['name']; 
            }
            $warehouses = $warehouse_arr;
            $this->view->warehouses = $warehouses;

            $Credit_Note = $QMarket->fetchCredit_Note($sn);

            $deposit = $QMarket->fetchDeposit($sn);

            $show_cash_menu=false;
            if (My_Staff_Group::inGroup($userStorage->group_id, OPPO_BRAND_SHOP_SERVICE) || $userStorage->group_id == ADMINISTRATOR_ID )
            {
                $show_cash_menu  = true;
            }

            foreach ($sales as $k => $sale) {
                //get warehouse
                $data[$k]['warehouse_name'] = isset($warehouses[$sale->warehouse_id]) ? $warehouses[$sale->
                    warehouse_id] : '';

                //get retailer
                    $data[$k]['retailer_name'] = isset($distributors[$sale->d_id]) ? $distributors[$sale->
                        d_id] : '';

                //get d_id
                        $data[$k]['d_id'] = $sale->d_id;

                //get retailer : rank
                        $data[$k]['retailer_rank'] = isset($distributors_info->rank) ? $distributors_info->rank : '';

                //get retailer : rank
                        $data[$k]['show_cash_menu'] = $show_cash_menu;

                //get retailer : credit amount
                        $data[$k]['credit_amount'] = isset($distributors_info->credit_amount) ? $distributors_info->credit_amount : '';

                //get retailer : credit type
                        $data[$k]['credit_type'] = isset($credit->name) ? $credit->name : '';

                //get created_by_name
                        $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->
                            user_id] : '';

                //get created_by_name
                            $data[$k]['salesman_name'] = isset($staffs[$sale->salesman]) ? $staffs[$sale->
                                salesman] : '';

                //get sales man Catty
                                if($sale->sales_catty_id !=''){
                                    $staffs_catty = $QStaff->getSalesCattyByStore($sale->d_id,$sale->sales_catty_id);
                                }

                                $data[$k]['salescatty_name'] = isset($staffs_catty) ? $staffs_catty[0]['fullname'] : '';

                //get category
                                $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->
                                    cat_id] : '';

                //get good
                                    $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';

                //get goods color
                                    $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->
                                        good_color] : '';

                                        $data[$k]['sale'] = $sale;


                                        $data[$k]['total_discount'] = $Credit_Note[0]['total_discount'];

                                        $data[$k]['credit_note_list'] = $Credit_Note;
                                        $data[$k]['deposit_list'] = $deposit;
                                        $data[$k]['total_spc_discount'] = $sale->total_spc_discount;

                                    }

             //Get remain discount BVG
                                    $discount_bvg = $QMarketProduct->getPriceDiscount(array('d_id' => $sales[0]['d_id']));
                                    $this->view->discount_bvg = $discount_bvg;

            //Get detail discount
                                    $detailDiscount = $QMarketProduct->getDetailDiscount($sn);
            //get detail BVG
                                    $detailBVG = $QMarketProduct->getDetailBVG($sn);

                                    $QBank = new Application_Model_Bank();
                                    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                                    $this->view->detailBVG = $detailBVG;
                                    $this->view->detailDiscount = $detailDiscount;

                                    $this->view->sales = $data;

                                }
                            }

                            public function salesConfirmPaygroupAction()
                            {
                                $this->view->back_url = HOST . 'finance/sales-payment';

                                $flashMessenger = $this->_helper->flashMessenger;

                                if ($this->getRequest()->getMethod() == 'POST'){

                                    $data_payment_id    = $this->getRequest()->getParam('data_payment_id');
                                    $data_payment_no    = $this->getRequest()->getParam('data_payment_no');
                                    $data_sn                 = $this->getRequest()->getParam('data_sn');

                                    $db = Zend_Registry::get('db');

                                    $db->beginTransaction();

                                    try {

                                        $date = date('Y-m-d H:i:s');

                                        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                                        $QMarket = new Application_Model_Market();
                                        $QCheckmoney = new Application_Model_Checkmoney();

                                        $data = array();
                                        $data['finance_confirm_id'] = $userStorage->id;
                                        $data['finance_confirm_date'] = $date;

                                        $where = $QCheckmoney->getAdapter()->quoteInto('payment_id = ?', $data_payment_id);
                                        $QCheckmoney->update($data,$where);

                                        $data = array();
                                        $data['pay_time'] = $date;
                                        $data['pay_user'] = $userStorage->id;
                                        $data['shipping_yes_time'] = $date;
                                        $data['shipping_yes_id'] = $userStorage->id;
                                        $data['finance_confirm_id'] = $userStorage->id;
                                        $data['finance_confirm_date'] = $date;

                                        $where = $QMarket->getAdapter()->quoteInto('payment_no = ?', $data_payment_no);
                                        $QMarket->update($data, $where);

                //todo log
                                        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

                                        $info = 'Verify: Sale order number: ' . $sn;

                                        $QLog = new Application_Model_Log();

                                        $QLog->insert(array(
                                            'info' => $info,
                                            'user_id' => $userStorage->id,
                                            'ip_address' => $ip,
                                            'time' => $date,
                                        ));

                                        $db->commit();

                                        $messages = $flashMessenger->setNamespace('success')->addMessage('Done!');

                                        echo json_encode(['status' => '201', 'message' => 'Done.', 'url' => HOST.'finance/sales-payment']);

                // $this->_redirect('/sales');

                                        exit();

                                    }
                                    catch (exception $e) {

                                        $db->rollback();

                // if($e->getMessage()){
                //     $messages = $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
                // }else{
                //     $messages = $flashMessenger->setNamespace('error')->addMessage('Cannot create payment group, please try again! : ' . $e->getMessage());
                // }

                                        echo json_encode(['status' => '400', 'message' => $e->getMessage()]);

                // $this->_redirect('/sales/create-payment-group');

                                        exit();
                                    }

                                }

                                $payment_no = $this->getRequest()->getParam('paygroup');

                                $QPG = new Application_Model_PayGroup();
                                $priceAndDetails = $QPG->getPaymentGroup($payment_no);

                                if(count($priceAndDetails) < 1){
                                    array_push($messages, 'ບໍ່ມີຂໍ້ມູນ');
                                }else{

                                    $part_pay_one = HOST . 'public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'finance';
                                    $part_pay_group = HOST . 'public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'payment_group'. DIRECTORY_SEPARATOR . 'pay_slips' . DIRECTORY_SEPARATOR;

                                    $partImg = '';

                                    switch ($priceAndDetails[0]['payment_group']) {
                                        case '0':
                                        $partImg = $part_pay_one;
                                        break;
                                        case '1':
                                        $partImg = $part_pay_group;
                                        break;
                                    }

                                    $this->view->partImg = $partImg;

                                    $arrDataDetail = [];



                                    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                                    $db = Zend_Registry::get('db');
                                    $QMarket = new Application_Model_Market();

                                    $QGoodCategory = new Application_Model_GoodCategory();
                                    $categories    = $QGoodCategory->get_cache();

                                    $QGood         = new Application_Model_Good();
                                    $goods         = $QGood->get_cache();

                                    $QGoodColor    = new Application_Model_GoodColor();
                                    $goodColors    = $QGoodColor->get_cache();

                                    $QStaff        = new Application_Model_Staff();
                                    $staffs        = $QStaff->get_cache();

                                    $QDistributor  = new Application_Model_Distributor();
                                    $distributors  = $QDistributor->get_cache();

            // get another info of distributor
                                    $where = $QDistributor->getAdapter()->quoteInto('id = ?', $sales[0]->d_id);
                                    $distributors_info = $QDistributor->fetchRow($where);

            // get credit from distributor's credit type
                                    $QCredit  = new Application_Model_Credit();
                                    $where = $QCredit->getAdapter()->quoteInto('id = ?', $distributors_info->credit_type);
                                    $credit = $QCredit->fetchRow($where);

                                    $QWarehouse    = new Application_Model_Warehouse();
            //$warehouses    = $QWarehouse->get_cache();
                                    $warehouse_type = $userStorage->warehouse_type;
                                    $where_wh = $QWarehouse->getAdapter()->quoteInto('warehouse_type IN ('.$warehouse_type.')', null);
                                    $warehouses_cached = $QWarehouse->fetchAll($where_wh, 'name');
                                    $warehouse_arr = array();
                                    foreach ($warehouses_cached as $k => $warehouse_data){
                                        $warehouse_arr[$warehouse_data['id']] = $warehouse_data['name']; 
                                    }
                                    $warehouses = $warehouse_arr;
                                    $this->view->warehouses = $warehouses;

                                    $show_cash_menu=false;
                                    if (My_Staff_Group::inGroup($userStorage->group_id, OPPO_BRAND_SHOP_SERVICE) || $userStorage->group_id == ADMINISTRATOR_ID )
                                    {
                                        $show_cash_menu  = true;
                                    }


                                    $arr_distributor = [];
                                    $bucketData = [];
                                    $sn_list="";
                                    foreach ($priceAndDetails as $key) {
                                        array_push($arr_distributor, $key['d_id']);

                                        $Credit_Note = $QMarket->fetchCredit_Note($key['sale_order']);
                                        $sn_list .=$key['sale_order'].",";

                                        $where = array();
                                        $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $key['sale_order']);
                                        $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

                                        $sales = $QMarket->fetchAll($where);

                                        $data = array();

                                        foreach ($sales as $k => $sale) {
                    //get warehouse
                                            $data[$k]['warehouse_name'] = isset($warehouses[$sale->warehouse_id]) ? $warehouses[$sale->
                                                warehouse_id] : '';

                    //get retailer
                    //print_r($distributors);
                                                $data[$k]['retailer_name'] = isset($distributors[$sale->d_id]) ? $distributors[$sale->
                                                    d_id] : '';

                    //get d_id
                                                    $data[$k]['d_id'] = $sale->d_id;

                    //get retailer : rank
                                                    $data[$k]['retailer_rank'] = isset($distributors_info->rank) ? $distributors_info->rank : '';

                    //get retailer : rank
                                                    $data[$k]['show_cash_menu'] = $show_cash_menu;

                    //get retailer : credit amount
                                                    $data[$k]['credit_amount'] = isset($distributors_info->credit_amount) ? $distributors_info->credit_amount : '';

                    //get retailer : credit type
                                                    $data[$k]['credit_type'] = isset($credit->name) ? $credit->name : '';

                    //get created_by_name
                                                    $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->
                                                        user_id] : '';

                    //get created_by_name
                                                        $data[$k]['salesman_name'] = isset($staffs[$sale->salesman]) ? $staffs[$sale->
                                                            salesman] : '';

                    //get sales man Catty
                                                            if($sale->sales_catty_id !=''){
                                                                $staffs_catty = $QStaff->getSalesCattyByStore($sale->d_id,$sale->sales_catty_id);
                                                            }

                                                            $data[$k]['salescatty_name'] = isset($staffs_catty) ? $staffs_catty[0]['fullname'] : '';

                    //get category
                                                            $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->
                                                                cat_id] : '';

                    //get good
                                                                $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';

                    //get goods color
                                                                $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->
                                                                    good_color] : '';

                                                                    $data[$k]['sale'] = $sale;

                   // $data[$k]['total_discount'] = $Credit_Note[0]['total_discount'];

                   // $data[$k]['credit_note_list'] = $Credit_Note[0];
                    //print_r($Credit_Note[0]);

                                                                    $data[$k]['total_discount'] = $Credit_Note[0]['total_discount'];

                                                                    $data[$k]['credit_note_list'] = $Credit_Note;
                                                                    $data[$k]['total_spc_discount'] = $sale->total_spc_discount;

                                                                }

                                                                array_push($bucketData, $data);

                                                            }

                                                            $Credit_Note_All = $QMarket->fetchCredit_Note_All(rtrim($sn_list, ','));

                                                            $this->view->bucketData = $bucketData;
                                                            $this->view->Credit_Note_All = $Credit_Note_All;

                                                            $distinct_arr_distributor = array_unique($arr_distributor);
                                                            $distinct_arr_distributor = array_values($distinct_arr_distributor);

                                                            $this->view->priceAndDetails = $priceAndDetails;

                                                            $QPGB = new Application_Model_PayGroupBank();
                                                            $getDetailPayBank = $QPGB->getPaymentGroupBank($priceAndDetails[0]['payment_id']);

                                                            $this->view->detailPayBank = $getDetailPayBank;

                                                            $QPGT = new Application_Model_PayGroupTran();
                                                            $usePaygroup = $QPGT->getUsePaymentGroupByPaymentID($priceAndDetails[0]['payment_id']);

                                                            $this->view->usePaygroup = $usePaygroup;

                                                            $messages = $flashMessenger->setNamespace('error')->getMessages();

            // $usePaygroup = [];
            // $QPGBal = new Application_Model_PayGroupBalance();

            // if(count($distinct_arr_distributor) <= 0 || count($distinct_arr_distributor) > 1){
            //     array_push($messages, 'ไม่สามารถสร้างกลุ่มใบเปอินได้ เนื่องจากมี Distributor มากว่า 1 รายการ');
            // }else{
            //     $usePaygroup = $QPGBal->getUsePaygroup($arr_distributor[0]);
            // }

            // $QPG = new Application_Model_PayGroup();
            // $checkPaygroup = $QPG->checkPaymentGroup($arraySn);

            // if(count($checkPaygroup) > 0){
            //     array_push($messages, 'ไม่สามารถสร้างกลุ่มใบเปอินได้ เนื่องจากมีรายการ SO ที่ถูกสร้างในกลุ่มใบเปอินอื่นเเล้ว');

            // $this->view->usePaygroup = $usePaygroup;

            // }

                                                        }

                                                        $this->view->messages = $messages;  
                                                    }

                                                    public function deleteFinanceConfirmAction()
                                                    {

                                                        $d_id = $this->getRequest()->getParam('d_id');
                                                        $ch_id = $this->getRequest()->getParam('ch_id');
                                                        $QStoreaccount  = new Application_Model_Storeaccount();
                                                        $QCheckmoney = new Application_Model_Checkmoney();
                                                        $where = $QCheckmoney->getAdapter()->quoteInto('id = ?', $ch_id);
                                                        $QCheckmoney->delete($where);
                                                        $QStoreaccount->updateBalance($d_id);


                                                    }
    //Tanong
                                                    public function returnConfirmAction()
                                                    {
                                                        $sn = $this->getRequest()->getParam('sn');

        //die;
                                                        if ($sn){

                                                            $QMarket = new Application_Model_Market();
                                                            $db = Zend_Registry::get('db');

                                                            if ($this->getRequest()->getMethod() == 'POST') {

                                                                $payment = $this->getRequest()->getParam('payment');
                                                                $pay_text = $this->getRequest()->getParam('pay_text');
                                                                $distributor_id = $this->getRequest()->getParam('distributer_id');
                                                                $create_cn = $this->getRequest()->getParam('create_cn','0');
                                                                $active_cn = $this->getRequest()->getParam('active_cn','0');
                                                                $return_type = $this->getRequest()->getParam('return_type',null);

                                                                if ($payment) {
                                                                    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                                                                    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);

                                                                    $data = array('pay_text' => $pay_text, );

                                                                    $date = date('Y-m-d H:i:s');

                                                                    $data['pay_time'] = $date;
                                                                    $data['pay_user'] = $userStorage->id;

                                                                    $data['shipping_yes_time'] = $date;
                                                                    $data['shipping_yes_id'] = $userStorage->id;

                                                                    $data['create_cn'] = $create_cn;
                                                                    $data['active_cn'] = $active_cn;

                                                                    $data['finance_confirm_date'] = $date;
                                                                    $data['finance_confirm_id'] = $userStorage->id;

                                                                    $return_type_status = null;
                                                                    if(isset($create_cn) && $create_cn == 1){
                                                                        $return_type_status = $return_type;
                                                                    }
                                                                    $data['return_type'] = $return_type_status;

                                                                    $flashMessenger = $this->_helper->flashMessenger;
                                                                    try {

                                                                        $db->beginTransaction();

                        // Tanong Create CN And ReCalcurate total_money,use_money,balance_money
                                                                        $QMarket->update($data, $where);

                        //todo update imei & accessories return table
                                                                        $data = array(
                                                                            'confirmed_at' => $date,
                                                                            'confirmed_by' => $userStorage->id,
                                                                        );

                                                                        $QImeiReturn = new Application_Model_ImeiReturn();
                                                                        $where = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
                                                                        $QImeiReturn->update($data, $where);


                                                                        $QProductReturn = new Application_Model_ProductReturn();
                                                                        $where = $QProductReturn->getAdapter()->quoteInto('return_sn = ?', $sn);
                                                                        $QProductReturn->update($data, $where);
                        // if($create_cn=='checked'){
                                                                        if(isset($create_cn) && $create_cn == 1){
                        //Tanong
                            // if($active_cn=="checked"){
                            //     $status="1";
                            // }else{
                            //     $status="0";
                            // }

                                                                            $status = 0;
                                                                            if(isset($active_cn) && $active_cn){
                                                                                $status = $active_cn;
                                                                            }

                                                                            $creditnote_sn = $this->get_credit_note_sn($db,$distributor_id,$userStorage->id,$sn,$status,$return_type);
                                                                            if($creditnote_sn==''){
                                                                                for($i=0;$i<3;$i++){ 
                                                                                    if($creditnote_sn==''){
                                                                                        $creditnote_sn = $this->get_credit_note_sn($db,$distributor_id,$userStorage->id,$sn,$status,$return_type);
                                                                                    }
                                                                                }
                                                                            }

                                                                        }

                        //todo log
                                                                        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

                                                                        $info = 'Verify: Return order number: ' . $sn.' CN='.$creditnote_sn;

                                                                        $QLog = new Application_Model_Log();

                                                                        $QLog->insert(array(
                                                                            'info' => $info,
                                                                            'user_id' => $userStorage->id,
                                                                            'ip_address' => $ip,
                                                                            'time' => $date,
                                                                        ));
                                                                        if(isset($create_cn) && $create_cn == 1){
                                                                            if($creditnote_sn !=''){
                                                                                $flashMessenger->setNamespace('success')->addMessage('Done!');
                                                                                $db->commit(); 
                                                                            }else{
                                                                                $db->rollback();
                                                                                $flashMessenger->setNamespace('error')->addMessage('Cannot Create CN For Return, Please try again!');
                                                                            }
                                                                        }else{
                                                                            $flashMessenger->setNamespace('success')->addMessage('Done!');
                                                                            $db->commit();  
                                                                        }



                                                                    }
                                                                    catch (exception $e) {
                                                                        $db->rollback();
                                                                        $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
                                                                    }
                                                                }

                                                                $this->_redirect('/finance/return-list');
                                                            }

                                                            $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                                                            $sales = $QMarket->fetchAll($where);
            //print_r($sales);
                                                            $data = array();

                                                            $QGoodCategory = new Application_Model_GoodCategory();
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

                                                            foreach ($sales as $k => $sale) {
                //get warehouse
                                                                $data[$k]['warehouse_name'] = isset($warehouses[$sale->backs_d_id]) ? $warehouses[$sale->
                                                                    backs_d_id] : '';

                //get retailer id
                                                                    $data[$k]['distributer_id'] = $sale->d_id;

                //get create cn
                                                                    $data[$k]['create_cn'] = $sale->create_cn;

                //get retailer
                                                                    $data[$k]['retailer_name'] = isset($distributors[$sale->d_id]) ? $distributors[$sale->
                                                                        d_id] : '';

                //get created_by_name
                                                                        $data[$k]['created_by_name'] = isset($staffs[$sale->user_id]) ? $staffs[$sale->
                                                                            user_id] : '';

                //get category
                                                                            $data[$k]['category'] = isset($categories[$sale->cat_id]) ? $categories[$sale->
                                                                                cat_id] : '';

                //get good
                                                                                $data[$k]['good'] = isset($goods[$sale->good_id]) ? $goods[$sale->good_id] : '';

                //get goods color
                                                                                $data[$k]['color'] = isset($goodColors[$sale->good_color]) ? $goodColors[$sale->
                                                                                    good_color] : '';

                                                                                    $data[$k]['sale'] = $sale;
                                                                                }

                                                                                $this->view->sales = $data;


                                                                            }
                                                                        }

                                                                        public function salesAction()
                                                                        {
        // auto refresh
        // print_r(111);

                                                                            if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']){
                                                                                session_start();
                                                                                $_SESSION["save_search"] = $_SERVER['REQUEST_URI'];
                                                                                session_write_close();
                                                                            }

                                                                            $this->view->meta_refresh = 300;

                                                                            $sort               = $this->getRequest()->getParam('sort', 'p.sales_confirm_date');
                                                                            $desc               = $this->getRequest()->getParam('desc', 0);
                                                                            $page               = $this->getRequest()->getParam('page', 1);

                                                                            $sn                 = $this->getRequest()->getParam('sn');
                                                                            $d_id               = $this->getRequest()->getParam('d_id');
                                                                            $good_id            = $this->getRequest()->getParam('good_id');
                                                                            $good_color         = $this->getRequest()->getParam('good_color');
                                                                            $num                = $this->getRequest()->getParam('num');
                                                                            $price              = $this->getRequest()->getParam('price');
                                                                            $created_at_to      = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));
                                                                            $created_at_from    = $this->getRequest()->getParam('created_at_from', date('d/m/Y', strtotime('-1 month')));
                                                                            $cat_id             = $this->getRequest()->getParam('cat_id');
                                                                            $warehouse_id       = $this->getRequest()->getParam('warehouse_id');
                                                                            $export             = $this->getRequest()->getParam('export', 0);
                                                                            $export_distributor = $this->getRequest()->getParam('export_distributor', 0);
                                                                            $export_warehouse   = $this->getRequest()->getParam('export_warehouse', 0);
                                                                            $tags               = $this->getRequest()->getParam('tags');

                                                                            $sn_munti           = $this->getRequest()->getParam('sn_munti');
                                                                            $sn_munti = array_values(array_unique($sn_munti));

                                                                            $no_show_brandshop  = $this->getRequest()->getParam('no_show_brandshop', 0);

                                                                            $rank               = $this->getRequest()->getParam('rank');
                                                                            $this->view->rank = $rank;
                                                                            $this->view->d_id = $d_id;

                                                                            $finance_group    = $this->getRequest()->getParam('finance_group');

                                                                            $QDistributor = new Application_Model_Distributor();
                                                                            $this->view->finance_group = $QDistributor->getFinanceGroup();

                                                                            $counter            = $this->getRequest()->getParam('counter', 0);
                                                                            $this->view->counter = $counter+1;

                                                                            if($counter < 1){
                                                                                $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                                                                                if (My_Staff_Group::inGroup($userStorage->group_id, array(CHECK_MONEY))){
                                                                                    $no_show_brandshop = 1;
                                                                                }
                                                                            }

                                                                            $limit = LIMITATION;
                                                                            $total = 0;

                                                                            if ($tags and is_array($tags))
                                                                                $tags = $tags;
                                                                            else
                                                                                $tags = null;

                                                                            $params = array_filter(array(
                                                                                'sn'              => $sn,
                                                                                'd_id'            => $d_id,
                                                                                'good_id'         => $good_id,
                                                                                'good_color'      => $good_color,
                                                                                'num'             => $num,
                                                                                'price'           => $price,
                                                                                'total'           => $total,
                                                                                'created_at_to'   => $created_at_to,
                                                                                'created_at_from' => $created_at_from,
            'from'            => $created_at_from, // tương tự 2 biến trên nhưng dùng cho function khác
            'to'              => $created_at_to, // (làm biếng đổi tên)
            'cat_id'          => $cat_id,
            'warehouse_id'    => $warehouse_id,
            'tags'            => $tags,
            'sn_munti'        => $sn_munti,
            'confirm_so'      => 1,                  // check confirm so before finance confirm
            'no_show_brandshop' => $no_show_brandshop,
            'counter'         => $counter,
            'rank'            => $rank,
            'finance_group'   => $finance_group
        ));

                                                                            if ($export_distributor == 1) {
                                                                                $this->_export_distributor($params);

                                                                                exit;
                                                                            }

                                                                            if ($export_warehouse == 1) {
                                                                                $this->_export_warehouse($params);

                                                                                exit;
                                                                            }

                                                                            $params['group_sn'] = true;
                                                                            $params['finance'] = true;
                                                                            $params['isbacks'] = false;
                                                                            $params['status'] = 1;

                                                                            $params['cancel'] = 0;

                                                                            $QGood          = new Application_Model_Good();
                                                                            $QGoodColor     = new Application_Model_GoodColor();
                                                                            $QMarket        = new Application_Model_Market();
                                                                            $QDistributor   = new Application_Model_Distributor();
                                                                            $QGoodCategory  = new Application_Model_GoodCategory();
                                                                            $QWarehouse     = new Application_Model_Warehouse();
                                                                            $QMarketProduct = new Application_Model_MarketProduct();

                                                                            $goods             = $QGood->get_cache();
                                                                            $goodColors        = $QGoodColor->get_cache();
                                                                            $distributors      = $QDistributor->get_with_store_code_cache();
                                                                            $good_categories   = $QGoodCategory->get_cache();
                                                                            $warehouses_cached = $QWarehouse->get_cache();

                                                                            $params['sort'] = $sort;
                                                                            $params['desc'] = $desc;

                                                                            $params['leftjoin_checkmoney'] = 'OK';

                                                                            if (isset($export) && $export) {
                                                                                $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
                                                                                $this->_exportExcel($markets_sn);
                                                                            }

                                                                            $params['get_fields'] = array(
                                                                                'sn',
                                                                                'd_id',
                                                                                'pay_time',
                                                                                'shipping_yes_time',
                                                                                'outmysql_time',
                                                                                'warehouse_id',
                                                                                'status',
                                                                                'add_time',
                                                                                'last_updated_at',
                                                                                'pay_text',
                                                                                'payment_type'
                                                                            );

                                                                            $markets_sn = $QMarket->fetchPagination($page, $limit, $total, $params);

        // print_r($markets_sn);die;

                                                                            $markets_sn_array = array();

                                                                            foreach($markets_sn as $k => $v)
                                                                            {
                                                                                $markets_sn_array[$k] = $v;
                                                                                $markets_sn_array[$k]['discount'] = $QMarketProduct->getDiscount($v['sn']);
                                                                            }

                                                                            $markets = array();

                                                                            unset($params['finance']);
                                                                            unset($params['group_sn']);
                                                                            unset($params['get_fields']);

                                                                            $params['sn'] = $sn;

                                                                            $this->view->goods             = $goods;
                                                                            $this->view->goodColors        = $goodColors;
                                                                            $this->view->markets           = $markets;
                                                                            $this->view->markets_sn        = $markets_sn_array;
                                                                            $this->view->distributors      = $distributors;
                                                                            $this->view->good_categories   = $good_categories;
                                                                            $this->view->warehouses_cached = $warehouses_cached;

                                                                            $this->view->desc   = $desc;
                                                                            $this->view->sort   = $sort;
                                                                            $this->view->params = $params;
                                                                            $this->view->limit  = $limit;
                                                                            $this->view->total  = $total;
                                                                            $this->view->no_show_brandshop = $no_show_brandshop;
                                                                            $this->view->url    = HOST . 'finance/sales/' . ($params ? '?' . http_build_query($params) .
                                                                                '&' : '?');

                                                                            $this->view->offset = $limit * ($page - 1);

                                                                            $flashMessenger = $this->_helper->flashMessenger;
                                                                            $messages = $flashMessenger->setNamespace('error')->getMessages();
                                                                            $this->view->messages = $messages;

                                                                            $messages_success = $flashMessenger->setNamespace('success')->getMessages();
                                                                            $this->view->messages_success = $messages_success;

                                                                            if ($this->getRequest()->isXmlHttpRequest()) {
                                                                                $this->_helper->layout->disableLayout();

                                                                                $this->_helper->viewRenderer->setRender('partials/list');
                                                                            }
                                                                        }

                                                                        public function salesPaymentAction()
                                                                        {

                                                                            if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']){
                                                                                session_start();
                                                                                $_SESSION["save_search"] = $_SERVER['REQUEST_URI'];
                                                                                session_write_close();
                                                                            }

                                                                            $this->view->meta_refresh = 300;

                                                                            $sort               = $this->getRequest()->getParam('sort', 'sales_confirm_date');
                                                                            $desc               = $this->getRequest()->getParam('desc', 0);
                                                                            $page               = $this->getRequest()->getParam('page', 1);

                                                                            $sn                 = $this->getRequest()->getParam('sn');
                                                                            $payment_no                 = $this->getRequest()->getParam('payment_no');
                                                                            $created_at_to      = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));
                                                                            $created_at_from    = $this->getRequest()->getParam('created_at_from', date('d/m/Y', strtotime('-1 month')));
                                                                            $warehouse_id       = $this->getRequest()->getParam('warehouse_id');
                                                                            $export             = $this->getRequest()->getParam('export', 0);
                                                                            $tags               = $this->getRequest()->getParam('tags');

                                                                            $sn_munti           = $this->getRequest()->getParam('sn_munti');
                                                                            $sn_munti = array_values(array_unique($sn_munti));

                                                                            $no_show_brandshop  = $this->getRequest()->getParam('no_show_brandshop', 0);

                                                                            $rank               = $this->getRequest()->getParam('rank');
                                                                            $d_id               = $this->getRequest()->getParam('d_id');
                                                                            $distributor_name   = $this->getRequest()->getParam('distributor_name');
                                                                            $this->view->rank = $rank;
                                                                            $this->view->d_id = $d_id;

                                                                            $finance_group    = $this->getRequest()->getParam('finance_group');

                                                                            $QDistributor = new Application_Model_Distributor();
                                                                            $this->view->finance_group = $QDistributor->getFinanceGroup();

                                                                            $counter            = $this->getRequest()->getParam('counter', 0);
                                                                            $this->view->counter = $counter+1;

                                                                            if($counter < 1){
                                                                                $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                                                                                if (My_Staff_Group::inGroup($userStorage->group_id, array(CHECK_MONEY))){
                                                                                    $no_show_brandshop = 1;
                                                                                }
                                                                            }

                                                                            $limit = LIMITATION;
                                                                            $total = 0;

                                                                            if ($tags and is_array($tags))
                                                                                $tags = $tags;
                                                                            else
                                                                                $tags = null;

                                                                            $params = array_filter(array(
                                                                                'sn'              => $sn,
                                                                                'payment_no'      => $payment_no,
                                                                                'd_id'            => $d_id,
                                                                                'distributor_name'=> $distributor_name,
                                                                                'created_at_to'   => $created_at_to,
                                                                                'created_at_from' => $created_at_from,
                                                                                'warehouse_id'    => $warehouse_id,
                                                                                'tags'            => $tags,
                                                                                'sn_munti'        => $sn_munti,
            'confirm_so'      => 1,                  // check confirm so before finance confirm
            'no_show_brandshop' => $no_show_brandshop,
            'counter'         => $counter,
            'rank'            => $rank,
            'finance_group'   => $finance_group
        ));

                                                                            $params['group_sn'] = true;
                                                                            $params['finance'] = true;
                                                                            $params['isbacks'] = false;
                                                                            $params['status'] = 1;

                                                                            $params['cancel'] = 0;

                                                                            $QGood          = new Application_Model_Good();
                                                                            $QGoodColor     = new Application_Model_GoodColor();
                                                                            $QMarket        = new Application_Model_Market();
                                                                            $QDistributor   = new Application_Model_Distributor();
                                                                            $QGoodCategory  = new Application_Model_GoodCategory();
                                                                            $QWarehouse     = new Application_Model_Warehouse();
                                                                            $QMarketProduct = new Application_Model_MarketProduct();
                                                                            $QCheckMoney    = new Application_Model_Checkmoney();

                                                                            $goods             = $QGood->get_cache();
                                                                            $goodColors        = $QGoodColor->get_cache();
                                                                            $distributors      = $QDistributor->get_with_store_code_cache();
                                                                            $good_categories   = $QGoodCategory->get_cache();
                                                                            $warehouses_cached = $QWarehouse->get_cache();

                                                                            $params['sort'] = $sort;
                                                                            $params['desc'] = $desc;

                                                                            if (isset($export) && $export) {
                                                                                $markets_sn = $QMarket->fetchPaginationPayGroup($page, null, $total, $params);
                                                                                $this->_exportExcelPaymentGroup($markets_sn);
                                                                            }

                                                                            $params['get_fields'] = array(
                                                                                'sn',
                                                                                'd_id',
                                                                                'pay_time',
                                                                                'shipping_yes_time',
                                                                                'outmysql_time',
                                                                                'warehouse_id',
                                                                                'status',
                                                                                'add_time',
                                                                                'last_updated_at',
                                                                                'pay_text',
                                                                                'payment_type',
                                                                                'pay_group',
                                                                                'payment_no'
                                                                            );

        // $markets_sn = $QMarket->fetchPagination($page, $limit, $total, $params);
                                                                            $markets_sn = $QMarket->fetchPaginationPayGroup($page, $limit, $total, $params);

        // print_r($markets_sn);die;

                                                                            $markets_sn_array = array();

                                                                            foreach($markets_sn as $k => $v)
                                                                            {
                                                                                $markets_sn_array[$k] = $v;
                                                                                $markets_sn_array[$k]['discount'] = $QMarketProduct->getDiscount($v['sn']);
                                                                            }

                                                                            $markets = array();

                                                                            unset($params['finance']);
                                                                            unset($params['group_sn']);
                                                                            unset($params['get_fields']);

                                                                            $params['sn'] = $sn;

                                                                            $this->view->goods             = $goods;
                                                                            $this->view->goodColors        = $goodColors;
                                                                            $this->view->markets           = $markets;
                                                                            $this->view->markets_sn        = $markets_sn_array;
                                                                            $this->view->distributors      = $distributors;
                                                                            $this->view->good_categories   = $good_categories;
                                                                            $this->view->warehouses_cached = $warehouses_cached;

                                                                            $this->view->desc   = $desc;
                                                                            $this->view->sort   = $sort;
                                                                            $this->view->params = $params;
                                                                            $this->view->limit  = $limit;
                                                                            $this->view->total  = $total;
                                                                            $this->view->no_show_brandshop = $no_show_brandshop;
                                                                            $this->view->url    = HOST . 'finance/sales-payment/' . ($params ? '?' . http_build_query($params) .
                                                                                '&' : '?');

                                                                            $this->view->offset = $limit * ($page - 1);

                                                                            $flashMessenger = $this->_helper->flashMessenger;
                                                                            $messages = $flashMessenger->setNamespace('error')->getMessages();
                                                                            $this->view->messages = $messages;

                                                                            $messages_success = $flashMessenger->setNamespace('success')->getMessages();
                                                                            $this->view->messages_success = $messages_success;

                                                                            if ($this->getRequest()->isXmlHttpRequest()) {
                                                                                $this->_helper->layout->disableLayout();

                                                                                $this->_helper->viewRenderer->setRender('partials/list');
                                                                            }
                                                                        }

                                                                        public function paymentnoListAction()
                                                                        {

                                                                            $this->view->meta_refresh = 300;

                                                                            $sort               = $this->getRequest()->getParam('sort');
                                                                            $desc               = $this->getRequest()->getParam('desc', 0);
                                                                            $page               = $this->getRequest()->getParam('page', 1);

                                                                            $payment_no         = $this->getRequest()->getParam('payment_no');
                                                                            $d_id               = $this->getRequest()->getParam('d_id');
                                                                            $distributor_name   = $this->getRequest()->getParam('distributor_name');
                                                                            $rank               = $this->getRequest()->getParam('rank');
                                                                            $export             = $this->getRequest()->getParam('export', 0);

                                                                            $limit = LIMITATION;
                                                                            $total = 0;

                                                                            $this->view->rank = $rank;
                                                                            $this->view->d_id = $d_id;

                                                                            $params = array_filter(array(
                                                                                'payment_no'       => $payment_no,
                                                                                'd_id'             => $d_id,
                                                                                'distributor_name' => $distributor_name
                                                                            ));

                                                                            $QPGBal = new Application_Model_PayGroupBalance();
                                                                            $QDistributor   = new Application_Model_Distributor();

                                                                            $distributors = $QDistributor->get_with_store_code_cache();

                                                                            $params['sort'] = $sort;
                                                                            $params['desc'] = $desc;

                                                                            if (isset($export) && $export) {
                                                                                $get_paymentno = $QPGBal->fetchPagination($page, null, $total, $params);
                                                                                $this->_exportExcelListPaymentNo($get_paymentno);
                                                                            }

        // $params['get_fields'] = array(
        //     'sn',
        //     'd_id',
        //     'pay_time',
        //     'shipping_yes_time',
        //     'outmysql_time',
        //     'warehouse_id',
        //     'status',
        //     'add_time',
        //     'last_updated_at',
        //     'pay_text',
        //     'payment_type',
        //     'pay_group',
        //     'payment_no'
        //     );

                                                                            $get_paymentno = $QPGBal->fetchPagination($page, $limit, $total, $params);

        // print_r($get_paymentno);die;

                                                                            $this->view->get_paymentno     = $get_paymentno;
                                                                            $this->view->distributors      = $distributors;

                                                                            $this->view->desc   = $desc;
                                                                            $this->view->sort   = $sort;
                                                                            $this->view->params = $params;
                                                                            $this->view->limit  = $limit;
                                                                            $this->view->total  = $total;
                                                                            $this->view->no_show_brandshop = $no_show_brandshop;
                                                                            $this->view->url    = HOST . 'finance/paymentno-list/' . ($params ? '?' . http_build_query($params) .
                                                                                '&' : '?');

                                                                            $this->view->offset = $limit * ($page - 1);

                                                                            $flashMessenger = $this->_helper->flashMessenger;
                                                                            $messages = $flashMessenger->setNamespace('error')->getMessages();
                                                                            $this->view->messages = $messages;

                                                                            $messages_success = $flashMessenger->setNamespace('success')->getMessages();
                                                                            $this->view->messages_success = $messages_success;

                                                                            if ($this->getRequest()->isXmlHttpRequest()) {
                                                                                $this->_helper->layout->disableLayout();

                                                                                $this->_helper->viewRenderer->setRender('partials/list');
                                                                            }
                                                                        }

                                                                        public function paymentnoLogAction()
                                                                        {

                                                                            $this->view->back_url = HOST . 'finance/paymentno-list';
                                                                            $this->view->meta_refresh = 300;

                                                                            $d_id               = $this->getRequest()->getParam('d_id');

                                                                            $sort               = $this->getRequest()->getParam('sort','create_date');
                                                                            $desc               = $this->getRequest()->getParam('desc', 1);
                                                                            $page               = $this->getRequest()->getParam('page', 1);

                                                                            $from_payment_no         = $this->getRequest()->getParam('from_payment_no');
                                                                            $to_payment_no         = $this->getRequest()->getParam('to_payment_no');
                                                                            $created_at_to     = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));
                                                                            $created_at_from   = $this->getRequest()->getParam('created_at_from', date('d/m/Y', strtotime('-0 day')));
                                                                            $export             = $this->getRequest()->getParam('export', 0);

                                                                            $limit = LIMITATION;
                                                                            $total = 0;

                                                                            $params = array_filter(array(
                                                                                'd_id'            => $d_id,
                                                                                'from_payment_no' => $from_payment_no,
                                                                                'to_payment_no'   => $to_payment_no,
                                                                                'created_at_to'   => $created_at_to,
                                                                                'created_at_from'   => $created_at_from
                                                                            ));

                                                                            $params['sort'] = $sort;
                                                                            $params['desc'] = $desc;

                                                                            $QPGT = new Application_Model_PayGroupTran();

                                                                            if (isset($export) && $export) {
                                                                                $get_paymentno_log = $QPGT->fetchPagination($page, null, $total, $params);
                                                                                $this->_exportExcelPaymentNoLog($get_paymentno_log);
                                                                            }

                                                                            $get_paymentno_log = $QPGT->fetchPagination($page, $limit, $total, $params);

        // print_r($get_paymentno_log);die;

                                                                            $QDistributor  = new Application_Model_Distributor();
                                                                            $distributors  = $QDistributor->get_cache();

                                                                            $d_name = isset($distributors[$d_id]) ? $distributors[$d_id] : '';

                                                                            $this->view->distributor = ['id' => $d_id,'name' => $d_name];

                                                                            $this->view->get_paymentno_log     = $get_paymentno_log;
                                                                            $this->view->distributors      = $distributors;

                                                                            $this->view->desc   = $desc;
                                                                            $this->view->sort   = $sort;
                                                                            $this->view->params = $params;
                                                                            $this->view->limit  = $limit;
                                                                            $this->view->total  = $total;
                                                                            $this->view->no_show_brandshop = $no_show_brandshop;
                                                                            $this->view->url    = HOST . 'finance/paymentno-log/' . ($params ? '?' . http_build_query($params) .
                                                                                '&' : '?');

                                                                            $this->view->offset = $limit * ($page - 1);

                                                                            $flashMessenger = $this->_helper->flashMessenger;
                                                                            $messages = $flashMessenger->setNamespace('error')->getMessages();
                                                                            $this->view->messages = $messages;

                                                                            $messages_success = $flashMessenger->setNamespace('success')->getMessages();
                                                                            $this->view->messages_success = $messages_success;

                                                                            if ($this->getRequest()->isXmlHttpRequest()) {
                                                                                $this->_helper->layout->disableLayout();

                                                                                $this->_helper->viewRenderer->setRender('partials/list');
                                                                            }
                                                                        }

                                                                        private function _exportExcel($data)
                                                                        {
                                                                            require_once 'PHPExcel.php';
                                                                            $PHPExcel = new PHPExcel();
                                                                            $heads = array(
                                                                                'No.',
                                                                                'SALE ORDER NUMBER',
                                                                                'DISTRIBUTOR NAME',
                                                                                'PRODUCT NAME',
                                                                                'PRODUCT COLOR',
                                                                                'SALES QUANTITY',
                                                                                'SALES PRICE',
                                                                                'TOTAL',
                                                                                'PAYMENT OR NOT',
                                                                                'SHIPPING',
                                                                                'ORDER TIME');

                                                                            $PHPExcel->setActiveSheetIndex(0);
                                                                            $sheet = $PHPExcel->getActiveSheet();

                                                                            $alpha = 'A';
                                                                            $index = 1;
                                                                            foreach ($heads as $key) {
                                                                                $sheet->setCellValue($alpha . $index, $key);
                                                                                $alpha++;
                                                                            }
                                                                            $index = 2;


                                                                            $QGood = new Application_Model_Good();
                                                                            $QGoodColor = new Application_Model_GoodColor();
                                                                            $QMarket = new Application_Model_Market();
                                                                            $QDistributor = new Application_Model_Distributor();
                                                                            $QGoodCategory = new Application_Model_GoodCategory();
                                                                            $QWarehouse = new Application_Model_Warehouse();

                                                                            $goods = $QGood->get_cache();
                                                                            $goodColors = $QGoodColor->get_cache();
                                                                            $distributors = $QDistributor->get_cache();
        /*$good_categories = $QGoodCategory->get_cache();
        $warehouses_cached = $QWarehouse->get_cache();*/

        $i = 1;
        $markets = array();

        foreach ($data as $key => $m) {
            $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
            $markets[$m['sn']] = $QMarket->fetchAll($where);
        }

        foreach ($data as $item) {
            $alpha = 'A';
            $sheet->setCellValue($alpha++ . $index, $i++);
            $sheet->getCell($alpha++ . $index)->setValueExplicit($item['sn'],
                PHPExcel_Cell_DataType::TYPE_STRING);

            if (isset($distributors) && isset($distributors[$item['d_id']]))
                $distributor = $distributors[$item['d_id']];
            else
                $distributor = '';

            //check payment
            isset($item['pay_time']) ? $pay = 'v' : $pay = 'X';

            //check shipping
            if ($item['shipping_yes_time'])
                $shipping = 'v';
            else
                $shipping = 'X';
            //check out_warehouse
            isset($item['outmysql_time']) ? $out = 'v' : $out = 'X';

            if ($item['status'] == 1)
                $status = 'v';
            else
                $status = 'X';

            $sheet->setCellValue($alpha++ . $index, $distributor);
            $sheet->setCellValue($alpha++ . $index, $item['price_1']);
            $sheet->setCellValue($alpha++ . $index, $item['price_2']);
            $sheet->setCellValue($alpha++ . $index, $item['total_qty']);
            $sheet->setCellValue($alpha++ . $index, $item['price_4']);
            $sheet->setCellValue($alpha++ . $index, $item['total_price']);
            $sheet->setCellValue($alpha++ . $index, $pay);
            $sheet->setCellValue($alpha++ . $index, $shipping);

            $sheet->setCellValue($alpha++ . $index, $item['add_time']);
            $index++;

            foreach ($markets[$item['sn']] as $key => $value) {
                $alpha = 'A';
                $sheet->setCellValue($alpha++ . $index, $i++);

                if (isset($goods) && isset($goods[$value['good_id']]))
                    $good_name = $goods[$value['good_id']];
                if (isset($goodColors) && isset($goodColors[$value['good_color']]))
                    $good_color = $goodColors[$value['good_color']];

                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, $good_name);
                $sheet->setCellValue($alpha++ . $index, $good_color);
                $sheet->setCellValue($alpha++ . $index, $value['num']);
                $sheet->setCellValue($alpha++ . $index, $value['price']);
                $sheet->setCellValue($alpha++ . $index, $value['total']);
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $index++;
            }
        }

        $filename = 'List_Sales_Order_' . date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');

        exit;
    }

    private function _exportExcelPaymentGroup($data)
    {
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'SALE ORDER NUMBER',
            'PAYMENT NUMBER',
            'DISTRIBUTOR NAME',
            'TOTAL AMOUNT',
            'WAREHOUSE',
            'SALES CONFIRM DATE');

        $PHPExcel->setActiveSheetIndex(0);
        $sheet = $PHPExcel->getActiveSheet();

        $alpha = 'A';
        $index = 1;
        foreach ($heads as $key) {
            $sheet->setCellValue($alpha . $index, $key);
            $alpha++;
        }
        $index = 2;


        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QMarket = new Application_Model_Market();
        $QDistributor = new Application_Model_Distributor();
        $QGoodCategory = new Application_Model_GoodCategory();
        $QWarehouse = new Application_Model_Warehouse();

        // $goods = $QGood->get_cache();
        // $goodColors = $QGoodColor->get_cache();
        $distributors = $QDistributor->get_cache();
        // $good_categories = $QGoodCategory->get_cache();
        $warehouses_cached = $QWarehouse->get_cache();

        $i = 1;

        foreach ($data as $item) {
            $alpha = 'A';

            $sn_ref = '';

            if(isset($item['pay_group']) and $item['pay_group'] == 1){$sn_ref = 'M'; 
        }else{ 
            $sn_ref = $item['sn_ref']; 
        }

            // $sheet->setCellValue($alpha++ . $index, $i++);
        $sheet->getCell($alpha++ . $index)->setValueExplicit($sn_ref,
            PHPExcel_Cell_DataType::TYPE_STRING);

        $sheet->getCell($alpha++ . $index)->setValueExplicit($item['payment_no'],
            PHPExcel_Cell_DataType::TYPE_STRING);

        if (isset($distributors) && isset($distributors[$item['d_id']]))
            $distributor = $distributors[$item['d_id']];
        else
            $distributor = '';

        $sheet->setCellValue($alpha++ . $index, $distributor);
        $sheet->setCellValue($alpha++ . $index, $item['total_price']);
        $sheet->setCellValue($alpha++ . $index, $warehouses_cached[$item['warehouse_id']]);
        $sheet->setCellValue($alpha++ . $index, $item['sales_confirm_date']);
        $index++;
    }

    $filename = 'List_Sales_Order_PaymentGroup_' . date('d/m/Y');
    $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

    $objWriter->save('php://output');

    exit;
}

public function returnListAction()
{
        //auto refresh
    $this->view->meta_refresh = 30;

    $sort = $this->getRequest()->getParam('sort', 'p.id');
    $desc = $this->getRequest()->getParam('desc', 1);
    $page = $this->getRequest()->getParam('page', 1);

    $sn = $this->getRequest()->getParam('sn');
    $d_id = $this->getRequest()->getParam('d_id');
    $good_id = $this->getRequest()->getParam('good_id');
    $good_color = $this->getRequest()->getParam('good_color');
    $num = $this->getRequest()->getParam('num');
    $price = $this->getRequest()->getParam('price');
    $created_at_to = $this->getRequest()->getParam('created_at_to');
    $created_at_from = $this->getRequest()->getParam('created_at_from');
    $cat_id = $this->getRequest()->getParam('cat_id');

    $export = $this->getRequest()->getParam('export', 0);

    $rank           = $this->getRequest()->getParam('rank');
    $this->view->rank = $rank;
    $this->view->d_id = $d_id;

    $invoice_number    = $this->getRequest()->getParam('invoice_number');
    $finance_group    = $this->getRequest()->getParam('finance_group');

    $QDistributor = new Application_Model_Distributor();
    $this->view->finance_group = $QDistributor->getFinanceGroup();

    $limit = LIMITATION;
    $total = 0;

    $params = array_filter(array(
        'sn' => $sn,
        'd_id' => $d_id,
        'good_id' => $good_id,
        'good_color' => $good_color,
        'num' => $num,
        'price' => $price,
        'total' => $total,
        'created_at_to' => $created_at_to,
        'created_at_from' => $created_at_from,
        'cat_id' => $cat_id,
        'rank' => $rank,
        'invoice_number' => $invoice_number,
        'finance_group' => $finance_group
    ));

    $params['isbacks'] = true;
    $params['finance_return'] = true;
    $params['group_sn'] = 1;


    $QGood = new Application_Model_Good();
    $QGoodColor = new Application_Model_GoodColor();
    $QMarket = new Application_Model_Market();
    $QDistributor = new Application_Model_Distributor();
    $QGoodCategory = new Application_Model_GoodCategory();

    $goods = $QGood->get_cache();
    $goodColors = $QGoodColor->get_cache();
    $distributors = $QDistributor->get_cache();
    $good_categories = $QGoodCategory->get_cache();

    $params['sort'] = $sort;
    $params['desc'] = $desc;

    if (isset($export) && $export) {
        $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
        $this->_exportReturnSaleExcel($markets_sn);
    } else {

        $markets_sn = $QMarket->fetchPagination($page, $limit, $total, $params);
    }
    $markets = array();

    foreach ($markets_sn as $key => $m) {
        $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
        $markets[$m['sn']] = $QMarket->fetchAll($where);
    }

    $this->view->goods = $goods;
    $this->view->goodColors = $goodColors;
    $this->view->markets = $markets;
    $this->view->markets_sn = $markets_sn;
    $this->view->distributors = $distributors;
    $this->view->good_categories = $good_categories;

    unset($params['isbacks']);
    unset($params['group_sn']);
    unset($params['finance_return']);

    $this->view->desc = $desc;
    $this->view->sort = $sort;
    $this->view->params = $params;
    $this->view->limit = $limit;
    $this->view->total = $total;
    $this->view->url = HOST . 'finance/return-list/' . ($params ? '?' .
        http_build_query($params) . '&' : '?');

    $this->view->offset = $limit * ($page - 1);

    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;

    $messages_success = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages_success = $messages_success;

    if ($this->getRequest()->isXmlHttpRequest()) {
        $this->_helper->layout->disableLayout();

        $this->_helper->viewRenderer->setRender('partials/return-list');
    }
}

private function _exportReturnSaleExcel($data)
{
    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();
    $heads = array(
        'No.',
        'SALE ORDER NUMBER',
        'DISTRIBUTOR NAME',
        'PRODUCT NAME',
        'PRODUCT COLOR',
        'SALES QUANTITY',
        'SALES PRICE',
        'TOTAL',
        'ORDER TIME',
        'FINANCE GROUP');

    $PHPExcel->setActiveSheetIndex(0);
    $sheet = $PHPExcel->getActiveSheet();

    $alpha = 'A';
    $index = 1;
    foreach ($heads as $key) {
        $sheet->setCellValue($alpha . $index, $key);
        $alpha++;
    }
    $index = 2;


    $QGood = new Application_Model_Good();
    $QGoodColor = new Application_Model_GoodColor();
    $QMarket = new Application_Model_Market();
    $QDistributor = new Application_Model_Distributor();
    $QGoodCategory = new Application_Model_GoodCategory();
    $QWarehouse = new Application_Model_Warehouse();

    $goods = $QGood->get_cache();
    $goodColors = $QGoodColor->get_cache();
    $distributors = $QDistributor->get_cache();
        /*$good_categories = $QGoodCategory->get_cache();
        $warehouses_cached = $QWarehouse->get_cache();*/


        $i = 1;
        $markets = array();
        foreach ($data as $key => $m) {
            $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
            $markets[$m['sn']] = $QMarket->fetchAll($where);
        }
        foreach ($data as $item) {
            $alpha = 'A';
            $sheet->setCellValue($alpha++ . $index, $i++);
            $sheet->getCell($alpha++ . $index)->setValueExplicit($item['sn'],
                PHPExcel_Cell_DataType::TYPE_STRING);

            if (isset($distributors) && isset($distributors[$item['d_id']]))
                $distributor = $distributors[$item['d_id']];
            else
                $distributor = '';
            //check payment
            isset($item['pay_time']) ? $pay = 'v' : $pay = 'X';

            //check shipping
            if ($item['shipping_yes_time'])
                $shipping = 'v';
            else
                $shipping = 'X';

            //check out_warehouse
            isset($item['outmysql_time']) ? $out = 'v' : $out = 'X';

            if ($item['status'] == 1)
                $status = 'v';
            else
                $status = 'X';

            $sheet->setCellValue($alpha++ . $index, $distributor);
            $sheet->setCellValue($alpha++ . $index, $item['price_1']);
            $sheet->setCellValue($alpha++ . $index, $item['price_2']);
            $sheet->setCellValue($alpha++ . $index, $item['total_qty']);
            $sheet->setCellValue($alpha++ . $index, $item['price_4']);
            $sheet->setCellValue($alpha++ . $index, $item['total_price']);
            $sheet->setCellValue($alpha++ . $index, $item['add_time']);
            $sheet->setCellValue($alpha++ . $index, $item['finance_group']);

            $index++;


            foreach ($markets[$item['sn']] as $key => $value) {
                $alpha = 'A';
                $sheet->setCellValue($alpha++ . $index, $i++);

                if (isset($goods) && isset($goods[$value['good_id']]))
                    $good_name = $goods[$value['good_id']];
                if (isset($goodColors) && isset($goodColors[$value['good_color']]))
                    $good_color = $goodColors[$value['good_color']];

                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, $good_name);
                $sheet->setCellValue($alpha++ . $index, $good_color);
                $sheet->setCellValue($alpha++ . $index, $value['num']);
                $sheet->setCellValue($alpha++ . $index, $value['price']);
                $sheet->setCellValue($alpha++ . $index, $value['total']);
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $index++;

            }

        }

        $filename = 'List_Return_Order_' . date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');
        exit;
    }


    function product_price($priceFloat)
    {
        $symbol = ' THB';
        $symbol_thousand = ',';
        $decimal_place = 2;
        $price = number_format($priceFloat, $decimal_place, '.', $symbol_thousand);
        return $price;
    }

    private function _exportExcelListPaymentNo($data)
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'payment_no_list_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'DISTRIBUTOR NAME',
            'TOTAL AMOUNT',
            'TOTAL USED',
            'TOTAl BALANCE'

        );

        fputcsv($output, $heads);

        $i = 2;
        foreach($data as $item)

        {

            $row = array();
            $row[] = $item['title'];
            $row[] = $item['total_amount'];
            $row[] = $item['use_total'];
            $row[] = $item['balance_total'];

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    private function _exportExcelPaymentNoLog($data)
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'payment_no_log_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'PAYMENT NO',
            'TO PAYMENT NO',
            'TOTAL USED',
            'CREATE DATE',
            'LOGTYPE',
            'REMARK'
        );

        fputcsv($output, $heads);

        $i = 2;
        foreach($data as $item)

        {

            $row = array();
            $row[] = $item['from_payment_no'];
            $row[] = $item['to_payment_no'];
            $row[] = $item['use_total'];
            $row[] = $item['create_date'];

            $pay_type = '';

            switch ($item['pay_type']) {
                case '0':
                $pay_type = 'System';
                break;
                case '1':
                $pay_type = 'Manual';
                break;
                default:
                echo '-';
                break;
            }

            $row[] = $pay_type;

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    private function _exportExcelCreditNote($data)
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Credit Note For Return Order '.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'DISTRIBUTOR CODE',
            'DISTRIBUTOR NAME',
           // 'TYPE DISCOUNT',
           // 'CREDIT NOTE NO',
            'DISCOUNT TOTAL',
            'BALANCE TOTAL',
            'CREATEDATE',

        );
        fputcsv($output, $heads);



        $i = 2;
        foreach($data as $item)

        {

            $row = array();
            $row[] = $item['store_code'];
            $row[] = $item['title'];
           // $row[] = '';
           // $row[] = '';
            $row[] = $item['total_amount'];
            $row[] = $item['balance_total'];
            $row[] = $item['create_date'];


            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    private function _exportExcelCreditNoteList($data)
    {
        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Credit Note For Return Order'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'CREATEDATE',
            'CREDIT NOTE NO',
            'DISTRIBUTOR TYPE',
            'DISTRIBUTOR CODE',
            'DISTRIBUTOR NAME',
            'TAX NO',
            'AREA_NAME',
            'PROVINCE',
            'DISTRICT',
            'CHANEL',
            'TYPE DISCOUNT',
            'STATUS',
            'TOTAL',
            'VAT PRICE',
            'TOTAL PRICE',
            'W/T',
            'NET PRICE',
            'SUB TOTAL',
            'BALANCE TOTAL',
            'FINANCE GROUP',
            'CONFIRM DATE',
            'MANUAL',
            'TOTAL PRICE OLD',
            'MANUAL REMARK',
        );
        
        if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID,CHECK_MONEY))) {
            $heads[] = 'FINANCE REMARK';
        }
        fputcsv($output, $heads);



        $i = 2;
        $vat = 0;$sp_chanel=0;
        //print_r($data);die;
        foreach($data as $item)
        {

            $total_amount = $item['total_amount'];
            $sp_total_amount = $item['sp_total_amount'];
            $creditnote_type_sn = $item['creditnote_type'];
            $creditnote_chanel_sn = $item['chanel'];

            if($creditnote_chanel_sn=='promotion'){
                $creditnote_chanel='ສົ່ງເສີມການຂາຍ';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='reward'){
                $creditnote_chanel='OPPO Club';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='incentive'){
                $creditnote_chanel='ຄ່າ Incentive';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='decoration'){
                $creditnote_chanel='ຄ່າຕົບແຕ່ງໜ້າຮ້ານ';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);  
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='price'){
                $creditnote_chanel='ແກ້ໄຂລາຄາ';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1; 
            }else if($creditnote_chanel_sn=='live_demo'){
                $creditnote_chanel='Live Demo';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1; 
            }else if($creditnote_chanel_sn=='return'){
                $creditnote_chanel='Return Product';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($total_amount-$b,2);
                $sp_chanel=0;             
            }else if($creditnote_chanel_sn=='oppo_all_green'){
                $creditnote_chanel='OPPO ALL GREEN';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat = 0;
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='top_green'){
                $creditnote_chanel='OPPO TOP GREEN';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat = 0;
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='service'){
                $creditnote_chanel='ຄ່າບໍລິການ';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='cn_service'){
                $creditnote_chanel='CN Service';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);  
                $sp_chanel=1; 
            }else if($creditnote_chanel_sn=='oppo_sis'){
                $creditnote_chanel='ສົ່ງເສີມການຂາຍ OPPO SIS';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;          
            }else if($creditnote_chanel_sn=='accessories'){
                $creditnote_chanel='Price Protection';
                $creditnote_type='CP';
                $total = number_format($sp_total_amount / 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;     
            }else if($creditnote_chanel_sn=='rent'){
                $creditnote_chanel='ຄ່າເຊົ່າ';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;
            }else{
                if($creditnote_type_sn=='CN')
                {
                    if($creditnote_chanel_sn=='oppo_all_green'){
                        $creditnote_chanel='oppo_all_green';
                        $creditnote_type='CN';
                        $total = number_format($sp_total_amount / 1,2);
                        $b = str_replace( ',', '', $total );
                        $vat =   number_format($sp_total_amount-$b,2);
                        $sp_chanel=1;
                    }else if($creditnote_chanel_sn=='service'){
                        $creditnote_chanel='ຄ່າບໍລິການ';
                        $creditnote_type='CN';
                        $total = number_format($sp_total_amount / 1,2);
                        $b = str_replace( ',', '', $total );
                        $vat =   number_format($sp_total_amount-$b,2);
                        $sp_chanel=1;
                    }else{
                        $creditnote_chanel='Return Product';
                        $creditnote_type='CN';
                        $total = number_format($total_amount/1 ,2);
                        $b = str_replace( ',', '', $total );
                        $vat =   number_format($total_amount-$b,2);
                        $sp_chanel=0;
                    }
                }else{
                    $creditnote_chanel='Price Protection';
                    $creditnote_type='CP';

                    $total = number_format($sp_total_amount / 1,2);
                    $b = str_replace( ',', '', $total );
                    $vat =   number_format($sp_total_amount-$b,2);
                    $sp_chanel=1;
                }
            }


            $parent_key = $item['parent'];

            $tax_no = '="'.$item['tax_no'].'"';
            $area_name = '="'.$item['area_name'].'"';
            $province = '="'.$item['province'].'"';
            $district = '="'.$item['district'].'"';

            if($parent_key=='0'){
                $distributor_type="ສຳນັກງານໃຫຍ່";
            }else{
                $distributor_type="ສາຂາ";
            }

            $status = '';
            switch ($item['status']) {
                case '1':
                $status = 'ເປີດໃຊ້ງານ';
                break;
                case '0':
                $status = 'ປິດໃຊ້ງານ';
                break;
                default : 
                $status = 'ປິດໃຊ້ງານ';
                break;    
            }

            $price_ext_vat=0;
            $total_price=0;
            $wht_price=0;

            $row = array();
            $row[] = $item['create_date'];
            $row[] = $item['creditnote_sn'];
            $row[] = $distributor_type;
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $tax_no;
            $row[] = $area_name;
            $row[] = $province;
            $row[] = $district;
            $row[] = $creditnote_chanel;
            $row[] = $creditnote_type;
            $row[] = $status;
            
            if($sp_chanel==1){
                if($item['creditnote_type'] == 'CP'){
                    $row[] = '-'.$total;
                    $row[] = '-'.$vat; 
                    $row[] = '-'.number_format($item['sp_total_amount'],2);
                    $row[] = $wht_price;
                    $row[] = '-'.number_format($item['sp_total_amount'],2);
                    $row[] = '-'.number_format($item['sp_use_total'],2);
                    $row[] = '-'.number_format($item['sp_balance_total'],2);
                }else{

                    $price_ext_vat = $item['price_ext_vat'];
                    switch($item['vat']) {
                        case '0':
                        $vat_cal = 0;
                        break;
                        case '7':
                        $vat_cal = 0.07;
                        break;
                        default:
                        $vat_cal = 0;
                    }

                    $total_price_vat = ($price_ext_vat * $vat_cal);

                    $price_in_vat = $price_ext_vat+($price_ext_vat * $vat_cal);

                    $row[] = '-'.number_format($price_ext_vat,2);
                    $row[] = '-'.number_format($total_price_vat,2); 
                    $row[] = '-'.number_format($price_in_vat,2);
                    $row[] = number_format($item['wht_price'],2);
                    $row[] = '-'.number_format($item['sp_total_amount'],2);
                    $row[] = '-'.number_format($item['sp_use_total'],2);
                    $row[] = '-'.number_format($item['sp_balance_total'],2);
                    /*$row[] = '-'.$total;
                    $row[] = '-'.$vat; 
                    $row[] = '-'.$total_price;
                    $row[] = $wht_price;
                    $row[] = '-'.number_format($item['sp_total_amount'],2);
                    $row[] = '-'.number_format($item['sp_use_total'],2);
                    $row[] = '-'.number_format($item['sp_balance_total'],2);*/
                }
            }else{
                if($item['total_amount_old'] <= 0){
                    $row[] = number_format(0,2);
                    $row[] = number_format(0,2);
                    $row[] = number_format(0,2);
                    $row[] = number_format(0,2);
                    $row[] = number_format(0,2);
                }else{
                   $row[] = '-'.$total;
                   $row[] = '-'.$vat; 
                   $row[] = '-'.number_format($item['total_amount'],2);
                   $row[] = number_format($wht_price,2);
                   $row[] = '-'.number_format($item['total_amount'],2); 
               }
               $row[] = $item['use_total']== 0 ?'0' : '-'.number_format($item['use_total'],2);
               $row[] = '-'.number_format($item['balance_total'],2);
           }

           $row[] = $item['finance_group'];
           $row[] = $item['confirm_date'];
           $row[] = $item['manual'];
           $row[] = '-'.number_format($item['total_amount_old'],2);
           $row[] = $item['manual_remark'];

           if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID,CHECK_MONEY))) {
            $row[] = $item['finance_remark'];
        }

        fputcsv($output, $row);
        unset($item);
        unset($row);

    }


    unset($data);
    unset($result);

    fclose($output);

    ob_flush();
    ob_start();
    while (@ob_end_flush());

    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;

    $file = fopen($path, 'r');
    $content = fread($file, filesize($path));
    var_dump(filesize($path));
    var_dump($content);

    exit;
}

private function _exportExcelCreditNoteListNumberPlus($data)
{
        //print_r($data);die;
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', '200M');
    $filename = 'Credit Note For Return Order'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);
    $path = $file_path.'/'.$filename;
    $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'CREATEDATE',
            'CREDIT NOTE NO',
            'DISTRIBUTOR TYPE',
            'DISTRIBUTOR CODE',
            'DISTRIBUTOR NAME',
            'TAX NO',
            'AREA_NAME',
            'PROVINCE',
            'DISTRICT',
            'CHANEL',
            'TYPE DISCOUNT',
            'STATUS',
            'TOTAL',
            'VAT PRICE',
            'TOTAL PRICE',
            'W/T',
            'NET PRICE',
            'SUB TOTAL',
            'BALANCE TOTAL',
            'FINANCE GROUP',
            'CONFIRM DATE',
            'MANUAL',
            'TOTAL PRICE OLD',
            'MANUAL REMARK',
        );
        fputcsv($output, $heads);



        $i = 2;
        $vat = 0;$sp_chanel=0;
        //print_r($data);die;
        foreach($data as $item)
        {

            $total_amount = $item['total_amount'];
            $sp_total_amount = $item['sp_total_amount'];
            $creditnote_type_sn = $item['creditnote_type'];
            $creditnote_chanel_sn = $item['chanel'];

            if($creditnote_chanel_sn=='promotion'){
                $creditnote_chanel='ส่งเสริมการขาย';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='reward'){
                $creditnote_chanel='OPPO Club';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='incentive'){
                $creditnote_chanel='ค่า Incentive';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='decoration'){
                $creditnote_chanel='ค่าตกแต่งหน้าร้าน';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);  
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='price'){
                $creditnote_chanel='แก้ไขราคา';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1; 
            }else if($creditnote_chanel_sn=='live_demo'){
                $creditnote_chanel='Live Demo';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1; 
            }else if($creditnote_chanel_sn=='return'){
                $creditnote_chanel='Return Product';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($total_amount-$b,2);
                $sp_chanel=0;             
            }else if($creditnote_chanel_sn=='oppo_all_green'){
                $creditnote_chanel='OPPO ALL GREEN';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat = 0;
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='top_green'){
                $creditnote_chanel='OPPO TOP GREEN';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat = 0;
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='service'){
                $creditnote_chanel='ค่าบริการ';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='cn_service'){
                $creditnote_chanel='CN Service';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);  
                $sp_chanel=1; 
            }else if($creditnote_chanel_sn=='oppo_sis'){
                $creditnote_chanel='ส่งเสริมการขาย OPPO SIS';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;          
            }else if($creditnote_chanel_sn=='accessories'){
                $creditnote_chanel='Price Protection';
                $creditnote_type='CP';
                $total = number_format($sp_total_amount / 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;     
            }else if($creditnote_chanel_sn=='rent'){
                $creditnote_chanel='ค่าเช่า';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;
            }else{
                if($creditnote_type_sn=='CN')
                {
                    if($creditnote_chanel_sn=='oppo_all_green'){
                        $creditnote_chanel='oppo_all_green';
                        $creditnote_type='CN';
                        $total = number_format($sp_total_amount / 1,2);
                        $b = str_replace( ',', '', $total );
                        $vat =   number_format($sp_total_amount-$b,2);
                        $sp_chanel=1;
                    }else if($creditnote_chanel_sn=='service'){
                        $creditnote_chanel='ค่าบริการ';
                        $creditnote_type='CN';
                        $total = number_format($sp_total_amount / 1,2);
                        $b = str_replace( ',', '', $total );
                        $vat =   number_format($sp_total_amount-$b,2);
                        $sp_chanel=1;
                    }else{
                        $creditnote_chanel='Return Product';
                        $creditnote_type='CN';
                        $total = number_format($total_amount/1 ,2);
                        $b = str_replace( ',', '', $total );
                        $vat =   number_format($total_amount-$b,2);
                        $sp_chanel=0;
                    }
                }else{
                    $creditnote_chanel='Price Protection';
                    $creditnote_type='CP';

                    $total = number_format($sp_total_amount / 1,2);
                    $b = str_replace( ',', '', $total );
                    $vat =   number_format($sp_total_amount-$b,2);
                    $sp_chanel=1;
                }
            }


            $parent_key = $item['parent'];

            $tax_no = '="'.$item['tax_no'].'"';
            $area_name = '="'.$item['area_name'].'"';
            $province = '="'.$item['province'].'"';
            $district = '="'.$item['district'].'"';

            if($parent_key=='0'){
                $distributor_type="สำนักงานใหญ่";
            }else{
                $distributor_type="สาขา";
            }

            $status = '';
            switch ($item['status']) {
                case '1':
                $status = 'เปิดใช้งาน';
                break;
                case '0':
                $status = 'ปิดใช้งาน';
                break;
                default : 
                $status = 'ปิดใช้งาน';
                break;    
            }

            $price_ext_vat=0;
            $total_price=0;
            $wht_price=0;

            $row = array();
            $row[] = $item['create_date'];
            $row[] = $item['creditnote_sn'];
            $row[] = $distributor_type;
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $tax_no;
            $row[] = $area_name;
            $row[] = $province;
            $row[] = $district;
            $row[] = $creditnote_chanel;
            $row[] = $creditnote_type;
            $row[] = $status;
            
            if($sp_chanel==1){
                if($item['creditnote_type'] == 'CP'){
                    $row[] = $total;
                    $row[] = $vat; 
                    $row[] = number_format($item['sp_total_amount'],2);
                    $row[] = $wht_price;
                    $row[] = number_format($item['sp_total_amount'],2);
                    $row[] = number_format($item['sp_use_total'],2);
                    $row[] = number_format($item['sp_balance_total'],2);
                }else{

                    $price_ext_vat = $item['price_ext_vat'];
                    switch($item['vat']) {
                        case '0':
                        $vat_cal = 0;
                        break;
                        case '7':
                        $vat_cal = 0.07;
                        break;
                        default:
                        $vat_cal = 0;
                    }

                    $total_price_vat = ($price_ext_vat * $vat_cal);

                    $price_in_vat = $price_ext_vat+($price_ext_vat * $vat_cal);

                    $row[] = number_format($price_ext_vat,2);
                    $row[] = number_format($total_price_vat,2); 
                    $row[] = number_format($price_in_vat,2);
                    $row[] = number_format($item['wht_price'],2);
                    $row[] = number_format($item['sp_total_amount'],2);
                    $row[] = number_format($item['sp_use_total'],2);
                    $row[] = number_format($item['sp_balance_total'],2);
                    /*$row[] = $total;
                    $row[] = $vat; 
                    $row[] = $total_price;
                    $row[] = $wht_price;
                    $row[] = number_format($item['sp_total_amount'],2);
                    $row[] = number_format($item['sp_use_total'],2);
                    $row[] = number_format($item['sp_balance_total'],2);*/
                }
            }else{
                if($item['total_amount_old'] <= 0){
                    $row[] = number_format(0,2);
                    $row[] = number_format(0,2);
                    $row[] = number_format(0,2);
                    $row[] = number_format(0,2);
                    $row[] = number_format(0,2);
                }else{
                   $row[] = $total;
                   $row[] = $vat; 
                   $row[] = number_format($item['total_amount'],2);
                   $row[] = number_format($wht_price,2);
                   $row[] = number_format($item['total_amount'],2); 
               }
               $row[] = $item['use_total']== 0 ?'0' : number_format($item['use_total'],2);
               $row[] = number_format($item['balance_total'],2);
           }

           $row[] = $item['finance_group'];
           $row[] = $item['confirm_date'];
           $row[] = $item['manual'];
           $row[] = number_format($item['total_amount_old'],2);
           $row[] = $item['manual_remark'];
           fputcsv($output, $row);
           unset($item);
           unset($row);

       }


       unset($data);
       unset($result);

       fclose($output);

       ob_flush();
       ob_start();
       while (@ob_end_flush());

       header('Expires: 0');
       header('Cache-Control: must-revalidate');
       header('Pragma: public');
       header('Content-Length: ' . filesize($path));
       readfile($path);
       exit;

       $file = fopen($path, 'r');
       $content = fread($file, filesize($path));
       var_dump(filesize($path));
       var_dump($content);

       exit;
   }


   private function _exportExcelCreditNoteList_bk($data)
   {
        //print_r($data);die;
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', '200M');
    $filename = 'Credit Note For Return Order'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);
    $path = $file_path.'/'.$filename;
    $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'CREATEDATE',
            'CREDIT NOTE NO',
            'DISTRIBUTOR TYPE',
            'DISTRIBUTOR CODE',
            'DISTRIBUTOR NAME',
            'TAX NO',
            'AREA_NAME',
            'PROVINCE',
            'DISTRICT',
            'CHANEL',
            'TYPE DISCOUNT',
            'STATUS',
            'TOTAL',
            'VAT PRICE',
            'TOTAL PRICE',
            'W/T',
            'NET PRICE',
            'SUB TOTAL',
            'BALANCE TOTAL',
            'FINANCE GROUP',
            'CONFIRM DATE',
            'MANUAL',
            'TOTAL PRICE OLD',
        );
        fputcsv($output, $heads);



        $i = 2;
        $vat = 0;$sp_chanel=0;
        //print_r($data);die;
        foreach($data as $item)
        {

            $total_amount = $item['total_amount'];
            $sp_total_amount = $item['sp_total_amount'];
            $creditnote_type_sn = $item['creditnote_type'];
            $creditnote_chanel_sn = $item['chanel'];
            $manual = $item['manual'];

            $price_ext_vat = $item['price_ext_vat'];
            $vat_per = $item['vat'];
            $wht_per = $item['wht_vat'];
            $wht_price = $item['wht_price'];


            if($price_ext_vat>0){
                $sp_total_amount = $price_ext_vat;

                switch($vat_per) {
                    case '0':
                    $vat_cal = 0;
                    break;
                    case '7':
                    $vat_cal = 0.07;
                    break;
                    default:
                    $vat_cal = 0;
                }

                switch($wht_per) {
                    case '1':
                    $wht_vat_cal = 0.01;
                    break;
                    case '2':
                    $wht_vat_cal = 0.02;
                    break;
                    case '3':
                    $wht_vat_cal = 0.03;
                    break;
                    case '5':
                    $wht_vat_cal = 0.05;
                    break;        
                    default:
                    $wht_vat_cal = 0;
                }


            }

            if($creditnote_chanel_sn=='promotion'){
                $creditnote_chanel='ส่งเสริมการขาย';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='reward'){
                $creditnote_chanel='OPPO Club';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='incentive'){
                $creditnote_chanel='ค่า Incentive';
                $creditnote_type='CN';
                $vat =   number_format($sp_total_amount*$vat_cal,2);
                $total_in_vat = $vat+$sp_total_amount;
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='decoration'){
                $creditnote_chanel='ค่าตกแต่งหน้าร้าน';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);  
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='price'){
                $creditnote_chanel='แก้ไขราคา';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1; 
            }else if($creditnote_chanel_sn=='live_demo'){
                $creditnote_chanel='Live Demo';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1; 
            }else if($creditnote_chanel_sn=='return'){
                $creditnote_chanel='Return Product';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($total_amount-$b,2);
                $sp_chanel=0;             
            }else if($creditnote_chanel_sn=='oppo_all_green'){
                $creditnote_chanel='OPPO ALL GREEN';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat = 0;
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='top_green'){
                $creditnote_chanel='OPPO TOP GREEN';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat = 0;
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='service'){
                $creditnote_chanel='ค่าบริการ';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;
            }else if($creditnote_chanel_sn=='cn_service'){
                $creditnote_chanel='CN Service';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);  
                $sp_chanel=1; 
            }else if($creditnote_chanel_sn=='oppo_sis'){
                $creditnote_chanel='ส่งเสริมการขาย OPPO SIS';
                $creditnote_type='CN';
                $total = number_format($sp_total_amount/ 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;          
            }else if($creditnote_chanel_sn=='accessories'){
                $creditnote_chanel='Price Protection';
                $creditnote_type='CP';
                $total = number_format($sp_total_amount / 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($sp_total_amount-$b,2);
                $sp_chanel=1;     
            }else{
                if($creditnote_type_sn=='CN')
                {
                    if($creditnote_chanel_sn=='oppo_all_green'){
                        $creditnote_chanel='oppo_all_green';
                        $creditnote_type='CN';
                        $total = number_format($sp_total_amount / 1,2);
                        $b = str_replace( ',', '', $total );
                        $vat =   number_format($sp_total_amount-$b,2);
                        $sp_chanel=1;
                    }else if($creditnote_chanel_sn=='service'){
                        $creditnote_chanel='ค่าบริการ';
                        $creditnote_type='CN';
                        $total = number_format($sp_total_amount / 1,2);
                        $b = str_replace( ',', '', $total );
                        $vat =   number_format($sp_total_amount-$b,2);
                        $sp_chanel=1;
                    }
                    else{
                        $creditnote_chanel='Return Product';
                        $creditnote_type='CN';
                        $total = number_format($total_amount / 1,2);
                        $b = str_replace( ',', '', $total );
                        $vat =   number_format($total_amount-$b,2);
                        $sp_chanel=0;
                    }
                }else{
                    $creditnote_chanel='Price Protection';
                    $creditnote_type='CP';

                    $total = number_format($sp_total_amount / 1,2);
                    $b = str_replace( ',', '', $total );
                    $vat =   number_format($sp_total_amount-$b,2);
                    $sp_chanel=1;
                }
            }


            $parent_key = $item['parent'];

            $tax_no = '="'.$item['tax_no'].'"';
            $area_name = '="'.$item['area_name'].'"';
            $province = '="'.$item['province'].'"';
            $district = '="'.$item['district'].'"';

            if($parent_key=='0'){
                $distributor_type="สำนักงานใหญ่";
            }else{
                $distributor_type="สาขา";
            }

            $status = '';
            switch ($item['status']) {
                case '1':
                $status = 'เปิดใช้งาน';
                break;
                case '0':
                $status = 'ปิดใช้งาน';
                break;
                default : 
                $status = 'ปิดใช้งาน';
                break;    
            }

            $row = array();
            $row[] = $item['create_date'];
            $row[] = $item['creditnote_sn'];
            $row[] = $distributor_type;
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $tax_no;
            $row[] = $area_name;
            $row[] = $province;
            $row[] = $district;
            $row[] = $creditnote_chanel;
            $row[] = $creditnote_type;
            $row[] = $status;
            
            if($sp_chanel==1){
                $row[] = '-'.$sp_total_amount;
                $row[] = '-'.$vat; 
                $row[] = '-'.number_format($total_in_vat,2);

                $wht_price = number_format($item['wht_price'],2);
                $net_price = $total_in_vat-$wht_price; 

                $row[] = $wht_price; 
                $row[] = $net_price;
                $row[] = '-'.number_format($item['sp_use_total'],2);
                $row[] = '-'.number_format($item['sp_balance_total'],2);
            }else{
                if($item['total_amount_old'] <= 0){
                    $row[] = number_format(0,2);
                    $row[] = number_format(0,2);
                    $row[] = number_format(0,2);
                    $row[] = number_format(0,2);
                    $row[] = number_format(0,2);
                }else{
                   $row[] = '-'.$sp_total_amount;
                   $row[] = '-'.$vat; 
                   $row[] = '-'.number_format($item['total_amount'],2); 

                   $wht_price = number_format($item['wht_price'],2);
                   $net_price = $item['total_amount']-$wht_price; 

                   $row[] = $wht_price; 
                   $row[] = $net_price; 
               }
               $row[] = $item['use_total']== 0 ?'0' : '-'.number_format($item['use_total'],2);
               $row[] = '-'.number_format($item['balance_total'],2);
           }
           $row[] = $item['finance_group'];
           $row[] = $item['confirm_date'];
           $row[] = $item['manual'];
           $row[] = '-'.number_format($item['total_amount_old'],2);
           fputcsv($output, $row);
           unset($item);
           unset($row);

       }


       unset($data);
       unset($result);

       fclose($output);

       ob_flush();
       ob_start();
       while (@ob_end_flush());

       header('Expires: 0');
       header('Cache-Control: must-revalidate');
       header('Pragma: public');
       header('Content-Length: ' . filesize($path));
       readfile($path);
       exit;

       $file = fopen($path, 'r');
       $content = fread($file, filesize($path));
       var_dump(filesize($path));
       var_dump($content);

       exit;
   }

   private function _exportExcelCreditNoteNew($data)
   {

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', '200M');
    $filename = 'Credit Note Report_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);
    $path = $file_path.'/'.$filename;
    $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $now_y = date('y');

        $heads = array(
            'No.',
            'CREATEDATE',
            'CREDIT NOTE NO',
            'DISTRIBUTOR CODE',
            'DISTRIBUTOR NAME',
            'CHANEL',
            'RETURN TYPE',
            'TYPE DISCOUNT',
            'ISSUED BALANCE',
            'ISSUED PERIOD',
            'USED BALANCE
            (Cash collection report)
            '.$now_y.'01',
            'USED BALANCE
            (Cash collection report)
            '.$now_y.'02',
            'USED BALANCE
            (Cash collection report)
            '.$now_y.'03',
            'USED BALANCE
            (Cash collection report)
            '.$now_y.'04',
            'USED BALANCE
            (Cash collection report)
            '.$now_y.'05',
            'USED BALANCE
            (Cash collection report)
            '.$now_y.'06',
            'USED BALANCE
            (Cash collection report)
            '.$now_y.'07',
            'USED BALANCE
            (Cash collection report)
            '.$now_y.'08',
            'USED BALANCE
            (Cash collection report)
            '.$now_y.'09',
            'USED BALANCE
            (Cash collection report)
            '.$now_y.'10',
            'USED BALANCE
            (Cash collection report)
            '.$now_y.'11',
            'USED BALANCE
            (Cash collection report)
            '.$now_y.'12',
            'CASH PAID',
            'CASH PAID PERIOD',
            'NO NEED TO PAID DUE TO CONTRA WITH SALES INVOICE',
            'CONTRA PERIOD',
            'CANCELLED',
            'CANCELLED PERIOD',
            'OUTSTANDING
            BALANCE',
            'REMARK'
        );
        
        fputcsv($output, $heads);

        $i = 0;
        foreach($data as $item)
        {
            $i++;
            $creditnote_type_sn = $item['creditnote_type'];
            $creditnote_chanel_sn = $item['chanel'];

            if($creditnote_chanel_sn=='reward'){
                $creditnote_chanel='ส่งเสริมการขาย';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='incentive'){
                $creditnote_chanel='ค่า Incentive';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='decoration'){
                $creditnote_chanel='ค่าตกแต่งหน้าร้าน';
                $creditnote_type='CN'; 
            }else if($creditnote_chanel_sn=='price'){
                $creditnote_chanel='แก้ไขราคา';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='live_demo'){
                $creditnote_chanel='Live Demo';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='return'){
                $creditnote_chanel='Return Product';
                $creditnote_type='CN';           
            }else if($creditnote_chanel_sn=='oppo_all_green'){
                $creditnote_chanel='OPPO ALL GREEN';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='top_green'){
                $creditnote_chanel='OPPO TOP GREEN';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='service'){
                $creditnote_chanel='ค่าบริการ';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='cn_service'){
                $creditnote_chanel='CN Service';
                $creditnote_type='CN'; 
            }else if($creditnote_chanel_sn=='oppo_sis'){
                $creditnote_chanel='ส่งเสริมการขาย OPPO SIS';
                $creditnote_type='CN';       
            }else if($creditnote_chanel_sn=='accessories'){
                $creditnote_chanel='Price Protection';
                $creditnote_type='CP'; 
            }else{
                if($creditnote_type_sn=='CN')
                {
                    if($creditnote_chanel_sn=='oppo_all_green'){
                        $creditnote_chanel='oppo_all_green';
                        $creditnote_type='CN';
                    }else if($creditnote_chanel_sn=='service'){
                        $creditnote_chanel='ค่าบริการ';
                        $creditnote_type='CN';
                    }
                    else{
                        $creditnote_chanel='Return Product';
                        $creditnote_type='CN';
                    }
                }else{
                    $creditnote_chanel='Price Protection';
                    $creditnote_type='CP';
                }
            }

            $row = array();

            $row[] = $i;
            $row[] = $item['create_date'];
            $row[] = $item['creditnote_sn'];
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $creditnote_chanel;
            $row[] = $item['cn_type'];
            $row[] = $creditnote_type;
            $row[] = number_format($item['total_amount'],2);
            $row[] = date("ymd", strtotime($item['create_date']));
            $row[] = number_format($item['1'],2);
            $row[] = number_format($item['2'],2);
            $row[] = number_format($item['3'],2);
            $row[] = number_format($item['4'],2);
            $row[] = number_format($item['5'],2);
            $row[] = number_format($item['6'],2);
            $row[] = number_format($item['7'],2);
            $row[] = number_format($item['8'],2);
            $row[] = number_format($item['9'],2);
            $row[] = number_format($item['10'],2);
            $row[] = number_format($item['11'],2);
            $row[] = number_format($item['12'],2);
            $row[] = '';
            $row[] = '';
            $row[] = '';
            $row[] = '';
            $row[] = '';
            $row[] = '';
            $row[] = number_format($item['balance_total'],2);
            $row[] = $item['remark'];

            fputcsv($output, $row);
            unset($item);
            unset($row);

        }


        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    private function _exportExcelCPByImei($data)
    {
        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Credit Note CP By Imei_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));;

        $heads = array(
            'Distributor',
            'Distributor Code',
            'Fianance Group',
            'Area Name',
            'Province',
            'District',
            'Product Name',
            'Product Color',
            'Invoice No',
            'Imei sn',
            'CN',
            'STATUS',
            'CN Type',
            'Unit Price',
            'Total Amount',
            'Use Total',
            'Balance Total',
            'Remark',
        );
        
        fputcsv($output, $heads);

        $old_data = '';
        $counter = 0;

        foreach($data as $item)
        {
            if($old_data == $item['creditnote_sn']){
                $counter++;
            }else{
                $counter = 0;
            }

            $old_data = $item['creditnote_sn'];

            $creditnote_type_sn = $item['creditnote_type'];
            $creditnote_chanel_sn = $item['chanel'];
            if($creditnote_chanel_sn=='reward'){
                $creditnote_chanel='ส่งเสริมการขาย';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='incentive'){
                $creditnote_chanel='ค่า Incentive';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='decoration'){
                $creditnote_chanel='ค่าตกแต่งหน้าร้าน';
                $creditnote_type='CN'; 
            }else if($creditnote_chanel_sn=='price'){
                $creditnote_chanel='แก้ไขราคา';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='live_demo'){
                $creditnote_chanel='Live Demo';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='return'){
                $creditnote_chanel='Return Product';
                $creditnote_type='CN';           
            }else if($creditnote_chanel_sn=='oppo_all_green'){
                $creditnote_chanel='OPPO ALL GREEN';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='top_green'){
                $creditnote_chanel='OPPO TOP GREEN';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='service'){
                $creditnote_chanel='ค่าบริการ';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='cn_service'){
                $creditnote_chanel='CN Service';
                $creditnote_type='CN'; 
            }else if($creditnote_chanel_sn=='oppo_sis'){
                $creditnote_chanel='ส่งเสริมการขาย OPPO SIS';
                $creditnote_type='CN';       
            }else if($creditnote_chanel_sn=='accessories'){
                $creditnote_chanel='Price Protection';
                $creditnote_type='CP'; 
            }else{
                if($creditnote_type_sn=='CN')
                {
                    if($creditnote_chanel_sn=='oppo_all_green'){
                        $creditnote_chanel='oppo_all_green';
                        $creditnote_type='CN';
                    }else if($creditnote_chanel_sn=='service'){
                        $creditnote_chanel='ค่าบริการ';
                        $creditnote_type='CN';
                    }
                    else{
                        $creditnote_chanel='Return Product';
                        $creditnote_type='CN';
                    }
                }else{
                    $creditnote_chanel='Price Protection';
                    $creditnote_type='CP';
                }
            }


            $return_type = '';
            switch ($item['cn_type']) {
                case '1':
                $return_type = 'Defective';
                break;
                case '2':
                $return_type = 'Adjustment';
                break;
                case '3':
                $return_type = 'Demo';
                break;
            }

            $status = '';
            switch ($item['status']) {
                case '1':
                $status = 'เปิดใช้งาน';
                break;
                case '0':
                $status = 'ปิดใช้งาน';
                break;
            }

            $row = array();

            $row[] = $item['title'];
            $row[] = $item['store_code'];
            $row[] = $item['finance_group'];
            $row[] = $item['area_name'];
            $row[] = $item['province'];
            $row[] = $item['district'];
            $row[] = $item['good_name'];
            $row[] = $item['color'];
            $row[] = $item['invoice_number'];
            $row[] = $item['imei_sn'];
            $row[] = $item['creditnote_sn'];
            $row[] = $status;
            $row[] = $creditnote_type;
            $row[] = number_format($item['price'],2);
            if($counter == 0){
                $row[] = number_format($item['total_amount'],2);
                $row[] = number_format($item['use_total'],2);
                $row[] = number_format($item['balance_total'],2);
            }else{
                $row[] = '';
                $row[] = '';
                $row[] = '';
            }
            
            $row[] = $item['remark'];
            

            fputcsv($output, $row);
            unset($item);
            unset($row);

        }

        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    private function _exportExcelCNByImei($data)
    {
        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Credit Note By Imei_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));;

        $heads = array(
            'Distributor',
            'Distributor Code',
            'Fianance Group',
            'Area Name',
            'Province',
            'District',
            'Product Name',
            'Product Color',
            'Invoice No',
            'Imei sn',
            'CN',
            'STATUS',
            'CN Type',
            'Chanel',
            'Return Type',
            'Count Total Price Protection',
            'Total Price Protection',
            'Unit Price Before Price Protection',
            'Unit Price',
            'Total Amount',
            'Use Total',
            'Balance Total',
            'Remark',
        );
        
        fputcsv($output, $heads);

        $old_data = '';
        $counter = 0;

        foreach($data as $item)
        {
            if($old_data == $item['creditnote_sn']){
                $counter++;
            }else{
                $counter = 0;
            }

            $old_data = $item['creditnote_sn'];

            $creditnote_type_sn = $item['creditnote_type'];
            $creditnote_chanel_sn = $item['chanel'];
            if($creditnote_chanel_sn=='reward'){
                $creditnote_chanel='ส่งเสริมการขาย';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='incentive'){
                $creditnote_chanel='ค่า Incentive';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='decoration'){
                $creditnote_chanel='ค่าตกแต่งหน้าร้าน';
                $creditnote_type='CN'; 
            }else if($creditnote_chanel_sn=='price'){
                $creditnote_chanel='แก้ไขราคา';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='live_demo'){
                $creditnote_chanel='Live Demo';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='return'){
                $creditnote_chanel='Return Product';
                $creditnote_type='CN';           
            }else if($creditnote_chanel_sn=='oppo_all_green'){
                $creditnote_chanel='OPPO ALL GREEN';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='top_green'){
                $creditnote_chanel='OPPO TOP GREEN';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='service'){
                $creditnote_chanel='ค่าบริการ';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='cn_service'){
                $creditnote_chanel='CN Service';
                $creditnote_type='CN'; 
            }else if($creditnote_chanel_sn=='oppo_sis'){
                $creditnote_chanel='ส่งเสริมการขาย OPPO SIS';
                $creditnote_type='CN';       
            }else if($creditnote_chanel_sn=='accessories'){
                $creditnote_chanel='Price Protection';
                $creditnote_type='CP'; 
            }else{
                if($creditnote_type_sn=='CN')
                {
                    if($creditnote_chanel_sn=='oppo_all_green'){
                        $creditnote_chanel='oppo_all_green';
                        $creditnote_type='CN';
                    }else if($creditnote_chanel_sn=='service'){
                        $creditnote_chanel='ค่าบริการ';
                        $creditnote_type='CN';
                    }
                    else{
                        $creditnote_chanel='Return Product';
                        $creditnote_type='CN';
                    }
                }else{
                    $creditnote_chanel='Price Protection';
                    $creditnote_type='CP';
                }
            }


            $return_type = '';
            switch ($item['cn_type']) {
                case '1':
                $return_type = 'Defective';
                break;
                case '2':
                $return_type = 'Adjustment';
                break;
                case '3':
                $return_type = 'Demo';
                break;
            }

            $status = '';
            switch ($item['status']) {
                case '1':
                $status = 'เปิดใช้งาน';
                break;
                case '0':
                $status = 'ปิดใช้งาน';
                break;
            }

            $row = array();

            $row[] = $item['title'];
            $row[] = $item['store_code'];
            $row[] = $item['finance_group'];
            $row[] = $item['area_name'];
            $row[] = $item['province'];
            $row[] = $item['district'];
            $row[] = $item['good_name'];
            $row[] = $item['color'];
            $row[] = $item['invoice_number'];
            $row[] = $item['imei_sn'];
            $row[] = $item['creditnote_sn'];
            $row[] = $status;
            $row[] = $creditnote_type;
            $row[] = $creditnote_chanel;
            $row[] = $return_type;
            $row[] = $item['count_bvg_price'];
            $row[] = $item['sum_bvg_price'];
            $row[] = number_format($item['unit_price1'],2);
            $row[] = number_format($item['unit_price'],2);
            if($counter == 0){
                $row[] = number_format($item['total_amount'],2);
                $row[] = number_format($item['use_total'],2);
                $row[] = number_format($item['balance_total'],2);
            }else{
                $row[] = '';
                $row[] = '';
                $row[] = '';
            }
            
            $row[] = $item['remark'];
            

            fputcsv($output, $row);
            unset($item);
            unset($row);

        }

        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }


    private function _exportExcelCNByModel($data)
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Credit Note By Model_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));;

        $heads = array(
            'Distributor',
            'Distributor Code',
            'Fianance Group',
            'Area Name',
            'Province',
            'District',
            'SN',
            'RO',
            'Invoice',
            'CN',
            'CN Type',
            'Chanel',
            'Return Type',
            'Total Amount',
            'Use Total',
            'Balance Total',
            'Remark',
            'Product Name',
            'Product Color',
            'Total'
        );
        
        fputcsv($output, $heads);

        $old_data = '';
        $counter = 0;

        foreach($data as $item)
        {
            if($old_data == $item['creditnote_sn']){
                $counter++;
            }else{
                $counter = 0;
            }

            $old_data = $item['creditnote_sn'];

            $creditnote_type_sn = $item['creditnote_type'];
            $creditnote_chanel_sn = $item['chanel'];
            if($creditnote_chanel_sn=='reward'){
                $creditnote_chanel='ส่งเสริมการขาย';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='incentive'){
                $creditnote_chanel='ค่า Incentive';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='decoration'){
                $creditnote_chanel='ค่าตกแต่งหน้าร้าน';
                $creditnote_type='CN'; 
            }else if($creditnote_chanel_sn=='price'){
                $creditnote_chanel='แก้ไขราคา';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='live_demo'){
                $creditnote_chanel='Live Demo';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='return'){
                $creditnote_chanel='Return Product';
                $creditnote_type='CN';           
            }else if($creditnote_chanel_sn=='oppo_all_green'){
                $creditnote_chanel='OPPO ALL GREEN';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='top_green'){
                $creditnote_chanel='OPPO TOP GREEN';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='service'){
                $creditnote_chanel='ค่าบริการ';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='cn_service'){
                $creditnote_chanel='CN Service';
                $creditnote_type='CN'; 
            }else if($creditnote_chanel_sn=='oppo_sis'){
                $creditnote_chanel='ส่งเสริมการขาย OPPO SIS';
                $creditnote_type='CN';       
            }else if($creditnote_chanel_sn=='accessories'){
                $creditnote_chanel='Price Protection';
                $creditnote_type='CP'; 
            }else{
                if($creditnote_type_sn=='CN')
                {
                    if($creditnote_chanel_sn=='oppo_all_green'){
                        $creditnote_chanel='oppo_all_green';
                        $creditnote_type='CN';
                    }else if($creditnote_chanel_sn=='service'){
                        $creditnote_chanel='ค่าบริการ';
                        $creditnote_type='CN';
                    }
                    else{
                        $creditnote_chanel='Return Product';
                        $creditnote_type='CN';
                    }
                }else{
                    $creditnote_chanel='Price Protection';
                    $creditnote_type='CP';
                }
            }


            $return_type = '';
            switch ($item['cn_type']) {
                case '1':
                $return_type = 'Defective';
                break;
                case '2':
                $return_type = 'Adjustment';
                break;
                case '3':
                $return_type = 'Demo';
                break;
            }

            $row = array();

            $row[] = $item['title'];
            $row[] = $item['store_code'];
            $row[] = $item['finance_group'];
            $row[] = $item['area_name'];
            $row[] = $item['province'];
            $row[] = $item['district'];
            $row[] = $item['sn'];
            $row[] = $item['sn_ref'];
            $row[] = $item['invoice_number'];
            $row[] = $item['creditnote_sn'];
            $row[] = $creditnote_type;
            $row[] = $creditnote_chanel;
            $row[] = $return_type;

            if($counter == 0){
                $row[] = number_format($item['total_amount'],2);
                $row[] = number_format($item['use_total'],2);
                $row[] = number_format($item['balance_total'],2);
            }else{
                $row[] = '';
                $row[] = '';
                $row[] = '';
            }
            
            $row[] = $item['remark'];
            $row[] = $item['good_name'];
            $row[] = $item['color'];
            $row[] = $item['num'];

            fputcsv($output, $row);
            unset($item);
            unset($row);

        }

        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    private function _exportExcelCreditNoteInvoice($data)
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Credit Note Invoice '.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'CREATEDATE',
            'CREDIT NOTE NO',
            'INVOICE NUMBER',
            'DISTRIBUTOR TYPE',
            'DISTRIBUTOR CODE',
            'DISTRIBUTOR NAME',
            'TAX NO',
            'AREA_NAME',
            'PROVINCE',
            'DISTRICT',
            'CHANEL',
            'TYPE DISCOUNT',
            'TOTAL',
            'VAT',
            'TOTAL PRICE',
            'SUB TOTAL',
            'BALANCE TOTAL',
            'FINANCE GROUP',
            'RETURN TYPE'

        );
        fputcsv($output, $heads);



        $i = 2;
        $vat = 0;
        //print_r($data);die;
        foreach($data as $item)
        {

            $total_amount = $item['total_amount'];
            $creditnote_type_sn = $item['creditnote_type'];
            $creditnote_chanel_sn = $item['chanel'];

            if($creditnote_chanel_sn=='reward'){
                $creditnote_chanel='ส่งเสริมการขาย';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($total_amount-$b,2);
            }else if($creditnote_chanel_sn=='incentive'){
                $creditnote_chanel='ค่า Incentive';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($total_amount-$b,2);
            }else if($creditnote_chanel_sn=='decoration'){
                $creditnote_chanel='ค่าตกแต่งหน้าร้าน';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($total_amount-$b,2);  
            }else if($creditnote_chanel_sn=='price'){
                $creditnote_chanel='แก้ไขราคา';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($total_amount-$b,2); 
            }else if($creditnote_chanel_sn=='live_demo'){
                $creditnote_chanel='Live Demo';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($total_amount-$b,2); 
            }else if($creditnote_chanel_sn=='return'){
                $creditnote_chanel='Return Product';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($total_amount-$b,2);             
            }else if($creditnote_chanel_sn=='oppo_all_green'){
                $creditnote_chanel='OPPO ALL GREEN';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat = 0;
            }else if($creditnote_chanel_sn=='top_green'){
                $creditnote_chanel='OPPO TOP GREEN';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat = 0;
            }else if($creditnote_chanel_sn=='service'){
                $creditnote_chanel='ค่าบริการ';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($total_amount-$b,2);
            }else if($creditnote_chanel_sn=='cn_service'){
                $creditnote_chanel='CN Service';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($total_amount-$b,2);   
            }else if($creditnote_chanel_sn=='oppo_sis'){
                $creditnote_chanel='ส่งเสริมการขาย OPPO SIS';
                $creditnote_type='CN';
                $total = number_format($total_amount,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($total_amount-$b,2);          
            }else if($creditnote_chanel_sn=='accessories'){
                $creditnote_chanel='Price Protection';
                $creditnote_type='CP';
                $total = number_format($total_amount / 1,2);
                $b = str_replace( ',', '', $total );
                $vat =   number_format($total_amount-$b,2);     
            }else{
                if($creditnote_type_sn=='CN')
                {
                    if($creditnote_chanel_sn=='oppo_all_green'){
                        $creditnote_chanel='oppo_all_green';
                        $creditnote_type='CN';
                        $total = number_format($total_amount / 1,2);
                        $b = str_replace( ',', '', $total );
                        $vat =   number_format($total_amount-$b,2);
                    }else if($creditnote_chanel_sn=='service'){
                        $creditnote_chanel='ค่าบริการ';
                        $creditnote_type='CN';
                        $total = number_format($total_amount / 1,2);
                        $b = str_replace( ',', '', $total );
                        $vat =   number_format($total_amount-$b,2);
                    }
                    else{
                        $creditnote_chanel='Return Product';
                        $creditnote_type='CN';
                        $total = number_format($total_amount / 1,2);
                        $b = str_replace( ',', '', $total );
                        $vat =   number_format($total_amount-$b,2);
                    }
                }else{
                    $creditnote_chanel='Price Protection';
                    $creditnote_type='CP';

                    $total = number_format($total_amount / 1,2);
                    $b = str_replace( ',', '', $total );
                    $vat =   number_format($total_amount-$b,2);

                }
            }

            $return_type = '';

            switch ($item['cn_type']) {
                case '1':
                $return_type = 'Defective';
                break;
                case '2':
                $return_type = 'Adjustment';
                break;
                case '3':
                $return_type = 'Demo';
                break;
            }

            $parent_key = $item['parent'];

            $tax_no = '="'.$item['tax_no'].'"';
            $area_name = '="'.$item['area_name'].'"';
            $province = '="'.$item['province'].'"';
            $district = '="'.$item['district'].'"';

            if($parent_key=='0'){
                $distributor_type="สำนักงานใหญ่";
            }else{
                $distributor_type="สาขา";
            }

            $row = array();
            $row[] = $item['create_date'];
            $row[] = $item['creditnote_sn'];
            $row[] = $item['invoice_number'];
            $row[] = $distributor_type;
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $tax_no;
            $row[] = $area_name;
            $row[] = $province;
            $row[] = $district;
            $row[] = $creditnote_chanel;
            $row[] = $creditnote_type;
            $row[] = '-'.$total;
            $row[] = '-'.$vat;
            $row[] = '-'.number_format($item['total_amount'],2);
            $row[] = $item['use_total']== 0 ?'0' : '-'.number_format($item['use_total'],2);
            $row[] = '-'.number_format($item['balance_total'],2);
            $row[] = $item['finance_group'];
            $row[] = $return_type;

            fputcsv($output, $row);
            unset($item);
            unset($row);

        }


        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    private function _exportExcelUseCN($data)
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Use_CN'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'sn',
            'sn_ref',
            'invoice_number',
            'pay_time',
            'creditnote_sn',
            'use_discount',
            'area_name',
            'province',
            'district',
            'd_id',
            'store_code',
            'title',
            'tax_code',
            'tel',
            'finance_group',



        );
        fputcsv($output, $heads);



        $i = 2;
        $vat = 0;
        //print_r($data);die;
        foreach($data as $item)
        {



            $row = array();
            $row[] = "'".$item['sn']."'";
            $row[] = $item['sn_ref'];
            $row[] = $item['invoice_number'];
            $row[] = $item['pay_time'];
            $row[] = $item['creditnote_sn'];
            $row[] = $item['use_discount'];
            $row[] = $item['area_name'];
            $row[] = $item['province'];
            $row[] = $item['district'];
            $row[] = $item['D_id'];
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $item['tax_code'];
            $row[] = $item['tel'];
            $row[] = $item['finance_group'];

            fputcsv($output, $row);
            unset($item);
            unset($row);

        }


        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    public function distributorPoAction(){
        $flashMessenger               = $this->_helper->flashMessenger;
        //distributor po
        $po_name = $this->getRequest()->getParam('po_name');
        $d_id    = $this->getRequest()->getParam('d_id');
        $status  = $this->getRequest()->getParam('status');
        $from    = $this->getRequest()->getParam('from');
        $to      = $this->getRequest()->getParam('to');
        $id      = $this->getRequest()->getParam('id');
        $page    = $this->getRequest()->getParam('page',1);
        $sort    = $this->getRequest()->getParam('sort','created_at');
        $desc    = $this->getRequest()->getParam('desc', 1);
        $total   = 0;
        $limit   = LIMITATION;
        $params = array(
            'id'      => $id,
            'po_name' => trim($po_name),
            'd_id'    => intval($d_id),
            'status'  => intval($status),
            'from'    => $from,
            'to'      => $to,
            'sort'    => $sort,
            'desc'    => $desc
        );

        $QDistributorPo     = new Application_Model_DistributorPo();
        $list               = $QDistributorPo->fetchPagination($page,$limit,$total,$params);
        $this->view->list   = $list;
        $this->view->sort   = $sort;
        $this->view->desc   = $desc;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'finance/distributor-po/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->offset = $limit*($page-1);

        $QDistributor = new Application_Model_Distributor();
        $QArea        = new Application_Model_Area();
        $distributors = $QDistributor->get_cache();
        $areas        = $QArea->get_cache();
        $this->view->areas        = $areas;
        $this->view->distributors = $distributors;


        $messages                     = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages         = $messages;
        $messages_success             = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;
    }

    public function orderByPoAction(){
        $flashMessenger    = $this->_helper->flashMessenger;
        $po_id             = $this->getRequest()->getParam('po_id');
        $sort              = $this->getRequest()->getParam('sort', 'p.id');
        $desc              = $this->getRequest()->getParam('desc', 1);
        $page              = $this->getRequest()->getParam('page', 1);
        $sn                = $this->getRequest()->getParam('sn');
        $d_id              = $this->getRequest()->getParam('d_id');
        $good_id           = $this->getRequest()->getParam('good_id');
        $good_color        = $this->getRequest()->getParam('good_color');
        $num               = $this->getRequest()->getParam('num');
        $price             = $this->getRequest()->getParam('price');
        $pay_time          = $this->getRequest()->getParam('payment', 0);
        $outmysql_time     = $this->getRequest()->getParam('outmysql_time', 0);
        $created_at_to     = $this->getRequest()->getParam('created_at_to');
        $created_at_from   = $this->getRequest()->getParam('created_at_from');
        $invoice_time_from = $this->getRequest()->getParam('invoice_time_from');
        $invoice_time_to   = $this->getRequest()->getParam('invoice_time_to');
        $cat_id            = $this->getRequest()->getParam('cat_id');
        $warehouse_id      = $this->getRequest()->getParam('warehouse_id');
        $type              = $this->getRequest()->getParam('type');
        $text              = $this->getRequest()->getParam('text');
        $warehouse_id      = $this->getRequest()->getParam('warehouse_id');
        $status            = $this->getRequest()->getParam('status', 1);
        $tags              = $this->getRequest()->getParam('tags');
        $invoice_number    = $this->getRequest()->getParam('invoice_number');
        $user_id           = $this->getRequest()->getParam('user_id');
        $area_id           = $this->getRequest()->getParam('area_id');
        $region_id         = $this->getRequest()->getParam('region_id');

        if ($tags and is_array($tags))
            $tags = $tags;
        else
            $tags = null;

        $limit = LIMITATION;
        $total = 0;

        $params = array_filter( array(
            'po_id'             => $po_id,
            'sn'                => $sn,
            'd_id'              => $d_id,
            'good_id'           => $good_id,
            'good_color'        => $good_color,
            'num'               => $num,
            'price'             => $price,
            'total'             => $total,
            'cat_id'            => $cat_id,
            'warehouse_id'      => $warehouse_id,
            'status'            => $status,
            'text'              => $text,
            'type'              => $type,
            'tags'              => $tags,
            'invoice_time_from' => $invoice_time_from,
            'invoice_time_to'   => $invoice_time_to,
            'invoice_number'    => $invoice_number,
            'user_id'           => $user_id,
            'area_id'           => $area_id,
            'region_id'         => $region_id,
        ));
        $params['created_at_from']  = $created_at_from;
        $params['created_at_to']    = $created_at_to;

        $params['isbacks'] = false;
        $params['group_sn'] = true;

        if ($pay_time)
            $params['payment'] = true;

        if ($outmysql_time)
            $params['outmysql_time'] = true;


        $QGood          = new Application_Model_Good();
        $QGoodColor     = new Application_Model_GoodColor();
        $QMarket        = new Application_Model_Market();
        $QDistributor   = new Application_Model_Distributor();
        $QGoodCategory  = new Application_Model_GoodCategory();
        $QWarehouse     = new Application_Model_Warehouse();
        $QStaff         = new Application_Model_Staff();
        $QArea          = new Application_Model_Area();
        $QRegion        = new Application_Model_Region();
        $QDistributorPo = new Application_Model_DistributorPo();


        $goods             = $QGood->get_cache();
        $goodColors        = $QGoodColor->get_cache();
        $distributors      = $QDistributor->get_cache();
        $distributors2     = $QDistributor->get_cache2();
        $good_categories   = $QGoodCategory->get_cache();
        $warehouses_cached = $QWarehouse->get_cache();
        $staffs_cached     = $QStaff->get_cache();

        $params['sort'] = $sort;
        $params['desc'] = $desc;
        $params['get_fields'] = array(
            'sn',
            'd_id',
            'pay_time',
            'shipping_yes_time',
            'outmysql_time',
            'warehouse_id',
            'status',
            'add_time',
            'canceled',
        );

        if(!$po_id){
            $flashMessenger->setNamespace('error')->addMessage('Please select PO name!');
            $this->_redirect('/finance/distributor-po');
        }

        $currentPo = $QDistributorPo->find($po_id)->current();

        if(!$currentPo){
            $flashMessenger->setNamespace('error')->addMessage('Please select PO name!');
            $this->_redirect('/finance/distributor-po');
        }
        $this->view->currentPo = $currentPo;
        $markets_sn = $QMarket->fetchPagination($page, NULL, $total, $params);


        unset($params['get_fields']);
        unset($params['isbacks']);
        unset($params['group_sn']);

        $this->view->areas = $QArea->get_cache();

        if ($area_id) {
            $where = $QRegion->getAdapter()->quoteInto('area_id = ?', $area_id);
            $regions = $QRegion->fetchAll($where, 'name');

            $regions_arr = array();

            foreach ($regions as $key => $value)
                $regions_arr[$value['id']] = $value['name'];

            $this->view->regions = $regions_arr;
        }

        $this->view->po_id           = $po_id;
        $this->view->goods           = $goods;
        $this->view->goodColors      = $goodColors;
        $this->view->markets_sn      = $markets_sn;
        $this->view->distributors    = $distributors;
        $this->view->distributors2   = $distributors2;
        $this->view->good_categories = $good_categories;
        $this->view->warehouses_cached = $warehouses_cached;
        $this->view->staffs_cached   = $staffs_cached;

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'finance/order-by-po/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->offset = $limit*($page-1);


        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

    }

    public function saveDistributorPoAction(){
        set_time_limit( 0 );
        ini_set('memory_limit', -1);
        if($this->getRequest()->isPost()){

            $flashMessenger = $this->_helper->flashMessenger;
            $db             = Zend_Registry::get('db');
            $QMarket        = new Application_Model_Market();
            $Qcheckmoney    = new Application_Model_Checkmoney();
            $QStoreAccount  = new Application_Model_Storeaccount();
            $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
            $id             = $this->getRequest()->getParam('po_id');//po_id
            $sns            = $this->getRequest()->getParam('sns');
            $pay_text       = $this->getRequest()->getParam('pay_text','');
            $shipping_text  = $this->getRequest()->getParam('shipping_text','');

            if(!$id){
                $flashMessenger->setNamespace('error')->addMessage('Please select Po to check payment!');
                $this->_redirect('/finance/distributor-po');
            }

            if(!is_array($sns)){
                $flashMessenger->setNamespace('error')->addMessage('Please select order number to check payment!');
                $this->_redirect('/finance/order-by-po?po_id='.$id);
            }

            if(!count($sns)){
                $flashMessenger->setNamespace('error')->addMessage('Please select order number to check payment!');
                $this->_redirect('/finance/order-by-po?po_id='.$id);
            }

            $arrUpdateBalance = array();
            $db->beginTransaction();
            try{

                foreach($sns as $sn){
                    $selectDis = $QMarket->select()
                    ->where('sn = ?',$sn)
                    ->where('status = ?',1);
                    $rowDistributor  = $QMarket->fetchRow($selectDis);
                    $d_id = $rowDistributor->d_id;

                    if(!in_array($d_id,$arrUpdateBalance) ){
                      $arrUpdateBalance[]  = $d_id;
                  }

                  $selectSn = $db->select()
                  ->from(array('m'=>'market'),array('m.sn','m.pay_time','m.shipping_yes_time','m.pay_user',
                    'm.shipping_yes_id','m.po_id','m.outmysql_time','m.warehouse_id'))
                  ->join(array('w'=>'warehouse'),'m.warehouse_id = w.id',array('w.company_id'))
                  ->where('sn = ?',$sn)
                  ->where('status = ?',1);
                  $rows           = $db->fetchAll($selectSn);

                  $QMarketProduct = new Application_Model_MarketProduct();
                  $intRebate      = intval($QMarketProduct->getPrice($sn));
                  $sn_total       = $QMarket->getPrice($sn) - $intRebate;

                  if(!count($rows)){
                    $flashMessenger->setNamespace('error')->addMessage('Error: '.$sn .' is not exist!');
                    $this->_redirect('/finance/order-by-po?po_id=',$id);
                }

                if($rows[0]['pay_user'] AND $rows[0]['shipping_yes_id'] ){
                    continue;
                }

                foreach($rows as $row){

                    if($row['pay_user'] AND $row['shipping_yes_id']){

                        $flashMessenger->setNamespace('error')->addMessage('Error: '. $sn .' has checked  payment, please review!');
                        $this->_redirect('/finance/order-by-po?po_id='.$id);
                    }

                    if($row['po_id'] != $id){
                        $flashMessenger->setNamespace('error')->addMessage('Error: '.$sn.' do not belong this PO');
                        $this->_redirect('/finance/order-by-po?po_id='.$id);
                    }

                    if($row['outmysql_time']){
                        $flashMessenger->setNamespace('error')->addMessage('Error: '. $sn .' was  out warehouse!');
                        $this->_redirect('/finance/order-by-po?po_id='.$id);
                    }
                }


                $date = date('Y-m-d H:i:s');

                    //update market
                $data = array(
                    'pay_text'          => 'via po',
                    'shipping_text'     => $shipping_text,
                    'pay_time'          => $date,
                    'pay_user'          => $userStorage->id,
                    'shipping_yes_time' => $date,
                    'shipping_yes_id'   => $userStorage->id,

                );
                $where = $QMarket->getAdapter()->quoteInto('sn = ?',$sn);
                $QMarket->update($data, $where);
                    //Tanong
                    //update checkmoney transaction
                $company_id = $rows[0]['company_id'];
                $transaction = array(
                    'd_id'      => $d_id,
                    'payment'   => $date,
                    'pay_time'  => $date,
                    'output'    => $sn_total,
                    'pay_money' => -$sn_total,
                    'type'      => 2,
                    'sn'        => $sn,
                    'user_id'   => $userStorage->id,
                    'create_by' => $userStorage->id,
                    'create_at' => $date,
                    'note'      => 'via po',
                    'company_id' => $company_id
                );

                    // kiem tra sn da co trong checkmoney?
                $selectSnTransaction = $Qcheckmoney->select()->where('sn = ?',$sn);
                $checkSnTransaction  = $Qcheckmoney->fetchRow($selectSnTransaction);
                if($checkSnTransaction){
                    $flashMessenger->setNamespace('error')->addMessage('Error: transaction sn '.$sn .' is existed');
                    $this->_redirect('/finance/order-by-po?po_id='.$id);
                }
                $Qcheckmoney->insert($transaction);

                    //todo log
                $ip = $this->getRequest()->getServer('REMOTE_ADDR');
                $info = 'Verify: Sale order number: '.$sn;
                $QLog = new Application_Model_Log();
                $QLog->insert( array (
                    'info'              => $info,
                    'user_id'           => $userStorage->id,
                    'ip_address'        => $ip,
                    'time'              => $date,
                ) );

                }//End foreach;

                //update Balance
                foreach ($arrUpdateBalance as $value) {
                    $QStoreAccount->updateBalance($value);
                }

                $db->commit();
            }catch (Exception $e){
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage('error: '.$e->getMessage());
                $this->_redirect('/finance/order-by-po?po_id=',$id);
            }

            $flashMessenger->setNamespace('success')->addMessage('success!');
            $this->_redirect('/finance/order-by-po?po_id='.$id);
        }//End if check post
    }

    public function updateStockTetAction(){
        /**
         * @for admin: update stock Tet transaction for checkmoney
         */
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        set_time_limit( 0 );
        ini_set('memory_limit', -1);
        $db = Zend_Registry::get('db');
        $QCheckMoney    = new Application_Model_Checkmoney();
        $QStoreAccount  = new Application_Model_Storeaccount();
        $selectSns = $db->select()
        ->from(array('t'=>'tag_object'),array('t.*'))
        ->join(array('m'=>'market'),'t.object_id = m.sn',array())
        ->where('t.tag_id = ?',3447)
            //->where('t.object_id = ?','201503181534463335')
        ->where('m.add_time >= ?','2015-02-14')
        ->where('m.add_time <= ?','2015-02-14 23:59:59')
        ->group('t.object_id')
        ;

        $tags           = $db->fetchAll($selectSns);
        $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
        $date           = date('Y-m-d H:i:s');
        $intCount = 0;
        $db->beginTransaction();
        try{
            if($tags){
                foreach($tags as $tag){
                    $sn     = $tag['object_id'];
                    $select = $db->select()
                    ->from(array('m'=>'market'),array('sn_total'=>'SUM(m.total)','d_id'))
                    ->where('m.sn = ?',$sn)
                    ->where('m.add_time >= ?','2015-02-14')
                    ->where('m.add_time <= ?','2015-02-14 23:59:59')
                    ->group('m.sn')
                    ;

                    $row = $db->fetchRow($select);
                    if(!$row){
                        if($sn = '201503090803183610'){
                            //truong hop nay khong co don hang
                            continue;
                        }
                        exit('Error: '.$sn.' không tồn tại');
                    }
                    $selectSnCheckMoney = $db->select()
                    ->from(array('c'=>'checkmoney'),array('c.sn'))
                    ->where('c.sn = ?',$sn);
                    $checkSnCheckMoney = $db->fetchRow($selectSnCheckMoney);
                    if($checkSnCheckMoney){
                        exit('Error: '.$sn.' tồn tại đã tồn tại trong checkmoney');
                    }

                    $data = array(
                        'd_id'      => $row['d_id'],
                        'payment'   => $date,
                        'pay_time'  => $date,
                        'output'    => $row['sn_total'],
                        'pay_money' => -$row['sn_total'],
                        'type'      => 2,
                        'sn'        => $sn,
                        'user_id'   => $userStorage->id,
                        'create_by' => $userStorage->id,
                        'create_at' => $date,
                        'note'      => 'Payment Order Auto-generate;stock Tet'
                    );

                    $QCheckMoney->insert($data);
                    $QStoreAccount->updateBalance($row['d_id']);
                    $intCount+=1;//dat sai cho :(
                }

                $db->commit();
            }
        }catch (Exception $e){
            $db->rollback();
            exit('Error: '.$e->getMessage());
        }
        echo "Update ". $intCount." sn Success!";
    }

    //Tanong Get Data Credit Note 20160310
    public function start_discount_credit_note_sn($distributor_id,$user_id,$creditnote_sn)
    {
        try {
            $flashMessenger = $this->_helper->flashMessenger;
            $db = Zend_Registry::get('db');

                //$db->beginTransaction();

            $stmt = $db->prepare("CALL start_discount_credit_note_sn('".$distributor_id."','".$user_id."','".$creditnote_sn."')");

            $stmt->execute();
            $credit_data = $stmt->fetchAll();
            $total_amount= $credit_data[0]['total_amount'];

               // $db->commit();
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Create Credit Note, please try again!');
        }

        return $creditnote_sn;
    }

    //Tanong Get Data Credit Note 20160310
    public function update_credit_note_sn($distributor_id,$user_id,$creditnote_sn)
    {
        $total_amount=0;
        try {
            $flashMessenger = $this->_helper->flashMessenger;
            $db = Zend_Registry::get('db');

                //$db->beginTransaction();
            $user_discount=0;$sales_order_sn='';
            $stmt = $db->prepare("CALL update_credit_note_sn('".$distributor_id."','".$_creditnote_sn."','".$user_discount."','".$sales_order_sn."','".$_user_id."')");

            $stmt->execute();
               // $credit_data = $stmt->fetchAll();
                //$total_amount= $credit_data[0]['total_amount'];

               // $db->commit();
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Create Credit Note, please try again!');
        }

        return $total_amount;
    }


    //Tanong Get SalesOrderNoRef 20160313 1155
    //`gen_credit_note_sn_new_return`(_distributor_id INT,_user_id INT,_sn VARCHAR(20))
    public function get_credit_note_sn($db,$distributor_id,$user_id,$sn,$status,$return_type)
    {

        $flashMessenger = $this->_helper->flashMessenger;
        $creditnote_sn="";

        try {
            if($return_type ==''){
                $return_type = 0;
            }
            $stmt = $db->query("CALL gen_credit_note_sn_new_return('".$distributor_id."','".$user_id."',".$sn.",'".$status."','".$return_type."')");
            $result = $stmt->fetch();
            $creditnote_sn = $result['creditnote_sn'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Create Credit Note No, please try again!');
        }
        return $creditnote_sn;


        /*try {
            $flashMessenger = $this->_helper->flashMessenger;
          //  $status= 0;
            if($return_type ==''){
                $return_type = 0;
            }
            $db->query("CALL gen_credit_note_sn_new_return('".$distributor_id."','".$user_id."',".$sn.",'".$status."','".$return_type."')");

        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Create Credit Note No, please try again!');
        }*/
    }

    public function getCreateNoteNo_Ref($db,$distributor_id,$user_id,$sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $creditnote_sn="";
        try {
            $stmt = $db->query("CALL gen_running_no_ref('CN',".$sn.")");
            $stmt->execute();
            $result = $stmt->fetch();
            $creditnote_sn = $result['running_no'];

        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage($e);
        }
        return $creditnote_sn;
    }

    public function getReward_CreateNoteNo_Ref($db,$distributor_id,$user_id,$sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $creditnote_sn="";
        try {
            $stmt = $db->prepare("CALL gen_running_no_ref_reward('CN',".$sn.")");
            $stmt->execute();
            $result = $stmt->fetch();
            $creditnote_sn = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage($e);
        }
        return $creditnote_sn;
    }

    

    function oppoclupRewardUploadCnAction()
    {
        $QJointCircular = new Application_Model_JointCircular();
        $this->view->jointCircular = $QJointCircular->fetchAll();

        $QJointType = new Application_Model_JointType();


        $this->view->joint_type = $QJointType->get_cache();

        $QGood          = new Application_Model_Good();
        $whereGood      = array();
        $whereGood[]    = $QGood->getAdapter()->quoteInto('cat_id = ?' , PHONE_CAT_ID);
        $good = $QGood->fetchAll($whereGood)->toArray();
        $this->view->good = $good;

    }

    function saveOppoClupRewardConfirmAction()
    {
        $this->_helper->layout->disableLayout();
        //print_r($_POST);die;
        if ($this->getRequest()->getMethod() == 'POST') {

            $quater_no_sel       = $this->getRequest()->getParam('quater_no');
            $quater_year_sel     = $this->getRequest()->getParam('quater_year');

            define('MASS_BVG_LIST_ROW_START', 2);
            define('MASS_BVG_LIST_COL_key_sn', 0);
            define('MASS_BVG_LIST_COL_quater_year', 1);
            define('MASS_BVG_LIST_COL_quater_no', 2);
            define('MASS_BVG_LIST_COL_distributor_id', 3);
            define('MASS_BVG_LIST_COL_store_code', 4);
            define('MASS_BVG_LIST_COL_title', 5);
            define('MASS_BVG_LIST_COL_level_name', 6);
            define('MASS_BVG_LIST_COL_total_imei', 7);
            define('MASS_BVG_LIST_COL_creditnote_price', 8);
            define('MASS_BVG_LIST_COL_decorate_status', 9);
            define('MASS_BVG_LIST_COL_creditnote_price_confirm', 10);

            set_time_limit(0);
            ini_set('memory_limit', -1);
            $db = Zend_Registry::get('db');


            $progress = new My_File_Progress('parent.set_progress');
            $progress->flush(0);

            $upload = new Zend_File_Transfer();

            $uniqid = uniqid('', true);
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files'
            . DIRECTORY_SEPARATOR . 'mou'
            . DIRECTORY_SEPARATOR . $userStorage->id
            . DIRECTORY_SEPARATOR . $uniqid;

            if (!is_dir($uploaded_dir))
                @mkdir($uploaded_dir, 0777, true);


            $upload->setDestination($uploaded_dir);

            $upload->setValidators(array(
                'Size' => array('min' => 50, 'max' => 10000000),
                'Count' => array('min' => 1, 'max' => 1),
                'Extension' => array('xlsx', 'xls'),
            ));

            if (!$upload->isValid()) { // validate IF
                $errors = $upload->getErrors();
                $sError = null;

                if ($errors and isset($errors[0]))
                    switch ($errors[0]) {
                        case 'fileUploadErrorIniSize':
                        $sError = 'File size is too large';
                        break;
                        case 'fileMimeTypeFalse':
                        $sError = 'The file you selected weren\'t the type we were expecting';
                        break;
                        case 'fileExtensionFalse':
                        $sError = 'Please choose a file in XLS or XLSX format.';
                        break;
                        case 'fileCountTooFew':
                        $sError = 'Please choose a PO file (in XLS or XLSX format)';
                        break;
                        case 'fileUploadErrorNoFile':
                        $sError = 'Please choose a PO file (in XLS or XLSX format)';
                        break;
                        case 'fileSizeTooBig':
                        $sError = 'File size is too big';
                        break;
                    }

                    $this->view->error = $sError;

                } else {
                    try {

                        $db->beginTransaction();

                        $path_info = pathinfo($upload->getFileName());
                        $filename =  $path_info['filename'];
                        $extension = $path_info['extension'];

                        $old_name = $filename . '.' . $extension;
                        $new_name = 'UPLOAD-' . md5($filename . uniqid('', true)) . '.' . $extension;

                        if (is_file($uploaded_dir . DIRECTORY_SEPARATOR . $old_name)) {
                            rename($uploaded_dir . DIRECTORY_SEPARATOR . $old_name, $uploaded_dir . DIRECTORY_SEPARATOR . $new_name);
                        } else {
                            $new_name = $old_name;
                        }

                        $upload->addFilter('Rename',
                           array('target' => $uploaded_dir. DIRECTORY_SEPARATOR .$new_name,
                             'overwrite' => true));

                        $upload->receive();
                        chmod($uploaded_dir. DIRECTORY_SEPARATOR .$new_name, 777);

                        $QFileLog = new Application_Model_FileUploadLog();
                        $QOppoClubRewardCn = new Application_Model_OppoClubRewardCn();
                        $QOppoClubRewardCnImei = new Application_Model_OppoClubRewardCnImei();

                        $data = array(
                            'staff_id' => $userStorage->id,
                            'folder' => $uniqid,
                            'filename' => $new_name,
                            'type' => 'mass Reward Confirm upload',
                            'real_file_name' => $filename . '.' . $extension,
                            'uploaded_at' => time(),
                        );

                        $log_id = $QFileLog->insert($data);

                        $number_of_order = 0;
                        $error_list = array();
                        $success_list = array();
                        $listBvgByProduct = array();

                        $QImei    = new Application_Model_Imei();

                        require_once 'PHPExcel.php';
                        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                        $cacheSettings = array('memoryCacheSize' => '8MB');
                        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                        switch ($extension) {
                            case 'xls':
                            $objReader = PHPExcel_IOFactory::createReader('Excel5');
                            break;
                            case 'xlsx':
                            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                            break;
                            default:
                            throw new Exception("Invalid file extension");
                            break;
                        }

                        $objReader->setReadDataOnly(true);

                        $objPHPExcel = $objReader->load($uploaded_dir . DIRECTORY_SEPARATOR . $new_name);
                        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

                    $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
                    $total_order_row = $highestRow - MASS_BVG_LIST_ROW_START + 1;

                    $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
                    $status=1; $d_id='';

                    $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
                    $date           = date('Y-m-d H:i:s');

                    for ($i = MASS_BVG_LIST_ROW_START; $i <= $highestRow; $i++) {

                        $key_sn = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_key_sn, $i)
                            ->getValue());

                        $quater_year = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_quater_year, $i)
                            ->getValue());

                        $quater_no = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_quater_no, $i)
                            ->getValue());

                        $distributor_id = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_distributor_id, $i)
                            ->getValue());

                        $store_code = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_store_code, $i)
                            ->getValue());

                        $dealer_name = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_title, $i)
                            ->getValue());

                        $level_name = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_level_name, $i)
                            ->getValue());

                        $total_imei = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_total_imei, $i)
                            ->getValue());

                        $decorate_status = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_decorate_status, $i)
                            ->getValue());

                        $creditnote_price = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_creditnote_price, $i)
                            ->getValue());

                        $creditnote_price_confirm = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_creditnote_price_confirm, $i)
                            ->getValue());

                        $chk_file_reward = $QOppoClubRewardCn->check_file_import_reward_cn_confirm($quater_year_sel,$quater_no_sel);
                        if($chk_file_reward <= 0){
                            $data_error['quater_year'] = $quater_year_sel;
                            $data_error['quater_no'] = $quater_no_sel;
                            $data_error['message'] = "Cannot Import. Quater Year or Quater No Not existed in System";
                            $error_list[] = $data_error;
                            //exit();
                        }else{

                            $chk_reward = $QOppoClubRewardCn->check_reward_cn_confirm($quater_year,$quater_no,$distributor_id,$level_name,$key_sn,$creditnote_price_confirm);
                        //print_r($chk_reward);die;
                            if($chk_reward>0){
                            //print_r($chk_reward);

                               $key_cn = date('YmdHis') . substr(microtime(), 2, 4);
                               $creditnote_sn = $this->getReward_CreateNoteNo_Ref($db,$distributor_id,$userStorage->id,$key_cn);
                               if($creditnote_sn!=''){

                                // --------------Update Confirm------------------
                                $data = array(
                                    'confirm_date' => $date,
                                    'confirm_by' => $userStorage->id,
                                    'status_cn' => 1,
                                    'creditnote_price_confirm' => $creditnote_price_confirm,
                                    'creditnote_sn' => $creditnote_sn,
                                    'key_cn' => $key_cn,
                                );

                                $whereReward = array();
                                $whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('key_sn = ?', $key_sn);
                                $whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_year = ?', $quater_year);
                                $whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_no = ?', $quater_no);
                                $whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);
                                $whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('level_name = ?', $level_name);

                                $QOppoClubRewardCn->update($data, $whereReward);


                                $data = array(
                                    'confirm_date' => $date,
                                    'confirm_by' => $userStorage->id,
                                    'creditnote_sn' => $creditnote_sn,
                                );

                                $whereRewardImei = array();
                                $whereRewardImei[] = $QOppoClubRewardCnImei->getAdapter()->quoteInto('quater_year = ?', $quater_year);
                                $whereRewardImei[] = $QOppoClubRewardCnImei->getAdapter()->quoteInto('quater_no = ?', $quater_no);
                                $whereRewardImei[] = $QOppoClubRewardCnImei->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);

                                $QOppoClubRewardCnImei->update($data, $whereRewardImei);

                                //--------------------------------------

                                $reward="quater_year=".$quater_year.";quater_no=".$quater_no.";level_name=".$level_name;

                                $create_date = date('Y-m-d H:i:s');
                                $data = array(
                                    'distributor_id' => $distributor_id,
                                    'create_by' => $userStorage->id,
                                    'create_date' => $create_date,
                                    'creditnote_type' => 'CN',
                                    'total_amount' => $creditnote_price_confirm,
                                    'use_total' => 0,
                                    'balance_total' => $creditnote_price_confirm,
                                    'status' => 0,
                                    'creditnote_sn' => $creditnote_sn,
                                    'chanel' => 'reward',
                                    'sn' => $key_cn,
                                    'remark' => $reward,
                                );

                                $QCreditNote = new Application_Model_CreditNote();
                                $QCreditNote->insert($data);

                                // ----------------------------

                            }else{
                                $data_error['quater_year'] = $quater_year;
                                $data_error['quater_no'] = $quater_no;
                                $data_error['distributor_id'] = $d_id;
                                $data_error['store_code'] = $store_code;
                                $data_error['dealer_name'] = $dealer_name;
                                $data_error['level_name'] = $level_name;
                                $data_error['creditnote_price'] = $creditnote_price;
                                $data_error['message'] = "Reward Cannot Create Credit Note No.";
                                $error_list[] = $data_error;
                            }
                        }else{
                            $data_error['quater_year'] = $quater_year;
                            $data_error['quater_no'] = $quater_no;
                            $data_error['distributor_id'] = $d_id;
                            $data_error['store_code'] = $store_code;
                            $data_error['dealer_name'] = $dealer_name;
                            $data_error['level_name'] = $level_name;
                            $data_error['creditnote_price'] = $creditnote_price;
                            $data_error['message'] = "Dealer Already Import Or Reward is not existed in System";
                            $error_list[] = $data_error;
                        }

                        //print_r($result);die;
                        //$data['dealer_name'] = $dealer_name;
                        $status = $result['code'];
                        if ($result['code'] == 0){
                            $success_list[] = $data;
                        } else {
                            $data['message'] = $result['message'];
                            $error_list[] = $data;
                        }
                    }
                    $number_of_order++;
                    $percent = round($number_of_order * 100 / $total_order_row, 1);
                    $progress->flush($percent);
                }

                $data = array(
                    'total' => $total_order_row,
                    'failed' => count($error_list),
                    'succeed' => $total_order_row - count($error_list),
                );

                    // xuất file excel các order lỗi
                if (is_array($error_list) && count($error_list) > 0)
                {

                    $data['error_file_name'] = $d_id.'-'.'FAILED-' . md5(microtime(true) . uniqid('', true)) . '.' . $extension;

                    $objPHPExcel_out = new PHPExcel();
                    $objPHPExcel_out->createSheet();
                    $objWorksheet_out = $objPHPExcel_out->getActiveSheet();
                        //
                        // get product list
                    $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_quater_year, 1, 'quater_year');
                    $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_quater_no, 1, 'quater_no');
                    $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_distributor_id, 1, 'distributor_id');
                    $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_store_code, 1, 'store_code');
                    $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_title, 1, 'store_name');
                    $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_level_name, 1, 'level_name');
                    $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_total_imei, 1, 'total_imei');
                    $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_creditnote_price, 1, 'creditnote_price');
                    $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_creditnote_price_confirm, 1, 'creditnote_price_confirm');
                        //
                    switch ($extension) {
                        case 'xls':
                        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel_out);
                        break;
                        case 'xlsx':
                        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel_out);
                        break;
                        default:
                        throw new Exception("Invalid file extension");
                        break;
                    }

                    $new_file_dir = $uploaded_dir . DIRECTORY_SEPARATOR . $data['error_file_name'];

                        //Tanong
                    $objWriter->save($new_file_dir);
                }
                    // END IF // xuất file excel các order lỗi

                $where = $QFileLog->getAdapter()->quoteInto('id = ?', $log_id);
                $QFileLog->update($data, $where);

                $this->view->error_list = $error_list;
                $this->view->objWorksheet = $objWorksheet;
                $this->view->number_of_order = $number_of_order;

                    //commit
                $db->commit();

                $this->view->error_file = isset($data['error_file_name']) ? (HOST
                    . 'files'
                    . DIRECTORY_SEPARATOR . 'mou'
                    . DIRECTORY_SEPARATOR . $userStorage->id
                    . DIRECTORY_SEPARATOR . $uniqid
                    . DIRECTORY_SEPARATOR . $data['error_file_name']) : false;

                } // end of Try
                catch (Exception $e) {
                    $db->rollback();
                    $this->view->error = $e->getMessage();
                    $progress->flush(0);
                }
                $progress->flush(100);
            }// end of check file

           // unlink(APPLICATION_PATH . '/../public/files/mou/lock');


        } // end of check POST
    }

    private function _exportExcelRewardCreditNote_Wait_Confirm($data)
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Credit Note For Reward To Wait Confirm'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        $heads = array(
            'key_sn',
            'Quater Year',
            'Quater No',
            'Distributor id',
            'Distributor Code',
            'Distributor Name',
            'Level Name',
            'Total Imei',
            'Decorate Status',
            'Creditnote Amount',
            'Creditnote Amount Confirm',
        );
        fputcsv($output, $heads);



        $i = 2;
        foreach($data as $item)
        {
           $decorate_status="-";
           if($item['decorate_status']==1){
            $decorate_status="Approve";
        }else{
            $decorate_status="Wait To Check";
        }
        $row = array();
        $row[] = $item['key_sn'];
        $row[] = $item['quater_year'];
        $row[] = $item['quater_no'];
        $row[] = $item['distributor_id'];
        $row[] = $item['store_code'];
        $row[] = $item['title'];
        $row[] = $item['level_name'];
        $row[] = $item['total_imei'];
        $row[] = $decorate_status;
        $row[] = $item['creditnote_price'];
        $row[] = $item['creditnote_price_confirm'];


        fputcsv($output, $row);
        unset($item);
        unset($row);
    }

    unset($data);
    unset($result);

    fclose($output);

    ob_flush();
    ob_start();
    while (@ob_end_flush());

    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;

    $file = fopen($path, 'r');
    $content = fread($file, filesize($path));
    var_dump(filesize($path));
    var_dump($content);

    exit;
}

private function _exportExcelRewardCreditNote_Confirm($data)
{

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', '200M');
    $filename = 'Credit Note For Reward Confirmed'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);
    $path = $file_path.'/'.$filename;
    $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        $heads = array(
            'Quater Year',
            'Quater No',
            'Start Date',
            'End Date',
            'Distributor id',
            'Distributor Code',
            'Distributor Name',
            'Level Name',
            'Total Imei',
            'Decorate Status',

            'Credit Note No',
            'Creditnote Amount',
            'Creditnote Amount Confirm',
            'Confirm Date',
            'Confirm By',
            'Status',

        );
        fputcsv($output, $heads);



        $i = 2;
        foreach($data as $item)
        {
            $decorate_status="-";
            if($item['decorate_status']==1){
                $decorate_status="Approve";
            }else{
                $decorate_status="Wait To Check";
            }
            $row = array();
            $row[] = $item['quater_year'];
            $row[] = $item['quater_no'];
            $row[] = $item['start_date'];
            $row[] = $item['end_date'];
            $row[] = $item['distributor_id'];
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $item['level_name'];
            $row[] = $item['total_imei'];
            $row[] = $decorate_status;
            $row[] = $item['creditnote_sn'];
            $row[] = $item['creditnote_price'];
            $row[] = $item['creditnote_price_confirm'];
            $row[] = $item['confirm_date'];
            $row[] = $item['confirm_by'];
            $row[] = $item['status_cn'];


            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);
        unset($result);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    public function getCreditNoteNo_Ref($db,$sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $sn_ref="";
        try {
            $stmt = $db->query("CALL gen_running_no_ref_reward('CN',".$sn.")");
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
        }
        return $sn_ref;
    }

    public function _exportExcelOppoAllGreen($data)
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);



        // this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'OPPO All Green Report -'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);

        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'ID',
            'ROUND NO',
            'ROUND YEAR',
            'AIR NUMBE',
            'TAX ID',
            'DISTRIBUTOR ID',
            'DISTRIBUTOR NAME',
            'STORE ID',
            'STORE CODE',
            'STORE NAME',
            'START DATE',
            'END DATE',
            'SHOP TYPE',
            'TOTAL REWARD_PRICE ',
            'TAX PRICE',
            'CREDITNOTE PRICE CONFIRM',
            'ASM CONFIRM BY',
            'ASM CONFIRM DATE',
            'CONFIRM BY' ,
            'CONFIRM DATE' ,
            'STATUS CN' ,
            'CREDITNOTE_SN',
            'CREATE_DATE',
            'REASON REMARK',
            'STORE CODE',
        );

        fputcsv($output, $heads);

        $QDistributor   = new Application_Model_Distributor();
        $distributor_cache = $QDistributor->get_cache2();

        $i = 2;

        foreach($data as $item) {
            $row = array();
            $row[] = $item['id'];
            $row[] = $item['round_no'];
            $row[] = $item['round_year'];
            $row[] = $item['air_number'];
            $row[] = '\''.$item['tax_id'].'\'';
            $row[] = $item['d_id'];
            $row[] = $item['title'];
            $row[] = $item['store_id'];
            $row[] = $item['store_code'];
            $row[] = $item['store_name'];
            $row[] = $item['start_date'];
            $row[] = $item['end_date'];
            $row[] = $item['shop_type'];
            $row[] = $item['total_reward_price'];
            $row[] = $item['tax_price'];
            $row[] = $item['creditnote_price_confirm'];
            $row[] = $item['asm_confirm_by'];
            $row[] = $item['asm_confirm_date'];
            $row[] = $item['confirm_by'];
            $row[] = $item['confirm_date'];
            $row[] = $item['status_cn'];
            $row[] = $item['creditnote_sn'];
            $row[] = $item['create_date'];
            $row[] = $item['reason_remark'];
            $row[] = $item['store_code'];


            fputcsv($output, $row);
            unset($item);
            unset($row);

        }
        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    public function duePaymentOrderAction()
    {
        $sort              = $this->getRequest()->getParam('sort', 'p.id');
        $desc              = $this->getRequest()->getParam('desc', 1);
        $page              = $this->getRequest()->getParam('page', 1);
        $d_id              = $this->getRequest()->getParam('d_id');
        $due_from          = $this->getRequest()->getParam('due_from',date('d/m/Y') );
        $due_to            = $this->getRequest()->getParam('due_to',date('d/m/Y') );

        if ($due_from == '') {
            $due_from = date('d/m/Y');
        }
        if ($due_to == '') {
            $due_to = date('d/m/Y');
        }


        $limit = 20;
        $total = 0;
        $params = array(
            'due_from'  =>$due_from,
            'due_to'    =>$due_to,
            'd_id'    =>$d_id,
        );
        $QMarket = new Application_Model_Market();
        $due = $QMarket->duePaymentOrder($page, $limit, $total, $params);
        $distributor = $QMarket->SelectDistributor();


        $this->view->due         = $due;
        $this->view->params      = $params;
        $this->view->distributor      = $distributor;
        $this->view->desc       = $desc;
        $this->view->sort       = $sort;
        $this->view->limit      = $limit;
        $this->view->total      = $total;
        $this->view->url        = HOST . 'due-payment-order/' . ($params ? '?' . http_build_query($params) .'&' : '?');

        $this->view->offset = $limit * ($page - 1);
        $this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER');


    }

    public function paymentnoManageAction(){

        $this->view->back_url = HOST . 'finance/paymentno-list';

        $flashMessenger = $this->_helper->flashMessenger;

        $d_id = $this->getRequest()->getParam('d_id');

        if ($this->getRequest()->getMethod() == 'POST'){

            $data_d_id          = $this->getRequest()->getParam('d_id');
            $data_payment_id    = $this->getRequest()->getParam('payment_id');
            $data_payment_no    = $this->getRequest()->getParam('payment_no');
            $data_use_balance   = $this->getRequest()->getParam('use_balance');
            $data_remark        = $this->getRequest()->getParam('remark');

            $QPGBal = new Application_Model_PayGroupBalance();

            $checkPayGroupBalance = $QPGBal->checkPaymentGroupBalance($data_d_id,$data_payment_id);

            if($checkPayGroupBalance){

                if($checkPayGroupBalance['balance_total'] < $data_use_balance){
                    echo json_encode(['status' => '400', 'message' => 'Can not update, Use Balance > Total Balance']);
                    exit();
                }

            }else{
                echo json_encode(['status' => '400', 'message' => 'Can not update, Not have Payment No']);
                exit();
            }

            
            $db = Zend_Registry::get('db');

            $db->beginTransaction();

            try {

                $date = date('Y-m-d H:i:s');

                $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                $QPGT = new Application_Model_PayGroupTran();

                $QPGT->insert(array(
                    'payment_id' => $data_payment_id,
                    'payment_tran_id' => $data_payment_id,
                    'distributor_id' => $data_d_id,
                    'use_total' => $data_use_balance,
                    'create_date' => $date,
                    'create_by' => $userStorage->id,
                    'status' => 1,
                    'pay_type' => 1,
                    'remark' => $data_remark
                ));

                $db->commit();

                $messages = $flashMessenger->setNamespace('success')->addMessage('Done!');

                echo json_encode(['status' => '201', 'message' => 'Done.', 'url' => HOST.'finance/paymentno-manage?d_id=' . $data_d_id]);

                exit();

            }
            catch (exception $e) {

                $db->rollback();

                echo json_encode(['status' => '400', 'message' => $e->getMessage()]);

                exit();
            }

        }

        $QPGBal = new Application_Model_PayGroupBalance();

        $getPayGroupBalance = $QPGBal->getDataPayGroupBalance($d_id);

        $this->view->getPayGroupBalance = $getPayGroupBalance;

        $QDistributor  = new Application_Model_Distributor();
        $distributors  = $QDistributor->get_cache();

        $d_name = isset($distributors[$d_id]) ? $distributors[$d_id] : '';

        $this->view->distributor = ['id' => $d_id,'name' => $d_name];

        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

    }

    public function exportPaymentnoManageAction()
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $d_id = $this->getRequest()->getParam('d_id');

        if ($this->getRequest()->getMethod() == 'POST' && isset($d_id) && $d_id){

            $QPGBal = new Application_Model_PayGroupBalance();

            $getPayGroupBalance = $QPGBal->getDataPayGroupBalance($d_id, true);

            $db = Zend_Registry::get('db');
            set_time_limit(0);
            error_reporting(~E_ALL);
            ini_set('display_error', 0);
            ini_set('memory_limit', '200M');
            $filename = 'payment_no_manage_'.date('d-m-Y H-i-s').'.csv';
            // output headers so that the file is downloaded rather than displayed
            while (@ob_end_clean());
            ob_start();
            header('Content-Encoding: UTF-8');
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename='.$filename);
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
            if (!file_exists($file_path))
                mkdir($file_path, 0777, true);
            $path = $file_path.'/'.$filename;
            $output = fopen($path, 'w+');
            echo "\xEF\xBB\xBF"; // UTF-8 BOM
            // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
            // fputs($output, chr(239) . chr(187) . chr(191));

            ////////////////////////////////////////////////////
            /////////////////// TỔNG HỢP DỮ LIỆU
            ////////////////////////////////////////////////////

            $heads = array(
                'PAYMENT NO',
                'TOTAL AMOUNT',
                'TOTAL USED',
                'TOTAl BALANCE',
                'CREATE DATE',
                'UPDATE DATE',
                'CONFIRM DATE',
                'CONFIRM STATUS'

            );

            fputcsv($output, $heads);

            $i = 2;
            foreach($getPayGroupBalance as $item)

            {

                $row = array();
                $row[] = $item['payment_no'];
                $row[] = $item['total_amount'];
                $row[] = $item['use_total'];
                $row[] = $item['balance_total'];
                $row[] = $item['create_date'];
                $row[] = $item['update_date'];
                $row[] = $item['confirmed_date'];

                $textConfirm_status = 'ยังไม่อนุมัติ';

                if(isset($item['confirmed_date']) && $item['confirmed_date']){
                    $textConfirm_status = 'อนุมัติ';
                }

                $row[] = $textConfirm_status;

                fputcsv($output, $row);
                unset($item);
                unset($row);
            }

            unset($data);

            fclose($output);

            ob_flush();
            ob_start();
            while (@ob_end_flush());

            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;

            $file = fopen($path, 'r');
            $content = fread($file, filesize($path));
            var_dump(filesize($path));
            var_dump($content);

            exit;
        }
    }

    public function confirmPaymentnoAction(){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $flashMessenger = $this->_helper->flashMessenger;

        $d_id = $this->getRequest()->getParam('d_id');
        $payment_id = $this->getRequest()->getParam('payment_id');

        $db = Zend_Registry::get('db');

        $db->beginTransaction();

        try {

            $date = date('Y-m-d H:i:s');

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $QPGB = new Application_Model_PayGroupBalance();

            $data = array(
                'update_date' => $date,
                'update_by' => $userStorage->id,
                'confirmed_date' => $date,
                'confirmed_by' => $userStorage->id
            );

            $where = [];
            $where[] = $QPGB->getAdapter()->quoteInto('payment_id in (?)', $payment_id);
            $where[] = $QPGB->getAdapter()->quoteInto('distributor_id = ?', $d_id);
            $where[] = $QPGB->getAdapter()->quoteInto('status = ?', 1);

            $QPGB->update($data, $where);

            $db->commit();

            $messages = $flashMessenger->setNamespace('success')->addMessage('Done!');

            echo json_encode(['status' => '201', 'message' => 'Done.', 'url' => HOST.'finance/paymentno-manage?d_id=' . $d_id]);

            exit();

        }
        catch (exception $e) {

            $db->rollback();

            echo json_encode(['status' => '400', 'message' => $e->getMessage()]);

            exit();
        }

    }


    function depositConfirmAction()
    {
        //print_r($_GET);die; 
        $flashMessenger = $this->_helper->flashMessenger;

        $deposit_sn = $this->getRequest()->getParam('deposit_sn');
        $distributor_id = $this->getRequest()->getParam('d_id');
        $view_status = $_GET['view_status'];
        //$view_status = $this->getRequest()->getParam('view_status');

        $QDeposit = new Application_Model_Deposit();
        $limit = LIMITATION;
        $total = 0;
        $params = array_filter(array(
            'deposit_sn'       => $deposit_sn,
            'd_id'             => $distributor_id,
            'view_status'             => $view_status,
            'distributor_name' => $distributor_name
        ));
        $get_resule = $QDeposit->fetchPagination($page, $limit, $total, $params);
        //print_r($get_resule);
        $this->view->get_resule     = $get_resule;


        if ($this->getRequest()->getMethod() == 'POST') {   
            $deposit_sn = $this->getRequest()->getParam('deposit_sn');
            $deposit_status = $this->getRequest()->getParam('deposit_status');
            //$view_status = $this->getRequest()->getParam('view_status');
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            try {
                if($deposit_sn!=''){

                    $db = Zend_Registry::get('db');
                    $db->beginTransaction();
                    $confirm_date = date('Y-m-d H:i:s');

                    $data = array(
                        'confirm_date' => $confirm_date,
                        'confirm_by' => $userStorage->id,
                        'status' => $deposit_status
                    );

                    $where = [];
                    $where[] = $QDeposit->getAdapter()->quoteInto('deposit_sn = ?', $deposit_sn);
                    $QDeposit->update($data, $where);

                    $db->commit();

                    $flashMessenger->setNamespace('success')->addMessage('Confirm Success');
                    $this->_redirect(($back_url ? $back_url : HOST . 'finance/deposit-confirm-list?view_status='.$view_status));
                }

            }catch (exception $e) {
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage('Cannot Confirm Deposit, Please try again!');
                $this->_redirect(($back_url ? $back_url : HOST . 'finance/deposit-confirm-list?view_status='.$view_status));
            }
        }
    }


    public function depositConfirmListAction()
    {

        $this->view->meta_refresh = 300;

        $sort               = $this->getRequest()->getParam('sort');
        $desc               = $this->getRequest()->getParam('desc', 0);
        $page               = $this->getRequest()->getParam('page', 1);

        $deposit_sn         = $this->getRequest()->getParam('deposit_sn');
        $d_id               = $this->getRequest()->getParam('d_id');
        $distributor_name   = $this->getRequest()->getParam('distributor_name');
        $rank               = $this->getRequest()->getParam('rank');
        $export             = $this->getRequest()->getParam('export', 0);
        $view_status        = $this->getRequest()->getParam('view_status', 1);

        $limit = LIMITATION;
        $total = 0;

        $this->view->rank = $rank;
        $this->view->d_id = $d_id;

        $params = array_filter(array(
            'deposit_sn'       => $deposit_sn,
            'rank' => $rank,
            'd_id'             => $d_id,
            'distributor_name' => $distributor_name,
            'view_status' => $view_status
        ));

        $QDeposit = new Application_Model_Deposit();
        $QDistributor   = new Application_Model_Distributor();

        $distributors = $QDistributor->get_with_store_code_cache();

        $params['sort'] = $sort;
        $params['desc'] = $desc;

        if (isset($export) && $export) {
            $get_resule = $QDeposit->fetchPagination($page, null, $total, $params);
            $this->_exportExcelListPaymentNo($get_resule);
        }

        $get_resule = $QDeposit->fetchPagination($page, $limit, $total, $params);

        // print_r($get_resule);die;

        $this->view->get_resule     = $get_resule;
        $this->view->distributors      = $distributors;

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->no_show_brandshop = $no_show_brandshop;
        $this->view->url    = HOST . 'finance/deposit-confirm-list/' . ($params ? '?' . http_build_query($params) .
            '&' : '?');

        $this->view->params = $params;

        $this->view->offset = $limit * ($page - 1);

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $this->_helper->viewRenderer->setRender('partials/deposit-confirm-list');
        }
    }

    
    public function createCnPriceManualAction()
    {
        $QDistributor = new Application_Model_Distributor();
        $this->view->distributors = $QDistributor->get_all();
    }

    function createCnPriceManualSaveAction()
    {
        //print_r($_POST);
        $flashMessenger = $this->_helper->flashMessenger;

        if ($this->getRequest()->getMethod() == 'POST') {   
            $distributor_id = $this->getRequest()->getParam('d_id');
            $create_date = $this->getRequest()->getParam('create_date');
            $creditnote_type = $this->getRequest()->getParam('creditnote_type');
            $chanel = $this->getRequest()->getParam('chanel');
            $creditnote_status = $this->getRequest()->getParam('creditnote_status');
            $price_ext_vat = $this->getRequest()->getParam('price_ext_vat');
            $price = $this->getRequest()->getParam('price');
            $vat = $this->getRequest()->getParam('vat');
            $wht_vat = $this->getRequest()->getParam('wht_vat');
            $wht_price = $this->getRequest()->getParam('wht_price');
            $manual_remark = $this->getRequest()->getParam('manual_remark');

            $QCreditNote = new Application_Model_CreditNote();
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            $sn = date('YmdHis') . substr(microtime(), 2, 4);

            try {
                if($sn!=''){
                    $db = Zend_Registry::get('db');
                    $db->beginTransaction();
                    $chk_create_date = date('Y-m-d');
                    if($chk_create_date==$create_date){
                        $creditnote_sn = $QCreditNote->getReward_CreateNoteNo_Ref($db,$distributor_id,$userStorage->id,$sn);
                    }else if($chk_create_date>=$create_date){
                        $sql="SELECT CONCAT(LEFT(MAX(creditnote_sn),9 ),mask(RIGHT(MAX(creditnote_sn),5 )+1,'#####')) AS new_running_CN
                        FROM `credit_note` WHERE create_date BETWEEN '".$create_date." 00:00:00' AND '".$create_date." 23:59:59'";
                        $result = $db->fetchAll($sql);
                        $creditnote_sn =$result[0]['new_running_CN'];
                        if($creditnote_sn==''){
                            $sql="SELECT CONCAT('CN',DATE_FORMAT(STR_TO_DATE('".$create_date."', '%Y-%m-%d') + INTERVAL 543 YEAR,'%y%m%d'),'-',mask(1,'#####')) AS new_running_CN";
                            $result_new = $db->fetchAll($sql);
                            $creditnote_sn =$result_new[0]['new_running_CN'];
                        }
                    }else{
                        $flashMessenger->setNamespace('error')->addMessage('Cannot Save Creditnote (Create CN Date > Date Now), Please try again!');
                        $this->_redirect(($back_url ? $back_url : HOST . '/finance/creditnote-manual-list'));
                    }
                    
                    $data = array(
                        'distributor_id' => $distributor_id,
                        'create_by' => $userStorage->id,
                        'create_date' => $create_date,
                        'creditnote_type' => $creditnote_type,
                        'chanel' => $chanel,
                        'price_ext_vat' => $price_ext_vat,
                        'total_amount' => $price,
                        'use_total' => 0,
                        'balance_total' => $price,
                        'status' => 0,
                        'creditnote_sn' => $creditnote_sn,
                        'manual' => 1,
                        'manual_active' => $creditnote_status,
                        'vat' => $vat,
                        'wht_vat' => $wht_vat,
                        'wht_price' => $wht_price,
                        'manual_remark' => $manual_remark,
                        'sn' => $sn
                    );

                  //  print_r($data);die;
                    $QCreditNote->insert($data);
                    
                    //commit
                    $db->commit();
                    

                    //$this->_redirect(($back_url ? $back_url : HOST . 'finance/deposit-list'));
                    $flashMessenger->setNamespace('success')->addMessage('Save Success');
                    $this->_redirect(($back_url ? $back_url : HOST . '/finance/creditnote-manual-list'));
                }
                //$stmt->execute();
            }catch (exception $e) {
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage('Cannot Save Creditnote, Please try again!'.$e.messages);
                $this->_redirect(($back_url ? $back_url : HOST . '/finance/creditnote-manual-list'));
            }
        }
    }

    function creditnoteManualConfirmAction()
    {

        $flashMessenger = $this->_helper->flashMessenger;

        $creditnote_sn = $this->getRequest()->getParam('creditnote_sn');
        $distributor_id = $this->getRequest()->getParam('d_id');
        $view_status = $_GET['view_status'];
        //$view_status = $this->getRequest()->getParam('view_status');

        $QCreditNote = new Application_Model_CreditNote();
        $limit = LIMITATION;
        $total = 0;
        $params = array_filter(array(
            'creditnote_sn'       => $creditnote_sn,
            'd_id'             => $distributor_id,
            'view_status'             => $view_status,
            'distributor_name' => $distributor_name,
            'action_frm' => 'confirm'
        ));
        $get_resule = $QCreditNote->creditnoteManualfetchPagination($page, $limit, $total, $params);
        //print_r($get_resule);
        $this->view->get_resule     = $get_resule;


        if ($this->getRequest()->getMethod() == 'POST') {   
            $creditnote_sn = $this->getRequest()->getParam('creditnote_sn');
            $creditnote_status = $this->getRequest()->getParam('creditnote_status');
            $status = $this->getRequest()->getParam('status');
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            //print_r($_POST);die;
            try {
                if($creditnote_sn!=''){

                    $db = Zend_Registry::get('db');
                    $db->beginTransaction();
                    $confirm_date = date('Y-m-d H:i:s');



                    $data = array(
                        'confirm_date' => $confirm_date,
                        'confirm_by' => $userStorage->id,
                        //'status' => $creditnote_status
                    );

                    if($status==1){
                        $data['status']=1;
                        $data['manual_active']=1;
                    }

                    $where = [];
                    $where[] = $QCreditNote->getAdapter()->quoteInto('creditnote_sn = ?', $creditnote_sn);
                    $QCreditNote->update($data, $where);

                    $db->commit();

                    $flashMessenger->setNamespace('success')->addMessage('Confirm Success');
                    $this->_redirect(($back_url ? $back_url : HOST . 'finance/creditnote-manual-confirm-list?view_status='.$view_status));
                }

            }catch (exception $e) {
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage('Cannot Confirm Creditnote, Please try again!');
                $this->_redirect(($back_url ? $back_url : HOST . 'finance/creditnote-manual-confirm-list?view_status='.$view_status));
            }
        }
    }

    public function creditnoteManualListAction()
    {

        $this->view->meta_refresh = 300;

        $sort               = $this->getRequest()->getParam('sort');
        $desc               = $this->getRequest()->getParam('desc', 0);
        $page               = $this->getRequest()->getParam('page', 1);

        $creditnote_sn         = $this->getRequest()->getParam('creditnote_sn');
        $d_id               = $this->getRequest()->getParam('d_id');
        $distributor_name   = $this->getRequest()->getParam('distributor_name');
        $rank               = $this->getRequest()->getParam('rank');
        $view_status        = $this->getRequest()->getParam('view_status', 1);
        $export             = $this->getRequest()->getParam('export', 0);

        $limit = LIMITATION;
        $total = 0;

        $this->view->rank = $rank;
        $this->view->d_id = $d_id;

        $params = array_filter(array(
            'creditnote_sn'       => $creditnote_sn,
            'rank' => $rank,
            'd_id'             => $d_id,
            'distributor_name' => $distributor_name,
            'view_status' => $view_status,
            'action_frm' => 'list'
        ));

        $QCreditNote = new Application_Model_CreditNote();
        $QDistributor   = new Application_Model_Distributor();

        $distributors = $QDistributor->get_with_store_code_cache();

        $params['sort'] = $sort;
        $params['desc'] = $desc;

        if (isset($export) && $export) {
            $get_resule = $QCreditNote->creditnoteManualfetchPagination($page, null, $total, $params);
            $this->_exportExcelListCreditnoteNoManual($get_resule);
        }

        $get_resule = $QCreditNote->creditnoteManualfetchPagination($page, $limit, $total, $params);

        // print_r($get_resule);die;

        $this->view->get_resule     = $get_resule;
        $this->view->distributors      = $distributors;

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->no_show_brandshop = $no_show_brandshop;
        $this->view->url    = HOST . 'finance/creditnote-manual-list/' . ($params ? '?' . http_build_query($params) .
            '&' : '?');
        $this->view->params = $params;
        $this->view->offset = $limit * ($page - 1);

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $this->_helper->viewRenderer->setRender('partials/creditnote-manual-list');
        }
    }

    public function creditnoteManualConfirmListAction()
    {

        $this->view->meta_refresh = 300;

        $sort               = $this->getRequest()->getParam('sort');
        $desc               = $this->getRequest()->getParam('desc', 0);
        $page               = $this->getRequest()->getParam('page', 1);

        $creditnote_sn         = $this->getRequest()->getParam('creditnote_sn');
        $d_id               = $this->getRequest()->getParam('d_id');
        $distributor_name   = $this->getRequest()->getParam('distributor_name');
        $rank               = $this->getRequest()->getParam('rank');
        $view_status        = $this->getRequest()->getParam('view_status', 1);
        $export             = $this->getRequest()->getParam('export', 0);

        $limit = LIMITATION;
        $total = 0;

        $this->view->rank = $rank;
        $this->view->d_id = $d_id;

        $params = array_filter(array(
            'creditnote_sn'       => $creditnote_sn,
            'rank' => $rank,
            'd_id'             => $d_id,
            'distributor_name' => $distributor_name,
            'view_status' => $view_status,
            'action_frm' => 'confirm'
        ));

        $QCreditNote = new Application_Model_CreditNote();
        $QDistributor   = new Application_Model_Distributor();

        $distributors = $QDistributor->get_with_store_code_cache();

        $params['sort'] = $sort;
        $params['desc'] = $desc;

        if (isset($export) && $export) {
            $get_resule = $QCreditNote->creditnoteManualfetchPagination($page, null, $total, $params);
            $this->_exportExcelListCreditnoteNoManual($get_resule);
        }

        $get_resule = $QCreditNote->creditnoteManualfetchPagination($page, $limit, $total, $params);

        // print_r($get_resule);die;

        $this->view->get_resule     = $get_resule;
        $this->view->distributors      = $distributors;

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->no_show_brandshop = $no_show_brandshop;
        $this->view->url    = HOST . 'finance/creditnote-manual-confirm-list/' . ($params ? '?' . http_build_query($params) .
            '&' : '?');
        $this->view->params = $params;
        $this->view->offset = $limit * ($page - 1);

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $this->_helper->viewRenderer->setRender('partials/creditnote-manual-confirm-list');
        }
    }

    private function _exportExcelListCreditnoteNoManual($data)
    {
        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'creditnote_no_manual_list_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/finance/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'REQUEST DATE',
            'REQUEST BY',
            'CONFIRM DATE',
            'CONFIRM BY',
            'STATUS',
            'CREDIT NOTE NO',
            'CHANEL',
            'TYPE DISCOUNT',
            'PRICE (EXT VAT)',
            'VAT (%)',
            'PRICE (ภาษีมูลค่าเพิ่ม)',
            'PRICE (IN VAT)',
            'ภาษีหัก ณ ที่จ่าย (%)',
            'PRICE (ภาษีหัก ณ ที่จ่าย)',
            'PRICE TOTAL(IN VAT)',
            'USE TOTAL',
            'BALANCE TOTAL',
            'DESCRIPTION CREDIT NOTE NO',
            'DISTRIBUTOR TYPE',
            'DISTRIBUTOR CODE',
            'DISTRIBUTOR NAME',
            'TAX NO',
            'AREA_NAME',
            'PROVINCE',
            'DISTRICT',
            'FINANCE GROUP'
            

        );

        if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID,CHECK_MONEY))) {
            $heads[] = 'FINANCE REMARK';
        }

        fputcsv($output, $heads);

        $i = 2;$chanel="";
        foreach($data as $item)
        {
            switch ($item['chanel']) {
                case 'reward':
                $chanel = "ส่งเสริมการขาย";
                break;
                case 'incentive':
                $chanel = "ค่า Incentive";
                break;
                case 'decoration':
                $chanel = "ค่าตกแต่งหน้าร้าน";
                break;
                case 'price':
                $chanel = "แก้ไขราคา";
                break;    
                case 'oppo_all_green':
                $chanel = "OPPO All Green";
                break;    
                case 'top_green':
                $chanel = "OPPO Top Green";
                break;                    
                default:
                $chanel = "";         
            }

            $parent_key = $item['parent'];
            if($parent_key=='0'){
                $distributor_type="สำนักงานใหญ่";
            }else{
                $distributor_type="สาขา";
            }

            $status = $item['status'];
            if($status=='0'){
                $status_name="-";
            }else{
                $status_name="เปิดใช้งาน";
            }
            $price_ext_vat= $item['price_ext_vat'];
            $vat= $item['vat'];
            switch($vat) {
                case '0':
                $vat_cal = 0;
                break;
                case '7':
                $vat_cal = 0.07;
                break;
                default:
                $vat_cal = 0;
            }

            $total_price_vat = ($price_ext_vat * $vat_cal);

            $row = array();
            $row[] = $item['create_date'];
            $row[] = $item['staff_name'];
            $row[] = $item['confirm_date'];
            $row[] = $item['confirm_staff_name'];
            $row[] = $status_name;
            $row[] = $item['creditnote_sn'];
            $row[] = $chanel;
            $row[] = $item['creditnote_type'];
            $row[] = number_format($item['price_ext_vat'],2);
            $row[] = $item['vat']."%";
            $row[] = number_format($total_price_vat,2);
            $row[] = number_format($total_price_vat+$price_ext_vat,2);
            $row[] = $item['wht_vat']."%";
            $row[] = number_format($item['wht_price'],2);
            $row[] = number_format($item['total_amount'],2);
            $row[] = number_format($item['use_total'],2);
            $row[] = number_format($item['balance_total'],2);
            $row[] = $item['manual_remark'];
            $row[] = $distributor_type;
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $item['tax_no'];
            $row[] = $item['area_name'];
            $row[] = $item['province'];
            $row[] = $item['district'];
            $row[] = $item['finance_group'];
            
            if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID,CHECK_MONEY))) {
                $row[] = $item['finance_remark'];
            }
            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    
    public function returnBoxNumberImeiConfirmListAction()
    {

        //print_r($_POST);
        $this->view->meta_refresh = 300;

        $sort               = $this->getRequest()->getParam('sort');
        $desc               = $this->getRequest()->getParam('desc', 0);
        $page               = $this->getRequest()->getParam('page', 1);

        $box_number         = $this->getRequest()->getParam('box_number');
        $box_post_number         = $this->getRequest()->getParam('box_post_number');
        $sender_name         = $this->getRequest()->getParam('sender_name');
        $d_id               = $this->getRequest()->getParam('d_id');
        $distributor_name   = $this->getRequest()->getParam('distributor_name');
        $rank               = $this->getRequest()->getParam('rank');
        $start_date         = $this->getRequest()->getParam('start_date');
        $end_date           = $this->getRequest()->getParam('end_date');
        $view_status        = $this->getRequest()->getParam('view_status', 1);
        $export             = $this->getRequest()->getParam('export', 0);

        
        $limit = LIMITATION;
        $total = 0;

        $this->view->rank = $rank;
        $this->view->d_id = $d_id;

        if ($start_date !=""){
            list( $day, $month, $year ) = explode('/', $start_date);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $start_date_val = $year.'-'.$month.'-'.$day.' '.$time. '00:00:00';
            }
        }

        if ($end_date !=""){
            list( $day, $month, $year ) = explode('/', $end_date);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $end_date_val = $year.'-'.$month.'-'.$day.' '.$time. '23:59:59';
            }
        }

        $params = array_filter(array(
            'box_number'       => $box_number,
            'box_post_number' => $box_post_number,
            'sender_name' => $sender_name,
            'rank' => $rank,
            'd_id'             => $d_id,
            'distributor_name' => $distributor_name,
            'view_status' => $view_status,
            'start_date' => $start_date_val,
            'end_date' => $end_date_val,
            'action_frm' => 'confirm'
        ));

        $QReturnBoxNumber = new Application_Model_ReturnBoxNumber();
        $QReturnBoxNumberImei = new Application_Model_ReturnBoxNumberImei();
        $QDistributor   = new Application_Model_Distributor();

        $distributors = $QDistributor->get_with_store_code_cache();

        $params['sort'] = $sort;
        $params['desc'] = $desc;

        /*if (isset($export) && $export) {
            $get_resule = $QReturnBoxNumber->fetchPagination($page, null, $total, $params);
            $this->_exportExcelBoxList($get_resule);
        }*/

        if (isset($export) && $export) {
            $params['action_frm'] = 'export';
            //$get_resule = $QReturnBoxNumberImei->fetchPagination($page, null, $total, $params);
            $get_resule = $QReturnBoxNumberImei->fetchPagination($page, null, $total, $params);
            $this->_exportBoxImeiList($get_resule);
        }else{
            $get_resule = $QReturnBoxNumber->fetchPagination($page, $limit, $total, $params);
            $get_resule_all = $QReturnBoxNumber->fetchPagination(1, 1000, $total, $params);
        }


        //$get_resule = $QReturnBoxNumber->fetchPagination($page, $limit, $total, $params);

        // print_r($get_resule);die;

        $this->view->get_resule     = $get_resule;
        $this->view->get_resule_all     = $get_resule_all;
        $this->view->distributors      = $distributors;

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->no_show_brandshop = $no_show_brandshop;
        $this->view->url    = HOST . 'finance/return-box-number-imei-confirm-list/' . ($params ? '?' . http_build_query($params) .
            '&' : '?');
        $this->view->params = $params;
        $this->view->offset = $limit * ($page - 1);

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $this->_helper->viewRenderer->setRender('partials/return-box-number-imei-confirm-list');
        }
    }


    public function cpAutoCheckImeiListAction()
    {

        //print_r($_POST);die;
        $this->view->meta_refresh = 300;

        $sort               = $this->getRequest()->getParam('sort');
        $desc               = $this->getRequest()->getParam('desc', 0);
        $page               = $this->getRequest()->getParam('page', 1);

        $lot_number         = $this->getRequest()->getParam('lot_number');
        $sender_name         = $this->getRequest()->getParam('sender_name');
        $d_id               = $this->getRequest()->getParam('d_id');
        $good_id            = $this->getRequest()->getParam('good_id');
        $distributor_name   = $this->getRequest()->getParam('distributor_name');
        $rank               = $this->getRequest()->getParam('rank');
        $start_date         = $this->getRequest()->getParam('start_date');
        $end_date           = $this->getRequest()->getParam('end_date');
        $view_status        = $this->getRequest()->getParam('view_status', 1);
        $export             = $this->getRequest()->getParam('export', 0);

        
        $limit = LIMITATION;
        $total = 0;

        $QGood          = new Application_Model_Good();
        $select_good = $QGood->select();
        $select_good->from(array('u' => 'good'),array('u.id','u.name'));
        $select_good->where('u.cat_id=?',11);
        $select_good->order('name');
        $good = $QGood->fetchAll($select_good);
        //print_r($good);
        $this->view->goods = $good;

        $this->view->rank = $rank;
        $this->view->d_id = $d_id;

        $params = array_filter(array(
            'lot_number'       => $lot_number,
            'd_id'             => $d_id,
            'good_id'          => $good_id,
            'distributor_name' => $distributor_name,
            'view_status' => $view_status,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'action_frm' => 'finance_confirm'
        ));
        //print_r($params);//die;
        $QCPAutoCheckImei = new Application_Model_CPAutoCheckImei();
        $QDistributor   = new Application_Model_Distributor();

        $distributors = $QDistributor->get_with_store_code_cache();

        $params['sort'] = $sort;
        $params['desc'] = $desc;

        if (isset($export) && $export) {
            $get_resule = $QCPAutoCheckImei->ImeiAutoCheckList($params);
            $this->_exportCPImeiList($get_resule);
        }else{
            $get_resule = $QCPAutoCheckImei->fetchPagination($page, $limit, $total, $params);
        }
        // print_r($get_resule);die;

        $this->view->get_resule     = $get_resule;
        $this->view->distributors      = $distributors;

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->no_show_brandshop = $no_show_brandshop;
        $this->view->url    = HOST . 'finance/cp-auto-check-imei-list/' . ($params ? '?' . http_build_query($params) .
            '&' : '?');
        $this->view->params = $params;
        $this->view->offset = $limit * ($page - 1);
        
        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $this->_helper->viewRenderer->setRender('partials/cp-auto-check-imei-list');
        }
    }


    private function _exportCPImeiList($result) 
    {

        //print_r($result['result_by_imei_log']);die;
        // this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        //$db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'CPLotImeiList - '.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);

        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF";  // UTF-8 BOM

        $heads = array(
            'Imei No',
            'distributor_id',
            'Store Code',
            'Distributor Name',
            'Finance Group',
            'Imei Type',
            'Remark',
            'Product Name',
            'Product Color',
            'Price',
            'Margin',
            'Sell Off Discount',
            'Special Discount',
            'Invoice Number',
            'Invoice Price',
            'Creditnote No',
            'CP Price',
            'Active CN',
            'Finance Confirm By',  
            'Admin Create By',  
            'Lot Number',
            'ວັນທີໃຊ້ລາຄາໃໝ່',
            'ຜົນການກວດສອບ',
            'ວັນທີຂາຍອອກໃນ Catty',
            'ຜົນການສ້າງ CP',
            'good_id',
            'good_color',
            'Sub Distributor ID',
            'Sub Distributor Name',
        );

        fputcsv($output, $heads);

        $QDistributor   = new Application_Model_Distributor();
        $distributor_cache = $QDistributor->get_cache2();
        //$result = $db->query($sql);
        $i = 2;

        foreach($result['result_by_imei'] as $item){

            $where_payment = $QDistributor->getAdapter()->quoteInto('id = ?', $item['d_id']);
            $distributors_payment = $QDistributor->fetchRow($where_payment);
            $rank = $distributors_payment->rank;

            $product_qty        = $item['num'];
            if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = 'ສຳນັກງານໃຫຍ່'; }else { $branch_type = 'สาขา'; }
            if ($item['canceled']== 1){$cancel = 'Canceled';}else{$cancel = 'Not Cancel';}

            if($item['check_timing_status'] =='0'){
                $remark_timing=" [ຂາຍອອກກ່ອນວັນປັບລາຄາ ".$item['timing_date']."]";
            }else{
                $remark_timing="";
            }

            if($item['check_activated_status'] =='0'){
                $remark_activated=" [ວັນທີ activate ນ້ອຍກ່ວາວັນປັບລາຄາ ".$item['activated_date']."]";
            }else{
                $remark_activated="";
            }

            if($item['check_out_date_status'] =='0'){
                $remark_out_date=" [ວັນທີ Sell IN ຫຼາຍກ່ວາວັນປັບລາຄາ ".$item['out_date']."]";
            }else{
                $remark_out_date="";
            }

            $status_check="";$remark='';
            $remark = $remark_timing.$remark_activated.$remark_out_date;
            if($remark !=''){
                $status_check="ບໍ່ສຳເລັດ";
            }else{
                $remark='ຜ່ານ';
                $status_check="ສຳເລັດ";
            }

            $row = array();
            $row[] = $item['imei_sn'];
            $row[] = $item['distributor_id'];
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $item['finance_group'];
            $row[] = $item['type_name'];
            $row[] = $item['remark'];
            $row[] = $item['product_name'];
            $row[] = $item['product_color'];
            $row[] = $item['price'];
            $row[] = $item['margin'];
            $row[] = $item['sale_off_percent'];
            $row[] = $item['spc_discount'];
            $row[] = $item['invoice_number'];
            $row[] = $item['invoice_price'];
            $row[] = $item['creditnote_sn'];
            $row[] = $item['unit_price'];
            $row[] = $item['active_name'];
            $row[] = $item['finance_confirm_by_name'];
            $row[] = $item['admin_create_by_name'];
            $row[] = $item['lot_number'];
            $row[] = $item['cp_date'];
            $row[] = $remark;
            $row[] = $item['timing_date'];
            $row[] = $status_check;
            $row[] = $item['good_id'];
            $row[] = $item['good_color'];
            $row[] = $item['sub_d_id'];
            $row[] = $item['sub_distributor_name'];

            fputcsv($output, $row);
            unset($item);
            unset($row);

        }


        foreach($result['result_by_imei_log'] as $item){

            $remark="";
            if($item['remark']!=''){
                $remark=$item['remark'];
            }

            if($item['creditnote_sn']!=''){
                $remark="ມີການຄືນສິນຄ້າ ".$item['creditnote_sn'];
            }
            if($item['type_name']==''){
                $remark="ບໍ່ເຫັນ Imei ໃນລະບົບ";
            }
            if($item['remark']=='duplicate'){
                $remark="Imei ຊ້ຳກັບການປັບລາຄາຮອບນີ້";
            }
            if($item['remark']=='no_order'){
                $remark="Imei ຍັງບໍ່ເປີດ Order";
            }

            if($item['remark']=='imei_error'){
                $remark="ບໍ່ເຫັນ Imei ໃນລະບົບ";
            }

            $row = array();
            $row[] = $item['imei_sn'];
            $row[] = $item['distributor_id'];
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $item['finance_group'];
            $row[] = $item['type_name'];
            $row[] = "";
            $row[] = $item['product_name'];
            $row[] = $item['product_color'];
            $row[] = $item['price'];
            $row[] = $item['margin'];
            $row[] = $item['sale_off_percent'];
            $row[] = $item['spc_discount'];
            $row[] = $item['invoice_number'];
            $row[] = $item['invoice_price'];
            $row[] = "";
            $row[] = $item['unit_price'];
            $row[] = $item['active_name'];
            $row[] = $item['finance_confirm_by_name'];
            $row[] = $item['admin_create_by_name'];
            $row[] = $item['lot_number'];
            $row[] = $item['cp_date'];
            $row[] = $remark;
            $row[] = $item['timing_date'];
            $row[] = "ບໍ່ສຳເລັດ";
            $row[] = $item['good_id'];
            $row[] = $item['good_color'];

            fputcsv($output, $row);
            unset($item);
            unset($row);

        }

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

    public function deleteCpAutoCheckImeiListAction()
    {
        //print_r($_POST);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $flashMessenger = $this->_helper->flashMessenger;
        if ($this->getRequest()->getMethod() == 'POST')
        {

            $lot_sn         = $this->getRequest()->getParam('lot_sn');
            try{
                $QCPAutoCheckImei = new Application_Model_CPAutoCheckImei();
                $QCPAutoCheckImeiList = new Application_Model_CPAutoCheckImeiList();
                $QCPAutoCheckImeiListLog = new Application_Model_CPAutoCheckImeiListLog();

                $db = Zend_Registry::get('db');
                $db->beginTransaction();

                $whereLotImei = array();
                $whereLotImei[] = $QCPAutoCheckImei->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                $delete_lot_imei = $QCPAutoCheckImei->delete($whereLotImei);
                //print_r($delete_lot_imei );

                $whereLotImeiList = array();
                $whereLotImeiList[] = $QCPAutoCheckImeiList->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                $delete_lot_imei_list = $QCPAutoCheckImeiList->delete($whereLotImeiList);

                $whereLotImeiLog = array();
                $whereLotImeiLog[] = $QCPAutoCheckImeiListLog->getAdapter()->quoteInto('lot_sn = ?', $lot_sn);
                $delete_lot_imei_log = $QCPAutoCheckImeiListLog->delete($whereLotImeiLog);

                if($delete_lot_imei==1)
                {
                    $db->commit();
                    echo 1;
                    $flashMessenger->setNamespace('success')->addMessage('Delete Done!');
                    exit;
                }else{
                    $db->rollback();
                    echo 0;
                    $flashMessenger->setNamespace('error')->addMessage('Cannot Delete Imei Data, Please try again!');
                    exit;
                }

            }catch (Exception $e){
                $db->rollback();
                echo 0;
                $flashMessenger->setNamespace('error')->addMessage('Cannot Delete Imei Data, Please try again!');
                exit;
            } 
        }

    }

    //BoxImeiList
    private function _exportBoxImeiList($result) 
    {
        //print_r($sql);die;
        // this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        //$db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'BoxImeiList - '.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);

        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'Box Number',
            'Sender Name',
            'Post Number',
            'Box Remark',
            'Imei No',
            'Imei Scan Date',
            'Imei Type',
            'Product Name',
            'Product Color',
            'Return Type',
            'Damage Detail',
            'RTN Number',
            'Create CN',
            'Active CN',
            'Warehouse',
            'Store Code',
            'Distributor Name',
            'Invoice Number',
            'Total Amount',
            'Creditnote No',
            'Creditnote Date',
            'Staff Confirm Date',
            'Staff Confirm By',
            'Finance Confirm Date',
            'Finance Confirm By',
            'distributor_id',
            'warehouse_id',
            'good_id',
            'good_color',
            'cn_to_d_id',
            'ร้านลูกตู้(บุญชัย)ที่ได้รับเงิน',
        );

        fputcsv($output, $heads);

        $QDistributor   = new Application_Model_Distributor();
        $distributor_cache = $QDistributor->get_cache2();
        //$result = $db->query($sql);
        $i = 2;

        foreach($result as $item){

            $where_payment = $QDistributor->getAdapter()->quoteInto('id = ?', $item['d_id']);
            $distributors_payment = $QDistributor->fetchRow($where_payment);
            $rank = $distributors_payment->rank;

            $product_qty        = $item['num'];
            if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = 'ສຳນັກງານໃຫຍ່'; }else { $branch_type = 'ສາຂາ'; }
            if ($item['canceled']== 1){$cancel = 'Canceled';}else{$cancel = 'Not Cancel';}

            if ($item['cn_to_d_id'] != '') { 
                $cn_to_d_id_name = $distributor_cache[$item['cn_to_d_id']]['title']; 
            }else { 
                $cn_to_d_id_name = ''; 
            }

            $row = array();
            $row[] = $item['box_number'];
            $row[] = $item['sender_name'];
            $row[] = $item['box_post_number'];
            $row[] = $item['box_remark'];
            $row[] = $item['imei_scan'];
            $row[] = $item['create_date'];
            $row[] = $item['imei_type_name'];
            $row[] = $item['product_name'];
            $row[] = $item['product_color'];
            $row[] = $item['return_type_name'];
            $row[] = $item['damage_detail'];
            $row[] = $item['rtn_number'];
            $row[] = $item['create_cn_name'];
            $row[] = $item['active_name'];
            $row[] = $item['warehouse_name'];
            $row[] = $item['store_code'];
            $row[] = $item['title'];
            $row[] = $item['invoice_number'];
            $row[] = $item['total_amount'];
            $row[] = $item['creditnote_sn'];
            $row[] = $item['creditnote_date'];
            $row[] = $item['staff_confirm_date'];
            $row[] = $item['staff_confirm_by_name'];
            $row[] = $item['finance_confirm_date'];
            $row[] = $item['finance_confirm_by_name'];
            $row[] = $item['distributor_id'];
            $row[] = $item['warehouse_id'];
            $row[] = $item['good_id'];
            $row[] = $item['good_color'];
            $row[] = $item['cn_to_d_id'];
            $row[] = $cn_to_d_id_name;

            fputcsv($output, $row);
            unset($item);
            unset($row);

        }
        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }


    function _exportExcel_stock_product_inout($data, $params)
    {
        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'stock_product_inout_list_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/finance/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'ຊື່ສິນຄ້າ',
            'ສີ',
            'ເລກທີ່ໃບສຳຄັນ',
            'ວັນ ເດືອນ ປີ',
            'ຮັບ',
            'ຈ່າຍ',
            'ຍອດເຫຼືອ',
            'ໝາຍເຫດ'
        );

        fputcsv($output, $heads);

        $i = 2;$chanel="";

        if (isset($data) and $data)
        {
            if($params['good_id']=="all"){
                $product_name="ສິນຄ້າທັງໝົດ";
            }else{
                foreach ($data as $keys=>$m){
                    $product_name= $m['good_code'];
                    if($product_name !=""){
                        continue;
                    }
                }
            }
            
            if($params['good_color']=="all"){
                $product_color="ທຸກສີ";
            }else{
                foreach ($data as $keys=>$m){
                    $product_color= $m['good_color'];
                    if($product_color !=""){
                        continue;
                    }
                }
            }
        }

        foreach($data as $item)
        {
            $qty_in_purchase =0;$qty_in =0;$out_product =0;$qty_ro_purchase =0;
            $qty_ro =0;$qty_do =0;$qty_re =0;
            $qty_in_re=0;$qty_out_re=0;

            /*$qty_in_purchase =0;$qty_in =0;$out_product =0;$qty_ro_purchase =0;

            if($item['doc_type']=='in_purchase'){
                $qty_in =$item['qty_in_purchase'];
            }else if($item['doc_type']=='in_cn'){
                $qty_in =$item['qty_in_cn'];
            }else if($item['doc_type']=='out_product'){
                $out_product =$item['out_product'];
            }else if($item['doc_type']=='Beginning'){
                $qty_in =$item['qty_in_purchase']+$item['qty_in_cn'];
                $out_product =$item['out_product'];
            }*/



            if($item['doc_type']=='in_purchase'){
                $qty_in =$item['qty_in_purchase'];
            }else if($item['doc_type']=='in_cn'){
                $qty_in =$item['qty_in_cn'];
            }else if($item['doc_type']=='in_do'){
                $qty_in =$item['qty_in_do'];
            }else if($item['doc_type']=='in_ro'){
                $qty_in =$item['qty_in_ro'];   
            }else if($item['doc_type']=='in_re'){
                //$qty_in =$item['qty_in_re'];
                if($item['qty_in_re']>=0){
                   $qty_in =$item['qty_in_re']; //บวก
               }        
           }else if($item['doc_type']=='out_product'){
            $out_product =$item['out_product'];
        }else if($item['doc_type']=='Beginning'){

            if($item['qty_in_re']>=0){
                   $qty_in_re =$item['qty_in_re']; //บวก
               }

               if($item['qty_in_re']<0){
                   $qty_out_re =$item['qty_in_re']*-1; // ลบ
               }

               $qty_in =$item['qty_in_purchase']+$item['qty_in_cn']+$item['qty_in_do']+$item['qty_in_ro']+$qty_in_re;
               $out_product =$item['out_product']+$qty_out_re;
           }


           $doc_no_day="";
           if($item['doc_no_day']=='Beginning'){
            $doc_no_day = "ຍອດຍົກມາ";
        }else{
            $doc_no_day = $item['doc_no_day'];
        }


        $total_bal +=($qty_in);
        $total_bal -=$out_product;


        $row = array();
        $row[] = $product_name;
        $row[] = $product_color;
        $row[] = $doc_no_day;
        $row[] = $item['doc_date'];
        $row[] = number_format($qty_in,0);
        $row[] = number_format($out_product,0);
        $row[] = number_format($total_bal,0);
        $row[] = $item['remark'];


        fputcsv($output, $row);
        unset($item);
        unset($row);
    }

    unset($data);

    fclose($output);

    ob_flush();
    ob_start();
    while (@ob_end_flush());

    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;

    $file = fopen($path, 'r');
    $content = fread($file, filesize($path));
    var_dump(filesize($path));
    var_dump($content);

    exit;
}

function _exportExcel_stock_product_balance($data, $params)
{
        //print_r($data);die;
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', '200M');
    $filename = 'stock_product_balance_list_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $file_path = APPLICATION_PATH.'/../public/files/finance/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);
    $path = $file_path.'/'.$filename;
    $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'ລຳດັບທີ',
            'ວັນ ເດືອນ ປີ',
            'ລາຍການ',
            'ຈຳນວນໜ່ວຍ',
            /*'ราคาต่อหน่วย',
            'จำนวนเงิน',*/
            'ໝາຍເຫດ'
        );

        fputcsv($output, $heads);

        $i = 2;$chanel="";

        $no=0;
        foreach($data as $item)
        {

            $total_qty =0;$cost_price =0;$total_cost =0;

            $total_qty =$item['total_qty'];
            $cost_price =$item['cost_price'];
            $total_cost =$item['total_cost'];

            $no +=1;
            $row = array();
            $row[] = $no;
            $row[] = $item['stock_date'];
            $row[] = $item['product_detail'];
            $row[] = number_format($total_qty,0);
            /*$row[] = number_format($cost_price,2);
            $row[] = number_format($total_cost,2);*/
            $row[] = $item['remark'];
            

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }

      public function _exportContactNote($data) {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'Contact_Note_list_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/finance/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

         $heads = array(
            'Code',
            'Distributor(My Side)',
            'Finance Client Code',
            'Finance Client',
            'Distributor(Your Side)',
            'Reconciliation Amount',
            'Reconciliation Details',
            'Business Date',
            'Adjustment Type',
            'Contain Product Is Required',
            'Status',
            'Creator',
            'Creation Time',
            'Finance Date',
            'Review Date',
            'Approved By',
            'Approved At',
        );

        fputcsv($output, $heads);

        $QDistributor = new Application_Model_Distributor();
        $QCostItem = new Application_Model_CostItem();
        $QStaff = new Application_Model_Staff();

        $distributor = $QDistributor->get_cache();
        $costitem = $QCostItem->get_cache();
        $staff = $QStaff->get_cache();

        foreach($data as $item)
        {
            $row = array();
            $row[] = $item['code'];
            $row[] = $distributor[$item['d_id']];
            $row[] = $item['finance_client_code'];
            $row[] = $item['finance_client_name'];
            $row[] = $distributor[$item['dis_y']];
            $row[] = number_format($item['amount'],0);
            $row[] = $item['reconcil_details'];
            $row[] = $item['business_date'];
            $row[] = $costitem[$item['adjust_type']];

            if($item['conatin_product'] == 1) {
                $row[] = 'Yes';
            }else{
                $row[] = 'No';
            }

            if($item['status'] == 1) {
                $status = 'Pending Review';
            }else if($item['status'] == 2) {
                $status = 'Approved';
            }else if($item['status'] == 3) {
                $status = 'Re-review';
            }else if($item['status'] == 4) {
                $status = 'Unapproved';
            }else{
                $status = '-';
            }

            $row[] = $status;
            $row[] = $staff[$item['created_by']];
            $row[] = $item['created_at'];
            $row[] = $item['finance_date'];
            $row[] = $item['review_date'];
            $row[] = $staff[$item['approved_by']];
            $row[] = $item['approved_at'];


            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);

        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;

    }

    public function importCnPriceManualAction()
    {
        //require_once 'finance' . DIRECTORY_SEPARATOR . 'save-creditnote-manual-upload.php';
    }

    public function saveImportCnPriceManualAction()
    {
        require_once 'finance' . DIRECTORY_SEPARATOR . 'save-import-cn-price-manual.php';
    }

    public function loadWarehouseAction()
    {
        $distributor_id = $this->getRequest()->getParam('distributor_id');

        $QDistributor = new Application_Model_Distributor();
        $QWarehouse = new Application_Model_Warehouse();
        $QAccountOrg = new Application_Model_AccountingOrganization();
        $QFinanceWarehouseGroup = new Application_Model_FinanceWarehouseGroup();

        $where = $QDistributor->getAdapter()->quoteInto('id =?',$distributor_id);
        $distributor_row = $QDistributor->fetchRow($where);

        $where2 = $QWarehouse->getAdapter()->quoteInto('id =?',$distributor_row->agent_warehouse_id);
        $warehouse_row = $QWarehouse->fetchAll($where2);

        $where3 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$distributor_id);
        $accountorg_row = $QAccountOrg->fetchAll($where3);

        $where4 = $QFinanceWarehouseGroup->getAdapter()->quoteInto('d_id =?',$distributor_id);
        $warehouse_group = $QFinanceWarehouseGroup->fetchAll($where4);

        echo json_encode(array('warehouse' => $warehouse_row->toArray(),'account' => $accountorg_row->toArray(),'warehouse_group' => $warehouse_group->toArray()));
        exit;


    }

    public function loadDistributorAction()
    {
        $distributor_id = $this->getRequest()->getParam('distributor_id');
        $d_id = $this->getRequest()->getParam('d_id');

        $QDistributor = new Application_Model_Distributor();
        $QFinanceWarehouse = new Application_Model_FinanceWarehouse();
        $QAccountOrg = new Application_Model_AccountingOrganization();

        if($d_id){
            $where = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$d_id);
            $accountorg_row = $QAccountOrg->fetchAll($where);

            echo json_encode(array('account' => $accountorg_row->toArray()));
            exit;

        }else{

           $where = $QDistributor->getAdapter()->quoteInto('id =?',$distributor_id);
           $distributor_row = $QDistributor->fetchRow($where);

           $where2 = $QDistributor->getAdapter()->quoteInto('warehouse_id =?',$distributor_row->agent_warehouse_id);
           $distributor = $QDistributor->fetchAll($where2);

           $where3 = $QFinanceWarehouse->getAdapter()->quoteInto('d_id =?',$distributor_id);
           $financewarehouse = $QFinanceWarehouse->fetchAll($where3);

           $where4 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$distributor_id);
           $accountorg_row = $QAccountOrg->fetchAll($where4);

           echo json_encode(array('distributor' => $distributor->toArray(),'financewarehouse' => $financewarehouse->toArray(),'account' => $accountorg_row->toArray()));
           exit;
       }
   }

   public function loadBankAccountMyAction()
   {
    $distributor_id = $this->getRequest()->getParam('distributor_id');

    $QBankAccountMy = new Application_Model_BankAccountMySide();
    $QStore = new Application_Model_Store();
    $QDistributor = new Application_Model_Distributor();
    $QFinanceClient = new Application_Model_FinanceClient();
    $QWarehouse = new Application_Model_Warehouse();

    if(in_array($distributor_id,array(5969,5970))) {

        $where = $QBankAccountMy->getAdapter()->quoteInto('d_id =?',5970);
        $bankAccount = $QBankAccountMy->fetchAll($where);

    }else{

        $where = $QBankAccountMy->getAdapter()->quoteInto('d_id =?',$distributor_id);
        $bankAccount = $QBankAccountMy->fetchAll($where);

    }

    $where2 = $QDistributor->getAdapter()->quoteInto('id =?',$distributor_id);
    $distributor_arr = $QDistributor->fetchRow($where2);

    $where3 = $QDistributor->getAdapter()->quoteInto('warehouse_id =?',$distributor_arr->agent_warehouse_id);
    $distributor_w = $QDistributor->fetchAll($where3);

        // Loop Array
    foreach($distributor_w as $value) {
        $dis_id[] = $value['id'];
        $w_id[] = $value['agent_warehouse_id'];
    }

    $where4 = $QStore->getAdapter()->quoteInto('d_id IN (?)',$dis_id);
    $store_arr = $QStore->fetchAll($where4);

    $where5 = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$distributor_id);
    $financeClient = $QFinanceClient->fetchAll($where5);

    $where6 = $QWarehouse->getAdapter()->quoteInto('id IN (?)',$w_id);
    $warehouse = $QWarehouse->fetchAll($where6);

    echo json_encode(array('bankaccount' => $bankAccount->toArray(),'store' => $store_arr->toArray(),'financeClient' => $financeClient->toArray(),'warehouse' => $warehouse->toArray()));
    exit;
}

public function loadFinanceAccountByStoreAction()
{
    $store_id = $this->getRequest()->getParam('store_id');

    $QStore = new Application_Model_Store();
    $QFinanceClient = new Application_Model_FinanceClient();
    $QDistributor = new Application_Model_Distributor();
    $QBankAccountMy = new Application_Model_BankAccountMySide();

    $where = $QStore->getAdapter()->quoteInto('id =?',$store_id);
    $store_arr = $QStore->fetchRow($where);

    $where2 = $QFinanceClient->getAdapter()->quoteInto('distributor_y_id =?',$store_arr->d_id);
    $financeClient = $QFinanceClient->fetchAll($where2);

    $where3 = $QDistributor->getAdapter()->quoteInto('id =?',$store_arr->d_id);
    $distributor_arr = $QDistributor->fetchRow($where3);

    $where4 = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$distributor_arr->warehouse_id);
    $supre_distributor = $QDistributor->fetchRow($where4);

    // print_r($store_arr->d_id); exit();

    if(in_array($distributor_arr->warehouse_id,array(36,246))){
        $whare = $QBankAccountMy->getAdapter()->quoteInto('d_id =?',5970);
        $bankaccount = $QBankAccountMy->fetchAll($whare);

        echo json_encode(array('bankaccount' => $bankaccount->toArray(),'financeclient' => $financeClient->toArray(),'distributor' => $distributor_arr->toArray(),'supre_distributor' => $supre_distributor->toArray()));
        exit;

    }else{

        echo json_encode(array('bankaccount' => [],'financeclient' => $financeClient->toArray(),'distributor' => $distributor_arr->toArray(),'supre_distributor' => $supre_distributor->toArray()));
        exit;
    }

}

public function loadFinanceAccountByWarehouseAction()
{
    $warehouse_id = $this->getRequest()->getParam('warehouse_id');

    $QDistributor = new Application_Model_Distributor();
    $QFinanceClient = new Application_Model_FinanceClient();
    $QBankAccountMy = new Application_Model_BankAccountMySide();

    $where = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$warehouse_id);
    $distributor = $QDistributor->fetchRow($where);

    $where2 = $QFinanceClient->getAdapter()->quoteInto('distributor_y_id =?',$distributor->id);
    $financeClient = $QFinanceClient->fetchAll($where2);

    $where3 = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$distributor->warehouse_id);
    $supre_distributor = $QDistributor->fetchRow($where3);

    $where5 = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$distributor->warehouse_id);
    $suppy = $QDistributor->fetchRow($where5);

    $where4 = $QBankAccountMy->getAdapter()->quoteInto('d_id =?',$suppy->id);
    $bankaccount = $QBankAccountMy->fetchAll($where4);

    echo json_encode(array('bankaccount' => $bankaccount->toArray(),'financeclient' => $financeClient->toArray(),'supre_distributor' => $supre_distributor->toArray(),'distributor' => $distributor->toArray()));
    exit;
}

public function loadAccountOrgByFinanceClientAction()
{
    $finance_client = $this->getRequest()->getParam('finance_client');
    $store_id = $this->getRequest()->getParam('store_id');

    $QFinanceClient = new Application_Model_FinanceClient();
    $QAccountOrg = new Application_Model_AccountingOrganization();

    $where = $QFinanceClient->getAdapter()->quoteInto('id =?',$finance_client);
    $financeClient = $QFinanceClient->fetchRow($where);

    $where2 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$financeClient->distributor_y_id);
    $accountOrgYou = $QAccountOrg->fetchRow($where2);

    $where3 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$financeClient->distributor_m_id);
    $accountOrgMy = $QAccountOrg->fetchRow($where3);

    if($store_id) {

        echo json_encode(array('financeClient' => $financeClient->toArray(),'accountOrgMy' => $accountOrgMy->toArray(),'accountOrgYou' => []));
        exit;

    }else{

        echo json_encode(array('financeClient' => $financeClient->toArray(),'accountOrgMy' => $accountOrgMy->toArray(),'accountOrgYou' => $accountOrgYou->toArray()));
        exit;

    }

}

public function loadStoreByDistributorAction()
{
    $distributor_id = $this->getRequest()->getParam('distributor_id');

    $QStore = new Application_Model_Store();
    $QDistributor = new Application_Model_Distributor();
    $QWarehouse = new Application_Model_Warehouse();
    $QFinanceClient = new Application_Model_FinanceClient();
    $QBankAccountMy = new Application_Model_BankAccountMySide();

    $where = $QDistributor->getAdapter()->quoteInto('id =?',$distributor_id);
    $distributor_arr = $QDistributor->fetchRow($where);

    $where2 = $QDistributor->getAdapter()->quoteInto('warehouse_id =?',$distributor_arr->agent_warehouse_id);
    $distributor_w = $QDistributor->fetchAll($where2);

    // Loop Array
    foreach($distributor_w as $value) {
        $dis_id[] = $value['id'];
        $w_id[] = $value['agent_warehouse_id'];
    }

    $where3 = $QStore->getAdapter()->quoteInto('d_id IN (?)',$dis_id);
    $store = $QStore->fetchAll($where3);

    $where5 = $QWarehouse->getAdapter()->quoteInto('id IN (?)',$w_id);
    $warehouse = $QWarehouse->fetchAll($where5);

    $where6 = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$distributor_id);
    $financeClient = $QFinanceClient->fetchAll($where6);

    $where7 = $QBankAccountMy->getAdapter()->quoteInto('d_id =?',$distributor_id);
    $account_my = $QBankAccountMy->fetchAll($where7);


    echo json_encode(array('store' => $store->toArray(),'warehouse' => $warehouse->toArray(),'financeClient' => $financeClient->toArray(),'account_my' => $account_my->toArray()));
    exit;

}

public function loadStoreOrWarehouseByFinanceClientAction()
{
    $finance_client = $this->getRequest()->getParam('finance_client');

    $QStore = new Application_Model_Store();
    $QDistributor = new Application_Model_Distributor();
    $QWarehouse = new Application_Model_Warehouse();
    $QFinanceClient = new Application_Model_FinanceClient();
    $QAccountOrg = new Application_Model_AccountingOrganization();

    $where = $QFinanceClient->getAdapter()->quoteInto('id =?',$finance_client);
    $financeClient = $QFinanceClient->fetchRow($where);

    $where2 = $QDistributor->getAdapter()->quoteInto('id =?',$financeClient->distributor_y_id);
    $distributor = $QDistributor->fetchRow($where2);

    $where5 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$financeClient->distributor_m_id);
    $account_my = $QAccountOrg->fetchRow($where5);

    $where6 = $QAccountOrg->getAdapter()->quoteInto('d_id =?',$financeClient->distributor_y_id);
    $account_you = $QAccountOrg->fetchRow($where6);

    if($distributor->agent_status == 1) {

        $where3 = $QWarehouse->getAdapter()->quoteInto('id =?',$distributor->agent_warehouse_id);
        $warehouse = $QWarehouse->fetchAll($where3);

        echo json_encode(array('warehouse' => $warehouse->toArray(),'account_my' => $account_my->toArray(),'distributor' => $distributor->toArray(),'account_you' => $account_you->toArray()));
        exit;

    }else{

        $where4 = $QStore->getAdapter()->quoteInto('d_id =?',$financeClient->distributor_y_id);
        $store = $QStore->fetchAll($where4);

        echo json_encode(array('store' => $store->toArray(),'account_my' => $account_my->toArray(),'distributor' => $distributor->toArray()));
        exit;
    }



}

public function financeClientWhereWithalAction()
{
    $finance_client = $this->getRequest()->getParam('finance_client');
    $distributor_id = $this->getRequest()->getParam('distributor_id');

    $QContactDetail = new Application_Model_ContactDetail();

    $test = $QContactDetail->getFianaceClientPaymentDetail($finance_client);

    // print_r($test); exit();

    echo json_encode($test);
    exit;
}

public function _exportDistributorReconciliation($data){

    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();
    $heads = array(
        'No',
        'Code',
        'Finance Client',
        'Sale Receipt',
        'Sale Refund',
        'Support Found',
        'Creadit',
        'Outstanding Debt',
        'Order Amount',
        'Available Balance'
    );

    $PHPExcel->setActiveSheetIndex(0);
    $sheet    = $PHPExcel->getActiveSheet();

    $alpha    = 'A';
    $index    = 1;

    $PHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
    $PHPExcel->getDefaultStyle()->getFont()->setSize(14);

    foreach($heads as $key) {
        $sheet->setCellValue($alpha.$index, $key);
        $alpha++;
    }

    $index = 2;
    $i = 1;

    foreach($data as $item) {

        $outstanding_debt  = (($item['total_sales_receipt'] + $item['total_support_payment']) - ($item['total_sales_refund'] + $item['total_sales_order'])) + $item['total_contact_note'];

        if($outstanding_debt > 0) {
            $available_balance = $outstanding_debt;
        }else{
            $available_balance = 0;
        }

        $alpha    = 'A';
        $sheet->setCellValue($alpha++.$index, $i++);
        $sheet->setCellValue($alpha++.$index, $item['code']);
        $sheet->setCellValue($alpha++.$index, $item['name']);
        $sheet->setCellValue($alpha++.$index, number_format($item['total_sales_receipt'],0));
        $sheet->setCellValue($alpha++.$index, number_format($item['total_sales_refund'],0));
        $sheet->setCellValue($alpha++.$index, number_format($item['total_support_payment'],0));
        $sheet->setCellValue($alpha++.$index, number_format($item['total_credit_limit'],0));
        $sheet->setCellValue($alpha++.$index, number_format($outstanding_debt,0));
        $sheet->setCellValue($alpha++.$index, number_format($item['total_sales_order'],0));
        $sheet->setCellValue($alpha++.$index, number_format($available_balance,0));
        $index++;

    }

    foreach (range('A', 'Z') as $columnID) {
        $PHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $filename = 'Distributor_reconciliation_' . strtotime("now");
    $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

    $objWriter->save('php://output');

    exit;

}

public function _exportSaleReceipt($data) {

    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();
    $heads = array(
        'No.',
        'Document No.',
        'Store.',
        'Finance Client',
        'Settlement Type',
        'Bank',
        'Card No.',
        'Transfer Type',
        'Remitted By',
        'Amount',
        'Serial Number',
        'Distributor MY',
        'Distributor Your',
        'Account Org MY',
        'Account Org Your',
        'Creation Date',
        'Creation',
        'Status',
        'Remark',
        'Business Date',
        'Pay Date',
        'Finance Date',
        'Approved At',
        'Approved Remark'
    );

    $PHPExcel->setActiveSheetIndex(0);
    $sheet    = $PHPExcel->getActiveSheet();

    $alpha    = 'A';
    $index    = 1;

    $PHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
    $PHPExcel->getDefaultStyle()->getFont()->setSize(14);


    $QStore = new Application_Model_Store();
    $QWarehouse = new Application_Model_Warehouse();
    $QDistributor = new Application_Model_Distributor();
    $QAccountOrg = new Application_Model_AccountingOrganization();
    $QStaff = new Application_Model_Staff();

    $store = $QStore->get_cache();
    $warehouse = $QWarehouse->get_cache();
    $distributor = $QDistributor->get_cache();
    $accountorg = $QAccountOrg->get_cache();
    $staff = $QStaff->get_cache();

    foreach($heads as $key) {
        $sheet->setCellValue($alpha.$index, $key);
        $alpha++;
    }

    $index = 2;
    $i = 1;

    foreach($data as $item)
    {
        $alpha    = 'A';
        $sheet->setCellValue($alpha++.$index, $i++);
        $sheet->setCellValue($alpha++.$index, $item['document_no']);

        if($item['store_id'] == null) {
            $sheet->setCellValue($alpha++.$index, $warehouse[$item['warehouse_id']]);
        } else {
            $sheet->setCellValue($alpha++.$index, $store[$item['store_id']]);
        }

        $sheet->setCellValue($alpha++.$index, $item['finance_client_name']);
        $sheet->setCellValue($alpha++.$index, $item['smt_name']);
        $sheet->setCellValue($alpha++.$index, $item['bank_account_name']);
        $sheet->setCellValue($alpha++.$index, '="'.$item['card_no'].'"');

        if($item['transfer_type'] == 1) {
            $transfer_type_bank = 'Individual-To-Company';
        }else if($item['transfer_type'] == 2) {
            $transfer_type_bank = 'Individual-To-Individual';
        }else if($item['transfer_type'] ==3) {
            $transfer_type_bank = 'Company-To-Company';
        }

        $sheet->setCellValue($alpha++.$index, $transfer_type_bank);
        $sheet->setCellValue($alpha++.$index, $item['remitted_by']);
        $sheet->setCellValue($alpha++.$index, number_format($item['amount'],2));
        $sheet->setCellValue($alpha++.$index, $item['serial_number']);
        $sheet->setCellValue($alpha++.$index, $distributor[$item['dis_my']]);
        $sheet->setCellValue($alpha++.$index, $distributor[$item['dis_you']]);
        $sheet->setCellValue($alpha++.$index, $accountorg[$item['account_org_you']]);
        $sheet->setCellValue($alpha++.$index, $accountorg[$item['account_org_my']]);
        $sheet->setCellValue($alpha++.$index, $item['created_at']);
        $sheet->setCellValue($alpha++.$index, $staff[$item['created_by']]);

        if($item['status'] == 1) {
            $status_label = 'Pending Review';
        }else if($item['status'] == 2) {
            $status_label = 'Approved';
        }else if($item['status'] == 3) {
            $status_label = 'Re-review';
        }else if($item['status'] == 4) {
            $status_label = 'Unapproved';
        }else{
            $status_label = '-';
        }

        $sheet->setCellValue($alpha++.$index, $status_label);
        $sheet->setCellValue($alpha++.$index, $item['remark']);
        $sheet->setCellValue($alpha++.$index, $item['business_date']);
        $sheet->setCellValue($alpha++.$index, $item['pay_date']);
        $sheet->setCellValue($alpha++.$index, $item['finance_date']);
        $sheet->setCellValue($alpha++.$index, $item['approved_at']);
        $sheet->setCellValue($alpha++.$index, $item['approved_remark']);
        $index++;
    }


    foreach (range('A', 'Z') as $columnID) {
        $PHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $filename = 'Sale_Receipt_' . strtotime("now");
    $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

    $objWriter->save('php://output');

    exit;

}

public function _exportSaleRefund($data) {

    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();
    $heads = array(
        'No.',
        'Document No.',
        'Distributor MY',
        'Store',
        'Financial Customers',
        'Settlement Type',
        'Bank Account',
        'Amount',
        'Status',
        'Business Date',
        'Finance Date',
        'remark',
        'Creator',
        'Creator Date'
    );

    $PHPExcel->setActiveSheetIndex(0);
    $sheet    = $PHPExcel->getActiveSheet();

    $alpha    = 'A';
    $index    = 1;

    $PHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
    $PHPExcel->getDefaultStyle()->getFont()->setSize(14);

    $QDistributor = new Application_Model_Distributor();
    $QWarehouse = new Application_Model_Warehouse();
    $QStore = new Application_Model_Store();
    $QFinanceClient = new Application_Model_FinanceClient();
    $QAccountType = new Application_Model_AccountType();
    $QStaff = new Application_Model_Staff();

    $financeclient = $QFinanceClient->get_cache();
    $accounttype = $QAccountType->get_cache();
    $staff = $QStaff->get_cache();
    $distributor = $QDistributor->get_cache();

    foreach($heads as $key) {
        $sheet->setCellValue($alpha.$index, $key);
        $alpha++;
    }

    $index = 2;
    $i = 1;

    foreach($data as $item)
    {
        $alpha    = 'A';
        $sheet->setCellValue($alpha++.$index, $i++);
        $sheet->setCellValue($alpha++.$index, $item['code']);
        $sheet->setCellValue($alpha++.$index, $distributor[$item['d_id']]);

        if($item['refund_dealer']) {
            $where = $QStore->getAdapter()->quoteInto('id =?',$item['refund_dealer']);
            $store = $QStore->fetchRow($where);

            if($store) {
                $sheet->setCellValue($alpha++.$index, $store->name);
            }else{
                $where = $QWarehouse->getAdapter()->quoteInto('id =?',$item['refund_dealer']);
                $warehouse = $QWarehouse->fetchRow($where);
                $sheet->setCellValue($alpha++.$index, $warehouse->name);
            }
        }

        $sheet->setCellValue($alpha++.$index, $financeclient[$item['finance_client_id']]);
        $sheet->setCellValue($alpha++.$index, $accounttype[$item['refund_type']]);
        $sheet->setCellValue($alpha++.$index, $item['bank_account']);
        $sheet->setCellValue($alpha++.$index, number_format($item['amount'],0));

        if($item['status'] == 1) {
            $status = 'Pending Review';
        }else if($item['status'] == 2) {
            $status = 'Approved';
        }else if($item['status'] == 3) {
            $status = 'Re-review';
        }else{
            $status = 'Unapproved';
        }

        $sheet->setCellValue($alpha++.$index, $status);
        $sheet->setCellValue($alpha++.$index, $item['business_date']);
        $sheet->setCellValue($alpha++.$index, $item['finance_date']);
        $sheet->setCellValue($alpha++.$index, $item['remark']);
        $sheet->setCellValue($alpha++.$index, $staff[$item['created_by']]);
        $sheet->setCellValue($alpha++.$index, $item['created_at']);
        $index++;
    }

    foreach (range('A', 'Z') as $columnID) {
        $PHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $filename = 'Sale_Refund_' . strtotime("now");
    $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

    $objWriter->save('php://output');

    exit;

}

public function _exportFinanceClient($data) {

    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();
    $heads = array(
        'No.',
        'Code',
        'Mnemonic code',
        'Finance Client Name',
        'Distributor My Side',
        'Distributor Your Side',
        'Finance Warehouse',
        'Finance Warehouse Code',
        'Accounting organization(My Side)',
        'Accounting organization(Your Side)',
        'Creator',
        'Remarks',
        'Network',
        'Off/On',
        'Cross - accounting organizations are allowed',
    );

    $PHPExcel->setActiveSheetIndex(0);
    $sheet    = $PHPExcel->getActiveSheet();

    $alpha    = 'A';
    $index    = 1;

    $PHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
    $PHPExcel->getDefaultStyle()->getFont()->setSize(14);

    $QStaff = new Application_Model_Staff();
    $staff = $QStaff->get_cache();

    foreach($heads as $key) {
        $sheet->setCellValue($alpha.$index, $key);
        $alpha++;
    }

    $index = 2;
    $i = 1;

    foreach($data as $item)
    {

        if($item['network'] == 1) {
            $network = 'All Network';
        }else{
            $network = '-';
        }

        if($item['status'] == 1) {
            $status = 'ON';
        }else{
            $status = 'Off';
        }

        if($item['cross_account'] == 1) {
            $cross_account = "Yes";
        }else{
            $cross_account = "No";
        }

        $alpha    = 'A';
        $sheet->setCellValue($alpha++.$index, $i++);
        $sheet->setCellValue($alpha++.$index, $item['code']);
        $sheet->setCellValue($alpha++.$index, $item['mnemonic_code']);
        $sheet->setCellValue($alpha++.$index, $item['name']);
        $sheet->setCellValue($alpha++.$index, $item['distributor_my_side']);
        $sheet->setCellValue($alpha++.$index, $item['distributor_your_side']);
        $sheet->setCellValue($alpha++.$index, $item['finance_warehouse_name']);
        $sheet->setCellValue($alpha++.$index, $item['finance_warehouse_code']);
        $sheet->setCellValue($alpha++.$index, $item['account_org_my']);
        $sheet->setCellValue($alpha++.$index, $item['account_org_your']);
        $sheet->setCellValue($alpha++.$index, $staff[$item['created_by']]);
        $sheet->setCellValue($alpha++.$index, $item['remark']);
        $sheet->setCellValue($alpha++.$index, $network);
        $sheet->setCellValue($alpha++.$index, $status);
        $sheet->setCellValue($alpha++.$index, $cross_account);
        $index++;

    }

    foreach (range('A', 'Z') as $columnID) {
        $PHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $filename = 'Finance_client_' . strtotime("now");
    $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

    $objWriter->save('php://output');

    exit;

}

public function _exportAccountingOrganization($data) {

    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();
    $heads = array(
        'No.',
        'Code',
        'Mnemonic code',
        'Accounting Organization Name',
        'Identification No.',
        'Corporation Type.',
        'Creation',
        'Creation Time.',
        'Remarks.',
        'On/Off'
    );

    $PHPExcel->setActiveSheetIndex(0);
    $sheet    = $PHPExcel->getActiveSheet();

    $alpha    = 'A';
    $index    = 1;

    $PHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
    $PHPExcel->getDefaultStyle()->getFont()->setSize(14);

    $QStaff = new Application_Model_Staff();
    $staff = $QStaff->get_cache();

    foreach($heads as $key) {
        $sheet->setCellValue($alpha.$index, $key);
        $alpha++;
    }

    $index = 2;
    $i = 1;

    foreach($data as $item)
    {

        if($item['corporation_type'] == 1) {
            $corporation_type = 'Corporation';
        }else if($item['corporation_type'] == 2) {
            $corporation_type = 'Profit Center';
        }else{
            $corporation_type = '-';
        }

        if($item['status'] == 1) {
            $status = 'On';
        }else{
            $status = 'Off';
        }

        $alpha    = 'A';
        $sheet->setCellValue($alpha++.$index, $i++);
        $sheet->setCellValue($alpha++.$index, $item['code']);
        $sheet->setCellValue($alpha++.$index, $item['mnemonic_code']);
        $sheet->setCellValue($alpha++.$index, $item['name']);
        $sheet->setCellValue($alpha++.$index, $item['identification_no']);
        $sheet->setCellValue($alpha++.$index, $corporation_type);
        $sheet->setCellValue($alpha++.$index, $staff[$item['created_by']]);
        $sheet->setCellValue($alpha++.$index, $item['created_at']);
        $sheet->setCellValue($alpha++.$index, $item['remark']);
        $sheet->setCellValue($alpha++.$index, $status);
        $index++;


    }

    foreach (range('A', 'Z') as $columnID) {
        $PHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $filename = 'Accounting_Organization_' . strtotime("now");
    $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

    $objWriter->save('php://output');

    exit;
}

public function _exportFinanceWarehouse($data) {

    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();
    $heads = array(
        'No.',
        'Code',
        'Mnemonic code',
        'Finance Warehouse Name',
        'Account Org',
        'Finance Warehouse Group',
        'App Consigment',
        'Warehouse Name',
        'On/Off',
        'Creator',
        'Creation Time',
        'remark'
    );

    $PHPExcel->setActiveSheetIndex(0);
    $sheet    = $PHPExcel->getActiveSheet();

    $alpha    = 'A';
    $index    = 1;

    $PHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
    $PHPExcel->getDefaultStyle()->getFont()->setSize(14);

    $QStaff = new Application_Model_Staff();
    $staff = $QStaff->get_cache();

    foreach($heads as $key) {
        $sheet->setCellValue($alpha.$index, $key);
        $alpha++;
    }

    $index = 2;
    $i = 1;

    foreach($data as $item)
    {

        if($item['app_consigment'] == 1) {
            $app_consigment = 'Yes';
        }else{
            $app_consigment = 'No';
        }

        if($item['status'] == 1) {
            $status = 'On';
        }else{
            $status = 'Off';
        }

        $alpha    = 'A';
        $sheet->setCellValue($alpha++.$index, $i++);
        $sheet->setCellValue($alpha++.$index, $item['code']);
        $sheet->setCellValue($alpha++.$index, $item['mnemonic_code']);
        $sheet->setCellValue($alpha++.$index, $item['name']);
        $sheet->setCellValue($alpha++.$index, $item['account_org_name']);
        $sheet->setCellValue($alpha++.$index, $item['finance_wh_group_name']);
        $sheet->setCellValue($alpha++.$index, $app_consigment);
        $sheet->setCellValue($alpha++.$index, $item['warehouse_name']);
        $sheet->setCellValue($alpha++.$index, $status);
        $sheet->setCellValue($alpha++.$index, $staff[$item['created_by']]);
        $sheet->setCellValue($alpha++.$index, $item['created_at']);
        $sheet->setCellValue($alpha++.$index, $item['remark']);
        $index++;

    }

    foreach (range('A', 'Z') as $columnID) {
        $PHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $filename = 'Finance_Warehouse_' . strtotime("now");
    $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

    $objWriter->save('php://output');

    exit;
}

public function _exportFinanceWarehouseGroup($data) {

    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();
    $heads = array(
        'No.',
        'Finance Warehouse Group Name',
        'Distibutor ID',
        'Distributor Name',
        'Warehouse Name',
        'On/Off',
        'Creator',
        'Creation Time',
        'Remark'
    );

    $PHPExcel->setActiveSheetIndex(0);
    $sheet    = $PHPExcel->getActiveSheet();

    $alpha    = 'A';
    $index    = 1;

    $PHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
    $PHPExcel->getDefaultStyle()->getFont()->setSize(14);

    $QStaff = new Application_Model_Staff();
    $staff = $QStaff->get_cache();

    foreach($heads as $key) {
        $sheet->setCellValue($alpha.$index, $key);
        $alpha++;
    }

    $index = 2;
    $i = 1;

    foreach($data as $item)
    {
        if($item['status'] == 1) {
            $status = 'On';
        }else{
            $status = 'Off';
        }

        $alpha    = 'A';
        $sheet->setCellValue($alpha++.$index, $i++);
        $sheet->setCellValue($alpha++.$index, $item['group_name']);
        $sheet->setCellValue($alpha++.$index, $item['d_id']);
        $sheet->setCellValue($alpha++.$index, $item['distributor_id']);
        $sheet->setCellValue($alpha++.$index, $item['warehouse_name']);
        $sheet->setCellValue($alpha++.$index, $status);
        $sheet->setCellValue($alpha++.$index, $staff[$item['created_by']]);
        $sheet->setCellValue($alpha++.$index, $item['created_at']);
        $sheet->setCellValue($alpha++.$index, $item['remark']);
        $index++;

    }

    foreach (range('A', 'Z') as $columnID) {
        $PHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $filename = 'Finance_Warehouse_Group_' . strtotime("now");
    $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

    $objWriter->save('php://output');

    exit;

}

public function _exportCostItem($data) {

    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();
    $heads = array(
        'No.',
        'Cost Name',
        'Remind Code',
        'Subject Code',
        'Category',
        'On/Off',
        'Remark',
        'Creator',
        'Creation Time'
    );

    $PHPExcel->setActiveSheetIndex(0);
    $sheet    = $PHPExcel->getActiveSheet();

    $alpha    = 'A';
    $index    = 1;

    $PHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
    $PHPExcel->getDefaultStyle()->getFont()->setSize(14);

    $QStaff = new Application_Model_Staff();
    $staff = $QStaff->get_cache();

    foreach($heads as $key) {
        $sheet->setCellValue($alpha.$index, $key);
        $alpha++;
    }

    $index = 2;
    $i = 1;

    foreach($data as $item)
    {

        if($item['category_id'] == 1) {
            $category = 'ADJUST';
        }else if($item['category_id'] == 2) {
            $category = 'DEPOSIT';
        }else if($item['category_id'] == 3) {
            $category = 'DOWN_PAYMENT';
        }else if($item['category_id'] == 4) {
            $category = 'OTHERS';
        }else{
            $category = '-';
        }


        if($item['status'] == 1) {
            $status = 'On';
        }else{
            $status = 'Off';
        }


        $alpha    = 'A';
        $sheet->setCellValue($alpha++.$index, $i++);
        $sheet->setCellValue($alpha++.$index, $item['cost_name']);
        $sheet->setCellValue($alpha++.$index, $item['remind_code']);
        $sheet->setCellValue($alpha++.$index, $item['subject_code']);
        $sheet->setCellValue($alpha++.$index, $category);
        $sheet->setCellValue($alpha++.$index, $status);
        $sheet->setCellValue($alpha++.$index, $item['remark']);
        $sheet->setCellValue($alpha++.$index, $staff[$item['created_by']]);
        $sheet->setCellValue($alpha++.$index, $item['created_at']);
        $index++;

    }

    foreach (range('A', 'Z') as $columnID) {
        $PHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $filename = 'Cost_Item_' . strtotime("now");
    $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

    $objWriter->save('php://output');

    exit;

}

public function _exportBankAccountMy($data) {

    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();
    $heads = array(
        'No.',
        'Code',
        'Mnemonic Code',
        'Bank Account',
        'Bank Card Number',
        'Bank',
        'Card Type',
        'Account Org',
        'Host',
        'Account Property',
        'Account Type',
        'Status',
        'Remark',
        'Creation',
        'Creation Time'
    );

    $PHPExcel->setActiveSheetIndex(0);
    $sheet    = $PHPExcel->getActiveSheet();

    $alpha    = 'A';
    $index    = 1;

    $PHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
    $PHPExcel->getDefaultStyle()->getFont()->setSize(14);

    $QStaff = new Application_Model_Staff();
    $staff = $QStaff->get_cache();

    foreach($heads as $key) {
        $sheet->setCellValue($alpha.$index, $key);
        $alpha++;
    }

    $index = 2;
    $i = 1;

    foreach($data as $item)
    {

        if($item['card_type'] == 1) {
            $card_type = 'Major Card';
        }else if($item['card_type'] == 2) {
            $card_type = 'Deputy Card';
        }else if($item['card_type'] == 3) {
            $card_type = 'Virtual Card';
        }else{
            $card_type = '-';
        }


        if($item['host'] == 1) {
            $host = 'Yes';
        }else if($item['host'] == 2) {
            $host = 'No';
        }else{
            $host = '-';
        }


        if($item['account_pp'] == 1){
            $account_pp = 'Personal Account';
        }else if($item['account_pp'] == 2){
            $account_pp = 'Company Account';
        }else {
            $account_pp = '-';
        }

        if($item['status'] == 1) {
            $status = 'In Use';
        }else if($item['status'] == 2) {
            $status = 'Not Use';
        }else if($item['status'] == 3) {
            $status = 'Close';
        }else{
            $status = '-';
        }

        $alpha    = 'A';
        $sheet->setCellValue($alpha++.$index, $i++);
        $sheet->setCellValue($alpha++.$index, $item['code']);
        $sheet->setCellValue($alpha++.$index, $item['mnemonic_code']);
        $sheet->setCellValue($alpha++.$index, $item['bank_account']);
        $sheet->setCellValue($alpha++.$index, '="'.$item['card_no']. '"');
        $sheet->setCellValue($alpha++.$index, $item['bank_name']);
        $sheet->setCellValue($alpha++.$index, $card_type);
        $sheet->setCellValue($alpha++.$index, $item['account_org_name']);
        $sheet->setCellValue($alpha++.$index, $host);
        $sheet->setCellValue($alpha++.$index, $account_pp);
        $sheet->setCellValue($alpha++.$index, $item['account_type_name']);
        $sheet->setCellValue($alpha++.$index, $status);
        $sheet->setCellValue($alpha++.$index, $item['remark']);
        $sheet->setCellValue($alpha++.$index, $staff[$item['created_by']]);
        $sheet->setCellValue($alpha++.$index, $item['created_at']);
        $index++;
        
    }

    foreach (range('A', 'Z') as $columnID) {
        $PHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $filename = 'Bank_Account_My_' . strtotime("now");
    $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

    $objWriter->save('php://output');

    exit;
}

public function _exportClientContactNote($data) {

 require_once 'PHPExcel.php';
 $PHPExcel = new PHPExcel();
 $heads = array(
    'No.',
    'Document No',
    'Finance Client',
    'Store',
    'Document Type',
    'Amount',
    'Description',
    'Business Date',
    'Creation Time',
    'Creator',
    'Remarks'
);

 $PHPExcel->setActiveSheetIndex(0);
 $sheet    = $PHPExcel->getActiveSheet();

 $alpha    = 'A';
 $index    = 1;

 $PHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
 $PHPExcel->getDefaultStyle()->getFont()->setSize(14);

 $QDistributor = new Application_Model_Distributor();
 $QStore = new Application_Model_Store();
 $QStaff = new Application_Model_Staff();

 $distributor = $QDistributor->get_cache();
 $store = $QStore->get_cache();
 $staff = $QStaff->get_cache();

 foreach($heads as $key) {
    $sheet->setCellValue($alpha.$index, $key);
    $alpha++;
}

$index = 2;
$i = 1;

foreach($data as $item)
{
    $alpha    = 'A';
    $sheet->setCellValue($alpha++.$index, $i++);

    if($item['sn_ref']) {
        $sheet->setCellValue($alpha++.$index, $item['sn_ref']);
    }else{
        $sheet->setCellValue($alpha++.$index, $item['doc_no']);
    }

    $sheet->setCellValue($alpha++.$index, $item['finance_client_name']);

    if($item['store_id']){
        $sheet->setCellValue($alpha++.$index, $store[$item['store_id']]);
    }else{
        $sheet->setCellValue($alpha++.$index, $distributor[$item['d_id']]);
    }

    $sheet->setCellValue($alpha++.$index, $item['document_type_name']);
    $sheet->setCellValue($alpha++.$index, number_format($item['amount'],0));
    $sheet->setCellValue($alpha++.$index, $item['description']);
    $sheet->setCellValue($alpha++.$index, $item['bill_date']);
    $sheet->setCellValue($alpha++.$index, $item['created_at']);
    $sheet->setCellValue($alpha++.$index, $staff[$item['created_by']]);
    $sheet->setCellValue($alpha++.$index, $item['remark']);
    $index++;
}

foreach (range('A', 'Z') as $columnID) {
    $PHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}

$filename = 'Client_Contact_Note_' . strtotime("now");
$objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

$objWriter->save('php://output');

exit;

}


}
