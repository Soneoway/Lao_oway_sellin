<?php 
$response = file_get_contents("http://dzbkjh2.myoppo.com/GetImeiByCountryCode.ashx?datetime=20151111&countryCode=84&authkey=fc18a04bd7be4844f45144e90fe6be76");
$json_response = $response;

$arr_response = json_decode($json_response,1);

$json_imei = $arr_response['Message'];

$arr_imei = json_decode($json_imei,1);
echo count($arr_imei);

exit;
