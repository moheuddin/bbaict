<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE,PATCH");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
//require "./vendor/autoload.php";
//use \Firebase\JWT\JWT;


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
$jwt = $string;

//echo $token;
//$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJUSEVfSVNTVUVSIiwiYXVkIjoiVEhFX0FVRElFTkNFIiwiaWF0IjoxNjIyNTY3Nzc4LCJuYmYiOjE2MjI1Njc3ODgsImV4cCI6MTYyMjU2NzgzOCwiZGF0YSI6eyJpZCI6IjIyIiwidXNlcm5hbWUiOiJtb2hldWRkaW4iLCJlbWFpbCI6Im1fdWRkaW5pdEB5YWhvby5jb20ifX0.LDe_35jMpGR3-WnZDFLNLZLROSzf7QvYqywek1fDTAU";
/*$publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
ehde/zUxo6UvS7UrBQIDAQAB
-----END PUBLIC KEY-----
EOD;*/

//$decoded = JWT::decode($token, $secret_key, array('HS256'));
//var_dump($decoded );
//$decoded = JWT::decode($jwt,$publicKey,array('RSA256'));
//var_dump($decoded);
# If exists $_SERVER['HTTP_AUTHENTICATION'] = "Bearer $TOKEN"
//new MyWrapper();
require_once  "./vendor/autoload.php";
//$data = $jwtWrapper->extractData();

# If you want decode directly:
//$data = $jwtWrapper->extractData($jwt);
//exit;
require_once  'check-jwt.php';
MyWrapper::getWrapper();
$easyJwt = MyWrapper::getWrapper();
//$tokenDecomposed = $easyJwt->extractData();

try {
    $tokenDecomposed = $easyJwt->extractData();
} catch (Exception $ex) {
    //header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die($ex);

}

//$check = $jwtWrapper->extractData();

# If you want decode directly:
//$check = $jwtWrapper->extractData($token);


switch ($method) {
    case 'GET':
        $id = isset($_GET['id']) ? intval($_GET['id']) : '';
        $filter = (isset($_GET['previous']) ? '<=' : '>=');
        if ( $id > 0) {
            $sql = "select * from employee where id=:id";
            $sql_data = ['id' => $id];
            $stmt = $pdocon->prepare($sql);
            $stmt->execute($sql_data);
            $result = $stmt->fetch();
        } else {
            $sql = "SELECT * FROM employee WHERE date $filter $date order by date, time desc";
            $stmt = $pdocon->query($sql);

            // Loop through query and push results into $someArray;
            while ($row = $stmt->fetch()) {

                array_push($response, [
                    'id' => $row['id'],
                    'date' => $row['date'],
                    'time' => $row['time'],
                    'program' => $row['program'],
                    'place' => $row['place'],
                    'comments' => $row['comments'],
                ]);
            }
            echo json_encode(array('result' => $response, 'message' => 200));

        }

        break;

    case 'DELETE':
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '';

        $sql = "DELETE FROM `employee` WHERE id = ?";
        $q = $pdocon->prepare($sql);
        $response = $q->execute(array($id));
        echo json_encode(array('message' => $response));
        $pdocon=null;

        break;

    case 'PATCH':

      $request = $data->params->data->fields;
      $date=$request->date;
      $time=$request->time;
      $program= $request->program;
      $place=$request->place;
      $comments= $request->comments;
      $id= $request->id;
       /* $date = isset($_REQUEST['date']) ? $_REQUEST['date'] : '';
        $time = isset($_REQUEST['time']) ? $_REQUEST['time'] : '';
        $program = isset($_REQUEST['program']) ? $_REQUEST['program'] : '';
        $comments = isset($_REQUEST['comments']) ? $_REQUEST['comments'] : '';
        $place = isset($_REQUEST['place']) ? $_REQUEST['place'] : '';
      */


        // change existing record
        $sql = "update employee set date = ?, time = ?, program=?,comments=?,  place=? where id = ?";
        $stmt= $pdo->prepare($sql);
        $response = $stmt->execute([$date, $time, $program, $place, $comments, $id]);
        echo json_encode(array('message' => $response));
        $pdocon=null;
        exit;

    case 'POST':
        // add new record
        $request = $data->params->data->fields;
        //var_dump($request );exit;

        $date=property_exists($request, "date")? $request->date:'';
        $time=property_exists($request, "time")? $request->time:'';
        $program= property_exists($request, "program")? $request->program:'';
        $place= property_exists($request, "place")? $request->place: '';
        $comments= property_exists($request, "comments")? $request->comments:'';
        $date = date_create()->format('Y-m-d H:i:s');
        try {
          $sql = "INSERT INTO employee (date, time, program, comments,  place, date_created) VALUES (?, ?, ?,?,?,?)";
          $stmt= $pdo->prepare($sql);
          $stmt->execute([$date, $time, $program, $comments, $place,  $date]);

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

        //$decoded = JWT::decode($token, $secret_key, array('HS256'));

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
