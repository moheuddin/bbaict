<?php
require 'vendor/autoload.php';
use Jenssegers\Date\Date;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

date_default_timezone_set('Asia/Dhaka');
Date::setLocale('bn');
$data = json_decode(file_get_contents("php://input"));
var_dump($data );exit;
include 'config.php';
## Read value
$where='';
/*if ((isset($_REQUEST['previous']) && isset($_REQUEST['previous']==true )){
  $where = " Where date <= $date";
}else{
$where = " Where date >= $date";
}*/


## Fetch records
$empQuery = "select * from employee $where";

$empRecords = mysqli_query($con, $empQuery);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
    //Date::now($row['date'])->format('Y-m-d');
    $data[] = array(
    		"id"=>$row['id'],
    		"date"=>Date::parse($row['date'])->format('l j F Y'),
    		"time"=>Date::parse($row['time'])->format('g:i A'),
    		"program"=>$row['program'],
    		"place"=>$row['place'],
    		"comments"=>$row['comments']
    	);
}

$response = array(
    "result" => $data
);

echo json_encode($response);
