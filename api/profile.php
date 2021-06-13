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

$data = json_decode(file_get_contents("php://input"));



//include "config.php";
include_once './config/database.php';
$databaseService = new DatabaseService();
$pdocon = $databaseService->getConnection();

$sql = '';
$result = '';
$stmt = '';
$userName = '';
$id = '';

$method = $_SERVER['REQUEST_METHOD'];

$date = date('Y-m-d');

$response = [];


switch ($method) {
    case 'GET':

        if ( $id > 0) {
            $sql = "select * from tbl_users where id=:id";
            $sql_data = ['id' => $id];
            $stmt = $pdocon->prepare($sql);
            $stmt->execute($sql_data);
            $result = $stmt->fetch();
        } else {
            $sql = "SELECT * FROM tbl_users order by username";
            $stmt = $pdocon->query($sql);

            // Loop through query and push results into $someArray;
            while ($row = $stmt->fetch()) {

                array_push($response, [
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'isActive' => $row['isActive'],
                    'roleid' => $row['roleid']
                ]);
            }
            echo json_encode(array('result' => $response, 'message' => 200));

        }

      break;

      case 'POST':
        $data =$data->data;

        $id='';
        if ( isset( $data ) && property_exists( $data, 'id' ) ) {
          $id=$data->id;
        }
        $username=$data->username;
        $email=$data->email;

        if ($id!='') {
          $id=$data->id;
          $sql = "update tbl_users set username = ?, email = ? where id = ?";
          $stmt= $pdocon->prepare($sql);
          $response = $stmt->execute([$username, $email,  $id]);

          if ( isset( $data ) && property_exists( $data, 'password' ) ) {
            $pass=$data->password;
            $stmt= $pdocon->prepare("update tbl_users set password=? where id=?");
            $stmt->execute([md5($pass),  $id]);
          }
          echo json_encode(array('result' =>  'updated'));
        }
          $pdocon=null;
         $stmt=null;
    break;

}



