<?php
$shop =  $_GET['shop'];
$index = "set";
include "inc/dbconnect.php";
include_once "inc/functions.php";
$sql = "SELECT * FROM main WHERE store='$shop'";
$stmt = $pdo->query($sql);
$value = $stmt->fetch();
$access_token = $value['token'];

$query = "SELECT COUNT(*) FROM subscriptions WHERE store_name='$shop'";
$stmt = $pdo->query($query);
$s = $stmt->fetchColumn();
if ($s == 1) {
    $query2 = "SELECT * FROM subscriptions WHERE store_name='$shop'";
    $stmt = $pdo->query($query2);
    $s = $stmt->fetch();
    $c_id = $s['charge_id'];
    // print_r($s);

    $get_status = shopify_call($access_token, $shop, "/admin/api/2022-01/recurring_application_charges/$c_id.json", array(), 'GET');
    $get_status = json_decode($get_status['response'], JSON_PRETTY_PRINT);
    // print_r($get_status);
    $price = $get_status['recurring_application_charge']['price'];
    $status = $get_status['recurring_application_charge']['status'];
    // echo $status;
    if ($status == "active") {
        echo "plan is active";
    }
} else {
    echo "no plan is active ";
}

?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>App Subscription Plan</title>

    <style>
        .card {
            border: none;
            padding: 15px;
        }

        .card::after {
            position: absolute;
            z-index: -1;
            opacity: 0;
            -webkit-transition: all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
            transition: all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .card:hover {


            transform: scale(1.02, 1.02);
            -webkit-transform: scale(1.02, 1.02);
            backface-visibility: hidden;
            will-change: transform;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .75) !important;
        }

        .card:hover::after {
            opacity: 1;
        }

        .btn-outline-primary:hover {
            color: white;
            background: #007bff;
        }

        .btn-padding {
            padding: 0.5rem 1rem 0.6rem;
            font-size: 1.25rem;
            border-radius: 0.3rem;
        }
    </style>

</head>

<body>


    <div class="container-fluid">
        <div class="container p-5">
            <div class="row justify-content-center align-items-center">
                <div class="col-12">
                    <a href="https://<?php echo $shop ?>/admin/apps/YOUR APP CODE" target="_PARENT" class="btn rounded-circle fs-2"><i class="fa-solid fa-circle-arrow-left"></i></a>
                </div>

                <div class="col-lg-6 col-md-12 mb-4">
                    <h2>plan1</h2>
                </div>
                <div class="col-lg-6 col-md-12 mb-4">
                    plan2
                </div>
            </div>
        </div>
</body>


<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>


</body>

</html>