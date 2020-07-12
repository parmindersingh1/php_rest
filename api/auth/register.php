<?php

include_once '../../config/cors.php';
include_once '../../config/Database.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();
// Instantiate user object
$user = new User($db);
// Get raw user data
$data = json_decode(file_get_contents("php://input"));

$user->first_name = $data->first_name;
$user->last_name = $data->last_name;
$user->email = $data->email;
$user->password = $data->password;

// create the user
if (
    !empty($user->first_name) &&
    !empty($user->email) &&
    !empty($user->password) &&
    $user->create()
) {

    // set response code
    http_response_code(200);

    // display message: user was created
    echo json_encode(array("message" => "User created successfully."));
} // message if unable to create user
else {

    // set response code
    http_response_code(400);

    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create user."));
}
?>