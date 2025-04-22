<?php
// start : confirm section
$tmp = $this->getRequest()->getParam('id',0);

if (isset($tmp) && $tmp != 0) {
$QSales = new Application_Model_Market();
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
$where = $QSales->getAdapter()->quoteInto('sn = ?', $tmp);
$data = array('confirm_so' => 1);

$flashMessenger = $this->_helper->flashMessenger;
try {
$QSales->update($data, $where);
//todo log
$ip = $this->getRequest()->getServer('REMOTE_ADDR');
$info = 'Confirm : Sales order number: ' . $tmp;
$QLog = new Application_Model_Log();
$QLog->insert(array(
'info' => $info,
'user_id' => $userStorage->id,
'ip_address' => $ip,
'time' => date('Y-m-d H:i:s'),
));
$flashMessenger->setNamespace('success')->addMessage('Confirm Sale Order : Done!');
}
catch (exception $e) {
$flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
}
$this->_redirect('/sales');
}
// end : confirm section
//print_r($_GET);die;
$sort              = $this->getRequest()->getParam('sort', 'p.id');
$desc              = $this->getRequest()->getParam('desc', 1);
$page              = $this->getRequest()->getParam('page', 1);
$sn                = $this->getRequest()->getParam('sn');
$distributor_name  = $this->getRequest()->getParam('distributor_name');
$d_id              = $this->getRequest()->getParam('d_id');
$good_id           = $this->getRequest()->getParam('good_id');
$good_color        = $this->getRequest()->getParam('good_color');
$num               = $this->getRequest()->getParam('num');
$price             = $this->getRequest()->getParam('price');
$pay_time          = $this->getRequest()->getParam('payment', 0);
$outmysql_time     = $this->getRequest()->getParam('outmysql_time', 0);
$created_at_to     = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));
$created_at_from   = $this->getRequest()->getParam('created_at_from', date('d/m/Y', strtotime('-0 day')));
$invoice_time_from = $this->getRequest()->getParam('invoice_time_from');
$invoice_time_to   = $this->getRequest()->getParam('invoice_time_to');
$finance_confirm_time_from  = $this->getRequest()->getParam('finance_confirm_time_from');
$finance_confirm_time_to    = $this->getRequest()->getParam('finance_confirm_time_to');
$cat_id            = $this->getRequest()->getParam('cat_id');
$warehouse_id      = $this->getRequest()->getParam('warehouse_id');
$type              = $this->getRequest()->getParam('type');
$text              = $this->getRequest()->getParam('text');
$sale_off_percent  = $this->getRequest()->getParam('sale_off_percent');
$status            = $this->getRequest()->getParam('status', 1);
$export            = $this->getRequest()->getParam('export', 0);
$tags              = $this->getRequest()->getParam('tags');
$brand_id 		   = $this->getRequest()->getParam('brand_id');
$invoice_number    = $this->getRequest()->getParam('invoice_number');
$user_id           = $this->getRequest()->getParam('user_id');
$area_id           = $this->getRequest()->getParam('area_id');
$region_id         = $this->getRequest()->getParam('region_id');
$district_id       = $this->getRequest()->getParam('district_id');
$distributor_po    = $this->getRequest()->getParam('distributor_po');
$campaign_id       = $this->getRequest()->getParam('campaign_id');
$distributor_ka    = $this->getRequest()->getParam('distributor_ka');
$cancel            = $this->getRequest()->getParam('cancel');
$can_sn            = $this->getRequest()->getParam('can_sn');
$rank              = $this->getRequest()->getParam('rank');
$tracking_no       = $this->getRequest()->getParam('tracking_no');
$payment_no          = $this->getRequest()->getParam('payment_no');
$sn_munti          = $this->getRequest()->getParam('sn_munti');
$sn_munti          = array_values(array_unique($sn_munti));
$in_munti          = $this->getRequest()->getParam('in_munti');
$in_munti          = array_values(array_unique($in_munti));
$order_packed_sim  = $this->getRequest()->getParam('order_packed_sim');
$payment_type     = $this->getRequest()->getParam('payment_type');
$this->view->rank = $rank;
$this->view->d_id = $d_id;
$finance_group    = $this->getRequest()->getParam('finance_group');
$order_no_payment    = $this->getRequest()->getParam('order_no_payment');
$db = Zend_Registry::get('db');
$QDistributor = new Application_Model_Distributor();
$this->view->finance_group = $QDistributor->getFinanceGroup();
if ($tags and is_array($tags))
$tags = $tags;
else
$tags = null;
if($sn_munti){
$limit = 100000;
//$limit = 1000;
}else{
 $limit = 10;
//$limit = 10;
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
// limit 50 to brandshop and service
if (My_Staff_Group::inGroup($userStorage->group_id, array(25, 28))){
$limit = none;
}
}
$total = 0;
$params = array_filter(array(
'sn'                 => $sn,
'distributor_name'   => $distributor_name,
'd_id'               => $d_id,
'good_id'            => $good_id,
'good_color'         => $good_color,
'num'                => $num,
'price'              => $price,
'total'              => $total,
'cat_id'             => $cat_id,
'warehouse_id'       => array_unique($warehouse_id),
'status'             => $status,
'text'               => $text,
'brand_id'			 => $brand_id,
'sale_off_percent'   => $sale_off_percent,
'type'               => $type,
'tags'               => $tags,
'invoice_time_from'  => $invoice_time_from,
'invoice_time_to'    => $invoice_time_to,
'finance_confirm_time_from'  => $finance_confirm_time_from,
'finance_confirm_time_to'    => $finance_confirm_time_to,
'invoice_number'     => $invoice_number,
'user_id'            => $user_id,
'area_id'            => $area_id,
'region_id'          => $region_id,
'district_id'        => $district_id,
'distributor_po'     => $distributor_po,
'campaign_id'        => $campaign_id,
'distributor_ka'     => $distributor_ka,
'tracking_no'        => $tracking_no,
'rank'               => $rank,
'finance_group'      => $finance_group,
'allowpage'          => 'salelist',
'sn_munti'           => $sn_munti,
'in_munti'           => $in_munti,
'payment_no'         => $payment_no,
'order_no_payment'   => $order_no_payment,
'order_packed_sim'   => $order_packed_sim,
'payment_type'    => $payment_type

));
if($cancel!=''){
$params['cancel'] = $cancel;
}
$params['created_at_from'] = $created_at_from;
$params['created_at_to'] = $created_at_to;
$params['isbacks'] = false;
$params['group_sn'] = true;
if ($pay_time)
$params['payment'] = true;
if ($outmysql_time)
$params['outmysql_time'] = true;
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
if (My_Staff_Group::inGroup($userStorage->group_id, SALES_ADMIN)) {
$params['create_user_id'] = $userStorage->hr_id;
}

