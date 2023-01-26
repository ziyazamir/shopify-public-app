<?php
//header("X-Frame-Options: SAMEORIGIN");


// Set variables for our request
$shop = $_GET['shop'];
//echo $shop;

$api_key = "API KEY";
$scopes = "write_content,read_orders,read_themes,write_themes,write_script_tags,read_customers,write_customers,write_draft_orders,read_products";
$redirect_uri = "your_domain/generate_token.php";

// Build install/approval URL to redirect to
$install_url = "https://" . $shop . "/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);
header("Location: " . $install_url);
die();
