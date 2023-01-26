<?php
$theme = shopify_call($access_token, $shop, "/admin/api/2020-04/themes.json", array(), 'GET');
$theme = json_decode($theme['response'], JSON_PRETTY_PRINT);

foreach ($theme as $cur_theme) {
    foreach ($cur_theme as $key => $value) {
        if ($value['role'] === 'main') { //current active theme
            $theme_id = $value['id'];
            $theme_role = $value['role'];

            // **********creating a layout file***********
            $layout_file = array(
                "asset" => array(
                    "key" => "layout/theme.empty.liquid",
                    "value" => " <!DOCTYPE html>
                    <html lang=en>
                    <head>
                        <meta charset=UTF-8>
                        <meta http-equiv=X-UA-Compatible content=IE=edge>
                        <meta name='viewport' content='user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi'>
                        <title>App layout</title>
                    </head>
                    <body>
                        {{ content_for_header }}
                                        {{ content_for_layout }}
                    </body>
                    </html>"

                )
            );

            $asset = shopify_call($access_token, $shop, "/admin/api/2020-04/themes/" . $theme_id .  "/assets.json", $layout_file, 'PUT');
            $asset = json_decode($asset['response'], JSON_PRETTY_PRINT);


            // **********creating custom name  template file***********

            $template_file = array(
                "asset" => array(
                    "key" => "templates/page.custom.liquid",
                    "value" => " {% layout 'theme.empty' %}
                    <h1>hello from app</h1>"
                )
            );

            $asset = shopify_call($access_token, $shop, "/admin/api/2020-04/themes/" . $theme_id .  "/assets.json", $template_file, 'PUT');
            $asset = json_decode($asset['response'], JSON_PRETTY_PRINT);

            // echo print_r($asset);

            // **********creating a page ***********
            $asset = shopify_call($access_token, $shop, "/admin/api/2021-10/pages.json", array(), 'GET');
            $asset = json_decode($asset['response'], JSON_PRETTY_PRINT);
            // echo print_r($asset);
            $page_exist = "false";
            foreach ($asset["pages"] as $page) { //check if page already exists

                if ($page['handle'] == "custom_page" && $page['template_suffix'] == "custom") {
                    $page_exist = "true";
                    echo "<script> alert('Already Integrated');</script>";
                    break;
                }
            }
            if ($page_exist == "false") {
                $page = array(
                    "page" => array(
                        "title" => "App Page",
                        "body_html" => "",
                        "template_suffix" => "custom",
                        "handle" => "custom_page"
                    )
                );

                $asset = shopify_call($access_token, $shop, "/admin/api/2021-10/pages.json", $page, 'POST');
                $asset = json_decode($asset['response'], JSON_PRETTY_PRINT);

                echo "<script> alert('Page added successfully');</script>";
            }
        }
    }
}
