<?php

function do_payment($post_values) {
    $post_url = "https://test.authorize.net/gateway/transact.dll";
    // This section takes the input fields and converts them to the proper format
    $post_string = "";
    foreach ($post_values as $key => $value) {
        $post_string .= "$key=" . urlencode($value) . "&";
    }
    $post_string = rtrim($post_string, "& ");

    // This sample code uses the CURL library for php to establish a connection,
    // submit the post, and record the response.
    // If you receive an error, you may want to ensure that you have the curl
    // library enabled in your php configuration

    $request = curl_init($post_url); // initiate curl object

    curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response

    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
    curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
    curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
    $post_response = curl_exec($request); // execute curl post and store results in $post_response
    // additional options may be required depending upon your server configuration
    // you can find documentation on curl options at http://www.php.net/curl_setopt
    curl_close($request); // close curl object
    // This line takes the response and breaks it into an array using the specified delimiting character
    $response_array = explode($post_values["x_delim_char"], $post_response);

    // The results are output to the screen in the form of an html numbered list.
    if ($response_array) {
        return $response_array;
    } else {
        return '';
    }
}
