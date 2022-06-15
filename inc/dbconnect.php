<?php
$username = "shopifyapp";

$password = "YSGY54XOR21adZi";

$host = "localhost";

$dbname = "shopifyapp";

$shop = $_GET['shop'];
//echo $shop;



//$global_link = "shopify.designo.software";



$dsn = "mysql:host=$host;dbname=$dbname";

$pdo = new PDO($dsn, $username, $password);


try {
    $pdo = new PDO($dsn, $username, $password);

    if ($pdo) {
        //echo "Connected to the $db database successfully!";
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}

function test_input($data)
{

    $data = trim($data);

    $data = stripslashes($data);

    $data = htmlspecialchars($data);

    return $data;
}

$meta[1] = "This Website is an ultimate source of DESIGNNBUY INC and start-ups around the world. It covers all upcoming technology and start-ups in the world.";

$title[1] = "DESIGNNBUY INC";

if ($pdo) {


    // $query = "SELECT * FROM users WHERE store='$shop'";
    // $stmt = $pdo->query($query);
    // $n = $stmt->fetch();
    // $global_link =  $n['link'];
    // echo $global_link;

} else {

    echo "Sorry , You are not connected";
}
