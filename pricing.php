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
        if ($price == "149.00") {
            $p1 = "disabled";
        } else if ($price == "1548.00") {
            $p2 = "disabled";
        } else if ($price == "299.00") {
            $p3 = "disabled";
        } else if ($price == "2988.00") {
            $p4 = "disabled";
        }
    }
} else {
    //no plan is active 
}



// print_r($shop);
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
    <title>Designo Subscription Plan</title>

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
                    <a href="https://<?php echo $shop ?>/admin/apps/designo-1" target="_PARENT" class="btn rounded-circle fs-2"><i class="fa-solid fa-circle-arrow-left"></i></a>
                </div>
                <div class="col-12">
                    <img class="d-block m-auto mb-4" src="inc/logo-design.JPG" alt="">
                </div>
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card h-100 shadow-lg">
                        <div class="card-body">
                            <div class="text-center p-3">
                                <h5 class="card-title">DECORATOR <br><span class="fs-6">(Merch Products Only)</span></h5>
                                <!-- <small>Small Business</small> -->
                                <br><br>
                                <span class="h2">$149</span>/Monthly<br><br>
                                <span class="h2 text-success"> $1548</span>/Yearly
                                <br><br>
                            </div>
                            <p class="card-text">This plan includes design studio support for selling personalized apparel and merchandise i.e t-shirts, caps, mugs, and much more. </p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> 14 Days Free Trial</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> One-time Setup Fee 1000 USD</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> Pre-loaded clipart and font library</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> Preloaded Design Templates</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> 20 GB Storage</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> Help Desk System</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> Personalized Live Training</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> User Manuals and Support Videos</li>
                        </ul>
                        <div class="card-body text-center">
                            <a class="btn w-75 btn-outline-primary btn-padding <?php echo $p1 ?>" href="api/billing.php?shop=<?php echo $shop; ?>&plan=1&duration=monthly">Select Monthly</a>
                        </div>
                        <div class="card-body text-center">
                            <a class="btn w-75 btn-outline-primary btn-padding <?php echo $p2 ?>" href="api/billing.php?shop=<?php echo $shop; ?>&plan=1&duration=yearly">Select Yearly</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card h-100 shadow-lg">
                        <div class="card-body">
                            <div class="text-center p-3">
                                <h5 class="card-title">PRINTER <br><span class="fs-6">(Merch + Printing Products)</span></h5>
                                <!-- <small>Small Business</small> -->
                                <br><br>
                                <span class="h2">$299</span>/Monthly<br><br>
                                <span class="h2 text-success">$2988</span>/Yearly
                                <br><br>
                            </div>
                            <p class="card-text">This plan is for selling print products like banners, signs, business cards, photo book, VDP & many more in addition to merchandise products</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> 14 Days Free Trial</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> One-time Setup Fee 1000 USD</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> Pre-loaded clipart and font library</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> Preloaded Design Templates</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> 20 GB Storage</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> Help Desk System</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> Personalized Live Training</li>
                            <li class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                </svg> User Manuals and Support Videos</li>
                        </ul>
                        <div class="card-body text-center">
                            <a class="btn w-75 btn-outline-primary btn-padding <?php echo $p3 ?>" href="api/billing.php?shop=<?php echo $shop; ?>&plan=2&duration=monthly">Select Monthly</a>
                        </div>
                        <div class="card-body text-center">
                            <a class="btn w-75 btn-outline-primary btn-padding <?php echo $p4 ?>" href="api/billing.php?shop=<?php echo $shop; ?>&plan=2&duration=yearly">Select Yearly</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>


<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>


</body>

</html>