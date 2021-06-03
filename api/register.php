<?php

//$req_dump = print_r($_REQUEST, true);
//$fp = file_put_contents('request.log', $req_dump, FILE_APPEND);
//echo getHeaders("Authorization");
function getHeaders($header_name=null)
{
    $keys=array_keys($_SERVER);

    if(is_null($header_name)) {
            $headers=preg_grep("/^HTTP_(.*)/si", $keys);
    } else {
            $header_name_safe=str_replace("-", "_", strtoupper(preg_quote($header_name)));
            $headers=preg_grep("/^HTTP_${header_name_safe}$/si", $keys);
    }

    foreach($headers as $header) {
            if(is_null($header_name)){
                    $headervals[substr($header, 5)]=$_SERVER[$header];
            } else {
                    return $_SERVER[$header];
            }
    }

    return $headervals;
}
//print_r(getHeaders());

die();
include_once './config/database.php';

//header("Access-Control-Allow-Origin: * ");
//header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: post");
//header("Access-Control-Max-Age: 3600");
//header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$firstName = '';
$lastName = '';
$email = '';
$password = '';
$conn = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

//$data = json_decode(file_get_contents("php://input"));

$firstName = $_POST['first_name'];
$lastName = $_POST['last_name'];
$email = $_POST['email'];
$password = $_POST['password'];

$table_name = 'Users';

$query = "INSERT INTO " . $table_name . "
                SET username = :username,
                    email = :email,
                    password = :password";

$stmt = $conn->prepare($query);

$stmt->bindParam(':username', $firstName);
$stmt->bindParam(':email', $email);

$password_hash = password_hash($password, PASSWORD_BCRYPT);

$stmt->bindParam(':password', $password_hash);


if($stmt->execute()){

    http_response_code(200);
    echo json_encode(array("message" => "User was successfully registered."));
}
else{
    http_response_code(400);

    echo json_encode(array("message" => "Unable to register the user."));
}
