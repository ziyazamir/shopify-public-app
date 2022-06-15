<?php


function graphql($token, $shop, $query = array())
{
    $url = "https://" . $shop . "/admin/api/2021-07/graphql.json";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, TRUE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);


    $request_headers[] = "";
    $request_headers[] = "Content-Type: application/json";
    if (!is_null($token)) $request_headers[] = "X-Shopify-Access-Token: " . $token;
    curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($query));
    curl_setopt($curl, CURLOPT_POST, true);

    $response = curl_exec($curl);
    $error_number = curl_errno($curl);
    $error_message = curl_error($curl);
    curl_close($curl);

    if ($error_number) {
        return $error_message;
    } else {

        $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

        $headers = array();
        $header_data = explode("\n", $response[0]);
        $headers['status'] = $header_data[0];
        array_shift($header_data);
        foreach ($header_data as $part) {
            $h = explode(":", $part, 2);
            $headers[trim($h[0])] = trim($h[1]);
        }

        return array('headers' => $headers, 'response' => $response[1]);
    }
}
// Code language: PHP (php)