<?php

$draft = "set";

include_once "../inc/functions.php";



include_once "../inc/dbconnect.php";

$query1 = new stdClass;
@$query1->draft_order->line_items = array();

$data = $_POST['myData'];
// echo json_encode($data, JSON_PRETTY_PRINT);
$shop = $_POST['shop'];

$sql = "SELECT * FROM main WHERE store='$shop' ORDER BY id DESC LIMIT 1";

$stmt = $pdo->query($sql);
if ($value = $stmt->fetch()) {

    $token = $value['token'];

    $data = json_decode($data);

    foreach ($data as $item) {
        $var = new stdClass;
        $var->variant_id = $item->variant_id;
        $var->quantity = $item->quantity;
        $var->properties = array();
        array_push($query1->draft_order->line_items, $var);
        if (isset($item->properties->CustomPrice)) {
            $order = new stdClass;
            $order->title = "customized " . $item->title;
            $order->price = $item->properties->CustomPrice;
            $order->quantity = $item->quantity;
            // $order->custom = true;
            $order->properties = array();
            //field property
            $draft_property1 = new stdClass;
            $draft_property1->name = "_field";
            $draft_property1->value = $item->properties->_field;
            array_push($order->properties, $draft_property1);
            // customized image
            $draft_property = new stdClass;
            $draft_property->name = "_Customized-Image";
            $str = $item->properties->_png;
            $draft_property->value = $str;
            array_push($order->properties, $draft_property);
            // sku property
            $draft_property2 = new stdClass;
            $draft_property2->name = "_sku";
            $draft_property2->value = $item->properties->_sku;
            array_push($order->properties, $draft_property2);

            //price property
            $draft_property3 = new stdClass;
            $draft_property3->name = "_price";
            $draft_property3->value = ($item->price) / 100;
            array_push($order->properties, $draft_property3);

            // image upload property
            $draft_property4 = new stdClass;
            $draft_property4->name = "Customized-Image";
            $str = $item->properties->upload;
            $draft_property4->value = $str;
            array_push($order->properties, $draft_property4);

            // fixed price property 
            $draft_property5 = new stdClass;
            $draft_property5->name = "Artwork Setup Fees";
            $str = $item->properties->Artwork_Setup_Fees;
            $draft_property5->value = $str;
            array_push($order->properties, $draft_property5);

            // adding fixed price product
            $order2 = new stdClass;
            $order2->title = "Artwork setup fees for " . $item->title;
            $order2->price = $item->properties->Artwork_Setup_Fees;
            $order2->quantity = 1;

            array_push($query1->draft_order->line_items, $order);
            array_push($query1->draft_order->line_items, $order2);
        }
    }


    // echo json_encode($query1, JSON_PRETTY_PRINT);



    $order = shopify_call($token, $shop, "/admin/api/2022-01/draft_orders.json", json_encode($query1), 'POST', array("Content-Type: application/json"));



    $order = json_decode($order['response'], JSON_PRETTY_PRINT);

    // echo json_encode($order, JSON_PRETTY_PRINT);

    // print_r($order);

    //echo json_encode($query1, JSON_PRETTY_PRINT);

    echo $order['draft_order']['invoice_url'];
} else {
    echo "store not exists";
}
