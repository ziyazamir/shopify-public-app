<?php
$theme = shopify_call($access_token, $shop, "/admin/api/2020-04/themes.json", array(), 'GET');
$theme = json_decode($theme['response'], JSON_PRETTY_PRINT);

foreach ($theme as $cur_theme) {
    foreach ($cur_theme as $key => $value) {
        if ($value['role'] === 'main') {
            $theme_id = $value['id'];
            $theme_role = $value['role'];

            $layout_file = array(
                "asset" => array(
                    "key" => "layout/theme.empty.liquid",
                    "value" => " <!DOCTYPE html>
                    <html lang=en>
                    <head>
                        <meta charset=UTF-8>
                        <meta http-equiv=X-UA-Compatible content=IE=edge>
                        <meta name='viewport' content='user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi'>
                        <title>Designo Editor</title>
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

            // echo print_r($asset);


            $template_file = array(
                "asset" => array(
                    "key" => "templates/page.designo.liquid",
                    "value" => " {% layout 'theme.empty' %}
                    <iframe id='designtool_iframe' type='text/html' target='_parent' name='Design N Buy' style='display: none;' frameborder='0' scrolling='yes' allowfullscreen=' width='100%' height='100%'></iframe>"
                )
            );

            $asset = shopify_call($access_token, $shop, "/admin/api/2020-04/themes/" . $theme_id .  "/assets.json", $template_file, 'PUT');
            $asset = json_decode($asset['response'], JSON_PRETTY_PRINT);

            // echo print_r($asset);

            // getting all pages
            $asset = shopify_call($access_token, $shop, "/admin/api/2021-10/pages.json", array(), 'GET');
            $asset = json_decode($asset['response'], JSON_PRETTY_PRINT);
            // echo print_r($asset);
            $page_exist = "false";
            foreach ($asset["pages"] as $page) {
                // echo $page['title'];
                if ($page['handle'] == "designo_editor" && $page['template_suffix'] == "designo") {
                    // echo "hhelo";
                    $page_exist = "true";
                    echo "<script> alert('Already Integrated');</script>";
                    break;
                }
            }
            if ($page_exist == "false") {
                $page = array(
                    "page" => array(
                        "title" => "designo",
                        "body_html" => "",
                        "template_suffix" => "designo",
                        "handle" => "designo_editor"
                    )
                );

                $asset = shopify_call($access_token, $shop, "/admin/api/2021-10/pages.json", $page, 'POST');
                $asset = json_decode($asset['response'], JSON_PRETTY_PRINT);
                // echo print_r($asset);
                // echo "all things added";
                echo "<script> alert('Integrated Successfully');</script>";
                // echo "put code here";
            }
        }
    }
}
