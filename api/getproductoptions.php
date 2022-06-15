<?php

//include_once "../inc/dbconnect.php";

//include_once "../inc/functions.php";

//$api = new stdClass;

//$api->array = array();
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Headers: *');
   header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
   header('Access-Control-Allow-Headers: Content-Type,x-prototype-version,x-requested-with');
   header('Content-Type: application/json');

$qtyData = array(
                    "id" => 'quantityBox',
                    "type" => 'text',
                    "title" => 'Quantity',
                    "label" => 'QuantityBox',
                    "is_require" => '',
                    "sort_order" => '',
                    "value" => 1,
                );
              //  array_push($options, $qtyData);
$options['data']['dnbProductOptions']['options'][0] = $qtyData;

 echo json_encode($options); exit;

//echo json_encode($api, JSON_PRETTY_PRINT);



?>