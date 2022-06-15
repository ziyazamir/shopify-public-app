<?php
$index = "set";
include_once "../inc/dbconnect.php";
include_once "../inc/functions.php";
$shop = $_GET['shop'];
$plan = $_GET['plan'];
if ($plan == "1") {
    $name = "DECORATOR
    (Merch Products Only)";
    $price = "149";
} else if ($plan == "2") {
    $name = "PRINTER
    (Merch + Printing Products)";
    $price = "299";
}
$sql = "SELECT * FROM main WHERE store='$shop'";
$stmt = $pdo->query($sql);
$value = $stmt->fetch();
$access_token = $value['token'];


if (isset($_GET['charge_id']) && $_GET['charge_id'] != '') {
    $charge_id = $_GET['charge_id'];

    // $array = array(
    //     'recurring_application_charge' => array(
    //         "id" => $charge_id,
    //         "name" => $name,
    //         "api_client_id" => rand(1000000, 9999999),
    //         "price" => $price,
    //         "status" => "accepted",
    //         "return_url" => "https://" . $shop . "/admin/apps/designo-1",
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
    $plan =  $activate['recurring_application_charge']['name'];
    $status = $activate['recurring_application_charge']['status'];
    print_r($activate);
    // echo $plan;

    $query = "SELECT COUNT(*) FROM subscriptions WHERE store_name='$shop'";
    $stmt = $pdo->query($query);
    $s = $stmt->fetchColumn();
    if ($s == 1) {
        $update = "UPDATE subscriptions SET `charge_id`='$charge_id',`plan`= '$plan', `plan_status`='$status' WHERE store_name='$shop'";
        $stmt = $pdo->prepare($update);
        $val = $stmt->execute();
        if ($val = $stmt->execute()) {
            // echo "<script> alert('updated succesfully');</script>";
            header("location:" . "https://" . $shop . "/admin/apps/designo-1");
        }
        // print_r($c_id);
        // echo
    } else {
        // try{
        $date = time();
        $plan_query = "INSERT INTO subscriptions (store_name,charge_id,plan,plan_status,install_date) VALUES('$shop','$charge_id','$plan','$status','$date')";
        $plan_stmt = $pdo->prepare($plan_query);
        $res = $plan_stmt->execute();
        if ($res) {
            echo "<script> alert('added succesfully');</script>";
            header("location:" . "https://" . $shop . "/admin/apps/designo-1");
        } else {
            echo "<script> alert('something went wrong');</script>";
        }
        // } catch(){

        // }
    }
}
// try {
//     $statements =
//         'CREATE TABLE subscriptions( 
//         id   INT AUTO_INCREMENT,
//         store_name  VARCHAR(100) NOT NULL, 
//         plan VARCHAR(50) NULL, 
//         plan_status   VARCHAR(100) NULL,
//         PRIMARY KEY(author_id)
//     );';

//     $pdo->exec($statements);
//     echo "users table created successfully";
// } catch (PDOException $e) {
//     echo $sql . "<br>" . $e->getMessage();
// }
// $sql = 'SHOW TABLES';
// $stmt = $pdo->query($sql);
// $value = $stmt->fetchAll();
// print_r($value);
