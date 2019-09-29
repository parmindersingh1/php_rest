<?php
require "../../vendor/autoload.php";

use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/Database.php';
include_once '../../models/User.php';
include_once '../../config/core.php';

$database = new Database();
$db = $database->connect();
// Instantiate user object
$user = new User($db);
// Get raw user data
$data = json_decode(file_get_contents("php://input"));

$user->email = $data->email;
$user->password = $data->password;
$email_exists = $user->emailExists();

//if ($row = $user->login()) {
//    $id = $row['id'];
//    $firstname = $row['first_name'];
//    $lastname = $row['last_name'];
//    $email = $row['email'];
//    $password2 = $row['password'];
//
//    if (password_verify($user->password, $password2)) {
//        $secret_key = "YOUR_SECRET_KEY";
//        $issuer_claim = "THE_ISSUER"; // this can be the servername
//        $audience_claim = "THE_AUDIENCE";
//        $issuedat_claim = time(); // issued at
//        $notbefore_claim = $issuedat_claim + 10; //not before in seconds
//        $expire_claim = $issuedat_claim + 60; // expire time in seconds
//        $token = array(
//            "iss" => $issuer_claim,
//            "aud" => $audience_claim,
//            "iat" => $issuedat_claim,
//            "nbf" => $notbefore_claim,
//            "exp" => $expire_claim,
//            "data" => array(
//                "id" => $id,
//                "firstname" => $firstname,
//                "lastname" => $lastname,
//                "email" => $email
//            ));
//
//        http_response_code(200);
//
//        $jwt = JWT::encode($token, $secret_key);
//        echo json_encode(
//            array(
//                "message" => "Successful login.",
//                "jwt" => $jwt,
//                "email" => $email,
//                "expireAt" => $expire_claim
//            ));
//    } else {
//
//        http_response_code(401);
//        echo json_encode(array("message" => "Login failed.", "password" => $password));
//    }
//}

// check if email exists and if password is correct
if ($email_exists && password_verify($data->password, $user->password)) {

    $token = array(
        "iss" => $iss,
        "aud" => $aud,
        "iat" => $iat,
        "nbf" => $nbf,
        "data" => array(
            "id" => $user->id,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email
        )
    );

    // set response code
    http_response_code(200);

    // generate jwt
    $jwt = JWT::encode($token, $key);
    echo json_encode(
        array(
            "message" => "Successful login.",
            "jwt" => $jwt
        )
    );

} // login failed
else {

    // set response code
    http_response_code(401);

    // tell the user login failed
    echo json_encode(array("message" => "Login failed."));
}
?>