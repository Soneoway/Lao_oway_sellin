<?php
require_once ('class/BCGFontFile.php');
require_once ('class/BCGColor.php');
require_once ('class/BCGDrawing.php');
require_once ('class/BCGcode39.barcode.php');

$font        = new BCGFontFile('./font/Arial.ttf', 14);
$text        = $_GET['code'];
$color_black = new BCGColor(0, 0, 0);
$color_white = new BCGColor(255, 255, 255);

// Barcode Part
$code = new BCGcode39();
$code->setScale(1);
$code->setThickness(60);
$code->setForegroundColor($color_black);
$code->setBackgroundColor($color_white);
$code->setFont($font);
$code->parse($text);

// Drawing Part
$drawing = new BCGDrawing('', $color_white);
$drawing->setBarcode($code);
$drawing->draw();

header('Content-Type: image/png');

$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
?>