<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE,PATCH");
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

date_default_timezone_set('Asia/Dhaka');

$data = json_decode(file_get_contents("php://input"));

include_once './config/database.php';
$databaseService = new DatabaseService();
$pdocon = $databaseService->getConnection();

$sql = '';
$sql_data = [];
$result = '';
$stmt = '';
$debug = '';
$userName = '';
$id = '';

$method = $_SERVER['REQUEST_METHOD'];
//echo $method;
//exit;

// $request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
//$input = json_decode(file_get_contents('php://input'),true);
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

    case 'DELETE':
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '';
        echo $id;
        $sql = "DELETE FROM `tbl_users` WHERE id = ?";
        $q = $pdocon->prepare($sql);
        $response = $q->execute(array($id));
        echo json_encode(array('message' => $response));
        $pdocon=null;

        break;

    case 'POST':
        // add new record
      // add new record
      $data =$data->data;

      $id='';
      if ( isset( $data ) && property_exists( $data, 'id' ) ) {
        $id=$data->id;
      }
      $username=$data->username;
      $email=$data->email;
      $isActive= $data->isActive;
      $roleid=$data->roleid;
      $date = date_create()->format('Y-m-d H:i:s');
      if ($id!='') {
        $id=$data->id;
        $sql = "update tbl_users set username = ?, email = ?, isActive=?,roleid=? where id = ?";
        $stmt= $pdocon->prepare($sql);
        $response = $stmt->execute([$username, $email, $isActive, $roleid,  $id]);

        if ( isset( $data ) && property_exists( $data, 'pass' ) ) {
          $pass=$data->pass;
          $stmt= $pdocon->prepare("update tbl_users set password=? where id=?");
          $stmt->execute([md5($pass),  $id]);
        }
        echo json_encode(array('result' =>  'updated'));

      }else{

        try {
          $sql = "INSERT INTO tbl_users (username, email, isActive, roleid, created_at) VALUES (?, ?, ?,?,?)";
          $stmt= $pdocon->prepare($sql);
          $stmt->execute([$username, $email, $isActive, $roleid,  $date]);

          $id = $pdocon->lastInsertId();

          if ( isset( $data ) && property_exists( $data, 'pass' ) ) {
            $pass=$data->pass;
            $stmt= $pdocon->prepare("update tbl_users set password=? where id=?");
            $stmt->execute([md5($pass),  $id]);
          }

          echo json_encode(array('result' => $id, 'message' => 'Data has been added Successfuly!'));

        } catch (PDOException $e) {
          echo "Error: " .$e->getMessage();
        }

      }
      $pdocon=null;
      $stmt=null;
        break;
}

function en2bnNumber($number)
{
    $replace_array = array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "১০");
    $search_array = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
    $bn_number = str_replace($search_array, $replace_array, $number);

    return $bn_number;
}
