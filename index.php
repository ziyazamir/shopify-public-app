<?php
// header("X-Frame-Options: SAMEORIGIN");
$index = "set";
include "inc/dbconnect.php";
include_once "inc/functions.php";
$shop = $_GET['shop'];


// checking if the store has app installed or not 
$ses =  $_GET['session'];
if (empty($ses)) {
    $shop = $_GET['shop'];
    //echo $shop;

    $api_key = " "; //chage it with your own api key
    $scopes = "write_content,read_orders,read_themes,write_themes,write_script_tags,read_customers,write_customers,write_draft_orders,read_products";
    $redirect_uri = "https://YourDomain/generate_token.php";

    // Build install/approval URL to redirect to
    $install_url = "https://" . $shop . "/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);
    header("Location: " . $install_url);
    die();
}


header('Content-Security-Policy: frame-ancestors https://' . $shop . ' https://admin.shopify.com;');

?>
<!DOCTYPE html>

<html lang="en">



<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>First Version of this app</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">



</head>



<body>

    <h1 class="text-center bg-dark">Hello From App</h1>


</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


</html>