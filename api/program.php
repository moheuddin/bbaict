<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET,UPLATE, DELETE,PUT,PATCH");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require "vendor/autoload.php";

require_once "include.php";

$easyJwt = MyWrapper::getWrapper();

//$tokenDecomposed = $easyJwt->extractData();

try {
  $tokenDecomposed = $easyJwt->extractData();
} catch (Exception $ex) {
  header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
  die($ex->getMessage());
}

date_default_timezone_set('Asia/Dhaka');

//$data = json_decode(file_get_contents("php://input"),true);
$data = json_decode(file_get_contents("php://input"));

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

$date = date('Y-m-d');

$response = [];

switch ($method) {
    case 'GET':
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '';

        $filter ='';
        if((isset($_REQUEST['data'])) && ($_REQUEST['data']=='previous')){
          $filter = '<=';
        }else{
          $filter = '>=';
        }

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


    case 'POST':
        // add new record
        $data =$data->data;
        $id='';
        if ( isset( $data ) && property_exists( $data, 'id' ) ) {
          $id=$data->id;
        }
        $date=$data->date;
        $time=$data->time;
        $program= $data->program;
        $place=$data->place;
        $comments= $data->comments;
        $date = date_create()->format('Y-m-d H:i:s');
        if ($id!='') {
          $id=$data->id;

          $sql = "update employee set date = ?, time = ?, program=?,comments=?,  place=? where id = ?";
          $stmt= $pdocon->prepare($sql);
          $response = $stmt->execute([$date, $time, $program, $place, $comments, $id]);
          echo json_encode(array('result'=> $id, 'message' => "Updated successfuly."));

        }else{

          try {
            $sql = "INSERT INTO employee (date, time, program, comments,  place, date_created) VALUES (?, ?, ?,?,?,?)";
            $stmt= $pdocon->prepare($sql);
            $stmt->execute([$date, $time, $program, $comments, $place,  $date]);

            $id = $pdocon->lastInsertId();

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
