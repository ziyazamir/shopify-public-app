<?php
$index = "set";
include_once "../inc/dbconnect.php";
include_once "../inc/functions.php";
include_once "../inc/gql_functions.php";
$shop = $_GET['shop'];
$plan = $_GET['plan'];
$duration = $_GET['duration'];
//echo $duration;
$url_data = $_SERVER["QUERY_STRING"];
$sql = "SELECT * FROM main WHERE store='$shop'";
$stmt = $pdo->query($sql);
$value = $stmt->fetch();
$access_token = $value['token'];

$query = "SELECT COUNT(*) FROM subscriptions WHERE store_name='$shop'";
$stmt = $pdo->query($query);
$s = $stmt->fetchColumn();
if ($s == 1) {
    $query = "SELECT * FROM subscriptions WHERE store_name='$shop'";
    $stmt = $pdo->query($query);
    $data = $stmt->fetch();
    $start = $data['install_date'];
    $now = time();
    // $now = strtotime('2022-03-20');
    $days_between = ceil(abs($now - $start) / 86400);
    if ($days_between <= "14") {
        $trial_days = 15 - $days_between;
    } else {
        $trial_days = 0;
    }
    // print_r($c_id);
    // echo
} else {
    // try{
    $trial_days = 14;
    // } catch(){

    // }
}
echo $trial_days;


if ($duration == "monthly") {
    if ($plan == "1") {

        $array = array(
            'recurring_application_charge' => array(
                'name' => 'DECORATOR
                (Merch Products Only)
                ',
                // 'test' => true,  //remove this line before sending to app store
                'price' => 149.0,
                'trial_days' => $trial_days,
                'return_url' => 'https://shoppyapp.designo.software/v1/api/verify_payment.php?' . $_SERVER['QUERY_STRING']
            )
        );
    } else if ($plan == "2") {
        $array = array(
            'recurring_application_charge' => array(
                'name' => 'PRINTER
                (Merch + Printing Products)',
                // 'test' => true,  //remove this line before sending to app store
                'price' => 299.0,
                //https://shoppyapp.designo.software/v1/api/billing.php?shop=designodemo.myshopify.com&plan=1
                'trial_days' => $trial_days,
                'return_url' => 'https://shoppyapp.designo.software/v1/api/verify_payment.php?' . $_SERVER['QUERY_STRING']
            )
        );
    }
    $charge = shopify_call($access_token, $shop, "/admin/api/2022-01/recurring_application_charges.json", $array, 'POST');
    $charge = json_decode($charge['response'], JSON_PRETTY_PRINT);
    $redir_url = $charge['recurring_application_charge']['confirmation_url'];
} else if ($duration == "yearly") {
    if ($plan == "1") {

        $query = array(
            "query" => '
        mutation {
            appSubscriptionCreate(
                name: "DECORATOR
                (Merch Products Only)"
                trialDays: ' . $trial_days . '
                returnUrl: "https://shoppyapp.designo.software/v1/api/verify_payment.php?' . $url_data . '"
                lineItems: [
                {
                    plan: {
                        appRecurringPricingDetails: {
                            price: { amount: 1548.00, currencyCode: USD }
                            interval: ANNUAL
                        }
                    }
                }
                ]
            ) {
                appSubscription {
                    id
                }
                confirmationUrl
                userErrors {
                    field
                    message
                }
            }
        }'
        );
        // echo "wprking";
        // $charge = graphql("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", "designodemo.myshopify.com", $query);
        // // $charge = shopify_call($access_token, $shop, "/admin/api/2022-01/recurring_application_charges.json", $array, 'POST');
        // $charge = json_decode($charge['response'], JSON_PRETTY_PRINT);
        // // print_r($charge);
        // $redir_url = $charge['data']['appSubscriptionCreate']['confirmationUrl'];
    } else if ($plan == "2") {
        //echo "working";
        $query = array(
            "query" => '
        mutation {
            appSubscriptionCreate(
                name: "PRINTER
                (Merch + Printing Products)"
                trialDays: ' . $trial_days . '
                returnUrl: "https://shoppyapp.designo.software/v1/api/verify_payment.php?' . $url_data . '"
                lineItems: [
                {
                    plan: {
                        appRecurringPricingDetails: {
                            price: { amount: 2988.00, currencyCode: USD }
                            interval: ANNUAL
                        }
                    }
                }
                ]
            ) {
                appSubscription {
                    id
                }
                confirmationUrl
                userErrors {
                    field
                    message
                }
            }
        }'
        );
    }
    $charge = graphql($access_token, $shop, $query);
    $charge = json_decode($charge['response'], JSON_PRETTY_PRINT);
    // print_r($charge);
    // print_r($charge['data']['appSubscriptionCreate']['confirmationUrl']);
    $redir_url = $charge['data']['appSubscriptionCreate']['confirmationUrl'];
}
// $query = array("query" => ' 

// mutation {
//     appSubscriptionCreate(
//         name: "Product Photos PRO"
//         returnUrl: "https://www.shopify.com"
//         lineItems: [
//         {
//             plan: {
//                 appRecurringPricingDetails: {
//                     price: { amount: 10.00, currencyCode: USD }
//                     interval: ANNUAL
//                 }
//             }
//         }
//         ]
//     ) {
//         appSubscription {
//             id
//         }
//         confirmationUrl
//         userErrors {
//             field
//             message
//         }
//     }
// }


// ');
echo "<script> window.parent.location.href = '$redir_url' ;</script>";
// echo $redir_url;
exit();
