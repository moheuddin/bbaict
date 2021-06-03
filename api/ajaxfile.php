<?php
require 'vendor/autoload.php';
use Jenssegers\Date\Date;
date_default_timezone_set('Asia/Dhaka');
Date::setLocale('bn');

include 'config.php';

## Read value
$draw = $_REQUEST['draw'];
$row = $_REQUEST['start'];
$rowperpage = $_REQUEST['length']; // Rows display per page
$columnIndex = $_REQUEST['order'][0]['column']; // Column index
$columnName = $_REQUEST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_REQUEST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($con,$_REQUEST['search']['value']); // Search value

## Search 
$searchQuery = " ";
if($searchValue != ''){
	$searchQuery = " and (emp_name like '%".$searchValue."%' or 
        email like '%".$searchValue."%' or 
        city like'%".$searchValue."%' ) ";
}

## Total number of records without filtering
$sel = mysqli_query($con,"select count(*) as allcount from employee");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($con,"select count(*) as allcount from employee WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from employee WHERE 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($con, $empQuery);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
    //Date::now($row['date'])->format('Y-m-d');
    $data[] = array(
    		"date"=>Date::parse($row['date'])->format('l j F Y'),
    		"time"=>Date::parse($row['time'])->format('g:i A'),
    		"program"=>$row['program'],
    		"place"=>$row['place'],
    		"salary"=>$row['salary'],
    		"city"=>$row['city']
    	);
}

## Response
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);

echo json_encode($response);
