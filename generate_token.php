<?php
//header("X-Frame-Options: SAMEORIGIN");
include_once("inc/dbconnect.php");
require_once("inc/functions.php");

// Set variables for our request
$api_key = "API KEY";
$shared_secret = " "; //put your own shared_secret
$params = $_GET; // Retrieve all request parameters
$hmac = $_GET['hmac']; // Retrieve HMAC request parameter

$params = array_diff_key($params, array('hmac' => '')); // Remove hmac from params
ksort($params); // Sort params lexographically

$computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);
$rootURL = 'https://yourDomain.com/';
// Use hmac data to check that the response is from Shopify or not
if (hash_equals($hmac, $computed_hmac)) {

    // Set variables for our request
    $query = array(
        "client_id" => $api_key, // Your API key
        "client_secret" => $shared_secret, // Your app credentials (secret key)
        "code" => $params['code'] // Grab the access key from the URL
    );

    // Generate access token URL
    $access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";

    // Configure curl client and execute request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $access_token_url);
    curl_setopt($ch, CURLOPT_POST, count($query));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
    $result = curl_exec($ch);
    curl_close($ch);

    // Store the access token
    $result = json_decode($result, true);
    $access_token = $result['access_token'];
    $shop = $params['shop'];

    // Show the access token (don't do this in production!)
    // 	echo $access_token;

    // **********checking and deleting script tag***************
    // Although the script tag get deleted after sometimes of uninstalling the app but sometimes it is good to check else your script will run twice
    $get_script = shopify_call($access_token, $shop, "/admin/api/2021-10/script_tags.json", array(), 'GET');
    $get_script = json_decode($get_script['response'], true);
    print_r($get_script);
    foreach ($get_script['script_tags'] as $single) {
        $src = $single['src'];
        $id = $single['id'];
        if (strpos($src, "script.js")) {
            echo "true";
            $delete_script = shopify_call($access_token, $shop, "/admin/api/2022-01/script_tags/$id.json", array(), 'DELETE');
            $delete_script = json_decode($delete_script['response'], true);
            // print_r($delete_script);
        }
    }


    //***********adding script tag*************
    $script_array = array(

        'script_tag' => array(

            'event' => 'onload',

            'src' => $rootURL . 'inc/script.js'

        )

    );

    $script = shopify_call($access_token, $shop, "/admin/api/2021-10/script_tags.json", $script_array, 'POST');
    $script = json_decode($script['response'], true);

    // **************creating webhook******************
    // This webhook will run if a product is updated by the owner
    $params = [
        'webhook' => [
            'topic' => 'products/update',
            'address' => $rootURL . 'api/update_product.php', //url where shopify send response of updated product 
            'format' => 'json'
        ]
    ];

    $json_string_params = json_encode($params);

    $webhook = shopify_call($access_token, $shop, "/admin/api/2022-01/webhooks.json", $json_string_params, 'POST', array("Content-Type: application/json"));

    $webhook = json_decode($webhook['response'], JSON_PRETTY_PRINT);
    // print_r($webhook);

    //*******************order created webhook******************
    // This webhook will trigger if there is any order created on store by any user
    $order_params = [
        'webhook' => [
            'topic' => 'orders/create',
            'address' => $rootURL . 'api/add_order.php', //url where shopify send response of order 
            'format' => 'json'
        ]
    ];

    $json_string_params = json_encode($order_params);

    $order_webhook = shopify_call($access_token, $shop, "/admin/api/2022-01/webhooks.json", $json_string_params, 'POST', array("Content-Type: application/json"));

    $order_webhook = json_decode($order_webhook['response'], JSON_PRETTY_PRINT);


    //************order updated webhook************
    // If any order is updated by any means then it will trigger the webhook
    $updated_order = [
        'webhook' => [
            'topic' => 'orders/updated',
            'address' => $rootURL . 'api/update_order.php',
            'format' => 'json'
        ]
    ];

    $json_string = json_encode($updated_order);

    $update_order_webhook = shopify_call($access_token, $shop, "/admin/api/2022-01/webhooks.json", $json_string, 'POST', array("Content-Type: application/json"));

    $update_order_webhook = json_decode($update_order_webhook['response'], JSON_PRETTY_PRINT);
    // print_r($update_order_webhook);


    // ##########creating themes layouts and templates#############
    include_once "inc/theme.php";


    // inserting data into data base
    //save store access token and name in database
    $query = "SELECT COUNT(*) FROM main WHERE store='$shop'";
    $stmt = $pdo->query($query);
    $n = $stmt->fetchColumn();
    if ($n > 0) { //check if store is re-installing the app
        $update_token = "UPDATE main SET `token`= '$access_token' ,`install_date`=NOW() WHERE store='$shop'";
        $token_stmt = $pdo->prepare($update_token);
        $val1 = $token_stmt->execute();

        $update_user = "UPDATE users SET `selected`= '', `link`='' WHERE store='$shop'";
        $user_stmt = $pdo->prepare($update_user);

        if ($val2 = $user_stmt->execute()) {
            header("location:https://" . $shop . "/admin/apps/*YOUR APP CODE*"); //change app name found on index page url of ur app
        } else {
            echo "<script> alert('something went wrong in updation');</script>";
        }
    } else {
        $shop_query = "INSERT INTO main (store,token,install_date) VALUES('$shop','$access_token',NOW())";
        $shop_stmt = $pdo->prepare($shop_query);

        if ($res1 = $shop_stmt->execute()) {
            echo "<script> alert('inserted succesfully');</script>";
            header("location:https://" . $shop . "/admin/apps/*YOUR APP CODE*");
        } else {
            echo "error installation";
        };
    }
} else {
    // Someone is trying to be shady!
    die('This request is NOT from Shopify!');
}

die();
