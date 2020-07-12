<?php
require "../../vendor/autoload.php";

include_once '../../config/cors.php';
include_once '../../config/Database.php';
include_once '../../config/core.php';
include_once '../../models/User.php';
include_once '../../models/Auth.php';

$database = new Database();
$db = $database->connect();
// Instantiate user object
$user = new User($db);
$jwt = null;
// Get raw user data
$data = json_decode(file_get_contents("php://input"));
$authHeader = $_SERVER['HTTP_AUTHORIZATION'];

$arr = explode(" ", $authHeader);


//echo json_encode(array(
//    "message" => "sd" . $arr[1]
//));

$jwt = $arr[1];

if ($jwt) {
    $decoded = Auth::authenticate($jwt);
    // Access is granted. Add code of the operation here
    unset($decoded["user"]["password"]);
    if ($decoded["status"]) {
        echo json_encode(array(
            "message" => "User fetched successfully",
            "user" => $decoded["user"]
        ));
    } else {
        http_response_code(401);

        echo json_encode(array(
            "message" => $decoded["error"]
        ));
    }
} else {
    http_response_code(401);

    echo json_encode(array(
        "message" => "Access denied."
    ));
}
