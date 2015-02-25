<?php

$url = "http://www.fittingo.com/Kalori_Cetveli.aspx/FoodDetails";
$content = json_encode(array("FoodId" => 215));

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER,
  array("Content-type: application/json"));
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

$json_response = curl_exec($curl);




curl_close($curl);

$response = json_decode($json_response, true);
print_r($response);