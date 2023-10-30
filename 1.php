<?php

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://demo.ckassa.ru/api-shop/rs/open/invoice/create2',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
        "servCode": "111-18298-1",
        "amount": 5000,
        "tgInvPayer": "test1",
        "startPaySelect": "true",
        "bestBefore": "17-12-2023 19:50:07 +0500",
        "invType": "READ_ONLY",
        "properties": [
                "Технопарк",
                "22222"
        ]
    }',
  CURLOPT_HTTPHEADER => array(
    'ApiLoginAuthorization: tech_lacquercoating_demo',
    'ApiAuthorization: SVO7RIR0H-HAOO-NPJ-5E4XL-FGXY6SBYLDN',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
$info = curl_getinfo($curl);

curl_close($curl);
var_dump($info);
echo 'Ответ: '.$response;


curl --location 'https://demo.ckassa.ru/api-shop/rs/open/invoice/create2' \
--header 'ApiLoginAuthorization: tech_lacquercoating_demo' \
--header 'ApiAuthorization: SVO7RIR0H-HAOO-NPJ-5E4XL-FGXY6SBYLDN' \
--header 'Content-Type: application/json' \
--data '{
    "servCode": "111-18298-1",
    "amount": 5000,
    "tgInvPayer": "test1",
    "startPaySelect": "true",
    "bestBefore": "22-12-2023 19:50:07 +0500",
    "invType": "READ_ONLY",
    "properties": [
            "Технопарк",
            "22222"
    ]
}'

// curl --location 'https://demo.ckassa.ru/api-shop/rs/open/invoice/create2' --header 'ApiLoginAuthorization: tech_lacquercoating_demo' --header 'ApiAuthorization: SVO7RIR0H-HAOO-NPJ-5E4XL-FGXY6SBYLDN' --header 'Content-Type: application/json' --data '{"servCode": "111-18298-1","amount": 5000,"tgInvPayer": "test1","startPaySelect": "true","bestBefore": "22-12-2023 19:50:07 +0500","invType": "READ_ONLY","properties": ["Технопарк","22222"]}'