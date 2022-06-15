<?php
include_once "../inc/dbconnect.php";
include_once "../inc/functions.php";

$shop = $_GET['shop'];
$id = $_GET['id'];

$sql = "SELECT * FROM main WHERE store='$shop' ORDER BY id DESC LIMIT 1";
$stmt = $pdo->query($sql);
// $value = $stmt->fetchAll();
if ($value = $stmt->fetch()) {
    // echo "data selected<br>";
    $token = $value['token'];
    // echo $token;

    $products_list = shopify_call($token, $shop, "/admin/api/2021-10/products/$id.json", array(), 'GET');
    $products_list = json_decode($products_list['response'], true);
    // print_r($products_list);
    if (isset($products_list['errors'])) {
        $error = new stdClass;
        @$error->data->success = "false";
        @$error->error->message = "Product does not exist.";
        echo json_encode($error, JSON_PRETTY_PRINT);
    } else {

        // print_r($products_list);

        //designo objects
        $item_object = new stdClass;
        $api = new stdClass;
        $config = new stdClass;
        $allvariants = new stdClass;

        $item_object->name = $products_list['product']['title'];
        $item_object->sku = $products_list['product']['id'];
        $item_object->color = "NULL";
        $item_object->size = "NULL";
        $item_object->configurable_options = array();
        $item_object->variants = array();

        @$api->data->products->total_count = "1";
        @$api->data->products->items = array();

        // configurable options
        foreach ((array)$products_list['product']['options'] as $option) {
            $items = new stdClass;
            // echo $variants['title'];
            // echo $variants['sku'];
            $items->attribute_id = $option['id'];
            $items->attribute_code = $option['name'];
            array_push($item_object->configurable_options, $items);
        }

        // variants 
        foreach ((array)$products_list['product']['variants'] as $variants) {
            $variant = new stdClass;
            $variant->attributes = array();
            // options array for each attribute

            $opt_no = 1;
            foreach ((array)$products_list['product']['options'] as $option) {
                $options = new stdClass;

                $opt = "option" . "$opt_no";
                // echo $variants['title'];
                // echo $variants['sku'];
                $options->label = $variants[$opt];
                $options->code = $option['name'];
                $options->value_index = $variants['id'];
                array_push($variant->attributes, $options);
                // unset($items);
                $opt_no = $opt_no + 1;
            }
            array_push($item_object->variants, $variant);
            // echo json_encode($variant, JSON_PRETTY_PRINT);
        }

        array_push($api->data->products->items, $item_object);

        // description
        @$item_object->short_description->html = $products_list['product']['body_html'];
        // image
        @$item_object->image->url = $products_list['product']['image']['src'];
        // page info
        @$api->data->products->page_info->page_size = 20;
        @$api->data->products->page_info->current_page = 1;



        $ans =  json_encode($api, JSON_PRETTY_PRINT);
        // Display the api
        echo $ans;
        //   
    }
} else {
    $error = new stdClass;
    @$error->data->success = "false";
    @$error->error->message = "Store does not exist.";
    echo json_encode($error, JSON_PRETTY_PRINT);
}
// echo $sql;
