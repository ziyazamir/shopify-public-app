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

        $obj =  new stdClass;

        $obj->order_array = array();



        $order = new stdClass;

        @$order->customer_details->name = $data->customer->first_name . $data->customer->last_name;

        $order->customer_details->email = $data->customer->email;
        if ($data->customer->phone == null) {
            $phone = "123456789";
        } else {
            $phone = $data->customer->phone;
        }
        $order->customer_details->phone = $phone;



        @$order->address->shipping_address = $data->shipping_address->address1 . " " . $data->shipping_address->address2;

        $order->address->shipping_country = $data->shipping_address->country;

        $order->address->shipping_state = $data->shipping_address->province;

        $order->address->shipping_city = $data->shipping_address->city;

        $order->address->shipping_zip = $data->shipping_address->zip;

        $order->address->shipping_contact = $data->shipping_address->phone;



        $order->address->billing_address = $data->billing_address->address1 . " " . $data->billing_address->address2;

        $order->address->billing_country = $data->billing_address->country;

        $order->address->billing_state = $data->billing_address->province;

        $order->address->billing_city = $data->billing_address->city;

        $order->address->billing_zip = $data->billing_address->zip;

        $order->address->billing_contact = $data->billing_address->phone;



        @$order->order_details->order_id = $data->id;

        $order->order_details->order_status = $data->financial_status;

        $order->order_details->order_date = $data->updated_at;

        $order->order_details->store_name = $data->line_items[0]->vendor;
        $order->order_details->store_code = get_domain($data->referring_site);
        $shop_name = get_domain($data->referring_site);
        // print_r($data->payment_gateway_names);

        $order->order_details->payment_mode = $data->payment_gateway_names[0];

        $order->order_details->payment_status = "paid";

        $order->order_details->subtotal = $data->current_subtotal_price;

        $order->order_details->shipping_amount = $data->shipping_lines[0]->price;

        $order->order_details->discount_amount = $data->current_total_discounts;

        $order->order_details->grand_total = $data->current_subtotal_price;

        $order->order_details->status = "1";





        $order->order_items = array();



        foreach ($data->line_items as $line_items) {
            if ($line_items->properties[0]->name === "_field") {
                $items = new stdClass;
                $str1 = substr($line_items->name, 11);
                $items->name = $str1;

                $items->thumb_image = $line_items->properties[1]->value;
                $items->info_buyRequest = $line_items->properties[0]->value;
                $items->SKU = $line_items->properties[2]->value;

                $items->qty = $line_items->quantity;

                $items->price = $pr_price = ($line_items->properties[3]->value + $line_items->price + $line_items->properties[5]->value);

                $items->subtotal = $pr_price * $line_items->quantity;

                $items->tax = $line_items->tax_lines->rate;

                $items->tax_amount = $line_items->tax_lines[0]->price;

                $items->discount = $line_items->total_discount;

                $items->total_amount = $pr_price * $line_items->quantity;

                array_push($order->order_items, $items);
            }
        }





        array_push($obj->order_array, $order);



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
            $crl = curl_init($global_link . 'api/studio/add-order');
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
