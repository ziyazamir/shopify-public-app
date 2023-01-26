<?php
include_once "../inc/dbconnect.php";
function get_domain($url)
{
    $charge = explode('/', $url);
    $charge = $charge[2]; //assuming that the url starts with http:// or https://
    return $charge;
}


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
        $myfile = fopen("getorder.json", "w") or die("Unable to open file!");

        fwrite($myfile, $data);

        fclose($myfile);
        $data = json_decode($data);

        print_r($data);
    } else {
        http_response_code(401);
    };
    // $json = file_get_contents('php://input');
} 
    // echo $data->id
// }
