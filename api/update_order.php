<?php
include_once "../inc/dbconnect.php";
function get_domain($url)
{
    $charge = explode('/', $url);
    $charge = $charge[2]; //assuming that the url starts with http:// or https://
    return $charge;
}


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
        $myfile = fopen("getorder.json", "w") or die("Unable to open file!");

        fwrite($myfile, $data);

        fclose($myfile);
        $data = json_decode($data);
        $obj = new stdClass;
        $obj->data->store_code = get_domain($data->referring_site);
        $obj->data->order_id = $data->id;
        $obj->data->order_status = $data->financial_status;


        $response = json_encode($obj, JSON_PRETTY_PRINT);

        // echo $response;

        $myfile = fopen("myorder.json", "w") or die("Unable to open file!");

        fwrite($myfile, json_encode($obj, JSON_PRETTY_PRINT));

        // $txt = "Jane Doe\n";

        // fwrite($myfile, $txt);

        fclose($myfile);

        $query = "SELECT * FROM users WHERE store='$shop_name'";
        $stmt = $pdo->query($query);
        $n = $stmt->fetch();
        $global_link =  $n['link'];
        // echo $global_link;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $global_link . "api/studio/ecomm-token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST"
            // CURLOPT_HTTPHEADER => array(
            //     "cache-control: no-cache",
            //     "postman-token: 9f12df12-f4cc-6b03-d03c-fa4a236f3acb"
            // ),
        ));

        $token_response = curl_exec($curl);
        $token_response = json_decode($token_response);

        print_r($token_response);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $token =  $token_response->token;
            $post_data = json_encode($rawdata, true);
            //  print_r($post_data);

            // Prepare new cURL resource
            $crl = curl_init($global_link . 'api/studio/update-order');
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($crl, CURLINFO_HEADER_OUT, true);
            curl_setopt($crl, CURLOPT_POST, true);
            curl_setopt($crl, CURLOPT_POSTFIELDS, $response);

            // Set HTTP Header for POST request 
            curl_setopt(
                $crl,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json',
                    // 'Content-Length: ' . strlen($payload),
                    'Authorization:' . $token
                )
            );

            // Submit the POST request
            $result = curl_exec($crl);
            echo $result;
            $myfile = fopen("order_result.json", "w") or die("Unable to open file!");

            fwrite($myfile, $result);

            // $txt = "Jane Doe\n";

            // fwrite($myfile, $txt);

            fclose($myfile);

            // handle curl error
            if (curl_errno($crl)) {
                $error_msg = curl_error($crl);
            }

            if ($result === false) {
                // throw new Exception('Curl error: ' . curl_error($crl));
                //print_r('Curl error: ' . curl_error($crl));
                $result_noti = 0;
                die();
            } else {

                $result_noti = 1;
                die();
            }
            // Close cURL session handle
            curl_close($crl);
        }
    } else {
        http_response_code(401);
    }
    // $json = file_get_contents('php://input');
} 
    // echo $data->id
// }
