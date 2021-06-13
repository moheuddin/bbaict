<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require "./vendor/autoload.php";
include_once './config/database.php';


$email = '';
$password = '';

$databaseService = new DatabaseService();
$pdocon = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));


//$email = $_POST['email'];
$email = $data->username;
$password = $data->password;

$table_name = 'tbl_users';

$stmt = $pdocon->prepare("SELECT * FROM tbl_users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

//if ($user && password_verify($password, $user['password']))
if ($user)
{
  $id = $user['id'];
  $role = $user['roleid'];
  $username = $user['username'];
  if(md5($password)==$user['password'])
  {

    use ReallySimpleJWT\Token;

    $userId = 12;
    $secret = 'sec!ReT423*&';
    $expiration = time() + 3600;
    $issuer = 'localhost';
    
    $token = Token::create($userId, $secret, $expiration, $issuer);

    echo $token;
    exit;     
    
    
    //-----------------------------New test
          $issuedat_claim = time(); // issued at
         $notbefore_claim = $issuedat_claim + 10; //not before in seconds
          $expire_claim = $issuedat_claim + 100000000000000; // expire time in seconds
          $server = "example.com";

          # Note that if you want to use RSA just pass the 3rd argument (public key)
          # See above how to create the RSA Key pair.
          //new MyWrapper();
          require_once "include.php";

            $easyJwt = MyWrapper::getWrapper();
            $jwt = $easyJwt->createJwtData(
                ["message" => "Successful login.",
              "id" => $user['id'],
              "username" => $user['username'],
              "email" => $user['email'],
              "role" => $user['roleid'],
              "expiry_at" => $expire_claim],
            600000000000000);
            //print_r($jwt);
            $return = $easyJwt->generateToken($jwt);


    

            //print_r($jwt);
          echo json_encode(array("jwt" => $return,
            "message" => "Successful login.",
            "id" => $user['id'],
            "username" => $user['username'],
            "email" => $user['email'],
            "role" => $user['roleid'],
            "expiry_at" => $expire_claim));

exit;

  }
  else{

      http_response_code(401);
      echo json_encode(array("message" => "Login failed.", "password" => $password));
      exit;
  }
}else{
  echo json_encode(array("message" => "Password or User wrong!"));
}
