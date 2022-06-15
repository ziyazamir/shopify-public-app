<?php
  header('Access-Control-Allow-Origin: *');

  header('Access-Control-Allow-Headers: *');

    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

        header('Access-Control-Allow-Headers: Content-Type,x-prototype-version,x-requested-with');
        header('Content-Type: application/json');

include_once "../inc/dbconnect.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    define('API_SECRET_KEY', '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');
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

    $json = file_get_contents('php://input');
    $json = json_decode($json);
     print_r($json);
 }
else {
        http_response_code(401);
    }
}
