<?php

$consumerKey = "yFLcokJjcu73kncJ0aMW89H8TxHwBZey5zrf6uEZ1LbvxmNu"; 
$consumerSecret = "balxfrcEkyEfwRC9HcsGICYXwgGCGlBF4KwOGs9vLapNwycqYielDwG8BXqOIiZ4"; 

$access_token_url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$headers = ['Content-Type:application/json; charset=utf8'];
$curl = curl_init($access_token_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_HEADER, FALSE);
curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
$result = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$result = json_decode($result);
echo $access_token = $result->access_token;
curl_close($curl);

// sand box

// $consumerKey = "zMBczI11YPYwr0nxtR6j4GnrN12y8vwH3qhFutz47G9jF8t0"; 
// $consumerSecret = "thOZCcyeh0tzbxd0MUXe1JUk5FMmXODrsAMvaKZoKnPulSraqwjtmWu8FbkEyBAU"; 

// $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
// $headers = ['Content-Type:application/json; charset=utf8'];
// $curl = curl_init($access_token_url);
// curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
// curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
// curl_setopt($curl, CURLOPT_HEADER, FALSE);
// curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
// $result = curl_exec($curl);
// $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
// $result = json_decode($result);
// echo $access_token = $result->access_token;
// curl_close($curl);




 