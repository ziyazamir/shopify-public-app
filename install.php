<?php
//header("X-Frame-Options: SAMEORIGIN");


// Set variables for our request
$shop = $_GET['shop'];
//echo $shop;

$api_key = "badf774df40ff216b72ad3461dd7ba59";
$scopes = "write_content,read_orders,read_themes,write_themes,write_script_tags,read_customers,write_customers,write_draft_orders,read_products";
$redirect_uri = "https://shoppyapp.designo.software/v1/generate_token.php";

// Build install/approval URL to redirect to
$install_url = "https://" . $shop . "/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);
header("Location: ".$install_url);
 die();
