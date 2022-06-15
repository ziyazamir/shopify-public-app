<?php
include_once "../inc/dbconnect.php";
include_once "../inc/functions.php";

$shop = 'appstoretest5.myshopify.com';

$sql = "SELECT * FROM main WHERE store='$shop'";
$stmt = $pdo->query($sql);
print_r($stmt);
// $value = $stmt->fetchAll();
if ($value = $stmt->fetch()) {
    // echo "data selected<br>";
    $token = $value['token'];
    // echo $token;

    $webhook_list = shopify_call($token, $shop, "/admin/api/2022-01/webhooks.json", array(), 'GET');
    //$webhook_list = json_decode($products_list['response'], true);
     print_r($webhook_list);
 }  
// echo $sql;