$QBrand 		= new Application_Model_Brand();
$QGood          = new Application_Model_Good();
$QGoodColor     = new Application_Model_GoodColor();
$QDiscount      = new Application_Model_Discount();
$QMarket        = new Application_Model_Market();
$QDistributor   = new Application_Model_Distributor();
$QGoodCategory  = new Application_Model_GoodCategory();
$QWarehouse     = new Application_Model_Warehouse();
$QStaff         = new Application_Model_Staff();
$QArea          = new Application_Model_Area();
$QRegion        = new Application_Model_RegionalMarket();
$QDistributorPo = new Application_Model_DistributorPo();
$QMarketProduct = new Application_Model_MarketProduct();
$QCampaign      = new Application_Model_Campaign();
$QStore  		= new Application_Model_Store();

$this->view->store = $QStore->get_cache();
$this->view->po_list = $QDistributorPo->fetchAll(null, 'created_at DESC');

$goods             = $QGood->getProduct2($params);

$goodColors        = $QGoodColor->get_cache();
$discounts         = $QDiscount->get_discount();
$distributors      = $QDistributor->get_cache();
$distributors2     = $QDistributor->get_cache2();
$distributor_ka    = $QDistributor->get_cacheKA();
$good_categories   = $QGoodCategory->get_cache();
$warehouse_type = $userStorage->warehouse_type;
$warehouses_cached = $QWarehouse->getWarehouses($warehouse_type);
$warehouse_arr = array();

foreach ($warehouses_cached as $k => $warehouse_data){
$warehouse_arr[$warehouse_data['id']] = $warehouse_data['name'];
}

