<?php 
date_default_timezone_set('Asia/Shanghai');
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Bangkok');

// $servername = "";
// $username   = "";
// $password   = '';
// $dbname     = "";

// // Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);
// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// } 

// $conn->query('SET NAMES utf8');

$conn = zend_registry::get('db');

$appid  = "thailand";
$secret = "ocbehiyzbvevhqswuqwixxksovuwlxci";
$agentFullName ="Thailand";

// Test: https://test1-open.myoas.net 
// Production: https://open.realme.com

$api_url = "https://open.realme.com/ecard/queryByDate";
function getSign($appid,$timestamp,$secret,$from,$to,$agentFullName){
   $str  = "";
   // $str .= "appid=".$appid."&timestamp=".$timestamp."&agentFullName=".$agentFullName."&from=".$from ."&to=".$to."&secret=".$secret;
   $str .= "agentFullName=".$agentFullName."&appid=".$appid."&from=".$from."&timestamp=".$timestamp ."&to=".$to."&secret=".$secret;
   $k = md5($str);
   return $k;
 }
// Signature Method
// Sort all the parameters in lexicographic order excluding the secret field
// Join the key-value pairs (k=v) with "&", remember to exclude the empty value parameters
// kvStr="paramName1=paramValue1&paramName2=paramValue2&paramNameN=paramValueN"
// Append your secret to the kvStr generated at Step 2 and calculate the signature on the generated string. (xxx need to be changed to your specified app secret value.)
// sign=MD5(kvStr + "&secret=xxx")
// completed pseudo code: sign=MD5(paramA=paramValue1&paramB=paramValue2&secret=secretValue)
$timestamp          = round(microtime(true) * 1000);
$from_time          = strtotime("-7 day",strtotime(date("YmdHis"))) * 1000;
$to_time            = $timestamp;
// echo $newTimeStamp=strtotime(date('m/d/Y H:m:sA', $oldTimeStamp) . " + 4 hours");
$sign      = getSign($appid,$timestamp,$secret,$from_time,$to_time,$agentFullName);

$full_url = $api_url."?appid=".$appid."&timestamp=".$timestamp."&agentFullName=".$agentFullName."&from=".$from_time ."&to=".$to_time."&sign=".$sign;
 
$curlSession = curl_init();
$json = file_get_contents($full_url);
$obj  = json_decode($json);
// echo "<br />";
$items = $obj->data;

foreach($items as $key=>$i){
   $imei_sn = $i->imei;
   $regDate = date("Y-m-d H:i:s",strtotime("-1 hour",$i->regDate/1000));
   $sql = "update imei set activated_date =  '{$regDate}' where imei_sn = '{$imei_sn}' and activated_date is null;";
   $conn->query($sql);

	echo $i->imei."<br>";
}
?>