<?php
if (!isset($index)) {
    header('Access-Control-Allow-Origin: *');

    header('Access-Control-Allow-Headers: *');

    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');



    if (isset($draft)) {
        header('Access-Control-Allow-Headers: Content-Type,x-prototype-version,x-requested-with');
    } else {
        header('Access-Control-Allow-Headers: Content-Type,x-prototype-version,x-requested-with');
        header('Content-Type: application/json');
    }
}
// echo "hello";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $json = file_get_contents('php://input');
    $json = json_decode($json);
    print_r($json);
}