$warehouses_cached = $warehouse_arr;
$campaign          = $QCampaign->get_cache();
$staffs_cached     = $QStaff->get_cache();
//echo $export;die;
if (isset($export) && $export)
{
// My_Report::preventExport();
set_time_limit(0);
ini_set('memory_limit', '-1');
error_reporting(0);
ini_set('display_error', 0);
$params['action_from'] = 'sales';
//print_r($_GET);die;
if ($export == 90) {

$this->_exportCancelIMEI($can_sn);
}
elseif ($export == 2)
{ //export for finance: use to import to account system
$params['sales_type'] = array(
1,
2,
3); //1: For Retailer;2: For Demo;3: For Staffs
$params['group_sn']       = false;
$params['no_accessories'] = true;
$params['canceled']       = -1;
$params['export']         = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
$this->_exportExcel2($markets_sn);
} elseif ($export == 3) { //export for finance: use to tax report
$params['sales_type'] = array(
1,
2,
3); //1: For Retailer;2: For Demo;3: For Staffs
$params['group_sn']         = true;
/*$params['no_accessories'] = true;*/
$params['canceled']         = -1;
$params['export']           = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);

$this->_exportExcel3($markets_sn);
} elseif ($export == 4) { //IMEI Export
if (My_Report::preventExport()) exit;
$params['group_sn']       = 0;
$params['get_imei']       = 1;
$params['no_accessories'] = true;
$params['canceled']       = -1;
$params['export']         = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
$this->_exportExcel4($markets_sn);
} elseif ($export == 5) { //Campaign Export
// if (My_Report::preventExport()) exit;
$params['get_imei'] = 1;
$params['group_sn'] = false;
$params['export']   = $export;
$params['campaign_id']    = $campaign_id;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);

$this->_exportCampaignExcel($markets_sn);
} elseif ($export == 6) {
$params['sales_type'] = array(
1,
2,
3); //1: For Retailer;2: For Demo;3: For Staffs
$params['group_sn']         = true;
$params['canceled']         = -1;
$params['export']           = $export;
$params['get_imei']         = 1;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
$this->_exportCampaignExcel($markets_sn);
} elseif ($export == 7) { //Output VAT Report
$params['campaign_id'] = false;
$params['group_sn'] = false;
$params['export'] = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
$this->_exportExcelOutputVat($markets_sn);
} elseif ($export == 20) { //Output VAT Report New
$params['campaign_id'] = false;
$params['group_sn'] = false;
$params['export'] = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
$this->_exportExcelOutputVatNew($markets_sn);

}elseif ($export == 30){
	
$params['campaign_id'] = false;
$params['group_sn'] = false;
$params['export'] = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
//echo($markets_sn);die;
$this->_exportExcelForFinance($markets_sn);


} elseif ($export == 8) {

$params['campaign_id'] = false;
$params['group_sn'] = false;
$params['export'] = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
//echo($markets_sn);die;
$this->_exportExcelOrderStatus($markets_sn);
} elseif ($export == 9) {
/*
$params['campaign_id'] = false;
$params['group_sn'] = false;
$params['export'] = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
$tmp = explode('FROM', $markets_sn);
$sql = $tmp[0].", SUM(p.num) as sum_num, SUM(p.price) as sum_price, SUM(p.total) as sum_total FROM ".$tmp[1]." GROUP BY d.region, p.good_id, p.good_color";
//print_r($sql);die;
$this->_exportExcelByProvince($sql);
*/
} elseif ($export == 10) { // Sale Master Data
$params['campaign_id'] = false;
$params['group_sn'] = false;
$params['export'] = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
$this->_exportExcelBydistributor($markets_sn);
} elseif ($export == 11) { // Sale Master Data By Imei
$params['campaign_id'] = false;
$params['group_sn'] = false;
$params['export'] = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
$this->_exportExcelOutputVatImei($markets_sn, $params);
} elseif ($export == 19) { // Sale Master Data By Imei Origin
$params['campaign_id'] = false;
$params['group_sn'] = false;
$params['export'] = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);

// echo $markets_sn; die;

