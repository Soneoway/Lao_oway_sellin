<?php

$flashMessenger    = $this->_helper->flashMessenger;
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
$created_at_from   = $this->getRequest()->getParam('created_at_from', date('d/m/Y', strtotime('-1 month')));
$invoice_time_from = $this->getRequest()->getParam('invoice_time_from');
$invoice_time_to   = $this->getRequest()->getParam('invoice_time_to');
$cat_id            = $this->getRequest()->getParam('cat_id');
$warehouse_id      = $this->getRequest()->getParam('warehouse_id');
$type              = $this->getRequest()->getParam('type');
$text              = $this->getRequest()->getParam('text');
$status            = $this->getRequest()->getParam('status', 1);
$export            = $this->getRequest()->getParam('export', 0);
$tags              = $this->getRequest()->getParam('tags');
$invoice_number    = $this->getRequest()->getParam('invoice_number');
$user_id           = $this->getRequest()->getParam('user_id');
$area_id           = $this->getRequest()->getParam('area_id');
$region_id         = $this->getRequest()->getParam('region_id');
$district_id       = $this->getRequest()->getParam('district_id');
$distributor_po    = $this->getRequest()->getParam('distributor_po');
$campaign_id       = $this->getRequest()->getParam('campaign_id');
$distributor_ka    = $this->getRequest()->getParam('distributor_ka');
$join              = $this->getRequest()->getParam('join', 0);

$last_url     = My_Url::refer('sales/stock');
$userStorage  = Zend_Auth::getInstance()->getStorage()->read();
$QDistributor = new Application_Model_Distributor();

if (!$userStorage || !isset($userStorage->id))
    $this->_redirect(HOST);

// gộp đơn
if (isset($join) && $join) {
    if (!isset($d_id) || !$d_id) {
        $flashMessenger->setNamespace('error')->addMessage('Vui lòng chọn 01 đại lý để gộp đơn');
        $this->_redirect($last_url);
        return;
    }

    $d_id = intval($d_id);
    $db = Zend_Registry::get('db');

    $where = $QDistributor->getAdapter()->quoteInto('id = ?', $d_id);
    $distributor_check = $QDistributor->fetchRow($where);
    if (!$distributor_check) {
        $flashMessenger->setNamespace('error')->addMessage('Invalid distributor');
        $this->_redirect($last_url);
        return;
    }

    $QAsm = new Application_Model_Asm();
    $asm_cache = $QAsm->get_cache($userStorage->hr_id);
    if (!isset($asm_cache) 
        || !isset($asm_cache['district'])
        || !is_array($asm_cache['district'])
        || !count($asm_cache['district'])
        || !in_array($distributor_check['district'], $asm_cache['district'])
    ) {
        $flashMessenger->setNamespace('error')->addMessage('Không thể gộp đơn của đại lý ở khu vực khác.');
        $this->_redirect($last_url);
        return;
    }

    $sql = "SELECT COUNT(DISTINCT sn) as qty FROM market_stock WHERE d_id=?";
    $result_check = $db->query($sql, array($d_id));
    $check = $result_check->fetch();

    if (!isset($check['qty']) || intval($check['qty']) < 2) {
        $flashMessenger->setNamespace('error')->addMessage('Đơn không cần gộp');
        $this->_redirect($last_url);
        return;
    }

    $sql = "call sp_join_stock_order(?, ?)";
    $db->query($sql, array($d_id, intval($userStorage->id)));

    $flashMessenger->setNamespace('success')->addMessage('Gộp đơn thành công');

    $this->_redirect($last_url);
    return;
}

if ($tags and is_array($tags))
    $tags = $tags;
else
    $tags = null;

$limit = LIMITATION;
$total = 0;

$params = array_filter(array(
    'sn'                => $sn,
    'distributor_name'  => $distributor_name,
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
    'district_id'       => $district_id,
    'distributor_po'    => $distributor_po,
    'campaign_id'       => $campaign_id,
    'distributor_ka'    => $distributor_ka,
    'export'            => $export,
));

$params['created_at_from'] = $created_at_from;
$params['created_at_to'] = $created_at_to;

$params['isbacks'] = false;
$params['group_sn'] = true;

if ($pay_time)
    $params['payment'] = true;

if ($outmysql_time)
    $params['outmysql_time'] = true;

if (My_Staff_Group::inGroup($userStorage->group_id, SALES_ADMIN)) {
    $params['create_user_id'] = $userStorage->hr_id;
}

$QGood          = new Application_Model_Good();
$QGoodColor     = new Application_Model_GoodColor();
$QMarket        = new Application_Model_MarketStock();
$QGoodCategory  = new Application_Model_GoodCategory();
$QWarehouse     = new Application_Model_Warehouse();
$QStaff         = new Application_Model_Staff();
$QArea          = new Application_Model_Area();
$QRegion        = new Application_Model_RegionalMarket();
$QDistributorPo = new Application_Model_DistributorPo();
$QMarketProduct = new Application_Model_MarketProduct();
$QCampaign      = new Application_Model_Campaign();

$this->view->po_list = $QDistributorPo->fetchAll(null, 'created_at DESC');

