<?php
include_once "../inc/dbconnect.php";
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type,x-prototype-version,x-requested-with');
// header("Content-Security-Policy: connect-src 'self';");
//  echo "hello";
$shop = $_POST['store'];
$query = "SELECT COUNT(*) FROM users WHERE store='$shop'";
$stmt = $pdo->query($query);
$n = $stmt->fetchColumn();
if ($n > 0) {
    $query = "SELECT * FROM users WHERE store='$shop'";
    $stmt = $pdo->query($query);
    $n = $stmt->fetch();
    echo $n['selected'] . "&" . $n['link'];
} else {
    echo "no";
}
