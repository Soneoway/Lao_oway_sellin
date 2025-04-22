<?php
include('SdkJD.php');
class SalesController extends My_Controller_Action
{

//--------Pre-Sales---------//

    public function savePreSalesOrderListAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-pre-sales-order-list.php';
    }

    public function preSalesOrderListViewAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'pre-sales-order-list-view.php';
    }

    public function preSalesOrderListAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'pre-sales-order-list.php';
    }

// Client Management Add By Khuan //

    public function clientManagementAction()
    {
        require_once 'sales'.DIRECTORY_SEPARATOR.'client'.DIRECTORY_SEPARATOR.'client-management.php';
    }

    public function addClientAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'client' . DIRECTORY_SEPARATOR . 'add-client.php';
    }

    public function saveClientAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'client' . DIRECTORY_SEPARATOR . 'save-client.php';
    }

    public function clientDetailAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'client' . DIRECTORY_SEPARATOR . 'client-detail.php';
    }

    public function freezeClientAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR .'client' . DIRECTORY_SEPARATOR . 'freeze-client.php';
    }

   // End Client Management //

   // Start  Distributor Management add By Khuan //

    public function distributorManagementAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-management' . DIRECTORY_SEPARATOR . 'distributor-management.php';
    }

    public function addDistributorAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-management' . DIRECTORY_SEPARATOR . 'create-distributor.php';
    }

    public function saveDistributorNewAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-management' . DIRECTORY_SEPARATOR . 'save-distributor.php';
    }

    public function distributorDetailAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-management' . DIRECTORY_SEPARATOR . 'detail-distributor.php';
    }

    public function distributorEventAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-management' . DIRECTORY_SEPARATOR . 'distributor-event.php';
    }


   // End Distributor Management //

    public function preSalesCreateSalesOrderAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'pre-sales-create-sales-order.php';
    }

    public function viewInvoicePdfFileAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $data_sn = $this->getRequest()->getParam('data_sn');
        $QEDT = new Application_Model_EtaxDocumentTran();
        $res = $QEDT->view_invoice_pdf_file($data_sn);
        echo $res;
    }

    public function epPrivilegesCheckStaffPositionAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'ep-privileges-check-staff-position.php';
    }

//khuan
    public function delChangeAction()
    {
        $flashMessenger = $this->_helper->flashMessenger;

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam('id');

        $Qchange = new Application_Model_ChangeImeiDistibutor();
        $where = $Qchange->getAdapter()->quoteInto('id =?',$id);
// $Qchange->delete($where);
        $change = $Qchange->fetchRow($where);
        $imei = $change['imei_sn'];
        $data2 = array(
            'distributor_id' => $change['old_distibutor'],
        );

        $QImei = new Application_Model_Imei();
        $where2 = $QImei->getAdapter()->quoteInto('imei_sn =?',$imei);
        $updatechange = $QImei->update($data2,$where2);

        if($updatechange > 0){
            $Qchange = new Application_Model_ChangeImeiDistibutor();
            $where3 = $Qchange->getAdapter()->quoteInto('imei_sn =?',$imei);
            $Qchange->delete($where3);
        }


        $flashMessenger->setNamespace('success')->addMessage('Done!');
        $this->_redirect('/sales/change-list');

    }

//edit khuan
    public function changeListAction()
    {
        require_once 'sales'.DIRECTORY_SEPARATOR.'changeList.php';
    }

    public function addChangeAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR.'add-change.php';
    }

    public function saveChangeAction()
    {
        require_once 'sales' .DIRECTORY_SEPARATOR. 'save-change.php';
    }

// Compare dealer shop for placing an order
    public function distributorOrderAction()
    {
        require_once 'sales'. DIRECTORY_SEPARATOR . 'distributor-order.php';
    }

//---------------------swal&modal
    public function distributorSwalAction()
    {
        require_once 'sales'. DIRECTORY_SEPARATOR . 'distributor-swal.php';
    }

    public function distributorViewAction()
    {
        require_once 'sales'. DIRECTORY_SEPARATOR . 'distributor-view.php';
    }

//end

    public function saveCreateStaffSalesOrderAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-create-staff-sales-order.php';
    }

    public function saveStaffSalesOrderListAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-staff-sales-order-list.php';
    }

    public function staffSalesOrderListViewAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'staff-sales-order-list-view.php';
    }

    public function staffSalesOrderListAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'staff-sales-order-list.php';
    }

    public function saveWhtManualUploadListAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-wht-manual-upload-list.php';
    }

    public function whtManualUploadListAction()
    {
//require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
    }

    public function whtManualPrintListAction()
    {
//require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
    }

    public function printWithholdingTaxAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'print-withholding-tax.php';
    }

    public function saveImportConfirmRebateOpenMarketAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-import-confirm-rebate-open-market.php';
    }

    public function importConfirmRebateOpenMarketAction()
    {

    }

    public function cpAutoCheckImeiAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'cp-auto-check-imei.php';
    }

    public function saveCpAutoCheckImeiAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-cp-auto-check-imei.php';
    }

    public function indexAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'index.php';
    }

    public function returnBoxNumberImeiAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'return-box-number-imei.php';
    }

    public function returnBoxNumberImeiCheckAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'return-box-number-imei-check.php';
    }

    public function saveReturnBoxNumberImeiCheckAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-return-box-number-imei-check.php';
    }

    public function saveReturnBoxNumberImeiAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-return-box-number-imei.php';
    }

    public function returnAutoCheckAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'return-auto-check.php';
    }

    public function saveReturnAutoCheckAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-return-auto-check.php';
    }

    public function returnAutoScanAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'return-auto-scan.php';
    }

    public function saveReturnAutoScanAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-return-auto-scan.php';
    }

    public function stockAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'stock.php';
    }

    public function saveCreateAutoSoAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-create-auto-so.php';
    }

    public function createAutoSoAction()
    {

    }

    public function stockStorageAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'stock-storage.php';
    }

    public function createAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create.php';
    }

    public function createDigitalAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-digital.php';
    }

    public function orderDetailAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'order-detail.php';
    }

    public function createServiceAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-service.php';
    }

    public function createAccessoriesAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-accessories.php';
    }

    public function indexAccessoriesAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'index-accessories.php';
    }

    public function createStockAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-stock.php';
    }

    public function saveStockAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-stock.php';
    }

    public function createExcelAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-excel.php';
    }

    public function createSimAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-sim.php';
    }

    public function createTgddAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-tgdd.php';
    }

    public function viewAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'view.php';
    }

    public function viewStockAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'view-stock.php';
    }

    public function saveAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save.php';
    }

    public function saveServiceAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save.php';
    }

    public function saveExcelAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-excel.php';
    }

    public function saveSimAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-sim.php';
    }

    public function saveTgddAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-tgdd.php';
    }

    public function mouLogAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'mou-log.php';
    }

    public function returnAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'return.php';
    }

    public function returnViewAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'return-view.php';
    }

    public function saveReturnAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-return.php';
    }

    public function distributorAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor.php';
    }

    public function distributorMassUploadAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-mass-upload.php';
    }

    public function distributorMassUploadSaveAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-mass-upload-save.php';
    }

    public function distributorMassUploadVtaAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-mass-upload-vta.php';
    }

    public function distributorMassUploadVtaSaveAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-mass-upload-vta-save.php';
    }

    public function createDistributorAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-distributor.php';
    }
    public function shippingAddressAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'shipping-address.php';
    }
    public function addShippingAddressAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'add-shipping-address.php';
    }
    public function saveDistributorAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-distributor.php';
    }

    public function deleteDistributorAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'delete-distributor.php';
    }

    public function undeleteDistributorAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'undelete-distributor.php';
    }

    public function suspendCooperationAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'suspend-cooperation.php';
    }

    public function reCooperationAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 're-cooperation.php';
    }

    public function returnListAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'return-list.php';
    }

    public function delAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'del.php';
    }

    public function del2Action()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'del2.php';
    }

    public function delStockAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'del-stock.php';
    }

    public function checkListCreditNoteAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'check-list-cn.php';
    }

    public function printSaleAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'print-sale.php';
    }

    public function printSaleExpireAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'print-sale-expire.php';
    }

    public function printOpenMarketAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'print-open-market.php';
    }

    public function targetAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'target.php';
    }

    public function targetSaveAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'target-save.php';
    }

    public function targetCheckAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'target-check.php';
    }

    public function targetViewAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'target-view.php';
    }

    public function targetUpdateAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'target-update.php';
    }

    public function delPoAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'del-po.php';
    }

    public function createPaymentGroupAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-payment-group.php';
    }

    public function showPaymentGroupAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'show-payment-group.php';
    }

    public function massDistributorOnlineAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'mass-distributor-online.php';
    }
/**
* List of model and quantity to check price protection
* @return [type] [description]
*/
public function priceProtectionAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'price-protection.php';
}

/**
* TÃƒÂ¡ch don hÃƒÂ ng thÃƒÂ nh 2 don nh? - (gi? m?t ph?n don g?c + t?o 1 don m?i)
* @return [type] [description]
*/
public function splitAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'split.php';
}

/**
* TÃƒÂ¡ch don hÃƒÂ ng thÃƒÂ nh 2 don nh? - (gi? m?t ph?n don g?c + t?o 1 don m?i)
* @return [type] [description]
*/
public function splitGetOrderAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'split-get-order.php';
}

/**
* TÃƒÂ¡ch don hÃƒÂ ng thÃƒÂ nh 2 don nh? - (gi? m?t ph?n don g?c + t?o 1 don m?i)
* @return [type] [description]
*/
public function splitActAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'split-act.php';
}

public function memberBrandshopAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'member-brandshop.php';
}

public function createMemberBrandshopAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'create-member-brandshop.php';
}

public function saveMemberBrandshopAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'save-member-brandshop.php';
}

public function deleteMemberBrandshopAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'delete-member-brandshop.php';
}
public function createDocumentAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'create-document.php';
}
public function distributorDocumentAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-document.php';
}

public function distributorApprovalAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-approval.php';
}
// start : add ice
public function saveCreateBigcAutoSoAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'save-create-bigc-auto-so.php';
}
public function createBigcAutoSoAction()
{

}

public function createImportMapKeyAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'create-import-map-key.php';
}

public function createOrderStaffDeductionAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'create-order-staff-deduction.php';
}
public function saveInsertOrderAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'save-insert-order.php';
}

public function getShippingaddressByStaffcodeAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'get-shippingaddress-by-staffcode.php';
}
public function listMapKeyproductAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'list-map-keyproduct.php';
}
public function closeDistributorAction()
{
    require_once 'sales' . DIRECTORY_SEPARATOR . 'close-distributor.php'; 
}
public function createCloseDistributorAction()
{

}

// end : add ice

public function getCreateMonthNo_Ref($db,$doc_type,$sn)
{
    $flashMessenger = $this->_helper->flashMessenger;
    $sn_ref="";
    try {
        $stmt = $db->prepare("CALL gen_month_running_no_ref('".$doc_type."',".$sn.")");
        $stmt->execute();
        $result = $stmt->fetch();
        $sn_ref = $result['running_no'];
    }catch (exception $e) {
        $flashMessenger->setNamespace('error')->addMessage($e);
    }
    return $sn_ref;
}

/**
* Function saveAPI
* Action sales/save vÃƒÂ  sales/save-excel s? dÃƒÂ¹ng chung API nÃƒÂ y
*     d? thu?n ti?n vi?c nh?p vÃƒÂ  tr? d? li?u
* @param  array  $params - m?ng t?t c? cÃƒÂ¡c tham s? c?n thi?t
* @return array
*/
private function saveAPI($params = array())
{   
// echo "<pre>";
//print_r($params);die;
// echo $this->getRequest()->getParam('sn');
$saleoff_price        = isset($params['sale_off_price']) ? $params['sale_off_price']:null;  // Add sale off price unit
$market_general_id    = isset($params['market_general_id']) ? $params['market_general_id'] : null;
$ids                  = isset($params['ids']) ? $params['ids'] : null;
$cat_ids              = isset($params['cat_id']) ? $params['cat_id'] : null;
$good_ids             = isset($params['good_id']) ? $params['good_id'] : null;
$good_colors          = isset($params['good_color']) ? $params['good_color'] : null;
$nums                 = isset($params['num']) ? $params['num'] : null;
$save_service         = isset($params['save_service']) ? $params['save_service'] : null;
$prices               = isset($params['price']) ? $params['price'] : null;
$totals               = isset($params['total']) ? $params['total'] : null;
$texts                = isset($params['text']) ? $params['text'] : null;
$distributor_id       = isset($params['distributor_id']) ? $params['distributor_id'] : null;
$warehouse_id         = isset($params['warehouse_id']) ? $params['warehouse_id'] : null;
$salesman             = isset($params['salesman']) ? $params['salesman'] : null;
$sales_catty_id       = isset($params['sales_catty_id']) ? $params['sales_catty_id'] : null;
$type                 = isset($params['type']) ? $params['type'] : null;
$sale_off_percent     = isset($params['sale_off_percent']) ? $params['sale_off_percent'] : null;
$sn                   = isset($params['sn']) ? $params['sn'] : null;
$sn_ref               = isset($params['sn_ref']) ? $params['sn_ref'] : null;
$isbatch              = isset($params['isbatch']) ? $params['isbatch'] : null;
$life_time            = isset($params['life_time']) ? $params['life_time'] : null;
$service_id           = isset($params['service_id']) ? $params['service_id'] : null;
$ids_bvg              = isset($params['ids_bvg']) ? $params['ids_bvg'] : null;
$good_ids_bvg         = isset($params['good_id_bvg']) ? $params['good_id_bvg'] : null;
$nums_bvg             = isset($params['num_bvg']) ? $params['num_bvg'] : null;
$prices_bvg           = isset($params['price_bvg']) ? $params['price_bvg'] : null;
$totals_bvg           = isset($params['total_bvg']) ? $params['total_bvg'] : null;
$joint                = isset($params['joint']) ? $params['joint'] : null;
$ids_discount         = isset($params['ids_discount']) ? $params['ids_discount'] : null;
$joint_discount       = isset($params['joint_discount']) ? $params['joint_discount'] : null;
$prices_discount      = isset($params['prices_discount']) ? $params['prices_discount'] : null;
$bvg_imei             = isset($params['bvg_imei']) ? $params['bvg_imei'] : null;
$userStorage          = Zend_Auth::getInstance()->getStorage()->read();
$distributor_po       = isset($params['distributor_po']) ? $params['distributor_po'] : null;
$invoice              = isset($params['invoice_data']) ? $params['invoice_data'] : null;
$gift_id              = isset($params['gift_id']) ? $params['gift_id'] : null;
$include_shipping_fee = isset($params['include_shipping_fee']) ? $params['include_shipping_fee'] : 0;
$user_uncheck         = isset($params['user_uncheck']) ? $params['user_uncheck'] : 0;
$campaign             = isset($params['campaign']) ? $params['campaign'] : null;
$payment_method       = isset($params['payment_method']) ? $params['payment_method'] : null;
$invoice_number_data  = isset($params['invoice_number_data']) ? $params['invoice_number_data'] : null;

$currentTime          = date('Y-m-d H:i:s');

//ThÃƒÂ´ng tin nhÃƒÂ¢n viÃƒÂªn mua mÃƒÂ¡y
$market_general_data  = isset($params['market_general_data']) ? $params['market_general_data'] : null;
$id_staffs            = isset($params['id_staff']) ? $params['id_staff'] : null;
$cmnd_staff_ingames   = isset($params['cmnd_staff_ingame']) ? $params['cmnd_staff_ingame'] : null;
$product_color_keys   = isset($params['product_color_key']) ? $params['product_color_key'] : null;
$staff_nums           = isset($params['staff_num']) ? $params['staff_num'] : null;
$shipment_id          = isset($params['shipment_id']) ? $params['shipment_id'] : null;
$for_partner          = isset($params['for_partner']) ? $params['for_partner'] : NULL;

//Tanong Add New credit_id 2016/02/26
$credit_id            = isset($params['credit_id']) ? $params['credit_id'] : NULL;
$creditnote_data      = isset($params['creditnote_data']) ? $params['creditnote_data'] : null;

//Tanong Add New credit_id 2016/02/26
$deposit_id            = isset($params['deposit_id']) ? $params['deposit_id'] : NULL;
$deposit_data          = isset($params['deposit_data']) ? $params['deposit_data'] : null;

//Tanong Delivery Address
$delivery_address     = isset($params['delivery_address']) ? $params['delivery_address'] : null;
$add_delivery_new     = $this->getRequest()->getParam('add_delivery_new');
//echo $add_delivery_new;
//Tanong Delivery Fee
$delivery_fee           = isset($params['delivery_fee']) ? $params['delivery_fee'] : null;
$customer_id            = isset($params['customer_id']) ? $params['customer_id'] : null;
$customer_name          = isset($params['customer_name']) ? $params['customer_name'] : null;
$customer_tax_number    = isset($params['customer_tax_number']) ? $params['customer_tax_number'] : null;
$customer_phone_number    = isset($params['customer_phone_number']) ? $params['customer_phone_number'] : null;
$customer_branch_number    = isset($params['customer_branch_number']) ? $params['customer_branch_number'] : null;
$customer_tax_address   = isset($params['customer_tax_address']) ? $params['customer_tax_address'] : null;
$customer_zip_code   = isset($params['customer_zip_code']) ? $params['customer_zip_code'] : null;
$rank                   = isset($params['rank']) ? $params['rank'] : null;

$order_accessories      = isset($params['order_accessories']) ? $params['order_accessories'] : null;
$edit                   = isset($params['edit']) ? $params['edit'] : null;
$sipping_add            = isset($params['sipping_add']) ? $params['sipping_add'] : null;
$total_spc_discount     = isset($params['total_spc_discount']) ? $params['total_spc_discount'] : 0;

$customer_name_for_staff= isset($params['customer_name_for_staff']) ? $params['customer_name_for_staff'] : null;
$digital_discount       = isset($params['digital_discount']) ? $params['digital_discount'] : null;
$job_sn                 = isset($params['job_sn']) ? $params['job_sn'] : null;
$job_type               = isset($params['job_type']) ? $params['job_type'] : null;

$group_type_id_post               = isset($params['group_type_id_post']) ? $params['group_type_id_post'] : null;
$member_brandshop_code               = isset($params['member_brandshop_code']) ? $params['member_brandshop_code'] : null;
$bs_campaign               = isset($params['bs_campaign']) ? $params['bs_campaign'] : null;
$phone_number              = isset($params['phone_number']) ? $params['phone_number'] : null;

$swap_imei               = isset($params['swap_imei']) ? $params['swap_imei'] : null;
$old_imei               = isset($params['old_imei']) ? $params['old_imei'] : null;
$new_imei               = isset($params['new_imei']) ? $params['new_imei'] : null;

$swap_acc               = isset($params['swap_acc']) ? $params['swap_acc'] : null;

$text_note               = isset($params['text_note']) ? $params['text_note'] : null;

$open_market_campaign    = isset($params['open_market_campaign']) ? $params['open_market_campaign'] : null;

$presales_sn            = isset($params['presales_sn']) ? $params['presales_sn'] : '';
$staff_code            = isset($params['staff_code']) ? $params['staff_code'] : '0';

$buy_return     = isset($params['buy_return']) ? $params['buy_return'] : 0;
$discount_buy_return     = isset($params['discount_buy_return']) ? $params['discount_buy_return'] : 0;
$discount_buy_return_true     = isset($params['discount_buy_return_true']) ? $params['discount_buy_return_true'] : 0;

$check_money_transfer   = isset($params['check_money_transfer']) ? $params['check_money_transfer'] : null;
$store_id               = isset($params['store_id']) ? $params['store_id'] : 0;
$finance_client         = isset($params['finance_client']) ? $params['finance_client'] : 0;

//Brand shop By Com7
if($distributor_id == 34119){
    $sales_catty_id = null;
}

//$total_spc_discount = 50000;
//$total_spc_discount=100;
//print_r($job_sn);die;
/**
* @author: buu.pham
* market fee - tÃƒÂ­nh giÃƒÂ¡ ship
* n?u giÃƒÂ¡ tr? don hÃƒÂ ng - b?o v? giÃƒÂ¡ - chi?t kh?u < giÃƒÂ¡ t?i thi?u => c?ng ti?n ship
*     >= giÃƒÂ¡ t?i thi?u => ti?n ship = 0
*/
$s_total_price = 0; // t?ng giÃƒÂ¡ don hÃƒÂ ng d? tÃƒÂ­nh ti?n ship

$QFinanceClient = new Application_Model_FinanceClient();
$QDistributor = new Application_Model_Distributor();
$QContactDetail = new Application_Model_ContactDetail();

//check can edit lifetime
$QExceptionCase = new Application_Model_ExceptionCase();
$where = $QExceptionCase->getAdapter()->quoteInto('name = ?',
    'LIFETIME_EXCEPTION');
$lifetime_exception = $QExceptionCase->fetchRow($where);

$exception_case = null;
if (isset($lifetime_exception) and $lifetime_exception['value'])
{
    eval(json_decode($lifetime_exception['value']));
    $exception_case = isset($data_exception) ? $data_exception : null;
}

if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, SUPER_SALES_ADMIN))
    or ($exception_case and in_array($userStorage->id, $exception_case)))
    $life_time_editable = 1;
else
    $life_time_editable = 0;

if ($life_time_editable and $life_time <= 0)
{
    return array(
        'code' => -1,
        'message' => 'Invalid lifetime, please try again!',
    );
}
//end of check can edit lifetime
//
if (!$type)
    return array(
        'code' => -1,
        'message' => 'Invalid type, please try again!',
    );

if (isset($ids_bvg) and $ids_bvg and isset($ids_discount) and $ids_discount)
{
    return array(
        'code' => -1,
        'message' => 'Multiple type, please select only one discount!',
    );
}

// check PO Number
if (!empty($distributor_po))
{
    $QDistributorPo = new Application_Model_DistributorPo();
    $po_check = $QDistributorPo->find($distributor_po);
    $po_check = $po_check->current();

    if (!$po_check)
        return array(
            'code' => -6,
            'message' => 'Invalid PO Number',
        );
}

$QLog = new Application_Model_Log();
$db = Zend_Registry::get('db');
$db->beginTransaction();

$QGood = new Application_Model_Good();
$QGoodColor = new Application_Model_GoodColor();
$goods_cache = $QGood->get_cache();
$good_colors_cache = $QGoodColor->get_cache();
try
{

//Tanong
    if (!$sn){
        $sn = date('YmdHis') . substr(microtime(), 2, 4);

        $sn_ref=$this->getSalesOrderNo_Ref($sn);
    //$sn_ref="";

    //save corn gen so
        $QCS = new Application_Model_CronSo();
        $cron_data = array(
            'sn' => $sn,
            'status' => 1,
            'created_date' => date('Y-m-d H:i:s')
        );

        $QCS->insert($cron_data);
    }

//customer_brandshop
    if($rank==10){
        $QCustomerBrandShop   = new Application_Model_CustomerBrandShop();

    //distributor group brand shop
        if($group_type_id_post == 10){

            $CustomerBrandShop = $QCustomerBrandShop->chkCustomerBrandshop($customer_name,$customer_tax_number,$member_brandshop_code);
            if(!$CustomerBrandShop){
                $customer_id='';
            }

            $data_customer = array();
            $data_customer['customer_name'] = $customer_name;
            $data_customer['phone_number'] = $customer_phone_number;
            $data_customer['tax_number'] = $customer_tax_number;
            $data_customer['branch_no'] = $customer_branch_number;
            $data_customer['address_tax'] = $customer_tax_address;
            $data_customer['zip_code'] = $customer_zip_code;
            $key_sn = date('YmdHis') . substr(microtime(), 2, 4);

            $data_customer['member_brandshop_code'] = $member_brandshop_code;


            if($customer_id !='')
        { //update
            $data_customer['update_date']   = $currentTime;
            $data_customer['update_by']  = $userStorage->id;
            $data_customer['key_sn']   = $key_sn;
            $where = $QCustomerBrandShop->getAdapter()->quoteInto('customer_id = ?', $customer_id);
            $QCustomerBrandShop->update($data_customer, $where);
        }else{ //insert

            $data_customer['status'] = 1;
            $data_customer['create_date']   = $currentTime;
            $data_customer['create_by']  = $userStorage->id;
            $data_customer['key_sn']   = $key_sn;
            // print_r($data_customer);die;
            $QCustomerBrandShop->insert($data_customer);

            $whereCustomer = array();
            $whereCustomer[] = $QCustomerBrandShop->getAdapter()->quoteInto('key_sn = ?', $key_sn);
            $customer = $QCustomerBrandShop->fetchRow($whereCustomer);
            $customer_id = $customer['customer_id'];
        }

    }else{

        $CustomerBrandShop = $QCustomerBrandShop->chkCustomerBrandshop($customer_name,$customer_tax_number);
        if(!$CustomerBrandShop){
            $customer_id='';
        }

        $data_customer = array();
        $data_customer['customer_name'] = $customer_name;
        $data_customer['phone_number'] = $customer_phone_number;
        $data_customer['tax_number'] = $customer_tax_number;
        $data_customer['branch_no'] = $customer_branch_number;
        $data_customer['address_tax'] = $customer_tax_address;
        $data_customer['zip_code'] = $customer_zip_code;
        $key_sn = date('YmdHis') . substr(microtime(), 2, 4);


        if($customer_id !='')
        { //update
            $data_customer['update_date']   = $currentTime;
            $data_customer['update_by']  = $userStorage->id;
            $data_customer['key_sn']   = $key_sn;
            $where = $QCustomerBrandShop->getAdapter()->quoteInto('customer_id = ?', $customer_id);
            $QCustomerBrandShop->update($data_customer, $where);
        }else{ //insert

            $data_customer['status'] = 1;
            $data_customer['create_date']   = $currentTime;
            $data_customer['create_by']  = $userStorage->id;
            $data_customer['key_sn']   = $key_sn;
            // print_r($data_customer);die;
            $QCustomerBrandShop->insert($data_customer);

            $whereCustomer = array();
            $whereCustomer[] = $QCustomerBrandShop->getAdapter()->quoteInto('key_sn = ?', $key_sn);
            $customer = $QCustomerBrandShop->fetchRow($whereCustomer);
            $customer_id = $customer['customer_id'];
        }
    }
}

$QMarketDeduction = new Application_Model_MarketDeduction();
//get old ids
$old_ids = $error_ids = null;

if ($sn)
{
    $where = $QMarketDeduction->getAdapter()->quoteInto('sn = ?', $sn);
    $old_sales = $QMarketDeduction->fetchAll($where);

    if ($old_sales)
    {
        foreach ($old_sales as $sale)
        {
            $old_ids[] = $sale->id;
        }
    }
}

if (isset($ids_discount) and is_array($ids_discount))
{

    $total_price_discount = 0;
    $price_discount_joint = 0;
    $discounts = array();
    $discount = array('d_id' => $distributor_id, );
    $price_discount_joint = $QMarketDeduction->getPrice($discount);

    foreach ($ids_discount as $k => $id)
    {
        if (isset($joint_discount[$k]) and $joint_discount[$k] and isset($prices_discount[$k]) and
            $prices_discount[$k])
        {

            if ((intval($price_discount_joint[$joint_discount[$k]]) - intval($prices_discount[$k])) < 0)
            {
                throw new Exception(' Your discount is larger than limited ');
            }

            $data = array(
                'price'             => My_Number::floatval( $prices_discount[$k] ),
                'd_id'              => intval( $distributor_id ),
                'joint_circular_id' => intval( $joint_discount[$k] ),
            );

            // t?ng giÃƒÂ¡
            $s_total_price -= My_Number::floatval( $prices_discount[$k] );

            if ($id)
            { //update
                $where = $QMarketDeduction->getAdapter()->quoteInto('id = ?', $id);
                $QMarketDeduction->update($data, $where);
            } else
            {
                $data['add_time']   = $currentTime;
                $data['sn']         = $sn;
                $QMarketDeduction->insert($data);
            }
        }
    }

    //todo log
    $ip = $this->getRequest()->getServer('REMOTE_ADDR');
    $info = 'Discount Sale : ';
    $info .= $sn;

    $QLog->insert(array(
        'info' => $info,
        'user_id' => $userStorage->id,
        'ip_address' => $ip,
        'time' => $currentTime,
    ));
}

if ($sn)
{
    if ($ids_discount)
    {
        $newIds = $ids_discount ? $ids_discount : array();
        $removed_sales_ids = array_diff($old_ids, $newIds);
        if ($removed_sales_ids)
        {
            $where = $QMarketDeduction->getAdapter()->quoteInto('id IN (?)', $removed_sales_ids);
            $QMarketDeduction->delete($where);
        }
    }
}

}
catch (exception $e)
{
    return array(
        'code' => -3,
        'message' => 'Cannot save, please try again! 1' . $e->getMessage(),
    );
}

try
{
    $QMarketProduct = new Application_Model_MarketProduct();
    $QBVGIMEI = new Application_Model_BvgImei();

//get old ids
    $old_ids = $error_ids = null;
    if ($sn)
    {
        $where = $QMarketProduct->getAdapter()->quoteInto('sn = ?', $sn);
        $old_sales = $QMarketProduct->fetchAll($where);

        if ($old_sales)
        {
            foreach ($old_sales as $sale)
            {
                $old_ids[] = $sale->id;
            }
        }
    }

    if (isset($ids_bvg) and is_array($ids_bvg))
    {

        foreach ($ids_bvg as $k => $id)
        {
            if (isset($good_ids_bvg[$k]) and $good_ids_bvg[$k] and isset($nums_bvg[$k]) and
                $nums_bvg[$k] and isset($prices_bvg[$k]) and $prices_bvg[$k] and isset($totals_bvg[$k]) and
                $totals_bvg[$k])
            {
            // them viec kiem tra imei do co fai duoc bao ve gia cho dai ly do khong
                $list_imei = explode(',', $bvg_imei[$k]);
                $where     = array();
                $where[]   = $QBVGIMEI->getAdapter()->quoteInto('id in ( ? )', $list_imei);
                $where[]   = $QBVGIMEI->getAdapter()->quoteInto('bvg_payment_confirmed_at is NULL', null);
                $where[]   = $QBVGIMEI->getAdapter()->quoteInto('d_id = ?', $distributor_id);

                $imeiListChecked = $QBVGIMEI->fetchAll($where);

                if ($imeiListChecked->count() != count($list_imei)){
                    throw new Exception('List BVG IMEI is invalid');
                }
            // End of them viec kiem tra imei do co fai duoc bao ve gia cho dai ly do khong

                $data = array(
                    'good_id'      => intval( $good_ids_bvg[$k] ),
                    'num'          => intval( $nums_bvg[$k] ),
                    'price'        => My_Number::floatval( $prices_bvg[$k] ),
                    'total'        => My_Number::floatval( $totals_bvg[$k] ),
                    'saleoff_price' => My_Number::floatval( $saleoff_price[$k] ),
                    'd_id'         => intval( $distributor_id ),
                    'warehouse_id' => intval( $warehouse_id ),
                    'joint'        => intval( $joint[$k] ),
                    'text'         => trim($texts[$k] ? $texts[$k] : '')
                );

            // t?ng giÃƒÂ¡
                $s_total_price -= My_Number::floatval( $totals_bvg[$k] );

                if ($id)
            { //update
                $where     = $QMarketProduct->getAdapter()->quoteInto('id = ?', $id);
                $QMarketProduct->update($data, $where);
                $list_imei = explode(',', $bvg_imei[$k]);
                $data      = array('bvg_market_product_id' => $id);
                $where     = array();
                $where[]   = $QBVGIMEI->getAdapter()->quoteInto('id in ( ? )', $list_imei);
                $where[]   = $QBVGIMEI->getAdapter()->quoteInto('bvg_payment_confirmed_at is NULL', null);
                $QBVGIMEI->update($data, $where);
            } else
            {
                $data['add_time'] = date('Y-m-d H:i:s');
                $data['user_id']  = $userStorage->id;
                $data['sn']       = $sn;
                $id_bvg           = $QMarketProduct->insert($data);
                $list_imei        = explode(',', $bvg_imei[$k]);
                $data             = array('bvg_market_product_id' => $id_bvg);
                $where            = array();
                $where[]          = $QBVGIMEI->getAdapter()->quoteInto('id in ( ? )', $list_imei);
                $where[]          = $QBVGIMEI->getAdapter()->quoteInto('bvg_payment_confirmed_at is NULL', null);
                $QBVGIMEI->update($data, $where);
            }
        }
    }



    //todo log
    $ip = $this->getRequest()->getServer('REMOTE_ADDR');
    $info = 'BVG : ';
    $info .= $sn;

    $QLog->insert(array(
        'info'      => $info,
        'user_id'   => $userStorage->id,
        'ip_address'=> $ip,
        'time'      => $currentTime,
    ));

}


if ($sn)
{
    if ($old_ids)
    {
        $newIds = $ids_bvg ? $ids_bvg : array();
        $removed_sales_ids = array_diff($old_ids, $newIds);

        if ($removed_sales_ids)
        {
            $where = $QMarketProduct->getAdapter()->quoteInto('id IN (?)', $removed_sales_ids);
            $QMarketProduct->delete($where);

            $where = $QBVGIMEI->getAdapter()->quoteInto('bvg_market_product_id IN (?)', $removed_sales_ids);
            $data             = array('bvg_market_product_id' => NULL);
            $QBVGIMEI->update($data, $where);
        }
    }
}

}
catch (exception $e)
{
    return array(
        'code' => -3,
        'message' => 'Cannot save, please try again! 2' . $e->getMessage(),
    );
}

if (is_array($ids))
{

    try
    {


        $QDistributor = new Application_Model_Distributor();
        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
        $array_good_color = array();
        $distributor = $QDistributor->fetchRow($where);
        $rank = $distributor['rank'];

        $spc_discount = $distributor['spc_discount'];
        $spc_discount_phone = $distributor['spc_discount_phone'];
        $spc_discount_acc = $distributor['spc_discount_acc'];
        $spc_discount_digital = $distributor['spc_discount_digital'];

        $QMarket = new Application_Model_Market();

        /* TODO insert market general */
        $QMarketGeneral = new Application_Model_MarketGeneral();
        $data           = array(
            'sn'                => $sn,
            'price_clas'        => intval( $rank ),
            'd_id'              => intval( $distributor_id ),
            'warehouse_id'      => intval( $warehouse_id ),
            'isbatch'           => intval( $isbatch ),
            'salesman'          => intval( $salesman ),
            'sales_catty_id'    => intval( $sales_catty_id ),
            'type'              => intval( $type ),
            'service'           => intval( $service_id ),
        );


        if ($market_general_id) {
            $whereMarketGeneral = $QMarketGeneral->getAdapter()->quoteInto('id = ?', $market_general_id);
            $QMarketGeneral->update($data, $whereMarketGeneral);
        } else {
            $data['add_time']   = $currentTime;
            $data['user_id']    = $userStorage->id;
            $newMarketGeneralId = $QMarketGeneral->insert($data);

        }
        /* TODO End of insert market general */



        if (!isset($ids_bvg))
        {
            $QMarketProduct = new Application_Model_MarketProduct();
            $where = $QMarketProduct->getAdapter()->quoteInto('sn = ? ', $sn);
            $QMarketProduct->delete($where);
        }

        if (!isset($ids_discount))
        {
            $QMarketProduct = new Application_Model_MarketDeduction();
            $where = $QMarketProduct->getAdapter()->quoteInto('sn = ? ', $sn);
            $QMarketProduct->delete($where);
        }

    //My_Lock::setStatus($sn , 0 , array());

    //get old ids
        $old_ids = $error_ids = null;
        if ($sn)
        {
            $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $old_sales = $QMarket->fetchAll($where);

            if ($old_sales)
            {
                foreach ($old_sales as $sale)
                {
                    $old_ids[] = $sale->id;

                    if ($sale['pay_time'] or $sale['shipping_yes_time'] or $sale['outmysql_time'])
                        $error_ids[] = $sale->id;
                }
            }
        }

        if ($error_ids)
        {
            return array(
                'code' => -5,
                'message' => 'This order was confirmed!',
                'sn' => $sn,
            );
        }


    // NhÃƒÂ¢n viÃƒÂªn mua mÃƒÂ¡y
        if($type == FOR_STAFFS AND in_array($distributor_id, array(OPPO_STAFF,OPPO_INGAME)) AND intval($for_partner) == 2 )
        {
        // Neu la don hang thanh ly thi kiem tra
            if($shipment_id){
                foreach($ids as $k =>  $v){
                    $where_shipment_s   = array();
                    $GoodShipmentPhone  = new Application_Model_GoodShipmentPhone();
                    $where_shipment_s[] = $GoodShipmentPhone->getAdapter()->quoteInto('good_shipment_id = ?', $market_general_data['shipment_id']);
                    $where_shipment_s[] = $GoodShipmentPhone->getAdapter()->quoteInto('good_id = ?', $good_ids[$k]);
                    $data_s             = $GoodShipmentPhone->fetchRow($where_shipment_s);

                    if(!$data_s){
                        return array(
                            'code'    => -5,
                            'message' => 'Ch? du?c ch?n m?t hÃƒÂ ng trong lÃƒÂ´ mÃƒÂ¡y.!!!!!!!!!!!',
                        );
                    }
                }
            }

            $QStaffOrder = new Application_Model_StaffOrder();
            $count_40 = array();
            if(count($product_color_keys) == 0){
                return array(
                    'code'    => -5,
                    'message' => 'Vui lÃƒÂ²ng ch?n NhÃƒÂ¢n viÃƒÂªn mua mÃƒÂ¡y',
                );
            }


        //Ki?m tra s? lu?ng s?n ph?m cÃƒÂ³ phÃƒÂ¹ h?p ko?
            foreach($cat_ids as $k => $v){
                $_product_color_key = $good_ids[$k].'_'.$good_colors[$k];
                $check_num = 0;
                foreach($product_color_keys as $_k => $_v){
                    if( $_v == $_product_color_key ){
                        $check_num += $staff_nums[$_k];
                    }
                }

                if($check_num != $nums[$k]){
                    return array(
                        'code'    => -5,
                        'message' => 'S? lu?ng s?n ph?m d?t mua vÃƒÂ  s? lu?ng s?n ph?m cho nhÃƒÂ¢n viÃƒÂªn khÃƒÂ´ng dÃƒÂºng'.$nums[$k].'-'.$check_num,
                    );
                }

            }

            if($distributor_id == OPPO_STAFF)
            {

                if($for_partner == 2){

                    foreach($cat_ids as $k => $v){
                        if($sale_off_percent[$k] == 40){
                            $_product_color_key = $good_ids[$k].'_'.$good_colors[$k];
                            foreach($product_color_keys as $_k => $_v){
                                if( $_v == $_product_color_key ){
                                    if( isset($count_40[$id_staffs[$_k]]) ){
                                        $count_40[$id_staffs[$_k]] += intval($staff_nums[$_k]);

                                    }else{
                                        $count_40[$id_staffs[$_k]] = intval($staff_nums[$_k]);
                                    }
                                }
                            }
                        }
                    }

                    foreach($count_40 as $_staff_id => $num){
                        if($num > 1){
                            return array(
                                'code'    => -5,
                                'message' => 'NhÃƒÂ¢n viÃƒÂªn m?i l?n ch? du?c mua 1 mÃƒÂ¡y 40%',
                            );
                        }else{
                            $check = $QStaffOrder->checkStaffBuyProduct($_staff_id,1,NULL,$sn);
                            if($check['status'] == 0){
                                return array(
                                    'code'    => -5,
                                    'message' => $check['message'],
                                );
                            }
                        }
                    }
                }
            }
            elseif($distributor_id == OPPO_INGAME){
                foreach($cat_ids as $k => $v){
                    if($sale_off_percent[$k] == 40){
                        $_product_color_key = $good_ids[$k].'_'.$good_colors[$k];
                        foreach($product_color_keys as $_k => $_v){
                            if( $_v == $_product_color_key ){
                                $cmnd = trim($cmnd_staff_ingames[$_k]);

                                if($cmnd == ''){
                                    return array(
                                        'code'    => -5,
                                        'message' => 'Please insert ID number',
                                    );
                                }

                                if( isset($count_40[$cmnd]) ){
                                    $count_40[$cmnd] += intval($staff_nums[$_k]);
                                }else{
                                    $count_40[$cmnd] = intval($staff_nums[$_k]);
                                }
                            }
                        }
                    }
                }

                foreach($count_40 as $_id_number => $num){
                    if($num > 1){
                        return array(
                            'code'    => -5,
                            'message' => 'NhÃƒÂ¢n viÃƒÂªn m?i l?n ch? du?c mua 1 mÃƒÂ¡y 40%',
                        );
                    }else{
                        $check = $QStaffOrder->checkStaffIngameBuyProduct($_id_number,1,NULL,$sn);
                        if($check['status'] == 0){
                            return array(
                                'code'    => -5,
                                'message' => $check['message'],
                            );
                        }
                    }
                }

            }

        }


        if (isset($invoice) and $invoice)
        {
            $QCustomer = new Application_Model_Customer();

            $data = array(
                'name'         => $invoice['customer_name'],
                'company'      => $invoice['company'],
                'address'      => $invoice['customer_address'],
                'office_id'    => intval($invoice['office']),
                'tax_code'     => $invoice['tax_code'],
                'invoice_type' => $invoice['invoice'],
                'add_time'     => $currentTime,
                'sn'           => $sn,
                'service_nvmm' => intval($invoice['service_nvmm']),
                'warehouse_nvmm' => intval($invoice['warehouse_nvmm']),
            );


            if (!$id)
                $QCustomer->insert($data);
            else
            {
                $where = $QCustomer->getAdapter()->quoteInto('id = ?', $id);
                $QCustomer->update($data, $where);
            }

        }

        $missing_stock = array();

        if (isset($ids) and $ids)
        {
            $resultSet = $QMarket->find($ids[0]);
            $market_current = $resultSet->current();
            if (isset($market_current) and $market_current)
            {
                $date_curent = $market_current['add_time'];
                $distributor_id_current = $market_current['d_id'];
                $current_time  = date('H:i:s');
            //ki?m tra khÃƒÂ´ng cho d?i d?i lÃƒÂ½
                if($distributor_id != $distributor_id_current && $current_time > TIME_LIMIT_ORDER)
                {
                    throw new Exception("Sorry, you can't change distributor for this order.Please remove order and create again.");
                }
            }
        }

        
        foreach ($ids as $k => $id)
        {
        // print_r($_POST);
            if (isset($cat_ids[$k]) and $cat_ids[$k] and isset($good_ids[$k]) and $good_ids[$k] and
                isset($good_colors[$k]) and $good_colors[$k] and isset($nums[$k]) and $nums[$k] and
                isset($prices[$k]))
            {
            //print_r($data);die;
                $total2 = 0;

                if ($cat_ids[$k] == PHONE_CAT_ID)
                {
                    if(in_array($good_ids[$k] . '_' . $good_colors[$k] , $array_good_color))
                    {
                        throw new Exception("Sorry, your input is duplicated, Model : " . $goods_cache[$good_ids[$k]] . " - " . $good_colors_cache[$good_colors[$k]]);
                    }
                    $array_good_color[] = $good_ids[$k] . '_' . $good_colors[$k];
                }

                $storageParams = array(
                    'warehouse_id'  => $warehouse_id,
                    'cat_id'        => $cat_ids[$k],
                    'good_id'       => $good_ids[$k],
                    'good_color_id' => $good_colors[$k],
                );

            // truong hop edit lai
                if ($id)
                    $storageParams['current_order_id'] = $id;

                $storageParams['not_get_ilike_bad_count'] = $storageParams['not_get_digital_bad_count'] =
                $storageParams['not_get_imei_bad_count'] = $storageParams['not_get_damage_product_count'] =
                $storageParams['not_get_total'] = $storageParams['not_order'] = true;

                if ($cat_ids[$k] == PHONE_CAT_ID)
                {
                    $storageParams['not_get_ilike_count'] = $storageParams['not_get_digital_count'] =
                    $storageParams['not_get_product_count'] = true;
                } elseif ($cat_ids[$k] == ACCESS_CAT_ID)
                {
                    $storageParams['not_get_ilike_count'] = $storageParams['not_get_digital_count'] =
                    $storageParams['not_get_imei_count'] = true;
                } elseif ($cat_ids[$k] == DIGITAL_CAT_ID)
                {
                    $storageParams['not_get_ilike_count'] = $storageParams['not_get_product_count'] =
                    $storageParams['not_get_imei_count'] = true;
                } elseif ($cat_ids[$k] == ILIKE_CAT_ID)
                {
                    $storageParams['not_get_digital_count'] = $storageParams['not_get_product_count'] =
                    $storageParams['not_get_imei_count'] = true;
                }

                $storage = $QGood->fetchPaginationStorage(1, null, $total2, $storageParams);

                $current_order = isset($storage[0]['current_order']) ? $storage[0]['current_order'] : 0;
                $current_change_order = isset($storage[0]['current_change_order']) ? $storage[0]['current_change_order'] : 0;
                if ($cat_ids[$k]==PHONE_CAT_ID and $type==FOR_DEMO){
                    $current_order = isset($storage[0]['current_order_demo']) ? $storage[0]['current_order_demo'] : 0;
                    $current_change_order   = isset($storage[0]['current_change_order_demo']) ? $storage[0]['current_change_order_demo'] : 0;
                }

                $current_storage = 0;

                if (isset($storage[0]) and $storage[0])
                {
                    switch ($cat_ids[$k]){
                        case DIGITAL_CAT_ID:
                        $current_storage = (isset($storage[0]['digital_count']) and $storage[0]['digital_count']) ? $storage[0]['digital_count'] : 0;
                        break;
                        case PHONE_CAT_ID:
                        $current_storage = (isset($storage[0]['imei_count']) and $storage[0]['imei_count']) ? $storage[0]['imei_count'] : 0;
                        if ($type==FOR_DEMO){
                            $current_storage = (isset($storage[0]['imei_demo_count']) and $storage[0]['imei_demo_count']) ? $storage[0]['imei_demo_count'] : 0;
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

                if (($current_storage - $current_order - $current_change_order) < $nums[$k])
                {
                    $missing_stock[] = array(
                        'warehouse_id'    => $warehouse_id,
                        'cat_id'          => $cat_ids[$k],
                        'good_id'         => $good_ids[$k],
                        'good_color_id'   => $good_colors[$k],
                        'current_storage' => $current_storage,
                        'current_order'   => $current_order,
                    );
                }


            // $tem_total = (isset($totals[$k]) and $totals[$k]) ? $totals[$k] : 0;
            //PungPond

                $input_discount_buy_return = 0;
                if(isset($discount_buy_return[$k]) && $discount_buy_return[$k]){
                    $input_discount_buy_return = $discount_buy_return[$k];
                }

                $input_discount_buy_return_true = 0;
                if(isset($discount_buy_return_true[$k]) && $discount_buy_return_true[$k]){
                    $input_discount_buy_return_true = $discount_buy_return_true[$k];
                }

                if($buy_return){
                    $prices[$k] = $prices[$k] - $input_discount_buy_return - $input_discount_buy_return_true;
                }
                
                $tem_total = ($prices[$k]*$nums[$k]);

                if($sale_off_percent[$k] > 0){
                    $total_price_one = $prices[$k] - round(($prices[$k]*$sale_off_percent[$k]/100)*100)/100;
                    $x_total = $total_price_one*$nums[$k];
                    $tem_total =  round($x_total); 
                }
                
                $price_ext = round(($prices[$k]/1),2);
                $total_ext = round(($tem_total/1),2);

                if($delivery_fee==''){
                    $delivery_fee=0;
                }

            // Check Special Discount
                if(isset($warehouse_id) and $warehouse_id == 71){
                    $total_discount_digital = $digital_discount + $distributor['spc_discount'];
                    $spc_discount = $total_discount_digital;
                }else{
                    $spc_discount = $distributor['spc_discount'];
                }
                
                $spc_discount_phone = $distributor['spc_discount_phone'];
                $spc_discount_acc = $distributor['spc_discount_acc'];
                $spc_discount_digital = $distributor['spc_discount_digital'];

                if(isset($total_discount_digital) and $total_discount_digital > 0){
                    $spc_discount_digital = 1;
                }

                if($store_id) {
                    $QStore = new Application_Model_Store();
                    $result = $QStore->find($store_id);

                    if($result){
                        $insert_store_id = $store_id;
                    }else{
                        $insert_store_id = '0';
                    }
                }
                
            //$total_spc_discount=20050;

                $data = array(
                    'market_general_id'=> ($market_general_id ? $market_general_id : (isset($newMarketGeneralId) ? $newMarketGeneralId : 0)),
                    'cat_id'           => intval( $cat_ids[$k] ),
                    'good_id'          => intval( $good_ids[$k] ),
                    'good_color'       => intval( $good_colors[$k] ),
                    'num'              => intval( $nums[$k] ),
                    'price'            => My_Number::floatval( $prices[$k] ),
                    'price_ext'        => My_Number::floatval( $price_ext ),
                    'total'            => My_Number::floatval( $tem_total ),
                    'sale_off_price' => My_Number::floatval( $saleoff_price[$k] ),
                    'total_ext'        => My_Number::floatval( $total_ext ),
                    'text'             => (isset($texts[$k]) ? $texts[$k] : null),
                    'price_clas'       => intval( $rank ),
                    'd_id'             => intval( $distributor_id ),
                    'warehouse_id'     => intval( $warehouse_id ),
                    'isbatch'          => intval( $isbatch ),
                    'salesman'         => intval( $salesman ),
                    'sales_catty_id'   => intval( $sales_catty_id ),
                    'type'             => intval( $type ),
                    'service'          => intval( $service_id ),
                    'sale_off_percent' => intval( $sale_off_percent[$k] ),
                    'campaign'         => intval($campaign[$k]),
                    'last_updated_at'  => $currentTime,
                    'payment_method'   => $payment_method,
                    'for_partner'      => $for_partner,
                    'credit_id'        => $credit_id,
                    'delivery_address' => $delivery_address,
                    'delivery_fee'     => $delivery_fee,
                    'shipping_address' => $sipping_add,
                    'spc_discount'      => $spc_discount,
                    'spc_discount_phone' => $spc_discount_phone,
                    'spc_discount_acc'   => $spc_discount_acc,
                    'spc_discount_digital' => $spc_discount_digital,
                    'total_spc_discount' => $total_spc_discount,
                    'customer_name'     => $customer_name_for_staff , 
                    'bs_campaign'       => $bs_campaign,
                    'shipping_text'     => $text_note,
                    'open_market_campaign' => $open_market_campaign,
                    'staff_code' => $staff_code,
                    'buy_return' => $buy_return,
                    'discount_buy_return' => $input_discount_buy_return,
                    'discount_buy_return_true' => $input_discount_buy_return_true,
                    'check_money_transfer' => $check_money_transfer,

                // Bypass
                    'shipping_yes_time' => date('Y-m-d H:m:s'),
                    'pay_time'          => date('Y-m-d H:m:s'),
                    'shipping_yes_id'   => 825,
                    'pay_user'          => 825,


		    'finance_client_id'      => intval( $finance_client ),
                    'store_id'               => intval( $insert_store_id ),

                );

                if($presales_sn !='')
                {
                    $data['presales_sn']=$presales_sn;
                }
            // echo print_r($data);
            //'presales_sn'       => $presales_sn

                if ( $cat_ids[$k] == 11 and ($prices[$k] <= 0 || $price_ext <= 0)) {
                    $QlogCreateSaleOrder = new Application_Model_logCreateSaleOrder();

                    $userAgent = new Zend_Http_UserAgent();
                    $device = $userAgent->getDevice();
                    $browser_1 = $device->getBrowser();
                    $browser_2 = $device->getUserAgent();
                    $data_i['text_data'] = json_encode($data);
                    $data_i['browser']   = $browser_1." | ".$browser_2;
                    $data_i['create_at'] = $currentTime;
                    $data_i['create_by'] = $userStorage->id;
                // $this->insertLog($data_i);
                    $QlogCreateSaleOrder ->insert($data_i);
                }
                if($rank==10){
                    if($customer_id !=''){
                     $data['customer_id'] = $customer_id; 
                     $data['customer_tax_address'] = $customer_tax_address;
                 }
             }    

             if(isset($invoice_number_data) and $invoice_number_data)
             {
                // s? hÃƒÂ³a don khi save mass upload
                $invoice_number = unserialize($invoice_number_data);
                if(is_array($invoice_number) and $invoice_number)
                {
                    $data['invoice_number'] = $invoice_number['invoice_number'] ? $invoice_number['invoice_number'] : '';
                    $data['invoice_sign']   = $invoice_number['invoice_sign'] ? $invoice_number['invoice_sign'] : '';
                    $data['invoice_time']   = $invoice_number['invoice_time'] ? $invoice_number['invoice_time'] : '';
                    $data['text']           = 'ÃƒÂon hÃƒÂ ng t?n kho t?ng kÃƒÂ¨m sim';

                    $data['shipping_yes_time'] = $currentTime;
                    $data['pay_time']          = $currentTime;
                    $data['shipping_yes_id']   = 1;
                    $data['pay_user']          = 1;
                    $data['outmysql_time']     = $currentTime;
                    $data['outmysql_user']     = 1;
                    $data['campaign']          = 99;

                }

            }

            // t?ng giÃƒÂ¡
            $s_total_price += My_Number::floatval( $tem_total );

            ///don hÃƒÂ ng t?ng cho nhÃƒÂ¢n viÃƒÂªn
            if (isset($gift_id) and $gift_id)
            {
                $data['office'] = $gift_id;
            }

            if (isset($invoice) and $invoice)
            {
                $data['office']             = intval($invoice['office']);  
                $data['warehouse_nvmm']     = intval($invoice['warehouse_nvmm']);
                $data['service']            = intval($invoice['service_nvmm']);
            }
            //Tanong
            //total

            if (!empty($distributor_po))
                $data['po_id'] = $distributor_po;
            else
                $data['po_id'] = null;

            if ($life_time_editable and $life_time) {
                if ($life_time <= 0 || $life_time > 5 || !is_numeric($life_time)) $life_time = 2;
                $data['life_time'] = $life_time * 24 * 60 * 60;
            }

        // Bypass
            $data['confirm_so'] = 1;

            //print_r($_POST);die;
            //insert_order
            if ($id)
            { //update

                if(!$check_money_transfer){

                    // START : Bypass Sale confirm and Finance confirm
                    $data['sales_confirm_date'] = $currentTime;
                    $data['sales_confirm_id'] = $userStorage->id;

                    $data['finance_confirm_date'] = $currentTime;
                    $data['finance_confirm_id'] = $userStorage->id;

                    $data['pay_time'] = $currentTime;
                    $data['pay_user'] = $userStorage->id;

                    $data['shipping_yes_time'] = $currentTime;
                    $data['shipping_yes_id'] = $userStorage->id;

                    $data['confirm_so'] = 1;

                    $QCheckmoney = new Application_Model_Checkmoney();

                    $data_ch2 = array(
                        'd_id'                  => $distributor_id,
                        'payment'               => $currentTime,
                        'pay_time'              => $currentTime,
                        'pay_service'           => 0,
                        'output'                => 0,
                        'pay_money'             => 0,
                        'type'                  => 2,
                        'sn'                    => $sn,
                        'user_id'               => $userStorage->id,
                        'create_by'             => $userStorage->id,
                        'create_at'             => $currentTime,
                        'note'                  => '',
                        'company_id'            => 1,
                        'sales_confirm_date'    => $currentTime,
                        'sales_confirm_id'      => $userStorage->id,
                        'finance_confirm_id'    => $userStorage->id,
                        'finance_confirm_date'  => $currentTime,
                        'payment_surplus'       => 0,
                        'lack_of_money'         => 0
                    );

                    $QCheckmoney->insert($data_ch2);
                    // START : Bypass Sale confirm and Finance confirm
                }

                $data['delivery_address'] = $add_delivery_new;

                $where = $QMarket->getAdapter()->quoteInto('id = ?', $id);
                $QMarket->update($data, $where);


                $db = Zend_Registry::get('db');

                $QLogQuota = new Application_Model_LogQuotaTran();
                $QLogQuotaDis = new Application_Model_LogQuotaTranDistributor();
                $data_q = array(
                    'warehouse_id'     => intval( $warehouse_id ),
                    'd_id'             => intval( $distributor_id ),
                    'cat_id'           => intval( $cat_ids[$k] ),
                    'good_id'          => intval( $good_ids[$k] ),
                    'good_color'       => intval( $good_colors[$k] ),
                    'num'              => intval( $nums[$k] ),
                    // 'add_time'         => $currentTime,
                    'user_id'          => $userStorage->id,
                    'sn'               => $sn,
                );
                $data_dis = array(
                    'warehouse_id'     => intval( $warehouse_id ),
                    'cat_id'           => intval( $cat_ids[$k] ),
                    'good_id'          => intval( $good_ids[$k] ),
                    'good_color'       => intval( $good_colors[$k] ),
                    'num'              => intval( $nums[$k] ),
                    // 'add_time'         => $currentTime,
                    'user_id'          => $userStorage->id,
                    // 'sn'               => $sn,
                );
                $where_l = $QLogQuota->getAdapter()->quoteInto('market_id = ?', $id);
                $where_dis = $QLogQuotaDis->getAdapter()->quoteInto('market_id = ?', $id);

                if (in_array($type, [FOR_RETAILER,FOR_DEMO,FOR_APK])) {

                    if(!$QLogQuota->update($data_q, $where_l)){

                    // $QLogQuota->delete($where_l);

                        $select_a = $db->select()
                        ->from(array('d' => 'distributor'), array('d.rank','d.quota_channel','d.group_id'));

                        $select_a->joinLeft(array('r' => HR_DB.'.regional_market'),'r.id=d.region',array());
                        $select_a->joinLeft(array('a' => HR_DB.'.area'),'r.area_id=a.id',array('area_id'=>'a.id'));
                        $select_a->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
                        $select_a->where('d.id = ?', $distributor_id);
                        $distributor = $db->fetchRow($select_a);

                   // $dis_type = '';
                   // if ($distributor['quota_channel']) {
                   //     if ($distributor['quota_channel'] == 1) {
                   //        $action_quota = "1";

                   //     }else if ($distributor['quota_channel'] == 10) {
                   //        $action_quota = "10";

                   //     }
                   // }else{
                   //      if (in_array($distributor['rank'], array(7,8))) {
                   //         $action_quota = "7"; 
                   //      }else if (in_array($distributor['rank'], array(1,2,5,6))) {
                   //        $action_quota = "1";
                   //      }else if ($distributor['rank'] == 10) {
                   //        $action_quota = "10";
                   //      }
                   // }

                   // Group brand Shop by Dealer to brand shop
                        $old_group_id = '';
                        if($distributor['group_type_id'] == 11){
                            $old_group_id = $distributor['group_type_id'];
                            $distributor['group_type_id'] = 10;
                        }

                        $action_quota = $distributor['group_type_id'];

                        if ($action_quota) {
                            $toDay = date('Y-m-d');

                            $select_q = $db->select()
                            ->from(array('o' => 'quota_oppo_by_distributor'), array('o.good_id','o.good_color'));
                            $select_q->where('o.quota_date = ?',$toDay);
                            $select_q->where('o.d_id = ?',$distributor_id);
                            $select_q->where('o.warehouse = ?',$warehouse_id);
                            $select_q->where('o.good_id = ?',$good_ids[$k]);
                            $select_q->where('o.good_color = ?',$good_colors[$k]);
                            $select_q->where('o.status = 1');
                            $select_q->where('o.del is null',1);
                            $select_q->group(array('o.good_id','o.good_color'));
                            $product_quota_by_dis = $db->fetchAll($select_q);

                            $select_q_d = $db->select()
                            ->from(array('nqd' => 'new_quota_distributor'), array('nqd.good_id'));
                            $select_q_d->joinLeft(array('nqdd' => 'new_quota_distributor_details'),'nqdd.nqd_id=nqd.id',array('nqdd.good_color'));
                            $select_q_d->where('nqd.quota_date = ?',$toDay);
                            $select_q_d->where('nqd.warehouse_id = ?', $warehouse_id);
                            $select_q_d->where('nqd.d_id = ?', $distributor_id);
                            $select_q_d->where('nqd.order_type = ?', $type);
                            $select_q_d->where('nqd.good_id = ?', $good_ids[$k]);
                            $select_q_d->where('nqdd.good_color = ?', $good_colors[$k]);
                            $select_q_d->where('nqd.status = 1');
                            $select_q_d->where('nqdd.status = 1');
                            $select_q_d->group(array('nqd.good_id','nqdd.good_color'));
                            $new_quota_by_distributor = $db->fetchAll($select_q_d); 

                            if (isset($product_quota_by_dis) and $product_quota_by_dis and $type == FOR_RETAILER) {

                                $QLogQuotaDis = new Application_Model_LogQuotaTranDistributor();
                                foreach ($product_quota_by_dis as $key => $value) {
                                    if ($value['good_id'] == $good_ids[$k] && $value['good_color'] == $good_colors[$k]) {
                                     $data_q = array(
                                    // 'market_id'        => intval( $id ),
                                        'warehouse_id'     => intval( $warehouse_id ),
                                        'd_id'             => intval( $distributor_id ),
                                        'cat_id'           => intval( $cat_ids[$k] ),
                                        'good_id'          => intval( $good_ids[$k] ),
                                        'good_color'       => intval( $good_colors[$k] ),
                                        'num'              => intval( $nums[$k] ),
                                    // 'add_time'         => $currentTime,
                                        'user_id'          => $userStorage->id,
                                        'sn'               => $sn,
                                    );

                               // $QLogQuotaDis->insert($data_q);

                                     if(intval( $nums[$k] ) > 0){
                                        $QLogQuotaDis->update($data_q, $where_dis);
                                    }else{
                                        $QLogQuotaDis->delete($where_dis);
                                    }
                                }
                            }

                        }else if(isset($new_quota_by_distributor) and $new_quota_by_distributor and $type == FOR_RETAILER){

                            $QLogQuotaDis = new Application_Model_LogQuotaTranDistributor();
                            foreach ($new_quota_by_distributor as $key => $value) {
                                if ($value['good_id'] == $good_ids[$k] && $value['good_color'] == $good_colors[$k]) {
                                 $data_q = array(
                                    // 'market_id'        => intval( $id ),
                                    'warehouse_id'     => intval( $warehouse_id ),
                                    'd_id'             => intval( $distributor_id ),
                                    'cat_id'           => intval( $cat_ids[$k] ),
                                    'good_id'          => intval( $good_ids[$k] ),
                                    'good_color'       => intval( $good_colors[$k] ),
                                    'good_type'        => $type,
                                    'num'              => intval( $nums[$k] ),
                                    // 'add_time'         => $currentTime,
                                    'user_id'          => $userStorage->id,
                                    'sn'               => $sn,
                                );

                               // $QLogQuotaDis->insert($data_q);

                                 if(intval( $nums[$k] ) > 0){
                                    $QLogQuotaDis->update($data_q, $where_dis);
                                }else{
                                    $QLogQuotaDis->delete($where_dis);
                                }
                            }
                        }
                        
                    }else{

                        $good_type = $type;

                        $select_q = $db->select()
                        ->from(array('o' => 'quota_oppo'), array('o.good_id','o.good_color'));
                        $select_q->where('o.quota_date = ?',$toDay);
                        $select_q->where('o.dis_type = ?',$action_quota);
                        $select_q->where('o.good_type = ?',$good_type);
                        $select_q->where('o.warehouse_id = ?',$warehouse_id);
                        $select_q->group(array('o.good_id','o.good_color'));
                        // echo $select_q;die;
                        $product_quota = $db->fetchAll($select_q);
                        
                        if ($product_quota) {
                            $QLogQuota = new Application_Model_LogQuotaTran();
                            foreach ($product_quota as $key => $value) {
                                if ($value['good_id'] == $good_ids[$k] && $value['good_color'] == $good_colors[$k]) {
                                 $data_q = array(
                                        // 'market_id'        => intval( $id ),
                                    'warehouse_id'     => intval( $warehouse_id ),
                                    'd_id'             => intval( $distributor_id ),
                                    'cat_id'           => intval( $cat_ids[$k] ),
                                    'good_id'          => intval( $good_ids[$k] ),
                                    'good_color'       => intval( $good_colors[$k] ),
                                    'good_type'       => $good_type,
                                    'num'              => intval( $nums[$k] ),
                                        // 'add_time'         => $currentTime,
                                    'user_id'          => $userStorage->id,
                                    'sn'               => $sn,
                                );

                                   // $QLogQuota->insert($data_q);

                                 if(intval( $nums[$k] ) > 0){
                                    $QLogQuota->update($data_q, $where_l);
                                }else{
                                    $QLogQuota->delete($where_l);
                                }

                            }
                        }
                    }else{
                        $QLogQuota = new Application_Model_LogQuotaTran();
                        foreach ($product_quota as $key => $value) {
                            if ($value['good_id'] == $good_ids[$k] && $value['good_color'] == $good_colors[$k]) {
                             $data_q = array(
                                'market_id'        => intval( $id ),
                                'warehouse_id'     => intval( $warehouse_id ),
                                'd_id'             => intval( $distributor_id ),
                                'cat_id'           => intval( $cat_ids[$k] ),
                                'good_id'          => intval( $good_ids[$k] ),
                                'good_color'       => intval( $good_colors[$k] ),
                                'good_type'       => $good_type,
                                'num'              => intval( $nums[$k] ),
                                'add_time'         => $currentTime,
                                'user_id'          => $userStorage->id,
                                'sn'               => $sn,
                            );

                             $QLogQuota->insert($data_q);
                         }
                     }
                 }
             }
         }
     }
 }else{
    $QLogQuota->delete($where_l);
}

            // $QLogQuotaDis->update($data_dis, $where_dis);

            }else{ //insert
               $db = Zend_Registry::get('db');
               if (isset($date_curent) and $date_curent)
                $date_time_add = $date_curent;
            else
                $date_time_add = $currentTime;

            $data['add_time'] = $date_time_add;
            $data['user_id']  = $userStorage->id;
            $data['sn']       = $sn;

            $data['print_no'] = ($QMarket->get_print_no_max($sn)) + 1;

            if (!$this->getRequest()->getParam('sn')){
                    /*$data['sn_ref']   = $sn_ref;  
                    if($sn_ref==''){
                        $db->rollback();
                        return array(
                        'code' => -5,
                        'message' => 'Cannot Save, please try again! 3',
                        );
                    }*/
                }else{
                    $data['sn_ref']   = $this->getRequest()->getParam('sn_ref');  
                }

                if($save_service =='service'){

                    $QWarehouse = new Application_Model_Warehouse();
                   // $invoice_number= $QWarehouse->getInvoiceNo($sn);

                    //$data['invoice_number'] = $invoice_number;
                    //$data['invoice_time'] = $currentTime;

                    $data['outmysql_time']     = $currentTime;
                    $data['outmysql_user']     = $userStorage->id;


                    if ($data['cat_id'] == ACCESS_CAT_ID) {
                        $QWarehouseProduct = new Application_Model_WarehouseProduct();
                        $where_acc = array();
                        $where_acc[] = $QWarehouseProduct->getAdapter()->quoteInto('cat_id = ?', $data['cat_id']);
                        $where_acc[] = $QWarehouseProduct->getAdapter()->quoteInto('good_id = ?', $data['good_id']);
                        $where_acc[] = $QWarehouseProduct->getAdapter()->quoteInto('good_color = ?', $data['good_color']);
                        $where_acc[] = $QWarehouseProduct->getAdapter()->quoteInto('warehouse_id = ?', $data['warehouse_id']);

                        $result = $QWarehouseProduct->fetchRow($where_acc);
                        if ($result) {
                            $where_acc = $QWarehouseProduct->getAdapter()->quoteInto('id = ?', $result['id']);
                            $quantity = $result['quantity'] - $data['num'];
                            $data_acc = array('quantity' => ($quantity > 0 ? $quantity : 0));

                            $QWarehouseProduct->update($data_acc, $where_acc);
                        }
                    }

                    if ($data['cat_id'] == PHONE_CAT_ID and $swap_imei) {

                        $data['outmysql_time']     = $currentTime;
                        $data['outmysql_user']     = $userStorage->id;

                        $data['sales_confirm_date'] = $currentTime;
                        $data['sales_confirm_id'] = $userStorage->id;

                        $data['finance_confirm_date'] = $currentTime;
                        $data['finance_confirm_id'] = $userStorage->id;

                        $data['pay_time'] = $currentTime;
                        $data['pay_user'] = $userStorage->id;

                        $data['shipping_yes_time'] = $currentTime;
                        $data['shipping_yes_id'] = $userStorage->id;

                        $data['confirm_so'] = 1;

                        $data['service_swap_imei_status'] = 1;
                        $data['service_old_imei'] = $old_imei;
                        $data['service_new_imei'] = $new_imei;

                        $QImei = new Application_Model_Imei();
                        $QCSI = new Application_Model_ChangeSalesImei();

                        $checkImeiNew = $QImei->checkImeiReadyService($new_imei,$data['warehouse_id'],$data['good_id'],$data['good_color']);

                        if(!$checkImeiNew){
                            return array(
                                'code' => -2,
                                'message' => 'Invalid Imei!',
                            );
                        }

                        $checkImeiNewCSI = $QCSI->getImeiChangeSalesImei([$new_imei]);

                        if($checkImeiNewCSI){
                            return array(
                                'code' => -2,
                                'message' => 'Invalid Imei on change!',
                            );
                        }

                        $QCheckmoney = new Application_Model_Checkmoney();

                        $data_ch2 = array(
                            'd_id'                  => $distributor_id,
                            'payment'               => $currentTime,
                            'pay_time'              => $currentTime,
                            'pay_service'           => 0,
                            'output'                => 0,
                            'pay_money'             => 0,
                            'type'                  => 2,
                            'sn'                    => $sn,
                            'user_id'               => $userStorage->id,
                            'create_by'             => $userStorage->id,
                            'create_at'             => $currentTime,
                            'note'                  => '',
                            'company_id'            => 1,
                            'sales_confirm_date'    => $currentTime,
                            'sales_confirm_id'      => $userStorage->id,
                            'finance_confirm_id'    => $userStorage->id,
                            'finance_confirm_date'  => $currentTime,
                            'payment_surplus'       => 0,
                            'lack_of_money'         => 0
                        );

                        $QCheckmoney->insert($data_ch2);

                        $data_ch1 = array(
                            'd_id'                  => $distributor_id,
                            'bank'                  => 10,//No Payment
                            'pay_money'             => 0,
                            'pay_servicecharge'     => 0,
                            'pay_banktransfer'      => 0,
                            'pay_service'           => 0,
                            'type'                  => 1,
                            'pay_time'              => $currentTime,
                            'bank_serial'           => null,
                            'bank_transaction_code' => null,
                            'note'                  => '',
                            'content'               => null,
                            'company_id'            => 1,
                            'sn'                    => $sn,
                            'file_pay_slip'         => '',
                            'user_id'               => $userStorage->id,
                            'create_by'             => $userStorage->id,
                            'create_at'             => $currentTime,
                            'sales_confirm_id'      => $userStorage->id,
                            'sales_confirm_date'    => $currentTime,
                            'addition'              => 0,
                            'payment_surplus'       => 0,
                            'lack_of_money'         => 0
                        );

                        $QCheckmoney->insert($data_ch1);
                    }

                    if ($data['cat_id'] == ACCESS_CAT_ID and $swap_acc) {

                        $data['outmysql_time']     = $currentTime;
                        $data['outmysql_user']     = $userStorage->id;

                        $data['sales_confirm_date'] = $currentTime;
                        $data['sales_confirm_id'] = $userStorage->id;

                        $data['finance_confirm_date'] = $currentTime;
                        $data['finance_confirm_id'] = $userStorage->id;

                        $data['pay_time'] = $currentTime;
                        $data['pay_user'] = $userStorage->id;

                        $data['shipping_yes_time'] = $currentTime;
                        $data['shipping_yes_id'] = $userStorage->id;

                        $data['confirm_so'] = 1;

                        $data['service_swap_acc_status'] = 1;

                        $QCheckmoney = new Application_Model_Checkmoney();

                        $data_ch2 = array(
                            'd_id'                  => $distributor_id,
                            'payment'               => $currentTime,
                            'pay_time'              => $currentTime,
                            'pay_service'           => 0,
                            'output'                => 0,
                            'pay_money'             => 0,
                            'type'                  => 2,
                            'sn'                    => $sn,
                            'user_id'               => $userStorage->id,
                            'create_by'             => $userStorage->id,
                            'create_at'             => $currentTime,
                            'note'                  => '',
                            'company_id'            => 1,
                            'sales_confirm_date'    => $currentTime,
                            'sales_confirm_id'      => $userStorage->id,
                            'finance_confirm_id'    => $userStorage->id,
                            'finance_confirm_date'  => $currentTime,
                            'payment_surplus'       => 0,
                            'lack_of_money'         => 0
                        );

                        $QCheckmoney->insert($data_ch2);

                        $data_ch1 = array(
                            'd_id'                  => $distributor_id,
                            'bank'                  => 10,//No Payment
                            'pay_money'             => 0,
                            'pay_servicecharge'     => 0,
                            'pay_banktransfer'      => 0,
                            'pay_service'           => 0,
                            'type'                  => 1,
                            'pay_time'              => $currentTime,
                            'bank_serial'           => null,
                            'bank_transaction_code' => null,
                            'note'                  => '',
                            'content'               => null,
                            'company_id'            => 1,
                            'sn'                    => $sn,
                            'file_pay_slip'         => '',
                            'user_id'               => $userStorage->id,
                            'create_by'             => $userStorage->id,
                            'create_at'             => $currentTime,
                            'sales_confirm_id'      => $userStorage->id,
                            'sales_confirm_date'    => $currentTime,
                            'addition'              => 0,
                            'payment_surplus'       => 0,
                            'lack_of_money'         => 0
                        );

                        $QCheckmoney->insert($data_ch1);
                    }

                    //print_r($job_sn);die;
                    $QJobNumber = new Application_Model_JobNumber(); 
                    $result_jobno  = $QJobNumber->getJobNumber($job_sn,$sn);
                    //print_r($result_jobno);die;
                    if($result_jobno){
                        $job_data = array(
                            'job_sn' => $job_sn,
                            'sales_order' => $sn,
                            'job_type' => $job_type,
                            'update_date' => date('Y-m-d H:i:s'),
                            'update_by' => $userStorage->id
                        );
                        $where_jobno = array();
                        $where_jobno[] = $QJobNumber->getAdapter()->quoteInto('job_sn = ?', $job_sn);
                        $where_jobno[] = $QJobNumber->getAdapter()->quoteInto('sales_order = ?', $sn);

                       // print_r($job_data);die;
                        $result_status = $QJobNumber->update($job_data,$where_jobno);
                    }else if($sn !='' && $job_sn !=''){
                       $job_data = array(
                        'job_sn' => $job_sn,
                        'sales_order' => $sn,
                        'job_type' => $job_type,
                        'd_id' => $distributor_id,
                        'create_date' => date('Y-m-d H:i:s'),
                        'create_by' => $userStorage->id
                    );
                        //print_r($job_data);die;
                       $result_status = $QJobNumber->insert($job_data);
                   }

               }

               if(!$check_money_transfer){

                    // START : Bypass Sale confirm and Finance confirm
                $data['sales_confirm_date'] = $currentTime;
                $data['sales_confirm_id'] = $userStorage->id;

                $data['finance_confirm_date'] = $currentTime;
                $data['finance_confirm_id'] = $userStorage->id;

                $data['pay_time'] = $currentTime;
                $data['pay_user'] = $userStorage->id;

                $data['shipping_yes_time'] = $currentTime;
                $data['shipping_yes_id'] = $userStorage->id;

                $data['confirm_so'] = 1;

                $QCheckmoney = new Application_Model_Checkmoney();

                $data_ch2 = array(
                    'd_id'                  => $distributor_id,
                    'payment'               => $currentTime,
                    'pay_time'              => $currentTime,
                    'pay_service'           => 0,
                    'output'                => 0,
                    'pay_money'             => 0,
                    'type'                  => 2,
                    'sn'                    => $sn,
                    'user_id'               => $userStorage->id,
                    'create_by'             => $userStorage->id,
                    'create_at'             => $currentTime,
                    'note'                  => '',
                    'company_id'            => 1,
                    'sales_confirm_date'    => $currentTime,
                    'sales_confirm_id'      => $userStorage->id,
                    'finance_confirm_id'    => $userStorage->id,
                    'finance_confirm_date'  => $currentTime,
                    'payment_surplus'       => 0,
                    'lack_of_money'         => 0
                );

                $QCheckmoney->insert($data_ch2);
                    // START : Bypass Sale confirm and Finance confirm
            }

            if($order_accessories==1){
                $data['order_accessories'] = 1;
            }

               // insert market pond
               // OPPW-1 - PRODUCT WARRANTY = 21921
               // WM04 - ?????????????????????? = 6
            // echo '<pre>';
            // print_r($data);
            // die;


            if ($cat_ids[$k] == 11)
            {
               if($prices[$k]==0 || $prices[$k] == ''|| $price_ext == 0 || $price_ext == '' || $tem_total == 0 || $tem_total == ''){
                if($distributor_id == 21921 && $warehouse_id == 6 || $swap_imei){
                    $id = $QMarket->insert($data);
                }else{
                    throw new Exception("Some product set to Zero. Please try again.");
                }
            }else{
                $id = $QMarket->insert($data);

		// Check Auto Bill  // Auto Confirm
            	$data_arr = $QContactDetail->getFianaceClientPaymentDetail($finance_client);

	     // Sale Receipt
            if($data_arr[0]['sale_receipt'] == null) {
                $sale_receipt = '0';
            }else{
                $sale_receipt = $data_arr[0]['sale_receipt'];
            }

            // Return Order
            if($data_arr[0]['retrun_amount'] == null) {
                $retrun_amount = '0';
            }else{
                $retrun_amount = $data_arr[0]['retrun_amount'];
            }

            // Credit Limit
            if($data_arr[0]['credit_limit'] == null) {
                $credit_limit = '0';
            }else{
                $credit_limit = $data_arr[0]['credit_limit'];
            }

            // Sale Order
            if($data_arr[0]['sale_order_amount'] == null) {
                $sale_order_amount = '0';
            }else{
                $sale_order_amount = $data_arr[0]['sale_order_amount'];
            }

            // Sale Refund
            if($data_arr[0]['sale_refund'] == null) {
                $sale_refund = '0';
            }else{
                $sale_refund = $data_arr[0]['sale_refund'];
            }

            // Contact Note
            if($data_arr[0]['contact_note'] == null) {
                $contact_note = '0';
            }else{
                $contact_note = $data_arr[0]['contact_note'];
            }

            // Total Blance Avalable
            $balance_total = (($sale_receipt + $retrun_amount + $credit_limit) - ($sale_order_amount + $sale_refund)) + $contact_note;

	     // if($balance_total > 0) {

            //     $QFinanceWarehouse = new Application_Model_FinanceWarehouse();

            //     $where = $QDistributor->getAdapter()->quoteInto('id =?',$distributor_id);
            //     $distributor_arr = $QDistributor->fetchRow($where);

            //     $where2 = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$distributor_arr->warehouse_id);
            //     $distributor_upper = $QDistributor->fetchRow($where2);

            //     $where3 = $QFinanceWarehouse->getAdapter()->quoteInto('d_id =?',$distributor_upper->id);
            //     $finance_warehouse = $QFinanceWarehouse->fetchRow($where3);

            //     $data_bill = array(
            //         'finance_confirm_date'  => date('Y-m-d H:i:s'),
            //         'finance_confirm_id'    => $userStorage->id,
            //         'shipping_yes_time'     => date('Y-m-d H:i:s'),
            //         'pay_time'              => date('Y-m-d H:i:s'),
            //         'shipping_yes_id'       => $userStorage->id,
            //         'finance_warehouse'     => $finance_warehouse->id,
            //         'bill_date'             => date('Y-m-d'),
            //         'bill_remark'           => "Auto Bill Order",
            //         'order_status'          => 2
            //     );

            //     $where_bill = $QMarket->getAdapter()->quoteInto('id =?',$id);
            //     $QMarket->update($data_bill,$where_bill);
            // }

		
		if($id) {

		 $QFinanceWarehouse = new Application_Model_FinanceWarehouse();

                 $where = $QDistributor->getAdapter()->quoteInto('id =?',$distributor_id);
                 $distributor_arr = $QDistributor->fetchRow($where);

                 $where2 = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$distributor_arr->warehouse_id);
                 $distributor_upper = $QDistributor->fetchRow($where2);

                 $where3 = $QFinanceWarehouse->getAdapter()->quoteInto('d_id =?',$distributor_upper->id);
                 $finance_warehouse = $QFinanceWarehouse->fetchRow($where3);

                 $data_bill = array(
                     'finance_confirm_date'  => date('Y-m-d H:i:s'),
                     'finance_confirm_id'    => $userStorage->id,
                     'shipping_yes_time'     => date('Y-m-d H:i:s'),
                     'pay_time'              => date('Y-m-d H:i:s'),
                     'shipping_yes_id'       => $userStorage->id,
                     'finance_warehouse'     => $finance_warehouse->id,
                     'bill_date'             => date('Y-m-d'),
                     'bill_remark'           => "Auto Bill Order",
                     'order_status'          => 2
                 );

                 $where_bill = $QMarket->getAdapter()->quoteInto('id =?',$id);
                 $QMarket->update($data_bill,$where_bill);


                $good_cache = $QGood->get_cache();
                $color_cache = $QGoodColor->get_cache();

                $good_name = $good_cache[$good_ids[$k]];
                $color_name = $color_cache[$good_colors[$k]];

                $total_price = $prices[$k]*$nums[$k];

                $contact_data = array(
                    'type'                  => 1,
                    'doc_no'                => $sn,
                    'store_id'              => $insert_store_id,
                    'd_id'                  => $distributor_id,
                    'finance_client_id'     => $finance_client,
                    'status'                => 1,
                    'created_at'            => date('Y-m-d H:i:s'),
                    'created_by'            => $userStorage->id,
                    'description'           => $good_name.' '.$color_name.'*'.$prices[$k].'*'.$nums[$k],
                    'amount'                => $total_price
                );

                $QContactDetail->insert($contact_data);
            }

            }
        }else{
            $id = $QMarket->insert($data);
        }

        if ($data['cat_id'] == PHONE_CAT_ID and $swap_imei) {

            $QImei = new Application_Model_Imei();

            $data_update_imei = array(
                'distributor_id' => $distributor_id,
                'sales_sn' => $sn,
                'sales_id' => $id,
                'out_date' => $currentTime,
                'out_price' => 0,
                'out_user' => $userStorage->id
            );

            $where_update_imei = $QImei->getAdapter()->quoteInto('imei_sn = ?', $new_imei);
            $QImei->update($data_update_imei,$where_update_imei);
        }

        // pond insert log quota 
        // allow quota for warehouse kerry only
        //if (in_array($type, [FOR_RETAILER,FOR_DEMO,FOR_APK]) && $warehouse_id == 36) {
        if (in_array($type, [FOR_RETAILER,FOR_DEMO,FOR_APK])) {
         $select_a = $db->select()
         ->from(array('d' => 'distributor'), array('d.rank','d.quota_channel','d.group_id'));

         $select_a->joinLeft(array('r' => HR_DB.'.regional_market'),'r.id=d.region',array());
         $select_a->joinLeft(array('a' => HR_DB.'.area'),'r.area_id=a.id',array('area_id'=>'a.id'));
         $select_a->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
         $select_a->where('d.id = ?', $distributor_id);
         $distributor = $db->fetchRow($select_a);

               // $dis_type = '';
               // if ($distributor['quota_channel']) {
               //     if ($distributor['quota_channel'] == 1) {
               //        $action_quota = "1";

               //     }else if ($distributor['quota_channel'] == 10) {
               //        $action_quota = "10";

               //     }
               // }else{
               //      if (in_array($distributor['rank'], array(7,8))) {
               //         $action_quota = "7"; 
               //      }else if (in_array($distributor['rank'], array(1,2,5,6))) {
               //        $action_quota = "1";
               //      }else if ($distributor['rank'] == 10) {
               //        $action_quota = "10";
               //      }
               // }

               // Group brand Shop by Dealer to brand shop
         $old_group_id = '';
         if($distributor['group_type_id'] == 11){
            $old_group_id = $distributor['group_type_id'];
            $distributor['group_type_id'] = 10;
        }

        $action_quota = $distributor['group_type_id'];

        if ($action_quota) {
            $toDay = date('Y-m-d');

            $select_q = $db->select()
            ->from(array('o' => 'quota_oppo_by_distributor'), array('o.good_id','o.good_color'));
            $select_q->where('o.quota_date = ?',$toDay);
            $select_q->where('o.d_id = ?',$distributor_id);
            $select_q->where('o.warehouse = ?',$warehouse_id);
            $select_q->where('o.good_id = ?',$good_ids[$k]);
            $select_q->where('o.good_color = ?',$good_colors[$k]);
            $select_q->where('o.status = 1');
            $select_q->where('o.del is null',1);
            $select_q->group(array('o.good_id','o.good_color'));
            $product_quota_by_dis = $db->fetchAll($select_q);

            $select_q_d = $db->select()
            ->from(array('nqd' => 'new_quota_distributor'), array('nqd.good_id'));
            $select_q_d->joinLeft(array('nqdd' => 'new_quota_distributor_details'),'nqdd.nqd_id=nqd.id',array('nqdd.good_color'));
            $select_q_d->where('nqd.quota_date = ?',$toDay);
            $select_q_d->where('nqd.warehouse_id = ?', $warehouse_id);
            $select_q_d->where('nqd.d_id = ?', $distributor_id);
            $select_q_d->where('nqd.order_type = ?', $type);
            $select_q_d->where('nqd.good_id = ?', $good_ids[$k]);
            $select_q_d->where('nqdd.good_color = ?', $good_colors[$k]);
            $select_q_d->where('nqdd.num > 0');
            $select_q_d->where('nqd.status = 1');
            $select_q_d->where('nqdd.status = 1');
            $select_q_d->group(array('nqd.good_id','nqdd.good_color'));
            $new_quota_by_distributor = $db->fetchAll($select_q_d);                           

            if (isset($product_quota_by_dis) and $product_quota_by_dis and $type == FOR_RETAILER) {

                $QLogQuotaDis = new Application_Model_LogQuotaTranDistributor();
                foreach ($product_quota_by_dis as $key => $value) {
                    if ($value['good_id'] == $good_ids[$k] && $value['good_color'] == $good_colors[$k]) {
                     $data_q = array(
                        'market_id'        => intval( $id ),
                        'warehouse_id'     => intval( $warehouse_id ),
                        'd_id'             => intval( $distributor_id ),
                        'cat_id'           => intval( $cat_ids[$k] ),
                        'good_id'          => intval( $good_ids[$k] ),
                        'good_color'       => intval( $good_colors[$k] ),
                        'num'              => intval( $nums[$k] ),
                        'add_time'         => $currentTime,
                        'user_id'          => $userStorage->id,
                        'sn'               => $sn,
                    );

                     $QLogQuotaDis->insert($data_q);
                 }
             }

         }else if(isset($new_quota_by_distributor) and $new_quota_by_distributor and $type == FOR_RETAILER){

            $QLogQuotaDis = new Application_Model_LogQuotaTranDistributor();
            foreach ($new_quota_by_distributor as $key => $value) {
                if ($value['good_id'] == $good_ids[$k] && $value['good_color'] == $good_colors[$k]) {
                 $data_q = array(
                    'market_id'        => intval( $id ),
                    'warehouse_id'     => intval( $warehouse_id ),
                    'd_id'             => intval( $distributor_id ),
                    'cat_id'           => intval( $cat_ids[$k] ),
                    'good_id'          => intval( $good_ids[$k] ),
                    'good_color'       => intval( $good_colors[$k] ),
                    'good_type'        => $type,
                    'num'              => intval( $nums[$k] ),
                    'add_time'         => $currentTime,
                    'user_id'          => $userStorage->id,
                    'sn'               => $sn,
                );

                 $QLogQuotaDis->insert($data_q);
             }
         }

     }else{

        $good_type = $type;

        $select_q = $db->select()
        ->from(array('o' => 'quota_oppo'), array('o.good_id','o.good_color'));
        $select_q->where('o.quota_date = ?',$toDay);
        $select_q->where('o.dis_type = ?',$action_quota);
        $select_q->where('o.good_type = ?',$good_type);
        $select_q->where('o.warehouse_id = ?',$warehouse_id);
        $select_q->group(array('o.good_id','o.good_color'));
                   // echo $select_q;die;
        $product_quota = $db->fetchAll($select_q);

        if ($product_quota) {
            $QLogQuota = new Application_Model_LogQuotaTran();
            foreach ($product_quota as $key => $value) {
                if ($value['good_id'] == $good_ids[$k] && $value['good_color'] == $good_colors[$k]) {
                 $data_q = array(
                    'market_id'        => intval( $id ),
                    'warehouse_id'     => intval( $warehouse_id ),
                    'd_id'             => intval( $distributor_id ),
                    'cat_id'           => intval( $cat_ids[$k] ),
                    'good_id'          => intval( $good_ids[$k] ),
                    'good_color'       => intval( $good_colors[$k] ),
                    'good_type'       => $good_type,
                    'num'              => intval( $nums[$k] ),
                    'add_time'         => $currentTime,
                    'user_id'          => $userStorage->id,
                    'sn'               => $sn,
                );

                 $QLogQuota->insert($data_q);
             }
         }
     }
 }
}             


        }// End insert quota


               // FoceSale ????????
        $findFoceSale = new Application_Model_Warehouse();
        $QForcesale = new Application_Model_ForceSaleDetail();
        $paramsFoce = array(
            'good_id'          => $good_ids[$k],
            'warehouse_id'     => intval( $warehouse_id ),
            'type'             => intval( $type ),
            'd_id'             => intval( $distributor_id ),
            'color'            => $good_colors[$k],
        );

        $foce_sale = $findFoceSale->findForceSales($paramsFoce) ; 

        if ($foce_sale) {


           $forcesale = $QForcesale->forceSale($foce_sale['campaign_id']);
           $total_foce = 0;
           foreach ($forcesale as $key => $value) {
               $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($value['g_id'],$sn);

               $data['good_id']          = $value['g_id'];
               $data['good_color']       = $value['color'];
               $data['price']            = '0';
               $data['total']            = '0';
               $data['cat_id']            = $value['cat_id'];
               $data['sale_off_percent'] = '100';

               if ($heckAccessories !='') {
                $total_foce = $nums[$k]*$value['g_id_num'];

                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                $data_update['num']              = ($heckAccessories['num']+$total_foce);
                        // print_r($data);echo '==';die;
                $id = $QMarket->update($data_update,$where);
            }else{
                $total_foce = $nums[$k]*$value['g_id_num'];


                $data['num']              = $total_foce;
                         // print_r($data);echo '++';die;
                $id = $QMarket->insert($data);
            }


        }


    }
                //endPungpond

                //Tanong New Sales Order Format 20160312 1324
                /*
                if ($sn!=''){
                    //print_r($sn);die;
                    $this->getSalesOrderNo_Ref($sn);
                }
                */
            }



            //Tanong
            $Total_Amount=$QMarket->LoadSalesOrderAmount($distributor_id);
            $total_amount_all = $Total_Amount[0]['total_acmount'];
            $total_ptice = $data['total'];
            $delivery_fee_all = $Total_Amount[0]['delivery_fee'];


            
            if ($delivery_fee_all >0 && $total_amount_all>=10000)
            { //update delivery_fee =0 all sales order in 1 day when sales order amount all 1 day >10000
             //   print_r($Total_Amount);
             // echo 'chk_total_amount_all >>'.$total_amount_all;
             // echo 'delivery_fee_all >>'.$delivery_fee_all;
             // die;
                $data_delivery = array(
                    'delivery_fee' => 0,
                );

                //$data['delivery_fee'] = 0;
                $where = $QMarket->getAdapter()->quoteInto("(DATE_FORMAT(add_time,'%Y%m%d')=DATE_FORMAT(NOW(),'%Y%m%d'))", null);
                $where = $QMarket->getAdapter()->quoteInto('d_id = ?', $distributor_id);
               // $QMarket->update($data_delivery, $where);
            }

            

        }
    }

    if($bs_campaign ==1)
    {
        $QPhoneNumber = new Application_Model_PhoneNumber();
        $phone = $QPhoneNumber->getPhone_number("",$sn);
        $data_phone = array(
            'sales_order' => $sn,
            'phone_number_sn' => $phone_number,
            'update_date' => $currentTime,
            'update_by' => $userStorage->id,
            'status' => 1,
        );   
        //print_r($data_phone);die;
        if ($phone['phone_number_sn'] !='')
        {
            $where = $QPhoneNumber->getAdapter()->quoteInto('sales_order = ?', $sn);
            $QPhoneNumber->update($data_phone,$where);
        }else{
            $QPhoneNumber->insert($data_phone);
        }
    }

    if($bs_campaign !=1)    // Not Operation Campaign 1= USE
    {
    //????????????????? Gift Box

        $data['price_ext'] = 0.00;
        $data['total_ext'] = 0.00;

        $Switch = new Application_Model_Switch();
        $switch_i =  $Switch->fetchRow();

        if ($switch_i['switch'] == 1) {

           $db = Zend_Registry::get('db');
           $kr = array(
                    // '57',
                    // '189',
            '318',
            '580',
            '606',
            '649',
            '992',
                    // '1076',
            '3172',
            '10021',
            '10216',
            '10217',
            '10343',
            '10362',
            '10449',
            '25054',
                    // '26704',
            '34649',
            '34650');                     
        if ($type == 2 || $type == 5) { // type Demo APK


        }else{

        // if($good_ids){ 

        //     $QAG = new Application_Model_AutoGiftbox();

        //     $auto_gift = $QAG->checkAutoGiftbox($good_ids);
        //     if(isset($auto_gift)){

        //         foreach ($auto_gift as $key) {

        //                  $product         = [$good_ids];
        //                  // $cat_ids_ag      = $auto_gift['cat_id'];
        //                  // $product      = $auto_gift['good_id'];
        //                  $cat_id_give     = $auto_gift['gift_cat_id'];
        //                  $good_id_give    = $auto_gift['gift_good_id'];
        //                  $good_color_give = $auto_gift['gift_good_color'];
        //                  $all_day     = $auto_gift['all_date'];
        //                  $start_date  = $auto_gift['start_date'];
        //                  $end_date    = $auto_gift['end_date'];

        //                  // kerry dealer / hub / KR Dealer
        //                    $findFoceSale = new Application_Model_Warehouse();   
        //                     if(isset($all_day) and $all_day ==1){ 
        //                     if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) { 
        //                         if(!in_array($distributor_id,$kr)){
        //                             $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
        //                             $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=".$cat_id_give."|good_id=".$good_id_give."|desc=1|search=1',0,40,@total)");
        //                             $result = $stock->fetchAll();
        //                             $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
        //                             $stock->closeCursor();
        //                             $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($good_id_give,$sn);
        //                             if ($stock_sun >= $total_produce && $total_produce > 0) {
        //                                 $data['good_id']          = $good_id_give;
        //                                 $data['good_color']       = $good_color_give;
        //                                 $data['price']            = '0';
        //                                 $data['total']            = '0';
        //                                 $data['cat_id']           = $cat_id_give;
        //                                 $data['sale_off_percent'] = '100';
        //                                 $data['num']              = $total_produce;

        //                                 if($heckAccessories !='') {
        //                                    $where = $QMarket->getAdapter()->quoteInto('id = ?',$heckAccessories['id']);
        //                                    $id = $QMarket->update($data,$where);
        //                                 }else{
        //                                    $id = $QMarket->insert($data);
        //                                 }
        //                             }
        //                         }
        //                     }
        //                 }else{

        //                     if(isset($start_date) and $start_date < date('Y-m-d H:i:s') and isset($end_date) and $end_date > date('Y-m-d H:i:s')){ 
        //                         if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
        //                             if(!in_array($distributor_id,$kr)){
        //                                 $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
        //                                 $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=".$cat_id_give."|good_id=".$good_id_give."|desc=1|search=1',0,40,@total)");
        //                                 $result = $stock->fetchAll();
        //                                 $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
        //                                 $stock->closeCursor();
        //                                 $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($good_id_give,$sn);
        //                                 if ($stock_sun >= $total_produce && $total_produce > 0) {
        //                                     $data['good_id']          = $good_id_give;
        //                                     $data['good_color']       = $good_color_give;
        //                                     $data['price']            = '0';
        //                                     $data['total']            = '0';
        //                                     $data['cat_id']           = $cat_id_give;
        //                                     $data['sale_off_percent'] = '100';
        //                                     $data['num']              = $total_produce;

        //                                     if($heckAccessories !='') {
        //                                        $where = $QMarket->getAdapter()->quoteInto('id = ?',$heckAccessories['id']);
        //                                        $id = $QMarket->update($data,$where);
        //                                     }else{
        //                                        $id = $QMarket->insert($data);
        //                                     }
        //                                 }
        //                             }
        //                         }
        //                     }// if date
        //                 }// else

        //                // Brand shop
        //                $findFoceSale = new Application_Model_Warehouse();
        //                if(isset($all_day) and $all_day ==1){ 
        //                     if ($warehouse_id == 92 AND $rank == 13) { 
        //                         if(!in_array($distributor_id,$kr)){
        //                             $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
        //                             $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=".$cat_id_give."|good_id=".$good_id_give."|desc=1|search=1',0,40,@total)");
        //                             $result = $stock->fetchAll();
        //                             $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
        //                             $stock->closeCursor();
        //                             $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($good_id_give,$sn);
        //                             if ($stock_sun >= $total_produce && $total_produce > 0) {
        //                                 $data['good_id']          = $good_id_give;
        //                                 $data['good_color']       = $good_color_give;
        //                                 $data['price']            = '0';
        //                                 $data['total']            = '0';
        //                                 $data['cat_id']           = $cat_id_give;
        //                                 $data['sale_off_percent'] = '100';
        //                                 $data['num']              = $total_produce;

        //                                 if($heckAccessories !='') {
        //                                    $where = $QMarket->getAdapter()->quoteInto('id = ?',$heckAccessories['id']);
        //                                    $id = $QMarket->update($data,$where);
        //                                 }else{
        //                                    $id = $QMarket->insert($data);
        //                                 }
        //                             }
        //                         }
        //                     }
        //                 }else{

        //                     if(isset($start_date) and $start_date < date('Y-m-d H:i:s') and isset($end_date) and $end_date > date('Y-m-d H:i:s')){ 
        //                         if ($warehouse_id == 92 AND $rank == 13) {
        //                             if(!in_array($distributor_id,$kr)){
        //                                 $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
        //                                 $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=".$cat_id_give."|good_id=".$good_id_give."|desc=1|search=1',0,40,@total)");
        //                                 $result = $stock->fetchAll();
        //                                 $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
        //                                 $stock->closeCursor();
        //                                 $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($good_id_give,$sn);
        //                                 if ($stock_sun >= $total_produce && $total_produce > 0) {
        //                                     $data['good_id']          = $good_id_give;
        //                                     $data['good_color']       = $good_color_give;
        //                                     $data['price']            = '0';
        //                                     $data['total']            = '0';
        //                                     $data['cat_id']           = $cat_id_give;
        //                                     $data['sale_off_percent'] = '100';
        //                                     $data['num']              = $total_produce;

        //                                     if($heckAccessories !='') {
        //                                        $where = $QMarket->getAdapter()->quoteInto('id = ?',$heckAccessories['id']);
        //                                        $id = $QMarket->update($data,$where);
        //                                     }else{
        //                                        $id = $QMarket->insert($data);
        //                                     }
        //                                 }
        //                             }
        //                         }
        //                     }// if date
        //                 }// else   

        //         } //end foreach
        //     }
        // } //end if(good_ids)



    // START : Kerry Dealer / Hub / KR Dealer 

            if ($good_ids) {
        // if (date('Y-m-d H:i:s') > '2017-04-03 00:00:00'){
        // if (in_array('208', $good_ids)) {//R9s
        //     $findFoceSale = new Application_Model_Warehouse();
        //     if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
        //         if(!in_array($distributor_id,$kr)){
        //         $product = array(208,142,256,267);
        //         $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
        //         $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=216|desc=1|search=1',0,40,@total)");
        //         $result = $stock->fetchAll();
        //         $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
        //         $stock->closeCursor();
        //         $goodtung = 216; //05GB0005 Gift Box (Mai Davika)
        //         $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);

        //         if ($stock_sun >= $total_produce && false) {

        //                 if ($stock_sun >= $total_produce && $total_produce > 0) {
        //                     $data['good_id']          = '216';
        //                     $data['good_color']       = '1';
        //                     $data['price']            = '0';
        //                     $data['total']            = '0';
        //                     $data['cat_id']            = '12';
        //                     $data['sale_off_percent'] = '100';
        //                     $data['num']              = $total_produce;
        //                     if ($heckAccessories !='') {
        //                         $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
        //                         $id = $QMarket->update($data,$where);
        //                     }else{

        //                            $id = $QMarket->insert($data);
        //                          }
        //                 }

        //             }else{
        //                 $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
        //                 $stock2 = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=215|desc=1|search=1',0,40,@total)");
        //                 $result2 = $stock2->fetchAll();
        //                 $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
        //                 $stock2->closeCursor();
        //                 $goodtung2 = 215; //05GB0004 Gift Box (Lee Min Ho)
        //                 $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
        //                 if ($stock_sun2) {

        //                         if ($stock_sun2 >= $total_produce2) {
        //                             $data['good_id']          = '215';
        //                             $data['good_color']       = '1';
        //                             $data['price']            = '0';
        //                             $data['total']            = '0';
        //                             $data['cat_id']            = '12';
        //                             $data['sale_off_percent'] = '100';
        //                             $data['num']              = $total_produce2;
        //                             if ($heckAccessories !='') {
        //                                 $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
        //                                 $id = $QMarket->update($data,$where);
        //                             }else{

        //                                    $id = $QMarket->insert($data);
        //                                  }
        //                         }

        //                     } 
        //             }
        //         }//kr      

        //     }
        // }//if good

        // }//Time

        if (in_array('210', $good_ids)) {//CPH1611 R9sPlus
            $findFoceSale = new Application_Model_Warehouse();
            if (date('Y-m-d H:i:s') > '2017-04-03 00:00:00'){
                if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                    if(!in_array($distributor_id,$kr)){
                        $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                        $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                        $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                        $result = $stock->fetchAll();
                        $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                        $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }else{
                        // $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                        // $stock2 = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=215|desc=1|search=1',0,40,@total)");
                        // $result2 = $stock2->fetchAll();
                        // $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
                        // $stock2->closeCursor();
                        // $goodtung2 = 215; //05GB0004 Gift Box (Lee Min Ho)
                        // $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
                        // if ($stock_sun2) {

                        //         if ($stock_sun2 >= $total_produce2) {
                        //             $data['good_id']          = '215';
                        //             $data['good_color']       = '1';
                        //             $data['price']            = '0';
                        //             $data['total']            = '0';
                        //             $data['cat_id']            = '12';
                        //             $data['sale_off_percent'] = '100';
                        //             $data['num']              = $total_produce2;
                        //             if ($heckAccessories !='') {
                        //                 $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                        //                 $id = $QMarket->update($data,$where);
                        //             }else{

                        //                    $id = $QMarket->insert($data);
                        //                  }
                        //         }

                        //     }
             }
                }//kr      

            }
        }

        
    }
        if (in_array('142', $good_ids)) {//F1s
            $findFoceSale = new Application_Model_Warehouse();
            if (date('Y-m-d H:i:s') > '2017-04-03 00:00:00'){
                if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                    if(!in_array($distributor_id,$kr)){
                        $product = array(142);
                        $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                        $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=216|desc=1|search=1',0,40,@total)");
                        $result = $stock->fetchAll();
                        $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                        $stock->closeCursor();
                $goodtung = 216; //05GB0005 Gift Box (Mai Davika)
                $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && false) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '216';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }else{
                $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock2 = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result2 = $stock2->fetchAll();
                $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
                $stock2->closeCursor();
                        $goodtung2 = 324; //06GB0003 2 in 1 Gift Box
                        $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
                        if ($stock_sun2) {

                            if ($stock_sun2 >= $total_produce2) {
                                $data['good_id']          = '324';
                                $data['good_color']       = '1';
                                $data['price']            = '0';
                                $data['total']            = '0';
                                $data['cat_id']            = '12';
                                $data['sale_off_percent'] = '100';
                                $data['num']              = $total_produce2;
                                if ($heckAccessories !='') {
                                    $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                    $id = $QMarket->update($data,$where);
                                }else{

                                 $id = $QMarket->insert($data);
                             }
                         }

                     }
                 }
               }//kr     

           }
       }


   }


    if (in_array('211', $good_ids)) {//CPH1701 A57
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2017-06-01 00:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                    $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }else{
                        // $product = array(211,300);
                        // $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                        // $stock2 = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=215|desc=1|search=1',0,40,@total)");
                        // $result2 = $stock2->fetchAll();
                        // $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
                        // $stock2->closeCursor();
                        // $goodtung2 = 215; //Gift Box  (4 in 1) 30GF0006
                        // $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
                        // if ($stock_sun2) {

                        //         if ($stock_sun2 >= $total_produce2) {
                        //             $data['good_id']          = '215';
                        //             $data['good_color']       = '1';
                        //             $data['price']            = '0';
                        //             $data['total']            = '0';
                        //             $data['cat_id']            = '12';
                        //             $data['sale_off_percent'] = '100';
                        //             $data['num']              = $total_produce2;
                        //             if ($heckAccessories !='') {
                        //                 $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                        //                 $id = $QMarket->update($data,$where);
                        //             }else{

                        //                    $id = $QMarket->insert($data);
                        //                  }
                        //         }

                        //     }
             }
               }//kr     

           }
       }


   }

    // if (in_array('256', $good_ids)) {//A77
    //         $findFoceSale = new Application_Model_Warehouse();
    //         if (date('Y-m-d H:i:s') > '2017-05-30 00:00:00'){
    //         if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
    //             if(!in_array($distributor_id,$kr)){
    //             $product = array(142,256,267,211);
    //             $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
    //             $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=215|desc=1|search=1',0,40,@total)");
    //             $result = $stock->fetchAll();
    //             $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
    //             $stock->closeCursor();
    //             $goodtung = 215; //05GB0004 Gift Box (Lee Min Ho)
    //             $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
    //             if ($stock_sun >= $total_produce && $total_produce > 0) {
    //                 if ($stock_sun >= $total_produce && $total_produce > 0) {
    //                     $data['good_id']          = '215';
    //                     $data['good_color']       = '1';
    //                     $data['price']            = '0';
    //                     $data['total']            = '0';
    //                     $data['cat_id']            = '12';
    //                     $data['sale_off_percent'] = '100';
    //                     $data['num']              = $total_produce;

    //                     if ($heckAccessories !='') {
    //                     $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
    //                     $id = $QMarket->update($data,$where);
    //                 }else{
    //                        $id = $QMarket->insert($data);
    //                      }
    //                 }

    //                 }else{
    //                     // $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
    //                     // $stock2 = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=215|desc=1|search=1',0,40,@total)");
    //                     // $result2 = $stock2->fetchAll();
    //                     // $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
    //                     // $stock2->closeCursor();
    //                     // $goodtung2 = 215; //05GB0004 Gift Box (Lee Min Ho)
    //                     // $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
    //                     // if ($stock_sun2) {

    //                     //         if ($stock_sun2 >= $total_produce2) {
    //                     //             $data['good_id']          = '215';
    //                     //             $data['good_color']       = '1';
    //                     //             $data['price']            = '0';
    //                     //             $data['total']            = '0';
    //                     //             $data['cat_id']            = '12';
    //                     //             $data['sale_off_percent'] = '100';
    //                     //             $data['num']              = $total_produce2;
    //                     //             if ($heckAccessories !='') {
    //                     //                 $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
    //                     //                 $id = $QMarket->update($data,$where);
    //                     //             }else{

    //                     //                    $id = $QMarket->insert($data);
    //                     //                  }
    //                     //         }

    //                     //     }
    //                 }
    //            }//kr     

    //         }
    //     }


    // }

    if (in_array('267', $good_ids)) {//CPH1613 (R9s Pro)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2017-09-23 00:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                    $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }else{
                        // $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                        // $stock2 = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=215|desc=1|search=1',0,40,@total)");
                        // $result2 = $stock2->fetchAll();
                        // $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
                        // $stock2->closeCursor();
                        // $goodtung2 = 215; //05GB0004 Gift Box (Lee Min Ho)
                        // $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
                        // if ($stock_sun2) {

                        //         if ($stock_sun2 >= $total_produce2) {
                        //             $data['good_id']          = '215';
                        //             $data['good_color']       = '1';
                        //             $data['price']            = '0';
                        //             $data['total']            = '0';
                        //             $data['cat_id']            = '12';
                        //             $data['sale_off_percent'] = '100';
                        //             $data['num']              = $total_produce2;
                        //             if ($heckAccessories !='') {
                        //                 $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                        //                 $id = $QMarket->update($data,$where);
                        //             }else{

                        //                    $id = $QMarket->insert($data);
                        //                  }
                        //         }

                        //     }
             }
               }//kr     

           }
       }


   }


    if (in_array('299', $good_ids)) {//CPH1723 (F5)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2017-11-09 12:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 324; //06GB0003 2 in 1 Gift Box
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '324';
                            $data['good_color']       = '1';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }

    }


    if (in_array('300', $good_ids)) {//CPH1725 (Phone F5 Youth)
        $findFoceSale = new Application_Model_Warehouse();
        if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
            if(!in_array($distributor_id,$kr)){
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                    $goodtung = 324; //06GB0003 2 in 1 Gift Box
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '324';
                            $data['good_color']       = '1';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }else{
                    //     $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                    //     $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    //     $stock2 = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                    //     $result2 = $stock2->fetchAll();
                    //     $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
                    //     $stock2->closeCursor();
                    //     $goodtung2 = 324; //06GB0003 2 in 1 Gift Box
                    //     $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
                    //     if ($stock_sun2) {

                    //             if ($stock_sun2 >= $total_produce2) {
                    //                 $data['good_id']          = '324';
                    //                 $data['good_color']       = '1';
                    //                 $data['price']            = '0';
                    //                 $data['total']            = '0';
                    //                 $data['cat_id']            = '12';
                    //                 $data['sale_off_percent'] = '100';
                    //                 $data['num']              = $total_produce2;
                    //                 if ($heckAccessories !='') {
                    //                     $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                    //                     $id = $QMarket->update($data,$where);
                    //                 }else{

                    //                        $id = $QMarket->insert($data);
                    //                      }
                    //             }

                    //         }
                 }
                }//kr     

            }

        }


    if (in_array('301', $good_ids)) {//CPH1727 (F5 6GB)
        $findFoceSale = new Application_Model_Warehouse();
        if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
            if(!in_array($distributor_id,$kr)){
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                    $goodtung = 324; //06GB0003 2 in 1 Gift Box
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '324';
                            $data['good_color']       = '1';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }

        }

    if (in_array('310', $good_ids)) {//CPH1819 (Phone F7)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2018-07-17 00:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                $goodtung = 324; //2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }
            }//kr     

        }
    }
}

    if (in_array('311', $good_ids)) {//CPH1821 (Phone F7 128 GB)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2018-07-17 00:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                $goodtung = 324; //2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }
            }//kr     

        }
    }
}


    if (in_array('313', $good_ids)) {//CPH1729(4G) (A83 4+64G)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2018-05-05 00:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 324; //06GB0003 2 in 1 Gift Box
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '324';
                            $data['good_color']       = '1';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }


    if (in_array('371', $good_ids)) {//CPH1969 (Phone F11 PRO)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2019-03-27 12:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 324; //06GB0003 2 in 1 Gift Box
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '324';
                            $data['good_color']       = '1';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }


    if (in_array('392', $good_ids)) {//CPH1969 6+128G (Phone F11 PRO)
        $findFoceSale = new Application_Model_Warehouse();
        if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
            if(!in_array($distributor_id,$kr)){
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }
            }//kr     

        }
    }


    if (in_array('321', $good_ids)) {//CPH1831 (Phone R15 PRO)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2018-05-31 00:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(321);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=322|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 322; //06GB0002 R15 Pro Gift Box
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '322';
                            $data['good_color']       = '7';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }


    if (in_array('340', $good_ids)) {//CPH1871(01MBOPPOCPH1871) (FIND X)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2018-08-14 00:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(340);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=342|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 342; //06GB0004 FIND X Car Charger Gift Box
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '342';
                            $data['good_color']       = '2';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }


    if (in_array('340', $good_ids)) {//CPH1871(01MBOPPOCPH1871) (FIND X)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2018-08-14 00:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(340);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=343|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 343; //06GB0005 FIND X Tripod Gift Box
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '343';
                            $data['good_color']       = '2';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }

    if (in_array('345', $good_ids)) {//CPH1823(01MBOPPOCPH1823) (Phone F9)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2018-08-30 00:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(345);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=341|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 341; //06SC0001 SIM CARD
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '341';
                            $data['good_color']       = '1';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }else{
                    $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                    $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock2 = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                    $result2 = $stock2->fetchAll();
                    $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
                    $stock2->closeCursor();
                        $goodtung2 = 324; //2 in 1 Gift Box
                        $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
                        if ($stock_sun2) {

                            if ($stock_sun2 >= $total_produce2) {
                                $data['good_id']          = '324';
                                $data['good_color']       = '1';
                                $data['price']            = '0';
                                $data['total']            = '0';
                                $data['cat_id']            = '12';
                                $data['sale_off_percent'] = '100';
                                $data['num']              = $total_produce2;
                                if ($heckAccessories !='') {
                                    $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                    $id = $QMarket->update($data,$where);
                                }else{

                                 $id = $QMarket->insert($data);
                             }
                         }

                     }
                 }

                }//kr     

            }
        }
    }


    if (in_array('345', $good_ids)) {//CPH1823(01MBOPPOCPH1823) (Phone F9)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2018-12-20 00:00:00' && date('Y-m-d H:i:s') < '2018-12-25 00:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(345);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product,17);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=368|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 368; //06MC0004 (F9 VIP Card - Green)
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '368';
                            $data['good_color']       = '17';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }


    if (in_array('353', $good_ids)) {//CPH1877(Phone R17 PRO)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2018-12-03 00:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(353);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=362|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 362; //06GB0007 (R17 Pro Tripod Gift Box)
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '362';
                            $data['good_color']       = '9';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }


    if (in_array('392', $good_ids)) {//CPH1969 6+128G (Phone F11 PRO)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2019-05-20 00:00:00' && date('Y-m-d H:i:s') < '2019-05-21 00:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(392);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product,5);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=397|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 397; //06MC0006 (F11 Pro VIP Card (Gray))
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '397';
                            $data['good_color']       = '5';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }


    if (in_array('403', $good_ids)) {//CPH1919 (Phone RENO 10X)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2019-06-19 00:00:00'){
            if (($warehouse_id == 36) AND ($rank == 7 || $rank == 8 || $rank == 14)) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(403);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=36|cat_id=12|good_id=412|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 412; //06GB0009 (Reno Tripod Gift Box)
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '412';
                            $data['good_color']       = '2';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }


    // END : Kerry Dealer / Hub / KR Dealer 

    // START : Brand Shop By Dealer

    // if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
    //     if (in_array('208', $good_ids)) {//R9s
    //         $findFoceSale = new Application_Model_Warehouse();
    //         if ($warehouse_id == 92 AND $rank == 13) {
    //             if(!in_array($distributor_id,$kr)){
    //             $product = array(208,142,256,267);
    //             $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
    //             $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=216|desc=1|search=1',0,40,@total)");
    //             $result = $stock->fetchAll();
    //             $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
    //             $stock->closeCursor();
    //             $goodtung = 216; //05GB0005 Gift Box (Mai Davika)
    //             $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);

    //             if ($stock_sun >= $total_produce && false) {

    //                     if ($stock_sun >= $total_produce && $total_produce > 0) {
    //                         $data['good_id']          = '216';
    //                         $data['good_color']       = '1';
    //                         $data['price']            = '0';
    //                         $data['total']            = '0';
    //                         $data['cat_id']            = '12';
    //                         $data['sale_off_percent'] = '100';
    //                         $data['num']              = $total_produce;
    //                         if ($heckAccessories !='') {
    //                             $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
    //                             $id = $QMarket->update($data,$where);
    //                         }else{

    //                                $id = $QMarket->insert($data);
    //                              }
    //                     }

    //                 }else{
    //                     $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
    //                     $stock2 = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=215|desc=1|search=1',0,40,@total)");
    //                     $result2 = $stock2->fetchAll();
    //                     $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
    //                     $stock2->closeCursor();
    //                     $goodtung2 = 215; //05GB0004 Gift Box (Lee Min Ho)
    //                     $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
    //                     if ($stock_sun2) {

    //                             if ($stock_sun2 >= $total_produce2) {
    //                                 $data['good_id']          = '215';
    //                                 $data['good_color']       = '1';
    //                                 $data['price']            = '0';
    //                                 $data['total']            = '0';
    //                                 $data['cat_id']            = '12';
    //                                 $data['sale_off_percent'] = '100';
    //                                 $data['num']              = $total_produce2;
    //                                 if ($heckAccessories !='') {
    //                                     $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
    //                                     $id = $QMarket->update($data,$where);
    //                                 }else{

    //                                        $id = $QMarket->insert($data);
    //                                      }
    //                             }

    //                         } 
    //                 }
    //             }//kr      

    //         }
    //     }//if good

    //     }//Time

        if (in_array('210', $good_ids)) {//CPH1611 R9sPlus
            $findFoceSale = new Application_Model_Warehouse();
            if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
                if ($warehouse_id == 92 AND $rank == 13) {
                    if(!in_array($distributor_id,$kr)){
                        $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                        $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                        $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                        $result = $stock->fetchAll();
                        $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                        $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }else{
                        // $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                        // $stock2 = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=215|desc=1|search=1',0,40,@total)");
                        // $result2 = $stock2->fetchAll();
                        // $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
                        // $stock2->closeCursor();
                        // $goodtung2 = 215; //05GB0004 Gift Box (Lee Min Ho)
                        // $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
                        // if ($stock_sun2) {

                        //         if ($stock_sun2 >= $total_produce2) {
                        //             $data['good_id']          = '215';
                        //             $data['good_color']       = '1';
                        //             $data['price']            = '0';
                        //             $data['total']            = '0';
                        //             $data['cat_id']            = '12';
                        //             $data['sale_off_percent'] = '100';
                        //             $data['num']              = $total_produce2;
                        //             if ($heckAccessories !='') {
                        //                 $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                        //                 $id = $QMarket->update($data,$where);
                        //             }else{

                        //                    $id = $QMarket->insert($data);
                        //                  }
                        //         }

                        //     }
             }
                }//kr      

            }
        }

        
    }
        if (in_array('142', $good_ids)) {//F1s
            $findFoceSale = new Application_Model_Warehouse();
            if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
                if ($warehouse_id == 92 AND $rank == 13) {
                    if(!in_array($distributor_id,$kr)){
                        $product = array(142);
                        $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                        $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=216|desc=1|search=1',0,40,@total)");
                        $result = $stock->fetchAll();
                        $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                        $stock->closeCursor();
                $goodtung = 216; //05GB0005 Gift Box (Mai Davika)
                $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && false) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '216';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }else{
                $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock2 = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result2 = $stock2->fetchAll();
                $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
                $stock2->closeCursor();
                        $goodtung2 = 324; //06GB0003 2 in 1 Gift Box
                        $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
                        if ($stock_sun2) {

                            if ($stock_sun2 >= $total_produce2) {
                                $data['good_id']          = '324';
                                $data['good_color']       = '1';
                                $data['price']            = '0';
                                $data['total']            = '0';
                                $data['cat_id']            = '12';
                                $data['sale_off_percent'] = '100';
                                $data['num']              = $total_produce2;
                                if ($heckAccessories !='') {
                                    $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                    $id = $QMarket->update($data,$where);
                                }else{

                                 $id = $QMarket->insert($data);
                             }
                         }

                     }
                 }
               }//kr     

           }
       }


   }

    if (in_array('211', $good_ids)) {//CPH1701 A57
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
            if ($warehouse_id == 92 AND $rank == 13) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                    $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }else{
                        // $product = array(211,300);
                        // $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                        // $stock2 = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=215|desc=1|search=1',0,40,@total)");
                        // $result2 = $stock2->fetchAll();
                        // $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
                        // $stock2->closeCursor();
                        // $goodtung2 = 215; //Gift Box  (4 in 1) 30GF0006
                        // $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
                        // if ($stock_sun2) {

                        //         if ($stock_sun2 >= $total_produce2) {
                        //             $data['good_id']          = '215';
                        //             $data['good_color']       = '1';
                        //             $data['price']            = '0';
                        //             $data['total']            = '0';
                        //             $data['cat_id']            = '12';
                        //             $data['sale_off_percent'] = '100';
                        //             $data['num']              = $total_produce2;
                        //             if ($heckAccessories !='') {
                        //                 $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                        //                 $id = $QMarket->update($data,$where);
                        //             }else{

                        //                    $id = $QMarket->insert($data);
                        //                  }
                        //         }

                        //     }
             }
               }//kr     

           }
       }


   }

    // if (in_array('256', $good_ids)) {//A77
    //         $findFoceSale = new Application_Model_Warehouse();
    //         if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
    //         if ($warehouse_id == 92 AND $rank == 13) {
    //             if(!in_array($distributor_id,$kr)){
    //             $product = array(142,256,267,211);
    //             $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
    //             $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=215|desc=1|search=1',0,40,@total)");
    //             $result = $stock->fetchAll();
    //             $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
    //             $stock->closeCursor();
    //             $goodtung = 215; //05GB0004 Gift Box (Lee Min Ho)
    //             $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
    //             if ($stock_sun >= $total_produce && $total_produce > 0) {
    //                 if ($stock_sun >= $total_produce && $total_produce > 0) {
    //                     $data['good_id']          = '215';
    //                     $data['good_color']       = '1';
    //                     $data['price']            = '0';
    //                     $data['total']            = '0';
    //                     $data['cat_id']            = '12';
    //                     $data['sale_off_percent'] = '100';
    //                     $data['num']              = $total_produce;

    //                     if ($heckAccessories !='') {
    //                     $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
    //                     $id = $QMarket->update($data,$where);
    //                 }else{
    //                        $id = $QMarket->insert($data);
    //                      }
    //                 }

    //                 }else{

    //                 }
    //            }//kr     

    //         }
    //     }


    // }

    if (in_array('267', $good_ids)) {//CPH1613 (R9s Pro)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
            if ($warehouse_id == 92 AND $rank == 13) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                    $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }else{

             }
               }//kr     

           }
       }


   }


if (in_array('299', $good_ids)) {//CPH1723 (F5)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
        if ($warehouse_id == 92 AND $rank == 13) {
            if(!in_array($distributor_id,$kr)){
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }
            }//kr     

        }
    }

    
}


if (in_array('300', $good_ids)) {//CPH1725 (Phone F5 Youth)
    $findFoceSale = new Application_Model_Warehouse();
    if ($warehouse_id == 92 AND $rank == 13) {
        if(!in_array($distributor_id,$kr)){
            $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
            $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
            $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
            $result = $stock->fetchAll();
            $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
            $stock->closeCursor();
                    $goodtung = 324; //06GB0003 2 in 1 Gift Box
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '324';
                            $data['good_color']       = '1';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }else{
                    //     $product = array(300,301,299,313,210);
                    //     $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    //     $stock2 = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                    //     $result2 = $stock2->fetchAll();
                    //     $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
                    //     $stock2->closeCursor();
                    //     $goodtung2 = 324; //06GB0003 2 in 1 Gift Box
                    //     $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
                    //     if ($stock_sun2) {

                    //             if ($stock_sun2 >= $total_produce2) {
                    //                 $data['good_id']          = '324';
                    //                 $data['good_color']       = '1';
                    //                 $data['price']            = '0';
                    //                 $data['total']            = '0';
                    //                 $data['cat_id']            = '12';
                    //                 $data['sale_off_percent'] = '100';
                    //                 $data['num']              = $total_produce2;
                    //                 if ($heckAccessories !='') {
                    //                     $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                    //                     $id = $QMarket->update($data,$where);
                    //                 }else{

                    //                        $id = $QMarket->insert($data);
                    //                      }
                    //             }

                    //         }
                 }
                }//kr     

            }

        }


if (in_array('301', $good_ids)) {//CPH1727 (F5 6GB)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
        if ($warehouse_id == 92 AND $rank == 13) {
            if(!in_array($distributor_id,$kr)){
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }
            }//kr     

        }
    }

    
}

if (in_array('310', $good_ids)) {//CPH1819 (Phone F7)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-07-17 00:00:00'){
        if ($warehouse_id == 92 AND $rank == 13) {
            if(!in_array($distributor_id,$kr)){
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 324; //2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }
            }//kr     

        }
    }

    
}

if (in_array('311', $good_ids)) {//CPH1821 (Phone F7 128 GB)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-07-17 00:00:00'){
        if ($warehouse_id == 92 AND $rank == 13) {
            if(!in_array($distributor_id,$kr)){
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 324; //2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }
            }//kr     

        }
    }

    
}


if (in_array('313', $good_ids)) {//CPH1729(4G) (A83 4+64G)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-05-05 00:00:00'){
        if ($warehouse_id == 92 AND $rank == 13) {
            if(!in_array($distributor_id,$kr)){
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }
            }//kr     

        }
    }

    
}


if (in_array('371', $good_ids)) {//CPH1729(4G) (A83 4+64G)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2019-03-27 12:00:00'){
        if ($warehouse_id == 92 AND $rank == 13) {
            if(!in_array($distributor_id,$kr)){
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }
            }//kr     

        }
    }

    
}


if (in_array('321', $good_ids)) {//CPH1831 (Phone R15 PRO)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-05-31 00:00:00'){
        if ($warehouse_id == 92 AND $rank == 13) {
            if(!in_array($distributor_id,$kr)){
                $product = array(321);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=322|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 322; //06GB0002 R15 Pro Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '322';
                        $data['good_color']       = '7';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }
            }//kr     

        }
    }

    
}


if (in_array('340', $good_ids)) {//CPH1871(01MBOPPOCPH1871) (FIND X)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-08-14 00:00:00'){
        if ($warehouse_id == 92 AND $rank == 13) {
            if(!in_array($distributor_id,$kr)){
                $product = array(340);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=342|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 342; //06GB0004 FIND X Car Charger Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '342';
                        $data['good_color']       = '2';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }
            }//kr     

        }
    }

    
}


if (in_array('340', $good_ids)) {//CPH1871(01MBOPPOCPH1871) (FIND X)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-08-14 00:00:00'){
        if ($warehouse_id == 92 AND $rank == 13) {
            if(!in_array($distributor_id,$kr)){
                $product = array(340);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=343|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 343; //06GB0005 FIND X Tripod Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '343';
                        $data['good_color']       = '2';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }
            }//kr     

        }
    }

    
}

if (in_array('345', $good_ids)) {//CPH1823(01MBOPPOCPH1823) (Phone F9)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-08-30 00:00:00'){
        if ($warehouse_id == 92 AND $rank == 13) {
            if(!in_array($distributor_id,$kr)){
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                    $goodtung = 324; //2 in 1 Gift Box
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '324';
                            $data['good_color']       = '1';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }


    if (in_array('392', $good_ids)) {//CPH1969 6+128G (Phone F11 PRO)
        $findFoceSale = new Application_Model_Warehouse();
        if ($warehouse_id == 92 AND $rank == 13) {
            if(!in_array($distributor_id,$kr)){
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 324; //2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }
            }//kr     

        }
    }


    if (in_array('345', $good_ids)) {//CPH1823(01MBOPPOCPH1823) (Phone F9)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2018-12-20 00:00:00' && date('Y-m-d H:i:s') < '2018-12-25 00:00:00'){
            if ($warehouse_id == 92 AND $rank == 13) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(345);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product,17);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=368|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 368; //06MC0004 (F9 VIP Card - Green)
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '368';
                            $data['good_color']       = '17';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }


    if (in_array('353', $good_ids)) {//CPH1877(Phone R17 PRO)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2018-12-03 00:00:00'){
            if ($warehouse_id == 92 AND $rank == 13) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(353);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=362|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 362; //06GB0007 (R17 Pro Tripod Gift Box)
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '362';
                            $data['good_color']       = '9';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }


    if (in_array('392', $good_ids)) {//CPH1969 6+128G (Phone F11 PRO)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2019-05-20 00:00:00' && date('Y-m-d H:i:s') < '2019-05-21 00:00:00'){
            if ($warehouse_id == 92 AND $rank == 13) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(392);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product,5);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=397|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 397; //06MC0006 (F11 Pro VIP Card (Gray))
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '397';
                            $data['good_color']       = '5';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }


    if (in_array('403', $good_ids)) {//CPH1919 (Phone RENO 10X)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2019-06-19 00:00:00'){
            if ($warehouse_id == 92 AND $rank == 13) {
                if(!in_array($distributor_id,$kr)){
                    $product = array(403);
                    $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=92|cat_id=12|good_id=412|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                    $goodtung = 412; //06GB0009 (Reno Tripod Gift Box)
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '412';
                            $data['good_color']       = '2';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }
                }//kr     

            }
        }
    }


// END : Brand Shop By Dealer


// START : Brand Shop By OPPO

    if (in_array('210', $good_ids)) {//CPH1611 R9sPlus
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
            $distributor_type_group = '';
            if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
                $distributor_type_group = $distributor['group_type_id'];
            }
            if(isset($old_group_id) && $old_group_id){
                $distributor_type_group = $old_group_id;
            }
            //10=brandshop
            if ($distributor_type_group == '10' && $warehouse_id) {
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }else{

             }

         }
     }


 }
        if (in_array('142', $good_ids)) {//F1s
            $findFoceSale = new Application_Model_Warehouse();
            if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
                $distributor_type_group = '';
                if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
                    $distributor_type_group = $distributor['group_type_id'];
                }
                if(isset($old_group_id) && $old_group_id){
                    $distributor_type_group = $old_group_id;
                }
            //10=brandshop
                if ($distributor_type_group == '10' && $warehouse_id) {
                    $product = array(142);
                    $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                    $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=216|desc=1|search=1',0,40,@total)");
                    $result = $stock->fetchAll();
                    $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                    $stock->closeCursor();
                $goodtung = 216; //05GB0005 Gift Box (Mai Davika)
                $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && false) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '216';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }else{
                $total_produce2 =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock2 = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result2 = $stock2->fetchAll();
                $stock_sun2 = $result2[0]['product_count']-$result2[0]['current_order']-$result2[0]['current_change_order'];
                $stock2->closeCursor();
                        $goodtung2 = 324; //06GB0003 2 in 1 Gift Box
                        $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung2,$sn);
                        if ($stock_sun2) {

                            if ($stock_sun2 >= $total_produce2) {
                                $data['good_id']          = '324';
                                $data['good_color']       = '1';
                                $data['price']            = '0';
                                $data['total']            = '0';
                                $data['cat_id']            = '12';
                                $data['sale_off_percent'] = '100';
                                $data['num']              = $total_produce2;
                                if ($heckAccessories !='') {
                                    $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                    $id = $QMarket->update($data,$where);
                                }else{

                                 $id = $QMarket->insert($data);
                             }
                         }

                     }
                 }

             }
         }


     }

    if (in_array('211', $good_ids)) {//CPH1701 A57
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
            $distributor_type_group = '';
            if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
                $distributor_type_group = $distributor['group_type_id'];
            }
            if(isset($old_group_id) && $old_group_id){
                $distributor_type_group = $old_group_id;
            }
            //10=brandshop
            if ($distributor_type_group == '10' && $warehouse_id) {
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }else{

             }

         }
     }


 }


    if (in_array('267', $good_ids)) {//CPH1613 (R9s Pro)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
            $distributor_type_group = '';
            if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
                $distributor_type_group = $distributor['group_type_id'];
            }
            if(isset($old_group_id) && $old_group_id){
                $distributor_type_group = $old_group_id;
            }
            //10=brandshop
            if ($distributor_type_group == '10' && $warehouse_id) {
                $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
                $total_produce =  $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }else{

             }

         }
     }


 }


if (in_array('299', $good_ids)) {//CPH1723 (F5)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
        $distributor_type_group = '';
        if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
            $distributor_type_group = $distributor['group_type_id'];
        }
        if(isset($old_group_id) && $old_group_id){
            $distributor_type_group = $old_group_id;
        }
        //10=brandshop
        if ($distributor_type_group == '10' && $warehouse_id) {
            $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
            $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
            $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
            $result = $stock->fetchAll();
            $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
            $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }

         }
     }


 }


if (in_array('300', $good_ids)) {//CPH1725 (Phone F5 Youth)
    $findFoceSale = new Application_Model_Warehouse();
    $distributor_type_group = '';
    if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
        $distributor_type_group = $distributor['group_type_id'];
    }
    if(isset($old_group_id) && $old_group_id){
        $distributor_type_group = $old_group_id;
    }
            //10=brandshop
    if ($distributor_type_group == '10' && $warehouse_id) {
        $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
        $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
        $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
        $result = $stock->fetchAll();
        $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
        $stock->closeCursor();
                    $goodtung = 324; //06GB0003 2 in 1 Gift Box
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '324';
                            $data['good_color']       = '1';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }else{

                 }

             }

         }


if (in_array('301', $good_ids)) {//CPH1727 (F5 6GB)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2017-12-18 00:00:00'){
        $distributor_type_group = '';
        if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
            $distributor_type_group = $distributor['group_type_id'];
        }
        if(isset($old_group_id) && $old_group_id){
            $distributor_type_group = $old_group_id;
        }
        //10=brandshop
        if ($distributor_type_group == '10' && $warehouse_id) {
            $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
            $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
            $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
            $result = $stock->fetchAll();
            $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
            $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }

         }
     }


 }

if (in_array('310', $good_ids)) {//CPH1819 (Phone F7)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-07-17 00:00:00'){
        $distributor_type_group = '';
        if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
            $distributor_type_group = $distributor['group_type_id'];
        }
        if(isset($old_group_id) && $old_group_id){
            $distributor_type_group = $old_group_id;
        }
        //10=brandshop
        if ($distributor_type_group == '10' && $warehouse_id) {
            $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
            $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
            $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
            $result = $stock->fetchAll();
            $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
            $stock->closeCursor();
                $goodtung = 324; //2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }

         }
     }


 }

if (in_array('311', $good_ids)) {//CPH1821 (Phone F7 128 GB)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-07-17 00:00:00'){
        $distributor_type_group = '';
        if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
            $distributor_type_group = $distributor['group_type_id'];
        }
        if(isset($old_group_id) && $old_group_id){
            $distributor_type_group = $old_group_id;
        }
        //10=brandshop
        if ($distributor_type_group == '10' && $warehouse_id) {
            $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
            $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
            $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
            $result = $stock->fetchAll();
            $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
            $stock->closeCursor();
                $goodtung = 324; //2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }

         }
     }


 }


if (in_array('313', $good_ids)) {//CPH1729(4G) (A83 4+64G)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-05-05 00:00:00'){
        $distributor_type_group = '';
        if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
            $distributor_type_group = $distributor['group_type_id'];
        }
        if(isset($old_group_id) && $old_group_id){
            $distributor_type_group = $old_group_id;
        }
        //10=brandshop
        if ($distributor_type_group == '10' && $warehouse_id) {
            $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
            $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
            $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
            $result = $stock->fetchAll();
            $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
            $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }

         }
     }


 }


if (in_array('371', $good_ids)) {//CPH1729(4G) (A83 4+64G)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2019-03-27 12:00:00'){
        $distributor_type_group = '';
        if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
            $distributor_type_group = $distributor['group_type_id'];
        }
        if(isset($old_group_id) && $old_group_id){
            $distributor_type_group = $old_group_id;
        }
        //10=brandshop
        if ($distributor_type_group == '10' && $warehouse_id) {
            $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
            $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
            $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
            $result = $stock->fetchAll();
            $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
            $stock->closeCursor();
                $goodtung = 324; //06GB0003 2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }

         }
     }


 }


if (in_array('321', $good_ids)) {//CPH1831 (Phone R15 PRO)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-05-31 00:00:00'){
        $distributor_type_group = '';
        if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
            $distributor_type_group = $distributor['group_type_id'];
        }
        if(isset($old_group_id) && $old_group_id){
            $distributor_type_group = $old_group_id;
        }
        //10=brandshop
        if ($distributor_type_group == '10' && $warehouse_id) {
            $product = array(321);
            $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
            $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=322|desc=1|search=1',0,40,@total)");
            $result = $stock->fetchAll();
            $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
            $stock->closeCursor();
                $goodtung = 322; //06GB0002 R15 Pro Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '322';
                        $data['good_color']       = '7';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }

         }
     }


 }


if (in_array('340', $good_ids)) {//CPH1871(01MBOPPOCPH1871) (FIND X)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-08-14 00:00:00'){
        $distributor_type_group = '';
        if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
            $distributor_type_group = $distributor['group_type_id'];
        }
        if(isset($old_group_id) && $old_group_id){
            $distributor_type_group = $old_group_id;
        }
        //10=brandshop
        if ($distributor_type_group == '10' && $warehouse_id) {
            $product = array(340);
            $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
            $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=342|desc=1|search=1',0,40,@total)");
            $result = $stock->fetchAll();
            $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
            $stock->closeCursor();
                $goodtung = 342; //06GB0004 FIND X Car Charger Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '342';
                        $data['good_color']       = '2';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }

         }
     }


 }


if (in_array('340', $good_ids)) {//CPH1871(01MBOPPOCPH1871) (FIND X)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-08-14 00:00:00'){
        $distributor_type_group = '';
        if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
            $distributor_type_group = $distributor['group_type_id'];
        }
        if(isset($old_group_id) && $old_group_id){
            $distributor_type_group = $old_group_id;
        }
        //10=brandshop
        if ($distributor_type_group == '10' && $warehouse_id) {
            $product = array(340);
            $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
            $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=343|desc=1|search=1',0,40,@total)");
            $result = $stock->fetchAll();
            $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
            $stock->closeCursor();
                $goodtung = 343; //06GB0005 FIND X Tripod Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '343';
                        $data['good_color']       = '2';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }

         }
     }


 }

if (in_array('345', $good_ids)) {//CPH1823(01MBOPPOCPH1823) (Phone F9)
    $findFoceSale = new Application_Model_Warehouse();
    if (date('Y-m-d H:i:s') > '2018-08-30 00:00:00'){
        $distributor_type_group = '';
        if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
            $distributor_type_group = $distributor['group_type_id'];
        }
        if(isset($old_group_id) && $old_group_id){
            $distributor_type_group = $old_group_id;
        }
            //10=brandshop
        if ($distributor_type_group == '10' && $warehouse_id) {
            $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
            $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
            $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
            $result = $stock->fetchAll();
            $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
            $stock->closeCursor();
                    $goodtung = 324; //2 in 1 Gift Box
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '324';
                            $data['good_color']       = '1';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }

             }
         }
     }


    if (in_array('392', $good_ids)) {//CPH1969 6+128G (Phone F11 PRO)
        $findFoceSale = new Application_Model_Warehouse();
        $distributor_type_group = '';
        if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
            $distributor_type_group = $distributor['group_type_id'];
        }
        if(isset($old_group_id) && $old_group_id){
            $distributor_type_group = $old_group_id;
        }
        //10=brandshop
        if ($distributor_type_group == '10' && $warehouse_id) {
            $product = array(210,142,211,267,299,300,301,313,310,311,345,371,392);
            $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
            $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=324|desc=1|search=1',0,40,@total)");
            $result = $stock->fetchAll();
            $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
            $stock->closeCursor();
                $goodtung = 324; //2 in 1 Gift Box
                $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                if ($stock_sun >= $total_produce && $total_produce > 0) {
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        $data['good_id']          = '324';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;

                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                         $id = $QMarket->insert($data);
                     }
                 }

             }

         }
     }


    if (in_array('345', $good_ids)) {//CPH1823(01MBOPPOCPH1823) (Phone F9)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2018-12-20 00:00:00' && date('Y-m-d H:i:s') < '2018-12-25 00:00:00'){
            $distributor_type_group = '';
            if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
                $distributor_type_group = $distributor['group_type_id'];
            }
            if(isset($old_group_id) && $old_group_id){
                $distributor_type_group = $old_group_id;
            }
            //10=brandshop
            if ($distributor_type_group == '10' && $warehouse_id) {
                $product = array(345);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product,17);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=368|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                    $goodtung = 368; //06MC0004 (F9 VIP Card - Green)
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '368';
                            $data['good_color']       = '17';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }

             }
         }
     }


    if (in_array('353', $good_ids)) {//CPH1877(Phone R17 PRO)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2018-12-03 00:00:00'){
            $distributor_type_group = '';
            if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
                $distributor_type_group = $distributor['group_type_id'];
            }
            if(isset($old_group_id) && $old_group_id){
                $distributor_type_group = $old_group_id;
            }
            //10=brandshop
            if ($distributor_type_group == '10' && $warehouse_id) {
                $product = array(353);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=362|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                    $goodtung = 362; //06GB0007 (R17 Pro Tripod Gift Box)
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '362';
                            $data['good_color']       = '9';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }

             }
         }
     }


    if (in_array('392', $good_ids)) {//CPH1969 6+128G (Phone F11 PRO)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2019-05-20 00:00:00' && date('Y-m-d H:i:s') < '2019-05-21 00:00:00'){
            $distributor_type_group = '';
            if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
                $distributor_type_group = $distributor['group_type_id'];
            }
            if(isset($old_group_id) && $old_group_id){
                $distributor_type_group = $old_group_id;
            }
            //10=brandshop
            if ($distributor_type_group == '10' && $warehouse_id) {
                $product = array(392);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product,5);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=397|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                    $goodtung = 397; //06MC0006 (F11 Pro VIP Card (Gray))
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '397';
                            $data['good_color']       = '5';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }

             }
         }
     }


    if (in_array('403', $good_ids)) {//CPH1919 (Phone RENO 10X)
        $findFoceSale = new Application_Model_Warehouse();
        if (date('Y-m-d H:i:s') > '2019-06-19 00:00:00'){
            $distributor_type_group = '';
            if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
                $distributor_type_group = $distributor['group_type_id'];
            }
            if(isset($old_group_id) && $old_group_id){
                $distributor_type_group = $old_group_id;
            }
            //10=brandshop
            if ($distributor_type_group == '10' && $warehouse_id) {
                $product = array(403);
                $total_produce = $findFoceSale->checkPhoneInMarketForesale($sn,$product);
                $stock = $db->query("CALL proc_get_storage('warehouse_id=$warehouse_id|cat_id=12|good_id=412|desc=1|search=1',0,40,@total)");
                $result = $stock->fetchAll();
                $stock_sun = $result[0]['product_count']-$result[0]['current_order']-$result[0]['current_change_order'];
                $stock->closeCursor();
                    $goodtung = 412; //06GB0009 (Reno Tripod Gift Box)
                    $heckAccessories = $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);
                    if ($stock_sun >= $total_produce && $total_produce > 0) {
                        if ($stock_sun >= $total_produce && $total_produce > 0) {
                            $data['good_id']          = '412';
                            $data['good_color']       = '2';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;

                            if ($heckAccessories !='') {
                                $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                                $id = $QMarket->update($data,$where);
                            }else{
                             $id = $QMarket->insert($data);
                         }
                     }

                 }

             }
         }
     }

// END : Brand Shop By OPPO


}//good_ids
} 

}//switch_i
    if ($rank == 10) { //rank Brand Shop/Service(10)

        $distributor_type_group = '';
        if(isset($distributor['group_type_id']) && $distributor['group_type_id']){
            $distributor_type_group = $distributor['group_type_id'];
        }
        if(isset($old_group_id) && $old_group_id){
            $distributor_type_group = $old_group_id;
        }
        //10=brandshop
        if ($distributor_type_group == '10') {

            if ($edit=='') { 

                /*if (in_array('11', $cat_ids)) {
                   
                    $total_produce =  $findFoceSale->checkPhoneInMarket($sn);
                    $goodtung = 127;
                    $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);

                    
                        $data['good_id']          = '127';
                        $data['good_color']       = '1';
                        $data['price']            = '0';
                        $data['total']            = '0';
                        $data['cat_id']            = '12';
                        $data['sale_off_percent'] = '100';
                        $data['num']              = $total_produce;
                    //print_r($data);die;
                    if ($heckAccessories !='') {
                        $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                        $id = $QMarket->update($data,$where);
                    }else{
                           $id = $QMarket->insert($data);
                    }
                }*/

            } 

        }

    }else{ 
        if ($type == 2 || $type == 5) { // type Demo APK


        }else{
            if ($warehouse_id == 5 || $warehouse_id == 6 || $warehouse_id == 8 || $warehouse_id == 117 || $distributor_id == 3028 || $distributor_id == 44927) {

            }else{

               if ($edit=='') { 

                    /*if (in_array('11', $cat_ids)) {
                       
                        $total_produce =  $findFoceSale->checkPhoneInMarket($sn);
                        $goodtung = 127;
                        $heckAccessories =   $findFoceSale->checkAccessoriesInMarket($goodtung,$sn);

                        
                            $data['good_id']          = '127';
                            $data['good_color']       = '1';
                            $data['price']            = '0';
                            $data['total']            = '0';
                            $data['cat_id']            = '12';
                            $data['sale_off_percent'] = '100';
                            $data['num']              = $total_produce;
                        //print_r($data);die;
                        if ($heckAccessories !='') {
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $heckAccessories['id']);
                            $id = $QMarket->update($data,$where);
                        }else{
                               $id = $QMarket->insert($data);
                        }
                    }*/

                }    

        }//warehouse_id
        }//type
    }//rank   

   }//-----------------------
    //delete old sales
   if ($this->getRequest()->getParam('sn'))
   {
    if ($old_ids)
    {
        $removed_sales_ids = array_diff($old_ids, $ids);
        if ($removed_sales_ids)
        {   
                //pond
            $QQLogQuotaTranDistributor = new Application_Model_LogQuotaTranDistributor();
            $QQLogQuotaTran = new Application_Model_LogQuotaTran();

            $where = $QMarket->getAdapter()->quoteInto('id IN (?)', $removed_sales_ids);
            $where1 = $QQLogQuotaTranDistributor->getAdapter()->quoteInto('market_id IN (?)', $removed_sales_ids);
            $where2 = $QQLogQuotaTran->getAdapter()->quoteInto('market_id IN (?)', $removed_sales_ids);
            $QMarket->delete($where);
            $QQLogQuotaTranDistributor->delete($where1);
            $QQLogQuotaTran->delete($where2);
        }
    }
    $info = 'Batch EditSale order number: Sale order number: 1';
} else
{
    $info = 'Batch addSale order number: Sale order number: 2';
}

    // NHAN VIEN MUA MAY
if($type == FOR_STAFFS AND in_array($distributor_id, array(OPPO_STAFF,OPPO_INGAME))){
    $QStaffOrder = new Application_Model_StaffOrder();
    $resultStaffOrder = $QStaffOrder->save($params,$sn);
    if($resultStaffOrder['status'] < 0){
        return array(
            'code' => -5,
            'message' => $resultStaffOrder['message']
        );
    }
}

    // Neu co nhung don khuyen mai massupload thi khong can kiem tra stock

if($save_service !='service'){
    if(!$invoice_number_data)
    {
        if ($missing_stock){
                // throw new Exception(1);
        }
    }
}

    //$sales_order = $result['sn'];

if ($sn!='')
{

    $creditnote_data['sales_order']=$sn;
        //print_r($creditnote_data);die;
    if($creditnote_data !=null)
    {  
     $result_status = $this->saveAPICreditNoteAction($db,$distributor_id,$sn,$creditnote_data);
 }else{
     $result_status = $this->saveAPICreditNoteAction($db,$distributor_id,$sn,'no_discount');
 }
}

    //$sales_order = $result['sn'];
if ($sn!='')
{
    $deposit_data['sales_order']=$sn;
    if($deposit_data !=null)
    {  
     $result_status = $this->saveAPIDepositAction($db,$distributor_id,$sn,$deposit_data);
 }else{
     $result_status = $this->saveAPIDepositAction($db,$distributor_id,$sn,'no_discount');
 }
}

if ($presales_sn !='')
{
    $QPreSalesOrder = new Application_Model_PreSalesOrder();
    $array_data = array('sales_order_sn' => $sn,
        'status' => 2,
        'sales_order_date' => date('Y-m-d H:i:s'),
        'admin_id' => $userStorage->id,
        'admin_confirm_date' => date('Y-m-d H:i:s'));

    $where = $QPreSalesOrder->getAdapter()->quoteInto('presales_sn = ?', $presales_sn);
    $QPreSalesOrder->update($array_data, $where);
}


    //todo log
$ip = $this->getRequest()->getServer('REMOTE_ADDR');
$info .= $sn;

$QLog->insert(array(
    'info'       => $info,
    'user_id'    => $userStorage->id,
    'ip_address' => $ip,
    'time'       => $currentTime,
));

    //commit
$chk = $db->commit();

$result = array(
    'code' => 1,
    'message' => 'Done!',
    'sn' => $sn,
);

if (isset($id) && $id) $result['id'] = $id;

return $result;

}
catch (exception $e)
{
    $db->rollback();

    if ($e->getMessage() == 1)
        return array(
            'code' => -2,
            'message' => 'The stock available is not enough! >>'.$e,
        );
    else
        return array(
            'code' => -3,
            'message' => 'Cannot save, please try again! 4' . $e->getCode()."/".$e->getMessage(),
        );
}

} else
{
    $db->rollback();
    return array(
        'code' => -4,
        'message' => 'Cannot save, please try again! 5',
    );
}
}

private function _exportChangeList($data)
{

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', '200M');
    $filename = 'Charge_Distibutor_Imei '.date('d-m-Y H-i-s').'.csv';
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
    echo "\xEF\xBB\xBF"; 
    $heads = array(
        'No.',
        'INVOICE NUMBER',
        'DO NUMBER',
        'IMEI',
        'STORE TRANFER ( IN )',
        'STORE TRANFER ( OUT )',
        'DISTRIBUTOR / WAREHOUSE ( IN )',
        'DISTRIBUTOR / WAREHOUSE ( OUT )',
        'WAREHOUSE',
        'REMARK',
        'CREATED BY',
        'TIME',

    );
    fputcsv($output, $heads);

    $QDistributor = new Application_Model_Distributor();
    $QStaff = new Application_Model_Staff();
    $QWarehouse = new Application_Model_Warehouse();
    $QStore = new Application_Model_Store();


    $warehouse  = $QWarehouse->get_cache();
    $staff = $QStaff->get_cache();
    $distributor = $QDistributor->get_cache3();
    $store = $QStore->get_cache();

    $i = 1;

    foreach($data as $item)
    {

        $row = array();
        $row[] = $i;
        $row[] = $item['invoice_number'];
        $row[] = $item['do_number'];
        $row[] = $item['imei_sn'];

        $row[] = $store[$item['new_store']];
        $row[] = $store[$item['old_store']];

        if($item['new_distibutor'] ==''){
            $row[] = $warehouse[$item['change_warehouse']];
        }else{ 
            $row[] = $distributor[$item['new_distibutor']];
        };

        $row[] = $distributor[$item['old_distibutor']];
        $row[] = $warehouse[$item['warehouse_id']];
        $row[] = $item['remark'];
        $row[] = $staff[$item['change_by']];
        $row[] = $item['change_at'];


        fputcsv($output, $row);
        unset($item);
        unset($row);
        $i++;
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

/**
* T?o don hÃƒÂ ng t? Stock t?t
* @param array $params
* @return integer
*/


private function saveAPIStock($params = array())
{
    $ids              = isset($params['ids']) ? $params['ids'] : null;
    $cat_ids          = isset($params['cat_id']) ? $params['cat_id'] : null;
    $good_ids         = isset($params['good_id']) ? $params['good_id'] : null;
    $good_colors      = isset($params['good_color']) ? $params['good_color'] : null;
    $nums             = isset($params['num']) ? $params['num'] : null;
    $prices           = isset($params['price']) ? $params['price'] : null;
    $totals           = isset($params['total']) ? $params['total'] : null;
    $texts            = isset($params['text']) ? $params['text'] : null;
    $distributor_id   = isset($params['distributor_id']) ? $params['distributor_id'] : null;
    $warehouse_id     = isset($params['warehouse_id']) ? $params['warehouse_id'] : null;
    $salesman         = isset($params['salesman']) ? $params['salesman'] : null;
    $type             = isset($params['type']) ? $params['type'] : null;
    $sale_off_percent = isset($params['sale_off_percent']) ? $params['sale_off_percent'] : null;
    $sn               = isset($params['sn']) ? $params['sn'] : null;
    $isbatch          = isset($params['isbatch']) ? $params['isbatch'] : null;
    $life_time        = isset($params['life_time']) ? $params['life_time'] : null;
    $userStorage      = Zend_Auth::getInstance()->getStorage()->read();
    $gift_id          = isset($params['gift_id']) ? $params['gift_id'] : null;
    $campaign         = isset($params['campaign']) ? $params['campaign'] : null;
    $imei_list        = isset($params['imei_list']) ? $params['imei_list'] : null;
    $currentTime      = date('Y-m-d H:i:s');

//check can edit lifetime
    $QExceptionCase = new Application_Model_ExceptionCase();
    $where = $QExceptionCase->getAdapter()->quoteInto('name = ?',
        'LIFETIME_EXCEPTION');
    $lifetime_exception = $QExceptionCase->fetchRow($where);

    $exception_case = null;
    if (isset($lifetime_exception) and $lifetime_exception['value']) {
        eval(json_decode($lifetime_exception['value']));
        $exception_case = isset($data_exception) ? $data_exception : null;
    }

    if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, SUPER_SALES_ADMIN))
        or ($exception_case and in_array($userStorage->id, $exception_case)))
        $life_time_editable = 1;
    else
        $life_time_editable = 0;

    if ($life_time_editable and $life_time <= 0) {
        return array(
            'code' => -1,
            'message' => 'Invalid lifetime, please try again!',
        );
    }
//end of check can edit lifetime
//
    if ($type != 1)
        return array(
            'code' => -1,
            'message' => 'Invalid type, please try again!',
        );

    $QLog         = new Application_Model_Log();
    $QGood        = new Application_Model_Good();
    $QGoodColor   = new Application_Model_GoodColor();
    $QDistributor = new Application_Model_Distributor();
    $QMarket      = new Application_Model_MarketStock();

    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    if (!$sn)
        $sn = date('YmdHis') . substr(microtime(), 2, 4);

    $goods_cache = $QGood->get_cache();
    $good_colors_cache = $QGoodColor->get_cache();

    if (!is_array($ids)) {
        $db->rollback();
        return array(
            'code' => -4,
            'message' => 'Cannot save, please try again! 6',
        );
    }

    try {
        $where            = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
        $distributor      = $QDistributor->fetchRow($where);
        $rank             = $distributor['rank'];
        $array_good_color = array();

//get old ids
        $old_ids = $error_ids = null;
        if ($sn) {
            $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $old_sales = $QMarket->fetchAll($where);

            if ($old_sales) {
                foreach ($old_sales as $sale) {
                    $old_ids[] = $sale->id;

                    if ($sale['pay_time'] or $sale['shipping_yes_time'] or $sale['outmysql_time'])
                        $error_ids[] = $sale->id;
                }
            }
        }

        if ($error_ids) {
            return array(
                'code' => -5,
                'message' => 'This order was confirmed!',
                'sn' => $sn,
            );
        }

        $missing_stock = array();

        if (isset($ids) and $ids) {
            $resultSet = $QMarket->find($ids[0]);
            $market_current = $resultSet->current();

            if (isset($market_current) and $market_current) {
                $date_curent = $market_current['add_time'];
                $distributor_id_current = $market_current['d_id'];
                $current_time  = date('H:i:s');
        //ki?m tra khÃƒÂ´ng cho d?i d?i lÃƒÂ½
                if($distributor_id != $distributor_id_current && $current_time > TIME_LIMIT_ORDER)
                    throw new Exception("Sorry, you can't change distributor for this order.Please remove order and create again.");
            }
        }

// format imei list
        $imei_list = trim($imei_list);
        $imei_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $imei_list);
        $imei_list = explode("\n", $imei_list);
        $imei_list = array_filter($imei_list);

        $imei_list_text = implode(',', $imei_list);

        if (!count($imei_list))
            throw new Exception("IMEI List cannot blank");

        $total_quantity = 0;
        $total_value = 0;

        $QImeiStock = new Application_Model_ImeiStock();
        $where = $QImeiStock->getAdapter()->quoteInto('market_stock_sn = ?', $sn);
        $QImeiStock->delete($where);

        foreach ($ids as $k => $id) {
            if ( !(
                isset($cat_ids[$k]) and $cat_ids[$k] and isset($good_ids[$k]) and $good_ids[$k] and
                isset($good_colors[$k]) and $good_colors[$k] and isset($nums[$k]) and $nums[$k] and
                isset($prices[$k])
            ) ) continue;

                $total2 = 0;

                if ($cat_ids[$k] == PHONE_CAT_ID) {
                    if(in_array($good_ids[$k] . '_' . $good_colors[$k] , $array_good_color))
                        throw new Exception("Sorry, your input is duplicated, Model : " . $goods_cache[$good_ids[$k]] . " - " . $good_colors_cache[$good_colors[$k]]);

                    $array_good_color[] = $good_ids[$k] . '_' . $good_colors[$k];
                }

                $total_quantity += intval( $nums[$k] );
                $total_value += My_Number::floatval( $prices[$k] ) * intval( $nums[$k] );

    // check stock
                $db->query("SET @result := 0;");

                $db->query("CALL sp_check_stock_storage (?, ?, ?, ?, ?, @result);", array(
                    $nums[$k],
                    $distributor_id,
                    $good_ids[$k],
                    $good_colors[$k],
                    $imei_list_text,
                ));

                $stmt = $db->query("SELECT @result");
                $result = $stmt->fetchAll();

                if (!isset($result[0]['@result']) || $result[0]['@result'] != 1)
                    throw new Exception("T?n kho khÃƒÂ´ng d?, ho?c s? lu?ng IMEI khÃƒÂ¡c v?i s? trÃƒÂªn chi ti?t don hÃƒÂ ng.");

                $tem_total = (isset($totals[$k]) and $totals[$k]) ? $totals[$k] : 0;

                $data = array(
                    'market_general_id' => 0,
                    'cat_id'            => intval( $cat_ids[$k] ),
                    'good_id'           => intval( $good_ids[$k] ),
                    'good_color'        => intval( $good_colors[$k] ),
                    'num'               => intval( $nums[$k] ),
                    'price'             => My_Number::floatval( $prices[$k] ),
                    'total'             => My_Number::floatval( $tem_total ),
                    'text'              => (isset($texts[$k]) ? My_String::trim($texts[$k]) : null),
                    'price_clas'        => intval( $rank ),
                    'd_id'              => intval( $distributor_id ),
                    'warehouse_id'      => intval( $warehouse_id ),
                    'isbatch'           => intval( $isbatch ),
                    'salesman'          => intval( $salesman ),
                    'type'              => intval( $type ),
                    'sale_off_percent'  => intval( $sale_off_percent[$k] ),
                    'campaign'          => 16,
                    'last_updated_at'   => $currentTime,
                );

                if ($life_time_editable and $life_time) {
                    if ($life_time <= 0 || $life_time > 5 || !is_numeric($life_time)) $life_time = 2;
                    $data['life_time'] = $life_time * 24 * 60 * 60;
                }

    if ($id) { //update
        $where = $QMarket->getAdapter()->quoteInto('id = ?', $id);
        $QMarket->update($data, $where);

    } else { //insert

        if (isset($date_curent) and $date_curent)
            $date_time_add = $date_curent;
        else
            $date_time_add = $currentTime;

        $data['add_time'] = $date_time_add;
        $data['user_id']  = $userStorage->id;
        $data['sn']       = $sn;
        $data['print_no'] = ($QMarket->get_print_no_max($sn)) + 1;
        $id = $QMarket->insert($data);
    }
}

if ($total_quantity != count($imei_list))
    throw new Exception("S? lu?ng IMEI trong list khÃƒÂ¡c v?i t?ng s? lu?ng trong chi ti?t don hÃƒÂ ng.");

// if ($total_value >= 20*1000*1000)
    // throw new Exception("ÃƒÂon hÃƒÂ ng ph?i du?i 20 tri?u");

//delete old sales
if ($this->getRequest()->getParam('sn')) {
    if ($old_ids) {
        $removed_sales_ids = array_diff($old_ids, $ids);
        if ($removed_sales_ids) {
            $where = $QMarket->getAdapter()->quoteInto('id IN (?)', $removed_sales_ids);
            $QMarket->delete($where);
        }
    }
    $info = 'Edit Stock Order SN number: ';
} else {
    $info = 'Create Stock Order SN number: ';
}

// insert imei
foreach ($imei_list as $key => $value) {
    $imei_data = array(
        'imei_sn'         => $value,
        'distributor_id'  => intval($distributor_id),
        'out_date'        => date('Y-m-d H:i:s'),
        'market_stock_sn' => $sn,
    );

    $QImeiStock->insert($imei_data);
}

$sql = "update imei_stock, imei
SET imei_stock.good_id=imei.good_id
, imei_stock.good_color=imei.good_color
WHERE imei_stock.imei_sn=imei.imei_sn
AND imei_stock.imei_sn IN (".implode(',', $imei_list).");";
$db->query($sql);

$sql = "update imei_stock, market_stock
SET imei_stock.market_stock_id=market_stock.id
WHERE imei_stock.market_stock_sn=market_stock.sn
AND imei_stock.good_id=market_stock.good_id
AND imei_stock.good_color=market_stock.good_color;
AND imei_stock.imei_sn IN (".implode(',', $imei_list).");";
$db->query($sql);

//todo log
$ip = $this->getRequest()->getServer('REMOTE_ADDR');
$info .= $sn;

$QLog->insert(array(
    'info'       => $info,
    'user_id'    => $userStorage->id,
    'ip_address' => $ip,
    'time'       => $currentTime,
));

//commit
$db->commit();

$result = array(
    'code' => 1,
    'message' => 'Done!',
    'sn' => $sn,
);

if (isset($id) && $id) $result['id'] = $id;

return $result;
}
catch (exception $e)
{
    $db->rollback();

    if ($e->getMessage() == 1) {
        return array(
            'code' => -2,
            'message' => 'The stock available is not enough!',
        );
    } else {
        return array(
            'code' => -3,
            'message' => 'Cannot save, please try again! 7' . $e->getCode()."/".$e->getMessage(),
        );
    }
}
}


private function _exportCancelIMEI($can_sn)
{

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
//ini_set('memory_limit', -1);
    $filename = 'Cancel List - IMEI'. ' - '.date('d-m-Y H-i-s');
// output headers so that the file is downloaded rather than displayed
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename.'.csv');
// echo "\xEF\xBB\xBF"; // UTF-8 BOM
echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
$output = fopen('php://output', 'w');

$headss = array(
    'NO',
    'IMEI',
    'SN',
    'CANCELED DATE',
    'CANCELED ID',
    'CANCELED BY',

);

fputcsv($output, $headss);
$QStaff         = new Application_Model_Staff();
$staff          = $QStaff->get_cache();

$sql_imei = "select * from imei_cancel_log m 
where m.sales_sn = ".$can_sn;
$data = $db->fetchAll($sql_imei);

if (!$data){return;}
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$i =1;
foreach($data as $item){
    $row = array();
    $row[] = $i++;
    $row[] = '="'.$item['imei_sn'].'"';
    $row[] = '="'.$item['sales_sn'].'"';
    $row[] = $item['date_canceled'];
    $row[] = $item['canceled_by'];
    $row[] = $staff[$item['canceled_by']];


    fputcsv($output, $row);
    unset($row);
    unset($item);

}

unset($data);
// die;
}

// NewNEWNEW
// private function _exportReturnSaleExcel($data)
//    {
//       $this->_helper->layout->disableLayout();
//        $this->_helper->viewRenderer->setNoRender();
//        set_time_limit(0);
//        error_reporting(~E_ALL);
//        ini_set('display_error', 0);
//        ini_set('memory_limit', -1);
//        $filename = 'Return List - '.date('d-m-Y H-i-s').'.csv';

//        while (@ob_end_clean());
//        ob_start();
//        header('Content-Encoding: UTF-8');
//        header('Content-Type: text/csv; charset=utf-8');

//        header('Content-Disposition: attachment; filename='.$filename);

//        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
//        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
//        if (!file_exists($file_path))
//            mkdir($file_path, 0777, true);


//        $path = $file_path.'/'.$filename;
//        $output = fopen($path, 'w+');
//        echo "\xEF\xBB\xBF"; // UTF-8 BOM

//        $heads = array(
//            'No',
//            'SALE ORDER NUMBER',
//            'INVOICE NUMBER',
//            'DISTRIBUTOR ID',
//            'DISTRIBUTOR NAME',
//            'ORDER TYPE',
//            'PRODUCT NAME',
//            'PRODUCT COLOR',
//            'QUANTITY',
//            'SALES PRICE',
//            'TOTAL',
//            'CREATE ORDERTIME',
//            'CREATE BY',
//            'WAREHOUSE',
//            'CONFIRM RETURNTIME',
//            'CONFIRM BY',
//            'STATUS'
//        );
//            fputcsv($output, $heads);

//            $QGood = new Application_Model_Good();
//            $QGoodColor = new Application_Model_GoodColor();
//            $QMarket = new Application_Model_Market();
//            $QDistributor = new Application_Model_Distributor();
//            $QGoodCategory = new Application_Model_GoodCategory();
//            $QWarehouse = new Application_Model_Warehouse();
//            $Qstaff = new Application_Model_Staff();

//            $staff = $Qstaff->get_cache();
//            $goods = $QGood->get_cache();
//            $goodColors = $QGoodColor->get_cache();
//            $distributors = $QDistributor->get_cache();
//            $good_categories = $QGoodCategory->get_cache();
//            $warehouses_cached = $QWarehouse->get_cache();

//            $markets = array();
//            foreach ($data as $key => $m)
//            {
//                $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
//                $markets[$m['sn']] = $QMarket->fetchAll($where);
//            }

//            $i = 1;
//            $old_sn = '';
//            foreach($data as $item) {
//                if($item['sn'] == $old_sn){
//                    $count_row++;
//                }else{
//                    $count_row = 0;
//                }

//                $old_sn = $item['sn'];
//                $d_id = $item['d_id'];

//                if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; }
//                else { $temp_sn = $item['sn_ref']; }

//                if (is_null($item['invoice_number']) || $item['invoice_number'] == ''){
//                    $temp_invoice = $item['invoice_number'];}
//                    else{
//                        $temp_invoice = $item['invoice_number'];
//                    }

//                    if (is_null($item['d_id']) || $item['d_id'] == ''){
//                        $temp_did = $item['d_id'];}
//                        else{
//                            $temp_did = $item['d_id'];
//                        }

//                        if (is_null($item['title']) || $item['title'] == ''){
//                            $temp_title = $item['title'];}
//                            else{
//                                $temp_title = $item['title'];
//                            }

//                            if (is_null($good_categories[$item['cat_id']]) || $good_categories[$item['cat_id']] == ''){
//                                $temp_cg = $good_categories[$item['cat_id']];}
//                                else{
//                                    $temp_cg = $good_categories[$item['cat_id']];
//                                }

//                                if (is_null($staff[$item['pay_user']]) || $staff[$item['pay_user']] == '') {
//                                    $temp_user = $staff[$item['pay_user']];
//                                }else{
//                                    $temp_user = $staff[$item['pay_user']];
//                                }

//                                if(is_null($staff[$item['outmysql_user']]) || $staff[$item['outmysql_user']] == ''){
//                                    $confirm_user = $staff[$item['outmysql_user']];}
//                                    else{
//                                        $confirm_user = $staff[$item['outmysql_user']];
//                                    }


//                                if (is_null($item['outmysql_time']) || $item['outmysql_time'] == ''){
//                                    $temp_pay = $item['outmysql_time'];}
//                                    else{
//                                        $temp_pay = $item['outmysql_time'];
//                                    }

//                                    if (is_null($item['add_time']) || $item['add_time'] == ''){
//                                        $temp_addt = $item['add_time'];}
//                                        else{
//                                            $temp_addt = $item['pay_time'];
//                                        }

//                                           if (is_null($warehouses_cached[$item['warehouse_id']]) || $warehouses_cached[$item['warehouse_id']] == ''){
//                                            $temp_house = $warehouses_cached[$item['warehouse_id']];}
//                                            else{
//                                                $temp_house = $warehouses_cached[$item['warehouse_id']];
//                                            }


//                if (isset($distributors) && isset($distributors[$item['d_id']]))
//                $distributor = $distributors[$item['d_id']];
//            else
//                $distributor = '';

//            if ($item['return_type']==1){
//                $order_type="Defective";
//            }else if ($item['return_type']==2){
//                $order_type="Adjustment";
//            }else if ($item['return_type']==3){
//                $order_type="Demo";
//            }else{
//                $order_type="-";
//            }

//            //check payment
//            isset($item['pay_time']) ? $pay = 'v' : $pay = 'X';

//            //check shipping
//            if ($item['shipping_yes_time'])
//                $shipping = 'v';
//            else
//                $shipping = 'X';

//            //check out_warehouse
//            isset($item['outmysql_time']) ? $out = 'v' : $out = 'X';

//            if ($item['status'] == 1)
//                $status = 'v';
//            else
//                $status = 'X';

//            $row = array();

//            $confirmstatus = $item['outmysql_user'];
//            if($confirmstatus == NULL){
//                $confirmstatus = 'NO';
//            }else{
//                $confirmstatus ='YES';
//            }

//            // $row[] = $house ;

//            foreach ($markets[$item['sn']] as $key => $value)
//            {
//                if (isset($goods) && isset($goods[$value['good_id']]))
//                    $good_name = $goods[$value['good_id']];
//                if (isset($goodColors) && isset($goodColors[$value['good_color']]))
//                    $good_color = $goodColors[$value['good_color']];

//                $row[] = $i;
//                $row[] = '="'.$temp_sn.'"';
//                $row[] = $temp_invoice;
//                $row[] = $temp_did;
//                $row[] = $temp_title;
//                $row[] = $temp_cg;
//                $row[] = $good_name;
//                $row[] = $good_color;
//                $row[] = $value['num'];
//                $row[] = My_Number::f(@$value['price'], 0, ',','.');
//                $row[] = My_Number::f($value['total'], 0, ',','.');
//                $row[] = $temp_addt;
//                $row[] = $temp_user;
//                $row[] = $temp_house ;
//                $row[] = $temp_pay;
//                $row[] = $confirm_user;
//                $row[] = $confirmstatus;

//                 fputcsv($output,$row);
//        unset($item);
//        unset($value);
//        unset($row);
//                $i++;
//            }
//        }
//        fclose($output);

//        ob_flush();
//        ob_start();
//        while (@ob_end_flush());

//        header('Expires: 0');
//        header('Cache-Control: must-revalidate');
//        header('Pragma: public');
//        header('Content-Length: ' . filesize($path));
//        readfile($path);
//        exit;

//        $file = fopen($path, 'r');
//        $content = fread($file, filesize($path));
//        var_dump(filesize($path));
//        var_dump($content);

//        exit;
//    }

private function _exportReturnSaleExcelCN($data)
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Return List IMEI -  '.date('d-m-Y H-i-s').'.csv';

    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');

    header('Content-Disposition: attachment; filename='.$filename);

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);


// $path = $file_path.'/'.$filename;
// $output = fopen($path, 'w+');
// echo "\xEF\xBB\xBF"; // UTF-8 BOM

echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
$output = fopen('php://output', 'w');

$heads = array(
    'No',
    'SALE ORDER NUMBER',
    'INVOICE NUMBER',
    'DISTRIBUTOR CODE',
    'DISTRIBUTOR NAME',
    'AREA',
    'ORDER TYPE',
    'PRODUCT NAME',
    'PRODUCT COLOR',
    'QUANTITY',
    'SALES PRICE',
// 'TOTAL',
    'CREATE ORDERTIME',
    'CREATE BY',
    'WAREHOUSE',
    'IMEI',
    'CONFIRM RETURNTIME',
    'CONFIRM BY',
    'STATUS'
);
fputcsv($output, $heads);

$QGood = new Application_Model_Good();
$QGoodColor = new Application_Model_GoodColor();
$QMarket = new Application_Model_Market();
$QDistributor = new Application_Model_Distributor();
$QGoodCategory = new Application_Model_GoodCategory();
$QWarehouse = new Application_Model_Warehouse();
$Qstaff = new Application_Model_Staff();

$staff = $Qstaff->get_cache();
$goods = $QGood->get_cache();
$goodColors = $QGoodColor->get_cache();
$distributors = $QDistributor->get_cache();
$good_categories = $QGoodCategory->get_cache();
$warehouses_cached = $QWarehouse->get_cache();

$markets = array();
foreach ($data as $key => $m)
{
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
    $markets[$m['sn']] = $QMarket->fetchAll($where);
}

$i = 1;
$old_sn = '';
foreach($data as $item) {
    if (isset($distributors) && isset($distributors[$item['d_id']]))
        $distributor = $distributors[$item['d_id']];
    else
        $distributor = '';

    if ($item['return_type']==1){
        $order_type="Defective";
    }else if ($item['return_type']==2){
        $order_type="Adjustment";
    }else if ($item['return_type']==3){
        $order_type="Demo";
    }else{
        $order_type="-";
    }

    $row = array();

    $confirmstatus = $item['outmysql_user'];
    if($confirmstatus == NULL){
        $confirmstatus = 'NO';
    }else{
        $confirmstatus ='YES';
    }
    if (isset($goods) && isset($goods[$item['good_id']]))
        $good_name = $goods[$item['good_id']];
    if (isset($goodColors) && isset($goodColors[$item['good_color']]))
        $good_color = $goodColors[$item['good_color']];

    if (isset($good_categories) && $good_categories[$item['cat_id']])
        $temp_cg = $good_categories[$item['cat_id']];

    if (isset($staff) && $staff[$item['pay_user']])
        $temp_user = $staff[$item['pay_user']];

    if(isset($staff) && $staff[$item['outmysql_user']])
        $confirm_user = $staff[$item['outmysql_user']];


    if (isset($warehouses_cached) && $warehouses_cached[$item['warehouse_id']])
        $temp_house = $warehouses_cached[$item['warehouse_id']];


    $row[] = $i;
    $row[] = $item['creditnote_sn'];
    $row[] = $item['m2_invoice_number'];
    $row[] = $item['distributor_code'];
    $row[] = $item['title'];
    $row[] = $item['areaname'];
    $row[] = $temp_cg;
    $row[] = $good_name;
    $row[] = $good_color;
    $row[] = '1';
    $row[] = My_Number::f(@$item['price'], 0, ',','.');
    // $row[] = My_Number::f($item['total'], 0, ',','.');
    $row[] = $item['add_time'];
    $row[] = $temp_user;
    $row[] = $temp_house;
    $row[] = $item['imei_sn'];
    $row[] = $item['outmysql_time'];
    $row[] = $confirm_user;
    $row[] = $confirmstatus;

    fputcsv($output,$row);
    unset($item);
    unset($value);
    unset($row);
    $i++;
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

private function _exportReturnSaleExcelNoCN($data)
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Return List No IMEI- '.date('d-m-Y H-i-s').'.csv';

    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');

    header('Content-Disposition: attachment; filename='.$filename);

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);


// $path = $file_path.'/'.$filename;
// $output = fopen($path, 'w+');
// echo "\xEF\xBB\xBF"; // UTF-8 BOM

echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
$output = fopen('php://output', 'w');

$heads = array(
    'No',
    'SALE ORDER NUMBER',
    'INVOICE NUMBER',
    'DISTRIBUTOR CODE',
    // 'FINANCE CODE',
    'DISTRIBUTOR CODE AND NAME',
    'AREA',
    'ORDER TYPE',
    'PRODUCT NAME',
    'PRODUCT COLOR',
    'QUANTITY',
    'SALES PRICE',
    'TOTAL',
    'RETURN TO DISTRIBUTOR CODE',
    'RETURN TO DISTRIBUTOR NAME',
    'CREATE ORDERTIME',
    'CREATE BY',
    'WAREHOUSE',
    'CONFIRM RETURNTIME',
    'CONFIRM BY',
    'STATUS'

);
fputcsv($output, $heads);

$QGood = new Application_Model_Good();
$QGoodColor = new Application_Model_GoodColor();
$QMarket = new Application_Model_Market();
$QDistributor = new Application_Model_Distributor();
$QGoodCategory = new Application_Model_GoodCategory();
$QWarehouse = new Application_Model_Warehouse();
$Qstaff = new Application_Model_Staff();
$QBrand = new Application_Model_Brand();

$staff = $Qstaff->get_cache();
$goods = $QGood->get_cache();
$goodColors = $QGoodColor->get_cache();
$distributors = $QDistributor->get_cache();
$good_categories = $QGoodCategory->get_cache();
$warehouses_cached = $QWarehouse->get_cache();

$markets = array();
foreach ($data as $key => $m)
{
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
    $markets[$m['sn']] = $QMarket->fetchAll($where);
}

$i = 1;
$old_sn = '';
foreach($data as $item) {
    if($item['sn'] == $old_sn){
        $count_row++;
    }else{
        $count_row = 0;
    }

    $old_sn = $item['sn'];
    $d_id = $item['d_id'];

    if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; }
    else { $temp_sn = $item['sn_ref']; }

    if (is_null($item['invoice_number']) || $item['invoice_number'] == ''){
        $temp_invoice = $item['invoice_number'];}
        else{
            $temp_invoice = $item['invoice_number'];
        }
        if (is_null($item['areaname']) || $item['areaname'] == '') {
            $temp_area = $item['areaname'];}
            else{
                $temp_area = $item['areaname']; 
            }

            if (is_null($item['d_id']) || $item['d_id'] == ''){
                $temp_did = $item['d_id'];}
                else{
                    $temp_did = $item['d_id'];
                }

                if(is_null($item['distributor_code']) || $item['distributor_code'] == ''){
                    $temp_distributor_code = $item['distributor_code'];
                }else{
                    $temp_distributor_code = $item['distributor_code'];
                }

                if (is_null($item['title']) || $item['title'] == ''){
                    $temp_title = $item['distributor_code'].' - '.$item['title'];}
                    else{
                        $temp_title = $item['distributor_code'].' - '.$item['title'];
                    }

                    if (is_null($good_categories[$item['cat_id']]) || $good_categories[$item['cat_id']] == ''){
                        $temp_cg = $good_categories[$item['cat_id']];}
                        else{
                            $temp_cg = $good_categories[$item['cat_id']];
                        }

                        if (is_null($staff[$item['pay_user']]) || $staff[$item['pay_user']] == '') {
                            $temp_user = $staff[$item['pay_user']];
                        }else{
                            $temp_user = $staff[$item['pay_user']];
                        }

                        if(is_null($staff[$item['outmysql_user']]) || $staff[$item['outmysql_user']] == ''){
                            $confirm_user = $staff[$item['outmysql_user']];}
                            else{
                                $confirm_user = $staff[$item['outmysql_user']];
                            }


                            if (is_null($item['outmysql_time']) || $item['outmysql_time'] == ''){
                                $temp_pay = date('Y-m-d', strtotime($item['outmysql_time']));}
                                else{
                                    $temp_pay = date('Y-m-d', strtotime($item['outmysql_time']));
                                }

                                if (is_null($item['add_time']) || $item['add_time'] == ''){
                                    $temp_addt = date('Y-m-d', strtotime($item['add_time']));}
                                    else{
                                        $temp_addt = date('Y-m-d', strtotime($item['pay_time']));
                                    }

                                    if (is_null($warehouses_cached[$item['warehouse_id']]) || $warehouses_cached[$item['warehouse_id']] == ''){
                                        $temp_house = $warehouses_cached[$item['warehouse_id']];}
                                        else{
                                            $temp_house = $warehouses_cached[$item['warehouse_id']];
                                        }
                                        if(is_null($item['return_shop'] || $item['return_shop']) == ''){
                                            $temp_name = $item['return_shop'];}
                                            else{
                                                $temp_name = $item['return_shop'];

                                            }


                                            if(is_null($distributors[$item['return_shop']]) || $distributors[$item['return_shop']] == ''){
                                                $name_shop = $distributors[$item['return_shop']];}
                                                else{
                                                    $name_shop = $distributors[$item['return_shop']];
                                                }


                                                if (isset($distributors) && isset($distributors[$item['d_id']]))
                                                    $distributor = $distributors[$item['d_id']];
                                                else
                                                    $distributor = '';

                                                if ($item['return_type']==1){
                                                    $order_type="Defective";
                                                }else if ($item['return_type']==2){
                                                    $order_type="Adjustment";
                                                }else if ($item['return_type']==3){
                                                    $order_type="Demo";
                                                }else{
                                                    $order_type="-";
                                                }

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

                                                $row = array();

                                                $confirmstatus = $item['outmysql_user'];
                                                if($confirmstatus == NULL){
                                                    $confirmstatus = 'NO';
                                                }else{
                                                    $confirmstatus ='YES';
                                                }

// $row[] = $house ;

                                                foreach ($markets[$item['sn']] as $key => $value)
                                                {
                                                    if (isset($goods) && isset($goods[$value['good_id']]))
                                                        $good_name = $goods[$value['good_id']];
                                                    $brands = $QBrand->getBrand($value['good_id']);
                                                    $brand_name = $brands[0]['brand_name'];

                                                    if (isset($goodColors) && isset($goodColors[$value['good_color']]))
                                                        $good_color = $goodColors[$value['good_color']];

                                                    $row[] = $i;
                                                    $row[] = '="'.$temp_sn.'"';
                                                    $row[] = $temp_invoice;
                                                    $row[] = $temp_distributor_code;
                                                    // $row[] = $temp_finance_code;
                                                    $row[] = $temp_title;
                                                    $row[] = $temp_area;
                                                    $row[] = $temp_cg;
                                                    $row[] = $brand_name.' '.$good_name;
                                                    $row[] = $good_color;
                                                    $row[] = $value['num'];
                                                    $row[] = My_Number::f(@$value['price'], 0, ',','.');
                                                    $row[] = My_Number::f($value['total'], 0, ',','.');
                                                    $row[] = $temp_name;
                                                    $row[] = $name_shop;
                                                    $row[] = $temp_addt;
                                                    $row[] = $temp_user;
                                                    $row[] = $temp_house ;
                                                    $row[] = $temp_pay;
                                                    $row[] = $confirm_user;
                                                    $row[] = $confirmstatus;

                                                    fputcsv($output,$row);
                                                    unset($item);
                                                    unset($value);
                                                    unset($row);
                                                    $i++;
                                                }
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

                                        private function _exportCampaignExcel($sql)
                                        {

                                            require_once 'ExcelWriterXML.php';

                                            set_time_limit(0);
                                            ini_set('memory_limit', '-1');
                                            error_reporting(E_ALL);
                                            ini_set('display_error', 1);

                                            $filename = 'List_Campaign_Sales_' . date('YmdHis') . '.xml';
                                            $xml = new ExcelWriterXML($filename);
                                            $xml->docAuthor('OPPO Vietnam');

                                            $xml->sendHeaders();
                                            $xml->stdOutStart();

                                            $sheet = $xml->addSheet('Sales');

                                            $heads = array(
                                                'SALE ORDER NUMBER',
                                                'RETAILER ID',
                                                'RETAILER NAME',
                                                'AREA',
                                                'PROVINCE',
                                                'DISTRICT',
                                                'PRODUCT NAME',
                                                'PRODUCT COLOR',
                                                'SALES QUANTITY',
                                                'SALES PRICE',
                                                'TOTAL',
                                                'INVOICE NUMBER',
                                                'INVOICE PREFIX',
                                                'WAREHOUSE',
                                                'OUT OF WAREHOUSE',
                                                'STATUS',
                                                'ORDER TIME',
                                                'ORDER TYPE',
                                                'ORDER DESCRIPTION',
                                                'CAMPAIGN NAME',
                                                'CAMPAIGN_ID'
                                            );

                                            $sheet->stdOutSheetStart();


                                            $sheet->stdOutSheetRowStart(1);
                                            foreach ($heads as $k => $item)
                                            {
                                                $sheet->stdOutSheetColumn('String', 1, $k + 1, $item);
                                            }
                                            $sheet->stdOutSheetRowEnd();

                                            $QGood = new Application_Model_Good();
                                            $QGoodColor = new Application_Model_GoodColor();
                                            $QDistributor = new Application_Model_Distributor();
                                            $QWarehouse = new Application_Model_Warehouse();
                                            $QInvoicePrefix = new Application_Model_InvoicePrefix();
                                            $QCampaign = new Application_Model_Campaign();

                                            $goods          = $QGood->get_cache();
                                            $goodColors     = $QGoodColor->get_cache();
                                            $distributors   = $QDistributor->get_all_cache();
                                            $warehouses     = $QWarehouse->get_cache();
                                            $invoice_prefixs= $QInvoicePrefix->get_cache();
                                            $campaign       = $QCampaign->get_cache();

//load config
                                            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
                                            $config = $config->toArray();
                                            $con = mysqli_connect($config['resources']['db']['params']['host'], $config['resources']['db']['params']['username'],
                                                $config['resources']['db']['params']['password'], $config['resources']['db']['params']['dbname']);
                                            mysqli_set_charset($con, "utf8");

                                            $result = mysqli_query($con, $sql);

                                            $i = 2;

                                            while ($item = mysqli_fetch_assoc($result)) {
                                                $sheet->stdOutSheetRowStart($i);

                                                $j = 1;

                                                $sheet->stdOutSheetColumn('String', $i, $j++, $item['sn']);

                                                if (isset($distributors) && isset($distributors[$item['d_id']]))
                                                    $distributor = $distributors[$item['d_id']]['title'];
                                                else
                                                    $distributor = '';

//check payment
                                                isset($item['pay_time']) ? $pay = 'v' : $pay = 'X';

//check invoice number
                                                $invoice_number = isset($item['invoice_number']) ? $item['invoice_number'] : 'x';
                                                $invoice_prefix = isset($invoice_prefixs[$item['invoice_sign']]) ? $invoice_prefixs[$item['invoice_sign']] : 'x';
//check out_warehouse
                                                isset($item['outmysql_time']) ? $out = 'v' : $out = 'X';
                                                $campaign_name = isset($campaign[$item['campaign']]) ? $campaign[$item['campaign']] : 'x';

                                                if ($item['status'] == 1)
                                                    $status = 'v';
                                                else
                                                    $status = 'X';

                                                $good_name = $good_color = '';
                                                if (isset($goods) && isset($goods[$item['good_id']]))
                                                    $good_name = $goods[$item['good_id']];
                                                if (isset($goodColors) && isset($goodColors[$item['good_color']]))
                                                    $good_color = $goodColors[$item['good_color']];

                                                $warehouse = isset($warehouses[$item['warehouse_id']]) ? $warehouses[$item['warehouse_id']] : '';
                                                $sheet->stdOutSheetColumn('String', $i, $j++, $item['d_id']);
                                                $sheet->stdOutSheetColumn('String', $i, $j++, $distributor);
                                                $sheet->stdOutSheetColumn('String', $i, $j++, isset($item['district']) ?
                                                    My_Region::getValue($item['district'], My_Region::Area) : '');
                                                $sheet->stdOutSheetColumn('String', $i, $j++, isset($item['district']) ?
                                                    My_Region::getValue($item['district'], My_Region::Province) : '');
                                                $sheet->stdOutSheetColumn('String', $i, $j++, isset($item['district']) ?
                                                    My_Region::getValue($item['district'], My_Region::District) : '');
                                                $sheet->stdOutSheetColumn('String', $i, $j++, $good_name);
                                                $sheet->stdOutSheetColumn('String', $i, $j++, $good_color);
                                                $sheet->stdOutSheetColumn('Number', $i, $j++, $item['num']);
                                                $sheet->stdOutSheetColumn('Number', $i, $j++, $item['price']);
                                                $sheet->stdOutSheetColumn('Number', $i, $j++, $item['total']);
                                                $sheet->stdOutSheetColumn('String', $i, $j++, $invoice_number);
                                                $sheet->stdOutSheetColumn('String', $i, $j++, $invoice_prefix);
                                                $sheet->stdOutSheetColumn('String', $i, $j++, $warehouse);
                                                $sheet->stdOutSheetColumn('String', $i, $j++, $out);
                                                $sheet->stdOutSheetColumn('String', $i, $j++, $status);
                                                $sheet->stdOutSheetColumn('String', $i, $j++, $item['add_time']);

if ($item['type'] == 1) //for retailer
$type = 'For Retailer';
elseif ($item['type'] == 2) //for APK
$type = 'For APK';
elseif ($item['type'] == 3) //for staffs
$type = 'For Staffs';
elseif ($item['type'] == 4) //for lending
$type = 'For Lending';
elseif ($item['type'] == 5) //for DEMO
$type = 'For DEMO';

$sheet->stdOutSheetColumn('String', $i, $j++, $type);
$sheet->stdOutSheetColumn('String', $i, $j++, $item['text'] ? $item['text'] : '');
$sheet->stdOutSheetColumn('String', $i, $j++, $campaign_name ? $campaign_name : '');
$sheet->stdOutSheetRowEnd();

$i++;

}

$sheet->stdOutSheetEnd();

$xml->stdOutEnd();

exit;
}


//Export
private function _exportExcel($sql) {
//echo $sql;die;
// this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Export_Sale_Volume_'.date('d-m-Y H-i-s').'.csv';
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

echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
$output = fopen('php://output', 'w');

//$path = $file_path.'/'.$filename;
//$output = fopen($path, 'w+');
//echo "\xEF\xBB\xBF"; // UTF-8 BOM


$heads = array(
    'SALE ORDER NUMBER',
    'INVOICE NUMBER',
    // 'DELIVERY TRACKING NO',

    'STORE CODE / WAREHOUSE CODE',
    'STORE NAME / WAREHOUSE NAME',

    // 'DISTRIBUTOR ID',

    'DISTRIBUTOR CODE',
    'DISTRIBUTOR NAME',
    'DISTRIBUTOR STATUS',

    // 'BRANCH OF NUMBER',
    // 'DISTRIBUTOR PHONE',
    // 'COMPANY NAME',
    // 'CONTRACT NAME',
    // 'ADDRESS',
    // 'PHONE',
    // 'MST NAME',
    // 'BRANCH TYPE',
    // 'BRANCH NUMBER',
    // 'DISTRIBUTOR TYPE GROUP',
    // 'GRAND AREA',

    'RGM',
    'RM',
    'Sale',

    // 'DISTRICT',

    'PRODUCT TYPE',
    'PRODUCT NAME',
    'PRODUCT COLOR',
    'SALES QUANTITY',
    'SET OFF (%)',
    'NORMAL UNIT PRICE EX',
    'NORMAL TOTAL PRICE EX',

    // 'SALE OFF UNIT PRICE EX ????????',

    'SALE OFF TOTAL PRICE EX',
    'ORDER DESCRIPTION',
    'TAX PO (STAFF CODE)',

    // 'DELIVERY FEE',
    // 'UNIT PRICE',
    // 'TOTAL',
    // 'DISCOUNT EX',
    // 'DISCOUNT',
    // 'GRAND TOTAL EX',
    // 'GRAND TOTAL',
    // 'SUMMARY TOTAL EX',
    // 'SUMMARY TOTAL VAT',
    // 'SUMMARY TOTAL',

    'SALES CONFIRM DATETIME',
    'PAID DATETIME',
    'SHIPPING DATETIME',
    'WAREHOUSE',
    'STOCKOUT DATETIME',
    'STATUS',
    'SALE ORDER TIME',
    'CUSTOMER ORDER TYPE',
    'Sale Name',

    // 'SALES CATTY',
    // 'SALES AREA',
    //   'SALES ADMIN ID',
    // 'SALES ADMIN',

    'PAYMENT TYPE',
    'CONFIRM CASH DATETIME',
    'CONFIRM CASH BY'

    // 'Finance Group',
    // 'PAY TEXT',
    // 'OPERATION CAMPAIGN',
    // 'PHONE NUMBER'
);

fputcsv($output, $heads);

$QGood          = new Application_Model_Good();
$QGoodColor     = new Application_Model_GoodColor();
$QGoodCategory  = new Application_Model_GoodCategory();
$QDistributor   = new Application_Model_Distributor();
$QWarehouse     = new Application_Model_Warehouse();
$QStaff         = new Application_Model_Staff();
$QBrand         = new Application_Model_Brand();
$QSubArea       = new Application_Model_SubArea();
$QStore         = new Application_Model_Store();

$goods             = $QGood->get_cache();
$goodColors        = $QGoodColor->get_cache();
$good_categories   = $QGoodCategory->get_cache();
$distributor_cache = $QDistributor->get_cache2();
$warehouses        = $QWarehouse->get_cache();
$staff             = $QStaff->get_cache();
$subarea           = $QSubArea->get_cache();

$result = $db->query($sql);
//print_r($result);die;

$i = 2;
$next_sn='';$delivery_fee_count =0;

$grand_e1 = array(81,82,83,110,111,112);
$grand_e2 = array(85,86,87,115,88,89,116,117);
$grand_e3 = array(90,91,92,93,113);
$grand_e4 = array(94,95,96);
$grand_e5 = array(97,109);
$grand_w1 = array(98,99,100,101,102,114);
$grand_w2 = array(103,104,105);
$grand_w3 = array(106,107,108);

$old_sn = '';
$count_row = 0;

foreach($result as $item) {

    if($item['sn'] == $old_sn){
        $count_row++;
    }else{
        $count_row = 0;
    }

    $old_sn = $item['sn'];

    $d_id = $item['d_id'];
    if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
    else { $temp_sn = $item['sn_ref']; }

    /*--Show delivery_fee 1 row --*/   
    if($next_sn==$item['sn']){
        $delivery_fee_count+=1;
    }else{
        $next_sn=$item['sn'];
    }

    if($delivery_fee_count ==1){
        $delivery_fee = !is_null($item['delivery_fee']) ? $item['delivery_fee'] : 0;
        $delivery_fee_count=0;
    }else{
        $delivery_fee =0;
    }

    if ($item['status'] == 1) { $temp_status = 'Actived'; }
    else if ($item['status'] == 2) { $temp_status = 'Expired'; }
    else { $temp_status = 'Expired'; }

if ($item['type'] == 1) //for retailer
$type = 'For Retailer';
// elseif ($item['type'] == 2) //for demo
// $type = 'For Demo';
elseif ($item['type'] == 3) //for staffs
$type = 'For Staffs';
elseif ($item['type'] == 4) //for lending
$type = 'For Lending';
elseif ($item['type'] == 5) //for APK
$type = 'For DEMO';    

if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = '????????????'; }
else { $branch_type = '????'; }
$num = $item['num'];

$where_payment = $QDistributor->getAdapter()->quoteInto('id = ?', $item['d_id']);
$distributors_payment = $QDistributor->fetchRow($where_payment);
$rank = $distributors_payment->rank;

$total_spc_discount = $item['total_spc_discount'];

if($rank=='9'){
    $price = $this->format_number_2($item['price']);
    $total_amount_ex_vat = $this->format_number_2($price)* $num;

    //Sale Off Price ???????????
    $saleoffprice = $this->format_number_2($item['sale_off_price']);
    $saleofftotal_amount_ex_vat = $this->format_number_2($item['total']);
}else{
    $price_ext = $this->ext_vat($item['price']);
    $price = $this->cal_sale_off_percent($item['sale_off_percent'],$price_ext,$num,$item['total']);

    //Sale Off Price ???????????
    $saleoffprice_ext = $this->ext_vat($item['sale_off_price']);
    $saleoffprice = $this->cal_sale_off_percent($item['sale_off_percent'],$saleoffprice_ext,$num,$item['total']);
    // $price_in = $price *1;
    //$total_amount_ex_vat = ($this->format_number_2($price)* $num)-$total_spc_discount;
    // $total_amount_ex_vat = ($this->format_number_2($this->ext_vat($item['total'])));
    // $total_amount_in_vat = ($total_amount_ex_vat*1);

    $price_in = $item['price'];
    $total_amount_ex_vat = $this->format_number_2($this->format_number_2($this->ext_vat($item['price']))*$item['num']);
    $total_amount_in_vat = $this->format_number_2($item['total']);

    //Sale Off Price ???????????
    $saleoffprice_in = $item['sale_off_price'];
    $saleofftotal_amount_ex_vat = $this->format_number_2($this->format_number_2($this->ext_vat($item['sale_off_price'])));
    $saleofftotal_amount_in_vat = $this->format_number_2($item['total']);
}

$excel_area_name = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area);

$excel_area_id = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area, My_Region::ID);

if ( in_array($excel_area_id, $grand_e1) ) { $grand_area = 'BKK East-1'; } 
else if ( in_array($excel_area_id, $grand_e2) ) { $grand_area = 'BKK East-2'; }
else if ( in_array($excel_area_id, $grand_e3) ) { $grand_area = 'BKK East-3'; }
else if ( in_array($excel_area_id, $grand_e4) ) { $grand_area = 'BKK East-4'; }
else if ( in_array($excel_area_id, $grand_e5) ) { $grand_area = 'BKK East-5'; }
else if ( in_array($excel_area_id, $grand_w1) ) { $grand_area = 'BKK West-1'; }
else if ( in_array($excel_area_id, $grand_w2) ) { $grand_area = 'BKK West-2'; }
else if ( in_array($excel_area_id, $grand_w3) ) { $grand_area = 'BKK West-3'; }
else { $grand_area = $excel_area_name; }

$where = $QDistributor->getAdapter()->quoteInto('id =?',$item['d_id']);
$distributor_arr = $QDistributor->fetchRow($where);


$status_type = '';
if($item['payment_type'] == 'CR'){
    $status_type = 'Cash';
}else if($item['payment_type'] == 'CA'){
    $status_type = 'Credit';
}else{
    $status_type = 'Not Payment';
}

$row = array();
$row[] = '="'.$temp_sn.'"';
$row[] = $item['invoice_number'];

// $row[] = $item['tracking_no'];


if($distributor_arr['agent_status'] == 1){

$where = $QWarehouse->getAdapter()->quoteInto('id =?',$distributor_arr['agent_warehouse_id']);
$warehouse_arr = $QWarehouse->fetchRow($where);

$row[] = $warehouse_arr['code'];
$row[] = $warehouse_arr['name'];

}else{

$row[] = $item['store_code'];
$row[] = $item['store_name'];

}

if($item['status'] == 1) {
    $distributor_status = 'In Cooperation';
}elseif($item['status'] == 2){
    $distributor_status = 'Suspend Cooperation';
}else{
    $distributor_status = 'Close';
}

// $row[] = $distributor_cache[$item['d_id']]['d_id'];

$row[] = $item['distributor_code'];
$row[] = $distributor_cache[$item['d_id']]['title'];
$row[] = $distributor_status;

// $row[] = $distributor_cache[$item['d_id']]['branchoffnumber'];
// $row[] = '="'.$distributor_cache[$item['d_id']]['tel'].'"';
// $row[] = $distributor_cache[$item['d_id']]['unames'];
// $row[] = $item['contact_name'];
// $row[] = $item['address'];
// $row[] = $item['phone'];
// $row[] = '="'.$distributor_cache[$item['d_id']]['mst_sn'].'"';
// $row[] = $branch_type;
// $row[] = '="'.$distributor_cache[$item['d_id']]['branch_no'].'"';

// $DTG = '';
switch ($item['group_type_id']) {
    case '10':
    $DTG = 'Brand Shop';
    break;
    case '11':
    $DTG = 'Brand Shop By Dealer';
    break;
    case '12':
    $DTG = 'Brand Shop-ORG';
    break;
    case '13':
    $DTG = 'Brand Shop by KR Dealer';
    break;
    case '1':
    $DTG = 'Dealer and Hub';
    break;
    case '8':
    $DTG = 'Digital';
    break;
    case '7':
    $DTG = 'Export';
    break;
    case '3':
    $DTG = 'KA(ORG)';
    break;
    case '2':
    $DTG = 'KR-Dealer';
    break;
    case '5':
    $DTG = 'Online';
    break;
    case '4':
    $DTG = 'Operator';
    break;
    case '9':
    $DTG = 'Service Shop';
    break;
    case '6':
    $DTG = 'Staff';
    break;
}

// $row[] = $DTG;

// $row[] = $grand_area;
$row[] = $excel_area_name;
$row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);

if($distributor_arr['agent_status'] == 1){

$row[] = '-';

}else{

$where = $QStore->getAdapter()->quoteInto('id =?',$item['store_id']);
$store_arr = $QStore->fetchRow($where);

$row[] = $subarea[$store_arr['agency']];

}


// $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::District);

$row[] = $good_categories[$item['cat_id']];

$brand_name = $QBrand->getBrand($item['good_id']);

$row[] = $brand_name[0]['brand_name'].' '.$goods[$item['good_id']];
$row[] = $goodColors[$item['good_color']];
$row[] = $item['num'];

$row[] = $item['sale_off_percent'];
$row[] = My_Number::f($price, 0, ',', '.');
if($total_amount_ex_vat <=0){
    $row[] = 0; 
}else{
    $row[] = My_Number::f($total_amount_ex_vat, 0, ',', '.');
}

// $row[] = $delivery_fee;
// $row[] = number_format($total_spc_discount, 2);
if($rank=='9'){
    //$row[] = number_format($price_ext, 2);
    if($total_amount_ex_vat <=0){
       //$row[] = 0; 
    }else{
       //$row[] = number_format($total_amount_ex_vat, 2);  
    }
}else{

    //$row[] = number_format($price_in, 2);
    if($total_amount_ex_vat<=0){
     $row[] = 0;
 }else{
     $row[] = number_format($total_amount_in_vat, 2);
 }
}

//Sale Off Price ???????????
// $row[] = My_Number::f($saleoffprice, 0, ',', '.');
if($saleofftotal_amount_ex_vat <=0){
    $row[] = 0; 
}else{
    $row[] = My_Number::f($saleofftotal_amount_ex_vat, 0, ',', '.');
}

if($rank=='9'){
    if($saleofftotal_amount_ex_vat <=0){
    }else{ 
    }
}else{
    if($saleofftotal_amount_ex_vat<=0){
     $row[] = 0;
 }else{
     $row[] = number_format($saleofftotal_amount_in_vat, 2);
 }
}


// $grand_total_amount_in_vat += $total_amount_in_vat;

if($count_row == 0){
    //$row[] = number_format($item['total_spc_discount'], 2);
    //$row[] = number_format($item['total_spc_discount']*1, 2);

    //$row[] = number_format($item['grand_total']/1, 2);
    //$row[] = number_format($item['grand_total'], 2);

    //$row[] = number_format($item['grand_total']/1 - $item['total_spc_discount'], 2);

    //$row[] = number_format(($item['grand_total'] - ($item['total_spc_discount']*1)) - ($item['grand_total']/1 - $item['total_spc_discount']), 2);

    //$row[] = number_format(($this->format_number_2($item['grand_total']/1) - ($this->format_number_2($item['total_spc_discount'])))*1, 2);
}else{
    // $row[] = '0';
    // $row[] = '0';
    // $row[] = '0';
    // $row[] = '0';
    // $row[] = '0';
    // $row[] = '0';
    // $row[] = '0';
}

$row[] = $item['text'];
$row[] = $item['tax_po'];
$row[] = $item['sales_confirm_date'];
$row[] = $item['pay_time'];
$row[] = $item['shipping_yes_time'];

$row[] = $warehouses[$item['warehouse_id']];
$row[] = $item['outmysql_time'];
$row[] = $temp_status;
$row[] = $item['add_time'];
$row[] = $type;
$row[] = $staff[$item['user_id']];
// $row[] = $item['sales_catty_name'];
// $row[] = $item['sales_area'];
// $row[] = $item['sales_admin_id'];
// $row[] = $item['sales_admin'];
$row[] = $status_type;
$row[] = $item['confirm_cash'];
$row[] = $staff[$item['confirm_cash_by']];
// $row[] = $item['finance_group'];
// $row[] = $item['pay_text'];
// $row[] = $item['bs_campaign'];
// $row[] = $item['phone_number_sn'];

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

private function _exportExcelNew($sql) {

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Export_By_Model_ '.date('d-m-Y H-i-s').'.csv';

    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);

echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
$output = fopen('php://output', 'w');

$heads = array(
    'SALE ORDER NUMBER',
    'INVOICE NUMBER',
    'PRODUCT NAME',
    'PRODUCT COLOR',
    'SALES QUANTITY',
    'UNIT PRICE EX',
    'TOTAL EX',
    'SALES CONFIRM DATETIME',
    'PAID DATETIME',
    'SHIPPING DATETIME',
    'WAREHOUSE SHIPPING',
    'Store OR Warehouse Code',
    'Store OR Warehouse Name',
    'DISTRIBUTOR CODE',
    'DISTRIBUTOR NAME',
    'AREA',
    'PROVINCE',
    'STOCKOUT DATETIME',
    'STATUS',
    'SALE ORDER TIME',
    'CUSTOMER ORDER TYPE',
    'TAX PO',

);

fputcsv($output, $heads);

$QGood          = new Application_Model_Good();
$QGoodColor     = new Application_Model_GoodColor();
$QGoodCategory  = new Application_Model_GoodCategory();
$QDistributor   = new Application_Model_Distributor();
$QWarehouse     = new Application_Model_Warehouse();
$QImei          = new Application_Model_Imei();
$QBrand         = new Application_Model_Brand();

$goods             = $QGood->get_cache();
$goodColors        = $QGoodColor->get_cache();
$good_categories   = $QGoodCategory->get_cache();
$distributor_cache = $QDistributor->get_cache2();
$warehouses        = $QWarehouse->get_cache();

$result = $db->query($sql);
//print_r($result);die;

$i = 2;
$next_sn='';$delivery_fee_count =0;

$grand_e1 = array(81,82,83,110,111,112);
$grand_e2 = array(85,86,87,115,88,89,116,117);
$grand_e3 = array(90,91,92,93,113);
$grand_e4 = array(94,95,96);
$grand_e5 = array(97,109);
$grand_w1 = array(98,99,100,101,102,114);
$grand_w2 = array(103,104,105);
$grand_w3 = array(106,107,108);

$count = 0;
$old_sn = '';

foreach($result as $item) {

    $d_id = $item['d_id'];
    if ($item['canceled']== 1){$cancel = 'Canceled';}else{$cancel = 'Not Cancel';}

    if($old_sn != $item['sn']){
        $old_sn = $item['sn'];
        $count = 0;
    }else{
        $count++;
    }

    if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
    else { $temp_sn = $item['sn_ref']; }

    /*--Show delivery_fee 1 row --*/   
    if($next_sn==$item['sn']){
        $delivery_fee_count+=1;
    }else{
        $next_sn=$item['sn'];
    }

    if($delivery_fee_count ==1){
        $delivery_fee = !is_null($item['delivery_fee']) ? $item['delivery_fee'] : 0;
        $delivery_fee_count=0;
    }else{
        $delivery_fee =0;
    }

    if ($item['status'] == 1) { $temp_status = 'Actived'; }
    else if ($item['status'] == 2) { $temp_status = 'Expired'; }
    else { $temp_status = 'Expired'; }

if ($item['type'] == 1) //for retailer
$type = 'For Retailer';
elseif ($item['type'] == 2) //for APK
$type = 'For APK';
elseif ($item['type'] == 3) //for staffs
$type = 'For Staffs';
elseif ($item['type'] == 4) //for lending
$type = 'For Lending';
elseif ($item['type'] == 5) //for DEMO
$type = 'For DEMO';    

if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = '????????????'; }
else { $branch_type = '????'; }
$num = $item['num'];

$where_payment = $QDistributor->getAdapter()->quoteInto('id = ?', $item['d_id']);
$distributors_payment = $QDistributor->fetchRow($where_payment);
$rank = $distributors_payment->rank;

$total_spc_discount = $item['total_spc_discount'];

if($rank=='9'){
    $price = $this->format_number_2($item['price']);
    $total_amount_ex_vat = $this->format_number_2($price)* $num;
}else{
    $price_ext = $this->ext_vat($item['price']); 
    $price = $this->cal_sale_off_percent($item['sale_off_percent'],$price_ext,$num,$item['total']);
    $price_in = $price *1;
    $total_amount_ex_vat = ($this->format_number_2($this->ext_vat($item['total'])));
    $total_amount_in_vat = ($total_amount_ex_vat*1);
}

$excel_area_name = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area);

$excel_area_id = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area, My_Region::ID);

if ( in_array($excel_area_id, $grand_e1) ) { $grand_area = 'BKK East-1'; } 
else if ( in_array($excel_area_id, $grand_e2) ) { $grand_area = 'BKK East-2'; }
else if ( in_array($excel_area_id, $grand_e3) ) { $grand_area = 'BKK East-3'; }
else if ( in_array($excel_area_id, $grand_e4) ) { $grand_area = 'BKK East-4'; }
else if ( in_array($excel_area_id, $grand_e5) ) { $grand_area = 'BKK East-5'; }
else if ( in_array($excel_area_id, $grand_w1) ) { $grand_area = 'BKK West-1'; }
else if ( in_array($excel_area_id, $grand_w2) ) { $grand_area = 'BKK West-2'; }
else if ( in_array($excel_area_id, $grand_w3) ) { $grand_area = 'BKK West-3'; }
else { $grand_area = $excel_area_name; }


$row = array();
$row[] = '="'.$temp_sn.'"';
$row[] = $item['invoice_number'];

$brands = $QBrand->getBrand($item['good_id']);
$row[] = $brands[0]['brand_name'].' '.$goods[$item['good_id']];
$row[] = $goodColors[$item['good_color']];

$product_total_price = $this->product_price($item['total']);

if($rank == '9'){
    $product_total_price = $this->product_price($item['total']);
}

$product_unit_price = $this->product_price($this->decimal_remove_comma($product_total_price) / $item['num']);
$row[] = $item['num'];
if($item['price'] > 0){
    $row[] = $product_unit_price;
    $row[] = $product_total_price;
}else{
    $row[] = 0;
    $row[] = 0;
}

$row[] = $item['sales_confirm_date'];
$row[] = $item['pay_time'];
$row[] = $item['shipping_yes_time'];
$row[] = $warehouses[$item['warehouse_id']];

    // Start 
    $where = $QDistributor->getAdapter()->quoteInto('id =?',$item['d_id']);
    $distributor_arr = $QDistributor->fetchRow($where);
    
    if($distributor_arr['agent_status'] == 1){

        $where = $QWarehouse->getAdapter()->quoteInto('id =?',$distributor_arr['agent_warehouse_id']);
        $warehouse_arr = $QWarehouse->fetchRow($where);
        
        $row[] = $warehouse_arr['code'];
        $row[] = $warehouse_arr['name'];
        
        }else{
        
        $row[] = $item['store_code'];
        $row[] = $item['store_name'];
        
        }
        // End

        
// $row[] = $distributor_cache[$item['d_id']]['d_id'];
// $row[] = $distributor_cache[$item['d_id']]['title'];
$row[] = $distributor_cache[$item['d_id']]['d_code'];
$row[] = $distributor_cache[$item['d_id']]['title'];

$row[] = $excel_area_name;
$row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);

$row[] = $item['outmysql_time'];
$row[] = $temp_status;
$row[] = $item['add_time'];
$row[] = $type;
$row[] = $item['sales_area'];
$row[] = $item['tax_po'];


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

private function _exportExcelResidue($data) {

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    require_once 'PHPExcel.php';

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'SALE ORDER NUMBER')
    ->setCellValue('B1', 'RETAILER NAME')
    ->setCellValue('C1', 'SALES QUANTITY')
    ->setCellValue('D1', 'TOTAL (EXCLUDE VAT)')
    ->setCellValue('E1', 'SPECIAL DISCOUNT (EXCLUDE VAT)')
    ->setCellValue('F1', 'TOTAL AMOUNT (INCLUDE VAT)')
    ->setCellValue('G1', 'DISCOUNT (INCLUDE VAT)')
    ->setCellValue('H1', 'DEPOSIT')
    ->setCellValue('I1', 'NAT PAID (INCLUDE VAT)')
    ->setCellValue('J1', 'PAYMENT OR NOT')
    ->setCellValue('K1', 'SHIPPING')
    ->setCellValue('L1', 'OUT OF WAREHOUSE')
    ->setCellValue('M1', 'WAREHOUSE')
    ->setCellValue('N1', 'STATUS')
    ->setCellValue('O1', 'ORDER TIME')
    ->setCellValue('P1', 'FINANCE CONFIRM')
    ->setCellValue('Q1', 'AREA')
    ->setCellValue('R1', 'SALE ADMIN')
    ->setCellValue('S1', 'SALE')
    ->setCellValue('T1', 'FINANCE GROUP');

    $array_summary_area = array();

    $col1 = 1;
    $col2 = 1;

    foreach ($data as $key => $value) {

        $col1++;

        $product_unit_price = $value['price'];
        $product_qty        = $value['total_qty'];
        $price_total        = $value['total_price_amount'];
        $price_total_invat  = $value['total_price_amount_invat'];
        $sale_off_percent   = isset($value['sale_off_percent'])?$value['sale_off_percent']:'0';
        $product_unit_price = $this->cal_sale_off_percent($sale_off_percent,$product_unit_price,$product_qty,$price_total);
        $total_price = $this->ext_vat($product_unit_price);
        $product_unit_price_4 = $this->format_number_4($total_price);
        $product_amount_4 =$this->format_number_2($product_unit_price_4) * $product_qty;
        $total_amount = $product_amount_4*1;

        $total_amount = ($value['total_price_amount']);
        $delivery_fee = $value['delivery_fee']/1;

        if($value['rank'] == 9){
          $total_amount = $price_total_invat;
      }

      $special_discount = 0;
      date_default_timezone_set('Asia/Bangkok');
      $date = new DateTime('2017-01-04 00:00:00');
      $date_start= date_format($date,"Y-m-d H:i:s");
      $date_order = $value['add_time'];

      if($date_order < $date_start){
          if ($value['d_id'] == 3025 || $value['d_id'] == 3691) {

              $special_discount = $this->format_number_2(($total_amount*1/100));
          }

      }else{
          $special_discount = ($value['total_spc_discount']);
      }

      $d_id = $value['d_id'];
      if ($d_id=='25760' || $d_id=='21088' || $d_id=='25550'){
          $total_amount_in_vat = (($total_amount+$delivery_fee-$special_discount));
      }else{
          $total_amount_in_vat = (($total_amount+$delivery_fee-$special_discount)*1);
      }

      $gran_total  = ($total_amount_in_vat - $value['total_discount'] - $value['total_deposit']);

      if ($gran_total <= 0) {
          $gran_total = 0;
      }

      $gran_total = $this->format_number_2($gran_total);

      if($value['delivery_fee']>0){
        $delivery_fee ="Delivery fee = ".$value['delivery_fee'];
    }else{
        $delivery_fee ="";
    }

    $paymeny_status = 'No';
    $shipping_status = 'No';
    $out_status = 'No';

    $order_status = '';
    if ($value['status']==1){
        $order_status = 'Active';
    }else{
        $order_status = 'Expired';
    }

    if ($value['pay_time']){
        $paymeny_status = 'Yes';
    }

    if ($value['shipping_yes_time']){
        $shipping_status = 'Yes';
    }

    if ($value['outmysql_time']){
        $out_status = 'Yes';
    }

    if(isset($array_summary_area[$value['area_id']])){

        $array_summary_area[$value['area_id']]['count'] = $array_summary_area[$value['area_id']]['count']+1;
        $array_summary_area[$value['area_id']]['total_amount'] = $array_summary_area[$value['area_id']]['total_amount']+$gran_total;
    }else{
        $data = array(
            'area_id' => $value['area_id'],
            'area_name' => $value['area_name'],
            'count' => 1,
            'total_amount' => $gran_total
        );
        $array_summary_area[$value['area_id']] = $data;
    }

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A'.$col1, $value['sn_ref'])
    ->setCellValue('B'.$col1, '['.$value['store_code'].'] '.$value['title'])
    ->setCellValue('C'.$col1, $value['total_qty'])
    ->setCellValue('D'.$col1, $total_amount)
    ->setCellValue('E'.$col1, $special_discount)
    ->setCellValue('F'.$col1, $total_amount_in_vat)
    ->setCellValue('G'.$col1, $value['total_discount'])
    ->setCellValue('H'.$col1, $value['total_deposit'])
    ->setCellValue('I'.$col1, $gran_total)
    ->setCellValue('J'.$col1, $paymeny_status)
    ->setCellValue('K'.$col1, $shipping_status)
    ->setCellValue('L'.$col1, $out_status)
    ->setCellValue('M'.$col1, $value['warehouse_name'])
    ->setCellValue('N'.$col1, $order_status)
    ->setCellValue('O'.$col1, $value['add_time'])
    ->setCellValue('P'.$col1, $value['pay_time'])
    ->setCellValue('Q'.$col1, $value['area_name'])
    ->setCellValue('R'.$col1, $value['sales_admin'])
    ->setCellValue('S'.$col1, $value['sales_catty_name'])
    ->setCellValue('t'.$col1, $value['finance_group']);
}

$objPHPExcel->getActiveSheet(0)->setTitle('Details');

$objWorkSheet = $objPHPExcel->createSheet(1);

$objPHPExcel->setActiveSheetIndex(1)
->setCellValue('A1', 'Area Name')
->setCellValue('B1', 'Count')
->setCellValue('C1', 'Total Amount');

foreach ($array_summary_area as $key => $value) {

    $col2++;

    $objPHPExcel->setActiveSheetIndex(1)
    ->setCellValue('A'.$col2, $value['area_name'])
    ->setCellValue('B'.$col2, $value['count'])
    ->setCellValue('C'.$col2, $value['total_amount']);
}

$objPHPExcel->getActiveSheet(0)->setTitle('Summary');

$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="report_residue_'.time().'.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

ob_end_clean();
ob_start();

$objWriter->save('php://output');
exit;
}

private function _exportExcelSaleCN($sql) {

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    require_once 'PHPExcel.php';

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'SALE ORDER NUMBER')
    ->setCellValue('B1', 'RETAILER NAME')
    ->setCellValue('C1', 'SALES QUANTITY')
    ->setCellValue('D1', 'TOTAL (EXCLUDE VAT)')
    ->setCellValue('E1', 'SPECIAL DISCOUNT (EXCLUDE VAT)')
    ->setCellValue('F1', 'TOTAL AMOUNT (INCLUDE VAT)')
    ->setCellValue('G1', 'DISCOUNT (EXCLUDE VAT)')
    ->setCellValue('H1', 'DISCOUNT (INCLUDE VAT)')
    ->setCellValue('I1', 'DEPOSIT')
    ->setCellValue('J1', 'NAT PAID (INCLUDE VAT)')
    ->setCellValue('K1', 'PAYMENT OR NOT')
    ->setCellValue('L1', 'STATUS');

    $array_summary_area = array();

    $col1 = 1;
    $col2 = 1;

    $data = $db->query($sql);

    foreach ($data as $key => $value) {

        $col1++;

        $product_unit_price = $value['price'];
        $product_qty        = $value['total_qty'];
        $price_total        = $value['total_price_amount'];
        $price_total_invat  = $value['total_price_amount_invat'];
        $sale_off_percent   = isset($value['sale_off_percent'])?$value['sale_off_percent']:'0';
        $product_unit_price = $this->cal_sale_off_percent($sale_off_percent,$product_unit_price,$product_qty,$price_total);
        $total_price = $this->ext_vat($product_unit_price);
        $product_unit_price_4 = $this->format_number_4($total_price);
        $product_amount_4 =$this->format_number_2($product_unit_price_4) * $product_qty;
        $total_amount = $product_amount_4*1;

        $total_amount = ($value['total_price_amount']);
        $delivery_fee = $value['delivery_fee']/1;

        if($value['rank'] == 9){
            $total_amount = $price_total_invat;
        }

        $special_discount = 0;
        date_default_timezone_set('Asia/Bangkok');
        $date = new DateTime('2017-01-04 00:00:00');
        $date_start= date_format($date,"Y-m-d H:i:s");
        $date_order = $value['add_time'];

        if($date_order < $date_start){
            if ($value['d_id'] == 3025 || $value['d_id'] == 3691) {

              $special_discount = $this->format_number_2(($total_amount*1/100));
          }

      }else{
        $special_discount = ($value['total_spc_discount']);
    }

    $d_id = $value['d_id'];
    if ($d_id=='25760' || $d_id=='21088' || $d_id=='25550'){
        $total_amount_in_vat = (($total_amount+$delivery_fee-$special_discount));
    }else{
        $total_amount_in_vat = (($total_amount+$delivery_fee-$special_discount)*1);
    }

    $gran_total  = ($total_amount_in_vat - $value['total_discount'] - $value['total_deposit']);

    if ($gran_total <= 0) {
        $gran_total = 0;
    }

    $gran_total = $this->format_number_2($gran_total);

    if($value['delivery_fee']>0){
        $delivery_fee ="Delivery fee = ".$value['delivery_fee'];
    }else{
        $delivery_fee ="";
    }

    $paymeny_status = 'No';
    $shipping_status = 'No';
    $out_status = 'No';

    $order_status = '';
    if ($value['status']==1){
        $order_status = 'Active';
    }else{
        $order_status = 'Expired';
    }

    if ($value['pay_time']){
        $paymeny_status = 'Yes';
    }

    if ($value['shipping_yes_time']){
        $shipping_status = 'Yes';
    }

    if ($value['outmysql_time']){
        $out_status = 'Yes';
    }

    if(isset($array_summary_area[$value['area_id']])){

        $array_summary_area[$value['area_id']]['count'] = $array_summary_area[$value['area_id']]['count']+1;
        $array_summary_area[$value['area_id']]['total_amount'] = $array_summary_area[$value['area_id']]['total_amount']+$gran_total;
    }else{
        $data = array(
            'area_id' => $value['area_id'],
            'area_name' => $value['area_name'],
            'count' => 1,
            'total_amount' => $gran_total
        );
        $array_summary_area[$value['area_id']] = $data;
    }

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A'.$col1, $value['sn_ref'])
    ->setCellValue('B'.$col1, '['.$value['store_code'].'] '.$value['title'])
    ->setCellValue('C'.$col1, $value['total_qty'])
    ->setCellValue('D'.$col1, My_Number::f($total_amount, 0, ',', '.'))
    ->setCellValue('E'.$col1, My_Number::f($special_discount, 0, ',', '.'))
    ->setCellValue('F'.$col1, My_Number::f($total_amount_in_vat, 0, ',', '.'))
    ->setCellValue('G'.$col1, My_Number::f(($value['total_discount']/1), 0, ',', '.'))
    ->setCellValue('H'.$col1, My_Number::f($value['total_discount'], 0, ',', '.'))
    ->setCellValue('I'.$col1, My_Number::f($value['total_deposit'], 0, ',', '.'))
    ->setCellValue('J'.$col1, My_Number::f($gran_total, 0, ',', '.'))
    ->setCellValue('K'.$col1, $paymeny_status)
    ->setCellValue('L'.$col1, $order_status);
}

$objPHPExcel->getActiveSheet(0)->setTitle('Details');

$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="report_sale_details_'.time().'.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

ob_end_clean();
ob_start();

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

private function _exportExcel2($sql)
{
    require_once 'ExcelWriterXML.php';

    set_time_limit(0);
    ini_set('memory_limit', -1);
    error_reporting(0);
    ini_set('display_error', 0);

    $filename = 'List_Sales_' . date('YmdHis') . '.xml';

    $xml = new ExcelWriterXML($filename);
    $xml->docAuthor('OPPO Vietnam');

    $xml->sendHeaders();

    $xml->stdOutStart();

    $sheet = $xml->addSheet('Sales');

    $heads = array(
        'Ma_KH',
        'Nguoi_MH',
        'Dia_Chi',
        'Quyet_So',
        'So_seri',
        'So_hoa_don',
        'Ngay_chung_tu',
        'So_luong',
        'Gia',
        'Tien',
        'Gia_von',
        'Tien_von',
        'Ty_le_chiet_khau',
        'Ma_thue',
        'Tai_khoan_no',
        'Tai_khoan_doanh_thu',
        'Tai_khoan_kho',
        'Tai_khoan_gia_vo',
        'Tai_khoan_chiec_khau',
        'Tai_khoan_thue',
        'Ma_kho',
        'Ma_vat_tu',
        'Dien_giai',
        'Han_thanh_toan'

    );

    $sheet->stdOutSheetStart();


    $sheet->stdOutSheetRowStart(1);
    foreach ($heads as $k => $item)
    {
        $sheet->stdOutSheetColumn('String', 1, $k + 1, $item);
    }
    $sheet->stdOutSheetRowEnd();

    $QGood = new Application_Model_Good();
    $QDistributor = new Application_Model_Distributor();
    $QDistributorPo = new Application_Model_DistributorPo();
    $po_cache = $QDistributorPo->get_cache();

    $goods = $QGood->get_cache();
    $distributors = $QDistributor->get_cache2();

//load config
    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
    $config = $config->toArray();
    $con = mysqli_connect($config['resources']['db']['params']['host'], $config['resources']['db']['params']['username'],
        $config['resources']['db']['params']['password'], $config['resources']['db']['params']['dbname']);
    mysqli_set_charset($con, "utf8");

    $result = mysqli_query($con, $sql);

    $i = 2;

    while ($item = mysqli_fetch_assoc($result))
    {
        $sheet->stdOutSheetRowStart($i);

        $j = 1;

        $store_code = '';

        if (isset($distributors) && isset($distributors[$item['d_id']]))
        {
            $distributor = $distributors[$item['d_id']];

            $store_code = $distributor['store_code'];
        }


        $good_name = '';
        if (isset($goods) && isset($goods[$item['good_id']]))
            $good_name = $goods[$item['good_id']];

        $Quyet_So = '01GTKT3';
        $Ma_kho = '';

        switch ($item['warehouse_id'])
        {
            case 1:
            $So_seri = 'QQ/14P';
            $Ma_kho = 'Q4HCM';
            break;
            case 2:
            $So_seri = 'HN/14P';
            $Ma_kho = 'HN';
            break;
            case 3:
            $So_seri = 'DN/14P';
            $Ma_kho = 'DANANG';
            break;
        }

        $Nguoi_MH = isset($distributors[$item['d_id']]['unames']) ? $distributors[$item['d_id']]['unames'] :
        '';
        $Dia_Chi = isset($distributors[$item['d_id']]['add']) ? $distributors[$item['d_id']]['add'] :
        '';
        $So_hoa_don = $item['invoice_number'];
        $Ngay_chung_tu = date('d/m/Y', strtotime($item['outmysql_time']));
        $So_luong = $item['num'];
        $Gia = round(($item['total'] / 1.1) / $So_luong, 2);
        $Tien = $Gia * $So_luong;
        $Gia_von = '';
        $Tien_von = '';
        $Ty_le_chiet_khau = '';
        $Ma_thue = '10';

        $Tai_khoan_doanh_thu = $Tai_khoan_no = '';
        switch ($item['type'])
        {
            case 1:
            $Tai_khoan_no = '131A';
            $Tai_khoan_doanh_thu = '511A';
            break;
            case 2:
            $Tai_khoan_no = '131E';
            $Tai_khoan_doanh_thu = '511E';
            break;
            case 3:
            $Tai_khoan_no = '131D';
            $Tai_khoan_doanh_thu = '511D';
            break;
        }


        $Tai_khoan_kho = '1561';
        $Tai_khoan_gia_vo = '632';
        $Tai_khoan_chiec_khau = '5321';
        $Tai_khoan_thue = '33311';

        $Ma_vat_tu = $good_name;
// xem comment bÃƒÂªn khai bÃƒÂ¡o hÃƒÂ m
        $Dien_giai = My_Sale_Order::getStockTetNote($item['sn']);

        if (empty($Dien_giai))
        {
            $Dien_giai = isset($po_cache[$item['po_id']]) ? $po_cache[$item['po_id']] : '';
            $Dien_giai .= "\r\n" . $item['text'];
        }

        $Han_thanh_toan = '';

        $sheet->stdOutSheetColumn('String', $i, $j++, $store_code);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Nguoi_MH);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Dia_Chi);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Quyet_So);
        $sheet->stdOutSheetColumn('String', $i, $j++, $So_seri);
        $sheet->stdOutSheetColumn('String', $i, $j++, $So_hoa_don);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Ngay_chung_tu);
        $sheet->stdOutSheetColumn('String', $i, $j++, $So_luong);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Gia);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Tien);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Gia_von);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Tien_von);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Ty_le_chiet_khau);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Ma_thue);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Tai_khoan_no);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Tai_khoan_doanh_thu);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Tai_khoan_kho);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Tai_khoan_gia_vo);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Tai_khoan_chiec_khau);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Tai_khoan_thue);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Ma_kho);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Ma_vat_tu);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Dien_giai);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Han_thanh_toan);
        $sheet->stdOutSheetRowEnd();
        $i++;

    }

    $sheet->stdOutSheetEnd();

    $xml->stdOutEnd();

    exit;
}

private function _exportExcel3($sql)
{
    require_once 'ExcelWriterXML.php';
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    $filename = 'List_Sales_For_Finance_' . date('YmdHis') . '.xml';

    $xml = new ExcelWriterXML($filename);
    $xml->docAuthor('OPPO Vietnam');

    $xml->sendHeaders();

    $xml->stdOutStart();

    $sheet = $xml->addSheet('Sales');

    $heads = array(
        'STT',
        'KyHieuMauHoaDon',
        'KyHieuHoaDon',
        'SoHoaDon',
        'SoDonHang',
        'Ngay',
        'MaKH',
        'TenNguoiMua',
        'MST',
        'MatHang',
        'BVG',
        'SoTienBVG',
        'GiaTriDonHang',
        'DoanhSoChuaThue',
        'ThueGTGT',
        'GiaTriDonHangHD',
        'DoanhSoChuaThueHD',
        'ThueGTGTHD',
        'GhiChu',
    );

    $sheet->stdOutSheetStart();


    $sheet->stdOutSheetRowStart(1);
    foreach ($heads as $k => $item)
    {
        $sheet->stdOutSheetColumn('String', 1, $k + 1, $item);
    }
    $sheet->stdOutSheetRowEnd();

//load config
    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
    $config = $config->toArray();
    $con = mysqli_connect($config['resources']['db']['params']['host'], $config['resources']['db']['params']['username'],
        $config['resources']['db']['params']['password'], $config['resources']['db']['params']['dbname']);
    mysqli_set_charset($con, "utf8");

    $result = mysqli_query($con, $sql);
    $QProductBVG = new Application_Model_MarketProduct();
    $QInvoiceSign = new Application_Model_InvoicePrefix();
    $QMarket_invoice_price = new Application_Model_MarketInvoicePriceSn();
    $QMarket_deduction = new Application_Model_MarketDeduction();
    $prefix = $QInvoiceSign->get_cache();
    $QDistributorPo = new Application_Model_DistributorPo();
    $po_cache = $QDistributorPo->get_cache();


    $i = 2;

    $KyHieuMauHoaDon = '01GTKT3/0';
    $MatHang = 'ÃƒÂi?n tho?i di d?ng';


    $k = 1;

    while ($item = mysqli_fetch_assoc($result))
    {
        $sheet->stdOutSheetRowStart($i);

        $j = 1;

        if (isset($prefix[$item['invoice_sign']]) and $prefix[$item['invoice_sign']])
            $KyHieuHoaDon = $prefix[$item['invoice_sign']];
        else
        {
    //th nh?ng s? hÃƒÂ³a don cu
            switch ($item['warehouse_id'])
            {
                case 1:
                $KyHieuHoaDon = 'QQ/14P';
                break;
                case 2:
                $KyHieuHoaDon = 'HN/14P';
                break;
                case 3:
                $KyHieuHoaDon = 'DN/14P';
                break;
                default:
                $KyHieuHoaDon = 'QQ/14P';
                break;
            }
        }


        $SoHoaDon = $item['invoice_number'];
        $Ngay = ($item['invoice_time'] ? date('d/m/Y', strtotime($item['invoice_time'])) :
            '');
        $SoDonHang = $item['sn'];
        $giaTriDonHang = $item['total_price'];
        $TenNguoiMua = $item['unames'];
        $MaNguoiMua = $item['store_code'];
        $MST = $item['mst_sn'];
        $total_price = $item['total_price'];
        $bvg = '';
        $price_bvg = 0;

        $discount = $QProductBVG->getDiscount($item['sn']);


        if (isset($discount) and $discount)
        {
            $invoice_discount = $QProductBVG->getInvoice($item['sn']);
            $params = array(
                'sn' => $SoDonHang,
                'isbacks' => 0,
                'group_good' => 1,
            );

            $params['group_sn'] = 1;
            $total2 = 0;


            if ($discount == 1)
            {
                $bvg = 'BVG';
        // $discount_result = $QProductBVG->fetchPagination(1, null, $total2, $params);
            } elseif ($discount == 2)
            {
                $bvg = 'CK';
        //   $discount_result = $QMarket_deduction->fetchPagination(1, null, $total2, $params);
            }
            $price_bvg = $QProductBVG->getPrice($item['sn']);
        }


        $DoanhSoChuaThue = round($total_price / 1.1, 2);
        $ThueGTGT = round($DoanhSoChuaThue * 0.1, 2);
        $where = array();
        $where[] = $QMarket_invoice_price->getAdapter()->quoteInto('sn = ?', $item['sn']);
//  $where[] = $QMarket_invoice_price->getAdapter()->quoteInto('invoice_number = ?',
//    $item['invoice_number']);
        $invoice_price = $QMarket_invoice_price->fetchRow($where);

// xem comment ? khai bÃƒÂ¡o hÃƒÂ m
        $GhiChu = My_Sale_Order::getStockTetNote($item['sn']);

        if (empty($GhiChu))
        {
            $GhiChu = isset($po_cache[$item['po_id']]) ? $po_cache[$item['po_id']] : '';
            $GhiChu .= "\r\n" . $item['text'];
        }

        $sheet->stdOutSheetColumn('String', $i, $j++, $k);
        $sheet->stdOutSheetColumn('String', $i, $j++, $KyHieuMauHoaDon);
        $sheet->stdOutSheetColumn('String', $i, $j++, $KyHieuHoaDon);
        $sheet->stdOutSheetColumn('String', $i, $j++, $SoHoaDon);
        $sheet->stdOutSheetColumn('String', $i, $j++, $SoDonHang);
        $sheet->stdOutSheetColumn('String', $i, $j++, $Ngay);
        $sheet->stdOutSheetColumn('String', $i, $j++, $MaNguoiMua);
        $sheet->stdOutSheetColumn('String', $i, $j++, $TenNguoiMua);
        $sheet->stdOutSheetColumn('String', $i, $j++, $MST);
        $sheet->stdOutSheetColumn('String', $i, $j++, $MatHang);
        $sheet->stdOutSheetColumn('String', $i, $j++, $bvg);
        $sheet->stdOutSheetColumn('String', $i, $j++, $price_bvg ? number_format($price_bvg) :
            0);
        $sheet->stdOutSheetColumn('String', $i, $j++, number_format($giaTriDonHang));
        $sheet->stdOutSheetColumn('String', $i, $j++, number_format($DoanhSoChuaThue));
        $sheet->stdOutSheetColumn('String', $i, $j++, number_format($ThueGTGT));
        $sheet->stdOutSheetColumn('String', $i, $j++, number_format($invoice_price['total_invoice_after_vat']));
        $sheet->stdOutSheetColumn('String', $i, $j++, number_format($invoice_price['total_invoice_price']));
        $sheet->stdOutSheetColumn('String', $i, $j++, number_format($invoice_price['total_invoice_vat']));
        $sheet->stdOutSheetColumn('String', $i, $j++, $GhiChu);
        $sheet->stdOutSheetRowEnd();
        $i++;
        $k++;

        if (isset($discount) and $discount)
        {

            $sheet->stdOutSheetRowStart($i);
            $j = 1;
            $total_price = $price_bvg;
            $DoanhSoChuaThue = round($total_price / 1.1, 2);
            $ThueGTGT = round($DoanhSoChuaThue * 0.1, 2);
            $where = array();
            $where[] = $QMarket_invoice_price->getAdapter()->quoteInto('sn = ?', $item['sn']);
            $where[] = $QMarket_invoice_price->getAdapter()->quoteInto('invoice_number = ?',
                $discount_result['invoice_number']);
            $invoice_price = $QMarket_invoice_price->fetchRow($where);
            $invoice_price = $invoice_price ? $invoice_price : null;
            $sheet->stdOutSheetColumn('String', $i, $j++, $k);
            $sheet->stdOutSheetColumn('String', $i, $j++, $KyHieuMauHoaDon);
            $sheet->stdOutSheetColumn('String', $i, $j++, $KyHieuHoaDon);
            $sheet->stdOutSheetColumn('String', $i, $j++, $invoice_discount);
            $sheet->stdOutSheetColumn('String', $i, $j++, $SoDonHang);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Ngay);
            $sheet->stdOutSheetColumn('String', $i, $j++, $MaNguoiMua);
            $sheet->stdOutSheetColumn('String', $i, $j++, $TenNguoiMua);
            $sheet->stdOutSheetColumn('String', $i, $j++, $MST);
            $sheet->stdOutSheetColumn('String', $i, $j++, $MatHang);
            $sheet->stdOutSheetColumn('String', $i, $j++, $bvg);
            $sheet->stdOutSheetColumn('String', $i, $j++, 0);
            $sheet->stdOutSheetColumn('String', $i, $j++, '-' . number_format($total_price));
            $sheet->stdOutSheetColumn('String', $i, $j++, '-' . number_format($DoanhSoChuaThue));
            $sheet->stdOutSheetColumn('String', $i, $j++, '-' . number_format($ThueGTGT));
            $sheet->stdOutSheetColumn('String', $i, $j++, '-' . number_format($total_price));
            $sheet->stdOutSheetColumn('String', $i, $j++, '-' . number_format($DoanhSoChuaThue));
            $sheet->stdOutSheetColumn('String', $i, $j++, '-' . number_format($ThueGTGT));
            $sheet->stdOutSheetColumn('String', $i, $j++, $GhiChu);
            $sheet->stdOutSheetRowEnd();
            $i++;
            $k++;
        }

    }

    $sheet->stdOutSheetEnd();

    $xml->stdOutEnd();

    exit;
}

private function _exportExcel4($data)
{
////////////////////////////////////////////////////
/////////////////// KH?I T?O ÃƒÂ? XU?T CSV
////////////////////////////////////////////////////
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Sell out List - IMEI - '.date('d-m-Y H-i-s').'.csv';
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
/////////////////// T?NG H?P D? LI?U
////////////////////////////////////////////////////

$heads = array(
    'SALE ORDER NUMBER',
    'PRODUCT CATEGORY',
    'PRODUCT NAME',
    'PRODUCT COLOR',
    'IMEI',
    'WAREHOUSE',
    'RETAILER',
    'CODE',
    'REGION',
    'AREA',
    'OUT TIME',
    'ACTIVATED_DATE',
    'GIA HE THONG',
    'GIA THUC TE',
    'INVOICE NUMBER',
    'INVOICE DATE',
    'INVOICE SIGN',
    'ORDER TYPE',
    'RETURN'
);

fputcsv($output, $heads);

$QGood          = new Application_Model_Good();
$QGoodColor     = new Application_Model_GoodColor();
$QDistributor   = new Application_Model_Distributor();
$QGoodCategory  = new Application_Model_GoodCategory();
$QWarehouse     = new Application_Model_Warehouse();
$QRegion        = new Application_Model_Region();
$QArea          = new Application_Model_Area();
$QMarket        = new Application_Model_Market();
$QInvoicePrefix = new Application_Model_InvoicePrefix();

$goods             = $QGood->get_cache();
$goodColors        = $QGoodColor->get_cache();
$distributor_cache = $QDistributor->get_cache2();
$good_categories   = $QGoodCategory->get_cache();
$warehouses        = $QWarehouse->get_cache();
$invoice_prefix    = $QInvoicePrefix->get_cache();

$result = $db->query($data);
//print_r($result);die;
$i = 2;

foreach($result as $item)
{
    if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
    else { $temp_sn = $item['sn_ref']; }

    $row = array();
    $row[] = '="'.$temp_sn.'"';
//$row[] = '="'.$item['sn'].'"';
    $row[] = $good_categories[$item['cat_id']];
    $row[] = $goods[$item['good_id']];
    $row[] = $goodColors[$item['good_color']];
    $row[] = $item['imei_sn'];
    $row[] = isset($warehouses[$item['warehouse_id']])? $warehouses[$item['warehouse_id']] : '';
    $row[] = isset($distributor_cache[ $item['d_id'] ]['title']) ? $distributor_cache[ $item['d_id'] ]['title'] : '';
    $row[] = isset($distributor_cache[ $item['d_id'] ]['code']) ? $distributor_cache[ $item['d_id'] ]['code'] : '';
    $row[] = isset($distributor_cache[ $item['d_id'] ]['district']) ? My_Region::getValue($distributor_cache[ $item['d_id'] ]['district'], My_Region::Area) : '';
    $row[] = isset($distributor_cache[ $item['d_id'] ]['district']) ? My_Region::getValue($distributor_cache[ $item['d_id'] ]['district'], My_Region::Province) : '';
    $row[] = $item['outmysql_time'];
    $row[] = $item['activated_date'];


    if ( isset($item['imei_sn']) ) {

        $price_product_relative = $price_export_relative = $invoice_time = 0;
        $order_type             = '';

        $price_product_relative = $item['price'];
        $price_export_relative  = intval($item['total'] / $item['num']) ? intval($item['total'] / $item['num']) : 0;
        switch ( $item['type'] ) {
            case '1':
            $order_type = 'Retailer';
            break;
            case '2':
            $order_type = 'Demo';
            break;
            case '3':
            $order_type = 'Staff';
            break;
            default:

            break;
        }

    }

    $row[] = isset($price_product_relative) ? $price_product_relative : '';
    $row[] = isset($price_export_relative) ? $price_export_relative : 0;
    $row[] = isset($item['invoice_number']) ? $item['invoice_number'] : 'x';
    $row[] = isset($item['invoice_time']) ? $item['invoice_time'] : 'x';
    $row[] = isset($invoice_prefix[$item['invoice_sign']]) ? $invoice_prefix[$item['invoice_sign']] : 'x';
    $row[] = $order_type;
    $row[] = $item['return_sn'] ? $item['return_sn'] : '';

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

private function _export_target($target)
{
    set_time_limit(0);
    error_reporting(0);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);

    $tmp_name = md5(uniqid("", true) . microtime(true)) . '.csv';

    $uniqid = uniqid('', true);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    if (!$userStorage)
        exit;

    $save_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
    'public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'sales' .
    DIRECTORY_SEPARATOR . 'target' . DIRECTORY_SEPARATOR . $userStorage->id .
    DIRECTORY_SEPARATOR . $uniqid;

    if (!is_dir($save_dir))
        @mkdir($save_dir, 0777, true);

    $fullpath = $save_dir . DIRECTORY_SEPARATOR . $tmp_name;

    $output = fopen($fullpath, 'w');

    $heads = array(
        'No.',
        'Area',
        'Retailer ID',
        'Retailer Name',
        'Target',
        'Value',
    );

    fputcsv($output, $heads);

    $QArea = new Application_Model_Area();
    $areas = $QArea->get_cache();

    $QDistributor = new Application_Model_Distributor();
    $distributors = $QDistributor->get_cache();

    $i = 1;

    foreach ($target as $value)
    {
        $row = array();
        $alpha = 'A';
        $row[] = $i++;
        $row[] = isset($areas[$value['area_id']]) ? $areas[$value['area_id']] : '';
        $row[] = $value['d_id'];
        $row[] = isset($distributors[$value['d_id']]) ? $distributors[$value['d_id']] :
        '';
        $row[] = $value['target'];
        $row[] = $value['total'];

        fputcsv($output, $row);
    }

    fclose($fullpath);

    $filename = 'Target - Distributor - ' . date('d-m-Y H-i-s') . ".csv";
    header('Content-Description: File Transfer');
    header('Content-Encoding: UTF-8');
    header('Content-Disposition: attachment; filename=' . $filename);
// echo "\xEF\xBB\xBF"; // UTF-8 BOM
    header('X-Rack-Cache: miss');
    header('ETag: ' . hash("sha1", microtime(true) . uniqid()));
    header('Content-Type: application/csv; charset=utf-8');
// header('Expires: 0');
    header('Cache-Control:  max-age=0, no-cache, no-store');
    header('Pragma: no-cache');
    header('Content-Length: ' . filesize($fullpath));

ob_clean(); // discard any data in the output buffer (if possible)
flush(); // flush headers (if possible)

echo chr(239) . chr(187) . chr(191); // UTF-8 BOM

readfile($fullpath);

exit;
}

public function createOrderAction()
{

}

public function createOrderExcelAction()
{
    $this->_helper->layout->disableLayout();

    $save_folder = 'importorder';
    $new_file_path = '';
    $requirement = array(
        'Size' => array('min' => 5, 'max' => 5000000),
        'Count' => array('min' => 1, 'max' => 1),
        'Extension' => array('xls', 'xlsx'),
    );

    try
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);

        $file = My_File::get($save_folder, $requirement);

        if (!$file || !count($file))
            throw new Exception("Upload failed");

        $inputFileName = My_File::getDefaultDir() . DIRECTORY_SEPARATOR . $save_folder .
        DIRECTORY_SEPARATOR . $file['folder'] . DIRECTORY_SEPARATOR . $file['filename'];

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    }
    catch (exception $e)
    {
        $this->view->errors = $e->getMessage();
        return;
    }

//read file
    include 'PHPExcel/IOFactory.php';

//  Read your Excel workbook
    try
    {
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);
    }
    catch (exception $e)
    {
        $this->view->errors = $e->getMessage();
        return;
    }

//  Get worksheet dimensions
    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();
    $db = Zend_Registry::get('db');
    $arrDistributorGood = array();
    $failed_list = array();
    $order_by_distributor = array();

    for ($row = 2; $row <= ($highestRow + 1); $row++)
    {
//  Read a row of data into an array
        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
        $rowData = $rowData[0];

        $old_distributor_id = isset($info['d_id']) ? $info['d_id'] : null;
        $old_date = isset($out_date_tmp) ? $out_date_tmp : null;

        if ((empty($rowData[0]) && empty($rowData[1])))
        {

            if (is_array($order_by_distributor) && count($order_by_distributor))
            {
                try
                {
                    $db->beginTransaction();
                    $this->_createGroupOrder($order_by_distributor);
                    $db->commit();
                }
                catch (exception $e)
                {
                    $db->rollback();
                    $failed_list[$row] = $e->getMessage();
                }

                $order_by_distributor = array();
            }

            break;
        }

//row data from excel
        $info = array(
            'd_id' => intval(trim($rowData[0])),
            'out_date' => '17/02/2015',
            'region' => trim($rowData[2]),
            'title' => trim($rowData[3]),
            'good_name' => trim($rowData[4]),
            'color_name' => trim($rowData[5]),
            'imei' => trim($rowData[6]),
            'price' => intval(trim($rowData[7])),
            'office' => trim($rowData[11]),
            'note' => trim($rowData[9]),
        );


// n?u th?y ngÃƒÂ y out m?i thÃƒÂ¬ tÃƒÂ¡ch don dÃƒÂ¹ cÃƒÂ¹ng dealer id
        if (!is_null($old_date) && isset($out_date_tmp) && $old_date != $out_date_tmp)
        {
            if (is_array($order_by_distributor) && count($order_by_distributor))
            {
                try
                {
                    $db->beginTransaction();
                    $this->_createGroupOrder($order_by_distributor);
                    $db->commit();
                }
                catch (exception $e)
                {
                    $db->rollback();
                    $failed_list[$row] = $e->getMessage();
                }

                $order_by_distributor = array();
            }
        }

// validate
        try
        {
            $QOffice = new Application_Model_Office();
            $office = $QOffice->get_warehouse($info['office']);

            if (!$office)
                throw new Exception("Wrong warehouse", 3);

            $info['warehouse_id'] = $office;

            if ($info['good_name'] == 'PT5111-Blue')
            {
                $info['good_id'] = 169;
                $info['good_color'] = 28;
                $info['price'] = 0;
                $info['cat_id'] = ACCESS_CAT_ID;

            } else
            {
                $this->_validateDataRow($info);
            }

        }
        catch (exception $e)
        {
            $failed_list[$row] = $e->getMessage();
            continue;
        }


        if (!is_null($old_distributor_id) && $info['d_id'] != $old_distributor_id)
        {
    // create Orders by group

            try
            {
                $db->beginTransaction();
                unset($info['out_date_tmp']);
                $this->_createGroupOrder($order_by_distributor);
                $db->commit();

            }
            catch (exception $e)
            {
                $db->rollback();

                $failed_list[$row] = $e->getMessage();
            }

            $order_by_distributor = array();
        }

// group by Order SN
        $order_by_distributor[] = $info;

} //end for

$objPHPExcel_out = new PHPExcel();
$objPHPExcel_out->createSheet();
$objWorksheet_out = $objPHPExcel_out->getActiveSheet();
$index = 1;

$row = array(
    'ID Dealer',
    'NgÃƒÂ y',
    'KV',
    'TÃƒÂªn c?a hÃƒÂ ng',
    'MÃƒÂ£ hÃƒÂ ng',
    'MÃƒÂ u',
    'IMEI',
    'ÃƒÂon giÃƒÂ¡',
    'ThÃƒÂ nh Ti?n',
    'NOTE',
    'OUT Date',
    'Distributor',
);

$objWorksheet_out->fromArray($row, null, 'A' . $index++);

foreach ($failed_list as $row => $reason)
{
//  Read a row of data into an array
    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
    $rowData = $rowData[0];

    if (isset($rowData['imei']))
        $rowData['imei'] = '\'' . $rowData['imei'];

    $rowData[] = $reason;

    $objWorksheet_out->fromArray($rowData, null, 'A' . $index++);
} // export error list

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel_out);
$new_file_dir = My_File::getDefaultDir() . DIRECTORY_SEPARATOR . $save_folder .
DIRECTORY_SEPARATOR . $file['folder'] . DIRECTORY_SEPARATOR . $file['filename'] .
'-failed.xlsx';

$objWriter->save($new_file_dir);

$this->view->failed_list = $failed_list;
} //end func create order

/**
* Ki?m tra d? li?u
* Dealer ID, KV, tÃƒÂªn c?a hÃƒÂ ng, mÃƒÂ£ hÃƒÂ ng, mÃƒÂ u, IMEI cÃƒÂ³ kh?p nhau, dÃƒÂºng th?c t? hay khÃƒÂ´ng
* ki?m tra IMEI cÃƒÂ³ xu?t cho kho office (distributor) nÃƒÂ y chua
* @param  array  $dataRow
* @return array : code, message
*/
private function _validateDataRow(array & $dataRow)
{
// ki?m tra distributor
    $QDistributor = new Application_Model_Distributor();
    $where = $QDistributor->getAdapter()->quoteInto('id = ?', $dataRow['d_id']);
    $distributor = $QDistributor->fetchRow($where);

    if (!$distributor)
        throw new Exception("Invalid Distributor " . $dataRow['d_id'], 1);

// ki?m tra kho office
    if (!$dataRow['office'])
        throw new Exception("Empty Office", 2);

// ki?m tra giÃƒÂ¡ tr?, d?nh d?ng ngÃƒÂ y
    $date = DateTime::createFromFormat('d/m/Y', $dataRow['out_date']);

    if (!$date)
        throw new Exception("Invalid date format/value", 5);

// ki?m tra tÃƒÂªn s?n ph?m, mÃƒÂ u v?i imei kh?p hay khÃƒÂ´ng
    $QImei = new Application_Model_Imei();
    $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $dataRow['imei']);
    $imei_check = $QImei->fetchRow($where);

    if (!$imei_check)
        throw new Exception("Invalid IMEI " . $dataRow['imei'], 6);

// ki?m tra product
    $QGood = new Application_Model_Good();
    $good_cache = $QGood->get_cache();

    if (!isset($good_cache[$imei_check['good_id']]) || $good_cache[$imei_check['good_id']] !=
        $dataRow['good_name'])
        throw new Exception("Wrong Product name " . $dataRow['good_name'] .
            ", check again", 7);

    $dataRow['good_id'] = $imei_check['good_id'];

// ki?m tra color
    $QGoodColor = new Application_Model_GoodColor();
    $color_cache = $QGoodColor->get_cache();

    if (!isset($color_cache[$imei_check['good_color']]) || $color_cache[$imei_check['good_color']] !=
        $dataRow['color_name'])
        throw new Exception("Wrong Product color " . $dataRow['color_name'] .
            ", check again", 8);

    $dataRow['good_color'] = $imei_check['good_color'];

    $dataRow['cat_id'] = PHONE_CAT_ID;

// ki?m tra kho office
// $where = $QDistributor->getAdapter()->quoteInto('id = ?', $imei_check['distributor_id']);
// $distributor_office = $QDistributor->fetchRow($where);

// if (!$distributor_office || $distributor_office['title'] != $dataRow['office']) throw new Exception("Invalid Office Warehouse", 9);

    return true;
}

/**
* [_createGroupOrder description]
* @param  [type] $orders [description]
* @return [type]         [description]
*/
private function _createGroupOrder($orders)
{
    $QMarket = new Application_Model_Market();
    $QImei = new Application_Model_Imei();
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

$order_quantity = array(); // list don hÃƒÂ ng d? chÃƒÂ¨n vÃƒÂ o b?ng market
$insert_orders = array(); // list don hÃƒÂ ng d? chÃƒÂ¨n vÃƒÂ o b?ng market
$imei_order = array();
$sn = date('YmdHis') . substr(microtime(), 2, 4);
$user_id = $userStorage->id;
$add_time = $shipping_yes_time = $outmysql_time = $pay_time = $price_time = date('Y-m-d H:i:s');
$out_date = '';
$d_id = 0;
$imei_list = array();

/**
* t?o list order m?i d? chÃƒÂ¨n vÃƒÂ o b?ng market
* group vÃƒÂ  d?m t?ng s? mÃƒÂ¡y theo model, mÃƒÂ u
*/
foreach ($orders as $key => $order)
{
    if (!isset($order_quantity[$order['good_id']][$order['good_color']]))
    {
        $out_date = DateTime::createFromFormat('d/m/Y', $order['out_date'])->format('Y-m-d');
        $d_id = $order['d_id'];
        $warehouse_id = $order['warehouse_id'];

        $order_quantity[$order['good_id']][$order['good_color']]['num'] = 0;
        $order_quantity[$order['good_id']][$order['good_color']]['cat_id'] = $order['cat_id'];
        $order_quantity[$order['good_id']][$order['good_color']]['price'] = $order['price'];
        $order_quantity[$order['good_id']][$order['good_color']]['note'] = $order['note'];
    }

    $order_quantity[$order['good_id']][$order['good_color']]['num']++;
    $imei_list[] = $order['imei'];

    if ($order['cat_id'] == PHONE_CAT_ID)
        $imei_order[$order['good_id']][$order['good_color']][] = $order['imei'];
}

foreach ($order_quantity as $_good_id => $_good_color)
{
    foreach ($_good_color as $_color_id => $item)
    {
        $insert_orders[] = array(
            'sn' => $sn,
            'd_id' => $d_id,
            'user_id' => $user_id,
            'num' => $item['num'],
            'add_time' => $add_time,
            'price' => $item['price'],
            'total' => $item['num'] * $item['price'],
            'shipping_yes_time' => $shipping_yes_time,
            'pay_time' => $pay_time,
            'shipping_yes_id' => $user_id,
            'pay_user' => $user_id,
            'outmysql_time' => $out_date,
            'outmysql_user' => $user_id,
            'price_time' => $price_time,
            'good_color' => $_color_id,
            'good_id' => $_good_id,
            'cat_id' => $item['cat_id'],
            'text' => $item['note'] . '--Imported by tool',
            'isbatch' => 1,
            'status' => 1,
            'life_time' => ORDER_TIMELIFE,
            'type' => FOR_RETAILER,
            'warehouse_id' => $warehouse_id,
            'salesman' => $user_id,
        );
    }
}


foreach ($insert_orders as $item)
{
//
// chÃƒÂ¨n don
    $id = $QMarket->insert($item);

    if (!$id)
        throw new Exception("Cannot insert sales order", 1);

    if ($item['cat_id'] == ACCESS_CAT_ID)
        continue;

// update num trÃƒÂªn don cu
    $imei_ = $imei_order[$item['good_id']][$item['good_color']];

    foreach ($imei_ as $imei__)
    {
        $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei__);
        $imei_check = $QImei->fetchRow($where);

        if (!$imei_check)
            throw new Exception("Cannot find IMEI " . $imei__, 1);

    // tr? stock imei
        $where = array();
        $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $imei_check['sales_sn']);
        $where[] = $QMarket->getAdapter()->quoteInto('good_id = ?', $item['good_id']);
        $where[] = $QMarket->getAdapter()->quoteInto('good_color = ?', $item['good_color']);
        $market_check = $QMarket->fetchRow($where);

        if (!$market_check)
            throw new Exception("Cannot find sales order " . $imei_check['sales_sn'], 1);

        $data = array('num' => ($market_check['num'] - 1));
        $QMarket->update($data, $where);

        if ($item['good_id'] == 168)
    { // N1 mini - cÃƒÂ³ hÃƒÂ ng t?ng kÃƒÂ¨m
        // tr? stock ph? ki?n luÃƒÂ´n
        $where = array();
        $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $market_check['sn']);
        $where[] = $QMarket->getAdapter()->quoteInto('good_id = ?', 169);
        $where[] = $QMarket->getAdapter()->quoteInto('good_color = ?', 28);
        $market_check = $QMarket->fetchRow($where);

        if (!$market_check)
            throw new Exception("Cannot find sales order " . $market_check['sn'], 1);

        $data = array('num' => ($market_check['num'] - 1));
        $QMarket->update($data, $where);

    } // end if ; tr? stock ph? ki?n

} // end foreach

// update b?ng imei theo don
$where = $QImei->getAdapter()->quoteInto('imei_sn IN (?)', $imei_);

$data = array(
    'out_date' => $out_date,
    'distributor_id' => $d_id,
    'sales_sn' => $sn,
    'out_price' => $item['price'],
    'price_date' => $item['outmysql_time'],
    'sales_id' => $id,
);

$QImei->update($data, $where);

//
//
} // end foreach $orders array
}

/**
* hoang.hien
* ki?m tra n?u mÃƒÂ  di?n tho?i ch?n m?c gi?m giÃƒÂ¡ ko cÃƒÂ³ trong b?ng gi?m giÃƒÂ¡(good_sale_phone) thÃƒÂ¬ xu?t thÃƒÂ´ng bÃƒÂ¡o
*/
public function checkSalePhoneAction()
{
    $phone_id             = $this->getRequest()->getParam('phone_id');
    $sale_off_percent     = $this->getRequest()->getParam('sale_off_percent');

    $QCheckSale = new Application_Model_GoodSalePhone();
    $check_sale = $QCheckSale->checkSalePhone(intval($phone_id),intval($sale_off_percent));
    echo $check_sale;
    exit;
}

/**
* [hoang.hien]ThÃƒÂªm view gi?m giÃƒÂ¡
* Tuong ?ng v?i 1 di?n tho?i thÃƒÂ¬ gi?m bao nhiÃƒÂªu %
*/

public function saleAction(){
    $QSale = new Application_Model_GoodSale();
    $data = $QSale->fetchAll();

    $this->view->data = $data;
}

public function saveSaleAction(){

    $id = $this->getRequest()->getParam('id');
    $type = $this->getRequest()->getParam('type');
    $sale = $this->getRequest()->getParam('sale');

    $data = array(
        'type'             => $type,
        'sale'             => $sale
    );

    $QSale = new Application_Model_GoodSale();
    if($id){
        $where = $QSale->getAdapter()->quoteInto('id = ?', $id);
        $QSale->update($data,$where);
        $this->_redirect(HOST . 'sales/edit-sale?id='.$id);
    }
    else{
        $QSale->insert($data);
        $this->_redirect(HOST . 'sales/sale');
    }

}

public function editSaleAction(){
    $id = $this->getRequest()->getParam('id');
    $QSale = new Application_Model_GoodSale();
    $where = $QSale->getAdapter()->quoteInto('id = ?', $id);
    $data = $QSale->fetchRow($where);

    $this->view->data = $data;
}

public function salePhoneAction(){
    $QSale = new Application_Model_GoodSale();
    $dataSale = $QSale->fetchAll();

    $QGood = new Application_Model_Good();
    $dataGood = $QGood->fetchAll();

    $QSalePhone = new Application_Model_GoodSalePhone();
    $data = $QSalePhone->getAllSalePhone();

    $QGoodCategory = new Application_Model_GoodCategory();
    $this->view->good_categories = $QGoodCategory->fetchAll();

    $this->view->dataSale = $dataSale;
    $this->view->dataGood = $dataGood;
    $this->view->data = $data;
}

public function deleteSaleAction(){
    $id = $this->getRequest()->getParam('id_sale');
    $QSale = new Application_Model_GoodSale();
    $where = $QSale->getAdapter()->quoteInto('id = ?', $id);
    $QSale->delete($where);
    echo "successful";
    exit;
}

public function changeSaleCheckboxAction(){
    $good_id = $this->getRequest()->getParam('good_id');
    $phone_sale_id = $this->getRequest()->getParam('phone_sale_id');
    $check = $this->getRequest()->getParam('check');

    if((int)$check == 1){
        $QSalePhone = new Application_Model_GoodSalePhone();
        $data = array(
            'good_id' => $good_id,
            'good_sale_id' => $phone_sale_id
        );
        $id = $QSalePhone->insert($data);

        echo "successful";
        exit;
    }
    elseif(((int)$check == 0)){
        $QSalePhone = new Application_Model_GoodSalePhone();
        $where[] = $QSalePhone->getAdapter()->quoteInto('good_id = ?', $good_id);
        $where[] = $QSalePhone->getAdapter()->quoteInto('good_sale_id = ?', $phone_sale_id);
        $QSalePhone->delete($where);

        echo "successful";
        exit;
    }
    elseif(((int)$check == 3)){
        $QSalePhone = new Application_Model_GoodSalePhone();
        $data = array(
            'good_id' => $good_id,
            'good_sale_id' => $phone_sale_id
        );
        $id = $QSalePhone->insert($data);
        $this->_redirect(HOST . 'sales/sale-phone');
    }

}



/**
* [hoang.hien]ThÃƒÂªm view lÃƒÂ´ hÃƒÂ ng (bÃƒÂ¡n cho nhÃƒÂ¢n viÃƒÂªn)
* Tuong ?ng v?i lÃƒÂ´ thÃƒÂ¬ cÃƒÂ³ cÃƒÂ¡c s?n ph?m cÃƒÂ³ giÃƒÂ¡ khÃƒÂ¡c nhau %
*/
public function shipmentAction(){
    $QShipment = new Application_Model_GoodShipment();
    $dataShipment = $QShipment->fetchAll();
    $this->view->data = $dataShipment;
}


public function saveShipmentAction(){
    $id = $this->getRequest()->getParam('id');
    $name = $this->getRequest()->getParam('name');
    $number_shipment = $this->getRequest()->getParam('number_shipment');
    $content = $this->getRequest()->getParam('content');
    $active = $this->getRequest()->getParam('active');
    $check = $this->getRequest()->getParam('check');

    if((int)$check == 1){
        $QShipment = new Application_Model_GoodShipment();
        $data = array(
            'name'      => $name,
            'number'    => $number_shipment,
            'content'   => $content,
            'active'    => $active

        );
        $where = $QShipment->getAdapter()->quoteInto('id = ?', $id);
        $QShipment->update($data,$where);
        $this->_redirect(HOST . 'sales/shipment-edit?id='.$id);
    }
    elseif(((int)$check == 0)){
        $QSalePhone = new Application_Model_GoodSalePhone();
        $where[] = $QSalePhone->getAdapter()->quoteInto('good_id = ?', $good_id);
        $where[] = $QSalePhone->getAdapter()->quoteInto('good_sale_id = ?', $phone_sale_id);
        $QSalePhone->delete($where);

        echo "successful";
        exit;
    }
    elseif(((int)$check == 3)){
        $QShipment = new Application_Model_GoodShipment();
        $data = array(
            'name'      => $name,
            'number'    => $number_shipment,
            'content'   => $content,
            'active'    => $active

        );
        $id = $QShipment->insert($data);
        $this->_redirect(HOST . 'sales/shipment');
    }

}

public function shipmentEditAction(){
    $id = $this->getRequest()->getParam('id');
    $QShipment = new Application_Model_GoodShipment();
    $where = $QShipment->getAdapter()->quoteInto('id = ?', $id);
    $data = $QShipment->fetchRow($where);

    $this->view->data = $data;
}

public function deleteShippmentAction(){
    $id = $this->getRequest()->getParam('id_shipment');
    $QShipment = new Application_Model_GoodShipmentPhone();
    $where = $QShipment->getAdapter()->quoteInto('id = ?', $id);
    $QShipment->delete($where);
    echo "successful";
    exit;
}

public function createShipmentPhoneAction(){
    $id = $this->getRequest()->getParam('id');
    $QShipmentPhone = new Application_Model_GoodShipmentPhone();
    $where = $QShipmentPhone->getAdapter()->quoteInto('id = ?', $id);
    $data = $QShipmentPhone->fetchRow($where);

    $QShipment = new Application_Model_GoodShipment();
    $dataShipment = $QShipment->fetchAll();

    $QGood = new Application_Model_Good();
    $dataGood = $QGood->fetchAll();

    $this->view->dataShipment = $dataShipment;
    $this->view->dataGood = $dataGood;
    $this->view->data = $data;
}


public function saveShipmentPhoneAction(){

    $id = $this->getRequest()->getParam('id');
    $id_shipment = $this->getRequest()->getParam('id_shipment');
    $id_good = $this->getRequest()->getParam('id_good');
    $price = $this->getRequest()->getParam('price');
    $type  = $this->getRequest()->getParam('type');
    $data = array(
        'good_shipment_id' => $id_shipment,
        'good_id'          => $id_good,
        'price'            => $price,
        'type'             => intval($type)
    );

    $QShipmentPhone = new Application_Model_GoodShipmentPhone();
    if($id){
        $where = $QShipmentPhone->getAdapter()->quoteInto('id = ?', $id);
        $QShipmentPhone->update($data,$where);
        $this->_redirect(HOST . 'sales/create-shipment-phone?id='.$id);
    }
    else{
        $QShipmentPhone->insert($data);
        $this->_redirect(HOST . 'sales/shipment-details?id='.$id_shipment);
    }

}

public function shipmentDetailsAction(){
    $id = $this->getRequest()->getParam('id');
    $QShipment = new Application_Model_GoodShipment();
    $where = $QShipment->getAdapter()->quoteInto('id = ?', $id);
    $data = $QShipment->fetchRow($where);

    $QShipmentPhone = new Application_Model_GoodShipmentPhone();
    $dataPhone = $QShipmentPhone->getShipmentPhone($id);

    $QGood = new Application_Model_Good();

    foreach($dataPhone as $key=>$value){
        $whereGood[] = $QGood->getAdapter()->quoteInto("id NOT IN (?)", (int)$value['id_good']);
    }

    if($dataPhone){
        $dataGood = $QGood->fetchAll($whereGood);
    }
    else{
        $dataGood = $QGood->fetchAll();
    }

    $this->view->dataGood = $dataGood;

    $QShipmentAll = new Application_Model_GoodShipment();
    $dataShipmentAll = $QShipmentAll->fetchAll();

    $QGoodCategory = new Application_Model_GoodCategory();
    $this->view->good_categories = $QGoodCategory->fetchAll();

    $this->view->data = $data;
    $this->view->dataPhone = $dataPhone;
    $this->view->dataShipment = $dataShipmentAll;
}

public function checkPriceShipmentAction(){
    $shipment_id = $this->getRequest()->getParam('shipment');
    $good_id     = $this->getRequest()->getParam('good_id');
    $price       = $this->getRequest()->getParam('price');

    if ($shipment_id) {
        $QGoodShipmentPhone = new Application_Model_GoodShipmentPhone();
        $where_shipment[] = $QGoodShipmentPhone->getAdapter()->quoteInto('good_id = ?', $good_id);
        $where_shipment[] = $QGoodShipmentPhone->getAdapter()->quoteInto('good_shipment_id = ?', $shipment_id);
        $where_shipment[] = $QGoodShipmentPhone->getAdapter()->quoteInto('price = ?', $price);
        $data = $QGoodShipmentPhone->fetchRow($where_shipment);

        if($data){
            echo "true";
            exit;
        }
        else{
            echo "false";
            exit;
        }

    }
    exit;
}

//Tanong Get SalesOrderNo 20160313 1155
public function getSalesOrderNo()
{
    $flashMessenger = $this->_helper->flashMessenger;    
    $sales_order_sn='';
    try {
        $db = Zend_Registry::get('db');
        $stmt = $db->prepare("CALL get_sales_order_sn()");
        $stmt->execute();
        $data = $stmt->fetchAll();
        $sales_order_sn= $data[0]['sales_order_sn'];
    }catch (exception $e) {
        $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
    }
    return $sales_order_sn;
}

//Tanong Get SalesOrderNoRef 20160313 1155
public function getSalesOrderNo_Ref($sn)
{
    $flashMessenger = $this->_helper->flashMessenger;
    $sn_ref="";
    try {
/*
$db = Zend_Registry::get('db');
$stmt = $db->prepare("CALL gen_running_no_ref('SO',".$sn.")");
$stmt->execute();
$result = $stmt->fetch();
$sn_ref = $result['running_no'];
*/

}catch (exception $e) {
    $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
}
return $sn_ref;
}

//Tanong Get SalesOrderNoRef 20160313 1155
public function getReturnOrderNo_Ref($sn)
{
    $flashMessenger = $this->_helper->flashMessenger;
    $sn_ref="";
    try {

        $db = Zend_Registry::get('db');
        $stmt = $db->prepare("CALL gen_running_no_ref('RO',".$sn.")");
//$stmt = $db->prepare("CALL gen_running_no_ref('SO',201603121740314924)");
        $stmt->execute();
        $result = $stmt->fetch();
        $sn_ref = $result['running_no'];
    }catch (exception $e) {
        $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
    }
    return $sn_ref;
}

//Tanong For Credit Note 20160311 1155
public function saveAPICreditNoteAction($db,$distributor_id,$sales_order,$creditnote_data)
{
    $flashMessenger = $this->_helper->flashMessenger;
    $status_sn='';
//return;
//print_r($creditnote_data);die;
    try {
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $db = Zend_Registry::get('db');
        $item_row=0;
        if($creditnote_data=='no_discount')
        {
            $item_row=0;
        }else{
            $item_row = count($creditnote_data['ids_discount_creditnote']);
        }
        $creditnote_sn='';
//echo $item_row;die;
        if($item_row>0)
        {
            $CreditNote_new = array();$CreditNote_old = array();
            for($i=0;$i<$item_row;$i++){
                $creditnote_sn=$creditnote_data['ids_discount_creditnote'][$i];
                $CreditNote_new[] = $creditnote_sn;
            }

            $QCreditNote = new Application_Model_CreditNote();
            $CreditNote = $QCreditNote->getCredit_Note_By_SalesOrder($sales_order,$distributor_id);
            foreach ($CreditNote as $cn){
                $creditnote_sn = $cn['creditnote_sn'];
                $CreditNote_old[] = $creditnote_sn;
            }

            $removed_ids = array_diff($CreditNote_old, $CreditNote_new);
            if ($removed_ids)
            {
                $QCreditNoteTran = new Application_Model_CreditNoteTran();
                $where   = array();
                $where[] = $QCreditNoteTran->getAdapter()->quoteInto('sales_order =?', $sales_order);
                $where[] = $QCreditNoteTran->getAdapter()->quoteInto('distributor_id =?', $distributor_id);
                $where[] = $QCreditNoteTran->getAdapter()->quoteInto('creditnote_sn =?', $removed_ids);
                $QCreditNoteTran->delete($where);
            }

            for($i=0;$i<$item_row;$i++){
                $creditnote_sn=$creditnote_data['ids_discount_creditnote'][$i];
                $use_total=$this->decimal_remove_comma($creditnote_data['price_use_discount_creditnote'][$i]);
                $balance_total=$creditnote_data['price_balance_discount_creditnote'][$i];
                $sales_order=$creditnote_data['sales_order'];
                $user_id=$userStorage->id;
                $distributor_id=$creditnote_data['distributor_id'];

        //$stmt = $db->prepare("CALL update_credit_note_sn('1105','CP590325-00001',100,'201603252045563500','10',0,0)");
        /*
        $stmt = $db->prepare("CALL update_credit_note_sn('".$distributor_id."','".$creditnote_sn."',".$use_total.",'".$sales_order."','".$user_id."',0,0)");          
        $stmt->execute();
        */
        $db->query("CALL update_credit_note_sn('".$distributor_id."','".$creditnote_sn."',".$use_total.",'".$sales_order."','".$user_id."',0,0)");          
        //$stmt->execute();
        
    }
}else{
    $user_id=$userStorage->id;   
    $db->query("CALL update_credit_note_sn('".$distributor_id."','no_discount',0,'".$sales_order."','".$user_id."',0,0)");
    //$stmt->execute();
    
    //$stmt = $db->prepare("CALL update_credit_note_sn('1105','CP590325-00001',100,'201603252045563500','10',0,0)");
    /* 
    $stmt = $db->prepare("CALL update_credit_note_sn('".$distributor_id."','no_discount',0,'".$sales_order."','10',0,0)");
    $stmt->execute();
    */
}
}catch (exception $e) {
// $flashMessenger->setNamespace('error')->addMessage('Cannot Use Credit Note, please try again!');
    $flashMessenger->setNamespace('error')->addMessage($e.messages);
}
return $status_sn;
}

//Tanong For Deposit
public function saveAPIDepositAction($db,$distributor_id,$sales_order,$deposit_data)
{
    $flashMessenger = $this->_helper->flashMessenger;
    $status_sn='';
//return;
//print_r($creditnote_data);die;
    try {
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $db = Zend_Registry::get('db');
        $item_row=0;
        if($deposit_data=='no_discount')
        {
            $item_row=0;
        }else{
            $item_row = count($deposit_data['ids_discount_deposit']);
        }
        $deposit_sn='';
        if($item_row>0)
        {
            $deposit_new = array();$deposit_old = array();
            for($i=0;$i<$item_row;$i++){
                $deposit_sn=$deposit_data['ids_discount_deposit'][$i];
                $deposit_new[] = $deposit_sn;
            }

            $QDepositTran = new Application_Model_DepositTran();
            $QDepositTran = $QDepositTran->getDeposit_By_SalesOrder($sales_order,$distributor_id);
            foreach ($QDepositTran as $cn){
                $deposit_sn = $cn['deposit_sn'];
                $deposit_old[] = $deposit_sn;
            }

            $removed_ids = array_diff($deposit_old, $deposit_new);
            if ($removed_ids)
            {
                $where   = array();
                $where[] = $QDepositTran->getAdapter()->quoteInto('sales_order =?', $sales_order);
                $where[] = $QDepositTran->getAdapter()->quoteInto('distributor_id =?', $distributor_id);
                $where[] = $QDepositTran->getAdapter()->quoteInto('deposit_sn =?', $removed_ids);
                $QDepositTran->delete($where);
            }

            for($i=0;$i<$item_row;$i++){
                $deposit_sn=$deposit_data['ids_discount_deposit'][$i];
                $use_total=$this->decimal_remove_comma($deposit_data['price_use_discount_deposit'][$i]);
                $balance_total=$deposit_data['price_balance_discount_deposit'][$i];
                $sales_order=$deposit_data['sales_order'];
                $user_id=$userStorage->id;
                $distributor_id=$deposit_data['distributor_id'];

                $db->query("CALL update_deposit_tran('".$distributor_id."','".$deposit_sn."',".$use_total.",'".$sales_order."','".$user_id."')");          

            }
        }else{
            $user_id=$userStorage->id;   
            $db->query("CALL update_deposit_tran('".$distributor_id."','no_discount',0,'".$sales_order."','".$user_id."')");
        }
    }catch (exception $e) {
        $flashMessenger->setNamespace('error')->addMessage('Cannot Uer Deposit No, please try again!');
    }
    return $status_sn;
}

function decimal_remove_comma($priceFloat)
{
    $price = str_replace(",","",$priceFloat);;
    return $price;
}


function cal_sale_off_percent($sale_off_percent,$price,$num,$price_total){
    if($sale_off_percent>0){
        $price_sale_off = $price - (($price*$sale_off_percent/100)*100)/100;
    }else{
        $price_sale_off = $price;
    }
    return $price_sale_off;
}

function ext_vat($num){
    return $num/1;
}
function format_number_4($num){
    return $this->decimal_remove_comma(number_format($num, 4));
}
function format_number_2($num){
    return $this->decimal_remove_comma(number_format($num, 2));
}

//Output VAT Report
private function _exportExcelOutputVat($sql) 
{
// this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Sell out List - OUTPUT VAT - '.date('d-m-Y H-i-s').'.csv';
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
    'INVOICE CREATE DATE',
    'INVOICE NUMBER',
    'COMPANY NAME',
    'MST NUMBER',
    'BRANCH TYPE',
    'BRANCH NUMBER',
    'TOTAL PRICE (EXCLUDE VAT)',
    'TOTAL VAT',
    'TOTAL PRICE (INCLUDE VAT)',
    'CANCEL OR NOT',
    'DELIVERY ADDRESS',
    'CUSTOMER BRANDSHOP',
    'SALES CATTY',
    'TAX PO',
    'Finance Group',
    'SALE ORDER NUMBER',
    'DISTRIBUTOR ID',
    'DISTRIBUTOR CODE',
    'DISTRIBUTOR NAME',
    'PROVINCE',
    'OPERATION CAMPAIGN',
    'PHONE NUMBER'
);

fputcsv($output, $heads);

$QDistributor   = new Application_Model_Distributor();
$distributor_cache = $QDistributor->get_cache2();
$result = $db->query($sql);
$i = 2;

foreach($result as $item) {

    $where_payment = $QDistributor->getAdapter()->quoteInto('id = ?', $item['d_id']);
    $distributors_payment = $QDistributor->fetchRow($where_payment);
    $rank = $distributors_payment->rank;

    $product_qty        = $item['num'];
    if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = '????????????'; }else { $branch_type = '????'; }
    if ($item['canceled']== 1){$cancel = 'Canceled';}else{$cancel = 'Not Cancel';}
    $row = array();
    $row[] = $item['invoice_time'];
    $row[] = $item['invoice_number'];
    $row[] = $distributor_cache[$item['d_id']]['unames'];
    $row[] = '="'.$distributor_cache[$item['d_id']]['mst_sn'].'"';
    $row[] = $branch_type;
    $row[] = '="'.$distributor_cache[$item['d_id']]['branch_no'].'"';

    if($item['canceled']==1){
        $row[]=0;
        $row[]=0;
        $row[]=0;
    }else{

        $sum_total_discount = 0;
        date_default_timezone_set('Asia/Bangkok');
        $date = new DateTime('2017-01-04 00:00:00');
        $date_start= date_format($date,"Y-m-d H:i:s");

        $date_outvat = new DateTime('2017-09-06 00:00:00');
        $date_outvat_start= date_format($date_outvat,"Y-m-d H:i:s");

        $date_order = $item['add_time'];
        if($date_order < $date_start){
            if ($item['d_id']== '3691')
            {
                if ($item['cat_id']==11)
                {
                 $total_discount = 1;
                 $sum_total_discount = ($item['sum_total']*$total_discount)/100;
             }else{
                 $sum_total_discount = 0;
             }                    

             $sum_total = $item['sum_total']-$sum_total_discount;
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( ($sum_total * 1) - $sum_total, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total * 1, 2);  // INCLUDE VAT
        }else if ($item['d_id']== '3025' ){
            $total_discount = 1;
            $sum_total_discount = ($item['sum_total']*$total_discount)/100;                  
            
            $sum_total = $item['sum_total']-$sum_total_discount;
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( ($sum_total * 1) - $sum_total, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total * 1, 2);  // INCLUDE VAT    
        }else if ($rank== '9') {
            $sum_total = $item['sum_total'];
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( 0, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total, 2);  // INCLUDE VAT
        }else{
            $sum_total = $item['sum_total'];
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( ($sum_total * 1) - $sum_total, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total * 1, 2);  // INCLUDE VAT  
        }
    }else{

        if ($rank== '9') {  //ZT MOBILE (GONGPALAN)
            $sum_total = $item['sum_total'];
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( 0, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total, 2);  // INCLUDE VAT
        }else{
            $sum_total = $item['sum_total'];
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( ($sum_total * 1) - $sum_total, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total * 1, 2);  // INCLUDE VAT  
        }



    }
    
}

$row[] = $cancel;
$row[] = $item['delivery_address'];
$row[] = $item['customer_name'];
$row[] = $item['sales_catty_name'];
$row[] = $item['tax_po'];
$row[] = $item['finance_group'];

if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
else { $temp_sn = $item['sn_ref']; }

$row[] = $temp_sn;
$row[] = $distributor_cache[$item['d_id']]['d_id'];
$row[] = $distributor_cache[$item['d_id']]['code'];
$row[] = $distributor_cache[$item['d_id']]['title'];
$row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
$row[] = $item['bs_campaign'];
$row[] = $item['phone_number_sn'];

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

//Output VAT Report New
private function _exportExcelOutputVatNew($sql) 
{
// this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Sell out List - OUTPUT VAT New - '.date('d-m-Y H-i-s').'.csv';
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
    'INVOICE CREATE DATE',
    'INVOICE NUMBER',
    'COMPANY NAME',
    'MST NUMBER',
    'BRANCH TYPE',
    'BRANCH NUMBER',
    'TOTAL PRICE (EXCLUDE VAT)',
    'TOTAL VAT',
    'TOTAL PRICE (INCLUDE VAT)',
    'CANCEL OR NOT',
    'DELIVERY ADDRESS',
    'CUSTOMER BRANDSHOP',
    'SALES CATTY',
    'TAX PO',
    'Finance Group',
    'SALE ORDER NUMBER',
    'DISTRIBUTOR ID',
    'DISTRIBUTOR CODE',
    'DISTRIBUTOR NAME',
    'PROVINCE',
    'OPERATION CAMPAIGN',
    'PHONE NUMBER'
);

fputcsv($output, $heads);

$QDistributor   = new Application_Model_Distributor();
$distributor_cache = $QDistributor->get_cache2();
$result = $db->query($sql);
$i = 2;

foreach($result as $item) {

    $where_payment = $QDistributor->getAdapter()->quoteInto('id = ?', $item['d_id']);
    $distributors_payment = $QDistributor->fetchRow($where_payment);
    $rank = $distributors_payment->rank;

    $product_qty        = $item['num'];
    if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = '????????????'; }else { $branch_type = '????'; }
    if ($item['canceled']== 1){$cancel = 'Canceled';}else{$cancel = 'Not Cancel';}
    $row = array();
    $row[] = $item['invoice_time'];
    $row[] = $item['invoice_number'];

    if(isset($item['customer_name']) && $item['customer_name']){
        $row[] = $item['customer_name'];
    }else{
        $row[] = $distributor_cache[$item['d_id']]['unames'];
    }

    if(isset($item['cus_tax_number']) && $item['cus_tax_number']){
        $row[] = '="'.$item['cus_tax_number'].'"';
    }else{
        $row[] = '="'.$distributor_cache[$item['d_id']]['mst_sn'].'"';
    }

    $row[] = $branch_type;
    $row[] = '="'.$distributor_cache[$item['d_id']]['branch_no'].'"';

    if($item['canceled']==1){
        $row[]=0;
        $row[]=0;
        $row[]=0;
    }else{

        $sum_total_discount = 0;
        date_default_timezone_set('Asia/Bangkok');
        $date = new DateTime('2017-01-04 00:00:00');
        $date_start= date_format($date,"Y-m-d H:i:s");

        $date_outvat = new DateTime('2017-09-06 00:00:00');
        $date_outvat_start= date_format($date_outvat,"Y-m-d H:i:s");

        $date_order = $item['add_time'];
        if($date_order < $date_start){
            if ($item['d_id']== '3691')
            {
                if ($item['cat_id']==11)
                {
                 $total_discount = 1;
                 $sum_total_discount = ($item['sum_total']*$total_discount)/100;
             }else{
                 $sum_total_discount = 0;
             }                    

             $sum_total = $item['sum_total']-$sum_total_discount;
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( ($sum_total * 1) - $sum_total, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total * 1, 2);  // INCLUDE VAT
        }else if ($item['d_id']== '3025' ){
            $total_discount = 1;
            $sum_total_discount = ($item['sum_total']*$total_discount)/100;                  
            
            $sum_total = $item['sum_total']-$sum_total_discount;
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( ($sum_total * 1) - $sum_total, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total * 1, 2);  // INCLUDE VAT    
        }else if ($rank== '9') {
            $sum_total = $item['sum_total'];
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( 0, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total, 2);  // INCLUDE VAT
        }else{
            $sum_total = $item['sum_total'];
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( ($sum_total * 1) - $sum_total, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total * 1, 2);  // INCLUDE VAT  
        }
    }else{

        if ($rank== '9') {  //ZT MOBILE (GONGPALAN)
            $sum_total = $item['sum_total'];
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( 0, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total, 2);  // INCLUDE VAT
        }else{
            $sum_total = $item['sum_total'];
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( ($sum_total * 1) - $sum_total, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total * 1, 2);  // INCLUDE VAT  
        }



    }
    
}

$row[] = $cancel;
$row[] = $item['delivery_address'];
$row[] = $item['customer_name'];
$row[] = $item['sales_catty_name'];
$row[] = $item['tax_po'];
$row[] = $item['finance_group'];

if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
else { $temp_sn = $item['sn_ref']; }

$row[] = $temp_sn;
$row[] = $distributor_cache[$item['d_id']]['d_id'];
$row[] = $distributor_cache[$item['d_id']]['code'];
$row[] = $distributor_cache[$item['d_id']]['title'];
$row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
$row[] = $item['bs_campaign'];
$row[] = $item['phone_number_sn'];

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

//Cash Collection
private function _exportExcelCashCollection($sql) 
{

// this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
//print_r($sql);die;
    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Sell out List - Cash Collection - '.date('d-m-Y H-i-s').'.csv';
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
    'INVOICE CREATE DATE',
    'INVOICE NUMBER',
    'COMPANY NAME',
    'MST NUMBER',
    'BRANCH TYPE',
    'BRANCH NUMBER',
    'TOTAL PRICE (EXCLUDE VAT)',
    'TOTAL VAT',
    'TOTAL PRICE (INCLUDE VAT)',
    'CANCEL OR NOT',
    'DELIVERY ADDRESS',
    'CUSTOMER BRANDSHOP',
    'SALES CATTY',
    'TAX PO',
    'Finance Group'
);

fputcsv($output, $heads);

$QDistributor   = new Application_Model_Distributor();
$distributor_cache = $QDistributor->get_cache2();
$result = $db->query($sql);
$i = 2;

foreach($result as $item) {
    $product_qty        = $item['num'];
    if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = '????????????'; }else { $branch_type = '????'; }
    if ($item['canceled']== 1){$cancel = 'Canceled';}else{$cancel = 'Not Cancel';}
    $row = array();
    $row[] = $item['invoice_time'];
    $row[] = $item['invoice_number'];
    $row[] = $distributor_cache[$item['d_id']]['unames'];
    $row[] = '="'.$distributor_cache[$item['d_id']]['mst_sn'].'"';
    $row[] = $branch_type;
    $row[] = '="'.$distributor_cache[$item['d_id']]['branch_no'].'"';


    if($item['canceled']==1){
        $row[]=0;
        $row[]=0;
        $row[]=0;
    }else{

        $sum_total_discount = 0;
        date_default_timezone_set('Asia/Bangkok');
        $date = new DateTime('2017-01-04 00:00:00');
        $date_start= date_format($date,"Y-m-d H:i:s");
        $date_order = $item['add_time'];
        if($date_order < $date_start){
        if ($item['d_id']== '3691') // ??????
        {
            if ($item['cat_id']==11)
            {
             $total_discount = 1;
             $sum_total_discount = ($item['sum_total']*$total_discount)/100;
         }else{
             $sum_total_discount = 0;
         }                    

         $sum_total = $item['sum_total']-$sum_total_discount;
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( ($sum_total * 1) - $sum_total, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total * 1, 2);  // INCLUDE VAT
        }else if ($item['d_id']== '3025' ) // Com 7
        {
            $total_discount = 1;
            $sum_total_discount = ($item['sum_total']*$total_discount)/100;                  
            
            $sum_total = $item['sum_total']-$sum_total_discount;
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( ($sum_total * 1) - $sum_total, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total * 1, 2);  // INCLUDE VAT    
        }else if ($item['d_id']== '21088') {
            $sum_total = $item['sum_total'];
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( 0, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total, 2);  // INCLUDE VAT
        }else{
            $sum_total = $item['sum_total'];
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( ($sum_total * 1) - $sum_total, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total * 1, 2);  // INCLUDE VAT  
        }
    }else{
        $sum_total = $item['sum_total'];
            $row[] = number_format( $sum_total, 2);//EXCLUDE VAT
            $row[] = number_format( ($sum_total * 1) - $sum_total, 2 ); // TOTAL VAT
            $row[] = number_format( $sum_total * 1, 2);  // INCLUDE VAT  
        }


    }

    $row[] = $cancel;
    $row[] = $item['delivery_address'];
    $row[] = $item['customer_name'];
    $row[] = $item['sales_catty_name'];
    $row[] = $item['tax_po'];
    $row[] = $item['finance_group'];
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

private function _exportExcelOrderStatus($sql) {

// this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Order Status - '.date('d-m-Y H-i-s').'.csv';
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
    'SALE ORDER NUMBER',
    'INVOICE NUMBER',
    'DISTRIBUTOR ID',
    'DISTRIBUTOR NAME',
    'AREA',
    'PROVINCE',
    'DISTRICT',
    'TOTAL',
    'PAID DATETIME',
    'SHIPPING DATETIME',
    'STOCKOUT DATETIME',
    'MONEYCHECK DATETIME',
    'OPERATION CAMPAIGN'
);

fputcsv($output, $heads);

$QDistributor   = new Application_Model_Distributor();
$distributor_cache = $QDistributor->get_cache2();

$result = $db->query($sql);
//print_r($result);die;
$i = 2;

foreach($result as $item) {

    if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
    else { $temp_sn = $item['sn_ref']; }

    $row = array();
    $row[] = '="'.$temp_sn.'"';
    $row[] = $item['invoice_number'];
    $row[] = $distributor_cache[$item['d_id']]['code'];
    $row[] = $distributor_cache[$item['d_id']]['title'];
    $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area);
    $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
    $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::District);
    $row[] = number_format($item['sum_total'], 2);
    $row[] = $item['pay_time'];
    $row[] = $item['shipping_yes_time'];
    $row[] = $item['outmysql_time'];
    $row[] = $item['checkmoney_create_at'];
    $row[] = $item['bs_campaign'];
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

private function _exportExcelByProvince($sql) {

// this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Sell out By Province - '.date('d-m-Y H-i-s').'.csv';
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
/*
'SALE ORDER NUMBER',
'INVOICE NUMBER',
'RETAILER ID',
'RETAILER NAME',
'Company NAME',
'MST NAME',
'BRANCH TYPE',
'BRANCH NUMBER',*/
'AREA',
'PROVINCE',
'DISTRICT',
'PRODUCT TYPE',
'PRODUCT NAME',
'PRODUCT COLOR',
'SALES QUANTITY',
'SET OFF (%)',
'UNIT PRICE EX',
'TOTAL EX',
'DELIVERY FEE',
'UNIT PRICE',
'TOTAL',
/*
'PAID DATETIME',
'SHIPPING DATETIME',
'WAREHOUSE',
'STOCKOUT DATETIME',
'STATUS',
'SALE ORDER TIME',
'CUSTOMER ORDER TYPE',
'ORDER DESCRIPTION'*/
);

fputcsv($output, $heads);

$QGood          = new Application_Model_Good();
$QGoodColor     = new Application_Model_GoodColor();
$QGoodCategory  = new Application_Model_GoodCategory();
$QDistributor   = new Application_Model_Distributor();
$QWarehouse     = new Application_Model_Warehouse();

$goods             = $QGood->get_cache();
$goodColors        = $QGoodColor->get_cache();
$good_categories   = $QGoodCategory->get_cache();
$distributor_cache = $QDistributor->get_cache2();
$warehouses        = $QWarehouse->get_cache();

$result = $db->query($sql);
//print_r($result);die;
$i = 2;

foreach($result as $item) {

    if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
    else { $temp_sn = $item['sn_ref']; }

    if ($item['status'] == 1) { $temp_status = 'Actived'; }
    else if ($item['status'] == 2) { $temp_status = 'Expired'; }
    else { $temp_status = 'Expired'; }

if ($item['type'] == 1) //for retailer
$type = 'For Retailer';
elseif ($item['type'] == 2) //for APK
$type = 'For APK';
elseif ($item['type'] == 3) //for staffs
$type = 'For Staffs';
elseif ($item['type'] == 4) //for lending
$type = 'For Lending';
elseif ($item['type'] == 5) //for DEMO
$type = 'For DEMO';    

if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = '????????????'; }
else { $branch_type = '????'; }

$row = array();
/*
$row[] = '="'.$temp_sn.'"';
$row[] = $item['invoice_number'];
$row[] = $distributor_cache[$item['d_id']]['code'];
$row[] = $distributor_cache[$item['d_id']]['title'];
$row[] = $distributor_cache[$item['d_id']]['unames'];
$row[] = '="'.$distributor_cache[$item['d_id']]['mst_sn'].'"';
$row[] = $branch_type;
$row[] = '="'.$distributor_cache[$item['d_id']]['branch_no'].'"';*/
$row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area);
$row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
$row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::District);
$row[] = $good_categories[$item['cat_id']];
$row[] = $goods[$item['good_id']];
$row[] = $goodColors[$item['good_color']];
$row[] = $item['sum_num'];

$row[] = $item['sale_off_percent'];
$row[] = number_format($item['sum_price'] / 1, 2);
$row[] = number_format($item['sum_total'] / 1, 2);
$row[] = !is_null($item['delivery_fee']) ? $item['delivery_fee'] : 0;

$row[] = number_format($item['sum_price'], 2);
$row[] = number_format($item['sum_total'], 2);
/*
$row[] = $item['pay_time'];
$row[] = $item['shipping_yes_time'];
$row[] = $warehouses[$item['warehouse_id']];
$row[] = $item['outmysql_time'];
$row[] = $temp_status;
$row[] = $item['add_time'];
$row[] = $type;
$row[] = $item['text'];*/

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
//Sale Master Data

private function _exportExcelBydistributor($sql) {

// this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Export_By_Distributor_ '.date('d-m-Y H-i-s').'.csv';
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

echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
$output = fopen('php://output', 'w');

//$path = $file_path.'/'.$filename;
//$output = fopen($path, 'w+');
//echo "\xEF\xBB\xBF"; // UTF-8 BOM

$heads = array(
    'SALE ORDER NUMBER',
    'INVOICE NUMBER',
    // 'DISTRIBUTOR STATUS',
    'Store OR Wareouse Code',
    'Store OR Warehouse Name',
    'Distributor Code',
    'Distributor Name',
    // 'DISTRIBUTOR TYPE GROUP',
    // 'GRAND AREA',
    'AREA',
    'PROVINCE',
    'DISTRICT',
    'QUANTITY',
    'TOTAL',
    'SALES CONFIRM',
    'PAID DATETIME',
    'SHIPPING DATETIME',
    'STOCKOUT DATETIME',
    // 'FINANCE GROUP',
    // 'OPERATION CAMPAIGN'
);

fputcsv($output, $heads);

$QDistributor   = new Application_Model_Distributor();
$distributor_cache = $QDistributor->get_cache2();

$QWarehouse   = new Application_Model_Warehouse();
$warehouses = $QWarehouse->get_cache();

$result = $db->query($sql);
//print_r($result);die;
$i = 2;

// $grand_e1 = array(73,81,82,83,84);
// $grand_e2 = array(74,80,85,86,87,88,89);
// $grand_e3 = array(77,90,91,92,93);
// $grand_e4 = array(79,94,95,96,97,109);
// $grand_w1 = array(75,98,99,100,101,102);
// $grand_w2 = array(76,78,103,104,105,106,107,108);

// $grand_e1 = array(81,82,83);
// $grand_e2 = array(85,86,87,115);
// $grand_e3 = array(90,91,92,93,113);
// $grand_e4 = array(94,95,96);
// $grand_e5 = array(88,89,117);
// $grand_e6 = array(110,111,112);
// $grand_e7 = array(97,109);
// $grand_w1 = array(98,99,100,101,102,114);
// $grand_w2 = array(103,104,105,116);
// $grand_w3 = array(106,107,108);

$grand_e1 = array(81,82,83,110,111,112);
$grand_e2 = array(85,86,87,115,88,89,116,117);
$grand_e3 = array(90,91,92,93,113);
$grand_e4 = array(94,95,96);
$grand_e5 = array(97,109);
$grand_w1 = array(98,99,100,101,102,114);
$grand_w2 = array(103,104,105);
$grand_w3 = array(106,107,108);

foreach($result as $item) {

    if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
    else { $temp_sn = $item['sn_ref']; }

    $excel_area_name = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area);

    $excel_area_id = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area, My_Region::ID);

    if ( in_array($excel_area_id, $grand_e1) ) { $grand_area = 'BKK East-1'; } 
    else if ( in_array($excel_area_id, $grand_e2) ) { $grand_area = 'BKK East-2'; }
    else if ( in_array($excel_area_id, $grand_e3) ) { $grand_area = 'BKK East-3'; }
    else if ( in_array($excel_area_id, $grand_e4) ) { $grand_area = 'BKK East-4'; }
    else if ( in_array($excel_area_id, $grand_e5) ) { $grand_area = 'BKK East-5'; }
    else if ( in_array($excel_area_id, $grand_w1) ) { $grand_area = 'BKK West-1'; }
    else if ( in_array($excel_area_id, $grand_w2) ) { $grand_area = 'BKK West-2'; }
    else if ( in_array($excel_area_id, $grand_w3) ) { $grand_area = 'BKK West-3'; }
    else { $grand_area = $excel_area_name; }

    $row = array();
    $row[] = '="'.$temp_sn.'"';
    $row[] = $item['invoice_number'];

    $dis_status = 'Active';
    if(isset($item['del']) && $item['del'] == '1'){
        $dis_status = 'Deleted';
    }

    // $row[] = $dis_status;

    
    // Start 
    $where = $QDistributor->getAdapter()->quoteInto('id =?',$item['d_id']);
    $distributor_arr = $QDistributor->fetchRow($where);
    
    if($distributor_arr['agent_status'] == 1){

        $where = $QWarehouse->getAdapter()->quoteInto('id =?',$distributor_arr['agent_warehouse_id']);
        $warehouse_arr = $QWarehouse->fetchRow($where);
        
        $row[] = $warehouse_arr['code'];
        $row[] = $warehouse_arr['name'];
        
        }else{
        
        $row[] = $item['store_code'];
        $row[] = $item['store_name'];
        
        }
        // End

    $row[] = $distributor_cache[$item['d_id']]['d_code'];
    $row[] = $distributor_cache[$item['d_id']]['title'];

    $DTG = '';
    switch ($item['group_type_id']) {
        case '10':
        $DTG = 'Brand Shop';
        break;
        case '11':
        $DTG = 'Brand Shop By Dealer';
        break;
        case '12':
        $DTG = 'Brand Shop-ORG';
        break;
        case '13':
        $DTG = 'Brand Shop by KR Dealer';
        break;
        case '1':
        $DTG = 'Dealer and Hub';
        break;
        case '8':
        $DTG = 'Digital';
        break;
        case '7':
        $DTG = 'Export';
        break;
        case '3':
        $DTG = 'KA(ORG)';
        break;
        case '2':
        $DTG = 'KR-Dealer';
        break;
        case '5':
        $DTG = 'Online';
        break;
        case '4':
        $DTG = 'Operator';
        break;
        case '9':
        $DTG = 'Service Shop';
        break;
        case '6':
        $DTG = 'Staff';
        break;
    }

    // $row[] = $DTG;

    // $row[] = $grand_area;
    $row[] = $excel_area_name;
    $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
    $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::District);
    $row[] = number_format($item['sum_num'], 2);
    $row[] = number_format($item['sum_total'], 2);
    $row[] = $item['sales_confirm_date'];
    $row[] = $item['pay_time'];
    $row[] = $item['shipping_yes_time'];
    $row[] = $item['outmysql_time'];
    // $row[] = $item['finance_group'];
    // $row[] = $item['bs_campaign'];
// $channel_type = '';
// switch (isset($item['quota_channel'])) {
//     case '10':
//         $channel_type = 'Brand Shop By Dealer';
//         break;
//     case '1':
//         $channel_type = 'ORG By Dealer';
//         break;
// }
// $row[] = $channel_type;

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

private function _exportExcelOutputVatImei($sql, $params) {
//echo $sql;die;
// this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Sale Master Data List - '.date('d-m-Y H-i-s').'.csv';
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

$heads = null;
if (isset($params['order_packed_sim']) and $params['order_packed_sim']=="1")
{
    $heads = array(
        'SALE ORDER NUMBER',
        'INVOICE NUMBER',
        'DISTRIBUTOR ID',
        'DISTRIBUTOR CODE',
        'DISTRIBUTOR NAME',
        'Company NAME',
        'MST NAME',
        'BRANCH TYPE',
        'BRANCH NUMBER',
        'AREA',
        'PROVINCE',
        'DISTRICT',
        'PRODUCT TYPE',
        'PRODUCT NAME',
        'PRODUCT COLOR',
        'SALES QUANTITY',
        'IMEI',
        'SET OFF (%)',
        'UNIT PRICE EX',
        'TOTAL EX',
        'DELIVERY FEE',
        'UNIT PRICE',
        'TOTAL',
        'PAID DATETIME',
        'SHIPPING DATETIME',
        'WAREHOUSE',
        'STOCKOUT DATETIME',
        'STATUS',
        'SALE ORDER TIME',
        'CUSTOMER ORDER TYPE',
        'ORDER DESCRIPTION',
        'OPERATION CAMPAIGN',
        'CATTY ID',
        'CANCELED',
        'ACTIVATED STATUS',
        'ACTIVATED DATE',
        'PACKED SIM IMEI',
        'SIM Serial',
        'Phone No',
        'SIM Activate Date',
        'Operator',
        'Sales Out Date',
        'Confirm Rebate Date'
        
    );
}else{
    $heads = array(
        'SALE ORDER NUMBER',
        'INVOICE NUMBER',
        'DISTRIBUTOR ID',
        'DISTRIBUTOR CODE',
        'DISTRIBUTOR NAME',
        'Company NAME',
        'MST NAME',
        'BRANCH TYPE',
        'BRANCH NUMBER',
        'AREA',
        'PROVINCE',
        'DISTRICT',
        'PRODUCT TYPE',
        'PRODUCT NAME',
        'PRODUCT COLOR',
        'SALES QUANTITY',
        'IMEI',
        'SET OFF (%)',
        'UNIT PRICE EX',
        'TOTAL EX',
        'DELIVERY FEE',
        'UNIT PRICE',
        'TOTAL',
        'PAID DATETIME',
        'SHIPPING DATETIME',
        'WAREHOUSE',
        'STOCKOUT DATETIME',
        'STATUS',
        'SALE ORDER TIME',
        'CUSTOMER ORDER TYPE',
        'ORDER DESCRIPTION',
        'OPERATION CAMPAIGN',
        'CATTY ID',
        'CANCELED',
        'ACTIVATED STATUS',
        'ACTIVATED DATE'
        
    );
}

fputcsv($output, $heads);

$QGood          = new Application_Model_Good();
$QGoodColor     = new Application_Model_GoodColor();
$QGoodCategory  = new Application_Model_GoodCategory();
$QDistributor   = new Application_Model_Distributor();
$QWarehouse     = new Application_Model_Warehouse();

$goods             = $QGood->get_cache();
$goodColors        = $QGoodColor->get_cache();
$good_categories   = $QGoodCategory->get_cache();
$distributor_cache = $QDistributor->get_cache2();
$warehouses        = $QWarehouse->get_cache();

$result = $db->query($sql);
//print_r($result);die;
$i = 2;

foreach($result as $item) {

    if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
    else { $temp_sn = $item['sn_ref']; }

    if ($item['status'] == 1) { $temp_status = 'Actived'; }
    else if ($item['status'] == 2) { $temp_status = 'Expired'; }
    else { $temp_status = 'Expired'; }

if ($item['type'] == 1) //for retailer
$type = 'For Retailer';
elseif ($item['type'] == 2) //for APK
$type = 'For APK';
elseif ($item['type'] == 3) //for staffs
$type = 'For Staffs';
elseif ($item['type'] == 4) //for lending
$type = 'For Lending';
elseif ($item['type'] == 5) //for DEMO
$type = 'For DEMO';    

if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = '????????????'; }
else { $branch_type = '????'; }

$row = array();
$row[] = '="'.$temp_sn.'"';
$row[] = $item['invoice_number'];
$row[] = $item['d_id'];
$row[] = $distributor_cache[$item['d_id']]['code'];
$row[] = $distributor_cache[$item['d_id']]['title'];
$row[] = $distributor_cache[$item['d_id']]['unames'];
$row[] = '="'.$distributor_cache[$item['d_id']]['mst_sn'].'"';
$row[] = $branch_type;
$row[] = '="'.$distributor_cache[$item['d_id']]['branch_no'].'"';
$row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area);
$row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
$row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::District);
$row[] = $good_categories[$item['cat_id']];
$row[] = $goods[$item['good_id']];
$row[] = $goodColors[$item['good_color']];
//$row[] = $item['num'];
if ($item['cat_id'] == PHONE_CAT_ID) { $row[] = '1'; } 
else { $row[] = $item['num']; } 
$row[] = $item['imei_sn'];

$row[] = $item['sale_off_percent'];
$row[] = number_format($item['price'] / 1, 2);
$row[] = number_format($item['total'] / 1, 2);
$row[] = !is_null($item['delivery_fee']) ? $item['delivery_fee'] : 0;

$row[] = number_format($item['price'], 2);
$row[] = number_format($item['total'], 2);
$row[] = $item['pay_time'];
$row[] = $item['shipping_yes_time'];
$row[] = $warehouses[$item['warehouse_id']];
$row[] = $item['outmysql_time'];
$row[] = $temp_status;
$row[] = $item['add_time'];
$row[] = $type;
$row[] = $item['text'];
$row[] = $item['bs_campaign'];
$row[] = $item['sales_catty_id'];

$order_status = '';
if(isset($item['canceled']) && $item['canceled']){
    $order_status = 'YES';
}

$row[] = $order_status;

$activated_status = 'No Activate';
if(isset($item['activated_date']) && $item['activated_date']){
    $activated_status = 'Activated';
}

if ($item['cat_id'] == PHONE_CAT_ID && $item['imei_sn']) {
    $row[] = $activated_status;
    $row[] = $item['activated_date'];
}else{
    $row[] = '';
    $row[] = '';
}

if (isset($params['order_packed_sim']) and $params['order_packed_sim']=="1")
{
    if($item['simcard']!=""){
        $row[] = 'YES';
        $row[] = "'".$item['simcard'];
        $row[] = $item['tel_no'];
        $row[] = $item['sim_activated_updated_at'];
        $row[] = $item['confirm_rebate_date'];
        $row[] = $item['operator'];
        $row[] = $item['sellout_updated_at'];
    }
}

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

private function _exportExcelOutputVatImei2($sql, $params) {
//echo $sql;die;
// this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Sale Master Data Imei2 List - '.date('d-m-Y H-i-s').'.csv';
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

$heads = null;

$heads = array(
    'NO',
    'PO/NO',
    'SALE ORDER NUMBER',
    'INVOICE NUMBER',
    'Company NAME',
    'PRODUCT TYPE',
    'PRODUCT NAME',
    'PRODUCT COLOR',
    'SALES QUANTITY',
    'IMEI',
    'IMEI2',
    'CUSTOMER ORDER TYPE',
);

fputcsv($output, $heads);

$QGood          = new Application_Model_Good();
$QGoodColor     = new Application_Model_GoodColor();
$QGoodCategory  = new Application_Model_GoodCategory();
$QDistributor   = new Application_Model_Distributor();
$QWarehouse     = new Application_Model_Warehouse();

$goods             = $QGood->get_cache();
$goodColors        = $QGoodColor->get_cache();
$good_categories   = $QGoodCategory->get_cache();
$distributor_cache = $QDistributor->get_cache2();
$warehouses        = $QWarehouse->get_cache();

$result = $db->query($sql);
//print_r($result);die;
$i = 2;
$r=1;
foreach($result as $item) {

    if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
    else { $temp_sn = $item['sn_ref']; }

    if ($item['status'] == 1) { $temp_status = 'Actived'; }
    else if ($item['status'] == 2) { $temp_status = 'Expired'; }
    else { $temp_status = 'Expired'; }

if ($item['type'] == 1) //for retailer
$type = 'For Retailer';
elseif ($item['type'] == 2) //for APK
$type = 'For APK';
elseif ($item['type'] == 3) //for staffs
$type = 'For Staffs';
elseif ($item['type'] == 4) //for lending
$type = 'For Lending';
elseif ($item['type'] == 5) //for DEMO
$type = 'For DEMO';    

if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = '????????????'; }
else { $branch_type = '????'; }

$row = array();
if($item['cat_id']=="11")
{
    $row[] = $r;
    $row[] = $item['po_no'];
    $row[] = '="'.$temp_sn.'"';
    $row[] = $item['invoice_number'];
    $row[] = $distributor_cache[$item['d_id']]['unames'];
    $row[] = $good_categories[$item['cat_id']];
    $row[] = $goods[$item['good_id']];
    $row[] = $goodColors[$item['good_color']];
    //$row[] = $item['num'];
    if ($item['cat_id'] == PHONE_CAT_ID) { $row[] = '1'; } 
    else { $row[] = $item['num']; } 
    $row[] = $item['imei_sn'];
    $row[] = $item['imei2_sn'];
    $row[] = $type;
    
    fputcsv($output, $row);
    unset($item);
    unset($row);
    $r +=1;
}
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

private function _exportExcelOutputVatImeiReturn($sql, $params) {
        //echo $sql;die;
        // this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'Export_By_IMEI_ '.date('d-m-Y H-i-s').'.csv';
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

    echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');

        //$path = $file_path.'/'.$filename;
        //$output = fopen($path, 'w+');
        //echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = null;
        if (isset($params['order_packed_sim']) and $params['order_packed_sim']=="1")
        {
            $heads = array(
                'ID',
                'SALE ORDER NUMBER',
                'INVOICE NUMBER',
                'Store OR Warehouse Code',
                'Store OR Warehouse Name',
                'DISTRIBUTOR CODE',
                'DISTRIBUTOR NAME',
                // 'Company NAME',
                // 'MST NAME',
                // 'BRANCH TYPE',
                // 'BRANCH NUMBER',
                'AREA',
                'PROVINCE',
                // 'DISTRICT',
                'PRODUCT TYPE',
                'PRODUCT NAME',
                'PRODUCT COLOR',
                'QUANTITY',
                'IMEI',
                'IMEI Return',
                'IMEI Return Date',
                // 'Return CN',
                'SET OFF (%)',
                'UNIT PRICE EX',
                // 'TOTAL EX',
                // 'DELIVERY FEE',
                // 'UNIT PRICE',
                // 'TOTAL',
                // 'PAID DATETIME',
                // 'SHIPPING DATETIME',
                'WAREHOUSE',
                'STOCKOUT TIME',
                'STATUS',
                'SALE ORDER TIME',
                'CUSTOMER ORDER TYPE',
                // 'ORDER DESCRIPTION',
                // 'OPERATION CAMPAIGN',
                // 'CATTY ID',
                // 'CANCELED',
                'ACTIVATED STATUS',
                'ACTIVATED DATE',
                'PACKED SIM IMEI',
                'SIM Serial',
                'Phone No',
                'SIM Activate Date',
                'Operator',
                'Sales Out Date',
                'Confirm Rebate Date'
                
            );
        }else{
            $heads = array(
                'ID',
                'SALE ORDER NUMBER',
                'INVOICE NUMBER',
                'Store OR Warehouse Code',
                'Store OR Warehouse Name',
                'DISTRIBUTOR CODE',
                'DISTRIBUTOR NAME',
                // 'Company NAME',
                // 'MST NAME',
                // 'BRANCH TYPE',
                // 'BRANCH NUMBER',
                'AREA',
                'PROVINCE',
                // 'DISTRICT',
                'PRODUCT TYPE',
                'PRODUCT NAME',
                'PRODUCT COLOR',
                'QUANTITY',
                'IMEI',
                'IMEI Return',
                'IMEI Return Date',
                // 'Return CN',
                'SET OFF (%)',
                'UNIT PRICE EX',
                // 'TOTAL EX',
                // 'DELIVERY FEE',
                // 'UNIT PRICE',
                // 'TOTAL',
                // 'PAID DATETIME',
                // 'SHIPPING DATETIME',
                'WAREHOUSE',
                'STOCKOUT TIME',
                'STATUS',
                'SALE ORDER TIME',
                'CUSTOMER ORDER TYPE',
                // 'ORDER DESCRIPTION',
                // 'OPERATION CAMPAIGN',
                // 'CATTY ID',
                // 'CANCELED',
                'ACTIVATED STATUS',
                'ACTIVATED DATE'
                
            );
        }
        
        fputcsv($output, $heads);

        $QGood          = new Application_Model_Good();
        $QGoodColor     = new Application_Model_GoodColor();
        $QGoodCategory  = new Application_Model_GoodCategory();
        $QDistributor   = new Application_Model_Distributor();
        $QWarehouse     = new Application_Model_Warehouse();
        $QBrand         = new Application_Model_Brand();

        $QIR            = new Application_Model_ImeiReturn();

        $goods             = $QGood->get_cache();
        $goodColors        = $QGoodColor->get_cache();
        $good_categories   = $QGoodCategory->get_cache();
        $distributor_cache = $QDistributor->get_cache2();
        $warehouses        = $QWarehouse->get_cache();

        $result = $db->query($sql);
        //print_r($result);die;
        $i = 2;

        $temp_array_imei_return = array();

        foreach($result as $item) {

            if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
            else { $temp_sn = $item['sn_ref']; }

            if ($item['status'] == 1) { $temp_status = 'Actived'; }
            else if ($item['status'] == 2) { $temp_status = 'Expired'; }
            else { $temp_status = 'Expired'; }

            if ($item['type'] == 1) //for retailer
            $type = 'For Retailer';
            elseif ($item['type'] == 2) //for APK
            $type = 'For APK';
            elseif ($item['type'] == 3) //for staffs
            $type = 'For Staffs';
            elseif ($item['type'] == 4) //for lending
            $type = 'For Lending';
            elseif ($item['type'] == 5) //for DEMO
            $type = 'For DEMO';    

            if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = '????????????'; }
            else { $branch_type = '????'; }

            if(!in_array($item['sn'], $temp_array_imei_return)){

                array_push($temp_array_imei_return, $item['sn']);
                $arrGetImeiReturn = $QIR->getImeiReturnOrder($item['sn']);

                foreach ($arrGetImeiReturn as $value) {
                    $row = array();
                    $row[] = $item['id'];
                    $row[] = '="'.$temp_sn.'"';
                    $row[] = $item['invoice_number'];
                    
    // Start 
    $where = $QDistributor->getAdapter()->quoteInto('id =?',$item['d_id']);
    $distributor_arr = $QDistributor->fetchRow($where);
    
    if($distributor_arr['agent_status'] == 1){

        $where = $QWarehouse->getAdapter()->quoteInto('id =?',$distributor_arr['agent_warehouse_id']);
        $warehouse_arr = $QWarehouse->fetchRow($where);
        
        $row[] = $warehouse_arr['code'];
        $row[] = $warehouse_arr['name'];
        
        }else{
        
        $row[] = $item['store_code'];
        $row[] = $item['store_name'];
        
        }
        // End
                    $row[] = $distributor_cache[$item['d_id']]['d_code'];
                    $row[] = $distributor_cache[$item['d_id']]['title'];
                    // $row[] = $distributor_cache[$item['d_id']]['unames'];
                    // $row[] = '="'.$distributor_cache[$item['d_id']]['mst_sn'].'"';
                    // $row[] = $branch_type;
                    // $row[] = '="'.$distributor_cache[$item['d_id']]['branch_no'].'"';
                    $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area);
                    $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
                    // $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::District);
                    $row[] = $good_categories[$value['cat_id']];

                    $brands = $QBrand->getBrand($value['good_id']);

                    $row[] = $brands[0]['brand_name'].' '.$goods[$value['good_id']];
                    $row[] = $goodColors[$value['good_color']];
                    //$row[] = $item['num'];
                    if ($value['cat_id'] == PHONE_CAT_ID) { $row[] = '1'; } 
                    else { $row[] = $value['num']; } 
                    
                    $row[] = 'IMEI Return';

                    $row[] = $value['imei_sn'];
                    $row[] = $value['created_at'];
                    // $row[] = $value['creditnote_sn'];

                    $row[] = $value['sale_off_percent'];
                    $row[] = number_format($value['price'] / 1, 2);
                    // $row[] = number_format($value['total'] / 1, 2);
                    // $row[] = !is_null($value['delivery_fee']) ? $value['delivery_fee'] : 0;

                    // $row[] = number_format($value['price'], 2);
                    // $row[] = number_format($value['total'], 2);
                    // $row[] = $value['pay_time'];
                    // $row[] = $value['shipping_yes_time'];
                    $row[] = $warehouses[$value['warehouse_id']];
                    $row[] = $value['outmysql_time'];
                    $row[] = $temp_status;
                    $row[] = $value['add_time'];
                    $row[] = $type;
                    // $row[] = $value['text'];
                    // $row[] = $value['bs_campaign'];
                    // $row[] = $value['sales_catty_id'];

                    // $order_status = '';
                    // if(isset($value['canceled']) && $value['canceled']){
                    //     $order_status = 'YES';
                    // }

                    // $row[] = $order_status;

                    $activated_status = 'No Activate';
                    if(isset($value['activated_date']) && $value['activated_date']){
                        $activated_status = 'Activated';
                    }

                    if ($value['cat_id'] == PHONE_CAT_ID && $value['imei_sn']) {
                        $row[] = $activated_status;
                        $row[] = $value['activated_date'];
                    }else{
                        $row[] = '';
                        $row[] = '';
                    }

                    if (isset($params['order_packed_sim']) and $params['order_packed_sim']=="1")
                    {
                        if($item['simcard']!=""){
                            $row[] = 'YES';
                            $row[] = "'".$item['simcard'];
                            $row[] = $item['tel_no'];
                            $row[] = $item['sim_activated_updated_at'];
                            $row[] = $item['confirm_rebate_date'];
                            $row[] = $item['operator'];
                            $row[] = $item['sellout_updated_at'];
                        }
                    }

                    fputcsv($output, $row);
                    unset($row);
                }
            }

            $row = array();
            $row[] = $item['id'];
            $row[] = '="'.$temp_sn.'"';
            $row[] = $item['invoice_number'];

    // Start 
    $where = $QDistributor->getAdapter()->quoteInto('id =?',$item['d_id']);
    $distributor_arr = $QDistributor->fetchRow($where);
    
    if($distributor_arr['agent_status'] == 1){

        $where = $QWarehouse->getAdapter()->quoteInto('id =?',$distributor_arr['agent_warehouse_id']);
        $warehouse_arr = $QWarehouse->fetchRow($where);
        
        $row[] = $warehouse_arr['code'];
        $row[] = $warehouse_arr['name'];
        
        }else{
        
        $row[] = $item['store_code'];
        $row[] = $item['store_name'];
        
        }
        // End

            $row[] = $distributor_cache[$item['d_id']]['d_code'];
            $row[] = $distributor_cache[$item['d_id']]['title'];
            // $row[] = $distributor_cache[$item['d_id']]['unames'];
            // $row[] = '="'.$distributor_cache[$item['d_id']]['mst_sn'].'"';
            // $row[] = $branch_type;
            // $row[] = '="'.$distributor_cache[$item['d_id']]['branch_no'].'"';
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area);
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
            // $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::District);
            $row[] = $good_categories[$item['cat_id']];
            $brands = $QBrand->getBrand($item['good_id']);
            $row[] = $brands[0]['brand_name'].' '.$goods[$item['good_id']];
            $row[] = $goodColors[$item['good_color']];
            //$row[] = $item['num'];
            if ($item['cat_id'] == PHONE_CAT_ID) { $row[] = '1'; } 
            else { $row[] = $item['num']; } 
            $row[] = $item['imei_sn'];

            $row[] = '';
            $row[] = '';
            // $row[] = '';

            $row[] = $item['sale_off_percent'];
            $row[] = number_format($item['price'] / 1, 2);
            // $row[] = number_format($item['total'] / 1, 2);
            // $row[] = !is_null($item['delivery_fee']) ? $item['delivery_fee'] : 0;

            // $row[] = number_format($item['price'], 2);
            // $row[] = number_format($item['total'], 2);
            // $row[] = $item['pay_time'];
            // $row[] = $item['shipping_yes_time'];
            $row[] = $warehouses[$item['warehouse_id']];
            $row[] = $item['outmysql_time'];
            $row[] = $temp_status;
            $row[] = $item['add_time'];
            $row[] = $type;
            // $row[] = $item['text'];
            // $row[] = $item['bs_campaign'];
            // $row[] = $item['sales_catty_id'];

            // $order_status = '';
            // if(isset($item['canceled']) && $item['canceled']){
            //     $order_status = 'YES';
            // }

            // $row[] = $order_status;

            $activated_status = 'No Activate';
            if(isset($item['activated_date']) && $item['activated_date']){
                $activated_status = 'Activated';
            }

            if ($item['cat_id'] == PHONE_CAT_ID && $item['imei_sn']) {
                $row[] = $activated_status;
                $row[] = $item['activated_date'];
            }else{
                $row[] = '';
                $row[] = '';
            }

            if (isset($params['order_packed_sim']) and $params['order_packed_sim']=="1")
            {
                if($item['simcard']!=""){
                    $row[] = 'YES';
                    $row[] = "'".$item['simcard'];
                    $row[] = $item['tel_no'];
                    $row[] = $item['sim_activated_updated_at'];
                    $row[] = $item['confirm_rebate_date'];
                    $row[] = $item['operator'];
                    $row[] = $item['sellout_updated_at'];
                }
            }

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

public function salesConfirmAccessoriesOrderAction()
{
// print_r($_POST);die;
    $sn = $this->getRequest()->getParam('sn');
    $flashMessenger = $this->_helper->flashMessenger;

    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;

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
            $this->_redirect('/sales');
        }

        if (!isset($sales[0]) || ($sales[0]['shipping_yes_time'] and $sales[0]['pay_time'])) {

            $flashMessenger->setNamespace('error')->addMessage('This order cannot be changed!');

            $this->_redirect('/sales');

        }

        $QMarketProduct = new Application_Model_MarketProduct();
        $QMarket = new Application_Model_Market();
//Ti?n di don n?u cÃƒÂ³ b?o v? giÃƒÂ¡ thÃƒÂ¬ dÃƒÂ£ tr? ti?n
        $sn_total = 0;
$intRebate = intval($QMarketProduct->getPrice($sn)); // s? ti?n du?c gi?m
//$sn_total = $total_amount - $intRebate; // s? ti?n cÃƒÂ²n l?i
$sn_total = $QMarket->getPrice($sn) - $intRebate; // s? ti?n cÃƒÂ²n l?i

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

//l?y dealer m?
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
$checkPaymentStatus = 0; //ki?m tra cÃƒÂ³ th? cho phÃƒÂ©p payment khÃƒÂ´ng?
if ($remain_balance) {

    if ($sn_total <= $remain_balance)
    {
        $checkPaymentStatus = 1;
    } else{
        $checkPaymentStatus = 0;
    }

    $checkBalance = $remain_balance - $sn_total;
} else{
    $checkBalance = -$sn_total;
}

//x? lÃƒÂ½ check payment
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

    if ($this->getRequest()->getMethod() == 'POST') {
    //print_r($_POST);die;
    // print_r($_FILES); die;
    //$file_name_show = $_FILES['file']['name'];
    //print_r($file_name_show);
    //die;

        $db->beginTransaction();
        try { 

            $payment       = $this->getRequest()->getParam('payment');
            $shipping      = $this->getRequest()->getParam('shipping');
            $pay_text      = $this->getRequest()->getParam('pay_text');
            $shipping_text = $this->getRequest()->getParam('shipping_text');
            $payment_type = $this->getRequest()->getParam('payment_type',NULL);
            $payment_order = $this->getRequest()->getParam('payment_order', 0);
            $payment_bank_transfer = $this->getRequest()->getParam('payment_bank_transfer', 0);
            $payment_service = $this->getRequest()->getParam('payment_service', 0);
            $payment_servicecharge = $this->getRequest()->getParam('payment_servicecharge', 0);

            $pay_time      = $this->getRequest()->getParam('pay_time');
            $bank          = $this->getRequest()->getParam('select_bank_id', NULL);
            $type          = 1;
        //$company_id    = $this->getRequest()->getParam('company_id');
            $company_id    = 1;
            $retailer_rank = $this->getRequest()->getParam('retailer_rank', NULL);

            $sn_total  = $this->getRequest()->getParam('total_out_amount', 0);
            $total_amount  = $this->getRequest()->getParam('total_amount', 0);


            if($total_amount==0)
            {
                $payment_order=0;
                $payment_bank_transfer=0;
                $payment_servicecharge=0;
                $payment_service=0;
            }                   

            $where = array();
            $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

            $data = array(
                'pay_text' => $pay_text,
                'shipping_text' => $shipping_text,
            );

            $date = date('Y-m-d H:i:s');

            $checkUpdateCheckMoney = 0;
        //$QCheckMoney    = new Application_Model_CheckmoneySales();

            $confirm_access_status = $this->getRequest()->getParam('confirm_access_status', NULL);
            $confirm_access_remark = $this->getRequest()->getParam('confirm_access_remark', NULL);
            if($confirm_access_status==1){
                $data['confirm_access_status'] = 1;
                $data['confirm_access_date'] = $date;
                $data['confirm_access_by'] = $userStorage->id;
                $data['confirm_access_remark'] = $confirm_access_remark;

                $data['confirm_so'] = 1;
                $data['sales_confirm_date'] = $date;
                $data['sales_confirm_id'] = $userStorage->id;

                $data['pay_time'] = $date;
                $data['pay_user'] = $userStorage->id;
                $data['shipping_yes_time'] = $date;
                $data['shipping_yes_id'] = $userStorage->id;

                $data['pay_text'] = $pay_text;
                $data['payment_type'] = 'CR';

                $data['shipping_text'] = $shipping_text;
                $data['finance_confirm_id'] = $userStorage->id;
                $data['finance_confirm_date'] = $date;

            }else{
                $data['confirm_access_status'] = 2;
                $data['confirm_access_date'] = $date;
                $data['confirm_access_by'] = $userStorage->id;
                $data['confirm_access_remark'] = $confirm_access_remark;
            }

        //print_r($data);die;
            $QMarket->update($data, $where);

            $QCheckmoney = new Application_Model_Checkmoney();
            /*---------------OUT-----------*/
            $note_new='';
            $data_ch = array(
                'd_id'       => $sales[0]['d_id'],
                'payment'    => $date,
                'pay_time'   => $date,
                'pay_service'     => $payment_service_val,
                'output'     => $total_amount,
                'pay_money'  => -$total_amount,
                'type'       => 2,
                'sn'         => $sn,
                'user_id'    => $userStorage->id,
                'create_by'  => $userStorage->id,
                'create_at'  => $date,
                'note'       => $note_new,
                'company_id' => $company_id,
                'sales_confirm_date'    => $date,
                'sales_confirm_id'    => $userStorage->id,
            );

            $QCheckmoney->insert($data_ch);

            /* -----------IN------------- */
            $date = date('Y-m-d H:i:s');
            $note_new='';
            $data_f = array(
                'd_id'                  => $sales[0]['d_id'],
                'bank'                  => 0,
                'pay_money'             => $total_amount,
                'pay_servicecharge'     => 0,
                'pay_banktransfer'      => 0,
                'pay_service'           => 0,
                'type'                  => 1,
                'pay_time'              => $date,
                'bank_serial'           => null,
                'bank_transaction_code' => null,
                'note'                  => $note_new,
                'content'               => null,
                'company_id'            => $company_id,
                'sn'                    => $sn,
                'file_pay_slip'         => null,
                'user_id'               => $userStorage->id,
                'create_by'             => $userStorage->id,
                'create_at'             => $date,
                'sales_confirm_id'      => $userStorage->id,
                'sales_confirm_date'    => $date,
                'addition'              => 1
            );

            $QCheckmoney->insert($data_f);

            /* ------------------------ */     

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
                    $this->_redirect('/sales/sales-confirm-accessories-order?sn=' . $sn);
                }
            }

            $db->commit();
            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $this->_redirect('/sales/index-accessories?view_accessories=wait');
        }
        catch (exception $e) {
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!' .
                $e->getMessage());
            $this->_redirect('/sales/sales-confirm-accessories-order?sn=' . $sn);
        }
        $flashMessenger->setNamespace('success')->addMessage('Done!');
        $this->_redirect('/sales/index-accessories?view_accessories=wait');

} //End if check post

$where = array();
$where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
$where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

$sales = $QMarket->fetchAll($where);
//print_r($sales);

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
$warehouses    = $QWarehouse->get_cache();

$Credit_Note = $QMarket->fetchCredit_Note($sn);

$deposit_list = $QMarket->fetchDeposit($sn);

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

    //get retailer : rank
            $data[$k]['retailer_rank'] = isset($distributors_info->rank) ? $distributors_info->rank : '';

    //get retailer : rank
            $data[$k]['show_cash_menu'] = $show_cash_menu;

    //get retailer : credit amount
            $data[$k]['credit_amount'] = isset($distributors_info->credit_amount) ? $distributors_info->credit_amount : '';

    //print_r($sales[0]['credit_amount']);

            $credit_amount = isset($distributors_info->credit_amount) ? $distributors_info->credit_amount : '0';

            $this->view->distributor_total_balance = ($credit_amount - ($distributor_balance*-1));


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

                            $data[$k]['deposit_list'] = $deposit_list;

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
                        $banks = $QBank->fetchAll(null,'name asc');
                        $this->view->banks = $banks;

                        $this->view->detailBVG = $detailBVG;
                        $this->view->detailDiscount = $detailDiscount;

                        $this->view->sales = $data;

//print_r($data);
                    }
                }

                public function salesConfirmOrderAction()
                {   

                    $sn = $this->getRequest()->getParam('sn');
                    $flashMessenger = $this->_helper->flashMessenger;

                    $messages = $flashMessenger->setNamespace('error')->getMessages();
                    $this->view->messages = $messages;

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
                            $this->_redirect('/sales');
                        }

                        if (!isset($sales[0]) || ($sales[0]['shipping_yes_time'] and $sales[0]['pay_time'])) {

                            $flashMessenger->setNamespace('error')->addMessage('This order cannot be changed!');

                            $this->_redirect('/sales');

                        }

                        $usePaygroup = [];
                        $QPGBal = new Application_Model_PayGroupBalance();

                        $usePaygroup = $QPGBal->getUsePaygroup($sales[0]['d_id']);

                        $this->view->usePaygroup = $usePaygroup;

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

                        $QMarketProduct = new Application_Model_MarketProduct();
                        $QMarket = new Application_Model_Market();
//Ti?n di don n?u cÃƒÂ³ b?o v? giÃƒÂ¡ thÃƒÂ¬ dÃƒÂ£ tr? ti?n
                        $sn_total = 0;
$intRebate = intval($QMarketProduct->getPrice($sn)); // s? ti?n du?c gi?m
//$sn_total = $total_amount - $intRebate; // s? ti?n cÃƒÂ²n l?i
$sn_total = $QMarket->getPrice($sn) - $intRebate; // s? ti?n cÃƒÂ²n l?i

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

//l?y dealer m?
$main_retailer                   = $QDistributor->getRootDistributor($sales[0]['d_id']);
$this->view->main_retailer       = $main_retailer;
$total_balance_row               = $QStoreaccount->getBalanceByGroup($sales[0]['d_id']);
$distributor_balance_row         = $QStoreaccount->getBalance($sales[0]['d_id']);
// $q_total_balance                 = $QStoreaccount->getMainDistributorBalance($sales[0]['d_id']); 
//------------------------

$selectCompany = $db->select()
->from(array('p'=>'warehouse'),array('p.company_id'))
->where('id = ?',$sales[0]['warehouse_id']);
$company_id = $db->fetchOne($selectCompany);

// $remain_balance                  = ($company_id == 1) ? $distributor_balance_row['balance'] : $distributor_balance_row['balance_smartmobile'];

$credit_amount                  = $distributor_balance_row['credit_amount'];
$remain_balance                  = $distributor_balance_row['use_credit'];
$total_balance                  = $distributor_balance_row['balance'];

$distributor_balance             = ($company_id == 1) ? $distributor_balance_row['balance'] : $distributor_balance_row['balance_smartmobile'];
$this->view->distributor_balance = $distributor_balance;

$checkBalance = 0;
$checkPaymentStatus = 0; //ki?m tra cÃƒÂ³ th? cho phÃƒÂ©p payment khÃƒÂ´ng?
if ($remain_balance) {

    if ($sn_total <= $remain_balance)
    {
        $checkPaymentStatus = 1;
    } else{
        $checkPaymentStatus = 0;
    }

    //$checkBalance = $remain_balance - $sn_total;
    $checkBalance =  $total_balance - $sn_total;
} else{
    $checkBalance = -$sn_total;
}

//x? lÃƒÂ½ check payment
$this->view->need               = abs($checkBalance);
$this->view->checkPaymentStatus = $checkPaymentStatus;
$this->view->credit_amount       = $credit_amount;
$this->view->checkBalance       = $checkBalance;
$this->view->remain_balance     = $remain_balance*-1;
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
    if ($this->getRequest()->getMethod() == 'POST') {
        // print_r($_POST);die;
        // echo "<pre>";
        // print_r($_POST);
        // print_r($_FILES); die;
        //$file_name_show = $_FILES['file']['name'];
        //print_r($file_name_show);
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('m' => 'market'),
            array('m.sn_ref'));
        $select->join(array('s'=>'staff'),'sales_confirm_id=s.id',array('s.firstname'));
        $select->where('m.sn = ?', $sn);
        $select->where('sales_confirm_id  is not null',1);
        $select->where('sales_confirm_date  is not null',1);
        $select->group('m.sn');



        $Check_pay_list = $db->fetchRow($select);



        if (isset($Check_pay_list) and $Check_pay_list) {
        // die($Check_pay_list['sn_ref']);
            $flashMessenger->setNamespace('error')->addMessage('Order '.$Check_pay_list['sn_ref'].' ??????????????? '.$Check_pay_list['firstname'] );
            $this->_redirect('/sales');

        }else{

        // update flag
            $this->addFlagConfirmSo($sn);

            $db->beginTransaction();

            try { 

                $payment       = $this->getRequest()->getParam('payment');
                $shipping      = $this->getRequest()->getParam('shipping');
                $pay_text      = $this->getRequest()->getParam('pay_text');
                $shipping_text = $this->getRequest()->getParam('shipping_text');
                $payment_type = $this->getRequest()->getParam('payment_type',NULL);
                $payment_order = $this->getRequest()->getParam('payment_order', 0);
                $payment_bank_transfer = $this->getRequest()->getParam('payment_bank_transfer', 0);
                $payment_service = $this->getRequest()->getParam('payment_service', 0);
                $payment_servicecharge = $this->getRequest()->getParam('payment_servicecharge', 0);

                $pay_time      = $this->getRequest()->getParam('pay_time');
                $bank          = $this->getRequest()->getParam('select_bank_id', 0);
                $type          = 1;
            //$company_id    = $this->getRequest()->getParam('company_id');
                $company_id    = 1;
                $retailer_rank = $this->getRequest()->getParam('retailer_rank', NULL);

                $sn_total       = $this->getRequest()->getParam('total_out_amount', 0);
                $total_amount  = $this->getRequest()->getParam('total_amount', 0);

                $lacksurplus  = $this->getRequest()->getParam('lacksurplus', 0);

                $checkbox_use_paygroup   = $this->getRequest()->getParam('checkbox_use_paygroup');
                $use_paygroup   = $this->getRequest()->getParam('use_paygroup');
                $money_use_paygroup   = $this->getRequest()->getParam('money_use_paygroup');

                $use_credit_card_input   = $this->getRequest()->getParam('use_credit_card_input');

                $d_id = $sales[0]['d_id'];

                $allow_surplus = false;

            // brand shop & service
                if (My_Staff_Group::inGroup($userStorage->group_id, array(25,28))){
                    $allow_surplus = true;
                }

                if($lacksurplus < -10 && !$allow_surplus){

                // remove flag
                    $db->rollback();
                    $this->removeFlagConfirmSo($sn);

                // echo '????????????????????????????? ?????????????????????????';
                    $flashMessenger->setNamespace('error')->addMessage('???????????????????? ?????????????????????????');
                    $this->_redirect('/sales/sales-confirm-order?sn=' . $sn);
                    exit();
                }

                if($checkbox_use_paygroup){

                    $usePaygroup = $QPGBal->getUsePaygroup($d_id);

                    $count_check_usepaygroup=0;
                    $i=0;
                    foreach ($use_paygroup as $key) {

                        foreach ($usePaygroup as $key_sub) {
                            if($key == $key_sub['payment_id']){

                                if($key_sub['balance_total'] >= $money_use_paygroup[$i]){
                                    $count_check_usepaygroup++;
                                }else{

                                // remove flag
                                    $db->rollback();
                                    $this->removeFlagConfirmSo($sn);

                                // echo '????????????????????????????????????????? ' . $key_sub['payment_no'] . ' ?????????????????????????';
                                    $flashMessenger->setNamespace('error')->addMessage('????????????????????????????????????????? ' . $key_sub['payment_no'] . ' ?????????????????????????');
                                    $this->_redirect('/sales/sales-confirm-order?sn=' . $sn);
                                    exit();
                                }
                            }
                        }
                        $i++;
                    }

                    if($count_check_usepaygroup < count($use_paygroup)){

                    // remove flag
                        $db->rollback();
                        $this->removeFlagConfirmSo($sn);

                    // echo '?????????????????????????????????';
                        $flashMessenger->setNamespace('error')->addMessage('?????????????????????????????????');
                        $this->_redirect('/sales/sales-confirm-order?sn=' . $sn);
                        exit();
                    }

                }

                if($total_amount==0)
                {
                    $payment_order=0;
                    $payment_bank_transfer=0;
                    $payment_servicecharge=0;
                    $payment_service=0;
                }

                if(isset($sales[0]['pay_group']) and $sales[0]['pay_group'] != 1){
                // payment no

                    $QPG = new Application_Model_PayGroup();
                    $where = array();
                    $where[] = $QPG->getAdapter()->quoteInto('sale_order = ?', $sn);
                    $where[] = $QPG->getAdapter()->quoteInto('status = ?', 1);
                    $payment_group_check = $QPG->fetchRow($where);

                    if ($payment_group_check) {

                        $payment_id = $payment_group_check['payment_id'];

                        $array_data = array(
                            'created_at' => $userStorage->id,
                            'modified_date' => date('Y-m-d H:i:s')
                        );

                        $where = array();
                        $where[] = $QPG->getAdapter()->quoteInto('sale_order = ?', $sn);
                        $where[] = $QPG->getAdapter()->quoteInto('status = ?', 1);

                        $QPG->update($array_data, $where);

                    }else{
                        $payment_id = date('YmdHis') . substr(microtime(), 2, 4);
                        $sale_order = $sn;
                        $payment_group = 0;
                        $case_text = null;
                        $money = $this->getRequest()->getParam('total_amount', 0);
                    // $lacksurplus = 0.00;

                        $pay_bank_transfer = 0.00;
                        $pay_servicecharge = 0.00;

                        $created_date = date('Y-m-d H:i:s');
                        $modified_date = date('Y-m-d H:i:s');
                        $status = 1;

                        $QPG->insert(array(
                            'payment_no' => $payment_id,
                            'payment_id' => $payment_id,
                            'sale_order' => $sale_order,
                            'd_id' => $d_id,
                            'payment_group' => $payment_group,
                            'case_text' => $case_text,
                            'money' => $money,
                            'lacksurplus' => $lacksurplus,
                            'pay_bank_transfer' => $pay_bank_transfer,
                            'pay_servicecharge' => $pay_servicecharge,

                            'created_at' => $userStorage->id,
                            'created_date' => $created_date,
                            'modified_at' => $userStorage->id,
                            'modified_date' => $modified_date,
                            'status' => $status
                        ));

                        if($lacksurplus > 0){
                            $QPGBal->insert(array(
                                'payment_id' => $payment_id,
                                'distributor_id' => $d_id,
                                'total_amount' => $lacksurplus,
                                'use_total' => 0,
                                'balance_total' => $lacksurplus,
                                'status' => $status,
                                'create_date' => $created_date,
                                'create_by' => $userStorage->id,
                                'update_date' => $modified_date,
                                'update_by' => $userStorage->id,
                                'use_status' => 0,
                                'remark' => null
                            ));
                        }else{
                            $QPGBal->insert(array(
                                'payment_id' => $payment_id,
                                'distributor_id' => $d_id,
                                'total_amount' => 0,
                                'use_total' => 0,
                                'balance_total' => 0,
                                'status' => $status,
                                'create_date' => $created_date,
                                'create_by' => $userStorage->id,
                                'update_date' => $modified_date,
                                'update_by' => $userStorage->id,
                                'use_status' => 0,
                                'remark' => null
                            ));
                        }
                    }

                    if($checkbox_use_paygroup){

                        $QPGT = new Application_Model_PayGroupTran();

                        $i=0;
                        foreach ($use_paygroup as $key) {

                            if($money_use_paygroup[$i] > 0){

                                $QPGT->insert(array(
                                    'payment_id' => $key,
                                    'payment_tran_id' => $payment_id,
                                    'distributor_id' => $d_id,
                                    'use_total' => $money_use_paygroup[$i],
                                    'create_date' => $created_date,
                                    'create_by' => $userStorage->id,
                                    'status' => 1
                                ));
                            }

                            $i++;
                        }
                    }

                }

                $where = array();
                $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

                $data = array(
                    'pay_text' => $pay_text,
                    'shipping_text' => $shipping_text,
                );

            // get another info of distributor
                $where_payment = $QDistributor->getAdapter()->quoteInto('id = ?', $sales[0]['d_id']);
                $distributors_payment = $QDistributor->fetchRow($where_payment);


                $auto_confirm_finance = $distributors_payment->auto_confirm_finance;
                $auto_confirm_finance_warehouse = $distributors_payment->warehouse_id;
                if($auto_confirm_finance_warehouse =='73' || $auto_confirm_finance_warehouse =='62' || $auto_confirm_finance_warehouse =='115' || $auto_confirm_finance_warehouse =='125')
                {
                  $auto_confirm_finance =1;  
              }

              $date = date('Y-m-d H:i:s');

              $checkUpdateCheckMoney = 0;
            //$QCheckMoney    = new Application_Model_CheckmoneySales();

              if ($payment) {

                //$data['pay_time'] = $date;
               // $data['pay_user'] = $userStorage->id;
                $data['payment_type'] = $payment_type;

                //check money
                $QCheckmoney = new Application_Model_Checkmoney();

                //Ki?m tra don hÃƒÂ ng dÃƒÂ£ dÃƒÂ¡nh d?u payment l?n nÃƒÂ o chua?, n?u r?i thÃƒÂ¬ b? qua
                $select_sn = array();
                $select_sn[] = $QCheckmoney->getAdapter()->quoteInto('sn = ?', $sales[0]['sn']);
                $select_sn[] = $QCheckmoney->getAdapter()->quoteInto('type = ?', 2); // phÃƒÂ¢n lo?i tr? ti?n
                $check_sn_exist = $QCheckmoney->fetchRow($select_sn);


                if (!$check_sn_exist) {

                    $payment_service_val=0;
                    for($i=0;$i<count($payment_order);$i++){
                        $payment_service_val +=$payment_service[$i];
                    }

                    $sn_total = 0;
                    $intRebate = intval($QMarketProduct->getPrice($sales[0]['sn']));
                    $sn_total = $QMarket->getPrice($sales[0]['sn']) - $intRebate;

                    $note_new='Payment Order='.number_format($sn_total,2).' ?????????????????????='.number_format($payment_service_val,2);

                    //data for checkmoney transaction
                    $data_ch = array(
                        'd_id'       => $sales[0]['d_id'],
                        'payment'    => $date,
                        'pay_time'   => $date,
                        'pay_service'     => $payment_service_val,
                        'output'     => $sn_total,
                        'pay_money'  => -$sn_total,
                        'type'       => 2,
                        'sn'         => $sales[0]['sn'],
                        'user_id'    => $userStorage->id,
                        'create_by'  => $userStorage->id,
                        'create_at'  => $date,
                        'note'       => $note_new,
                        'company_id' => $company_id,
                        'sales_confirm_date'    => $date,
                        'sales_confirm_id'    => $userStorage->id,
                    );

                    if($auto_confirm_finance =='1')
                    {
                        $data_ch['finance_confirm_id'] = $userStorage->id;
                        $data_ch['finance_confirm_date'] = $date;
                    }


                    $checkUpdateCheckMoney = $QCheckmoney->insert($data_ch);

                    // update balance
                    if ($checkUpdateCheckMoney) {
                       // $QStoreaccount->updateBalance($sales[0]['d_id']);
                    } else {
                        exit("Loi khong dong bo!");
                    }

                    // Neu co uy nhiem chi
                    $payment_order_val=0;$payment_bank_transfer_val=0;$pay_service_val=0;$i=0;
                    for($i=0;$i<count($payment_order);$i++){
                        $payment_order_val +=$payment_order[$i];
                        $pay_service_val +=$pay_service[$i];
                        $payment_order_val +=$payment_order[$i];
                    }

                    if($payment_type=="CA"){
                        if ($payment_order_val >=0) {
                            $status=1;
                            if($retailer_rank ==10){
                                $status=0;
                            }
                               // $pay_money = $payment_order+$payment_bank_transfer;
                            $QCheckmoneyPaymentorder = new Application_Model_CheckmoneyPaymentorder();
                            $QCheckmoneyPaymentorder->insert(array(
                                'd_id'          => $sales[0]['d_id'],
                                'sn'            => $sales[0]['sn'],
                                'payment_order' => ($payment_order_val+$payment_order_val),
                                'pay_banktransfer' => $payment_bank_transfer_val,
                                'status'        => $status,
                                'created_at'    => $date,
                                'created_by'    => $userStorage->id,
                                'sales_confirm_date'    => $date,
                                'sales_confirm_id'    => $userStorage->id,
                            ));
                            } //End check payment order
                        }
                } //End check sn existed

            } //End check if payment

            if ($shipping) {
               // $data['shipping_yes_time'] = $date;
               // $data['shipping_yes_id'] = $userStorage->id;
            }

            $data['confirm_so'] = 1;

            $data['sales_confirm_date'] = $date;
            $data['sales_confirm_id'] = $userStorage->id;


            if($auto_confirm_finance =='1'){
                $data['pay_time'] = $date;
                $data['pay_user'] = $userStorage->id;
                $data['shipping_yes_time'] = $date;
                $data['shipping_yes_id'] = $userStorage->id;

                $data['pay_text'] = "Confirm Payment Auto by Finance";
                //$data['shipping_text'] = "Confirm Shipping Auto by Finance";
                $data['finance_confirm_id'] = $userStorage->id;
                $data['finance_confirm_date'] = $date;

            }
            //print_r($data);die;
            $QMarket->update($data, $where);

            $time = time();

            /* ---------Add Money Check--------------- */

            if($total_amount >= 0 && $payment_type=="CA")
            {
                $payment_order_val=0;$payment_bank_transfer_val=0;$i=0;
                $payment_service_val=0;$payment_servicecharge_val=0;

                for($i=0;$i<count($payment_order);$i++){

                 if($bank[$i]==''){
                  $bank_id=0; 
              }else{
                  $bank_id=$bank[$i];
              }
              $payment_order_val =$payment_order[$i];
              $payment_bank_transfer_val =$payment_bank_transfer[$i];
              $payment_service_val =$payment_service[$i];
              $payment_servicecharge_val =$payment_servicecharge[$i];
              $pay_time_val=$pay_time[$i];
              $d_id = $sales[0]['d_id'];
              $date = date('Y-m-d H:i:s');

                    //pay_banktransfer

              $QCheckMoney    = new Application_Model_Checkmoney();
              $QStoreaccount  = new Application_Model_Storeaccount();
                    //$userStorage    = Zend_Auth::getInstance()->getStorage()->read();

                    //$pay_money = $payment_order+$payment_bank_transfer;

              $userStorage = Zend_Auth::getInstance()->getStorage()->read();
              $renew_file_name = ($i+1).'_'.$sn.'_'.$time.'_'.$userStorage->id.'.'.pathinfo($_FILES['file']['name'][$i],PATHINFO_EXTENSION);
              $file_name_upload = '/pay_slips/'.$sn.'/'.$renew_file_name;
                    // $file_name_upload = '/pay_slips/'.$sn.'/'.$_FILES['file']['name'][$i];

              $note_new='PayMoney='.number_format($payment_order_val,2) .' Fee transfer='.number_format($payment_bank_transfer_val,2).' Service Charge='.number_format($payment_servicecharge_val,2).' ?????????????????????='.number_format($payment_service_val,2);

              $credit_card = 0;
              if($use_credit_card_input[$i]){
                $credit_card = $use_credit_card_input[$i];
            }

            $data = array(
                'd_id'                  => $d_id,
                'bank'                  => $bank_id,
                'pay_money'             => $payment_order_val,
                'pay_servicecharge'     => $payment_servicecharge_val,
                'pay_banktransfer'      => $payment_bank_transfer_val,
                'pay_service'           => $payment_service_val,
                'type'                  => 1,
                'pay_time'              => $pay_time_val,
                'bank_serial'           => null,
                'bank_transaction_code' => null,
                'note'                  => $note_new,
                'content'               => null,
                'company_id'            => $company_id,
                'sn'                    => $sn,
                'file_pay_slip'         => $file_name_upload,
                'user_id'               => $userStorage->id,
                'create_by'             => $userStorage->id,
                'create_at'             => $date,
                'sales_confirm_id'      => $userStorage->id,
                'sales_confirm_date'    => $date,
                'addition'              => 1,
                'credit_card'           => $credit_card
            );

            if($lacksurplus > 0){
                        // $data['payment_surplus'] = $lacksurplus;
                $data['payment_surplus'] = 0;
            }

            if($lacksurplus < 0){
                        // $data['lack_of_money'] = $lacksurplus*-1;
                $data['lack_of_money'] = 0;
            }

            $checkUpdateCheckMoney2 = $QCheckmoney->getCheckDuplicate($sn,$file_name_upload,$payment_order_val);


                // $file_name_upload = $_FILES['file']['name'][$i];

                // die;
            if ($checkUpdateCheckMoney2) {
                    // print_r($checkUpdateCheckMoney2);

                    // die;
            }else{
                if($ch_id){
                        // echo "1";
                    $old_checkmoney = $QCheckMoney->find($ch_id)->current();
                    $data['user_id'] = $userStorage->id;
                    $data['updated_at'] = $date;
                    $where = $db->quoteInto('id = ?',$ch_id);
                    $QCheckMoney->update($data,$where); 

                }else{
                        // echo "2";
                    $data['bank'] = $bank_id;
                    $data['create_by'] = $userStorage->id;
                    $data['create_at'] = $date;
                    $QCheckMoney->insert($data);
                }



                    /*
                    if($data['d_id'] >= 0){  
                        if($ch_id){//Tru?ng h?p update
                            if($data['d_id'] == $old_checkmoney['d_id']){
                                $QStoreaccount->updateBalance( $data['d_id'] );
                            }else{
                                $QStoreaccount->updateBalance( $data['d_id'] );
                                if( intval( $old_checkmoney['d_id'] ) >= 0 ){
                                    $QStoreaccount->updateBalance($old_checkmoney['d_id']);
                                }
                            }
                        }else{//Tru?ng h?p thÃƒÂªm m?i.
                            $QStoreaccount->updateBalance($data['d_id']);
                        }           
                    }
                    */
                }

                 //print_r($data_money);die;
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

                        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                        $filename = ($r+1).'_'.$sn.'_'.$time.'_'.$userStorage->id.'.'.pathinfo($fileInfo['name'],PATHINFO_EXTENSION);
                        $upload->addFilter('Rename', array('target' => $uploaded_dir.'/'.$filename)); 

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
                                    // remove flag
                                        $db->rollback();
                                        $this->removeFlagConfirmSo($sn);

                                        $flashMessenger->setNamespace('error')->addMessage($sError);
                                        $this->_redirect('/sales/sales-confirm-order?sn=' . $sn);
                                    }
                                }else{
                                 $upload->receive($file);
                             }                                                     
                         }
                         $r+=1;
                     }
                 }    
                 /*-------------------End File Pay Slip Upload--------------------------*/
             }
             /* ------------------------ */
             $time = time();
             if($total_amount >= 0 && $payment_type=="CC")
             {
                $payment_order_val=0;$payment_bank_transfer_val=0;$i=0;
                $payment_service_val=0;$payment_servicecharge_val=0;

                for($i=0;$i<count($payment_order);$i++){

                 if($bank[$i]==''){
                  $bank_id=0; 
              }else{
                  $bank_id=$bank[$i];
              }
              $payment_order_val =$payment_order[$i];
              $payment_bank_transfer_val =$payment_bank_transfer[$i];
              $payment_service_val =$payment_service[$i];
              $payment_servicecharge_val =$payment_servicecharge[$i];
              $pay_time_val=$pay_time[$i];
              $d_id = $sales[0]['d_id'];
              $date = date('Y-m-d H:i:s');

                    //pay_banktransfer

              $QCheckMoney    = new Application_Model_Checkmoney();
              $QStoreaccount  = new Application_Model_Storeaccount();
                    //$userStorage    = Zend_Auth::getInstance()->getStorage()->read();

                    //$pay_money = $payment_order+$payment_bank_transfer;

              $userStorage = Zend_Auth::getInstance()->getStorage()->read();
              $renew_file_name = ($i+1).'_'.$sn.'_'.$time.'_'.$userStorage->id.'.'.pathinfo($_FILES['file']['name'][$i],PATHINFO_EXTENSION);
              $file_name_upload = '/pay_slips/'.$sn.'/'.$renew_file_name;
                    // $file_name_upload = '/pay_slips/'.$sn.'/'.$_FILES['file']['name'][$i];

              $note_new='PayMoney='.number_format($payment_order_val,2) .' Fee transfer='.number_format($payment_bank_transfer_val,2).' Service Charge='.number_format($payment_servicecharge_val,2).' ?????????????????????='.number_format($payment_service_val,2);

              $data = array(
                'd_id'                  => $d_id,
                'bank'                  => $bank_id,
                'pay_money'             => $payment_order_val,
                'pay_servicecharge'     => $payment_servicecharge_val,
                'pay_banktransfer'      => $payment_bank_transfer_val,
                'pay_service'           => $payment_service_val,
                'type'                  => 1,
                'pay_time'              => $pay_time_val,
                'bank_serial'           => null,
                'bank_transaction_code' => null,
                'note'                  => $note_new,
                'content'               => null,
                'company_id'            => $company_id,
                'sn'                    => $sn,
                'file_pay_slip'         => $file_name_upload,
                'user_id'               => $userStorage->id,
                'create_by'             => $userStorage->id,
                'create_at'             => $date,
                'sales_confirm_id'      => $userStorage->id,
                'sales_confirm_date'    => $date,
                'addition'              => 1
            );

                    // $file_name_upload = $_FILES['file']['name'][$i];
                    // die;
              if($ch_id){
                $old_checkmoney = $QCheckMoney->find($ch_id)->current();
                $data['user_id'] = $userStorage->id;
                $data['updated_at'] = $date;
                $where = $db->quoteInto('id = ?',$ch_id);
                $QCheckMoney->update($data,$where); 

            }else{
                $data['bank'] = $bank_id;
                $data['create_by'] = $userStorage->id;
                $data['create_at'] = $date;
                $QCheckMoney->insert($data);
            }

                    /*
                    if($data['d_id'] >= 0){  
                        if($ch_id){//Tru?ng h?p update
                            if($data['d_id'] == $old_checkmoney['d_id']){
                                $QStoreaccount->updateBalance( $data['d_id'] );
                            }else{
                                $QStoreaccount->updateBalance( $data['d_id'] );
                                if( intval( $old_checkmoney['d_id'] ) >= 0 ){
                                    $QStoreaccount->updateBalance($old_checkmoney['d_id']);
                                }
                            }
                        }else{//Tru?ng h?p thÃƒÂªm m?i.
                            $QStoreaccount->updateBalance($data['d_id']);
                        }           
                    }
                    */
                }

                 //print_r($data_money);die;
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

                        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                        $filename = ($r+1).'_'.$sn.'_'.$time.'_'.$userStorage->id.'.'.pathinfo($fileInfo['name'],PATHINFO_EXTENSION);
                        $upload->addFilter('Rename', array('target' => $uploaded_dir.'/'.$filename));

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

                                    // remove flag
                                        $db->rollback();
                                        $this->removeFlagConfirmSo($sn);

                                        $flashMessenger->setNamespace('error')->addMessage($sError);
                                        $this->_redirect('/sales/sales-confirm-order?sn=' . $sn);
                                    }
                                }else{
                                 $upload->receive($file);
                             }                                                     
                         }
                         $r+=1;
                     }

                     /*-------------------End File Pay Slip Upload--------------------------*/
                 }

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
                    // remove flag
                        $db->rollback();
                        $this->removeFlagConfirmSo($sn);

                        $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
                        $this->_redirect('/sales/sales-confirm-order?sn=' . $sn);
                    }
                }

                $db->commit();
                $flashMessenger->setNamespace('success')->addMessage('Done!');
                $this->_redirect('/sales');
            }
            catch (exception $e) {

            // remove flag
                $db->rollback();
                $this->removeFlagConfirmSo($sn);

                $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!' .
                    $e->getMessage());
                $this->_redirect('/sales/sales-confirm-order?sn=' . $sn);
            }
            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $this->_redirect('/sales');
        }

    } //End if check post

    $where = array();
    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

    $sales = $QMarket->fetchAll($where);
//print_r($sales);

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
    $warehouses    = $QWarehouse->get_cache();

    $Credit_Note = $QMarket->fetchCredit_Note($sn);

    $deposit_list = $QMarket->fetchDeposit($sn);
//print_r($deposit_list);
    $QStoreAccount  = new Application_Model_Storeaccount();


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

    //get retailer : rank
                $data[$k]['retailer_rank'] = isset($distributors_info->rank) ? $distributors_info->rank : '';

    //get retailer : rank
                $data[$k]['show_cash_menu'] = $show_cash_menu;

    //get retailer : credit amount
                $q_store_account  = $QStoreAccount->getMainDistributorBalance($sale->d_id);

    //print_r($distributors_info);

    //$data[$k]['credit_amount'] = isset($distributors_info->credit_amount) ? $distributors_info->credit_amount : '';

                $credit_amount = isset($q_store_account['credit_amount']) ? $q_store_account['credit_amount'] : '0';

                $data[$k]['credit_amount'] = $credit_amount;



    //print_r($sales[0]['credit_amount']);

    //$credit_amount = isset($distributors_info->credit_amount) ? $distributors_info->credit_amount : '0';

                $this->view->distributor_total_balance = ($credit_amount - ($distributor_balance));


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
                                $data[$k]['deposit_list'] = $deposit_list;

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

                            $where = $QBank->getAdapter()->quoteInto("type_payment in('TR','CC')",null);

// Service Show Bank
                            if (My_Staff_Group::inGroup($userStorage->group_id, array(28))){
                                $where = array();
                                $where[] = $QBank->getAdapter()->quoteInto('id IN (1,5,17)',null);
                                $where[] = $QBank->getAdapter()->quoteInto("type_payment in('TR','CC')",null);
// Brand Shop Show Bank
                            }else if(My_Staff_Group::inGroup($userStorage->group_id, array(25))){
                                $where = array();
                                $where[] = $QBank->getAdapter()->quoteInto('id IN (1,3,5,7,8,9,10,16,17)',null);
                                $where[] = $QBank->getAdapter()->quoteInto("type_payment in('TR','CC')",null);
                            }

                            $banks = $QBank->fetchAll($where,'name asc');

                            $where = $QBank->getAdapter()->quoteInto('type_payment = ?','CC');
                            $banksCredit = $QBank->fetchAll($where,'name asc');
                            $this->view->banks = $banks;
                            $this->view->banksCredit = $banksCredit;

                            $this->view->detailBVG = $detailBVG;
                            $this->view->detailDiscount = $detailDiscount;

                            $this->view->sales = $data;

//print_r($data);
                        }
                    }

                    public function salesConfirmOrderMultiAction()
                    {   
                        $sn = $this->getRequest()->getParam('sn');
                        $flashMessenger = $this->_helper->flashMessenger;

                        $messages = $flashMessenger->setNamespace('error')->getMessages();
                        $this->view->messages = $messages;

                        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                        $sn = explode(",", $sn);

                        if ($sn) {

                            $db = Zend_Registry::get('db');
                            $QMarket = new Application_Model_Market();
                            $where = array();
                            $where[] = $QMarket->getAdapter()->quoteInto('sn in (?)', $sn);
                            $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

                            $sales = $QMarket->fetchAll($where);

                            $checkDis = array();
                            foreach ($sales as $key) {
                                array_push($checkDis, $key['d_id']);
                            }
                            if(count(array_unique($checkDis)) > 1){
                                $flashMessenger->setNamespace('error')->addMessage('Can not select many Distributor!');
                                $this->_redirect('/sales');
                            }

//check
                            if (!$sales) {
                                $flashMessenger->setNamespace('error')->addMessage('Invalid SN!');
                                $this->_redirect('/sales');
                            }

                            if (!isset($sales[0]) || ($sales[0]['shipping_yes_time'] and $sales[0]['pay_time'])) {

                                $flashMessenger->setNamespace('error')->addMessage('This order cannot be changed!');

                                $this->_redirect('/sales');

                            }

                            $QMarketProduct = new Application_Model_MarketProduct();
                            $QMarket = new Application_Model_Market();
//Ti?n di don n?u cÃƒÂ³ b?o v? giÃƒÂ¡ thÃƒÂ¬ dÃƒÂ£ tr? ti?n
                            $sn_total = 0;
$intRebate = intval($QMarketProduct->getPrice($sn)); // s? ti?n du?c gi?m
//$sn_total = $total_amount - $intRebate; // s? ti?n cÃƒÂ²n l?i
$sn_total = $QMarket->getPrice($sn) - $intRebate; // s? ti?n cÃƒÂ²n l?i

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

//l?y dealer m?
$main_retailer                   = $QDistributor->getRootDistributor($sales[0]['d_id']);
$this->view->main_retailer       = $main_retailer;
$total_balance_row               = $QStoreaccount->getBalanceByGroup($sales[0]['d_id']);
$distributor_balance_row         = $QStoreaccount->getBalance($sales[0]['d_id']);
// $q_total_balance                 = $QStoreaccount->getMainDistributorBalance($sales[0]['d_id']); 
//------------------------

$selectCompany = $db->select()
->from(array('p'=>'warehouse'),array('p.company_id'))
->where('id = ?',$sales[0]['warehouse_id']);
$company_id = $db->fetchOne($selectCompany);

// $remain_balance                  = ($company_id == 1) ? $distributor_balance_row['balance'] : $distributor_balance_row['balance_smartmobile'];

$credit_amount                  = $distributor_balance_row['credit_amount'];
$remain_balance                  = $distributor_balance_row['use_credit'];
$total_balance                  = $distributor_balance_row['balance'];

$distributor_balance             = ($company_id == 1) ? $distributor_balance_row['balance'] : $distributor_balance_row['balance_smartmobile'];
$this->view->distributor_balance = $distributor_balance;

$checkBalance = 0;
$checkPaymentStatus = 0; //ki?m tra cÃƒÂ³ th? cho phÃƒÂ©p payment khÃƒÂ´ng?
if ($remain_balance) {

    if ($sn_total <= $remain_balance)
    {
        $checkPaymentStatus = 1;
    } else{
        $checkPaymentStatus = 0;
    }

    //$checkBalance = $remain_balance - $sn_total;
    $checkBalance =  $total_balance - $sn_total;
} else{
    $checkBalance = -$sn_total;
}

//x? lÃƒÂ½ check payment
$this->view->need               = abs($checkBalance);
$this->view->checkPaymentStatus = $checkPaymentStatus;
$this->view->credit_amount       = $credit_amount;
$this->view->checkBalance       = $checkBalance;
$this->view->remain_balance     = $remain_balance*-1;
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
    if ($this->getRequest()->getMethod() == 'POST') {
        // echo "<pre>";
        // print_r($_POST);
        // print_r($_FILES); die;
        //$file_name_show = $_FILES['file']['name'];
        //print_r($file_name_show);
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('m' => 'market'),
            array('m.sn_ref'));
        $select->join(array('s'=>'staff'),'sales_confirm_id=s.id',array('s.firstname'));
        $select->where('m.sn = ?', $sn);
        $select->where('sales_confirm_id  is not null',1);
        $select->where('sales_confirm_date  is not null',1);
        $select->group('m.sn');



        $Check_pay_list = $db->fetchRow($select);



        if (isset($Check_pay_list) and $Check_pay_list) {
        // die($Check_pay_list['sn_ref']);
            $flashMessenger->setNamespace('error')->addMessage('Order '.$Check_pay_list['sn_ref'].' ??????????????? '.$Check_pay_list['firstname'] );
            $this->_redirect('/sales');

        }else{
            $db->beginTransaction();

            try { 

                $payment       = $this->getRequest()->getParam('payment');
                $shipping      = $this->getRequest()->getParam('shipping');
                $pay_text      = $this->getRequest()->getParam('pay_text');
                $shipping_text = $this->getRequest()->getParam('shipping_text');
                $payment_type = $this->getRequest()->getParam('payment_type',NULL);
                $payment_order = $this->getRequest()->getParam('payment_order', 0);
                $payment_bank_transfer = $this->getRequest()->getParam('payment_bank_transfer', 0);
                $payment_service = $this->getRequest()->getParam('payment_service', 0);
                $payment_servicecharge = $this->getRequest()->getParam('payment_servicecharge', 0);

                $pay_time      = $this->getRequest()->getParam('pay_time');
                $bank          = $this->getRequest()->getParam('select_bank_id', 0);
                $type          = 1;
            //$company_id    = $this->getRequest()->getParam('company_id');
                $company_id    = 1;
                $retailer_rank = $this->getRequest()->getParam('retailer_rank', NULL);

                $sn_total       = $this->getRequest()->getParam('total_out_amount', 0);
                $total_amount  = $this->getRequest()->getParam('total_amount', 0);


                if($total_amount==0)
                {
                    $payment_order=0;
                    $payment_bank_transfer=0;
                    $payment_servicecharge=0;
                    $payment_service=0;
                }                   

                $where = array();
                $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

                $data = array(
                    'pay_text' => $pay_text,
                    'shipping_text' => $shipping_text,
                );

            // get another info of distributor
                $where_payment = $QDistributor->getAdapter()->quoteInto('id = ?', $sales[0]['d_id']);
                $distributors_payment = $QDistributor->fetchRow($where_payment);


                $auto_confirm_finance = $distributors_payment->auto_confirm_finance;
                $auto_confirm_finance_warehouse = $distributors_payment->warehouse_id;
                if($auto_confirm_finance_warehouse =='73' || $auto_confirm_finance_warehouse =='62')
                {
                  $auto_confirm_finance =1;  
              }

              $date = date('Y-m-d H:i:s');

              $checkUpdateCheckMoney = 0;
            //$QCheckMoney    = new Application_Model_CheckmoneySales();

              if ($payment) {

                //$data['pay_time'] = $date;
               // $data['pay_user'] = $userStorage->id;
                $data['payment_type'] = $payment_type;

                //check money
                $QCheckmoney = new Application_Model_Checkmoney();

                //Ki?m tra don hÃƒÂ ng dÃƒÂ£ dÃƒÂ¡nh d?u payment l?n nÃƒÂ o chua?, n?u r?i thÃƒÂ¬ b? qua
                $select_sn = array();
                $select_sn[] = $QCheckmoney->getAdapter()->quoteInto('sn = ?', $sales[0]['sn']);
                $select_sn[] = $QCheckmoney->getAdapter()->quoteInto('type = ?', 2); // phÃƒÂ¢n lo?i tr? ti?n
                $check_sn_exist = $QCheckmoney->fetchRow($select_sn);


                if (!$check_sn_exist) {

                    $payment_service_val=0;
                    for($i=0;$i<count($payment_order);$i++){
                        $payment_service_val +=$payment_service[$i];
                    }

                    $sn_total = 0;
                    $intRebate = intval($QMarketProduct->getPrice($sales[0]['sn']));
                    $sn_total = $QMarket->getPrice($sales[0]['sn']) - $intRebate;

                    $note_new='Payment Order='.number_format($sn_total,2).' ?????????????????????='.number_format($payment_service_val,2);

                    //data for checkmoney transaction
                    $data_ch = array(
                        'd_id'       => $sales[0]['d_id'],
                        'payment'    => $date,
                        'pay_time'   => $date,
                        'pay_service'     => $payment_service_val,
                        'output'     => $sn_total,
                        'pay_money'  => -$sn_total,
                        'type'       => 2,
                        'sn'         => $sales[0]['sn'],
                        'user_id'    => $userStorage->id,
                        'create_by'  => $userStorage->id,
                        'create_at'  => $date,
                        'note'       => $note_new,
                        'company_id' => $company_id,
                        'sales_confirm_date'    => $date,
                        'sales_confirm_id'    => $userStorage->id,
                    );

                    if($auto_confirm_finance =='1')
                    {
                        $data_ch['finance_confirm_id'] = $userStorage->id;
                        $data_ch['finance_confirm_date'] = $date;
                    }


                    $checkUpdateCheckMoney = $QCheckmoney->insert($data_ch);

                    // update balance
                    if ($checkUpdateCheckMoney) {
                       // $QStoreaccount->updateBalance($sales[0]['d_id']);
                    } else {
                        exit("Loi khong dong bo!");
                    }

                    // Neu co uy nhiem chi
                    $payment_order_val=0;$payment_bank_transfer_val=0;$pay_service_val=0;$i=0;
                    for($i=0;$i<count($payment_order);$i++){
                        $payment_order_val +=$payment_order[$i];
                        $pay_service_val +=$pay_service[$i];
                        $payment_order_val +=$payment_order[$i];
                    }

                    if($payment_type=="CA"){
                        if ($payment_order_val >=0) {
                            $status=1;
                            if($retailer_rank ==10){
                                $status=0;
                            }
                               // $pay_money = $payment_order+$payment_bank_transfer;
                            $QCheckmoneyPaymentorder = new Application_Model_CheckmoneyPaymentorder();
                            $QCheckmoneyPaymentorder->insert(array(
                                'd_id'          => $sales[0]['d_id'],
                                'sn'            => $sales[0]['sn'],
                                'payment_order' => ($payment_order_val+$payment_order_val),
                                'pay_banktransfer' => $payment_bank_transfer_val,
                                'status'        => $status,
                                'created_at'    => $date,
                                'created_by'    => $userStorage->id,
                                'sales_confirm_date'    => $date,
                                'sales_confirm_id'    => $userStorage->id,
                            ));
                            } //End check payment order
                        }
                } //End check sn existed

            } //End check if payment

            if ($shipping) {
               // $data['shipping_yes_time'] = $date;
               // $data['shipping_yes_id'] = $userStorage->id;
            }

            $data['confirm_so'] = 1;

            $data['sales_confirm_date'] = $date;
            $data['sales_confirm_id'] = $userStorage->id;


            if($auto_confirm_finance =='1'){
                $data['pay_time'] = $date;
                $data['pay_user'] = $userStorage->id;
                $data['shipping_yes_time'] = $date;
                $data['shipping_yes_id'] = $userStorage->id;

                $data['pay_text'] = "Confirm Payment Auto by Finance";
                //$data['shipping_text'] = "Confirm Shipping Auto by Finance";
                $data['finance_confirm_id'] = $userStorage->id;
                $data['finance_confirm_date'] = $date;

            }
            //print_r($data);die;
            $QMarket->update($data, $where);

            /* ---------Add Money Check--------------- */

            if($total_amount >= 0 && $payment_type=="CA")
            {
                $payment_order_val=0;$payment_bank_transfer_val=0;$i=0;
                $payment_service_val=0;$payment_servicecharge_val=0;

                for($i=0;$i<count($payment_order);$i++){

                 if($bank[$i]==''){
                  $bank_id=0; 
              }else{
                  $bank_id=$bank[$i];
              }
              $payment_order_val =$payment_order[$i];
              $payment_bank_transfer_val =$payment_bank_transfer[$i];
              $payment_service_val =$payment_service[$i];
              $payment_servicecharge_val =$payment_servicecharge[$i];
              $pay_time_val=$pay_time[$i];
              $d_id = $sales[0]['d_id'];
              $date = date('Y-m-d H:i:s');

                    //pay_banktransfer

              $QCheckMoney    = new Application_Model_Checkmoney();
              $QStoreaccount  = new Application_Model_Storeaccount();
                    //$userStorage    = Zend_Auth::getInstance()->getStorage()->read();

                    //$pay_money = $payment_order+$payment_bank_transfer;
              $file_name_upload = '/pay_slips/'.$sn.'/'.$_FILES['file']['name'][$i];

              $note_new='PayMoney='.number_format($payment_order_val,2) .' Fee transfer='.number_format($payment_bank_transfer_val,2).' Service Charge='.number_format($payment_servicecharge_val,2).' ?????????????????????='.number_format($payment_service_val,2);

              $data = array(
                'd_id'                  => $d_id,
                'bank'                  => $bank_id,
                'pay_money'             => $payment_order_val,
                'pay_servicecharge'     => $payment_servicecharge_val,
                'pay_banktransfer'      => $payment_bank_transfer_val,
                'pay_service'           => $payment_service_val,
                'type'                  => 1,
                'pay_time'              => $pay_time_val,
                'bank_serial'           => null,
                'bank_transaction_code' => null,
                'note'                  => $note_new,
                'content'               => null,
                'company_id'            => $company_id,
                'sn'                    => $sn,
                'file_pay_slip'         => $file_name_upload,
                'user_id'               => $userStorage->id,
                'create_by'             => $userStorage->id,
                'create_at'             => $date,
                'sales_confirm_id'      => $userStorage->id,
                'sales_confirm_date'    => $date,
                'addition'              => 1
            );

              $checkUpdateCheckMoney2 = $QCheckmoney->getCheckDuplicate($sn,$file_name_upload,$payment_order_val);


              $file_name_upload = $_FILES['file']['name'][$i];

                // die;
              if ($checkUpdateCheckMoney2) {
                    // print_r($checkUpdateCheckMoney2);

                    // die;
              }else{
                if($ch_id){
                        // echo "1";
                    $old_checkmoney = $QCheckMoney->find($ch_id)->current();
                    $data['user_id'] = $userStorage->id;
                    $data['updated_at'] = $date;
                    $where = $db->quoteInto('id = ?',$ch_id);
                    $QCheckMoney->update($data,$where); 

                }else{
                        // echo "2";
                    $data['bank'] = $bank_id;
                    $data['create_by'] = $userStorage->id;
                    $data['create_at'] = $date;
                    $QCheckMoney->insert($data);
                }



                    /*
                    if($data['d_id'] >= 0){  
                        if($ch_id){//Tru?ng h?p update
                            if($data['d_id'] == $old_checkmoney['d_id']){
                                $QStoreaccount->updateBalance( $data['d_id'] );
                            }else{
                                $QStoreaccount->updateBalance( $data['d_id'] );
                                if( intval( $old_checkmoney['d_id'] ) >= 0 ){
                                    $QStoreaccount->updateBalance($old_checkmoney['d_id']);
                                }
                            }
                        }else{//Tru?ng h?p thÃƒÂªm m?i.
                            $QStoreaccount->updateBalance($data['d_id']);
                        }           
                    }
                    */
                }

                 //print_r($data_money);die;
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
                                        $this->_redirect('/sales/sales-confirm-order?sn=' . $sn);
                                    }
                                }else{
                                 $upload->receive($file);
                             }                                                     
                         }
                         $r+=1;
                     }
                 }    
                 /*-------------------End File Pay Slip Upload--------------------------*/
             }
             /* ------------------------ */
             if($total_amount >= 0 && $payment_type=="CC")
             {
                $payment_order_val=0;$payment_bank_transfer_val=0;$i=0;
                $payment_service_val=0;$payment_servicecharge_val=0;

                for($i=0;$i<count($payment_order);$i++){

                 if($bank[$i]==''){
                  $bank_id=0; 
              }else{
                  $bank_id=$bank[$i];
              }
              $payment_order_val =$payment_order[$i];
              $payment_bank_transfer_val =$payment_bank_transfer[$i];
              $payment_service_val =$payment_service[$i];
              $payment_servicecharge_val =$payment_servicecharge[$i];
              $pay_time_val=$pay_time[$i];
              $d_id = $sales[0]['d_id'];
              $date = date('Y-m-d H:i:s');

                    //pay_banktransfer

              $QCheckMoney    = new Application_Model_Checkmoney();
              $QStoreaccount  = new Application_Model_Storeaccount();
                    //$userStorage    = Zend_Auth::getInstance()->getStorage()->read();

                    //$pay_money = $payment_order+$payment_bank_transfer;
              echo    $file_name_upload = '/pay_slips/'.$sn.'/'.$_FILES['file']['name'][$i];

              $note_new='PayMoney='.number_format($payment_order_val,2) .' Fee transfer='.number_format($payment_bank_transfer_val,2).' Service Charge='.number_format($payment_servicecharge_val,2).' ?????????????????????='.number_format($payment_service_val,2);

              $data = array(
                'd_id'                  => $d_id,
                'bank'                  => $bank_id,
                'pay_money'             => $payment_order_val,
                'pay_servicecharge'     => $payment_servicecharge_val,
                'pay_banktransfer'      => $payment_bank_transfer_val,
                'pay_service'           => $payment_service_val,
                'type'                  => 1,
                'pay_time'              => $pay_time_val,
                'bank_serial'           => null,
                'bank_transaction_code' => null,
                'note'                  => $note_new,
                'content'               => null,
                'company_id'            => $company_id,
                'sn'                    => $sn,
                'file_pay_slip'         => $file_name_upload,
                'user_id'               => $userStorage->id,
                'create_by'             => $userStorage->id,
                'create_at'             => $date,
                'sales_confirm_id'      => $userStorage->id,
                'sales_confirm_date'    => $date,
                'addition'              => 1
            );

              $file_name_upload = $_FILES['file']['name'][$i];
                    // die;
              if($ch_id){
                $old_checkmoney = $QCheckMoney->find($ch_id)->current();
                $data['user_id'] = $userStorage->id;
                $data['updated_at'] = $date;
                $where = $db->quoteInto('id = ?',$ch_id);
                $QCheckMoney->update($data,$where); 

            }else{
                $data['bank'] = $bank_id;
                $data['create_by'] = $userStorage->id;
                $data['create_at'] = $date;
                $QCheckMoney->insert($data);
            }

                    /*
                    if($data['d_id'] >= 0){  
                        if($ch_id){//Tru?ng h?p update
                            if($data['d_id'] == $old_checkmoney['d_id']){
                                $QStoreaccount->updateBalance( $data['d_id'] );
                            }else{
                                $QStoreaccount->updateBalance( $data['d_id'] );
                                if( intval( $old_checkmoney['d_id'] ) >= 0 ){
                                    $QStoreaccount->updateBalance($old_checkmoney['d_id']);
                                }
                            }
                        }else{//Tru?ng h?p thÃƒÂªm m?i.
                            $QStoreaccount->updateBalance($data['d_id']);
                        }           
                    }
                    */
                }

                 //print_r($data_money);die;
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
                                        $this->_redirect('/sales/sales-confirm-order?sn=' . $sn);
                                    }
                                }else{
                                 $upload->receive($file);
                             }                                                     
                         }
                         $r+=1;
                     }

                     /*-------------------End File Pay Slip Upload--------------------------*/
                 }
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
                        $this->_redirect('/sales/sales-confirm-order?sn=' . $sn);
                    }
                }

                $db->commit();
                $flashMessenger->setNamespace('success')->addMessage('Done!');
                $this->_redirect('/sales');
            }
            catch (exception $e) {
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!' .
                    $e->getMessage());
                $this->_redirect('/sales/sales-confirm-order?sn=' . $sn);
            }
            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $this->_redirect('/sales');
        }

    } //End if check post

    $where = array();
    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

    $sales = $QMarket->fetchAll($where);
//print_r($sales);

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
    $warehouses    = $QWarehouse->get_cache();

    $Credit_Note = $QMarket->fetchCredit_Note($sn);

    $deposit_list = $QMarket->fetchDeposit($sn);

    $QStoreAccount  = new Application_Model_Storeaccount();


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

    //get retailer : rank
                $data[$k]['retailer_rank'] = isset($distributors_info->rank) ? $distributors_info->rank : '';

    //get retailer : rank
                $data[$k]['show_cash_menu'] = $show_cash_menu;

    //get retailer : credit amount
                $q_store_account  = $QStoreAccount->getMainDistributorBalance($sale->d_id);

    //print_r($distributors_info);

    //$data[$k]['credit_amount'] = isset($distributors_info->credit_amount) ? $distributors_info->credit_amount : '';

                $credit_amount = isset($q_store_account['credit_amount']) ? $q_store_account['credit_amount'] : '0';

                $data[$k]['credit_amount'] = $credit_amount;



    //print_r($sales[0]['credit_amount']);

    //$credit_amount = isset($distributors_info->credit_amount) ? $distributors_info->credit_amount : '0';

                $this->view->distributor_total_balance = ($credit_amount - ($distributor_balance));


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
                                $data[$k]['deposit_list'] = $deposit_list;

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
                            $where = $QBank->getAdapter()->quoteInto("type_payment in('TR','CC')",null);
                            $banks = $QBank->fetchAll($where,'name asc');

                            $where = $QBank->getAdapter()->quoteInto('type_payment = ?','CC');
                            $banksCredit = $QBank->fetchAll($where,'name asc');
                            $this->view->banks = $banks;
                            $this->view->banksCredit = $banksCredit;

                            $this->view->detailBVG = $detailBVG;
                            $this->view->detailDiscount = $detailDiscount;

                            $this->view->sales = $data;

//print_r($data);
                        }
                    }

                    function saveMassDistributorOnlineAction()
                    {
                        $this->_helper->layout->disableLayout();
                        $warehouse_online = $this->getRequest()->getParam('warehouse_online');

                        switch ($_POST['warehouse_online']) {
                            case '1':
                            $sales_ch = 'LA';
                            $warehouse_id = '62';
                            $add = '(Lazada)';
                            $finance_group="B02_Lazada";
                            break;
                            case '2':
                            $sales_ch = 'ST';
                            $warehouse_id = '73';
                            $add = '(11Street)';
                            $finance_group="B03_11ST";
                            break;
                            case '3':
                            $sales_ch = 'SP';
                            $warehouse_id = '115';
                            $add = '(Shopee)';
                            $finance_group="B08_Shopee";
                            break;
                            case '4':
                            $sales_ch = 'JD';
                            $warehouse_id = '125';
                            $add = '(JD)';
                            $finance_group="B09_JD";
                            break;
                            case '5':
                            $sales_ch = 'OS';
                            $warehouse_id = '164';
                            $add = '(OS)';
                            $finance_group="B11_ONLINEOPPO";
                            break;

                            default:
                            echo 'Error! Data.';
                            exit();
                            break;
                        }

// if ($_POST['warehouse_online'] =='1') {
//     $sales_ch = 'LA';
//     $warehouse_id = '62';
//     $add = '(Lazada)';
//     $finance_group="B02_Lazada";
// }else{
//     $sales_ch = 'ST';
//     $warehouse_id = '73';
//     $add = '(11Street)';
//     $finance_group="B03_11ST";
// }

                        $QDistributor = new Application_Model_Distributor();
                        $QDistributorMapping = new Application_Model_DistributorMapping();

                        if ($this->getRequest()->getMethod() == 'POST') {
                            define('MASS_BVG_LIST_ROW_START', 2);
                            define('MASS_BVG_LIST_COL_CUSTOMER', 0);
                            define('MASS_BVG_LIST_COL_ADDESS', 1);
                            define('MASS_BVG_LIST_COL_POST_CODE', 2);
                            define('MASS_BVG_LIST_COL_PHONE_NUMBER', 3);


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


            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            $QFileLog = new Application_Model_FileUploadLog();

            $data = array(
                'staff_id' => $userStorage->id,
                'folder' => $uniqid,
                'filename' => $new_name,
                'type' => 'mass BVG upload',
                'real_file_name' => $filename . '.' . $extension,
                'uploaded_at' => time(),
            );

            $log_id = $QFileLog->insert($data);


            $number_of_order = 0;
            $error_list = array();
            $success_list = array();
            $listBvgByProduct = array();



            $QImei    = new Application_Model_Imei();
            $QBvgImei = new Application_Model_BvgImei();
            $QBvgProduct = new Application_Model_BvgProduct();


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

        $data_sn=null;
        $sn = date('YmdHis') . substr(microtime(), 2, 1);

        for ($i = MASS_BVG_LIST_ROW_START; $i <= $highestRow; $i++) {

            $customer = trim($objWorksheet
                ->getCellByColumnAndRow(MASS_BVG_LIST_COL_CUSTOMER, $i)
                ->getValue());

            $address = trim($objWorksheet
                ->getCellByColumnAndRow(MASS_BVG_LIST_COL_ADDESS, $i)
                ->getValue());

            $post_code = trim($objWorksheet
                ->getCellByColumnAndRow(MASS_BVG_LIST_COL_POST_CODE, $i)
                ->getValue());

            $phone_number = trim($objWorksheet
                ->getCellByColumnAndRow(MASS_BVG_LIST_COL_PHONE_NUMBER, $i)
                ->getValue());

            
            $chk_customer = $this->check_customer($customer);
            // print_r($chk_customer);die;
            if(is_array($chk_customer)>0){
                $data_error['name'] = $customer;
                $data_error['address'] = $address;
                $data_error['post_code'] = $post_code;
                $data_error['phone_number'] = $phone_number;
                $data_error['message'] = "?????????????????????????";
                $error_list[] = $data_error;
            }else{


                $data = array(
                    'title'         => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $customer)),
                    'name'          => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $customer)),
                    'tel'           => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $phone_number)),
                    'warehouse_id'  => intval($warehouse_id),
                    'region'        => intval('5902'),
                    'district'      => intval('5903'),
                    'add'           => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $address.' '.$post_code)),
                    'add_tax'       => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $add.' '.$address.' '.$post_code)),
                    'admin'         => 0,
                    'rank'          => intval('3'),
                    'unames'        => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $customer)),
                    'mst_sn'        => '',
                    'branch_no'     => '00000',
                    'credit_amount' => '0.00',
                    'credit_type'   => 6,
                    'parent'        => 0,

                    'retailer_type' => 1,
                    'is_ka'         => 0,
                    'is_internal'   => 0,
                    'credit_status' => 0,
                    'finance_group' => $finance_group,
                    'activate'      => 1
                );


                $add_time = date('Y-m-d H:i:s');
                $data['add_time'] = $add_time;
                $data['create_by'] = $userStorage->id;
                $data['create_date'] = $add_time;
                $store_code = $QDistributor->getDistributorCode($db,$sales_ch);
                $data['store_code'] = $store_code;
                $data['sales_ch'] = $sales_ch;
                $data['group_id'] = 5;
              //  $result = $QDistributorMapping->get_insert_inport_distributor($data);
                $result = $QDistributor->insert($data);
            }

            //$data['dealer_name'] = $dealer_name;
            $status = $result['code'];
            if ($result) {
                $success_list[] = $data;                           
            } else {
               // $data['message'] = $result['message'];
                // $error_list[] = $data;
            }

            $number_of_order++;
            $percent = round($number_of_order * 100 / $total_order_row, 1);
            $progress->flush($percent);
        }

        //print_r($data_sn);die;
        // echo '<pre>';
        
        // echo "<br/>".count($error_list);

        $data = array(
            'total' => $total_order_row,
            'failed' => count($error_list),
            'succeed' => $total_order_row - count($error_list),
        );
        
        // xu?t file excel cÃƒÂ¡c order l?i
        if (is_array($error_list) && count($error_list) > 0) 
        {

            $data['error_file_name'] = $d_id.'-'.'FAILED-' . md5(microtime(true) . uniqid('', true)) . '.' . $extension;
            // xu?t excel @@
            //
            //$error_file_name = date('YmdHis') . substr(microtime(), 2, 4);
            //$data['error_file_name'] = 'FAILED-' .$error_file_name.'.' . $extension;

            $objPHPExcel_out = new PHPExcel();
            $objPHPExcel_out->createSheet();
            $objWorksheet_out = $objPHPExcel_out->getActiveSheet();
            //
            // get product list
            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CUSTOMER, 1, 'CUSTOMER');
            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ADDESS, 1, 'ADDESS');
            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_POST_CODE, 1, 'POST_CODE');
            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_PHONE_NUMBER, 1, 'PHONE_NUMBER');


            // cÃƒÂ¡c dÃƒÂ²ng l?i
            $i = 2;
            foreach ($error_list as $key => $row) {

                $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CUSTOMER, $i, $row['name']);
                $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ADDESS, $i, $row['address']);
                $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_POST_CODE, $i, $row['post_code']);
                $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_PHONE_NUMBER, $i, $row['phone_number']);
                $i++;
            }

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
        // END IF // xu?t file excel cÃƒÂ¡c order l?i

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
        $progress->flush(100);
    } // end of Try
    catch (Exception $e) {
        $db->rollback();
        $this->view->error = $e->getMessage();
        $progress->flush(0);
    }
    
}// end of check file

// unlink(APPLICATION_PATH . '/../public/files/mou/lock');

// $progress->flush(0);
} // end of check POST
}

function saveMassDistributorOnlineImportAction()
{ 
    $this->_helper->layout->disableLayout();
    $warehouse_online = $this->getRequest()->getParam('warehouse_online');

    switch ($_POST['warehouse_online']) {
        case '1':
        $sales_ch = 'LA';
        $warehouse_id = '62';
        $add = '(Lazada)';
        $finance_group="B02_Lazada";
        break;
        case '2':
        $sales_ch = 'ST';
        $warehouse_id = '73';
        $add = '(11Street)';
        $finance_group="B03_11ST";
        break;
        case '3':
        $sales_ch = 'SP';
        $warehouse_id = '115';
        $add = '(Shopee)';
        $finance_group="B08_Shopee";
        break;
        case '4':
        $sales_ch = 'JD';
        $warehouse_id = '125';
        $add = '(JD)';
        $finance_group="B09_JD";
        break;
        case '5':
        $sales_ch = 'OS';
        $warehouse_id = '164';
        $add = '(OS)';
        $finance_group="B11_ONLINEOPPO";
        break;

        default:
        echo 'Error! Data.';
        exit();
        break;
    }

// if ($_POST['warehouse_online'] =='1') {
//     $sales_ch = 'LA';
//     $warehouse_id = '62';
//     $add = '(Lazada)';
//     $finance_group="B02_Lazada";
// }else{
//     $sales_ch = 'ST';
//     $warehouse_id = '73';
//     $add = '(11Street)';
//     $finance_group="B03_11ST";
// }

    $QDistributor = new Application_Model_Distributor();
    $QDistributorMapping = new Application_Model_DistributorMapping();
    $QDistributorMap_online = new Application_Model_DistributorMapOnline();

    if ($this->getRequest()->getMethod() == 'POST') {
        define('MASS_BVG_LIST_ROW_START', 2);


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


            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            $QFileLog = new Application_Model_FileUploadLog();

            $data = array(
                'staff_id' => $userStorage->id,
                'folder' => $uniqid,
                'filename' => $new_name,
                'type' => 'mass BVG upload',
                'real_file_name' => $filename . '.' . $extension,
                'uploaded_at' => time(),
            );

            $log_id = $QFileLog->insert($data);


            $number_of_order = 0;
            $error_list = array();
            $success_list = array();
            $listBvgByProduct = array();



            $QImei    = new Application_Model_Imei();
            $QBvgImei = new Application_Model_BvgImei();
            $QBvgProduct = new Application_Model_BvgProduct();


    // $file = fopen($uploaded_dir. DIRECTORY_SEPARATOR .$new_name,"r");

    // $data_csv = [];
    // while(! feof($file)){

    //       $line_text = fgets($file);

    //       if($line_text != ''){
    //        $t = explode(';',$line_text);

    //        $data_csv[] = $t;
    //       }
    //        $line_no++;
    // }

    // // print_r(count($data_csv,COUNT_RECURSIVE)); die;
    // if(count($data_csv,COUNT_RECURSIVE) <100){
    //     $error_from_csv = "file in CSV format (Please select file CSV format Semicolon (;) )";
    //     $this->view->error = $error_from_csv;
    //     return;
    // }

    // echo'<pre>';
    // print_r($data_csv); die;
    // echo'</pre>';

    // $highestRow = count($data_csv);
    // $total_order_row = $highestRow - MASS_BVG_LIST_ROW_START + 1;

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
//---------- file excel array
$headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null,true,true,true);
$headingsArray = $headingsArray[1];

$r = -1;
$data_excel = array();
for($row = 2; $row <= $highestRow; ++$row){
   $dataRow = $objWorksheet->rangeToArray('A1:'.$highestColumn.$row,null,true,true,true);
   if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')){
    ++$r;
    foreach ($headingsArray as $columkey => $columHeading) {
        $data_excel[$r][$columHeading] = $dataRow[$row][$columkey];
    }
}

}
    // echo'<pre>';
    // print_r($data_excel); die;
    // echo'</pre>';
$status=1; $d_id='';                    
$data_sn=null;
$sn = date('YmdHis') . substr(microtime(), 2, 1);  

foreach ($data_excel as $key => $value) 
{ 

    // if($key == 0){ //???? array ?????? ?????? array ??????
    //     continue;
    // }
    $customer = array();
    $address  = array();
    $post_code = array();
    $phone_number = array();
    $order_number = array();

    $customer       = trim($value["Customer"]);
    $address        = trim($value["Address"]);
    $post_code      = trim($value["Post Code"]);
    $phone_number   = trim($value["Tel. no."]);
    $order_number   = trim($value["Order Number"]);
    // echo $customer; die;

    $chk_customer = $this->check_customer($customer);
            // print_r($chk_customer);die;
    if(is_array($chk_customer)>0){
        $data_error['name'] = $customer;
        $data_error['address'] = $address;
        $data_error['post_code'] = $post_code;
        $data_error['phone_number'] = $phone_number;
        $data_error['message'] = "?????????????????????????";
        $error_list[] = $data_error;
    }else{

        $data = array(
            'title'  => trim(preg_replace('/[[space:]]+/', ' ', $customer)),
                        // 'name'          => trim(preg_replace('/[[space:]]+/', ' ', $customer)),
            'name'          => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $customer)),
            'tel'           => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $phone_number)),
            'warehouse_id'  => intval($warehouse_id),
            'region'        => intval('5902'),
            'district'      => intval('5903'),
            'add'           => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $address.' '.$post_code)),
            'add_tax'       => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $add.' '.$address.' '.$post_code)),
            'admin'         => 0,
            'rank'          => intval('3'),
            'unames'        => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $customer)),
            'mst_sn'        => '',
            'branch_no'     => '00000',
            'credit_amount' => '0.00',
            'credit_type'   => 6,
            'parent'        => 0,
            'retailer_type' => 1,
            'is_ka'         => 0,
            'is_internal'   => 0,
            'credit_status' => 0,
            'finance_group' => $finance_group,
            'activate'      => 1
        );


        $add_time = date('Y-m-d H:i:s');
        $data['add_time'] = $add_time;
        $data['create_by'] = $userStorage->id;
        $data['create_date'] = $add_time;
        $store_code = $QDistributor->getDistributorCode($db,$sales_ch);
        $data['store_code'] = $store_code;
        $data['sales_ch'] = $sales_ch;
        $data['group_id'] = 5;

        $result = $QDistributor->insert($data);

        $data_map = array(
            'distributor_id_online' => $result,
            'order_number' => $order_number,
            'address'     => $address,
            'create_date'  => date('Y-m-d H:i:s'),
            'create_by'    => $userStorage->id,
        );

        $QDistributorMap_online->insert($data_map);

    }
            //check order ??? ?? DistributorMap_online
    $check_ordernumber=$this->check_ordernumber($order_number);
    if($check_ordernumber > 0){

    }else{

                //---inser ????create?????????????????------------------
        $where = array();
        $where[] = $QDistributor->getAdapter()->quoteInto('title =?', trim($customer));
        $where[] = $QDistributor->getAdapter()->quoteInto('del =?',0);

        $insert_data2 = $QDistributor->fetchRow($where);

                // print_r($insert_data2); die;
        if(isset($insert_data2)){

            $date_map = date('Y-m-d');

            if($insert_data2['add_time'] > $date_map){

                $data2 = array(
                    'title'  => trim(preg_replace('/[[space:]]+/', ' ', $customer.'.')),
                            // 'name'   => trim(preg_replace('/[[space:]]+/', ' ', $customer.'(1)')),
                    'name'   => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $customer.'.')),
                    'tel'    => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $phone_number)),
                    'warehouse_id'  => intval($warehouse_id),
                    'region'        => intval('5902'),
                    'district'      => intval('5903'),
                    'add'           => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $address.' '.$post_code)),
                    'add_tax'       => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $add.' '.$address.' '.$post_code)),
                    'admin'         => 0,
                    'rank'          => intval('3'),
                    'unames'        => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $customer.'.')),
                    'mst_sn'        => '',
                    'branch_no'     => '00000',
                    'credit_amount' => '0.00',
                    'credit_type'   => 6,
                    'parent'        => 0,
                    'retailer_type' => 1,
                    'is_ka'         => 0,
                    'is_internal'   => 0,
                    'credit_status' => 0,
                    'finance_group' => $finance_group,
                    'activate'      => 1
                );

                $add_time = date('Y-m-d H:i:s');
                $data2['add_time'] = $add_time;
                $data2['create_by'] = $userStorage->id;
                $data2['create_date'] = $add_time;
                $store_code = $QDistributor->getDistributorCode($db,$sales_ch);
                $data2['store_code'] = $store_code;
                $data2['sales_ch'] = $sales_ch;
                $data2['group_id'] = 5;

                $result = $QDistributor->insert($data2);

                $data_map = array(
                    'distributor_id_online' => $result,
                    'order_number' => $order_number,
                    'address'      => $address,
                    'create_date'  => date('Y-m-d H:i:s'),
                    'create_by'    => $userStorage->id,
                );

                $QDistributorMap_online->insert($data_map);


            }else{

                $check_distributor = $this->check_address($customer,$address,$post_code);

                if($check_distributor){

                    $data_map = array(
                        'distributor_id_online' =>
                        $check_distributor['id'],
                        'order_number' => $order_number,
                        'address'      => $address,
                        'create_date'  => $check_distributor['add_time'],
                        'update_date'  => date('Y-m-d H:i:s'),
                        'update_by'    => $userStorage->id,
                    );
                    $QDistributorMap_online->insert($data_map);

                }else{

                    $data3 = array(
                        'title'  => trim(preg_replace('/[[space:]]+/', ' ', $customer)),
                            // 'name'   => trim(preg_replace('/[[space:]]+/', ' ', $customer.'(1)')),
                        'name'   => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $customer)),
                        'tel'    => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $phone_number)),
                        'warehouse_id'  => intval($warehouse_id),
                        'region'        => intval('5902'),
                        'district'      => intval('5903'),
                        'add'           => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $address.' '.$post_code)),
                        'add_tax'       => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $add.' '.$address.' '.$post_code)),
                        'admin'         => 0,
                        'rank'          => intval('3'),
                        'unames'        => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $customer.'.')),
                        'mst_sn'        => '',
                        'branch_no'     => '00000',
                        'credit_amount' => '0.00',
                        'credit_type'   => 6,
                        'parent'        => 0,
                        'retailer_type' => 1,
                        'is_ka'         => 0,
                        'is_internal'   => 0,
                        'credit_status' => 0,
                        'finance_group' => $finance_group,
                        'activate'      => 1
                    );

                    $add_time = date('Y-m-d H:i:s');
                    $data3['add_time'] = $add_time;
                    $data3['create_by'] = $userStorage->id;
                    $data3['create_date'] = $add_time;
                    $store_code = $QDistributor->getDistributorCode($db,$sales_ch);
                    $data3['store_code'] = $store_code;
                    $data3['sales_ch'] = $sales_ch;
                    $data3['group_id'] = 5;

                    $result = $QDistributor->insert($data3);

                    $data_map = array(
                        'distributor_id_online' => $result,
                        'order_number' => $order_number,
                        'address'      => $address,
                        'create_date'  => date('Y-m-d H:i:s'),
                        'create_by'    => $userStorage->id,
                    );
                    $QDistributorMap_online->insert($data_map);
                }



                        // //update ???????????????
                        // $where = $QDistributorMap_online->getAdapter()->quoteInto('address =?', preg_replace('/\s+/', '', $address));
                        // $update_distrubutor = $QDistributorMap_online->fetchRow($where);

                        //     if(isset($update_distrubutor)){

                        //         $data_map2 = array(
                        //             // 'distributor_id_online' => $result,
                        //             'address'      => preg_replace('/\s+/', '', $address),
                        //             'order_number' => $order_number,
                        //             'update_date'  => date('Y-m-d H:i:s'),
                        //             'update_by'    => $userStorage->id,
                        //         ); 
                        //         $QDistributorMap_online->insert($data_map2,$where);

                        //     }else{
                        //     // ???? distributor ???????? order number?? QDistributorMap_online
                        //         $data3 = array(
                        //         'title'  => trim(preg_replace('/[[space:]]+/', ' ', $customer.'.')),
                        //         // 'name'   => trim(preg_replace('/[[space:]]+/', ' ', $customer.'(1)')),
                        //         'name'   => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $customer.'.')),
                        //         'tel'    => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $phone_number)),
                        //         'warehouse_id'  => intval($warehouse_id),
                        //         'region'        => intval('5902'),
                        //         'district'      => intval('5903'),
                        //         'add'           => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $address.' '.$post_code)),
                        //         'add_tax'       => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $add.' '.$address.' '.$post_code)),
                        //         'admin'         => 0,
                        //         'rank'          => intval('3'),
                        //         'unames'        => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $customer.'.')),
                        //         'mst_sn'        => '',
                        //         'branch_no'     => '00000',
                        //         'credit_amount' => '0.00',
                        //         'credit_type'   => 6,
                        //         'parent'        => 0,
                        //         'retailer_type' => 1,
                        //         'is_ka'         => 0,
                        //         'is_internal'   => 0,
                        //         'credit_status' => 0,
                        //         'finance_group' => $finance_group,
                        //         'activate'      => 1
                        //         );

                        //         $add_time = date('Y-m-d H:i:s');
                        //         $data3['add_time'] = $add_time;
                        //         $data3['create_by'] = $userStorage->id;
                        //         $data3['create_date'] = $add_time;
                        //         $store_code = $QDistributor->getDistributorCode($db,$sales_ch);
                        //         $data3['store_code'] = $store_code;
                        //         $data3['sales_ch'] = $sales_ch;
                        //         $data3['group_id'] = 5;

                        //         $result = $QDistributor->insert($data3);

                        //         $data_map = array(
                        //                         'distributor_id_online' => $result,
                        //                         'order_number' => $order_number,
                        //                         'address'      => preg_replace('/\s+/', '', $address),
                        //                         'create_date'  => date('Y-m-d H:i:s'),
                        //                         'create_by'    => $userStorage->id,
                        //                   );
                        //         $QDistributorMap_online->insert($data_map);
                        //     }
            }

        }
            }// else check order

            // print_r($result);die;
            //$data['dealer_name'] = $dealer_name;
            $status = $result['code'];
            if ($result) {
                $success_list[] = $data;                           
            } else {
               // $data['message'] = $result['message'];
                // $error_list[] = $data;
            }

            $number_of_order++;
            $percent = round($number_of_order * 100 / $total_order_row, 1);
            $progress->flush($percent);
        }

        //print_r($data_sn);die;
        // echo '<pre>';
        
        // echo "<br/>".count($error_list);

        $data = array(
            'total' => $total_order_row,
            'failed' => count($error_list),
            'succeed' => $total_order_row - count($error_list),
        );

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
        $progress->flush(100);
    } // end of Try
    catch (Exception $e) {
        $db->rollback();
        $this->view->error = $e->getMessage();
        $progress->flush(0);
    }
    
}// end of check file

// unlink(APPLICATION_PATH . '/../public/files/mou/lock');

// $progress->flush(0);
} // end of check POST
}

function check_customer($customer)
{ 
    $db = Zend_Registry::get('db');
    $select = $db->select()
    ->from(array('s'=> 'distributor'),array('s.*'))

    ->where('s.title = ?',$customer);

// echo $select; die;
    $total = $db->fetchRow($select);
    return $total;
}
function check_customer2($customer,$phone)
{ 
    $db = Zend_Registry::get('db');
    $select = $db->select()
    ->from(array('d'=> 'distributor'),array('d.*'))
    ->where('d.title = ?' , $customer )
    ->where('d.tel LIKE ?' , '%'.$phone.'%');

// echo $select; die;
    $total = $db->fetchRow($select);
    return $total;
}

function check_ordernumber($order_number)
{
    $db = Zend_Registry::get('db');
    $select = $db->select()
    ->from(array('s' => 'distributor_mapping_online'),array('s.*'))
    ->where('s.order_number = ?',$order_number);

    $total = $db->fetchRow($select);
    return $total;
}

public function listDistributorForCreateAction(){
    $this->_helper->layout->disableLayout();
// $this->_helper->viewRenderer->setNoRender(true);
    $QDistributor               = new Application_Model_Distributor();


    $rank                       = $this->getRequest()->getParam('rank_id');

    $distributors                  = $QDistributor->listDistributorForCreate($rank);
    $this->view->distributors      = $distributors;


}


// Add By Khuan //

public function listStoreForCreateAction(){

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $distributor_id     = $this->getRequest()->getParam('distributor_id');


    $QStore = new Application_Model_Store();
    $QDistributor = new Application_Model_Distributor();
    $QWarehouse = new Application_Model_Warehouse();

    $distributorRowSet = $QDistributor->find($distributor_id);


    if($distributorRowSet[0]['agent_status'] == 1) {

        $where = array();
        $where[] = $QWarehouse->getAdapter()->quoteInto('id =?',$distributorRowSet[0]['agent_warehouse_id']);

        echo json_encode($QWarehouse->fetchAll($where, 'name')->toArray());
    }else{

        $where = array();
        $where[] = $QStore->getAdapter()->quoteInto('d_id =?', $distributor_id);
        $where[] = $QStore->getAdapter()->quoteInto('status =?',1);

        echo json_encode($QStore->fetchAll($where, 'name')->toArray());
        exit;
    }

}

public function getSuppyerWarehouseIdAction(){
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $distributor_id     = $this->getRequest()->getParam('distributor_id');
    $store_id   = $this->getRequest()->getParam('store_id');

    $QDistributor = new Application_Model_Distributor();
    $distributorRowSet = $QDistributor->find($distributor_id);

    // print_r($distributorRowSet[0]['warehouse_id']); exit;

    echo json_encode($distributorRowSet->toArray());
    exit;
}

public function listDistributorForCreateNewAction(){

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $distributor_type   = $this->getRequest()->getParam('distributor_type');

    $QDistributor = new Application_Model_Distributor();
    $QStaff = new Application_Model_Staff();
    $QRegionalMarket = new Application_Model_RegionalMarket();
    $QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    if(in_array($userStorage->group_id,array(95,109,106))){

        $where_staff = $QStaff->getAdapter()->quoteInto('id =?',$userStorage->id);
        $staffs = $QStaff->fetchAll($where_staff);

        $text = explode(",",$staffs[0]['area_id']);

        $where_regional = $QRegionalMarket->getAdapter()->quoteInto('area_id in (?)',$text);
        $regional_data = $QRegionalMarket->fetchAll($where_regional);

        foreach($regional_data as $key => $value) {
            $proviece[$key] = $value['id'];

        }

        $where = array();
        $where[] = $QDistributor->getAdapter()->quoteInto('distributor_type =?', $distributor_type);
        $where[] = $QDistributor->getAdapter()->quoteInto('region IN (?)', $proviece);
        $where[] = $QDistributor->getAdapter()->quoteInto('status =?',1);

        echo json_encode($QDistributor->fetchAll($where, 'name')->toArray());
        exit;


    }elseif (in_array($userStorage->group_id,array(98,107))){

        $where_user = $QWarehouseGroupUser->getAdapter()->quoteInto('user_id =?',$userStorage->id);
        $warehouse_group = $QWarehouseGroupUser->fetchAll($where_user);

        $text = explode(",",$warehouse_group[0]['warehouse_id']);

        $where = array();
        $where[] = $QDistributor->getAdapter()->quoteInto('distributor_type =?', $distributor_type);
        $where[] = $QDistributor->getAdapter()->quoteInto('warehouse_id IN (?)', $text);
        $where[] = $QDistributor->getAdapter()->quoteInto('status =?',1);

        echo json_encode($QDistributor->fetchAll($where, 'name')->toArray());
        exit;


    }else{


        $where = array();
        $where[] = $QDistributor->getAdapter()->quoteInto('distributor_type =?', $distributor_type);
        $where[] = $QDistributor->getAdapter()->quoteInto('status =?',1);

        echo json_encode($QDistributor->fetchAll($where, 'name')->toArray());
        exit;

    }

}

public function getDeliveryAddressAction(){
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $store_id   = $this->getRequest()->getParam('store_id');
    $distributor_id     = $this->getRequest()->getParam('distributor_id');

    $QStore = new Application_Model_Store();
    $QDistributor = new Application_Model_Distributor();
    $QWarehouse = new Application_Model_Warehouse();
    $QFinanceClient = new Application_Model_FinanceClient();

    $distributorRowSet = $QDistributor->find($distributor_id);

    if($distributorRowSet[0]['agent_status'] == 1){

        $where = array();
        $where[] = $QWarehouse->getAdapter()->quoteInto('id =?',$distributorRowSet[0]['agent_warehouse_id']);
        $where[] = $QWarehouse->getAdapter()->quoteInto('status =?',1);
        $warehouse = $QWarehouse->fetchAll($where);

        $where_dis = $QDistributor->getAdapter()->quoteInto('id =?',$distributor_id);
        $distributor = $QDistributor->fetchRow($where_dis);

        $where_sp = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$distributor->warehouse_id);
        $distributor_superi = $QDistributor->fetchRow($where_sp);

        $where_fc = array();
        $where_fc[] = $QFinanceClient->getAdapter()->quoteInto('distributor_y_id =?',$distributor_id);
        $where_fc[] = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$distributor_superi->id);
        $finance_client = $QFinanceClient->fetchAll($where_fc);


        echo json_encode(array('warehouse' => $warehouse->toArray(),'finance_client' => $finance_client->toArray()));

    }else{

        $where = array();
        $where[] = $QStore->getAdapter()->quoteInto('id =?',$store_id);
        $where[] = $QStore->getAdapter()->quoteInto("status =?",1);
        $store = $QStore->fetchAll($where);

        $where_dis = $QDistributor->getAdapter()->quoteInto('id =?',$distributor_id);
        $distributor = $QDistributor->fetchRow($where_dis);

        $where_sp = $QDistributor->getAdapter()->quoteInto('agent_warehouse_id =?',$distributor->warehouse_id);
        $distributor_superi = $QDistributor->fetchRow($where_sp);

        $where_fc = array();
        $where_fc[] = $QFinanceClient->getAdapter()->quoteInto('distributor_y_id =?',$distributor_id);
        $where_fc[] = $QFinanceClient->getAdapter()->quoteInto('distributor_m_id =?',$distributor_superi->id);
        $finance_client = $QFinanceClient->fetchAll($where_fc);

        echo json_encode(array('store' => $store->toArray(),'finance_client' => $finance_client->toArray()));
        exit;
    }
}

public function getDistributorSuppyAction(){
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $store_id   = $this->getRequest()->getParam('store_id');
    $distributor_id     = $this->getRequest()->getParam('distributor_id');

    $QDistributor = new Application_Model_Distributor();
    $QWarehouse = new Application_Model_Warehouse();

    $distributorRowSet = $QDistributor->find($distributor_id);
    $warehouse = $QWarehouse->find($distributorRowSet[0]['warehouse_id']);

    echo json_encode($warehouse->toArray());

}

// End //

function check_address($customer,$address,$post_code)
{   
    $add_post = array();
    array_push($add_post, $address.' '.$post_code);

    $db = Zend_Registry::get('db');
    $select = $db->select()
    ->from(array('s' => 'distributor'),array('s.*'));
    $select->where('s.title = ?',trim($customer));
    $select->where('s.add =?',$add_post);

    $total = $db->fetchRow($select);

    return $total;
}

public function saveShippingAction()
{
    $QShippingAddress       = new Application_Model_ShippingAddress();
    $this->_helper->layout->disableLayout();
    $userStorage          = Zend_Auth::getInstance()->getStorage()->read();
// print_r($_POST);die;
    $flashMessenger = $this->_helper->flashMessenger;
    try{

        $db = Zend_Registry::get('db');

        $db->beginTransaction();
        if ($this->getRequest()->getMethod() == 'POST'){

            $ship_id  = $this->getRequest()->getParam('ship_id', null);
            $id  = $this->getRequest()->getParam('id', null);
            $contract_name  = $this->getRequest()->getParam('contract_name', null);
            $data_address   = $this->getRequest()->getParam('data_address', null);
            $ship_province  = $this->getRequest()->getParam('ship_province', null);
            $amphures       = $this->getRequest()->getParam('amphures', null);
            $districts_sipping = $this->getRequest()->getParam('districts_sipping', null);
            $zip_id         = $this->getRequest()->getParam('zip_id', null);
            $contract_phone = $this->getRequest()->getParam('contract_phone', null);
            $back_url       = $this->getRequest()->getParam('back_url', null);
            $remark         = $this->getRequest()->getParam('remark', null);
            $area_id         = $this->getRequest()->getParam('area_id', null);

            $data_address =array(

                'contact_name'  =>  $contract_name,
                'd_id'          =>  $id,
                'address'       =>  $data_address,
                'province_id'   =>  $ship_province,
                'amphures_id'   =>  $amphures,
                'districts_id'  =>  $districts_sipping,
                'zipcodes'      =>  $zip_id,
                'phone'         =>  $contract_phone,
                'remark'        =>  $remark,
                'area_id'        =>  $area_id
            );

            if(!$area_id){
                unset($data_address['area_id']);
            }

            if (isset($ship_id) and $ship_id) {
                $data_address['updated_at'] = date('Y-m-d H:i:s');
                $data_address['updated_by'] = $userStorage->id;
                $where =  $QShippingAddress->getAdapter()->quoteInto('id = ?',$ship_id);
                $QShippingAddress->update($data_address,$where);   
            }else{

                $data_address['created_at'] = date('Y-m-d H:i:s');
                $data_address['created_by'] = $userStorage->id;
    // print_r($data_address);die;

                $QShippingAddress->insert($data_address);

            }    
// print_r($data_address); die;


        }  
        $db->commit();
        $flashMessenger->setNamespace('success')->addMessage('Done!');
        $this->_redirect($back_url);

    }catch (Exception $e){

        $db->rollback();
        $flashMessenger->setNamespace('success')->addMessage('Cannot save, please try again!');
        $this->_redirect($back_url);    

    }  

}


public function checkQuotaAction()
{
    $distributor_id     = $this->getRequest()->getParam('distributor_id', null);
    $good_id            = $this->getRequest()->getParam('good_id', null);
    $good_color         = $this->getRequest()->getParam('good_color', null);
    $num                = $this->getRequest()->getParam('num', null);
    $QMarket              = new Application_Model_Market();

    $params = array(
        'distributor_id' => $distributor_id,
        'good_id'        => $good_id,
        'good_color'     => $good_color,
        'num'            => $num   

    );

    $quota      = $QMarket->checkQuota($params);

    print_r($quota);exit();

}
public function checkQuota($params)
{
    $db = Zend_Registry::get('db');
    $num = $params['num'];
    $select_q = $db->select()
    ->from(array('q' => 'quota'), array('q.quantity'));
    $select_q->where('q.d_id = ?', $params['distributor_id']);
    $select_q->where('q.good_id = ?', $params['good_id']);
    $select_q->where('q.good_color = ?', $params['good_color']);
    $select_q->where('q.status = ?', 1);
    $data = $db->fetchOne($select_q);
    if ($data) {
        $select_m = $db->select()
        ->from(array('m' => $this->_name), array('sum(m.num)'));
        $select_m->where('m.d_id = ?', $params['distributor_id']);
        $select_m->where('m.good_id = ?', $params['good_id']);
        $select_m->where('m.good_color = ?', $params['good_color']);
        $select_m->where('m.status = ?', 1);
        $select_m->where('m.canceled = ?', 0);
        $select_m->where('m.add_time >= ?', '2017-01-01 00:00:00');


        $isSum = $db->fetchOne($select_m);

        if ($isSum) {
            if ($num+$isSum > $data) {
                $q='1';
            }else{
                $q='0';
            }
        }else{
            $q = '0';
        }

}else{ // ??No data return 0
    $q = '0';
}
return $q;
}
public function checkQuotaOppoAction()
{
    $distributor_id       = $this->getRequest()->getParam('distributor_id', null);
    $good_id              = $this->getRequest()->getParam('good_id', null);
    $good_color           = $this->getRequest()->getParam('good_color', null);
    $num                  = $this->getRequest()->getParam('num', null);
    $rank                 = $this->getRequest()->getParam('rank', null);
    $warehouse_id         = $this->getRequest()->getParam('warehouse_id', null);
    $cat_id               = $this->getRequest()->getParam('cat_id', null);
    $sales_sn             = $this->getRequest()->getParam('sales_sn', null);
    $type                 = $this->getRequest()->getParam('type', null);
    $QMarket              = new Application_Model_Market();

    $params = array(
        'distributor_id' => $distributor_id,
        'good_id'        => $good_id,
        'good_color'     => $good_color,
        'num'            => $num,   
        'rank'           => $rank,   
        'warehouse_id'      => $warehouse_id,   
        'cat_id'         => $cat_id,   
        'sales_sn'       => $sales_sn,   
        'type'           => $type,   

    );

    $quota      = $QMarket->checkQuotaOppo($params);

    print_r($quota);exit();

}

private function _exportExcelCheckCn($data)
{

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', '200M');
    $filename = 'Check Credit Note_'.date('d-m-Y H-i-s').'.csv';
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
/////////////////// T?NG H?P D? LI?U
////////////////////////////////////////////////////

$heads = array(
    'Distributor ID',
    'Distributor Number',
    'Distributor Name',
    'Grand Area',
    'Area',
    'Province',
    'District',
    'Type',
    'Type Name',
    'Creditnote',
    'Total',
    'Use',
    'Balance',
    'Created Time',
    'User Time'
);

fputcsv($output, $heads);

// $grand_e1 = array(73,81,82,83,84);
// $grand_e2 = array(74,80,85,86,87,88,89);
// $grand_e3 = array(77,90,91,92,93);
// $grand_e4 = array(79,94,95,96,97,109);
// $grand_w1 = array(75,98,99,100,101,102);
// $grand_w2 = array(76,78,103,104,105,106,107,108);

// $grand_e1 = array(81,82,83);
// $grand_e2 = array(85,86,87,115);
// $grand_e3 = array(90,91,92,93,113);
// $grand_e4 = array(94,95,96);
// $grand_e5 = array(88,89,117);
// $grand_e6 = array(110,111,112);
// $grand_e7 = array(97,109);
// $grand_w1 = array(98,99,100,101,102,114);
// $grand_w2 = array(103,104,105,116);
// $grand_w3 = array(106,107,108);

$grand_e1 = array(81,82,83,110,111,112);
$grand_e2 = array(85,86,87,115,88,89,116,117);
$grand_e3 = array(90,91,92,93,113);
$grand_e4 = array(94,95,96);
$grand_e5 = array(97,109);
$grand_w1 = array(98,99,100,101,102,114);
$grand_w2 = array(103,104,105);
$grand_w3 = array(106,107,108);

foreach($data as $item)
{
    $creditnote_type_sn = $item['creditnote_type'];
    $creditnote_chanel_sn = $item['chanel'];

    if($creditnote_chanel_sn=='reward'){
        $creditnote_chanel='OPPOCLUB';
        $creditnote_type='CN';
    }else if($creditnote_chanel_sn=='accessories'){
        $creditnote_chanel='Price Protection';
        $creditnote_type='CP';
    }else if($creditnote_chanel_sn=='oppo_all_green'){
        $creditnote_chanel='OPPO ALL GREEN';
        $creditnote_type='CN';
    }else if($creditnote_chanel_sn=='top_green'){
        $creditnote_chanel='OPPO TOP GREEN';
        $creditnote_type='CN';
    }else{
        if($creditnote_type_sn=='CN')
        {
            if($creditnote_chanel_sn=='oppo_all_green'){
                $creditnote_chanel='oppo_all_green';
                $creditnote_type='CN';
            }else if($creditnote_chanel_sn=='service'){
                $creditnote_chanel='Credit Note';
                $creditnote_type='CN';
            }else{
                $creditnote_chanel='Return';
                $creditnote_type='CN';
            }
        }else{
            $creditnote_chanel='Price Protection';
            $creditnote_type='CP';
        }
    }

    $excel_area_id = $item['area_id'];
    $excel_area_name = $item['area_name'];

// if ( in_array($excel_area_id, $grand_e1) ) { $grand_area = 'BKK East-1'; } 
// else if ( in_array($excel_area_id, $grand_e2) ) { $grand_area = 'BKK East-2'; }
// else if ( in_array($excel_area_id, $grand_e3) ) { $grand_area = 'BKK East-3'; }
// else if ( in_array($excel_area_id, $grand_e4) ) { $grand_area = 'BKK East-4'; }
// else if ( in_array($excel_area_id, $grand_w1) ) { $grand_area = 'BKK West-1'; }
// else if ( in_array($excel_area_id, $grand_w2) ) { $grand_area = 'BKK West-2'; }
// else { $grand_area = $excel_area_name; }

// if ( in_array($excel_area_id, $grand_e1) ) { $grand_area = 'BKK East-1'; } 
// else if ( in_array($excel_area_id, $grand_e2) ) { $grand_area = 'BKK East-2'; }
// else if ( in_array($excel_area_id, $grand_e3) ) { $grand_area = 'BKK East-3'; }
// else if ( in_array($excel_area_id, $grand_e4) ) { $grand_area = 'BKK East-4'; }
// else if ( in_array($excel_area_id, $grand_e5) ) { $grand_area = 'BKK East-5'; }
// else if ( in_array($excel_area_id, $grand_e6) ) { $grand_area = 'BKK East-6'; }
// else if ( in_array($excel_area_id, $grand_e7) ) { $grand_area = 'BKK East-7'; }
// else if ( in_array($excel_area_id, $grand_w1) ) { $grand_area = 'BKK West-1'; }
// else if ( in_array($excel_area_id, $grand_w2) ) { $grand_area = 'BKK West-2'; }
// else if ( in_array($excel_area_id, $grand_w3) ) { $grand_area = 'BKK West-3'; }
// else { $grand_area = $excel_area_name; }

    if ( in_array($excel_area_id, $grand_e1) ) { $grand_area = 'BKK East-1'; } 
    else if ( in_array($excel_area_id, $grand_e2) ) { $grand_area = 'BKK East-2'; }
    else if ( in_array($excel_area_id, $grand_e3) ) { $grand_area = 'BKK East-3'; }
    else if ( in_array($excel_area_id, $grand_e4) ) { $grand_area = 'BKK East-4'; }
    else if ( in_array($excel_area_id, $grand_e5) ) { $grand_area = 'BKK East-5'; }
    else if ( in_array($excel_area_id, $grand_w1) ) { $grand_area = 'BKK West-1'; }
    else if ( in_array($excel_area_id, $grand_w2) ) { $grand_area = 'BKK West-2'; }
    else if ( in_array($excel_area_id, $grand_w3) ) { $grand_area = 'BKK West-3'; }
    else { $grand_area = $excel_area_name; }

    $row = array();

    $row[] = $item['D_id'];
    $row[] = $item['store_code'];
    $row[] = $item['title'];

    $row[] = $grand_area;
    $row[] = $excel_area_name;
    $row[] = $item['province'];
    $row[] = $item['district'];

    $row[] = $creditnote_type;
    $row[] = $creditnote_chanel;
    $row[] = $item['creditnote_sn'];
    $row[] = $item['total_amount'];
    $row[] = $item['use_discount'];
    $row[] = $item['balance_total'];
    $row[] = $item['create_date'];
    $row[] = $item['pay_time'];

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

public function ajaxgetimgpaymentAction()
{

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $sn = $this->getRequest()->getParam('sn');
    $sn = str_replace("'","",$sn);
    $payment_no = $this->getRequest()->getParam('payment_no');

    $QMarket = new Application_Model_Market();

    if($payment_no){
        $sn = $QMarket->getSnByPaymentNo($payment_no);
    }

    $getImgPatment = $QMarket->getImgPayment($sn);

// print_r($getImgPatment);die;

    $arr_part_img = [];
    $part_pay_one = HOST . 'public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'finance';
    $part_pay_group = HOST . 'public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'payment_group'. DIRECTORY_SEPARATOR . 'pay_slips' . DIRECTORY_SEPARATOR;

    foreach ($getImgPatment as $key) {
        if($key['pay_group'] == 0 && $key['img_pay_one'] && !$key['ep_id']){
            $part_img = $part_pay_one . $key['img_pay_one'];
            array_push($arr_part_img, $part_img);
        }

        if($key['pay_group'] == 1 && $key['img_pay_group'] && !$key['ep_id']){
            $part_img = $part_pay_group . $key['img_pay_group'];
            array_push($arr_part_img, $part_img);
        }

        if($key['ep_id']){
            $part_img = API_IOPPO_STAFF_URL . 'staff_slip_payment/' . $key['ep_staff_id'] . '/' . $key['ep_payslip'];
            array_push($arr_part_img, $part_img);
        }
    }

// print_r($arr_part_img);die;

    echo json_encode(['img_payment' => $arr_part_img]);
    exit();

}

public function get_DepositNo_Ref($db,$distributor_id,$user_id,$sn)
{
    $flashMessenger = $this->_helper->flashMessenger;
    $deposit_sn="";
    try {
        $stmt = $db->prepare("CALL gen_running_no_ref_reward('DP',".$sn.")");
        $stmt->execute();
        $result = $stmt->fetch();
        $deposit_sn = $result['running_no'];
    }catch (exception $e) {
        $flashMessenger->setNamespace('error')->addMessage($e);
    }
    return $deposit_sn;
}

function depositCreateAction()
{
//print_r($_POST);die; 
    $flashMessenger = $this->_helper->flashMessenger;
//$this->_helper->layout->disableLayout();


    if ($this->getRequest()->getMethod() == 'POST') {   
        $distributor_id = $this->getRequest()->getParam('d_id');
        $deposit_type = $this->getRequest()->getParam('deposit_type');
        $deposit_status = $this->getRequest()->getParam('deposit_status');

        $price = $this->getRequest()->getParam('price');

        $sn = date('YmdHis') . substr(microtime(), 2, 4);

        $QDeposit = new Application_Model_Deposit();

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
//Tanong


        try {
            if($sn!=''){
                $db = Zend_Registry::get('db');
                $db->beginTransaction();
                $create_date = date('Y-m-d H:i:s');


                if($deposit_type=='DP'){
                    $key_sn = date('YmdHis') . substr(microtime(), 2, 4);
                    $deposit_sn = $this->get_DepositNo_Ref($db,$distributor_id,$userStorage->id,$key_sn);
                }else{
                    $db->rollback();
                    $flashMessenger->setNamespace('error')->addMessage('Cannot Create Deposit No, please try again!');
                }
                
                $file_name_upload = '/deposit_pay_slips/'.$deposit_sn.'/'.$_FILES['file']['name'][0];

        //print_r($_FILES['file']);die;

                $data = array(
                    'distributor_id' => $distributor_id,
                    'create_by' => $userStorage->id,
                    'create_date' => $create_date,
                    'update_by' => $userStorage->id,
                    'update_date' => $create_date,
                    'deposit_type' => $deposit_type,
                    'total_amount' => $price,
                    'use_total' => 0,
                    'balance_total' => $price,
                    'status' => $deposit_status,
                    'deposit_sn' => $deposit_sn,
                    'file_pay_slip' => $file_name_upload,
                    'sn' => $sn
                );

        //print_r($data);die;
                $QDeposit->insert($data);
                $db->commit();
                
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
                        . DIRECTORY_SEPARATOR . 'sales'. DIRECTORY_SEPARATOR . 'deposit_pay_slips'
                        . DIRECTORY_SEPARATOR . $deposit_sn;

                        $file_pay_slip = DIRECTORY_SEPARATOR . 'deposit_pay_slips'
                        . DIRECTORY_SEPARATOR . $deposit_sn . DIRECTORY_SEPARATOR;    

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
                                        $this->_redirect(($back_url ? $back_url : HOST . 'sales/deposit-list'));
                                    }
                                }else{
                                 $upload->receive($file);
                             }                                                     
                         }
                         $r+=1;
                     }

                     /*-------------------End File Pay Slip Upload--------------------------*/



        //$this->_redirect(($back_url ? $back_url : HOST . 'finance/deposit-list'));
                     $flashMessenger->setNamespace('success')->addMessage('Save Success');
                     $this->_redirect(($back_url ? $back_url : HOST . 'sales/deposit-list'));
                 }
    //$stmt->execute();
             }catch (exception $e) {
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage('Cannot Save Deposit, Please try again!');
                $this->_redirect(($back_url ? $back_url : HOST . 'sales/deposit-list'));
            }
        }
    }


    public function depositListAction()
    {

        $this->view->meta_refresh = 300;

        $sort               = $this->getRequest()->getParam('sort');
        $desc               = $this->getRequest()->getParam('desc', 0);
        $page               = $this->getRequest()->getParam('page', 1);

        $deposit_sn         = $this->getRequest()->getParam('deposit_sn');
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
        $this->view->url    = HOST . 'sales/deposit-list/' . ($params ? '?' . http_build_query($params) .
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

            $this->_helper->viewRenderer->setRender('partials/deposit-list');
        }
    }


    public function depositLogAction()
    {

        $this->view->back_url = HOST . 'sales/deposit-list';
        $this->view->meta_refresh = 300;

        $d_id               = $this->getRequest()->getParam('d_id');
        $deposit_sn         = $this->getRequest()->getParam('deposit_sn');

        $sort               = $this->getRequest()->getParam('sort','update_date');
        $desc               = $this->getRequest()->getParam('desc', 1);
        $page               = $this->getRequest()->getParam('page', 1);

        $to_sales_no         = $this->getRequest()->getParam('to_sales_no');
        $created_at_to     = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));
        $created_at_from   = $this->getRequest()->getParam('created_at_from', date('d/m/Y', strtotime('-0 day')));
        $export             = $this->getRequest()->getParam('export', 0);

        $limit = LIMITATION;
        $total = 0;

        $params = array_filter(array(
            'd_id'            => $d_id,
            'deposit_no' => $deposit_no,
            'to_sales_no'   => $to_sales_no,
            'created_at_to'   => $created_at_to,
            'created_at_from'   => $created_at_from
        ));

        $params['sort'] = $sort;
        $params['desc'] = $desc;

        $QDepositTran = new Application_Model_DepositTran();

        if (isset($export) && $export) {
            $get_deposit_log = $QDepositTran->fetchPagination($page, null, $total, $params);
            $this->_exportExcelPaymentNoLog($get_deposit_log);
        }

        $get_deposit_log = $QDepositTran->fetchPagination($page, $limit, $total, $params);

// print_r($get_paymentno_log);die;

        $QDistributor  = new Application_Model_Distributor();
        $distributors  = $QDistributor->get_cache();

        $d_name = isset($distributors[$d_id]) ? $distributors[$d_id] : '';

        $this->view->distributor = ['id' => $d_id,'name' => $d_name];

        $this->view->get_deposit_log     = $get_deposit_log;
        $this->view->distributors      = $distributors;

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->no_show_brandshop = $no_show_brandshop;
        $this->view->url    = HOST . 'sales/deposit-log/' . ($params ? '?' . http_build_query($params) .
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

    private function _exportServiceJobNumber($params) {
// print_r($params);die;
// this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'SERVICE-JOBNUMBER'. ' - '.date('d-m-Y H-i-s').'.csv';
// output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/service/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);


        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
echo "\xEF\xBB\xBF"; // UTF-8 BOM

$heads = array(
    'NO',
    'SHOP CODE',
    'SHOP NAME',
    'JOB NUMBER',
    'SALES ORDER',
    'INVOICE NUMBER',
//'CUSTOMER NAME',
//'PHONE NUMBER',
    'TOTAL PRICE',
    'WITHHOLDING TAX',
    'TYPE PAYMENT',
    'NOTE',
    'DELIVERY ADDRESS'
);

fputcsv($output, $heads);

$start_date = "";
$end_date = "";
$where="";$where_group_id="";
$time=" 00:00:00";$time_to=" 23:59:59";

if (isset($params['created_at_from']) and $params['created_at_from']){
    list( $day, $month, $year ) = explode('/', $params['created_at_from']);

    if (isset($day) and isset($month) and isset($year) ){
        $where=" and m.add_time >= '$year-$month-$day $time'";
        $start_date = $year.'-'.$month.'-'.$day.' '.$time;
    }
}

if (isset($params['created_at_to']) and $params['created_at_to']){
    list( $day, $month, $year ) = explode('/', $params['created_at_to']);

    if (isset($day) and isset($month) and isset($year) ){
        $where.=" and m.add_time <= '$year-$month-$day $time_to'";
        $end_date = $year.'-'.$month.'-'.$day.' '.$time_to;
    }
}

if (isset($params['out_time_from']) and $params['out_time_from']){
    list( $day, $month, $year ) = explode('/', $params['out_time_from']);

    if (isset($day) and isset($month) and isset($year) ){
        $where=" and m.outmysql_time >= '$year-$month-$day $time'";
        $start_date = $year.'-'.$month.'-'.$day.' '.$time;
    }
}

if (isset($params['out_time_to']) and $params['out_time_to']){
    list( $day, $month, $year ) = explode('/', $params['out_time_to']);

    if (isset($day) and isset($month) and isset($year) ){
        $where.=" and m.outmysql_time <= '$year-$month-$day $time_to'";
        $end_date = $year.'-'.$month.'-'.$day.' '.$time_to;
    }
}

if (isset($params['finance_confirm_time_from']) and $params['finance_confirm_time_from']){
    list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_from']);

    if (isset($day) and isset($month) and isset($year) ){
        $where=" and m.pay_time >= '$year-$month-$day $time'";
        $start_date = $year.'-'.$month.'-'.$day.' '.$time;
    }
}

if (isset($params['finance_confirm_time_to']) and $params['finance_confirm_time_to']){
    list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_to']);

    if (isset($day) and isset($month) and isset($year) ){
        $where .=" and m.pay_time <= '$year-$month-$day $time_to'";
        $end_date = $year.'-'.$month.'-'.$day.' '.$time_to;
    }
}

$select_group = $db->select()
->from(array('u' => 'distributor_group_user'),array('u.group_id'))
->where('u.user_id=?',$userStorage->id);
$result_group = $db->fetchAll($select_group);
$group_id = "";
if ($result_group){
    foreach ($result_group as $to) {
        $group_id .= $to['group_id'].',';
    }

    $where_group_id = ' and d.group_id in('.rtrim($group_id, ',').')';
}


$i =1;

$sql_service = "select SUBSTRING(j.job_sn,16) AS running_jobno_real,j.`job_sn`,LEFT(j.job_sn,15) AS service_shop_no,m.d_id,d.`store_code`,d.`title`,m.sn_ref,m.invoice_number,m.invoice_time,m.`total`,cm.pay_servicecharge
,(round(m.`total`*1,2)-m.`total`) as vat
,j.`job_type`,CASE j.job_type WHEN '1' THEN '?????????' WHEN '2' THEN '????????/??????????' ELSE '-' END as job_type_name
,m.payment_type
,CASE j.job_type WHEN '1' THEN (CASE m.payment_type WHEN 'CA' THEN 'Cash' WHEN 'CR' THEN 'Credit' ELSE '-' END) WHEN '2' THEN 'No Payment' ELSE '-' END as payment_type_name
,m.customer_id,c.customer_name,c.`address_tax`,c.phone_number,m.payment_no,m.shipping_text,m.delivery_address
from market m
left join customer_brandshop c on m.customer_id=c.customer_id
left join job_number j on j.`d_id`=m.`d_id` and j.`sales_order`=m.`sn`
LEFT JOIN `checkmoney` cm ON cm.`d_id`=m.`d_id` AND cm.`sn`=m.`sn` and cm.type=1
left join distributor d on d.id=m.d_id
where 1=1 and d.rank = 10
".$where."
".$where_group_id."
group by m.sn order by m.d_id,j.job_sn";

//echo $sql_service;die;    
$data = $db->fetchAll($sql_service);

if (!$data){return;}

$payment_no="";$pay_servicecharge=0;
foreach($data as $item){
    if($item['payment_no']!=$payment_no){
        $payment_no = $item['payment_no'];
        $pay_servicecharge = $item['pay_servicecharge'];
    }else{
        $pay_servicecharge=0;
    }

    $row = array();
    $row[] = $i++;
    $row[] = $item['store_code'];
    $row[] = $item['title'];
    $row[] = $item['job_sn'];
    $row[] = $item['sn_ref'];
    $row[] = $item['invoice_number'];
    /*$row[] = $item['customer_name'];
    if($item['phone_number'] !=''){
        $row[] = "'".$item['phone_number'];
    }else{
        $row[]='';
    }*/
    $row[] = $item['total'];
    $row[] = $pay_servicecharge;
    $row[] = $item['payment_type_name'];
    $row[] = $item['shipping_text'];
    $row[] = $item['delivery_address'];

    fputcsv($output, $row);
    unset($row);
    unset($item);

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

private function _exportServiceJobNumber_old($params) {
// print_r($params);die;
// this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'SERVICE-JOBNUMBER'. ' - '.date('d-m-Y H-i-s').'.csv';
// output headers so that the file is downloaded rather than displayed
    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $file_path = APPLICATION_PATH.'/../public/files/sales/service/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);


    $path = $file_path.'/'.$filename;
    $output = fopen($path, 'w+');
echo "\xEF\xBB\xBF"; // UTF-8 BOM

$heads = array(
    'NO',
    'SHOP CODE',
    'SHOP NAME',
    'JOB RUNNING',
    'JOB NUMBER',
    'JOB TYPE',
    'SALES ORDER',
    'INVOICE NUMBER',
    'CUSTOMER NAME',
    'PHONE NUMBER',
    'TOTAL PRICE',
    'WITHHOLDING TAX',
    'TYPE PAYMENT'
);

fputcsv($output, $heads);

$start_date = "";
$end_date = "";
$where="";$where_group_id="";
$time=" 00:00:00";$time_to=" 23:59:59";

if (isset($params['created_at_from']) and $params['created_at_from']){
    list( $day, $month, $year ) = explode('/', $params['created_at_from']);

    if (isset($day) and isset($month) and isset($year) ){
        $where=" and m.add_time >= '$year-$month-$day $time'";
        $start_date = $year.'-'.$month.'-'.$day.' '.$time;
    }
}

if (isset($params['created_at_to']) and $params['created_at_to']){
    list( $day, $month, $year ) = explode('/', $params['created_at_to']);

    if (isset($day) and isset($month) and isset($year) ){
        $where.=" and m.add_time <= '$year-$month-$day $time_to'";
        $end_date = $year.'-'.$month.'-'.$day.' '.$time_to;
    }
}

if (isset($params['out_time_from']) and $params['out_time_from']){
    list( $day, $month, $year ) = explode('/', $params['out_time_from']);

    if (isset($day) and isset($month) and isset($year) ){
        $where=" and m.outmysql_time >= '$year-$month-$day $time'";
        $start_date = $year.'-'.$month.'-'.$day.' '.$time;
    }
}

if (isset($params['out_time_to']) and $params['out_time_to']){
    list( $day, $month, $year ) = explode('/', $params['out_time_to']);

    if (isset($day) and isset($month) and isset($year) ){
        $where.=" and m.outmysql_time <= '$year-$month-$day $time_to'";
        $end_date = $year.'-'.$month.'-'.$day.' '.$time_to;
    }
}

if (isset($params['finance_confirm_time_from']) and $params['finance_confirm_time_from']){
    list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_from']);

    if (isset($day) and isset($month) and isset($year) ){
        $where=" and m.pay_time >= '$year-$month-$day $time'";
        $start_date = $year.'-'.$month.'-'.$day.' '.$time;
    }
}

if (isset($params['finance_confirm_time_to']) and $params['finance_confirm_time_to']){
    list( $day, $month, $year ) = explode('/', $params['finance_confirm_time_to']);

    if (isset($day) and isset($month) and isset($year) ){
        $where .=" and m.pay_time <= '$year-$month-$day $time_to'";
        $end_date = $year.'-'.$month.'-'.$day.' '.$time_to;
    }
}

$select_group = $db->select()
->from(array('u' => 'distributor_group_user'),array('u.group_id'))
->where('u.user_id=?',$userStorage->id);
$result_group = $db->fetchAll($select_group);
$group_id = "";
if ($result_group){
    foreach ($result_group as $to) {
        $group_id .= $to['group_id'].',';
    }

    $where_group_id = ' and d.group_id in('.rtrim($group_id, ',').')';
}




$sql_master ="SELECT j.d_id,d.store_code,d.title,LEFT(j.job_sn,16) AS service_shop_no,MIN(SUBSTRING(j.job_sn,17)) AS running_min,MAX(SUBSTRING(j.job_sn,17)) AS running_max,min(j.create_date)as start_date,max(j.create_date) as end_date
FROM job_number j
left join distributor d on d.id=j.d_id
WHERE j.create_date BETWEEN '".$start_date."' AND '".$end_date."'".$where_group_id."

group by DATE_FORMAT(j.create_date, '%Y-%m-%d'),j.d_id";

//echo $sql_master;die;

$data_master = $db->fetchAll($sql_master);
//print_r($data_master);die;
if (!$data_master){return;}
$i =1;
foreach($data_master as $item_master)
{
    $d_id = $item_master['d_id'];  
    $store_code = $item_master['store_code'];  
    $store_name = $item_master['title'];  
    $service_shop_no = $item_master['service_shop_no'];  
    $running_min = $item_master['running_min'];
    $running_max = $item_master['running_max'];


    $sql_service = "select mask(t1.running,'######') as running,concat('".$service_shop_no."',mask(t1.running,'######')) as job_running,t2.* 
    from ( 
    (SELECT (a + b + c + d + e + f) as running,concat('".$service_shop_no."',mask((a + b + c + d + e + f),'######')) as job_running_chk
    FROM
    (SELECT 0 a UNION SELECT 1 a UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 ) d,
    (SELECT 0 b UNION SELECT 10 b UNION SELECT 20 UNION SELECT 30 UNION SELECT 40 UNION SELECT 50 UNION SELECT 60 UNION SELECT 70 UNION SELECT 80 UNION SELECT 90 ) d1,
    (SELECT 0 c UNION SELECT 100 c UNION SELECT 200 UNION SELECT 300 UNION SELECT 400 UNION SELECT 500 UNION SELECT 600 UNION SELECT 700 UNION SELECT 800 UNION SELECT 900 ) d2,
    (SELECT 0 d UNION SELECT 1000 d UNION SELECT 2000 UNION SELECT 3000 UNION SELECT 4000 UNION SELECT 5000 UNION SELECT 6000 UNION SELECT 7000 UNION SELECT 8000 UNION SELECT 9000 ) d3,
    (SELECT 0 e UNION SELECT 10000 e UNION SELECT 20000 UNION SELECT 30000 UNION SELECT 40000 UNION SELECT 50000 UNION SELECT 60000 UNION SELECT 70000 UNION SELECT 80000 UNION SELECT 90000 ) d4,
    (SELECT 0 f UNION SELECT 100000 f UNION SELECT 200000 UNION SELECT 300000 UNION SELECT 400000 UNION SELECT 500000 UNION SELECT 600000 UNION SELECT 700000 UNION SELECT 800000 UNION SELECT 900000 ) d5
    ))t1 
    left join
    (select SUBSTRING(j.job_sn,16) AS running_jobno_real,j.`job_sn`,LEFT(j.job_sn,15) AS service_shop_no,m.d_id,d.`store_code`,d.`title`,m.sn_ref,m.invoice_number,m.invoice_time,m.`total`,cm.pay_servicecharge
    ,(round(m.`total`*1,2)-m.`total`) as vat
    ,j.`job_type`,CASE j.job_type WHEN '1' THEN '?????????' WHEN '2' THEN '????????/??????????' ELSE '-' END as job_type_name
    ,m.payment_type
    ,CASE j.job_type WHEN '1' THEN (CASE m.payment_type WHEN 'CA' THEN 'Cash' WHEN 'CR' THEN 'Credit' ELSE '-' END) WHEN '2' THEN 'No Payment' ELSE '-' END as payment_type_name
    ,m.customer_id,c.customer_name,c.`address_tax`,c.phone_number
    from market m
    left join customer_brandshop c on m.customer_id=c.customer_id
    left join job_number j on j.`d_id`=m.`d_id` and j.`sales_order`=m.`sn`
    LEFT JOIN `checkmoney` cm ON cm.`d_id`=m.`d_id` AND cm.`sn`=m.`sn` and cm.type=1
    left join distributor d on d.id=m.d_id
    where 1=1
    ".$where."
    and m.d_id='".$d_id."'".$where_group_id."
    group by m.sn)t2 on t1.job_running_chk  COLLATE utf8_unicode_ci = t2.job_sn
    WHERE t1.running BETWEEN ".$running_min." AND ".$running_max."
    order by job_running";

//echo $sql_service;die;    
    $data = $db->fetchAll($sql_service);

    if (!$data){return;}


    foreach($data as $item){
        $row = array();
        $row[] = $i++;
        $row[] = $store_code;
        $row[] = $store_name;
        $row[] = $item['job_running'];
        $row[] = $item['job_sn'];
        $row[] = $item['job_type_name'];
        $row[] = $item['sn_ref'];
        $row[] = $item['invoice_number'];
        $row[] = $item['customer_name'];
        if($item['phone_number'] !=''){
            $row[] = "'".$item['phone_number'];
        }else{
            $row[]='';
        }
        $row[] = $item['total'];
        $row[] = $item['pay_servicecharge'];
        $row[] = $item['payment_type_name'];

        fputcsv($output, $row);
        unset($row);
        unset($item);

    }
    unset($data);
}  
unset($data_master);



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

public function getMemberBrandshopAction()
{

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $code = $this->getRequest()->getParam('code');

    $QMB = new Application_Model_MemberBrandshop();

    $getMemberBrandshop = $QMB->getMemberBrandshop($code);

    if(!$getMemberBrandshop){
        echo json_encode(['status' => 400,'message' => 'Not Find Member!']);
        exit();
    }

    echo json_encode(['status' => 200,'data' => $getMemberBrandshop]);
    exit();

}


public function _exportExcelExportCashCollectionServiceExcel($data)
{
//print_r($data);die;
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
//$filename = 'SERVICE-JOBNUMBER'. ' - '.date('d-m-Y H-i-s').'.csv';
    $filename = 'MoneyChecksCashCollectionService_'.date('d-m-Y H-i-s').'.csv';
// output headers so that the file is downloaded rather than displayed
    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $file_path = APPLICATION_PATH.'/../public/files/sales/service/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);


    $path = $file_path.'/'.$filename;
    $output = fopen($path, 'w+');
echo "\xEF\xBB\xBF"; // UTF-8 BOM

$heads = array(
    'NO',
    'STORE ID',
    'RETAILER',
    'STORE CODE',
    'COMPANY',
    'PAYMENT NO',
    'BANK',    
    'IN TIME',
    'TYPE',
    'IN MONEY',
    'ORDER NUMBER',
    'INVOICE NUMBER',

    'FINANCE GROUP'
);

fputcsv($output, $heads);
//--------------------------------------


$float = floatval($string);
$QDistributor = new Application_Model_Distributor();
$distributors_cached = $QDistributor->get_cache();
$distributors_storecode_cached = $QDistributor->storecode_get_cache();

$i = 2;
$index    = 2;
$payment_note='';$payment_no="";$pay_servicechargefee=0;$payment_servicechargefee='';
foreach($data as $t){
    $row = array();
//$row[] = $t['job_running'];


    $float = floatval($string);

    if($t['payment_no'].'-'.$t['TYPE']!=$payment_servicechargefee && ($t['TYPE']==8)){
        $pay_servicechargefee=1;
        $payment_servicechargefee = $t['payment_no'].'-'.$t['TYPE'];
    }else{
   // $pay_servicechargefee=0;
    }

    if($t['output'] <= 0)
    {
        $money = $t['output'] * -1;  
    }

    $money_check = $t['output'];

    $alpha = 'A';
    $arrMoneyType = unserialize(MONEY_TYPE);
    foreach($arrMoneyType as $key => $value):
        if($t['TYPE'] == $key){
            $type_title = $value;
            break;
        }
    endforeach;
    $sn_ref=$t['sn_ref'];
    if($sn_ref==''){
      $sn_ref=$t['sn'];  
  }

  $title = isset($distributors_cached[$t['d_id']]) ? $distributors_cached[$t['d_id']] : '';
  $store_code = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

//$finance_group = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

  if($t['seq']=='2'){
    $payment_note=$t['pay_text'];
}

if($t['canceled']=='1'){
    $cancel_status='canceled';
    $canceled_remark=$t['canceled_remark'];
}else{
    $cancel_status='';
    $canceled_remark='';
}

if($pay_servicechargefee==1 && $t['TYPE']==8){ 
    $pay_servicechargefee=0;
    if($t['note'] !='Discount 1 %'){

        $row[] = $index - 1;
        $row[] = $t['d_id'];
        $row[] = $title;
        $row[] = $store_code;
        $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
        $row[] = $t['payment_no'];
        $row[] = $t['bank_name'];

        $row[] = $t['pay_time'];
        $row[] = $type_title;
        $row[] = $t['pay_money'];
        $row[] = $sn_ref;
        $row[] = $t['invoice_number'];


    }
    $row[] = $t['finance_group'];

    if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %')){
        $row[] = $index - 1;
        $row[] = $t['d_id'];
        $row[] = $title;
        $row[] = $store_code;
        $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
        $row[] = $t['payment_no'];
        $row[] = $t['bank_name'];
        $row[] = $t['pay_time'];
        $row[] = $type_title;
        $row[] = $t['pay_money'];
        $row[] = $sn_ref;
        $row[] = $t['invoice_number'];

        $row[] = $t['finance_group'];
    }


    $index++;

    fputcsv($output, $row);
    unset($t);
    unset($row);
}else
if($t['TYPE'] !=8 && $t['TYPE'] !=2){
    if($t['note'] !='Discount 1 %'){
        $row[] = $index - 1;
        $row[] = $t['d_id'];
        $row[] = $title;
        $row[] = $store_code;
        $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
        $row[] = $t['payment_no'];
        $row[] = $t['bank_name'];
        $row[] = $t['pay_time'];
        $row[] = $type_title;
        $row[] = $t['pay_money'];
        $row[] = $sn_ref;
        $row[] = $t['invoice_number'];

    }
    $row[] = $t['finance_group'];

    if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %')){
        $row[] = $index - 1;
        $row[] = $t['d_id'];
        $row[] = $title;
        $row[] = $store_code;
        $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
        $row[] = $t['payment_no'];
        $row[] = $t['bank_name'];
        $row[] = $t['pay_time'];
        $row[] = $type_title;
        $row[] = $t['pay_money'];
        $row[] = $sn_ref;
        $row[] = $t['invoice_number'];

        $row[] = $t['finance_group'];
    }  
    $index++;

    fputcsv($output, $row);
    unset($t);
    unset($row);
}

}
unset($data);

//--------------------------------------
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

public function _exportExcelExportCashCollectionBrandShopExcel($data)
{
//print_r($data);die;
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
//$filename = 'SERVICE-JOBNUMBER'. ' - '.date('d-m-Y H-i-s').'.csv';
    $filename = 'MoneyChecksCashCollectionBrandShop_'.date('d-m-Y H-i-s').'.csv';
// output headers so that the file is downloaded rather than displayed
    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $file_path = APPLICATION_PATH.'/../public/files/sales/service/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);


    $path = $file_path.'/'.$filename;
    $output = fopen($path, 'w+');
echo "\xEF\xBB\xBF"; // UTF-8 BOM

$heads = array(
    'NO',
    'STORE ID',
    'RETAILER',
    'STORE CODE',
    'COMPANY',
    'PAYMENT NO',
    'BANK',    
    'IN TIME',
    'TYPE',
    'IN MONEY',
    'ORDER NUMBER',
    'INVOICE NUMBER',

    'FINANCE GROUP'
);

fputcsv($output, $heads);
//--------------------------------------


$float = floatval($string);
$QDistributor = new Application_Model_Distributor();
$distributors_cached = $QDistributor->get_cache();
$distributors_storecode_cached = $QDistributor->storecode_get_cache();

$i = 2;
$index    = 2;
$payment_note='';$payment_no="";$pay_servicechargefee=0;$payment_servicechargefee='';
foreach($data as $t){
    $row = array();
//$row[] = $t['job_running'];


    $float = floatval($string);

    if($t['payment_no'].'-'.$t['TYPE']!=$payment_servicechargefee && ($t['TYPE']==8)){
        $pay_servicechargefee=1;
        $payment_servicechargefee = $t['payment_no'].'-'.$t['TYPE'];
    }else{
   // $pay_servicechargefee=0;
    }

    if($t['output'] <= 0)
    {
        $money = $t['output'] * -1;  
    }

    $money_check = $t['output'];

    $alpha = 'A';
    $arrMoneyType = unserialize(MONEY_TYPE);
    foreach($arrMoneyType as $key => $value):
        if($t['TYPE'] == $key){
            $type_title = $value;
            break;
        }
    endforeach;
    $sn_ref=$t['sn_ref'];
    if($sn_ref==''){
      $sn_ref=$t['sn'];  
  }

  $title = isset($distributors_cached[$t['d_id']]) ? $distributors_cached[$t['d_id']] : '';
  $store_code = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

//$finance_group = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

  if($t['seq']=='2'){
    $payment_note=$t['pay_text'];
}

if($t['canceled']=='1'){
    $cancel_status='canceled';
    $canceled_remark=$t['canceled_remark'];
}else{
    $cancel_status='';
    $canceled_remark='';
}

if($pay_servicechargefee==1 && $t['TYPE']==8){ 
    $pay_servicechargefee=0;
    if($t['note'] !='Discount 1 %'){

        $row[] = $index - 1;
        $row[] = $t['d_id'];
        $row[] = $title;
        $row[] = $store_code;
        $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
        $row[] = $t['payment_no'];
        $row[] = $t['bank_name'];

        $row[] = $t['pay_time'];
        $row[] = $type_title;
        $row[] = $t['pay_money'];
        $row[] = $sn_ref;
        $row[] = $t['invoice_number'];


    }
    $row[] = $t['finance_group'];

    if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %')){
        $row[] = $index - 1;
        $row[] = $t['d_id'];
        $row[] = $title;
        $row[] = $store_code;
        $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
        $row[] = $t['payment_no'];
        $row[] = $t['bank_name'];
        $row[] = $t['pay_time'];
        $row[] = $type_title;
        $row[] = $t['pay_money'];
        $row[] = $sn_ref;
        $row[] = $t['invoice_number'];

        $row[] = $t['finance_group'];
    }


    $index++;

    fputcsv($output, $row);
    unset($t);
    unset($row);
}else
if($t['TYPE'] !=8 && $t['TYPE'] !=2){
    if($t['note'] !='Discount 1 %'){
        $row[] = $index - 1;
        $row[] = $t['d_id'];
        $row[] = $title;
        $row[] = $store_code;
        $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
        $row[] = $t['payment_no'];
        $row[] = $t['bank_name'];
        $row[] = $t['pay_time'];
        $row[] = $type_title;
        $row[] = $t['pay_money'];
        $row[] = $sn_ref;
        $row[] = $t['invoice_number'];

    }
    $row[] = $t['finance_group'];

    if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %')){
        $row[] = $index - 1;
        $row[] = $t['d_id'];
        $row[] = $title;
        $row[] = $store_code;
        $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
        $row[] = $t['payment_no'];
        $row[] = $t['bank_name'];
        $row[] = $t['pay_time'];
        $row[] = $type_title;
        $row[] = $t['pay_money'];
        $row[] = $sn_ref;
        $row[] = $t['invoice_number'];

        $row[] = $t['finance_group'];
    }  
    $index++;

    fputcsv($output, $row);
    unset($t);
    unset($row);
}

}
unset($data);

//--------------------------------------
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

public function _exportExcelExportCashCollectionServiceExcel_old($data)
{
//print_r($data);die;
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
//$filename = 'SERVICE-JOBNUMBER'. ' - '.date('d-m-Y H-i-s').'.csv';
    $filename = 'MoneyChecksCashCollectionService_'.date('d-m-Y H-i-s').'.csv';
// output headers so that the file is downloaded rather than displayed
    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $file_path = APPLICATION_PATH.'/../public/files/sales/service/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);


    $path = $file_path.'/'.$filename;
    $output = fopen($path, 'w+');
echo "\xEF\xBB\xBF"; // UTF-8 BOM

$heads = array(
    'NO',
    'PAYMENT NO',
    'STORE ID',
    'RETAILER',
    'STORE CODE',
    'BANK',
    'COMPANY',
    'TYPE',
    'PAYMENT TYPE',
    'ORDER NUMBER',
    'INVOICE NUMBER',
    'CREDIT NOTE',
    'DEPOSIT NO',
    'IN / OUT MONEY',
    'IN / OUT TIME',
    'BANK TRANSACTION CODE',
    'CONTENT',
    'NOTE',
    'PAY NOTE',
    'PAY NOTE FROM OUT',
    'BALANCE',
    'Cash collection period (YMD)',
    'FINANCE GROUP',
    'CANCEL STATUS',
    'CANCEL REMARK',
    'SALES ORDER REF',
    'PAYMENT NO REF'
);

fputcsv($output, $heads);
//--------------------------------------


$float = floatval($string);
$QDistributor = new Application_Model_Distributor();
$distributors_cached = $QDistributor->get_cache();
$distributors_storecode_cached = $QDistributor->storecode_get_cache();

$i = 2;
$index    = 2;
$payment_note='';$payment_no="";$pay_servicechargefee=0;$payment_servicechargefee='';
foreach($data as $t){
    $row = array();
//$row[] = $t['job_running'];


    $float = floatval($string);

    if($t['payment_no'].'-'.$t['TYPE']!=$payment_servicechargefee && ($t['TYPE']==8)){
        $pay_servicechargefee=1;
        $payment_servicechargefee = $t['payment_no'].'-'.$t['TYPE'];
    }else{
   // $pay_servicechargefee=0;
    }

    if($t['output'] <= 0)
    {
        $money = $t['output'] * -1;  
    }

    $money_check = $t['output'];

    $alpha = 'A';
    $arrMoneyType = unserialize(MONEY_TYPE);
    foreach($arrMoneyType as $key => $value):
        if($t['TYPE'] == $key){
            $type_title = $value;
            break;
        }
    endforeach;
    $sn_ref=$t['sn_ref'];
    if($sn_ref==''){
      $sn_ref=$t['sn'];  
  }

  $title = isset($distributors_cached[$t['d_id']]) ? $distributors_cached[$t['d_id']] : '';
  $store_code = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

//$finance_group = isset($distributors_storecode_cached[$t['d_id']]) ? $distributors_storecode_cached[$t['d_id']] : '';

  if($t['seq']=='2'){
    $payment_note=$t['pay_text'];
}

if($t['canceled']=='1'){
    $cancel_status='canceled';
    $canceled_remark=$t['canceled_remark'];
}else{
    $cancel_status='';
    $canceled_remark='';
}

if($pay_servicechargefee==1 && $t['TYPE']==8){ 
    $pay_servicechargefee=0;
    if($t['note'] !='Discount 1 %'){

        $row[] = $index - 1;
        $row[] = $t['payment_no'];
        $row[] = $t['d_id'];
        $row[] = $title;
        $row[] = $store_code;
        $row[] = $t['bank_name'];

        $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
        $row[] = $type_title;
        $row[] = $t['payment_type'];
        $row[] = $sn_ref;
        $row[] = $t['invoice_number'];
        $row[] = $t['creditnote_sn'];
        $row[] = $t['deposit_sn'];
        $row[] = $t['pay_money'];
        $row[] = $t['pay_time'];

        $row[] = $t['bank_transaction_code'];
        $row[] = $t['content'];
        $row[] = $t['note'];
        $row[] = $t['pay_text'];
        $row[] = $payment_note;
        $row[] = $t['balance'];
        $row[] = date("ymd", strtotime($t['pay_time']));
    }
    $row[] = $t['finance_group'];
    $row[] = $cancel_status;

    $row[] = $canceled_remark;
    $row[] = $t['sales_order_ref'];
    $row[] = $t['payment_no_ref'];

    if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %')){
        $row[] = $index - 1;
        $row[] = $t['payment_no'];
        $row[] = $t['d_id'];
        $row[] = $title;
        $row[] = $store_code;
        $row[] = $t['bank_name'];

        $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
        $row[] = $type_title;
        $row[] = $t['payment_type'];
        $row[] = $sn_ref;
        $row[] = $t['invoice_number'];
        $row[] = $t['creditnote_sn'];
        $row[] = $t['deposit_sn'];
        $row[] = $t['pay_money'];
        $row[] = $t['pay_time'];

        $row[] = $t['bank_transaction_code'];
        $row[] = $t['content'];
        $row[] = $t['note'];
        $row[] = $t['pay_text'];
        $row[] = $payment_note;
        $row[] = $t['balance'];
        $row[] = date("ymd", strtotime($t['pay_time']));
        $row[] = $t['finance_group'];
        $row[] = $cancel_status;

        $row[] = $canceled_remark;
        $row[] = $t['sales_order_ref'];
        $row[] = $t['payment_no_ref'];

    }


    $index++;

    fputcsv($output, $row);
    unset($t);
    unset($row);
}else
if($t['TYPE'] !=8){
    if($t['note'] !='Discount 1 %'){
        $row[] = $index - 1;
        $row[] = $t['payment_no'];
        $row[] = $t['d_id'];
        $row[] = $title;
        $row[] = $store_code;
        $row[] = $t['bank_name'];

        $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
        $row[] = $type_title;
        $row[] = $t['payment_type'];
        $row[] = $sn_ref;
        $row[] = $t['invoice_number'];
        $row[] = $t['creditnote_sn'];
        $row[] = $t['deposit_sn'];
        $row[] = $t['pay_money'];
        $row[] = $t['pay_time'];

        $row[] = $t['bank_transaction_code'];
        $row[] = $t['content'];
        $row[] = $t['note'];
        $row[] = $t['pay_text'];
        $row[] = $payment_note;
        $row[] = $t['balance'];
        $row[] = date("ymd", strtotime($t['pay_time']));
    }
    $row[] = $t['finance_group'];
    $row[] = $cancel_status;

    $row[] = $canceled_remark;
    $row[] = $t['sales_order_ref'];
    $row[] = $t['payment_no_ref'];

    if(floatval($money_check) >= 0 && ($t['note'] =='Discount 1 %')){
        $row[] = $index - 1;
        $row[] = $t['payment_no'];
        $row[] = $t['d_id'];
        $row[] = $title;
        $row[] = $store_code;
        $row[] = $t['bank_name'];

        $row[] = ($t['company_id'] == 1 ) ? 'OPPO':'TM';
        $row[] = $type_title;
        $row[] = $t['payment_type'];
        $row[] = $sn_ref;
        $row[] = $t['invoice_number'];
        $row[] = $t['creditnote_sn'];
        $row[] = $t['deposit_sn'];
        $row[] = $t['pay_money'];
        $row[] = $t['pay_time'];

        $row[] = $t['bank_transaction_code'];
        $row[] = $t['content'];
        $row[] = $t['note'];
        $row[] = $t['pay_text'];
        $row[] = $payment_note;
        $row[] = $t['balance'];
        $row[] = date("ymd", strtotime($t['pay_time']));
        $row[] = $t['finance_group'];
        $row[] = $cancel_status;

        $row[] = $canceled_remark;
        $row[] = $t['sales_order_ref'];
        $row[] = $t['payment_no_ref'];
    }  
    $index++;

    fputcsv($output, $row);
    unset($t);
    unset($row);
}

}
unset($data);

//--------------------------------------
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

public function uploadAction()
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $upload = new Zend_File_Transfer();

//check function
    if (function_exists('finfo_file'))
// $upload->addValidator('MimeType', false, array(
//  'image/jpeg',
//  'image/pjpeg',
//  'image/png',
//  'image/gif'));

// $upload->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $upload->addValidator('Extension', false, 'pdf,jpg,jpeg');
    $upload->addValidator('FilesSize', false, array('max' => '10MB'));

    $upload->addValidator('ExcludeExtension', false, 'php,sh');

    $upload->addValidator('Count', false, 1);

    $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' .
    DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'upload' .
    DIRECTORY_SEPARATOR . 'distributor_doc' . DIRECTORY_SEPARATOR;

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $uniqid = uniqid();

    $uploaded_dir .= 'temp' . DIRECTORY_SEPARATOR . $userStorage->id .
    DIRECTORY_SEPARATOR . $uniqid . DIRECTORY_SEPARATOR;

    if (!is_dir($uploaded_dir))
        @mkdir($uploaded_dir, 0777, true);

    $upload->setDestination($uploaded_dir);

    if (!$upload->isValid())
    {
        $errors = $upload->getErrors();

        $sError = null;

        if ($errors and isset($errors[0]))
            switch ($errors[0])
        {
            case 'fileUploadErrorIniSize':
            $sError = 'File size is too large';
            break;
            case 'fileMimeTypeFalse';
            $sError = 'The file(s) you selected weren\'t the type we were expecting';
            break;
        }

        $result = array('error' => $sError);

    } else
    {

        try
        {
            $upload->receive();


            $result = array('success' => true);

        }
        catch (Zend_File_Transfer_Exception $e)
        {

            $result = array('error' => $e->message());
        }

        $result['uniqid'] = $uniqid;
    }

// to pass data through iframe you will need to encode all html tags
    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

    exit;
}

public function uploadBlacklistAction()
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $upload = new Zend_File_Transfer();

//check function
    if (function_exists('finfo_file'))
// $upload->addValidator('MimeType', false, array(
//  'image/jpeg',
//  'image/pjpeg',
//  'image/png',
//  'image/gif'));

// $upload->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $upload->addValidator('Extension', false, 'pdf,jpg,jpeg');
    $upload->addValidator('FilesSize', false, array('max' => '10MB'));

    $upload->addValidator('ExcludeExtension', false, 'php,sh');

    $upload->addValidator('Count', false, 1);

    $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' .
    DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'upload' .
    DIRECTORY_SEPARATOR . 'blacklist_image' . DIRECTORY_SEPARATOR;

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $uniqid = uniqid();

    $uploaded_dir .= 'temp' . DIRECTORY_SEPARATOR . $userStorage->id .
    DIRECTORY_SEPARATOR . $uniqid . DIRECTORY_SEPARATOR;

    if (!is_dir($uploaded_dir))
        @mkdir($uploaded_dir, 0777, true);

    $upload->setDestination($uploaded_dir);

    if (!$upload->isValid())
    {
        $errors = $upload->getErrors();

        $sError = null;

        if ($errors and isset($errors[0]))
            switch ($errors[0])
        {
            case 'fileUploadErrorIniSize':
            $sError = 'File size is too large';
            break;
            case 'fileMimeTypeFalse';
            $sError = 'The file(s) you selected weren\'t the type we were expecting';
            break;
        }

        $result = array('error' => $sError);

    } else
    {

        try
        {
            $upload->receive();


            $result = array('success' => true);

        }
        catch (Zend_File_Transfer_Exception $e)
        {

            $result = array('error' => $e->message());
        }

        $result['uniqid'] = $uniqid;
    }

// to pass data through iframe you will need to encode all html tags
    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

    exit;
}

public function deleteDocumentDistributorAction()
{
    $id               = $this->getRequest()->getParam('id');
    $d_id               = $this->getRequest()->getParam('d_id');
    $d_document               = $this->getRequest()->getParam('d_document');
// die($id);
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $QCreateDocument = new Application_Model_CreateDocument();
    if ($id) {
        $where = $QCreateDocument->getAdapter()->quoteInto('id = ?', $id);
        $QCreateDocument->delete($where);

        $located_dir = APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.
        DIRECTORY_SEPARATOR.'public'.
        DIRECTORY_SEPARATOR.'upload'.
        DIRECTORY_SEPARATOR.'distributor_doc'.
        DIRECTORY_SEPARATOR.$d_id.
        DIRECTORY_SEPARATOR.$d_document;
        // print_r($located_dir);
        unlink($located_dir);       

    }
    $this->_redirect(HOST . 'sales/distributor-document?id='.$d_id);

}


public function returnBoxNumberImeiListAction()
{

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
        'action_frm' => 'list'
    ));

    $QReturnBoxNumber = new Application_Model_ReturnBoxNumber();
    $QReturnBoxNumberImei = new Application_Model_ReturnBoxNumberImei();
    $QDistributor   = new Application_Model_Distributor();

    $distributors = $QDistributor->get_with_store_code_cache();

    $params['sort'] = $sort;
    $params['desc'] = $desc;

    if (isset($export) && $export) {
        $params['action_frm'] = 'export';
        $get_resule = $QReturnBoxNumberImei->fetchPagination($page, null, $total, $params);
        $this->_exportBoxImeiList($get_resule);
    }else{
        $get_resule = $QReturnBoxNumber->fetchPagination($page, $limit, $total, $params);
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
    $this->view->url    = HOST . 'sales/return-box-number-imei-list/' . ($params ? '?' . http_build_query($params) .
        '&' : '?');
    $this->view->params = $params;
    $this->view->offset = $limit * ($page - 1);

//print_r($params);

    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;

    $messages_success = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages_success = $messages_success;

    if ($this->getRequest()->isXmlHttpRequest()) {
        $this->_helper->layout->disableLayout();

        $this->_helper->viewRenderer->setRender('partials/return-box-number-imei-list');
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
    '??????????(??????)?????????????',
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
    if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = '????????????'; }else { $branch_type = '????'; }
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

public function removeShippingAddressAction(){

    $id = $this->getRequest()->getParam('id');
    $ship_id = $this->getRequest()->getParam('ship_id');

    if(!$id || !$ship_id){
        echo json_encode(['status' => 400,'message' => 'Invalid Data : Can Not Remove Address']);
        exit();
    }

    $QSA = new Application_Model_ShippingAddress();
    $userStorage          = Zend_Auth::getInstance()->getStorage()->read();

    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    try{

        $data = array(
            'status' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $userStorage->id
        );

        $where            = array();
        $where[]          = $QSA->getAdapter()->quoteInto('id = ?', $ship_id);
        $where[]          = $QSA->getAdapter()->quoteInto('d_id = ?', $id);
        $where[]          = $QSA->getAdapter()->quoteInto('status is null',1);

        $status = $QSA->update($data, $where);

        if($status){
            $db->commit();
            echo json_encode(['status' => 200,'message' => 'Done']);
            exit();
        }

        $db->rollback();
        echo json_encode(['status' => 400,'message' => 'Invalid Data : Remove Address Fail']);
        exit();

    }catch (exception $e){

        $db->rollback();
        echo json_encode(['status' => 400,'message' => 'Invalid Data : ' . $e->getMessage()]);
        exit();
    }
}

public function removeFlagConfirmSo($sn){

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $QMarket = new Application_Model_Market();

// remove flag
    $where = array();
    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

    $data = array(
        'sales_confirm_id' => null,
        'sales_confirm_date' => null,
    );

    $QMarket->update($data, $where);
}

public function addFlagConfirmSo($sn){

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $QMarket = new Application_Model_Market();

// add flag
    $where = array();
    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $where[] = $QMarket->getAdapter()->quoteInto('status = ?', 1);

    $data = array(
        'sales_confirm_id' => $userStorage->id,
        'sales_confirm_date' => date('Y-m-d H:i:s'),
    );

    $QMarket->update($data, $where);
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

public function cpAutoCheckImeiListAction()
{

    set_time_limit(0);
    ini_set('memory_limit', -1);
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
        'action_frm' => 'confirm'
    ));
//print_r($params);//die;
    $QCPAutoCheckImei = new Application_Model_CPAutoCheckImei();
    $QDistributor   = new Application_Model_Distributor();

    $distributors = $QDistributor->get_with_store_code_cache();

    $params['sort'] = $sort;
    $params['desc'] = $desc;

    if (isset($export) && $export) {
        $get_resule = $QCPAutoCheckImei->ImeiAutoCheckList($params);
//print_r($get_resule);
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
    $this->view->url    = HOST . 'sales/cp-auto-check-imei-list/' . ($params ? '?' . http_build_query($params) .
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

//print_r($result);die;
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
    'Distributor Group Name',
    'Area Name',
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
    '???????????????',
    '????????????',
    '????????????? Catty',
    '?????????? CP',
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
    if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = '????????????'; }else { $branch_type = '????'; }
    if ($item['canceled']== 1){$cancel = 'Canceled';}else{$cancel = 'Not Cancel';}

    if($item['check_timing_status'] =='0'){
        $remark_timing=" [???????????????????? ".$item['timing_date']."]";
    }else{
        $remark_timing="";
    }

    if($item['check_activated_status'] =='0'){
        $remark_activated=" [????? activate ?????????????????? ".$item['activated_date']."]";
    }else{
        $remark_activated="";
    }

    if($item['check_out_date_status'] =='0'){
        $remark_out_date=" [????? Sell IN ?????????????????? ".$item['out_date']."]";
    }else{
        $remark_out_date="";
    }

    $status_check="";$remark='';
    $remark = $remark_timing.$remark_activated.$remark_out_date;
    if($remark !=''){
        $status_check="?????????";
    }else{
        $remark='????';
        $status_check="??????";
    }

    $row = array();
    $row[] = $item['imei_sn'];
    $row[] = $item['distributor_id'];
    $row[] = $item['store_code'];
    $row[] = $item['title'];
    $row[] = $item['finance_group'];
    $row[] = $item['distributor_group_name'];
    $row[] = $item['area_name'];
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
        $remark="?????????????? ".$item['creditnote_sn'];
    }
    if($item['type_name']==''){
        $remark="??????? Imei ???????";
    }
    if($item['remark']=='duplicate'){
        $remark="Imei ??????????????????????";
    }
    if($item['remark']=='no_order'){
        $remark="Imei ?????????? Order";
    }

    if($item['remark']=='imei_error'){
        $remark="??????? Imei ???????";
    }

    $row = array();
    $row[] = $item['imei_sn'];
    $row[] = $item['distributor_id'];
    $row[] = $item['store_code'];
    $row[] = $item['title'];
    $row[] = $item['finance_group'];
    $row[] = $item['distributor_group_name'];
    $row[] = $item['area_name'];
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
    $row[] = "?????????";
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

public function openMarketCampaignAction()
{
    ini_set('memory_limit', -1);

    $this->view->meta_refresh = 300;

    $sort               = $this->getRequest()->getParam('sort');
    $desc               = $this->getRequest()->getParam('desc', 0);
    $page               = $this->getRequest()->getParam('page', 1);

    $imei_sn            = $this->getRequest()->getParam('imei_sn');
    $partner_code       = $this->getRequest()->getParam('partner_code');
    $d_id               = $this->getRequest()->getParam('d_id');
    $rank               = $this->getRequest()->getParam('rank');
    $view_status        = $this->getRequest()->getParam('view_status', 1);
    $export             = $this->getRequest()->getParam('export', 0);
    $good_id               = $this->getRequest()->getParam('good_id');
    $phone_no               = $this->getRequest()->getParam('phone_no');

    $price_per_imei     = 400;

    $limit = LIMITATION;
    $total = 0;

    $this->view->rank = $rank;
    $this->view->d_id = $d_id;

    $params = array_filter(array(
        'imei_sn'           => $imei_sn,
        'partner_code'      => $partner_code,
        'rank'              => $rank,
        'd_id'              => $d_id,
        'good_id'           => $good_id,
        'phone_no'           => $phone_no,
        'imei_sn'           => $imei_sn,
        'price_per_imei'    => $price_per_imei,
        'view_status'       => $view_status
    ));

    $QPackedSim     = new Application_Model_PackedSim();
    $QDistributor   = new Application_Model_Distributor();
    $QGood          = new Application_Model_Good();

    $distributors = $QDistributor->get_with_store_code_cache();
    $goods             = $QGood->get_cache();

    $params['sort'] = $sort;
    $params['desc'] = $desc;

    if (isset($export) && $export) {
        $get_resule = $QPackedSim->getDataExportOpenMarketCampaign($params);
        $this->_exportExcelOpenMarketCampaign($get_resule);
    }else{
        $get_resule = $QPackedSim->fetchPagination($page, $limit, $total, $params);
    }

// print_r($get_resule);die;

    $this->view->get_resule     = $get_resule;
    $this->view->distributors      = $distributors;
//print_r($goods);die;
    $this->view->goods             = $goods;

    $this->view->desc   = $desc;
    $this->view->sort   = $sort;
    $this->view->params = $params;
    $this->view->limit  = $limit;
    $this->view->total  = $total;
    $this->view->no_show_brandshop = $no_show_brandshop;
    $this->view->url    = HOST . 'sales/open-market-campaign/' . ($params ? '?' . http_build_query($params) .
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

        $this->_helper->viewRenderer->setRender('partials/open-market-campaign');
    }
}


private function _exportExcelOpenMarketCampaign($data) {

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

//print_r($data);
    require_once 'PHPExcel.php';

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Imei No')
    ->setCellValue('B1', 'Good Name')
    ->setCellValue('C1', 'Good Color')
    ->setCellValue('D1', 'Price Per Imei')
    ->setCellValue('E1', 'SIM Serial')
    ->setCellValue('F1', 'Phone No')
    ->setCellValue('G1', 'Sales In Date')
    ->setCellValue('H1', 'Sales Out Date')
    ->setCellValue('I1', 'SIM Activate Date')
    ->setCellValue('J1', 'Imei Activate Date')
    ->setCellValue('K1', 'Distributor ID')
    ->setCellValue('L1', 'Partner Code')
    ->setCellValue('M1', 'Store Code')
    ->setCellValue('N1', 'Distributor Name')
    ->setCellValue('O1', 'Distributor Type')
    ->setCellValue('P1', 'Operator')
    ->setCellValue('Q1', 'Pay Rebate')
    ->setCellValue('R1', 'Confirm Rebate Date');

    $array_summary_area = array();

    $col1 = 1;
    $col2 = 1;
    $gran_total = 0;
    foreach ($data as $key => $value) {

//->setCellValue('B'.$col1, My_Number::f($value['price_per_imei'], 0, ',', '.'))
        $col1++;$pay_rebate="";
        if($value['no_pay_rebate'] !='1'){
            $pay_rebate="Y";
        }else{
            $pay_rebate="N";
        }

        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$col1, $value['imei_sn'])
        ->setCellValue('B'.$col1, $value['good_name'])
        ->setCellValue('C'.$col1, $value['goodcolor_name'])
        ->setCellValue('D'.$col1, $value['price_per_imei'])
        ->setCellValue('E'.$col1, "'".$value['simcard'])
        ->setCellValue('F'.$col1, "'".$value['tel_no'])
        ->setCellValue('G'.$col1, $value['sellin_datetime'])
        ->setCellValue('H'.$col1, $value['sellout_at'])
        ->setCellValue('I'.$col1, $value['sim_activated_at'])
        ->setCellValue('J'.$col1, $value['activated_at'])
        ->setCellValue('K'.$col1, $value['distributor_id'])
        ->setCellValue('L'.$col1, $value['partner_code'])
        ->setCellValue('M'.$col1, $value['store_code'])
        ->setCellValue('N'.$col1, $value['distributor_name'])
        ->setCellValue('O'.$col1, $value['distributor_type'])
        ->setCellValue('P'.$col1, $value['operator'])
        ->setCellValue('Q'.$col1, $pay_rebate)
        ->setCellValue('R'.$col1, $value['confirm_rebate_date']);

        $gran_total = $value['price_per_imei'];
        if(isset($array_summary_area[$value['distributor_id']])){

            $array_summary_area[$value['distributor_id']]['total_imei'] = $array_summary_area[$value['distributor_id']]['total_imei']+1;
            $array_summary_area[$value['distributor_id']]['total_amount'] = $array_summary_area[$value['distributor_id']]['total_amount']+$gran_total;
        }else{
            $data = array(
                'distributor_id' => $value['distributor_id'],
                'partner_code' => $value['partner_code'],
                'distributor_name' => $value['distributor_name'],
                'operator' => $value['operator'],
                'total_imei' => 1,
                'pay_rebate' => $pay_rebate,
                'total_amount' => $gran_total
            );
            $array_summary_area[$value['distributor_id']] = $data;
        }

    }

    $objPHPExcel->getActiveSheet(0)->setTitle('Details');

    $objWorkSheet = $objPHPExcel->createSheet(1);

    $objPHPExcel->setActiveSheetIndex(1)
    ->setCellValue('A1', 'Distributor ID')
    ->setCellValue('B1', 'Partner Code')
    ->setCellValue('C1', 'Distributor Name')
    ->setCellValue('C1', 'Distributor Type')
    ->setCellValue('D1', 'Operator')
    ->setCellValue('E1', 'Imei Total')
    ->setCellValue('F1', 'Total Amount')
    ->setCellValue('G1', 'Pay Rebate');

    foreach ($array_summary_area as $key => $value) 
    {

        $col2++;

        $objPHPExcel->setActiveSheetIndex(1)
        ->setCellValue('A'.$col2, $value['distributor_id'])
        ->setCellValue('B'.$col2, $value['partner_code'])
        ->setCellValue('C'.$col2, $value['distributor_name'])
        ->setCellValue('C'.$col2, $value['distributor_type'])
        ->setCellValue('D'.$col2, $value['operator'])
        ->setCellValue('E'.$col2, $value['total_imei'])
        ->setCellValue('F'.$col2, $value['total_amount'])
        ->setCellValue('G'.$col2, $value['pay_rebate']);
    }

    $objPHPExcel->getActiveSheet(0)->setTitle('Summary');

    $objPHPExcel->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="report_open_market_campaign_'.time().'.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

    ob_end_clean();
    ob_start();

    $objWriter->save('php://output');
    exit;
}


public function whtManualListAction()
{

    $this->view->meta_refresh = 300;

    $sort               = $this->getRequest()->getParam('sort');
    $desc               = $this->getRequest()->getParam('desc', 0);
    $page               = $this->getRequest()->getParam('page', 1);

    $wht_running_no     = $this->getRequest()->getParam('wht_running_no');
    $import_name        = $this->getRequest()->getParam('import_name');
    $wht_type         = $this->getRequest()->getParam('wht_type');
    $start_date         = $this->getRequest()->getParam('start_date');
    $start_date         = $this->getRequest()->getParam('start_date');

    $distributor_name   = $this->getRequest()->getParam('distributor_name');
    $start_date         = $this->getRequest()->getParam('start_date');
    $end_date           = $this->getRequest()->getParam('end_date');
    $view_status        = $this->getRequest()->getParam('view_status', 1);
    $export             = $this->getRequest()->getParam('export', 0);

    $limit = LIMITATION;
    $total = 0;

    $params = array_filter(array(
        'wht_running_no'        => $wht_running_no,
        'import_name'           => $import_name,
        'wht_type'              => $wht_type,
        'province_name'         => $province_name,
        'topic'                 => $topic,
        'start_date'             => $start_date,
        'end_date'             => $end_date,
        'distributor_name' => $distributor_name,
        'view_status' => $view_status,
        'export' => $export,
        'action_frm' => 'list'
    ));

    $QWithholdingTaxManual = new Application_Model_WithholdingTaxManual();

    $params['sort'] = $sort;
    $params['desc'] = $desc;

    if (isset($export) && $export) {
        $get_resule = $QWithholdingTaxManual->whtfetchPagination($page, null, $total, $params);
//print_r($get_resule);die;
        $this->_exportExcelWithholdingTax($get_resule);
    }

    $get_resule = $QWithholdingTaxManual->whtfetchPagination($page, $limit, $total, $params);

// print_r($get_resule);die;

    $this->view->get_resule     = $get_resule;
    $this->view->distributors      = $distributors;

    $this->view->desc   = $desc;
    $this->view->sort   = $sort;
    $this->view->params = $params;
    $this->view->limit  = $limit;
    $this->view->total  = $total;
    $this->view->no_show_brandshop = $no_show_brandshop;
    $this->view->url    = HOST . 'sales/wht-manual-list/' . ($params ? '?' . http_build_query($params) .
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

        $this->_helper->viewRenderer->setRender('partials/wht-manual-list');
    }
}


private function _exportExcelWithholdingTax($data) {

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

//print_r($data);
    require_once 'PHPExcel.php';

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'RUNNING NO')
    ->setCellValue('B1', '????????????')
    ->setCellValue('C1', '???????')
    ->setCellValue('D1', '??????')
    ->setCellValue('E1', 'Distributor Name')
    ->setCellValue('F1', 'Distributor Tax')
    ->setCellValue('G1', '1 ??????????????')
    ->setCellValue('H1', '1 ??????????')
    ->setCellValue('I1', '1 ??????????????????????')
    ->setCellValue('J1', '1 ???????')
    ->setCellValue('K1', '1 ??????????')
    ->setCellValue('L1', '2 ??????????????')
    ->setCellValue('M1', '2 ??????????')
    ->setCellValue('N1', '2 ??????????????????????')
    ->setCellValue('O1', '2 ???????')
    ->setCellValue('P1', '2 ??????????')
    ->setCellValue('Q1', '???????????????')
    ->setCellValue('R1', '??????????????????')
    ->setCellValue('S1', '???????');

    $array_summary_area = array();

    $col1 = 1;
    $col2 = 1;
    $gran_total = 0;
    foreach ($data as $key => $value) {
        $col1++;
        if($value['wht_type']==1){
            $wht_type_name="WP";
        }else if($value['wht_type']==2){
            $wht_type_name="WR";
        }else if($value['wht_type']==3){
            $wht_type_name="WO";
        }else if($value['wht_type']==4){
            $wht_type_name="WA";
        }else if($value['wht_type']==5){
            $wht_type_name="WS";
        }else if($value['wht_type']==6){
            $wht_type_name="WT";
        }else{
            $wht_type_name="";
        }
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$col1, $value['wht_running_no'])
        ->setCellValue('B'.$col1, $wht_type_name)
        ->setCellValue('C'.$col1, $value['province_name'])
        ->setCellValue('D'.$col1, $value['topic'])
        ->setCellValue('E'.$col1, $value['distributor_name'])
        ->setCellValue('F'.$col1, "'".$value['distributor_tax_no'])
        ->setCellValue('G'.$col1, $value['payment_name_01'])
        ->setCellValue('H'.$col1, $value['payment_type_wht_vat_01'])
        ->setCellValue('I'.$col1, $value['payment_date_01'])
        ->setCellValue('J'.$col1, $value['payment_price_01'])
        ->setCellValue('K'.$col1, $value['payment_wht_vat_01'])
        ->setCellValue('L'.$col1, $value['payment_name_02'])
        ->setCellValue('M'.$col1, $value['payment_type_wht_vat_02'])
        ->setCellValue('N'.$col1, $value['payment_date_02'])
        ->setCellValue('O'.$col1, $value['payment_price_02'])
        ->setCellValue('P'.$col1, $value['payment_wht_vat_02'])
        ->setCellValue('Q'.$col1, $value['staff_name'])
        ->setCellValue('R'.$col1, $value['create_date'])
        ->setCellValue('S'.$col1, $value['address_tax']);                    
    }

    $objPHPExcel->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="report_withholding_tax_manual_list_'.time().'.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

    ob_end_clean();
    ob_start();

    $objWriter->save('php://output');
    exit;
}

function cellColor($cells,$color){

    require_once 'PHPExcel.php';
//global $objPHPExcel;
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
           'rgb' => $color
       )
    ));
}

private function _exportExcelPrivilegesStaffOrder($data) 
{

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

//echo"<pre>";print_r($data);die;

    $year          = date('Y');
    require_once 'PHPExcel.php';

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', '')
    ->setCellValue('B1', '')
    ->setCellValue('C1', '')
    ->setCellValue('D1', '')
    ->setCellValue('E1', '')
    ->setCellValue('F1', '')
    ->setCellValue('G1', '')
    ->setCellValue('H1', '')
    ->setCellValue('I1', '??????????????????????????????? '.($year+543))
    ->setCellValue('J1', '')
    ->setCellValue('K1', '')
    ->setCellValue('L1', '')
    ->setCellValue('M1', '')
    ->setCellValue('N1', '')
    ->setCellValue('O1', '')
    ->setCellValue('P1', '')
    ->setCellValue('Q1', '')
    ->setCellValue('R1', '')
    ->setCellValue('S1', '')
    ->setCellValue('T1', '')
    ->getStyle('A1:T1')
    ->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->getStartColor()
    ->setRGB('FFD732')
    ;

//$this->cellColor('A1:Q1', 'F28A8C');

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A2', '?????')
    ->setCellValue('B2', '????????????')
    ->setCellValue('C2', '?????? Order')
    ->setCellValue('D2', '????/??????')
    ->setCellValue('E2', '??')
    ->setCellValue('F2', '???????????')
    ->setCellValue('G2', '???? - ???????')
    ->setCellValue('H2', '???????')
    ->setCellValue('I2', '????/????')
    ->setCellValue('J2', '??????????????')
    ->setCellValue('K2', '????????????')
    ->setCellValue('L2', '??????')
    ->setCellValue('M2', '?????')
    ->setCellValue('N2', '???????')
    ->setCellValue('O2', '?????? Requst')
    ->setCellValue('P2', '???')
    ->setCellValue('Q2', '?????????????')
    ->setCellValue('R2', '??????????????')
    ->setCellValue('S2', '????????')
    ->setCellValue('T2', '?????????')
    ->getStyle('A2:T2')
    ->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->getStartColor()
    ->setRGB('AAFF00')
    ;


    $array_summary_area = array();


    $col1 = 2;
    $col2 = 1;
    $gran_total = 0;$i=1;
    foreach ($data as $key => $value) {

        $col1++;
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$col1, $i)
        ->setCellValue('B'.$col1, $value['privileges_no'])
        ->setCellValue('C'.$col1, $value['sales_order_no'])
        ->setCellValue('D'.$col1, $value['good_name'])
        ->setCellValue('E'.$col1, $value['good_color_name'])
        ->setCellValue('F'.$col1, $value['staff_code'])
        ->setCellValue('G'.$col1, $value['staff_name'])
        ->setCellValue('H'.$col1, $value['position_name'])
        ->setCellValue('I'.$col1, $value['department_name'])
        ->setCellValue('J'.$col1, number_format($value['master_unit_price'],2))
        ->setCellValue('K'.$col1, $value['discount_type_name'])
        ->setCellValue('L'.$col1, number_format($value['total_price'],2))
        ->setCellValue('M'.$col1, $value['qty'])
        ->setCellValue('N'.$col1, number_format($value['total_amount'],2))
        ->setCellValue('O'.$col1, $value['create_date'])
        ->setCellValue('P'.$col1, $value['provice_name'])
        ->setCellValue('Q'.$col1, $value['delivery_address'])
        ->setCellValue('R'.$col1, "")
        ->setCellValue('S'.$col1, "")
        ->setCellValue('T'.$col1, "")
        ->getStyle('A'.$col1.':Q'.$col1)
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB('FAFF50')
        ; 
        $i +=1;                          
    }

    $objPHPExcel->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="report_privileges_staff_order_list_'.time().'.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

    ob_end_clean();
    ob_start();

    $objWriter->save('php://output');
    exit;
}

// by ice
public function exportshippingAction(){

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
// echo "test"; die;
$d_id = 30344; //---?????? ?????? ???????????????? ????? (?????)

$QShippingaddress = new Application_Model_ShippingAddress();

$data = $QShippingaddress->data_export_excel($d_id);
$this->export_shipping_address($data);
}

private function export_shipping_address($data){
// echo "TT"; die;
    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', '200M');
    $filename = 'shipping_address_big-c_' . date( "m-d-Y" ) . ".csv";

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
    'ID Shipping Address',
    'Contact Name',
    'Address'
);

fputcsv($output, $heads);

// $i = 1;

foreach ($data as $item) {
    $row = array();
// $row[] = $i;
    $row[] = $item['id'];
    $row[] = $item['contact_name'];
    $row[] = $item['address'];

    fputcsv($output, $row);
    unset($item);
    unset($row);
// $i +=1;

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

//-------delete map-keyproduct lazada----
public function delkeyAction()
{
    $id = $this->getRequest()->getParam('id');

    $m = new Application_Model_CsvImport();
    $where = $m->getAdapter()->quoteInto('id = ?', $id);

    $m->delete($where);
    $this->_redirect('/sales/list-map-keyproduct');

}

public function ajaxSaveDistributorRemarkAction()
{

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $d_id = $this->getRequest()->getParam('d_id');
    $remark = $this->getRequest()->getParam('remark');

    $QDistributor = new Application_Model_Distributor();

    $where = $QDistributor->getAdapter()->quoteInto('id = ?', $d_id);
    $status = $QDistributor->update(['remark' => $remark], $where);

    echo json_encode(['status' => 200,'message' => 'Add Remark Success.']);
    exit();
}


private function _exportSalesRequest($data){
// echo "TT"; die;
    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', '200M');
    $filename = 'sales_request_' . date( "m-d-Y" ) . ".csv";

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
    'SALES REQUEST NUMBER',
    'SALES ORDER NUMBER',
    'STORE CODE',
    'DISTRIBUTOR NAME',
    'REQUEST STAFF CODE',
    'REQUEST BY',
    'REQUEST DATE',
    'STATUS',
    'ADMIN NAME',
    'ADMIN CONFIRM DATE',
    'REMARK',
    'USE CN',
    'GOOD_CODE',
    'GOOD_COLOR',
    'QUANTITY',
);

fputcsv($output, $heads);

// $i = 1;

foreach ($data as $item) 
{
    $status_name="";$use_cn="";
    if($item['status']==2)
    {
        $status_name= 'Confirm';
    }else if($item['status']==3){
        $status_name= 'Cancel';
    }else{
        $status_name= 'Wait';
    }

    if($item['use_cn']==1)
    {
        $use_cn= '????? CN';
    }else{
        $use_cn= '';
    }

    $row = array();
// $row[] = $i;
    $row[] = $item['presales_no'];
    $row[] = $item['sales_order_no'];
    $row[] = $item['store_code'];
    $row[] = $item['title'];

    $row[] = $item['request_staff_code'];
    $row[] = $item['sell_name'];
    $row[] = $item['request_date'];

    $row[] = $status_name;
    $row[] = $item['admin_name'];
    $row[] = $item['admin_confirm_date'];

    $row[] = $item['sell_remark'];
    $row[] = $use_cn;
    $row[] = $item['good_name'];
    $row[] = $item['good_color_name'];
    $row[] = $item['qty'];

    fputcsv($output, $row);
    unset($item);
    unset($row);
// $i +=1;

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
public function  testAction(){
    ini_set('memory_limit', '-1');
    set_time_limit(0);
    $db = Zend_Registry::get('db');
    $QDistributor = new Application_Model_Distributor();
    $QDistributorMapping = new Application_Model_DistributorMapping();
    $QDistributorMap_online = new Application_Model_DistributorMapOnline();
    $OnlineOrderTransaction = new Application_Model_OnlineOrderTransaction();  
    $flashMessenger   = $this->_helper->flashMessenger;
    $this->_helper->layout->disableLayout();
    $userStorage   = Zend_Auth::getInstance()->getStorage()->read();
    $this->_helper->viewRenderer->setNoRender(true);
    $add_time = date('Y-m-d H:i:s');
    $time2 =time()-259200;
    $time1 =time();
    $sales_ch = 'JD';
    $warehouse_id = '125';
    $add = '(JD)';
    $finance_group="B09_JD";
    $store_code = $QDistributor->getDistributorCode($db,$sales_ch);

    $c = new ApiManager();
    $c->appKey = "8cf91f3725ebafe856f8886b78970c87";
    $c->appSecret = "e4aca97f9e22e10d8f8c935ce73372a7";
    $c->accessToken = "c0ddd29ee9222b305dac6c749367b089";
    $c->serverUrl = "http://open.jd.co.th/api"; 
    $c->method = "jingdong.PopAdminExport.queryListNew";
    $data=array ('queryParams' => array (
        'beginTime' =>(string)$time2,
        'endTime' => (string)$time1,
        'originStatuList' => 
        array (
            0 => 25,
        ),
        'pageInfo' => 
        array (
            'pageSize' => 20,
            'pageStart' => 1,
        ),
    ),
);

    $c->param_json = json_encode($data);
    $resp = $c->call();
    $de = json_decode($resp,true);
    echo "<pre>";
    $a1 =json_decode($de['openapi_data'],true);

    foreach ($a1['thailandPopOrderDocumentVOs'] as $key => $val) {
        foreach ($val['skus']as $key => $value1) {
            $e = new ApiManager();
            $e->appKey = "8cf91f3725ebafe856f8886b78970c87";
            $e->appSecret = "e4aca97f9e22e10d8f8c935ce73372a7";
            $e->accessToken = "c0ddd29ee9222b305dac6c749367b089";
            $e->serverUrl = "http://open.jd.co.th/api"; 
            $e->method = "jingdong.gms.ItemModelGlobalService.getItemModelById";
            $data=
            '{
               "skuId": '.$value1['id'].',
               "searchCondition": {
                "containBaseInfo": true,
                "containImageList": true,
                "containImageListAll": true,
                "containProductProperty": true,
                "containSkuProperty": true,
                "source": "SELF"
            }
        }';
        $e->param_json = $data;
        $resp1 = $e->call();
        $de2 = json_decode($resp1,true);
        $a2 =json_decode($de2['openapi_data'],true);

        foreach ($a2['obj']['skuList'] as $key => $value) {
            if($value1['id']==$a2['obj']['skuList'][$key]['skuId']){
               $code  = $a2['obj']['skuList'][$key]['upcCode'] ; 
               $color = $a2['obj']['skuList'][$key]['saleAttList'][0]['saleValue'];
               $price = $value1['price'];
               $num   = $value1['num'];
               $order_number = $val['order_id'];
               $name  = $val['consignee_name'];
               $phone  = substr($val['consignee_phone'],6,10);
               $address  = $val['addr_info']['address'];
               $post_code  = $val['post_code'];
               $chk_customer = $this->check_customer2($name,$phone);
               if(is_array($chk_customer)==0){
                   $data = array(
                    'title'         => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $name)),
                    'name'          => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $name)),
                    'tel'           => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $phone)),
                    'warehouse_id'  => intval($warehouse_id),
                    'region'        => intval('5902'),
                    'district'      => intval('5903'),
                    'add'           => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $address.' '.$post_code)),
                    'add_tax'       => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $add.' '.$address.' '.$post_code)),
                    'admin'         => 0,
                    'rank'          => intval('3'),
                    'unames'        => trim(preg_replace(array('/\s{2,}/', '/[\t\n]+/'), ' ', $customer)),
                    'mst_sn'        => '',
                    'branch_no'     => '00000',
                    'credit_amount' => '0.00',
                    'credit_type'   => 6,
                    'parent'        => 0,

                    'retailer_type' => 1,
                    'is_ka'         => 0,
                    'is_internal'   => 0,
                    'credit_status' => 0,
                    'finance_group' => $finance_group,
                    'activate'      => 1
                );


                   $data['add_time'] = $add_time;
                   $data['create_by'] = 1;
                   $data['create_date'] = $add_time;
                   $data['store_code'] = $store_code;
                   $data['sales_ch'] = $sales_ch;
                   $data['group_id'] = 5;
                   $distributor_id = $QDistributor->insert($data);
               }else{
                $distributor_id = $chk_customer['id']; 
            }

            $data_map = array(
                'distributor_id_online' => $distributor_id,
                'order_number' => $order_number,
                'address'     => $address,
                'create_date'  => date('Y-m-d H:i:s'),
                'create_by'    => 1 ,
            );
            $QDistributorMap_online->insert($data_map);

            $QMappingColorOnline = new Application_Model_MappingColorOnline();
            $map_color = $QMappingColorOnline->checkcolor($color);
            $pro_color_id =  $map_color['good_color_id'];
            $QGood = new Application_Model_Good();
            $pro=$QGood->getId($code);  
            if((!empty($pro))&&(!empty($map_color))){
                $pro_id = $pro['id'];
                $pro_cat = $pro['cat_id'];
                $QGoodBundleOnline = new Application_Model_GoodBundleOnline();
                $bundleonline = $QGoodBundleOnline->getGood($pro_id,$pro_color_id,$add_time);
                if(!empty($bundleonline)){
                 $ids=[];
                 $cat_ids=[];
                 $good_ids=[];
                 $good_colors=[];
                 $nums=[];
                 $prices=[];
                 $totals=[];
                 $texts=[];
                 $sale_off_percents=[];
                 $campaign=[];
                 array_push($ids, '');
                 array_push($cat_ids, $pro_cat);
                 array_push($good_ids, $pro_id);
                 array_push($good_colors, $pro_cat); 
                 array_push($nums, $num); 
                 array_push($prices, $price);
                 array_push($totals, 100);
                 array_push($texts, ''); 
                 array_push($sale_off_percents, 0);
                 array_push($campaign, '');
                 foreach ($bundleonline as $key => $value) {
                     array_push($ids, '');
                     array_push($cat_ids, $value['bundle_good_cat_id']);
                     array_push($good_ids, $value['bundle_good_id']);
                     array_push($good_colors, $value['bundle_color_id']); 
                     array_push($nums, $value['bundle_qty']); 
                     array_push($prices, 0);
                     array_push($totals, 0);
                     array_push($texts, ''); 
                     array_push($sale_off_percents, 100);
                     array_push($campaign, '');
                 }


             }else{
                $ids=[];
                $cat_ids=[];
                $good_ids=[];
                $good_colors=[];
                $nums=[];
                $prices=[];
                $totals=[];
                $texts=[];
                $sale_off_percents=[];
                $campaign=[];
                array_push($ids, '');
                array_push($cat_ids, $pro_cat);
                array_push($good_ids, $pro_id);
                array_push($good_colors, $pro_cat); 
                array_push($nums, $num); 
                array_push($prices, $price);
                array_push($totals, 100);
                array_push($texts, ''); 
                array_push($sale_off_percents, 0);
                array_push($campaign, '');

            }


            $insert = [] ;
            $insert['order_type'] = 1 ;
            $insert['market_general_id'] = 0 ;
            $insert['ids'] = $ids ;
            $insert['save_service'] = 'sales';
            $insert['cat_id'] =  $cat_ids ;
            $insert['good_id'] = $good_ids ;
            $insert['good_color'] =  $good_colors ;
            $insert['num'] =  $nums ;
            $insert['price'] =  $prices ;
            $insert['total'] =  $totals ;
            $insert['text'] =  $texts ;
            $insert['distributor_id'] = $distributor_id ;
            $insert['warehouse_id'] =  $warehouse_id  ;
            $insert['salesman'] =  0 ;
            $insert['sales_catty_id'] =  '' ;
            $insert['area_id'] =  '' ;
            $insert['type'] =  1 ;
            $insert['sale_off_percent'] =  $sale_off_percents ;
            $insert['sn'] =  '' ;
            $insert['life_time'] =  1 ;
            $insert['isbatch'] =  1 ;
            $insert['rebate_price'] =  '' ;
            $insert['service_id'] =  '' ;
            $insert['ids_bvg'] =  '' ;
            $insert['joint'] =  '' ;
            $insert['good_id_bvg'] =  '' ;
            $insert['num_bvg'] =  '' ;
            $insert['total_bvg'] =  '' ;
            $insert['joint_discount'] =  '' ;
            $insert['ids_discount'] =  '' ;
            $insert['prices_discount'] =  '' ;
            $insert['bvg_imei'] =  '' ;
            $insert['distributor_po'] =  '' ;
            $insert['gift_id'] =  '' ;
            $insert['include_shipping_fee'] =  1 ;
            $insert['user_uncheck'] =  1 ;
            $insert['campaign'] =  0 ;
            $insert['payment_method'] = '' ;
            $insert['id_staff'] =  '' ;
            $insert['name_staff_ingame'] =  '' ;
            $insert['cmnd_staff_ingame'] =  '' ;
            $insert['shipment_type'] =  '' ;
            $insert['sophieuthu'] =  '' ;
            $insert['sotienthucte'] =  '' ;
            $insert['payment_date'] =  '' ;
            $insert['shipment_id'] =  '' ;
            $insert['product_color_key'] = '' ;
            $insert['staff_num'] =  '' ;
            $insert['for_partner'] =  '' ;
            $insert['credit_id'] =  '' ;
            $insert['delivery_fee'] =  '' ;
            $insert['customer_id'] =  '' ;
            $insert['customer_name'] =  '' ;
            $insert['customer_tax_number'] =  '' ;
            $insert['customer_branch_number'] =  '' ;
            $insert['customer_tax_address'] =  '' ;
            $insert['rank'] =  3 ;
            $insert['edit'] =  '' ;
            $insert['sipping_add'] =  '' ;
            $insert['customer_name_for_staff'] =  '' ;
            $insert['total_spc_discount'] =  0 ;
            $insert['digital_discount'] =  '' ;
            $insert['market_general_data'] =  0 ;
            $insert['creditnote_data'] =  '' ;
            $insert['deposit_data'] =  '' ;
            $checkforinsert  = $OnlineOrderTransaction->checkforinsert($order_number); 
            if( $checkforinsert['status_order'] != 1 && $checkforinsert['status_order'] != 2   ){
                $result = $this->saveAPI($insert);
            }

            $tag=[];
            array_push($tags, $order_number);
            if ($result['code'] == 1) { //success 
                //print_r($result);
                //update discount when created
                if($edit != 1){ 
                    $QMarket = new Application_Model_Market();
                    
                    $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
                    $distributor = $QDistributor->fetchRow($where);


                    // Check Special Discount
                    if(isset($warehouse_id) and $warehouse_id == 71){
                        $total_discount_digital = $digital_discount + $distributor['spc_discount'];
                        $spc_discount = $total_discount_digital;
                    }else{
                        $spc_discount = $distributor['spc_discount'];
                    }
                    
                    $spc_discount_phone = $distributor['spc_discount_phone'];
                    $spc_discount_acc = $distributor['spc_discount_acc'];
                    $spc_discount_digital = $distributor['spc_discount_digital'];

                    if(isset($total_discount_digital) and $total_discount_digital > 0){
                        $spc_discount_digital = 1;
                    }
                    
                    $array_data = array('spc_discount' => $spc_discount,
                        'spc_discount_phone' => $spc_discount_phone,
                        'spc_discount_acc' => $spc_discount_acc,
                        'spc_discount_digital' => $spc_discount_digital);

                    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $result['sn']);
                    $QMarket->update($array_data, $where); 
                }

                if( ($checkforinsert['status_order'] == 3) || ($checkforinsert['status_order']== 4) ){
                    $data3['order_id']=$result['sn'];
                    $data3['order_time'] = $add_time;
                    $data3['status_order']= 1;
                    $where = $OnlineOrderTransaction->getAdapter()->quoteInto('order_online_id = ?', $order_number);
                    $OnlineOrderTransaction->update($data3,$where); 

                }elseif($checkforinsert['status_order'] != 1 && $checkforinsert['status_order'] != 2 ){
                    if($checkforinsert['order_online_id'] != $order_number ){
                        $data3['order_id']=$result['sn'];
                        $data3['order_time'] = $add_time;
                        $data3['order_online_id']=$order_number;
                        $data3['order_online_time']=$val['order_time'];
                        $data3['chanel'] = 1;
                        $data3['created_at'] = $add_time; 
                        $data3['status_order']= 1;
                        $data3['error_message'] = '';
                        $OnlineOrderTransaction->insert($data3); 
                    }


                }


                $success_list[] = $insert;  
                $flashMessenger->setNamespace('success')->addMessage($result['message']);

            }else if ($result['code'] == -5){
              if( ($checkforinsert['status_order'] == 3) || ($checkforinsert['status_order']== 4) ){
                $data3['order_id']=$result['sn'];
                $data3['order_time'] = $add_time;
                $data3['status_order']= 1;
                $where = $OnlineOrderTransaction->getAdapter()->quoteInto('order_online_id = ?', $order_number);
                $OnlineOrderTransaction->update($data3,$where); 

            }elseif($checkforinsert['status_order'] != 1 && $checkforinsert['status_order'] != 2 ){
                if($checkforinsert['order_online_id'] != $order_number ){
                    $data3['order_id']=$result['sn'];
                    $data3['order_time'] = $add_time;
                    $data3['order_online_id']=$order_number;
                    $data3['order_online_time']=$val['order_time'];
                    $data3['chanel'] = 1;
                    $data3['created_at'] = $add_time; 
                    $data3['status_order']= 1;
                    $data3['error_message'] = '';
                    $OnlineOrderTransaction->insert($data3); 
                }


            }
        } else {
          if( ($checkforinsert['status_order'] == 3) || ($checkforinsert['status_order']== 4) ){
            $data3['order_id']=$result['sn'];
            $data3['order_time'] = $add_time;
            $data3['status_order']= 1;
            $where = $OnlineOrderTransaction->getAdapter()->quoteInto('order_online_id = ?', $order_number);
            $OnlineOrderTransaction->update($data3,$where); 

        }elseif($checkforinsert['status_order'] != 1 && $checkforinsert['status_order'] != 2 ){
            if($checkforinsert['order_online_id'] != $order_number ){
                $data3['order_id']=$result['sn'];
                $data3['order_time'] = $add_time;
                $data3['order_online_id']=$order_number;
                $data3['order_online_time']=$val['order_time'];
                $data3['chanel'] = 1;
                $data3['created_at'] = $add_time; 
                $data3['status_order']= 1;
                $data3['error_message'] = '';
                $OnlineOrderTransaction->insert($data3); 
            }


        }
    }


}else{

  if( ($checkforinsert['status_order'] == 3) || ($checkforinsert['status_order']== 4) ){
    $data3['order_id']='';
    $data3['order_time'] = '';
    $data3['status_order']= 1;
    $where = $OnlineOrderTransaction->getAdapter()->quoteInto('order_online_id = ?', $order_number);
    $OnlineOrderTransaction->update($data3,$where); 

}
}



echo "Order : ";
print_r($order_number); 
echo "<br>";
echo "Product CODE : ";
print_r($code); 
echo "<br>";
echo "Name : ";
print_r($name); 
echo "<br>";
echo "Address : ";
print_r($address); 
echo "<br>";
echo "Tel : ";
print_r($phone); 
echo "<br>";
echo "Color : ";
print_r($color);
echo "<br>";
echo "Price : ";
print_r($price);
echo "<br>";
echo "QTY : ";
print_r($num);
echo "<br>";
echo "Postcode  : ";
print_r($post_code);
echo "<br>--------------------------------";

echo "<br>";

}
file_get_contents(HOST."cron/gen-sn-ref");

}


}
}



}

public function  viewOnlineOrderAction(){
    $from_date      = $this->getRequest()->getParam('from_date');
    $to_date        = $this->getRequest()->getParam('to_date');
    $order_number        = $this->getRequest()->getParam('order_number');


    $OnlineOrderTransaction = new Application_Model_OnlineOrderTransaction();
    $params = array(
        'from_date'     => $from_date,
        'to_date'       => $to_date,
        'order_number'       => $order_number
    );

    $online = $OnlineOrderTransaction->fetchPagination($page, $limit, $total, $params);
    $this->view->logs = $online;
    $this->view->params = $params;
    $this->view->limit  = 20;
    $this->view->total  = $total;
    $this->view->url    = HOST.'product/color-online'.( $params ? '?'.http_build_query($params).'&' : '?' );
    $this->view->offset = $limit*($page-1);
// echo "<pre>";
// print_r($online);
// die;

    $this->_helper->viewRenderer->setRender('view-online-order/list');




}


// Export Compare dealer shop placing an order
private function _exportCheckOrderExcel($data)
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Check_Order_list -  '.date('d-m-Y H-i-s').'.csv';

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
    'No',
    'Distributor ID',
    'Distributor Name',
    'Order Qulity',
    'Order From Warehouse',
    'Distributor Status',
);
fputcsv($output, $heads);
$QWarehouse = new Application_Model_Warehouse();
$warehouse = $QWarehouse->get_cache();

$i = 1;
foreach($data as $item) {

    if($item['total_qulity'] > 0){ $qulity = $item['total_qulity']; }else{ $qulity = "0"; }

    if($item['status'] == 0){
        $status = "Enable";
    }else{
        $status = "Disable";
    }

    $row = array();
    $row[] = $i;
    $row[] = $item['distributors_id'];
    $row[] = $item['distributors_name'];
    $row[] = $qulity;
    $row[] = $warehouse[$item['warehouse_name']];
    $row[] = $status;

    fputcsv($output,$row);
    unset($item);
    unset($row);
    $i++;
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

// Export Compare dealer shop placing an order
private function _exportImeiCheckOrderExcel($data)
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Check_Order_list -  '.date('d-m-Y H-i-s').'.csv';

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
    'No',
    'Imei',
    'Product Name',
    'Color Name',
    'Distributor ID',
    'Distributor Name',
// 'Order Qulity',
    'Order From Warehouse',
    'Distributor Status',
);
fputcsv($output, $heads);
$QWarehouse = new Application_Model_Warehouse();
$warehouse = $QWarehouse->get_cache();
$QGood = new Application_Model_Good();
$good = $QGood->get_cache();
$QGoodColor = new Application_Model_GoodColor();
$color = $QGoodColor->get_cache();

$i = 1;
foreach($data as $item) {

    if($item['total_qulity'] > 0){ $qulity = $item['total_qulity']; }else{ $qulity = "0"; }

    if($item['status'] == 0){
        $status = "Enable";
    }else{
        $status = "Disable";
    }

    $row = array();
    $row[] = $i;
    $row[] = $item['imei_sn'];
    $row[] = $good[$item['good_id']];
    $row[] = $color[$item['good_color']];
    $row[] = $item['distributors_id'];
    $row[] = $item['distributors_name'];
    $row[] = $warehouse[$item['warehouse_name']];
// $row[] = $qulity;
    $row[] = $status;

    fputcsv($output,$row);
    unset($item);
    unset($row);
    $i++;
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

// Export Compare dealer shop placing an order by model
private function _exportModelCheckOrderExcel($data)
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Check_Order_Model -  '.date('d-m-Y H-i-s').'.csv';

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
    'No',
    'Distributor ID',
    'Distributor Name',
    'Category',
    'Model Name',
// 'Color',
    'Quantity',
    'Area',
    'Province',
    'Warehouse',
    'Distributor Status',
);
fputcsv($output, $heads);
$QWarehouse = new Application_Model_Warehouse();
$warehouse = $QWarehouse->get_cache();

$i = 1;
foreach($data as $item) {

    if($item['total_qulity'] > 0){ $qulity = $item['total_qulity']; }else{ $qulity = "0"; }

    if($item['status'] == 0){
        $status = "Enable";
    }else{
        $status = "Disable";
    }

    $row = array();
    $row[] = $i;
    $row[] = $item['distributors_id'];
    $row[] = $item['distributors_name'];
    $row[] = $item['category'];
    $row[] = $item['model_name'];
    // $row[] = $item['model_color'];
    $row[] = $qulity;
    $row[] = $item['area_name'];
    $row[] = $item['province'];
    $row[] = $warehouse[$item['warehouse_name']];
    $row[] = $status;

    fputcsv($output,$row);
    unset($item);
    unset($row);
    $i++;
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

private function _exportReturnSaleExcelCNForFinance($data)
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Return_List_For_Finance_ '.date('d-m-Y H-i-s').'.csv';

    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');

    header('Content-Disposition: attachment; filename='.$filename);

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);


// $path = $file_path.'/'.$filename;
// $output = fopen($path, 'w+');
// echo "\xEF\xBB\xBF"; // UTF-8 BOM

echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
$output = fopen('php://output', 'w');

$heads = array(
    'No',
    'SALE ORDER NUMBER',
    'INVOICE NUMBER',
    'DISTRIBUTOR CODE',
    'DISTRIBUTOR NAME',
    'AREA',
    // 'FINANCE CODE',
    'DISTRIBUTOR CODE AND NAME',
    'ORDER TYPE',
    'PRODUCT NAME',
// 'PRODUCT COLOR',
    'QUANTITY',
    'UNIT PRICE EX',
    'TOTAL EX',
// 'TOTAL',
    'CREATE ORDERTIME',
    'CREATE BY',
    'WAREHOUSE',
    'CONFIRM RETURNTIME',
    'CONFIRM BY',
    'STATUS'

);
fputcsv($output, $heads);

$QGood = new Application_Model_Good();
$QGoodColor = new Application_Model_GoodColor();
$QMarket = new Application_Model_Market();
$QDistributor = new Application_Model_Distributor();
$QGoodCategory = new Application_Model_GoodCategory();
$QWarehouse = new Application_Model_Warehouse();
$Qstaff = new Application_Model_Staff();
$QBrand = new Application_Model_Brand();

$staff = $Qstaff->get_cache();
$goods = $QGood->get_cache();
$goodColors = $QGoodColor->get_cache();
$distributors = $QDistributor->get_cache();
$good_categories = $QGoodCategory->get_cache();
$warehouses_cached = $QWarehouse->get_cache();

$markets = array();
foreach ($data as $key => $m)
{
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
    $markets[$m['sn']] = $QMarket->fetchAll($where);
}

$i = 1;
$old_sn = '';
foreach($data as $item) {
    if($item['sn'] == $old_sn){
        $count_row++;
    }else{
        $count_row = 0;
    }

    $old_sn = $item['sn'];
    $d_id = $item['d_id'];

    if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; }
    else { $temp_sn = $item['sn_ref']; }

    if (is_null($item['invoice_number']) || $item['invoice_number'] == ''){
        $temp_invoice = $item['invoice_number'];}
        else{
            $temp_invoice = $item['invoice_number'];
        }
        if (is_null($item['areaname']) || $item['areaname'] == '') {
            $temp_area = $item['areaname'];}
            else{
                $temp_area = $item['areaname']; 
            }

            if (is_null($item['d_id']) || $item['d_id'] == ''){
                $temp_did = $item['d_id'];}
                else{
                    $temp_did = $item['d_id'];
                }

                if(is_null($item['distributor_code']) || $item['distributor_code'] == ''){
                    $temp_distributor_code = $item['distributor_code'];
                }else{
                    $temp_distributor_code = $item['distributor_code'];
                }

                if (is_null($item['title']) || $item['title'] == ''){
                    $temp_title = $item['distributor_code'].' - '.$item['title'];
                    $temp_name = $item['title'];
                }else{
                    $temp_name = $item['title'];
                    $temp_title = $item['distributor_code'].' - '.$item['title'];
                }

                if (is_null($good_categories[$item['cat_id']]) || $good_categories[$item['cat_id']] == ''){
                    $temp_cg = $good_categories[$item['cat_id']];}
                    else{
                        $temp_cg = $good_categories[$item['cat_id']];
                    }

                    if (is_null($staff[$item['pay_user']]) || $staff[$item['pay_user']] == '') {
                        $temp_user = $staff[$item['pay_user']];
                    }else{
                        $temp_user = $staff[$item['pay_user']];
                    }

                    if(is_null($staff[$item['outmysql_user']]) || $staff[$item['outmysql_user']] == ''){
                        $confirm_user = $staff[$item['outmysql_user']];}
                        else{
                            $confirm_user = $staff[$item['outmysql_user']];
                        }


                        if (is_null($item['outmysql_time']) || $item['outmysql_time'] == ''){
                            $temp_pay = date('Y-m-d', strtotime($item['outmysql_time']));}
                            else{
                                $temp_pay = date('Y-m-d', strtotime($item['outmysql_time']));
                            }

                            if (is_null($item['add_time']) || $item['add_time'] == ''){
                                $temp_addt = date('Y-m-d', strtotime($item['add_time']));}
                                else{
                                    $temp_addt = date('Y-m-d', strtotime($item['pay_time']));
                                }

                                if (is_null($warehouses_cached[$item['warehouse_id']]) || $warehouses_cached[$item['warehouse_id']] == ''){
                                    $temp_house = $warehouses_cached[$item['warehouse_id']];}
                                    else{
                                        $temp_house = $warehouses_cached[$item['warehouse_id']];
                                    }

                                    if (isset($goods) && isset($goods[$item['good_id']]))
                                        $good_name = $goods[$item['good_id']];

                                    if (isset($goodColors) && isset($goodColors[$item['good_color']]))
                                        $good_color = $goodColors[$item['good_color']];

                                    $brands = $QBrand->getBrand($item['good_id']);
                                    $brands_name = $brands[0]['brand_name'];


                                    if (isset($distributors) && isset($distributors[$item['d_id']]))
                                        $distributor = $distributors[$item['d_id']];
                                    else
                                        $distributor = '';

                                    if ($item['return_type']==1){
                                        $order_type="Defective";
                                    }else if ($item['return_type']==2){
                                        $order_type="Adjustment";
                                    }else if ($item['return_type']==3){
                                        $order_type="Demo";
                                    }else{
                                        $order_type="-";
                                    }

                                    isset($item['pay_time']) ? $pay = 'v' : $pay = 'X';
                                    if ($item['shipping_yes_time'])
                                        $shipping = 'v';
                                    else
                                        $shipping = 'X';

                                    isset($item['outmysql_time']) ? $out = 'v' : $out = 'X';

                                    if ($item['status'] == 1)
                                        $status = 'v';
                                    else
                                        $status = 'X';

                                    $row = array();

                                    $confirmstatus = $item['outmysql_user'];
                                    if($confirmstatus == NULL){
                                        $confirmstatus = 'NO';
                                    }else{
                                        $confirmstatus ='YES';
                                    }


                                    $params = array(
                                        'sn' => $item['sn'],
                                        'good_id' => $item['good_id'],
                                        'good_color' => $item['good_color'],
                                    );

                                    $m_query = $QMarket->getNumForReuturnByModelColor($params);
                                    $num = $m_query[0]['qty'];



                                    $row[] = $i;
                                    $row[] = '="'.$temp_sn.'"';
                                    $row[] = $temp_invoice;
                                    // $row[] = $temp_did;
                                    $row[] = $temp_distributor_code;
                                    $row[] = $temp_name;
                                    $row[] = $temp_area;
                                    $row[] = $temp_title;
                                    $row[] = $temp_cg;
                                    $row[] = $brands_name.' '.$good_name.' '.$good_color;
                                // $row[] = $good_color;
                                    $row[] = $num;
                                    $row[] = My_Number::f(@$item['price'], 0, ',','.');
                                    $row[] = My_Number::f($item['price']*$num, 0, ',', '.');
                                // $row[] = My_Number::f($value['total'], 0, ',','.');
                                    $row[] = $temp_addt;
                                    $row[] = $temp_user;
                                    $row[] = $temp_house ;
                                    $row[] = $temp_pay;
                                    $row[] = $confirm_user;
                                    $row[] = $confirmstatus;

                                    fputcsv($output,$row);
                                    unset($item);
                                    unset($value);
                                    unset($row);
                                    $i++;
                                        // }
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


                            private function _exportExcelForFinance($sql){
                                $this->_helper->layout->disableLayout();
                                $this->_helper->viewRenderer->setNoRender(true);

                                $db = Zend_Registry::get('db');
                                set_time_limit(0);
                                error_reporting(~E_ALL);
                                ini_set('display_error', 0);
                                ini_set('memory_limit', -1);
                                $filename = 'Sellout_List_For_Finance - '.date('d-m-Y H-i-s').'.csv';

                                while (@ob_end_clean());
                                ob_start();
                                header('Content-Encoding: UTF-8');
                                header('Content-Type: text/csv; charset=utf-8');
                                header('Content-Disposition: attachment; filename='.$filename);
                                $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                                $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
                                if (!file_exists($file_path))
                                    mkdir($file_path, 0777, true);

echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
$output = fopen('php://output', 'w');

//$path = $file_path.'/'.$filename;
//$output = fopen($path, 'w+');
// echo "\xEF\xBB\xBF"; // UTF-8 BOM

$heads = array(
'SALE ORDER NUMBER',//
'INVOICE NUMBER',//
//'FINANCE CODE',//
//'CARRIER CODE',//
//'DISTRIBUTOR NAME',//
//'DISTRIBUTOR ID',//
//'EXTENAL CODE',//
'DISTRIBUTOR NAME',//
'DISTRIBUTOR ID',//
'PRODUCT NAME',//
'SALES QUANTITY',//
'UNIT PRICE EX',//
'TOTAL EX',//
'SALES QUANTITY',//
'UNIT PRICE EX',//
'TOTAL EX',//
'PAID DATETIME',//
'BillDate',//
'WAREHOUSE',//
//'SHPPING WAREHOUSE',//
'SHIPPING DATETIME',//
//'SHIPMENT DATE',//
//'EXPRESS Code',//
//'BHQ',//
//'HQ',//
'RGM',//
'RM',//
//'SALES',//
//'AreaType',//
//'DISTRIBUTOR NAME',//
//'PURCHASING CHANNEL',//
//'SALE CHANNEL',//
'SALES CONFIRM DATETIME',//
//'ORDER DATE',//
//'REMARK',//,
//'EXPRESS PRICE',//
//'PRODUCT TYPE',//

);

fputcsv($output, $heads);

$QGood          = new Application_Model_Good();
$QGoodColor     = new Application_Model_GoodColor();
$QGoodCategory  = new Application_Model_GoodCategory();
$QDistributor   = new Application_Model_Distributor();
$QWarehouse     = new Application_Model_Warehouse();
$QImei          = new Application_Model_Imei();
$QBrand         = new Application_Model_Brand();

$goods             = $QGood->get_cache();
$goodColors        = $QGoodColor->get_cache();
$good_categories   = $QGoodCategory->get_cache();
$distributor_cache = $QDistributor->get_cache2();
$warehouses        = $QWarehouse->get_cache();

$result = $db->query($sql);
//print_r($result);die;

$i = 2;
$next_sn='';$delivery_fee_count =0;

$grand_e1 = array(81,82,83,110,111,112);
$grand_e2 = array(85,86,87,115,88,89,116,117);
$grand_e3 = array(90,91,92,93,113);
$grand_e4 = array(94,95,96);
$grand_e5 = array(97,109);
$grand_w1 = array(98,99,100,101,102,114);
$grand_w2 = array(103,104,105);
$grand_w3 = array(106,107,108);

$count = 0;
$old_sn = '';

foreach($result as $item) {

    $d_id = $item['d_id'];
    if ($item['canceled']== 1){$cancel = 'Canceled';}else{$cancel = 'Not Cancel';}

    if($old_sn != $item['sn']){
        $old_sn = $item['sn'];
        $count = 0;
    }else{
        $count++;
    }

    if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
    else { $temp_sn = $item['sn_ref']; }

    /*--Show delivery_fee 1 row --*/   
    if($next_sn==$item['sn']){
        $delivery_fee_count+=1;
    }else{
        $next_sn=$item['sn'];
    }

    if($delivery_fee_count ==1){
        $delivery_fee = !is_null($item['delivery_fee']) ? $item['delivery_fee'] : 0;
        $delivery_fee_count=0;
    }else{
        $delivery_fee =0;
    }

    if ($item['status'] == 1) { $temp_status = 'Actived'; }
    else if ($item['status'] == 2) { $temp_status = 'Expired'; }
    else { $temp_status = 'Expired'; }

if ($item['type'] == 1) //for retailer
$type = 'For Retailer';
elseif ($item['type'] == 2) //for APK
$type = 'For APK';
elseif ($item['type'] == 3) //for staffs
$type = 'For Staffs';
elseif ($item['type'] == 4) //for lending
$type = 'For Lending';
elseif ($item['type'] == 5) //for DEMO
$type = 'For DEMO';    

if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = '????????????'; }
else { $branch_type = '????'; }
$num = $item['num'];

$where_payment = $QDistributor->getAdapter()->quoteInto('id = ?', $item['d_id']);
$distributors_payment = $QDistributor->fetchRow($where_payment);
$rank = $distributors_payment->rank;

$total_spc_discount = $item['total_spc_discount'];

if($rank=='9'){
    $price = $this->format_number_2($item['price']);
    $total_amount_ex_vat = $this->format_number_2($price)* $num;
}else{
    $price_ext = $this->ext_vat($item['price']); 
    $price = $this->cal_sale_off_percent($item['sale_off_percent'],$price_ext,$num,$item['total']);
    $price_in = $price *1;
    $total_amount_ex_vat = ($this->format_number_2($this->ext_vat($item['total'])));
    $total_amount_in_vat = ($total_amount_ex_vat*1);

    //$total_amount_ex_vat = ($this->format_number_2($price)* $num)-$total_spc_discount;
}

$excel_area_name = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area);

$excel_area_id = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area, My_Region::ID);

if ( in_array($excel_area_id, $grand_e1) ) { $grand_area = 'BKK East-1'; } 
else if ( in_array($excel_area_id, $grand_e2) ) { $grand_area = 'BKK East-2'; }
else if ( in_array($excel_area_id, $grand_e3) ) { $grand_area = 'BKK East-3'; }
else if ( in_array($excel_area_id, $grand_e4) ) { $grand_area = 'BKK East-4'; }
else if ( in_array($excel_area_id, $grand_e5) ) { $grand_area = 'BKK East-5'; }
else if ( in_array($excel_area_id, $grand_w1) ) { $grand_area = 'BKK West-1'; }
else if ( in_array($excel_area_id, $grand_w2) ) { $grand_area = 'BKK West-2'; }
else if ( in_array($excel_area_id, $grand_w3) ) { $grand_area = 'BKK West-3'; }
else { $grand_area = $excel_area_name; }

if($count == 0){

    if($item['delivery_fee'] > 0){
        $row = array();
        $row[] = '="'.$temp_sn.'"';
        $row[] = $item['invoice_number'];
        //$row[] = $item['tracking_no'];
        $row[] = $distributor_cache[$item['d_id']]['d_id'];
        $row[] = $distributor_cache[$item['d_id']]['code'];
        $row[] = $distributor_cache[$item['d_id']]['title'];
        $row[] = $distributor_cache[$item['d_id']]['unames'];
        $row[] = '="'.$distributor_cache[$item['d_id']]['mst_sn'].'"';
        $row[] = $branch_type;
        $row[] = '="'.$distributor_cache[$item['d_id']]['branch_no'].'"';
        $row[] = $grand_area;
        $row[] = $excel_area_name;
        $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
        $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::District);
        //$row[] = 'Delivery Fee';
        $row[] = '';
        $row[] = '';

        $row[] = 1;

        if($item['delivery_fee'] > 0){
            $row[] = number_format($this->ext_vat($item['delivery_fee']),2);
            $row[] = number_format($this->ext_vat($item['delivery_fee']),2);
        }else{
            $row[] = 0;
            $row[] = 0;
        }

        $sales_confirm_date = '';
        if($item['sales_confirm_date']){
            $sales_confirm_date = date('Y-m-d', strtotime($item['sales_confirm_date']));
        }

        $pay_time = '';
        if($item['pay_time']){
            $pay_time = date('Y-m-d', strtotime($item['pay_time']));
        }

        $shipping_yes_time = '';
        if($item['shipping_yes_time']){
            $shipping_yes_time = date('Y-m-d', strtotime($item['shipping_yes_time']));
        }

        $outmysql_time = '';
        if($item['outmysql_time']){
            $outmysql_time = date('Y-m-d', strtotime($item['outmysql_time']));
        }

        $add_time = '';
        if($item['add_time']){
            $add_time = date('Y-m-d', strtotime($item['add_time']));
        }

        // $row[] = $item['sales_confirm_date'];
        // $row[] = $item['pay_time'];
        // $row[] = $item['shipping_yes_time'];

        $row[] = $sales_confirm_date;
        $row[] = $pay_time;
        $row[] = $shipping_yes_time;
        
        $row[] = $warehouses[$item['warehouse_id']];//24

        // $row[] = $item['outmysql_time'];

        $row[] = $outmysql_time;

        $row[] = $temp_status;

        // $row[] = $item['add_time'];

        $row[] = $add_time;

        $row[] = $type;
        $row[] = $item['text'];
        $row[] = $item['sales_catty_name'];
        $row[] = $item['sales_area'];
        $row[] = $item['sales_admin_id'];
        $row[] = $item['sales_admin'];
        $row[] = $item['tax_po'];
        $row[] = $item['finance_group'];
        $row[] = $cancel;
        $row[] = $item['bs_campaign'];
        $row[] = $item['phone_number_sn'];

        $sv_swap_status = 'NO';
        if(isset($item['service_swap_imei_status']) && $item['service_swap_imei_status']){
            $sv_swap_status = 'YES';
        }
        $row[] = $sv_swap_status;
        $row[] = $item['service_old_imei'];

        if($item['service_old_imei']){
            $data_old_imei = $QImei->getImeiRecord($item['service_old_imei']);

            $row[] = $good_categories[11];
            $row[] = $goods[$data_old_imei['good_id']];
            $row[] = $goodColors[$data_old_imei['good_color']];
        }else{
            $row[] = '';
            $row[] = '';
            $row[] = '';
        }

        $row[] = $item['service_new_imei'];

        if($item['service_new_imei']){
            $row[] = $good_categories[$item['cat_id']];
            $row[] = $goods[$item['good_id']];
            $row[] = $goodColors[$item['good_color']];
        }else{
            $row[] = '';
            $row[] = '';
            $row[] = '';
        }

        $sv_swap_acc_status = 'NO';
        if(isset($item['service_swap_acc_status']) && $item['service_swap_acc_status']){
            $sv_swap_acc_status = 'YES';
        }
        $row[] = $sv_swap_acc_status;

        if($sv_swap_acc_status == 'YES'){
            $row[] = $good_categories[$item['old_cat_id']];
            $row[] = $goods[$item['old_good_id']];
            $row[] = $goodColors[$item['old_good_color_id']];
            $row[] = $item['old_num'];

            $row[] = $good_categories[$item['new_cat_id']];
            $row[] = $goods[$item['new_good_id']];
            $row[] = $goodColors[$item['new_good_color_id']];
            $row[] = $item['new_num'];
        }else{
            $row[] = '';
            $row[] = '';
            $row[] = '';
            $row[] = '';

            $row[] = '';
            $row[] = '';
            $row[] = '';
            $row[] = '';
        }

        fputcsv($output, $row);
        unset($row);
    }

    if($item['total_spc_discount'] > 0){

        $row = array();
        $row[] = '="'.$temp_sn.'"';
        $row[] = $item['invoice_number'];
        $row[] = $item['tracking_no'];
        $row[] = $distributor_cache[$item['d_id']]['d_id'];
        $row[] = $distributor_cache[$item['d_id']]['code'];
        $row[] = $distributor_cache[$item['d_id']]['title'];
        $row[] = $distributor_cache[$item['d_id']]['unames'];
        $row[] = '="'.$distributor_cache[$item['d_id']]['mst_sn'].'"';
        $row[] = $branch_type;
        $row[] = '="'.$distributor_cache[$item['d_id']]['branch_no'].'"';
        $row[] = $grand_area;
        $row[] = $excel_area_name;
        $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
        $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::District);
        $row[] = 'Discount';
        $row[] = '';
        $row[] = '';

        $row[] = 1;
        $row[] = $item['total_spc_discount']*-1;
        $row[] = $item['total_spc_discount']*-1;

        $sales_confirm_date = '';
        if($item['sales_confirm_date']){
            $sales_confirm_date = date('Y-m-d', strtotime($item['sales_confirm_date']));
        }

        $pay_time = '';
        if($item['pay_time']){
            $pay_time = date('Y-m-d', strtotime($item['pay_time']));
        }

        $shipping_yes_time = '';
        if($item['shipping_yes_time']){
            $shipping_yes_time = date('Y-m-d', strtotime($item['shipping_yes_time']));
        }

        $outmysql_time = '';
        if($item['outmysql_time']){
            $outmysql_time = date('Y-m-d', strtotime($item['outmysql_time']));
        }

        $add_time = '';
        if($item['add_time']){
            $add_time = date('Y-m-d', strtotime($item['add_time']));
        }

        // $row[] = $item['sales_confirm_date'];
        // $row[] = $item['pay_time'];
        // $row[] = $item['shipping_yes_time'];

        $row[] = $sales_confirm_date;
        $row[] = $pay_time;
        $row[] = $shipping_yes_time;
        
        $row[] = $warehouses[$item['warehouse_id']];
        $row[] = $item['outmysql_time'];
        $row[] = $temp_status;

        // $row[] = $item['add_time'];

        $row[] = $add_time;

        $row[] = $type;
        $row[] = $item['text'];
        $row[] = $item['sales_catty_name'];
        $row[] = $item['sales_area'];
        $row[] = $item['sales_admin_id'];
        $row[] = $item['sales_admin'];
        $row[] = $item['tax_po'];
        $row[] = $item['finance_group'];
        $row[] = $cancel;
        $row[] = $item['bs_campaign'];
        $row[] = $item['phone_number_sn'];

        $sv_swap_status = 'NO';
        if(isset($item['service_swap_imei_status']) && $item['service_swap_imei_status']){
            $sv_swap_status = 'YES';
        }
        $row[] = $sv_swap_status;
        $row[] = $item['service_old_imei'];

        if($item['service_old_imei']){
            $data_old_imei = $QImei->getImeiRecord($item['service_old_imei']);

            $row[] = $good_categories[11];
            $row[] = $goods[$data_old_imei['good_id']];
            $row[] = $goodColors[$data_old_imei['good_color']];
        }else{
            $row[] = '';
            $row[] = '';
            $row[] = '';
        }

        $row[] = $item['service_new_imei'];

        if($item['service_new_imei']){
            $row[] = $good_categories[$item['cat_id']];
            $row[] = $goods[$item['good_id']];
            $row[] = $goodColors[$item['good_color']];
        }else{
            $row[] = '';
            $row[] = '';
            $row[] = '';
        }

        $sv_swap_acc_status = 'NO';
        if(isset($item['service_swap_acc_status']) && $item['service_swap_acc_status']){
            $sv_swap_acc_status = 'YES';
        }
        $row[] = $sv_swap_acc_status;

        if($sv_swap_acc_status == 'YES'){
            $row[] = $good_categories[$item['old_cat_id']];
            $row[] = $goods[$item['old_good_id']];
            $row[] = $goodColors[$item['old_good_color_id']];
            $row[] = $item['old_num'];

            $row[] = $good_categories[$item['new_cat_id']];
            $row[] = $goods[$item['new_good_id']];
            $row[] = $goodColors[$item['new_good_color_id']];
            $row[] = $item['new_num'];
        }else{
            $row[] = '';
            $row[] = '';
            $row[] = '';
            $row[] = '';

            $row[] = '';
            $row[] = '';
            $row[] = '';
            $row[] = '';
        }

        fputcsv($output, $row);
        unset($row);

    }

}

$row = array();
$row[] = '="'.$temp_sn.'"';//1
$row[] = $item['invoice_number'];//2

//$row[] = 'N/A';//3
//$row[] = 'N/A';//4
// $row[] = $distributor_cache[$item['d_id']]['title'];//5
// $row[] = $distributor_cache[$item['d_id']]['d_id'];//6
//$row[] = 'N/A';//7

$row[] = $item['distributor_code'].'-'.$distributor_cache[$item['d_id']]['title'];//8
$row[] = $item['distributor_code'];//9

$brand_name = $QBrand->getBrand($item['good_id']);

$row[] = $brand_name[0]['brand_name'].' '. $goods[$item['good_id']].' '.$goodColors[$item['good_color']];//10

$product_total_price = $this->product_price($item['total']);

if($rank == '9'){
    $product_total_price = $this->product_price($item['total']);
}

$product_unit_price = $this->product_price($this->decimal_remove_comma($product_total_price) / $item['num']);

$row[] = $item['num'];//11

if($item['price'] > 0){
    $row[] = $product_unit_price;//12
    $row[] = $product_total_price;//13
}else{
    $row[] = 0;//12
    $row[] = 0;//13
}

$row[] = $item['num'];//14

if($item['price'] > 0){
    $row[] = $product_unit_price;//15
    $row[] = $product_total_price;//16
}else{
    $row[] = 0;//15
    $row[] = 0;//16
}

$pay_time = '';
if($item['pay_time']){
    $pay_time = date('Y-m-d', strtotime($item['pay_time']));
}

$sales_confirm_date = '';
if($item['sales_confirm_date']){
    $sales_confirm_date = date('Y-m-d', strtotime($item['sales_confirm_date']));
}

$shipping_yes_time = '';
if($item['shipping_yes_time']){
    $shipping_yes_time = date('Y-m-d', strtotime($item['shipping_yes_time']));
}

$outmysql_time = '';
if($item['outmysql_time']){
    $outmysql_time = date('Y-m-d', strtotime($item['outmysql_time']));
}

//$row[] = $item['pay_time'];
//$row[] = $item['sales_confirm_date'];

$row[] = $pay_time;//17
$row[] = $sales_confirm_date;//18

$row[] = $warehouses[$item['warehouse_id']];//19
//$row[] = 'N/A';//20

//$row[] = $item['shipping_yes_time'];

$row[] = $shipping_yes_time;//21

//$row[] = 'N/A';//22
//$row[] = 'N/A';//23
//$row[] = 'N/A';//24
//$row[] = 'N/A';//25

$row[] = $excel_area_name;//26
$row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);//27
//$row[] = 'N/A';//28
//$row[] = 'N/A';//29
//$row[] = $distributor_cache[$item['d_id']]['title'];//30
//$row[] = 'N/A';//31
//$row[] = 'N/A';//32

// $row[] = $item['outmysql_time'];

$row[] = $outmysql_time;//33

//$row[] = 'N/A';//34
//$row[] = 'N/A';//35
//$row[] = 'N/A';//36
//$row[] = 'N/A';//37

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


public function _exportClientExcel($data)
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Client List - '.date('d-m-Y H-i-s').'.csv';

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
    'No.',
    'customer ID',
    'Customer Code',
    'certificate name',
    'short name',
    'Actual Controller',
    'customer level',
    'Client Cooperation Degree',
    'Client OPPO Brand Share(%)',
    'Client Type',
    'Tel.',
    'credential no',
    'Actual Cooperate Date',
    'Email',
    'Customer Type',
    'Remark',
    'Creator',
    'Creation Time',
    'Last Modifier',
    'Last Modified Time',
    'status',
    'certification status'
);
fputcsv($output, $heads);

$QStaff = new Application_Model_Staff();
$staff = $QStaff->get_cache();

$i = 1;
foreach($data as $item) {

    if($item['status'] == 1) {

        $status = "Official";

    }elseif($item['status'] == 2){

        $status = "Freeze";

    }else{

        $status = "Close";
    }

    $row = array();
    $row[] = $i;
    $row[] = $item['id'];
    $row[] = $item['customer_code'];
    $row[] = $item['client_name'];
    $row[] = $item['short_name'];
    $row[] = $item['custormer'];
    $row[] = $item['level'];
    $row[] = '';
    $row[] = '';
    $row[] = '';
    $row[] = $item['phone_number'];
    $row[] = '';
    $row[] = $item['cooperate_date'];
    $row[] = $item['email'];
    $row[] = '';
    $row[] = $item['remark'];
    $row[] = $staff[$item['created_by']];
    $row[] = $item['created_at'];
    $row[] = $staff[$item['updated_by']];
    $row[] = $item['updated_at'];
    $row[] = $status;
    $row[] = 'NOT CERTIFICATED';


    fputcsv($output,$row);
    unset($item);
    unset($row);
    $i++;
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


public function _exportDistributortExcel($data)
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', -1);
    $filename = 'Distributor List - '.date('d-m-Y H-i-s').'.csv';

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
        'No.',
        'Distributor Code',
        'Distributor Name',
        'Extarnal Code',
        'Upper-level Distributor',
        'Distributor Type',
        'Number of Store',
        'Shops in cooperating',
        'Price System',
        'Office',
        'Affiliated Client',
        'Certificate Name',
        'Customer Type',
        'Leader Name',
        'Leader Tel.',
        'Leader IdCardNo',
        'Status',
        'Remark',
        'Creation Time',
        'Last Modified Time'

    );
    fputcsv($output, $heads);

    $QDistributorNew = new Application_Model_DistributorNew();
    $QRegionalMarket = new Application_Model_RegionalMarket();

    $distributor_cache = $QDistributorNew->get_cache();
    $regional_cache = $QRegionalMarket->get_cache();

    $i = 1;
    foreach($data as $item) {

        if($item['distributor_type'] == 1) {
            $dsi_type = "Regional Distributor";
        } elseif ($item['distributor_type'] == 2) {
            $dis_type = "Affiliate";
        }else{
            $dis_type = "Retailer";
        }

        switch($item['status']) {
            case 1:
            $status = 'In Cooperation';
            break;
            case 2:
            $satus = 'Suspend Cooperation';
            break;
            case 3:
            $satus = 'Close';
            break;
        }

        $row = array();
        $row[] = $i;
        $row[] = $item['distributor_code'];
        $row[] = $item['distributor_name'];
        $row[] = $item['external_serial'];
        $row[] = $distributor_cache[$item['superior_distributor']];
        $row[] = $dis_type;
        $row[] = '';
        $row[] = '';
        $row[] = '';
        $row[] = $regional_cache[$item['provience_id']];
        $row[] = $item['client_name'];
        $row[] = $item['short_name'];
        $row[] = $item['client_type'];
        $row[] = $item['leader'];
        $row[] = $item['phone_number'];
        $row[] = '';
        $row[] = $status;
        $row[] = $item['reamrk'];
        $row[] = $item['created_at'];
        $row[] = $item['updated_at'];

        fputcsv($output,$row);
        unset($item);
        unset($row);
        $i++;

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


}




