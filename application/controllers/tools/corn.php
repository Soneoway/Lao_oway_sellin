<?php
$servername = "203.151.4.229";
$username = "sellin";
$password = "cSN8aVjd39C7@";
$db = "warehouseme";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

$today = date("Y-m-d");

$conn->query("delete from daily_stock_new where stock_date = '{$today}'");

$sql   = "select warehouse_id,good_id,good_color,count(`imei_sn`) as imei_storage from imei 
where old_data is null and out_date is null and  warehouse_id is not null and type <> 4 group by warehouse_id,good_id,good_color;";
$result = $conn->query($sql);

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Vientiane');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , EOL;

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
       if($row['warehouse_id'] != ''):
          //Insert into Daily Balance
          $sra = "insert into daily_stock_new(stock_date,warehouse_id,good_id,good_color_id,imei_storage)
           values('{$today}','".$row['warehouse_id']."','".$row['good_id']."','".$row['good_color']."','".$row['imei_storage']."');";
          $conn->query($sra);
       endif;
    }
} else {
    echo "0 results";
}

        echo json_encode(array());
        exit;

?>