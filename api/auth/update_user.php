<?php
require "../../vendor/autoload.php";

include_once '../../config/cors.php';
include_once '../../config/Database.php';
include_once '../../models/User.php';
include_once '../../models/Auth.php';
include_once '../../config/core.php';

$database = new Database();
$db = $database->connect();
// Instantiate user object
$user = new User($db);
// Get raw user data
$data = json_decode(file_get_contents("php://input"));
// get jwt
//$jwt = isset($data->jwt) ? $data->jwt : "";
$authHeader = $_SERVER['HTTP_AUTHORIZATION'];

$arr = explode(" ", $authHeader);

$jwt = $arr[1];
// if jwt is not empty
if ($jwt) {

    // if decode succeed, show user details
    // decode jwt
    $decoded = Auth::authenticate($jwt);
    if ($decoded["status"]) {
        // set user property values
        $user->first_name = $data->first_name;
        $user->last_name = $data->last_name;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->id = $decoded["user"]["id"];


        // update the user record
        if ($user->update()) {
            // we need to re-generate jwt because user details might be different
            $res = Auth::generateToken($user);
            // set response code
            http_response_code(200);

            // response in json format
            echo json_encode(
                array(
                    "message" => "User updated successfully.",
                    "jwt" =>  $res["token"]
                )
            );
        } // message if unable to update user
        else {
            // set response code
            http_response_code(401);

            // show error message
            echo json_encode(array("message" => "Unable to update user."));
        }
    } // if decode fails, it means jwt is invalid
    else {
        // set response code
        http_response_code(401);

        // show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $decoded["status"]["error"]
        ));
    }
} // show error message if jwt is empty
else {

    // set response code
    http_response_code(401);

    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}
