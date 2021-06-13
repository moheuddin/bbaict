<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET,UPLATE, DELETE,PUT,PATCH");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require "vendor/autoload.php";

require_once "include.php";

$easyJwt = MyWrapper::getWrapper();

$tokenDecomposed = $easyJwt->extractData();

try {
  $tokenDecomposed = $easyJwt->extractData();
} catch (Exception $ex) {
  header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
  die($ex->getMessage());
}
echo 'success';