$this->_exportExcelOutputVatImeiReturn($markets_sn, $params);
} elseif ($export == 21) { // Sale Master Data By Imei 2
$params['campaign_id'] = false;
$params['group_sn'] = false;
$params['export'] = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
$this->_exportExcelOutputVatImei2($markets_sn, $params);
} elseif ($export == 12) { //Cash Collection
$params['campaign_id'] = false;
$params['group_sn'] = false;
$params['export'] = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
$this->_exportExcelCashCollection($markets_sn);
} elseif ($export == 13){
$params['campaign_id'] = false;
$params['group_sn'] = false;
$params['export'] = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
$this->_exportExcel($markets_sn);
} elseif ($export == 14){ //Export Service Job Number
//print_r($params);die;
//$params['export'] = $export;
//$sql_market = $QMarket->fetchPagination($page, null, $total, $params);
//echo ($sql);die;
$this->_exportServiceJobNumber($params);
} elseif ($export == 15){ //Export Cash Collection For Service
set_time_limit( 0 );
error_reporting( 0 );
ini_set('display_error', 0);
ini_set('memory_limit', -1);
$params['shop_type']='service';
//print_r($export);die;
$QCheckmoney      = new Application_Model_Checkmoney();
$result_ch = $QCheckmoney->_getDataForExcelCashCollection($data,$params);
//print_r($result_ch);die;
$this->_exportExcelExportCashCollectionServiceExcel($result_ch);
exit();
}elseif ($export == 16){ //Export Cash Collection For Brand Shop
set_time_limit( 0 );
error_reporting( 0 );
ini_set('display_error', 0);
ini_set('memory_limit', -1);
//print_r($export);die;
$params['shop_type']='brandshop';
$QCheckmoney      = new Application_Model_Checkmoney();
$result_ch = $QCheckmoney->_getDataForExcelCashCollection($data,$params);
//print_r($result_ch);die;
$this->_exportExcelExportCashCollectionBrandShopExcel($result_ch);
exit();
}elseif ($export == 17){ //Export new order residue
$getData = $QMarket->getDataResidue();
$this->_exportExcelResidue($getData);
}elseif ($export == 18){ //Export sale cn
$params['campaign_id'] = false;
$params['group_sn'] = true;
$params['export'] = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
$this->_exportExcelSaleCN($markets_sn);
// $getData = $QMarket->getDataSaleCN();
// $this->_exportExcelSaleCN($getData);
} else {    //Export
$params['campaign_id'] = false;
$params['group_sn'] = false;
$params['export'] = $export;
$markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
//echo($markets_sn);die;
$this->_exportExcelNew($markets_sn);
}
}
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
'last_updated_at',
'pay_group',
'payment_no'
);
$params['have_quota'] = true;
$markets_sn = $QMarket->fetchPagination($page, $limit, $total, $params);
// print_r($markets_sn);die;
$markets_sn_array = array();
foreach ($markets_sn as $k => $v)
{
$markets_sn_array[$k] = $v;
$markets_sn_array[$k]['discount'] = $QMarketProduct->getDiscount($v['sn']);
}
unset($params['get_fields']);
unset($params['isbacks']);
unset($params['group_sn']);
$this->view->areas = $QArea->get_cache();
if ($area_id)
{
$where = $QRegion->getAdapter()->quoteInto('area_id = ?', $area_id);
$regions = $QRegion->fetchAll($where, 'name');
$regions_arr = array();
foreach ($regions as $key => $value)
$regions_arr[$value['id']] = $value['name'];
$this->view->regions = $regions_arr;
}
if ($region_id)
{
$where = $QRegion->getAdapter()->quoteInto('parent = ?', $region_id);
$region_search = $QRegion->fetchAll($where);
$this->view->districts = array();
foreach ($region_search as $_region){
	$this->view->districts[$_region['id']] = $_region['name'];
}

}

$this->view->brands            = $QBrand->get_cache();
$this->view->goods             = $goods;
$this->view->goodColors        = $goodColors;
$this->view->discounts         = $discounts;
$this->view->markets_sn        = $markets_sn_array;
$this->view->distributors      = $distributors;
$this->view->distributors2     = $distributors2;
$this->view->good_categories   = $good_categories;
$this->view->warehouses_cached = $warehouses_cached;
$this->view->staffs_cached     = $staffs_cached;
$this->view->campaign          = $campaign;
$this->view->distributor_ka    = $distributor_ka;
$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST . 'sales/' . ($params ? '?' . http_build_query($params) .
'&' : '?');
$this->view->offset = $limit * ($page - 1);
$flashMessenger               = $this->_helper->flashMessenger;
$messages                     = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages         = $messages;
$messages_success             = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;
if ($this->getRequest()->isXmlHttpRequest())
{
$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setRender('partials/list');
}