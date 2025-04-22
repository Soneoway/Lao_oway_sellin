<?php
$area_id    = $this->getRequest()->getParam('area_id', 0);
$good_id    = $this->getRequest()->getParam('good_id', 0);
$good_color = $this->getRequest()->getParam('good_color', 0);
$stock_from = $this->getRequest()->getParam('stock_from', null);
$stock_to   = $this->getRequest()->getParam('stock_to', null);
$export     = $this->getRequest()->getParam('export', 0);

$QGood      = new Application_Model_Good();
$QGoodColor = new Application_Model_GoodColor();
$QArea      = new Application_Model_Area();
$this->view->goods       = $QGood->get_phone_cache();
$this->view->good_colors = $QGoodColor->get_cache();
$this->view->areas       = $QArea->get_cache();
$this->view->params = array(
    'area_id'    => intval($area_id),
    'good_id'    => intval($good_id),
    'good_color' => intval($good_color),
    'stock_from' => $stock_from,
    'stock_to'   => $stock_to,
);

$db = Zend_Registry::get('db');
$sql = "CALL sp_check_area_stock_storage(?,?,?,?,?)";
$resultSet = $db->query($sql, array(intval($area_id), intval($good_id), intval($good_color), is_null($stock_from) || $stock_from == '' ? -1 : $stock_from, is_null($stock_to) || $stock_to == '' ? -1 : $stock_to));

if (!isset($export) || !$export) {
    $this->view->resultSet = $resultSet;
    return;
}

if ($export) {
    set_time_limit(0);
    ini_set('memory_limit', -1);
    ini_set('display_error', 0);
    error_reporting(~E_ALL);

    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();
    $heads = array(
        'STT',
        'Area',
        'Model',
        'Color',
        'Stock',
        'Sell In',
        'Area Give',
        'Area Receive',
        'Return',
        'Area Storage',
        'Price',
        'Value',
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
            $sheet->setCellValue($alpha++ . $index, $i++);
            $sheet->setCellValue($alpha++ . $index, isset($this->view->areas[ $value['area_id'] ]) && $this->view->areas[ $value['area_id'] ] ? $this->view->areas[ $value['area_id'] ] : '');
            $sheet->setCellValue($alpha++ . $index, isset($this->view->goods[ $value['good_id'] ]) && $this->view->goods[ $value['good_id'] ] ? $this->view->goods[ $value['good_id'] ] : '');
            $sheet->setCellValue($alpha++ . $index, isset($this->view->good_colors[ $value['good_color'] ]) && $this->view->good_colors[ $value['good_color'] ] ? $this->view->good_colors[ $value['good_color'] ] : '');
            $sheet->setCellValue($alpha++ . $index, $value['stock']);
            $sheet->setCellValue($alpha++ . $index, $value['sell_in']);
            $sheet->setCellValue($alpha++ . $index, $value['change_sales']);
            $sheet->setCellValue($alpha++ . $index, $value['receive_change_sales']);
            $sheet->setCellValue($alpha++ . $index, $value['return']);
            $sheet->setCellValue($alpha++ . $index, $value['storage']);
            $sheet->setCellValue($alpha++ . $index, $value['price']);
            $sheet->setCellValue($alpha++ . $index, $value['value']);
            $index++;
        }
    }

    $filename = 'Stock - Area Storage - ' . date('Y-m-d H-i-s');
    $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

    $objWriter->save('php://output');

    exit;
}
