<?php
include_once "../inc/functions.php";
include_once "../inc/dbconnect.php";
$data1 = json_decode(file_get_contents("php://input", true));
$id = $data1->val;
// echo $id;
$shop = $data1->shop;
if(isset($id)){
// $shop = $_GET["shop"];
$sql = "SELECT * FROM main WHERE store='$shop' ORDER BY id DESC LIMIT 1";
$stmt = $pdo->query($sql); 
$value = $stmt->fetch();
$token = $value['token'];
//  echo $token;
$user = shopify_call($token, $shop, "/admin/api/2022-01/customers/$id.json", array(), 'GET');
$user = json_decode($user['response'], true);
    // print_r($user);
    $name = $user['customer']['first_name'].$user['customer']['last_name'];
    $obj = new stdClass;
    @$obj->success->error = "false";
    @$obj->success->status = "true";
    @$obj->success->id = $id;
    @$obj->data->id = $id;
    $obj->data->email = $user['customer']['email'];
    $obj->data->prefix ="";
    $obj->data->suffix = "";
    $obj->data->dob = "";
    $obj->data->firstname = $name;
    $obj->data->middlename = "";
    $obj->data->lastname = $user['customer']['last_name'];
    $obj->data->company = $user['customer']['default_address']['company'];
    $obj->data->street = $user['customer']['default_address']['address1'];
    $obj->data->city = $user['customer']['default_address']['city'];
    $obj->data->region = $user['customer']['default_address']['province'];
    $obj->data->country = $user['customer']['default_address']['country'];
    $obj->data->postcode = $user['customer']['default_address']['zip'];
    $obj->data->telephone = $user['customer']['default_address']['phone'];
    $obj->data->vat = "";
    $obj->data->profile_image = "";
    $obj->data->corporate_logo = "";
    echo json_encode($obj, JSON_PRETTY_PRINT);
}else{
     $error = new stdClass;
    @$error->data->success = "false";
    @$error->error->message = "user not logged in";
    echo json_encode($error, JSON_PRETTY_PRINT);
}


?>