<?php
// header("X-Frame-Options: SAMEORIGIN");
$index = "set";
include "inc/dbconnect.php";
include_once "inc/functions.php";
$shop = $_GET['shop'];

// header("content-type:application/json");
$ses =  $_GET['session'];
if (empty($ses)) {
    $shop = $_GET['shop'];
    //echo $shop;

    $api_key = "badf774df40ff216b72ad3461dd7ba59";
    $scopes = "write_content,read_orders,read_themes,write_themes,write_script_tags,read_customers,write_customers,write_draft_orders,read_products";
    $redirect_uri = "https://shoppyapp.designo.software/v1/generate_token.php";

    // Build install/approval URL to redirect to
    $install_url = "https://" . $shop . "/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);
    header("Location: " . $install_url);
    die();
}
header('Content-Security-Policy: frame-ancestors https://' . $shop . ' https://admin.shopify.com;');
$sql = "SELECT * FROM main WHERE store='$shop'";
$stmt = $pdo->query($sql);
$value = $stmt->fetch();
$access_token = $value['token'];
// echo $access_token;

// checking subscription plan 
$query = "SELECT COUNT(*) FROM subscriptions WHERE store_name='$shop'";
$stmt = $pdo->query($query);
$s = $stmt->fetchColumn();
// echo $s;
if ($s == 1) {
    $query = "SELECT * FROM subscriptions WHERE store_name='$shop'";
    $stmt = $pdo->query($query);
    $shop_data = $stmt->fetch();
    $c_id = $shop_data['charge_id'];
    // echo $c_id;
    // exit();

    $get_status = shopify_call($access_token, $shop, "/admin/api/2022-01/recurring_application_charges/$c_id.json", array(), 'GET');
    $get_status = json_decode($get_status['response'], JSON_PRETTY_PRINT);
    // print_r($get_status);
    // exit();
    $plan_status = $get_status['recurring_application_charge']['status'];
    if ($plan_status != "active") {
        header("location:pricing.php?shop=$shop");
    }
    // print_r($c_id);
    // echo
} else {
    header("location:pricing.php?shop=$shop");
}

$query = "SELECT COUNT(*) FROM users WHERE store='$shop'";
$stmt = $pdo->query($query);
$n = $stmt->fetchColumn();
// print_r($val);
// echo "<script> alert($n);</script>";
// echo $val;
if ($n == 1) {
    $query = "SELECT * FROM users WHERE store='$shop'";
    $stmt = $pdo->query($query);
    $val = $stmt->fetch();
    // $n = 1;
    // echo "<script> alert($n);</script>";
    $designo_link = $val['link'];
    // echo $val['selected'];
    if ($val['selected'] == "yes") {
        $yes = "selected";
    } else {
        $no = "selected";
        $disabled = "disabled";
    }
    $btn = " btn-primary";
} else {
    $n = 0;
    $disabled = "disabled";
    $btn = "disabled btn-secondary";
    // echo "<script> alert('store is not present');</script>";
}
?>
<!DOCTYPE html>

<html lang="en">



<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Document</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script>
        function url_show() {
            $("#url-alert").show();
        }
    </script>

</head>



<body>

    <div class="container-fluid row justify-content-center align-items-center">
        <div class="col-12 mt-2">
            <a href="<?php echo $designo_link ?>" target="_blank" type="button" class="btn <?php echo $btn ?> float-start <?php if ($designo_link == '') {echo 'disabled';} ?>">Go to DesignO Admin</a>
            <a href="pricing.php?shop=<?php echo $shop ?>" type="button" class="btn btn-primary float-start ms-1">Plan & Pricing</a>

            <form action="" method="post" style="float: right;">
                <a href="setup.php" class="btn btn-primary me-1">DesignO Shopify App Guide</a>
                <button type="submit" name="install_theme" class="btn btn-primary">Integration</button>
            </form>
        </div>
        <div class="col-6">
            <div class="col-md-12 text-center mb-3 mt-2"><img src="https://shoppyapp.designo.software/v1/inc/logo-design.JPG" style="max-width:100%;" /></div>
            <div class="col-md-12 text-center mb-3">
                <p>Ask for 14 days <a href="https://www.designnbuy.com/freetrial.html" target="_blank">Free Trial</a> if you don't have DESIGNO access</p>
            </div>
            <form method="POST" class="mb-5">
                <div class="mb-3">

                    <label class="form-label">Enable DESIGNO</label>

                    <select name="options" class="form-select" required>
                        <option <?php echo $no ?> value="no">no</option>
                        <option <?php echo $yes ?> value="yes">yes</option>
                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">DESIGNO URL</label>

                    <input name="link" type="url" class="form-control" value="<?php echo $designo_link ?>" required>

                </div>
                <div id="url-alert" class="alert alert-danger" style="display: none;" role="alert">
                    Provided URL is not correct.
                </div>
                <div class="mb-3">

                    <p style="color:#aaaaaa">Enter only your DesignO software URL which you must have got from the Design’N’Buy team</p>

                </div>

                <button type="submit" name="submit" class="btn btn-primary">Submit</button>

            </form>

        </div>

    </div>

    <?php
    // echo $n;
    if (isset($_POST["install_theme"])) {
        include_once "inc/theme.php";
    }

    if (isset($_POST["submit"])) {
        $selected = $_POST["options"];
        if ($selected == "yes") {
            echo "<script> alert('App is Enabled');</script>";
        } else {
            echo "<script> alert('App is Disabled');</script>";
        }
        $url = $_POST["link"];
        $slash = substr($url, -1);
        if ($slash != "/") {
            $url .= "/";
            // echo "<script> alert('$url');</script>";
        }
        if (strpos($url, 'designo.software')) {
            if ($n == 1) {
                $update = "UPDATE users SET `selected`= '$selected', `link`='$url' WHERE store='$shop'";
                $stmt = $pdo->prepare($update);
                $val = $stmt->execute();
                if ($val = $stmt->execute()) {
                    // echo "<script> alert('updated succesfully');</script>";
                } else {
                    echo "<script> alert('something went wrong in updation');</script>";
                }
                header("Refresh:0");
            } else if ($n == 0) {
                $query = "INSERT INTO users (selected,link,store) VALUES('$selected','$url','$shop')";

                $stmt = $pdo->prepare($query);

                if ($res = $stmt->execute()) {

                    echo "<script> alert('added succesfully');</script>";
                } else {
                    echo "<script> alert('something went wrong');</script>";
                }
                header("Refresh:0");
            }
        } else {
            // echo "<script> alert('invalid url') </script>";
            echo "<script> url_show() </script>";
        }



        // $shop = $_POST['store_name'];
        // echo "<script> alert('$selected<br>$url');</script>";


    }

    ?>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<!-- <script>
    const str = window.location.href;
    let myarr = str.split("/");
    console.log(myarr);
    let arr2 = myarr[4].split("=");
    console.log(arr2);
    let arr3 = arr2[5].split("&");
    console.log(arr3);

    let shop_name = arr3[0];
    console.log(shop_name);
    document.getElementById("store_input").value = shop_name;
</script> -->


</html>