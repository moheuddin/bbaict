<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE,PATCH");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'vendor/autoload.php';
use Jenssegers\Date\Date;
date_default_timezone_set('Asia/Dhaka');
Date::setLocale('bn');
$date = date('Y-m-d');
include 'config.php';
include 'nubmer-conver.php';
## Read value
$where='';

if ($_GET['data']=='previous'){

	$empQuery = "Select * from employee where date <= '$date' order by date DESC";
}else{
	$empQuery = "Select * from employee where date >= '$date' order by date DESC";
}
//var_dump($_GET['data']);
//echo $empQuery ;exit;
//var_dump($_GET);exit;
## Fetch records
//echo $empQuery;
$empRecords = mysqli_query($con, $empQuery);
$data = array();
$sl=0;
while ($row = mysqli_fetch_assoc($empRecords)) {

    //Date::now($row['date'])->format('Y-m-d');
    $data[] = array(
    		"id"=>$row['id'],
        "sl"=>BanglaConverter::en2bn($sl),
    		"date"=>Date::parse($row['date'])->format('l j F Y'),
    		"time"=>Date::parse($row['time'])->format('g:i A'),
    		"program"=>$row['program'],
    		"place"=>$row['place'],
    		"comments"=>$row['comments']
    	);
      $sl++;
}

$response = array(
    "result" => $data
);

echo json_encode($response);
