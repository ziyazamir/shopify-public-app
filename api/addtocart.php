<?php
include_once "../inc/dbconnect.php";
$draft = "set";
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Headers: *');

header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type,x-prototype-version,x-requested-with');
header('Content-Type: application/json');
header("Content-Security-Policy: connect-src 'self';");



$tooltype = $_REQUEST['toolType'];

$product = $_REQUEST['product'];
$qty = $_REQUEST['qty'];
$totalQuntity = $_REQUEST['totalQty'];
$png = $_REQUEST['totalQty'];
$png = $global_link . 'images/cart/' . $png;
$svg = $_REQUEST['svg'];
$svg = $global_link . 'images/cart/' . $svg;
$super_attribute = $_REQUEST['super_attribute']; //variant id
$price = $_REQUEST['price'];
if ($price == '') {
    $price = 0;
}

$cart_item = new stdClass;
$cart_item->items = array();

$item = new stdClass;
$item->id = $super_attribute;
$item->quantity = $qty;
$item->properties->tooltype = $tooltype;
$item->properties->svg = $svg;
$item->properties->png = $png;
$item->properties->customprice = $price;
array_push($cart_item->items, $item);
$final = json_encode($cart_item);

function success($var)
{
    $obj = new stdClass;
    $obj->data->success = $var;
    $obj->error->message = "You added test canvas to your shopping cart";
    echo json_encode($obj);
}
?>
<script>
    fetch("/cart/add.js", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: <?php echo $final ?>,
        })
        .then((response) => {
            alert("You added test canvas to your shopping cart.");
            <?php echo success("true") ?>
        })
        .catch((error) => {
            alert("Error:", error);
            <?php echo success("false") ?>
        });
</script>
// $formData = {
// items: [
// {
// id: variant_id,
// quantity: formdata.qty,
// properties: {
// tooltype: formdata.toolType,
// svg: svg_image_anchor,
// png: png_image_anchor,
// CustomPrice: "price"
// },
// },
// ],
// };