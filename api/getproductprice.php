<?php
include_once "../inc/functions.php";
include_once "../inc/dbconnect.php";


$data = json_decode(file_get_contents("php://input", true));
// print_r($data);
//$qty = $data->quantity;

//$id = $data->id;
$id = $_REQUEST['var_id'];
$shop = $_REQUEST['shop'];
$qty = $_REQUEST['quantity'];
//$shop =$data->shop;
$sql = "SELECT * FROM main WHERE store='$shop' ORDER BY id DESC LIMIT 1";
$stmt = $pdo->query($sql);
//print_r($stmt);
// $value = $stmt->fetchAll();
if ($value = $stmt->fetch()) {
    // echo "data selected<br>";
    $token = $value['token'];
    $variant = shopify_call($token, $shop, "/admin/api/2021-10/variants/$id.json", array(), 'GET');
    //print_r($variant);
    $variant = json_decode($variant['response'], true);
    if (isset($variant['errors'])) {
        $product = shopify_call($token, $shop, "/admin/api/2021-10/products/$id.json", array(), 'GET');
        // print_r($variant);
        $product = json_decode($product['response'], true);
        $price = $product['product']['variants']['0']['price'];
        // print_r($product);
    } else {
        $price = $variant['variant']['price'];
    }
    $api = new stdClass;
    @$api->data->price = $price;
    @$api->data->quantity = $qty;
    echo json_encode($api, JSON_PRETTY_PRINT);
    // echo $api;
    // print_r($variant);
} else {
    echo "store not exists";
}
// echo '{
//     "data": {
//         "price": 500
//     }
// }';