$goods             = $QGood->get_cache();
$goodColors        = $QGoodColor->get_cache();
$distributors      = $QDistributor->get_cache();
$distributors2     = $QDistributor->get_cache2();
$distributor_ka    = $QDistributor->get_cacheKA();
$good_categories   = $QGoodCategory->get_cache();
$warehouses_cached = $QWarehouse->get_cache();
$campaign          = $QCampaign->get_cache();
$staffs_cached     = $QStaff->get_cache();

if (isset($export) && $export)
{
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    error_reporting(0);
    ini_set('display_error', 0);
    $area_list = false;

    if (My_Staff_Group::inGroup($userStorage->group_id, SALES_ADMIN)) {
        $params['create_user_id'] = $userStorage->hr_id;
        $QAsm = new Application_Model_Asm();
        $asm_cache = $QAsm->get_cache();
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        if (!(isset($asm_cache[$params['create_user_id']]) && isset($asm_cache[$params['create_user_id']]['area']))) {
            $flashMessenger->setNamespace('error')->addMessage('No area assigned to you.');
            $this->_redirect(HOST.'sales/stock');
            return;
        }

        $area_list = $asm_cache[$params['create_user_id']]['area'];
    }

    $sql = "SELECT
            a.`name` AS area_name,
            p.`name` AS province_name,
            dt.`name` AS district_name,
            a.id,
            d.id AS dealer_id,
            d.store_code AS store_code,
            d.title AS dealer_name,
            m.sn AS order_sn,
            m.add_time AS add_time,
            g.`name` AS good_name,
            c.`name` AS good_color,
            i.imei_sn,
            m.price,
            m.text
        FROM
            market_stock m
        JOIN imei_stock i ON m.id = i.market_stock_id
        JOIN distributor d ON d.id = m.d_id
        JOIN hr.regional_market dt ON dt.id = d.district
        JOIN hr.regional_market p ON p.id = dt.parent
        JOIN hr.area a ON a.id = p.area_id
        JOIN imei ii ON ii.imei_sn = i.imei_sn
        JOIN good g ON g.id = ii.good_id
        JOIN good_color c ON c.id = ii.good_color
        GROUP BY
            i.imei_sn
        "
        .
        (
            (isset($area_list) && is_array($area_list) && count($area_list))
            ? ("HAVING a.`id` IN (".implode(',', $area_list).")" )
            : ""
        )
        .
        "
        ORDER BY
            a.`name`,
            p.`name`,
            dt.`name`,
            d.title,
            m.add_time,
            g.`name`,
            c.`name`
        ";
    $db = Zend_Registry::get('db');
    PC::db($sql);
    $resultSet = $db->query($sql);

    if (!$resultSet) return false;

    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();
    $heads = array(
        'AREA',
        'PROVINCE',
        'DISTRICT',
        'DEALER ID',
        'STORE CODE',
        'TITLE',
        'ORDER SN',
        'ORDER TIME',
        'MODEL',
        'COLOR',
        'IMEI',
        'PRICE',
        'NOTE',
    );

    $PHPExcel->setActiveSheetIndex(0);
    $sheet = $PHPExcel->getActiveSheet();

    $alpha = 'A';
    $index = 1;

    foreach ($heads as $key)
        $sheet->setCellValue($alpha++ . $index, $key);

    $index++;

    if (isset($resultSet) && $resultSet) {
        $i = 1;
        foreach ($resultSet as $key => $value) {
            $alpha = 'A';
            $sheet->setCellValue($alpha++ . $index, $value['area_name']);
            $sheet->setCellValue($alpha++ . $index, $value['province_name']);
            $sheet->setCellValue($alpha++ . $index, $value['district_name']);
            $sheet->setCellValue($alpha++ . $index, $value['dealer_id']);
            $sheet->setCellValue($alpha++ . $index, $value['store_code']);
            $sheet->setCellValue($alpha++ . $index, $value['dealer_name']);
            $sheet->getCell($alpha++ . $index)->setValueExplicit($value['order_sn'],
                    PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue($alpha++ . $index, $value['add_time']);
            $sheet->setCellValue($alpha++ . $index, $value['good_name']);
            $sheet->setCellValue($alpha++ . $index, $value['good_color']);
            $sheet->getCell($alpha++ . $index)->setValueExplicit($value['imei_sn'],
                    PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue($alpha++ . $index, $value['price']);
            $sheet->setCellValue($alpha++ . $index, $value['text']);
            $index++;
        }
    }

    $filename = 'Stock - Order Detail - ' . date('Y-m-d H-i-s');
    $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

    $objWriter->save('php://output');

    exit;
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
    'last_updated_at'
);

$markets_sn = $QMarket->fetchPagination($page, $limit, $total, $params);

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

    foreach ($region_search as $_region)
    {
        $this->view->districts[$_region['id']] = $_region['name'];
    }
}

$this->view->goods             = $goods;
$this->view->goodColors        = $goodColors;
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
$this->view->url    = HOST . 'sales/stock' . ($params ? '?' . http_build_query($params) .
    '&' : '?');

$this->view->offset = $limit * ($page - 1);

$messages             = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success             = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if ($this->getRequest()->isXmlHttpRequest())
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setRender('partials/list');
}
