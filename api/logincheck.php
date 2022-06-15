<?php
include_once "../inc/functions.php";
include_once "../inc/dbconnect.php";
$data1 = json_decode(file_get_contents("php://input", true));
$id = $data1->val;
// echo $id;
$shop = $data1->shop;
// $id = $_GET["val"];
if(!empty($id)){
    // $shop = $_GET["shop"];
$sql = "SELECT * FROM main WHERE store='$shop' ORDER BY id DESC LIMIT 1";
$stmt = $pdo->query($sql);
$value = $stmt->fetch();
 $token = $value['token'];
 
$user = shopify_call($token, $shop, "/admin/api/2022-01/customers/$id.json", array(), 'GET');
$user = json_decode($user['response'], true);

if(isset($user['errors'])){
    $error = new stdClass;
    @$error->data->success = "false";
    @$error->error->message = "user not logged in";
    echo json_encode($error, JSON_PRETTY_PRINT);
}else{
    //  echo json_encode($user, JSON_PRETTY_PRINT);
    $name = $user['customer']['first_name'].$user['customer']['last_name'];
    $obj = new stdClass;
    @$obj->data->success = "true";
    @$obj->data->data->id = $id;
    $obj->data->data->user_name = $name;
    $obj->data->data->form_key = "";
    echo json_encode($obj, JSON_PRETTY_PRINT);
}
}else{
    $error = new stdClass;
    @$error->data->success = "false";
    @$error->error->message = "user not logged in";
    echo json_encode($error, JSON_PRETTY_PRINT);
}


?>