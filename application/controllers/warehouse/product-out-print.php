<?php
$this->_helper->layout->disableLayout();

$sn = $this->getRequest()->getParam('sn');

$params = array(
    'sn' => $sn,
    'group_sn' => 1,
    'isbacks' => 0,
    //    'outmysql_time' => 1,
    );

$page = 1;
$limit = 1;
$total = 0;

$QGood = new Application_Model_Good();
$QMarket = new Application_Model_Market();
$QImei = new Application_Model_Imei();
$QDigitalSn = new Application_Model_DigitalSn();
$QWarehouse = new Application_Model_Warehouse();

//Tanong
//$QWarehouse->getInvoiceNo_Ref($sn);

// Lấy danh sách các đơn hàng (nhóm theo sn)
$market = $QMarket->fetchPagination($page, $limit, $total, $params);

foreach ($market as $k => $v) {
    if (isset($v['service']))
        $service = $v['service'];
    if (isset($v['office']))
        $office = $v['office'];
    if (isset($v['warehouse_nvmm'])){
        $warehouse_nvmm = $v['warehouse_nvmm'];
    }
}

if (!(isset($market) && isset($market[0]) && $market[0])) {
    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->addMessage('Sales Number not exists');
    $this->_redirect(HOST . "warehouse/product-out");
} else {
    $market = $market[0];
}

unset($params['group_sn']);
unset($params['sort']);

// Lấy danh sách các sản phẩm theo từng đơn hàng (đã lấy ở trên)
$params['sn'] = $market['sn'];
$total2 = 0;
$mk_goods = $QMarket->fetchPagination(1, null, $total2, $params);

$po_id = null; // dùng để hiện tên PO ra phiếu xuất kho
// lấy danh sách IMEI có sn như trên theo từng sản phẩm
$imeis = $digital_sns = array();

$db = Zend_Registry::get('db');

foreach ($mk_goods as $k => $v) {
    if (isset($v['po_id'])) $po_id = $v['po_id'];


    $select = $db->select()->from(array('i' => 'imei'), array('i.imei_sn'))
        ->where('sales_id = ?', $v['id']);

    $imeis[$v['id']] = $db->fetchAll($select);

    $where = $QDigitalSn->getAdapter()->quoteInto('sales_id = ?', $v['id']);

    $digital_sns[$v['id']] = $QDigitalSn->fetchAll($where);
}

unset($params['sn']);

$this->view->market = $market;
$this->view->mk_goods = $mk_goods;
$this->view->imeis = $imeis;
$this->view->digital_sns = $digital_sns;

$tmp = $QGood->fetchAll();
$all_goods = array();
foreach ($tmp as $k => $v) {
    $all_goods[$v['id']] = $v;
}
$this->view->all_goods = $all_goods;

$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

$QGoodColor = new Application_Model_GoodColor();
$this->view->good_colors = $QGoodColor->get_cache();


$QDistributor = new Application_Model_Distributor();

$distributor = $QDistributor->find($market['d_id']);
$distributor = $distributor->current();

if ($distributor) {
    $this->view->distributor = $distributor;
}

$QStaff = new Application_Model_Staff();

if ($market['salesman']) {
    $staff = $QStaff->find($market['salesman']);
    $staff = $staff->current();
}

if ($market['user_id']) {
    $oStaff = $QStaff->find($market['user_id']);
    $oStaff = $oStaff->current();
}

if (isset($oStaff) and $oStaff)
    $this->view->oStaff = $oStaff;

if (isset($staff) and $staff)
    $this->view->staff = $staff;

if (isset($service) and $service) {
    $QService = new Application_Model_Service();
    $services = $QService->get_cache_service();
    $this->view->services = $services[$service];

//    var_dump($services[$service]);
//    exit;
}

if (isset($office) and $office) {
    $QOffice = new Application_Model_Office();
    $officeRowSet = $QOffice->find($office);
    $offices = $officeRowSet->current();
    $this->view->offices = $offices;
}

if(isset($warehouse_nvmm) AND $warehouse_nvmm){
    $QWarehouse = new Application_Model_Warehouse();
    $warehouse_nvmm_row = $QWarehouse->find($warehouse_nvmm)->current();
    $this->view->warehouse_nvmm_row = $warehouse_nvmm_row;
}

// My_Image_Barcode::renderNoCode($sn);
$QGoodColor = new Application_Model_GoodColor();
$this->view->colors_list = $QGoodColor->get_cache();

// lưu danh sách số thứ tự/sn/ngày
$gop = $QMarket->get_print_no($sn);
$this->view->gop = $gop;

$print_time = $QMarket->get_print_time($sn);

$warehouseRowset       = $QWarehouse->find($market['warehouse_id']);
$warehouse             = $warehouseRowset->current();
$this->view->warehouse = $warehouse;

if (!$print_time) {
    $data = array('print_time' => date('Y-m-d H:i:s'));
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $QMarket->update($data, $where);
}

// load tên PO ra phiếu xuất kho
if ($po_id) {
    $QDistributorPo = new Application_Model_DistributorPo();
    $where = $QDistributorPo->getAdapter()->quoteInto('id = ?', intval($po_id));
    $this->view->po = $QDistributorPo->fetchRow($where);
}
