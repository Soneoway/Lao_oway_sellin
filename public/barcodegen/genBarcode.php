<?php
// Including all required classes
require_once ('class/BCGFontFile.php');
require_once ('class/BCGColor.php');
require_once ('class/BCGDrawing.php');
require_once ('class/BCGean13.barcode.php');
// require_once ('class/BCGcode39.barcode.php');

$font        = new BCGFontFile('./font/Arial.ttf', 18);
$text        = $_GET['code'];
$color_black = new BCGColor(0, 0, 0);
$color_white = new BCGColor(255, 255, 255);

$drawException = null;
try {
	$code = new BCGean13();
	$code->setScale(2);// Resolution
	$code->setThickness(30);// Thickness
	$code->setForegroundColor($color_black);// Color of bars
	$code->setBackgroundColor($color_white);// Color of spaces
	$code->setFont($font);// Font (or 0)
	$code->parse($text);// Text
} catch (Exception $exception) {
	$drawException = $exception;
}

/* Here is the list of the arguments
1 - Filename (empty : display on screen)
2 - Background color */
$drawing = new BCGDrawing('', $color_white);
if ($drawException) {
	$drawing->drawException($drawException);
} else {
	$drawing->setBarcode($code);
	$drawing->draw();
}

// Header that says it is an image (remove it if you save the barcode to a file)
header('Content-Type: image/png');

// Draw (or save) the image into PNG format.

$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

?>