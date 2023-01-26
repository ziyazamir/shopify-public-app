<?php
include_once "../inc/dbconnect.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    define('API_SECRET_KEY', ' ');
    function verify_webhook($data, $hmac_header)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, API_SECRET_KEY, true));
        return hash_equals($hmac_header, $calculated_hmac);
    }
    $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
    $data = file_get_contents('php://input');
    $verified = verify_webhook($data, $hmac_header);
    error_log('Webhook verified: ' . var_export($verified, true)); // Check error.log to see the result
    if ($verified) {
        $store_code = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
        $query = "SELECT * FROM users WHERE store='$store_code'";
        $stmt = $pdo->query($query);
        $n = $stmt->fetch();
        $global_link =  $n['link'];
        // $data = file_get_contents('php://input');
        $myfile = fopen("get_updated_pr.json", "w") or die("Unable to open file!");
        fwrite($myfile, $data);
        fclose($myfile);

        $products_list = json_decode($data);
        echo $products_list->title;
        echo $products_list->id;
    } else {
        http_response_code(401);
    }
}
