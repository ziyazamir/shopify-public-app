<?php
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
        $item_object = new stdClass;
        $api = new stdClass;
        $config = new stdClass;
        $allvariants = new stdClass;

        $item_object->name = $products_list->title;
        $item_object->sku = $products_list->id;
        // $item_object->store_code = "mystore12346.myshopify.com";
        $item_object->categories = [];
        $item_object->color = "NULL";
        $item_object->size = "NULL";

        $item_object->configurable_options = array();
        $item_object->variants = array();

        @$api->data->products->total_count = "1";
        @$api->data->products->items = array();
        // configurable options
        foreach ($products_list->options as $option) {
            $items = new stdClass;
            // echo $variants->title;
            // echo $variants->sku;
            $items->attribute_id = $option->id;
            $items->attribute_code = $option->name;
            array_push($item_object->configurable_options, $items);
        }
        // variants 
        $temp  = $products_list->variants;
        // print_r($temp);
        foreach ($products_list->variants as $variants) {
            $variant = new stdClass;
            $variant->attributes = array();
            // options array for each attribute
            $opt_no = 1;
            foreach ($products_list->options as $option) {
                $options = new stdClass;
                $var_option = "option$opt_no";
                $opt = "option" . "$opt_no";
                // echo $variants->title;
                // echo $variants->sku;
                $options->label = $variants->$var_option;
                $options->code = $option->name;
                $options->value_index = $variants->id;
                array_push($variant->attributes, $options);
                // unset($items);
                $opt_no = $opt_no + 1;
            }
            array_push($item_object->variants, $variant);
            // echo json_encode($variant, JSON_PRETTY_PRINT);
        }
        array_push($api->data->products->items, $item_object);
        // description
        @$item_object->short_description->html = $products_list->body_html;
        // image
        @$item_object->image->url = $products_list->image->src;
        // page info
        @$api->data->products->page_info->page_size = 20;
        @$api->data->products->page_info->current_page = 1;
        $ans =  json_encode($api, JSON_PRETTY_PRINT);
        // Display the api
        $myfile = fopen("sentforupdate.json", "w") or die("Unable to open file!");
        fwrite($myfile, $ans);
        fclose($myfile);
        //echo $ans;
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
            //    echo"<pre>"; print_r(json_decode($ans,true)['data']); exit;
            // Prepare new cURL resource
            $urll = $global_link . 'api/update-product';
            $url = str_replace(" ", '%20', $urll);
            $post = [
                'store_code' => $store_code,
                'SKU' => $products_list->id,
                'params' => json_encode(json_decode($ans, true))
            ];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            // curl_setopt(
            //     $ch,
            //     CURLOPT_HTTPHEADER,
            //     array(
            //         'Content-Type: application/json',
            //         'Authorization:' . $token
            //     )
            // );
            $result = curl_exec($ch);
            $myfile = fopen("pr_result.json", "w") or die("Unable to open file!");
            fwrite($myfile, json_encode($result));
            // $txt = "Jane Doe\n";
            // fwrite($myfile, $txt);
            fclose($myfile);
            curl_close($ch);
            var_dump($result);
            exit;
            // handle curl error
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
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
            curl_close($ch);
        }
    } else {
        http_response_code(401);
    }
}
