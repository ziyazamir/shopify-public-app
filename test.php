<?php
include_once "inc/gql_functions.php";
echo "hello";
$query = array("query" => '

mutation {
    appSubscriptionCreate(
        name: "Product Photos PRO"
        returnUrl: "https://www.shopify.com"
        lineItems: [
        {
            plan: {
                appRecurringPricingDetails: {
                    price: { amount: 10.00, currencyCode: USD }
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
}


');

// $mutation = graphql($access_token, $shop_url, $query);
// include_once "inc/gql_functions.php";

$shop = graphql("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", "designodemo.myshopify.com", $query);
// print_r($shop['headers']);
print_r($shop['body']);
