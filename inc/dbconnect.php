<?php
$username = "username";

$password = "password";

$host = "localhost";

$dbname = "dbname";

$shop = $_GET['shop'];


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


$meta[1] = "Meta description";

$title[1] = "title";

if ($pdo) {

    //you are connected

} else {

    echo "Sorry , You are not connected";
}
