<?php
require "../../vendor/autoload.php";

use \Firebase\JWT\JWT;


class Auth
{

 private static $key = "YOUR_SECRET_KEY";
 // Constructor with DB
 public function __construct()
 {
 }

 public static  function generateToken($user)
 {
  // variables used for jwt
  $payload = array(
   'iss' => $_SERVER['HTTP_HOST'],
   'aud' => $_SERVER['HTTP_HOST'],
   // 'exp' => time() + 600, // token expiry time in timestamp We have used current we have used 10 minutes as expiry time
   'user' => $user
  );
  try {
   $jwt = JWT::encode($payload, self::$key, 'HS256'); // last parameter is the Engryption Algorithm name
   $res = array("status" => true, "token" => $jwt);
  } catch (UnexpectedValueException $e) {
   $res = array("status" => false, "Error" => $e->getMessage());
  }
  return $res;
 }

 public static  function authenticate($JWT)
 {
  try {
   $decoded = JWT::decode($JWT, self::$key, array('HS256'));
   $payload = json_decode(json_encode($decoded), true);

   if ($payload['user']) // verify that the user id coming in after login api is equals to the decoded payload user id, if matched then the token is fine and data not tempered
   {
    $res = array("status" => true, "user" => $payload['user']);
   } else {
    $res = array("status" => false, "error" => "Invalid Token or Token Exipred, So Please login Again!");
   }
  } catch (UnexpectedValueException $e) {
   $res = array("status" => false, "error" => $e->getMessage());
  }
  return $res;
 }
}
