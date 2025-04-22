<?php
require_once 'PHPExcel.php';
$PHPExcel = new PHPExcel();
$heads = array(
    'No.',
    'FROM WAREHOUSE',
    'TO WAREHOUSE',
    'SN',
    'ORDER NAME',
    'INVOICE NUMBER',
    'INVOICE PREFIX',
    'CREATED AT',
    'TRANSPORT DATE'
);


$PHPExcel->setActiveSheetIndex(0);
$sheet = $PHPExcel->getActiveSheet();

$alpha = 'A';
$index = 1;
foreach ($heads as $key) {
    $sheet->setCellValue($alpha . $index, $key);
    $alpha++;
}
$index = 2;


$QInvoicePrefix  = new Application_Model_InvoicePrefix();
$invoice_prefixs = $QInvoicePrefix->get_cache();


$i = 1;
$markets = array();

foreach ($data as $item) {
    $alpha = 'A';
    $sheet->setCellValue($alpha++ . $index, $i++);
    $sheet->setCellValue($alpha++ . $index, $item['from_warehouse']);
    $sheet->setCellValue($alpha++ . $index, $item['to_warehouse']);
    $sheet->getCell($alpha++ . $index)->setValueExplicit($item['sn'], PHPExcel_Cell_DataType::TYPE_STRING);
    $sheet->getCell($alpha++ . $index)->setValueExplicit($item['order_name'], PHPExcel_Cell_DataType::TYPE_STRING);
    $sheet->setCellValue($alpha++ . $index, $item['invoice_number']);
    $sheet->setCellValue($alpha++ . $index, $invoice_prefixs[$item['invoice_prefix']] ? $invoice_prefixs[$item['invoice_prefix']] : 'x');
    $sheet->setCellValue($alpha++ . $index, $item['created_at'] ? $item['created_at'] : 'x');
    $sheet->setCellValue($alpha++ . $index, $item['transport_date'] ? $item['transport_date'] : 'x');
    $index++;
}

$filename = 'List_Invoice_Internal_Report_' . date('d/m/Y');
$objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

$objWriter->save('php://output');

exit;