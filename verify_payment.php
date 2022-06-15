<?php
$index = "set";
//echo "hello";
include_once "../inc/dbconnect.php";
include_once "../inc/functions.php";
$data = $_GET;
//print_r($data);
$shop = $_GET['shop'];
$sql = "SELECT * FROM main WHERE store='$shop'";
$stmt = $pdo->query($sql);
$value = $stmt->fetch();
$access_token = $value['token'];
if (isset($_GET['charge_id']) && $_GET['charge_id'] != '') {
    $charge_id = $_GET['charge_id'];

    // $array = array(
    //     'recurring_application_charge' => array(
    //         "id" => $charge_id,
    //         "name" => "Example Plan",
    //         "api_client_id" => rand(1000000, 9999999),
    //         "price" => "1.00",
    //         "status" => "accepted",
    //         "return_url" => "https://weeklyhow.myshopfy.com/admin/apps/exampleapp-14",
    //         "billing_on" => null,
    //         "test" => true,
    //         "activated_on" => null,
    //         "trial_ends_on" => null,
    //         "cancelled_on" => null,
    //         "trial_days" => 14,
    //         "decorated_return_url" => "https://weeklyhow.myshopfy.com/admin/apps/exampleapp-14/?charge_id=" . $charge_id
    //     )
    // );

    $activate = shopify_call($access_token, $shop, "/admin/api/2019-10/recurring_application_charges/" . $charge_id . "/activate.json", array(), 'POST');
    $activate = json_decode($activate['response'], JSON_PRETTY_PRINT);


    $query = "SELECT COUNT(*) FROM subscriptions WHERE store_name='$shop'";
    $stmt = $pdo->query($query);
    $s = $stmt->fetchColumn();
    if ($s == 1) {
        $query = "SELECT * FROM subscriptions WHERE store_name='$shop'";
        $stmt = $pdo->query($query);
        $shop_data = $stmt->fetch();
        $c_id = $shop_data['charge_id'];
        $get_status = shopify_call($access_token, $shop, "/admin/api/2022-01/recurring_application_charges/$c_id.json", array(), 'GET');
        $get_status = json_decode($get_status['response'], JSON_PRETTY_PRINT);
        $plan_status = $get_status['recurring_application_charge']['status'];
        if ($plan_status != "active") {
            header("location:pricing.php");
        }
        // print_r($c_id);
        // echo
    } else {
        header("location:pricing.php");
    }
    //print_r($activate);
}
