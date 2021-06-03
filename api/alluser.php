<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE,PATCH");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require "./vendor/autoload.php";
use \Firebase\JWT\JWT;

date_default_timezone_set('Asia/Dhaka');
/*include 'protected.php';
if(!is_authenticated("YOUR_SECRET_KEY")){
json_encode(array('result'=>"No authenticated"));
exit(0);
}
 */
$data = json_decode(file_get_contents("php://input"),true);

//print_r($data);


//include "config.php";
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
$token= getToken();
$string = str_replace("\r", "", $token);
$string = str_replace("\n", "", $token);
$token = $string;
//echo $token;
//$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJUSEVfSVNTVUVSIiwiYXVkIjoiVEhFX0FVRElFTkNFIiwiaWF0IjoxNjIyNTY3Nzc4LCJuYmYiOjE2MjI1Njc3ODgsImV4cCI6MTYyMjU2NzgzOCwiZGF0YSI6eyJpZCI6IjIyIiwidXNlcm5hbWUiOiJtb2hldWRkaW4iLCJlbWFpbCI6Im1fdWRkaW5pdEB5YWhvby5jb20ifX0.LDe_35jMpGR3-WnZDFLNLZLROSzf7QvYqywek1fDTAU";
//$secret_key = "YOUR_SECRET_KEY";
//$decoded = JWT::decode($token, $secret_key, array('HS256'));
//var_dump($decoded );


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

        $sql = "DELETE FROM `tbl_users` WHERE id = ?";
        $q = $pdocon->prepare($sql);
        $response = $q->execute(array($id));
        echo json_encode(array('message' => $response));
        $pdocon=null;

        break;

    case 'PATCH':

      $request = $data->params->data->fields;
      $username=$request->username;
      $email=$request->email;
      $isActive= $request->isActive;
      $roleid=$request->roleid;
      $id= $request->id;
       /* $date = isset($_REQUEST['date']) ? $_REQUEST['date'] : '';
        $time = isset($_REQUEST['time']) ? $_REQUEST['time'] : '';
        $program = isset($_REQUEST['program']) ? $_REQUEST['program'] : '';
        $comments = isset($_REQUEST['comments']) ? $_REQUEST['comments'] : '';
        $place = isset($_REQUEST['place']) ? $_REQUEST['place'] : '';
      */


        // change existing record
        $sql = "update tbl_users set username = ?, email = ?, isActive=?,roleid=? where id = ?";
        $stmt= $pdo->prepare($sql);
        $response = $stmt->execute([$username, $email, $isActive, $roleid, $id]);
        echo json_encode(array('message' => $response));
        $pdocon=null;
        exit;

    case 'POST':
        // add new record
        $request = $data->params->data->fields;
        //var_dump($request );exit;

        $date=property_exists($request, "username")? $request->username:'';
        $time=property_exists($request, "email")? $request->email:'';
        $program= property_exists($request, "isActive")? $request->isActive:'';
        $place= property_exists($request, "roleid")? $request->roleid: '';
        $date = date_create()->format('Y-m-d H:i:s');
        try {
          $sql = "INSERT INTO tbl_users (username, email, isActive, roleid date_created) VALUES (?, ?, ?,?,?)";
          $stmt= $pdo->prepare($sql);
          $stmt->execute([$username, $email, $isActive, $roleid,  $date]);

          $id = $stmt->lastInsertId();

          echo json_encode(array('result' => $id, 'message' => 'Data has been added Successfuly!'));

        } catch (PDOException $e) {
          echo "Error: " .$e->getMessage();
        }

        $stmt->close();
        $con->close();
        break;
}

function en2bnNumber($number)
{
    $replace_array = array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "১০");
    $search_array = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
    $bn_number = str_replace($search_array, $replace_array, $number);

    return $bn_number;
}

function getToken() {

  $token='';
  foreach($_SERVER as $key => $value) {
      if (substr($key, 0, 5) <> 'HTTP_') {
          continue;
      }
      $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
      if ($header === 'Authorization'){
      //$headers[$header] = $value;

      $token = $value;
      break;
      }

  }
  return $token;
}
exit;
/**
 * Get header Authorization
 * */
 function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

function getRequestHeaders() {
  $headers = array();
  $token='';
  foreach($_SERVER as $key => $value) {
      if (substr($key, 0, 5) <> 'HTTP_') {
          continue;
      }
      $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
      if ($header === 'Authorization'){
      //$headers[$header] = $value;
      $token = $value;
      break;
      }

  }
  return $token;
}
/**
* get access token from header
* */
function getBearerToken() {
$headers = getAuthorizationHeader();
// HEADER: Get the access token from the header
if (!empty($headers)) {
    if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
    }
}
return null;
}

//$token = getRequestHeaders();
$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJUSEVfSVNTVUVSIiwiYXVkIjoiVEhFX0FVRElFTkNFIiwiaWF0IjoxNjIyNTU4NzMzLCJuYmYiOjE2MjI1NTg3NDMsImV4cCI6MTYyMjU1ODc5MywiZGF0YSI6eyJpZCI6IjIyIiwidXNlcm5hbWUiOiJtb2hldWRkaW4iLCJlbWFpbCI6Im1fdWRkaW5pdEB5YWhvby5jb20ifX0.-pud_QFkS3XVpSD8rKIaJ02qcXI2uEM5HTJriCeegCk";
$secret_key = "YOUR_SECRET_KEY";
if($token !=''){

    try {

        $decoded = JWT::decode($token, $secret_key, array('HS256'));

        // Access is granted. Add code of the operation here

        echo json_encode(array(
            "message" => "Access granted:",
            "error" => $e->getMessage()
        ));


        }catch (Exception $e){

        http_response_code(401);

        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));

    }

}else{
    echo 'Empty authorization';

}
