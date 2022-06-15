<?php
include_once "../inc/functions.php";
include_once "../inc/dbconnect.php";
header('Content-type: image/png');
$url = $_GET['url'];
$shop = $_GET['shop'];
$query = "SELECT * FROM users WHERE store='$shop'";
$stmt = $pdo->query($query);
$n = $stmt->fetch();
$global_link =  $n['link'];
function file_get_contents_curl($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

$data = file_get_contents_curl(
    $global_link . 'images/cart/' . $url
);
echo $data;
